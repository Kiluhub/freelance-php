<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;

// Use environment variable on Render (fallback optional)
$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $action = $_POST['action']; // "login" or "register"

    if ($action === "register") {
        // Check if admin already exists
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
        // Validate login
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            // Prepare JWT payload
            $payload = [
                'user_id' => $admin['id'],
                'role' => 'admin',
                'name' => $admin['username'],
                'exp' => time() + 86400 // 1 day expiry
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Set JWT cookie securely
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            setcookie('token', $jwt, time() + 86400, '/', '', $secure, true); // HTTP-only, secure if HTTPS

            // Redirect to admin dashboard
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
    <title>Admin Login / Signup - SmartLearn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 50px; }
        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        input, select, button {
            width: 100%;
            margin-top: 12px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #111;
            color: white;
            cursor: pointer;
        }
        .message {
            margin-top: 20px;
            color: #d00;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Access</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Admin Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="action" required>
            <option value="login">Login</option>
            <option value="register">Register</option>
        </select>
        <button type="submit">Continue</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</div>
</body>
</html>
