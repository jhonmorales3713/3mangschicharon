-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2022 at 06:25 PM
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
-- Table structure for table `sys_users`
--

CREATE TABLE `sys_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL COMMENT 'email address',
  `password` varchar(100) NOT NULL,
  `full_name` varchar(250) NOT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `mname` varchar(11) NOT NULL,
  `mobile_number` int(11) DEFAULT NULL,
  `avatar` varchar(150) NOT NULL,
  `functions` text DEFAULT NULL,
  `access_nav` varchar(255) NOT NULL COMMENT 'cp_main_navigation',
  `access_content_nav` text NOT NULL COMMENT 'cp_content_navigation',
  `last_seen` datetime DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `failed_login_attempts` tinyint(2) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1 COMMENT '1 = admin | 0 = off | 2 = customer',
  `first_login` int(11) NOT NULL DEFAULT 0,
  `code_isset` int(11) NOT NULL DEFAULT 0,
  `login_code` varchar(255) DEFAULT NULL,
  `attempt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_users`
--

INSERT INTO `sys_users` (`id`, `username`, `password`, `full_name`, `fname`, `lname`, `mname`, `mobile_number`, `avatar`, `functions`, `access_nav`, `access_content_nav`, `last_seen`, `active`, `failed_login_attempts`, `role`, `first_login`, `code_isset`, `login_code`, `attempt`) VALUES
(17, 'moralesjhon03@gmail.com', 'SVFGWTY4MnYzYVZvTmM1cEl4ODVqdz09', '', NULL, NULL, '', NULL, '15879843880595Paul_Photo.jpg', '{\"overall_access\":1,\"seller_access\":0,\"seller_branch_access\":0,\"food_hub_access\":0,\"online_ordering\":0,\"dashboard\":{\"view\":1,\"sales_count_view\":1,\"transactions_count_view\":1,\"views_count_view\":1,\"overall_sales_count_view\":1,\"visitors_chart_view\":1,\"views_chart_view\":1,\"sales_chart_view\":1,\"top10productsold_list_view\":1,\"transactions_chart_view\":1},\"transactions\":{\"view\":1,\"update\":1,\"reassign\":1,\"mark_as_paid\":0,\"process_order\":1,\"ready_pickup\":1,\"booking_confirmed\":1,\"mark_fulfilled\":1,\"returntosender\":1,\"redeliver\":1,\"shipped\":1},\"pending_orders\":{\"view\":1},\"paid_orders\":{\"view\":1},\"readyforprocessing_orders\":{\"view\":1},\"aul\":{\"view\":1,\"create\":1,\"update\":1,\"disable\":1,\"delete\":1},\"processing_orders\":{\"view\":1},\"readyforpickup_orders\":{\"view\":1},\"bookingconfirmed_orders\":{\"view\":1},\"fulfilled_orders\":{\"view\":1},\"shipped_orders\":{\"view\":1},\"returntosender_orders\":{\"view\":1},\"voided_orders\":{\"view\":1},\"forpickup_orders\":{\"view\":0},\"manualorder_list\":{\"view\":1,\"create\":1},\"refund_order\":{\"create\":1},\"refund_order_approval\":{\"view\":1,\"update\":1,\"approve\":1,\"reject\":1},\"refund_order_trans\":{\"view\":1},\"products\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"shops\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"shop_branch\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"branch_account\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"shop_account\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"customer\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"accounts\":1,\"billing\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"billing_portal_fee\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"codes\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0,\"disable\":0},\"vouchers\":1,\"vc\":{\"view\":1},\"Voucher_List\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0,\"disabled\":0},\"wallet\":1,\"prepayment\":{\"view\":1,\"create\":1},\"manual_order\":{\"view\":1,\"create\":1},\"reports\":1,\"ps\":{\"view\":1},\"por\":{\"view\":0},\"sr\":{\"view\":1},\"ssr\":{\"view\":0},\"pr\":{\"view\":0},\"psr\":{\"view\":0},\"aov\":{\"view\":1},\"to\":{\"view\":1},\"os\":{\"view\":1},\"tps\":{\"view\":1},\"tsr\":{\"view\":1},\"rbsr\":{\"view\":1},\"rbbr\":{\"view\":1},\"oscrr\":{\"view\":1},\"tacr\":{\"view\":1},\"po\":{\"view\":1},\"inv\":{\"view\":1},\"invend\":{\"view\":1},\"invlist\":{\"view\":1},\"osr\":{\"view\":1},\"rbl\":{\"view\":1},\"oblr\":{\"view\":1},\"bpr\":{\"view\":1},\"prr\":{\"view\":1},\"rosum\":{\"view\":1},\"rostat\":{\"view\":1},\"settings\":1,\"change_password\":{\"update\":0},\"users\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"members\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"delivery_areas\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0,\"disable\":0},\"announcement\":{\"view\":1,\"update\":1},\"currency\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0,\"disable\":0},\"payment_type\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"ref_comrate\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"settings_region\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"settings_city\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"settings_province\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"shipping_and_delivery\":1,\"general_shipping\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"custom_shipping\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"category\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"shipping_partners\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"shop_banners\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"void_record\":{\"process\":1},\"void_record_list\":{\"view\":1},\"csr\":0,\"ticket_history\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"csr_ticket\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"csr_ticket_log\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"developer_settings\":1,\"shop_utilities\":{\"view\":1,\"update\":1},\"content_navigation\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0,\"disable\":0},\"cron_logs\":{\"view\":0,\"disable\":0},\"manual_cron\":1,\"client_information\":{\"view\":0,\"create\":0,\"update\":0,\"delete\":0,\"disable\":0},\"audit_trail\":{\"view\":1},\"api_postback_logs\":{\"view\":1},\"voucher_list\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1},\"pandabooks_api_logs\":{\"view\":1}}', '3, 4, 5, 6, 7, 8, 10', '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 19, 20, 25, 27, 28, 29, 30, 31, 32, 33, 34, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 70, 71, 72, 73, 74, 75, 76, 81, 82, 83, 115', NULL, 1, 0, 0, 0, 0, NULL, 0),
(101, 'admin@yahoo.com', 'SVFGWTY4MnYzYVZvTmM1cEl4ODVqdz09', '', NULL, NULL, '', NULL, '16469189053756viber_image_2022-03-08_12-23-14-486.jpg', '{\"online_ordering\":1,\"products\":{\"view\":1,\"create\":1,\"update\":1,\"delete\":1,\"disable\":1},\"variants\":{\"view\":1,\"create\":1,\"update\":1,\"disable\":1,\"delete\":1},\"aul\":{\"view\":1,\"create\":1,\"update\":1,\"disable\":1,\"delete\":1}}', '3, 8', '37, 47', NULL, 1, 0, 1, 0, 0, NULL, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
