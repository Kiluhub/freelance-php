<?php
session_start();
require 'connect.php';

$taskId = $_GET['task_id'] ?? null;
if (!$taskId || !is_numeric($taskId)) {
    die("❌ No task specified.");
}

// ROLE CHECK
$isStudent = isset($_SESSION['student_id']);
$isAdmin   = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$senderRole = $isStudent ? 'student' : ($isAdmin ? 'admin' : null);

if (!$senderRole) {
    die("❌ Unauthorized. Please log in.");
}

// If student, verify task ownership
if ($isStudent) {
    $verify = $conn->prepare("SELECT id FROM questions WHERE id = :tid AND student_id = :sid");
    $verify->execute(['tid' => $taskId, 'sid' => $_SESSION['student_id']]);
    if (!$verify->fetch()) {
        die("❌ Access denied. This task doesn’t belong to you.");
    }
}

// Handle message POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    $type = $_POST['type'] ?? 'Other';
    $files_paths = [];

    // Handle multiple file uploads
    if (!empty($_FILES['attachment']['name'][0])) {
        $uploadDir = 'uploads/chat/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        foreach ($_FILES['attachment']['tmp_name'] as $i => $tmp) {
            $name = basename($_FILES['attachment']['name'][$i]);
            $uniq = uniqid() . '_' . $name;
            $dest = $uploadDir . $uniq;
            if (move_uploaded_file($tmp, $dest)) {
                $files_paths[] = $dest;
            }
        }
    }
    $files_csv = implode(',', $files_paths);

    $ins = $conn->prepare("
        INSERT INTO messages (task_id, sender_role, message, type, file_path) 
        VALUES (:tid, :role, :msg, :type, :file_path)
    ");
    $ins->execute([
        'tid' => $taskId,
        'role' => $senderRole,
        'msg' => $msg,
        'type' => $type,
        'file_path' => $files_csv
    ]);

    header("Location: chat.php?task_id=$taskId");
    exit;
}

// Fetch messages
$q = $conn->prepare("SELECT * FROM messages WHERE task_id = :tid ORDER BY sent_at ASC");
$q->execute(['tid' => $taskId]);
$messages = $q->fetchAll(PDO::FETCH_ASSOC);

// Mark messages as seen
if ($isAdmin) {
    $conn->prepare("UPDATE messages SET seen_by_admin = TRUE WHERE task_id = :tid AND sender_role = 'student'")
         ->execute(['tid' => $taskId]);
} elseif ($isStudent) {
    $conn->prepare("UPDATE messages SET seen_by_student = TRUE WHERE task_id = :tid AND sender_role = 'admin'")
         ->execute(['tid' => $taskId]);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title><?= ucfirst($senderRole) ?> Chat – Task #<?= htmlspecialchars($taskId) ?></title>
  <style>
    body { font-family: 'Segoe UI'; background: #eef2f7; margin:0; padding:20px; }
    .chat-box { max-width:900px;margin:auto;background:#fff;padding:25px;border-radius:10px;box-shadow:0 0 12px rgba(0,0,0,0.1); }
    h2 { text-align:center;color:#333;margin-bottom:20px; }
    .msg-container { display:flex; flex-direction:column; gap:12px; max-height:600px; overflow-y:auto; margin-bottom:20px; }
    .message { padding:12px 16px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.1); position:relative; max-width:75%; }
    .student { align-self:flex-start; background:#dcf8c6 }
    .admin { align-self:flex-end; background:#e1dff8 }
    .type-tag { font-size:12px; background:#444; color:#fff; padding:2px 6px; border-radius:4px; display:inline-block; margin-bottom:5px; }
    .file-link { display:block; font-size:13px; margin-top:5px; }
    form textarea, select, input[type="file"] { width:100%; padding:12px; font-size:14px; border-radius:8px; border:1px solid #ccc; margin-top:10px; }
    input[type="file"] { padding:0.5em; }
    button { margin-top:15px; padding:12px 24px; background:#111; color:#fff; border:none; border-radius:6px; font-size:16px; cursor:pointer; }
    .back-link { display:block; text-align:center; margin-top:30px; color:#444; font-weight:bold; text-decoration:none; }
    .back-link:hover { color:#000; }
    .new-notice { position:fixed; top:10px; right:10px; font-size:24px; display:none; cursor:pointer; }
  </style>
</head>
<body>
  <div class="chat-box">
    <h2><?= $isAdmin ? 'Admin Chat Panel' : 'Your Task Chat' ?> — Task #<?= htmlspecialchars($taskId) ?></h2>
    <div class="msg-container" id="msgs">
      <?php if (empty($messages)): ?>
        <p style="color:#888;text-align:center;">No messages yet. Start below.</p>
      <?php endif; ?>

      <?php foreach ($messages as $msg): ?>
        <?php 
          $paths = array_filter(explode(',', $msg['file_path'] ?? ''));
          $seen = '';
          if ($senderRole === 'admin' && $msg['sender_role'] === 'student') {
              $seen = $msg['seen_by_admin'] ? '✅ Seen' : '🕓 Unread';
          } elseif ($senderRole === 'student' && $msg['sender_role'] === 'admin') {
              $seen = $msg['seen_by_student'] ? '✅ Seen' : '🕓 Unread';
          }
        ?>
        <div class="message <?= $msg['sender_role'] ?>">
          <span class="type-tag"><?= htmlspecialchars($msg['type'] ?? 'Other') ?></span><br>
          <strong><?= ucfirst($msg['sender_role']) ?>:</strong><br>
          <?= nl2br(htmlspecialchars($msg['message'])) ?>
          <?php foreach ($paths as $fp): ?>
            <a class="file-link" href="<?= htmlspecialchars($fp) ?>" download>📎 <?= basename($fp) ?></a>
          <?php endforeach; ?>
          <small><?= $msg['sent_at'] ?> — <em><?= $seen ?></em></small>
        </div>
      <?php endforeach; ?>
    </div>

    <form method="post" enctype="multipart/form-data">
      <textarea name="message" placeholder="Type your message…" required></textarea>
      <select name="type" required>
        <option value="Answer">Answer</option>
        <option value="Update">Update</option>
        <option value="Info">Info</option>
        <option value="Other" selected>Other</option>
      </select>
      <input type="file" name="attachment[]" multiple>
      <button type="submit">Send</button>
    </form>

    <a class="back-link" href="<?= $isAdmin ? 'admin_dashboard.php' : 'submit_question.php' ?>">⬅ Back to Tasks</a>
  </div>

  <div class="new-notice" id="notice">🔔 New!</div>
  <audio id="ding" src="https://www.myserver.com/ding.mp3"></audio>

  <script>
    let lastCount = <?= count($messages) ?>;
    setInterval(() => {
      fetch('chat_poll.php?task_id=<?= $taskId ?>')
        .then(r => r.json())
        .then(data => {
          if (data.count > lastCount) {
            document.getElementById('notice').style.display = 'block';
            document.getElementById('ding').play();
            lastCount = data.count;
          }
        });
    }, 5000);

    document.getElementById('notice').onclick = () => {
      location.reload();
    };
  </script>
</body>
</html>
