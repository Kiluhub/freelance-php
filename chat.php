<?php
session_start();
require 'connect.php';

$taskId = $_GET['task_id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    die("❌ No task specified.");
}

// ============================
// 🔒 SECURE ROLE VERIFICATION
// ============================
$isStudent = false;
$isAdmin = false;
$senderRole = null;

if (isset($_SESSION['student_id'])) {
    $studentCheck = $conn->prepare("SELECT id FROM students WHERE id = :id");
    $studentCheck->execute(['id' => $_SESSION['student_id']]);
    if ($studentCheck->fetch()) {
        $isStudent = true;
        $senderRole = 'student';
    }
}

if (isset($_SESSION['admin_id'])) {
    $adminCheck = $conn->prepare("SELECT id FROM admins WHERE id = :id");
    $adminCheck->execute(['id' => $_SESSION['admin_id']]);
    if ($adminCheck->fetch()) {
        $isAdmin = true;
        $senderRole = 'admin';
    }
}

if (!$isStudent && !$isAdmin) {
    die("❌ Unauthorized access. Please log in.");
}

// 🛡 If student, confirm task belongs to them
if ($isStudent) {
    $taskOwnership = $conn->prepare("SELECT id FROM questions WHERE id = :task_id AND student_id = :student_id");
    $taskOwnership->execute([
        'task_id' => $taskId,
        'student_id' => $_SESSION['student_id']
    ]);
    if (!$taskOwnership->fetch()) {
        die("❌ Access denied. This task doesn't belong to you.");
    }
}

// ============================
// 💬 HANDLE NEW MESSAGE
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $type = $_POST['type'] ?? 'Other';
    $filePath = '';

    // Handle file upload
    if (!empty($_FILES['attachment']['name'])) {
        $uploadDir = 'uploads/chat/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = uniqid() . '_' . basename($_FILES['attachment']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
            $filePath = $targetPath;
        }
    }

    $insert = $conn->prepare("
        INSERT INTO messages (task_id, sender_role, message, type, file_path) 
        VALUES (:task_id, :role, :msg, :type, :file_path)
    ");
    $insert->execute([
        'task_id' => $taskId,
        'role' => $senderRole,
        'msg' => $msg,
        'type' => $type,
        'file_path' => $filePath
    ]);

    header("Location: chat.php?task_id=" . $taskId);
    exit;
}

// ============================
// 📩 FETCH MESSAGES
// ============================
$msgQuery = $conn->prepare("SELECT * FROM messages WHERE task_id = :task_id ORDER BY sent_at ASC");
$msgQuery->execute(['task_id' => $taskId]);
$messages = $msgQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= ucfirst($senderRole) ?> Chat – Task #<?= htmlspecialchars($taskId) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 20px;
        }

        .chat-box {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .msg-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            max-width: 75%;
            word-wrap: break-word;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            position: relative;
        }

        .student { align-self: flex-start; background: #dcf8c6; }
        .admin { align-self: flex-end; background: #e1dff8; }

        .message small {
            display: block;
            margin-top: 5px;
            font-size: 0.75em;
            color: #666;
        }

        .message .type-tag {
            font-size: 12px;
            background: #444;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .message .file-link {
            display: block;
            font-size: 13px;
            margin-top: 5px;
        }

        form textarea, form select, form input[type="file"] {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        form button {
            margin-top: 15px;
            padding: 12px 24px;
            background: #111;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            text-decoration: none;
            color: #444;
            font-weight: bold;
        }

        .back-link:hover { color: #000; }
    </style>
</head>
<body>
    <div class="chat-box">
        <h2><?= $isAdmin ? 'Admin Chat Panel' : 'Your Task Chat' ?> — Task #<?= htmlspecialchars($taskId) ?></h2>

        <div class="msg-container">
            <?php if (empty($messages)): ?>
                <p style="color:#888; text-align:center;">No messages yet. Start the conversation below.</p>
            <?php endif; ?>

            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_role'] ?>">
                    <span class="type-tag"><?= htmlspecialchars($msg['type'] ?? 'Other') ?></span><br>
                    <strong><?= ucfirst($msg['sender_role']) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    <?php if (!empty($msg['file_path'])): ?>
                        <a class="file-link" href="<?= htmlspecialchars($msg['file_path']) ?>" download>📎 Attachment</a>
                    <?php endif; ?>
                    <small><?= $msg['sent_at'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post" enctype="multipart/form-data">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <select name="type" required>
                <option value="Answer">Answer</option>
                <option value="Update">Update</option>
                <option value="Info">Info</option>
                <option value="Other" selected>Other</option>
            </select>
            <input type="file" name="attachment">
            <button type="submit">Send</button>
        </form>

        <a class="back-link" href="<?= $isAdmin ? 'admin_dashboard.php' : 'submit_question.php' ?>">⬅ Back to Tasks</a>
    </div>
</body>
</html>
