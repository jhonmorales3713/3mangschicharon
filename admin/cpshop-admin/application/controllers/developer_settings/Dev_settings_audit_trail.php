<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dev_settings_audit_trail extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('dev_settings/model_audit_trail');
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
    
    public function audit_trail($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['audit_trail']['view'] == 1) {
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
                'get_module' => $this->model_audit_trail->get_module_audit_trail()
            );
            
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/audit_trail', $data_admin);
        }else{
             $this->load->view('error_404');
        }
    }
  
    public function audit_trail_table(){
        $this->isLoggedIn();
        $query = $this->model_audit_trail->audit_trail_table();
        generate_json($query);
    }

    public function audit_trail_export()
    {

        $this->isLoggedIn();

        $audit_trail   = $this->model_audit_trail->audit_trail_table(true);
        $filters       = json_decode($this->input->post('_filters'));
        $filter_string = $this->audittrail->voidrecordListString($filters);

        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();                
    
        $sheet->setCellValue('B1', 'Audit Trail');
        $sheet->setCellValue('B2', $filters->date_from.' - '.$filters->date_to);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(90);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);
    
        $sheet->setCellValue('A6', 'Date');
        $sheet->setCellValue('B6', 'Module');
        $sheet->setCellValue('C6', 'Details');
        $sheet->setCellValue('D6', 'Action Type');
        $sheet->setCellValue('E6', 'Username');
        $sheet->setCellValue('F6', 'IP Address');
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($audit_trail['data'] as $key => $row) {
    
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[4],
            '6' => $row[5]
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A7');
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Audit Trail';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Audit Trail', 'Audit Trail been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();

        //echo json_encode($void_record);
    }
}
