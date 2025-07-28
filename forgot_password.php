<?php
require 'connect.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if email is registered
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiry

        // Save token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
        $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires' => $expires
        ]);

        // Send email with reset link
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Gmail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'yourgmail@gmail.com';
            $mail->Password = 'your_gmail_app_password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('yourgmail@gmail.com', 'Your Site');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Link';
            $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
            $mail->Body = "Click the link to reset your password: $resetLink";

            $mail->send();
            $message = "✅ Check your email for a reset link.";
        } catch (Exception $e) {
            $message = "❌ Could not send email. Try again.";
        }
    } else {
        $message = "❌ Email not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
    <form method="post">
        <h2>Forgot Password</h2>
        <input type="email" name="email" placeholder="Your registered email" required>
        <button type="submit">Send Reset Link</button>
        <p><?= htmlspecialchars($message) ?></p>
    </form>
</body>
</html>
