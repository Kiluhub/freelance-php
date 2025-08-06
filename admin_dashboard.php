<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';

// Check JWT token
if (!isset($_COOKIE['admin_token'])) {
    header("Location: admin_auth.php");
    exit;
}

try {
    $decoded = JWT::decode($_COOKIE['admin_token'], new Key($secretKey, 'HS256'));
    if ($decoded->role !== 'admin') {
        throw new Exception("Unauthorized");
    }
} catch (Exception $e) {
    header("Location: admin_auth.php");
    exit;
}

// Fetch submitted tasks with student ID and name
$sql = "SELECT q.*, u.id AS student_id, u.full_name 
        FROM questions q
        JOIN users u ON q.student_id = u.id
        ORDER BY q.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f6fa;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #001f3f;
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
        }

        h2 {
            text-align: center;
            margin: 40px 0 20px;
        }

        .logout-btn {
            background: red;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        .notif-container {
            position: relative;
        }

        #notif-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: white;
            cursor: pointer;
        }

        #notif-count {
            color: white;
            background: red;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: absolute;
            top: -8px;
            right: -10px;
            display: none;
        }

        #notif-box {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
            width: 300px;
            z-index: 100;
            border-radius: 5px;
            overflow: auto;
            max-height: 300px;
        }

        #notif-list {
            list-style: none;
            padding: 10px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px #ccc;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: black;
            color: white;
        }

        .expand-toggle {
            background: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .description-content {
            display: none;
            margin-top: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }

        .chat-link {
            background: green;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .download-btn {
            background: darkorange;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<header>
    <div style="font-size: 22px; font-weight: bold;">Admin Dashboard</div>

    <div style="display: flex; align-items: center; gap: 20px;">
        <!-- 🔔 Notification Bell -->
        <div class="notif-container">
            <button id="notif-btn">🔔 <span id="notif-count"></span></button>
            <div id="notif-box">
                <ul id="notif-list"></ul>
                <div style="text-align: center; padding: 5px;">
                    <button id="mute-btn">🔊 Sound On</button>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <form method="post" action="admin_logout.php" style="margin:0;">
            <button class="logout-btn">Logout</button>
        </form>
    </div>
</header>

<h2>Submitted Questions</h2>

<table>
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Title</th>
        <th>Pages</th>
        <th>Price ($)</th>
        <th>Description</th>
        <th>File</th>
        <th>Posted</th>
        <th>Chat</th>
    </tr>

    <?php $i = 0; ?>
    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= (int)$row['pages'] ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td>
                <button class="expand-toggle" onclick="toggleDescription(<?= $i ?>)">View</button>
                <div class="description-content" id="desc-<?= $i ?>">
                    <strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?><br><br>
                    <strong>Other Info:</strong><br><?= nl2br(htmlspecialchars($row['other_info'])) ?>
                </div>
            </td>
            <td>
                <?php if (!empty($row['file_path'])): ?>
                    <a class="download-btn" href="<?= htmlspecialchars($row['file_path']) ?>" download>Download</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a class="chat-link" href="admin_chat.php?task_id=<?= $row['id'] ?>">Chat</a>
            </td>
        </tr>
    <?php $i++; endwhile; ?>
</table>

<!-- 🔔 Sound -->
<audio id="notif-sound" src="notif.mp3" preload="auto"></audio>

<script>
function toggleDescription(index) {
    const content = document.getElementById("desc-" + index);
    content.style.display = (content.style.display === "block") ? "none" : "block";
}

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
                        <a href="${n.link}" style="text-decoration:none; color:#333;">
                            <div style="padding:8px; border-bottom:1px solid #eee;">
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
                notifList.innerHTML = "<li style='padding:10px; text-align:center;'>No new messages</li>";
            }
        });
}

fetchNotifications();
setInterval(fetchNotifications, 30000);
</script>

</body>
</html>
