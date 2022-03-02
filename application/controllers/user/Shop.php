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
        $data['has_search'] = true;
        $view_data['categories'] = $this->model_products->get_categories();
		
        $products = $this->model_products->get_products();

        $view_data['products'] = array();
        foreach($products as $product){            
            $product['id'] = en_dec('en',$product['id']); 
            array_push($view_data['products'],$product);
        }

        $data['page_content'] = $this->load->view('user/shop/index',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}    
    
}