<?php 
class Model_shops extends CI_Model {
	public function shops_profile_table($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status = $filters['_record_status'];
		$_shopname	    = $filters['_shopname'];
		$_address	    = $filters['_address'];
		$_city	    	= $filters['_city'];
		$shopid 		= $this->session->userdata('sys_shop_id');

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'shopname',
			1 => 'email',
			2 => 'mobile',
			3 => 'address',
			4 => 'shop_city'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_shops AS a
				LEFT JOIN app_members AS b
				ON a.id = b.`sys_shop`
				WHERE a.status IN (1, 2)
				GROUP BY  a.id
				";


		if($shopid > 0){
			$sql .=" AND a.id = " . $this->db->escape($shopid) . "";
		}
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 

		$sql = "SELECT 
		        a.Id  AS shop_id,
		        a.shopname AS shop_name,
				a.`email` AS email_shop,
				a.`mobile` AS mobile_shop,
				a.address AS address_shop,
				a.shop_city,
				a.`status` AS status_shop
		        FROM sys_shops AS a
				LEFT JOIN app_members AS b
				ON a.id = b.`sys_shop`
				WHERE a.status IN (1, 2)";


		if($shopid > 0){
			$sql .=" AND a.id = " . $this->db->escape($shopid) . "";
		}
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" AND a.status = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" AND a.status = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 3){
			$sql.=" AND b.status = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" AND a.status > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if($_shopname != ""){
			$sql.=" AND a.shopname LIKE '%" . $this->db->escape_like_str($_shopname) . "%' ";
		}
		if($_address != ""){
			$sql.=" AND a.address LIKE '%" . $this->db->escape_like_str($_address) . "%' ";
		}
		if($_city != ""){
			$sql.=" AND a.shop_city = '" . $this->db->escape_str($_city) . "' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" GROUP BY  a.id";
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		if (!$exportable) {
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		}

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["shop_name"];
			$nestedData[] = $row["email_shop"];
			$nestedData[] = $row["mobile_shop"];
			$nestedData[] = $row["address_shop"];
			$nestedData[] = $this->get_city_description($row["shop_city"]);
			if ($row['status_shop'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['status_shop'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			$actions = '
            <div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            ';
            if ($this->loginstate->get_access()['shops']['update'] == 1 || $this->loginstate->get_access()['shops']['view'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_edit" data-value="'.en_dec('en', $row['shop_id']).'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                    <div class="dropdown-divider"></div>';
            }

            if ($this->loginstate->get_access()['shops']['disable'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_disable" data-value="'.$row['shop_id'].'" data-record_status="'.$row['status_shop'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
                    <div class="dropdown-divider"></div>';
            }
            
            if ($this->loginstate->get_access()['shops']['delete'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_delete " data-value="'.$row['shop_id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
					<div class="dropdown-divider"></div>';
					
            }

			$shop_id = $row['shop_id'];
			$sql_appmembers = "SELECT * FROM app_members WHERE STATUS = '3' AND sys_shop = '$shop_id'";
			$appmembers  = $this->db->query($sql_appmembers);

			if($appmembers->num_rows() == 1){
				$actions .= '
				   <a class="dropdown-item action_approved " data-value="'.$row['shop_id'].'" data-toggle="modal" data-target="#approved_login"><i class="fa fa-address-card" aria-hidden="true"></i> Approve Login</a>';
            $actions .= '
                </div>
            </div>';
			}

            $nestedData[] = $actions;
			// $nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-success btnView btnTable" name="update" data-value="'.$row['id'].'" areaId="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" areaId="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

	public function delete_modal_confirm($delete_id){
		$sql = "UPDATE `sys_shops` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `sys_shops` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	function check_pendingorder($shopid){
        $sql ="SELECT id, date_ordered FROM app_sales_order_details
        WHERE sys_shop = ? AND order_status <> ? AND date(date_ordered) >= ? AND status > ? LIMIT 1";

        $data = array(
            $shopid,
            's', 
            date('Y-m-d', strtotime("-30 days")),
            0
        );
        return $this->db->query($sql, $data);
	}
	
		function check_pendingorder_unpaid($shopid){
			$sql ="SELECT b.order_id, b.date_ordered FROM app_order_details_shipping AS a
			LEFT JOIN app_order_details AS b ON a.reference_num = b.reference_num
			WHERE a.sys_shop = ? AND b.payment_status = ? AND date(b.date_ordered) >= ? AND a.status > ? LIMIT 1";

			$data = array(
				$shopid,
				0, 
				date('Y-m-d', strtotime("-30 days")),
				0
			);
			return $this->db->query($sql, $data);
		}
		
	

		public function save_shop_det($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_banner, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled, $advertisement, $file_advertisement, $merchant_arrangement, $set_allowpickup, $commrate, $toktokdel) {
			$sql = "INSERT into sys_shops (`shopcode`, `shopurl`, `shopname`, `email`, `mobile`, `updated`, `status`, `logo`, `banner`, `address`, `shop_city`, `shop_region`, `billing_type`, `latitude`, `longitude`, `generatebilling`, `prepayment`, `threshold_amt`, `app_currency_id`, `inv_threshold`, `allowed_unfulfilled`, `toktok_shipping`, `set_advertisement`, `set_shop_advertisement`,`set_featured_arrangement`,`allow_pickup`, `commission_rate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
			$data = array($shopcode, str_replace(" ", "_", $shopurl), $shopname, $email, $mobile, todaytime(), 1, $file_name, $file_banner, $address, $shop_city, $shop_region, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt,  $currency, $treshold, $allowed_unfulfilled, $toktokdel, $advertisement, $file_advertisement,  $merchant_arrangement, $set_allowpickup, $commrate);		
			$this->db->query($sql, $data);
	
			$shopid = $this->db->insert_id();
	
			// $DB_vouchers = $this->load->database('vouchers', TRUE);
			
			// $sql = "INSERT into v_shops (`shopid`, `shopcode`, `shopname`, `shopaddr`, `shopcity`, `shopcountry`, `shopconno`, `shopemail`, `shopimage`, shopothers, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
			// $data = array($shopid, $shopcode, $shopname, $address, $shop_city, 'Philippines', $mobile, $email, $file_banner, '', 1);		
			// $DB_vouchers->query($sql, $data);
	
			return $shopid;

		}
		
		public function save_shop_rate_det($ratetype, $rate, $shopid) {
			$sql = "INSERT into sys_shop_rate (`syshop`, `ratetype`, `rateamount`, `status`) VALUES (?, ?, ?, ?) ";
			$data = array($shopid, $ratetype, $rate, 1);
			$this->db->query($sql, $data);
		}

		public function save_shop_rate_det_toktokmall($ratetype = "p", $merchant_comrate, $shopid) {
			$sql = "INSERT into sys_shop_rate (`syshop`, `ratetype`, `rateamount`, `status`) VALUES (?, ?, ?, ?) ";
			$data = array($shopid, $ratetype, $merchant_comrate, 1);
			$this->db->query($sql, $data);
		}

        /////////////////////////////////////for Toktokmall

		public function save_shop_det_toktokmall($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_banner, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled, $advertisement, $file_advertisement, $merchant_arrangement, $whatsnew, $whatsnew_arrangement, $file_whatsnew, $set_allowpickup, $toktokdel) {
			$sql = "INSERT into sys_shops (`shopcode`, `shopurl`, `shopname`, `email`, `mobile`, `updated`, `status`, `logo`, `banner`, `address`, `shop_city`, `shop_region`, `billing_type`, `latitude`, `longitude`, `generatebilling`, `prepayment`, `threshold_amt`, `app_currency_id`, `inv_threshold`, `allowed_unfulfilled`, `toktok_shipping`, `set_advertisement`, `set_shop_advertisement`,`set_featured_arrangement`, `set_whatsnew_merchant`, `set_whatsnew_merchant_arrangement`, `set_whatsnew_merchant_photo`, `allow_pickup`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
			$data = array($shopcode, str_replace(" ", "_", $shopurl), $shopname, $email, $mobile, todaytime(), 1, $file_name, $file_banner, $address, $shop_city, $shop_region, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt,  $currency, $treshold, $allowed_unfulfilled, $toktokdel, $advertisement, $file_advertisement,  $merchant_arrangement, $whatsnew, $whatsnew_arrangement, $file_whatsnew, $set_allowpickup);		
			$this->db->query($sql, $data);
	
			$shopid = $this->db->insert_id();
	
			// $DB_vouchers = $this->load->database('vouchers', TRUE);
			
			// $sql = "INSERT into v_shops (`shopid`, `shopcode`, `shopname`, `shopaddr`, `shopcity`, `shopcountry`, `shopconno`, `shopemail`, `shopimage`, shopothers, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
			// $data = array($shopid, $shopcode, $shopname, $address, $shop_city, 'Philippines', $mobile, $email, $file_banner, '', 1);		
			// $DB_vouchers->query($sql, $data);
	
			return $shopid;

		}


		public function select_referralcomrate_toktokmall($id,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others)
		{
            
			$query="SELECT * FROM 8_referralcom_rate_shops WHERE shopid = ? AND status > ?";
			$data = array($id, 0);
			$shopid = $this->db->query($query, $data)->result_array();


			if(count($shopid) != 0){

				$sql = "UPDATE 8_referralcom_rate_shops SET merchant_comrate = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE shopid = ? AND status = ?";
				$data = array($merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,$id,1);
				return	$this->db->query($sql, $data);

				
			}else{
	
				$sql = "INSERT into 8_referralcom_rate_shops (`shopid`, `merchant_comrate`, `startup`, `jc`, `mcjr`,  `mc`,  `mcsuper`, `mcmega`, `others`, `date_created`,`date_updated`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1) ";
				$data = array($id,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));
				return  $this->db->query($sql, $data);

			}

				
		}


		public function select_referralcomrate_toktokmall_approval($id,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others)
		{
            

			$query="SELECT * FROM sys_shops_mcr_approval WHERE shopid = ? AND status > ?";
			$data = array($id, 0);
			$shopid_approval = $this->db->query($query, $data)->result_array();

			if(count($shopid_approval) != 0){

				$sql = "UPDATE sys_shops_mcr_approval SET merchant_comrate = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ?, status = ? WHERE shopid = ?";
				$data = array($merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,3,$id);
				return	$this->db->query($sql, $data);

				
			}else{
	
				$sql = "INSERT into sys_shops_mcr_approval (`shopid`, `merchant_comrate`, `startup`, `jc`, `mcjr`,  `mc`,  `mcsuper`, `mcmega`, `others`, `date_created`,`date_updated`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 3) ";
				$data = array($id,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));
				return  $this->db->query($sql, $data);

			}

				
		}

		public function select_sysrate_toktokmall($ratetype = "p", $merchant_comrate, $id)
		{
            
			$query="SELECT * FROM sys_shop_rate WHERE syshop = ? AND status > ?";
			$data = array($id, 0);
			$shopid = $this->db->query($query, $data)->result();

	
			if(count($shopid) != 0){


				$sql = "UPDATE sys_shop_rate SET ratetype = ?, rateamount = ? WHERE syshop = ? AND status = ?";
				$data = array($ratetype, $merchant_comrate, $id, 1);
				return $this->db->query($sql, $data);
				
			}else{
	

				$sql = "INSERT into sys_shop_rate (`syshop`, `ratetype`, `rateamount`, `status`) VALUES (?, ?, ?, ?) ";
				$data = array($id, $ratetype, $merchant_comrate, 1);
				return$this->db->query($sql, $data);
			}

		
		}

		
	
		public function save_shop_refferal_com_rate($shopid,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others) {
			$sql = "INSERT into 8_referralcom_rate_shops (`shopid`, `merchant_comrate`, `startup`, `jc`, `mcjr`,  `mc`,  `mcsuper`, `mcmega`, `others`, `date_created`,`date_updated`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1) ";
			$data = array($shopid,$merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));
			$this->db->query($sql, $data);
		}

		public function save_shop_bank_det($bankname, $acctname, $acctno, $desc, $shopid, $branchid) {
			$sql = "INSERT into sys_shop_account (`accountname`, `accountno`, `bankname`, `description`,  `sys_shop`,  `branch_id`) VALUES (?, ?, ?, ?, ?, ?) ";
			$data = array($acctname, $acctno, $bankname, $desc, $shopid, $branchid);
			$this->db->query($sql, $data);
		}

		public function get_shop_details($id) {
			$query=" SELECT shop.*, shop.id as idno, rate.ratetype, rate.rateamount, bank.*  
					FROM sys_shops as shop
					LEFT JOIN sys_shop_rate as rate ON shop.id = rate.syshop 
					LEFT JOIN sys_shop_account as bank ON shop.id = bank.sys_shop  
					WHERE shop.id = ? AND shop.status > ?";
			$data = array($id, 0);
			return $this->db->query($query, $data);
		}

		public function get_shop_details_toktokmall($id) {
			$query=" SELECT shop.*, shop.id as idno,rate.*, bank.*,shoprate.*  
					FROM sys_shops as shop
					LEFT JOIN 8_referralcom_rate_shops AS rate  ON shop.id = rate.shopid 
					LEFT JOIN sys_shop_account as bank ON shop.id = bank.sys_shop  
					LEFT JOIN sys_shop_rate as shoprate ON shop.id = shoprate.syshop  
					WHERE shop.id = ? AND shop.status > ?";
			$data = array($id, 0);
			return $this->db->query($query, $data);
		}


	public function update_shop_det($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_name_banner, $shopid, $islogochange, $isbannerchange, $isadvertisementchange, $old_logo, $old_banner, $old_advertisement, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled,  $advertisement, $file_advertisement, $merchant_arrangement, $set_allowpickup, $commrate, $toktokdel) {

		
		$sql = " UPDATE sys_shops SET shopcode = ?, shopurl = ?, shopname = ?, email = ?, mobile = ?, updated = ?, address = ?, shop_city = ?, shop_region = ?, billing_type = ?, latitude = ?, longitude = ?, generatebilling = ?, prepayment = ?, threshold_amt = ?,  app_currency_id = ?, inv_threshold = ?, allowed_unfulfilled = ?, toktok_shipping = ?, set_advertisement = ?, set_featured_arrangement = ?, allow_pickup = ?, commission_rate = ?";

		
		if($islogochange == 1){
			$sql.=", logo  = ".$this->db->escape($file_name)." ";

			$directory_logo = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$old_logo;
			$directory_logo_60 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/').$old_logo;

			if(file_exists($directory_logo)) {
				unlink($directory_logo);
			}

			if(file_exists($directory_logo_60)) {
				unlink($directory_logo_60);
			}
		}
		if($isbannerchange == 1){
			$sql.=", banner = ".$this->db->escape($file_name_banner)." ";

			$directory_shop_banner = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$old_banner;
			$directory_shop_banner_1500 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner1500/').$old_banner;

			if(file_exists($directory_shop_banner)) {
				unlink($directory_shop_banner);
			}	
		}

		if($isadvertisementchange == 1){
			
			$sql.=",set_shop_advertisement = ".$this->db->escape($file_advertisement)." ";
		
		}
		
		$sql.=" WHERE id = ? AND status = ?";

		//print_r($sql);
		//die();
		$data = array($shopcode, str_replace(" ", "_", $shopurl), $shopname, $email, $mobile, todaytime(), $address, $shop_city, $shop_region, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt,$currency, $treshold, $allowed_unfulfilled, $toktokdel, $advertisement, $merchant_arrangement, $set_allowpickup, $commrate, $shopid, 1);
		$this->db->query($sql, $data);

		// $DB_vouchers = $this->load->database('vouchers', TRUE);
		
		// $sql = "UPDATE v_shops SET shopid = ?, shopcode = ?, shopname = ?, shopaddr = ?, shopcity = ?, shopcountry = ?, shopconno = ?, shopemail = ?, shopimage = ?, shopothers = ? WHERE shopid = ? AND status = ?";
		// $data = array($shopid, $shopcode, $shopname, $address, $shop_city, 'Philippines', $mobile, $email, $file_name_banner, '', $shopid, 1);		
		// $DB_vouchers->query($sql, $data);
	}

	public function update_shop_rate_det($ratetype, $rate, $shopid) {
		$sql = "UPDATE sys_shop_rate SET ratetype = ?, rateamount = ? WHERE syshop = ? AND status = ?";
		$data = array($ratetype, $rate, $shopid, 1);
		$this->db->query($sql, $data);
	}

////////////////////for toktokmall
	public function update_shop_det_toktokmall($shopcode, $shopurl, $shopname, $email, $mobile, $file_name, $file_name_banner, $shopid, $islogochange, $isbannerchange, $isadvertisementchange, $isWhatsnewchange, $old_logo, $old_banner, $old_advertisement, $old_whatsnew, $address, $shop_region, $shop_city, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt, $currency, $treshold, $allowed_unfulfilled,  $advertisement, $file_advertisement, $merchant_arrangement, $whatsnew, $whatsnew_arrangement, $file_whatsnew, $set_allowpickup, $toktokdel) {

		
		$sql = " UPDATE sys_shops SET shopcode = ?, shopurl = ?, shopname = ?, email = ?, mobile = ?, updated = ?, address = ?, shop_city = ?, shop_region = ?, billing_type = ?, latitude = ?, longitude = ?, generatebilling = ?, prepayment = ?, threshold_amt = ?,  app_currency_id = ?, inv_threshold = ?, allowed_unfulfilled = ?, toktok_shipping = ?, set_advertisement = ?, set_featured_arrangement = ?, set_whatsnew_merchant = ?, set_whatsnew_merchant_arrangement = ?,  allow_pickup = ? ";

		
		if($islogochange == 1){
			$sql.=", logo  = ".$this->db->escape($file_name)." ";

			$directory_logo = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$old_logo;
			$directory_logo_60 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/').$old_logo;

			if(file_exists($directory_logo)) {
				unlink($directory_logo);
			}

			if(file_exists($directory_logo_60)) {
				unlink($directory_logo_60);
			}
		}
		if($isbannerchange == 1){
			$sql.=", banner = ".$this->db->escape($file_name_banner)." ";

			$directory_shop_banner = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$old_banner;
			$directory_shop_banner_1500 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner1500/').$old_banner;

			if(file_exists($directory_shop_banner)) {
				unlink($directory_shop_banner);
			}	
		}

		if($isadvertisementchange == 1){
			
			$sql.=",set_shop_advertisement = ".$this->db->escape($file_advertisement)." ";
		
		}

		
		if($isWhatsnewchange == 1){
			
			$sql.=",set_whatsnew_merchant_photo = ".$this->db->escape($file_whatsnew)." ";
		
		}
		
		$sql.=" WHERE id = ? AND status = ?";

		//print_r($sql);
		//die();
		$data = array($shopcode, str_replace(" ", "_", $shopurl), $shopname, $email, $mobile, todaytime(), $address, $shop_city, $shop_region, $withshipping, $loc_latitude, $loc_longitude, $generatebilling, $prepayment, $thresholdamt,$currency, $treshold, $allowed_unfulfilled, $toktokdel, $advertisement, $merchant_arrangement, $whatsnew, $whatsnew_arrangement,  $set_allowpickup, $shopid, 1);
		$this->db->query($sql, $data);

		// $DB_vouchers = $this->load->database('vouchers', TRUE);
		
		// $sql = "UPDATE v_shops SET shopid = ?, shopcode = ?, shopname = ?, shopaddr = ?, shopcity = ?, shopcountry = ?, shopconno = ?, shopemail = ?, shopimage = ?, shopothers = ? WHERE shopid = ? AND status = ?";
		// $data = array($shopid, $shopcode, $shopname, $address, $shop_city, 'Philippines', $mobile, $email, $file_name_banner, '', $shopid, 1);		
		// $DB_vouchers->query($sql, $data);
	}

	// public function update_shop_refferal_com_rate($merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,$shopid) {
	// 	$sql = "UPDATE 8_referralcom_rate_shops SET merchant_comrate = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE shopid = ? AND status = ?";
	// 	$data = array($merchant_comrate,$f_startup,$f_jc,$f_mcjr,$f_mc,$f_mcsuper,$f_mcmega,$f_others,$shopid,1);
	// 	$this->db->query($sql, $data);
	// }



	public function update_shop_bank_det($bankname, $acctname, $acctno, $desc, $shopid, $branchid) {
		$sql = "UPDATE sys_shop_account SET accountname = ?, accountno = ?, bankname = ?, description = ? WHERE sys_shop = ? AND branch_id = ?";
		$data = array($acctname, $acctno, $bankname, $desc, $shopid, $branchid);
		$this->db->query($sql, $data);
	}

	public function is_shopcodeexist($shopcode){
		$sql ="SELECT COUNT(shopcode) as count FROM sys_shops WHERE status > ? AND shopcode = ?";
		$data = array(0, $shopcode);
		return $this->db->query($sql, $data)->row()->count;
	}

	public function is_shopurlexist($shopurl){
		$sql ="SELECT COUNT(shopurl) as count FROM sys_shops WHERE status > ? AND shopurl = ?";
		$data = array(0, $shopurl);
		return $this->db->query($sql, $data)->row()->count;
	}

	public function is_shopcodeexist_edit($shopcode, $id){
		$sql ="SELECT COUNT(shopcode) as count FROM sys_shops WHERE status > ? AND shopcode = ? AND id <> ?";
		$data = array(0, $shopcode, $id);
		return $this->db->query($sql, $data)->row()->count;
	}

	public function is_shopurlexist_edit($shopurl, $id){
		$sql ="SELECT COUNT(shopurl) as count FROM sys_shops WHERE status > ? AND shopurl = ? AND id <> ?";
		$data = array(0, $shopurl, $id);
		return $this->db->query($sql, $data)->row()->count;
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

	public function get_city_description($citycode){
        $sql ="SELECT citymunDesc as description FROM sys_citymun WHERE status > ? AND citymunCode = ?";
        $data = array(0, $citycode);
        $result = $this->db->query($sql, $data);
        // print_r($this->db->query($sql, $data));die();
        if ($result->num_rows() > 0) {
            return $result->row()->description;
        }else{
            return "N/A";
        }
	}
	
	public function get_shop_opts_oderbyname($id = false)
	{
		$query="SELECT * FROM sys_shops WHERE status = 1";
		if($id){
			$id = $this->db->escape($id);
			$query .= " AND id = $id";
		}
		$query .= " ORDER BY shopname";
		return $this->db->query($query)->result_array();
	}

	public function get_shopsListByIds ($ids) {
		$sql = "SELECT id, shopname FROM sys_shops WHERE id IN ('$ids')";
		return $this->db->query($sql)->result_array();
	}

	public function get_currency() {
		$query=" SELECT * FROM app_currency WHERE status > ?";
		$data = array(0);
		return $this->db->query($query, $data);
	}


	public function getFeaturedMerchant(){
		$query="SELECT * FROM sys_shops WHERE status = '1' AND set_advertisement = '1' ORDER BY set_featured_arrangement ASC ";
		return $this->db->query($query)->result_array();
	}
	
	public function getFeaturedMerchantCount(){
		$query="SELECT * FROM sys_shops WHERE status = '1' AND set_advertisement = '1' ";
		return $this->db->query($query)->num_rows();
	}

	public function checkFeaturedMerchantArrangement($merchant_number){
		$query="SELECT * FROM sys_shops WHERE status = '1' AND set_advertisement = '1' AND set_featured_arrangement = '$merchant_number'  AND set_featured_arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}

	
	public function  checkedFeaturedMerchant($shop_id){
		$query="SELECT * FROM sys_shops WHERE  set_advertisement = '1' AND status = '1' AND  `id` = '$shop_id' ";
		$result = $this->db->query($query)->num_rows();

		if($result > 0){
            return 1;
          }else{
            return 0;
          }
	}

	public function  checkedWhatsNewMerchant($shop_id){
		$query="SELECT * FROM sys_shops WHERE  set_whatsnew_merchant = '1' AND status = '1' AND  `id` = '$shop_id' ";
		$result = $this->db->query($query)->num_rows();

		if($result > 0){
            return 1;
          }else{
            return 0;
          }
	}

	public function getWhatsNewMerchantCount(){
		$query="SELECT * FROM sys_shops WHERE status = '1' AND set_whatsnew_merchant = '1' ";
		return $this->db->query($query)->num_rows();
	}

	public function getWhatsNewMerchant(){
		$query="SELECT * FROM sys_shops WHERE status = '1' AND set_whatsnew_merchant = '1' ORDER BY set_whatsnew_merchant_arrangement ASC ";
		return $this->db->query($query)->result_array();
	}


	public function checkWhatsNewMerchantArrangement($merchant_number){
		$query="SELECT * FROM sys_shops WHERE status = '1' AND set_whatsnew_merchant = '1' AND set_whatsnew_merchant_arrangement = '$merchant_number'  AND set_whatsnew_merchant_arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}


	public function approved_shops($shop_id){
		$sql = "UPDATE `app_members` SET `status` = ?,  updated = ? WHERE `sys_shop` = ?";
		$data = array(
					 1,
					date('Y-m-d H:i:s'),
					$shop_id,
					);		
		$this->db->query($sql, $data);
    }


	public function get_shoprate($id){

		$query="SELECT * FROM 8_referralcom_rate_shops WHERE shopid = ? AND status > ?";
		$data = array($id, 0);
		return  $this->db->query($query, $data)->result_array();

	}
	

}
?>