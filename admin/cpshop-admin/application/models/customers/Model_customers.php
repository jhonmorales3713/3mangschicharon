<?php
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) { //to ignore maximum time limit
    @set_time_limit(0);
}

class Model_customers extends CI_Model {

  public function get_customer_history($id,$userid) {
		if($userid == 0) {
			$query=" SELECT 1 as count, total_amount as amount, payment_status FROM app_order_details WHERE order_id = ? ";
			return $this->db->query($query, $id)->row_array();
		} else {
			$query=" SELECT count(order_id) as count, SUM(total_amount) as amount, payment_status FROM app_order_details WHERE user_id = ? AND payment_status = 1";
			return $this->db->query($query, $userid)->row_array();
		}
	}

  public function get_customers($_type, $_name, $_city, $requestData, $exportable = false){

		$columns = array(
		// datatable column index  => database column name for sorting
			0 => 'name',
			1 => 'city',
			2 => 'city',
			3 => 'userid',
		);

		$sql = "SELECT * FROM (SELECT a.id as id, CONCAT(a.first_name,' ', a.last_name) as name, b.name as city, a.status as status, a.user_id as userid,
            '' as shop_ids
            FROM app_customers as a
            LEFT JOIN sys_delivery_areas as b ON a.areaid = b.id
            UNION
            SELECT c.order_id as id, c.name as name, d.name as city, 1 as status, c.user_id as userid,
            @shop_ids := (SELECT GROUP_CONCAT(sys_shop SEPARATOR ', ') FROM app_sales_order_details WHERE reference_num = c.reference_num AND status = 1) as shop_ids
            FROM app_order_details as c
            LEFT JOIN sys_delivery_areas as d ON c.areaid = d.id
            WHERE c.user_id = 0 ) as e
            WHERE e.status = 1 ";

		// getting records as per search parameters
		if($_type != ""){
			if($_type == 2){
                $sql .= " AND e.userid != 0 ";
            } else if($_type == 3){
                $sql .= " AND e.userid = 0 ";
            }
        }
        if($_name != ""){
            $sql .= " AND e.name LIKE '%".$_name."%'";
        }
        if($_city != ""){
			$sql .= " AND e.city LIKE '%".$_city."%'";
		}

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";
    if (!$exportable) {
      $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {
            $history = $this->get_customer_history($row["id"],$row["userid"]);

            $spent = "0.00";
            // if($history["payment_status"] == 1){
            $spent = number_format($history["amount"],2);
            // }

            
            $nestedData = array();
            $nestedData[] = $row["name"];
            $nestedData[] = $row["city"];
            $nestedData[] = $history["count"].' order(s) with '.$spent.' spent';

            if($row["userid"] != 0 ) {
                $nestedData[] = (!$exportable) ? "<label class='badge badge-success'> Verified</label>":"Verified";
            }else{
                $nestedData[] = (!$exportable) ? "<label class='badge badge-info'> Guest</label>":"Guest";
            }
            if($row['userid'] != 0){
                $nestedData[] =
                '
                <div class="dropdown text-center">
                    <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                    <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
                        <a class="dropdown-item btn_history"
                          data-id="'.en_dec('en', $row['userid']).'"
                          data-total_amount = "'.$history["amount"].'"
                          data-name = "'.$row['name'].'"
                        >
                          <i class="fa fa-ye" aria-hidden="true"></i> Order History
                        </a>
                        <a class="dropdown-item btn_login_history"
                            data-id = "'.en_dec('en',$row['userid']).'"
                        >
                            Login History
                        </a>
                    </div>
                </div>
                ';
            }else{
                $nestedData[] = '<center>---</center>';
            }

            $data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
    }

    public function add_customer($_fname, $_lname, $_birthdate, $_gender, $_mobile, $_email, $_address1, $_address2, $_city) {

		$sql = "INSERT INTO app_customers (`first_name`, `last_name`, `conno`, `email`, `address1`, `address2`, `areaid`, `birthdate`, `gender`, `created`, `updated`, `status`)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ";

        $data = array($_fname, $_lname, $_mobile, $_email, $_address1, $_address2, $_city, $_birthdate, $_gender, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 1);

		return $this->db->query($sql, $data);
	}

  public function get_order_history($search,$user_id,$total_amount){
      $requestData = $_REQUEST;
      $user_id = $this->db->escape($user_id);
      $columns = array(
        0 => 'reference_num',
        1 => 'referral_code',
        2 => 'Address',
        3 => 'b.total_amount',
        4 => 'c.delivery_amount',
        5 => 'date_ordered',
        6 => 'date_shipped',
        7 => 'b.payment_status'
      );


      $sql = "SELECT a.*, b.referral_code, c.delivery_amount
      FROM app_sales_order_details a
      LEFT JOIN app_referral_codes b ON a.reference_num = b.order_reference_num
      LEFT JOIN app_order_details_shipping c ON a.reference_num = c.reference_num AND a.sys_shop =  c.sys_shop
      WHERE a.user_id = $user_id";

      $query = $this->db->query($sql);
      $totalData = $query->num_rows();
      $totalFiltered = $totalData;

      $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

      $query = $this->db->query($sql);

      $data = array();

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();
        $status = '';
        switch ($row['payment_status']) {
          case 1:
            $status = "<label class='badge badge-success text-center' style = 'width:53px;'> Paid</label>";
            break;
          case 2:
            $status = "<label class='badge badge-danger text-center' style = 'width:53px;'> Un Paid</label>";
            break;
          default:
          $status = "<label class='badge badge-warning text-center' style = 'width:53px;'>Processing</label>";
            break;
        }
        $nestedData[] = $row['reference_num'];
        $nestedData[] = $row['referral_code'];
        $nestedData[] = $row['address'];
        $nestedData[] = $row['total_amount'];
        $nestedData[] = $row['delivery_amount'];
        $nestedData[] = $row['date_ordered'];
        $nestedData[] = $row['date_shipped'];
        $nestedData[] = $status;

        $data[] = $nestedData;
      }
      $json_data = array(

        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "data"            => $data
      );

      return $json_data;
    }

    public function get_login_history($user_id){
      $requestData = $_REQUEST;
      $user_id = $this->db->escape($user_id);
      $columns = array(
        0 => 'name',
        1 => 'action',
        2 => 'created',
      );


      $sql = "SELECT a.*, CONCAT(b.first_name,' ',b.last_name) as name
        FROM sys_customer_auth_audittrail a
        INNER JOIN app_customers b ON a.user_id = b.user_id
        WHERE a.user_id = $user_id";

      $query = $this->db->query($sql);
      $totalData = $query->num_rows();
      $totalFiltered = $totalData;

      $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

      $query = $this->db->query($sql);

      $data = array();

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();
        $status = '';
        switch ($row['action']) {
          case 'fb_login':
            $status = "<center><label class='badge badge-primary text-center' style = 'width:53px;'>Facebook</label></center>";
            break;
          case 'gmail_login':
            $status = "<center><label class='badge badge-danger text-center' style = 'width:53px;'>Google</label></center>";
            break;
          case 'login':
            $status = "<center><label class='badge badge-success text-center' style = 'width:53px;'>Login</label></center>";
            break;
          default:
          $status = "<center><label class='badge badge-dark text-center' style = 'width:53px;'>Logout</label></center>";
            break;
        }

        $nestedData[] = $row['name'];
        $nestedData[] = $status;
        $nestedData[] = $row['created'];

        $data[] = $nestedData;
      }
      $json_data = array(

        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "data"            => $data
      );

      return $json_data;
    }


}
