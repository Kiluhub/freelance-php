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

        // Remove the token
        $conn->prepare("DELETE FROM password_resets WHERE token = :token")->execute(['token' => $token]);
        $message = "✅ Password updated. You can now login.";
    } else {
        $message = "❌ Invalid or expired token.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <form method="post">
        <h2>Reset Your Password</h2>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <button type="submit">Update Password</button>
        <p><?= htmlspecialchars($message) ?></p>
    </form>
</body>
</html>
