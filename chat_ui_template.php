<?php
// Requires: $taskId, $taskInfo, $userName, $userRole, $messages, $userId
$isAdmin = $userRole === 'admin';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat – Task #<?= htmlspecialchars((string)$taskId) ?></title>
    <style>
        body { font-family: 'Segoe UI'; background: #eef2f7; padding: 20px; margin: 0; }
        .chat-box { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 0 12px rgba(0,0,0,0.1); }
        .chat-header { background: <?= $isAdmin ? '#2c3e50' : '#27ae60' ?>; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .user-info, .message-form { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .msg-container { display: flex; flex-direction: column; gap: 12px; max-height: 600px; overflow-y: auto; background: #f1f3f5; padding: 10px; border-radius: 8px; }
        .message { padding: 12px; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); max-width: 75%; }
        .message.admin { align-self: flex-end; background: #e3f2fd; border-left: 4px solid #2196f3; }
        .message.student { align-self: flex-start; background: #e8f5e8; border-left: 4px solid #4caf50; }
        .file-link { display: block; font-size: 13px; margin-top: 5px; color: #007bff; text-decoration: none; }
        .timestamp { font-size: 11px; color: #666; margin-top: 5px; }
        button { background: <?= $isAdmin ? '#2c3e50' : '#27ae60' ?>; color: white; padding: 12px; border: none; border-radius: 6px; width: 100%; margin-top: 10px; cursor: pointer; }
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
