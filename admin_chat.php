<?php
require 'connect.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';
$taskId = $_GET['task_id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    die("❌ Invalid or missing task ID.");
}

// Check if task belongs to this student
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = :tid AND student_id = :sid");
$stmt->execute(['tid' => $taskId, 'sid' => $studentId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("❌ Task not found or unauthorized access.");
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    $type = $_POST['type'] ?? 'General';
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
    $stmt = $conn->prepare("INSERT INTO messages (task_id, sender_role, sender_id, sender_name, message, type, file_path) VALUES (:tid, 'student', :sid, :sname, :msg, :type, :file_path)");
    $stmt->execute([
        'tid' => $taskId,
        'sid' => $studentId,
        'sname' => $studentName,
        'msg' => $msg,
        'type' => $type,
        'file_path' => $files_csv
    ]);

    header("Location: chat.php?task_id=$taskId");
    exit;
}

// Mark all unseen admin messages as seen
$conn->prepare("UPDATE messages SET seen_by_student = TRUE WHERE task_id = :tid AND sender_role = 'admin' AND seen_by_student = FALSE")
    ->execute(['tid' => $taskId]);

// Fetch messages
$stmt = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$stmt->execute(['tid' => $taskId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat with Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; }
        .chat-container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        .chat-box { max-height: 400px; overflow-y: auto; margin-bottom: 20px; padding-right: 10px; }
        .message { margin-bottom: 15px; }
        .student { color: blue; }
        .admin { color: green; }
        .timestamp { font-size: 0.8em; color: gray; }
        .attachments a { display: block; font-size: 0.9em; }
    </style>
</head>
<body>
<div class="chat-container">
    <h2>Chat with Admin — <?= htmlspecialchars($task['title']) ?></h2>
    <div class="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="message">
                <strong class="<?= $msg['sender_role'] ?>">
                    <?= htmlspecialchars($msg['sender_name']) ?>:
                </strong>
                <?= nl2br(htmlspecialchars($msg['message'])) ?><br>
                <span class="timestamp">[<?= $msg['sent_at'] ?>]</span>
                <?php if (!empty($msg['file_path'])): ?>
                    <div class="attachments">
                        <?php foreach (explode(',', $msg['file_path']) as $file): ?>
                            <a href="<?= htmlspecialchars($file) ?>" download>📎 <?= basename($file) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <textarea name="message" rows="3" style="width:100%;" placeholder="Write your message..." required></textarea><br>
        <input type="file" name="attachment[]" multiple>
        <input type="hidden" name="type" value="General">
        <button type="submit">Send</button>
    </form>
</div>
</body>
</html>
