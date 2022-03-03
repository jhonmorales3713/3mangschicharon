<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Main_products extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('products/model_products');
        $this->load->library('upload');
        $this->load->library('uuid');
    }

    
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function products_home($labelname = null){
        header('location:'.base_url('admin/Main_products/'));
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
    public function check_feutured_products($product_id = ""){
        $data = $this->model_products->checkedFeaturedProduct($product_id);
        echo json_encode($data);
    }
    public function check_feutured_product_arrangement($product_number= ""){
    
        $data = $this->model_products->checkFeaturedProductArrangement($product_number);
        echo json_encode($data);
    }

    public function export_product_table()
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $request = url_decode(json_decode($this->input->post("_search")));
        $member_id = $this->session->userdata('sys_users_id');
        $filter_text = "";
      
        // $sys_shop = $this->model_products->get_sys_shop($member_id);
        $sys_shop = $this->session->userdata('sys_shop');
        
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

    public function products($token = ''){
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
                'categories'          => $this->model_products->get_category_options(),
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/products/products',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
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
    

    public function add_products($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'featured_products'   => $this->model_products->getFeaturedProduct(),
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/products/add_products',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }

    
    public function save_variant()
    {
        $f_id      = $this->uuid->v4_formatted();
        $file_name = $f_id;
        $imgArr    = [];

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

            $directory    = 'assets/img/';
            if (!is_dir( 'assets/img/')) {
                mkdir( 'assets/img/', 0777, true);
            }
            if (!is_dir( 'assets/img/products/')) {
                mkdir( 'assets/img/products/', 0777, true);
            }
           
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
                            copy($_FILES['userfile']['tmp_name'], 'assets/img/products/'. en_dec('en', $_FILES['userfile']['name']).'.'. pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
                                
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
        
        // $response = [
        //     'environment' => ENVIRONMENT,
        //     'success'     => false,
        //     'message'     => $this->response->message('error')
        // ];

        // $check_itemid = $this->model_products->check_products_itemid($this->input->post('f_itemid'), $this->input->post('f_member_shop'));
        // $parent_id_details=$this->model_products->get_productdetails($this->input->post('f_parent_product_id'));
        
        // $check_id = ($check_itemid->num_rows() > 0)?$this->model_products->check_products_id($check_itemid->row()->Id):'';
        
        // if($check_itemid->num_rows() > 0 && $this->input->post('f_itemid') != ''  && ini() == 'jcww'){
        //     $check_parentid = $this->model_products->check_products_id($check_id->row()->parent_product_id);
        //     if($parent_id_details['itemid'] != $this->input->post('f_itemid') ){
        //         $response = [
        //             'environment' => ENVIRONMENT,
        //             'success'     => false,
        //             'message'     => 'Referral Commission ItemID already exists.'
        //         ];    
        //         echo json_encode($response);
        //         die();
        //     }
        // }else 
        // if($check_itemid->num_rows() > 0 && $this->input->post('f_itemid') != '' && ini() != 'jcww'){
        //     $response = [
        //         'environment' => ENVIRONMENT,
        //         'success'     => false,
        //         'message'     => 'Referral Commission ItemID already exists.'
        //     ];

        //     echo json_encode($response);
        //     die();
        // }

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
        // $Get_email_settings = $this->model_products->get_email_settings();

        // $data_email = array(
        //     'variant_name'           => $this->input->post('f_itemname'),
        //     'itemname'               => $Get_parent_product[0]['itemname'],
        //     'shopname'               => $Get_shop_details[0]['shopname'],
        //     'new_product_email'      =>  $Get_email_settings[0]['new_product_email'],
        //     'new_product_name'       =>  $Get_email_settings[0]['new_product_name'],
        //     'approval_email'         => $Get_email_settings[0]['approval_product_email'],
        //     'approval_name'          => $Get_email_settings[0]['approval_product_name'],
        // );
        // $this->sendProductVariantNewlyCreatedEmail($data_email);
        // $this->sendProductVariantForApprovalEmail($data_email);


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
                
                if(ini() == 'jcww'){
                    $response = array(
                        'success'      => false,
                        'environment' => ENVIRONMENT,
                        'message'     => 'Delivery Area field must be filled at least 1.'
                    );
                    echo json_encode($response);
                    die();
                }
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
        $count_upload = count($_FILES['product_image']['name']);
        $reorder_image   = $this->input->post('reorder_image');

        if(empty($this->input->post('f_category'))){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => 'No category selected.'
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
            // if($this->input->post('f_member_shop') == ''){
            //     $response = array(
            //         'success'      => false,
            //         'environment' => ENVIRONMENT,
            //         'message'     => 'Please select a shop.'
            //     );
            //     echo json_encode($response);
            //     die();
            // }
            $shopcode = $this->model_products->get_shopcode_via_shopid($this->session->userdata('sys_shop_id'));

            // $this->makedirImage($f_id, $shopcode);

            $directory    = 'assets/img/';
            if (!is_dir( 'assets/img/')) {
                mkdir( 'assets/img/', 0777, true);
            }
            if (!is_dir( 'assets/img/products/')) {
                mkdir( 'assets/img/products/', 0777, true);
            }
           
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
                            copy($_FILES['userfile']['tmp_name'], 'assets/img/products/'. en_dec('en', $_FILES['userfile']['name']).'.'. pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
                                
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
        
        // $response = [
        //     'environment' => ENVIRONMENT,
        //     'success'     => false,
        //     'message'     => $this->response->message('error')
        // ];

        // $check_itemid = $this->model_products->check_products_itemid($this->input->post('f_itemid'), $this->input->post('f_member_shop'));

        // if($check_itemid->num_rows() > 0 && $this->input->post('f_itemid') != ''){
        //     $response = [
        //         'environment' => ENVIRONMENT,
        //         'success'     => false,
        //         'message'     => 'Referral Commission ItemID already exists.'
        //     ];

        //     echo json_encode($response);
        //     die();
        // }

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
            
            // $Get_shop_details = $this->model_products->getSysShopsDetails($this->input->post('f_member_shop'));
            // $Get_email_settings = $this->model_products->get_email_settings();

            // $data_email = array(
            //     'itemname'               => $this->input->post('f_itemname'),
            //     'shopname'               => $Get_shop_details[0]['shopname'],
            //     'new_product_email'      =>  $Get_email_settings[0]['new_product_email'],
            //     'new_product_name'       =>  $Get_email_settings[0]['new_product_name'],
            //     'approval_email'         => $Get_email_settings[0]['approval_product_email'],
            //     'approval_name'          => $Get_email_settings[0]['approval_product_name'],
            // );
            
            // $this->sendProductNewlyCreatedEmail($data_email);
            // $this->sendProductForApprovalEmail($data_email);

            $response['success']    = $success;
            $response['message']    = "Product created successfully.";
            $response['product_id'] = $f_id;
            $this->audittrail->logActivity('Product List', $this->input->post('f_itemname').' successfully added to Products.', 'add', $this->session->userdata('username'));
        }
        echo json_encode($response);

    }


    public function checkProductStatus($product_id)
    {
        $product_status = $this->model_products->checkProductStatus($product_id);
        $product_status = $product_status->enabled;

        if($product_status == 0){
            header("location:" . base_url('products/Main_products/display404'));
        }
       
    }



    public function add_variant($token = '', $parent_Id)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['create'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token'               => $token,
                'branchid'            => $branchid,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'main_nav_id'           => $main_nav_id,
                'parent_Id'           => $parent_Id,
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'featured_products'   => $this->model_products->getFeaturedProduct(),
                'get_parentProduct'   => $this->model_products->get_productdetails($parent_Id),
                'getVariants'         => $this->model_products->getVariants($parent_Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($parent_Id)->result_array()
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/products/add_variant',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
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

            $member_id = $this->session->userdata('sys_users_id');
            $branchid  = $this->session->userdata('branchid');

            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products->get_shop_options(),
                'categories'          => $this->model_products->get_category_options(),
                'get_productdetails'  => $this->model_products->get_productdetails($Id),
                //'get_branchdetails'   => $this->model_products->get_sys_branch_profile($this->model_products->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'featured_products'   => $this->model_products->getFeaturedProduct(),
                'getVariants'         => $this->model_products->getVariants($Id),
                'getVariantsOption'   => $this->model_products->getVariantsOption($Id)->result_array()

            );
            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/products/update_products',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
         
        }else{
            $this->load->view('error_404');
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
          //  header("location:" . base_url('Main/logout'));
        } else {
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }
}