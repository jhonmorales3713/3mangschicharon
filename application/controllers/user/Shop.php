<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_products');  
        $this->load->model('orders/model_orders');  
        $this->load->model('promotions/model_promotions');  
    }
    
    public function generate_product_number_of_sold($product){
        $orders = $this->model_orders->order_table_data();
        $times_sold = 0;
        foreach($orders as $id => $order){
            foreach(json_decode($order['order_data']) as $product_id => $order_data){
                if($product_id == $product){
                    $times_sold+=$order_data->qty;
                }
            }

        }
		return $times_sold;
    }
    public function search_category(){
        
        $this->session->set_userdata('search_category',$this->input->post('categories'));
    }
    public function index()
	{		
        // print_r(basename($_SERVER['REQUEST_URI']));
        // print_r();
        // die();
		$data['active_page'] = 'shop';        
        $data['has_search'] = true;
        $data['searchbox'] = isset($_GET['search'])?$_GET['search']:'';
        $view_data['categories'] = $this->model_products->get_categories();
        $view_data['search_categories'] = $this->session->userdata('search_category');
		
        $products = $this->model_products->get_products();

        $view_data['products'] = array();
		$discounts = $this->model_promotions->get_ongoing();
        foreach($products as $product){
            if($data['searchbox']!= ''){
                // print_r(str_contains(strtolower($product['name']),strtolower($data['searchbox'])));
                // die();
                if(str_contains(strtolower($product['name']),strtolower($data['searchbox']))){
    
                    $product['variants'] = $this->model_products->get_variants($product['id']);
                    $product['inventory'] = $this->model_products->get_inventorydetails($product['id']);
                    $product['id'] = en_dec('en',$product['id']);  
                    $times_sold = 0;
                    foreach($product['variants'] as $variant){
                        $times_sold+=($this->generate_product_number_of_sold(en_dec('en',$variant['id'])));
                        // print_r(en_dec('en',$variant['id']).'//');
                    }
                    $product['sold_count']=$times_sold;
                    array_push($view_data['products'],$product);
                    
                }
            }else{
    
                $product['variants'] = $this->model_products->get_variants($product['id']);
                $product['inventory'] = $this->model_products->get_inventorydetails($product['id']);
                $product['id'] = en_dec('en',$product['id']);  
                $times_sold = 0;
                foreach($product['variants'] as $variant){
                    $times_sold+=($this->generate_product_number_of_sold(en_dec('en',$variant['id'])));
                    // print_r(en_dec('en',$variant['id']).'//');
                }
                $product['sold_count']=$times_sold;
                array_push($view_data['products'],$product);
            }
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