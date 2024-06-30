CREATE DATABASE hall_booking;

USE hall_booking;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMINISTRATIVE', 'HOD', 'PRINCIPAL', 'EXAMSECTION') NOT NULL
);

CREATE TABLE halls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    college ENUM('TU', 'PU') NOT NULL
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hall_id INT NOT NULL,
    user_id INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    speaker VARCHAR(255) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    letter VARCHAR(255) NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    approval_letter VARCHAR(255),
    FOREIGN KEY (hall_id) REFERENCES halls(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO halls (name, college) VALUES
('Hall 1', 'PU'),
('Hall 2', 'PU'),
('Hall 3', 'TU'),
('Hall 4', 'TU');

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@example.com', MD5('adminpass'), 'ADMINISTRATIVE');
