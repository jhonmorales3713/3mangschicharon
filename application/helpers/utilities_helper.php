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