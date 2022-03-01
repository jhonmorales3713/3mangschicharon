<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();    
        
        $this->load->model('user/model_products');
    }

    public function index()
	{		
		$data['active_page'] = 'products';
		
        $data['page_content'] = $this->load->view('user/home/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

}