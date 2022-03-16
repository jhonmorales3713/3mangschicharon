<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Main_orders extends CI_Controller {


    public function __construct() {
        parent::__construct();        
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('orders/model_orders');
        $this->load->library('upload');
        $this->load->library('uuid');
    }
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            
            //header("location:" . base_url('Main/logout'));
        }
    }

    public function orders_home($labelname = null){
        $this->session->set_userdata('active_page',$labelname);
        header('location:'.base_url('admin/Main_orders/'));
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
    

    public function order_table()
    {
        $this->isLoggedIn();
        
        $query = $this->model_orders->order_table();
        
        
        generate_json($query);
    }
    
    public function orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'cities'            => $this->model_orders->get_cities()->result_array()
            );


            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/orders/orders',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }

        
    }

    public function orders_view($token = '', $ref_num = '')
    {
        $this->isLoggedIn();
        if( $this->loginstate->get_access()['orders']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = 'Orders';
            //$main_nav_id = $this->views_restriction($content_url);
            $row            = $this->model_orders->orders_details($ref_num)[0];
            //$order_items    = $this->model_orders->order_item_table_print($reference_num, $sys_shop);
            //$refundedOrder  = $this->model_orders->get_refundedOrder($reference_num, $sys_shop)->result_array();
            // $orders_history = $this->model_orders->orders_history($row['order_id']);
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'reference_num'       => $ref_num,
                'order_details'       => $row,
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/orders/orders_view',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }
    public function order_item_table()
    {
        $this->isLoggedIn();
        $reference_num = sanitize($this->input->post('reference_num'));
        $query = $this->model_orders->order_item_table($reference_num);
        
        generate_json($query);
    }

    
}