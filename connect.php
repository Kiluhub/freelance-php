<?php
$host = "dpg-d1ogat3e5dus73e6k5ug-a"; // internal hostname (Render)
$port = "5432";
$dbname = "smartlearn_iaqb";
$user = "smartlearn_iaqb_user";
$password = "aGJqiJZiRQxg0vG5hZsNYbDnqWQv49tC";

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // ✅ Create 'users' table if not exists
    $create = "
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $conn->exec($create);

} catch (PDOException $e) {
    error_log("DB connection failed: " . $e->getMessage());
    echo "<p style='color:red;'>Database connection failed. Please try again later.</p>";
    $conn = null;
}

?>
