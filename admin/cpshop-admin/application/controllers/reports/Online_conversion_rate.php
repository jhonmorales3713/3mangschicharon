<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class online_conversion_rate extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_online_conversion_rate');
        $this->load->model('reports/Model_sales_report');
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
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['oscrr']['view'] == 1){
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
            $this->load->view('reports/online_store_conversion_rate',$data_admin);
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

    public function oscr_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['oscrr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $filtertype = sanitize($this->input->post('filtertype'));
            // print_r("hello");
            // exit();

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->Model_online_conversion_rate->get_oscr_reports_data($fromdate,$todate,$shopid,$_REQUEST);
            // print_r($data);
            // exit();
            // $new_data = [];
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array());
                echo json_encode($data);
            }

        }else{
            $this->load->view('error_404');
        }
    }

    public function oscr_chart_data()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['oscrr']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $shopid = sanitize($this->input->post('shopid'));
            $filtertype = sanitize($this->input->post('filtertype'));
            // print_r("hello");
            // exit();
            $shopid = ($shopid == 0) ? 'all':$shopid;

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
            $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
            $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");
            $todate = date("Y-m-d", strtotime($todate));
            $is_date_equal = ($fromdate == $todate) ? true:false;
            
            if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
                $legend = 'Yesterday';
            } elseif ($is_date_equal) {
                $legend = date_format(date_create($new_date), 'M d, Y');
            } else {
                $legend = date_format(date_create($new_date), 'M d')." to ".date_format(date_create($prev_to), 'M d, Y');
            }

            $data = $this->Model_online_conversion_rate->get_oscr_reports_data($fromdate,$todate,$shopid);
            $data2 = $this->Model_online_conversion_rate->get_oscr_reports_data($new_date,$prev_to,$shopid);
            // print_r($data);
            $visits = array_column($this->Model_online_conversion_rate->get_visitors($new_date, $prev_to, $todate), 'visitors');
            $total_visits = isset($visits[0]) ? $visits[0]:0;
            $total_visits2 = isset($visits[1]) ? $visits[1]:0;
            
            if(count($data['data']) > 0){
                $c_data1 = $data['tfoot'];
                $c_data2 = $data2['tfoot'];

                $vis_growth = number_format(getPercentage($total_visits,$total_visits2));
                $visitor_growth = ($total_visits > $total_visits2) ? "<i class='fa fa-arrow-up text-blue-400'></i> $vis_growth %":"<i class='fa fa-arrow-down text-red-400'></i> $vis_growth %";

                $result = [
                    'success' => true,
                    'result' => 1,
                    'legend' => "from $legend",
                    'top_data' => [
                        $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2,false),
                        $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2,true),
                    ],
                    'data' => [
                        'visits' => [
                            number_format($total_visits),"",$visitor_growth
                        ],
                        'atc' => [
                            0 => number_format($c_data1[0]),
                            1 => $this->compareThenToString($c_data1[0], $c_data2[0], $total_visits, $total_visits2, false),
                            2 => $this->compareThenToString($c_data1[0], $c_data2[0], $total_visits, $total_visits2, true)
                        ],
                        'rc' => [
                            0 => number_format($c_data1[1]),
                            1 => $this->compareThenToString($c_data1[1], $c_data2[1], $total_visits, $total_visits2),
                            2 => $this->compareThenToString($c_data1[1], $c_data2[1], $total_visits, $total_visits2, true)
                        ],
                        'ptp' => [
                            0 => number_format($c_data1[2]),
                            1 => $this->compareThenToString($c_data1[2], $c_data2[2], $total_visits, $total_visits2),
                            2 => $this->compareThenToString($c_data1[2], $c_data2[2], $total_visits, $total_visits2, true)
                        ],
                        'sessions' => [
                            0 => number_format($c_data1[3]),
                            1 => $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2),
                            2 => $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2, true)
                        ]
                    ]
                ];
                echo json_encode($result);
            }else{
                $data = [
                    "success" => 1,
                    'result' => 0,
                    'top_data' => [0,"0 %"],
                    "data" => [
                        'atc' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                        'rc' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                        'ptp' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                        'sessions' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                    ]
                ];
                echo json_encode($data);
            }

        }else{
            $this->load->view('error_404');
        }
    }

    private function compareThenToString($needle, $compare, $divisor1, $divisor2, $is_2nd = false){
        $data1 = ($divisor1 == 0 || $needle == 0) ? 0:$needle/$divisor1 * 100;
        if ($is_2nd) {
            $data2 = ($divisor2 == 0 || $compare == 0) ? 0:$compare/$divisor2 * 100;
                // echo "$needle $divisor1 $compare $divisor2 <br>";
            $up = ($data1 > $data2) ? true:false;
            $data1 = getPercentage($needle, $compare);
            $data1 = number_format($data1);
            $result = ($up) ? "<i class='fa fa-arrow-up text-blue-400'></i> $data1 %":"<i class='fa fa-arrow-down text-red-400'></i> $data1 %";
        }
        else{
            $data1 = number_format($data1);
            $result = "$data1 %";
        }
        
        return $result;
    }

    public function export_oscr_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['oscrr']['view'] == 1){
            // print_r($this->input->post());
            // exit();
            $requestData = url_decode(json_decode($this->input->post("_search")));
            $filter = json_decode($this->input->post("_filter"));
            $data = json_decode($this->input->post("_data"));

            $fromdate = sanitize($filter->fromdate);
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            // $filtertype = sanitize($filter->filtertype);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $result = $this->Model_online_conversion_rate->get_oscr_reports_data($fromdate,$todate,$shopid,$requestData,true);
            $shop_name = ($shopid == "all") ? "All Shops":$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];
            
            $filter = "";
            
            if($shopid != 0 && $shopid != "0" && $shopid != "all"){
                $filter.=" with filters Shop = '".$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname']."'";
            }            

            $remarks = 'Online Store Conversion Rate has been exported into excel'.$filter.', Dated '.$fromdate.' to '.$todate;
            $this->audittrail->logActivity('Online Store Conversion Rate Report', $remarks, 'export', $this->session->userdata('username'));

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Online Store Convertion Rate");
            $sheet->setCellValue('B2', "Filter: $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);

            $sheet->setCellValue('A6', 'Date Created');
            $sheet->setCellValue('B6', 'Added To Cart');
            $sheet->setCellValue('C6', 'Reached Checkout');
            $sheet->setCellValue('D6', 'Proceeded to Payment');
            $sheet->setCellValue('E6', 'Sessions Converted');

            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6:E6')->getFont()->setBold(true);

            // print_r($result['data']);
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                // $resultArray = ($filtertype == "summary") ? array_except($row, 4):;
                $exceldata[] = $row;
            }

            $sheet->fromArray($exceldata, null, 'A7');

            $last = count($result['data']) + 7;
            
            $sheet->fromArray([
                'Total',$result['tfoot'][0],$result['tfoot'][1],$result['tfoot'][2],$result['tfoot'][3]
            ], null, "A$last");

            $sheet->getStyle("A$last:D$last")->getFont()->setBold(true);
            $row_count = count($exceldata)+7;
            for ($i=7; $i < $row_count; $i++) {
                $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Online Store Convertion Rate ' . date('Y/m/d');
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
