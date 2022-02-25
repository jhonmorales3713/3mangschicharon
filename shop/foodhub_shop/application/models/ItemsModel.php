<?php
class ItemsModel extends CI_Model {

	function getProducts($id, $data){
		if($data['page'] == 'shop' && (isset($this->session->get_products) && $this->session->get_products != [])){

			// if product count and total product is equal use session products
			if($this->get_products_count() == $this->session->get_products_total){
				$result = $this->session->get_products;
				$totalData = $this->session->get_products_total;
				if ($data['page'] !== 'search' && c_default_order() == 1) {
					shuffle($result);
				}
				return array('total' => $totalData, 'results' => $result);
			}

		}
		$query=" SELECT a.*, a.itemname as parent_itemname, b.shopcode, b.shopurl, b.shopname, c.id as category,
		(IF(a.parent_product_id IS NULL, a.itemname,(SELECT itemname FROM sys_products WHERE id = a.parent_product_id))) as itemname,
		@sid := b.id as sid,
		@primary_pics := (SELECT filename FROM sys_products_images WHERE arrangement = 1 AND product_id = a.Id AND status = 1) as primary_pics
		FROM sys_products a
		LEFT JOIN sys_shops b ON a.sys_shop = b.id
		LEFT JOIN sys_product_category c ON a.cat_id = c.id
			WHERE a.enabled = 1 and b.status = 1";

		// 011122
		if (!empty($this->session->userdata("user_id")) && $this->session->userdata("user_type") == "JC") {
			$query .= " AND a.is_availability IN (1,2)"; // for reseller login only
		}else{
			$query .= " AND a.is_availability = 1"; // for public
		}
		// 011122

		$bind_data = array();

		if($data['keyword'] != null) {
			$query.=" AND (itemname LIKE ? OR tags LIKE ? OR category_name LIKE ? ) ";
			array_push($bind_data, "%".$this->db->escape_like_str($data['keyword'])."%", "%".$this->db->escape_like_str($data['keyword'])."%", "%".$this->db->escape_like_str($data['keyword'])."%");
		}

		if((isset($data['price_min']) && $data['price_min'] != "") || (isset($data['price_max']) && $data['price_max'] != "")) {
			if ($data['price_min'] != "" && $data['price_max'] != "") {
				$query.=" AND (a.price >= ? AND a.price <= ?)";
				array_push($bind_data, sanitize($data['price_min']), sanitize($data['price_max']));
			} else if ($data['price_min'] != "" && $data['price_max'] == "") {
				$query.=" AND (a.price >= ?)";
				array_push($bind_data, sanitize($data['price_min']));
			} else if ($data['price_min'] == "" && $data['price_max'] != "") {
				$query.=" AND (a.price <= ?)";
				array_push($bind_data, sanitize($data['price_max']));
			}
		}

		if($data['category'] != null) {
			$data['category'] = sanitize($data['category']);
			$cats = explode(",", $data['category']);

			$in = trim(str_repeat('?,', count($cats)), ',');
			$query.=" AND a.cat_id IN ($in)";
			foreach($cats as $cat) {
				array_push($bind_data, $cat);
			}

		}

		$zone_query = "";

		// cities
		if($data['cities'] != null){
			$arr = explode(',',$data['cities']);
			$shipped = "";
			$i = count($arr);
			$n = 0;
			foreach($arr as $r){
				if(++$n === $i){
					$shipped .= "'".$r."'";
				}else{
					$shipped .= "'".$r."',";
				}
			}

			$sql = "SELECT a.id, c.product_id, a.sys_shop_id, a.is_custom
							FROM sys_shipping a
							INNER JOIN sys_shipping_zone b ON a.id = b.sys_shipping_id
							LEFT JOIN sys_shipping_zone_products c ON c.shipping_zone_id = b.id AND c.enabled = 1
							WHERE a.enabled = 1 AND b.enabled = 1 AND b.citymunCode IN ($shipped)";
			$zone = $this->db->query($sql);
			// $zone_query = "";
			if($zone->num_rows() > 0){
				foreach($zone->result_array() as $key => $z){
					$sys_shop_id = $this->db->escape($z['sys_shop_id']);
					if($zone_query == ""){
						if($z['is_custom'] == 0){
							$zone_query .= "b.id = $sys_shop_id";
						}else{
							$product_id = $this->db->escape($z['product_id']);
							$zone_query .= "(b.id = $sys_shop_id AND a.Id = $product_id)";
						}
					}else{
						if($z['is_custom'] == 0){
							$zone_query .= " OR b.id = $sys_shop_id";
						}else{
							$product_id = $this->db->escape($z['product_id']);
							$zone_query .= " OR (b.id = $sys_shop_id AND a.Id = $product_id)";
						}
					}

				}

				// $query .= " AND ($zone_query)";
			}else{
				$zone_query = "b.id = 0";
			}

		}

		// provinces
		if($data['province'] != null){
			$arr = explode(',',$data['province']);
			$shipped = "";
			$i = count($arr);
			$n = 0;
			foreach($arr as $r){
				if(++$n === $i){
					$shipped .= "'".$r."'";
				}else{
					$shipped .= "'".$r."',";
				}
			}

			$sql = "SELECT a.id, c.product_id, a.sys_shop_id, a.is_custom
							FROM sys_shipping a
							INNER JOIN sys_shipping_zone b ON a.id = b.sys_shipping_id
							LEFT JOIN sys_shipping_zone_products c ON c.shipping_zone_id = b.id AND c.enabled = 1
							WHERE a.enabled = 1 AND b.enabled = 1 AND b.provCode IN ($shipped)";
			$zone = $this->db->query($sql);
			// $zone_query = "";
			if($zone->num_rows() > 0){
				foreach($zone->result_array() as $key => $z){
					$sys_shop_id = $this->db->escape($z['sys_shop_id']);
					if($zone_query == ""){
						if($z['is_custom'] == 0){
							$zone_query .= "b.id = $sys_shop_id";
						}else{
							$product_id = $this->db->escape($z['product_id']);
							$zone_query .= "(b.id = $sys_shop_id AND a.Id = $product_id)";
						}
					}else{
						if($z['is_custom'] == 0){
							$zone_query .= " OR b.id = $sys_shop_id";
						}else{
							$product_id = $this->db->escape($z['product_id']);
							$zone_query .= " OR (b.id = $sys_shop_id AND a.Id = $product_id)";
						}
					}

				}

				// $query .= " AND ($zone_query)";
			}else{
				$zone_query = "b.id = 0";
			}

		}

		// regions
		if($data['shipped'] != null){
			$arr = explode(',',$data['shipped']);
			$shipped = "";
			$i = count($arr);
			$n = 0;
			foreach($arr as $r){
				if(++$n === $i){
					$shipped .= "'".$r."'";
				}else{
					$shipped .= "'".$r."',";
				}
			}

			$sql = "SELECT a.id, c.product_id, a.sys_shop_id, a.is_custom
							FROM sys_shipping a
							INNER JOIN sys_shipping_zone b ON a.id = b.sys_shipping_id
							LEFT JOIN sys_shipping_zone_products c ON c.shipping_zone_id = b.id AND c.enabled = 1
							WHERE a.enabled = 1 AND b.enabled = 1 AND b.regCode IN ($shipped)";
			$zone = $this->db->query($sql);
			// $zone_query = "";
			if($zone->num_rows() > 0){
				foreach($zone->result_array() as $key => $z){
					$sys_shop_id = $this->db->escape($z['sys_shop_id']);
					if($zone_query == ""){
						if($z['is_custom'] == 0){
							$zone_query .= "b.id = $sys_shop_id";
						}else{
							$product_id = $this->db->escape($z['product_id']);
							$zone_query .= "(b.id = $sys_shop_id AND a.Id = $product_id)";
						}
					}else{
						if($z['is_custom'] == 0){
							$zone_query .= " OR b.id = $sys_shop_id";
						}else{
							$product_id = $this->db->escape($z['product_id']);
							$zone_query .= " OR (b.id = $sys_shop_id AND a.Id = $product_id)";
						}
					}

				}

				// $query .= " AND ($zone_query)";
			}else{
				$zone_query = "b.id = 0";
			}
		}

		if($zone_query != ""){
			$query .= " AND ($zone_query)";
		}else{
			// $query .= " AND b.id = 0";
		}

		// die($query);

		if($data['page'] == 'products') {
			$query.=" AND a.Id != ? ";
			// $query.=" ORDER BY c.priority DESC, itemname LIMIT 12";
			$query.=" ORDER BY (CASE
					WHEN a.sys_shop = ? AND a.cat_id = ? THEN 1
					WHEN a.sys_shop = ? THEN 2
					WHEN a.cat_id IN (?) THEN 3
					ELSE 4
			END), itemname LIMIT 12";

			array_push($bind_data, sanitize($data['id']), sanitize($data['shop']), sanitize($data['category']), sanitize($data['shop']), sanitize($data['category']));
		}
		else if($data['page'] == 'store') {
			$query.=" AND a.sys_shop = ? ";
			$query.=" ORDER BY (CASE
					WHEN a.sys_shop = ? AND a.cat_id = ? THEN 1
					WHEN a.sys_shop = ? THEN 2
					WHEN a.cat_id IN (?) THEN 3
					ELSE 4
			END), itemname";

