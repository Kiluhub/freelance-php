<?php
$host = "dpg-d1ogat3e5dus73e6k5ug-a";
$port = "5432";
$dbname = "smartlearn_db";
$user = "smartlearn_iaqb_user";
$password = "aGJqiJZiRQxg0vG5hZsNYbDnqWQv49tC";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
