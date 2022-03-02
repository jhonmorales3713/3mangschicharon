<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Main_products extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_packages');
        $this->load->model('user/model_tickets');
        $this->load->model('model');
        $this->load->model('model_dev_settings');
        $this->load->model('products/model_products');
    }

    
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function products_home($labelname = null){
        header('location:'.base_url('admin/Main_products/'));
        $this->session->set_userdata('active_page',$labelname);
    }    

    public function index(){

        $this->isLoggedIn();

        $data = array(
            'active_page' => $this->session->userdata('active_page'),
            'subnav' => true, //for highlight the navigation,
            'token' => $this->session->userdata('token_session')
        );
        $this->session->set_userdata('current_location',base_url('admin/Main_products/'));
        $data['page_content'] = $this->load->view('admin/dashboard/index',$data,TRUE);
		$this->load->view('admin_template',$data,'',TRUE);
    }

    
    public function export_product_table()
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $request = url_decode(json_decode($this->input->post("_search")));
        $member_id = $this->session->userdata('sys_users_id');
        $filter_text = "";
      
        // $sys_shop = $this->model_products->get_sys_shop($member_id);
        $sys_shop = $this->session->userdata('sys_shop');
        
        if($sys_shop == 0) {
            $query = $this->model_products->product_table($sys_shop, $request, true);
        }else{
            $query = $this->model_products->product_table($sys_shop, $request, true);
        }

        // $_record_status = ($this->input->post('_record_status') == 1 && $this->input->post('_record_status') != '') ? "Enabled":"Disabled";

        if($this->input->post('_record_status') == ''){
            $_record_status = 'All Records';
        }
        else if($this->input->post('_record_status') == 1){
            $_record_status = 'Enabled';
        }
        else if($this->input->post('_record_status') == 2){
            $_record_status = 'Disabled';
        }
        else{
            $_record_status = '';
        }

		$_name 			= ($this->input->post('_name') == "") ? "":"'" . $this->input->post('_name') ."'";
        $_shops 		= ($this->input->post('_shops') == "") ? "All Shops":array_get($query, 'data.0.5');
        
        /// for details column in audit trail
        if($_name != ''){
            $filter_text .= $_record_status.' in '.$_shops. ', Product Name: '.$_name;
        }else{
            $filter_text .= $_record_status.' in '.$_shops;
        }
        
        $sheet->setCellValue('B1', "Products");
        $sheet->setCellValue('B2', "Filter: '$_name', $_record_status in $_shops");
        $sheet->setCellValue('B3', date('Y/m/d'));
        
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->setCellValue('A6', 'Product Name');
        $sheet->setCellValue('B6', 'Category');
        $sheet->setCellValue('C6', 'Price');
        $sheet->setCellValue('D6', 'No of Stock');
        $sheet->setCellValue('E6', 'Shop Name');
        $sheet->setCellValue('F6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);

        // print_r($query);
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
        $filename = 'Products ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Product List', 'Products has been exported into excel with filter '.$filter_text, 'export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function products($token = ''){
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products->get_sys_shop($member_id),
                'shopcode'            => $this->model_products->get_shopcode($member_id),
                'categories'          => $this->model_products->get_category_options(),
            );

            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/products/products',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
        }else{
            $this->load->view('error_404');
        }
    }


    public function product_table()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products->get_sys_shop($member_id);
        
        $query = $this->model_products->product_table($sys_shop, $request);
        
        
        generate_json($query);
    }
    public function views_restriction($content_url)
    {
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
}