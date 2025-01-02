-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 05:39 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `daily_reports_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_reports_table`
--

CREATE TABLE `daily_reports_table` (
  `id` int(11) NOT NULL,
  `head_name` varchar(255) NOT NULL,
  `count` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `department` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_reports_table`
--

INSERT INTO `daily_reports_table` (`id`, `head_name`, `count`, `remarks`, `user_id`, `created_at`, `department`) VALUES
(1, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(2, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(3, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(4, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(5, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(6, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(7, 'arohi', 1, 'text', 4, '2024-12-04 14:30:00', 0),
(8, 'Abcd', 2, 'text', 1, '2024-12-11 22:28:33', 0),
(9, 'Abcd', 2, 'text', 1, '2024-12-11 22:28:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `mobile_no` varchar(20) NOT NULL,
  `department` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `created_at`, `user_type`, `image`, `fname`, `lname`, `mobile_no`, `department`) VALUES
(1, 'ssgautamji9@gmail.com', '1111', '2024-11-14 18:28:45', 'admin', '', '', '', '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_reports_table`
--
ALTER TABLE `daily_reports_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_reports_table`
--
ALTER TABLE `daily_reports_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
