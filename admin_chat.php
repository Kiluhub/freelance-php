<?php
require 'connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_auth.php");
    exit();
}

$adminId = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$taskId = $_GET['task_id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    die("❌ Invalid or missing task ID.");
}

// Get task and student info
$stmt = $conn->prepare("SELECT q.*, u.full_name FROM questions q JOIN users u ON q.student_id = u.id WHERE q.id = :tid");
$stmt->execute(['tid' => $taskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("❌ Task not found.");
}

// Handle admin message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    $type = $_POST['type'] ?? 'Update';
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
    $stmt = $conn->prepare("INSERT INTO messages (task_id, sender_role, sender_id, sender_name, message, type, file_path) VALUES (:tid, 'admin', :aid, :aname, :msg, :type, :files)");
    $stmt->execute([
        'tid' => $taskId,
        'aid' => $adminId,
        'aname' => $adminName,
        'msg' => $msg,
        'type' => $type,
        'files' => $files_csv
    ]);

    header("Location: admin_chat.php?task_id=$taskId");
    exit;
}

// Mark student messages as seen
$conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student' AND seen_by_admin = FALSE")
    ->execute(['tid' => $taskId]);

// Fetch chat messages
$stmt = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$stmt->execute(['tid' => $taskId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat — <?= htmlspecialchars($task['title']) ?></title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        .chat-container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .chat-box { max-height: 450px; overflow-y: auto; margin-bottom: 20px; padding-right: 10px; }
        .message { margin-bottom: 15px; }
        .admin { color: green; font-weight: bold; }
        .student { color: blue; font-weight: bold; }
        .timestamp { font-size: 0.8em; color: gray; }
        .attachments a { display: block; font-size: 0.9em; }
    </style>
</head>
<body>
<div class="chat-container">
    <h2>Chat with <?= htmlspecialchars($task['full_name']) ?> — Task: <?= htmlspecialchars($task['title']) ?></h2>
    <div class="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="message">
                <span class="<?= $msg['sender_role'] ?>">
                    <?= htmlspecialchars($msg['sender_name']) ?>:
                </span>
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
        <textarea name="message" rows="3" style="width:100%;" placeholder="Write your reply..." required></textarea><br>
        <input type="file" name="attachment[]" multiple>
        <input type="hidden" name="type" value="Reply">
        <button type="submit">Send Message</button>
    </form>
</div>
</body>
</html>
