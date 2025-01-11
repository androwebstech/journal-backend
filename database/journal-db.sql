-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2025 at 05:11 PM
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
(22, 'Aarti gupta', '$2y$10$nTxNZSe27XtZtjhv6DlGEOP7QQx8Ul4YcmzLFXH7Y192/YSVE9/Ym', 'reviewer', '2025-01-11 13:34:31');

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
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `published_papers`
--
ALTER TABLE `published_papers`
  MODIFY `ppuid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
