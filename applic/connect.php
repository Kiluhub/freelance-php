<?php
$host = "dpg-d1octeruibrs73ck17kg-a";
$port = "5432";
$dbname = "smartlearn_db";
$user = "smartlearn_user";
$password = "pmlynWWOoOX6zYU3jxPWiqJLfEQX8pS6";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
