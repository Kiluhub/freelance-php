<?php
session_start();
require 'connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $student_id = $_SESSION['student_id'];

    if (empty($title) || empty($description)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO questions (student_id, title, description, created_at) VALUES (:student_id, :title, :description, NOW())");
            $stmt->execute([
                ':student_id' => $student_id,
                ':title' => $title,
                ':description' => $description
            ]);
            $success = "Question submitted successfully!";
            header("Location: my_questions.php"); // or post_question.php or dashboard
            exit;
        } catch (PDOException $e) {
            $error = "Error submitting question. Try again.";
        }
    }
}
?>

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit a Question</title>
    <style>
        body { font-family: Arial; background: #f0f4f8; padding: 20px; }
        .form-box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
        }
        textarea, input, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: black;
            color: white;
            margin-top: 20px;
            cursor: pointer;
        }
        .error, .success {
            text-align: center;
            margin-top: 15px;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Submit a Question</h2>
    <form method="post">
        <input type="text" name="title" placeholder="Enter question title" required>
        <textarea name="description" placeholder="Describe your question in detail..." rows="6" required></textarea>
        <button type="submit">Submit Question</button>
    </form>
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
</div>
</body>
</html>
<?php include 'footer.php'; ?>
