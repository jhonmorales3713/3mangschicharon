<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_cart');
        $this->load->model('user/model_products');
    }

    public function index()
	{		
		$data['active_page'] = 'cart';
        $data['page_content'] = $this->load->view('user/cart/cart','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function add_to_cart(){
        $data = $this->input->post();
        
        $en_product_id = $data['product_id'];
        $product_id = en_dec('dec',$data['product_id']);
        $product = $this->model_products->get_product_info($product_id);        
        unset($product['id']);
        
        $key = $en_product_id.$data['size'];
        $product['size'] = $data['size'];
        $product['qty'] = $data['quantity'];

        if($data['size'] == 'S'){
            $product['amount'] = $product['price_small'];
        }
        else if($data['size'] == 'L'){
            $product['amount'] = $product['price_large'];
        }
        else{
            $product['amount'] = $product['price'];
        }        

        if(!isset($_SESSION['cart'])){
            $_SESSION['cart'] = [];
        }

        if(!isset($_SESSION['cart_items'])){
            $_SESSION['cart_items'] = 0;
        }

        if(isset($_SESSION['cart'][$key])){            
            $_SESSION['cart'][$key]['quantity'] = intval($_SESSION['cart'][$key]['quantity']) + intval($data['quantity']);     
        }
        else{
            $_SESSION['cart'][$key] = $product;
            $_SESSION['cart'][$key]['quantity'] = intval($data['quantity']);            
        }        

        $total_qty = 0;
        foreach($_SESSION['cart'] as $key => $value){
            $total_qty += intval($_SESSION['cart'][$key]['quantity']);
        }
        $_SESSION['cart_items'] = $total_qty;

        $response['success'] = true;
        $response['cart_items'] = $_SESSION['cart_items'];
        $response['message'] = 'Successfully added to cart';        

        generate_json($response);
    }

    public function modify_quantity(){
        $data = $this->input->post();
        
        $key = $data['target'];
        
        $_SESSION['cart'][$key]['quantity'] = intval($data['quantity']);

        $total_qty = 0;
        foreach($_SESSION['cart'] as $key => $value){
            $total_qty += intval($_SESSION['cart'][$key]['quantity']);
        }
        $_SESSION['cart_items'] = $total_qty;
        
        $response['success'] = true;
        $response['quantity'] = $_SESSION['cart'][$key]['quantity'];        

        generate_json($response);        
    }

    public function clear_cart(){
        $data = $this->input->post();

        unset($_SESSION['cart']);
        unset($_SESSION['cart_items']);

        $response['success'] = true;
        $response['cart_items'] = 0;
        $response['message'] = 'Successfully cleared cart';

        generate_json($response);
    }

    public function checkout(){
        $data['active_page'] = 'cart';
        $data['page_content'] = $this->load->view('user/cart/checkout','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
    }
    
}