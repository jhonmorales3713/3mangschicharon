<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// to shorten your url route:

//toktokmall orders for merchant SRP amount
$route['Main_orders/orders_merchant/(:any)'] = 'orders/Main_orders_toktokmall/orders/$1';
$route['Main_orders/orders_view_merchant/(:any)/(:any)/(:any)'] = 'orders/Main_orders_toktokmall/orders_view/$1/$2/$3';
$route['Main_orders/print_order_merchant/(:any)/(:any)'] = 'orders/Main_orders_toktokmall/print_order/$1/$2';

// orders
$route['Main_orders/orders/(:any)'] = 'orders/Main_orders/orders/$1';
$route['Main_orders/pending_orders/(:any)'] = 'orders/Main_orders/pending_orders/$1';
$route['Main_orders/paid_orders/(:any)'] = 'orders/Main_orders/paid_orders/$1';
$route['Main_orders/readyforprocessing_orders/(:any)'] = 'orders/Main_orders/readyforprocessing_orders/$1';
$route['Main_orders/processing_orders/(:any)'] = 'orders/Main_orders/processing_orders/$1';
$route['Main_orders/readyforpickup_orders/(:any)'] = 'orders/Main_orders/readyforpickup_orders/$1';
$route['Main_orders/bookingconfirmed_orders/(:any)'] = 'orders/Main_orders/bookingconfirmed_orders/$1';
$route['Main_orders/fulfilled_orders/(:any)'] = 'orders/Main_orders/fulfilled_orders/$1';
$route['Main_orders/returntosender_orders/(:any)'] = 'orders/Main_orders/returntosender_orders/$1';
$route['Main_orders/shipped_orders/(:any)'] = 'orders/Main_orders/shipped_orders/$1';
$route['Main_orders/confirmed_orders/(:any)'] = 'orders/Main_orders/confirmed_orders/$1';
$route['Main_orders/voided_orders/(:any)'] = 'orders/Main_orders/voided_orders/$1';
$route['Main_orders/orders_view/(:any)/(:any)/(:any)'] = 'orders/Main_orders/orders_view/$1/$2/$3';
$route['Main_orders/print_order/(:any)/(:any)'] = 'orders/Main_orders/print_order/$1/$2';
$route['Main_orders/refund_order/(:any)'] = 'orders/Refund_order/index/$1';
$route['Main_orders/refund_approval/(:any)'] = 'orders/Refund_order/approval_index/$1';
$route['Main_orders/refund_approval/refund_order/view/(:any)/(:any)'] = 'orders/Refund_order/refund_order/$1/$2';
$route['Main_orders/refund_approval/refund_order/edit/(:any)/(:any)'] = 'orders/Refund_order/edit_refund_order/$1/$2';
$route['Main_orders/refund_order_transactions/(:any)'] = 'orders/Refund_order/transaction_index/$1';
$route['Main_orders/refund_order_transactions/refund_order/view/(:any)/(:any)'] = 'orders/Refund_order/refund_order/$1/$2';
$route['manual_order/index/(:any)'] = 'orders/Manual_order/index/$1';
$route['manual_order_list/index/(:any)'] = 'orders/Manual_order/index/$1';
$route['Main_orders/forpickup_orders/(:any)'] = 'orders/Main_orders/forpickup_orders/$1';
$route['Main_orders/orders_modify/(:any)/(:any)/(:any)'] = 'orders/Main_orders/orders_modify/$1/$2/$3';


// products
$route['Main_products/products/(:any)'] = 'products/Main_products/products/$1';
$route['Main_products/add_products/(:any)'] = 'products/Main_products/add_products/$1';
$route['Main_products/update_products/(:any)/(:any)'] = 'products/Main_products/update_products/$1/$2';
$route['Main_products/view_products/(:any)/(:any)'] = 'products/Main_products/view_products/$1/$2';
$route['Main_products/update_variants/(:any)/(:any)/(:any)'] = 'products/Main_products/update_variants/$1/$2/$3';
$route['Main_products/add_variant/(:any)/(:any)'] = 'products/Main_products/add_variant/$1/$2';
$route['Main_products/sample_cms/(:any)'] = 'products/Main_products/sample_cms/$1';
$route['automateUpdateProductImg'] = 'products/Main_products/automateUpdateProductImg';

