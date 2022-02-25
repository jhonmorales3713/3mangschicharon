<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Product_orders_report extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/model_product_orders_report');
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['por']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id
      // start - data to be used for views
      $data_admin = array(
      'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $this->model_product_orders_report->get_shop_options(),
      );
      // end - data to be used for views
      $this->load->view('includes/header',$data_admin);
      $this->load->view('reports/product_orders_report',$data_admin);
    }else{
      $this->load->view('error_404');
    }
  }

  public function to_all(){
      $data = $this->model_product_orders_report->get_total_orders('2020-08-01','2020-09-01','all');
      echo json_encode($data);
  }

  public function list_table(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['por']['view'] == 1){
      $fromdate = sanitize($this->input->post('fromdate'));
      $todate = sanitize($this->input->post('todate'));
      $shopid = sanitize($this->input->post('shopid'));
      $filtertype = sanitize($this->input->post('filtertype'));

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));
      $requestData = $_REQUEST;

      $data = $this->model_product_orders_report->list_table($fromdate,$todate,$shopid,$filtertype,$requestData);
      echo json_encode($data);
    }else{
      $this->load->view('error_404');
    }
  }

  public function export_list_table()
  {
    $this->isLoggedIn();
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['por']['view'] == 1){
      $requestData = url_decode(json_decode($this->input->post("_search")));
      $filter = json_decode($this->input->post("_filters"));
      
      $fromdate = sanitize($filter->fromdate);
      $todate = sanitize($filter->todate);
      $shopid = sanitize($filter->shopid);
      $shop_name = ($shopid > 0) ? $this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname']:"All Shops";
      $filtertype = sanitize($filter->filtertype);

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));

      $data = $this->model_product_orders_report->list_table($fromdate,$todate,$shopid,$filtertype,$requestData, true);

      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('B1', "Products Order Report");
      $sheet->setCellValue('B2', "Filter: $shop_name");
      $sheet->setCellValue('B3', "$fromdate to $todate");
      
      $sheet->getColumnDimension('A')->setWidth(20);
      $sheet->getColumnDimension('B')->setWidth(20);
      $sheet->getColumnDimension('C')->setWidth(20);
      // $sheet->getColumnDimension('D')->setWidth(20);
      // $sheet->getColumnDimension('E')->setWidth(20);
      // $sheet->getColumnDimension('F')->setWidth(30);

      $sheet->setCellValue('A6', 'Shop');
      $sheet->setCellValue('B6', 'Product Name');
      $sheet->setCellValue('C6', 'Quantity');
      // $sheet->setCellValue('D6', 'No of Stock');
      // $sheet->setCellValue('E6', 'Shop Name');
      // $sheet->setCellValue('F6', 'Status');

      $sheet->getStyle('B1')->getFont()->setBold(true);
      $sheet->getStyle('A6:C6')->getFont()->setBold(true);

      // print_r($query);
      $exceldata= array();
      foreach ($data['data'] as $key => $row) {
          $quantity = floatval(preg_replace('/[^0-9.]/', '', $row[2]));
          // $stocks = floatval(preg_replace('/[^0-9.]/', '', $row[4]));

          $resultArray = array(
              '1' => $row[0],
              '2' => $row[1],
              '3' => number_format($quantity)
          );
          $exceldata[] = $resultArray;
      }

      $sheet->fromArray($exceldata, null, 'A7');

      $writer = new Xlsx($spreadsheet);
      $filename = 'Products Order Report ' . date('Y/m/d');
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
      header('Cache-Control: max-age=0');
      ob_end_clean();
      
      $types = ["all" => "All Transactions", "forprocess" => "For Process/Delivery", "fullfilled" => "Fulfilled/Delivered"];
      $filterstr = "and no filters. ";
      if ($filtertype !== "all" || $shop_name !== "All Shops") {
        $filterstr = "with filters: $types[$filtertype]";
        $filterstr .= ($shop_name == "All Shops") ? " in All shops" : " in '$shop_name', ";
      }
      $filterstr .= "Dated $fromdate to $todate";
      $this->audittrail->logActivity('Product Orders Report', "Product Orders Report has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
      
      return $writer->save('php://output');
      exit();
    }else{
      $this->load->view('error_404');
    }
  }    
}
