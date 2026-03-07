

CREATE DATABASE flood_relief_db;
USE flood_relief_db;

CREATE TABLE IF  users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE relief_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    relief_type ENUM('Food', 'Water', 'Medicine', 'Shelter') NOT NULL,
    district VARCHAR(100) NOT NULL,
    divisional_secretariat VARCHAR(100) NOT NULL,
    gn_division VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    family_members INT NOT NULL,
    severity ENUM('Low', 'Medium', 'High') NOT NULL,
    description TEXT,
    status ENUM('Pending', 'Processing', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO users (full_name, email, password, phone, role) 
VALUES ('System Administrator', 'admin@gmail.com', 'admin', '0112345678', 'admin')
ON DUPLICATE KEY UPDATE email = email;
