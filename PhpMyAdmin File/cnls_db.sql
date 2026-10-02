-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 07:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cnls_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `penalty_amount` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `note_id`, `borrower_id`, `loan_date`, `due_date`, `return_date`, `penalty_amount`) VALUES
(1, 3, 2, '2026-04-20', '2026-04-27', '2026-05-10', 65.00),
(2, 5, 1, '2026-04-23', '2026-04-30', NULL, 0.00),
(3, 10, 4, '2026-04-15', '2026-04-22', NULL, 0.00),
(4, 1, 3, '2026-03-01', '2026-03-08', '2026-03-08', 0.00),
(5, 2, 4, '2026-03-05', '2026-03-12', '2026-03-15', 15.00),
(6, 6, 5, '2026-03-10', '2026-03-17', '2026-03-20', 15.00),
(7, 12, 6, '2026-05-04', '2026-05-11', NULL, 0.00),
(8, 2, 8, '2026-05-06', '2026-05-13', '2026-05-20', 35.00);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `name`, `student_number`, `department`, `email`, `created_at`) VALUES
(1, 'Zehra Betul Celik', '20221805057', 'Management Information Systems', 'zehra.celik@stu.khas.edu.tr', '2026-05-05 21:40:33'),
(2, 'Ibrahim Akin Dalgic', '20211805054', 'Management Information Systems', 'ibrahim.dalgic@stu.khas.edu.tr', '2026-05-05 21:40:33'),
(3, 'Kaan Bakir', '20221805061', 'Computer Engineering', 'kaan.bakir@stu.khas.edu.tr', '2026-05-05 21:40:33'),
(4, 'Oğuzhan Ceylan', '20221805070', 'Industrial Engineering', 'oguzhan.ceylan@khas.edu.tr', '2026-05-05 21:40:33'),
(5, 'Mert İlhan Ecevit', '20221805080', 'Computer Engineering', 'mertilhan.ecevit@khas.edu.tr', '2026-05-05 21:40:33'),
(6, 'Demo Member', '20210000000', 'Business Administration', 'demo.delete@stu.khas.edu.tr', '2026-05-05 21:40:33'),
(8, 'Buse Kehribar', '20221810099', 'Psyc', 'busekehribar@stu.khas.edu.tr', '2026-05-06 17:44:00'),
(9, 'New Member', '20260000000', 'Drama', 'member@stu.khas.edu.tr', '2026-05-06 18:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `note_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `status` enum('Available','Borrowed') NOT NULL DEFAULT 'Available',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`note_id`, `title`, `course_name`, `department`, `owner_id`, `status`, `created_at`) VALUES
(1, 'Database Final Notes', 'Database Design and Management', 'Management Information Systems', 1, 'Available', '2026-05-05 21:40:33'),
(2, 'PHP Week 1-8 Summary', 'Server-Side Programming', 'Management Information Systems', 2, 'Available', '2026-05-05 21:40:33'),
(3, 'Data Mining Classification Notes', 'Data Mining', 'Management Information Systems', 1, 'Available', '2026-05-05 21:40:33'),
(4, 'Computer Networks OSI Model', 'Computer Networking and Security', 'Computer Engineering', 3, 'Available', '2026-05-05 21:40:33'),
(5, 'Cloud Computing AWS Lambda Notes', 'Cloud Computing', 'Computer Engineering', 5, 'Borrowed', '2026-05-05 21:40:33'),
(6, 'Statistics Midterm Summary', 'Statistics', 'Industrial Engineering', 4, 'Available', '2026-05-05 21:40:33'),
(7, 'Business Intelligence Dashboard Notes', 'Business Intelligence', 'Management Information Systems', 2, 'Available', '2026-05-05 21:40:33'),
(8, 'Algorithm Complexity Cheat Sheet', 'Algorithms', 'Computer Engineering', 3, 'Available', '2026-05-05 21:40:33'),
(9, 'System Analysis DFD Notes', 'Systems Analysis and Design', 'Management Information Systems', 1, 'Available', '2026-05-05 21:40:33'),
(10, 'Marketing Analytics GA4 Notes', 'Digital Marketing Analytics', 'Management Information Systems', 2, 'Borrowed', '2026-05-05 21:40:33'),
(12, 'MIS325 Final Works', 'Data Mining', 'Management Information Systems', 1, 'Borrowed', '2026-05-05 22:18:34'),
(13, 'Football Exam Notes', 'History of Soccer', 'Free-Election', 2, 'Available', '2026-05-06 18:46:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `fk_loans_note` (`note_id`),
  ADD KEY `fk_loans_borrower` (`borrower_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `fk_notes_owner` (`owner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_borrower` FOREIGN KEY (`borrower_id`) REFERENCES `members` (`member_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_loans_note` FOREIGN KEY (`note_id`) REFERENCES `notes` (`note_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_owner` FOREIGN KEY (`owner_id`) REFERENCES `members` (`member_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
