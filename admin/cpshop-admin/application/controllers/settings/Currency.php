<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Currency extends CI_Controller {
	public function __construct(){
        parent::__construct();
        //load model or libraries below here...
		$this->load->model('setting/model_currency', 'model_currency');
		$this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
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
		if ($this->loginstate->get_access()['currency']['view'] == 1){
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
	        $this->load->view('settings/settings_currency', $data_admin);
	        // end - load all the views synchronously
    	}else{
    		$this->load->view('error_404');
    	}
	}

	public function currency_table() 
	{
		$this->isLoggedIn();
		$filters = [
			'_record_status' => $this->input->post('_record_status'),
			'_code' => $this->input->post('_code'),
			'_country_name' => $this->input->post('_country_name')
		];
        $query = $this->model_currency->currency_table($filters, $_REQUEST);
        generate_json($query);
	}

	public function export_currency_table() 
	{
		$this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
		$query = $this->model_currency->currency_table($filters, $requestData, true);
		
		$fil_arr = [
			'Currency Code' => $filters['_code'],
			'Country Name' => $filters['_country_name'],
            'Record Status' => array(
				'' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
			)[$filters['_record_status']],
		];
		$fromdate = "None";
		$todate   = "None";

        extract($this->audittrail->get_ReportExportRemarks("", "", $fromdate, $todate, "Currency", $fil_arr));
        
        $this->audittrail->logActivity('Currency', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Currency");
        $sheet->setCellValue('B2', "Filters: $_filters");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        // $sheet->getColumnDimension('F')->setWidth(15);

        $sheet->setCellValue("A6", 'Country Name');
        $sheet->setCellValue('B6', 'Currency Code');
        $sheet->setCellValue('C6', 'Exchange Rate to PHP');
        $sheet->setCellValue('D6', 'Exchange Rate from PHP');
        $sheet->setCellValue('E6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
                '4' => $row[3],
                '5' => $row[4],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Currency ' . date('Y/m/d');
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

		$files = $_FILES;
		$file_name = '';
		if(!empty($_FILES)){
			$_FILES['userfile'] = [
				'name'     => $files['file_container']['name'],
				'type'     => $files['file_container']['type'],
				'tmp_name' => $files['file_container']['tmp_name'],
				'error'    => $files['file_container']['error'],
				'size'     => $files['file_container']['size']
			];
			$F[] = $_FILES['userfile'];
			
			//Upload requirements
			$config = array();
			$directory = 'assets/img';
			$config['file_name'] = $file_name;
			$config['upload_path'] = $directory;
			$config['allowed_types'] = '*';
			$config['max_size'] = 3000;
			$this->load->library('upload',$config, 'logo');
			$this->logo->initialize($config);

			
			$data = array();
			if(!$this->logo->do_upload()){

			}else{
				$file_name = $this->logo->data()['file_name'];
				
				///upload image to s3 bucket
				$fileTempName    = $files['file_container']['tmp_name'];
				$activityContent = 'assets/img/flags/'.$file_name;
				$uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

				if($uploadS3 != 1){
					$data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
					generate_json($data);
					die();
				}

				unlink($directory.'/'.$file_name);
			}
		}

		if ($this->model_currency->create_data($file_name)) {
			$data = array("success" => 1, 'message' => "Added successfully!");
			$this->audittrail->logActivity('Currency', $this->input->post('_add_country_name')." Currency has been added successfully.", 'add', $this->session->userdata('username'));
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
		$result = $this->model_currency->get_data($id);
		generate_json($result);
	}

	public function update_data()
	{
		$this->isLoggedIn();
		$files = $_FILES;
		$file_name = '';
		$img_upload = 0;
		$file_name = sanitize($this->input->post('_edit_filename'));
		if($_FILES['file_container_update']['size'] != 0){
			$_FILES['userfile'] = [
				'name'     => $files['file_container_update']['name'],
				'type'     => $files['file_container_update']['type'],
				'tmp_name' => $files['file_container_update']['tmp_name'],
				'error'    => $files['file_container_update']['error'],
				'size'     => $files['file_container_update']['size']
			];
			$F[] = $_FILES['userfile'];
			
			//Upload requirements
			$config = array();
			$directory = 'assets/img';
			$config['file_name'] = $file_name;
			$config['upload_path'] = $directory;
			$config['allowed_types'] = '*';
			$config['max_size'] = 3000;
			$this->load->library('upload',$config, 'logo');
			$this->logo->initialize($config);

			
			$data = array();
			if(!$this->logo->do_upload()){

			}else{
				$file_name = $this->logo->data()['file_name'];
				$img_upload = 1;
				///upload image to s3 bucket
				$fileTempName    = $files['file_container_update']['tmp_name'];
				$activityContent = 'assets/img/flags/'.$file_name;
				$uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);
				
				if($uploadS3 != 1){
					$data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
					generate_json($data);
					die();
				}

				unlink($directory.'/'.$file_name);
			}
		}

		$cur_val = [
			'country_name' => sanitize($this->input->post('_edit_country_name')),
			'currency' => sanitize($this->input->post('_edit_currency')),
			'currency_symbol' => sanitize($this->input->post('_edit_currency_symbol')),
			'country_code' => sanitize($this->input->post('_edit_country_code')),
			'exchangerate_php_to_n' => sanitize($this->input->post('_edit_exchangerate_php_to_n')),
			'exchangerate_n_to_php' => sanitize($this->input->post('_edit_exchangerate_n_to_php')),
			'from_dts' => sanitize($this->input->post('_edit_from_dts')),
			'to_dts' => sanitize($this->input->post('_edit_to_dts')),
			'phone_prefix' => sanitize($this->input->post('_edit_phone_prefix')),
			'phone_limit' => sanitize($this->input->post('_edit_phone_limit')),
			'utc' => sanitize($this->input->post('_edit_utc')),
			'arrangement' => sanitize($this->input->post('_edit_arrangement'))
		];

		$prev_val = (array) json_decode($this->input->post('prev_val'));

		$main = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val), $prev_val);

		if ($this->model_currency->update_data($file_name)) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$main = ($img_upload == 1) ? "1 image uploaded\n" : $main;
			$this->audittrail->logActivity('Currency', "Currency ".$prev_val['country_name']." has been updated successfully. \nChanges: \n$main", 'update', $this->session->userdata('username'));
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
        	if ($this->model_currency->disable_data($disable_id, $record_status)) {
        		$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Currency', "Currency $record_name has been $record_text successfully.", $record_text, $this->session->userdata('username'));
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
			if ($this->model_currency->delete_data($delete_id)) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Currency', "Currency $record_name has been deleted successfully.", 'delete', $this->session->userdata('username'));
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