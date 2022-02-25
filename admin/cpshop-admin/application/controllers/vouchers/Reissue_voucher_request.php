<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reissue_voucher_request extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('vouchers/model_reissue_voucher_request');
  }

  public function isLoggedIn() {
      if($this->session->userdata('isLoggedIn') == false) {
          header("location:".base_url('Main/logout'));
      }
  }

  public function views_restriction($content_url){
      //this code is for destroying session and page if they access restricted page
      $access_content_nav = $this->session->userdata('access_content_nav');
      $arr_ = explode(', ', $access_content_nav); //string comma separated to array
      $get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();
      $url_content_arr = array();
      foreach ($get_url_content_db as $cun) {
          $url_content_arr[] = $cun['cn_url'];
      }

      if (in_array($content_url, $url_content_arr) == false) {
          header("location:" . base_url('Main/logout'));
      } else {
          return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
      }
  }

  public function index($token = ""){
      $this->isLoggedIn();
      if ($this->loginstate->get_access()['vc']['view'] == 1){
          //start - for restriction of views
          $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
          $main_nav_id = $this->views_restriction($content_url);
          //end - for restriction of views main_nav_id

          // start - data to be used for views
          $data_admin = array(
          'token' => $token,
          'main_nav_id' => $main_nav_id, //for highlight the navigation,
          'shops' => $this->model_reissue_voucher_request->get_shop_options()
          );
          // end - data to be used for views

          // start - load all the views synchronously
          $this->load->view('includes/header', $data_admin);
          $this->load->view('vouchers/reissue_voucher_request', $data_admin);
          // end - load all the views synchronously
      }else{
          $this->load->view('error_404');
      }
  }

  public function get_reissue_voucher_request_json(){
    $this->isLoggedIn();

    $search = $this->input->post('searchValue');
    $data = $this->model_reissue_voucher_request->get_reissue_voucher_request_json($search);
    // die($data);
    echo json_encode($data);
  }

  public function request_reissue(){
    $vrefno = en_dec('dec',$this->input->post('uid'));
    $email = $this->input->post('email');
    if(empty($vrefno)){
      $data = array("success" => 0, "message" => "Invalid Voucher Code.");
      generate_json($data);
      exit();
    }

    $validate_voucher = $this->model_reissue_voucher_request->check_voucher($vrefno);
    if($validate_voucher->num_rows() == 0){
      $data = array("success" => 0, "message" => "No result found!. Invalid Voucher Code.");
      generate_json($data);
      exit();
    }

    $inserted = $this->model_reissue_voucher_request->set_reissue_voucher_request($vrefno,$email);
    if($inserted === false){
      $data = array("success" => 0, "message" => "Requesting for reissuing of voucher failed. Please try again.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "message" => "Request for reissue voucher successful");
    generate_json($data);
  }
}
