<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class user_list extends CI_Controller {
	public function __construct(){
		parent::__construct();
        //load model or libraries below here...
		$this->load->model('settings/model_user_list', 'model_user_list');
		$this->load->model('settings/model_access_control', 'model_access_control');
		$this->load->model('model');
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


    public function user_list_table() 
    {
		$this->isLoggedIn();
		$filters = [
            '_record_status' => $this->input->post('_record_status'),
            '_username' => $this->input->post('_username'),
        ];
    	$query = $this->model_user_list->user_list_table($filters, $_REQUEST);
    	generate_json($query);
	}

    
    public function f_upload_file()
    {
    	$this->load->library('upload');
    	$_FILES['userfile']['name']     = $_FILES['avatar_image']['name'];
    	$_FILES['userfile']['type']     = $_FILES['avatar_image']['type'];
    	$_FILES['userfile']['tmp_name'] = $_FILES['avatar_image']['tmp_name'];
    	$_FILES['userfile']['error']    = $_FILES['avatar_image']['error'];
    	$_FILES['userfile']['size']     = $_FILES['avatar_image']['size'];

        $directory    = 'assets/uploads/avatars/';
        if (!is_dir( 'assets/uploads/')) {
            mkdir( 'assets/uploads/', 0777, true);
        }
        if (!is_dir( 'assets/uploads/avatars/')) {
            mkdir( 'assets/uploads/avatars/', 0777, true);
        }
    
		$images = $this->model_user_list->getImageByFileName($this->input->post('f_email'));
		foreach($images->result_array() as $image){
			if(file_exists('assets/uploads/avatars/'.str_replace('==','',$image['avatar']))){
				unlink(('assets/uploads/avatars/'.str_replace('==','',$image['avatar'])));
			}
		}
	
    	// $mct =  microtime('get_as_float');
    	// $mct = str_replace('.', '', $mct);
		// //$this->makedirImage();
		
		$file_name = en_dec('en',$this->upload->data()['file_name']).'.'.pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
    	$config = array(
    		'file_name'     => $file_name,
    		'allowed_types' => 'jpg|jpeg|png|pdf',
    		'max_size'      => 3000,
    		'overwrite'     => FALSE,
    		'upload_path' => $directory
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
    	}
    	else {
			
			// $directory = './assets/uploads/avatars';
			// $fileTempName    = $_FILES['userfile']['tmp_name'];
			// $activityContent = 'assets/uploads/avatars/'.$file_name;
			// $uploadS3 = $this->s3_upload->uploadS3ImagesOrig($fileTempName, $activityContent);
			// if($uploadS3 != 1){
			// 	$response = [
			// 		'environment' => ENVIRONMENT,
			// 		'success'     => false,
			// 		'message'     => 'S3 Bucket upload failed.'
			// 	];
	
			// 	echo json_encode($response);
			// 	die();
			// }
			
			// unlink($directory.'/'.$file_name);
			
    		if($file_name == "") { // avatar is required upon merchant creation
    			$response = array(
    				'success'      => false,
    				'environment' => ENVIRONMENT,
    				'message'     => 'No user avatar specified',
    				'csrf_name'   => $this->security->get_csrf_token_name(),
    				'csrf_hash'   => $this->security->get_csrf_hash()
    			);
    			echo json_encode($response);
    			die();
    		}
    		else {
    			return $file_name;	
    		}
    	}
	}
    
	

    public function disable_data()
    {
    	$this->isLoggedIn();

    	$disable_id = sanitize($this->input->post('disable_id'));
    	$record_status = sanitize($this->input->post('record_status'));

        // print_r($disable_id);
        // print_r('<br>');
        // print_r($record_status);
        // die();

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
		$user_data = $this->model_user_list->get_data($disable_id);
		$remarks = $user_data[0]['username']." has been successfully ".$record_text;

    	if ($disable_id > 0 && $record_status > 0) {
    		if ($this->model_user_list->disable_data($disable_id, $record_status)) {
				$data = array("success" => 1, 'message' => "Record ".$record_text." successfully!");
				$this->audittrail->logActivity('User List', $remarks, $record_text, $this->session->userdata('username'));
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
		
		//get category data using delete_id
		$user_data = $this->model_user_list->get_data($delete_id);
		$remarks = $user_data[0]['username']." has been successfully deleted";

    	if ($delete_id > 0) {
    		if ($this->model_user_list->delete_data($delete_id,$user_data)) {
				$data = array("success" => 1, 'message' => "Record deleted successfully!");
				$this->audittrail->logActivity('User List', $remarks, 'deleted', $this->session->userdata('username'));
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

    public function create_data()
    {
    	$file_name = (!empty($this->input->post('current_avatar_url'))) ? $this->input->post('current_avatar_url') : "";

    	//if there is a file, upload it first and take note of the file name
    	if(count($_FILES)>1){
    		$file_name = $this->f_upload_file();
		}
		
		$password = '';

        //validate fields
        $validation = array(
        	// array('f_password','Password','required'),
        	array('f_email','Username','required|max_length[100]|min_length[5]|valid_email'),
        );

        //initial validation
        foreach ($validation as $value) {
        	$this->form_validation->set_rules($value[0],$value[1],$value[2]);
        }

        //secondary validation
        //username should be unique
        $user_check = $this->model_user_list->check_username(
        	$this->input->post('f_email')
        );

        if($user_check==false){
        	$response = array(
        		'success'      => false,
        		'environment' => ENVIRONMENT,
        		'message'     => "Username already taken.",
        		'csrf_name'   => $this->security->get_csrf_token_name(),
        		'csrf_hash'   => $this->security->get_csrf_hash()
        	);
        	echo json_encode($response);
        	die();
        }

        if ($this->form_validation->run() == FALSE){
        	$response = array(
        		'success'      => false,
        		'environment' => ENVIRONMENT,
        		'message'     => validation_errors(),
        		'csrf_name'   => $this->security->get_csrf_token_name(),
        		'csrf_hash'   => $this->security->get_csrf_hash()
        	);

            //remove uploaded image
        	if($file_name!=""){
        		unlink('./assets/uploads/avatars/'.$file_name);
        	}
        	echo json_encode($response);
        }else{
                //generate functions json
        	$functions = $this->model_access_control->generate_functions($this->input->post());
        	$response = array(
        		'success'     => true,
        		'environment' => ENVIRONMENT,
        		'csrf_name'   => $this->security->get_csrf_token_name(),
        		'csrf_hash'   => $this->security->get_csrf_hash()
        	);

        	$this->model_user_list->create_user(
        		1,
        		$password,
        		$this->input->post('f_email'),
        		$file_name,
        		$functions
			);         
			
			$email = $this->input->post('f_email');
            $first_name = $this->input->post('f_first_name');
			$last_name = $this->input->post('f_last_name');
            $client_name = $this->input->post('client_name');
			$remarks = $email." sucessfully added to User List";
			$response['message'] = 'User added';      
			$this->audittrail->logActivity('User List', $remarks, 'add', $this->session->userdata('username'));  
			
			$resetpasslink = base_url().'Main/first_password_reset_form/'.md5($email);

            $this->sendVerificationEmail($email, $password, $resetpasslink);   
            
        	echo json_encode($response);
        }
	}
	
	// function sendVerificationEmail($email, $password, $resetpasslink){
	// 	$this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
	// 	$this->email->to($email);
	// 	$this->email->subject(get_company_name()." | Account Details");
	// 	$data['company_email'] = get_company_email();
	// 	$data['email'] = $email;
	// 	$data['password'] = $password;
	// 	$data['resetpasslink'] = $resetpasslink;
	// 	$this->email->message($this->load->view("includes/emails/acc_verify_template", $data, TRUE));
	// 	$this->email->send();
	// }

    public function sendVerificationEmail($email,$password, $resetpasslink){
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'teeseriesphilippines@gmail.com',
            'smtp_pass' => 'teeseriesph',
            'charset' => 'utf-8',
            'newline'   => "\r\n",
            'wordwrap'=> TRUE,
            'mailtype' => 'html'
        );
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");  
        $this->email->from('ulul@gmail.com',get_company_name());
        $this->email->to($email);
        
        $this->email->subject(get_company_name()." | Password Set Up");
        $data['email']=$email;
        $data['resetpasslink'] = $resetpasslink;
        $view = $this->load->view('email/templates/verify_email',$data,true);
        $this->email->message($view);
        $this->email->send();
        
        // Set to, from, message, etc.
        
        //print_r($this->email->print_debugger());
    }

    public function view($token = '')
    {   
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['aul']['view'] == 1) {
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
            $data_admin['page_content'] = $this->load->view('admin/settings/user_list',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
            // end - load all the views synchronously

            
        }else{
            $this->load->view('error_404');
        }
    }
	
	public function add_user($token = '')
	{
		$this->isLoggedIn();
        if ($this->loginstate->get_access()['aul']['create'] == 1) {
        	$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/';
        	$main_nav_id = $this->views_restriction($content_url);
            // start - data to be used for views
        	$data_admin = array(
				'token' => $token,
				'type'  => 'New User',
                'main_nav_id' => $main_nav_id,
				'id'	=> '',
			);
			// end - data to be used for views

            // start - load all the views synchronously
            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/settings/add_user',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
	}
    public function get_data_admin()
    {
        $this->isLoggedIn();
        $id = sanitize($this->input->post('id'));
        $result = $this->model_user_list->get_data($id);
        generate_json($result);
    }
	
    public function update_data()
    {
    	$file_name = (!empty($this->input->post('current_avatar_url'))) ? $this->input->post('current_avatar_url') : "";
    	//if there is a file, upload it first and take note of the file name
    	if(count($_FILES)>1){
    		$file_name = $this->f_upload_file();
    	}
		$file_name = str_replace('==.','.',$file_name);

    	//secondary validation
        //username should be unique
    	$user_check = $this->model_user_list->check_username(
    		$this->input->post('f_email'),
    		$this->input->post('f_id')
    	);

    	if ($user_check == FALSE){
    		$response = array(
    			'success'      => false,
    			'environment' => ENVIRONMENT,
    			'message'     => 'Username doesnt exist',
    			'csrf_name'   => $this->security->get_csrf_token_name(),
    			'csrf_hash'   => $this->security->get_csrf_hash()
    		);

            //remove uploaded image
    		// if($file_name!=""){
    		// 	unlink('./assets/uploads/avatars/'.$file_name);
    		// }
    		echo json_encode($response);
    	}
    	else {
    		//generate functions json
    		$functions = $this->model_access_control->generate_functions($this->input->post());
    		$response = array(
    			'success'     => true,
    			'environment' => ENVIRONMENT,
    			'csrf_name'   => $this->security->get_csrf_token_name(),
    			'csrf_hash'   => $this->security->get_csrf_hash()
			);
			
			$user_data = $this->model_user_list->get_data($this->input->post('f_id'));
			
			$prev_val = [
				'f_email' => $user_data[0]['username'],
				'f_password' => en_dec('dec',$user_data[0]['password']),
				'avatar' => $user_data[0]['avatar'],
				//'functions' => $user_data[0]['functions'],
			];			

    		$this->model_user_list->update_user(
    			$this->input->post('f_email'),
    			$this->input->post('f_password'),
    			$file_name,
    			$functions,
    			$this->input->post('f_id')
    		);              
    		// if (!empty($this->input->post('f_password')))
			// 	$this->notification->change_password($this->input->post('f_id'));

			$cur_val = [
				'f_email' => $this->input->post('f_email'),
				'f_password' => $this->input->post('f_password'),
				'avatar' => $file_name,
				//'functions' => $functions				
			];

			$audit_string = "";
			$pre_func = json_decode($user_data[0]['functions'],true);
			$cur_func = json_decode($functions,true);
			$cur_arr = [];
			$pre_arr = [];

			
           if($pre_func  != $cur_func){
		  	 $audit_string .= $this->audittrail->UserListString($pre_func,$cur_func);
             $this->audittrail->logActivity('User List', 'User List'." has been updated successfully. \nChanges: \n".$audit_string, 'update', $this->session->userdata('username'));
		   }
		
			$response['message'] = 'User updated';
			$response['cur_arr'] = $cur_arr;
			$response['pre_arr'] = $pre_arr;
    		echo json_encode($response);
    		die();
    	}
    }
	public function edit_user($token = '', $id = "")
	{
		$this->isLoggedIn();
        if ($this->loginstate->get_access()['aul']['update'] == 1) {
        	$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/';
        	$main_nav_id = $this->views_restriction($content_url);
            // start - data to be used for views
        	$data_admin = array(
				'token' => $token,
				'type'  => 'Edit User',
                'main_nav_id' => $main_nav_id,
				'id'	=> $id,
			);
			// end - data to be used for views

            // start - load all the views synchronously
            $data_admin['active_page'] =  $this->session->userdata('active_page');
            $data_admin['subnav'] = false;
            $data_admin['page_content'] = $this->load->view('admin/settings/add_user',$data_admin,TRUE);
            $this->load->view('admin_template',$data_admin,'',TRUE);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
	}

}