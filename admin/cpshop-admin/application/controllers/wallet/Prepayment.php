<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Prepayment extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('wallet/model_prepayment');
    $this->load->model('wallet/Model_shops');
    $this->load->library('s3_upload');
  }

  protected function email_signature_key(){
    // return 'CPSHOPEMAIL2021';
    if (ENVIRONMENT == "production") {
      return 'CPSHOPEMAIL2021';
    }else if (ENVIRONMENT == "testing") {
      return 'CPSHOPEMAIL2021';
    }else{
      return 'CPSHOPEMAIL2021';
    }
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

  public function get_prepayment_table(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_prepayment->get_prepayment_table($search, $_REQUEST);
    echo json_encode($data);
  }

  public function export_prepayment_table(){
    $requestData = url_decode(json_decode($this->input->post("_search")));
    $search = json_decode($requestData['searchValue']);
    // print_r($search);
    // exit();
    $search_val = ($search->search == "") ? "No Search":$search->search;
    $shop_name = ($search->shop == "") ? "All Shops":$this->Model_shops->get_shop_details($search->shop)->result_array()[0]['shopname'];
    $branch_name = ($search->branch == "") ? "All Branch":$this->model_prepayment->get_branch($search->branch)->result_array()[0]['branchname'];
    $fromdate = ($search->from == "") ? date('Y-m-d'):$search->from;
    $todate = ($search->to == "") ? date('Y-m-d'):$search->to;

    $data = $this->model_prepayment->get_prepayment_table($search, $requestData, true);
    // echo json_encode($data);
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('B1', "Pre Payment");
    $sheet->setCellValue('B2', "Filter: $search_val;$shop_name");
    $sheet->setCellValue('B3', "$fromdate to $todate");

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    // $sheet->getColumnDimension('F')->setWidth(10);
    // $sheet->getColumnDimension('F')->setWidth(30);

    $sheet->setCellValue('A6', 'Reference No.');
    $sheet->setCellValue('B6', 'Shop Name');
    $sheet->setCellValue('C6', 'Branch Name');
    $sheet->setCellValue('D6', 'Date Updated');
    $sheet->setCellValue('E6', 'Balance');
    // $sheet->setCellValue('F6', 'Remarks');

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:E6')->getFont()->setBold(true);

    // print_r($data['data']);
    // exit();
    $exceldata= array();
    foreach ($data['data'] as $key => $row) {

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
    $row_count = count($exceldata)+11;
    for ($i=7; $i < $row_count; $i++) {
        $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'Pre-Payment ' . date('Y/m/d');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    ob_end_clean();

    $filterstr = "and no filters. ";
    if ($search->shop !== "" || $search->search !== "") {
      $filterstr = "with filters: $search_val in $shop_name, $branch_name ";
    }
    $filterstr .= "Dated $fromdate to $todate";
    $this->audittrail->logActivity('Prepayment', "Prepayment has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));

    return $writer->save('php://output');
    exit();
    // print_r($data['data']);
  }

  public function get_shop_wallet_logs_table(){

    $search = json_decode($this->input->post('searchValue'));
    $refnum = $this->input->post('refnum');
    $id = en_dec('dec',$this->input->post('vid'));
    $branchid = en_dec('dec',$this->input->post('branchid'));
    $data = $this->model_prepayment->get_shop_wallet_logs_table($search,$id,$branchid);
    if ($data['recordsTotal'] > 0) {
      $this->audittrail->logActivity('Prepayment', "Viewed a total of ".$data['recordsTotal']." logs on prepayment # $refnum.", 'view', $this->session->userdata('username'));
    }
    echo json_encode($data);
  }

  public function get_shop_wallet_and_sales(){
    $shopid = en_dec('dec',$this->input->post('shopid'));
    $branchid = en_dec('dec',$this->input->post('branchid'));

    $balance = $this->model_prepayment->get_total_balance($shopid,$branchid);
    $total_sales = $this->model_prepayment->get_total_deduction($shopid,$branchid);

    $data = array("success" => 1, "balance" => number_format($balance,2), "total_sales" => number_format($total_sales,2));
    generate_json($data);
  }

  public function export_shop_wallet_logs(){
    // $requestData = url_decode(json_decode($this->input->post("_search_logs")));
    // var_dump($requestData);
    // die();
    // $search = json_decode($requestData['searchValue']);

    $searchValue = $this->input->post('searchValue');
    $search = json_decode($searchValue);
    $shopid = en_dec('dec',$this->input->post('export_logs_shopid'));
    $branchid = en_dec('dec',$this->input->post('export_logs_branchid'));

    $search_val = ($search->search == "") ? "No Search":$search->search;
    $shop_name = ($search->shop == "") ? "All Shops":$this->Model_shops->get_shop_details($search->shop)->result_array()[0]['shopname'];
    $fromdate = ($search->from == "") ? date('Y-m-d'):$search->from;
    $todate = ($search->to == "") ? date('Y-m-d'):$search->to;

    $data = $this->model_prepayment->get_shop_wallet_logs_table_export($search,$shopid,$branchid);
    $shopname = $this->model->get_shopname($shopid);
    $branchname = $this->model->get_branchname($branchid);
    // print_r($data);
    // die();
    // echo json_encode($data);
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('B1', $shopname." (".$branchname.") Wallet logs");
    $sheet->setCellValue('B2', "Filter: $search_val;$shop_name");
    $sheet->setCellValue('B3', "$fromdate to $todate");

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('D')->setWidth(30);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(40);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(20);
    // $sheet->getColumnDimension('F')->setWidth(30);

    $sheet->setCellValue('A6', 'Deposit Ref No.');
    $sheet->setCellValue('B6', 'Deposit Type.');
    $sheet->setCellValue('C6', 'Transaction No.');
    $sheet->setCellValue('D6', 'Transaction Date');
    $sheet->setCellValue('E6', 'Transaction Type');
    $sheet->setCellValue('F6', 'Remarks');
    $sheet->setCellValue('G6', 'Transaction Amount');
    $sheet->setCellValue('H6', 'Wallet Amount');

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:H6')->getFont()->setBold(true);

    // print_r($data);
    // exit();
    $exceldata= array();
    foreach ($data as $key => $row) {

        $resultArray = array(
            '1' => $row['tran_depnum'],
            '2' => $row['tran_deptype'],
            '3' => $row['tran_num'],
            '4' => $row['tran_date'],
            '5' => $row['tran_type'],
            '6' => $row['tran_remarks'],
            '7' => $row['tran_amount'],
            '8' => $row['tran_balance'],
        );
        $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');
    $row_count = count($exceldata)+11;
    for ($i=7; $i < $row_count; $i++) {
        $sheet->getStyle("G$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'Pre-Payment ' . date('Y/m/d');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    ob_end_clean();

    $filterstr = "and no filters. ";
    if ($search->shop !== "" || $search->search !== "") {
      $filterstr = "with filters: $search_val in $shopname, $branchname ";
    }
    $filterstr .= "Dated $fromdate to $todate";
    $this->audittrail->logActivity('Prepayment wallet logs', $shopname." (".$branchname.") Prepayment Wallet logs has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));

    return $writer->save('php://output');
    exit();
  }

  public function index($token = ""){
    $this->isLoggedIn();
    //start - for restriction of views
    $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
    $main_nav_id = $this->views_restriction($content_url);
    //end - for restriction of views main_nav_id

    // start - data to be used for views
    $data_admin = array(
        'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $this->model_prepayment->get_shop_options(),
        'payments' => $this->model_prepayment->get_payment_type()
    );
    // end - data to be used for views

    // start - load all the views synchronously
    $this->load->view('includes/header', $data_admin);
    $this->load->view('wallet/prepayment', $data_admin);
    // end - load all the views synchronously
  }

  public function deposit(){
    $this->isLoggedIn();
    date_default_timezone_set('Asia/Manila');
    // $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
    // $main_nav_id = $this->views_restriction($content_url);

    $validation = array(
      array('shop','Shop','required'),
      array('type','Deposit type','required'),
      array('remarks','Remarks','max_length[100]'),
      array('deposit_amount','Amount','required'),
      array('deposit_ref_no', 'Deposit Reference Number', 'required')
    );

    foreach ($validation as $value) {
        $this->form_validation->set_rules($value[0],$value[1],$value[2]);
    }

    if($this->form_validation->run() === FALSE){
      $data['environment']  =  ENVIRONMENT;
      $data['success']      =  false;
      $data['message']      =  validation_errors();
      $data['csrf_name']    =  $this->security->get_csrf_token_name();
      $data['csrf_hash']    =  $this->security->get_csrf_hash();
      echo json_encode($data);
      exit();
    }

    try{

      $shop = sanitize($this->input->post('shop'));
      $branch = sanitize($this->input->post('branch'));
      $shopname = sanitize($this->input->post('shopname'));
      $branchname = sanitize($this->input->post('branchname'));
      $type = sanitize($this->input->post('type'));
      $deposit_amount = sanitize($this->input->post('deposit_amount'));
      $remarks = sanitize($this->input->post('remarks'));
      $deposit_ref_no = sanitize($this->input->post('deposit_ref_no'));
      $refnum = order_ref_prefix().rand(0,1000).round(microtime(true));

      $isExist = $this->model_prepayment->get_shop_wallet($shop,$branch);

      ### attachment ###
      $attachment = false;
      if(isset($_FILES['attachment'])){
        $this->makedirImage();
        $config['upload_path']       = 'assets/prepayment/';
        $config['allowed_types']     = 'jpeg|jpg|png';
        $config['max_size']          = 2048;
        $config['encrypt_name']      = true;

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('attachment')){
          $error = array('error' => $this->upload->display_errors());
          $data = array("success" => false, "message" => $error['error']);
          echo json_encode($data);
          exit();
        }else{
          $cdata = array('upload_data' => $this->upload->data());
          $attachment = $config['upload_path'].$cdata['upload_data']['file_name'];

          ///upload image to s3 bucket
          $fileTempName    = $_FILES['attachment']['tmp_name'];
          $activityContent = 'assets/prepayment/'.$cdata['upload_data']['file_name'];
          $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

          if($uploadS3 != 1){
              $response = [
                  'environment' => ENVIRONMENT,
                  'success'     => false,
                  'message'     => 'S3 Bucket upload failed.'
              ];

              echo json_encode($response);
              die();
          }

          unlink($config['upload_path'].$cdata['upload_data']['file_name']);

        }
      }

      // if shop has no wallet
      if($isExist->num_rows() == 0){
        $user_wallet_data = array(
          "shopid" => $shop,
          "branchid" => $branch,
          "refnum" => $refnum,
          "balance" => $deposit_amount,
          "deposit_date" => today(),
          "updated_at" => todaytime(),
          "created_at" => todaytime()
        );

        $saved = $this->model_prepayment->set_wallet($user_wallet_data);
      }

      // if shop already has wallet
      if($isExist->num_rows() > 0){
        $saved = $this->model_prepayment->update_wallet_balance($deposit_amount,$shop,$branch);
        // $remarks_updated = $this->model_prepayment->update_wallet_remarks($shop,$branch,"<p> - ".$remarks."</p>");
      }

      if($saved === false){
        $data = array("success" => false, "message" => "Deposit failed. Please try again.");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }


      date_default_timezone_set('Asia/Manila');
      $tran_ref_no = rand(100000,999999);
      $tran_ref_no = date('ymd').$tran_ref_no;
      while($this->model_prepayment->get_wallet_log_tran_refno($tran_ref_no)->num_rows() > 0){
        $tran_ref_no = rand(1000000,9999999);
        $tran_ref_no = date('ymd').$tran_ref_no;
      }



      if($attachment == false){
        $attachment = '';
      }

      $wallet = $this->model_prepayment->get_shop_wallet($shop,$branch)->row();
      $wallet_logs_data = array(
        "shopid" => $shop,
        "branchid" => $branch,
        "refnum" => $wallet->refnum,
        "tran_ref_num" => $tran_ref_no,
        "deposit_ref_num" => $deposit_ref_no,
        "attachment" => $attachment,
        "remarks" => $remarks,
        "logs_date" => today(),
        "logs_type" => $type,
        "type" => 'plus',
        "amount" => $deposit_amount,
        "balance" => $wallet->balance,
        "created_at" => todaytime()
      );

      $inserted_logs = $this->model_prepayment->set_wallet_logs($wallet_logs_data);
      if($inserted_logs === false){
        $data = array("success" => false, "message" => "Unable to record deposit logs");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        echo json_encode($data);
        exit();
      }

      $data = array("success" => true, "message" => "Deposit successful");
      $dep_amount = number_format($deposit_amount,2);
      $this->audittrail->logActivity('Prepayment', "Php $dep_amount has been deposited to $type of $shopname ($branchname) by successfully.", 'add', $this->session->userdata('username'));
      echo json_encode($data);

    } catch(Exception $e){
      $data = array(
          'success'     => "error",
          'message'     => $e->message(),
          'environment' => ENVIRONMENT
      );
      echo json_encode($data);

    }
  }

  public function makedirImage(){
    if (!is_dir('./assets/')) {
        mkdir('./assets/', 0777, TRUE);
    }
    if (!is_dir('./assets/prepayment/')) {
        mkdir('./assets/prepayment/', 0777, TRUE);
    }
  }

  // public function deduction(){
  //   $shop_w_wallet = $this->model_prepayment->get_allshop_wallet();
  //   $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
  //   if($shop_w_wallet->num_rows() > 0){
  //     // start cron logs
  //     $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron for wallet deduction', 'For wallet deduction on all shop with pre payment'));
  //
  //     $logs_batch = array();
  //     $bill_batch = array();
  //     foreach($shop_w_wallet->result() as $shop){
  //       $shop_wallet = $shop->balance;
  //       $billings = $this->model_prepayment->get_billing_for_shop($shop->shopid);
  //       if($shop_wallet > 0 && $billings->num_rows() > 0){
  //         foreach($billings->result() as $bill){
  //           $deducted = $this->model_prepayment->update_wallet_balance_deduct($bill->netamount,$shop->shopid);
  //           $balance = $this->model_prepayment->get_shop_wallet($shop->shopid)->row()->balance;
  //           if($deducted == true){
  //             $billing_data = array(
  //               "paystatus" => "Settled",
  //               "paiddate" => todaytime(),
  //               "paidamount" => $bill->netamount,
  //               "paytype" => 7,
  //               "payref" => $shop->refnum,
  //               "id" => $bill->id
  //             );
  //
  //             $logs_data = array(
  //               "shopid" => $shop->shopid,
  //               "refnum" => $bill->billcode,
  //               "logs_date" => today(),
  //               "logs_type" => 'wallet',
  //               "amount" => $bill->netamount,
  //               "balance" => $balance,
  //               "type" => 'minus',
  //               "created_at" => todaytime()
  //             );
  //
  //             $bill_batch[] = $billing_data;
  //             $logs_batch[] = $logs_data;
  //           }
  //         }
  //       }
  //     }
  //
  //     if(count((array)$bill_batch) > 0){
  //       $this->model_prepayment->update_billings_batch($bill_batch);
  //     }
  //
  //     if(count((array)$logs_batch) > 0){
  //       $this->model_prepayment->set_wallet_logs_batch($logs_batch);
  //     }
  //
  //     // End of cron logs
  //     $cron_status = ($cron_id != '') ? 'successful' : 'failed';
  //     $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);
  //
  //   }
  // }

  public function deduction($trandate){
    $shop_w_wallet = $this->model_prepayment->get_allshop_wallet();
    // $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    if($shop_w_wallet->num_rows() > 0){
      // start cron logs
      $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron for wallet deduction', 'For wallet deduction on all shop with pre payment'));

      $logs_batch = array();
      $bill_batch = array();
      foreach($shop_w_wallet->result() as $shop){
        $shop_wallet = $shop->balance;
        $billings = $this->model_prepayment->get_billing_for_shop($shop->shopid,$trandate,$shop->branchid);
        // die();
        if($shop_wallet > 0 && $billings->num_rows() > 0){
          foreach($billings->result() as $bill){
            $deducted = $this->model_prepayment->update_wallet_balance_deduct($bill->total_deduct,$shop->shopid,$shop->branchid);
            $balance = $this->model_prepayment->get_shop_wallet($shop->shopid,$shop->branchid)->row()->balance;
            if($deducted == true){
              $billing_data = array(
                "paystatus" => "Settled",
                "paiddate" => todaytime(),
                "paidamount" => $bill->total_deduct,
                "paytype" => 9,
                "payref" => $shop->refnum,
                "id" => $bill->id
              );

              date_default_timezone_set('Asia/Manila');
              $tran_ref_no = rand(100000,999999);
              $tran_ref_no = date('ymd').$tran_ref_no;
              while($this->model_prepayment->get_wallet_log_tran_refno($tran_ref_no)->num_rows() > 0){
                $tran_ref_no = rand(1000000,9999999);
                $tran_ref_no = date('ymd').$tran_ref_no;
              }

              $logs_data = array(
                "shopid" => $shop->shopid,
                "branchid" => $shop->branchid,
                "refnum" => $bill->billcode,
                "tran_ref_num" => $tran_ref_no,
                "logs_date" => today(),
                "logs_type" => 'wallet',
                "amount" => $bill->total_deduct,
                "balance" => $balance,
                "type" => 'minus',
                "created_at" => todaytime()
              );

              $bill_batch[] = $billing_data;
              $logs_batch[] = $logs_data;

              if(floatval($shop->threshold_amt) > floatval($balance)){
                $branchname = "Main";
                if($shop->branchid != 0){
                  $branchname = $this->model_prepayment->get_branch($shop->branchid)->row()->branchname;
                }
                $email_data = array();
                $email_data['balance'] = floatval($balance);
                $email_data['shopname'] = $shop->shopname;
                $email_data['branchname'] = $branchname;
                $email_data['threshold'] = floatval($shop->threshold_amt);
                $this->send_email("prepayment_email_send",$email_data);
              }
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

  public function authentication_password(){
    $this->isLoggedIn();
    $c_password = $this->input->post('c_password');
    $username = $this->session->username;
    if(empty($c_password)){
      $data = array("success" => 0, "message" => "Password authentication failed. Please try again");
      generate_json($data);
      exit();
    }

    $validate_username = $this->model->validate_username($username);
    if($validate_username->num_rows() == 0){
      $data = array("success" => 0, "message" => "Password authentication failed. Invalid User");
      generate_json($data);
      exit();
    }

    $userObj = $validate_username->row();
    $hash_password = $userObj->password;
    if(password_verify($c_password,$hash_password)){
      $data = array("success" => 1, "message" => "Password authentication successful");
      generate_json($data);
    }else{
      $data = array("success" => 0, "message" => "Password authentication failed. Wrong password");
      generate_json($data);
    }

  }

  public function get_shop_branches(){
    $shopid = sanitize($this->input->post('shopid'));
    $branches = $this->model_prepayment->get_shop_branches($shopid);
    if($branches->num_rows() == 0){
      $data = array("success" => 0, "message" => "No available branch");
      generate_json($data);
      exit();
    }

    $branches = $branches->result_array();
    $data = array("success" => 1, "branches" => $branches);
    generate_json($data);
  }

  function send_email($function, $data, $orderStatus="", $paypandaRef="", $shop="", $shopCode="") {

      if(get_apiserver_link() != "" || get_apiserver_link() != null)
        $url = get_apiserver_link()."api/Emails/".$function;
      else
        $url = base_url()."api/Emails/".$function."/";

      //post parameters
      $fields = array(
        'data'   => $data,
        'signature' => en_dec('en',$this->email_signature_key())
      );
      //build post parameters
      $postvars = http_build_query($fields);

      // open connection
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
      curl_setopt($ch, CURLOPT_USERAGENT, 'api');
      curl_setopt($ch, CURLOPT_TIMEOUT, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
      curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

      // execute post
      curl_exec($ch);
        // die($server_output);

      //close connection
      curl_close($ch);
    }

}
