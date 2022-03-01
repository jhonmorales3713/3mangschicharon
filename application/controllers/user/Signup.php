<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(){
        $data['active_page'] = 'signup';		
        $data['page_content'] = $this->load->view('user/signup/signup_form','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
        
    }


}