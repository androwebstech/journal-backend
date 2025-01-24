-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2025 at 08:32 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u654306003_jouranl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `journal_join_requests`
--

CREATE TABLE `journal_join_requests` (
  `req_id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `approval_status` enum('pending','approve','reject') DEFAULT 'pending',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_join_requests`
--

INSERT INTO `journal_join_requests` (`req_id`, `journal_id`, `user_id`, `created_at`, `approval_status`, `remarks`) VALUES
(1, 1, 8, '2025-01-23 16:33:03', '', NULL),
(2, 2, 8, '2025-01-23 16:46:36', 'pending', NULL),
(3, 2, 8, '2025-01-23 16:48:15', 'pending', NULL),
(4, 1, 2, '2025-01-23 16:49:15', 'pending', NULL),
(5, 2, 2, '2025-01-23 18:55:06', 'pending', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `journal_join_requests`
--
ALTER TABLE `journal_join_requests`
  ADD PRIMARY KEY (`req_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `journal_join_requests`
--
ALTER TABLE `journal_join_requests`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
