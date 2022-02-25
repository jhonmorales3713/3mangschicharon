<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Shop_branch extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('shop_branch/Model_shopbranch');
        $this->load->model('shops/Model_shops');
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
            $check_pendingorders = $this->Model_shopbranch->check_pendingorder($delete_id)->num_rows();
     
            if($check_pendingorders == 0){
                $branchname = $this->Model_shopbranch->get_record_details($delete_id)->row_array()['branchname'];
                $query = $this->Model_shopbranch->delete_modal_confirm($delete_id);
                if ($query == 1) {
                    $this->Model_shopbranch->updateInventoryQty($delete_id);
                    $data = array("success" => 1, 'message' => "Record deleted successfully!");
                    $this->audittrail->logActivity('Shop Branch', $branchname.' branch successfully deleted.', "delete", $this->session->userdata('username'));
                }else{
                    $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                    $this->audittrail->logActivity('Shop Branch', $branchname.' branch failed to delete.', "delete", $this->session->userdata('username'));
                }
            }else{
                 $data = array("success" => 0, 'message' => "Cannot delete shop branch due to pending orders.");   
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
        $check_pendingorders = $this->Model_shopbranch->check_pendingorder($disable_id)->num_rows();

        if($check_pendingorders > 0 && $record_status == 2){
            $data = array("success" => 0, 'message' => "Cannot disable shop branch due to pending orders.");   
        }
        else if ($disable_id > 0 && $record_status > 0) {
            $query = $this->Model_shopbranch->disable_modal_confirm($disable_id, $record_status);
            $branchname = $this->Model_shopbranch->get_record_details($disable_id)->row_array()['branchname'];

            if ($query == 1) {
                $this->Model_shopbranch->updateInventoryQty($disable_id);
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
                $this->audittrail->logActivity('Shop Branch', $branchname.' branch has been successfully '.$record_text, $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                $this->audittrail->logActivity('Shop Branch', ' Failed to '.$record_text.' '.$branchname, $record_text, $this->session->userdata('username'));
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function view($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['shop_branch']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $city = $this->Model_shopbranch->get_all_city()->result();
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'mainshop' => $mainshop,
                'city' => $city
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('shop_branch/shop_branch_list_table', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function shopbranch_list(){
        $this->isLoggedIn();
        $filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_mainshop' => $this->input->post('_mainshop'),
            '_branchname'      => $this->input->post('_branchname'),
            '_city'      => $this->input->post('_city'),
        ];
        $query = $this->Model_shopbranch->shopbranch_list($filters, $_REQUEST);
        generate_json($query);
    }

    public function export_shopbranch_list()
    {
        $this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        $query = $this->Model_shopbranch->shopbranch_list($filters, $requestData, true);
        // print_r($query);
        // exit();
        $fromdate = date('Y-m-d');
        $shopid = $filters['_mainshop'];
        $fil_arr = [
            'Branch Name' => $filters['_branchname'],
            'City' => $filters['_city'],
            'Record Status' => array(
                '' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
            )[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, "", $fromdate, $fromdate, "Shop Branch", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Shop Branch', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Shop Branch");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);
        // $sheet->getColumnDimension('F')->setWidth(15);

        $sheet->setCellValue("A6", 'Main Shop');
        $sheet->setCellValue('B6', 'Branch');
        $sheet->setCellValue('C6', 'Email');
        $sheet->setCellValue('D6', 'Mobile No.');
        $sheet->setCellValue('E6', 'City');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Shop Branch ' . date('Y/m/d');
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
        if ($this->loginstate->get_access()['shop_branch']['create'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            //end - for restriction of views main_nav_id
            $city = $this->Model_shopbranch->get_all_city()->result();
            $region = $this->Model_shopbranch->get_all_region()->result();
            $province = $this->Model_shopbranch->get_all_province()->result();
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Shopbranch/home/'.$token).'">Back</button>';
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'idno' => '',
                'city' => $city,
                'region' => $region,
                'province' => $province,
                'mainshop' => $mainshop,
                'back_button' => $back_button,
                'main_js' => 'assets/js/shop_branch/shop_branch_add.js',
                'googlemap_js' => 'assets/js/shop_branch/googlemap_branch.js',
                'breadcrumbs' => 'Add Shop Branch'
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('shop_branch/shop_branch_add', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function save_shop_branch(){
        $mainshop = sanitize($this->input->post('entry-mainshop'));
        $branchname = sanitize($this->input->post('entry-branch'));
        $contactperson = sanitize($this->input->post('entry-contactperson'));
        $conno = sanitize($this->input->post('entry-conno'));
        $email = sanitize($this->input->post('entry-email'));
        $address = sanitize($this->input->post('entry-address'));
        $branch_city = sanitize($this->input->post('entry-branch_city'));
        $branch_region = sanitize($this->input->post('entry-branch_region'));
        $loc_latitude = sanitize($this->input->post('loc_latitude'));
        $loc_longitude = sanitize($this->input->post('loc_longitude'));

        //Bank Details
        $bankname = sanitize($this->input->post('entry-bankname'));
        $acctname = sanitize($this->input->post('entry-acctname'));
        $acctno = sanitize($this->input->post('entry-acctno'));
        $desc = sanitize($this->input->post('entry-desc'));

        //Admin Settings
        $idnopb   = sanitize($this->input->post('entry-idnopb'));
        $treshold = sanitize($this->input->post('entry-treshold'));

        if(!empty($this->input->post('entry-city'))){
            $city = implode(",", $this->input->post('entry-city'));
        }else{
            $city = "";
        }
        if(!empty($this->input->post('entry-region'))){
            $region = implode(",", $this->input->post('entry-region'));
        }else{
            $region = "";
        }
        if(!empty($this->input->post('entry-province'))){
            $province = implode(",", $this->input->post('entry-province'));
        }else{
            $province = "";
        }
        $isautoassign = sanitize($this->input->post('entry-isautoassign'));
       
        if(!empty($mainshop) AND !empty($branchname) AND !empty($contactperson) AND !empty($conno) AND !empty($email) AND !empty($address) AND !empty($branch_region) AND !empty($loc_latitude) AND !empty($loc_longitude) AND !empty($bankname) AND  !empty($acctname) AND  !empty($acctno) AND  !empty($desc)){
            $checkisautoassign = $this->checkisautoassign($isautoassign, $city, $region, $province);
            if($checkisautoassign['status']){
                if(!$this->Model_shopbranch->name_is_exist($branchname)){
                    // if(phoneNum_isvalid($conno, 11)){
                        if($this->Model_shopbranch->idnopb_isvalid($idnopb)){
                            $branchid = $this->Model_shopbranch->save_shop_branch($mainshop, $branchname, $contactperson, $conno, $email, $address, $branch_city, $branch_region, $city, $province, $region, $isautoassign, $loc_latitude, $loc_longitude, $idnopb, $treshold);
                            $this->Model_shops->save_shop_bank_det($bankname, $acctname, $acctno, $desc, $mainshop, $branchid);
                            $data = array(
                                "success" => 1,
                                "message" => 'Branch Saved!'
                            );
                            $this->audittrail->logActivity('Shop Branch', $branchname.' branch has been successfully added.', 'add', $this->session->userdata('username'));
                        }else{
                            $data = array(
                                "success" => 0,
                                "message" => 'IDNO already exist!'
                            );
                        }
                    // }else{
                    //     $data = array(
                    //         "success" => 0,
                    //         "message" => 'Invalid Phone Number, Please try again.'
                    //     );
                    // }
                }else{
                    $data = array(
                        "success" => 0,
                        "message" => 'Branch name already exist, Please try again.'
                    );
                }
            }else{
                $data = array(
                            "success" => 0,
                            "message" => $checkisautoassign['errormsg']
                        );                
            }
        }else{
            $data = array(
                        "success" => 0,
                        "message" => 'Please Complete All Required Fields!'
                    );
        }
        generate_json($data);
    }

    public function edit($id, $token = ''){
        $this->isLoggedIn();
        //start - for restriction of views
        if ($this->loginstate->get_access()['shop_branch']['update'] == 1 || $this->loginstate->get_access()['shop_branch']['view'] == 1 || $this->loginstate->get_access()['branch_account']['view'] == 1 || $this->loginstate->get_access()['branch_account']['update'] == 1){
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            //end - for restriction of views main_nav_id
            $id = en_dec('dec', $id);
            $city = $this->Model_shopbranch->get_all_city()->result();
            $region = $this->Model_shopbranch->get_all_region()->result();
            $province = $this->Model_shopbranch->get_all_province()->result();
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $back_button = '<button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="'.base_url('Shopbranch/home/'.$token).'">Back</button>';
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'idno' => $id,
                'city' => $city,
                'region' => $region,
                'province' => $province,
                'mainshop' => $mainshop,
                'back_button' => $back_button,
                'main_js' => 'assets/js/shop_branch/shop_branch_edit.js',
                'googlemap_js' => 'assets/js/shop_branch/googlemapedit_branch.js',
                'breadcrumbs' => 'Branch Details'
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('shop_branch/shop_branch_add', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_record_details(){
        $idno = sanitize($this->input->post('idno'));
        if(!empty($idno)){
            $record_details = $this->Model_shopbranch->get_record_details($idno);
            $data = array(
                        "success" => 1,
                        "record_details" => $record_details->row()
                    );
        }else{
            $data = array(
                        "success" => 0,
                        "message" => 'Missing idno!'
                    );            
        }
        generate_json($data);
    }

    public function update_shop_branch(){
        $idno = sanitize($this->input->post('idno_hidden'));
        $mainshop = sanitize($this->input->post('entry-mainshop'));
        $branchname = sanitize($this->input->post('entry-branch'));
        $contactperson = sanitize($this->input->post('entry-contactperson'));
        $conno = sanitize($this->input->post('entry-conno'));
        $email = sanitize($this->input->post('entry-email'));
        $address = sanitize($this->input->post('entry-address'));
        $branch_city = sanitize($this->input->post('entry-branch_city'));
        $branch_region = sanitize($this->input->post('entry-branch_region'));
        $loc_latitude = sanitize($this->input->post('loc_latitude'));
        $loc_longitude = sanitize($this->input->post('loc_longitude'));

        //Bank Details
        $bankname = sanitize($this->input->post('entry-bankname'));
        $acctname = sanitize($this->input->post('entry-acctname'));
        $acctno = sanitize($this->input->post('entry-acctno'));
        $desc = sanitize($this->input->post('entry-desc'));

        //Admin Settings
        $idnopb = sanitize($this->input->post('entry-idnopb'));
        $treshold = sanitize($this->input->post('entry-treshold'));

        if(!empty($this->input->post('entry-city'))){
            $city = implode(",", $this->input->post('entry-city'));
        }else{
            $city = "";
        }
        if(!empty($this->input->post('entry-region'))){
            $region = implode(",", $this->input->post('entry-region'));
        }else{
            $region = "";
        }
        if(!empty($this->input->post('entry-province'))){
            $province = implode(",", $this->input->post('entry-province'));
        }else{
            $province = "";
        }
        $isautoassign = sanitize($this->input->post('entry-isautoassign'));

        if(!empty($idno) AND !empty($mainshop) AND !empty($branchname) AND !empty($contactperson) AND !empty($conno) AND !empty($email) AND !empty($address) AND !empty($branch_region) AND !empty($branch_region) AND !empty($loc_latitude) AND !empty($loc_longitude) AND !empty($bankname) AND  !empty($acctname) AND  !empty($acctno) AND  !empty($desc)){
            $checkisautoassign = $this->checkisautoassign($isautoassign, $city, $region, $province);
            if($checkisautoassign['status']){
                if(!$this->Model_shopbranch->name_is_exist_edit($email, $idno)){
                    if($this->Model_shopbranch->idnopb_isvalid_edit($idnopb, $idno)){
                        $prev_data = $this->Model_shopbranch->get_record_details($idno)->row_array();
                        $this->Model_shopbranch->update_shop_branch($idno, $mainshop, $branchname, $contactperson, $conno, $email, $address, $branch_city, $branch_region, $city, $province, $region, $isautoassign, $loc_latitude, $loc_longitude, $idnopb, $treshold);
                        $this->Model_shops->update_shop_bank_det($bankname, $acctname, $acctno, $desc, $mainshop, $idno);
                        $data = array(
                            "success" => 1,
                            "message" => 'Record Updated!'
                        );  
                        
                        $audit_string = $this->audittrail->branchString($prev_data, $this->input->post());
                        $this->audittrail->logActivity('Shop Branch', $branchname." branch has been updated successfully. \nChanges: \n".$audit_string, 'update', $this->session->userdata('username'));
                    }else{
                        $data = array(
                            "success" => 0,
                            "message" => 'IDNO already exist!'
                        );
                    }
                }else{
                    $data = array(
                            "success" => 0,
                            "message" => 'Branch name already exist, Please try again.'
                        );
                }
            }else{
                $data = array(
                            "success" => 0,
                            "message" => $checkisautoassign['errormsg']
                        );                
            }
        }else{
            $data = array(
                        "success" => 0,
                        "message" => 'Please Complete All Required Fields!'
                    );
        }
        generate_json($data);
        die();
    }

    public function get_shop_branches(){
        $mainshopid = sanitize($this->input->post('mainshopid'));
        $shopbranches = $this->Model_shopbranch->get_all_branch($mainshopid);
        if($shopbranches->num_rows() > 0){
            $data = array(
                "success" => 1,
                "shopbranches" => $shopbranches->result()
            );
        }else{
            $data = array(
                "success" => 0
            );
        }
        generate_json($data);
    }

    public function reassign_branch(){
        $mainshopid = sanitize($this->input->post('mainshopid'));
        $branchid = sanitize($this->input->post('branchid'));
        $reference_num = sanitize($this->input->post('reference_num'));
        $remarks = sanitize($this->input->post('remarks'));

        if(!empty($mainshopid) AND !empty($remarks) AND !empty($reference_num)){
            $this->Model_shopbranch->reassign_branch($branchid, $reference_num, $remarks);
            $data = array(
                        'success' => 1,
                        'message' => 'Order Transfered!'
                    );
        }else{
            $data = array(
                        'success' => 0,
                        'message' => 'Please Complete All Required Fields'
                    );
        }
        generate_json($data);
    }

    public function get_city_of_region(){
        $region = sanitize($this->input->post('region'));

        if(!empty($region)){
            $cityofregion = $this->Model_shopbranch->get_city_of_region($region)->result();
            $data = array(
                        'success' => 1,
                        'cityofregion' => $cityofregion
                    );
        }else{
            $data = array(
                        'success' => 0,
                        'message' => 'Missing region code'
                    );
        }
        generate_json($data);
    }

    public function checkisautoassign($isautoassign, $city, $region, $province){
        if($isautoassign == 1){
            if(empty($city) AND empty($region) AND empty($province)){
                $data = array(
                            'status' => false,
                            'errormsg' => 'Please input at least 1 delivery area if auto assign orders is on.'
                        );
            }else{
                $data = array(
                            'status' => true
                        );
            }
        }else{
            $data = array(
                            'status' => true
                        );
        }
        return $data;
    }

    public function branch_account($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['branch_account']['view'] == 1 AND !empty($this->session->userdata('branchid'))){
            $branchid = en_dec('en', $this->session->userdata('branchid'));
            header("location:".base_url('Shopbranch/manage/'.$branchid.'/'.$token));
        }else{
            $this->load->view('error_404');
        }
    }
}

?>