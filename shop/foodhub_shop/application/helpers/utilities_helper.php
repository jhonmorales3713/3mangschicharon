<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function sanitize($in) {
	$str = addslashes(htmlentities(htmlspecialchars(strip_tags(trim($in)))));
	$str = str_replace('\\', '',$str);
	return $str;
}

function removeSpecialchar($value){
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $value); // Removes special chars.
	return $string;
}

function time_diff($start,$end){
	$start = strtotime($start);
	$end = strtotime($end);
	$diff = $end - $start;
	return round(abs($diff) / 60);
}

function desanitize($in){
	$decode = $in;
	$library = array(
		"&NTILDE;" =>		"Ñ",
		"&ntilde;" =>		"ñ"
	);

	foreach($library as $key =>	$char){
		if($decode == $in){
			$decode = str_replace($key,$char,$in);
		}
	}

	return $decode;
}

function generate_json($data) {
	(ENVIRONMENT ==  "production")
	? header("access-control-allow-origin: ".c_url_root())
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

function get_vouchers($refno,$shopid){
	$that =& get_instance();
	$that->load->model('profile/model_customer_profile');
	$vouchers = $that->model_customer_profile->get_app_order_vouchers($refno,$shopid);
	if($vouchers->num_rows() > 0){
		return $vouchers->result_array();
	}else{
		return 0;
	}
}

function format_fulldatetime($date){
  return date_format(date_create($date), "F d, Y h:i A");
}

function format_shortdatetime($date){
 return date_format(date_create($date), "M d, Y h:i A");
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

// used for jc api
function en_dec_jc_api($action, $string) {

	$output = false;

	$encrypt_method = "AES-256-CBC";

    if (ENVIRONMENT == "production") {
        $secret_key = 'JCWCovidResponseHelp911';
        $secret_iv = 'TheDarkHorseRule';
    }else if (ENVIRONMENT == "testing") {
        $secret_key = 'test_key_1';
        $secret_iv = 'test_iv_1';
    }else{
        $secret_key = 'test_key_1';
        $secret_iv = 'test_iv_1';
    }

	// hash
	$key = hash('sha256', $secret_key);

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);

	if( $action == 'en' ) {
	  $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	  $output = base64_encode($output);
	}else if( $action == 'dec' ){
	  $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}

	return $output;
}

function generate_est_delivery_date($est_deliveryArr){
  return $est_deliveryArr;
}

function getShippingRateJCWW($areaId, $totamt){

  if($totamt < 5000) {
    switch($areaId) {

      case "Caloocan City": return 250; break;
      case "Las Piñas City": return 250; break;
      case "Makati City": return 120; break;
      case "Malabon City": return 210; break;
      case "Mandaluyong City": return 150; break;
      case "Manila City": return 150; break;
      case "Marikina City": return 250; break;
      case "Muntinlupa City": return 210; break;
      case "Navotas City": return 220; break;
      case "Parañaque City": return 180; break;
      case "Pasay City": return 120; break;
      case "Pasig City": return 200; break;
      case "Pateros City": return 150; break;
      case "Quezon City": return 220; break;
      case "San Juan City": return 180; break;
      case "Taguig City": return 150; break;
      case "Valenzuela City": return 250; break;
      case "Rizal (Cainta)":
      case "Rizal (San Mateo)":
      case "Rizal (Taytay)":
      case "Cavite (Amadeo)":
      case "Cavite (Bacoor)":
      case "Cavite (Carmona)":
      case "Cavite (Dasmariñas)":
      case "Cavite (General Trias)":
      case "Cavite (GMA)":
      case "Cavite (Imus)":
      case "Cavite (Indang)":
      case "Cavite (Kawit)":
      case "Cavite (Mendez)":
      case "Cavite (Naic)":
      case "Cavite (Noveleta)":
      case "Cavite (Rosario)":
      case "Cavite (Silang)":
      case "Cavite (Tagaytay)":
      case "Cavite (Tanza)":
      case "Cavite (Trece Martires)":
      case "Laguna (Biñan)":
      case "Laguna (Cabuyao)":
      case "Laguna (Calamba)":
      case "Laguna (Los Baños)":
      case "Laguna (San Pedro)":
      case "Laguna (Sta. Rosa)":
      case "Bataan (Mariveles)":
      case "Ilocos Sur (Vigan)":
      case "Ilocos Sur (Sta. Catalina)":
      case "Ilocos Sur (San Ildefonso)":
      case "La Union (San Fernando City)":
      case "Batangas (Rosario)":
      case "Batangas (Sto. Tomas)":
      case "Tarlac (Tarlac City)":
      case "Batangas (Batangas City)":
      case "Antipolo": return 250; break;
      default: return 250;
    }
  }

  else {
    switch($areaId) {
      case "Caloocan City": return 150; break;
      case "Las Piñas City": return 150; break;
      case "Makati City": return 70; break;
      case "Malabon City": return 150; break;
      case "Mandaluyong City": return 100; break;
      case "Manila City": return 100; break;
      case "Marikina City": return 150; break;
      case "Muntinlupa City": return 150; break;
      case "Navotas City": return 150; break;
      case "Parañaque City": return 120; break;
      case "Pasay City": return 70; break;
      case "Pasig City": return 150; break;
      case "Pateros City": return 100; break;
      case "Quezon City": return 150; break;
      case "San Juan City": return 120; break;
      case "Taguig City": return 100; break;
      case "Valenzuela City": return 150; break;
      case "Rizal (Cainta)":
      case "Rizal (San Mateo)":
      case "Rizal (Taytay)":
      case "Cavite (Amadeo)":
      case "Cavite (Bacoor)":
      case "Cavite (Carmona)":
      case "Cavite (Dasmariñas)":
      case "Cavite (General Trias)":
      case "Cavite (GMA)":
      case "Cavite (Imus)":
      case "Cavite (Indang)":
      case "Cavite (Kawit)":
      case "Cavite (Mendez)":
      case "Cavite (Naic)":
      case "Cavite (Noveleta)":
      case "Cavite (Rosario)":
      case "Cavite (Silang)":
      case "Cavite (Tagaytay)":
      case "Cavite (Tanza)":
      case "Cavite (Trece Martires)":
      case "Laguna (Biñan)":
      case "Laguna (Cabuyao)":
      case "Laguna (Calamba)":
      case "Laguna (Los Baños)":
      case "Laguna (San Pedro)":
      case "Laguna (Sta. Rosa)":
      case "Bataan (Mariveles)":
      case "Ilocos Sur (Vigan)":
      case "Ilocos Sur (Sta. Catalina)":
      case "Ilocos Sur (San Ildefonso)":
      case "La Union (San Fernando City)":
      case "Batangas (Rosario)":
      case "Batangas (Sto. Tomas)":
      case "Tarlac (Tarlac City)":
      case "Batangas (Batangas City)":
      case "Antipolo": return 150; break;
      default: return 150;
    }
  }
}

function get_ip($server) {
   //whether ip is from share internet
   if (!empty($server['HTTP_CLIENT_IP'])){
     $ip_address = $server['HTTP_CLIENT_IP'];
   }
   //whether ip is from proxy
   elseif (!empty($server['HTTP_X_FORWARDED_FOR'])) {
     $ip_address = $server['HTTP_X_FORWARDED_FOR'];
   }
   //whether ip is from remote address
   else{
     $ip_address = $server['REMOTE_ADDR'];
   }
   return $ip_address;
}

function column_multiple_search($value, array $cols) {
   // returns false if value to be search is not found,
   // otherwise return the key
   $found = array();
   if (sizeof($cols) > 0) {
      foreach($cols as $key => $col) {
         $exploded = explode(",", $col);
         if (in_array($value, $exploded)) {
            $found[] = $key;
            // break;
         }
      }
   }

   return $found;
}

function get_distance($address_from_lat,$address_from_lng,$address_to_lat,$address_to_lng){
  $latitudeFrom  = $address_from_lat;
  $longitudeFrom = $address_from_lng;
  $latitudeTo    = $address_to_lat;
  $longitudeTo   = $address_to_lng;

  // Calculate distance between latitude and longitude
  $theta    = $longitudeFrom - $longitudeTo;
  $dist    = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
  $dist    = acos($dist);
  $dist    = rad2deg($dist);
  $miles    = $dist * 60 * 1.1515;

  // Convert unit and return distance
  // $unit = strtoupper($unit);
  //kilometers
  $kilometers = round($miles * 1.609344, 2);
  //converts to meter
  $meters = round($miles * 1609.344, 2);
  return $meters;
}

function get_distance2($origin_lat,$origin_lng,$des_lat,$des_long){
	$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin_lat.",".$origin_lng."&destinations=".$des_lat.",".$des_long."&avoid=tolls|highways&key=AIzaSyCG6rxlvQvmRzHNJ8OJdCNAuzmXFqqAWfQ";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($order));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$json = json_decode($server_output);
	if($json->status != "OK"){
		return 0;
	}

	return $json->rows[0]->elements[0]->distance->value;
	// return $json;
}

