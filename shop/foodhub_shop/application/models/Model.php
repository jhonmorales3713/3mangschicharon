<?php
class Model extends CI_Model {

	function isLoggedIn() {
			if (!$this->session->userdata('is_logged_in')) {
				 header("location:" . base_url() . "Auth/");
			}
		}

	function validate_username($username){
		$this->db->where('email', $username);
		$this->db->where('status', 1);
		return $this->db->get('jcwfp_users');

	}

	//use  db from jc portal
	function validate_delivery_username($username){
		$this->db->where('email', $username);
		$this->db->where('status', 1);
		return $this->db->get('jcwfp_users');
	}

	function get_userinfo(){
		$user_id = $this->session->userdata('user_id');
		$this->db->where('id', $user_id);
		$this->db->where('status', 1);
		return 	$this->db->get('jcwfp_users')->row_array();
	}

	function update_profile($user_id,  $data){
		$this->db->where('userId', $user_id);
		$this->db->where('status', 1);
		return $this->db->update("jcwfp_users", $data);
	}

	public function getAreas(){
		$this->db->where("status", 1);
		$this->db->order_by("name", "ASC");
		return $this->db->get("sys_delivery_areas");
	}

	public function getCategories(){
		$this->db->select("count(pro.cat_id), cat.id AS category_id, cat.category_name");
		$this->db->where("pro.enabled", 1);
		$this->db->where("cat.status", 1);
		$this->db->group_by("pro.cat_id");
		// $this->db->order_by("count(pro.cat_id)", "DESC");
		$this->db->order_by("priority", "DESC");
		$this->db->join("sys_product_category cat", "cat.id = pro.cat_id", "left");
		$this->db->join("sys_shops sh", "sh.id = pro.sys_shop", "left");
		$this->db->where("sh.status", 1);
		$this->db->limit(9);
		return $this->db->get("sys_products pro");
	}

	public function getEstDelivery($id){
		$this->db->select("mon, tue, wed, thu, fri, sat");
		$this->db->where("areaid", $id);
		$this->db->where("status",1);
		return $this->db->get("cloudpanda-jcwfp_dev.8_areasched");
	}

	public function getProductCat($id){
		$this->db->select("cat.id, cat.description");
		$this->db->join("cloudpanda-jcwfp_dev.8_inventoryfranchise jp", "jp.itemid = inv.id", "left");
		$this->db->join("cloudpanda-jcwfp_dev.8_itemcategory cat", "cat.id = inv.catid", "left");
		$this->db->where("inv.franchiseid", $id);
		$this->db->where("inv.status", 1);
		$this->db->where("jp.status", 1);
		$this->db->where("cat.status", 1);
		$this->db->group_by("cat.id");
		return $this->db->get("cloudpanda-jcwfp_dev.8_inventory inv");
	}

	public function getShippingRate($areaId){

		$this->db->select("ship.sys_shop as shopid, ship.shippingfee as sf, ship.daystoship as dts");
		$this->db->where("ship.areaid", $areaId);
		$this->db->where("ship.status", 1);
		return $this->db->get("sys_shop_shipping ship");
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

	function validate_referral_code($referralcode){
		$this->db->where('code_name', $referralcode);
		$this->db->where('code_type', 1); //referral code type
		$this->db->where('status', 1);
		$this->db->where('is_active', 1);
		return $this->db->get('sys_codes');

	}

	function get_banners(){
		$sql = "SELECT * FROM `sys_banners` WHERE status = 1 ORDER BY `sys_banners`.`sorting` DESC LIMIT 6";
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0) {
			return $query = array();
		}else{
			return $query->result_array();
		}
	}

	function getAreaById($areaId) {
		$sql = "SELECT name FROM `sys_delivery_areas` WHERE id = ?";
		$query = $this->db->query($sql, $areaId);

		if ($query->num_rows() > 0) {
			return $query->row()->name;
		}else{
			return "";
		}
	}
	
