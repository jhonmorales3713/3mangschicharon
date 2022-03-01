<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct() {
        parent::__construct();        
        $this->load->helper('core');
        // $this->load->model('chapter/model_chapter');
        // $this->load->model('admin/model_events');
        // $this->load->model('model_public');
        // $this->load->model('admin/model_chapters');
        
        // $this->load->model('admin/model_settings');
        // declarations
        // $this->session->active_module = strtolower($this->router->class);
        // $this->parent_path = array('', base_url('chapter/'), 'Chapter');
    }
	public function index()
	{
		if($this->session->userdata('role') == 'admin'){
			header('Location: '.base_url('admin/Home'));
			if($this->session->userdata('role')=='admin'){

			}
		}
		var_dump($this->session->userdata());
	}
    public function logout()
    {
        if(!empty($this->session->userdata('username'))){
            //$this->log_seller_time_out_activity();
            //$this->audittrail->logActivity('Logout', $this->session->userdata('username').' have been successfully logged out.', 'logout', $this->session->userdata('username'));
        }

        $this->session->sess_destroy();
		header('Location: '.base_url('admin/Home'));
    }
	
}
