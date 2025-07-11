<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Tutors - SmartLearn</title>
     
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6fa;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #111;
        }

        .tutor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            max-width: 1100px;
            margin: auto;
        }

        .tutor-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            position: relative;
            z-index: 1;
        }

        .tutor-card:hover {
            transform: translateY(-5px);
            z-index: 10;
        }

        .tutor-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .tutor-details {
            padding: 20px;
        }

        .tutor-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .tutor-subject {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .tutor-rating {
            color: #ffa500;
            margin-bottom: 12px;
        }

        .tutor-desc {
            font-size: 14px;
            color: #444;
            margin-bottom: 15px;
        }

        .contact-link {
            display: inline-block;
            background: black;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .contact-link:hover {
            background: #222;
        }
    </style>
</head>
<body>

<h2>Meet Our Top Tutors</h2>

<div class="tutor-grid">

    <?php
    $tutors = [
        ["name" => "Dr. Grace Wambui", "subject" => "Nursing & Healthcare", "rating" => 4.9, "img" => "img/tutor1.jpg", "desc" => "Over 8 years of experience in clinical case analysis and nursing essays."],
        ["name" => "James Otieno", "subject" => "Engineering & Math", "rating" => 4.7, "img" => "img/tutor2.jpg", "desc" => "Mechanical engineer helping students with technical problem solving."],
        ["name" => "Maria Sanchez", "subject" => "Literature & Humanities", "rating" => 4.8, "img" => "img/tutor3.jpg", "desc" => "Passionate about creative writing, essays, and research reviews."],
        ["name" => "Ali Mohammed", "subject" => "Finance & Business", "rating" => 4.6, "img" => "img/tutor4.jpg", "desc" => "Expert in financial modeling, accounting, and market analysis."],
        ["name" => "Chen Liu", "subject" => "Computer Science", "rating" => 5.0, "img" => "img/tutor5.jpg", "desc" => "Specializes in algorithms, data structures, and project help."]
    ];

    foreach ($tutors as $tutor):
    ?>
        <div class="tutor-card">
            <img class="tutor-image" src="<?= $tutor['img'] ?>" alt="Tutor Image">
            <div class="tutor-details">
                <div class="tutor-name"><?= $tutor['name'] ?></div>
                <div class="tutor-subject"><?= $tutor['subject'] ?></div>
                <div class="tutor-rating">⭐ <?= number_format($tutor['rating'], 1) ?>/5.0</div>
                <div class="tutor-desc"><?= $tutor['desc'] ?></div>
                <a href="contact.php" class="contact-link">Contact Tutor</a>
            </div>
        </div>
    <?php endforeach; ?>

</div>

</body>
</html>
<?php include 'footer.php'; ?>
