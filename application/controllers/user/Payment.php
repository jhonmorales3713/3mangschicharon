<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_payment');
    }

    public function index(){
        
    }

    public function create_source(){
        $data = $this->input->post();

        $data['active_page'] = 'shop';

        $view_data['ref'] = [];

        $data['page_content'] = $this->load->view('user/payment/source',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);        
    }
    
}