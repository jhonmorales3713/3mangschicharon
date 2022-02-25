<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reclaimed_vouchers extends CI_Controller {
  public function __construct(){
    parent::__construct();
  $this->load->model('vouchers/Model_reclaimed_vouchers');
  
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


  public function reclaimed_vouchers($token = '')
  {
      $this->isLoggedIn();
      if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['rec_vc']['view'] == 1) {
          $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
          $main_nav_id = $this->views_restriction($content_url);

          $member_id = $this->session->userdata('sys_users_id');

          $data_admin = array(
              'token'               => $token,
              'main_nav_id'         => $main_nav_id, //for highlight the navigation
              'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
              // 'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
              // 'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
              // 'shops'               => $this->model_products_approval->get_shop_options(),
          );

          $this->load->view('includes/header', $data_admin);
          $this->load->view('vouchers/reclaimed_vouchers', $data_admin);
      } else {
          $this->load->view('error_404');
      }
  }


  public function reclaimed_vouchers_table()
  {
      $this->isLoggedIn();
      $request = $_REQUEST;

      $query = $this->Model_reclaimed_vouchers->reclaimed_vouchers_table($request);


      generate_json($query);
  }



  public function export_reclaimed_voucher_table()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();

        $export_data = array(
            '_order_ref_export'               => $this->input->post('_order_ref_export'),
            '_vcode_export'                  => $this->input->post('_vcode_export'),
            '_record_status_export'           => $this->input->post('_record_status_export'),
            'date_from_export'                => $this->input->post('date_from_export'),

        );

    
        // print_r($export_data);
        // die();
 
        $data = $this->Model_reclaimed_vouchers->reclaimed_vouchers_table_export($export_data);

        $sheet->setCellValue('B1', 'Reclaimed Voucher List');
        $sheet->setCellValue('B2', $this->input->post('date_from_export'));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);

        $sheet->setCellValue('A6', 'Reclaimed Date');
        $sheet->setCellValue('B6', 'Voucher Code');
        $sheet->setCellValue('C6', 'Name');
        $sheet->setCellValue('D6', 'Order Ref #');
        $sheet->setCellValue('E6', 'Order Date');
        $sheet->setCellValue('F6', 'Email');
        $sheet->setCellValue('G6', 'Mobile');
        $sheet->setCellValue('H6', 'Reason');
        $sheet->setCellValue('I6', 'Status');
 


        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);
        
        $exceldata= array();

        // print_r($query);
        // die();
      
        
        foreach($data as $k=> $row){
            // $name = ''.$row["firstname"].'  '.$row["lastname"].'';

            $resultArray = array(
                '1' => $row['trandate'],
                '2' => $row["voucher_code"],
                '3' => $row["name"],
                '4' => $row["order_refnum"],
                '5' => $row["date_ordered"],
                '6' => $row["email"],
                '7' => $row["mobile"],
                '8' => $row["reason"],
                '9' => $row['status']    
            );
                        
             $exceldata[] = $resultArray;
          
        }
      
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'Reclaimed Voucher List';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Reclaimed Voucher List', 'Reclaimed Voucher List has been exported into excel with filter '.$this->input->post('date_from_export'), 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }


 

}