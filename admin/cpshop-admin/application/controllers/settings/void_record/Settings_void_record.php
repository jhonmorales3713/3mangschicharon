<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Settings_void_record extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->model('setting/void_record/model_settings_void_record');
        $this->load->model('orders/model_orders');
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->load->view('login');
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

	public function void_record($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['void_record']['process'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token' 	  => $token,
                'main_nav_id' => $main_nav_id //for highlight the navigation
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_void_record', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function void_order($token = '', $reference_num)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['void_record']['process'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token' 	  => $token,
                'reference_num' => $reference_num,
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_void_order', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function void_order_view($token = '', $ref_num)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['void_record']['process'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_orders->get_sys_shop($member_id);

            if($sys_shop == 0) {
                $split = explode("-",$ref_num);
                $sys_shop = $split[0];
                $reference_num = $split[1];
            } else {
                $reference_num = $ref_num;
            }

            $row = $this->model_orders->orders_details($reference_num, $sys_shop);
            $refcode = $this->model_orders->get_referral_code($row['reference_num']);
            $branch_details = $this->model_orders->get_branchname_orders($reference_num, $sys_shop)->row();
            if($sys_shop != 0){
                $mainshopname = $this->model_orders->get_mainshopname($sys_shop)->row()->shopname;
            }else{
                $mainshopname = 'toktokmall';
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
            $this->load->view('settings/settings_void_order_view', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function void_record_list($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['void_record_list']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token' 	  => $token,
                'main_nav_id' => $main_nav_id //for highlight the navigation
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_void_record_list', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function void_record_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_orders->get_sys_shop($member_id);

        $query = $this->model_settings_void_record->void_record_table($sys_shop);

        generate_json($query);
    }

    public function order_details_view($token = '', $ref_num, $order_status)
    {
        // $this->load->view('settings/settings_void_order_details_view');
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['void_record_list']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_orders->get_sys_shop($member_id);

            if ($sys_shop == 0) {
                $split = explode('-', $ref_num);
                $sys_shop = $split[0];
                $reference_num = $split[1];
            } else {
                $reference_num = $ref_num;
            }

            $void_details = $this->model_settings_void_record->void_details($reference_num)->row();

            $data_admin = array(
                'token'                 => $token,
                'main_nav_categories'   => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'                => $sys_shop,
                'order_status'          => $order_status,
                'void_details'          => $void_details,
                'reference_num'         => $reference_num,
                'url_ref_num'           => $ref_num
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('settings/settings_void_order_details_view', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function export_void_records_tbl()
    {

        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_orders->get_sys_shop($member_id);

        $void_record = $this->model_settings_void_record->void_record_table($sys_shop,true);

        $filters = json_decode($this->input->post('_filters'));
        $filter_string = $this->audittrail->voidrecordListString($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B1', 'Void Record List');
        $sheet->setCellValue('B2', $filters->date_from.' - '.$filters->date_to);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->setCellValue('A6', 'Date');
        $sheet->setCellValue('B6', 'ID');
        $sheet->setCellValue('C6', 'Reference Number');
        $sheet->setCellValue('D6', 'Type');
        $sheet->setCellValue('E6', 'Reason');
        $sheet->setCellValue('F6', 'Username');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);

        $exceldata= array();
        foreach ($void_record['data'] as $key => $row) {

          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[4],
            '6' => $row[5]
          );
          $exceldata[] = $resultArray;
        }

        $sheet->getStyle('C')->getNumberFormat()->setFormatCode("#");
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=6; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Void Record List';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Void Record List', 'Void Record List has been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();

        //echo json_encode($void_record);
    }

    public function voidOrder()
    {
        $member_id     = $this->session->userdata('sys_users_id');
        $username      = $this->session->userdata('username');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $reference_num = $this->input->post('po_id');
        $reason        = $this->input->post('reason');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }else{
            $reference_num = $reference_num;
        }

        if($sys_shop == 0){
            $success = $this->model_settings_void_record->voidOrder_unpaid($reference_num, $username, $reason);
        }else{
            $checkIfVoided = $this->model_settings_void_record->checkIfVoided($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder_modify($reference_num, $sys_shop);
            $proceed = 1;

            if($checkIfVoided > 0){
                $response['success'] = false;
                $response['message'] = 'Order #'.$reference_num.' already voided';
                echo json_encode($response);
                die();
            }
            else if($salesOrder->payment_method == 'TOKTOKWALLET'){
                $url = get_apiserver_link().'toktok/ToktokAPI/giveMoney_toktokwallet';
                $data = array(
                    'sys_shop'      => $sys_shop,
                    'reference_num' => $reference_num,
                    'toktokuser_id' => $salesOrder->toktok_userid,
                    'amount'        => $salesOrder->total_amount+$salesOrder->delfee,
                    'signature'     => en_dec('en',md5('TOKTOKWALLET2021')),
                    'refund_type'   => 'Cancellation'
                );
                $result = $this->postCURL($url, $data);

                if(!empty($result->success)){
                    if($result->success == 1){
                        $proceed = 1;
                    }
                    else{
                        $proceed = 0;
                        $response['success']  = false;
                        $response['message']  = "There's a problem encountered refunding the amount.";
                        $response['response'] = json_encode($result);
                    }
                }
                else{
                    $proceed = 0;
                    $response['success']  = false;
                    $response['message']  = "There's a problem encountered refunding the amount.";
                    $response['response'] = json_encode($result);
                }
            }
            else{
                $proceed = 1;
            }
            if($proceed == 1){
                $success = $this->model_settings_void_record->voidOrder_paid($reference_num, $sys_shop, $username, $reason);
                $response['success'] = $success;
                $response['message'] = 'Order #'.$reference_num.' has been successfully voided';
                $this->audittrail->logActivity('Void Record', 'Order #'.$reference_num.' has been successfully voided.', 'Void Order', $this->session->userdata('username'));
            }
        }

        if(ini() == 'toktokmart' && $salesOrder->payment_method == 'TOKTOKWALLET'){
            $shopDetails               = $this->model_orders->read_shop($sys_shop);
            $order["shopItems"]        = $this->getShopItems($sys_shop, $reference_num);
            $order["payment_status"]   = "Paid";

            $checkbranchOrder          = $this->model_orders->checkbranchOrder($reference_num, $sys_shop);

            if($checkbranchOrder->num_rows() > 0){
                $order["shop_email"]     = $checkbranchOrder->row_array()['email']; 
                $order["shop_name"]      = $checkbranchOrder->row_array()['branchname'];
            }else{
                $order["shop_email"]     = $shopDetails['email']; 
                $order["shop_name"]      = $shopDetails['shopname'];
            }

            $url = get_apiserver_link().'Email/sendVoidedOrderEmail';
            $data = array(
                'sys_shop' => $sys_shop,
                'reference_num' => $reference_num,
                'refunded_amt' => $salesOrder->total_amount+$salesOrder->delfee
            );
            $result = $this->postCURL($url, $data);
        }
        else{
            //send email start
            $order_email = $this->model_settings_void_record->get_voided_ref_email($reference_num);
            $email=$order_email['email'];
            $name=$order_email['name'];
            
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($email);
            $this->email->subject(get_company_name()." | Your order has been cancelled.");
            $data['name']   = $name;
            $data['reference_num'] = $reference_num;
            $this->email->message($this->load->view("includes/emails/void_record_email", $data, TRUE));
            $this->email->send();
            //send email end
        }

        echo json_encode($response);
    }

    public function getOrders()
	{
        $reference_num = sanitize($this->input->post('reference_num'));

        $data = $this->model_settings_void_record->getOrders($reference_num)->result_array();

        if(count($data) > 0){
            $response = [
                'success' => true
            ];
        }else{
            $response = [
                'success' => false
            ];
        }

        echo json_encode($response);
    }

    public function order_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_settings_void_record->get_sys_shop($member_id);

        $query = $this->model_settings_void_record->order_table($sys_shop);
        generate_json($query);
    }

		public function get_prepayment_logs(){
			$reference_num = sanitize($this->input->post('reference_num'));
			if(empty($reference_num)){
				$data = array("success" => 0, "message" => "Please input reference number");
				generate_json($data);
				exit();
			}

			$isExist = $this->model_settings_void_record->get_prepayment_logs($reference_num);
			if($isExist->num_rows() == 0){
				$data = array("success" => 0, "message" => "Invalid transaction reference number");
				generate_json($data);
				exit();
			}

			$row = $isExist->row_array();
			$transaction_type = ($row['type'] == 'plus')
      ? 'Pre-Payment Reload ('.ucfirst(str_replace('_',' ',$row['logs_type'])).')'
      : 'Sales Billing ('.$row['refnum'].')';


			$data = array(
				"success" => 1,
				"deposit_ref_num" => ($row['deposit_ref_num'] == "") ? '---' :  $row['deposit_ref_num'],
				"tran_date" => $row['created_at'],
				"tran_amount" => number_format($row['amount'],2),
				"tran_ref_num" => $row['tran_ref_num'],
				"tran_type" => $transaction_type,
				"log_type" => $row['type'],
				"prepayment_void_refnum" => en_dec('en',$row['tran_ref_num'])
			);
			generate_json($data);
		}

	public function set_prepayment_voidrecord(){
			$prepayment_void_refnum = en_dec('dec',$this->input->post('prepayment_void_refnum'));
			$prepayment_reason = $this->input->post('prepayment_reason');
			$log_type = $this->input->post('log_type');

			if(empty($prepayment_void_refnum)){
				$data = array("success" => 0, "message" => 'Invalid transaction reference number');
				generate_json($data);
				exit();
			}

			if(empty($prepayment_reason)){
				$data = array("success" => 0, "message" => "Please tell the reason why you're voiding this pre-payment");
				generate_json($data);
				exit();
			}

			$isExist = $this->model_settings_void_record->get_prepayment_logs($prepayment_void_refnum);
			if($isExist->num_rows() == 0){
				$data = array("success" => 0, "message" => "Invalid transaction reference number");
				generate_json($data);
				exit();
			}

			$row = $isExist->row_array();
			$updated = $this->model_settings_void_record->update_prepayment($row['id'],$row['amount'],$row['refnum'],$log_type);
			if($updated == false){
				$data = array("success" => 0, "message" => "Unable to void prepayment transaction (".$row['tran_ref_num'].")");
				generate_json($data);
				exit();
			}

			$insert_data = array(
				"reference_num" => $row['tran_ref_num'],
				"f_id" => $row['id'],
				"username" => $this->session->username,
				"type" => 'Pre-Payment',
				"reason" => $prepayment_reason,
				"date_created" => datetime()
			);

			$inserted = $this->model_settings_void_record->set_prepayment_voidrecord($insert_data);
			if($inserted === false){
				$data = array("success" => 0, "message" => "Unable to save pre payment void record data.");
				generate_json($data);
				exit();
			}

      $data = array("success" => 1, "message" => "Pre-Payment transaction no.(".$row['tran_ref_num'].") voided successfully");
      $this->audittrail->logActivity('Void Record', 'Pre-Payment Transaction #'.$row['tran_ref_num'].' has been successfully voided.', 'Void Pre-Payment', $this->session->userdata('username'));
			generate_json($data);
    }

    public function get_billing_table(){
        $requestData = json_decode(json_encode($_REQUEST));
        $search = json_decode($requestData->searchValue);
        $data = $this->model_billing->get_billing_table($search, $requestData);
        echo json_encode($data);
    }

    public function postCURL($_url, $_param){

        $postvars = http_build_query($_param);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_POST, count($_param));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $response = json_decode($server_output);

        return $response;
    }

    public function getShopItems($sys_shop, $reference_num){
        $shopItems = array();
        $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);

        foreach($items as $item){
            if(empty($shopItems[$sys_shop])) {
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                    $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                }else{
                    $shippingdts['daystoship'] = $shopDetails["daystoship"];
                    $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                }
    
                $shopItems[$sys_shop] = array(
                    "shopname" => $shopDetails["shopname"],
                    "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                    "shopcode" => $shopDetails["shopcode"],
                    "shopaddress" => $shopDetails["address"],
                    "shopemail" => $shopDetails["email"],
                    "shopmobile" => $shopDetails["mobile"],
                    "shopdts" => $shippingdts["daystoship"],
                    "shopdts_to" => $shippingdts["daystoship_to"]
                );

                $shopItems[$sys_shop]["items"] = array();
            }

            array_push($shopItems[$sys_shop]["items"], array(
                "productid" => $item["productid"],
                "itemname" => $item["itemname"],
                "unit" => $item["unit"],
                "quantity" => $item["qty"],
                "price" => $item["amount"]
            ));
        }

        return $shopItems;
    }
}
