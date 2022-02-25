<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 300);
set_time_limit(300);

class Shop_banners extends CI_Controller {
	public function __construct(){
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('setting/model_shop_banners');
        $this->load->library('s3_upload');
        $this->load->library('s3_resizeupload');
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
		if ($this->loginstate->get_access()['shop_banners']['view'] == 1){
			//start - for restriction of views
	        $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';
	        $main_nav_id = $this->views_restriction($content_url);
	        //end - for restriction of views main_nav_id

	        // start - data to be used for views
	        $data_admin = array(
	            'token' => $token,
                'main_nav_id' => $main_nav_id, //for highlight the navigation
                'banners' => $this->model_shop_banners->get_banners(),
                'inactive_banners' => $this->model_shop_banners->get_inactive_banners()
	        );
	        // end - data to be used for views

	        // start - load all the views synchronously
	        $this->load->view('includes/header', $data_admin);
	        $this->load->view('settings/settings_shop_banners', $data_admin);
	        // end - load all the views synchronously
        }else{
    		$this->load->view('error_404');
    	}
	}

	function cleanNameFunction($name){
        $name = preg_replace("/[^a-zA-Z0-9_.]/", "", $name);
        return $name;
    }

    public function fileupload(){
        $request = sanitize($this->input->post('request'));

        if(empty($request)){ //add banner
            if(!empty($_FILES['file']['name'])){
                // Set preference
                $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/');
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['max_size'] = '10000'; // max_size in kb
                $config['file_name'] = $this->cleanNameFunction($_FILES['file']['name']);

                //Load upload library
                $this->load->library('upload',$config); 

                // File upload
                if($this->upload->do_upload('file')){
                // Get data about the file
                    $uploadData = $this->upload->data(); //add to folder
                    $filename = $uploadData['file_name'];
                    $add_banner = $this->model_shop_banners->add_banner($uploadData['file_name']); //add to database
                    // do the resizes
                    $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/').$uploadData['file_name'], "banners0110");
                    $this->audittrail->logActivity('Shop Banners', "Shop Banner $filename has been added successfully.", 'add', $this->session->userdata('username'));
                }                
            }
            
        }else if($request == 2){ //fetch the banner 
            $target_dir = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/');
            $file_list = array();
            $count = 1;
            // Target directory
            $dir = $target_dir;
            if (is_dir($dir)){
                if ($dh = opendir($dir)){
                    // Read files
                    while (($file = readdir($dh)) !== false){

                        if($file != '' && $file != '.' && $file != '..'){
                            // File path
                            $file_path = $target_dir.$file;

                            if(!is_dir($file_path)){

                                $size = filesize($file_path);

                                $file_list[] = array('name' => $file, 'size' => $size, 'path' => $file_path, 'count' => $count++);
                            }
                        }
                    }
                    closedir($dh);
                }
            }

            if (!empty($file_list)) { //to get the total count of products  
                for ($x = 0; $x < count($file_list); $x++) {
                    $file_list[$x]['count'] = $count-1;
                }
            }

            generate_json($file_list);
        }  
    }    

    public function deleteupload($arr_names){
        //$arr_names = $this->input->post('arr_names');
        
        foreach ($arr_names as $filename) {
            //$target_directory_image = './assets/img/ad-banner/';
            //$target_directory_image2 = './assets/img/all_banner/';
            //$target_directory_image3 = './assets/img/all_banner/webp/';
            // $target_directory_image = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/');
            // $target_directory_image2 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/all_banner/');
            //$target_directory_image3 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/all_banner/webp/').$filename;
            // if (file_exists($target_directory_image.$filename)){
            //     unlink($target_directory_image.$filename); //delete to folder
            //     unlink($target_directory_image2.(explode('.',$filename)[0]).'.jpg'); //delete to folder - replace file extension with .jpg
            //     //unlink($d3.(explode('.',$filename)[0]).'.webp'); //delete to folder - replace file extension with .webp
            //     //$add_banner = $this->model_shop_banners->delete_banner($filename); //delete to database (status = 0)
            //     $this->audittrail->logActivity('Shop Banners', "Shop Banner $filename has been removed successfully.", 'delete', $this->session->userdata('username'));
            // }

            /// s3 delete image function
            $s3_directory = 'assets/img/ad-banner/'.$filename;
            $this->s3_upload->deleteS3Images($s3_directory);

            $s3_directory = 'assets/img/all_banner/'.$filename;
            $this->s3_upload->deleteS3Images($s3_directory);

            $this->audittrail->logActivity('Shop Banners', "Shop Banner $filename has been removed successfully.", 'delete', $this->session->userdata('username'));
        }

        //generate_json($arr_names);        
    }

