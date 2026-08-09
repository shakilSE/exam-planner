-- Exam Hall Seat & Invigilator Planner Database
-- Import this file in phpMyAdmin

CREATE DATABASE IF NOT EXISTS exam_planner CHARACTER SET utf8 COLLATE utf8_general_ci;
USE exam_planner;

-- Admin users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: admin@exam.com / admin123
INSERT INTO users (name, email, password) VALUES 
('Administrator', 'admin@exam.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Batches / Departments
CREATE TABLE IF NOT EXISTS batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    total_students INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(50) NOT NULL,
    building VARCHAR(100),
    capacity INT NOT NULL,
    `rows` INT NOT NULL DEFAULT 5,
    `cols` INT NOT NULL DEFAULT 6,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers / Invigilators
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    duty_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    batch_id INT NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
);

-- Exams
CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    exam_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending','generated','published') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exam Batches (which batches appear in which exam)
CREATE TABLE IF NOT EXISTS exam_batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    batch_id INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
);

-- Seat Plan
CREATE TABLE IF NOT EXISTS seat_plan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    seat_row INT NOT NULL,
    seat_col INT NOT NULL,
    seat_no VARCHAR(20) NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Duty Plan
CREATE TABLE IF NOT EXISTS duty_plan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    teacher_id INT NOT NULL,
    room_id INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Sample Data
INSERT INTO batches (name, department, total_students) VALUES
('CSE-A 2021', 'Computer Science', 40),
('CSE-B 2021', 'Computer Science', 38),
('EEE-A 2021', 'Electrical Engineering', 42),
('BBA-A 2021', 'Business Administration', 45);

INSERT INTO rooms (room_no, building, capacity,  `rows`, `cols`) VALUES
('101', 'Academic Building A', 30, 5, 6),
('102', 'Academic Building A', 30, 5, 6),
('201', 'Academic Building B', 40, 5, 8),
('202', 'Academic Building B', 40, 5, 8);

INSERT INTO teachers (name, department, email, phone) VALUES
('Dr. Rafiqul Islam', 'Computer Science', 'rafiq@uni.edu', '01711000001'),
('Prof. Nasrin Akter', 'Electrical Engineering', 'nasrin@uni.edu', '01711000002'),
('Mr. Kamal Hossain', 'Mathematics', 'kamal@uni.edu', '01711000003'),
('Ms. Sonia Begum', 'Business Administration', 'sonia@uni.edu', '01711000004'),
('Dr. Aminul Islam', 'Physics', 'aminul@uni.edu', '01711000005'),
('Prof. Razia Sultana', 'Chemistry', 'razia@uni.edu', '01711000006');
