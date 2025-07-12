<?php
require 'connect.php'; // your usual connection script

try {
    $sql = "
        CREATE TABLE IF NOT EXISTS messages (
            id SERIAL PRIMARY KEY,
            task_id INT NOT NULL,
            sender_role VARCHAR(10) CHECK (sender_role IN ('student', 'admin')),
            message TEXT NOT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES questions(id)
        );
    ";
    $conn->exec($sql);
    echo "✅ messages table created.";
} catch (PDOException $e) {
    echo "❌ Error creating table: " . $e->getMessage();
}
?>
