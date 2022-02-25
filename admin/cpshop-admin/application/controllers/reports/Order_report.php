<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
set_time_limit(0);
ini_set('memory_limit', '2048M');

class Order_report extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_order_report', 'model_order_report');
        $this->db2 = $this->load->database('reports', TRUE);
    }

    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    public function views_restriction($content_url)
    {        
        $access_content_nav = $this->session->userdata('access_content_nav');
        $arr_ = explode(', ', $access_content_nav); 
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
    
    //main view
    public function index($token = "")
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['or']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            $member_id  = $this->session->userdata('sys_users_id');
            $shopid     = $this->session->userdata('sys_shop_id');
            $branchid   = $this->session->userdata('branchid');
            $shopcode   = $this->session->userdata('shopcode');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $shopid,
                'branchid'            => $branchid,
                'shopcode'            => $shopcode,
                'shops'               => $this->model_order_report->get_shop_options(),
                'regions'             => $this->model_order_report->get_regions()->result_array(),
                'provinces'           => $this->model_order_report->get_provinces()->result_array(),
                'citymuns'            => $this->model_order_report->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('reports/order_report', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }  

    public function order_report_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_order_report->get_sys_shop($member_id);
        if($sys_shop == 0){
            $query = $this->model_order_report->order_report_table($sys_shop);
        }else{
            $query = $this->model_order_report->order_report_table_shop($sys_shop); 
        }
        
        generate_json($query);
    }

    public function export_order_report()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_order_report->get_sys_shop($member_id);
        $status        = $this->input->post('status_export');
        $date_from 	   = format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 	   = format_date_reverse_dash($this->input->post('date_to_export'));
        $requestData   = url_decode(json_decode($this->input->post("request_filter")));
        $filter_string = $this->audittrail->ordersFilterString($this->input->post());
        $date_from_2   = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2     = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
        $_name 		   = $this->input->post('_name_export');

        if($sys_shop == 0){
            $query = $this->model_order_report->order_report_table_export($sys_shop);
        }else{
            $query = $this->model_order_report->order_report_table_shop_export($sys_shop); 
        }

        $getShippingData    = $this->model_order_report->getShippingData($_name, $date_from_2, $date_to_2)->result_array();
        $getShippingDataArr = [];

        foreach($getShippingData as $row){
			$getShippingDataArr[strval($row['reference_num'])][strval($row['sys_shop'])]['delivery_amount'] = $row['delivery_amount'];
        }

        $sheet->setCellValue('B1', 'Order Report');
        $sheet->setCellValue('B2', $this->input->post('date_from_export').' - '.$this->input->post('date_to_export'));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);

        $sheet->setCellValue('A6', 'Ordered Date');
        $sheet->setCellValue('B6', 'Payment Date');
        $sheet->setCellValue('C6', 'Order Ref#');
        $sheet->setCellValue('D6', 'Customer');
        $sheet->setCellValue('E6', 'Contact No.');
        $sheet->setCellValue('F6', 'Amount');
        $sheet->setCellValue('G6', 'Shipping');
        $sheet->setCellValue('H6', 'Total');
        $sheet->setCellValue('I6', 'Actual Shipping Fee');
        $sheet->setCellValue('J6', 'Payment');
        $sheet->setCellValue('K6', 'Status');
        $sheet->setCellValue('L6', 'Shop');
        $sheet->setCellValue('M6', 'Branch');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:M6')->getFont()->setBold(true);
        
        $exceldata= array();
        $international = c_international();
        $allow_cod = cs_clients_info()->c_allow_cod;
        foreach($query as $row){

            $payment_status = display_payment_status($row['payment_status'], $row['payment_method'], $allow_cod, true);
            $order_status   = display_order_status($row['order_status'], true);
            $branchname = $this->model_order_report->get_branchname($row["reference_num"], $row['sys_shop']);

            if($row['sys_shop'] != 0) {
				// $voucher_total_amount = $this->model_order_report->get_vouchers_total_shop($row["reference_num"], $row["sys_shop"])->total_voucher_amount;
				$voucher_total_amount = 0;
				$subtotal = max($row["total_amount"] - $voucher_total_amount, 0);
			}else{
				$voucher_total_amount = 0;
				$subtotal = $row["total_amount"];
            }
            
            if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
			}

            if($international == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->model_order_report->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);
                $actual_shipping_fee_conv       = currencyConvertedRate($row['delivery_amount'], $row['currval']);

				$subtotal_converted             = displayCurrencyValue_withPHP($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue_withPHP($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue_withPHP($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue_withPHP(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				$actual_shipping_fee_converted  = displayCurrencyValue_withPHP($row['delivery_amount'], $actual_shipping_fee_conv, $row['currcode']);
			}
			else if($international == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->model_order_report->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);
                $actual_shipping_fee_conv       = currencyConvertedRate($row['delivery_amount'], $row['currval']);

				$subtotal_converted             = displayCurrencyValue($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
                $actual_shipping_fee_converted  = displayCurrencyValue($row['delivery_amount'], $actual_shipping_fee_conv, $row['currcode']);
            }
			else{
				$subtotal_converted             = number_format($subtotal, 2);
				$voucher_total_amount_converted = number_format($voucher_total_amount, 2);
				$delivery_amount_converted      = number_format($delivery_amount, 2);
				$total_amount_converted         = number_format(max($subtotal+$delivery_amount, 0), 2);
                $actual_shipping_fee_converted  = number_format($row['delivery_amount'], 2);
			}

            $resultArray = array(
                '1' => $row["date_ordered"],
                '2' => $row["payment_date"],
                '3' => $row["reference_num"],
                '4' => ucwords($row["name"]),
                '5' => $row["conno"],
                '6' => $subtotal_converted,
                '7' => $delivery_amount_converted,
                '8' => $total_amount_converted,
                '9' => $actual_shipping_fee_converted,
                '10' => $payment_status,
                '11' => $order_status,
                '12' => $row['shopname'],
                '13' => $branchname
            );
            
            $exceldata[] = $resultArray;
        }
      
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'orders_report';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Order Report', 'Order Report has been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function toktok_booking_report($token = "")
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['tbr']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            $member_id  = $this->session->userdata('sys_users_id');
            $shopid     = $this->session->userdata('sys_shop_id');
            $branchid   = $this->session->userdata('branchid');
            $shopcode   = $this->session->userdata('shopcode');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $shopid,
                'branchid'            => $branchid,
                'shopcode'            => $shopcode,
                'shops'               => $this->model_order_report->get_shop_options(),
                'regions'             => $this->model_order_report->get_regions()->result_array(),
                'provinces'           => $this->model_order_report->get_provinces()->result_array(),
                'citymuns'            => $this->model_order_report->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('reports/toktok_booking_report', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    } 

    public function toktok_booking_report_table()
    {
        // $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_order_report->get_sys_shop($member_id);
        if($sys_shop == 0){
            $query = $this->model_order_report->toktok_booking_report_table($sys_shop);
        }else{
            $query = $this->model_order_report->toktok_booking_report_table_shop($sys_shop); 
        }
        
        generate_json($query);
    }

    public function export_toktok_booking_report()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_order_report->get_sys_shop($member_id);
        $status        = $this->input->post('status_export');
        $date_from 	   = format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 	   = format_date_reverse_dash($this->input->post('date_to_export'));
        $requestData   = url_decode(json_decode($this->input->post("request_filter")));
        // $filter_string = $this->audittrail->ordersFilterString($this->input->post());
        $date_from_2   = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2     = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
        $_name 		   = $this->input->post('_name_export');

        if($sys_shop == 0){
            $query = $this->model_order_report->toktok_booking_report_table_export($sys_shop);
        }else{
            $query = $this->model_order_report->toktok_booking_report_table_shop_export($sys_shop); 
        }

        $sheet->setCellValue('B1', 'toktok Booking Report');
        $sheet->setCellValue('B2', $this->input->post('date_from_export').' - '.$this->input->post('date_to_export'));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);

        $sheet->setCellValue('A6', 'Ordered Date');
        $sheet->setCellValue('B6', 'Order Ref#');
        $sheet->setCellValue('C6', 'Rider Name');
        $sheet->setCellValue('D6', 'Rider Contact No.');
        $sheet->setCellValue('E6', 'Rider Plate Number');
        $sheet->setCellValue('F6', 'Delivery Amount');
        $sheet->setCellValue('G6', 'Shop');
        $sheet->setCellValue('H6', 'Branch');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:H6')->getFont()->setBold(true);
        
        $exceldata= array();
        foreach($query as $row){

            if($row["rider_name"] != ''){
                $resultArray = array(
                    '1' => $row["date_ordered"],
                    '2' => $row["reference_num"],
                    '3' => ucwords($row["rider_name"]),
                    '4' => $row["rider_conno"],
                    '5' => $row["rider_platenum"],
                    '6' => $row["delivery_amount"],
                    '7' => $row["shopname"],
                    '8' => $this->model_order_report->get_branchname($row["reference_num"], $row['sys_shop'])
                );
                
                $exceldata[] = $resultArray;
            }
        }
      
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'toktokBookingReport';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('toktok Booking Report', 'toktok Booking Report has been exported into excel with filter '.$this->input->post('date_from_export').' - '.$this->input->post('date_to_export'), 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }


     ///Order List Payout Status Report
     
     public function order_list_payout_status_report($token = "")
     {
         $this->isLoggedIn();
         if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['olps']['view'] == 1) {
             $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
             $main_nav_id = $this->views_restriction($content_url);
             $member_id  = $this->session->userdata('sys_users_id');
             $shopid     = $this->session->userdata('sys_shop_id');
             $branchid   = $this->session->userdata('branchid');
             $shopcode   = $this->session->userdata('shopcode');
         
             $data_admin = array(
                 'token'               => $token,
                 'main_nav_id'         => $main_nav_id, //for highlight the navigation
                 'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                 'shopid'              => $shopid,
                 'branchid'            => $branchid,
                 'shopcode'            => $shopcode,
                 'shops'               => $this->model_order_report->get_shop_options(),
                 'regions'             => $this->model_order_report->get_regions()->result_array(),
                 'provinces'           => $this->model_order_report->get_provinces()->result_array(),
                 'citymuns'            => $this->model_order_report->get_citymuns()->result_array(),
             );
 
             $this->load->view('includes/header', $data_admin);
            $this->load->view('reports/order_list_payout_status_report', $data_admin);
         }else{
             $this->load->view('error_404');
         }
     } 



     public function order_list_payout_status_report_table()
    {
        $this->isLoggedIn();
   

        $export_data = array(
            '_order_ref'          => $this->input->post('_order_ref'),
            '_payment_ref'        => $this->input->post('_payment_ref'),
            '_bill_code'         => $this->input->post('_bill_code'),
            '_shops'            => $this->input->post('_shops'),
            '_branch'           => $this->input->post('_branch'),
            'date_from'         => $this->input->post('date_from'),
            // 'date_to_export'           => $this->input->post('date_to_export'),
        );

        $query = $this->model_order_report->order_list_payout_status_report_model($export_data);
      
        
        generate_json($query);
    }


    public function export_order_list_payout_status_report_report()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();

        $export_data = array(
            '_order_ref_export'        => $this->input->post('_order_ref_export'),
            '_payment_ref_export'      => $this->input->post('_payment_ref_export'),
            '_bill_code_export'        => $this->input->post('_bill_code_export'),
            '_shops_export'            => $this->input->post('_shops_export'),
            '_branch_export'           => $this->input->post('_branch_export'),
            'date_from_export'         => $this->input->post('date_from_export'),
            // 'date_to_export'           => $this->input->post('date_to_export'),
        );

    
        // print_r($export_data);
        // die();
 
        $query = $this->model_order_report->order_list_payout_status_report_export($export_data);
    

        $sheet->setCellValue('B1', 'Order List Payout Status Report');
        $sheet->setCellValue('B2', $this->input->post('date_from_export'));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);

        $sheet->setCellValue('A6', 'Billing Date');
        $sheet->setCellValue('B6', 'Billing Code');
        $sheet->setCellValue('C6', 'SHOP');
        $sheet->setCellValue('D6', 'Order Ref #');
        $sheet->setCellValue('E6', 'Customer');
        $sheet->setCellValue('F6', 'Payment Ref #');
        $sheet->setCellValue('G6', 'Order Type');
        $sheet->setCellValue('H6', 'Amount');
        $sheet->setCellValue('I6', 'Delivery Amount');
        $sheet->setCellValue('J6', 'Refcom Amount');
        $sheet->setCellValue('K6', 'Net Amount');    


        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:K6')->getFont()->setBold(true);
        
        $exceldata= array();

        // print_r($query);
        // die();
      
        
        foreach($query as $row){


            switch (ini()) {
				case "toktokmall":


                    if($row["billcode"] != ''){
                        $order_type = 'Regular Order';
                        if($row['user_id'] != 0){
                        $order_type = "Thru OF Login";
                        }
        
                        if($row['referral_code'] != "" || $row['referral_code'] != null){
                        $order_type = "Via OF Shoplink";
                        }
        
        
                        $sum = $row['delivery_amount'] + $row['srp_totalamount'] - $row['refcom_total_amount'];
        
                            $resultArray = array(
                                '1' => $row['trandate'],
                                '2' => $row["billcode"],
                                '3' => $row["shopname"],
                                '4' => $row["reference_num"],
                                '5' => $row["name"],
                                '6' => $row["paypanda_ref"],
                                '7' => $order_type,
                                '8' => number_format($row["srp_totalamount"],2),
                                '9' => number_format($row["delivery_amount"],2),
                                '10' => number_format($row["refcom_total_amount"],2),
                                '11' => number_format($sum,2)
                            );
                            
                            $exceldata[] = $resultArray;
                    }
							
							
				break;
				default:

                if($row["billcode"] != ''){
                    $order_type = 'Regular Order';
                    if($row['user_id'] != 0){
                    $order_type = "Thru OF Login";
                    }
    
                    if($row['referral_code'] != "" || $row['referral_code'] != null){
                    $order_type = "Via OF Shoplink";
                    }
    
    
                    $sum = $row['delivery_amount'] + $row['total_amount'] - $row['refcom_total_amount'];
    
                        $resultArray = array(
                            '1' => $row['trandate'],
                            '2' => $row["billcode"],
                            '3' => $row["shopname"],
                            '4' => $row["reference_num"],
                            '5' => $row["name"],
                            '6' => $row["paypanda_ref"],
                            '7' => $order_type,
                            '8' => $row["total_amount"],
                            '9' => $row["delivery_amount"],
                            '10' => number_format($row["refcom_total_amount"],2),
                            '11' => number_format($sum,2)
                        );
                        
                        $exceldata[] = $resultArray;
                }

					

				break;
			}
          
        }
      
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'Order List Payout Status Report';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Order List Payout Status Report', 'Order List Payout Status Report has been exported into excel with filter '.$this->input->post('date_from_export'), 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }


}