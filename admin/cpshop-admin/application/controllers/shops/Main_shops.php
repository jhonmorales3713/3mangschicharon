<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Main_shops extends CI_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('adhoc_resize/Model_adhoc_resize');
		$this->load->model('shops/Model_shops');
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


    public function delete_modal_confirm(){
        $this->isLoggedIn();
        
        $delete_id                 = sanitize($this->input->post('delete_id'));
        $check_pendingorders       = $this->Model_shops->check_pendingorder($delete_id)->num_rows();
        $check_pendingorder_unpaid = $this->Model_shops->check_pendingorder_unpaid($delete_id)->num_rows();
    
        if($check_pendingorders > 0){
            $data = array("success" => 0, 'message' => "Cannot delete shop due to pending orders.");   
        }
        else if($check_pendingorder_unpaid > 0){
            $data = array("success" => 0, 'message' => "Cannot delete shop due to pending orders.");   
        }
        else if ($delete_id > 0) {
            $shopname = $this->model_shops->get_shop_details($delete_id)->row_array()['shopname'];
            $query = $this->Model_shops->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
                $this->audittrail->logActivity('Shop Profile', $shopname.' shop successfully deleted.', "delete", $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                $this->audittrail->logActivity('Shop Profile', $shopname.' shop failed to delete.', "delete", $this->session->userdata('username'));
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function disable_modal_confirm(){
        $this->isLoggedIn();
        
        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
        }

        $check_pendingorders       = $this->Model_shops->check_pendingorder($disable_id)->num_rows();
        $check_pendingorder_unpaid = $this->Model_shops->check_pendingorder_unpaid($disable_id)->num_rows();

        if($check_pendingorders > 0 && $record_status == 2){
            $data = array("success" => 0, 'message' => "Cannot disable shop due to pending orders.");   
        }
        else if($check_pendingorder_unpaid > 0 && $record_status == 2){
            $data = array("success" => 0, 'message' => "Cannot disable shop due to pending orders.");   
        }
        else
        
         if ($disable_id > 0 && $record_status > 0) {
            $query = $this->Model_shops->disable_modal_confirm($disable_id, $record_status);
            $shopname = $this->model_shops->get_shop_details($disable_id)->row_array()['shopname'];

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
                $this->audittrail->logActivity('Shop Profile', $shopname.' shop has been successfully '.$record_text, $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                $this->audittrail->logActivity('Shop Profile',' Failed to '.$record_text.' '.$shopname, $record_text, $this->session->userdata('username'));

            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function shop_profile($token = ''){
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['shops']['create'] == 1){
            $city = $this->Model_shopbranch->get_all_city()->result();
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'city' => $city
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
    		$this->load->view('shops/shops_profile', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function shops_profile_table(){
        $this->isLoggedIn();
        $filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_shopname'	    => $this->input->post('_shopname'),
            '_address'	    => $this->input->post('_address'),
            '_city'	    	=> $this->input->post('_city'),
        ];
        $query = $this->Model_shops->shops_profile_table($filters, $_REQUEST);
        generate_json($query);
    }
    // Under Navigation of Settings
    
    public function export_shops_profile_table(){
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
        $query = $this->Model_shops->shops_profile_table($filters, $requestData, true);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $shopid = $filters['_shopname'];
        $fil_arr = [
            'Address' => $filters['_address'],
            'City' => $this->Model_settings_city->get_CityNameByCode($filters['_city'])[0]['name'],
            'Record Status' => array(
                '' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
            )[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, "", $fromdate, $fromdate, "Shops Profile", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Shops Profile', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Shops Profile");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);
        // $sheet->getColumnDimension('F')->setWidth(15);

        $sheet->setCellValue("A6", 'Shop Name');
        $sheet->setCellValue('B6', 'E-mail Address');
        $sheet->setCellValue('C6', 'Contact Number');
        $sheet->setCellValue('D6', 'Address');
        $sheet->setCellValue('E6', 'City');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Shops Profile ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
    }

	public function add_new($token = ''){
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['shops']['create'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Shops/profile/'.$token).'">Back</button>';
            $region = $this->Model_shopbranch->get_all_region()->result();
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
            	'idno' => '',//empty since its a new record not update
                'token' => $token,
                'back_button' => $back_button,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'get_currency' => $this->Model_shops->get_currency()->result_array(),
                'core_js' => 'assets/js/shops/shop_add.js',
                'region' => $region,
                'breadcrumbs_active' => 'Add Shop',
                'get_featured_merchant' => $this->Model_shops->getFeaturedMerchant(),
                'get_whatsnew_merchant' => $this->Model_shops->getWhatsNewMerchant(),
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
    		$this->load->view('shops/shop_add', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

	public function save_shop(){

        switch (ini()) {
            case "toktokmall":

                $f_id = $this->uuid->v4_formatted();
                $file_name = $f_id;
                //Form entry
        
                $id = sanitize($this->input->post('entry-id'));
                $shopurl = sanitize($this->input->post('entry-shopurl'));
                $shopcode = sanitize($this->input->post('entry-shopcode'));
                $shopname = sanitize($this->input->post('entry-shopname'));
                $mobile = sanitize($this->input->post('entry-mobile'));
                $email = sanitize($this->input->post('entry-email'));
                $merchant_comrate = sanitize($this->input->post('entry-merchant-comrate')) / 100;
                $f_startup = sanitize($this->input->post('entry-f_startup')) / 100;
                $f_jc = sanitize($this->input->post('entry-f_jc')) / 100;
                $f_mcjr = sanitize($this->input->post('entry-f_mcjr')) / 100;
                $f_mc = sanitize($this->input->post('entry-f_mc')) / 100;
                $f_mcsuper = sanitize($this->input->post('entry-f_mcsuper')) / 100;
                $f_mcmega = sanitize($this->input->post('entry-f_mcmega')) / 100;
                $f_others = sanitize($this->input->post('entry-f_others')) / 100;
                // $commrate = sanitize($this->input->post('entry-commrate'));
                // $ratetype = sanitize($this->input->post('entry-ratetype'));
                // $rate = sanitize($this->input->post('entry-rate'));
                $bankname = sanitize($this->input->post('entry-bankname'));
                $acctname = sanitize($this->input->post('entry-acctname'));
                $acctno = sanitize($this->input->post('entry-acctno'));
                $desc = sanitize($this->input->post('entry-desc'));
                $address = sanitize($this->input->post('entry-address'));
                $shop_city = sanitize($this->input->post('entry-shop_city'));
                $shop_region = sanitize($this->input->post('entry-shop_region'));
                $withshipping = sanitize($this->input->post('entry-withshipping'));
                $loc_latitude = sanitize($this->input->post('loc_latitude'));
                $loc_longitude = sanitize($this->input->post('loc_longitude'));
                $generatebilling = sanitize($this->input->post('entry-generatebilling'));
                $prepayment = sanitize($this->input->post('entry-prepayment'));
                $toktokdel = sanitize($this->input->post('entry-toktokdel'));
                $thresholdamt = sanitize($this->input->post('entry-thresholdamt'));
                $currency = sanitize($this->input->post('entry-currency'));
                $treshold = sanitize($this->input->post('entry-treshold'));
                $advertisement = sanitize($this->input->post('set_advertisement'));
                $merchant_arrangement = sanitize($this->input->post('entry-feat-merchant-arrangement'));
                $whatsnew = sanitize($this->input->post('set_whatsnew_merchant'));
                $whatsnew_arrangement = sanitize($this->input->post('entry-feat-whatsnew-merchant-arrangement'));
                $allowed_unfulfilled = sanitize($this->input->post('entry-allowed-unfulfilled'));
                $set_allowpickup = sanitize($this->input->post('set_allowpickup'));
                $shop_city = ($shop_city != "") ? $shop_city : "Empty";
              
              //  die($advertisement);
        
                //File inputs
                $files = $_FILES;
                $files_banner = $_FILES;
                $files_advertisement = $_FILES;
                $files_whatsnew = $_FILES;
        
           
                if(!empty($shopcode) AND !empty($shopname) AND !empty($shopurl) AND  !empty($email) AND    !empty($bankname) AND  !empty($acctname) AND  !empty($acctno) AND  !empty($desc) AND !empty($files) AND !empty($address) AND !empty($shop_region) AND !empty($loc_latitude) AND !empty($loc_longitude)) {
                    if(strlen($mobile) == 0){
                        $data = array(
                                    'success' => 0,
                                    // 'message' => 'Mobile number must be a 11 digit'
                                    'message' => 'You must enter a contact number'
                                );
                    }else{
                        $validate_shopcodeurl = $this->validate_shopcodeurl($shopcode, $shopurl);
                        if($validate_shopcodeurl['error'] == 1){
                            $data = array(
                                    'success' => 0,
                                    'message' => $validate_shopcodeurl['message']
                                );
                        }else{
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
        
                                }else{
                                    $file_name = $this->logo->data()['file_name'];
                                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$file_name, "shops0110");
                                    
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
                                            $file_banner = $this->banner->data()['file_name'];
                                            $fileTempName    = $files_banner['file_container_banner']['tmp_name'];
                                            $activityContent = 'assets/img/shops-banner/'.$file_banner;
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
        
                                            unlink($directory_banner.'/'.$file_banner);
        
                                            $getOrigimageDim = getimagesize($files_banner['file_container_banner']['tmp_name']);
                                            $fileTempName    = $files_banner['file_container_banner']['tmp_name'];
                                            $s3_directory    = 'assets/img/shops-banner/';
                                            $activityContent = 'assets/img/shops-banner/'.$file_banner;
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
                                        
                                            $file_banner = "";
                                        }
                                    }
        
                                if(!empty($_FILES)){
                                    $_FILES['userfile'] = [
                                        'name'     => $files_advertisement['file_container_advertisement']['name'],
                                        'type'     => $files_advertisement['file_container_advertisement']['type'],
                                        'tmp_name' => $files_advertisement['file_container_advertisement']['tmp_name'],
                                        'error'    => $files_advertisement['file_container_advertisement']['error'],
                                        'size'     => $files_advertisement['file_container_advertisement']['size']
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
                                        $file_advertisement = $this->banner->data()['file_name'];
                                        ///upload image to s3 bucket
                                        $fileTempName    = $files_advertisement['file_container_advertisement']['tmp_name'];
                                        $activityContent = 'assets/img/shops/ads/'.$file_advertisement;
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
        
                                        unlink($directory_banner.'/'.$file_advertisement);
                                        /*
        
                                        $getOrigimageDim = getimagesize($files_shopbanner['file_container_shopbanner']['tmp_name']);
                                        $fileTempName    = $files_shopbanner['file_container_shopbanner']['tmp_name'];
                                        $s3_directory    = 'assets/img/shops-banner/';
                                        $activityContent = 'assets/img/shops-banner/'.$file_shopbanner;
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
                                            */
                                    }else{
                                        $file_advertisement = "";
                                    }
                                }

                                if(!empty($_FILES)){
                                    $_FILES['userfile'] = [
                                        'name'     => $files_whatsnew['file_container_whatsnew']['name'],
                                        'type'     => $files_whatsnew['file_container_whatsnew']['type'],
                                        'tmp_name' => $files_whatsnew['file_container_whatsnew']['tmp_name'],
                                        'error'    => $files_whatsnew['file_container_whatsnew']['error'],
                                        'size'     => $files_whatsnew['file_container_whatsnew']['size']
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
                                        $file_whatsnew = $this->banner->data()['file_name'];
                                        ///upload image to s3 bucket
                                        $fileTempName    = $files_whatsnew['file_container_whatsnew']['tmp_name'];
                                        $activityContent = 'assets/img/shops/whatsnew/'.$file_whatsnew;
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
        
                                        unlink($directory_banner.'/'.$file_whatsnew);
                        
                                    }else{
                                        $file_whatsnew = "";
                                    }
                                }

                                // print_r($file_whatsnew);
                                // die();

                                // Database Insertion
                                $shopid = $this->Model_shops->save_shop_det_toktokmall($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_banner, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled, $advertisement, $file_advertisement, $merchant_arrangement, $whatsnew, $whatsnew_arrangement, $file_whatsnew, $set_allowpickup, $toktokdel);
                                $this->Model_shops->save_shop_refferal_com_rate($shopid,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others);
                                $this->Model_shops->save_shop_rate_det_toktokmall($ratetype = "p", $merchant_comrate, $shopid);
                                $this->Model_shops->save_shop_bank_det($bankname, $acctname, $acctno, $desc, $shopid, 0);
                                $data = array(
                                            'success' => 1,
                                            'message' => 'Shop saved successfully'
                                        );
                                $this->audittrail->logActivity('Shop Profile', $shopname.' shop has been successfully added.', "add", $this->session->userdata('username'));
                            }
                        }
                   }else{
             
                    $data = array(
                        
                                    'success' => 0,
                                    'message' => 'Please complete all required fields'
                                );
                }
                generate_json($data);
              
              break;
            default:
///////////////////////// Default
            $f_id = $this->uuid->v4_formatted();
            $file_name = $f_id;
            //Form entry
    
            $id = sanitize($this->input->post('entry-id'));
            $shopurl = sanitize($this->input->post('entry-shopurl'));
            $shopcode = sanitize($this->input->post('entry-shopcode'));
            $shopname = sanitize($this->input->post('entry-shopname'));
            $mobile = sanitize($this->input->post('entry-mobile'));
            $email = sanitize($this->input->post('entry-email'));
            $ratetype = sanitize($this->input->post('entry-ratetype'));
            $rate = sanitize($this->input->post('entry-rate'));
            $commrate = sanitize($this->input->post('entry-commrate'));
            $bankname = sanitize($this->input->post('entry-bankname'));
            $acctname = sanitize($this->input->post('entry-acctname'));
            $acctno = sanitize($this->input->post('entry-acctno'));
            $desc = sanitize($this->input->post('entry-desc'));
            $address = sanitize($this->input->post('entry-address'));
            $shop_city = sanitize($this->input->post('entry-shop_city'));
            $shop_region = sanitize($this->input->post('entry-shop_region'));
            $withshipping = sanitize($this->input->post('entry-withshipping'));
            $loc_latitude = sanitize($this->input->post('loc_latitude'));
            $loc_longitude = sanitize($this->input->post('loc_longitude'));
            $generatebilling = sanitize($this->input->post('entry-generatebilling'));
            $prepayment = sanitize($this->input->post('entry-prepayment'));
            $toktokdel = sanitize($this->input->post('entry-toktokdel'));
            $thresholdamt = sanitize($this->input->post('entry-thresholdamt'));
            $currency = sanitize($this->input->post('entry-currency'));
            $treshold = sanitize($this->input->post('entry-treshold'));
            $advertisement = sanitize($this->input->post('set_advertisement'));
            $merchant_arrangement = sanitize($this->input->post('entry-feat-merchant-arrangement'));
            $allowed_unfulfilled = sanitize($this->input->post('entry-allowed-unfulfilled'));
            $set_allowpickup = sanitize($this->input->post('set_allowpickup'));
            $shop_city = ($shop_city != "") ? $shop_city : "Empty";
          
          //  die($advertisement);
    
            //File inputs
            $files = $_FILES;
            $files_banner = $_FILES;
            $files_advertisement = $_FILES;
    
       
            if(!empty($shopcode) AND !empty($shopname) AND !empty($shopurl) AND  !empty($email) AND  !empty($ratetype) AND  !empty($rate) AND  !empty($bankname) AND  !empty($acctname) AND  !empty($acctno) AND  !empty($desc) AND !empty($files) AND !empty($address) AND !empty($shop_region) AND !empty($loc_latitude) AND !empty($loc_longitude)) {
                if(strlen($mobile) == 0){
                    $data = array(
                                'success' => 0,
                                // 'message' => 'Mobile number must be a 11 digit'
                                'message' => 'You must enter a contact number'
                            );
                }else{
                    $validate_shopcodeurl = $this->validate_shopcodeurl($shopcode, $shopurl);
                    if($validate_shopcodeurl['error'] == 1){
                        $data = array(
                                'success' => 0,
                                'message' => $validate_shopcodeurl['message']
                            );
                    }else{
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
    
                            }else{
                                $file_name = $this->logo->data()['file_name'];
                                // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$file_name, "shops0110");
                                
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
                                        $file_banner = $this->banner->data()['file_name'];
                                        $fileTempName    = $files_banner['file_container_banner']['tmp_name'];
                                        $activityContent = 'assets/img/shops-banner/'.$file_banner;
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
    
                                        unlink($directory_banner.'/'.$file_banner);
    
                                        $getOrigimageDim = getimagesize($files_banner['file_container_banner']['tmp_name']);
                                        $fileTempName    = $files_banner['file_container_banner']['tmp_name'];
                                        $s3_directory    = 'assets/img/shops-banner/';
                                        $activityContent = 'assets/img/shops-banner/'.$file_banner;
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
                                    
                                        $file_banner = "";
                                    }
                                }
    
                            if(!empty($_FILES)){
                                $_FILES['userfile'] = [
                                    'name'     => $files_advertisement['file_container_advertisement']['name'],
                                    'type'     => $files_advertisement['file_container_advertisement']['type'],
                                    'tmp_name' => $files_advertisement['file_container_advertisement']['tmp_name'],
                                    'error'    => $files_advertisement['file_container_advertisement']['error'],
                                    'size'     => $files_advertisement['file_container_advertisement']['size']
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
                                    $file_advertisement = $this->banner->data()['file_name'];
                                    ///upload image to s3 bucket
                                    $fileTempName    = $files_advertisement['file_container_advertisement']['tmp_name'];
                                    $activityContent = 'assets/img/shops/ads/'.$file_advertisement;
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
    
                                    unlink($directory_banner.'/'.$file_advertisement);
                                    /*
    
                                    $getOrigimageDim = getimagesize($files_shopbanner['file_container_shopbanner']['tmp_name']);
                                    $fileTempName    = $files_shopbanner['file_container_shopbanner']['tmp_name'];
                                    $s3_directory    = 'assets/img/shops-banner/';
                                    $activityContent = 'assets/img/shops-banner/'.$file_shopbanner;
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
                                        */
                                }else{
                                    $file_advertisement = "";
                                }
                            }
                            // Database Insertion
                            $shopid = $this->Model_shops->save_shop_det($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_banner, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled, $advertisement, $file_advertisement, $merchant_arrangement, $set_allowpickup, $commrate, $toktokdel);
                            $this->Model_shops->save_shop_rate_det($ratetype, $rate, $shopid);
                            $this->Model_shops->save_shop_bank_det($bankname, $acctname, $acctno, $desc, $shopid, 0);
                            $data = array(
                                        'success' => 1,
                                        'message' => 'Shop saved successfully'
                                    );
                            $this->audittrail->logActivity('Shop Profile', $shopname.' shop has been successfully added.', "add", $this->session->userdata('username'));
                        }
                    }
               }else{
         
                $data = array(
                    
                                'success' => 0,
                                'message' => 'Please complete all required fields'
                            );
            }
            generate_json($data);
            break;

          }

      
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

    public function update_record($id, $token = ''){
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['shops']['view'] == 1 || $this->loginstate->get_access()['shops']['update'] == 1 || $this->loginstate->get_access()['shop_account']['view'] == 1 || $this->loginstate->get_access()['shop_account']['update'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Shops/profile/'.$token).'">Back</button>';
            $region = $this->Model_shopbranch->get_all_region()->result();
            //end - for restriction of views main_nav_id
            // $details =  '';  
            //     print_r($platform);
            // if( ini() == "toktokmall") { 
            //     $details = $this->Model_shops->get_shop_details_toktokmall(en_dec('dec', $id))->row();
            // }  else{
            //     $details =  $this->Model_shops->get_shop_details(en_dec('dec', $id))->row();
            // } 
            // start - data to be used for views
            
            $data_admin = array(
                'idno' => $id,//empty since its a new record not update
                'token' => $token,
                'back_button' => $back_button,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation 
                'sys_shop_details' => (ini() == "toktokmall")? $this->Model_shops->get_shop_details_toktokmall(en_dec('dec', $id))->row(): $this->Model_shops->get_shop_details(en_dec('dec', $id))->row(),
                // 'sys_shop_details' =>  $details,
                'get_currency' => $this->Model_shops->get_currency()->result_array(),
                'core_js' => 'assets/js/shops/shop_edit.js',
                'region' => $region,
                'breadcrumbs_active' => 'Update Shop Details',
                'get_featured_merchant' => $this->Model_shops->getFeaturedMerchant(),
                'get_whatsnew_merchant' => $this->Model_shops->getWhatsNewMerchant()
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('shops/shop_add', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function update_shop(){
        switch (ini()) {
            case "toktokmall":
                $f_id = $this->uuid->v4_formatted();
                $file_name = $f_id;
                //Form entry
                $shopid = sanitize($this->input->post('entry-id'));
                $shopcode = sanitize($this->input->post('entry-shopcode'));
                $shopurl = sanitize($this->input->post('entry-shopurl'));
                $shopname = sanitize($this->input->post('entry-shopname'));
                $mobile = sanitize($this->input->post('entry-mobile'));
                $email = sanitize($this->input->post('entry-email'));
                $merchant_comrate = sanitize($this->input->post('entry-merchant-comrate')) / 100;
                $f_startup = sanitize($this->input->post('entry-f_startup')) / 100;
                $f_jc = sanitize($this->input->post('entry-f_jc')) / 100;
                $f_mcjr = sanitize($this->input->post('entry-f_mcjr')) / 100;
                $f_mc = sanitize($this->input->post('entry-f_mc')) / 100;
                $f_mcsuper = sanitize($this->input->post('entry-f_mcsuper')) / 100;
                $f_mcmega = sanitize($this->input->post('entry-f_mcmega')) / 100;
                $f_others = sanitize($this->input->post('entry-f_others')) / 100;
                // $ratetype = sanitize($this->input->post('entry-ratetype'));
                // $rate = sanitize($this->input->post('entry-rate'));
                // $commrate = sanitize($this->input->post('entry-commrate'));
                $bankname = sanitize($this->input->post('entry-bankname'));
                $acctname = sanitize($this->input->post('entry-acctname'));
                $acctno = sanitize($this->input->post('entry-acctno'));
                $desc = sanitize($this->input->post('entry-desc'));
                $old_logo = sanitize($this->input->post('entry-old_logo'));
                $old_banner = sanitize($this->input->post('entry-old_banner'));
                $old_advertisement = sanitize($this->input->post('entry-old_advertisement'));
                $old_whatsnew = sanitize($this->input->post('entry-old_whatsnew'));
                $address = sanitize($this->input->post('entry-address'));
                $shop_city = sanitize($this->input->post('entry-shop_city'));
                $shop_region = sanitize($this->input->post('entry-shop_region'));
                $withshipping = sanitize($this->input->post('entry-withshipping'));
                $loc_latitude = sanitize($this->input->post('loc_latitude'));
                $loc_longitude = sanitize($this->input->post('loc_longitude'));
                $generatebilling = sanitize($this->input->post('entry-generatebilling'));
                $prepayment = sanitize($this->input->post('entry-prepayment'));
                $toktokdel = sanitize($this->input->post('entry-toktokdel'));
                $thresholdamt = sanitize($this->input->post('entry-thresholdamt'));
                $currency = sanitize($this->input->post('entry-currency'));
                $treshold = sanitize($this->input->post('entry-treshold'));
                $advertisement = sanitize($this->input->post('set_advertisement'));
                $merchant_arrangement = sanitize($this->input->post('entry-feat-merchant-arrangement'));
                $whatsnew = sanitize($this->input->post('set_whatsnew_merchant'));
                $whatsnew_arrangement = sanitize($this->input->post('entry-feat-whatsnew-merchant-arrangement'));
                $allowed_unfulfilled = sanitize($this->input->post('entry-allowed-unfulfilled'));
                $set_allowpickup = sanitize($this->input->post('set_allowpickup'));
                $shop_city = ($shop_city != "") ? $shop_city : "Empty";

                $audit_string = "";
        
                //File inputs
                $files = $_FILES;
                $files_banner = $_FILES;
                $files_advertisement = $_FILES;
                $files_whatsnew = $_FILES;
                $islogochange = 0;
                $isbannerchange = 0;
                $isadvertisementchange = 0;
                $isWhatsnewchange = 0;
        
        
            
                    if (!empty($shopid) AND !empty($shopcode) AND !empty($shopurl) AND !empty($shopname) AND  !empty($email)  AND  !empty($bankname) AND  !empty($acctname) AND  !empty($acctno) AND  !empty($desc) AND !empty($address) AND !empty($shop_region) AND !empty($loc_latitude) AND !empty($loc_longitude)) {
                    if(strlen($mobile) == 0){
                        $data = array(
                                    'success' => 0,
                                    // 'message' => 'Mobile number must be a 11 digit'
                                    'message' => 'You must enter a contact number'
                                );
                    }else{
                        $validate_shopcodeurl = $this->validate_shopcodeurl_edit($shopcode, $shopurl, $shopid);
                        if($validate_shopcodeurl['error'] == 1){
                            $data = array(
                                    'success' => 0,
                                    'message' => $validate_shopcodeurl['message']
                                );
                        }else{
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
                                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$file_name, "shops0110");
                                    $audit_string .= "Logo Image Changed. \n";
        
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
                                    $audit_string .= "Shop Banner Image Changed. \n";
        
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
        
        
                            if(!empty($_FILES)){
                                $_FILES['userfile'] = [
                                    'name'     => $files_advertisement['file_container_advertisement']['name'],
                                    'type'     => $files_advertisement['file_container_advertisement']['type'],
                                    'tmp_name' => $files_advertisement['file_container_advertisement']['tmp_name'],
                                    'error'    => $files_advertisement['file_container_advertisement']['error'],
                                    'size'     => $files_advertisement['file_container_advertisement']['size']
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
                                    $isadvertisementchange = 1;
                                    $file_advertisement = $this->banner->data()['file_name'];
                                    // echo $file_name;
                                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$file_name_banner, "shops-banner0110");
                                    $audit_string .= "Advertisement Image Changed. \n";
        
                                    ///upload image to s3 bucket
                                    $fileTempName    = $files_advertisement['file_container_advertisement']['tmp_name'];
                                    $activityContent = 'assets/img/shops/ads/'.$file_advertisement;
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
        
                                    unlink($directory_banner.'/'.$file_advertisement);
                                    /*
        
                                    $getOrigimageDim = getimagesize($files_shopbanner['file_container_shopbanner']['tmp_name']);
                                    $fileTempName    = $files_shopbanner['file_container_shopbanner']['tmp_name'];
                                    $s3_directory    = 'assets/img/shops-banner/';
                                    $activityContent = 'assets/img/shops-banner/'.$file_shopbanner;
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
                                        */
                                }else{
                                    $isadvertisementchange = 0;
                                    $file_advertisement = "";
                                }
                            }


                            if(!empty($_FILES)){
                                $_FILES['userfile'] = [
                                    'name'     => $files_whatsnew['file_container_whatsnew']['name'],
                                    'type'     => $files_whatsnew['file_container_whatsnew']['type'],
                                    'tmp_name' => $files_whatsnew['file_container_whatsnew']['tmp_name'],
                                    'error'    => $files_whatsnew['file_container_whatsnew']['error'],
                                    'size'     => $files_whatsnew['file_container_whatsnew']['size']
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
                                    $isWhatsnewchange = 1;
                                    $file_whatsnew = $this->banner->data()['file_name'];
                                    // echo $file_name;
                                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$file_name_banner, "shops-banner0110");
                                    $audit_string .= "What`s New Image Changed. \n";
        
                                    ///upload image to s3 bucket
                                    $fileTempName    = $files_whatsnew['file_container_whatsnew']['tmp_name'];
                                    $activityContent = 'assets/img/shops/whatsnew/'.$file_whatsnew;
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
        
                                    unlink($directory_banner.'/'.$file_whatsnew);
                                    /*
        
                                    $getOrigimageDim = getimagesize($files_shopbanner['file_container_shopbanner']['tmp_name']);
                                    $fileTempName    = $files_shopbanner['file_container_shopbanner']['tmp_name'];
                                    $s3_directory    = 'assets/img/shops-banner/';
                                    $activityContent = 'assets/img/shops-banner/'.$file_shopbanner;
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
                                        */
                                }else{
                                    $isWhatsnewchange = 0;
                                    $file_whatsnew = "";
                                }
                            }
        
    
                            // rename shop's product images folder
                            // $shopcode_current = $this->model_products->get_shopcode_via_shopid($shopid);
                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode_current), $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode));
                            // Database Insertion
                            $prev_data = $this->model_shops->get_shop_details_toktokmall($shopid)->row_array();
                            $shop_rate = $this->Model_shops->get_shoprate($shopid);

                            
                            if(strval($merchant_comrate)  != strval($shop_rate[0]['merchant_comrate'])  || strval($f_startup) != strval($shop_rate[0]['startup'])  || strval($f_jc) != strval($shop_rate[0]['jc'])  || strval($f_mcjr) != strval($shop_rate[0]['mcjr'])  || strval($f_mc) != strval($shop_rate[0]['mc']) || strval($f_mcsuper) != strval($shop_rate[0]['mcsuper']) ||  strval($f_mcmega) != strval($shop_rate[0]['mcmega']) ||  strval($f_others) != strval($shop_rate[0]['others']) ){
                                $this->Model_shops->select_referralcomrate_toktokmall_approval($shopid,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others);
                                $Get_email_settings = $this->model_products->get_email_settings();
                                $Get_shop_details = $this->model_products->getSysShopsDetails($shopid);
                                $data_email = array(
                                    'shopname'               => $Get_shop_details[0]['shopname'],
                                    'approval_email'         => $Get_email_settings[0]['shop_mcr_approval_email'],
                                    'approval_name'          => $Get_email_settings[0]['shop_mcr_approval_name'],
                                );
                                $this->sendMcrApprovalEmail($data_email);
                            }else{
                                $this->Model_shops->select_referralcomrate_toktokmall($shopid,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others);
                                $this->Model_shops->select_sysrate_toktokmall($ratetype = "p", $merchant_comrate, $shopid);
                            }
                            $this->Model_shops->update_shop_det_toktokmall($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_name_banner, $shopid, $islogochange, $isbannerchange,$isadvertisementchange, $isWhatsnewchange, $old_logo, $old_banner, $old_advertisement, $old_whatsnew, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled, $advertisement, $file_advertisement, $merchant_arrangement, $whatsnew, $whatsnew_arrangement, $file_whatsnew, $set_allowpickup, $toktokdel);
                            $this->Model_shops->update_shop_bank_det($bankname, $acctname, $acctno, $desc, $shopid, 0);
                          
                            
                            $data = array(
                                        'success' => 1,
                                        'message' => 'Shop updated successfully'
                                    );
                            $audit_string .= $this->audittrail->shopsString($prev_data, $this->input->post());
                            $this->audittrail->logActivity('Shop Profile', $shopname." shop has been updated successfully. \nChanges: \n".$audit_string, 'update', $this->session->userdata('username'));
                        }
                    }
                }else{
                    $data = array(
                                    'success' => 0,
                                    'message' => 'Please complete all required fields'
                                );
                }
                generate_json($data);
                break;
            default;
                $f_id = $this->uuid->v4_formatted();
                $file_name = $f_id;
                //Form entry
                $shopid = sanitize($this->input->post('entry-id'));
                $shopcode = sanitize($this->input->post('entry-shopcode'));
                $shopurl = sanitize($this->input->post('entry-shopurl'));
                $shopname = sanitize($this->input->post('entry-shopname'));
                $mobile = sanitize($this->input->post('entry-mobile'));
                $email = sanitize($this->input->post('entry-email'));
                $ratetype = sanitize($this->input->post('entry-ratetype'));
                $rate = sanitize($this->input->post('entry-rate'));
                $commrate = sanitize($this->input->post('entry-commrate'));
                $bankname = sanitize($this->input->post('entry-bankname'));
                $acctname = sanitize($this->input->post('entry-acctname'));
                $acctno = sanitize($this->input->post('entry-acctno'));
                $desc = sanitize($this->input->post('entry-desc'));
                $old_logo = sanitize($this->input->post('entry-old_logo'));
                $old_banner = sanitize($this->input->post('entry-old_banner'));
                $old_advertisement = sanitize($this->input->post('entry-old_advertisement'));
                $address = sanitize($this->input->post('entry-address'));
                $shop_city = sanitize($this->input->post('entry-shop_city'));
                $shop_region = sanitize($this->input->post('entry-shop_region'));
                $withshipping = sanitize($this->input->post('entry-withshipping'));
                $loc_latitude = sanitize($this->input->post('loc_latitude'));
                $loc_longitude = sanitize($this->input->post('loc_longitude'));
                $generatebilling = sanitize($this->input->post('entry-generatebilling'));
                $prepayment = sanitize($this->input->post('entry-prepayment'));
                $toktokdel = sanitize($this->input->post('entry-toktokdel'));
                $thresholdamt = sanitize($this->input->post('entry-thresholdamt'));
                $currency = sanitize($this->input->post('entry-currency'));
                $treshold = sanitize($this->input->post('entry-treshold'));
                $advertisement = sanitize($this->input->post('set_advertisement'));
                $merchant_arrangement = sanitize($this->input->post('entry-feat-merchant-arrangement'));
                $allowed_unfulfilled = sanitize($this->input->post('entry-allowed-unfulfilled'));
                $set_allowpickup = sanitize($this->input->post('set_allowpickup'));
                $shop_city = ($shop_city != "") ? $shop_city : "Empty";
        
        
                $audit_string = "";
        
                //File inputs
                $files = $_FILES;
                $files_banner = $_FILES;
                $files_advertisement = $_FILES;
                $islogochange = 0;
                $isbannerchange = 0;
                $isadvertisementchange = 0;
        
            
                    if (!empty($shopid) AND !empty($shopcode) AND !empty($shopurl) AND !empty($shopname) AND  !empty($email) AND  !empty($ratetype) AND  !empty($rate) AND  !empty($bankname) AND  !empty($acctname) AND  !empty($acctno) AND  !empty($desc) AND !empty($address) AND !empty($shop_region) AND !empty($loc_latitude) AND !empty($loc_longitude)) {
                    if(strlen($mobile) == 0){
                        $data = array(
                                    'success' => 0,
                                    // 'message' => 'Mobile number must be a 11 digit'
                                    'message' => 'You must enter a contact number'
                                );
                    }else{
                        $validate_shopcodeurl = $this->validate_shopcodeurl_edit($shopcode, $shopurl, $shopid);
                        if($validate_shopcodeurl['error'] == 1){
                            $data = array(
                                    'success' => 0,
                                    'message' => $validate_shopcodeurl['message']
                                );
                        }else{
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
                                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$file_name, "shops0110");
                                    $audit_string .= "Logo Image Changed. \n";
        
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
                                    $audit_string .= "Shop Banner Image Changed. \n";
        
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
        
        
                            if(!empty($_FILES)){
                                $_FILES['userfile'] = [
                                    'name'     => $files_advertisement['file_container_advertisement']['name'],
                                    'type'     => $files_advertisement['file_container_advertisement']['type'],
                                    'tmp_name' => $files_advertisement['file_container_advertisement']['tmp_name'],
                                    'error'    => $files_advertisement['file_container_advertisement']['error'],
                                    'size'     => $files_advertisement['file_container_advertisement']['size']
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
                                    $isadvertisementchange = 1;
                                    $file_advertisement = $this->banner->data()['file_name'];
                                    // echo $file_name;
                                    // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$file_name_banner, "shops-banner0110");
                                    $audit_string .= "Advertisement Image Changed. \n";
        
                                    ///upload image to s3 bucket
                                    $fileTempName    = $files_advertisement['file_container_advertisement']['tmp_name'];
                                    $activityContent = 'assets/img/shops/ads/'.$file_advertisement;
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
        
                                    unlink($directory_banner.'/'.$file_advertisement);
                                    /*
        
                                    $getOrigimageDim = getimagesize($files_shopbanner['file_container_shopbanner']['tmp_name']);
                                    $fileTempName    = $files_shopbanner['file_container_shopbanner']['tmp_name'];
                                    $s3_directory    = 'assets/img/shops-banner/';
                                    $activityContent = 'assets/img/shops-banner/'.$file_shopbanner;
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
                                        */
                                }else{
                                    $isadvertisementchange = 0;
                                    $file_advertisement = "";
                                }
                            }
        
        
                    
                        // die($file_shopbanner);
            
                        
                            // rename shop's product images folder
                            // $shopcode_current = $this->model_products->get_shopcode_via_shopid($shopid);
                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode_current), $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode));
                            // Database Insertion
                            $prev_data = $this->model_shops->get_shop_details($shopid)->row_array();
                    
                            $this->Model_shops->update_shop_det($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_name_banner, $shopid, $islogochange, $isbannerchange,$isadvertisementchange, $old_logo, $old_banner, $old_advertisement, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled, $advertisement, $file_advertisement, $merchant_arrangement, $set_allowpickup, $commrate, $toktokdel);
                            $this->Model_shops->update_shop_rate_det($ratetype, $rate, $shopid);
                            $this->Model_shops->update_shop_bank_det($bankname, $acctname, $acctno, $desc, $shopid, 0);
                           
                            
                            $data = array(
                                        'success' => 1,
                                        'message' => 'Shop updated successfully'
                                    );
                            $audit_string .= $this->audittrail->shopsString($prev_data, $this->input->post());
                            $this->audittrail->logActivity('Shop Profile', $shopname." shop has been updated successfully. \nChanges: \n".$audit_string, 'update', $this->session->userdata('username'));
                        }
                    }
                }else{
                    $data = array(
                                    'success' => 0,
                                    'message' => 'Please complete all required fields'
                                );
                }
                generate_json($data);
            break;
        }
    }

    function validate_shopcodeurl($shopcode, $shopurl){
        $error = 0;
        $message = "";
        if(strlen($shopcode) > 4){
            $error = 1;
            $message = "Shop Code max of 4 characters only";
        }else if(strlen($shopurl) > 20){
            $error = 1;
            $message = "Shop Url max of 20 characters only";
        }else if($this->Model_shops->is_shopcodeexist($shopcode) > 0){
            $error = 1;
            $message = "Shop Code already exist!";
        }else if($this->Model_shops->is_shopurlexist($shopurl) > 0){
            $error = 1;
            $message = "Shop Url already exist!";
        }else{
            $error = 0;
            $message = "";  
        }
        $data = array(
                    'error' => $error,
                    'message' => $message
                );
        return $data;
    }

    function validate_shopcodeurl_edit($shopcode, $shopurl, $id){
        $error = 0;
        $message = "";
        if(strlen($shopcode) > 4){
            $error = 1;
            $message = "Shop Code max of 4 characters only";
        }else if(strlen($shopurl) > 20){
            $error = 1;
            $message = "Shop Url max of 20 characters only";
        }else if($this->Model_shops->is_shopcodeexist_edit($shopcode, $id) > 0){
            $error = 1;
            $message = "Shop Code already exist!";
        }else if($this->Model_shops->is_shopurlexist_edit($shopurl, $id) > 0){
            $error = 1;
            $message = "Shop Url already exist!";
        }else{
            $error = 0;
            $message = "";  
        }
        $data = array(
                    'error' => $error,
                    'message' => $message
                );
        return $data;
    }

    public function shop_account($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['shop_account']['view'] == 1 || $this->loginstate->get_access()['shop_account']['update'] == 1 AND $this->session->userdata('sys_shop') > 0){
            $id = en_dec('en', $this->session->userdata('sys_shop'));
            header("location:".base_url('Shops/update/'.$id.'/'.$token));
        }else{
            $this->load->view('error_404');
        }
    }


    public function get_feutured_merchant_count(){
    
        $data = $this->model_shops->getFeaturedMerchantCount();
        echo json_encode($data);
      }

      public function check_feutured_merchat_arrangement($merchant_number= ""){
    
        $data = $this->model_shops->checkFeaturedMerchantArrangement($merchant_number);
        echo json_encode($data);
      }

      public function check_feutured_merchants($shop_id= ""){
    
        $data = $this->model_shops->checkedFeaturedMerchant($shop_id);
        echo json_encode($data);
      }


      public function get_whatsnew_merchant_count(){
    
        $data = $this->model_shops->getWhatsNewMerchantCount();
        echo json_encode($data);
      }


      public function check_whatsnew_merchant_arrangement($merchant_number= ""){
    
        $data = $this->model_shops->checkWhatsNewMerchantArrangement($merchant_number);
        echo json_encode($data);
      }

      public function check_whatsnew_merchants($shop_id= ""){
    
        
        $data = $this->model_shops->checkedWhatsNewMerchant($shop_id);
        echo json_encode($data);
      }



      public function  approved_shops(){

        $shop_id = $this->input->post('shop_id');
   
        $this->Model_shops->approved_shops($shop_id);

        $Get_shop_details = $this->model_products->getSysShopsDetails($shop_id);
        $Get_appmember_details = $this->model_products->getAppmemberDetails($shop_id);
        $data_email = array(
            'email'         => $Get_shop_details[0]['email'],
            'fname'         => $Get_appmember_details[0]['fname'],
            'lname'         => $Get_appmember_details[0]['lname'],
        );
        
        $this->sendShopApprovedEmail($data_email);

        $response['success']  = 1;
        echo json_encode($response);

    }

    
    public function sendShopApprovedEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['email']);
        $this->email->subject(get_company_name()." | Merchant Access");
        $data['data']          = $data;
        $data['fname']         = $data['fname'];
        $data['lname']         = $data['lname'];

        $this->email->message($this->load->view("includes/emails/approvedshop_application_template", $data, TRUE));
        $this->email->send();
    }

    public function shop_popup_image($token = '')
    {
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['shop_popup']['view'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            
            // //end - for restriction of views main_nav_id

            // // start - data to be used for views
            $data_admin = array(
            	'idno' => '',//empty since its a new record not update
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'get_currency' => $this->Model_shops->get_currency()->result_array(),
                'core_js' => 'assets/js/shops/shop_popup.js',
                'breadcrumbs_active' => 'Pop Up Image',
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
    		$this->load->view('shop_popup/shop_popup_index', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }
    
    public function save_popup_image()
    {

        $this->load->model('dev_settings/Model_client_information', 'model_client_information');
        $update_promo = false;

        $popup_link = $this->input->post('popup_link') ?? '';
        $popup_enable = $this->input->post('popup_enable') ?? 0;
        $popup_enable = filter_var($popup_enable, FILTER_VALIDATE_BOOLEAN);

        $response = [];
    
        if (ini() == 'toktokmall') {
            $validation = $this->validate_popup($popup_link, $popup_enable);
            
            if ($validation != '') {
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => $validation
                ];
                echo json_encode($response);
                die();
            }
            
            $response = $this->save_toktokmall_popup_img();

            if (isset($response['success'])) {
                if ($response['success'] == true) {
                    // Update cs clients info
                    if(!empty($_FILES)) {
                        $update_promo = $this->model_client_information->update_popup_promo($response['filename'], $popup_link, $popup_enable, ini());
                    } else {
                        $update_promo = $this->model_client_information->update_popup_promo('', $popup_link, $popup_enable, ini());
                    }
                } else {
                    $update_promo = $this->model_client_information->update_popup_promo('', $popup_link, $popup_enable, ini());
                }

                if (filter_var($update_promo, FILTER_VALIDATE_BOOLEAN) == true) {
                    $this->audittrail->logActivity('Shop Popup', 'Shop Popup Image successfully updated!', "update", $this->session->userdata('username'));
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => true,
                        'message'     => 'Pop up image upload success!'
                    ];
                    echo json_encode($response);
                    die();
                } else {
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'Saving of pop up image in database failed!'
                    ];
                    echo json_encode($response);
                    die();
                }
            } else {
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => 'Pop up image upload failed.'
                ];
                echo json_encode($response);
                die();
            }
        }
    }

    private function validate_popup($popup_link, $popup_enable)
    {
        $message = '';
        if (c_popup_img() != '' && $popup_link == '') {
            $message = 'Pop up image link is required.';
        }

        if ($popup_enable && (c_popup_img() == '' && empty($_FILES))) {
            $message = 'Pop up image is required.';
        }

        return $message;
    }

    private function save_toktokmall_popup_img()
    {
        $files = $_FILES;
        $f_id = $this->uuid->v4_formatted();
        $file_name = $f_id;

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

            if(!$this->logo->do_upload()){
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => 'Pop up image upload failed.'
                ];
                return $response;
            }else{
                $file_name = $this->logo->data()['file_name'];
                
                ///upload image to s3 bucket
                $fileTempName    = $files['file_container']['tmp_name'];
                $activityContent = 'assets/img/promo_popup/'.$file_name;
                $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'Pop up image upload failed.'
                    ];
        
                    return $response;
                }

                unlink($directory.'/'.$file_name);
                // 380x450
                $getOrigimageDim = getimagesize($files['file_container']['tmp_name']);
                $fileTempName    = $files['file_container']['tmp_name'];
                $s3_directory    = 'assets/img/promo_popup/';
                $activityContent = 'assets/img/promo_popup/'.$file_name;
                $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '380', $getOrigimageDim, 450);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'Pop up image upload failed.'
                    ];
                    return $response;
                }
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => true,
                    'message'     => 'Pop up image upload success!',
                    'filename'    => $file_name
                ];
                return $response;
            }
        }
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => 'Pop up image upload failed.'
        ];
        return $response;
    }


    public function sendMcrApprovalEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['approval_email']);
        $this->email->subject(get_company_name()." |  Shop Mcr Approval Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/shop_mcr_approval_template", $data, TRUE));
        $this->email->send();
    }
    

}