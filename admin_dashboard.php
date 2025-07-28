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
} catch (Exception $e) {
    header("Location: admin_auth.php");
    exit;
}

// Get questions from DB
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
        h2 { text-align: center; }
        .logout-btn {
            float: right;
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background: black;
            color: white;
        }
    </style>
</head>
<body>
    <form method="post" action="admin_logout.php">
        <button class="logout-btn">Logout</button>
    </form>

    <h2>Admin Dashboard — Submitted Questions</h2>

    <table>
        <tr>
            <th>Student</th>
            <th>Title</th>
            <th>Pages</th>
            <th>Price</th>
            <th>Description</th>
            <th>File</th>
            <th>Posted</th>
        </tr>
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= $row['pages'] ?></td>
            <td>$<?= number_format($row['price'], 2) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td>
                <?php if ($row['file_path']): ?>
                    <a href="<?= $row['file_path'] ?>" download>Download</a>
                <?php else: ?>N/A<?php endif; ?>
            </td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
