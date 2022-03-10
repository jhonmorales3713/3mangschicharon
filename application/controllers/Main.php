<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct() {
        parent::__construct();        
        $this->load->helper('core');
        // $this->load->model('chapter/model_chapter');
         $this->load->model('model');
         $this->load->model('model_profile_settings');
        // $this->load->model('model_public');
        // $this->load->model('admin/model_chapters');
        
        // $this->load->model('admin/model_settings');
        // declarations
        // $this->session->active_module = strtolower($this->router->class);
        // $this->parent_path = array('', base_url('chapter/'), 'Chapter');
    }
	public function index()
	{
		if($this->session->userdata('role') == 'admin'){
			
			$access_nav=explode(',',$this->session->userdata('access_nav'));
			$main_nav = ($access_nav)[0];
			$url='';
			switch($main_nav){
				case 2://orders
					$url = "Main_orders";
				break;
				case 3://products
					$url = "Main_products";
					$this->session->set_userdata('active_page','Products');
				break;
				case 4://orders
					$url = "Main_shops";
				break;
				case 5://orders
					$url = "Main_customers";
				break;
				case 6://orders
					$url = "Main_accounts";
				break;
			}
			header('Location: '.base_url('admin/'.$url));
			if($this->session->userdata('role')=='admin'){

			}
		}
	}

    public function first_password_reset_form($email)
    {
        $this->session->sess_destroy();
        $checkIfFirstReset = $this->model->checkIfFirstReset($email)->num_rows();

        if($checkIfFirstReset == 0){
            $this->load->view('error_404');
        }else{
            $validate_username = $this->model->first_validate_username_md5($email);
            if ($validate_username->num_rows() > 0) { // check if email is exist
                $userObj = $validate_username->row();
                $data_admin = array(
                                'sys_users_id' => $userObj->id,
								'adminstyle' => true
                            );
                $this->load->view('admin/first_change_pass_reset', $data_admin);
            }else{
                $this->load->view('error_404');
            }

        }
    }
	
    public function setfirstpassword(){
        $id = sanitize($this->input->post('user-id'));
        $secNewpass = sanitize($this->input->post('password'));
        $secRetypenewpass = sanitize($this->input->post('passwordretype'));

        if ($secNewpass == $secRetypenewpass) {
            // for password decryption
			$secNewpass = en_dec('en',$secNewpass);
            //$secNewpass = password_hash($secNewpass, PASSWORD_BCRYPT, $options);
            //for password decryption

            $query = $this->model_profile_settings->update_first_password($secNewpass, $id);

            $data = array('success' => 1, 'message' => 'Successfully Saved!');
        }else{
            $data = array('success' => 0, 'message' => 'New Password and Re-type Password is not the same.');
        }
        generate_json($data);
    }

    public function logout()
    {
        if(!empty($this->session->userdata('username'))){
            //$this->log_seller_time_out_activity();
            //$this->audittrail->logActivity('Logout', $this->session->userdata('username').' have been successfully logged out.', 'logout', $this->session->userdata('username'));
        }

        $this->session->sess_destroy();
		header('Location: '.base_url('admin/Login'));
    }
	
}
