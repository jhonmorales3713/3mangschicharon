<?php 
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) //to ignore maximum time limit
{
    @set_time_limit(0);
}
ini_set('memory_limit', '1024M');

class Model_settings_shipping_delivery extends CI_Model {
	public $app_db;

	public function get_shopdetails($shop_id){
		$query="SELECT * FROM sys_shops WHERE status = 1 AND md5(id) = ?";
		$params = array($shop_id);

		return $this->db->query($query, $params);
	}

	public function get_region(){
		$query="SELECT * FROM sys_region WHERE status = 1";

		return $this->db->query($query);
	}

	public function get_province_all(){
		$query="SELECT a.*, b.regDesc FROM sys_prov AS a
		LEFT JOIN sys_region AS b ON a.regCode = b.regCode AND b.status = 1
		WHERE a.status = 1";

		return $this->db->query($query);
	}

	public function get_citymun_all(){
		$query="SELECT a.*, b.provDesc, c.regCode, c.regDesc FROM sys_citymun AS a 
		LEFT JOIN sys_prov AS b ON a.provCode = b.provCode AND b.status = 1
		LEFT JOIN sys_region AS c ON a.regDesc = c.regCode AND c.status = 1
		WHERE a.status = 1";

		return $this->db->query($query);
	}

	public function get_province_old($regCode){
		$query = "SELECT * FROM sys_prov WHERE regCode = ? AND status = 1";
		$params = array($regCode);

		return $this->db->query($query, $params);
	}

	public function get_province($regCode){
		$query = "SELECT a.*, b.provDesc FROM sys_citymun AS a
		LEFT JOIN sys_prov AS b ON a.provCode = b.provCode AND b.status = 1
		WHERE a.regDesc = ? AND a.status = 1";
		$params = array($regCode);

		return $this->db->query($query, $params);
	}

	public function get_citymun($provCode){
		$query = "SELECT * FROM sys_citymun WHERE provCode = ? AND status = 1";
		$params = array($provCode);

		return $this->db->query($query, $params);
	}

	public function get_branches($shop_id){
		$query = "SELECT b.branchname, b.id as branch_id FROM sys_branch_mainshop as a
		LEFT JOIN sys_branch_profile as b ON a.branchid = b.id
		WHERE b.status = 1 AND md5(a.mainshopid) = ?";
		$params = array($shop_id);

		return $this->db->query($query, $params);
	}

