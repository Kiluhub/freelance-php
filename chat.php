<?php
// ==========================
// chat.php (JWT-based secure chat page)
// ==========================

require 'vendor/autoload.php'; // for firebase/php-jwt
require 'connect.php'; // database connection

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// JWT secret key used during login
$secretKey = 'your-very-secret-key';

// ----------------------------
// Step 1: Read and Decode JWT
// ----------------------------
$jwt = $_COOKIE['token'] ?? null;
if (!$jwt) {
    die("❌ Unauthorized. No token provided.");
}

try {
    $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
    $userId = $decoded->user_id;
    $userRole = $decoded->role;
    $userName = $decoded->name ?? 'User';
} catch (Exception $e) {
    die("❌ Invalid or expired token.");
}

$isStudent = $userRole === 'student';
$isAdmin = $userRole === 'admin';
$senderRole = $userRole;

// ----------------------------
// Step 2: Validate Task ID
// ----------------------------
$taskId = $_GET['task_id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    die("❌ No task specified.");
}

// ----------------------------
// Step 3: Fetch Task Info
// ----------------------------
if ($isStudent) {
    $check = $conn->prepare("SELECT id, question_text AS question, student_id, student_name FROM questions WHERE id = :tid AND student_id = :sid");
    $check->execute(['tid' => $taskId, 'sid' => $userId]);
} else {
    $check = $conn->prepare("SELECT id, question_text AS question, student_id, student_name FROM questions WHERE id = :tid");
    $check->execute(['tid' => $taskId]);
}

$taskInfo = $check->fetch(PDO::FETCH_ASSOC);
if (!$taskInfo) {
    die("❌ Access denied or task not found.");
}

// ----------------------------
// Step 4: Handle Message Submit
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    $type = $_POST['type'] ?? 'Other';
    $files_paths = [];

    // Handle file uploads
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
    $stmt = $conn->prepare("INSERT INTO messages (task_id, sender_role, sender_id, sender_name, message, type, file_path) VALUES (:tid, :role, :sender_id, :sender_name, :msg, :type, :file_path)");
    $stmt->execute([
        'tid' => $taskId,
        'role' => $senderRole,
        'sender_id' => $userId,
        'sender_name' => $userName,
        'msg' => $msg,
        'type' => $type,
        'file_path' => $files_csv
    ]);

    header("Location: chat.php?task_id=$taskId");
    exit;
}

// ----------------------------
// Step 5: Fetch Messages
// ----------------------------
$q = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$q->execute(['tid' => $taskId]);
$messages = $q->fetchAll(PDO::FETCH_ASSOC);

// ----------------------------
// Step 6: Mark Seen
// ----------------------------
if ($isAdmin) {
    $conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student' AND seen_by_admin = FALSE")->execute(['tid' => $taskId]);
} elseif ($isStudent) {
    $conn->prepare("UPDATE messages SET seen_by_student = TRUE WHERE task_id = :tid AND sender_role = 'admin' AND seen_by_student = FALSE")->execute(['tid' => $taskId]);
}

// ----------------------------
// Step 7: Render HTML Output
// ----------------------------
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat – Task #<?= htmlspecialchars((string)$taskId) ?></title>
    <style>
        body { font-family: 'Segoe UI'; background: #eef2f7; padding: 20px; margin:0; }
        .chat-box { max-width:900px; margin:auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1); }
        .chat-header { background: <?= $isAdmin ? '#2c3e50' : '#27ae60' ?>; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .user-info, .message-form { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .msg-container { display:flex; flex-direction:column; gap:12px; max-height:600px; overflow-y:auto; background: #f1f3f5; padding: 10px; border-radius: 8px; }
        .message { padding:12px; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); max-width:75%; }
        .message.admin { align-self:flex-end; background:#e3f2fd; border-left: 4px solid #2196f3; }
        .message.student { align-self:flex-start; background:#e8f5e8; border-left: 4px solid #4caf50; }
        .file-link { display:block; font-size:13px; margin-top:5px; color:#007bff; text-decoration:none; }
        .timestamp { font-size:11px; color:#666; margin-top:5px; }
        button { background: <?= $isAdmin ? '#2c3e50' : '#27ae60' ?>; color:white; padding:12px; border:none; border-radius:6px; width:100%; margin-top:10px; cursor:pointer; }
        button:hover { background: <?= $isAdmin ? '#34495e' : '#2ecc71' ?>; }
    </style>
</head>
<body>
<div class="chat-box">
    <div class="chat-header">
        <h2>Chat – Task #<?= htmlspecialchars((string)$taskId) ?></h2>
        <div>Task: <?= htmlspecialchars($taskInfo['question'] ?? 'Unknown') ?></div>
        <div>Student: <?= htmlspecialchars($taskInfo['student_name'] ?? ('ID: ' . $taskInfo['student_id'])) ?></div>
    </div>

    <div class="user-info">
        <strong>Logged in as:</strong> <?= htmlspecialchars($userName) ?> (<?= ucfirst($userRole) ?>)<br>
        <strong>User ID:</strong> <?= htmlspecialchars((string)$userId) ?>
    </div>

    <div class="msg-container" id="msgs">
        <?php if (empty($messages)): ?>
            <p style="color:#888; text-align:center;">No messages yet. Start the conversation.</p>
        <?php endif; ?>
        <?php foreach ($messages as $msg): ?>
            <?php
                $paths = array_filter(explode(',', $msg['file_path']));
                $role = $msg['sender_role'];
                $senderName = $msg['sender_name'] ?? ucfirst($role);
                $seen = ($userRole === 'admin' && $role === 'student') ? ($msg['seen_by_admin'] ? '✅ Seen' : '🕓 Unread') :
                        (($userRole === 'student' && $role === 'admin') ? ($msg['seen_by_student'] ? '✅ Seen' : '🕓 Unread') : '');
            ?>
            <div class="message <?= $role ?>">
                <div><strong><?= htmlspecialchars((string)$senderName) ?> (<?= ucfirst($role) ?>)</strong></div>
                <div><em><?= htmlspecialchars($msg['type'] ?? 'Other') ?></em></div>
                <div><?= nl2br(htmlspecialchars($msg['message'] ?? '')) ?></div>
                <?php foreach ($paths as $fp): ?>
                    <a class="file-link" href="<?= htmlspecialchars($fp) ?>" download>📎 <?= basename($fp) ?></a>
                <?php endforeach; ?>
                <div class="timestamp"><?= date('M j, Y g:i A', strtotime($msg['sent_at'])) ?> <?= $seen ? "<span style='color:#28a745'>— $seen</span>" : '' ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="message-form">
        <h3>Send Message</h3>
        <form method="post" enctype="multipart/form-data">
            <textarea name="message" placeholder="Type your message..." required rows="4"></textarea>
            <select name="type">
                <option value="Answer">Answer</option>
                <option value="Update">Update</option>
                <option value="Info">Info</option>
                <option value="Other" selected>Other</option>
            </select>
            <input type="file" name="attachment[]" multiple>
            <button type="submit">Send Message</button>
        </form>
    </div>

    <div style="text-align:center;margin-top:20px;">
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const msgContainer = document.getElementById('msgs');
    msgContainer.scrollTop = msgContainer.scrollHeight;
});
setInterval(() => location.reload(), 30000);
</script>
</body>
</html>