			array_push($bind_data, sanitize($data['shop']), sanitize($data['shop']), sanitize($data['category']), sanitize($data['shop']), sanitize($data['category']));
		}
		else if($data['page'] == 'shop'){
			$query.=" ORDER BY (CASE WHEN (a.no_of_stocks > 0 AND a.tq_isset = 1) OR a.tq_isset = 0 THEN 1 ELSE 2 END), a.date_created";
		}
		else {
			if (isset($data['sort_value']) && $data['sort_order']) {
				$sort_value = sanitize($data['sort_value']);
				$sort_order = sanitize($data['sort_order']);
				if($sort_value == 'price') $sort_value = "price";
				$query.=" ORDER BY a.".$sort_value." ".$sort_order;
			}
		}

		$query = $this->db->query($query, $bind_data);

		// if($data['shipped'] != null)
			// die($this->db->last_query());

		$totalData = 0;
		$result = array();
		if ($query->num_rows() > 0) {
			$totalData = $query->num_rows();
			$result = $query->result_array();
			$this->session->unset_userdata('get_products_total');
			$this->session->unset_userdata('get_products');
			$this->session->get_products_total = $totalData;
			$this->session->get_products = $result;
		}

		// if request not from search page, shuffle result set
		if ($data['page'] !== 'search' && c_default_order() == 1) {
			shuffle($result);
		}

		return array('total' => $totalData, 'results' =>$result); // return only 2 attribute for data_table query
	}

	function get_products_count(){
		$sql = "SELECT COUNT(a.Id) as count FROM sys_products a
			LEFT JOIN sys_shops b ON a.sys_shop = b.id
			LEFT JOIN sys_product_category c ON a.cat_id = c.id
				WHERE a.enabled = 1 and b.status = 1
				AND c.status = 1";
		return $this->db->query($sql)->row()->count;
	}

	public function get_primarypics3($product_id){
		$sql = "SELECT * FROM sys_products_images WHERE product_id = ? AND arrangement = 1 AND status = 1";

        $params = array(
            $product_id
				);

		$result = $this->db->query($sql, $params)->row()->filename;
		$result = (!empty($result)) ? $result : 'none';
		return $result;
	}

	public function get_product_images($productid){
		$productid = $this->db->escape($productid);
		$sql = "SELECT * FROM sys_products_images WHERE status = 1 AND product_id = $productid
			ORDER BY arrangement ASC";
		return $this->db->query($sql);
	}

	function getProductDetails($id) {
		$this->db->select("p.*, cat.category_name, cat.id as category_id, sh.shopcode, sh.shopname, sh.logo, sh.shopurl, (SELECT filename FROM sys_products_images WHERE arrangement = 1 AND product_id = p.Id AND status = 1) as primary_pics ");
		$this->db->select("(IF(p.parent_product_id IS NULL, p.itemname, (SELECT itemname FROM sys_products WHERE id = p.parent_product_id))) as itemname");
		$this->db->where("p.Id", $id);
		// 011122
		if (!empty($this->session->userdata("user_id")) && $this->session->userdata("user_type") == "JC") {
			$avail_arr = array(1, 2);
			$this->db->where_in("p.is_availability", $avail_arr); // for reseller login only | is_availability = 2
		}else{
			$this->db->where("p.is_availability", 1); // for public | is_availability = 1
		}
		// 011122
		$this->db->join("sys_product_category cat", "cat.id = p.cat_id", "left");
		$this->db->join("sys_shops sh", "sh.id = p.sys_shop", "left");
		return $this->db->get("sys_products p")->row_array();
	}

	//Product Inventory
	function isQuantityTrackingSet($productid) {

		$query=" SELECT `tq_isset` FROM `sys_products` WHERE id = ?";

		$result = $this->db->query($query, $productid);
		if($result->num_rows() > 0)
			return $result->row()->tq_isset;
		else
			return 0;
	}

	function insertInventoryTrans($data) {
		return $this->db->insert("sys_products_invtrans", $data);
	}

	function updateProductStocks($productid) {

		// $sql = "UPDATE `sys_products` SET `no_of_stocks` = (SELECT SUM(quantity) FROM `sys_products_invtrans` WHERE product_id = ?) WHERE Id = ?";
		$sql = "UPDATE sys_products
			SET no_of_stocks = (SELECT SUM(no_of_stocks) FROM sys_products_invtrans_branch WHERE product_id = ? AND status = 1)
			WHERE Id = ?";
		$bind_data = array(
			$productid,
			$productid
		);
		return $this->db->query($sql, $bind_data);
	}

	function getProductWeight($productid) {

		$query=" SELECT `weight` FROM `sys_products_shipping` WHERE product_id = ?";

		$result = $this->db->query($query, $productid);
		if($result->num_rows() > 0)
			return $result->row()->weight;
		else
			return 0;
	}

	function getProductMaxQty($productid) {


		$query=" SELECT `max_qty` FROM `sys_products` WHERE Id = ? AND `max_qty_isset` = 1 ";

		$result = $this->db->query($query, $productid);
		if($result->num_rows() > 0)
			return $result->row()->max_qty;
		else
			return 0;
	}

	public function update_invtrans_branch($orders,$branch){
		$sql = "UPDATE sys_products_invtrans_branch
			SET no_of_stocks = (no_of_stocks - ?)
			WHERE product_id = ? AND branchid = ?";
		$data = array($orders['quantity'],$orders['product_id'],$branch);
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function insert_invtrans_batch($data){
		$this->db->insert_batch('sys_products_invtrans',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function get_product_variants(){
		$data = array();
		$sql = "SELECT Id, itemname, itemid, parent_product_id, price
			FROM sys_products
			WHERE parent_product_id IS NOT NULL AND enabled = 1";
		$result = $this->db->query($sql);
		if($result->num_rows() > 0){
			$data = $result->result_array();
		}

		return $data;
	}

	public function get_all_productimg(){
		$data = array();
		$sql = "SELECT filename as primary_pics, product_id FROM sys_products_images WHERE arrangement = 1 AND status = 1";
		$result = $this->db->query($sql);
		if($result->num_rows() > 0){
			$data = $result->result_array();
		}

		return $data;
	}
}
?>
