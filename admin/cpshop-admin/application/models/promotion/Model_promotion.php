<?php 
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) { //to ignore maximum time limit
    @set_time_limit(0);
}

class Model_promotion extends CI_Model {

	 public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('reports', TRUE);
    }

	# Start - Promotion

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
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

	public function get_product_categories() {
		$query="SELECT * FROM sys_product_category WHERE status = 1 ORDER BY priority ASC";
		return $this->db->query($query)->result_array();
    }


    public function get_uptodate_nostocks($shop_id, $product_id){
		
		$sql=" SELECT SUM(c.no_of_stocks) as total_inv_qty FROM sys_branch_mainshop AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
		LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
		WHERE a.mainshopid = ? AND a.status = 1
		GROUP BY b.id";
		$params = array($product_id, $shop_id);

		$sql = $this->db->query($sql, $params); 

        if($sql->num_rows() > 0){
            return $sql->result_array();
        }else{
            return false;
        }
	}

    public function get_uptodate_nostocks_main($product_id){
		
		$sql=" SELECT SUM(no_of_stocks) as total_inv_qty FROM sys_products_invtrans_branch
		WHERE branchid = 0 AND product_id = ? AND status = 1";
		$params = array($product_id);

		$sql = $this->db->query($sql, $params); 

        if($sql->num_rows() > 0){
            return $sql->row();
        }else{
            return false;
        }
	}

    public function get_invqty_branch($branchid, $product_id){
		$sql="SELECT * FROM sys_products_invtrans_branch WHERE branchid = ? AND product_id = ? AND status = 1";
		$params = array($branchid, $product_id);
		
        return $this->db->query($sql, $params)->row_array();
	}

    public function getParentProductInvBranch($Id, $branchid){

		$sql = "SELECT * FROM sys_products WHERE parent_product_id = ? AND enabled > 0";
		$bind_data = array(
			$Id
		);

		$result = $this->db->query($sql, $bind_data)->result_array();
		$total_qty = 0;
	
		foreach($result as $row){
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

    public function product_table($sys_shop, $requestData){
		// storing  request (ie, get/post) global array to a variable  
		$token_session   = $this->session->userdata('token_session');
		$token           = en_dec('en', $token_session);
		$branchid        = $this->session->userdata('branchid');
        // $_name          = sanitize($this->input->post('_name'));
        $_name 			 = sanitize($this->input->post('_name'));
        $select_category = sanitize($this->input->post('select_category'));
		$productArray    = json_decode($this->input->post('productArray'));
        $productArray    = json_decode(json_encode($productArray), true);

		// print_r($productArray);
		// die();

	
		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'Id',
            1 => 'itemname',
            2 => 'category_name',
            3 => 'price',
            4 => 'no_of_stocks',
            5 => 'shopname',
            6 => 'enabled',
		);

		$sql = "SELECT a.*, code.shopcode, c.category_name, b.shopname, d.itemname as parent_product_name FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id
				LEFT JOIN sys_products AS d ON a.parent_product_id = d.Id
				-- LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1
                WHERE a.enabled = 1";

		// getting records as per search parameters

		if(!empty($productArray)){
			$productString = "(";
			foreach($productArray as $value){
				if($value != ''){
					$productString .= "'".$value['product_id']."', ";
				}
			}
			$productString = rtrim($productString, ', ');
			$productString .= ")";
		}
		else{
			$productString = "";
		}

        if($_name != ""){
			$sql.=" AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		
		if($select_category != ""){
			$sql.=" AND a.cat_id = " . $this->db->escape($select_category) . "";
		}

		if($sys_shop != ""){
			$sql.=" AND a.sys_shop = " . $this->db->escape($sys_shop) . "";
		}
		if($productString != ""){
			$sql .=" AND a.Id NOT IN $productString";

		}

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";  // adding length
        // $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query = $this->db->query($sql);
        

		$data  = array();
		$count = 0;
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			if($row['variant_isset'] == 0 && $row['parent_product_id'] == ''){
				$nestedData     = array(); 
				$otherinfo      = ($row['otherinfo'] != '' || $row['otherinfo'] != null) ? ' ('.$row['otherinfo'].')' : '';
				$parent_product = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ': '';
				$no_of_stocks   = ($row['no_of_stocks'] != '') ? number_format($row['no_of_stocks'], 1) : 'None';
				$nestedData[]   = '<input type="checkbox" class="form-control checkbox_perprod" name="checkbox_perprod[]" id="checkbox_perprod" value="'.$row['Id'].'" data-product_name="'.str_replace('"', '&quot;', $parent_product.$row["itemname"].$otherinfo).'" data-product_price="'.$row["price"].'" data-product_stock="'.$no_of_stocks.'" data-sys_shop="'.$row["sys_shop"].'">';
				$nestedData[]   = $parent_product.$row["itemname"];
				$nestedData[]   = "₱".number_format($row["price"], 2);
				$nestedData[]   = $no_of_stocks;
				$data[]         = $nestedData;
			}
			else if($row['parent_product_id'] != ''){
				$nestedData     = array(); 
				$parent_product = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ': '';
				$no_of_stocks   = ($row['no_of_stocks'] != '') ? number_format($row['no_of_stocks'], 1) : 'None';
				$nestedData[]   = '<input type="checkbox" class="form-control checkbox_perprod" name="checkbox_perprod[]" id="checkbox_perprod" value="'.$row['Id'].'" data-product_name="'.$parent_product.$row["itemname"].'" data-product_price="'.$row["price"].'" data-product_stock="'.$no_of_stocks.'" data-sys_shop="'.$row["sys_shop"].'">';
				$nestedData[]   = $parent_product.$row["itemname"];
				$nestedData[]   = "₱".number_format($row["price"], 2);
				$nestedData[]   = $no_of_stocks;
				$data[]         = $nestedData;
			}
			else{
				$count++;
			}

		}
		
		$json_data = array(
			// "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData - $count ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered - $count ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function checkActiveProdPromo($id){
		$sql=" SELECT * FROM sys_products_promotion
		WHERE product_id = ? AND status > 0";
		$params = array(
			$id
		);
        return $this->db->query($sql, $params)->num_rows();;
	}

	public function checkProdPromoDetails($id){
		$sql=" SELECT * FROM sys_products_promotion
		WHERE product_id = ? AND status > 0";
		$params = array(
			$id
		);
        return $this->db->query($sql, $params)->row_array();;
	}

	public function fetch_productPromo($sys_shop){
		$sql=" SELECT a.promo_type, a.promo_rate, a.promo_price, a.start_date, a.end_date, a.promo_stock, a.purchase_limit, a.status,a.product_id, b.sys_shop, b.price, b.itemname, c.itemname as parent_product_name, b.otherinfo as item_otherinfo, b.no_of_stocks, DATE(a.start_date) as startdate, DATE(a.end_date) as enddate, TIME(a.start_date) as starttime, TIME(a.end_date) as endtime FROM sys_products_promotion AS a
		LEFT JOIN sys_products AS b ON a.product_id = b.Id
		LEFT JOIN sys_products AS c ON b.parent_product_id = c.Id
		WHERE a.status > 0";

		if($sys_shop != ""){
			$sql.=" AND b.sys_shop = " . $this->db->escape($sys_shop) . "";
		}

		$sql .= " ORDER BY a.date_created ASC";
	
        return $this->db2->query($sql);;
	}

	public function updateActiveProduct($dataArr){
		$checkActiveProdPromo = $this->checkActiveProdPromo($dataArr['product_id']);
		$dataArr['product_promo_stock'] = ($dataArr['product_promo_stock'] == '') ? null : $dataArr['product_promo_stock'];
		$dataArr['product_purch_limit'] = ($dataArr['product_purch_limit'] == '') ? null : $dataArr['product_purch_limit'];

		if($checkActiveProdPromo > 0){
			$sql = "UPDATE sys_products_promotion SET promo_type = ?, promo_rate = ?, promo_price = ?, start_date = ?, end_date = ?, promo_stock = ?, purchase_limit = ?, date_updated = ?, status = ? WHERE product_id = ? AND status > 0";
			$bind_data = array(
				$dataArr['product_promo_type'],
				$dataArr['product_promo_rate'],
				$dataArr['product_promo_price'],
				$dataArr['start_date'],
				$dataArr['end_date'],
				$dataArr['product_promo_stock'],
				$dataArr['product_purch_limit'],
				date('Y-m-d H:i:s'),
				$dataArr['product_status'],
				$dataArr['product_id']
			);

			$this->db->query($sql, $bind_data);
		}
		else{
			$sql = "INSERT INTO sys_products_promotion (`product_id`, `promo_type`, `promo_rate`, `promo_price`, `start_date`, `end_date`, `promo_stock`, `purchase_limit`, `date_created`, `date_updated`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
			$bind_data = array(
				$dataArr['product_id'],
				$dataArr['product_promo_type'],
				$dataArr['product_promo_rate'],
				$dataArr['product_promo_price'],
				$dataArr['start_date'],
				$dataArr['end_date'],
				$dataArr['product_promo_stock'],
				$dataArr['product_purch_limit'],
				date('Y-m-d H:i:s'),
				'0000-00-00 00:00:00',
				$dataArr['product_status'],
			);

			$this->db->query($sql, $bind_data);
		}
		$sql = "UPDATE sys_products SET promo_isset = 1 WHERE Id = ?";
		$bind_data = array(
			$dataArr['product_id']
		);

		$this->db->query($sql, $bind_data);
	}

	public function updateInactiveProduct($dataArr){
		$checkActiveProdPromo = $this->checkActiveProdPromo($dataArr['product_id']);
		$dataArr['product_promo_stock'] = ($dataArr['product_promo_stock'] == '') ? null : $dataArr['product_promo_stock'];

		if($checkActiveProdPromo > 0){
			$sql = "UPDATE sys_products_promotion SET promo_type = ?, promo_rate = ?, promo_price = ?, start_date = ?, end_date = ?, promo_stock = ?, purchase_limit = ?, date_updated = ?, status = ? WHERE product_id = ? AND status > 0";
			$bind_data = array(
				$dataArr['product_promo_type'],
				$dataArr['product_promo_rate'],
				$dataArr['product_promo_price'],
				$dataArr['start_date'],
				$dataArr['end_date'],
				$dataArr['product_promo_stock'],
				$dataArr['product_purch_limit'],
				date('Y-m-d H:i:s'),
				$dataArr['product_status'],
				$dataArr['product_id']
			);

			$this->db->query($sql, $bind_data);
		}
		else{
			$sql = "INSERT INTO sys_products_promotion (`product_id`, `promo_type`, `promo_rate`, `promo_price`, `start_date`, `end_date`, `promo_stock`, `purchase_limit`, `date_created`, `date_updated`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
			$bind_data = array(
				$dataArr['product_id'],
				$dataArr['product_promo_type'],
				$dataArr['product_promo_rate'],
				$dataArr['product_promo_price'],
				$dataArr['start_date'],
				$dataArr['end_date'],
				$dataArr['product_promo_stock'],
				$dataArr['product_purch_limit'],
				date('Y-m-d H:i:s'),
				'0000-00-00 00:00:00',
				$dataArr['product_status'],
			);

			$this->db->query($sql, $bind_data);
		}
		$sql = "UPDATE sys_products SET promo_isset = 0 WHERE Id = ?";
		$bind_data = array(
			$dataArr['product_id']
		);

		$this->db->query($sql, $bind_data);
	}

	public function updateDeletedProduct($value){
		$sql = "UPDATE sys_products_promotion SET status = 0 WHERE product_id = ?";
		$bind_data = array(
			$value
		);

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET promo_isset = 0 WHERE Id = ?";
		$bind_data = array(
			$value
		);

		$this->db->query($sql, $bind_data);

		$sql = "SELECT * FROM sys_products WHERE Id = ?";
		$bind_data = array(
			$value
		);

		$prod = $this->db->query($sql, $bind_data);
		if($prod->num_rows() > 0){
			return $prod->row_array()['itemname'];
		}
		else{
			return "";
		}
	}


	public function getFeaturedProductPiso(){
		$query="SELECT b.itemname, a.arrangement, a.is_featured FROM sys_products_promotion AS a LEFT JOIN sys_products as b ON a.product_id = b.Id WHERE a.status = '1' AND a.is_featured = '1' ORDER BY a.arrangement ASC";
		return $this->db->query($query)->result_array();
	}


	public function getFeaturedProductCountPiso(){
		$query="SELECT * FROM sys_products_promotion WHERE status = '1' AND is_featured = '1' ";
		return $this->db->query($query)->num_rows();
	}


	public function checkFeaturedProductArrangementPiso($product_number){
		$query="SELECT * FROM sys_products_promotion WHERE status = '1' AND is_featured = '1' AND arrangement = '$product_number'  AND arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}


	public function save_featured_piso($feauted_piso_data){

		$sql = "UPDATE `sys_products_promotion` SET `is_featured` = ?,  arrangement = ? WHERE `product_id` = ?";
		$data = array(
			        1,
					$feauted_piso_data['product_arrangement'],
					$feauted_piso_data['product_id'],
					);		
		$this->db->query($sql, $data);


	}


	public function removed_featured_piso($feauted_piso_data){

		$sql = "UPDATE `sys_products_promotion` SET `is_featured` = ?,  arrangement = ? WHERE `product_id` = ?";
		$data = array(
			        0,
			        0,
					$feauted_piso_data['product_id'],
					);		
		$this->db->query($sql, $data);


	}


	public function get_vouchers_discount_table($filters,$requestData, $exportable = false){

    
  
		$db2  =  $this->load->database('default',TRUE);
		$requestData = $_REQUEST;


		if(!$exportable){
				// storing  request (ie, get/post) global array to a variable			
		  $_voucher_type	    = $this->input->post('_voucher_type');
		  $_voucher_code	    = $this->input->post('_voucher_code');
		  $_vname	            = $this->input->post('_vname');
		  $date_from	        = $this->input->post('date_from');
		  $date_to	            = $this->input->post('date_to');
		  $_record_status	    = $this->input->post('_record_status');
		 $requestData = $_REQUEST;
	  
			}
			else{
		 // 
		  //on form export from controller			
		  $filter = json_decode($this->input->post('_filter'));
		  $_voucher_type	    = $this->input->post('_voucher_type');
		  $_voucher_code	    = $this->input->post('_voucher_code');
		  $_vname	            = $this->input->post('_vname');
		  $date_from	        = $this->input->post('date_from');
		  $date_to	            = $this->input->post('date_to');
		  $_record_status	    = $this->input->post('_record_status');
		$requestData 	= url_decode(json_decode($this->input->post("_search")));
	  //  echo"test";
			}
	
		$columns = array(
		  0 => 'start_date',
		  1 => 'end_date',
		  2 => 'shopname',
		  3 => 'voucher_type',
		  4 => 'voucher_name',
		  5 => 'voucher_code',
		  6 => 'disc_ammount',
		  7 => 'minimum_basket_price',
		  8 => 'usage_quantity',

		);
	
	
	
		// $shopid = $this->session->userdata('sys_shop_id');
	
	
		$sql = " SELECT *, a.status as voucher_status, a.id as voucher_id FROM sys_promotion_vouchers AS a 
				LEFT JOIN sys_shops AS b
				ON a.`shop_id` = b.`id`
				";
	
		
		  if ($_record_status == 1) {
		     $sql.=" WHERE a.status = " . $this->db->escape($_record_status) . "";
		  }else if ($_record_status == 2){
			 $sql.=" WHERE a.status = " . $this->db->escape($_record_status) . "";
		  }else{
			$sql.=" WHERE a.status > 0 ";
		  }
           


		  if($date_from != '' && $date_to != ''){
			$from = new Datetime($date_from);
			$from = $this->db->escape($from->format('Y-m-d'));
			$to = new Datetime($date_to);
			$to = $this->db->escape($to->format('Y-m-d'));
			$sql .= " AND DATE(a.start_date) BETWEEN $from AND $to";
	
		  }
		  

	

	
		if($_voucher_type != ""){
			$sql.=" AND a.voucher_type = " . $this->db->escape($_voucher_type) . "";
		}
		if($_voucher_code != ""){
			$sql.=" AND a.voucher_code LIKE '%" . $this->db->escape_like_str($_voucher_code) . "%' ";
		}
		if($_vname != ""){
			$sql.=" AND a.voucher_name LIKE '%" . $this->db->escape_like_str($_vname) . "%' ";
		}

	
		$query =  $db2->query($sql);          
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 
		$sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
		
		//export 
		if(!$exportable){
		 $sql .=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}
		
	
		$query =  $db2->query($sql);
		$data = array();
		
	   
		foreach( $query->result_array() as $row )
		{
		  

		    if ($row['voucher_type'] == 1) {
				$voucher_type = 'Shop Voucher';
			}else if ($row['voucher_type'] == 2) {
				$voucher_type = 'Product Voucher';
			}

				
			if ($row['voucher_status'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['voucher_status'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			if ($row['voucher_status'] == 1) {
				$status = 'Enabled';
			}else if ($row['voucher_status'] == 2) {
				$status = 'Disabled';
			}

			$ci = get_instance();
		    $ci->load->helper('url');
	

			$actions = '
				<div class="dropdown">
					<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['voucher_discount']['update'] == 1) {
            $actions .= ' 
				<a class="dropdown-item" href="'.$ci->config->config['base_url'].'promotion/Main_promotion/edit_vouchers/'.$row['voucher_id'].'" data-value="'.$row['voucher_id'].'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
				<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['voucher_discount']['disable'] == 1) {
				$actions .= '
				<a class="dropdown-item action_disable" data-value="'.$row['voucher_id'].'" data-record_status="'.$row['voucher_status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
				<div class="dropdown-divider"></div>';
			}
				
			if ($this->loginstate->get_access()['voucher_discount']['delete'] == 1) {
				$actions .= '
				<a class="dropdown-item action_delete " data-value="'.$row['voucher_id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}

			$actions .= '
				</div>
			</div>';

			$set_label = '';
	
            if($row['on_top'] == 0){
			       $set_label .= '<label class="switch" data-toggle="modal" data-target="#setFeadutedModal" data-id="'.$row['voucher_id'].'"> <input type="checkbox"> <span class="slider round"></span></label>';
			}else{
		        	$set_label .= '<label class="switch" data-toggle="modal" data-target="#unsetFeadutedModal" data-id="'.$row['voucher_id'].'"> <input checked  type="checkbox"> <span class="slider round"></span></label>';
			}


			
			$nestedData=array();
			$nestedData[] = $row['start_date'];
			$nestedData[] = $row['end_date'];
			$nestedData[] = $voucher_type;
			$nestedData[] = $row['voucher_name'];
			$nestedData[] = $row['voucher_code'];
			$nestedData[] = $row['disc_amount'];
			$nestedData[] = $row['minimum_basket_price'];
			$nestedData[] = $row['usage_quantity'];
			$nestedData[] = $status;
			$nestedData[] = $actions;
			$nestedData[] = $set_label;
			$data[] = $nestedData;

		  
		}
	
		$json_data = array(
	
		  "recordsTotal"    => intval( $totalData ),
		  "recordsFiltered" => intval( $totalFiltered ),
		  "data"            => $data
		);
	
		return $json_data;
	  }


	  public function shop_table($requestData) {

    
		$db2  =  $this->load->database('default',TRUE);
		$requestData = $_REQUEST;

		$columns = array(
		  0 => 'shopname',
		);
	
		$sql = " SELECT * FROM sys_shops  WHERE status = 1";
	


		$query =  $db2->query($sql);          
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 
	
		
		$query =  $db2->query($sql);
		$data = array();
		
	   
		foreach( $query->result_array() as $row )
		{
		  

			
			$nestedData=array();
			$nestedData[]   = '<input type="checkbox" class="form-control checkbox_perprod" name="checkbox_perprod[]" id="checkbox_perprod" value="'.$row['id'].'" data-shopname="'.$row["shopname"].'">';
			$nestedData[] = $row['shopname'];
			$data[]         = $nestedData;
		  
		}
	
		$json_data = array(
	
		  "recordsTotal"    => intval( $totalData ),
		  "recordsFiltered" => intval( $totalFiltered ),
		  "data"            => $data
		);
	
		return $json_data;
	  }


	  
	public function check_voucher_code($voucher_code){
		$query="SELECT * FROM sys_promotion_vouchers WHERE voucher_code = '$voucher_code'";
		return $this->db->query($query)->num_rows();
	}



	
	public function vouchers_add($data_admin){


		$db2  =  $this->load->database('default',TRUE);
		$sql = "INSERT INTO `sys_promotion_vouchers` (`voucher_type`, `shop_id`, `product_id`, `voucher_name`, `voucher_code`, `start_date`, `end_date`, `disc_amount_type`,`disc_amount`, `max_discount_isset`, `max_discount_price`, `minimum_basket_price`, `usage_quantity`, `date_created`, `date_updated`, `status`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$data = array(
		     $data_admin['voucher_type'],
			 $data_admin ['shop_id'],
			 $data_admin['product_id'],
			 $data_admin['voucher_name'], 
			 $data_admin['voucher_code'], 
			 $data_admin['date_from'], 
			 $data_admin['date_to'], 
			 $data_admin['disc_ammount_type'], 
			 $data_admin['disc_ammount'], 
			 $data_admin['set_amount_limit'], 
			 $data_admin['disc_ammount_limit'], 
			 $data_admin['minimum_basket_price'], 
			 $data_admin['usage_quantity'], 
			 date('Y-m-d H:i:s'), 
			 date('Y-m-d H:i:s'), 
			 1,
		
		);
	
	
		if ($db2->query($sql, $data)) {
				return 1;
			}else{
				return 0;
			}
		}

		public function edit_voucher($id){

			$db2  =  $this->load->database('default',TRUE);
			$query = "SELECT * FROM sys_promotion_vouchers WHERE id = ? ";
			$data = array($id);
			return $this->db->query($query, $data);
		}


		public function get_shop_details($id){

			$db2  =  $this->load->database('default',TRUE);
			$query = "SELECT * FROM sys_shops WHERE id = ? ";
			$data = array($id);
			return $this->db->query($query, $data);
		}

		public function get_product_details($id){

			$db2  =  $this->load->database('default',TRUE);
			$query = "SELECT * FROM sys_products WHERE Id = ? ";
			$data = array($id);
			return $this->db->query($query, $data);
		}


		
		public function vouchers_update_data($data_admin){

			$db2  =  $this->load->database('default',TRUE);
			
				
			$sql = "UPDATE sys_promotion_vouchers
			        SET `voucher_type` = ?, 
					    `shop_id` = ?, 
						`product_id` = ?,
						`voucher_name` = ?,
						`voucher_code` = ?, 
						`start_date` = ?, 
						`end_date` = ?, 
						`disc_amount_type` = ?, 
						`disc_amount` = ?, 
						`max_discount_isset` = ?, 
						`max_discount_price` = ?, 
						`minimum_basket_price` = ?, 
						`usage_quantity` = ?, 
						`date_updated` = ?
				    WHERE `id` = ?
					";

			$data = array(
				        $data_admin['voucher_type'],
						$data_admin['shop_id'],
						$data_admin['product_id'],
						$data_admin['voucher_name'],
						$data_admin['voucher_code'],
						$data_admin['date_from'],
						$data_admin['date_to'],
						$data_admin['disc_ammount_type'],
						$data_admin['disc_ammount'],
						$data_admin['set_amount_limit'],
						$data_admin['disc_ammount_limit'],
						$data_admin['minimum_basket_price'],
						$data_admin['usage_quantity'],
						date('Y-m-d H:i:s'), 
						$data_admin['vouchers_id'],
		              	);

		
			  
			if ( $db2->query($sql, $data) ) 
			{
				return 1;
			}else{
				return 0;
			}
		
		}


		function disable_modal_confirm($disable_id, $record_status){


			$db2  =  $this->load->database('default',TRUE);
			$sql="UPDATE `sys_promotion_vouchers` SET `status` = ? WHERE `id` = ?";
			$data = array($record_status, $disable_id);
  
		  //  die($db2->query($sql, $data));
		   
			if ($db2->query($sql, $data)) {
			  return 1;
			}else{
			  return 0;
			}
		  }


		  public function voucher_delete($id)
		  {
			
			$db2  =  $this->load->database('default',TRUE);
		  
			  
		  // Update
				  $sql = "UPDATE sys_promotion_vouchers SET `status` = 0  WHERE `id` = ?";
	  
				  $data = array(  $id);
			
			  if ($db2->query($sql, $data)) {
				return 1;
			  }else{
				return 0;
			  }
	  
		  }


		  public function set_to_all($voucher_data){

			$sql = "UPDATE `sys_promotion_vouchers` SET `on_top` = ? WHERE `id` = ?";
			$data = array(
						1,
						$voucher_data['voucher_id'],
						);		
			$this->db->query($sql, $data);
	
	
		}


		
	public function unset_to_all($voucher_data){

		$sql = "UPDATE `sys_promotion_vouchers` SET `on_top` = ? WHERE `id` = ?";
		$data = array(
			        0,
					$voucher_data['voucher_id'],
					);		
		$this->db->query($sql, $data);


	}
		
		


    # End - Promotion


    public function campaign_type_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_name  = $filters['_name'];

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'name'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM campaign_type";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * 
			FROM campaign_type WHERE status > 0";


		if($_name != ""){
			$sql.=" AND name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody

			$nestedData=array(); 
			$nestedData[] = $row["name"];

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['campaign_type']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['campaign_type']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';

			$set_label = '';
	
            if($row['on_top'] == 0){
			       $set_label .= '<label class="switch" data-toggle="modal" data-target="#setFeadutedModal" data-id="'.$row['id'].'"> <input type="checkbox"> <span class="slider round"></span></label>';
			}else{
		        	$set_label .= '<label class="switch" data-toggle="modal" data-target="#unsetFeadutedModal" data-id="'.$row['id'].'"> <input checked  type="checkbox"> <span class="slider round"></span></label>';
			}

			$cater = '';
	
            if($row['loss_promo'] == 1){
			       $cater .= '<label class="switch" data-toggle="modal" data-target="#setFeadutedModalPromo" data-id="'.$row['id'].'"> <input type="checkbox"> <div class="slider round"><span class="on">Merchant</span>
  							<span class="off">Toktokmall</span></div></label>';
			       
			}else{
		        	$cater .= '<label class="switch" data-toggle="modal" data-target="#unsetFeadutedModalPromo" data-id="'.$row['id'].'"> <input type="checkbox" checked> <div class="slider round"><span class="on">Merchant</span>
  							<span class="off">Toktokmall</span></div></label>';
		        	
			}

			

			$nestedData[] = $cater;
			$nestedData[] = $actions;
			$nestedData[] = $set_label;
			
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

	public function get_campaign_type_name($name){
		$sql = "SELECT * FROM campaign_type
		WHERE name = ? AND status <> 0";
		$data = array($name);
		return $this->db->query($sql, $data);
	}

	public function campaign_type_add_modal_confirm($name, $promo_img){
		$sql = "INSERT INTO `campaign_type` (`name`,`promo_img`)
				VALUES (?,?)";
		$data = array($name,$promo_img);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_campaign_type_data($edit_id){
		$sql = "SELECT *
				FROM campaign_type 
				WHERE id = ?";
		$data = array($edit_id);
		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function campaign_type_update_modal_confirm($id, $name, $promo_img){
		$sql = "UPDATE `campaign_type` SET `name` = ? WHERE `id` = ?";
		$data = array($name, $id);


		if($promo_img!=""){
			$sql = "UPDATE `campaign_type` SET `promo_img` = ? WHERE `id` = ?";
			$data = array($promo_img, $id);
		}
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function campaign_type_delete_modal_confirm($delete_id){
		$sql = "UPDATE `campaign_type` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

    public function get_all_campaign_type(){
		$sql = "SELECT * FROM campaign_type WHERE status = 1 AND id != 1";
		return $this->db->query($sql)->result_array();
	}

	public function mc_product_table($sys_shop, $requestData){
		// storing  request (ie, get/post) global array to a variable  
		$token_session   = $this->session->userdata('token_session');
		$token           = en_dec('en', $token_session);
		$branchid        = $this->session->userdata('branchid');
        // $_name          = sanitize($this->input->post('_name'));
        $_name 			 = sanitize($this->input->post('_name'));
        $select_category = sanitize($this->input->post('select_category'));
		$productArray    = json_decode($this->input->post('productArray'));
        $productArray    = json_decode(json_encode($productArray), true);

		// print_r($productArray);
		// die();

	
		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'Id',
            1 => 'itemname',
            2 => 'category_name',
            3 => 'price',
            4 => 'no_of_stocks',
            5 => 'shopname',
            6 => 'enabled',
		);

		$sql = "SELECT a.*, code.shopcode, c.category_name, b.shopname, d.itemname as parent_product_name FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id
				LEFT JOIN sys_products AS d ON a.parent_product_id = d.Id
                WHERE a.enabled = 1";

		// getting records as per search parameters
               // print_r($productArray);

		if(!empty($productArray)){
			$productString = "(";
			foreach($productArray as $value){
				if($value != ''){
					$productString .= "'".$value['product_id']."', ";
				}
			}
			$productString = rtrim($productString, ', ');
			$productString .= ")";
		}
		else{
			$productString = "";
		}

        if($_name != ""){
			$sql.=" AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		
		if($select_category != ""){
			$sql.=" AND a.cat_id = " . $this->db->escape($select_category) . "";
		}

		if($sys_shop != ""){
			$sql.=" AND a.sys_shop = " . $this->db->escape($sys_shop) . "";
		}
		if($productString != ""){
			$sql .=" AND a.Id NOT IN $productString";

		}

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";  // adding length
        // $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query = $this->db->query($sql);
        

		$data  = array();
		$count = 0;
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			if($row['variant_isset'] == 0 && $row['parent_product_id'] == ''){
				$nestedData     = array(); 
				$otherinfo      = ($row['otherinfo'] != '' || $row['otherinfo'] != null) ? ' ('.$row['otherinfo'].')' : '';
				$parent_product = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ': '';
				$no_of_stocks   = ($row['no_of_stocks'] != '') ? number_format($row['no_of_stocks'], 1) : 'None';
				$nestedData[]   = '<input type="checkbox" class="form-control checkbox_perprod" name="checkbox_perprod[]" id="checkbox_perprod" value="'.$row['Id'].'" data-product_name="'.str_replace('"', '&quot;', $parent_product.$row["itemname"].$otherinfo).'" data-product_price="'.$row["price"].'" data-product_stock="'.$no_of_stocks.'" data-sys_shop="'.$row["sys_shop"].'">';
				$nestedData[]   = $parent_product.$row["itemname"];
				$nestedData[]   = "₱".number_format($row["price"], 2);
				$nestedData[]   = $no_of_stocks;
				$data[]         = $nestedData;
			}
			else if($row['parent_product_id'] != ''){
				$nestedData     = array(); 
				$parent_product = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ': '';
				$no_of_stocks   = ($row['no_of_stocks'] != '') ? number_format($row['no_of_stocks'], 1) : 'None';
				$nestedData[]   = '<input type="checkbox" class="form-control checkbox_perprod" name="checkbox_perprod[]" id="checkbox_perprod" value="'.$row['Id'].'" data-product_name="'.$parent_product.$row["itemname"].'" data-product_price="'.$row["price"].'" data-product_stock="'.$no_of_stocks.'" data-sys_shop="'.$row["sys_shop"].'">';
				$nestedData[]   = $parent_product.$row["itemname"];
				$nestedData[]   = "₱".number_format($row["price"], 2);
				$nestedData[]   = $no_of_stocks;
				$data[]         = $nestedData;
			}
			else{
				$count++;
			}

		}
		
		$json_data = array(
			// "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData - $count ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered - $count ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function mc_fetch_productPromo($sys_shop){
		$sql=" SELECT a.promo_type, a.promo_rate, a.promo_price, a.start_date, a.end_date, a.promo_stock, a.purchase_limit, a.status,a.product_id, b.sys_shop, b.price, b.itemname, c.itemname as parent_product_name, b.otherinfo as item_otherinfo, b.no_of_stocks, DATE(a.start_date) as startdate, DATE(a.end_date) as enddate, TIME(a.start_date) as starttime, TIME(a.end_date) as endtime FROM sys_products_promotion AS a
		LEFT JOIN sys_products AS b ON a.product_id = b.Id
		LEFT JOIN sys_products AS c ON b.parent_product_id = c.Id
		WHERE a.status > 0 AND a.promo_type != 1";

		if($sys_shop != ""){
			$sql.=" AND b.sys_shop = " . $this->db->escape($sys_shop) . "";
		}

		$sql .= " ORDER BY a.date_created ASC";
	
        return $this->db2->query($sql);;
	}

	function get_all_region(){
        $sql="SELECT * FROM sys_region WHERE status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

    function sf_name_is_exist($name){
        $sql="SELECT COUNT(*) as count FROM sys_shipping_discount WHERE UPPER(shipping_discount_name) = ? AND status = ?";
    	$data = array(strtoupper($name), 1);
    	return $this->db->query($sql, $data)->row()->count;
    }

    function sf_name_is_exist_update($name,$id){
        $sql="SELECT COUNT(*) as count FROM sys_shipping_discount WHERE UPPER(shipping_discount_name) = ? AND status = ? AND id != ?";
    	$data = array(strtoupper($name), 1, $id);
    	return $this->db->query($sql, $data)->row()->count;
    }

    function sf_code_is_exist($name){
        $sql="SELECT COUNT(*) as count FROM sys_shipping_discount WHERE UPPER(shipping_discount_code) = ? AND status = ?";
    	$data = array(strtoupper($name), 1);
    	return $this->db->query($sql, $data)->row()->count;
    }
    function sf_code_is_exist_update($name,$id){
        $sql="SELECT COUNT(*) as count FROM sys_shipping_discount WHERE UPPER(shipping_discount_code) = ? AND status = ? AND id != ?";
    	$data = array(strtoupper($name), 1, $id);
    	return $this->db->query($sql, $data)->row()->count;
    }

	public function check_sfd_date($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_shipping_discount` WHERE DATE(`start_date`) <= DATE(?) AND DATE(`end_date`) >= DATE(?) /* startdate yung enddate comparison OR `end_date` = '0000-00-00 00:00:00' */ AND `shop_id` = ? AND `status` = ?";
		return $this->db->query($sql, $data)->row()->count;
	}
	public function check_sfd_date_endDate($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_shipping_discount` WHERE DATE(`end_date`) >= DATE(?) /* startdate yung enddate comparison */ AND `shop_id` = ? AND `status` = ?";
		return $this->db->query($sql, $data)->row()->count;
	}
	public function check_sfd_endDateOnly($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_shipping_discount` WHERE DATE(`end_date`) >= DATE(?) AND `shop_id` = ? AND `status` = ?";
		return $this->db->query($sql, $data)->row()->count;
	}
	public function check_sfd_date_update($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_shipping_discount` WHERE DATE(`start_date`) <= DATE(?) AND DATE(`end_date`) >= DATE(?) /* startdate yung enddate comparison OR `end_date` = '0000-00-00 00:00:00' */ AND `shop_id` = ? AND `status` = ? AND id != ?";
		return $this->db->query($sql, $data)->row()->count;
	}
	public function check_sfd_date_endDate_update($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_shipping_discount` WHERE DATE(`end_date`) >= DATE(?) /* startdate yung enddate comparison */ AND `shop_id` = ? AND `status` = ? AND id != ?";
		return $this->db->query($sql, $data)->row()->count;
	}
	public function check_sfd_endDateOnly_update($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_shipping_discount` WHERE DATE(`end_date`) >= DATE(?) AND `shop_id` = ? AND `status` = ? AND id != ?";
		return $this->db->query($sql, $data)->row()->count;
	}

	function set_end_date_is_exist($shopid){
        $sql="SELECT COUNT(*) as count FROM sys_shipping_discount WHERE set_end_date = ? AND shop_id = ? AND status = ?";
    	$data = array(0,$shopid,1);
    	return $this->db->query($sql, $data)->row()->count;
    }

    function set_end_date_is_exist_update($shopid,$id){
        $sql="SELECT COUNT(*) as count FROM sys_shipping_discount WHERE set_end_date = ? AND shop_id = ? AND status = ? AND id != ?";
    	$data = array(0,$shopid,1,$id);
    	return $this->db->query($sql, $data)->row()->count;
    }

    public function insert_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $is_percentage, $minimum_price, $region, $limitOne_perCustomer, $subsidized, $is_sfCodeRequired, $sf_amount, $user_type_created,$setEndDate,$shouldered_by) {
		$sql = "INSERT INTO sys_shipping_discount (`shop_id`, `shipping_discount_name`, `start_date`, `end_date`, `requirement_isset`, `is_percentage`, `minimum_price`,  `sf_discount`, `region`, `limitone_perCustomer`, `date_created`, `date_updated`, `status`, `is_sfCodeRequired`, `shipping_discount_code`, `no_of_stocks`, `is_subsidize`, `user_type_created`, `set_end_date`, `shouldered_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
		$data = array($select_shop, $sfd_name, $start_date, $end_date, $requirement, $is_percentage, $minimum_price, $sf_amount, $region , $limitOne_perCustomer, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 1, $is_sfCodeRequired, $sfd_code, $usage_qty, $subsidized,$user_type_created,$setEndDate,$shouldered_by);
		return $this->db->query($sql, $data);
	}

	public function shipping_fee_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_name  = $filters['_name'];
		$_select_shop  = $filters['_select_shop'];

		$shopid = $this->session->userdata('sys_shop_id');

		$shop_id = "";

		if($shopid != 0){
			$shop_id = " AND shop_id = ".$shopid;
		}

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'shipping_discount_name'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM sys_shipping_discount WHERE status > 0".$shop_id;

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData;

		$sql = "SELECT * 
			FROM sys_shipping_discount WHERE status > 0".$shop_id;

		if($_name != ""){
			$sql.=" AND shipping_discount_name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_select_shop != 0){
			$sql.=" AND shop_id=".$_select_shop;
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		// print_r($query);
		// die();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody

			$nestedData=array(); 
                   
        	$nestedData[] = $row["shipping_discount_name"];

			$condition = "No Requirements";
			if($row["requirement_isset"] != 0){

				$condition = "";
				$minimum_price = explode(',', $row["minimum_price"]);
				$is_subsidize = explode(',',$row["is_subsidize"]);
				$is_percentage = explode(',', $row["is_percentage"]);
				$sf_discount = explode(',',$row["sf_discount"]);

        		// $counter = count($minimum_price);
        		// $count = 0;
				
				foreach ($minimum_price as $key => $value) {
					$condition .= "Buy <b>&#8369;".number_format($value,2)."</b>, shipping fee ";

					if (array_key_exists($key, $is_subsidize)) {
						$subsidized = $is_subsidize[$key];
					}

					if (array_key_exists($key, $is_percentage)) {
						$percentage = $is_percentage[$key];
					}

					if (array_key_exists($key, $sf_discount)) {
						$sf_disc = $sf_discount[$key];
					}

					if ($subsidized == 0 || $subsidized == '') {
						if ($percentage == 0 || $percentage == '') {
							$condition .= "less <b>&#8369;".number_format($sf_disc,2)."</b></br>";
						} else {
							$condition .= "less <b>".intval($sf_disc)."&#37;</b></br>";
						}
					} else {
						$condition .= " is <b>FREE.</b></br>";
					}

				}
			}

			$nestedData[] = $condition;

			$status = "";

			$date_today = today_datetime_dash_reverse();

			if($row["start_date"] > $date_today){
				$status = "<p style='background-color:#91dfec;'><b>Upcoming</b></p>";
			}

			else if($row["end_date"] >= $date_today && $row["start_date"] <= $date_today || $row["end_date"] == '0000-00-00 00:00:00'){
				$status = "<p style='background-color:#aeeca5;'><b>On Going</b></p>";
			}

			else if ($row["end_date"] < $date_today){
				$status = "<p style='background-color:#d2cfcf;'><b>Expired</b></p>";
			}

			// if($row["promo_limit"] == 0){
			// 	$status = "<p style='background-color:#aeeca5;'><b>On Going</b></p>";
			// }

			$nestedData[] = $status;

			// $promo_period = "No Limit";
			
			$end_date = $row["end_date"] == "0000-00-00 00:00:00" ? 'Not Set' : '<br>'.date('Y-m-d h:i A', strtotime($row["end_date"]));
			$promo_period = "<b>Start Date:</b><br>".date('Y-m-d h:i A', strtotime($row["start_date"]))."<br><b>End Date:</b> ".$end_date;
			
			$nestedData[] = $promo_period;

			//shouldered by

			$shouldered = '';
	
            if($row['shouldered_by'] == 1){
			       $shouldered .= '<label class="switch" data-toggle="modal" data-target="#setFeadutedModalPromo" data-id="'.$row['id'].'"> <input type="checkbox"> <div class="slider round"><span class="on">Merchant</span>
  							<span class="off">Toktokmall</span></div></label>';
			       
			}else{
		        	$shouldered .= '<label class="switch" data-toggle="modal" data-target="#unsetFeadutedModalPromo" data-id="'.$row['id'].'"> <input type="checkbox" checked> <div class="slider round"><span class="on">Merchant</span>
  							<span class="off">Toktokmall</span></div></label>';
		        	
			}

			$nestedData[] = $shouldered;

			//

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['sf_discount']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['sf_discount']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';

			$set_label = '';
	
            if($row['on_top'] == 0){
			       $set_label .= '<label class="switch" data-toggle="modal" data-target="#setFeadutedModal" data-id="'.$row['id'].'"> <input type="checkbox"> <span class="slider round"></span></label>';
			}else{
		        	$set_label .= '<label class="switch" data-toggle="modal" data-target="#unsetFeadutedModal" data-id="'.$row['id'].'"> <input checked  type="checkbox"> <span class="slider round"></span></label>';
			}
			
			$nestedData[] = $actions;
			$nestedData[] = $set_label;

                
	           
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

	public function get_sf_data($edit_id){
		$sql = "SELECT * FROM sys_shipping_discount 
				WHERE id = ?";
		$data = array($edit_id);
		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function sf_delete_modal_confirm($delete_id){
		$sql = "UPDATE `sys_shipping_discount` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function update_shipping_discount($sfd_name, $sfd_code, $select_shop, $usage_qty, $start_date, $end_date, $requirement, $minimum_price, $region, $subsidized, $sf_amount, $edit_id, $is_percentage, $limitOne, $is_sfCodeRequired ,$edit_setEndDate) {

	 	$sql = "UPDATE `sys_shipping_discount` SET `shop_id` = ?, `shipping_discount_name` = ?, `start_date` = ?, `end_date` = ?, `requirement_isset` = ?, `is_percentage` = ?, `minimum_price` = ?, `sf_discount` = ?, `region` = ?, `date_updated` = ?, `shipping_discount_code` = ?, `no_of_stocks` = ?, `is_subsidize` = ?, `limitone_perCustomer` = ?, `is_sfCodeRequired` = ?, `set_end_date` = ? WHERE `id` = ?";
		$data = array(
			        $select_shop, $sfd_name, $start_date, $end_date, $requirement, $is_percentage, $minimum_price, $sf_amount, $region , date('Y-m-d H:i:s'), $sfd_code, $usage_qty, $subsidized, $limitOne, $is_sfCodeRequired, $edit_setEndDate, $edit_id
					);		
		$this->db->query($sql, $data);


	}

	public function sf_set_to_all($voucher_data){

		$sql = "UPDATE `sys_shipping_discount` SET `on_top` = ? WHERE `id` = ?";
		$data = array(
					1,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);
	}

		
	public function sf_unset_to_all($voucher_data){

		$sql = "UPDATE `sys_shipping_discount` SET `on_top` = ? WHERE `id` = ?";
		$data = array(
			        0,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);

	}

	public function ct_set_to_all($voucher_data){

		$sql = "UPDATE `campaign_type` SET `on_top` = ? WHERE `id` = ?";
		$data = array(
					1,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);
	}

		
	public function ct_unset_to_all($voucher_data){

		$sql = "UPDATE `campaign_type` SET `on_top` = ? WHERE `id` = ?";
		$data = array(
			        0,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);

	}

	public function discount_promo_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_name  = $filters['_name'];
		$_start_date  = $filters['_start_date'];
		$_end_date  = $filters['_end_date'];

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'name'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM campaign_type 
				WHERE status > 0 AND id != 1";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT DISTINCT(a.name), a.id, b.start_date, b.end_date
				FROM campaign_type a 
				JOIN sys_products_promotion b 
				ON a.id = b.promo_type
				WHERE a.id != 1 AND a.status >0";


		if($_name != ""){
			$sql.=" AND a.name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}


		$start_date = date_format(date_create($_start_date), 'Y-m-d');
		$end_date = date_format(date_create($_end_date), 'Y-m-d');

		if($_start_date != ""){

			$sql.=" AND b.start_date BETWEEN '".$start_date."' AND '".$end_date."' OR '".$start_date."' BETWEEN b.start_date AND b.end_date";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody


			$date_today = todaytime();

			$nestedData=array(); 
			$nestedData[] = $row["name"];


			$status = "";

			if($row["start_date"] >= $date_today ){

				$status = "<p style='background-color:#91dfec;'><b>Upcoming</b></p>";
			}

			else if($row["end_date"] >= $date_today){

				$status = "<p style='background-color:#aeeca5;'><b>On Going</b></p>";
			}

			else{

				$status = "<p style='background-color:#d2cfcf;'><b>Expired</b></p>";
			}

			$ci = get_instance();
		    $ci->load->helper('url');
			$token_session = $this->session->userdata('token_session');
        	$token = en_dec('en', $token_session);

			if ($this->loginstate->get_access()['mystery_coupon']['view'] == 1) {
            $actions = ' 
				<a href="'.$ci->config->config['base_url'].'promotion/Main_promotion/discount_fee_byId/'.$token.'/'.$row['id'].'" data-value="'.$row['id'].'"><button data-backdrop="static" data-value="'.$row["id"].'" data-keyboard="false" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-info mb-3 action_view">View</button></a>';
			}

			$nestedData[] = $status;
			$nestedData[] = $row["start_date"]." - ".$row["end_date"];
			$nestedData[] = $actions;
			
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


	public function mc_fetch_productPromo_id($data){
		$sql=" SELECT a.promo_type, a.promo_rate, a.promo_price, a.start_date, a.end_date, a.promo_stock, a.purchase_limit, a.status,a.product_id, b.sys_shop, b.price, b.itemname, c.itemname as parent_product_name, b.otherinfo as item_otherinfo, b.no_of_stocks, DATE(a.start_date) as startdate, DATE(a.end_date) as enddate, TIME(a.start_date) as starttime, TIME(a.end_date) as endtime FROM sys_products_promotion AS a
		LEFT JOIN sys_products AS b ON a.product_id = b.Id
		LEFT JOIN sys_products AS c ON b.parent_product_id = c.Id
		WHERE a.status > 0 AND a.promo_type != 1";

		if($data["sys_shop"] != ""){
			$sql.=" AND b.sys_shop = " . $this->db->escape($data["sys_shop"]) . "";
		}

		if($data["id"] != ""){
			$sql.=" AND a.promo_type = " . $this->db->escape($data["id"]) . "";
		}

		$sql .= " ORDER BY a.date_created ASC";

		return $this->db2->query($sql);
	
     }  
     public function getPromoDetailsByPromoId($id){
		$sql=" SELECT * FROM sys_products_promotion
		WHERE promo_type = ? AND status > 0";
		$params = array(
			$id
		);
        return $this->db->query($sql, $params)->result_array();
	}

	public function ct_set_to_all_promo($voucher_data){

		$sql = "UPDATE `campaign_type` SET `loss_promo` = ? WHERE `id` = ?";
		$data = array(
					2,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);
	}

		
	public function ct_unset_to_all_promo($voucher_data){

		$sql = "UPDATE `campaign_type` SET `loss_promo` = ? WHERE `id` = ?";
		$data = array(
			        1,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);
	}


	public function getCampaignType()
	{
		$sql = "SELECT * FROM campaign_type WHERE `status` = 1 ORDER BY `featured_status` DESC";
		return $this->db->query($sql)->result();
	}

	public function unsetFeaturedCampaign()
	{
		$sql = "UPDATE campaign_type SET featured_status = 0";
		return $this->db->query($sql);
	}
	public function setFeaturedCampaign($id)
	{
		$sql = "UPDATE campaign_type SET featured_status = 1 WHERE id = ?";
		 return $this->db->query($sql, $id);
	}

	public function ct_set_shouldered_by($voucher_data){

		$sql = "UPDATE `sys_shipping_discount` SET `shouldered_by` = ? WHERE `id` = ?";
		$data = array(
					2,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);
	}

		
	public function ct_unset_shouldered_by($voucher_data){

		$sql = "UPDATE `sys_shipping_discount` SET `shouldered_by` = ? WHERE `id` = ?";
		$data = array(
			        1,
					$voucher_data['id'],
					);		
		$this->db->query($sql, $data);
	}


} 