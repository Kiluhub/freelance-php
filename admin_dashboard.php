<?php
require 'connect.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = getenv('JWT_SECRET') ?: 'your-very-secret-key';

if (!isset($_COOKIE['admin_token'])) {
    header("Location: login.php");
    exit;
}

try {
    $decoded = JWT::decode($_COOKIE['admin_token'], new Key($secretKey, 'HS256'));
    if ($decoded->role !== 'admin') {
        throw new Exception("Unauthorized");
    }
    $adminName = $decoded->name ?? 'Admin';
} catch (Exception $e) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #222;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background-color: #222;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .badge {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            vertical-align: top;
            margin-left: 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 300px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0;
            color: black;
        }

        .dropdown-content li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content li:last-child {
            border-bottom: none;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: black;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .time {
            display: block;
            font-size: 12px;
            color: gray;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Welcome, <?php echo htmlspecialchars($adminName); ?></h1>

        <div class="dropdown">
            <button class="dropbtn">
                🔔 Notifications <span class="badge" id="notifCount">0</span>
            </button>
            <ul class="dropdown-content" id="notifDropdown">
                <li>No new messages</li>
            </ul>
        </div>
    </div>

    <script>
        async function fetchNotifications() {
            try {
                const res = await fetch('fetch_notifications.php');
                const data = await res.json();

                const dropdown = document.getElementById('notifDropdown');
                const count = document.getElementById('notifCount');

                dropdown.innerHTML = '';

                if (data.length === 0) {
                    dropdown.innerHTML = '<li>No new messages</li>';
                    count.textContent = '0';
                } else {
                    data.forEach(n => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <a href="${n.link}">
                                <strong>${n.sender}</strong>: ${n.message}<br>
                                <span class="time">${n.time}</span>
                            </a>
                        `;

                        // Optional: Mark as read on click
                        li.querySelector('a').addEventListener('click', () => {
                            fetch('mark_notification_read.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `message_id=${n.id}`
                            });
                        });

                        dropdown.appendChild(li);
                    });

                    count.textContent = data.length;
                }
            } catch (err) {
                console.error('Failed to fetch notifications:', err);
            }
        }

        fetchNotifications();
        setInterval(fetchNotifications, 1000);
    </script>
</body>
</html>
