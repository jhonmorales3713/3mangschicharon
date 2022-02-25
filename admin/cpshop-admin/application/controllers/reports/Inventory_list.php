<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class inventory_list extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('orders/Model_orders', 'model_orders');
        $this->load->model('products/Model_products', 'model_products');
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
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['invlist']['view'] == 1){
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
                'shop' => $shopid,
                'branch' => $this->session->userdata('branchid'),
                'shops' => $shops,
                'setDate' => [
                    'fromdate' => (isset($_GET['fromdate'])) ? $_GET['fromdate']:'',
                    'todate'   => (isset($_GET['todate'])) ? $_GET['todate']:''
                ]
            );
            // end - data to be used for views
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/inventory_list',$data_admin);
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

    public function get_invlist_table()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['invlist']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $search_val = strtolower($this->input->post('search_val'));
            $shopid = sanitize($this->input->post('shopid'));
            $branchid = sanitize($this->input->post('branchid'));
            
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));
            
            $result = $this->model_products->get_inventory_table($fromdate, $todate, $search_val, $shopid, $branchid, $_REQUEST);

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

    public function get_inv_chart()
    {
        $this->isLoggedIn();
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));
        $search_val = strtolower($this->input->post('search_val'));
        $shopid = sanitize($this->input->post('shopid'));
        $branchid = sanitize($this->input->post('branchid'));
        
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        $is_date_equal = ($fromdate == $todate) ? true:false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = 'Today';
        } elseif ($is_date_equal) {
            $legend = date_format(date_create($fromdate), 'M d, Y');
        } else {
            $legend = date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y');
        }
        
        $result = $this->model_products->get_inv_chart($fromdate, $todate, $search_val, $shopid, $branchid);
        $labels = $result['labels'];
        $inventory = [];
        foreach ($result['data'] as $key => $value) {
            $inventory[] = $value['inventory'];
        }
        $result = runPieChartCalc($labels, $inventory);
        extract(runPieChartCalc($result['labels'], $result['data']));
        $cnt = count($data);
        if (isset($data[$cnt - 1])) {
            $data[$cnt - 1] = intval($data[$cnt - 1]/ $cnt);
        }
        $chartdata = [$data];

        echo json_encode([
            'success' => true,
            'legend' => $legend,
            'total' => $total,
            'chartdata' => ['labels' => $labels, 'data' => $chartdata, 'background' => get_CoolorsHex()],
        ]);
    }

    public function export_invlist_data()
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['invlist']['view'] == 1){
            $filter = json_decode($this->input->post('_filter'));
            $requestData = url_decode(json_decode($this->input->post('_search')));
            $fromdate = sanitize($filter->fromdate);
            $search_val = strtolower(sanitize($filter->search_val));
            $todate = sanitize($filter->todate);
            $shopid = sanitize($filter->shopid);
            $branchid = sanitize($filter->branchid);
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));
            $is_date_equal = ($fromdate == $todate) ? true:false;

            if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
                $legend = 'Today';
            } elseif ($is_date_equal) {
                $legend = date_format(date_create($fromdate), 'M d, Y');
            } else {
                $legend = date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y');
            }
            
            $result = $this->model_products->get_inventory_table($fromdate, $todate, $search_val, $shopid, $branchid, $requestData, true);
            $shop_name = ($shopid == "all") ? "All Shops":$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];

            extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, "Inventory List", ['Item Name' => $search_val]));
            $this->audittrail->logActivity('Inventory List Report', $remarks, 'export', $this->session->userdata('username'));
            
            $sheet_filter = "";
            if ($search_val !== '') $sheet_filter = "Filter = Item Name like '$search_val' in";
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Inventory List");
            $sheet->setCellValue('B2', "$sheet_filter Shop = $shop_name");
            $sheet->setCellValue('B3', "$fromdate to $todate");
            $sheet->getStyle('B1')->getFont()->setBold(true);

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
            
            $sheet->setCellValue('A6', 'Shop Name');
            $sheet->setCellValue('B6', 'Branch Name');
            $sheet->setCellValue('C6', 'Item Name');
            $sheet->setCellValue('D6', 'Unit Price');
            $sheet->setCellValue('E6', 'Start Date Quantity');
            $sheet->setCellValue('F6', "Added Quantity of ($legend)");
            $sheet->setCellValue('G6', 'No of Stocks');
            $sheet->setCellValue('H6', 'Inventory Price');
            $sheet->setCellValue('I6', "Sales Quantity of ($legend)");
            $sheet->setCellValue('J6', 'Total Sales');
            $sheet->getStyle('A6:J6')->getFont()->setBold(true);

            // print_r($result['data']);
            $exceldata= array();
            foreach ($result['data'] as $key => $row) {
                // echo gettype($row[8]);
                $exceldata[] = $row;
            }
            // exit();

            $sheet->fromArray($exceldata, null, 'A7');

            $row_count = count($exceldata)+7;
            if ($this->session->sys_shop_id > 0) {
                $myData = $sheet->rangeToArray("B6:K$row_count");
                $sheet->fromArray($myData, null, 'A6');
                for ($i=6; $i < $row_count; $i++) { 
                    $sheet->setCellValue("J$i", '');
                }
            }
            for ($i=7; $i <= $row_count; $i++) {
                if ($this->session->sys_shop_id == 0) {
                    $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("J$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                } else {
                    $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("I$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                }
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Inventory List' . date('Y/m/d');
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