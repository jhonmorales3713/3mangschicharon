<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * ------------------------------------------------------
 *  Initialize the `core_shopanda` database.
 * ------------------------------------------------------
 */

function db_core(){
	$ci =& get_instance();
	$core = $ci->load->database('core', TRUE);

	return $core;
}


/*
 * ------------------------------------------------------
 *  Load and fetch the `cs_clients_info` database.
 * ------------------------------------------------------
 */

 $cs_client_info = null;

 function get_cs_clients_info(){
 	global $cs_client_info;
 	$sql = "SELECT * FROM cs_clients_info WHERE id_key = ? LIMIT 1";
 	$query = db_core()->query($sql, ini());
   $cs_client_info = $query->num_rows() > 0 ? $query->row() : FALSE;
 	return $cs_client_info;
 }

 get_cs_clients_info();

function cs_clients_info(){
	global $cs_client_info;
	return ($cs_client_info !== FALSE) ? $cs_client_info : FALSE;
}

function cpshop_api_url(){
	return (cs_clients_info()) ? cs_clients_info()->c_cpshop_api_url : "";
}

function c_inv_threshold(){
	return (cs_clients_info()) ? cs_clients_info()->c_inv_threshold : 0;
}

function get_company_name(){
	return (cs_clients_info()) ? cs_clients_info()->c_name : "";
}

function get_tag_line(){
	return (cs_clients_info()) ? cs_clients_info()->c_tag_line : "";
}

function get_company_email(){
	return (cs_clients_info()) ? cs_clients_info()->c_email : "";
}

function get_company_phone(){
	return (cs_clients_info()) ? cs_clients_info()->c_phone : "";
}

function get_autoemail_sender(){
	return (cs_clients_info()) ? cs_clients_info()->c_auto_email_sender : "";
}

function company_initial(){
	return (cs_clients_info()) ? cs_clients_info()->c_initial : "";
}

function fb_link(){
	return (cs_clients_info()) ? cs_clients_info()->c_social_media_fb_link : "";
}

function ig_link(){
	return (cs_clients_info()) ? cs_clients_info()->c_social_media_ig_link : "";
}

function order_ref_prefix(){
	return (cs_clients_info()) ? cs_clients_info()->c_order_ref_prefix : "";
}

function order_so_ref_prefix(){
	return (cs_clients_info()) ? cs_clients_info()->c_order_so_ref_prefix : "";
}

function allow_login(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_login : 0;
}

function allow_shop_page(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_shop_page : 0;
}

function allow_facebook_login(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_facebook_login : 0;
}

function allow_gmail_login(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_gmail_login : 0;
}

function allow_connect_as_online_reseller(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_connect_as_online_reseller : 0;
}

function allow_physical_login(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_physical_login : 0;
}

function continue_as_guest_button(){
	return (cs_clients_info()) ? cs_clients_info()->c_continue_as_guest_button : 0;
}

function allow_registration(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_registration : 0;
}

function allow_cod(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_cod : 0;
}

function allow_google_addr(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_google_addr : 0;
}

function c_google_api_key(){
	return (cs_clients_info()) ? cs_clients_info()->c_google_api_key : 0;
}

function allow_voucher(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_voucher : 0;
}

function allow_toktok_shipping(){
	return (cs_clients_info()) ? cs_clients_info()->c_allow_toktok_shipping : 0;
}

function c_order_threshold(){
	return (cs_clients_info()) ? cs_clients_info()->c_order_threshold : 0;
}





function get_root_dir($path){
	switch (ENVIRONMENT){
		case 'development': $segment = (cs_clients_info()) ? cs_clients_info()->c_url_root_segment_local : ""; break;
		case 'testing'    : $segment = (cs_clients_info()) ? cs_clients_info()->c_url_root_segment_test  : ""; break;
		case 'production' : $segment = (cs_clients_info()) ? cs_clients_info()->c_url_root_segment_live  : ""; break;
		default           : $segment = "/";
	}
	return $segment.$path;
}

function shop_url(){
	switch (ENVIRONMENT){
		case 'development': $shop_url = (cs_clients_info()) ? cs_clients_info()->c_url_shop_local : ""; break;
		case 'testing'    : $shop_url = (cs_clients_info()) ? cs_clients_info()->c_url_shop_test  : ""; break;
		case 'production' : $shop_url = (cs_clients_info()) ? cs_clients_info()->c_url_shop_live  : ""; break;
		default           : $shop_url = "";
	}
	return $shop_url;
}

function get_shop_url($path){
	return shop_url().$path;
}

function c_url_root(){
	switch (ENVIRONMENT){
		case 'development': $url_root = (cs_clients_info()) ? cs_clients_info()->c_url_root_local : ""; break;
		case 'testing'    : $url_root = (cs_clients_info()) ? cs_clients_info()->c_url_root_test  : ""; break;
		case 'production' : $url_root = (cs_clients_info()) ? cs_clients_info()->c_url_root_live  : ""; break;
		default           : $url_root = "";
	}
	return $url_root;
}

