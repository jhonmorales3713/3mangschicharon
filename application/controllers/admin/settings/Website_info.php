<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class website_info extends CI_Controller {
	public function __construct(){
		parent::__construct();
        //load model or libraries below here...
		$this->load->model('settings/model_user_list', 'model_user_list');
		$this->load->model('settings/model_access_control', 'model_access_control');
		$this->load->model('model');
        $this->load->model('model_dev_settings');
		$this->load->library('form_validation');
	}
    
    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn')=='') {
            header("location:" . base_url('Main/logout'));
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
            //header("location:".base_url('Main/logout'));
        }else{
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }
    public function get_faq(){
        $data = $this->input->post();
        $result = $this->model_dev_settings->get_faq($data['id']);
        echo json_encode($result[0]);
    }
    public function get_aboutus(){
        $data = $this->input->post();
        $result = $this->model_dev_settings->get_aboutus($data['id']);
        echo json_encode($result[0]);
    }
    public function view($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['web']['view'] == 1) {
    		//start - for restriction of views
            $this->isLoggedIn();

            $data = array(
                'active_page' => $this->session->userdata('active_page'),
                'subnav' => true, //for highlight the navigation,
                'token' => $this->session->userdata('token_session')
            );
            $this->load->view('admin/settings/web_info',$data,'',TRUE);
            // end - load all the views synchronously

            
        }else{
            $this->load->view('error_404');
        }
    }
    public function enable_disable_faq(){
        $data = $this->input->post();
        $this->model_dev_settings->enable_disable_faq($data);
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => 1,
            'message'     => 'FAQ Saved Successfully'
        ];
        echo json_encode($response);
    }
    public function enable_disable_aboutus(){
        $data = $this->input->post();
        $this->model_dev_settings->enable_disable_aboutus($data);
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => 1,
            'message'     => 'Info Saved Successfully'
        ];
        echo json_encode($response);
    }
    public function delete_faq(){
        $data = $this->input->post();
        $this->model_dev_settings->delete_faq($data);
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => 1,
            'message'     => 'FAQ Deleted Successfully'
        ];
        echo json_encode($response);
    }
    public function delete_aboutus(){
        $data = $this->input->post();
        $this->model_dev_settings->delete_aboutus($data);
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => 1,
            'message'     => 'Info Deleted Successfully'
        ];
        echo json_encode($response);
    }
    public function save_faq(){
        $data = $this->input->post();
		$validation = array(
            array('f_title','Title','required|max_length[100]|min_length[1]'),
            // array('f_content','Content','required|max_length[100]|min_length[1]')
        );
        $this->form_validation->set_data($data); 
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
        if($this->form_validation->run() == FALSE){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => 0,
                'message'     => explode("\n",validation_errors())
            ];

            echo json_encode($response);
            die();
        }else{
            $this->model_dev_settings->save_faq($data);
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => 1,
                'message'     => 'FAQ Saved Successfully'
            ];
            echo json_encode($response);
        }
    }
    
    public function save_aboutus(){
        $data = $this->input->post();
		$validation = array(
            array('f_title','Title','required|max_length[100]|min_length[1]'),
            // array('f_content','Content','required|max_length[100]|min_length[1]')
        );
        $this->form_validation->set_data($data); 
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
        if($this->form_validation->run() == FALSE){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => 0,
                'message'     => explode("\n",validation_errors())
            ];

            echo json_encode($response);
            die();
        }else{
            $this->model_dev_settings->save_aboutus($data);
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => 1,
                'message'     => 'Info Saved Successfully'
            ];
            echo json_encode($response);
        }
    }
    public function update(){
        $data = $this->input->post();
        
		$validation = array(
            array('f_name','Website Name','required|max_length[100]|min_length[1]'),
            array('f_shortname','Short Name','required|max_length[100]|min_length[1]'),
            array('f_tagline','Tagline','required|max_length[255]|min_length[2]'),
            // array('f_otherinfo','Other Info','required|max_length[100]|min_length[1]'),
            array('f_support','Support E-mail','required|max_length[255]|valid_email'),
            // array('main_logo','Main Logo','required'),
            // array('main_icon','Main Icon','required'),
            // array('background_image','Background Image','required'),
            // array('placeholder_image','Placeholder  Image','required'),
        );
        validate_link($data['f_facebook'],'Facebook');
        validate_link($data['f_instagram'],'Instagram');
        validate_link($data['f_youtube'],'Youtube');
        $this->form_validation->set_data($data); 
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
        if($this->form_validation->run() == FALSE){
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => 0,
                'message'     => explode("\n",validation_errors())
            ];

            echo json_encode($response);
            die();
        }else{
            if($data['main_icon_checker'] != 'false'){
                if(file_exists('assets/img/'.get_icon())){
                    unlink('assets/img/'.get_icon());
                }
                copy($_FILES['main_icon']['tmp_name'],'assets/img/'.get_icon());
            }
            if($data['main_logo_checker'] != 'false'){
                if(file_exists('assets/img/'.get_logo())){
                    unlink('assets/img/'.get_logo());
                }
                copy($_FILES['main_logo']['tmp_name'],'assets/img/'.get_logo());
            }
            if($data['placeholder_image_checker'] != 'false'){
                if(file_exists('assets/img/'.get_placeholder())){
                    unlink('assets/img/'.get_placeholder());
                }
                copy($_FILES['placeholder_image']['tmp_name'],'assets/img/'.get_placeholder());
            }
            if($data['background_image_checker'] != 'false'){
                if(file_exists('assets/img/'.get_bg())){
                    unlink('assets/img/'.get_bg());
                }
                copy($_FILES['background_image']['tmp_name'],'assets/img/'.get_bg());
            }
            $this->model->update_core($data);
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => 1,
                'message'     => 'Website Info Updated Successfully'
            ];
            echo json_encode($response);

        }
    }
    public function save_arrangement(){
        $data = $this->input->post()['arrangement'];
        foreach($data as $arr){
            $this->model_dev_settings->save_arrangement($arr['value'],1+$arr['order']);
        }
        $response = [
            'success'     => 1,
            'message'     => 'Faqs Arrangement Saved Successfully'
        ];
        echo json_encode($response);
    }

    public function save_arrangement_aboutus(){
        $data = $this->input->post()['arrangement'];
        foreach($data as $arr){
            $this->model_dev_settings->save_arrangement_aboutus($arr['value'],1+$arr['order']);
        }
        $response = [
            'success'     => 1,
            'message'     => 'About Us Arrangement Saved Successfully'
        ];
        echo json_encode($response);
    }

    public function faqs_view($token = ''){
        //start - for restriction of views
        $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/';
        $main_nav_id = $this->views_restriction($content_url);
        //end - for restriction of views main_nav_id

        // start - data to be used for views
        $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation
        );
        // end - data to be used for views

        // start - load all the views synchronously
        $data_admin['active_page'] =  $this->session->userdata('active_page');
        $data_admin['subnav'] = false;
        $data_admin['page_content'] = $this->load->view('admin/settings/faqs',$data_admin,TRUE);
        $this->load->view('admin_template',$data_admin,'',TRUE);
    }
    public function about_us($token = ''){
        //start - for restriction of views
        $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/';
        $main_nav_id = $this->views_restriction($content_url);
        //end - for restriction of views main_nav_id

        // start - data to be used for views
        $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation
        );
        // end - data to be used for views

        // start - load all the views synchronously
        $data_admin['active_page'] =  $this->session->userdata('active_page');
        $data_admin['subnav'] = false;
        $data_admin['page_content'] = $this->load->view('admin/settings/about_us',$data_admin,TRUE);
        $this->load->view('admin_template',$data_admin,'',TRUE);
    }
    
    public function load_faqs(){
    
        $this->isLoggedIn();
        $request = $_REQUEST;
        $query = $this->model_dev_settings->load_faqs($request);
        generate_json($query);
    }    
    public function load_aboutus(){
    
        $this->isLoggedIn();
        $request = $_REQUEST;
        $query = $this->model_dev_settings->load_aboutus($request);
        generate_json($query);
    }

    public function web_info_details($token = ''){
        
        $this->isLoggedIn();
        //start - for restriction of views
        $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/';
        $main_nav_id = $this->views_restriction($content_url);
        //end - for restriction of views main_nav_id

        // start - data to be used for views
        $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation
        );
        // end - data to be used for views

        // start - load all the views synchronously
        $data_admin['active_page'] =  $this->session->userdata('active_page');
        $data_admin['subnav'] = false;
        $data_admin['page_content'] = $this->load->view('admin/settings/web_info_details',$data_admin,TRUE);
        $this->load->view('admin_template',$data_admin,'',TRUE);
    }

}