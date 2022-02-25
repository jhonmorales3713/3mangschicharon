<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * ConversionRate Class
 *
 * Conversion Rate library for Gogome
 *
 * @category Libraries
 * @author Tristan Ross Lazaro
 * @link http://cloudpanda.com/
 * @version 1 
 */

class ConversionRate {
    private $CI;
    
    function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->database('default',TRUE);
    }

    function logConversionRate($type, $sys_shop){
        if($type == 'atc'){
            $atc  = 1;
            $rc   = 0;
            $ptp  = 0;
            $save = true;
        }
        else if($type == 'rc'){
            $atc  = 1;
            $rc   = 1;
            $ptp  = 0;
            $save = true;
        }
        else if($type == 'ptp'){
            $atc  = 1;
            $rc   = 1;
            $ptp  = 1;
            $save = true;
        }
        else{
            $save = false;
        }

        if($save){
            $query  = " INSERT INTO sys_conversion_rate (session_id, atc, rc, ptp, sys_shop, date_created, date_updated) 
                        VALUES (?, ?, ?, ?, ?, ?, ?) ";

            $params = array(
                $_SESSION['sesswebtraf'],
                $atc,
                $rc,
                $ptp,
                $sys_shop,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            );
            $result = $this->CI->db->query($query, $params);

            return $result; 
        }else{
            return false;
        }
    }
  
}