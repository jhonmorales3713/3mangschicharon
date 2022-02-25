<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Sale_settlement2 extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('reports/Model_sale_settlement2', 'model_sale_settlement2');
    $this->load->model('wallet/model_prepayment');
    $this->load->model('wallet/Model_shops');
    $this->load->model('shop_branch/Model_shopbranch', 'model_branch');
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

      if (in_array($content_url, $url_content_arr) == false) {
          header("location:" . base_url('Main/logout'));
      } else {
          return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
      }
  }

  public function get_billing_table(){
    $requestData = json_decode(json_encode($_REQUEST));
    $search = json_decode($requestData->searchValue);
    $data = $this->model_sale_settlement2->get_billing_table($search, $requestData);
    echo json_encode($data);
  }

  public function export_billing_tbl(){
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $requestData = json_decode($this->input->post('_search'));
    // print_r($requestData);
    // exit();
    $search = json_decode($requestData->searchValue);
    $billing_res = $this->model_sale_settlement2->get_billing_table($search, $requestData, true);

    $types = ["1" => "All Status", "2" => "On Process", "3" => "Settled"];
    $status = $search->status;
    $shop = ($search->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($search->shop)->result_array()[0]['shopname'];

    $sheet->setCellValue('B1', 'Reports > Sale Settlement');
    $sheet->setCellValue('B2', "Filters: $types[$status] in $shop");
    $sheet->setCellValue('B3', $search->from.' - '.$search->to);
    
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(30);

    $sheet->setCellValue('A6', 'Date');
    $sheet->setCellValue('B6', 'Billing Code');
    $sheet->setCellValue('C6', 'Total Amount');
    $sheet->setCellValue('D6', 'Processing Fee');
    $sheet->setCellValue('E6', 'Net Amount');
    $sheet->setCellValue('F6', 'Shop');

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:J6')->getFont()->setBold(true);

    $exceldata= array();
    foreach ($billing_res['data'] as $key => $row) {
      $resultArray = array(
        '1' => $row[0],
        '2' => $row[1],
        '3' => $row[2],
        '4' => $row[3],
        '5' => $row[4],
        '6' => ucwords($row[5])
      );
      $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');
    $row_count = count($exceldata)+7;
    for ($i=7; $i < $row_count; $i++) {
      $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'Sale Settlement';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $filterstr = "and no filters. ";
    if ($search->status != 1 || $search->shop != "") {
      $filterstr = "filtered with: $types[$status]";
      $filterstr .= ($shop == "") ? " in All shops" : " in $shop, ";
    }
    $filterstr .= "Dated from $search->from to $search->to";
    $this->audittrail->logActivity('Sale Settlement', "Sale Settlement has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
    // echo json_encode($data);
  }

  public function get_billing_government_table(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_sale_settlement2->get_billing_government_table($search);
    echo json_encode($data);
  }

  public function get_billing_breakdown_table(){
    $search = $this->input->post('searchValue');
    $shop = $this->input->post('shop');
    $trandate = $this->input->post('trandate');
    $ratetype = $this->input->post('ratetype');
    $processrate = $this->input->post('processrate');
    $branch_id = $this->input->post('branch_id');
    $per_branch_billing = $this->input->post('per_branch_billing');
    $totalamount = $this->input->post('totalamount');
    $processfee = $this->input->post('processfee');
    $netamount = $this->input->post('netamount');
    $delivery_amount = $this->input->post('delivery_amount');

    $data = $this->model_sale_settlement2->get_billing_breakdown_table($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount,$delivery_amount);
    echo json_encode($data);
  }

  public function get_billing_logs(){
    $order_id = en_dec('dec',$this->input->post('order_id'));
    $totalamount = $this->input->post('totalamount');
    $processfee = $this->input->post('processfee');
    $netamount = $this->input->post('netamount');
    $data = $this->model_sale_settlement2->get_billing_logs($order_id,$totalamount,$processfee,$netamount);
    if($data){
      $remarks = "Viewed logs of ".$order_id;
      $this->audittrail->logActivity('Sale Settlement Report', $remarks, 'view', $this->session->userdata('username'));
    }    
    echo json_encode($data);
  }

  public function get_billing_government_breakdown_table(){
    $search = $this->input->post('searchValue');
    $shop = $this->input->post('shop');
    $trandate = $this->input->post('trandate');
    $portal_fee = $this->input->post('portal_fee');
    $totalamount = $this->input->post('totalamount');
    $processfee = $this->input->post('processfee');
    $netamount = $this->input->post('netamount');
    $delivery_amount = $this->input->post('delivery_amount');

    $data = $this->model_sale_settlement2->get_billing_government_breakdown_table($search,$shop,$trandate,$portal_fee,$totalamount,$processfee,$netamount,$delivery_amount);
    echo json_encode($data);
  }

  public function get_billing_branch_tbl(){
    $search = $this->input->post('searchValue');
    $id = $this->input->post('shopid');
    $trandate = $this->input->post('trandate');
    $data = $this->model_sale_settlement2->get_billing_branch_tbl($search,$id,$trandate);
    echo json_encode($data);
  }

  public function get_billing_branch_logs(){
    $branchid = en_dec('dec',$this->input->post('branchid'));
    $trandate = $this->input->post('trandate');
    $totalamount = $this->input->post('totalamount');
    $processfee = $this->input->post('processfee');
    $netamount = $this->input->post('netamount');
    $data = $this->model_sale_settlement2->get_billing_branch_logs($branchid,$trandate,$totalamount,$processfee,$netamount);
    echo json_encode($data);
  }

  public function get_billing_branch_government_tbl(){
    $search = $this->input->post('searchValue');
    $id = $this->input->post('shopid');
    $trandate = $this->input->post('trandate');
    $data = $this->model_sale_settlement2->get_billing_branch_government_tbl($search,$id,$trandate);
    echo json_encode($data);
  }

  public function get_billing(){
    $this->isLoggedIn();
    $id = $this->input->post('id');
    $bilcod = $this->input->post('bilcod');
    $row = $this->model_sale_settlement2->get_billing($id);
    if($row->num_rows() == 0){
      $data = array("success" => false, "message" => "Something went wrong. Please try again");
      echo json_encode($data);
      exit();
    }
    
    $data = array("success" => true, "message" => $row->row_array());
    $this->audittrail->logActivity('Sale Settlement', "Viewed a total of ".$row->num_rows()." logs on billing # $bilcod.", 'view', $this->session->userdata('username'));
    echo json_encode($data);
  }

  public function get_billing_government(){
    $this->isLoggedIn();
    $id = $this->input->post('id');
    $row = $this->model_sale_settlement2->get_billing_government($id);
    if($row->num_rows() == 0){
      $data = array("success" => false, "message" => "Something went wrong. Please try again");
      echo json_encode($data);
      exit();
    }

    $data = array("success" => true, "message" => $row->row_array());
    echo json_encode($data);
  }

  public function index($token = ""){
    $this->isLoggedIn();
    //start - for restriction of views
    $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
    $main_nav_id = $this->views_restriction($content_url);
    //end - for restriction of views main_nav_id

    // start - data to be used for views
    $user_id = $this->session->userdata('id'); $shops = [];
    $shopid = $this->model_shops->get_sys_shop($user_id);
    if ($shopid == 0) {
        $shops = $this->model_shops->get_shop_opts_oderbyname();
    }elseif ($shopid > 0) {
        $shops = $this->model_branch->get_branch_options($shopid);
    }
    $data_admin = array(
        'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $shops,
        'payments' => $this->model_sale_settlement2->get_options()
    );
    // end - data to be used for views

    // start - load all the views synchronously
    $this->load->view('includes/header', $data_admin);
    $this->load->view('reports/sale_settlement2', $data_admin);
    // end - load all the views synchronously
  }

  public function government($token = ""){
    $this->isLoggedIn();
    //start - for restriction of views
    $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
    $main_nav_id = $this->views_restriction($content_url);
    //end - for restriction of views main_nav_id

    // start - data to be used for views
    $data_admin = array(
        'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $this->model_sale_settlement2->get_shop_options(),
        'payments' => $this->model_sale_settlement2->get_options()
    );
    // end - data to be used for views

    // start - load all the views synchronously
    $this->load->view('includes/header', $data_admin);
    $this->load->view('accounts/billing_government', $data_admin);
  }

  public function settleBilling(){
    $amount_to_pay = $this->model_sale_settlement2->get_amount_to_pay($this->input->post('f_id-p'))['netamount'];
    $args = $this->input->post();
    $cur_val = [
      'paytype'=> $args['f_payment'],
      'payref'=> $args['f_payment_ref_num'],
      'paidamount'=> $args['f_payment_fee'],
      'payremarks'=> $args['f_payment_notes'],
      'paiddate' => date('Y-m-d H:i:s'),
      'paystatus' => 'Settled'
    ];
    $prev_val = [
      'paytype'=> 0,
      'payref'=> '',
      'paidamount'=> '0.00',
      'payremarks'=> '',
      'paiddate' => '',
      'paystatus' => 'On Process'
    ];

    $response = [
        'environment' => ENVIRONMENT,
        'success'     => false,
        'message'     => $this->response->message('error'),
        'csrf_name'   => $this->security->get_csrf_token_name(),
        'csrf_hash'   => $this->security->get_csrf_hash(),
    ];

    if($amount_to_pay != $this->input->post('f_payment_fee')) {
        $response['message'] = 'Payment Amount is not equal to total payable amount.';
        echo json_encode($response);
        die();
    }

    if($this->input->post('f_payment') != 'Others') {
        $validation = array(
            array('f_payment','Payment Type','required|max_length[5]|min_length[1]'),
            array('f_payment_ref_num','Payment Reference Number','required|max_length[50]'),
            array('f_payment_fee','Payment Amount','required|greater_than_equal_to['.floatval($amount_to_pay-1).']|less_than_equal_to['.floatval($amount_to_pay+1).']')
        );
    } else {
        $validation = array(
            array('f_payment_others','Payment Type','required|max_length[50]|min_length[2]'),
            array('f_payment_ref_num','Payment Reference Number','required|max_length[50]'),
            array('f_payment_fee','Payment Amount','required|greater_than_equal_to['.floatval($amount_to_pay-1).']|less_than_equal_to['.floatval($amount_to_pay+1).']')
        );
    }

    foreach ($validation as $value) {
        $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
    }

    if ($this->form_validation->run() == FALSE) {
        $response['message'] = validation_errors();
        echo json_encode($response);
        die();
    } else {

        $success = $this->model_sale_settlement2->tagPayment($this->input->post());
        $response['success'] = $success;
        $main = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val), $prev_val);
        $response['message'] = $this->response->message(($success ? 'success' : 'failed'), 'update', 'Sale Settlement');
        if ($success) {
          $this->audittrail->logActivity('Sale Settlement', "Sale Settlement # ".$args['f_id-p']." has been updated successfully. \nChanges:\n$main", 'update', $this->session->userdata('username'));
        }
    }
    echo json_encode($response);
  }

  public function settleBilling_portal_fee(){
    $amount_to_pay = $this->model_sale_settlement2->get_amount_to_pay_portal_fee($this->input->post('f_id-p'))['netamount'];

    $response = [
        'environment' => ENVIRONMENT,
        'success'     => false,
        'message'     => $this->response->message('error'),
        'csrf_name'   => $this->security->get_csrf_token_name(),
        'csrf_hash'   => $this->security->get_csrf_hash(),
    ];

    if($amount_to_pay != $this->input->post('f_payment_fee')) {
        $response['message'] = 'Payment Amount is not equal to total payable amount.';
        echo json_encode($response);
        die();
    }

    if($this->input->post('f_payment') != 'Others') {
        $validation = array(
            array('f_payment','Payment Type','required|max_length[5]|min_length[1]'),
            array('f_payment_ref_num','Payment Reference Number','required|max_length[50]'),
            array('f_payment_fee','Payment Amount','required|greater_than_equal_to['.floatval($amount_to_pay-1).']|less_than_equal_to['.floatval($amount_to_pay+1).']')
        );
    } else {
        $validation = array(
            array('f_payment_others','Payment Type','required|max_length[50]|min_length[2]'),
            array('f_payment_ref_num','Payment Reference Number','required|max_length[50]'),
            array('f_payment_fee','Payment Amount','required|greater_than_equal_to['.floatval($amount_to_pay-1).']|less_than_equal_to['.floatval($amount_to_pay+1).']')
        );
    }

    foreach ($validation as $value) {
        $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
    }

    if ($this->form_validation->run() == FALSE) {
        $response['message'] = validation_errors();
        echo json_encode($response);
        die();
    } else {

        $success = $this->model_sale_settlement2->tagPayment_portal_fee($this->input->post());
        $response['success'] = $success;
        $response['message'] = $this->response->message(($success ? 'success' : 'failed'), 'update', 'Billing');
    }
    echo json_encode($response);
  }

  public function shopandaUKey(){
			return "ShopandaKeyCloud3578";
	}

  public function processBilling(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processDailyMerchantPay($cronkey, $trandate);
    $this->deduction();
  }

  public function process_billing_government(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processdaily_merchant_pay_government($cronkey, $trandate);
  }

  public function processDailyMerchantPay($cronkey,$trandate){
		if($cronkey == $this->shopandaUKey())
		{
				// $res = $this->model_sale_settlement2->processDailyMerchantPay($trandate);
				$res = $this->model_sale_settlement2->processDailyMerchantPay_per_product_rate($trandate);
		}//cronkey checking close

		echo "DONE";
	}

  public function processdaily_merchant_pay_government($cronkey,$trandate){
		if($cronkey == $this->shopandaUKey())
		{
				$res = $this->model_sale_settlement2->processdaily_merchant_pay_government($trandate);
		}//cronkey checking close

		echo "DONE";
	}

  public function deduction(){
    $shop_w_wallet = $this->model_prepayment->get_allshop_wallet();
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    if($shop_w_wallet->num_rows() > 0){
      // start cron logs
      $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron for wallet deduction', 'For wallet deduction on all shop with pre payment'));

      $logs_batch = array();
      $bill_batch = array();
      foreach($shop_w_wallet->result() as $shop){
        $shop_wallet = $shop->balance;
        $billings = $this->model_prepayment->get_billing_for_shop($shop->shopid,$trandate);
        if($shop_wallet > 0 && $billings->num_rows() > 0){
          foreach($billings->result() as $bill){
            $deducted = $this->model_prepayment->update_wallet_balance_deduct($bill->netamount,$shop->shopid);
            $balance = $this->model_prepayment->get_shop_wallet($shop->shopid)->row()->balance;
            if($deducted == true){
              $billing_data = array(
                "paystatus" => "Settled",
                "paiddate" => todaytime(),
                "paidamount" => $bill->netamount,
                "paytype" => 7,
                "payref" => $shop->refnum,
                "id" => $bill->id
              );

              $logs_data = array(
                "shopid" => $shop->shopid,
                "refnum" => $bill->billcode,
                "logs_date" => today(),
                "logs_type" => 'wallet',
                "amount" => $bill->netamount,
                "balance" => $balance,
                "type" => 'minus',
                "created_at" => todaytime()
              );

              $bill_batch[] = $billing_data;
              $logs_batch[] = $logs_data;
            }
          }
        }
      }

      if(count((array)$bill_batch) > 0){
        $this->model_prepayment->update_billings_batch($bill_batch);
      }

      if(count((array)$logs_batch) > 0){
        $this->model_prepayment->set_wallet_logs_batch($logs_batch);
      }

      // End of cron logs
      $cron_status = ($cron_id != '') ? 'successful' : 'failed';
      $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

    }
  }

}
