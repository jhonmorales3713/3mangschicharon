<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    public function __construct() {
        parent::__construct();    
        $this->load->model('user/model_orders');
    }

    public function orders($order_id = ""){
        $this->isLoggedIn();
        $customer_id = en_dec('dec',$this->session->customer_id);

        if($order_id != ""){
            $order_id = en_dec('dec',$order_id);
        }        

        $orders = $this->model_orders->get_orders($customer_id,$order_id);

        foreach($orders as $key => $value){
            $orders[$key]['id'] = en_dec('en',$value['id']);
        }

        $view_data['orders'] = $orders;

        $data['active_page'] = 'orders';

        if($order_id == ""){
            $data['page_content'] = $this->load->view('user/orders/index',$view_data,TRUE);     
            $this->load->view('landing_template',$data,'',TRUE);
        }
        else{                        
            $data['page_content'] = $this->load->view('user/orders/order_details',$view_data,TRUE);
            $this->load->view('landing_template',$data,'',TRUE);
        }        
    }
    public function isLoggedIn()
    {
        if ($this->session->userdata('has_logged_in')=='') {
            header("location:" . base_url('/signout'));
        }
    }
    public function rate_order(){
        $data = $this->input->post();
        // print_r($data);
        if($data['rating'] == 0){
            $response = Array(
                'success' => false,
                'message' => 'Rating is required'
            );
        }else{
            $response = Array(
                'success' => true,
                'message' => 'Order Successfully rated.'
            );
        }
        $rating_data = Array(
            'rating' => $data['rating'],
            'message' => $data['message'] 
        );
        $this->model_orders->rate_order(en_dec('dec',$data['id']),json_encode($rating_data));
        echo json_encode($response);
    }
    public function details($order_id){
        $order_id = en_dec('dec',$order_id);

        $order = $this->model_orders->get_order_info($order_id);

        $view_data['order_id'] = $order['order_id'];
        $view_data['order_data'] = json_decode($order['order_data'],true);
        $view_data['payment_data'] = json_decode($order['payment_data'],true);

		$data['active_page'] = 'shop';		
        $data['page_content'] = $this->load->view('user/orders/receipt',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
    }

    public function order_confirmation($order_id){        

        $id = en_dec('dec',$order_id);

        $order = $this->model_orders->get_order_info($id);

        $view_data['id'] = $order_id;
        $view_data['order_id'] = $order['order_id'];
        $view_data['order_data'] = json_decode($order['order_data'],true);
        $view_data['payment_data'] = json_decode($order['payment_data'],true);
        $this->model_orders->update_payment($order['order_id']);
        
        if(isset($_SESSION['cart'])){
            //set temporary cart
            $_SESSION['temp_cart'] = $_SESSION['cart'];
            foreach($_SESSION['cart'] as $key => $value){
                if($value['is_included'] == 1){
                    unset($_SESSION['cart'][$key]);
                }
            }
        }
		$data['active_page'] = 'shop';		
        $data['page_content'] = $this->load->view('user/orders/order_confirmation',$view_data,TRUE);
		$this->load->view('landing_template',$data,'',TRUE);
    }

    public function receipt($order_id)
	{	
        $order_id = en_dec('dec',$order_id);

        $order = $this->model_orders->get_order_info($order_id);

        $view_data['order_id'] = $order['order_id'];
        $view_data['order_data'] = json_decode($order['order_data'],true);
        $view_data['payment_data'] = json_decode($order['payment_data'],true);

		$data['active_page'] = 'shop';		
        $data['page_content'] = $this->load->view('user/orders/receipt',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    
}