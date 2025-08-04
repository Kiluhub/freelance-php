<?php
require 'vendor/autoload.php'; // PHPMailer via Composer
require 'connect.php'; // Your DB connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $message = "❌ Please enter your email.";
    } else {
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $message = "❌ No account found with that email.";
        } else {
            // Generate reset token and expiry
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Save to password_resets table
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
            $stmt->execute([
                'email' => $email,
                'token' => $token,
                'expires' => $expiry
            ]);

            // Create reset link
            $resetLink = "https://freelance-php-iyko.onrender.com/reset_password.php?token=$token";

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your@gmail.com'; // your email
                $mail->Password = 'your-app-password'; // Gmail app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your@gmail.com', 'SmartLearn Support');
                $mail->addAddress($email, $user['name']);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "
                    <p>Hi <strong>{$user['name']}</strong>,</p>
                    <p>You requested a password reset. Click below to set a new password:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>This link expires in 1 hour.</p>
                ";

                $mail->send();
                $message = "✅ Reset link sent to your email.";
            } catch (Exception $e) {
                $message = "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 40px; }
        .container {
            max-width: 400px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: black;
            color: white;
            border: none;
        }
        .message {
            margin-top: 15px;
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <?php if (!empty($message)): ?>
        <p class="message <?= strpos($message, '✅') !== false ? 'success' : '' ?>"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</div>
</body>
</html>
