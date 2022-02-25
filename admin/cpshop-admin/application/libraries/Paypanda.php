<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Paypanda Class
 *
 * This is implementation of Paypanda to your merchant webpage.
 *
 * @category Libraries
 * @author Rex Diamante
 * @link http://cloudpanda.com/
 * @version 1 
 */

class Paypanda {
    private $CI;
    
    function __construct()
    {
        $this->CI = get_instance();
    }

    function get_merchant_key()
    {
      return "CPSHOPLOCAL";
    }

    function get_secret_key()
    {
      return "HF67SBXB";
    }
  
    function paypanda_signature($source_string)
    {
      return base64_encode(hash('sha256',$this->binarize($source_string)));
    }

    // this part may be changed to avoid conflict with ipay88 for exact same signature generation algorithm

    function binarize($source_string)
    {
        $binary = "";

        for ($a=0;$a<strlen($source_string);$a++)
        {
            $binary .= chr(hexdec(substr($source_string,$a,2)));
        }

        return $binary;
    }

    function generate_signature($ref_num, $amount){
      $secret_key = $this->get_secret_key();
      $merchant_key = $this->get_merchant_key();
      $amount = number_format($amount,0);
      $source = $secret_key.$merchant_key.$ref_num.$amount."PHP";

      $source = str_replace('.', '', $source);
      $source = str_replace(',', '', $source);
      return $this->paypanda_signature($source);
    }

    function validate_signature($ref_num, $paypanda_ref, $payment_status, $amount){
      $secret_key = $this->get_secret_key();
      $amount = number_format($amount,0);
      $source = $ref_num.$paypanda_ref.$payment_status.$amount.$secret_key;

      $source = str_replace('.', '', $source);
      $source = str_replace(',', '', $source);
      return $this->paypanda_signature($source);
    }

    function validate_manual_signature($ref_num, $manual_ref, $payment_status, $amount, $trigger){
      $amount = number_format($amount,0);
      $source = $ref_num.$manual_ref.$payment_status.$amount.$trigger;

      $source = str_replace('.', '', $source);
      $source = str_replace(',', '', $source);
      return $this->paypanda_signature($source);
    }
}