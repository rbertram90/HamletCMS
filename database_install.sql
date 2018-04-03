-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2018 at 12:35 AM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog_cms`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `wordcount` (`str` TEXT) RETURNS INT(11) NO SQL
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
    DECLARE wordCnt, idx, maxIdx INT DEFAULT 0;
    DECLARE currChar, prevChar BOOL DEFAULT 0;
    SET maxIdx=char_length(str);
    WHILE idx < maxIdx DO
        SET currChar=SUBSTRING(str, idx, 1) RLIKE '[[:alnum:]]';
        IF NOT prevChar AND currChar THEN
            SET wordCnt=wordCnt+1;
        END IF;
        SET prevChar=currChar;
        SET idx=idx+1;
    END WHILE;
    RETURN wordCnt;
  END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(10) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(8) NOT NULL,
  `anon_search` tinyint(1) NOT NULL DEFAULT '1',
  `visibility` enum('anon','private','members','friends') NOT NULL DEFAULT 'anon',
  `widgetJSON` text NOT NULL,
  `pagelist` varchar(500) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(8) NOT NULL,
  `message` text NOT NULL,
  `blog_id` bigint(10) NOT NULL,
  `post_id` int(8) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(8) NOT NULL,
  `approved` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contributorgroups`
--

CREATE TABLE `contributorgroups` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  `data` text NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contributors`
--

CREATE TABLE `contributors` (
  `user_id` int(10) NOT NULL,
  `blog_id` bigint(10) NOT NULL,
  `privileges` enum('postonly','all') NOT NULL DEFAULT 'postonly',
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `user_id` int(8) NOT NULL,
  `blog_id` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `postautosaves`
--

CREATE TABLE `postautosaves` (
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `allowcomments` int(11) NOT NULL,
  `date_last_saved` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(8) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `blog_id` bigint(10) NOT NULL,
  `link` varchar(150) NOT NULL,
  `draft` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL,
  `allowcomments` tinyint(1) NOT NULL DEFAULT '1',
  `tags` varchar(300) NOT NULL,
  `author_id` int(8) NOT NULL,
  `type` enum('video','standard','gallery') NOT NULL DEFAULT 'standard',
  `videoid` varchar(20) NOT NULL,
  `videosource` enum('youtube','vimeo') NOT NULL,
  `gallery_imagelist` text NOT NULL,
  `initialautosave` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `postviews`
--

CREATE TABLE `postviews` (
  `postid` int(8) NOT NULL,
  `userip` varchar(20) NOT NULL,
  `userviews` smallint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(8) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `location` varchar(50) NOT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'profile_default.jpg 	',
  `description` text NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `signup_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `flickrid` varchar(20) NOT NULL,
  `security_q` varchar(300) NOT NULL,
  `security_a` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contributorgroups`
--
ALTER TABLE `contributorgroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contributors`
--
ALTER TABLE `contributors`
  ADD PRIMARY KEY (`user_id`,`blog_id`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`user_id`,`blog_id`);

--
-- Indexes for table `postautosaves`
--
ALTER TABLE `postautosaves`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`);

--
-- Indexes for table `postviews`
--
ALTER TABLE `postviews`
  ADD PRIMARY KEY (`postid`,`userip`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `contributorgroups`
--
ALTER TABLE `contributorgroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
