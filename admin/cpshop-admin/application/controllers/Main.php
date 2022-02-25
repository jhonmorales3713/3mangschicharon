<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Main extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tips/Model_tips');        
        $this->load->model('reports/Model_product_orders_report','model_product_orders_report');
        $this->load->model('reports/Model_product_orders_report','product_orders_report');
        $this->load->model('shops/Model_shops');
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function index()
    {
        if ($this->session->userdata('isLoggedIn') == true) {
            $token_session = $this->session->userdata('token_session');
            $token = en_dec('en', $token_session);
            header("location:" . base_url('Main_page/display_page/home/' . $token));
        }
        $this->load->view('login');
    }

    public function login()
    {
        $username = sanitize($this->input->post('loginUsername'));
        $password = sanitize($this->input->post('loginPassword'));

        $validate_username = $this->model->validate_username($username);
        if ($validate_username->num_rows() > 0) { // check if email is exist
            $userObj = $validate_username->row(); // get the matched data
            $verify_username = $userObj->active; // check if unverified email
            $verify_status   = ($userObj->shop_status == '') ? 1 : $userObj->shop_status; // check shop status
            $verify_status   = ($verify_status > 0 ) ? $verify_status : 0; // check shop status
            $branch_status   = ($userObj->branchid == 0) ? 1 : $userObj->branch_status; // check shop status
            $branch_status   = ($userObj->branch_status == '') ? 1 : $userObj->branch_status; // check shop status
            $branch_status   = ($branch_status > 0 ) ? $branch_status : 0; // check shop status
            $hash_password   = $userObj->password;
            $code_isset      = $userObj->code_isset;
            $user_status     = $userObj->user_status;


            if($user_status  == 3){

                $this->audittrail->logActivity('Login', $username.' attempts and failed to login. Merchant login need to approve 1st.', 'login', 'none');
                $data = array(
                    'success' => 0,
                    'message' => 'Your account will remain offline while we\'re setting up your store.',
                );
     
            }


            else if ($verify_username == 1 && $verify_status > 0 && $branch_status > 0) {
                $hash_password = $userObj->password;
                if (password_verify($password, $hash_password)) { // password is valid

                    $userData = array( // store in array
                        'id' => $userObj->sys_users_id,
                        'username' => $userObj->username,
                        'avatar' => $userObj->avatar,
                        'functions' => $userObj->functions,
                        'access_nav' => $userObj->access_nav,
                        'access_content_nav' => $userObj->access_content_nav,
                        'sys_shop' => $userObj->sys_shop,
                        'fname' => $userObj->fname,
                        'mname' => $userObj->mname,
                        'lname' => $userObj->lname,
                        'email' => $userObj->email,
                        'mobile_number' => $userObj->mobile_number,
                        'comm_type' => $userObj->comm_type,
                        'branchid' => $userObj->branchid,
                        'shopcode' => $userObj->shopcode == null ? 0 : $userObj->shopcode,
                        'shopname' => $userObj->shopname == null ? 0 : $userObj->shopname,
                        'shop_logo' => $userObj->logo == null ? 0 : $userObj->logo,
                        'shop_url' => $userObj->shopurl == null ? 0 : $userObj->shopurl,
                        'sys_users_id' => $userObj->sys_users_id,
                        'app_members_id' => $userObj->app_members_id,
                        'sys_shop_id' => $userObj->sys_shop_id,                        
                        'isLoggedIn' => true,

                        // 'get_position_access' => $this->model->get_position_details_access($userObj->position_id)->row(),
                    );

                    // Record time of log in of merchant
                    if ($userData['shopname'] != '') {
                        $this->model->log_seller_time_activity($userData['sys_users_id'], $userData['sys_shop'], 'in');
                    }

                    $first_login = ($userObj->first_login == 1) ? 1:0;
                    $checkIfFirstLogin = $this->model->checkIfFirstLogin($userObj->sys_users_id);
                    
                    if($checkIfFirstLogin > 0){
                        if($code_isset == 0){
                            $checkIPNotExist = $this->model->checkIPNotExist($userObj->sys_users_id);
                            $code_isset   = ($checkIPNotExist == 0) ? 1 : 0;
                            $login_code   = getRandomString(6, 6);
                            ($code_isset == 1) ? $this->model->setLoginCode($userObj->sys_users_id, $login_code):null;
                        }
                    }

                    $token_session = uniqid();
                    $token = en_dec('en', $token_session);
                    $token_arr = array( // store token in array
                        'token_session' => $token_session,
                    );

                    // set session
                    $this->session->set_userdata($userData);
                    $this->session->set_userdata($token_arr);

                    $access_nav = $this->session->userdata('access_nav');
                    if(in_array(1, explode(', ', $access_nav))) {
                        $is_dashboard = 1;
                    }else{
                        $is_dashboard = 0;
                    }

                    $this->audittrail->logActivity('Login', $username.' successfully logged in.', 'login', $username);
                    $data = array(
                        'success' => 1,
                        'message' => 'Login successfully',
                        'token_session' => $token,
                        'is_dashboard' => $is_dashboard,
                        'first_login' => $first_login,
                        'code_isset' => $code_isset,
                        'username' => $username,
                        'md5_sys_users_id' => md5($userObj->sys_users_id)
                    );

                    ($first_login == 1) ? $this->session->sess_destroy():'';
                    ($code_isset == 1) ? $this->session->sess_destroy():'';
                    ($code_isset == 1) ? null:$this->resetLoginAttempts($userObj->sys_users_id);
                } else { // password is invalid
                    $this->audittrail->logActivity('Login', $username.' attempts and failed to login. Password is incorrect.', 'login', 'none');
                    $data = array(
                        'success' => 0,
                        'message' => 'The password you\'ve entered is not correct. Please try again.',
                    );
                }
            } else if($verify_status == 0){
                $this->audittrail->logActivity('Login', $username.' attempts and failed to login. Shop is not active.', 'login', 'none');
                $data = array(
                    'success' => 0,
                    'message' => 'The shop of username you\'ve entered is not active. <br>For assistance, <br>contact our support.',
                );
            } else if($branch_status == 0){
                $this->audittrail->logActivity('Login', $username.' attempts and failed to login. Shop Branch is not active.', 'login', 'none');
                $data = array(
                    'success' => 0,
                    'message' => 'The shop branch of username you\'ve entered is not active. <br>For assistance, <br>contact our support.',
                );
            }
             else {
                $this->audittrail->logActivity('Login', $username.' attempts and failed to login. Username is not active.', 'login', 'none');
                $data = array(
                    'success' => 0,
                    'message' => 'The username you\'ve entered is not active. <br>For assistance, <br>contact our support.',
                );
            }

        } else {
            $this->audittrail->logActivity('Login', $username.' attempts and failed to login. Doesn\'t match any account.', 'login', 'none');
            $data = array(
                'success' => 0,
                'message' => 'The username you\'ve entered doesn\'t match any account.',
            );
        }
        generate_json($data);
    }

    public function getClientIP(){      
        $return = '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
               $return = $_SERVER["HTTP_X_FORWARDED_FOR"];  
        }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
               $return = $_SERVER["REMOTE_ADDR"]; 
        }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
               $return = $_SERVER["HTTP_CLIENT_IP"]; 
        } 

        $return = ($return != '') ? $return : '';
        $split  = explode(",",$return);
        $return = (!empty($split[1])) ? $split[0] : $return;

        return $return;
   }

    public function addLoginAttempts(){
        $ip_address   = $this->getClientIP();
        $user_agent   = $_SERVER['HTTP_USER_AGENT'];
        $date_created = date('Y-m-d H:i:s');
        $username = sanitize($this->input->post('username'));
        $get_sys_user_id = $this->model->get_sys_user_id($username);

        $attempt = $this->model->addLoginAttempts($get_sys_user_id, $ip_address, $date_created);

        $data = array(
            'attempt' => $attempt
        );

        if($attempt == 5 && $get_sys_user_id != 0){
            $this->sendEmailLoginAttempts($username, $ip_address, $date_created, $user_agent);
        }

        generate_json($data);
    }
    
    public function sendEmailLoginAttempts($username, $ip_address, $date_created, $user_agent){

            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($username);
            $this->email->subject(get_company_name()." | Account Login Attempt");
            $data['email']         = $username;
            $data['ip_address']    = $ip_address;
            $data['date_created']  = $date_created;
            $data['user_agent']    = $user_agent;
            $this->email->message($this->load->view("includes/emails/loginattempts_template", $data, TRUE));
            $this->email->send();
    }

    public function sendCodeEmailLoginAttempts($username = ''){
        $ip_address   = $this->getClientIP();
        $user_agent   = $_SERVER['HTTP_USER_AGENT'];
        $date_created = date('Y-m-d H:i:s');
        $username     = ($username == '') ? sanitize($this->input->post('username')) : $username;
        $get_sys_user = $this->model->get_sys_user($username)->row();

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($username);
        $this->email->subject(get_company_name()." | Account Login Attempt");
        $data['email']         = $username;
        $data['ip_address']    = $ip_address;
        $data['date_created']  = $date_created;
        $data['user_agent']    = $user_agent;
        $login_code   = getRandomString(6, 6);
        $this->model->setLoginCode($get_sys_user->id, $login_code);
        $data['login_code']    = $login_code;
        $this->email->message($this->load->view("includes/emails/codeloginattempts_template", $data, TRUE));
        $this->email->send();
        
    }

    public function resendCodeEmailLoginAttempts(){
        $username = sanitize($this->input->post('username'));
        // $login_code   = getRandomString(6, 6);
        $validate_username = $this->model->validate_username($username);
        $userObj = $validate_username->row();
        // $this->model->setLoginCode($userObj->sys_users_id, $login_code);
        $this->sendCodeEmailLoginAttempts($username);

        $data = array(
            'success' => 1,
            'message' => 'Access Code successfully sent to '.$username
        );

        generate_json($data);
    }

    public function getLoginAttempts(){
        $ip_address   = $this->getClientIP();

        $attempt = $this->model->getLoginAttempts($ip_address);

        $data = array(
            'attempt' => $attempt
        );

        generate_json($data);
    }

    public function resetLoginAttempts($user_id){
        $ip_address   = $this->getClientIP();
        $date_created = date('Y-m-d H:i:s');

        $attempt = $this->model->resetLoginAttempts($user_id, $ip_address, $date_created);
       
    }

    //insert all main navigation here //
    //note: make sure name of the function is the same name of main_nav_href column in cp_main_navigation. see database first
    //$myglobalvar;
    // $main_page_nav = $this->model->get_main_page_navigation()->result();

    public function home($token = '')
    {
        $this->isLoggedIn();
       
        $user_id = $this->session->userdata('id');

        $data = array(
            'token' => $token,
            'shopid' => $this->model_shops->get_sys_shop($user_id),
            'user_id' => $user_id
            // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
        );
        $this->load->view('includes/header', $data);
        if($this->Model_tips->is_tipsOn($user_id)){
            $product = $this->Model_tips->check_product($this->session->userdata('sys_shop'));
            $shipping = $this->Model_tips->check_shipping_delivery($this->session->userdata('sys_shop'));
            $banners = $this->Model_tips->check_banner();
            $product_category = $this->Model_tips->check_product_category();
            $tips_status = $this->Model_tips->check_tips_status($user_id)->row()->tips;
            $data['product'] = $product;
            $data['shipping'] = $shipping;
            $data['banners'] = $banners;
            $data['product_category'] = $product_category;
            $data['tips_status'] = $tips_status;
            $this->load->view('tips/tips', $data);
        }else{
            $this->load->view('main_navigation/home', $data);
        }
    }

    public function get_NullDatesRecord()
    {
        echo $this->model->get_NullDatesRecord();
    }

    public function dashboard_table()
    {
        $report = sanitize($this->input->get('report'));
        $fromdate = sanitize($this->input->get('fromdate'));
        $todate = sanitize($this->input->get('todate'));
        $shopid = sanitize($this->input->get('shopid'));
        $branchid = sanitize($this->input->get('branchid'));
        $index = sanitize($this->input->get('index'));
        $run_req = sanitize($this->input->get('run_request'));
        $charts = array_flatten(json_decode($this->loginstate->get_report_access()));

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));

        // $reschart = $this->model->getDashboardTable($fromdate,$todate,$shopid);
        $reschart = []; $show_chart = 0;

        // $restopitems = $this->model->total_topitems($fromdate,$todate,$shopid);

        // $resoverallsales = $this->model->total_overallsales($shopid);

        switch ($report) {
            case 'tsr':
                if (isset($this->loginstate->get_access()['tsr']) && in_array('tsr', $charts)) {
                    if ($this->loginstate->get_access()['tsr']['view'] == 1) {
                        $reschart['totalsales'] = ($run_req !== 'false') ? $this->get_Totalsales($fromdate, $todate, $shopid, $branchid):['total' => 0];
                        $show_chart = ($reschart['totalsales']['total'] > 0) ? 1:0;
                    }
                }
                break;
            case 'oscrr':
                if (isset($this->loginstate->get_access()['oscrr']) && in_array('oscrr', $charts)) {
                    if ($this->loginstate->get_access()['oscrr']['view'] == 1) {
                        $reschart['oscrr'] = ($run_req !== 'false') ? $this->get_OnlineConversionRate($fromdate, $todate, $shopid):['result' => 0];
                        $show_chart = ($reschart['oscrr']['result'] > 0) ? 1:0;
                    }
                }
                break;
            case 'rbsr':
                if (isset($this->loginstate->get_access()['rbsr']) && in_array('rbsr', $charts)) {
                    if ($this->loginstate->get_access()['rbsr']['view'] == 1) {
                        $reschart['rBS'] = ($run_req !== 'false') ? $this->get_RevenueByStore($fromdate, $todate, $shopid,$branchid):['data1' => [], 'data2' => []];   
                        $show_chart = (count($reschart['rBS']['data1']) > 0 || count($reschart['rBS']['data2']) > 0) ? 1:0;
                    }
                }
                break;
            case 'rbbr':
                if (isset($this->loginstate->get_access()['rbbr']) && in_array('rbbr', $charts)) {
                    if ($this->loginstate->get_access()['rbbr']['view'] == 1) {
                        $reschart['rBB'] = ($run_req !== 'false') ? $this->get_RevenueByBranch($fromdate, $todate, $shopid,$branchid):[
                            'data1' => [],
                            'data2' => []
                        ];   
                        $show_chart = (count($reschart['rBB']['data1']) > 0 || count($reschart['rBB']['data2']) > 0) ? 1:0;
                    }
                }
                break;
            case 'rbl':
                if (isset($this->loginstate->get_access()['rbl']) && in_array('rbl', $charts)) {
                    if ($this->loginstate->get_access()['rbl']['view'] == 1) {
                        $reschart['rBL'] = ($run_req !== 'false') ? $this->get_RevenueByLocation($fromdate, $todate, $shopid,$branchid):['chartdata' => ['labels' => []]];
                        $show_chart = (count($reschart['rBL']['chartdata']['labels']) > 0) ? 1:0;
                    }
                }
                break;
            case 'tacr':
                if (isset($this->loginstate->get_access()['tacr']) && in_array('tacr', $charts)) {
                    if ($this->loginstate->get_access()['tacr']['view'] == 1) {
                        $reschart['tacr'] = ($run_req !== 'false') ? $this->get_AbandonedCarts($fromdate, $todate, $shopid,$branchid):['totalData' => 0];   
                        $show_chart = ($reschart['tacr']['totalData'] > 0) ? 1:0;
                    }
                }
                break;
            case 'aov':
                if (isset($this->loginstate->get_access()['aov']) && in_array('aov', $charts)) {
                    if ($this->loginstate->get_access()['aov']['view'] == 1) {
                        $reschart['aov'] = ($run_req !== 'false') ? $this->getAverageOrderValue($fromdate,$todate,$shopid,$branchid):['cur_ave' => 0];
                        $show_chart = $reschart['aov']['cur_ave'] > 0 ? 1:0;
                    }
                }
                break;
            case 'ps':
                if ($this->loginstate->get_access()['ps'] && in_array('ps', $charts)) {
                    if ($this->loginstate->get_access()['ps']['view'] == 1) {
                        $reschart['ps'] = ($run_req !== 'false') ? $this->getPageStatistics($fromdate,$todate):['cur_total' => 0];
                        $show_chart = $reschart['ps']['cur_total'] > 0 ? 1:0;
                    }
                }
                break;
            case 'os':
                if (isset($this->loginstate->get_access()['os']) && in_array('os', $charts)) {
                    if ($this->loginstate->get_access()['os']['view'] == 1) {
                        $reschart['os'] = ($run_req !== 'false') ? $this->getOrderAndSales($fromdate,$todate,$shopid,$branchid):['cur_total_to' => 0];
                        $show_chart = $reschart['os']['cur_total_to'] > 0 ? 1:0;
                    }
                }
                break;
            case 'to':
                if (isset($this->loginstate->get_access()['to']) && in_array('to', $charts)) {
                    if ($this->loginstate->get_access()['to']['view'] == 1) {
                        $reschart['to'] = ($run_req !== 'false') ? $this->getTotalOrders($fromdate,$todate,$shopid,$branchid):['cur_total' => 0];
                        $show_chart = $reschart['to']['cur_total'] > 0 ? 1:0;
                    }
                }
                break;
            case 'tps':
                if (isset($this->loginstate->get_access()['tps']) && in_array('tps', $charts)) {
                    if ($this->loginstate->get_access()['tps']['view'] == 1) {
                        $reschart['tps'] = ($run_req !== 'false') ? $this->getTopProductsSold($fromdate,$todate,$shopid,$branchid):['cur_result' => []];
                        $show_chart = count($reschart['tps']['cur_result']) > 0 ? 1:0;
                    }
                }
                break;
            case 'po':
                if (isset($this->loginstate->get_access()['po']) && in_array('po', $charts)) {
                    if ($this->loginstate->get_access()['po']['view'] == 1) {
                        $reschart['po'] = ($run_req !== 'false') ? $this->getPendingOrders($fromdate,$todate,$shopid,$branchid):['total' => 0];
                        $show_chart = $reschart['po']['total'] > 0 ? 1:0;
                    }
                }
                break;
            case 'invlist':
                if (isset($this->loginstate->get_access()['invlist']) && in_array('invlist', $charts)) {
                    if ($this->loginstate->get_access()['invlist']['view'] == 1) {
                        $reschart['invlist'] = ($run_req !== 'false') ? $this->getInventoryList($fromdate,$todate,$shopid,$branchid):['total' => 0];
                        $show_chart = $reschart['invlist']['total'] > 0 ? 1:0;
                    }
                }
                break;
            case 'oblr':
                if (isset($this->loginstate->get_access()['oblr']) && in_array('oblr', $charts)) {
                    if ($this->loginstate->get_access()['oblr']['view'] == 1) {
                        $reschart['oblr'] = ($run_req !== 'false') ? $this->get_OblrChart($fromdate,$todate,$shopid,$branchid,'city'):['chartdata' => ['total' => 0]];
                        $show_chart = $reschart['oblr']['chartdata']['total'] > 0 ? 1:0;
                    }
                }
                break;
            default:
                # code...
                break;
        }

        $response = array(
            'success' => true,
            'chartdata' => $reschart,
            'show_chart' => $show_chart,
            'next_chart' => (!isset($charts[$index+1])) ? 'end':$charts[$index+1],
            'next_index' => $index+1,
            'charts' => $charts,
        );

        if ($shopid == 0) {
            $shop = 'All Shops';
        } else {
            $shop_info = $this->Model_shops->get_shop_details($shopid);
            $shop = ($shop_info->num_rows() == 0) ? 'All Shops':$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];
        }
        $this->audittrail->logActivity('Dashboard', "Dashboard searched records in $shop, dated $fromdate to $todate.", 'search', $this->session->userdata('username'));

        echo json_encode($response);
    }

    private function getAverageOrderValue($fromdate,$todate,$shop_id,$branch_id){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['aov']['view'] == 1){    
            $this->load->model('reports/Model_average_order_value');            
           
        $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
        $p_fromdate = $pre_date_range['fromdate'];
        $p_todate = $pre_date_range['todate'];
    
        $p_fromdate = $p_fromdate->format('Y-m-d');
        $p_todate = $p_todate->format('Y-m-d');
    
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
    
        if($fromdate == $todate){            
            
            $cur_result = $this->Model_average_order_value->get_order_values($fromdate,$todate,$shop_id,$branch_id);
            $pre_result = $this->Model_average_order_value->get_order_values($p_fromdate,$p_todate,$shop_id,$branch_id);     
    
            $cur_range = array_column($cur_result,'date_ordered');
            $pre_range = array_column($pre_result,'date_ordered');
            
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

            //post process dates and total_amount
            $new_cur_result = [];
            foreach($cur_dates as $date){                    
                $cur_total_amount = 0;                    
                $new_cur_date = "";
                foreach($cur_result as $result){
                    if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                        $cur_total_amount += floatval($result['total_amount']);
                        $new_cur_date = $result['date_ordered'];
                    }
                }
                $new_cur_result[] = array(
                    'date_ordered' => $new_cur_date,
                    'total_amount' => $cur_total_amount
                );                    
            }           
            $new_pre_result = [];
            foreach($pre_dates as $date){                                        
                $pre_total_amount = 0;                    
                $new_pre_date = "";                   
                foreach($pre_result as $result){
                    if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                        $pre_total_amount += floatval($result['total_amount']);
                        $new_pre_date = $result['date_ordered'];
                    }
                }
                $new_pre_result[] = array(
                    'date_ordered' => $new_pre_date,
                    'total_amount' => $pre_total_amount
                );
            }
    
            $cur_data = generateArrayIn($cur_result,$cur_dates,'date_ordered','total_amount');
            $pre_data = generateArrayIn($pre_result,$pre_dates,'date_ordered','total_amount');
    
            $cur_total = getTotalInArray($cur_data,'total_amount');
            $pre_total = getTotalInArray($pre_data,'total_amount');
            
            $cur_tb_head = date('m/d/Y',strtotime($todate));
            $pre_tb_head = date('m/d/Y',strtotime($p_todate));
    
            $cur_period = date('M d, Y',strtotime($todate));
            $pre_period = date('M d, Y',strtotime($p_todate));

            if(date('Y-m-d',strtotime($todate)) == date('Y-m-d')){
                $cur_period = 'Today';
                $pre_period = 'Yesterday';
                $cur_tb_head = 'Today';
                $pre_tb_head = 'Yesterday';
            }
        }
        else{
    
            $dates = generateDates($fromdate,$todate,'M d');
    
            $cur_result = $this->Model_average_order_value->get_order_values($fromdate,$todate,$shop_id,$branch_id);
            $pre_result = $this->Model_average_order_value->get_order_values($p_fromdate,$p_todate,$shop_id,$branch_id);

            $cur_dates = generateDates($fromdate,$todate);
            $pre_dates = generateDates($p_fromdate,$p_todate);

            //post process dates and total_amount
            $new_cur_result = [];                
            foreach($cur_dates as $date){                    
                $cur_total_amount = 0;                           
                $new_cur_date = "";                    
                foreach($cur_result as $result){
                    if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                        $cur_total_amount += floatval($result['total_amount']);
                        $new_cur_date = $result['date_ordered'];
                    }
                }
                $new_cur_result[] = array(
                    'date_ordered' => $new_cur_date,
                    'total_amount' => $cur_total_amount
                );                    
            }
            $new_pre_result = [];
            foreach($pre_dates as $date){                    
                $pre_total_amount = 0;  
                $new_pre_date = "";                   
                foreach($pre_result as $result){
                    if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                        $pre_total_amount += floatval($result['total_amount']);
                        $new_pre_date = $result['date_ordered'];
                    }
                }
                $new_pre_result[] = array(
                    'date_ordered' => $new_pre_date,
                    'total_amount' => $pre_total_amount
                );
            }

            $cur_data = generateArrayIn($cur_result,$cur_dates,'date_ordered','total_amount');
            $pre_data = generateArrayIn($pre_result,$pre_dates,'date_ordered','total_amount');
    
            $cur_total = getTotalInArray($cur_data,'total_amount');
            $pre_total = getTotalInArray($pre_data,'total_amount');
    
            $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
            $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));
    
            $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
            $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
        }
    
        $size = sizeof($dates);
    
        $cur_ave = floatval($cur_total)/$size;
        $pre_ave = floatval($pre_total)/$size;     
    
        $increased = false;
        $percentage = 0.0;
        $diff = 0.0;

        if($cur_ave >= $pre_ave){
            $diff = $cur_ave-$pre_ave;
            $increased = true;
        }
        else{
            $diff = $pre_ave-$cur_ave;        
        }
    
        if($pre_ave > 0){
            $percentage = ($diff/$pre_ave)*100;
        }
        else{
            $percentage = 100;
        }         
        $step = get_StepSize(array_column($cur_data, 'total_amount'), array_column($pre_data, 'total_amount'), 4);
    
          return $reschart = array(
            'dates' => $dates,
            'shop_id' => $shop_id,
            'branch_id' => $branch_id,
            'cur_dates' => $cur_dates,
            'step' => $step,
            'pre_dates' => $pre_dates,
            'current_data' => $cur_data,
            'previous_data' => $pre_data,
            'cur_result' => $cur_result,
            'pre_result' => $pre_result,
            'cur_ave' => number_format($cur_ave,2),
            'pre_ave' => number_format($pre_ave,2),
            'diff' => number_format($diff,2),
            'cur_period' => $cur_period,
            'pre_period' => $pre_period,
            'percentage' => array('percentage' => round($percentage,2), 'increased' => $increased)
          );          
        }else{
            return [];
        //   $this->load->view('error_404');
        }
    }

    private function getPageStatistics($fromdate,$todate){
        $this->isLoggedIn();
        $this->load->model('reports/Model_online_store_sessions','model_online_store_sessions');
        if($this->loginstate->get_access()['ps']['view'] == 1){    

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
    
            $cur_period = date('M d, Y',strtotime($todate));
            $pre_period = date('M d, Y',strtotime($p_todate));
            if ($cur_period == date('M d, Y')) {
                $cur_period = "Today"; $pre_period = "Yesterday";
            }
        }
        else{ 
            $dates = generateDates($fromdate,$todate,'M d');       
            $cur_result = $this->model_online_store_sessions->get_visitors($fromdate,$todate);
            $pre_result = $this->model_online_store_sessions->get_visitors($p_fromdate,$p_todate);
            $cur_dates = generateDates($fromdate,$todate);
            $pre_dates = generateDates($p_fromdate,$p_todate);
            $cur_data = generateArrayIn($cur_result,$cur_dates,'trandate','visitors');
            $pre_data = generateArrayIn($pre_result,$pre_dates,'trandate','visitors');            
    
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

        return $reschart = array(
            'dates' => $dates,
            'current_data' => $cur_data,
            'previous_data' => $pre_data,
            'cur_total' => number_format(intval($cur_total)),
            'pre_total' => intval($pre_total),
            'cur_result' => $cur_result,
            'pre_result' => $pre_result,                
            'cur_period' => $cur_period,
            'pre_period' => $pre_period,
            'cur_dates' => $cur_dates,
            'pre_dates' => $pre_dates,
            'percentage' => array('percentage' => round($percentage,2), 'increased' => $increased),
            'step' => get_StepSize(array_column($cur_data, 'visitors'), array_column($pre_data, 'visitors'), 4),
        );            
            }else{
            return [];
            }
        }

    private function getOrderAndSales($fromdate,$todate,$shop_id,$branch_id){
        $this->isLoggedIn();
        $this->load->model('reports/Model_order_and_sales');
        if($this->loginstate->get_access()['os']['view'] == 1){

            $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
            $p_fromdate = $pre_date_range['fromdate'];
            $p_todate = $pre_date_range['todate'];

            $p_fromdate = $p_fromdate->format('Y-m-d');
            $p_todate = $p_todate->format('Y-m-d');

            if($fromdate == $todate){                
                
                $cur_result = $this->Model_order_and_sales->get_order_and_sales($fromdate,$todate,$shop_id,$branch_id);
                $pre_result = $this->Model_order_and_sales->get_order_and_sales($p_fromdate,$p_todate,$shop_id,$branch_id);  
        
                $cur_range = array_column($cur_result,'date_ordered');
                $pre_range = array_column($pre_result,'date_ordered');
                
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
                
                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;                     
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                        }
                    }
                    $new_cur_result[] = array(
                        'total_orders' => $cur_total_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                        }
                    }
                    $new_pre_result[] = array(
                        'total_orders' => $pre_total_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }
        
                $cur_data = generateArrayIn($cur_result,$cur_dates,'date_ordered','total_orders');
                $pre_data = generateArrayIn($pre_result,$pre_dates,'date_ordered','total_orders');
        
                $cur_total_to = getTotalInArray($cur_result,'total_orders');
                $pre_total_to = getTotalInArray($pre_result,'total_orders');
        
                $cur_total_ts = getTotalInArray($cur_result,'total_amount');
                $pre_total_ts = getTotalInArray($pre_result,'total_amount');        
                
                $cur_tb_head = date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d/Y',strtotime($p_todate));
          
                $cur_period = date('M d, Y',strtotime($todate));
                $pre_period = date('M d, Y',strtotime($p_todate));
                if ($cur_period == date('M d, Y')) {
                    $cur_period = "Today"; $pre_period = "Yesterday";
                }
              }
              else{ 
        
                $dates = generateDates($fromdate,$todate,'M d');

                $cur_result = $this->Model_order_and_sales->get_order_and_sales($fromdate,$todate,$shop_id,$branch_id,'');
                $pre_result = $this->Model_order_and_sales->get_order_and_sales($p_fromdate,$p_todate,$shop_id,$branch_id,'');                

                $cur_dates = generateDates($fromdate,$todate);
                $pre_dates = generateDates($p_fromdate,$p_todate);

                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;                     
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                        }
                    }
                    $new_cur_result[] = array(
                        'total_orders' => $cur_total_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                        }
                    }
                    $new_pre_result[] = array(
                        'total_orders' => $pre_total_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_orders');

                $cur_total_to = getTotalInArray($cur_result,'total_orders');
                $pre_total_to = getTotalInArray($pre_result,'total_orders');

                $cur_total_ts = getTotalInArray($cur_result,'total_amount');
                $pre_total_ts = getTotalInArray($pre_result,'total_amount');

                $size = sizeof($dates);

                $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));

                $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
                $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
        
              
              }      
        
              $cur_total_ts = floatval($cur_total_ts);
              $pre_total_ts = floatval($pre_total_ts);
        
              $increased_to = false;
              $percentage_to = 0.0;
              
              if($cur_total_to >= $pre_total_to){        
                $increased_to = true;
              }
              $percentage_to = getPercentage($cur_total_to,$pre_total_to);
              
              $increased_ts = false;
              $percentage_ts = 0.0;      
        
              if($cur_total_ts >= $pre_total_ts){
                $increased_ts = true;
              }
              $percentage_ts = getPercentage($cur_total_ts,$pre_total_ts);       

        $reschart = array(
            'dates' => $dates,
            'cur_dates' => $cur_dates,
            'pre_dates' => $pre_dates,
            'current_data' => $cur_data,
            'previous_data' => $pre_data,
            'cur_result' => $cur_result,
            'pre_result' => $pre_result,
            'cur_total_to' => intval($cur_total_to),
            'pre_total_to' => intval($pre_total_to),
            'cur_total_ts' => number_format($cur_total_ts,2),
            'pre_total_ts' => number_format($pre_total_ts,2),            
            'cur_period' => $cur_period,
            'pre_period' => $pre_period,
            'cur_tb_head' => $cur_tb_head,
            'pre_tb_head' => $pre_tb_head,
            'percentage_to' => array('percentage_to' => round($percentage_to,2), 'increased_to' => $increased_to),
            'percentage_ts' => array('percentage_ts' => round($percentage_ts,2), 'increased_ts' => $increased_ts)
        );
        
        return $reschart;
        }else{
        // $this->load->view('error_404');
        return [];
        }        
    }

    private function getTotalOrders($fromdate,$todate,$shop_id,$branch_id){

        $this->isLoggedIn();
        $this->load->model('reports/Model_total_orders');
        if($this->loginstate->get_access()['to']['view'] == 1){

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
            $p_fromdate = $pre_date_range['fromdate'];
            $p_todate = $pre_date_range['todate'];
      
            $p_fromdate = $p_fromdate->format('Y-m-d');
            $p_todate = $p_todate->format('Y-m-d');
      
            if($fromdate == $todate){
                
                $cur_result = $this->Model_total_orders->get_total_orders_data($fromdate,$todate,$shop_id,$branch_id);
                $pre_result = $this->Model_total_orders->get_total_orders_data($p_fromdate,$p_todate,$shop_id,$branch_id);   

                $cur_range = array_column($cur_result,'date_ordered');
                $pre_range = array_column($pre_result,'date_ordered');
                
                if($cur_range && $pre_range){
                    $time_range = getTimeRange($cur_range,$pre_range);
                }else if($cur_range && !$pre_range){
                    $time_range = getTimeRange($cur_range,$cur_range);
                }else{
                    $time_range = []; $time_range['start'] = '00:00'; $time_range['end'] = '23:00';
                }
                
                $dates = createTimeRangeArray($time_range['start'],$time_range['end']);

                $cur_dates = createTimeRangeArray($time_range['start'],$time_range['end'],'1 hour',date('Y-m-d',strtotime($fromdate)));
                $pre_dates = createTimeRangeArray($time_range['start'],$time_range['end'],'1 hour',date('Y-m-d',strtotime($p_fromdate)));     
                
                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;  
                    $cur_total_f_count = 0;
                    $cur_total_s_count = 0;                   
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            }                            
                        }
                    }
                    $new_cur_result[] = array(
                        'total_paid_orders' => $cur_total_count,
                        'total_fulfilled_orders' => $cur_total_f_count,
                        'total_delivered_orders' => $cur_total_s_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $pre_total_f_count = 0;
                    $pre_total_s_count = 0;
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            } 
                        }
                    }
                    $new_pre_result[] = array(
                        'total_paid_orders' => $pre_total_count,
                        'total_fulfilled_orders' => $pre_total_f_count,
                        'total_delivered_orders' => $pre_total_s_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_paid_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_paid_orders');
        
                $cur_data_f = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_fulfilled_orders');
                $cur_data_d = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_delivered_orders');
        
                $pre_data_f = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_fulfilled_orders');
                $pre_data_d = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_delivered_orders');

                $cur_total = getTotalInArray($new_cur_result,'total_paid_orders');
                $cur_total_f = getTotalInArray($new_cur_result,'total_fulfilled_orders');
                $cur_total_d = getTotalInArray($new_cur_result,'total_delivered_orders');
        
                $pre_total = getTotalInArray($new_pre_result,'total_paid_orders');
                $pre_total_f = getTotalInArray($new_pre_result,'total_fulfilled_orders');
                $pre_total_d = getTotalInArray($new_pre_result,'total_delivered_orders');
                
                $cur_tb_head = date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d/Y',strtotime($p_todate));
        
                $cur_period = date('M d, Y',strtotime($todate));
                $pre_period = date('M d, Y',strtotime($p_todate));

                if(date('Y-m-d',strtotime($todate)) == date('Y-m-d')){
                    $cur_period = 'Today';
                    $pre_period = 'Yesterday';
                    $cur_tb_head = 'Today';
                    $pre_tb_head = 'Yesterday';
                }

            }else{ 

                $dates = generateDates($fromdate,$todate,'M d');

                $cur_result = $this->Model_total_orders->get_total_orders_data($fromdate,$todate,$shop_id,$branch_id);
                $pre_result = $this->Model_total_orders->get_total_orders_data($p_fromdate,$p_todate,$shop_id,$branch_id);      
        
                $cur_dates = generateDates($fromdate,$todate);
                $pre_dates = generateDates($p_fromdate,$p_todate);

                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;  
                    $cur_total_f_count = 0;
                    $cur_total_s_count = 0;                   
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            }                            
                        }
                    }
                    $new_cur_result[] = array(
                        'total_paid_orders' => $cur_total_count,
                        'total_fulfilled_orders' => $cur_total_f_count,
                        'total_delivered_orders' => $cur_total_s_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $pre_total_f_count = 0;
                    $pre_total_s_count = 0;
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            } 
                        }
                    }
                    $new_pre_result[] = array(
                        'total_paid_orders' => $pre_total_count,
                        'total_fulfilled_orders' => $pre_total_f_count,
                        'total_delivered_orders' => $pre_total_s_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_paid_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_paid_orders');
        
                $cur_data_f = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_fulfilled_orders');
                $cur_data_d = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_delivered_orders');
        
                $pre_data_f = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_fulfilled_orders');
                $pre_data_d = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_delivered_orders');
        
                $cur_total = getTotalInArray($new_cur_result,'total_paid_orders');
                $cur_total_f = getTotalInArray($new_cur_result,'total_fulfilled_orders');
                $cur_total_d = getTotalInArray($new_cur_result,'total_delivered_orders');
        
                $pre_total = getTotalInArray($new_pre_result,'total_paid_orders');
                $pre_total_f = getTotalInArray($new_pre_result,'total_fulfilled_orders');
                $pre_total_d = getTotalInArray($new_pre_result,'total_delivered_orders');

                $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));

                $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
                $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
            }     
      
            $size = sizeof($cur_data);      
      
            $increased = false;
            $percentage = 0.0;      
      
            if($cur_total >= $pre_total){        
              $increased = true;        
            }
            $percentage = getPercentage($cur_total,$pre_total);    
      
            $increased_f = false;
            $percentage_f = 0.0;      
      
            if($cur_total_f >= $pre_total_f){        
              $increased_f = true;        
            }
            $percentage_f = getPercentage($cur_total_f,$pre_total_f);    
      
            $increased_d = false;
            $percentage_d = 0.0;      
      
            if($cur_total_d >= $pre_total_d){        
              $increased_d = true;
            }
            $percentage_d = getPercentage($cur_total_d,$pre_total_d);      

        $reschart = array(
            'dates' => $dates,
            'current_data' => $cur_data,
            'previous_data' => $pre_data,
            'cur_result' => $cur_result,
            'pre_result' => $pre_result,
            'cur_total' => intval($cur_total),
            'cur_total_f' => intval($cur_total_f),
            'cur_total_d' => intval($cur_total_d),            
            'pre_total' => $pre_total,
            'pre_total_f' => $pre_total_f,
            'pre_total_d' => $pre_total_d,            
            'cur_period' => $cur_period,
            'pre_period' => $pre_period,
            'cur_tb_head' => $cur_tb_head,
            'pre_tb_head' => $pre_tb_head,
            'percentage' => array('percentage' => round($percentage,2), 'increased' => $increased),
            'percentage_f' => array('percentage_f' => round($percentage_f,2), 'increased_f' => $increased_f),
            'percentage_d' => array('percentage_d' => round($percentage_d,2), 'increased_d' => $increased_d),
        );

       return $reschart;

       }else{
            // $this->load->view('error_404');
            return [];
       }
    }

    private function getTopProductsSold($fromdate,$todate,$shop_id,$branch_id){
        $this->isLoggedIn();
        $this->load->model('reports/Model_top_products_sold');
        if($this->loginstate->get_access()['tps']['view'] == 1){

        $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
        $p_fromdate = $pre_date_range['fromdate'];
        $p_todate = $pre_date_range['todate'];

        $p_fromdate = $p_fromdate->format('Y-m-d');
        $p_todate = $p_todate->format('Y-m-d');

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));

        $dates = generateDates($fromdate,$todate,'M d');
        
        $cur_result = $this->Model_top_products_sold->get_top_products_sold($fromdate,$todate,$shop_id,$branch_id,null,5);      
        $pre_result = $this->Model_top_products_sold->get_top_products_sold_in($p_fromdate,$p_todate,$shop_id,$branch_id,$cur_result);      
        
        $cur_dates = generateDates($fromdate,$todate);
        $pre_dates = generateDates($p_fromdate,$p_todate);
        
        $cur_data = $cur_result;
        $pre_data = [];
        if($cur_result != null){
            $pre_data = sortArrayLike($cur_result,$pre_result,'id');
        }      

        $size = sizeof($dates);

        if($fromdate == $todate){
            $cur_period = date('M d, Y',strtotime($todate));
            $pre_period = date('M d, Y',strtotime($p_todate));
            if ($cur_period == date('M d, Y')) {
                $cur_period = "Today"; $pre_period = "Yesterday";
            }
        }
        else{
            $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
            $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
        }

        return $reschart = array(
            'dates' => $dates,
            'cur_dates' => $cur_dates,
            'pre_dates' => $pre_dates,
            'cur_data' => $cur_data,
            'pre_data' => $pre_data,
            'cur_result' => $cur_result,
            'pre_result' => $pre_result,
            'cur_period' => $cur_period,
            'pre_period' => $pre_period
        );
       
        }else{
        // $this->load->view('error_404');
        return [];
        }
    }

    private function get_Totalsales($fromdate, $todate, $shopid, $branchid){
        $this->load->model('reports/Model_sales_report');
        // $fromdate = date("Y-m-d", strtotime($fromdate));
        // $todate = date("Y-m-d", strtotime($todate));
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate));
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval->format('%y Year %m Month %d Day'))), "Y-m-d");
        $is_date_equal = ($fromdate == $todate) ? true:false;
        $is_range_tiny = $todate == date('Y-m-d') ? ($interval->format('%m') >= '1' ? false:($interval->format('%d') <= '14' ? true:false)):false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = ['Today','Yesterday'];
        } elseif ($is_date_equal) {
            $legend = [
                date_format(date_create($fromdate), 'M d, Y'),date_format(date_create($new_date), 'M d, Y')
            ];
        } else {
            $legend = [
                date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y'),
                date_format(date_create($new_date), 'M d')." to ".date_format(date_create($prev_to), 'M d, Y'),
            ];
        }

        $totalsales1 = $this->Model_sales_report->totalsales_view($fromdate,$todate, $shopid, $branchid, true, $is_range_tiny);
        $totalsales2 = $this->Model_sales_report->totalsales_view($new_date,$prev_to, $shopid, $branchid, true, $is_range_tiny);

        // print_r($totalsales1);
        // print_r($totalsales2);
        $ts_amount1 = (count($totalsales1) > 0) ? array_sum(array_pluck($totalsales1, 'total_amount')):0;
        $ts_amount2 = (count($totalsales2) > 0) ? array_sum(array_pluck($totalsales2, 'total_amount')):0;
        // $total = $ts_amount1 + $ts_amount2;

        if ($ts_amount1 == 0) return ['total' => 0, 'legend' => $legend, 'dates' => []];
        // get op/mp arr vals
        $op_data = $this->Model_sales_report->onlinePurchases($new_date, $prev_to, $todate, $shopid, $branchid);
        $mp_data = $this->Model_sales_report->manualPurchases($new_date, $prev_to, $todate, $shopid, $branchid);
        // set to make comparison of data
        $op = $op_data['curr']['total'];
        $mp = $mp_data['curr']['total'];
        $op = ($op == 0 || $op == null || $op == '') ? 0:$op;
        $mp = ($mp == 0 || $mp == null || $mp == '') ? 0:$mp;
        
        $op2 = $op_data['prev']['total'];
        $mp2 = $mp_data['prev']['total'];
        $op2 = ($op2 == 0 || $op2 == null || $op2 == '') ? 0:$op2;
        $mp2 = ($mp2 == 0 || $mp2 == null || $mp2 == '') ? 0:$mp2;

        if ($op !== 0 && $op2 !== 0) {
            if ($ts_amount1 == 0 || $ts_amount2 == 0) {
                $op_final_percent = 100;
            }else{
                $op_final_percent = abs(round((($op/$ts_amount1)-1)*100 - (($op2/$ts_amount2)-1) * 100, 2));
            }
        } elseif ($op == 0 && $op2 == 0) {
            $op_final_percent = 0;
        } else {
            $op_final_percent = 100; 
        }
        
        if ($mp !== 0 && $mp2 !== 0) {
            $mp_final_percent = abs(round((($mp/$ts_amount1)-1)*100 - (($mp2/$ts_amount2)-1) * 100, 2));
        } elseif ($mp == 0 && $mp2 == 0) {
            $mp_final_percent = 0;
        } else {
            $mp_final_percent = 100;
        }
        // echo "$_op2 - $_mp2 <br/> $ts_percent - $ts_percent2";
        if ($ts_amount1 !== 0 && $ts_amount2 !== 0) {
            $ts_final_percent = abs(round((($ts_amount1/$ts_amount2) - 1) * 100, 2));
        } elseif ($ts_amount1 == 0 && $ts_amount2 == 0) {
            $ts_final_percent = 0;
        } else {
            $ts_final_percent = 100;
        }
        
        // set a date period for array
        $dates = [];
        $begin = new DateTime( $fromdate );
        $end = new DateTime( $todate );
        $end = $end->modify( '+1 day' );

        $interval = ($fromdate == $todate) ? new DateInterval('PT1H'):new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);
        $date_format = ($fromdate == $todate) ? "H:00":"M d";

        foreach($daterange as $date){
            $dates[] = $date->format($date_format);
        }

        // set dates as array key
        $ts = array_fill_keys(array_keys(array_flip($dates)),array());
        data_set($ts, '*', ['data1' => ['total_amount' => 0],'data2' => ['total_amount' => 0]]);
        // push current data as data1
        foreach ($totalsales1 as $key => $sale) {
            $mmdd = date_format(date_create($sale['payment_date_time']), "$date_format");
            $ts[$mmdd]['data1'] = $sale;
        }

        if ($fromdate == $todate) {
            foreach ($totalsales2 as $key => $sale) {
                $mmdd = date_format(date_create($sale['payment_date_time']), "$date_format");
                $ts[$mmdd]['data2'] = $sale;
            }
            $dates = array_keys($ts);
        }else{
            $ctr = 0;
            foreach ($ts as $key => $value) {
                // push comparer data as data2
                if (isset($totalsales2[$ctr])) {
                    $ts[$key]['data2'] = $totalsales2[$ctr];
                }
                $ctr++;
            }
        }
        // remove first date
        $dates[0] = '';
        $cur_data = array_column(array_column($ts, 'data1'), 'total_amount');
        $pre_data = array_column(array_column($ts, 'data2'), 'total_amount');
        $step = get_StepSize($cur_data, $pre_data, 4);
        
        $result = [
            'ts' => [$cur_data, $pre_data],
            'dates' => $dates,
            'total' => $ts_amount1,
            'step' => $step,
            'cur_amount' => $ts_amount1,
            'pre_amount' => $ts_amount2,
            'head' => [
                'percent' => ($ts_amount1 >= $ts_amount2) ? "<i class='fa fa-arrow-up text-blue-400'></i> $ts_final_percent %":"<i class='fa fa-arrow-down text-red-400'></i> $ts_final_percent %",
                'total' => number_format($ts_amount1, 2),
                'pre_total' => number_format($ts_amount2, 2),
            ],
            'legend' => $legend,
            'op' => [
                'total' => number_format($op, 2),
                'pre_total' => number_format($op2, 2),
                'percent' => ($op >= $op2) ? "<i class='fa fa-arrow-up text-blue-400 text-lg'></i> $op_final_percent %":"<i class='fa fa-arrow-down text-red-400 text-lg'></i> $op_final_percent %",
            ],
            'mp' => [
                'total' => number_format($mp, 2),
                'pre_total' => number_format($mp2, 2),
                'percent' => ($mp >= $mp2) ? "<i class='fa fa-arrow-up text-blue-400 text-lg'></i> $mp_final_percent %":"<i class='fa fa-arrow-down text-red-400 text-lg'></i> $mp_final_percent %",
            ]
        ];

        return $result;
    }

    private function get_AbandonedCarts($fromdate, $todate, $shopid, $branchid)
    {
        $this->load->model('reports/Model_abandoned_carts');
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");
        $todate = date("Y-m-d", strtotime($todate));
        $is_date_equal = ($fromdate == $todate) ? true:false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = ['Today','Yesterday'];
        } elseif ($is_date_equal) {
            $legend = [
                date_format(date_create($fromdate), 'M d, Y'),date_format(date_create($new_date), 'M d, Y')
            ];
        } else {
            $legend = [
                date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y'),
                date_format(date_create($new_date), 'M d')." to ".date_format(date_create($prev_to), 'M d, Y'),
            ];
        }

        $abandoned1 = $this->Model_abandoned_carts->get_atc_reports_chart($fromdate,$todate,$shopid);/* current data */
        $abandoned2 = $this->Model_abandoned_carts->get_atc_reports_chart($new_date,$prev_to,$shopid);/* previous data */
        $labels = [];
        foreach (array_keys($abandoned1) as $value) {
            $labels[] = date_format(date_create($value), 'M d');
        }
        $labels[0] = '';
        // print_r($chart_data1);
        $computation = get_AbandonedCartsComputation($abandoned1, $abandoned2);
        $result = [
            'computation' => $computation,
            'labels' => $labels,
            'legend' => $legend,
            'data' => [array_column($abandoned1, 'abandoned'), array_column($abandoned2, 'abandoned')],
            'totalData' => array_sum(array_column($abandoned1, 'abandoned')),
            'chartType' => ($is_date_equal) ? 'horizontalBar':'line',
        ];

        return $result;
    }

    private function get_OnlineConversionRate($fromdate, $todate, $shopid){
        $this->load->model('reports/Model_online_conversion_rate');
        $this->load->model('reports/Model_sales_report');
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate))->format('%y Year %m Month %d Day');
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval)), "Y-m-d");

        $data = $this->Model_online_conversion_rate->get_oscr_reports_data($fromdate,$todate,$shopid);
        $data2 = $this->Model_online_conversion_rate->get_oscr_reports_data($new_date,$prev_to,$shopid);
        // print_r($data);
        $visits = array_column($this->Model_online_conversion_rate->get_visitors($new_date, $prev_to, $todate), 'visitors');
        $total_visits = isset($visits[0]) ? $visits[0]:0;
        $total_visits2 = isset($visits[1]) ? $visits[1]:0;
        
        if(count($data['data']) > 0){
            $c_data1 = $data['tfoot'];
            $c_data2 = $data2['tfoot'];

            $result = [
                'success' => true,
                'result' => 1,
                'top_data' => [
                    $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2,false),
                    $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2,true),
                ],
                'data' => [
                    'visits' => [
                        "$total_visits"
                    ],
                    'atc' => [
                        0 => number_format($c_data1[0]),
                        1 => $this->compareThenToString($c_data1[0], $c_data2[0], $total_visits, $total_visits2, false),
                        2 => $this->compareThenToString($c_data1[0], $c_data2[0], $total_visits, $total_visits2, true)
                    ],
                    'rc' => [
                        0 => number_format($c_data1[1]),
                        1 => $this->compareThenToString($c_data1[1], $c_data2[1], $total_visits, $total_visits2),
                        2 => $this->compareThenToString($c_data1[1], $c_data2[1], $total_visits, $total_visits2, true)
                    ],
                    'ptp' => [
                        0 => number_format($c_data1[2]),
                        1 => $this->compareThenToString($c_data1[2], $c_data2[2], $total_visits, $total_visits2),
                        2 => $this->compareThenToString($c_data1[2], $c_data2[2], $total_visits, $total_visits2, true)
                    ],
                    'sessions' => [
                        0 => number_format($c_data1[3]),
                        1 => $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2),
                        2 => $this->compareThenToString($c_data1[3], $c_data2[3], $total_visits, $total_visits2, true)
                    ]
                ]
            ];
            return $result;
        }else{
            $data = [
                "success" => false,
                'result' => 0,
                'top_data' => [0,"0 %"],
                "data" => [
                    'atc' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                    'rc' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                    'ptp' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                    'sessions' => ["0 sessions","<span>0 %</span>","<span>0 %</span>"],
                ]
            ];
            return $data;
        }
    }

    private function compareThenToString($needle, $compare, $divisor1, $divisor2, $is_2nd = false){
        $data1 = ($divisor1 == 0 || $needle == 0) ? 0:$needle/$divisor1 * 100;
        if ($is_2nd) {
            $data2 = ($divisor2 == 0 || $compare == 0) ? 0:$compare/$divisor2 * 100;
                // echo "$needle $divisor1 $compare $divisor2 <br>";
            $up = ($data1 > $data2) ? true:false;
            $data1 = getPercentage($needle, $compare);
            $data1 = number_format($data1);
            $result = ($up) ? "<i class='fa fa-arrow-up text-blue-400'></i> $data1 %":"<i class='fa fa-arrow-down text-red-400'></i> $data1 %";
        }
        else{
            $data1 = number_format($data1);
            $result = "$data1 %";
        }
        
        return $result;
    }

    private function get_RevenueByStore($fromdate, $todate, $shopid,$branchid){
        $this->load->model('reports/Model_sales_report');
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate));
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval->format('%y Year %m Month %d Day'))), "Y-m-d");
        $is_date_equal = ($fromdate == $todate) ? true:false;
        $is_range_tiny = $todate == date('Y-m-d') ? ($interval->format('%m') >= '1' ? false:($interval->format('%d') <= '14' ? true:false)):false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = ['Today','Yesterday'];
        } elseif ($is_date_equal) {
            $legend = [
                date_format(date_create($fromdate), 'M d, Y'),date_format(date_create($new_date), 'M d, Y')
            ];
        } else {
            $legend = [
                date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y'),
                date_format(date_create($new_date), 'M d')." to ".date_format(date_create($prev_to), 'M d, Y'),
            ];
        }
        $rbs1 = $this->Model_sales_report->revenueByStore($fromdate, $todate, $shopid, '', true, $is_range_tiny);
        $rbs2 = $this->Model_sales_report->revenueByStore($new_date, $prev_to, $shopid, '', true, $is_range_tiny);

        // get all shop names
        $data2 = []; $setted_key = [];
        if (count($rbs2) > 0) {
            $keys = array_pluck($rbs2, 'shopname');
            
            for ($i=0; $i < count($rbs1); $i++) {
                // set key(0,1,2,...) to $key to know where to push
                $key = array_search($rbs1[$i]['shopname'], $keys);
                $data2[] = ($key >= 0 && !in_array($key, $setted_key)) ? $rbs2[$key]:['total_amount'=>0];
                // compile all shopnames that is in data1
                $setted_key[] = $key;
            }
        }

        return [
            'legend' => $legend,
            'shopnames' => array_pluck($rbs1, 'shopname'),
            'data1' => array_pluck($rbs1, 'total_amount'),
            'data2' => array_pluck($data2, 'total_amount'),
            'data' => [
                $rbs1, $rbs2
            ]
        ];
    }

    private function get_RevenueByBranch($fromdate, $todate, $shopid, $branchid)
    {
        $this->load->model('reports/Model_sales_report', 'model_sales_report');
        $prev_to = date_sub(date_create($fromdate), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
        $interval = date_diff(date_create($fromdate), date_create($todate));
        $new_date = date_format(date_sub(date_create($prev_to), date_interval_create_from_date_string($interval->format('%y Year %m Month %d Day'))), "Y-m-d");
        $is_date_equal = ($fromdate == $todate) ? true:false;
        $is_range_tiny = $todate == date('Y-m-d') ? ($interval->format('%m') >= '1' ? false:($interval->format('%d') <= '14' ? true:false)):false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = ['Today','Yesterday'];
        } elseif ($is_date_equal) {
            $legend = [
                date_format(date_create($fromdate), 'M d, Y'),date_format(date_create($new_date), 'M d, Y')
            ];
        } else {
            $legend = [
                date_format(date_create($fromdate), 'M d')." to ".date_format(date_create($todate), 'M d, Y'),
                date_format(date_create($new_date), 'M d')." to ".date_format(date_create($prev_to), 'M d, Y'),
            ];
        }
        
        $rbs1 = $this->model_sales_report->revenueByBranch($fromdate, $todate, $shopid, $branchid, true, $is_range_tiny);
        $rbs2 = $this->model_sales_report->revenueByBranch($new_date, $prev_to, $shopid, $branchid, true, $is_range_tiny);

        // get all shop names
        $keys = array_pluck($rbs2, 'branchname');
        $data2 = []; $setted_key = [];
        if (count($rbs2) > 0) {
            for ($i=0; $i < count($rbs1); $i++) {
                // set key(0,1,2,...) to $key to know where to push
                $key = array_search($rbs1[$i]['branchname'], $keys);
                $data2[] = ($key >= 0 && !in_array($key, $setted_key)) ? $rbs2[$key]:['amount'=>0];
                // compile all branchnames that is in data1
                $setted_key[] = $key;
            }
        }else{
            $data2 = array_fill(0, count($rbs1), ['amount'=>0]);
        }

        return [
            'legend' => $legend,
            'branchnames' => array_pluck($rbs1, 'branchname'),
            'data1' => array_pluck($rbs1, 'amount'),
            'data2' => array_pluck($data2, 'amount'),
            'data' => [
                $rbs1, $rbs2
            ]
        ];
    }

    public function get_RevenueByLocation($fromdate, $todate, $shopid, $branchid)
    {
        $this->load->model('reports/Model_sales_report', 'model_sales_report');
        $rbl_filter = sanitize($this->input->get('rbl_filter'));
        $interval = date_diff(date_create($fromdate), date_create($todate));
        $is_date_equal = ($fromdate == $todate) ? true:false;
        $is_range_tiny = $todate == date('Y-m-d') ? ($interval->format('%m') >= '1' ? false:($interval->format('%d') <= '14' ? true:false)):false;
        $pmethodtype = '';

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = 'Today';
        } elseif ($is_date_equal) {
            $legend = date_format(date_create($fromdate), 'M d, Y');
        } else {
            $legend = date_format(date_create($fromdate), 'M d') ." - ". date_format(date_create($todate), 'M d, Y');
        }

        $rbl = $this->model_sales_report->revenueByLocation($fromdate, $todate, $shopid, $branchid, $pmethodtype, $rbl_filter, true, $is_range_tiny);

        // get all shop names
        // print_r($rbl);
        // exit();
        $data = [];
        $dataset = [
            'label' => $legend,
            'backgroundColor' => get_CoolorsHex(),
            'data' => [],
        ];
        $labels = $rbl['labels'];
        foreach ($rbl['data'] as $value) {
            $data[] = intval($value['total_amount']);
        }
        $result = runPieChartCalc($labels, $data);
        extract(runPieChartCalc($result['labels'], $result['data']));
        $cnt = count($data);
        if (in_array('Others', $labels)) {
            $data[$cnt - 1] = intval($data[$cnt - 1]/ $cnt);
        }
        $dataset['data'] = $data;
        $json_data = [
            "success" => 1,
            "chartdata" => [
                'labels' => $labels,
                'dataset' => [$dataset],
            ]
        ];
        return $json_data;
    }

    public function get_rblChartData()
    {
        $fromdate = sanitize($this->input->get('fromdate'));
        $todate = sanitize($this->input->get('todate'));
        $shopid = sanitize($this->input->get('shopid'));
        $branchid = sanitize($this->input->get('branchid'));
        $rbl_filter = sanitize($this->input->get('rbl_filter'));

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        
        if (isset($this->loginstate->get_access()['rbl'])) {
            if ($this->loginstate->get_access()['rbl']['view'] == 1) {
                $reschart['rBL'] = $this->get_RevenueByLocation($fromdate, $todate, $shopid, $branchid);   
            }
        }

        $response = array(
            'success' => true,
            'chartdata' => $reschart,
        );

        $shop = ($shopid == 0) ? 'All Shops':$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];
        $this->audittrail->logActivity('Dashboard', "Dashboard: Revenue by Location searched records filtered by $rbl_filter, dated $fromdate to $todate.", 'search', $this->session->userdata('username'));

        echo json_encode($response);
    }

    private function getPendingOrders($fromdate, $todate, $shopid, $branchid)
    {   
        $this->load->model('shop_branch/Model_shopbranch', 'model_shopbranch');
        $this->load->model('orders/Model_orders', 'model_orders');
        // print_r($requestData);
        // exit();
        $labels = [];
        if ($fromdate == $todate) {
            $legend = [date_format(date_create($fromdate), 'M d')];
            if ($fromdate == date("Y-m-d")) {
                $legend = ["Today"];
            }
        } else {
            $legend = [date_format(date_create($fromdate), 'M d') . " - " . date_format(date_create($todate), 'M d, Y')];
        }
        
        $result = $this->model_orders->get_pending_orders_chart($fromdate, $todate, $shopid, $branchid);
        // print_r($result['data']);
        // exit();
        $data = $result['data']; $total_val = 0;
        $chartdata = [
            'label' => $legend,
            'data' => [],
        ];
        foreach ($data as $key => $value) {
            if ($shopid != 'all' && $shopid > 0) {
                $labels[] = (strlen($value['branchname']) > 13) ? substr($value['branchname'], 0 , 13) . "...":$value['branchname'];
            }else{
                $labels[] = (strlen($value['shopname']) > 13) ? substr($value['shopname'], 0 , 13) . "...":$value['shopname'];
            }
            $total_val += intval($value['cnt']);
            $chartdata['data'][] = intval($value['cnt']);
        }
        // print_r($data);
        // exit();
        return [
            'total' => $total_val,
            'chartdata' => ['labels' => $labels, 'data' => $chartdata],
        ];
    }

    public function getInventoryList($fromdate, $todate, $shopid, $branchid)
    {
        $this->load->model('products/Model_products', 'model_products');
        if ($shopid > 0) {
            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));
            
            $result = $this->model_products->get_inv_chart($fromdate, $todate, '', $shopid, $branchid);
            $labels = $result['labels'];
            $inventory = [];
            foreach ($result['data'] as $key => $value) {
                $inventory[] = $value['inventory'];
            }
            $result = runPieChartCalc($labels, $inventory);
            // extract($result);
            extract(runPieChartCalc($result['labels'], $result['data']));
            $cnt = count($data);
            $data[$cnt - 1] = intval($data[$cnt - 1]/ $cnt);
            $chartdata = [$data];
            
            return [
                'success' => true,
                'total' => $total,
                'chartdata' => ['labels' => $labels, 'data' => $chartdata, 'background' => get_CoolorsHex()],
            ];
        }
        
        return [
            'success' => false,
            'total' => 0,
        ];
    }

    private function get_OblrChart($fromdate, $todate, $shopid, $branchid, $location){
        $this->load->model('reports/Model_sales_report', 'model_sales_report');
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        
        $interval = date_diff(date_create($fromdate), date_create($todate));
        $is_range_tiny = $todate == date('Y-m-d') ? ($interval->format('%m') >= '1' ? false:($interval->format('%d') <= '14' ? true:false)):false;

        $result = $this->model_sales_report->oblrChartData($fromdate, $todate, $shopid, $branchid, $location, true, $is_range_tiny);
        $is_date_equal = ($fromdate == $todate) ? true:false;

        if ($fromdate == date('Y-m-d') && $todate == date('Y-m-d')) {
            $legend = 'Today';
        } elseif ($is_date_equal) {
            $legend = date_format(date_create($fromdate), 'M d, Y');
        } else {
            $legend = date_format(date_create($fromdate), 'M d') ." - ". date_format(date_create($todate), 'M d, Y');
        }

        // get all shop names
        // print_r($oblr);
        // exit();
        $data = [];
        $dataset = [
            'label' => $legend,
            'backgroundColor' => get_CoolorsHex(),
            'data' => [],
        ];
        // here
        $labels = $result['labels'];
        foreach ($result['data'] as $value) {
            $data[] = intval($value['cnt']);
        }
        $result = runPieChartCalc($labels, $data);
        extract(runPieChartCalc($result['labels'], $result['data']));
        $cnt = count($data);
        if (in_array('Others', $labels)) {
            $data[$cnt - 1] = intval($data[$cnt - 1]/ $cnt);
        }
        $dataset['data'] = $data;
        return [
            "chartdata" => [
                'total' => $total,
                'labels' => $labels,
                'dataset' => [$dataset],
            ]
        ];
    }

    public function oblr_chart_data()
    {
        $fromdate = sanitize($this->input->get('fromdate'));
        $todate = sanitize($this->input->get('todate'));
        $shopid = sanitize($this->input->get('shopid'));
        $branchid = sanitize($this->input->get('branchid'));
        $oblr_filter = sanitize($this->input->get('oblr_filter'));

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        
        if (isset($this->loginstate->get_access()['oblr'])) {
            if ($this->loginstate->get_access()['oblr']['view'] == 1) {
                $reschart['oblr'] = $this->get_OblrChart($fromdate, $todate, $shopid, $branchid, $oblr_filter);   
            }
        }

        $response = array(
            'success' => true,
            'chartdata' => $reschart,
        );

        $shop = ($shopid == 0) ? 'All Shops':$this->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];
        $this->audittrail->logActivity('Dashboard', "Dashboard: Orders by Location searched records filtered by $oblr_filter, dated $fromdate to $todate.", 'search', $this->session->userdata('username'));

        echo json_encode($response);
    }

    public function single_product()
    {
        $this->isLoggedIn();
        $token_session = $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);

        $data = array(
            'token' => $token,
            // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
        );
        $this->load->view('includes/header', $data);
        $this->load->view('products/single-product', $data);
        $this->load->view('includes/footer', $data);
    }

    //end of insert all main navigation here //

    public function logout()
    {
        if(!empty($this->session->userdata('username'))){
            $this->log_seller_time_out_activity();
            $this->audittrail->logActivity('Logout', $this->session->userdata('username').' have been successfully logged out.', 'logout', $this->session->userdata('username'));
        }

        $this->session->sess_destroy();
        $this->load->view('login');
    }

    public function error_404()
    {
        $this->load->view('error_404');
    }

    // start of globally used functions

    public function get_tax()
    {
        $tax_id = $this->input->post('tax_id');

        return generate_json($this->model_sql->get_tax($tax_id)->row());
    }

    public function get_location_details()
    {
        $location_id = $this->input->get('location_id');

        return generate_json($this->model_sql->get_location_details($location_id)->row());
    }

    // end of globally used functions

    public function forgot_password()
    {
        if ($this->session->userdata('isLoggedIn') == true) {
            $token_session = $this->session->userdata('token_session');
            $token = en_dec('en', $token_session);
            header("location:" . base_url('Main/home/' . $token));
        }
        $this->load->view('forgot_password');
    }

    public function reset_password(){
        $email = sanitize($this->input->post('email-for-pass'));
        if(!empty($email)){
            $validate_username = $this->model->validate_username($email);
            if ($validate_username->num_rows() > 0) { // check if email is exist
                $userObj = $validate_username->row(); // get the matched data
            
                $reset_token = generate_passres_token(8);
                if($this->model->is_token_exist($reset_token)) {
                    $data = array(
                        "success" => 0,
                        "message" => 'Oops, Something went wrong pls try again.'
                    );
                }else{
                    $this->model->save_passres_token($reset_token);
                    $this->load->library('email');

                    $token_email = en_dec('en','CloudPandaPHInc');
                    $verify_href = base_url('Account/passwordreset/'.md5($email).'/'.$token_email.'/'.en_dec('en', $reset_token));

                    $subject = "Reset password";
                    // Get full html:
                    // $body = $this->password_reset_email($verify_href, $userObj);
                    $data['verify_href'] = $verify_href;
                    $data['userObj']     = $userObj;
                    $result = $this->email
                            ->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender())
                            // ->reply_to('noreply@shopanda.ph')    // Optional, an account where a human being reads.
                            ->to($email)
                            ->subject($subject)
                            ->message($this->load->view("includes/emails/forgotpass_template", $data, TRUE))
                            ->send();
                    $data = array(
                        "success" => 1,
                        "message" => 'Successfully requested for a password reset. Please check your email to complete the process.'
                    );
                }
            }else{
                $data = array(
                    "success" => 0,
                    "message" => 'Account does not exist.'
                );
            }
        }else{
            $data = array(
                "success" => 0,
                "message" => 'Please enter a valid email address.'
            );
        }
        generate_json($data);
    }

    public function password_reset_email($verify_href, $userObj){
        $body = "<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css' rel='stylesheet' id='bootstrap-css'>
            <script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js'></script>
            <script src='//code.jquery.com/jquery-1.11.1.min.js'></script>
            <!------ Include the above in your HEAD tag ---------->

            <div style='font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;'>
                <table style='width: 100%;'>
                <tr>
                    <td></td>
                    <td bgcolor='#fff'>
                    <div style='padding: 15px; max-width: 600px;margin: 0 auto;display: block; border-radius: 0px;padding: 0px; border: 1px solid lightseagreen;'>
                        <table style='width: 100%;background: ".footer_titlecolor()." ;'>
                        <tr>
                            <td></td>
                            <td>
                            <div>
                                <table width='100%'>
                                <tr>
                                    <td rowspan='2' style='text-align:center;padding:10px;'>
                                        <span style='color:white;float:right;font-size: 13px;font-style: italic;margin-top: 20px; padding:10px; font-size: 14px; font-weight:normal;'>
                                        ".get_company_name()."<span></span></span></td>
                                </tr>
                                </table>
                            </div>
                            </td>
                            <td></td>
                        </tr>
                        </table>
                        <table style='padding: 10px;font-size:14px; width:100%;'>
                        <tr>
                            <td style='padding:10px;font-size:14px; width:100%;'>
                                <p>Hi ".$userObj->fname.",</p>
                                <p><br /> You have requested to reset your password. Please click below to reset.</p>
                                <a class='btn btn-success' href='".$verify_href."'>Reset Password</a>
                                <p><br />This link will expire within 24 hours.
                                    If you are unaware that your admin has requested this, just ignore this message and nothing will be changed.</p>
                                <p> </p>
                                <p>Thanks for choosing ".get_company_name().",</p>
                                <p>".get_company_name()." Team.</p>
                            <!-- /Callout Panel -->
                            <!-- FOOTER -->
                            </td>
                        </tr>
                        <tr>
                        <td>
                            <div align='center' style='font-size:12px; margin-top:20px; padding:5px; width:100%; background:#eee;'>
                                © 2020 <a href='".base_url()."' target='_blank' style='color:#333; text-decoration: none;'>".get_company_name()."</a>
                            </div>
                            </td>
                        </tr>
                        </table>
                    </div>";
          return $body;
    }

    public function password_reset_form($email, $token, $reset_token)
    {
        if($this->model->is_24hrsold(en_dec('dec', $reset_token))){
            $this->load->view('error_404');
        }else{
            $token_email = en_dec('en','CloudPandaPHInc');
            if($token == $token_email){
                $validate_username = $this->model->validate_username_md5($email);
                if ($validate_username->num_rows() > 0) { // check if email is exist
                    $userObj = $validate_username->row();
                    $data_admin = array(
                                    'sys_users_id' => $userObj->sys_users_id,
                                    'reset_token' => $reset_token
                                );
                    $this->load->view('change_pass_reset', $data_admin);
                }else{
                    $this->load->view('error_404');
                }
            }else{
              $this->load->view('error_404');  
            }
        }
    }

    public function first_password_reset_form($email)
    {
        $this->session->sess_destroy();
        $checkIfFirstReset = $this->model->checkIfFirstReset($email)->num_rows();
        
        if($checkIfFirstReset == 0){
            $this->load->view('error_404');
        }else{
            $validate_username = $this->model->first_validate_username_md5($email);
            if ($validate_username->num_rows() > 0) { // check if email is exist
                $userObj = $validate_username->row();
                $data_admin = array(
                                'sys_users_id' => $userObj->id
                            );
                $this->load->view('first_change_pass_reset', $data_admin);
            }else{
                $this->load->view('error_404');
            }
           
        }
    }

    public function save_changepass_user(){
        $id = sanitize($this->input->post('user-id'));
        $reset_token = sanitize($this->input->post('reset-token'));
        $secNewpass = sanitize($this->input->post('secNewpass'));
        $secRetypenewpass = sanitize($this->input->post('secRetypenewpass'));

        if ($secNewpass == $secRetypenewpass) {
            // for password decryption
            $options = [
            'cost' => 12,
            ];

            $secNewpass = password_hash($secNewpass, PASSWORD_BCRYPT, $options);
            //for password decryption

            $query = $this->model_profile_settings->update_password($secNewpass, $id);
            $this->model->close_reset_token(en_dec('dec', $reset_token));

            $data = array('success' => 1, 'message' => 'Successfully Saved!');
        }else{
            $data = array('success' => 0, 'message' => 'New Password and Re-type Password is not the same.');
        }
        generate_json($data);
    }

    public function setfirstpassword(){
        $id = sanitize($this->input->post('user-id'));
        $secNewpass = sanitize($this->input->post('secNewpass'));
        $secRetypenewpass = sanitize($this->input->post('secRetypenewpass'));

        if ($secNewpass == $secRetypenewpass) {
            // for password decryption
            $options = [
            'cost' => 12,
            ];

            $secNewpass = password_hash($secNewpass, PASSWORD_BCRYPT, $options);
            //for password decryption

            $query = $this->model_profile_settings->update_first_password($secNewpass, $id);

            $data = array('success' => 1, 'message' => 'Successfully Saved!');
        }else{
            $data = array('success' => 0, 'message' => 'New Password and Re-type Password is not the same.');
        }
        generate_json($data);
    }

    public function set_password($md5_sys_user_id){
        $data_admin = array(
            'md5_sys_user_id'     => $md5_sys_user_id,
            'email'     => $this->model->get_userInformation_md5($md5_sys_user_id)->row()->username
        );

        $first_login = $this->model->get_userInformation_md5($md5_sys_user_id)->row()->first_login;
        if($first_login == 1){
            $this->load->view('set_password', $data_admin);
        }
        else{
            $this->load->view('error_404');
        }
        
    }

    public function setpassword()
    {
        $username  = sanitize($this->input->post('loginUsername'));
        $password  = sanitize($this->input->post('loginPassword'));
        $password2 = sanitize($this->input->post('loginPassword2'));

        $validate_username = $this->model->validate_username($username);
        $userObj = $validate_username->row(); // get the matched data
        $hash_password   = $userObj->password;
  
        if ($password == $password2) { // check if email is exist
            $success = $this->model->setpassword($username, $password);
            $this->audittrail->logActivity('Set Password', $username.' successfully set new password', 'set password', $username);
            $data = array(
                'success' => 1,
                'message' => 'New password successfully set.',
            );

        } else {
            $data = array(
                'success' => 0,
                'message' => 'Password doesn\'t match.',
            );
        }
        generate_json($data);
    }

    public function set_code($md5_sys_user_id){
        $data_admin = array(
            'md5_sys_user_id'     => $md5_sys_user_id,
            'email'     => $this->model->get_userInformation_md5($md5_sys_user_id)->row()->username
        );

        $code_isset = $this->model->get_userInformation_md5($md5_sys_user_id)->row()->code_isset;
        if($code_isset == 1){
            $this->load->view('set_code', $data_admin);
        }
        else{
            $this->load->view('error_404');
        }
        
    }

    public function setcode()
    {
        $username  = sanitize($this->input->post('loginUsername'));
        $loginCode  = sanitize($this->input->post('loginCode'));

        $validate_username = $this->model->validate_username($username);
        $userObj = $validate_username->row(); // get the matched data
        $login_code   = $userObj->login_code;
  
        if ($loginCode == $login_code) { 
            $success = $this->model->setcode($username);
            $validate_username = $this->model->validate_username($username);
            $userObj = $validate_username->row(); // get the matched data
            $verify_username = $userObj->active; // check if unverified email
            $verify_status   = ($userObj->shop_status == '') ? 1 : $userObj->shop_status; // check shop status
            $verify_status   = ($verify_status > 0 ) ? $verify_status : 0; // check shop status
            $branch_status   = ($userObj->branchid == 0) ? 1 : $userObj->branch_status; // check shop status
            $branch_status   = ($userObj->branch_status == '') ? 1 : $userObj->branch_status; // check shop status
            $branch_status   = ($branch_status > 0 ) ? $branch_status : 0; // check shop status
            $hash_password   = $userObj->password;
            $code_isset      = $userObj->code_isset;

            $userData = array( // store in array
                'id' => $userObj->sys_users_id,
                'username' => $userObj->username,
                'avatar' => $userObj->avatar,
                'functions' => $userObj->functions,
                'access_nav' => $userObj->access_nav,
                'access_content_nav' => $userObj->access_content_nav,
                'sys_shop' => $userObj->sys_shop,
                'fname' => $userObj->fname,
                'mname' => $userObj->mname,
                'lname' => $userObj->lname,
                'email' => $userObj->email,
                'mobile_number' => $userObj->mobile_number,
                'comm_type' => $userObj->comm_type,
                'branchid' => $userObj->branchid,
                'shopcode' => $userObj->shopcode == null ? 0 : $userObj->shopcode,
                'shopname' => $userObj->shopname == null ? 0 : $userObj->shopname,
                'shop_logo' => $userObj->logo == null ? 0 : $userObj->logo,
                'shop_url' => $userObj->shopurl == null ? 0 : $userObj->shopurl,
                'sys_users_id' => $userObj->sys_users_id,
                'app_members_id' => $userObj->app_members_id,
                'sys_shop_id' => $userObj->sys_shop_id,                        
                'isLoggedIn' => true
            );

            $token_session = uniqid();
            $token = en_dec('en', $token_session);
            $token_arr = array( // store token in array
                'token_session' => $token_session,
            );

            // set session
            $this->session->set_userdata($userData);
            $this->session->set_userdata($token_arr);


            $this->audittrail->logActivity('Set Code', $username.' successfully entered access code', 'set code', $username);
            $data = array(
                'success' => 1,
                'message' => 'Access Code matched.',
            );

            $access_nav = $this->session->userdata('access_nav');
            if(in_array(1, explode(', ', $access_nav))) {
                $is_dashboard = 1;
            }else{
                $is_dashboard = 0;
            }

            $this->audittrail->logActivity('Login', $username.' successfully logged in.', 'login', $username);
            ($code_isset == 1) ? null:$this->resetLoginAttempts($userObj->sys_users_id);

        } else {
            $data = array(
                'success' => 0,
                'message' => 'Access Code doesn\'t match.',
            );
        }
        generate_json($data);
    }

    public function log_seller_time_in_activity() {
        $sys_id = $this->session->userdata('sys_users_id');
        $sysshop = $this->session->userdata('sys_shop');
        if ($sys_id != '') {
            $this->model->log_seller_time_activity($sys_id, $sysshop, 'in');
        }
    }

    public function log_seller_time_out_activity() {
        $sys_id = $this->session->userdata('sys_users_id');
        $sysshop = $this->session->userdata('sys_shop');
        if ($sys_id != '') {
            $this->model->log_seller_time_activity($sys_id, $sysshop, 'out');
        }
    }
}
