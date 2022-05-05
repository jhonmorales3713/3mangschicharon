<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();        
		$this->load->model('promotions/model_promotions');
        $this->load->model('user/model_products');
        $this->load->model('orders/model_orders');  
        $this->load->model('model_products','admin_product');
    }

    public function index()
	{		
		$data['active_page'] = 'products';
		
        $data['page_content'] = $this->load->view('user/home/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function generate_product_number_of_sold($product){
        $orders = $this->model_orders->order_table_data();
        $times_sold = 0;
        foreach($orders as $id => $order){
            foreach(json_decode($order['order_data']) as $product_id => $order_data){
                if($product_id == $product){
                    $times_sold++;
                }
            }

        }
		return $times_sold;
    }

    public function products($en_product_id){
        
        $data['active_page'] = 'shop';
        
        $product_id = en_dec('dec',$en_product_id);
        $product = $this->model_products->get_product_info($product_id);
        $product['inventory'] = $this->admin_product->get_inventorydetails($product_id);
        $product['variants'] = $this->model_products->get_variants($product_id);
        $product['id'] = en_dec('en',$product['id']);
        
        $times_sold = 0;
        foreach($product['variants'] as $variant){
            $times_sold+=($this->generate_product_number_of_sold(en_dec('en',$variant['id'])));
            // print_r(en_dec('en',$variant['id']).'//');
        }
        $product['sold_count']=$times_sold;
        
        $view_data['product'] = $product;
		$discounts = $this->model_promotions->get_ongoing();
        $view_data['discounts'] = $discounts;
        $data['page_content'] = $this->load->view('user/products/view_product',$view_data,TRUE);
        $this->load->view('landing_template',$data,'',TRUE);
    }

}