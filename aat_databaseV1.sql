-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2021 at 08:28 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aat_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `article_id` int(6) NOT NULL,
  `author_id` int(4) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` mediumtext NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `public` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`article_id`, `author_id`, `title`, `content`, `creation_date`, `update_date`, `public`) VALUES
(1, 1, 'Test Article 1', 'Test Article 1 Content Here', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(2, 2, 'Test Article 2', 'THIS IS THE UPDATE TEST for article with id 2', '0000-00-00 00:00:00', '2021-05-12 06:23:19', 0),
(4, 2, 'Test Article 3', 'Test Article 3 Content Here', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(5, 1, 'Testin Creation Date T', 'Testing trigger for creation date', '2021-05-12 00:00:00', '0000-00-00 00:00:00', 0),
(6, 1, 'Testin Creation Date T', 'Testing trigger for creation date', '2021-05-12 15:43:56', '0000-00-00 00:00:00', 0),
(7, 1, 'THIS IS THE UPDATE TEST', 'Content for update time functionality', '2021-05-12 15:50:26', '2021-05-12 06:22:32', 0);

--
-- Triggers `articles`
--
DELIMITER $$
CREATE TRIGGER `creationDateT` BEFORE INSERT ON `articles` FOR EACH ROW SET NEW.creation_date = IFNULL(NEW.creation_date, CURRENT_TIMESTAMP)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(4) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `pwd` varchar(128) NOT NULL,
  `perm` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `lname`, `email`, `pwd`, `perm`) VALUES
(1, 'Jeff', 'Bob', 'jeffbob@gmail.com', 'testing', 0),
(2, 'Test', 'Two', 'testtwo@gmail.com', 'testing2', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
