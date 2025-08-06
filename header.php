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
    <style>
        .notif-wrapper {
            position: relative;
            display: inline-block;
        }

        #notif-box {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            width: 300px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
            z-index: 999;
            max-height: 400px;
            overflow-y: auto;
            border-radius: 6px;
        }

        #notif-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #notif-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        #notif-list li a {
            text-decoration: none;
            color: #333;
            display: block;
        }

        .notif-footer {
            text-align: center;
            padding: 5px;
        }

        #notif-count {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: relative;
            top: -10px;
            right: 5px;
        }

        .notif-empty {
            text-align: center;
            color: #777;
            padding: 10px;
        }
    </style>
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
let lastNotifCount = 0;

document.getElementById("mute-btn").addEventListener("click", () => {
    notifMuted = !notifMuted;
    document.getElementById("mute-btn").textContent = notifMuted ? "🔇 Sound Off" : "🔊 Sound On";
});

document.getElementById("notif-btn").addEventListener("click", () => {
    const box = document.getElementById("notif-box");
    box.style.display = (box.style.display === "block") ? "none" : "block";
});

function fetchNotifications() {
    fetch("fetch_notifications.php")
        .then(res => res.json())
        .then(data => {
            const notifCount = document.getElementById("notif-count");
            const notifList = document.getElementById("notif-list");
            notifList.innerHTML = "";

            if (data.length > 0) {
                notifCount.textContent = data.length;
                notifCount.style.display = "inline-block";

                // Play sound only if there's new notification
                if (!notifMuted && data.length > lastNotifCount) {
                    document.getElementById("notif-sound").play();
                }

                lastNotifCount = data.length;

                data.forEach(n => {
                    const li = document.createElement("li");
                    li.innerHTML = `
                        <a href="${n.link}">
                            <div>
                                <strong>${n.sender}</strong><br>
                                <span>${n.message}</span><br>
                                <small>${n.time}</small>
                            </div>
                        </a>
                    `;
                    notifList.appendChild(li);
                });

            } else {
                notifCount.style.display = "none";
                notifList.innerHTML = "<li class='notif-empty'>No new messages</li>";
                lastNotifCount = 0;
            }
        });
}

// Initial fetch + repeat every 0.5 seconds
fetchNotifications();
setInterval(fetchNotifications, 500);
</script>
