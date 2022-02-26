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


function cs_clients_info(){
	$sql = "SELECT * FROM cs_clients_info WHERE id_key = ? LIMIT 1";
    $query = db_core()->query($sql, ini());

    return $query->num_rows() > 0 ? $query->row() : FALSE;
}
?>