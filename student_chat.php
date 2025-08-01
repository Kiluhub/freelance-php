<?php
ob_start(); // Start output buffering
require 'connect.php';
require 'vendor/autoload.php';

session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';
$taskId = $_GET['task_id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    die("❌ Invalid or missing task ID.");
}

// Get task and verify ownership
$check = $conn->prepare("SELECT id, title FROM questions WHERE id = :id AND student_id = :sid");
$check->execute(['id' => $taskId, 'sid' => $studentId]);
$taskInfo = $check->fetch(PDO::FETCH_ASSOC);
if (!$taskInfo) {
    die("❌ You are not allowed to access this chat.");
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
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
    $stmt = $conn->prepare("INSERT INTO messages (task_id, sender_role, sender_id, sender_name, message, type, file_path) VALUES (:tid, 'student', :sid, :sname, :msg, 'text', :files)");
    $stmt->execute([
        'tid' => $taskId,
        'sid' => $studentId,
        'sname' => $studentName,
        'msg' => $msg,
        'files' => $files_csv
    ]);

    header("Location: student_chat.php?task_id=$taskId");
    exit;
}

// Mark admin messages as seen
$conn->prepare("UPDATE messages SET seen_by_student = TRUE WHERE task_id = :tid AND sender_role = 'admin'")
    ->execute(['tid' => $taskId]);

// Fetch all messages
$q = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$q->execute(['tid' => $taskId]);
$messages = $q->fetchAll(PDO::FETCH_ASSOC);

ob_end_flush(); // Send output
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with Admin</title>
    <style>
        body { font-family: Arial; background: #f3f4f6; padding: 20px; }
        .chat-box { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .msg { margin-bottom: 15px; }
        .msg .meta { font-size: 0.85em; color: gray; }
        .msg .bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 70%;
        }
        .student { text-align: right; }
        .student .bubble { background: #d1e7dd; }
        .admin .bubble { background: #e2e3e5; }
        form { margin-top: 20px; }
        textarea { width: 100%; padding: 10px; border-radius: 6px; }
        .attachments-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .attachment-item {
            background: #f1f1f1;
            padding: 5px 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
        }
        .attachment-item span {
            margin-right: 8px;
        }
        .attachment-item button {
            background: none;
            border: none;
            color: red;
            cursor: pointer;
            font-size: 16px;
        }
        button {
            margin-top: 10px;
            padding: 10px 15px;
            background: black;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .attachments a {
            display: block;
            color: #007bff;
            text-decoration: none;
            font-size: 0.9em;
        }
        .attachments a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="chat-box">
    <h2>Chat for Task: <?= htmlspecialchars($taskInfo['title']) ?></h2>

    <?php foreach ($messages as $msg): ?>
        <div class="msg <?= $msg['sender_role'] === 'student' ? 'student' : 'admin' ?>">
            <div class="meta"><?= htmlspecialchars($msg['sender_name']) ?> — <?= $msg['sent_at'] ?></div>
            <div class="bubble"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
            <?php if (!empty($msg['file_path'])): ?>
                <div class="attachments">
                    <strong>Attachments:</strong>
                    <?php foreach (explode(',', $msg['file_path']) as $file): ?>
                        <a href="<?= htmlspecialchars($file) ?>" target="_blank" download>
                            📎 <?= basename($file) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <textarea name="message" placeholder="Type your message here..." required></textarea>
        <input type="file" name="attachment[]" id="fileInput" multiple onchange="handleFiles(this.files)">
        <div class="attachments-preview" id="preview"></div>
        <button type="submit">Send</button>
    </form>
</div>

<script>
    let selectedFiles = [];

    function handleFiles(files) {
        for (const file of files) {
            selectedFiles.push(file);
        }
        renderPreview();
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        renderPreview();
    }

    function renderPreview() {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'attachment-item';
            item.innerHTML = `<span>${file.name}</span><button type="button" onclick="removeFile(${index})">&times;</button>`;
            preview.appendChild(item);
        });

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        document.getElementById('fileInput').files = dataTransfer.files;
    }
</script>
</body>
</html>
