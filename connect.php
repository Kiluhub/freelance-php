<?php
$host = "dpg-d1ogat3e5dus73e6k5ug-a"; // Internal hostname
$port = "5432";
$dbname = "smartlearn_iaqb";
$user = "smartlearn_iaqb_user";
$password = "aGJqiJZiRQxg0vG5hZsNYbDnqWQv49tC";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    error_log("DB connection failed: " . $e->getMessage());
    echo "<p style='color:red;'>Database connection failed. Please try again later.</p>";
    $conn = null;
}
?>
