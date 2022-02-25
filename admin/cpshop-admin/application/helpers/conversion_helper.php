<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function conversion_rate(){
  $conversion = 1;
  $that =& get_instance();
  if(isset($that->session->app_currency) && !empty($that->session->app_currency)){
    $app_currency = $that->session->app_currency;
    $conversion = $app_currency['exchangerate_n_to_php'];
  }
  return $conversion;
}

function currency(){
  $currency = 'PHP';
  $that =& get_instance();
  if(isset($that->session->app_currency) && !empty($that->session->app_currency)){
    $app_currency = $that->session->app_currency;
    $currency = $app_currency['currency_symbol'];
  }
  return $currency;
}

function country_code(){
  $country_code = 'PH';
  $that =& get_instance();
  if(isset($that->session->app_currency) && !empty($that->session->app_currency)){
    $app_currency = $that->session->app_currency;
    $country_code = $app_currency['country_code'];
  }
  return $country_code;
}

function country_name(){
  $country_name = 'PHILIPPINES';
  $that =& get_instance();
  if(isset($that->session->app_currency) && !empty($that->session->app_currency)){
    $app_currency = $that->session->app_currency;
    $country_name = $app_currency['country_name'];
  }
  return $country_name;
}

function shipping_rate(){
  $shipping_array = array(
    "shippingfee" => 0.00,
    "from_dts" => 1,
    "to_dts" => 1
  );
  $that =& get_instance();
  if(isset($that->session->app_currency) && !empty($that->session->app_currency)){
    $app_currency = $that->session->app_currency;
    $shipping_array['shippingfee'] = $app_currency['shippingfee'];
    $shipping_array['from_dts'] = $app_currency['from_dts'];
    $shipping_array['to_dts'] = $app_currency['to_dts'];
  }
  return $shipping_array;
}

function format_number($number){
  return floor($number * 100) / 100;
}

function numberFormatPrecision2($number, $precision = 2, $separator = '.'){
    $numberParts = explode($separator, $number);
    $response = $numberParts[0];
    if (count($numberParts)>1 && $precision > 0) {
        $response .= $separator;
        $response .= substr($numberParts[1], 0, $precision);
    }
    return $response;
}

function php_to_n($number,$rate = false, $round = false){
  $converted = 0;
  $rate = ($rate) ? $rate : conversion_rate();
  $converted = $number / $rate;
  $pieces = explode('.',$converted);
  if(isset($pieces[1])){
    $decimal = $pieces[1];
    if(strlen($decimal) > 2){
      $converted = numberFormatPrecision2($converted) + 0.01;
    }else{
      $converted = numberFormatPrecision2($converted);
    }
  }

  if($round){
    return round($converted,2);
  }

  return $converted;
}

function n_to_php($number,$rate = false){
  $converted = 0;
  $rate = ($rate) ? $rate : conversion_rate();
  $converted = $number * $rate;
  return round($converted,2);
}
