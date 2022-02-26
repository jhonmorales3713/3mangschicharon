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
?>