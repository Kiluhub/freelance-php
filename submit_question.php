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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $pages = (int)$_POST['pages'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $other_info = trim($_POST['other_info']);
    $tutorId = isset($_POST['tutor_id']) ? (int)$_POST['tutor_id'] : 0;

    if ($tutorId <= 0) {
        $error = "⚠️ Please select a tutor.";
    } elseif ($price < 1) {
        $error = "⚠️ Price must be at least $1.";
    } elseif ($pages < 1) {
        $error = "⚠️ Number of pages must be at least 1.";
    } else {
        // Handle optional file upload
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
            $stmt = $conn->prepare("
                INSERT INTO questions (student_id, tutor_id, title, pages, price, description, other_info, file_path, created_at) 
                VALUES (:student_id, :tutor_id, :title, :pages, :price, :description, :other_info, :file_path, NOW())
            ");
            $stmt->execute([
                'student_id' => $studentId,
                'tutor_id' => $tutorId,
                'title' => $title,
                'pages' => $pages,
                'price' => $price,
                'description' => $description,
                'other_info' => $other_info,
                'file_path' => $filePath
            ]);
            $success = "✅ Task submitted successfully.";
        }
    }
}

// Fetch existing tasks for this student
$stmt = $conn->prepare("SELECT q.*, t.full_name AS tutor_name FROM questions q LEFT JOIN tutors t ON q.tutor_id = t.id WHERE q.student_id = :student_id ORDER BY q.created_at DESC");
$stmt->execute(['student_id' => $studentId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Task </title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        h2 { margin-bottom: 20px; text-align: center; }

        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input, textarea, select, button {
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
            text-align: left;
        }
        th { background: #222; color: white; }
        tr:hover { background: #f9f9f9; }
        a.chat-link {
            background: #007BFF; color: white;
            padding: 5px 10px; border-radius: 4px;
            text-decoration: none;
        }

        .view-btn {
            padding: 6px 12px;
            background: darkorange;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.7);
            overflow: auto;
        }
        .modal-content {
            background-color: #fff;
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
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #888;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit a New Task or  Scroll Down To See Your Submitted Tasks</h2>

    <?php if ($success): ?><div class="status"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title of the Task" required>
        <input type="number" name="pages" placeholder="Number of Pages" required min="1">
        <input type="number" name="price" placeholder="Your Budget in $" required min="1" step="0.01">
        <textarea name="description" placeholder="Task Description" rows="4" required></textarea>
        <textarea name="other_info" placeholder="Other Instructions (optional)" rows="3"></textarea>
        <input type="file" name="file">

        <label for="tutor_id"><strong>Choose a Tutor:</strong></label>
        <select name="tutor_id" required>
            <option value="">-- Select Tutor --</option>
            <?php
            $tutorStmt = $conn->query("SELECT id, full_name FROM tutors ORDER BY full_name ASC");
            $tutors = $tutorStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tutors as $tutor):
            ?>
                <option value="<?= $tutor['id'] ?>"><?= htmlspecialchars($tutor['full_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <a href="tutors.php" target="_blank" style="color:#007BFF; font-size:14px; display:inline-block; margin-top:8px;">🔍 View Tutor Profiles</a>

        <button type="submit">Submit Task</button>
    </form>

    <?php if (!empty($tasks)): ?>
        <h2>Your Submitted Tasks</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>Pages</th>
                <th>Price ($)</th>
                <th>Tutor</th>
                <th>File</th>
                <th>Posted</th>
                <th>Info</th>
                <th>Chat</th>
            </tr>
            <?php foreach ($tasks as $index => $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= $task['pages'] ?></td>
                    <td><?= number_format($task['price'], 2) ?></td>
                    <td><?= htmlspecialchars($task['tutor_name'] ?? 'N/A') ?></td>
                    <td>
                        <?= $task['file_path'] ? "<a href='{$task['file_path']}' download>Download</a>" : "N/A" ?>
                    </td>
                    <td><?= $task['created_at'] ?></td>
                    <td>
                        <button class="view-btn" onclick="openModal(<?= $index ?>)">View</button>
                    </td>
                    <td><a class="chat-link" href="student_chat.php?task_id=<?= $task['id'] ?>">Chat</a></td>
                </tr>

                <!-- Modal Content -->
                <div class="modal" id="modal<?= $index ?>">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal(<?= $index ?>)">&times;</span>
                        <h3><?= htmlspecialchars($task['title']) ?></h3>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                        <p><strong>Other Info:</strong><br><?= nl2br(htmlspecialchars($task['other_info'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="margin-top:20px;">You haven't submitted any tasks yet.</p>
    <?php endif; ?>
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
<?php include 'footer.php'; ?>
