<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Us - SmartLearn</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f7f9fc;
            color: #333;
        }

        .contact-section {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #111;
            margin-bottom: 30px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            font-size: 16px;
        }

        .contact-item i {
            font-style: normal;
            font-weight: bold;
            margin-right: 12px;
            color: #008793;
            min-width: 28px;
        }

        @media (max-width: 600px) {
            .contact-section {
                margin: 30px 15px;
                padding: 25px;
            }
        }
    </style>
</head>
<body>

<div class="contact-section">
    <h2>Contact SmartLearn</h2>

    <div class="contact-item"><i>📧</i> Email: <a href="mailto:smartlearn.help@gmail.com">smartlearn.help@gmail.com</a></div>
    <div class="contact-item"><i>📞</i> Phone: +254 712 345 678</div>
    <div class="contact-item"><i>🌐</i> Website: <a href="https://smartlearn.com">smartlearn.com</a></div>
    <div class="contact-item"><i>⏰</i> Support Hours</div>
</div>

</body>
</html>

<?php include 'footer.php'; ?>
