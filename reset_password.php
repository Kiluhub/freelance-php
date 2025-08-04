<?php
require 'connect.php';

$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];

    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = :pwd WHERE email = :email");
        $stmt->execute([
            'pwd' => $hashed,
            'email' => $row['email']
        ]);

        $conn->prepare("DELETE FROM password_resets WHERE token = :token")->execute(['token' => $token]);
        $message = "✅ Password updated. You can now login.";
    } else {
        $message = "❌ Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .form-container {
            max-width: 400px;
            background: white;
            padding: 25px;
            margin: auto;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
        }
        h2 {
            text-align: center;
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
            text-align: center;
            margin-top: 15px;
            color: #d00;
        }
        .message.success {
            color: green;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Reset Your Password</h2>
    <form method="post">
        <!-- Hidden token from URL -->
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <button type="submit">Update Password</button>
    </form>

    <?php if (!empty($message)): ?>
        <p class="message <?= strpos($message, '✅') !== false ? 'success' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
</div>
</body>
</html>
