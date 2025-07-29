<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

require 'connect.php';
include 'header.php';

// Fetch tutors from DB
$tutorsStmt = $conn->query("SELECT id, full_name, subject, bio, profile_pic FROM tutors");
$tutors = $tutorsStmt->fetchAll(PDO::FETCH_ASSOC);
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
            max-width: 800px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
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

        .tutor-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 6px;
            background: #f9f9f9;
        }

        .tutor-item {
            margin-bottom: 15px;
        }

        .tutor-item a {
            color: #0066cc;
            font-size: 14px;
            margin-left: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Post Your Assignment or Question</h2>
    <p>Please note that your money is held until you're satisfied with the solution. If not, you can request a refund subject to support approval.</p>

    <form action="submit_question.php" method="post" enctype="multipart/form-data">
        <label for="title">Question Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="pages">Number of Pages:</label>
        <input type="number" name="pages" id="pages" required min="1">

        <label for="price">Price (in USD):</label>
        <input type="number" name="price" id="price" required min="1" step="0.01" placeholder="Enter at least $1">

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="other_info">Other Info (optional):</label>
        <textarea name="other_info" id="other_info"></textarea>

        <label for="file">Upload File (optional):</label>
        <input type="file" name="file" id="file">

        <label for="tutor">Choose a Tutor:</label>
        <div class="tutor-list">
            <?php foreach ($tutors as $tutor): ?>
                <div class="tutor-item">
                    <input type="radio" name="tutor_id" value="<?= $tutor['id'] ?>" required>
                    <strong><?= htmlspecialchars($tutor['full_name']) ?></strong> — <?= htmlspecialchars($tutor['subject']) ?>
                    <a href="tutors.php#tutor-<?= $tutor['id'] ?>" target="_blank">Read More</a>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit">Submit Question</button>
    </form>

    <a class="back-link" href="index.php">&larr; Back to Home</a>
</div>

</body>
</html>

<?php include 'footer.php'; ?>
