<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Main_customers extends CI_Controller {

	public function logout() {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

	public function isLoggedIn() {
		if($this->session->userdata('isLoggedIn') == false) {
			header("location:".base_url('Main/logout'));
		}
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

	public function index() {
		if($this->session->userdata('isLoggedIn') == true) {
			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}

		$this->load->view('login');
	}

	public function view($token = '')
	{
		$this->isLoggedIn();
			if ($this->loginstate->get_access()['customer']['view'] == 1){
			//start - for restriction of views
	        $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';
	        $main_nav_id = $this->views_restriction($content_url);
	        //end - for restriction of views main_nav_id

	        // start - data to be used for views
	        $data_admin = array(
	            'token' => $token,
	            'main_nav_id' => $main_nav_id, //for highlight the navigation
	        );
	        // end - data to be used for views

	        // start - load all the views synchronously
	        $this->load->view('includes/header', $data_admin);
	        $this->load->view('customers/customer_list', $data_admin);
	        // end - load all the views synchronously
    	}else{
    		$this->load->view('error_404');
    	}
	}

  public function get_cities(){
		$this->isLoggedIn();
		generate_json($this->model->get_cities()->result_array());
	}

	public function get_customers(){
		$this->isLoggedIn();

        $_type = sanitize($this->input->post('_type'));
        $_name = sanitize($this->input->post('_name'));
        $_city = sanitize($this->input->post('_city'));

		$query = $this->Model_customers->get_customers($_type, $_name, $_city, $_REQUEST);

        generate_json($query);
	}

	public function export_customers(){
		$this->isLoggedIn();

		$requestData = url_decode(json_decode($this->input->post('_search')));
        $_type = sanitize($requestData['_type']);
        $_name = sanitize($requestData['_name']);
		$_city = sanitize($requestData['_city']);

		$query = $this->Model_customers->get_customers($_type, $_name, $_city, $requestData, true);

		$types = ["" => "All Customer Type", "2" => "Verified", "3" => "Guest"];
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('B1', "Customer List");
		$sheet->setCellValue('B2', "Filter: '$_name';$types[$_type]");
		$sheet->setCellValue('B3', ($_city == "") ? "All Cities":$_city);

		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('A6', 'Customer');
		$sheet->setCellValue('B6', 'City');
		$sheet->setCellValue('C6', 'Order History');
		$sheet->setCellValue('D6', 'Account');

		$sheet->getStyle('B1')->getFont()->setBold(true);
		$sheet->getStyle('A6:D6')->getFont()->setBold(true);

		// print_r($data['data']);
		// exit();
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

		$writer = new Xlsx($spreadsheet);
		$filename = 'Customer List ' . date('Y/m/d');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		ob_end_clean();

		$filterstr = "and no filters";
		if ($_type !== "" || $_name !== null || $_city !== "") {
			$filterstr = "with filters: $types[$_type]";
			$filterstr .= ($_name == "") ? " in " : "'$_name' in ";
			$filterstr .= ($_city == "") ? "All Cities":$_city;
		}
		$this->audittrail->logActivity('Customer List', "Customer List has been exported into excel $filterstr.", 'export', $this->session->userdata('username'));

		return $writer->save('php://output');
		exit();
        // generate_json($query);
	}

	public function add_customer(){
		$this->isLoggedIn();

        $_fname = sanitize($this->input->post('_fname'));
        $_lname = sanitize($this->input->post('_lname'));
        $_birthdate = sanitize($this->input->post('_birthdate'));
        $_gender = sanitize($this->input->post('_gender'));
        $_mobile = sanitize($this->input->post('_mobile'));
        $_email = sanitize($this->input->post('_email'));
        $_address1 = sanitize($this->input->post('_address1'));
        $_address2 = sanitize($this->input->post('_address2'));
        $_city = sanitize($this->input->post('_city'));

		$query = $this->Model_customers->add_customer($_fname, $_lname, $_birthdate, $_gender, $_mobile, $_email, $_address1, $_address2, $_city);

		$data = array('success' => 1, 'message' => "Employee successfully added");
		$this->audittrail->logActivity('Customer List', "A customer has been added successfully.", 'add', $this->session->userdata('username'));
		echo json_encode($data);
	}

	public function get_order_history(){
		$search = json_decode($this->input->post('searchValue'));
		$id = en_dec('dec',$this->input->post('id'));
		$total_amount = $this->input->post('total_amount');
	    $data = $this->Model_customers->get_order_history($search,$id,$total_amount);
	    echo json_encode($data);
	}

	public function get_login_history(){
		$id = en_dec('dec',$this->input->post('id'));
	    $data = $this->Model_customers->get_login_history($id);
	    echo json_encode($data);
	}


}
