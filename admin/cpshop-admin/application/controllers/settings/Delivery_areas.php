<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Delivery_areas extends CI_Controller {
	public function __construct(){
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('setting/model_delivery_areas', 'model_delivery_areas');
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
		if ($this->loginstate->get_access()['delivery_areas']['view'] == 1){
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
	        $this->load->view('settings/settings_delivery_areas', $data_admin);
	        // end - load all the views synchronously
    	}else{
    		$this->load->view('error_404');
    	}
	}

	public function delivery_areas_table() 
	{
		$this->isLoggedIn();
        $query = $this->model_delivery_areas->delivery_areas_table();
        generate_json($query);
	}

	public function create_data()
	{
		$this->isLoggedIn();
		if ($this->model_delivery_areas->create_data()) {
			$data = array("success" => 1, 'message' => "Added successfully!");
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
		$result = $this->model_delivery_areas->get_data($id);
		generate_json($result);
	}

	public function update_data()
	{
		$this->isLoggedIn();
		if ($this->model_delivery_areas->update_data()) {
			$data = array("success" => 1, 'message' => "Record updated successfully!");
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
        	if ($this->model_delivery_areas->disable_data($disable_id, $record_status)) {
        		$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
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
		if ($delete_id > 0) {
			if ($this->model_delivery_areas->delete_data($delete_id)) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
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