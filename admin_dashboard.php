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
            background: #f2f2f2;
            margin: 0;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
        }

        .logout-btn {
            float: right;
            background-color: crimson;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #111;
            color: white;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .download-btn {
            background: steelblue;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
        }

        .chat-btn {
            background: forestgreen;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
        }

        .view-btn {
            background: darkorange;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #888;
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
            <th>Price ($)</th>
            <th>File</th>
            <th>Date Posted</th>
            <th>Info</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->rowCount() > 0): ?>
            <?php $index = 0; ?>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= (int)$row['pages'] ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td>
                        <?php if (!empty($row['file_path'])): ?>
                            <a class="download-btn" href="<?= htmlspecialchars($row['file_path']) ?>" download>Download</a>
                        <?php else: ?>
                            No file
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <button class="view-btn" onclick="openModal(<?= $index ?>)">View</button>
                    </td>
                    <td>
                        <a class="chat-btn" href="admin_chat.php?task_id=<?= $row['id'] ?>">Chat</a>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal" id="modal<?= $index ?>">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal(<?= $index ?>)">&times;</span>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        <p><strong>Other Info:</strong><br><?= nl2br(htmlspecialchars($row['other_info'])) ?></p>
                        <p><strong>Pages:</strong> <?= $row['pages'] ?> | <strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                    </div>
                </div>

                <?php $index++; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No questions submitted yet.</td></tr>
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
