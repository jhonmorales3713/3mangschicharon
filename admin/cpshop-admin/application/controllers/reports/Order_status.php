<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Order_status extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_order_status');
        $this->load->model('shops/Model_shops');
    }

    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    public function views_restriction($content_url){
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
    public function index($token = ""){
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['osr']['view'] == 1){        
        $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
        $main_nav_id = $this->views_restriction($content_url);

        $branches = null;
        if($this->session->userdata('sys_shop_id')){
            $branches = $this->Model_order_status->getBranches($this->session->userdata('sys_shop_id'))->result_array();
        }
        
        $data_admin = array(
        'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            'shops' => $this->Model_order_status->get_shop_options(),
            //'branches' => $branches,            
        );

        $this->load->view('includes/header',$data_admin);
        $this->load->view('reports/order_status_report',$data_admin);

        } else {
            $this->load->view('error_404');
        }
    }    

    public function pending_orders_data(){
        $this->isLoggedIn();
        $orders = $this->Model_order_status->get_pending_orders();

        $data = array_column($orders,'single_cnt');        
        $label = array();     
        
        foreach($orders as $row){
            $label[] = $row['shopname'].' - '.$row['branchname'];
        }
        
        $reschart = array(
            'data' => $data,
            'label' => $label            
        );

        $data = array("success" => 1, "chartdata" => $reschart);
        generate_json($data);
    }    

    public function pending_orders_table(){
        $this->isLoggedIn();        
        $data = $this->Model_order_status->get_pending_orders_table();
        echo json_encode($data);
    }

    public function export_pending_orders_table(){
        $this->isLoggedIn();  
        
        $fromshop = "";
        if(intval($this->session->sys_shop_id) == 0){
            $filters = json_decode($this->input->post('_filters'));
            $shop_id = $filters->shop_id;
            
            if($shop_id !== "all"){
                $fromshop.=" with filters Shop = '".$this->Model_order_status->getShopName($shop_id)."'";
            }
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            $fromshop.=" with filters Shop = '".$this->Model_order_status->getShopName($shop_id)."'";
        }        

        $order_status = $this->Model_order_status->get_pending_orders_table(true);
        
        $asOf = date('m/d/Y');
    
        $remarks = 'Order Status Report has been exported into excel'.$fromshop.', As of'.$asOf;
        $this->audittrail->logActivity('Order Status Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Order Status Report (Summary)');
        $sheet->setCellValue('B2', 'As of '.$asOf);
        $sheet->getColumnDimension('A')->setWidth(30);    
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
    
        $sheet->setCellValue('A6', 'Shop Name');    
        $sheet->setCellValue('B6', 'Branch Name');
        $sheet->setCellValue('C6', 'Total Pending Orders');
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:C6')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($order_status['data'] as $key => $row) {
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2]
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Order Status (Summary)';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

    //end of main view

    //2nd page -> branch pending orders
    public function shop_branch_pending_orders_list($shop_id, $branch_id, $token){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['osr']['view'] == 1){            
            
            $data_admin = array(
                'token' => $token,
                'main_nav_categories' => $this->Model_order_status->main_nav_categories()->result(),
                'shops' => $this->Model_order_status->get_shop_options(),
                'shop_id' => $shop_id,
                'branch_id' => $branch_id,
                'shopname' => $this->Model_order_status->getShopName($shop_id),
                'branchname' => $branch_id != 0 ? $this->Model_order_status->getBranchName($branch_id) : 'Main',
                'branch_details' => $this->Model_order_status->getBranchDetails($shop_id, $branch_id)
            );
    
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/order_status_shop_branch_report',$data_admin);
    
        } else {
            $this->load->view('error_404');
        }        
    }

    public function pending_orders_branch_table(){
        $this->isLoggedIn();        
        $data = $this->Model_order_status->get_pending_orders_branch_table();
        echo json_encode($data);
    }

    public function export_pending_orders_branch_table(){
        $this->isLoggedIn();  

        $filters = json_decode($this->input->post('_filters'));
        $shop_id = $filters->shop_id;
        $branch_id = $filters->branch_id;

        $fromshop = "";
        $branch_name = "";
        if($shop_id !== "all"){
            $fromshop.=" with filters Shop = '".$this->Model_order_status->getShopName($shop_id);
            if($branch_id != 0){
                $branch_name = $this->Model_order_status->getBranchName($branch_id);                
            }
            else{                
                $branch_name = "Main";
            }
            $fromshop.="' > Branch = '".$branch_name."'";
        }

        $order_status = $this->Model_order_status->get_pending_orders_branch_table($branch_id,true);
        
        $asOf = date('m/d/Y');
    
        $remarks = 'Order Status Branch Report has been exported into excel'.$fromshop.', As of'.$asOf;
        $this->audittrail->logActivity('Order Status Branch Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Order Status - Branch Report');
        $sheet->setCellValue('B2', 'As of '.$asOf);

        $sheet->setCellValue('A4', 'Shop Name:');
        $sheet->setCellValue('A5', 'Branch Name:');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $sheet->setCellValue('B4', $this->Model_order_status->getShopName($shop_id));
        $sheet->setCellValue('B5', $branch_name);

        $sheet->getColumnDimension('A')->setWidth(30);    
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
    
        $sheet->setCellValue('A8', 'Payment Date');    
        $sheet->setCellValue('B8', 'Total Pending Orders');
        $sheet->setCellValue('C8', 'Total Orders on Process');
        $sheet->setCellValue('D8', 'Total Orders Ready for Booking');
        $sheet->setCellValue('E8', 'Total Orders Booking Confirmed');
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A8:E8')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($order_status['data'] as $key => $row) {
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[4],
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A9');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Order Status - Branch';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

    //end of 2nd Page

    //3rd page -> Branch pending Order details
    public function shop_branch_pending_orders_in_date($shop_id, $branch_id, $reference_num, $token){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['osr']['view'] == 1){            
            
            $data_admin = array(
                'token' => $token,
                'main_nav_categories' => $this->Model_order_status->main_nav_categories()->result(),
                'shops' => $this->Model_order_status->get_shop_options(),
                'shop_id' => $shop_id,
                'branch_id' => $branch_id,
                'shopname' => $this->Model_order_status->getShopName($shop_id),
                'branchname' => $branch_id != 0 ? $this->Model_order_status->getBranchName($branch_id) : 'Main',
                'branch_details' => $this->Model_order_status->getBranchDetails($shop_id, $branch_id),                
                'payment_date' => $_GET['payment_date']
            );
    
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/order_status_order_list_report',$data_admin);
    
        } else {
            $this->load->view('error_404');
        }        
    }

    public function shop_branch_pending_orders_in_date_table($branch_id){
        $this->isLoggedIn();                
        $payment_date = $this->input->post('payment_date');
        $data = $this->Model_order_status->get_branch_order_in_date_table($branch_id,$payment_date);
        echo json_encode($data);
    }

    public function export_shop_branch_pending_orders_in_date_table($branch_id){
        $this->isLoggedIn();  

        $filters = json_decode($this->input->post('_filters'));
        $shop_id = $filters->shop_id;
        $payment_date = $filters->payment_date;

        $fromshop = "";
        $branch_name = "";
        if($shop_id !== "all"){
            $fromshop.=" with filters Shop = '".$this->Model_order_status->getShopName($shop_id);
            if($branch_id != 0){
                $branch_name = $this->Model_order_status->getBranchName($branch_id);                
            }
            else{                
                $branch_name = "Main";
            }
            $fromshop.="' > Branch = '".$branch_name."'";
        }

        $order_status = $this->Model_order_status->get_branch_order_in_date_table($branch_id,$payment_date,true);
        
        $asOf = date('Y-m-d',strtotime($payment_date));
    
        $remarks = 'Order Status Branch Report has been exported into excel'.$fromshop.', Dated'.$asOf;
        $this->audittrail->logActivity('Order Status Branch Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Order Status - Branch Report');
        $sheet->setCellValue('B2', 'Pending Orders on '.$asOf);

        $sheet->setCellValue('A4', 'Shop Name:');
        $sheet->setCellValue('A5', 'Branch Name:');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $sheet->setCellValue('B4', $this->Model_order_status->getShopName($shop_id));
        $sheet->setCellValue('B5', $branch_name);

        $sheet->getColumnDimension('A')->setAutoSize(true);      
        $sheet->getColumnDimension('B')->setAutoSize(true);    
        $sheet->getColumnDimension('C')->setAutoSize(true);    
        $sheet->getColumnDimension('D')->setAutoSize(true);    
        $sheet->getColumnDimension('E')->setAutoSize(true);    
        $sheet->getColumnDimension('F')->setAutoSize(true);    
        $sheet->getColumnDimension('G')->setAutoSize(true);        
    
        $sheet->setCellValue('A8', 'Payment Date');    
        $sheet->setCellValue('B8', 'Reference Number');
        $sheet->setCellValue('C8', 'Sold To');
        $sheet->setCellValue('D8', 'Subtotal');
        $sheet->setCellValue('E8', 'Shipping');
        $sheet->setCellValue('F8', 'Total Amount');        
        $sheet->setCellValue('G8', 'Address');
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A8:G8')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($order_status['data'] as $key => $row) {
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
            '5' => $row[4],
            '6' => $row[5],
            '7' => $row[6],
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A9');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Order Status - Branch Pending Order List';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

    //end of 3rd

    //4th page     
    public function shop_branch_order_details($shop_id, $branch_id, $reference_num, $is_manual_order, $token){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['osr']['view'] == 1){            
            
            $data_admin = array(
                'token' => $token,
                'main_nav_categories' => $this->Model_order_status->main_nav_categories()->result(),
                'shops' => $this->Model_order_status->get_shop_options(),
                'shop_id' => $shop_id,
                'branch_id' => $branch_id,
                'shopname' => $this->Model_order_status->getShopName($shop_id),
                'branchname' => $branch_id != 0 ? $this->Model_order_status->getBranchName($branch_id) : 'Main',
                'branch_details' => $this->Model_order_status->getBranchDetails($shop_id, $branch_id),  
                'order_details' => $this->Model_order_status->getOrderDetails($reference_num),
                'reference_num' => $reference_num,
                'is_manual_order' => $is_manual_order,
                'payment_date' => date('Y-m-d', strtotime($_GET['payment_date']))
            );
    
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/order_status_order_details_report',$data_admin);
    
        } else {
            $this->load->view('error_404');
        }         
    }

    public function order_details_table($reference_num,$isManualOrder){
        $this->isLoggedIn();                        
        $data = $this->Model_order_status->get_order_details_table($reference_num,$isManualOrder);
        echo json_encode($data);
    }

    public function export_order_details_table($reference_num){
        $this->isLoggedIn();  

        $filters = json_decode($this->input->post('_filters'));
        $shop_id = $filters->shop_id;
        $branch_id = $filters->branch_id;
        $payment_date = $filters->payment_date;
        $order_stat = $filters->order_status;

        $fromshop = "";
        $branch_name = "";
        if($shop_id !== "all"){
            $fromshop.=" with filters Shop = '".$this->Model_order_status->getShopName($shop_id);
            if($branch_id != 0){
                $branch_name = $this->Model_order_status->getBranchName($branch_id);                
            }
            else{                
                $branch_name = "Main";
            }
            $fromshop.="' > Branch = '".$branch_name."'";
        }

        $order_status = $this->Model_order_status->get_order_details_table($reference_num,true);
        
        $asOf = date('Y-m-d',strtotime($payment_date));
    
        $remarks = 'Order Status Branch Report has been exported into excel'.$fromshop.', Dated'.$asOf;
        $this->audittrail->logActivity('Order Status Branch Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Order Status - Branch Report');
        $sheet->setCellValue('B2', 'Order Details ');

        $sheet->setCellValue('A4', 'Shop Name:');
        $sheet->setCellValue('A5', 'Branch Name:');
        $sheet->setCellValue('A6', 'Reference Number:');
        $sheet->setCellValue('A7', 'Payment Date:');
        $sheet->setCellValue('A8', 'Order Status:');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7')->getFont()->setBold(true);
        $sheet->getStyle('A8')->getFont()->setBold(true);

        $sheet->setCellValue('B4', $this->Model_order_status->getShopName($shop_id));
        $sheet->setCellValue('B5', $branch_name);
        $sheet->setCellValue('B6', $reference_num);
        $sheet->setCellValue('B7', date('M d, Y',strtotime($payment_date)));
        $sheet->setCellValue('B8', $order_stat);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
    
        $sheet->setCellValue('A11', 'Item Name');    
        $sheet->setCellValue('B11', 'Quantity');
        $sheet->setCellValue('C11', 'Amount');
        $sheet->setCellValue('D11', 'Total Amount');
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A11:D11')->getFont()->setBold(true);
    
        $exceldata= array();
        foreach ($order_status['data'] as $key => $row) {
          $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3],
          );
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A12');
        $row_count = count($exceldata)+12;
        for ($i=12; $i < $row_count; $i++) {
            $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Order Status - Branch Pending Order Details';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }
}   