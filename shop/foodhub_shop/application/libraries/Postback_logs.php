<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Postback_logs {

    protected $CI;
    
    public $logs = array(
        'activity' => '',
        'details' => '',
        'action_by' => null,
        'reference_number' => '',
        'data' => null,
        'ip_address' => null,
        'referer' => null,
    );

    public function __construct(){
        $this->CI =& get_instance();
    }

    public function save_log(array $args){
        $sql = "INSERT INTO app_postback_logs (
                    activity,
                    details,
                    action_by,
                    reference_number,
                    data,
                    ip_address,
                    referer,
                    date_created
                ) VALUES (?,?,?,?,?,?,?,?) ";
        $bind_data = array(
            $args['activity'],
            $args['details'],
            $args['action_by'],
            $args['reference_number'],
            $args['data'],
            get_ip($_SERVER),
            isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '',
            todaytime()
        );
        $this->CI->db->query($sql, $bind_data);
        return $this->CI->db->insert_id();
    }    
}