	public function get_custom_shipping_per_product($product_id, $citymunCode){
		// Try to get if there's city specific zone first
		$shippingPerShop = [];
		$query = "SELECT c.*  FROM sys_shipping_zone_products AS a
		LEFT JOIN sys_shipping_zone AS b ON a.shipping_zone_id = b.id
		INNER JOIN sys_shipping_zone_rates AS c ON b.id = c.sys_shipping_zone_id
		LEFT JOIN sys_shipping AS d ON d.id = b.sys_shipping_id ";

		$append = " AND a.product_id = ? AND a.enabled = 1 AND b.enabled AND c.enabled = 1 AND d.enabled = 1 AND d.profile_name <> 'General Shipping' ";
		$query1 = $query . " WHERE b.citymunCode = ? AND b.provCode != 0 AND b.regCode != 0" . $append;

		$params1 = array($citymunCode, $product_id);
		$result1 = $this->db->query($query1, $params1)->result_array();
		if (sizeof($result1) > 0) {
			$shippingPerShop = $result1;
		}

		// Try to get if there's prov specific if no result set from above
		if (sizeof($shippingPerShop) == 0) {
			$query2 = $query . " WHERE b.provCode = (SELECT provCode FROM sys_citymun WHERE citymunCode = ?) AND b.citymunCode = 0 AND b.regCode != 0" . $append;

			$params2 = array($citymunCode, $product_id);
			$result2 = $this->db->query($query2, $params2)->result_array();
			if (sizeof($result2) > 0) {
				$shippingPerShop = $result1;
			}
		}

		// Try to get if there's reg specific if no result set from above
		if (sizeof($shippingPerShop) == 0) {
			$query3 = $query . "
			WHERE b.regCode = (SELECT regDesc FROM sys_citymun WHERE citymunCode = ?) AND b.citymunCode = 0 AND b.provCode = 0" . $append;

			$params3 = array($citymunCode, $product_id);
			$result3 = $this->db->query($query3, $params3)->result_array();
			if (sizeof($result3) > 0) {
				$shippingPerShop = $result3;
			}
		}
		//print_r($query1);
		return $shippingPerShop;
	}

	public function get_custom_shipping_per_product_branch($shop_id, $branch_id, $product_id, $citymunCode){
		$shop_id = $this->db->escape($shop_id);
		$branch_id = $this->db->escape($branch_id);
		$product_id = $this->db->escape($product_id);
		$citymunCode = $this->db->escape($citymunCode);
		$shipping = [];
		$sql = "SELECT c.*  FROM sys_shipping_zone_products AS a
			INNER JOIN sys_shipping_zone AS b ON a.shipping_zone_id = b.id
			INNER JOIN sys_shipping_zone_rates AS c ON b.id = c.sys_shipping_zone_id
			LEFT JOIN sys_shipping AS d ON d.id = b.sys_shipping_id
			LEFT JOIN sys_shipping_zone_branch e ON b.id = e.shipping_zone_id";
		$append = " AND e.shop_id = $shop_id AND e.branch_id = $branch_id AND a.product_id = $product_id AND d.profile_name != 'General Shipping' AND a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1 AND e.enabled = 1";
		$query1 = $sql." WHERE b.citymunCode = $citymunCode AND b.provCode != 0 AND b.regCode != 0".$append;
		$result1 = $this->db->query($query1);
		if($result1->num_rows() > 0){
			$shipping = $result1->result_array();
		}

		if(sizeof($shipping) == 0){
			$query2 = $sql." WHERE b.provCode = (SELECT provCode FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1) AND b.citymunCode = 0 AND b.regCode != 0".$append;
			$result2 = $this->db->query($query2);
			if($result2->num_rows() >0){
				$shipping = $result2->result_array();
			}
		}

		if(sizeof($shipping) == 0){
			$query3 = $sql." WHERE b.regCode = (SELECT regDesc FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1) AND b.citymunCode = 0 AND b.provCode = 0".$append;
			$result3 = $this->db->query($query3);
			if($result3->num_rows() >0){
				$shipping = $result3->result_array();
			}
		}
		return $shipping;
		// $data = array($shop_id, $branch_id, $product_id);
		// return $this->db->query($sql,$data);
	}

