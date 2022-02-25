<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Billing_merchant extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('accounts/model_billing_merchant');
  }

  public function shopandaUKey(){
			return "ShopandaKeyCloud3578";
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

  public function index($token = "",$billcode = ""){
    $this->isLoggedIn();
    //start - for restriction of views
    $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
    $main_nav_id = $this->views_restriction($content_url);
    //end - for restriction of views main_nav_id

    // start - data to be used for views
    $data_admin = array(
        'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $this->model_billing_merchant->get_shop_options(),
        'payments' => $this->model_billing_merchant->get_options(),
        'billcode' => $billcode
    );
    // end - data to be used for views

    // start - load all the views synchronously
    $this->load->view('includes/header', $data_admin);
    $this->load->view('accounts/billing_merchant', $data_admin);
    // end - load all the views synchronously
  }

  public function get_billing_table(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_billing_merchant->get_billing_merchant_table($search);
    echo json_encode($data);
  }

  public function billing_merchant(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processBilling_toktokmall($cronkey, $trandate);
  }

  public function processBilling_merchant($cronkey,$trandate,$id = 0){
    if($cronkey == $this->shopandaUKey())
    {
        $res = $this->model_billing_merchant->process_billing_merchant($trandate,$id);
    }//cronkey checking close

    echo "DONE";
    // $this->deduction($trandate);
  }


}
