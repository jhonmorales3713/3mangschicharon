<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms {

  protected $CI;
  protected $url;
  protected $username;
  protected $password;
  protected $originator;

  public function __construct(){
      $this->CI =& get_instance();
      $this->url = 'https://svr20.synermaxx.asia/vmobile/cloudpanda/api/sendnow.php';
      $this->username = 'cloudpandaapi';
      $this->password = 'f4b8a3dd9bf00cb0a7f1782975939d7d';
  }

  public function sendSMS($receiver, $mobilenum, $message, $originator) {
      $args = array(
        'username' => $this->username,
        'password' => $this->password,
        'mobilenum' => $mobilenum,
        'fullmesg' => $message,
        'originator' => $originator
      );

      // prepare post params
      $param = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($args),
          ),
      );

      $data = array(
        'receiver' => $receiver, //1=client,2=seller,3=branch
        'mobilenum' => $mobilenum,
        'message' => $message,
        'success' => 0,
        'data' => null,
      );

      // if(ENVIRONMENT == 'production'){
      //   $context  = stream_context_create($param);
      //   $content = file_get_contents($this->url, false, $context);
      //   $res = explode('|', $content);

      //   // Check if sms was sent succesfully
      //   // if ACK = success, if NACK = failed
      //   if ($res[0] == "ACK") {
      //     $data['success'] = 1;
      //   } else if ($res[1] == "NACK") {
      //     $data['success'] = 0;
      //   }

      //   $data['data'] = $content;
      //   $this->saveLog($data);

      //   return $content;
      // }else{
        if (c_allow_sms()) {
            $context  = stream_context_create($param);
            $content = file_get_contents($this->url, false, $context);
            $res = explode('|', $content);

            // Check if sms was sent succesfully
            // if ACK = success, if NACK = failed
            if ($res[0] == "ACK") {
              $data['success'] = 1;
            } else if ($res[1] == "NACK") {
              $data['success'] = 0;
            }

            $data['data'] = $content;
            $this->saveLog($data);

            return $content;
        }
      // }

  }

  private function saveLog($args) {
      $sql = "INSERT INTO app_sms_logs
              (receiver, mobile, message, success, data, date_created)
              VALUES
              (?, ?, ?, ?, ?, ?)";

      $bind_data = array(
        $args['receiver'],
        $args['mobilenum'],
        $args['message'],
        $args['success'],
        $args['data'],
        todaytime()
      );

      return $this->CI->db->query($sql, $bind_data);
  }

}
