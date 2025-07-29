<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

require 'connect.php';
include 'header.php';

$student_id = $_SESSION['student_id'];

// Fetch tutors from DB (always up-to-date)
$tutorsStmt = $conn->query("SELECT id, full_name, subject, bio FROM tutors ORDER BY id DESC");
$tutors = $tutorsStmt->fetchAll(PDO::FETCH_ASSOC);

// Check if student has previous questions
$checkTasksStmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE student_id = :sid");
$checkTasksStmt->execute(['sid' => $student_id]);
$hasTasks = $checkTasksStmt->fetchColumn() > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Question - SmartLearn</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f0f4f8;
        }

        .container {
            max-width: 850px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .task-link {
            text-align: right;
            margin-bottom: 15px;
        }

        .task-link a {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .task-link a:hover {
            background-color: #218838;
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
        }

        .disclaimer {
            background-color: #e3f2fd;
            color: #0056b3;
            padding: 12px 15px;
            border-left: 5px solid #2196f3;
            margin-bottom: 25px;
            border-radius: 6px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .tutor-box {
            background: #fff3cd;
            padding: 20px;
            border-left: 6px solid #ffc107;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .tutor-box h3 {
            margin-top: 0;
            color: #856404;
        }

        .tutor-list {
            max-height: 220px;
            overflow-y: auto;
            margin-top: 10px;
        }

        .tutor-item {
            margin-bottom: 12px;
        }

        .tutor-item a {
            color: #007bff;
            font-size: 13px;
            margin-left: 10px;
        }

        .view-all {
            margin-top: 10px;
            font-size: 14px;
        }

        .view-all a {
            color: #0056b3;
            text-decoration: none;
        }

        .view-all a:hover {
            text-decoration: underline;
        }

        button {
            margin-top: 25px;
            background-color: red;
            color: white;
            border: none;
            padding: 14px 28px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: darkred;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #0066cc;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">

    <?php if ($hasTasks): ?>
        <div class="task-link">
            <a href="my_tasks.php">📂 See Your Previous Tasks</a>
        </div>
    <?php endif; ?>

    <h2>Post Your Assignment or Question</h2>

    <div class="disclaimer">
        💬 Please note: Your payment is held safely until you're satisfied with the solution. If not, you can request a refund, subject to support approval.
    </div>

    <form action="submit_question.php" method="post" enctype="multipart/form-data">

        <!-- Tutor Selection -->
        <div class="tutor-box">
            <h3>👩‍🏫 Choose a Tutor</h3>
            <div class="tutor-list">
                <?php foreach ($tutors as $tutor): ?>
                    <div class="tutor-item">
                        <input type="radio" name="tutor_id" value="<?= $tutor['id'] ?>" required>
                        <strong><?= htmlspecialchars($tutor['full_name']) ?></strong> — <?= htmlspecialchars($tutor['subject']) ?>
                        <a href="tutors.php#tutor-<?= $tutor['id'] ?>" target="_blank">Read More</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all">
                Or <a href="tutors.php" target="_blank">View All Tutors</a>
            </div>
        </div>

        <!-- Question Info -->
        <label for="title">Question Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="pages">Number of Pages:</label>
        <input type="number" name="pages" id="pages" required min="1">

        <label for="price">Price (in USD):</label>
        <input type="number" name="price" id="price" required min="1" step="0.01">

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="other_info">Other Info (optional):</label>
        <textarea name="other_info" id="other_info"></textarea>

        <label for="file">Upload File (optional):</label>
        <input type="file" name="file" id="file">

        <button type="submit">Submit Question</button>
    </form>

    <a class="back-link" href="index.php">← Back to Home</a>
</div>

</body>
</html>

<?php include 'footer.php'; ?>
