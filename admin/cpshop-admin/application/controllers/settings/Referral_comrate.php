<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Referral_comrate extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('setting/Model_referral_comrate');
        $this->load->model('shop_branch/Model_shopbranch');
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
        if ($delete_id > 0) {
            $query = $this->Model_referral_comrate->delete_modal_confirm($delete_id);

            $ref_data = $this->Model_referral_comrate->get_record_details($delete_id)->row();
		    $remarks = $ref_data->itemname." has been successfully deleted";

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
                $this->audittrail->logActivity('Referral Comrate', $remarks, 'deleted', $this->session->userdata('username'));
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

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
        }

        $ref_data = $this->Model_referral_comrate->get_record_details($disable_id)->row();
		$remarks = $ref_data->itemname." has been successfully ".$record_text;        

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->Model_referral_comrate->disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
                $this->audittrail->logActivity('Referral Comrate', $remarks, $record_text, $this->session->userdata('username'));                
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
        //start - for restriction of views
        if ( $this->loginstate->get_access()['ref_comrate']['view'] == 1){    
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'mainshop' => $mainshop
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/referral_comrate/referral_comrate_list_table', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function referral_comrate_list(){
        $this->isLoggedIn();
        $filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_itemid' => $this->input->post('_itemid'),
            '_itemname' => $this->input->post('_itemname'),
        ];
        $query = $this->Model_referral_comrate->referral_comrate_list($filters, $_REQUEST);
        generate_json($query);
    }
    
    public function export_referral_comrate_list(){
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
        $query = $this->Model_referral_comrate->referral_comrate_list($filters, $requestData, true);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $fil_arr = [
            'Item Id' => $filters['_itemid'],
            'Item Name' => $filters['_itemname'],
            'Record Status' => array(
                '' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
            )[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks("", "", $fromdate, $fromdate, "Referral Comrate", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Referral Comrate', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Referral Comrate");
        $sheet->setCellValue('B2', "Filters: $_filters");
        $sheet->setCellValue('B3', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);

        $sheet->setCellValue("A6", 'Item ID');
        $sheet->setCellValue('B6', 'Item Name');
        $sheet->setCellValue('C6', 'Unit');
        $sheet->setCellValue('D6', 'Startup');
        $sheet->setCellValue('E6', 'JC');
        $sheet->setCellValue('F6', 'MCJR');
        $sheet->setCellValue('G6', 'MC');
        $sheet->setCellValue('H6', 'MC Super');
        $sheet->setCellValue('I6', 'MC Mega');
        $sheet->setCellValue('J6', 'Others');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4],
                '6' => $row[5],
                '7' => $row[6],
                '8' => $row[7],
                '9' => $row[8],
                '10' => $row[9],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Referral Comrate ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
    }

    public function add($token = ''){
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['ref_comrate']['create'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            //end - for restriction of views main_nav_id
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $back_button = '<button type="button" style="float:right" class="btn btn-outline-secondary mr-2" id="back-button" data-value="'.base_url('Referralcomrate/home/'.$token).'">Back</button>';
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'idno' => '',
                'mainshop' => $mainshop,
                'back_button' => $back_button,
                'main_js' => 'assets/js/settings/referral_comrate/referral_comrate_add.js'
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/referral_comrate/referral_comrate_add', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_product_byshop(){
        $mainshop = sanitize($this->input->post('mainshop'));
        if(!empty($mainshop)){
            $result = $this->Model_referral_comrate->get_product_byshop($mainshop)->result();
            $data = array(
                        'success' => 1,
                        'result' => $result
                    );
        }else{
            $data = array(
                        'success' => 0,
                        'message' => 'Missing shopid'
                    );
        }
        generate_json($data);
    }

    public function save_refcomrate(){
        $mainshop = sanitize($this->input->post('entry-mainshop'));
        $product = sanitize($this->input->post('entry-product'));
        $startup = sanitize($this->input->post('entry-startup'));
        $jc = sanitize($this->input->post('entry-jc'));
        $mcjr = sanitize($this->input->post('entry-mcjr'));
        $mc = sanitize($this->input->post('entry-mc'));
        $mcsuper = sanitize($this->input->post('entry-mcsuper'));
        $mcmega = sanitize($this->input->post('entry-mcmega'));
        $others = sanitize($this->input->post('entry-others'));

        if(!empty($mainshop) AND !empty($product) AND !empty($startup) AND !empty($jc) AND !empty($mcjr) AND !empty($mc) AND !empty($mcsuper) AND !empty($mcmega) AND !empty($others)){
            if($this->Model_referral_comrate->productid_isexist($product)->row()->count > 0){
                $data = array(
                        'success' => 0,
                        'message' => 'Product Already Exist!'
                    );
            }else{
                $this->Model_referral_comrate->save_refcomrate($mainshop, $product, $startup, $jc, $mcjr, $mc, $mcsuper, $mcmega, $others);
                $data = array(
                            'success' => 1,
                            'message' => 'Record Saved successfully'
                        );
                $itemname = $this->Model_referral_comrate->get_item_name($product);
                $remarks = $itemname.' successfully added to Referral Comrate';
                $this->audittrail->logActivity('Referral Comrate', $remarks, 'add', $this->session->userdata('username'));     
            }
        }else{
            $data = array(
                        'success' => 0,
                        'message' => 'Please Complete all required fields!'
                    );
        }
        generate_json($data);
    }

    public function edit($id, $token = ''){
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['ref_comrate']['update'] == 1 || $this->loginstate->get_access()['ref_comrate']['view'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            //end - for restriction of views main_nav_id
            $id = en_dec('dec', $id);
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Referralcomrate/home/'.$token).'">Back</button>';
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'idno' => $id,
                'mainshop' => $mainshop,
                'record_details' => $this->Model_referral_comrate->get_record_details($id)->row(),
                'back_button' => $back_button,
                'main_js' => 'assets/js/settings/referral_comrate/referral_comrate_edit.js'
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/referral_comrate/referral_comrate_add', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function update_refcomrate(){
        $idno = sanitize($this->input->post('idno_hidden'));
        $mainshop = sanitize($this->input->post('entry-mainshop'));
        $product = sanitize($this->input->post('entry-product'));
        $startup = sanitize($this->input->post('entry-startup'));
        $jc = sanitize($this->input->post('entry-jc'));
        $mcjr = sanitize($this->input->post('entry-mcjr'));
        $mc = sanitize($this->input->post('entry-mc'));
        $mcsuper = sanitize($this->input->post('entry-mcsuper'));
        $mcmega = sanitize($this->input->post('entry-mcmega'));
        $others = sanitize($this->input->post('entry-others'));

        $cur_val = [
            //'mainshop' => $mainshop,
            //'product' => $product,
            'startup' => $startup,
            'jc' => $jc,
            'mcjr' => $mcjr,
            'mc' => $mc,
            'mcsuper' => $mcsuper,
            'mcmega' => $mcmega,
            'others' => $others
        ];

        $prev_val =json_decode($this->input->post('prev_val'),true);

        $changes = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val),$prev_val);
        $itemname = $this->Model_referral_comrate->get_item_name($product);
		$remarks = "Referral Comrate ".$itemname." has been updated successfully. \nChanges: \n$changes";

        if(!empty($idno) AND !empty($mainshop) AND !empty($product) AND !empty($startup) AND !empty($jc) AND !empty($mcjr) AND !empty($mc) AND !empty($mcsuper) AND !empty($mcmega) AND !empty($others)){
            if($this->Model_referral_comrate->productid_isexist_edit($product, $idno)->row()->count > 0){
                $data = array(
                        'success' => 0,
                        'message' => 'Product Already Exist!'
                    );
            }else{
                $this->Model_referral_comrate->update_refcomrate($idno, $mainshop, $product, $startup, $jc, $mcjr, $mc, $mcsuper, $mcmega, $others);
                $data = array(
                            'success' => 1,
                            'message' => 'Record Updated successfully'
                        );
                $this->audittrail->logActivity('Referral Comrate', $remarks, 'update', $this->session->userdata('username'));
            }
        }else{
            $data = array(
                        'success' => 0,
                        'message' => 'Please Complete all required fields!'
                    );
        }
        generate_json($data);
    }



}
?>