	// get parent products concatenated with variants
	public function get_products($shop_id_md5){
		$query = "SELECT
		a.`Id`,
		CONCAT(
		  (SELECT
			`itemname`
		  FROM
			`sys_products`
		  WHERE `id` IN (a.parent_product_id)
		  GROUP BY parent_product_id),
		  ' - ',
		  a.`itemname`
		) AS itemname
	  FROM
		sys_products AS a
		LEFT JOIN sys_shipping_zone_products AS b
		  ON a.Id = b.product_id
		  AND b.enabled = 1
	  WHERE b.product_id IS NULL
		AND a.enabled = 1
		AND MD5(a.sys_shop) = ". $this->db->escape($shop_id_md5) .
		"AND a.`parent_product_id` IS NOT NULL
	  union
	  all
	  SELECT DISTINCT
		t.`Id`,
		t.`itemname`
	  FROM
		sys_products AS t
		LEFT JOIN sys_shipping_zone_products AS u
		  ON t.Id = u.product_id
		  AND u.enabled = 1
	  WHERE u.product_id IS NULL
		AND t.enabled = 1
		AND MD5(t.sys_shop) = ". $this->db->escape($shop_id_md5) .
		"AND parent_product_id IS NULL
		and t.id not in
		(SELECT DISTINCT
		  a.`parent_product_id`
		FROM
		  sys_products AS a
		  LEFT JOIN sys_shipping_zone_products AS b
			ON a.Id = b.product_id
			AND b.enabled = 1
		WHERE b.product_id IS NULL
		  AND a.enabled = 1
		  AND MD5(a.sys_shop) = ". $this->db->escape($shop_id_md5) .
		  "AND a.`parent_product_id` IS not NULL)
	  GROUP BY `itemname`
	  ORDER BY itemname";

		return $this->db->query($query);
	}

	// get parent products that has no variants.
	public function get_products2($shop_id_md5)
	{
		$sql = "SELECT DISTINCT
		a.*
	  FROM
		sys_products AS a
		LEFT JOIN sys_shipping_zone_products AS b
		  ON a.Id = b.product_id
		  AND b.enabled = 1
	  WHERE b.product_id IS NULL
		AND a.enabled = 1
		AND MD5(a.sys_shop) = ?";

		return $this->db->query($sql, $shop_id_md5);
	}

	public function get_general_rates($shop_id_md5){
		$query = "SELECT * FROM sys_shipping WHERE is_custom = 0 AND enabled = 1 AND md5(sys_shop_id) = ?";
		$params = array($shop_id_md5);

		return $this->db->query($query, $params);
	}

	public function get_custom_rates($shipping_id){
		$query = "SELECT * FROM sys_shipping WHERE is_custom = 1 AND enabled = 1 AND md5(id) = ?";
		$params = array($shipping_id);

		return $this->db->query($query, $params);
	}

	public function get_custom_rates_products($shipping_id){
		$query = "SELECT DISTINCT b.product_id FROM sys_shipping_zone as a
		LEFT JOIN sys_shipping_zone_products as b ON a.id = b.shipping_zone_id
		WHERE a.enabled = 1 AND md5(a.sys_shipping_id) = ?";
		$params = array($shipping_id);

		return $this->db->query($query, $params);
	}

	public function get_product($product_id){
		$query = "SELECT Id,
		(
		  CASE
			WHEN parent_product_id IS NOT NULL
			THEN CONCAT(
			  (SELECT
				itemname
			  FROM
				`sys_products`
			  WHERE id = a.`parent_product_id`),
			  ' - ',
			  itemname
			)
			WHEN a.parent_product_id IS NULL
			THEN a.itemname
		  END
		) as itemname FROM sys_products AS a WHERE enabled = 1
		AND Id = ?";
		$params = array($product_id);

		return $this->db->query($query, $params);
	}

	public function get_general_zone($shipping_id){
		$query = "SELECT a.*, b.regDesc, c.provDesc, CONCAT(c.provDesc, ' - ', b.regDesc) as provDescCons, d.citymunDesc, CONCAT(d.citymunDesc, ' - ', c.provDesc) as citymunDescCons FROM sys_shipping_zone AS a
		LEFT JOIN sys_region AS b ON a.regCode = b.regCode AND b.status = 1
		LEFT JOIN sys_prov AS c ON a.provCode = c.provCode AND c.status = 1
		LEFT JOIN sys_citymun AS d ON a.citymunCode = d.citymunCode AND d.status = 1
		WHERE a.enabled = 1 AND a.sys_shipping_id = ?
		ORDER BY a.zone_name ASC";
		$params = array($shipping_id);

		return $this->db->query($query, $params);
	}

	public function get_custom_zone($shipping_id){
		$query = "SELECT a.*, b.regDesc, c.provDesc, CONCAT(c.provDesc, ' - ', b.regDesc) as provDescCons, d.citymunDesc, CONCAT(d.citymunDesc, ' - ', c.provDesc) as citymunDescCons FROM sys_shipping_zone AS a
		LEFT JOIN sys_region AS b ON a.regCode = b.regCode AND b.status = 1
		LEFT JOIN sys_prov AS c ON a.provCode = c.provCode AND c.status = 1
		LEFT JOIN sys_citymun AS d ON a.citymunCode = d.citymunCode AND d.status = 1
		WHERE a.enabled = 1 AND a.sys_shipping_id = ?
		ORDER BY a.zone_name ASC";
		$params = array($shipping_id);

		return $this->db->query($query, $params);
	}

	public function get_general_zone_rates($shipping_zone_id){
		$query = "SELECT * FROM sys_shipping_zone_rates
		WHERE enabled = 1 AND sys_shipping_zone_id = ?";
		$params = array($shipping_zone_id);

		return $this->db->query($query, $params);
	}

	public function get_general_zone_branches($shipping_zone_id){
		$query = "SELECT a.*, b.branchname FROM sys_shipping_zone_branch as a
		LEFT JOIN sys_branch_profile as b ON a.branch_id = b.id
		WHERE a.enabled = 1 AND a.shipping_zone_id = ?";
		$params = array($shipping_zone_id);

		return $this->db->query($query, $params);
	}

	public function get_custom_zone_rates($shipping_zone_id){
		$query = "SELECT * FROM sys_shipping_zone_rates
		WHERE enabled = 1 AND sys_shipping_zone_id = ?";
		$params = array($shipping_zone_id);

		return $this->db->query($query, $params);
	}

	public function save_general_rates($shop_id) {
		
		$sql = "INSERT into sys_shipping (`sys_shop_id`, `is_custom`, `profile_name`, `date_created`, `enabled`) VALUES (?,?,?,?,?) ";
		$params = array(
			$shop_id,
			0,
			'General Shipping',
			date('Y-m-d H:i:s'),
			1
		);
		$this->db->query($sql, $params);

		return $this->db->insert_id();
	}

	public function save_custom_rates($shop_id, $profile_name) {
		
		$sql = "INSERT into sys_shipping (`sys_shop_id`, `is_custom`, `profile_name`, `date_created`, `enabled`) VALUES (?,?,?,?,?) ";
		$params = array(
			$shop_id,
			1,
			$profile_name,
			date('Y-m-d H:i:s'),
			1
		);
		$this->db->query($sql, $params);

		return $this->db->insert_id();
	}

	public function save_general_rates_zone($shipping_id, $zone_name, $regCode, $provCode, $citymunCode, $array_f_key) {
		
		$sql = "INSERT into sys_shipping_zone (`sys_shipping_id`, `zone_name`, `regCode`, `provCode`, `citymunCode`, `array_f_key`, `date_created`, `enabled`) 
				VALUES (?,?,?,?,?,?,?,?) ";
		$params = array(
			$shipping_id,
			$zone_name,
			$regCode,
			$provCode,
			$citymunCode,
			$array_f_key,
			date('Y-m-d H:i:s'),
			1
		);
		$this->db->query($sql, $params);

		return $this->db->insert_id();
	}

	public function save_custom_rates_zone($shipping_id, $productArray, $zone_name, $regCode, $provCode, $citymunCode, $array_f_key) {
		
		$sql = "INSERT into sys_shipping_zone (`sys_shipping_id`, `zone_name`, `regCode`, `provCode`, `citymunCode`, `array_f_key`, `date_created`, `enabled`) 
				VALUES (?,?,?,?,?,?,?,?) ";
		$params = array(
			$shipping_id,
			$zone_name,
			$regCode,
			$provCode,
			$citymunCode,
			$array_f_key,
			date('Y-m-d H:i:s'),
			1
		);
		$this->db->query($sql, $params);
		$shipping_zone_id = $this->db->insert_id();

		foreach ($productArray as $row) {
			if($row['status'] == 1){

				$sql = "INSERT into sys_shipping_zone_products (`shipping_zone_id`, `product_id`, `enabled`) 
				VALUES (?,?,?) ";
				$params = array(
					$shipping_zone_id,
					$row['product_id'],
					1
				);
				$this->db->query($sql, $params);

			}
			
		}
		return $shipping_zone_id;
	}

	public function save_general_rates_zone_rates($shipping_zone_id, $rate_name, $rate_amount, $is_condition, $minimum_value, $maximum_value, $from_day, $to_day, $additional_isset, $set_value, $set_amount){
		
		$sql = "INSERT into sys_shipping_zone_rates (`sys_shipping_zone_id`, `rate_name`, `rate_amount`, `is_condition`, `condition_min_value`, `condition_max_value`, `from_day`, `to_day`, `additional_isset`, `set_value`, `set_amount`, `date_created`, `enabled`) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		$params = array(
			$shipping_zone_id,
			$rate_name,
			$rate_amount,
			$is_condition,
			$minimum_value,
			$maximum_value,
			$from_day,
			$to_day,
			$additional_isset,
			$set_value,
			$set_amount,
			date('Y-m-d H:i:s'),
			1
		);

		return $this->db->query($sql, $params);
	}

