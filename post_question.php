<?php
session_start();
require 'connect.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? 'Anonymous';

// ✅ Safely retrieve form inputs
$title = trim($_POST['title'] ?? '');
$pages = (int)($_POST['pages'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');
$other_info = trim($_POST['other_info'] ?? '');
$file_path = null;

// ✅ Check required fields before inserting
if ($title === '' || $pages <= 0 || $price <= 0 || $description === '') {
    die("❌ Required fields missing. Please fill in all required inputs.");
}

// ✅ Handle file upload
if (!empty($_FILES['file']['name'])) {
    $uploadDir = 'uploads/questions/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $file_path = $targetPath;
    }
}

try {
    // ✅ Insert into database
    $stmt = $conn->prepare("INSERT INTO questions (
        student_id, student_name, question_text, pages, price, description, other_info, file_path, created_at
    ) VALUES (
        :sid, :sname, :title, :pages, :price, :desc, :info, :file, NOW()
    )");

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

    // ✅ Redirect to new student_chat
    header("Location: student_chat.php?task_id=$questionId");
    exit();

} catch (PDOException $e) {
    echo "❌ Failed to post question: " . $e->getMessage();
}
?>
