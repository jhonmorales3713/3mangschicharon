<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_users');
    }

    public function index(){
        $data['active_page'] = 'registration';		
        $data['page_content'] = $this->load->view('user/signup/signup_form','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);        
    }

    public function signup(){
        $user_data = $this->input->post();        

        $this->form_validation->validation_data = $user_data;

        //declaration of form validations      
        $this->form_validation->set_rules('full_name','Full Name','required');
        $this->form_validation->set_rules('mobile','Mobile Number','required|regex_match[/^[0-9]{11}$/]');
        $this->form_validation->set_rules('email','Email Address','required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password','Password','required');
        $this->form_validation->set_rules('password2','Confirm Password','required');
        $this->form_validation->set_message('is_unique','{field} is already in use');        

        if($this->form_validation->run() == FALSE) {
            $response = array(
              'success'      => false,
              'message'      => 'Please check for field errors',
              'field_errors' => $this->form_validation->error_array(),              
            );

            if($user_data['password'] != $user_data['password2'] && $user_data['password2'] != ''){
                $response['field_errors']['password'] = 'Passwords do not match';
                $response['field_errors']['password2'] = 'Passwords do not match';
            }

            generate_json($response);
            die();            
        }     

        unset($user_data['password2']);
        
        $user_id = $this->model_users->insert_user($user_data);

        $response['success'] = true;
        $response['user_id'] = $user_id;
        $response['message'] = 'Registration Successful';

        generate_json($response); 
       
    }

}