<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Billing extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('accounts/model_billing');
    $this->load->model('wallet/model_prepayment');
    $this->load->model('wallet/Model_shops');
  }

  public function pb_totalsales_api_url(){
    if (ENVIRONMENT == "production") {
        return "http://35.173.0.77/dev/pb_dynamic/";
    }else if (ENVIRONMENT == "testing") {
        return "http://35.173.0.77/dev/pb_dynamic/";
    }else{
        return "http://35.173.0.77/dev/pb_dynamic/";
    }
  }

  public function pb_totalsales_authkey(){
    if (ENVIRONMENT == "production") {
      return "Basic dGVzdDE6MTIzNDU2";
    }else if (ENVIRONMENT == "testing") {
      return "Basic dGVzdDE6MTIzNDU2";
    }else{
      return "Basic dGVzdDE6MTIzNDU2";
    }

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

  ///////////////////////////////////////////////////////////////////////////
  //////////  BILLING BREAKDOWN START
  ///////////////////////////////////////////////////////////////////////////

  public function get_billing_table(){
    $requestData = json_decode(json_encode($_REQUEST));
    $search = json_decode($requestData->searchValue);
    $data = $this->model_billing->get_billing_table($search, $requestData);
    // $filtered = false;
    // $shop = ($search->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($search->shop)->result_array()[0]['shopname'];
    // $status = $search->status;
    // $types = ["1" => "All Status", "2" => "On Process", "3" => "Settled"];
    // $filterstr = "";
    // if ($search->status != 1 || $search->shop != "") {
    //   $filtered = true;
    //   $filterstr = "filtered with: $types[$status]";
    //   $filterstr .= ($shop == "") ? " in All shops" : " in $shop, ";
    // }
    // $filterstr .= "Dated from $search->from to $search->to";
    // $this->audittrail->logActivity('Billing', "Viewed a total of ".$data['recordsTotal']." record(s) $filterstr.", ($filtered) ? 'search':'view', $this->session->userdata('username'));
    echo json_encode($data);
  }

  public function export_billing_tbl(){
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $requestData = json_decode($this->input->post('_search'));
    // print_r($requestData);
    // exit();
    $search = json_decode($requestData->searchValue);
    $billing_res = $this->model_billing->get_billing_table($search, $requestData, true);

    $types = ["1" => "All Status", "2" => "On Process", "3" => "Settled"];
    $status = $search->status;
    $shop = ($search->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($search->shop)->result_array()[0]['shopname'];

    $sheet->setCellValue('B1', 'Accounts > Billing');
    $sheet->setCellValue('B2', "Filters: $types[$status] in $shop");
    $sheet->setCellValue('B3', $search->from.' - '.$search->to);

    $shop_id = $this->session->userdata('sys_shop_id');

    if($shop_id == 0){
      $sheet->getColumnDimension('A')->setWidth(20);
      $sheet->getColumnDimension('B')->setWidth(30);
      $sheet->getColumnDimension('C')->setWidth(20);
      $sheet->getColumnDimension('D')->setWidth(20);
      $sheet->getColumnDimension('E')->setWidth(20);
      $sheet->getColumnDimension('F')->setWidth(20);
      //$sheet->getColumnDimension('G')->setWidth(30);

      $sheet->setCellValue('A6', 'Date');
      $sheet->setCellValue('B6', 'Billing Code');
      $sheet->setCellValue('C6', 'Total Amount');
      //$sheet->setCellValue('D6', 'Refcom Total Amount');
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
          //'4' => $row[3],
          '4' => $row[4],
          '5' => $row[5],
          '6' => ucwords($row[6])
        );
        $exceldata[] = $resultArray;
      } 

      $sheet->fromArray($exceldata, null, 'A7');
      $row_count = count($exceldata)+7;
      for ($i=7; $i < $row_count; $i++) {
        $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      }
    }

    else{
      $sheet->getColumnDimension('A')->setWidth(20);
      $sheet->getColumnDimension('B')->setWidth(30);
      $sheet->getColumnDimension('C')->setWidth(20);
      $sheet->getColumnDimension('D')->setWidth(20);
      $sheet->getColumnDimension('E')->setWidth(20);
      $sheet->getColumnDimension('F')->setWidth(20);
      $sheet->getColumnDimension('G')->setWidth(30);

      $sheet->setCellValue('A6', 'Date');
      $sheet->setCellValue('B6', 'Billing Code');
      $sheet->setCellValue('C6', 'Total Amount');
      $sheet->setCellValue('D6', 'Refcom Total Amount');
      $sheet->setCellValue('E6', 'Processing Fee');
      $sheet->setCellValue('F6', 'Net Amount');
      $sheet->setCellValue('G6', 'Shop');

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
          '6' => $row[5],
          '7' => ucwords($row[6])
        );
        $exceldata[] = $resultArray;
      } 

      $sheet->fromArray($exceldata, null, 'A7');
      $row_count = count($exceldata)+7;
      for ($i=7; $i < $row_count; $i++) {
        $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      }
    }
    

    $writer = new Xlsx($spreadsheet);
    $filename = 'Accounts Billing';
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
    $this->audittrail->logActivity('Billing', "Billing has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
    // echo json_encode($data);
  } 

  public function get_billing_government_table(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_billing->get_billing_government_table($search);
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
    $netamount_w_shipping = $this->input->post('netamount_w_shipping');
    $delivery_amount = $this->input->post('delivery_amount');
    $total_comrate = $this->input->post('total_comrate');

    if(ini() == "toktokmall"){
      $data = $this->model_billing->get_billing_breakdown_toktokmall($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount_w_shipping,$delivery_amount,$total_comrate);
    }else{
      $data = $this->model_billing->get_billing_breakdown_foodhub($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount_w_shipping,$delivery_amount,$total_comrate);
    }
    echo json_encode($data);
  }

  public function get_billing_logs(){
    $order_id = en_dec('dec',$this->input->post('order_id'));
    $totalamount = $this->input->post('totalamount');
    $processfee = $this->input->post('processfee');
    $netamount = $this->input->post('netamount');
    if(ini() == "toktokmall"){
      $total_comrate = $this->input->post('total_comrate');
      $data = $this->model_billing->get_billing_logs_toktokmall($order_id,$totalamount,$processfee,$netamount,$total_comrate);
    }else{
      $data = $this->model_billing->get_billing_logs($order_id,$totalamount,$processfee,$netamount);
    }
    if($data){
      $remarks = "Viewed logs of ".$order_id;
      $this->audittrail->logActivity('Billling (By Payment Portal Fee)', $remarks, 'view', $this->session->userdata('username'));
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

    $data = $this->model_billing->get_billing_government_breakdown_table($search,$shop,$trandate,$portal_fee,$totalamount,$processfee,$netamount,$delivery_amount);
    echo json_encode($data);
  }

  public function get_billing_branch_tbl(){
    $search = $this->input->post('searchValue');
    $id = $this->input->post('shopid');
    $trandate = $this->input->post('trandate');
    $data = $this->model_billing->get_billing_branch_tbl($search,$id,$trandate);
    echo json_encode($data);
  }

  public function get_billing_branch_logs(){
    $branchid = en_dec('dec',$this->input->post('branchid'));
    $trandate = $this->input->post('trandate');
    $totalamount = $this->input->post('totalamount');
    $processfee = $this->input->post('processfee');
    $netamount = $this->input->post('netamount');
    $data = $this->model_billing->get_billing_branch_logs($branchid,$trandate,$totalamount,$processfee,$netamount);
    echo json_encode($data);
  }

  public function get_billing_branch_government_tbl(){
    $search = $this->input->post('searchValue');
    $id = $this->input->post('shopid');
    $trandate = $this->input->post('trandate');
    $data = $this->model_billing->get_billing_branch_government_tbl($search,$id,$trandate);
    echo json_encode($data);
  }

  public function get_billing(){
    $this->isLoggedIn();
    $id = $this->input->post('id');
    $bilcod = $this->input->post('bilcod');
    $row = $this->model_billing->get_billing($id);
    if($row->num_rows() == 0){
      $data = array("success" => false, "message" => "Something went wrong. Please try again");
      echo json_encode($data);
      exit();
    }

    $data = array("success" => true, "message" => $row->row_array());
    $this->audittrail->logActivity('Billing', "Viewed a total of ".$row->num_rows()." logs on billing # $bilcod.", 'view', $this->session->userdata('username'));
    echo json_encode($data);
  }

  public function get_billing_government(){
    $this->isLoggedIn();
    $id = $this->input->post('id');
    $row = $this->model_billing->get_billing_government($id);
    if($row->num_rows() == 0){
      $data = array("success" => false, "message" => "Something went wrong. Please try again");
      echo json_encode($data);
      exit();
    }

    $data = array("success" => true, "message" => $row->row_array());
    echo json_encode($data);
  }

  ///////////////////////////////////////////////////////////////////////////
  //////////  BILLING BREAKDOWN END
  ///////////////////////////////////////////////////////////////////////////

  public function index($token = "",$billcode = ""){
    $this->isLoggedIn();
    //start - for restriction of views
    $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
    $main_nav_id = $this->views_restriction($content_url);
    //end - for restriction of views main_nav_id

    // start - data to be used for views
    $data_admin = array(
        'token' => $token,
        'main_nav_id' => $main_nav_id, //for highlight the navigation,
        'shops' => $this->model_billing->get_shop_options(),
        'payments' => $this->model_billing->get_options(),
        'billcode' => $billcode
    );
    // end - data to be used for views

    // start - load all the views synchronously
    $this->load->view('includes/header', $data_admin);
    $this->load->view('accounts/billing', $data_admin);
    // end - load all the views synchronously
  }

  public function get_shop_branches(){
    $shop = en_dec('dec',$this->input->post('shop'));
    if($shop == ""){
      $data = array("success" => 0, "message" => "Unable to fetch any branch");
      generate_json($data);
      exit();
    }

    $branches = $this->model_billing->get_shop_branch($shop);
    if($branches->num_rows() == 0){
      $data = array("success" => 0, "message" => "Unable to fetch any branch");
      generate_json($data);
      exit();
    }

    $data = array("success" => 1, "branches" => $branches->result_array());
    generate_json($data);

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
        'shops' => $this->model_billing->get_shop_options(),
        'payments' => $this->model_billing->get_options()
    );
    // end - data to be used for views

    // start - load all the views synchronously
    $this->load->view('includes/header', $data_admin);
    $this->load->view('accounts/billing_government', $data_admin);
  }

  public function settleBilling(){
    $args = $this->input->post();
    if(isset($args['status']) && $args['status'] == 'Unsettled'){
      $unsettled = $this->model_billing->get_unsettled_amount($this->input->post('f_id-p'));
      $amount_to_pay = $unsettled['unsettled_amount'];
      $sys_shop = $unsettled['syshop'];
      $branch_id = $unsettled['branch_id'];
    }else{
      $amount_to_pay = $this->model_billing->get_amount_to_pay($this->input->post('f_id-p'))['netamount'];
    }
    // die($args['shopcode']);
    $cur_val = [
      'paytype'=> $args['f_payment'],
      'payref'=> $args['f_payment_ref_num'],
      'paidamount'=> $args['f_payment_fee'],
      'payremarks'=> $args['f_payment_notes'],
      'paiddate' => date('Y-m-d H:i:s'),
      'deposit_date' => $args['deposit_date'],
      'paystatus' => 'Settled'
    ];

    $prev_val = [
      'paytype'=> 0,
      'payref'=> '',
      'paidamount'=> '0.00',
      'payremarks'=> '',
      'paiddate' => '',
      'deposit_date' => '0000-00-00',
      'paystatus' => 'On Process'
    ];

    $response = [
        'environment' => ENVIRONMENT,
        'success'     => false,
        'message'     => $this->response->message('error'),
        'csrf_name'   => $this->security->get_csrf_token_name(),
        'csrf_hash'   => $this->security->get_csrf_hash(),
    ];

    // var_dump($amount_to_pay);
    // die();

    if($amount_to_pay != number($this->input->post('f_payment_fee'))) {
        $response['message'] = 'Payment Amount is not equal to total payable amount.';
        echo json_encode($response);
        die();
    }

    if($this->input->post('f_payment') != 'Others') {
        $validation = array(
            array('f_payment','Payment Type','required|max_length[5]|min_length[1]'),
            array('f_payment_ref_num','Payment Reference Number','required|max_length[50]')
            // array('f_payment_fee','Payment Amount','required|greater_than_equal_to['.floatval($amount_to_pay-1).']|less_than_equal_to['.floatval($amount_to_pay+1).']')
        );
    } else {
        $validation = array(
            array('f_payment_others','Payment Type','required|max_length[50]|min_length[2]'),
            array('f_payment_ref_num','Payment Reference Number','required|max_length[50]')
            // array('f_payment_fee','Payment Amount','required|greater_than_equal_to['.floatval($amount_to_pay-1).']|less_than_equal_to['.floatval($amount_to_pay+1).']')
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

        // print_r($_FILES['billing_attachment']);
        // die();

        ### billing_attachment ###
        $billing_attachment = false;
        $attachment_arr = array();
        $this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
        $uploads_path = './assets/billing_attachment/'.$args['shopcode'].'/'.date('M-Y').'/';
        $s3_uploads_path = 'assets/billing_attachment/'.$args['shopcode'].'/'.date('M-Y').'/';

        if (!is_dir($uploads_path)){
    			mkdir($uploads_path, 0777, true);
    		}

        if(isset($_FILES['billing_attachment'])){
          $config['upload_path']       = $uploads_path;
          $config['allowed_types']     = 'png|jpg|jpeg';
          $config['max_size']          = 2048;
          $config['encrypt_name']      = true;

          $this->load->library('upload', $config);

          $count = count($_FILES['billing_attachment']['name']);
          $upload_arr = array();
          for ($i=0; $i < $count; $i++) {
            $temp_arr = array();
            $temp_arr['name'] = $_FILES['billing_attachment']['name'][$i];
            $temp_arr['type'] = $_FILES['billing_attachment']['type'][$i];
            $temp_arr['tmp_name'] = $_FILES['billing_attachment']['tmp_name'][$i];
            $temp_arr['error'] = $_FILES['billing_attachment']['error'][$i];
            $temp_arr['size'] = $_FILES['billing_attachment']['size'][$i];
            $upload_arr[] = $temp_arr;
          }


          // die($config['upload_path']);
          // die('hello'.$this->upload->allowed_types);

          for ($i=0; $i < count($upload_arr); $i++) {
            $_FILES['tmp_upload'] = $upload_arr[$i];
            if(!$this->upload->do_upload('tmp_upload')){
                $error = array('error' => $this->upload->display_errors());
                $data = array("success" => false, "message" => $error['error']);
                echo json_encode($data);
                exit();
            }else{
              $cdata = array('upload_data' => $this->upload->data());
              $billing_attachment = $config['upload_path'].$cdata['upload_data']['file_name'];
            }

            if($billing_attachment == false){
              $billing_attachment = '';
            }

            if($billing_attachment != ''){
              $file_temp_name  = $_FILES['tmp_upload']['tmp_name'];
              $activityContent = $s3_uploads_path.$cdata['upload_data']['file_name'];
              // echo $activityContent;
              // echo $file_temp_name;
              // die();
              $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($file_temp_name, $activityContent);

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
              $attachment_arr[] = $activityContent;
            }
          }
        }


        $attachment = implode(",",$attachment_arr);
        if(isset($args['status']) && $args['status'] == 'Unsettled'){
          $update_data = [
            'paytype'=> $args['f_payment'],
            'payref'=> $args['f_payment_ref_num'],
            'paidamount'=> number($args['f_payment_fee']),
            'unsettled_payremarks'=> $args['f_payment_notes'],
            'paiddate' => date('Y-m-d H:i:s'),
            'paystatus' => 'Settled',
            'payattach' => $attachment,
            'remaining_to_pay' => 0
          ];

          date_default_timezone_set('Asia/Manila');
          $tran_ref_no = rand(100000,999999);
          $tran_ref_no = date('ymd').$tran_ref_no;
          while($this->model_prepayment->get_wallet_log_tran_refno($tran_ref_no)->num_rows() > 0){
            $tran_ref_no = rand(1000000,9999999);
            $tran_ref_no = date('ymd').$tran_ref_no;
          }

          $saved = $this->model_prepayment->update_wallet_balance($amount_to_pay,$sys_shop,$branch_id);
          $wallet = $this->model_prepayment->get_shop_wallet($sys_shop,$branch_id)->row();
          $wallet_logs_data = array(
            "shopid" => $sys_shop,
            "branchid" => $branch_id,
            "refnum" => trim($args['f_payment_ref_num']),
            "tran_ref_num" => $tran_ref_no,
            "deposit_ref_num" => $args['f_payment_ref_num'],
            "attachment" => $attachment,
            "logs_date" => today(),
            "logs_type" => 'wallet',
            "type" => 'plus',
            "amount" => $amount_to_pay,
            "balance" => $wallet->balance,
            "created_at" => todaytime()
          );

          $inserted_logs = $this->model_prepayment->set_wallet_logs($wallet_logs_data);
        }else{
          $update_data = [
            'paytype'=> $args['f_payment'],
            'payref'=> $args['f_payment_ref_num'],
            'paidamount'=> $args['f_payment_fee'],
            'payremarks'=> $args['f_payment_notes'],
            'paiddate' => date('Y-m-d H:i:s'),
            'deposit_date' => $args['deposit_date'],
            'paystatus' => 'Settled',
            'payattach' => $attachment
          ];
        }


        $success = $this->model_billing->tag_payment($update_data,$args['f_id-p']);
        $response['success'] = $success;
        $main = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val), $prev_val);
        $response['message'] = $this->response->message(($success ? 'success' : 'failed'), 'update', 'Billing');
        if ($success) {
          $this->audittrail->logActivity('Billing', "Billing # ".$args['f_id-p']." has been updated successfully. \nChanges:\n$main", 'update', $this->session->userdata('username'));
        }
    }
    echo json_encode($response);
  }

  public function settleBilling_portal_fee(){
    $amount_to_pay = $this->model_billing->get_amount_to_pay_portal_fee($this->input->post('f_id-p'))['netamount'];

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

        $success = $this->model_billing->tagPayment_portal_fee($this->input->post());
        $response['success'] = $success;
        $response['message'] = $this->response->message(($success ? 'success' : 'failed'), 'update', 'Billing');
    }
    echo json_encode($response);
  }

  public function shopandaUKey(){
			return "ShopandaKeyCloud3578";
	}

  ///////////////////////////////////////////////////////////////////////////
  //////////  BILLING PROCESS START
  ///////////////////////////////////////////////////////////////////////////

  public function processBilling(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processDailyMerchantPay($cronkey, $trandate);
  }

  public function billing_foodhubs(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processBilling_foodhubs($cronkey, $trandate);
  }

  public function billing_foodhub_confirmed(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processBilling_foodhub_confirmed($cronkey, $trandate);
  }

  public function billing_toktokmall(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processBilling_toktokmall($cronkey, $trandate);
  }

  public function billing_unsettled(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processBilling_unsettled($cronkey, $trandate);
  }

  public function billing_unsettled_confirmed(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processBilling_unsettled_confirmed($cronkey, $trandate);
  }

  public function process_billing_government(){
    $trandate = date('Y-m-d',strtotime(date('Y-m-d') . ' -1 day'));
    $cronkey = $this->shopandaUKey();
    $this->processdaily_merchant_pay_government($cronkey, $trandate);
  }

  public function processBilling_foodhub($cronkey,$trandate,$manual = 0){
    if($cronkey == $this->shopandaUKey())
    {
        $res = $this->model_billing->process_billing($trandate);
    }//cronkey checking close

    echo "DONE";
    $this->deduction($trandate);
  }

  public function processBilling_foodhub_confirmed($cronkey,$trandate,$manual = 0){
    if($cronkey == $this->shopandaUKey())
    {
        $res = $this->model_billing->process_billing_dateconfirmed($trandate);
    }//cronkey checking close

    echo "DONE";
    $this->deduction($trandate);
  }

  public function processBilling_toktokmall($cronkey,$trandate,$id = 0){
    if($cronkey == $this->shopandaUKey())
    {
        $res = $this->model_billing->process_billing_toktokmall($trandate,$id);
    }//cronkey checking close

    echo "DONE";
    $this->deduction($trandate);
    $this->process_billing_totalsales($this->shopandaUKey(),$trandate);
  }

  public function process_billing_totalsales($cronkey,$trandate){
    if($cronkey == $this->shopandaUKey()){
      $refcode = order_ref_prefix();
      $refcode = $refcode.str_replace("-","",$trandate);
      $refnum  = generate_merchant_id();
      $reference_num = $refcode.$refnum;
      $total_sales = 0;
      $total_comrate = 0;
      $total_fee = 0;

      $logs = $this->model_billing->get_totalsales_logs($refcode);
      while($this->model_billing->get_totalsales_logs($reference_num)->num_rows() > 0){
        $refnum = generate_merchant_id();
        $reference_num = $refcode.$refnum;
      }


      $billing = $this->model_billing->get_totalsales_billing($trandate);
      // print_r($billing);
      // die();
      if(count((array)$billing) > 0){
        $total_sales = floatval($billing['totalsales']) + floatval($billing['totalcomrate']);
        $total_comrate = floatval($billing['totalcomrate']);
        $total_fee = floatval($billing['totalsales']);
      }


      $cron_data = array();
      $cron_data['reference_code'] = $reference_num;
      $cron_data['amount'] = $total_sales;
      $cron_data['total_processing_revenue'] = $total_sales;
      $cron_data['referral_fee'] = $total_comrate;
      $cron_data['net_processing_revenue'] = $total_fee;
      $cron_data['date'] = todaytime();

      $insert_data = array();
      $insert_data['reference_num'] = $reference_num;
      $insert_data['totalsales'] = $total_sales;
      $insert_data['data'] = json_encode($cron_data);

      $update_id = $this->model_billing->set_totalsales_api_logs($insert_data);
      if(empty($update_id)){
        $data = array("success" => 0, "message" => "Unable to save api totalsales logs");
        generate_json($data);
        exit();
      }

      $ch = curl_init();
      $post_data = array(
        "reference_code" => $reference_num,
        "amount" => $total_sales
      );

      curl_setopt($ch, CURLOPT_URL, $this->pb_totalsales_api_url().'api/create_signature');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Authorization: '.$this->pb_totalsales_authkey().'')
      );
      $signature_server_output = curl_exec($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      $signature_result = json_decode($signature_server_output);
      if($http_status != 200){
        $signature_update_data = array(
          "response" => $signature_server_output,
          "date_updated" => todaytime()
        );

        $this->model_billing->update_totalsales_api($signature_update_data,$update_id);
        exit();
      }


      $signature = $signature_result->signature;
      $cron_data['signature'] = $signature;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->pb_totalsales_api_url().'api/sales-summary');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($cron_data));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Authorization: '.$this->pb_totalsales_authkey().'')
      );
      $insert_server_output = curl_exec($ch);
      curl_close($ch);

      $insert_update_data = array("response" => $insert_server_output);
      $this->model_billing->update_totalsales_api($insert_update_data,$update_id);

    }//cronkey checking close

  }

  public function processBilling_unsettled($cronkey,$trandate,$manual = 0){
    if($cronkey == $this->shopandaUKey())
    {
        $res = $this->model_billing->process_billing_process($trandate);
    }//cronkey checking close

    echo "DONE";
    $this->deduction($trandate);
  }

  public function processBilling_unsettled_confirmed($cronkey,$trandate,$manual = 0){
    if($cronkey == $this->shopandaUKey())
    {
        $res = $this->model_billing->process_billing_process_confirmed($trandate);
    }//cronkey checking close

    echo "DONE";
    $this->deduction($trandate);
  }

  public function processDailyMerchantPay($cronkey,$trandate,$manual = 0){
    // $cronkey = ($manual == '1') ? en_dec('dec',$cronkey) : $cronkey;
		if($cronkey == $this->shopandaUKey())
		{
				// $res = $this->model_billing->processDailyMerchantPay($trandate);
				$res = $this->model_billing->processDailyMerchantPay_per_product_rate($trandate);
				// $res = $this->model_billing->process_billing($trandate);
		}//cronkey checking close

		echo "DONE";
    $this->deduction($trandate);
	}

  public function processDailyMerchantPay_confirmed($cronkey,$trandate,$manual = 0){
    // $cronkey = ($manual == '1') ? en_dec('dec',$cronkey) : $cronkey;
		if($cronkey == $this->shopandaUKey())
		{
				// $res = $this->model_billing->processDailyMerchantPay($trandate);
				$res = $this->model_billing->processDailyMerchantPay_per_product_rate_confirmed($trandate);
		}//cronkey checking close

		echo "DONE";
    $this->deduction($trandate);
	}

  public function processDailyMerchantPay_old($cronkey,$trandate,$manual = 0){
    // $cronkey = ($manual == '1') ? en_dec('dec',$cronkey) : $cronkey;
		if($cronkey == $this->shopandaUKey())
		{
				$res = $this->model_billing->processDailyMerchantPay($trandate);
				// $res = $this->model_billing->processDailyMerchantPay_per_product_rate($trandate);
		}//cronkey checking close

		echo "DONE";
    $this->deduction($trandate);
	}

  public function processdaily_merchant_pay_government($cronkey,$trandate,$manual = 0){
		if($cronkey == $this->shopandaUKey())
		{
				$res = $this->model_billing->processdaily_merchant_pay_government($trandate);
		}//cronkey checking close

		echo "DONE";
	}

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
            $balance = $this->model_prepayment->get_shop_wallet($shop->shopid,$shop->branchid)->row()->balance;
            if($bill->total_deduct > $balance){
              $billing_data = array(
                "paystatus" => "Unsettled",
                "paiddate" => todaytime(),
                "paid_prepayment" => $balance,
                "remaining_to_pay" => floatval($bill->total_deduct) - floatval($balance),
                "prepaymentpaid_date" => todaytime(),
                "paytype" => 9,
                "unsettled_payref" => $bill->billcode,
                "id" => $bill->id
              );
            }else{
              $billing_data = array(
                "paystatus" => "Settled",
                "paiddate" => todaytime(),
                "paidamount" => $bill->total_deduct,
                "paytype" => 9,
                "payref" => $bill->billcode,
                "id" => $bill->id
              );

            }

            $balance = ($balance - $bill->total_deduct);
            $deducted = $this->model_prepayment->update_wallet_balance_deduct($bill->total_deduct,$shop->shopid,$shop->branchid);
            $total_deduction = $this->model_prepayment->get_total_deduction($shop->shopid,$shop->branchid);
            $total_deduction += floatval($bill->total_deduct);
            $total_deposit = $this->model_prepayment->get_total_deposit($shop->shopid,$shop->branchid);
            if($deducted == true){

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

              if(floatval($shop->threshold_amt) > floatval($balance) && $billings->num_rows() > 0){
                $branchname = "Main";
                $merchant_email = $shop->merchant_email;
                if($shop->branchid != 0){
                  // $branchname = $this->model_prepayment->get_branch($shop->branchid)->row()->branchname;
                  $branchname = $shop->branchname;
                  $merchant_email = $shop->branch_merchant_email;
                }
                $email_data = array();
                $email_data['balance'] = floatval($balance);
                $email_data['total_deduction'] = floatval($total_deduction);
                $email_data['total_deposit'] = floatval($total_deposit);
                $email_data['shopname'] = $shop->shopname;
                $email_data['branchname'] = $branchname;
                $email_data['threshold'] = floatval($shop->threshold_amt);
                $email_data['merchant_email'] = $merchant_email;
                $this->send_email("prepayment_email_send",$email_data);
                $this->send_email("prepayment_merchant_email_send",$email_data);
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

  public function delete_billing(){
    $delete_id = en_dec('dec',$this->input->post('delete_id'));
    $delete_shopid = en_dec('dec',$this->input->post('delete_shopid'));
    $delete_branchid = en_dec('dec',$this->input->post('delete_branchid'));
    $delete_trandate = $this->input->post('delete_trandate');
    $delete_billcode = $this->input->post('delete_billcode');
    $delete_payref = $this->input->post('delete_payref');
    $delete_unsettled_payref = $this->input->post('delete_unsettled_payref');

    // var_dump($delete_id);
    // var_dump($delete_shopid);
    // var_dump($delete_branchid);
    // var_dump($delete_trandate);
    // var_dump($delete_billcode);
    // die();

    if(empty($delete_id) || empty($delete_shopid) || empty($delete_trandate) || empty($delete_billcode)){
      $data = array("success" => 0, "message" => "Unable to delete billing . Try to reload and try again.");
      generate_json($data);
      exit();
    }

    $isExist = $this->model_billing->get_billing($delete_id);
    if($isExist->num_rows() == 0){
      $data = array("success" => 0, "message" => "Billing does not exist. Please try again.");
      generate_json($data);
      exit();
    }

    $billing_deleted = $this->model_billing->delete_billing($delete_id);
    $billing_logs_deleted = $this->model_billing->delelete_billing_logs($delete_shopid,$delete_branchid,$delete_trandate);
    if($billing_deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete billing.");
      generate_json($data);
      exit();
    }

    if($billing_logs_deleted === false){
      $data = array("success" => 0, "message" => "Unable to delete billing logs");
      generate_json($data);
      exit();
    }

    $shop_wallet = $this->model_prepayment->get_shop_wallet($delete_shopid,$delete_branchid);
    $shop_wallet_logs = $this->model_prepayment->get_wallet_logs_thru_refnum($delete_billcode);
    if($shop_wallet->num_rows() > 0 && $shop_wallet_logs->num_rows() > 0){
      $wallet_logs_updated = $this->model_billing->delete_wallet_logs($delete_billcode,$delete_shopid,$delete_branchid,$delete_payref,$delete_unsettled_payref);
      if($wallet_logs_updated === false){
        $data = array("success" => 0, "message" => "Unable to update wallet logs");
        generate_json($data);
        exit();
      }
    }

    $check_billings = $this->model_billing->get_billing_thru_trandate($delete_trandate);
    if($check_billings->num_rows() == 0){
      $this->model_billing->delete_process_billing($delete_trandate);
    }

    $data = array("success" => 1, "message" => "Billing deleted successfully");
    generate_json($data);

  }

  ///////////////////////////////////////////////////////////////////////////
  //////////  BILLING PROCESS END
  ///////////////////////////////////////////////////////////////////////////

  public function print_breakdown($billing_id){
    $billing_id = en_dec('dec',$billing_id);
    if(empty($billing_id)){
      $data = array("success" => 0, "message" => "Unable to print breakdown. Invalid billing ID");
      generate_json($data);
      exit();
    }

    $billing_query = $this->model_billing->get_billing($billing_id);
    if($billing_query->num_rows() > 0){
      $pdf_data = array();

      // BILLING DETAILS
      $billing                            = $billing_query->row_array();
      $pdf_data['billing_billno']         = $billing['billno'];
      $pdf_data['billing_billcode']       = $billing['billcode'];
      $pdf_data['billing_trandate']       = $billing['trandate'];
      $pdf_data['billing_shop']           = $billing['syshop'];
      $pdf_data['billing_branch']         = $billing['branch_id'];
      $pdf_data['billing_deliveryamount'] = $billing['delivery_amount'];
      $pdf_data['billing_totalamount']    = $billing['totalamount'];
      $pdf_data['billing_totalcomrate']   = $billing['totalcomrate'];
      $pdf_data['billing_voucher_amount'] = $billing['voucher_amount'];
      $pdf_data['billing_processfee']     = $billing['processfee'];
      $pdf_data['billing_netamount']      = $billing['netamount'];
      $pdf_data['billing_paystatus']      = $billing['paystatus'];
      $pdf_data['billing_paiddate']       = $billing['paiddate'];
      $pdf_data['billing_paidamount']     = $billing['paidamount'];
      $pdf_data['billing_payref']         = $billing['payref'];
      $pdf_data['billing_payremarks']     = $billing['payremarks'];

      // SHOP AND BANK DETAILS
      $shop_query  = $this->model_billing->get_shop_n_branch($pdf_data['billing_shop'],$pdf_data['billing_branch']);
      $bank_query  = $this->model_billing->get_shop_bankaccount($pdf_data['billing_shop'],$pdf_data['billing_branch']);
      $pdf_data['shopname']    = "-----";
      $pdf_data['branchname']  = "(Main)";
      $pdf_data['accountno']   = "-----";
      $pdf_data['accountname'] = "-----";
      $pdf_data['bankname']    = "-----";

      if($shop_query->num_rows() > 0){
        $shop_details = $shop_query->row_array();
        $pdf_data['shopname']   = $shop_details['shopname'];
        $pdf_data['branchname'] = ($shop_details['branchname'] != null) ? '('.$shop_details['branchname'].')' : '(Main)';
      }

      if($bank_query->num_rows() > 0){
        $bank_details = $bank_query->row_array();
        $pdf_data['accountno']   = $bank_details['accountno'];
        $pdf_data['accountname'] = $bank_details['accountname'];
        $pdf_data['bankname']    = $bank_details['bankname'];
      }

      if(ini() == "jcww"){
        $pdf_data['billing_breakdown'] = $this->model_billing->get_billing_breakdown_pdf($pdf_data['billing_shop'],$pdf_data['billing_branch'],$pdf_data['billing_trandate']);
      }

      // print_r($pdf_data['billing_breakdown']);
      // die();

      $page = $this->load->view('accounts/billing_print', $pdf_data, true);
      $this->load->library('Pdf');
      $obj_pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
      $obj_pdf->SetCreator(PDF_CREATOR);
      $obj_pdf->SetTitle("Billing for ".$pdf_data['shopname']." ".$pdf_data['branchname']." ".$pdf_data['billing_trandate']);
      $obj_pdf->SetDefaultMonospacedFont('helvetica');
      $obj_pdf->SetFont('helvetica', '', 9);
      $obj_pdf->setFontSubsetting(false);
      $obj_pdf->setPrintHeader(false);
      $obj_pdf->AddPage('L');
      ob_start();

      $obj_pdf->writeHTML($page, true, false, true, false, '');
      ob_clean();
      $obj_pdf->Output("Billing for ".$pdf_data['shopname']." ".$pdf_data['branchname']." ".$pdf_data['billing_trandate'].".pdf", 'I');
      $this->audittrail->logActivity('Billing', $this->session->userdata('username').' printed billingcode #'.$pdf_data['billing_billcode'], 'Print', $this->session->userdata('username'));

    }

  }

}
