<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main_profile_settings extends CI_Controller {

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

	public function isLoggedIn() {
		if($this->session->userdata('isLoggedIn') == false) {
			header("location:".base_url('Main/logout'));
		}
	}

	// change_pass 092418
	public function change_pass($token = ''){
		// $this->isLoggedIn();
	
		$data_admin = array(
			'token' => $token
        );

		$this->load->view('includes/header', $data_admin);
		$this->load->view('profile_settings/change_pass', $data_admin);
		
	}
	
	public function save_changepass_user(){
		$id = $this->session->userdata('id');	
		$secOldpass = sanitize($this->input->post('secOldpass'));
		$secNewpass = sanitize($this->input->post('secNewpass'));
		$secRetypenewpass = sanitize($this->input->post('secRetypenewpass'));

		$check_pass_using_id_fk = $this->model_profile_settings->check_pass_using_id_fk($id);

		$getOldpass_hash = $check_pass_using_id_fk->row()->password;

		if (password_verify($secOldpass, $getOldpass_hash)){ //verify if password is valid
			//password is valid
			if ($secNewpass == $secRetypenewpass) {

				// for password decryption
				$options = [
			    'cost' => 12,
				];

				$secNewpass = password_hash($secNewpass, PASSWORD_BCRYPT, $options);
				//for password decryption

				$query = $this->model_profile_settings->update_password($secNewpass, $id);

				$data = array('success' => 1, 'message' => 'Password Updated!');

			}else{
				$data = array('success' => 0, 'message' => 'New Password and Re-type Password is not the same.');
			}

		}else{
			$data = array('success' => 0, 'message' => 'Old Password is not correct.');
		}

		generate_json($data);
	}
	// change_pass 092418	

	// change avatar

	public function change_personal_information($token = ''){
		$this->isLoggedIn();

		$data_admin = array(
			'token' => $token,
			'personal_information' => $this->model_profile_settings->get_user_personal_information($this->session->userdata('id'))->row()
        );

		$this->load->view('includes/header', $data_admin);
		$this->load->view('profile_settings/change_avatar', $data_admin);
	}

	public function makedirImage(){
        if (!is_dir('./assets/')) {
            mkdir('./assets/', 0777, TRUE);
		}
		if (!is_dir('./assets/uploads/')) {
            mkdir('./assets/uploads/', 0777, TRUE);
		}
		if (!is_dir('./assets/uploads/avatars/')) {
            mkdir('./assets/uploads/avatars/', 0777, TRUE);
        }
        
    }

	public function save_changeavatar(){
		$this->load->library('s3_upload');
		$id = $this->session->userdata('id');
		$directory = './assets/uploads/avatars/';
		
		$this->makedirImage();
		if (!is_dir($directory)){
			mkdir($directory, 0777, true);
		}

		$fname = $this->input->post('first_name');
		$mname = $this->input->post('middle_name');
		$lname = $this->input->post('last_name');
		$mobile_no = $this->input->post('mobile_no');

		if (!empty($_FILES['picture'])) {
			$config_image = array();
			$config_image['upload_path'] = $directory;
			$config_image['file_name'] = $user_id = $this->session->userdata('id') . '.png';
			$config_image['overwrite'] = true;
			$config_image['allowed_types'] = '*';

			$this->load->library('upload', $config_image);

			if (!$this->upload->do_upload('picture') && $this->input->post('picture') == ''){
				$data = array('success' => 0, 'message' => $this->upload->display_errors());
			}else{
				$upload_data = $this->upload->data();

				$img = $this->security->sanitize_filename($upload_data['file_name']);

				///upload image to s3 bucket
				$s3_directory    = 'assets/uploads/avatars/'.$img;
				$this->s3_upload->deleteS3Images($s3_directory);
				
				$fileTempName    = $_FILES['picture']['tmp_name'];
				$activityContent = 'assets/uploads/avatars/'.$img;
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

				unlink($directory.'/'.$img);

				if ($this->model_profile_settings->save_changeavatar($fname, $mname, $lname, $mobile_no, $img, $id)){
					$data = array('success' => 1, 'message' => 'Changes saved successfully.');
	
					$userData = array( // store in array
						'fname'	  => $fname,
						'mname'  => $mname,
						'lname'	  => $lname,
						'mobile_no'	  => $mobile_no,
						'avatar' => $img,
					);
	
					$this->session->set_userdata($userData); // set session
				}else{
					$data = array('success' => 0, 'message' => 'Failed saving changes.');
				}
			}
		} else{
			if ($this->model_profile_settings->save_profilename($fname, $mname, $lname, $mobile_no, $id)){
				$data = array('success' => 1, 'message' => 'Changes saved successfully.');

				$userData = array( // store in array
					'fname'	  => $fname,
					'mname'  => $mname,
					'lname'	  => $lname,
					'mobile_no'	  => $mobile_no,
				);

				$this->session->set_userdata($userData); // set session
			}else{
				$data = array('success' => 0, 'message' => 'Failed saving changes.');
			}
		}

		generate_json($data);
	}

	public function image_resize($path, $file){
		$config_resize = array();
		$config_resize['image_library'] = 'gd2';
		$config_resize['source_image'] = $path;

		$config_resize['maintain_ratio'] = TRUE;
		$config_resize['width'] = 80;
		$config_resize['height'] = 80;
		$config_resize['new_image'] =  './assets/avatar/'.$file;
		$this->load->library('image_lib',$config_resize);
		$this->image_lib->resize();
	}	
}