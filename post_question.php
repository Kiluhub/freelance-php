<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post a Question - SmartLearn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
        }
        .container {
            max-width: 700px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 10px;
        }
        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background-color: red;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Post a Question</h2>
    <form action="post_question.php" method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Question Title" required>
        <input type="number" name="pages" placeholder="Number of Pages" required min="1">
        <input type="number" name="price" placeholder="Price (USD)" required min="1" step="0.01">
        <textarea name="description" placeholder="Description" required></textarea>
        <textarea name="other_info" placeholder="Other Info (Optional)"></textarea>
        <input type="file" name="file">
        <button type="submit">Submit Question</button>
    </form>
</div>

</body>
</html>
