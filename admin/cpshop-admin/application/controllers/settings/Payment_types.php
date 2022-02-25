<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Payment_types extends CI_Controller {
	public function __construct(){
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('setting/model_payment_types', 'model_payment_types');
    }

	public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    public function index() {
		if($this->session->userdata('isLoggedIn') == true) {
			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}

		$this->load->view('login');
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




	public function view($token = '')
	{
		$this->isLoggedIn();
		if ($this->loginstate->get_access()['payment_type']['view'] == 1){
			//start - for restriction of views
	        $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';
	        $main_nav_id = $this->views_restriction($content_url);
	        //end - for restriction of views main_nav_id

	        // start - data to be used for views
	        $data_admin = array(
	            'token' => $token,
	            'main_nav_id' => $main_nav_id, //for highlight the navigation
	            // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
	        );
	        // end - data to be used for views

	        // start - load all the views synchronously
	        $this->load->view('includes/header', $data_admin);
	        $this->load->view('settings/settings_payment_types', $data_admin);
	        // end - load all the views synchronously
    	}else{
    		$this->load->view('error_404');
    	}
	}

	public function payment_types_table() 
	{
		$this->isLoggedIn();
		$filters = [
			'_record_status' => $this->input->post('_record_status'),
			'_code' => $this->input->post('_code'),
			'_payment_type' => $this->input->post('_payment_type'),
			'_date_from' => ($this->input->post('_date_from') != "") ? date("Y-m-d 00:00:00", strtotime($this->input->post('_date_from'))) : "",
			'_date_to' => ($this->input->post('_date_to') != "") ? date("Y-m-d 23:59:59", strtotime($this->input->post('_date_to'))) : "",
		];
        $query = $this->model_payment_types->payment_types_table($filters, $_REQUEST);
        generate_json($query);
	}

	public function export_payment_types_table() 
	{
		$this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
		$query = $this->model_payment_types->payment_types_table($filters, $requestData, true);
		// print_r($query);
        // exit();
		$fromdate = $filters['_date_from'];
		$todate = $filters['_date_to'];

		$fromdate = date("Y-m-d", strtotime($fromdate));
		$todate = date("Y-m-d", strtotime($todate));
		
		$fil_arr = [
			'Code' => $filters['_code'],
			'Payment Type' => $filters['_payment_type'],
            'Record Status' => array(
				'' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
			)[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks("", "", $fromdate, $todate, "Payment Types", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Payment Types', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Payment Types");
        $sheet->setCellValue('B2', "Filters: $_filters");
        $sheet->setCellValue('B3', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        // $sheet->getColumnDimension('F')->setWidth(15);

        $sheet->setCellValue("A6", 'Code');
        $sheet->setCellValue('B6', 'Payment Type');
        $sheet->setCellValue('C6', 'Date Created');
        $sheet->setCellValue('D6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Payment Types ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
	}

	public function create_data()
	{
		$this->isLoggedIn();
		if ($this->model_payment_types->create_data()) {
			$data = array("success" => 1, 'message' => "Added successfully!");
			$this->audittrail->logActivity('Payment Types', $this->input->post('_add_code')." Payment Type has been added successfully.", 'add', $this->session->userdata('username'));
		}
		else {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}

		generate_json($data);
	}

	public function get_data()
	{
		$this->isLoggedIn();
		$id = sanitize($this->input->post('id'));
		$result = $this->model_payment_types->get_data($id);
		generate_json($result);
	}

	public function update_data()
	{
		$this->isLoggedIn();
		
		$cur_val = [
			'code' => sanitize($this->input->post('_edit_code')),
			'payment' => sanitize($this->input->post('_edit_payment'))
		];

		$prev_val = (array) json_decode($this->input->post('prev_val'));

		$main = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val), $prev_val);

		if ($this->model_payment_types->update_data()) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$this->audittrail->logActivity('Payment Types', "Payment Type ".$prev_val['code']." has been updated successfully. \nChanges: \n$main", 'update', $this->session->userdata('username'));
		}
		else {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}

		generate_json($data);
	}

	public function disable_data()
	{
		$this->isLoggedIn();
		$disable_id = sanitize($this->input->post('disable_id'));
		$record_status = sanitize($this->input->post('record_status'));
		$record_name = sanitize($this->input->post('record_name'));

		if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
        }

        if ($disable_id > 0 && $record_status > 0) {
        	if ($this->model_payment_types->disable_data($disable_id, $record_status)) {
        		$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Payment Types', "Payment Type $record_name has been $record_text successfully.", $record_text, $this->session->userdata('username'));
        	}
        	else {
        		$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        	}
        }
        else {
        	$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }

        generate_json($data);
	}

	public function delete_data()
	{
		$this->isLoggedIn();
		$delete_id = sanitize($this->input->post('delete_id'));
		$record_name = sanitize($this->input->post('record_name'));
		if ($delete_id > 0) {
			if ($this->model_payment_types->delete_data($delete_id)) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Payment Types', "Payment Type $record_name has been deleted successfully.", 'delete', $this->session->userdata('username'));
			}
			else {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}
		}
		else {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}

		generate_json($data);
	}
}