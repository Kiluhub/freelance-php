<?php
require 'connect.php';
session_start();

$role = $_SESSION['user_role'] ?? ($_SESSION['student_id'] ? 'student' : '');
$id = $_SESSION['user_id'] ?? $_SESSION['student_id'] ?? null;

if (!$id || !$role) exit(json_encode([]));

$stmt = $conn->prepare("SELECT id, task_id, message, sender_name, sent_at 
    FROM messages 
    WHERE seen_by_recipient = FALSE AND receiver_role = :role AND receiver_id = :id 
    ORDER BY sent_at DESC LIMIT 10");
$stmt->execute(['role' => $role, 'id' => $id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
