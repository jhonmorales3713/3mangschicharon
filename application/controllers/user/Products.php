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

    public function products($en_product_id){
        
        $data['active_page'] = 'products';
        
        $product_id = en_dec('dec',$en_product_id);
        $view_data['product'] = $this->model_products->get_product_info($product_id);

        $data['page_content'] = $this->load->view('user/products/view_product',$view_data,TRUE);
        $this->load->view('landing_template',$data,'',TRUE);
    }

}