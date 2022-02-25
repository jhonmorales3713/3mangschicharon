<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customer_profile extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('profile/model_customer_profile');

  }

  public function check_allow_login(){
      if (allow_login() == 0) {
          $this->session->unset_userdata("user_id");
          header("Location: ".base_url());
      }
  }

  public function is_logged_in(){
    if(!isset($this->session->user_id2) || $this->session->user_id2 == ""){
      $this->logout();
      exit();
    }
  }

  public function logout(){
      // for customer auth audit trail
      if(isset($this->session->user_id2) && $this->session->user_id2 != ''){
        $this->load->model('auth/model_authentication');
        $audit_trail_data = array("user_id" => $this->session->user_id2, "action" => "logout");
        $this->model_authentication->set_customer_auth_audittrail($audit_trail_data);
      }
      $this->session->sess_destroy();
      header("location: ".base_url());
  }

  public function index(){
    $email = (isset($this->session->jc_seller_login) && $this->session->jc_seller_login == 1)
    ? $this->session->user_id : $this->session->email;
    $this->is_logged_in();
    $this->load->model('auth/model_authentication');
    $user = $this->model_authentication->get_user($email)->row();
    $data['categories'] = null;
    $data['user'] = $user;
    $this->load->view("includes/header", $data);
    $this->load->view("profile/profile");
  }

  public function update_profile(){
    $this->is_logged_in();
    $this->load->model('auth/model_authentication');
    $validation = array(
        array('fname','First Name','required'),
        array('lname','Last Name','required'),
        array('email','Email','required|valid_email'),
        array('gender','Gender','required'),
        array('conno','Contact Number','required|regex_match[/^[0-9]{11}$/]|max_length[11]|min_length[11]'),
        array('birthday','Birthdate','required')
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
      die();
    }

    try{

      $fname = sanitize($this->input->post('fname'));
      $lname = sanitize($this->input->post('lname'));
      $email = sanitize($this->input->post('email'));
      $conno = sanitize($this->input->post('conno'));
      $gender = sanitize($this->input->post('gender'));
      $birthday = sanitize($this->input->post('birthday'));

      $isExist = $this->model_authentication->validate_customer_email($email,$this->session->user_id2);
      if($isExist->num_rows() > 0){
        $data = array("success" => false, "message" => "Email already taken");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }

      $update_data = array(
        "first_name" => $fname,
        "last_name" => $lname,
        "email" => $email,
        "conno" => $conno,
        "gender" => $gender,
        "birthdate" => $birthday
      );

      $updated = $this->model_customer_profile->update_profile($update_data,$this->session->user_id2);
      if($updated === false){
        $data = array("success" => false, "message" => "Unable to update profile. Please try again");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }

      $this->session->fname = $fname;
      $this->session->lname = $lname;
      $this->session->name = $fname." ".$lname;

      $data = array("success" => true, "message" => "Profile updated successfully.");
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

  public function address(){
    $this->is_logged_in();
    $data['categories'] = null;
    $data['addresses'] = $this->model_customer_profile->get_user_address($this->session->user_id2);
    $data['regions'] = $this->model->get_region()->result_array();
    $this->load->view("includes/header", $data);
    $this->load->view("profile/address");
  }

  public function add_address(){
    $this->is_logged_in();
    $this->load->model('auth/model_authentication');
    $validation = array(
        array('receiver_name','Receiver name','required'),
        array('receiver_no','Receiver mobile no.','required|regex_match[/^[0-9]{11}$/]|max_length[11]|min_length[11]'),
        array('receiver_address','Address','required'),
        array('region','Region','required'),
        array('city','City','required'),
        array('country','country','required')
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

      $receiver_name = sanitize($this->input->post('receiver_name'));
      $receiver_no = sanitize($this->input->post('receiver_no'));
      $receiver_address = sanitize($this->input->post('receiver_address'));
      $region = sanitize($this->input->post('region'));
      $city = sanitize($this->input->post('city'));
      $country = sanitize($this->input->post('country'));
      $postal_code = sanitize($this->input->post('postal_code'));
      $landmark = sanitize($this->input->post('landmark'));
      $isExist = $this->model_customer_profile->get_user_address($this->session->user_id2);

      $address_data = array(
        "customer_id" => $this->session->user_id2,
        "receiver_name" => $receiver_name,
        "receiver_contact" => $receiver_no,
        "address" => $receiver_address,
        "region_id" => $region,
        "province_id" => 0,
        "brgy_id" => 0,
        "municipality_id" => $city,
        "landmark" => $landmark,
        "postal_code" => $postal_code,
        "default_add" => ($isExist->num_rows() > 0) ? 0 : 1 // set default address if no existing add.
      );

      $inserted = $this->model_customer_profile->set_address($address_data);
      if($inserted === false){
        $data = array("success" => false, "message" => "Unable to save address. Please try again.");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }

      if($isExist->num_rows() == 0){
        $this->session->receiver_name = $address_data['receiver_name'];
        $this->session->receiver_address = $address_data['address'];
        $this->session->receiver_landmark = $address_data['landmark'];
        $this->session->receiver_postal_code = $address_data['postal_code'];
        $this->session->receiver_region_id = $address_data['region_id'];
        $this->session->receiver_municipality_id = $address_data['municipality_id'];
        $this->session->receiver_conno = $address_data['receiver_contact'];
      }

      $data = array("success" => true, "message" => "Address save successfully");
      echo json_encode($data);
      exit();

    }catch(Exception $e){
      $data = array(
          'success'     => "error",
          'message'     => $e->message(),
          'environment' => ENVIRONMENT
      );
      echo json_encode($data);
    }
  }

  public function update_address(){
    $this->is_logged_in();
    $validation = array(
        array('update_receiver_name','Receiver name','required'),
        array('update_receiver_no','Receiver mobile no.','required|regex_match[/^[0-9]{11}$/]|max_length[11]|min_length[11]'),
        array('update_receiver_address','Address','required'),
        array('update_region','Region','required'),
        array('update_city','City','required'),
        array('update_country','country','required')
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

    try {
      $uid = en_dec('dec',$this->input->post('uid'));
      $receiver_name = sanitize($this->input->post('update_receiver_name'));
      $receiver_no = sanitize($this->input->post('update_receiver_no'));
      $receiver_address = sanitize($this->input->post('update_receiver_address'));
      $region = sanitize($this->input->post('update_region'));
      $city = sanitize($this->input->post('update_city'));
      $country = sanitize($this->input->post('update_country'));
      $postal_code = sanitize($this->input->post('update_postal_code'));
      $landmark = sanitize($this->input->post('update_landmark'));

      $update_data = array(
        "receiver_name" => $receiver_name,
        "receiver_contact" => $receiver_no,
        "address" => $receiver_address,
        "region_id" => $region,
        "municipality_id" => $city,
        "landmark" => $landmark,
        "postal_code" => $postal_code,
      );

      $updated = $this->model_customer_profile->update_address($update_data,$uid);
      if($updated === false){
        $data = array("success" => false, "message" => "Unable to update address. Please try again");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "Address updated successfully");
      echo json_encode($data);

    } catch(Exception $e){
      $data = array(
          'success'     => "error",
          'message'     => $e->message(),
          'environment' => ENVIRONMENT
      );
      echo json_encode($data);
      exit();
    }

  }

  public function set_default_address(){
    $this->is_logged_in();
    $defaultid = en_dec('dec',sanitize($this->input->post('defaultid')));
    $updated = $this->model_customer_profile->update_default_address($defaultid,$this->session->user_id2);
    if($updated['success'] === false){
      $data = array("success" => false, "message" => "Unable to change default address");
      exit();
      echo json_encode($data);
    }

    if($updated['data']->num_rows() > 0){
      $add = $updated['data']->row();
      $this->session->receiver_name = $add->receiver_name;
      $this->session->receiver_address = $add->address;
      $this->session->receiver_landmark = $add->landmark;
      $this->session->receiver_postal_code = $add->postal_code;
      $this->session->receiver_region_id = $add->region_id;
      $this->session->receiver_municipality_id = $add->municipality_id;
      $this->session->receiver_conno = $add->receiver_contact;
    }

    $data = array("success" => true, "message" => "Default address change.");
    echo json_encode($data);
  }

  public function delete_address(){
    $this->is_logged_in();
    $delid = en_dec('dec',sanitize($this->input->post('delid')));
    $deleted = $this->model_customer_profile->update_address_status($delid);
    if($deleted === false){
      $data = array("success" => false, "message" => "Unable to delete address. Please try again");
      echo json_encode($data);
      exit();
    }

    $data = array("success" => true, "message" => "Address deleted successfully");
    echo json_encode($data);
    exit();
  }

  public function password(){
    $this->is_logged_in();

    $data['categories'] = null;
    $this->load->view("includes/header", $data);
    $this->load->view("profile/password");
  }

  public function update_password(){
    $this->is_logged_in();
    $this->load->model('auth/model_authentication');
    $validation = array(
        array('current_pass','Current Password','required'),
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

    try {

      $current_pass = sanitize($this->input->post('current_pass'));
      $new_pass = sanitize($this->input->post('new_pass'));
      $con_new_pass = sanitize($this->input->post('con_new_pass'));

      $result = $this->model_authentication->get_user($this->session->username);
      if($result->num_rows() == 0){
        $this->logout();
        exit();
      }

      $user = $result->row();
      if(password_verify($current_pass,$user->password)){

        $hash_password = password_hash($con_new_pass,PASSWORD_BCRYPT);
        $updated = $this->model_customer_profile->update_password($hash_password,$this->session->user_id2);
        if($updated === false){
          $data = array("success" => false, "message" => "Unable to change password. Please try again");
          echo json_encode($data);
          exit();
        }

        $data = array("success" => true, "message" => "Password change successfully");
        echo json_encode($data);

      }else{
        $data = array("success" => false, "message" => "Invalid current password");
        echo json_encode($data);
        exit();
      }

    } catch(Exception $e){
      $data = array(
          'success'     => "error",
          'message'     => $e->message(),
          'environment' => ENVIRONMENT
      );
      echo json_encode($data);
      exit();
    }
  }

  public function purchases(){
    $this->is_logged_in();
    $this->session->set_userdata('load_count', 5);
    $this->session->set_userdata('load_count2', 10);

    $data['categories'] = null;
    $data['orders'] = $this->model_customer_profile->get_app_order_details($this->session->user_id2);
    $data['orders_count'] = $this->model_customer_profile->get_app_order_details($this->session->user_id2,null,0,10)->num_rows();
    $data['order_shops'] = $this->model_customer_profile->get_app_order_shipping_shop($this->session->user_id2);
    $data['order_logs'] = $this->model_customer_profile->get_app_order_logs($this->session->user_id2);
    $this->load->view("includes/header", $data);
    $this->load->view("profile/purchases");
  }

  public function purchases_status(){
    $this->is_logged_in();
    $status = sanitize($this->input->post('status'));
    switch ($status) {
      case 'all':
        $this->session->load_count = 5;
        $status = null;
        break;
      case 'to_pay':
        $this->session->load_count = 5;
        $status = 0;
        break;
      case 'to_ship':
        $this->session->load_count = 5;
        $status = 1;
        break;
      default:
        $this->session->load_count = 5;
        $status = null;
        break;
    }

    $orders = $this->model_customer_profile->get_app_order_details($this->session->user_id2,$status);
    if($orders->num_rows() == 0){
      $data = array("success" => false, "message" => "You don't have any orders to pay.");
      $data['csrf_name']    =  $this->security->get_csrf_token_name();
      $data['csrf_hash']    =  $this->security->get_csrf_hash();
      echo json_encode($data);
      exit();
    }

    $order_shops = $this->model_customer_profile->get_app_order_shipping_shop($this->session->user_id2,$status);
    $order_logs = $this->model_customer_profile->get_app_order_logs($this->session->user_id2,$status);
    $data = array(
      "success" => true,
      "orders" => $orders->result(),
      "order_shops" => $order_shops->result(),
      "order_logs" => $order_logs->result()
    );
    echo json_encode($data);
  }

  public function purchase_load_more(){
    $this->is_logged_in();
    $status = sanitize($this->input->post('status'));
    switch ($status) {
      case 'to_pay':
        $status = 0;
        break;
      case 'to_ship':
        $status = 1;
        break;
      case 'all':
        $status = null;
        break;
      default:
        $status = null;
        break;
    }

    $load_count = $this->session->load_count;
    $load_count2 = $this->session->load_count2;

    // die('load'.$load_count2);

    $orders_count = $this->model_customer_profile->get_app_order_details($this->session->user_id2,$status,$load_count2)->num_rows();
    $orders = $this->model_customer_profile->get_app_order_details($this->session->user_id2,$status,$load_count);
    if($orders->num_rows() == 0){
      $data = array("success" => false, "message" => "No more data to show");
      $data['csrf_name']    =  $this->security->get_csrf_token_name();
      $data['csrf_hash']    =  $this->security->get_csrf_hash();
      echo json_encode($data);
      exit();
    }

    $order_shops = $this->model_customer_profile->get_app_order_shipping_shop($this->session->user_id2,$status,$load_count);
    $order_logs = $this->model_customer_profile->get_app_order_logs($this->session->user_id2,$status,$load_count);
    $this->session->load_count += 5;
    $this->session->load_count2 += 5;
    $data = array(
      "success" => true,
      "orders" => $orders->result(),
      "order_shops" => $order_shops->result(),
      "order_logs" => $order_logs->result(),
      "orders_count" => $orders_count
    );
    echo json_encode($data);
  }

  public function get_vouchers(){
    $refno = $this->input->post('refno');
    $shopid = $this->input->post('shopid');
    $vouchers = $this->model_customer_profile->get_app_order_vouchers($refno,$shopid);
    if($vouchers->num_rows() > 0){
      $data = array("vouchers" => $vouchers->result_array());
      generate_json($data);
    }else{
      $data = array( "vouchers" => 0);
      generate_json($data);
    }
  }

  public function get_order_history(){
    // die('heelo');
    // $this->is_logged_in();
    $refno = $this->input->post('refno');
    $shopid = $this->input->post('shopid');

    if(empty($refno) || empty($shopid)){
      $data = array("success" => 0, "message" => "Invalid order history");
      generate_json($data);
      exit();
    }

    $order_histories = $this->model_customer_profile->get_order_history($refno,$shopid);
    if($order_histories->num_rows() == 0){
      $data = array("success" => 0, "message" => "No available order history");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => $order_histories->result());
    generate_json($data);
    exit();

  }


}
