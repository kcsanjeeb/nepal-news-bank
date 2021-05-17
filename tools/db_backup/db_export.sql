-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2021 at 06:46 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nepalnewsbank`
--

-- --------------------------------------------------------

--
-- Table structure for table `archive_photos`
--

CREATE TABLE `archive_photos` (
  `id` int(11) NOT NULL,
  `archive_id` varchar(30) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `title` text DEFAULT NULL,
  `series` int(11) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `published_date` datetime DEFAULT NULL,
  `wp_id` text DEFAULT NULL,
  `wp_media_id` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `archive_video`
--

CREATE TABLE `archive_video` (
  `id` int(11) NOT NULL,
  `archive_id` varchar(30) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `title` text DEFAULT NULL,
  `series` int(11) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `video` text DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `published_date` datetime DEFAULT NULL,
  `wp_id` text DEFAULT NULL,
  `wp_media_id` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `interview`
--

CREATE TABLE `interview` (
  `id` int(11) NOT NULL,
  `interview_id` varchar(30) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `title` text DEFAULT NULL,
  `series` int(11) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `video` text DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `body` text DEFAULT NULL,
  `published_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `interview_text`
--

CREATE TABLE `interview_text` (
  `id` int(11) NOT NULL,
  `iframe_title` text DEFAULT NULL,
  `iframe_iframe` text DEFAULT NULL,
  `iframe_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `nas`
--

CREATE TABLE `nas` (
  `id` int(11) NOT NULL,
  `newsid` varchar(20) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `local_published_date` date DEFAULT NULL,
  `byline` varchar(300) DEFAULT NULL,
  `category_list` text DEFAULT NULL,
  `videolong` text DEFAULT NULL,
  `videolazy` text DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `audio` text DEFAULT NULL,
  `photos` text DEFAULT NULL,
  `newsbody` text DEFAULT NULL,
  `videoextra` text DEFAULT NULL,
  `tag_list` text DEFAULT NULL,
  `uploaded_by` varchar(300) DEFAULT NULL,
  `reporter` varchar(300) DEFAULT NULL,
  `camera_man` varchar(300) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `video_type` varchar(30) DEFAULT NULL,
  `series` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `nas`
--

INSERT INTO `nas` (`id`, `newsid`, `created_date`, `local_published_date`, `byline`, `category_list`, `videolong`, `videolazy`, `thumbnail`, `audio`, `photos`, `newsbody`, `videoextra`, `tag_list`, `uploaded_by`, `reporter`, `camera_man`, `district`, `video_type`, `series`) VALUES
(1, '58183', '2021-05-17 10:05:12', '2021-05-17', 'dasd das dasddasd', '', NULL, NULL, NULL, NULL, '', NULL, NULL, '', 'hari bahadur', 'rabi lamicchane', 'shiva pangeni', 'kathmandu', 'selfhost', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `web`
--

CREATE TABLE `web` (
  `id` int(11) NOT NULL,
  `newsid` varchar(20) DEFAULT NULL,
  `videolong` text DEFAULT NULL,
  `videolazy` text DEFAULT NULL,
  `previewgif` text DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `audio` text DEFAULT NULL,
  `photos` text DEFAULT NULL,
  `videoextra` varchar(200) DEFAULT NULL,
  `newsbody` text DEFAULT NULL,
  `pushed_by` varchar(200) DEFAULT NULL,
  `pushed_date` datetime DEFAULT NULL,
  `wp_post_id` bigint(20) DEFAULT NULL,
  `vimeo_videolong` text DEFAULT NULL,
  `vimeo_videolazy` text DEFAULT NULL,
  `vimeo_video_extra` text DEFAULT NULL,
  `wp_media_id` text DEFAULT NULL,
  `wp_post_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archive_photos`
--
ALTER TABLE `archive_photos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `archive_id` (`archive_id`);

--
-- Indexes for table `archive_video`
--
ALTER TABLE `archive_video`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `archive_id` (`archive_id`);

--
-- Indexes for table `interview`
--
ALTER TABLE `interview`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `interview_id` (`interview_id`);

--
-- Indexes for table `interview_text`
--
ALTER TABLE `interview_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nas`
--
ALTER TABLE `nas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `newsid` (`newsid`);

--
-- Indexes for table `web`
--
ALTER TABLE `web`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `newsid` (`newsid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archive_photos`
--
ALTER TABLE `archive_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archive_video`
--
ALTER TABLE `archive_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview`
--
ALTER TABLE `interview`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_text`
--
ALTER TABLE `interview_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nas`
--
ALTER TABLE `nas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `web`
--
ALTER TABLE `web`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `web`
--
ALTER TABLE `web`
  ADD CONSTRAINT `web_ibfk_1` FOREIGN KEY (`newsid`) REFERENCES `nas` (`newsid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
