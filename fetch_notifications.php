<?php
require 'connect.php';
require 'vendor/autoload.php';

session_start();
header('Content-Type: application/json');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$userRole = null;
$userId = null;

// Student login
if (isset($_SESSION['student_id'])) {
    $userRole = 'student';
    $userId = $_SESSION['student_id'];
}

// Admin login via JWT cookie
if (!$userId && isset($_COOKIE['admin_token'])) {
    $secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';
    try {
        $decoded = JWT::decode($_COOKIE['admin_token'], new Key($secretKey, 'HS256'));
        if ($decoded->role === 'admin') {
            $userRole = 'admin';
            $userId = $decoded->admin_id;
        }
    } catch (Exception $e) {
        echo json_encode([]);
        exit;
    }
}

if (!$userId || !$userRole) {
    echo json_encode([]);
    exit;
}

$seenColumn = $userRole === 'admin' ? 'seen_by_admin' : 'seen_by_student';

$stmt = $conn->prepare("
    SELECT m.id, m.message, m.sent_at, m.task_id, m.sender_name, m.sender_role
    FROM messages m
    JOIN questions q ON m.task_id = q.id
    WHERE m.sender_role != :role
      AND $seenColumn = FALSE
      AND (
        (:role = 'student' AND q.student_id = :uid)
        OR (:role = 'admin')
      )
    ORDER BY m.sent_at DESC
    LIMIT 10
");

$stmt->execute([
    'role' => $userRole,
    'uid' => $userId
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notifications = [];

foreach ($messages as $msg) {
    $notifications[] = [
        'sender' => $msg['sender_name'] . " (" . ucfirst($msg['sender_role']) . ")",
        'message' => mb_substr($msg['message'], 0, 80) . (mb_strlen($msg['message']) > 80 ? "..." : ""),
        'link' => $userRole === 'admin' 
            ? "admin_chat.php?task_id=" . $msg['task_id']
            : "student_chat.php?task_id=" . $msg['task_id'],
        'time' => date("M j, Y g:i A", strtotime($msg['sent_at']))
    ];
}

echo json_encode($notifications);
