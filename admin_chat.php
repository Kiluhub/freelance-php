// ✅ admin_chat.php
<?php
require 'vendor/autoload.php';
require 'connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$adminId = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$taskId = $_GET['task_id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    die("❌ Invalid or missing task.");
}

$check = $conn->prepare("SELECT id, question_text AS question, student_id, student_name FROM questions WHERE id = :tid");
$check->execute(['tid' => $taskId]);
$taskInfo = $check->fetch(PDO::FETCH_ASSOC);
if (!$taskInfo) {
    die("❌ Task not found.");
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    $type = $_POST['type'] ?? 'Other';
    $files_paths = [];

    if (!empty($_FILES['attachment']['name'][0])) {
        $uploadDir = 'uploads/chat/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($_FILES['attachment']['tmp_name'] as $i => $tmp) {
            if (is_uploaded_file($tmp)) {
                $name = basename($_FILES['attachment']['name'][$i]);
                $dest = $uploadDir . uniqid() . '_' . $name;
                if (move_uploaded_file($tmp, $dest)) {
                    $files_paths[] = $dest;
                }
            }
        }
    }

    $files_csv = implode(',', $files_paths);
    $stmt = $conn->prepare("INSERT INTO messages (task_id, sender_role, sender_id, sender_name, message, type, file_path) VALUES (:tid, 'admin', :sender_id, :sender_name, :msg, :type, :file_path)");
    $stmt->execute([
        'tid' => $taskId,
        'sender_id' => $adminId,
        'sender_name' => $adminName,
        'msg' => $msg,
        'type' => $type,
        'file_path' => $files_csv
    ]);

    header("Location: admin_chat.php?task_id=$taskId");
    exit;
}

$conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student' AND seen_by_admin = FALSE")
    ->execute(['tid' => $taskId]);

$q = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$q->execute(['tid' => $taskId]);
$messages = $q->fetchAll(PDO::FETCH_ASSOC);

include 'chat_ui_template.php';
