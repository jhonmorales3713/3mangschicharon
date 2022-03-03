<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_users');
    }

    public function index(){
        $data['active_page'] = 'registration';		
        $data['page_content'] = $this->load->view('user/signup/index','',TRUE);     
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
        $user_data['password'] = en_dec('en',$user_data['password']);
        
        $user_id = $this->model_users->insert_user($user_data);

        $response['success'] = true;
        $response['user_id'] = $user_id;
        $response['message'] = 'Registration Successful';

        generate_json($response); 
       
    }

    public function login(){
        $data = $this->input->post();        

        $this->form_validation->validation_data = $data;

        //declaration of form validations
        $this->form_validation->set_rules('login_email','Email Address','required');
        $this->form_validation->set_rules('login_password','Password','required');

        if($this->form_validation->run() == FALSE) {
            $response = array(
              'success'      => false,
              'message'      => 'Please check for field errors',
              'field_errors' => $this->form_validation->error_array(),              
            );

            generate_json($response);
            die();            
        }
        
        $user_data = $this->model_users->get_user_by_email($data['login_email']); 

        $en_password = en_dec('en',$data['login_password']);
        if($user_data){
            if($en_password != $user_data['password']){
                $response['success'] = false;
                $response['field_errors'] = array('login_password' => 'Invalid Password');
                generate_json($response); die();
            }
        }
        else{
            $response['success'] = false;
            $response['field_errors'] = array('login_email' => 'Account does not exist');
            generate_json($response); die();
        }

        $response['success'] = true;        
        $response['message'] = 'Login Successful';

        generate_json($response);       
    }

    private function set_session(){

    }

    public function signout(){

    }

}