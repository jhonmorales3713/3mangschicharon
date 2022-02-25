<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class merchant_serviceable_areas extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/model_merchant_serviceable_areas');
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['msr']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      $mainshop = $this->model_merchant_serviceable_areas->get_all_shop()->result();
      $city = $this->model_merchant_serviceable_areas->get_all_city()->result();
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
        'mainshop' => $mainshop,
        'city' => $city,
        'shopid' => $shopid,
        'branch' => $this->session->userdata('branchid'),
      );
      // end - data to be used for views
      $this->load->view('includes/header',$data_admin);
      $this->load->view('reports/merchant_serviceable_areas',$data_admin);
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

  public function merchant_serviceable_areas_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['msr']['view'] == 1){
      $filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_mainshop' => $this->input->post('_mainshop'),
            '_branchname'      => $this->input->post('_branchname'),
            '_city'      => $this->input->post('_city'),
        ];
      $requestData = $_REQUEST;
      $data = $this->model_merchant_serviceable_areas->get_merchant_serviceable_areas_data($filters,$requestData);
      echo json_encode($data);

    }else{
      $this->load->view('error_404');
    }
  }

  public function export_merchant_serviceable_areas_list()
    {
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        $query = $this->model_merchant_serviceable_areas->get_merchant_serviceable_areas_data($filters,$requestData);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $shopid = $filters['_mainshop'];
        $fil_arr = [
            'Branch Name' => $filters['_branchname'],
            'City' => $filters['_city'],
            'Record Status' => array(
                '' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
            )[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, "", $fromdate, $fromdate, "Shop Branch", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Merchant Serviceable Areas', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Merchant Serviceable Areas");
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
        $sheet->setCellValue('B6', 'Branch');
        $sheet->setCellValue('C6', 'Delivery Zone');
        $sheet->setCellValue('D6', 'Region');
        $sheet->setCellValue('E6', 'Province');
        $sheet->setCellValue('F6', 'City/Municipality');
        $sheet->setCellValue('G6', 'Shipping Fee');


        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getStyle('A')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G')->getAlignment()->setWrapText(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $row[3]=str_replace("<br />", "\n", $row[3]);
            $row[4]=str_replace("<br />", "\n", $row[4]);
            $row[5]=str_replace("<br />", "\n", $row[5]);
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4],
                '6' => $row[5],
                '7' => $row[6],
            
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Merchant Serviceable Areas ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();

    }

  // public function merchant_serviceable_areas_chart_data(){
  //   $this->isLoggedIn();
  //   if($this->loginstate->get_access()['sr']['view'] == 1){
  //     $fromdate = sanitize($this->input->post('fromdate'));
  //     $todate = sanitize($this->input->post('todate'));
  //     $shopid = sanitize($this->input->post('shopid'));
  //     $filtertype = sanitize($this->input->post('filtertype'));

  //     $fromdate = date("Y-m-d", strtotime($fromdate));
  //     $todate = date("Y-m-d", strtotime($todate));

  //     $chart_data = $this->model_merchant_serviceable_areas->get_merchant_serviceable_areass_chart_data($fromdate,$todate,$shopid,$filtertype);
  //     if($chart_data->num_rows() > 0){
  //       $chart_data = $chart_data->result_array();
  //       $data = array("success" => 1, "chartdata" => $chart_data);
  //       generate_json($data);
  //       exit();
  //     }else{
  //       $data = array("success" => 0, "chartdata" => array());
  //       generate_json($data);
  //       exit();
  //     }
  //   }else{
  //     $data = array("success" => 0, "chartdata" => array());
  //     generate_json($data);
  //     exit();
  //   }

  // }
}
