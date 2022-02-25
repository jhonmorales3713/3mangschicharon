### JULY 08, 2020 ### => PENDING
UPDATE cp_content_navigation SET status = 0 WHERE cn_fkey = 10
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'claimed_vouchers/index/', 'Claimed Vouchers', 'View claimed vouchers', '1', '10', '2018-07-27 12:50:58', '1', '1')
INSERT INTO `cp_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Wallet', 'fa-money', 'wallet_home', 'acb_wallet', 'cb_wallet', '9', '2020-07-02 00:00:00', '2020-07-02 00:00:00', '1');
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'prepayment/index/', 'Pre Payment', 'Manage all pre payment', '1', '11', '2018-07-27 12:50:58', '1', '1');
UPDATE cp_main_navigation SET main_nav_href = 'accounts_home', attr_val = 'acb_accounts', attr_val_edit = 'cb_accounts' WHERE main_nav_desc = 'Accounts'
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'billing/index/', 'Billing', 'Manage billing and remittance', '1', '6', '2018-07-27 12:50:58', '1', '1');
UPDATE `cp_main_navigation` SET `arrangement` = '10' WHERE `cp_main_navigation`.`main_nav_id` = 8;
CREATE TABLE `sys_billing_branch` ( `id` INT NOT NULL AUTO_INCREMENT , `billno` INT NOT NULL , `billcode` VARCHAR(100) NOT NULL , `syshop` INT NOT NULL , `branchid` INT NOT NULL , `transdate` DATETIME NOT NULL , `totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `remarks` VARCHAR(255) NOT NULL , `processdate` DATETIME NOT NULL , `dateupdated` DATETIME NOT NULL , `ratetype` VARCHAR(10) NOT NULL , `processrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `processfee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `netamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `status` TINYINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


### JULY 16, 2020 ### => DONE
ALTER TABLE `app_sales_order_details` ADD `payment_portal_fee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `payment_method`;
ALTER TABLE `app_order_details` ADD `payment_portal_fee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `payment_method`;
CREATE TABLE `sys_billing_government` ( `id` INT NOT NULL AUTO_INCREMENT , `billno` INT NOT NULL , `billcode` VARCHAR(100) NOT NULL , `syshop` INT NOT NULL , `trandate` DATETIME NOT NULL , `totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `remarks` VARCHAR(255) NOT NULL , `processdate` DATETIME NOT NULL , `dateupdated` DATETIME NOT NULL , `portal_fee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `netamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `paystatus` VARCHAR(255) NOT NULL DEFAULT 'On Process' , `paiddate` DATETIME NOT NULL DEFAULT '0000-00-00' , `paidamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `paytype` INT NOT NULL DEFAULT '0' , `payref` VARCHAR(100) NOT NULL DEFAULT '---' , `payattach` VARCHAR(255) NULL , `payremarks` VARCHAR(255) NULL , `status` TINYINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `sys_billing_branch_government` (
  `id` int(11) NOT NULL,
  `billno` int(11) NOT NULL,
  `billcode` varchar(100) NOT NULL,
  `syshop` int(11) NOT NULL,
  `branchid` int(11) NOT NULL,
  `transdate` datetime NOT NULL,
  `totalamount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(255) NOT NULL,
  `processdate` datetime NOT NULL,
  `dateupdated` datetime NOT NULL,
  `portal_fee` decimal(11,2) NOT NULL DEFAULT '0.00',
  `netamount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'billing/government/', 'Billing (By Payment Portal Fee)', 'Manage billing and remittance', '1', '6', '2018-07-27 12:50:58', '1', '1');

### JULY 21, 2020 ### => DONE
CREATE TABLE `sys_billing_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `billing_id` INT NOT NULL , `sys_shop` INT NOT NULL , `product_id` VARCHAR(255) NOT NULL , `order_id` INT NOT NULL , `trandate` DATETIME NOT NULL , `totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `price` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `quantity` INT NOT NULL , `ratetype` VARCHAR(10) NOT NULL COMMENT 'f = fix, p = percentage' , `processrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `processfee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `netamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `status` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `sys_billing_branch_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `billing_id` INT NOT NULL , `sys_shop` INT NOT NULL , `product_id` VARCHAR(255) NOT NULL , `order_id` INT NOT NULL , `branch_id` INT NOT NULL , `trandate` DATETIME NOT NULL , `totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `price` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `quantity` INT NOT NULL , `ratetype` VARCHAR(10) NOT NULL COMMENT 'f = fix, p = percentage' , `processrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `processfee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `netamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `status` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### JULY 22, 2020 ### => DONE
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'manual_order/index/', 'Manual Order', 'Manage all pre payment manual order', '1', '11', '2018-07-27 12:50:58', '1', '1');
INSERT INTO `sys_payment_type` (`id`, `paycode`, `description`, `created`, `updated`, `status`) VALUES (NULL, 'PREPAYMENT', 'Prepayment', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1');

### JULY 27, 2020 ### => DONE
ALTER TABLE `sys_shops` ADD `billing_type` TINYINT NOT NULL DEFAULT '1' AFTER `shippingfee`;
CREATE TABLE `sys_cron_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `cron_name` VARCHAR(255) NOT NULL , `cron_desc` TEXT NULL , `cron_start` TIMESTAMP NOT NULL , `cron_end` TIMESTAMP NOT NULL , `cron_status` ENUM('attempted','successful') NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `sys_billing` ADD `delivery_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `trandate`;
ALTER TABLE `sys_billing_government` ADD `delivery_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `trandate`;
ALTER TABLE `sys_billing_branch_government` ADD PRIMARY KEY(`id`);
ALTER TABLE `sys_billing_branch_government` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

