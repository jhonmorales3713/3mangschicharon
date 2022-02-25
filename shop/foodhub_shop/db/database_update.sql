### JUNE 25, 2020 ### => DONE
ALTER TABLE `sys_customer_auth` ADD `last_failed_attempt` DATETIME NULL DEFAULT NULL AFTER `failed_login_attempts`;
CREATE TABLE `sys_customer_auth_audittrail` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `action` ENUM('login','logout','fb_login','gmail_login') NOT NULL , `created` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `sys_customer_auth` ADD `fb_login` INT NOT NULL DEFAULT '0' AFTER `last_failed_attempt`, ADD `gmail_login` INT NOT NULL DEFAULT '0' AFTER `fb_login`;

### JUNE 26, 2020 ### => DONE
CREATE TABLE `app_customer_addresses` ( `id` INT NOT NULL AUTO_INCREMENT , `customer_id` INT NOT NULL , `receiver_name` VARCHAR(155) NOT NULL , `receiver_contact` VARCHAR(11) NOT NULL , `address` TEXT NOT NULL , `landmark` TEXT NOT NULL , `region_id` VARCHAR(10) NOT NULL , `province_id` VARCHAR(10) NOT NULL , `municipality_id` VARCHAR(10) NOT NULL , `brgy_id` VARCHAR(10) NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `default_add` INT NOT NULL DEFAULT '1' , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `app_customer_addresses` ADD `postal_code` INT NOT NULL AFTER `landmark`;

### JULY 2, 2020 ### => DONE [admin]
UPDATE `cp_main_navigation` SET `arrangement` = '9' WHERE `cp_main_navigation`.`main_nav_id` = 8; [not run]
CREATE TABLE `sys_shops_wallet` ( `id` INT NOT NULL AUTO_INCREMENT , `shopid` INT NOT NULL , `refnum` VARCHAR(155) NOT NULL , `balance` DOUBLE NOT NULL DEFAULT '0' , `remarks` TEXT NOT NULL DEFAULT '' , `status` ENUM('confirmed') NOT NULL DEFAULT 'confirmed' , `deposit_date` DATE NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `sys_shops_wallet_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `shopid` INT NOT NULL , `refnum` VARCHAR(155) NOT NULL , `logs_date` DATE NOT NULL , `logs_type` ENUM('cash','check','online_banking','wallet') NOT NULL , `amount` DOUBLE NOT NULL DEFAULT '0', `balance` DOUBLE NOT NULL DEFAULT '0' , `type` ENUM('plus','minus') NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `sys_payment_type` (`id`, `paycode`, `description`, `created`, `updated`, `status`) VALUES (NULL, 'WALLET', 'Wallet', '2020-07-04 14:16:00', '2020-07-04 14:28:40', '1');

### JULY 7, 2020 ### => DONE
CREATE TABLE `sys_billing_branch` ( `id` INT NOT NULL AUTO_INCREMENT , `billno` INT NOT NULL , `billcode` VARCHAR(100) NOT NULL , `syshop` INT NOT NULL , `branchid` INT NOT NULL , `transdate` DATETIME NOT NULL , `totalamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `remarks` VARCHAR(255) NOT NULL , `processdate` DATETIME NOT NULL , `dateupdated` DATETIME NOT NULL , `ratetype` VARCHAR(10) NOT NULL , `processrate` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `processfee` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `netamount` DECIMAL(11,2) NOT NULL DEFAULT '0.00' , `status` TINYINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### OCT 14, 2020 ### => DONE
ALTER TABLE `app_order_details` ADD `latitude` VARCHAR(300) NULL DEFAULT NULL AFTER `brgyCode`, ADD `longitude` VARCHAR(300) NULL DEFAULT NULL AFTER `latitude`;
ALTER TABLE `app_sales_order_details` ADD `latitiude` VARCHAR(300) NULL DEFAULT NULL AFTER `brgyCode`, ADD `longitude` VARCHAR(300) NULL DEFAULT NULL AFTER `latitiude`;

### OCT 20, 2020 ### => DONE
CREATE TABLE `api_referral_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `refnum` VARCHAR(255) NOT NULL , `response` TEXT NULL , `date_created` DATE NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `api_jc_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `refnum` VARCHAR(255) NOT NULL , `response` TEXT NULL , `date_created` DATE NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `api_jcww_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `refnum` VARCHAR(255) NOT NULL , `response` TEXT NULL , `date_created` DATE NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `app_postback_error_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `text` TEXT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `api_jcfulfillment_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `refnum` VARCHAR(255) NOT NULL , `response` TEXT NULL , `type` ENUM('checkout','postback') NOT NULL , `date_created` DATE NOT NULL , `create_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### OCT 22, 2020 ### => DONE
CREATE TABLE `app_postback_process_order` ( `refnum` VARCHAR(255) NOT NULL , `referral_status` INT NOT NULL DEFAULT '500' , `voucher_status` INT NOT NULL DEFAULT '500' , `split_order_status` INT NOT NULL DEFAULT '500' , `shop_order_failed` TEXT NOT NULL DEFAULT '' , `jc_api_status` INT NOT NULL DEFAULT '500' , `jcww_api_status` INT NOT NULL DEFAULT '500' , `branch_sms_status` INT NOT NULL DEFAULT '404' , `branch_email_status` INT NOT NULL DEFAULT '404' , `seller_sms_status` INT NOT NULL DEFAULT '404' , `seller_email_status` INT NOT NULL DEFAULT '500' , `client_email_status` INT NOT NULL DEFAULT '500' , `update_order_status` INT NOT NULL DEFAULT '500' , `jc_fulfillment_status` INT NOT NULL DEFAULT '500' , `success` INT NOT NULL DEFAULT '500' , `created_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`refnum`)) ENGINE = InnoDB;

### OCT 26, 2020 ### => DONE
CREATE TABLE `app_order_branch_details` ( `id` INT NOT NULL AUTO_INCREMENT , `shopid` INT NOT NULL , `branchid` INT NOT NULL COMMENT '0 = main shop' , `order_refnum` VARCHAR(255) NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `app_order_details` ADD `payment_option` VARCHAR(255) NULL AFTER `payment_method`;

### NOV 19, 2020 ### => DONE
ALTER TABLE `cs_clients_info` ADD `c_cpshop_api_url` TEXT NOT NULL DEFAULT '' AFTER `c_jcfulfillment_shopidno`;

### NOV 24, 2020 ### => DONE
ALTER TABLE `cs_utilities` ADD `c_allowed_jcfulfillment_prefix` VARCHAR(255) NOT NULL DEFAULT '' AFTER `c_paypanda_link_test`;
ALTER TABLE `cs_clients_info` ADD `c_allow_voucher` TINYINT NOT NULL DEFAULT '0' AFTER `c_cpshop_api_url`;

### DEC 2, 2020 ### => DONE
ALTER TABLE `cs_clients_info` ADD `c_allow_toktok_shipping` INT NOT NULL DEFAULT '0' AFTER `c_allow_voucher`;
CREATE TABLE `api_toktok_shipping_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `refnum` VARCHAR(255) NOT NULL , `response` TEXT NOT NULL , `date_created` DATE NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `api_toktok_shipping_logs` ADD `shopid` INT NOT NULL AFTER `response`;

### DEC 10, 2020 ### => DONE
ALTER TABLE `sys_customer_auth` ADD `seller_login` INT NOT NULL DEFAULT '0' AFTER `gmail_login`;

### MARCH 09, 2021 ### => DONE
ALTER TABLE `cs_clients_info` ADD `c_allowed_unfulfilled_orders` INT NOT NULL DEFAULT '0' AFTER `c_s3bucket_link_live`;
CREATE TABLE `sys_unfulfilled_settings` ( `id` INT NOT NULL AUTO_INCREMENT , `shopid` INT NOT NULL , `branchid` INT NOT NULL , `regcode` INT NOT NULL , `provcode` INT NOT NULL , `citymuncode` INT NOT NULL , `allowed_unfulfilled` INT NOT NULL , `date_created` DATETIME NOT NULL , `date_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `sys_shops` ADD `check_unfulfilled_orders` INT NOT NULL DEFAULT '0' AFTER `inv_threshold`;
ALTER TABLE `sys_shops` ADD `allowed_unfulfilled` INT NOT NULL DEFAULT '5' AFTER `inv_threshold`;

### MARCH 12, 2021 ### DONE
ALTER TABLE `sys_branch_profile` ADD `pending_orders` INT NOT NULL DEFAULT '0' AFTER `inv_threshold`, ADD `last_ordered` DATETIME NOT NULL AFTER `pending_orders`;

### APRIL 12, 2021 ### DONE
ALTER TABLE `app_sales_order_details` ADD `date_assigned` DATETIME NULL DEFAULT NULL AFTER `payment_date`;

### MAY 07, 2021 ### DONE
ALTER TABLE `sys_branch_profile` ADD `on_hold` TINYINT NOT NULL DEFAULT '0' AFTER `last_ordered`;

### JUNE 11, 2021 ### PENDING
ALTER TABLE `cs_clients_info` ADD `c_order_threshold` DOUBLE NOT NULL DEFAULT '0.30' AFTER `c_inv_threshold`;
