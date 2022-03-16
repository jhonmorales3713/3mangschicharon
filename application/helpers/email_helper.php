<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function __construct()
{
    $this->CI = get_instance();
    $this->CI->load->database('default',TRUE);
}
function send_email($email,$subject,$message){
    
    $config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'teeseriesphilippines@gmail.com',
        'smtp_pass' => 'teeseriesph',
        'charset' => 'utf-8',
        'newline'   => "\r\n",
        'wordwrap'=> TRUE,
        'mailtype' => 'html'
    );
    $this->email->initialize($config);
    $this->email->set_newline("\r\n");  
    $this->email->from('ulul@gmail.com',get_company_name());
    $this->email->to($email);
    $this->email->subject($subject);
    $this->email->message($message);
    $this->email->send();
}
?>