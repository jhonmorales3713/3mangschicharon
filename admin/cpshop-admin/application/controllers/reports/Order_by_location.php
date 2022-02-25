<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class order_by_location extends CI_Controller {

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

    public function index($token = "")
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['oblr']['view'] == 1){
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
                'oblr_filter' => (isset($_GET['oblr_filter'])) ? $_GET['oblr_filter']:'city'
            );
            // end - data to be used for views
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/order_by_location',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function oblr_data()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['oblr']['view'] == 1){
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

            $data = $this->model_sales_report->get_oblr_data($fromdate,$todate,$shopid,$branchid,$filtertype,$location,$_REQUEST);
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

    public function oblr_chart_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['oblr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            $filtertype = sanitize($this->input->post('location'));
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->model_sales_report->oblrChartData($fromdate, $todate, $shopid, $branchid, $filtertype);
            $is_date_equal = ($fromdate == $todate) ? true:false;

            if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
                $legend = 'Today';
            } elseif ($is_date_equal) {
                $legend = date_format(date_create($fromdate), 'M d, Y');
            } else {
                $legend = date_format(date_create($fromdate), 'M d') ." - ". date_format(date_create($todate), 'M d, Y');
            }

            // get all shop names
            // print_r($oblr);
            // exit();
            $data = [];
            $dataset = [
                'label' => $legend,
                'backgroundColor' => get_CoolorsHex(),
                'data' => [],
            ];
            // here
            $labels = $result['labels'];
            foreach ($result['data'] as $value) {
                $data[] = intval($value['cnt']);
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

    public function export_oblr_data(){
        $this->load->model('shop_branch/Model_shopbranch');
        $this->isLoggedIn();
        if($this->loginstate->get_access()['oblr']['view'] == 1){
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

            $result = $this->model_sales_report->get_oblr_data($fromdate,$todate,$shopid,$branchid,"summary",$location,$requestData,true);
            
            $flocation = array("city" => "City", "prov" => "Province", "reg" => "Region")[sanitize($filter->location)];
            $filter=['Location' => $flocation];
            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, 'Orders by Location', $filter));
            $this->audittrail->logActivity('Orders by Location Report', $remarks, 'export', $this->session->userdata('username')); 

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Orders By Location Report");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "Location: ".title_case($flocation));
            $sheet->setCellValue('B4', "$fromdate to $todate");

            // SUMMARY
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);

            $sheet->setCellValue('A6', 'SUMMARY');
            $report_headers = [
                'o_date' => 'Date Ordered', 
                'shop' =>'Shop', 
                'branch' => 'Branch', 
                'loc' => 'Location', 
                'orders' => 'Orders', 
                'ref_num' => 'Reference Number', 
                'o_stat' => 'Order Status'
            ];
            $summary_headers = $report_headers; $filtertype = "summary";
            // shop
            $is_shop = ($shopid == 'all' && $filtertype == 'summary') ? false:(($this->session->sys_shop_id > 0) ? false:(($shopid !== 'all' && $shopid > 0 && $filtertype == 'summary') ? true:(($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? true:(($shopid == 'all' && $filtertype == 'logs') ? true:false))));
            if (!$is_shop) $summary_headers = array_except($summary_headers, 'shop');
            // branch
            $is_branch = ($shopid !== 'all' && $shopid > 0) ? true:false;
            if (!$is_branch) $summary_headers = array_except($summary_headers, 'branch');
            // orders count
            $is_cnt = ($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? false:true;
            if (!$is_cnt) $summary_headers = array_except($summary_headers, 'orders');
            // ref num
            $is_refn = ($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? true:false;
            if (!$is_refn) $summary_headers = array_except($summary_headers, 'ref_num');
            // order status
            $is_ostat = ($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? true:false;
            if (!$is_ostat) $summary_headers = array_except($summary_headers, 'o_stat');

            // print_r($summary_headers);exit();
            
            $sheet->fromArray($summary_headers, null, 'A7');
            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6')->getFont()->setBold(true);
            $sheet->getStyle('A7:G7')->getFont()->setBold(true);

            // print_r($result['data']);
            // exit();
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                $arr = [];
                $arr[] = $row[0];
                if ($is_shop) $arr[] = $row[1];
                if ($is_branch) $arr[] = $row[2];
                $arr[] = $row[3];
                if ($is_cnt) $arr[] = $row[4];
                if ($is_refn) $arr[] = $row[5];
                if ($is_ostat) $arr[] = $row[6];
                $exceldata[] = $arr;
            }

            $sheet->fromArray($exceldata, null, 'A8');
            $row_count = count($exceldata)+8;

            // LOGS
            $logs_row = $row_count+4;
            $title_row = $row_count+3;
            $result = $this->model_sales_report->get_oblr_data($fromdate,$todate,$shopid,$branchid,"logs",$location,$requestData,true);

            $logs_headers = $report_headers; $filtertype = "logs";
            // echo $shopid;
            // echo "<br>";
            // echo $filtertype;
            // echo "<br>";
            // print_r($logs_headers);
            // echo "<br>";
            // shop
            $is_shop = ($shopid == 'all' && $filtertype == 'summary') ? false:(($this->session->sys_shop_id > 0) ? false:(($shopid !== 'all' && $shopid > 0 && $filtertype == 'summary') ? true:(($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? true:(($shopid == 'all' && $filtertype == 'logs') ? true:false))));
            if (!$is_shop) $logs_headers = array_except($logs_headers, 'shop');
            // branch
            // print_r($shopid); exit();
            $is_branch = ($shopid !== 'all' && $shopid > 0) ? true:false;
            if (!$is_branch) $logs_headers = array_except($logs_headers, 'branch');
            // orders count
            $is_cnt = ($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? false:true;
            if (!$is_cnt) $logs_headers = array_except($logs_headers, 'orders');
            // ref num
            $is_refn = ($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? true:false;
            if (!$is_refn) $logs_headers = array_except($logs_headers, 'ref_num');
            // order status
            $is_ostat = ($shopid !== 'all' && $shopid > 0 && $filtertype == 'logs') ? true:false;
            if (!$is_ostat) $logs_headers = array_except($logs_headers, 'o_stat');
            
            // print_r($logs_headers); exit();
            
            $sheet->setCellValue("A$title_row", 'LOGS');
            $sheet->fromArray($logs_headers, null, "A$logs_row");
            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle("A$title_row")->getFont()->setBold(true);
            $sheet->getStyle("A$logs_row:G$logs_row")->getFont()->setBold(true);

            // print_r($result['data']);
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                $arr = [];
                $arr[] = $row[0];
                if ($is_shop) $arr[] = $row[1];
                if ($is_branch) $arr[] = $row[2];
                $arr[] = $row[3];
                if ($is_cnt) $arr[] = $row[4];
                if ($is_refn) $arr[] = $row[5];
                if ($is_ostat) $arr[] = $row[6];
                $exceldata[] = $arr;
            }

            $logs_row_start = $logs_row+1;
            $sheet->fromArray($exceldata, null, "A$logs_row_start");

            $writer = new Xlsx($spreadsheet);
            $filename = 'Orders By Location Report ' . date('Y/m/d');
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