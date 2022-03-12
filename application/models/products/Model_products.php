<?php 
class Model_products extends CI_Model {

	# Start - Products

	public function save_product($args,$imgArr,$featured_product,$featured_product_arrangment) {

		$branchid = $this->session->userdata('branchid');
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2]         = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3] 		   = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4] 		   = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5]         = (!empty($imgArr[5])) ? $imgArr[5] : '';

		$args['f_status']  = ($args['f_itemid'] == '') ? 2 : $args['f_status'];

		$sql = "INSERT INTO sys_products (`category_id`,`name`,`price`, `tags`, `no_of_stocks`, `max_qty_isset`, `max_qty`, `summary`, `img`, `img_2`, `img_3`, `img_4`, `img_5`, `img_6`, `enabled`, `date_created`, `date_updated`, `variant_isset`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";

		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}
		// print_r($args);
		// die();
		
		$bind_data = array(
			$args['f_category'],
			$args['f_itemname'],
			$args['f_price'],
			$args['f_tags'],
			$args['f_no_of_stocks'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_summary'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			1,
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			$args['f_variants_isset']
		);

		$this->db->query($sql, $bind_data);

		$id =$this->db->query("SELECT id from sys_products order by date_created DESC limit 1")->row()->id;
		foreach($imgArr as $key => $value){
			if($value != ""){
				$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
				$bind_data = array(
					$id,
					$key+1,
					$value,
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
			}
		}

		$sql = "UPDATE sys_products SET img = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$id,
			$id
		);
		$this->db->query($sql, $bind_data);
		$data = array('success'=>true,'id'=>$id);
		return $data;
	}

	public function getVariants($product_id) {
		$query  = "SELECT * FROM sys_products WHERE enabled > 0 AND  parent_product_id = ?";
		$params = array($product_id);
		return $this->db->query($query, $params)->result_array();
	}



	public function getParentProduct($id){
		$query="SELECT * FROM sys_products WHERE Id = '$id'";
		return $this->db->query($query)->result_array();
		
	}

	public function getSysShopsDetails($id){
		$query="SELECT * FROM sys_shops WHERE id = '$id' AND status = '1' ";
		return $this->db->query($query)->result_array();
	}
	public function check_products_id($id){
		$sql=" SELECT * FROM sys_products WHERE Id = ? AND enabled > 0";
		$params = array($id);
       
        return $this->db->query($sql, $params);
        
	}
	
	public function check_products($id){
		$sql=" SELECT a.*, d.shopcode, d.shopname  
		FROM sys_products AS a
		LEFT JOIN sys_shops AS d ON 1 = d.id 
		WHERE a.Id = ?";
		$params = array($id);
       
        return $this->db->query($sql, $params);
        
    }
	public function update_product($args, $id, $save_promo_log, $imgArr,$featured_product,$featured_product_arrangment) {
		$branchid    	   = $this->session->userdata('branchid');
		$get_product 	   = $this->check_products($id)->row_array();
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2]         = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3]         = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4]         = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5]   	   = (!empty($imgArr[5])) ? $imgArr[5] : '';

		$f_status          = $get_product['enabled'];
		$sql = "UPDATE sys_products SET category_id = ?, name = ?, price =?,  tags = ?, no_of_stocks = ?, max_qty_isset = ?, max_qty = ?, summary = ?, img = ?, img_2 = ?, img_3 = ?, img_4 = ?, img_5 = ?, img_6 = ?, enabled = ?, date_updated = ?, variant_isset = ? WHERE Id = ?";
		
		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}
		
		$bind_data = array(
			$args['f_category'],
			$args['f_itemname'],
			$args['f_price'],
			$args['f_tags'],
			$args['f_no_of_stocks'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_summary'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			$f_status,
			date('Y-m-d H:i:s'),
			$args['f_variants_isset'],
			$id
		);

		$this->db->query($sql, $bind_data);
		$string = $this->audittrail->checkProductChanges_sys_products($get_product, $args);
		
		if($imgArr[0] != ''){
			$sql = "UPDATE sys_products_images SET status = 0 WHERE product_id = ? AND status = 1";
			$bind_data = array(
				$id
			);

			$this->db->query($sql, $bind_data);

			foreach($imgArr as $key => $value){
				if($value != ""){
					$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
					$bind_data = array(
						$id,
						$key+1,
						$value,
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}
			}
		}

		$sql = "UPDATE sys_products SET img = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$id,
			$id
		);

		$this->db->query($sql, $bind_data);

		return $string;
	}

	public function update_variant($args, $id, $save_promo_log, $imgArr,$featured_product,$featured_product_arrangment, $delivery_areas_str) {


		$branchid    	   = $this->session->userdata('branchid');
		$get_product 	   = $this->check_products($id)->row_array();
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2]         = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3]         = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4]         = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5]   	   = (!empty($imgArr[5])) ? $imgArr[5] : '';


		
		//delivery areas, only set to jcww
		
		$str_update = "";
		$f_status          = ($args['f_itemid'] == '') ? 2 : $get_product['enabled'];

		$sql = "UPDATE sys_products SET category_id = ?, name = ?, price =?,  tags = ?, no_of_stocks = ?, max_qty_isset = ?, max_qty = ?, summary = ?, img = ?, img_2 = ?, img_3 = ?, img_4 = ?, img_5 = ?, img_6 = ?, enabled = ?, date_updated = ?, variant_isset = ? WHERE Id = ?";
		
		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}
		
		$bind_data = array(
			$args['f_category'],
			$args['f_itemname'],
			$args['f_price'],
			$args['f_tags'],
			$args['f_no_of_stocks'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_summary'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			$f_status,
			date('Y-m-d H:i:s'),
			$args['f_variants_isset'],
			$id
		);
		
		$this->db->query($sql, $bind_data);
		$string = $this->audittrail->checkProductChanges_sys_products($get_product, $args);
		
		if($imgArr[0] != ''){
			$sql = "UPDATE sys_products_images SET status = 0 WHERE product_id = ? AND status = 1";
			$bind_data = array(
				$id
			);

			$this->db->query($sql, $bind_data);

			foreach($imgArr as $key => $value){
				if($value != ""){
					$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
					$bind_data = array(
						$id,
						$key+1,
						$value,
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}
			}
		}

		$sql = "UPDATE sys_products SET img = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$id,
			$id
		);

		$this->db->query($sql, $bind_data);
		
		return $string;
	}

	public function save_variant($args, $imgArr,$featured_product,$featured_product_arrangment) {

		$branchid          = $this->session->userdata('branchid');
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2] 		   = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3] 	   	   = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4] 		   = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5] 		   = (!empty($imgArr[5])) ? $imgArr[5] : '';
		$str_insert = "";
		$str_value  = "";
		$args['f_status']  = $args['f_status'];

		//delivery areas condition. only set to jcww 
		$sql = "INSERT INTO sys_products (`category_id`,`name`,`price`, `no_of_stocks`, `max_qty_isset`, `max_qty`, `summary`, `img`, `img_2`, `img_3`, `img_4`, `img_5`, `img_6`, `enabled`, `date_created`, `date_updated`, `parent_product_id`, `variant_isset`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";

		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}
		// print_r($args);
		// die();
		
		$bind_data = array(
			$args['f_category'],
			$args['f_itemname'],
			$args['f_price'],
			$args['f_no_of_stocks'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_summary'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			1,
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			$args['f_parent_product_id'],
			$args['f_variants_isset']
		);

		$this->db->query($sql, $bind_data);
		$id =$this->db->query("SELECT id from sys_products order by date_created DESC limit 1")->row()->id;
		
		foreach($imgArr as $key => $value){
			
			if($value != ""){
				$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
				$bind_data = array(
					$id,
					$key+1,
					$value,
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);

				$sql = "UPDATE sys_products SET  variant_isset = 1 WHERE id = ".$args['f_parent_product_id'];
				$this->db->query($sql, $bind_data);
			}
		}


		$sql = "UPDATE sys_products SET img = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$id,
			$id
		);

		return $this->db->query($sql, $bind_data);
	}
	public function updateParentProductInventoryQty($Id){

		// $sql = "SELECT * FROM sys_products WHERE Id = ? AND enabled > 0";
		// $bind_data = array(
		// 	$Id
		// );	

		// $parentProduct = $this->db->query($sql, $bind_data)->row_array();
		// $parentInvQty  = $parentProduct['no_of_stocks'];

		$sql = "SELECT SUM(no_of_stocks) as total_qty_variant FROM sys_products WHERE parent_product_id = ? AND enabled > 0";
		$bind_data = array(
			$Id
		);	

		$variantProduct = $this->db->query($sql, $bind_data)->row_array();
		$variantInvQty  = $variantProduct['total_qty_variant'];

		$grand_total_no_of_stocks = $variantInvQty;

		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			$grand_total_no_of_stocks,
			$Id
		);	

		return $this->db->query($sql, $bind_data);
	}
	public function getAppMember($id){
		$query="SELECT * FROM app_members WHERE sys_user = '$id' AND status = '1' ";
		return $this->db->query($query)->result_array();
	}
	public function getAppmemberDetails($id){
		$query="SELECT * FROM app_members WHERE sys_shop = '$id' ";
		return $this->db->query($query)->result_array();
	}
	public function getImagesfilename($Id) {
		$query="SELECT * FROM sys_products_images
		WHERE product_id = ? AND status = 1
		ORDER BY arrangement ASC";
		
		$params = array($Id);
		return $this->db->query($query, $params);
	}
	public function getImageByFileName($filename,$update_isset = false,$id='') {
		$query="SELECT * FROM sys_products_images join sys_products on product_id = sys_products.id WHERE product_id = ".$id;
		if($id != ''){
			//$query.='WHERE filename = ? and product_id = '.$id.' OR (status = 0 AND filename = ?)';
		}
		//print_r($filename);
		// }else if($update_isset){
		//  	$query.='WHERE filename = ? and status = 1';
		// }else{
		// 	$query.='WHERE filename = ? ';
		// }
		//params = array($filename);
		// print_r($query);
		// print_r($filename);
		// print_r($id);
		// die();
		return $this->db->query($query);
	}

	public function get_productdetails($Id) {
		$query=" SELECT a.*, d.shopcode, d.shopname
		FROM sys_products AS a 
		LEFT JOIN sys_shops AS d ON 1 = d.id 
		WHERE a.Id = ? AND a.enabled > 0;";
		
		$params = array($Id);
		return $this->db->query($query, $params)->row_array();
	}

	public function checkFeaturedProductArrangement($product_number){
		$query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' AND set_product_arrangement = '$product_number'  AND set_product_arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}
    
    public function get_shopcode_via_shopid($id) {
		$query="SELECT * FROM sys_shops WHERE id = 1 AND status > 0";
		$params = array($id);

		return $this->db->query($query, $params)->row()->shopcode;
	}

    public function disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `sys_products` SET `enabled` = ? WHERE `Id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}
	public function delete_modal_confirm($delete_id){
		$sql = "UPDATE `sys_products` SET `enabled` = '0' WHERE `Id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}
	
	public function checkOrderActive($product_id){
		$query="SELECT * from sys_orders join sys_products on product_id = sys_products.id
		WHERE product_id = ? OR parent_product_id = ? AND status_id <> '0' AND date(sys_orders.date_created) >= ? LIMIT 1";
		
		$params = array(
			$product_id,
			$product_id,
			date('Y-m-d', strtotime("-30 days"))
		);
		return $this->db->query($query, $params);
	}

	public function delete_modal_confirm2($delete_id)
	{
		$sql = "UPDATE `sys_products` SET `enabled` = '0' WHERE `parent_product_id` = ?";
		if ($this->db->query($sql, $delete_id)) {
			return 1;
		} else {
			return 0;
		}
	}

    public function getFeaturedProductCount(){
        $query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ";
        return $this->db->query($query)->num_rows();
    }
    public function product_table($sys_shop, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$date_from      = $this->input->post('date_from');
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_categories 	= $this->input->post('_categories');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'name',
            1 => 'name',
            2 => 'category_name',
            3 => 'price',
            4 => 'no_of_stocks',
            5 => 'shopname',
            6 => 'enabled',
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_products a 
                LEFT JOIN sys_shops b ON 1 = b.id AND b.status > 0
                LEFT JOIN sys_product_category c ON a.category_id = c.id AND c.status > 0
				WHERE a.enabled > 0 AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		if (!$exportable) {
			$sql = "SELECT a.*, code.shopcode, c.category_name, no_of_stocks FROM sys_products a 
                LEFT JOIN sys_shops b ON 1 = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.category_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON 1 = code.id";
			$sql = "SELECT a.*, code.shopcode, c.category_name, no_of_stocks FROM sys_products a 
                LEFT JOIN sys_shops b ON 1 = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.category_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON 1 = code.id ";
				// LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1";
		}
		else{
			$sql = "SELECT a.*, code.shopcode, c.category_name, no_of_stocks,img.filename FROM sys_products a 
                LEFT JOIN sys_shops b ON 1 = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.category_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON 1 = code.id 
				LEFT JOIN sys_products_images img ON a.id = img.product_id";
		}
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		
		if($_name != ""){
			$sql.=" AND a.name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_categories != ""){
			$sql.=" AND c.id = " . $this->db->escape($_categories) . "";
		}

		if($date_from != ""){
			$sql.="  AND DATE_FORMAT(a.`date_created`, '%m/%d/%Y') ='".$date_from. "'";
		}
		$sql.=" AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";  // adding length
		if (!$exportable) {
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}

		// print_r($sql);
		// exit();
		
		$query = $this->db->query($sql);

		$data = array();
		$get_s3_imgpath_upload = get_s3_imgpath_upload();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = (!$exportable) ? '<img class="img-thumbnail" style="width: 50px;" src="'.base_url('assets/uploads/products/'.str_replace('==','',$row['img']).'?'.rand()).'">' : '';;
			// $nestedData[] = (!$exportable) ?'<u><a href="'.base_url('Main_products/view_products/'.$token.'/'.$row['id']).'" style="color:blue;">'.$row["name"].'</a></u>':$row["name"];
			$nestedData[] = $row["name"];
			$nestedData[] = $row["category_name"];
			$nestedData[] = number_format($row["price"], 2);


			
			// $inv_qty_branch = $this->get_uptodate_nostocks($row['sys_shop'], $row['id']);
			// $total_inv_qty  = 0;

			// if($inv_qty_branch != false){
			// 	foreach($inv_qty_branch as $val){
			// 		$total_inv_qty += $val['total_inv_qty'];
			// 	}
			// }
			// $total_inv_qty_main = ($this->get_uptodate_nostocks_main($row['id']) != false) ? $this->get_uptodate_nostocks_main($row['id'])->total_inv_qty:0;
			// $grand_total_qty = ($total_inv_qty+$total_inv_qty_main == 0) ? $row['no_of_stocks'] : $total_inv_qty+$total_inv_qty_main;
			// $no_of_stocks = ($branchid != 0) ? (!empty($this->get_invqty_branch($branchid, $row['id'])['no_of_stocks']) ? $this->get_invqty_branch($branchid, $row['id'])['no_of_stocks'] : 0) : $grand_total_qty;
			// $no_of_stocks = ($branchid != 0 && $no_of_stocks == 0) ? $this->getParentProductInvBranch($row['id'], $branchid) : $no_of_stocks;
			// $nestedData[] = number_format($no_of_stocks, 1);

			$nestedData[] = $row["no_of_stocks"];



			if($row["enabled"]==1){
                $nestedData[] = 'Enabled';
            }else{
                $nestedData[] = 'Disabled';
            }

			if ($row['enabled'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['enabled'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			$buttons = "";
			$buttons .= '
				<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('admin/Main_products/view_products/'.$token.'/'.$row['id']).'"><i class="fa fa-search" aria-hidden="true"></i> View</a>';

			if($this->loginstate->get_access()['products']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('admin/Main_products/update_products/'.$token.'/'.$row['id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
			}

            /// checker of product in product status table
			if(ini() == 'toktokmall'){


				$sql = "SELECT * FROM sys_product_status WHERE product_id = '".$row['id']."' AND status != '1'";
				$check_status_product = $this->db->query($sql);		
	
				if($check_status_product->num_rows() == 0){
	
						if($this->loginstate->get_access()['products']['disable'] == 1){
							$buttons .= '<div class="dropdown-divider"></div>
							<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
						}
		
				}else{
	
				}

			}else{

				if($this->loginstate->get_access()['products']['disable'] == 1){
					$buttons .= '<div class="dropdown-divider"></div>
					<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
				}

			}
		

			if($this->loginstate->get_access()['products']['delete'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}


			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    '.$buttons.'
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

	

	public function deleteVariant($Id){
		$sql = "UPDATE sys_products SET `enabled` = '0' WHERE Id = ?";
		$bind_data = array(
			$Id
		);	

		$this->db->query($sql, $bind_data);

		// $sql = "UPDATE sys_products_promotion SET `status` = '0' WHERE product_id = ?";
		// $bind_data = array(
		// 	$Id
		// );	

		// $this->db->query($sql, $bind_data);
	}
	public function update_variants($product_id, $child_product_id, $variant_name, $variant_price, $variant_sku, $variant_status) {
		$sql = "UPDATE sys_products SET name = ?, price = ?, enabled = ?, date_updated = ?";



	 	$sql .= " WHERE Id = ? AND parent_product_id = ? ";
		
		$variant_price = ($variant_price == '') ? 0: $variant_price;
		$bind_data = array(
			$variant_name,
			$variant_price,
			$variant_status,
			date('Y-m-d H:i:s'),
			$child_product_id,
			$product_id
		);	

		return $this->db->query($sql, $bind_data);
	}

    public function get_shopcode($user_id){
		$sql=" SELECT b.shopcode as shopcode FROM app_members a 
			    LEFT JOIN sys_shops b ON 1 = b.id
				WHERE a.sys_user = ? AND a.status = 1";
		$sql = $this->db->query($sql, $user_id); 

        if($sql->num_rows() > 0){
            return $sql->row()->shopcode;
        }else{
            return "";
        }
    }
    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
    }
	public function get_prev_product_per_shop($name, $sys_shop) {
		$query="SELECT id FROM sys_products WHERE name < ? AND enabled = 1 ORDER BY name DESC LIMIT 1";
		
		$params = array($name);

		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	
	}


	public function get_next_product($name) {
		$query="SELECT id FROM sys_products WHERE name > ? AND enabled = 1 ORDER BY name LIMIT 1";
		
		$params = array($name);
		
		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	}

	public function get_next_product_per_shop($name, $sys_shop) {
		$query="SELECT id FROM sys_products WHERE name > ? AND enabled = 1 ORDER BY name LIMIT 1";
		
		$params = array($name);
		
		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	}

	public function checkProductStatus($Id) {
		$query=" SELECT enabled FROM sys_products WHERE Id = ?";
		
		$params = array($Id);
		return $this->db->query($query, $params)->row();
	}

    public function get_category_options() {
		$query="SELECT * FROM sys_product_category WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

}