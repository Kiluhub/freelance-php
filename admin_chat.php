<?php
// ✅ admin_chat.php (Full working version)
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';

// Authenticate using JWT only
if (!isset($_COOKIE['admin_token'])) {
    header("Location: admin_auth.php");
    exit;
}

try {
    $decoded = JWT::decode($_COOKIE['admin_token'], new Key($secretKey, 'HS256'));
    if ($decoded->role !== 'admin') {
        throw new Exception("Not admin");
    }
    $adminId = $decoded->admin_id ?? null;
    $adminName = $decoded->admin_name ?? 'Admin';
} catch (Exception $e) {
    header("Location: admin_auth.php");
    exit;
}

$taskId = $_GET['task_id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    die("❌ Invalid or missing task ID.");
}

// Validate task exists
$check = $conn->prepare("SELECT id, title, student_id FROM questions WHERE id = :tid");
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

// Mark all unseen student messages as seen
$conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student' AND seen_by_admin = FALSE")
    ->execute(['tid' => $taskId]);

// Fetch messages
$q = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$q->execute(['tid' => $taskId]);
$messages = $q->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f5; padding: 20px; }
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
    <h2>Chat with Student — <?= htmlspecialchars($taskInfo['title']) ?></h2>
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
        <textarea name="message" rows="3" style="width:100%;" placeholder="Type your message..." required></textarea><br>
        <input type="file" name="attachment[]" multiple>
        <input type="hidden" name="type" value="General">
        <button type="submit">Send</button>
    </form>
</div>
</body>
</html>
