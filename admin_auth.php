<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;

$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $action = $_POST['action']; // login or register

    if ($action === "register") {
        $check = $conn->prepare("SELECT id FROM admins WHERE username = :username");
        $check->execute(['username' => $username]);

        if ($check->fetch()) {
            $message = "❌ Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
            $stmt->execute(['username' => $username, 'password' => $hashed]);
            $message = "✅ Registration successful. You may now log in.";
        }
    } elseif ($action === "login") {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $payload = [
                'user_id' => $admin['id'],
                'role' => 'admin',
                'name' => $admin['username'],
                'exp' => time() + 86400
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');
            setcookie('admin_token', $jwt, time() + 86400, '/', '', false, true);

            header("Location: admin_dashboard.php");
            exit;
        } else {
            $message = "❌ Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Access - SmartLearn</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 40px; }
        .container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        input, select, button { width: 100%; margin-top: 10px; padding: 10px; border-radius: 6px; }
        button { background: #333; color: white; }
        .message { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Access</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="action" required>
            <option value="login">Login</option>
            <option value="register">Register</option>
        </select>
        <button type="submit">Submit</button>
    </form>
    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</div>
</body>
</html>
