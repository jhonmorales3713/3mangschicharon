-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2022 at 03:24 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `core_projects`
--

-- --------------------------------------------------------

--
-- Table structure for table `cs_clients_info`
--

CREATE TABLE `cs_clients_info` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `id_key` varchar(20) DEFAULT NULL,
  `primary_color` varchar(8) DEFAULT NULL,
  `button_radius_size` varchar(20) DEFAULT NULL,
  `button_text_color` varchar(20) DEFAULT NULL,
  `button_primary_color` varchar(20) DEFAULT NULL,
  `header_bg_color` varchar(20) DEFAULT NULL,
  `middle_bg_color` varchar(20) DEFAULT NULL,
  `footer_bg_color` varchar(20) DEFAULT NULL,
  `facebook_link` varchar(100) DEFAULT NULL,
  `instagram_link` varchar(100) DEFAULT NULL,
  `youtube_link` varchar(100) DEFAULT NULL,
  `twitter_link` varchar(100) DEFAULT NULL,
  `c_favicon` varchar(20) DEFAULT NULL,
  `c_main_logo` varchar(20) DEFAULT NULL,
  `c_secondary_logo` varchar(20) DEFAULT NULL,
  `c_faqs` varchar(20) DEFAULT NULL,
  `c_contact_us` varchar(20) DEFAULT NULL,
  `c_terms_and_condition` varchar(20) DEFAULT NULL,
  `c_404` varchar(20) DEFAULT NULL,
  `c_email` varchar(255) DEFAULT NULL,
  `c_password` varchar(255) DEFAULT NULL,
  `c_host` varchar(255) DEFAULT NULL,
  `font_choice` varchar(20) DEFAULT NULL,
  `status` int(2) DEFAULT NULL COMMENT 'active=1,inactive=0,deleted=2',
  `tagline` varchar(255) DEFAULT NULL,
  `c_phone` varchar(20) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cs_clients_info`
--

INSERT INTO `cs_clients_info` (`id`, `name`, `id_key`, `primary_color`, `button_radius_size`, `button_text_color`, `button_primary_color`, `header_bg_color`, `middle_bg_color`, `footer_bg_color`, `facebook_link`, `instagram_link`, `youtube_link`, `twitter_link`, `c_favicon`, `c_main_logo`, `c_secondary_logo`, `c_faqs`, `c_contact_us`, `c_terms_and_condition`, `c_404`, `c_email`, `c_password`, `c_host`, `font_choice`, `status`, `tagline`, `c_phone`, `date_created`) VALUES
(0, 'Test Site', 'test_site', '#ffffff', '25', '#abcdef', '#c2edf', '#ffffff', '#ffffff', '#ffffff', 'https://www.facebook.com/', 'https://www.instagram.com/', 'https://www.youtube.com/', 'https://www.twitter.com/', 'favicon.png', 'logo.png', 'secondary.png', 'faqs', 'contact_us', 'terms_and_condition', '404', 'testemail@test.com', 'test', 'non', 'Lato', 1, 'Wa ay ni', NULL, '2022-01-16 14:02:02'),
(1, '3 Mangs Chicharon', '3mangs', '#DD7E48', '50', '#fff', '#DD7E48', '#ffffff', '#ffffff', '#fffff', 'https://www.facebook.com/', 'https://www.instagram.com/', 'https://www.youtube.com/', 'https://www.twitter.com/', 'favicon.png', 'logo.png', 'secondary.png', 'faqs', 'constac_us', 'terms_and_condition', '404', 'testemail@test.com', 'test', 'non', 'Lato', 1, 'Tagline Here', NULL, '2022-02-26 15:16:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_clients_info`
--
ALTER TABLE `cs_clients_info`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
