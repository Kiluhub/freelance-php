<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Testimonials - SmartLearn</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #111;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            max-width: 1100px;
            margin: auto;
        }

        .testimonial-card {
            background: white;
            border-left: 5px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .testimonial-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 6px;
        }

        .testimonial-role {
            font-size: 13px;
            color: #777;
            margin-bottom: 10px;
        }

        .testimonial-text {
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<h2>What Students Are Saying</h2>

<div class="testimonial-grid">

    <?php
    $testimonials = [
        ["name" => "Jeniffer Aniston", "role" => "Student", "text" => "SmartLearn helped me finish my final-year project. The tutor was super helpful and fast!"],
        ["name" => "Mohammed Ali", "role" => "Student", "text" => "Best platform for urgent essays. Got mine done overnight and got an A."],
        ["name" => "Abdullahi Mohamed", "role" => "Student", "text" => "The tutors are really professional and understand university-level work very well."],
        ["name" => "Kevin Hakeem", "role" => "Student", "text" => "As a working student, I appreciate the flexibility SmartLearn gives me to meet deadlines."],
        ["name" => "Faith Oliver", "role" => "Student", "text" => "Excellent support and fast responses. I’ve used SmartLearn for two semesters now!"],
        ["name" => "Diego Alejandro", "role" => "Student", "text" => "I was nervous at first, but SmartLearn exceeded my expectations. Great job!"]
    ];

    foreach ($testimonials as $t) {
        echo '<div class="testimonial-card">';
        echo '<div class="testimonial-name">' . htmlspecialchars($t['name']) . '</div>';
        echo '<div class="testimonial-role">' . htmlspecialchars($t['role']) . '</div>';
        echo '<div class="testimonial-text">“' . htmlspecialchars($t['text']) . '”</div>';
        echo '</div>';
    }
    ?>

</div>

</body>
</html>
