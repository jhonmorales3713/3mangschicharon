<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Manual_order extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('orders/model_manual_order');
    $this->load->model('orders/model_orders');
    $this->load->model('wallet/Model_shops');
    $this->load->library('form_validation');
  }

  public function jc_comrate_hashkey(){
    switch (ENVIRONMENT) {
        case 'development':
          return "TRIAL123";
        break;
        case 'testing':
          return "TRIAL123";
        break;
        case 'production':
          return "JCFHHASH202102#02!!";
        break;
    }
  }

  protected function jc_disc_idno_hash(){
      if (ENVIRONMENT == "production") {
          return "TEST4";
      }else if (ENVIRONMENT == "testing") {
          return "TEST4";
      }else{
          return "TEST4";
      }
    }

  public function jcww_validate_reseller_link(){
    switch (ENVIRONMENT) {
        case 'development':
          return 'https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/discount_of_idno_toktok';
        break;
        case 'testing':
          return 'https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/discount_of_idno_toktok';
        break;
        case 'production':
          return 'https://thedarkhorse.ph/tdh/api/JCReferralAPI/discount_of_idno_toktok';
        break;
    }
  }

  protected function jc_validate_resellerid_url(){
    if (ENVIRONMENT == "production") {
        return "https://comm.toktokmall.ph/discount_idno";
    }else if (ENVIRONMENT == "testing") {
        return "http://13.251.19.26/discount_idno";
    }else{
        return "http://13.251.19.26/discount_idno";
    }
  }

  public function isLoggedIn() {
      if($this->session->userdata('isLoggedIn') == false) {
          header("location:".base_url('Main/logout'));
      }
  }

  public function list_table($token = ""){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_manual_order->list_table($search,$token,$_REQUEST);
    echo json_encode($data);
  }

  public function export_list_table(){
    $requestData = url_decode(json_decode($this->input->post('_search')));
    $search = json_decode($requestData['searchValue']);

    $search_val = ($search->search == "") ? "No Search":$search->search;
    $shop_id = ($search->shop == "") ? "All Shops":$search->shop;
    $fromdate = ($search->from == "") ? "All":$search->from . " to";
    $todate = ($search->to == "") ? "Records":$search->to;
    $shop_name = ($search->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($shop_id)->result_array()[0]['shopname'];

    $data = $this->model_manual_order->list_table($search,'', $requestData, true);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('B1', "Manual Order");
    $sheet->setCellValue('B2', "Filter: $search_val;$shop_name");
    $sheet->setCellValue('B3', "$fromdate $todate");

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->getColumnDimension('F')->setWidth(15);

    $sheet->setCellValue('A6', 'Date Ordered');
    $sheet->setCellValue('B6', 'Reference No.');
    $sheet->setCellValue('C6', 'Customer');
    $sheet->setCellValue('D6', 'Amount');
    $sheet->setCellValue('E6', 'Shipping');
    $sheet->setCellValue('F6', 'Total');

    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:F6')->getFont()->setBold(true);

    // print_r($data['data']);
    // exit();
    $exceldata= array();
    foreach ($data['data'] as $key => $row) {

        $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[5],
            '6' => $row[6],
        );
        $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');
    $row_count = count($exceldata)+7;
    for ($i=7; $i < $row_count; $i++) {
      $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("F$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'Manual Order ' . date('Y/m/d');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $filterstr = "and no filters. ";
    if ($search->shop !== "" || $search->search !== "") {
      $filterstr = "with filters: $search_val in $shop_name, ";
    }
    $filterstr .= "Dated $fromdate $todate";
    $this->audittrail->logActivity('Manual Order', "Manual order has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
    // print_r($data['data']);
    // echo json_encode($data);
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

  public function index($token = ""){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['manualorder_list']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id

      // start - data to be used for views
      $data_admin = array(
          'token' => $token,
          'main_nav_id' => $main_nav_id, //for highlight the navigation,
          'shops' => $this->model_manual_order->get_shop_options(),
          'cities' => $this->model_manual_order->get_cities(),
          'regions'             => $this->model_orders->get_regions()->result_array(),
          'provinces'           => $this->model_orders->get_provinces()->result_array(),
          'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
          // 'payments' => $this->model_billing->get_options()
      );
      // end - data to be used for views

      // start - load all the views synchronously
      $this->load->view('includes/header', $data_admin);
      $this->load->view('orders/manual_order', $data_admin);
    }else{
      $this->load->view('error_404');
    }

  }

  public function get_shop_order_details(){
    $this->isLoggedIn();
    try{
      $shopid = $this->input->post('shopid');
      if(empty($shopid)){
        $data = array("success" => 0, "message" => "Something went wrong. Please try again");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        generate_json($data);
        exit();
      }

      $branches = $this->model_manual_order->get_shop_branches($shopid);
      $products = $this->model_manual_order->get_shop_products($shopid);
      if($products->num_rows() == 0){
        $data = array("success" => 0, "message" => "No available products.");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        generate_json($data);
        exit();
      }

      $products = $products->result_array();
      if(isset($this->session->disrate) && count((array)$this->session->disrate) > 0 && $this->session->authenticated_seller == 1){
        $distributor_rate = $this->session->disrate;
        if(ini() == "toktokmall"){
          $run_pids = array();
          // print('<pre>'.print_r($distributor_rate,true).'</pre>');
          // die();
          if(count((array)$products) > 0){
            foreach ($products as $key1 => $value1) {
              $rkey = array_search($products[$key1]['itemid'],array_column($distributor_rate,'itemid'));
              if($rkey !== false){
                $discrate = ($distributor_rate[$rkey]->discrate > 0 ) ? $distributor_rate[$rkey]->discrate : floatval($products[$key1][$this->session->account_type]);
                $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - ($discrate)),2),2);
              }else{
                $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1][$this->session->account_type]))),2),2);
              }
              // foreach ($distributor_rate as $rate) {
              //   if(!in_array($products[$key1]['Id'],$run_pids)){
              //     if($rate->itemid == $value1["itemid"]){
              //       // if($products[$key1]["admin_isset"] == 1 && floatval($products[$key1]["disc_rate"]) != 0){
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['disc_rate']) * c_ofps() * $rate->discrate)),2),2);
              //       // }else{
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['shop_rate']) * c_ofps() * $rate->discrate)),2),2);
              //       // }
              //       $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - ($rate->discrate)),2),2);
              //     }else{
              //       // if($products[$key1]["admin_isset"] == 1 && floatval($products[$key1]["disc_rate"]) != 0){
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['disc_rate']) * c_ofps() * floatval($products[$key1][$this->session->account_type]))),2),2);
              //       // }else{
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['shop_rate']) * c_ofps() * floatval($products[$key1][$this->session->account_type]))),2),2);
              //       // }
              //
              //       $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1][$this->session->account_type]))),2),2);
              //     }
              //
              //     $run_pids[] = $products[$key1]["Id"];
              //   }
              //
              // }

            }
          }
        }else{
          if(count((array)$products) > 0){
            foreach ($products as $key1 => $value1) {
              foreach ($distributor_rate as $rate) {
                if($rate->itemid == $value1["itemid"])
                  $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - floatval($rate->discrate)),2),2);
              }

            }
          }
        }

      }

      $data['success'] = 1;
      $data['products'] = $products;
      $data['branches'] = $branches->result_array();
      generate_json($data);
      exit();

    } catch(Exception $e){
      $data = array(
          'success'     => 'error',
          'message'     => $e->message(),
          'environment' => ENVIRONMENT
      );
      generate_json($data);
    }
  }

  public function get_branch_products(){
    $this->isLoggedIn();
    $shopid = $this->input->post('shopid');
    $branchid = $this->input->post('branchid');

    try{
      if(empty($shopid)){
        $data = array("success" => 0, "message" => "Something went wrong. Please try again");
        $data['csrf_name']    =  $this->security->get_csrf_token_name();
        $data['csrf_hash']    =  $this->security->get_csrf_hash();
        generate_json($data);
        exit();
      }

      $products = $this->model_manual_order->get_shop_products($shopid,$branchid);
      if($products->num_rows() == 0){
        $products = $this->model_manual_order->get_shop_products($shopid);
        if($products->num_rows() == 0){
          $data = array("success" => 0 , "message" => "No product item available");
          $data['csrf_name']    =  $this->security->get_csrf_token_name();
          $data['csrf_hash']    =  $this->security->get_csrf_hash();
          generate_json($data);
          exit();
        }
      }

      $products = $products->result_array();
      if(isset($this->session->disrate) && count((array)$this->session->disrate) > 0 && $this->session->authenticated_seller == 1){
        $distributor_rate = $this->session->disrate;
        if(ini() == "toktokmall"){
          $run_pids = array();
          // print('<pre>'.print_r($products,true).'</pre>');
          if(count((array)$products) > 0){
            foreach ($products as $key1 => $value1) {

              $rkey = array_search($products[$key1]['itemid'],array_column($distributor_rate,'itemid'));
              if($rkey !== false){
                $discrate = ($distributor_rate[$rkey]->discrate > 0 ) ? $distributor_rate[$rkey]->discrate : floatval($products[$key1][$this->session->account_type]);
                $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - ($discrate)),2),2);
              }else{
                $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1][$this->session->account_type]))),2),2);
              }

              // foreach ($distributor_rate as $rate) {
              //   if(!in_array($products[$key1]['Id'],$run_pids)){
              //     if($rate->itemid == $value1["itemid"]){
              //       // if($products[$key1]["admin_isset"] == 1 && floatval($products[$key1]["disc_rate"]) != 0){
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['disc_rate']) * c_ofps() * $rate->discrate)),2),2);
              //       // }else{
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['shop_rate']) * c_ofps() * $rate->discrate)),2),2);
              //       // }
              //       $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - ($rate->discrate)),2),2);
              //     }else{
              //       // if($products[$key1]["admin_isset"] == 1 && floatval($products[$key1]["disc_rate"]) != 0){
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['disc_rate']) * c_ofps() * floatval($products[$key1][$this->session->account_type]))),2),2);
              //       // }else{
              //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['shop_rate']) * c_ofps() * floatval($products[$key1][$this->session->account_type]))),2),2);
              //       // }
              //       $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1][$this->session->account_type]))),2),2);
              //     }
              //
              //     $run_pids[] = $products[$key1]["Id"];
              //   }
              //
              // }

            }
          }
        }else{
          if(count((array)$products) > 0){
            foreach ($products as $key1 => $value1) {
              foreach ($distributor_rate as $rate) {
                if($rate->itemid == $value1["itemid"])
                  $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - floatval($rate->discrate)),2),2);
              }
            }
          }
        }

      }

      $data = array("success" => 1, "products" => $products);
      generate_json($data);
      exit();

    }catch(Exception $e){
      $data = array(
          'success'     => 'error',
          'message'     => $e->message(),
          'environment' => ENVIRONMENT
      );
      generate_json($data);
    }
  }
  // CONTINUE AS GUEST
  public function get_manual_order_option(){
    $this->session->set_userdata('authenticated_seller',0);
    $overall_access = $this->loginstate->get_access()['overall_access'];
    $seller_access = $this->loginstate->get_access()['seller_access'];
    $seller_branch_access = $this->loginstate->get_access()['seller_branch_access'];
    $shops = null;
    $branches = null;
    $products = null;
    $access = null;

    // overall access
    if($overall_access == 1){
      $access = "overall_access";
      $shops = $this->model_manual_order->get_shop_options();
    // seller access
    }else if($seller_access == 1){
      $access = "seller_access";
      $shopid = $this->session->sys_shop_id;
      $shops = $this->model_manual_order->get_shop_options($shopid);
      // $branches = $this->model_manual_order->get_shop_branches($shopid);
    // seller branch access
    }else if($seller_branch_access == 1 || $this->loginstate->get_access()['food_hub_access'] == 1){
      $access = "branch_access";
      $shopid = $this->session->sys_shop_id;
      $branchid = $this->session->branchid;
      $shops = $this->model_manual_order->get_shop_options($shopid);
      $branches = $this->model_manual_order->get_shop_branches($shopid,$branchid);
      $products = $this->model_manual_order->get_shop_products($shopid,$branchid);
    // else
    }else{
      $data = array("success" => 0, "message" => "You are not authorize to access this.");
      generate_json($data);
      exit();
    }

    $branches = ($branches != null) ? $branches->result_array() : $branches;
    $products = ($products != null) ? $products->result_array() : $products;

    $data = array(
      "success" => 1,
      "shops" => $shops,
      "branches" => $branches,
      "products" => $products,
      "access" => $access
    );
    generate_json($data);
    exit();
  }
  // CONTINUE AS SELLER
  public function authenticate_seller_id(){
    $access = null;
    $this->session->set_userdata('authenticated_seller',0);
    $reseller_id = strtoupper(sanitize($this->input->post('reseller_id')));
    $processDate = date('Y-m-d H:i:s');
    if(empty($reseller_id)){
      $data = array("success" => 0, "message" => "Please fill up all required fields");
      generate_json($data);
      exit();
    }

    if(ini() == "toktokmall"){
      $signature = md5($processDate.$this->jc_disc_idno_hash());
      $reseller_data = array (
        "signature" => sanitize(en_dec_ttm_api("en",$signature)),
        "idno" => sanitize(en_dec_ttm_api("en",$reseller_id)),
        "date" => $processDate
      );
    }else{
      $signature = md5($processDate.$this->jc_comrate_hashkey());
      $reseller_data = array (
        "signature" => sanitize(en_dec_jcw_api("en",$signature)),
        "idno" => sanitize(en_dec_jcw_api("en",$reseller_id)),
        "date" => $processDate
      );
    }
    // $id = sanitize(en_dec_jcw_api("en",$reseller_id));

    // die(sanitize(en_dec_jcw_api('dec',$id)));

    $postvars = http_build_query($reseller_data);

    $ch = curl_init();
    if(ini() == "toktokmall"){
      curl_setopt($ch, CURLOPT_URL, $this->jc_validate_resellerid_url());
    }else{
      curl_setopt($ch, CURLOPT_URL, $this->jcww_validate_reseller_link());
    }
    curl_setopt($ch, CURLOPT_POST, count($reseller_data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);

    if ($server_output === false) {
        $info = curl_getinfo($ch);
        curl_close($ch);
        die('error occured during curl exec. Additional info: ' . var_export($info));
    }
    curl_close($ch);
    $response =  json_decode($server_output);

    if(ini() == "toktokmall"){
      $account_type = en_dec_ttm_api("dec",$response->account_type);
      $account_type = str_replace('"','',$account_type);
    }
    // var_dump($response);
    // die();

    if($response == null){
      $data = array("success" => 0, "message" => "Unable to validate Online Franchise ID Number. Please try again");
      generate_json($data);
      exit();
    }

    if($response->success == 0){
      $data = array("success" => 0, "message" => 'Invalid Reseller ID. Please try again using valid Reseller ID or continue as guest.');
      generate_json($data);
      exit();
    }

    if(ini() == "toktokmall"){
      $distributor_rate = $this->model->get_comrate_by_account_type($account_type)->result();
      $distributor_info = en_dec_ttm_api('dec',$response->disinfo);
      $this->session->set_userdata('account_type',$account_type);
    }else{
      $distributor_info = en_dec_jcw_api('dec',$response->disinfo);
      $distributor_rate = en_dec_jcw_api('dec',$response->data_res);
      $distributor_rate = json_decode($distributor_rate);
    }
    // print_r($distributor_rate);
    // die();
    $distributor_info = json_decode($distributor_info);
    $this->session->set_userdata('disrate',$distributor_rate);

    $overall_access = $this->loginstate->get_access()['overall_access'];
    $seller_access = $this->loginstate->get_access()['seller_access'];
    $seller_branch_access = $this->loginstate->get_access()['seller_branch_access'];
    $shops = null;
    $branches = null;
    $products = null;

    // overall access
    if($overall_access == 1){
      $shops = $this->model_manual_order->get_shop_options();
      $access = "overall_access";
    // seller access
    }else if($seller_access == 1){
      $shopid = $this->session->sys_shop_id;
      $shops = $this->model_manual_order->get_shop_options($shopid);
      $access = "seller_access";
      // $branches = $this->model_manual_order->get_shop_branches($shopid);
    // seller branch access
    }else if($seller_branch_access == 1 || $this->loginstate->get_access()['food_hub_access'] == 1){
      $access = "branch_access";
      $shopid = $this->session->sys_shop_id;
      $branchid = $this->session->branchid;
      $shops = $this->model_manual_order->get_shop_options($shopid);
      $branches = $this->model_manual_order->get_shop_branches($shopid,$branchid);
      $products = $this->model_manual_order->get_shop_products($shopid,$branchid);
    // else
    }else{
      $data = array("success" => 0, "message" => "You are not authorize to access this.");
      generate_json($data);
      exit();
    }

    $branches = ($branches != null) ? $branches->result_array() : $branches;
    $products = ($products != null) ? $products->result_array() : $products;

    // echo "1"."<br>";
    // print_r($products);

    if($products != null){
      if(ini() == "toktokmall"){
        $run_pids = array();
        if(count((array)$products) > 0){
          foreach ($products as $key1 => $value1) {

            $rkey = array_search($products[$key1]['itemid'],array_column($distributor_rate,'itemid'));
            if($rkey !== false){
              $discrate = ($distributor_rate[$rkey]->discrate > 0 ) ? $distributor_rate[$rkey]->discrate : floatval($products[$key1][$this->session->account_type]);
              $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - ($discrate)),2),2);
            }else{
              $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1][$this->session->account_type]))),2),2);
            }

            // foreach ($distributor_rate as $rate) {
            //   if(!in_array($products[$key1]['Id'],$run_pids)){
            //     if($rate->itemid == $value1["itemid"]){
            //       // if($products[$key1]["admin_isset"] == 1 && floatval($products[$key1]["disc_rate"]) != 0){
            //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['disc_rate']) * c_ofps() * $rate->discrate)),2),2);
            //       // }else{
            //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['shop_rate']) * c_ofps() * $rate->discrate)),2),2);
            //       // }
            //
            //       $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - ($rate->discrate)),2),2);
            //     }else{
            //       // if($products[$key1]["admin_isset"] == 1 && floatval($products[$key1]["disc_rate"]) != 0){
            //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['disc_rate']) * c_ofps() * floatval($products[$key1][$account_type]))),2),2);
            //       // }else{
            //       //   $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1]['shop_rate']) * c_ofps() * floatval($products[$key1][$account_type]))),2),2);
            //       // }
            //
            //       $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - (floatval($products[$key1][$account_type]))),2),2);
            //     }
            //
            //     $run_pids[] = $products[$key1]["Id"];
            //   }
            //
            // }

          }
        }
      }else{
        if(count((array)$products) > 0){
          foreach ($products as $key1 => $value1) {
            foreach ($distributor_rate as $rate) {
              if($rate->itemid == $value1["itemid"])
                $products[$key1]["price"] = number_format(round(floatval($products[$key1]["price"]) * (1.00 - floatval($rate->discrate)),2),2);
            }

          }
        }
      }

    }

    // echo "2"."<br>";
    // print_r($products);
    // die();
    $fullname = $distributor_info->lname.', '.$distributor_info->fname.' '.$distributor_info->mname;
    $this->session->authenticated_seller = 1;
    $data = array(
      "success" => 1,
      "shops" => $shops,
      "branches" => $branches,
      "products" => $products,
      "name" => $fullname,
      "email" => $distributor_info->email,
      "conno" => $distributor_info->conno,
      "access" => $access
    );
    generate_json($data);
    exit();


  }

  public function create(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['manualorder_list']['create'] == 0){
      $data = array("success" => 0, "message" => "You don't have authority for this action");
      generate_json($data);
      exit();
    }

    $validation = array(
      array('customer','Customer Name','required'),
      array('shop','Shop','required'),
      array('date_ordered','Date Ordered','required'),
      array('date_shipped','Date Shipped','required'),
      array('email','Email','required|valid_email'),
      array('contact_no','Contact Number','required|min_length[7]'),
      array('city','City','required')
    );

    foreach ($validation as $value) {
        $this->form_validation->set_rules($value[0],$value[1],$value[2]);
    }
    if($this->form_validation->run() === FALSE){
      $data['environment']  =  ENVIRONMENT;
      $data['success']      =  0;
      $data['message']      =  validation_errors();
      $data['csrf_name']    =  $this->security->get_csrf_token_name();
      $data['csrf_hash']    =  $this->security->get_csrf_hash();
      echo json_encode($data);
      exit();
    }

    // try{

      $customer = $this->input->post('customer');
      $shop = $this->input->post('shop');
      $branches = $this->input->post('branches');
      $branches = ($branches != '') ? $branches : 0;
      $products = $this->input->post('products');
      $total_amount = $this->input->post('total_amount');
      $city = $this->input->post('city');
      $citymuncode = $this->input->post('citymuncode');
      $provcode = $this->input->post('provcode');
      $regcode = $this->input->post('regcode');
      $email = $this->input->post('email');
      $contact_no = $this->input->post('contact_no');
      $date_ordered = $this->input->post('date_ordered');
      $date_shipped = $this->input->post('date_shipped');
      $timestamp = $date_shipped." ".time_w_sec();
      $timestamp2 = $date_ordered." ".time_w_sec();

      $date_ordered = date('Y-m-d G:i:s', strtotime($timestamp2));
      $date_shipped = date('Y-m-d G:i:s', strtotime($timestamp));
      $shipping = (float)$this->input->post('shipping');
      $quantity_batch = array();
      $quantity_batch2 = array();
      $quantity_batch3 = array();

      $shopinfo = $this->model_manual_order->get_shop($shop);
      if(count((array)$shopinfo) == 0){
        $data = array("success" => 0, "message" => "Invalid shop. Please try again");
        generate_json($data);
        exit();
      }

      if($shop == "" || !(float)$total_amount > 0){
        $data = array("success" => 0, "message" => "Please fill up all required fields.");
        generate_json($data);
        exit();
      }

      if(count((array)$products) == 0){
        $data = array("success" => 0, "message" => "Unable to count any products. Please try again.");
        generate_json($data);
        exit();
      }

      if($date_ordered != $date_shipped){
        $data = array("success" => 0, "message" => "Date ordered and Date Shipped should be the same");
        generate_json($data);
        exit();
      }

      foreach($products as $prod){
        $quantity = $this->model_manual_order->get_products($prod['productid']);
        $branch_quantity = $this->model_manual_order->get_product_branch($prod['productid'],$shop,$branches);
        if($quantity->num_rows() == 0){
          $data = array("success" => 0, "message" => "Unable to find product quantity");
          generate_json($data);
          exit();
        }

        if($branch_quantity->num_rows() == 0){
          $data = array("success" => 0, "message" => "Unable to find product quantity");
          generate_json($data);
          exit();
        }

        $quantity = $quantity->row_array();
        if($quantity['quantity'] < $prod['quantity']){
          $data = array("success" => 0, "message" => $quantity['product_name']." no. of stocks is not enough for the order");
          generate_json($data);
          // break;
          exit();
        }

        $branch_quantity = $branch_quantity->row_array();
        if($branch_quantity['no_of_stocks'] < $prod['quantity']){
          $data = array("success" => 0, "message" => $quantity['product_name']." no. of stocks is not enough for the order");
          generate_json($data);
          // break;
          exit();
        }


        $quantity_arr = array(
          "no_of_stocks" => (int)$quantity['quantity'] - (int)$prod['quantity'],
          "Id" => $prod['productid']
        );

        $quantity_arr2 = array(
          "no_of_stocks" => (int)$prod['quantity'],
          "Id" => $prod['productid']
        );

        $quantity_arr3 = array(
          "quantity" => -(int)$prod['quantity'],
          "product_id" => $prod['productid'],
          "type" => 'Manual Order',
          "branchid" => $branches,
          "enabled " => 1
        );

        $quantity_batch[] = $quantity_arr;
        $quantity_batch2[] = $quantity_arr2;
        $quantity_batch3[] = $quantity_arr3;

      }

      $orderRefNum = order_ref_prefix(). rand(0, 1000) . round(microtime(true));
      $orderid = $this->uuid->v4_formatted();
      // app_manual_order_list
      $app_manual_order_list = array(
        "sys_shop" => $shop,
        "branch_id" => $branches,
        "reference_num" => $orderRefNum,
        "paypanda_ref" => '', //payment reference number
        "user_id" => 0, //userid of customer
        "name" => $customer,
        "conno" => $contact_no,
        "email" => $email,
        "address" => $city,
        "notes" => '',
        "areaid" => 0,
        "regCode" => $regcode,
        "citymunCode" => $citymuncode,
        "provCode" => $provcode,
        "postalcode" => 0,
        "total_amount" => $total_amount,
        "order_status" => "s",
        "payment_status" => 1,
        "payment_method" => 'Manual Order',
        "payment_amount" => ((float)$total_amount + (float)$shipping),
        "payment_date" => $date_ordered,
        "date_ordered" => $date_ordered,
        "date_order_processed" => $date_ordered,
        "date_ready_pickup" => $date_ordered,
        "date_booking_confirmed" => $date_ordered,
        "date_fulfilled" => $date_ordered,
        "date_shipped" => $date_shipped,
        "date_received" => $date_shipped,
        "delivery_amount" => $shipping
      );
      $this->model_manual_order->set_manual_order_details($app_manual_order_list);
      // app_manual_orders_shipping
      $app_manual_orders_shipping = array(
        "reference_num" => $orderRefNum,
        "sys_shop" => $shop,
        "delivery_amount" => $shipping,
        "daystoship" => 1,
        "daystoship_to" => 1,
        "created" => $date_shipped,
        "updated" => $date_shipped
      );
      $this->model_manual_order->set_app_manual_orders_shipping($app_manual_orders_shipping);
      // app_manual_order_logs_batch
      $app_manual_order_logs_batch = array();
      foreach($products as $item){
        // checking of duplicate product id
        if(count((array)$app_manual_order_logs_batch) > 0){
          foreach($app_manual_order_logs_batch as $item2){
            if($item['productid'] == $item2['product_id']){
              $item2['quantity'] += $item['quantity'];
              $item2['amount'] += $item['amount'];
            }
          }
        }

        $app_manual_order_logs = array(
          // "sys_shop" => $shop,
          "order_id" => $orderRefNum,
          "product_id" => $item['productid'],
          "quantity" => $item['quantity'],
          "amount" => $item['price'],
          "total_amount" => $item['amount']
        );

        $app_manual_order_logs_batch[] = $app_manual_order_logs;
      }
      $this->model_manual_order->set_app_manual_order_logs_batch($app_manual_order_logs_batch);

      // update sys_product.no_of_stocks
      $this->model_manual_order->update_prod_quantity($quantity_batch);

      // branchorders
      if($branches != ''){
        $sys_branch_orders = array(
          "branchid" => $branches,
          "orderid" => $orderRefNum,
          "date_created" => $date_shipped,
          "status" => 1
        );
        foreach($quantity_batch2 as $key => $prod){
          $quantity_batch2[$key]['branchid'] = $branches;
          $quantity_batch2[$key]['shopid'] = $shop;
        }
        $this->model_manual_order->set_branch_order($sys_branch_orders);
      }else{
        foreach($quantity_batch2 as $key => $prod){
          $quantity_batch2[$key]['branchid'] = 0;
          $quantity_batch2[$key]['shopid'] = $shop;
        }
      }
      $this->model_manual_order->update_sysproduct_invtrans_branch($quantity_batch2);
      $this->model_manual_order->set_sys_products_invtrans_batch($quantity_batch3);
      $data = array("success" => 1, "message" => "Order save successfully");
      $dep_amount = number_format($total_amount,2);
      $this->audittrail->logActivity('Manual Order', "$customer has ordered #$orderRefNum with a total amount of Php $dep_amount.", 'add', $this->session->userdata('username'));
      generate_json($data);
      exit();

    // }catch(Exception $e){
    //   $data = array(
    //       'success'     => "error",
    //       'message'     => $e,
    //       'environment' => ENVIRONMENT
    //   );
      generate_json($data);
    // }

  }

  public function manual_orders_view($token = '', $ref_num, $order_status_view){
      $this->isLoggedIn();
      if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['pending_orders']['view'] == 1 || $this->loginstate->get_access()['paid_orders']['view'] == 1 || $this->loginstate->get_access()['readyforprocessing_orders']['view'] == 1 || $this->loginstate->get_access()['processing_orders']['view'] == 1 || $this->loginstate->get_access()['readyforpickup_orders']['view'] == 1 || $this->loginstate->get_access()['bookingconfirmed_orders']['view'] == 1 || $this->loginstate->get_access()['fulfilled_orders']['view'] == 1 || $this->loginstate->get_access()['shipped_orders']['view'] == 1 || $this->loginstate->get_access()['returntosender_orders']['view'] == 1) {
          $member_id = $this->session->userdata('sys_users_id');
          $sys_shop = $this->model_orders->get_sys_shop($member_id);

          if($sys_shop == 0) {
              $split         = explode("-",$ref_num);
              $sys_shop      = $split[0];
              $reference_num = $split[1];

              $date_ordered  = $this->model_manual_order->get_app_order_details_date_ordered($reference_num);
              $prev_order    = $this->model_manual_order->get_prev_orders($reference_num, $date_ordered);
              $next_order    = $this->model_manual_order->get_next_orders($reference_num, $date_ordered);
              $prev_order    = $prev_order[0]['sys_shop'].'-'.$prev_order[0]['reference_num'];
              $next_order    = $next_order[0]['sys_shop'].'-'.$next_order[0]['reference_num'];

              if($sys_shop == 0){
                  $get_vouchers = $this->model_manual_order->get_vouchers($reference_num);
              }else{
                  $get_vouchers = $this->model_manual_order->get_vouchers_shop($reference_num, $sys_shop);
              }
          }else{
              $split         = explode("-",$ref_num);
              $sys_shop      = $split[0];
              $reference_num = $split[1];
              // $reference_num = $ref_num;
              $date_ordered  = $this->model_manual_order->get_app_order_details_date_ordered_shop($reference_num);
              $prev_order    = $this->model_manual_order->get_prev_orders_per_shop($reference_num, $sys_shop, $date_ordered);
              $next_order    = $this->model_manual_order->get_next_orders_per_shop($reference_num, $sys_shop, $date_ordered);
              $prev_order    = $prev_order[0]['reference_num'];
              $next_order    = $next_order[0]['reference_num'];
              $get_vouchers  = $this->model_manual_order->get_vouchers_shop($reference_num, $sys_shop);
          }

          $row            = $this->model_manual_order->orders_details($reference_num, $sys_shop);
          $refcode        = $this->model_manual_order->get_referral_code($row['reference_num']);
          $branch_details = $this->model_manual_order->get_branchname_orders($reference_num, $sys_shop)->row();

          if($sys_shop != 0){
              $mainshopname = $this->model_manual_order->get_mainshopname($sys_shop)->row()->shopname;
          }else{
              $mainshopname = 'toktokmall';
          }

          $orders_history = $this->model_manual_order->orders_history($row['order_id']);
          if(!empty($row['app_sales_id'])){
              $orders_history_sales = $this->model_manual_order->orders_history_sales($row['app_sales_id']);
          }else{
              $orders_history_sales = array();
          }
          $orders_history = array_merge($orders_history_sales, $orders_history);

          $data_admin = array(
              'token'               => $token,
              'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
              'shopid'              => $this->model_orders->get_sys_shop($member_id),
              'shopcode'            => $this->model_orders->get_shopcode($member_id),
              'partners'            => $this->model_orders->get_partners_options(),
              'partners_api_isset'  => $this->model_orders->get_partners_options_api_isset(),
              'payments'            => $this->model_orders->get_payments_options(),
              'reference_num'       => $reference_num,
              'order_details'       => $row,
              'referral'            => $refcode,
              'branch_details'      => $branch_details,
              'mainshopname'        => $mainshopname,
              'mainshopid'          => $sys_shop,
              'branch_count'        => count($this->model_orders->get_all_branch($sys_shop)->result()),
              'url_ref_num'         => $ref_num,
              'prev_order'          => $prev_order,
              'next_order'          => $next_order,
              'orders_history'      => $orders_history,
              'voucher_details'     => $get_vouchers,
              'branchid' 		      => $this->session->userdata('branchid'),
              'order_status_view'   => $order_status_view
          );

          $this->load->view('includes/header', $data_admin);
          $this->load->view('orders/manual_orders_view', $data_admin);
      }else{
          $this->load->view('error_404');
      }
  }

  public function order_item_table(){
      $this->isLoggedIn();
      $member_id = $this->session->userdata('sys_users_id');
      $sys_shop = $this->model_orders->get_sys_shop($member_id);

      $reference_num = sanitize($this->input->post('reference_num'));

      if($sys_shop == 0) {
          $split = explode("-",$reference_num);
          $sys_shop = $split[0];
          $reference_num = $split[1];
      }else{
          $split = explode("-",$reference_num);
          $sys_shop = $split[0];
          $reference_num = $split[1];
          $reference_num = $reference_num;
      }

      $query = $this->model_manual_order->order_item_table($reference_num, $sys_shop);

      generate_json($query);
  }

  public function print_order($token = '', $ref_num){
      $this->isLoggedIn();
      if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['pending_orders']['view'] == 1 || $this->loginstate->get_access()['paid_orders']['view'] == 1 || $this->loginstate->get_access()['readyforprocessing_orders']['view'] == 1 || $this->loginstate->get_access()['processing_orders']['view'] == 1 || $this->loginstate->get_access()['readyforpickup_orders']['view'] == 1 || $this->loginstate->get_access()['bookingconfirmed_orders']['view'] == 1 || $this->loginstate->get_access()['fulfilled_orders']['view'] == 1 || $this->loginstate->get_access()['shipped_orders']['view'] == 1 || $this->loginstate->get_access()['returntosender_orders']['view'] == 1) {
          error_reporting(0);
          $member_id = $this->session->userdata('sys_users_id');
          $sys_shop = $this->model_orders->get_sys_shop($member_id);

          if($sys_shop == 0) {
              $split         = explode("-",$ref_num);
              $sys_shop      = $split[0];
              $reference_num = $split[1];

              if($sys_shop == 0){
                  $get_vouchers = $this->model_manual_order->get_vouchers($reference_num);
              }else{
                  $get_vouchers = $this->model_manual_order->get_vouchers_shop($reference_num, $sys_shop);
              }
          }else{
              $split         = explode("-",$ref_num);
              $sys_shop      = $split[0];
              $reference_num = $split[1];
              $get_vouchers  = $this->model_manual_order->get_vouchers_shop($reference_num, $sys_shop);
          }

          $row            = $this->model_manual_order->orders_details($reference_num, $sys_shop);
          $refcode        = $this->model_manual_order->get_referral_code($row['reference_num']);
          $branch_details = $this->model_manual_order->get_branch_details($reference_num, $sys_shop)->row();
          $order_items    = $this->model_manual_order->order_item_table_print($reference_num, $sys_shop);

          if($sys_shop != 0){
              $mainshopname = $this->model_manual_order->get_mainshopname($sys_shop)->row()->shopname;
          }else{
              $mainshopname = 'toktokmall';
          }

          $data_admin = array(
              'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
              'shopid'              => $this->model_orders->get_sys_shop($member_id),
              'shopcode'            => $this->model_orders->get_shopcode($member_id),
              'reference_num'       => $reference_num,
              'order_details'       => $row,
              'referral'            => $refcode,
              'branch_details'      => $branch_details,
              'mainshopname'        => $mainshopname,
              'mainshopid'          => $sys_shop,
              'url_ref_num'         => $ref_num,
              'orders_history'      => $orders_history,
              'voucher_details'     => $get_vouchers,
              'order_items'         => $order_items
          );

          $page = $this->load->view('orders/order_print', $data_admin, true);

          $this->load->library('Pdf');
          $obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
          $obj_pdf->SetCreator(PDF_CREATOR);
          $obj_pdf->SetTitle('Order Print');
          $obj_pdf->SetDefaultMonospacedFont('helvetica');
          $obj_pdf->SetFont('helvetica', '', 9);
          $obj_pdf->setFontSubsetting(false);
          $obj_pdf->setPrintHeader(false);
          $obj_pdf->AddPage();
          ob_start();

          $obj_pdf->writeHTML($page, true, false, true, false, '');
          ob_clean();
          $obj_pdf->Output("print_order.pdf", 'I');
          $this->audittrail->logActivity('Order List', $this->session->userdata('username').' printed order #'.$reference_num, 'Print', $this->session->userdata('username'));
      }else{
          $this->load->view('error_404');
      }
  }

}
