<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
    public function __construct() {
        parent::__construct();  
		$this->load->model('user/model_home');
		$this->load->model('model_landing');
    }

	public function index()
	{		
		$data['active_page'] = 'home';
		$data['subnav'] = false;
		$data['has_search'] = true;
		$data['banners'] = $this->model_home->get_banners();
		
        $data['page_content'] = $this->load->view('user/home/index',$data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

	public function contact_us()
	{		
		$data['active_page'] = 'contact_us';
		
        $data['page_content'] = $this->load->view('user/home/contact_us','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
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
