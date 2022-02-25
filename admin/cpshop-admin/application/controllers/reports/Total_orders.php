<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Total_orders extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_total_orders');        
    }
  
    public function index($token = ""){
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['to']['view'] == 1){        
        $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
        $main_nav_id = parent::views_restriction($content_url);
        
        $data_admin = array(
        'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            'shops' => parent::getShops(),            
            'setDate' => [
                'fromdate' => (isset($_GET['fromdate']) ? $_GET['fromdate'] : null),
                'todate' => (isset($_GET['todate']) ? $_GET['todate'] : null),
            ]
        );

        $this->load->view('includes/header',$data_admin);
        $this->load->view('reports/total_orders_report',$data_admin);

        } else {
            $this->load->view('error_404');
        }
    }    

    public function get_total_orders_data(){
        parent::isLoggedIn();
        if($this->loginstate->get_access()['to']['view'] == 1){
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
                
                $cur_result = $this->Model_total_orders->get_total_orders_data($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);
                $pre_result = $this->Model_total_orders->get_total_orders_data($p_fromdate,$p_todate,$shop_id,$branch_id,$pmethodtype);   

                $cur_range = array_column($cur_result,'date_ordered');
                $pre_range = array_column($pre_result,'date_ordered');
                
                if($cur_range && $pre_range){
                    $time_range = getTimeRange($cur_range,$pre_range);
                }else if($cur_range && !$pre_range){
                    $time_range = getTimeRange($cur_range,$cur_range);
                }else{
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
                    $cur_total_f_count = 0;
                    $cur_total_s_count = 0;                   
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            }                            
                        }
                    }
                    $new_cur_result[] = array(
                        'total_paid_orders' => $cur_total_count,
                        'total_fulfilled_orders' => $cur_total_f_count,
                        'total_delivered_orders' => $cur_total_s_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $pre_total_f_count = 0;
                    $pre_total_s_count = 0;
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            } 
                        }
                    }
                    $new_pre_result[] = array(
                        'total_paid_orders' => $pre_total_count,
                        'total_fulfilled_orders' => $pre_total_f_count,
                        'total_delivered_orders' => $pre_total_s_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_paid_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_paid_orders');
        
                $cur_data_f = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_fulfilled_orders');
                $cur_data_d = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_delivered_orders');
        
                $pre_data_f = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_fulfilled_orders');
                $pre_data_d = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_delivered_orders');

                $cur_total = getTotalInArray($new_cur_result,'total_paid_orders');
                $cur_total_f = getTotalInArray($new_cur_result,'total_fulfilled_orders');
                $cur_total_d = getTotalInArray($new_cur_result,'total_delivered_orders');
        
                $pre_total = getTotalInArray($new_pre_result,'total_paid_orders');
                $pre_total_f = getTotalInArray($new_pre_result,'total_fulfilled_orders');
                $pre_total_d = getTotalInArray($new_pre_result,'total_delivered_orders');
                
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

            }else{ 

                $dates = generateDates($fromdate,$todate,'M d');

                $cur_result = $this->Model_total_orders->get_total_orders_data($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);
                $pre_result = $this->Model_total_orders->get_total_orders_data($p_fromdate,$p_todate,$shop_id,$branch_id,$pmethodtype);      
        
                $cur_dates = generateDates($fromdate,$todate);
                $pre_dates = generateDates($p_fromdate,$p_todate);

                //post process dates and total_amount and total_orders
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;
                    $cur_total_count = 0;  
                    $cur_total_f_count = 0;
                    $cur_total_s_count = 0;                   
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $cur_total_count++;
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            }                            
                        }
                    }
                    $new_cur_result[] = array(
                        'total_paid_orders' => $cur_total_count,
                        'total_fulfilled_orders' => $cur_total_f_count,
                        'total_delivered_orders' => $cur_total_s_count,
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                   
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){ 
                    $pre_total_amount = 0; 
                    $pre_total_count = 0;  
                    $pre_total_f_count = 0;
                    $pre_total_s_count = 0;
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $pre_total_count++;
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                            if($result['order_status'] == 'f'){
                                $cur_total_f_count++;
                            }                            
                            if($result['order_status'] == 's'){
                                $cur_total_s_count++;
                            } 
                        }
                    }
                    $new_pre_result[] = array(
                        'total_paid_orders' => $pre_total_count,
                        'total_fulfilled_orders' => $pre_total_f_count,
                        'total_delivered_orders' => $pre_total_s_count,
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_paid_orders');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_paid_orders');
        
                $cur_data_f = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_fulfilled_orders');
                $cur_data_d = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_delivered_orders');
        
                $pre_data_f = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_fulfilled_orders');
                $pre_data_d = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_delivered_orders');
        
                $cur_total = getTotalInArray($new_cur_result,'total_paid_orders');
                $cur_total_f = getTotalInArray($new_cur_result,'total_fulfilled_orders');
                $cur_total_d = getTotalInArray($new_cur_result,'total_delivered_orders');
        
                $pre_total = getTotalInArray($new_pre_result,'total_paid_orders');
                $pre_total_f = getTotalInArray($new_pre_result,'total_fulfilled_orders');
                $pre_total_d = getTotalInArray($new_pre_result,'total_delivered_orders');

                $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));

                $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
                $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
            }      

            $size = sizeof($cur_data);      

            $increased = false;
            $percentage = 0.0;      

            if($cur_total >= $pre_total){        
                $increased = true;        
            }
            $percentage = getPercentage($cur_total,$pre_total);    

            $increased_f = false;
            $percentage_f = 0.0;      

            if($cur_total_f >= $pre_total_f){        
                $increased_f = true;        
            }
            $percentage_f = getPercentage($cur_total_f,$pre_total_f);    

            $increased_d = false;
            $percentage_d = 0.0;      

            if($cur_total_d >= $pre_total_d){        
                $increased_d = true;
            }
            $percentage_d = getPercentage($cur_total_d,$pre_total_d);     

            $reschart = array(
                'dates' => $dates,
                'current_data' => $cur_data,
                'previous_data' => $pre_data,
                'cur_result' => $cur_result,
                'pre_result' => $pre_result,
                'cur_dates' => $cur_dates,
                'pre_dates' => $pre_dates,
                'cur_total' => intval($cur_total),
                'cur_total_f' => intval($cur_total_f),
                'cur_total_d' => intval($cur_total_d),        
                'pre_total' => $pre_total,
                'pre_total_f' => $pre_total_f,
                'pre_total_d' => $pre_total_d,          
                'cur_period' => $cur_period,
                'pre_period' => $pre_period,
                'cur_tb_head' => $cur_tb_head,
                'pre_tb_head' => $pre_tb_head,
                'percentage' => array('percentage' => number_format(round($percentage,2),2), 'increased' => $increased),
                'percentage_f' => array('percentage_f' => number_format(round($percentage_f,2),2), 'increased_f' => $increased_f),
                'percentage_d' => array('percentage_d' => number_format(round($percentage_d,2),2), 'increased_d' => $increased_d),
            );

            $data = array("success" => 1, "chartdata" => $reschart);
            generate_json($data);
            exit();
            }else{
            $this->load->view('error_404');
        }
    }

    public function get_total_orders_table(){
        parent::isLoggedIn();
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate'));   
        $type = $this->input->post('type');
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

        $data = $this->Model_total_orders->get_total_orders_table($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype);
        echo json_encode($data);
    }

    public function export_total_orders_table(){
        parent::isLoggedIn();

        $filters = json_decode($this->input->post('_filters'));
        $fromdate = sanitize($filters->fromdate);
        $todate = sanitize($filters->todate);   
        $pmethodtype = $filters->pmethodtype; 
        $type = $filters->type;
        
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
        
        $total_orders = $this->Model_total_orders->get_total_orders_table($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype, true);
        $fromshop = "";

        if($shop_id != 0 && $shop_id != "0" && $shop_id != "all"){
            $fromshop.=" with filters Shop = '".parent::getShopName($shop_id)."'";
        }

        $remarks = 'Total orders has been exported into excel'.$fromshop.', Dated '.$fromdate.' to '.$todate;
        $this->audittrail->logActivity('Total Orders Report', $remarks, 'export', $this->session->userdata('username'));     

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if($type == "summary"){
            $sheet->setCellValue('B1', 'Total Orders (Summary)');
        }
        else{
            $sheet->setCellValue('B1', 'Total Orders (Logs)');
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
        $sheet->getColumnDimension('F')->setAutoSize(true);

        if($shop_id != 0){
            $sheet->setCellValue('A4', 'Shop Name');
            $sheet->getStyle('A4')->getFont()->setBold(true);
            $sheet->setCellValue('B4', parent::getShopName($shop_id));
        }       

        if($type == "logs"){
            if($shop_id == 0){
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);                                
            }
            else{                                
                if($branch_id == 0){
                    $sheet->getColumnDimension('C')->setAutoSize(true);                    
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
        $sheet->getStyle('A7:F7')->getFont()->setBold(true);

        if($type == "summary"){
            $sheet->setCellValue('A7', 'Date Ordered');
            $sheet->setCellValue('B7', 'Total Paid Orders');
            $sheet->setCellValue('C7', 'Total Fulfilled Orders');
            $sheet->setCellValue('D7', 'Total Delivered Orders');
        }
        else{
            if($shop_id == 0){                         
                $sheet->setCellValue('A7', 'Date Ordered');
                $sheet->setCellValue('B7', 'Shop Name');
                $sheet->setCellValue('C7', 'Branch Name');
                $sheet->setCellValue('D7', 'Total Paid Orders');
                $sheet->setCellValue('E7', 'Total Fulfilled Orders');
                $sheet->setCellValue('F7', 'Total Delivered Orders');                
            }    
            else{
                $sheet->setCellValue('A7', 'Date Ordered');                
                $sheet->setCellValue('B7', 'Branch Name');
                $sheet->setCellValue('C7', 'Total Paid Orders');
                $sheet->setCellValue('D7', 'Total Fulfilled Orders');
                $sheet->setCellValue('E7', 'Total Delivered Orders'); 
            }
        }

        $exceldata= array();
        $counter  = 8;
        foreach ($total_orders['data'] as $key => $row) {
            if($type == "summary"){
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[3],
                    '3' => $row[4],
                    '4' => $row[5]
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
                        '6' => $row[5]
                    );    
                }
                else{
                    $resultArray = array(
                        '1' => $row[0],                            
                        '2' => $row[2],
                        '3' => $row[3],
                        '4' => $row[4],
                        '5' => $row[5]
                    );
                }
            }           
            $counter++; 
            $exceldata[] = $resultArray;        
        }

        $sheet->fromArray($exceldata, null, 'A8');     
        
        if($type == "summary"){
            $sheet->getStyle('B'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('B'.$counter, $total_orders['t_p_orders']);   
            $sheet->getStyle('C'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('C'.$counter, $total_orders['t_f_orders']); 
            $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
            $sheet->setCellValue('D'.$counter, $total_orders['t_d_orders']); 
        }
        else{
            if($shop_id == 0){                    
                $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('D'.$counter, $total_orders['t_p_orders']);   
                $sheet->getStyle('E'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('E'.$counter, $total_orders['t_f_orders']); 
                $sheet->getStyle('F'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('F'.$counter, $total_orders['t_d_orders']);                     
            }
            else{                    
                $sheet->getStyle('C'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('C'.$counter, $total_orders['t_p_orders']);   
                $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('D'.$counter, $total_orders['t_f_orders']); 
                $sheet->getStyle('E'.$counter)->getFont()->setBold(true);
                $sheet->setCellValue('E'.$counter, $total_orders['t_d_orders']); 
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Total Orders';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

}