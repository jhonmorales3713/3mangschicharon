<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
    public function __construct() {
        parent::__construct();  
		$this->load->model('user/model_home');
		$this->load->model('user/model_products');
		$this->load->model('orders/model_orders');
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
        $products2 = $this->model_products->get_products();
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
        $view_data['top_products'] = array();

		$discounts = $this->model_promotions->get_ongoing();
        foreach($products2 as $product){
            $product['variants'] = $this->model_products->get_variants($product['id']);
            $product['inventory'] = $this->model_products->get_inventorydetails($product['id']);
            $product['id'] = en_dec('en',$product['id']);  
			$times_sold = 0;
			foreach($product['variants'] as $variant){
				$times_sold+=($this->generate_product_number_of_sold(en_dec('en',$variant['id'])));
				// print_r(en_dec('en',$variant['id']).'//');
			}
			$product['sold_count']=$times_sold;
			if($times_sold > 0){
				array_push($view_data['top_products'],$product);
			}
        }
	
		function cmp($a, $b) {
			return strcmp($a['sold_count'], $b['sold_count']);
		}
		
		usort($view_data['top_products'], "cmp");
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
		$data['top_products'] = $view_data['top_products'];
        $data['page_content'] = $this->load->view('user/home/index',$data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
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

        $email = $data['email'];
		$data['email'] = $email;
		$data['name'] = $data['name'];
		$data['message'] = $data['message'];
        $subject = "Contact Us - New Inquiry";
        $data['view'] = $this->load->view('email/contact_us',$data,TRUE);
        $message = $this->load->view('email/templates/email_template',$data,true);
		$this->send_email($email,$subject,$message);

		generate_json($response);		
	}
	// public function test_email(){
	// 	$data['email'] = "ss";
	// 	$data['name'] = "ss";
	// 	$data['message'] = "ss";
    //     $data['view'] = $this->load->view('email/contact_us',$data,TRUE);
    //     $this->load->view('email/templates/email_template',$data,'',true);
	// }
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
		$this->email->to('3mangschicha@gmail.com');
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
        
	}
	
}
