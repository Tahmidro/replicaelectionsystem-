-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 02:18 PM
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
-- Database: `election_system`
--
CREATE DATABASE IF NOT EXISTS `election_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `election_system`;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `candidate_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `election_id` int(11) DEFAULT NULL,
  `party` varchar(100) DEFAULT NULL,
  `manifesto` text DEFAULT NULL,
  PRIMARY KEY (`candidate_id`),
  KEY `user_id` (`user_id`),
  KEY `fk_election` (`election_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `user_id`, `status`, `election_id`, `party`, `manifesto`) VALUES
(19, 55, 'rejected', NULL, 'bimpi', 'druto nirbachon'),
(20, 54, 'approved', 2, 'amlig', 'druto fire asbo'),
(22, 62, 'approved', NULL, 'bjp', 'bbbbb');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

DROP TABLE IF EXISTS `elections`;
CREATE TABLE IF NOT EXISTS `elections` (
  `election_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
  PRIMARY KEY (`election_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`election_id`, `title`, `description`, `start_time`, `end_time`, `status`) VALUES
(2, 'chairman election', 'bllllllahh blahhh', '2025-10-05 00:00:00', '2025-10-06 00:00:00', 'ongoing'),
(3, 'blah', 'blaha ', '2025-10-22 00:00:00', '2025-10-31 00:00:00', 'upcoming'),
(4, 'member election', 'i dont know', '2025-10-11 00:00:00', '2025-10-12 00:00:00', 'upcoming'),
(5, 'i dont know', 'i dont know', '2025-10-09 00:00:00', '2025-10-11 00:00:00', 'upcoming'),
(6, 'member election', 'i dont know', '2025-10-11 00:00:00', '2025-10-12 00:00:00', 'upcoming');

-- --------------------------------------------------------

--
-- Table structure for table `election_candidates`
--

DROP TABLE IF EXISTS `election_candidates`;
CREATE TABLE IF NOT EXISTS `election_candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `election_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `election_id` (`election_id`),
  KEY `candidate_id` (`candidate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `face_auth_logs`
--

DROP TABLE IF EXISTS `face_auth_logs`;
CREATE TABLE IF NOT EXISTS `face_auth_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `voter_id` int(11) DEFAULT NULL,
  `nid` varchar(20) DEFAULT NULL,
  `status` enum('success','failure') NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `voter_id` (`voter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nid` char(13) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nid` (`nid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `nid`, `created_at`, `is_admin`, `otp_code`, `otp_expires`, `email_verified`) VALUES
(54, 'opu', 'opu@gmail.com', '$2y$10$n5ZR/9oe8cxPubJpgeCkqe6bdYJZuOhDGJIhTKdpn/4x2ukd7WloW', '2222222222222', '2025-10-06 09:58:05', 0, NULL, NULL, 0),
(55, 'aniya', 'aniya@gmail.com', '$2y$10$0SjyUOf9xbx60bU/UK93.eddHt9OYiFg0GPuXFpz5qeSPWRruoSiu', '3333333333333', '2025-10-06 09:58:33', 0, NULL, NULL, 0),
(56, 'tahmid', 'tahmid@gmail.com', '$2y$10$XBeez23ZN5j55nXKHOvW..0oUpah5ycQHbfczt8m3HjkNhgjhaK3y', '7777777777777', '2025-10-06 10:49:31', 0, NULL, NULL, 0),
(57, 'tah', 'tah@gmail.com', '$2y$10$PTHNfP0k6FWyohVjcdAPY.ckb5sY8b85fRDlc9cbXbtq7fforZIOG', '0000000000000', '2025-10-07 11:29:53', 0, NULL, NULL, 0),
(59, 'tahmidosmani', 'tahmidosmani.uiu@gmail.com', '$2y$10$4Pwyh3/ziYgAUZcY5V4HY.3bbBDqeP6TZPwqabMpZk9WlBhF6bUN.', '6666666666666', '2025-10-07 13:00:07', 1, NULL, NULL, 1),
(61, 'tanha', 'tanha@gmail.com', '$2y$10$a8e2RlLBV7dMyh2SGbpvUuTpmQMQIczXrt3vo3pdVLltcIYrIQ0w.', '1111111111111', '2025-10-07 13:33:10', 0, NULL, NULL, 0),
(62, 'alif', 'alifcseuiu@gmail.com', '$2y$10$yulAPIbRUJlyeuVon4LZ5O1dkWpB200hhexVTJqr/ZLQTrWKlcPCO', '4444444444444', '2025-10-07 13:58:27', 0, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

DROP TABLE IF EXISTS `voters`;
CREATE TABLE IF NOT EXISTS `voters` (
  `voter_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nid_photo_path` varchar(255) DEFAULT NULL,
  `self_photo_path` varchar(255) DEFAULT NULL,
  `face_data_path` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`voter_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`voter_id`, `user_id`, `nid_photo_path`, `self_photo_path`, `face_data_path`, `is_verified`) VALUES
(37, 54, 'uploads/voters/nid_54_Screenshot 2025-09-02 172510.png', 'uploads/voters/self_54_Screenshot 2025-09-02 172517.png', NULL, 1),
(38, 55, 'uploads/voters/nid_55_Screenshot 2025-09-02 172556.png', 'uploads/voters/self_55_Screenshot 2025-09-03 125322.png', NULL, 1),
(44, 62, 'uploads/voters/nid_62_Screenshot 2025-09-02 172510.png', 'uploads/voters/self_62_Screenshot 2025-09-02 172517.png', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
CREATE TABLE IF NOT EXISTS `votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `voter_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `voter_id` (`voter_id`,`election_id`),
  KEY `candidate_id` (`candidate_id`),
  KEY `election_id` (`election_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`vote_id`, `voter_id`, `candidate_id`, `election_id`, `voted_at`) VALUES
(1, 44, 20, 2, '2025-10-09 11:11:51');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`) ON DELETE SET NULL;

--
-- Constraints for table `election_candidates`
--
ALTER TABLE `election_candidates`
  ADD CONSTRAINT `election_candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `election_candidates_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE;

--
-- Constraints for table `face_auth_logs`
--
ALTER TABLE `face_auth_logs`
  ADD CONSTRAINT `face_auth_logs_ibfk_1` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`voter_id`) ON DELETE SET NULL;

--
-- Constraints for table `voters`
--
ALTER TABLE `voters`
  ADD CONSTRAINT `voters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`voter_id`),
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`),
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
