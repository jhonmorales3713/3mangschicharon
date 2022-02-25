<?php
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) //to ignore maximum time limit
{
    @set_time_limit(0);
}
class Model_settings_void_record extends CI_Model {
	public $app_db;

	public function getOrders($reference_num){
		$query = "SELECT * FROM (
			SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount
			FROM `app_order_details` as a
			WHERE a.payment_status = 0 AND a.status = 1
			UNION ALL
			SELECT b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount
			FROM `app_sales_order_details` as b
			LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
			LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
			WHERE b.status = 1 AND d.sys_shop = b.sys_shop AND b.status = 1) as u
			WHERE u.reference_num = ?";
		$params = array($reference_num);

		return $this->db->query($query, $params);
	}

	public function get_sys_shop($user_id){
		$sql=" SELECT sys_shop FROM app_members WHERE sys_user = ? AND status = 1";
		$sql = $this->db->query($sql, $user_id);

        if($sql->num_rows() > 0){
            return $sql->row()->sys_shop;
        }else{
            return "";
        }
    }

	public function get_branchname($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db->query($sql, $data);

        if($result->num_rows() > 0){
            $branchname = $result->row()->branchname;
        }else{
            $branchname = 'Main';
        }
        return $branchname;
	}

	public function get_branchname_id($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db->query($sql, $data);

        if($result->num_rows() > 0){
            $branchid = $result->row()->branchid;
        }else{
            $branchid = 0;
        }
        return $branchid;
	}

	public function voidOrder_paid($reference_num, $sys_shop, $username, $reason) {

		$sql = "SELECT * FROM `app_sales_order_details` WHERE reference_num = ? AND sys_shop = ? AND status = 1";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);

		$details = $this->db->query($sql, $bind_data);
		$sales_order_id = $details->row()->id;

		$sql = "UPDATE `app_sales_order_details` SET `status` = 2 WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_order_details_shipping` SET `status` = 2 WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_sales_order_logs` SET `status` = 2 WHERE order_id = ?";
		$bind_data = array(
			$sales_order_id
		);
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_sales_order_details_rider` SET `status` = 2 WHERE app_sales_order_id = ?";
		$bind_data = array(
			$sales_order_id
		);
		$this->db->query($sql, $bind_data);

		///replenish inventory trans
		$sql = "SELECT * FROM `app_sales_order_logs` WHERE order_id = ?";
		$bind_data = array(
			$sales_order_id
		);
		$list_item = $this->db->query($sql, $bind_data)->result_array();
		$branch_id = $this->get_branchname_id($reference_num, $sys_shop);

		foreach($list_item as $row){
			$this->replenishInventory($sys_shop, $branch_id, $row['product_id'], $row['quantity']);
		}

		$sql="INSERT INTO sys_void_record (reference_num, f_id, username, type, reason, date_created, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array(
			$reference_num,
			$sales_order_id,
			$username,
			'Order List',
			$reason,
			date('Y-m-d H:i:s'),
			1
		);

		return $this->db->query($sql, $bind_data);
	}

	public function voidOrder_unpaid($reference_num, $username, $reason) {

		$sql = "SELECT * FROM `app_order_details` WHERE reference_num = ? AND status = 1";
		$bind_data = array(
			$reference_num
		);

		$details = $this->db->query($sql, $bind_data);
		$order_id = $details->row()->order_id;

		$sql = "UPDATE `app_order_details` SET `status` = 2 WHERE reference_num = ?";
		$bind_data = array(
			$reference_num
		);

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_order_logs` SET `status` = 2 WHERE order_id = ?";
		$bind_data = array(
			$order_id
		);

		$this->db->query($sql, $bind_data);

		$sql="INSERT INTO sys_void_record (reference_num, f_id, username, type, reason, date_created, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array(
			$reference_num,
			$order_id,
			$username,
			'Order List',
			$reason,
			date('Y-m-d H:i:s'),
			1
		);

		return $this->db->query($sql, $bind_data);
	}

	public function order_table($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);
		$reference_num 			= $this->input->post('reference_num');

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_ordered',
            1 => 'shopcode',
            2 => 'reference_num',
            3 => 'name',
            6 => 'total_amount',
            7 => 'payment_status',
            8 => 'order_status',
		);

		$sql = "SELECT * FROM (
			SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount
			FROM `app_order_details` as a
			WHERE a.payment_status = 0 AND a.status = 1
			UNION ALL
			SELECT b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount
			FROM `app_sales_order_details` as b
			LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
			LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
			WHERE b.status = 1 AND d.sys_shop = b.sys_shop AND b.status = 1) as u
			WHERE u.reference_num = ".$this->db->escape($reference_num)."";

		if($sys_shop != 0){
			$sql .= "AND u.sys_shop = ".$this->db->escape($sys_shop)."";
		}

		// getting records as per search parameters

        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
			$nestedData[] = $row["date_ordered"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
            $nestedData[] = number_format($row["total_amount"],2);
            $nestedData[] = number_format($row["delivery_amount"],2);
            $nestedData[] = number_format($row["total_amount"]+$row["delivery_amount"],2);

            if($row["payment_status"] == 1) {
                $nestedData[] = "<label class='badge badge-success'> Paid</label>";
            }
            else {
                $nestedData[] = "<label class='badge badge-info'> Pending</label>";
			}

			switch ($row["order_status"]) {
				case 'p':
				$nestedData[] = "<label class='badge badge-warning'> Ready for Processing</label>";
				break;
				case 'po':
				$nestedData[] = "<label class='badge badge-warning'> Processing Order</label>";
				break;
				case 'rp':
				$nestedData[] = "<label class='badge badge-warning'> Ready for Pickup</label>";
				break;
				case 'bc':
				$nestedData[] = "<label class='badge badge-warning'> Booking Confirmed</label>";
				break;
				case 'f':
				$nestedData[] = "<label class='badge badge-success'> Fulfilled</label>";
				break;
				case 's':
				$nestedData[] = "<label class='badge badge-success'> Shipped</label>";
				break;
				default:
				$nestedData[] = "<label class='badge badge-warning'> Ready for Processing</label>";
				break;
			}

            $nestedData[] = $row["shopname"];

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
            $nestedData[] = $branchname;

            if($sys_shop == 0) {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item"  href="'.base_url('settings/void_record/Settings_void_record/void_order_view/'.$token.'/'.$row['sys_shop'].'-'.$row["reference_num"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }
            else {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item" href="'.base_url('settings/void_record/Settings_void_record/void_order_view/'.$token.'/'.$row["reference_num"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
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

	public function void_record_table($sys_shop, $exportable = false){

		$token_session  = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		// $_name;
		// $status;
		// $date_from;
		// $date_to;
		// $requestData;

		//when not export
		if(!$exportable){
			// storing  request (ie, get/post) global array to a variable
			$_name 			= $this->input->post('_name');
			$status 		= $this->input->post('status');
			$date_from 		= format_date_reverse_dash($this->input->post('date_from'));
			$date_to 		= format_date_reverse_dash($this->input->post('date_to'));
			$order_status_view = $this->input->post('order_status_view');
			$requestData = $_REQUEST;
		}
		else{
			$filters = json_decode($this->input->post('_filters'));
			$_name 			= $filters->_name;
			$status 		= $filters->status;
			$date_from 		= format_date_reverse_dash($filters->date_from);
			$date_to 		= format_date_reverse_dash($filters->date_to);
			$requestData 	= url_decode(json_decode($this->input->post("_search")));
		}

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_created',
            1 => 'f_id',
            2 => 'reference_num',
            3 => 'type',
            4 => 'reason',
            5 => 'username'
		);


		$sql = "SELECT a.id, a.reference_num, a.f_id, a.username, a.type, a.reason, a.date_created, a.status FROM sys_void_record AS a
			LEFT JOIN app_sales_order_details AS b ON a.f_id = b.id OR b.reference_num = a.reference_num
			WHERE a.status = 1 AND DATE(a.date_created) BETWEEN ".$this->db->escape($date_from)." AND ".$this->db->escape($date_to)."
			";

		// getting records as per search parameters
		if($_name != ""){
			$sql.=" AND a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%'  ";
		}

		if($sys_shop != 0) {
			$sql.=" AND b.sys_shop = ".$this->db->escape($sys_shop)." ";
		}

		if($status != ""){
			$sql.=" AND a.type = ".$this->db->escape($status)." ";
		}

		$sql .= " GROUP BY a.f_id, a.reference_num";

        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];

		//when exportable limit is removed
		if(!$exportable){
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		}

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
			$nestedData[] = $row["date_created"];
			$nestedData[] = $row["f_id"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = $row["type"];
			$nestedData[] = $row["reason"];
			$nestedData[] = $row["username"];
			if ($sys_shop == 0) {
				$nestedData[] = '<a class="btn btn-primary" href="'.base_url('Settings_void_record/order_details_view/'.$token.'/'.$sys_shop.'-'.$row['reference_num']).'/'.$order_status_view.'" target="_blank">View</a>';
			} else {
				$nestedData[] = '<a class="btn btn-primary" href="'.base_url('Settings_void_record/order_details_view/'.$token.'/'.$row['reference_num']).'/'.$order_status_view.'" target="_blank">View</a>';
			}

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data,   // total data array
		);

		return $json_data;
	}

	public function void_details($ref_num)
	{
		$sql = "SELECT
			a.*,
			b.`id` as bid,
			b.`paypanda_ref`,
			b.`name`,
			b.`conno`,
			b.`address`,
			b.`total_amount`,
			b.`srp_totalamount` AS `srp`,
			b.`payment_method`,
			b.`payment_date`,
			b.`date_ordered`,
			b.`date_confirmed`,
			b.`date_shipped`
		FROM
			sys_void_record AS a
			LEFT JOIN app_sales_order_details AS b
			ON a.f_id = b.id
			OR b.`reference_num` = a.`reference_num`
		WHERE a.status = 1
			AND a.`reference_num` = ?
		GROUP BY a.`f_id`,
			b.`reference_num`
		ORDER BY a.id,
			date_created ASC";

	  return $this->db->query($sql, $ref_num);
	}

  public function get_prepayment_logs($refnum){
    $refnum = $this->db->escape($refnum);
    $sql = "SELECT * FROM sys_shops_wallet_logs WHERE tran_ref_num = $refnum AND enabled = 1";
    return $this->db->query($sql);
  }

  public function update_prepayment($id,$amount,$refnum,$type){
    $id = $this->db->escape($id);
    $amount = $this->db->escape($amount);
    $refnum = $this->db->escape($refnum);
    $type = $this->db->escape($type);
    $sql = "UPDATE sys_shops_wallet_logs SET enabled = 0
      WHERE id = $id";
    $this->db->query($sql);
    $disabled = $this->db->affected_rows();

    if($disabled > 0){
      $operator = ($type == "plus") ? "-" : "+";
      $sql3 = "UPDATE sys_shops_wallet_logs SET balance = (balance $operator $amount) WHERE id > $id";
      $this->db->query($sql3);

      $sql2 = "UPDATE sys_shops_wallet SET balance = (balance $operator $amount)
        WHERE refnum = $refnum";
      $this->db->query($sql2);
      return ($this->db->affected_rows() > 0) ? true : true;
    }


  }

  public function set_prepayment_voidrecord($data){
    $this->db->insert('sys_void_record',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function replenishInventory($sys_shop, $branch_id, $product_id, $qty){

	$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
	$bind_data = array(
		$branch_id,
		$product_id,
		$qty,
		'Replenish Inventory from Void Order',
		$this->session->userdata('username'),
		date('Y-m-d H:i:s'),
		1
	);

	$this->db->query($sql, $bind_data);

	$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND product_id = ? AND branchid = ? AND status = 1";
	$bind_data = array(
		$sys_shop,
		$product_id,
		$branch_id
	);

	$check_branch = $this->db->query($sql, $bind_data);

	$sql = "SELECT SUM(no_of_stocks) as current_qty FROM sys_products_invtrans_branch WHERE shopid = ? AND product_id = ? AND branchid = ? AND status = 1";
	$bind_data = array(
		$sys_shop,
		$product_id,
		$branch_id
	);

	$branch_invtrans = $this->db->query($sql, $bind_data);

	$current_qty     = $branch_invtrans->row()->current_qty;
	$current_qty     = (!empty($current_qty)) ? $current_qty : 0 ;
	$total_qty       = $qty + $current_qty;

	if($check_branch->num_rows() > 0){
		$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND product_id = ? AND branchid = ? AND status = 1";
		$bind_data = array(
			$total_qty,
			$sys_shop,
			$product_id,
			$branch_id

		);	
		$this->db->query($sql, $bind_data);
	}
	else{
		$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
		$bind_data = array(
			$sys_shop,
			$branch_id,
			$product_id,
			$total_qty,
			date('Y-m-d H:i:s'),
			1
		);	

		$this->db->query($sql, $bind_data);
	}

	/// update total stock of product
	$sql = "SELECT SUM(no_of_stocks) as grand_total_no_of_stocks FROM sys_products_invtrans_branch WHERE product_id = ? AND status = 1";
	$bind_data = array(
		$product_id
	);
	$grand_total_no_of_stocks = $this->db->query($sql, $bind_data);

	$sql = "SELECT SUM(a.no_of_stocks) as deleted_stocks FROM sys_products_invtrans_branch AS a
	LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
	WHERE a.product_id = ? AND a.status = 1 AND b.status IN (0, 2)";

	$bind_data = array(
		$product_id
	);
	$deleted_stocks = $this->db->query($sql, $bind_data)->row()->deleted_stocks;

	$grand_total_no_of_stocks = $grand_total_no_of_stocks->row()->grand_total_no_of_stocks;
	$grand_total              = $grand_total_no_of_stocks - abs($deleted_stocks);

	$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
	$bind_data = array(
		$grand_total,
		$product_id
	);

	$this->db->query($sql, $bind_data);
  }

  public function checkIfVoided($reference_num, $sys_shop){
	  $sql = "SELECT * FROM app_sales_order_details WHERE sys_shop = ? AND reference_num = ? AND status = 2";
	  $bind_data = array(
		$sys_shop,
		$reference_num
	  );

	  return $this->db->query($sql, $bind_data)->num_rows();
  }

  public function get_voided_ref_email($ref_num) {

			$query=" SELECT email, name FROM app_order_details
					WHERE reference_num = ?";

			$bind_data = array(
				$ref_num
			);

			return $this->db->query($query, $bind_data)->row_array();
	}

}