	public function save_general_zone_branch($shop_id, $branch_id, $shipping_zone_id){
		
		$sql = "INSERT into sys_shipping_zone_branch (`shop_id`, `branch_id`, `shipping_zone_id`, `date_created`, `enabled`) 
				VALUES (?,?,?,?,?) ";
		$params = array(
			$shop_id,
			$branch_id,
			$shipping_zone_id,
			date('Y-m-d H:i:s'),
			1
		);

		return $this->db->query($sql, $params);
	}

	public function save_custom_zone_branch($shop_id, $branch_id, $shipping_zone_id){
		
		$sql = "INSERT into sys_shipping_zone_branch (`shop_id`, `branch_id`, `shipping_zone_id`, `date_created`, `enabled`) 
				VALUES (?,?,?,?,?) ";
		$params = array(
			$shop_id,
			$branch_id,
			$shipping_zone_id,
			date('Y-m-d H:i:s'),
			1
		);

		return $this->db->query($sql, $params);
	}

	public function save_custom_rates_zone_rates($shipping_zone_id, $rate_name, $rate_amount, $is_condition, $minimum_value, $maximum_value, $from_day, $to_day, $additional_isset, $set_value, $set_amount){
		
		$sql = "INSERT into sys_shipping_zone_rates (`sys_shipping_zone_id`, `rate_name`, `rate_amount`, `is_condition`, `condition_min_value`, `condition_max_value`, `from_day`, `to_day`, `additional_isset`, `set_value`, `set_amount`, `date_created`, `enabled`) 
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		$params = array(
			$shipping_zone_id,
			$rate_name,
			$rate_amount,
			$is_condition,
			$minimum_value,
			$maximum_value,
			$from_day,
			$to_day,
			$additional_isset,
			$set_value,
			$set_amount,
			date('Y-m-d H:i:s'),
			1
		);

		return $this->db->query($sql, $params);
	}

