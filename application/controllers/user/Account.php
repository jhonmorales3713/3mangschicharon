<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_customers');
    }

    public function index()
	{		
		$data['active_page'] = 'about';
		
        $data['page_content'] = $this->load->view('user/home/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function profile(){
        $customer_id = en_dec('dec',$this->session->customer_id);
        $email = en_dec('dec',$this->session->email);

        $customer_data = $this->model_customers->get_customer_by_email($email); 

        $view_data['customer_data'] = $customer_data;

        $data['active_page'] = 'profile';
		
        $data['page_content'] = $this->load->view('user/account/profile',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
    }
    
}