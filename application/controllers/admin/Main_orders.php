<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Main_orders extends CI_Controller {


    public function __construct() {
        parent::__construct();        
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('orders/model_orders');
        $this->load->library('upload');
        $this->load->library('uuid');
    }
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            
            //header("location:" . base_url('Main/logout'));
        }
    }

    public function orders_home($labelname = null){
        $this->session->set_userdata('active_page',$labelname);
        header('location:'.base_url('admin/Main_orders/'));
    }    

    public function processOrder()
    {
        $reference_num = $this->input->post('po_id');
        $reference_num = 'ABCD';
        $success    = $this->model_orders->processOrder($reference_num);
        // $items      = $this->model_orders->listShopItems($reference_num, $sys_shop);
        // $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
        //$this->model_orders->addOrderHistory($salesOrder->id, 'Order is being prepared for shipping by the seller.', 'Process Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));

        $order = $this->model_orders->orders_details($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);
        // $this->sendProcessOrderEmail($order);
        

		$subject = get_company_name()." | Password Set Up";
        $data = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $reference_num
        );
        
        $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        $email = 'moralesjhon03@gmail.com';
        $subject = "Order #".$reference_num." has been confirmed";
        $message = $this->load->view('email/templates/email_template',$data,true);
		$this->send_email($email,$subject,$message);

        $response['success'] = $success;
        $response['message'] = "Order #".$reference_num." has been tagged as Process Order";
        $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Process Order', 'Process Order', $this->session->userdata('username'));
        echo json_encode($response);

        //for testing
        
        // $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        // $this->load->view('email/templates/email_template',$data,'',true);
    }

    
	function send_email($emailto,$subject,$message){
		
		$this->load->library('email');
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => 'teeseriesphilippines@gmail.com',
			'smtp_pass' => 'teeseriesph',
			'charset' => 'utf-8',
			'newline'   => "\r\n",
			'wordwrap'=> TRUE,
			'mailtype' => 'html'
		);
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");  
		$this->email->from('ulul@gmail.com',get_company_name());
		$this->email->to($emailto);
		$this->email->subject($subject);
		$this->email->message($message);
		$this->email->send();
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
    public function index(){

        $this->isLoggedIn();

        $data = array(
            'active_page' => $this->session->userdata('active_page'),
            'subnav' => true, //for highlight the navigation,
            'token' => $this->session->userdata('token_session')
        );
        $data['page_content'] = $this->load->view('admin/dashboard/index',$data,TRUE);
		$this->load->view('admin_template',$data,'',TRUE);
    }
    

    public function order_table()
    {
        $this->isLoggedIn();
        
        $query = $this->model_orders->order_table();
        
        
        generate_json($query);
    }
    
    public function orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'cities'            => $this->model_orders->get_cities()->result_array()
            );


            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/orders/orders',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }

        
    }

    public function orders_view($token = '', $ref_num = '')
    {
        $this->isLoggedIn();
        if( $this->loginstate->get_access()['orders']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = 'Orders';
            //$main_nav_id = $this->views_restriction($content_url);
            $row            = $this->model_orders->orders_details($ref_num)[0];
            //$order_items    = $this->model_orders->order_item_table_print($reference_num, $sys_shop);
            //$refundedOrder  = $this->model_orders->get_refundedOrder($reference_num, $sys_shop)->result_array();
            // $orders_history = $this->model_orders->orders_history($row['order_id']);
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'reference_num'       => $ref_num,
                'order_details'       => $row,
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/orders/orders_view',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }
    public function order_item_table()
    {
        $this->isLoggedIn();
        $reference_num = sanitize($this->input->post('reference_num'));
        $query = $this->model_orders->order_item_table($reference_num);
        
        generate_json($query);
    }

    
}