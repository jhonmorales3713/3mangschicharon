<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Main_shops_approval extends CI_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('adhoc_resize/Model_adhoc_resize');
		$this->load->model('shops/Model_shops');
        $this->load->model('shops/Model_shops_approval');
		$this->load->model('products/model_products');
		$this->load->model('libs/Model_generatedfilename');
        $this->load->model('shop_branch/Model_shopbranch');
        $this->load->model('setting/Model_settings_city');
        $this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
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


    public function shops_changes_mcr_approval($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['shop_mcr']['view'] == 1) {
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
                'validation'           => $this->loginstate->get_access()['shop_mcr'],
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('shops/shops_changes_mcr_approval', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }


    public function shop_changes_approval_tables()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $data_admin = array(
            '_record_status'           => sanitize($this->input->post('_record_status')),
            '_shops'        =>  sanitize($this->input->post('_shops')),
        );

        $query = $this->Model_shops_approval->shop_changes_approval_table($data_admin);

        $x = 0;
        foreach($query as $k => $v){
            $query[$x]['shop_enc_id'] = en_dec('en', $v['shopid']);
            $x++;
        }

        $response = [
            'success' => true,
            'productArr' => $query,
        ];

        echo json_encode($response);

    }



    public function shop_mcr_approve()
    {

        $shop_id    = sanitize($this->input->post('id'));
        $this->Model_shops_approval->shop_mcr_approve($shop_id);

        $Get_email_settings = $this->model_products->get_email_settings();
        $Get_shop_details = $this->model_products->getSysShopsDetails($shop_id);
        $data_email = array(
            'shopname'               => $Get_shop_details[0]['shopname'],
            'verify_email'         => $Get_email_settings[0]['shop_mcr_verify_email'],
            'verify_name'          => $Get_email_settings[0]['shop_mcr_verify_name'],
        );
        $this->sendShopMCRverifyEmail($data_email);

        $shopDetails = $this->Model_csr_orders->getShopDetails($shop_id);
        $this->audittrail->logActivity('MCR Approval', "Shop ".$shopDetails['shopname']." has been successfully approved.", 'Approved', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }


    public function shop_mcr_approve_all()
    {

        $ApproveAll    = $this->input->post('shop_id');

        // $reason =  $this->input->post('textarea');
        foreach($ApproveAll as $k => $v){
            $this->Model_shops_approval->shop_mcr_approve($v['shop_id']);

            $Get_email_settings = $this->model_products->get_email_settings();
            $Get_shop_details = $this->model_products->getSysShopsDetails($v['shop_id']);
            $data_email = array(
                'shopname'              => $Get_shop_details[0]['shopname'],
                'verify_email'         => $Get_email_settings[0]['shop_mcr_verify_email'],
                'verify_name'          => $Get_email_settings[0]['shop_mcr_verify_name'],
            );
            $this->sendShopMCRverifyEmail($data_email);

            $shopDetails = $this->Model_csr_orders->getShopDetails($v['shop_id']);
            $this->audittrail->logActivity('MCR Approval', "Shop ".$shopDetails['shopname']." has been successfully approved.", 'Approved', $this->session->userdata('username'));

        }
        $response['success'] = true;
        echo json_encode($response);


    }


    public function shop_mcr_verify_all()
    {

        $ApproveAll    = $this->input->post('shop_id');

        // $reason =  $this->input->post('textarea');
        foreach($ApproveAll as $k => $v){
            $this->Model_shops_approval->shop_mcr_verify($v['shop_id']);
            $Get_email_settings = $this->model_products->get_email_settings();
            $Get_shop_details = $this->model_products->getSysShopsDetails($v['shop_id']);
            $data_email = array(
                'shopname'               => $Get_shop_details[0]['shopname'],
                'verified_email'         => $Get_email_settings[0]['shop_mcr_verify_email'],
                'verified_name'          => $Get_email_settings[0]['shop_mcr_verify_name'],
            );
            $this->sendShopMCRverifiedEmail($data_email);

            $shopDetails = $this->Model_csr_orders->getShopDetails($v['shop_id']);
            $this->audittrail->logActivity('MCR Approval', "Shop ".$shopDetails['shopname']." has been successfully verified.", 'Verified', $this->session->userdata('username'));
        }
        $response['success'] = true;
        echo json_encode($response);


    }


    public function shop_mcr_verify()
    {
        $shop_id    = sanitize($this->input->post('id'));
        $this->Model_shops_approval->shop_mcr_verify($shop_id);

        $Get_email_settings = $this->model_products->get_email_settings();
        $Get_shop_details = $this->model_products->getSysShopsDetails($shop_id);
        $data_email = array(
            'shopname'               => $Get_shop_details[0]['shopname'],
            'verified_email'         => $Get_email_settings[0]['shop_mcr_verify_email'],
            'verified_name'          => $Get_email_settings[0]['shop_mcr_verify_name'],
        );
        $this->sendShopMCRverifiedEmail($data_email);

        $shopDetails = $this->Model_csr_orders->getShopDetails($shop_id);
        $this->audittrail->logActivity('MCR Approval', "Shop ".$shopDetails['shopname']." has been successfully verified.", 'Verified', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }


    public function shops_mcr_decline()
    {
        $shop_id = sanitize($this->input->post('id'));
        $this->Model_shops_approval->shops_mcr_decline($shop_id);

        $shopDetails = $this->Model_csr_orders->getShopDetails($shop_id);
        $this->audittrail->logActivity('MCR Approval', "Shop ".$shopDetails['shopname']." has been successfully declined.", 'Declined', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }

    public function shops_mcr_decline_all()
    {

        $ApproveAll    = $this->input->post('shop_id');

        // $reason =  $this->input->post('textarea');
        foreach($ApproveAll as $k => $v){
            $this->Model_shops_approval->shops_mcr_decline($v['shop_id']);

            $shopDetails = $this->Model_csr_orders->getShopDetails($v['shop_id']);
            $this->audittrail->logActivity('MCR Approval', "Shop ".$shopDetails['shopname']." has been successfully declined.", 'Declined', $this->session->userdata('username'));
        }
       
        $response['success'] = true;
        echo json_encode($response);
    }


    public function update_mcr_approval($id, $token = ''){
        $this->isLoggedIn();
 
        //start - for restriction of views
        if ($this->loginstate->get_access()['shops']['view'] == 1 || $this->loginstate->get_access()['shops']['update'] == 1 || $this->loginstate->get_access()['shop_account']['view'] == 1 || $this->loginstate->get_access()['shop_account']['update'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Shops/changes_approval/'.$token).'">Back</button>';
            $region = $this->Model_shopbranch->get_all_region()->result();
            

            $data_admin = array(
                'idno' => $id,//empty since its a new record not update
                'token' => $token,
                'back_button' => $back_button,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation 
                'sys_shop_details' => (ini() == "toktokmall")? $this->Model_shops->get_shop_details_toktokmall(en_dec('dec', $id))->row(): $this->Model_shops->get_shop_details(en_dec('dec', $id))->row(),
                // 'sys_shop_details' =>  $details,
                'get_currency' => $this->Model_shops->get_currency()->result_array(),
                'core_js' => 'assets/js/shops/shop_edit_approval.js',
                'region' => $region,
                'breadcrumbs_active' => 'Update Shop Details',
                'get_featured_merchant' => $this->Model_shops->getFeaturedMerchant(),
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('shops/shop_mcr_approval', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }


    public function view_shop($id, $token = ''){
        $this->isLoggedIn();
         
  

        //start - for restriction of views
        if ($this->loginstate->get_access()['shops']['view'] == 1 || $this->loginstate->get_access()['shops']['update'] == 1 || $this->loginstate->get_access()['shop_account']['view'] == 1 || $this->loginstate->get_access()['shop_account']['update'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Shops/profile/'.$token).'">Back</button>';
            $region = $this->Model_shopbranch->get_all_region()->result();
            // $sys_shops_updated = $this->model_shops_approval->getShopChangesApproval(en_dec('dec', $id));
            // $sys_shops_status= $this->model_shops_approval->getShopChangeStatus(en_dec('dec', $id));
            $data_admin = array(
                'idno' => $id,//empty since its a new record not update
                'token' => $token,
                'back_button' => $back_button,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation 
                'sys_shop_details' => (ini() == "toktokmall")? $this->Model_shops->get_shop_details_toktokmall(en_dec('dec', $id))->row(): $this->Model_shops->get_shop_details(en_dec('dec', $id))->row(),
                // 'sys_shop_details' =>  $details,
                'get_currency' => $this->Model_shops->get_currency()->result_array(),
                'core_js' => 'assets/js/shops/view_shop.js',
                'region' => $region,
                'breadcrumbs_active' => 'View Shop',
                'shop_mcr_staus' => $this->Model_shops_approval->sys_shops_mcr_approval(en_dec('dec', $id)),
                'validation'     => $this->loginstate->get_access()['shop_mcr'],
            );

            // print_r($sys_shops_updated[0]['shopname_tobeapply']);
            // die();
            // end - data to be used for views
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('shops/View_Shop', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }


    public function sendShopMCRverifyEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['verify_email']);
        $this->email->subject(get_company_name()." | Shop MCR Verify Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/shop_mcr_verify_template", $data, TRUE));
        $this->email->send();
    }


    public function sendShopMCRverifiedEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['verified_email']);
        $this->email->subject(get_company_name()." | Shop MCR Verified Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/shop_mcr_verified_template", $data, TRUE));
        $this->email->send();
    }


 
}