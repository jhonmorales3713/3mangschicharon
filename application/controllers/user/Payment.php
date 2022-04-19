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
        $source_amount = isset($_SESSION['order_data'][0]['total_amount'])?$_SESSION['order_data'][0]['total_amount']:0; 
        $order_id = isset($_SESSION['order_data'][0]['order_id'])?$_SESSION['order_data'][0]['order_id']:-1;    
        $current_order_id = isset($_SESSION['current_order_id'])?$_SESSION['current_order_id']:-1;
        $keyword = isset($_SESSION['payment_keyword'])?$_SESSION['payment_keyword']:-1;    
        
        //check if already made a source
        if($this->model_payment->check_order_id_exists($order_id) == 0){
            //set params for creating source
            $amount = intval(floatval($source_amount)*100);
            $success_url = base_url('order_confirmation/'.$current_order_id);
            $failed_url = base_url('payment_failed');

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
            
            $payment_id = $this->model_payment->save_source_data($source_data_arr,$_SESSION['order_data'][0]);
        } 
        else{
            
            //retrieve payment record if already exists
            $payment = $this->model_payment->get_payment_data($current_order_id);
            $source_data_arr = json_decode($payment['source_data'],TRUE);
            
        }

        $view_data['source_data'] = $source_data_arr;
        $view_data['checkout_url'] = $source_data_arr['data']['attributes']['redirect']['checkout_url'];
        $_SESSION['ref_no'] = $source_data_arr['data']['id'];

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

    //capture paymongo data (authorized, paid and failed)
    public function paymongo_capture(){        

        header('Content-Type: application/json');
        $request = file_get_contents('php://input');
        $payload = json_decode($request, true);
        $type = $payload['data']['attributes']['type'];

        //If event type is source.chargeable, call the createPayment API
        if ($type == 'source.chargeable') {            

            $amount = $payload['data']['attributes']['data']['attributes']['amount'];
            $id = $payload['data']['attributes']['data']['id'];
            $description = "GCash Payment";
            $curl = curl_init();
            $fields = array("data" => array ("attributes" => array ("amount" => $amount, "source" => array ("id" => $id, "type" => "source"), "currency" => "PHP", "description" => $description)));
            $jsonFields = json_encode($fields);
                
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.paymongo.com/v1/payments",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $jsonFields,
                CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                //Input your encoded API keys below for authorization
                "Authorization: Basic c2tfbGl2ZV9FdmhzbVZ1TndyRk5QcDVReERGUjhwZ0w6c2tfbGl2ZV9FdmhzbVZ1TndyRk5QcDVReERGUjhwZ0w=" ,
                "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            //Log the response
            //$fp = file_put_contents( 'test.log', $response );
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
                //Log the response
                //$fp = file_put_contents( 'test.log', $err );
            } else {
                echo $response;
            }

            //update source data when source.chargeable
            $this->model_payment->update_source_data($payload);
            echo 'This is source chargeable';
        }

        else if($type == 'payment.paid'){
            echo 'This is payment paid';
        }

        else if($type == 'payment.failed'){
            echo 'This is payment failed';
        }
    }
    
}