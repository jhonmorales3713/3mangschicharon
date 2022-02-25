<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sale_settlement extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/model_sale_settlement');
    $this->load->library('form_validation');
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['ssr']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id
      // start - data to be used for views
      $data_admin = array(
      'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $this->model_sale_settlement->get_shop_options(),
        // 'payments' => $this->model_billing->get_options()
      );
      // end - data to be used for views
      $this->load->view('includes/header',$data_admin);
      $this->load->view('reports/sale_settlement',$data_admin);
    }else{
      $this->load->view('error_404');
    }
  }

  public function list_table(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_sale_settlement->list_table($search);
    echo json_encode($data);
  }
}
