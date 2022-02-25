<?php
class OrdersModel extends CI_Model {

	function getOrders($start,$length,$filter){
		$this->db->select("order.order_id,order.user_id,order.admin_drno,order.total_amount");
		$this->db->select("order.order_status,order.payment_method,order.date_ordered");
		$this->db->select("order.order_so_no,order.reference_num,order.paypanda_ref");
		$this->db->select("user.branchname");
		$this->db->join("jcwfp_users user", "order.user_id = user.userId", "left");
		$this->db->where('order.paypanda_ref <>', 0);
		$this->db->where('DATE_FORMAT(order.date_ordered, "%Y-%m-%d") >=', $filter['startDate']);
		$this->db->where('DATE_FORMAT(order.date_ordered, "%Y-%m-%d")   <=',$filter['endDate']);
		if($filter['search_input'] != null)
		{
			$this->db->like('CONCAT(order.reference_num,order.order_so_no) ', $filter['search_input']);
		}
		if($filter['status'] != null){
			$this->db->like('order.order_status ', $filter['status']);
		}

		$this->db->where('order.user_id', $filter['userId']);
		if($filter['parent']){
			$this->db->or_where("user.parent_id", $filter['userId']);
		}

		if($start != null && $length != null)
			$this->db->limit($length,$start);

		return $this->db->get('app_order_details order');
	}

	public function getByRefNum($refnum){
		$this->db->select("uo.*, uo.user_id as idno, uo.total_amount as order_total_amt, uo.name as fullname");
		$this->db->where("uo.reference_num", $refnum);
		return $this->db->get("app_order_details uo")->row_array();
	}

	public function searchByRefNum($refnum){
		$this->db->select("uo.*, uo.user_id as idno, uo.total_amount as order_total_amt, uo.name as fullname");
		$this->db->where("uo.reference_num", $refnum);
		// $this->db->where("uo.paypanda_ref <>", 0);
		// $this->db->where("uo.payment_status", 1);
		return $this->db->get("app_order_details uo")->row_array();
	}

	public function isRefNumExist($refnum){
		$sql = "SELECT EXISTS(SELECT reference_num FROM app_order_details WHERE reference_num = ?) AS is_exists "; // returns 1 if data exists, else 0

    return $this->db->query($sql, $refnum)->row()->is_exists;
	}

	public function getBySonoNum($sono){
		$this->db->select("uo.*, uo.user_id as idno, uo.total_amount as order_total_amt, uo.name as fullname");
		$this->db->where("uo.admin_sono", $sono);
		return $this->db->get("app_order_details uo")->row_array();
	}

	public function getByDrno($drno){
		$this->db->select("uo.*, uo.user_id as idno, uo.total_amount as order_total_amt, uo.name as fullname");
		$this->db->where("uo.admin_drno", $drno);
		return $this->db->get("app_order_details uo")->row_array();
	}

	public function get_citymun($citymunCode){
		$sql = "SELECT * FROM `sys_citymun` WHERE citymunCode = ?";
		$data = array($citymunCode);
		return $this->db->query($sql,$data);
	}

	function getOrdersToDeliver(){

	}

	function insertOrder($data){
		$this->db->insert("app_order_details", $data);
		return $this->db->affected_rows();
	}

	function insertOrderItem($data){
		return $this->db->insert("app_order_logs", $data);
	}

	function getOrderDetails($start, $length, $orderId){
		$this->db->select("ord.order_id, ur.order_so_no, ord.product_id, ord.quantity, ord.amount, ord.total_amount, ur.order_status, ur.total_amount as order_total_amt, ur.date_ordered, ur.payment_date, ord.sys_shop");
		$this->db->select("ur.reference_num,ur.paypanda_ref,ur.date_received");
		$this->db->select("prod.itemname as parent_item_name,prod.otherinfo");
		$this->db->select("(SELECT filename FROM sys_products_images WHERE product_id = prod.Id AND arrangement = 1 AND status = 1 LIMIT 1) as primary_pics");
		$this->db->select("(IF(prod.parent_product_id IS NULL, prod.itemname,(SELECT itemname FROM sys_products WHERE id = prod.parent_product_id))) as itemname");
		$this->db->from("app_order_logs ord");
		$this->db->join("app_order_details ur", "ur.order_id = ord.order_id", "left");
		$this->db->join("sys_products prod", "prod.Id = ord.product_id", "left");
		$this->db->where("ord.order_id", $orderId);
		if($start != null && $length != null){
			$this->db->limit($length,$start);
		}
		return $this->db->get();
	}

