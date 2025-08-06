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
        <div style="position: relative; display: inline-block;">
            <button id="notif-btn" style="background: none; border: none; font-size: 20px; cursor: pointer;">
                🔔 <span id="notif-count" style="color: white; background: red; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: absolute; top: -8px; right: -10px; display: none;"></span>
            </button>
            <div id="notif-box" style="display: none; position: absolute; right: 0; background: #fff; box-shadow: 0 0 8px rgba(0,0,0,0.2); width: 300px; z-index: 100; border-radius: 5px; overflow: auto; max-height: 300px;">
                <ul id="notif-list" style="list-style: none; padding: 10px; margin: 0;"></ul>
                <div style="text-align: center; padding: 5px;">
                    <button id="mute-btn" style="font-size: 12px; background: none; border: none; cursor: pointer;">🔊 Sound On</button>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- 🔔 Sound -->
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
