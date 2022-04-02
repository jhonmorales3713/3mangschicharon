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


function get_company_name(){
	return (cs_clients_info()) ? cs_clients_info()->name : "";
}

function get_shortened_name(){
	return (cs_clients_info()) ? cs_clients_info()->c_shortened_name : "";
}

function get_shop_url($path){
	return 'shop url in core helper';
}


function get_s3_imgpath_upload(){
	$directory = base_url('assets/uploads');
	return $directory;
}
function get_tag_line(){
	return (cs_clients_info()) ? cs_clients_info()->tagline : "";
}
function get_icon(){
	return (cs_clients_info()) ? cs_clients_info()->c_favicon : "";
}
function get_placeholder(){
	return (cs_clients_info()) ? cs_clients_info()->c_placeholder : "";
}
function get_logo(){
	return (cs_clients_info()) ? cs_clients_info()->c_main_logo : "";
}
function get_bg(){
	return (cs_clients_info()) ? cs_clients_info()->c_background_img : "";
}
function get_telephone(){
	return (cs_clients_info()) ? cs_clients_info()->c_telephone : "";
}
function get_address(){
	return (cs_clients_info()) ? cs_clients_info()->c_address : "";
}

function fb_link(){
	return (cs_clients_info()) ? cs_clients_info()->facebook_link : "";
}
function youtube_link(){
	return (cs_clients_info()) ? cs_clients_info()->youtube_link : "";
}
function ig_link(){
	return (cs_clients_info()) ? cs_clients_info()->instagram_link : "";
}
function faqs_link(){
	return (cs_clients_info()) ? cs_clients_info()->c_faqs : "";
}

function get_company_email(){
	return (cs_clients_info()) ? cs_clients_info()->c_email : "";
}

function get_404(){
	return (cs_clients_info()) ? cs_clients_info()->c_404 : "";
}

function get_company_phone(){
	return (cs_clients_info()) ? cs_clients_info()->c_phone : "";
}
function get_email(){
	return (cs_clients_info()) ? cs_clients_info()->c_email : "";
}
function get_host(){
	return (cs_clients_info()) ? cs_clients_info()->c_host : "";
}
function get_emailpassword(){
	return (cs_clients_info()) ? cs_clients_info()->c_password : "";
}

function cs_clients_info(){
	$sql = "SELECT * FROM cs_clients_info WHERE id_key = ? LIMIT 1";
    $query = db_core()->query($sql, ini());

    return $query->num_rows() > 0 ? $query->row() : FALSE;
}
?>