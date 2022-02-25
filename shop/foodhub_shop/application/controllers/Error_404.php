<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_404 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // load base_url
        $this->load->helper('url');
    }

    public function index(){
        $this->output->set_status_header('404'); 

        $data['categories'] = $this->model->getCategories()->result_array();
        $data['get_banners'] = $this->model->get_banners();
        $this->load->view('includes/header', $data);
        $this->load->view('error_404');

    }

}