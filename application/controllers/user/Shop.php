<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_products');  
        $this->load->model('promotions/model_promotions');  
    }
    
    public function index()
	{		
		$data['active_page'] = 'shop';        
        $data['has_search'] = true;
        $view_data['categories'] = $this->model_products->get_categories();
		
        $products = $this->model_products->get_products();

        $view_data['products'] = array();
		$discounts = $this->model_promotions->get_ongoing();
        foreach($products as $product){
            $product['variants'] = $this->model_products->get_variants($product['id']);
            $product['inventory'] = $this->model_products->get_inventorydetails($product['id']);
            $product['id'] = en_dec('en',$product['id']);  
            array_push($view_data['products'],$product);
        }

		$count = 0;
        foreach($discounts as $discount){
			$view_data['discounts'][$count]['discount_info'] = $discount;
			$view_data['products_promo'] = array();
			foreach(json_decode($discount['product_id']) as $products){
				$product_id = en_dec('dec',$products);
				$product = $this->model_products->get_product_info($product_id);
				$product['variants'] = $this->model_products->get_variants($product_id);;
				$product['inventory'] = $this->model_products->get_inventorydetails($product_id);
				$product['id'] = en_dec('en',$product_id);  
				$product['discount'] = $discount;  
				array_push($view_data['products_promo'],$product);
			}
			$view_data['discounts'][$count]['products'] = $view_data['products_promo'];
			$count++;

		}
        // print_r($view_data);
        $data['page_content'] = $this->load->view('user/shop/index',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    
}