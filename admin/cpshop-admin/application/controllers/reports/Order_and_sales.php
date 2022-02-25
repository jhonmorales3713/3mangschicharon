<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Order_and_sales extends MY_Controller {
    //controller extended to MY_Controller in core folder

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_order_and_sales');
    }   
     
    public function index($token = ""){
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['os']['view'] == 1){        
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = parent::views_restriction($content_url);            
            
            $data_admin = array(
            'token' => $token,
                'main_nav_id' => $main_nav_id,
                'shops' => parent::getShops(),                
                'setDate' => [
                'fromdate' => (isset($_GET['fromdate']) ? $_GET['fromdate'] : null),
                'todate' => (isset($_GET['todate']) ? $_GET['todate'] : null),
                ]
            );
            
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/order_and_sales_report',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_order_and_sales_data(){
        parent::isLoggedIn();
        if($this->loginstate->get_access()['os']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));             
            $pmethodtype = sanitize($this->input->post('pmethodtype'));             
            
            if($this->session->sys_shop_id == 0){
                $shop_id = $this->input->post('shop_id');
                $branch_id = $this->input->post('branch_id');
            }
            else{
                $shop_id = $this->session->sys_shop_id;
    
                if($this->session->branchid != 0){
                    $branch_id = $this->session->branchid;
                }
                else{
                    $branch_id = $this->input->post("branch_id");
                }
            }
          
            $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
            $p_fromdate = $pre_date_range['fromdate'];
            $p_todate = $pre_date_range['todate'];

            $p_fromdate = $p_fromdate->format('Y-m-d');
            $p_todate = $p_todate->format('Y-m-d');

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            if($fromdate == $todate){               
                
                $cur_result = $this->Model_order_and_sales->get_order_and_sales($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);
                $pre_result = $this->Model_order_and_sales->get_order_and_sales($p_fromdate,$p_todate,$shop_id,$branch_id,$pmethodtype);  

                $cur_range = array_column($cur_result,'date_ordered');
                $pre_range = array_column($pre_result,'date_ordered');
                
                if($cur_range && $pre_range){
                $time_range = getTimeRange($cur_range,$pre_range);
                }
                else if($cur_range && !$pre_range){
                $time_range = getTimeRange($cur_range,$cur_range);
                }
                else{
                $time_range = []; $time_range['start'] = '00:00'; $time_range['end'] = '23:00';
                }
                
                $dates = createTimeRangeArray($time_range['start'],$time_range['end']);                

                $cur_dates = createTimeRangeArray($time_range['start'],$time_range['end'],'1 hour',date('Y-m-d',strtotime($fromdate)));
                $pre_dates = createTimeRangeArray($time_range['start'],$time_range['end'],'1 hour',date('Y-m-d',strtotime($p_fromdate)));        

                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;                     
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                        }
                    }
                    $new_cur_result[] = array(
                        'total_orders' => $cur_total_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                        }
                    }
                    $new_pre_result[] = array(
                        'total_orders' => $pre_total_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_orders');

                $cur_total_to = getTotalInArray($cur_result,'total_orders');
                $pre_total_to = getTotalInArray($pre_result,'total_orders');

                $cur_total_ts = getTotalInArray($cur_result,'total_amount');
                $pre_total_ts = getTotalInArray($pre_result,'total_amount');        
                
                $cur_tb_head = date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d/Y',strtotime($p_todate));
                
                $cur_period = date('M d, Y',strtotime($todate));
                $pre_period = date('M d, Y',strtotime($p_todate));

                if(date('Y-m-d',strtotime($todate)) == date('Y-m-d')){
                    $cur_period = 'Today';
                    $pre_period = 'Yesterday';
                    $cur_tb_head = 'Today';
                    $pre_tb_head = 'Yesterday';
                }
            }
            else{ 

                $dates = generateDates($fromdate,$todate,'M d');

                $cur_result = $this->Model_order_and_sales->get_order_and_sales($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);
                $pre_result = $this->Model_order_and_sales->get_order_and_sales($p_fromdate,$p_todate,$shop_id,$branch_id,$pmethodtype);                

                $cur_dates = generateDates($fromdate,$todate);
                $pre_dates = generateDates($p_fromdate,$p_todate);

                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;                     
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                        }
                    }
                    $new_cur_result[] = array(
                        'total_orders' => $cur_total_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                        }
                    }
                    $new_pre_result[] = array(
                        'total_orders' => $pre_total_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_orders');

                $cur_total_to = getTotalInArray($cur_result,'total_orders');
                $pre_total_to = getTotalInArray($pre_result,'total_orders');

                $cur_total_ts = getTotalInArray($cur_result,'total_amount');
                $pre_total_ts = getTotalInArray($pre_result,'total_amount');

                $size = sizeof($dates);

                $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));

                $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
                $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
            
            }      

            $cur_total_ts = floatval($cur_total_ts);
            $pre_total_ts = floatval($pre_total_ts);

            $increased_to = false;
            $percentage_to = 0.0;
            
            if($cur_total_to >= $pre_total_to){        
                $increased_to = true;
            }
            $percentage_to = getPercentage($cur_total_to,$pre_total_to);
            
            $increased_ts = false;
            $percentage_ts = 0.0;      

            if($cur_total_ts >= $pre_total_ts){
                $increased_ts = true;
            }
            $percentage_ts = getPercentage($cur_total_ts,$pre_total_ts);    

            $reschart = array(
                'dates' => $dates,
                'cur_dates' => $cur_dates,
                'pre_dates' => $pre_dates,
                'cur_data' => $cur_data,
                'pre_data' => $pre_data,
                'cur_result' => $cur_result,
                'pre_result' => $pre_result,
                'cur_total_to' => intval($cur_total_to),
                'pre_total_to' => intval($pre_total_to),
                'cur_total_ts' => number_format($cur_total_ts,2),
                'pre_total_ts' => number_format($pre_total_ts,2),
                'cur_period' => $cur_period,
                'pre_period' => $pre_period,
                'cur_tb_head' => $cur_tb_head,
                'pre_tb_head' => $pre_tb_head,
                'percentage_to' => array('percentage_to' => round($percentage_to,2), 'increased_to' => $increased_to),
                'percentage_ts' => array('percentage_ts' => round($percentage_ts,2), 'increased_ts' => $increased_ts)
            );

            $data = array("success" => 1, "chartdata" => $reschart);
            generate_json($data);
            exit();
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_order_and_sales_table(){
        parent::isLoggedIn();    
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));   
        $type = $this->input->post('type');
        $datetype = $this->input->post('datetype');
        $pmethodtype = $this->input->post('pmethodtype');
        
        if($this->session->sys_shop_id == 0){
            $shop_id = $this->input->post('shop_id');
            $branch_id = $this->input->post('branch_id');
        }
        else{
            $shop_id = $this->session->sys_shop_id;

            if($this->session->branchid != 0){
                $branch_id = $this->session->branchid;
            }
            else{
                $branch_id = $this->input->post("branch_id");
            }
        }       

        $data = $this->Model_order_and_sales->get_order_and_sales_table($fromdate, $todate, $shop_id, $branch_id, $type, $datetype, $pmethodtype);
        echo json_encode($data);
    }

    public function export_order_and_sales_table(){
        parent::isLoggedIn();    

        $filters = json_decode($this->input->post('_filters'));
        $fromdate = sanitize($filters->fromdate);
        $todate = sanitize($filters->todate);    
        $pmethodtype = $filters->pmethodtype; 
        $type = $filters->type;    
        $datetype = $filters->datetype;    
        $datetype_label = ($datetype == 'date_ordered') ? 'Date Ordered' : 'Payment Date';

        if($this->session->sys_shop_id == 0){
            $shop_id = $filters->shop_id;
            if($shop_id != "all"){
                $branch_id = $filters->branch_id;
            }
            else{
                $branch_id = null;
            }    
        }
        else{
            $shop_id = $this->session->sys_shop_id;
            if($this->session->branchid != 0){
                $branch_id = $this->session->branchid;
            }
            else{
                $branch_id = $filters->branch_id;
            }            
        } 

        $order_and_sales = $this->Model_order_and_sales->get_order_and_sales_table($fromdate,$todate,$shop_id,$branch_id,$type,$datetype,$pmethodtype,true);   

        $fromshop = "";

        if($shop_id != 0 && $shop_id != "0" && $shop_id != "all"){
            $fromshop.=" with filters Shop = '".parent::getShopName($shop_id)."'";
        }

        $remarks = 'Order and Sales has been exported into excel'.$fromshop.', Dated '.$fromdate.' to '.$todate;
        $this->audittrail->logActivity('Order and Sales Report', $remarks, 'export', $this->session->userdata('username'));     


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if($type == "summary"){
            $sheet->setCellValue('B1', 'Order and Sales (Summary)');
        }
        else{
            $sheet->setCellValue('B1', 'Order and Sales (Logs)');
        }

        if($fromdate == $todate){
            $sheet->setCellValue('B2', $fromdate);
        }
        else{
            $sheet->setCellValue('B2', $fromdate.' - '.$todate);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        if($shop_id != 0){
            $sheet->setCellValue('A4', 'Shop Name');
            $sheet->getStyle('A4')->getFont()->setBold(true);
            $sheet->setCellValue('B4', parent::getShopName($shop_id));
        }       

        if($type == "logs"){
            if($shop_id == 0){
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);                                
            }
            else{                                
                if($branch_id == 0){
                    $sheet->getColumnDimension('D')->setAutoSize(true);                    
                }
            }
        }
        else{            
            if($shop_id != "all"){
                $sheet->setCellValue('A5', 'Branch Name');
                $sheet->getStyle('A5')->getFont()->setBold(true);
                if($branch_id != "all"){
                    $sheet->setCellValue('B5', parent::getBranchName($branch_id == 'main' ? 0 : $branch_id )); 
                }
                else{
                    $sheet->setCellValue('B5', "All Branches"); 
                }                
            }            
        }

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A7:E7')->getFont()->setBold(true);

        if($type == "summary"){
            $sheet->setCellValue('A7', $datetype_label);
            $sheet->setCellValue('B7', 'Total Orders');
            $sheet->setCellValue('C7', 'Total Amount');
        }
        else{
            if($shop_id == 0){                         
                $sheet->setCellValue('A7', $datetype_label);
                $sheet->setCellValue('B7', 'Shop Name');
                $sheet->setCellValue('C7', 'Branch Name');
                $sheet->setCellValue('D7', 'Total Orders');
                $sheet->setCellValue('E7', 'Total Amount');             
            }    
            else{
                $sheet->setCellValue('A7', $datetype_label);                
                $sheet->setCellValue('B7', 'Branch Name');
                $sheet->setCellValue('C7', 'Total Orders');
                $sheet->setCellValue('D7', 'Total Amount');
            }
        }

        $exceldata= array();
        $counter  = 8;
        foreach ($order_and_sales['data'] as $key => $row) {

            if($type == "summary"){
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[3],
                    '3' => $row[4]
                );
            }
            else{
                if($shop_id == 0){
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
                    );
                }
            }
            $counter++;
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A8');

        if($type == "summary"){
            $sheet->getStyle('B'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('B'.$counter, $order_and_sales['total_orders']);   
            $sheet->getStyle('C'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('C'.$counter, $order_and_sales['total_amount']); 
        }
        else{
            if($shop_id == 0){                    
                $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('D'.$counter, $order_and_sales['total_orders']);   
                $sheet->getStyle('E'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('E'.$counter, $order_and_sales['total_amount']);                      
            }
            else{                    
                $sheet->getStyle('C'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('C'.$counter, $order_and_sales['total_orders']);   
                $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('D'.$counter, $order_and_sales['total_amount']);  
            }
        }

        $row_count = count($exceldata)+9;
        for ($i=7; $i < $row_count; $i++) {               
            if($type == "summary" || $this->session->branchid != 0){
                $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
            }
            else{
                if($this->session->sys_shop_id == 0){                    
                    $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);                        
                }
                else{                    
                    if($this->session->branchid == 0){
                        $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
                    }                    
                }
            }       
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Order and Sales';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }  

}