	public function get_custom_shipping_per_branch($shop_id, $branch_id, $citymunCode){
		$shop_id = $this->db->escape($shop_id);
		$branch_id = $this->db->escape($branch_id);
		$citymunCode = $this->db->escape($citymunCode);
		$shipping = [];
		$sql = "SELECT c.* 
			FROM sys_shipping_zone AS b
			INNER JOIN sys_shipping_zone_rates AS c ON b.id = c.sys_shipping_zone_id
			LEFT JOIN sys_shipping AS d ON d.id = b.sys_shipping_id
			LEFT JOIN sys_shipping_zone_branch e ON b.id = e.shipping_zone_id";
		$append = " AND e.shop_id = $shop_id AND e.branch_id = $branch_id AND d.profile_name != 'General Shipping' AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1 AND e.enabled = 1";
		$query1 = $sql." WHERE b.citymunCode = $citymunCode AND b.provCode != 0 AND b.regCode != 0".$append;
		$result1 = $this->db->query($query1);
		if($result1->num_rows() > 0){
			$shipping = $result1->result_array();
		}

		if(sizeof($shipping) == 0){
			$query2 = $sql." WHERE b.provCode = (SELECT provCode FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1) AND b.citymunCode = 0 AND b.regCode != 0".$append;
			$result2 = $this->db->query($query2);
			if($result2->num_rows() >0){
				$shipping = $result2->result_array();
			}
		}

		if(sizeof($shipping) == 0){
			$query3 = $sql." WHERE b.regCode = (SELECT regDesc FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1) AND b.citymunCode = 0 AND b.provCode = 0".$append;
			$result3 = $this->db->query($query3);
			if($result3->num_rows() >0){
				$shipping = $result3->result_array();
			}
		}
		return $shipping;
		// $data = array($shop_id, $branch_id, $product_id);
		// return $this->db->query($sql,$data);
	}

	public function get_general_shipping_per_shop($shop_id, $citymunCode){
		// Try to get if there's city specific zone first
		$sid = $this->db->escape($shop_id);
		$shippingPerShop = [];
		$query = "SELECT c.*, b.regCode, b.provCode, b.citymunCode  FROM sys_shipping AS a
		LEFT JOIN sys_shipping_zone AS b ON a.id = b.sys_shipping_id
		LEFT JOIN sys_shipping_zone_rates AS c ON b.id = c.sys_shipping_zone_id";

		$append = " AND a.sys_shop_id = ? AND a.enabled = 1 AND b.enabled  = 1
			AND c.enabled = 1 AND a.profile_name = 'General Shipping'
			AND b.id NOT IN (SELECT * FROM (SELECT shipping_zone_id FROM sys_shipping_zone_branch WHERE enabled = 1 AND shop_id = $sid) as szb)";
		$query1 = $query . " WHERE b.citymunCode = ? AND b.provCode != 0 AND b.regCode != 0" . $append;

		$params1 = array($citymunCode, $shop_id);
		$result1 = $this->db->query($query1, $params1)->result_array();
		if (sizeof($result1) > 0) {
			$shippingPerShop = $result1;
		}

		// Try to get if there's prov specific if no result set from above
		if (sizeof($shippingPerShop) == 0) {
			$query2 = $query . " WHERE b.provCode = (SELECT provCode FROM sys_citymun WHERE citymunCode = ?) AND b.citymunCode = 0 AND b.regCode != 0" . $append;

			$params2 = array($citymunCode, $shop_id);
			$result2 = $this->db->query($query2, $params2)->result_array();
			if (sizeof($result2) > 0) {
				// $shippingPerShop = $result1;
				$shippingPerShop = $result2;
			}
		}

		// Try to get if there's prov specific if no result set from above
		if (sizeof($shippingPerShop) == 0) {
			$query3 = $query . " WHERE b.regCode = (SELECT regDesc FROM sys_citymun WHERE citymunCode = ?) AND b.citymunCode = 0 AND b.provCode = 0" . $append;

			$params3 = array($citymunCode, $shop_id);
			$result3 = $this->db->query($query3, $params3)->result_array();
			if (sizeof($result3) > 0) {
				$shippingPerShop = $result3;
			}
		}

		return $shippingPerShop;
	}

