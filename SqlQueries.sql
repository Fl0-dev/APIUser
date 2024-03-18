CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    address VARCHAR(255),
    postal_code VARCHAR(255),
    city VARCHAR(255),
    phone VARCHAR(30),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_connected DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT 0
);

CREATE INDEX last_connected ON users (last_connected);