<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;

// Load from environment (safer for live hosting like Render)
$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Create token payload
            $payload = [
                'user_id' => $user['id'],
                'role' => 'student',
                'name' => $user['name'] ?? 'Student',
                'exp' => time() + 86400 // expires in 1 day
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Set secure HTTP-only cookie (check if site is on HTTPS)
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('token', $jwt, time() + 86400, '/', '', $secure, true); // secure+HTTP-only

            // ✅ Redirect to post_question.php after login
            header("Location: post_question.php");
            exit;
        } else {
            $error = "❌ Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "Login error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <style>
        body { font-family: Arial; background: #f0f4f8; padding: 20px; }
        .form-box {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
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
        .error {
            color: red;
            text-align: center;
            margin-top: 15px;
        }
        .register-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #0077cc;
        }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Student Login</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- 🔗 Register & Forgot Password Links -->
    <a class="register-link" href="register.php">Don't have an account? Click here to register.</a>
    <a class="register-link" href="forgot_password.php">Forgot Password?</a>
</div>
</body>
</html>
