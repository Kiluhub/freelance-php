<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Testimonials - SmartLearn</title>
    <style>
        body { font-family: Arial; background: #f0f4f8; padding: 30px; }
        .wrapper { max-width: 900px; margin: auto; }
        h2 { text-align: center; color: #111; margin-bottom: 40px; }
        .testimonial-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .testimonial {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            text-align: center;
        }
        .testimonial img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .name { font-weight: bold; margin-bottom: 8px; }
        .comment { font-size: 14px; color: #444; }
    </style>
</head>
<body>

<div class="wrapper">
    <h2>What Students Say</h2>

    <div class="testimonial-box">
        <div class="testimonial">
            <img src="img/student1.jpg" alt="Student 1">
            <div class="name">Sarah Mwangi</div>
            <div class="comment">“SmartLearn helped me finish my final-year project. The tutor was super helpful and fast!”</div>
        </div>
        <div class="testimonial">
            <img src="img/student2.jpg" alt="Student 2">
            <div class="name">Mohammed Ali</div>
            <div class="comment">“Best platform for urgent essays. Got mine done overnight and got an A.”</div>
        </div>
        <div class="testimonial">
            <img src="img/student3.jpg" alt="Student 3">
            <div class="name">Brenda Achieng</div>
            <div class="comment">“The tutors are really professional and understand university-level work very well.”</div>
        </div>
    </div>
</div>

</body>
</html>
<?php include 'footer.php'; ?>
