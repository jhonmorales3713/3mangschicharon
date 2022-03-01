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


function get_s3_imgpath_upload(){
	$directory = base_url('assets/uploads');
	return $directory;
}
function get_tag_line(){
	return (cs_clients_info()) ? cs_clients_info()->tag_line : "";
}

function get_company_email(){
	return (cs_clients_info()) ? cs_clients_info()->c_email : "";
}

function get_company_phone(){
	return (cs_clients_info()) ? cs_clients_info()->c_phone : "";
}

function cs_clients_info(){
	$sql = "SELECT * FROM cs_clients_info WHERE id_key = ? LIMIT 1";
    $query = db_core()->query($sql, ini());

    return $query->num_rows() > 0 ? $query->row() : FALSE;
}
?>