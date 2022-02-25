<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 300);
set_time_limit(300);

class Dev_settings_client_information extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('model_dev_settings');
        $this->load->model('dev_settings/Model_client_information', 'model_client_information');
        $this->load->library('s3_upload');
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
  
    public function client_information_table(){
        $this->isLoggedIn();
        $query = $this->model_client_information->client_information_table();
        generate_json($query);
    }
    
    public function client_information($token = ''){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['client_information']['view'] == 1) {
            //start - for restriction of views
            $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
                'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array(),
                'clients' => $this->model_client_information->get_clients()->result(),
            );
            // end - data to be used for views
            
            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/client_information', $data_admin);
            // end - load all the views synchronously
        }else{
             $this->load->view('error_404');
        }
    }

    public function add_client_information($token = '')
    {
        $this->isLoggedIn();        
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['client_information']['create'] == 1) {            
            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result_array()
            );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/add_client_information', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function disable_modal_confirm()
    {
        $this->isLoggedIn();

        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));        

        if ($disable_id === 0) {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        } else {
            $client_data = $this->model_client_information->get_client_info($disable_id)->row();		    
            $query = $this->model_client_information->disable_modal_confirm($record_status,$disable_id);

           
            if ($query == 1) {
                $message="";
                if($record_status == 2){
                    $message="Client disabled successfully!";
                    $record_text = "disabled";
                }
                else{
                    $message="Client enabled successfully!";
                    $record_text = "enabled";
                }
                $data = array("success" => 1, 'message' => $message);
                $remarks = "Client ".$client_data->c_name." has been successfully ".$record_text;                
                $this->model_client_information->log_audit_trail($remarks, $record_text);          
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }

        }

        generate_json($data);
    }

    public function delete_modal_confirm()
    {
        $this->isLoggedIn();

        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id === 0) {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        } else {
            $client_data = $this->model_client_information->get_client_info($delete_id)->row_array();
            $query = $this->model_client_information->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Client deleted successfully!");
                $this->audittrail->logActivity('Client Information', 'Client '.$client_data['c_name'].' has been successfully deleted.', "delete", $this->session->userdata('username'));  
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }

        }

        generate_json($data);
    }

    public function edit_client_information($token = '', $c_id)
    {        
        //$this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['client_information']['update'] == 1) {
            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'c_id'                  => $c_id
            );
        
            $this->load->view('includes/header', $data_admin);
            $this->load->view('dev_settings/update_client_information', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function get_client_info($c_id){
        $row = $this->model_client_information->get_client_info($c_id)->row();
        $response = [
            'success' => true,
            'message' => $row
        ];
        echo json_encode($response);
    }   

    public function makedirImage(){
        if (!is_dir('./assets/')) {
            mkdir('./assets/', 0777, TRUE);
		}
		if (!is_dir('./assets/img/')) {
            mkdir('./assets/img/', 0777, TRUE);
		}
    }

    public function create(){

        $this->isLoggedIn();        
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['client_information']['create'] == 1) {
            
            //$directory                = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/');
            $directory                = "./assets/img/";
            $main_logo_filename       = "";
            $secondary_logo_filename  = "";
            $placeholder_img_filename = "";
            $fb_image_filename        = "";
            $favicon_filename         = "";

            //shop url
            $shop_directory           = get_shop_url("assets/img/");

            $this->makedirImage();
            
            if($this->input->post('main_logo_checker') === 'true'){
                $this->load->library('upload');

                $_FILES['userfile']['name']     = $_FILES['main_logo']['name'];
                $_FILES['userfile']['type']     = $_FILES['main_logo']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['main_logo']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['main_logo']['error'];
                $_FILES['userfile']['size']     = $_FILES['main_logo']['size'];
                
                $id_key = $this->input->post('f_id_key');
                $main_logo_filename = $id_key.'-main_logo';
                

                $config = array(
                    'file_name'     => $main_logo_filename,
                    'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                    'max_size'      => 3000,
                    'overwrite'     => FALSE,
                    'min_width'     => '20',
                    'min_height'    => '20',
                    'upload_path'   =>  $directory
                );

                $this->upload->initialize($config);
                if ( ! $this->upload->do_upload()) {
                    $error = array('error' => $this->upload->display_errors());
                    $response = array(
                        'status'      => false,
                        'environment' => ENVIRONMENT,
                        'message'     => $error['error'],
                        'csrf_name'   => $this->security->get_csrf_token_name(),
                        'csrf_hash'   => $this->security->get_csrf_hash(),                        
                    );
                    echo json_encode($response);
                    die();
                }else{
                    $main_logo_filename = $this->upload->data()['file_name'];

                    ///upload image to s3 bucket
                    $fileTempName    = $_FILES['userfile']['tmp_name'];
                    $activityContent = 'assets/img/'.$main_logo_filename;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory.'/'.$main_logo_filename);
                }

                //upload file in shop url                
                // $config = array(
                //     'file_name'     => $main_logo_filename,
                //     'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                //     'max_size'      => 3000,
                //     'overwrite'     => FALSE,
                //     'min_width'     => '20',
                //     'min_height'     => '20',
                //     'upload_path'    => $shop_directory
                // );

                // $this->upload->initialize($config);
                // if ( ! $this->upload->do_upload()) {
                //     $error = array('error' => $this->upload->display_errors());
                //     $response = array(
                //         'status'      => false,
                //         'environment' => ENVIRONMENT,
                //         'message'     => $error['error'],
                //         'csrf_name'   => $this->security->get_csrf_token_name(),
                //         'csrf_hash'   => $this->security->get_csrf_hash(),
                //         //'directory'   => $shop_directory
                //     );
                //     echo json_encode($response);
                //     die();
                // }else{
                    //$main_logo_filename = $this->upload->data()['file_name'];
                // }

            }

            if($this->input->post('secondary_logo_checker') === 'true'){
                $this->load->library('upload');

                $_FILES['userfile']['name']     = $_FILES['secondary_logo']['name'];
                $_FILES['userfile']['type']     = $_FILES['secondary_logo']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['secondary_logo']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['secondary_logo']['error'];
                $_FILES['userfile']['size']     = $_FILES['secondary_logo']['size'];
                
                $id_key = $this->input->post('f_id_key');
                $secondary_logo_filename = $id_key.'-secondary_logo';
                

                $config = array(
                    'file_name'     => $secondary_logo_filename,
                    'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                    'max_size'      => 3000,
                    'overwrite'     => FALSE,
                    'min_width'     => '20',
                    'min_height'     => '20',
                    'upload_path'
                    =>  $directory
                );

                $this->upload->initialize($config);
                if ( ! $this->upload->do_upload()) {
                    $error = array('error' => $this->upload->display_errors());
                    $response = array(
                        'status'      => false,
                        'environment' => ENVIRONMENT,
                        'message'     => $error['error'],
                        'csrf_name'   => $this->security->get_csrf_token_name(),
                        'csrf_hash'   => $this->security->get_csrf_hash()
                    );
                    echo json_encode($response);
                    die();
                }else{
                    $secondary_logo_filename = $this->upload->data()['file_name'];

                    ///upload image to s3 bucket
                    $fileTempName    = $_FILES['userfile']['tmp_name'];
                    $activityContent = 'assets/img/'.$secondary_logo_filename;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory.'/'.$secondary_logo_filename);
                }

                //upload file in shop url                
                // $config = array(
                //     'file_name'     => $secondary_logo_filename,
                //     'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                //     'max_size'      => 3000,
                //     'overwrite'     => FALSE,
                //     'min_width'     => '20',
                //     'min_height'     => '20',
                //     'upload_path'    => $shop_directory
                // );

                // $this->upload->initialize($config);
                // if ( ! $this->upload->do_upload()) {
                //     $error = array('error' => $this->upload->display_errors());
                //     $response = array(
                //         'status'      => false,
                //         'environment' => ENVIRONMENT,
                //         'message'     => $error['error'],
                //         'csrf_name'   => $this->security->get_csrf_token_name(),
                //         'csrf_hash'   => $this->security->get_csrf_hash()
                //     );
                //     echo json_encode($response);
                //     die();
                // }else{
                    //$secondary_logo_filename = $this->upload->data()['file_name'];
                // }
            }

            if($this->input->post('placeholder_img_checker') === 'true'){

                $this->load->library('upload');

                $_FILES['userfile']['name']     = $_FILES['placeholder_img']['name'];
                $_FILES['userfile']['type']     = $_FILES['placeholder_img']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['placeholder_img']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['placeholder_img']['error'];
                $_FILES['userfile']['size']     = $_FILES['placeholder_img']['size'];
                
                $id_key = $this->input->post('f_id_key');
                $placeholder_img_filename = $id_key.'-placeholder_img';
                

                $config = array(
                    'file_name'     => $placeholder_img_filename,
                    'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                    'max_size'      => 3000,
                    'overwrite'     => FALSE,
                    'min_width'     => '20',
                    'min_height'     => '20',
                    'upload_path'
                    =>  $directory
                );

                $this->upload->initialize($config);
                if ( ! $this->upload->do_upload()) {
                    $error = array('error' => $this->upload->display_errors());
                    $response = array(
                        'status'      => false,
                        'environment' => ENVIRONMENT,
                        'message'     => $error['error'],
                        'csrf_name'   => $this->security->get_csrf_token_name(),
                        'csrf_hash'   => $this->security->get_csrf_hash()
                    );
                    echo json_encode($response);
                    die();
                }else{
                    $placeholder_img_filename = $this->upload->data()['file_name'];

                    ///upload image to s3 bucket
                    $fileTempName    = $_FILES['userfile']['tmp_name'];
                    $activityContent = 'assets/img/'.$placeholder_img_filename;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory.'/'.$placeholder_img_filename);
                }

                //upload file in shop url                
                // $config = array(
                //     'file_name'     => $placeholder_img_filename,
                //     'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                //     'max_size'      => 3000,
                //     'overwrite'     => FALSE,
                //     'min_width'     => '20',
                //     'min_height'     => '20',
                //     'upload_path'    => $shop_directory
                // );

                // $this->upload->initialize($config);
                // if ( ! $this->upload->do_upload()) {
                //     $error = array('error' => $this->upload->display_errors());
                //     $response = array(
                //         'status'      => false,
                //         'environment' => ENVIRONMENT,
                //         'message'     => $error['error'],
                //         'csrf_name'   => $this->security->get_csrf_token_name(),
                //         'csrf_hash'   => $this->security->get_csrf_hash()
                //     );
                //     echo json_encode($response);
                //     die();
                // }else{
                    //$secondary_logo_filename = $this->upload->data()['file_name'];
                // }
            }

            if($this->input->post('fb_image_checker') === 'true'){
                $this->load->library('upload');

                $_FILES['userfile']['name']     = $_FILES['fb_image']['name'];
                $_FILES['userfile']['type']     = $_FILES['fb_image']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['fb_image']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['fb_image']['error'];
                $_FILES['userfile']['size']     = $_FILES['fb_image']['size'];
                
                $id_key = $this->input->post('f_id_key');
                $fb_image_filename = $id_key.'-fb_image';
                

                $config = array(
                    'file_name'     => $fb_image_filename,
                    'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                    'max_size'      => 3000,
                    'overwrite'     => FALSE,
                    'min_width'     => '20',
                    'min_height'     => '20',
                    'upload_path'
                    =>  $directory
                );

                $this->upload->initialize($config);
                if ( ! $this->upload->do_upload()) {
                    $error = array('error' => $this->upload->display_errors());
                    $response = array(
                        'status'      => false,
                        'environment' => ENVIRONMENT,
                        'message'     => $error['error'],
                        'csrf_name'   => $this->security->get_csrf_token_name(),
                        'csrf_hash'   => $this->security->get_csrf_hash()
                    );
                    echo json_encode($response);
                    die();
                }else{
                    $fb_image_filename = $this->upload->data()['file_name'];

                    ///upload image to s3 bucket
                    $fileTempName    = $_FILES['userfile']['tmp_name'];
                    $activityContent = 'assets/img/'.$fb_image_filename;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory.'/'.$fb_image_filename);
                }

                //upload file in shop url                
                // $config = array(
                //     'file_name'     => $fb_image_filename,
                //     'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                //     'max_size'      => 3000,
                //     'overwrite'     => FALSE,
                //     'min_width'     => '20',
                //     'min_height'     => '20',
                //     'upload_path'    => $shop_directory
                // );

                // $this->upload->initialize($config);
                // if ( ! $this->upload->do_upload()) {
                //     $error = array('error' => $this->upload->display_errors());
                //     $response = array(
                //         'status'      => false,
                //         'environment' => ENVIRONMENT,
                //         'message'     => $error['error'],
                //         'csrf_name'   => $this->security->get_csrf_token_name(),
                //         'csrf_hash'   => $this->security->get_csrf_hash()
                //     );
                //     echo json_encode($response);
                //     die();
                // }else{
                    //$secondary_logo_filename = $this->upload->data()['file_name'];
                // }
            }

            if($this->input->post('favicon_checker') === 'true'){
                $this->load->library('upload');

                $_FILES['userfile']['name']     = $_FILES['favicon']['name'];
                $_FILES['userfile']['type']     = $_FILES['favicon']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['favicon']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['favicon']['error'];
                $_FILES['userfile']['size']     = $_FILES['favicon']['size'];
                
                $id_key = $this->input->post('f_id_key');
                $favicon_filename = $id_key.'-favicon';
                

                $config = array(
                    'file_name'     => $favicon_filename,
                    'allowed_types' => 'ico',
                    'max_size'      => 3000,
                    'overwrite'     => FALSE,
                    'min_width'     => '20',
                    'min_height'     => '20',
                    'upload_path'
                    =>  $directory
                );

                $this->upload->initialize($config);
                if ( ! $this->upload->do_upload()) {
                    $error = array('error' => $this->upload->display_errors());
                    $response = array(
                        'status'      => false,
                        'environment' => ENVIRONMENT,
                        'message'     => $error['error'],
                        'csrf_name'   => $this->security->get_csrf_token_name(),
                        'csrf_hash'   => $this->security->get_csrf_hash()
                    );
                    echo json_encode($response);
                    die();
                }else{
                    $favicon_filename = $this->upload->data()['file_name'];

                    ///upload image to s3 bucket
                    $fileTempName    = $_FILES['userfile']['tmp_name'];
                    $activityContent = 'assets/img/'.$favicon_filename;
                    $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                    if($uploadS3 != 1){
                        $response = [
                            'environment' => ENVIRONMENT,
                            'success'     => false,
                            'message'     => 'S3 Bucket upload failed.'
                        ];
            
                        echo json_encode($response);
                        die();
                    }

                    unlink($directory.'/'.$favicon_filename);
                }

                //upload file in shop url                
                // $config = array(
                //     'file_name'     => $favicon_filename,
                //     'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                //     'max_size'      => 3000,
                //     'overwrite'     => FALSE,
                //     'min_width'     => '20',
                //     'min_height'     => '20',
                //     'upload_path'    => $shop_directory
                // );

                // $this->upload->initialize($config);
                // if ( ! $this->upload->do_upload()) {
                //     $error = array('error' => $this->upload->display_errors());
                //     $response = array(
                //         'status'      => false,
                //         'environment' => ENVIRONMENT,
                //         'message'     => $error['error'],
                //         'csrf_name'   => $this->security->get_csrf_token_name(),
                //         'csrf_hash'   => $this->security->get_csrf_hash()
                //     );
                //     echo json_encode($response);
                //     die();
                // }else{
                    //$secondary_logo_filename = $this->upload->data()['file_name'];
                // }
            }

            $this->db = $this->load->database('core', TRUE);

            $validation = array(
                array('f_c_name','Client Name','required|max_length[100]|min_length[1]|is_unique[cs_clients_info.c_name]'),
                array('f_c_initial','Client Initial','required|max_length[10]|min_length[1]'),
                array('f_id_key','Client ID Key','required|max_length[10]|min_length[1]|is_unique[cs_clients_info.id_key]'),
                array('f_email','Client Email','required|max_length[255]|min_length[1]'),
                array('f_phone','Client Phone','required|max_length[100]|min_length[1]')
            );
            
            foreach ($validation as $value) {
                $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
            }
            
            $response = [
                'environment' => ENVIRONMENT,
                'success'     => false,
                'message'     => $this->response->message('error'),
                'csrf_name'   => $this->security->get_csrf_token_name(),
                'csrf_hash'   => $this->security->get_csrf_hash(),
            ];

            $check_idkey_exists = $this->model_client_information->get_idkey($this->input->post('f_id_key'))->num_rows();

            if($check_idkey_exists > 0){
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => 'ID Key already exists.',
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash(),
                ];
                echo json_encode($response);
                die();
            }
            else if ($this->form_validation->run() == FALSE) {
                $response['message'] = validation_errors();
                echo json_encode($response);
                die();
            }else{                
                $success = $this->model_client_information->create($this->input->post(), $main_logo_filename, $secondary_logo_filename, $placeholder_img_filename, $fb_image_filename, $favicon_filename);                
                $client_name = $this->input->post('f_c_name');
                $remarks = $client_name." has been successfully added Client Information";                
                $this->model_client_information->log_audit_trail($remarks, "add");
                $response['success'] = $success;
                $response['message'] = $this->response->message(($success ? 'success' : 'failed'), 'create', 'Client Information');
            }
            echo json_encode($response);
            
        }else{
            return;
        }
    }

    public function update(){
        //$directory = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/');
        $c_id                     = sanitize($this->input->post('c_id'));
        $directory                = "./assets/img/";
        $main_logo_filename       = "";
        $secondary_logo_filename  = "";
        $placeholder_img_filename = "";
        $fb_image_filename        = "";
        $favicon_filename         = "";

        $this->makedirImage();
        if($this->input->post('main_logo_checker') === 'true'){
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['main_logo']['name'];
            $_FILES['userfile']['type']     = $_FILES['main_logo']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['main_logo']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['main_logo']['error'];
            $_FILES['userfile']['size']     = $_FILES['main_logo']['size'];
            
            $id_key = $this->input->post('f_id_key');
            $main_logo_filename = $id_key.'-main_logo-'.rand(100,10000);
            

            $config = array(
                'file_name'     => $main_logo_filename,
                'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                'max_size'      => 3000,
                'overwrite'     => FALSE,
                'min_width'     => '20',
                'min_height'     => '20',
                'upload_path'
                =>  $directory
            );

            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $response = array(
                    'status'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => $error['error'],
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                );
                echo json_encode($response);
                die();
            }else{
                $main_logo_filename = $this->upload->data()['file_name'];

                ///upload image to s3 bucket
                $fileTempName    = $_FILES['userfile']['tmp_name'];
                $activityContent = 'assets/img/'.$main_logo_filename;
                $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'S3 Bucket upload failed.'
                    ];
        
                    echo json_encode($response);
                    die();
                }

                unlink($directory.'/'.$main_logo_filename);
            }
        }else{
            $main_logo_filename = $this->model_client_information->get_clients_info($c_id)->row()->c_main_logo;
        }

        if($this->input->post('secondary_logo_checker') === 'true'){
            $this->load->library('upload');

            $_FILES['userfile']['name']     = $_FILES['secondary_logo']['name'];
            $_FILES['userfile']['type']     = $_FILES['secondary_logo']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['secondary_logo']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['secondary_logo']['error'];
            $_FILES['userfile']['size']     = $_FILES['secondary_logo']['size'];
            
            $id_key = $this->input->post('f_id_key');
            $secondary_logo_filename = $id_key.'-secondary_logo-'.rand(100,10000);
            

            $config = array(
                'file_name'     => $secondary_logo_filename,
                'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                'max_size'      => 3000,
                'overwrite'     => FALSE,
                'min_width'     => '20',
                'min_height'     => '20',
                'upload_path'
                =>  $directory
            );

            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $response = array(
                    'status'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => $error['error'],
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                );
                echo json_encode($response);
                die();
            }else{
                $secondary_logo_filename = $this->upload->data()['file_name'];

                ///upload image to s3 bucket
                $fileTempName    = $_FILES['userfile']['tmp_name'];
                $activityContent = 'assets/img/'.$secondary_logo_filename;
                $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'S3 Bucket upload failed.'
                    ];
        
                    echo json_encode($response);
                    die();
                }

                unlink($directory.'/'.$secondary_logo_filename);
            }
        }else{
            $secondary_logo_filename = $this->model_client_information->get_clients_info($c_id)->row()->c_secondary_logo;
        }

        if($this->input->post('placeholder_img_checker') === 'true'){
            $this->load->library('upload');

            $_FILES['userfile']['name']     = $_FILES['placeholder_img']['name'];
            $_FILES['userfile']['type']     = $_FILES['placeholder_img']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['placeholder_img']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['placeholder_img']['error'];
            $_FILES['userfile']['size']     = $_FILES['placeholder_img']['size'];
            
            $id_key = $this->input->post('f_id_key');
            $placeholder_img_filename = $id_key.'-placeholder_img-'.rand(100,10000);
            

            $config = array(
                'file_name'     => $placeholder_img_filename,
                'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                'max_size'      => 3000,
                'overwrite'     => FALSE,
                'min_width'     => '20',
                'min_height'     => '20',
                'upload_path'
                =>  $directory
            );

            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $response = array(
                    'status'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => $error['error'],
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                );
                echo json_encode($response);
                die();
            }else{
                $placeholder_img_filename = $this->upload->data()['file_name'];

                ///upload image to s3 bucket
                $fileTempName    = $_FILES['userfile']['tmp_name'];
                $activityContent = 'assets/img/'.$placeholder_img_filename;
                $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'S3 Bucket upload failed.'
                    ];
        
                    echo json_encode($response);
                    die();
                }

                unlink($directory.'/'.$placeholder_img_filename);
            }
        }else{
            $placeholder_img_filename = $this->model_client_information->get_clients_info($c_id)->row()->c_placeholder_img;
        }

        if($this->input->post('fb_image_checker') === 'true'){
            $this->load->library('upload');

            $_FILES['userfile']['name']     = $_FILES['fb_image']['name'];
            $_FILES['userfile']['type']     = $_FILES['fb_image']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['fb_image']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['fb_image']['error'];
            $_FILES['userfile']['size']     = $_FILES['fb_image']['size'];
            
            $id_key = $this->input->post('f_id_key');
            $fb_image_filename = $id_key.'-fb_image-'.rand(100,10000);
            

            $config = array(
                'file_name'     => $fb_image_filename,
                'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
                'max_size'      => 3000,
                'overwrite'     => FALSE,
                'min_width'     => '20',
                'min_height'     => '20',
                'upload_path'
                =>  $directory
            );

            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $response = array(
                    'status'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => $error['error'],
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                );
                echo json_encode($response);
                die();
            }else{
                $fb_image_filename = $this->upload->data()['file_name'];

                ///upload image to s3 bucket
                $fileTempName    = $_FILES['userfile']['tmp_name'];
                $activityContent = 'assets/img/'.$fb_image_filename;
                $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'S3 Bucket upload failed.'
                    ];
        
                    echo json_encode($response);
                    die();
                }

                unlink($directory.'/'.$fb_image_filename);
            }
        }else{
            $fb_image_filename = $this->model_client_information->get_clients_info($c_id)->row()->c_fb_image;
        }

        if($this->input->post('favicon_checker') === 'true'){
            $this->load->library('upload');

            $_FILES['userfile']['name']     = $_FILES['favicon']['name'];
            $_FILES['userfile']['type']     = $_FILES['favicon']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['favicon']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['favicon']['error'];
            $_FILES['userfile']['size']     = $_FILES['favicon']['size'];
            
            $id_key = $this->input->post('f_id_key');
            $favicon_filename = $id_key.'-favicon-'.rand(100,10000);
            

            $config = array(
                'file_name'     => $favicon_filename,
                'allowed_types' => 'ico',
                'max_size'      => 3000,
                'overwrite'     => FALSE,
                'min_width'     => '20',
                'min_height'     => '20',
                'upload_path'
                =>  $directory
            );

            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $response = array(
                    'status'      => false,
                    'environment' => ENVIRONMENT,
                    'message'     => $error['error'],
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                );
                echo json_encode($response);
                die();
            }else{
                $favicon_filename = $this->upload->data()['file_name'];

                ///upload image to s3 bucket
                $fileTempName    = $_FILES['userfile']['tmp_name'];
                $activityContent = 'assets/img/'.$favicon_filename;
                $uploadS3        = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);

                if($uploadS3 != 1){
                    $response = [
                        'environment' => ENVIRONMENT,
                        'success'     => false,
                        'message'     => 'S3 Bucket upload failed.'
                    ];
        
                    echo json_encode($response);
                    die();
                }

                unlink($directory.'/'.$favicon_filename);
            }
        }else{
            $favicon_filename = $this->model_client_information->get_clients_info($c_id)->row()->c_favicon;
        }

        $this->db = $this->load->database('core', TRUE);        
        
        $is_unique = '';
        if($this->model_client_information->read($c_id)['id_key'] != $this->input->post('f_id_key')){
            $is_unique = '|is_unique[cs_clients_info.id_key]';
        }
        
        $validation = array(
            array('f_name','Client Name','required|max_length[100]|min_length[1]'),
            array('f_initial','Client Initial','required|max_length[10]|min_length[1]'),
            array('f_id_key','Client ID Key','required|max_length[20]|min_length[1]'.$is_unique),
            array('f_email','Client Email','required|max_length[255]|min_length[1]'),
            array('f_phone','Client Phone','required|max_length[100]|min_length[1]')
        );
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }
        
        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => $this->response->message('error'),
            'csrf_name'   => $this->security->get_csrf_token_name(),
            'csrf_hash'   => $this->security->get_csrf_hash(),
        ];

        if($c_id == ''){
            echo json_encode($response);
            die();
        }

        $client_info = $this->model_client_information->get_client_info($c_id)->row();
        $prev_val = [];
        foreach($client_info as $key => $value){
            if($value != 'false' && $value != 'true'){
                $prev_val[$key] = $value;
            }            
        }

        $cur_val = [];

        foreach($this->input->post() as $key => $value){
            if (strpos($key, 'checker') === false) {
                $new_key = 'f'.(substr($key,1));
                $cur_val[$new_key] = $value;
            }
        }
        
        $cur_val['f_main_logo'] = $main_logo_filename;
        $cur_val['f_secondary_logo'] = $secondary_logo_filename;
        $cur_val['f_placeholder_image'] = $placeholder_img_filename;
        $cur_val['f_fb_image'] = $fb_image_filename;
        $cur_val['f_faveicon'] = $favicon_filename;        

        $changes = $this->audittrail->createUpdateFormat(array_diff_assoc($cur_val, $prev_val),$prev_val);        
        
        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
        }else{
            $success = $this->model_client_information->update($this->input->post(), $c_id, $main_logo_filename, $secondary_logo_filename, $placeholder_img_filename, $fb_image_filename, $favicon_filename);
            $response['success'] = $success;
            $response['message'] = $this->response->message(($success ? 'success' : 'failed'), 'update', 'Client Information');
            $response['prev_val'] = $prev_val;
            $response['cur_val'] = $cur_val;
            //log audit trail              
            $client_name = $this->input->post('f_name');
            $remarks = "Client Information ".$client_name." has been updated successfully. \nChanges: \n$changes";
            $this->model_client_information->log_audit_trail($remarks, "update");
        }

        echo json_encode($response);        
    }
}
