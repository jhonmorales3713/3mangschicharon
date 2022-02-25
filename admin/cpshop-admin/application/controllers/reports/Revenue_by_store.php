<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class revenue_by_store extends CI_Controller {
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
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['rbsr']['view'] == 1){
            //start - for restriction of views
            // print_r($_GET);
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $user_id = $this->session->userdata('id'); $shops = [];
            $shopid = $this->model_shops->get_sys_shop($user_id);
            $shops = $this->model_shops->get_shop_opts_oderbyname();
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
            $this->load->view('reports/revenue_by_store_report',$data_admin);
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

    public function rbs_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbsr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $filtertype = sanitize($this->input->post('filtertype'));
            $pmethodtype = sanitize($this->input->post('pmethodtype'));
            // print_r("hello");
            // exit();

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            if ($shopid == 0) {
                $data = $this->model_sales_report->get_rBS_reports_data($fromdate,$todate,$shopid,$filtertype,$pmethodtype,$_REQUEST);
            } else {
                $data = $this->model_sales_report->get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,$filtertype,$pmethodtype,$_REQUEST);
            }
            
            // print_r($data);
            // exit();
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                // print_r($data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array());
                echo json_encode($data);
            }

        }else{
            $this->load->view('error_404');
        }
    }

    public function export_rbs_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbsr']['view'] == 1){
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

            if ($shopid > 0) {
                $result = $this->model_sales_report->get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,"summary",$pmethodtype,$requestData,true);
            } else {
                $result = $this->model_sales_report->get_rBS_reports_data($fromdate,$todate,$shopid,"summary",$pmethodtype,$requestData,true);
            }
            $pm_type = array(
                '' => 'All Payment Method', 'op' => 'Online Purchases', 'mp' => 'Manual Purchases'
            )[$pmethodtype];
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Revenue by Store", ['Payment Method' => $pm_type]));
            $this->audittrail->logActivity('Revenue by Store Report', $remarks, 'export', $this->session->userdata('username')); 

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Revenue By Store Report");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "Payment Method: $pm_type");
            $sheet->setCellValue('B4', "$fromdate to $todate");
            
            // SUMMARY
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);

            $sheet->setCellValue('A6', 'SUMMARY');
            $sheet->setCellValue('A7', 'Payment Date');
            $sheet->setCellValue('B7', 'Shop');
            if ($shopid > 0) {
                $sheet->setCellValue('C7', 'Branch');
                $sheet->setCellValue('D7', 'Total Orders');
                $sheet->setCellValue('E7', 'Amount');
            } else {
                $sheet->setCellValue('C7', 'Total Orders');
                $sheet->setCellValue('D7', 'Amount');
            }
            

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6')->getFont()->setBold(true);
            $sheet->getStyle('A7:E7')->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                if ($shopid > 0) {
                    $resultArray = [
                        $row[0], $row[2], $row[3], $row[7], $row[8], 
                    ];
                } else {
                    $resultArray = [
                        $row[0], $row[2], $row[7], $row[8], 
                    ];
                }
                $exceldata[] = $resultArray;
            }

            $sheet->fromArray($exceldata, null, 'A8');
            $row_count = count($exceldata)+8;
            $yKey = ($shopid > 0) ? 'E':'D';
            for ($i=8; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            // LOGS
            $logs_row = $row_count+4;
            $title_row = $row_count+3;
            if ($shopid > 0) {
                $result = $this->model_sales_report->get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,"logs",$pmethodtype,$requestData,true);
            } else {
                $result = $this->model_sales_report->get_rBS_reports_data($fromdate,$todate,$shopid,"logs",$pmethodtype,$requestData,true);
            }
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);

            $sheet->setCellValue("A$title_row", 'LOGS');
            $sheet->setCellValue("A$logs_row", 'Payment Date');
            $sheet->setCellValue("B$logs_row", 'Order Date');
            $sheet->setCellValue("C$logs_row", 'Shop');
            if ($shopid > 0) {
                $sheet->setCellValue("D$logs_row", 'Branch');
                $sheet->setCellValue("E$logs_row", 'Reference Number');
                $sheet->setCellValue("F$logs_row", 'Customer');
                $sheet->setCellValue("G$logs_row", 'Payment Method');
                $sheet->setCellValue("H$logs_row", 'Amount');
            } else {
                $sheet->setCellValue("D$logs_row", 'Reference Number');
                $sheet->setCellValue("E$logs_row", 'Customer');
                $sheet->setCellValue("F$logs_row", 'Payment Method');
                $sheet->setCellValue("G$logs_row", 'Amount');
            }
            

            $sheet->getStyle("A$title_row")->getFont()->setBold(true);
            $sheet->getStyle("A$logs_row:H$logs_row")->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                if ($shopid > 0) {
                    $resultArray = array_except($row, 7);
                } else {
                    $row = array_except($row, 3);
                    $resultArray = array_except($row, 6);
                }
                
                $exceldata[] = $resultArray;
            }

            $logs_row_start = $logs_row+1;
            $sheet->fromArray($exceldata, null, "A$logs_row_start");
            $row_count = count($exceldata)+$logs_row_start;
            $yKey = ($shopid > 0) ? 'H':'G';
            for ($i=$logs_row_start; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Revenue By Store Report ' . date('Y/m/d');
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

    public function rbs_chart_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbsr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $pmethodtype = sanitize($this->input->post('pmethodtype'));
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            if ($shopid > 0) {
                $data = $this->rbbchartdata($fromdate, $todate, $shopid, $branchid, $pmethodtype);
            } else {
                $data = $this->rbschartdata($fromdate, $todate, $shopid, $pmethodtype);
            }
            
            generate_json($data);
            exit();
        }else{
            $data = array("success" => 0, "chartdata" => array());
            generate_json($data);
            exit();
        }
    }

    private function rbschartdata($fromdate, $todate, $shopid, $pmethodtype)
    {
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");
        $is_date_equal = ($fromdate == $todate) ? true:false;
        
        $rbs1 = $this->model_sales_report->revenueByStore($fromdate, $todate, $shopid, $pmethodtype);
        $rbs2 = $this->model_sales_report->revenueByStore($new_date, $prev_to, $shopid, $pmethodtype);

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

        // get all shop names
        $keys = array_pluck($rbs2, 'shopname');
        $data2 = []; $setted_key = [];
        if (count($rbs2) > 0) {
            for ($i=0; $i < count($rbs1); $i++) {
                // set key(0,1,2,...) to $key to know where to push
                $key = array_search($rbs1[$i]['shopname'], $keys);
                if (in_array($rbs1[$i]['shopname'], $keys)) {
                    $data2[] = ($key >= 0 && !in_array($key, $setted_key)) ? $rbs2[$key]:['total_amount'=>0];
                    // compile all shopnames that is in data1
                    $setted_key[] = $key;
                }else{
                    $data2[] = ['total_amount'=>0];
                }
            }
        }else{
            $data2 = array_fill(0, count($rbs1), ['total_amount'=>0]);
        }

        $data = [
            "success" => true,
            "chartdata" => [
                'legend' => $legend,
                'shopnames' => array_pluck($rbs1, 'shopname'),
                'data1' => array_pluck($rbs1, 'total_amount'),
                'data2' => array_pluck($data2, 'total_amount'),
                'data' => [
                    $rbs1, $rbs2
                ]
            ]
        ];

        return $data;
    }

    public function rbb_index($token = ""){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['rbbr']['view'] == 1){
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
                $branches = $this->model_branch->get_branch_options($shopid);
            }elseif ($shopid > 0) {
                $shops = $this->model_branch->get_branch_options($shopid);
                $branches = [];
            }
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation,
                'shops' => $shops,
                'branches' => $branches,
                'shop' => $shopid,
                'branch' => $this->session->userdata('branchid'),
                'setDate' => [
                    'fromdate' => (isset($_GET['fromdate'])) ? $_GET['fromdate']:null,
                    'todate'   => (isset($_GET['todate'])) ? $_GET['todate']:null
                ]
            );
            // end - data to be used for views
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/revenue_by_branch_report',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function rbb_chart_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbbr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $pmethodtype = sanitize($this->input->post('pmethodtype'));
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->rbbchartdata($fromdate, $todate, $shopid, $branchid, $pmethodtype);
            generate_json($data);
            exit();
        }else{
            $data = array("success" => 0, "chartdata" => array());
            generate_json($data);
            exit();
        }
    }

    private function rbbchartdata($fromdate, $todate, $shopid, $branchid, $pmethodtype)
    {
        $rbs1 = $this->model_sales_report->revenueByBranch($fromdate, $todate, $shopid, $branchid, $pmethodtype);
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");
        $rbs2 = $this->model_sales_report->revenueByBranch($new_date, $prev_to, $shopid, $branchid, $pmethodtype);
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

        // get all shop names
        $keys = array_pluck($rbs2, 'branchname');
        $data2 = []; $setted_key = [];
        if (count($rbs2) > 0) {
            for ($i=0; $i < count($rbs1); $i++) {
                // set key(0,1,2,...) to $key to know where to push
                $key = array_search($rbs1[$i]['branchname'], $keys);
                if (in_array($rbs1[$i]['branchname'], $keys)) {
                    $data2[] = ($key >= 0 && !in_array($key, $setted_key)) ? $rbs2[$key]:['amount'=>0];
                    // compile all branchnames that is in data1
                    $setted_key[] = $key;
                }else{
                    $data2[] = ['amount'=>0];
                }
            }
        }else{
            $data2 = array_fill(0, count($rbs1), ['amount'=>0]);
        }

        $data = [
            "success" => true,
            "chartdata" => [
                'legend' => $legend,
                'branchnames' => array_pluck($rbs1, 'branchname'),
                'data1' => array_pluck($rbs1, 'amount'),
                'data2' => array_pluck($data2, 'amount'),
                'data' => [
                    $rbs1, $rbs2
                ]
            ]
        ];
        return $data;
    }
    
    public function rbb_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbbr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $filtertype = sanitize($this->input->post('filtertype'));
            $pmethodtype = sanitize($this->input->post('pmethodtype'));
            // print_r("hello");
            // exit();

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_sales_report->get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,$filtertype,$pmethodtype,$_REQUEST);
            // print_r($data);
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                // print_r($data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array());
                echo json_encode($data);
            }

        }else{
            $this->load->view('error_404');
        }
    }

    public function export_rbb_data(){
        $this->load->model('shop_branch/Model_shopbranch');
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbbr']['view'] == 1){
            // print_r($this->input->post());
            // exit();
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = sanitize($filter->branchid);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_sales_report->get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,"summary",$requestData,true);
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Revenue by Branch"));
            $this->audittrail->logActivity('Revenue by Branch Report', $remarks, 'export', $this->session->userdata('username')); 

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Revenue By Branch Report");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            
            // SUMMARY
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);

            $sheet->setCellValue('A6', 'SUMMARY');
            $sheet->setCellValue('A7', 'Payment Date');
            $sheet->setCellValue('B7', 'Branch Name');
            $sheet->setCellValue('C7', 'Total Orders');
            $sheet->setCellValue('D7', 'Amount');

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6')->getFont()->setBold(true);
            $sheet->getStyle('A7:D7')->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                $resultArray = [
                    $row[0], $row[2], $row[6], $row[7], 
                ];
                $exceldata[] = $resultArray;
            }

            $sheet->fromArray($exceldata, null, 'A8');
            $row_count = count($exceldata)+8;
            for ($i=8; $i <= $row_count; $i++) {
                $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            // LOGS
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(20);

            $logs_row = $row_count+4;
            $title_row = $row_count+3;
            $result = $this->model_sales_report->get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,"logs",$requestData,true);
            $sheet->setCellValue("A$title_row", 'LOGS');
            $sheet->setCellValue("A$logs_row", 'Payment Date');
            $sheet->setCellValue("B$logs_row", 'Order Date');
            $sheet->setCellValue("C$logs_row", 'Branch Name');
            $sheet->setCellValue("D$logs_row", 'Reference #');
            $sheet->setCellValue("E$logs_row", 'Customer');
            $sheet->setCellValue("F$logs_row", 'Payment Method');
            $sheet->setCellValue("G$logs_row", 'Amount');

            $sheet->getStyle("A$title_row")->getFont()->setBold(true);
            $sheet->getStyle("A$logs_row:G$logs_row")->getFont()->setBold(true);

            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                $resultArray = array_except($row, 6);
                $exceldata[] = $resultArray;
            }

            $logs_row_start = $logs_row+1;
            $sheet->fromArray($exceldata, null, "A$logs_row_start");
            $row_count = count($exceldata)+$logs_row_start;
            for ($i=$logs_row_start; $i <= $row_count; $i++) {
                $sheet->getStyle("G$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Revenue By Branch Report ' . date('Y/m/d');
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

    public function rbl_index($token = "")
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['rbl']['view'] == 1){
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
                ],
                'rbl_filter' => (isset($_GET['rbl_location'])) ? $_GET['rbl_location']:'city'
            );
            // end - data to be used for views
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/revenue_by_location_report',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function rbl_data()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbl']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $filtertype = sanitize($this->input->post('filtertype'));
            $location = sanitize($this->input->post('location'));
            // print_r("hello");
            // exit();

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_sales_report->get_rBL_reports_data($fromdate,$todate,$shopid,$branchid,$filtertype,$location,$_REQUEST);
            // print_r($data);
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                // print_r($data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array());
                echo json_encode($data);
            }

        }else{
            $this->load->view('error_404');
        }
    }

    public function rbl_chart_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbl']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $pmethodtype = sanitize($this->input->post('pmethodtype'));
            $filtertype = sanitize($this->input->post('location'));
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $rbl = $this->model_sales_report->revenueByLocation($fromdate, $todate, $shopid, $branchid, $pmethodtype, $filtertype);
            $is_date_equal = ($fromdate == $todate) ? true:false;

            if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
                $legend = 'Today';
            } elseif ($is_date_equal) {
                $legend = date_format(date_create($fromdate), 'M d, Y');
            } else {
                $legend = date_format(date_create($fromdate), 'M d') ." - ". date_format(date_create($todate), 'M d, Y');
            }

            // get all shop names
            // print_r($rbl);
            // exit();
            $data = [];
            $dataset = [
                'label' => $legend,
                'backgroundColor' => get_CoolorsHex(),
                'data' => [],
            ];
            $labels = $rbl['labels'];
            foreach ($rbl['data'] as $value) {
                $data[] = intval($value['total_amount']);
            }
            $result = runPieChartCalc($labels, $data);
            extract(runPieChartCalc($result['labels'], $result['data']));
            $cnt = count($data);
            if (in_array('Others', $labels)) {
                $data[$cnt - 1] = intval($data[$cnt - 1]/ $cnt);
            }
            $dataset['data'] = $data;
            $json_data = [
                "success" => 1,
                "chartdata" => [
                    'labels' => $labels,
                    'dataset' => [$dataset],
                ]
            ];
            generate_json($json_data);
            exit();
        }else{
            $data = array("success" => 0, "chartdata" => array());
            generate_json($data);
            exit();
        }
    }

    public function export_rbl_data(){
        $this->load->model('shop_branch/Model_shopbranch');
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rbl']['view'] == 1){
            // print_r($this->input->post());
            // exit();
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = sanitize($filter->branchid);
            $location = sanitize($filter->location);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_sales_report->get_rBL_reports_data($fromdate,$todate,$shopid,$branchid,"summary",$location,$requestData,true);
            
            $flocation = array("city" => "City", "prov" => "Province", "reg" => "Region")[sanitize($filter->location)];
            $filter=['Location' => $flocation];
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, 'Revenue by Location', $filter));
            $this->audittrail->logActivity('Revenue by Location Report', $remarks, 'export', $this->session->userdata('username')); 

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Revenue By Location Report");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "Location: ".title_case($flocation));
            $sheet->setCellValue('B4', "$fromdate to $todate");

            // SUMMARY
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);

            $sheet->setCellValue('A6', 'SUMMARY');
            $sheet->setCellValue('A7', 'Payment Date');
            $sheet->setCellValue('B7', 'Location');
            $sheet->setCellValue('C7', 'Amount');

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6')->getFont()->setBold(true);
            $sheet->getStyle('A7:D7')->getFont()->setBold(true);

            // print_r($result['data']);
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                $row = array_except($row, 1);
                $row = array_except($row, 2);
                $exceldata[] = $row;
            }

            $sheet->fromArray($exceldata, null, 'A8');
            $row_count = count($exceldata)+8;
            for ($i=8; $i <= $row_count; $i++) {
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            // LOGS
            $logs_row = $row_count+4;
            $title_row = $row_count+3;
            $result = $this->model_sales_report->get_rBL_reports_data($fromdate,$todate,$shopid,$branchid,"logs",$location,$requestData,true);
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);

            $sheet->setCellValue("A$title_row", 'LOGS');
            $sheet->setCellValue("A$logs_row", 'Payment Date');
            $sheet->setCellValue("B$logs_row", 'Shop');
            if ($shopid > 0) {
                $sheet->setCellValue("C$logs_row", 'Branch');
                $sheet->setCellValue("D$logs_row", 'Location');
                $sheet->setCellValue("E$logs_row", 'Amount');
            } else {
                $sheet->setCellValue("C$logs_row", 'Location');
                $sheet->setCellValue("D$logs_row", 'Amount');
            }
            

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle("A$title_row")->getFont()->setBold(true);
            $sheet->getStyle("A$logs_row:E$logs_row")->getFont()->setBold(true);

            // print_r($result['data']);
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                if ($shopid == 0) {
                    $row = array_except($row, 2);
                }
                $exceldata[] = $row;
            }

            $logs_row_start = $logs_row+1;
            $sheet->fromArray($exceldata, null, "A$logs_row_start");
            $row_count = count($exceldata)+$logs_row_start;
            $yKey = ($shopid > 0) ? 'E':'D';
            for ($i=$logs_row_start; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Revenue By Location Report ' . date('Y/m/d');
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
}
