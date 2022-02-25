<?php
defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(0);
ini_set('memory_limit', '1024M');

class Settings_shipping_delivery extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('shipping_delivery/model_settings_shipping_delivery');
		$this->load->model('products/model_products');
	}

	public function logout() 
	{
		$this->session->sess_destroy();
		$this->load->view('login');
	}
	
	public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
    }

	public function shipping_delivery($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['shipping_and_delivery'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token' 	  => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'sys_shop' 	  => $this->model_products->get_sys_shop($member_id),
                'shopcode' 	  => $this->model_products->get_shopcode($member_id),
                'shops'       => $this->model_products->get_shop_options(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_shipping_delivery', $data_admin);
        }else{
            $this->load->view('error_404');
        }
	}

	public function general_list($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['general_shipping']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'    => $token,
                'sys_shop' => $this->model_products->get_sys_shop($member_id),
                'shopcode' => $this->model_products->get_shopcode($member_id),
                'shops'    => $this->model_products->get_shop_options(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_general_list', $data_admin);
        }else{
            $this->load->view('error_404');
        }
	}

	public function custom_list($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['custom_shipping']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token' => $token,
                'sys_shop' => $this->model_products->get_sys_shop($member_id),
                'shopcode' => $this->model_products->get_shopcode($member_id),
                'shops' => $this->model_products->get_shop_options(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_custom_list', $data_admin);
        }else{
            $this->load->view('error_404');
        }
	}

	public function general_rates($token = '', $shop_id)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['general_shipping']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'        => $token,
                'sys_shop'     => $this->model_products->get_sys_shop($member_id),
                'shopcode'     => $this->model_products->get_shopcode($member_id),
                'shops'        => $this->model_products->get_shop_options(),
                'shop_id'      => $this->model_settings_shipping_delivery->get_shopdetails($shop_id)->row()->id,
                'shop_id_md5'  => $shop_id,
                'shop_name'    => $this->model_settings_shipping_delivery->get_shopdetails($shop_id)->row()->shopname,
                'get_region'   => $this->model_settings_shipping_delivery->get_region()->result_array(),
                'get_province' => $this->model_settings_shipping_delivery->get_province_all()->result_array(),
                'get_citymun'  => $this->model_settings_shipping_delivery->get_citymun_all()->result_array(),
                'get_branches' => $this->model_settings_shipping_delivery->get_branches($shop_id)->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_general_rates', $data_admin);
        }else{
            $this->load->view('error_404');
        }
	}

	public function custom_profile_list($token = '', $shop_id)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['custom_shipping']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token' 	   => $token,
                'sys_shop'     => $this->model_products->get_sys_shop($member_id),
                'shopcode'     => $this->model_products->get_shopcode($member_id),
                'shops'        => $this->model_products->get_shop_options(),
                'shop_id'      => $this->model_settings_shipping_delivery->get_shopdetails($shop_id)->row()->id,
                'shop_id_md5'  => $shop_id,
                'shop_name'    => $this->model_settings_shipping_delivery->get_shopdetails($shop_id)->row()->shopname,
                'get_region'   => $this->model_settings_shipping_delivery->get_region()->result_array(),
                'get_branches' => $this->model_settings_shipping_delivery->get_branches($shop_id)->result_array(),
                'shipping_id'  => 0,
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_custom_profile_list', $data_admin);
        }else{
            $this->load->view('error_404');
        }
	}

	public function custom_rates($token = '', $shop_id, $shipping_id)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['custom_shipping']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'        => $token,
                'sys_shop'     => $this->model_products->get_sys_shop($member_id),
                'shopcode' 	   => $this->model_products->get_shopcode($member_id),
                'shops'        => $this->model_products->get_shop_options(),
                'shop_id'      => $this->model_settings_shipping_delivery->get_shopdetails($shop_id)->row()->id,
                'shop_id_md5'  => $shop_id,
                'shop_name'    => $this->model_settings_shipping_delivery->get_shopdetails($shop_id)->row()->shopname,
                'get_region'   => $this->model_settings_shipping_delivery->get_region()->result_array(),
                'get_province' => $this->model_settings_shipping_delivery->get_province_all()->result_array(),
                'get_citymun'  => $this->model_settings_shipping_delivery->get_citymun_all()->result_array(),
                'get_branches' => $this->model_settings_shipping_delivery->get_branches($shop_id)->result_array(),
                'shipping_id'  => $shipping_id,
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_custom_rates', $data_admin);
        }else{
            
        }
	}

	public function get_custom_rates_products()
	{
        $shipping_id = sanitize($this->input->post('shipping_id'));
        $shipping_products = [];
        $product_id = $this->model_settings_shipping_delivery->get_custom_rates_products($shipping_id)->result_array();
        
        foreach($product_id as $value){
            $shipping_products[] = $this->model_settings_shipping_delivery->get_product($value['product_id'])->row();
        }

        if(empty($product_id)){
            $response = [
                'success' => false,
            ];
        }else{
            $response = [
                'success' => true,
                'shipping_products' => $shipping_products,
            ];
        }
        

        echo json_encode($response);
    }

	public function get_province_old()
	{
        $regCode = sanitize($this->input->post('regCode'));

        $data = $this->model_settings_shipping_delivery->get_province($regCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
    }
    
    public function get_province()
	{
        $regCode = sanitize($this->input->post('regCode'));

        $data = $this->model_settings_shipping_delivery->get_province($regCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
	}
	
	public function get_citymun()
	{
        $provCode = sanitize($this->input->post('provCode'));

        $data = $this->model_settings_shipping_delivery->get_citymun($provCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
	}

	public function get_products()
	{
        $shop_id_md5 = sanitize($this->input->post('shop_id_md5'));
        $data = $this->model_settings_shipping_delivery->get_products($shop_id_md5)->result();

        if(count($data) == 0) // check if there's no product variant
        {
            $data = $this->model_settings_shipping_delivery->get_products2($shop_id_md5)->result();
        }

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
    }

	public function get_general_rates()
	{
        $shop_id_md5 = sanitize($this->input->post('shop_id_md5'));
        $shipping_zone_rates = [];
        $shipping_zone_branches = [];
        $shipping_id = $this->model_settings_shipping_delivery->get_general_rates($shop_id_md5)->row()->id;
        $shipping_zone = $this->model_settings_shipping_delivery->get_general_zone($shipping_id)->result_array();

        foreach($shipping_zone as $row){
            $shipping_zone_rates[] = $this->model_settings_shipping_delivery->get_general_zone_rates($row['id'])->result_array();
            $shipping_zone_branches[] = $this->model_settings_shipping_delivery->get_general_zone_branches($row['id'])->result_array();
        }

        if(empty($shipping_id)){
            $response = [
                'success' => false,
            ];
        }else{
            $response = [
                'success' => true,
                'shipping_zone' => $shipping_zone,
                'shipping_zone_rates' => $shipping_zone_rates,
                'shipping_zone_branches' => $shipping_zone_branches,
                'shipping_id' => $shipping_id
            ];
        }
        

        echo json_encode($response);
	}

	public function get_custom_rates()
	{
        $shipping_id = sanitize($this->input->post('shipping_id'));
        $shipping_zone_rates = [];
        $shipping_zone_branches = [];
        $shipping = $this->model_settings_shipping_delivery->get_custom_rates($shipping_id)->row();
        $shipping_id = $this->model_settings_shipping_delivery->get_custom_rates($shipping_id)->row()->id;
        $profile_name = $shipping->profile_name;
        $shipping_zone = $this->model_settings_shipping_delivery->get_custom_zone($shipping_id)->result_array();

        foreach($shipping_zone as $row){
            $shipping_zone_rates[] = $this->model_settings_shipping_delivery->get_custom_zone_rates($row['id'])->result_array();
            $shipping_zone_branches[] = $this->model_settings_shipping_delivery->get_general_zone_branches($row['id'])->result_array();
        }

        if(empty($shipping_id)){
            $response = [
                'success' => false,
            ];
        }else{
            $response = [
                'success' => true,
                'shipping_zone' => $shipping_zone,
                'shipping_zone_rates' => $shipping_zone_rates,
                'shipping_zone_branches' => $shipping_zone_branches,
                'shipping_id' => $shipping_id,
                'profile_name' => $profile_name
            ];
        }
        

        echo json_encode($response);
    }
	
	public function save_general_rates()
	{
        $shop_id     = sanitize($this->input->post('shop_id'));
        $rateExists  = sanitize($this->input->post('rateExists'));
        $zoneArray   = json_decode($this->input->post('zoneArray'));
        $zoneArray   = json_decode(json_encode($zoneArray), true);
        $branchArray = json_decode($this->input->post('branchArray'));
        $branchArray = json_decode(json_encode($branchArray), true);
        $rateArray   = json_decode($this->input->post('rateArray'));
        $rateArray   = json_decode(json_encode($rateArray), true);
       
        // if($rateExists != 0){
            $this->model_settings_shipping_delivery->disable_existing_rates($rateExists, $shop_id);
        // }

        $shipping_id = $this->model_settings_shipping_delivery->save_general_rates($shop_id);
        $counter = 0;
        $audit_string = "";
        
        foreach($zoneArray as $row){
            if($row['status'] == 1){
                $shipping_zone_id = $this->model_settings_shipping_delivery->save_general_rates_zone($shipping_id, $row['zone_name'], $row['regCode'], $row['provCode'], $row['citymunCode'], $row['f_key']);
                $audit_string .= $this->audittrail->shippingdeliveryZoneString($row['zone_name'], $row['regCode'], $row['provCode'], $row['citymunCode']);
                if(!empty($branchArray)){
                    foreach($branchArray as $val){
                        if($val['index_id'] == $row['f_key'] && $val['status'] != 0){
                            $this->model_settings_shipping_delivery->save_general_zone_branch($shop_id, $val['branch_id'], $shipping_zone_id);
                        }
                    }
                }
                if(!empty($rateArray)){
                    foreach($rateArray as $value){
                        if(!empty($value['status'])){
                            if($value['status'] == 1 && $value['index_id'] == $row['f_key'] ){
                                $this->model_settings_shipping_delivery->save_general_rates_zone_rates($shipping_zone_id, $value['rate_name'], $value['rate_amount'], $value['is_condition'], $value['minimum_value'], $value['maximum_value'], $value['from_day'], $value['to_day'], $value['additional_isset'], $value['set_value'], $value['set_amount']);
                                $audit_string .= $this->audittrail->shippingdeliveryRateString($row['zone_name'], $value['rate_name'], $value['rate_amount'], $value['is_condition'], $value['minimum_value'], $value['maximum_value'], $value['from_day'], $value['to_day'], $value['additional_isset'], $value['set_value'], $value['set_amount']);
                            }
                        }
                        
                    }
                }
            }
            $counter++;
        }

        

        $response = [
            'success' => true,
        ];
        $this->audittrail->logActivity('Shipping and Delivery', "General Rates has been added to ".$this->model_settings_shipping_delivery->get_shopdetails(md5($shop_id))->row()->shopname." Details:\n".$audit_string, 'Add General Rates', $this->session->userdata('username'));
        echo json_encode($response);
	}
	
	public function save_custom_rates()
	{
        $shop_id         = sanitize($this->input->post('shop_id'));
        $shipping_id     = sanitize($this->input->post('shipping_id'));
        $update          = sanitize($this->input->post('update'));
        $profile_name    = sanitize($this->input->post('profile_name'));
        $productArray    = json_decode($this->input->post('productArray'));
        $productArray    = json_decode(json_encode($productArray), true);
        $zoneArray       = json_decode($this->input->post('zoneArray'));
        $zoneArray       = json_decode(json_encode($zoneArray), true);
        $branchArray     = json_decode($this->input->post('branchArray'));
        $branchArray     = json_decode(json_encode($branchArray), true);
        $rateArray       = json_decode($this->input->post('rateArray'));
        $rateArray       = json_decode(json_encode($rateArray), true);
        $products        = array();

        if($shipping_id != 0 || $update == 1){
            $this->model_settings_shipping_delivery->disable_existing_rates_md5($shipping_id);
        }


        $shipping_id = $this->model_settings_shipping_delivery->save_custom_rates($shop_id, $profile_name);
        $counter = 0;
        $audit_string = "";
        foreach($zoneArray as $row){
            if($row['status'] == 1){
                $shipping_zone_id = $this->model_settings_shipping_delivery->save_custom_rates_zone($shipping_id, $productArray, $row['zone_name'], $row['regCode'], $row['provCode'], $row['citymunCode'], $row['f_key']);
                $audit_string .= $this->audittrail->shippingdeliveryZoneString($row['zone_name'], $row['regCode'], $row['provCode'], $row['citymunCode']);
                if(!empty($branchArray)){
                    foreach($branchArray as $val){
                        if($val['index_id'] == $row['f_key'] && $val['status'] == 1){
                            $this->model_settings_shipping_delivery->save_custom_zone_branch($shop_id, $val['branch_id'], $shipping_zone_id);
                        }
                    }
                }
                if(!empty($rateArray)){
                    foreach($rateArray as $value){
                        if(!empty($value['status'] )){
                            if($value['status'] == 1 && $value['index_id'] == $row['f_key'] ){
                                $this->model_settings_shipping_delivery->save_custom_rates_zone_rates($shipping_zone_id, $value['rate_name'], $value['rate_amount'], $value['is_condition'], $value['minimum_value'], $value['maximum_value'], $value['from_day'], $value['to_day'], $value['additional_isset'], $value['set_value'], $value['set_amount']);
                                $audit_string .= $this->audittrail->shippingdeliveryRateString($row['zone_name'], $value['rate_name'], $value['rate_amount'], $value['is_condition'], $value['minimum_value'], $value['maximum_value'], $value['from_day'], $value['to_day'], $value['additional_isset'], $value['set_value'], $value['set_amount']);
                            }
                        }
                    } 
                }
            }
            $counter++;
        }

        

        $response = [
            'success' => true,
        ];
        $audit_product_string = $this->audittrail->shippingdeliveryProductsString($productArray);
        $this->audittrail->logActivity('Shipping and Delivery', "Custom Rates with Profile Name ".$profile_name." has been added to ".$this->model_settings_shipping_delivery->get_shopdetails(md5($shop_id))->row()->shopname." Details:\n".$audit_product_string.$audit_string, 'Add Custom Rates', $this->session->userdata('username'));
        echo json_encode($response);
    }
	
	public function product_list()
	{
        $shop_id = sanitize($this->input->post('shop_id'));

        $result = $this->model_settings_shipping_delivery->product_list($shop_id);

        $data = [];
        foreach($result['filtered'] as $row) {
            $nestedData = array(); 
            $nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/img/'.$row['shopcode'].'/products-250/'.$row['Id'].'/'.removeFileExtension($row['primary_pic']).'.jpg?'.rand().'">';
            $nestedData[] = $row["itemname"];
             
            $data[] = $nestedData;
        }

        $this->response->data_table($data, $result['total']);

    }
	
	public function general_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products->get_sys_shop($member_id);
        if($sys_shop == 0) {
            $query = $this->model_settings_shipping_delivery->general_table();
        }
        
        generate_json($query);
	}

	public function custom_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products->get_sys_shop($member_id);
        if($sys_shop == 0) {
            $query = $this->model_settings_shipping_delivery->custom_table();
        }
        
        generate_json($query);
	}

	public function profile_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $shop_id_md5 = sanitize($this->input->post('shop_id_md5'));
		$sys_shop = $this->model_products->get_sys_shop($member_id);
		
        $query = $this->model_settings_shipping_delivery->profile_table($shop_id_md5);
        
        generate_json($query);
	}

	public function delete_custom_shipping()
    {
        $this->isLoggedIn();

        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id === 0) {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        } else {
            $query = $this->model_settings_shipping_delivery->delete_custom_shipping($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Custom Shipping deleted successfully!");
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }

        }

        generate_json($data);
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
}