	public function getOrdersForAdmin(){
		$this->db->where("payment_status", 1);
		$this->db->where("order_status", "p");
		$this->db->from("app_order_details");
		return $this->db->get();
	}

	public function processOrder($data, $orderId){
		$this->db->where("order_id", $orderId);
		$this->db->update('app_order_details', $data);
	}

	public function payOrder($ref, $data){
		$this->db->where("reference_num", $ref);
		$this->db->update('app_order_details', $data);
	}

	public function pay_order($data,$ref){
		// $this->db->update('app_order_details',$data,array('reference_num' => $ref));
		// $id = $this->db->escape($data);
		$ref = $this->db->escape($ref);
		$sql = "UPDATE app_order_details SET payment_status = 1 WHERE reference_num = $ref";
		$this->db->query($sql);
	}

	public function process_dr_no($data){
		$this->db->update_batch("app_order_details", $data, 'admin_sono');
		return $this->db->affected_rows();
	}

	public function so_no(){
		$this->db->select("order_id");
		$res = $this->db->get("app_order_details");
		return $res->num_rows();
	}

	public function verify_item($id){
		$this->db->select("Id");
		$this->db->where("enabled", 1);
		$this->db->where("Id", $id);
		return $this->db->get("sys_products")->row();
	}

	public function processDelivery($data){
		// $this->db->update_batch("app_order_details", $data, 'admin_drno');
		// return $this->db->affected_rows();
		$sql = "UPDATE app_order_details a
			INNER JOIN app_sales_order_details b ON a.reference_num = b.reference_num
			SET a.admin_drno = ?,
				a.order_status = ?, b.order_status = ?,
				a.delivery_info = ?, b.delivery_info = ?,
				a.delivery_ref_num = ?, b.delivery_ref_num = ?,
				a.delivery_amount = ?, b.delivery_amount = ?,
				a.date_shipped = ?, b.date_shipped = ?
			WHERE a.admin_drno = ?";
		$param = array(
			$data['admin_drno'],
			$data['order_status'], $data['order_status'],
			$data['delivery_info'], $data['delivery_info'],
			$data['delivery_ref_num'], $data['delivery_ref_num'],
			$data['delivery_amount'], $data['delivery_amount'],
			$data['date_shipped'], $data['date_shipped'],
			$data['admin_drno']
		);

		$this->db->query($sql,$param);
		if($this->db->affected_rows() > 0){
			$sql2 = "SELECT b.id FROM app_order_details a
				INNER JOIN app_sales_order_details b ON a.reference_num = b.reference_num
				WHERE a.admin_drno = ?";
			$param2 = array($data['admin_drno']);
			$sales_id = $this->db->query($sql2,$param2);
			if($sales_id->num_rows() > 0){
				return $sales_id->row()->id;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//DELIVERY

	function getDeliveryOrders($userId, $filter){
		$ids = $this->session->userdata('branches');
		$this->db->select("order.order_id,order.user_id,order.admin_drno,order.total_amount");
		$this->db->select("order.order_status,order.payment_method,order.date_ordered");
		$this->db->select("order.order_so_no,order.reference_num,order.paypanda_ref");
		$this->db->select("user.branchname");
		$this->db->join("jcwfp_users user", "order.user_id = user.userId", "left");
		$this->db->where('order.paypanda_ref <>', 0);
		$this->db->where_in('order.user_id', $filter['userId']);

		return $this->db->get('app_order_details order');
	}

	function getOrderItemEcommerce($orderId){
		$this->db->select("prd.itemname, prd.otherinfo as unit, oi.amount, oi.quantity");
			// $this->db->join("sys_products prd", "prd.itemid = oi.product_id", "LEFT");
		$this->db->join("sys_products prd", "prd.Id = oi.product_id", "LEFT");
		$this->db->where("oi.order_id", $orderId);

		return $this->db->get("app_order_logs oi");
	}

	public function getOrderLogs($ref) {
		$this->db->select("a.*");
		$this->db->join("app_order_details b", "a.order_id = b.order_id", "LEFT");
		$this->db->where("b.reference_num", $ref);

		return $this->db->get("app_order_logs a");
	}

	//REFERRAL
	function insertReferralCode($data){
		return $this->db->insert("app_referral_codes", $data);
	}

	public function updateReferenceCode($ref, $data){
		$this->db->where("order_reference_num", $ref);
		$this->db->update("app_referral_codes", $data);
	}

	public function getReferralRecord($ref){
		$this->db->where("order_reference_num", $ref);
		$this->db->where("is_processed", 0);
		$this->db->where("payment_status", 1);
		return $this->db->get('app_referral_codes');
	}

	public function get_order_ReferralRecord($ref){
		$this->db->where("order_reference_num", $ref);
		// $this->db->where("is_processed", 0);
		$this->db->where("payment_status", 1);
		return $this->db->get('app_referral_codes');
	}

	//SALES ORDER
	function insertSalesOrder($data){
		$this->db->insert("app_sales_order_details", $data);
		return $this->db->insert_id();
	}

	function insertSalesOrderItem($data){
		return $this->db->insert("app_sales_order_logs", $data);
	}

	public function updateSalesOrderAmount($data, $orderId){
		$this->db->where("id", $orderId);
		$this->db->update('app_sales_order_details', $data);
	}

	//SHOP
	function getShopCode($sys_shop){
		$this->db->select("shopcode");
		$this->db->where("id", $sys_shop);
		$query = $this->db->get("sys_shops");

		if ($query->num_rows() > 0) {
			return $query->row()->shopcode;
		} else {
			return "";
		}
	}

	function getShopDetails($sys_shop){
		$this->db->select("sh.*, sh.shop_region as region");
		$this->db->where("id", $sys_shop);
		return $this->db->get("sys_shops sh")->row_array();
	}

	function get_shipping_zone($sys_shop){
		$sys_shop = $this->db->escape($sys_shop);
		$sql = "SELECT b.regCode as shop_region, b.provCode as shop_prov, b.citymunCode as shop_city
		 FROM sys_shipping a LEFT JOIN sys_shipping_zone b ON a.id = b.sys_shipping_id AND b.enabled = 1
		 WHERE a.enabled = 1 AND sys_shop_id = $sys_shop";
		return $this->db->query($sql)->result_array();

	}

	function get_branch_details($branchid){
		$branchid = $this->db->escape($branchid);
		$sql = "SELECT longitude, latitude, branch_region as region FROM sys_branch_profile WHERE id = $branchid AND status = 1";
		return $this->db->query($sql);
	}

	function getShopDetailsByShopCode($shopcode){
		$this->db->select("sh.*");
		$this->db->where("shopcode", $shopcode);
		return $this->db->get("sys_shops sh")->row_array();
	}

	function getShopDetailsByShopUrl($shopurl){
		$this->db->select("sh.*");
		$this->db->where("shopurl", $shopurl);
		return $this->db->get("sys_shops sh")->row_array();
	}

	//PRODUCT
	function getInventoryID($product_id){
		$this->db->select("itemid");
		$this->db->where("Id", $product_id);
		$query = $this->db->get("sys_products");

		if ($query->num_rows() > 0) {
			return $query->row()->itemid;
		} else {
			return "";
		}
	}

	//SHIPPING PER SHOP
	function insertShippingPerShop($data){
		return $this->db->insert("app_order_details_shipping", $data);
	}

	//FULFILLMENT

	public function getReferenceNumberBySO($sono) {

		$sql = "SELECT `reference_num`FROM `app_order_details` WHERE `admin_sono` = ? ";
		$query = $this->db->query($sql, $sono);

		if ($query->num_rows() > 0) {
			return $query->row()->reference_num;
		} else {
			return "";
		}
	}

	public function getJCWWShopID() {

		$sql = "SELECT `id`FROM `sys_shops` WHERE `shopcode` IN ('JCW','JCWW')";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->row()->id;
		} else {
			return "";
		}
	}

	public function fulfillOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_shipped` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"f",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function get_daystoship($reference_num, $sys_shop) {
		$query=" SELECT * FROM app_order_details_shipping WHERE reference_num = ? AND sys_shop = ?";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);

		$result = $this->db->query($query, $bind_data);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

	public function getOrderStatusPerShop($reference_num, $sys_shop) {
		$query=" SELECT order_status, date_ordered, date_order_processed, date_ready_pickup, date_booking_confirmed, date_fulfilled, date_shipped FROM app_sales_order_details WHERE reference_num = ? AND sys_shop = ?";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);

		$result = $this->db->query($query, $bind_data);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

	// Order Vouchers
	public function insertOrderVoucher($data){
		$this->db->insert("app_order_payment", $data);
		return $this->db->insert_id();
	}

	public function processOrderVoucherPayment($order_ref_num){
		$data = array(
			'date_processed' => todaytime(),
			'date_updated' => todaytime()
		);
		$this->db->where("order_ref_num", $order_ref_num);
		$this->db->update('app_order_payment', $data);
	}

	public function updateOrderVoucherPaypanda($order_ref_num, $paypanda_refno){
		$data = array(
			'payment_refno' => $paypanda_refno,
			'date_processed' => todaytime(),
			'date_updated' => todaytime()
		);
		$this->db->where("order_ref_num", $order_ref_num);
		$this->db->where("payment_type", 'paypanda');
		$this->db->update('app_order_payment', $data);
	}

	public function getOrderVoucher($order_ref_num) {
		$this->db->select("shopid, order_ref_num, amount, payment_type, payment_refno, date_processed");
		$this->db->where("order_ref_num", $order_ref_num);
		return $this->db->get("app_order_payment")->result_array();
	}

	public function get_branch_inv($shopid,$branchid,$productid){
		$sql = "SELECT no_of_stocks,
			@cont_selling_isset := (SELECT cont_selling_isset FROM sys_products WHERE Id = product_id AND enabled = 1) as cont_selling_isset
			FROM sys_products_invtrans_branch
			WHERE shopid = ? AND branchid = ? AND product_id = ? AND status = 1
			ORDER BY branchid";
		$data = array($shopid,$branchid,$productid);
		
		return $this->db->query($sql,$data);
	}

	public function get_branch_orders($refnum){
		$refnum = $this->db->escape($refnum);
		$sql = "SELECT * FROM sys_branch_orders WHERE orderid = $refnum AND status = 1";
		return $this->db->query($sql);
	}

	public function get_app_order_sales($refnum){
		$refnum = $this->db->escape($refnum);
		$sql = "SELECT reference_num FROM app_sales_order_details
			WHERE reference_num = $refnum AND status = 1";
		return $this->db->query($sql);
	}

	public function get_app_order_branch_details($refnum,$shopid){
		$refnum = $this->db->escape($refnum);
		$shopid = $this->db->escape($shopid);
		$sql = "SELECT b.branchname, b.contactperson, b.mobileno, b.email, b.address,
			b.branch_city, b.branch_region, b.city as delivery_city, b.province as delivery_province,
			b.region as delivery_region, b.isautoassign, b.latitude, b.longitude, c.id,
			a.branchid, a.shopid, c.mainshopid
			FROM app_order_branch_details a
			INNER JOIN sys_branch_profile b ON a.branchid = b.id
			LEFT JOIN sys_branch_mainshop c ON c.branchid = b.id
			WHERE a.order_refnum = $refnum AND a.shopid = $shopid AND c.mainshopid = $shopid
			AND a.enabled = 1 AND c.status = 1 AND b.status = 1";
		return $this->db->query($sql);
	}

	public function set_app_order_branch_details_batch($data){
		$this->db->insert_batch('app_order_branch_details',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	// API LOGS
	public function get_postback_process_order($refnum){
		$refnum = $this->db->escape($refnum);
		$sql = "SELECT * FROM app_postback_process_order WHERE refnum = $refnum";
		return $this->db->query($sql)->result_array();
	}

	public function set_postback_process_order($data){
		$this->db->insert('app_postback_process_order',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function update_postback_process_order($data,$refnum){
		$this->db->update('app_postback_process_order',$data,array('refnum' => $refnum));
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function set_api_referral_logs($data){
		$this->db->insert('api_referral_logs',$data);
		return ($this->db->affected_rows() > 0) ? true :false;
	}

	public function set_api_jc_logs($data){
		$this->db->insert('api_jc_logs',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function set_api_jcww_logs($data){
		$this->db->insert('api_jcww_logs',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function set_jc_fulfillment_logs($data){
		$this->db->insert('api_jcfulfillment_logs',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function set_postback_error_logs($data){
		$this->db->insert('app_postback_error_logs',$data);
	}

	public function set_toktok_shipping_api_logs_batch($data){
		$this->db->insert_batch('api_toktok_shipping_logs',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	// panda  books api
	public function FulfilledOrder($reference_num, $sys_shop) {
		$sql = "SELECT * FROM app_sales_order_details WHERE reference_num = ? AND sys_shop = ?";

		$bind_data = array(
			$reference_num,
			$sys_shop
		);

		$result = $this->db->query($sql, $bind_data)->row_array();

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_fulfilled` = ?";

		if($result['date_ready_pickup'] == '0000-00-00 00:00:00' || $result['date_booking_confirmed'] == '0000-00-00 00:00:00'){
			$sql .= ", date_ready_pickup = '".date('Y-m-d H:i:s')."', date_booking_confirmed = '".date('Y-m-d H:i:s')."'";
		}

		$sql .= " WHERE reference_num = ? AND sys_shop = ? ";

		$bind_data = array(
			"f",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);

		$this->db->query($sql, $bind_data);
		return ($this->db->affected_rows() > 0) ?  $result['id'] : false;
		// return $result['id'];
	}

	public function shippedOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_shipped` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"s",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	function addOrderHistory($order_id, $description, $action, $username, $date_created){
		$sql="INSERT INTO app_order_history (order_id, description, action, username, date_created, status) VALUES (?, ?, ?, ?, ?, ?)";
		$data = array($order_id, $description, $action, $username, $date_created, 1);

		$this->db->query($sql, $data);
	}

	function logActivity($module, $details, $action_type, $username){
        $query  = " INSERT INTO sys_audittrail (module, details, action_type, username, ip_address, date_created)
                    VALUES (?, ?, ?, ?, ?, ?) ";

        $params = array(
            $module,
            $details,
            $action_type,
            $username,
            $_SERVER['REMOTE_ADDR'],
            date('Y-m-d H:i:s')
        );
        $result = $this->db->query($query, $params);

        return $result;
  }

	public function get_last_unfulfilled_order($shopid,$branchid = 0,$days = 5){
		$shopid = $this->db->escape($shopid);
		$branchid = $this->db->escape($branchid);
		$days = $this->db->escape($days);
		$sql = "SELECT a.date_ordered, a.payment_date, a.date_assigned
		 FROM app_sales_order_details a
		 LEFT JOIN sys_branch_orders b ON a.reference_num = b.orderid
		 WHERE a.status = 1 AND b.status = 1 AND a.sys_shop = $shopid AND b.branchid = $branchid
		 AND a.order_status NOT IN ('f','s') AND a.payment_status = 1
		 AND a.date_assigned < DATE_SUB(NOW(),INTERVAL $days DAY)
		 ORDER BY a.date_assigned DESC LIMIT 1";
		return $this->db->query($sql);
	}

	public function get_default_allowed_unfulfilled_orders($shopid){
		$shopid = $this->db->escape($shopid);
		$sql = "SELECT check_unfulfilled_orders, allowed_unfulfilled
			FROM sys_shops WHERE status = 1 AND id = $shopid";
		return $this->db->query($sql)->row_array();
	}

	public function get_specific_allowed_unfulfilled_orders($shopid,$branchid,$citymuncode){
		$shopid = $this->db->escape($shopid);
		$branchid = $this->db->escape($branchid);
		$citymuncode = $this->db->escape($citymuncode);
		$return_data = null;
		$sql = "SELECT a.allowed_unfulfilled
			FROM sys_unfulfilled_settings a
			WHERE a.shopid = $shopid AND a.branchid = $branchid AND a.status = 1";
		$append = " AND FIND_IN_SET($citymuncode,citymuncode)";
		$query1 = $sql.$append;
		$result1 = $this->db->query($query1);
		if($result1->num_rows() > 0){
			$return_data = $result1->row()->allowed_unfulfilled;
		}

		if($return_data == null){
			$append2 = " AND FIND_IN_SET(
				(SELECT provCode FROM sys_citymun WHERE citymunCode = $citymuncode AND status = 1),
				a.provcode
			)";
			$query2 = $sql.$append2;
			$result2 = $this->db->query($query2);
			if($result2->num_rows() > 0){
				$return_data = $result2->row()->allowed_unfulfilled;
			}
		}

		if($return_data == null){
			$append3 = " AND FIND_IN_SET(
				(SELECT regDesc FROM sys_citymun WHERE citymunCode = $citymuncode AND status = 1),
				a.regcode
			)";
			$query3 = $sql.$append3;
			$result3 = $this->db->query($query3);
			if($result3->num_rows() > 0){
				$return_data = $result3->row()->allowed_unfulfilled;
			}
		}


		return $return_data;
	}
}

?>
