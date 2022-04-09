<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_payment');
    }

    public function index(){
        
    }

    public function create_source(){
        $data = $this->input->post();

        $data['active_page'] = 'shop';

        $view_data['ref'] = [];

        require_once('vendor/autoload.php');
        $client = new \GuzzleHttp\Client();

        //original amount
        $source_amount = $_SESSION['order_data']['total_amount'];

        //set params for creating source
        $amount = intval(floatval($_SESSION['order_data']['total_amount'])*100);
        $success_url = base_url('order_confirmation/'.$_SESSION['current_order_id']);
        $failed_url = base_url('payment_failed');
        $keyword = $_SESSION['payment_keyword'];

        $response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
            'body' => '{"data":{"attributes":{"amount":'.$amount.',"redirect":{"success":"'.$success_url.'","failed":"'.$failed_url.'"},"type":"'.$keyword.'","currency":"PHP"}}}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic cGtfbGl2ZV9rOXJDeXRwQWViZWcyS0dOZTFhWnB5aHY6c2tfbGl2ZV9FdmhzbVZ1TndyRk5QcDVReERGUjhwZ0w=',
                'Content-Type' => 'application/json',
            ],
        ]);

        $source_data = $response->getBody();
        $source_data_arr = json_decode($source_data,TRUE);

        $_SESSION['ref_no'] = $source_data_arr['data']['id'];

        $payment_id = $this->model_payment->save_source_data($source_data_arr,$_SESSION['order_data']);       

        $view_data['source_data'] = $source_data_arr;
        $view_data['checkout_url'] = $source_data_arr['data']['attributes']['redirect']['checkout_url'];

        $data['page_content'] = $this->load->view('user/payment/source',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);        
    }

    public function create_payment(){

    }

    public function payment_failed(){
        $data = $this->input->post();        

        $data['active_page'] = 'shop';

        $view_data['ref'] = [];

        $_SESSION['cart'] = $_SESSION['temp_cart'];
        $total_qty = 0;
        foreach($_SESSION['cart'] as $key => $value){
            $total_qty += intval($_SESSION['cart'][$key]['quantity']);
        }
        $_SESSION['cart_items'] = $total_qty;

        $data['page_content'] = $this->load->view('user/payment/payment_failed',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);    
    }

    public function capture(){        

        
        echo 'CAPTURED - sample muna - wait ka lng... ';
    }
    
}