    function cleanStringFilename($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     }

    public function upload_banner(){ //from dev_settings client_info - easy upload process
        $this->isLoggedIn();

        $this->load->library('upload');     
        $this->makedirImage();   
        $directory = 'assets/img';
        //$directory = './assets/img/ad-banner/';
        //$directory = get_shop_url('assets/img/ad-banner/');

        $sort_num = $this->input->post('sorting');

        $_FILES['userfile']['name']     = $_FILES['file']['name'];
        $_FILES['userfile']['type']     = $_FILES['file']['type'];
        $_FILES['userfile']['tmp_name'] = $_FILES['file']['tmp_name'];
        $_FILES['userfile']['error']    = $_FILES['file']['error'];
        $_FILES['userfile']['size']     = $_FILES['file']['size'];        

   
        $config = array(
            'file_name'     => $this->cleanStringFilename($_FILES['file']['name']).randomShopCode(),
            'allowed_types' => 'jpg|png|jpeg|JPG|PNG',
            'max_size'      => 10000,
            'overwrite'     => TRUE,
            'min_width'     => '20',
            'min_height'    => '20',
            'upload_path'   =>  $directory
        );

        $this->upload->initialize($config);
        if ( ! $this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());
            $response = array(
                'status'      => false,
                'error'       => $error,
                'directory'   => $directory
            );
        }
        else{
            $uploadData = $this->upload->data(); //add to folder
            
            $filename = $uploadData['file_name'];
            $insert_banner = $this->model_shop_banners->add_banner($filename,$sort_num);
            // do the resizes
            // $this->Model_adhoc_resize->resize_images($directory.$uploadData['file_name'], "banners0110");
            
            ///upload image to s3 bucket
            $fileTempName    = $_FILES['userfile']['tmp_name'];
            $activityContent = 'assets/img/ad-banner/'.$filename;
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
            
            unlink($directory.'/'.$filename);
            
            $getOrigimageDim = getimagesize($_FILES['userfile']['tmp_name']);
            $fileTempName    = $_FILES['userfile']['tmp_name'];
            $s3_directory    = 'assets/img/ad-banner/';
            $new_str = str_replace(' ', '', $filename);
           // die($filename);
            $activityContent = 'assets/img/ad-banner/'.removeFileExtension($filename).'.jpg';
            $uploadS3        = $this->s3_resizeupload->uploadResize_S3($fileTempName, $s3_directory, $activityContent, 'all_banner', $getOrigimageDim);

            if($uploadS3 != 1){
                $response = [
                    'environment' => ENVIRONMENT,
                    'success'     => false,
                    'message'     => 'S3 Bucket upload failed.'
                ];
    
                echo json_encode($response);
                die();
            }
            
            $this->audittrail->logActivity('Shop Banners', "Shop Banner $filename has been added successfully.", 'add', $this->session->userdata('username'));
            $response = array(
                'status' => true,
                'message' => 'Files Successfully Uploaded',
                'id' => explode('.',$filename)[0]
            );         
        }
        echo json_encode($response);        
    }

    public function update_sorting(){
        $filename = $this->input->post('filename');
        $sorting = $this->input->post('sorting');

        $query = $this->model_shop_banners->update_sorting($filename,$sorting);
        $response = [];
        if($query > 0){
            $response = array(
                'status' => true,
                'message' => 'Sorting Updated'
            );
            $banner_info = $this->model_shop_banners->get_banner_info($filename);
            $remarks = "Shop Banner ".$filename." sorting has been updated to ".$sorting." successfully ";
            $this->audittrail->logActivity('Shop Banners', $remarks, 'update', $this->session->userdata('username'));
        }        
        echo json_encode($response);
    }

    public function makedirImage(){
        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'), 0777, TRUE);
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/'), 0777, TRUE);
        }
    }

    public function delete_banners(){
        $file_names = $this->input->post('to_delete');
        $deleted = 0;
        if($file_names){
            foreach($file_names as $file_name){
                $res = $this->model_shop_banners->delete_banner($file_name);
                $deleted += intval($res);
                if($res){                
                    $this->audittrail->logActivity('Shop Banners', "Shop Banner $file_name has been deleted successfully.", 'delete', $this->session->userdata('username'));
                }
            }
            $this->deleteupload($file_names);
        }
        $response = [];
        if($deleted > 0){
            $response = array(
                'status' => true,
                'message' => $deleted.' records deleted'
            );
        }        
        echo json_encode($response);
    }


    public function add_linkBanner(){


       $banner_array = array(
             'BannerLink'   =>  sanitize($this->input->post('BannerLink')),
             'BannerID'     =>  sanitize($this->input->post('BannerID'))
       );

       $this->model_shop_banners->add_banner_link($banner_array);

       $response['success'] = true;
       echo json_encode($response);

    }

    public function setSchedPost_Banner()
    {
        $input = $this->input->post();
        
        // format date into db format
        $input["dateFrom"] = $input['dateFrom'] == '' ? '' : date("Y-m-d", strtotime($input["dateFrom"]));
        $input["dateTo"] = $input['dateTo'] == '' ? '' : date("Y-m-d", strtotime(($input["dateTo"])));
        $input["timeFrom"] = $input['timeFrom'] == '' ? '00:00' : $input['timeFrom'];
        $input["timeTo"] = $input['timeTo'] == '' ? '00:00' : $input['timeTo'];

        // concat time to date
        $data['update']["dateFrom"] = $input["dateFrom"] .' '. $input["timeFrom"];
        $data['update']["dateTo"] = $input["dateTo"] .' '. $input["timeTo"];
        $data['update']["banner_id"] = $input["banner_id"];

        $data['start']['dateFrom'] = $data['update']['dateFrom'];
        $data['start']['dateTo'] = $data['update']['dateFrom'];
        $data['start']['id'] = $input['banner_id'];

        $data['startFinal']['dateFrom'] = $data['update']['dateFrom'];
        $data['startFinal']['id'] = $input['banner_id'];
        
        $data['end']['dateFrom'] = $data['update']["dateFrom"];
        $data['end']['dateTo'] = $data['update']['dateTo'];
        $data['end']['id'] = $input['banner_id'];

        $data['endFinal']['dateFrom'] = $data['update']["dateTo"];
        $data['endFinal']['dateTo'] = $data['update']["dateTo"];
        $data['endFinal']['id'] = $input['banner_id'];

        if (!empty($data['update']['dateFrom']) && !empty($data['update']['dateTo'])) {
            // check if having same scheduled date, max to six(6) only
            $check_banner_startdate = $this->model_shop_banners->check_banner_startdate($data['start']);
            $check_banner_endDate = $this->model_shop_banners->check_banner_endDate($data['endFinal']);
            if ($check_banner_startdate <= 6 && $check_banner_endDate <= 6) {
                // check if there's an overlapping date
                $check_banner_startdate_endDate = $this->model_shop_banners->check_banner_startdate_endDate($data['startFinal']);
                $check_banner_startdate_endDate_final = $this->model_shop_banners->check_banner_startdate_endDate_final($data['end']);
                if ($check_banner_startdate_endDate == 0 || $check_banner_startdate_endDate_final == 0) {
                    $this->db->trans_begin(); // start transaction to avoid updating mistakes
                    $setSched = $this->model_shop_banners->setSchedPost_Banner($data['update']);

                    if ($setSched === FALSE) {
                        $this->db->trans_rollback();
                        $respo = array(
                            'statusCode' => 500,
                            'statusText' => 'Failed setting of Schedule.'
                        );
                    } else {
                        $this->db->trans_commit();
                        $respo = array(
                            'statusCode' => 201,
                            'statusText' => 'Set Schedule Successfully!'
                        );
                    }
                } else {
                    $respo['statusCode'] = 500;
                    $respo['statusText'] = 'Avoid saving Shop Banners that are less than Start Date or Greater than End Date to the previously scheduled banner!';
                }
            } else {
                $respo['statusCode'] = 500;
                $respo['statusText'] = 'Avoid saving more than six(6) Shop Banners that have same scheduling date!';
            }
        } else {
            $respo['statusCode'] = 422;
            $respo['statusText'] = 'Start Date and End Date is required!';
        }

        echo json_encode($respo);
    }

    public function getBanner_info()
    {
        $banner_id = $this->input->post('banner_id');
        
        $query = $this->model_shop_banners->getBanner_info($banner_id)->row();

        $data['start_date'] = date('Y-m-d', strtotime($query->scheduledFrom_post));
        $data['start_time'] = date('H:i', strtotime($query->scheduledFrom_post));
        $data['end_date'] = date('Y-m-d', strtotime($query->scheduledTo_post));
        $data['end_time'] = date('H:i', strtotime($query->scheduledTo_post));

        echo json_encode($data);
    }

    public function deact_banner()
    {
        $id = $this->input->post('id');
        
        $this->db->trans_begin();
        $query = $this->model_shop_banners->deact_banner($id);

        if ($query === FALSE) {
            $this->db->trans_rollback();
            $respo['success'] = false;
            $respo['message'] = 'Deactivation Failed';
        } else {
            $this->db->trans_commit();
            $respo['success'] = true;
            $respo['message'] = 'Successfully deactivated!';
        }

        echo json_encode($respo);
    }

    public function activate_banner()
    {
        $id = $this->input->post('id');
        $query = $this->model_shop_banners->get_banners();
        
        if(count($query) < 6) {
            $this->db->trans_begin();
            $activate = $this->model_shop_banners->activate_banner($id);
            if ($activate === FALSE) {
                $this->db->trans_rollback();
                $respo['success'] = false;
                $respo['message'] = 'Failed to activate banner.';
            } else {
                $this->db->trans_commit();
                $respo['success'] = true;
                $respo['message'] = 'Successfully Activated!';
            }
        } else {
            $respo['success'] = false;
            $respo['message'] = 'Maximum of 6 active banner, kindly deactivate one first.';
        }

        echo json_encode($respo);
    }

    /** for cronjob -- start */
    public function shop_banner_functionality()
    {
        $this->_check_activate();
        $this->_check_deactivate();

        return true;
    }

    private function _check_activate()
    {
        $data['date'] = today();
        $data['time'] = date('H:i'.':00');
        $data['status'] = 1;
        $data['is_active'] = 0;

        $query = $this->model_shop_banners->check_activate($data);
        $get_banners = count($this->model_shop_banners->get_banners());
        $count = $get_banners - $query < 0 ? $query - $get_banners : 6 - $query;

        $count2 = $get_banners + $query;
        $all = $count2;
        if ($count2 > 6) {
            $count2 = $count2 - 6;
            $count2 = $get_banners - $count2;
        }

        if ($query > 0) {
            if ($get_banners == 6) {
                for ($i = $get_banners; $i >= $count; $i--) {
                    $this->_deact_banner_sorting($i);
                }

                $this->model_shop_banners->activate_banner_sched($data);
                
            } else {
                if ($all > 6) {
                    for ($i = $get_banners; $i >= $count2; $i--) { 
                        $this->_deact_banner_sorting($i);
                    }
                }

                $this->model_shop_banners->activate_banner_sched($data);
            }
        }

        return true;
    }

    public function _check_deactivate()
    {
        $data['date'] = today();
        $data['time'] = date('H:i'.':00');
        $data['status'] = 1;
        $data['is_active'] = 1;

        $query = $this->model_shop_banners->check_deactivate($data);

        $this->db->trans_begin();
        $deact = $this->model_shop_banners->deactivate_banner_sched($data);
        if ($deact === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        return true;
    }

    private function _deact_banner_sorting($sorting)
    {
        $data['status'] = 1;
        $data['is_active'] = 1;
        $data['sorting'] = $sorting;
        $query = $this->model_shop_banners->deact_banner_sorting($data);

        $this->db->trans_begin();
        if ($query === FALSE) {
            $status = false;
        } else {
            $status = true;
        }

        return $status;
    }

    /** for cronjob -- end */

}