	public function get_general_shipping_of_branch($shop_id,$branch_id,$citymunCode){
		$shop_id = $this->db->escape($shop_id);
		$branch_id = $this->db->escape($branch_id);
		$citymunCode = $this->db->escape($citymunCode);
		$shipping = [];

		$sql = "SELECT c.*, a.regCode, a.provCode, a.citymunCode
			FROM sys_shipping_zone a
			INNER JOIN sys_shipping_zone_branch b ON a.id = b.shipping_zone_id
			INNER JOIN sys_shipping_zone_rates c ON a.id = c.sys_shipping_zone_id
			INNER JOIN sys_shipping d ON a.sys_shipping_id = d.id";

		$append = " AND b.shop_id = $shop_id AND b.branch_id = $branch_id AND a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1 AND d.profile_name = 'General Shipping'";
		$query1 = $sql." WHERE a.citymunCode = $citymunCode AND a.provCode != 0 AND a.regCode != 0".$append;
		$result1 = $this->db->query($query1);
		if($result1->num_rows() > 0){
			$shipping = $result1->result_array();
		}

		if(sizeof($shipping) == 0){
			$query2 = $sql." WHERE a.provCode = (SELECT provCode FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1) AND a.citymunCode = 0 AND a.regCode != 0".$append;
			$result2 = $this->db->query($query2);
			if($result2->num_rows() >0){
				$shipping = $result2->result_array();
			}
		}

		if(sizeof($shipping) == 0){
			$query3 = $sql." WHERE a.regCode = (SELECT regDesc FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1) AND a.citymunCode = 0 AND a.provCode = 0".$append;
			$result3 = $this->db->query($query3);
			if($result3->num_rows() >0){
				$shipping = $result3->result_array();
			}
		}
		// return $this->db->last_query();
		return $shipping;


		// $data = array($shop_id,$branch_id,$citymunCode);
		// $result = $this->db->query($sql,$data);
		// if($result->num_rows() == 0){
		// 	$citymunCode = $this->db->escape($citymunCode);
		// 	$sql .= " AND
		// 	(a.provcode = (SELECT provCode FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1)
		// 		OR a.regCode = (SELECT regDesc FROM sys_citymun WHERE citymunCode = $citymunCode AND status = 1))";
		// 	$result = $this->db->query($sql);
		// }
		//
		// return $result;
	}

	public function get_region(){
		$query="SELECT * FROM sys_region WHERE status = 1 ORDER BY regDesc";

		return $this->db->query($query);
	}

	public function get_province($regCode){
		$query = "SELECT * FROM sys_prov WHERE regCode = ? AND status = 1 ORDER BY provDesc";
		$params = array($regCode);

		return $this->db->query($query, $params);
	}

	public function get_citymun($regCode){
		$query = "SELECT *, (SELECT provDesc FROM sys_prov WHERE regCode = ? AND provCode = a.provCode LIMIT 1) as provDesc
		FROM sys_citymun as a
		WHERE regDesc = ? AND status = 1 ";

		if($regCode == '13')
			$query .= "ORDER BY a.citymunDesc";
		else
			$query .= "ORDER BY provDesc, a.citymunDesc";
		$params = array($regCode,$regCode);

		return $this->db->query($query, $params);
	}

	public function get_brgy($citymunCode) {
		$query = "SELECT * FROM sys_brgy WHERE citymunCode = ? AND status = 1 ORDER BY brgyDesc";
		$params = array($citymunCode);

		return $this->db->query($query, $params);
	}

	public function get_shopbranch($shopid, $auto_assign = "") {

		$bind_data = [];
		$query = " SELECT
				sbm.id,
				sbm.mainshopid,
				sbm.branchid,
				sbp.branchname,
				sbp.contactperson,
				sbp.mobileno,
				sbp.email,
				sbp.address,
				sbp.branch_city,
				sbp.branch_region,
				sbp.city delivery_city,
				sbp.province delivery_province,
				sbp.region delivery_region,
				sbp.isautoassign,
				sbp.latitude,
				sbp.longitude,
				sbp.pending_orders as branch_pending_orders,
				sbp.inv_threshold,
				sbp.last_ordered as last_order,
				sbp.on_hold
		FROM sys_branch_mainshop sbm
		LEFT JOIN sys_branch_profile sbp
		ON sbm.branchid = sbp.id
		WHERE sbm.mainshopid = ?
		AND sbm.status = 1
		AND sbp.status = 1 ";

		$bind_data[] = $shopid;

		if ($auto_assign != "" && in_array($auto_assign, [1,0])) {
			$query .= " AND sbp.isautoassign = ? ";
			$bind_data[] = $auto_assign;
		}

		return $this->db->query($query, $bind_data)->result_array();
	}

