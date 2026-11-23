-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2026 at 05:28 PM
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
-- Database: `imboni_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `conduct_logs`
--

CREATE TABLE `conduct_logs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `law_id` int(11) NOT NULL,
  `marks_deducted` int(11) NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conduct_logs`
--

INSERT INTO `conduct_logs` (`id`, `student_id`, `user_id`, `law_id`, `marks_deducted`, `logged_at`) VALUES
(1, 1, 1, 3, 10, '2026-11-22 18:04:52');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `title`, `description`, `icon`, `sort_order`) VALUES
(1, 'Student Permissions', 'Manage student movements', 'fa-user-group', 1),
(2, 'Parent Messages', 'Keep parents informed', 'fa-comment-dots', 2),
(3, 'Return Tracking', 'Know when students come back', 'fa-regular fa-calendar', 3),
(4, 'Discipline Records', 'Record and monitor marks', 'fa-shield-halved', 4);

-- --------------------------------------------------------

--
-- Table structure for table `laws`
--

CREATE TABLE `laws` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `reduction_marks` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laws`
--

INSERT INTO `laws` (`id`, `name`, `reduction_marks`, `created_at`) VALUES
(1, 'Late to Class', 2, '2026-11-22 17:48:29'),
(2, 'No Uniform', 5, '2026-11-22 17:48:29'),
(3, 'Disrespectful', 10, '2026-11-22 17:48:29'),
(4, 'Fighting', 20, '2026-11-22 17:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `current_conduct_marks` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `grade`, `current_conduct_marks`) VALUES
(1, 'John Doe', 'Grade 10', 85),
(2, 'Jane Smith', 'Grade 11', 100),
(3, 'Mike Johnson', 'Grade 10', 85);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('headmaster','dod','dos','teacher','animation') NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_approved`, `created_at`) VALUES
(1, 'Head Master', 'head@imboni.com', 'password123', 'headmaster', 1, '2026-11-22 17:48:28'),
(2, 'Director of Discipline', 'dod@imboni.com', 'password123', 'dod', 1, '2026-11-22 17:48:28'),
(3, 'Teacher Smith', 'teacher@imboni.com', 'password123', 'teacher', 1, '2026-11-22 17:48:28'),
(4, 'cyusa', 'cyusa@gmail.com', '$2y$10$TXcx9gKPTJ/N3KWTbYMYo.mbxiF7LA/efueHKnDuaBj/pt6zs9Qtm', 'dod', 1, '2026-11-22 17:49:58'),
(5, 'mama', 'mama@gmail.com', '$2y$10$dkggC4kiXLGxmaygIK7Ss.KOd.1Otdhs3gVUBmcrMBc4NpcERKKUS', 'dod', 1, '2026-11-22 17:56:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conduct_logs`
--
ALTER TABLE `conduct_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `law_id` (`law_id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laws`
--
ALTER TABLE `laws`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grade` (`grade`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conduct_logs`
--
ALTER TABLE `conduct_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laws`
--
ALTER TABLE `laws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conduct_logs`
--
ALTER TABLE `conduct_logs`
  ADD CONSTRAINT `conduct_logs_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conduct_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conduct_logs_ibfk_3` FOREIGN KEY (`law_id`) REFERENCES `laws` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
