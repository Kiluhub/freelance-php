<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['student_id']);
$studentName = $_SESSION['student_name'] ?? 'Student';
?>

<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartLearn</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">SmartLearn</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="tutors.php">Tutors</a>
        <a href="testimonials.php">Testimonials</a>
        <a href="contact.php">Contact</a>
        <a href="post_question.php" class="post-btn">Post a Question</a>

        <!-- 🔔 Notification Bell -->
        <div class="notif-wrapper">
            <button id="notif-btn">
                🔔 <span id="notif-count"></span>
            </button>
            <div id="notif-box">
                <ul id="notif-list"></ul>
                <div class="notif-footer">
                    <button id="mute-btn">🔊 Sound On</button>
                </div>
            </div>
        </div>

        <!-- 🔐 Login/Logout -->
        <?php if ($isLoggedIn): ?>
            <a href="logout.php">Logout (<?= htmlspecialchars($studentName) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<audio id="notif-sound" src="notif.mp3" preload="auto"></audio>

<script>
let notifMuted = false;

document.getElementById("mute-btn")?.addEventListener("click", () => {
    notifMuted = !notifMuted;
    document.getElementById("mute-btn").textContent = notifMuted ? "🔇 Sound Off" : "🔊 Sound On";
});

document.getElementById("notif-btn")?.addEventListener("click", () => {
    const box = document.getElementById("notif-box");
    box.style.display = (box.style.display === "block") ? "none" : "block";
});

function fetchNotifications() {
    fetch("fetch_notifications.php")
        .then(res => res.json())
        .then(data => {
            const count = data.length;
            const notifCount = document.getElementById("notif-count");
            const notifList = document.getElementById("notif-list");
            notifList.innerHTML = "";

            if (count > 0) {
                notifCount.textContent = count;
                notifCount.style.display = "inline-block";

                data.forEach(n => {
                    const li = document.createElement("li");
                    li.innerHTML = `
                        <a href="${n.link}" class="notif-item">
                            <div>
                                <strong>${n.sender}</strong><br>
                                <span>${n.message}</span><br>
                                <small>${n.time}</small>
                            </div>
                        </a>
                    `;
                    notifList.appendChild(li);
                });

                if (!notifMuted) document.getElementById("notif-sound").play();
            } else {
                notifCount.style.display = "none";
                notifList.innerHTML = "<li class='notif-empty'>No new messages</li>";
            }
        });
}

fetchNotifications();
setInterval(fetchNotifications, 30000);
</script>
