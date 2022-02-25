<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function sanitize($in) {
	return addslashes(htmlentities(htmlspecialchars(strip_tags(trim($in)))));
}

function linked_company_url(){
  return "http://35.173.0.77/dev/jcw/";
}

function base_url_admin($data){
	$base_url_admin = "";

	switch (ENVIRONMENT) {
		case 'development':
			$base_url_admin = 'http://localhost/cpshop-admin/'.$data;
		break;

		case 'testing':
			$base_url_admin = 'https://shopanda.ph/cpshop-admin/'.$data;
		break;

		case 'production':
			$base_url_admin = 'https://shopanda.ph/shop-login'.$data;
		break;
	}

	return $base_url_admin;
}

function generate_json($data) {
	(ENVIRONMENT ==  "production") 
	? header("access-control-allow-origin: https://shopanda.ph/")
	: header("access-control-allow-origin: *");
	
	header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
	header('Content-type: application/json');
	echo json_encode($data);

}

function today() {
	date_default_timezone_set('Asia/Manila');
	return date("Y-m-d");
}

function today_text() {
	date_default_timezone_set('Asia/Manila');
	return date("m/d/Y");
}

function time_only() {
	date_default_timezone_set('Asia/Manila');
	return date("G:i");
}

function todaytime() {
	date_default_timezone_set('Asia/Manila');
	return date("Y-m-d G:i:s");
}

function company_name() {
	echo "Shopanda PH";
}
function get_company_name() {
	return "Shopanda PH";
}
function get_company_email() {
	return "orders@shopanda.ph";
}
function get_company_phone() {
	return "(632) ‎8898 1309";
}
function get_autoemail_sender() {
	return "noreply@shopanda.ph";
}
function get_seller_reg_form() {
	return "https://docs.google.com/forms/d/e/1FAIpQLScdh3GXRkAntN0nCFxgXhm20-j0lF4HKVSVNWOJIvhmdBXavg/viewform";
}

function company_initial() {
	echo "CPPI";
}

function fb_link(){
	return "https://www.facebook.com/OfficialCloudPandaPH";
}

function ig_link(){
	return "https://www.instagram.com/cloudpandaph";
}

function powered_by(){
	echo "Powered by <a href='http://www.cloudpanda.ph/' class='external' style='text-decoration:underline;'>Cloud Panda PH, Inc.</a>";
}

function shop_main_announcement() {
	return "We deliver daily between 10 a.m. to 5 p.m. Orders placed beyond 5 p.m. will be processed the following day."; 
}

function format_fulldatetime($date){
  return date_format(date_create($date), "F d, Y h:i A");
}

function en_dec($action, $string) //used for token
{
	$output = false;

	$encrypt_method = "AES-256-CBC";
	$secret_key = 'CloudPandaPHInc';
	$secret_iv = 'TheDarkHorseRule';

	// hash
	$key = hash('sha256', $secret_key);

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);

	if( $action == 'en' ) 
	{
	  $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	  $output = base64_encode($output);
	}
	else if( $action == 'dec' )
	{
	  $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}

	return $output;
}

function Generate_random_password() {
    $alphabet = "abcdefghijklmnopqrstuwxyz";
    $alphabetUpper = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
    $alphabetNumber = "0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabetNumber) - 1; //put the length -1 in cache
    for ($i = 0; $i < 3; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n].$alphabetUpper[$n].$alphabetNumber[$n];
    }
    return implode($pass); //turn the array into a string
}

function generate_est_delivery_date($est_deliveryArr){
  // $firstDate = strtotime(date("Y-m-d"));
  // $est_delivery = "";
  // $drAvailable = false;
  // foreach($est_deliveryArr as $key => $val){
  //   if($val == "true"){
  //     $drAvailable = true;
  //     $newDate = strtotime('next '.$key);
  //     if(date("Y-m-d", $firstDate) > date("Y-m-d", $newDate) || date("Y-m-d", $firstDate) == date("Y-m-d")){
  //       $est_delivery = date("F d, Y", $newDate);
  //     }
  //   }
  //   if(!$drAvailable){
  //     $est_delivery = "";
  //   }
  // };
  // return $est_delivery;
  return $est_deliveryArr;
}