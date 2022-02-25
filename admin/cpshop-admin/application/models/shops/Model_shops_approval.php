<?php
if (function_exists("set_time_limit") == TRUE and @ini_get("safe_mode") == 0) { //to ignore maximum time limit
	@set_time_limit(0);
}

class Model_shops_approval extends CI_Model
{


	public function shop_changes_approval_table($data_admin)
	{
		// storing  request (ie, get/post) global array to a variable
		$_record_status = $data_admin['_record_status'];
		$_shops 		= $data_admin['_shops'];
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);



		$sql = "SELECT 
				*, a.status as ChangesStatus
                    FROM sys_shops_mcr_approval AS a
					LEFT JOIN sys_shops AS b ON a.shopid = b.id
				";


		if ($_record_status == '0') {
			$sql .= " WHERE a.status = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == '1') {
			$sql .= " WHERE a.status = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == '2') {
			$sql .= " WHERE a.status = " . $this->db->escape($_record_status) . "";
		} else if ($_record_status == '3') {
			$sql .= " WHERE a.status = " . $this->db->escape($_record_status) . "";
		}else{
			$sql .= " WHERE a.status IN (0,1,2,3)  ";
		}

		
		$shopid = $this->session->userdata('sys_shop_id');
        if($shopid == 0){

		}else{
			$sql .= " AND a.shopid = '$shopid '  ";
		}
	

		$query = $this->db->query($sql);
		return $query->result_array();

	}

   


	public function shop_mcr_approve($shop_id)
	{

		$sql = "UPDATE sys_shops_mcr_approval SET date_updated = ?, status = ? WHERE shopid = ?";
		$params = array(
			date('Y-m-d H:i:s'),
			2,
			$shop_id
		);
		$this->db->query($sql, $params);
	
	}

	public function shops_mcr_decline($shop_id)
	{

		$sql = "UPDATE sys_shops_mcr_approval SET date_updated = ?, status = ? WHERE shopid = ?";
		$params = array(
			date('Y-m-d H:i:s'),
			0,
			$shop_id
		);
		$this->db->query($sql, $params);
	
	}



	public function shop_mcr_verify($shop_id)
	{

		$sql = "UPDATE sys_shops_mcr_approval SET date_updated = ?, status = ? WHERE shopid = ?";
		$params = array(
			date('Y-m-d H:i:s'),
			1,
			$shop_id
		);
		$this->db->query($sql, $params);

		$query="SELECT * FROM sys_shops_mcr_approval WHERE shopid = ? AND status = ?";
		$data = array($shop_id, 1);
		$shopid_approval = $this->db->query($query, $data)->result_array();

		$query="SELECT * FROM 8_referralcom_rate_shops WHERE shopid = ? AND status > ?";
		$data = array($shop_id, 0);
		$shopid = $this->db->query($query, $data)->result_array();



		if(count($shopid) != 0){

			$sql = "UPDATE 8_referralcom_rate_shops SET merchant_comrate = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ?, date_updated = ? WHERE shopid = ? AND status = ?";
			$data = array(
				          $shopid_approval[0]['merchant_comrate'],
						  $shopid_approval[0]['startup'],
						  $shopid_approval[0]['jc'],
						  $shopid_approval[0]['mcjr'],
						  $shopid_approval[0]['mc'],
						  $shopid_approval[0]['mcsuper'],
						  $shopid_approval[0]['mcmega'],
						  $shopid_approval[0]['others'],
						  date('Y-m-d H:i:s'),
					      $shop_id,
						  1);
			$this->db->query($sql, $data);

			
		}else{

			$sql = "INSERT into 8_referralcom_rate_shops (`shopid`, `merchant_comrate`, `startup`, `jc`, `mcjr`,  `mc`,  `mcsuper`, `mcmega`, `others`, `date_created`,`date_updated`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1) ";
			$data = array(
				         $shop_id,
						 $shopid_approval[0]['merchant_comrate'],
						 $shopid_approval[0]['startup'],
						 $shopid_approval[0]['jc'],
						 $shopid_approval[0]['mcjr'],
						 $shopid_approval[0]['mc'],
						 $shopid_approval[0]['mcsuper'],
						 $shopid_approval[0]['mcmega'],
						 $shopid_approval[0]['others'],
						 date('Y-m-d H:i:s'),
						 date('Y-m-d H:i:s'));
		    $this->db->query($sql, $data);

	
		}


		$query="SELECT * FROM sys_shop_rate WHERE syshop = ? AND status > ?";
		$data = array($shop_id, 0);
		$shopid_rate = $this->db->query($query, $data)->result_array();


		if(count($shopid_rate) != 0){

			$sql = "UPDATE sys_shop_rate SET ratetype = ?, rateamount = ? WHERE syshop = ? AND status = ?";
			$data = array('p',  $shopid_approval[0]['merchant_comrate'], $shop_id, 1);
			return $this->db->query($sql, $data);
			
		}else{

			$sql = "INSERT into sys_shop_rate (`syshop`, `ratetype`, `rateamount`, `status`) VALUES (?, ?, ?, ?) ";
			$data = array($shop_id, 'p',  $shopid_approval[0]['merchant_comrate'], 1);
			return$this->db->query($sql, $data);
		}

	
	}


	public function sys_shops_mcr_approval($shop_id){

		$query = "SELECT 	* FROM sys_shops_mcr_approval WHERE shopid='$shop_id'";
		return $this->db->query($query)->result_array();

	}




	# End - Products
}
