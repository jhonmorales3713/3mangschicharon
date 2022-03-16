<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function send_email($emailto,$subject,$message){
    
    $this->load->library('email');
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
    $this->email->to($emailto);
    $this->email->subject($subject);
    $this->email->message($message);
    $this->email->send();
}
?>