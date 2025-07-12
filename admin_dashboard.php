<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_auth.php");
    exit;
}

require 'connect.php';

// Get all questions joined with user info
$sql = "SELECT q.*, u.full_name 
        FROM questions q
        JOIN users u ON q.student_id = u.id
        ORDER BY q.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - SmartLearn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #222;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background: black;
            color: white;
        }
        tr:hover {
            background: #f9f9f9;
        }
        a.download-btn {
            background: blue;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }
        a.chat-btn {
            background: green;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 10px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
        }
        .logout-btn {
            float: right;
            background: red;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            margin-bottom: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="logout.php" method="post" style="text-align: right;">
        <button class="logout-btn">Logout</button>
    </form>

    <h2>Admin Dashboard — Submitted Questions</h2>

    <table>
        <tr>
            <th>Student</th>
            <th>Title</th>
            <th>Pages</th>
            <th>Description</th>
            <th>Other Info</th>
            <th>File</th>
            <th>Date Posted</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->rowCount() > 0): ?>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= (int)$row['pages'] ?></td>
                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['other_info'])) ?></td>
                    <td>
                        <?php if (!empty($row['file_path'])): ?>
                            <a class="download-btn" href="<?= htmlspecialchars($row['file_path']) ?>" download>Download</a>
                        <?php else: ?>
                            No file
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <a class="chat-btn" href="chat.php?task_id=<?= $row['id'] ?>">Chat</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No questions submitted yet.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
