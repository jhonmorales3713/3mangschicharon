<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dev_settings_email_settings extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('dev_settings/Model_email_settings');
    }

    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    public function views_restriction($content_url)
    {
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

	public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }
    
	public function index() {
		if($this->session->userdata('isLoggedIn') == true) {
			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}
		$this->load->view('login');
    }    
    
    public function email_settings($token = ''){
        $this->isLoggedIn();
        //echo 'test';
        if ($this->loginstate->get_access()['email_settings']['view'] == 1) {
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token'                         => $token,
                'main_nav_id'                   => $main_nav_id, 
                'main_nav_categories'           => $this->model_dev_settings->main_nav_categories()->result_array(),
                'get_email_settings'            => $this->Model_email_settings->get_email_settings(),
            );
            
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/email_settings', $data_admin);
        }else{
             $this->load->view('error_404');
        } 
    }


    public function update_email_info(){
        $this->isLoggedIn();


        $data = array(
            'email_settings_id'            =>      sanitize($this->input->post('email_settings_id')),
            'new_product_email'            =>      sanitize($this->input->post('new_product_email')),
            'new_product_name'             =>      sanitize($this->input->post('new_product_name')),
            'new_approval_email'           =>      sanitize($this->input->post('new_approval_email')),
            'new_approval_name'            =>      sanitize($this->input->post('new_approval_name')),
            'new_verification_email'       =>      sanitize($this->input->post('new_verification_email')),
            'new_verification_name'        =>      sanitize($this->input->post('new_verification_name')),
            'shop_mcr_approval_email'      =>      sanitize($this->input->post('shop_mcr_approval_email')),
            'shop_mcr_approval_name'       =>      sanitize($this->input->post('shop_mcr_approval_name')),
            'shop_mcr_verification_email'  =>      sanitize($this->input->post('shop_mcr_verification_email')),
            'shop_mcr_verification_name'   =>      sanitize($this->input->post('shop_mcr_verification_name')),
            'shop_mcr_verified_name'       =>      sanitize($this->input->post('shop_mcr_verified_name')),
            'shop_mcr_verified_email'      =>      sanitize($this->input->post('shop_mcr_verified_email')),
        
        );

        $query = $this->Model_email_settings->emailSettings_update_data($data);
        $data = array("success" => 1, 'message' => "Record updated successfully!");
  
        generate_json($data);
    }
  

    
}
