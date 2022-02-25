-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2020 at 03:45 AM
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
-- Table structure for table `cp_main_navigation`
--

CREATE TABLE `cp_main_navigation` (
  `main_nav_id` int(11) NOT NULL,
  `main_nav_desc` varchar(255) NOT NULL,
  `main_nav_icon` varchar(255) NOT NULL,
  `main_nav_href` varchar(255) NOT NULL COMMENT 'name of function inside of the Main controller',
  `attr_val` varchar(255) NOT NULL COMMENT 'class,id,name attr of checkbox',
  `attr_val_edit` varchar(255) NOT NULL COMMENT 'class,id,name attr of checkbox (edit)',
  `arrangement` int(11) NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1' COMMENT 'if = 2 it means strictly for superuser only'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_main_navigation`
--

INSERT INTO `cp_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES
(1, 'Dashboard', 'fa-home', 'home', 'acb_home', 'cb_home', 1, '2020-06-16 00:00:00', '2020-06-16 00:00:00', 1),
(2, 'Orders', 'fa-shopping-cart', 'sales_home', 'acb_sales', 'cb_sales', 2, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(3, 'Products', 'fa-tags', 'products_home', 'acb_products', 'cb_products', 3, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(4, 'Shops', 'fa-bank', 'inventory_home', 'acb_inventory', 'cb_inventory', 4, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(5, 'Customers', 'fa-users', 'entity_home', 'acb_entity', 'cb_entity', 5, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(6, 'Accounts', 'fa-credit-card-alt', 'manufacturing_home', 'acb_manufacturing', 'cb_manufacturing', 6, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(7, 'Reports', 'fa-file-text', 'report_home', 'acb_reports', 'cb_reports', 7, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(8, 'Settings', 'fa-cog', 'settings_home', 'acb_settings', 'cb_settings', 8, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(9, 'Developer Settings', 'fa-android', 'dev_settings_home', 'acb_ds', 'cb_ds', 11, '2018-07-26 00:00:00', '2018-07-26 00:00:00', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cp_main_navigation`
--
ALTER TABLE `cp_main_navigation`
  ADD PRIMARY KEY (`main_nav_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cp_main_navigation`
--
ALTER TABLE `cp_main_navigation`
  MODIFY `main_nav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
