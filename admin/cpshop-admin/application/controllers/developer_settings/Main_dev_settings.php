<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main_dev_settings extends CI_Controller {

    public function __construct(){
        parent::__construct();
        //load model or libraries below here...
    }

    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
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


    public function delete_modal_confirm(){
        $this->isLoggedIn();
        
        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id > 0) {
            $query = $this->model_dev_settings->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record deleted successfully!");
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
            
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function disable_modal_confirm(){
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
            $query = $this->model_dev_settings->disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function content_navigation($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['content_navigation']['view'] == 1) {
            // start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/content_navigation', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function content_navigation_table(){
        $this->isLoggedIn();
        $query = $this->model_dev_settings->content_navigation_table();
        generate_json($query);
    }


    public function get_content_navigation(){
        $this->isLoggedIn();

        $id = sanitize($this->input->post('id'));

        $query = $this->model_dev_settings->get_content_navigation($id)->row();
        echo json_encode($query);
    }

    public function update_modal_confirm(){
        $this->isLoggedIn();
        
        $id = sanitize($this->input->post('id'));
        $url = sanitize($this->input->post('url'));
        $name = sanitize($this->input->post('name'));
        $description = sanitize($this->input->post('description'));
        $category = sanitize($this->input->post('category'));

        if ($id != '' || $url != '' || $name != '' || $description != '' || $category != '') {
            $query = $this->model_dev_settings->update_modal_confirm($id, $url, $name, $description, $category);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Navigation updated successfully!");
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function add_modal_confirm(){
        $this->isLoggedIn();
        
        $url = sanitize($this->input->post('url'));
        $name = sanitize($this->input->post('name'));
        $description = sanitize($this->input->post('description'));
        $category = sanitize($this->input->post('category'));

        if ($url != '' || $name != '' || $description != '' || $category != '') {
            $query = $this->model_dev_settings->add_modal_confirm($url, $name, $description, $category);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Navigation added successfully!");
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }

    public function cron_logs($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['cron_logs']['view'] == 1) {
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                // 'get_position' => $this->model->get_position($this->session->userdata('position_id'))->row(),
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/cron_logs', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

    public function cron_logs_table(){
        $this->isLoggedIn();
        $query = $this->model_dev_settings->cron_logs_table();
        generate_json($query);
    }

    public function disable_modal_confirm_cron_logs(){
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
            $query = $this->model_dev_settings->disable_modal_confirm_cron_logs($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
            }else{
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        }else{
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }
        
        generate_json($data);
    }
}
