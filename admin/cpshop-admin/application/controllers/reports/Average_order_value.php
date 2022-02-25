<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Average_order_value extends MY_Controller {
    //controller extended to MY_Controller in core folder

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_average_order_value');        
    } 
    
    public function index($token = ""){    
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['aov']['view'] == 1){          
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
            $this->load->view('reports/average_order_value_report',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_average_order_value(){
        parent::isLoggedIn();
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['por']['view'] == 1){
            $fromdate = date('Y-m-d',strtotime('2020-07-02'));
            $todate = date('Y-m-d',strtotime('2020-09-02'));
        
            $rows = $this->Model_average_order_value->get_order_values_data($fromdate,$todate);
            echo json_encode($rows);
        }else{
            $this->load->view('error_404');
        }
    }    
    
    public function get_average_order_value_data(){
        parent::isLoggedIn();
        if($this->loginstate->get_access()['aov']['view'] == 1){
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

                $cur_result = $this->Model_average_order_value->get_order_values($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);                
                $pre_result = $this->Model_average_order_value->get_order_values($p_fromdate,$p_todate,$shop_id,$branch_id,$pmethodtype);
        
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

                //post process dates and total_amount
                $new_cur_result = [];
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;                    
                    $new_cur_date = "";
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                        }
                    }
                    $new_cur_result[] = array(
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                    
                }           
                $new_pre_result = [];
                foreach($pre_dates as $date){                                        
                    $pre_total_amount = 0;                    
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d H:00',strtotime($result['date_ordered']))){
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                        }
                    }
                    $new_pre_result[] = array(
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }
        
                $cur_data = generateArrayIn($new_cur_result,$cur_dates,'date_ordered','total_amount');
                $pre_data = generateArrayIn($new_pre_result,$pre_dates,'date_ordered','total_amount');
        
                $cur_total = getTotalInArray($cur_data,'total_amount');
                $pre_total = getTotalInArray($pre_data,'total_amount');
                
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
        
                $cur_result = $this->Model_average_order_value->get_order_values($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);
                $pre_result = $this->Model_average_order_value->get_order_values($p_fromdate,$p_todate,$shop_id,$branch_id,$pmethodtype);               

                $cur_dates = generateDates($fromdate,$todate);
                $pre_dates = generateDates($p_fromdate,$p_todate);

                //post process dates and total_amount
                $new_cur_result = [];                
                foreach($cur_dates as $date){                    
                    $cur_total_amount = 0;                           
                    $new_cur_date = "";                    
                    foreach($cur_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $cur_total_amount += floatval($result['total_amount']);
                            $new_cur_date = $result['date_ordered'];
                        }
                    }
                    $new_cur_result[] = array(
                        'date_ordered' => $new_cur_date,
                        'total_amount' => $cur_total_amount
                    );                    
                }
                $new_pre_result = [];
                foreach($pre_dates as $date){                    
                    $pre_total_amount = 0;  
                    $new_pre_date = "";                   
                    foreach($pre_result as $result){
                        if($date == date('Y-m-d',strtotime($result['date_ordered']))){
                            $pre_total_amount += floatval($result['total_amount']);
                            $new_pre_date = $result['date_ordered'];
                        }
                    }
                    $new_pre_result[] = array(
                        'date_ordered' => $new_pre_date,
                        'total_amount' => $pre_total_amount
                    );
                }

                $cur_data = generateArrayIn($cur_result,$cur_dates,'date_ordered','total_amount');
                $pre_data = generateArrayIn($pre_result,$pre_dates,'date_ordered','total_amount');
        
                $cur_total = getTotalInArray($cur_data,'total_amount');
                $pre_total = getTotalInArray($pre_data,'total_amount');
                
                $cur_tb_head = date('m/d',strtotime($fromdate)).' - '.date('m/d/Y',strtotime($todate));
                $pre_tb_head = date('m/d',strtotime($p_fromdate)).' - '.date('m/d/Y',strtotime($p_todate));
        
                $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
                $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
            }
        
            $size = sizeof($dates);
        
            $cur_ave = floatval($cur_total)/$size;
            $pre_ave = floatval($pre_total)/$size;
        
            $increased = false;
            $percentage = 0.0;
            $diff = 0.0;

            if($cur_ave >= $pre_ave){
                $diff = $cur_ave-$pre_ave;
                $increased = true;
            }
            else{
                $diff = $pre_ave-$cur_ave;        
            }
        
            if($pre_ave > 0){
                $percentage = ($diff/$pre_ave)*100;
            }
            else{
                $percentage = 100;
            }         
            $step = get_StepSize(array_column($cur_data, 'total_amount'), array_column($pre_data, 'total_amount'), 4);
            $reschart = array(
                'step' => $step,
                'dates' => $dates,        
                'cur_dates' => $cur_dates,
                'pre_dates' => $pre_dates,
                'cur_total' => $cur_total,
                'pre_total' => $pre_total,
                'current_data' => $cur_data,
                'previous_data' => $pre_data,
                'cur_result' => $cur_result,
                'pre_result' => $pre_result,
                'cur_ave' => number_format($cur_ave,2),
                'pre_ave' => number_format($pre_ave,2),
                'diff' => number_format($diff,2),
                'cur_period' => $cur_period,
                'pre_period' => $pre_period,
                'cur_tb_head' => $cur_tb_head,
                'pre_tb_head' => $pre_tb_head,
                'percentage' => array('percentage' => number_format(round($percentage,2),2), 'increased' => $increased),
                'shop_id' => $shop_id,
                'branch_id' => $branch_id
            );
    
            $data = array("success" => 1, "chartdata" => $reschart);
            generate_json($data);
            exit();

        }else{
            $this->load->view('error_404');
        }
    }
    
    public function get_average_order_value_table(){
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
    
        $data = $this->Model_average_order_value->get_order_values_table($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype);
        echo json_encode($data);
    }
    
    public function export_average_order_table(){
        parent::isLoggedIn();
    
        $filters = json_decode($this->input->post('_filters'));
        $fromdate = sanitize($filters->fromdate);
        $todate = sanitize($filters->todate);
        $type = $filters->type;
        $pmethodtype = $filters->pmethodtype;

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
    
        $average_order = $this->Model_average_order_value->get_order_values_table($fromdate,$todate,$shop_id,$branch_id,$type,$pmethodtype,true);
    
        $fromshop = "";
    
        if($shop_id !== 0 && $shop_id !== "0" && $shop_id !== "all"){
            $fromshop.=" with filters Shop = '".parent::getShopName($shop_id)."'";
        }
    
        $remarks = 'Average Order Value has been exported into excel'.$fromshop.', Dated from '.$fromdate.' to '.$todate;
        $this->audittrail->logActivity('Average Order Value Report', $remarks, 'export', $this->session->userdata('username')); 
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();       
        
        if($type == "summary"){
            $sheet->setCellValue('B1', 'Average Order Value (Summary)');
        }
        else{
            $sheet->setCellValue('B1', 'Average Order Value (Logs)');
        }

        if($fromdate == $todate){
            $sheet->setCellValue('B2', $fromdate);
        }
        else{
            $sheet->setCellValue('B2', $fromdate.' - '.$todate);
        }        
        
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);       
        
        if($this->session->sys_shop_id != 0){
            $sheet->setCellValue('A4', 'Shop Name');
            $sheet->getStyle('A4')->getFont()->setBold(true);
            $sheet->setCellValue('B4', parent::getShopName($shop_id));
        }
        
        if($type == "logs"){
            if($this->session->sys_shop_id == 0){
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);                                
            }
            else{                
                if($this->session->branchid == 0){
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

        if($type == "summary" || $this->session->branchid != 0){
            $sheet->setCellValue('A7', 'Date Ordered');
            $sheet->setCellValue('B7', 'Total Amount');
        }
        else{
            if($this->session->sys_shop_id == 0){                         
                $sheet->setCellValue('A7', 'Date Ordered');    
                $sheet->setCellValue('B7', 'Shop Name');    
                $sheet->setCellValue('C7', 'Branch Name');
                $sheet->setCellValue('D7', 'Total Amount');                
            }    
            else{
                $sheet->setCellValue('A7', 'Date Ordered');    
                $sheet->setCellValue('B7', 'Branch Name');
                $sheet->setCellValue('C7', 'Total Amount');
            }
        }        
    
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A7:D7')->getFont()->setBold(true);
    
        $exceldata= array();
        $counter  = 8;
        foreach ($average_order['data'] as $key => $row) {

        
            if($type == "summary" || $this->session->branchid != 0){
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[3]
                );
            }
            else{
                if($this->session->sys_shop_id == 0){
                    if($type == "logs"){
                        $resultArray = array(
                            '1' => $row[0],
                            '2' => $row[1],
                            '3' => $row[2],
                            '4' => $row[3]
                        );    
                    }                
                }
                else{
                    if($this->session->branchid == 0){
                        $resultArray = array(
                            '1' => $row[0],
                            '2' => $row[2],
                            '3' => $row[3]
                        );
                    }
                }
            }
            $counter++; 
            $exceldata[] = $resultArray;
          
        }
    
        $sheet->fromArray($exceldata, null, 'A8');

      
        $row_count = count($exceldata)+8;
        for ($i=7; $i < $row_count; $i++) {
            if($type == "summary" || $this->session->branchid != 0){
                $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
        
            }
            else{
                if($this->session->sys_shop_id == 0){                    
                    $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
                                        
                }
                else{                    
                    if($this->session->branchid == 0){
                        $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
                    }                    
                }
            }                       
        }

    //Get Total Sum 
        if($type == "summary" || $this->session->branchid != 0){
            $sheet->setCellValue('A'.$counter, '');   
            $sheet->getStyle('B'.$counter)->getFont()->setBold(true);
            $sheet->getStyle('B'.$counter)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
            $sheet->setCellValue('B'.$counter, $average_order['total_amount']);   
        }
        else{
            if($this->session->sys_shop_id == 0){
                if($type == "logs"){
                    $sheet->setCellValue('A'.$counter, ''); 
                    $sheet->setCellValue('B'.$counter, ''); 
                    $sheet->setCellValue('C'.$counter, ''); 
                    $sheet->getStyle('D'.$counter)->getFont()->setBold(true);
                    $sheet->getStyle('D'.$counter)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
                    $sheet->setCellValue('D'.$counter, $average_order['total_amount']);          
                }                
            }
            else{
                if($this->session->branchid == 0){
                    $sheet->setCellValue('A'.$counter, ''); 
                    $sheet->setCellValue('B'.$counter, ''); 
                    $sheet->getStyle('C'.$counter)->getFont()->setBold(true);
                    $sheet->getStyle('C'.$counter)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);    
                    $sheet->setCellValue('C'.$counter, $average_order['total_amount']);   
                }
            }
        }

    
    
        $writer = new Xlsx($spreadsheet);
        $filename = 'Average Order Value';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }  

}