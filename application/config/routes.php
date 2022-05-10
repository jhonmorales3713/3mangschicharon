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

$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['contact_us'] = 'home/contact_us';
$route['faqs'] = 'home/faqs';

$route['shop'] = 'user/shop/index';
$route['about'] = 'user/about/index';

$route['login'] = 'user/signup/index';
$route['signup'] = 'user/signup/signup';
$route['signin'] = 'user/signup/login';
$route['signout'] = 'user/signup/signout';

$route['products/(:any)'] = 'user/products/products/$1';

$route['orders'] = 'user/orders/orders';
$route['orders/(:any)'] = 'user/orders/orders/$1';
$route['order_confirmation/(:any)'] = 'user/orders/order_confirmation/$1';

$route['checkout_gcash'] = 'user/payment/create_source';
$route['payment_failed'] = 'user/payment/payment_failed';

$route['profile'] = 'user/account/profile';
$route['account/verification'] = 'user/account/verification';

$route['cart'] = 'user/cart/index';

//admin side
$route['admin'] = 'admin/login/index';
$route['admin/signout'] = 'admin/login/signout';
$route['Account/setfirstpassword']    = 'Main/setfirstpassword';
$route['admin/dashboard'] = 'admin/dashboard/index';
$route['products_home/(:any)'] = 'admin/Main_products/products_home/$1';
$route['profile_home/(:any)'] = 'admin/Main_settings/profile_home/$1';
$route['orders_home/(:any)'] = 'admin/Main_orders/orders_home/$1';
$route['customers_home/(:any)'] = 'admin/Main_customers/customers_home/$1';
$route['reports_home/(:any)'] = 'admin/Main_reports/reports_home/$1';
$route['promotions_home/(:any)'] =  'admin/Main_promotions/promotions_home/$1';
$route['settings_home/(:any)'] = 'admin/Main_settings/settings_home/$1';
$route['change_pass/(:any)'] = 'admin/Main_settings/change_pass/$1';
$route['Main_orders/orders/(:any)'] = 'admin/Main_orders/orders/$1';
$route['Main_customers/customers/(:any)'] = 'admin/Main_customers/customers/$1';
$route['Main_products/products/(:any)'] = 'admin/Main_products/products/$1';
$route['Main_products/add_products/(:any)'] = 'admin/Main_products/add_products/$1';
$route['Main_products/update_products/(:any)'] = 'admin/Main_products/update_products/$1';
$route['Main_products/add_variant/(:any)'] = 'admin/Main_products/add_variant/$1/$2';
$route['Main_products/update_variants/(:any)'] = 'admin/Main_products/update_variants/$1/$2/$3';
$route['Main_products/view_products/(:any)'] = 'admin/Main_products/view_products/$1/$2';

$route['Main_promotions/products_discount_list/(:any)'] = 'admin/Main_promotions/products_discount_list/$1';


