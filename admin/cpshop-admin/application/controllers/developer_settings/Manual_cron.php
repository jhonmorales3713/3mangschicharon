<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Manual_cron extends CI_Controller {
  public function __construct(){
    parent::__construct();
  }

  public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
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

    if($this->loginstate->get_access()['overall_access'] == 1
      && $this->loginstate->get_access()['manual_cron'] == 1){
        //start - for restriction of views
        $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
        $main_nav_id = $this->views_restriction($content_url);
        //end - for restriction of views main_nav_id

        // start - data to be used for views
        $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            "shops" => $this->model->get_all_shops()
        );
        // end - data to be used for views

        // start - load all the views synchronously
        $this->load->view('includes/header', $data_admin);
        $this->load->view('dev_settings/manual_cron', $data_admin);
    }else{
      $this->load->view('error_404');
    }

  }
}
