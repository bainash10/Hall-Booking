-- Create the database
CREATE DATABASE hall_booking;

-- Use the newly created database
USE hall_booking;

-- Create the users table with the updated structure
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMINISTRATIVE', 'HOD', 'PRINCIPAL', 'EXAMSECTION') NOT NULL,
    college ENUM('Khwopa Engineering College', 'Khwopa College of Engineering') NULL,
    department VARCHAR(100) NULL,
    photo LONGBLOB NOT NULL
);

-- Create the halls table with the updated structure
CREATE TABLE halls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    college ENUM('Khwopa Engineering College', 'Khwopa College of Engineering') NOT NULL
);

-- Create the bookings table with the updated structure
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hall_id INT NOT NULL,
    user_id INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    speaker VARCHAR(255) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    letter LONGBLOB NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    approval_letter LONGBLOB NULL,
    FOREIGN KEY (hall_id) REFERENCES halls(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert initial data into the halls table
INSERT INTO halls (name, college) VALUES
('Hall 1', 'Khwopa Engineering College'),
('Hall 2', 'Khwopa Engineering College'),
('Hall 3', 'Khwopa College of Engineering'),
('Hall 4', 'Khwopa College of Engineering');

-- Insert initial data into the users table (administrative user)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@example.com', MD5('adminpass'), 'ADMINISTRATIVE');