	public function get_branch_pending_order($shopid,$branchid){
		$shopid = $this->db->escape($shopid);
		$branchid = $this->db->escape($branchid);
		$sql = "SELECT COUNT(a.id) as branch_pending_orders
			FROM sys_branch_orders a
			INNER JOIN app_sales_order_details b ON a.orderid = b.reference_num
			WHERE b.order_status = 'p' AND a.branchid = $branchid AND b.sys_shop = $shopid
			AND a.status = 1 AND b.status = 1";
		$result = $this->db->query($sql);
		if($result->num_rows() > 0){
			return $result->row()->branch_pending_orders;
		}else{
			return 0;
		}
	}

	public function get_last_branch_ordered($shopid,$branchid){
		$shopid = $this->db->escape($shopid);
		$branchid = $this->db->escape($branchid);
		$sql = "SELECT date_ordered as last_order
			FROM sys_branch_orders a
			INNER JOIN app_sales_order_details b ON a.orderid = b.reference_num
			WHERE a.branchid = $branchid AND b.sys_shop = $shopid
			AND a.status = 1 AND b.status = 1
			ORDER BY date_ordered DESC LIMIT 1";
		return $this->db->query($sql)->row()->last_order;
	}

	public function assign_order_to_branch($branchid, $orderid, $remarks){
    if($this->check_if_exist_to_branch($orderid) > 0){
      $sql="UPDATE sys_branch_orders SET status = ? WHERE status = ? AND orderid = ?";
      $data = array(0, 1, $orderid);
      $this->db->query($sql, $data);
    }
    if(!empty($branchid) || $branchid != ""){
      $sql="INSERT INTO sys_branch_orders (branchid, orderid, status, remarks) VALUES (?, ?, ?, ?)";
      $data = array($branchid, $orderid, 1, $remarks);
      $this->db->query($sql, $data);
    }
  }

	public function set_order_to_branch($branchid, $orderid, $remarks){
		// if(!empty($branchid) || $branchid != ""){
      $sql="INSERT INTO sys_branch_orders (branchid, orderid, status, remarks) VALUES (?, ?, ?, ?)";
      $data = array($branchid, $orderid, 1, $remarks);
      $this->db->query($sql, $data);
    // }
	}

  public function check_if_exist_to_branch($orderid){
    $sql="SELECT id FROM sys_branch_orders WHERE status = ? AND orderid = ?";
    $data = array(1, $orderid);
    return $this->db->query($sql, $data)->num_rows();
  }

  public function get_specific_city($citymunCode) {
  	$sql = "SELECT regDesc as regCode, provCode, citymunCode FROM sys_citymun WHERE citymunCode = ?";

  	return $this->db->query($sql, $citymunCode)->row_array();
  }

	public function get_cities(){
		$query = "SELECT a.*, b.provDesc
		FROM sys_citymun as a
		LEFT JOIN sys_prov as b ON a.provCode = b.provCode
		WHERE a.status = 1 AND b.status = 1
		ORDER BY a.citymunDesc, b.provDesc";

		return $this->db->query($query);
	}

	public function get_filtered_cities_backup(){
		$query = "SELECT 
							  * 
							FROM
							  (SELECT 
							    b.*,
							    c.provDesc 
							  FROM
							    sys_shipping_zone a 
							    LEFT JOIN sys_citymun b 
							      ON (
							        CASE
							          WHEN a.citymunCode = 0 
							          AND a.provCode != 0 
							          THEN a.provCode = b.provCode 
							          AND b.status = 1 
							          WHEN a.citymunCode = 0 
							          AND a.provCode = 0 
							          AND a.regCode != 0 
							          THEN a.regCode = b.regDesc 
							          AND b.status = 1 
							          ELSE a.citymunCode = b.citymunCode 
							          AND b.status = 1 
							        END
							      ) 
							    LEFT JOIN sys_shipping_zone_branch d 
							      ON d.shipping_zone_id = a.id 
							    LEFT JOIN sys_prov c 
							      ON c.provCode = b.provCode 
							      AND c.status = 1 
							  WHERE a.enabled = 1 
							    AND b.status = 1 
							    AND c.status = 1 
							    AND d.enabled = 1 
							  UNION
							  SELECT 
							    b.*,
							    c.provDesc 
							  FROM
							    sys_shipping_zone a 
							    LEFT JOIN sys_citymun b 
							      ON (
							        CASE
							          WHEN a.citymunCode = 0 
							          AND a.provCode != 0 
							          THEN a.provCode = b.provCode 
							          AND b.status = 1 
							          WHEN a.citymunCode = 0 
							          AND a.provCode = 0 
							          AND a.regCode != 0 
							          THEN a.regCode = b.regDesc 
							          AND b.status = 1 
							          ELSE a.citymunCode = b.citymunCode 
							          AND b.status = 1 
							        END
							      ) 
							    LEFT JOIN sys_shipping_zone_products d 
							      ON d.shipping_zone_id = a.id 
							    LEFT JOIN sys_prov c 
							      ON c.provCode = b.provCode 
							      AND c.status = 1 
							  WHERE a.enabled = 1 
							    AND b.status = 1 
							    AND c.status = 1 
							    AND d.enabled = 1) AS table1
							GROUP BY citymunCode 
							ORDER BY citymunDesc";

		return $this->db->query($query);
	}

