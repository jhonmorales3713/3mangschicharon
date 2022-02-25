<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dev_settings extends CI_Controller {

    public function __construct(){
        parent::__construct();
        //load model or libraries below here...
        $this->load->model("dev_settings/Model_shop_utilities");
        $this->load->model("dev_settings/Model_orderpostbacklogs", "model_orderpostbacklogs");
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

        // if ($this->session->userdata('username') != 'paulchua@cloudpanda.ph') {
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

    public function shop_utilities($token = ''){
        $this->isLoggedIn();
        
        if ($this->loginstate->get_access()['shop_utilities']['view'] == 1) {
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
                'shop_util' => [
                    'id' => cp_id(),
                    'powered_by' => powered_by(),
                    'cp_logo' => cp_logo(),
                    'shop_main_announcement' => shop_main_announcement(),
                    'c_paypanda_link' => c_paypanda_link(),
                    'c_paypanda_test' => c_paypanda_test(),
                    'c_allowed_jcfulfillment_prefix' => c_allowed_jcfulfillment_prefix(),
                ]
            );
            // end - data to be used for views
            // print_r($data_admin);
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/shop_utilities', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function update_shop_utility(){
        // $this->
        // print_r($_FILES['logo_image']);
        if ($this->loginstate->get_access()['shop_utilities']['update']) {
            # code...
            $data = [];
            if ($this->input->post('cp_id')) {
                $data = [
                    'id' => $this->input->post('cp_id'),
                    'powered_by' => $this->input->post('powered_by'),
                    'shop_main_announcement' => $this->input->post('shop_main_announcement'),
                    'c_paypanda_link_live' => $this->input->post('c_paypanda_link_live'),
                    'c_paypanda_link_test' => $this->input->post('c_paypanda_link_test'),
                    'c_allowed_jcfulfillment_prefix' => $this->input->post('c_allowed_jcfulfillment_prefix'),
                ];

                $prev_val = (array) json_decode($this->input->post('prev_val'));
            }
    
            if ($_FILES['logo_image']['tmp_name']) {
                $target_dir = "assets/img/";
                $target_file = $target_dir . basename($_FILES["logo_image"]["name"]);
    
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                
                $newFileName = substr(md5(microtime()),0,16) . ".$imageFileType";
    
                $target_file = $target_dir.$newFileName;
                $data['cp_logo'] = $newFileName;
                
                if (move_uploaded_file($_FILES["logo_image"]["tmp_name"], $target_file)) {
                    $data['image'] = $_FILES["logo_image"]["name"];
                    $response['success'] = true;
                    $response['message'] = "The file ". basename( $_FILES["logo_image"]["name"]). " has been uploaded.";
                } else {
                    $response['message'] = "Sorry, there was an error uploading your file.";
                }
            }
    
            if ($this->Model_shop_utilities->update_data($data)) {
                $changes = $this->audittrail->createUpdateFormat(array_diff_assoc($data, $prev_val),$prev_val);
                $remarks = "Shop Utilities has been updated successfully. \nChanges: \n$changes";
                $this->audittrail->logActivity('Shop Utilities', $remarks, 'update', $this->session->userdata('username'));
                $response['success'] = true;
                $response['message'] = "Shop utilities has been updated.";
            }else{
                $response['message'] = "Shop utilities has not been updated.";
            }
    
            echo json_encode($response);
        }else{
            $this->load->view('error_404');
        }
    }

    public function postback_logs($token = '')
    {
        $this->isLoggedIn();
        
        if ($this->loginstate->get_access()['api_postback_logs']['view'] == 1) {
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
            );
            // end - data to be used for views
            // print_r($data_admin);
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/toktok_postback_logs', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function postback_logs_tbl()
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['api_postback_logs']['view'] == 1) {
            $search = $this->input->post('search');
            $fromdate = $this->input->post('date_from');
            $todate = $this->input->post('date_to');

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_orderpostbacklogs->get_postback_logs($fromdate, $todate, $search, $_REQUEST);
            generate_json($result);
        }else{
            $this->load->view('error_404');
        }
    }

    public function postback_logs_tbl_export()
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['api_postback_logs']['view'] == 1) {
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filters"));

            $fromdate = sanitize($filter->date_from);
            $todate = sanitize($filter->date_to);
            $search = sanitize($filter->search);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_orderpostbacklogs->get_postback_logs($fromdate, $todate, $search, $requestData, true);
            // print_r($result);
            $filter = ['Reference Num' => $search];
            extract($this->audittrail->get_ReportExportRemarks('', '', $fromdate, $todate, 'Tok-Tok API Postback Logs', $filter));
            $this->audittrail->logActivity('API Postback Logs', $remarks, 'export', $this->session->userdata('username')); 

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "API Postback Logs");
            $sheet->setCellValue('B2', "Reference Num: $search");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);

            $headers = [
                'Date', 'Reference Number', 'Details'
            ];
            $sheet->fromArray($headers, null, 'A7');
            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6')->getFont()->setBold(true);
            $sheet->getStyle('A7:C7')->getFont()->setBold(true);

            $exceldata= array_get($result, 'data');
            
            $sheet->fromArray($exceldata, null, 'A8');
            $writer = new Xlsx($spreadsheet);
            $filename = 'API Postback Logs ' . date('Y/m/d');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
            header('Cache-Control: max-age=0');
            ob_end_clean();
            return $writer->save('php://output');
            exit();
        }else{
            $this->load->view('error_404');
        }
    }
}