// products waiting for approval
$route['Main_products/products_waiting_for_approval/(:any)'] = 'products/Products_approval/products_waiting_for_approval/$1';
$route['Products_approval/view_products_approval/(:any)/(:any)'] = 'products/Products_approval/view_products_approval/$1/$2';

// products declined
$route['Main_products/products_declined/(:any)'] = 'products/Products_approval/products_declined/$1';
$route['Products_approval/view_products_declined/(:any)/(:any)'] = 'products/Products_approval/view_products_declined/$1/$2';

// products approved
$route['Main_products/products_approved/(:any)'] = 'products/Products_approval/products_approved/$1';
$route['Products_approval/view_products_approved/(:any)/(:any)'] = 'products/Products_approval/view_products_approved/$1/$2';


// products verified
$route['Main_products/products_verified/(:any)'] = 'products/Products_approval/products_verified/$1';
$route['Products_approval/view_products_verified/(:any)/(:any)'] = 'products/Products_approval/view_products_verified/$1/$2';

// products main category
$route['Main_settings/product_main_category/(:any)'] = 'Main_settings/product_main_category/$1';


//settings
$route['Settings_shipping_delivery/shipping_delivery/(:any)'] = 'shipping_delivery/Settings_shipping_delivery/shipping_delivery/$1';
$route['Settings_shipping_delivery/general_list/(:any)'] = 'shipping_delivery/Settings_shipping_delivery/general_list/$1';
$route['Settings_shipping_delivery/general_rates/(:any)/(:any)'] = 'shipping_delivery/Settings_shipping_delivery/general_rates/$1/$2';
$route['Settings_shipping_delivery/custom_list/(:any)'] = 'shipping_delivery/Settings_shipping_delivery/custom_list/$1';
$route['Settings_shipping_delivery/custom_profile_list/(:any)/(:any)'] = 'shipping_delivery/Settings_shipping_delivery/custom_profile_list/$1/$2';
$route['Settings_shipping_delivery/custom_rates/(:any)/(:any)/(:any)'] = 'shipping_delivery/Settings_shipping_delivery/custom_rates/$1/$2/$3';
$route['Settings_void_record/void_record/(:any)'] = 'settings/void_record/Settings_void_record/void_record/$1';
$route['Settings_void_record/void_order/(:any)/(:any)'] = 'settings/void_record/Settings_void_record/void_order/$1/$2';
$route['Settings_void_record/void_record_list/(:any)'] = 'settings/void_record/Settings_void_record/void_record_list/$1';
$route['Settings_void_record/order_details_view/(:any)/(:any)/(:any)'] = 'settings/void_record/Settings_void_record/order_details_view/$1/$2/$3';

// developer settings
$route['Main_dev_settings/content_navigation/(:any)'] = 'developer_settings/Main_dev_settings/content_navigation/$1';
$route['Main_dev_settings/cron_logs/(:any)'] = 'developer_settings/Main_dev_settings/cron_logs/$1';
$route['Dev_settings/shop_utilities/(:any)'] = 'developer_settings/Dev_settings/shop_utilities/$1';
$route['Dev_settings_client_information/client_information/(:any)'] = 'developer_settings/Dev_settings_client_information/client_information/$1';
$route['Dev_settings_client_information/add_client_information/(:any)'] = 'developer_settings/Dev_settings_client_information/add_client_information/$1';
$route['Dev_settings_client_information/update_client_information/(:any)/(:any)'] = 'developer_settings/Dev_settings_client_information/edit_client_information/$1/$2';
$route['Dev_settings_audit_trail/audit_trail/(:any)'] = 'developer_settings/Dev_settings_audit_trail/audit_trail/$1';
$route['Dev_settings_pandabooks_api_logs/pandabooks_api_logs/(:any)'] = 'developer_settings/Dev_settings_pandabooks_api_logs/pandabooks_api_logs/$1';
$route['Dev_settings/postback_logs/(:any)'] = 'developer_settings/Dev_settings/postback_logs/$1';
$route['Dev_settings_maintenance_page/maintenance_page/(:any)'] = 'developer_settings/Dev_settings_maintenance_page/maintenance_page/$1';
$route['Dev_settings_maintenance_page/update_maintenance_page/(:any)/(:any)'] = 'developer_settings/Dev_settings_maintenance_page/edit_client_information/$1/$2';
$route['Shops/merchant_registration/(:any)'] = 'developer_settings/Shops_merchant_registration/merchant_registration/$1';
$route['Shops_merchant_registration/view_merchant_registration/(:any)/(:any)'] = 'developer_settings/Shops_merchant_registration/view_merchant_registration/$1/$2';
$route['manual_cron/index/(:any)'] = 'developer_settings/Manual_cron/index/$1';
$route['Dev_settings_api_request_postback_logs/api_request_postback_logs/(:any)'] = 'developer_settings/Dev_settings_api_request_postlog/api_request_postback_logs/$1';
$route['Dev_settings/email_settings/(:any)'] = 'developer_settings/Dev_settings_email_settings/email_settings/$1';

