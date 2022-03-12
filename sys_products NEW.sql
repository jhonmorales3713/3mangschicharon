-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2022 at 06:24 PM
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
-- Database: `3mangschicharon_3`
--

-- --------------------------------------------------------

--
-- Table structure for table `sys_products`
--

CREATE TABLE `sys_products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `no_of_stocks` double(11,1) DEFAULT 0.0,
  `max_qty_isset` int(11) NOT NULL DEFAULT 0 COMMENT '0 = unchecked, 1 = checked',
  `max_qty` double(11,1) NOT NULL DEFAULT 1.0 COMMENT 'max quantity per checkout',
  `summary` longtext DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `img_2` varchar(255) DEFAULT NULL,
  `img_3` varchar(255) DEFAULT NULL,
  `img_4` varchar(255) DEFAULT NULL,
  `img_5` varchar(255) DEFAULT NULL,
  `img_6` varchar(255) DEFAULT NULL,
  `enabled` int(1) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `variant_isset` int(11) NOT NULL DEFAULT 0,
  `parent_product_id` varchar(255) DEFAULT NULL,
  `search_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_products`
--

INSERT INTO `sys_products` (`id`, `category_id`, `name`, `price`, `tags`, `no_of_stocks`, `max_qty_isset`, `max_qty`, `summary`, `img`, `img_2`, `img_3`, `img_4`, `img_5`, `img_6`, `enabled`, `date_created`, `date_updated`, `variant_isset`, `parent_product_id`, `search_count`) VALUES
(9898, 1, 'CHICHA', '200.00', '', 40.0, 1, 1.0, 'CHICHA', 'VEYrbDNtZDFhV0FGck5TczQ0aEFKZz09==.png', 'none', 'none', 'none', 'none', 'none', 1, '2022-03-11 19:35:06', '2022-03-12 18:07:49', 1, NULL, 0),
(9899, 0, 'SMALL', '10.00', '', 20.0, 1, 1.0, '', 'WTJFNVpWMDI2aWJTOENZM0V5WW9LcHhBQjZFY0pWNVJMdnVqdjhnclRLYz0==.jpg', 'none', 'none', 'none', 'none', 'none', 1, '2022-03-11 19:35:29', '2022-03-12 18:07:49', 0, '9898', 0),
(9900, 0, 'MEDIUM', '10.00', '', 20.0, 1, 1.0, '', 'VjM4TGlMZWYwb1pJd3BrYzhEYWZRZz09==.png', 'none', 'none', 'none', 'none', 'none', 0, '2022-03-11 19:36:28', '2022-03-12 18:07:37', 0, '9898', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sys_products`
--
ALTER TABLE `sys_products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sys_products`
--
ALTER TABLE `sys_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9901;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
