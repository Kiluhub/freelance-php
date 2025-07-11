<?php
$host = "postgresql-infinity1.alwaysdata.net";
$port = "5432";
$dbname = "infinity1_db";
$user = "infinity1";
$password = "391082@Bk";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
