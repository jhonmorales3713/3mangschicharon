<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
        $this->load->model('model');
    }
    
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            header("location:" . base_url('Main/logout'));
        }
    }


    public function index($token = ''){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'shops'               => $this->model_products->get_shop_options(),
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data['page_content'] = $this->load->view('admin/dashboard/index',$data,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }    


}