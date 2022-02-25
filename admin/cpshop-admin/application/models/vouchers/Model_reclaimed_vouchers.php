<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_reclaimed_vouchers extends CI_Model {


  	//products  Verified

	public function reclaimed_vouchers_table($requestData)
	{
		// storing  request (ie, get/post) global array to a variable
		$_record_status = $this->input->post('_record_status');
		$_vcode 		  	= $this->input->post('_vcode');
		$_order_ref 		= $this->input->post('_order_ref');
    $date_from 	  	= $this->input->post('date_from');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);

		$columns = array(
			// datatable column index  => database column name for sorting
			0 => 'reclaimed_date',
			1 => 'voucher_code',
			2 => 'name',
			3 => 'order_ref',
			4 => 'order_date',
			5 => 'email',
			6 => 'mobile',
			7 => 'reason',
			8 => 'status'
		);


    $custom_query = "";

    $custom_query .="WHERE b.status != 0";

    if( $date_from != ""){
      $custom_query.=" AND DATE_FORMAT(a.`trans_date`, '%m/%d/%Y') ='".$date_from. "'";
    }


    if($_vcode != ""){
      $custom_query.=" AND a.voucher_code  LIKE '%" . $this->db->escape_like_str($_vcode) . "%' ";
    }


    if($_order_ref != ""){
      $custom_query.=" AND a.order_refnum LIKE '%" . $this->db->escape_like_str($_order_ref) . "%' ";
    }

    if($_record_status != ""){
      $custom_query.=" AND a.status = '" . $this->db->escape_like_str($_record_status) . "' ";
    }

		$sql = "SELECT a.*,
                   DATE(a.trans_date) as trandate,
                   DATE(b.date_ordered) as date_ordered 
                  FROM sys_reclaimed_vouchers AS a
                  LEFT JOIN app_order_details AS b
                  ON a.`order_refnum` = b.`reference_num`
                  ". $custom_query ."
                  ";




    
		$query = $this->db->query($sql);

    $totalData = $query->num_rows();
		$totalFiltered = $totalData;
	
		$sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";


		$data = array();
		foreach ($query->result_array() as $row) {  // preparing an array for table tbody
			$nestedData = array();
      $nestedData[] = $row["trandate"];
			$nestedData[] = en_dec_vouchers_concat('dec', $row["voucher_code"]) ;
			$nestedData[] = $row["name"];
      $nestedData[] = $row["order_refnum"];
      $nestedData[] = $row["date_ordered"];
      $nestedData[] = $row["email"];
      $nestedData[] = $row["mobile"];
      $nestedData[] = '<textarea disabled readonly class = "form-control" rows = "4" cols = "100">'.$row["reason"].'</textarea>';


      if($row['status'] == 0){
          $status = "Declined";
       }elseif($row['status'] == 1){
          $status = "Approved";
        }else{
          $status = "Pending";
        }

      $nestedData[] = $status;

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval($totalData),  // total number of records
			"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}



	public function reclaimed_vouchers_table_export($requestData)
  {


          $_record_status = $this->input->post('_record_status_export');
          $_vcode 		  	= $this->input->post('_vcode_export');
          $_order_ref 		= $this->input->post('_order_ref_export');
          $date_from 	  	= $this->input->post('date_from_export');
          $token_session  = $this->session->userdata('token_session');
          $token          = en_dec('en', $token_session);

          $columns = array(
            // datatable column index  => database column name for sorting
            0 => 'reclaimed_date',
            1 => 'voucher_code',
            2 => 'name',
            3 => 'order_ref',
            4 => 'order_date',
            5 => 'email',
            6 => 'mobile',
            7 => 'reason',
            8 => 'status'
          );

          $custom_query = "";

          $custom_query .="WHERE b.status != 0";

          if( $date_from != ""){
            $custom_query.=" AND DATE_FORMAT(a.`trans_date`, '%m/%d/%Y') ='".$date_from. "'";
          }


          if($_vcode != ""){
            $custom_query.=" AND a.voucher_code  LIKE '%" . $this->db->escape_like_str($_vcode) . "%' ";
          }


          if($_order_ref != ""){
            $custom_query.=" AND a.order_refnum LIKE '%" . $this->db->escape_like_str($_order_ref) . "%' ";
          }

          if($_record_status != ""){
            $custom_query.=" AND a.status = '" . $this->db->escape_like_str($_record_status) . "' ";
          }

          $sql = "SELECT a.*,
                        DATE(a.trans_date) as trandate,
                        DATE(b.date_ordered) as date_ordered 
                  FROM sys_reclaimed_vouchers AS a
                  LEFT JOIN app_order_details AS b
                  ON a.`order_refnum` = b.`reference_num`
                  ". $custom_query ."
                  ";

          $query = $this->db->query($sql);

        // return $query;

      foreach ($query->result_array() as $row) {  // preparing an array for table tbody
      $nestedData = array();
      $nestedData['trandate']  = $row["trandate"];
      $nestedData['voucher_code']  = en_dec_vouchers_concat('dec', $row["voucher_code"]) ;
      $nestedData['name']  = $row["name"];
      $nestedData['order_refnum']  = $row["order_refnum"];
      $nestedData['date_ordered']  = $row["date_ordered"];
      $nestedData['email']  = $row["email"];
      $nestedData['mobile']  = $row["mobile"];
      $nestedData['reason']  = $row["reason"];


      if($row['status'] == 0){
          $status = "Declined";
       }elseif($row['status'] == 1){
          $status = "Approved";
        }else{
          $status = "Pending";
        }

      $nestedData['status'] = $status;

      $data[] = $nestedData;
    } 

  

    return $data;

    }


  }
  





