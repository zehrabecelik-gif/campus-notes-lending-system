CREATE DATABASE IF NOT EXISTS cnls_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE cnls_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS notes;
DROP TABLE IF EXISTS members;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    department VARCHAR(100) NOT NULL,
    owner_id INT NOT NULL,
    status ENUM('Available','Borrowed') NOT NULL DEFAULT 'Available',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notes_owner
        FOREIGN KEY (owner_id) REFERENCES members(member_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE loans (
    loan_id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    borrower_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    penalty_amount DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_loans_note
        FOREIGN KEY (note_id) REFERENCES notes(note_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_loans_borrower
        FOREIGN KEY (borrower_id) REFERENCES members(member_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO members (name, student_number, department, email) VALUES
('Zehra Betul Celik', '20221805057', 'Management Information Systems', 'zehra.celik@stu.khas.edu.tr'),
('Ibrahim Akin Dalgic', '20211805054', 'Management Information Systems', 'ibrahim.dalgic@stu.khas.edu.tr'),
('Kaan Bakir', '20221805061', 'Computer Engineering', 'kaan.bakir@stu.khas.edu.tr'),
('Elif Yilmaz', '20221805070', 'Industrial Engineering', 'elif.yilmaz@stu.khas.edu.tr'),
('Mert Demir', '20221805080', 'Computer Engineering', 'mert.demir@stu.khas.edu.tr'),
('Demo Delete Member', '20229999999', 'Business Administration', 'demo.delete@stu.khas.edu.tr');

INSERT INTO notes (title, course_name, department, owner_id, status) VALUES
('Database Final Notes', 'Database Design and Management', 'Management Information Systems', 1, 'Available'),
('PHP Week 1-8 Summary', 'Server-side Programming', 'Management Information Systems', 2, 'Available'),
('Data Mining Classification Notes', 'Data Mining', 'Management Information Systems', 1, 'Borrowed'),
('Computer Networks OSI Model', 'Computer Networking and Security', 'Computer Engineering', 3, 'Available'),
('Cloud Computing AWS Lambda Notes', 'Cloud Computing', 'Computer Engineering', 5, 'Borrowed'),
('Statistics Midterm Summary', 'Statistics', 'Industrial Engineering', 4, 'Available'),
('Business Intelligence Dashboard Notes', 'Business Intelligence', 'Management Information Systems', 2, 'Available'),
('Algorithm Complexity Cheat Sheet', 'Algorithms', 'Computer Engineering', 3, 'Available'),
('System Analysis DFD Notes', 'Systems Analysis and Design', 'Management Information Systems', 1, 'Available'),
('Marketing Analytics GA4 Notes', 'Digital Marketing Analytics', 'Management Information Systems', 2, 'Borrowed'),
('Demo Delete Note', 'Demo Course', 'Business Administration', 6, 'Available');

INSERT INTO loans (note_id, borrower_id, loan_date, due_date, return_date, penalty_amount) VALUES
(3, 2, '2026-04-20', '2026-04-27', NULL, 0.00),
(5, 1, '2026-04-23', '2026-04-30', NULL, 0.00),
(10, 4, '2026-04-15', '2026-04-22', NULL, 0.00),
(1, 3, '2026-03-01', '2026-03-08', '2026-03-08', 0.00),
(2, 4, '2026-03-05', '2026-03-12', '2026-03-15', 15.00),
(6, 5, '2026-03-10', '2026-03-17', '2026-03-20', 15.00);