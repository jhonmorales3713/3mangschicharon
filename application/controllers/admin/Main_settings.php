<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Main_settings extends CI_Controller {


    public function __construct() {
        parent::__construct();        
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('customers/Model_customers');
        $this->load->library('upload');
        $this->load->library('uuid');
    }
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function settings_home($labelname = null){
        header('location:'.base_url('admin/Main_settings/'));
        $this->session->set_userdata('active_page',$labelname);
    }    

    public function change_pass($token =''){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['profile']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' ;
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result()
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/settings/change_pass',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }    

    public function validate_password(){
        if(en_dec('en',$this->input->post('oldpassword')) == $this->session->oldpass){
            echo true;
        }else{
            echo false;
        }
    }

    public function profile_home($labelname = null){
        $this->session->set_userdata('active_page',$labelname);

        $this->isLoggedIn();
        $data = array(
            'active_page' => $this->session->userdata('active_page'),
            'subnav' => true, //for highlight the navigation,
            'token' => $this->session->userdata('token_session')
        );
        
        $data['page_content'] = $this->load->view('admin/dashboard/index',$data,TRUE);
		$this->load->view('admin_template',$data,'',TRUE);
    }    

	public function views_restriction($content_url){
        //this code is for destroying session and page if they access restricted page
        $access_content_nav = $this->session->userdata('access_content_nav');
        $arr_ = explode(', ', $access_content_nav); //string comma separated to array 
        $get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();
        $url_content_arr = array();
        foreach ($get_url_content_db as $cun) {
            $url_content_arr[] = $cun['cn_url'];
        }

        if (in_array($content_url, $url_content_arr) == false){
           header("location:".base_url('Main/logout'));
        }else{
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }
    public function index(){

        $this->isLoggedIn();

        $data = array(
            'active_page' => $this->session->userdata('active_page'),
            'subnav' => true, //for highlight the navigation,
            'token' => $this->session->userdata('token_session')
        );
        $data['page_content'] = $this->load->view('admin/dashboard/index',$data,TRUE);
		$this->load->view('admin_template',$data,'',TRUE);
    }

    
    public function user_list($action = '',$token = ''){

    }
    
}