<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class total_sales extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('reports/model_sales_report');
        $this->load->model('shops/Model_shops');
        $this->load->model('shop_branch/Model_shopbranch', 'model_branch');
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
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['tsr']['view'] == 1){
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
                $shops = $this->model_branch->get_branch_options($shopid);
            }
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation,
                'shops' => $shops,
                'shop' => $shopid,
                'branch' => $this->session->userdata('branchid'),
                'setDate' => [
                    'fromdate' => (isset($_GET['fromdate'])) ? $_GET['fromdate']:null,
                    'todate'   => (isset($_GET['todate'])) ? $_GET['todate']:null
                ]
            );
            // end - data to be used for views
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/total_sales_report',$data_admin);
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

    public function total_sales_data_backup(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['tsr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $filtertype = sanitize($this->input->post('filtertype'));
            $pmethodtype = sanitize($this->input->post('pmethodtype'));

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_sales_report->get_totalsales_data($fromdate,$todate,$shopid,$branchid,$filtertype,$pmethodtype,$_REQUEST);
            // print_r($data);
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array(), "total_amount" => "0.00");
                echo json_encode($data);
            }

        }else{
            $this->load->view('error_404');
        }
    }

    public function total_sales_data(){
        $this->isLoggedIn();    
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));   
        $type = $this->input->post('type');
        $pmethodtype = $this->input->post('pmethodtype');
        
        if($this->session->sys_shop_id == 0){
            $shop_id = $this->input->post('shop_id');
            $branch_id = $this->input->post('branch_id');
        }
        else{
            $shop_id = $this->session->sys_shop_id;

            if($this->session->branchid != 0){
                $branch_id = $this->session->branchid;
            }
            else{
                $branch_id = $this->input->post("branch_id");
            }
        }       

        $data = $this->model_sales_report->get_totalsales_data($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype);
        echo json_encode($data);
    }

    public function export_totalsales_data_backup(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['tsr']['view'] == 1){
            // print_r($this->input->post());
            // exit();
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = sanitize($filter->branchid);
            $pmethodtype = sanitize($filter->pmethodtype);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_sales_report->get_totalsales_data($fromdate,$todate,$shopid,$branchid,"summary",$pmethodtype,$requestData,true);
            $pm_type = array(
                '' => 'All Payment Method', 'op' => 'Online Purchases', 'mp' => 'Manual Purchases'
            )[$pmethodtype];
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Total Sales Report", ['Payment Method' => $pm_type]));
            $this->audittrail->logActivity('Total Sales', $remarks, 'export', $this->session->userdata('username'));

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Total Sales Report");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "Payment Method: $pm_type");
            $sheet->setCellValue('B4', "$fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            
            // get op/mp arr vals
            $op_data = $this->model_sales_report->export_onlinePurchases($fromdate, $todate, $shopid, $branchid);
            $mp_data = $this->model_sales_report->export_manualPurchases($fromdate, $todate, $shopid, $branchid);
            // set to make comparison of data
            $op = $op_data['total'];
            $mp = $mp_data['total'];
            $op = ($op == 0 || $op == null || $op == '') ? 0:$op;
            $mp = ($mp == 0 || $mp == null || $mp == '') ? 0:$mp;
            $total = number_format($op + $mp, 2);

            $sheet->mergeCells('D5:E5');
            $sheet->fromArray(['Sales','','Total'], null, 'A5');
            $sheet->mergeCells('A6:B6');
            $sheet->mergeCells('A7:B7');
            $sheet->mergeCells('A8:B8');
            
            $sheet->fromArray([
                ['Total Amount','', $total],
                ['Online Purchases','', number_format($op, 2)],
                ['Manual Purchases','', number_format($mp, 2)],
            ], null, 'A6');
            
            for ($i=6; $i < 8; $i++) {
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            // SUMMARY

            $sheet->setCellValue("A10", 'SUMMARY');
            $sheet->setCellValue('A11', 'Payment Date');
            $sheet->setCellValue('B11', 'Shop');
            if ($shopid > 0) {
                $sheet->setCellValue('C11', 'Branch');
                $sheet->setCellValue('D11', 'Sales Count');
                $sheet->setCellValue('E11', 'Amount');
            } else {
                $sheet->setCellValue('C11', 'Sales Count');
                $sheet->setCellValue('D11', 'Amount');
            }
            

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A5:E5')->getFont()->setBold(true);
            $sheet->getStyle('A10')->getFont()->setBold(true);
            $sheet->getStyle('A11:E11')->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                if ($shopid > 0) {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[2],
                        '3' => $row[3],
                        '4' => $row[6],
                        '5' => $row[7],
                    );
                } else {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[2],
                        '3' => $row[6],
                        '4' => $row[7],
                    );
                }
                
                $exceldata[] = $resultArray;
            }

            $sheet->fromArray($exceldata, null, 'A12');

            $last_row = count($result['data'])+12;
            $sheet->mergeCells("A$last_row:B$last_row");
            if ($shopid > 0) {
                $sheet->fromArray([
                    'Total','','','',$result['total_amount']
                ], null, "A$last_row");
            } else {
                $sheet->fromArray([
                    'Total','','',$result['total_amount']
                ], null, "A$last_row");
            }
            $row_count = count($exceldata)+12;
            $yKey = ($shopid > 0) ? 'E':'D';
            for ($i=12; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            // LOGS

            $logs_row = $row_count+4;
            $title_row = $row_count+3;
            $result = $this->model_sales_report->get_totalsales_data($fromdate,$todate,$shopid,$branchid,"logs",$pmethodtype,$requestData,true);

            $sheet->setCellValue("A$title_row", 'LOGS');
            $sheet->setCellValue("A$logs_row", 'Payment Date');
            $sheet->setCellValue("B$logs_row", 'Date Ordered');
            $sheet->setCellValue("C$logs_row", 'Shop');
            if ($shopid > 0) {
                $sheet->setCellValue("D$logs_row", 'Branch');
                $sheet->setCellValue("E$logs_row", 'Reference Number');
                $sheet->setCellValue("F$logs_row", 'Payment Method');
                $sheet->setCellValue("G$logs_row", 'Amount');
            } else {
                $sheet->setCellValue("D$logs_row", 'Reference Number');
                $sheet->setCellValue("E$logs_row", 'Payment Method');
                $sheet->setCellValue("F$logs_row", 'Amount');
            }
            

            $sheet->getStyle("A$title_row")->getFont()->setBold(true);
            $sheet->getStyle("A$logs_row:G$logs_row")->getFont()->setBold(true);
            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                if ($shopid > 0) {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[1],
                        '3' => $row[2],
                        '4' => $row[3],
                        '5' => $row[4],
                        '6' => $row[5],
                        '7' => $row[7],
                    );
                } else {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[1],
                        '3' => $row[2],
                        '4' => $row[4],
                        '5' => $row[5],
                        '6' => $row[7],
                    );
                }
                $exceldata[] = $resultArray;
            }

            $logs_row_start = $logs_row+1;
            $sheet->fromArray($exceldata, null, "A$logs_row_start");

            $last_row = count($result['data'])+$logs_row_start;
            $row_count = count($exceldata)+$logs_row_start;
            $yKey = ($shopid > 0) ? 'E':'D';
            for ($i=$logs_row_start; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Total Sales Report ' . date('Y/m/d');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
            header('Cache-Control: max-age=0');
            ob_end_clean();

            return $writer->save('php://output');
            exit();
            // echo json_encode($data);

        }else{
            $this->load->view('error_404');
        }
    }

    public function export_totalsales_data(){
        $this->isLoggedIn();    

        $filters = json_decode($this->input->post('_filters'));
        $fromdate = sanitize($filters->fromdate);
        $todate = sanitize($filters->todate);    
        $pmethodtype = $filters->pmethodtype; 
        $type = $filters->type;    

        if($this->session->sys_shop_id == 0){
            $shop_id = $filters->shop_id;
            if($shop_id != "all"){
                $branch_id = $filters->branch_id;
            }
            else{
                $branch_id = null;
            }    
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            if($this->session->branchid != 0){
                $branch_id = $this->session->branchid;
            }
            else{
                $branch_id = $filters->branch_id;
            }            
        } 

        $total_sales = $this->model_sales_report->get_totalsales_data($fromdate,$todate,$shop_id,$branch_id,$type,$pmethodtype,true);   

        $fromshop = "";

        if($shop_id != 0 && $shop_id != "0" && $shop_id != "all"){
            $fromshop.=" with filters Shop = '".$this->model_sales_report->getShopName($shop_id)."'";
        }

        $remarks = 'Total Sales has been exported into excel'.$fromshop.', Dated '.$fromdate.' to '.$todate;
        $this->audittrail->logActivity('Total Sales Report', $remarks, 'export', $this->session->userdata('username'));     


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if($type == "summary"){
            $sheet->setCellValue('B1', 'Total Sales (Summary)');
        }
        else{
            $sheet->setCellValue('B1', 'Total Sales (Logs)');
        }

        if($fromdate == $todate){
            $sheet->setCellValue('B2', $fromdate);
        }
        else{
            $sheet->setCellValue('B2', $fromdate.' - '.$todate);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        if($shop_id != 0){
            $sheet->setCellValue('A4', 'Shop Name');
            $sheet->getStyle('A4')->getFont()->setBold(true);
            $sheet->setCellValue('B4', $this->model_sales_report->getShopName($shop_id));
        }       

        if($type == "logs"){
            if($shop_id == 0){
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);                                
            }
            else{                                
                if($branch_id == 0){
                    $sheet->getColumnDimension('D')->setAutoSize(true);                    
                }
            }
        }
        else{            
            if($shop_id != "all"){
                $sheet->setCellValue('A5', 'Branch Name');
                $sheet->getStyle('A5')->getFont()->setBold(true);
                if($branch_id != "all"){
                    $sheet->setCellValue('B5', $this->model_sales_report->getBranchName_id($branch_id)); 
                }
                else{
                    $sheet->setCellValue('B5', "All Branches"); 
                }                
            }            
        }

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A7:F7')->getFont()->setBold(true);

        if($type == "summary"){
            $sheet->setCellValue('A7', 'Payment Date');
            $sheet->setCellValue('B7', 'Sales Count');
            $sheet->setCellValue('C7', 'Total Amount');
        }
        else{
            if($shop_id == 0){                         
                $sheet->setCellValue('A7', 'Payment Date');
                $sheet->setCellValue('B7', 'Shop Name');
                $sheet->setCellValue('C7', 'Branch Name');
                $sheet->setCellValue('D7', 'Reference Number');
                $sheet->setCellValue('E7', 'Sales Count');
                $sheet->setCellValue('F7', 'Total Amount');             
            }    
            else{
                $sheet->setCellValue('A7', 'Payment Date');                
                $sheet->setCellValue('B7', 'Branch Name');
                $sheet->setCellValue('C7', 'Reference Number');
                $sheet->setCellValue('D7', 'Sales Count');
                $sheet->setCellValue('E7', 'Total Amount');
            }
        }

        $exceldata= array();
        $counter  = 8;
        foreach ($total_sales['data'] as $key => $row) {

            if($type == "summary"){
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[4],
                    '3' => $row[5]
                );
            }
            else{
                if($shop_id == 0){
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[1],
                        '3' => $row[2],
                        '4' => $row[3],
                        '5' => $row[4],                            
                        '6' => $row[5]                            
                    );    
                }
                else{
                    $resultArray = array(
                        '1' => $row[0],                            
                        '2' => $row[2],
                        '3' => $row[3],
                        '4' => $row[4],                            
                        '5' => $row[5]                            
                    );
                }
            }
            $counter++;
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A8');

        if($type == "summary"){
            $sheet->getStyle('B'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('B'.$counter, $total_sales['total_orders']);   
            $sheet->getStyle('C'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('C'.$counter, $total_sales['total_amount']); 
        }
        else{
            if($shop_id == 0){                    
                $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('D'.$counter, $total_sales['total_orders']);   
                $sheet->getStyle('E'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('E'.$counter, $total_sales['total_amount']);                        
            }
            else{                    
                $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('D'.$counter, $total_sales['total_orders']);   
                $sheet->getStyle('E'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('E'.$counter, $total_sales['total_amount']);    
            }
        } 

        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {               
            if($type == "summary" || $this->session->branchid != 0){
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
            }
            else{
                if($this->session->sys_shop_id == 0){                    
                    $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);                        
                }
                else{                    
                    if($this->session->branchid == 0){
                        $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
                    }                    
                }
            }       
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Total Sales';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }  

    public function total_sales_report_chart_data(){
        $this->isLoggedIn();
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));
        $shopid = sanitize($this->input->post('shopid'));
        $branchid = sanitize($this->input->post('branchid'));

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");
        $is_date_equal = ($fromdate == $todate) ? true:false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = ['Today','Yesterday'];
        } elseif ($is_date_equal) {
            $legend = [
                date_format(date_create($fromdate), 'M d, Y'),date_format(date_create($new_date), 'M d, Y')
            ];
        } else {
            $legend = [
                date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y'),
                date_format(date_create($new_date), 'M d')." to ".date_format(date_create($prev_to), 'M d, Y'),
            ];
        }

        if($this->loginstate->get_access()['tsr']['view'] == 1){
            $totalsales1 = $this->model_sales_report->totalsales_view($fromdate,$todate, $shopid, $branchid);
            $totalsales2 = $this->model_sales_report->totalsales_view($new_date,$prev_to, $shopid, $branchid);

            // print_r($totalsales1);
            // print_r($totalsales2);
            $ts_amount1 = (count($totalsales1) > 0) ? array_sum(array_pluck($totalsales1, 'total_amount')):0;
            $ts_amount2 = (count($totalsales2) > 0) ? array_sum(array_pluck($totalsales2, 'total_amount')):0;
            // $total = $ts_amount1 + $ts_amount2;
    
            // get op/mp arr vals
            $op_data = $this->model_sales_report->onlinePurchases($new_date, $prev_to, $todate, $shopid, $branchid);
            $mp_data = $this->model_sales_report->manualPurchases($new_date, $prev_to, $todate, $shopid, $branchid);
            // set to make comparison of data
            $op = $op_data['curr']['total'];
            $mp = $mp_data['curr']['total'];
            $op = ($op == 0 || $op == null || $op == '') ? 0:$op;
            $mp = ($mp == 0 || $mp == null || $mp == '') ? 0:$mp;
            
            $op2 = $op_data['prev']['total'];
            $mp2 = $mp_data['prev']['total'];
            $op2 = ($op2 == 0 || $op2 == null || $op2 == '') ? 0:$op2;
            $mp2 = ($mp2 == 0 || $mp2 == null || $mp2 == '') ? 0:$mp2;

            if ($op !== 0 && $op2 !== 0) {
                $op_final_percent = abs(round((($op/$ts_amount1)-1)*100 - (($op2/$ts_amount2)-1) * 100, 2));
            } else {
                $op_final_percent = 100;
            }
            
            if ($mp !== 0 && $mp2 !== 0) {
                $mp_final_percent = abs(round((($mp/$ts_amount1)-1)*100 - (($mp2/$ts_amount2)-1) * 100, 2));
            } else {
                $mp_final_percent = 100;
            }
            // echo "$_op2 - $_mp2 <br/> $ts_percent - $ts_percent2";
            if ($ts_amount1 !== 0 && $ts_amount2 !== 0) {
                $ts_final_percent = abs(round((($ts_amount1/$ts_amount2) - 1) * 100, 2));
                if ($mp == 0 && $mp2 == 0) $op_final_percent = $ts_final_percent;
                if ($op == 0 && $op2 == 0) $mp_final_percent = $ts_final_percent;
            } else {
                $ts_final_percent = 100;
            }
            
            // set a date period for array
            $dates = [];
            $begin = new DateTime( $fromdate );
            $end = new DateTime( $todate );
            $end = $end->modify( '+1 day' );

            $interval = ($is_date_equal) ? new DateInterval('PT1H'):new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval ,$end);
            $date_format = ($is_date_equal) ? "H:00":"M d";

            foreach($daterange as $date){
                $dates[] = $date->format($date_format);
            }

            // set dates as array key
            $ts = array_fill_keys(array_keys(array_flip($dates)),array());
            data_set($ts, '*', ['data1' => ['total_amount' => 0],'data2' => ['total_amount' => 0]]);
            // push current data as data1
            foreach ($totalsales1 as $key => $sale) {
                $mmdd = date_format(date_create($sale['payment_date_time']), "$date_format");
                if ($ts[$mmdd]['data1']['total_amount'] > 0) {
                    $ts[$mmdd]['data1']['total_amount'] += $sale['total_amount'];
                } else {
                    $ts[$mmdd]['data1'] = $sale;
                }
            }

            if ($is_date_equal) {
                foreach ($totalsales2 as $key => $sale) {
                    $mmdd = date_format(date_create($sale['payment_date_time']), "$date_format");
                    if ($ts[$mmdd]['data2']['total_amount'] > 0) {
                        $ts[$mmdd]['data2']['total_amount'] += $sale['total_amount'];
                    } else {
                        $ts[$mmdd]['data2'] = $sale;
                    }
                }
                $dates = array_keys($ts);
            }else{
                $ctr = 0;
                foreach ($ts as $key => $value) {
                    // push comparer data as data2
                    if (isset($totalsales2[$ctr])) {
                        $ts[$key]['data2'] = $totalsales2[$ctr];
                    }
                    $ctr++;
                }
            }
            // remove first date
            $dates[0] = '';
            $cur_data = array_column(array_column($ts, 'data1'), 'total_amount');
            $pre_data = array_column(array_column($ts, 'data2'), 'total_amount');
            $step = get_StepSize($cur_data, $pre_data, 4);
            $result = [
                'success' => 1,
                'chartdata' => [
                    'ts' => [$cur_data, $pre_data],
                    'dates' => $dates,
                    'step' => $step,
                    'head' => [
                        'percent' => ($ts_amount1 >= $ts_amount2) ? "<i class='fa fa-arrow-up text-blue-400'></i> $ts_final_percent %":"<i class='fa fa-arrow-down text-red-400'></i> $ts_final_percent %",
                        'total' => number_format($ts_amount1, 2),
                        'pre_total' => number_format($ts_amount2, 2),
                    ],
                    'legend' => $legend,
                    'op' => [
                        'total' => number_format($op, 2),
                        'pre_total' => number_format($op2, 2),
                        'percent' => ($op >= $op2) ? "<i class='fa fa-arrow-up text-blue-400 text-lg'></i> $op_final_percent %":"<i class='fa fa-arrow-down text-red-400 text-lg'></i> $op_final_percent %",
                    ],
                    'mp' => [
                        'total' => number_format($mp, 2),
                        'pre_total' => number_format($mp2, 2),
                        'percent' => ($mp >= $mp2) ? "<i class='fa fa-arrow-up text-blue-400 text-lg'></i> $mp_final_percent %":"<i class='fa fa-arrow-down text-red-400 text-lg'></i> $mp_final_percent %",
                    ]
                ]
            ];

            generate_json($result);
            exit();
        }else{
            $data = array(
                "success" => 0, 
                "chartdata" => [
                    'ts' => [
                        0 => ['data1' => ['total_amount' => 0],'data2' => ['total_amount' => 0]],
                    ],
                    'dates' => [],
                    'head' => [
                        'percent' => "0 %",
                        'total' => "0.00"
                    ],
                    'legend' => $legend,
                    'op' => [
                        'percent' => '0 %',
                        'total' => "0.00",
                    ],
                    'mp' => [
                        'percent' => '0 %',
                        'total' => "0.00",
                    ],
                ]);
            generate_json($data);
            exit();
        }
    }
}
