-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 18, 2020 at 10:37 AM
-- Server version: 10.1.39-MariaDB
-- PHP Version: 7.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloudpanda-shopanda`
--

-- --------------------------------------------------------

--
-- Table structure for table `8_referralcomlog`
--

CREATE TABLE `8_referralcomlog` (
  `id` int(11) NOT NULL,
  `idno` varchar(255) NOT NULL,
  `order_reference_num` varchar(255) NOT NULL,
  `soldto` varchar(255) NOT NULL DEFAULT '---',
  `sys_shop` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `totalamount` float NOT NULL DEFAULT '0',
  `compercentage` float NOT NULL DEFAULT '0',
  `netamount` float NOT NULL DEFAULT '0',
  `payment_status` int(11) NOT NULL DEFAULT '1',
  `trandate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_ordered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_processed` int(11) NOT NULL DEFAULT '1' COMMENT '0-not tranferred from jcw, 1 = tranferred to jcw to jcp, 2 = com done processing',
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `8_referralcomsummary`
--

CREATE TABLE `8_referralcomsummary` (
  `id` int(11) NOT NULL,
  `commno` int(11) NOT NULL,
  `commcode` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  `fromdate` date NOT NULL DEFAULT '0000-00-00',
  `todate` date NOT NULL DEFAULT '0000-00-00',
  `processdate` date NOT NULL DEFAULT '0000-00-00',
  `netamount` float NOT NULL DEFAULT '0',
  `remarks` varchar(255) NOT NULL DEFAULT 'Referral Commission',
  `status` int(11) NOT NULL DEFAULT '1',
  `payoutdate` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `8_referralpayout`
--

CREATE TABLE `8_referralpayout` (
  `id` int(11) NOT NULL,
  `commno` int(11) NOT NULL,
  `commcode` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  `trandate` date NOT NULL DEFAULT '0000-00-00',
  `netamount` float NOT NULL DEFAULT '0',
  `paytype` varchar(255) NOT NULL COMMENT 'CASH,CHECK,BANK TRANSFER',
  `reference_num` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL DEFAULT 'Profit Share',
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_customers`
--

CREATE TABLE `app_customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `conno` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address1` text NOT NULL,
  `address2` text,
  `areaid` int(11) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` char(1) NOT NULL COMMENT 'M = Male, F = Female',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_members`
--

