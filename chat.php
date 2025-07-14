<?php
session_start();
require 'connect.php';

// Step 1: Validate session
$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Unknown';

if (!$userId || !$userRole || !in_array($userRole, ['student', 'admin'])) {
    die("❌ Unauthorized access. Please log in first.");
}

$isStudent = ($userRole === 'student');
$isAdmin = ($userRole === 'admin');

// Step 2: Get task ID from URL
$taskId = $_GET['task_id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    die("❌ Invalid or missing task ID.");
}

// Step 3: Fetch task info
if ($isStudent) {
    $stmt = $conn->prepare("SELECT id, question_text AS question, student_id, student_name FROM questions WHERE id = :tid AND student_id = :sid");
    $stmt->execute(['tid' => $taskId, 'sid' => $userId]);
} else {
    $stmt = $conn->prepare("SELECT id, question_text AS question, student_id, student_name FROM questions WHERE id = :tid");
    $stmt->execute(['tid' => $taskId]);
}
$taskInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$taskInfo) {
    die("❌ Task not found or access denied.");
}

// Step 4: Handle message post
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
    $insert = $conn->prepare("INSERT INTO messages (task_id, sender_role, sender_id, sender_name, message, type, file_path) 
                              VALUES (:tid, :role, :sid, :sname, :msg, :type, :files)");
    $insert->execute([
        'tid' => $taskId,
        'role' => $userRole,
        'sid' => $userId,
        'sname' => $userName,
        'msg' => $msg,
        'type' => $type,
        'files' => $files_csv
    ]);

    header("Location: chat.php?task_id=$taskId");
    exit;
}

// Step 5: Fetch messages
$q = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$q->execute(['tid' => $taskId]);
$messages = $q->fetchAll(PDO::FETCH_ASSOC);

// Step 6: Mark messages as seen
if ($isAdmin) {
    $conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student' AND seen_by_admin = FALSE")
         ->execute(['tid' => $taskId]);
} elseif ($isStudent) {
    $conn->prepare("UPDATE messages SET seen_by_student = TRUE WHERE task_id = :tid AND sender_role = 'admin' AND seen_by_student = FALSE")
         ->execute(['tid' => $taskId]);
}

