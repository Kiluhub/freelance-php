<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Us - SmartLearn</title>
    <style>
        body { font-family: Arial; background: #f0f4f8; padding: 40px; }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
        }
        h2 { text-align: center; margin-bottom: 25px; color: #111; }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 20px;
            padding: 12px 20px;
            background: black;
            color: white;
            border: none;
            border-radius: 5px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Contact Us</h2>
    <form action="mailto:your_email@example.com" method="post" enctype="text/plain">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message..." required></textarea>
        <button type="submit">Send Message</button>
    </form>
</div>

</body>
</html>
<?php include 'footer.php'; ?>
