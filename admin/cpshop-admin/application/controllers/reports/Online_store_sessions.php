<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Online_store_sessions extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/model_online_store_sessions');
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
    if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['ps']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id
      // start - data to be used for views
      $data_admin = array(
      'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        // 'shops' => $this->model_page_statistics->get_shop_options(),
        'setDate' => [
            'fromdate' => (isset($_GET['fromdate']) ? $_GET['fromdate'] : null),
            'todate' => (isset($_GET['todate']) ? $_GET['todate'] : null),
        ]
      );
      // end - data to be used for views
      $this->load->view('includes/header',$data_admin);
      $this->load->view('reports/online_store_sessions',$data_admin);
    }else{
      $this->load->view('error_404');
    }
  }

  public function get_page_statistics_data(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['ps']['view'] == 1){
      $fromdate = sanitize($this->input->post('fromdate'));
      $todate = sanitize($this->input->post('todate'));      

      $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
      $p_fromdate = $pre_date_range['fromdate'];
      $p_todate = $pre_date_range['todate'];

      $p_fromdate = $p_fromdate->format('Y-m-d');
      $p_todate = $p_todate->format('Y-m-d');

      $fromdate = date("Y-m-d", strtotime($fromdate));
      $todate = date("Y-m-d", strtotime($todate));      

      if($fromdate == $todate){        
        $fromdate = $fromdate.' 00:00:00';
        $todate = $todate.' 23:59:59';
        $p_fromdate = $p_fromdate.' 00:00:00';
        $p_todate = $p_todate.' 23:59:59';
        
        $cur_result = $this->model_online_store_sessions->get_visitors($fromdate,$todate);
        $pre_result = $this->model_online_store_sessions->get_visitors($p_fromdate,$p_todate);        

        $cur_range = array_column($cur_result,'trandate');
        $pre_range = array_column($pre_result,'trandate');        
        
        if($cur_range && $pre_range){
          $time_range = getTimeRange($cur_range,$pre_range);
        }
        else if($cur_range && !$pre_range){
          $time_range = getTimeRange($cur_range,$cur_range);
        }
        else{
          $time_range = []; $time_range['start'] = '00:00'; $time_range['end'] = '23:00';
        }
        
        $dates = createTimeRangeArray($time_range['start'],$time_range['end']);

        $cur_dates = createTimeRangeArray($time_range['start'],$time_range['end'],'1 hour',date('Y-m-d',strtotime($fromdate)));
        $pre_dates = createTimeRangeArray($time_range['start'],$time_range['end'],'1 hour',date('Y-m-d',strtotime($p_fromdate)));

        $cur_data = generateArrayIn($cur_result,$cur_dates,'trandate','visitors');
        $pre_data = generateArrayIn($pre_result,$pre_dates,'trandate','visitors');
        
        $cur_tb_head = date('m/d/Y',strtotime($todate));
        $pre_tb_head = date('m/d/Y',strtotime($p_todate));
  
        $cur_period = date('M d, Y',strtotime($todate));
        $pre_period = date('M d, Y',strtotime($p_todate));
      }
      else{ 
        $dates = generateDates($fromdate,$todate,'M d');       
        $cur_result = $this->model_online_store_sessions->get_visitors($fromdate,$todate.' 23:59:59');
        $pre_result = $this->model_online_store_sessions->get_visitors($p_fromdate,$p_todate.' 23:59:59');
        $cur_dates = generateDates($fromdate,$todate);
        $pre_dates = generateDates($p_fromdate,$p_todate);
        $cur_data = generateArrayIn($cur_result,$cur_dates,'trandate','visitors');
        $pre_data = generateArrayIn($pre_result,$pre_dates,'trandate','visitors');

        $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
        $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));
  
        $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
        $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
      }      

      $cur_total = getTotalInArray($cur_result,'visitors');
      $pre_total = getTotalInArray($pre_result,'visitors');

      $size = sizeof($cur_data);

      $increased = false;
      $percentage = 0.0;

      if($cur_total >= $pre_total){
        $diff = $cur_total-$pre_total;
        $increased = true;
      }
      else{
        $diff = $pre_total-$cur_total;        
      }
      if($pre_total > 0){
        $percentage = ($diff/$pre_total)*100;
      }
      else{
        $percentage = 100;
      }     

      $reschart = array(
        'dates' => $dates,
        'current_data' => $cur_data,
        'previous_data' => $pre_data,
        'cur_total' => intval($cur_total),
        'pre_total' => intval($pre_total),
        'cur_result' => $cur_result,
        'pre_result' => $pre_result,                
        'cur_period' => $cur_period,
        'pre_period' => $pre_period,
        'cur_dates' => $cur_dates,
        'pre_dates' => $pre_dates,
        'cur_tb_head' => $cur_tb_head,
        'pre_tb_head' => $pre_tb_head,
        'percentage' => array('percentage' => number_format(round($percentage,2),2), 'increased' => $increased)
      );

      $data = array("success" => 1, "chartdata" => $reschart);
      generate_json($data);
      exit();
    }else{
      $this->load->view('error_404');
    }
  }

  public function get_visitors_online_table(){
    $this->isLoggedIn();    
    $fromdate = sanitize($this->input->post('fromdate'));
    $todate = sanitize($this->input->post('todate'));

    $data = $this->model_online_store_sessions->get_visitors_online_table($fromdate, $todate);
    echo json_encode($data);
  }

  public function export_visitors_online_table(){
    // $this->isLoggedIn();

    $filters = json_decode($this->input->post('_filters'));
    $fromdate = sanitize($filters->fromdate);
    $todate = sanitize($filters->todate);
    
    $total_visitors = $this->model_online_store_sessions->get_visitors_online_table($fromdate,$todate,true);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('B1', 'Total Site Visitors');
    if($fromdate == $todate){
      $sheet->setCellValue('B2', $fromdate);
    }
    else{
      $sheet->setCellValue('B2', $fromdate.' - '.$todate);
    }
    
    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(20);    

    $sheet->setCellValue('A6', 'Date');
    $sheet->setCellValue('B6', 'Visitors');    

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:B6')->getFont()->setBold(true);

    $exceldata= array();
    $counter  = 7;
    foreach ($total_visitors['data'] as $key => $row) {

      $resultArray = array(
        '1' => $row[0],
        '2' => $row[1]
      );
      $counter++; 
      $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');

    $row_count = count($exceldata)+7;
    for ($i=7; $i < $row_count; $i++) {
        $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
    }

    $sheet->setCellValue('A'.$counter, 'TOTAL');   
    $sheet->getStyle('B'.$counter)->getFont()->setBold(true);
    $sheet->getStyle('B'.$counter)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
    $sheet->setCellValue('B'.$counter, $total_visitors['sub_total']);   

    $writer = new Xlsx($spreadsheet);
    $filename = 'Total Site Visitors';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
    header('Cache-Control: max-age=0');
    ob_end_clean();
    
    $filterstr = "and no filters. ";
    $filterstr .= "Dated $fromdate to $todate";
    $this->audittrail->logActivity('Online Store Sessions', "Online Store Sessions has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));

    return $writer->save('php://output');
    exit();
  }
}
