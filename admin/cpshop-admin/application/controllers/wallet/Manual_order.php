<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Manual_order extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->model('wallet/model_manual_order');
    $this->load->model('wallet/Model_shops');
    $this->load->library('form_validation');
  }

  public function isLoggedIn() {
      if($this->session->userdata('isLoggedIn') == false) {
          header("location:".base_url('Main/logout'));
      }
  }

  public function list_table(){
    $search = json_decode($this->input->post('searchValue'));
    $data = $this->model_manual_order->list_table($search, $_REQUEST);
    echo json_encode($data);
  }

  public function export_list_table(){
    $requestData = url_decode(json_decode($this->input->post('_search')));
    $search = json_decode($requestData['searchValue']);

    $search_val = ($search->search == "") ? "No Search":$search->search;
    $shop_id = ($search->shop == "") ? "All Shops":$search->shop;
    $fromdate = ($search->from == "") ? "Current Date":$search->from;
    $todate = ($search->to == "") ? "Current Date":$search->to;
    $shop_name = ($search->shop == '') ? "All Shops":$this->Model_shops->get_shop_details($shop_id)->result_array()[0]['shopname'];

    $data = $this->model_manual_order->list_table($search, $requestData, true);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('B1', "Manual Order");
    $sheet->setCellValue('B2', "Filter: $search_val;$shop_name");
    $sheet->setCellValue('B3', "$fromdate to $todate");

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->getColumnDimension('F')->setWidth(30);

    $sheet->setCellValue('A6', 'Shop Name');
    $sheet->setCellValue('B6', 'Reference No.');
    $sheet->setCellValue('C6', 'Amount');
    $sheet->setCellValue('D6', 'Payment Type');
    $sheet->setCellValue('E6', 'Date Ordered');
    $sheet->setCellValue('F6', 'Date Shipped');

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
            '5' => $row[4],
            '6' => $row[5],
        );
        $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');
    $row_count = count($exceldata)+7;
    for ($i=7; $i < $row_count; $i++) {
      $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
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
    $filterstr .= "Dated $fromdate to $todate";
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
    if($this->loginstate->get_access()['manual_order']['view'] == 1){
      //start - for restriction of views
      $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
      $main_nav_id = $this->views_restriction($content_url);
      //end - for restriction of views main_nav_id

      // start - data to be used for views
      $data_admin = array(
          'token' => $token,
          'main_nav_id' => $main_nav_id, //for highlight the navigation,
          'shops' => $this->model_manual_order->get_shop_options(),
          'shops_w_wallets' => $this->model_manual_order->get_shop_w_wallet(),
          'cities' => $this->model_manual_order->get_cities()
          // 'payments' => $this->model_billing->get_options()
      );
      // end - data to be used for views

      // start - load all the views synchronously
      $this->load->view('includes/header', $data_admin);
      $this->load->view('wallet/manual_order', $data_admin);
    }else{
      $this->load->view('error_404');
    }

  }

  public function get_shop_order_details(){
    $this->isLoggedIn();
    try{
      $shopid = en_dec('dec',$this->input->post('shopid'));
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

      $data['success'] = 1;
      $data['products'] = $products->result_array();
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

  public function create(){
    $this->isLoggedIn();
    if($this->loginstate->get_access()['manual_order']['create'] == 0){
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
      $shop = en_dec('dec',$this->input->post('shop'));
      $branches = $this->input->post('branches');
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


      foreach($products as $prod){
        $quantity = $this->model_manual_order->get_products($prod['productid']);
        if($quantity->num_rows() == 0){
          $data = array("success" => 0, "message" => "Unable to find product quantity");
          generate_json($data);
          // break;
          exit();
        }

        $quantity = $quantity->row_array();
        if($quantity['quantity'] < $prod['quantity']){
          $data = array("success" => 0, "message" => $quantity['product_name']." quantity is not enough for the order");
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
          "enabled " => 1
        );

        $quantity_batch[] = $quantity_arr;
        $quantity_batch2[] = $quantity_arr2;
        $quantity_batch3[] = $quantity_arr3;

      }

      $orderRefNum = order_ref_prefix(). rand(0, 1000) . round(microtime(true));
      $orderid = $this->uuid->v4_formatted();
      // app_order_details
      $app_order_details = array(
        "order_id" => $orderid,
        "order_so_no" => order_so_ref_prefix().str_pad($this->model_manual_order->so_no(), 8, '0', STR_PAD_LEFT),
        "reference_num" => $orderRefNum,
        "paypanda_ref" => $orderRefNum, //payment reference number
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
        "admin_drno" => 0,
        "total_amount" => $total_amount,
        "order_status" => "f",
        "payment_status" => 1,
        "payment_method" => 'Prepayment',
        "payment_date" => $date_ordered,
        "date_ordered" => $date_ordered,
        "date_shipped" => $date_shipped,
        "date_received" => $date_shipped,
        "delivery_amount" => $shipping
      );
      $this->model_manual_order->set_app_order_details($app_order_details);
      // app_order_details_shipping
      $app_order_details_shipping = array(
        "reference_num" => $orderRefNum,
        "sys_shop" => $shop,
        "delivery_amount" => $shipping,
        "daystoship" => 1,
        "daystoship_to" => $shopinfo['daystoship'],
        "created" => $date_shipped,
        "updated" => $date_shipped
      );
      $this->model_manual_order->set_app_order_details_shipping($app_order_details_shipping);
      // app_order_logs
      $app_order_logs_batch = array();
      foreach($products as $item){
        // checking of duplicate product id
        if(count((array)$app_order_logs_batch) > 0){
          foreach($app_order_logs_batch as $item2){
            if($item['productid'] == $item2['product_id']){
              $item2['quantity'] += $item['quantity'];
              $item2['amount'] += $item['amount'];
            }
          }
        }

        $app_order_logs = array(
          "sys_shop" => $shop,
          "order_id" => $orderid,
          "product_id" => $item['productid'],
          "quantity" => $item['quantity'],
          "amount" => $item['price'],
          "total_amount" => $item['amount']
        );

        $app_order_logs_batch[] = $app_order_logs;
      }
      $this->model_manual_order->set_app_order_logs_batch($app_order_logs_batch);
      // app_sales_order_details
      $app_sales_order_details = array(
        "sys_shop" => $shop,
        "reference_num" => $orderRefNum,
        "paypanda_ref" => $orderRefNum,
        "user_id" => 0,
        "name" => $customer,
        "conno" => $contact_no,
        "email" => $email,
        "address" => $city,
        "notes" => '',
        "areaid" => 0,
        "postalcode" => 0,
        "regCode" => $regcode,
        "provCode" => $provcode,
        "citymunCode" => $citymuncode,
        "total_amount" => $total_amount,
        "order_status" => 'f',
        "payment_status" => 1,
        "payment_method" => 'Prepayment',
        "delivery_notes" => '',
        "delivery_amount" => $shipping,
        "payment_date" => $date_ordered,
        "date_ordered" => $date_ordered,
        "date_shipped" => $date_shipped,
        "date_order_processed" => $date_ordered,
        "date_ready_pickup" => $date_ordered,
        "date_booking_confirmed" => $date_ordered,
        "date_fulfilled" => $date_ordered,
        "date_received" => $date_shipped
      );
      $order_id = $this->model_manual_order->set_app_sales_order_details($app_sales_order_details);
      // app_sales_order_logs
      $app_sales_order_logs_batch = array();
      foreach($app_order_logs_batch as $logs){
        $app_sales_order_logs = array(
          "order_id" => $order_id,
          "product_id" => $logs['product_id'],
          "quantity" => $logs['quantity'],
          "amount" => $logs['amount'],
          "total_amount" => $logs['total_amount']
        );
        $app_sales_order_logs_batch[] = $app_sales_order_logs;
      }
      $this->model_manual_order->set_app_sales_order_logs_batch($app_sales_order_logs_batch);
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
      $this->audittrail->logActivity('Manual Order', "$customer has ordered manually with reference #$orderRefNum with a total amount of Php $dep_amount.", 'add', $this->session->userdata('username'));
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
}
