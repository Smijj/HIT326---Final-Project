-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2021 at 06:24 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6


-- --------------------------------------------------------

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `lname`, `email`, `pwd`, `perm`) VALUES
(8, 'Example', 'Admin', 'example.admin@gmail.com', '$2y$10$aqkFgXCXI2xmChNoieJ34OHihOsoYGy16fw4IusCp.kR4vQmQYZvu', 3),
(9, 'Example', 'Editor', 'example.editor@gmail.com', '$2y$10$kaIRN0Fh2QLRItVTmjH4ZukhQ1zZ7ES1.Df86np96r.QE9rTLAP8q', 2),
(10, 'Example', 'Journalist 1', 'example.journalist1@gmail.com', '$2y$10$tRUPFFPnk3DKM34hpUV9XuZCuBAF5DAceoKHhHgm/pWqv222aveUe', 1),
(11, 'Example', 'Journalist 2', 'example.journalist2@gmail.com', '$2y$10$hL5pvWBBoztASYDivqyXmOxQVORyqnMXfKxXYPEy3tLKRyydmMPeC', 1);


-- --------------------------------------------------------

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`article_id`, `author_id`, `title`, `keywords`, `content`, `creation_date`, `update_date`, `public`) VALUES
(18, 10, 'Example Public Article 1', 'Example, Public, Article', 'This is an example of a public article', '2021-05-26 13:49:10', '2021-05-26 04:22:00', 1),
(19, 10, 'Example Public Article 2', 'Example, Public, Article', 'This is an example of a public article', '2021-05-26 13:49:20', '2021-05-26 04:21:56', 1),
(20, 10, 'Example Public Article 3', 'Example, Public, Article', 'This is an example of a public article', '2021-05-26 13:49:35', '2021-05-26 04:21:53', 1),
(21, 10, 'Example Hidden Article 1', 'Example, Hidden, Article', 'This is an example of a hidden article, that has not been made public by the editor or admin yet.', '2021-05-26 13:50:07', '2021-05-26 04:20:07', 0),
(22, 11, 'Example Public Article 4', 'Example, Public, Article', 'This is an example of a public article', '2021-05-26 13:50:31', '2021-05-26 04:21:47', 1),
(24, 11, 'Example Public Article 6', 'Example, Public, Article', 'This is an example of a public article.', '2021-05-26 13:50:55', '2021-05-26 04:21:38', 1),
(25, 11, 'Example Hidden Article 2', 'Example, Hidden, Article', 'This is an example of a hidden article the editor or admin has not approved and made public yet.', '2021-05-26 13:51:17', '2021-05-26 04:21:17', 0),
(26, 9, 'Example Public Article 5', 'Example, Public, Article', 'This is an example of a public article', '2021-05-26 13:52:47', '2021-05-26 04:22:52', 1);

