<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_list_vouchers extends CI_Model {



  public function get_vouchers_list_json($filters,$requestData, $exportable = false){

    
   

    $db2  =  $this->load->database('vouchers',TRUE);
    $requestData = $_REQUEST;
    // $_voucher_code;
		// $_voucher_refnum; 
	  // $_shopname;      
    // $_record_status;
    // $_select_status;

  //  die();
    
    //when not export
		if(!$exportable){
			// storing  request (ie, get/post) global array to a variable			
      $_voucher_code	    = $this->input->post('_voucher_code');
      $_voucher_refnum	  = $this->input->post('_voucher_refnum');
      $_shopname	        = $this->input->post('_shopname');
      $_record_status	    = $this->input->post('_record_status');
      $_select_status	    = $this->input->post('_select_status');
			$requestData = $_REQUEST;
  
		}
		else{
     // 
      //on form export from controller			
      $filter = json_decode($this->input->post('_filter'));
      $_voucher_code	    = $this->input->post('_voucher_code');
      $_voucher_refnum	  = $this->input->post('_voucher_refnum');
      $_shopname	        = $this->input->post('_shopname');
      $_record_status	    = $this->input->post('_record_status');
      $_select_status	    = $this->input->post('_select_status');
			$requestData 	= url_decode(json_decode($this->input->post("_search")));
  //  echo"test";
		}

    $columns = array(
      0 => 'date_issue',
      1 => 'shopcode',
      2 => 'idno',
      3 => 'vrefno',
      4 => 'vcode',
      5 => 'vamount',
      6 => 'claim_status',
    );



    $shopid = $this->session->userdata('sys_shop_id');


   $sql = "SELECT A.id, A.`date_issue`, A.shopcode, A.idno, A.vrefno, A.vcode, A.vamount, A.shopid, A.status, A.claim_status, A.`date_valid`
          FROM `8_wallet_vouchers` AS A
          LEFT JOIN v_wallet_all AS B
          ON A.`vrefno` = B.`vrefno`
          LEFT JOIN v_wallet_available AS C
          ON A.`vrefno` = C.`vrefno`
          ";

    
      if ($_record_status == 1) {
      $sql.=" WHERE A.status = " . $this->db->escape($_record_status) . "";
      }else if ($_record_status == 2){
        $sql.=" WHERE A.status = " . $this->db->escape($_record_status) . "";
      }else{
        $sql.=" WHERE A.status > 0 ";
      }


      $tomorrow = date("Y-m-d");

  
       if($_select_status  ==  5){
          // print_r('expired');
           $sql.=" AND date(A.date_valid) < DATE_SUB(NOW(), INTERVAL 1 DAY) ";
       }else{
          // print_r('notexpired');
          if($_select_status != ""){
            $sql.=" AND A.claim_status = " . $this->db->escape_like_str($_select_status) . " ";
          }
       }
     
  

      if($_voucher_code != ""){
        $sql.=" AND A.vcode LIKE '%" . $this->db->escape_like_str($_voucher_code) . "%' ";
      }
      if($_voucher_refnum != ""){
        $sql.=" AND A.vrefno LIKE '%" . $this->db->escape_like_str($_voucher_refnum) . "%' ";
      }
      if($_shopname != ""){
        $sql.=" AND A.shopcode = " . $this->db->escape($_shopname) . "";
      }


    $query =  $db2->query($sql);          
    $totalData = $query->num_rows();
    $totalFiltered = $totalData; 

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
    
    //export 
    if(!$exportable){
     $sql .=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }
    

    $query =  $db2->query($sql);

    $data = array();
    
   
    foreach( $query->result_array() as $row )
    {
      
      $nestedData=array();


      $date=date_create($row['date_issue']);
      $date1=date_create($row['date_valid']);
      $nestedData[] = date_format($date,"m-d-Y");
      $nestedData[] = date_format($date1,"m-d-Y");
      $nestedData[] = $row['shopcode'];
      $nestedData[] = $row['idno'];
      $nestedData[] = $row['vrefno'];
      $nestedData[] = $row['vcode'];
      $nestedData[] = $row['vamount'];


      if($_select_status  ==  5  || $_select_status  ==  '' ){


        $date2 =    date("Y-m-d", strtotime($row['date_valid']));
        $tomorrow = date("Y-m-d");

  
                if ($date2 < $tomorrow) {
                  $status = 'Expired';
                  }else{
                    if ($row['claim_status'] == 1) {
                      $status = 'Available';
                    }else if ($row['claim_status'] == 2) {
                      $status = 'Encoded';
                    }else if ($row['claim_status'] == 3) {
                      $status = 'Claimed';
                    }else if ($row['claim_status'] == 4) {
                      $status = 'Reclaimed';
                    }
        
                }

      }else{

                  if ($row['claim_status'] == 1) {
                    $status = 'Available';
                  }else if ($row['claim_status'] == 2) {
                    $status = 'Encoded';
                  }else if ($row['claim_status'] == 3) {
                    $status = 'Claimed';
                  }else if ($row['claim_status'] == 4) {
                    $status = 'Reclaimed';
                  }

      }
     
    

      if ($row['status'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['status'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

      $ci = get_instance();
      $ci->load->helper('url');


      $actions = '
      <div class="dropdown">
          <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
          <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
      ';
      if ($this->loginstate->get_access()['voucher_list']['update'] == 1 || $this->loginstate->get_access()['voucher_list']['view'] == 1) {
          $actions .= ' 
              <a class="dropdown-item" href="'.$ci->config->config['base_url'].'/voucher/edit_vouchers/'.$row['id'].'" data-value="'.$row['id'].'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
              <div class="dropdown-divider"></div>';
      }

      if ($this->loginstate->get_access()['voucher_list']['delete'] == 1) {
          $actions .= '
          <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
          <div class="dropdown-divider"></div>';
      }
      
      if ($this->loginstate->get_access()['voucher_list']['view'] == 1) {
          $actions .= '
          <a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
      }

      $actions .= '
          </div>
      </div>';
      
      $nestedData[] = $status;
      $nestedData[] = $actions;
      $data[] = $nestedData;



      
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_shop_options() {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    return $this->db->query($query)->result_array();
  }

  public function get_sys_shop_per_id(){
    $shopid = $this->session->userdata('sys_shop_id');
    $query="SELECT * FROM sys_shops WHERE status = 1 AND id = '$shopid'";
    return $this->db->query($query)->result_array();
  }



	public function vouchers_add($idno, $shopcode, $shopid, $vrefnum, $vcode, $vamount, $date_issue, $date_valid){
    $db2  =  $this->load->database('vouchers',TRUE);


		$sql = "INSERT INTO `8_wallet_vouchers` (`idno`, `shopcode`, `shopid`, `vrefno`, `vcode`, `vamount`, `date_issue`, `date_valid`,`status`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, '1')";

    $sql1 = "INSERT INTO `v_wallet_all` (`idno`,`shopcode`, `shopid`, `vrefno`, `vcode`, `vamount`, `date_issue`, `date_valid`,`status`)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, '1')";

    $sql2 = "INSERT INTO `v_wallet_available` (`idno`,`shopcode`, `shopid`, `vrefno`, `vcode`, `vamount`, `date_issue`, `date_valid`,`status`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, '1')";


  

		$data = array(
      $idno,
			$shopcode, 
			$shopid, 
			$vrefnum, 
			$vcode, 
			$vamount, 
      date('Y/m/d', strtotime($date_issue)), 
      date('Y/m/d', strtotime($date_valid))
		);


    $data1  = array(
      $idno,
			$shopcode, 
			$shopid, 
			$vrefnum, 
			$vcode, 
			$vamount, 
      date('Y/m/d', strtotime($date_issue)), 
      date('Y/m/d', strtotime($date_valid))
		);


    $data2  = array(
      $idno,
			$shopcode, 
			$shopid, 
			$vrefnum, 
			$vcode, 
			$vamount, 
      date('Y/m/d', strtotime($date_issue)), 
      date('Y/m/d', strtotime($date_valid))
		);


    if ($db2->query($sql, $data) &&  $db2->query($sql1, $data1) &&  $db2->query($sql2, $data2) ) {
			return 1;
		}else{
			return 0;
		}
	}
  
  public function edit_voucher($id){

    $db2  =  $this->load->database('vouchers',TRUE);
    $query = "SELECT A.`id`,A.`date_issue`,A.`shopcode`,A.`idno`,A.`vrefno`,A.`vcode`,A.`vamount`,A.`date_valid`,A.`shopid` 
    FROM 8_wallet_vouchers AS A
    LEFT JOIN v_wallet_all AS B
    ON A.`vrefno` = B.`vrefno`
    LEFT JOIN v_wallet_available AS C
    ON A.`vrefno` = C.`vrefno`
    WHERE A.id = $id
    ";
    return  $db2->query($query)->result_array();
  }



  public function vouchers_update_data($id, $idno, $shopcode, $shopid, $vrefnum, $vcode, $vamount, $date_issue, $date_valid){

    $db2  =  $this->load->database('vouchers',TRUE);
    
        
    // Update
			$sql = "UPDATE 8_wallet_vouchers SET `idno` = ?, `shopcode` = ?, `shopid` = ?, `vamount` = ?, `date_issue` = ?, `date_valid` = ? WHERE `vrefno` = ? AND `vcode` = ?";

			$data = array($idno, $shopcode, $shopid, $vamount, date('Y/m/d', strtotime($date_issue)),date('Y/m/d', strtotime($date_valid)), $vrefnum, $vcode);

      
    
      $sql1 = "UPDATE v_wallet_all SET  `idno` = ?, `shopcode` = ?, `shopid` = ?, `vamount` = ?, `date_issue` = ?, `date_valid` = ? WHERE `vrefno` = ? AND `vcode` = ?";

      $data1 = array($idno, $shopcode, $shopid, $vamount, date('Y/m/d', strtotime($date_issue)),date('Y/m/d', strtotime($date_valid)), $vrefnum, $vcode);



      $sql2 = "UPDATE v_wallet_available SET  `idno` = ?, `shopcode` = ?, `shopid` = ?,  `vamount` = ?, `date_issue` = ?, `date_valid` = ? WHERE `vrefno` = ? AND `vcode` = ?";

      $data2 = array($idno, $shopcode, $shopid, $vamount, date('Y/m/d', strtotime($date_issue)),date('Y/m/d', strtotime($date_valid)), $vrefnum, $vcode);

      
        if ( $db2->query($sql, $data)  &&  $db2->query($sql1, $data1)  && $db2->query($sql2, $data2) ) 
        {
          return 1;
        }else{
          return 0;
        }

    }

	

    
    public function voucher_delete($id)
    {
      
      $db2  =  $this->load->database('vouchers',TRUE);
    
        
    // Update
			$sql = "UPDATE 8_wallet_vouchers SET `status` = 0  WHERE `id` = ?";

			$data = array(  $id);
      
        if ($db2->query($sql, $data)) {
          return 1;
        }else{
          return 0;
        }

    }


    public function enabled_disabled($id){

      $db2  =  $this->load->database('vouchers',TRUE);
  
      // Update
        $sql = "UPDATE 8_wallet_vouchers SET `status` = ?  WHERE `id` = ?";
  
        $data = array( 2, $id);
  
        
          if ($db2->query($sql, $data)) {
            return 1;
          }else{
            return 0;
          }
        }


        //Checker

        function name_is_exist($idnopb){
          $db2  =  $this->load->database('vouchers',TRUE);
          $sql ="SELECT * FROM `8_wallet_vouchers` WHERE  `idno` = ? AND `status`IN  ('1,2')";
          $data = array($idnopb);
       
          $result = $db2->query($sql, $data)->num_rows();
          if($result > 0){
            return 1;
          }else{
            return 0;
          }
          
        }

        function vrefnum_is_exist($vrefnum){
          $db2  =  $this->load->database('vouchers',TRUE);
          $sql ="SELECT * FROM `8_wallet_vouchers` WHERE  vrefno = ? AND `status`IN ('1,2')";
          $data = array($vrefnum);
       
          $result = $db2->query($sql, $data)->num_rows();
          if($result > 0){
            return 1;
          }else{
            return 0;
          }

        }

        function vcode_is_exist($vcode){
          $db2  =  $this->load->database('vouchers',TRUE);
          $sql ="SELECT * FROM `8_wallet_vouchers` WHERE  `vcode` = ? AND `status` IN ('1,2')";
          $data = array($vcode);
       
          $result = $db2->query($sql, $data)->num_rows();
          if($result > 0){
            return 1;
          }else{
            return 0;
          }

        }


        function disable_modal_confirm($disable_id, $record_status){


          $db2  =  $this->load->database('vouchers',TRUE);
          $sql="UPDATE `8_wallet_vouchers` SET `status` = ? WHERE `id` = ?";
          $data = array($record_status, $disable_id);

        //  die($db2->query($sql, $data));
         
          if ($db2->query($sql, $data)) {
            return 1;
          }else{
            return 0;
          }
        }


        public function get_voucher_details($id){

          $db2  =  $this->load->database('vouchers',TRUE);
          $query = "SELECT A.id,A.`date_issue`,A.shopcode,A.idno,A.vrefno,A.vcode,A.vamount,A.date_valid,A.shopid  
          FROM 8_wallet_vouchers AS A
          LEFT JOIN v_wallet_all AS B
          ON A.`vrefno` = B.`vrefno`
          LEFT JOIN v_wallet_available AS C
          ON A.`vrefno` = C.`vrefno`
          WHERE A.id = $id
          ";
          return  $db2->query($query);
        }


  }
  





