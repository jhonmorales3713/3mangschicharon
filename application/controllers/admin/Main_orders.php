<?php

date_default_timezone_set('Asia/Manila');
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            
            header("location:" . base_url('Main/logout'));
        }
    }

    public function orders_home($labelname = null){
        $this->session->set_userdata('active_page',$labelname);
        header('location:'.base_url('admin/Main_orders/'));
    }    

    public function processOrder()
    {
        $reference_num = $this->input->post('reference_num');
        //$reference_num = 'ABCD';
        $success    = $this->model_orders->processOrder($reference_num);
        // $items      = $this->model_orders->listShopItems($reference_num, $sys_shop);
        // $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
        //$this->model_orders->addOrderHistory($salesOrder->id, 'Order is being prepared for shipping by the seller.', 'Process Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));

        $order = $this->model_orders->orders_details($reference_num);
        //print_r($reference_num);
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
        $email = json_decode($order[0]['shipping_data'])->email;
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

    public function fulFillOrder(){
        $reference_num = $this->input->post('reference_num');
        
        $success    = $this->model_orders->fulFillOrder($reference_num);
        // $items      = $this->model_orders->listShopItems($reference_num, $sys_shop);
        // $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
        //$this->model_orders->addOrderHistory($salesOrder->id, 'Order is being prepared for shipping by the seller.', 'Process Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));

        $order = $this->model_orders->orders_details($reference_num);
        //print_r($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);
        // $this->sendProcessOrderEmail($order);
        

        $data = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $reference_num
        );
        
        $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        $email = json_decode($order[0]['shipping_data'])->email;
        $subject = "Order #".$reference_num." has been tagged as Fulfilled";
        $message = $this->load->view('email/templates/email_template',$data,true);
		$this->send_email($email,$subject,$message);
        $response['success'] = $success;
        $response['message'] = "Order #".$reference_num." has been tagged as Fulfilled";
        $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Fulfilled', 'Fulfill Order', $this->session->userdata('username'));
        echo json_encode($response);

    }
    public function confirmOrder(){
        $reference_num = $this->input->post('reference_num');
        $reason = $this->input->post('f_reason')!=''?$this->input->post('reason_option').','.$this->input->post('f_reason'):$this->input->post('reason_option');
        if($this->input->post('delivery_option') == ''){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Please select delivery status.'
            );
            echo json_encode($response);
            die();
        }
        if($this->input->post('reason_option') == 'Others' && $this->input->post('f_reason') == ''){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Please Input reason for others option.'
            );
            echo json_encode($response);
            die();
        }
        if($this->input->post('delivery_option') != 5 && $this->input->post('reason_option') == ''){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Please select reason below.'
            );
            echo json_encode($response);
            die();
        }
        $delivery_option = $this->input->post('delivery_option');
        $success    = $this->model_orders->confirmOrder($reference_num,$this->input->post('delivery_option'),$reason);
        // $items      = $this->model_orders->listShopItems($reference_num, $sys_shop);
        // $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
        //$this->model_orders->addOrderHistory($salesOrder->id, 'Order is being prepared for shipping by the seller.', 'Process Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));
        
        $order = $this->model_orders->orders_details($reference_num);
        //print_r($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);
        // $this->sendProcessOrderEmail($order);
        

        $data = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $reference_num
        );
        $status = 'Delivered';
        if($delivery_option == 8 || $delivery_option == 9){
            $status = 'Re-Deliver';
        }
        $data['delivery_option'] = $delivery_option;
        $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        $email = json_decode($order[0]['shipping_data'])->email;
        $subject = "Order #".$reference_num." has been tagged as ".$status;
        $message = $this->load->view('email/templates/email_template',$data,true);
		$this->send_email($email,$subject,$message);
        
        if($delivery_option == 5){
            $data['include_receipt'] = true;
            $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
            $email = json_decode($order[0]['shipping_data'])->email;
            $subject = "Order #".$reference_num." has been tagged as ".$status;
            $message = $this->load->view('email/templates/email_template',$data,true);
            $this->send_email($email,$subject,$message);
        }

        $response['success'] = $success;
        $response['message'] = "Order #".$reference_num." has been tagged as ".$status;
        $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as '.$status, $status.' Order', $this->session->userdata('username'));
        echo json_encode($response);

    }

    public function readyfordeliveryOrder2(){
        $reference_num = 'SS5ZRJFOG';
        $order = $this->model_orders->orders_details($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);
		$subject = get_company_name()." | Password Set Up";
        $data = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $reference_num
        );
        $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        $email = json_decode($order[0]['shipping_data'])->email;
        $subject = "Order #".$reference_num." has been confirmed";
         $this->load->view('email/templates/email_template',$data,'',true);
    }
    public function cancelOrder(){
        $reference_num = $this->input->post('reference_number');
        if($this->input->post('reason_option') == ''){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Please select reason.'
            );
            echo json_encode($response);
            die();
        }
        if($this->input->post('reason_option') == 'Others' && $this->input->post('f_reason') == ''){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Please Input reason for others option.'
            );
            echo json_encode($response);
            die();
        }
        $reason = $this->input->post('f_reason')!=''?$this->input->post('reason_option').','.$this->input->post('f_reason'):$this->input->post('reason_option');
        $success    = $this->model_orders->cancelOrder($reference_num,$reason);
        $order = $this->model_orders->orders_details($reference_num);
        //print_r($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);

        $data = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $reference_num
        );
        $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        $email = json_decode($order[0]['shipping_data'])->email;
        $subject = "Order #".$reference_num." has been tagged as Cancelled/Declined";
        $message = $this->load->view('email/templates/email_template',$data,true);
		$this->send_email($email,$subject,$message);
        $response['success'] = $success;
        $response['message'] = "Order #".$reference_num." has been tagged as Cancelled/Declined";
        $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Cancelled/Declined', 'Decline Order', $this->session->userdata('username'));
        echo json_encode($response);

    }
    public function readyfordeliveryOrder(){
        
        $reference_num = $this->input->post('reference_num');
        
        $this->load->library('upload');
        $count_upload = count($_FILES['order_attachment']['name']);
        $shipping_partner = $this->input->post('shipping_partner');
        $c_reference_num = $this->input->post('c_reference_num');
        $c_driver_name = $this->input->post('c_driver_name');
        $c_vehicle_type = $this->input->post('c_vehicle_type');
        $c_delivery_fee = $this->input->post('c_delivery_fee');
        $c_contact_number = $this->input->post('c_contact_number');
        //if($count_upload === '0'){
            
        if($count_upload == 0){
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'No attached image.',
            );
            echo json_encode($response);
            die();
        
        }else if($shipping_partner != '' && ($c_reference_num == '' || $c_vehicle_type == '' || $c_driver_name == '' || $c_delivery_fee == '')){
            
            $response = array(
                'success'      => false,
                'environment' => ENVIRONMENT,
                'message'     => 'Courier Information is required',
            );
            echo json_encode($response);
            die();

        }else{

            // if($this->input->post('f_member_shop') == ''){
            //     $response = array(
            //         'success'      => false,
            //         'environment' => ENVIRONMENT,
            //         'message'     => 'Please select a shop.'
            //     );
            //     echo json_encode($response);
            //     die();
            // }
            //$shopcode = $this->model_products->get_shopcode_via_shopid($this->session->userdata('sys_shop_id'));

            // $this->makedirImage($f_id, $shopcode);

            $directory    = 'assets/uploads/orders';
            if (!is_dir( 'assets/uploads/')) {
                mkdir( 'assets/uploads/', 0777, true);
            }
            if (!is_dir( 'assets/uploads/orders/')) {
                mkdir( 'assets/uploads/orders/', 0777, true);
            }
            //$shopcode = $this->model_products->get_shopcode_via_shopid($this->session->userdata('sys_shop_id'));

            $images = $this->model_orders->getImageByFileName($reference_num);
            foreach($images->result_array() as $image){
                if(file_exists('assets/uploads/orders/'.str_replace('==','',$image['filename']))){
                    unlink(('assets/uploads/orders/'.str_replace('==','',$image['filename'])));
                }
            }
        
            // foreach($reorder_image as $val){
            for($i = 0; $i < $count_upload; $i++) { 
                // if($val == $_FILES['product_image']['name'][$i]){
                    $_FILES['userfile']['name']     = $_FILES['order_attachment']['name'][$i];
                    $_FILES['userfile']['type']     = $_FILES['order_attachment']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $_FILES['order_attachment']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $_FILES['order_attachment']['error'][$i];
                    $_FILES['userfile']['size']     = $_FILES['order_attachment']['size'][$i];

                    $file_name   = en_dec('en', $_FILES['userfile']['name'].rand()).'.'.pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
                    
                    $imgArr[] =  str_replace('===.','==.',str_replace('.','==.',$file_name));
                    $config = array(
                        'file_name'     => $file_name,
                        'allowed_types' => '*',
                        'max_size'      => 20000,
                        'overwrite'     => FALSE,
                        'min_width'     => '1',
                        'min_height'     => '1',
                        'upload_path'
                        =>  $directory
                    );
                    $this->upload->initialize($config);
                    if ( ! $this->upload->do_upload()) {
                        $error = array('error' => $this->upload->display_errors());
                        $response = array(
                            'status'      => false,
                            'environment' => ENVIRONMENT,
                            'message'     => $error['error']
                        );
                        echo json_encode($response);
                        die();
                    }
                }
                
            //} 
        } 
        $courier_info = array(
            'reference_num' => $c_reference_num,
            'vehicle_type' => $c_vehicle_type,
            'name' => $c_driver_name,
            'delivery_fee' =>$c_delivery_fee,
            'partner' =>$shipping_partner,
            'contact_no'=> $c_contact_number
        );
       // $imgArr = array();
        $success    = $this->model_orders->readyfordeliveryOrder($reference_num,$imgArr,$courier_info);
        $order = $this->model_orders->orders_details($reference_num);
        $recipient_details = json_decode($order[0]['shipping_data']);
        $order_details = json_decode($order[0]['order_data']);
        $data = array(
            'recipient_details' => $recipient_details,
            'order_data'=> $order_details,
            'order_data_main'=> $order,
            'reference_num'=> $reference_num
        );
        $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        $email = json_decode($order[0]['shipping_data'])->email;
        $subject = "Order #".$reference_num." has been confirmed";
        $message = $this->load->view('email/templates/email_template',$data,true);
		$this->send_email($email,$subject,$message);

        $response['success'] = $success;
        $response['message'] = "Order #".$reference_num." has been tagged as Ready for Pick Up";
        if($c_reference_num != ''){
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' shipping delivery information has changed', 'Shipping Information', $this->session->userdata('username'));
            
        }
        $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Ready for Pick Up', 'Ready for Pick Up Order', $this->session->userdata('username'));
        echo json_encode($response);
        //for testing
        
        // $data['view'] = $this->load->view('email/order_processing',$data,TRUE);
        // $this->load->view('email/templates/email_template',$data,'',true);

    }

	function send_email($emailto,$subject,$message){
		
		$this->load->library('email');
        
		// $config = Array(
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => 'ssl://smtp.googlemail.com',
		// 	'smtp_port' => 465,
		// 	'smtp_user' => 'teeseriesphilippines@gmail.com',
		// 	'smtp_pass' => 'teeseriesph',
		// 	'charset' => 'utf-8',
		// 	'newline'   => "\r\n",
		// 	'wordwrap'=> TRUE,
		// 	'mailtype' => 'html'
		// );
        $this->load->library('email');
        if(strpos(base_url(),'3mangs.com')){
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => get_host(),
                'smtp_port' => 587,
                'smtp_user' => get_email(),
                'smtp_pass' => get_emailpassword(),
                'charset' => 'utf-8',
                'newline'   => "\r\n",
                'mailtype' => 'html'
            );
        }else{
            $config = Array(
            	'protocol' => 'smtp',
            	'smtp_host' => 'ssl://smtp.googlemail.com',
            	'smtp_port' => 465,
            	'smtp_user' => 'teeseriesphilippines@gmail.com',
            	'smtp_pass' => '@ugOct0810',
            	'charset' => 'utf-8',
            	'newline'   => "\r\n",
            	'wordwrap'=> TRUE,
            	'mailtype' => 'html'
            );
        }
		// $config = Array(
		// 	'protocol' => 'smtp',
		// 	'smtp_host' => get_host(),
		// 	'smtp_port' => 587,
		// 	'smtp_user' => get_email(),
		// 	'smtp_pass' => get_emailpassword(),
		// 	'charset' => 'utf-8',
		// 	'newline'   => "\r\n",
		// 	'wordwrap'=> TRUE,
		// 	'mailtype' => 'html'
		// );
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");  
		$this->email->from('noreply@3mangs.com');
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

    
    public function export_order_table()
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $request = url_decode(json_decode($this->input->post("_record_status_export")));
        // $member_id = $this->session->userdata('sys_users_id');
        $filter_text = "";
      
        // $sys_shop = $this->model_products->get_sys_shop($member_id);
        // $sys_shop = $this->session->userdata('sys_shop');
        $query = $this->model_orders->order_table($request, true);

        // $_record_status = ($this->input->post('_record_status') == 1 && $this->input->post('_record_status') != '') ? "Enabled":"Disabled";

        if($this->input->post('_record_status') == ''){
            $_record_status = 'All Records';
        }
        else if($this->input->post('_record_status') == 1){
            $_record_status = 'Enabled';
        }
        else if($this->input->post('_record_status') == 2){
            $_record_status = 'Disabled';
        }
        else{
            $_record_status = '';
        }

		$_name 			= ($this->input->post('_name') == "") ? "":"'" . $this->input->post('_name') ."'";
        
        /// for details column in audit trail
        // if($_name != ''){
        //     $filter_text .= $_record_status.' in '.$_shops. ', Product Name: '.$_name;
        // }else{
        //     $filter_text .= $_record_status.' in '.$_shops;
        // }
        
        $sheet->setCellValue('A1', "Orders");
        // print_r($request);
        // $sheet->setCellValue('B2', "Filter: '$_name', $_record_status in $_shops");
        $sheet->setCellValue('A2', date('Y/m/d'));
        $sheet->setCellValue('A4', "Date From");
        // $sheet->setCellValue('B2', "Filter: '$_name', $_record_status in $_shops");
        $sheet->setCellValue('A5',$request['date_from'] != '' ? $request['date_from'] : "All Time");
        $sheet->setCellValue('B4', "Date To");
        // $sheet->setCellValue('B2', "Filter: '$_name', $_record_status in $_shops");
        $sheet->setCellValue('B5', $request['date_to'] != '' ? $request['date_to']: "All Time");
        
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        // <th>Date</th>
        // <th width="150">Order</th>
        // <th>Customer</th>
        // <th>Contact No.</th>
        // <th>City</th>
        // <th>Amount</th>
        // <th>Discount</th>
        // <th>Shipping</th>
        // <th>Total</th>
        // <th>Payment</th>
        // <th>Status</th>
        // <th width="30">Action</th>
        $sheet->setCellValue('A6', 'Date Ordered');
        $sheet->setCellValue('B6', 'Order ID');
        $sheet->setCellValue('C6', 'Customer Name');
        // $sheet->setCellValue('D6', 'Contact No.');
        $sheet->setCellValue('D6', 'City');
        $sheet->setCellValue('E6', 'Amount');
        $sheet->setCellValue('F6', 'Discount');
        $sheet->setCellValue('G6', 'Shipping Fee');
        $sheet->setCellValue('H6', 'Total');
        $sheet->setCellValue('I6', 'Payment Status');
        $sheet->setCellValue('J6', 'Rating');
        $sheet->setCellValue('K6', 'Status');

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A4:B4')->getFont()->setBold(true);
        $sheet->getStyle('A6:K6')->getFont()->setBold(true);

        // print_r($query);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[1],
                '2' => $row[2],
                '3' => $row[3],
                '4' => $row[4],
                '5' => ucwords($row[5]),
                '6' => ucwords($row[6]),
                '7' => ucwords($row[7]),
                '8' => ucwords($row[8]),
                '9' => ucwords($row[9]),
                '10' => ucwords($row[10]),
                '11' => ucwords($row[11])
            );
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Orders List' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Product List', 'Products has been exported into excel with filter '.$filter_text, 'export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }
    public function orders_view($token = '', $ref_num = '')
    {
        $this->isLoggedIn();
        if( $this->loginstate->get_access()['orders']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = 'Orders';
            $images = $this->model_orders->getImageByFileName($ref_num);
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
                'images'       => $images,
                'shipping_partners'  => $this->model_dev_settings->get_shipping_partners()->result_array()
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