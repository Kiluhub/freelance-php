<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

require 'connect.php';
include 'header.php';

$student_id = $_SESSION['student_id'];

// Fetch tutors from DB
$tutorsStmt = $conn->query("SELECT id, full_name, subject, bio FROM tutors ORDER BY id DESC");
$tutors = $tutorsStmt->fetchAll(PDO::FETCH_ASSOC);

// Check if student has previous questions
$checkTasksStmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE student_id = :sid");
$checkTasksStmt->execute(['sid' => $student_id]);
$hasTasks = $checkTasksStmt->fetchColumn() > 0;

// Get pre-selected tutor name (if any)
$selectedTutorName = isset($_GET['tutor_name']) ? urldecode($_GET['tutor_name']) : '';
$selectedTutorId = null;
foreach ($tutors as $tutor) {
    if (strcasecmp($tutor['full_name'], $selectedTutorName) === 0) {
        $selectedTutorId = $tutor['id'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Question - SmartLearn</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
        }

        .container {
            max-width: 850px;
            margin: 50px auto;
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
        input[type="file"],
        select {
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

        .selected-tutor {
            margin-top: 10px;
            color: #007bff;
            font-weight: bold;
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
            <a href="submit_question.php">📂 See Your Previous Tasks</a>
        </div>
    <?php endif; ?>

    <h2>Post Your Assignment or Question</h2>

    <div class="disclaimer">
        💬 Please note: Your payment is held safely until you're satisfied with the solution. If not, you can request a refund, subject to support approval.
    </div>

    <form action="submit_question.php" method="post" enctype="multipart/form-data">
        <label for="tutor_id">Choose a Tutor:</label>
        <select name="tutor_id" id="tutor_id" required>
            <option value="">-- Select a tutor --</option>
            <?php foreach ($tutors as $tutor): ?>
                <option value="<?= $tutor['id'] ?>" <?= $selectedTutorId === $tutor['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tutor['full_name']) ?> — <?= htmlspecialchars($tutor['subject']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if ($selectedTutorId): ?>
            <div class="selected-tutor">✔ Tutor selected: <?= htmlspecialchars($selectedTutorName) ?></div>
        <?php endif; ?>

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