// For student name display
$studentDisplay = isset($taskInfo['student_name']) && $taskInfo['student_name'] !== null
    ? $taskInfo['student_name']
    : 'ID: ' . ($taskInfo['student_id'] ?? 'Unknown');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat – Task #<?= htmlspecialchars((string)$taskId) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; margin:0; padding:20px; }
        .chat-box { max-width:900px; margin:auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1); }
        .chat-header {
            background: <?= $isAdmin ? '#2c3e50' : '#27ae60' ?>;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .user-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .msg-container { display:flex; flex-direction:column; gap:12px; max-height:600px; overflow-y:auto; margin-bottom:20px; padding: 10px; background: #f8f9fa; border-radius: 8px; }
        .message { padding:12px 16px; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); position:relative; max-width:75%; }
        .message.admin { align-self:flex-end; background:#e3f2fd; border-left: 4px solid #2196f3; }
        .message.student { align-self:flex-start; background:#e8f5e8; border-left: 4px solid #4caf50; }
        .sender-info { font-weight: bold; color: #444; font-size: 13px; margin-bottom: 5px; }
        .type-tag { font-size:12px; background:#444; color:#fff; padding:2px 6px; border-radius:4px; display:inline-block; margin-bottom:5px; }
        .type-tag.answer { background: #28a745; }
        .type-tag.update { background: #ffc107; color: #000; }
        .type-tag.info { background: #17a2b8; }
        .file-link { display:block; font-size:13px; margin-top:5px; color: #007bff; text-decoration: none; }
        .file-link:hover { text-decoration: underline; }
        .message-form { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px; }
        form textarea, select, input[type="file"] { width:100%; padding:12px; font-size:14px; border-radius:8px; border:1px solid #ccc; margin-top:10px; box-sizing: border-box; }
        button { margin-top:15px; padding:12px 24px; background: <?= $isAdmin ? '#2c3e50' : '#27ae60' ?>; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer; }
        button:hover { background: <?= $isAdmin ? '#34495e' : '#2ecc71' ?>; }
        .back-link { display:block; text-align:center; margin-top:30px; color:#444; font-weight:bold; text-decoration:none; padding: 10px; background: #e9ecef; border-radius: 5px; }
        .back-link:hover { color:#000; background: #dee2e6; }
        .timestamp { font-size: 11px; color: #666; margin-top: 5px; }
        .seen-status { font-size: 11px; color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
<div class="chat-box">
    <div class="chat-header">
        <h2>Chat – Task #<?= htmlspecialchars((string)$taskId) ?></h2>
        <div>Task: <?= htmlspecialchars($taskInfo['question'] ?? 'Unknown Task') ?></div>
        <div>Student: <?= htmlspecialchars($studentDisplay) ?></div>
    </div>

    <div class="user-info">
        <strong>Logged in as:</strong> <?= htmlspecialchars($userName) ?> (<?= ucfirst($userRole) ?>)<br>
        <strong>User ID:</strong> <?= htmlspecialchars((string)$userId) ?>
    </div>

    <div class="msg-container" id="msgs">
        <?php if (empty($messages)): ?>
            <p style="color:#888;text-align:center;">No messages yet. Start the conversation below.</p>
        <?php endif; ?>
        <?php foreach ($messages as $msg): ?>
            <?php
                $paths = array_filter(explode(',', $msg['file_path']));
                $role = $msg['sender_role'];
                $senderName = $msg['sender_name'] ?? ucfirst($role);
                $senderId = $msg['sender_id'] ?? 'unknown';
                $seen = ($userRole === 'admin' && $role === 'student') ? ($msg['seen_by_admin'] ? '✅ Seen' : '🕓 Unread') :
                        (($userRole === 'student' && $role === 'admin') ? ($msg['seen_by_student'] ? '✅ Seen' : '🕓 Unread') : '');
            ?>
            <div class="message <?= $role ?>">
                <div class="sender-info"><?= htmlspecialchars((string)$senderName) ?> (<?= ucfirst($role) ?>)
                    <?php if ($isAdmin): ?> - ID: <?= htmlspecialchars((string)$senderId) ?><?php endif; ?>
                </div>
                <span class="type-tag <?= strtolower($msg['type']) ?>"><?= htmlspecialchars($msg['type'] ?? 'Other') ?></span>
                <div><?= nl2br(htmlspecialchars($msg['message'] ?? '')) ?></div>
                <?php foreach ($paths as $fp): ?>
                    <a class="file-link" href="<?= htmlspecialchars($fp) ?>" download>📎 <?= basename($fp) ?></a>
                <?php endforeach; ?>
                <div class="timestamp"><?= date('M j, Y g:i A', strtotime($msg['sent_at'])) ?> <?= $seen ? "<span class='seen-status'>— $seen</span>" : '' ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="message-form">
        <h3>Send Message</h3>
        <form method="post" enctype="multipart/form-data">
            <textarea name="message" placeholder="Type your message…" required rows="4"></textarea>
            <select name="type" required>
                <option value="Answer">Answer</option>
                <option value="Update">Update</option>
                <option value="Info">Info</option>
                <option value="Other" selected>Other</option>
            </select>
            <input type="file" name="attachment[]" multiple>
            <small style="color: #666;">You can attach multiple files</small>
            <button type="submit">Send Message</button>
        </form>
    </div>

    <a class="back-link" href="<?= $isAdmin ? 'admin_dashboard.php' : 'submit_question.php' ?>">
        ⬅ Back to <?= $isAdmin ? 'Admin Dashboard' : 'Student Tasks' ?>
    </a>
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
