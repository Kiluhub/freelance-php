<?php
session_start();
require 'connect.php'; // Ensure connect.php uses PDO

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $rawPassword = $_POST['password'];

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $rawPassword)) {
        $error = "⚠️ Password must be at least 8 characters and include letters, numbers, and special characters.";
    } else {
        $password = password_hash($rawPassword, PASSWORD_DEFAULT);
        try {
            $check = $conn->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
            $check->bindParam(':email', $email);
            $check->execute();

            if ($check->fetch()) {
                $error = "⚠️ Email already exists. Try logging in.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (:name, :email, :password)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .register-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .register-wrapper .form-box {
            max-width: 400px;
            width: 100%;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .register-wrapper input,
        .register-wrapper button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .register-wrapper button {
            background: black;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .register-wrapper button:hover {
            background: #333;
        }

        .register-wrapper a {
            display: block;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            color: #007bff;
        }

        .register-wrapper a:hover {
            text-decoration: underline;
        }

        .register-wrapper .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .register-wrapper .info {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="register-wrapper">
    <div class="form-box">
        <h2>Student Registration</h2>
        <form method="post" onsubmit="return validatePassword()">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" id="password" name="password" placeholder="Create Password" required>
            <p class="info">Password must be at least 8 characters and include a number, a letter, and a special character.</p>
            <button type="submit">Register</button>
            <a href="login.php">Already have an account? Login</a>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</div>

<script>
function validatePassword() {
    const password = document.getElementById("password").value;
    const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!regex.test(password)) {
        alert("Password must be at least 8 characters and include a letter, a number, and a special character.");
        return false;
    }
    return true;
}
</script>

<?php include 'footer.php'; ?>

</body>
</html>
