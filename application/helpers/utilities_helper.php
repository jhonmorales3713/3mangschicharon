<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function __construct()
{
    $this->CI = get_instance();
    $this->CI->load->database('default',TRUE);
}

function sanitize($in) {
    return htmlspecialchars(strip_tags(trim($in)));
}

function sanitize_array($arr) {
    foreach ($arr as $k => $v) {
        $arr[$k] = htmlspecialchars(strip_tags(trim($v)));
    }
    return $arr;
}

function en_dec($action, $string){ //used for token
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = '2022Project';
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



function php_money($data){ // return with php string
	return "Php " . number_format($data,2);
}

function generate_json($data) {

	header("access-control-allow-origin: *");
	header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
	header('Content-type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ci =& get_instance();
    }	

	echo json_encode($data);
}

	// Start of date and time functions

    function datetime()
    {
        date_default_timezone_set('Asia/Manila');
        return date("Y-m-d h:i:s");
    }

    function today() {
        date_default_timezone_set('Asia/Manila');
        return date("Y-m-d");
    }

    function today_close_reverse() {
        date_default_timezone_set('Asia/Manila');
        return date("Ymd");
    }

    function today_datetime_dash_reverse() {
        date_default_timezone_set('Asia/Manila');
        return date("Y-m-d H:i:s");
    }

    function today_text() {
        date_default_timezone_set('Asia/Manila');
        return date("m/d/Y");
    }

    function today_date() {
        date_default_timezone_set('Asia/Manila');
        return date("m/d/Y");
    }

    function time_only() {
        date_default_timezone_set('Asia/Manila');
        return date("G:i");
    }

    function time_w_sec(){
        date_default_timezone_set('Asia/Manila');
        return date("G:i:s");
    }

    function year_only() {
        date_default_timezone_set('Asia/Manila');
        return date("Y");
    }

    function todaytime() {
        date_default_timezone_set('Asia/Manila');
        return date("Y-m-d G:i:s");
    }

    function todaytime_slash_proper() {
        date_default_timezone_set('Asia/Manila');
        return date("m/d/Y h:i A");
    }

    function check_date_full_long($str){
        return ($str ? format_date_full_long($str) : 'N/A');
    }

    function check_date_full($str){
        return ($str ? format_date_full($str) : 'N/A');
    }

    function format_date_full_long($str){
        $datetime = date('F d, Y h:i:s A', strtotime($str));
        return $datetime;
    }

    function format_date_full($str){
        $datetime = date('F d, Y - h:i A', strtotime($str));
        return $datetime;
    }

    function format_date_full_long_withday($str){
        $datetime = date('D - F d, Y h:i:s A ', strtotime($str));
        return $datetime;
    }

    function format_full_time($str){
        $date = date('h:i:s A', strtotime($str));
        return $date;
    }

    function format_date_dash_reverse($str){
        $date = date('Y-m-d', strtotime($str));
        return $date;
    }

    function format_datetime_dash_reverse($str){
        $date = date('Y-m-d H:i:s', strtotime($str));
        return $date;
    }

    function check_date($time, $format, $format_week, $format_else){
        date_default_timezone_set('Asia/Manila');
        $given_time = format_date_dash_reverse($time);
        if($given_time === format_date_dash_reverse('today')){
            return "Today at ".$format($time);
        }else if($given_time === format_date_dash_reverse('-1 days')){
            return "Yesterday at ".$format($time);
        }else if($given_time === format_date_dash_reverse('-7 days')){
            return $format_week($time);
        }else{
            return $format_else($time);
        };
    }
// End of date and time functions

function removeFileExtension($filename){
    $filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);

    return $filename;
}

function crypto_rand_secure($min, $max){
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}


function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited
    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
    }
    return $token;
}


?>