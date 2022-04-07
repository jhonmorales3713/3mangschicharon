<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
    public function __construct() {
        parent::__construct();  
		$this->load->model('user/model_home');
    }

	public function index()
	{		
		$data['active_page'] = 'home';
		$data['subnav'] = false;
		$data['has_search'] = true;
		$data['banners'] = $this->model_home->get_banners();
		
        $data['page_content'] = $this->load->view('user/home/index',$data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}

	public function contact_us()
	{		
		$data['active_page'] = 'contact_us';
		
        $data['page_content'] = $this->load->view('user/home/contact_us','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
	
}
