<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
    public function __construct() {
        parent::__construct();  
		$this->load->model('user/model_home');
		$this->load->model('user/model_products');
		$this->load->model('promotions/model_promotions');
		$this->load->model('model_landing');
    }

	public function index()
	{		
		$data['active_page'] = 'home';
		$data['subnav'] = false;
		$data['has_search'] = true;
		$data['banners'] = $this->model_home->get_banners();
		
        $data['categories'] = $this->model_products->get_categories();
		$discounts = $this->model_promotions->get_ongoing();
        $products = $this->model_products->get_products();
		$data['discounts'] = array();
		$count = 0;
        foreach($discounts as $discount){
			$data['discounts'][$count]['discount_info'] = $discount;
			$data['products'] = array();
			foreach(json_decode($discount['product_id']) as $products){
				$product_id = en_dec('dec',$products);
				$product = $this->model_products->get_product_info($product_id);
				$product['variants'] = $this->model_products->get_variants($product_id);;
				$product['inventory'] = $this->model_products->get_inventorydetails($product_id);
				$product['id'] = en_dec('en',$product_id);  
				$product['discount'] = $discount;  
				array_push($data['products'],$product);
			}
			$data['discounts'][$count]['products'] = $data['products'];
			$count++;

		}
        $data['page_content'] = $this->load->view('user/home/index',$data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
	public function get_discounts(){

		
        $data['categories'] = $this->model_products->get_categories();
		$discounts = $this->model_promotions->get_ongoing();
        $products = $this->model_products->get_products();
		$data['discounts'] = array();
		$count = 0;
        foreach($discounts as $discount){
			$data['discounts'][$count]['discount_info'] = $discount;
			$data['products'] = array();
			foreach(json_decode($discount['product_id']) as $products){
				$product_id = en_dec('dec',$products);
				$product = $this->model_products->get_product_info($product_id);
				$product['variants'] = $this->model_products->get_variants($product_id);;
				$product['inventory'] = $this->model_products->get_inventorydetails($product_id);
				$product['id'] = en_dec('en',$product_id);  
				$product['discount'] = $discount;  
				array_push($data['products'],$product);
			}
			$data['discounts'][$count]['products'] = $data['products'];
			$count++;

		}
		echo json_encode($data);
	}
	public function contact_us()
	{		
		$data['active_page'] = 'contact_us';
		
        $data['page_content'] = $this->load->view('user/home/contact_us','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
	public function en_dec($enc_type,$str){
		echo json_encode(en_dec($enc_type,$str));
	}
    public function get_variants($id)
    {

        $row = $this->model_products->get_variants($id);
        $response = [
            'success' => true,
            'message' => $row,
            // 'images' => $this->model_products->getImagesfilename($id)->result_array()
        ];
        echo json_encode($response);
    }
	public function send_message(){
		$data = $this->input->post();

		if(isset($_SESSION['message_sent_count'])){
			if(intval($_SESSION['message_sent_count']) == 3){
				$response = array(
					'success'      => false,		
					'limit'		   => true,			
				);
			}
		}

		//declaration of form validations
		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('email','Email','required|valid_email');
		$this->form_validation->set_rules('message','Message','required');		

		if($this->form_validation->run() == FALSE) {
			$response = array(
			'success'      => false,
			'message'      => 'Please check for field errors',
			'field_errors' => $this->form_validation->error_array(),              
			);

			generate_json($response);
			die();            
		}

		$info_message = array(
			'name' => $data['name'],
			'email' => $data['email'],
			'message' => $data['message'],
			'date_created' => datetime(),
		);

		$message_id = $this->model_landing->insert_message($info_message);

		if(isset($_SESSION['message_sent_count'])){
			$_SESSION['message_sent_count'] = intval($_SESSION['message_sent_count']) + 1;
		}
		else{
			$_SESSION['message_sent_count'] = 1;
		}

		$response['id'] = en_dec('en',$message_id);
		$response['success'] = true;
		$response['message'] = 'Message send succesfully';

		generate_json($response);		
	}
	
}
