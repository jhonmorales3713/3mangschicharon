<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_cart');
        $this->load->model('user/model_products');
        $this->load->model('user/model_address');
        $this->load->model('promotions/model_promotions');
        $this->load->model('user/model_orders');
    }

    public function index()
	{		
		$data['active_page'] = 'cart';
		$data['discounts'] = $this->model_promotions->get_ongoing();
        $data['page_content'] = $this->load->view('user/cart/cart',$data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function add_to_cart($p_product_id = '',$p_variant_id = '', $size = '', $quantity = '',$from_checkout = 'true'){ 
        if($p_product_id == ''){
            $data = $this->input->post();
            $from_checkout = isset($data['from_checkout'])?$data['from_checkout']:$from_checkout;
        }
        else{
            $data['product_id'] = $p_product_id;
            $data['variant_id'] = $p_variant_id; 
            $data['size'] = $size;
            $data['quantity'] = $quantity;            
        }        
        
        // print_r($data);
        // die();
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
            $data['max_checkout'] = ($product['max_qty_isset']==1)?$product['max_qty']:0;
            $data['max_checkout2'] = ($variant['max_qty_isset']==1)?$variant['max_qty']:0;
            if($data['max_checkout2'] != 0){
                $data['max_checkout'] =$data['max_checkout2'];
            }
            if(intval($_SESSION['cart'][$key]['quantity']) + intval($data['quantity'])>$data['max_checkout'] && $data['max_checkout'] > 0 && $from_checkout == 'false'){
                $response['success'] = false;
                $response['cart_items'] = $_SESSION['cart_items'];
                $response['message'] = 'Maximum of '.intval($data['max_checkout']).' quantity allowed to checkout per transaction exceeded.'; 
                generate_json($response);
                die();
            }else{
                $_SESSION['cart'][$key]['quantity'] = intval($_SESSION['cart'][$key]['quantity']) + intval($data['quantity']);     
            }
        }
        else{
            $data['max_checkout'] = ($product['max_qty_isset']==1)?$product['max_qty']:0;
            $data['max_checkout2'] = ($variant['max_qty_isset']==1)?$variant['max_qty']:0;
            if($data['max_checkout2'] != 0){
                $data['max_checkout'] =$data['max_checkout2'];
            }
            if(intval($data['quantity'])>$data['max_checkout'] && $data['max_checkout'] > 0){
                $response['success'] = false;
                $response['cart_items'] = $_SESSION['cart_items'];
                $response['message'] = 'Maximum of '.intval($data['max_checkout']).' quantity allowed to checkout per transaction exceeded.';   
                generate_json($response);
                die();
            }else{
                $_SESSION['cart'][$key] = $product;
                $_SESSION['cart'][$key]['quantity'] = intval($data['quantity']);    
            }        
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

        if(!isset($_SESSION['cart'])){
            header('Location:'.base_url());
        }
        if($product_id != '' && $this->session->userdata('first_time') == ''){
            if(isset($_SESSION['cart'])){
                if(sizeof($_SESSION['cart'])){
                    foreach($_SESSION['cart'] as $key => $value){
                        $_SESSION['cart'][$key]['is_included'] = 0; //reset selected item in cart
                    }
                }
            }          
            $this->add_to_cart($product_id, $variant_id, $size, $quantity);
        }
        if($this->session->userdata('first_time') == ''){
            $this->session->set_userdata('first_time', true);
        }
		$view_data['discounts'] = $this->model_promotions->get_ongoing();
        $data['active_page'] = 'shop'; 
        $view_data['sub_active_page'] = 'checkout';

        $view_data['customer_id'] = isset($_SESSION['customer_id']) ? en_dec('dec',$_SESSION['customer_id']) : 0;         
        $view_data['shipping_address'] = $this->model_address->get_shipping_address($view_data['customer_id']);

        foreach($view_data['shipping_address'] as $key => $value){
            $view_data[$key]['id'] = en_dec('en',$value['id']); //encrypt ids
        }

        $view_data['payment_methods'] = $this->model_cart->get_payment_methods();
        $view_data['cities'] = $this->model_orders->get_cities()->result_array();
        $view_data['shipping_types'] = $this->model_cart->get_shipping_types();        

        $data['page_content'] = $this->load->view('user/cart/checkout',$view_data,TRUE);
		$this->load->view('landing_template',$data,'',TRUE);
    }

    public function place_order(){

        $data = $this->input->post();        
        $id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';

        $customer_id = en_dec('dec',$id);
        
        $order_data = array();        

        $product_id = array();
        //check discount usage
		$discounts = $this->model_promotions->get_ongoing();
		$completed_orders = $this->model_orders->get_completed_orders();
        if(!isset($_SESSION['has_logged_in']) && $data['shipping_data']['email'] != ''){
            $email = $data['shipping_data']['email'];
        }
        else{
            $email = en_dec('dec',$_SESSION['email']);
        }
        //end checking of discount usage

        if(isset($_SESSION['cart'])){
            //set temporary cart
            $_SESSION['temp_cart'] = $_SESSION['cart'];
            foreach($_SESSION['cart'] as $key => $value){
                if($value['is_included'] == 1){
                    $order_data[$key] = $value;
                    $discount_info = Array();
                    foreach($discounts as $discount){
                        if(in_array($key,json_decode($discount['product_id']))){
                            $discount_info = $discount;
                        }
                        // $usage_max = $discount['usage_quantity'];
                        // foreach($completed_orders as $order){
                        //     if(strtolower(json_decode($order['shipping_data'])->email) == strtolower($email)){
                        //         foreach(json_decode($order['order_data']) as $product_id => $order_value){
                        //             if(in_array($order,json_decode($discount['product_id'])) && $product_id == $key){
                                        
                        //             }
                        //             print_r($order);
                        //         }
                        //     }
                        // }
                    }
                    $order_data[$key]['discount_info'] = $discount_info;
                    // print_r($order_data);
                    // die();
                    unset($_SESSION['cart'][$key]);
                    array_push($product_id,en_dec('dec',$key));
                }
            }
        }
        $payment_data = array(
            'payment_method_id:' => isset($data['payment_method']) ? $data['payment_method'] : 2,
            'payment_method_name' => $data['payment_keyword'] != ''?$data['payment_keyword'] : 'COD',
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

        // print_r($customer_shipping_address);
        // die();
        // if($customer_shipping_address){
        //     //
        // }
        // else{
        // }
        
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

        if(!isset($_SESSION['has_logged_in'])){
            $this->form_validation->set_rules('email','Email Address','required');
        }
        else{
            $shipping_data['email'] = en_dec('dec',$_SESSION['email']);
        }

        if($data['payment_method'] == ''){
            $this->form_validation->set_rules('payment_method_error','Payment Method','required');
        }

        if($this->form_validation->run() == FALSE) {
            $response = array(
            'success'      => false,
            'message'      => 'Please check for field errors',
            'field_errors' => $this->form_validation->error_array(),              
            );

            generate_json($response);
            die();            
        }
        if($order_data == '' || count($order_data) == 0){
            
            $response = array(
                'success'      => false,
                'msg'           => true,
                'message'      => 'Please Select Product/s to checkout',
                'field_errors' => $this->form_validation->error_array(),              
            );
            generate_json($response);
            die();            
        }
        $order_id = $this->generate_order_id();

        $order = array(
            'customer_id' => $customer_id,
            'order_id' => $order_id,
            'product_id' => json_encode($product_id),
            'order_data' => json_encode($order_data),
            'payment_data' => json_encode($payment_data),
            'shipping_data' => json_encode($shipping_data),
            'status_id'        => $data['payment_method'] == 1 ? 0 : 1,
            'total_amount' => floatval($data['total_amount']),
            'delivery_amount' => floatval($data['delivery_amount'])            
        );        
        // print_r($order_data);
        // die();

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
        $response['order_id'] = $order_id;
        $response['id'] = en_dec('en',$id);
        $response['cart_items'] = $total_qty;
        $order = $this->model_orders->orders_details($order_id);
        
        //print_r($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);
        // $this->sendProcessOrderEmail($order);
        

        $data2 = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $order_id
        );
        
        $data2['view'] = $this->load->view('email/order_processing',$data2,TRUE);
        $email = $shipping_data['email'];
        $subject = "Order #".$order_id." has been placed";
        $message = $this->load->view('email/templates/email_template',$data2,true);
		$this->send_email($email,$subject,$message);

        // print_r($message);
        // print_r($email);
        // print_r($this->email->print_debugger());
        // die();
        $data2 = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            '(reference_num)'=> $order_id,
            'customer_name'=> $data['shipping_data']['full_name']
        );
        $data2['view'] = $this->load->view('email/order_processing',$data2,TRUE);
        $email = 'teeseriesphilippines@gmail.com';
        $subject = "Order #".$order_id." has been placed by ".$data['shipping_data']['full_name'];
        $message = $this->load->view('email/templates/email_template',$data2,true);
		$this->send_email($email,$subject,$message);
        //gcash payment redirect
        $response['redirect_url'] = '';
        if($data['payment_method'] == 1){
            $_SESSION['order_data'] = $order;
            $_SESSION['current_order_id'] = en_dec('en',$id);
            $_SESSION['payment_keyword'] = $data['payment_keyword'];
            $response['redirect_url'] = base_url('checkout_gcash');
        }

        generate_json($response);
    }    

	function send_email($emailto,$subject,$message){
		
		$this->load->library('email');
        
		// $config = Array(
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => 'ssl://smtp.googlemail.com',
		// 	'smtp_port' => 465,
		// 	'smtp_user' => 'teeseriesphilippines@gmail.com',
		// 	'smtp_pass' => 'teeseriesph',
		// 	'charset' => 'utf-8',
		// 	'newline'   => "\r\n",
		// 	'wordwrap'=> TRUE,
		// 	'mailtype' => 'html'
		// );
        $this->load->library('email');
        if(strpos(base_url(),'3mangs.com')){
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => get_host(),
                'smtp_port' => 587,
                'smtp_user' => get_email(),
                'smtp_pass' => get_emailpassword(),
                'charset' => 'utf-8',
                'newline'   => "\r\n",
                'mailtype' => 'html'
            );
        }else{
            $config = Array(
            	'protocol' => 'smtp',
            	'smtp_host' => 'ssl://smtp.googlemail.com',
            	'smtp_port' => 465,
            	'smtp_user' => 'teeseriesphilippines@gmail.com',
            	'smtp_pass' => '@ugOct0810',
            	'charset' => 'utf-8',
            	'newline'   => "\r\n",
            	'wordwrap'=> TRUE,
            	'mailtype' => 'html'
            );
        }
		// $config = Array(
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => get_host(),
		// 	'smtp_port' => 587,
		// 	'smtp_user' => get_email(),
		// 	'smtp_pass' => get_emailpassword(),
		// 	'charset' => 'utf-8',
		// 	'newline'   => "\r\n",
		// 	'wordwrap'=> TRUE,
		// 	'mailtype' => 'html'
		// );
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");  
		$this->email->from('noreply@3mangs.com');
		$this->email->to($emailto);
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
        
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