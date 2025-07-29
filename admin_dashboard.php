<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';

// Check JWT token
if (!isset($_COOKIE['admin_token'])) {
    header("Location: admin_auth.php");
    exit;
}

try {
    $decoded = JWT::decode($_COOKIE['admin_token'], new Key($secretKey, 'HS256'));
    if ($decoded->role !== 'admin') {
        throw new Exception("Unauthorized");
    }
} catch (Exception $e) {
    header("Location: admin_auth.php");
    exit;
}

// Fetch submitted tasks with student ID and name
$sql = "SELECT q.*, u.id AS student_id, u.full_name 
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
        body {
            font-family: Arial, sans-serif;
            background: #f2f6fa;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .logout-btn {
            float: right;
            background: red;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px #ccc;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: black;
            color: white;
        }

        .expand-toggle {
            background: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .description-content {
            display: none;
            margin-top: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }

        .chat-link {
            background: green;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .download-btn {
            background: darkorange;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>

    <script>
        function toggleDescription(index) {
            const content = document.getElementById("desc-" + index);
            content.style.display = (content.style.display === "block") ? "none" : "block";
        }
    </script>
</head>
<body>

<form method="post" action="admin_logout.php">
    <button class="logout-btn">Logout</button>
</form>

<h2>Admin Dashboard — Submitted Questions</h2>

<table>
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Title</th>
        <th>Pages</th>
        <th>Price ($)</th>
        <th>Description</th>
        <th>File</th>
        <th>Posted</th>
        <th>Chat</th>
    </tr>

    <?php $i = 0; ?>
    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= (int)$row['pages'] ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td>
                <button class="expand-toggle" onclick="toggleDescription(<?= $i ?>)">View</button>
                <div class="description-content" id="desc-<?= $i ?>">
                    <strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?><br><br>
                    <strong>Other Info:</strong><br><?= nl2br(htmlspecialchars($row['other_info'])) ?>
                </div>
            </td>
            <td>
                <?php if (!empty($row['file_path'])): ?>
                    <a class="download-btn" href="<?= htmlspecialchars($row['file_path']) ?>" download>Download</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a class="chat-link" href="admin_chat.php?task_id=<?= $row['id'] ?>">Chat</a>
            </td>
        </tr>
    <?php $i++; endwhile; ?>
</table>

</body>
</html>
