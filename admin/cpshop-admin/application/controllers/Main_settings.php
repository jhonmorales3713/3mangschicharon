<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Main_settings extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('model_settings');
		$this->load->model('setting/model_settings_void');
		$this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
        $this->load->library('upload');
		// $this->load->model('setting/model_voidrecord');
		// $this->load->model('cmj_model/model_void_cmj');
		// $config_app = switch_db(company_database($this->session->userdata('company_id')));
		// $this->model_settings->app_db = $this->load->database($config_app,TRUE);
		// $this->model_settings_void->app_db = $this->load->database($config_app,TRUE);
		// // $this->model_voidrecord->app_db = $this->load->database($config_app,TRUE);
		// $this->model_sql->app_db = $this->load->database($config_app,TRUE);
		// $this->load->library('Pdf');
		// $this->load->library('Numbertowords');
	}

	public function logout() {
		$this->session->sess_destroy();
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
	
	public function index() {
		if($this->session->userdata('isLoggedIn') == true) {

			$token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);

			// $this->load->view(base_url('Main/home/'.$token));
			header("location:".base_url('Main/home/'.$token));
		}

		$this->load->view('login');
	}

	public function isLoggedIn() {
		if($this->session->userdata('isLoggedIn') == false) {
			header("location:".base_url('Main/logout'));
		}
	}

	// Admin Settings 012018 - Paul Chua

	// Under Navigation of Settings

	public function settings_home($token = '') {
		$this->isLoggedIn();

		$data_admin = array(
			 // get data using email
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_home', $data_admin);
		}else{
			$this->logout();
		}
	}

	// Start - Area	
		public function area($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_area', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function area_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$area = $this->input->post('area');
				$query = $this->model_settings->area_table($area);
			}
			echo json_encode($query);
		}

		public function insert_area(){
			$info_desc = sanitize($this->input->post('info_desc'));
			$monday_check = sanitize($this->input->post('monday_check'));
			$tuesday_check = sanitize($this->input->post('tuesday_check'));
			$wednesday_check = sanitize($this->input->post('wednesday_check'));
			$thursday_check = sanitize($this->input->post('thursday_check'));
			$friday_check = sanitize($this->input->post('friday_check'));
			$saturday_check = sanitize($this->input->post('saturday_check'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			}
			else {
				if($this->session->userdata('position_id') != ""){ //admin
					$isExists = $this->model_settings->get_area_unique($info_desc);

					if($isExists->num_rows() == 0) {
						$areaId = $this->model_settings->insert_area($info_desc);
						$query = $this->model_settings->insert_areasched($areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else {
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				}
				else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_area() {
			$areaId = sanitize($this->input->post('areaId'));

			$query = $this->model_settings->get_area($areaId);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_area_unique($info_unique) {

			$query = $this->model_settings->get_area_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_area() {

			$info_areaId = sanitize($this->input->post('info_areaId'));
			$info_desc = sanitize($this->input->post('info_desc'));
			$monday_check = sanitize($this->input->post('monday_check'));
			$tuesday_check = sanitize($this->input->post('tuesday_check'));
			$wednesday_check = sanitize($this->input->post('wednesday_check'));
			$thursday_check = sanitize($this->input->post('thursday_check'));
			$friday_check = sanitize($this->input->post('friday_check'));
			$saturday_check = sanitize($this->input->post('saturday_check'));

			if ($info_desc == "" || $info_areaId == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$query1 = $this->model_settings->update_area($info_areaId, $info_desc);

					$checkAreaExist= $this->model_settings->checkAreaExist($info_areaId);

					if($checkAreaExist -> num_rows() > 0) {
					$query2 = $this->model_settings->update_areasched($info_areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check);
					}
					else {
						$query3 = $this->model_settings->insert_areasched($info_areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check);
					}

					$data = array("success" => 1, 'message' => 'Successfully updated');

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_area() {

			$del_areaId = sanitize($this->input->post('del_areaId'));

			if ($del_areaId == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_area($del_areaId);

				$data = array("success" => 1, 'message' => "Area Deleted!" , "del_areaId" => $del_areaId);
			}

			generate_json($data);
		}
	// End - Area

	// Start - Credit Term	

		public function credit_term($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_credit_term', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function credit_term_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$credit = $this->input->post("credit");
				$query = $this->model_settings->credit_term_table($credit);
			}
			echo json_encode($query);
		}

		public function comm_sup_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$supplier = $this->input->post("supplier");
				$query = $this->model_settings->comm_sup_table($supplier);
			}
			echo json_encode($query);
		}

		public function comm_sup_product_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$supid = $this->input->post("supid");
				$query = $this->model_settings->comm_sup_product_table($supid);
			}
			echo json_encode($query);
		}

		public function targetsales_agent_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$agent = $this->input->post("agent");
				$query = $this->model_settings->targetsales_agent_table($agent);
			}
			echo json_encode($query);
		}

		public function insert_credit_term(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin
					$isExists = $this->model_settings->get_credit_term_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_credit_term($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_credit_term() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_credit_term($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_credit_term_unique($info_unique) {

			$query = $this->model_settings->get_credit_term_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_credit_term() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));
			
			$termChecking = $this->model_sql->selectNow('8_credit','id','description',$info_desc)->row();
			$originalTerm = $this->model_sql->selectNow('8_credit','description','id',$info_id)->row();
			
			if(!is_null($termChecking)) {
			
				if($originalTerm->description != $info_desc) {
					
					$data = array("success" => 2, 'message' => 'Term Already Exist.');
					echo json_encode($data);
					
				}else {

					if ($info_desc == "" || $info_id == "") {
						
						$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
					} else {

						if($this->session->userdata('position_id') != ""){ //admin

							$query1 = $this->model_settings->update_credit_term($info_id, $info_desc);

							$data = array("success" => 1, 'message' => 'Successfully updated');

						} else {
							$this->logout();
						}
					}

					generate_json($data);

				}

			}else {

				if ($info_desc == "" || $info_id == "") {
					
					$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
				} else {

					if($this->session->userdata('position_id') != ""){ //admin

						$query1 = $this->model_settings->update_credit_term($info_id, $info_desc);

						$data = array("success" => 1, 'message' => 'Successfully updated');

					} else {
						$this->logout();
					}
				}

				generate_json($data);
			}

		}

		public function delete_credit_term() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_credit_term($del_id);

				$data = array("success" => 1, 'message' => "Credit Term Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Credit Term

	// Start - Delivery Vehicle	

		public function delivery_vehicle($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_delivery_vehicle', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function delivery_vehicle_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$plateno = $this->input->post("plateno");
				$query = $this->model_settings->delivery_vehicle_table($plateno);
			}
			echo json_encode($query);
		}

		public function insert_delivery_vehicle(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_delivery_vehicle_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_delivery_vehicle($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_delivery_vehicle() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_delivery_vehicle($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_delivery_vehicle_unique($info_unique) {

			$query = $this->model_settings->get_delivery_vehicle_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_delivery_vehicle() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));

			$plateNoChecking = $this->model_sql->selectNow('9_delvehicle','plateno','plateno',$info_desc)->row();
			$originalPlateNo = $this->model_sql->selectNow('9_delvehicle','plateno','id',$info_id)->row();

			if( ( !is_null($plateNoChecking)) ) {

				if($originalPlateNo->plateno != $info_desc) {

					$data = array("success" => 2, 'message' => 'Plate# Exist');
					echo json_encode($data);
					
				}else {

					if ($info_desc == "" || $info_id == "") {
						
						$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
					} else {

						if($this->session->userdata('position_id') != ""){ //admin

							$query1 = $this->model_settings->update_delivery_vehicle($info_id, $info_desc);

							$data = array("success" => 1, 'message' => 'Successfully updated');

						} else {
							$this->logout();
						}
					}

					generate_json($data);
				}

			}else {

				if ($info_desc == "" || $info_id == "") {
					
					$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
				} else {

					if($this->session->userdata('position_id') != ""){ //admin

						$query1 = $this->model_settings->update_delivery_vehicle($info_id, $info_desc);

						$data = array("success" => 1, 'message' => 'Successfully updated');

					} else {
						$this->logout();
					}
				}

				generate_json($data);
			}

		}

		public function delete_delivery_vehicle() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_delivery_vehicle($del_id);

				$data = array("success" => 1, 'message' => "Delivery Vehicle Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Delivery Vehicle

	// Start - Employee	

		public function employee($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_emptype' => $this->model_settings->get_emptype(),
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_employee', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function employee_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$id = $this->input->post("id");
				$name = $this->input->post("name");
				$type = $this->input->post("type");
				$query = $this->model_settings->employee_table($id, $name, $type);
			}
			echo json_encode($query);
		}

		public function insert_employee(){
			$info_empid = sanitize($this->input->post('info_empid'));
			$info_fname = sanitize($this->input->post('info_fname'));
			$info_mname = sanitize($this->input->post('info_mname'));
			$info_lname = sanitize($this->input->post('info_lname'));
			$info_type = sanitize($this->input->post('info_type'));

			$empIdChecker = $this->model_sql->selectNow('jcw_employee','id','empid',$info_empid)->row();


			if ($info_empid == "" || $info_fname == "" || $info_lname == "" || $info_type == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_employee_unique($info_fname, $info_mname, $info_lname, $info_empid, $info_type);

					// if($isExists->num_rows() == 0)
					// {
						if(is_null($empIdChecker)) {
							$id = $this->model_settings->insert_employee($info_empid, $info_fname, $info_mname, $info_lname, $info_type);
							$data = array("success" => 1, 'message' => 'Successfully Added');
						}else {
							$data = array('success' => 2, 'message' => 'Employee ID already exist.');
						}
					// }
					// else
					// {
					// 	$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					// }


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_employee() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_employee($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_employee_unique($info_fname, $info_mname, $info_lname, $info_empid) {

			$query = $this->model_settings->get_employee_unique($info_fname, $info_mname, $info_lname, $info_empid);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_employee() {
			$id = sanitize($this->input->post('primeid'));
			$info_id = sanitize($this->input->post('info_id'));
			$info_empid = sanitize($this->input->post('info_empid'));
			$info_fname = sanitize($this->input->post('info_fname'));
			$info_mname = sanitize($this->input->post('info_mname'));
			$info_lname = sanitize($this->input->post('info_lname'));
			$info_type = sanitize($this->input->post('info_type'));

			if ($info_id == "" || $info_empid == "" || $info_fname == "" || $info_lname == "" || $info_type == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					if($id == $info_empid)
					{
						$query1 = $this->model_settings->update_employee($info_id, $info_empid, $info_fname, $info_mname, $info_lname, $info_type);
						$data = array("success" => 1, 'message' => 'Successfully updated');

					}
					else
					{

						$originalIds = $this->model_settings->check_employee_exist($info_empid);
						if ($originalIds != $info_empid)
						{
							$query1 = $this->model_settings->update_employee($info_id, $info_empid, $info_fname, $info_mname, $info_lname, $info_type);
						$data = array("success" => 1, 'message' => 'Successfully updated');
						}
						else{
							$data = array("success" => 2, 'message' => 'Employee Id Already Exist.');
						}
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_employee() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_employee($del_id);

				$data = array("success" => 1, 'message' => "Employee Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Employee

	// Start - Employee Type

		public function employee_type($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_employee_type', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function employee_type_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$empType = $this->input->post("empType");
				$query = $this->model_settings->employee_type_table($empType);
			}
			echo json_encode($query);
		}

		public function insert_employee_type(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_employee_type_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_employee_type($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_employee_type() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_employee_type($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_employee_type_unique($info_unique) {

			$query = $this->model_settings->get_employee_type_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_employee_type() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));
			$info_desc1 = sanitize($this->input->post('info_desc1'));


			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin


					if($info_desc == $info_desc1)
					{
						$query1 = $this->model_settings->update_employee_type($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');
					}
					else
					{
						$isExists = $this->model_settings->get_employee_type_unique($info_desc);

						if($isExists->num_rows() == 0)
						{
							$query1 = $this->model_settings->update_employee_type($info_id, $info_desc);
							$data = array("success" => 1, 'message' => 'Successfully updated');
						}
						else
						{
							$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
						}
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_employee_type() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_employee_type($del_id);

				$data = array("success" => 1, 'message' => "Employee Type Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Employee Type

	// Start - Franchise
		public function franchise($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_franchise', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function franchise_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$franchise = $this->input->post("franchise");
				$query = $this->model_settings->franchise_table($franchise);
			}
			echo json_encode($query);
		}

		public function insert_franchise(){
			$info_desc = sanitize($this->input->post('info_desc'));
			$info_fee = sanitize($this->input->post('info_fee'));
			$info_cashbond = sanitize($this->input->post('info_cashbond'));
			$info_commission = sanitize($this->input->post('info_commission'));


			if ($info_desc == "" || $info_fee == "" || $info_cashbond == "" || $info_commission == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');

			} 
			else if (filter_var($info_fee, FILTER_VALIDATE_INT) === false || filter_var($info_cashbond, FILTER_VALIDATE_INT) === false || filter_var($info_commission, FILTER_VALIDATE_INT) === false) {
				
				$data = array("success" => 0, 'message' => 'Please make sure to enter proper amount.');
			}
			else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_franchise_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_franchise($info_desc, $info_fee, $info_cashbond, $info_commission);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_franchise() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_franchise($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_franchise_unique($info_unique) {

			$query = $this->model_settings->get_franchise_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_franchise() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));
			$info_fee = sanitize($this->input->post('info_fee'));
			$info_cashbond = sanitize($this->input->post('info_cashbond'));
			$info_commission = sanitize($this->input->post('info_commission'));

			$franchiseChecker = $this->model_sql->selectNow('8_franchises','id','description',$info_desc)->row();
			$originalFranchise = $this->model_sql->selectNow('8_franchises','description','id',$info_id)->row();

			if ($info_desc == "" || $info_id == "" || $info_fee == "" || $info_cashbond == "" || $info_commission == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');

			} 
			else if (filter_var($info_fee, FILTER_VALIDATE_INT) === false || filter_var($info_cashbond, FILTER_VALIDATE_INT) === false || filter_var($info_commission, FILTER_VALIDATE_INT) === false) {
				
				$data = array("success" => 0, 'message' => 'Please make sure to enter proper amount.');
			}
			else {


				if($this->session->userdata('position_id') != ""){ //admin


					if(!is_null($franchiseChecker)) {

						if($originalFranchise->description == $info_desc) {

							$query1 = $this->model_settings->update_franchise($info_id, $info_desc, $info_fee, $info_cashbond, $info_commission);
							$data = array("success" => 1, 'message' => 'Successfully Updated');

						}else {

							$data = array("success" => 2, 'message' => 'Franchise Already Exist');

						}

					}else {

						$query1 = $this->model_settings->update_franchise($info_id, $info_desc, $info_fee, $info_cashbond, $info_commission);
						$data = array("success" => 1, 'message' => 'Successfully Updated');
					
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_franchise() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_franchise($del_id);

				$data = array("success" => 1, 'message' => "Franchise Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Franchise

	// Start - GL Accounts
		public function gl_accounts($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_gl_accounts', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function gl_accounts_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$account = $this->input->post("account");
				$type = $this->input->post("acctype");
				$query = $this->model_settings->gl_accounts_table($account, $type);
			}
			echo json_encode($query);
		}

		public function insert_gl_accounts(){
			$info_desc = sanitize($this->input->post('info_desc'));
			$info_type = sanitize($this->input->post('info_type'));
			$accountcode = sanitize($this->input->post('accountcode'));


			if ($info_desc == "") {

				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {


				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_gl_accounts_unique_atype($info_desc, $info_type);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_gl_accounts($info_desc, $info_type, $accountcode);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_gl_accounts() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_gl_accounts($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_gl_accounts_unique($info_unique) {

			$query = $this->model_settings->get_gl_accounts_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_gl_accounts() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_type = sanitize($this->input->post('info_type'));
			$info_desc = sanitize($this->input->post('info_desc'));
			$accountcode = sanitize($this->input->post('accountcode'));

			if ($info_desc == "" || $info_id == "") {

				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} 
			else 
			{

				if($this->session->userdata('position_id') != "")
				{ //admin

					$isExists = $this->model_settings->get_gl_accounts_unique_atype($info_desc, $info_type)->row();

					if($isExists == ''){
						$id = $this->model_settings->update_gl_accounts($info_id, $info_type, $info_desc, $accountcode);
						$data = array("success" => 1, 'message' => 'Successfully Updated.');
					}else{
						if($isExists->id == $info_id)
						{
							$id = $this->model_settings->update_gl_accounts($info_id, $info_type, $info_desc, $accountcode);

							$data = array("success" => 1, 'message' => 'Successfully Updated.');
						}
						else
						{
							$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
						}
					}


				} 
				else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_gl_accounts() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_gl_accounts($del_id);

				$data = array("success" => 1, 'message' => "GL Accounts Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - GL Accounts

	// Start - Inventory Category
		public function inventory_category($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_inventory_category', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function inventory_category_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$category = $this->input->post("category");
				$query = $this->model_settings->inventory_category_table($category);
			}
			echo json_encode($query);
		}

		public function insert_inventory_category(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {
				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_inventory_category_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_inventory_category($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_inventory_category() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_inventory_category($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_inventory_category_unique($info_unique) {

			$query = $this->model_settings->get_inventory_category_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_inventory_category() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_inventory_category_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->update_inventory_category($info_id,$info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Updated');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_inventory_category() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_inventory_category($del_id);

				$data = array("success" => 1, 'message' => "Inventory Category Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Inventory Category

	// Start - Payment Option

		public function payment_option($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_payment_option', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function payment_option_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$payment = $this->input->post("payment");
				$query = $this->model_settings->payment_option_table($payment);
			}
			echo json_encode($query);
		}

		public function insert_payment_option(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_payment_option_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
					$id = $this->model_settings->insert_payment_option($info_desc);

					$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_payment_option() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_payment_option($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_payment_option_unique($info_unique) {

			$query = $this->model_settings->get_payment_option_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}


		public function update_payment_option() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));

			$termChecker = $this->model_sql->selectNow('8_payment','id','description',$info_desc)->row();
			$originalTerm = $this->model_sql->selectNow('8_payment','description','id',$info_id)->row();


			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					if(!is_null($termChecker)) {

						if($originalTerm->description == $info_desc) {

							$query1 = $this->model_settings->update_payment_option($info_id, $info_desc);
							$data = array("success" => 1, 'message' => 'Successfully updated');

						}else {

							$data = array("success" => 2, 'message' => 'Term Already Exist');

						}

					}else {

						$query1 = $this->model_settings->update_payment_option($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');

					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_payment_option() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_payment_option($del_id);

				$data = array("success" => 1, 'message' => "Payment Option Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Payment Option

	// Start - Price Category

		public function price_category($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_price_category', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function price_category_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$category = $this->input->post("description");
				$query = $this->model_settings->price_category_table($category);
			}
			echo json_encode($query);
		}

		public function insert_price_category(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_price_category_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_price_category($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_price_category() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_price_category($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_price_category_unique($info_unique) {

			$query = $this->model_settings->get_price_category_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_price_category() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_price_category_unique($info_desc);

				if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->update_price_category($info_id,$info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Updated');
					}
				else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_price_category() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_price_category($del_id);

				$data = array("success" => 1, 'message' => "Price Category Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Price Category

	// Start - Sales Area

		public function sales_area($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_sales_area', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function sales_area_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$area = $this->input->post("area");
				$query = $this->model_settings->sales_area_table($area);
			}
			echo json_encode($query);
		}

		public function insert_sales_area(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_sales_area_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_sales_area($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_sales_area() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_sales_area($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_sales_area_unique($info_unique) {

			$query = $this->model_settings->get_sales_area_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_sales_area() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_sales_area_unique($info_desc);

					if($isExists->num_rows() == 0)
					{

					$query1 = $this->model_settings->update_sales_area($info_id, $info_desc);

					$data = array("success" => 1, 'message' => 'Successfully updated');

					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_sales_area() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_sales_area($del_id);

				$data = array("success" => 1, 'message' => "Sales Area Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Sales Area

	// Start - Shipping

		public function shipping($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_shipping', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function shipping_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$query = $this->model_settings->shipping_table();
			}
			echo json_encode($query);
		}

		public function insert_shipping(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_shipping_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_shipping($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_shipping() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_shipping($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_shipping_unique($info_unique) {

			$query = $this->model_settings->get_shipping_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_shipping() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$query1 = $this->model_settings->update_shipping($info_id, $info_desc);

					$data = array("success" => 1, 'message' => 'Successfully updated');

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_shipping() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_shipping($del_id);

				$data = array("success" => 1, 'message' => "Shipping Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Shipping

	# Start of Currency Methods
		public function currency($token = ''){ 
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") # Admin
			{
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_currency', $data_admin);
			}
			else
			{
				$this->logout();
			}
		}

		public function insert_update_currency()
		{
			$code = strtoupper($this->input->post('info_code'));
			$desc = $this->input->post('info_desc');
			$val  = $this->input->post('info_val');
			$id   = $this->input->post('info_id');

			if($id == '') # If id is empty, insert to db
			{
				$insert_currency = $this->model_settings->insert_currency($code, $desc, $val);

				if($insert_currency)
				{
					$data = array(
						'success' => 1
						, 'message' => 'Currency has been saved successfully!'
					);
				}
				else
				{
					$data = array(
						'success' => 0
						, 'message' => 'Failed to insert data.'
					);
				}
			}
			else # When id is no longer empty, update
			{
				$update_currency = $this->model_settings->update_currency($code, $desc, $val, $id);

				if($update_currency)
				{
					$data = array(
						'success' => 1
						, 'message' => 'Currency has been updated successfully!'
					);
				}
				else
				{
					$data = array(
						'success' => 1
						, 'message' => 'Failed to update data'
					);
				}
			}

			generate_json($data);
		}

		public function retrieve_currencies()
		{
			if ($this->session->userdata('position_id') != "") # admin
			{ 
				$status = $this->input->post("status");
				$symbol = $this->input->post("symbol");
				$retrieve_currencies = $this->model_settings->retrieve_currencies($status, $symbol);
			}

			generate_json($retrieve_currencies);
			// echo $retrieve_currencies;
		}

		public function retrieve_currency()
		{
			$id = $this->input->post('id');

			$retrieve_currency = $this->model_settings->retrieve_currency($id);

			generate_json($retrieve_currency->row());
		}

		public function toggle_currency_status()
		{
			$id 	= $this->input->post('id');
			$status = $this->input->post('status');

			$toggle_currency_status = $this->model_settings->toggle_currency_status($status, $id);

			if($toggle_currency_status)
			{
				$data = array(
					'success' => 1
					, 'message' => 'Currency has been deactivated successfully!'
				);
			}
			else
			{
				$data = array(
					'success' => 0
					, 'message' => 'Failed to deactivate currency.'
				);
			}

			generate_json($data);
		}
	# End of Currency Methods

	// Start - Ticket Status

		public function ticket_status($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_ticket_status', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function ticket_status_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$status = $this->input->post("status");
				$query = $this->model_settings->ticket_status_table($status);
			}
			echo json_encode($query);
		}

		public function insert_ticket_status(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_ticket_status_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_ticket_status($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_ticket_status() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_ticket_status($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_ticket_status_unique($info_unique) {

			$query = $this->model_settings->get_ticket_status_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_ticket_status() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));
			$ticketdesc = sanitize($this->input->post('ticketstatid'));


			$ticketChecker = $this->model_sql->selectNow('8_ticketstatus','id','description',$info_desc)->row();
			$originalTicket = $this->model_sql->selectNow('8_ticketstatus','description','id',$info_id)->row();

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					// if(!is_null($ticketChecker)) {
					// 	if($originalTicket->description == $info_desc) {
					// 		$query1 = $this->model_settings->update_ticket_status($info_id, $info_desc);
					// 		$data = array("success" => 1, 'message' => 'Successfully updated');
					// 	}else {
					// 		 $data = array("success" => 2, 'message' => 'Ticket Already Exist.');
					// 	}
					// }else {
					// 	$data = array("success" => 2, 'message' => 'Ticket Already Exist.');
					// }

					if($info_desc == $ticketdesc)
					{
						$query1 = $this->model_settings->update_ticket_status($info_id, $info_desc);
						$data = array("success" => 1, 'message' => 'Successfully updated');

					}
					else
					{

						$originalIds = $this->model_settings->check_ticketstatus_exist($ticketdesc);
						if ($originalIds != $info_desc)
						{
							$query1 = $this->model_settings->update_ticket_status($info_id, $info_desc);
							$data = array("success" => 1, 'message' => 'Successfully updated');
						}
						else{
							$data = array("success" => 2, 'message' => 'Ticket Status Already Exist.');
						}
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_ticket_status() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_ticket_status($del_id);

				$data = array("success" => 1, 'message' => "Ticket Status Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Ticket Status

	// Start - Unit of Measurement

		public function uom($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_uom', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function uom_table(){
			if ($this->session->userdata('position_id') != "") { // admin
				$uom = $this->input->post("uom");
				$query = $this->model_settings->uom_table($uom);
			}
			echo json_encode($query);
		}

		public function insert_uom(){
			$info_desc = sanitize($this->input->post('info_desc'));


			if ($info_desc == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					$isExists = $this->model_settings->get_uom_unique($info_desc);

					if($isExists->num_rows() == 0)
					{
						$id = $this->model_settings->insert_uom($info_desc);

						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else
					{
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}


				} else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_uom() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_uom($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_uom_unique($info_unique) {

			$query = $this->model_settings->get_uom_unique($info_unique);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function update_uom() {

			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));


			$uomChecker = $this->model_sql->selectNow('8_uom','id','description',$info_desc)->row();
			$uomOriginal = $this->model_sql->selectNow('8_uom','description','id',$info_id)->row();

			if ($info_desc == "" || $info_id == "") {
				
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {

				if($this->session->userdata('position_id') != ""){ //admin

					if(is_null($uomChecker)) {
						if($uomOriginal->description != $info_desc) {
							$query1 = $this->model_settings->update_uom($info_id, $info_desc);
							$data = array("success" => 1, 'message' => 'Successfully updated');
						}else {
							$data = array("success" => 2, 'message' => 'Unit of Measure already exist.');
						}
					}else {
						$data = array("success" => 2, 'message' => 'Unit of Measure already exist.');
					}

				} else {
					$this->logout();
				}
			}

			generate_json($data);

		}

		public function delete_uom() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_uom($del_id);

				$data = array("success" => 1, 'message' => "Unit of Measurement Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Unit of Measurement

	// Start - Warehouse Location

		public function warehouse_location($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_warehouse_location', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function warehouse_location_table(){
			$this->isLoggedIn();
			
			$location = $this->input->get("location");
			$query = $this->model_settings->warehouse_location_table($location);
			echo json_encode($query);
		}

		public function insert_warehouse_location() {
			$info_desc = sanitize($this->input->post('info_desc'));
			$info_address = sanitize($this->input->post('info_address'));

			if ($info_desc == "") {
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			}
			else {
				if ($this->session->userdata('position_id') != "") {
					$isExists = $this->model_settings->get_warehouse_location_unique($info_desc);

					if ($isExists->num_rows() == 0) {
						$id = $this->model_settings->insert_warehouse_location($info_desc, $info_address);
						$data = array("success" => 1, 'message' => 'Successfully Added');
					}
					else {
						$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
					}
				}
				else {
					$this->logout();
				}
			}

			generate_json($data);
		}

		public function get_warehouse_location() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_warehouse_location($id);

			if ($query->num_rows() > 0) {

				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);

		}

		public function get_warehouse_location_unique($info_unique) {
			$query = $this->model_settings->get_warehouse_location_unique($info_unique);

			if ($query->num_rows() > 0) {
				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}

			generate_json($data);
		}

		public function update_warehouse_location() {
			$info_id = sanitize($this->input->post('info_id'));
			$info_desc = sanitize($this->input->post('info_desc'));
			$old_info_desc = sanitize($this->input->post('old_info_desc'));
			$info_address = sanitize($this->input->post('info_address'));

			$continue = false; # used to determine if validation is success or fail

			if ($info_desc == "" || $info_id == "" || $old_info_desc == "") {
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			}
			else {
				# check if old name and editted name are the same
				# if the same, just proceed in saving data
				# if not, check if the new name already exists
				if ($old_info_desc == $info_desc) {
					$continue = true;
				}
				else {
					$locationChecker = $this->model_sql->selectNow('8_itemloc','id','description',$info_desc)->row();
					
					if (is_null($locationChecker)) {
						$continue = true;
					}
					else {
						$data = array("success" => 2, 'message' => 'Location already exist.');
					}
				}
			}

			if ($continue)
				if ($this->model_settings->update_warehouse_location($info_id, $info_desc, $info_address))
					$data = array("success" => 1, 'message' => 'Successfully updated');

			generate_json($data);
		}

		public function delete_warehouse_location() {

			$del_id = sanitize($this->input->post('del_id'));

			if ($del_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_warehouse_location($del_id);

				$data = array("success" => 1, 'message' => "Warehouse Location Deleted!" , "del_id" => $del_id);
			}

			generate_json($data);
		}

	// End - Warehouse Location

	//Start - user role
	
		public function user_role($token = ''){
			$this->isLoggedIn();
			$get_main_nav = $this->model_settings->get_main_nav()->result();

			$data = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
				'get_main_nav' => $get_main_nav,
				//'get_crud_access' => $this->model->get_crud_access($this->session->userdata('position_id'))->row(),
			);

			$this->load->view('includes/header', $data);
			$this->load->view('settings/settings_user_role', $data);
		}

		public function user_role_table(){
			$position = $this->input->post("position");
			$query = $this->model_settings->user_role_table($position);
			echo json_encode($query);
		}

		public function add_userrole(){
			$r_position = sanitize($this->input->post('a_position'));
			$acb_home = sanitize($this->input->post('acb_home'));//042419
			$acb_sales = sanitize($this->input->post('acb_sales'));
			$acb_purchases = sanitize($this->input->post('acb_purchases'));
			$acb_inventory = sanitize($this->input->post('acb_inventory'));
			$acb_entity = sanitize($this->input->post('acb_entity'));
			$acb_manufacturing = sanitize($this->input->post('acb_manufacturing'));
			$acb_accounts = sanitize($this->input->post('acb_accounts'));
			$acb_settings = sanitize($this->input->post('acb_settings'));
			$acb_packagecart = sanitize($this->input->post('acb_packagecart'));
			$acb_reports = sanitize($this->input->post('acb_reports'));
			$acb_qr = sanitize($this->input->post('acb_qr')); //072318
			$acb_ds = sanitize($this->input->post('acb_ds')); //072618
			$acb_sc = sanitize($this->input->post('acb_sc'));//042419
			$acb_commission = sanitize($this->input->post('acb_commission'));//042419
			$acb_content = $this->input->post('acb_content');  //071618
			$edit_content_access = $this->input->post('edit_access');
			$approve_content_access = $this->input->post('approve_access');
			
			$ar_approve = sanitize($this->input->post('ar_approve'));
			$ar_process = sanitize($this->input->post('ar_process'));
			$ar_edit = sanitize($this->input->post('ar_edit'));
			$ar_delete = sanitize($this->input->post('ar_delete'));

			$checkbox_approve_arr = array();
			if (!empty($approve_content_access)){
				array_push($checkbox_approve_arr, $approve_content_access);
			}

			if (empty($acb_content)) {
				$data = array('success' => 0, 'message' => 'Please choose Content Navigation Role.');
				generate_json($data);
				//die();
			}
			$acb_content_str = implode(", ",$acb_content); //071618
			//$edit_access = implode(", ",$edit_content_access); //071618

			$checkbox_arr = array();

			if (!empty($acb_home)){
				array_push($checkbox_arr, $acb_home);
			}

			if (!empty($acb_sales)){
				array_push($checkbox_arr, $acb_sales);
			}

			if (!empty($acb_purchases)){
				array_push($checkbox_arr, $acb_purchases);

				// if(!empty($_POST['edit_access']) || !empty($acb_purchases)) {
				//  //$acb_purchases1 = $acb_content;
				//  $edit_access = $this->input->post('edit_access');
				// }else{
				//  $edit_access = 0;
				//  $data = array('success' => 0, 'message' => 'Please check submodule first.');
				// } 

				// print_r($edit_access);
				// die();
			}

			if (!empty($acb_inventory)){
				array_push($checkbox_arr, $acb_inventory);
			}

			if (!empty($acb_entity)){
				array_push($checkbox_arr, $acb_entity);
			}

			if (!empty($acb_manufacturing)){
				array_push($checkbox_arr, $acb_manufacturing);
			}

			if (!empty($acb_accounts)){
				array_push($checkbox_arr, $acb_accounts);
			}

			if (!empty($acb_settings)){
				array_push($checkbox_arr, $acb_settings);
			}

			if (!empty($acb_packagecart)){
				array_push($checkbox_arr, $acb_packagecart);
			}

			if (!empty($acb_reports)){
				array_push($checkbox_arr, $acb_reports);
			}

			if (!empty($acb_qr)){
				array_push($checkbox_arr, $acb_qr);
			}

			if (!empty($acb_ds)){
				array_push($checkbox_arr, $acb_ds);
			}

			if (!empty($acb_sc)){
				array_push($checkbox_arr, $acb_sc);
			}
			
			if (!empty($acb_commission)){       
				array_push($checkbox_arr, $acb_commission);
			}

			$checkbox_str = implode(", ",$checkbox_arr);

			if (!empty($r_position)){
				$res = $this->model_settings->checkunique_userrole($r_position);
				if($res->num_rows() > 0)
				{
					$data = array('success' => 0, 'message' => 'Duplicate user role. Please check your data.');
				}
				else
				{
					$position_id = $this->model_settings->add_userrole($r_position, $checkbox_str, $acb_content_str, $ar_approve, $ar_process, $ar_edit, $ar_delete); //071618
					$batchData      = array();
					
					// foreach ($edit_content_access as $value) {
					
					//  foreach ($checkbox_approve_arr as $key) {
					//      # code...
					//  }
				//     $crudItem = array(
					//         "navigation_id"      => $value,
					//         "edit"    => 1,
					//         "approve" => $key,
					//         "status"    => 1,
					//         "position_id"    => $position_id,
							
					//     );

					//     array_push($batchData, $crudItem);
					// }
					//$query_crud = $this->model_settings->add_crud_navaccess($batchData);
					$data = array('success' => 1, 'message' => 'Successfully added!');
				}
			}else{
				$data = array('success' => 0, 'message' => 'Please fill up required fields.');
			}
			generate_json($data);
		}

		public function edit_userrole(){
			$r_position_id = sanitize($this->input->post('r_position_id'));
			$r_positionorig = sanitize($this->input->post('r_positionorig'));
			$r_position = sanitize($this->input->post('r_position'));
			$cb_home = sanitize($this->input->post('cb_home'));
			$cb_sales = sanitize($this->input->post('cb_sales'));
			$cb_purchases = sanitize($this->input->post('cb_purchases'));
			$cb_inventory = sanitize($this->input->post('cb_inventory'));
			$cb_entity = sanitize($this->input->post('cb_entity'));
			$cb_manufacturing = sanitize($this->input->post('cb_manufacturing'));
			$cb_accounts = sanitize($this->input->post('cb_accounts'));
			$cb_settings = sanitize($this->input->post('cb_settings'));
			$cb_packagecart = sanitize($this->input->post('cb_packagecart'));
			$cb_reports = sanitize($this->input->post('cb_reports'));
			$cb_qr = sanitize($this->input->post('cb_qr')); //072318
			$cb_ds = sanitize($this->input->post('cb_ds')); //072618
			$cb_content = $this->input->post('cb_content'); //071618
			$cb_commission = $this->input->post('cb_commission'); //071618
			$edit_content_access = $this->input->post('e_edit_access');
			$cb_portal_pos = sanitize($this->input->post('cb_portal_pos')); //072618
			$cb_sc = sanitize($this->input->post('cb_sc')); //072618

			$edit_can_approve = $this->input->post('ear_approve');
			$edit_can_process = $this->input->post('ear_process');
			$edit_can_edit = $this->input->post('ear_edit');
			$edit_can_delete = $this->input->post('ear_delete');

			if (empty($cb_content)) {
				$data = array('success' => 0, 'message' => 'Please choose Content Navigation Role.');
			}
			$content_checkbox_str = implode(", ",$cb_content);

			$checkbox_arr = array();

			if (!empty($cb_home)){
				array_push($checkbox_arr, $cb_home);
			}

			if (!empty($cb_sales)){
				array_push($checkbox_arr, $cb_sales);
			}

			if (!empty($cb_purchases)){
				array_push($checkbox_arr, $cb_purchases);
			}

			if (!empty($cb_inventory)){
				array_push($checkbox_arr, $cb_inventory);
			}

			if (!empty($cb_entity)){
				array_push($checkbox_arr, $cb_entity);
			}

			if (!empty($cb_manufacturing)){
				array_push($checkbox_arr, $cb_manufacturing);
			}

			if (!empty($cb_accounts)){
				array_push($checkbox_arr, $cb_accounts);
			}

			if (!empty($cb_settings)){
				array_push($checkbox_arr, $cb_settings);
			}

			if (!empty($cb_packagecart)){
				array_push($checkbox_arr, $cb_packagecart);
			}

			if (!empty($cb_reports)){
				array_push($checkbox_arr, $cb_reports);
			}

			if (!empty($cb_qr)){
				array_push($checkbox_arr, $cb_qr);
			}

			if (!empty($cb_ds)){
				array_push($checkbox_arr, $cb_ds);
			}

			if (!empty($cb_portal_pos)){
				array_push($checkbox_arr, $cb_portal_pos);
			}
			if (!empty($cb_commission)){
				array_push($checkbox_arr, $cb_commission);
			}
			if (!empty($cb_sc)){
				array_push($checkbox_arr, $cb_sc);
			}

			$checkbox_str = implode(", ",$checkbox_arr);

			// $data = array('approve' => $edit_can_approve, 'process' => $edit_can_process, 'edit' => $edit_can_edit, 'delete' => $edit_can_delete);

			if (!empty($r_position_id)){

				if($r_position == $r_positionorig)
				{
					$query = $this->model_settings->edit_userrole($r_position_id, $r_position, $checkbox_str, $content_checkbox_str, $edit_can_approve, $edit_can_process, $edit_can_delete, $edit_can_edit);
					$batchData      = array();
			//          foreach ($edit_content_access as $value) {
			//              // print_r($value);
			//              // die();
			//           $crudItem = array(
			//              "navigation_id"      => $value,
			//              "edit"    => 1,
			//              "status"    => 1,
			//              "position_id"    => $r_position_id,
							
			//          );
						// array_push($batchData, $crudItem);
				//       }
						// $query_crud = $this->model_settings->edit_crud_navaccess($batchData, $r_position_id);
						$data = array('success' => 1, 'message' => 'Successfully edited!');
				}
				else
				{
					$res = $this->model_settings->checkunique_userrole($r_position);
					if($res->num_rows() > 0)
					{
						$data = array('success' => 0, 'message' => 'Duplicate user role. Please check your data.');
					}
					else
					{
						$position_id = $this->model_settings->edit_userrole($r_position_id, $r_position, $checkbox_str, $content_checkbox_str, $edit_can_approve, $edit_can_process, $edit_can_delete, $edit_can_edit);
						$batchData      = array();
			//          foreach ($edit_content_access as $value) {
			//              // print_r($value);
			//              // die();
			//           $crudItem = array(
			//              "navigation_id"      => $value,
			//              "edit"    => 1,
			//              "status"    => 1,
			//              "position_id"    => $r_position_id,
							
			//          );
						// array_push($batchData, $crudItem);
					//  }
						$query_crud = $this->model_settings->edit_crud_navaccess($batchData, $r_position_id);
						//print_r($quer)
						$data = array('success' => 1, 'message' => 'Successfully edited!');
					}
				}

				
			}else{
				$data = array('success' => 0, 'message' => 'Something went wrong, please try again.');
			}
			generate_json($data);
		}

		public function delete_userrole(){
			$r_position_id_delete = sanitize($this->input->post('r_position_id_delete'));
			if ($r_position_id_delete > 0) {
				$this->model_settings->delete_userrole($r_position_id_delete);
				$data = array('success' => 1, 'message' => 'User Role Deleted!');
			}else{
				$data = array('success' => 0, 'message' => 'something went wrong, please try again.');
			}

			generate_json($data);
		}

	//End - user role

	// Start - System Users

		public function system_user($token = ''){ //
			$this->isLoggedIn();
			
			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
				'positions' => $this->model_settings->get_positions()->result_array()
			);
			
			if ($this->session->userdata('position_id') != "") { // admin
				$this->load->view('includes/header', $data_admin);
				$this->load->view('settings/settings_system_user', $data_admin);
			}else{
				$this->logout();
			}
		}

		public function system_user_table(){
			$daterange_from = $this->input->post("0[daterange_from]") != "" ? date_format(date_create(sanitize($this->input->post("0[daterange_from]"))),"Y-m-d") : "";
			$daterange_to = $this->input->post("1[daterange_to]") != "" ? date_format(date_create(sanitize($this->input->post("1[daterange_to]"))),"Y-m-d") : ""; 

			$position = $this->input->post("2[position]");
			$fullname = $this->input->post("3[fullname]");

			$query = $this->model_settings->system_user_table($daterange_from, $daterange_to, $position, $fullname);

			generate_json($query);
		}
			
		public function insert_system_user(){
			$info_position = sanitize($this->input->post('info_position'));
			$info_user_fname = sanitize($this->input->post('info_user_fname'));
			$info_user_mname = sanitize($this->input->post('info_user_mname'));
			$info_user_lname = sanitize($this->input->post('info_user_lname'));
			$info_username = sanitize($this->input->post('info_username'));
			$info_password = sanitize($this->input->post('info_password'));
			$info_re_password = sanitize($this->input->post('info_re_password'));			
			
			if ($info_position == "" || $info_user_fname == "" || $info_user_lname == "" || $info_username == "" || $info_password == "" || $info_re_password == "") {	//Check if required fields are filled up			
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {
				if($this->session->userdata('position_id') != ""){ //admin
					$isExists = $this->model_settings->get_system_user_unique($info_username);
					if($isExists == 0){
						if($info_password == $info_re_password){
							$row = array(
								'username' => $info_username, 
								'user_fname' => $info_user_fname, 
								'user_mname' => $info_user_mname, 
								'user_lname' => $info_user_lname, 
								'position_id' => $info_position, 
								'password' => password_hash($info_password,PASSWORD_BCRYPT), 
								'date_activated' => date('Y-m-d H:i:s'), 
								'date_created' => date('Y-m-d H:i:s'), 
								'enabled' => 1, 
							);
							$id = $this->model_settings->insert_system_user($row);
							$data = array("success" => 1, 'message' => 'Successfully Added');
						}else{
							$data = array('success' => 0, 'message' => 'Password and Password Confirmation do not match.');
						}
					}
					else{
						$data = array('success' => 0, 'message' => 'Username is already taken. Please try again.');
					}
					
					
				} else {
					$this->logout();
				}
			}
			generate_json($data);
		}
		
		public function get_system_user() {
			$id = sanitize($this->input->post('user_id'));
			
			$query = $this->model_settings->get_system_user($id);
			
			if ($query->num_rows() > 0) {
				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}
			
			generate_json($data);
			
		}
		
		public function get_system_user_unique($info_unique) {
			
			$query = $this->model_settings->get_warehouse_location_unique($info_unique);
			
			if ($query->num_rows() > 0) {
				
				$data = array('success' => 1, 'result' => $query->result());
			}else{
				$data = array('success' => 0, 'result' => 'no result');
			}
			
			generate_json($data);
			
		}
		
		public function update_system_user() {
			
			$info_position = sanitize($this->input->post('info_position'));
			$info_user_fname = sanitize($this->input->post('info_user_fname'));
			$info_user_mname = sanitize($this->input->post('info_user_mname'));
			$info_user_lname = sanitize($this->input->post('info_user_lname'));
			$info_username = sanitize($this->input->post('info_username'));
			$info_password = sanitize($this->input->post('info_password'));
			$info_re_password = sanitize($this->input->post('info_re_password'));			
			
			if ($info_position == "" || $info_user_fname == "" || $info_user_lname == "" || $info_username == "") {	//Check if required fields are filled up			
				$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
			} else {
				if($this->session->userdata('position_id') != ""){ //admin
					$orig_user = $this->model_settings->get_system_user($this->input->post("info_user_id"));
					$row = array(
						'username' => $info_username, 
						'user_fname' => $info_user_fname, 
						'user_mname' => $info_user_mname, 
						'user_lname' => $info_user_lname, 
						'position_id' => $info_position, 
						'date_updated' => date('Y-m-d H:i:s')
					);
					if($info_password == ""){
						if($info_username == $orig_user->result_array()[0]["username"]){
							$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
							$data = array("success" => 1, 'message' => 'Successfully updated');
						}else{
							$isExists = $this->model_settings->get_system_user_unique($info_username);
							if($isExists == 0){
								$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
								$data = array("success" => 1, 'message' => 'Successfully updated');
							}else{
								$data = array('success' => 0, 'message' => 'Username is already taken. Please try again.');
							}
						}
					}else{
						if($info_password == $info_re_password){
							$row["password"] = password_hash($info_password,PASSWORD_BCRYPT); 		
							if($info_username == $orig_user->result_array()[0]["username"]){
								$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
								$data = array("success" => 1, 'message' => 'Successfully updated');
							}else{
								$isExists = $this->model_settings->get_system_user_unique($info_username);
								if($isExists == 0){
									$id = $this->model_settings->update_system_user($this->input->post("info_user_id"),$row);
									$data = array("success" => 1, 'message' => 'Successfully updated');
								}else{
									$data = array('success' => 0, 'message' => 'Username is already taken. Please try again.');
								}
							}
						}else{
							$data = array('success' => 0, 'message' => 'Password and Password Confirmation do not match.');
						}
					}
				} else {
					$this->logout();
				}
			}
			generate_json($data);
			
		}
		
		public function delete_system_user() {
			
			$del_user_id = sanitize($this->input->post('del_user_id'));
			
			if ($del_user_id == "") {
				$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
			}else{
				$query = $this->model_settings->delete_system_user($del_user_id);
				
				$data = array("success" => 1, 'message' => "System User Deleted!" , "del_user_id" => $del_user_id);
			}
			
			generate_json($data);
		}
	
	// End - System Users

	public function void_record($token = '') { 
		$this->isLoggedIn();
		
		$credit_id = sanitize($this->input->post('mode_payment'));
		$idno      = sanitize($this->input->post('idno'));
		$rowrec      = sanitize($this->input->post('rowrec'));
		
		$data_admin = array(
			// get data using email
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'get_users' => $this->model->get_users($this->session->userdata('user_id'))->row()
		);
		
		if ($this->session->userdata('position_id') != '') { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_voidrecord', $data_admin);
		}else{
			$this->logout();
		}
		
	}
	
	// Start of Permit type

		public function permit_type($token = ''){ //
			$this->isLoggedIn();

			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
			);

			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_permit_type', $data_admin);
		}

		public function permit_type_table(){
			$this->isLoggedIn();
			
			$description = $this->input->post("description");
			$query = $this->model_settings->permit_type_table($description);
			echo json_encode($query);
		}

		public function save_permit_type() {
			$this->isLoggedIn();

			$this->load->library('form_validation');

			$this->form_validation->set_rules('info_desc', 'Permit Type Description', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data = array('success' => 0, 'message' => validation_errors());
			}
			else {
				$description = sanitize($this->input->post('info_desc'));

				if ($this->model_settings->is_unique_permit_type($description)->num_rows() == 0) {
					$values = array(
						$description,
						date("Y-m-d H:i:s"),
						date("Y-m-d H:i:s")
					);
		
					if ($this->model_settings->save_permit_type($values)) {
						$data = array("success" => 1, 'message' => 'Record has been saved.');
					}
					else {
						$data = array("success" => 0, 'message' => 'Record saving failed.');
					}
				}
				else {
					$data = array("success" => 0, 'message' => 'Record already exists.');
				}
			}

			generate_json($data);
		}

		public function get_permit_type() {
			$id = sanitize($this->input->post('id'));

			$query = $this->model_settings->get_permit_type($id);

			if ($query->num_rows() > 0) {
				$data = array('success' => 1, 'result' => $query->result());
			}
			else {
				$data = array('success' => 0, 'result' => 'No data found.');
			}

			generate_json($data);

		}

		public function update_permit_type() {
			$this->isLoggedIn();

			$this->load->library('form_validation');

			$this->form_validation->set_rules('info_id', 'Permit Type Id', 'trim|required');
			$this->form_validation->set_rules('info_desc', 'Permit Type Description', 'trim|required|is_unique[pb_permit_type.description]');

			if ($this->form_validation->run() == FALSE) {
				$data = array('success' => 0, 'message' => validation_errors());
			}
			else {
				$values = array(
					sanitize($this->input->post('info_desc')),
					date("Y-m-d H:i:s"),
					sanitize($this->input->post('info_id'))
				);

				if ($this->model_settings->update_permit_type($values)) {
					$data = array("success" => 1, 'message' => 'Record has been saved.');
				}
				else {
					$data = array("success" => 0, 'message' => 'Record saving failed.');
				}
			}

			generate_json($data);
		}

		public function delete_permit_type() {
			$this->isLoggedIn();

			$this->load->library('form_validation');

			$this->form_validation->set_rules('dm_info_id', 'Permit Type Id', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data = array('success' => 0, 'message' => validation_errors());
			}
			else {
				$values = array(
					date("Y-m-d H:i:s"),
					0,
					sanitize($this->input->post('dm_info_id'))
				);

				if ($this->model_settings->delete_permit_type($values)) {
					$data = array("success" => 1, 'message' => 'Record has been deleted.');
				}
				else {
					$data = array("success" => 0, 'message' => 'Record deletion failed.');
				}
			}

			generate_json($data);
		}

	// End of Permit type

	public function portal_home_announcement($token = '') {
		$this->isLoggedIn();

		$data_admin = array(
			 // get data using email
			'token' => $token,
			'get_announcement' => $this->model_settings->get_portal_announcement()->row(),
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/portal_home_announcement', $data_admin);
		}else{
			//$this->logout();
		}
	}

	public function editPortalAnnouncement(){
		$title = sanitize($this->input->post('title'));
		$details = sanitize($this->input->post('details'));
		$file = sanitize($this->input->post('file'));

		$files = $_FILES;

		if($files == ""){

			$filename = $files;

		}else{
			 //assigning unique filename
		$tmp = explode('.', $_FILES['file']['name']); 
		$document_ext = end($tmp);
		$raw_filename = 'portal_'."_".microtime(true);
		$filename = str_replace(".","_",$raw_filename) . ".".$document_ext;

		$_FILES['userfile'] = [
			'name'     => $filename,
			'type'     => $files['file']['type'],
			'tmp_name' => $files['file']['tmp_name'],
			'error'    => $files['file']['error'],
			'size'     => $files['file']['size']
		];

		//$F[] = $_FILES['userfile'];
		//Upload requirements
		$config['upload_path'] = './assets/img/portal_image';
		$config['allowed_types'] = 'jpg|png|jpeg';
		// $config['max_size'] = '2048';

		$this->load->library('upload',$config);

		$this->upload->do_upload();

		}

		if ($title != "" || $details != "" || $document != "") {
			$this->model_settings->save_portal_announcement($title, $details, $filename);
			$data = array("success" => 1, 'message' => 'Record has been saved.');
		}
		else {
			$data = array("success" => 0, 'message' => 'Record saving failed.');
		}

		generate_json($data);
	}

	//050319
	public function upgrade_package($token = ''){
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		$this->load->view('includes/header', $data_admin);
		$this->load->view('settings/settings_upgrade_package', $data_admin);
	}

	public function upgrade_package_table() {
		$this->isLoggedIn();

		$search = $this->input->post('search');
		$package_id = $this->input->post('package_id');
		$package_description = $this->input->post('package_description');

		$query = $this->model_settings->upgrade_package_table($search, $package_id, $package_description);
		echo json_encode($query);
	}

	public function save_package() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('cm_packagename', 'Package Name', 'trim|required|is_unique[8_packages.description]');
		$this->form_validation->set_rules('cm_packageweight', 'Package Weight', 'trim|required');
		// $this->form_validation->set_rules('cm_atype', 'Atype', 'trim|required');
		$this->form_validation->set_rules('cm_amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('cm_country_name', 'Country', 'trim|required');

		if (empty($this->input->post('cm_atype'))) {
			$response['success'] = false;
			$response['message'] = 'Please choose package type';
			echo json_encode($response);
			die();
		}

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array(
				$this->input->post('cm_packagename'),
				$this->input->post('cm_packageweight'),
				$cm_atype = implode(', ', $this->input->post('cm_atype')),
				$this->input->post('cm_amount'),
				$this->input->post('cm_country_name')
			);


			if ($this->model_settings->insert_package_details($values)) {
				$response['success'] = true;
				$response['message'] = 'Data has been successfully saved.';
			}
			else {
				$response['success'] = false;
				$response['message'] = 'An error occured during the saving of data.';
			}
		}

		echo json_encode($response);
	}

	public function update_package() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('cm_packagename', 'Package Name', 'trim|required');
		$this->form_validation->set_rules('cm_packageweight', 'Weight', 'trim|required');
		$this->form_validation->set_rules('cm_amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('cm_country_name', 'Country', 'trim|required');
		$this->form_validation->set_rules('cm_packageid', 'Package ID', 'trim|required');

		if (empty($this->input->post('cm_atype'))) {
			$response['success'] = false;
			$response['message'] = 'Please choose package type';
			echo json_encode($response);
			die();
		}

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array(
				$this->input->post('cm_packagename'),
				$this->input->post('cm_packageweight'),
				$cm_atype = implode(', ', $this->input->post('cm_atype')),
				$this->input->post('cm_amount'),
				$this->input->post('cm_country_name'),
				$this->input->post('cm_packageid'),
			);

			if ($this->model_settings->update_package_details($values)) {
				$response['success'] = true;
				$response['message'] = 'Data has been successfully updated.';
			}
			else {
				$response['success'] = false;
				$response['message'] = 'An error occured during the updating of data.';
			}
		}

		echo json_encode($response);
	}

	public function delete_package() {
		$this->isLoggedIn();

		echo json_encode($this->model_settings->delete_package());
	}
	
	public function get_package_details() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('id', 'Package ID', 'trim|required');

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array($this->input->post('id'));

			echo json_encode($this->model_settings->get_package_details($values));
		}
	}

	public function autocomplete_country(){
		$this->isLoggedIn();

		$texttyped = $this->input->post('texttyped');

		$result = $this->model_settings->autocomplete_country($texttyped);

		echo json_encode($result);
	}

	public function upgrade_package_items($token = '', $id){
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'id' => $id,
			'package_details' => $this->model_settings->get_package_details($id)
		);

		$this->load->view('includes/header', $data_admin);
		$this->load->view('settings/settings_upgrade_package_items', $data_admin);
	}

	public function upgrade_package_details_table() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('up_id', 'Package ID', 'trim|required');

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array($this->input->post('up_id'));

			echo json_encode($this->model_settings->upgrade_package_details_table($values));
		}
	}

	public function autocomplete_item(){
		$this->isLoggedIn();

		$texttyped = $this->input->post('texttyped');

		$result = $this->model_settings->autocomplete_item($texttyped);

		echo json_encode($result);
	}

	public function get_package_items() {
		$this->isLoggedIn();

		$texttyped = $this->input->post('texttyped');

		$result = $this->model_settings->get_package_items($texttyped);

		echo json_encode($result);

	}

	public function save_package_item() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('cm_upid', 'Package', 'trim|required');
		$this->form_validation->set_rules('cm_itemid', 'Item', 'trim|required');
		$this->form_validation->set_rules('cm_quantity', 'Quantity', 'trim|required|greater_than[0]');

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array(
				$this->input->post('cm_upid'),
				$this->input->post('cm_itemid'),
				$this->input->post('cm_quantity')
			);

			if ($this->model_settings->insert_package_item($values)) {
				$response['success'] = true;
				$response['message'] = 'Item has been successfully added.';
			}
			else {
				$response['success'] = false;
				$response['message'] = 'An error occured during the adding of the item.';
			}
		}

		echo json_encode($response);
	}

	public function update_package_item() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('cm_upid', 'Package', 'trim|required');
		$this->form_validation->set_rules('cm_itemid', 'Item', 'trim|required');
		$this->form_validation->set_rules('cm_quantity', 'Quantity', 'trim|required|greater_than[0]');

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array(
				$this->input->post('cm_quantity'),
				$this->input->post('cm_upid'),
				$this->input->post('cm_itemid')
			);

			if ($this->model_settings->update_package_item($values)) {
				$response['success'] = true;
				$response['message'] = 'Item has been successfully updated.';
			}
			else {
				$response['success'] = false;
				$response['message'] = 'An error occured during the updating of the item.';
			}
		}

		echo json_encode($response);
	}

	public function delete_package_item() {
		$this->isLoggedIn();

		$this->load->library('form_validation');

		$this->form_validation->set_rules('del_item_id', 'Item', 'trim|required');

		if ($this->form_validation->run() === FALSE) {
			$response['success'] = false;
			$response['message'] = validation_errors();
		}
		else {
			$values = array(
				$this->input->post('del_item_id')
			);

			if ($this->model_settings->delete_package_item($values)) {
				$response['success'] = true;
				$response['message'] = 'Item has been successfully updated.';
			}
			else {
				$response['success'] = false;
				$response['message'] = 'An error occured during the updating of the item.';
			}
		}

		echo json_encode($response);
	}

	public function package_release_category($token = ''){ //
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'items' => $this->model_settings->get_items(),
		);

		$this->load->view('includes/header', $data_admin);
		$this->load->view('settings/settings_package_release_category', $data_admin);
	}

	public function package_release_cat_table(){
		$this->isLoggedIn();
		
		$item = $this->input->post("item");

		$query = $this->model_settings->package_release_cat_table($item);
		echo json_encode($query);
	}

	public function add_releaseitem(){
		$category = $this->input->post("category");
		$item = $this->input->post("item");

		if ($category == "" || $item == "") {
			
			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		} else {

			if($this->session->userdata('position_id') != ""){ //admin
				$isExists = $this->model_settings->get_inventory_itemid_unique($item);

				if($isExists->num_rows() == 0)
				{
					$id = $this->model_settings->add_releaseitem($category, $item);

					$data = array("success" => 1, 'message' => 'Successfully Added');
				}
				else
				{
					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}


			} else {
				$this->logout();
			}
		}
		generate_json($data);
	}

	public function delete_releaseitem(){
		$id = $this->input->post("id");

		if ($id == "") {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}else{
			$query = $this->model_settings->delete_releaseitem($id);

			$data = array("success" => 1, 'message' => "Item Deleted!" , "id" => $id);
		}

		generate_json($data);
	}

	public function edit_releaseitem(){
		$category = $this->input->post("category");
		$item = $this->input->post("item");
		$id = $this->input->post("id");

		if ($category == "" || $item == "") {

			$data = array("success" => 0, 'message' => 'Please fill up all required fields.');
		}else{

			$isExists = $this->model_settings->get_packagerelease_info($item)->row();

			if($isExists == ''){

				$id = $this->model_settings->edit_releaseitem($category, $item, $id);

				$data = array("success" => 1, 'message' => 'Successfully Updated!');
			}else{

				if($isExists->id == $id && $isExists->itemid != $item){

					$id = $this->model_settings->edit_releaseitem($category, $item, $id);

					$data = array("success" => 1, 'message' => 'Successfully Updated!');
				}else{

					$data = array('success' => 0, 'message' => 'Record already exists. Please try again.');
				}
			}
		}
		generate_json($data);
	}

	public function get_packagerelease_id(){
		$id = sanitize($this->input->post('id'));

		$query = $this->model_settings->get_packagerelease_id($id);

		if ($query->num_rows() > 0) {
			$data = array("success" => 1, "result" => $query->result());
		}else{
			$data = array("success" => 0, 'result' => "no result");
		}
		
		generate_json($data);
	}
	// End of Upgrade Package

	// Start of Tax Settings

		public function tax_settings($token = '') {
			$this->isLoggedIn();
			
			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
				'positions' => $this->model_settings->get_positions()->result_array()
			);
			
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/tax_settings', $data_admin);
		}

		public function taxes_table() {
			$this->isLoggedIn();

			$data = array(
				"data" => $this->model_settings->taxes_table()->result()
			);
			
			echo json_encode($data);
		}

		public function save_tax() {
			$this->load->library('form_validation');

			$tax_id = $this->input->post('tax_id');
			$tax_desc = $this->input->post('tax_desc');
			$tax_value = $this->input->post('tax_value');

			$this->form_validation->set_rules('tax_desc', 'Tax Description', 'trim|required');
			$this->form_validation->set_rules('tax_value', 'Tax Value', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[100]');

			if ($tax_id != "")
				$this->form_validation->set_rules('tax_id', 'Tax ID', 'trim|required');

			if ($this->form_validation->run() === FALSE) {
				$data = array('success' => 0, 'message' => validation_errors());
			}
			else {
				if ($tax_id == "") { // if empty proceed to saving, else update exsisting data
					$values = array($tax_desc, $tax_value, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));

					if ($this->model_settings->save_tax($values))
						$data = array('success' => 1, 'message' => 'Successfully saved user data');
					else
						$data = array('success' => 0, 'message' => 'Failed saving user data');
				}
				else {
					$values = array($tax_desc, $tax_value, date("Y-m-d H:i:s"), $tax_id);

					if ($this->model_settings->update_tax($values))
						$data = array('success' => 1, 'message' => 'Successfully updated tax data');
					else
						$data = array('success' => 0, 'message' => 'Failed updating User data');
				}
			}

			generate_json($data);
		}

		public function get_tax() {
			$tax_id = $this->input->post('tax_id');

			generate_json($this->model_settings->get_tax($tax_id)->row());
		}

		public function delete_tax() {
			$values = array($this->input->post('tax_id'));

			if ($this->model_settings->delete_tax($values))
				$data = array('success' => 1, 'message' => 'Successfully deleted tax data');
			else
				$data = array('success' => 0, 'message' => 'Failed deleting tax data');

			generate_json($data);
		}

	// End of Tax Settings

	# Start of Discounts Settings

		# Discounts View
		public function discounts($token = '') {
			$this->isLoggedIn();
			
			$data_admin = array(
				'token' => $token,
				'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
				'positions' => $this->model_settings->get_positions()->result_array()
			);
			
			$this->load->view('includes/header', $data_admin);
			$this->load->view('discounts/discount_settings', $data_admin);
		}

		# Get current discount
		public function get_current_idisc(){
			$get_current_idisc = $this->model_settings->get_current_idisc()->row();

			generate_json($get_current_idisc);
		}

		public function save_new_idisc(){
			$new_disc = $this->input->post('disc_perc');

			$save_new_idisc = $this->model_settings->save_new_idisc($new_disc);

			if($save_new_idisc) {
				$data = array('success' => 1, 'message' => 'Discount is saved successfully!');
			}
			else {
				$data = array('success' => 0, 'message' => 'Failed to update discount.');
			}

			generate_json($data);
		}


	# End of Discounts Settings

	public function test()
	{
		$date = date("Y-m-d h:i:s");
		echo $date;
	}

	public function commission_supplier($token = ''){ 
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_commission_supplier', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function commission_supplier_product($token = '', $supid){ 
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
			'supid' => $supid 
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_commission_supplier_product', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function targetsales_agent($token = ''){ 
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row()
		);

		if ($this->session->userdata('position_id') != "") { // admin
			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_targetsales_agent', $data_admin);
		}else{
			$this->logout();
		}
	}

	public function get_supplier_info_comm() {
		$supid = sanitize($this->input->post('id'));
		$itemid = sanitize($this->input->post('itemid'));
		
		$query = $this->model_settings->get_supplier_info_comm($supid,$itemid);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function get_agent_targetsales() {
		$id = sanitize($this->input->post('id'));


		$query = $this->model_settings->get_agent_targetsales($id);

		if ($query->num_rows() > 0) {

			$data = array('success' => 1, 'result' => $query->result());
		}else{
			$data = array('success' => 0, 'result' => 'no result');
		}

		generate_json($data);

	}

	public function add_commission_percentage() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_itemid = sanitize($this->input->post('info_itemid'));
		$info_percentage = sanitize($this->input->post('info_percentage'));
		$username = $this->session->userdata('username');
		$trandate = date('Y-m-d');
		
		$check_comm_supp = $this->model_settings->check_comm_supp($info_id,$info_itemid)->num_rows();

		if($check_comm_supp > 0){
			$this->model_settings->update_comm_supp($info_id,$info_itemid,$info_percentage,$trandate,$username);
			$data = array("success" => 1, 'message' => 'Successfully updated');
		}
		else if($check_comm_supp == 0){
			$this->model_settings->insert_comm_supp($info_id,$info_itemid,$info_percentage,$trandate,$username);
			$data = array("success" => 1, 'message' => 'Successfully updated');
		}else{
			$data = array("success" => 0, 'message' => 'Error.');
		}

		generate_json($data);
	

	}

	public function add_target_sales() {

		$info_id = sanitize($this->input->post('info_id'));
		$info_target_sales = sanitize($this->input->post('info_target_sales'));
		$info_percentage = sanitize($this->input->post('info_percentage'));
		$username = $this->session->userdata('username');
		$trandate = date('Y-m-d');
		
		$check_target_sales = $this->model_settings->check_target_sales($info_id)->num_rows();

		if($info_target_sales == "" || $info_percentage == ""){
			$data = array("success" => 0, 'message' => 'Please fill out required fields.');
		}
		else if($check_target_sales > 0){
			$this->model_settings->update_target_sales($info_id,$info_target_sales,$info_percentage,$trandate,$username);
			$data = array("success" => 1, 'message' => 'Successfully updated');
		}
		else if($check_target_sales == 0){
			$this->model_settings->insert_target_sales($info_id,$info_target_sales,$info_percentage,$trandate,$username);
			$data = array("success" => 1, 'message' => 'Successfully updated');
		}else{
			$data = array("success" => 0, 'message' => 'Error.');
		}

		generate_json($data);
	

	}

	public function member($token = '') {
		$this->isLoggedIn();
		if ($this->loginstate->get_access()['members']['view'] == 1){ 
			$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
	        $main_nav_id = $this->views_restriction($content_url);

			$data_admin = array(
				'token' => $token,
				'main_nav_id' => $main_nav_id,
	            'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
	            'shop_options' => $this->model->get_shop_options()->result_array()
			);

			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_member', $data_admin);
		}else{
			$this->load->view('error_404');
		}
	}

	public function member_list(){
		$this->isLoggedIn();
		$filters = [
			'_record_status' => $this->input->post('_record_status'),
			'_shop'			 => $this->input->post('_shop'),
			'_shopbranch'	 => $this->input->post('_shopbranch'),
			'_name'			 => $this->input->post('_name'),
			'_mobile'		 => $this->input->post('_mobile'),
			'_email'		 => $this->input->post('_email'),
		];
        $query = $this->model_settings->member_list($filters, $_REQUEST);
        generate_json($query);
	}
	
	public function export_member_list(){
		$this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
        $query = $this->model_settings->member_list($filters, $requestData, true);
		// print_r($query);
        // exit();
		$fromdate = date('Y-m-d');
		$shopid = $filters['_shop'];
		$branchid = 'main';
        $fil_arr = [
			'Name' => $filters['_name'],
			'Mobile' => $filters['_mobile'],
            'Record Status' => array(
				'' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
			)[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $fromdate, "Members", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Members', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Members");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        // $sheet->getColumnDimension('F')->setWidth(15);

        $sheet->setCellValue("A6", 'Shop Name');
        $sheet->setCellValue('B6', 'Shop Branch');
        $sheet->setCellValue('C6', 'Name');
        $sheet->setCellValue('D6', 'Mobile Number');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[1],
                '2' => $row[2],
                '3' => $row[3],
                '4' => $row[4],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Members ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
	}
	
	public function member_disable_modal_confirm(){
        $this->isLoggedIn();
        
        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
		}
		
		$member_data = $this->model_settings->get_member_data($disable_id)->row();		
		$remarks = $member_data->fname." ".$member_data->lname." has been successfully ".$record_text;

        if ($disable_id > 0 && $record_status > 0) {
			$query = $this->model_settings->member_disable_modal_confirm($disable_id, $record_status);			

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Members', $remarks, $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}
	
	public function member_delete_modal_confirm(){
        $this->isLoggedIn();
        
        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id > 0) {
			$query = $this->model_settings->member_delete_modal_confirm($delete_id);
			
			$member_data = $this->model_settings->get_member_data($delete_id)->row();		
			$remarks = $member_data->fname." ".$member_data->lname." has been successfully deleted";

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Members', $remarks, 'deleted', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}
	
	public function get_member_data(){
        $this->isLoggedIn();
        
		$edit_id = sanitize($this->input->post('edit_id'));
		
		$query = $this->model_settings->get_member_data($edit_id)->row();
	
		if ($query) {
			$data = array("success" => 1, 'message' => "Record deleted successfully!", 'result' => $query);
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}
	
	public function member_update_modal_confirm(){
        $this->isLoggedIn();
        
		$id = sanitize($this->input->post('id'));
		$shop = sanitize($this->input->post('shop'));
		$fname = sanitize($this->input->post('fname'));
		$mname = sanitize($this->input->post('mname'));
		$lname = sanitize($this->input->post('lname'));
		// $email = sanitize($this->input->post('email'));
		$mobile = sanitize($this->input->post('mobile'));
		$shopbranch = sanitize($this->input->post('shopbranch'));
		
		$query = $this->model_settings->member_update_modal_confirm($id, $shop, $fname, $mname, $lname, $mobile, $shopbranch);

		$cur_val = [
			'sys_shop' => $shop,
			'fname' => $fname,
			'mname' => $mname,
			'lname' => $lname,
			'mobile' => $mobile,
			'branchid' =>$shopbranch
		];

		$prev_val = $this->input->post('prev_val');

		$changes = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val),$prev_val);
		$remarks = "Member ".$fname." ".$lname." has been updated successfully. \nChanges: \n$changes";		
	
		if ($query) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$this->audittrail->logActivity('Members', $remarks, 'update', $this->session->userdata('username'));
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}

	public function get_non_members(){
		$this->isLoggedIn();
		
        $query = $this->model->get_non_members()->result_array();
        generate_json($query);
	}
	
	public function member_add_modal_confirm(){
        $this->isLoggedIn();
        
		$shop = sanitize($this->input->post('shop'));
		$user = sanitize($this->input->post('user'));
		$fname = sanitize($this->input->post('fname'));
		$mname = sanitize($this->input->post('mname'));
		$lname = sanitize($this->input->post('lname'));
		// $email = sanitize($this->input->post('email'));
		$mobile = sanitize($this->input->post('mobile'));
		$shopbranch = sanitize($this->input->post('shopbranch'));

		$checker = $this->model_settings->check_duplicate_member($user);

		if($checker){
			$query = $this->model_settings->member_add_update_modal_confirm($shop, $user, $fname, $mname, $lname, $mobile, $shopbranch);
		} else{
			$query = $this->model_settings->member_add_modal_confirm($shop, $user, $fname, $mname, $lname, $mobile, $shopbranch);
		}
		
		$remarks = $fname.' '.$lname.' sucessfully added to Members';
		
		if ($query) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$this->audittrail->logActivity('Members', $remarks, 'add', $this->session->userdata('username'));     
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}

	public function product_category($token = '') {
		$this->isLoggedIn();
		if ($this->loginstate->get_access()['category']['view'] == 1){
			$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
	        $main_nav_id = $this->views_restriction($content_url);

			$data_admin = array(
				'token' => $token,
				'main_nav_id' => $main_nav_id,
	            'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
			);

			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_product_category', $data_admin);
		}else{
    		$this->load->view('error_404');
    	}
	}

	public function product_category_list(){
		$this->isLoggedIn();
		$filters = [
			'_record_status' => $this->input->post('_record_status'),
			'_category' => $this->input->post('_category'),
			'_name' => $this->input->post('_name'),
			'_onmenu' => $this->input->post('_onmenu'),
			'_priority' => $this->input->post('_priority'),
		];
        $query = $this->model_settings->product_category_list($filters, $_REQUEST);
        generate_json($query);
	}

	public function export_product_category_list(){
		$this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
		$query = $this->model_settings->product_category_list($filters, $requestData, true);
		// print_r($query);
        // exit();
		$fromdate = date('Y-m-d');
		$shopid = $filters['_shop'];
		$branchid = 'main';
        $fil_arr = [
			'Category' => $filters['_category'],
			'Name' => $filters['_name'],
			'Priority' => $filters['_priority'],
            'Record Status' => array(
				'' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
			)[$filters['_record_status']],
            'On Menu' => array(
				'' => 'All Menu Type', 1 => 'Displayed', 2 => 'Not Displayed'
			)[$filters['_onmenu']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $fromdate, "Product Category", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Product Category', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Product Category");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);

        $sheet->setCellValue("A6", 'Category Code');
        $sheet->setCellValue('B6', 'Category Name');
        $sheet->setCellValue('C6', 'On Menu');
        $sheet->setCellValue('D6', 'Priority');
        $sheet->setCellValue('E6', 'Last Updated');

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
        $filename = 'Product Category ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
	}

	public function product_category_disable_modal_confirm(){
        $this->isLoggedIn();
        
        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
		}
		
		//get category data using disable_id
		$category_data = $this->model_settings->get_product_category_data($disable_id)->row();		
		$remarks = $category_data->category_code." has been successfully ".$record_text;

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->model_settings->product_category_disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Product Categories', $remarks, $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}

	public function product_category_delete_modal_confirm(){
        $this->isLoggedIn();
        
		$delete_id = sanitize($this->input->post('delete_id'));
		
		//get category data using delete_id
		$category_data = $this->model_settings->get_product_category_data($delete_id)->row();		
		$remarks = $category_data->category_code." has been successfully deleted";

        if ($delete_id > 0) {
            $query = $this->model_settings->product_category_delete_modal_confirm($delete_id);

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Product Categories', $remarks, 'deleted', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}

	public function get_product_category_data(){
        $this->isLoggedIn();
        
		$edit_id = sanitize($this->input->post('edit_id'));
		
		$query = $this->model_settings->get_product_category_data($edit_id)->row();
	
		if ($query) {
			$data = array("success" => 1, 'message' => "Record deleted successfully!", 'result' => $query);
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}

	public function product_category_update_modal_confirm(){
        $this->isLoggedIn();
        
		$id = sanitize($this->input->post('id'));
		$category = sanitize($this->input->post('category'));
		$name = sanitize($this->input->post('name'));
		$onmenu = sanitize($this->input->post('onmenu'));
		$priority = sanitize($this->input->post('priority'));
		
		$get_productcat      = $this->model_settings->get_productcat($id)->row();
		$get_productcat_code = $this->model_settings->get_productcat_code($category)->num_rows();

		$cur_val = [
			'category_code' => $category,
			'category_name' => $name,
			'on_menu' => $onmenu,
			'priority' => $priority
		];

		$prev_val = $this->input->post('prev_val');

		$changes = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val),$prev_val);
		$remarks = "Product Category ".$cur_val['category_name']." has been updated successfully. \nChanges: \n$changes";
		
		if($get_productcat->category_code != $category){
			if($get_productcat_code > 0){
				$message = 'Category Code already exists.';
				$query = false;
			}else{
				$query = $this->model_settings->product_category_update_modal_confirm($id, $category, $name, $onmenu, $priority);
			}
		}else{
			$query = $this->model_settings->product_category_update_modal_confirm($id, $category, $name, $onmenu, $priority);
		}
		
		if ($query) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$this->audittrail->logActivity('Payment Types', $remarks, 'update', $this->session->userdata('username'));
		}else{
			$data = array("success" => 0, 'message' => $message);
		}
        
        generate_json($data);
	}

	public function product_category_add_modal_confirm(){
        $this->isLoggedIn();
        
		$category = sanitize($this->input->post('category'));
		$name = sanitize($this->input->post('name'));
		$onmenu = sanitize($this->input->post('onmenu'));
		$priority = sanitize($this->input->post('priority'));

		$get_productcat_code = $this->model_settings->get_productcat_code($category)->num_rows();
		
		if($get_productcat_code > 0){
			$message = 'Category Code already exists.';
			$query = false;
		}else{
			$query = $this->model_settings->product_category_add_modal_confirm($category, $name, $onmenu, $priority);
		}

		$remarks = $name." successfully added to Product Categories";
		
		if ($query) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$this->audittrail->logActivity('Product Categories', $remarks, 'add', $this->session->userdata('username'));
		}else{
			$data = array("success" => 0, 'message' => $message);
		}
        
        generate_json($data);
	}

	public function shipping_partner($token = '') {
		$this->isLoggedIn();
		if ($this->loginstate->get_access()['shipping_partners']['create'] == 1){
			$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
	        $main_nav_id = $this->views_restriction($content_url);

			$data_admin = array(
				'token' => $token,
				'main_nav_id' => $main_nav_id,
	            'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result()
			);

			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_shipping_partner', $data_admin);
		}else{
			$this->load->view('error_404');
		}	
	}

	public function shipping_partner_list(){
		$this->isLoggedIn();
		$filters = [
			'_record_status'  => $this->input->post('_record_status'),
			'code'  => $this->input->post('code'),
			'name'  => $this->input->post('name'),
		];
        $query = $this->model_settings->shipping_partner_list($filters, $_REQUEST);
        generate_json($query);
	}

	public function export_shipping_partner_list(){
		$this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
		$query = $this->model_settings->shipping_partner_list($filters, $requestData, true);
		// print_r($query);
        // exit();
		$fromdate = date('Y-m-d');
        $fil_arr = [
			'Code' => $filters['code'],
			'Name' => $filters['name'],
            'Record Status' => array(
				'' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
			)[$filters['_record_status']],
        ];
        extract($this->audittrail->get_ReportExportRemarks("", "", $fromdate, $fromdate, "Shipping Partners", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Shipping Partners', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Shipping Partners");
        $sheet->setCellValue('B2', "Filters: $_filters");
        $sheet->setCellValue('B3', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);

        $sheet->setCellValue("A6", 'Shipping Code');
        $sheet->setCellValue('B6', 'Shipping Name');
        $sheet->setCellValue('C6', 'Date Created');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:C6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[0],
                '2' => $row[1],
                '3' => $row[2],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Shipping Partners ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
	}

	public function shipping_partner_disable_modal_confirm(){
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
            $query = $this->model_settings->shipping_partner_disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Shipping Partners', "Shipping partner $record_name has been $record_text successfully.", $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}

	public function shipping_partner_delete_modal_confirm(){
        $this->isLoggedIn();
        
        $record_name = sanitize($this->input->post('record_name'));
        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id > 0) {
            $query = $this->model_settings->shipping_partner_delete_modal_confirm($delete_id);

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Shipping Partners', "Shipping partner $record_name has been deleted successfully.", 'delete', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}

	public function get_shipping_partner_data(){
        $this->isLoggedIn();
        
		$edit_id = sanitize($this->input->post('edit_id'));
		
		$query = $this->model_settings->get_shipping_partner_data($edit_id)->row();
	
		if ($query) {
			$data = array("success" => 1, 'message' => "Record deleted successfully!", 'result' => $query);
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}

	public function shipping_partner_update_modal_confirm(){
        $this->isLoggedIn();
		
		$cur_val = $this->input->post('cur_val');
		$prev_val = $this->input->post('prev_val');

		$id           = sanitize($this->input->post('cur_val')['id']);
		$code         = sanitize($this->input->post('cur_val')['code']);
		$name         = sanitize($this->input->post('cur_val')['name']);
		$api_isset    = sanitize($this->input->post('cur_val')['api_isset']);
		$dev_api_url  = sanitize($this->input->post('cur_val')['dev_api_url']);
		$test_api_url = sanitize($this->input->post('cur_val')['test_api_url']);
		$prod_api_url = sanitize($this->input->post('cur_val')['prod_api_url']);

		$main = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val,$prev_val), $prev_val);
	
		$query = $this->model_settings->shipping_partner_update_modal_confirm($id, $code, $name, $api_isset, $dev_api_url, $test_api_url, $prod_api_url);
	
		if ($query) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
			$this->audittrail->logActivity('Shipping Partners', "Shipping partner $code has been updated successfully. \nChanges: \n$main", 'update', $this->session->userdata('username'));
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}

	public function shipping_partner_add_modal_confirm(){
        $this->isLoggedIn();
        
		$code 	      = sanitize($this->input->post('code'));
		$name         = sanitize($this->input->post('name'));
		$api_isset    = sanitize($this->input->post('api_isset'));
		$dev_api_url  = sanitize($this->input->post('dev_api_url'));
		$test_api_url = sanitize($this->input->post('test_api_url'));
		$prod_api_url = sanitize($this->input->post('prod_api_url'));
	
		$query = $this->model_settings->shipping_partner_add_modal_confirm($code, $name, $api_isset, $dev_api_url, $test_api_url, $prod_api_url);
		
		if ($query) {
			$data = array("success" => 1, 'message' => "Record added successfully!");
			$this->audittrail->logActivity('Shipping Partners', "Shipping partner $name added successfully.", 'add', $this->session->userdata('username'));
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}


	public function get_shopbranch(){
		$this->isLoggedIn();
		$shop_id = sanitize($this->input->post('shop_id'));

		$query = $this->model_settings->get_shopbranch($shop_id);

		if ($query->num_rows() > 0) {
			$data = array("success" => 1, 'result' => $query->result());
		}else{
			$data = array("success" => 0, 'result' => 'no result found');
		}

		generate_json($data);
	}

	public function product_main_category($token = '') {
		$this->isLoggedIn();
		if ($this->loginstate->get_access()['products_main_category']['view'] == 1){
			$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
	        $main_nav_id = $this->views_restriction($content_url);
	        $product_category = $this->model_settings->get_all_sub_category_list()->result();

			$data_admin = array(
				'token' => $token,
				'main_nav_id' => $main_nav_id,
	            'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
	            'product_category' => $product_category,
			);

			$this->load->view('includes/header', $data_admin);
			$this->load->view('settings/settings_product_main_category', $data_admin);
		}else{
    		$this->load->view('error_404');
    	}
	}

	public function product_main_category_list(){
		$this->isLoggedIn();
		$filters = [
			'_record_status' => $this->input->post('_record_status'),
			'_category' => $this->input->post('_category'),
			'_name' => $this->input->post('_name'),
			'_onmenu' => $this->input->post('_onmenu'),
			'_priority' => $this->input->post('_priority'),
		];
        $query = $this->model_settings->product_main_category_list($filters, $_REQUEST);
        generate_json($query);
	}


	public function save_product_main_category(){

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
			$file_name = rand();
			$config = array();
			$directory = 'assets/img';
			$config['file_name'] = $file_name;
			$config['upload_path'] = $directory;
			$config['allowed_types'] = '*';
			$config['max_size'] = 3000;
			//$this->load->library('upload',$config, 'logo');
			
			$this->upload->initialize($config);

			
			$data = array();
			if(!$this->upload->do_upload()){
				
			}else{
				$file_name = $this->upload->data()['file_name'];
				
				///upload image to s3 bucket
				$fileTempName    = $files['file_container']['tmp_name'];
				$activityContent = 'assets/img/main_category/'.$file_name;
				$uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

				if($uploadS3 != 1){
					$data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
					generate_json($data);
					die();
				}

				unlink($directory.'/'.$file_name);
			}
		}

        $add_code = sanitize($this->input->post('add_code'));
        $add_name = sanitize($this->input->post('add_name'));
        $add_icon = sanitize($this->input->post('add_icon'));
        $add_onmenu = sanitize($this->input->post('add_onmenu'));
        $add_priority = sanitize($this->input->post('add_priority'));

        if(!empty($this->input->post('entry-subcategory'))){
            $subcategory = implode(",", $this->input->post('entry-subcategory'));
        }else{
            $subcategory = "";
        }
       
        if(!empty($add_code) AND !empty($add_name) AND !empty($add_icon) AND !empty($add_onmenu) AND !empty($add_priority) AND !empty($subcategory)){

            if(!$this->model_settings->name_category_is_exist($add_name)){
                
                $this->model_settings->save_product_main_category($add_code, $add_name, $add_icon, $add_onmenu, $add_priority, $subcategory, $file_name);

                $data = array(
                    "success" => 1,
                    "message" => 'Product Main Category Saved!'
                );
                $this->audittrail->logActivity('Product Main Category', $add_name.' category has been successfully added.', 'add', $this->session->userdata('username'));
                    
            }else{
                $data = array(
                    "success" => 0,
                    "message" => 'Product Main Category name already exist, Please try again.'
                );
            }
            
        }else{
            $data = array(
                        "success" => 0,
                        "message" => 'Please Complete All Required Fields!'
                    );
        }
        generate_json($data);
    }



    public function get_product_main_category_data(){
        $this->isLoggedIn();
        
		$edit_id = sanitize($this->input->post('edit_id'));
		
		$query = $this->model_settings->get_product_main_category_data($edit_id)->row();
	
		if ($query) {
			$data = array("success" => 1, 'message' => "Record deleted successfully!", 'result' => $query);
		}else{
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}
        
        generate_json($data);
	}

	public function update_product_main_category(){

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
				$activityContent = 'assets/img/main_category/'.$file_name; 
				$uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);
				
				if($uploadS3 != 1){
					$data = array("success" => 0, 'message' => "S3 Bucket upload failed.");
					generate_json($data);
					die();
				}

				unlink($directory.'/'.$file_name);
			}
		}

    	$edit_id = sanitize($this->input->post('edit_id'));
        $edit_code = sanitize($this->input->post('edit_code'));
        $edit_name = sanitize($this->input->post('edit_name'));
        $edit_icon = sanitize($this->input->post('edit_icon'));
        $edit_onmenu = sanitize($this->input->post('edit_onmenu'));
        $edit_priority = sanitize($this->input->post('edit_priority'));

        if(!empty($this->input->post('edit-entry-subcategory'))){
            $subcategory = implode(",", $this->input->post('edit-entry-subcategory'));
        }else{
            $subcategory = "";
        }
       
        if(!empty($edit_code) AND !empty($edit_name) AND !empty($edit_icon) AND !empty($edit_onmenu) AND !empty($edit_priority) AND !empty($subcategory)){

            if(!$this->model_settings->name_category_is_exist_update($edit_name,$edit_id)){
                
                $this->model_settings->update_product_main_category($edit_code, $edit_name, $edit_icon, $edit_onmenu, $edit_priority, $subcategory, $edit_id, $file_name);

                $data = array(
                    "success" => 1,
                    "message" => 'Product Main Category Updated!'
                );
                $this->audittrail->logActivity('Product Main Category', $edit_name.' category has been successfully updated.', 'update', $this->session->userdata('username'));
                    
            }else{
                $data = array(
                    "success" => 0,
                    "message" => 'Product Main Category name already exist, Please try again.'
                );
            }
            
        }else{
            $data = array(
                        "success" => 0,
                        "message" => 'Please Complete All Required Fields!'
                    );
        }
        generate_json($data);
    }

    public function product_main_category_delete_modal_confirm(){
        $this->isLoggedIn();
        
		$delete_id = sanitize($this->input->post('delete_id'));
		
		//get category data using delete_id
		$category_data = $this->model_settings->get_product_main_category_data($delete_id)->row();		
		$remarks = $category_data->parent_category_code." has been successfully deleted";

        if ($delete_id > 0) {
            $query = $this->model_settings->product_main_category_delete_modal_confirm($delete_id);

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('Product Main Categories', $remarks, 'deleted', $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}

	public function product_main_category_disable_modal_confirm(){
        $this->isLoggedIn();
        
        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        }else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        }else{
            $record_status = 0;
		}
		
		//get category data using disable_id
		$category_data = $this->model_settings->get_product_main_category_data($disable_id)->row();		
		$remarks = $category_data->parent_category_code." has been successfully ".$record_text;

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->model_settings->product_main_category_disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
				$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('Product Main Categories', $remarks, $record_text, $this->session->userdata('username'));
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
	}

	public function export_product_main_category_list(){
		$this->isLoggedIn();
        $requestData = url_decode(json_decode($this->input->post('_search')));
        $filters = (array) json_decode($this->input->post('_filter'));
        // print_r($filters);
        // exit();
		$query = $this->model_settings->product_main_category_list($filters, $requestData, true);
		// print_r($query);
        // exit();
		$fromdate = date('Y-m-d');
		$shopid = $filters['_shop'];
		$branchid = 'main';
        $fil_arr = [
			'Category' => $filters['_category'],
			'Name' => $filters['_name'],
			'Priority' => $filters['_priority'],
            'Record Status' => array(
				'' => 'All Records', 1 => 'Enabled', 2 => 'Disabled'
			)[$filters['_record_status']],
            'On Menu' => array(
				'' => 'All Menu Type', 1 => 'Displayed', 2 => 'Not Displayed'
			)[$filters['_onmenu']],
        ];
        extract($this->audittrail->get_ReportExportRemarks($shopid, $branchid, $fromdate, $fromdate, "Product Category", $fil_arr));
        // print_r($remarks);
        // exit();
        $this->audittrail->logActivity('Product Main Category', $remarks, 'export', $this->session->userdata('username'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Product Category");
        $sheet->setCellValue('B2', "Shop: $shop_name");
        $sheet->setCellValue('B3', "Filters: $_filters");
        $sheet->setCellValue('B4', "Date: $fromdate");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);

        $sheet->setCellValue("A6", 'Category Code');
        $sheet->setCellValue('B6', 'Category Name');
        $sheet->setCellValue('C6', 'On Menu');
        $sheet->setCellValue('D6', 'Priority');
        $sheet->setCellValue('E6', 'Last Updated');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:E6')->getFont()->setBold(true);
        $exceldata= array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[1],
                '2' => $row[2],
                '3' => $row[3],
                '4' => $row[4],
                '5' => $row[5],
            );
            
            $exceldata[] = $resultArray;
        }
        $sheet->fromArray($exceldata, null, 'A7');
        // $last_row = count($result['data'])+12;
        $writer = new Xlsx($spreadsheet);
        $filename = 'Product Main Category ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();

        return $writer->save('php://output');
        exit();
	}


}
