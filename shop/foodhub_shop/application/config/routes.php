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


$route['paypanda/postback'] = 'api/Orders/paypanda_postback';
$route['paypanda/returnurl'] = 'api/Orders/paypanda_return_url';
$route['api/processDrNo'] = 'api/Orders/save_admin_dr';
$route['api/processDelivery'] = 'api/Orders/process_delivery';
$route['paymentRedirect'] = 'api/Orders/paymentRedirect';

$route['api/insertCartItems'] = 'api/Shop/insertCartItems';
$route['api/getCartItems'] = 'api/Shop/getCartItems';
$route['api/getCartCount'] = 'api/Shop/getCartCount';
$route['api/removeCartItem'] = 'api/Shop/removeCartItem';
$route['api/removeCartShop'] = 'api/Shop/removeCartShop';
$route['order_again'] = 'api/Orders/order_again';
$route['api/getShippingRate'] = 'api/Orders/getShippingRate';

$route['api/validateVoucher'] = 'api/Voucher/validateVoucher';
$route['api/makeAvailableVoucher'] = 'api/Voucher/makeAvailableVoucher';
$route['api/updateValidVouchers'] = 'api/Voucher/updateValidVouchers';
$route['api/getValidVouchers'] = 'api/Voucher/getValidVouchers';

$route['order_items/(:any)'] = 'OrderItem/order_items/$1';
$route['shop/cart'] = 'main/shoppingCart';
$route['shop/checkout'] = 'main/checkoutPage';

$route['check_order'] = 'main/checkOrderPage';
$route['check_order_details'] = 'main/checkOrder';
$route['api/checkRef'] = 'main/checkRef';
$route['api/searchProducts'] = 'main/searchProducts';
$route['search'] = 'main/search';

$route['store/(:any)/(:any)'] = 'main/shop_page/$1/$2';
$route['store/(:any)'] = 'main/shop_page/$1';

$route['products/(:any)/(:any)'] = 'main/products/$1/$2';
$route['products/(:any)'] = 'main/products/$1';

$route['api/getItems'] = 'api/Shop/getItems';
$route['api/validateRefCode'] = 'main/validate_referral_code';

$route['user/login'] = 'auth/Authentication/index';
$route['user/logout'] = 'auth/Authentication/logout';
$route['user/check_email'] = 'auth/Authentication/check_email';
$route['auth/authentication/login'] = 'auth/Authentication/login';
$route['user/register'] = 'auth/Authentication/register';
$route['auth/authentication/login_jc'] = 'auth/Authentication/login_jc';
$route['auth/reset/(:any)/(:any)/(:any)'] = 'auth/Authentication/reset/$1/$2/$3';

$route['user/profile'] = 'profile/Customer_profile/index';
$route['user/address'] = 'profile/Customer_profile/address';
$route['user/password'] = 'profile/Customer_profile/password';
$route['user/purchases'] = 'profile/Customer_profile/purchases';

$route['privacy-policy'] = 'main/privacy';
$route['terms-and-conditions'] = 'main/terms';
$route['contact-us'] = 'main/contact';

$route['sys/shipping_delivery/get_citymun'] = 'api/Shop/get_citymun';
$route['sys/shipping_delivery/get_province'] = 'api/Shop/get_province';

$route['404_override'] = 'Error_404';
$route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = 'Main';


$route['ordernow/(:any)'] = 'Main/index/$1';
$route['(:any)'] = 'Main/index/$1';