### AUG 3, 2020 ### => DONE
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'sale_settlement/index/', 'Sales Settlement Report', 'Summary of sales settlement report for each merchant', '0', '7', '2018-07-13 00:00:00', '0', '1');
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'sales_report/index/', 'Sales Report', 'Summary of sales transactions', '0', '7', '2018-07-13 00:00:00', '0', '1');
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'page_statistics/index/', 'Page Statistics', 'Summary of total page views, and visitors', '0', '7', '2018-07-13 00:00:00', '0', '1');
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'product_orders_report/index/', 'Product Orders Report', 'Summary of all products ordered', '0', '7', '2018-07-13 00:00:00', '0', '1');
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'profit_sharing_report/index/', 'Profit Sharing Report', 'Summary of profit shares from sales transactions generated thru your referral code', '0', '7', '2018-07-13 00:00:00', '0', '1');
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'payout_report/index/', 'Payout Report', 'Summary of weekly payout for your profit shares', '0', '7', '2018-07-13 00:00:00', '0', '1');

### AUG 11, 2020 ### => DONE
ALTER TABLE `sys_shops_wallet_logs` ADD `deposit_ref_num` VARCHAR(255) NOT NULL AFTER `refnum`;
ALTER TABLE `sys_shops_wallet_logs` CHANGE `logs_type` `logs_type` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `sys_shops_wallet_logs` ADD `tran_ref_num` VARCHAR(255) NOT NULL DEFAULT '' AFTER `refnum`;
ALTER TABLE `sys_shops_wallet_logs` ADD `attachment` TEXT NOT NULL DEFAULT '' AFTER `deposit_ref_num`;
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reissue_voucher_request/index/', 'Reissue Voucher Request', 'List of all vouchers that can be requested for reissuing', '0', '10', '2018-07-13 00:00:00', '0', '1');
CREATE TABLE `toktokmall_vouchers`.`v_wallet_reissue` ( `id` INT NOT NULL AUTO_INCREMENT , `shopid` VARCHAR(255) NOT NULL , `shopcode` VARCHAR(255) NOT NULL , `vrefno` VARCHAR(255) NOT NULL COMMENT 'unique' , `vcode` VARCHAR(255) NOT NULL , `vamount` DOUBLE NOT NULL , `date_issued` DATETIME NOT NULL , `date_valid` DATETIME NOT NULL , `date_used` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' , `use_branchid` VARCHAR(255) NOT NULL , `use_branchcode` VARCHAR(255) NOT NULL , `use_orderref` VARCHAR(255) NOT NULL , `use_in` VARCHAR(255) NOT NULL , `username` VARCHAR(255) NOT NULL , `others` TEXT NULL DEFAULT NULL , `status` INT NOT NULL DEFAULT '1' , `claim_status` TINYINT NOT NULL DEFAULT '4' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### AUG 26, 2020 ### => DONE
ALTER TABLE `cs_clients_info` ADD `c_allow_cod` INT NOT NULL DEFAULT '0' COMMENT '1 = ON | 0 = OFF' AFTER `c_allow_sms`;

### SEPT 08, 2020 ### => DONE
ALTER TABLE `sys_branch_profile` ADD `latitude` VARCHAR(255) NOT NULL DEFAULT '0.000000' AFTER `region`, ADD `longitude` VARCHAR(255) NOT NULL DEFAULT '0.000000' AFTER `latitude`;
ALTER TABLE `cs_clients_info` ADD `c_allow_google_addr` TINYINT NOT NULL DEFAULT '0' AFTER `c_allow_cod`;

### SEPT 15, 2020 ### => DONE
ALTER TABLE `sys_billing_logs` ADD `branch_id` INT NOT NULL DEFAULT '0' AFTER `sys_shop`;
ALTER TABLE `sys_billing` ADD `branch_id` INT NOT NULL DEFAULT '0' AFTER `syshop`;
ALTER TABLE `sys_billing` ADD `per_branch_billing` INT NOT NULL DEFAULT '0' AFTER `branch_id`;

### OCT 19, 20202 ### => DONE
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'manual_cron/index/', 'Manual Cron', 'Trigger cron billing manually', '1', '9', '2018-07-27 12:50:58', '1', '1');

