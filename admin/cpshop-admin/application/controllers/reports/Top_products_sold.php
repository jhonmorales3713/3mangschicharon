<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Top_products_sold extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('reports/Model_top_products_sold');
        $this->load->model('shops/Model_shops');
        $this->load->model('shop_branch/Model_shopbranch', 'model_branch');
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
    
    public function index($token = ""){    
        if($this->loginstate->get_access()['reports'] == 1 && $this->loginstate->get_access()['tps']['view'] == 1){        
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $user_id = $this->session->userdata('id'); $shops = []; $branches = null;
            $shopid = $this->model_shops->get_sys_shop($user_id);
            if ($shopid == 0) {
                $shops = $this->model_shops->get_shop_opts_oderbyname();
            }elseif ($shopid > 0) {
                $branches = $this->model_branch->get_branch_options($shopid);
            }

            $data_admin = array(
            'token' => $token,
                'main_nav_id' => $main_nav_id,
                'shops' => $shops,
                'branches' => $branches,
                'setDate' => [
                'fromdate' => (isset($_GET['fromdate']) ? $_GET['fromdate'] : null),
                'todate' => (isset($_GET['todate']) ? $_GET['todate'] : null),
                ]
            );
            
            $this->load->view('includes/header',$data_admin);
            $this->load->view('reports/top_products_sold_report',$data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_top_products_sold_data(){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['tps']['view'] == 1){
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
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

            $pre_date_range = generateDateRangeBackwards($fromdate,$todate);
            $p_fromdate = $pre_date_range['fromdate'];
            $p_todate = $pre_date_range['todate'];

            $p_fromdate = $p_fromdate->format('Y-m-d');
            $p_todate = $p_todate->format('Y-m-d');

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $dates = generateDates($fromdate,$todate,'M d');
            
            $cur_result = $this->Model_top_products_sold->get_top_products_sold($fromdate,$todate,$shop_id,$branch_id,$pmethodtype);
            $pre_result = $this->Model_top_products_sold->get_top_products_sold_in($p_fromdate,$p_todate,$shop_id,$branch_id,$cur_result,$pmethodtype);
            
            $cur_dates = generateDates($fromdate,$todate);
            $pre_dates = generateDates($p_fromdate,$p_todate);
            
            $cur_data = $cur_result;            

            if($cur_result != null){
                $pre_data = sortArrayLike($cur_result,$pre_result,'id');
            }
            else{                
                $pre_data = $pre_result;
            }      

            $size = sizeof($dates);
            
            if($fromdate == $todate){
                $cur_period = date('M d, Y',strtotime($todate));
                $pre_period = date('M d, Y',strtotime($p_todate));
                
                if($todate == date('Y-m-d')){
                    $cur_period = 'Today';
                    $pre_period = 'Yesterday';
                }                
            }
            else{
                $cur_period = date('M d',strtotime($fromdate)).' - '.date('M d, Y',strtotime($todate));
                $pre_period = date('M d',strtotime($p_fromdate)).' - '.date('M d, Y',strtotime($p_todate));
            }
            

            //$percentage = computePercentageInArray($cur_data,$pre_data,'qty');

            $reschart = array(
                'dates' => $dates,
                'cur_dates' => $cur_dates,
                'pre_dates' => $pre_dates,
                'cur_data' => $cur_data,
                'pre_data' => $pre_data,
                'cur_result' => $cur_result,
                'pre_result' => $pre_result,
                'cur_period' => $cur_period,
                'pre_period' => $pre_period
                //'percentage' => $percentage
            );

            $data = array("success" => 1, "chartdata" => $reschart);
            generate_json($data);
            exit();
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_top_products_sold_table(){
        $this->isLoggedIn();    
        $fromdate = sanitize($this->input->post('fromdate'));
        $todate = sanitize($this->input->post('todate')); 
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

        $data = $this->Model_top_products_sold->get_top_products_sold_table($fromdate, $todate, $shop_id, $branch_id, $pmethodtype);
        echo json_encode($data);
    }

    public function export_top_products_sold_table(){
        $this->isLoggedIn();

        $filters = json_decode($this->input->post('_filters'));
        $fromdate = sanitize($filters->fromdate);
        $todate = sanitize($filters->todate);    
        $pmethodtype = $filters->pmethodtype;
  
        if($this->session->sys_shop_id == 0){
            $shop_id = $filters->shop_id;
            $branch_id = $filters->branch_id;
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
      
        $top_products_sold = $this->Model_top_products_sold->get_top_products_sold_table($fromdate,$todate,$shop_id,$branch_id, $pmethodtype,true);    

        $fromshop = "";
        $pm_type = array(
            '' => 'All Payment Method', 'op' => 'Online Purchases', 'mp' => 'Manual Purchases'
        )[$pmethodtype];

        if($shop_id != 0 && $shop_id != "0" && $shop_id != "all"){
            $fromshop.=" with filters Shop = '".$this->Model_top_products_sold->getShopName($shop_id)."' Payment Method = $pm_type";
        }

        $remarks = 'Top Products Sold has been exported into excel'.$fromshop.', Dated '.$fromdate.' to '.$todate;
        $this->audittrail->logActivity('Top Products Sold Report', $remarks, 'export', $this->session->userdata('username'));  

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B1', 'Top Products Sold');
        $sheet->setCellValue('B2', $fromdate.' - '.$todate);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $sheet->setCellValue('A6', 'Item Name');
        $sheet->setCellValue('B6', 'Quantity');
        $sheet->setCellValue('C6', 'Total Sales');
        $sheet->setCellValue('D6', 'Last Sold');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);

        $exceldata= array();
        foreach ($top_products_sold['data'] as $key => $row) {

        $resultArray = array(
            '1' => $row[0],
            '2' => $row[1],
            '3' => $row[2],
            '4' => $row[3]
        );
        $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A7');

        $row_count = count($exceldata)+7;
        for ($i=6; $i < $row_count; $i++) {
            $sheet->getStyle("B$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);            
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Top Products Sold';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        return $writer->save('php://output');
        exit();
    }

}
