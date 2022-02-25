<?php 
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) { //to ignore maximum time limit
    @set_time_limit(0);
}

class Model_dev_settings extends CI_Model {

	# Start - Content Navigation

	public function main_nav_categories() {
		$sql = "SELECT `main_nav_id`, `main_nav_desc` FROM `cp_main_navigation` WHERE `enabled` >= 1";

		return $this->db->query($sql);
	}

	public function delete_modal_confirm($delete_id){
		$sql = "UPDATE `cp_content_navigation` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `cp_content_navigation` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function insert_content_navigation($row){
		$this->db->insert("cp_content_navigation", $row);
		return $this->db->insert_id();
	}

	public function get_content_navigation($id){
		$sql = "SELECT *  FROM cp_content_navigation WHERE id = ?
		AND status = ? ORDER BY arrangement ASC";
		$data = array($id, 1);
		return $this->db->query($sql, $data);
	}

	public function get_content_navigation_unique_url($url){ //Simply returns the number of rows
		$this->db->where(array("cn_url" => $url, "status" => 1));
		$this->db->from("cp_content_navigation");
		return $this->db->count_all_results();
	}
	
	public function get_content_navigation_unique_name($name){ //Simply returns the number of rows
		$this->db->where(array("cn_name" => $name, "status" => 1));
		$this->db->from("cp_content_navigation");
		return $this->db->count_all_results();
	}

	public function update_content_navigation($id, $row){
		$this->db->where("id", $id);
		$this->db->update("cp_content_navigation", $row);
	}

	public function delete_content_navigation($del_id){
		$this->db->where("id", $del_id);
		$this->db->update("cp_content_navigation", array("status" => 0));
	}

