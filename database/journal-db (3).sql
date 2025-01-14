-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2025 at 02:00 PM
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
  `admin_id` int(30) NOT NULL,
  `profile_image` varchar(50) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(250) DEFAULT NULL,
  `contact` varchar(15) NOT NULL,
  `author_name` varchar(200) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`user_id`, `profile_image`, `contact`, `author_name`, `department`, `designation`, `created_at`) VALUES
(23, NULL, '8888888888', 'shivam', NULL, NULL, '2025-01-13 16:39:33'),
(24, NULL, '8888888888', 'shivam', NULL, NULL, '2025-01-13 16:41:13'),
(29, NULL, '68574939478', 'aarti gupta', NULL, NULL, '2025-01-13 17:55:20');

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
  `journal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `journal_name` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `paid` tinyint(1) NOT NULL,
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
  `jounal_status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journals`
--

INSERT INTO `journals` (`journal_id`, `user_id`, `journal_name`, `status`, `paid`, `eissn_no`, `pissn_no`, `first_volume`, `number_of_issue_per_year`, `publisher_name`, `broad_research_area`, `website_link`, `journal_submission_link`, `indexing`, `country`, `state`, `publication`, `usd_publication_charge`, `review_type`, `publication_link`, `jounal_status`, `created_at`) VALUES
(8, 22, 'Information SecurityCentral university', 'pending', 0, '222534', '324', 233, 0, 'Aarti', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', '', '4.00', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 10:04:53'),
(10, 22, 'Sneha', 'pending', 0, '222534', '324', 233, 0, 'abcd', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', 'spacing', 'India', 'California', '', '4.00', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 10:34:04'),
(11, 22, 'Sneha', 'pending', 0, '222534', '324', 233, 0, 'abcd', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', 'spacing', 'USA', 'California', '', '4.00', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 11:09:48'),
(12, 22, 'Ai Healthcare and fitness for more creativity', 'pending', 0, '222534', '324', 233, 0, 'Aarti', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', '', '4.00', 'Double-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 11:13:27'),
(13, 22, 'Ai Healthcare and fitness for more creativity', 'pending', 0, '222534', '324', 233, 0, 'Aarti', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', 'spacing', 'UK', 'California', '', '4.00', 'Double-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 11:25:24'),
(14, 22, 'Cyber security and information technology', 'pending', 0, '222534', '324', 233, 0, 'Aarti gupta', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', 'spacing', 'USA', 'California', '', '4.00', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 11:27:10'),
(15, 22, 'Cyber security and information technology', 'pending', 0, '222534', '324', 233, 0, 'Aarti gupta', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', 'spacing', 'India', 'California', '', '4.00', 'Single-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 12:28:15'),
(16, 22, 'Ai Healthcare and fitness for more creativity', 'pending', 0, '222534', '324', 233, 0, 'Aarti', 'gopalganj', 'http://localhost/journal-backend/api/auth/add_journal', 'http://localhost/journal-backend/api/auth/add_journal', '1', 'India', 'New York', '', '4.00', 'Double-blind', 'http://localhost/journal-backend/api/auth/add_journal', '0', '2025-01-14 12:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `published_papers`
--

CREATE TABLE `published_papers` (
  `ppuid` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `authors` varchar(255) DEFAULT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `paper_type` enum('journal','patent','book') DEFAULT NULL,
  `issn` varchar(50) DEFAULT NULL,
  `volume` varchar(50) DEFAULT NULL,
  `issue` varchar(50) DEFAULT NULL,
  `live_url` varchar(255) DEFAULT NULL,
  `indexing_with` varchar(255) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publish_requests`
--

