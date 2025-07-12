<?php
session_start();
require 'connect.php';

$taskId = $_GET['task_id'] ?? null;
if (!$taskId) { echo json_encode(['count'=>0]); exit; }
$q = $conn->prepare("SELECT COUNT(*) FROM messages WHERE task_id = :tid");
$q->execute(['tid'=>$taskId]);
$c = $q->fetchColumn();
echo json_encode(['count' => (int)$c]);
?>
