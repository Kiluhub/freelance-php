<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION['student_id'];
$error = '';
$success = '';

// Handle task submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $pages = (int)$_POST['pages'];
    $description = trim($_POST['description']);
    $other_info = trim($_POST['other_info']);

    // Handle file upload
    $filePath = '';
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $filePath = $targetPath;
        } else {
            $error = "⚠️ File upload failed.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO questions (student_id, title, pages, description, other_info, file_path) VALUES (:student_id, :title, :pages, :description, :other_info, :file_path)");
        $stmt->execute([
            'student_id' => $studentId,
            'title' => $title,
            'pages' => $pages,
            'description' => $description,
            'other_info' => $other_info,
            'file_path' => $filePath
        ]);
        $success = "✅ Task submitted successfully.";
    }
}

// Fetch existing tasks for this student
$stmt = $conn->prepare("SELECT * FROM questions WHERE student_id = :student_id ORDER BY created_at DESC");
$stmt->execute(['student_id' => $studentId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Task</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        h2 { margin-bottom: 20px; text-align: center; }

        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input, textarea, button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button { background: black; color: white; border: none; }

        .status { margin-top: 10px; color: green; }
        .error { margin-top: 10px; color: red; }

        table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 5px #ccc;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th { background: black; color: white; }
        tr:hover { background: #f9f9f9; }
        a.chat-link { background: #007BFF; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit a New Task</h2>

    <?php if ($success): ?><div class="status"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title of the Task" required>
        <input type="number" name="pages" placeholder="Number of Pages" required>
        <textarea name="description" placeholder="Task Description" rows="4" required></textarea>
        <textarea name="other_info" placeholder="Other Instructions (optional)" rows="3"></textarea>
        <input type="file" name="file">
        <button type="submit">Submit Task</button>
    </form>

    <?php if (!empty($tasks)): ?>
        <h2>Your Submitted Tasks</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>Pages</th>
                <th>Description</th>
                <th>Other Info</th>
                <th>File</th>
                <th>Posted</th>
                <th>Chat</th>
            </tr>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= $task['pages'] ?></td>
                    <td><?= nl2br(htmlspecialchars($task['description'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($task['other_info'])) ?></td>
                    <td>
                        <?php if ($task['file_path']): ?>
                            <a href="<?= $task['file_path'] ?>" download>Download</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= $task['created_at'] ?></td>
                    <td><a class="chat-link" href="chat.php?task_id=<?= $task['id'] ?>">Chat</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="margin-top:20px;">You haven't submitted any tasks yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
