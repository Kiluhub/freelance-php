<?php
session_start();
require 'connect.php';

$hasTasks = false;

if (isset($_SESSION['student_id'])) {
    $stmt = $conn->prepare("SELECT id FROM questions WHERE student_id = :sid LIMIT 1");
    $stmt->execute(['sid' => $_SESSION['student_id']]);
    $hasTasks = $stmt->fetch() !== false;
}

include 'header.php';
?>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #f3f4f6, #e0f7fa);
    }

    .hero {
        position: relative;
        text-align: center;
        color: white;
        background: linear-gradient(to right, #004d7a, #008793, #00bf72, #a8eb12);
        background-size: 400% 400%;
        animation: gradientShift 10s ease infinite;
        min-height: 90vh;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        padding-top: 40px;
    }

    @keyframes gradientShift {
        0% {background-position: 0% 50%;}
        50% {background-position: 100% 50%;}
        100% {background-position: 0% 50%;}
    }

    .carousel {
        width: 100%;
        max-height: 70vh;
        overflow: hidden;
        margin-top: 30px;
    }

    .carousel img {
        width: 100%;
        height: 70vh;
        object-fit: cover;
        transition: opacity 0.5s ease-in-out;
    }

    .cta h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
        color: #fff;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
    }

    .post-btn, .tasks-btn {
        display: inline-block;
        margin: 10px;
        background-color: #ff1744;
        color: white;
        padding: 15px 30px;
        font-size: 18px;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .tasks-btn {
        background-color: #00796b;
    }

    .post-btn:hover {
        background-color: #d50000;
        transform: scale(1.05);
    }

    .tasks-btn:hover {
        background-color: #004d40;
        transform: scale(1.05);
    }

    .features {
        padding: 50px 20px;
        background-color: #ffffff;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 40px;
    }

    .feature-card {
        background: #f9fafb;
        padding: 25px;
        border-radius: 12px;
        max-width: 300px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-card h3 {
        color: #333;
        margin-bottom: 15px;
    }

    .feature-card p {
        color: #555;
        font-size: 15px;
    }

    @media (max-width: 768px) {
        .carousel img {
            height: 50vh;
        }

        .cta h1 {
            font-size: 2rem;
        }

        .post-btn, .tasks-btn {
            padding: 12px 20px;
            font-size: 16px;
        }
    }
</style>

<section class="hero">
    <div class="cta">
        <h1>Join Thousands of Students and Professionals Getting Help Today!</h1>
        <h2>Experience first—pay only when you're satisfied. Your trust matters to us.<h2>
        <a href="post_question.php" class="post-btn">➕ Post Your Question</a>
        <?php if ($hasTasks): ?>
            <a href="submit_question.php" class="tasks-btn">📁 View My Tasks</a>
        <?php endif; ?>
    </div>

    <div class="carousel">
        <img id="carousel-img" src="images/student0.jpg" alt="Student image">
    </div>

    <script>
        const images = [
            "images/student1.jpg",
            "images/student2.jpg",
            "images/student3.jpg",
            "images/student4.jpg"
        ];
        let current = 0;
        const img = document.getElementById("carousel-img");

        setInterval(() => {
            current = (current + 1) % images.length;
            img.style.opacity = 0;
            setTimeout(() => {
                img.src = images[current];
                img.style.opacity = 1;
            }, 200);
        }, 1500);
    </script>
</section>

<section class="features">
    <div class="feature-card">
        <h3>📚 Wide Range of Subjects</h3>
        <p>Get help in math, science, writing, coding, and more — from qualified tutors worldwide.</p>
    </div>
    <div class="feature-card">
        <h3>⏰ Fast Turnaround</h3>
        <p>Set your deadline and get answers when you need them — even in a few hours.</p>
    </div>
    <div class="feature-card">
        <h3>💬 Real-Time Support</h3>
        <p>Live chat with your tutor, share files, and ask follow-ups instantly through our platform.</p>
    </div>
    <div class="feature-card">
        <h3>💵 Flexible Pricing</h3>
        <p>Pay based on your budget starting from just $1. Fair and transparent pricing always.</p>
    </div>
</section>

<?php include 'footer.php'; ?>
