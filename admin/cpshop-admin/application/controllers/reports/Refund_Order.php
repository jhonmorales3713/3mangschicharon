<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Refund_Order extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('orders/model_refund_orders');
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

    public function summary($token = ""){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['rosum']['view'] == 1){
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
            $this->load->view('reports/refund_order_summary',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_RefundSummary_data()
    {
        $this->isLoggedIn();
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));
        $shopid = sanitize($this->input->post('shopid'));
        $branchid = sanitize($this->input->post('branchid'));
        $filtertype = sanitize($this->input->post('filtertype'));

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

        if($this->loginstate->get_access()['rosum']['view'] == 1){
            $cur_data = $this->model_refund_orders->get_refundorders_data($fromdate, $todate, $shopid, $branchid);
            $pre_data = $this->model_refund_orders->get_refundorders_data($new_date, $prev_to, $shopid, $branchid);

            $strDateInterval = ($is_date_equal) ? 'PT1H':'P1D';
            $date_format = ($is_date_equal) ? "H:i":"M d";
            // current date range
            // create date period
            $dates = createDateInterval($fromdate, $todate, $strDateInterval, $date_format);
            // set dates as array key
            $cur_val = array_fill_keys(array_keys($dates),array());
            data_set($cur_val, '*', ['amount' => 0]);

            foreach ($cur_data as $key => $row) {
                $mmdd = date_format(date_create($row['date']), "$date_format");
                $cur_val[$mmdd] = $row;
            }

            // previous date range
            // create date period
            $dates = createDateInterval($new_date, $prev_to, $strDateInterval, $date_format);
            // set dates as array key
            $pre_val = array_fill_keys(array_keys($dates),array());
            data_set($pre_val, '*', ['amount' => 0]);

            foreach ($pre_data as $key => $row) {
                $mmdd = date_format(date_create($row['date']), "$date_format");
                $pre_val[$mmdd] = $row;
            }

            $labels = array_keys($cur_val);
            $labels[0] = '';
            $total = array_sum(array_column($cur_val, 'amount'));
            $step = get_StepSize(array_column($cur_val, 'amount'), array_column($pre_val, 'amount'), 4);
            $data = [
                "success" => 1,
                "chartdata" => [
                    'step' => $step,
                    'total' => $total,
                    'cur_val' => array_column($cur_val, 'amount'),
                    'pre_val' => array_column($pre_val, 'amount'),
                    'legend' => $legend,
                    'dates' => $labels,
                ]
            ];

            generate_json($data);
            exit();
        } else {
            $data = array(
                "success" => 0, 
                "chartdata" => [
                    'ts' => [
                        0 => ['data1' => ['amount' => 0],'data2' => ['amount' => 0]],
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
        }
    }

    public function get_RefundSummary_table()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rosum']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $filtertype = sanitize($this->input->post('filtertype'));

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_refund_orders->get_refundorders_table($fromdate,$todate,$shopid,$branchid,$filtertype,$_REQUEST);
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

    public function export_refund_summary_table()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rosum']['view'] == 1){
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = sanitize($filter->branchid);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_refund_orders->get_refundorders_table($fromdate,$todate,$shopid,$branchid,'summary',$requestData,true);
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Refund Order Summary"));
            $this->audittrail->logActivity('Refund Order Summary', $remarks, 'export', $this->session->userdata('username'));

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Refund Order Summary");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);

            $report_headers = [
                'date' => 'Approval Date',
                'refnum' => 'Reference Number',
                'shop' => 'Shop',
                'branch' => 'Branch',
                'item' => 'Item Name',
                'qty' => 'Quantity',
                'price' => 'Item Price',
                'amount' => 'Amount',
            ];
            // SUMMARY
            $summary_headers = $report_headers;
            $sheet->setCellValue("A5", 'SUMMARY');
            $summary_headers = array_except($summary_headers, 'refnum');
            $summary_headers = array_except($summary_headers, 'qty');
            $summary_headers = array_except($summary_headers, 'item');
            $summary_headers = array_except($summary_headers, 'price');
            if ($shopid == 0) {
                $summary_headers = array_except($summary_headers, 'branch');
            }
            $sheet->fromArray($summary_headers, null, 'A6');

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A5')->getFont()->setBold(true);
            $sheet->getStyle('A6:E6')->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                if ($shopid > 0) {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[2],
                        '3' => $row[3],
                        '4' => number_format($row[7], 2),
                    );
                } else {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[2],
                        '3' => number_format($row[7], 2),
                    );
                }
                
                $exceldata[] = $resultArray;
            }

            $sheet->fromArray($exceldata, null, 'A7');

            $last_row = count($result['data'])+7;
            $sheet->mergeCells("A$last_row:B$last_row");
            $sheet->fromArray([
                'Total','', $result['total_amount']
            ], null, "A$last_row");
            $row_count = count($exceldata)+7;
            $yKey = ($shopid > 0) ? 'D':'C';
            for ($i=7; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            // LOGS

            $logs_row = $row_count+4;
            $title_row = $row_count+3;
            $result = $this->model_refund_orders->get_refundorders_table($fromdate,$todate,$shopid,$branchid,'logs',$requestData,true);

            $logs_headers = $report_headers;
            $sheet->setCellValue("A$title_row", 'LOGS');
            if (!is_numeric($shopid)) {
                $logs_headers = array_except($logs_headers, 'branch');
            }
            $sheet->fromArray($logs_headers, null, "A$logs_row");

            $sheet->getStyle("A$title_row")->getFont()->setBold(true);
            $sheet->getStyle("A$logs_row:H$logs_row")->getFont()->setBold(true);
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
                        '7' => number_format($row[6], 2),
                        '8' => number_format($row[7], 2),
                    );
                } else {
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[1],
                        '3' => $row[2],
                        '4' => $row[4],
                        '5' => $row[5],
                        '6' => number_format($row[6], 2),
                        '7' => number_format($row[7], 2),
                    );
                }
                $exceldata[] = $resultArray;
            }

            $logs_row_start = $logs_row+1;
            $sheet->fromArray($exceldata, null, "A$logs_row_start");

            $last_row = count($result['data'])+$logs_row_start;
            $row_count = count($exceldata)+$logs_row_start;
            $yKey1 = ($shopid > 0) ? 'G':'F';
            $yKey2 = ($shopid > 0) ? 'H':'G';
            for ($i=$logs_row_start; $i <= $row_count; $i++) {
                $sheet->getStyle("$yKey1$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("$yKey2$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Refund Order Summary ' . date('Y/m/d');
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


    public function status($token = ""){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['rostat']['view'] == 1){
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
            $this->load->view('reports/refund_order_status',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_RefundStatus_table()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rostat']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_refund_orders->get_refundorders_status_table($fromdate,$todate,$shopid,$_REQUEST);
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

    public function export_refund_stat_table()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['rostat']['view'] == 1){
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = 0;

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_refund_orders->get_refundorders_status_table($fromdate,$todate,$shopid,$requestData,true);
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Refund Order Status"));
            $this->audittrail->logActivity('Refund Order Status', $remarks, 'export', $this->session->userdata('username'));

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Refund Order Status");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);

            $report_headers = [
                'date' => 'Date',
                'req' => 'Waiting For Approval',
                'app' => 'Approved',
                'rej' => 'Rejected',
            ];
            // SUMMARY
            $summary_headers = $report_headers;
            $sheet->fromArray($summary_headers, null, 'A6');

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6:E6')->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                $exceldata[] = array_flatten($row);
            }

            $sheet->fromArray($exceldata, null, 'A7');

            $writer = new Xlsx($spreadsheet);
            $filename = 'Refund Order Summary ' . date('Y/m/d');
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

?>