function base_url_admin($data){
	switch (ENVIRONMENT){
		case 'development': $base_url_admin = (cs_clients_info()) ? cs_clients_info()->c_url_admin_local : ""; break;
		case 'testing'    : $base_url_admin = (cs_clients_info()) ? cs_clients_info()->c_url_admin_test  : ""; break;
		case 'production' : $base_url_admin = (cs_clients_info()) ? cs_clients_info()->c_url_admin_live  : ""; break;
	}

	return $base_url_admin.$data;
}

function privacy_policy(){
	$privacy_policy =  (cs_clients_info()) ? cs_clients_info()->c_privacy_policy : "";

	if (filter_var($privacy_policy, FILTER_VALIDATE_URL)) {
	  	return $privacy_policy;
	} else {
		$privacy_policy = '/'.$privacy_policy;
	 	$privacy_policy = substr($privacy_policy, strrpos($privacy_policy, '/' )+1);

	 	if ($privacy_policy != "" && $privacy_policy != "/") {
	 		return base_url('main/privacy');
	 	}
	}
}

function privacy_policy_view(){
	return (cs_clients_info()) ? cs_clients_info()->c_privacy_policy : "";
}

function terms_and_condition(){
	$terms_and_condition =  (cs_clients_info()) ? cs_clients_info()->c_terms_and_condition : "";

	if (filter_var($terms_and_condition, FILTER_VALIDATE_URL)) {
	  	return $terms_and_condition;
	} else {
		$terms_and_condition = '/'.$terms_and_condition;
	 	$terms_and_condition = substr($terms_and_condition, strrpos($terms_and_condition, '/' )+1);

	 	if ($terms_and_condition != "" && $terms_and_condition != "/") {
	 		return base_url('main/terms');
	 	}
	}
}

function terms_and_condition_view(){
	return (cs_clients_info()) ? cs_clients_info()->c_terms_and_condition : "";
}

function contact_us(){
	$contact_us =  (cs_clients_info()) ? cs_clients_info()->c_contact_us : "";

	if (filter_var($contact_us, FILTER_VALIDATE_URL)) {
	  	return $contact_us;
	} else {
		$contact_us = '/'.$contact_us;
	 	$contact_us = substr($contact_us, strrpos($contact_us, '/' )+1);

	  	if ($contact_us != "" && $contact_us != "/") {
	 		return base_url('main/contact');
	 	}
	}
}

function contact_us_view(){
	return (cs_clients_info()) ? cs_clients_info()->c_contact_us : "";
}

function fb_pixel_id(){
	$c_fb_pixel_id = "";
	switch (ENVIRONMENT){
		case 'testing'    : $c_fb_pixel_id = (cs_clients_info()) ? cs_clients_info()->c_fb_pixel_id_test  : ""; break;
		case 'production' : $c_fb_pixel_id = (cs_clients_info()) ? cs_clients_info()->c_fb_pixel_id_live  : ""; break;
	}

	return $c_fb_pixel_id;
}

function get_seller_reg_form() {
	return (cs_clients_info()) ? cs_clients_info()->c_get_seller_reg_form : "";
}

function main_logo() {
	return base_url("/assets/img/".((cs_clients_info()) ? cs_clients_info()->c_main_logo : ""));
}

function main_logo_webp(){
	return base_url("/assets/img/webp/".pathinfo(((cs_clients_info()) ? cs_clients_info()->c_main_logo : ""), PATHINFO_FILENAME).".webp");
}

function secondary_logo() {
	return base_url("/assets/img/".((cs_clients_info()) ? cs_clients_info()->c_secondary_logo : ""));
}

function secondary_logo_webp() {
	return base_url("/assets/img/webp/".pathinfo(((cs_clients_info()) ? cs_clients_info()->c_secondary_logo : ""), PATHINFO_FILENAME).".webp");
}

function placeholder_img() {
	return base_url("/assets/img/".((cs_clients_info()) ? cs_clients_info()->c_placeholder_img : ""));
}

function fb_image() {
	return get_s3_imgpath_upload()."assets/img/".((cs_clients_info()) ? cs_clients_info()->c_fb_image : "");
}

function favicon() {
	return get_s3_imgpath_upload()."assets/img/".((cs_clients_info()) ? cs_clients_info()->c_favicon : "");
}

function header_upper_bg(){
	return (cs_clients_info()) ? cs_clients_info()->header_upper_bg : "";
}

function header_upper_txtcolor(){
	return (cs_clients_info()) ? cs_clients_info()->header_upper_txtcolor : "";
}

function header_middle_bg(){
	return (cs_clients_info()) ? cs_clients_info()->header_middle_bg : "";
}

