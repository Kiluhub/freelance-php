<?php
require 'connect.php'; // DB connection file

// Helper function to send email using Resend API
function sendResetEmail($toEmail, $toName, $token) {
    $apiKey = 're_693fPgJ4_JDggiDmj1yner7yGp6PmBPwo'; // <-- Replace with real key
    $resetLink = "https://freelance-php-iyko.onrender.com/reset_password.php?token=$token";

    $data = [
        "from" => "SmartLearn <onboarding@resend.dev>",
        "to" => [$toEmail],
        "subject" => "Reset Your SmartLearn Password",
        "html" => "
            <p>Hello <strong>$toName</strong>,</p>
            <p>You requested a password reset. Click the link below:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link will expire in 1 hour.</p>
        "
    ];

    $ch = curl_init("https://api.resend.com/emails");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 202) {
        return true;
    } else {
        return "Failed to send email: $response";
    }
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        echo "Please enter your email.";
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "❌ No account found with that email.";
        exit;
    }

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save to DB
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
    $stmt->execute([
        'email' => $email,
        'token' => $token,
        'expires' => $expiry
    ]);

    // Send the email
    $result = sendResetEmail($email, $user['name'], $token);

    if ($result === true) {
        echo "✅ A reset link has been sent to your email.";
    } else {
        echo "❌ $result";
    }
} else {
    echo "Invalid request.";
}
?>
