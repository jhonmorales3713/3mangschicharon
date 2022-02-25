<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Claimed_vouchers extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('vouchers/model_claimed_vouchers');
    $this->load->model('vouchers/Model_shops');
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
        if ($this->loginstate->get_access()['vc']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            'shops' => $this->model_claimed_vouchers->get_shop_options()
            );
            // end - data to be used for views

            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('vouchers/claimed_vouchers', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

  public function get_vouchers_claimed_json(){
    $this->isLoggedIn();

    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_claimed_vouchers->get_vouchers_claimed_json($search);
    echo json_encode($data);
  }

  public function export_vouchers_claimed(){
    $this->isLoggedIn();          

    $filters = json_decode($this->input->post('_filters'));

    $_search = ($filters->search == '') ? "No Search":$filters->search;
    $_name = ($filters->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($filters->shop)->result_array()[0]['shopname'];
    $from = sanitize($filters->from);
    $to = sanitize($filters->to);

    $void_record = $this->model_claimed_vouchers->get_vouchers_claimed_json($filters,true);    

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();                

    $sheet->setCellValue('B1', 'Claimed Vouchers');
    $sheet->setCellValue('B2', $_name);
    $sheet->setCellValue('B3', $filters->from.' - '.$filters->to);
    
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(30);
    $sheet->getColumnDimension('G')->setWidth(20);
    // $sheet->getColumnDimension('H')->setWidth(30);

    $sheet->setCellValue('A6', 'Shop');
    $sheet->setCellValue('B6', 'Customer');
    $sheet->setCellValue('C6', 'Order Reference Number');
    $sheet->setCellValue('D6', 'Voucher Reference Number');
    $sheet->setCellValue('E6', 'Voucher Code');
    $sheet->setCellValue('F6', 'Voucher Amount');
    $sheet->setCellValue('G6', 'Date Used');
    // $sheet->setCellValue('H6', 'Used in');

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:G6')->getFont()->setBold(true);

    $exceldata= array();
    foreach ($void_record['data'] as $key => $row) {

      $resultArray = array(
        '1' => $row[0],
        '2' => $row[1],
        '3' => $row[2],
        '4' => $row[3],
        '5' => $row[4],
        '6' => $row[5],
        '7' => $row[6],
        // '8' => $row[7],
      );
      $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');

    $writer = new Xlsx($spreadsheet);
    $filename = 'Claimed Vouchers';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
    header('Cache-Control: max-age=0');
    ob_end_clean();
    // $types = ["" => "All Records", "2" => "Enabled", "3" => "Disabled"];
    $filterstr = "and no filters. ";
    if ($_search !== "" || $_name !== "All Shops") {
      $filterstr = "with filters: '$_search'";
      $filterstr .= ($_name == "") ? " in All shops" : " in $_name, ";
    }
    $filterstr .= "Dated $from to $to";
    $this->audittrail->logActivity('Claimed Vouchers', "Claimed Vouchers has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
  }
}
