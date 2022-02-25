<?php
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) { //to ignore maximum time limit
    @set_time_limit(0);
}
set_time_limit(0);
ini_set('memory_limit', '2048M');

class Model_orders_toktokmall extends CI_Model {

	# Start - Orders

	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('reports', TRUE);
		$this->load->model('Model_paid_orders_with_branch', 'model_powb');	
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

    public function get_shopcode($user_id){
		$sql=" SELECT b.shopcode as shopcode FROM app_members a
			    LEFT JOIN sys_shops b ON a.sys_shop = b.id
				WHERE a.sys_user = ? AND a.status = 1";
		$sql = $this->db->query($sql, $user_id);

        if($sql->num_rows() > 0){
            return $sql->row()->shopcode;
        }else{
            return "";
        }
    }

    public function get_shopcode_via_shopid($id) {
		$query="SELECT * FROM sys_shops WHERE id = ? AND status = 1";
		$params = array($id);

		return $this->db->query($query, $params)->row()->shopcode;
	}

    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1 ORDER BY shopname";
		return $this->db->query($query)->result_array();
    }

    public function get_category_options() {
		$query="SELECT * FROM sys_product_category WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

	public function get_partners_options() {
		$query="SELECT * FROM sys_shipping_partners WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

	public function get_partners_options_api_isset() {
		$query="SELECT * FROM sys_shipping_partners WHERE api_isset =  1 AND status = 1 ";
		return $this->db->query($query)->result_array();
	}

	public function get_payments_options() {
		$query="SELECT * FROM sys_payment_type WHERE status = 1";
		return $this->db->query($query)->result_array();
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

	public function get_branchname_orders($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname, b.address FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db2->query($sql, $data);

        return $result;
    }


	public function orders_details($ref_num, $sys_shop) {
		//if shop = shopanda; not yet paid
		if($sys_shop == 0) {
			$query=" SELECT a.*, a.order_id as orderid, a.srp_totalamount as totamt, a.order_status as orderstatus, a.delivery_amount as sf, a.payment_status as paystatus,
					s.daystoship as paid_daystoship, s.daystoship_to as paid_daystoship_to, b.id as app_sales_id, b.date_shipped as shipped_date, b.payment_notes as p_notes,
					rider.rider_name, rider.rider_platenum, rider.rider_conno, b.delivery_info as shippingpartner, b.delivery_ref_num as shipping_ref, b.delivery_notes as shipping_note,
					a.latitude as lati, a.longitude as longi, b.delivery_amount as shipping_cost, b.delivery_imgurl as pickedup_photo, b.delivery_imgurl_2 as shipped_photo,
					b.date_order_processed, b.date_ready_pickup, b.date_booking_confirmed, b.date_fulfilled, b.date_returntosender, b.date_redeliver, b.t_deliveryId,
					a.exrate_n_to_php as currval, a.currency as currcode, b.isconfirmed
					FROM app_order_details as a
					LEFT JOIN app_sales_order_details as b ON a.reference_num = b.reference_num
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE a.reference_num = ?";

			$bind_data = array(
				$ref_num
			);
		}
		else {
			$query="SELECT * FROM (
					SELECT a.*, c.id as orderid, SUM(b.srp_totalamount) as totamt, a.order_status as orderstatus, c.order_status as sales_order_status, s.delivery_amount as sf, s.sys_shop as sys_shop, a.payment_status as paystatus, c.date_shipped as dateshipped,
						s.daystoship as paid_daystoship, s.daystoship_to as paid_daystoship_to, shop.shopname as shopname, shop.address as pickup_address,
						rider.rider_name, rider.rider_platenum, rider.rider_conno, c.id as app_sales_id, c.date_shipped as shipped_date, c.payment_notes as p_notes,
						c.delivery_info as shippingpartner, c.delivery_ref_num as shipping_ref, c.delivery_notes as shipping_note,
						a.latitude as lati, a.longitude as longi, c.delivery_amount as shipping_cost, c.delivery_imgurl as pickedup_photo, c.delivery_imgurl_2 as shipped_photo,
						c.date_order_processed, c.date_ready_pickup, c.date_booking_confirmed, c.date_fulfilled, c.date_returntosender, c.date_redeliver, c.t_deliveryId,
						c.exrate_n_to_php as currval, c.currency as currcode, c.isconfirmed, shop.merch_referral_code
						FROM app_order_details as a
						LEFT JOIN `app_order_logs` as b ON a.order_id = b.order_id
						LEFT JOIN `app_sales_order_details` as c ON a.reference_num = c.reference_num
						LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
						LEFT JOIN `sys_shops` as shop ON c.sys_shop = shop.id
						LEFT JOIN `app_sales_order_details_rider` as rider ON c.id = rider.app_sales_order_id AND rider.status = 1
						WHERE a.reference_num = ? AND b.sys_shop = ? AND s.sys_shop = ? AND a.payment_status = 0
					UNION ALL
					SELECT a.*, c.id as orderid, SUM(b.srp_totalamount) as totamt, a.order_status as orderstatus, c.order_status as sales_order_status, s.delivery_amount as sf, s.sys_shop as sys_shop, c.payment_status as paystatus, c.date_shipped as dateshipped,
						s.daystoship as paid_daystoship, s.daystoship_to as paid_daystoship_to, shop.shopname as shopname, shop.address as pickup_address,
						rider.rider_name, rider.rider_platenum, rider.rider_conno, c.id as app_sales_id, c.date_shipped as shipped_date, c.payment_notes as p_notes,
						c.delivery_info as shippingpartner, c.delivery_ref_num as shipping_ref, c.delivery_notes as shipping_note,
						a.latitude as lati, a.longitude as longi, c.delivery_amount as shipping_cost, c.delivery_imgurl as pickedup_photo, c.delivery_imgurl_2 as shipped_photo,
						c.date_order_processed, c.date_ready_pickup, c.date_booking_confirmed, c.date_fulfilled, c.date_returntosender, c.date_redeliver, c.t_deliveryId,
						c.exrate_n_to_php as currval, c.currency as currcode, c.isconfirmed, shop.merch_referral_code
						FROM app_order_details as a
						LEFT JOIN `app_order_logs` as b ON a.order_id = b.order_id
						LEFT JOIN `app_sales_order_details` as c ON a.reference_num = c.reference_num
						LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
						LEFT JOIN `sys_shops` as shop ON c.sys_shop = shop.id
						LEFT JOIN `app_sales_order_details_rider` as rider ON c.id = rider.app_sales_order_id AND rider.status = 1
						WHERE a.reference_num = ? AND b.sys_shop = ? AND c.sys_shop = ? AND s.sys_shop = ? AND a.payment_status = 1 ) as u WHERE u.sys_shop = ? ";

			$bind_data = array(
				$ref_num,
				$sys_shop,
				$sys_shop,
				$ref_num,
				$sys_shop,
				$sys_shop,
				$sys_shop,
				$sys_shop
			);
		}

		return $this->db2->query($query, $bind_data)->row_array();
	}

	public function orders_history($order_id) {
		$query = "SELECT * FROM app_order_history
		WHERE order_id = ? ORDER BY date_created DESC" ;

		$bind_data = array(
			$order_id,
		);

		return $this->db2->query($query, $bind_data)->result_array();
	}

	public function orders_history_sales($order_id) {
		$query = "SELECT * FROM app_order_history
		WHERE order_id = ? ORDER BY date_created DESC" ;

		$bind_data = array(
			$order_id,
		);

		return $this->db2->query($query, $bind_data)->result_array();
	}

	public function get_referral_code($order_reference_number) {
		$query = "SELECT referral_code FROM app_referral_codes
				WHERE order_reference_num = ? ";

		$result = $this->db2->query($query, $order_reference_number);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
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

	function get_branch_details_id($branchid){
        $sql="SELECT * FROM sys_branch_profile WHERE id = ?";
        $data = array($branchid);
        return $this->db->query($sql, $data);
	}

	function get_shop_details_id($shopid){
        $sql="SELECT * FROM sys_shops WHERE id = ? AND status > 0";
        $data = array($shopid);
        return $this->db->query($sql, $data);
	}

	function get_mainshopname($sys_shop){
        $sql="SELECT shopname FROM sys_shops WHERE status = ? AND id = ?";
        $data = array(1, $sys_shop);
        return $this->db->query($sql, $data);
	}

	function get_mainshopname_all($sys_shop){
        $sql="SELECT * FROM sys_shops WHERE id = ?";
        $data = array($sys_shop);
        return $this->db2->query($sql, $data);
	}

	public function get_regions() {
		$query = "SELECT * FROM sys_region
				WHERE status = 1
				ORDER BY regDesc ASC";

		return $this->db->query($query);

	}

	public function get_provinces() {
		$query = "SELECT * FROM sys_prov
				WHERE status = 1
				ORDER BY provDesc ASC";

		return $this->db->query($query);

	}

	public function get_citymuns() {
		$query = "SELECT a.*, CONCAT(a.citymunDesc, ' - ', b.provDesc) as citymunName FROM sys_citymun AS a
				LEFT JOIN sys_prov AS b ON a.provCode = b.provCode AND b.status = 1
				WHERE a.status = 1
				ORDER BY a.citymunDesc ASC";

		return $this->db->query($query);

	}

	function get_all_branch($mainshopid){
        $sql="SELECT branch.branchname, branch.id, branchms.mainshopid, branchms.branchid
        FROM sys_branch_profile branch LEFT JOIN sys_branch_mainshop branchms ON branch.id = branchms.branchid
        WHERE branchms.mainshopid = ? AND branchms.status = ?";
        $data = array($mainshopid, 1);
        return $this->db2->query($sql, $data);
	}

	function reassign_branch($branchid, $orderid, $remarks, $mainshopid, $prev_branchid){

		if($branchid != 0){
			if($this->check_if_exist_to_branch($orderid, $mainshopid)->num_rows() > 0){
				$sql="UPDATE sys_branch_orders SET status = ?, remarks = ? WHERE status = ? AND orderid = ? AND branchid = ?";
				$data = array(0, $remarks, 1, $orderid, $this->check_if_exist_to_branch($orderid, $mainshopid)->row_array()['branchid']);
				$this->db->query($sql, $data);
				
			}
			if(!empty($branchid) || $branchid != ""){
				$sql="INSERT INTO sys_branch_orders (branchid, orderid, status) VALUES (?, ?, ?)";
				$data = array($branchid, $orderid, 1);
				$this->db->query($sql, $data);
			}
		}else{
			$sql="UPDATE sys_branch_orders SET status = ?, remarks = ? WHERE status = ? AND orderid = ? AND branchid = ?";
			$data = array(0, $remarks, 1, $orderid, $prev_branchid);
			$this->db->query($sql, $data);

			if(!empty($branchid) || $branchid != ""){
				$sql="INSERT INTO sys_branch_orders (branchid, orderid, status) VALUES (?, ?, ?)";
				$data = array($branchid, $orderid, 1);
				$this->db->query($sql, $data);
			}
		}

		$sql="UPDATE app_order_branch_details SET branchid = ? WHERE shopid = ? AND order_refnum = ? AND enabled = 1";
			$data = array($branchid, $mainshopid, $orderid);
			$this->db->query($sql, $data);

	}

	function reassign_date($reference_num, $mainshopid){
		$sql="UPDATE app_sales_order_details SET date_assigned = ? WHERE sys_shop = ? AND reference_num = ?";
		$data = array(date('Y-m-d H:i:s'), $mainshopid, $reference_num);
		$this->db->query($sql, $data);
	}

	function updatePickupAddressBranch($branch_id, $pickup_address){
		$sql="UPDATE sys_branch_profile SET address = ? WHERE id = ?";
            $data = array($pickup_address, $branch_id);
            $this->db->query($sql, $data);
	}

	function updatePickupAddressShop($shop_id, $pickup_address){
		$sql="UPDATE sys_shops SET address = ? WHERE id = ?";
            $data = array($pickup_address, $shop_id);
            $this->db->query($sql, $data);
	}

	function addOrderHistory($order_id, $description, $action, $username, $date_created){
		$sql="INSERT INTO app_order_history (order_id, description, action, username, date_created, status) VALUES (?, ?, ?, ?, ?, ?)";
		$data = array($order_id, $description, $action, $username, $date_created, 1);

		$this->db->query($sql, $data);
	}

	function getAppOrderDetails($reference_num){
		$sql="SELECT * FROM app_order_details WHERE reference_num = ?";
		$data = array($reference_num);

		return $this->db->query($sql, $data)->row();;
	}

	function check_if_exist_to_branch($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname, b.address FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        return $this->db->query($sql, $data);
	}

	public function getSplitOrder($reference_num) {
		$query=" SELECT a.*, SUM(b.total_amount) as tot_amount, b.sys_shop as sys_shop
				FROM `app_order_details` as a LEFT JOIN `app_order_logs` as b ON a.order_id = b.order_id
			WHERE reference_num = ? AND payment_status = 0 GROUP BY a.reference_num, b.sys_shop ";
		return $this->db->query($query, $reference_num)->result_array();
	}

	public function getOrderLogs($reference_num) {
		$query=" SELECT a.* FROM app_order_logs as a
				LEFT JOIN app_order_details as b ON a.order_id = b.order_id WHERE b.reference_num = ? ";
		return $this->db->query($query, $reference_num)->result_array();
	}

	function insertSalesOrder($data){
		$this->db->insert("app_sales_order_details", $data);
		return $this->db->insert_id();
	}

	function insertSalesOrderItem($data){
		return $this->db->insert("app_sales_order_logs", $data);
	}

	public function tagPayment($args, $shop) {

		if($args['f_payment'] != 'Others') {
			$payment_partner = $this->get_payment_type($args['f_payment']);
		} else {
			$payment_partner = $args['f_payment_others'];
		}

		$sql = "UPDATE `app_order_details` SET `payment_method` = ?, `paypanda_ref` = ?, `payment_date` = ? WHERE reference_num = ? ";
		$bind_data = array(
			$payment_partner,
			$args['f_payment_ref_num'],
			date('Y-m-d H:i:s'),
			$args['f_id-p']
		);

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_sales_order_details` SET `payment_id` = ?, `payment_method` = ?, `paypanda_ref` = ?,`payment_amount` = ?, `payment_notes` = ?, `payment_date` = ? WHERE reference_num = ? ";
		if($shop != 0 )
		 $sql .= "AND sys_shop = ".$shop;
		$bind_data = array(
			$args['f_payment'],
			$payment_partner,
			$args['f_payment_ref_num'],
			$args['f_payment_fee'],
			$args['f_payment_notes'],
			date('Y-m-d H:i:s'),
			$args['f_id-p']
		);
		return $this->db->query($sql, $bind_data);
	}

	public function get_payment_type($id) {
		$query = "SELECT description FROM sys_payment_type
				WHERE id = ? ";

		$result = $this->db->query($query, $id);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

	public function tagManualPayment($refnum, $shop) {

		$payment_partner = "Paid to Seller";
		$payrefnum = $refnum;
		$payment_amount = 0;
		$payment_note = "";

		$sql = "UPDATE `app_order_details` SET `payment_method` = ?, `paypanda_ref` = ?, `payment_date` = ? WHERE reference_num = ? ";
		$bind_data = array(
			$payment_partner,
			$payrefnum,
			date('Y-m-d H:i:s'),
			$refnum
		);

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_sales_order_details` SET `payment_id` = ?, `payment_method` = ?, `paypanda_ref` = ?,`payment_amount` = ?, `payment_notes` = ?, `payment_date` = ? WHERE reference_num = ? ";
		if($shop != 0 )
		 $sql .= "AND sys_shop = ".$shop;
		$bind_data = array(
			0,
			$payment_partner,
			$payrefnum,
			$payment_amount,
			$payment_note,
			date('Y-m-d H:i:s'),
			$refnum
		);
		return $this->db->query($sql, $bind_data);
	}

	public function payOrder($reference_num, $shop) {

		$sql = "UPDATE `app_order_details` SET `payment_status` = ? WHERE reference_num = ? ";
		$bind_data = array(
			1,
			$reference_num
		);

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `app_sales_order_details` SET `payment_status` = ? WHERE reference_num = ? ";
		if($shop != 0 )
		 $sql .= "AND sys_shop = ".$shop;

		$bind_data = array(
			1,
			$reference_num
		);
		return $this->db->query($sql, $bind_data);
	}

	public function processOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_order_processed` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"po",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function getSalesOrder($reference_num, $sys_shop) {

		$sql = "SELECT a.*, b.latitude, b.longitude, b.notes as order_notes FROM app_sales_order_details AS a
		LEFT JOIN app_order_details AS b ON a.reference_num = b.reference_num
		WHERE a.reference_num = ? AND a.sys_shop = ?";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data)->row();
	}

	public function getSalesOrder_book($reference_num, $sys_shop) {

		$sql = "SELECT a.*, b.latitude, b.longitude, b.notes as order_notes FROM app_sales_order_details AS a
		LEFT JOIN app_order_details AS b ON a.reference_num = b.reference_num
		WHERE a.sys_shop = ? AND a.reference_num = ?";
		$bind_data = array(
			$sys_shop,
			$reference_num
		);
		return $this->db->query($sql, $bind_data)->row();
	}

	public function readyPickupOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_ready_pickup` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"rp",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function readyPickupOrder_toktok($reference_num, $sys_shop, $price, $deliveryId, $rp_shipping_partner, $shareLink, $referralCode) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_ready_pickup` = ?, `delivery_amount` = ?, `t_deliveryId` = ?, `t_shareLink` = ?, `t_referralCode` = ?,  `rp_shipping_partner` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"rp",
			date('Y-m-d H:i:s'),
			$price,
			$deliveryId,
			$shareLink,
			$referralCode,
			$rp_shipping_partner,
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function cancelOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `delivery_amount` = '', `t_deliveryId` = '' WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"po",
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function bookingConfirmOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_booking_confirmed` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"bc",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);

		$this->db->query($sql, $bind_data);

		$sql = "SELECT * FROM app_sales_order_details WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			$reference_num,
			$sys_shop
		);

		return $this->db->query($sql, $bind_data)->row()->id;
	}

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
		return $result['id'];
	}

	public function returntosenderOrder($reference_num, $sys_shop) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_returntosender` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"rs",
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function redeliverOrder($reference_num, $sys_shop, $app_sales_id) {

		$sql = "UPDATE `app_sales_order_details` SET `order_status` = ?, `date_order_processed` = ?, `date_ready_pickup` = ?, `date_booking_confirmed` = ?, `date_fulfilled` = ?, `date_redeliver` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			"p",
			'0000-00-00 00:00:00',
			'0000-00-00 00:00:00',
			'0000-00-00 00:00:00',
			'0000-00-00 00:00:00',
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE app_sales_order_details_rider SET status = 0 WHERE app_sales_order_id = ? AND status = 1 ";
		$bind_data = array(
			$app_sales_id
		);
		return $this->db->query($sql, $bind_data);
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

	public function confirmedOrder($reference_num, $sys_shop) {

		$sql = "UPDATE app_sales_order_details SET isconfirmed = ?, date_confirmed = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			1,
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function tagRider($args, $id) {

		$sql="INSERT INTO app_sales_order_details_rider (app_sales_order_id, rider_name, rider_platenum, rider_conno, status) VALUES (?, ?, ?, ?, ?)";
		$bind_data = array(
			$id,
			$args['bc_rider_name'],
			$args['bc_platenum'],
			$args['bc_conno'],
			1
		);
		return $this->db->query($sql, $bind_data);
	}

	public function mf_tagRider($args, $id) {
		$sql = "UPDATE app_sales_order_details_rider SET status = 0 WHERE app_sales_order_id = ? AND status = 1 ";
		$bind_data = array(
			$id
		);

		$this->db->query($sql, $bind_data);

		$sql="INSERT INTO app_sales_order_details_rider (app_sales_order_id, rider_name, rider_platenum, rider_conno, status) VALUES (?, ?, ?, ?, ?)";
		$bind_data = array(
			$id,
			$args['mf_rider_name'],
			$args['mf_platenum'],
			$args['mf_conno'],
			1
		);

		return $this->db->query($sql, $bind_data);
	}

	public function tagShipping($reference_num, $args, $sys_shop) {

		if($args['f_shipping'] != 'Others') {
			$shipping_partner = $this->get_shipping_partner($args['f_shipping']);
		} else {
			$shipping_partner = $args['f_shipping_others'];
		}

		$sql = "UPDATE `app_sales_order_details` SET `delivery_id` = ?, `delivery_info` = ?, `delivery_ref_num` = ?,`delivery_amount` = ?, `delivery_notes` = ?, `date_fulfilled` = ? WHERE reference_num = ? AND sys_shop = ? ";
		$bind_data = array(
			$args['f_shipping'],
			$shipping_partner,
			$args['f_shipping_ref_num'],
			$args['f_shipping_fee'],
			$args['f_shipping_notes'],
			date('Y-m-d H:i:s'),
			$reference_num,
			$sys_shop
		);
		return $this->db->query($sql, $bind_data);
	}

	public function insertInventoryTrans($data) {
		return $this->db->insert("sys_products_invtrans", $data);
	}

	public function updateProductStocks($productid) {
		$sql = "UPDATE sys_products SET no_of_stocks = (SELECT SUM(quantity) FROM sys_products_invtrans WHERE product_id = ? AND enabled = 1) WHERE Id = ?";
		$data = array(
			$productid,
			$productid
		);

		return $this->db->query($sql, $data);
	}

	public function get_shipping_partner($id) {
		$query = "SELECT name FROM sys_shipping_partners
				WHERE id = ? ";

		$result = $this->db->query($query, $id);
		if($result->num_rows() > 0)
			return $result->row()->name;
		else
			return "";
	}

	public function listShopItems($reference_num, $sys_shop) {

		$query=" SELECT c.itemid as id, c.itemname as itemname, a.quantity as qty, a.amount as amount, a.total_amount as total_amount, c.Id as productid, c.otherinfo as unit,
				a.exrate_n_to_php as currval, a.currency as currcode
				FROM `app_sales_order_logs` a
				LEFT JOIN `app_sales_order_details` b on a.order_id = b.id
				LEFT JOIN `sys_products` c on c.Id = a.product_id
				WHERE a.status = 1 AND b.status = 1 AND b.reference_num = '".$reference_num. "' AND b.sys_shop = '".$sys_shop. "' ";

		return $this->db->query($query)->result_array();
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

	public function get_next_orders($reference_num, $date_ordered, $order_status = 'all') {
		$data_array = array();

		if($order_status == 'all'){
		// 	$query = "SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, 0 as sys_shop
		// 		FROM `app_order_details` as a
		// 		WHERE a.payment_status = 0 AND a.status = 1
		// 		AND a. date_ordered < ?
		// 		ORDER BY a.date_ordered DESC LIMIT 20";

		// 	$params     = array($date_ordered);
		// 	$query1_arr = $this->db->query($query, $params)->result_array();
			$query1_arr = array();

			$query = "SELECT b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
				FROM `app_sales_order_details` as b
				WHERE b.status = 1 AND b.status = 1
				AND b. date_ordered < ?
				ORDER BY b.date_ordered DESC LIMIT 20";

			$params     = array($date_ordered);
			$query2_arr = $this->db->query($query, $params)->result_array();

			$data_array = array_merge($query1_arr, $query2_arr);
		}
		else{
			if($order_status == 'pending'){
				$query = "SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, 0 as sys_shop
				FROM `app_order_details` as a
				WHERE a.payment_status = 0 AND a.status = 1
				AND a. date_ordered < ?
				ORDER BY a.date_ordered DESC LIMIT 20";

				$params     = array($date_ordered);
				$query1_arr = $this->db->query($query, $params)->result_array();

				$query2_arr = array();

				$data_array = array_merge($query1_arr, $query2_arr);
			} 
			else{
				if($order_status == 'paid'){
					$status_string = '';
				}
				else if($order_status == 'readyforprocessing'){
					$status_string = " AND b.order_status = 'p'";
				}
				else if($order_status == 'processing'){	
					$status_string = " AND b.order_status = po'";
				}
				else if($order_status == 'readyforpickup'){
					$status_string = " AND b.order_status = 'rp'";
				}
				else if($order_status == 'bookingconfirmed'){	
					$status_string = " AND b.order_status = 'bc'";
				}
				else if($order_status == 'fulfilled'){
					$status_string = " AND b.order_status = 'f'";
				}
				else if($order_status == 'shipped'){
					$status_string = " AND b.order_status = 's'";
				}
				else if($order_status == 'returntosender'){
					$status_string = " AND b.order_status = 'rs'";
				}
				else{
					$status_string = "";
				}	

				$query1_arr = array();

				$query = "SELECT b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
					FROM `app_sales_order_details` as b
					WHERE b.status = 1 AND b.status = 1
					AND b. date_ordered < ? $status_string
					ORDER BY b.date_ordered DESC LIMIT 20";

				$params     = array($date_ordered);
				$query2_arr = $this->db->query($query, $params)->result_array();

				$data_array = array_merge($query1_arr, $query2_arr);
			}


		}

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

	public function get_prev_orders($reference_num, $date_ordered, $order_status = 'all') {
		$data_array = array();

		if($order_status == 'all'){
			// $query = "SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, 0 as sys_shop
			// 	FROM `app_order_details` as a
			// 	WHERE a.payment_status = 0 AND a.status = 1
			// 	AND a. date_ordered > ?
			// 	ORDER BY a.date_ordered ASC LIMIT 20";

			// $params     = array($date_ordered);
			// $query1_arr = $this->db->query($query, $params)->result_array();
			$query1_arr = array();

			$query = "SELECT b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
				FROM `app_sales_order_details` as b
				WHERE b.status = 1 AND b.status = 1
				AND b. date_ordered > ?
				ORDER BY b.date_ordered ASC LIMIT 20";

			$params     = array($date_ordered);
			$query2_arr = $this->db->query($query, $params)->result_array();

			$data_array = array_merge($query1_arr, $query2_arr);
		}
		else{
			if($order_status == 'pending'){
				$query = "SELECT a.date_ordered as date_ordered, a.reference_num as reference_num, 0 as sys_shop
					FROM `app_order_details` as a
					WHERE a.payment_status = 0 AND a.status = 1
					AND a. date_ordered > ?
					ORDER BY a.date_ordered ASC LIMIT 20";

				$params     = array($date_ordered);
				$query1_arr = $this->db->query($query, $params)->result_array();
				$query2_arr = array();
				$data_array = array_merge($query1_arr, $query2_arr);
			} 
			else{
				if($order_status == 'paid'){
					$status_string = '';
				}
				else if($order_status == 'readyforprocessing'){
					$status_string = " AND b.order_status = 'p'";
				}
				else if($order_status == 'processing'){	
					$status_string = " AND b.order_status = po'";
				}
				else if($order_status == 'readyforpickup'){
					$status_string = " AND b.order_status = 'rp'";
				}
				else if($order_status == 'bookingconfirmed'){	
					$status_string = " AND b.order_status = 'bc'";
				}
				else if($order_status == 'fulfilled'){
					$status_string = " AND b.order_status = 'f'";
				}
				else if($order_status == 'shipped'){
					$status_string = " AND b.order_status = 's'";
				}
				else if($order_status == 'returntosender'){
					$status_string = " AND b.order_status = 'rs'";
				}
				else{
					$status_string = "";
				}	

				$query1_arr = array();

				$query = "SELECT b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop
					FROM `app_sales_order_details` as b
					WHERE b.status = 1 AND b.status = 1
					AND b. date_ordered > ? $status_string
					ORDER BY b.date_ordered ASC LIMIT 20";

				$params     = array($date_ordered);
				$query2_arr = $this->db->query($query, $params)->result_array();

				$data_array = array_merge($query1_arr, $query2_arr);
			}
		}

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

	public function get_next_orders_per_shop($reference_num, $sys_shop, $date_ordered, $order_status = 'all') {
		$branchid = $this->session->userdata('branchid');

		if($branchid == 0){
			$data_array = array();

			$query="SELECT a.created as date_ordered, a.reference_num as reference_num, a.sys_shop as sys_shop
				FROM `app_order_details_shipping` as a
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
				LEFT JOIN `app_order_details_shipping` as b ON a.orderid = b.reference_num
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

	public function get_prev_orders_per_shop($reference_num, $sys_shop, $date_ordered, $order_status = 'all') {
		$branchid = $this->session->userdata('branchid');

		if($branchid == 0){
			$data_array = array();

			$query="SELECT a.created as date_ordered, a.reference_num as reference_num, a.sys_shop as sys_shop
				FROM `app_order_details_shipping` as a
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
				LEFT JOIN `app_order_details_shipping` as b ON a.orderid = b.reference_num
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

	public function read_shop($id) {
		$query=" SELECT a.*, b.ratetype, b.rateamount FROM sys_shops as a
				LEFT JOIN sys_shop_rate as b ON a.id = b.syshop
				WHERE a.id = ? ";
		return $this->db->query($query, $id)->row_array();
	}

	public function get_vouchers($reference_num) {
		$query=" SELECT amount as voucheramount, payment_refno as vouchercode FROM app_order_payment
		WHERE status = 1 AND payment_type = 'toktokmall' AND order_ref_num = ?";
		$params = array($reference_num);
		return $this->db2->query($query, $params)->result_array();
	}

	public function get_app_order_details_date_ordered($reference_num) {
		$query=" SELECT date_ordered FROM app_order_details WHERE reference_num = ?";
		$params = array($reference_num);
		return $this->db->query($query, $params)->row()->date_ordered;
	}

	public function get_app_order_details_date_ordered_shop($reference_num) {
		$query=" SELECT created FROM app_order_details_shipping WHERE reference_num = ?";
		$params = array($reference_num);
		return $this->db->query($query, $params)->row()->created;
	}

	public function get_vouchers_shop($reference_num, $sys_shop) {
		$query=" SELECT amount as voucheramount, payment_refno as vouchercode FROM app_order_payment
		WHERE status = 1 AND payment_type = 'toktokmall' AND order_ref_num = ? AND shopid = ?";
		$params = array($reference_num, $sys_shop);
		return $this->db2->query($query, $params)->result_array();
	}

	public function get_citymun($citymunCode) {
		$query="SELECT citymunDesc FROM sys_citymun WHERE citymunCode = ?";
		$params = array($citymunCode);

		$result = $this->db->query($query, $params);

		if($result->num_rows() > 0){
			return $result->row_array()['citymunDesc'];
		}
		else{
			return "None";
		}

	}

    public function order_table($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $this->input->post('_record_status');
		$_name 			      = $this->input->post('_name');
		$status 		      = $this->input->post('status');
		$date 		          = $this->input->post('date');
		$location 		      = $this->input->post('location');
		$address 		      = $this->input->post('address');
		$regCode 		      = $this->input->post('regCode');
		$provCode 		      = $this->input->post('provCode');
		$citymunCode	      = $this->input->post('citymunCode');
		$drno	 		      = $this->input->post('drno');
		$order_status_view    = $this->input->post('order_status_view');
		$forpickup            = $this->input->post('forpickup');
		$isconfirmed          = $this->input->post('isconfirmed');
		$_shops 		      = $this->input->post('_shops');
		$date_from 		      = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));

		$requestData = $_REQUEST;



		switch ($date) {
			case "date_fulfilled":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_fulfilled',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_shipped":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_shipped',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
				
			break;
			case "date_processed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_order_processed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_booking_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_booking_confirmed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_confirmed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_paid":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'payment_date',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
		    default:
			$columns = array(
				// datatable column index  => database column name for sorting
					0 => 'date_ordered',
					1 => 'reference_num',
					2 => 'name',
					3 => 'conno',
					4 => 'total_amount',
					5 => 'payment_status',
					6 => 'order_status',
					7 => 'shopname',
				);
			break;
		}

		

		if($status == ""){
			$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = "SELECT * FROM (
				SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.srp_totalamount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.notes, a.conno, a.payment_date
				FROM `app_order_details` as a USE INDEX(date_ordered)
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string."
				UNION ALL
				SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname, 'none' as delivery_amount,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.notes, b.conno, b.payment_date
				FROM `app_sales_order_details` as b USE INDEX(date_ordered)
				WHERE b.status = 1 AND b.status = 1 ".$date_string2.") as u
				WHERE u.reference_num <> ''";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND u.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND u.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND u.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND u.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND u.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND u.citymunCode = '".$citymunCode."' ";
			}
		}
		else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
			
			if($date === 'date_fulfilled'){
			   	$date_string = ($_name != "") ? "" : "AND b.date_fulfilled BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_shipped'){
				$date_string = ($_name != "") ? "" : "AND b.date_shipped BETWEEN ".$date_from_2." AND ".$date_to_2."";
		    }else if($date === 'date_processed'){
			    $date_string = ($_name != "") ? "" : "AND b.date_order_processed BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_booking_confirmed'){
				$date_string = ($_name != "") ? "" : "AND b.date_booking_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_confirmed'){
				$date_string = ($_name != "") ? "" : "AND b.date_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_paid'){
				$date_string = ($_name != "") ? "" : "AND b.payment_date BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}
			else{
			    $date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}
			
			$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.date_fulfilled, b.date_shipped, b.date_order_processed, b.date_booking_confirmed, b.date_confirmed, b.payment_date
				FROM `app_sales_order_details` as b
				LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
				-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
				-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
				WHERE b.status = 1 AND b.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND b.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND b.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND b.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND b.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND b.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND b.citymunCode = '".$citymunCode."' ";
			}
			
			if($forpickup == 'fp'){
				$sql .= " AND b.notes LIKE '%|::PA::|%' ";
			}

			if($isconfirmed != ''){
				$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
			}

		}
		else if($status == 0 || $status == '6'){
			$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			$_shops      = ($_shops == '') ? 0 : $_shops;

			$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, SUM(s.srp_totalamount) as total_amount, a.order_status as order_status, a.payment_status as payment_status, '".$_shops."' as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
				FROM `app_order_details` as a USE INDEX(date_ordered)
				LEFT JOIN `app_order_logs` as s ON a.order_id = s.order_id
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%') ";
			}

			if($_shops != 0) {
				$sql.=" AND s.sys_shop = ".$this->db->escape($_shops)." ";
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND a.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND a.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND a.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND a.citymunCode = '".$citymunCode."' ";
			}

			$sql .= "GROUP BY a.reference_num";
		}

		if($status == 0 || $status == '6'){
			$query = $this->db2->query($sql);
		}
		else{
			$query = $this->db2->query($sql);
		}

        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	
		$query = $this->db2->query($sql);

		$data          = array();
		$international = c_international();
		$allow_cod     = cs_clients_info()->c_allow_cod;
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($international == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$subtotal_amount_conv           = $total_amount_item;

				$subtotal_converted             = displayCurrencyValue_withPHP($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
				
			}
			else if($international == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				// $voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = $total_amount_item;
				// $delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
				// $voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				// $delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				// $total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($row['total_amount'], 2);
			}


			if($date === 'date_fulfilled'){	
		       $nestedData[] = $row["date_fulfilled"];
			}else if($date === 'date_shipped'){
		     	$nestedData[] = $row["date_shipped"];
			}else if($date === 'date_processed'){
			    $nestedData[] = $row["date_order_processed"];   
			}else if($date === 'date_booking_confirmed'){
				$nestedData[] = $row["date_booking_confirmed"];
			}else if($date === 'date_confirmed'){
			    $nestedData[] = $row["date_confirmed"];
			}else if($date === 'date_paid'){
			    $nestedData[] = $row["payment_date"];
			}
			else{
				$nestedData[] = $row["date_ordered"];
			}
		
		
			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
			$nestedData[] = $row["conno"];
            $nestedData[] = $subtotal_converted;

			$nestedData[] = display_payment_status($row['payment_status'], $row['payment_method'], $allow_cod);
			$nestedData[] = display_order_status($row['order_status']);

            $nestedData[] = $row['shopname'];

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
            $nestedData[] = $branchname;

            if($sys_shop == 0) {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item"  href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row['sys_shop'].'-'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }
            else {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item" href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
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

	public function order_table_backup($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $this->input->post('_record_status');
		$_name 			      = $this->input->post('_name');
		$status 		      = $this->input->post('status');
		$location 		      = $this->input->post('location');
		$address 		      = $this->input->post('address');
		$regCode 		      = $this->input->post('regCode');
		$provCode 		      = $this->input->post('provCode');
		$citymunCode	      = $this->input->post('citymunCode');
		$drno	 		      = $this->input->post('drno');
		$order_status_view    = $this->input->post('order_status_view');
		$forpickup            = $this->input->post('forpickup');
		$isconfirmed            = $this->input->post('isconfirmed');
		$_shops 		      = $this->input->post('_shops');
		$date_from 		      = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_ordered',
            1 => 'shopcode',
            2 => 'reference_num',
            3 => 'name',
            5 => 'total_amount',
            6 => 'payment_status',
            7 => 'order_status',
            8 => 'shopname',
		);

		$getShippingData    = $this->getShippingData($_name, $date_from_2_shipping, $date_to_2_shipping)->result_array();
		$getShippingDataArr = [];
		$getShopData        = $this->getShopData()->result_array();
		$getShopDataArr     = [];

		foreach($getShippingData as $row){
			$getShippingDataArr[strval($row['reference_num'])][strval($row['sys_shop'])]['delivery_amount'] = $row['delivery_amount'];
		}

		foreach($getShopData as $row){
			$getShopDataArr[strval($row['id'])]['shopname'] = $row['shopname'];
		}

		

		if($status == ""){
			$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = "SELECT * FROM (
				SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.notes, a.conno
				FROM `app_order_details` as a USE INDEX(date_ordered)
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string."
				UNION ALL
				SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname, 'none' as delivery_amount,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.notes, b.conno
				FROM `app_sales_order_details` as b USE INDEX(date_ordered)
				WHERE b.status = 1 AND b.status = 1 ".$date_string2.") as u
				WHERE u.reference_num <> ''";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND u.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND u.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND u.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND u.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND u.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND u.citymunCode = '".$citymunCode."' ";
			}
		}
		else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
			$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
				FROM `app_sales_order_details` as b USE INDEX(date_ordered)
				-- LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
				-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
				-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
				WHERE b.status = 1 AND b.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND b.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND b.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND b.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND b.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND b.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND b.citymunCode = '".$citymunCode."' ";
			}
			
			if($forpickup == 'fp'){
				$sql .= " AND b.notes LIKE '%|::PA::|%' ";
			}

			if($isconfirmed != ''){
				$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
			}

		}
		else if($status == 0 || $status == '6'){
			$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
				FROM `app_order_details` as a USE INDEX(date_ordered)
				-- LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%') ";
			}

			// if($sys_shop != 0) {
			// 	$sql.=" AND s.sys_shop = '".$sys_shop."' ";
			// }else{
			// 	if($_shops !='') {
			// 		$sql.=" AND s.sys_shop = ".$this->db->escape($_shops)." ";
			// 	}
			// }

			if($status != ""){
				if($status == '1'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND a.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND a.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND a.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND a.citymunCode = '".$citymunCode."' ";
			}

			// if($drno != ''){
			// 	$sql.=" AND a.admin_drno = '".$drno."' ";
			// }

		}

        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($row['sys_shop'] != 0) {
				$voucher_total_amount = $this->get_vouchers_total_shop($row["reference_num"], $row["sys_shop"])->total_voucher_amount;
				$subtotal = max($row["total_amount"] - $voucher_total_amount, 0);
			}else{
				$voucher_total_amount = 0;
				$subtotal = $row["total_amount"];
			}

			if($status == ""){
				if($row['delivery_amount'] == 'none'){
					$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
				}
				else{
					$delivery_amount = $row['delivery_amount'];
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
			}
			else if($status == 0 || $status == '6'){
				$delivery_amount = $row['delivery_amount'];
			}

			$shopname = ($row['shopname'] == 0 && $row['sys_shop'] != 0) ? $getShopDataArr[$row['sys_shop']]['shopname'] : $row['shopname'];

			if(c_international() == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue_withPHP($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue_withPHP($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue_withPHP($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue_withPHP(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				
			}
			else if(c_international() == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($subtotal, 2);
				$voucher_total_amount_converted = number_format($voucher_total_amount, 2);
				$delivery_amount_converted      = number_format($delivery_amount, 2);
				$total_amount_converted         = number_format(max($subtotal+$delivery_amount, 0), 2);
			}
			
			$nestedData[] = $row["date_ordered"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
			$nestedData[] = $row["conno"];
            $nestedData[] = $subtotal_converted;
            $nestedData[] = $voucher_total_amount_converted;
            $nestedData[] = $delivery_amount_converted;
            $nestedData[] = $total_amount_converted;

            if($row["payment_status"] == 1 && $row["payment_method"] != 'Free Payment' && $row["payment_method"] != 'Prepayment' && $row["payment_method"] != 'PayPanda' && cs_clients_info()->c_allow_cod == 1) {
                $nestedData[] = "<label class='badge badge-success'> Paid(COD)</label>";
			}
			else if($row["payment_status"] == 1) {
                $nestedData[] = "<label class='badge badge-success'> Paid</label>";
			}
			else if($row["payment_status"] == 0 && $row["payment_method"] != 'Free Payment' && $row["payment_method"] != 'Prepayment' && $row["payment_method"] != 'PayPanda' && cs_clients_info()->c_allow_cod == 1){
				$nestedData[] = "<label class='badge badge-info'> Pending(COD)</label>";
			}
            else if($row["payment_status"] == 0){
                $nestedData[] = "<label class='badge badge-info'> Pending</label>";
			}
			else{
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
				case 'rs':
				$nestedData[] = "<label class='badge badge-success'> Return to Sender</label>";
				break;
				case 's':
				$nestedData[] = "<label class='badge badge-success'> Shipped</label>";
				break;
				default:
				$nestedData[] = "<label class='badge badge-warning'> Ready for Processing</label>";
				break;
			}

            $nestedData[] = $shopname;

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
            $nestedData[] = $branchname;
            $nestedData[] = $this->get_citymun($row["citymunCode"]);

            if($sys_shop == 0) {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item"  href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row['sys_shop'].'-'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }
            else {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item" href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }

			$data[] = $nestedData;
		}
		$key = $requestData['order'][0]['column'];
		$dir = $requestData['order'][0]['dir'];
		uasort($data, build_sorter($key, $dir));
		$data = (isset($requestData['start'])) ? array_slice($data, $requestData['start'], $requestData['length']):$data;

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function order_table_export($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$date 		    = $this->input->post('date_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$_shops 		= $this->input->post('_shops_export');
		$forpickup 		= $this->input->post('forpickup_export');
		$isconfirmed    = $this->input->post('isconfirmed_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$date_from_2    = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($status == ""){
			$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = "SELECT * FROM (
				SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.srp_totalamount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.notes, a.conno, a.payment_date
				FROM `app_order_details` as a USE INDEX(date_ordered)
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string."
				UNION ALL
				SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname, 'none' as delivery_amount,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.notes, b.conno, b.payment_date
				FROM `app_sales_order_details` as b USE INDEX(date_ordered)
				WHERE b.status = 1 AND b.status = 1 ".$date_string2.") as u
				WHERE u.reference_num <> ''";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND u.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND u.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND u.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND u.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND u.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND u.citymunCode = '".$citymunCode."' ";
			}
		}
		else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
			if($date === 'date_fulfilled'){
			   	$date_string = ($_name != "") ? "" : "AND b.date_fulfilled BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_shipped'){
				$date_string = ($_name != "") ? "" : "AND b.date_shipped BETWEEN ".$date_from_2." AND ".$date_to_2."";
		    }else if($date === 'date_processed'){
			    $date_string = ($_name != "") ? "" : "AND b.date_order_processed BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_booking_confirmed'){
				$date_string = ($_name != "") ? "" : "AND b.date_booking_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_confirmed'){
				$date_string = ($_name != "") ? "" : "AND b.date_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}else if($date === 'date_paid'){
				$date_string = ($_name != "") ? "" : "AND b.payment_date BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}
			else{
			    $date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			}
			
			$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.date_fulfilled, b.date_shipped, b.date_order_processed, b.date_booking_confirmed, b.date_confirmed, b.payment_date
				FROM `app_sales_order_details` as b
				LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
				-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
				-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
				WHERE b.status = 1 AND b.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND b.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND b.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND b.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND b.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND b.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND b.citymunCode = '".$citymunCode."' ";
			}
			
			if($forpickup == 'fp'){
				$sql .= " AND b.notes LIKE '%|::PA::|%' ";
			}

			if($isconfirmed != ''){
				$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
			}

		}
		else if($status == 0 || $status == '6'){
			$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			$_shops      = ($_shops == '') ? 0 : $_shops;

			$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, SUM(s.srp_totalamount) as total_amount, a.order_status as order_status, a.payment_status as payment_status, '".$_shops."' as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
				FROM `app_order_details` as a USE INDEX(date_ordered)
				LEFT JOIN `app_order_logs` as s ON a.order_id = s.order_id
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%') ";
			}

			if($_shops != 0) {
				$sql.=" AND s.sys_shop = ".$this->db->escape($_shops)." ";
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND a.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND a.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND a.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND a.citymunCode = '".$citymunCode."' ";
			}

			$sql .= "GROUP BY a.reference_num";
		}

		$sql .= " ORDER BY date_ordered DESC";

		if($status == 0 || $status == '6'){
			return $this->db2->query($sql)->result_array();
		}
		else{
			return $this->db2->query($sql)->result_array();
		}
	}

	public function order_table_export_backup($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$_shops 		= $this->input->post('_shops_export');
		$forpickup 		= $this->input->post('forpickup_export');
		$isconfirmed    = $this->input->post('isconfirmed_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$date_from_2    = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($status == ""){
			$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
			$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = "SELECT * FROM (
				SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.notes, a.conno
				FROM `app_order_details` as a USE INDEX(date_ordered)
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string."
				UNION ALL
				SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname, 'none' as delivery_amount,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.notes, b.conno
				FROM `app_sales_order_details` as b USE INDEX(date_ordered)
				WHERE b.status = 1 AND b.status = 1 ".$date_string2.") as u
				WHERE u.reference_num <> ''";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND u.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND u.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND u.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND u.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND u.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND u.citymunCode = '".$citymunCode."' ";
			}
		}
		else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
			$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname,
				b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
				FROM `app_sales_order_details` as b
				-- LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
				-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
				-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
				WHERE b.status = 1 AND b.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($sys_shop != 0) {
				$sql.=" AND b.sys_shop = '".$sys_shop."' ";
			}else{
				if($_shops !='') {
					$sql.=" AND b.sys_shop = ".$this->db->escape($_shops)." ";
				}
			}

			if($status != ""){
				if($status == '1'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND b.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND b.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND b.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND b.citymunCode = '".$citymunCode."' ";
			}
			
			if($forpickup == 'fp'){
				$sql .= " AND b.notes LIKE '%|::PA::|%' ";
			}

			if($isconfirmed != ''){
				$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
			}

		}
		else if($status == 0 || $status == '6'){
			$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

			$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
				a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
				FROM `app_order_details` as a USE INDEX(date_ordered)
				-- LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%') ";
			}

			// if($sys_shop != 0) {
			// 	$sql.=" AND s.sys_shop = '".$sys_shop."' ";
			// }else{
			// 	if($_shops !='') {
			// 		$sql.=" AND s.sys_shop = ".$this->db->escape($_shops)." ";
			// 	}
			// }

			if($status != ""){
				if($status == '1'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '0'){
					$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
				}
				else if($status == '6'){
					$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else if($status == '7'){
					$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				}
				else{
					$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
				}
			}

			if($location == 'address'){
				$sql.=" AND a.address LIKE '%".$address."%' ";
			}
			else if($location == 'region'){
				$sql.=" AND a.regCode = '".$regCode."' ";
			}
			else if($location == 'province'){
				$sql.=" AND a.provCode = '".$provCode."' ";
			}
			else if($location == 'citymun'){
				$sql.=" AND a.citymunCode = '".$citymunCode."' ";
			}

			// if($drno != ''){
			// 	$sql.=" AND a.admin_drno = '".$drno."' ";
			// }

		}

		$sql .= " ORDER BY date_ordered ASC";

		return $this->db->query($sql)->result_array();
	}

	public function order_table_shop($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status    = $this->input->post('_record_status');
		$_name 			   = $this->input->post('_name');
		$status 		   = $this->input->post('status');
		$date 		       = $this->input->post('date');
		$location 		   = $this->input->post('location');
		$address 		   = $this->input->post('address');
		$regCode 		   = $this->input->post('regCode');
		$provCode 		   = $this->input->post('provCode');
		$citymunCode	   = $this->input->post('citymunCode');
		$forpickup         = $this->input->post('forpickup');
		$isconfirmed       = $this->input->post('isconfirmed');
		$_shops 		   = $this->input->post('_shops');
		$date_from 		   = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		   = format_date_reverse_dash($this->input->post('date_to'));
		$token_session     = $this->session->userdata('token_session');
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');
		$order_status_view = $this->input->post('order_status_view');
		$date_from_2       = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		$requestData = $_REQUEST;

	
		switch ($date) {
			case "date_fulfilled":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_fulfilled',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_shipped":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_shipped',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
				
			break;
			case "date_processed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_order_processed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_booking_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_booking_confirmed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_confirmed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_paid":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'payment_date',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
		    default:
			$columns = array(
				// datatable column index  => database column name for sorting
					0 => 'date_ordered',
					1 => 'reference_num',
					2 => 'name',
					3 => 'conno',
					4 => 'total_amount',
					5 => 'payment_status',
					6 => 'order_status',
					7 => 'shopname',
				);
			break;
		}

		if($branchid == 0){
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(srp_totalamount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 1
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND b.status = 1
					".$date_string2." ) as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				if($date == 'date_fulfilled'){
					    $date_string = ($_name != "") ? "" : "AND b.date_fulfilled BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_shipped'){
						$date_string = ($_name != "") ? "" : "AND b.date_shipped BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_processed'){
						$date_string = ($_name != "") ? "" : "AND b.date_order_processed BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_booking_confirmed'){
						$date_string = ($_name != "") ? "" : "AND b.date_booking_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_confirmed'){
						$date_string = ($_name != "") ? "" : "AND b.date_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_paid'){
					    $date_string = ($_name != "") ? "" : "AND b.payment_date BETWEEN ".$date_from_2." AND ".$date_to_2."";
			    }else{
						$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}
		

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.date_fulfilled, b.date_shipped, b.date_order_processed, b.date_booking_confirmed, b.date_confirmed, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1
					AND b.status = 1
					".$date_string."
					AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
				$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, SUM(s.srp_totalamount) as total_amount, a.order_status as order_status, a.payment_status as payment_status, '".$sys_shop."' as sys_shop, shop.shopname as shopname, a.delivery_amount as delivery_amount,
				a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
				FROM `app_order_details` as a USE INDEX(date_ordered)
				LEFT JOIN `app_order_logs` as s ON a.order_id = s.order_id
				LEFT JOIN `sys_shops` as shop ON s.sys_shop = shop.id
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				$sql.=" AND s.sys_shop = ".$this->db->escape($sys_shop)." ";


				if($status != ""){
					if($status == '1'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND a.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND a.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND a.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND a.citymunCode = '".$citymunCode."' ";
				}

				$sql .= "GROUP BY a.reference_num";
			}
		}else{
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(srp_totalamount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0
					AND a.status = 100
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.conno, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` as bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` as bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` as bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)." AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string2.") as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.name LIKE '%".$this->db->escape_like_str($_name)."%' OR u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
			}
		}

		if($status == 0 || $status == '6'){
			$query = $this->db2->query($sql);
		}
		else{
			$query = $this->db2->query($sql);
		}

        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db2->query($sql);
		$international = c_international();
		$allow_cod     = cs_clients_info()->c_allow_cod;
		$data          = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($international == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$subtotal_amount_conv           = $total_amount_item;
				$subtotal_converted             = displayCurrencyValue_withPHP($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
			}
			else if($international == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$subtotal_amount_conv           = $total_amount_item;
				$subtotal_converted             = displayCurrencyValue($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($row['total_amount'], 2);
			}

			if($date == 'date_fulfilled'){
				$nestedData[] = $row["date_fulfilled"];
			}else if($date === 'date_shipped'){
				$nestedData[] = $row["date_shipped"];
			}else if($date === 'date_processed'){
				$nestedData[] = $row["date_order_processed"];
			}else if($date === 'date_booking_confirmed'){
				$nestedData[] = $row["date_booking_confirmed"];
			}else if($date === 'date_confirmed'){
				$nestedData[] = $row["date_confirmed"];
			}else if($date === 'date_paid'){
				$nestedData[] = $row["payment_date"];
			}
			else{
				$nestedData[] = $row["date_ordered"];
			}

			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
			$nestedData[] = $row["conno"];
            $nestedData[] = $subtotal_converted;

           	$nestedData[] = display_payment_status($row['payment_status'], $row['payment_method'], $allow_cod);
           	$nestedData[] = display_order_status($row['order_status']);

            $nestedData[] = $row['shopname'];

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
			$nestedData[] = $branchname;

            if($sys_shop == 0) {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item"  href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row['sys_shop'].'-'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }
            else {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item" href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
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

	public function order_table_shop_backup($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status    = $this->input->post('_record_status');
		$_name 			   = $this->input->post('_name');
		$status 		   = $this->input->post('status');
		$location 		   = $this->input->post('location');
		$address 		   = $this->input->post('address');
		$regCode 		   = $this->input->post('regCode');
		$provCode 		   = $this->input->post('provCode');
		$citymunCode	   = $this->input->post('citymunCode');
		$forpickup         = $this->input->post('forpickup');
		$_shops 		   = $this->input->post('_shops');
		$date_from 		   = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		   = format_date_reverse_dash($this->input->post('date_to'));
		$token_session     = $this->session->userdata('token_session');
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');
		$order_status_view = $this->input->post('order_status_view');
		$date_from_2       = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
			0 => 'date_ordered',
			1 => 'shopcode',
			2 => 'reference_num',
			3 => 'name',
			5 => 'total_amount',
			6 => 'payment_status',
			7 => 'order_status',
			8 => 'shopname',
		);

		$getShippingData    = $this->getShippingData($_name, $date_from_2, $date_to_2)->result_array();
		$getShippingDataArr = [];
		$getShopData        = $this->getShopData()->result_array();
		$getShopDataArr     = [];

		foreach($getShippingData as $row){
			$getShippingDataArr[strval($row['reference_num'])][strval($row['sys_shop'])]['delivery_amount'] = $row['delivery_amount'];
		}

		foreach($getShopData as $row){
			$getShopDataArr[strval($row['id'])]['shopname'] = $row['shopname'];
		}

		if($branchid == 0){
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 1
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND b.status = 1
					".$date_string2." ) as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					-- LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1
					AND b.status = 1
					".$date_string."
					AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 1
					".$date_string." AND s.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND a.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND a.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND a.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND a.citymunCode = '".$citymunCode."' ";
				}
			}
		}else{
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0
					AND a.status = 100
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` as bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` as bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` as bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)." AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string2.") as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.name LIKE '%".$this->db->escape_like_str($_name)."%' OR u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 100
					".$date_string." AND ".$this->db->escape($date_to)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( a.name LIKE '%".$this->db->escape_like_str($_name)."%' OR a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND a.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND a.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND a.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND a.citymunCode = '".$citymunCode."' ";
				}
			}
		}


        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($row['sys_shop'] != 0) {
				$voucher_total_amount = $this->get_vouchers_total_shop($row["reference_num"], $row["sys_shop"])->total_voucher_amount;
				$subtotal = max($row["total_amount"] - $voucher_total_amount, 0);
			}else{
				$voucher_total_amount = 0;
				$subtotal = $row["total_amount"];
			}

			if($status == ""){
				if($row['delivery_amount'] == 'none'){
					$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
				}
				else{
					$delivery_amount = $row['delivery_amount'];
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
			}
			else if($status == 0 || $status == '6'){
				$delivery_amount = $row['delivery_amount'];
			}

			$shopname = ($row['shopname'] == 0 && $row['sys_shop'] != 0) ? $getShopDataArr[$row['sys_shop']]['shopname'] : $row['shopname'];

			if(c_international() == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue_withPHP($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue_withPHP($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue_withPHP($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue_withPHP(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				
			}
			else if(c_international() == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($subtotal, 2);
				$voucher_total_amount_converted = number_format($voucher_total_amount, 2);
				$delivery_amount_converted      = number_format($delivery_amount, 2);
				$total_amount_converted         = number_format(max($subtotal+$delivery_amount, 0), 2);
			}

			$nestedData[] = $row["date_ordered"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
			$nestedData[] = $row["conno"];
            $nestedData[] = $subtotal_converted;
            $nestedData[] = $voucher_total_amount_converted;
            $nestedData[] = $delivery_amount_converted;
            $nestedData[] = $total_amount_converted;

            if($row["payment_status"] == 1 && $row["payment_method"] != 'Free Payment' && $row["payment_method"] != 'Prepayment' && $row["payment_method"] != 'PayPanda' && cs_clients_info()->c_allow_cod == 1) {
                $nestedData[] = "<label class='badge badge-success'> Paid(COD)</label>";
			}
			else if($row["payment_status"] == 1) {
                $nestedData[] = "<label class='badge badge-success'> Paid</label>";
			}
			else if($row["payment_status"] == 0 && $row["payment_method"] != 'Free Payment' && $row["payment_method"] != 'Prepayment' && $row["payment_method"] != 'PayPanda' && cs_clients_info()->c_allow_cod == 1){
				$nestedData[] = "<label class='badge badge-info'> Pending(COD)</label>";
			}
            else if($row["payment_status"] == 0){
                $nestedData[] = "<label class='badge badge-info'> Pending</label>";
			}
			else{
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
				case 'rs':
				$nestedData[] = "<label class='badge badge-success'> Return to Sender</label>";
				break;
				case 's':
				$nestedData[] = "<label class='badge badge-success'> Shipped</label>";
				break;
				default:
				$nestedData[] = "<label class='badge badge-warning'> Ready for Processing</label>";
				break;
			}

            $nestedData[] = $shopname;

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
			$nestedData[] = $branchname;
			$nestedData[] = $this->get_citymun($row['citymunCode']);

            if($sys_shop == 0) {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item"  href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row['sys_shop'].'-'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }
            else {
				$nestedData[] =
					'<div class="dropdown">
						<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
						<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
							<a class="dropdown-item" href="'.base_url('Main_orders/orders_view_merchant/'.$token.'/'.$row["reference_num"]).'/'.$order_status_view.'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
						</div>
					</div>';
            }


			$data[] = $nestedData;
		}
		$key = $requestData['order'][0]['column'];
		$dir = $requestData['order'][0]['dir'];
		uasort($data, build_sorter($key, $dir));
		$data = (isset($requestData['start'])) ? array_slice($data, $requestData['start'], $requestData['length']):$data;

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function order_table_shop_export($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$date 	     	= $this->input->post('date_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$forpickup		= $this->input->post('forpickup_export');
		$isconfirmed	= $this->input->post('isconfirmed_export');
		$_shops 		= $this->input->post('_shops_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$branchid 		= $this->session->userdata('branchid');
		$date_from_2    = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($branchid == 0){
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(srp_totalamount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 1
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND b.status = 1
					".$date_string2." ) as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				if($date == 'date_fulfilled'){
					    $date_string = ($_name != "") ? "" : "AND b.date_fulfilled BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_shipped'){
						$date_string = ($_name != "") ? "" : "AND b.date_shipped BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_processed'){
						$date_string = ($_name != "") ? "" : "AND b.date_order_processed BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_booking_confirmed'){
						$date_string = ($_name != "") ? "" : "AND b.date_booking_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_confirmed'){
						$date_string = ($_name != "") ? "" : "AND b.date_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}else if($date === 'date_paid'){
				    	$date_string = ($_name != "") ? "" : "AND b.payment_date BETWEEN ".$date_from_2." AND ".$date_to_2."";
			    }else{
						$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				}
		

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.date_fulfilled, b.date_shipped, b.date_order_processed, b.date_booking_confirmed, b.date_confirmed, b.payment_date 
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1
					AND b.status = 1
					".$date_string."
					AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
				$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, SUM(s.srp_totalamount) as total_amount, a.order_status as order_status, a.payment_status as payment_status, '".$sys_shop."' as sys_shop, shop.shopname as shopname, a.delivery_amount as delivery_amount,
				a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
				FROM `app_order_details` as a USE INDEX(date_ordered)
				LEFT JOIN `app_order_logs` as s ON a.order_id = s.order_id
				LEFT JOIN `sys_shops` as shop ON s.sys_shop = shop.id
				WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				$sql.=" AND s.sys_shop = ".$this->db->escape($sys_shop)." ";


				if($status != ""){
					if($status == '1'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND a.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND a.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND a.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND a.citymunCode = '".$citymunCode."' ";
				}

				$sql .= "GROUP BY a.reference_num";
			}
		}else{
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(srp_totalamount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0
					AND a.status = 100
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.conno, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` as bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` as bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` as bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)." AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string2.") as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.name LIKE '%".$this->db->escape_like_str($_name)."%' OR u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.srp_totalamount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
			}
		}

		$sql .= " ORDER BY date_ordered DESC";

		if($status == 0 || $status == '6'){
			return $this->db2->query($sql)->result_array();
		}
		else{
			return $this->db2->query($sql)->result_array();
		}
	}

	public function order_table_shop_export_backup($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$_shops 		= $this->input->post('_shops_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$branchid 		= $this->session->userdata('branchid');
		$date_from_2    = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($branchid == 0){
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 1
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND b.status = 1
					".$date_string2." ) as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					-- LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1
					AND b.status = 1
					".$date_string."
					AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 1
					".$date_string." AND s.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND a.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND a.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND a.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND a.citymunCode = '".$citymunCode."' ";
				}
			}
		}else{
			if($status == ""){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
				$date_string2 = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT * FROM (
					SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0
					AND a.status = 100
					".$date_string."
					UNION ALL
					SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname, d.delivery_amount as delivery_amount,
					e.admin_drno as admin_drno, b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` as bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` as bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` as bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND d.sys_shop = ".$this->db->escape_str($sys_shop)." AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string2.") as u
					WHERE u.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( u.name LIKE '%".$this->db->escape_like_str($_name)."%' OR u.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND u.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND u.payment_status = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND u.payment_status = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND u.order_status = ".$this->db->escape($status)." AND u.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND u.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND u.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND u.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND u.citymunCode = '".$citymunCode."' ";
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db->escape_str($branchid)."
					AND bms.branchid = ".$this->db->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}

				if($isconfirmed != ''){
					$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
				}
			}
			else if($status == 0 || $status == '6'){
				$date_string  = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql="SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, (SELECT SUM(total_amount) FROM `app_order_logs` WHERE order_id = a.order_id AND sys_shop = ".$sys_shop.") as total_amount, a.order_status as order_status, a.payment_status as payment_status, s.sys_shop as sys_shop, '".get_company_name()."' as shopname, s.delivery_amount as delivery_amount,
					a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno
					FROM `app_order_details` as a USE INDEX(date_ordered)
					LEFT JOIN `app_order_details_shipping` as s ON a.reference_num = s.reference_num
					WHERE a.payment_status = 0 AND s.sys_shop = ".$this->db->escape_str($sys_shop)."
					AND a.status = 100
					".$date_string." AND ".$this->db->escape($date_to)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( a.name LIKE '%".$this->db->escape_like_str($_name)."%' OR a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND a.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND a.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND a.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND a.citymunCode = '".$citymunCode."' ";
				}
			}
		}

		$sql .= " ORDER BY date_ordered ASC";

		return $this->db->query($sql)->result_array();
	}

	public function order_item_table($reference_num, $sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);
		// $member_id = $this->session->userdata('sys_users_id');
        // $sys_shop = $this->model_orders->get_sys_shop($member_id);

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
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.srp_amount as amount, a.srp_totalamount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item,
					e.filename as primary_pic, a.exrate_n_to_php as currval, a.currency as currcode, f.itemname as parent_product_name
					FROM `app_order_logs` a
					LEFT JOIN `app_order_details` b on a.order_id = b.order_id
					LEFT JOIN `sys_products` c on c.Id = a.product_id
					LEFT JOIN `sys_shops` d on c.sys_shop = d.id
					LEFT JOIN sys_products_images e ON a.product_id = e.product_id AND e.arrangement = 1 AND e.status = 1
					LEFT JOIN sys_products f ON c.parent_product_id = f.Id
					WHERE b.reference_num = ".$this->db->escape($reference_num). " ";
		} else {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.srp_amount as amount, a.srp_totalamount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item,
				e.filename as primary_pic, a.exrate_n_to_php as currval, a.currency as currcode, f.itemname as parent_product_name
				FROM `app_order_logs` a
				LEFT JOIN `app_order_details` b on a.order_id = b.order_id
				LEFT JOIN `sys_products` c on c.Id = a.product_id
				LEFT JOIN `sys_shops` d on c.sys_shop = d.id
				LEFT JOIN sys_products_images e ON a.product_id = e.product_id AND e.arrangement = 1 AND e.status = 1
				LEFT JOIN sys_products f ON c.parent_product_id = f.Id
				WHERE b.reference_num = ".$this->db->escape($reference_num). " AND a.sys_shop = ".$this->db->escape($sys_shop). " ";
		}


        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);
		$c_international = c_international();
		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/img/'.$row['shopcode'].'/products-250/'.$row['productid'].'/'.removeFileExtension($row['primary_pic']).'.jpg?'.rand().'">';
            $parent_prod  = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : '';
			$nestedData[] = ucwords($parent_prod.$row["itemname"]);
			$nestedData[] = $row["qty"];

			if($c_international == 1 && $sys_shop == 0){

				$item_amount       = currencyConvertedRate($row['amount'], $row['currval']);
				$item_total_amount = currencyConvertedRate_peritem($row['amount'], $row['currval'], $row['qty']);
				
				$nestedData[]      = displayCurrencyValue_withPHP($row['amount'], $item_amount, $row['currcode']);
				$nestedData[]      = displayCurrencyValue_withPHP($row['total_amount'], $item_total_amount, $row['currcode']);
			}
			else if($c_international == 1 && $sys_shop != 0){
				$item_amount       = currencyConvertedRate($row['amount'], $row['currval']);
				$item_total_amount = currencyConvertedRate_peritem($row['amount'], $row['currval'], $row['qty']);
				
				$nestedData[]      = displayCurrencyValue($row['amount'], $item_amount, $row['currcode']);
				$nestedData[]      = displayCurrencyValue($row['total_amount'], $item_total_amount, $row['currcode']);
			}
			else{
				$nestedData[] = number_format($row["amount"], 2);
				$nestedData[] = number_format($row["total_amount"], 2);
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

	public function order_item_table_print($reference_num, $sys_shop){
		if($sys_shop == 0) {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.srp_amount as amount, a.srp_totalamount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item,
					a.exrate_n_to_php as currval, a.currency as currcode, f.itemname as parent_product_name
					FROM `app_order_logs` a
					LEFT JOIN `app_order_details` b on a.order_id = b.order_id
					LEFT JOIN `sys_products` c on c.Id = a.product_id
					LEFT JOIN `sys_shops` d on c.sys_shop = d.id
					LEFT JOIN sys_products f ON c.parent_product_id = f.Id
					WHERE b.reference_num = ".$this->db->escape($reference_num). " ";
		} else {
			$sql=" SELECT d.shopcode, c.itemid as id, c.itemname as itemname, a.quantity as qty, a.srp_amount as amount, a.srp_totalamount as total_amount, c.Id as productid, c.otherinfo as unit, c.sys_shop as shop_id_item,
				a.exrate_n_to_php as currval, a.currency as currcode, f.itemname as parent_product_name
				FROM `app_order_logs` a
				LEFT JOIN `app_order_details` b on a.order_id = b.order_id
				LEFT JOIN `sys_products` c on c.Id = a.product_id
				LEFT JOIN `sys_shops` d on c.sys_shop = d.id
				LEFT JOIN sys_products f ON c.parent_product_id = f.Id
				WHERE b.reference_num = ".$this->db->escape($reference_num). " AND a.sys_shop = ".$this->db->escape($sys_shop). " ";
		}

        return $this->db2->query($sql)->result_array();
	}

	public function get_vouchers_total_shop($reference_num, $sys_shop) {
		$query=" SELECT SUM(amount) as total_voucher_amount FROM app_order_payment
		WHERE status = 1 AND payment_type = 'toktokmall' AND order_ref_num = ? AND shopid = ?";
		$params = array($reference_num, $sys_shop);
		return $this->db2->query($query, $params)->row();
	}

	public function updateInv_fromBranch($product_id, $qty, $branchid, $shopid){

		$sql="INSERT INTO sys_products_invtrans (branchid, product_id, quantity, type, username, date_created, enabled) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array(
			$branchid,
			$product_id,
			$qty,
			'Re-assign orders',
			$this->session->userdata('username'),
			date('Y-m-d H:i:s'),
			1
		);

		$this->db->query($sql, $bind_data);

		$sql="SELECT SUM(quantity) as total_qty FROM sys_products_invtrans WHERE branchid = ? AND product_id = ? AND enabled = 1";
		$bind_data = array(
			$branchid,
			$product_id
		);

		$total_product_qty_perbranch = $this->db->query($sql, $bind_data)->row()->total_qty;

		$sql="UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE status = 1 AND shopid = ? AND branchid = ? AND product_id = ?";
		$bind_data = array(
			$total_product_qty_perbranch,
			$shopid,
			$branchid,
			$product_id
		);

		$this->db->query($sql, $bind_data);

	}

	public function updateInv_toBranch($product_id, $qty, $branchid, $shopid){

		$sql="INSERT INTO sys_products_invtrans (branchid, product_id, quantity, type, username, date_created, enabled) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array(
			$branchid,
			$product_id,
			-$qty,
			'Re-assign orders',
			$this->session->userdata('username'),
			date('Y-m-d H:i:s'),
			1
		);

		$this->db->query($sql, $bind_data);

		$sql="SELECT SUM(quantity) as total_qty FROM sys_products_invtrans WHERE branchid = ? AND product_id = ? AND enabled = 1";
		$bind_data = array(
			$branchid,
			$product_id
		);

		$total_product_qty_perbranch = $this->db->query($sql, $bind_data)->row()->total_qty;

		$sql="UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE status = 1 AND shopid = ? AND branchid = ? AND product_id = ?";
		$bind_data = array(
			$total_product_qty_perbranch,
			$shopid,
			$branchid,
			$product_id
		);

		$this->db->query($sql, $bind_data);
	}

	public function checkinvQty_fromBranch($product_id, $branchid, $shopid){

		$sql="SELECT a.*, b.itemname FROM sys_products_invtrans_branch AS a
		LEFT JOIN sys_products AS b ON a.product_id = b.Id
		WHERE a.shopid = ? AND a.branchid = ? AND a.product_id = ? AND a.status = 1";
		$bind_data = array(
			$shopid,
			$branchid,
			$product_id
		);

		return $this->db->query($sql, $bind_data);
	}

	public function get_pending_orders_table($fromdate, $todate, $shopid = 'all', $branchid = 0, $requestData, $exportable = false)
	{
		$fromdate = $this->db->escape($fromdate);
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate = $this->db->escape(date_format($todate, 'Y-m-d'));

		$columns = ['date_ordered', 'shopname', 'branchname', 'order_status'];

		$shop_filter = ""; $branch_filter = ""; $usertype = 0;
		if ($shopid > 0) {
			$shop_filter = "sys_shop = $shopid";
			if ($branchid > 0) {
				$usertype = 2;
				$branch_filter = "branch_id = $branchid";
			}
			elseif ($branchid == 'all') {
				$branchid = 0;
				// $branch_filter = "branch_id = 0";
			}
		}
		
		$res = $this->model_powb->paid_order_with_branch_query(
			$columns,
			[
				'fromdate' 		=> $fromdate,
				'todate'		=> $todate,
				'shop_id'		=> $shopid,
				'branch_id'		=> $branchid,
				'pmethodtype'	=> 'op',
				'filters'		=> [
					'shop_filter'	=> $shop_filter,
					'branch_filter'	=> $branch_filter,
					'date_filter'	=> "date_ordered BETWEEN $fromdate AND $todate",
					0				=> "order_status IN ('p','po','rp','bc')"
				],
				'group_by'		=> "",
			], $usertype, false, false
		);

		$temp_res = []; $indexes = []; $set_key_ctlrs = explode(", ", "date_ordered, shopname, branchname");
		foreach ($res as $key => $value) {
			$is_key = get_key_ctrl($value, $set_key_ctlrs);
			if (!array_key_exists($is_key, $indexes)) {
				$indexes[$is_key] = count($temp_res);
				$temp_res[] = [
					'date_ordered' 	=> $value['date_ordered'],
					'shopname'		=> $value['shopname'],
					'branchname'	=> $value['branchname'],
					'p'				=> ($value['order_status'] == 'p') ? 1:0,
					'po'			=> ($value['order_status'] == 'po') ? 1:0,
					'rp'			=> ($value['order_status'] == 'rp') ? 1:0,
					'bc'			=> ($value['order_status'] == 'bc') ? 1:0,
				];
			} else {
				$temp_res[$indexes[$is_key]][$value['order_status']] += 1;
			}
		}

		$sql = "SELECT
					DATE(date_ordered) AS date_ordered,
					shopname,
					branchname,
					SUM(IF(order_status = 'p', 1, 0)) AS p,
					SUM(IF(order_status = 'po', 1, 0)) AS po,
					SUM(IF(order_status = 'rp', 1, 0)) AS rp,
					SUM(IF(order_status = 'bc', 1, 0)) AS bc
				FROM `view_paid_orders_with_branch` a
				WHERE date_ordered BETWEEN $fromdate AND $todate AND order_status IN ('p','po','rp','bc')";

		if ($shopid > 0) {
			$sql .= " AND a.shop_id = $shopid";
			if ($branchid > 0) {
				$sql .= " AND a.branch_id = $branchid";
			} elseif ($branchid == 'main') {
				$sql .= " AND a.branch_id = 0";
			}
		}

		$sql .= " GROUP BY DATE(date_ordered), shop_id, branch_id";

		$total_count = $totalFiltered = $totalData = count($res);
		$tfoot = [
			'p' => array_sum(array_pluck($temp_res, 'p')),
			'po' => array_sum(array_pluck($temp_res, 'po')),
			'rp' => array_sum(array_pluck($temp_res, 'rp')),
			'bc' => array_sum(array_pluck($temp_res, 'bc')),
		];

		// $temp

		// $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
		// if (!$exportable) {
		// 	$sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		// }

		$json_data = array(
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"total_transaction" => $total_count,
			"tfoot"				=> $tfoot,
			"data"				=> array_chunk(array_flatten($temp_res), 7)
		);

		return $json_data;
	}

	public function get_pending_orders_chart($fromdate, $todate, $shopid = 'all', $branchid = 0)
	{
		$fromdate = $this->db->escape($fromdate);
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate = $this->db->escape(date_format($todate, 'Y-m-d'));

		$usertype = 0; $shop_filter = ""; $branch_filter = ""; $group_by = "";
		if ($shopid != 'all' && $shopid > 0) {
			$shop_filter = " sys_shop = $shopid";
			$branchid = '';
			if ($branchid > 0) {
				$usertype = 2;
				$branch_filter = " branch_id = $branchid";
			} elseif ($branchid == 'main') {
				$branchid = 0;
				$branch_filter .= " branch_id = 0";
			}
			$key_ctrl = "branchname";
		} else {
			$key_ctrl = "shopname";
		}

		$res = $this->model_powb->paid_order_with_branch_query(
			['shop_id', 'shopname', 'branch_id', 'branchname'],
			[
				'fromdate' => $fromdate, 
				'todate' => $todate,
				'shop_id' => $shopid,
				'branch_id' => $branchid,
				'pmethodtype' => 'op',
				'filters' => [
					'shop_filter' => $shop_filter,
					'branch_filter' => $branch_filter,
					'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
					0 => "order_status IN ('p','po','rp','bc')"
				],
				'group_by' => "",
			], $usertype, false, false
		);
		$data = [];
		foreach ($res as $value) {
			$id = ($key_ctrl == "shopname") ? $value['shopname']:$value['shopname'].".".$value['branchname'];
			if (!array_key_exists($id, $data)) {
				$data[$id] = array_merge($value, ['cnt' => 1]);
			} else {
				$data[$id]['cnt'] += 1;
			}
		}
		uasort($data, build_sorter('cnt', 'desc'));
		$json_data = array(
			"data"				=> $data,
		);

		return $json_data;
	}

	public function updateLongLatCustomer($reference_num, $loc_latitude, $loc_longitude, $notes) {
		$query = "UPDATE app_order_details SET notes = ?, latitude = ?, longitude = ? WHERE reference_num = ?" ;

		$bind_data = array(
			$notes,
			$loc_latitude,
			$loc_longitude,
			$reference_num
		);

		return $this->db->query($query, $bind_data);
	}

	public function checkIfOrderChanged($sys_shop, $reference_num, $status) {
		$query=" SELECT * FROM app_sales_order_details
				WHERE sys_shop = ? AND reference_num = ? AND order_status <> ? AND status = 1";
		$params = array(
			$sys_shop,
			$reference_num,
			$status
		);

		$result = $this->db->query($query, $params)->num_rows();

		if($result > 0){
			return 1;
		}else{
			return 0;
		}
	}

	public function checkIfOrderChanged_fulfilled($sys_shop, $reference_num) {
		$query=" SELECT * FROM app_sales_order_details
				WHERE sys_shop = ? AND reference_num = ? AND order_status IN ('po', 'bc') AND status = 1";
		$params = array(
			$sys_shop,
			$reference_num
		);

		$result = $this->db->query($query, $params)->num_rows();

		if($result > 0){
			return 0;
		}else{
			return 1;
		}
	}

	public function checkbranchOrder($reference_num, $sys_shop) {

		$sql="SELECT a.orderid, a.branchid, b.branchname, b.email FROM sys_branch_orders a 
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id 
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $reference_num, $sys_shop);
        $result = $this->db->query($sql, $data);

        return $result;
	}

	public function get_refundedOrder($reference_num, $sys_shop){
		$sql="SELECT a.*, b.itemname as name_of_item FROM app_refund_orders_details AS a
		LEFT JOIN sys_products AS b ON a.product_id = b.Id
		LEFT JOIN app_refund_orders_summary AS c ON a.summary_id = c.id
		WHERE a.refnum = ? AND is_checked = 1 AND a.sys_shop = ? AND status = 1";
        $data = array($reference_num, $sys_shop);

		return $this->db2->query($sql, $data);
	}

	public function get_primarypics3($product_id){
		$sql = "SELECT * FROM sys_products_images WHERE product_id = ? AND arrangement = 1 AND status = 1";

        $params = array(
            $product_id
		);
		
		$result = $this->db->query($sql, $params)->row();
		$result = (!empty($result->filename)) ? $result->filename : 'none';
		return $result;
	}

	public function getShippingData($_name, $date_from, $date_to){
		$where_string = ($_name != "") ? "reference_num = '".$_name."'" : "created BETWEEN ".$date_from." AND ".$date_to."";
		$sql = "SELECT * FROM app_order_details_shipping WHERE ".$where_string." AND status = 1";

		
		$result = $this->db->query($sql);

		return $result;
	}

	public function getOrderData(){
		$sql = "SELECT * FROM app_order_details WHERE status = 1";

		
		$result = $this->db->query($sql);

		return $result;
	}

	public function getShopData(){
		$sql = "SELECT * FROM sys_shops";

		
		$result = $this->db->query($sql);

		return $result;
	}

	public function getOrderLogs_orderid($orderid){
		$sql = "SELECT quantity as qty, amount, exrate_n_to_php as currval FROM app_order_logs WHERE order_id = ? AND status = 1";
		$params = array($orderid);
		
		$result = $this->db2->query($sql, $params);

		if(empty($result->result_array())){
			$sql = "SELECT quantity as qty, amount, exrate_n_to_php as currval FROM app_sales_order_logs WHERE order_id = ? AND status = 1";
			$params = array($orderid);
			
			$result = $this->db2->query($sql, $params);
		}

		return $result;
	}

	public function get_branchname_id($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db2->query($sql, $data);

        if($result->num_rows() > 0){
            $branchid = $result->row()->branchid;
        }else{
            $branchid = 0;
        }
        return $branchid;
	}

	public function update_branch_pending_orders($branchid, $action, $num = 1){

		if($action == '+'){
			$sql = "UPDATE sys_branch_profile SET pending_orders = (pending_orders + $num)
					WHERE id = $branchid AND status = 1";
		}
		else{
			$sql = "UPDATE sys_branch_profile SET pending_orders = (pending_orders - $num)
					WHERE id = $branchid AND status = 1";
		}

		$this->db->query($sql);
		return ($this->db->affected_rows() > 0) ? true : false;

	}

	public function getDetailsfromShipping($sys_shop, $reference_num){
		$sql = "SELECT * FROM app_order_details_shipping WHERE reference_num = ? AND sys_shop = ?";
		$params = array(
			$reference_num,
			$sys_shop
		);
		
		$result = $this->db->query($sql, $params)->row_array();

		return $result;
	}

	public function getPaidOrder($reference_num) {
		$query="SELECT * FROM app_sales_order_details WHERE reference_num = ?";
		return $this->db->query($query, $reference_num);
	}

    # End - Orders
}