	public function get_filtered_cities(){
		$query = "SELECT b.*, c.provDesc FROM
			sys_shipping_zone a
			INNER JOIN sys_citymun b
			ON (CASE
			    WHEN a.citymunCode = 0 AND a.provCode != 0 THEN a.provCode = b.provCode AND b.status = 1 AND a.enabled = 1
			    WHEN a.citymunCode = 0 AND a.provCode = 0 AND a.regCode != 0 THEN a.regCode = b.regDesc AND b.status = 1 AND a.enabled = 1
			    ELSE a.citymunCode = b.citymunCode AND b.status = 1 END)
			LEFT JOIN sys_prov c ON c.provCode = b.provCode AND c.status = 1
			WHERE a.enabled = 1 AND b.status = 1 AND c.status = 1
			GROUP BY b.citymunCode
			ORDER BY b.citymunDesc";

		return $this->db->query($query);
	}

	public function get_region_prov($citymunCode){
		$query = "SELECT regDesc, provCode
		FROM sys_citymun as a
		WHERE a.status = 1 and a.citymunCode = ?";

		return $this->db->query($query, $citymunCode)->row_array();
	}

	public function get_ship_from_details($col1, $col2, $table, $in) {
		$query = " SELECT $col1 FROM $table WHERE $col2 in ($in) AND status = 1";

		return $this->db->query($query)->result_array();
	}

	public function insertReferralLog($data){
		$this->db->insert("app_referral_link_logs", $data);
		return $this->db->insert_id();
	}

	public function get_shipping_city($regCode){
		$regCode = $this->db->escape($regCode);
		$sql = "SELECT a.* FROM sys_citymun a
			INNER JOIN sys_shipping_zone b ON a.citymunCode = b.citymunCode
			WHERE a.status = 1 AND b.enabled = 1 AND b.citymunCode != 0
			AND a.regDesc = $regCode GROUP BY b.citymunCode ORDER BY a.citymunDesc ASC";
		return $this->db->query($sql);
	}

	public function get_shipping_city_w_prov($provCode){
		$provCode = $this->db->escape($provCode);
		$sql = "SELECT a.* FROM sys_citymun a
			INNER JOIN sys_shipping_zone b ON a.citymunCode = b.citymunCode
			WHERE a.status = 1 AND b.enabled = 1 AND b.citymunCode != 0
			AND a.provCode = $provCode GROUP BY b.citymunCode ORDER BY a.citymunDesc ASC";
		return $this->db->query($sql);
	}

	public function get_shipping_prov($prov_code){
		$prov_code = $this->db->escape($prov_code);
		$sql = "SELECT a.*
			FROM sys_prov a
			INNER JOIN sys_shipping_zone b ON a.provCode = b.provCode
			WHERE a.status = 1 AND b.enabled = 1 AND b.provCode != 0
			AND b.provCode = $prov_code GROUP BY b.provCode ORDER BY a.provDesc";
		return $this->db->query($sql);
	}

	public function automate_updateProductImg(){
		$sql=" SELECT a.Id as product_id, b.shopcode FROM sys_products AS a
			LEFT JOIN sys_shops AS b ON a.sys_shop = b.id
			LEFT JOIN sys_products_images AS c ON a.Id = c.product_id
			WHERE a.enabled > 0 AND c.product_id IS NULL";

        return $this->db->query($sql)->result_array();;
	}

	public function update_productImgUrl($product_id, $filename, $arrangement){

		if(!empty($filename)){
			$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
			$bind_data = array(
				$product_id,
				$arrangement,
				$filename,
				date('Y-m-d H:i:s'),
				1
			);

			$this->db->query($sql, $bind_data);
		}
	}