### OCT 29, 2020 ### => DONE
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'manual_order_list/index/', 'Manual Order List', 'Add order manually', '0', '2', '2018-07-27 12:50:58', '1', '1');
CREATE TABLE `app_manual_order_details` ( `id` INT NOT NULL AUTO_INCREMENT , `sys_shop` INT NOT NULL , `reference_num` VARCHAR(255) NOT NULL , `paypanda_ref` VARCHAR(255) NOT NULL , `user_id` VARCHAR(255) NOT NULL COMMENT 'd from jcwfp_users or idno for sk online' , `name` VARCHAR(255) NOT NULL , `conno` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `address` TEXT NOT NULL , `notes` TEXT NULL , `areaid` INT NOT NULL , `regCode` VARCHAR(255) NOT NULL DEFAULT '0' , `provCode` VARCHAR(255) NOT NULL DEFAULT '0' , `citymunCode` VARCHAR(255) NOT NULL DEFAULT '0' , `brgyCode` VARCHAR(255) NOT NULL DEFAULT '0' , `latitude` VARCHAR(300) NULL , `longitude` VARCHAR(300) NULL , `postalcode` VARCHAR(255) NOT NULL , `total_amount` DECIMAL(11,2) NOT NULL , `order_status` CHAR(3) NULL COMMENT ' p = pending order, po = processing order, rp = ready for pickup, bc = booking confirmed, f = fulfilled, s = shipped' , `payment_status` INT NOT NULL COMMENT '0 = pending, 1 = paid, 2 = unpaid' , `payment_method` VARCHAR(255) NOT NULL , `payment_portal_fee` DECIMAL(11,2) NULL , `delivery_signature` VARCHAR(255) NULL , `reason` VARCHAR(255) NULL , `delivery_id` TINYINT NOT NULL , `delivery_info` VARCHAR(255) NULL , `delivery_ref_num` VARCHAR(255) NULL , `delivery_amount` DECIMAL(11,2) NULL , `delivery_notes` TEXT NOT NULL , `delivery_imgurl` TEXT NULL , `payment_id` TINYINT NOT NULL , `payment_amount` DECIMAL(11,2) NOT NULL , `payment_notes` TEXT NULL , `payment_date` DATETIME NOT NULL , `date_ordered` DATETIME NOT NULL , `date_order_processed` DATETIME NOT NULL , `date_ready_pickup` DATETIME NOT NULL , `date_booking_confirmed` DATETIME NOT NULL , `date_fulfilled` DATETIME NOT NULL , `date_returntosender` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `date_redeliver` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `date_shipped` DATETIME NOT NULL , `date_received` DATETIME NOT NULL , `t_deliveryId` VARCHAR(255) NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , `curcode` VARCHAR(100) NOT NULL DEFAULT 'PHP' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `app_manual_order_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `order_id` VARCHAR(255) NOT NULL COMMENT 'id from app_sales_order_details' , `product_id` VARCHAR(255) NOT NULL , `quantity` INT NOT NULL , `amount` DECIMAL(11,2) NOT NULL , `total_amount` DECIMAL(11,2) NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , `curcode` VARCHAR(100) NOT NULL DEFAULT 'PHP' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `app_manual_order_details` ADD `branch_id` INT NOT NULL AFTER `sys_shop`;
CREATE TABLE `app_manual_orders_shipping` ( `id` INT NOT NULL AUTO_INCREMENT , `reference_num` VARCHAR(255) NOT NULL , `sys_shop` INT NOT NULL , `delivery_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `daystoship` INT NOT NULL DEFAULT '2' , `daystoship_to` INT NOT NULL , `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `status` TINYINT NOT NULL DEFAULT '1' , `curcode` VARCHAR(100) NOT NULL DEFAULT 'PHP' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `app_manual_order_details` ADD `admin_drno` VARCHAR(255) NULL DEFAULT NULL AFTER `postalcode`, ADD `admin_sono` VARCHAR(255) NULL DEFAULT NULL AFTER `admin_drno`;

### DEC 03, 2020 ### => DONE
ALTER TABLE `sys_billing` CHANGE `payattach` `payattach` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'payment pic attachment';

### JAN 18, 2020 ### => DONE
CREATE TABLE `sys_billing_processed` ( `billing_date` DATE NOT NULL , `is_processed` TINYINT NOT NULL DEFAULT '0' , `date_created` TIMESTAMP NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`billing_date`)) ENGINE = InnoDB;

### FEB 03, 2021 ### DONE
ALTER TABLE `app_order_details_shipping` ADD `converted_delivery_amount` DECIMAL(11,2) NULL AFTER `currency`;
ALTER TABLE `app_sales_order_details` ADD `total_amount_oc` DECIMAL(11,2) NULL AFTER `total_amount`;
ALTER TABLE `sys_billing` ADD `totalamount_oc` DECIMAL(11,2) NULL AFTER `payremarks`, ADD `processfee_oc` DECIMAL(11,2) NULL AFTER `totalamount_oc`, ADD `netamount_oc` DECIMAL(11,2) NULL AFTER `processfee_oc`, ADD `delivery_amount_oc` DECIMAL(11,2) NULL AFTER `netamount_oc`;
ALTER TABLE `sys_billing_logs` ADD `totalamount_oc` DECIMAL(11,2) NULL AFTER `netamount`, ADD `price_oc` DECIMAL(11,2) NULL AFTER `totalamount_oc`, ADD `processfee_oc` DECIMAL(11,2) NULL AFTER `price_oc`, ADD `netamount_oc` DECIMAL(11,2) NULL AFTER `processfee_oc`;
ALTER TABLE `sys_billing_branch` ADD `totalamount_oc` DECIMAL(11,2) NULL AFTER `payremarks`, ADD `processfee_oc` DECIMAL(11,2) NULL AFTER `totalamount_oc`, ADD `netamount_oc` DECIMAL(11,2) NULL AFTER `processfee_oc`;
ALTER TABLE `sys_billing_branch_logs` ADD `totalamount_oc` DECIMAL(11,2) NULL AFTER `netamount`, ADD `price_oc` DECIMAL(11,2) NULL AFTER `totalamount_oc`, ADD `processfee_oc` DECIMAL(11,2) NULL AFTER `price_oc`, ADD `netamount_oc` DECIMAL(11,2) NULL AFTER `processfee_oc`;
ALTER TABLE `sys_billing_branch_logs` ADD `exrate_n_to_php` DECIMAL(11,2) NULL AFTER `netamount_oc`, ADD `currency` VARCHAR(5) NULL AFTER `exrate_n_to_php`;
ALTER TABLE `sys_billing_logs` ADD `exrate_n_to_php` DECIMAL(11,2) NULL AFTER `netamount_oc`, ADD `currency` VARCHAR(5) NULL AFTER `exrate_n_to_php`;

### MARCH 27, 2021 ### DONE
-- need to review it first
ALTER TABLE `sys_billing` ADD `voucher_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `totalamount`;
ALTER TABLE `sys_billing_branch` ADD `voucher_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `totalamount`;
---------------------------------------------------------------
ALTER TABLE `app_sales_order_details` ADD `total_amount_w_voucher` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `total_amount`;


### MAY 17, 2021 ### DONE
ALTER TABLE `app_sales_order_logs` ADD `refcom_amount` DECIMAL(11,2) NOT NULL DEFAULT '0' AFTER `total_amount`, ADD `refcom_totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0' AFTER `refcom_amount`, ADD `refcom_rate` DOUBLE NOT NULL DEFAULT '0' AFTER `refcom_totalamount`;
ALTER TABLE `cs_clients_info` ADD `c_ofps` DOUBLE NOT NULL DEFAULT '0.50' AFTER `c_inv_threshold`;
ALTER TABLE `sys_billing` ADD `totalcomrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `totalamount`

### JUNE 1, 2021 ### DONE
ALTER TABLE `app_sales_order_logs` ADD `srp_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `quantity`;
ALTER TABLE `app_sales_order_details` ADD `srp_totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `postalcode`;
ALTER TABLE `sys_billing_logs` ADD `srp_totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `trandate`;
ALTER TABLE `sys_billing_logs` ADD `srp_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `srp_totalamount`;
ALTER TABLE `sys_billing_logs` ADD `comrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `processrate`;

### JUNE 22, 2021 ### DONE
ALTER TABLE `cs_clients_info` ADD `c_whtax_percentage` DOUBLE NOT NULL DEFAULT '.10' AFTER `c_max_width`;
ALTER TABLE `sys_billing` ADD `total_whtax` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `totalcomrate`;

### JULY 05, 2021 ### DONE
ALTER TABLE `sys_shops_wallet` ADD `branchid` INT NOT NULL DEFAULT '0' COMMENT '0 = main ' AFTER `shopid`;
ALTER TABLE `sys_shops_wallet_logs` ADD `branchid` INT NOT NULL DEFAULT '0' COMMENT '0 = main' AFTER `shopid`;
ALTER TABLE `cs_clients_info` ADD `c_accounting_email` VARCHAR(255) NOT NULL AFTER `c_email`;


### JULY 13, 2021 ### DONE
ALTER TABLE `sys_billing` ADD `paid_prepayment` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `paidamount`;
ALTER TABLE `sys_billing` ADD `remaining_to_pay` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `paid_prepayment`;
ALTER TABLE `sys_billing` ADD `prepaymentpaid_date` DATETIME NULL DEFAULT NULL AFTER `remaining_to_pay`;
ALTER TABLE `sys_billing` ADD `unsettled_payref` VARCHAR(100) NULL DEFAULT NULL AFTER `payref`;
ALTER TABLE `sys_billing` ADD `unsettled_payremarks` VARCHAR(255) NULL DEFAULT NULL AFTER `payremarks`;

### JULY 23, 2021 ### DONE
ALTER TABLE `sys_shops_wallet_logs` ADD `remarks` TEXT NOT NULL DEFAULT '' AFTER `attachment`;

### SEPT 23, 2021 ### DONE
INSERT INTO `cp_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'billing_merchant/index/', 'Billing Merchant', 'Manage billing and remittance', '1', '6', '2021-09-23 12:50:58', '1', '1');
CREATE TABLE `sys_billing_merchant` ( `id` INT NOT NULL AUTO_INCREMENT , `billno` INT NOT NULL , `billcode` VARCHAR(100) NOT NULL , `trandate` DATETIME NOT NULL , `shopid` INT NOT NULL , `branchid` INT NOT NULL , `processfee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `paystatus` VARCHAR(100) NOT NULL COMMENT 'On Process, Settled' , `paiddate` DATETIME NOT NULL , `paidamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `payref` VARCHAR(100) NOT NULL DEFAULT '---' , `payremarks` INT NULL , `payattach` TEXT NOT NULL , `paytype` INT NOT NULL DEFAULT '0' , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `sys_billing_merchant_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `shopid` INT NOT NULL , `branchid` INT NOT NULL , `productid` VARCHAR(255) NOT NULL , `orderid` INT NOT NULL , `trandate` DATETIME NOT NULL , `srp_amount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `srp_totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `quantity` INT NOT NULL , `processfee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `processrate` DOUBLE(11,2) NOT NULL DEFAULT '0.00' , `processtype` VARCHAR(10) NOT NULL COMMENT 'p = percentage, f = fix' , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `sys_billing_merchant` ADD `per_branch_billing` TINYINT NOT NULL AFTER `branchid`;
ALTER TABLE `sys_billing_merchant` ADD `processdate` DATETIME NOT NULL AFTER `processfee`;
ALTER TABLE `sys_billing_merchant` ADD `total_comrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `per_branch_billing`;
ALTER TABLE `sys_billing_merchant_logs` ADD `comrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `quantity`;
ALTER TABLE `app_order_details` ADD `riderPaymentMethod` VARCHAR(100) NULL COMMENT 'CASH, TOKTOKWALLET' AFTER `payment_method`;
ALTER TABLE `app_sales_order_details` ADD `riderPaymentMethod` VARCHAR(100) NULL COMMENT 'CASH, TOKTOKWALLET' AFTER `payment_method`;
ALTER TABLE `sys_billing_merchant` ADD `remarks` VARCHAR(255) NOT NULL DEFAULT '' AFTER `processdate`;

### NOV 24, 2021 ### PENDING
ALTER TABLE `sys_billing` ADD `deposit_date` DATE NULL DEFAULT NULL AFTER `paiddate`;

### JAN 05, 2021 ### PENDING TOTAL SALES CRON
CREATE TABLE `api_totalsales_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `reference_num` VARCHAR(100) NOT NULL , `data` TEXT NOT NULL , `response` TEXT NOT NULL , `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `date_updated` DATETIME NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `api_totalsales_logs` ADD `totalsales` DECIMAL(11,2) NOT NULL DEFAULT '0.00' AFTER `reference_num`;
