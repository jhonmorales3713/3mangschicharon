<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Withholding_tax_reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/model_withholding_tax_reports');
    // $this->load->model('orders/model_orders');
    $this->load->model('wallet/Model_shops');
    $this->load->library('form_validation');
  }

  public function isLoggedIn() {
      if($this->session->userdata('isLoggedIn') == false) {
          header("location:".base_url('Main/logout'));
      }
  }

  public function list_table($token = ""){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_withholding_tax_reports->list_table($search,$token,$_REQUEST);
    echo json_encode($data);
  }

  public function export_list_table(){
    $requestData = url_decode(json_decode($this->input->post('_search')));
    $search = json_decode($requestData['searchValue']);

    $search_val = ($search->search == "") ? "No Search":$search->search;
    $shop_id = ($search->shop == "") ? "All Shops":$search->shop;
    $fromdate = ($search->from == "") ? "All":$search->from . " to";
    $todate = ($search->to == "") ? "Records":$search->to;
    $shop_name = ($search->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($shop_id)->result_array()[0]['shopname'];

    $data = $this->model_withholding_tax_reports->list_table($search,'', $requestData, true);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', "Withholding Tax Reports");
    $sheet->setCellValue('A2', "Filter: $search_val;$shop_name");
    $sheet->setCellValue('A3', "$fromdate $todate");

    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(15);

    $sheet->setCellValue('A6', 'Billing Date');
    $sheet->setCellValue('B6', 'Billing Code');
    $sheet->setCellValue('C6', 'Billing No');
    $sheet->setCellValue('D6', 'Shopname');
    $sheet->setCellValue('E6', 'Branch');
    $sheet->setCellValue('F6', 'Withholding Tax');

    $sheet->getStyle('A1')->getFont()->setBold(true);
    $sheet->getStyle('A6:F6')->getFont()->setBold(true);

    // print_r($data['data']);
    // exit();
    $count = 7;
    $exceldata= array();
    $total = 0;
    foreach ($data['data'] as $key => $row) {

        $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[4],
            '6' => $row[5]
        );
        $total += floatval($row[5]);
        $exceldata[] = $resultArray;
        $count++;
    }

    $sheet->fromArray($exceldata, null, 'A7');
    $row_count = count($exceldata)+7;
    for ($i=7; $i < $row_count; $i++) {
      $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("F$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }
    //
    $spreadsheet->setActiveSheetIndex(0)->setCellValue("E$count","Total Tax");
    $spreadsheet->setActiveSheetIndex(0)->setCellValue("F$count",$total);
    $sheet->getStyle("E$count")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle("F$count")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('E'.$count.':F'.$count)->getFont()->setBold(true);


    $writer = new Xlsx($spreadsheet);
    $filename = 'Withholding Tax ' . date('Y/m/d');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $filterstr = "and no filters. ";
    if ($search->shop !== "" || $search->search !== "") {
      $filterstr = "with filters: $search_val in $shop_name, ";
    }
    $filterstr .= "Dated $fromdate $todate";
    $this->audittrail->logActivity('Withholding Tax Reports', "Withholding Tax Reports has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
    // print_r($data['data']);
    // echo json_encode($data);
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['wtr']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id

      // start - data to be used for views
      $data_admin = array(
          'token' => $token,
          'main_nav_id' => $main_nav_id, //for highlight the navigation,
          'shops' => $this->model_withholding_tax_reports->get_shop_options(),
          // 'payments' => $this->model_billing->get_options()
      );
      // end - data to be used for views

      // start - load all the views synchronously
      $this->load->view('includes/header', $data_admin);
      $this->load->view('reports/withholding_tax_reports', $data_admin);
    }else{
      $this->load->view('error_404');
    }

  }

  public function get_branches(){
    $shopid = $this->input->post('shopid');
    if(empty($shopid)){
      $data = array("success" => 0, "message" => "Invalid shop id. Please try to reload and try again.");
      generate_json($data);
      exit();
    }

    $branches = $this->model_withholding_tax_reports->get_branches($shopid);
    if($branches->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to find any branch.");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "branches" => $branches->result_array());
    generate_json($data);
    exit();
  }

  public function print_order($token = '', $ref_num){
      $this->isLoggedIn();
      if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['pending_orders']['view'] == 1 || $this->loginstate->get_access()['paid_orders']['view'] == 1 || $this->loginstate->get_access()['readyforprocessing_orders']['view'] == 1 || $this->loginstate->get_access()['processing_orders']['view'] == 1 || $this->loginstate->get_access()['readyforpickup_orders']['view'] == 1 || $this->loginstate->get_access()['bookingconfirmed_orders']['view'] == 1 || $this->loginstate->get_access()['fulfilled_orders']['view'] == 1 || $this->loginstate->get_access()['shipped_orders']['view'] == 1 || $this->loginstate->get_access()['returntosender_orders']['view'] == 1) {
          error_reporting(0);
          $member_id = $this->session->userdata('sys_users_id');
          $sys_shop = $this->model_orders->get_sys_shop($member_id);

          if($sys_shop == 0) {
              $split         = explode("-",$ref_num);
              $sys_shop      = $split[0];
              $reference_num = $split[1];

              if($sys_shop == 0){
                  $get_vouchers = $this->model_manual_order->get_vouchers($reference_num);
              }else{
                  $get_vouchers = $this->model_manual_order->get_vouchers_shop($reference_num, $sys_shop);
              }
          }else{
              $split         = explode("-",$ref_num);
              $sys_shop      = $split[0];
              $reference_num = $split[1];
              $get_vouchers  = $this->model_manual_order->get_vouchers_shop($reference_num, $sys_shop);
          }

          $row            = $this->model_manual_order->orders_details($reference_num, $sys_shop);
          $refcode        = $this->model_manual_order->get_referral_code($row['reference_num']);
          $branch_details = $this->model_manual_order->get_branch_details($reference_num, $sys_shop)->row();
          $order_items    = $this->model_manual_order->order_item_table_print($reference_num, $sys_shop);

          if($sys_shop != 0){
              $mainshopname = $this->model_manual_order->get_mainshopname($sys_shop)->row()->shopname;
          }else{
              $mainshopname = 'toktokmall';
          }

          $data_admin = array(
              'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
              'shopid'              => $this->model_orders->get_sys_shop($member_id),
              'shopcode'            => $this->model_orders->get_shopcode($member_id),
              'reference_num'       => $reference_num,
              'order_details'       => $row,
              'referral'            => $refcode,
              'branch_details'      => $branch_details,
              'mainshopname'        => $mainshopname,
              'mainshopid'          => $sys_shop,
              'url_ref_num'         => $ref_num,
              'orders_history'      => $orders_history,
              'voucher_details'     => $get_vouchers,
              'order_items'         => $order_items
          );

          $page = $this->load->view('orders/order_print', $data_admin, true);

          $this->load->library('Pdf');
          $obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
          $obj_pdf->SetCreator(PDF_CREATOR);
          $obj_pdf->SetTitle('Order Print');
          $obj_pdf->SetDefaultMonospacedFont('helvetica');
          $obj_pdf->SetFont('helvetica', '', 9);
          $obj_pdf->setFontSubsetting(false);
          $obj_pdf->setPrintHeader(false);
          $obj_pdf->AddPage();
          ob_start();

          $obj_pdf->writeHTML($page, true, false, true, false, '');
          ob_clean();
          $obj_pdf->Output("print_order.pdf", 'I');
          $this->audittrail->logActivity('Order List', $this->session->userdata('username').' printed order #'.$reference_num, 'Print', $this->session->userdata('username'));
      }else{
          $this->load->view('error_404');
      }
  }

}