function header_middle_txtcolor(){
	return (cs_clients_info()) ? cs_clients_info()->header_middle_txtcolor : "";
}

function header_middle_icons(){
	return (cs_clients_info()) ? cs_clients_info()->header_middle_icons : "";
}

function header_bottom_bg(){
	return (cs_clients_info()) ? cs_clients_info()->header_bottom_bg : "";
}

function header_bottom_textcolor(){
	return (cs_clients_info()) ? cs_clients_info()->header_bottom_textcolor : "";
}

function footer_bg(){
	return (cs_clients_info()) ? cs_clients_info()->footer_bg : "";
}

function footer_textcolor(){
	return (cs_clients_info()) ? cs_clients_info()->footer_textcolor : "";
}

function footer_titlecolor(){
	return (cs_clients_info()) ? cs_clients_info()->footer_titlecolor : "";
}

function primaryColor_accent(){
	return (cs_clients_info()) ? cs_clients_info()->primaryColor_accent : "";
}

function fontChoice(){
	return (cs_clients_info()) ? cs_clients_info()->fontChoice : "";
}

function google_site_verification(){
	return (cs_clients_info()) ? cs_clients_info()->google_site_verification : "";
}

function c_default_order() {
	return cs_clients_info() && isset(cs_clients_info()->c_default_order) ? cs_clients_info()->c_default_order : 0;
}

function c_allow_sms() {
  return cs_clients_info() && isset(cs_clients_info()->c_allow_sms) ? cs_clients_info()->c_allow_sms : 0;
}


/*
 * ------------------------------------------------------
 *  Load and fetch the `cs_utilities` database.
 * ------------------------------------------------------
 */

 $cs_utilities = null;

 function get_cs_utilities(){
 	global $cs_utilities;
 	$sql = "SELECT * FROM cs_utilities LIMIT 1";
   $query = db_core()->query($sql, ini());
 	$cs_utilities = $query->num_rows() > 0 ? $query->row() : FALSE;
 	return $cs_utilities;
 }

 get_cs_utilities();

 function cs_utilities(){
 	global $cs_utilities;
 	return ($cs_utilities !== FALSE) ? $cs_utilities : FALSE;
 }

function powered_by(){
	return (cs_utilities()) ? cs_utilities()->powered_by : "";
}

function c_allowed_jcfulfillment_prefix(){
	$allowed = "";
	if(cs_utilities()){
		$allowed = explode(",",cs_utilities()->c_allowed_jcfulfillment_prefix);
	}else{
		$allowed = [];
	}
	return $allowed;
}

function cp_logo() {
	return base_url("/assets/img/".((cs_utilities()) ? cs_utilities()->cp_logo : ""));
}

function cp_logo_webp() {
	return base_url("/assets/img/webp/".pathinfo(((cs_utilities()) ? cs_utilities()->cp_logo : ""), PATHINFO_FILENAME).".webp");
}

function c_paypanda_link(){
	$c_paypanda_link = "";
	switch (ENVIRONMENT){
		case 'development': $c_paypanda_link = (cs_utilities()) ? cs_utilities()->c_paypanda_link_test  : ""; break;
		case 'testing'    : $c_paypanda_link = (cs_utilities()) ? cs_utilities()->c_paypanda_link_test  : ""; break;
		case 'production' : $c_paypanda_link = (cs_utilities()) ? cs_utilities()->c_paypanda_link_live  : ""; break;
	}

	return $c_paypanda_link;
}

function shop_main_announcement(){
	return (cs_clients_info()) ? cs_clients_info()->c_shop_main_announcement : "";
}

function c_404page(){
	return (cs_clients_info()) ? cs_clients_info()->c_404page : "";
}

function c_jcfulfillment_shopidno(){
	return (cs_clients_info()) ? cs_clients_info()->c_jcfulfillment_shopidno : "";
}

function get_s3_imgpath_upload(){
	switch (ENVIRONMENT){
		case 'development': $directory = (cs_clients_info()) ? cs_clients_info()->c_s3bucket_link_local : ""; break;
		case 'testing'    : $directory = (cs_clients_info()) ? cs_clients_info()->c_s3bucket_link_test  : ""; break;
		case 'production' : $directory = (cs_clients_info()) ? cs_clients_info()->c_s3bucket_link_live  : ""; break;
		default           : $directory = "";
	}
	return $directory;
}

function get_apiserver_link(){
	switch (ENVIRONMENT){
		case 'development': $directory = (cs_clients_info()) ? cs_clients_info()->c_apiserver_link_local : ""; break;
		case 'testing'    : $directory = (cs_clients_info()) ? cs_clients_info()->c_apiserver_link_test  : ""; break;
		case 'production' : $directory = (cs_clients_info()) ? cs_clients_info()->c_apiserver_link_live  : ""; break;
		default           : $directory = "";
	}
	return $directory;
}
