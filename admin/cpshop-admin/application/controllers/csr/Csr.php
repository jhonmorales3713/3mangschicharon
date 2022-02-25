<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Csr extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('csr/Model_csr');
        $this->load->model('csr/Model_csr_orders');
        $this->load->model('orders/model_orders');
        $this->load->model('shop_branch/Model_shopbranch');
        $this->load->model('shops/Model_shops');
	}
    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    public function views_restriction($content_url){
        //this code is for destroying session and page if they access restricted 
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
            $query = $this->Model_csr->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
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

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->Model_csr->disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function create_ticket($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            $branchlist = $this->Model_csr->get_all_branch()->result();
            $mainshoplist = $this->Model_csr->get_all_mainshop()->result();
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'breadcrumbs' => 'Customer Support',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'branchlist' => $branchlist,
                'mainshoplist' => $mainshoplist
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function orders_view($token = '', $ref_num)
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = 0;

        if($sys_shop == 0) {
            $split = explode("-",$ref_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        } else {
            $reference_num = $ref_num;
        }
        $row = $this->model_orders->orders_details($reference_num, $sys_shop);
        if(!empty($row)){
            $refcode = $this->model_orders->get_referral_code($row['reference_num']);
            $branch_details = $this->model_orders->get_branch_details($reference_num, $sys_shop)->row();
            if($sys_shop != 0){
                $mainshopname = $this->model_orders->get_mainshopname($sys_shop)->row()->shopname;
            }else{
                $mainshopname = 'CSR View Order';
            }
            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'partners'            => $this->model_orders->get_partners_options(),
                'payments'            => $this->model_orders->get_payments_options(),
                'reference_num'       => $reference_num,
                'order_details'       => $row,
                'referral'            => $refcode,
                'branch_details'      => $branch_details,
                'mainshopname'        => $mainshopname,
                'mainshopid'          => $sys_shop,
                'branch_count'        => count($this->model_orders->get_all_branch($sys_shop)->result()),
                'url_ref_num'         => $ref_num
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/orders/csr_orders_view', $data_admin);
        }else{
            $this->load->view('error_404');
        }
        
    }

    public function customer_list(){
        $this->isLoggedIn();
        $query = $this->Model_csr->customer_list();
        generate_json($query);
    }    

    public function ticket_history($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['ticket_history']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            $issuecat = $this->Model_csr->get_all_issuecat()->result();
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'breadcrumbs' => 'Ticket History'
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/ticket_history', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function ticket_list(){
        $this->isLoggedIn();
        $query = $this->Model_csr->ticket_list();
        generate_json($query);
    }

    public function export_ticket_list(){

        $this->isLoggedIn();         

        $ticket_list = $this->Model_csr->ticket_list(true);

        $filters = json_decode($this->input->post('_filters'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();                
    
        $sheet->setCellValue('B1', 'Ticket History');
        $sheet->setCellValue('B2', $filters->date_from.' - '.$filters->date_to);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
    
        $sheet->setCellValue('A6', 'Ticket Number');
        $sheet->setCellValue('B6', 'Subject');
        $sheet->setCellValue('C6', 'Customer');
        $sheet->setCellValue('D6', 'Category');
        $sheet->setCellValue('E6', 'Status');        
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($ticket_list['data'] as $key => $row) {
    
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[4]            
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A7');
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Ticket History';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }
    
    public function save_ticket(){
        $tickettype = sanitize($this->input->post('entry-tickettype'));
        $branchid = sanitize($this->input->post('entry-branchid'));
        $shopid = sanitize($this->input->post('entry-shopid'));
        $ticketrefno = generate_randomid("CSRTICKET", 8);
        $sys_users_id = $this->session->userdata('sys_users_id');
        $member_type = $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type;
        if($tickettype != ""){
            if(!$this->Model_csr->is_ticket_exist($ticketrefno)){
                $this->Model_csr->save_ticket($ticketrefno, $tickettype, $branchid, $shopid, $member_type);
                $this->Model_csr->save_ticket_details($ticketrefno, 'Open Ticket');
                $recordid = $this->Model_csr->get_id_byticketno($ticketrefno)->row()->id;
                $data = array(
                    "success" => 1,
                    "ticketrefno" => $ticketrefno,
                    "recordid" => en_dec('en', $recordid),
                    "message" => 'Ticket Created successfully, ticket reference below'
                );
            }else{
                $data = array(
                    "success" => 0,
                    "message" => 'Oops, something went wrong. Please try again later.'
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

    public function edit_ticket($id, $token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['update'] == 1 || $this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $issuecat = $this->Model_csr->get_all_issuecat()->result();
            $ticket_details = $this->Model_csr->get_ticket_details(en_dec('dec', $id))->row();
            $sys_users_id = $this->session->userdata('sys_users_id');
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Ticket Details',
                'issuecat' => $issuecat,
                'idno' => $id,
                'main_js' => 'assets/js/csr/update_ticket.js',
                'ticket_details' => $ticket_details,
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function update_ticket(){
        if ($this->loginstate->get_access()['csr_ticket_log']['update'] == 1 || $this->loginstate->get_access()['csr_ticket_log']['view'] == 1){
            $id = sanitize($this->input->post('idno_hidden'));
            $subject = sanitize($this->input->post('entry-subject'));
            $subcatid = sanitize($this->input->post('entry-subcategory'));
            $callername = sanitize($this->input->post('entry-callername'));
            $agentid = sanitize($this->input->post('entry-agentid'));
            $priolevel = sanitize($this->input->post('entry-priolevel'));
            $id = en_dec('dec', $id);
            $ticketrefno = $this->Model_csr->get_ticketrefno($id)->row()->ticket_refno;
            $sys_users_id = $this->session->userdata('sys_users_id');
            $member_type = $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type;
            if(!empty($id) AND !empty($subject) AND !empty($subcatid) AND !empty($priolevel) AND !empty($agentid)){
                if(empty($agentid) AND $member_type == 4){
                    $data = array(
                                "success" => 0,
                                "message" => 'Please Complete All Required Fields!'
                            );
                }else{
                    $this->Model_csr->update_ticket($id, $subject, $subcatid, $callername, $agentid, $priolevel);
                    $this->Model_csr->save_ticket_details($ticketrefno, "Updated the ticket main details");
                    $data = array(
                        "success" => 1,
                        "message" => 'Ticket Details Saved!'
                    );
                }
            }else{
                $data = array(
                            "success" => 0,
                            "message" => 'Please Complete All Required Fields!'
                        );
            }
            generate_json($data);
        }else{
            $this->load->view('error_404');
        }
    }

    public function ticket_log($id, $token = '')
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        if ($this->loginstate->get_access()['csr_ticket_log']['update'] == 1 || $this->loginstate->get_access()['csr_ticket_log']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $sub_category = $this->Model_csr->get_all_issuecat()->result();
            $ticket_details = $this->Model_csr->get_ticket_details(en_dec('dec', $id))->row();
            $log_details = $this->Model_csr->get_log_details(en_dec('dec', $id))->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            $agent_list = $this->Model_csr->agent_list()->result();
            $ticket_type = $this->Model_csr->get_all_ticket_type()->result();
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            //$branch_details = $this->model_orders->get_branch_details($reference_num, $sys_shop)->row();
            $mainshopname = 'CSR View Order';

            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Ticket Logs',
                'sub_category' => $sub_category,
                'idno' => $id,
                'main_js' => 'assets/js/csr/ticket_log.js',
                'ticket_details' => $ticket_details,
                'log_details' => $log_details,
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'agent_list' => $agent_list,
                'ticket_type' => $ticket_type
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/ticket_log', $data_admin);
            // end - load all the views synchronously

        }else{
            $this->load->view('error_404');
        }
    }

    public function close_ticket(){
        if ($this->loginstate->get_access()['csr_ticket_log']['update'] == 1 || $this->loginstate->get_access()['csr_ticket_log']['view'] == 1){
            $close_id = sanitize($this->input->post('close_id'));
            $close_id = en_dec('dec', $close_id);
            $ticketrefno = $this->Model_csr->get_ticketrefno($close_id)->row()->ticket_refno;
            if(!empty($close_id)){
                $this->Model_csr->close_ticket($close_id);
                $this->Model_csr->save_ticket_details($ticketrefno, 'Ticket Closed');
                $data = array(
                            "success" => 1,
                            "message" => "Ticket Closed successfully"
                        );
            }else{
                $data = array(
                            "success" => 0,
                            "message" => "Missing ticket id"
                        );
            }
            generate_json($data);
        }else{
            $this->load->view('error_404');
        }
    }

    public function reopen_ticket(){
        if ($this->loginstate->get_access()['csr_ticket_log']['update'] == 1 || $this->loginstate->get_access()['csr_ticket_log']['view'] == 1){
            $reopen_id = sanitize($this->input->post('reopen_id'));
            $reopen_id = en_dec('dec', $reopen_id);
            $ticketrefno = $this->Model_csr->get_ticketrefno($reopen_id)->row()->ticket_refno;
            if(!empty($reopen_id)){
                $this->Model_csr->reopen_ticket($reopen_id);
                $this->Model_csr->save_ticket_details($ticketrefno, 'Ticket Re Open');
                $data = array(
                            "success" => 1,
                            "message" => "Ticket Re Open successfully"
                        );
            }else{
                $data = array(
                            "success" => 0,
                            "message" => "Missing ticket id"
                        );
            }
            generate_json($data);
        }else{
            $this->load->view('error_404');
        }
    }

    public function save_ticketlog(){
        if ($this->loginstate->get_access()['csr_ticket_log']['update'] == 1 || $this->loginstate->get_access()['csr_ticket_log']['view'] == 1){
            $id = sanitize($this->input->post('idno_hidden'));
            $subject = sanitize($this->input->post('entry-subject'));
            $subcatid = sanitize($this->input->post('entry-subcategory'));
            $ticket_type = sanitize($this->input->post('entry-ticket_type'));
            $agentid = sanitize($this->input->post('entry-agentid'));
            $priolevel = sanitize($this->input->post('entry-priolevel'));
            $commentbox = $this->input->post('commentbox');
            $id = en_dec('dec', $id);
            $ticketrefno = $this->Model_csr->get_ticketrefno($id)->row()->ticket_refno;
            $sys_users_id = $this->session->userdata('sys_users_id');
            $member_type = $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type;
            if(!empty($id) AND !empty($subject) AND !empty($subcatid) AND !empty($priolevel) AND !empty($agentid) AND !empty($ticket_type)){
                if(empty($agentid) AND $member_type == 4){
                    $data = array(
                                "success" => 0,
                                "message" => 'Please Complete All Required Fields!'
                            );
                }else{
                    $this->Model_csr->update_ticket($id, $subject, $subcatid, $ticket_type, $agentid, $priolevel);
                    if(!empty($commentbox)){
                        $this->Model_csr->save_ticket_details($ticketrefno, $commentbox);
                    }else{
                        $this->Model_csr->save_ticket_details($ticketrefno, "Updated the ticket main details");
                    }
                    $data = array(
                        "success" => 1,
                        "message" => 'Ticket Submitted!'
                    );
                }
            }else{
                $data = array(
                            "success" => 0,
                            "message" => 'Please Complete All Required Fields!'
                        );
            }
            generate_json($data);
        }else{
            $this->load->view('error_404');
        }
    }

    public function approve_ticket(){
        if ($this->loginstate->get_access()['ticket_history']['update'] == 1 || $this->loginstate->get_access()['ticket_history']['view'] == 1){
            $approve_id = sanitize($this->input->post('approve_id'));
            $approve_id = en_dec('dec', $approve_id);
            $ticketrefno = $this->Model_csr->get_ticketrefno($approve_id)->row()->ticket_refno;
            if(!empty($approve_id)){
                $this->Model_csr->approve_ticket($approve_id);
                $this->Model_csr->save_ticket_details($ticketrefno, 'Ticket Approved');
                $data = array(
                            "success" => 1,
                            "message" => "Ticket Approved"
                        );
            }else{
                $data = array(
                            "success" => 0,
                            "message" => "Missing ticket id"
                        );
            }
            generate_json($data);
        }else{
            $this->load->view('error_404');
        }
    }

    public function reject_ticket(){
        if ($this->loginstate->get_access()['ticket_history']['update'] == 1 || $this->loginstate->get_access()['ticket_history']['view'] == 1){
            $reject_id = sanitize($this->input->post('reject_id'));
            $reject_id = en_dec('dec', $reject_id);
            $ticketrefno = $this->Model_csr->get_ticketrefno($reject_id)->row()->ticket_refno;
            if(!empty($reject_id)){
                $this->Model_csr->reject_ticket($reject_id);
                $this->Model_csr->save_ticket_details($ticketrefno, 'Ticket Rejected');
                $data = array(
                            "success" => 1,
                            "message" => "Ticket Rejected"
                        );
            }else{
                $data = array(
                            "success" => 0,
                            "message" => "Missing ticket id"
                        );
            }
            generate_json($data);
        }else{
            $this->load->view('error_404');
        }
    }

    public function validate_orderrefno(){
        $orderrefno = sanitize($this->input->post('entry-orderrefno'));
        $row = $this->model_orders->orders_details($orderrefno, 0);
        if(!empty($row) AND !empty($orderrefno)){
            $data = array(
                "success" => 1
            );
        }else{
            $data = array(
                "success" => 0,
                "message" => 'Invalid Order reference no.'
            );
        }
        generate_json($data);
    }

    public function view_orders($token, $ticket_type, $ref_num=''){
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $reference_num = $ref_num;
        $sys_shop = 0;
        $orderDetails = $this->Model_csr_orders->getByRefNum($ref_num);
        $branchlist = $this->Model_csr->get_all_branch()->result();
        $mainshoplist = $this->Model_csr->get_all_mainshop()->result();
        if ($this->loginstate->get_access()['csr_ticket']['update'] == 1 || $this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            if(!empty($orderDetails)){
                $orderItems = $this->Model_csr_orders->getOrderDetails(null,null,$orderDetails["order_id"])->result_array();
                // dd($orderDetails);
                $shopItems = array();
                foreach($orderItems as $item){
                    $shopDetails = $this->Model_csr_orders->getShopDetails($item["sys_shop"]);
                    // $shippingPerShop = $this->model->getShippingPerShop($orderDetails["areaid"],$item["sys_shop"])->row_array();
                    $shippingPerShop = $this->Model_csr_orders->getShippingPerShop($ref_num, $item["sys_shop"])->row_array();

                    $orderStatus = $this->Model_csr_orders->getOrderStatusPerShop($ref_num, $item["sys_shop"]);
                    if($orderStatus != null && $orderStatus != "" && ini() != "jcww") {
                        $order_status = $orderStatus["order_status"];
                        $date_shipped = $orderStatus["date_shipped"];
                        $date_ordered = $orderStatus["date_ordered"];
                        $date_order_processed = $orderStatus["date_order_processed"];
                        $date_ready_pickup = $orderStatus["date_ready_pickup"];
                        $date_booking_confirmed = $orderStatus["date_booking_confirmed"];
                        $date_fulfilled = $orderStatus["date_fulfilled"];
                    }
                    else {
                        $order_status = $orderDetails["order_status"];
                        $date_shipped = $orderDetails["date_shipped"];
                        $date_ordered = $orderDetails["date_ordered"];
                        $date_order_processed = '';
                        $date_ready_pickup = '';
                        $date_booking_confirmed = '';
                        $date_fulfilled = '';
                    }

                    if(empty($shopItems[$item["sys_shop"]])) {
                      $shopItems[$item["sys_shop"]] = array(
                        "shopname" => $shopDetails["shopname"],
                        "shopcode" => $shopDetails["shopcode"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingPerShop["dts"],
                        "shopdts_to" => $shippingPerShop["dts_to"],
                        "shippingfee" => $shippingPerShop["sf"],
                        "logo" => $shopDetails["logo"],
                        "order_status" => $order_status,
                        "date_shipped" => $date_shipped,
                        "date_ordered" => $date_ordered,
                        "date_order_processed" => $date_order_processed,
                        "date_ready_pickup" => $date_ready_pickup,
                        "date_booking_confirmed" => $date_booking_confirmed,
                        "date_fulfilled" => $date_fulfilled,
                      );
                      $shopItems[$item["sys_shop"]]["items"] = array();
                    }
                    array_push($shopItems[$item["sys_shop"]]["items"], array(
                            "productid" => $item["product_id"],
                            "itemname" => $item["itemname"],
                            "unit" => $item["otherinfo"],
                            "quantity" => $item["quantity"],
                            "price" => $item["amount"]
                    ));
                }

                // Get voucher details
                $voucherAmount = 0.00;
                $vouchers = $this->Model_csr_orders->getOrderVoucher($orderDetails["reference_num"]);
                if (sizeof($vouchers) > 0) {
                    // loop through voucher records, if payment_type is toktokmall add amount to voucherAmount
                    foreach($vouchers as $key => $value) {
                      if ($value['payment_type'] == 'toktokmall') {
                        $shopItems[$value['shopid']]['vouchers'][] = array(
                            'vcode' => $value['payment_refno'],
                            'vamount' => $value['amount']
                        );
                        $shopItems[$value['shopid']]['voucherSubTotal'] = isset($shopItems[$value['shopid']]['voucherSubTotal']) ? floatval($shopItems[$value['shopid']]['voucherSubTotal']) + floatval($value['amount']) : floatval($value['amount']);
                        $voucherAmount += floatval($value['amount']);
                      }
                    }
                }
                $orderDetails['voucherAmount'] = $voucherAmount;
                $est_deliveryArr = "NA";
                $data = array(
                'status' => $this->input->get('status', TRUE),
                'order_details' => $orderDetails,
                'order_items' => $shopItems,
                'est_delivery_date' => generate_est_delivery_date($est_deliveryArr),
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                );
                $data['categories'] = null;

                $data_admin = array(
                    'token' => $token,
                    'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                    'breadcrumbs' => 'Ticket Logs',
                    'maincat' => $maincat,
                    'main_js' => 'assets/js/csr/create_ticket.js',
                    'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                    'view_orders' => $this->load->view("csr/orders/check-order-page", $data, true),
                    'branchlist' => $branchlist,
                    'mainshoplist' => $mainshoplist,
                    'ticket_type' => $ticket_type
                );
                // end - data to be used for views
                
                // start - load all the views synchronously
                $this->load->view('includes/header', $data_admin);
                $this->load->view('csr/create_ticket', $data_admin);
                // end - load all the views synchronously
            }else{
                //do nothing for now
            }
        }else{
            $this->load->view('error_404');
        }
    }

    public function view_customer_table($token, $ticket_type){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            $branchlist = $this->Model_csr->get_all_branch()->result();
            $mainshoplist = $this->Model_csr->get_all_mainshop()->result();

            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Create ticket',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'view_customer_table' => $this->load->view('csr/account/customer_table', '', true),
                'branchlist' => $branchlist,
                'mainshoplist' => $mainshoplist,
                'ticket_type' => $ticket_type
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function view_customer($token, $ticket_type, $id=''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            $customer_details = $this->Model_csr->get_customerdetails(en_dec('dec', $id))->row();
            $branchlist = $this->Model_csr->get_all_branch()->result();
            $mainshoplist = $this->Model_csr->get_all_mainshop()->result();

            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin_2 = array(
                    'customer_details' => $customer_details
                );
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Create ticket',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'view_customer' => $this->load->view('csr/account/view_customer', $data_admin_2, true),
                'branchlist' => $branchlist,
                'mainshoplist' => $mainshoplist,
                'ticket_type' => $ticket_type
            );
        // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function validate_branch(){
        $email = sanitize($this->input->post('entry-email'));
        $row = $this->Model_csr->validate_branch($email)->row();
        if(!empty($row) AND !empty($email)){
            $data = array(
                "success" => 1,
                "branch_id" => en_dec('en', $row->id)
            );
        }else{
            $data = array(
                "success" => 0,
                "message" => 'Account not found.'
            );
        }
        generate_json($data);
    }

    public function view_branch($token, $ticket_type, $id=''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $id = en_dec('dec', $id);
            $city = $this->Model_shopbranch->get_all_city()->result();
            $region = $this->Model_shopbranch->get_all_region()->result();
            $province = $this->Model_shopbranch->get_all_province()->result();
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $branchlist = $this->Model_csr->get_all_branch()->result();
            $mainshoplist = $this->Model_csr->get_all_mainshop()->result();
            // start - data to be used for views
            $data_admin_2 = array(
                'token' => $token,
                'idno' => $id,
                'city' => $city,
                'region' => $region,
                'province' => $province,
                'mainshop' => $mainshop,
                'breadcrumbs' => 'Branch Details'
            );
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Create ticket',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'view_branch' => $this->load->view('csr/branch/view_branch', $data_admin_2, true),
                'view_branch_js'=> 'assets/js/shop_branch/shop_branch_edit.js',
                'branchlist' => $branchlist,
                'mainshoplist' => $mainshoplist,
                'ticket_type' => $ticket_type
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function validate_shop(){
        $email = sanitize($this->input->post('entry-email'));
        $row = $this->Model_csr->validate_shop($email)->row();
        if(!empty($row) AND !empty($email)){
            $data = array(
                "success" => 1,
                "shop_id" => en_dec('en', $row->id)
            );
        }else{
            $data = array(
                "success" => 0,
                "message" => 'Account not found.'
            );
        }
        generate_json($data);
    }

    public function view_shop($token, $ticket_type, $id=''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $id = en_dec('dec', $id);
            $region = $this->Model_shopbranch->get_all_region()->result();
            $mainshop = $this->Model_shopbranch->get_all_shop()->result();
            $branchlist = $this->Model_csr->get_all_branch()->result();
            $mainshoplist = $this->Model_csr->get_all_mainshop()->result();
            // start - data to be used for views
            $data_admin_2 = array(
                'token' => $token,
                'idno' => $id,
                'region' => $region,
                'sys_shop_details' => $this->Model_shops->get_shop_details($id)->row(),
                'breadcrumbs' => 'Shop Details'
            );
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Create ticket',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'view_shop' => $this->load->view('csr/shop/view_shop', $data_admin_2, true),
                'view_shop_js'=> 'assets/js/shops/shop_edit.js',
                'branchlist' => $branchlist,
                'mainshoplist' => $mainshoplist,
                'ticket_type' => $ticket_type
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function view_account_table($token, $ticket_type){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');

            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Create ticket',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'view_account_table' => $this->load->view('csr/account/account_table', '', true),
                'ticket_type' => $ticket_type
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function view_account($token, $ticket_type, $id=''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['csr_ticket']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $maincat = $this->Model_csr->get_all_maincat()->result();
            $sys_users_id = $this->session->userdata('sys_users_id');
            $account_details = $this->Model_csr->get_accountdetails(en_dec('dec', $id))->row();
            //end - for restriction of views main_nav_id
            // start - data to be used for views
            $data_admin_2 = array(
                    'account_details' => $account_details
                );
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $this->model->get_main_nav_id_cn_url($content_url), //for highlight the navigation
                'breadcrumbs' => 'Create ticket',
                'maincat' => $maincat,
                'idno' => '',
                'main_js' => 'assets/js/csr/create_ticket.js',
                'member_type' => $this->Model_csr->get_membertype_id($sys_users_id)->row()->member_type,
                'view_account' => $this->load->view('csr/account/view_account', $data_admin_2, true),
                'ticket_type' => $ticket_type
            );
        // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('csr/create_ticket', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function account_list(){
        $this->isLoggedIn();
        $query = $this->Model_csr->account_list();
        generate_json($query);
    }

    public function checkOrder(){
        $orderDetails = $this->Model_csr_orders->getByRefNum($this->input->get("refno", TRUE));
        if(!empty($orderDetails)) {
            $orderItems = $this->Model_csr_orders->getOrderDetails(null,null,$orderDetails["order_id"])->result_array();
            // dd($orderDetails);
            $shopItems = array();
            foreach($orderItems as $item){
                $shopDetails = $this->Model_csr_orders->getShopDetails($item["sys_shop"]);
                // $shippingPerShop = $this->model->getShippingPerShop($orderDetails["areaid"],$item["sys_shop"])->row_array();
                $shippingPerShop = $this->Model_csr_orders->getShippingPerShop($this->input->get("refno", TRUE), $item["sys_shop"])->row_array();

                $orderStatus = $this->Model_csr_orders->getOrderStatusPerShop($this->input->get("refno", TRUE), $item["sys_shop"]);
                if($orderStatus != null && $orderStatus != "" && ini() != "jcww") {
                    $order_status = $orderStatus["order_status"];
                    $date_shipped = $orderStatus["date_shipped"];
                    $date_ordered = $orderStatus["date_ordered"];
                    $date_order_processed = $orderStatus["date_order_processed"];
                    $date_ready_pickup = $orderStatus["date_ready_pickup"];
                    $date_booking_confirmed = $orderStatus["date_booking_confirmed"];
                    $date_fulfilled = $orderStatus["date_fulfilled"];
                }
                else {
                    $order_status = $orderDetails["order_status"];
                    $date_shipped = $orderDetails["date_shipped"];
                    $date_ordered = $orderDetails["date_ordered"];
                    $date_order_processed = '';
                    $date_ready_pickup = '';
                    $date_booking_confirmed = '';
                    $date_fulfilled = '';
                }

                if(empty($shopItems[$item["sys_shop"]])) {
                  $shopItems[$item["sys_shop"]] = array(
                    "shopname" => $shopDetails["shopname"],
                    "shopcode" => $shopDetails["shopcode"],
                    "shopemail" => $shopDetails["email"],
                    "shopmobile" => $shopDetails["mobile"],
                    "shopdts" => $shippingPerShop["dts"],
                    "shopdts_to" => $shippingPerShop["dts_to"],
                    "shippingfee" => $shippingPerShop["sf"],
                    "logo" => $shopDetails["logo"],
                    "order_status" => $order_status,
                    "date_shipped" => $date_shipped,
                    "date_ordered" => $date_ordered,
                    "date_order_processed" => $date_order_processed,
                    "date_ready_pickup" => $date_ready_pickup,
                    "date_booking_confirmed" => $date_booking_confirmed,
                    "date_fulfilled" => $date_fulfilled,
                  );
                  $shopItems[$item["sys_shop"]]["items"] = array();
                }
                array_push($shopItems[$item["sys_shop"]]["items"], array(
                        "productid" => $item["product_id"],
                        "itemname" => $item["itemname"],
                        "unit" => $item["otherinfo"],
                        "quantity" => $item["quantity"],
                        "price" => $item["amount"]
                ));
            }

            // Get voucher details
            $voucherAmount = 0.00;
            $vouchers = $this->Model_csr_orders->getOrderVoucher($orderDetails["reference_num"]);
            if (sizeof($vouchers) > 0) {
                // loop through voucher records, if payment_type is toktokmall add amount to voucherAmount
                foreach($vouchers as $key => $value) {
                  if ($value['payment_type'] == 'toktokmall') {
                    $shopItems[$value['shopid']]['vouchers'][] = array(
                        'vcode' => $value['payment_refno'],
                        'vamount' => $value['amount']
                    );
                    $shopItems[$value['shopid']]['voucherSubTotal'] = isset($shopItems[$value['shopid']]['voucherSubTotal']) ? floatval($shopItems[$value['shopid']]['voucherSubTotal']) + floatval($value['amount']) : floatval($value['amount']);
                    $voucherAmount += floatval($value['amount']);
                  }
                }
            }
            $orderDetails['voucherAmount'] = $voucherAmount;
            $est_deliveryArr = "NA";
            $data = array(
            'status' => $this->input->get('status', TRUE),
            'order_details' => $orderDetails,
            'order_items' => $shopItems,
            'est_delivery_date' => generate_est_delivery_date($est_deliveryArr),
            );
            $data['categories'] = null;
            $this->load->view("includes/header", $data);
            $this->load->view("csr/orders/check-order-page", $data);
        }
        else {
            $this->index();
        }

    }

}

?>