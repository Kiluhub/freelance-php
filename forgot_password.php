<?php
require 'connect.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if email is registered
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour from now

        // Store token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
        $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires' => $expires
        ]);

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'yourgmail@gmail.com';
            $mail->Password = 'your_gmail_app_password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('yourgmail@gmail.com', 'SmartLearn');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset';
            $link = "http://yourdomain.com/reset_password.php?token=$token";
            $mail->Body = "Click the link below to reset your password:\n$link";

            $mail->send();
            $message = "✅ Check your email for a reset link.";
        } catch (Exception $e) {
            $message = "❌ Failed to send email. Try again.";
        }
    } else {
        $message = "❌ That email is not registered.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
<div class="form-box">
    <h2>Reset Your Password</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a class="register-link" href="login.php">Back to Login</a>
</div>
</body>
</html>
