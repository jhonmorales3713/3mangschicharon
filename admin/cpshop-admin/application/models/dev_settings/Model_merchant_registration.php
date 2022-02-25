<?php 
class Model_merchant_registration extends CI_Model {

    public function __construct(){
        parent::__construct();  
    }    

 
    public function merchant_registration_table(){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status = $this->input->post('_record_status');
		$c_name 			= $this->input->post('c_name');
		$c_email 			= $this->input->post('c_email');
		$c_conno 			= $this->input->post('c_conno');
		$token_session 		= $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'date_created',
			1 => 'shop_name',
			2 => 'ci_email',
			3 => 'ci_conno'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM app_merchant_registration ";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * FROM app_merchant_registration WHERE status = 1";

		
		// start - for default search
		if ($_record_status != 'all') {
			$sql.=" AND application_status = " . $this->db->escape($_record_status) . "";
		}
	
		if($c_name != ""){
			$sql.=" AND shop_name LIKE '%" . $this->db->escape_like_str($c_name) . "%' ";
		}
		if($c_email != ""){
			$sql.=" AND ci_email LIKE '%" . $this->db->escape_like_str($c_email) . "%' ";
		}
		if($c_conno != ""){
			$sql.=" AND ci_conno LIKE '%" . $this->db->escape_like_str($c_conno) . "%' ";
		}


		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["date_created"];
			$nestedData[] = $row["shop_name"];
			$nestedData[] = $row["ci_email"];
			$nestedData[] = $row["ci_conno"];
			$nestedData[] = get_application_merchant_status($row["application_status"]);

			$buttons = "";
			
			$buttons .= '
			<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('Shops_merchant_registration/view_merchant_registration/'.$token.'/'.$row['id']).'"><i class="fa fa-search" aria-hidden="true"></i> View</a>';
			
			if($this->loginstate->get_access()['merchant_registration']['delete'] == 1){
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


	public function get_merchant_details($id)
	{

		$sql = "SELECT a.*, CONCAT(a.a_unit_no, ' ', a.a_street, ' ', a.a_brgy, ' ', b.citymunDesc, ', ', c.provDesc, ' ', d.regDesc) as full_address, a.a_citymunCode, a.a_provCode, a.a_regCode FROM app_merchant_registration AS a
		LEFT JOIN sys_citymun as b ON a.a_citymunCode = b.citymunCode and b.status = 1
		LEFT JOIN sys_prov as c ON a.a_provCode = c.provCode and c.status = 1
		LEFT JOIN sys_region as d ON a.a_regCode = d.regCode and d.status = 1
		WHERE a.id = ? AND a.status = 1";
		$params = array($id);

		return $this->db->query($sql, $params);

	}

	public function get_currency() {
		$query=" SELECT * FROM app_currency WHERE status > ?";
		$data = array(0);
		return $this->db->query($query, $data);
	}

	function get_all_region(){
        $sql="SELECT * FROM sys_region WHERE status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

	function get_all_province(){
        $sql="SELECT * FROM sys_prov WHERE status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

	public function get_province($regCode){
		$query = "SELECT a.*, b.provDesc FROM sys_citymun AS a
		LEFT JOIN sys_prov AS b ON a.provCode = b.provCode AND b.status = 1
		WHERE a.regDesc = ? AND a.status = 1";
		$params = array($regCode);

		return $this->db->query($query, $params);
	}

	public function get_citymun($provCode){
		$query = "SELECT a.* FROM sys_citymun AS a
				  LEFT JOIN sys_region AS b ON a.regDesc = b.regCode AND b.status = 1
					WHERE a.provCode = ? AND a.status = 1";
		$params = array($provCode);

		return $this->db->query($query, $params);
	}

	function get_all_citymun(){
        $sql="SELECT * FROM sys_citymun WHERE status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

	public function checkShopCodeExist($shopcode) {
		$query=" SELECT * FROM sys_shops WHERE shopcode = ? AND shopurl = ? AND status > ?";
		$data = array(
			$shopcode, 
			$shopcode,
			0
		);
		return $this->db->query($query, $data);
	}

	public function save_shop($shopcode, $inputData, $currentData){
	
		$sql="INSERT INTO sys_shops (shopcode, shopurl, shopname, email, mobile, address, shop_city, shop_region, logo, banner, created, status, latitude, longitude, merch_referral_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
		$currentData['shop_logo']     			= ($currentData['shop_logo'] != '') ? $currentData['shop_logo'] : 'none';
		$currentData['shop_banner']   			= ($currentData['shop_banner'] != '') ? $currentData['shop_banner'] : 'none';
		$currentData['shop_name']    			= ($currentData['shop_name'] != '') ? $currentData['shop_name'] : 'none';
		$currentData['ci_email']      			= ($currentData['ci_email'] != '') ? $currentData['ci_email'] : 'none';
		$currentData['ci_conno']      			= ($currentData['ci_conno'] != '') ? $currentData['ci_conno'] : 'none';
		$currentData['full_address']  			= ($currentData['full_address'] != '') ? $currentData['full_address'] : 'none';
		$currentData['a_citymunCode'] 			= ($currentData['a_citymunCode'] != '') ? $currentData['a_citymunCode'] : 'none';
		$currentData['a_regCode']     			= ($currentData['a_regCode'] != '') ? $currentData['a_regCode'] : 'none';
		$currentData['pa_latitude']   			= ($currentData['pa_latitude'] != '') ? $currentData['pa_latitude'] : 'none';
		$currentData['pa_longitude']  			= ($currentData['pa_longitude'] != '') ? $currentData['pa_longitude'] : 'none';
		// $inputData['invtreshold']     			= ($inputData['invtreshold'] != '') ? $inputData['invtreshold'] : 'none';
		// $inputData['set_allowpickup'] 			= ($inputData['set_allowpickup'] != '') ? $inputData['set_allowpickup'] : 'none';
		$currentData['bi_bank_account_name']    = ($currentData['bi_bank_account_name'] != '') ? $currentData['bi_bank_account_name'] : 'none';
		$currentData['bi_bank_account_number']  = ($currentData['bi_bank_account_number'] != '') ? $currentData['bi_bank_account_number'] : 'none';
		$currentData['bi_bank']                 = ($currentData['bi_bank'] != '') ? $currentData['bi_bank'] : 'none';
		$currentData['bi_bank_account_type']     = ($currentData['bi_bank_account_type'] != '') ? $currentData['bi_bank_account_type'] : 'none';

		
		$bind_data = array(
			$shopcode,
			$shopcode,
			$currentData['shop_name'],
			$currentData['ci_email'],
			$currentData['ci_conno'],
			$currentData['full_address'],
			$currentData['a_citymunCode'],
			$currentData['a_regCode'],
			$currentData['shop_logo'],
			$currentData['shop_banner'],
			date('Y-m-d H:i:s'),
			1,
			$currentData['pa_latitude'],
			$currentData['pa_longitude'],
			// $inputData['invtreshold'],
			// $inputData['allowed_unful'],
			// $inputData['set_allowpickup'],
			$currentData['referral_code'],
		);

		$this->db->query($sql, $bind_data);
		$id_shop = $this->db->insert_id();

		$sql="INSERT INTO sys_shop_rate (syshop, ratetype, rateamount, status, curcode) VALUES (?, ?, ?, ?, ?)";
		$bind_data = array(
			$id_shop,
			'p',
			$inputData['merchant_comrate'],
			1,
			'PHP'
		);

		$this->db->query($sql, $bind_data);

		$sql="INSERT INTO sys_shop_account (accountname, accountno, bankname, description, sys_shop, branch_id, created, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array(
			$currentData['bi_bank_account_name'],
			$currentData['bi_bank_account_number'],
			$currentData['bi_bank'],
			$currentData['bi_bank_account_type'],
			$id_shop,
			0,
			date('Y-m-d H:i:s'),
			1
		);

		$this->db->query($sql, $bind_data);


		$sql = "INSERT into 8_referralcom_rate_shops (`shopid`, `merchant_comrate`, `startup`, `jc`, `mcjr`,  `mc`,  `mcsuper`, `mcmega`, `others`, `date_created`,`date_updated`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1) ";
		$bind_data = array(
			  $id_shop,
			  $inputData['merchant_comrate'],
			  $inputData['f_startup'],
			  $inputData['f_jc'],
			  $inputData['f_mcjr'],
			  $inputData['f_mc'],
			  $inputData['f_mcsuper'],
			  $inputData['f_mcmega'],
			  $inputData['f_others'],
			  date('Y-m-d H:i:s'),
			  date('Y-m-d H:i:s')
		);
		 $this->db->query($sql, $bind_data);

		return $id_shop;
	}

	public function save_members($sys_user, $sys_shop, $currentData){
	
		$sql="INSERT INTO app_members (member_type, sys_user, sys_shop, fname, lname, email, mobile_number, comm_type, created, status, branchid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array(
			1,
			$sys_user,
			$sys_shop,
			$currentData['cn_first_name'],
			$currentData['cn_last_name'],
			$currentData['ci_email'],
			$currentData['ci_conno'],
			0,
			date('Y-m-d H:i:s'),
			3,
			0
		);

		$this->db->query($sql, $bind_data);
	}

	public function approveApplication($id){
		$sql = "UPDATE app_merchant_registration SET date_updated = ?, application_status = 2 WHERE id = ?";

		$params = array(
			date('Y-m-d H:i:s'),
			$id
		);

		$this->db->query($sql, $params);


	}

	public function declineApplication($id, $reason){
		$sql = "UPDATE app_merchant_registration SET date_updated = ?, application_status = 0, reason = ? WHERE id = ?";

		$params = array(
			date('Y-m-d H:i:s'),
			$reason,
			$id
		);

		$this->db->query($sql, $params);

	}

	public function checkEmailExist($email){
		$sql = "SELECT * FROM sys_users WHERE username = ?";
		$params = array($email);

		return $this->db->query($sql, $params);
	}

	public function getSysUsers($email){
		$sql = "SELECT * FROM sys_users WHERE username = ?";
		$params = array($email);

		return $this->db->query($sql, $params);
	}

	public function delete_modal_confirm($delete_id){
		//switch db to core
		$sql = "UPDATE app_merchant_registration SET status = 0 WHERE id = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

     public function checkDataExist($id){

		$query = "SELECT * FROM app_merchant_registration WHERE id = ? AND status = 1 	AND (bi_bank = '' OR bi_bank IS NULL OR bi_bank_account_name = '' OR bi_bank_account_name IS NULL OR bi_bank_account_number = '' OR bi_bank_account_name IS NULL OR bi_bank_account_type = '' OR bi_bank_account_type IS NULL OR shop_logo = '' OR shop_logo IS NULL OR shop_banner = '' OR shop_banner IS NULL)";
		$params = array($id);
		return $this->db->query($query, $params);

	}

	// public function checkLogoExist($id){

	// 	$query = "SELECT * FROM app_merchant_registration WHERE id = ? AND status = 1 	AND (shop_logo = '' OR shop_logo IS NULL)";
	// 	$params = array($id);
	// 	return $this->db->query($query, $params);

	// }

	// public function checkBannerExist($id){

	// 	$query = "SELECT * FROM app_merchant_registration WHERE id = ? AND status = 1 	AND (shop_banner = '' OR shop_banner IS NULL)";
	// 	$params = array($id);
	// 	return $this->db->query($query, $params);

	// }


	public function checkEmailExist_shop($email){
		$query = "SELECT * FROM sys_shops WHERE email = ? AND status = 1";
		$params = array($email);

		return $this->db->query($query, $params);
	}

	public function updateApplication($islogochange,$isbannerchange,$file_name,$file_name_banner,$data){
		$sql = "UPDATE app_merchant_registration SET cn_first_name = ?, cn_last_name = ?, ci_email = ?, ci_conno = ?, sml_facebook = ?, sml_instagram = ?,  sml_fields = ?, ci_registered_company_name = ?, ci_company_description = ?, shop_name = ?, shop_description = ?, referral_code = ?, a_unit_no = ?, a_street = ?, a_brgy = ?, a_citymunCode = ?, a_provCode = ?, a_regCode = ?, a_zipcode = ?, pa_latitude = ?, pa_longitude = ?, bi_bank = ?, bi_bank_account_name = ?, bi_bank_account_number = ?, bi_bank_account_type = ?, date_updated = ?";

		$sml_strings = "";
		foreach($data['up_socmed'] as $value){
			$sml_strings .= $value.", ";	
		}

		$params = array(
			$data['up_first_name'],
			$data['up_last_name'],
			$data['up_email'],
			$data['up_conno'],
			$data['up_facebook'],
			$data['up_instagram'],
			$sml_strings,
			$data['up_registered_company_name'],
			$data['up_company_description'],
			$data['up_shop_name'],
			$data['up_shop_description'],
			$data['up_referral_code'],
			$data['up_unit_no'],
			$data['up_street'],
			$data['up_brgy'],
			$data['up_citymunCode'],
			$data['up_provCode'],
			$data['up_regCode'],
			$data['up_zipcode'],
			$data['loc_latitude'],
			$data['loc_longitude'],
			$data['up_bank'],
			$data['up_bank_account_name'],
			$data['up_bank_account_number'],
			$data['up_bank_account_type'],
			date('Y-m-d H:i:s'),
			$data['up_app_id']
		);


		if($islogochange == 1){
			$sql.=",shop_logo  = ".$this->db->escape($file_name)." ";

			$directory_logo = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/').$data['entry-old_logo'];
			$directory_logo_60 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/').$data['entry-old_logo'];

			// if(file_exists($directory_logo)) {
			// 	unlink($directory_logo);
			// }

		}
		if($isbannerchange == 1){
			$sql.=",shop_banner = ".$this->db->escape($file_name_banner)." ";

			$directory_shop_banner = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner/').$data['entry-old_banner'];
			$directory_shop_banner_1500 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner1500/').$data['entry-old_banner'];

			// if(file_exists($directory_shop_banner)) {
			// 	unlink($directory_shop_banner);
			// }	
		}

		$sql.=" WHERE id = ?";

		$this->db->query($sql, $params);

	}


}
