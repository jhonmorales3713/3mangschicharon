<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main_page extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('tips/Model_tips');
    }


	public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

	public function isLoggedIn() {
		if($this->session->userdata('isLoggedIn') == false) {
			header("location:".base_url('Main/logout'));
		}
	}

	//insert all main navigation here //
	//note: make sure name of the function is the same name of main_nav_href column in jcw_main_navigation. see database first
	public function display_page($page_name,$token) {
		$this->isLoggedIn();
		//this function is dependent on what inside in main_nav_href column of jcw_main_navigation
		//$page_name is dynamic = main_nav_href
		//see the configuration in config/routes.php
		//$route['main_page/(:any)'] = 'main_page/display_page/$1';
		$data = array(
			 // get data using email
			'token' => $token,
			// 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		// Allow Merchant to view the Merchant Order List even without the Order List access
		if ($page_name == 'orders_home') {
			$merchant_order_list = $this->model_settings->get_cp_content_navigation('Merchant Order List');
			$data['merchant_order_list'] = $merchant_order_list;
		}

		

		// ---- START SHOP WALLET ----
		$functions = json_decode($this->session->functions);
		if($page_name == "wallet_home" && $functions->wallet == 1){
			// $this->load->model('wallet/model_prepayment');
			$wallet = $this->model->get_shop_wallet($this->session->sys_shop_id,$this->session->branchid);
			if($wallet->num_rows() > 0){
				$page_name = "shop_wallet";
				$data['balance'] = $wallet->row()->balance;
				$data['refnum'] = $wallet->row()->refnum;
				$data['total_sales'] = $this->model->get_total_deduction($this->session->sys_shop,$this->session->branchid);
			}
		}
		// ---- END SHOP WALLET ----
		if($page_name == "home"){
			$user_id = $this->session->userdata('id');
			$data['shopid'] = $this->model_shops->get_sys_shop($user_id);

		}

		$this->load->view('includes/header', $data);
		if($page_name == "home"){
			if($this->Model_tips->is_tipsOn($user_id)){
				$product = $this->Model_tips->check_product($this->session->userdata('sys_shop'));
            	$shipping = $this->Model_tips->check_shipping_delivery($this->session->userdata('sys_shop'));
            	$banners = $this->Model_tips->check_banner();
            	$product_category = $this->Model_tips->check_product_category();
            	$tips_status = $this->Model_tips->check_tips_status($user_id)->row()->tips;

				$data['banners'] = $banners;
            	$data['product_category'] = $product_category;
            	$data['tips_status'] = $tips_status;
            	$data['product'] = $product;
            	$data['shipping'] = $shipping;
				$data['user_id'] = $user_id;
            	$this->load->view('tips/tips', $data);
        	}else{
            	$this->load->view('main_navigation/'.$page_name, $data);
        	}
    	}else{
			$this->load->view('main_navigation/'.$page_name, $data);
    	}
	}

	public function page_under_construction($token) {
		$this->isLoggedIn();
		$data = array(
			 // get data using email
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data);
		$this->load->view('atwork', $data);
	}
	//end of insert all main navigation here //

	//SALES CHART
	public function dr_listing_report_table()
    {
        $query = $this->homepage->dr_listing_report_table();
        echo json_encode($query);
    }

    public function po_receive_report_table()
    {
        $query = $this->homepage->po_receive_report_table();
        echo json_encode($query);
    }
}
