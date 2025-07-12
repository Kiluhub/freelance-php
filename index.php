<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    exit();
}
?>

<?php include 'header.php'; ?>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #f3f4f6, #e0f7fa);
    }

    .hero {
        position: relative;
        text-align: center;
        padding: 60px 20px;
        color: white;
        background: linear-gradient(to right, #004d7a, #008793, #00bf72, #a8eb12);
        background-size: 400% 400%;
        animation: gradientShift 10s ease infinite;
    }

    @keyframes gradientShift {
        0% {background-position: 0% 50%;}
        50% {background-position: 100% 50%;}
        100% {background-position: 0% 50%;}
    }

    .carousel {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
        animation: fadeIn 2s ease;
    }

    .carousel img {
        width: 250px;
        height: 160px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }

    .carousel img:hover {
        transform: scale(1.05);
    }

    .cta h1 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: #fff;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
    }

    .post-btn {
        background-color: #ff1744;
        color: white;
        padding: 15px 30px;
        font-size: 18px;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .post-btn:hover {
        background-color: #d50000;
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
        .carousel {
            flex-direction: column;
            align-items: center;
        }
    }

    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }
</style>

<section class="hero">
    <div class="carousel">
        <img src="images/student1.jpg" alt="Student 1">
        <img src="images/student2.jpg" alt="Student 2">
        <img src="images/student3.jpg" alt="Student 3">
    </div>
    <div class="cta">
        <h1>Join Thousands of Students Getting Help Today!</h1>
        <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>
    </div>
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