CREATE TABLE `app_members` (
  `id` int(11) NOT NULL,
  `member_type` int(11) NOT NULL DEFAULT '1',
  `sys_user` int(11) NOT NULL,
  `sys_shop` int(11) NOT NULL,
  `fname` varchar(45) NOT NULL,
  `mname` varchar(45) NOT NULL,
  `lname` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `comm_type` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `app_members`
--

INSERT INTO `app_members` (`id`, `member_type`, `sys_user`, `sys_shop`, `fname`, `mname`, `lname`, `email`, `mobile_number`, `comm_type`, `created`, `updated`, `status`) VALUES
(9, 1, 17, 0, 'Paul', '', 'Chua', 'paulchua@cloudpanda.ph', '09985962156', 0, '2019-10-02 11:33:34', '2020-04-11 15:04:27', 1),
(17, 2, 60, 1, 'Cloud', '', 'Panda', 'panda@cloudpanda.ph', '09985962156', 0, '2020-04-17 15:45:33', '2020-04-24 09:44:21', 1),
(18, 2, 61, 3, 'JC', '', 'Premiere', 'test@jcpremiere.com', '09152551485', 0, '2020-04-29 12:02:08', '2020-04-29 12:02:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_member_type`
--

CREATE TABLE `app_member_type` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app_member_type`
--

INSERT INTO `app_member_type` (`id`, `type`, `description`, `created`, `updated`, `status`) VALUES
(1, 'ADMIN', 'System Administrator', '2020-05-15 17:53:10', '2020-05-15 17:53:10', 1),
(2, 'SELLER', 'Shop Owner', '2020-05-15 17:53:10', '2020-05-15 17:53:10', 1),
(3, 'CELEB', 'Endorser/Influencer', '2020-05-15 17:53:38', '2020-05-15 17:53:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_notifications`
--

CREATE TABLE `app_notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(250) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `reciever` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `is_read` tinyint(2) NOT NULL DEFAULT '0',
  `link` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `app_order_details`
--

CREATE TABLE `app_order_details` (
  `order_id` varchar(255) NOT NULL COMMENT 'generated using uuid',
  `order_so_no` varchar(255) NOT NULL,
  `reference_num` varchar(255) NOT NULL,
  `paypanda_ref` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL COMMENT 'Id from jcwfp_users or idno for sk online',
  `name` varchar(255) NOT NULL,
  `conno` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `notes` text,
  `areaid` int(11) NOT NULL,
  `admin_sono` varchar(255) NOT NULL,
  `admin_drno` varchar(255) DEFAULT NULL,
  `total_amount` decimal(11,2) NOT NULL,
  `order_status` char(1) NOT NULL COMMENT 'p = processing, s = shipped, d = delivery, r = received, f = fulfilled',
  `payment_status` int(1) NOT NULL COMMENT '0 = pending, 1 = paid, 2 = unpaid',
  `payment_method` varchar(255) NOT NULL,
  `delivery_signature` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `delivery_info` varchar(255) DEFAULT NULL,
  `delivery_ref_num` varchar(255) DEFAULT NULL,
  `delivery_amount` decimal(11,2) DEFAULT NULL,
  `delivery_notes` text NOT NULL,
  `payment_date` datetime NOT NULL,
  `date_ordered` datetime NOT NULL,
  `date_shipped` datetime NOT NULL,
  `date_received` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_order_logs`
--

CREATE TABLE `app_order_logs` (
  `Id` int(11) NOT NULL,
  `sys_shop` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL COMMENT 'orderId from jcwfp_user_orders',
  `product_id` varchar(255) NOT NULL COMMENT 'Id from 8_inventory',
  `quantity` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `total_amount` decimal(11,2) NOT NULL,
  `order_status` char(1) NOT NULL COMMENT 'p = processing, s = shipped, d = delivery, r = received	',
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_referral_codes`
--

CREATE TABLE `app_referral_codes` (
  `id` int(11) NOT NULL,
  `referral_code` varchar(50) NOT NULL,
  `order_reference_num` varchar(255) NOT NULL,
  `soldto` varchar(255) NOT NULL DEFAULT '---',
  `total_amount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `payment_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = pending, 1 = paid, 2 = unpaid',
  `date_ordered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_processed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = not processed, 1 = processed',
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_referral_commission`
--

CREATE TABLE `app_referral_commission` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `rate_type` char(1) NOT NULL DEFAULT 'f' COMMENT 'f = fixed, p = percentage',
  `rate` decimal(11,2) NOT NULL DEFAULT '0.00',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_referral_commission_shop`
--

CREATE TABLE `app_referral_commission_shop` (
  `id` int(11) NOT NULL,
  `sys_shop` int(11) NOT NULL,
  `comm_type` int(11) NOT NULL,
  `rate_type` varchar(255) NOT NULL DEFAULT 'p' COMMENT 'f=fixed, p=percentage',
  `rate` decimal(11,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_sales_order_details`
--

CREATE TABLE `app_sales_order_details` (
  `id` int(11) NOT NULL,
  `sys_shop` int(11) NOT NULL,
  `reference_num` varchar(255) NOT NULL,
  `paypanda_ref` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL COMMENT 'Id from jcwfp_users or idno for sk online',
  `name` varchar(255) NOT NULL,
  `conno` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `notes` text,
  `areaid` int(11) NOT NULL,
  `total_amount` decimal(11,2) NOT NULL,
  `order_status` char(1) NOT NULL COMMENT 'p = processing, s = shipped, d = delivery, r = received, f = fulfilled',
  `payment_status` int(1) NOT NULL COMMENT '0 = pending, 1 = paid, 2 = unpaid',
  `payment_method` varchar(255) NOT NULL,
  `delivery_signature` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `delivery_id` tinyint(4) NOT NULL,
  `delivery_info` varchar(255) DEFAULT NULL,
  `delivery_ref_num` varchar(255) DEFAULT NULL,
  `delivery_amount` decimal(11,2) DEFAULT NULL,
  `delivery_notes` text NOT NULL,
  `payment_id` tinyint(4) NOT NULL,
  `payment_amount` decimal(11,2) NOT NULL,
  `payment_notes` text NOT NULL,
  `payment_date` datetime NOT NULL,
  `date_ordered` datetime NOT NULL,
  `date_shipped` datetime NOT NULL,
  `date_received` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_sales_order_logs`
--

CREATE TABLE `app_sales_order_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(255) NOT NULL COMMENT 'id from app_sales_order_details',
  `product_id` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `total_amount` decimal(11,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sys_billing`
--

CREATE TABLE `sys_billing` (
  `id` int(11) NOT NULL,
  `billno` int(11) NOT NULL,
  `billcode` varchar(100) NOT NULL,
  `syshop` int(11) NOT NULL,
  `trandate` datetime NOT NULL,
  `totalamount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(255) NOT NULL,
  `processdate` datetime NOT NULL,
  `dateupdated` datetime NOT NULL,
  `ratetype` varchar(10) NOT NULL DEFAULT '---',
  `processrate` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '0 if fix amount, >0 and <=1 if percentage',
  `processfee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `netamount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `paystatus` varchar(255) NOT NULL DEFAULT 'On Process' COMMENT 'On Process, Settled',
  `paiddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `paidamount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `paytype` int(11) NOT NULL DEFAULT '0',
  `payref` varchar(100) NOT NULL DEFAULT '---',
  `payattach` varchar(255) DEFAULT NULL COMMENT 'payment pic attachment',
  `payremarks` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sys_codes`
--

CREATE TABLE `sys_codes` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `code_type` int(11) NOT NULL COMMENT 'id from sys_code_type',
  `code_name` varchar(50) NOT NULL,
  `code_desc` varchar(255) NOT NULL,
  `startdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = inactive, 1 = active',
  `status` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_codes`
--

INSERT INTO `sys_codes` (`id`, `owner_id`, `code_type`, `code_name`, `code_desc`, `startdate`, `enddate`, `created`, `updated`, `is_active`, `status`) VALUES
(1, 0, 1, 'SHOPANDA2020', 'Launch codes for referral program', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2020-05-15 14:43:30', '2020-05-15 14:43:30', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_code_type`
--

CREATE TABLE `sys_code_type` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_code_type`
--

INSERT INTO `sys_code_type` (`id`, `type`, `description`, `created`, `updated`, `status`) VALUES
(1, 'REFERRAL', 'Referral Codes (For commissions)', '2020-05-15 14:42:02', '2020-05-15 14:42:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_cron_referral`
--

CREATE TABLE `sys_cron_referral` (
  `id` int(11) NOT NULL,
  `success` tinyint(4) NOT NULL DEFAULT '-1',
  `message` varchar(255) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `cron_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cron_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sys_delivery_areas`
--

CREATE TABLE `sys_delivery_areas` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_delivery_areas`
--

INSERT INTO `sys_delivery_areas` (`id`, `code`, `name`, `created`, `updated`, `status`) VALUES
(1, 'ANTIPOLO', 'Antipolo', '2020-04-27 07:11:27', '2020-04-27 07:41:04', 1),
(2, 'CALOOCAN', 'Caloocan City', '2020-04-27 07:11:27', '2020-04-27 07:11:27', 1),
(3, 'BACOOR', 'Cavite (Bacoor)', '2020-04-27 07:11:59', '2020-04-27 07:11:59', 1),
(4, 'DASMA', 'Cavite (Dasmariñas)', '2020-04-27 07:11:59', '2020-04-27 07:11:59', 1),
(5, 'IMUS', 'Cavite (Imus)', '2020-04-27 07:12:17', '2020-04-27 07:12:17', 1),
(6, 'LASPINAS', 'Las Piñas City', '2020-04-27 07:12:17', '2020-04-27 07:12:17', 1),
(7, 'MAKATI', 'Makati City', '2020-04-27 07:12:36', '2020-04-27 07:12:36', 1),
(8, 'MALABON', 'Malabon City', '2020-04-27 07:12:36', '2020-04-27 07:12:36', 1),
(9, 'MANDA', 'Mandaluyong City', '2020-04-27 07:12:57', '2020-04-27 07:12:57', 1),
(10, 'MANILA', 'Manila City', '2020-04-27 07:12:57', '2020-04-27 07:12:57', 1),
(11, 'MARIKINA', 'Marikina City', '2020-04-27 07:13:23', '2020-04-27 07:13:23', 1),
(12, 'MUNTINLUPA', 'Muntinlupa City', '2020-04-27 07:13:23', '2020-04-27 07:13:23', 1),
(13, 'NAVOTAS', 'Navotas City', '2020-04-27 07:13:42', '2020-04-27 07:13:42', 1),
(14, 'PQUE', 'Parañaque City', '2020-04-27 07:13:42', '2020-04-27 07:13:42', 1),
(15, 'PASAY', 'Pasay City', '2020-04-27 07:14:03', '2020-04-27 07:14:03', 1),
(16, 'PASIG', 'Pasig City', '2020-04-27 07:14:03', '2020-04-27 07:14:03', 1),
(17, 'CAINTA', 'Rizal (Cainta)', '2020-04-27 07:14:20', '2020-04-27 07:14:20', 1),
(18, 'SANMATEO', 'Rizal (San Mateo)', '2020-04-27 07:14:20', '2020-04-27 07:14:20', 1),
(19, 'TAYTAY', 'Rizal (Taytay)', '2020-04-27 07:14:35', '2020-04-27 07:14:35', 1),
(20, 'QC', 'Quezon City', '2020-04-27 07:14:35', '2020-04-27 07:14:35', 1),
(21, 'SANJUAN', 'San Juan City', '2020-04-27 07:14:55', '2020-04-27 07:14:55', 1),
(22, 'TAGUIG', 'Taguig City', '2020-04-27 07:14:55', '2020-04-27 07:14:55', 1),
(23, 'VALENZUELA', 'Valenzuela City', '2020-04-27 07:15:06', '2020-04-27 07:15:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_idkey`
--

CREATE TABLE `sys_idkey` (
  `id` int(11) NOT NULL,
  `billno` int(11) NOT NULL,
  `commno` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sys_payment_type`
--

CREATE TABLE `sys_payment_type` (
  `id` int(11) NOT NULL,
  `paycode` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_payment_type`
--

INSERT INTO `sys_payment_type` (`id`, `paycode`, `description`, `created`, `updated`, `status`) VALUES
(1, 'CASH', 'Cash', '2020-04-27 06:16:00', '2020-04-27 06:16:00', 1),
(2, 'CHECK', 'Check', '2020-04-27 06:16:00', '2020-04-27 06:16:00', 1),
(3, 'BDEP', 'Bank Deposit', '2020-04-27 06:16:00', '2020-04-27 06:16:00', 1),
(4, 'ONDEP', 'Online Banking Deposit', '2020-04-27 06:16:00', '2020-04-27 06:16:00', 1),
(5, 'PAYPANDA', 'PayPanda', '2020-04-27 06:16:00', '2020-04-27 06:28:50', 1),
(6, 'PAYKASH', 'PayKash', '2020-04-27 06:16:00', '2020-04-27 06:28:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_products`
--

CREATE TABLE `sys_products` (
  `Id` varchar(255) NOT NULL,
  `sys_shop` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `itemid` varchar(255) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `otherinfo` varchar(255) NOT NULL,
  `uom` int(11) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `summary` longtext NOT NULL,
  `details` longtext NOT NULL,
  `enabled` int(1) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_products`
--

INSERT INTO `sys_products` (`Id`, `sys_shop`, `cat_id`, `itemid`, `itemname`, `otherinfo`, `uom`, `price`, `tags`, `summary`, `details`, `enabled`, `date_created`, `date_updated`) VALUES
('01829ad1b587445ba55da1b3a124ea48', 6, 3, '0', 'Apple \"Red\" (Mansanas)', '5 Pieces', 0, '185.00', 'food,apples,fruits,fresh,market,mansanas,grocery,healthy', '', '', 1, '2020-05-12 14:21:44', '2020-05-12 14:21:44'),
('02daa696411e4c909db4abc658f7b297', 1, 1, '484', 'SK HONGKONG SIOMAI', '40Pcs/Pack', 12, '280.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-03-19 00:00:00'),
('03e71d419f704099a461bfa8662ba94a', 6, 6, '0', 'Fish Pomfret \'White\' (Pampano)', '500g', 0, '276.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:38:46'),
('09e02f437e564bcbb48b39c4bb6ec466', 6, 5, '0', 'Beef Sinigang Cut', '500g', 0, '240.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:48:03'),
('0a2b6eb4c6e441dda0e2cc8f2eb134d8', 6, 6, '0', 'Fish Short Bodied Mackerel (Hasa Hasa)', '500g', 0, '254.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:47:43'),
('0bc36429f18248af8dea21852e6ad475', 1, 1, '3450', 'PK SOUR CREAM FLAVOR POWDER', '500G/Pack', 12, '300.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:31:48'),
('0fa89019b1df4e59921a287fd4bd610a', 6, 6, '0', 'Seafood \'Sugpo\' Shrimp Small', '500g', 0, '450.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:40:26'),
('146922b0b57b4019a9459ce6a3a294e8', 6, 6, '0', 'Seafood \'Sugpo\' Shrimp Medium', '500g', 0, '490.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:40:33'),
('14f5b157cb384287b07b53c5281c0dfc', 1, 1, '478', 'SPK CHICKEN SIOPAO', '6Pcs/Pack', 12, '130.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-04-14 12:32:02'),
('1561c87db3fa4753a479fb949e6de9e3', 1, 1, '477', 'SPK COMBI SIOPAO', '6Pcs/Pack', 12, '130.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-04-14 12:32:05'),
('15dd47601b7b4bdda376d117c21395f1', 6, 3, '0', 'Mango \"Green\" (Manggang Kalabaw)', '500g', 0, '90.00', 'food,mango,fruit,green mango,green,fresh,market,manga,grocery,healthy', '', '', 1, '2020-05-12 14:15:35', '2020-05-12 14:15:35'),
('186bb66955514ba19745418687d6ed07', 6, 5, '0', 'Pork Pata', '1kg', 0, '247.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:50:25'),
('2513b51bd1744fde839f6b16cc72a290', 1, 1, '2524', 'POTATO KING CLASSIC CHIPS', '2.2KG/Pack', 12, '650.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:31:50'),
('2fc7f9f1d9944668a824f1598e9e6ae8', 6, 4, '0', 'Fresh Lettuce \'Green Ice\' (Litsugas)', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:58:50'),
('351ce44af700459b85c13931c09825a2', 6, 4, '0', 'Fresh Eggplant (Talong)', '500g', 0, '48.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:57:45'),
('3624bd98d76a4c52b11b2e42af282ff8', 6, 5, '0', 'Pork Kasim (Sinigang Cut)', '500g', 0, '142.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:49:54'),
('367eb96563654eb28a9d1898aa61a843', 6, 6, '0', 'Fish Milkfish \'Boneless\' (Bangus)', '500g', 0, '160.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:38:26'),
('392b35ba3a6b4c6786265610902921a0', 1, 1, '333', 'NH SHARKSFIN SIOMAI', '500G/Pack', 12, '370.00', '', '', '', 1, '2020-04-21 15:50:02', '2020-04-21 15:50:02'),
('3b9733d9c57d4e039ed8d950b2361010', 6, 4, '0', 'Fresh Cucumber \'Green\' (Pipino)', '500g', 0, '44.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:57:33'),
('3cdfc9c8d45d46f09b4133b38ec9cfbe', 6, 5, '0', 'Chicken One Whole', '1 piece', 0, '175.00', 'food,meat,chicken,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:48:48'),
('3d889fe8ea84445d8042cb6922a35e1b', 6, 4, '0', 'Fresh Sweet Peas (Chicharo)', '500g', 0, '118.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:03:21'),
('3e4b30fb107f4c379c7bcda6b13d7b96', 6, 4, '0', 'Fresh Garlic (Bawang)', '500g', 0, '110.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:57:54'),
('455c28bfbac8476db34ad12aa052f77e', 6, 3, '0', 'Banana \"Saba\"', '500g', 0, '50.00', 'food,banana,fruit,saba,fresh,market,saging,grocery,healthy', '', '', 1, '2020-05-12 14:11:26', '2020-05-12 14:11:26'),
('48fa7bde9f6046659940352b8b05196e', 6, 4, '0', 'Fresh Bottle Gourd (Upo)', '1 piece', 0, '93.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:54:48'),
('4b61d39cf58a420592cad99f740b54e7', 6, 3, '0', 'Apple \"Green\" (Mansanas)', '5 Pieces', 0, '185.00', 'food,apple,fruit,green apple,fresh,market,mansanas,grocery,healthy', '', '', 1, '2020-05-12 14:22:34', '2020-05-12 14:22:34'),
('4dfcbde02fa245bbbc2a085c249d3160', 6, 5, '0', 'Chicken Wings', '500g', 0, '102.00', 'food,meat,chicken,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:49:04'),
('501007bc92e9487893ace7f9d2f25f44', 6, 4, '0', 'Fresh Sweet Potato (Kamote)', '500g', 0, '43.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:03:33'),
('51657971ff90456e9131cb96cc7cea67', 6, 4, '0', 'Fresh Green Finger Pepper (Sili Pangsigang)', '500g', 0, '40.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:58:23'),
('51b26297493e4d6eb2dd4498ef9397bd', 1, 1, '509', 'CHILI SAUCE', '1Kg/Bottle', 27, '500.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-05-11 10:53:36'),
('52a82a077c5549458c7057c066b7e9a4', 6, 6, '0', 'Fish Cream Dory \'Fillet\'', '500g', 0, '189.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:38:03'),
('57f644af65714a95ba2641b400d45eff', 6, 5, '0', 'Beef Mechado Cut', '500g', 0, '240.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:47:31'),
('583be50b9fa74f92a0e05cb7f96faa99', 6, 6, '0', 'Seafood \'Sugpo\' Shrimp Large', '500g', 0, '510.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:40:39'),
('58c7a7fb316c490f94e72d1c6f40c487', 6, 4, '0', 'Fresh Carrot', '500g', 0, '32.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:56:30'),
('5b8d99ab19374270854df9d5a0675936', 1, 1, '3448', 'PK CHEESE FLAVOR POWDER', '500G/Pack', 12, '300.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:31:46'),
('5beef42beb424ec6bc9690f8e937b397', 6, 5, '0', 'Pork Ribs', '500g', 0, '125.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:50:34'),
('5d60b495a55043f68251d3b733dfdf7a', 1, 1, '475', 'SPK ASADO SIOPAO', '6Pcs/Pack', 12, '120.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-04-14 12:31:58'),
('5e8923df662c477688e1021d280ad034', 6, 4, '0', 'Fresh Lettuce \'Romaine\' (Litsugas)', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:59:18'),
('608e03faaba645a99fa521e8e8ccbdd6', 6, 5, '0', 'Pork Kasim (Adobo Cut)', '500g', 0, '142.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:49:36'),
('60ad952c70a8476585041722efcfa77a', 6, 4, '0', 'Fresh Pechay \'Baguio\' (Petsay Baguio)', '500g', 0, '55.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:00:42'),
('61eb389b3f8b489abf53d01e210230c6', 6, 4, '0', 'Fresh Moringa (Malunggay)', '500g', 0, '117.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:59:27'),
('62c23ea9beee483cb12c7614652de760', 6, 4, '0', 'Fresh Bell Pepper \'Red\' (Siling Pari)', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:54:28'),
('62c839dd3c3844b88cad17ae199ebe80', 6, 3, '0', 'Mango \"Ripe\" (Manggang Hinog)', '500g', 0, '105.00', 'food,mango,fruit,ripe mango,ripe,fresh,market,manga,grocery,healthy', '', '', 1, '2020-05-12 14:13:01', '2020-05-12 14:13:01'),
('6405aad253084a04b98caf7cef166680', 6, 4, '0', 'Fresh Taro (Gabi)', '500g', 0, '55.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:03:57'),
('65beb5948bf240a8bdcb0de3d8cf3318', 6, 4, '0', 'Fresh Onion \'Red\' (Sibuyas Pula)', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:00:04'),
('68dc2bcc48ef45b1ba1d3cc4e514ee23', 6, 4, '0', 'Fresh Ginger (Luya)', '500g', 0, '156.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:58:08'),
('69defcc76d07403caea25bd3d3941d8f', 6, 4, '0', 'Fresh Pumpkin (Kalabasa)', '500g', 0, '24.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:01:35'),
('6b3ee60caf564ad2ab3f9db560114bbf', 6, 4, '0', 'Fresh Baguio Beans', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:54:06'),
('6c4901c2f7584069bb960e124e7dadfd', 6, 4, '0', 'Fresh Bitter Gourd (Ampalaya)', '500g', 0, '52.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:54:36'),
('6c99c54a1eac4eccab1da838501d514c', 6, 6, '0', 'Seafood \'Suahe\' Shrimp Small', '500g', 0, '225.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:40:54'),
('71fa78d6c8e543bdb1dd7456e9b57fa4', 6, 4, '0', 'Fresh Jicama \'Singkamas\'', '500g', 0, '39.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:58:35'),
('72a227767be544dba41c6061913538dc', 6, 5, '0', 'Beef Ground', '500g', 0, '240.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:47:17'),
('72a2a552f9ac4ab8a34c300e9baa5c17', 6, 3, '0', 'Orange \"Ponkan\"', '5 Pieces', 0, '105.00', 'food,orange,fruit,ponkan,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:24:01', '2020-05-12 14:24:01'),
('7332486ad67a4389aa93e66b05fc06d2', 6, 4, '0', 'Fresh Tamarind (Sampaloc)', '500g', 0, '93.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:03:49'),
('7578763ff95e40a9894d09fe9a28fc71', 6, 4, '0', 'Fresh Water Spinach Native (Kangkong Tagalog)', '500g', 0, '56.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:04:25'),
('759020dcfd0843529d5a0fc0f198c9f0', 1, 1, '2523', 'POTATO KING CLASSIC FRIES', '2.5KG/Pack', 12, '450.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:31:52'),
('769b4453d4cb415eacb18cef05cf5caf', 6, 6, '0', 'Fish Milkfish \'Whole\' (Bangus)', '500g', 0, '145.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:38:33'),
('7a0f6932f3104af4a36f5dc3feff7a54', 6, 6, '0', 'Seafood \'Suahe\' Shrimp Medium', '500g', 0, '285.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:41:01'),
('7d0e29a348a94303952911fdd5c6ed5b', 6, 4, '0', 'Fresh Okra', '500g', 0, '47.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:59:49'),
('7df6bad2bcd949458cc122efad22e837', 6, 3, '0', 'Orange \"Sunkist\"', '5 Pieces', 0, '250.00', 'food,orange,fruit,sunkist,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:24:32', '2020-05-12 14:24:32'),
('885cdc15ffda4affae8d18fa3f94e2fc', 6, 6, '0', 'Fish Tilapia', '500g', 0, '109.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:39:53'),
('892c157f14134673ad4049c0b6e2a97e', 6, 4, '0', 'Fresh Radish (Labanos)', '500g', 0, '60.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:01:49'),
('9401372c22fe49bfbe039eb384938221', 6, 4, '0', 'Fresh Pechay \'Native\' (Petsay Tagalog)', '500g', 0, '48.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:00:52'),
('943b4c82d28749b89f6216fee0edccab', 6, 4, '0', 'Fresh Pechay \'Taiwan\' (Petsay Taiwan)', '500g', 0, '48.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:01:12'),
('961d17f21c004424af321da1dea11920', 6, 3, '0', 'Pineapple (Pinya)', '1 Piece', 0, '110.00', 'food,pineapple,fruit,fresh,market,pinya,grocery,healthy', '', '', 1, '2020-05-12 14:20:16', '2020-05-12 14:20:16'),
('965e6e1932794e6dbf9d975a72406a48', 1, 1, '476', 'SPK BOLA BOLA SIOPAO', '6Pcs/Pack', 12, '120.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-04-14 12:32:00'),
('970fb552b36043d7bd587d392d6991b2', 6, 4, '0', 'Fresh String Beans (Sitaw)', '500g', 0, '66.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:02:49'),
('a0701eb19896468ba59eed13d11293be', 6, 4, '0', 'Fresh Spinach', '500g', 0, '47.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:01:58'),
('a543ef0e94ed45afa7e9d54c1c0121b0', 6, 5, '0', 'Beef Camto', '500g', 0, '211.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:47:10'),
('a59702a0a9f04cc8963ff69d272d3523', 6, 4, '0', 'Fresh Chayote (Sayote)', '500g', 0, '40.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:57:08'),
('a73ec7855de6481db0d4992697936cc9', 6, 4, '0', 'Fresh Cauliflower', '1 piece', 0, '171.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:56:39'),
('a905f13e5b0b486a9677611ebddd08be', 6, 4, '0', 'Fresh Sponge Gourd (Patola)', '500g', 0, '47.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:02:37'),
('aabbaf96487242e8b9af6756ac29a187', 6, 3, '0', 'Pears (Peras)', '5 pieces', 0, '185.00', 'food,pears,fruit,fresh,market,peras,grocery,healthy', '', '', 1, '2020-05-12 14:18:36', '2020-05-12 14:18:36'),
('abe1c46e8985410d9821780ad396e3cf', 1, 1, '491', 'SK JAPANESE SIOMAI', '40Pcs/Pack', 12, '320.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-03-19 00:00:00'),
('b2de0891c60d4f0ebf32cd6549029960', 6, 4, '0', 'Fresh Bell Pepper \'Green\' (Siling Pari)', '500g', 0, '78.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:54:21'),
('b2ea2561d77a4c5fafeca44ba085b887', 6, 4, '0', 'Fresh Celery (Kinchay)', '500g', 0, '55.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:56:53'),
('b5e4c5d761ea42d883583d09f41bc79b', 6, 5, '0', 'Pork Liver', '500g', 0, '73.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 0, '2020-05-12 17:44:04', '2020-05-12 17:51:35'),
('b63430e27e414c5b97b3e6ef7526bb1a', 6, 4, '0', 'Fresh Mung Beans (Munggo)', '500g', 0, '93.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:59:39'),
('b66593fd6232438a8e24317aec108c31', 6, 5, '0', 'Pork Ground', '500g', 0, '142.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:49:27'),
('b7cb4e42e7c841ea97887fb0ea5e8469', 1, 1, '871', 'SK CHICKEN SIOMAI', '40Pcs/Pack', 12, '280.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-04-14 12:31:57'),
('b96af09f79ca49608849cd45a143af8e', 6, 4, '0', 'Fresh Sweet Corn (Matamis na Mais)', '1 piece', 0, '40.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:03:11'),
('bae0286d3dd2483a9aa90f523c8fed9a', 1, 1, '2677', 'POTATO KING SKINNY FRIES', '1.8KG/Pack', 12, '550.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:31:53'),
('baf148410591494195bad9199ad1c861', 6, 6, '0', 'Fish Threadfin Bream (Bisugo)', '500g', 0, '290.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:39:46'),
('bbfab8e9cf054518a9503674119de44c', 6, 6, '0', 'Fish Long Jawed Mackerel (Alumahan)', '500g', 0, '254.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:38:10'),
('bc2270384b124657b732ca856165d09a', 6, 6, '0', 'Fish Spinefoot (Samaral)', '500g', 0, '276.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:39:39'),
('bc47b8b12b5e4215bf3459704560a82e', 6, 3, '0', 'Papaya \"Ripe\"', '1 Piece', 0, '50.00', 'food,papaya,fruit,ripe,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:16:28', '2020-05-12 14:18:49'),
('bd73cb8e14414b9ca7cba0e2c5743efc', 6, 6, '0', 'Seafood \'Suahe\' Shrimp Large', '500g', 0, '340.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:41:27'),
('c04b892656724797b80fd075fc6f3dfe', 6, 4, '0', 'Fresh Broccoli (Per Pc)', '1 piece', 0, '140.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:55:17'),
('c1332b1f5c7e4416b0f0f2aa95ad748d', 6, 4, '0', 'Fresh Cabbage \'Regular Size\' (Repolyo)', '1 piece', 0, '60.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:55:44'),
('c6e7f40516594f64a6f48a64adb2a7e0', 1, 1, '3449', 'PK BARBEQUE FLAVOR POWDER', '500G/Pack', 12, '300.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:31:44'),
('c90fa89edd6e48fab7678a6080c62bcc', 1, 1, '510', 'ROASTED GARLIC', '300G/Bottle', 27, '350.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-04-14 12:31:55'),
('cbe91bed63944d5199ce6d8f5bf99d35', 6, 5, '0', 'Beef Brisket', '500g', 0, '211.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:47:01'),
('d13566c62a4b49b6a3218f5b2add314d', 6, 4, '0', 'Fresh Winged Bean \'Sigarilyas\'', '500g', 0, '55.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:04:34'),
('d1b0cd2fe89a4551999e623899c1a01e', 6, 5, '0', 'Chicken Breast', '500g', 0, '87.00', 'food,meat,chicken,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:48:30'),
('d4ff4996d7674393954f6eb1aa8692df', 6, 5, '0', 'Pork Chop', '500g', 0, '142.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:49:19'),
('d650bda219e045478167abdf5270d98f', 6, 4, '0', 'Fresh Potato (Patatas)', '500g', 0, '78.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:01:22'),
('da8248807dce463eb5c143bf4e645753', 6, 5, '0', 'Pork Kasim (Menudo Cut)', '500g', 0, '142.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:49:43'),
('dc46351a4f684bbf95b81c3b5d8daa5c', 6, 4, '0', 'Fresh Chili Pepper (Sili Labuyo)', '500g', 0, '48.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:57:21'),
('e28d5ef2bfc44120893246860a01c262', 6, 5, '0', 'Pork Liempo', '500g', 0, '167.00', 'food,meat,pork,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:50:02'),
('e501115d845e48a78a4b679bf5fa708d', 6, 6, '0', 'Fish Mackerel Scad (Galunggong)', '500g', 0, '145.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:38:18'),
('e728636ba42c480e9cb34662acb392bb', 6, 3, '0', 'Avocado', '500g', 0, '190.00', 'food,avocado,fruit,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:19:35', '2020-05-12 14:19:35'),
('ecb2d5a21b144b3bb45b0dc9a2cc0cee', 6, 5, '0', 'Chicken Drumstick', '500g', 0, '98.00', 'food,meat,chicken,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:48:39'),
('eed54224384044f7bd3a551226150e60', 6, 6, '0', 'Fish Ponyfish (Sapsap)', '500g', 0, '327.00', 'food,seafood,fish,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:44:12'),
('ef310fcd25594dad94a85a7a24c3b1ff', 6, 4, '0', 'Fresh Lettuce \'Iceberg\' (Litsugas)', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:59:08'),
('f0b6133afdd149f39621f66c241a8a0d', 6, 3, '0', 'Orange \"Kiat Kiat\"', '500g', 0, '109.00', 'food,orange,fruit,kiatkiat,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:23:23', '2020-05-12 14:23:23'),
('f2442b4985044d81877d2c4f3f1047e0', 6, 4, '0', 'Fresh Tomato (Kamatis)', '500g', 0, '40.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:04:08'),
('f2751211b6ee4f41961a3aa24eb17b30', 6, 5, '0', 'Beef Kaldereta Cut', '500g', 0, '240.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:47:24'),
('f38f34bc699641e780465f1ebdaa2d4c', 1, 1, '479', 'SPK CHILI ASADO SIOPAO', '6Pcs/Pack', 12, '130.00', '', '', '', 1, '2020-04-04 22:10:00', '2020-04-14 12:32:03'),
('f3cc4ac839a64493aca6ad8ef3120f9e', 6, 4, '0', 'Fresh Calamansi', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 14:55:56'),
('f54446ad06614e63b32c897c791782ba', 6, 5, '0', 'Chicken Thigh', '500g', 0, '87.00', 'food,meat,chicken,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:48:57'),
('f5babf07ba2a4db6ae8fa35ceb5cf139', 1, 1, '485', 'SK SHANGHAI SIOMAI', '40Pcs/Pack', 12, '280.00', '', '', '', 1, '2020-03-19 00:00:00', '2020-03-19 00:00:00'),
('f969cab6a37a43fdabc5b5d92ecb979e', 6, 4, '0', 'Fresh Onion Spring (Dahon ng Sibuyas)', '500g', 0, '62.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:00:31'),
('fa477164868c40ce90eed2293d7374be', 6, 6, '0', 'Seafood Squid (Pusit)', '500g', 0, '174.00', 'food,seafood,squid,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:41:43'),
('fb04345540164ffeb8738377d9b55dc9', 6, 4, '0', 'Fresh Onion \'White\' (Sibuyas Puti)', '500g', 0, '48.00', 'food,veggies,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 14:53:32', '2020-05-12 15:00:14'),
('fe89bd147d0446eba369083d36db4f7c', 6, 5, '0', 'Beef Menudo Cut', '500g', 0, '240.00', 'food,meat,beef,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 17:44:04', '2020-05-12 17:51:48'),
('fe8ce1a9f37b44e2bb0bec2c1f826045', 6, 6, '0', 'Seafood \'Sugpo\' Shrimp Jumbo', '500g', 0, '560.00', 'food,seafood,suahe,shrimp,fresh,market,grocery,healthy', '', '', 1, '2020-05-12 18:36:44', '2020-05-12 18:41:15');

-- --------------------------------------------------------

--
-- Table structure for table `sys_product_category`
--

CREATE TABLE `sys_product_category` (
  `id` int(11) NOT NULL,
  `category_code` varchar(10) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `on_menu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = not displayed, 1 = displayed on menu',
  `priority` decimal(11,2) NOT NULL COMMENT 'used for sorting the display',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_product_category`
--

INSERT INTO `sys_product_category` (`id`, `category_code`, `category_name`, `on_menu`, `priority`, `created`, `updated`, `status`) VALUES
(1, 'FOODS', 'Food', 1, '0.02', '2020-04-27 02:27:35', '2020-05-11 01:59:30', 1),
(2, 'DRINK', 'Drinks', 1, '2.00', '2020-04-27 02:27:35', '2020-05-11 01:57:53', 1),
(3, 'FRUIT', 'Fruits', 1, '10.00', '2020-05-11 02:01:49', '2020-05-11 02:01:49', 1),
(4, 'VEGGIES', 'Vegetables', 1, '5.00', '2020-05-12 06:32:16', '2020-05-12 06:32:16', 1),
(5, 'MEAT', 'Meat', 1, '10.00', '2020-05-12 09:42:55', '2020-05-12 09:42:55', 1),
(6, 'SEAFOOD', 'Seafoods', 1, '10.00', '2020-05-12 09:56:54', '2020-05-12 09:56:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_shipping_partners`
--

CREATE TABLE `sys_shipping_partners` (
  `id` int(11) NOT NULL,
  `shipping_code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_shipping_partners`
--

INSERT INTO `sys_shipping_partners` (`id`, `shipping_code`, `name`, `created`, `updated`, `status`) VALUES
(1, 'GRAB', 'Grab', '2020-04-22 14:42:41', '2020-04-27 14:12:12', 1),
(2, 'LALA', 'Lalamove', '2020-04-22 14:42:41', '2020-04-22 14:42:41', 1),
(3, 'ANGKS', 'Angkas', '2020-04-22 14:42:41', '2020-04-22 14:42:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_shops`
--

CREATE TABLE `sys_shops` (
  `id` int(11) NOT NULL,
  `shopcode` varchar(5) NOT NULL,
  `shopname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `shippingfee` decimal(11,2) NOT NULL,
  `daystoship` int(11) NOT NULL DEFAULT '2',
  `logo` varchar(100) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sys_shops`
--

INSERT INTO `sys_shops` (`id`, `shopcode`, `shopname`, `email`, `mobile`, `shippingfee`, `daystoship`, `logo`, `created`, `updated`, `status`) VALUES
(1, 'JCWW', 'JC Worldwide', 'pauldelarosachua@gmail.com', '09773881298', '100.00', 5, '524487eaf349492db8f80f75c7220a63', '2020-04-13 13:51:23', '2020-05-12 16:59:17', 1),
(3, 'JCP', 'JC Premiere', 'paul_vincent_chua@yahoo.com', '09999999999', '150.00', 2, '9480648339824cf98f765ede45d265f5', '2020-04-17 14:19:18', '2020-05-12 19:03:01', 1),
(6, 'FMPH', 'Fresh Market PH', 'paulchua@cloudpanda.ph', '09985962156', '155.00', 5, '54a86f929258462c8596cc1085886b8e', '2020-05-08 17:15:47', '2020-05-12 16:57:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_shop_account`
--

CREATE TABLE `sys_shop_account` (
  `id` int(11) NOT NULL,
  `accountname` varchar(100) NOT NULL,
  `accountno` varchar(50) NOT NULL,
  `bankname` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sys_shop_rate`
--

CREATE TABLE `sys_shop_rate` (
  `id` int(11) NOT NULL,
  `syshop` int(11) NOT NULL,
  `ratetype` varchar(100) NOT NULL DEFAULT 'p' COMMENT 'p=percetange, f=fix',
  `rateamount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_shop_rate`
--

INSERT INTO `sys_shop_rate` (`id`, `syshop`, `ratetype`, `rateamount`, `status`) VALUES
(1, 0, 'p', '0.20', 1),
(2, 1, 'p', '1.00', 1),
(3, 2, 'f', '100.00', 1),
(4, 3, 'f', '150.00', 1),
(5, 6, 'f', '150.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sys_shop_shipping`
--

CREATE TABLE `sys_shop_shipping` (
  `id` int(11) NOT NULL,
  `sys_shop` int(11) NOT NULL,
  `areaid` int(11) NOT NULL,
  `shippingfee` decimal(11,2) NOT NULL,
  `daystoship` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `last_seen` datetime DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `failed_login_attempts` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_users`
--

INSERT INTO `sys_users` (`id`, `username`, `password`, `avatar`, `functions`, `last_seen`, `active`, `failed_login_attempts`) VALUES
(17, 'paulchua@cloudpanda.ph', '$2y$12$MJNxB0RhkGUlCkJpu3Zmw.BAPE9bvNaGJqpBeeJAxwwcMsAvdquKO', '15879843880595Paul_Photo.jpg', '{\"overall_access\":1,\"online_ordering\":1,\"dashboard\":{\"view\":1},\"transactions\":{\"view\":1},\"products\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"shops\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"customer\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"accounts\":1,\"billing\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"codes\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"reports\":1,\"ps\":{\"view\":1},\"por\":{\"view\":1},\"sr\":{\"view\":1},\"ssr\":{\"view\":1},\"settings\":1,\"change_password\":{\"update\":1},\"users\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"members\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"delivery_areas\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"payment_type\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"category\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"shipping_partners\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1}}', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `web_pageviews`
--

CREATE TABLE `web_pageviews` (
  `id` int(11) NOT NULL,
  `page` text NOT NULL,
  `trandate` datetime NOT NULL,
  `ip` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `web_total_visitors`
--

CREATE TABLE `web_total_visitors` (
  `id` int(11) NOT NULL,
  `session` text NOT NULL,
  `trandate` datetime NOT NULL,
  `timesess` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `8_referralcomlog`
--
ALTER TABLE `8_referralcomlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `8_referralcomsummary`
--
ALTER TABLE `8_referralcomsummary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `8_referralpayout`
--
ALTER TABLE `8_referralpayout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_customers`
--
ALTER TABLE `app_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_members`
--
ALTER TABLE `app_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_member_type`
--
ALTER TABLE `app_member_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_notifications`
--
ALTER TABLE `app_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_order_details`
--
ALTER TABLE `app_order_details`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `app_order_logs`
--
ALTER TABLE `app_order_logs`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `app_referral_codes`
--
ALTER TABLE `app_referral_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_referral_commission`
--
ALTER TABLE `app_referral_commission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_referral_commission_shop`
--
ALTER TABLE `app_referral_commission_shop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_sales_order_details`
--
ALTER TABLE `app_sales_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sys_shop` (`sys_shop`);

--
-- Indexes for table `app_sales_order_logs`
--
ALTER TABLE `app_sales_order_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `sys_billing`
--
ALTER TABLE `sys_billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_codes`
--
ALTER TABLE `sys_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_code_type`
--
ALTER TABLE `sys_code_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_cron_referral`
--
ALTER TABLE `sys_cron_referral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_delivery_areas`
--
ALTER TABLE `sys_delivery_areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_idkey`
--
ALTER TABLE `sys_idkey`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_payment_type`
--
ALTER TABLE `sys_payment_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_products`
--
ALTER TABLE `sys_products`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `sys_product_category`
--
ALTER TABLE `sys_product_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_shipping_partners`
--
ALTER TABLE `sys_shipping_partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_shops`
--
ALTER TABLE `sys_shops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_shop_account`
--
ALTER TABLE `sys_shop_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_shop_rate`
--
ALTER TABLE `sys_shop_rate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_shop_shipping`
--
ALTER TABLE `sys_shop_shipping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_users`
--
ALTER TABLE `sys_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_pageviews`
--
ALTER TABLE `web_pageviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_total_visitors`
--
ALTER TABLE `web_total_visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `8_referralcomlog`
--
ALTER TABLE `8_referralcomlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `8_referralcomsummary`
--
ALTER TABLE `8_referralcomsummary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `8_referralpayout`
--
ALTER TABLE `8_referralpayout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_customers`
--
ALTER TABLE `app_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_members`
--
ALTER TABLE `app_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `app_member_type`
--
ALTER TABLE `app_member_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `app_notifications`
--
ALTER TABLE `app_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_order_logs`
--
ALTER TABLE `app_order_logs`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_referral_codes`
--
ALTER TABLE `app_referral_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_referral_commission`
--
ALTER TABLE `app_referral_commission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_referral_commission_shop`
--
ALTER TABLE `app_referral_commission_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_sales_order_details`
--
ALTER TABLE `app_sales_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_sales_order_logs`
--
ALTER TABLE `app_sales_order_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_billing`
--
ALTER TABLE `sys_billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_codes`
--
ALTER TABLE `sys_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sys_code_type`
--
ALTER TABLE `sys_code_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sys_cron_referral`
--
ALTER TABLE `sys_cron_referral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_delivery_areas`
--
ALTER TABLE `sys_delivery_areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sys_idkey`
--
ALTER TABLE `sys_idkey`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_payment_type`
--
ALTER TABLE `sys_payment_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sys_product_category`
--
ALTER TABLE `sys_product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sys_shipping_partners`
--
ALTER TABLE `sys_shipping_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sys_shops`
--
ALTER TABLE `sys_shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sys_shop_rate`
--
ALTER TABLE `sys_shop_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sys_shop_shipping`
--
ALTER TABLE `sys_shop_shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_users`
--
ALTER TABLE `sys_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `web_pageviews`
--
ALTER TABLE `web_pageviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_total_visitors`
--
ALTER TABLE `web_total_visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
