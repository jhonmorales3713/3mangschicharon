-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2020 at 04:07 AM
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
-- Table structure for table `cp_content_navigation`
--

CREATE TABLE `cp_content_navigation` (
  `id` int(11) NOT NULL,
  `cn_url` varchar(255) NOT NULL,
  `cn_name` varchar(255) NOT NULL,
  `cn_description` varchar(255) NOT NULL,
  `cn_hasline` int(11) NOT NULL DEFAULT '0',
  `cn_fkey` int(11) NOT NULL COMMENT 'jcw_main_navigation->id',
  `date_created` datetime NOT NULL,
  `arrangement` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cp_content_navigation`
--

INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES
(1, 'Main_settings/area/', 'Area', 'New Area and option to manage each record.', 0, 8, '2018-07-13 00:00:00', 0, 1),
(2, 'Main_settings/credit_term/', 'Credit Term', 'New supplier credit term and option to manage each record.', 0, 8, '2018-07-13 00:00:00', 0, 1),
(3, 'Main_settings/delivery_vehicle/', 'Delivery Vehicle', 'New Delivery Vechicle and option to manage each record.', 0, 8, '2018-07-13 00:00:00', 0, 1),
(4, 'Main_settings/employee/', 'Employee', 'New Employee and option to manage each record.', 0, 8, '2018-07-13 00:00:00', 0, 1),
(5, 'Main_settings/employee_type/', 'Employee Type', 'New Employee type and option to manage each record.', 0, 8, '2018-07-13 00:00:00', 0, 1),
(6, 'Main_settings/franchise/', 'Franchise', 'New franchise package and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(7, 'Main_settings/gl_accounts/', 'GL Accounts', 'New GL Accounts and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(8, 'Main_settings/inventory_category/', 'Inventory Category', 'New inventory category and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(9, 'Main_settings/payment_option/', 'Payment Option', 'New payment type and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(10, 'Main_settings/price_category/', 'Price Category', 'New inventory price category and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(12, 'Main_settings/sales_area/', 'Sales Area', 'New Sales Area and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(13, 'Main_settings/system_user/', 'System User', 'System User Setup.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(14, 'Main_settings/ticket_status/', 'Ticket Status', 'New ticket status and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(15, 'Main_settings/uom/', 'Unit of Measurement', 'New unit of measurement and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(16, 'Main_dev_settings/user_role/', 'User Role', 'New user roles and option to add/edit/delete each record.', 0, 12, '2018-07-17 00:00:00', 0, 1),
(17, 'Main_settings/void_record/', 'Void Record', 'New void record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(18, 'Main_settings/warehouse_location/', 'Warehouse Location', 'New warehouse and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 1),
(20, 'sales_order_form/', 'Sales Order', 'New sales order transaction.', 0, 2, '2018-07-20 07:25:08', 1, 1),
(21, 'sales_summary/', 'Sales Order Transaction History', 'Summary of all repeat order transaction.', 0, 2, '2018-07-20 07:34:19', 2, 1),
(22, 'Main_sales/sales_dr/', 'Delivery Receipt', 'New Delivery Receipt Order.', 0, 2, '2018-07-20 07:35:15', 3, 1),
(23, 'Main_sales/sales_drtran/', 'Delivery Receipt Transaction History', 'Summary of all Delivery Receipt Order transaction.', 0, 2, '2018-07-20 07:36:20', 4, 1),
(24, 'Main_sales/sales_return/', 'Sales Return', 'Inventory returns from distributors/customers.', 0, 2, '2018-07-20 07:37:36', 9, 1),
(25, 'Main_sales/salesreturn_summary/', 'Sales Return Transaction History', 'Summary of all sales return transaction.', 0, 2, '2018-07-20 08:17:19', 10, 1),
(26, 'Main_sales/sales_collection/', 'Collection', 'New collection/payment transaction.', 0, 2, '2018-07-20 08:57:43', 7, 1),
(27, 'Main_sales/collection_summary/', 'Collection Transaction History', 'Summary of all collection transaction.', 0, 2, '2018-07-20 09:14:15', 8, 1),
(28, 'Main_sales/sales_invoice/', 'Sales Invoice', 'List of Sales Invoice.', 0, 2, '2018-07-20 09:16:45', 0, 0),
(29, 'Main_sales/credit_memo/', 'Credit Memo', 'New credit memo transaction.', 0, 2, '2018-07-20 10:43:52', 11, 1),
(30, 'Main_sales/salesorder_itinerary/', 'Sales Order Itinerary', 'Summary of all Sales Order Itinerary transaction.', 0, 2, '2018-07-20 10:44:23', 13, 1),
(31, 'Main_sales/itinerary_summary/', 'Itinerary Report', 'Set the itinerary of DR.', 0, 2, '2018-07-20 10:44:59', 14, 1),
(32, 'Main_sales/fran_service_receipt/', 'Franchise Service Receipt', 'New service receipt transaction.', 0, 2, '2018-07-20 10:45:27', 0, 0),
(33, 'Main_sales/fran_service_history/', 'Franchise Service Receipt Transaction History', 'New service receipt transaction.', 0, 2, '2018-07-20 10:45:51', 0, 0),
(34, 'Main_sales/service_collection/', 'Franchise Service Receipt Collection', 'New collection/payment transaction for franchise service.', 0, 2, '2018-07-20 10:46:24', 0, 0),
(35, 'Main_sales/fsr_collection_summary/', 'FSR Collection Transaction History', 'Summary of all franchise service receipt collection transaction.', 0, 2, '2018-07-20 10:46:53', 0, 0),
(36, 'Main_sales/sales_order_preparation/', 'Sales Order for Preparation', 'List of Sales Order for Preparation.', 0, 2, '2018-07-20 10:47:24', 15, 1),
(37, 'Main_sales/salesorder_prep_summary/', 'Sales Order Preparation Summary Transaction History', 'New collection/payment transaction.', 0, 2, '2018-07-20 10:47:51', 16, 1),
(38, 'Main_sales/sales_invoicetran/', 'Sales Invoice Transaction History', 'Summary of all Sales Invoice transaction.', 0, 2, '2018-07-20 10:48:21', 0, 0),
(39, 'Main_sales/credit_memosummary/', 'Credit Memo Transaction History', 'Summary of all credit memo transaction.', 0, 2, '2018-07-20 10:48:51', 12, 1),
(42, 'google.coms', 'Googles', 'redirect to google.coms', 0, 1, '2018-07-21 07:05:58', 0, 0),
(43, 'www.google.comX', 'google', 'sasaS', 0, 4, '2018-07-21 11:45:10', 0, 0),
(44, 'Main_sales/sales_drtagging/', 'Delivery Receipt Tagging', 'Tagging of Delivery Receipt Orders.', 0, 2, '2018-07-21 18:29:10', 0, 0),
(45, 'Main_sales/sales_drtagginghistory/', 'Delivery Receipt Tagging Transaction History', 'Summary of all Delivery Receipt Tagged.', 0, 2, '2018-07-21 18:30:01', 0, 0),
(54, 'inventory_list/', 'Inventory List', 'List of all inventory and option to add/edit/delete each record.', 0, 4, '2018-07-21 20:26:34', 0, 1),
(55, 'Main_inventory/inventory_pricing_list/', 'Inventory Pricing', 'Set inventory selling price for inventory for each pricing category.', 0, 4, '2018-07-21 20:27:29', 0, 1),
(56, 'Main_inventory/inventory_location_transfer/', 'Inventory Location Transfer', 'Transfer inventory from one location to another.', 0, 4, '2018-07-21 20:27:59', 0, 1),
(57, 'Main_inventory/inventory_ilt_receive/', 'Inventory Location Transfer Receive', 'Set receive inventory location transfer items.', 0, 4, '2018-07-21 20:28:31', 0, 1),
(58, 'Main_inventory/inventory_location_transfer_transaction_history/', 'Inventory Location Transfer Transaction History', 'Summary of all inventory location transfer transaction.', 0, 4, '2018-07-21 20:29:01', 0, 1),
(59, 'Main_inventory/inventory_adjustment/', 'Inventory Adjustment', 'Inventory adjustment for construction materials, manufacturing, spoilages, research and development and expense.', 0, 4, '2018-07-21 20:29:47', 0, 1),
(60, 'Main_inventory/inventory_adjustment_history/', 'Inventory Adjustment History', 'Summary of all inventory adjustment transaction.', 0, 4, '2018-07-21 20:30:15', 0, 1),
(61, 'Main_inventory/inventory_adjustment_offset/', 'Inventory Adjustment (Off Set)', 'Inventory adjustment for balancing the inventory count.', 0, 4, '2018-07-21 20:30:44', 0, 1),
(62, 'Main_inventory/inventory_supplier_pricing/', 'Inventory Supplier Pricing', 'Set inventory purchasing price.', 0, 4, '2018-07-21 20:31:53', 0, 1),
(63, 'Main_inventory/inventory_franchise_assignment/', 'Inventory Franchise Assignment', 'Set franchise inventory.', 0, 4, '2018-07-21 20:32:25', 0, 1),
(64, 'Main_inventory/inventory_actual_count/', 'Inventory Actual Count', 'Add new inventory actual count record.', 0, 4, '2018-07-21 20:33:20', 0, 1),
(65, 'Main_inventory/inventory_limit_purchases/', 'Inventory Limit Purchases', 'List of all inventory limit purchases.', 0, 4, '2018-07-21 20:34:08', 0, 1),
(66, 'Main_inventory/inventory_status_update/', 'Inventory Status Update', 'Update inventory status. This will be use to determine in which part of the system the inventory can be used.', 0, 4, '2018-07-21 20:34:35', 0, 1),
(67, 'Main_entity/entity_supplierlist/', 'Supplier', 'List of all suppliers and option to add/edit/delete each record.', 0, 5, '2018-07-22 22:01:24', 0, 1),
(68, 'Main_entity/customer/', 'Customer', 'Manage customer information.', 0, 5, '2018-07-22 22:01:51', 0, 1),
(69, 'Main_entity/entity_ticket/', 'Customer Ticket', 'New customer ticket to manage distributor\\\'s account and concerns.', 0, 5, '2018-07-22 22:02:32', 0, 1),
(70, 'Main_entity/entity_ticketlist/', 'Customer Ticket Transaction History', 'Summary of all customer ticket transaction.', 0, 5, '2018-07-22 22:04:01', 0, 1),
(71, 'Main_entity/entity_deliveryvehicle/', 'Delivery Vehicle Maintenance', 'Delivery vehicle maintenance record.', 0, 5, '2018-07-22 22:04:32', 0, 1),
(72, 'Main_entity/entity_franchise_paymentrecord/', 'Franchise Payment Record', 'New Franchise Payment Record.', 0, 5, '2018-07-22 22:06:12', 0, 1),
(73, 'Main_entity/entity_franchiseList/', 'Franchise Payment Record Transaction History', 'Summary of all Franchise Payment Record.', 0, 5, '2018-07-22 22:06:39', 0, 1),
(74, 'Main_entity/entity_supplier_limitpurchases/', 'Supplier Limit Purchases', 'Supplier limit purchase and option to add/edit/delete each record.', 0, 5, '2018-07-22 22:07:06', 0, 1),
(75, 'Main_entity/entity_customersoa/', 'Customer Statement of Account', 'Customer statement of account.', 0, 5, '2018-07-22 22:07:31', 0, 1),
(76, 'Main_entity/entity_salesagent/', 'Sales Agent', 'Update Sales Agent.', 0, 5, '2018-07-22 22:07:53', 0, 1),
(77, 'Main_manufacturing/build_inventory/', 'Build List', 'New Build build.', 0, 6, '2018-07-22 22:31:18', 0, 1),
(78, 'Main_manufacturing/ingredients_addition/', 'Ingredients Addition', 'New ingredients addition to inventory build.', 0, 6, '2018-07-22 22:31:43', 0, 1),
(79, 'Main_manufacturing/Material_balance/', 'Material Balance', 'List of all material balance and option to add/edit/delete each record.', 0, 6, '2018-07-22 22:32:07', 0, 1),
(80, 'Main_manufacturing/build_inventory_list/', 'Build Inventory Transaction History', 'Summary of all inventory build transaction.', 0, 6, '2018-07-22 22:32:35', 0, 1),
(81, 'Main_manufacturing/manufacturing_ingredientslist/', 'Ingredients Addition Transaction History', 'Summary of all ingredients addition transaction.', 0, 6, '2018-07-22 22:33:04', 0, 1),
(82, 'Main_account/accounts_payable_voucher/', 'Accounts Payable Voucher', 'New accounts payable voucher.', 0, 7, '2018-07-22 22:43:17', 0, 1),
(83, 'Main_account/check/', 'Check', 'New check.', 0, 7, '2018-07-22 22:44:08', 0, 1),
(84, 'Main_account/cashvoucher/', 'Cash Voucher', 'New cash voucher.', 0, 7, '2018-07-22 22:44:32', 0, 1),
(85, 'Main_account/cv_approval/', 'Cash Voucher Approval', 'Summary of all cash voucher for approval.', 0, 7, '2018-07-22 22:44:59', 0, 1),
(86, 'Main_account/check_release/', 'Check Release', 'Release check.', 0, 7, '2018-07-22 22:45:19', 0, 1),
(87, 'Main_account/check_transaction_release_history/', 'Check Release Transaction History', 'Summary of all checks that has been released.', 0, 7, '2018-07-22 22:45:49', 0, 1),
(88, 'Main_account/bankdeposit/', 'Bank Deposit', 'New bank deposit.', 0, 7, '2018-07-22 22:46:09', 0, 1),
(89, 'Main_account/bankdeposit_transaction/', 'Bank Deposit Transaction History', 'Summary of all bank deposit transaction.', 0, 7, '2018-07-22 22:46:33', 0, 1),
(90, 'Main_account/bounce_check/', 'Bounce Check', 'New Bounce Check.', 0, 7, '2018-07-22 22:46:57', 0, 1),
(91, 'Main_account/bct_history/', 'Bounce Check Transaction History', 'Summary of all Bounce Check transaction.', 0, 7, '2018-07-22 22:47:25', 0, 1),
(92, 'Main_account/apv_list/', 'Accounts Payable Voucher List', 'Summary of all accounts payable voucher.', 0, 7, '2018-07-22 22:47:54', 0, 1),
(93, 'Main_account/check_approval/', 'Check Approval', 'Summary of all checks for approval.', 0, 7, '2018-07-22 22:48:24', 0, 1),
(94, 'Main_account/check_transaction_history/', 'Check Transaction History', 'Summary of all check transaction.', 0, 7, '2018-07-22 22:48:47', 0, 1),
(95, 'Main_account/cashvoucher_transaction/', 'Cash Voucher Transaction History', 'Summary of all cash voucher transaction.', 0, 7, '2018-07-22 22:49:11', 0, 1),
(96, 'Main_account/gl_transaction/', 'GL Transaction', 'New GL transaction.', 0, 7, '2018-07-22 22:49:33', 0, 1),
(97, 'Main_account/gl_transaction_history/', 'GL Transaction History', 'Summary of all GL transaction.', 0, 7, '2018-07-22 22:49:58', 0, 1),
(98, 'Main_account/cash_on_hand/', 'Cash On Hand', 'Summary of cash on hand transaction.', 0, 7, '2018-07-22 22:50:27', 0, 1),
(99, 'Main_account/cash_on_hand_history/', 'Cash On Hand Transaction History', 'Summary of cash on hand transaction list.', 0, 7, '2018-07-22 22:51:49', 0, 1),
(100, 'Main_reports/customer_summary_report/', 'Customer Summary Report', 'Summary of all Customer.', 1, 10, '2018-07-23 09:52:54', 0, 1),
(101, 'Main_reports/gl_tran_report/', 'GL Transaction Report', 'Summary of all GL Transaction.', 2, 10, '2018-07-23 09:53:33', 0, 1),
(102, 'Main_reports/eh_report/', 'Expense History Report', 'Summary of all expense transaction with all entries listed base on classification.', 2, 10, '2018-07-23 09:53:55', 0, 1),
(103, 'Main_reports/salesandcollectionreport/', 'Sales and Collection Report', 'Summary of all Sales and Collection List Report transaction.', 3, 10, '2018-07-23 09:54:50', 0, 1),
(104, 'Main_reports/bd_report/', 'Bank Deposit Report', 'Summary of all bank deposit transactions.', 3, 10, '2018-07-23 09:55:08', 0, 1),
(105, 'Main_reports/cr_report/', 'Check Release Report', 'Summary of all Released Check.', 3, 10, '2018-07-23 09:55:30', 0, 1),
(106, 'Main_reports/pcr_report/', 'Petty Cash Reimbursement Report', 'Summary of all transaction using petty cash fund.', 3, 10, '2018-07-23 09:55:55', 0, 1),
(107, 'Main_reports/pcto_report/', 'Petty Cash Turn Over Report', 'Petty cash turn over and breakdown of transaction.', 3, 10, '2018-07-23 09:55:55', 0, 1),
(108, 'Main_reports/collection_report/', 'Collection Report', 'Summary of all Collections.', 3, 10, '2018-07-23 10:11:42', 0, 1),
(109, 'Main_reports/cash_voucher_summary/', 'Cash Voucher Summary', 'Summary of all cash voucher transaction.', 3, 10, '2018-07-23 10:12:11', 0, 1),
(110, 'Main_reports/pcrep_report/', 'Petty Cash Replenishment Report', 'Petty cash replenishment daily summary.', 3, 10, '2018-07-23 10:12:53', 0, 1),
(111, 'Main_reports/icr_by_category/', 'Inventory Consumption Report (By Category)', 'Summary of all inventory consumption (By Category).', 4, 10, '2018-07-23 10:13:27', 0, 1),
(112, 'Main_reports/icr_by_Item/', 'Inventory Consumption Report (By Item)', 'Summary of all inventory consumption (By Item).', 4, 10, '2018-07-23 10:14:07', 0, 1),
(113, 'Main_reports/ie_report/', 'Inventory Ending Report', 'Summary of all inventory ending count.', 4, 10, '2018-07-23 10:14:25', 0, 1),
(114, 'Main_reports/ris_report/', 'Received Items Summary Report', 'Summary of all Received Items details.', 4, 10, '2018-07-23 10:14:43', 0, 1),
(115, 'Main_reports/it_report/', 'Inventory Transaction Report', 'Summary of all inventory transaction history. From the sales, purchases, location transfer, returns, production and spoilages.', 4, 10, '2018-07-23 10:15:43', 0, 1),
(116, 'Main_reports/mi_report/', 'Monthly Inventory Report as of (Rundate)', 'Summary of monthly inventory history. From the sales, purchases, location transfer, returns, production and spoilages.', 4, 10, '2018-07-23 10:16:05', 0, 1),
(117, 'Main_reports/po_listing_report/', 'PO Listing Report', 'Summary of all purchase order transaction.', 5, 10, '2018-07-23 10:17:17', 0, 1),
(118, 'Main_reports/po_adjustment_report/', 'PO Adjustment Report', 'Summary of purchase order transaction with adjustment for payment.', 5, 10, '2018-07-23 10:17:38', 0, 1),
(119, 'Main_reports/po_receive_report/', 'Purchase Order Receive Report', 'Summary of all purchase order transaction that has been received', 5, 10, '2018-07-23 10:17:56', 0, 1),
(120, 'Main_reports/purchases_payable_report/', 'Purchases Payable Report', 'Summary of purchase order transaction with purchase order receipt for payment.', 5, 10, '2018-07-23 10:18:11', 0, 1),
(121, 'Main_reports/po_payable_report/', 'Payable Report (with Purchase Order)', 'Summary of all transaction using petty cash fund.', 5, 10, '2018-07-23 10:18:34', 0, 1),
(122, 'Main_reports/cb_report/', 'Customer Balances Report', 'Summary of all distributor account that has balances for payment.', 6, 10, '2018-07-23 10:18:51', 0, 1),
(123, 'Main_reports/dr_listing_report/', 'DR Listing Report', 'Summary of all Delivery Receipt transaction.', 6, 10, '2018-07-23 10:19:12', 0, 1),
(124, 'Main_reports/drlbc_report/', 'DR Listing Report (by Warehouse)', 'Summary of all Delivery Receipt Listing transaction filter by warehouse.', 6, 10, '2018-07-23 10:19:27', 0, 1),
(125, 'Main_reports/srb_customer/', 'Sales Report by Franchise', 'Summary of all sales report by franchise.', 6, 10, '2018-07-23 10:19:41', 0, 1),
(126, 'Main_reports/srl_report/', 'Sales Return Listing Report', 'Summary of all Sales Return transaction.', 6, 10, '2018-07-23 10:20:00', 0, 1),
(127, 'Main_sales/sales_invoice_collection/', 'Sales Invoice Collection', 'New sales invoice collection/payment transaction for service.', 1, 2, '2018-07-26 07:56:10', 0, 0),
(128, 'Main_sales/sales_invoice/', 'Sales Invoice', 'List of Sales Invoice', 1, 2, '2018-07-27 12:45:03', 5, 1),
(129, 'Main_sales/sales_invoicetran/', 'Sales Invoice Transaction History', 'Summary of all Sales Invoice Transaction', 1, 2, '2018-07-27 12:50:58', 6, 1),
(131, 'Main_dev_settings/content_navigation/', 'Content Navigation', 'New Content Navigation and option to manage each record', 1, 9, '2018-08-26 18:52:36', 0, 1),
(132, 'Main_dev_settings/company_manager/', 'Company Manager', 'View Company records and option to add and manage each record.', 0, 12, '2018-12-03 00:00:00', 0, 1),
(133, 'Main_settings/shipping/', 'Shipping', 'New Shipping and option to manage each record.', 0, 8, '2018-12-14 00:00:00', 0, 1),
(134, 'Main_settings/tax_settings/', 'Tax', 'New Tax and option to manage each record.', 0, 8, '2018-07-13 00:00:00', 0, 1),
(135, 'document_tracker_list/', 'Document Tracker List', 'Monitoring and tracking of documents', 0, 13, '2019-07-16 23:27:06', 0, 1),
(136, 'document_tracker_teams/', 'Document Tracker Teams', 'List of users who are assigned to differenct document category', 0, 8, '2019-07-16 23:29:06', 0, 1),
(137, 'document_tracker_categories/', 'Document Tracker Categories', 'List of categories for received documents', 0, 8, '2019-07-18 11:25:40', 0, 1),
(138, 'document_tracker_configuration/', 'Document Tracker Configuration', 'Standards of Documents received.', 0, 8, '2019-07-19 10:47:40', 0, 1),
(139, 'upload_sales_data/', 'Upload Sales Data', 'Upload sales data excel file to the system', 1, 2, '2019-09-17 11:33:57', 0, 1),
(140, 'Main_dev_settings/access_control/', 'Access Control', 'Control the access of different positions.', 0, 12, '2018-09-08 05:12:00', 0, 0),
(141, 'document_tracker_list_categories/', 'Document Tracker List - Categories', 'List of Documents per Category', 0, 13, '2019-09-04 17:00:00', 0, 1),
(142, 'document_tracker_list_teams/', 'Document Tracker List - Teams', 'List of Documents per Tracker Team', 0, 13, '2019-09-04 17:00:00', 0, 1),
(143, 'currency/', 'Currency', 'New currency and option to manage each record.', 1, 8, '2019-10-01 19:43:11', 0, 1),
(144, 'Main_settings/discounts/', 'Discounts', 'New Discount and option to manage each record.', 1, 8, '2019-10-08 13:22:49', 0, 1),
(146, '30498053/inventory_list/', 'Inventory List QCC', 'List of all inventory and option to add/edit/delete each record.', 0, 4, '2018-07-21 20:26:34', 0, 1),
(147, '30498053/customer/', 'Customer QCC', 'Manage customer information.', 0, 5, '2018-07-22 22:01:51', 0, 1),
(148, '30498053/sales_order_form/', 'Job Order', 'New job order transaction.', 0, 2, '2018-07-20 07:25:08', 1, 1),
(149, '30498053/sales_summary/', 'Job Order Transaction History', 'Summary of all job order transaction.', 0, 2, '2018-07-20 07:34:19', 2, 1),
(150, '30498053/sales_dr/', 'Delivery Receipt QCC', 'New Delivery Receipt Order.', 0, 2, '2018-07-20 07:35:15', 3, 1),
(151, '30498053/sales_drtran/', 'Delivery Receipt Transaction History QCC', 'Summary of all Delivery Receipt Order transaction.', 0, 2, '2018-07-20 07:36:20', 4, 1),
(152, '30498053/sales_invoice/', 'Sales Invoice QCC', 'List of Sales Invoice', 1, 2, '2018-07-27 12:45:03', 5, 1),
(153, '30498053/sales_invoicetran/', 'Sales Invoice Transaction History QCC', 'Summary of all Sales Invoice Transaction', 1, 2, '2018-07-27 12:50:58', 6, 1),
(154, '262834490/sales_order_form/', 'Quotation (Presidium PH)', 'New quotation transaction.', 0, 2, '2018-07-20 07:25:08', 1, 2),
(155, '262834490/sales_summary/', 'Quotation Transaction History (Presidium PH)', 'Summary of all quotation transaction.', 0, 2, '2018-07-20 07:25:08', 1, 2),
(156, '262834490/sales_dr/', 'Statement of Account (Presidium PH)', 'Convert Quotation into SOA.', 0, 2, '2018-07-20 07:25:08', 1, 0),
(157, '262834490/sales_drtran/', 'Statement of Account Transaction History (Presidium PH)', 'Summary of all SOA.', 0, 2, '2018-07-20 07:25:08', 1, 2),
(158, '262834490/sales_invoice/', 'Sales Invoice (Presidium PH)', 'List of Sales Invoice', 1, 2, '2018-07-27 12:45:03', 5, 0),
(159, '262834490/sales_invoicetran/', 'Sales Invoice Transaction History (Presidium PH)', 'Summary of all Sales Invoice Transaction', 1, 2, '2018-07-27 12:50:58', 6, 1),
(160, '262834490/soexpenses_summary/', 'Sales Order with Expenses (Presidium PH)', 'Sales order with expenses transaction.', 0, 2, '2018-07-20 07:25:08', 1, 1),
(161, '262834490/account_receivables_home/', 'Accounts Receivable Report for All Customers', 'Accounts Receivable Report for All Customers', 3, 10, '2020-03-24 20:46:23', 0, 1),
(162, '262834490/profit_and_loss_report/', 'Profit and Loss Report', 'Profit and Loss Report', 3, 10, '2020-03-18 20:02:00', 1, 1),
(163, '262834490/gl_accounts/', 'GL Accounts (Presidium PH)', 'New GL Accounts and option to manage each record.', 0, 8, '2018-07-17 00:00:00', 0, 0),
(164, 'Main_reports/incomestatement/', 'Income Statement', 'Income Statement', 3, 10, '2018-07-23 10:11:42', 0, 1),
(165, 'Main_reports/incomestatement/', 'Income Statement', 'Income Statement', 3, 10, '2018-07-23 10:11:42', 0, 1),
(166, '268230835/inventory_list/', 'Inventory List (First Fil-Bio)', 'List of all inventory and option to add/edit/delete each record.', 0, 4, '2018-07-21 20:26:34', 0, 1),
(167, '268230835/sales_order_form/', 'Sales Order (First Fil-Bio)', 'New sales order transaction.', 0, 2, '2018-07-20 07:25:08', 1, 1),
(168, '268230835/sales_summary/', 'Sales Order Transaction History (First Fil-Bio)', 'Summary of all repeat order transaction.', 0, 2, '2018-07-20 07:34:19', 2, 1),
(169, 'Main_settings/commission_supplier/', 'Commission per Supplier Product', 'Set percentage of commission per supplier', 0, 8, '2018-07-13 00:00:00', 0, 1),
(170, 'Main_settings/targetsales_agent/', 'Target Sales per Agent', 'Set target sales per agent', 0, 8, '2018-07-13 00:00:00', 0, 1),
(171, '268230835/entity_salesagent/', 'Sales Agent (First Fil-Bio)', 'Update Sales Agent.', 0, 5, '2018-07-22 22:07:53', 0, 1),
(174, '268230835/sales_dr/', 'Delivery Receipt (First Fil-Bio)', 'New Delivery Receipt Order.', 0, 2, '2018-07-20 07:35:15', 3, 1),
(175, '268230835/sales_drtran/', 'Delivery Receipt Transaction History (First Fil-Bio)', 'Summary of all Delivery Receipt Order transaction.', 0, 2, '2018-07-20 07:36:20', 4, 1),
(176, '268230835/sales_invoice/', 'Sales Invoice (First Fil-Bio)', 'List of Sales Invoice', 1, 2, '2018-07-27 12:45:03', 5, 1),
(177, '268230835/sales_invoicetran/', 'Sales Invoice Transaction History (First Fil-Bio)', 'Summary of all Sales Invoice Transaction', 1, 2, '2018-07-27 12:50:58', 6, 1),
(178, 'Main_reports/commission_report/', 'Commission Report', 'Summary of all Commission.', 3, 10, '2018-07-23 10:11:42', 0, 1),
(179, '268230835/collection_report_ffb/', 'Collection Report (First Fil-Bio)', 'Summary of all Delivery Receipt transaction.', 6, 10, '2018-07-23 10:19:12', 0, 1),
(180, '268230835/sales_register_report/', 'Sales Register Report (First Fil-Bio)', 'Summary of all Sales Order transaction per customer.', 6, 10, '2018-07-23 10:19:12', 0, 1),
(186, '268230835/entity_supplierlist/', 'Supplier (First Fil-Bio)', 'List of all suppliers and option to add/edit/delete each record.', 0, 5, '2018-07-22 22:01:24', 0, 1),
(187, '268230835/customer/', 'Customer (First Fil-Bio)', 'Manage customer information.', 0, 5, '2018-07-22 22:01:51', -1, 1),
(188, '268230835/sales_collection/', 'Collection (First Fil-Bio)', 'New collection/payment transaction.', 0, 2, '2018-07-20 08:57:43', 7, 1),
(189, '268230835/collection_summary/', 'Collection Transaction History (First Fil-Bio)', 'Summary of all collection transaction.', 0, 2, '2018-07-20 09:14:15', 8, 0),
(190, 'Main_products/products/', 'Products', 'Manage a products', 0, 3, '2020-06-18 00:00:00', 1, 1),
(191, 'Main_products/sample_cms/', 'Sample CMS', 'Manage a cms', 0, 3, '2020-06-18 00:00:00', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cp_content_navigation`
--
ALTER TABLE `cp_content_navigation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cp_content_navigation`
--
ALTER TABLE `cp_content_navigation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
