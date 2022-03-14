<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_cart');
        $this->load->model('user/model_products');
        $this->load->model('user/model_address');
        $this->load->model('user/model_orders');
    }

    public function index()
	{		
		$data['active_page'] = 'cart';
        $data['page_content'] = $this->load->view('user/cart/cart','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function add_to_cart($p_product_id = '',$p_variant_id = '', $size = '', $quantity = ''){ //accepst product id an variant if order now
        
        if($p_product_id == ''){
            $data = $this->input->post();
        }
        else{
            $data['product_id'] = $p_product_id;
            $data['variant_id'] = $p_variant_id; 
            $data['size'] = $size;
            $data['quantity'] = $quantity;            
        }        
        
        $en_product_id = $data['product_id'];
        $en_variant_id = $data['variant_id'];
        $product_id = en_dec('dec',$data['product_id']);        
        $variant_id = en_dec('dec',$data['variant_id']);

        $product = $this->model_products->get_product_info($product_id);
        $variant = $this->model_products->get_product_info($variant_id);
        unset($product['id']);
        
        $key = $en_variant_id;        
        $product['size'] = $data['size'];
        $product['qty'] = $data['quantity'];
        $product['variant_id'] = $en_variant_id;
        $product['amount'] = $variant['price'];    
        $product['is_included'] = 1;

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
        
        if($p_product_id == ''){
            generate_json($response);
        }
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
        $response['cart_items'] = $_SESSION['cart_items'];
        $response['cart_data'] = $_SESSION['cart'];   

        generate_json($response);        
    }

    public function modify_included(){
        $data = $this->input->post();
        
        $key = $data['target'];
        

        $is_included = 0;
        if($data['value'] == 'true'){
            $is_included = 1;
        }
        
        $_SESSION['cart'][$key]['is_included'] = $is_included;
        
        $response['success'] = true;          
        $response['cart_data'] = $_SESSION['cart'];

        generate_json($response);       
    }

    public function remove_from_cart(){
        $data = $this->input->post();

        $response['success'] = true;
        $response['message'] = 'Successfully removed';

        if($data['key'] == 'all'){
            $this->clear_cart();
            generate_json($response);
            die();
        }
        else{
            unset($_SESSION['cart'][$data['key']]);
            $total_qty = 0;
            foreach($_SESSION['cart'] as $key => $value){
                $total_qty += intval($_SESSION['cart'][$key]['quantity']);
            }
            $_SESSION['cart_items'] = $total_qty;
        }        

        if(isset($_SESSION['cart'])){
            if(sizeof($_SESSION['cart']) == 0){
                $this->clear_cart();
                generate_json($response);
                die();            
            }
        }

        $response['cart_items'] = $_SESSION['cart_items'];
        $response['cart_data'] = $_SESSION['cart'];

        generate_json($response);
    }

    public function clear_cart(){
        unset($_SESSION['cart']);
        unset($_SESSION['cart_items']);
    }

    public function checkout($product_id = '', $variant_id = '', $size = '', $quantity = ''){

        if($product_id != ''){
            if(isset($_SESSION['cart'])){
                if(sizeof($_SESSION['cart'])){
                    foreach($_SESSION['cart'] as $key => $value){
                        $_SESSION['cart'][$key]['is_included'] = 0; //reset selected item in cart
                    }
                }
            }            
            $this->add_to_cart($product_id, $variant_id, $size, $quantity);
        }

        $data['active_page'] = 'shop'; 
        $view_data['sub_active_page'] = 'checkout';

        $customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0;        
        $view_data['shipping_address'] = $this->model_address->get_shipping_address($customer_id);

        $view_data['payment_methods'] = $this->model_cart->get_payment_methods();
        $view_data['shipping_types'] = $this->model_cart->get_shipping_types();

        $data['page_content'] = $this->load->view('user/cart/checkout',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
    }

    public function place_order(){

        $data = $this->input->post();

        $customer_id = en_dec('dec',$_SESSION['customer_id']);
        $order_data = array();        

        $product_id = array();

        foreach($_SESSION['cart'] as $key => $value){
            if($value['is_included'] == 1){
                $order_data[$key] = $value;
                unset($_SESSION['cart'][$key]);
                array_push($product_id,en_dec('dec',$key));
            }
        }
        
        $payment_data = array(
            'payment_method_id:' => 1,
            'payment_method_name' => 'COD',
            'amount' => $data['total_amount'],
            'status_id' => 1, //pending
        );

        $rider = array(
            'name' => '3Mang Rider',
            'contact_no' => '09091901632',
            'vehicle_type' => 'motorcycle'
        );
        
        $data['shipping_data']['rider'] = $rider;

        $shipping_data = $data['shipping_data'];

        $deliver_amount = $data['delivery_amount'];
        $total_amount = $data['total_amount'];
    
        $customer_shipping_address = $this->model_address->get_shipping_address($customer_id);

        if($customer_shipping_address){
            //
        }
        else{
            $this->form_validation->validation_data = $data['shipping_data'];

            //declaration of form validations
            $this->form_validation->set_rules('address_category_id','Address Type','required');
            $this->form_validation->set_rules('full_name','Full Name','required');
            $this->form_validation->set_rules('contact_no','Contact Number','required');
            $this->form_validation->set_rules('province','Province','required');
            $this->form_validation->set_rules('city','City / Municipality','required');
            $this->form_validation->set_rules('barangay','Barangay','required');
            $this->form_validation->set_rules('zip_code','Zip Code','required');
            $this->form_validation->set_rules('address','Street Address','required');

            if($this->form_validation->run() == FALSE) {
                $response = array(
                'success'      => false,
                'message'      => 'Please check for field errors',
                'field_errors' => $this->form_validation->error_array(),              
                );

                generate_json($response);
                die();            
            }
        }        

        $order_id = $this->generate_order_id();

        $order = array(
            'customer_id' => $customer_id,
            'order_id' => $order_id,
            'product_id' => json_encode($product_id),
            'order_data' => json_encode($order_data),
            'payment_data' => json_encode($payment_data),
            'shipping_data' => json_encode($shipping_data),
            'total_amount' => floatval($data['total_amount']),
            'delivery_amount' => floatval($data['delivery_amount'])            
        );

        $id = $this->model_orders->insert_order($order);

        $total_qty = 0;
        if(sizeof($_SESSION['cart']) > 0){
            foreach($_SESSION['cart'] as $key => $value){
                $total_qty += intval($_SESSION['cart'][$key]['quantity']);
            }
            $_SESSION['cart_items'] = $total_qty;
        }
        else{
            $this->clear_cart();
        }

        $response['success'] = true;
        $response['message'] = 'Order successful';
        $response['order_id'] = en_dec('en',$order_id);
        $response['cart_items'] = $total_qty;

        generate_json($response);
    }    

    private function shipping_address_validation(){
        $validation = array(

        );
    }

    private function generate_order_id(){
        $order_id = '';
        do
        {           
            $order_id = getToken(9); //generator of alphanumeric characters --see utilities_helper on helpers
            $order_id = strtoupper($order_id);
            $res = $this->model_orders->check_unique($order_id);
        } 
        while( $res > 0 );
        return $order_id;
    }
    
}