<?php
session_start(); // Start session first

require 'connect.php'; // PDO connection to PostgreSQL
?>

<?php include 'header.php'; ?>

<?php
$error = ''; // Error message placeholder

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Use named placeholders with PDO
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Get inserted ID (note: only works if your table has SERIAL or IDENTITY column)
        $_SESSION['student_id'] = $conn->lastInsertId();
        header("Location: post_question.php");
        exit();
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
        input { width: 100%; padding: 12px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { margin-top: 20px; background: black; color: white; padding: 12px; width: 100%; border: none; border-radius: 5px; }
        a { display: block; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>
<div class="form
