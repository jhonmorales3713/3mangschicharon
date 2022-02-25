<?php

class Main_tips extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('tips/Model_tips');
	}
    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
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

    public function tip_off(){
        $user_id = sanitize($this->input->post('user_id'));
        
        if(!empty($user_id)){
            $this->Model_tips->tip_off($user_id);
            $data = array(
                        'success' => 1
                    );
        }else{
            $data = array(
                        'success' => 0,
                        'message' => 'Missing user id'
                    );
        }
        generate_json($data);
    }

    public function tips_section($token = '')
    {
        $this->isLoggedIn();
       
        $user_id = $this->session->userdata('id');
        $product = $this->Model_tips->check_product($this->session->userdata('sys_shop'));
        $shipping = $this->Model_tips->check_shipping_delivery($this->session->userdata('sys_shop'));
        $banners = $this->Model_tips->check_banner();
        $product_category = $this->Model_tips->check_product_category();
        $tips_status = $this->Model_tips->check_tips_status($user_id)->row()->tips;

        $data = array(
            'token' => $token,
            'shopid' => $this->session->userdata('sys_shop'),
            'user_id' => $user_id,
            'product' => $product,
            'shipping' => $shipping,
            'banners' => $banners,
            'product_category' => $product_category,
            'tips_status' => $tips_status
        );
        $this->load->view('includes/header', $data);
        $this->load->view('tips/tips', $data);
    }
}

?>