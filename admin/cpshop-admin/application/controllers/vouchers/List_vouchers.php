<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class List_vouchers extends CI_Controller {
  public function __construct(){
    parent::__construct();
  $this->load->model('vouchers/Model_list_vouchers');
  
  }

  public function isLoggedIn() {
      if($this->session->userdata('isLoggedIn') == false) {
          header("location:".base_url('Main/logout'));
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

      if (in_array($content_url, $url_content_arr) == false) {
          header("location:" . base_url('Main/logout'));
      } else {
          return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
      }
  }

    public function index($token = ""){
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['voucher_list']['view'] == 1){
            //start - for restriction of views
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            //end - for restriction of views main_nav_id

            // start - data to be used for views
            $data_admin = array(
            'token' => $token,
            'main_nav_id' => $main_nav_id, //for highlight the navigation,
            'shops' => $this->Model_list_vouchers->get_shop_options(),
            );
            // end - data to be used for views

            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('vouchers/list_vouchers', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
    }

  public function get_vouchers_list_json_data($exportable = false){
    $this->isLoggedIn();

    $filters = [
			'_voucher_code'  => $this->input->post('_voucher_code'),
			'_voucher_refnum'  => $this->input->post('_voucher_refnum'),
      '_shopname'  => $this->input->post('_shopname'),
      '_record_status'  => $this->input->post('_record_status'),
      '_select_status'  => $this->input->post('_select_status')
		];

   //  die($this->input->post('_voucher_code'));
    $data = $this->Model_list_vouchers->get_vouchers_list_json($filters, $_REQUEST);
    echo json_encode($data);
  }


  public function export_vouchers_list(){
    $this->isLoggedIn();          

    $filters = [
			'_voucher_code'  => $this->input->post('_voucher_code'),
			'_voucher_refnum'  => $this->input->post('_voucher_refnum'),
      '_shopname'  => $this->input->post('_shopname'),
      '_record_status'  => $this->input->post('_record_status'),
		];

        
   $void_record = $this->Model_list_vouchers->get_vouchers_list_json($filters,$_REQUEST,true);    
   $filters1     = json_decode($this->input->post('_filters'));
   $filter_string        = $this->audittrail->voidrecordListString($filters1,true);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();                

    $sheet->setCellValue('B1', 'Vouchers List');    

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(20);
    $sheet->getColumnDimension('G')->setWidth(20);

    $sheet->setCellValue('A6', 'Date Issued');
    $sheet->setCellValue('B6', 'Valid Until');
    $sheet->setCellValue('C6', 'Shop Name');
    $sheet->setCellValue('D6', 'ID No');
    $sheet->setCellValue('E6', 'Voucher Reference Number');
    $sheet->setCellValue('F6', 'Voucher Code');
    $sheet->setCellValue('G6', 'Voucher Amount');
    $sheet->setCellValue('H6', 'Status');


    $sheet->getStyle('B1')->getFont()->setBold(true);
    $sheet->getStyle('A6:H6')->getFont()->setBold(true);

    $exceldata= array();
    foreach ($void_record['data'] as $key => $row) {

      $resultArray = array(
        '1' => $row[0],
        '2' => $row[1],
        '3' => $row[2],
        '4' => $row[3],
        '5' => $row[4],
        '6' => $row[5],
        '7' => $row[6],
        '8' => $row[7],
    
      );
      $exceldata[] = $resultArray;
    }

    $sheet->fromArray($exceldata, null, 'A7');

    $writer = new Xlsx($spreadsheet);
    $filename = 'Vouchers List';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xls"'); 
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $this->audittrail->logActivity('Audit Trail', 'Audit Trail been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
    return $writer->save('php://output');
    exit();
    
  }


   public function add_voucher($token = '')
    {

    $this->isLoggedIn();
    if ($this->loginstate->get_access()['voucher_list']['create'] == 1){
            // start - data to be used for views
        	$data_admin = array(
				  'token' => $token,
				  'type'  => 'New User',
			   	'id'	=> '',
          'shops_per_id' => $this->Model_list_vouchers->get_sys_shop_per_id(),
          'shops' => $this->Model_list_vouchers->get_shop_options(),
			);
			// end - data to be used for views

            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('vouchers/add_vouchers', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
 
    }

  public function save_voucher(){
    $this->isLoggedIn();

  

      $idno         = sanitize($this->input->post('idnum'));
      $shopcode     = sanitize($this->input->post('shopcode'));
      $shopid       = sanitize($this->input->post('shopid'));
      $vrefnum      = sanitize($this->input->post('vrefnum'));
      $vcode        = sanitize($this->input->post('vcode'));
      $vamount      = sanitize($this->input->post('vamount'));
      $date_issue   = sanitize($this->input->post('date_issue'));
      $date_valid   = sanitize($this->input->post('date_valid'));

   if(!empty($shopcode))
  {

    /*
        if($this->Model_list_vouchers->name_is_exist($idno) == 0 )
        { 
     */        
             if($this->Model_list_vouchers->vrefnum_is_exist($vrefnum) == 0 )
                { 
                    
                        if($this->Model_list_vouchers->vcode_is_exist($vcode) == 0 )
                        { 
                         

                              $query = $this->Model_list_vouchers->vouchers_add($idno, $shopcode, $shopid, $vrefnum, $vcode, $vamount, $date_issue, $date_valid);
      
                                if ($query) {
                                  $data = array("success" => 1, 'message' => "Record added successfully!");
                                  $this->audittrail->logActivity('Voucher', "Voucher with $vrefnum and $vcode added successfully.", 'add', $this->session->userdata('username'));
                                }else{
                                  $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                                }
                                   
                
                        }else{
                            $data = array(
                              "success" => 0,
                              "message" => 'Voucher Code already exist!'
                            );
                        } 
      
    
                }else{
                    $data = array(
                      "success" => 0,
                      "message" => 'Voucher Reference Number already exist!'
                    );
                } 
    /*
        }else{
            $data = array(
              "success" => 0,
              "message" => 'IDNO already exist!'
            );
        } 
    */

   }else{
    $data = array(
      "success" => 0,
      "message" => 'Please Complete All Required Fields!'
        );
   }
    

      generate_json($data);



   
}


  public function edit_voucher($id)
    {

      
      $token_session = $this->session->userdata('token_session');
			$token = en_dec('en', $token_session);


    $this->isLoggedIn();
    if ($this->loginstate->get_access()['voucher_list']['update'] == 1){
            // start - data to be used for views
        	$data_admin = array(
				  'token' => $token,
				  'type'  => 'New User',
			   	'id_voucher'	=> $this->Model_list_vouchers->edit_voucher($id),
          'shops_per_id' => $this->Model_list_vouchers->get_sys_shop_per_id(),
          'shops' => $this->Model_list_vouchers->get_shop_options(),
			);

 
			// end - data to be used for views

            // start - load all the views synchronously
            $this->load->view('includes/header', $data_admin);
            $this->load->view('vouchers/edit_vouchers', $data_admin);
            // end - load all the views synchronously
        }else{
            $this->load->view('error_404');
        }
 
    }




    public function update_voucher(){
      $this->isLoggedIn();

     


       if(sanitize($this->input->post('shopcode')) == ""){
        $shopcode    = sanitize($this->input->post('shopcode-edit'));
       }else{
        $shopcode     = sanitize($this->input->post('shopcode'));
       
        }
        $id           = sanitize($this->input->post('id'));
        $idno         = sanitize($this->input->post('idnum'));
        $shopid       = sanitize($this->input->post('shopid'));
        $vrefnum      = sanitize($this->input->post('vrefnum'));
        $vcode        = sanitize($this->input->post('vcode'));
        $vamount      = sanitize($this->input->post('vamount'));
        $date_issue   = sanitize($this->input->post('date_issue'));
        $date_valid   = sanitize($this->input->post('date_valid'));
        $voucher_details =  $this->Model_list_vouchers->get_voucher_details($id)->row();


       // print_r($voucher_details);

    if(!empty($shopcode))
    {
       /*
      if($this->Model_list_vouchers->name_is_exist($idno) == 0 || $voucher_details->idno == $idno )
          { */
  
                  if($this->Model_list_vouchers->vrefnum_is_exist($vrefnum) == 0 || $voucher_details->vrefno == $vrefnum )
                  { 
  
                          if($this->Model_list_vouchers->vcode_is_exist($vcode) == 0 || $voucher_details->vcode ==  $vcode  )
                          { 
                            
  
                                    
                                            $query = $this->Model_list_vouchers->vouchers_update_data($id, $idno, $shopcode, $shopid, $vrefnum, $vcode, $vamount, $date_issue, $date_valid);
                                            
                                            if ($query) {
                                              $data = array("success" => 1, 'message' => "Record updated successfully!");
                                              $this->audittrail->logActivity('Voucher', "Voucher with $vrefnum and $vcode updated successfully.", 'add', $this->session->userdata('username'));
                                            }else{
                                              $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
                                            }
                                        
   

                            }else{
                              $data = array(
                                "success" => 0,
                                "message" => 'Voucher Code already exist!'
                              );
                          } 


                  }else{
                  $data = array(
                  "success" => 0,
                  "message" => 'Voucher Reference Number already exist!'
                  );
              } 
 /*
        }else{
        $data = array(
        "success" => 0,
        "message" => 'IDNO already exist!'
        );
        }  */

  }else{
  $data = array(
  "success" => 0,
  "message" => 'Please Complete All Required Fields!'
  );
  }

      generate_json($data);
     
  }


  public function delete_voucher($id= ""){


    $token_session = $this->session->userdata('token_session');
    $token = en_dec('en', $token_session);

    $this->isLoggedIn();
    if ($this->loginstate->get_access()['voucher_list']['delete'] == 1){
            // start - data to be used for views
          $data_admin = array(
          'token' => $token,
          'type'  => 'New User',
      );


      $query = $this->Model_list_vouchers->voucher_delete($id);
        
      if ($query) {
        $data = array("success" => 1, 'message' => "Record Deleted successfully!");
        $this->audittrail->logActivity('Voucher', "Voucher Deleted successfully.", 'add', $this->session->userdata('username'));
      }else{
        $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
      }
          
      generate_json($data); 
  

    }


  
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

 // die($record_status.' <br>'.$disable_id.'<br>'.$record_text);
 


      $query = $this->Model_list_vouchers->disable_modal_confirm($disable_id, $record_status);
      
   
      if ($query) {
        $data = array("success" => 1, 'message' => "Voucher  $record_text  successfully!");
        $this->audittrail->logActivity('Voucher', "Voucher  $record_text  successfully.", 'Voucher', $this->session->userdata('username'));
      }else{
        $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
      }
      
   
  generate_json($data);





}

}