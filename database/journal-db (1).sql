-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2025 at 02:56 PM
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
-- Database: `journal-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` int(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(250) DEFAULT NULL,
  `author_name` varchar(200) NOT NULL,
  `author_details` text DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_table`
--

CREATE TABLE `contact_table` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_table`
--

INSERT INTO `contact_table` (`id`, `name`, `email`, `message`) VALUES
(1, 'arohi', 'neha@gmail.com', 'this is me arohi singh');

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` int(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` int(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_table`
--

CREATE TABLE `journal_table` (
  `id` int(11) NOT NULL,
  `journal_name` varchar(255) NOT NULL,
  `eissn_no` varchar(20) DEFAULT NULL,
  `pissn_no` varchar(20) DEFAULT NULL,
  `first_volume` int(11) DEFAULT NULL,
  `number_of_issue_per_year` int(11) DEFAULT NULL,
  `publisher_name` varchar(255) DEFAULT NULL,
  `broad_research_area` varchar(255) DEFAULT NULL,
  `website_link` varchar(255) DEFAULT NULL,
  `journal_submission_link` varchar(255) DEFAULT NULL,
  `indexing` varchar(255) DEFAULT NULL,
  `country` enum('USA','India','UK','Canada','Australia') NOT NULL,
  `state` enum('California','New York','Texas','Ontario','Queensland') NOT NULL,
  `publication` enum('Monthly','Quarterly','Yearly') NOT NULL,
  `usd_publication_charge` decimal(10,2) DEFAULT NULL,
  `review_type` enum('Single-blind','Double-blind','Open Review','Editorial Review') NOT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `jounal_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_table`
--

INSERT INTO `journal_table` (`id`, `journal_name`, `eissn_no`, `pissn_no`, `first_volume`, `number_of_issue_per_year`, `publisher_name`, `broad_research_area`, `website_link`, `journal_submission_link`, `indexing`, `country`, `state`, `publication`, `usd_publication_charge`, `review_type`, `publication_link`, `jounal_status`) VALUES
(1, 'Arohi', '222534', '324', 233, 3, 'aarti', 'patna', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', 'Yearly', '4.50', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0'),
(2, 'hey', '222534', '324', 233, 3, 'neha', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', 'Yearly', '4.50', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0'),
(3, 'hey', '222534', '324', 233, 3, 'neha', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', 'Yearly', '4.50', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0'),
(4, 'hey', '222534', '324', 233, 3, 'neha', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', 'Yearly', '4.50', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', 'pending'),
(5, 'hey', '222534', '324', 233, 3, 'neha', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', 'Yearly', '4.50', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `reviewers`
--

CREATE TABLE `reviewers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `reviewer_details` text DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` int(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `type` enum('author','publisher','reviewer','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `contact`, `password`, `type`) VALUES
(11, 'User', 'user@gmail.com', 2147483647, '111111', ''),
(13, 'User', 'useop@gmail.com', 2147483647, '$2y$10$NoI2fDHh8P0YDRvOZxIFN.vPY7rvtJrGHwZ77sLEbMX', ''),
(14, 'neha', 'neha@gmail.com', 77777777, '$2y$10$xRvt2x7lFfIewmLgPoVnle/b7hx1FNGTShA9NJjDzxu', 'author'),
(15, 'neha', 'na@gmail.com', 77777777, '$2y$10$4D4vyYolpR3SiAm.gizw8uYQOFy6LyoEKqVgxFSCd13yacP9fF7Xq', 'author'),
(16, 'neha', 'nehasingh@gmail.com', 77777777, '$2y$10$D8pAaJIKmDs.L7PFx.D.wumv.vDeEYfkqnySUZojDhy8vI8EIT29S', 'author'),
(17, 'neha', 'arohinehasingh@gmail.com', 77777777, '$2y$10$XCjvuwNBu5pDX7Zn661ncOFYWWWNSReOHDhtsZxKGxrt8jTKh8No.', 'author'),
(18, 'supriya', 'supriyaSignh@gmail.com', 2147483647, '$2y$10$uMeBxMyH66OlgG68kgV5POcHIQolObUOKUkQHVSpTdakoePwt.Xem', 'publisher'),
(19, 'supriya', 'ksupriya@gmail.com', 2147483647, '$2y$10$RLbWfwGuT9/uTgZb1ascLOOj988fSZfqyy1PetdWXUALLUQBnxNc2', 'reviewer'),
(20, 'Aarti Kumari', 'kaarti.kc@gmail.com', 2147483647, '$2y$10$hxLKK6rTMthYetjI4EfBkeWc5IBj2Ky/FI0eefcPT.MPDJsDP.sUC', 'author'),
(21, 'guptaAarti', 'gupta@gmail.com', 2147483647, '$2y$10$Wby/TFAJqgkzaIpERKVfR.L9tjujTZ30yHJaqqr1hi9Yuj..90F/2', 'publisher'),
(22, 'Aarti gupta', 'kaarti.kc9@gmail.com', 2147483647, '$2y$10$nTxNZSe27XtZtjhv6DlGEOP7QQx8Ul4YcmzLFXH7Y192/YSVE9/Ym', 'reviewer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_table`
--
ALTER TABLE `contact_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_table`
--
ALTER TABLE `journal_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviewers`
--
ALTER TABLE `reviewers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_table`
--
ALTER TABLE `contact_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `journal_table`
--
ALTER TABLE `journal_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviewers`
--
ALTER TABLE `reviewers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