	public function disable_existing_rates($shipping_id, $shop_id){
		///disable shop's rate
		$query = "SELECT *  FROM sys_shipping WHERE sys_shop_id = ? AND is_custom = 0 AND enabled = 1 ";
		$params = array($shop_id);
		$shipping_shop = $this->db->query($query, $params)->result_array();

		$query = "UPDATE sys_shipping SET enabled = 0 WHERE sys_shop_id = ? AND is_custom = 0 AND enabled = 1 ";
		$params = array($shop_id);
		$this->db->query($query, $params);

		if(!empty($shipping_shop)){
			foreach($shipping_shop as $row){
				$query = "SELECT *  FROM sys_shipping_zone WHERE sys_shipping_id = ? AND enabled = 1";
				$params = array($row['id']);
				$shipping_zone_shop = $this->db->query($query, $params)->result_array();
	
				$query = "UPDATE sys_shipping SET enabled = 0 WHERE id = ?";
				$params = array($row['id']);
				$this->db->query($query, $params);
	
				$query = "UPDATE sys_shipping_zone SET enabled = 0 WHERE sys_shipping_id = ?";
				$params = array($row['id']);
				$this->db->query($query, $params);
	
				foreach($shipping_zone_shop as $val){
					$query = "UPDATE sys_shipping_zone_rates SET enabled = 0 WHERE sys_shipping_zone_id = ?";
					$params = array($val['id']);
					$this->db->query($query, $params);
	
					$query = "UPDATE sys_shipping_zone_branch SET enabled = 0 WHERE shipping_zone_id = ?";
					$params = array($val['id']);
					$this->db->query($query, $params);
				}
	
			}
		}

		///disable current rates
		$query = "SELECT *  FROM sys_shipping_zone WHERE sys_shipping_id = ?";
		$params = array($shipping_id);
		$shipping = $this->db->query($query, $params)->result_array();

		$query = "UPDATE sys_shipping SET enabled = 0 WHERE id = ?";
		$params = array($shipping_id);
		$shipping_table = $this->db->query($query, $params);

		$query = "UPDATE sys_shipping_zone SET enabled = 0 WHERE sys_shipping_id = ?";
		$params = array($shipping_id);
		$shipping_zone_table = $this->db->query($query, $params);

		if(!empty($shipping)){
			foreach($shipping as $row){
				$query = "UPDATE sys_shipping_zone_rates SET enabled = 0 WHERE sys_shipping_zone_id = ?";
				$params = array($row['id']);
				$this->db->query($query, $params);
	
				$query = "UPDATE sys_shipping_zone_branch SET enabled = 0 WHERE shipping_zone_id = ?";
				$params = array($row['id']);
				$this->db->query($query, $params);
			}
		}

		return $shipping_table;
	}

