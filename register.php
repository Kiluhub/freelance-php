<?php
session_start();
require 'connect.php'; // Ensure $conn is defined
?>

<?php include 'header.php'; ?>

<?php
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && $conn) {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
} elseif (!$conn) {
    $error = "Could not connect to the database.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: Arial; background: #f0f4f8; padding: 20px; }
        .form-box { max-width: 400px; margin: auto; background: white; padding: 30px; box-shadow: 0 0 10px #ccc; border-radius: 8px; }
        input { width: 100%; padding: 12px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { margin-top: 20px; background: black; color: white; padding: 12px; width: 100%; border: none; border-radius: 5px; }
        a { display: block; margin-top: 10px; text-align: center; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Student Registration</h2>
    <form method="post">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Create Password" required>
        <button type="submit">Register</button>
        <a href="login.php">Already have an account? Login</a>
    </form>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html>

<?php include 'footer.php'; ?>
