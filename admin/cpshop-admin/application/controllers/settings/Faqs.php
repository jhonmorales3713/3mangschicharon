<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Faqs extends CI_Controller {
    public function __construct(){
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('setting/model_faqs', 'model_faqs');
        $this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
    }

    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
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




    public function view($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['faqs']['view'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';
            $main_nav_id = $this->views_restriction($content_url);

            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shops'               => $this->model_faqs->get_shop_options(),
                'shopid'              => $this->session->userdata('sys_shop')
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_faqs', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function faqs_table(){
        $this->isLoggedIn();
        $filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_name' => $this->input->post('_name'),
            '_shops' => $this->input->post('_shops')
        ];
        $query = $this->model_faqs->faqs_table($filters, $_REQUEST);
        generate_json($query);
    }

    public function add_faqs(){
        $this->isLoggedIn();
        
        $faqs_arrangement = sanitize($this->input->post('faqs_arrangement'));
        $faqs_title = sanitize($this->input->post('faqs_title'));
        $faqs_content = $this->input->post('faqs_content');
        $faqs_for = sanitize($this->input->post('faqs_for'));

        $remarks = "Faqs ".$faqs_title." has been added successfully.";

        $query = $this->model_faqs->add_faqs($faqs_arrangement, $faqs_title, $faqs_content, $faqs_for);
        
        if ($query) {
            $data = array("success" => 1, 'message' => "Faqs added successfully!");
            $this->audittrail->logActivity('Faqs', $remarks, 'add', $this->session->userdata('username'));
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong.");
        }
        
        generate_json($data);
    }

    public function get_faqs_data(){
        $this->isLoggedIn();
        
        $edit_id = sanitize($this->input->post('edit_id'));
        
        $query = $this->model_faqs->get_faqs_data($edit_id)->row();
    
        if ($query) {
            $data = array("success" => 1, 'message' => "Successfully fetched!", 'result' => $query);
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function update_faqs(){
        $this->isLoggedIn();
        
        $id                      = sanitize($this->input->post('id'));
        $edit_faqs_arrangement   = sanitize($this->input->post('edit_faqs_arrangement'));
        $edit_faqs_title         = sanitize($this->input->post('edit_faqs_title'));
        $edit_faqs_content       = $this->input->post('edit_faqs_content');
        $edit_faqs_for           = sanitize($this->input->post('edit_faqs_for'));
        $prev_val                = $this->input->post('prev_val');

        
        $cur_val = [
            'faqs_arrangement' => $edit_faqs_arrangement,
            'faqs_title' => $edit_faqs_title,
            'faqs_content' => $edit_faqs_content,
            'faqs_for' => $edit_faqs_for
        ];


        $changes = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val),$prev_val);
        $remarks = "Faqs ".$cur_val['faqs_title']." has been updated successfully. \nChanges: \n$changes";
        

        $query = $this->model_faqs->update_faqs($id, $edit_faqs_arrangement, $edit_faqs_title, $edit_faqs_content, $edit_faqs_for);
        
        if ($query) {
            $data = array("success" => 1, 'message' => "Faqs updated successfully!");
            $this->audittrail->logActivity('Faqs', $remarks, 'update', $this->session->userdata('username'));
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong.");
        }
        
        generate_json($data);
    }

    public function disable_faqs(){
        $this->isLoggedIn();
        
        $disable_id    = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));
        $disable_name  = sanitize($this->input->post('disable_name'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
        }
        
        $remarks = $disable_name." has been successfully ".$record_text;

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->model_faqs->disable_faqs($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Faqs ".$record_text." successfully!");
                $this->audittrail->logActivity('Faqs', $remarks, $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function delete_faqs(){
        $this->isLoggedIn();
        
        $delete_id   = sanitize($this->input->post('delete_id'));
        $delete_name = sanitize($this->input->post('delete_name'));
        
        //get category data using delete_id
        $remarks = $delete_name." has been successfully deleted";

        if ($delete_id > 0) {
            $query = $this->model_faqs->delete_faqs($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Faqs deleted successfully!");
                $this->audittrail->logActivity('Faqs', $remarks, 'delete', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function export_roles_table(){
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
        $query = $this->model_roles ->roles_table($filters, $requestData, true);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $shopid = '';
        $branchid = '';
        $fil_arr = [
            'Role Name' => $filters['_name'],
            'Record Status' => array(
                '' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
            )[$filters['_record_status']]
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $fromdate, "Roles", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Roles', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Roles");
        $sheet->setCellValue('B2', "Filters: $_filters");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);

        $sheet->setCellValue("A6", 'ID');
        $sheet->setCellValue("B6", 'Shop');
        $sheet->setCellValue("C6", 'Role Name');
        $sheet->setCellValue('D6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3]
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'RolesExport' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
    }
}