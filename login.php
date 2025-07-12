<?php
session_start();
require 'connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($conn) {
        try {
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['student_id'] = $user['id'];
                    header("Location: post_question.php");
                    exit;
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                // Optional: Redirect if account doesn't exist
                // header("Location: register.php?error=notfound");
                $error = "Account not found. Please register below.";
            }
        } catch (PDOException $e) {
            $error = "Login error: " . $e->getMessage();
        }
    } else {
        $error = "Database connection failed.";
    }
}
?>
