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

	function generate_merchant_id(){
		$letters = array("A","B","C","D","E",
						 "F","G","H","I","J",
						 "K","L","M","N","O",
						 "P","Q","R","S","T",
						 "U","V","W","X","Y",
						 "Z");

		$numbers = array("1","2","3","4","5",
						 "6","7","8","9","0");

		$generated_key = array();
		for($x=0; $x < 5; $x++){
			if (count($generated_key) < 1) {
				$get_val = array_rand($letters, 1);

				array_push($generated_key, $letters[$get_val]);
			}else{
				$get_val = array_rand($numbers, 1);
				array_push($generated_key, $numbers[$get_val]);
			}
		}
		$generated_key = implode("",$generated_key);

		return $generated_key;
	}

	function generate_json($data) {
		header("access-control-allow-origin: *");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Content-type: application/json');

		// $ci =& get_instance();
		// $ci->load->helper('security');

		// $data['csrf_name'] = $ci->security->get_csrf_token_name();
		// $data['csrf_hash'] = $ci->security->get_csrf_hash();

		echo json_encode($data);
	}

	function number($num_format){
		if($num_format == ""){
			$num = floatval($num_format);
		}else{
			$num = filter_var($num_format, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

		}
		return $num;
	}

	function company_name(){
		echo "toktokmall";
	}

	function get_module_access($segment){
		$ci =& get_instance();
		$row_position = $ci->session->userdata('get_position_access'); // get default access per position
		$row_user = $ci->model->get_userInformation($ci->session->userdata('user_id'))->row(); // get custom access for this user
        if (!empty($row_position->access) || !empty($row_position->access)) {
			if (!empty($row_user->access) ){
				$arr_access = json_decode($row_user->access)->access;
			}else if (!empty($row_position->access) ){
				$arr_access = json_decode($row_position->access)->access;
			}
			$m_ids  = array_map(function ($item) { return $item->module_id; }, $arr_access);
			$module = $ci->model->get_modules(['module_href','module_id'], [$segment."/", $m_ids])->row();
			if($module){
				$row  = array_values(array_filter($arr_access, function ($item) use ($module) { return ($item->module_id == $module->module_id ? true : false); }));
				return $row[0];
			}else{
				return header("location:".base_url('Main/logout'));
			}
        }else{
			return header("location:".base_url('Main/logout'));
		}
	}

	function class_control_matched($access, $ctrl, $if, $else = ''){
		echo (in_array($ctrl, $access) ? $if : $else );
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

	// Start of session functions

		function company_id() {
			$ci=& get_instance();
			$ci->load->library('session');
			return $ci->session->userdata('company_id');
		}

	// End of session functions

	// Start of cron function
		function insert_cron_data($name,$desc = '',$status = 'attempted'){
			$cron_data = array(
				"cron_name" => $name,
				"cron_desc" => $desc,
				"cron_start" => todaytime(),
				"cron_status" => $status
			);

			return $cron_data;
		}

		function update_cron_data($status = 'successful'){
			$cron_data = array(
				"cron_status" => $status,
				"cron_end" => todaytime()
			);

			return $cron_data;

		}
	// End of cron function

	function en_dec($action, $string){ //used for token
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

	function en_dec_jc_api($action, $string) {

		$output = false;

		$encrypt_method = "AES-256-CBC";
		$secret_key = 'JCWCovidResponseHelp911';
		$secret_iv = 'TheDarkHorseRule';

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

	function en_dec_jcw_api($action, $string){
	    //echo "</br>".$action. " - ".$string."</br>";
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
	    }
	    else if( $action == 'dec' ){
	        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }
			//echo "</br>hoho - ".$output."</br>";
	    return $output;
	}

	function en_dec_ttm_api($action, $string){
		$output = false;
		$encrypt_method = "AES-256-CBC";
		
		if (ENVIRONMENT == "production") {
		    $secret_key = 'TOKMALL_KEY';
		    $secret_iv = 'TOKMALL_IV';
		}else{
		    $secret_key = 'TOKMALL_KEY';
		    $secret_iv = 'TOKMALL_IV';
		}

		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expect 16 bytes - else you will get warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		if($action == 'en'){
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}else if($action == 'dec'){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}

	function filter_unique($data,$index){
		$temp_arr = array_unique(array_column($data,$index));
		$unique_arr = array_intersect_key($data,$temp_arr);
		return $unique_arr;
	}

	function filter_so_main($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['branchid'] == 0 && $row['sys_shop'] == $cond['sys_shop']){
				$return_data[] = $data[$key];
			}
		}

		return $return_data;
	}

	function filter_so_branch($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['branchid'] != 0 && $row['sys_shop'] == $cond['sys_shop']){
				$return_data[] = $data[$key];
			}
		}

		return $return_data;
	}

	function filter_logs_main($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['order_id'] == $cond['order_id']){
				$return_data[] = $data[$key];
			}
		}
		return $return_data;
	}

	function filter_logs_branch($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['order_id'] == $cond['order_id']){
				$return_data[] = $data[$key];
			}
		}
		return $return_data;
	}

	function filter_so($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['sys_shop'] == $cond['sys_shop']){
				$return_data[] = $data[$key];
			}
		}

		return $return_data;
	}

	function filter_so_logs($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['order_id'] == $cond['order_id']){
				$return_data[] = $data[$key];
			}
		}

		return $return_data;
	}

	function filter_settled($data,$cond){
		$return_data = array();
		foreach($data as $key => $row){
			if($row['syshop'] == $cond['shopid'] && $row['branch_id'] == $cond['branchid']){
				$return_data[] = $data[$key];
			}
		}

		return $return_data;
	}

	function filter_process_fee($data,$cond){
		$process_fee = 0;
		if(count($data) > 0){
			foreach($data as $row){
				if($row['order_id'] == $cond['order_id']){
					$process_fee += floatval($row['processfee']);
				}
			}
		}

		return $process_fee;
	}

	function filter_shippingfee($data,$cond){
		$shipping = 0;
		if(count($data) > 0){
			$key = array_search($cond['reference_num'],array_column($data,'reference_num'));
			$shipping = $data[$key]['delivery_amount'];
		}

		return floatval($shipping);
	}

	function filter_voucher($data,$cond){
		$voucher = 0;
		if(count($data) > 0){
			foreach($data as $row){
				if($row['reference_num'] == $cond['reference_num']){
					$voucher += floatval($row['amount']);
				}
			}
		}
		return $voucher;
	}

	function filter_refcom($data,$cond){
		$refcom = 0;
		if(count($data) > 0){
			foreach($data as $row){
				if($row['reference_num'] == $cond['reference_num']){
					$refcom += floatval($row['refcom']);
				}
			}
		}

		return $refcom;
	}

	function filter_referral($data,$cond){
		$referral = 0;
		if(count($data) > 0){
			$key = array_search($cond['reference_num'],array_column($data,'reference_num'));
			if($key !== false){
				$referral = $data[$key]['referral_code'];
			}
		}

		return $referral;
	}

	function join_with_and($array){
		$str = array_pop($array);
		if ($array)
		    $str = implode(', ', $array)." and ".$str;
		return $str;
	}

	function remove_timestamp_name($str){
		$display = explode('_', $str);
		array_shift($display);
		return implode('_', $display);
	}

	function check_if_null($str){
		return ($str ? $str : 'N/A');
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

	function oneway_combination($str){
	    // $src = hash('sha256', $str);
	    $src = md5($str);
	    $new = substr($src, -5);
        for ($a=0; $a<10; $a++){
	    	$new.=substr(hex2bin($src), 0, 5).substr($src, $a, 5);
	    }
	    return preg_replace('/[^A-Za-z0-9]+/', '', substr($new, 0, 50));
	}

	function generate_player_no(){
		$letters = array("A","B","C","D","E",
						"F","G","H","I","J",
						"K","L","M","N","O",
						"P","Q","R","S","T",
						"U","V","W","X","Y",
						"Z");

		$numbers = array("1","2","3","4","5",
						"6","7","8","9","0");

		$generated_key = array();
		for($x=0; $x < 11; $x++){
			if (count($generated_key) < 4) {
				$get_val = array_rand($letters, 1);

				array_push($generated_key, $letters[$get_val]);
			}else{
				$get_val = array_rand($numbers, 1);
				array_push($generated_key, $numbers[$get_val]);
			}
		}
		$generated_key = implode("",$generated_key);

		return $generated_key;
	}

	# Start of Discount Related Functions

	function selec_discount_type($price, $qty, $discamt, $disctype){
		if($disctype == 1){
			return number_format($discamt, 2, '.', ',');
		}else if($disctype == 2){
			return number_format($discamt, 2, '.', ',').'%';
		}else{
			return number_format(0, 2, '.', ',');
		}
	}

	function discounted_total($price, $qty, $discamt, $disctype) {
		if ($disctype == "2") {
			$discamt = $price * ($discamt / 100);
		}

		$subtotal = ($price - $discamt) * $qty;

		return number_format($subtotal, 2, ".", ",");
	}

	function general_discounted_total($total, $freight, $discamt, $disctype) {
		$subtotal = $total ;
		if ($disctype == "2") {
			$discamt = $subtotal * ($discamt / 100);
		}

		$total = $subtotal - $discamt;

		return number_format($total + $freight, 2, ".", ",");
	}

	function apply_discount($price, $disc_amt) {
		$grand_price = $price - ($price * $disc_amt);

		return $grand_price;
	}

	function apply_discount2($price, $disc_type, $disc_amt) {
		if($disc_type == 1){
			$grand_price = $price - $disc_amt;
		}
		else {
			$grand_price = $price - ($price * $disc_amt);
		}

		return $grand_price;
	}

	function convert_discount($disc_type, $disc_amt) {
		if($disc_type == 1) {
			$discount = $disc_amt;
		}
		elseif($disc_type == 2) {
			$discount = $disc_amt / 100;
		}

		return $discount;
	}

	// End of Discount Related Functions


	# End of Discount Related Functions

	# Start of currency conversion function

		function peso_to_foreign($curr_id, $curr_val, $amount)
		{
			$amount   = (float)$amount;
			$curr_val = (float)$curr_val;

			if($curr_id != 1)
			{
				$amount = $amount / $curr_val;
			}

			return number_format($amount, 2, '.', '');
		}

		function foreign_to_peso($curr_id, $curr_val, $amount)
		{
			$amount   = (float)$amount;
			$curr_val = (float)$curr_val;

			if($curr_id != 1)
			{
				$amount = $amount * $curr_val;
			}

			return number_format($amount, 2, '.', '');
		}

	# End of currency conversion function

	# Start of text formatting functions
	function remove_format($text){
		$text = str_replace(",", "", $text);
		return $text;
	}

	function concatenate_name($fname, $mname, $lname, $supplement = "", $company = "") {
		$name = "";

		if (trim($supplement) != "" && trim($supplement) != "none") {
			$name .= "" . $supplement . "". " - ";
		}

		if (trim($fname) != "") {
			$name .= $fname . " ";
		}

		if (trim($mname) != "") {
			$name .= $mname . " ";
		}

		if (trim($lname) != "") {
			$name .= $lname . "";
		}

		return strtoupper($name);
	}

	function concatenate_name_company($fname, $mname, $lname, $supplement = "", $company = "") {
		$name = "";

		if (trim($supplement) != "" && trim($supplement) != "none") {
			$name .= "" . $supplement . "";

			if (trim($fname) != "") {
				$name .= " - ";
			}
		}


		if (trim($fname) != "") {
			$name .= $fname . " ";
		}

		if (trim($mname) != "") {
			$name .= $mname . " ";
		}

		if (trim($lname) != "") {
			$name .= $lname . "";
		}

		// if (trim($company) != "") {
		// 	$name .= "".$company."";
		// }

		return strtoupper($name);
	}

	function format_address($address) {
		$result = preg_replace('/[ ,]+/', ' ', trim($address));
		return trim($result);
	}

	function page_details()
	{
		$url = uri_string();
		$segments = explode('/', $url);

		$controller_function = $segments[0] . '/' . $segments[1] . '/';

		// Get details from database
		$ci=& get_instance();
		$ci->load->database();
		$ci->load->model('model_sql');

		$page_details = $ci->model_sql->get_page_details($controller_function)->row();
		return $page_details;
	}

	# End of text formatting functions

	// Start of Url Functions

		function pandabooks_url() {
			switch (ENVIRONMENT) {
			case 'development':
					return "http://localhost/pandabooks.ph/pbwebsite/";
			break;
			case 'development_server':
					return "http://192.168.1.181/dev/pbwebsite/";
			break;
			case 'testing':
					return "http://192.168.1.181/test/pbwebsite/";
			break;
			case 'uat':
					return "http://35.173.0.77/uat/pbwebsite/";
			break;
				case 'production':
				case 'production_debug':
					return "https://www.pandabooks.ph/";
				break;
			}
		}

		function avatar_folder_url() {
			$ci=& get_instance();
			$ci->load->library('session');
			$company_id = $ci->session->userdata('company_id');
			$user_id = $ci->session->userdata('user_id');

			return 'company_uploads/' . $company_id . '/user_uploads/avatar/' . $user_id;
		}

		function company_images_url() {
			$ci=& get_instance();
			$ci->load->library('session');
			$company_id = $ci->session->userdata('company_id');

			return 'company_uploads/' . $company_id . '/company_images/';
		}

		function company_dts_url() {
			$ci=& get_instance();
			$ci->load->library('session');
			$company_id = $ci->session->userdata('company_id');

			return 'company_uploads/' . $company_id . '/dts_documents/';
		}

		function company_import_files_url() {
			$ci=& get_instance();
			$ci->load->library('session');
			$company_id = $ci->session->userdata('company_id');

			return 'company_uploads/' . $company_id . '/import_files/';
		}

		function registration_url() {
			switch (ENVIRONMENT) {
				case 'development':
					return "http://localhost/pandabooks.ph/pbwebsite/register/";
				break;
				case 'development_server':
					return "http://192.168.1.181/dev/pbwebsite/register/";
				break;
				case 'testing':
					return "http://192.168.1.181/test/pbwebsite/register/";
				break;
				case 'uat':
					return "http://35.173.0.77/uat/pbwebsite/register/";
				break;
				case 'production':
				case 'production_debug':
					return "https://www.pandabooks.ph/register";
				break;
			}
		}

		function marketing_website_url() {
			switch (ENVIRONMENT) {
				case 'development':
					return "http://localhost/pandabooks.ph/pbwebsite/";
				break;
				case 'development_server':
					return "http://192.168.1.181/dev/pbwebsite/";
				break;
				case 'testing':
					return "http://192.168.1.181/test/pbwebsite/";
				break;
				case 'uat':
					return "http://35.173.0.77/uat/pbwebsite/";
				break;
				case 'production':
				case 'production_debug':
					return "https://www.pandabooks.ph";
				break;
			}
		}

	// End of Url Function

	function generate_idkey($idkey, $table, $condition = "") {
		// parameter 1 '$idkey' contains the column to retrieve in 8_idkey table
		// parameter 2 '$table' will be the table to validate the existence of the idkey
		//              if the idkey exists in the table it will be incremented
		// parameter 3 '$condition' is optional, it will be used to perform other conditional requirement in validating the idkey
		//              if the parameter 3 will only contain one additional conditional requirement an 'AND' word is not needed
		//              but for multiple conditional requirement 'AND' is required
		// this method will return a new idkey of choice
		// sample usage:
		// $cvno = generate_idkey('cvno', '8_cashvoucer', ' status = 1 ');
		// after successfully generating a new idkey please perform this action to update 8_idkey:
		// $this->model_sql->update_idkey('cvno', $cvno);

		$ci=& get_instance();
		$ci->load->library('session');
		$config_app = switch_db(company_database($ci->session->userdata('company_id')));
		$ci->load->model('model_sql');
		$ci->model_sql->app_db = $ci->load->database($config_app,TRUE);

		$idkey_old = $ci->model_sql->get_idkey($idkey);
		$idkey_new = (int)$idkey_old + 1;
		$exists = true;

		while ($exists) {
			if (count($ci->model_sql->validate_idkey($idkey, $idkey_new, $table, $condition)) == 0) {
				$exists = false;
			}
			else {
				$idkey_new++;
			}
		}

		return $idkey_new;
	}

	function generate_temporary_secret($phrase) {
		$hash_phrase = md5(strtoupper(preg_replace('/[ ]+/', '', trim($phrase)))); // will also be used as initial secret
		$unencrypted_secret = hash('crc32', $hash_phrase);

		return $unencrypted_secret;
	}

	function generate_hashed_secret($phrase) {
		// for password decryption
		$options = [
			'cost' => 12,
		];

		$secret = password_hash($phrase, PASSWORD_BCRYPT, $options);

		return $secret;
	}

	function check_recipient($chkno) {
		$ci=& get_instance();
		$ci->load->database();
		$config_app = switch_db(company_database($ci->session->userdata('company_id')));
		$ci->load->model('model_sql');
		$ci->model_sql->app_db = $ci->load->database($config_app,TRUE);

		$supid = $ci->model_sql->get_check_details($chkno)->supid;

		if ($supid == "-5") {
			$name = $ci->model_sql->get_customer_details($chkno)->checkname;
		}
		else {
			$name = $ci->model_sql->get_supplier_details($supid)->suppliername;
		}

		return $name;
	}


	function display_supplier($supid){
		$ci=& get_instance();
		$ci->load->database();
		$config_app = switch_db(company_database($ci->session->userdata('company_id')));
		$ci->load->model('model_sql');
		$ci->model_sql->app_db = $ci->load->database($config_app,TRUE);


		$name = $ci->model_sql->get_supplier_details($supid)->suppliername;

		return $name;
	}
	# Start of Dynamic Logo Printing
		function company_logo_external_print($company_id, $company_name = '', $company_address = '', $company_website = '', $company_phone = '')
		{
			# Add company code here for customized header printing with logo
			$customized_company_logo_arr = [
				'130699219',
				// 'pbpb'
			];

			$header_content = '';

			if(in_array($company_id, $customized_company_logo_arr))
			{
				$header_content .= '<img src="'. company_logo($company_id) .'" style="height: 70rem; width: 150rem">';
				$header_content .= $company_address != "" ? "<p class='line-height-two'>Address:" . $company_address . "</p>" : "";
				$header_content .= $company_phone != "" ? "<p class='line-height-two'>Contact:" . $company_phone . "</p>" : "";

				return $header_content;
			}
			else
			{
				$header_content .= $company_name != "" ? "<h3>".$company_name."</h3>" : "";
				$header_content .= $company_address != "" ? "<p class='line-height-two'>Address:" . $company_address . "</p>" : "";
				$header_content .= $company_website != "" ? "<p class='line-height-two'>Website:" . $company_website . "</p>" : "";
				$header_content .= $company_phone != "" ? "<p class='line-height-two'>Contact:" . $company_phone . "</p>" : "";

				return $header_content;
			}
		}
	# End of Dynamic Logo Printing


	function format_date($date, $format){
		$formatted_date = date($format, strtotime($date));
		return $formatted_date;
	}

	//Minify file (Single file)
	function min_file_s($path, $type){
		$ci=& get_instance();
		if($type == 'js'){
			$result = $ci->ugly->js($path);
		}else{
			$result = $ci->ugly->css($path);
		}
		return $result;
	}

	function month_to_word($monthNum, $type){
		if($type == 'short'){
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('F'); // March

			$result = substr($monthName, 0, 3);
		}else{
			$result = $monthName;
		}
		return $result;
	}

	function format_money($value, $decimal){
		 return number_format($value,$decimal,".",",");
	}

	function display_customer($idno){
		$ci=& get_instance();
		$ci->load->database();
		$config_app = switch_db(company_database($ci->session->userdata('company_id')));
		$ci->load->model('model_sql');
		$ci->model_sql->app_db = $ci->load->database($config_app,TRUE);
		$customer = $ci->model_sql->get_membermain_details($idno);

		if($customer->branchname == "none"){
			$name = $customer->fname." ".$customer->mname." ".$customer->lname;
		}else{
			if($customer->fname == "" && $customer->lname == ""){
				$name = $customer->branchname;
			}else{
				$name = $customer->fname." ".$customer->mname." ".$customer->lname." - ".$customer->branchname;
			}

		}


		return $name;
	}

	function format_date_proper($str){
		$datetime = date('F d, Y', strtotime($str));
		return $datetime;
	}

	function format_date_reverse_dash($date) {
		return date("Y-m-d", strtotime($date));
	}

	function readable_date($date) {
    date_default_timezone_set('Asia/Manila');
    return date_format(date_create($date), 'M d, Y');
}

	function draw_transaction_status($status){
		$element = "";

		if($status =='1')
		{
			$element = "<label class='badge badge-success'>Paid</label>";
			return $element;
		}
		else if($status=='p')
		{
			$element = "<label class='badge badge-warning'>Ready for Processing</label>";
			return $element;
		}
		else if($status=='po')
		{
			$element = "<label class='badge badge-warning'>Processing Order</label>";
			return $element;
		}
		else if($status=='rp')
		{
			$element = "<label class='badge badge-warning'>Ready for Pickup</label>";
			return $element;
		}
		else if($status=='bc')
		{
			$element = "<label class='badge badge-warning'>Booking Confirmed</label>";
			return $element;
		}
		else if($status=='bc')
		{
			$element = "<label class='badge badge-warning'>Booking Confirmed</label>";
			return $element;
		}
		else if($status=='f')
		{
			$element = "<label class='badge badge-success'>Fulfilled</label>";
			return $element;
		}
		else if($status=='rs')
		{
			$element = "<label class='badge badge-warning'>Return to Sender</label>";
			return $element;
		}
		else if($status=='s')
		{
			$element = "<label class='badge badge-success'>Shipped</label>";
			return $element;
		}
		else if($status == '0')
		{
			$element = "<label class='badge badge-info'>Pending</label>";
			return $element;
		}
		else if($status == 'On Process')
		{
			$element = "<label class='badge badge-info'>On Process</label>";
			return $element;
		}
		else if($status == 'Settled')
		{
			$element = "<label class='badge badge-success'>Settled</label>";
			return $element;
		}
		else if($status == 'Unpaid')
		{
			$element = "<label class='badge badge-danger'>Unpaid</label>";
			return $element;
		}
		else
		{
			$element = "<label class='badge badge-danger'>Unpaid</label>";
			return $element;
		}
	}

	function draw_transaction_status_method($status, $method){
		$element = "";

		if($status == '1' && $method != 'Free Payment' && $method != 'Prepayment' && $method != 'PayPanda' && cs_clients_info()->c_allow_cod == 1)
		{
			$element = "<label class='badge badge-success'> Paid(COD)</label>";
			return $element;
		}
		else if($status =='1')
		{
			$element = "<label class='badge badge-success'> Paid</label>";
			return $element;
		}
		else if($status=='p')
		{
			$element = "<label class='badge badge-warning'> Ready for Processing</label>";
			return $element;
		}
		else if($status=='po')
		{
			$element = "<label class='badge badge-warning'> Processing Order</label>";
			return $element;
		}
		else if($status=='rp')
		{
			$element = "<label class='badge badge-warning'> Ready for Pickup</label>";
			return $element;
		}
		else if($status=='bc')
		{
			$element = "<label class='badge badge-warning'> Booking Confirmed</label>";
			return $element;
		}
		else if($status=='bc')
		{
			$element = "<label class='badge badge-warning'> Booking Confirmed</label>";
			return $element;
		}
		else if($status=='f')
		{
			$element = "<label class='badge badge-success'> Fulfilled</label>";
			return $element;
		}
		else if($status=='s')
		{
			$element = "<label class='badge badge-success'> Shipped</label>";
			return $element;
		}
		else if($status == '0' && $method != 'Free Payment' && $method != 'Prepayment' && $method != 'PayPanda' && cs_clients_info()->c_allow_cod == 1)
		{
			$element = "<label class='badge badge-info'> Pending(COD)</label>";
			return $element;
		}
		else if($status == '0')
		{
			$element = "<label class='badge badge-info'> Pending</label>";
			return $element;
		}
		else if($status == 'On Process')
		{
			$element = "<label class='badge badge-info'> On Process</label>";
			return $element;
		}
		else if($status == 'Settled')
		{
			$element = "<label class='badge badge-success'> Settled</label>";
			return $element;
		}
		else
		{
			$element = "<label class='badge badge-danger'> Unpaid</label>";
			return $element;
		}
	}

	function generate_filename($index, $length){
		$ci=& get_instance();
		$ci->load->database();
		$ci->load->model('libs/Model_generatedfilename');
		$permitted_chars = '0123456789abcdefghijkl0123456789mnopqrstuvwxyz0123456789ABCDEFGHIJKL0123456789MNOPQRSTU0123456789VWXYZ';
		$generated_id = generate_id($permitted_chars, $length);
		while ($ci->Model_generatedfilename->is_exist(strtoupper($index.$generated_id))) {
			$generated_id = generate_id($permitted_chars, $length);
		}
		return strtoupper($index.$generated_id);
	}

	function generate_id($input, $strength) {
	    $input_length = strlen($input);
	    $random_string = '';
	    for($i = 0; $i < $strength; $i++) {
	        $random_character = $input[mt_rand(0, $input_length - 1)];
	        $random_string .= $random_character;
	    }
	    return $random_string;
	}

	function select_option_obj($obj_res, $type=""){
		$list = "";
		foreach ($obj_res as $row) {
			if($type == "city"){
				$list .= "<option value='".$row->citymunCode."'>".$row->citymunDesc.' - '.$row->provDesc."</option>";
			}else if($type == "package_inclusion"){
				$list .= "<option value='".$row->itemid."' data-itemname='".$row->itemname."' data-itemprice='".$row->price."' data-unit='".$row->unit."'>".$row->itemname."</option>";
			}else if($type == "package"){
				$list .= "<option value='".$row->package_code."'>".$row->name."</option>";
			}else if($type == "mainshop"){
				$list .= "<option value='".$row->id."'>".$row->shopname."</option>";
			}else if($type == "region"){
				$list .= "<option value='".$row->regCode."' data-regcode='".$row->regCode."'>".$row->regDesc."</option>";
			}else if($type == "province"){
				$list .= "<option value='".$row->provCode."' data-provcode='".$row->provCode."'>".$row->provDesc.' - '.$row->regDesc."</option>";
			}else if($type == "branch"){
				$list .= "<option value='".$row->id."'>".$row->branchname."</option>";
			}else if($type == "mainshop_en"){
				$list .= "<option value='".en_dec('en', $row->id)."'>".$row->shopname."</option>";
			}else if($type == "branch_en"){
				$list .= "<option value='".en_dec('en', $row->id)."'>".$row->branchname."</option>";
			}else{
				$list .= "<option value='".$row->id."'>".$row->description."</option>";
			}
		}
		echo $list;
	}

	function infoicon_helper_msg($message){
		return '<i data-toggle="tooltip" data-html="true" title="'.$message.'" class="fa fa-info-circle"></i>';
	}

	function generate_randomid($index, $length){
		$ci=& get_instance();
		$ci->load->database();
		$ci->load->model('csr/Model_csr');
		$permitted_chars = '0123456789abcdefghijkl0123456789mnopqrstuvwxyz0123456789ABCDEFGHIJKL0123456789MNOPQRSTU0123456789VWXYZ';
		$generated_id = generate_ticketid($permitted_chars, $length);
		while ($ci->Model_csr->is_ticket_exist(strtoupper($index.$generated_id))) {
			$generated_id = generate_ticketid($permitted_chars, $length);
		}
		return strtoupper($index.$generated_id);
	}

	function generate_ticketid($input, $strength) {
	    $input_length = strlen($input);
	    $random_string = '';
	    for($i = 0; $i < $strength; $i++) {
	        $random_character = $input[mt_rand(0, $input_length - 1)];
	        $random_string .= $random_character;
	    }
	    return $random_string;
	}

	function url_decode($query){
        $new_arr = [];
        foreach (explode('&', $query) as $chunk) {
            $param = explode("=", $chunk);

            if ($param) {
                $key = str_replace("columns[", "", urldecode($param[0]));
				$key = str_replace("][", ".", $key);
				$key = str_replace("[", ".", $key);
				$key = str_replace("]", "", $key);

				$value=urldecode($param[1]);

                data_set($new_arr, $key, $value);
            }
        }
        return $new_arr;
    }

	function get_ticket_status($value){
		$status = "";
		switch ($value) {
			case '1':
				$status = "<label class='badge badge-success'>OPEN</label>";
				break;
			case '2':
				$status = "<label class='badge badge-primary'>RESOLVED</label>";
				break;
			case '3':
				$status = "<label class='badge badge-warning'>PENDING</label>";
				break;
			case '0':
				$status = "<label class='badge badge-info'>ARCHIVED</label>";
				break;
			case '4':
				$status = "<label class='badge badge-danger'>REJECTED</label>";
				break;
			default:
				$status = "<label class='badge badge-danger'>ARCHIVED</label>";
				break;
		}
		return $status;
	}

	//generate dates based on start and end
	function generateDates($date1, $date2, $format = 'Y-m-d'){
		$dates = array();
		$current = strtotime($date1);
		$date2 = strtotime($date2);
		$stepVal = '+1 day';
		while( $current <= $date2 ) {
		   $dates[] = date($format, $current);
		   $current = strtotime($stepVal, $current);
		}
		return $dates;
	}

	function createDateInterval($fromdate, $todate, $strDateInterval, $date_format, $group = 'day') {
		$dates = [];
        $begin = new DateTime( $fromdate );
        $end = new DateTime( $todate );
        $end = $end->modify( "+1 $group" );

        $interval = new DateInterval($strDateInterval);
        $daterange = new DatePeriod($begin, $interval ,$end);

        foreach($daterange as $date){
            $dates[] = $date->format($date_format);
		}
		// print_r($dates);
        // get current dates
		$dates = array_flip($dates);
		return $dates;
	}

	function getDateFormat($interval, $type)
	{
		$groupings = [
            'day' => explode(' ', $interval)[4],
		];
		$group = 'hour';
		foreach ($groupings as $key => $value) {
			if ($value > 0) {
				$group = $key;
				break;
			}
		}
		$formats = [
			'date_format' => [
				'year' => "Y",
				'month' => "M Y",
				'day' => "M d",
				'hour' => "H:00",
			],
			'date_interval' => [
				'year' => "P1Y",
				'month' => "P1M1D",
				'day' => "P1D",
				'hour' => "PT1H",
			],
			'db_date' => [
				'year' => "%Y",
				'month' => "%M-%Y",
				'day' => "%Y-%m-%d",
				'hour' => "%Y-%m-%d %H",
			]
		];

		if ($type == "group") {
			return $group;
		}else{
			return $formats[$type][$group];
		}
	}

	//generate date range backwards based on supplied start and end date
	function generateDateRangeBackwards($date_from,$date_to){
		$from = new DateTime($date_from);
		$to = new DateTime($date_to);

		//get date diff
		$date_diff = date_diff($from,$to);
		$fromdate = date_sub($from, $date_diff);
		$todate = date_sub($to, $date_diff);
		//subtract 1 day
		$fromdate = $fromdate->sub(new DateInterval('P1D'));
		$todate = $todate->sub(new DateInterval('P1D'));

		return array( 'fromdate' => $fromdate, 'todate' => $todate );
	}

	//generates array using supplied array with insertions of existing array
	function generateArrayIn($in_array, $on_array, $on_column, $in_column){
		$new_array = array();
		foreach($on_array as $row){
		  $key = array_search($row, array_column($in_array, $on_column));
		  if($key !== false){
			$item = array($in_column => $in_array[$key][$in_column], $on_column => $in_array[$key][$on_column]);
			array_push($new_array, $item);
		  }
		  else{
			$item = array($in_column => 0, $on_column => $row);
			array_push($new_array,$item);
		  }
		}
		return $new_array;
	}

	//returns total of a column in an array with default float value
	function getTotalInArray($array,$column_name){
		$total = 0;
		foreach($array as $arr){
			$total += floatval($arr[$column_name]);
		}
		return $total;
	}

	//sorts an array base on the first array on selected column
	function sortArrayLike($baseArray,$toSortArray,$on_column){
		$new_array = array();
		$size = sizeof($baseArray);
		for($x=0; $x<$size; $x++){
			foreach($toSortArray as $row){
				if($baseArray[$x][$on_column] == $row[$on_column]){
					array_push($new_array,$row);
				}
			}
		}
		return $new_array;
	}

	// has the same function with sortArrayLike but with using usort or ursort
	function build_sorter($key, $dir) {
        return function ($a, $b) use ($key, $dir) {
            if ($dir == 'asc') {
				$test = remove_format($a[$key]);
				if (is_numeric($test)) {
					return (remove_format($a[$key]) > remove_format($b[$key])) ? +1:-1;
				}else {
					return ($a[$key] > $b[$key]) ? +1:-1;
				}
            }else{
				$test = remove_format($a[$key]);
				if (is_numeric($test)) {
					return (remove_format($a[$key]) > remove_format($b[$key])) ? -1:+1;
				}else {
					return ($a[$key] > $b[$key]) ? -1:+1;
				}
            }
        };
	}

	//returns percentage array - compares current and previous data in array supplied. array needs to be same size
	//only process associative arrays
	function computePercentageInArray($current_data,$previous_data,$on_column){
		$p_array = array();
		$percentage = 0.0;
		$diff;
		$increased = false;
		for($x=0; $x<sizeof($current_data); $x++){
			$cur = $current_data[$x][$on_column];
			$pre = $previous_data[$x][$on_column];
			if($cur >= $pre){
				$diff = $cur - $pre;
				$increased = true;
			}
			else{
				$diff = $pre - $cur;
				$increased = false;
			}
			$percentage = (floatval($diff)/floatval($pre))*100;
			$item = array('percentage' => number_format($percentage,2), 'increased' => $increased);
			array_push($p_array,$item);
		}
		return $p_array;
	}

    function get_AbandonedCartsComputation($abandoned1, $abandoned2)
    {
        $total1 = array_sum(array_column($abandoned1, 'abandoned'));
		$total2 = array_sum(array_column($abandoned2, 'abandoned'));
		$percentage = round(getPercentage($total1, $total2), 2);

        return ($total1 > $total2) ? "<i class='fa fa-arrow-down text-red-400'></i> $percentage %":"<i class='fa fa-arrow-up text-blue-400'></i> $percentage %";
	}

	// get step size of charts
	function get_StepSize($array1, $array2, $count){
		$step = round(max(array_merge($array1, $array2))/$count, 0);
		$step_len = strlen($step)-1;
		return round($step, 0-$step_len);
	}

	function phoneNum_isvalid($phonenum, $length){
		$startindex = $phonenum[0].$phonenum[1];
		if($startindex == "09" AND strlen($phonenum) == $length){
			$status = true;//valid
		}else{
			$status = false;//not valid
		}
		return $status;
	}

	function generate_passres_token($length){
		$ci=& get_instance();
		$ci->load->database();
		$ci->load->model('Model');
		$permitted_chars = '0123456789abcdefghijkl0123456789mnopqrstuvwxyz0123456789ABCDEFGHIJKL0123456789MNOPQRSTU0123456789VWXYZ';
		$generated_id = generate_reset_token($permitted_chars, $length);
		while ($ci->Model->is_token_exist(strtoupper($generated_id))) {
			$generated_id = generate_reset_token($permitted_chars, $length);
		}
		return strtoupper($generated_id);
	}

	function generate_reset_token($input, $strength) {
	    $input_length = strlen($input);
	    $random_string = '';
	    for($i = 0; $i < $strength; $i++) {
	        $random_character = $input[mt_rand(0, $input_length - 1)];
	        $random_string .= $random_character;
	    }
	    return $random_string;
	}

	//returns percentage, checks for values -- used in reports and analytics
	function getPercentage($cur_val, $pre_val){
		$percentage = 0;
		if($cur_val == $pre_val){
		  $percentage = 0;
		}
		else if($cur_val == 0){
		  if($pre_val == 0){
			$percentage = 0;
		  }
		  else{
			$percentage = 100;
		  }
		}
		else if($cur_val > 0 && $cur_val > $pre_val){
		  if($pre_val == 0){
			$percentage = 100;
		  }
		  else if($pre_val > 0){
			$diff = $cur_val - $pre_val;
			$percentage = ($diff/$pre_val)*100;
		  }
		}
		else if($cur_val > 0 && $cur_val < $pre_val){
		  $diff = $pre_val - $cur_val;
		  $percentage = ($diff/$pre_val)*100;
		}
		return $percentage;
	}

	  //checks time array to get time range start and end
	  function getTimeRange($current,$previous){

	      if(sizeof($current) == 1){
			$cur_start = $current[0];
			$cur_end = $current[0];
		  }
		  else{
			$cur_start = strtotime($current[0]);
		    $cur_end = strtotime(end($current));
		  }
		  if(sizeof($previous) == 1){
			$pre_start = strtotime($previous[0]);
			$pre_end = strtotime($previous[0]);
		  }
		  else{
			$pre_start = strtotime($previous[0]);
			$pre_end = strtotime(end($previous));
		  }

		  if($cur_start > $pre_start){
			  $start = $cur_start;
		  }
		  else{
			  $start = $pre_start;
		  }

		  if($cur_end > $pre_end){
			  $end = $cur_end;
		  }
		  else{
			  $end = $pre_end;
		  }

		  $timerange = [];
		  $timerange['start'] = date('Y-m-d H:00',$start);
		  $timerange['end'] = date('Y-m-d H:00',$end);
		  return $timerange;

	  }

	  //generates time range array based on start and end datetime
	  function createTimeRangeArray($start, $end, $interval = '1 hour', $prefix_date = '') {
		$startTime = strtotime($start);
		$endTime   = strtotime($end);
		$returnTimeFormat = 'H:00';

		$current   = time();
		$addTime   = strtotime('+'.$interval, $current);
		$diff      = $addTime - $current;

		$times = array();
		while ($startTime <= $endTime) {
			if($prefix_date != ''){
				$time = $prefix_date.' '.date($returnTimeFormat, $startTime);
				$times[] = $time;
			}
			else{
				$times[] = date($returnTimeFormat, $startTime);
			}

			$startTime += $diff;
		}
		if(sizeof($times) == 1){
			$startTime -= $diff;
			$startTime -= $diff;
			$new_times = [];
			if($prefix_date != ''){
				$time = $prefix_date.' '.date($returnTimeFormat, $startTime);
				$new_times[] = $time;
			}
			else{
				$times[] = date($returnTimeFormat, $startTime);
			}
			$new_times[] = $times[0];
			$times = $new_times;
		}
		//$times[] = date($returnTimeFormat, $startTime);
		return $times;
	}

	function generate_est_delivery_date($est_deliveryArr){
  		return $est_deliveryArr;
	}

	function format_shortdatetime($date){
 		return date_format(date_create($date), "M d, Y h:i A");
	}

	function get_CoolorsHex()
	{
		return array('#f94144','#277da1','#f3722c','#80089c','#ff5993','#6892ed','#f8961e','#1dad1d','#f9c74f','#404240');
	}

	function get_GreenColorSet(){
		return array('#0e5d0e','#116e11','#137f13','#169016','#19a119','#1bb21b','#1ec31e','#20d420','#2ade2a','#3be13b','#4ce44c','#5de65d','#7feb7f','#90ee90','#a1f1a1','#b2f3b2','#c3f6c3','#d4f8d4','#e5fbe5','#f6fef6','#ebebeb','#e0e0e0','#d6d6d6','#cccccc','#c2c2c2','#b8b8b8','#adadad','#a3a3a3','#999999','#8f8f8f','#858585');
	}

	function get_BlueColorSet()
	{
		return array('#0e1b78','#101f8a','#12239b','#1427ad','#162bbf','#182fd0','#1a33e2','#2941e6','#3b50e8','#4c60ea','#5e70ec','#707fee','#818ff0','#939ff2','#a4aef5','#b6bef7','#c7cef9','#d9ddfb','#ebedfd','#fcfcff','#f6fef6','#ebebeb','#e0e0e0','#d6d6d6','#cccccc','#c2c2c2','#b8b8b8','#adadad','#a3a3a3','#999999','#8f8f8f','#858585');
	}

	function gen_ColorSet(){
		$arr = [];
		for ($i=0; $i < 500; $i++) {
			$arr[$i] = random_color();
		}

		return $arr;
	}

	function random_color() {
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}

	function get_Abbr($str)
	{
		$new_str = "";
		foreach (explode(" ", $str) as $key => $value) {
			if (is_numeric($value)) {
				$new_str .= " " . substr($value, 0, 1);
			} else {
				$new_str .= substr($value, 0, 1) . ".";
			}
		}
		return $new_str;
	}

	function runPieChartCalc($labels, $inventory)
    {
        $arr_cnt = count($inventory);
        $data_sum = array_sum($inventory); $total_percentage = 0; $cnt = 0;
        if ($data_sum > 0) {
            foreach ($inventory as $value) {
                $cur_per = ($value/$data_sum) * 100;
                $total_percentage += $cur_per;
                $cnt++;
                if ($arr_cnt > 3) {
                    if ($cur_per < 2) {
                        break;
                    }
                    if ($cnt == 9 && $total_percentage < 70) {
                        break;
                    }
                    if ($total_percentage >= 70) {
                        break;
                    }
                }
            }
        }
        if (count($inventory) > $cnt) {
            $dataset1 =  array_merge(array_slice($inventory, 0, $cnt), [array_sum(array_slice($inventory, $cnt, count($inventory)))]);
        } else {
            $dataset1 = $inventory;
        }
        if (count($labels) > $cnt) {
            $labels = array_merge(array_slice($labels, 0, $cnt), ['Others']);
        }

        return [
            'total' => $data_sum,
            'labels' => $labels,
            'data' => $dataset1,
        ];
    }

	function get_bookdelivery_link() {
		return get_apiserver_link().'toktok/ToktokAPI/bookDelivery';
	}

	function getDeliveryCancellationCat() {
		return get_apiserver_link().'toktok/ToktokAPI/getDeliveryCancellationCat';
	}

	function get_cancelDelivery_link() {
		return get_apiserver_link().'toktok/ToktokAPI/cancelDelivery';
	}

	//returns formatted time from seconds
	function format_seconds_to_time($time_in_seconds){
		$total_hours = floor(intval($time_in_seconds) / 3600);
		$minutes = abs(floor(($time_in_seconds / 60) % 60));

		$time_string = "";
		if($total_hours > 24){
			$days = intval($total_hours / 24);
			$hours = intval($total_hours % 24);
			if($days > 1){
				$time_string.= $days." days ";
			}
			else{
				$time_string.= $days." day ";
			}
			if($hours > 0){
				if($hours > 1){
					$time_string.= $hours." hrs ";
				}
				else{
					$time_string.= $hours." hr ";
				}

			}
		}
		else{
			if($total_hours > 0){
				if($total_hours > 1){
					$time_string.= $total_hours." hours ";
				}
				else{
					$time_string.= $total_hours." hour ";
				}
			}
		}
		if($minutes > 1){
			$time_string.= $minutes." mins";
		}
		else{
			$time_string.= $minutes." min";
		}
		return $time_string;
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

	function array_removeNullElements($arr) {
		$result = [];
		foreach ($arr as $key => $value) {
			if ($value == '' || $value == null) {
				continue;
			} else {
				$result[$key] = $value;
			}
		}
		return $result;
	}

	function get_key_ctrl ($value, $key_ctrl) {
		$str_result = "";
		foreach ($key_ctrl as $key_ctrl_val) {
			$str_result .= $value[$key_ctrl_val] . ".";
		}
		return $str_result;
	}

	function getRandomString($maxLength, $minLength)
	{
		$minLength = $maxLength < $minLength ? $maxLength : $minLength;
		$halfMin = ceil($minLength / 2);
		$halfMax = ceil($maxLength / 2);
		$bytes = random_bytes(rand($halfMin, $halfMax));
		$randomString = bin2hex($bytes);
		$randomString = strlen($randomString) > $maxLength ? substr($randomString, 0, -1) : $randomString;
		return $randomString;
	}

	function currencyConvertedValue_withPHP($amount, $currval, $currcode){
		$currval = ($currval == '' || $currval == 0) ? 1 : $currval;
		$convertedval = $amount / $currval;
		return "PHP ".number_format($amount, 2)."/".$currcode." ".number_format($convertedval, 2);

	}

	function currencyConvertedValue($amount, $currval, $currcode){
		$currval = ($currval == '' || $currval == 0) ? 1 : $currval;
		$convertedval = $amount / $currval;
		return $currcode." ".number_format($convertedval, 2);

	}

	function currencyConvertedValue_withPHP_totalperitem($amount, $currval, $currcode){
		$currval = ($currval == '' || $currval == 0) ? 1 : $currval;
		$convertedval = $amount / $currval;
		$pieces = explode('.',$convertedval);

		if(isset($pieces[1])){
			$decimal = $pieces[1];
			if(strlen($decimal) > 2){
				$convertedval = $convertedval + 0.01;
			}else{
				$convertedval = $convertedval;
			}
		}
		return "PHP ".number_format($amount, 2)."/".$currcode." ".number_format($convertedval, 2);

	}

	function currencyConvertedValue_totalperitem($amount, $currval, $currcode){
		$currval = ($currval == '' || $currval == 0) ? 1 : $currval;
		$convertedval = $amount / $currval;
		$pieces = explode('.',$convertedval);

		if(isset($pieces[1])){
			$decimal = $pieces[1];
			if(strlen($decimal) > 2){
				$convertedval = $convertedval + 0.01;
			}else{
				$convertedval = $convertedval;
			}
		}
		return $currcode." ".number_format($convertedval, 2);

	}

	function currencyConvertedRate_peritem($amount, $currval, $qty){
		$currval = ($currval == '' || $currval == 0) ? 1 : $currval;
		$convertedval = $amount / $currval;
		$pieces = explode('.',$convertedval);

		if(isset($pieces[1])){
			$decimal = $pieces[1];
			if(strlen($decimal) > 2){
				$convertedval = $convertedval + 0.01;
			}else{
				$convertedval = $convertedval;
			}
			$convertedval = numberFormatPrecision($convertedval) * $qty;
		}
		else{
			$convertedval = $convertedval * $qty;
		}

		return $convertedval;

	}

	function currencyConvertedRate($amount, $currval){
		$currval = ($currval == '' || $currval == 0) ? 1 : $currval;

		if($amount == 0){
			$convertedval = 0;
		}
		else{
			$convertedval = $amount / $currval;
			$pieces = explode('.',$convertedval);
			if(isset($pieces[1])){
				$decimal = $pieces[1];
				if(strlen($decimal) > 2){
					$convertedval = $convertedval + 0.01;
				}else{
					$convertedval = $convertedval;
				}
			}
		}

		return numberFormatPrecision($convertedval);

	}

	function displayCurrencyValue_withPHP($amount, $convertedval, $currcode){

		if($currcode == ""){
			return "PHP ".number_format($amount, 2);
		}
		else{
			return "PHP ".number_format($amount, 2)."/".$currcode." ".$convertedval;
		}
	}

	function displayCurrencyValue($amount, $convertedval, $currcode){

		if($currcode == ""){
			return "PHP ".number_format($convertedval, 2);
		}
		else{
			return $currcode." ".number_format($convertedval, 2);
		}

	}

	function numberFormatPrecision($number, $precision = 2, $separator = '.'){
		$numberParts = explode($separator, $number);
		$response = $numberParts[0];
		if (count($numberParts)>1 && $precision > 0) {
			$response .= $separator;
			$response .= substr($numberParts[1], 0, $precision);
		}
		return $response;
	}

	function display_order_status($status, $export = false){

		if($status == 'p'){
			$label = (!$export) ? "<label class='badge badge-warning'> Ready for Processing</label>" : "Ready for Processing";
		}
		else if($status == 'po'){
			$label = (!$export) ? "<label class='badge badge-warning'> Processing Order</label>" : "Processing Order";
		}
		else if($status == 'rp'){
			$label = (!$export) ? "<label class='badge badge-warning'> Ready for Pickup</label>" : "Ready for Pickup";
		}
		else if($status == 'bc'){
			$label = (!$export) ? "<label class='badge badge-warning'> Booking Confirmed</label>" : "Booking Confirmed";
		}
		else if($status == 'f'){
			$label = (!$export) ? "<label class='badge badge-success'> Fulfilled</label>" : "Fulfilled";
		}
		else if($status == 'rs'){
			$label = (!$export) ? "<label class='badge badge-success'> Return to Sender</label>" : "Return to Sender";
		}
		else if($status == 's'){
			$label = (!$export) ? "<label class='badge badge-success'> Shipped</label>" : "Shipped";
		}
		else{
			$label = (!$export) ? "<label class='badge badge-warning'> Ready for Processing</label>" : "Ready for Processing";
		}

		return $label;
	}

	function display_payment_status($payment_status, $payment_method, $allow_cod, $export = false){

		if($payment_status == 1 && $payment_method != 'Free Payment' && $payment_method != 'Prepayment' && $payment_method != 'PayPanda' && $allow_cod == 1) {
			$label = (!$export) ? "<label class='badge badge-success'> Paid(COD)</label>" : "Paid(COD)";
		}
		else if($payment_status == 1) {
			$label = (!$export) ? "<label class='badge badge-success'> Paid</label>" : "Paid";
		}
		else if($payment_status == 0 && $payment_method != 'Free Payment' && $payment_method != 'Prepayment' && $payment_method != 'PayPanda' && $allow_cod == 1){
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


	function get_application_merchant_status($value){
		$status = "";
		switch ($value) {
			case '2':
				$status = "<label class='badge badge-success'>Approved</label>";
				break;
			case '1':
				$status = "<label class='badge badge-primary'>Pending</label>";
				break;
			case '0':
				$status = "<label class='badge badge-info'>Declined</label>";
				break;
			default:
				$status = "<label class='badge badge-primary'>Pending</label>";
				break;
		}

		return $status;
	}

    function randomShopCode(){
		$len = 3;
		$abc="aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ";
		$letters = str_split($abc);
		$str = "";
		for ($i=0; $i<=$len; $i++) {
			$str .= $letters[rand(0, count($letters)-1)];
		};
		return $str;
	  }

	function select_sub_category_list($obj_res){
		$list = "";
		foreach ($obj_res as $row) {

			$list .= "<option value='".$row->id."'>".$row->category_name."</option>";

		}
		echo $list;
	}

	function en_dec_vouchers_keys($value){
		if ($value == "secret_key") {

			if (ENVIRONMENT == "production") {
				$secret_key = 'test123';
			}else if (ENVIRONMENT == "testing") {
				$secret_key = 'test123';
			}else{
				$secret_key = 'test123';
			}

			return $secret_key;

		} else if($value == "secret_iv"){

			if (ENVIRONMENT == "production") {
				$secret_iv = 'test456';
			}else if (ENVIRONMENT == "testing") {
				$secret_iv = 'test456';
			}else{
				$secret_iv = 'test456';
			}

			return $secret_iv;

		}



	}
	function en_dec_vouchers($action, $string) {
	    $output = false;
	    $encrypt_method = "AES-256-CBC";

	    if (ENVIRONMENT == "production") { //live env keys
	        $secret_key = en_dec_vouchers_keys("secret_key");
	        $secret_iv  = en_dec_vouchers_keys("secret_iv");
	    }else if (ENVIRONMENT == "testing") { // test env keys
	        $secret_key = en_dec_vouchers_keys("secret_key");
	        $secret_iv  = en_dec_vouchers_keys("secret_iv");
	    }else{  // test env keys
	        $secret_key = en_dec_vouchers_keys("secret_key");
	        $secret_iv  = en_dec_vouchers_keys("secret_iv");
	    }

	    // hash
	    $key = hash('sha256', $secret_key);
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);

	    if( $action == 'en' ) {
	        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	        $output = base64_encode($output);
	    }else if( $action == 'dec' ) {
	        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }

	    return $output;
	}

	function en_dec_vouchers_concat($action, $string) {
	    $output = false;
	    $encrypt_method = "AES-256-CBC";

	    if (ENVIRONMENT == "production") { //live env keys
	        $secret_key = en_dec_vouchers_keys("secret_key");
	        $secret_iv  = en_dec_vouchers_keys("secret_iv");
	    }else if (ENVIRONMENT == "testing") { // test env keys
	        $secret_key = en_dec_vouchers_keys("secret_key");
	        $secret_iv  = en_dec_vouchers_keys("secret_iv");
	    }else{  // test env keys
	        $secret_key = en_dec_vouchers_keys("secret_key");
	        $secret_iv  = en_dec_vouchers_keys("secret_iv");
	    }

	    // hash
	    $key = hash('sha256', $secret_key);
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);

	    if( $action == 'en' ) {
	        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	        $output = base64_encode($output);
	    }else if( $action == 'dec' ) {
	    	if($string==""){
	    		 $output = "";
	    	}else{
	    	$ex_string=explode(', ', $string);
	    	$new_string=[];
	    	$count=count($ex_string);
	    	$counter=0;
	    	foreach ($ex_string as $string) {
				$new_string[$counter]=openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
				$counter++;
			}
	    	$output=implode(', ', $new_string);
			}
	    }

	    return $output;
	}

	function select_region_list($obj_res){
		$list = "<option value='000'>All Region</option>";
		foreach ($obj_res as $row) {

			$list .= "<option value='".$row->regCode."'>".$row->regDesc."</option>";

		}
		echo $list;
	}

	function getresizedHeight($type, $origWidth, $origHeight){
        $type       = floatval($type);
        $origWidth  = floatval($origWidth);
        $origHeight = floatval($origHeight);
        $max_width  = $type;
        $new_width  = $origWidth;
        $new_height = $origHeight;
        $ratio      = $max_width / $origWidth;
        $new_width  = $max_width;
        $new_height = $origHeight * $ratio;

        return $new_height;
    }
