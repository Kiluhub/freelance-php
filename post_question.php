<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? 'Anonymous';
$title = trim($_POST['title']);
$pages = (int)$_POST['pages'];
$price = (float)$_POST['price'];
$description = trim($_POST['description']);
$other_info = trim($_POST['other_info']);
$file_path = null;

if (!empty($_FILES['file']['name'])) {
    $uploadDir = 'uploads/questions/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $file_path = $targetPath;
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO questions (student_id, student_name, question_text, pages, price, description, other_info, file_path, created_at) VALUES (:sid, :sname, :title, :pages, :price, :desc, :info, :file, NOW())");

    $stmt->execute([
        'sid'   => $student_id,
        'sname' => $student_name,
        'title' => $title,
        'pages' => $pages,
        'price' => $price,
        'desc'  => $description,
        'info'  => $other_info,
        'file'  => $file_path
    ]);

    $questionId = $conn->lastInsertId();

    // ✅ Redirect to student_chat.php instead of old chat.php
    header("Location: student_chat.php?task_id=$questionId");
    exit();

} catch (PDOException $e) {
    echo "❌ Failed to post question: " . $e->getMessage();
}
?>
