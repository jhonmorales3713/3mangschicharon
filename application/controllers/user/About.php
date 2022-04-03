<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('model_dev_settings');
    }

    public function index()
	{		
		$data['active_page'] = 'about';		
        $data['page_content'] = $this->load->view('user/about/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    public function faqs()
	{		
		$data['active_page'] = 'about';		
        $data['faqs'] = $this->model_dev_settings->get_active_faq();
        $data['page_content'] = $this->load->view('faqs',$data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    
}