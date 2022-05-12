<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_customers');
        $this->load->model('user/model_accounts');
        $this->load->model('user/model_address');
        $this->load->model('orders/model_orders');
    }

    public function index()
	{		
		$data['active_page'] = 'about';
		
        $data['page_content'] = $this->load->view('user/home/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

    public function profile(){
        $customer_id = en_dec('dec',$this->session->customer_id);
        $email = en_dec('dec',$this->session->email);

        $customer_data = $this->model_customers->get_customer_by_email($email); 
        $cities = $this->model_orders->get_cities()->result_array(); 

        $view_data['customer_data'] = $customer_data;
        $view_data['cities'] = $cities;
        $view_data['shipping_addresses'] = $this->model_address->get_shipping_address($customer_id);

        $doc_count = $this->model_accounts->check_pending_verification($customer_id);
        $view_data['has_docs'] = $doc_count > 0 ? true : false;

        $data['active_page'] = 'profile';
		
        $data['page_content'] = $this->load->view('user/account/profile',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
    }

    public function update_profile(){
        $profile_info = $this->input->post();   
        
        $customer_id = en_dec('dec',$_SESSION['customer_id']);

        $f_name = '';
        if(!empty($_FILES['profile_img'])){
            $profile_info['profile_img'] = $_FILES['profile_img']['name'];
            $f_name = explode('.',$_FILES['profile_img']['name'])[0];
        }

        //folder checking
        if (!is_dir('uploads/profile_img/')) {
            mkdir('uploads/profile_img/', 0777, true);
        }

        //check file before insert
        $file_error = '';
        if(isset($_FILES['profile_img'])){
            if(!empty($_FILES['profile_img'])){
                $errors= array();
                $file_name = $_FILES['profile_img']['name'];
                $file_size = $_FILES['profile_img']['size'];
                $file_tmp = $_FILES['profile_img']['tmp_name'];
                $file_type= $_FILES['profile_img']['type'];
                $tmp = explode('.', $file_name);
                $file_ext = end($tmp);               
                $assetpath = base_url('uploads/profile_img');
                $filepath = 'uploads/profile_img/';
                $extensions= array("jpeg","jpg","png");
                
                if(in_array($file_ext,$extensions)=== false){
                    $errors[]="extension not allowed, please choose a JPEG or PNG file.";
                }
                
                if($file_size > 12097152){
                    $errors[]='File size must be 2 MB';
                }
                
                if(empty($errors)==true){                    
                    if(!file_exists($filepath.$_FILES['profile_img']['name'])){
                        move_uploaded_file($file_tmp,$filepath.$file_name);
                        $profile_info['profile_img'] = $_FILES['profile_img']['name'];
                    }
                    else{
                        move_uploaded_file($file_tmp,$filepath.$f_name.'_1.'.$file_ext);
                        $profile_info['profile_img'] = $f_name.'_1.'.$file_ext;
                    }
                }            
            }
            else{
                $profile_info['profile_img'] = '';
            }
        }

        $this->form_validation->validation_data = $profile_info;

        //declaration of form validations      
        $this->form_validation->set_rules('full_name','Full Name','required');
        $this->form_validation->set_rules('email','Email Address','required|valid_email');
        $this->form_validation->set_rules('mobile','Mobile Number','required|regex_match[/^[0-9]{11}$/]');

        if($this->form_validation->run() == FALSE) {

            $field_errors = $this->form_validation->error_array();            

            $response = array(
              'success'      => false,
              'message'      => 'Please check for field errors',
              'field_errors' => $field_errors,
            );
            generate_json($response);
            die();            
        }
        
        if($profile_info['profile_img'] == '' || $profile_info['profile_img'] == 'undefined'){
            unset($profile_info['profile_img']);
        }
        else{
            $_SESSION['profile_img'] = $profile_info['profile_img'];
        }       
        $this->model_accounts->update_profile($customer_id,$profile_info);

        $response['success'] = true;
        $response['message'] = 'Profile has been updated';        

        generate_json($response);
    }

    public function verification(){
        $customer_id = en_dec('dec',$this->session->customer_id);
        $email = en_dec('dec',$this->session->email);

        
        $customer_data = $this->model_customers->get_customer_by_email($email);

        $view_data['customer_data'] = $customer_data;
        $doc_count = $this->model_accounts->check_pending_verification($customer_id);
        $view_data['has_docs'] = $doc_count > 0 ? true : false;

        $data['active_page'] = 'account';
		
        $data['page_content'] = $this->load->view('user/account/verification',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
    }

    public function upload_document(){
        $doc_info = $this->input->post();   
        
        $customer_id = en_dec('dec',$_SESSION['customer_id']);

        $f_name = '';
        if(!empty($_FILES['image'])){
            $doc_info['id_pic'] = $_FILES['image']['name'];
            $f_name = explode('.',$_FILES['image']['name'])[0];
        }

        //folder checking
        if (!is_dir('uploads/docs/')) {
            mkdir('uploads/docs/', 0777, true);
        }

        //check file before insert
        $file_error = '';
        if(isset($_FILES['image'])){
            if(!empty($_FILES['image'])){
                $errors= array();
                $file_name = $_FILES['image']['name'];
                $file_size = $_FILES['image']['size'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_type= $_FILES['image']['type'];
                $tmp = explode('.', $file_name);
                $file_ext = end($tmp);               
                $assetpath = base_url('uploads/docs');
                $filepath = 'uploads/docs/';
                $extensions= array("jpeg","jpg","png");
                
                if(in_array($file_ext,$extensions)=== false){
                    $errors[]="extension not allowed, please choose a JPEG or PNG file.";
                }
                
                if($file_size > 12097152){
                    $errors[]='File size must be 2 MB';
                }
                
                if(empty($errors)==true){                    
                    if(!file_exists($filepath.$_FILES['image']['name'])){
                        move_uploaded_file($file_tmp,$filepath.$file_name);
                        $doc_info['id_pic'] = $_FILES['image']['name'];
                    }
                    else{
                        move_uploaded_file($file_tmp,$filepath.$f_name.'_1.'.$file_ext);
                        $doc_info['id_pic'] = $f_name.'_1.'.$file_ext;
                    }
                }            
            }
            else{
                $doc_info['id_pic'] = '';
            }
        }

        $this->form_validation->validation_data = $doc_info;

        //declaration of form validations      
        $this->form_validation->set_rules('doc_type','Document Type','required');
        $this->form_validation->set_rules('id_pic','Image','required');

        if($this->form_validation->run() == FALSE) {

            $field_errors = $this->form_validation->error_array();            

            $response = array(
              'success'      => false,
              'message'      => 'Please check for field errors',
              'field_errors' => $field_errors,
            );
            generate_json($response);
            die();            
        }

        $doc_info['customer_id'] = $customer_id;
        $doc_info['image'] = $doc_info['id_pic'];
        unset($doc_info['id_pic']);

        $doc_id = $this->model_accounts->insert_document($doc_info);

        $response['success'] = true;
        $response['message'] = 'Document has been submitted';
        $response['doc_id'] = en_dec('en',$doc_id);

        $data['email'] = en_dec('dec',$_SESSION['email']);
        $email = get_host();
        $subject = "New Profile Application - Activation Request";
        $message = $this->load->view('email/customer_verification_application',$data,true);
		$this->send_email($email,$subject,$message);
        echo json_encode($response);
        die();
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
    public function set_default_address(){
        $address_id = $this->model_address->set_default_address($this->input->post());

        $response['success'] = true;
        $response['message'] = 'Default Address has been saved';
        $response['address_id'] = en_dec('en',$address_id);

        generate_json($response);
    }
    public function remove_address(){
        $address_id = $this->model_address->remove_address($this->input->post());

        $response['success'] = true;
        $response['message'] = 'Default Address has been removed';
        $response['address_id'] = en_dec('en',$address_id);

        generate_json($response);
    }
    public function save_address(){
        $address_info = $this->input->post();        

        $customer_id = en_dec('dec',$_SESSION['customer_id']);

        $this->form_validation->validation_data = $address_info;

        //declaration of form validations
        $this->form_validation->set_rules('address_category_id','Address Type','required');
        $this->form_validation->set_rules('contact_person','Full Name','required');
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
        $address_info['customer_id'] = $customer_id;
        $address_id = $this->model_address->insert_address($address_info);

        $response['success'] = true;
        $response['message'] = 'Address has been saved';
        $response['address_id'] = en_dec('en',$address_id);

        generate_json($response);
    }

    public function update_address(){
        $address_info = $this->input->post();        

        $address_id = en_dec('dec',$address_info['address_id']);
        unset($address_info['address_id']);

        $this->form_validation->validation_data = $address_info;

        //declaration of form validations
        $this->form_validation->set_rules('address_category_id','Address Type','required');
        $this->form_validation->set_rules('contact_person','Full Name','required');
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
        
        $this->model_address->update_shipping_address_by_id($address_id,$address_info);

        $response['success'] = true;
        $response['message'] = 'Address has been updated';
        $response['address_id'] = $address_id;

        generate_json($response);
    }

    public function get_address(){
        $data = $this->input->post();

        $address_id = en_dec('dec',$data['address_id']);

        $address = $this->model_address->get_shippind_address_by_id($address_id);

        $response['success'] = true;   
        $response['address_data'] = $address;
        
        generate_json($response);
    }
    
}