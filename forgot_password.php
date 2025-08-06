<?php
require 'connect.php'; // Your DB connection

$message = '';

// Resend email sender function
function sendResetEmail($toEmail, $toName, $token) {
    $apiKey = 'YOUR_RESEND_API_KEY'; // Replace this with your real Resend key
    $resetLink = "https://freelance-php-iyko.onrender.com/reset_password.php?token=$token";

    $data = [
        "from" => "SmartLearn <onboarding@resend.dev>", // No domain needed
        "to" => [$toEmail],
        "subject" => "Reset Your SmartLearn Password",
        "html" => "
            <p>Hi <strong>{$toName}</strong>,</p>
            <p>You requested a password reset. Click below to set a new password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link expires in 1 hour.</p>
        "
    ];

    $ch = curl_init("https://api.resend.com/emails");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 202) {
        return true;
    } else {
        return "Failed to send email: " . $response;
    }
}

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
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
            $stmt->execute([
                'email' => $email,
                'token' => $token,
                'expires' => $expiry
            ]);

            $result = sendResetEmail($email, $user['name'], $token);

            if ($result === true) {
                $message = "✅ Reset link sent to your email.";
            } else {
                $message = "❌ " . $result;
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
