<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Inventory_report extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_inventory_report');
        $this->load->model('shops/Model_shops');
        $this->load->model('products/Model_products', 'model_products');
        $this->load->model('orders/Model_orders', 'model_orders');
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
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['inv']['view'] == 1){        
        $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
        $main_nav_id = $this->views_restriction($content_url);

        $branches = null;
        if($this->session->userdata('sys_shop_id') != 0){
            $branches = $this->Model_inventory_report->getBranches($this->session->userdata('sys_shop_id'))->result_array();
        }
        
        $data_admin = array(
        'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            'shops' => $this->Model_inventory_report->get_shop_options(),
            'branches' => $branches,
            //'types' => $this->Model_inventory_report->getTransactionTypes(),
        );

        $this->load->view('includes/header',$data_admin);
        $this->load->view('reports/inventory_report',$data_admin);

        } else {
            $this->load->view('error_404');
        }
    }  

    public function inventory_transactions_table(){
        $fromdate = $this->input->post('date_from');
        $todate = $this->input->post('date_to');        
        $this->isLoggedIn();        
        
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        
        $data = $this->Model_inventory_report->get_inventory_trans_table($fromdate,$todate);
        echo json_encode($data);
    }

    public function export_inventory_transactions_table(){
        $this->isLoggedIn();  

        $filters = json_decode($this->input->post('_filters'));
        $fromdate = sanitize($filters->date_from);
        $todate = sanitize($filters->date_to);

        $fromshop = "";
        if(intval($this->session->sys_shop_id) == 0){
            $filters = json_decode($this->input->post('_filters'));
            $shop_id = $filters->shop_id;
            
            if($shop_id !== "all"){
                $fromshop.=" with filters Shop = '".$this->Model_inventory_report->getShopName($shop_id)."'";
            }
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            $fromshop.=" with filters Shop = '".$this->Model_inventory_report->getShopName($shop_id)."'";

            if($this->session->branchid != 0){
                $branch_id = $this->session->branchid;
                $fromshop.= " and branch = '" + $this->Model_inventory_report->getBranchName($branch_id)."'";
            }
            else{
                $branch_id = $filters->branch_id;
                if($branch_id != "all" && $branch_id != "main"){
                    $fromshop.= " and branch = '" + $this->Model_inventory_report->getBranchName($branch_id)."'";
                }
            }
        }                

        $inventory_transactions = $this->Model_inventory_report->get_inventory_trans_table($fromdate, $todate, true);        
    
        $remarks = 'Inventory Report has been exported into excel'.$fromshop.', Dated from '.$fromdate.' to '.$todate;;
        $this->audittrail->logActivity('Inventory Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Inventory Report');
        $sheet->setCellValue('B2', date('M d, Y',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate)));

        $sheet->getColumnDimension('A')->setAutoSize(true);        
        $sheet->getColumnDimension('B')->setAutoSize(true); 
        $sheet->getColumnDimension('C')->setAutoSize(true); 
        $sheet->getColumnDimension('D')->setAutoSize(true); 
        // $sheet->getColumnDimension('E')->setAutoSize(true); 
        $sheet->getColumnDimension('E')->setAutoSize(true);         
        // $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
    
        if($this->session->sys_shop_id == 0){
            $sheet->setCellValue('A6', 'Transaction Date');
            $sheet->setCellValue('B6', 'Shop Name');
            $sheet->setCellValue('C6', 'Branch Name');
            $sheet->setCellValue('D6', 'Product Name');
            $sheet->setCellValue('E6', 'Price');
            // $sheet->setCellValue('E6', 'Beginning Quantity');
            $sheet->setCellValue('F6', 'Quantity');
            // $sheet->setCellValue('G6', 'Ending Quantity');
            
        }
        else{
            if($this->session->branchid == 0){
                $sheet->setCellValue('A6', 'Transaction Date');
                $sheet->setCellValue('B6', 'Branch Name');
                $sheet->setCellValue('C6', 'Item Name');
                $sheet->setCellValue('D6', 'Price');
                // $sheet->setCellValue('D6', 'Beginning Quantity');
                $sheet->setCellValue('E6', 'Quantity');
                // $sheet->setCellValue('F6', 'Ending Quantity');
                
                
            }
            else{
                $sheet->setCellValue('A6', 'Transaction Date');
                $sheet->setCellValue('B6', 'Branch Name');
                $sheet->setCellValue('C6', 'Item Name');
                $sheet->setCellValue('D6', 'Price');
                // $sheet->setCellValue('D6', 'Beginning Quantity');
                $sheet->setCellValue('E6', 'Quantity');
                // $sheet->setCellValue('F6', 'Ending Quantity');      
            }
        }

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:H6')->getFont()->setBold(true);
        
    
        $exceldata= array();
        foreach ($inventory_transactions['data'] as $key => $row) {
            if($this->session->sys_shop_id == 0){
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[1],
                    '3' => $row[2],
                    '4' => $row[3],
                    '5' => $row[4],
                    '6' => $row[5],
                );
            }
            else{
                if($this->session->branchid == 0){
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[1],
                        '3' => $row[2],
                        '4' => $row[3],
                        '5' => $row[4],                      
                    );
                }
                else{
                    $resultArray = array(
                        '1' => $row[0],
                        '2' => $row[2],
                        '3' => $row[3],
                        '4' => $row[4],                       
                        '5' => $row[5],                       
                    );
                }
                
            }
          
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=6; $i < $row_count; $i++) {            
            if($this->session->branchid == 0){                
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);                
            }
            if($this->session->branchid ==0 && $this->session->sys_shop_id != 0){
                $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
            if($this->session->sys_shop_id == 0){
                $sheet->getStyle("G$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
            }

            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Inventory Report';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

    public function inventory_ending_report($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['invend']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            $member_id = $this->session->userdata('sys_users_id');

            $branches = null;
            if($this->session->userdata('sys_shop_id') != 0){
                $branches = $this->Model_inventory_report->getBranches($this->session->userdata('sys_shop_id'))->result_array();
            }

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'branchid'            => $this->session->userdata('branchid'),
                'branches'            => $branches
            );
    
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/inventory_ending_report',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function inventory_ending_table()
    {
        $this->isLoggedIn();
        $query = $this->Model_inventory_report->inventory_ending_table();
        
        generate_json($query);
    }

    public function get_branches()
	{
        $shopid = sanitize($this->input->post('shopid'));

        $data = $this->Model_inventory_report->getBranches($shopid)->result_array();
  
        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
	}

    public function inventory_ending_export()
    {
        $spreadsheet          = new Spreadsheet();
        $sheet                = $spreadsheet->getActiveSheet();
        $_name 			      = $this->input->post('_name_export');
        $_searchproduct_export  = $this->input->post('_searchproduct_export');
        $_shops 		      = $this->input->post('_shops_export');
        $_branches 		      = $this->input->post('_branches_export');
        $date_from 		      = format_date_reverse_dash($this->input->post('date_from_export'));
        $date_to 		      = format_date_reverse_dash($this->input->post('date_to_export'));
        $date_from_2          = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
        $date_to_2         	  = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
        $filter_string        = "Date".$this->input->post('date_from_export').' - '.$this->input->post('date_to_export');
        $requestData          = url_decode(json_decode($this->input->post("request_filter")));

        $query = $this->Model_inventory_report->inventory_ending_export();

        $getTotalEndingQty    = $this->Model_inventory_report->getTotalEndingQty($date_to_2)->result_array();
		$getTotalEndingQtyArr = [];

        $getBranch    = $this->Model_inventory_report->getBranch()->result_array();
		$getBranchArr = [];

		foreach($getTotalEndingQty as $row){
			$getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'] = $row['ending_quantity'];
		}

        foreach($getBranch as $row){
			$getBranchArr[strval($row['id'])]['branchname'] = $row['branchname'];
		}
            
        $sheet->setCellValue('B1', 'Inventory Ending Report');
        $sheet->setCellValue('B2', $this->input->post('date_from_export').' - '.$this->input->post('date_to_export'));
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);

        $sheet->setCellValue('A6', 'Shop Name');
        $sheet->setCellValue('B6', 'Branch Name');
        $sheet->setCellValue('C6', 'Product Name');
        $sheet->setCellValue('D6', 'Total Qty');
        $sheet->setCellValue('E6', 'Category');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        
        $exceldata= array();

        foreach($query as $row){

            if($row["variant_isset"] == 0 && $row['parent_product_id'] == '') {

                $ending_qty = (!empty($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'])) ? number_format($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'], 2) : '-';
                $branch     = ($row["branchid"] == 0) ? 'Main' :  $getBranchArr[strval($row['branchid'])]['branchname'];
                $itemname   = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : ''; 
                $resultArray = array(
                    '1' => $row["shopname"],
                    '2' => $branch,
                    '3' => $itemname.$row["itemname"],
                    '4' => $ending_qty,
                    '5' => $row["category_name"]
                );
                $exceldata[] = $resultArray;

            }else if($row['parent_product_id'] != ''){

                $ending_qty = (!empty($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'])) ? number_format($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'], 2) : '-';
                $branch     = ($row["branchid"] == 0) ? 'Main' :  $getBranchArr[strval($row['branchid'])]['branchname'];
                $itemname   = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : ''; 
                $resultArray = array(
                    '1' => $row["shopname"],
                    '2' => $branch,
                    '3' => $itemname.$row["itemname"],
                    '4' => $ending_qty,
                    '5' => $row["category_name"]
                );
                $exceldata[] = $resultArray;
            }

        }
        $key = $requestData['order'][0]['column']+1;
        $dir = $requestData['order'][0]['dir'];
        uasort($exceldata, build_sorter($key, $dir));
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'inventory_ending_report';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Inventory Ending Report', 'Inventory Ending Reporrt has been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }



}