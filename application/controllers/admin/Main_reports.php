<?php

date_default_timezone_set('Asia/Manila');
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Main_reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('reports/Model_reports');
        $this->load->model('orders/model_orders');
        $this->load->model('products/model_products');
        $this->load->library('upload');
        $this->load->library('uuid');
        $this->load->library('pdf');
        $this->load->library('excel');
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            
            // header("location:" . base_url('Main/logout'));
        }
    }

    public function reports_home($labelname = null){
        $this->session->set_userdata('active_page',$labelname);
        header('location:'.base_url('admin/Main_reports/'));
    }

    public function sales_report($token = ""){

        $this->isLoggedIn();
        if($this->loginstate->get_access()['sales_report']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/'. $this->uri->segment(3) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'cities'            => $this->model_orders->get_cities()->result_array()
            );


            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/reports/sales_report',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }

    public function inventory_report($token = ""){

        $this->isLoggedIn();
        if($this->loginstate->get_access()['sales_report']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/'. $this->uri->segment(3) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'cities'            => $this->model_orders->get_cities()->result_array(),
                'categories'          => $this->model_products->get_category_options()
            );


            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/reports/inventory_report',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }

    public function export_sales_report_table(){

        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $request = url_decode(json_decode($this->input->post("_record_status")));
        $filter_text = "";
      
        // $sys_shop = $this->model_products->get_sys_shop($member_id);
        
        $query = $this->Model_reports->sales_report_table($request,true);

        // print_r($query);
        // die();
        // $_record_status = ($this->input->post('_record_status') == 1 && $this->input->post('_record_status') != '') ? "Enabled":"Disabled";

        // if($this->input->post('_record_status') == ''){
        //     $_record_status = 'All Records';
        // }
        // else if($this->input->post('_record_status') == 1){
        //     $_record_status = 'Enabled';
        // }
        // else if($this->input->post('_record_status') == 2){
        //     $_record_status = 'Disabled';
        // }
        // else{
        //     $_record_status = '';
        // }

		$_search 			= ($this->input->post('_search') == "") ? "":"'" . $this->input->post('_search') ."'";
        // $_shops 		= ($this->input->post('_shops') == "") ? "All Shops":array_get($query, 'data.0.5');
        
        // /// for details column in audit trail
        // if($_name != ''){
        //     $filter_text .= $_record_status.' in '.$_shops. ', Product Name: '.$_name;
        // }else{
        //     $filter_text .= $_record_status.' in '.$_shops;
        // }
        
        $sheet->setCellValue('A1', "Sales Report As Of");
        $sheet->setCellValue('A2', date('Y/m/d'));
        
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->setCellValue('A6', 'Date Ordered');
        $sheet->setCellValue('B6', 'Order ID');
        $sheet->setCellValue('C6', 'Customer Name');
        $sheet->setCellValue('D6', 'Contact No.');
        $sheet->setCellValue('E6', 'City');
        $sheet->setCellValue('F6', 'Amount');
        $sheet->setCellValue('G6', 'Discount');
        $sheet->setCellValue('H6', 'Shipping Fee');
        $sheet->setCellValue('I6', 'Total');
        $sheet->setCellValue('J6', 'Payment Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);

        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[1],
                '2' => $row[2],
                '3' => $row[3],
                '4' => $row[4],
                '5' => ucwords($row[5]),
                '6' => ucwords($row[6])
            );
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Sales Report ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Product List', 'Products has been exported into excel with filter '.$filter_text, 'export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }
    public function export_inventory_report_table(){

        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $request = url_decode(json_decode($this->input->post("_record_status")));
        $filter_text = "";
      
        // $sys_shop = $this->model_products->get_sys_shop($member_id);
        
        $query = $this->Model_reports->inventory_report_table($request,true);

        // print_r($query);
        // die();
        // $_record_status = ($this->input->post('_record_status') == 1 && $this->input->post('_record_status') != '') ? "Enabled":"Disabled";

        // if($this->input->post('_record_status') == ''){
        //     $_record_status = 'All Records';
        // }
        // else if($this->input->post('_record_status') == 1){
        //     $_record_status = 'Enabled';
        // }
        // else if($this->input->post('_record_status') == 2){
        //     $_record_status = 'Disabled';
        // }
        // else{
        //     $_record_status = '';
        // }

		$_search 			= ($this->input->post('_search') == "") ? "":"'" . $this->input->post('_search') ."'";
        // $_shops 		= ($this->input->post('_shops') == "") ? "All Shops":array_get($query, 'data.0.5');
        
        // /// for details column in audit trail
        // if($_name != ''){
        //     $filter_text .= $_record_status.' in '.$_shops. ', Product Name: '.$_name;
        // }else{
        //     $filter_text .= $_record_status.' in '.$_shops;
        // }
        
        $sheet->setCellValue('A1', "Inventory Report as of");
        $sheet->setCellValue('A2', date('Y/m/d'));
        
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->setCellValue('A6', 'Product Name');
        $sheet->setCellValue('B6', 'Category');
        $sheet->setCellValue('C6', 'Available Qty');
        $sheet->setCellValue('D6', 'Date Manufactured');
        $sheet->setCellValue('E6', 'Date Expiration');
        $sheet->setCellValue('F6', 'Deducted Qty');
        $sheet->setCellValue('G6', 'Total Qty');
        $sheet->setCellValue('H6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:H6')->getFont()->setBold(true);

        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => ucwords($row[4]),
                '6' => ucwords($row[5]),
                '7' => ucwords($row[6]),
                '8' => ucwords($row[7])
            );
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Inventory Report ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Product List', 'Products has been exported into excel with filter '.$filter_text, 'export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }
    
    public function sales_report_table()
    {
        $this->isLoggedIn();
        
        $request = $_REQUEST;
        $query = $this->Model_reports->sales_report_table($request,false);
        
        
        generate_json($query);
    }
    
    public function inventory_report_table()
    {
        $this->isLoggedIn();
        
        $request = $_REQUEST;
        $query = $this->Model_reports->inventory_report_table($request,false);
        
        
        generate_json($query);
    }
    
    public function sales_report_pdf($post_data = '',$type = ''){

        $data = $this->input->post();
        $title = 'Sales Report';
        $filename = $title;        
        $request = url_decode(json_decode($this->input->post("_record_status")));
        // $request = url_decode(json_decode($this->input->post('filter')));
        //print_r( $this->model_products->product_table(0, $request, true)['data']);

        $view_data['data'] = $this->Model_reports->sales_report_table($request, TRUE)['data'];
        $view_data['date_from'] = $request['date_from'];
        $view_data['date_to'] = $request['date_to'];
        // print_r($view_data['data']);
        // print_r($request);
        // print_r($this->Model_reports->sales_report_table($request, TRUE));
        $page = $this->load->view('admin/reports/sales_report_pdf',$view_data,TRUE);
        $this->pdf->load_pdf($title, $page, $filename, TRUE, $data);
        
    }
    public function inventory_report_pdf($post_data = '',$type = ''){

        $data = $this->input->post();
        $title = 'Inventory Report';
        $filename = $title;        
        $request = url_decode(json_decode($this->input->post("_record_status")));
        // $request = url_decode(json_decode($this->input->post('filter')));
        //print_r( $this->model_products->product_table(0, $request, true)['data']);

        $view_data['data'] = $this->Model_reports->inventory_report_table($request, TRUE)['data'];
        $view_data['date_from'] = $request['date_from'];
        $view_data['date_to'] = $request['date_to'];
        // print_r($view_data['data']);
        // print_r($request);
        // print_r($this->Model_reports->sales_report_table($request, TRUE));
        $page = $this->load->view('admin/reports/inventory_report_pdf',$view_data,TRUE);
        $this->pdf->load_pdf($title, $page, $filename, TRUE, $data);
        
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

        if (in_array($content_url, $url_content_arr) == false){
            header("location:".base_url('Main/logout'));
        }else{
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }
    public function index(){

        $this->isLoggedIn();

        $data = array(
            'active_page' => $this->session->userdata('active_page'),
            'subnav' => true, //for highlight the navigation,
            'token' => $this->session->userdata('token_session')
        );
        $data['page_content'] = $this->load->view('admin/dashboard/index',$data,TRUE);
		$this->load->view('admin_template',$data,'',TRUE);
    }

    
}
?>