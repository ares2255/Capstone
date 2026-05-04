CREATE DATABASE IF NOT EXISTS netcafepos;
USE netcafepos;

-- 1. Users Table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255),
    role ENUM('admin','staff')
);

INSERT INTO users (username, password, role) VALUES
('admin', MD5('admin123'), 'admin'),
('staff', MD5('staff123'), 'staff');

-- 2. Settings Table (THIS WAS YOUR SETTINGS.PHP ERROR)
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    id INT PRIMARY KEY,
    hourly_rate DECIMAL(10,2),
    minimum_charge DECIMAL(10,2),
    bw_rate DECIMAL(10,2),
    color_rate DECIMAL(10,2),
    billing_method VARCHAR(20)
);

-- Crucial: This inserts the "id 1" row that settings.php looks for
INSERT INTO settings (id, hourly_rate, minimum_charge, bw_rate, color_rate, billing_method) 
VALUES (1, 15.00, 5.00, 2.00, 10.00, 'per_minute');

-- 3. PCs Table
DROP TABLE IF EXISTS pcs;
CREATE TABLE pcs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    status ENUM('available','active') DEFAULT 'available'
);

INSERT INTO pcs (name) VALUES
('PC-01'),('PC-02'),('PC-03'),('PC-04'),
('PC-05'),('PC-06'),('PC-07'),('PC-08'),
('PC-09'),('PC-10'),('PC-11'),('PC-12');

-- 4. Sessions Table
DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pc_id INT,
    start_time DATETIME,
    end_time DATETIME,
    cost DECIMAL(10,2)
);

-- 5. Print Jobs Table
DROP TABLE IF EXISTS print_jobs;
CREATE TABLE print_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('bw','color'),
    pages INT,
    price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO pcs (name, status) VALUES ('PC-13', 'available');