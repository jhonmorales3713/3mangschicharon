<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dev_settings_api_request_postlog extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('dev_settings/Model_api_request_postback_logs');
        //load model or libraries below here...
    }

    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    public function views_restriction($content_url)
    {
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

	public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }
    
	public function index() {
		if($this->session->userdata('isLoggedIn') == true) {
			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}
		$this->load->view('login');
    }    
    
    public function api_request_postback_logs($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['api_request_postback_logs']['view'] == 1) {
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
                'get_type' => $this->Model_api_request_postback_logs->get_type_apirequest_postlog()
            );
            
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/api_request_postback_logs', $data_admin);
        }else{
             $this->load->view('error_404');
        }
    }
  
    public function api_request_postback_logs_table(){
        $this->isLoggedIn();
        $query = $this->Model_api_request_postback_logs->api_request_postback_logs_table();
        generate_json($query);
    }

    public function api_request_postback_logs_export()
    {

        $this->isLoggedIn();

        $api_request_postback_logs   = $this->Model_api_request_postback_logs->api_request_postback_logs_table(true);
        $filters       = json_decode($this->input->post('_filters'));
        $filter_string = $this->audittrail->voidrecordListString($filters);

        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();                
    
        $sheet->setCellValue('B1', 'Api Request Postback Logs');
        $sheet->setCellValue('B2', $filters->date_from.' - '.$filters->date_to);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(150);
        $sheet->getColumnDimension('C')->setWidth(30);

    
        $sheet->setCellValue('A6', 'Date');
        $sheet->setCellValue('B6', 'Data Request');
        $sheet->setCellValue('C6', 'Type');

    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:C6')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($api_request_postback_logs['data'] as $key => $row) {
    
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A7');
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Api Request Postback Logs';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Api Request Postback Logs', 'Api Request Postback Logs into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();

        //echo json_encode($void_record);
    }
}
