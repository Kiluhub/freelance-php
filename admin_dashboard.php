<?php
require 'connect.php';

// Get all questions joined with user info
$sql = "SELECT q.*, u.full_name 
        FROM questions q
        JOIN users u ON q.student_id = u.id
        ORDER BY q.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - SmartLearn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; }
        h2 { text-align: center; margin-bottom: 30px; color: #222; }
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
        .container {
            max-width: 1100px;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Dashboard - Submitted Questions</h2>

    <table>
        <tr>
            <th>#</th>
            <th>Student</th>
            <th>Title</th>
            <th>Pages</th>
            <th>Description</th>
            <th>Other Info</th>
            <th>File</th>
            <th>Date Posted</th>
            <th>Chat</th>
        </tr>

        <?php
        $count = 1;
        if ($stmt && $stmt->rowCount() > 0):
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        ?>
            <tr>
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= (int)$row['pages'] ?></td>
                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                <td><?= nl2br(htmlspecialchars($row['other_info'])) ?></td>
                <td>
                    <?php if (!empty($row['file_path'])): ?>
                        <a class="download-btn" href="<?= $row['file_path'] ?>" download>Download</a>
                    <?php else: ?>
                        No file
                    <?php endif; ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td><a class="download-btn" href="chat.php?task_id=<?= $row['id'] ?>">Open Chat</a></td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="9">No questions submitted yet.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

<?php include 'footer.php'; ?>
