<?php

class Authentication extends CI_Controller {

    protected function tdh_api_url() {

        if (ENVIRONMENT == "production") {
            return "https://thedarkhorse.ph/tdh/api/JCReferralAPI/getDiscountRateOfIDNO";
        }else if (ENVIRONMENT == "testing") {
            return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/getDiscountRateOfIDNO";
        }else{
            return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/getDiscountRateOfIDNO";
        }
    }

    protected function tdh_api_key() {

        if (ENVIRONMENT == "production") {
            return "JCCOMRATE06032020NOW!";
        }else if (ENVIRONMENT == "testing") {
            return "JCCOMRATE06032020NOW!";
        }else{
            return "JCCOMRATE06032020NOW!";
        }
    }

    public function check_allow_login(){
        if (allow_login() == 0) {
            $this->session->unset_userdata("user_id");
            header("Location: ".base_url());
        }
    }

    public function index()
    {
        $this->check_allow_login();
        $is_logged_in = $this->session->userdata("user_id");
        if(!empty($is_logged_in)){
            header("location: ".base_url());
        }else{
            $data['page'] = "login";
            $data['categories'] = null;
            $this->load->view("includes/header", $data);
            $this->load->view("auth/login");
        }
    }

    public function register()
    {
        $this->check_allow_login();
        // $data['page'] = "login";
        $this->session->unset_userdata('email_code');
        $this->session->unset_userdata('email_receiver');
        $data['categories'] = null;
        $this->load->view("includes/header", $data);
        $this->load->view("auth/register");
    }

