<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
set_time_limit(0);
ini_set('memory_limit', '2048M');

class Notification extends CI_Controller
{
    

    public function __construct()
    {
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('notification/model_notification');
        
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
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

    public function logout()
    {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    public function index()
    {
        if ($this->session->userdata('isLoggedIn') == true) {
            $token_session = $this->session->userdata('token_session');
            $token = en_dec('en', $token_session);

            // $this->load->view(base_url('Main/home/'.$token));
            header("location:" . base_url('Main/home/' . $token));
        }

        $this->load->view('login');
    }

    public function notifications($token = ''){
		// $this->isLoggedIn();
	
		$data_admin = array(
			'token'  => $token,
            'shopid' => $this->model_notification->get_sys_shop($this->session->userdata('sys_users_id')),
            'shops'  => $this->model_notification->get_shop_options()
        );

		$this->load->view('includes/header', $data_admin);
		$this->load->view('notification/notifications', $data_admin);
		
	}

    public function notifications_table()
    {
        $this->isLoggedIn();
        $query = $this->model_notification->notifications_table();
        
        generate_json($query);
    }

    public function get_notification_details()
    {
        $this->isLoggedIn();
        $notiflogs_id  = sanitize($this->input->post('notiflogs_id'));
        $token_session = $this->session->userdata('token_session');
        $token         = en_dec('en', $token_session);

        $notif_data  = $this->model_notification->get_notification_details($notiflogs_id)->row_array();
        
        if($notif_data['has_read'] == 0){
            $this->model_notification->update_notifiation_has_read($notiflogs_id);
            $this->audittrail->logActivity('Notification', $this->session->userdata('username').' viewed notification log #'.$notiflogs_id, 'Notification', $this->session->userdata('username'));
        }

        $notif_count = $this->model_notification->count_notification()->row_array();
        $data = array(
            "success"                => 1,
            "notif_activity_details" => $notif_data['activity_details'],
            "notif_message"          => $notif_data['message'],
            "notif_link"             => ($this->session->userdata('sys_shop') == 0) ? base_url().str_replace('token', $token, $notif_data['link']) : base_url().str_replace('token', $token, $notif_data['link_shop']),
            "notif_date_created"     => date("M d, Y h:i A", strtotime($notif_data["date_created"])),
            "notif_count"            => $notif_count['notif_count']
        );
        
        generate_json($data);
    }

    public function get_notification()
    {
        $this->isLoggedIn();
        $token_session = $this->session->userdata('token_session');
        $token         = en_dec('en', $token_session);

        $notif_data  = $this->model_notification->get_notification()->result_array();
        $notif_count = $this->model_notification->count_notification()->row_array();
        $dataNotif = array();
        foreach($notif_data as $row){
            $data['link']                = $row['link'];
            $data['link_shop']           = $row['link_shop'];
            $data['activity_details']    = $row['activity_details'];
            $data['date_created']        = date("M d, Y h:i A", strtotime($row["date_created"]));
            $data['has_read']            = $row['has_read'];
            $data['sys_notification_id'] = $row['sys_notification_id'];
            $data['shop_id']             = $row['shop_id'];
            $data['branch_id']           = $row['branch_id'];

            $dataNotif[] = $data;
        }

        $data = array(
            "success"    => 1,
            "notif_data" => $dataNotif,
            "notif_count" => $notif_count['notif_count']
        );
        
        generate_json($data);
    }

    public function read_notification()
    {
        $this->isLoggedIn();
        $sys_notification_id  = sanitize($this->input->post('sys_notification_id'));
        $shop_id              = sanitize($this->input->post('shop_id'));
        $branch_id            = sanitize($this->input->post('branch_id'));
        $success              = 0;
        $notiflogs_data       = $this->model_notification->get_notificationlogs_details($sys_notification_id, $shop_id, $branch_id)->row_array();
        $notif_data           = $this->model_notification->get_notification_details($notiflogs_data['id'])->row_array();
        
        if($notif_data['has_read'] == 0){
            $this->model_notification->update_notifiation_has_read($notiflogs_data['id']);
            $this->audittrail->logActivity('Notification', $this->session->userdata('username').' viewed notification log #'.$notiflogs_data['id'], 'Notification', $this->session->userdata('username'));
            $success = 1;
        }

        $data = array(
            "success"                => $success
        );
        
        generate_json($data);
    }
}
