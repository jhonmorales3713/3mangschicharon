<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_manual_order extends CI_Model {
  public function list_table($search, $token, $requestData, $exportable = false){

    $columns = array(
      0 => 'date_ordered',
      1 => 'reference_num',
      2 => 'name',
      3 => 'amount',
      4 => 'voucher_amount',
      5 => 'delivery_amount',
      6 => 'total_amount',
      7 => 'payment_status',
      8 => 'order_status',
      9 => 'branch_name'
    );


    $sql = "SELECT a.*, b.shopname, (a.total_amount + a.delivery_amount) as total_amount, a.total_amount as amount,
      @branch_name := (SELECT branchname FROM sys_branch_profile WHERE id = a.branch_id AND status = 1) as branch_name,
      @voucher_amount := (SELECT amount FROM app_order_payment WHERE order_ref_num = a.reference_num AND shopid = b.id AND status = 1 AND payment_type = 'toktokmall') as voucher_amount,
      @city := (SELECT citymunDesc FROM sys_citymun WHERE citymunCode = a.citymunCode AND status = 1) as city
      FROM app_manual_order_details a
      LEFT JOIN sys_shops b ON a.sys_shop = b.id
      WHERE b.status = 1 AND a.status = 1";

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND a.sys_shop = $shopid";
    }

    if($search->address != ''){
      $address = $this->db->escape('%'.$search->address.'%');
      $sql .= " AND a.address LIKE $address";
    }

    if($search->region != ''){
      $region = $this->db->escape($search->region);
      $sql .= " AND a.regCode = $region";
    }

    if($search->province != ''){
      $province = $this->db->escape($search->province);
      $sql .= " AND a.provCode = $province";
    }

    if($search->citymun != ''){
      $citymun = $this->db->escape($search->citymun);
      $sql .= " AND a.citymunCode = $citymun";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND ((DATE(a.date_ordered) BETWEEN $from AND $to) OR (DATE(a.date_shipped) BETWEEN $from AND $to))";
    }

    if($this->loginstate->get_access()['seller_access'] == 1 && $this->session->sys_shop_id != ''){
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.sys_shop = $sys_shop_id";
    }

    if(($this->loginstate->get_access()['seller_branch_access'] == 1 || $this->loginstate->get_access()['food_hub_access'] == 1) && $this->session->sys_shop_id != ''){
      $branchid = $this->db->escape($this->session->branchid);
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.branch_id = $branchid AND a.sys_shop = $sys_shop_id";
    }

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    if (!$exportable) {
      $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $total_amount = ($row['amount'] + $row['voucher_amount'] + $row['delivery_amount']);
      $nestedData[] = $row['date_ordered'];
      $nestedData[] = $row['reference_num'];
      $nestedData[] = $row['name'];
      $nestedData[] = number_format($row['amount'],2);
      $nestedData[] = number_format($row['voucher_amount'],2);
      $nestedData[] = number_format($row['delivery_amount'],2);
      $nestedData[] = number_format($row['total_amount'],2);
      $nestedData[] = '<center><label class="badge badge-success">Paid</label></center>';
      $nestedData[] = '<center><label class="badge badge-success">Shipped</label></center>';
      $nestedData[] = $row['shopname'];
      $nestedData[] = ($row['branch_name'] != null) ? $row['branch_name'] : 'Main';
      $nestedData[] = $row['city'];
      $nestedData[] =
      '
        <div class="dropdown">
          <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
          <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item" href="'.base_url('orders/Manual_order/manual_orders_view/'.$token.'/'.$row['sys_shop'].'-'.$row["reference_num"]).'/all"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
          </div>
        </div>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_shop_options($id = false) {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    if($id){
      $id = $this->db->escape($id);
      $query .= " AND id = $id";
    }
    return $this->db->query($query)->result_array();
  }

  public function get_shop($id = false) {
    $id = $this->db->escape($id);
    $query="SELECT * FROM sys_shops WHERE status = 1 AND id = $id";
    return $this->db->query($query)->row_array();
  }

  public function get_shop_w_wallet(){
    $sql = "SELECT a.id, a.shopname, a.shopcode, a.shippingfee, a.daystoship
      FROM sys_shops a INNER JOIN sys_shops_wallet b ON a.id = b.shopid
      WHERE b.balance > 0 AND b.enabled = 1 AND a.status = 1 ORDER BY a.shopname ASC";
    return $this->db->query($sql);
  }

  public function get_shop_products($shopid,$branchid = false){
    $shopid = $this->db->escape($shopid);
    if(ini() == "toktokmall"){
      $sql = "SELECT a.*, b.no_of_stocks as nos, c.startup, c.jc, c.mcjr, c.mc, c.mcsuper, c.mcmega, c.others,
        @shop_rate := (SELECT rateamount FROM sys_shop_rate WHERE syshop = a.sys_shop AND status = 1) as shop_rate,
        @parent_name := (SELECT itemname FROM sys_products WHERE Id = a.parent_product_id) as parent_name
        FROM sys_products a
        INNER JOIN sys_products_invtrans_branch b ON a.Id = b.product_id
        LEFT JOIN `8_referralcom_rate_shops` c ON a.sys_shop = c.shopid
        WHERE a.sys_shop = $shopid AND a.enabled = 1 AND b.status = 1
        AND (b.no_of_stocks > 0 OR a.cont_selling_isset)";
    }else{
      $sql = "SELECT a.*, b.no_of_stocks as nos
            FROM sys_products a
            INNER JOIN sys_products_invtrans_branch b ON a.Id = b.product_id
            WHERE a.sys_shop = $shopid AND a.enabled = 1 AND b.status = 1
            AND (b.no_of_stocks > 0 OR a.cont_selling_isset)";
    }


    if($branchid){
      $branchid = $this->db->escape($branchid);
      $sql .= " AND b.branchid = $branchid";
    }else{
      $sql .= " AND b.branchid = 0";
    }

    $sql .= " GROUP BY Id ORDER BY a.itemname ASC";
    return $this->db->query($sql);
  }

  public function get_shop_branches($shopid,$branchid = false){
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT b.* FROM sys_branch_mainshop a
      INNER JOIN sys_branch_profile b ON a.branchid = b.id
      WHERE a.mainshopid = $shopid AND a.status = 1 AND b.status = 1";

    if($branchid){
      $branchid = $this->db->escape($branchid);
      $sql .= " AND b.id = $branchid";
    }

    $sql .= " ORDER BY branchname ASC";
    return $this->db->query($sql);
  }

  public function get_products($productid){
    $productid = $this->db->escape($productid);
    $sql = "SELECT a.no_of_stocks as quantity,
      CONCAT(a.itemname,' (',a.otherinfo,')') as product_name
      FROM sys_products a
      WHERE a.Id = $productid";
    return $this->db->query($sql);
  }

  public function get_product_branch($productid,$shop,$branch){
    $sql = "SELECT no_of_stocks FROM sys_products_invtrans_branch WHERE status = 1
     AND product_id = ? AND shopid = ? AND branchid = ?";
    $data = array($productid,$shop,$branch);
    return $this->db->query($sql,$data);
  }

  public function set_app_order_details($data){
    $this->db->insert('app_order_details',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function get_cities(){
    $sql = "SELECT a.*, CONCAT(a.citymunDesc,', ',b.provDesc) as city, b.regCode
      FROM sys_citymun a
      INNER JOIN sys_prov b ON a.provCode = b.provCode
      ORDER BY a.regDesc";
    return $this->db->query($sql);
  }

  public function set_app_order_details_shipping($data){
    $this->db->insert('app_order_details_shipping',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_app_manual_orders_shipping($data){
    $this->db->insert('app_manual_orders_shipping',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_app_order_logs_batch($data){
    $this->db->insert_batch('app_order_logs',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_app_sales_order_details($data){
    $this->db->insert('app_sales_order_details',$data);
    return $this->db->insert_id();
  }

  public function set_manual_order_details($data){
    $this->db->insert('app_manual_order_details',$data);
    return $this->db->insert_id();
  }

  public function set_app_sales_order_logs_batch($data){
    $this->db->insert_batch('app_sales_order_logs',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_app_manual_order_logs_batch($data){
    $this->db->insert_batch('app_manual_order_logs',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_branch_order($data){
    $this->db->insert('sys_branch_orders',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_sys_products_invtrans_batch($data){
    $this->db->insert_batch('sys_products_invtrans',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function so_no(){
		$this->db->select("order_id");
		$res = $this->db->get("app_order_details");
		return $res->num_rows();
	}

  public function update_prod_quantity($data){
    $this->db->update_batch('sys_products',$data,'Id');
  }

  public function update_sysproduct_invtrans_branch($data){
    $sql = '';
    // print_r($data);
    // die();
    foreach($data as $row){
      $quantity = $this->db->escape($row['no_of_stocks']);
      $product_id = $this->db->escape($row['Id']);
      $branchid = $this->db->escape($row['branchid']);
      $shopid = $this->db->escape($row['shopid']);
      $sql .= "UPDATE sys_products_invtrans_branch SET no_of_stocks = (no_of_stocks - $quantity)
        WHERE product_id = $product_id AND branchid = $branchid AND shopid = $shopid;";
    }
    $this->db->query($sql);
  }

  // MAIN ORDER VIEW
  public function get_app_order_details_date_ordered($reference_num) {
		$query=" SELECT date_ordered FROM app_manual_order_details WHERE reference_num = ?";
		$params = array($reference_num);
		return $this->db->query($query, $params)->row()->date_ordered;
	}

  public function get_prev_orders($reference_num, $date_ordered) {
		$data_array = array();

		$query = "SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, 0 as sys_shop
			FROM `app_manual_order_details` as a
			WHERE a.payment_status = 0 AND a.status = 1
			AND a. date_ordered > ?
			ORDER BY a.date_ordered ASC LIMIT 20";

		$params     = array($date_ordered);
		$query1_arr = $this->db->query($query, $params)->result_array();

		$query = "SELECT b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
			FROM `app_manual_order_details` as b
			WHERE b.status = 1 AND b.status = 1
			AND b. date_ordered > ?
			ORDER BY b.date_ordered ASC LIMIT 20";

		$params     = array($date_ordered);
		$query2_arr = $this->db->query($query, $params)->result_array();

		$data_array = array_merge($query1_arr, $query2_arr);

		usort($data_array, function($a, $b) {
			$ad = new DateTime($a['date_ordered']);
			$bd = new DateTime($b['date_ordered']);

			if($ad == $bd){
			  return 0;
			}

			return $ad < $bd ? -1 : 1;
		  });

		if(!empty($data_array[0]['date_ordered'])){
			return $data_array;
		}else{
			return 0;
		}

	}

  public function get_next_orders($reference_num, $date_ordered) {
		$data_array = array();

		$query = "SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, 0 as sys_shop
			FROM `app_manual_order_details` as a
			WHERE a.payment_status = 0 AND a.status = 1
			AND a. date_ordered < ?
			ORDER BY a.date_ordered DESC LIMIT 20";

		$params     = array($date_ordered);
		$query1_arr = $this->db->query($query, $params)->result_array();

		$query = "SELECT b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
			FROM `app_manual_order_details` as b
			WHERE b.status = 1 AND b.status = 1
			AND b. date_ordered < ?
			ORDER BY b.date_ordered DESC LIMIT 20";

		$params     = array($date_ordered);
		$query2_arr = $this->db->query($query, $params)->result_array();

		$data_array = array_merge($query1_arr, $query2_arr);

		usort($data_array, function($a, $b) {
			$ad = new DateTime($a['date_ordered']);
			$bd = new DateTime($b['date_ordered']);

			if($ad == $bd){
			  return 0;
			}

			return $ad > $bd ? -1 : 1;
		  });

		if(!empty($data_array[0]['date_ordered'])){
			return $data_array;
		}else{
			return 0;
		}

	}

  public function get_vouchers($reference_num) {
		$query=" SELECT amount as voucheramount, payment_refno as vouchercode FROM app_order_payment
		WHERE status = 1 AND payment_type = 'toktokmall' AND order_ref_num = ?";
		$params = array($reference_num);
		return $this->db->query($query, $params)->result_array();
	}

  public function get_vouchers_shop($reference_num, $sys_shop) {
		$query=" SELECT amount as voucheramount, payment_refno as vouchercode FROM app_order_payment
		WHERE status = 1 AND payment_type = 'toktokmall' AND order_ref_num = ? AND shopid = ?";
		$params = array($reference_num, $sys_shop);
		return $this->db->query($query, $params)->result_array();
	}

  public function get_app_order_details_date_ordered_shop($reference_num) {
		$query=" SELECT date_ordered FROM app_manual_order_details WHERE reference_num = ?";
		$params = array($reference_num);
		return $this->db->query($query, $params)->row()->date_ordered;
	}

  public function get_prev_orders_per_shop($reference_num, $sys_shop, $date_ordered) {
		$branchid = $this->session->userdata('branchid');

		if($branchid == 0){
			$data_array = array();

			$query="SELECT a.created as date_ordered, a.reference_num as reference_num, a.sys_shop as sys_shop
				FROM `app_manual_orders_shipping` as a
				WHERE a.sys_shop = ".$this->db->escape_str($sys_shop)."
				AND a.status = 1
				AND a.created > ?
				ORDER BY a.created ASC LIMIT 1";

			$params     = array($date_ordered);
			$data_array = $this->db->query($query, $params)->result_array();

			if(!empty($data_array[0]['date_ordered'])){
				return $data_array;
			}else{
				return 0;
			}
		}
		else{
			$data_array = array();

			$query="SELECT b.created as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
				FROM `sys_branch_orders` as a
				LEFT JOIN `app_manual_orders_shipping` as b ON a.orderid = b.reference_num
				WHERE b.sys_shop = ".$this->db->escape_str($sys_shop)."
				AND a.branchid = ".$this->db->escape_str($branchid)."
				AND a.status = 1
				AND b.status = 1
				AND b.created > ?
				ORDER BY b.created ASC LIMIT 1";

			$params     = array($date_ordered);
			$data_array = $this->db->query($query, $params)->result_array();

			if(!empty($data_array[0]['date_ordered'])){
				return $data_array;
			}else{
				return 0;
			}
		}

	}

  public function get_next_orders_per_shop($reference_num, $sys_shop, $date_ordered) {
		$branchid = $this->session->userdata('branchid');

		if($branchid == 0){
			$data_array = array();

			$query="SELECT a.created as date_ordered, a.reference_num as reference_num, a.sys_shop as sys_shop
				FROM `app_manual_orders_shipping` as a
				WHERE a.sys_shop = ".$this->db->escape_str($sys_shop)."
				AND a.status = 1
				AND a.created < ?
				ORDER BY a.created DESC LIMIT 1";

			$params     = array($date_ordered);
			$data_array = $this->db->query($query, $params)->result_array();

			if(!empty($data_array[0]['date_ordered'])){
				return $data_array;
			}else{
				return 0;
			}
		}
		else{
			$data_array = array();

			$query="SELECT b.created as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
				FROM `sys_branch_orders` as a
				LEFT JOIN `app_manual_orders_shipping` as b ON a.orderid = b.reference_num
				WHERE b.sys_shop = ".$this->db->escape_str($sys_shop)."
				AND a.branchid = ".$this->db->escape_str($branchid)."
				AND a.status = 1
				AND b.status = 1
				AND b.created < ?
				ORDER BY b.created DESC LIMIT 1";

			$params     = array($date_ordered);
			$data_array = $this->db->query($query, $params)->result_array();

			if(!empty($data_array[0]['date_ordered'])){
				return $data_array;
			}else{
				return 0;
			}
		}

	}

  public function orders_details($ref_num, $sys_shop) {
		//if shop = shopanda; not yet paid
		if($sys_shop == 0) {
			$query=" SELECT a.*, a.total_amount as totamt, a.order_status as orderstatus, a.delivery_amount as sf, a.payment_status as paystatus,
					s.daystoship as paid_daystoship, s.daystoship_to as paid_daystoship_to, a.id as app_sales_id, a.date_shipped as shipped_date, a.payment_notes as p_notes,
					rider.rider_name, rider.rider_platenum, rider.rider_conno, a.delivery_info as shippingpartner, a.delivery_ref_num as shipping_ref, a.delivery_notes as shipping_note,
					a.latitude as lati, a.longitude as longi, a.delivery_amount as shipping_cost
					FROM app_order_details as a
					LEFT JOIN `app_manual_orders_shipping` as s ON a.reference_num = s.reference_num
					LEFT JOIN `app_sales_order_details_rider` as rider ON a.id = rider.app_sales_order_id AND rider.status = 1
					WHERE a.reference_num = ?";

			$bind_data = array(
				$ref_num
			);
		}
		else {
			$query="SELECT * FROM (
					SELECT a.*, a.id as order_id, SUM(b.total_amount) as totamt, a.order_status as orderstatus, a.order_status as sales_order_status, s.delivery_amount as sf, a.payment_status as paystatus, a.date_shipped as dateshipped,
						s.daystoship as paid_daystoship, s.daystoship_to as paid_daystoship_to, shop.shopname as shopname, shop.address as pickup_address,
						rider.rider_name, rider.rider_platenum, rider.rider_conno, a.id as app_sales_id, a.date_shipped as shipped_date, a.payment_notes as p_notes,
						a.delivery_info as shippingpartner, a.delivery_ref_num as shipping_ref, a.delivery_notes as shipping_note,
						a.latitude as lati, a.longitude as longi, a.delivery_amount as shipping_cost
						FROM app_manual_order_details as a
						LEFT JOIN `app_manual_order_logs` as b ON a.reference_num = b.order_id
						LEFT JOIN `app_manual_orders_shipping` as s ON a.reference_num = s.reference_num
						LEFT JOIN `sys_shops` as shop ON a.sys_shop = shop.id
						LEFT JOIN `app_sales_order_details_rider` as rider ON a.id = rider.app_sales_order_id AND rider.status = 1
						WHERE a.reference_num = ? AND a.sys_shop = ? AND s.sys_shop = ? AND a.payment_status = 0
					UNION ALL
					SELECT a.*, a.id as order_id, SUM(b.total_amount) as totamt, a.order_status as orderstatus, a.order_status as sales_order_status, s.delivery_amount as sf, a.payment_status as paystatus, a.date_shipped as dateshipped,
						s.daystoship as paid_daystoship, s.daystoship_to as paid_daystoship_to, shop.shopname as shopname, shop.address as pickup_address,
						rider.rider_name, rider.rider_platenum, rider.rider_conno, a.id as app_sales_id, a.date_shipped as shipped_date, a.payment_notes as p_notes,
						a.delivery_info as shippingpartner, a.delivery_ref_num as shipping_ref, a.delivery_notes as shipping_note,
						a.latitude as lati, a.longitude as longi, a.delivery_amount as shipping_cost
						FROM app_manual_order_details as a
						LEFT JOIN `app_manual_order_logs` as b ON a.reference_num = b.order_id
						LEFT JOIN `app_manual_orders_shipping` as s ON a.reference_num = s.reference_num
						LEFT JOIN `sys_shops` as shop ON a.sys_shop = shop.id
						LEFT JOIN `app_sales_order_details_rider` as rider ON a.id = rider.app_sales_order_id AND rider.status = 1
						WHERE a.reference_num = ? AND a.sys_shop = ? AND a.sys_shop = ? AND a.payment_status = 1 ) as u WHERE u.sys_shop = ? ";

			$bind_data = array(
				$ref_num,
				$sys_shop,
				$sys_shop,
				$ref_num,
				$sys_shop,
				$sys_shop,
				$sys_shop
			);
		}

		return $this->db->query($query, $bind_data)->row_array();
	}

  public function get_referral_code($order_reference_number) {
		$query = "SELECT referral_code FROM app_referral_codes
				WHERE order_reference_num = ? ";

		$result = $this->db->query($query, $order_reference_number);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

  public function get_branchname_orders($orderid, $shopid){
      $sql="SELECT a.orderid, a.branchid, b.branchname, b.address FROM sys_branch_orders a
            LEFT JOIN sys_branch_profile b ON a.branchid = b.id
            LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
            WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
      $data = array(1, $orderid, $shopid);
      $result = $this->db->query($sql, $data);

      return $result;
  }

  function get_mainshopname($sys_shop){
        $sql="SELECT shopname FROM sys_shops WHERE status = ? AND id = ?";
        $data = array(1, $sys_shop);
        return $this->db->query($sql, $data);
	}

  public function orders_history($order_id) {
		$query = "SELECT * FROM app_order_history
		WHERE order_id = ? ORDER BY date_created DESC" ;

		$bind_data = array(
			$order_id,
		);

		return $this->db->query($query, $bind_data)->result_array();
	}

  public function orders_history_sales($order_id) {
		$query = "SELECT * FROM app_order_history
		WHERE order_id = ? ORDER BY date_created DESC" ;

		$bind_data = array(
			$order_id,
		);

		return $this->db->query($query, $bind_data)->result_array();
	}

  public function order_item_table($reference_num, $sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
			0 => 'id',
			1 => 'itemname',
			2 => 'qty',
			3 => 'amount',
			4 => 'total_amount',
		);

		if($sys_shop == 0) {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.amount as amount, a.total_amount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item
					FROM `app_manual_order_logs` a
					LEFT JOIN `app_manual_order_details` b on a.order_id = b.reference_num
					LEFT JOIN `sys_products` c on c.Id = a.product_id
					LEFT JOIN `sys_shops` d on c.sys_shop = d.id
					WHERE b.reference_num = ".$this->db->escape($reference_num). " ";
		} else {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.amount as amount, a.total_amount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item
				FROM `app_manual_order_logs` a
				LEFT JOIN `app_manual_order_details` b on a.order_id = b.reference_num
				LEFT JOIN `sys_products` c on c.Id = a.product_id
				LEFT JOIN `sys_shops` d on c.sys_shop = d.id
				WHERE b.reference_num = ".$this->db->escape($reference_num). " AND b.sys_shop = ".$this->db->escape($sys_shop). " ";
		}


        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_shop_url('assets/img/'.$row['shopcode'].'/products-250/'.$row['productid'].'/0-'.$row['productid']).'.jpg?'.rand().'">';
            $nestedData[] = ucwords($row["itemname"]);
            $nestedData[] = $row["qty"];
            $nestedData[] = number_format($row["amount"], 2);
			$nestedData[] = number_format($row["total_amount"], 2);

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

  function get_branch_details($orderid){
        $sql="SELECT a.branchid, a.orderid, b.branchname, d.shopname, b.address FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid =  b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              LEFT JOIN sys_shops d ON c.mainshopid = d.id
              WHERE a.status = ? AND a.orderid = ?";
        $data = array(1, $orderid);
        return $this->db->query($sql, $data);
	}

  public function order_item_table_print($reference_num, $sys_shop){
		if($sys_shop == 0) {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.amount as amount, a.total_amount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item
					FROM `app_manual_order_logs` a
					LEFT JOIN `app_manual_order_details` b on a.order_id = b.reference_num
					LEFT JOIN `sys_products` c on c.Id = a.product_id
					LEFT JOIN `sys_shops` d on c.sys_shop = d.id
					WHERE b.reference_num = ".$this->db->escape($reference_num). " ";
		} else {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.amount as amount, a.total_amount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item
				FROM `app_manual_order_logs` a
				LEFT JOIN `app_manual_order_details` b on a.order_id = b.reference_num
				LEFT JOIN `sys_products` c on c.Id = a.product_id
				LEFT JOIN `sys_shops` d on c.sys_shop = d.id
				WHERE b.reference_num = ".$this->db->escape($reference_num). " AND b.sys_shop = ".$this->db->escape($sys_shop). " ";
		}

        return $this->db->query($sql)->result_array();
	}

}
