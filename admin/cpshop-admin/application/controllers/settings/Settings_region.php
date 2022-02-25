<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Settings_region extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('setting/Model_settings_region');
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

        if (in_array($content_url, $url_content_arr) == false){
            header("location:".base_url('Main/logout'));
        }else{
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

    public function delete_modal_confirm(){
        $this->isLoggedIn();
        
        $delete_id = sanitize($this->input->post('delete_id'));
        $record_name = sanitize($this->input->post('record_name'));

        if ($delete_id > 0) {
            $query = $this->Model_settings_region->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Region', "Region $record_name has been deleted successfully.", 'delete', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        generate_json($data);
    }

    public function disable_modal_confirm(){
        $this->isLoggedIn();
        
        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));
        $record_name = sanitize($this->input->post('record_name'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
        }

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->Model_settings_region->disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Region', "Region $record_name has been $record_text successfully.", $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function view($token = '')
    {
        $this->isLoggedIn();
        if ( $this->loginstate->get_access()['settings_region']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
        $this->load->view('settings/region/region_list_table', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function region_list(){
        $this->isLoggedIn();
        $filters = [
            '_record_status' => sanitize($this->input->post('_record_status')),
            '_code' => sanitize($this->input->post('_code')),
            '_name'      => sanitize($this->input->post('_name')),
        ];
        $query = $this->Model_settings_region->region_list($filters, $_REQUEST);
        generate_json($query);
    }

    public function export_region_list(){
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
        $query = $this->Model_settings_region->region_list($filters, $requestData, true);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $fil_arr = [
            'Code' => $filters['_code'],
            'Name' => $filters['_name'],
            'Record Status' => array(
                '' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
            )[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks("", "", $fromdate, $fromdate, "Region", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Region', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Region");
        $sheet->setCellValue('B2', "Filters: $_filters");
        $sheet->setCellValue('B3', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);

        $sheet->setCellValue("A6", 'Region Code');
        $sheet->setCellValue('B6', 'Region Name');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:B6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        $last_row = count($exceldata)+7;
        for ($i=7; $i < $last_row; $i++) {
            $sheet->getStyle("A$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Region ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
    }

}
?>