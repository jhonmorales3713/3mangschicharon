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

        $data['page_content'] = $this->load->view('user/payment/source',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);        
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
        
            require_once('vendor/autoload.php');

            $client = new \GuzzleHttp\Client();

            $response = $client->request('POST', 'https://api.paymongo.com/v1/webhooks', [
            'body' => '{"data":{"attributes":{"events":["source.chargeable"],"url":"http://localhost/user/payment/capture"}}}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic c2tfbGl2ZV9FdmhzbVZ1TndyRk5QcDVReERGUjhwZ0w6c2tfbGl2ZV9FdmhzbVZ1TndyRk5QcDVReERGUjhwZ0w=',
                'Content-Type' => 'application/json',
            ],
            ]);

            echo $response->getBody();

    }
    
}