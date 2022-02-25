<?php
if (function_exists("set_time_limit") == TRUE and @ini_get("safe_mode") == 0) { //to ignore maximum time limit
	@set_time_limit(0);
}

class Model_products_approval extends CI_Model
{

	# Start - Products

	public function get_sys_shop($user_id)
	{
		$sql = " SELECT sys_shop FROM app_members WHERE sys_user = ? AND status = 1";
		$sql = $this->db->query($sql, $user_id);

		if ($sql->num_rows() > 0) {
			return $sql->row()->sys_shop;
		} else {
			return "";
		}
	}

	public function get_sys_branch_profile($shop_id, $product_id, $branchid)
	{

		if ($branchid == 0) {
			$sql = " SELECT b.*, c.no_of_stocks as inv_qty FROM sys_branch_mainshop AS a
			LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
			LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
			WHERE a.mainshopid = ? AND a.status = 1
			GROUP BY b.id";
			$params = array($product_id, $shop_id);
		} else {
			$sql = " SELECT b.*, c.no_of_stocks as inv_qty FROM sys_branch_mainshop AS a
			LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
			LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
			WHERE a.mainshopid = ? AND a.branchid = ? AND a.status = 1
			GROUP BY b.id";
			$params = array($product_id, $shop_id, $branchid);
		}

		$sql = $this->db->query($sql, $params);

		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	public function get_uptodate_nostocks($shop_id, $product_id)
	{

		$sql = " SELECT SUM(c.no_of_stocks) as total_inv_qty FROM sys_branch_mainshop AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
		LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
		WHERE a.mainshopid = ? AND a.status = 1
		GROUP BY b.id";
		$params = array($product_id, $shop_id);

		$sql = $this->db->query($sql, $params);

		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	public function get_uptodate_nostocks_main($product_id)
	{

		$sql = " SELECT SUM(no_of_stocks) as total_inv_qty FROM sys_products_invtrans_branch
		WHERE branchid = 0 AND product_id = ? AND status = 1";
		$params = array($product_id);

		$sql = $this->db->query($sql, $params);

		if ($sql->num_rows() > 0) {
			return $sql->row();
		} else {
			return false;
		}
	}

	public function get_shopcode($user_id)
	{
		$sql = " SELECT b.shopcode as shopcode FROM app_members a
			    LEFT JOIN sys_shops b ON a.sys_shop = b.id
				WHERE a.sys_user = ? AND a.status = 1";
		$sql = $this->db->query($sql, $user_id);

		if ($sql->num_rows() > 0) {
			return $sql->row()->shopcode;
		} else {
			return "";
		}
	}

	public function get_shopcode_via_shopid($id)
	{
		$query = "SELECT * FROM sys_shops WHERE id = ? AND status = 1";
		$params = array($id);

		return $this->db->query($query, $params)->row()->shopcode;
	}

	public function get_shop_options()
	{
		$query = "SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

	public function get_category_options()
	{
		$query = "SELECT * FROM sys_product_category WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

	public function get_productdetails($Id)
	{
		$query = " SELECT a.*, c.weight, c.uom_id, c.shipping_isset, d.shopcode, d.shopname, e.no_of_stocks as inv_qty, f.startup as refstartup, f.jc as refjc, f.mcjr as refmcjr, f.mc as refmc, f.mcsuper as refmcsuper, f.mcmega as refmcmega, f.others as refothers,
		c.length, c.width, c.height
		FROM sys_products AS a
		LEFT JOIN sys_products_shipping AS c ON a.Id = c.product_id AND c.enabled = 1
		LEFT JOIN sys_shops AS d ON a.sys_shop = d.id
		LEFT JOIN sys_products_invtrans_branch AS e ON a.sys_shop = e.shopid AND e.status = 1 AND e.branchid = 0  AND e.product_id = ?
		LEFT JOIN 8_referralcom_rate AS f ON a.Id = f.product_id AND f.status = 1
		WHERE a.Id = ? AND a.enabled > 0;";

		$params = array($Id, $Id);
		return $this->db->query($query, $params)->row_array();
	}

	public function getVariants($product_id)
	{
		$query  = "SELECT * FROM sys_products WHERE enabled > 0 AND  parent_product_id = ?";
		$params = array($product_id);
		return $this->db->query($query, $params)->result_array();
	}

	public function getVariantsOption($product_id)
	{
		$query  = "SELECT * FROM sys_products_variantsummary WHERE parent_product_id = ? AND status = 1 AND (variant_type <> '' OR variant_type IS NOT NULL AND variant_list <> '' OR variant_list IS NOT NULL)";
		$params = array($product_id);
		return $this->db->query($query, $params);
	}

	public function checkProductStatus($Id)
	{
		$query = " SELECT enabled FROM sys_products WHERE Id = ?";

		$params = array($Id);
		return $this->db->query($query, $params)->row();
	}

	public function get_prev_product($itemname)
	{
		$query = "SELECT Id FROM sys_products WHERE itemname < ? AND enabled = 1 ORDER BY itemname DESC LIMIT 1";

		$params = array($itemname);

		if (!empty($this->db->query($query, $params)->row()->Id)) {
			return $this->db->query($query, $params)->row()->Id;
		} else {
			return 0;
		}
	}

	public function get_prev_product_per_shop($itemname, $sys_shop)
	{
		$query = "SELECT Id FROM sys_products WHERE itemname < ? AND enabled = 1 AND sys_shop = ? ORDER BY itemname DESC LIMIT 1";

		$params = array($itemname, $sys_shop);

		if (!empty($this->db->query($query, $params)->row()->Id)) {
			return $this->db->query($query, $params)->row()->Id;
		} else {
			return 0;
		}
	}

	public function get_next_product($itemname)
	{
		$query = "SELECT Id FROM sys_products WHERE itemname > ? AND enabled = 1 ORDER BY itemname LIMIT 1";

		$params = array($itemname);

		if (!empty($this->db->query($query, $params)->row()->Id)) {
			return $this->db->query($query, $params)->row()->Id;
		} else {
			return 0;
		}
	}

	public function get_next_product_per_shop($itemname, $sys_shop)
	{
		$query = "SELECT Id FROM sys_products WHERE itemname > ? AND enabled = 1 AND sys_shop = ? ORDER BY itemname LIMIT 1";

		$params = array($itemname, $sys_shop);

		if (!empty($this->db->query($query, $params)->row()->Id)) {
			return $this->db->query($query, $params)->row()->Id;
		} else {
			return 0;
		}
	}


	public function check_products_itemid($itemid, $sys_shop)
	{

		if (ini() == 'jconlineshop') {
			$sql = " SELECT * FROM sys_products WHERE sys_shop = ? AND itemid = ? AND enabled > 0";
			$params = array($sys_shop, $itemid);
		} else {
			$sql = " SELECT * FROM sys_products WHERE itemid = ? AND enabled > 0";
			$params = array($itemid);
		}

		return $this->db->query($sql, $params);
	}

	public function check_referralcom_rate($shopcode, $itemid)
	{
		$sql = " SELECT * FROM 8_referralcom_rate WHERE itemid = ? AND status = 1";
		$params = array($shopcode . '_' . $itemid);

		return $this->db->query($sql, $params);
	}

	public function check_products_id($id)
	{
		$sql = " SELECT * FROM sys_products WHERE Id = ? AND enabled > 0";
		$params = array($id);

		return $this->db->query($sql, $params);
	}

	public function check_products($id)
	{
		$sql = " SELECT a.*, c.weight, c.uom_id, c.shipping_isset, d.shopcode, d.shopname, e.no_of_stocks as inv_qty, f.startup as refstartup, f.jc as refjc, f.mcjr as refmcjr, f.mc as refmc, f.mcsuper as refmcsuper, f.mcmega as refmcmega, f.others as refothers
		FROM sys_products AS a
		LEFT JOIN sys_products_shipping AS c ON a.Id = c.product_id AND c.enabled = 1
		LEFT JOIN sys_shops AS d ON a.sys_shop = d.id
		LEFT JOIN sys_products_invtrans_branch AS e ON a.sys_shop = e.shopid AND e.status = 1 AND e.branchid = 0  AND e.product_id = ?
		LEFT JOIN 8_referralcom_rate AS f ON a.Id = f.product_id AND f.status = 1
		WHERE a.Id = ?";
		$params = array($id, $id);

		return $this->db->query($sql, $params);
	}



	public function getParentProductInvBranch($Id, $branchid)
	{

		$sql = "SELECT * FROM sys_products WHERE parent_product_id = ? AND enabled > 0";
		$bind_data = array(
			$Id
		);

		$result = $this->db->query($sql, $bind_data)->result_array();
		$total_qty = 0;

		foreach ($result as $row) {
			$sql = "SELECT * FROM sys_products_invtrans_branch WHERE branchid = ? AND product_id = ? AND status > 0";
			$bind_data = array(
				$branchid,
				$row['Id']
			);

			$variantQtyBranch = $this->db->query($sql, $bind_data)->row_array();
			$variantQtyBranch['no_of_stocks'] = (!empty($variantQtyBranch['no_of_stocks'])) ? $variantQtyBranch['no_of_stocks'] : 0;
			$total_qty += $variantQtyBranch['no_of_stocks'];
		}

		return $total_qty;
	}

	public function update_branch_invtrans($shopid, $branchid, $no_of_stocks, $product_id, $type)
	{

		$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND branchid = ? AND product_id = ? AND status = 1";
		$bind_data = array(
			$shopid,
			$branchid,
			$product_id
		);

		$branch_invtrans = $this->db->query($sql, $bind_data);

		if ($branch_invtrans->num_rows() > 0) {
			$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND branchid = ? AND product_id = ? AND status = 1";
			$bind_data = array(
				$no_of_stocks,
				$shopid,
				$branchid,
				$product_id
			);

			$this->db->query($sql, $bind_data);
		} else {
			$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
			$bind_data = array(
				$shopid,
				$branchid,
				$product_id,
				$no_of_stocks,
				date('Y-m-d H:i:s'),
				1
			);

			$this->db->query($sql, $bind_data);
		}

		$sql = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = ? AND enabled = 1";
		$bind_data = array(
			$product_id,
			$branchid
		);

		$invtrans = $this->db->query($sql, $bind_data);

		if ($invtrans->row()->qty_count_stocks > 0) {
			$total_qty = 0;
			$total_qty = $no_of_stocks - $invtrans->row()->qty_count_stocks;

			if ($total_qty != 0) {
				$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
				$bind_data = array(
					$branchid,
					$product_id,
					$total_qty,
					$type,
					$this->session->userdata('username'),
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
			} else {
				$this->db->query($sql, $bind_data);
			}
		} else {

			if ($no_of_stocks != 0) {
				$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
				$bind_data = array(
					$branchid,
					$product_id,
					$no_of_stocks,
					$type,
					$this->session->userdata('username'),
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
			} else {
				$this->db->query($sql, $bind_data);
			}
		}
	}

	public function update_product_refcommrate($args, $id, $shopcode)
	{
		//// update referral commission rate
		$sql = "UPDATE 8_referralcom_rate SET itemid = ?, itemname = ?, others = ? WHERE product_id = ? AND status = 1";
		$bind_data = array(
			$shopcode . '_' . $args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$id
		);

		return $this->db->query($sql, $bind_data);
	}




	public function product_waiting_for_approval_table($sys_shop, $requestData, $exportable = false)
	{
		// storing  request (ie, get/post) global array to a variable
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_shops 		= $this->input->post('_shops');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');

		

		$sql = "SELECT
		        a.*,
				code.shopcode,
				a.parent_product_id AS parentIDproduct,
				b.shopname,
				g.itemname AS parent_product_name,
				d.filename AS primary_pic,
				e.disc_rate,
				e.startup,
				e.jc,
				e.mcjr,
				e.mc,
				e.mcsuper,
				e.mcmega,
				e.others,
				e.price as approval_price,
				h.merchant_comrate AS shop_disc_rate,
				h.startup AS shop_startup,
				h.jc AS shop_jc,
				h.mcjr AS shop_mcjr,
				h.mc AS shop_mc,
				h.mcsuper AS shop_mcsuper,
				h.mcmega AS shop_mcmega,
				h.others AS shop_others
		        FROM sys_products a
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				-- LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id
				LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1
				LEFT JOIN sys_product_status as e  ON a.Id = e.product_id AND e.status = 3
				LEFT JOIN sys_products AS g ON a.parent_product_id = g.Id
				LEFT JOIN 8_referralcom_rate_shops AS h  ON  h.shopid = a.sys_shop 
				";


		// start - for default search
		if ($_record_status == 1) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == 2) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else {
			$sql .= " WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters

		if ($_name != "") {
			$sql .= " AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if ($_shops != "") {
			$sql .= " AND b.id = " . $this->db->escape($_shops) . "";
		}

		if ($sys_shop != 0) {
			$sql .= " AND b.id = " . $this->db->escape($sys_shop) . "";
		}

		$sql .= "AND e.status = '3' ORDER BY a.itemname, b.shopname ASC ";

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . " ";  // adding length
		// if (!$exportable) {
		// 	$sql .= " LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		// }

		$query = $this->db->query($sql);

		return $query->result_array();
	}

	public function product_waiting_for_approval_application($prod_id)
	{

		$sql = "UPDATE sys_product_status SET updated = ?, status = 2 WHERE product_id = ?";

		$params = array(
			date('Y-m-d H:i:s'),
			$prod_id
		);

		$this->db->query($sql, $params);
	}


	public function product_waiting_for_approval_application_decline($array_prod)
	{


		$sql = "UPDATE sys_product_status SET updated = ?, reason = ?, status = ? WHERE product_id = ?";

		$params = array(
			date('Y-m-d H:i:s'),
			$array_prod['reason'],
			0,
			$array_prod['id']

		);

		$sql1 = "UPDATE sys_products SET enabled = 2 WHERE Id = ?";

		$params1 = array(
			$array_prod['id']
		);
		
		$this->db->query($sql1, $params1);
		

		$this->db->query($sql, $params);
	}


	/// approved all

	public function product_waiting_for_approval_application_all($array_prod){

		$sql = "UPDATE sys_product_status SET updated = ?, status = 2 WHERE product_id IN (?)";

		$params = array(
			date('Y-m-d H:i:s'),
			$array_prod
		);

		$this->db->query($sql, $params);

	}


	public function product_waiting_for_approval_decline_all($array_prod,$reason){

		$sql = "UPDATE sys_product_status SET updated = ?, reason = ?, status = 0 WHERE product_id IN (?)";

		$params = array(
			date('Y-m-d H:i:s'),
			$reason,
			$array_prod
		);

		$sql1 = "UPDATE sys_products SET enabled = 2 WHERE Id = ?";

		$params1 = array(
			$array_prod
		);
		
		$this->db->query($sql1, $params1);

		

		$this->db->query($sql, $params);

	}




	//products declined


	public function product_declined_table($sys_shop, $requestData, $exportable = false)
	{
		// storing  request (ie, get/post) global array to a variable
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_shops 		= $this->input->post('_shops');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');


		$sql = "SELECT
		        a.*,
				code.shopcode,
				a.parent_product_id AS parentIDproduct,
				b.shopname,
				g.itemname AS parent_product_name,
				d.filename AS primary_pic,
				e.reason,
				e.disc_rate,
				e.startup,
				e.jc,
				e.mcjr,
				e.mc,
				e.mcsuper,
				e.mcmega,
				e.others,
				e.price as approval_price,
				h.merchant_comrate AS shop_disc_rate,
				h.startup AS shop_startup,
				h.jc AS shop_jc,
				h.mcjr AS shop_mcjr,
				h.mc AS shop_mc,
				h.mcsuper AS shop_mcsuper,
				h.mcmega AS shop_mcmega,
				h.others AS shop_others
		        FROM sys_products a
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id
				LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1
				LEFT JOIN sys_product_status as e  ON a.Id = e.product_id AND e.status = 0
				LEFT JOIN sys_products AS g ON a.parent_product_id = g.Id
				LEFT JOIN 8_referralcom_rate_shops AS h ON  h.shopid = a.sys_shop";

		// start - for default search
		if ($_record_status == 1) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == 2) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else {
			$sql .= " WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters

		if ($_name != "") {
			$sql .= " AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if ($_shops != "") {
			$sql .= " AND b.id = " . $this->db->escape($_shops) . "";
		}

		if ($sys_shop != 0) {
			$sql .= " AND b.id = " . $this->db->escape($sys_shop) . "";
		}

		$sql .= "AND e.status = '0' ORDER BY a.itemname ASC";

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$query = $this->db->query($sql);
		return $query->result_array();
	}


	public function product_declined_to_approved_application($prod_id)
	{

		$sql = "UPDATE sys_product_status SET updated = ?, status = 3 WHERE product_id = ?";

		$params = array(
			date('Y-m-d H:i:s'),
			$prod_id
		);

		$this->db->query($sql, $params);
	}


	public function product_decline_application_all($array_prod){

		$sql = "UPDATE sys_product_status SET updated = ?, status = 3 WHERE product_id IN (?)";

		$params = array(
			date('Y-m-d H:i:s'),
			$array_prod
		);

		$sql1 = "UPDATE sys_products SET enabled = 2 WHERE Id = ?";

		$params1 = array(
			$array_prod
		);
		
		$this->db->query($sql1, $params1);
		

		$this->db->query($sql, $params);

	}



	//products  Approved

	public function product_approved_table($sys_shop, $requestData, $exportable = false)
	{
		// storing  request (ie, get/post) global array to a variable
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_shops 		= $this->input->post('_shops');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');

		$columns = array(
			// datatable column index  => database column name for sorting
			0 => 'Id',
			1 => 'itemname',
			2 => 'shopname',
			3 => 'merchantcomrate',
			4 => 'startup',
			5 => 'jc',
			6 => 'mcjr',
			7 => 'mc',
			8 => 'mcsuper',
			9 => 'mcmega',
			10 => 'others',

		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_products a
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
                LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				WHERE a.enabled > 0 AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData;
		//

		$sql = "SELECT 
                   a.*,
					code.shopcode,
					a.parent_product_id AS parentIDproduct,
					b.shopname,
					g.itemname AS parent_product_name,
					d.filename AS primary_pic,
					e.disc_rate,
					e.startup,
					e.jc,
					e.mcjr,
					e.mc,
					e.mcsuper,
					e.mcmega,
					e.others,
					e.price as approval_price,
					h.merchant_comrate AS shop_disc_rate,
					h.startup AS shop_startup,
					h.jc AS shop_jc,
					h.mcjr AS shop_mcjr,
					h.mc AS shop_mc,
					h.mcsuper AS shop_mcsuper,
					h.mcmega AS shop_mcmega,
					h.others AS shop_others
		        FROM sys_products a
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				-- LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id
				LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1
				LEFT JOIN sys_product_status as e  ON a.Id = e.product_id AND e.status = 2
				LEFT JOIN sys_products AS g ON a.parent_product_id = g.Id
				LEFT JOIN 8_referralcom_rate_shops AS h ON  h.shopid = a.sys_shop 
				";


		// start - for default search
		if ($_record_status == 1) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == 2) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else {
			$sql .= " WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters

		if ($_name != "") {
			$sql .= " AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if ($_shops != "") {
			$sql .= " AND b.id = " . $this->db->escape($_shops) . "";
		}

		if ($sys_shop != 0) {
			$sql .= " AND b.id = " . $this->db->escape($sys_shop) . "";
		}

		$sql .= "AND e.status = '2'  ORDER BY a.itemname ASC";

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// print_r($sql);
		// exit();
		$query = $this->db->query($sql);

		return $query->result_array();

	
	}


	public function product_approved_to_verified_application($prod_id)
	{

		$sql_prodStatus = "SELECT * FROM sys_product_status WHERE product_id =  '$prod_id' ";
		$prod_status  = $this->db->query($sql_prodStatus)->result_array();

	
		$sql = "SELECT * FROM 8_referralcom_rate WHERE product_id = '$prod_id' AND STATUS = 1 ";
		$prod_refcomm = $this->db->query($sql);


		if($prod_refcomm->num_rows() == 0){
			$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
			$bind_data = array(
				$prod_status[0]['itemid'],
				$prod_id,
				$prod_status[0]['instance_id'],
				$prod_status[0]['startup'],
				$prod_status[0]['jc'],
				$prod_status[0]['mcjr'],
				$prod_status[0]['mc'],
				$prod_status[0]['mcsuper'],
				$prod_status[0]['mcmega'],
				$prod_status[0]['others'],
				1
			);	
			$this->db->query($sql, $bind_data);
		}else{
			$sql = "UPDATE 8_referralcom_rate SET itemid = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE product_id = ? AND status = ?";
			$bind_data = array(
				$prod_status[0]['itemid'],
				$prod_status[0]['startup'],
				$prod_status[0]['jc'],
				$prod_status[0]['mcjr'],
				$prod_status[0]['mc'],
				$prod_status[0]['mcsuper'],
				$prod_status[0]['mcmega'],
				$prod_status[0]['others'],
				$prod_id,
				1
			);
			$this->db->query($sql, $bind_data);
		}
	

		$sql1 = "UPDATE sys_products SET disc_rate = ?, enabled = ? WHERE Id = ?";
		$params1 = array(
			$prod_status[0]['disc_rate'],
			1,
			$prod_id
		);
		$this->db->query($sql1, $params1);

		$sql = "UPDATE sys_product_status SET updated = ?, status = ? WHERE product_id = ?";
		$params = array(
			date('Y-m-d H:i:s'),
			1,
			$prod_id
		);
		$this->db->query($sql, $params);


		$sql = "UPDATE sys_products SET price = ? WHERE Id = ?";
		$params = array(
			$prod_status[0]['price'],
			$prod_id
		);
		$this->db->query($sql, $params);

	}


	public function product_approved_application_decline($array_prod)
	{


		$sql = "UPDATE sys_product_status SET updated = ?, reason = ?, status = ? WHERE product_id = ?";

		$params = array(
			date('Y-m-d H:i:s'),
			$array_prod['reason'],
			0,
			$array_prod['id']

		);

		$sql1 = "UPDATE sys_products SET enabled = 2 WHERE Id = ?";

		$params1 = array(
			$array_prod['id']
		);
		
		$this->db->query($sql1, $params1);

		$this->db->query($sql, $params);
	}

	public function product_approved_to_verify_application_all($prod_id)
	{

		$sql_prodStatus = "SELECT * FROM sys_product_status WHERE product_id =  '$prod_id' ";
		$prod_status  = $this->db->query($sql_prodStatus)->result_array();

		$sql = "SELECT * FROM 8_referralcom_rate WHERE product_id = '$prod_id' AND STATUS = 1 ";
		$prod_refcomm = $this->db->query($sql);


		if($prod_refcomm->num_rows() == 0){
			$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
			$bind_data = array(
				$prod_status[0]['itemid'],
				$prod_id,
				$prod_status[0]['instance_id'],
				$prod_status[0]['startup'],
				$prod_status[0]['jc'],
				$prod_status[0]['mcjr'],
				$prod_status[0]['mc'],
				$prod_status[0]['mcsuper'],
				$prod_status[0]['mcmega'],
				$prod_status[0]['others'],
				1
			);	
			$this->db->query($sql, $bind_data);
		}else{
			$sql = "UPDATE 8_referralcom_rate SET itemid = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE product_id = ? AND status = ?";
			$bind_data = array(
				$prod_status[0]['itemid'],
				$prod_status[0]['startup'],
				$prod_status[0]['jc'],
				$prod_status[0]['mcjr'],
				$prod_status[0]['mc'],
				$prod_status[0]['mcsuper'],
				$prod_status[0]['mcmega'],
				$prod_status[0]['others'],
				$prod_id,
				1
			);
			$this->db->query($sql, $bind_data);
		}

		$sql = "UPDATE sys_product_status SET updated = ?, status = ? WHERE product_id = ?";
		$params = array(
			date('Y-m-d H:i:s'),
			1,
			$prod_id
		);
		$this->db->query($sql, $params);

		$sql1 = "UPDATE sys_products SET disc_rate = ?, enabled = ? WHERE Id = ?";
		$params1 = array(
			$prod_status[0]['disc_rate'],
			1,
			$prod_id
		);
		$this->db->query($sql1, $params1);


		$sql = "UPDATE sys_products SET price = ? WHERE Id = ?";
		$params = array(
			$prod_status[0]['price'],
			$prod_id
		);
		$this->db->query($sql, $params);

	
	}


	//products  Verified

	public function product_verified_table($sys_shop, $requestData, $exportable = false)
	{
		// storing  request (ie, get/post) global array to a variable
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_shops 		= $this->input->post('_shops');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');

		$columns = array(
			// datatable column index  => database column name for sorting
			0 => 'date_updated',
			1 => 'itemname',
			2 => 'shopname',
			3 => 'merchantcomrate',
			4 => 'startup',
			5 => 'jc',
			6 => 'mcjr',
			7 => 'mc',
			8 => 'mcsuper',
			9 => 'mcmega',
			10 => 'others'

		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_products a
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
                LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				WHERE a.enabled > 0 AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData;
		//

		$sql = "SELECT 
				a.*,
				a.parent_product_id AS parentIDproduct,
				b.shopname,
				g.itemname AS parent_product_name,
				e.disc_rate,
				e.startup,
				e.jc,
				e.mcjr,
				e.mc,
				e.mcsuper,
				e.mcmega,
				e.others,
				e.price as approval_price,
				h.merchant_comrate AS shop_disc_rate,
				h.startup AS shop_startup,
				h.jc AS shop_jc,
				h.mcjr AS shop_mcjr,
				h.mc AS shop_mc,
				h.mcsuper AS shop_mcsuper,
				h.mcmega AS shop_mcmega,
				h.others AS shop_others
		        FROM sys_products a
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_status as e  ON a.Id = e.product_id AND e.status = 1
				LEFT JOIN sys_products AS g ON a.parent_product_id = g.Id
				LEFT JOIN 8_referralcom_rate_shops AS h ON  h.shopid = a.sys_shop 
              
				";

		// start - for default search
		if ($_record_status == 1) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == 2) {
			$sql .= " WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		} else {
			$sql .= " WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters

		if ($_name != "") {
			$sql .= " AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if ($_shops != "") {
			$sql .= " AND b.id = " . $this->db->escape($_shops) . "";
		}

		if ($sys_shop != 0) {
			$sql .= " AND b.id = " . $this->db->escape($sys_shop) . "";
		}

		$sql .= "AND e.status = '1'  GROUP BY a.Id ";

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] . " ";  // adding length
		if (!$exportable) {
			$sql .= " LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		}

		// print_r($sql);
		// exit();
		$query = $this->db->query($sql);

		$data = array();
		foreach ($query->result_array() as $row) {  // preparing an array for table tbody

			if($row['parentIDproduct'] != null && $row['parentIDproduct']  != 0){

				$nestedData = array();
				$nestedData[] = $row["date_updated"];
				$nestedData[] = $row['parent_product_name'].' - '. $row["itemname"];
				$nestedData[] = $row["shopname"];
		     	if($row["disc_rate"] == 0 &&  $row["startup"] == 0 &&  $row["jc"] == 0 &&  $row["mcjr"] == 0 &&  $row["mc"] == 0 &&  $row["mcsuper"] == 0 &&  $row["mcmega"] == 0 &&   $row["others"] == 0){

					$nestedData[] = number_format($row["shop_disc_rate"] * 100, 2);
					$nestedData[] = number_format($row["shop_startup"] * 100, 2);
					$nestedData[] = number_format($row["shop_jc"] * 100, 2);
					$nestedData[] = number_format($row["shop_mcjr"] * 100, 2);
					$nestedData[] = number_format($row["shop_mc"] * 100, 2);
					$nestedData[] = number_format($row["shop_mcsuper"] * 100, 2);
					$nestedData[] = number_format($row["shop_mcmega"] * 100, 2);
					$nestedData[] = number_format($row["shop_others"] * 100, 2);


				}else{
					$nestedData[] = number_format($row["disc_rate"] * 100, 2);
					$nestedData[] = number_format($row["startup"] * 100, 2);
					$nestedData[] = number_format($row["jc"] * 100, 2);
					$nestedData[] = number_format($row["mcjr"] * 100, 2);
					$nestedData[] = number_format($row["mc"] * 100, 2);
					$nestedData[] = number_format($row["mcsuper"] * 100, 2);
					$nestedData[] = number_format($row["mcmega"] * 100, 2);
					$nestedData[] = number_format($row["others"] * 100, 2);
					
				}
		
				$nestedData[] = $row["approval_price"];
				$buttons = "";
				$buttons .= '<a class="btn btn-primary" data-prod-id="' . $row['Id'] . '" href="' . base_url('Products_approval/view_products_verified/' . $token . '/' . $row['Id']) . '"> View</a> &nbsp;';
				$nestedData[] =
					'
					' . $buttons . '
				';
				$data[] = $nestedData;

			
		}else{


			$nestedData = array();
			$nestedData[] = $row["date_updated"];
			$nestedData[] = $row["itemname"];
			$nestedData[] = $row["shopname"];

			if($row["disc_rate"] == 0 && $row["startup"] == 0 && $row["jc"] == 0 && $row["mcjr"] == 0 && $row["mc"] == 0 && $row["mcsuper"] == 0 && $row["mcmega"] == 0 &&  $row["others"] == 0){

				$nestedData[] = number_format($row["shop_disc_rate"] * 100, 2);
				$nestedData[] = number_format($row["shop_startup"] * 100, 2);
				$nestedData[] = number_format($row["shop_jc"] * 100, 2);
				$nestedData[] = number_format($row["shop_mcjr"] * 100, 2);
				$nestedData[] = number_format($row["shop_mc"] * 100, 2);
				$nestedData[] = number_format($row["shop_mcsuper"] * 100, 2);
				$nestedData[] = number_format($row["shop_mcmega"] * 100, 2);
				$nestedData[] = number_format($row["shop_others"] * 100, 2);

			}else{
				$nestedData[] = number_format($row["disc_rate"] * 100, 2);
				$nestedData[] = number_format($row["startup"] * 100, 2);
				$nestedData[] = number_format($row["jc"] * 100, 2);
				$nestedData[] = number_format($row["mcjr"] * 100, 2);
				$nestedData[] = number_format($row["mc"] * 100, 2);
				$nestedData[] = number_format($row["mcsuper"] * 100, 2);
				$nestedData[] = number_format($row["mcmega"] * 100, 2);
				$nestedData[] = number_format($row["others"] * 100, 2);
			
			}
			$nestedData[] = $row["approval_price"];
			$buttons = "";
			$buttons .= '<a class="btn btn-primary" data-prod-id="' . $row['Id'] . '" href="' . base_url('Products_approval/view_products_verified/' . $token . '/' . $row['Id']) . '"> View</a> &nbsp;';
			$nestedData[] =
				'
				' . $buttons . '
			';
			$data[] = $nestedData;


		}

	}

		$json_data = array(
			"draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval($totalData),  // total number of records
			"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}


	public function getProduct($product_id) {

		$sql = "SELECT * FROM sys_products WHERE ID = ? AND enabled != 0";
		$bind_data = array(
			$product_id,
		);
		return $this->db->query($sql, $bind_data)->result_array();
    }






	public function get_invqty_branch($branchid, $product_id)
	{
		$sql = "SELECT * FROM sys_products_invtrans_branch WHERE branchid = ? AND product_id = ? AND status = 1";
		$params = array($branchid, $product_id);

		return $this->db->query($sql, $params)->row_array();
	}




	private function get_InvStartQty($fromdate, $branch_ids)
	{
		$sql = "SELECT CONCAT(branchid,'.', product_id) as id, SUM(quantity) as start_qty
				FROM
					`sys_products_invtrans`
				USE INDEX
					(date_created)
				WHERE
					date_created < $fromdate AND CONCAT(branchid,'.', product_id) IN ('$branch_ids')
				GROUP BY branchid, product_id";

		return $this->db->query($sql)->result_array();
	}

	private function get_InvAddedQty($fromdate, $todate, $branch_ids)
	{
		$sql = "SELECT CONCAT(branchid,'.', product_id) as id, SUM(quantity) AS added_qty
				FROM
					`sys_products_invtrans`
				USE INDEX
					(date_created)
				WHERE
					date_created BETWEEN $fromdate AND $todate AND CONCAT(branchid,'.', product_id) IN ('$branch_ids')
				GROUP BY branchid, product_id";

		return $this->db->query($sql)->result_array();
	}

	private function get_InvSoldQty($fromdate, $todate, $branch_ids)
	{
		$sql = "SELECT CONCAT(branchid,'.', product_id) as id, ABS(SUM(quantity)) as sold_qty
				FROM
					`sys_products_invtrans`
				USE INDEX
					(date_created)
				WHERE
					date_created BETWEEN $fromdate AND $todate AND `type` IN(
						'Online Purchase',
						'Online Payment',
						'Manual Order',
						'Refund Order'
					) AND CONCAT(branchid,'.', product_id) IN ('$branch_ids')
				group by branchid, product_id";

		return $this->db->query($sql)->result_array();
	}

	public function getImagesfilename($Id)
	{
		$query = "SELECT * FROM sys_products_images
		WHERE product_id = ? AND status = 1
		ORDER BY arrangement ASC";

		$params = array($Id);
		return $this->db->query($query, $params);
	}

	public function checkOrderActive($product_id)
	{
		$query = "SELECT b.id, b.date_ordered FROM app_sales_order_logs AS a
		LEFT JOIN app_sales_order_details AS b ON a.order_id = b.id
		WHERE a.product_id = ? AND b.order_status <> 's' AND date(b.date_ordered) >= ? AND b.status = 1 LIMIT 1";

		$params = array(
			$product_id,
			date('Y-m-d', strtotime("-30 days"))
		);
		return $this->db->query($query, $params);
	}

	public function getWishlist($product_id)
	{
		$query = "SELECT a.product_id, b.first_name, b.email, c.itemname, d.filename AS primary_pic, e.shopcode FROM app_customers_wishlist AS a
		LEFT JOIN app_customers AS b ON a.user_id = b.user_id
		LEFT JOIN sys_products AS c ON a.product_id = c.Id
		LEFT JOIN sys_products_images AS d ON c.Id = d.product_id AND d.arrangement = 1 AND d.status = 1
		LEFT JOIN sys_shops AS e ON c.sys_shop = e.id
		WHERE a.product_id = ? AND a.status > 0";

		$params = array(
			$product_id
		);
		return $this->db->query($query, $params);
	}

	public function getProductTotalStocks($product_id)
	{
		$query = "SELECT no_of_stocks FROM sys_products
		WHERE Id = ?";

		$params = array(
			$product_id
		);
		return $this->db->query($query, $params);
	}

	public function getTotalEndingQty($date_to_2)
	{

		$sql = "SELECT branchid, product_id, SUM(quantity) as ending_quantity FROM sys_products_invtrans
        WHERE date_created <= " . $date_to_2 . " AND enabled = 1
        GROUP BY branchid, product_id";


		$result = $this->db->query($sql);

		return $result;
	}


	public function getFeaturedProduct()
	{
		$query = "SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ORDER BY set_product_arrangement ASC";
		return $this->db->query($query)->result_array();
	}

	public function getFeaturedProductCount()
	{
		$query = "SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ";
		return $this->db->query($query)->num_rows();
	}

	public function checkFeaturedProductArrangement($product_number)
	{
		$query = "SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' AND set_product_arrangement = '$product_number'  AND set_product_arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}

	public function  checkedFeaturedProduct($product_id)
	{
		$query = "SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' AND  `Id` = '$product_id' ";
		$result = $this->db->query($query)->num_rows();

		if ($result > 0) {
			return 1;
		} else {
			return 0;
		}
	}




	# End - Products
}
