<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Main_promotion extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('promotion/model_promotion');
        $this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
        $this->load->library('upload');
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
    }


    public function display404()
    {
        $this->load->view('error_404');
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

    public function logout()
    {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    public function index()
    {
        if ($this->session->userdata('isLoggedIn') == true) {
            $token_session = $this->session->userdata('token_session');
            $token = en_dec('en', $token_session);

            // $this->load->view(base_url('Main/home/'.$token));
            header("location:" . base_url('Main/home/' . $token));
        }

        $this->load->view('login');
    }

    public function product_promotion($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['product_promotion']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                'shopcode'            => $this->model_promotion->get_shopcode($member_id),
                'shops'               => $this->model_promotion->get_shop_options(),
                'prod_prom_access'    => $this->loginstate->get_access()['product_promotion'],
                'product_categories'  => $this->model_promotion->get_product_categories(),
                'featured_productsPiso'   => $this->model_promotion->getFeaturedProductPiso(),
                
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('promotion/product_promotion', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function product_table()
    {
        $this->isLoggedIn();
        $sys_shop = sanitize($this->input->post('select_shop'));
        $request = $_REQUEST;
        $query = $this->model_promotion->product_table($sys_shop, $request);
        
        generate_json($query);
    }

    public function update_promotion(){

        $product_id          = $this->input->post('product_id');
        $product_name        = $this->input->post('product_name');
        $product_promo_type  = $this->input->post('product_promo_type');
        $product_promo_rate  = $this->input->post('product_promo_rate');
        $product_promo_price = $this->input->post('product_promo_price');
        $product_promo_stock = $this->input->post('product_promo_stock');
        $product_purch_limit = $this->input->post('product_purch_limit');
        $product_status      = $this->input->post('product_status');
        $start_date          = date("Y-m-d ", strtotime($this->input->post('start_date')));
        $end_date            = date("Y-m-d ", strtotime($this->input->post('end_date')));
        $start_time          = sanitize($this->input->post('start_time'));
        $end_time            = sanitize($this->input->post('end_time'));
        $start_date          = $start_date.$start_time.":00";
        $end_date            = $end_date.$end_time.":00";
        $deletedProductArr   = explode(",",$this->input->post('deletedProductArr'));
        $product_count       = (!empty($product_id)) ? count($product_id) : 0;

        // print_r($product_purch_limit);
        // die();

        /// audit trail
        for($i = 0; $i < $product_count; $i++) { 
            if($product_status[$i] == 2 || $product_status[$i] == 1){
                $dataArr = array(
                    'product_id'          => $product_id[$i],
                    'product_name'        => $product_name[$i],
                    'product_promo_type'  => $product_promo_type[$i],
                    'product_promo_rate'  => $product_promo_rate[$i],
                    'product_promo_price' => $product_promo_price[$i],
                    'product_promo_stock' => $product_promo_stock[$i],
                    'product_purch_limit' => $product_purch_limit[$i],
                    'product_status'      => $product_status[$i],
                    'start_date'          => $start_date,
                    'end_date'            => $end_date
                );
                $productDetails = $this->model_promotion->checkProdPromoDetails($product_id[$i]);
                $prodPromString = $this->audittrail->prodPromString($productDetails, $dataArr);
                $this->audittrail->logActivity('Piso Deals', $prodPromString, 'update', $this->session->userdata('username'));
            }
        }
      
        // inactive product promotion
        for($i = 0; $i < $product_count; $i++) { 
            if($product_status[$i] == 2){
                $dataArr = array(
                    'product_id'          => $product_id[$i],
                    'product_name'        => $product_name[$i],
                    'product_promo_type'  => $product_promo_type[$i],
                    'product_promo_rate'  => $product_promo_rate[$i],
                    'product_promo_price' => $product_promo_price[$i],
                    'product_promo_stock' => $product_promo_stock[$i],
                    'product_purch_limit' => $product_purch_limit[$i],
                    'product_status'      => $product_status[$i],
                    'start_date'          => $start_date,
                    'end_date'            => $end_date
                );
                $this->model_promotion->updateInactiveProduct($dataArr);
                // $this->audittrail->logActivity('Product Promotion', $product_name[$i].' successfully updated. Inactive', 'update', $this->session->userdata('username'));
            }
        }

        // active product promotion
        for($i = 0; $i < $product_count; $i++) { 
            if($product_status[$i] == 1){
                $dataArr = array(
                    'product_id'          => $product_id[$i],
                    'product_name'        => $product_name[$i],
                    'product_promo_type'  => $product_promo_type[$i],
                    'product_promo_rate'  => $product_promo_rate[$i],
                    'product_promo_price' => $product_promo_price[$i],
                    'product_promo_stock' => $product_promo_stock[$i],
                    'product_purch_limit' => $product_purch_limit[$i],
                    'product_status'      => $product_status[$i],
                    'start_date'          => $start_date,
                    'end_date'            => $end_date
                );
                $this->model_promotion->updateActiveProduct($dataArr);
                // $this->audittrail->logActivity('Product Promotion', $product_name[$i].' successfully updated. Active', 'update', $this->session->userdata('username'));
            }
        }

         // deleted product promotion
         if(!empty($deletedProductArr)){
            foreach($deletedProductArr as $value){
                if($value != ''){
                    $deletedProd = $this->model_promotion->updateDeletedProduct($value);
                    if($deletedProd != ""){
                        $this->audittrail->logActivity('Piso Deals', $deletedProd.' successfully deleted', 'delete', $this->session->userdata('username'));
                    }
                }
            }
        }

        $response['success'] = true;
        $response['message'] = "You have succesfully set the promotion for the nominated items.";
        echo json_encode($response);
    }


    public function fetch_productPromo(){
        $sys_shop = $this->input->post('select_shop');
        
        $productArr = $this->model_promotion->fetch_productPromo($sys_shop)->result_array();
     
        $response = [
            'success' => true,
            'productArr' => $productArr
        ];

        echo json_encode($response);
    }

    //Campaign Type
    public function campaign_type($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['campaign_type']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            //$member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('promotion/campaign_type', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function campaign_type_list(){
        $this->isLoggedIn();
        $filters = [
            '_name' => $this->input->post('_name'),
        ];
        $query = $this->model_promotion->campaign_type_list($filters, $_REQUEST);
        generate_json($query);
    }

    public function campaign_type_add_modal_confirm(){
        $this->isLoggedIn();
        
        $name = sanitize($_POST['add_name']);
        $promo_img = sanitize($_POST['filename']);

        $get_campaign_type_name = $this->model_promotion->get_campaign_type_name($name)->num_rows();
        
        if($get_campaign_type_name > 0){
            $message = 'Campaign Name already exists.';
            $query = false;
        }else{
            $query = $this->model_promotion->campaign_type_add_modal_confirm($name,$promo_img);
        }

        $remarks = $name." successfully added to Campaign Type";
        
        if ($query) {
            $files = $_FILES;
            $file_name = '';

            if(!empty($_FILES)){

                $_FILES['userfile'] = [
                    'name'     => $files['image']['name'],
                    'type'     => $files['image']['type'],
                    'tmp_name' => $files['image']['tmp_name'],
                    'error'    => $files['image']['error'],
                    'size'     => $files['image']['size']
                ];
                $F[] = $_FILES['userfile'];
                
                //Upload requirements
                $file_name = $F[0]['name'];
                $config = array();
                $directory = 'assets/img';
                $config['file_name'] = $file_name;
                $config['upload_path'] = $directory;
                $config['allowed_types'] = '*';
                $config['max_size'] = 3000;
                //$this->load->library('upload',$config, 'logo');
                
                $this->upload->initialize($config);

                
                $data = array();
                if(!$this->upload->do_upload()){
                    
                }else{
                    $file_name = $this->upload->data()['file_name'];
                    ///upload image to s3 bucket
                    $fileTempName    = $F[0]['tmp_name'];
                    $activityContent = 'assets/img/promo_img/'.$file_name;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
                        generate_json($data);
                        die();
                    }

                    unlink($directory.'/'.$file_name);
                }
            }

             $data = array("success" => 1, 'message' => "Record added successfully!");
            $this->audittrail->logActivity('Campaign Type', $remarks, 'add', $this->session->userdata('username'));
        }else{
            $data = array("success" => 0, 'message' => $message);
        }
        
        generate_json($data);
    }

    public function get_campaign_type_data(){
        $this->isLoggedIn();
        
        $edit_id = sanitize($this->input->post('edit_id'));
        
        $query = $this->model_promotion->get_campaign_type_data($edit_id)->row();
    
        if ($query) {
            $data = array("success" => 1, 'message' => "Record deleted successfully!", 'result' => $query);
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function campaign_type_update_modal_confirm(){
        $this->isLoggedIn();
        
        $id = sanitize($_POST['id']);
        $name = sanitize($_POST['add_name']);
        $promo_img = sanitize($_POST['filename']);
        
        $get_campaign_type_data = $this->model_promotion->get_campaign_type_data($id)->row();
        $get_campaign_type_name = $this->model_promotion->get_campaign_type_name($name)->num_rows();

        $cur_val = [
            'name' => $name
        ];

        $prev_val = json_decode($_POST['prev_val'],true);

        $changes = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val),$prev_val);
        $remarks = "Campaign Type ".$cur_val['name']." has been updated successfully. \nChanges: \n$changes";
        
        if($get_campaign_type_data->name != $name){
            if($get_campaign_type_name > 0){
                $message = 'Campaign Name already exists.';
                $query = false;
            }else{
                $query = $this->model_promotion->campaign_type_update_modal_confirm($id, $name, $promo_img);
            }
        }else{
            $query = $this->model_promotion->campaign_type_update_modal_confirm($id, $name, $promo_img);
        }
        
        if ($query) {   
            $files = $_FILES;
            $file_name = '';

            if(!empty($_FILES)){

                $_FILES['userfile'] = [
                    'name'     => $files['image']['name'],
                    'type'     => $files['image']['type'],
                    'tmp_name' => $files['image']['tmp_name'],
                    'error'    => $files['image']['error'],
                    'size'     => $files['image']['size']
                ];
                $F[] = $_FILES['userfile'];
                
                //Upload requirements
                $file_name = $F[0]['name'];
                $config = array();
                $directory = 'assets/img';
                $config['file_name'] = $file_name;
                $config['upload_path'] = $directory;
                $config['allowed_types'] = '*';
                $config['max_size'] = 3000;
                //$this->load->library('upload',$config, 'logo');
                
                $this->upload->initialize($config);

                
                $data = array();
                if(!$this->upload->do_upload()){
                    
                }else{
                    $file_name = $this->upload->data()['file_name'];
                    ///upload image to s3 bucket
                    $fileTempName    = $F[0]['tmp_name'];
                    $activityContent = 'assets/img/promo_img/'.$file_name;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
                        generate_json($data);
                        die();
                    }

                    unlink($directory.'/'.$file_name);
                }
            }

            $data = array("success" => 1, 'message' => "Record updated successfully!");
            $this->audittrail->logActivity('Campaign Type', $remarks, 'update', $this->session->userdata('username'));
        }else{
            $data = array("success" => 0, 'message' => $message);
        }
        
        generate_json($data);
    }

    public function campaign_type_delete_modal_confirm(){
        $this->isLoggedIn();
        
        $delete_id = sanitize($this->input->post('delete_id'));
        
        //get campaign data using delete_id
        $get_campaign_type_data = $this->model_promotion->get_campaign_type_data($delete_id)->row();       
        $remarks = $get_campaign_type_data->name." has been successfully deleted";

        if ($delete_id > 0) {
            $query = $this->model_promotion->campaign_type_delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
                $this->audittrail->logActivity('Campaign Type', $remarks, 'deleted', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function export_campaign_type_list(){
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
        $query = $this->model_promotion->campaign_type_list($filters, $requestData, true);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $shopid = $filters['_shop'];
        $branchid = 'main';
        $fil_arr = [
            'Name' => $filters['_name'],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $fromdate, "Campaign Type", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Campaign Type', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Campaign Type");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);

        $sheet->setCellValue("A6", 'Campaign Name');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Campaign Type ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
    }

    //Mystery Coupon
    public function mystery_coupon($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['mystery_coupon']['view'] == 1) {
            //$content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = 13;

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                'shopcode'            => $this->model_promotion->get_shopcode($member_id),
                'shops'               => $this->model_promotion->get_shop_options(),
                'prod_prom_access'    => $this->loginstate->get_access()['mystery_coupon'],
                'product_categories'  => $this->model_promotion->get_product_categories(),
                'campaign'            => $this->model_promotion->get_all_campaign_type()
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('promotion/mystery_coupon', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function mc_product_table()
    {
        $this->isLoggedIn();
        $sys_shop = sanitize($this->input->post('select_shop'));
        $request = $_REQUEST;
        $query = $this->model_promotion->mc_product_table($sys_shop, $request);
        
        generate_json($query);
    }


    public function mc_fetch_productPromo(){
        $sys_shop = $this->input->post('select_shop');
        
        $productArr = $this->model_promotion->mc_fetch_productPromo($sys_shop)->result_array();
     
        $response = [
            'success' => true,
            'productArr' => $productArr
        ];

        echo json_encode($response);
    }

    public function get_campaign_type_list(){
        
        $campaignArray = $this->model_promotion->get_all_campaign_type();
     
        $response = [
            'success' => true,
            'campaignArray' => $campaignArray
        ];

        echo json_encode($response);
    }

    public function mc_update_promotion(){
        $formData             = json_decode($this->input->post('formData'));
        $formData             = json_decode(json_encode($formData), true);
        $deletedProductArr    = json_decode($this->input->post('deletedProductArr'));
        $deletedProductArr    = json_decode(json_encode($deletedProductArr), true);
        $postData             = [];
        $productIdArr         = array();
        $productNameArr       = array();
        $productPromoTypeArr  = array();
        $productPromoRateArr  = array();
        $productPromoPriceArr = array();
        $productPromoStockArr = array();
        $productPurchLimitArr = array();
        $productStatusArr     = array();

        foreach($formData as $row){
            if($row['name'] == 'start_date'){
                $postData['start_date'] = $row['value'];
            }
            if($row['name'] == 'end_date'){
                $postData['end_date'] = $row['value'];
            }
            if($row['name'] == 'start_time'){
                $postData['start_time'] = $row['value'];
            }
            if($row['name'] == 'end_time'){
                $postData['end_time'] = $row['value'];
            }
            if($row['name'] == 'todaydate'){
                $postData['todaydate'] = $row['value'];
            }
            if($row['name'] == 'batch_promo_type'){
                $postData['batch_promo_type'] = $row['value'];
            }
            if($row['name'] == 'batch_promo_rate'){
                $postData['batch_promo_rate'] = $row['value'];
            }
            if($row['name'] == 'batch_promo_price'){
                $postData['batch_promo_price'] = $row['value'];
            }
            if($row['name'] == 'batch_promo_stock_qty'){
                $postData['batch_promo_stock_qty'] = $row['value'];
            }
            if($row['name'] == 'batch_purch_limit_select'){
                $postData['batch_purch_limit_select'] = $row['value'];
            }
            if($row['name'] == 'batch_purch_limit_select'){
                $postData['batch_purch_limit_select'] = $row['value'];
            }
            if($row['name'] == 'product_id[]'){
                $productIdArr[] = $row['value'];
            }
            if($row['name'] == 'product_name[]'){
                $productNameArr[] = $row['value'];
            }
            if($row['name'] == 'product_promo_type[]'){
                $productPromoTypeArr[] = $row['value'];
            }
            if($row['name'] == 'product_promo_rate[]'){
                $productPromoRateArr[] = $row['value'];
            }
            if($row['name'] == 'product_promo_price[]'){
                $productPromoPriceArr[] = $row['value'];
            }
            if($row['name'] == 'product_promo_stock[]'){
                $productPromoStockArr[] = $row['value'];
            }
            if($row['name'] == 'product_purch_limit[]'){
                $productPurchLimitArr[] = $row['value'];
            }
            if($row['name'] == 'product_status[]'){
                $productStatusArr[] = $row['value'];
            }
        }
        // $product_id          = $this->input->post('product_id');
        // $product_name        = $this->input->post('product_name');
        // $product_promo_type  = $this->input->post('product_promo_type');
        // $product_promo_rate  = $this->input->post('product_promo_rate');
        // $product_promo_price = $this->input->post('product_promo_price');
        // $product_promo_stock = $this->input->post('product_promo_stock');
        // $product_purch_limit = $this->input->post('product_purch_limit');
        // $product_status      = $this->input->post('product_status');
        $product_id          = $productIdArr;
        $product_name        = $productNameArr;
        $product_promo_type  = $productPromoTypeArr;
        $product_promo_rate  = $productPromoRateArr;
        $product_promo_price = $productPromoPriceArr;
        $product_promo_stock = $productPromoStockArr;
        $product_purch_limit = $productPurchLimitArr;
        $product_status      = $productStatusArr;
        // $start_date          = date("Y-m-d ", strtotime($this->input->post('start_date')));
        // $end_date            = date("Y-m-d ", strtotime($this->input->post('end_date')));
        $start_date          = date("Y-m-d ", strtotime($postData['start_date']));
        $end_date            = date("Y-m-d ", strtotime($postData['end_date']));
        $start_time          = sanitize($postData['start_time']);
        $end_time            = sanitize($postData['end_time']);
        $start_date          = $start_date.$start_time.":00";
        $end_date            = $end_date.$end_time.":00";
        $deletedProductArr   = $deletedProductArr;
        $product_count       = (!empty($product_id)) ? count($product_id) : 0;



        /// audit trail
        for($i = 0; $i < $product_count; $i++) { 
            if($product_status[$i] == 2 || $product_status[$i] == 1){
                $dataArr = array(
                    'product_id'          => $product_id[$i],
                    'product_name'        => $product_name[$i],
                    'product_promo_type'  => $product_promo_type[$i],
                    'product_promo_rate'  => $product_promo_rate[$i],
                    'product_promo_price' => $product_promo_price[$i],
                    'product_promo_stock' => $product_promo_stock[$i],
                    'product_purch_limit' => $product_purch_limit[$i],
                    'product_status'      => $product_status[$i],
                    'start_date'          => $start_date,
                    'end_date'            => $end_date
                );
                $productDetails = $this->model_promotion->checkProdPromoDetails($product_id[$i]);
                $prodPromString = $this->audittrail->prodPromString($productDetails, $dataArr);
                $this->audittrail->logActivity('Mystery Coupon', $prodPromString, 'update', $this->session->userdata('username'));
            }
        }
      
        // inactive product promotion
        for($i = 0; $i < $product_count; $i++) { 
            if($product_status[$i] == 2){
                $dataArr = array(
                    'product_id'          => $product_id[$i],
                    'product_name'        => $product_name[$i],
                    'product_promo_type'  => $product_promo_type[$i],
                    'product_promo_rate'  => $product_promo_rate[$i],
                    'product_promo_price' => $product_promo_price[$i],
                    'product_promo_stock' => $product_promo_stock[$i],
                    'product_purch_limit' => $product_purch_limit[$i],
                    'product_status'      => $product_status[$i],
                    'start_date'          => $start_date,
                    'end_date'            => $end_date
                );
                $this->model_promotion->updateInactiveProduct($dataArr);
                // $this->audittrail->logActivity('Product Promotion', $product_name[$i].' successfully updated. Inactive', 'update', $this->session->userdata('username'));
            }
        }

        // active product promotion
        for($i = 0; $i < $product_count; $i++) { 
            if($product_status[$i] == 1){
                $dataArr = array(
                    'product_id'          => $product_id[$i],
                    'product_name'        => $product_name[$i],
                    'product_promo_type'  => $product_promo_type[$i],
                    'product_promo_rate'  => $product_promo_rate[$i],
                    'product_promo_price' => $product_promo_price[$i],
                    'product_promo_stock' => $product_promo_stock[$i],
                    'product_purch_limit' => $product_purch_limit[$i],
                    'product_status'      => $product_status[$i],
                    'start_date'          => $start_date,
                    'end_date'            => $end_date
                );
                $this->model_promotion->updateActiveProduct($dataArr);
                // $this->audittrail->logActivity('Product Promotion', $product_name[$i].' successfully updated. Active', 'update', $this->session->userdata('username'));
            }
        }

         // deleted product promotion
         if(!empty($deletedProductArr)){
            foreach($deletedProductArr as $value){
                if($value != ''){
                    $deletedProd = $this->model_promotion->updateDeletedProduct($value);
                    if($deletedProd != ""){
                        $this->audittrail->logActivity('Mystery Coupon', $deletedProd.' successfully deleted', 'delete', $this->session->userdata('username'));
                    }
                }
            }
        }

        $response['success'] = true;
        $response['message'] = "You have succesfully set the promotion for the nominated items.";
        echo json_encode($response);
    }

    //Shipping Fee Discount
    public function sf_discount($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['sf_discount']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                'shopcode'            => $this->model_promotion->get_shopcode($member_id),
                'shops'               => $this->model_promotion->get_shop_options(),
                'prod_prom_access'    => $this->loginstate->get_access()['sf_discount'],
                'product_categories'  => $this->model_promotion->get_product_categories(),
                'region'              => $this->model_promotion->get_all_region()->result()
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('promotion/sf_discount', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function shipping_fee_list(){
        $this->isLoggedIn();
        $filters = [
            '_name' => $this->input->post('_name'),
            '_select_shop' => $this->input->post('_select_shop'),
        ];

        
        $query = $this->model_promotion->shipping_fee_list($filters, $_REQUEST);
        generate_json($query);
    }

    public function saveShippingDiscount(){

        $sfd_name = $this->input->post('sfd_name');
        $select_shop = $this->input->post('select_shop');
        $usage_qty = $this->input->post('usage_qty');
        $requirement = $this->input->post('requirement');
        $limitOne_perCustomer = $this->input->post('limit_times') == null ? 0 : $this->input->post('limit_times');

        $sfd_code = $this->input->post('require_code') == '' ? '' : en_dec_vouchers("en", strtoupper($this->input->post('sfd_code')));
        $is_sfCodeRequired = $this->input->post('require_code') == '' ? '0': $this->input->post('require_code');

        $startdate = $this->input->post('date_from') == '' ? '' : date('Y-m-d', strtotime($this->input->post('date_from')));
        $starttime = $this->input->post('time_from') == '' ? '' : date('H:i', strtotime($this->input->post('time_from')));
        $enddate = $this->input->post('date_to') == '' ? '' : date('Y-m-d', strtotime($this->input->post('date_to')));
        $endtime = $this->input->post('time_to') == '' ? '' : date('H:i', strtotime($this->input->post('time_to')));

        $start_date = $startdate.' '.$starttime;
        $end_date = $enddate.' '.$endtime;

        $setEndDate = $this->input->post('setEndDate') == null ? 0 : $this->input->post('setEndDate');
        $count_row      = $this->input->post('count_row');
        $select_region  = $this->input->post('select_region');
        $shouldered_by  = $this->input->post('shouldered_by');

        $user_type_created = 0;
        $shopid = 0;
        if($this->session->userdata('sys_shop_id') != 0){
            $user_type_created = 1;
            $shopid = $this->session->userdata('sys_shop_id');
        }

        if(!empty($sfd_name)){

            if ($startdate == '' || $starttime == '') {
                $response['success'] = false;
                $response['message'] = "Start Date and Time is required!";
            } else if ($select_region == '') {
                $response['success'] = false;
                $response['message'] = 'Region is required!';
            } else if ($setEndDate == 1 && $enddate == '') {
                $response['success'] = false;
                $response['message'] = 'End Date and time is required!';
            } else if ($usage_qty < 1){
                $response['success'] = false;
                $response['message'] = "Usage Limit must not be less than 1";
            } else if ($is_sfCodeRequired == 1 && empty($sfd_code)) {
                $response['success'] = false;
                $response['message'] = 'Shipping Fee Code is required!';
	        } else if($requirement == 0){

                $is_percentage = "";
                $minimum_price = "";
                $subsidized = "";
                $sf_amount = "";

                $region = "";
                if ($select_region != '') {
                    $region = implode(',', $select_region);
                }

                // check if sf name do exists
                $check_sfname = $this->model_promotion->sf_name_is_exist($sfd_name);

                if ($check_sfname == 0) {
                    if ($sfd_code != '') {
                        $check_sfcode = $this->model_promotion->sf_code_is_exist($sfd_code); // check if sf code do exists
                        if ($check_sfcode == 0) {
                            $check_startdate = $this->check_sfd_date($startdate, $select_shop);
                            $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop);
                            $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop);
                            if($this->model_promotion->set_end_date_is_exist($shopid) == 0){
                                if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                    $this->db->trans_begin();
                                    $insert = $this->model_promotion->insert_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $is_percentage, $minimum_price, $region, $limitOne_perCustomer, $subsidized, $is_sfCodeRequired, $sf_amount, $user_type_created,$setEndDate,$shouldered_by);
                                    $success = $this->check_insert_update($insert);
                                    $response['success'] = $success == true ? true : false;
                                    $response['message'] = $success == true ? 'Shipping Fee Discount Saved!' : 'Error occur, try again later.';
                                } else {
                                    $response['success'] = false;
                                    $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                                }
                            }else {
                                $response['success'] = false;
                                $response['message'] = 'Avoid saving with existing no limit promotion.';
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = "Shipping Fee Code already exists!";
                        }
                    } else {
                        $check_startdate = $this->check_sfd_date($startdate, $select_shop);
                        $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop);
                        $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop);
                        if($this->model_promotion->set_end_date_is_exist($shopid) == 0){
                            if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                $insert = $this->model_promotion->insert_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $is_percentage, $minimum_price, $region, $limitOne_perCustomer, $subsidized, $is_sfCodeRequired, $sf_amount, $user_type_created,$setEndDate,$shouldered_by); 
                                $success = $this->check_insert_update($insert);
                                $response['success'] = $success == true ? true : false;
                                $response['message'] = $success == true ? 'Shipping Fee Discount Saved!' : 'Error occur, try again later.';
                            } else {
                                $response['success'] = false;
                                $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                            }
                        }else {
                            $response['success'] = false;
                            $response['message'] = 'Avoid saving with existing no limit promotion.';
                        }
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "Shipping Fee Name already exists!";
                }
                
                $this->audittrail->logActivity('Shipping Fee Discount', $sfd_name.' has been successfully added.', 'add', $this->session->userdata('username'));

            }
            else if($count_row != "" && $requirement != 0 && !empty($select_region)){
                $error = 0;
                // $count = 0;

                $min_price = $this->input->post('minimum_price');
                $is_subsidize = $this->input->post('is_subsidize');
                $sf_amnt = $this->input->post('sf_amount');

                if (!empty($min_price)) {
                    foreach ($min_price as $key => $value) {
                        if (empty($value) || $value == '') {
                            $error++;
                        }
                    }
                } else {
                    $error++;
                }

                if (!empty($is_subsidize) && !empty($sf_amnt)) {
                    if (count($min_price) == count($is_subsidize) && count($min_price) == count($sf_amnt)) {
                        foreach ($is_subsidize as $key => $value) {
                            if ($value == '') {
                                $error++;
                            }
                        }
                    } else {
                        $error++;
                    }
                } else {
                    $error++;
                }

                if($error != 0){

                    $response['success'] = false;
                    $response['message'] = "Please fill out all required fields on shipping fee conditions.";
                }//if error 
                else{

		            $is_percentage = $this->input->post('isPercentage');
                    $minimum_price = $this->input->post('minimum_price');
                    $subsidized = $this->input->post('is_subsidize');
                    $sf_amount = $this->input->post('sf_amount');
                    $min_array = [];
                    $sub_array = [];
                    $amount_array = [];
		            $percent_array = [];

                    foreach($minimum_price as $key) { 
                        $min_array[] = $key;
                    }
                    foreach($subsidized as $key) { 
                        $sub_array[] = $key;
                    }
                    foreach($sf_amount as $key) { 
                        $amount_array[] = $key;
                    }
		            foreach($is_percentage as $key) {
                        $percent_array[] = $key;
                    }
                   
                    if($start_date == ''){
                        $start_date = "0000-00-00 00:00:00";
                    }
                    if($end_date == ''){
                        $end_date = "0000-00-00 00:00:00";
                    }
                    
                    $amnt_values = array();
                    $sub_values = array();
                    $percent_values = array();

                    foreach ($sub_array as $key => $value) {
                        if (empty($value) || $value == '') {
                            $value = '0';
                        }
                        array_push($sub_values, $value);
                    }

                    foreach ($percent_array as $key => $value) {
                        if (empty($value) || $value == '') {
                            $value = '0';
                        }
                        array_push($percent_values, $value);
                    }

                    foreach($amount_array as $key => $value) {
                        if (empty($value) || $value == '') {
                            $value = '0';
                        }
                        array_push($amnt_values, $value);
                    }
                    
                    $min_array = implode(',', $min_array);
                    $amount_array = implode(',', $amnt_values);
                    $sub_array = implode(',', $sub_values);
                    $percent_array = implode(',', $percent_values);

                    $region = "";
                    if($this->input->post('select_region') != ''){
                        $region = implode(",", $this->input->post('select_region'));
                    }

                    $check_sfname = $this->model_promotion->sf_name_is_exist($sfd_name);

                    if ($check_sfname == 0) {
                        if ($sfd_code != '') {
                            $check_sfcode = $this->model_promotion->sf_code_is_exist($sfd_code);
                            if ($check_sfcode == 0) {
                                $check_startdate = $this->check_sfd_date($startdate, $select_shop);
                                $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop);
                                $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop);
                                if($this->model_promotion->set_end_date_is_exist($shopid) == 0){
                                    if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                        $this->db->trans_begin();
                                        $insert_with_requirements = $this->model_promotion->insert_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $percent_array, $min_array, $region, $limitOne_perCustomer, $sub_array, $is_sfCodeRequired, $amount_array, $user_type_created,$setEndDate,$shouldered_by);
                                        $success = $this->check_insert_update($insert_with_requirements);
                                        $response['success'] = $success == true ? true : false;
                                        $response['message'] = $success == true ? 'Shipping Fee Discount Saved!' : 'Error occur, try again later.';
                                    } else {
                                        $response['success'] = false;
                                        $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                                    }
                                }else {
                                    $response['success'] = false;
                                    $response['message'] = 'Avoid saving with existing no limit promotion.';
                                }
                            } else {
                                $response['success'] = false;
                                $response['message'] = "Shipping Fee Code already exists!";
                            }
                        } else {
                            $check_startdate = $this->check_sfd_date($startdate, $select_shop);
                            $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop);
                            $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop);
                            if($this->model_promotion->set_end_date_is_exist($shopid) == 0){
                                if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                    $insert_with_requirements1 = $this->model_promotion->insert_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $percent_array, $min_array, $region, $limitOne_perCustomer, $sub_array, $is_sfCodeRequired, $amount_array, $user_type_created,$setEndDate,$shouldered_by);
                                    $success = $this->check_insert_update($insert_with_requirements1);
                                    $response['success'] = $success == true ? true : false;
                                    $response['message'] = $success == true ? 'Shipping Fee Discount Saved!' : 'Error occur, try again later.';
                                } else {
                                    $response['success'] = false;
                                    $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                                }
                            }else {
                                $response['success'] = false;
                                $response['message'] = 'Avoid saving with existing no limit promotion.';
                            }
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Shipping Fee Name already exists!";
                    }
                    
                    $this->audittrail->logActivity('Shipping Fee Discount', $sfd_name.' has been successfully added.', 'add', $this->session->userdata('username'));
                }
            }
            else{
                $response['success'] = false;
                $response['message'] = "Please fill out all required fields on shipping fee conditions.";
            }
        }//end if required fields is not complete
        else{
            $response['success'] = false;
            $response['message'] = "Please Complete All Required Fields!";
        }//end else

        echo json_encode($response);
    }

    private function check_insert_update($query)
    {
        if ($query === FALSE) {
            $this->db->trans_rollback();
            $success = false;
        } else {
            $this->db->trans_commit();
            $success = true;
        }

        return $success;
    }

    private function check_sfd_date($startdate, $shop_id, $update_id = null)
    {
        $data['start_date'] = $startdate;
        $data['start_date2'] = $startdate;
        //$data['admin_shop_id'] = 0;
        $data['merch_shop_id'] = $shop_id;
        $data['status'] = 1;
        if ($update_id != null) {
            $data['update_id'] = $update_id;
            $query = $this->model_promotion->check_sfd_date_update($data);
        } else {
            $query = $this->model_promotion->check_sfd_date($data);
        }

        return $query;
    }

    private function check_sfd_date_endDate($startdate, $shop_id, $update_id = null)
    {
        $data['end_date'] = $startdate;
        //$data['admin_shop_id'] = 0;
        $data['merch_shop_id'] = $shop_id;
        $data['status'] = 1;
        if ($update_id != null) {
            $data['update_id'] = $update_id;
            $query = $this->model_promotion->check_sfd_date_endDate_update($data);
        } else {
            $query = $this->model_promotion->check_sfd_date_endDate($data);
        }

        return $query;
    }

    public function check_sfd_endDateOnly($enddate, $shop_id, $update_id = null)
    {
        $data['end_date'] = $enddate;
        //$data['admin_shop_id'] = 0;
        $data['merch_shop_id'] = $shop_id;
        $data['status'] = 1;
        if ($update_id != null) {
            $data['update_id'] = $update_id;
            $query = $this->model_promotion->check_sfd_endDateOnly_update($data);
        } else {
            $query = $this->model_promotion->check_sfd_endDateOnly($data);
        }

        return $query;
    }

    // public function check_sfd_noEndDate($startdate, $shop_id, $update_id = null)
    // {
    //     $data['start_date'] = $startdate;
    //     $date['start_date2'] = $startdate;
    //     $data['admin_shop_id'] = 0;
    //     $data['merch_shop_id'] = $shop_id;
    //     $data['status'] = 1;
    //     if ($update_id != null) {
    //         $data['update_id'] = $update_id;
    //         $query = $this->model_promotion->check_sfd_noEndDate_update($data);
    //     } else {
    //         $query = $this->model_promotion->check_sfd_noEndDate($data);
    //     }
        
    //     return $query;
    // }

    // public function check_sfd_EndDate($enddate, $shop_id, $update_id = null)
    // {
    //     $data['start_date'] = $enddate;
    //     $data['admin_shop_id'] = 0;
    //     $data['merch_shop_id'] = $shop_id;
    //     $data['status'] = 1;
    //     if ($update_id != null) {
    //         $data['update_id'] = $update_id;
    //         $query = $this->model_promotion->check_sfd_EndDate_update($data);
    //     } else {
    //         $query = $this->model_promotion->check_sfd_EndDate($data);
    //     }
        
    //     return $query;
    // }

    public function sf_discount_delete_modal_confirm(){
        $this->isLoggedIn();
        
        $delete_id = sanitize($this->input->post('delete_id'));
        
        //get campaign data using delete_id
        $get_sf_data = $this->model_promotion->get_sf_data($delete_id)->row();       
        $remarks = $get_sf_data->shipping_discount_name." has been successfully deleted";

        if ($delete_id > 0) {
            $query = $this->model_promotion->sf_delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
                $this->audittrail->logActivity('Shipping Fee Discount', $remarks, 'deleted', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function get_sf_data(){
        $this->isLoggedIn();
        
        $edit_id = sanitize($this->input->post('edit_id'));
        
        $query = $this->model_promotion->get_sf_data($edit_id)->row_array();

        $finalData = array();

        $minimum_price = explode(',', $query["minimum_price"]);
        $is_subsidize = explode(',',$query["is_subsidize"]);
        $sf_discount = explode(',',$query["sf_discount"]);
        $is_percentage = explode(',', $query["is_percentage"]);
        $counter = count($minimum_price);
        $count = 0;

        $markup = "";
        $option = "";
        $free = false;

        if($query["requirement_isset"] != 0){
        
            do{

                if($is_subsidize[$count] != 0){
                    $free = true;
                    $checked_f = "checked=true";
                    $checked_s = "";
                    $disabled = "";
                    $style = 'style="display: none;"';
                }
                else{
                    $checked_s = "checked=true";
                    $checked_f = "";
                    $disabled = 'value="'.intval($sf_discount[$count]).'"';
                    $style = "";
                }

                if ($is_percentage[$count] == 0) {
                    $option .= "<option value='0' selected>Fixed</option>";
                    $option .= "<option value='1'>Percentage</option>";
                } else if ($is_percentage[$count] == 1) {
                    $option .= "<option value='0'>Fixed</option>";
                    $option .= "<option value='1' selected>Percentage</option>";
                } else {
                    $option .= "<option value='0' selected>Fixed</option>";
                    $option .= "<option value='1'>Percentage</option>";
                }

                $markup .= "<tr id='edit_count_row' rowIndex='".$count."' class='edit_product_tr_".$count."'>";
                    $markup .= '<td><input type="text" value="'.intval($minimum_price[$count]).'" id="edit_minimum_price" name="edit_minimum_price[]" class="form-control material_josh form-control-sm search-input-text m_input_edit" placeholder="Amount"><p id="m_price_error_edit" class="m_price_error_edit" style="color:red"></p></td>';
                    $markup .= '<td><input type="checkbox" '.$checked_s.' id="edit_enabledId'.$count.'" name="edit_is_subsidize[]" value="0" class="edit_checkBtn sfd_input_edit edit_checks'.$count.'"  data-value="'.$count.'">';
                    $markup .= '<span> Discount Amount </span> <select name="edit_isPercentage[]" '.$style.' class="form-control edit_select_element" id="edit_isPercentage'.$count.'">'.$option.'</select>';
                    $markup .= '<input type="text" '.$disabled.' '.$style.' id="edit_sf_amount" value="0" name="edit_sf_amount[]" class="form-control edit_show_sf'.$count.' sf_input_edit" placeholder="Amount"><p id="sf_price_error_edit" class="sf_price_error_edit" style="color:red"></p>';
                    $markup .= '<br><input type="checkbox" '.$checked_f.'  name="edit_is_subsidize[]" value="1" class="edit_checkBtn sfd_input_edit edit_fsBtn" id="edit_checkSf'.$count.'" data-value="'.$count.'"><span> Free Shipping</span><input type="hidden" name="edit_count_row" value="'.$counter.'"></td>';
                    $markup .= '<td><a class="btn btn-xs edit-delete-record" data-value="'.$count.'"><i class="fa fa-trash"></i></a></td>';
                $markup .= "</tr>";
                $count ++;
            }while($count<$counter);

        }
            

        $finalData = array(
            'id'                     => $query["id"],
            'shipping_discount_name' => $query["shipping_discount_name"],
            'shipping_discount_code' => $query["shipping_discount_code"] == '' ? '' : en_dec_vouchers("dec", $query["shipping_discount_code"]),
            'shop_id'                => $query["shop_id"],
            'is_sfCodeRequired'      => $query['is_sfCodeRequired'],
            'no_of_stocks'           => $query["no_of_stocks"],
            'limitOne'               => $query["limitone_perCustomer"],
            'start_date'             => date('Y-m-d', strtotime($query["start_date"])),
            'start_time'             => date('H:i', strtotime($query["start_date"])),
            'end_date'               => $query["end_date"] == '0000-00-00 00:00:00' ? '' : date('Y-m-d', strtotime($query["end_date"])),
            'end_time'               => $query["end_date"] == '0000-00-00 00:00:00' ? '' : date('H:i', strtotime($query["end_date"])),
            'requirement_isset'      => $query["requirement_isset"],
            'region'                 => $query["region"],
            'total_element'          => $counter,
            'free'                   => $free,
            'is_percentage'          => $query["is_percentage"],
            'minimum_price'          => $query["minimum_price"],
            'is_subsidize'           => $query["is_subsidize"],
            'sf_discount'            => $query["sf_discount"],
            'markup'                 => $markup
        );

    
        if ($query) {
            $data = array("success" => 1, 'message' => "Record deleted successfully!", 'result' => $finalData);
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function updateShippingDiscount(){

        $sfd_name       = $this->input->post('edit_sfd_name');
        $sfd_code       = $this->input->post('edit_sfd_code') == '' ? '' : en_dec_vouchers("en", strtoupper($this->input->post('edit_sfd_code')));
        $select_shop    = $this->input->post('edit_select_shop');
        $usage_qty      = $this->input->post('edit_usage_qty');
        $limitOne       = $this->input->post('edit_limit_times') == null ? 0 : $this->input->post('edit_limit_times');
        $is_sfCodeRequired = $this->input->post('edit_require_code') == '' ? 0 : 1;
        $requirement    = $this->input->post('edit_requirement');
        $startdate      = $this->input->post('edit_date_from') == '' ? '' : date('Y-m-d', strtotime($this->input->post('edit_date_from')));
        $starttime      = $this->input->post('edit_time_from') == '' ? '' : date('H:i', strtotime($this->input->post('edit_time_from')));
        $enddate        = $this->input->post('edit_date_to') == '' ? '' : date('Y-m-d', strtotime($this->input->post('edit_date_to')));
        $endtime        = $this->input->post('edit_time_to') == '' ? '' : date('H:i', strtotime($this->input->post('edit_time_to')));
        $start_date     = $startdate.' '.$starttime;
        $end_date       = $enddate.' '.$endtime;
        $edit_setEndDate= $this->input->post('edit_setEndDate') == null ? 0 : $this->input->post('edit_setEndDate');
        $edit_id        = $this->input->post('edit_id');
        $count_row      = $this->input->post('edit_count_row');

       // print_r($sfd_name);

        $shopid = 0;
        if($this->session->userdata('sys_shop_id') != 0){
            $shopid = $this->session->userdata('sys_shop_id');
        }

        if(!empty($sfd_name)){

            if ($start_date == '' || $starttime == '') {
                $response['success'] = false;
                $response['message'] = "Start Date and Time is required!";
            } else if ($this->input->post('edit_select_region') == '') {
                $response['success'] = false;
                $response['message'] = 'Region is required!';
            }else if ($edit_setEndDate == 1 && $enddate == '') {
                $response['success'] = false;
                $response['message'] = 'End Date and Time is required!';
            } else if($usage_qty < 1){
                $response['success'] = false;
                $response['message'] = "Usage Quantity must not be less than 1";
            } else if ($is_sfCodeRequired && empty($sfd_code)) {
                $response['success'] = false;
                $response['message'] = "Shipping Fee Code is required!";
            }
            else if($requirement == 0){

                $is_percentage = "";
                $minimum_price = "";
                $subsidized = "";
                $sf_amount = "";

                $region = "";
                if (!empty($this->input->post('edit_select_region'))) {
                    $region = implode(',', $this->input->post('edit_select_region'));
                }

                $update_check_sfname = $this->model_promotion->sf_name_is_exist_update($sfd_name, $edit_id);
                
                if ($update_check_sfname == 0) {
                    if ($sfd_code != '') {
                        $update_check_sfcode = $this->model_promotion->sf_code_is_exist_update($sfd_code, $edit_id);
                        if ($update_check_sfcode == 0) {
                            $check_startdate = $this->check_sfd_date($startdate, $select_shop, $edit_id);
                            $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop, $edit_id);
                            $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop, $edit_id);
                            if($this->model_promotion->set_end_date_is_exist_update($shopid,$edit_id) == 0){
                                if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                    $this->db->trans_begin();
                                    $update = $this->model_promotion->update_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $minimum_price, $region, $subsidized, $sf_amount,$edit_id, $is_percentage, $limitOne, $is_sfCodeRequired,$edit_setEndDate);
                                    $success = $this->check_insert_update($update);
                                    $response['success'] = $success == true ? true : false;
                                    $response['message'] = $success == true ? 'Shipping fee discount updated!' : 'Error occur, try again later!';
                                } else {
                                    $response['success'] = false;
                                    $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                                }
                            } else {
                                $response['success'] = false;
                                $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Shipping Fee Code already exists!';
                        }
                    } else {
                        $check_startdate = $this->check_sfd_date($startdate, $select_shop, $edit_id);
                        $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop, $edit_id);
                        $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop, $edit_id);
                        if($this->model_promotion->set_end_date_is_exist_update($shopid,$edit_id) == 0){
                            if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                $update = $this->model_promotion->update_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $minimum_price, $region, $subsidized, $sf_amount,$edit_id, $is_percentage, $limitOne, $is_sfCodeRequired,$edit_setEndDate);
                                $success = $this->check_insert_update($update);
                                $response['success'] = $success == true ? true : false;
                                $response['message'] = $success == true ? 'Shipping fee discount updated!' : 'Error occur, try again later!';
                            } else {
                                $response['success'] = false;
                                $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                        }
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Shipping Fee Name already exists!';
                }
                    
                $this->audittrail->logActivity('Shipping Fee Discount', $sfd_name.' has been successfully updated.', 'update', $this->session->userdata('username'));

            }

            else if($count_row != "" && $requirement != 0 && !empty($this->input->post('edit_select_region'))){
                //print_r(!empty($this->input->post('is_subsidize')));
                $error = 0;
                // $count = 0;

                $min_price = $this->input->post('edit_minimum_price');
                $is_subsidize = $this->input->post('edit_is_subsidize');
                $sf_amnt = $this->input->post('edit_sf_amount');

                if (!empty($min_price)) {
                    foreach ($min_price as $key => $value) {
                        if (empty($value) || $value == '') {
                            $error++;
                        }
                    }
                } else {
                    $error++;
                }

                if (!empty($is_subsidize) && !empty($sf_amnt)) {
                    if (count($min_price) == count($is_subsidize) && count($min_price) == count($sf_amnt)) {
                        foreach ($is_subsidize as $key => $value) {
                            if ($value == '') {
                                $error++;
                            }
                        }
                    } else {
                        $error++;
                    }
                } else {
                    $error++;
                }

                if($error != 0){

                    $response['success'] = false;
                    $response['message'] = "Please fill up all required fields in shipping fee condition.";
                }//if error 
                else{
                    
                    $region = "";
                    if(!empty($this->input->post('edit_select_region'))){
                        $region = implode(",", $this->input->post('edit_select_region'));
                    }

		            $is_percentage = $this->input->post('edit_isPercentage');
                    $minimum_price = $this->input->post('edit_minimum_price');
                    $subsidized = $this->input->post('edit_is_subsidize');
                    $sf_amount = $this->input->post('edit_sf_amount');
                    $min_array = [];
                    $sub_array = [];
                    $amount_array = [];
		            $percent_array = [];

                    foreach($minimum_price as $key) { 
                        $min_array[] = $key;
                    }
                    foreach($subsidized as $key) { 
                        $sub_array[] = $key;
                    }
                    foreach($sf_amount as $key) { 
                        $amount_array[] = $key;
                    }
                    foreach($is_percentage as $key) {
                        $percent_array[] = $key;
                    }
                   
                    if($start_date == ''){
                        $start_date = "0000-00-00 00:00:00";
                    }
                    if($end_date == ''){
                        $end_date = "0000-00-00 00:00:00";
                    }

                    $min_array = implode(',' , $min_array);
                    $sub_array = implode(',' , $sub_array);
                    $amount_array = implode(',' , $amount_array);
                    $percent_array = implode(',', $percent_array);

                    $update_check_sfname = $this->model_promotion->sf_name_is_exist_update($sfd_name, $edit_id);
                
                    if ($update_check_sfname == 0) {
                        if ($sfd_code != '') {
                            $update_check_sfcode = $this->model_promotion->sf_code_is_exist_update($sfd_code, $edit_id);
                            if ($update_check_sfcode == 0) {
                                $check_startdate = $this->check_sfd_date($startdate, $select_shop, $edit_id);
                                $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop, $edit_id);
                                $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop, $edit_id);
                                if($this->model_promotion->set_end_date_is_exist_update($shopid,$edit_id) == 0){
                                    if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                        $this->db->trans_begin();
                                        $update = $this->model_promotion->update_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $min_array, $region, $sub_array, $amount_array,$edit_id, $percent_array, $limitOne, $is_sfCodeRequired,$edit_setEndDate);
                                        $success = $this->check_insert_update($update);
                                        $response['success'] = $success == true ? true : false;
                                        $response['message'] = $success == true ? 'Shipping fee discount updated!' : 'Error occur, try again later!';
                                    } else {
                                        $response['success'] = false;
                                        $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                                    }
                                }else {
                                    $response['success'] = false;
                                    $response['message'] = 'Avoid saving with existing no limit promotion.';
                                }
                            } else {
                                $response['success'] = false;
                                $response['message'] = 'Shipping Fee Code already exists!';
                            }
                        } else {
                            $check_startdate = $this->check_sfd_date($startdate, $select_shop, $edit_id);
                            $check_startdate_endDate = $this->check_sfd_date_endDate($startdate, $select_shop, $edit_id);
                            $check_endDateOnly = $enddate == '' ? 0 : $this->check_sfd_endDateOnly($enddate, $select_shop, $edit_id);
                            if($this->model_promotion->set_end_date_is_exist_update($shopid,$edit_id) == 0){
                                if ($check_startdate == 0 && $check_startdate_endDate == 0 && $check_endDateOnly == 0) {
                                    $update = $this->model_promotion->update_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $min_array, $region, $sub_array, $amount_array,$edit_id, $percent_array, $limitOne, $is_sfCodeRequired,$edit_setEndDate);
                                    $success = $this->check_insert_update($update);
                                    $response['success'] = $success == true ? true : false;
                                    $response['message'] = $success == true ? 'Shipping fee discount updated!' : 'Error occur, try again later!';
                                } else {
                                    $response['success'] = false;
                                    $response['message'] = 'Avoid saving shipping fee discount with same promotion date.';
                                }
                            }else {
                                $response['success'] = false;
                                $response['message'] = 'Avoid saving with existing no limit promotion.';
                            }
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Shipping Fee Name already exists!';
                    }
                    
                    $this->audittrail->logActivity('Shipping Fee Discount', $sfd_name.' has been successfully updated.', 'update', $this->session->userdata('username'));
                }
            }
            else{
                $response['success'] = false;
                $response['message'] = "Please fill out all required fields on shipping fee conditions.";
            }
            
        }//end if required fields is not complete
        else{
            $response['success'] = false;
            $response['message'] = "Please Complete All Required Fields!";
        }//end else

        echo json_encode($response);
    }

    public function get_feutured_products_count_piso(){
        $data = $this->model_promotion->getFeaturedProductCountPiso();
        echo json_encode($data);
    }

    public function check_feutured_product_arrangementPiso($product_number= ""){
    
        $data = $this->model_promotion->checkFeaturedProductArrangementPiso($product_number);
        echo json_encode($data);
    }


    public function save_featured_piso(){

        $feauted_piso_data = array(
        'product_id' => $this->input->post('product_id'),
        'product_arrangement' => $this->input->post('product_arrangement'),
        );

        $this->model_promotion->save_featured_piso($feauted_piso_data);
        $response['success']  = 1;
        echo json_encode($response);
   
      }


      public function removed_featured_piso(){

        $feauted_piso_data = array(
        'product_id' => $this->input->post('product_id'),
        );

        $this->model_promotion->removed_featured_piso($feauted_piso_data);
        $response['success']  = 1;
        echo json_encode($response);
   
      }



      ///voucher discounts 

      public function voucher_discounts($token = '')
      {
          $this->isLoggedIn();
          if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['voucher_discount']['view'] == 1) {
              $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
              $main_nav_id = $this->views_restriction($content_url);
  
              $member_id = $this->session->userdata('sys_users_id');
          
              $data_admin = array(
                  'token'               => $token,
                  'main_nav_id'         => $main_nav_id, //for highlight the navigation
                  'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                  'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                  'shopcode'            => $this->model_promotion->get_shopcode($member_id),
                  'shops'               => $this->model_promotion->get_shop_options(),
                  'prod_prom_access'    => $this->loginstate->get_access()['product_promotion'],
                  'product_categories'  => $this->model_promotion->get_product_categories(),
                  'featured_productsPiso'   => $this->model_promotion->getFeaturedProductPiso(),
                  
              );
  
              $this->load->view('includes/header', $data_admin);
              $this->load->view('promotion/promotion_voucher_discount', $data_admin);
          }else{
              $this->load->view('error_404');
          }
      }



      public function get_vouchers_list_table($exportable = false){
        $this->isLoggedIn();
    
        $filters = [
                '_voucher_type'  => sanitize($this->input->post('_voucher_type')),
                '_voucher_code'  => sanitize($this->input->post('_voucher_code')),
                '_vname'  => sanitize($this->input->post('_vname')),
                'date_from'  => $this->input->post('date_from'),
                'date_to'  => $this->input->post('date_to'),
                '_record_status'  => $this->input->post('_record_status'),
            ];
    
       //  die($this->input->post('_voucher_code'));
        $data = $this->model_promotion->get_vouchers_discount_table($filters, $_REQUEST);
        echo json_encode($data);
      }


      public function add_voucher_discount($token = '')
      {
  
      $this->isLoggedIn();
      if ($this->loginstate->get_access()['voucher_discount']['create'] == 1){
              // start - data to be used for views
              $member_id = $this->session->userdata('sys_users_id');
              $data_admin = array(
                    'token' => $token,
                    'type'  => 'New User',
                     'id'	=> '',
                     'shops'               => $this->model_promotion->get_shop_options(),
                     'product_categories'  => $this->model_promotion->get_product_categories(),
                     'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                     'shopcode'            => $this->model_promotion->get_shopcode($member_id),
                    // 'shops_per_id' => $this->Model_list_vouchers->get_sys_shop_per_id(),
                    // 'shops' => $this->Model_list_vouchers->get_shop_options(),
                    );
              // end - data to be used for views
  
              // start - load all the views synchronously
              $this->load->view('includes/header', $data_admin);
              $this->load->view('promotion/product_voucher_discount_add', $data_admin);
              // end - load all the views synchronously
          }else{
              $this->load->view('error_404');
          }
   
      }


      public function check_voucher_code($voucher_code= ""){
    
        $data = $this->model_promotion->check_voucher_code($voucher_code);
        echo json_encode($data);
      }



      public function save_voucher(){
        $this->isLoggedIn();
    

        $set_amount_limit      = sanitize($this->input->post('set_amount_limit'));
        if($set_amount_limit == 1){
            $disc_ammount_limit    = sanitize($this->input->post('disc_ammount_limit'));
        } else if($set_amount_limit == 2){
             $disc_ammount_limit    = 0;
        }else{
            $disc_ammount_limit    = 0;
        }

         $unwantedChars = [];
        $voucher_type          = sanitize($this->input->post('voucher_type'));
        if($voucher_type == 2){
            $product_id_list   = $this->input->post('product_id');
            $product_id        = implode(',' , $product_id_list);
            $shop_id           = 0;
        }else if($voucher_type == 1){
            $shop_id_list     = $this->input->post('shop_id');
            $shop_id = '{' . implode('},{', $shop_id_list) . '}';
            $product_id       = 0;

        }else{
            $shop_id          = 0;
            $product_id       = 0;
        }


        $data_admin = array(
                                
            'voucher_type'         => sanitize($this->input->post('voucher_type')),
            'product_id'           => $product_id,
            'shop_id'              => $shop_id,
            'voucher_name'         => sanitize($this->input->post('voucher_name')),
            'voucher_code'         => sanitize($this->input->post('voucher_code')),
            'date_from'            => sanitize($this->input->post('date_from')),
            'date_to'              => sanitize($this->input->post('date_to')),
            'disc_ammount_type'    => sanitize($this->input->post('disc_ammount_type')),
            'disc_ammount'         => sanitize($this->input->post('disc_ammount')),
            'set_amount_limit'     => sanitize($this->input->post('set_amount_limit')),
            'disc_ammount_limit'   =>  $disc_ammount_limit,
            'minimum_basket_price' => sanitize($this->input->post('minimum_basket_price')),
            'usage_quantity'       => sanitize($this->input->post('usage_quantity')),
        );

        $query = $this->model_promotion->vouchers_add($data_admin);

        $vname = sanitize($this->input->post('voucher_name'));
        $vcode  =  sanitize($this->input->post('voucher_code'));

        if ($query) {
        $data = array("success" => 1, 'message' => "Record added successfully!");
        $this->audittrail->logActivity('Promotion Voucher Discount', "Voucher with $vname and $vcode added successfully.", 'add', $this->session->userdata('username'));
        }else{
        $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }

        generate_json($data);

       
    }


    public function shop_table()
    {
        $this->isLoggedIn();
        $sys_shop = sanitize($this->input->post('select_shop'));
        $request = $_REQUEST;
        $query = $this->model_promotion->shop_table($request);
        
        generate_json($query);
    }


    
  public function edit_vouchers($id)
  {
    $token_session = $this->session->userdata('token_session');
    $token = en_dec('en', $token_session);
    $this->isLoggedIn();
    if ($this->loginstate->get_access()['voucher_discount']['update'] == 1){
          // start - data to be used for views
          $member_id = $this->session->userdata('sys_users_id');
          $voucher_details	   = $this->model_promotion->edit_voucher($id)->row();
          $shop_id    =  explode(',' , $voucher_details->shop_id);

          $product_id =  explode(',' , $voucher_details->product_id);

         

          foreach($shop_id as $value){
                $shop_id  = $value;
                $shop_id = trim($shop_id, '{}');
                 $shop_details = $this->model_promotion->get_shop_details($shop_id)->row();
            }
            
      
            
            foreach($product_id as $value){
                $product_id  = $value;
                $product_details = $this->model_promotion->get_product_details($product_id)->row();
        
           }

            $data_admin = array(
                'token' => $token,
                'type'  => 'New User',
                'id'	=> $id,
                'voucher_details'	   => $this->model_promotion->edit_voucher($id)->row(),
                'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                'shopcode'            => $this->model_promotion->get_shopcode($member_id),
            );

        

          // start - load all the views synchronously
          $this->load->view('includes/header', $data_admin);
          $this->load->view('promotion/product_voucher_discount_edit', $data_admin);
          // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }

  }


  public function update_voucher(){

        $set_amount_limit      = sanitize($this->input->post('set_amount_limit'));
        if($set_amount_limit == 1){
            $disc_ammount_limit    = sanitize($this->input->post('disc_ammount_limit'));
        } else if($set_amount_limit == 2){
            $disc_ammount_limit    = 0;
        }else{
            $disc_ammount_limit    = 0;
        }

        $voucher_type          = sanitize($this->input->post('voucher_type'));
        if($voucher_type == 2){
            $product_id_list   = $this->input->post('product_id');
             
            if($product_id_list != NULL  && $product_id_list != 'NULL'){
                $product_id        = implode(',' , $product_id_list);
            }
            else{
                $product_id          = 0;
            }
          
            $shop_id           = 0;
        }else if($voucher_type == 1){
            $shop_id_list     = $this->input->post('shop_id');
    
            if($shop_id_list != NULL  && $shop_id_list != 'NULL'){
                $shop_id = '{' . implode('},{', $shop_id_list) . '}';
            }else{
                $shop_id          = 0;
            }

            $product_id       = 0;
        }else{
            $shop_id          = 0;
            $product_id       = 0;
        }

        if($shop_id == NULL  && $shop_id == 'NULL'){
            $shop_id = 0;
        }

        if($product_id == NULL  && $product_id == 'NULL'){
            $product_id = 0;
        }


    

        $data_admin = array(
            'vouchers_id'          => sanitize($this->input->post('vouchers_id')),              
            'voucher_type'         => sanitize($this->input->post('voucher_type')),
            'product_id'           => $product_id,
            'shop_id'              => $shop_id,
            'voucher_name'         => sanitize($this->input->post('voucher_name')),
            'voucher_code'         => sanitize($this->input->post('voucher_code')),
            'date_from'            => sanitize($this->input->post('date_from')),
            'date_to'              => sanitize($this->input->post('date_to')),
            'disc_ammount_type'    => sanitize($this->input->post('disc_ammount_type')),
            'disc_ammount'         => sanitize($this->input->post('disc_ammount')),
            'set_amount_limit'     => sanitize($this->input->post('set_amount_limit')),
            'disc_ammount_limit'   =>  $disc_ammount_limit,
            'minimum_basket_price' => sanitize($this->input->post('minimum_basket_price')),
            'usage_quantity'       => sanitize($this->input->post('usage_quantity')),
        );

        $query = $this->model_promotion->vouchers_update_data($data_admin);
                                            
        $vname = sanitize($this->input->post('voucher_name'));
        $vcode  =  sanitize($this->input->post('voucher_code'));

        if ($query) {
        $data = array("success" => 1, 'message' => "Record added successfully!");
        $this->audittrail->logActivity('Promotion Voucher Discount', "Voucher with $vname and $vcode added successfully.", 'add', $this->session->userdata('username'));
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
      
       // die($record_status.' <br>'.$disable_id.'<br>'.$record_text);
       
      
      
            $query = $this->model_promotion->disable_modal_confirm($disable_id, $record_status);
            
         
            if ($query) {
              $data = array("success" => 1, 'message' => "Voucher  $record_text  successfully!");
              $this->audittrail->logActivity('Promotion', "Voucher Discount  $record_text  successfully.", 'Voucher', $this->session->userdata('username'));
            }else{
              $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
         
        generate_json($data);
      
     
      }



      public function delete_voucher($id= ""){


        $token_session = $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);
    
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['voucher_discount']['delete'] == 1){
                // start - data to be used for views
              $data_admin = array(
              'token' => $token,
              'type'  => 'New User',
          );
    
    
          $query = $this->model_promotion->voucher_delete($id);
            
          if ($query) {
            $data = array("success" => 1, 'message' => "Record Deleted successfully!");
            $this->audittrail->logActivity('Promotion', "Voucher Discount Deleted successfully.", 'add', $this->session->userdata('username'));
          }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
          }
              
          generate_json($data); 
      
    
        }
    
    
      
      }


      public function set_to_all(){

        $voucher_data = array(
        'voucher_id' => $this->input->post('voucher_id'),
        );
        $this->model_promotion->set_to_all($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);
   
      }
      
      public function  unset_to_all(){

        $voucher_data = array(
        'voucher_id' => $this->input->post('voucher_id'),
        );

        $this->model_promotion->unset_to_all($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);
   
      }

    public function sf_set_to_all(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );
        $this->model_promotion->sf_set_to_all($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    public function  sf_unset_to_all(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );

        $this->model_promotion->sf_unset_to_all($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    public function ct_set_to_all(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );
        $this->model_promotion->ct_set_to_all($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    public function  ct_unset_to_all(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );

        $this->model_promotion->ct_unset_to_all($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    //Discount Promo
    public function discount_promo($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['mystery_coupon']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            //$member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('promotion/discount_promo', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function discount_promo_list(){
        $this->isLoggedIn();
        $filters = [
            '_name' => $this->input->post('_name'),
            '_start_date' => $this->input->post('_start_date'),
            '_end_date' => $this->input->post('_end_date'),
        ];
        $query = $this->model_promotion->discount_promo_list($filters, $_REQUEST);
        generate_json($query);
    }

    public function discount_fee_byId($token = '',$id)
    {
        $token_session = $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['mystery_coupon']['view'] == 1) {
            
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = 13;

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_promotion->get_sys_shop($member_id),
                'shopcode'            => $this->model_promotion->get_shopcode($member_id),
                'shops'               => $this->model_promotion->get_shop_options(),
                'product_categories'  => $this->model_promotion->get_product_categories(),
                'campaign'            => $this->model_promotion->get_all_campaign_type(),
                'id'                  => $id,
                'detailsPromoId'      => $this->model_promotion->getPromoDetailsByPromoId($id)
            );

            //print_r($this->loginstate->get_access()['mystery_coupon']);

            $this->load->view('includes/header', $data_admin);
            $this->load->view('promotion/mystery_coupon_id', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function mc_fetch_productPromo_id(){
        $sys_shop = $this->input->post('select_shop');
        $id = $this->input->post('id');


        $data = array(
            'sys_shop'  => $sys_shop,
            'id'        => $id
        );

        $productArr = $this->model_promotion->mc_fetch_productPromo_id($data)->result_array();

        $response = [
            'success' => true,
            'productArr' => $productArr
        ];

        echo json_encode($response);
    }
    
    public function featured_campaign_list()
    {
        $campaign_type = $this->model_promotion->getCampaignType();
        echo json_encode($campaign_type);
    }

    public function confirm_featured_campaign()
    {
        $id = $this->input->post('id');

        $update_unset = $this->model_promotion->unsetFeaturedCampaign();
        if($update_unset) {
            $update_set = $this->model_promotion->setFeaturedCampaign($id);
            if ($update_set) {
                $data['success'] = true;
            } else {
                $data['success'] = false;
            }
        } else {
            $data['success'] = false;
        }

        echo json_encode($data);
    }

    public function ct_set_to_all_promo(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );
        $this->model_promotion->ct_set_to_all_promo($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    public function  ct_unset_to_all_promo(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );

        $this->model_promotion->ct_unset_to_all_promo($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    public function ct_set_shouldered_by(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );
        $this->model_promotion->ct_set_shouldered_by($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }

    public function  ct_unset_shouldered_by(){

        $voucher_data = array(
        'id' => $this->input->post('id'),
        );

        $this->model_promotion->ct_unset_shouldered_by($voucher_data);
        $response['success']  = 1;
        echo json_encode($response);

    }
}
