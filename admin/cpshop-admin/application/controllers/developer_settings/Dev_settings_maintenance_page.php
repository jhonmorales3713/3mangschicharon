<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dev_settings_maintenance_page extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('dev_settings/Model_maitenance_page');
        //load model or libraries below here...
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
    
    public function maintenance_page($token = ''){
        $this->isLoggedIn();
        //echo 'test';
        if ($this->loginstate->get_access()['maintenance_page']['view'] == 1) {
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),

            );
            
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/maintenance_page', $data_admin);
        }else{
             $this->load->view('error_404');
        } 
    }
  
    public function client_information_table(){
        $this->isLoggedIn();
        $query = $this->Model_maitenance_page->client_information_table();
        generate_json($query);
    }

    public function edit_client_information($token = '', $c_id)
    {        

        //echo $c_id;
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['maintenance_page']['update'] == 1) {
            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'c_id'                => $c_id,
                'coming_soon_cover'   => $this->Model_maitenance_page->get_client_info_data($c_id)->row(),
            );
                // die($coming_soon_cover );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/update_maintenance_page', $data_admin);
        }else{
            $this->load->view('error_404');
        }
       
    }

    public function update_client_info(){
        $this->isLoggedIn();

        $c_id           = sanitize($this->input->post('c_id'));
        $csc_local           = sanitize($this->input->post('csc_local'));
        $csc_test         = sanitize($this->input->post('csc_test'));
        $csc_live       = sanitize($this->input->post('csc_live'));
        $csc_local_pass      = sanitize($this->input->post('csc_local_pass'));
        $csc_test_pass        = sanitize($this->input->post('csc_test_pass'));
        $csc_live_pass      = sanitize($this->input->post('csc_live_pass'));

       // echo "ID:$c_id <br> comingsoon_local  $csc_local  <br> comingsoon_test $csc_test <br>  comingsoon_live   $csc_live ";
      

        $query = $this->Model_maitenance_page->client_info_update_data($csc_local, $csc_test, $csc_live, $csc_local_pass, $csc_test_pass, $csc_live_pass, $c_id);
        $data = array("success" => 1, 'message' => "Record updated successfully!");
  
        generate_json($data);
    }
    
}
