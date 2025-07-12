-- schema.sql

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS questions (
    id SERIAL PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    pages INT NOT NULL,
    description TEXT NOT NULL,
    other_info TEXT,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS messages (
    id SERIAL PRIMARY KEY,
    task_id INT NOT NULL,
    sender_role VARCHAR(10) CHECK (sender_role IN ('student', 'admin')),
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES questions(id)
);
CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
