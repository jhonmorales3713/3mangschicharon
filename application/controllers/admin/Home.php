<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
        $this->load->model('model');
    }

    public function index(){        
        $data['active_page'] = 'dashboard';
        $view_data['mewo'] = 'dashboard';
        $data['page_content'] = $this->load->view('admin/dashboard/index',$view_data,TRUE);
		$this->load->view('admin_template',$data,'',TRUE);
    }    


}