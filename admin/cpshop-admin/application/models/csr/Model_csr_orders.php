<?php
class Model_csr_orders extends CI_Model {

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
		$this->db->select("prod.itemname,prod.otherinfo");
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
		$this->db->update_batch("app_order_details", $data, 'admin_drno');
		return $this->db->affected_rows();
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
		$this->db->select("sh.*");
		$this->db->where("id", $sys_shop);
		return $this->db->get("sys_shops sh")->row_array();
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

		$sql = "SELECT `id`FROM `sys_shops` WHERE `shopcode` = 'JCWW' ";
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
		$sql = "SELECT no_of_stocks FROM sys_products_invtrans_branch
			WHERE shopid = ? AND branchid = ? AND product_id = ? AND status = 1";
		$data = array($shopid,$branchid,$productid);
		return $this->db->query($sql,$data);
	}

	public function getShippingPerShop($refnum, $sys_shop){

		// $this->db->select("ship.sys_shop as shopid, ship.shippingfee as sf, ship.daystoship as dts");
		// $this->db->where("ship.areaid", $areaId);
		// $this->db->where("ship.sys_shop", $sys_shop);
		// $this->db->where("ship.status", 1);
		// return $this->db->get("sys_shop_shipping ship");

		$sql = "SELECT delivery_amount as sf, daystoship as dts, daystoship_to as dts_to FROM `app_order_details_shipping` WHERE reference_num = ? AND sys_shop = ?";
		$data = array($refnum, $sys_shop);
		$query = $this->db->query($sql, $data);

		if ($query->num_rows() > 0) {
			return $query;
		}else{
			return "";
		}
	}
}

?>