	public function get_shop_inv_threshold($shopid){
		$shopid = $this->db->escape($shopid);
		$sql = "SELECT inv_threshold FROM sys_shops WHERE status = 1 AND id = $shopid";
		return $this->db->query($sql);
	}

	public function get_branch_inv_threshold($branchid){
		$branchid = $this->db->escape($branchid);
		$sql = "SELECT inv_threshold FROM sys_branch_profile WHERE status = 1 AND id = $branchid";
		return $this->db->query($sql);
	}

	public function update_on_hold_status($branchid,$status = 0){
		$branchid = $this->db->escape($branchid);
		$status = $this->db->escape($status);

		$sql = "UPDATE sys_branch_profile SET on_hold = $status WHERE id = $branchid AND status = 1";
		$this->db->query($sql);
	}


	public function get_prov_variant_price($productid,$provCode){
		if($provCode==""){
			$provCode="undefined";
		}
		$sql = "SELECT min(price) as price
			FROM sys_products
			WHERE parent_product_id IS NOT NULL AND enabled = 1 AND `delivery_areas` LIKE '%{$provCode}%' AND parent_product_id= '{$productid}'";
    $result = $this->db->query($sql);
    $price = $result->row()->price;
		return $price;
	}


	public function get_prov_variant($productid,$provCode){
		if($provCode==""){
			$provCode="undefined";
		}
		$sql = "SELECT a.*, b.no_of_stocks as stocks, b.branchid
			FROM sys_products a
			LEFT JOIN sys_products_invtrans_branch b
			ON a.Id = b.product_id AND a.sys_shop = b.shopid
			WHERE parent_product_id IS NOT NULL AND enabled = 1 AND `delivery_areas` LIKE '%{$provCode}%' AND parent_product_id= '{$productid}' ORDER BY price ASC LIMIT 1";
    $result = $this->db->query($sql)->result_array();
		return $result;
	}


	public function get_prov_variant_id($productid,$provCode){
		if($provCode==""){
			$provCode="undefined";
		}
		$sql = "SELECT min(price) as price, Id
			FROM sys_products
			WHERE parent_product_id IS NOT NULL AND enabled = 1 AND `delivery_areas` LIKE '%{$provCode}%' AND parent_product_id= '{$productid}' AND is_availability = 1 AND enabled = 1";
    $result = $this->db->query($sql);
		$Id = $result->row()->Id;
		return $Id;
	}


	// public function get_prov_variant_id($productid,$provCode){
	// 	if($provCode==""){
	// 		$provCode="undefined";
	// 	}
	// 	$sql = "SELECT min(price) as price, Id as productid
	// 		FROM sys_products
	// 		WHERE parent_product_id IS NOT NULL AND enabled = 1 AND `delivery_areas` LIKE '%{$provCode}%' AND parent_product_id= '{$productid}'";
 //    $result = $this->db->query($sql);
 //    $Id = $result->row()->productid;
	// 	return $Id;
	// }

	public function get_default_price($productid){
		$sql = "SELECT price
			FROM sys_products
			WHERE Id='{$productid}' AND enabled = 1";
    $result = $this->db->query($sql);
    $price = $result->row()->price;
		return $price;
	}

	// public function get_prov_variant_price($productid,$provCode){
	// 	if($provCode==""){
	// 		$provCode="undefined";
	// 	}
	// 	$sql = "SELECT min(price) as price, Id
	// 		FROM sys_products
	// 		WHERE parent_product_id IS NOT NULL AND enabled = 1 AND `delivery_areas` LIKE '%{$provCode}%' AND parent_product_id= '{$productid}' AND price <> '' 
	// 	  GROUP BY Id";
 //    $result = $this->db->query($sql)->result_array();
	// 	return $result;
	// }

	// public function get_default_price($productid){
	// 	$sql = "SELECT *
	// 		FROM sys_products
	// 		WHERE Id='{$productid}' AND enabled = 1";
 //    $result = $this->db->query($sql)->result_array();
	// 	return $result;
	// }

	// public function get_parent_id($productid){
	// 	$sql = "SELECT *
	// 		FROM sys_products
	// 		WHERE Id='{$productid}' AND enabled = 1 AND parent_product_id <> ''";
 //    $result = $this->db->query($sql)->result_array();
	// 	return $result;
	// }
}
