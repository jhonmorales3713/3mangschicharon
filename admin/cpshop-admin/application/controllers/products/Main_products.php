<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Main_products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('products/model_products');
        $this->load->model('adhoc_resize/Model_adhoc_resize');
        $this->load->library('uuid');
        $this->load->library('s3_resizeupload');
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function checkProductStatus($product_id)
    {
        $product_status = $this->model_products->checkProductStatus($product_id);
        $product_status = $product_status->enabled;

        if($product_status == 0){
            header("location:" . base_url('products/Main_products/display404'));
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

    public function delete_modal_confirm()
    {
        $this->isLoggedIn();

        $delete_id        = sanitize($this->input->post('delete_id'));
        $get_product      = $this->model_products->check_products($delete_id)->row();
        $checkOrderActive = $this->model_products->checkOrderActive($delete_id)->num_rows();

        if ($delete_id === 0) {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            $this->audittrail->logActivity('Product List', $get_product->itemname.' failed to delete.', "delete", $this->session->userdata('username'));
        }
        else if($checkOrderActive > 0){
            $data = array("success" => 0, 'message' => "Cannot delete ".$get_product->itemname." due to pending orders.");
        } 
        else {
            $query = $this->model_products->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Product deleted successfully!");
                $this->audittrail->logActivity('Product List', $get_product->itemname.' successfully deleted.', "delete", $this->session->userdata('username'));
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                $this->audittrail->logActivity('Product List', $get_product->itemname.' failed to delete.', "delete", $this->session->userdata('username'));
            }

        }

        generate_json($data);
    }

    public function disable_modal_confirm()
    {
        $this->isLoggedIn();

        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        } else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        } else {
            $record_status = 0;
        }
        // $checkOrderActive = $this->model_products->checkOrderActive($disable_id)->num_rows();
      
        // if($checkOrderActive > 0 && $record_status == 2){
        //     $get_product = $this->model_products->check_products($disable_id)->row();
        //     $data = array("success" => 0, 'message' => "Cannot disable ".$get_product->itemname." due to pending orders.");
        // } 
        // else
         if ($record_status > 0) {
            $query = $this->model_products->disable_modal_confirm($disable_id, $record_status);
            $get_product = $this->model_products->check_products($disable_id)->row();

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record " . $record_text . " successfully!");
                $this->audittrail->logActivity('Product List', $get_product->itemname.' has been successfully '.$record_text, $record_text, $this->session->userdata('username'));
            }
            else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                $this->audittrail->logActivity('Product List', 'Failed to '.$record_text.' '.$get_product->itemname, $record_text, $this->session->userdata('username'));
            }
        } else {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }

        generate_json($data);
    }

    public function products($token = '')
    {
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

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/products', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function add_products($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'featured_products'   => $this->model_products->getFeaturedProduct(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/add_products', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function add_variant($token = '', $parent_Id)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'parent_Id'           => $parent_Id,
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'featured_products'   => $this->model_products->getFeaturedProduct(),
                'get_parentProduct'   => $this->model_products->get_productdetails($parent_Id),
                'getVariants'         => $this->model_products->getVariants($parent_Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($parent_Id)->result_array(),
                'get_province'        => $this->model_products->get_province()
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/add_variant', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function update_products($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['update'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products->get_sys_shop($member_id);
            
            if($sys_shop == 0){
                $prev_product = $this->model_products->get_prev_product($this->model_products->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products->get_next_product($this->model_products->get_productdetails($Id)['itemname']);
            }else{
                $prev_product = $this->model_products->get_prev_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products->get_next_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'get_productdetails'  => $this->model_products->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products->get_sys_branch_profile($this->model_products->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'featured_products'   => $this->model_products->getFeaturedProduct(),
                'getVariants'         => $this->model_products->getVariants($Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($Id)->result_array()

            );
         
            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/update_products', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function update_variants($token = '', $Id, $parent_Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['update'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products->get_sys_shop($member_id);
            
            if($sys_shop == 0){
                $prev_product = $this->model_products->get_prev_product($this->model_products->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products->get_next_product($this->model_products->get_productdetails($Id)['itemname']);
            }else{
                $prev_product = $this->model_products->get_prev_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products->get_next_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'parent_Id'           => $parent_Id,
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'get_productdetails'  => $this->model_products->get_productdetails($Id),
                'get_parentProduct'   => $this->model_products->get_productdetails($parent_Id),
                'get_branchdetails'   => $this->model_products->get_sys_branch_profile($this->model_products->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'featured_products'   => $this->model_products->getFeaturedProduct(),
                'getVariants'         => $this->model_products->getVariants($parent_Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($parent_Id)->result_array(),
                'get_province'        => $this->model_products->get_province()
            );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/update_variants', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function view_products($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products->get_sys_shop($member_id);
            
            if($sys_shop == 0){
                $prev_product = $this->model_products->get_prev_product($this->model_products->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products->get_next_product($this->model_products->get_productdetails($Id)['itemname']);
            }else{
                $prev_product = $this->model_products->get_prev_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products->get_next_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'get_productdetails'  => $this->model_products->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products->get_sys_branch_profile($this->model_products->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'getVariants'         => $this->model_products->getVariants($Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($Id)->result_array()
            );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/view_products', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function product_table()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products->get_sys_shop($member_id);
        
        $query = $this->model_products->product_table($sys_shop, $request);
        
        
        generate_json($query);
    }

    public function export_product_table()
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

    
        
        $request = url_decode(json_decode($this->input->post("_search")));
        $member_id = $this->session->userdata('sys_users_id');
        $filter_text = "";
      
    //    print_r($request);
    //    die();

        $sys_shop = $this->model_products->get_sys_shop($member_id);
        
        if($sys_shop == 0) {
            $query = $this->model_products->product_table($sys_shop, $request, true);
        }else{
            $query = $this->model_products->product_table($sys_shop, $request, true);
        }

        // $_record_status = ($this->input->post('_record_status') == 1 && $this->input->post('_record_status') != '') ? "Enabled":"Disabled";

        if($this->input->post('_record_status') == ''){
            $_record_status = 'All Records';
        }
        else if($this->input->post('_record_status') == 1){
            $_record_status = 'Enabled';
        }
        else if($this->input->post('_record_status') == 2){
            $_record_status = 'Disabled';
        }
        else{
            $_record_status = '';
        }

		$_name 			= ($this->input->post('_name') == "") ? "":"'" . $this->input->post('_name') ."'";
        $_shops 		= ($this->input->post('_shops') == "") ? "All Shops":array_get($query, 'data.0.5');
        
        /// for details column in audit trail
        if($_name != ''){
            $filter_text .= $_record_status.' in '.$_shops. ', Product Name: '.$_name;
        }else{
            $filter_text .= $_record_status.' in '.$_shops;
        }
        
        $sheet->setCellValue('B1', "Products");
        $sheet->setCellValue('B2', "Filter: '$_name', $_record_status in $_shops");
        $sheet->setCellValue('B3', date('Y/m/d'));
        
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->setCellValue('A6', 'Product Name');
        $sheet->setCellValue('B6', 'Category');
        $sheet->setCellValue('C6', 'Price');
        $sheet->setCellValue('D6', 'No of Stock');
        $sheet->setCellValue('E6', 'Shop Name');
        $sheet->setCellValue('F6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);

        // print_r($query);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[1],
                '2' => $row[2],
                '3' => $row[3],
                '4' => $row[4],
                '5' => ucwords($row[5]),
                '6' => ucwords($row[6])
            );
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Products ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Product List', 'Products has been exported into excel with filter '.$filter_text, 'export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function save_product()
    {
     

        
        $f_id      = $this->uuid->v4_formatted();
        $file_name = $f_id;
        $imgArr    = [];

        // variants validation if empty
        $variants_isset  = sanitize($this->input->post('f_variants_isset'));
        $var_option_name = $this->input->post('f_var_option_name');
        $var_option_list = $this->input->post('f_var_option_list');
        $variant_checker = 0;

        if($variants_isset == 1){
            if($var_option_name[0] == "" && $var_option_list[0] != ""){
                $variant_checker = 1;
            }
            else if($var_option_name[0] != "" && $var_option_list[0] == ""){
                $variant_checker = 1;
            }
            if($var_option_name[1] == "" && $var_option_list[1] != ""){
                $variant_checker = 1;
            }
            else if($var_option_name[1] != "" && $var_option_list[1] == ""){
                $variant_checker = 1;
            }
            if($var_option_name[2] == "" && $var_option_list[2] != ""){
                $variant_checker = 1;
            }
            else if($var_option_name[2] != "" && $var_option_list[2] == ""){
                $variant_checker = 1;
            }
        }


        if($variant_checker == 1){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Please complete variants field.'
            ];
            echo json_encode($response);
            die();
        }
     
        $this->load->library('upload');
        $this->load->library('s3_upload');
        $count_upload = count($_FILES['product_image']['name']);
        $reorder_image   = $this->input->post('reorder_image');

        if(empty($this->input->post('f_member_shop')) || empty($this->input->post('f_category'))){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'No shop or category selected.'
            ];

            echo json_encode($response);
            die();
        }

        if(in_array("", $_FILES['product_image']['name'])){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Please upload at least one image.'
            ];

            echo json_encode($response);
            die();
        }

        /// upload product images
        if($count_upload > 6){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Image number of upload exceeded. Max:6'
            );
            echo json_encode($response);
            die();
        }else if($count_upload == 0){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'No attached image.'
            );
            echo json_encode($response);
            die();
        }else{
            if($this->input->post('f_member_shop') == ''){
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'Please select a shop.'
                );
                echo json_encode($response);
                die();
            }

            $shopcode = $this->model_products->get_shopcode_via_shopid($this->input->post('f_member_shop'));

            // $this->makedirImage($f_id, $shopcode);

            $directory    = 'assets/img';
            $s3_directory = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';
           
            foreach($reorder_image as $val){
                for($i = 0; $i < $count_upload; $i++) { 
                    if($val == $_FILES['product_image']['name'][$i]){
                        $_FILES['userfile']['name']     = $_FILES['product_image']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['product_image']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['product_image']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['product_image']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['product_image']['size'][$i];

                        $file_name   = $i.'-'.$f_id;
                        $file_names3 = $i.'-'.$f_id;
                        
                        $config = array(
                            'file_name'     => $file_name,
                            'allowed_types' => '*',
                            'max_size'      => 20000,
                            'overwrite'     => FALSE,
                            'min_width'     => '1',
                            'min_height'     => '1',
                            'upload_path'
                            =>  $directory
                        );

                        $this->upload->initialize($config);
                        if ( ! $this->upload->do_upload()) {
                            $error = array('error' => $this->upload->display_errors());
                            $response = array(
                                'status'      => false,
                                'environment' => ENVIRONMENT,
                                'message'     => $error['error']
                            );
                            echo json_encode($response);
                            die();
                        }else {
                        
                            $file_name = $this->upload->data()['file_name'];
                            // do the resizes
                            // $this->adhoc_product_curl($shopcode, $f_id, $file_name);

                            $imgArr[] = $file_name;

                            ///upload image to s3 bucket
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $activityContent = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_name;
                            $uploadS3 = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);
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
                            //// upload resized images to diff folder
                            $getOrigimageDim = getimagesize($_FILES['userfile']['tmp_name']);
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $s3_directory    = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';
                            $activityContent = $s3_directory.$file_names3.'.jpg';
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '40', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '50', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '250', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '520', $getOrigimageDim);
                            
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
                }
            } 
        }

        $validation = array(
            array('f_member_shop','Shop Name','required|max_length[100]|min_length[1]'),
            array('f_category','Category','required|max_length[100]|min_length[1]'),
            array('f_itemname','Product Name','required|max_length[255]|min_length[2]'),
            // array('f_otherinfo','Other Info','required|max_length[100]|min_length[1]'),
            array('f_price','Price','required|max_length[10]|min_length[1]'),
    	);
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
        
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => $this->response->message('error')
        ];

        $check_itemid = $this->model_products->check_products_itemid($this->input->post('f_itemid'), $this->input->post('f_member_shop'));

        if($check_itemid->num_rows() > 0 && $this->input->post('f_itemid') != ''){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Referral Commission ItemID already exists.'
            ];

            echo json_encode($response);
            die();
        }

        if($this->input->post('f_compare_at_price') > $this->input->post('f_price') || $this->input->post('f_compare_at_price') == 0){

        }else{
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Compared at Price should be greater than the original price'
            ];

            echo json_encode($response);
            die();
        }


    	if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
    	}else{
            $featured_product =  $this->input->post('featured_prod_isset');
            $featured_product_arrangment =  $this->input->post('entry-feat-product-arrangement');
    		$success = $this->model_products->save_product($this->input->post(), $f_id, $imgArr,$featured_product,$featured_product_arrangment);
            if($variants_isset == 1){
                $variants_count = count($var_option_name);
                for($i = 0; $i < $variants_count; $i++) { 
                    $this->model_products->save_variantsummary($f_id, $var_option_name[$i], $var_option_list[$i], $i);
                }
                $variant_name    = $this->input->post('variant_name');
                $variant_price   = $this->input->post('variant_price');
                $variant_sku     = $this->input->post('variant_sku');
                $variant_barcode = $this->input->post('variant_barcode');
                $variant_counter = count($variant_name);

                for($i = 0; $i < $variant_counter; $i++) { 
                    if($variant_price[$i] != ''){
                        $child_product_id = $this->uuid->v4_formatted();
                        $this->model_products->save_variants($f_id, $child_product_id, $variant_name[$i], $variant_price[$i], $variant_sku[$i], $variant_barcode[$i], $this->input->post(), $variants_isset);
                    }
                }
            }
            
            $Get_app_member_details = $this->model_products->getAppMember($this->session->userdata('id'));
            $Get_shop_details = $this->model_products->getSysShopsDetails($this->input->post('f_member_shop'));
            $Get_email_settings = $this->model_products->get_email_settings();

            $data_email = array(
                'itemname'               => $this->input->post('f_itemname'),
                'shopname'               => $Get_shop_details[0]['shopname'],
                'new_product_email'      =>  $Get_email_settings[0]['new_product_email'],
                'new_product_name'       =>  $Get_email_settings[0]['new_product_name'],
                'approval_email'         => $Get_email_settings[0]['approval_product_email'],
                'approval_name'          => $Get_email_settings[0]['approval_product_name'],
            );
            
            $this->sendProductNewlyCreatedEmail($data_email);
            $this->sendProductForApprovalEmail($data_email);

            $response['success']    = $success;
            $response['message']    = "Product created successfully.";
            $response['product_id'] = $f_id;
            $this->audittrail->logActivity('Product List', $this->input->post('f_itemname').' successfully added to Products.', 'add', $this->session->userdata('username'));
        }
        echo json_encode($response);

    }

     //send email to maam mia every new created  product
    public function sendProductNewlyCreatedEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['new_product_email']);
        $this->email->subject(get_company_name()." | New Product Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/products_newlycreated_template", $data, TRUE));
        $this->email->send();
    }



    public function save_variant()
    {
        $f_id      = $this->uuid->v4_formatted();
        $file_name = $f_id;
        $imgArr    = [];

        $this->load->library('upload');
        $this->load->library('s3_upload');
        $count_upload = count($_FILES['product_image']['name']);
        $reorder_image    = $this->input->post('reorder_image');
        $f_delivery_areas = $this->input->post('f_delivery_areas');

        if($this->input->post('f_member_shop') == ""){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'No shop or category selected.'
            ];

            echo json_encode($response);
            die();
        }

        if(in_array("", $_FILES['product_image']['name'])){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Please upload at least one image.'
            ];

            echo json_encode($response);
            die();
        }

        /// upload product images
        if($count_upload > 6){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Image number of upload exceeded. Max:6'
            );
            echo json_encode($response);
            die();
        }else if($count_upload == 0){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'No attached image.'
            );
            echo json_encode($response);
            die();
        }else{
            if($this->input->post('f_member_shop') == ''){
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'Please select a shop.'
                );
                echo json_encode($response);
                die();
            }

            $shopcode = $this->model_products->get_shopcode_via_shopid($this->input->post('f_member_shop'));

            // $this->makedirImage($f_id, $shopcode);

            $directory    = 'assets/img';
            $s3_directory = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';
           
            foreach($reorder_image as $val){
                for($i = 0; $i < $count_upload; $i++) { 
                    if($val == $_FILES['product_image']['name'][$i]){
                        $_FILES['userfile']['name']     = $_FILES['product_image']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['product_image']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['product_image']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['product_image']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['product_image']['size'][$i];

                        $file_name   = $i.'-'.$f_id;
                        $file_names3 = $i.'-'.$f_id;
                        
                        $config = array(
                            'file_name'     => $file_name,
                            'allowed_types' => '*',
                            'max_size'      => 20000,
                            'overwrite'     => FALSE,
                            'min_width'     => '1',
                            'min_height'     => '1',
                            'upload_path'
                            =>  $directory
                        );

                        $this->upload->initialize($config);
                        if ( ! $this->upload->do_upload()) {
                            $error = array('error' => $this->upload->display_errors());
                            $response = array(
                                'status'      => false,
                                'environment' => ENVIRONMENT,
                                'message'     => $error['error']
                            );
                            echo json_encode($response);
                            die();
                        }else {
                        
                            $file_name = $this->upload->data()['file_name'];
                            // do the resizes
                            // $this->adhoc_product_curl($shopcode, $f_id, $file_name);

                            $imgArr[] = $file_name;

                            ///upload image to s3 bucket
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $activityContent = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_name;
                            $uploadS3 = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);
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
                            //// upload resized images to diff folder
                            $getOrigimageDim = getimagesize($_FILES['userfile']['tmp_name']);
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $s3_directory    = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';
                            $activityContent = $s3_directory.$file_names3.'.jpg';
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '40', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '50', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '250', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '520', $getOrigimageDim);
                            
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
                }
            } 
        }

        $validation = array(
            array('f_member_shop','Shop Name','required|max_length[100]|min_length[1]'),
            array('f_itemname','Product Name','required|max_length[255]|min_length[2]'),
            array('f_price','Price','required|max_length[10]|min_length[1]'),
    	);
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
        
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => $this->response->message('error')
        ];

        $check_itemid = $this->model_products->check_products_itemid($this->input->post('f_itemid'), $this->input->post('f_member_shop'));
        $parent_id_details=$this->model_products->get_productdetails($this->input->post('f_parent_product_id'));
        
        if($check_itemid->num_rows() > 0 && $this->input->post('f_itemid') != ''){
            if($check_itemid->result_array()[0]['itemid']!=$parent_id_details['itemid']){
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => 'Referral Commission ItemID already exists.'
                ];

                echo json_encode($response);
                die();
            }
        }

        if($this->input->post('f_compare_at_price') > $this->input->post('f_price') || $this->input->post('f_compare_at_price') == 0){

        }else{
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Compared at Price should be greater than the original price'
            ];

            echo json_encode($response);
            die();
        }


        $Get_shop_details = $this->model_products->getSysShopsDetails($this->input->post('f_member_shop'));
        $Get_app_member_details = $this->model_products->getAppMember($this->session->userdata('id'));
        $Get_parent_product     = $this->model_products->getParentProduct($this->input->post('f_parent_product_id'));
        $Get_email_settings = $this->model_products->get_email_settings();

        $data_email = array(
            'variant_name'           => $this->input->post('f_itemname'),
            'itemname'               => $Get_parent_product[0]['itemname'],
            'shopname'               => $Get_shop_details[0]['shopname'],
            'new_product_email'      =>  $Get_email_settings[0]['new_product_email'],
            'new_product_name'       =>  $Get_email_settings[0]['new_product_name'],
            'approval_email'         => $Get_email_settings[0]['approval_product_email'],
            'approval_name'          => $Get_email_settings[0]['approval_product_name'],
        );
        $this->sendProductVariantNewlyCreatedEmail($data_email);
        $this->sendProductVariantForApprovalEmail($data_email);


    	if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
    	}else{
            $delivery_areas_str = "";
            if(!empty($f_delivery_areas)){
                foreach($f_delivery_areas AS $row) {
                    $delivery_areas_str .= $row.", ";
                }
                $delivery_areas_str = rtrim($delivery_areas_str, ', ');
            }else{
                
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'Delivery Area field must be filled at least 1.'
                );
                echo json_encode($response);
                die();
            }
         
            $featured_product =  $this->input->post('featured_prod_isset');
            $featured_product_arrangment =  $this->input->post('entry-feat-product-arrangement');
    		$success = $this->model_products->save_variant($this->input->post(), $f_id, $imgArr,$featured_product,$featured_product_arrangment, $delivery_areas_str);
            $this->model_products->updateParentProductInventoryQty($this->input->post('f_parent_product_id'));
            $response['success'] = $success;
            $response['message'] = "Variant created successfully.";
            $response['parent_product_id'] = $this->input->post('f_parent_product_id');
            $this->audittrail->logActivity('Product List - Variant', $this->input->post('f_itemname').' successfully added to Products.', 'add', $this->session->userdata('username'));
        }
        echo json_encode($response);
        
    }

            //send email to maam mia every new created  product variant
        public function sendProductVariantNewlyCreatedEmail($data){

            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data['new_product_email']);
            $this->email->subject(get_company_name()." | New Product - Variant Notification");
            $data['data']          = $data;
            $this->email->message($this->load->view("includes/emails/products_varirants_newlycreated_template", $data, TRUE));
            $this->email->send();
        }


        //send email to  Ms. Hope every new approve product variant
        public function sendProductVariantForApprovalEmail($data){

            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data['approval_email']);
            $this->email->subject(get_company_name()." |  Product Approval Notification");
            $data['data']          = $data;
            $this->email->message($this->load->view("includes/emails/products__variant_forapproval_template", $data, TRUE));
            $this->email->send();
        }


    public function update_product()
    {

        
        if(!empty($this->input->post('current_product_url'))){
            $file_name = $this->input->post('f_id');
        } else {
            $file_name = "";
        }
        $f_id      = $this->input->post('f_id');
        $imgArr    = [];

        // variants validation if empty
        $variants_isset  = sanitize($this->input->post('f_variants_isset'));
        $var_option_name = $this->input->post('f_var_option_name');
        $var_option_list = $this->input->post('f_var_option_list');
        $variant_checker = 0;

        $if_have_uploaded_image = $_FILES['product_image']['tmp_name'][0];
        
        //if there is a file, upload it first and take note of the file name
        $reorder_image   = $this->input->post('reorder_image');
        $image_changes   = $this->input->post('productimage_changes');
        $prev_image_name = $this->input->post('prev_image_name');
        $prev_image      = $this->input->post('prev_image_name_noformat');
        $prev_image_40   = 1;
        $prev_image_50   = 1;
        $prev_image_250  = 1;
        $prev_image_520  = 1;
        $already_upload  = 0;
        $upload_checker  = $this->input->post('upload_checker');
        $upload_string   = "\n";
        $this->load->library('s3_upload');

        if(empty($this->input->post('f_member_shop')) || empty($this->input->post('f_category'))){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'No shop or category selected.'
            ];

            echo json_encode($response);
            die();
        }

        if($upload_checker === '0'){
            $this->load->library('upload');
            $count_upload = count($_FILES['product_image']['name']);
            
            if($count_upload > 6){
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'Image number of upload exceeded. Max:6',
                );
                echo json_encode($response);
                die();

            }else if($count_upload == 0){
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'No attached image.',
                );
                echo json_encode($response);
                die();
            }else{

                $shopcode = $this->model_products->get_shopcode_via_shopid($this->input->post('f_member_shop'));

                // $this->makedirImage($f_id, $shopcode);

                if ($if_have_uploaded_image != "") {
                    error_reporting(0);
                    // unlink image
                    $this->unlinkImage($f_id, $shopcode, $prev_image_name, $prev_image);
                    // rearrange image
                    $rearrange = $this->rearrangeImage($f_id, $shopcode, $reorder_image, $prev_image, $imgArr);
                    $imgArr = $rearrange['imgArr'];
                    $count  = $rearrange['count'] + 1;

                    $directory    = 'assets/img';
                    $s3_directory = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';

                    for($i = 0; $i < $count_upload; $i++) { 

                        $_FILES['userfile']['name']     = $_FILES['product_image']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['product_image']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['product_image']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['product_image']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['product_image']['size'][$i];

                        $file_name   = ($i + $count).'-'.$f_id;
                        $file_names3 = ($i + $count).'-'.$f_id;

                        $config = array(
                            'file_name'     => $file_name,
                            'allowed_types' => '*',
                            'max_size'      => 20000,
                            'overwrite'     => TRUE,
                            'min_width'     => '1',
                            'min_height'     => '1',
                            'upload_path'
                            =>  $directory
                        );

                        $this->upload->initialize($config);

                        //uploading
                        if ( ! $this->upload->do_upload()) {
                            $error = array('error' => $this->upload->display_errors());
                            $response = array(
                                'status'      => false,
                                'environment' => ENVIRONMENT,
                                'message'     => $error['error'],
                            );

                            $prev_image_name = $this->input->post('prev_image_name');
                            $prev_image      = $this->input->post('prev_image_name_noformat');
                            $prev_image_40   = 1;
                            $prev_image_50   = 1;
                            $prev_image_250  = 1;
                            $prev_image_520  = 1;

                            echo json_encode($response);
                            die();
                        }else {
                            $already_upload = 1;
                            $file_name = $this->upload->data()['file_name'];
                            // do the resizes
                            // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').$file_name, $shopcode);
                            // $this->adhoc_product_curl($shopcode, $f_id, $file_name);
                            $imgArr[] = $file_name;

                            ///upload image to s3 bucket
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $activityContent = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_name;
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
                            //// upload resized images to diff folder
                            $getOrigimageDim = getimagesize($_FILES['userfile']['tmp_name']);
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $s3_directory    = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';
                            $activityContent = $s3_directory.$file_names3.'.jpg';
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '40', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '50', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '250', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '520', $getOrigimageDim);
                            
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
                    $upload_string .= $count_upload." image(s) uploaded. \n";
                } 

            }  

        }

        $shopcode = $this->model_products->get_shopcode_via_shopid($this->input->post('f_member_shop'));
        if($already_upload == 0 && $image_changes == 1){
            error_reporting(0);
             // unlink image
            $this->unlinkImage($f_id, $shopcode, $prev_image_name, $prev_image);
            // rearrange image
            $rearrange = $this->rearrangeImage($f_id, $shopcode, $reorder_image, $prev_image, $imgArr);
            $imgArr = $rearrange['imgArr'];
            $count  = $rearrange['count'];

            $upload_string .= count($prev_image_name)." image(s) deleted. \n";

        }

        error_reporting(1);
        $id = $this->input->post('f_id');
        $is_unique = '';
        if($this->model_products->get_productdetails($id)['itemname'] != $this->input->post('f_itemname')){
            $is_unique = '|is_unique[sys_products.itemname]';
        }
		$validation = array(
            array('f_member_shop','Shop Name','required|max_length[100]|min_length[1]'),
            array('f_category','Category','required|max_length[100]|min_length[1]'),
            array('f_itemname','Product Name','required|max_length[255]|min_length[2]'),
            // array('f_otherinfo','Other Info','required|max_length[100]|min_length[1]'),
            array('f_price','Price','required|max_length[10]|min_length[1]'),
        );
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }

        if($id == ''){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => $this->response->message('error')
            ];

            echo json_encode($response);
            die();
        }

        $check_itemid = $this->model_products->check_products_itemid($this->input->post('f_itemid'), $this->input->post('f_member_shop'));
        $check_id = $this->model_products->check_products_id($id);
        // $this->db = $this->load->database('vouchers', TRUE);
        // $check_itemid_referralcom_rate = $this->model_products->check_referralcom_rate($shopcode, $this->input->post('f_itemid'));
        $this->db = $this->load->database('default', TRUE);


        if($check_itemid->num_rows() > 0 && $check_id->row()->itemid != $this->input->post('f_itemid') && $this->input->post('f_itemid') != ''){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Referral Commission ItemID already exists.'
            ];

            echo json_encode($response);
            die();
        }

        // if($check_itemid_referralcom_rate->num_rows() > 0 && $check_id->row()->itemid != $this->input->post('f_itemid')){
        //     $response = [
        //         'environment' => ENVIRONMENT,
        //         'success'     => false,
        //         'message'     => 'Referral Commission ItemID already exists.'
        //     ];

        //     echo json_encode($response);
        //     die();
        // }

        if($this->input->post('f_compare_at_price') > $this->input->post('f_price') || $this->input->post('f_compare_at_price') == 0){
            $current_promo_price = $check_id->row()->promo_price;
            if($current_promo_price != $this->input->post('f_compare_at_price')){
                $save_promo_log = 1;
            }else{
                $save_promo_log = 0;
            }
        }else{
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Compared at Price should be greater than the original price'
            ];

            echo json_encode($response);
            die();
        }

        if($this->form_validation->run() == FALSE){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => validation_errors(),
                'message'     => $this->response->message('error')
            ];

            echo json_encode($response);
            die();
    	}else{
            // $prev_nostock = $this->model_products->getProductTotalStocks($id)->row()->no_of_stocks;
            $get_product = $this->model_products->check_products($id)->row();
            $featured_product =  $this->input->post('featured_prod_isset');
            $featured_product_arrangment =  $this->input->post('entry-feat-product-arrangement');
            $main = $this->model_products->update_product($this->input->post(), $id, $save_promo_log, $imgArr,$featured_product, $featured_product_arrangment);    

            if($variants_isset == 1){
                $variants_count = count($var_option_name);
                for($i = 0; $i < $variants_count; $i++) { 
                   
                    $this->model_products->update_variantsummary($id, $var_option_name[$i], $var_option_list[$i], $i);
                    if($var_option_name[$i] != ''){
                        $this->audittrail->logActivity('Product List - Variant Options: ', $var_option_name[$i].' and '.$var_option_list[$i].' from '.$get_product->itemname.' has been updated successfully.', 'update', $this->session->userdata('username'));
                    }
                }
                $variant_id      = $this->input->post('variant_id');
                $variant_name    = $this->input->post('variant_name');
                $variant_price   = $this->input->post('variant_price');
                $variant_sku     = $this->input->post('variant_sku');
                $variant_status  = $this->input->post('variant_status');
                $variant_counter = count($variant_id);

                for($i = 0; $i < $variant_counter; $i++) { 
                    $this->model_products->update_variants($id, $variant_id[$i], $variant_name[$i], $variant_price[$i], $variant_sku[$i], $variant_status[$i]);
                    $this->audittrail->logActivity('Product List - Variant', $variant_name[$i].' Variant from '.$get_product->itemname.' has been updated successfully.', 'update', $this->session->userdata('username'));
                }
                
                $this->model_products->updateParentProductInventoryQty($id);
            }
            $deletedVariants = $this->input->post('deletedVariants');
            $deletedVariants = explode(",", $deletedVariants);
            if(!empty(($deletedVariants))){
                foreach($deletedVariants as $row){
                    $get_productdetails = $this->model_products->get_productdetails($row);
                    if($row != ''){
                        $this->model_products->deleteVariant($row);
                        $this->audittrail->logActivity('Product List - Variant', 'Variant '.$get_productdetails['itemname'].' from '.$get_product->itemname.' has been successfully deleted.', 'delete', $this->session->userdata('username'));
                    }
                }
            }
            // $new_nostock = $this->model_products->getProductTotalStocks($id)->row()->no_of_stocks;
            // $this->db = $this->load->database('vouchers', TRUE);
            // $success = $this->model_products->update_product_refcommrate($this->input->post(), $id, $shopcode);
            $this->db = $this->load->database('default', TRUE);
            $response['success'] = true;
            $response['message'] = "Product updated successfully.";
            if(!empty($prev_image_name) || $count_upload > 0){
                $main .= $upload_string;
            }


            $Get_app_member_details = $this->model_products->getAppMember($this->session->userdata('id'));
            $Get_email_settings = $this->model_products->get_email_settings();
            $data_email = array(
                'itemname'               => $get_product->itemname,
                'fname'                  => $Get_app_member_details[0]['fname'],
                'lname'                  =>  $Get_app_member_details[0]['lname'],
                'approval_email'         => $Get_email_settings[0]['approval_product_email'],
                'approval_name'          => $Get_email_settings[0]['approval_product_name'],
            );
            $this->sendProductForApprovalEmail($data_email);

            $main = (strpos($main, 'into') !== false) ? $main : 'None';
            $this->audittrail->logActivity('Product List', $get_product->itemname.' has been updated successfully. Changes: '.$main, 'update', $this->session->userdata('username'));
        
            // $wishlist = (floatval($prev_nostock) <= floatval(0) && floatval($new_nostock) > floatval(0)) ? $this->emailWishlist($id) : '';
            // if($wishlist != ''){
            //     exec($wishlist);
            // }
            
        }   
        echo json_encode($response);
    }


    public function sendProductForApprovalEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['approval_email']);
        $this->email->subject(get_company_name()." |  Product Approval Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/products__forapproval_template", $data, TRUE));
        $this->email->send();
    }


    public function update_variant()
    {

        
        if(!empty($this->input->post('current_product_url'))){
            $file_name = $this->input->post('f_id');
        } else {
            $file_name = "";
        }
        $f_id             = $this->input->post('f_id');
        $imgArr           = [];

        $if_have_uploaded_image = $_FILES['product_image']['tmp_name'][0];
        
        //if there is a file, upload it first and take note of the file name
        $reorder_image    = $this->input->post('reorder_image');
        $image_changes    = $this->input->post('productimage_changes');
        $prev_image_name  = $this->input->post('prev_image_name');
        $prev_image       = $this->input->post('prev_image_name_noformat');
        $prev_image_40    = 1;
        $prev_image_50    = 1;
        $prev_image_250   = 1;
        $prev_image_520   = 1;
        $already_upload   = 0;
        $upload_checker   = $this->input->post('upload_checker');
        $upload_string    = "\n";
        $f_delivery_areas = $this->input->post('f_delivery_areas');


        $this->load->library('s3_upload');

        if($this->input->post('f_member_shop') == ""){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'No shop or category selected.'
            ];

            echo json_encode($response);
            die();
        }

        if($upload_checker === '0'){
            $this->load->library('upload');
            $count_upload = count($_FILES['product_image']['name']);
            
            if($count_upload > 6){
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'Image number of upload exceeded. Max:6',
                );
                echo json_encode($response);
                die();

            }else if($count_upload == 0){
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'No attached image.',
                );
                echo json_encode($response);
                die();
            }else{

                $shopcode = $this->model_products->get_shopcode_via_shopid($this->input->post('f_member_shop'));

                // $this->makedirImage($f_id, $shopcode);

                if ($if_have_uploaded_image != "") {
                    error_reporting(0);
                    // unlink image
                    $this->unlinkImage($f_id, $shopcode, $prev_image_name, $prev_image);
                    // rearrange image
                    $rearrange = $this->rearrangeImage($f_id, $shopcode, $reorder_image, $prev_image, $imgArr);
                    $imgArr = $rearrange['imgArr'];
                    $count  = $rearrange['count'] + 1;

                    $directory    = 'assets/img';
                    $s3_directory = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';

                    for($i = 0; $i < $count_upload; $i++) { 

                        $_FILES['userfile']['name']     = $_FILES['product_image']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['product_image']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['product_image']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['product_image']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['product_image']['size'][$i];

                        $file_name   = ($i + $count).'-'.$f_id;
                        $file_names3 = ($i + $count).'-'.$f_id;

                        $config = array(
                            'file_name'     => $file_name,
                            'allowed_types' => '*',
                            'max_size'      => 20000,
                            'overwrite'     => TRUE,
                            'min_width'     => '1',
                            'min_height'     => '1',
                            'upload_path'
                            =>  $directory
                        );

                        $this->upload->initialize($config);

                        //uploading
                        if ( ! $this->upload->do_upload()) {
                            $error = array('error' => $this->upload->display_errors());
                            $response = array(
                                'status'      => false,
                                'environment' => ENVIRONMENT,
                                'message'     => $error['error'],
                            );

                            $prev_image_name = $this->input->post('prev_image_name');
                            $prev_image      = $this->input->post('prev_image_name_noformat');
                            $prev_image_40   = 1;
                            $prev_image_50   = 1;
                            $prev_image_250  = 1;
                            $prev_image_520  = 1;

                            echo json_encode($response);
                            die();
                        }else {
                            $already_upload = 1;
                            $file_name = $this->upload->data()['file_name'];
                            // do the resizes
                            // $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').$file_name, $shopcode);
                            // $this->adhoc_product_curl($shopcode, $f_id, $file_name);
                            $imgArr[] = $file_name;

                            ///upload image to s3 bucket
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $activityContent = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_name;
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
                            //// upload resized images to diff folder
                            $getOrigimageDim = getimagesize($_FILES['userfile']['tmp_name']);
                            $fileTempName    = $_FILES['userfile']['tmp_name'];
                            $s3_directory    = 'assets/img/'.$shopcode.'/products/'.$f_id.'/';
                            $activityContent = $s3_directory.$file_names3.'.jpg';
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '40', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '50', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '250', $getOrigimageDim);
                            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, '520', $getOrigimageDim);
                            
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
                    $upload_string .= $count_upload." image(s) uploaded. \n";
                } 

            }  

        }

        $shopcode = $this->model_products->get_shopcode_via_shopid($this->input->post('f_member_shop'));
        if($already_upload == 0 && $image_changes == 1){
            error_reporting(0);
             // unlink image
            $this->unlinkImage($f_id, $shopcode, $prev_image_name, $prev_image);
            // rearrange image
            $rearrange = $this->rearrangeImage($f_id, $shopcode, $reorder_image, $prev_image, $imgArr);
            $imgArr = $rearrange['imgArr'];
            $count  = $rearrange['count'];

            $upload_string .= count($prev_image_name)." image(s) deleted. \n";

        }

        error_reporting(1);
        $id = $this->input->post('f_id');
        $is_unique = '';
        if($this->model_products->get_productdetails($id)['itemname'] != $this->input->post('f_itemname')){
            $is_unique = '|is_unique[sys_products.itemname]';
        }
		$validation = array(
            array('f_member_shop','Shop Name','required|max_length[100]|min_length[1]'),
            array('f_itemname','Product Name','required|max_length[255]|min_length[2]'),
            array('f_price','Price','required|max_length[10]|min_length[1]'),
        );
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }

        if($id == ''){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => $this->response->message('error')
            ];

            echo json_encode($response);
            die();
        }

        $check_itemid = $this->model_products->check_products_itemid($this->input->post('f_itemid'), $this->input->post('f_member_shop'));
        $check_id = $this->model_products->check_products_id($id);
        // $this->db = $this->load->database('vouchers', TRUE);
        // $check_itemid_referralcom_rate = $this->model_products->check_referralcom_rate($shopcode, $this->input->post('f_itemid'));
        $this->db = $this->load->database('default', TRUE);

        // Modified
        if($check_itemid->num_rows() > 0 && $check_id->row()->itemid != $this->input->post('f_itemid') && $this->input->post('f_itemid') != '' && $this->input->post('f_parent_product_id ') != $check_id->row()->itemid){
            $check_parentid = $this->model_products->check_products_id($check_id->row()->parent_product_id);
            if(($check_parentid->row()->itemid != $this->input->post('f_itemid') && ini() == 'jcww') || ini() != 'jcww'){
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => 'Referral Commission ItemID already exists.'
                ];    
                echo json_encode($response);
                die();
            }
        }

        // if($check_itemid_referralcom_rate->num_rows() > 0 && $check_id->row()->itemid != $this->input->post('f_itemid')){
        //     $response = [
        //         'environment' => ENVIRONMENT,
        //         'success'     => false,
        //         'message'     => 'Referral Commission ItemID already exists.'
        //     ];

        //     echo json_encode($response);
        //     die();
        // }

        if($this->input->post('f_compare_at_price') > $this->input->post('f_price') || $this->input->post('f_compare_at_price') == 0){
            $current_promo_price = $check_id->row()->promo_price;
            if($current_promo_price != $this->input->post('f_compare_at_price')){
                $save_promo_log = 1;
            }else{
                $save_promo_log = 0;
            }
        }else{
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'Compared at Price should be greater than the original price'
            ];

            echo json_encode($response);
            die();
        }

        if($this->form_validation->run() == FALSE){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => validation_errors(),
                'message'     => $this->response->message('error')
            ];

            echo json_encode($response);
            die();
    	}else{

            $delivery_areas_str = "";
            if(!empty($f_delivery_areas)){
                foreach($f_delivery_areas AS $row) {
                    $delivery_areas_str .= $row.", ";
                }
                $delivery_areas_str = rtrim($delivery_areas_str, ', ');
            }else{
                
                $response = array(
                    'success'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => 'Delivery Area field must be filled at least 1.'
                );
                echo json_encode($response);
                die();
                
            }

            // $prev_nostock = $this->model_products->getProductTotalStocks($id)->row()->no_of_stocks;
            $featured_product =  $this->input->post('featured_prod_isset');
            $featured_product_arrangment =  $this->input->post('entry-feat-product-arrangement');
            $main = $this->model_products->update_variant($this->input->post(), $id, $save_promo_log, $imgArr,$featured_product,$featured_product_arrangment, $delivery_areas_str);
            // $new_nostock = $this->model_products->getProductTotalStocks($id)->row()->no_of_stocks;
            // $this->db = $this->load->database('vouchers', TRUE);
            // $success = $this->model_products->update_product_refcommrate($this->input->post(), $id, $shopcode);
            $this->db = $this->load->database('default', TRUE);
            // $this->model_products->updateParentProductInventoryQty($this->input->post('f_parent_product_id'));
            $response['success'] = true;
            $response['message'] = "Variant updated successfully.";
            $get_product = $this->model_products->check_products($id)->row();
            if(!empty($prev_image_name) || $count_upload > 0){
                $main .= $upload_string;
            }
            $main = (strpos($main, 'into') !== false) ? $main : 'None';

            $Get_app_member_details = $this->model_products->getAppMember($this->session->userdata('id'));
            $Get_parent_product     = $this->model_products->getParentProduct($this->input->post('f_parent_product_id'));
            $Get_email_settings = $this->model_products->get_email_settings();
            $data_email = array(
                'variant_name'           => $this->input->post('f_itemname'),
                'itemname'               => $Get_parent_product[0]['itemname'],
                'fname'                  => $Get_app_member_details[0]['fname'],
                'lname'                  => $Get_app_member_details[0]['lname'],
                'approval_email'         => $Get_email_settings[0]['approval_product_email'],
                'approval_name'          => $Get_email_settings[0]['approval_product_name'],
            );
            $this->sendProductVariantForApprovalEmail($data_email);

            $this->audittrail->logActivity('Product List - Variant', $get_product->itemname.' has been updated successfully. Changes: '.$main, 'update', $this->session->userdata('username'));
        
            // $wishlist = (floatval($prev_nostock) <= floatval(0) && floatval($new_nostock) > floatval(0)) ? $this->emailWishlist($id) : '';
            // if($wishlist != ''){
            //     exec($wishlist);
            // }
            
        }   
        echo json_encode($response);
    }

    

    public function adhoc_product_curl($shopcode, $f_id, $file_name) 
    {
        $url = base_url()."products/main_products/submit_adhoc_product_processing/";
            
        $fields = array(
            'shopcode'   => $shopcode,
            'f_id'       => $f_id,
            'file_name'  => $file_name
        );
        $postvars = http_build_query($fields);
        // open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'api');
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        // execute post

        curl_exec($ch);
        curl_close($ch);
    }

    public function submit_adhoc_product_processing()
    {
        $shopcode   = $this->input->post('shopcode');
        $f_id       = $this->input->post('f_id');
        $file_name  = $this->input->post('file_name');

        $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').$file_name, $shopcode);
    }

    public function get_productdetails($id)
    {

        $row = $this->model_products->get_productdetails($id);
        $response = [
            'success' => true,
            'message' => $row,
            'images' => $this->model_products->getImagesfilename($id)->result_array()
        ];
        echo json_encode($response);
    }

    public function sample_cms($token = '')
    {
        $this->isLoggedIn();
        //start - for restriction of views
        $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
        $main_nav_id = $this->views_restriction($content_url);
        //end - for restriction of views main_nav_id

        // start - data to be used for views
        $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation
            // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
            'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
        );
        // end - data to be used for views

        // start - load all the views synchronously
        $this->load->view('includes/header', $data_admin);
        $this->load->view('products/sample_cms', $data_admin);
        // end - load all the views synchronously
    }

    public function content_navigation_table()
    {
        $this->isLoggedIn();
        $query = $this->model_dev_settings->content_navigation_table();
        generate_json($query);
    }

    public function get_sys_branch_profile($shop_id){
        $branchid = $this->session->userdata('branchid');
        $result   = $this->model_products->get_sys_branch_profile($shop_id, 0, $branchid);

        if($result != false){
            $response = [
                'success'  => true,
                'details'  => $result,
                'branchid' => $branchid
            ];
        }else{
            $response = [
                'success' => false,
                'branchid' => $branchid
            ];
        }

        echo json_encode($response);
    }

    public function rearrangeImage($f_id, $shopcode, $reorder_image, $prev_image, $imageArr){
        $count = 0;
        if(!empty($reorder_image)){
            foreach($reorder_image as $value){
                if(!empty($prev_image)){
                    foreach($prev_image as $val){
                        if(!in_array($value, $prev_image) && $value != '' && !in_array($value, $imgArr)){
                            $file_new = 'reorder-'.$value;
                            $file     = $value;
                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new);
                            $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
                            $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
                            // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                            
                            $str      = explode(".",$value);
                            $file_new = 'reorder-'.$str[0].'.jpg';
                            $file     = $str[0].'.jpg';

                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file_new);
                            $s3_directory_old = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file;
                            $s3_directory_new = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file_new;
                            // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file_new);
                            $s3_directory_old = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file;
                            $s3_directory_new = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file_new;
                            // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file_new);
                            $s3_directory_old = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file;
                            $s3_directory_new = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file_new;
                            // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                            // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file_new);
                            $s3_directory_old = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file;
                            $s3_directory_new = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file_new;
                            // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                            
                            $imgArr[] = $value;
                            $imgnum   = explode("-",$value);
                            if(floatval($imgnum[0]) > floatval($count)){
                                $count = $imgnum[0];
                            }
                            
                        }
                    }
                }
                else{
                    if($value != ''){
                        $file_new = 'reorder-'.$value;
                        $file     = $value;
                        // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new);
                        $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
                        $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
                        // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                        $str      = explode(".",$value);
                        $file_new = 'reorder-'.$str[0].'.jpg';
                        $file     = $str[0].'.jpg';

                        // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file_new);
                        $s3_directory_old = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file;
                        $s3_directory_new = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file_new;
                        // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                        // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file_new);
                        $s3_directory_old = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file;
                        $s3_directory_new = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file_new;
                        // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                        // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file_new);
                        $s3_directory_old = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file;
                        $s3_directory_new = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file_new;
                        // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                        // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file_new);
                        $s3_directory_old = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file;
                        $s3_directory_new = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file_new;
                        // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

                        $imgArr[] = $value;
                        $imgnum   = explode("-",$value);
                        if(floatval($imgnum[0]) > floatval($count)){
                            $count = $imgnum[0];
                        }
                    }
                }
            }
       
            return array(
                'imgArr' => $imgArr,
                'count' => $count
            );

            // $counter = 0;
            // foreach($reorder_image as $value){
            //     if(!empty($prev_image)){
            //         foreach($prev_image as $val){
            //             if($value != $val){
            //                 $file     = 'reorder-'.$value;
            //                 $str      = explode("-",$value);
            //                 $file_new = $counter."-".$str[1];
            //                 // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new);
            //                 $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
            //                 $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
            //                 // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            //                 $str      = explode(".",$value);
            //                 $file     = 'reorder-'.$str[0].'.jpg';
            //                 $str      = explode("-",$str[0]);
            //                 $file_new = $counter."-".$str[1].'.jpg';

            //                 // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file_new);
            //                 $s3_directory_old = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file;
            //                 $s3_directory_new = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file_new;
            //                 // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            //                 // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file_new);
            //                 $s3_directory_old = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file;
            //                 $s3_directory_new = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file_new;
            //                 // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                            
            //                 // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file_new);
            //                 $s3_directory_old = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file;
            //                 $s3_directory_new = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file_new;
            //                 // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                            
            //                 // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file_new);
            //                 $s3_directory_old = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file;
            //                 $s3_directory_new = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file_new;
            //                 // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            //                 $counter++;
            //             }
            //         }
            //     }
            //     else{
            //         $file     = 'reorder-'.$value;
            //         $str      = explode("-",$value);
            //         $file_new = $counter."-".$str[1];
            //         // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new);
            //         $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
            //         $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
            //         // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            //         $str      = explode(".",$value);
            //         $file     = 'reorder-'.$str[0].'.jpg';
            //         $str      = explode("-",$str[0]);
            //         $file_new = $counter."-".$str[1].'.jpg';

            //         // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/').''.$file_new);
            //         $s3_directory_old = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file;
            //         $s3_directory_new = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file_new;
            //         // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            //         // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/').''.$file_new);
            //         $s3_directory_old = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file;
            //         $s3_directory_new = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file_new;
            //         $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                    
            //         // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/').''.$file_new);
            //         $s3_directory_old = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file;
            //         $s3_directory_new = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file_new;
            //         $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                    
            //         // rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/').''.$file_new);
            //         $s3_directory_old = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file;
            //         $s3_directory_new = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file_new;
            //         // $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            //         $counter++;
            //     }
            // }
        }
    }

    public function makedirImage($f_id, $shopcode){
        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/'), 0777, TRUE);
        }
    }

    public function unlinkImage($f_id, $shopcode, $prev_image_name, $prev_image){
        if(!empty($prev_image_name)){
            foreach($prev_image_name as $value){
                $dir_clean = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/')."".$value;
                array_map('unlink', glob($dir_clean));

                /// s3 delete image function
                $s3_directory = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$value;
                $this->s3_upload->deleteS3Images($s3_directory);

                $value = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value);
                $s3_directory = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$value.'.jpg';
                $this->s3_upload->deleteS3Images($s3_directory);
                $s3_directory = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$value.'.jpg';
                $this->s3_upload->deleteS3Images($s3_directory);
                $s3_directory = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$value.'.jpg';
                $this->s3_upload->deleteS3Images($s3_directory);
                $s3_directory = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$value.'.jpg';
                $this->s3_upload->deleteS3Images($s3_directory);
            }
        }
    }

    public function renameImage($f_id, $shopcode, $imageArr){
        // $imageArr;
        $count  = 0;
        for($x = 0; $x < 12; $x++) { 
            $checker  = 0;

            $file     = $x.'-'.$f_id.'.jpg';
            $file_new = $count.'-'.$f_id.'.jpg';
            
            /// s3 rename functions
            $s3_directory_old = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file;
            $s3_directory_new = 'assets/img/'.$shopcode.'/products-40/'.$f_id.'/'.$file_new;
            $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            $s3_directory_old = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file;
            $s3_directory_new = 'assets/img/'.$shopcode.'/products-50/'.$f_id.'/'.$file_new;
            $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            $s3_directory_old = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file;
            $s3_directory_new = 'assets/img/'.$shopcode.'/products-250/'.$f_id.'/'.$file_new;
            $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            $s3_directory_old = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file;
            $s3_directory_new = 'assets/img/'.$shopcode.'/products-520/'.$f_id.'/'.$file_new;
            $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);

            $file     = $x.'-'.$f_id.'.jpg';
            $file_new =  $count.'-'.$f_id.'.jpg';

            if(rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new)){
                $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
                $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
                $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                $count++;
                $checker = 1;
                $imgArr[] = $file_new;
            }

            $file     = $x.'-'.$f_id.'.png';
            $file_new = $count.'-'.$f_id.'.png';
            if(rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new)){
                if($checker == 0){
                    $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
                    $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
                    $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                    $count++;
                    $checker = 1; 
                    $imgArr[] = $file_new;
                }
                
            }

            $file     = $x.'-'.$f_id.'.jpeg';
            $file_new = $count.'-'.$f_id.'.jpeg';
            if(rename($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file, $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').''.$file_new)){
                if($checker == 0){
                    $s3_directory_old = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file;
                    $s3_directory_new = 'assets/img/'.$shopcode.'/products/'.$f_id.'/'.$file_new;
                    $this->s3_upload->renameS3images($s3_directory_old, $s3_directory_new);
                    $count++;
                    $checker = 1;
                    $imgArr[] = $file_new;
                }
                
            }
        }

        return array(
            'imgArr' => $imgArr,
            'count' => $count
        );
    }

    public function automateUpdateProductImg(){
        $this->load->helper('directory');
        $result  = $this->model_products->automate_updateProductImg();
        $counter = 0;

        foreach($result as $row){
            $map = directory_map(shop_url().'assets/img/'.$row['shopcode'].'/products/'.$row['product_id'].'/', 1);
            if(!empty($map)){
                $count = 1;
                for($x = 0; $x < count($map); $x++){
                    $arrangement = $count;
                    $success = $this->model_products->update_productImgUrl($row['product_id'], $map[$x], $arrangement);
                    $count++;
                }
                $counter += 1;
            }
        }

        print_r($counter." product/s image updated.");
    }

    function emailWishlist($product_id){

        $result  = $this->model_products->getWishlist($product_id)->result_array();

        foreach($result as $row){
            $data['first_name']  = $row['first_name'];
            $data['itemname']    = $row['itemname'];
            $data['primary_pic'] = $row['primary_pic'];
            $data['product_id']  = $row['product_id'];
            $data['shopcode']    = $row['shopcode'];

            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($row["email"]);
            $this->email->subject(get_company_name()." | Wishlist Replenish");
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/wishlist_template", $data, TRUE));
            $this->email->send();
        }
    }

    function display_PHPInform(){
        phpinfo();
    }


    public function get_feutured_products_count(){
        $data = $this->model_products->getFeaturedProductCount();
        echo json_encode($data);
    }

    public function check_feutured_product_arrangement($product_number= ""){
    
        $data = $this->model_products->checkFeaturedProductArrangement($product_number);
        echo json_encode($data);
    }

    public function check_feutured_products($product_id = ""){
        $data = $this->model_products->checkedFeaturedProduct($product_id);
        echo json_encode($data);
    }

    public function view_products_main_category($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products->get_sys_shop($member_id);
            
            if($sys_shop == 0){
                $prev_product = $this->model_products->get_prev_product($this->model_products->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products->get_next_product($this->model_products->get_productdetails($Id)['itemname']);
            }else{
                $prev_product = $this->model_products->get_prev_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products->get_next_product_per_shop($this->model_products->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'get_productdetails'  => $this->model_products->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products->get_sys_branch_profile($this->model_products->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'getVariants'         => $this->model_products->getVariants($Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($Id)->result_array()
            );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/view_products', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function puregold_export_template_product($token)
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);

        $sheet->setCellValue('A1', 'STORE');
        $sheet->setCellValue('B1', 'SKU');
        $sheet->setCellValue('C1', 'BARCODE');
        $sheet->setCellValue('D1', 'BRAND');
        $sheet->setCellValue('E1', 'ITEM DESCRIPTION');
        $sheet->setCellValue('F1', 'VARIANT');
        $sheet->setCellValue('G1', 'CATEGORY');
        $sheet->setCellValue('H1', 'SUBCATEGORY');
        $sheet->setCellValue('I1', 'CLASS');
        $sheet->setCellValue('J1', 'SIZE');
        $sheet->setCellValue('K1', 'PRICE');
        $sheet->setCellValue('L1', 'BEGINNING BALANCE');
        $sheet->setCellValue('M1', 'ITEM IMAGE');
        $sheet->setCellValue('N1', 'PRIMARY BARCODE');
        $sheet->setCellValue('O1', 'ISTYPE');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'puregoldproducts_template ' . date('Y/m/d');
        ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        $this->audittrail->logActivity('Product List', 'Puregold Products Template has been downloaded.', 'download', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function puregold_upload_products_files(){
        $this->isLoggedIn();

        $files = $_FILES;
        $file_name = '';
        if(!empty($_FILES)){
            $_FILES['userfile'] = [
                'name'     => $files['file']['name'],
                'type'     => $files['file']['type'],
                'tmp_name' => $files['file']['tmp_name'],
                'error'    => $files['file']['error'],
                'size'     => $files['file']['size']
            ];
            $F[] = $_FILES['userfile'];
            
            //Upload requirements
            $config = array();
            $directory = 'assets/img';
            $config['file_name'] = $file_name;
            $config['upload_path'] = $directory;
            $config['allowed_types'] = 'csv|xls|xlsx';
            $config['max_size'] = 3000;
            $this->load->library('upload',$config, 'bupload');
            $this->bupload->initialize($config);

            $data = array();

            if(!$this->bupload->do_upload()){
                $error = array('error' => $this->bupload->display_errors());
                $data = array("success" => 0, 'message' => $error['error']);
                generate_json($data);
            }else{
                $file_name = $this->bupload->data()['file_name'];

                // print_r($file_name."1");

                $arr_file = explode('.', $file_name);
                $extension = end($arr_file);

                $is_supported = "";
                if('csv' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                    $is_supported = 1;
                }elseif('xls' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    $is_supported = 1;
                }elseif('xlsx' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $is_supported = 1;
                }
                else{
                    $is_supported = 0;
                }

               // print_r($is_supported);

                if($is_supported == 1){
                
                    ///upload image to s3 bucket
                    $fileTempName    = $files['file']['tmp_name'];
                    $activityContent = 'assets/docs/products-upload/puregold/'.$file_name;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
                        generate_json($data);
                        die();
                    }

                    else{
                        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                        $sheetData = $spreadsheet->getActiveSheet()->toArray();

                        /// spreadsheet validation per row
                        $empty_validation = 0;
                        $empty_str        = "";
                        $cell_count       = 2;
                        $query            = false;
                        $auditstring      = "\nProducts: \n";
                        foreach(array_slice($sheetData, 1) as $value){

                            $value[7] = str_replace(' ', '', $value[7]);
                            $value[10] = str_replace(' ', '', $value[10]);
                            $value[11] = str_replace(' ', '', $value[11]);

                            if($value[0] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "A".$cell_count.", ";
                            }

                            if($value[1] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "B".$cell_count.", ";
                            }

                            if($value[2] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "C".$cell_count.", ";
                            }

                            if($value[4] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "E".$cell_count.", ";
                            }

                            
                            if($value[7] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "H".$cell_count.", ";
                            }

                            if($value[10] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "K".$cell_count.", ";
                            }
                            
                            if($value[11] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "L".$cell_count.", ";
                            }
                            $auditstring .= $value[4]."\n";

                            $cell_count++;
                        } 

                        $empty_str = rtrim($empty_str, ', ');

                        if(empty(array_slice($sheetData, 1))){
                            $empty_validation = 2;
                        }
                        if($empty_validation == 1){
                            $data = array("success" => 0, 'message' => "Please fill up required cell. \n".$empty_str);
                            generate_json($data);
                            die();
                        }
                        else if($empty_validation == 2){
                            $data = array("success" => 0, 'message' => "Unable to upload. No data found!");
                            generate_json($data);
                            die();
                        }
                        else{
                            $query = $this->model_products->puregold_upload_products_files($sheetData);
                        }
             
                        if ($query) {
                           $data = array("success" => 1, 'message' => "File successfully uploaded.");
                           $this->audittrail->logActivity('Toktokmart Product', "Successfuly upload file ".$file_name.$auditstring, 'upload', $this->session->userdata('username'));
                            generate_json($data);

                        }else{
                            $data = array("success" => 0, 'message' => "Something went wrong.");
                            generate_json($data);
                        } 

                    }//end of else (succes upload s3)

                    unlink($directory.'/'.$file_name);

                }//end of supported file
                else{
                    $data = array("success" => 0, 'message' => "File is not supported!");
                    generate_json($data);
                }//end else not supported
            }//end else 
        }//end of if
    }

    public function export_template_product($token)
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $shop_id = $this->session->userdata('sys_shop');

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(30);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getColumnDimension('R')->setWidth(30);
        $sheet->getColumnDimension('S')->setWidth(30);
        $sheet->getColumnDimension('T')->setWidth(30);
        $sheet->getColumnDimension('U')->setWidth(30);

        $sheet->setCellValue('A1', 'Shop');
        $sheet->setCellValue('B1', 'Category');
        $sheet->setCellValue('C1', 'Product Name');
        $sheet->setCellValue('D1', 'Product Summary');
        $sheet->setCellValue('E1', 'Other Info');
        $sheet->setCellValue('F1', 'Price');
        $sheet->setCellValue('G1', 'Compared at Price');
        $sheet->setCellValue('H1', 'Product Tags');
        $sheet->setCellValue('I1', 'SKU');
        $sheet->setCellValue('J1', 'Barcode');
        $sheet->setCellValue('K1', 'Max Quantity per Checkout');
        $sheet->setCellValue('L1', 'Weight(Grams)');
        $sheet->setCellValue('M1', 'Length(Inches)');
        $sheet->setCellValue('N1', 'Width(Inches)');
        $sheet->setCellValue('O1', 'Height(Inches)');
        $sheet->setCellValue('P1', 'Image URL 1');
        $sheet->setCellValue('Q1', 'Image URL 2');
        $sheet->setCellValue('R1', 'Image URL 3');
        $sheet->setCellValue('S1', 'Image URL 4');
        $sheet->setCellValue('T1', 'Image URL 5');
        $sheet->setCellValue('U1', 'Image URL 6');

        if($shop_id != 0){
            $shopname = $this->model_products->getShopDetails_byID($shop_id)->row_array()['shopname'];
            $sheet->setCellValue('A2', $shopname);
        }
        else{
            $shopsList = $this->model_products->get_shop_options();
            $shopsStr  = '';
            foreach($shopsList as $shops){
                $shopsStr .= $shops['shopname'].", ";
            }

            $shopsStr = rtrim($shopsStr, ', ');
            $shopsStr .= '';

            $validation = $sheet->getCell('A2')->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setFormula1('"'.$shopsStr.'"');
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setShowInputMessage(true);
            $validation->setPromptTitle('Note');
            $validation->setPrompt('Must select one from the shop list.');
            $validation->setShowErrorMessage(true);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setErrorTitle('Invalid option');
            $validation->setError('Select one from the shop list.');
        }
        

        $catList = $this->model_products->get_category_options();
        $catStr  = '';
        foreach($catList as $category){
            $catStr .= $category['category_name'].", ";
        }

        $catStr = rtrim($catStr, ', ');
        $catStr .= '';

        $validation = $sheet->getCell('B2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setFormula1('"'.$catStr.'"');
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowInputMessage(true);
        $validation->setPromptTitle('Note');
        $validation->setPrompt('Must select one from the category list.');
        $validation->setShowErrorMessage(true);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setErrorTitle('Invalid option');
        $validation->setError('Select one from the category list.');

        $sheet->getStyle('A1:U1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'toktokmartproducts_template' . date('Y/m/d');
        ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        $this->audittrail->logActivity('Product List', 'Toktokmart Products Template has been downloaded.', 'download', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function upload_products_files(){
        $this->isLoggedIn();

        $files = $_FILES;
        $file_name = '';
        if(!empty($_FILES)){
            $_FILES['userfile'] = [
                'name'     => $files['file']['name'],
                'type'     => $files['file']['type'],
                'tmp_name' => $files['file']['tmp_name'],
                'error'    => $files['file']['error'],
                'size'     => $files['file']['size']
            ];
            $F[] = $_FILES['userfile'];
            
            //Upload requirements
            $config = array();
            $directory = 'assets/img';
            $config['file_name'] = $file_name;
            $config['upload_path'] = $directory;
            $config['allowed_types'] = 'csv|xls|xlsx';
            $config['max_size'] = 3000;
            $this->load->library('upload',$config, 'bupload');
            $this->bupload->initialize($config);

            $data = array();

            if(!$this->bupload->do_upload()){
                $error = array('error' => $this->bupload->display_errors());
                $data = array("success" => 0, 'message' => $error['error']);
                generate_json($data);
            }else{
                $file_name = $this->bupload->data()['file_name'];

                // print_r($file_name."1");

                $arr_file = explode('.', $file_name);
                $extension = end($arr_file);

                $is_supported = "";
                if('csv' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                    $is_supported = 1;
                }elseif('xls' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    $is_supported = 1;
                }elseif('xlsx' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $is_supported = 1;
                }
                else{
                    $is_supported = 0;
                }

               // print_r($is_supported);

                if($is_supported == 1){
                
                    ///upload image to s3 bucket
                    $fileTempName    = $files['file']['tmp_name'];
                    // print_r($fileTempName);
                    // die();
                    $activityContent = 'assets/docs/products-upload/uncustomized/'.$file_name;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
                        generate_json($data);
                        die();
                    }

                    else{
                        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                        $sheetData = $spreadsheet->getActiveSheet()->toArray();
                     
                        /// spreadsheet validation per row
                        $empty_validation = 0;
                        $empty_str        = "";
                        $cell_count       = 2;
                        $query            = false;
                        $auditstring      = "\nProducts: \n";
                        foreach(array_slice($sheetData, 1) as $key => $value){
                            if($value[0] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "A".$cell_count.", ";
                            }

                            if($value[1] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "B".$cell_count.", ";
                            }

                            if($value[2] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "C".$cell_count.", ";
                            }

                            if($value[4] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "E".$cell_count.", ";
                            }

                            if($value[5] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "F".$cell_count.", ";
                            }

                            if($value[9] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "J".$cell_count.", ";
                            }

                            $auditstring .= $value[2]."\n";

                            $cell_count++;
                        } 

                        $empty_str = rtrim($empty_str, ', ');

                        if(empty(array_slice($sheetData, 1))){
                            $empty_validation = 2;
                        }
                        if($empty_validation == 1){
                            $data = array("success" => 0, 'message' => "Please fill up required cell. \n".$empty_str);
                            generate_json($data);
                            die();
                        }
                        else if($empty_validation == 2){
                            $data = array("success" => 0, 'message' => "Unable to upload. No data found!");
                            generate_json($data);
                            die();
                        }
                        else{
                            $query = $this->model_products->upload_products_files($sheetData);
                        }
             
                        if ($query) {
                           $data = array("success" => 1, 'message' => "File successfully uploaded.");
                           $this->audittrail->logActivity('Toktokmart Product', "Successfuly upload file ".$file_name.$auditstring, 'upload', $this->session->userdata('username'));
                            generate_json($data);

                        }else{
                            $data = array("success" => 0, 'message' => "Something went wrong.");
                            generate_json($data);
                        } 

                    }//end of else (succes upload s3)

                    unlink($directory.'/'.$file_name);

                }//end of supported file
                else{
                    $data = array("success" => 0, 'message' => "File is not supported!");
                    generate_json($data);
                }//end else not supported
            }//end else 
        }//end of if
    }

    public function export_template_product_inventory($token)
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $shop_id = $this->session->userdata('sys_shop');

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        $sheet->setCellValue('A1', 'Item Name');
        $sheet->setCellValue('B1', 'Branch Name');
        $sheet->setCellValue('C1', 'Barcode');
        $sheet->setCellValue('D1', 'Inventory Qty');

        if($shop_id != 0){
            $shopBranches = $this->model_products->getBranches_shop($shop_id)->result_array();
            $branchesStr  = '';
            foreach($shopBranches as $sB){
                $branchesStr .= $sB['branchname'].", ";
            }

            $branchesStr = rtrim($branchesStr, ', ');
            $branchesStr .= '';
        }
        else{
            $shopBranches = $this->model_products->getBranches()->result_array();
       
            $branchesStr  = '';
            foreach($shopBranches as $sB){
                $branchesStr .= $sB['branchname'].", ";
            }

            $branchesStr = rtrim($branchesStr, ', ');
            $branchesStr .= '';
        }

        // $validation = $sheet->getCell('B2')->getDataValidation();
        // $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        // $validation->setFormula1('"Main, '.$branchesStr.'"');
        // $validation->setAllowBlank(false);
        // $validation->setShowDropDown(true);
        // $validation->setShowInputMessage(true);
        // $validation->setPromptTitle('Note');
        // $validation->setPrompt('Must select one from the branch list.');
        // $validation->setShowErrorMessage(true);
        // $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        // $validation->setErrorTitle('Invalid option');
        // $validation->setError('Select one from the branch list.');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'toktokmartproductsinventory_template' . date('Y/m/d');
        ob_end_clean();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        $this->audittrail->logActivity('Product List', 'Toktokmart Products Template has been downloaded.', 'download', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function upload_inventory_products_files(){
        $this->isLoggedIn();

        $files = $_FILES;
        $file_name = '';
        if(!empty($_FILES)){
            $_FILES['userfile'] = [
                'name'     => $files['file']['name'],
                'type'     => $files['file']['type'],
                'tmp_name' => $files['file']['tmp_name'],
                'error'    => $files['file']['error'],
                'size'     => $files['file']['size']
            ];
            $F[] = $_FILES['userfile'];
            
            //Upload requirements
            $config = array();
            $directory = 'assets/img';
            $config['file_name'] = $file_name;
            $config['upload_path'] = $directory;
            $config['allowed_types'] = 'csv|xls|xlsx';
            $config['max_size'] = 3000;
            $this->load->library('upload',$config, 'bupload');
            $this->bupload->initialize($config);

            $data = array();

            if(!$this->bupload->do_upload()){
                $error = array('error' => $this->bupload->display_errors());
                $data = array("success" => 0, 'message' => $error['error']);
                generate_json($data);
            }else{
                $file_name = $this->bupload->data()['file_name'];

                // print_r($file_name."1");

                $arr_file = explode('.', $file_name);
                $extension = end($arr_file);

                $is_supported = "";
                if('csv' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                    $is_supported = 1;
                }elseif('xls' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    $is_supported = 1;
                }elseif('xlsx' == $extension){
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $is_supported = 1;
                }
                else{
                    $is_supported = 0;
                }

               // print_r($is_supported);

                if($is_supported == 1){
                
                    ///upload image to s3 bucket
                    $fileTempName    = $files['file']['tmp_name'];
                    $activityContent = 'assets/docs/products-upload/uncustomized/'.$file_name;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
                        generate_json($data);
                        die();
                    }

                    else{
                        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                        $sheetData = $spreadsheet->getActiveSheet()->toArray();
                     
                        /// spreadsheet validation per row
                        $empty_validation = 0;
                        $empty_str        = "";
                        $cell_count       = 2;
                        $query            = false;
                        $auditstring      = "\nProducts: \n";
                        foreach(array_slice($sheetData, 1) as $value){
                            if($value[0] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "A".$cell_count.", ";
                            }

                            if($value[1] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "B".$cell_count.", ";
                            }

                            if($value[2] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "C".$cell_count.", ";
                            }

                            if($value[3] == ''){
                                $empty_validation = 1;
                                $empty_str        .= "D".$cell_count.", ";
                            }

                            $auditstring .= $value[0]."\n";

                            $cell_count++;
                        } 

                        $empty_str = rtrim($empty_str, ', ');

                        if(empty(array_slice($sheetData, 1))){
                            $empty_validation = 2;
                        }
                        if($empty_validation == 1){
                            $data = array("success" => 0, 'message' => "Please fill up required cell. \n".$empty_str);
                            generate_json($data);
                            die();
                        }
                        else if($empty_validation == 2){
                            $data = array("success" => 0, 'message' => "Unable to upload. No data found!");
                            generate_json($data);
                            die();
                        }
                        else{
                            $query = $this->model_products->upload_inventory_products_files($sheetData);
                        }
             
                        if ($query) {
                           $data = array("success" => 1, 'message' => "File successfully uploaded.");
                           $this->audittrail->logActivity('Toktokmart Product', "Successfuly upload file ".$file_name.$auditstring, 'upload', $this->session->userdata('username'));
                            generate_json($data);

                        }else{
                            $data = array("success" => 0, 'message' => "Something went wrong.");
                            generate_json($data);
                        } 

                    }//end of else (succes upload s3)

                    unlink($directory.'/'.$file_name);

                }//end of supported file
                else{
                    $data = array("success" => 0, 'message' => "File is not supported!");
                    generate_json($data);
                }//end else not supported
            }//end else 
        }//end of if
    }
}