//Shops
$route['Shops/profile/(:any)'] = 'shops/Main_shops/shop_profile/$1';
$route['Shops/profile_list'] = 'shops/Main_shops/shops_profile_table';
$route['Shops/delete_shop'] = 'shops/Main_shops/delete_modal_confirm';
$route['Shops/disable_shop'] = 'shops/Main_shops/disable_modal_confirm';
$route['Shops/new/(:any)'] = 'shops/Main_shops/add_new/$1';
$route['Shops/save_shop'] = 'shops/Main_shops/save_shop';
$route['Shops/update/(:any)/(:any)'] = 'shops/Main_shops/update_record/$1/$2';
$route['Shops/update_shop'] = 'shops/Main_shops/update_shop';
$route['Shops/account/(:any)'] = 'shops/Main_shops/shop_account/$1';

// Shop Pop up image
$route['Shops/popup_image/(:any)'] = 'shops/Main_shops/shop_popup_image/$1'; // view
$route['Shops/save_popup_image'] = 'shops/Main_shops/save_popup_image'; // store and update

//SHOP BRANCH ROUTES
$route['Shopbranch/home/(:any)'] = 'shop_branch/Shop_branch/view/$1';
$route['Shopbranch/list'] = 'shop_branch/Shop_branch/shopbranch_list';
$route['Shopbranch/create/(:any)'] = 'shop_branch/Shop_branch/add/$1';
$route['Shopbranch/save'] = 'shop_branch/Shop_branch/save_shop_branch';
$route['Shopbranch/manage/(:any)/(:any)'] = 'shop_branch/Shop_branch/edit/$1/$2';
$route['Shopbranch/getrecorddetails'] = 'shop_branch/Shop_branch/get_record_details';
$route['Shopbranch/update'] = 'shop_branch/Shop_branch/update_shop_branch';
$route['Shopbranch/deactivate'] = 'shop_branch/Shop_branch/delete_modal_confirm';
$route['Shopbranch/shopbranches'] = 'shop_branch/Shop_branch/get_shop_branches';
$route['Shopbranch/getcityofregion'] = 'shop_branch/Shop_branch/get_city_of_region';
$route['Shopbranch/disable_branch'] = 'shop_branch/Shop_branch/disable_modal_confirm';
$route['Shopbranch/account/(:any)'] = 'shop_branch/Shop_branch/branch_account/$1';

//Vouchers
$route['claimed_vouchers/index/(:any)'] = 'vouchers/Claimed_vouchers/index/$1';
$route['reissue_voucher_request/index/(:any)'] = 'vouchers/Reissue_voucher_request/index/$1';


/// Voucher list
$route['voucher_list/index/(:any)'] = 'vouchers/List_vouchers/index/$1';
$route['voucher/add_vouchers/(:any)'] = '/vouchers/List_vouchers/add_voucher/$1';
$route['voucher/edit_vouchers/(:any)'] = '/vouchers/List_vouchers/edit_voucher/$1';
$route['voucher/delete_vouchers/(:any)'] = '/vouchers/List_vouchers/delete_voucher/$1';
$route['voucher/disable_voucher'] = '/vouchers/List_vouchers/disable_modal_confirm';


///Reclaimed Voucher List
$route['reclaimed_vouchers/index/(:any)'] = 'vouchers/Reclaimed_vouchers/reclaimed_vouchers/$1';


