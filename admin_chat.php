<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';

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

// Get task info with student name
$check = $conn->prepare("
    SELECT q.id, q.title, q.student_id, u.full_name 
    FROM questions q
    JOIN users u ON q.student_id = u.id
    WHERE q.id = :tid
");
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

// Mark messages as seen
$conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student' AND seen_by_admin = FALSE")
    ->execute(['tid' => $taskId]);

// Fetch all messages
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
        .chat-box { max-height: 450px; overflow-y: auto; margin-bottom: 20px; padding: 10px; background: #f9f9f9; border-radius: 8px; }
        .message {
            margin-bottom: 18px;
            max-width: 75%;
            padding: 10px;
            border-radius: 10px;
            clear: both;
        }
        .student {
            background-color: #dbeafe;
            float: left;
        }
        .admin {
            background-color: #dcfce7;
            float: right;
            text-align: right;
        }
        .timestamp {
            font-size: 0.75em;
            color: gray;
            display: block;
            margin-top: 5px;
        }
        .attachments a {
            display: block;
            font-size: 0.9em;
            color: #333;
            text-decoration: none;
        }
        .attachments a:hover {
            text-decoration: underline;
        }
        form textarea {
            width: 100%;
            resize: vertical;
            padding: 10px;
        }
        .file-preview {
            margin-top: 10px;
        }
        .file-preview div {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-preview span {
            margin-right: 10px;
        }
        .remove-file {
            color: red;
            cursor: pointer;
            font-weight: bold;
        }
        form input[type="file"] {
            margin-top: 8px;
        }
        form button {
            margin-top: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background: #0056b3;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="chat-container">
    <h2>
        Task: <?= htmlspecialchars($taskInfo['title']) ?><br>
        <small style="font-weight: normal; color: gray;">
            Student: <?= htmlspecialchars($taskInfo['full_name']) ?> (ID: <?= htmlspecialchars($taskInfo['student_id']) ?>)
        </small>
    </h2>

    <div class="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?= $msg['sender_role'] ?>">
                <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong><br>
                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                <?php if (!empty($msg['file_path'])): ?>
                    <div class="attachments">
                        <?php foreach (explode(',', $msg['file_path']) as $file): ?>
                            <a href="<?= htmlspecialchars($file) ?>" download>📎 <?= basename($file) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <span class="timestamp"><?= $msg['sent_at'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <textarea name="message" rows="3" placeholder="Type your message..." required></textarea><br>
        <input type="file" name="attachment[]" multiple id="fileInput"><br>
        <div class="file-preview" id="filePreview"></div>
        <input type="hidden" name="type" value="General">
        <button type="submit">Send</button>
    </form>
</div>

<script>
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');

    fileInput.addEventListener('change', () => {
        filePreview.innerHTML = '';
        Array.from(fileInput.files).forEach((file, index) => {
            const fileRow = document.createElement('div');
            const nameSpan = document.createElement('span');
            nameSpan.textContent = file.name;
            const removeBtn = document.createElement('span');
            removeBtn.textContent = '✖';
            removeBtn.className = 'remove-file';
            removeBtn.onclick = () => {
                const dataTransfer = new DataTransfer();
                Array.from(fileInput.files).forEach((f, i) => {
                    if (i !== index) dataTransfer.items.add(f);
                });
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            };
            fileRow.appendChild(nameSpan);
            fileRow.appendChild(removeBtn);
            filePreview.appendChild(fileRow);
        });
    });
</script>
</body>
</html>