CREATE TABLE `publish_requests` (
  `paper_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `publisher_id` int(11) DEFAULT NULL,
  `sender` enum('author','journal') DEFAULT NULL,
  `pr_status` enum('pending','accept','proceed_payment','published','reject') DEFAULT NULL,
  `payment_status` enum('none','pending','complete','failed') DEFAULT NULL,
  `live_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_papers`
--

CREATE TABLE `research_papers` (
  `paper_id` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `corresponding_email` varchar(255) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `author_contact` varchar(50) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `affiliation` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `co_authors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`co_authors`)),
  `paper_title` varchar(255) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviewers`
--

CREATE TABLE `reviewers` (
  `user_id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `contact` varchar(15) NOT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviewers`
--

INSERT INTO `reviewers` (`user_id`, `profile_image`, `contact`, `reviewer_name`, `approval_status`, `created_at`) VALUES
(26, NULL, '8888888888', 'shivam', 'pending', '2025-01-13 16:42:11'),
(27, NULL, '8888888888', 'reviewer2', 'pending', '2025-01-13 16:50:21'),
(28, NULL, '8888888888', 'reviewer2', 'pending', '2025-01-13 16:51:00'),
(30, NULL, '6206510871', 'Aarti Kumari', 'pending', '2025-01-14 09:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(70) NOT NULL,
  `password` varchar(100) NOT NULL,
  `type` enum('author','publisher','reviewer','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `type`, `created_at`) VALUES
(11, 'User', '111111', '', '2025-01-11 13:34:31'),
(13, 'User', '$2y$10$NoI2fDHh8P0YDRvOZxIFN.vPY7rvtJrGHwZ77sLEbMX', '', '2025-01-11 13:34:31'),
(14, 'neha', '$2y$10$xRvt2x7lFfIewmLgPoVnle/b7hx1FNGTShA9NJjDzxu', 'author', '2025-01-11 13:34:31'),
(15, 'neha', '$2y$10$4D4vyYolpR3SiAm.gizw8uYQOFy6LyoEKqVgxFSCd13yacP9fF7Xq', 'author', '2025-01-11 13:34:31'),
(16, 'neha', '$2y$10$D8pAaJIKmDs.L7PFx.D.wumv.vDeEYfkqnySUZojDhy8vI8EIT29S', 'author', '2025-01-11 13:34:31'),
(17, 'neha', '$2y$10$XCjvuwNBu5pDX7Zn661ncOFYWWWNSReOHDhtsZxKGxrt8jTKh8No.', 'author', '2025-01-11 13:34:31'),
(18, 'supriya', '$2y$10$uMeBxMyH66OlgG68kgV5POcHIQolObUOKUkQHVSpTdakoePwt.Xem', 'publisher', '2025-01-11 13:34:31'),
(19, 'supriya', '$2y$10$RLbWfwGuT9/uTgZb1ascLOOj988fSZfqyy1PetdWXUALLUQBnxNc2', 'reviewer', '2025-01-11 13:34:31'),
(20, 'Aarti Kumari', '$2y$10$hxLKK6rTMthYetjI4EfBkeWc5IBj2Ky/FI0eefcPT.MPDJsDP.sUC', 'author', '2025-01-11 13:34:31'),
(21, 'guptaAarti', '$2y$10$Wby/TFAJqgkzaIpERKVfR.L9tjujTZ30yHJaqqr1hi9Yuj..90F/2', 'publisher', '2025-01-11 13:34:31'),
(22, 'Aarti gupta', '$2y$10$nTxNZSe27XtZtjhv6DlGEOP7QQx8Ul4YcmzLFXH7Y192/YSVE9/Ym', 'reviewer', '2025-01-11 13:34:31'),
(23, 'shivam@gmail.com', '$2y$10$kjjbtFA9jnqi7kXruUvVuOWpzzKWeEH0chmDMuSb4G0zfa7lTjU6S', 'author', '2025-01-13 16:39:33'),
(24, 'shivam1@gmail.com', '$2y$10$CYycCHanG1hDbFzrJuCkguvrzkGlkUFKVwcXhd0ZQal6ucXeoA4iK', 'author', '2025-01-13 16:41:13'),
(25, 'publisher@gmail.com', '$2y$10$LFBZ5cQQPCOUBjc4jzE9rOh1alzO3QViV3bL4QOSCwjVUNAYNLVA.', 'publisher', '2025-01-13 16:41:52'),
(26, 'reviewer@gmail.com', '$2y$10$FVxS1FCSPNUFNfEtgfsFAO3jM3yW2TiW/6SuAgz7C2Cj4kclSqDJa', 'reviewer', '2025-01-13 16:42:11'),
(27, 'reviewer2@gmail.com', '$2y$10$ylPHphbBZvSdxfYEjRnX5ejNToyIA1heR7s.RMGw.JtryYt51RA8K', 'reviewer', '2025-01-13 16:50:21'),
(28, 'reviewer3@gmail.com', '$2y$10$yaUGkqX75GgYunpwlX64keyG9j172FjhFAaWliCw8ZVC0lsIm/LO2', 'reviewer', '2025-01-13 16:51:00'),
(29, 'aarti9@gmail.com', '$2y$10$2o7qEHhG4YfYL1cQwTejNOz22HwceneE23Ci0rlaSv9uGlnYkCOei', 'author', '2025-01-13 17:55:20'),
(30, 'kaarti.kcr@gmail.com', '$2y$10$J8fuCL.aZpsNr4hABdE7XunzlnNOJ/cXlErvmEBDX8gT5TGxsjRYG', 'reviewer', '2025-01-14 09:36:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `contact_table`
--
ALTER TABLE `contact_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`journal_id`),
  ADD KEY `user_id_fk` (`user_id`);

--
-- Indexes for table `published_papers`
--
ALTER TABLE `published_papers`
  ADD PRIMARY KEY (`ppuid`),
  ADD KEY `fk_published_papers_user_id` (`user_id`);

--
-- Indexes for table `publish_requests`
--
ALTER TABLE `publish_requests`
  ADD KEY `fk_publish_requests_paper_id` (`paper_id`),
  ADD KEY `fk_publish_requests_author_id` (`author_id`),
  ADD KEY `fk_publish_requests_publisher_id` (`publisher_id`),
  ADD KEY `fk_publish_requests_journal_id` (`journal_id`);

--
-- Indexes for table `research_papers`
--
ALTER TABLE `research_papers`
  ADD PRIMARY KEY (`paper_id`),
  ADD KEY `fk_research_papers_author_id` (`author_id`);

--
-- Indexes for table `reviewers`
--
ALTER TABLE `reviewers`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_table`
--
ALTER TABLE `contact_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `published_papers`
--
ALTER TABLE `published_papers`
  MODIFY `ppuid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `authors`
--
ALTER TABLE `authors`
  ADD CONSTRAINT `fk_authors_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `journals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `published_papers`
--
ALTER TABLE `published_papers`
  ADD CONSTRAINT `fk_published_papers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `published_papers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `publish_requests`
--
ALTER TABLE `publish_requests`
  ADD CONSTRAINT `fk_publish_requests_author_id` FOREIGN KEY (`author_id`) REFERENCES `authors` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publish_requests_journal_id` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publish_requests_paper_id` FOREIGN KEY (`paper_id`) REFERENCES `research_papers` (`paper_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publish_requests_publisher_id` FOREIGN KEY (`publisher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_papers`
--
ALTER TABLE `research_papers`
  ADD CONSTRAINT `fk_research_papers_author_id` FOREIGN KEY (`author_id`) REFERENCES `authors` (`user_id`);

--
-- Constraints for table `reviewers`
--
ALTER TABLE `reviewers`
  ADD CONSTRAINT `fk_reviewers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
