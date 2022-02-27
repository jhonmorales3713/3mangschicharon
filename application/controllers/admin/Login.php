<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
    }

    public function index(){
        $this->load->view('admin/login/index');
    }

    public function signin(){
        
    }


}