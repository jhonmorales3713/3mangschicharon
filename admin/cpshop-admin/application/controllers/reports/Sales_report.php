<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class sales_report extends CI_Controller {
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['sr']['view'] == 1){
      //start - for restriction of views
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
        'shopid' => $shopid,
        'branch' => $this->session->userdata('branchid'),
      );
      // end - data to be used for views
      $this->load->view('includes/header',$data_admin);
      $this->load->view('reports/sales_report',$data_admin);
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

  public function salesreport_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['sr']['view'] == 1){
      $fromdate = sanitize($this->input->post('fromdate'));
      $todate = sanitize($this->input->post('todate'));
      $shopid = sanitize($this->input->post('shopid'));
      $branchid = sanitize($this->input->post('branchid'));
      $filtertype = sanitize($this->input->post('filtertype'));
      $pmethodtype = sanitize($this->input->post('pmethodtype'));

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));

      $data = $this->model_sales_report->get_sales_reports_data($fromdate,$todate,$shopid,$branchid,$filtertype,$pmethodtype,$_REQUEST);
      echo json_encode($data);

    }else{
      $this->load->view('error_404');
    }
  }

  public function export_salesreport_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['sr']['view'] == 1){
     
      $requestData = url_decode(json_decode($this->input->post("_search")));
      $filter = json_decode($this->input->post("_filters"));
      $data = json_decode($this->input->post("_data"));
  
      $fromdate = sanitize($filter->fromdate);
      $todate = sanitize($filter->todate);
      $shopid = sanitize($filter->shopid);
      $pmethodtype = sanitize($filter->pmethodtype);
      $branchid = sanitize($filter->branchid);
      $filtertype = sanitize($filter->filtertype);

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));

      $result = $this->model_sales_report->get_sales_reports_data($fromdate,$todate,$shopid,$branchid,$filtertype,$pmethodtype,$requestData,true);

      $shop_name = ($shopid == "all") ? "All Shops":$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];

      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('B1', "Sales Report");
      $sheet->setCellValue('B2', "Filter: $shop_name;$filtertype");
      $sheet->setCellValue('B3', "$fromdate to $todate");
      
      $sheet->getColumnDimension('A')->setWidth(20);
      $sheet->getColumnDimension('B')->setWidth(30);
      $sheet->getColumnDimension('C')->setWidth(20);
      $sheet->getColumnDimension('D')->setWidth(20);
      $sheet->getColumnDimension('E')->setWidth(10);
      $sheet->getColumnDimension('F')->setWidth(30);

      $sheet->mergeCells('A6:D6');$sheet->mergeCells('E6:F6');
      $sheet->mergeCells('A7:D7');$sheet->mergeCells('E7:F7');
      $sheet->mergeCells('A8:D8');$sheet->mergeCells('E8:F8');
      $sheet->mergeCells('A9:D9');$sheet->mergeCells('E9:F9');
      
      $sheet->fromArray([
        ['Total No. Of Transactions','','','', $result['total_transaction']],
        ['Total Paid Amount','','','', $result['total_paid']],
        ['Total Unpaid Amount','','','', $result['total_unpaid']],
        ['Total Transactions Amount','','','', $result['total_transaction_amount']]
      ], null, 'A6');

      for ($i=6; $i <= 9; $i++) {
        $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      }

      $sheet->setCellValue('A11', 'Shop');
      $sheet->setCellValue('B11', 'Customer');
      $sheet->setCellValue('C11', 'Order Date');
      $sheet->setCellValue('D11', 'Ref#');
      $sheet->setCellValue('E11', 'Amount');
      $sheet->setCellValue('F11', 'Status');

      $sheet->getStyle('B1')->getFont()->setBold(true);
      $sheet->getStyle('A11:F11')->getFont()->setBold(true);

      // print_r($query);
      $exceldata= array();
      foreach ($result['data'] as $key => $row) {
          $resultArray = array(
              '1' => $row[1],
              '2' => $row[2],
              '3' => $row[3],
              '4' => $row[4],
              '5' => $row[5],
              '6' => $row[6]
          );
          $exceldata[] = $resultArray;
      }

      $sheet->fromArray($exceldata, null, 'A12');
      $row_count = count($exceldata)+12;
      for ($i=12; $i < $row_count; $i++) {
        $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      }

      $writer = new Xlsx($spreadsheet);
      $filename = 'Sales Report ' . date('Y/m/d');
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
      header('Cache-Control: max-age=0');
      ob_end_clean();

      $filterstr = "and no filters. ";
      if ($filtertype !== "all" || $shop_name !== "All Shops") {
        $filterstr = "with filters: $filtertype";
        $filterstr .= ($shop_name == "All Shops") ? " in All shops" : " in '$shop_name', ";
      }
      $filterstr .= "Dated $fromdate to $todate";
      $this->audittrail->logActivity('Sales Report', "Sales Report has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
      
      return $writer->save('php://output');
      exit();
      // echo json_encode($data);

    }else{
      $this->load->view('error_404');
    }
  }

  public function sales_report_chart_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['sr']['view'] == 1){
      $fromdate = sanitize($this->input->post('fromdate'));
      $todate = sanitize($this->input->post('todate'));
      $shopid = sanitize($this->input->post('shopid'));
      $filtertype = sanitize($this->input->post('filtertype'));

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));

      $chart_data = $this->model_sales_report->get_sales_reports_chart_data($fromdate,$todate,$shopid,$filtertype);
      if($chart_data->num_rows() > 0){
        $chart_data = $chart_data->result_array();
        $data = array("success" => 1, "chartdata" => $chart_data);
        generate_json($data);
        exit();
      }else{
        $data = array("success" => 0, "chartdata" => array());
        generate_json($data);
        exit();
      }
    }else{
      $data = array("success" => 0, "chartdata" => array());
      generate_json($data);
      exit();
    }

  }
}