	public function disable_existing_rates_md5($shipping_id){
		$query = "SELECT *  FROM sys_shipping_zone WHERE md5(sys_shipping_id) = ?";
		$params = array($shipping_id);
		$shipping = $this->db->query($query, $params)->result_array();

		$query = "UPDATE sys_shipping SET enabled = 0 WHERE md5(id) = ?";
		$params = array($shipping_id);
		$shipping_table = $this->db->query($query, $params);

		$query = "UPDATE sys_shipping_zone SET enabled = 0 WHERE md5(sys_shipping_id) = ?";
		$params = array($shipping_id);
		$shipping_zone_table = $this->db->query($query, $params);

		foreach($shipping as $row){
			$query = "UPDATE sys_shipping_zone_rates SET enabled = 0 WHERE sys_shipping_zone_id = ?";
			$params = array($row['id']);
			$this->db->query($query, $params);

			$query = "UPDATE sys_shipping_zone_products SET enabled = 0 WHERE shipping_zone_id = ?";
			$params = array($row['id']);
			$this->db->query($query, $params);

			$query = "UPDATE sys_shipping_zone_branch SET enabled = 0 WHERE shipping_zone_id = ?";
			$params = array($row['id']);
			$this->db->query($query, $params);
		}

		return $shipping_table;
	}

	public function delete_custom_shipping($delete_id){


		$query = "SELECT * FROM sys_shipping_zone WHERE sys_shipping_id = ?";
		$params = array($delete_id);
		$shipping = $this->db->query($query, $params)->result_array();

		$query = "UPDATE sys_shipping_zone SET enabled = 0 WHERE sys_shipping_id = ?";
		$params = array($delete_id);
		$shipping_zone_table = $this->db->query($query, $params);

		foreach($shipping as $row){
			$query = "UPDATE sys_shipping_zone_rates SET enabled = 0 WHERE sys_shipping_zone_id = ?";
			$params = array($row['id']);
			$this->db->query($query, $params);

			$query = "UPDATE sys_shipping_zone_products SET enabled = 0 WHERE shipping_zone_id = ?";
			$params = array($row['id']);
			$this->db->query($query, $params);

			$query = "UPDATE sys_shipping_zone_branch SET enabled = 0 WHERE shipping_zone_id = ?";
			$params = array($row['id']);
			$this->db->query($query, $params);
		}

		$sql = "UPDATE `sys_shipping` SET `enabled` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function product_list($shop_id) {

		$requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;
		
		$columns = array( 
            0 => 'itemname',
            1 => 'itemid'
		);
		
		$query="SELECT a.*, b.shopcode, c.filename as primary_pic FROM sys_products a 
				LEFT JOIN sys_shops b ON a.sys_shop = b.id
				LEFT JOIN sys_products_images c ON a.Id = c.product_id AND c.arrangement = 1 AND c.status = 1
				WHERE a.enabled = 1 AND md5(a.sys_shop) = ? ";
		$params = array($shop_id);
		
        $totalData = $this->db->query($query, $params)->num_rows();
		$totalFiltered = $totalData;
        $query.=" ORDER BY ".$columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." 
                  LIMIT ".intval($requestData['start'])." , ".intval($requestData['length']);
		$result = $this->db->query($query, $params)->result_array();

		return array('total' => $totalData, 'filtered' =>$result); // return only 2 attribute for data_table query
	}

