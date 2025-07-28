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
    $admin_id = $decoded->user_id;
    $admin_name = $decoded->name;
} catch (Exception $e) {
    header("Location: admin_auth.php");
    exit;
}

$sql = "SELECT q.*, u.full_name 
        FROM questions q
        JOIN users u ON q.student_id = u.id
        ORDER BY q.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; background: #eef2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px #ccc; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        th { background: #222; color: white; }
        .btn { padding: 6px 10px; border-radius: 4px; text-decoration: none; color: white; }
        .download-btn { background: blue; }
        .chat-btn { background: green; }
        .view-btn { background: darkorange; border: none; cursor: pointer; }
        .logout-btn { float: right; background: red; color: white; padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <form action="logout.php" method="post" style="text-align: right;">
        <button class="logout-btn">Logout</button>
    </form>
    <h2>Welcome, <?= htmlspecialchars($admin_name) ?> — Admin Dashboard</h2>
    <table>
        <tr>
            <th>Student</th>
            <th>Title</th>
            <th>Pages</th>
            <th>Price</th>
            <th>File</th>
            <th>Posted</th>
            <th>Details</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->rowCount() > 0): ?>
            <?php $i = 0; ?>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['pages'] ?></td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <?php if (!empty($row['file_path'])): ?>
                            <a class="btn download-btn" href="<?= $row['file_path'] ?>" download>Download</a>
                        <?php else: ?>No file<?php endif; ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td><button class="btn view-btn" onclick="openModal(<?= $i ?>)">View</button></td>
                    <td><a class="btn chat-btn" href="admin_chat.php?task_id=<?= $row['id'] ?>">Chat</a></td>
                </tr>
                <div class="modal" id="modal<?= $i ?>">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal(<?= $i ?>)">&times;</span>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        <p><strong>Other Info:</strong><br><?= nl2br(htmlspecialchars($row['other_info'])) ?></p>
                    </div>
                </div>
                <?php $i++; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No questions submitted.</td></tr>
        <?php endif; ?>
    </table>
</div>

<script>
function openModal(id) {
    document.getElementById('modal' + id).style.display = 'block';
}
function closeModal(id) {
    document.getElementById('modal' + id).style.display = 'none';
}
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
};
</script>
</body>
</html>
