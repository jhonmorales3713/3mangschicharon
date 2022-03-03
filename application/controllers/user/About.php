<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    public function __construct() {
        parent::__construct();                
    }

    public function index()
	{		
		$data['active_page'] = 'about';		
        $data['page_content'] = $this->load->view('user/about/index','',TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    
}