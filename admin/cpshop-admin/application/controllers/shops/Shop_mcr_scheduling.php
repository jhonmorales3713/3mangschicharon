<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Shop_mcr_scheduling extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('adhoc_resize/Model_adhoc_resize');
        $this->load->model('shops/Model_shops');
        $this->load->model('shops/Model_shop_mcr_scheduling');
        $this->load->model('products/model_products');
        $this->load->model('libs/Model_generatedfilename');
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

    public function shop_mcr_scheduling($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['shop_mcr_scheduling']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
            $mainshop = $this->Model_shop_mcr_scheduling->get_all_shop()->result();

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'mainshop'            => $mainshop,
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'shops'               => $this->model_products->get_shop_options(),
                'validation'           => $this->loginstate->get_access()['shop_mcr'],
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('shops/shop_mcr_scheduling', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function shop_mcr_scheduling_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['shop_mcr_scheduling']['view'] == 1){
      $filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_mainshop' => $this->input->post('_mainshop'),
            '_effectivity_date' => $this->input->post('_effectivity_date'),
        ];
      $requestData = $_REQUEST;
      $data = $this->Model_shop_mcr_scheduling->get_shop_mcr_scheduling_data($filters,$requestData);
      echo json_encode($data);

    }else{
      $this->load->view('error_404');
    }
  }

  public function export_shop_mcr_scheduling()
    {
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        $query = $this->Model_shop_mcr_scheduling->get_shop_mcr_scheduling_data($filters,$requestData);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $shopid = $filters['_mainshop'];
        $fil_arr = [
            'Effectivity Date' => $filters['_effectivity_date'],
            'Record Status' => array(
                '' => 'All Records', 0 => 'Pending', 1 => 'Applied'
            )[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, "", $fromdate, $fromdate, "Shop MCR Scheduling", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Shop MCR Scheduling ', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Shop MCR Scheduling ");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        // $sheet->getColumnDimension('A')->setWidth(20);
        // $sheet->getColumnDimension('B')->setWidth(30);
        // $sheet->getColumnDimension('C')->setWidth(20);
        // $sheet->getColumnDimension('D')->setWidth(10);
        // $sheet->getColumnDimension('D')->setWidth(10);
       
        // $sheet->getColumnDimension('F')->setWidth(15);

        $sheet->setCellValue("A6", 'Shop Name');
        $sheet->setCellValue('B6', 'MCR');
        $sheet->setCellValue('C6', 'Startup');
        $sheet->setCellValue('D6', 'JC');
        $sheet->setCellValue('E6', 'MCJR');
        $sheet->setCellValue('F6', 'MC');
        $sheet->setCellValue('G6', 'MCSUPER');
        $sheet->setCellValue('H6', 'MCMEGA');
        $sheet->setCellValue('I6', 'Others');
        $sheet->setCellValue('J6', 'Effectivity Date');
        $sheet->setCellValue('K6', 'Status');
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:K6')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getStyle('A')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('H')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('K')->getAlignment()->setWrapText(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4],
                '6' => $row[5],
                '7' => $row[6],
                '8' => $row[7],
                '9' => $row[8],
                '10' => $row[9],
                '11' => $row[10]
            
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Shop MCR Scheduling ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();

    }

    public function delete_mcr_schedule_record(){
        $this->isLoggedIn();
        
        $delete_id = sanitize($this->input->post('delete_id'));
        if ($delete_id > 0) {
            
            $query = $this->Model_shop_mcr_scheduling->delete_mcr_schedule_record($delete_id);
            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
                $this->audittrail->logActivity('Shop MCR Scheduling', 'Schedule successfully deleted.', "delete", $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");        
            }

        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        generate_json($data);
    }

    public function shop_mcr_cron(){
        $cron_check_succeed = $this->Model_shop_mcr_scheduling->shop_mcr_cron();
        if($cron_check_succeed==true){
            $data = array("success" => 1, 'message' => $cron_check_succeed);
            generate_json($data);
        }
    }
}