	public function content_navigation_table(){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status = $this->input->post('_record_status');
		$_url 			= $this->input->post('_url');
		$_name 			= $this->input->post('_name');
		$_description 	= $this->input->post('_description');
		$_main_nav 		= $this->input->post('_main_nav');

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'cn_url',
			1 => 'cn_name',
			2 => 'cn_description',
			3 => 'cn_fkey_name',
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM cp_content_navigation 
				JOIN (SELECT main_nav_id, main_nav_desc AS cn_fkey_name 
	 			FROM cp_main_navigation) AS main_nav 
				ON main_nav.main_nav_id = cp_content_navigation.cn_fkey
				WHERE status > 0";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT id, cn_url, cn_name, cn_description, cn_fkey_name, main_nav_id, status FROM cp_content_navigation 
				JOIN (SELECT main_nav_id, main_nav_desc AS cn_fkey_name 
	 			FROM cp_main_navigation) AS main_nav 
				ON main_nav.main_nav_id = cp_content_navigation.cn_fkey";

		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE status = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE status = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE status > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if($_url != ""){
			$sql.=" AND cn_url LIKE '%" . $this->db->escape_like_str($_url) . "%' ";
		}
		if($_name != ""){
			$sql.=" AND cn_name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_description != ""){
			$sql.=" AND cn_description LIKE '%" . $this->db->escape_like_str($_description) . "%' ";
		}
		if($_main_nav != ""){
			$sql.=" AND main_nav_id = " . $this->db->escape($_main_nav) . "";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["cn_url"];
			$nestedData[] = $row["cn_name"];
			$nestedData[] = $row["cn_description"];
			$nestedData[] = $row["cn_fkey_name"];

			if ($row['status'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['status'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    	<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>
			    	<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
			  	</div>
			</div>';
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

	# End - Content Navigation

	# Start - Company Manager

	public function company_manager_table($company_code, $company_name) {
		# storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		$columns = array( 
			# datatable column index  => database column name for sorting
			0 => 'company_code',
			1 => 'company_name',
			2 => 'plan_id',
			3 => 'date_created',
			4 => 'end_of_trial',
		);
		
		$sql = "SELECT `company_code`, `company_name`, `plan_id`, `date_created`, DATE_ADD(`date_created`, INTERVAL 1 MONTH) AS end_of_trial, `status` 
				FROM pb_companies 
				WHERE status >= ?";
		
		# getting records as per search parameters
		if ($company_code != "") {  //id
			$sql .=" AND company_code LIKE '%" . $this->db->escape_like_str($company_code) . "%' ";
		}
		if ($company_name != "") {  # main nav category
			$sql .=" AND company_name LIKE '%" . $this->db->escape_like_str($company_name) . "%' ";
		}

		$data = array(0);
		$query = $this->db->query($sql, $data);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  # when there is no search parameter then total number rows = total number filtered rows.
		
		$sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		
		$data = array(0);
		$query = $this->db->query($sql, $data);

		$data = array();
		foreach( $query->result_array() as $row ) {  # preparing an array for table tbody
			$nestedData=array();
			
			$nestedData[] = $row["company_code"];
			$nestedData[] = $row["company_name"];

			if ($row["plan_id"] == "1") {
				$row["plan_id"] = "Silver";
			}
			elseif ($row["plan_id"] == "2") {
				$row["plan_id"] = "Gold";
			}
			elseif ($row["plan_id"] == "3") {
				$row["plan_id"] = "Platinum";
			}

			$nestedData[] = $row["plan_id"];
			$nestedData[] = format_date_dash_reverse($row["date_created"]);
			$nestedData[] = format_date_dash_reverse($row["end_of_trial"]);

			if($row['status'] == "1")
			{
				$buttons = '
				<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView" data-company_code="'.$row['company_code'].'"><i class="fa fa-edit"></i> Edit</button> 
				<button class="btn btn-primary btnUsers btn-inline" data-company_code="'.$row['company_code'].'"><i class="fa fa-user-o"></i> View Users</button> 
				<button class="btn btn-danger btnDeact btn-inline" data-company_status="1" data-company_code="'.$row['company_code'].'"><i class="fa fa-trash-o"></i> Deactivate</button>';
			}
			elseif($row['status'] == "0")
			{
				$buttons = '
				<button class="btn btn-warning btnReactivate btn-inline" data-company_status="0" data-company_code="'.$row['company_code'].'"><i class="fa fa-trash-o"></i> Reactivate</button>';
			}

			$nestedData[] = $buttons;
			
			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   # for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  # total number of records
			"recordsFiltered" => intval( $totalFiltered ), # total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   # total data array
		);

		return $json_data;
	}

	public function get_company_details($company_code)
	{
		$sql = "SELECT * FROM pb_companies WHERE company_code = ?";
		// $sql = "SELECT * FROM pb_companies WHERE company_code = ? AND status = ? ";
		// $data = array($company_code, 1);
		$data = array($company_code);
		return $this->db->query($sql, $data);
	}

	public function save_company($add_code, $add_name, $add_initial, $add_address, $add_website, $add_phone, $add_email, $add_database, $add_plan, $add_logo, $add_logo_small) {
		$sql = "INSERT INTO `pb_companies`(`company_code`, `company_name`, `company_initial`, `company_logo`, `company_logo_small`, `company_address`, `company_website`, `company_phone`, `company_email`, `company_database`, `plan_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$data = array($add_code, $add_name, $add_initial, $add_logo, $add_logo_small, $add_address, $add_website, $add_phone, $add_email, $add_database, $add_plan);

		return $this->db->query($sql, $data);
	}

	public function update_company($update_id, $update_code, $update_name, $update_initial, $update_address, $update_website, $update_phone, $update_email, $update_database, $update_plan, $update_logo, $update_logo_small) {
		$sql = "UPDATE pb_companies SET company_code = ?, company_name = ?, company_initial = ?, company_logo = ?, company_logo_small = ?, company_address = ?, company_website = ?, company_phone = ?, company_email = ?, company_database = ?, plan_id = ?, date_updated = ? WHERE company_id = ? ";
		$data = array($update_code, $update_name, $update_initial, $update_logo, $update_logo_small, $update_address, $update_website, $update_phone, $update_email, $update_database, $update_plan, date("Y-m-d H:i:s"), $update_id);

		return $this->db->query($sql, $data);
	}

	public function update_related_users($original_company_code, $update_code) {
		$sql = "UPDATE jcw_users SET company_code = ? WHERE company_code = ? ";
		$data = array($update_code, $original_company_code);

		return $this->db->query($sql, $data);
	}

	public function company_users_table($name, $position, $company_code){
		# storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		# datatable column index  => database column name for sorting
		$columns = array(
			0 => 'CONCAT(user_fname, user_lname)',
			1 => 'position_id'
		);
		
		$sql = "SELECT user_id, CONCAT(user_fname, ' ', user_lname) as name, ju.position_id, jp.position, ju.enabled 
				FROM jcw_users ju 
				LEFT JOIN jcw_position jp
				ON ju.position_id = jp.position_id
				WHERE ju.enabled IN (0,1,2) AND ju.company_code = '" . $this->db->escape_like_str($company_code) . "' ";
		
		# getting records as per search parameters
		if( $name != ""){  //id
			$sql .=" AND CONCAT(user_fname, ' ', user_lname) LIKE '%" . $this->db->escape_like_str($name) . "%' ";
		}
		if( $position != ""){  //main nav category
			$sql .=" AND position_id LIKE '%" . $this->db->escape_like_str($position) . "%' ";
		}

		// print_r($sql);
		
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  # when there is no search parameter then total number rows = total number filtered rows.
		
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		$query = $this->db->query($sql);

		$data = array();

		# preparing an array for table tbody
		foreach( $query->result_array() as $row ) {  
			$nestedData = array();

			# First column
			$nestedData[] = $row["name"]; # array push

			// # Second column
			// if ($row["position_id"] == "2" || $row["position_id"] == "4") {
			// 	$row["position_id"] = "Admin";
			// }
			// elseif ($row["position_id"] == "3" || $row["position_id"] == "5") {
			// 	$row["position_id"] = "Staff";
			// }
			// else {
			// 	$row["position_id"] = "Super User";
			// }

			$nestedData[] = $row["position"]; # array push

			# Third column
			$buttons = "";

			if ($row["enabled"] == "1")
			{
				$row["enabled"] = "Registered";

				$buttons = 
				"<button class='btn btn-primary btnEdit' data-user_id='".$row['user_id']."'>
					<i class='fa fa-edit'></i> Edit Details
				</button> 
			
				<button class='btn btn-danger btnDeactivate btn-inline' data-user_status='registered' data-user_id='".$row['user_id']."'>
					<i class='fa fa-trash-o'></i> Deactivate
				</button>";
			}
			elseif ($row["enabled"] == "2")
			{
				$row["enabled"] = "Verified";

				$buttons = 
				"<button class='btn btn-primary btnEdit' data-user_id='".$row['user_id']."'><i class='fa fa-edit'>
					</i> Edit Details
				</button> 

				<button class='btn btn-danger btnDeactivate btn-inline' data-user_status='verified' data-user_id='".$row['user_id']."'>
					<i class='fa fa-trash-o'></i> Deactivate
				</button>";
			}
			elseif ($row["enabled"] == "0")
			{
				$row["enabled"] = 'Deactivated';

				$buttons = 
				"<button class='btn btn-warning btnReactivate btn-inline' data-user_status='deactivated' data-user_id='".$row['user_id']."'>
					<i class='fa fa-repeat'></i> Reactivate
				</button>";
			}
			
			$nestedData[] = $row["enabled"]; # array push

			# Fourth Column
			$nestedData[] = $buttons; # array push

			 
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

	public function has_admin($company_code) {
		$sql = "SELECT user_id FROM jcw_users WHERE company_code = ? AND (position_id = 2 OR position_id = 4) AND enabled = 1 ";
		return $this->db->query($sql, $company_code);
	}

	public function user_limitation($company_code) {
		$plan_id = $this->get_company_details($company_code)->row()->plan_id;

		switch ($plan_id) {
			case '1':
				$position_code = '3'; // gold plan
				break;
			case '2':
				$position_code = '5'; // silver plan
				break;
		}

		$sql = "SELECT user_id FROM jcw_users WHERE company_code = ? AND position_id = ? AND enabled = 1 ";
		$return = $this->db->query($sql, array($company_code, $position_code))->num_rows();

		switch ($plan_id) {
			case '1':
				if ($return < 3) {
					return true;
				}
				else {
					return false;
				}
				break;
			case '2':
				if ($return < 1) {
					return true;
				}
				else {
					return false;
				}
				break;
		}
	}

	public function get_user_details($user_id){
		$sql = "SELECT user_id, company_code, username, password, user_fname, user_mname, user_lname, position_id FROM jcw_users WHERE user_id = ?";
		$data = array($user_id);
		return $this->db->query($sql, $data);
	}

	public function save_user($values) {
		$sql = "INSERT INTO `jcw_users`(`company_code`, `username`, `password`, `user_fname`, `user_mname`, `user_lname`, `position_id`, `date_activated`, `date_created`, `date_updated`, `user_id`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
		$this->db->query($sql, $values);

		$last_id = $this->db->insert_id(); # Returns last inserted id

		return $last_id;
	}

	public function update_user($user_fname, $user_mname, $user_lname, $user_username, $new_user_pass, $user_position, $user_idno){
		$sql = "UPDATE jcw_users SET user_fname = ?, user_mname = ?, user_lname = ?, username = ?, password = ?, position_id = ? WHERE user_id = ?";
		$data = array($user_fname, $user_mname, $user_lname, $user_username, $new_user_pass, $user_position, $user_idno);

		return $this->db->query($sql, $data);
	}

	public function select_deactivate_user($id)
	{
		$sql = "SELECT CONCAT(user_fname, ' ', user_lname) as name FROM jcw_users WHERE user_id = ?";
		$data = array($id);

		return $this->db->query($sql, $data);
	}

	public function set_deactivate_status($id)
	{
		$sql = "UPDATE jcw_users SET enabled = 0 WHERE user_id = ?";
		$data = array($id);

		return $this->db->query($sql, $data);
	}

	public function set_registered_status($defaultPass, $id)
	{
		$sql = "UPDATE jcw_users SET enabled = 1, password = ? WHERE user_id = ?";
		$data = array($defaultPass, $id);

		return $this->db->query($sql, $data);
	}

	public function set_deactivate_status_company($id)
	{
		$sql = "UPDATE pb_companies SET status = 0 WHERE company_code = ?";
		$data = array($id);
		
		return $this->db->query($sql, $data);
	}

	public function set_deactivate_status_company_users($id)
	{
		$sql = "UPDATE jcw_users SET enabled = 0 WHERE company_code = ?";
		$data = array($id);

		return $this->db->query($sql, $data);
	}

	public function set_activate_status_company($id)
	{
		$sql = "UPDATE pb_companies SET status = 1 WHERE company_code = ?";
		$data = array($id);
		
		return $this->db->query($sql, $data);
	}

	public function set_activate_status_company_sadmin($id)
	{
		$sql = "UPDATE jcw_users SET enabled = ? WHERE company_code = ? AND position_id = ?";
		$data = array(1, $id, 1);

		return $this->db->query($sql, $data);
	}

	public function verify_user($id)
	{
		$sql = "UPDATE jcw_users SET enabled = 2 WHERE md5(`user_id`) = ?";
		$data = array($id);

		return $this->db->query($sql, $data);
	}

	# End - Company Manager

	# Start - User Role

	public function get_main_nav(){ 
		$sql = "SELECT * FROM cp_main_navigation WHERE enabled = 1";
		return $this->db->query($sql);
	}

	public function user_role_table($position){
		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		$columns = array( 
			// datatable column index  => database column name for sorting
			0 => 'position'
		);

		$position_id = $this->session->userdata('position_id');

		if ($position_id == 1) {
			$sql = "SELECT position_id, position, access_nav, access_content_nav  FROM `jcw_position` WHERE enabled = 1 ";
		}else{
			$sql = "SELECT position_id, position, access_nav, access_content_nav  FROM `jcw_position` WHERE enabled = 1 AND position_id != 1 ";
		}
		

		// getting records as per search parameters
		if( !empty($position) ){  //position
			$sql.=" AND position LIKE '%".str_replace('', '', sanitize($position))."%' ";
		}

		//print_r($sql);
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			$nestedData[] = $row["position"];
			$nestedData[] = '<button 
								data-toggle="modal" 
								data-backdrop="static" 
								data-keyboard="false" 
								data-target="#edit_userrole_modal" 
								data-position_id="'.$row["position_id"].'"
								data-position="'.$row["position"].'"
								data-access_nav="'.$row["access_nav"].'"
								data-access_content_nav="'.$row["access_content_nav"].'"
								class="btn btn-primary btnTable btnEdit 
								name="update"><i class="fa fa-edit"></i> Edit
							</button> <button 
								data-toggle="modal" 
								data-backdrop="static" 
								data-keyboard="false" 
								data-target="#delete_userrole_modal" 
								data-position_id="'.$row["position_id"].'"
								data-position="'.$row["position"].'"
								class="btn btn-danger btnTable btnDelete 
								name="update"><i class="fa fa-trash-o"></i> Delete
							</button>';
			
			$data[] = $nestedData;
			
		}


		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;

		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		if($requestData['columns'][0]['search']['value'] == ""){ //if not getting a request 
			$requestData= $_REQUEST;
		}

		$columns = array( 
			// datatable column index  => database column name for sorting
			0 => 'position'
		);

		
		// getting total number records without any search
		$sql = "SELECT position_id FROM `jcw_position` WHERE enabled = 1";

		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		/////////////// totalamt + freight;
		$sql = "SELECT * FROM `jcw_position` WHERE enabled = 1";

		// getting records as per search parameters

		// if( !empty( $requestData['columns'][0]['search']['value']) ){   //date
		// 	$sql.=" AND ds.trandate = '".$trandate."' ";
		// }

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		// print_r($sql);
		// die();
		$query = $this->db->query($sql);

		$data = array();
		foreach($query->result_array() as $row){  // preparing an array for table tbody
			$nestedData=array(); 

			$nestedData[] = $row["position"];


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
	
	public function edit_userrole($r_position_id, $r_position, $checkbox_str, $content_checkbox_str){
		$sql = "UPDATE `jcw_position` SET `position`= ?, `access_nav`= ?, `access_content_nav`= ?, `date_updated`= ?,`date_created`= ? WHERE `position_id`= ?";

		$data = array($r_position, $checkbox_str, $content_checkbox_str, todaytime(), todaytime(), $r_position_id);
		$query = $this->db->query($sql, $data);
	}

	public function delete_userrole($r_position_id_delete){
		$sql = "UPDATE `jcw_position` SET `enabled` = 0 WHERE `position_id` = ?";

		$data = array($r_position_id_delete);
		$query = $this->db->query($sql, $data);
	}

	public function add_userrole($r_position, $checkbox_str, $acb_content_str){
		$sql = "INSERT INTO `jcw_position`(`position`, `access_nav`, `access_content_nav`, `date_updated`, `date_created`) VALUES (?, ?, ?, ?, ?)";
		$data = array($r_position, $checkbox_str, $acb_content_str, todaytime(), todaytime());
		$query = $this->db->query($sql, $data);
	}

	public function checkunique_userrole($position){
		$sql = "SELECT position_id FROM jcw_position WHERE position = ?
		AND enabled = ?";
		$data = array($position, 1);
		return $this->db->query($sql, $data);
	}

	public function get_pb_userrole_main_nav(){
		$position_id = $this->session->userdata('position_id');
		if ($position_id == 1) { //for superuser only
			$sql = "SELECT main_nav_id, main_nav_desc, attr_val, attr_val_edit, arrangement 
				FROM cp_main_navigation
				WHERE enabled = 1 ORDER BY arrangement ASC";

		}else{
			$sql = "SELECT main_nav_id, main_nav_desc, attr_val, attr_val_edit, arrangement 
				FROM cp_main_navigation
				WHERE enabled = 1 AND main_nav_id != 12 ORDER BY arrangement ASC";	

		}
		
		return $this->db->query($sql);
	}
	
	public function get_content_nav_userrole(){ //071618
		$position_id = $this->session->userdata('position_id');
		if ($position_id == 1) { //for superuser only
			$sql = "SELECT * FROM cp_content_navigation WHERE status = 1 ORDER BY cn_fkey ASC";
		
		}else{
			$sql = "SELECT * FROM cp_content_navigation WHERE status = 1 AND cn_fkey != 12 ORDER BY cn_fkey ASC";
		
		}

		return $this->db->query($sql);
		
	}

	public function get_cn_fkey_eq_name(){ //071618
		$position_id = $this->session->userdata('position_id');
		if ($position_id == 1) { //for superuser only

			$sql = "SELECT * FROM cp_main_navigation jmn 
					LEFT JOIN cp_content_navigation jcn 
					ON jmn.main_nav_id = cn_fkey
					WHERE jcn.status = 1
					AND  jmn.enabled = 1
					GROUP BY jmn.main_nav_id
					ORDER BY jmn.main_nav_id ASC";

		}else{

			$sql = "SELECT * FROM cp_main_navigation jmn 
					LEFT JOIN cp_content_navigation jcn 
					ON jmn.main_nav_id = cn_fkey
					WHERE jcn.status = 1
					AND  jmn.enabled = 1
					AND jmn.main_nav_id != 12
					GROUP BY jmn.main_nav_id
					ORDER BY jmn.main_nav_id ASC";
		}
		return $this->db->query($sql);
	}

	public function email_does_not_exist($email, $idno =""){
		$sql ="SELECT COUNT(username) as count, enabled, user_id 
			   FROM jcw_users 
			   WHERE username = ? AND enabled > ?";
		if(!empty($idno)){
			$sql .=" AND user_id <> " . $this->db->escape_str($idno) . "";;
		}
		$data = array($email, 0);
		$result  = $this->db->query($sql, $data)->row();
		if($result->count > 0){
			$not_exist = false;
		}else{
			$not_exist = true;
		}
		return $not_exist;
	}

	// End - User Role

	public function update_modal_confirm($id, $url, $name, $description, $category){
		$sql = "UPDATE `cp_content_navigation` SET `cn_url` = ?, `cn_name` = ?, `cn_description` = ?, `cn_fkey` = ? WHERE `id` = ?";
		$data = array($url, $name, $description, $category, $id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function add_modal_confirm($url, $name, $description, $category){
		$sql = "INSERT INTO cp_content_navigation (cn_url, cn_name, cn_description, cn_fkey, date_created, arrangement, status)
			VALUES (?, ?, ?, ?, ?, ?, ?)";

		$data = array($url, $name, $description, $category, date("Y-m-d H:i:s"), 0, 1);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	
	public function cron_logs_table(){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status = $this->input->post('_record_status');
		$cron_name 			= $this->input->post('cron_name');
		$cron_desc 			= $this->input->post('cron_desc');
		// $_description 	= $this->input->post('_description');
		// $_main_nav 		= $this->input->post('_main_nav');

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'cron_name',
			1 => 'cron_desc',
			2 => 'cron_start',
			3 => 'cron_end',
			4 => 'cron_status'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_cron_logs ";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * FROM sys_cron_logs ";

		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE enabled = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2 || $_record_status == 0){
			$sql.=" WHERE enabled = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if($cron_name != ""){
			$sql.=" AND cron_name LIKE '%" . $this->db->escape_like_str($cron_name) . "%' ";
		}
		if($cron_desc != ""){
			$sql.=" AND cron_desc LIKE '%" . $this->db->escape_like_str($cron_desc) . "%' ";
		}
		// if($_description != ""){
		// 	$sql.=" AND cn_description LIKE '%" . $this->db->escape_like_str($_description) . "%' ";
		// }
		// if($_main_nav != ""){
		// 	$sql.=" AND main_nav_id = " . $this->db->escape($_main_nav) . "";
		// }

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["cron_name"];
			$nestedData[] = $row["cron_desc"];
			$nestedData[] = $row["cron_start"];
			$nestedData[] = $row["cron_end"];
			$nestedData[] = $row["cron_status"];

			if ($row['enabled'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['enabled'] == 2 || $row['enabled'] == 0) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}

			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			  	</div>
			</div>';
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

	public function disable_modal_confirm_cron_logs($disable_id, $record_status){
		$sql = "UPDATE `sys_cron_logs` SET `enabled` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}
}