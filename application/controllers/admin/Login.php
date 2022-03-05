<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
        $this->load->model('model');
    }

    public function index(){
        $this->load->view('admin/login/index');
    }

    public function signin(){
        $username_exist = $this->validate_username($this->input->post('username'));
        if($username_exist->num_rows()>0){
            $user_info       = $username_exist->row();
            $verify_username = $user_info->active; // check if unverified email
            $hash_password   = $user_info->password;
            $code_isset      = $user_info->code_isset;
            $user_status     = $user_info->user_status;
            if($user_status  == 0){
                $data = array(
                    'success' => 0,
                    'message' => 'Account is offline at the moment, please contact administrator for more info.',
                );
                
            }
            if(password_verify($this->input->post('password'),$hash_password)){
                if($user_status == 1){
                    $userData = array( // store in array
                        'id' => $user_info->sys_users_id,
                        'username' => $user_info->username,
                        'avatar' => $user_info->avatar,
                        'functions' => $user_info->functions,
                        'role' => 'admin',
                        'access_nav' => $user_info->access_nav,
                        'access_content_nav' => $user_info->access_content_nav,
                        'sys_shop' => 1,
                        'fname' => $user_info->fname,
                        'mname' => $user_info->mname,
                        'lname' => $user_info->lname,
                        'email' => $user_info->email,
                        'mobile_number' => $user_info->mobile_number,
                        'shop_logo' => $user_info->logo == null ? 0 : $user_info->logo,
                        'shop_url' => $user_info->shopurl == null ? 0 : $user_info->shopurl,
                        'sys_users_id' => $user_info->sys_users_id,
                        'sys_shop_id' => 1,                        
                        'isLoggedIn' => true,
    
                        // 'get_position_access' => $this->model->get_position_details_access($user_info->position_id)->row(),
                    );
    
                    // Record time of log in of merchant
                    // if ($userData['shopname'] != '') {
                    //     $this->model->log_seller_time_activity($userData['sys_users_id'], $userData['sys_shop'], 'in');
                    // }
    
                    //$first_login = ($user_info->first_login == 1) ? 1:0;
                    // $checkIfFirstLogin = $this->model->checkIfFirstLogin($user_info->sys_users_id);
                    
                    // if($checkIfFirstLogin > 0){
                    //     if($code_isset == 0){
                    //         $checkIPNotExist = $this->model->checkIPNotExist($user_info->sys_users_id);
                    //         $code_isset   = ($checkIPNotExist == 0) ? 1 : 0;
                    //         $login_code   = getRandomString(6, 6);
                    //         ($code_isset == 1) ? $this->model->setLoginCode($user_info->sys_users_id, $login_code):null;
                    //     }
                    // }
    
                    $token_session = uniqid();
                    $token = en_dec('en', $token_session);
                    $token_arr = array( // store token in array
                        'token_session' => $token_session,
                    );
    
                    // set session
                    $this->session->set_userdata($userData);
                    $this->session->set_userdata($token_arr);
    
                    $access_nav = $this->session->userdata('access_nav');
                    if(in_array(1, explode(', ', $access_nav))) {
                        $is_dashboard = 1;
                    }else{
                        $is_dashboard = 0;
                    }
    
                    // $this->audittrail->logActivity('Login', $this->input->post('username').' successfully logged in.', 'login', $this->input->post('username'));
                    $data = array(
                        'success' => 1,
                        'response' => 'Login successfully',
                        'token_session' => $token,
                        'is_dashboard' => $is_dashboard,
                        'code_isset' => $code_isset,
                        'username' => $this->input->post('username'),
                        'md5_sys_users_id' => md5($user_info->sys_users_id)
                    );
    
                    //($first_login == 1) ? $this->session->sess_destroy():'';
                    ($code_isset == 1) ? $this->session->sess_destroy():'';
                    ($code_isset == 1) ? null:$this->resetLoginAttempts($user_info->sys_users_id);
                    echo json_encode($data);
                }else{
                    echo json_encode(array("response"=>"Account is disabled. Please contact administrator for more info.","success"=>0));
                }
            }else{
                echo json_encode(array("response"=>"Invalid Password, please try again.","success"=>0));
            }
        }else{
            echo json_encode(array("response"=>"Account does not exist in our database.","success"=>0));
        }
    }
    public function validate_username($username){
        return $this->model->validate_username($username);
    }

    public function resetLoginAttempts($user_id){
        //$ip_address   = $this->getClientIP();
        //$date_created = date('Y-m-d H:i:s');

        $attempt = $this->model->resetLoginAttempts($user_id);
       
    }
    public function getClientIP(){      
        $return = '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
               $return = $_SERVER["HTTP_X_FORWARDED_FOR"];  
        }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
               $return = $_SERVER["REMOTE_ADDR"]; 
        }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
               $return = $_SERVER["HTTP_CLIENT_IP"]; 
        } 

        $return = ($return != '') ? $return : '';
        $split  = explode(",",$return);
        $return = (!empty($split[1])) ? $split[0] : $return;

        return $return;
   }

   public function signout(){
       session_destroy();
       redirect('admin');
   }


}