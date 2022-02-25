<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Shops_merchant_registration extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('dev_settings/Model_merchant_registration');
        $this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
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
    
    public function merchant_registration($token = ''){
        $this->isLoggedIn();
        //echo 'test';
        if ($this->loginstate->get_access()['merchant_registration']['view'] == 1) {
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array()
            );
            
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/merchant_registration', $data_admin);
        }else{
             $this->load->view('error_404');
        } 
    }
  
    public function merchant_registration_table(){
        $this->isLoggedIn();
        $query = $this->Model_merchant_registration->merchant_registration_table();
        generate_json($query);
    }

    public function view_merchant_registration($token = '', $id)
    {        

        //echo $c_id;
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['merchant_registration']['view'] == 1) {

            $region   = $this->Model_merchant_registration->get_all_region()->result();
            $province = $this->Model_merchant_registration->get_all_province()->result();
            $citymun  = $this->Model_merchant_registration->get_all_citymun()->result();

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'id'                  => $id,
                'merchant_details'    => $this->Model_merchant_registration->get_merchant_details($id)->row_array(),
                'sys_shop'            => $this->session->userdata('sys_shop'),
                'get_currency'        => $this->Model_merchant_registration->get_currency()->result_array(),
                'region'              => $region,
                'province'            => $province,
                'citymun'             => $citymun
            );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/view_merchant_registration', $data_admin);
        }else{
            $this->load->view('error_404');
        }
       
    }

    public function approveApplication(){



        $id               = sanitize($this->input->post('id'));
        $shopcode         = $this->generateUniqueShopCode();
        $merchant_details = $this->Model_merchant_registration->get_merchant_details($id)->row_array();
        $checkEmailExist  = $this->Model_merchant_registration->checkEmailExist($merchant_details['ci_email'])->num_rows();
        $checkDataExist  = $this->Model_merchant_registration->checkDataExist($this->input->post('id'))->num_rows();


        // echo $checkDataExist;
        // die();
        if($checkDataExist === 1){

            $response['success'] = false;
            $response['message'] = 'Required fields empty. Make sure to save your changes before approving.';

        } else if($checkEmailExist == 0){
            // $this->sendApprovedApp($merchant_details);

            $com_rate_array = array(
            'merchant_comrate' => sanitize($this->input->post('merchant_comrate')) * 2,
            'f_startup' => sanitize($this->input->post('f_startup')),
            'f_jc' => sanitize($this->input->post('f_jc')),
            'f_mcjr' => sanitize($this->input->post('f_mcjr')),
            'f_mc' => sanitize($this->input->post('f_mc')),
            'f_mcsuper' => sanitize($this->input->post('f_mcsuper')),
            'f_mcmega' => sanitize($this->input->post('f_mcmega')),
            'f_mcsuper' => sanitize($this->input->post('f_mcsuper')),
            'f_others' => sanitize($this->input->post('f_others')),
            );
        

            $sys_shop = $this->Model_merchant_registration->save_shop($shopcode, $com_rate_array, $merchant_details);

            $url = base_url().'settings/User_list/create_data';
            $data = array(
                'f_email' => $merchant_details['ci_email'],
                'f_first_name' => $merchant_details['cn_first_name'],
                'f_last_name' => $merchant_details['cn_last_name'],
                'ac_dashboard_view' => 1,
                'istoktok' => 1,
                'seller_access' => 1,
                'main_nav_ac_orders_view' => 1,
                'main_nav_ac_products_view' => 1,
                'main_nav_ac_shops_view' => 1,
                'main_nav_ac_accounts_view' => 1,
                'main_nav_ac_wallet_view' => 1,
                'main_nav_ac_reports_view' => 1,
                'main_nav_ac_settings_view' => 1,
                'ac_dashboard_sales_count' => 1,
                'ac_dashboard_transactions_count' => 1,
                'ac_dashboard_views_count' => 1,
                'ac_dashboard_overall_sales_count' => 1,
                'ac_dashboard_visitors_chart' => 1,
                'ac_dashboard_sales_chart' => 1,
                'ac_dashboard_top10productsold_list' => 1,
                'ac_dashboard_transactions_chart' => 1,
                // 'ac_transactions_view' => 1,
                'ac_merchant_ol_view' => 1,
                'ac_transactions_update' => 1,
                'ac_transactions_reassign' => 1,
                'ac_transactions_process_order' => 1,
                'ac_transactions_ready_pickup' => 1,
                'ac_transactions_booking_confirmed' => 1,
                'ac_transactions_mark_fulfilled' => 1,
                'ac_returntosender' => 1,
                'ac_redeliver' => 1,
                'ac_transactions_shipped' => 1,
                'ac_confirmed' => 1,
                'ac_paid_orders' => 1,
                'ac_readyforprocessing_orders' => 1,
               'ac_bookingconfirmed_orders' => 1,
               'ac_processing_orders' => 1,
               'ac_readyforpickup_orders' => 1,
               'ac_fulfilled_orders' => 1,
               'ac_shipped_orders' => 1,
               'ac_returntosender_orders' => 1,
               'ac_voided_orders' => 1,
               'ac_manualorder_list' => 1,
               'ac_manualorder_list_create' => 1,
               'ac_refund_order_approval_view' => 1,
               'ac_refund_order_approval_update' => 1,
               'ac_refund_order_approval_approve' => 1,
               'ac_refund_order_approval_reject' => 1,
               'ac_refund_order_create' => 1,
               'ac_refund_order_trans_view' => 1,
               'ac_pending_orders' => 1,
               'ac_forpickup_orders' => 1,
               'ac_confirmed_order_list' => 1,
                'ac_products_view' => 1,
                'ac_products_create' => 1,
                'ac_products_update' => 1,
                'ac_products_disable' => 1,
                'ac_products_delete' => 1,
                'ac_settings_shop_branch_view' => 1,
                'ac_settings_shop_branch_update' => 1,
                'shop_account_view' => 1,
                'shop_account_update' => 1,
                'ac_customer_view' => 1,
                'ac_billing_view' => 1,
                'ac_aov_view' => 1,
                'ac_bpr_view' => 1,
                'ac_invlist_view' => 1,
                'ac_inv_view' => 1,
                'ac_oscrr_view' => 1,
                'ac_ps_view' => 1,
                'ac_invend_view' => 1,
                'ac_os_view' => 1,
                'ac_osr_view' => 1,
                'ac_oblr_view' => 1,
                'ac_po_view' => 1,
                'ac_prr_view' => 1,
                'ac_or_view' => 1,
                'ac_rosum_view' => 1,
                'ac_rostat_view' => 1,
                'ac_rbbr_view' => 1,
                'ac_rbl_view' => 1,
                'ac_sr_view' => 1,
                'ac_tps_view' => 1,
                'ac_tacr_view' => 1,
                'ac_to_view' => 1,
                'ac_tsr_view' => 1,
                'ac_settings_announcement_view' => 1,
                'ac_settings_void_record_process' => 1,
                'ac_settings_void_record_list_view' => 1,
                'ac_prepayment_create' => 1,
                'ac_shipping_and_delivery_view' => 1,
                'ac_settings_shop_branch_view' => 1,
                'ac_settings_shop_branch_update' => 1,
                'ac_settings_shop_branch_create' => 1,
                'shop_account_view' => 1,
                'shop_account_update' => 1,
                // 'billing_portal_fee_view' => 1,
                'ac_prepayment_view' => 1,
                'ac_general_shipping_view' => 1,
                'ac_general_shipping_create' => 1,
                'ac_general_shipping_update' => 1,
                'ac_general_shipping_delete' => 1,
                'ac_general_shipping_disable' => 1,
                'ac_custom_shipping_view' => 1,
                'ac_custom_shipping_create' => 1,
                'ac_custom_shipping_update' => 1,
                'ac_custom_shipping_delete' => 1,
                'ac_custom_shipping_disable' => 1
            );
            $result = $this->postCURL($url, $data);

            $users_details = $this->Model_merchant_registration->getSysUsers($merchant_details['ci_email'])->row_array();
            $this->Model_merchant_registration->save_members($users_details['id'], $sys_shop, $merchant_details);
            $this->Model_merchant_registration->approveApplication($id);

            
    
            $response['success'] = true;
            //  $response['message'] = 'Merchant Application has been approved';
            $this->audittrail->logActivity('Merchant Registration', "Shop ".$merchant_details['shop_name']." has been successfully added as a merchant.", 'Merchant Registration - Approve', $this->session->userdata('username'));
        }
        else{
            $response['success'] = false;
            $response['message'] = 'Email already in use.';
        }
      
        echo json_encode($response);
    }

    public function declineApplication(){
        $id               = sanitize($this->input->post('id'));
        $reason           = sanitize($this->input->post('reason'));

     
        // $checkDataExist  = $this->Model_merchant_registration->checkDataExist($this->input->post('id'))->num_rows();

        // if($checkDataExist === 1){

        //     $response['success'] = false;
        //     $response['message'] = 'Required fields empty. Make sure to save your changes before declining.';

        // }else{
            
            $this->Model_merchant_registration->declineApplication($id, $reason);
            $merchant_details = $this->Model_merchant_registration->get_merchant_details($id)->row_array();
            $this->sendDeclinedApp($merchant_details);
            $response['success'] = true;
            // $response['message'] = 'Merchant Application has been successfully removed.';
            $this->audittrail->logActivity('Merchant Registration', "Application of ".$merchant_details['ci_registered_company_name']." has been successfully declined.", 'Merchant Registration - Decline', $this->session->userdata('username'));

        // }
       
    
        echo json_encode($response);
    }

    public function sendDeclinedApp($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['ci_email']);
        $this->email->subject(get_company_name()." | Merchant Application");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/decmerchant_application_template", $data, TRUE));
        $this->email->send();
    }

    public function sendApprovedApp($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['ci_email']);
        $this->email->subject(get_company_name()." | Merchant Application");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/appmerchant_application_template", $data, TRUE));
        $this->email->send();
    }

    public function generateUniqueShopCode(){
        $shopcode_checker = 0;
        for($x = 0; $x <= 9999; $x++){
            $shopcode = strtoupper(randomShopCode());
            $checkShopCodeExist = $this->Model_merchant_registration->checkShopCodeExist($shopcode)->num_rows();
            if($checkShopCodeExist == 0){
                break;
            }
        }

        return $shopcode;
    }

    public function delete_modal_confirm()
    {
        $this->isLoggedIn();

        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id === 0) {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        } else {
            $client_data = $this->Model_merchant_registration->get_merchant_details($delete_id)->row_array();
            $query = $this->Model_merchant_registration->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Merchant application deleted successfully!");
                $this->audittrail->logActivity('Merchant Registration', $client_data['ci_registered_company_name'].' has been successfully deleted.', "Delete", $this->session->userdata('username'));  
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }

        }

        generate_json($data);
    }

    public function postCURL($_url, $_param){

        $postvars = http_build_query($_param);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_POST, count($_param));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $response = json_decode($server_output);

        return $response;
    }

    public function get_province()
	{
        $regCode = sanitize($this->input->post('regCode'));

        $data = $this->Model_merchant_registration->get_province($regCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
	}

    public function get_citymun()
	{
        $provCode = sanitize($this->input->post('provCode'));

        $data = $this->Model_merchant_registration->get_citymun($provCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
	}

    public function updateApplication(){

        $merchantData    = $this->input->post();
        $m_email         = sanitize($this->input->post('up_email'));
        $checkEmailExist = $this->Model_merchant_registration->checkEmailExist($m_email)->num_rows();
        $merchant_details = $this->Model_merchant_registration->get_merchant_details($this->input->post('up_app_id'))->row_array();
     
        if($checkEmailExist == 0){

            $f_id = $this->uuid->v4_formatted();
            $file_name = $f_id;

            $files = $_FILES;
            $files_banner = $_FILES;

        //    print_r($files);
        //     die();
        
            $islogochange = 0;
            $isbannerchange = 0;


            if(!empty($_FILES)){
                $_FILES['userfile'] = [
                    'name'     => $files['file_container']['name'],
                    'type'     => $files['file_container']['type'],
                    'tmp_name' => $files['file_container']['tmp_name'],
                    'error'    => $files['file_container']['error'],
                    'size'     => $files['file_container']['size']
                ];
                $F[] = $_FILES['userfile'];
                
                $this->makedirImage();
                //Upload requirements
                $config = array();
                $directory = 'assets/img';
                $config['file_name'] = $file_name;
                $config['upload_path'] = $directory;
                $config['allowed_types'] = '*';
                $config['max_size'] = 3000;
                $this->load->library('upload',$config, 'logo');
                $this->logo->initialize($config);
    
                
                $data = array();
                if(!$this->logo->do_upload()){
                    $islogochange = 0;
                }else{
                    $islogochange = 1;
                    $file_name = $this->logo->data()['file_name'];
            
                    ///upload image to s3 bucket
                    $fileTempName    = $files['file_container']['tmp_name'];
                    $activityContent = 'assets/img/shops/'.$file_name;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory.'/'.$file_name);

                    $getOrigimageDim = getimagesize($files['file_container']['tmp_name']);
                    $fileTempName    = $files['file_container']['tmp_name'];
                    $s3_directory    = 'assets/img/shops/';
                    $activityContent = 'assets/img/shops/'.$file_name;
                    $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, 'shops-60', $getOrigimageDim);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }
                }
            } 

            if(!empty($_FILES)){
                $_FILES['userfile'] = [
                    'name'     => $files_banner['file_container_banner']['name'],
                    'type'     => $files_banner['file_container_banner']['type'],
                    'tmp_name' => $files_banner['file_container_banner']['tmp_name'],
                    'error'    => $files_banner['file_container_banner']['error'],
                    'size'     => $files_banner['file_container_banner']['size']
                ];
                $F[] = $_FILES['userfile'];
                
                // if(!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/'))) {
                //     mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/'), 0777, TRUE);
                // }

                $this->makedirImage();
                //Upload requirements
                $config_banner = array();
                $directory_banner = 'assets/img';
                $config_banner['file_name'] = $file_name;
                $config_banner['upload_path'] = $directory_banner;
                $config_banner['allowed_types'] = '*';
                $config_banner['max_size'] = 3000;
                $this->load->library('upload',$config_banner, 'banner');
                $this->banner->initialize($config_banner);
    
                $this->load->library('upload',$config_banner);
                $data = array();
                if($this->banner->do_upload()){
                    $isbannerchange = 1;
                    $file_name_banner = $this->banner->data()['file_name'];
                    // echo $file_name;
                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$file_name_banner, "shops-banner0110");
                    // $audit_string .= "Shop Banner Image Changed. \n";

                    ///upload image to s3 bucket
                    $fileTempName    = $files_banner['file_container_banner']['tmp_name'];
                    $activityContent = 'assets/img/shops-banner/'.$file_name_banner;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory_banner.'/'.$file_name_banner);

                    $getOrigimageDim = getimagesize($files_banner['file_container_banner']['tmp_name']);
                    $fileTempName    = $files_banner['file_container_banner']['tmp_name'];
                    $s3_directory    = 'assets/img/shops-banner/';
                    $activityContent = 'assets/img/shops-banner/'.$file_name_banner;
                    $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, 'shops-banner1500', $getOrigimageDim);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                }else{
                    $isbannerchange = 0;
                    $file_name_banner = "";
                   
                }
            }
         
            $this->Model_merchant_registration->updateApplication($islogochange,$isbannerchange,$file_name, $file_name_banner,$this->input->post());
            $response['success']  = 1;
            // $response['message']  = 'Application saved successfully.';
            $audit_string = $this->audittrail->MerchantAppString($this->input->post(), $merchant_details);
            $this->audittrail->logActivity('Merchant Registration', 'Applicant '.$this->input->post('up_registered_company_name').' details successfully updated '.$audit_string, 'Update', $this->session->userdata('username'));
        }
        else{
            $response['success']  = 0;
            $response['message']  = 'Email already in use.';
        }

        echo json_encode($response);

    }


    public function makedirImage(){
        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/'), 0777, TRUE);
        }

        
    }
}
