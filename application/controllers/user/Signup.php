<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_customers');
    }

    public function index(){
        $data['active_page'] = 'registration';		
        $data['page_content'] = $this->load->view('user/signup/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);        
    }
    
    public function signup(){
        $customer_data = $this->input->post();        

        $this->form_validation->validation_data = $customer_data;

        //declaration of form validations      
        $this->form_validation->set_rules('full_name','Full Name','required');
        $this->form_validation->set_rules('mobile','Mobile Number','required|regex_match[/^[0-9]{11}$/]');
        $this->form_validation->set_rules('email','Email Address','required|valid_email|is_unique[sys_customers.email]');
        $this->form_validation->set_rules('password','Password','required');
        $this->form_validation->set_rules('password2','Confirm Password','required');
        $this->form_validation->set_message('is_unique','{field} is already in use');        

        if($this->form_validation->run() == FALSE) {
            $response = array(
              'success'      => false,
              'message'      => 'Please check for field errors',
              'field_errors' => $this->form_validation->error_array(),              
            );

            if($customer_data['password'] != $customer_data['password2'] && $customer_data['password2'] != ''){
                $response['field_errors']['password'] = 'Passwords do not match';
                $response['field_errors']['password2'] = 'Passwords do not match';
            }

            generate_json($response);
            die();            
        }     

        unset($customer_data['password2']);
        $customer_data['password'] = en_dec('en',$customer_data['password']);
        
        $customer_id = $this->model_customers->insert_customer($customer_data);

        $response['success'] = true;
        $response['customer_id'] = $customer_id;
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
        
        $customer_data = $this->model_customers->get_customer_by_email($data['login_email']); 

        $en_password = en_dec('en',$data['login_password']);
        if($customer_data){
            if($en_password != $customer_data['password']){
                $response['success'] = false;
                $response['field_errors'] = array('login_password' => 'Invalid Password');
                generate_json($response); die();
            }else if($customer_data['status_id'] == 2){
                $response['success'] = false;
                $response['field_errors'] = array('login_email' => 'Account has been disabled by admin. For more info, please contact the administrator.');
                generate_json($response); die();

            }
        }
        else{
            $response['success'] = false;
            $response['field_errors'] = array('login_email' => 'Account does not exist');
            generate_json($response); die();
        }
        // print_r($customer_data);
        $this->set_session($customer_data);
        $response['success'] = true;        
        $response['message'] = 'Login Successful';

        generate_json($response);       
    }

    private function set_session($customer_data){
        $_SESSION['has_logged_in'] = true;
        $_SESSION['full_name'] = $customer_data['full_name'];
        $_SESSION['decline_details'] = isset($customer_data['decline_reason'])?$customer_data['decline_reason']:'';
        $_SESSION['customer_id'] = en_dec('en',$customer_data['id']);
        $_SESSION['email'] = en_dec('en',$customer_data['email']);
        $_SESSION['is_verified'] = $customer_data['user_type_id'];
        $_SESSION['addresses'] = $this->model_customers->get_customer_addresses($customer_data['id']);
        
        $cart_session = $this->model_customers->get_cart_session($customer_data['id']);     
        if($cart_session){            
            $_SESSION['cart'] = json_decode($cart_session['cart_data'],true);
            $cart_items = 0;
            foreach($_SESSION['cart'] as $key => $value){
                if(isset($_SESSION['cart'][$key]['quantity'])){
                    $cart_items += intval($_SESSION['cart'][$key]['quantity']);
                }
            }
            $_SESSION['cart_items'] = $cart_items;            
        }
    }

    private function save_cart_session(){
        $cart_session = array(
            'customer_id' => en_dec('dec',$_SESSION['customer_id']),
            'cart_data' => json_encode($_SESSION['cart'])
        );
        $this->model_customers->save_cart_session($cart_session);        
    }

    public function signout(){        
        $customer_id = en_dec('dec',$_SESSION['customer_id']);
        $cart_session = $this->model_customers->get_cart_session($customer_id);
        if(isset($_SESSION['cart'])){            
            if(sizeof($_SESSION['cart']) > 0){                
                if($cart_session){                    
                    $this->model_customers->update_cart_session($customer_id,json_encode($_SESSION['cart']));
                }
                else{
                    $this->save_cart_session();
                }                
            }                        
        }
        else{
            if($cart_session){
                $this->model_customers->remove_cart_session($customer_id);
            }
        }
        session_destroy();
        redirect('home');
    }

}