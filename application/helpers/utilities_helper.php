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

    function is_valid_date($date, $replace_only = false) {
        $date = str_replace('/', '-', $date);
        if($replace_only == false){
            $date = strtotime($date);
        }
        return $date;
    }

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

	function format_date_reverse_dash($date) {
		return date("Y-m-d", strtotime($date));
	}

    function format_shortfulldate($date){
        $date = is_valid_date($date);
        if (! $date) {
            return false;
        }
    	$datetime = date('M d, Y', $date);
    	return $datetime;
    }
    

	function display_payment_status($payment_status, $payment_method, $export = false){

		if($payment_status == 1 && $payment_method != 'Free Payment' && $payment_method != 'Prepayment') {
			$label = (!$export) ? "<label class='badge badge-info'> Pending(COD)</label>" : "Pending(COD)";
		}
		else if($payment_status == 1) {
			$label = (!$export) ? "<label class='badge badge-success'> Paid</label>" : "Paid";
		}
		else if($payment_status == 3 && $payment_method != 'Free Payment' && $payment_method != 'Prepayment'){
			$label = (!$export) ? "<label class='badge badge-info'> Pending(COD)</label>" : "Pending(COD)";
		}
		else if($payment_status == 0){
			$label = (!$export) ? "<label class='badge badge-info'> Pending</label>" : "Pending";
		}
		else{
			$label = (!$export) ? "<label class='badge badge-info'> Pending</label>" : "Pending";
		}

		return $label;
	}

    
	function display_order_status($status, $export = false){
        $value='';
		if($status == 1){
            $value = 'Pending';
		}
		else if($status == 2){
            $value = 'Processing';
		}
		else if($status == 3){
            $value = 'Approved';
		}
		else if($status == 4){
            $value = 'Shipped';
		}
		else if($status == 5){
            $value = 'Delivered';
		}
		else if($status == 6){
            $value = 'Cancelled';
		}
		else if($status == 7){
            $value = 'Return';
		}
		else if($status == 8){
            $value = 'Accepted';
		}
		else if($status == 9){
            $value = 'Declined';
		}

        $label = (!$export) ? "<label class='badge badge-warning'>".$value.'</label>':$value;
		return $label;
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
function validate_link($name,$usename){
    if(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$name) && $name != ''){
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => 0,
            'message'     =>array($usename." link must be a valid link")
        ];
        echo json_encode($response);
        die();
    }
}
function get_status_ui($status_id){
    $status_string = "";
    switch($status_id){
        case 0:
            $status_string = '<span class="badge badge-pill badge-danger">Cancelled</span>';
        break;
        case 1:
            $status_string = '<span class="badge badge-pill badge-warning">Pending</span>';
        break;
        case 2:
            $status_string = '<span class="badge badge-pill badge-primary">Processing</span>';
        break;
        case 3:
            $status_string = '<span class="badge badge-pill badge-info">For Pickup Delivery</span>';
        break;
        case 4:
            $status_string = '<span class="badge badge-pill badge-info">Fulfilled</span>';
        break;
        case 5:
            $status_string = '<span class="badge badge-pill badge-success">Delivered</span>';
        break;
        case 6:
            $status_string = '<span class="badge badge-pill badge-danger">Cancelled By Customer</span>';
        break;
        case 7:
            $status_string = '<span class="badge badge-pill badge-danger">Cancelled By System</span>';
        break;
    }
    return $status_string;
}


?>