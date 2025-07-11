<?php
$host = "dpg-d1ogat3e5dus73e6k5ug-a";
$port = "5432";
$dbname = "smartlearn_iaqb";
$user = "smartlearn_iaqb_user";
$password = "aGJqiJZiRQxg0vG5hZsNYbDnqWQv49tC";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Suppress DB errors for non-critical pages
    error_log("DB connection failed: " . $e->getMessage());
    $conn = null; // let the page continue
}
?>
