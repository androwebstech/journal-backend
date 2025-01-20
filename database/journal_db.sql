-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2025 at 11:36 AM
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
-- Database: `journal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(30) NOT NULL,
  `image` varchar(50) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `image`, `role`, `email`, `password`, `created_at`) VALUES
(1, '4275377ab4db3825178f7387725107b9.png', 'admin', 'admin@gmail.com', '$2y$10$q4FFAy1z877EzZhFmFet.uUdKeIFZdq.DoPI8gb2beSpC7iMXPJzO', '2025-01-20 10:23:10'),
(2, 'bdeb9047cdbaa6635656ceeaf5427160.png', 'admin', 'admin@gmail.com', '$2y$10$s9ENZjZ0zSceH7xDqfsPfe/GfZj0egAOeALXM30exsWoL5PB13kS6', '2025-01-20 10:23:28'),
(3, '50384946711ea9c17682fad03bbaeb4a.png', 'admin', 'admin@gmail.com', '$2y$10$5CSCXMGuOQVFVq6L0ZjuI.oWPLj/GfbeNbCJIVWoYr4o1NynVjvcC', '2025-01-20 10:23:37'),
(4, 'a0ed743e80b45dac78f124ff6974e248.png', 'admin', 'admin@gmail.com', '$2y$10$AGUEGYPF7dHOJ5Netqx9SuVTQxNwkO4lwp0oFO9CiDA17gWOjaT2a', '2025-01-20 10:23:41'),
(5, 'c3390367f429517f8b37edede0923a27.png', 'admin', 'admin43@gmail.com', '$2y$10$0V4Tcj8cH6na7K.1rYFcpuR12OIV1tqLEMHu/vFwuS.b4zW0UAvxa', '2025-01-20 10:24:41'),
(6, 'b7c1050167a5730d87f3bdbd9dc1cc5a.png', 'admin', 'admin43@gmail.com1', '$2y$10$cotvRCj7.09vwI5aPh9FOuf.Wr9wY7a01jjSupIxKao9u9z5lnP0u', '2025-01-20 10:25:14'),
(7, '25b92ba31d98a269cf45ca4f77100e52.png', 'admin', 'admin43@gmail.co', '$2y$10$S2D3r4KAlU2M0KWgE0RyUe/yxwnJ8IJx3h5Y9Ndph6N2y80KW0ijO', '2025-01-20 10:28:30'),
(8, '26f593c09142e33803d9e56c596f833f.png', 'admin', 'admin43@gmail.comm', '$2y$10$VFwkeGUAmZvPr1kOY8dwGOV3.sce8Ypjm3OG3qJ1Yir/N/ymeYLFO', '2025-01-20 10:28:59'),
(9, '2c542eca4c8f6557b8fe1fe7cfa8dd3e.png', 'admin', 'tanav@gmail.com', '$2y$10$9GhQN8cw.nZgwEFm5Ijyc.lCs4xJqneE/bsO.KeR8nkkuFn1hdzl.', '2025-01-20 10:31:22'),
(10, 'b3abf899445e5dcbcbf10cd4495e3f84.png', 'admin', 'tanavmahendru@gmail.com', '$2y$10$hate4a1o7j2LMNrGfRXaoeAr.TVml3tadN/isYe40wH.Y6eu7eImG', '2025-01-20 10:32:07'),
(11, 'd84ae84868f840167ca4a0b59aaad70b.png', 'admin', 'tanavmahendru2003@gmail.com', '$2y$10$yvBb/5OkjI4xOswaLQ4Xd.MVejSk17JGC8mcKhKGq3FS9F3GkWSeW', '2025-01-20 10:32:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