    public function login_jc()
    {
        $this->check_allow_login();
        $this->session->unset_userdata('cart');
        $this->session->unset_userdata('distributorRate');
        $this->session->unset_userdata('referral_disname');
        $this->session->unset_userdata('total_amount');
        $this->session->unset_userdata('cart_count');
        $this->load->model('auth/model_authentication');
        $validation = array(
            array('username','User Name','required'),
            array('password','Password','required'),
        );

        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2]);
        }

        if ($this->form_validation->run() == FALSE){
            $response['environment']  =  ENVIRONMENT;
            $response['success']      =  false;
            $response['message']      =  validation_errors();
            $response['csrf_name']    =  $this->security->get_csrf_token_name();
            $response['csrf_hash']    =  $this->security->get_csrf_hash();
            echo json_encode($response);
            die();
        }else{
            try {
          //sanitize user input
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $processDate = date('Y-m-d H:i:s');

                $referralData = array (
                    "signature" => en_dec_jc_api("en", md5($processDate.$this->tdh_api_key())),
                    "username" => en_dec_jc_api("en", $username),
                    "password" => en_dec_jc_api("en", $password),
                    "date" => $processDate
                );

                $postvars = http_build_query($referralData);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->tdh_api_url());
                curl_setopt($ch, CURLOPT_POST, count($referralData));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec($ch);

                if ($server_output === false) {
                    $info = curl_getinfo($ch);
                    curl_close($ch);
                    die('error occured during curl exec. Additional info: ' . var_export($info));
                }
                curl_close($ch);
                $response =  json_decode($server_output);

                if($response != null ) {

                    if($response->success) {
                        $disinfo = json_decode(en_dec_jc_api('dec',$response->disinfo));
                        $this->session->set_userdata("distributorRate", json_decode(en_dec_jc_api("dec", $response->data_res)));
                        $this->session->set_userdata("distributorInfo", $disinfo);

                        $isExist = $this->model_authentication->get_user($disinfo->idno);
                        if($isExist->num_rows() == 0){
                          $sys_cust_data = array(
                            "username" => $disinfo->idno,
                            "active" => 1,
                            "seller_login" => 1
                          );

                          $inserted = $this->model_authentication->set_sys_customer_auth($sys_cust_data);
                          if($inserted['status'] === false){
                            $data = array("success" => false, "message" => "Online Seller login failed. Please try again.");
                            $data['csrf_name']    =  $this->security->get_csrf_token_name();
                            $data['csrf_hash']    =  $this->security->get_csrf_hash();
                            echo json_encode($data);
                            exit();
                          }

                          $app_customer_data = array(
                            "user_id" => $inserted['user_id'],
                            "first_name" => $disinfo->fname,
                            "last_name" => $disinfo->lname,
                            "email" => $disinfo->email,
                            "conno" => $disinfo->conno,
                            "address1" => $disinfo->address
                          );

                          $inserted2 = $this->model_authentication->set_app_customers($app_customer_data);
                          if($inserted2 === false){
                            $data = array("success" => false, "message" => "Facebook Login Failed. Please try again.");
                            $data['csrf_name']    =  $this->security->get_csrf_token_name();
                            $data['csrf_hash']    =  $this->security->get_csrf_hash();
                            echo json_encode($data);
                            exit();
                          }
                          $user_id2 = $inserted['user_id'];
                          $this->session->fname      =  ucwords(strtolower($this->session->userdata("distributorInfo")->fname));
                          $this->session->lname      =  ucwords(strtolower($this->session->userdata("distributorInfo")->lname));
                          $this->session->name       =  $this->session->fname." ".$this->session->lname;
                          $this->session->address    =  ucwords(strtolower($this->session->userdata("distributorInfo")->address));
                          $this->session->city       =  ucwords(strtolower($this->session->userdata("distributorInfo")->city));
                          $this->session->email      =  $this->session->userdata("distributorInfo")->email;
                          $this->session->conno      =  $this->session->userdata("distributorInfo")->conno;
                        }else{
                          $user = $isExist->row();
                          $user_id2 = $user->id;
                          $this->session->fname      =  ucwords(strtolower($user->first_name));
                          $this->session->lname      =  ucwords(strtolower($user->last_name));
                          $this->session->name       =  $user->first_name." ".$user->last_name;
                          $this->session->address    =  ucwords(strtolower($user->address1));
                          $this->session->city       =  ucwords(strtolower($user->address2));
                          $this->session->email      =  $user->email;
                          $this->session->conno      =  $user->conno;
                          $this->session->receiver_name = $user->receiver_name;
                          $this->session->receiver_address = $user->receiver_address;
                          $this->session->receiver_landmark = $user->receiver_landmark;
                          $this->session->receiver_postal_code = $user->receiver_postal_code;
                          $this->session->receiver_region_id = $user->receiver_region_id;
                          $this->session->receiver_municipality_id = $user->receiver_municipality_id;
                          $this->session->receiver_conno = $user->receiver_contact;
                        }


                        // die();
                        // destroy session first
                        // $this->session->sess_destroy();
                        //Remove referral id and cart details from session
                        $this->session->set_userdata("jc_seller_login",1);
                        $this->session->set_userdata("referral", "");
                        $this->session->set_userdata("total_amount", 0);
                        $this->session->set_userdata("cart_count", 0);
                        $this->session->set_userdata("cart", []);

                        $this->session->set_userdata('user_id2',$user_id2);
                        $this->session->user_id    =  $this->session->userdata("distributorInfo")->idno;
                        $this->session->user_type  =  "JC";


                        //assume successful authentication
                        $response = array(
                            'success'     =>  true,
                            'message'     =>  'Authentication Successful',
                            'environment' =>  ENVIRONMENT,
                            'csrf_name'   =>  '',
                            'csrf_hash'   =>  '',
                            'url'         =>  base_url()
                        );
                    }

                }
                else {
                    $response = array();
                    $response['success'] = false;
                    $response['message'] = 'Oops! the account you are trying to access is not enrolled in the system. If you believe this is an error, please contact your administrator.';
                }
                echo json_encode($response);
            } catch (Exception $e) {
                $response = array(
                    'success'     => "error",
                    'message'     => $e->message(),
                    'environment' => ENVIRONMENT
                );
                echo json_encode($response);
            }
        }
    }

    public function login_social_media()
    {
      $this->check_allow_login();
      $this->session->unset_userdata('cart');
      $this->session->unset_userdata('distributorRate');
      $this->session->unset_userdata('referral_disname');
      $this->session->unset_userdata('total_amount');
      $this->session->unset_userdata('cart_count');
      $this->load->model('auth/model_authentication');
      $validation = array(
          array('email','User Name','required|valid_email'),
          array('fname','First Name','required'),
          array('lname','Last Name','required')
      );

      foreach ($validation as $value) {
          $this->form_validation->set_rules($value[0],$value[1],$value[2]);
      }

      if($this->form_validation->run() === FALSE){
        $response['environment']  =  ENVIRONMENT;
        $response['success']      =  false;
        $response['message']      =  validation_errors();
        $response['csrf_name']    =  $this->security->get_csrf_token_name();
        $response['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($response);
        exit();
      }

      try{
        $email = sanitize($this->input->post('email'));
        $fname = sanitize($this->input->post('fname'));
        $lname = sanitize($this->input->post('lname'));
        $birthday = sanitize($this->input->post('birthday'));
        $gender = sanitize($this->input->post('gender'));
        $login_type = sanitize($this->input->post('login_type'));
        $bday = new DateTime($birthday);
        $birthday = $bday->format('Y-m-d');

        $isExist = $this->model_authentication->get_user($email);
        // email does not exist, direct to sign up.
        if($isExist->num_rows() == 0){
          $sys_cust_authdata = array("username" => $email, "active" => 1);
          ($login_type == "fb")
          ? $sys_cust_authdata['fb_login'] = 1 // fb login
          : $sys_cust_authdata['gmail_login'] = 1; // gmail login

          $inserted = $this->model_authentication->set_sys_customer_auth($sys_cust_authdata);
          if($inserted['status'] === false){
            $data = array("success" => false, "message" => "Facebook login failed. Please try again.");
            $data['csrf_name']    =  $this->security->get_csrf_token_name();
            $data['csrf_hash']    =  $this->security->get_csrf_hash();
            echo json_encode($data);
            exit();
          }

          $app_customer_data = array(
            "user_id" => $inserted['user_id'],
            "first_name" => $fname,
            "last_name" => $lname,
            "email" => $email,
            "birthdate" => $birthday,
            "gender" => strtoupper(substr($gender,0,1))
          );
          $inserted2 = $this->model_authentication->set_app_customers($app_customer_data);
          if($inserted2 === false){
            $data = array("success" => false, "message" => "Facebook Login Failed. Please try again.");
            $data['csrf_name']    =  $this->security->get_csrf_token_name();
            $data['csrf_hash']    =  $this->security->get_csrf_hash();
            echo json_encode($data);
            exit();
          }

          $this->session->user_id    =  $inserted['user_id'];
          $this->session->user_id2    =  $inserted['user_id'];
          $this->session->user_type  =  get_company_name();
          $this->session->username   =  $email;
          $this->session->last_seen  =  '';
          $this->session->fname      =  ucwords(strtolower($fname));
          $this->session->lname      =  ucwords(strtolower($lname));
          $this->session->address    =  ucwords(strtolower(''));
          $this->session->city       =  ucwords(strtolower(''));
          $this->session->email      =  $email;
          $this->session->conno      =  '';
          $this->session->set_userdata('social_media_login', 1);

          $data = array(
              'success'     =>  true,
              'message'     =>  'Authentication Successful',
              'environment' =>  ENVIRONMENT,
              'csrf_name'   =>  '',
              'csrf_hash'   =>  '',
              'url'         =>  base_url()
          );

          // for customer auth audit trail
          $audit_trail_data = array("user_id" => $inserted['user_id']);
          ($login_type == 'fb')
          ? $audit_trail_data['action'] = "fb_login"
          : $audit_trail_data['action'] = "gmail_login";
          $this->model_authentication->set_customer_auth_audittrail($audit_trail_data);
          $this->model_authentication->update_last_seen($email);
          session_write_close();
          echo json_encode($data);
          exit();
        }

        // email already exist.
        if($isExist->num_rows() > 0){
          $row = $isExist->row();
          $data = array(
              'success'     =>  true,
              'message'     =>  'Authentication Successful',
              'environment' =>  ENVIRONMENT,
              'csrf_name'   =>  '',
              'csrf_hash'   =>  '',
              'url'         =>  base_url()
          );

          $this->session->user_id    =  $row->id;
          $this->session->user_id2    =  $row->id;
          $this->session->user_type  =  get_company_name();
          $this->session->username   =  $row->username;
          $this->session->last_seen  =  $row->lastseen;
          $this->session->fname      =  ucwords(strtolower($row->first_name));
          $this->session->lname      =  ucwords(strtolower($row->last_name));
          $this->session->address    =  ucwords(strtolower($row->address1));
          $this->session->city       =  ucwords(strtolower($row->address2));
          $this->session->email      =  $row->email;
          $this->session->conno      =  $row->conno;
          $this->session->set_userdata('social_media_login', 1);
          $this->session->receiver_name = $row->receiver_name;
          $this->session->receiver_address = $row->receiver_address;
          $this->session->receiver_landmark = $row->receiver_landmark;
          $this->session->receiver_postal_code = $row->receiver_postal_code;
          $this->session->receiver_region_id = $row->receiver_region_id;
          $this->session->receiver_municipality_id = $row->receiver_municipality_id;
          $this->session->receiver_conno = $row->receiver_contact;

          // for customer auth audit trail
          $audit_trail_data = array("user_id" => $row->id);
          ($login_type == 'fb')
          ? $audit_trail_data['action'] = "fb_login"
          : $audit_trail_data['action'] = "gmail_login";
          $this->model_authentication->set_customer_auth_audittrail($audit_trail_data);
          $this->model_authentication->update_last_seen($row->username);

          session_write_close();
          echo json_encode($data);
          exit();

        }

      } catch(Exception $e){
        $response = array(
            'success'     => "error",
            'message'     => $e->message(),
            'environment' => ENVIRONMENT
        );
        echo json_encode($response);
        exit();
      }
    }

    public function login()
    {
        $this->check_allow_login();
        $this->session->unset_userdata('cart');
        $this->session->unset_userdata('distributorRate');
        $this->session->unset_userdata('referral_disname');
        $this->session->unset_userdata('total_amount');
        $this->session->unset_userdata('cart_count');
        $this->load->model('auth/model_authentication');
        $validation = array(
            array('username','User Name','required'),
            array('password','Password','required'),
        );

        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2]);
        }

        if ($this->form_validation->run() == FALSE){
            $response['environment']  =  ENVIRONMENT;
            $response['success']      =  false;
            $response['message']      =  validation_errors();
            $response['csrf_name']    =  $this->security->get_csrf_token_name();
            $response['csrf_hash']    =  $this->security->get_csrf_hash();
            echo json_encode($response);
            die();
        }else{
            try {
                //sanitize user input
                $username = sanitize($this->input->post('username'));
                $password = sanitize($this->input->post('password'));

                $result = $this->model_authentication->get_user($username);

                //assume successful authentication
                $response = array(
                    'success'     =>  true,
                    'message'     =>  'Authentication Successful',
                    'environment' =>  ENVIRONMENT,
                    'csrf_name'   =>  '',
                    'csrf_hash'   =>  '',
                    'url'         =>  base_url()
                );

                if($result->num_rows()!==1){
                    $response['success'] = false;
                    $response['message'] = 'Oops! the username you entered is not enrolled in the system. If you
                    believe this is an error, please contact your administrator.';
                }else{
                    $row = $result->row();
                    // Temporary disabled login for 30 min.
                    $min_diff = time_diff($row->last_failed_attempt,todaytime());
                    if($row->failed_login_attempts >= 5 && $min_diff < 30){
                      $response['success'] = false;
                      $response['message'] = "You login failed too many times. Please try again after 30 minutes.";;
                      $response['csrf_name'] = $this->security->get_csrf_token_name();
                      $response['csrf_hash'] = $this->security->get_csrf_hash();
                      echo json_encode($response);
                      exit();
                    }

                    if(password_verify($password,$row->password)){
                        if($row->active==='0'){
                            $response['success'] = false;
                            $response['message'] = 'Sorry, your account is currently disabled. Please contact your administrator.';
                        }else{
                            if(null !== ($this->input->post('app_key'))){
                                $app_data = $this->model_trusted_apps->authenticate_app($this->input->post('app_key'));
                                if($app_data['value']){
                                    //create session for the application
                                }else{
                                    $response = $this->response->action_denied_message();
                                }
                            }else{

                                if($this->input->post('serviceurl') && $this->input->post('serviceaccount')){
                                    $response['url'] = $this->input->post('serviceurl');
                                }

                                $this->session->web_login  = 1;
                                $this->session->user_id    =  $row->id;
                                $this->session->user_id2    =  $row->id;
                                $this->session->user_type  =  get_company_name();
                                $this->session->username   =  $row->username;
                                $this->session->last_seen  =  $row->lastseen;
                                $this->session->fname      =  ucwords(strtolower($row->first_name));
                                $this->session->lname      =  ucwords(strtolower($row->last_name));
                                $this->session->name       =  $this->session->fname." ".$this->session->lname;
                                $this->session->address    =  ucwords(strtolower($row->address1));
                                $this->session->city       =  ucwords(strtolower($row->address2));
                                $this->session->email      =  $row->email;
                                $this->session->conno      =  $row->conno;
                                $this->session->set_userdata('social_media_login', 0);


                                $this->session->receiver_name = $row->receiver_name;
                                $this->session->receiver_address = $row->receiver_address;
                                $this->session->receiver_landmark = $row->receiver_landmark;
                                $this->session->receiver_postal_code = $row->receiver_postal_code;
                                $this->session->receiver_region_id = $row->receiver_region_id;
                                $this->session->receiver_municipality_id = $row->receiver_municipality_id;
                                $this->session->receiver_conno = $row->receiver_contact;

                                // for customer auth audit trail
                                $audit_trail_data = array("user_id" => $row->id, "action" => "login");
                                $this->model_authentication->set_customer_auth_audittrail($audit_trail_data);
                                $this->model_authentication->update_last_seen($username);
                                $this->model_authentication->update_failed_attempt($username,true);
                            }
                        }
                    }else{
                        $response['success']    = false;
                        $response['message']    = 'Invalid password. Please try again!';
                        $response['csrf_hash']  = $this->security->get_csrf_hash();
                        // for failed attempt count
                        ($row->failed_login_attempts >= 5)
                        ? $this->model_authentication->update_failed_attempt($username,true)
                        : $this->model_authentication->update_failed_attempt($username);
                    }
                }

                session_write_close();
                if(!$response['success']){
                    $response['csrf_name'] = $this->security->get_csrf_token_name();
                    $response['csrf_hash'] = $this->security->get_csrf_hash();
                }
                echo json_encode($response);
            } catch (Exception $e) {
                $response = array(
                    'success'     => "error",
                    'message'     => $e->message(),
                    'environment' => ENVIRONMENT
                );
                echo json_encode($response);
            }
        }
    }

    public function get_token()
    {
        $response['csrf_name'] = $this->security->get_csrf_token_name();
        $response['csrf_hash'] = $this->security->get_csrf_hash();
        $data = array(
            'environment' => ENVIRONMENT,
            'data'        => $response
        );
        echo json_encode($data);
    }

    public function generate_captcha()
    {

        $data = array(
            'environment'   =>  ENVIRONMENT,
            'data'          =>  $this->cp_captcha->generate_captcha(6,$this->input->ip_address())['image']
        );

        echo json_encode($data);
    }

    public function validate_captcha()
    {
        $this->form_validation->set_rules('captcha_text', 'Captcha Text', 'required');
        if ($this->form_validation->run() == FALSE){
            $data = array(
                'environment'   =>  ENVIRONMENT,
                'data'          =>  validation_errors()
            );
            echo json_encode($data);
        }else{
            $this->load->library('cp_captcha');
            $data = array(
                'environment'   =>  ENVIRONMENT,
                'data'          =>  $this->cp_captcha->validate_captcha($this->input->captcha_text,$this->input->ip_address())
            );
            echo json_encode($data);
        }
    }

    public function logout()
    {
        // for customer auth audit trail
        if(isset($this->session->user_id) && $this->session->user_id != ''){
          $this->load->model('auth/model_authentication');
          $audit_trail_data = array("user_id" => $this->session->user_id, "action" => "logout");
          $this->model_authentication->set_customer_auth_audittrail($audit_trail_data);
        }
        $this->session->sess_destroy();
        header("location: ".base_url());
    }

    public function validate_email()
    {
      $this->check_allow_login();
      $this->load->model('auth/model_authentication');
      $email = sanitize($this->input->post('email'));
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

      if($this->form_validation->run() === FALSE){
        $data = array("success" => false, "message" => "Invalid Email Address.");
        echo json_encode($data);
        exit();
      }

      $isExist = $this->model_authentication->validate_username($email);
      if($isExist->num_rows() > 0){
        $data = array("success" => false, "message" => "Email already taken.");
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "Email is valid");
      echo json_encode($data);

    }

    public function send_email_code()
    {
      $email = sanitize($this->input->post('email'));
      $fname = sanitize($this->input->post('fname'));
      $lname = sanitize($this->input->post('lname'));

      if(empty($email)){
        $data = array("success" => false, "message" => "Unable to send email code. Please try again");
        echo json_encode($data);
        exit();
      }

      $this->form_validation->set_rules('email', 'Email', 'valid_email');
      if($this->form_validation->run() === FALSE){
        $data = array("success" => false, "message" => "Unable to send email code. Please check your email");
        echo json_encode($data);
        exit();
      }

      $email_code = rand(100000, 999999);
      $this->session->set_userdata('email_code', $email_code);
      $this->session->set_userdata('email_receiver', $email);

      try{
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($email);
        $this->email->subject(get_company_name()." | Verify Email");
        $email_data['email_code'] = $email_code;
        $email_data['fname'] = $fname;
        $email_data['lname'] = $lname;
        $email_data['fullname'] = $fname.' '.$lname;
        $this->email->message($this->load->view("emails/email_code", $email_data, TRUE));
        $this->email->send();
      }catch(Exception $err){
        // echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
        $data = array("success" => false, "message" => "Unable to send email code. Please try again");
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "Email Code Sent !");
      echo json_encode($data);
    }

    public function resend_email_code()
    {
      $email_code = $this->session->email_code;
      $email = sanitize($this->input->post('email'));
      $fname = sanitize($this->input->post('fname'));
      $lname = sanitize($this->input->post('lname'));

      $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
      if($this->form_validation->run() === FALSE){
        $data = array("success" => false, "message" => validation_errors());
        echo json_encode($data);
        exit();
      }

      try{
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($email);
        $this->email->subject(get_company_name()." | Verify Email");
        $email_data['email_code'] = $email_code;
        $email_data['fname'] = $fname;
        $email_data['lname'] = $lname;
        $email_data['fullname'] = $fname.' '.$lname;
        $this->email->message($this->load->view("emails/email_code", $email_data, TRUE));
        $this->email->send();
      }catch(Exception $err){
        // echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
        $data = array("success" => false, "message" => "Unable to send email code. Please try again");
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "Email Code Sent !");
      echo json_encode($data);
    }

    public function register_user()
    {
      $this->check_allow_login();
      $this->load->model('auth/model_authentication');

      $validation = array(
        array('email','Email','required|valid_email'),
        array('password','Password','required|min_length[8]'),
        array('gender','Gender','required'),
        array('birthday','Birthdate','required'),
        array('fname','First Name','required'),
        array('lname','Last Name','required')
      );

      foreach ($validation as $value) {
          $this->form_validation->set_rules($value[0],$value[1],$value[2]);
      }

      if($this->form_validation->run() === FALSE){
        $data['environment']  =  ENVIRONMENT;
        $data['success']      =  false;
        $data['message']      =  validation_errors();
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }

      try{

        $email = sanitize($this->input->post('email'));
        $email_code = sanitize($this->input->post('email_code'));
        $password = sanitize($this->input->post('password'));
        $gender = sanitize($this->input->post('gender'));
        $birthday = sanitize($this->input->post('birthday'));
        $fname = sanitize($this->input->post('fname'));
        $lname = sanitize($this->input->post('lname'));

        if($email_code != $this->session->email_code || $email != $this->session->email_receiver){
          $data = array("success" => false, "message" => "Invalid Email Code");
          echo json_encode($data);
          exit();
        }

        $isExist = $this->model_authentication->validate_username($email);
        if($isExist->num_rows() > 0){
          $data = array("success" => false, "message" => "Email already taken.");
          echo json_encode($data);
          exit();
        }

        $hash_password = password_hash($password,PASSWORD_BCRYPT);
        $sys_data = array(
          "username" => $email,
          "password" => $hash_password,
          "active" => 1
        );

        $inserted = $this->model_authentication->set_sys_customer_auth($sys_data);
        if($inserted['status'] === false){
          $data = array("success" => false, "message" => "Registration Failed. Please try again");
          generate_json($data);
          exit();
        }

        $customer_data = array(
          "user_id" => $inserted['user_id'],
          "first_name" => $fname,
          "last_name" => $lname,
          "email" => $email,
          "birthdate" => $birthday,
          "gender" => ($gender == 1) ? "M" : "F"
        );

        $registered = $this->model_authentication->set_app_customers($customer_data);
        if($registered === false){
          $data = array("success" => false, "message" => "Unable to save customer information.");
          echo json_encode($data);
          exit();
        }

        $data = array(
            'success'     =>  true,
            'message'     =>  'Registration Successful',
            'environment' =>  ENVIRONMENT,
            'csrf_name'   =>  '',
            'csrf_hash'   =>  '',
            'url'         =>  base_url()
        );

        $user = $this->model_authentication->get_user($email);
        if($user->num_rows() == 0){
          $data = array("success" => false, "message" => "Login Failed. Please try again");
          echo json_encode($data);
          exit();
        }

        $user = $user->row();
        $this->session->user_id    =  $user->id;
        $this->session->user_id2    =  $user->id;
        $this->session->user_type  =  get_company_name();
        $this->session->username   =  $user->username;
        $this->session->last_seen  =  $user->lastseen;
        $this->session->fname      =  ucwords(strtolower($user->first_name));
        $this->session->lname      =  ucwords(strtolower($user->last_name));
        $this->session->address    =  ucwords(strtolower($user->address1));
        $this->session->city       =  ucwords(strtolower($user->address2));
        $this->session->email      =  $user->email;
        $this->session->conno      =  $user->conno;

        session_write_close();
        if(!$data['success']){
            $data['csrf_name'] = $this->security->get_csrf_token_name();
            $data['csrf_hash'] = $this->security->get_csrf_hash();
        }

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($email);
        $this->email->subject("Your Registration was Successful");
        $email_data['email_code'] = $email_code;
        $email_data['fname'] = $fname;
        $email_data['lname'] = $lname;
        $email_data['fullname'] = $fname.' '.$lname;
        $this->email->message($this->load->view("emails/registration_success", $email_data, TRUE));
        $this->email->send();

        echo json_encode($data);

      }catch(Exception $e){
        $data = array(
            'success'     => "error",
            'message'     => $e->message(),
            'environment' => ENVIRONMENT
        );
        echo json_encode($data);
      }


    }

    public function forgot_password()
    {
      $this->load->model('auth/model_authentication');
      $forgot_email = sanitize($this->input->post('forgot_email'));
      $this->form_validation->set_rules('forgot_email', 'Email', 'required|valid_email');

      if($this->form_validation->run() === FALSE){
        $response['environment']  =  ENVIRONMENT;
        $response['success']      =  false;
        $response['message']      =  validation_errors();
        $response['csrf_name']    =  $this->security->get_csrf_token_name();
        $response['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($response);
        exit();
      }

      $isExist = $this->model_authentication->get_user($forgot_email);
      if($isExist->num_rows() == 0){
        $data = array("success" => false, "message" => "There is no account with the email you provided. Please try again.");
        echo json_encode($data);
        exit();
      }

      $user = $isExist->row();
      $token = en_dec('en','CloupandaInc');
      $email_hash = removeSpecialchar(en_dec('en',$forgot_email));
      $date_hash = en_dec('en',today());
      $reset_link = base_url('auth/reset/'.$token.'/'.$email_hash.'/'.$date_hash);

      try{
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($forgot_email);
        $this->email->subject(get_company_name()." | Reset Password");
        $email_data['reset_link'] = $reset_link;
        $email_data['fname'] = $user->first_name;
        $email_data['lname'] = $user->last_name;
        $email_data['fullname'] = $user->first_name.' '.$user->last_name;
        $this->email->message($this->load->view("emails/reset_password", $email_data, TRUE));
        $this->email->send();
      }catch(Exception $err){
        // echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
        $data = array("success" => false, "message" => "Unable to send email code. Please try again");
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "An email was sent to the email you provided. Please check your email.");
      echo json_encode($data);

    }

    public function reset($token,$email_hash,$date)
    {
      if(en_dec('dec', $token) != "CloupandaInc"){
        header("Location: ".base_url());
        exit();
      }

      if(today() != en_dec('dec', $date)){
        header("Location: ".base_url());
        exit();
      }

      $data['categories'] = null;
      $data['email_hash'] = $email_hash;
      $this->load->view("includes/header", $data);
      $this->load->view("auth/reset_password");
    }

    public function reset_password()
    {
      $this->load->model('auth/model_authentication');
      $new_pass = sanitize($this->input->post('new_pass'));
      $con_new_pass = sanitize($this->input->post('con_new_pass'));
      $email = sanitize(en_dec('dec',$this->input->post('email')));

      $validation = array(
          array('new_pass','New Password','required|min_length[8]'),
          array('con_new_pass','Confirm New Password','required|min_length[8]|matches[new_pass]')
      );

      foreach ($validation as $value) {
          $this->form_validation->set_rules($value[0],$value[1],$value[2]);
      }

      if($this->form_validation->run() === FALSE){
        $response['environment']  =  ENVIRONMENT;
        $response['success']      =  false;
        $response['message']      =  validation_errors();
        $response['csrf_name']    =  $this->security->get_csrf_token_name();
        $response['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($response);
        exit();
      }

      $isExist = $this->model_authentication->validate_username($email);
      if($isExist->num_rows() == 0){
        $data = array("success" => false, "message" => "Invalid Account. Please try again.");
        echo json_encode($data);
        exit();
      }

      $hash_password = password_hash($con_new_pass,PASSWORD_BCRYPT);
      $updated = $this->model_authentication->update_password($hash_password,$email);
      if($updated === false){
        $data = array("success" => false, "message" => "Unable to reset password. Please try again");
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "Password reset successful.", "url" => base_url('user/login'));
      echo json_encode($data);
      exit();
    }

    public function check_email(){
      $this->load->model('auth/model_authentication');
      $email = sanitize($this->input->post('email'));
      $isExist = $this->model_authentication->validate_username($email);
      if($isExist->num_rows() > 0){
        $data = array("success" => 0, "message" => "Email is already registered. You can login <u><a href = '".base_url('user/login')."'>here</a></u>");
        generate_json($data);
        exit();
      }

      $data = array("success" => 1, "message" => "Pass");
      generate_json($data);
    }

}
