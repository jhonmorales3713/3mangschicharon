<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Payout_report extends CI_Controller {

  public function __construct(){
    parent::__construct();
    $this->load->model('reports/model_payout_report');
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['pr']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id
      // start - data to be used for views
      $data_admin = array(
      'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'member_id' => $this->session->user_id
      );
      // end - data to be used for views
      $this->load->view('includes/header',$data_admin);
      $this->load->view('reports/payout_report',$data_admin);
    }else{
      $this->load->view('error_404');
    }
  }

  public function payout_report_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['pr']['view'] == 1){
      $member_id = sanitize($this->input->post('member_id'));
      $fromdate = sanitize($this->input->post('fromdate'));
      $todate = sanitize($this->input->post('todate'));

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));

      $chart_data = $this->model_payout_report->get_payout_report_chart($fromdate,$todate,$member_id);
      $data = array("success" => 1, "chartdata" => $chart_data);
      generate_json($data);
      exit();

    }else{
      $this->load->view('error_404');
    }

  }

  public function list_table(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['pr']['view'] == 1){
      $fromdate    = sanitize($this->input->post('fromdate'));
      $todate      = sanitize($this->input->post('todate'));
      $member_id   = sanitize($this->input->post('member_id'));
      $requestData = json_decode(json_encode($_REQUEST));
      $fromdate    = date("Y-m-d", strtotime($fromdate));
      $todate      = date("Y-m-d", strtotime($todate));
      

      $data = $this->model_payout_report->list_table($fromdate,$todate,$member_id,$requestData);
      echo json_encode($data);

    }else{
      $this->load->view('error_404');
    }
  }

  public function payout_export(){
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $requestData = json_decode($this->input->post('_search'));
    $fromdate = sanitize($this->input->post('date_from_export'));
    $todate = sanitize($this->input->post('date_to_export'));
    $member_id = sanitize($this->input->post('member_id_export'));
    $fromdate = date("Y-m-d", strtotime($fromdate));
    $todate = date("Y-m-d", strtotime($todate));
    
    $search = json_decode($requestData->searchValue);
    $data = $this->model_payout_report->list_table($fromdate,$todate,$member_id, $requestData);

    $sheet->setCellValue('B1', 'Payout Report');
    $sheet->setCellValue('B2', $fromdate.' - '.$todate);
    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);

    $sheet->setCellValue('A6', 'Transaction Rate');
    $sheet->setCellValue('B6', 'Amount');
    $sheet->setCellValue('C6', 'Payout');

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:C6')->getFont()->setBold(true);

    $exceldata= array();
    foreach ($data['data'] as $key => $row) {
      $amount  = floatval(preg_replace('/[^0-9.]/', '', $row[1]));

      $resultArray = array(
        '1' => $row[0],
        '2' => number_format($amount, 2),
        '3' => $row[2]
      );
      $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');

    $writer = new Xlsx($spreadsheet);
    $filename = 'Payout Report';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
    header('Cache-Control: max-age=0');
    ob_end_clean();
    
    $filterstr = "and no filters. ";
    $filterstr .= "Dated $fromdate to $todate";
    $this->audittrail->logActivity('Payout Report', "Payout Report has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
  }

}