	public function general_table(){
		// storing  request (ie, get/post) global array to a variable  
		$_name 			= $this->input->post('_name');
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'shopcode',
			1 => 'shopname',
			2 => 'created',
			3 => 'status'
		);

		// getting total number records without any search

		$sql = "SELECT * FROM sys_shops WHERE status = 1";
		
		// getting records as per search parameters
		
		if($_name != ""){
			$sql.=" AND shopname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$totalData = $query->num_rows();

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["shopcode"];
			$nestedData[] = $row["shopname"];
			$nestedData[] = $row["email"];

			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    	<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('Settings_shipping_delivery/general_rates/'.$token.'/'.md5($row['id']).'').'">View</a>
			  	</div>
			</div>';
			
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

	public function custom_table(){
		// storing  request (ie, get/post) global array to a variable  
		$_name 			= $this->input->post('_name');
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'shopcode',
			1 => 'shopname',
			2 => 'created',
			3 => 'status'
		);

		// getting total number records without any search

		$sql = "SELECT * FROM sys_shops WHERE status = 1";
		
		// getting records as per search parameters
		
		if($_name != ""){
			$sql.=" AND shopname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$totalData = $query->num_rows();

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["shopcode"];
			$nestedData[] = $row["shopname"];
			$nestedData[] = $row["email"];

			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    	<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('Settings_shipping_delivery/custom_profile_list/'.$token.'/'.md5($row['id']).'').'"> View</a>
			  	</div>
			</div>';
			
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

	public function profile_table($shop_id_md5){
		// storing  request (ie, get/post) global array to a variable  
		$_name 			= $this->input->post('_name');
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'date_created',
			1 => 'profile_name'
		);

		// getting total number records without any search

		$sql = "SELECT * FROM sys_shipping WHERE enabled = 1 AND is_custom = 1 AND md5(sys_shop_id) = ?";
		
		// getting records as per search parameters
		
		if($_name != ""){
			$sql.=" AND profile_name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		$params = array($shop_id_md5);
		$query = $this->db->query($sql, $params);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$totalData = $query->num_rows();

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql, $params);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["date_created"];
			$nestedData[] = $row["profile_name"];

			if($this->loginstate->get_access()['custom_shipping']['update'] == 1 && $this->loginstate->get_access()['custom_shipping']['delete'] == 1){
				$nestedData[] = 
				'<div class="dropdown">
					<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
					<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
						<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('Settings_shipping_delivery/custom_rates/'.$token.'/'.md5($row['sys_shop_id']).'/'.md5($row['id'])).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
					</div>
				</div>';
			}
			else if($this->loginstate->get_access()['custom_shipping']['update'] == 1){
				$nestedData[] = 
				'<div class="dropdown">
					<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
					<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
						<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('Settings_shipping_delivery/custom_rates/'.$token.'/'.md5($row['sys_shop_id']).'/'.md5($row['id'])).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
					</div>
				</div>';
			}
			else if($this->loginstate->get_access()['custom_shipping']['delete'] == 1){
				$nestedData[] = 
				'<div class="dropdown">
					<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
					<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
					</div>
				</div>';
			}else{
				$nestedData[] = "";
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
		
}
