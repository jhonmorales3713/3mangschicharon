-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2020 at 03:44 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloudpanda-cpshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `sys_users`
--

CREATE TABLE `sys_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL COMMENT 'email address',
  `password` varchar(100) NOT NULL,
  `avatar` varchar(150) NOT NULL,
  `functions` text,
  `access_nav` text NOT NULL COMMENT 'cp_main_navigation',
  `access_content_nav` text NOT NULL COMMENT 'cp_content_navigation',
  `last_seen` datetime DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `failed_login_attempts` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_users`
--

INSERT INTO `sys_users` (`id`, `username`, `password`, `avatar`, `functions`, `access_nav`, `access_content_nav`, `last_seen`, `active`, `failed_login_attempts`) VALUES
(1, 'superuser', '$2y$12$MJNxB0RhkGUlCkJpu3Zmw.BAPE9bvNaGJqpBeeJAxwwcMsAvdquKO', '15879843880595Paul_Photo.jpg', '{\"overall_access\":1,\"online_ordering\":1,\"dashboard\":{\"view\":1},\"transactions\":{\"view\":1},\"products\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"shops\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"customer\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"accounts\":1,\"billing\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"codes\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"reports\":1,\"ps\":{\"view\":1},\"por\":{\"view\":1},\"sr\":{\"view\":1},\"ssr\":{\"view\":1},\"settings\":1,\"change_password\":{\"update\":1},\"users\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"members\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"delivery_areas\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"payment_type\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"category\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"shipping_partners\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1}}', '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12', '20, 21, 22, 23, 24, 25, 26, 27, 29, 30, 31, 36, 37, 39, 128, 129, 139, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 40, 41, 46, 47, 48, 50, 51, 52, 53, 145, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 146, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 147, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 17, 18, 133, 134, 136, 137, 138, 143, 144, 163, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 161, 162, 164, 165, 16, 131, 132, 135, 141, 142, 190, 191', NULL, 1, 0),
(60, 'test@cloudpanda.ph', '$2y$12$WV1fy77aLHwKiBlWbY.kzOR0YEs1c4QbgetLkC5U4Ldf5l0qzQuYC', '1586845675447shopanda-favicon.png', '{\"overall_access\":0,\"online_ordering\":1,\"dashboard\":{\"view\":0},\"transactions\":{\"view\":1},\"products\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"shops\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":0},\"customer\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"accounts\":0,\"billing\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"codes\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"reports\":1,\"ps\":{\"view\":1},\"por\":{\"view\":1},\"sr\":{\"view\":1},\"ssr\":{\"view\":1},\"settings\":1,\"change_password\":{\"update\":1},\"users\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"members\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"delivery_areas\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"payment_type\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"category\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"shipping_partners\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0}}', '', '', NULL, 1, 0),
(61, 'test@jcpremiere.com', '$2y$12$2khFMwSHQagIkIZiSMNvqu6xvJd4DdRM/eYYwklvyEaLaUghPXR2m', '1588132887177gl_classic.png', '{\"overall_access\":0,\"online_ordering\":1,\"dashboard\":{\"view\":1},\"transactions\":{\"view\":1},\"products\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"shops\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"customer\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"accounts\":0,\"billing\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"codes\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"reports\":1,\"ps\":{\"view\":1},\"por\":{\"view\":1},\"sr\":{\"view\":1},\"ssr\":{\"view\":1},\"settings\":1,\"change_password\":{\"update\":1},\"users\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"members\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"delivery_areas\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"payment_type\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"category\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0},\"shipping_partners\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0}}', '', '', NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sys_users`
--
ALTER TABLE `sys_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sys_users`
--
ALTER TABLE `sys_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