//Wallet
$route['prepayment/index/(:any)'] = 'wallet/Prepayment/index/$1';
$route['prepayment/get_branches'] = 'wallet/Prepayment/get_shop_branches';
$route['prepayment/export_logs'] = 'wallet/Prepayment/export_shop_wallet_logs';
$route['prepayment/get_shop_wallet_and_sales'] = 'wallet/Prepayment/get_shop_wallet_and_sales';
$route['manual_order/index/(:any)'] = 'wallet/Manual_order/index/$1';

//Accounts
$route['billing/index/(:any)'] = 'accounts/Billing/index/$1';
$route['billing_merchant/index/(:any)'] = 'accounts/Billing_merchant/index/$1';
$route['billing/index/(:any)/(:any)'] = 'accounts/Billing/index/$1/$2';
$route['billing_merchant/index/(:any)/(:any)'] = 'accounts/Billing_merchant/index/$1/$2';
$route['billing/delete_billing'] = 'accounts/Billing/delete_billing/$1';
$route['billing/government/(:any)'] = 'accounts/Billing/government/$1';
$route['billing/totalsales/(:any)/(:any)'] = 'accounts/Billing/process_billing_totalsales/$1/$2';

//Reports
$route['sale_settlement/index/(:any)'] = 'reports/sale_settlement2/index/$1';
$route['sales_report/index/(:any)'] = 'reports/sales_report/index/$1';
$route['reports/online_store_sessions/(:any)'] = 'reports/online_store_sessions/index/$1';
$route['product_orders_report/index/(:any)'] = 'reports/product_orders_report/index/$1';
$route['profit_sharing_report/index/(:any)'] = 'reports/profit_sharing_report/index/$1';
$route['payout_report/index/(:any)'] = 'reports/payout_report/index/$1';
$route['reports/average_order_value/(:any)'] = 'reports/Average_order_value/index/$1';
$route['reports/total_orders/(:any)'] = 'reports/Total_orders/index/$1';
$route['reports/order_and_sales/(:any)'] = 'reports/Order_and_sales/index/$1';
$route['reports/top_products_sold/(:any)'] = 'reports/Top_products_sold/index/$1';
$route['total_sales/index/(:any)'] = 'reports/total_sales/index/$1';
$route['revenue_by_store/index/(:any)'] = 'reports/revenue_by_store/index/$1';
$route['revenue_by_store/by_branch/(:any)'] = 'reports/revenue_by_store/rbb_index/$1';
$route['revenue_by_store/by_location/(:any)'] = 'reports/revenue_by_store/rbl_index/$1';
$route['online_conversion_rate/index/(:any)'] = 'reports/Online_conversion_rate/index/$1';
$route['abandoned_carts/index/(:any)'] = 'reports/abandoned_carts/index/$1';
$route['reports/pending_orders/(:any)'] = 'reports/Pending_orders/index/$1';
$route['reports/inventory_list/(:any)'] = 'reports/Inventory_list/index/$1';
$route['reports/order_status/(:any)'] = 'reports/Order_status/index/$1';
$route['Order_status/shop_branch_pending_orders_list/(:any)/(:any)/(:any)'] = 'reports/Order_status/shop_branch_pending_orders_list/$1/$2/$3';
$route['Order_status/shop_branch_pending_orders_in_date/(:any)/(:any)/(:any)/(:any)'] = 'reports/Order_status/shop_branch_pending_orders_in_date/$1/$2/$3/$4';
$route['Order_status/shop_branch_order_details/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'reports/Order_status/shop_branch_order_details/$1/$2/$3/$4/$5';
$route['reports/inventory_report/(:any)'] = 'reports/Inventory_report/index/$1';
$route['reports/orders_by_location/(:any)'] = 'reports/Order_by_location/index/$1';
$route['reports/branch_performance/(:any)'] = 'reports/Branch_performance/index/$1';
$route['reports/product_releasing/(:any)'] = 'reports/Product_releasing/index/$1';
$route['reports/reforder_summary/(:any)'] = 'reports/Refund_Order/summary/$1';
$route['reports/reforder_status/(:any)'] = 'reports/Refund_Order/status/$1';
$route['reports/inventory_ending_report/(:any)'] = 'reports/Inventory_report/inventory_ending_report/$1';
$route['reports/Order_report/(:any)'] = 'reports/Order_report/index/$1';
$route['order_report/order_report_table'] = 'reports/Order_report/order_report_table';
$route['order_report/export_order_report'] = 'reports/Order_report/export_order_report';
$route['reports/toktok_booking_report/(:any)'] = 'reports/Order_report/toktok_booking_report/$1';
$route['order_report/export_toktok_booking_report'] = 'reports/Order_report/export_toktok_booking_report';
$route['order_report/toktok_booking_report_table'] = 'reports/Order_report/toktok_booking_report_table';
$route['reports/order_list_payout_status_report/(:any)'] = 'reports/Order_report/order_list_payout_status_report/$1';
$route['order_report/order_list_payout_status_report_table'] = 'reports/Order_report/order_list_payout_status_report_table';
$route['order_report/export_order_list_payout_status_report'] = 'reports/Order_report/export_order_list_payout_status_report_report';
$route['reports/withholding_tax_reports/(:any)'] = 'reports/Withholding_tax_reports/index/$1';
$route['merchant_serviceable_areas/index/(:any)'] = 'reports/Merchant_serviceable_areas/index/$1';
$route['merchant_serviceable_areas/export_merchant_serviceable_areas_list'] = 'reports/merchant_serviceable_areas/export_merchant_serviceable_areas_list';

//Referral Comrate
$route['Referralcomrate/home/(:any)'] = 'settings/Referral_comrate/view/$1';
$route['Referralcomrate/list'] = 'settings/Referral_comrate/referral_comrate_list';
$route['Referralcomrate/create/(:any)'] = 'settings/Referral_comrate/add/$1';
$route['Referralcomrate/getproducts'] = 'settings/Referral_comrate/get_product_byshop';
$route['Referralcomrate/save'] = 'settings/Referral_comrate/save_refcomrate';
$route['Referralcomrate/deactivate'] = 'settings/Referral_comrate/delete_modal_confirm';
$route['Referralcomrate/disable'] = 'settings/Referral_comrate/disable_modal_confirm';
$route['Referralcomrate/manage/(:any)/(:any)'] = 'settings/Referral_comrate/edit/$1/$2';
$route['Referralcomrate/update'] = 'settings/Referral_comrate/update_refcomrate';

//Region
$route['Region/home/(:any)'] = 'settings/Settings_region/view/$1';
$route['Region/list'] = 'settings/Settings_region/region_list';
$route['Region/disable'] = 'settings/Settings_region/disable_modal_confirm';
$route['Region/deactivate'] = 'settings/Settings_region/delete_modal_confirm';

//Province
$route['Province/home/(:any)'] = 'settings/Settings_province/view/$1';
$route['Province/list'] = 'settings/Settings_province/province_list';
$route['Province/disable'] = 'settings/Settings_province/disable_modal_confirm';
$route['Province/deactivate'] = 'settings/Settings_province/delete_modal_confirm';

//City
$route['City/home/(:any)'] = 'settings/Settings_city/view/$1';
$route['City/list'] = 'settings/Settings_city/city_list';
$route['City/disable'] = 'settings/Settings_city/disable_modal_confirm';
$route['City/deactivate'] = 'settings/Settings_city/delete_modal_confirm';

//CSR
$route['Csr/ticketing/(:any)'] = 'csr/Csr/create_ticket/$1';
$route['Csr/checkcustomer'] = 'csr/Csr/customer_list';
$route['Csr/vieworder/(:any)/(:any)'] = 'csr/Csr/orders_view/$1/$2';
$route['Csr/save_ticket'] = 'csr/Csr/save_ticket';
$route['Csr/ticket_history/(:any)'] = 'csr/Csr/ticket_history/$1';
$route['Csr/ticket_list'] = 'csr/Csr/ticket_list';
$route['Csr/deactivate'] = 'csr/Csr/delete_modal_confirm';
$route['Csr/disable'] = 'csr/Csr/disable_modal_confirm';
$route['Csr/manage/(:any)/(:any)'] = 'csr/Csr/edit_ticket/$1/$2';
$route['Csr/update'] = 'csr/Csr/update_ticket';
$route['Csr/ticket_log/(:any)/(:any)'] = 'csr/Csr/ticket_log/$1/$2';
$route['Csr/ticketclose'] = 'csr/Csr/close_ticket';
$route['Csr/ticketreopen'] = 'csr/Csr/reopen_ticket';
$route['Csr/logticket'] = 'csr/Csr/save_ticketlog';
$route['Csr/approve_ticket'] = 'csr/Csr/approve_ticket';
$route['Csr/reject_ticket'] = 'csr/Csr/reject_ticket';
$route['Csr/validate_order'] = 'csr/Csr/validate_orderrefno';
$route['Csr/validate_shop'] = 'csr/Csr/validate_shop';
$route['Csr/validate_branch'] = 'csr/Csr/validate_branch';
$route['Csr/view_orders/(:any)/(:any)/(:any)'] = 'csr/Csr/view_orders/$1/$2/$3';
$route['Csr/view_customer/(:any)/(:any)/(:any)'] = 'csr/Csr/view_customer/$1/$2/$3';
$route['Csr/view_branch/(:any)/(:any)/(:any)'] = 'csr/Csr/view_branch/$1/$2/$3';
$route['Csr/view_shop/(:any)/(:any)/(:any)'] = 'csr/Csr/view_shop/$1/$2/$3';
$route['Csr/customer_table/(:any)/(:any)'] = 'csr/Csr/view_customer_table/$1/$2';
$route['Csr/account_table/(:any)/(:any)'] = 'csr/Csr/view_account_table/$1/$2';
$route['Csr/view_account/(:any)/(:any)/(:any)'] = 'csr/Csr/view_account/$1/$2/$3';
$route['Csr/checkaccount'] = 'csr/Csr/account_list';


//Tips
$route['Tips/turn_off'] = 'tips/Main_tips/tip_off';
$route['Tips/tips_section/(:any)'] = 'tips/Main_tips/tips_section/$1';

//Forgot Password
$route['Account/forgotpassword'] = 'Main/forgot_password';
$route['Account/resetpassword'] = 'Main/reset_password';
$route['Account/passwordreset/(:any)/(:any)/(:any)'] = 'Main/password_reset_form/$1/$2/$3';
$route['Account/savechangepass'] = 'Main/save_changepass_user';
$route['Account/setfirstpassword']    = 'Main/setfirstpassword';

// promotion
$route['Main_promotion/product_promotion/(:any)'] = 'promotion/Main_promotion/product_promotion/$1';
$route['Main_promotion/campaign_list/(:any)'] = 'promotion/Main_promotion/campaign_type/$1';
$route['Main_promotion/mystery_coupon/(:any)'] = 'promotion/Main_promotion/mystery_coupon/$1';
$route['Main_promotion/sf_discount/(:any)'] = 'promotion/Main_promotion/sf_discount/$1';
$route['Main_promotion/discount_promo/(:any)'] = 'promotion/Main_promotion/discount_promo/$1';

//products approval
$route['notify/logProductNotification'] = 'notification/Notification/logProductNotification';

//promotion vouchers discount

// promotion
$route['Main_promotion/voucher_discounts/(:any)'] = 'promotion/Main_promotion/voucher_discounts/$1';


// TIME IN AND OUT ACTIVITY
$route['activity/in'] = 'Main/log_seller_time_in_activity';
$route['activity/out'] = 'Main/log_seller_time_out_activity';


// Shops MCR APproval
$route['Shops/comrate_approval/(:any)'] = 'shops/Main_shops_approval/shops_changes_mcr_approval/$1';
$route['Shops/update_comrate_approval/(:any)/(:any)'] = 'shops/Main_shops_approval/update_mcr_approval/$1/$2';

// Shops MCR Scheduling
$route['Shops/shop_mcr_scheduling/(:any)'] = 'shops/Shop_mcr_scheduling/shop_mcr_scheduling/$1';
$route['Shop_mcr_scheduling/export_shop_mcr_scheduling'] = 'shops/Shop_mcr_scheduling/export_shop_mcr_scheduling';
$route['Shop_mcr_scheduling/delete_mcr_schedule_record'] = 'shops/Shop_mcr_scheduling/delete_mcr_schedule_record';
$route['Shop_mcr_scheduling/shop_mcr_cron'] = 'shops/Shop_mcr_scheduling/shop_mcr_cron';

