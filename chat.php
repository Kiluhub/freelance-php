<?php
session_start();
require 'connect.php';

$taskId = $_GET['task_id'] ?? null;
if (!$taskId) {
    die("No task specified.");
}

$isStudent = isset($_SESSION['student_id']);
$senderRole = $isStudent ? 'student' : 'admin';

// Security: If student, confirm the task belongs to them
if ($isStudent) {
    $check = $conn->prepare("SELECT id FROM questions WHERE id = :task_id AND student_id = :student_id");
    $check->execute(['task_id' => $taskId, 'student_id' => $_SESSION['student_id']]);
    if (!$check->fetch()) {
        die("Unauthorized access to this task.");
    }
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO messages (task_id, sender_role, message) VALUES (:task_id, :role, :msg)");
    $stmt->execute(['task_id' => $taskId, 'role' => $senderRole, 'msg' => $msg]);
    header("Location: chat.php?task_id=" . $taskId); // Prevent form resubmit
    exit;
}

// Fetch chat messages
$stmt = $conn->prepare("SELECT * FROM messages WHERE task_id = :task_id ORDER BY sent_at ASC");
$stmt->execute(['task_id' => $taskId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Chat</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; padding: 20px; }
        .chat-box {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .student { background: #e0f7fa; align-self: flex-start; }
        .admin { background: #d1c4e9; align-self: flex-end; text-align: right; }
        form textarea {
            width: 100%;
            height: 80px;
            margin-top: 10px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        form button {
            margin-top: 10px;
            padding: 10px 20px;
            background: black;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .msg-container {
            display: flex;
            flex-direction: column;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        a.back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>
<div class="chat-box">
    <h2>Task Chat — Task ID #<?= htmlspecialchars($taskId) ?></h2>

    <div class="msg-container">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?= $msg['sender_role'] ?>">
                <strong><?= ucfirst($msg['sender_role']) ?>:</strong> <br>
                <?= nl2br(htmlspecialchars($msg['message'])) ?><br>
                <small><?= $msg['sent_at'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post">
        <textarea name="message" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>

    <a class="back-link" href="submit_question.php">⬅ Back to Tasks</a>
</div>
</body>
</html>
