<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Product_releasing extends MY_Controller {
    //controller is extended to core Controller in folder core    

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_product_releasing');
    }    
    
    public function index($token = ""){
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['prr']['view'] == 1){        
        $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
        $main_nav_id = parent::views_restriction($content_url);

        $shops = null;
        if($this->session->sys_shop_id == 0){            
            $shops = parent::getShops();
        }
        
        $data_admin = array(
        'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            'shops' => $shops            
            //'types' => $this->Model_inventory_report->getTransactionTypes(),
        );

        $this->load->view('includes/header',$data_admin);
        $this->load->view('reports/product_releasing_report',$data_admin);

        } else {
            $this->load->view('error_404');
        }
    }  

    public function get_product_releasing_data(){
        parent::isLoggedIn();
        if($this->loginstate->get_access()['prr']['view'] == 1){    
            
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));

            $release_type = $this->input->post('release_type');            
                       
            if($this->session->sys_shop_id == 0){
                $shop_id = $this->input->post('shop_id');
                $branch_id = $this->input->post('branch_id');
            }
            else{
                $shop_id = $this->session->sys_shop_id;
                if($this->session->branchid == 0){
                    $branch_id = $this->input->post('branch_id');
                }
                else{
                    $branch_id = $this->sesion->branchid;
                }
            }            
                     
            $items = $this->Model_product_releasing->get_product_releasing_data($fromdate,$todate,$shop_id,$branch_id,$release_type);
            
            $reschart = array(                
                'data' => $items,
                'shop_id' => $shop_id,
                'branch_id' => $branch_id,   
                'release_type' => $release_type,
            );
            
            $data = array("success" => 1, "chartdata" => $reschart);
            generate_json($data);
            exit();

        }else{
            $this->load->view('error_404');
        }
    }
    
    

    public function get_product_releasing_table(){        

        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));

        $release_type = $this->input->post('release_type');     
        
        if($this->session->sys_shop_id == 0){
            $shop_id = $this->input->post('shop_id');
            $branch_id = $this->input->post('branch_id');
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            if($this->session->branchid == 0){
                $branch_id = $this->input->post('branch_id');
            }
            else{
                $branch_id = $this->sesion->branchid;
            }
        }         

        $data = $this->Model_product_releasing->get_product_releasing_table($fromdate, $todate, $shop_id, $branch_id, $release_type);
        echo json_encode($data);
    }

    public function export_product_releasing_table(){
        parent::isLoggedIn();

        $filters = json_decode($this->input->post('_filters'));           
        $fromdate = sanitize($filters->fromdate);
        $todate = sanitize($filters->todate);
        $release_type = $filters->release_type;
        
        $fromshop = "";
        if(intval($this->session->sys_shop_id) == 0){
            $filters = json_decode($this->input->post('_filters'));
            $shop_id = $filters->shop_id;
            $branch_id = $filters->branch_id;            
            
            if($shop_id != "all"){
                $fromshop.=" with filters Shop = '".parent::getShopName($shop_id)."'";
                if($time_in_seconds != 0){
                    $fromshop.= " and Criteria = ".$criteria;
                }
            }
            else{
                if($time_in_seconds != 0){
                    $fromshop.= " with filters Criteria = ".$criteria;
                }
            }            
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            $fromshop.=" with filters Shop = '".parent::getShopName($shop_id)."'";

            if($this->session->branchid != 0){
                $branch_id = $this->session->branchid;
                $fromshop.= " and branch = '".parent::getBranchName($branch_id)."'";
            }
            else{
                $branch_id = $filters->branch_id;
                if($branch_id != "all"){
                    $fromshop.= " and branch = '".parent::getBranchName($branch_id)."'";
                }
            }
        }      
        
        if($fromdate == $todate){
            $asOf = $fromdate;            
        }
        else{
            $asOf = $fromdate.' - '.$todate;            
        }

        if($release_type == "released"){
            $sub_header = " - (Released)";
        }
        else{
            $sub_header = " - (Not Released)";
        }

        $product_releasing = $this->Model_product_releasing->get_product_releasing_table($fromdate, $todate, $shop_id, $branch_id, $release_type, true);
    
        $remarks = 'Product Releasing Report has been exported into excel'.$fromshop.' '.$asOf;
        $this->audittrail->logActivity('Product Releasing Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Product Releasing Report '.$sub_header);            
        $sheet->setCellValue('B2', $asOf);
        
        $sheet->getColumnDimension('A')->setAutoSize(true);        
        $sheet->getColumnDimension('B')->setAutoSize(true); 
        $sheet->getColumnDimension('C')->setAutoSize(true); 
        $sheet->getColumnDimension('D')->setAutoSize(true); 
    
        if($this->session->sys_shop_id == 0){
            $sheet->setCellValue('A6', 'Shop Name');
            $sheet->setCellValue('B6', 'Branch Name');            
            $sheet->setCellValue('C6', 'Item Name');
            $sheet->setCellValue('D6', 'Quantity');
        }
        else{
            if($this->session->branchid == 0){                
                $sheet->setCellValue('A6', 'Branch Name');            
                $sheet->setCellValue('B6', 'Item Name');
                $sheet->setCellValue('C6', 'Quantity');
                $sheet->setCellValue('A4', 'Shop Name');
                $sheet->getStyle('A4')->getFont()->setBold(true);
                $sheet->setCellValue('B4', parent::getShopName($shop_id));
            }
            else{                    
                $sheet->setCellValue('A6', 'Item Name');
                $sheet->setCellValue('B6', 'Quantity');
            }
        }

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);
        
    
        $exceldata= array();
        foreach ($product_releasing['data'] as $key => $row) {
            if($this->session->sys_shop_id == 0){
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[1],
                    '3' => $row[2],
                    '4' => $row[3],
                );
            }
            else{
                if($this->session->branchid == 0){
                    $resultArray = array(
                        '1' => $row[1],
                        '2' => $row[2],
                        '3' => $row[3],                      
                    );
                }
                else{
                    $resultArray = array(
                        '1' => $row[2],
                        '2' => $row[3],                        
                    );
                }
                
            }          
            $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A7');        

        $writer = new Xlsx($spreadsheet);
        $filename = 'Product Releasing Report';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

    //branch performance details   
    public function branch_performance_details($shop_id, $branch_id, $time_in_seconds, $token){
        parent::isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['bpr']['view'] == 1){                

            $shops = null;
            if($this->session->sys_shop_id == 0){
                $shops = parent::getShops();
            }
            
            $data_admin = array(
                'token' => $token,
                'main_nav_categories' => $this->Model_branch_performance->main_nav_categories()->result(),
                'shops' => $shops,
                'shop_id' => $shop_id,
                'branch_id' => $branch_id,    
                'time_in_seconds' => $time_in_seconds,
                'shopname' => parent::getShopName($shop_id),
                'branchname' => parent::getBranchName($branch_id),
            );
    
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/branch_performance_details_report',$data_admin);
    
        } else {
            $this->load->view('error_404');
        }        
    }

    public function branch_performance_breakdown_table(){        
        
        if($this->session->sys_shop_id == 0){
            $shop_id = $this->input->post('shop_id');
            $branch_id = $this->input->post('branch_id');
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            if($this->session->branchid == 0){
                $branch_id = $this->input->post('branch_id');
            }
            else{
                $branch_id = $this->sesion->branchid;
            }
        }     
        
        $time_in_seconds = $this->input->post('time_in_seconds');

        $data = $this->Model_branch_performance->get_branch_performance_breakdown_table($shop_id, $branch_id, $time_in_seconds);
        echo json_encode($data);
    }

    public function export_branch_performance_breakdown_table(){
        parent::isLoggedIn();        

        $filters = json_decode($this->input->post('_filters'));

        if($time_range != '0' && $time_range != '168'){
            $time_in_seconds = abs(intval($time_range * 3600));
        }
        else{
            $time_in_seconds = 0;
        }

        $filters = json_decode($this->input->post('_filters'));
        $shop_id = $filters->shop_id;
        $branch_id = $filters->branch_id;
        
        $fromshop = "";
        $fromshop.=" with filters Shop = '".parent::getShopName($shop_id)."'";
        $fromshop.= " and branch = '" + parent::getBranchName($branch_id)."'";

        $branch_performance = $this->Model_branch_performance->get_branch_performance_breakdown_table($shop_id, $branch_id, $time_in_seconds, true);        
    
        $remarks = 'Branch Performance Report - Order Breakdown has been exported into excel'.$fromshop.', As of '.format_shortdatetime(date(''));
        $this->audittrail->logActivity('Branch Performance Report - Order Breakdown', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('B1', 'Branch Performance Report - Orders Breakdown');
        $sheet->setCellValue('B2', 'As of '.format_shortdatetime(date('')));

        $sheet->getColumnDimension('A')->setAutoSize(true);        
        $sheet->getColumnDimension('B')->setAutoSize(true); 
        $sheet->getColumnDimension('C')->setAutoSize(true); 
        $sheet->getColumnDimension('D')->setAutoSize(true); 
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $sheet->setCellValue('A4', 'Shop Name');
        $sheet->setCellValue('A5', 'Branch Name');

        $sheet->setCellValue('B4', parent::getShopName($shop_id));
        $sheet->setCellValue('B5', parent::getBranchName($branch_id));
        
        $sheet->setCellValue('A7', 'Payment Date');
        $sheet->setCellValue('B7', 'Date Shipped');            
        $sheet->setCellValue('C7', 'Referenc Number');
        $sheet->setCellValue('D7', 'Order Aging');
        $sheet->setCellValue('E7', 'Amount');
      
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A7:E7')->getFont()->setBold(true);
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5')->getFont()->setBold(true);        
    
        $exceldata= array();        
        foreach ($branch_performance['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4]
            );          
          $exceldata[] = $resultArray;
        }
    
        $sheet->fromArray($exceldata, null, 'A8');        
        $row_count = count($exceldata)+8;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
            $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);                          
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Branch Performance Report';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

}