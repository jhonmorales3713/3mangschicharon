<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_404 {

    protected $CI;

    public function __construct(){
        $this->CI =& get_instance();
    }

   public function view() {
        $data['categories'] = $this->CI->model->getCategories()->result_array();
        $data['get_banners'] = $this->CI->model->get_banners();
        $this->CI->output->set_status_header('404'); 

        $this->CI->load->view('includes/header', $data);
        $this->CI->load->view('error_404');

   }
}