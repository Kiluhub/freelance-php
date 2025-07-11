
<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartLearn - Academic Help</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<section class="hero">
  <div class="carousel">
    <img src="img/student1.jpg" class="runner layer1">
    <img src="img/student2.jpg" class="runner layer2">
    <img src="img/student3.jpg" class="runner layer3">
  </div>
  <div class="cta">
    <h1>Join Thousands of Students Getting Help Today!</h1>
    <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>
  </div>
</section>

</body>
</html>

<?php include 'footer.php'; ?>