function payment_option($data){

    $order_details['payment_option'] = $data;

    if($order_details['payment_option'] == 'ob'){
      $payment_option = 'Online Bank / E-Wallets';
    }
    else if($order_details['payment_option'] == 'otcb'){
      $payment_option = 'OTC Bank';
    }
    else if($order_details['payment_option'] == 'otcnb'){
      $payment_option = 'OTC Non Bank';
    }
    else if($order_details['payment_option'] == 'ccdb'){
      $payment_option = 'Credit / Debit Card';
    }
    else if($order_details['payment_option'] == 'wechatpay'){
      $payment_option = 'Wechatpay';
    }
    else if($order_details['payment_option'] == 'mobilepay'){
      $payment_option = 'Mobile Payments';
    }
    else if($order_details['payment_option'] == 'ewallets'){
      $payment_option = 'E-wallets';
    }
    else if($order_details['payment_option'] == 'alipay'){
      $payment_option = 'Alipay';
    }
    else if($order_details['payment_option'] == 'ewllt'){
      $payment_option = 'JCWALLET';
    }
    else{
      $payment_option = 'None';
    }

    return $payment_option;
  }

	function removeFileExtension($filename){
		$filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);

		return $filename;
	}

	function get_order_history($refnum,$shopid){
		$that =& get_instance();
		$that->load->model('profile/model_customer_profile');
		return $that->model_customer_profile->get_order_history($refnum,$shopid);
	}

	function get_product_images($productid){
		$that =& get_instance();
		$that->load->model('profile/itemsmodel');
		return $that->itemsmodel->get_product_images($productid);
	}

	function generate_random_password() {
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
