<?php
require 'connect.php';
require 'vendor/autoload.php';

session_start();
header('Content-Type: application/json');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Determine role and ID
$userRole = null;
$userId = null;

// Student
if (isset($_SESSION['student_id'])) {
    $userRole = 'student';
    $userId = $_SESSION['student_id'];
}

// Admin
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

// Determine column for "seen" status
$seenColumn = $userRole === 'admin' ? 'seen_by_admin' : 'seen_by_student';

// Fetch unread messages
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

$stmt->execute(['role' => $userRole, 'uid' => $userId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format notifications
$notifications = array_map(function($m) use ($userRole) {
    $link = $userRole === 'admin'
        ? "admin_chat.php?task_id=" . $m['task_id']
        : "chat.php?task_id=" . $m['task_id'];

    return [
        'sender' => $m['sender_name'] . " (" . ucfirst($m['sender_role']) . ")",
        'message' => substr($m['message'], 0, 80) . (strlen($m['message']) > 80 ? "..." : ""),
        'link' => $link,
        'time' => date("M j, Y g:i A", strtotime($m['sent_at']))
    ];
}, $messages);

echo json_encode($notifications);
// returns json