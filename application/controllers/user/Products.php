<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_products');
        $this->load->model('model_products','admin_product');
    }

    public function index()
	{		
		$data['active_page'] = 'products';
		
        $data['page_content'] = $this->load->view('user/home/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function products($en_product_id){
        
        $data['active_page'] = 'shop';
        
        $product_id = en_dec('dec',$en_product_id);
        $product = $this->model_products->get_product_info($product_id);
        $product['inventory'] = $this->admin_product->get_inventorydetails($product_id);
        $product['variants'] = $this->model_products->get_variants($product_id);
        $product['id'] = en_dec('en',$product['id']);
        $view_data['product'] = $product;

        $data['page_content'] = $this->load->view('user/products/view_product',$view_data,TRUE);
        $this->load->view('landing_template',$data,'',TRUE);
    }

}