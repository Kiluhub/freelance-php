<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Only allow POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("🚫 Access denied. Please submit the form properly.");
}

// ✅ Now safely access posted values
$title = trim($_POST['title'] ?? '');
$pages = (int)($_POST['pages'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');
$other_info = trim($_POST['other_info'] ?? '');
$file_path = null;

// Validate
if (empty($title) || $pages <= 0 || $price <= 0 || empty($description)) {
    die("❌ Required fields missing. Please fill in all required inputs.");
}

// Upload (optional)
if (!empty($_FILES['file']['name'])) {
    $uploadDir = 'uploads/questions/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $file_path = $targetPath;
    }
}

// Save to DB
try {
    $stmt = $conn->prepare("INSERT INTO questions (student_id, student_name, question_text, pages, price, description, other_info, file_path, created_at)
        VALUES (:sid, :sname, :title, :pages, :price, :desc, :info, :file, NOW())");

    $stmt->execute([
        'sid'   => $_SESSION['student_id'],
        'sname' => $_SESSION['student_name'] ?? 'Anonymous',
        'title' => $title,
        'pages' => $pages,
        'price' => $price,
        'desc'  => $description,
        'info'  => $other_info,
        'file'  => $file_path
    ]);

    $questionId = $conn->lastInsertId();

    header("Location: student_chat.php?task_id=$questionId");
    exit();
} catch (PDOException $e) {
    die("❌ Failed to post question: " . $e->getMessage());
}
