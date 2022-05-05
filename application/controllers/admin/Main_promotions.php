<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Main_promotions extends CI_Controller {


    public function __construct() {
        parent::__construct();        
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('products/model_products');
        $this->load->model('promotions/model_promotions');
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

    public function promotions_home($labelname = null){
        header('location:'.base_url('admin/Main_promotions/'));
        $this->session->set_userdata('active_page',$labelname);
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
    public function store_selectedproducts(){
        $products = $this->input->post('selected_products');
        $selectedproducts = $this->session->userdata('selected_products')!= '' ? $this->session->userdata('selected_products') : Array();
        $new_products = array();
        // foreach($selectedproducts as $product){
        //     $products_info = $this->model_products->get_productdetails(en_dec('dec',$product));
        //     $id = $products_info['parent_product_id'] != ''? $products_info['parent_product_id']:en_dec('dec',$product);
        //     // print_r($id);
        //     $variants = $this->model_products->getVariants($id);
        //     foreach($variants as $variant){
        //         if(!in_array(en_dec('en',$variant['id']),$new_products) && $variant['enabled'] == 1){
        //             $new_products[] = en_dec('en',$variant['id']);
        //         }
        //     }
        // }
        if(count($new_products) == 0 ) {
            foreach(explode(',',$products) as $product){
                $products_info = $this->model_products->get_productdetails(en_dec('dec',$product));
                $id = $products_info['parent_product_id'] != ''? $products_info['parent_product_id']:en_dec('dec',$product);
                // print_r($id);
                $variants = $this->model_products->getVariants($id);
                // print_r(explode(',',$products));
                foreach($variants as $variant){
                    if  (!in_array(en_dec('en',$variant['id']),$new_products) && $variant['enabled'] == 1 && 
                            (in_array(en_dec('en',$variant['id']),explode(',',$products)) || in_array(en_dec('en',$id),explode(',',$products)))
                        ){
                        $new_products[] = en_dec('en',$variant['id']);
                    }
                }
                // $new_products = explode(',',$products);
            }
        }
        // print_r($products);
        // print_r($new_products);
        $this->session->set_userdata('selected_products',$new_products);
        $data = Array('success'=>true,'products'=>$this->session->userdata('selected_products'));
        return $data;
    }
    public function get_selectedproducts(){
        $products = $this->session->userdata('selected_products') != '' ? $this->session->userdata('selected_products') : Array();
        $products_info = Array();
        foreach($products as $product){
            $products_info[] = $this->model_products->get_productdetails(en_dec('dec',$product));
        }
        echo generate_json($products_info);
    }
    public function save_discount(){
        $data = $this->input->post();
        $products = $this->session->userdata('selected_products') != '' ? $this->session->userdata('selected_products') : Array();
        $this->model_promotions->checkProductActiveDiscount($products,$data['date_from'],$data['date_to'],isset($data['update'])?'update':'add');
        $validation = array(
            array('date_from','Date Issue','required'),
            array('date_to','Valid Until','required'),
            array('disc_ammount','Discount Amount','required'),
            // array('usage_quantity','Usage Quantity','required'),
            // array('f_otherinfo','Other Info','required|max_length[100]|min_length[1]'),
    	);
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
    	if ($this->form_validation->run() == FALSE && validation_errors() != '') {
            $response['success'] = false;
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
    	}

        if($this->session->userdata('selected_products') == '' || count($this->session->userdata('selected_products')) == 0){ 
            $response['success'] = false;
            $response['message'] = 'Products must have atleast 1';
            echo json_encode($response);
            die();
        }

        if(isset($data['set_max_amount'])){
            if($data['set_max_amount'] != '' && $data['disc_ammount_limit'] == ''){ 
                $response['success'] = false;
                $response['message'] = 'Maximum Discount Price is required.';
                echo json_encode($response);
                die();
            }
        }
        
        if(strtotime($data['date_from']) >= strtotime($data['date_to'])){
            $response['success'] = false;
            $response['message'] = 'Date Issue cannot be greater than Validation Date';
            echo json_encode($response);
            die();
        }

        $now = time(); // or your date as well
        $daysdiff_datefrom = round(($now - strtotime($data['date_from'])) / (60 * 60 * 24));
        $daysdiff_dateto = round(($now - strtotime($data['date_to'])) / (60 * 60 * 24));
        // print_r($daysdiff_dateto);
        if( $daysdiff_dateto >= 0){
            $response['success'] = false;
            $response['message'] = 'Date To range must be greater than 1';
            echo json_encode($response);
            die();
        }
        $products = $this->session->userdata('selected_products') != '' ? $this->session->userdata('selected_products') : Array();
        $this->model_promotions->checkProductActiveDiscount($products,$data['date_from'],$data['date_to'],isset($data['update'])?'update':'add');
        if(isset($data['update'])){
            $id = $data['id'];
            $this->model_promotions->update_discount($data,$products,$id);
        }else{
            $this->model_promotions->save_discount($data,$products);
        }
        $response['success'] = true;
        $response['message'] = 'Discount Saved Successfully';
        echo json_encode($response);
        die();
        
    }

    public function remove_selectedproducts(){
        $products = $this->session->userdata('selected_products');
        $selectedproduct = $this->input->post('product');
        array_splice($products,array_search(en_dec('en',$selectedproduct),$products),1);
        $this->session->set_userdata('selected_products',$products);
    }
    public function clear_selectedproducts(){
        if($this->session->userdata('selected_products') != ''){
            $this->session->unset_userdata('selected_products');
        }
    }
    public function add_product_discount($token = ''){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['product_discount']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            // $main_nav_id = $this->views_restriction($content_url);
            $main_nav_id = 0;


            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'admin'               => true,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->session->userdata('sys_shop_id'),
                'categories'          => $this->model_products->get_category_options()
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/promotions/add_product_discount',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }
    public function add_product_voucher($token = ''){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['product_discount']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            // $main_nav_id = $this->views_restriction($content_url);
            $main_nav_id = 0;


            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'admin'               => true,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->session->userdata('sys_shop_id'),
                'categories'          => $this->model_products->get_category_options()
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/promotions/add_product_voucher',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }

    public function discount_table(){
        $this->isLoggedIn();
        $request = $_REQUEST;
        $member_id = $this->session->userdata('id');
        $sys_shop = $this->session->userdata('sys_shop_id');
        $query = $this->model_promotions->discount_table($sys_shop, $request);
        generate_json($query);
    }

    public function update_discount($token = '', $id){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['product_discount']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            // $main_nav_id = $this->views_restriction($content_url);
            $main_nav_id = 0;


            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'admin'               => true,
                'id'                  => $id,
                'discount_info'       => $this->model_promotions->get_promotion(en_dec('dec',$id)),
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->session->userdata('sys_shop_id'),
                'categories'          => $this->model_products->get_category_options()
            );
            $this->session->set_userdata('selected_products',json_decode($data_admin['discount_info']['product_id']));

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/promotions/update_product_discount',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }
    
    public function change_status(){
        $data = $this->input->post();
        $status = $data['status'];
        $date_from = isset($data['date_from'])?$data['date_from']:'';
        $date_to = isset($data['date_from'])?$data['date_to']:'';
        $id = $data['id'];
        $this->model_promotions->change_status($id, $status,$date_from,$date_to);
        if($status == 1){
            $message = 'enabled';
        }
        if($status == 0){
            $message = 'deleted';
        }
        if($status == 2){
            $message = 'disabled';
        }
        $result = Array(
            'success' => true,
            'message' => 'Discount was '.$message.' successfully'
        );
        generate_json($result);
    }
    public function products_discount_list($token = ''){
        
        $this->isLoggedIn();
        if($this->loginstate->get_access()['product_discount']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'admin'               => true,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result()
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/promotions/products',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }
}