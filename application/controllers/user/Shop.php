<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_products');
    }
    
    public function index()
	{		
		$data['active_page'] = 'shop';
		$view_data['products'] = $this->model_products->get_products();
        $data['page_content'] = $this->load->view('user/shop/index',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    


}