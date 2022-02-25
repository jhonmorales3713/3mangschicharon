<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class pending_orders extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('shop_branch/Model_shopbranch', 'model_shopbranch');
        $this->load->model('orders/Model_orders', 'model_orders');
        $this->load->model('shops/Model_shops');
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

        if (in_array($content_url, $url_content_arr) == false) {
            header("location:" . base_url('Main/logout'));
        } else {
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }

    public function index($token = ""){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['po']['view'] == 1){
            //start - for restriction of views
            // print_r($_GET);
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $user_id = $this->session->userdata('id'); $shops = [];
            $shopid = $this->model_shops->get_sys_shop($user_id);
            if ($shopid == 0) {
                $shops = $this->model_shops->get_shop_opts_oderbyname();
            }elseif ($shopid > 0) {
                $shops = $this->model_shopbranch->get_branch_options($shopid);
            }
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation,
                'shopid' => $this->model_shops->get_sys_shop($user_id),
                'shops' => $shops,
                'shop' => $shopid,
                'branch' => $this->session->userdata('branchid'),
                'setDate' => [
                    'fromdate' => (isset($_GET['fromdate'])) ? $_GET['fromdate']:'',
                    'todate'   => (isset($_GET['todate'])) ? $_GET['todate']:''
                ]
            );
            // end - data to be used for views
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/pending_orders',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function list_table(){
        $this->isLoggedIn();
        $search = json_decode($this->input->post('searchValue'));
        $data = $this->model_sale_settlement->list_table($search);
        echo json_encode($data);
    }

    public function get_pending_orders_table()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['po']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));
            
            $result = $this->model_orders->get_pending_orders_table($fromdate, $todate, $shopid, $branchid, $_REQUEST);

            if(count($result['data']) > 0){
                $result = array_merge(array("success" => 1), $result);
                echo json_encode($result);
            }else{
                $result = array("success" => 0, "data" => array());
                echo json_encode($result);
            }

        } else {
            $this->load->view('error_404');
        }
    }

    public function get_po_chart()
    {
        $this->isLoggedIn();
        $filter = $this->input->post('filter');
        $fromdate = sanitize($filter['fromdate']);
        $todate = sanitize($filter['todate']);
        $shopid = sanitize($filter['shopid']);
        $branchid = sanitize($filter['branchid']);
        
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));

        $labels = [];
        if ($fromdate == $todate) {
            $legend = [date_format(date_create($fromdate), 'M d')];
            if ($fromdate == date("Y-m-d")) {
                $legend = ["Today"];
            }
        } else {
            $legend = [date_format(date_create($fromdate), 'M d') . " - " . date_format(date_create($todate), 'M d, Y')];
        }
        
        $result = $this->model_orders->get_pending_orders_chart($fromdate, $todate, $shopid, $branchid);
        $bgColors = get_GreenColorSet();
        // print_r($result['data']);
        // exit();
        $data = $result['data'];
        $chartdata = [
            'label' => $legend,
            'backgroundColor' => $bgColors,
            'data' => [],
        ];
        foreach ($data as $key => $value) {
            if ($shopid != 'all' && $shopid > 0) {
                $labels[] = (strlen($value['branchname']) > 13) ? substr($value['branchname'], 0 , 13) . "...":$value['branchname'];
            }else{
                $labels[] = (strlen($value['shopname']) > 13) ? substr($value['shopname'], 0 , 13) . "...":$value['shopname'];
            }
            $chartdata['data'][] = intval($value['cnt']);
        }
        // print_r($data);
        // exit();
        echo json_encode([
            'success' => true,
            'chartdata' => ['labels' => $labels, 'data' => $chartdata],
        ]);
    }

    public function export_pending_orders_data()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['po']['view'] == 1){
            $filter = json_decode($this->input->post('_filter'));
            $requestData = url_decode(json_decode($this->input->post('_search')));
            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = sanitize($filter->branchid);
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));
            
            $result = $this->model_orders->get_pending_orders_table($fromdate, $todate, $shopid, $branchid, $requestData, true);
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Pending Orders Report"));
            $this->audittrail->logActivity('Pending orders Report', $remarks, 'export', $this->session->userdata('username'));
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Pending Orders");
            $sheet->setCellValue('B2', "Filter: $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            $sheet->getStyle('B1')->getFont()->setBold(true);
            
            if ($this->session->sys_shop_id == 0) {
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                
                $sheet->setCellValue('A6', 'Date Ordered');
                $sheet->setCellValue('B6', 'Shop');
                $sheet->setCellValue('C6', 'Branch');
                $sheet->setCellValue('D6', 'Pending Order');
                $sheet->setCellValue('E6', 'Processing Order');
                $sheet->setCellValue('F6', 'Ready For Pickup');
                $sheet->setCellValue('G6', 'Booking Confirmed');
                $sheet->getStyle('A6:G6')->getFont()->setBold(true);
            } else {
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                
                $sheet->setCellValue('A6', 'Date Ordered');
                $sheet->setCellValue('B6', 'Branch');
                $sheet->setCellValue('C6', 'Pending Order');
                $sheet->setCellValue('D6', 'Processing Order');
                $sheet->setCellValue('E6', 'Ready For Pickup');
                $sheet->setCellValue('F6', 'Booking Confirmed');
                $sheet->getStyle('A6:F6')->getFont()->setBold(true);
            }

            // print_r($result['data']);
            $exceldata= array();
            $counter  = 7;
            foreach ($result['data'] as $key => $row) {
                $row[3] = (string) $row[3];
                $row[4] = (string) $row[4];
                $row[5] = (string) $row[5];
                $row[6] = (string) $row[6];
                if ($this->session->sys_shop_id > 0) array_forget($row, 1);
                $counter++; 
                $exceldata[] = $row;
            }

            $sheet->fromArray($exceldata, null, 'A7');

            $sheet->setCellValue('A'.$counter, ''); 
            $sheet->setCellValue('B'.$counter, ''); 
            $sheet->setCellValue('C'.$counter, 'TOTAL'); 
            $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
            $sheet->getStyle('D'.$counter)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
            $sheet->setCellValue('D'.$counter, number_format($result['recordsTotal'],0));  
            $sheet->setCellValue('E'.$counter, number_format($result['t_onprocess'],0));  
            $sheet->setCellValue('F'.$counter, number_format($result['t_pickup'],0));  
            $sheet->setCellValue('G'.$counter, number_format($result['t_confirmed'],0));  

            $writer = new Xlsx($spreadsheet);
            $filename = 'Pending Orders Report' . date('Y/m/d');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
            header('Cache-Control: max-age=0');
            ob_end_clean();
            return $writer->save('php://output');
            exit();
        } else {
            $this->load->view('error_404');
        }
    }

}