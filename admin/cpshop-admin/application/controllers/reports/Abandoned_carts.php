<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class abandoned_carts extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_abandoned_carts');
        $this->load->model('reports/Model_sales_report');
        $this->load->model('shops/Model_shops');
        $this->load->model('shop_branch/Model_shopbranch', 'model_branch');
        $this->load->model('reports/Model_online_store_sessions','model_page_statistics');
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
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['tacr']['view'] == 1){
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
            $this->load->view('reports/abandoned_carts',$data_admin);
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

    public function abandoned_carts_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['tacr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            // print_r("$fromdate");
            // exit();
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->Model_abandoned_carts->get_atc_reports_data($fromdate,$todate,$shopid,$_REQUEST);/* current data */
            if (count($data['data']) > 0) {
                $data = array_merge(array("success" => 1), $data);
                echo json_encode($data);
            } else {
                $data = array("success" => 0, "data" => array(), "total_amount" => "0.00");
                echo json_encode($data);
            }
            // echo json_encode($this->get_abandoned_carts_data($fromdate, $todate, $shopid));
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_abandoned_carts_chart()
    {
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));
        $shopid = sanitize($this->input->post('shopid'));

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");
        $todate = date("Y-m-d", strtotime($todate));
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

        $abandoned1 = $this->Model_abandoned_carts->get_atc_reports_chart($fromdate,$todate,$shopid);/* current data */
        $abandoned2 = $this->Model_abandoned_carts->get_atc_reports_chart($new_date,$prev_to,$shopid);/* previous data */
        $labels = [];
        foreach (array_keys($abandoned1) as $value) {
            $labels[] = date_format(date_create($value), 'M d');
        }
        $labels[0] = '';
        // print_r($chart_data1);
        $computation = get_AbandonedCartsComputation($abandoned1, $abandoned2);
        $result = [
            'success' => true,
            'raw' => [
                'current' => $abandoned1,
                'previous' => $abandoned2,
            ],
            'chartdata' => [
                'computation' => $computation,
                'labels' => $labels,
                'legend' => $legend,
                'data' => [array_column($abandoned1, 'abandoned'), array_column($abandoned2, 'abandoned')],
                'totalData' => array_sum(array_merge(array_column($abandoned1, 'abandoned'), array_column($abandoned2, 'abandoned'))),
                'chartType' => ($is_date_equal) ? 'horizontalBar':'line',
            ]
        ];

        echo json_encode($result);
    }

    public function export_abandoned_carts_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['tacr']['view'] == 1){
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->Model_abandoned_carts->get_atc_reports_data($fromdate,$todate,$shopid,$requestData, true);/* current data */
            $shop_name = ($shopid == "all") ? "All Shops":$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];

            $filter = "";
            
            if($shopid != 0 && $shopid != "0" && $shopid != "all"){
                $filter.=" with filters Shop = '".$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname']."'";
            }            

            $remarks = 'Total Abandoned Carts has been exported into excel'.$filter.', Dated '.$fromdate.' to '.$todate;
            $this->audittrail->logActivity('Total Abandoned Carts Report', $remarks, 'export', $this->session->userdata('username'));

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Abandoned Carts");
            $sheet->setCellValue('B2', "Shop: $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);

            $sheet->setCellValue('A6', 'Date');
            $sheet->setCellValue('B6', 'Added To Cart');
            $sheet->setCellValue('C6', 'Sales');
            $sheet->setCellValue('D6', 'Abandoned');
            

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6:D6')->getFont()->setBold(true);
           

            //  echo "<pre>";
            //die(print_r($result['data']));
           // $sheet->setCellValue('D10', 0);
            $exceldata=  array();
         
             
            foreach ($result['data'] as $key => $row) {
            //$resultArray = ($filtertype == "summary") ? array_except($row, 4):;
                $exceldata[] = $row;
            }
      
            $sheet->fromArray($exceldata, 0, 'A7',true);
            
            $last = count($result['data']) + 7;
            
            $sheet->fromArray([
                'Total',$result['tfoot'][0],$result['tfoot'][1],$result['tfoot'][2]
            ], null, "A$last");

            $sheet->getStyle("A$last:D$last")->getFont()->setBold(true);
            $row_count = count($exceldata)+7;
            for ($i=7; $i < $row_count; $i++) {
                $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Abandoned Carts ' . date('Y/m/d');
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
