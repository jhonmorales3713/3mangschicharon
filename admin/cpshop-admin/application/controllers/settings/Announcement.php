<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Announcement extends CI_Controller {
	public function __construct(){
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('setting/model_announcement', 'model_announcement');
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
		if ($this->loginstate->get_access()['announcement']['view'] == 1){
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
	        $this->load->view('settings/settings_announcement', $data_admin);
	        // end - load all the views synchronously
    	}else{
    		$this->load->view('error_404');
    	}
    }
    
    public function announcement_table() 
	{
		$this->isLoggedIn();
        $query = $this->model_announcement->announcement_table();
        generate_json($query);
    }
    
    public function get_data()
	{
		$this->isLoggedIn();
		$id = sanitize($this->input->post('id'));
		$result = $this->model_announcement->get_data($id);
		generate_json($result);
    }
    
    public function update_data()
	{
		$this->isLoggedIn();
		if ($this->model_announcement->update_data()) {
			$data = array("success" => 1, 'message' => "Announcement updated successfully!");
		}
		else {
			$data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
		}

		generate_json($data);
	}
    

}