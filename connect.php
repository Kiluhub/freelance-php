<?php
$host = "your_host"; // e.g. render's hostname
$port = "5432";
$dbname = "your_database_name";
$user = "your_database_user";
$password = "your_password";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
