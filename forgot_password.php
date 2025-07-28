<?php
require 'vendor/autoload.php'; // Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'connect.php'; // your DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        echo "Please enter your email.";
        exit;
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "No account found with that email.";
        exit;
    }

    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Store token in DB (assumes 'password_resets' table)
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
    $stmt->execute([
        'email' => $email,
        'token' => $token,
        'expires' => $expiry
    ]);

    // Create reset link
    $resetLink = "https://yourdomain.com/reset_password.php?token=$token";

    // Send email
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your@gmail.com'; // your SMTP email
        $mail->Password = 'your-app-password'; // use app password for Gmail
        $mail->SMTPSecure = 'tls'; // or 'ssl'
        $mail->Port = 587; // 465 for SSL

        // Recipients
        $mail->setFrom('your@gmail.com', 'YourAppName');
        $mail->addAddress($email, $user['name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "
            <p>Hi {$user['name']},</p>
            <p>You requested a password reset. Click the link below to reset your password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link will expire in 1 hour.</p>
        ";

        $mail->send();
        echo "A password reset link has been sent to your email.";
    } catch (Exception $e) {
        echo "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Invalid request.";
}
?>
