<?php 
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) //to ignore maximum time limit
{
    @set_time_limit(0);
}
ini_set('memory_limit', '1024M');

class Model_settings extends CI_Model {
	public $app_db;

	public function get_emptype(){
		$sql = "SELECT * FROM jcw_emptype WHERE status = 1 ORDER BY description ASC";
		return $this->app_db->query($sql);
	}

	// Start - Area
		public function area_table($area){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_areas WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_areas WHERE status = 1";

			// getting records as per search parameters
			if( $area != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($area) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-success btnView btnTable" name="update" data-value="'.$row['id'].'" areaId="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" areaId="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_area($info_desc){

			$sql = "INSERT INTO `8_areas`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function insert_areasched($areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check){
			$sql = "INSERT INTO 8_areasched(areaid,mon,tue,wed,thu,fri,sat,status) VALUES (?,?,?,?,?,?,?,?)";
			$data = array($areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check, 1);	
			return $this->app_db->query($sql, $data); 
		}

		public function get_area($areaId){
			$sql = "SELECT *  FROM 8_areas ar LEFT JOIN 8_areasched ars ON ar.id = ars.areaid WHERE ar.id = ?
			AND ar.status = ?";
			$data = array($areaId, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_area_unique($areaName){
			$sql = "SELECT id FROM 8_areas WHERE description = ?
			AND status = ?";
			$data = array($areaName, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_area($info_areaId, $info_desc){

			$sql = "UPDATE `8_areas` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_areaId);
			return $this->app_db->query($sql, $data);
		}

		public function checkAreaExist($info_areaId) {
			$sql = "SELECT areaid  FROM 8_areasched WHERE areaid = ?";
			$data = array($info_areaId);
			return $this->app_db->query($sql, $data);
		}

		public function update_areasched($areaId, $monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check){
			$sql = "UPDATE 8_areasched SET mon = ?, tue= ?, wed=?, thu=?, fri=?, sat=? WHERE areaid = ?";
			$data = array($monday_check, $tuesday_check, $wednesday_check, $thursday_check, $friday_check, $saturday_check, $areaId);	
			return $this->app_db->query($sql, $data); 
		}

		public function delete_area($del_areaId){
			
			$sql = "UPDATE `8_areas` SET `status`= 0 WHERE id = ?";
			$data = array($del_areaId);

			$query = $this->app_db->query($sql, $data);

			$sql = "UPDATE `8_areasched` SET status = 0 WHERE areaid = ?";
			$data = array($del_areaId);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Area

	// Start - Credit Term
		public function credit_term_table($credit){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_credit WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_credit WHERE status = 1";

			// getting records as per search parameters
			if( $credit != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($credit) ."%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_credit_term($info_desc){

			$sql = "INSERT INTO `8_credit`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_credit_term($id){
			$sql = "SELECT *  FROM 8_credit WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_credit_term_unique($creditTerm){
			$sql = "SELECT id FROM 8_credit WHERE description = ?
			AND status = ?";
			$data = array($creditTerm, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_credit_term($info_id, $info_desc){

			$sql = "UPDATE `8_credit` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_credit_term($del_id){
			
			$sql = "UPDATE `8_credit` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Credit Term

	// Start - Delivery Vehicle
		public function delivery_vehicle_table($plateno){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'plateno'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 9_delvehicle WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, plateno FROM 9_delvehicle WHERE status = 1";

			// getting records as per search parameters
			if( $plateno != "" ){  //position
				$sql.=" AND plateno LIKE '%" . $this->app_db->escape_like_str($plateno). "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["plateno"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_delivery_vehicle($info_desc){

			$sql = "INSERT INTO `9_delvehicle`(`plateno`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_delivery_vehicle($id){
			$sql = "SELECT *  FROM 9_delvehicle WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_delivery_vehicle_unique($delvehicle){
			$sql = "SELECT id FROM 9_delvehicle WHERE plateno = ?
			AND status = ?";
			$data = array($delvehicle, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_delivery_vehicle($info_id, $info_desc){

			$sql = "UPDATE `9_delvehicle` SET `plateno` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_delivery_vehicle($del_id){
			
			$sql = "UPDATE `9_delvehicle` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Delivery Vehicle

	// Start - Employee
		public function employee_table($id, $name, $type){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'empid',
					2 => 'CONCAT(fname, mname, lname)',
					3 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT emp.id 'id' FROM jcw_employee emp LEFT JOIN jcw_emptype et ON emp.emptypeid = et.id WHERE emp.status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT emp.id 'id', empid, fname, mname, lname, description FROM jcw_employee emp LEFT JOIN jcw_emptype et ON emp.emptypeid = et.id WHERE emp.status = 1";

			// getting records as per search parameters
			if( $id != "" ){  //position
				$sql.=" AND empid LIKE '%" . $this->app_db->escape_like_str($id) . "%' ";
			}
			if( $name != "" ){  //position
				$sql.=" AND CONCAT(fname, mname, lname) LIKE '%" . str_replace('', '', $this->app_db->escape_like_str($name)) . "%' ";
			}
			if( $type != "" ){  //position
				$sql.=" AND description LIKE '%" . str_replace('', '', $this->app_db->escape_like_str($type)) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["empid"];
				$nestedData[] = $row["fname"].' '.$row["mname"].' '.$row["lname"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_employee($info_empid, $info_fname, $info_mname, $info_lname, $info_type){

			$sql = "INSERT INTO `jcw_employee`(`empid`,`fname`,`mname`,`lname`,`emptypeid`,`status`) VALUES (?,?,?,?,?,?)";

			$data = array($info_empid, $info_fname, $info_mname, $info_lname, $info_type, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_employee($id){
			$sql = "SELECT *  FROM jcw_employee WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_employee_unique($info_fname, $info_mname, $info_lname,$info_empid, $info_type){
			$sql = "SELECT id FROM jcw_employee WHERE fname = ? AND mname = ? AND lname = ? AND empid = ? AND emptypeid = ?
			AND status = ?";
			$data = array($info_fname, $info_mname, $info_lname, $info_empid, $info_type, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_employee($info_id, $info_empid, $info_fname, $info_mname, $info_lname, $info_type){

			$sql = "UPDATE `jcw_employee` SET `empid` = ?, `fname` = ?, `mname` = ?, `lname` = ?, `emptypeid` = ? WHERE id = ?";
			$data = array($info_empid, $info_fname, $info_mname, $info_lname, $info_type, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_employee($del_id){
			
			$sql = "UPDATE `jcw_employee` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}

		public function select_employee($empid, $id){
			$sql = "SELECT id, empid FROM jcw_employee WHERE empid = ? AND id = ? AND status = 1";
			$data = array($empid, $id);
			return $this->app_db->query($sql, $data);
		}

		public function check_employee_exist($empid){
			$sql = "SELECT empid FROM jcw_employee WHERE status=1 AND empid = ? ";
			$result = $this->app_db->query($sql,$empid);
			if ($result->num_rows() > 0)
				return $result->row()->empid;
			else
				return false;
		}


	// End - Employee

	// Start - Employee Type
		public function employee_type_table($empType){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM jcw_emptype WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM jcw_emptype WHERE status = 1";

			// getting records as per search parameters
			if( $empType != "" ){  //position
				$sql .= " AND description LIKE '%" . $this->app_db->escape_like_str($empType) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_employee_type($info_desc){

			$sql = "INSERT INTO `jcw_emptype`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_employee_type($id){
			$sql = "SELECT *  FROM jcw_emptype WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_employee_type_unique($emptype){
			$sql = "SELECT id FROM jcw_emptype WHERE description = ?
			AND status = ?";
			$data = array($emptype, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_employee_type($info_id, $info_desc){

			$sql = "UPDATE `jcw_emptype` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_employee_type($del_id){
			
			$sql = "UPDATE `jcw_emptype` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Employee Type

	// Start - Franchise
		public function franchise_table($franchise){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description',
					2 => 'franchisefee',
					3 => 'cashbond',
					4 => 'commission'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_franchises WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description, franchisefee, cashbond, commission FROM 8_franchises WHERE status = 1";

			// getting records as per search parameters
			if( $franchise != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($franchise) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = number_format($row["franchisefee"], 2);
				$nestedData[] = number_format($row["cashbond"], 2);
				$nestedData[] = number_format($row["commission"], 2);
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_franchise($info_desc, $info_fee, $info_cashbond, $info_commission){

			$sql = "INSERT INTO `8_franchises`(`description`,`franchisefee`,`cashbond`,`commission`,`status`) VALUES (?,?,?,?,?)";

			$data = array($info_desc, $info_fee, $info_cashbond, $info_commission, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_franchise($id){
			$sql = "SELECT * FROM 8_franchises WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_franchise_unique($franchise){
			$sql = "SELECT id FROM 8_franchises WHERE description = ?
			AND status = ?";
			$data = array($franchise, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_franchise($info_id, $info_desc, $info_fee, $info_cashbond, $info_commission){

			$sql = "UPDATE `8_franchises` SET `description` = ?, `franchisefee` = ?, `cashbond` = ?, `commission` = ? WHERE id = ?";
			$data = array($info_desc, $info_fee, $info_cashbond, $info_commission, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_franchise($del_id){
			
			$sql = "UPDATE `8_franchises` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Franchise

	// Start - GL Accounts
		public function gl_accounts_table($account, $type){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description',
					2 => 'acttype',
					3 => 'accountcode'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_accountlist WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description, acttype, accountcode FROM 8_accountlist WHERE status = 1";

			// getting records as per search parameters
			if( $account != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($account) . "%' ";
			}
			if( $type != "" ){  //position
				$sql.=" AND acttype LIKE '%" . $this->app_db->escape_like_str($type) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = $row["acttype"];
				$nestedData[] = $row["accountcode"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-success btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_gl_accounts($info_desc, $info_type, $accountcode){

			$sql = "INSERT INTO `8_accountlist`(`description`,`acttype`,`status`, `accountcode`) VALUES (?,?,?,?)";

			$data = array($info_desc, $info_type, 1, $accountcode);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_gl_accounts($id){
			$sql = "SELECT *  FROM 8_accountlist WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_gl_accounts_unique($acc){
			$sql = "SELECT id FROM 8_accountlist WHERE description = ?
			AND status = ?";
			$data = array($acc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_gl_accounts_unique_atype($acc, $info_type){
			$sql = "SELECT id FROM 8_accountlist WHERE description = ?
			AND status = ? AND acttype = ?";
			$data = array($acc, 1, $info_type);
			return $this->app_db->query($sql, $data);
		}

		public function update_gl_accounts($info_id, $info_type, $info_desc, $accountcode){

			$sql = "UPDATE `8_accountlist` SET `description` = ?, `acttype` = ?, accountcode = ? WHERE id = ?";
			$data = array($info_desc, $info_type, $accountcode, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_gl_accounts($del_id){
			
			$sql = "UPDATE `8_accountlist` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - GL Accounts

	// Start - Inventory Category
		public function inventory_category_table($category){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_itemcategory WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_itemcategory WHERE status = 1";

			// getting records as per search parameters
			if( $category != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($category) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_inventory_category($info_desc){

			$sql = "INSERT INTO `8_itemcategory`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_inventory_category($id){
			$sql = "SELECT *  FROM 8_itemcategory WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_inventory_category_unique($invcat){
			$sql = "SELECT id FROM 8_itemcategory WHERE description = ?
			AND status = ?";
			$data = array($invcat, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_inventory_category($info_id, $info_desc){

			$sql = "UPDATE `8_itemcategory` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_inventory_category($del_id){
			
			$sql = "UPDATE `8_itemcategory` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Inventory Category

	// Start - Payment Option
		public function payment_option_table($payment){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_payment WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_payment WHERE status = 1";

			// getting records as per search parameters
			if( $payment != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($payment) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_payment_option($info_desc){

			$sql = "INSERT INTO `8_payment`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_payment_option($id){
			$sql = "SELECT *  FROM 8_payment WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_payment_option_unique($desc){
			$sql = "SELECT id FROM 8_payment WHERE description = ?
			AND status = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_payment_option($info_id, $info_desc){

			$sql = "UPDATE `8_payment` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_payment_option($del_id){
			
			$sql = "UPDATE `8_payment` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Payment Option

	// Start - Price Category
		public function price_category_table($category){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_pricecat WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_pricecat WHERE status = 1";

			// getting records as per search parameters
			if( $category != ""){  //position
				$sql .= " AND description LIKE '%" . $this->app_db->escape_like_str($category) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_price_category($info_desc){

			$sql = "INSERT INTO `8_pricecat`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_price_category($id){
			$sql = "SELECT *  FROM 8_pricecat WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_price_category_unique($desc){
			$sql = "SELECT id FROM 8_pricecat WHERE description = ?
			AND status = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_price_category($info_id, $info_desc){

			$sql = "UPDATE `8_pricecat` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_price_category($del_id){
			
			$sql = "UPDATE `8_pricecat` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Price Category

	// Start - Sales Area
		public function sales_area_table($area){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 9_salesarea WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 9_salesarea WHERE status = 1";

			// getting records as per search parameters
			if( $area != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($area) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_sales_area($info_desc){

			$sql = "INSERT INTO `9_salesarea`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_sales_area($id){
			$sql = "SELECT *  FROM 9_salesarea WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_sales_area_unique($desc){
			$sql = "SELECT id FROM 9_salesarea WHERE description = ?
			AND status = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_sales_area($info_id, $info_desc){

			$sql = "UPDATE `9_salesarea` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_sales_area($del_id){
			
			$sql = "UPDATE `9_salesarea` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Sales Area

	// Start - Shipping
		public function shipping_table(){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT * FROM 8_shipping WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT * FROM 8_shipping WHERE status = 1";

			// getting records as per search parameters
			if( !empty($requestData['columns'][0]['search']['value']) ){  //position
				$sql.=" AND description LIKE '%".str_replace('', '', sanitize($requestData['columns'][0]['search']['value']))."%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#viewShippingModal" class="btn btn-success btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_shipping($info_desc){

			$sql = "INSERT INTO `8_shipping`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_shipping($id){
			$sql = "SELECT *  FROM 8_shipping WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_shipping_unique($desc){
			$sql = "SELECT id FROM 8_shipping WHERE description = ?
			AND status = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_shipping($info_id, $info_desc){

			$sql = "UPDATE `8_shipping` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_shipping($del_id){
			
			$sql = "UPDATE `8_shipping` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Shipping

	// Start - Ticket Status
		public function ticket_status_table($status){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_ticketstatus WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_ticketstatus WHERE status = 1";

			// getting records as per search parameters
			if( $status != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str(str_replace('', ' ', $status)) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_ticket_status($info_desc){

			$sql = "INSERT INTO `8_ticketstatus`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_ticket_status($id){
			$sql = "SELECT *  FROM 8_ticketstatus WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_ticket_status_unique($desc){
			$sql = "SELECT id FROM 8_ticketstatus WHERE description = ?
			AND status = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_ticket_status($info_id, $info_desc){

			$sql = "UPDATE `8_ticketstatus` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function check_ticketstatus_exist($empid){
			$sql = "SELECT description FROM 8_ticketstatus WHERE status=1 AND description = ? ";
			$result = $this->app_db->query($sql,$empid);
			if ($result->num_rows() > 0)
				return $result->row()->description;
			else
				return false;
		}
		
		public function delete_ticket_status($del_id){
			
			$sql = "UPDATE `8_ticketstatus` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Ticket Status

	// Start - Unit of Measurement
		public function uom_table($uom){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_uom WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_uom WHERE status = 1";

			// getting records as per search parameters
			if( $uom != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($uom) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  <button class="btn btn-danger btnDelete btnTable" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
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

		public function insert_uom($info_desc){

			$sql = "INSERT INTO `8_uom`(`description`,`status`) VALUES (?,?)";

			$data = array($info_desc, 1);
			$this->app_db->query($sql, $data);

			return $this->app_db->insert_id();
		}

		public function get_uom($id){
			$sql = "SELECT *  FROM 8_uom WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);	
		}

		public function get_uom_unique($desc){
			$sql = "SELECT id FROM 8_uom WHERE description = ?
			AND status = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}

		public function update_uom($info_id, $info_desc){

			$sql = "UPDATE `8_uom` SET `description` = ? WHERE id = ?";
			$data = array($info_desc, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_uom($del_id){
			
			$sql = "UPDATE `8_uom` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Unit of Measurement

	// Start - Warehouse Location
		public function warehouse_location_table($location){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_GET;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'id',
					1 => 'description'
				);

			// getting total number records without any search
			$sql = "SELECT id FROM 8_itemloc WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM 8_itemloc WHERE status = 1";

			// getting records as per search parameters
			if( $location != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($location) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i>Edit</button>  <button class="btn btn-danger btnDelete btnTable btn-inline" name="delete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i>Delete</button>';
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

		public function insert_warehouse_location($info_desc, $info_address) {
			$sql = "INSERT INTO `8_itemloc`(`description`,`address`,`status`) VALUES (?, ?, ?)";
	
			$data = array($info_desc, $info_address, 1);
			$this->app_db->query($sql, $data);
	
			return $this->app_db->insert_id();
		}

		public function get_warehouse_location($id) {
			$sql = "SELECT * FROM `8_itemloc` WHERE id = ?
			AND status = ?";
			$data = array($id, 1);
			return $this->app_db->query($sql, $data);
		}

		public function get_warehouse_location_unique($desc){
			$sql = "SELECT `id` FROM `8_itemloc` WHERE `description` = ? AND `status` = ?";
			$data = array($desc, 1);
			return $this->app_db->query($sql, $data);
		}
	
		public function update_warehouse_location($info_id, $info_desc, $info_address){
			$sql = "UPDATE `8_itemloc` SET `description` = ?, `address` = ? WHERE `id` = ?";
			$data = array($info_desc, $info_address, $info_id);
			return $this->app_db->query($sql, $data);
		}

		public function delete_warehouse_location($del_id){
			
			$sql = "UPDATE `8_itemloc` SET `status`= 0 WHERE id = ?";
			$data = array($del_id);

			$query = $this->app_db->query($sql, $data);
		}
	// End - Warehouse Location

	# Start - Currency
		public function insert_currency($code, $desc, $val)
		{
			$sql = "INSERT INTO pb_currency(curr_code, curr_desc, value) VALUES (?,?,?)";
			$data = array($code, $desc, $val);
			
			return $this->app_db->query($sql, $data);
		}

		public function retrieve_currencies($status, $symbol)
		{
			# Storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			# Datatable column index  => database column name for sorting
			$columns = array(
				0 => 'id'
				, 1 => 'curr_code'
				, 2 => 'curr_desc'
				, 3 => 'value'
			);

			# Getting total number records without any search
			$sql = "SELECT id, curr_code, curr_desc, value, status FROM pb_currency";

			# Getting records as per search parameters
			if( $status == '' && $symbol == '') # If status is empty and symbol is empty
			{
				$sql = "SELECT id, curr_code, curr_desc, value, status FROM pb_currency"; # Initial query that searches all
			}
			else
			{
				if($status != '' && $symbol == '') # If status is set and symbol is empty
				{
					$sql .= " WHERE status LIKE '%" . $this->app_db->escape_like_str($status) . "%' ";
				}
				else if($status == '' && $symbol != '') # When symbol is set
				{
					$sql .= " WHERE curr_code LIKE '%" . $this->app_db->escape_like_str($symbol) . "%' ";
				}
			}

			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			
			$query = $this->app_db->query($sql);

			$data = array();

			foreach( $query->result_array() as $row ) # Preparing an array for table tbody
			{  
				$nestedData = [];

				# First Column
				$nestedData[] = $row["id"];

				# Second Column
				$nestedData[] = $row["curr_code"];

				# Third Column
				$nestedData[] = $row["curr_desc"];
				
				# Fourth Column
				$nestedData[] = $row["value"];

				# Fifth Column
				if($row['status'] == 1)
				{
					$buttons = '
					<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnUpdate" name="update" data-id="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>  
					<button class="btn btn-danger btnToggle" data-status="'.$row['status'].'" data-id="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>';
				}
				else
				{
					$buttons = '<button class="btn btn-warning btnToggle" data-status="'.$row['status'].'" data-id="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Restore</button>';
				}

				$nestedData[] = $buttons;
				
				$data[] = $nestedData;
			}

			$json_data = array(
				"draw"            => intval( $requestData['draw'] ), # for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => intval( $totalData ),  		 # total number of records
				"recordsFiltered" => intval( $totalFiltered ), 		 # total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => $data   						 # total data array
			);

			return $json_data;
		}

		public function retrieve_currency($id)
		{
			$sql = "SELECT curr_code, curr_desc, value FROM pb_currency WHERE id = ?";
			$data = array($id);
			
			return $this->app_db->query($sql, $data);
		}

		public function update_currency($code, $desc, $val, $id)
		{
			$sql = "UPDATE pb_currency SET curr_code = ?, curr_desc = ?, value = ?, date_updated = ? where id = ?";
			$data = array($code, $desc, $val, datetime(), $id);
			
			return $this->app_db->query($sql, $data);
		}

		public function toggle_currency_status($status, $id)
		{
			$sql = "UPDATE pb_currency SET status = ? WHERE id = ?";
			$data = $status == 0 ? array(1, $id) : array(0, $id) ;
			
			return $this->app_db->query($sql, $data);
		}
	# End - Currency

	// Start - user role
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

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			// print_r($sql);
			// die();
			$query = $this->app_db->query($sql);

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

		public function get_crud_navid($positionid){
			$asql = "SELECT jcn.id, jca.status, jca.position_id, jcn.cn_name FROM `jcw_crud_access` jca
			LEFT JOIN cp_content_navigation jcn ON jca.navigation_id = jcn.id
			WHERE jca.position_id = ? AND jcn.edit = 1 AND jca.edit = 1 AND jca.status = 1 AND jcn.status  = 1";
			$data = array($positionid);
			$aquery = $this->db->query($asql, $data);

			$mappingId = array();
			foreach( $aquery->result() as $row ) {
				array_push($mappingId, $row->id);
			}

			$comma_separated = implode(",", $mappingId);

			return $comma_separated;
		}

		public function get_nav_crud(){ //071618
			$sql = "SELECT * FROM cp_content_navigation WHERE status = 1 AND status = 1 ORDER BY cn_fkey ASC";
			return $this->db->query($sql);
			
		}

		public function get_status_crud($position_id){

			$asql = "SELECT * FROM `jcw_crud_access` WHERE position_id = ?";
			$data = array($position_id);
			$aquery = $this->db->query($asql, $data);

			$mappingId = array(); 
			foreach( $aquery->result() as $row ) {
				array_push($mappingId, $row->status);
			}

			$comma_separated = implode(",", $mappingId);

			return $comma_separated;
		}
		
		public function edit_userrole($r_position_id, $r_position, $checkbox_str, $content_checkbox_str, $can_approve, $can_process, $can_delete, $can_edit){
			$sql = "UPDATE `jcw_position` SET `position`= ?, `access_nav`= ?, `access_content_nav`= ?, can_approve = ?, can_process = ?, can_delete = ?, can_edit = ?,  `date_updated`= ?,`date_created`= ? WHERE `position_id`= ?";

			$data = array($r_position, $checkbox_str, $content_checkbox_str, $can_approve, $can_process, $can_delete, $can_edit, todaytime(), todaytime(), $r_position_id);
			$query = $this->db->query($sql, $data);
		}

		public function delete_userrole($r_position_id_delete){
			$sql = "UPDATE `jcw_position` SET `enabled` = 0 WHERE `position_id` = ?";

			$data = array($r_position_id_delete);
			$query = $this->db->query($sql, $data);
		}

		public function add_userrole($r_position, $checkbox_str, $acb_content_str, $ar_approve, $ar_process, $ar_edit, $ar_delete){
		    $sql = "INSERT INTO `jcw_position`(`position`, `access_nav`, `access_content_nav`, `can_approve`, `can_process`, `can_edit`, `can_delete`, `date_updated`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		    $data = array($r_position, $checkbox_str, $acb_content_str, $ar_approve, $ar_process, $ar_edit, $ar_delete, todaytime(), todaytime());
		    $query = $this->db->query($sql, $data);  
		    return $this->db->insert_id();
		}

		public function checkunique_userrole($position){
			$sql = "SELECT position_id FROM jcw_position WHERE position = ?
			AND enabled = ?";
			$data = array($position, 1);
			return $this->db->query($sql, $data);
		}

	//End - user role

	public function get_content_nav_userrole(){ //071618
		$position_id = $this->session->userdata('position_id');
		if ($position_id == 1) { //for superuser only
			$sql = "SELECT * FROM cp_content_navigation WHERE status = 1 ORDER BY cn_fkey ASC";
		
		}else{
			$sql = "SELECT * FROM cp_content_navigation WHERE status = 1 AND cn_fkey != 12 ORDER BY cn_fkey ASC";
		
		}

		return $this->db->query($sql);
		
	}

	public function get_company_name($company_code){
		if($company_code != 0){
			$sql = "SELECT company_name FROM pb_companies WHERE company_code = ".$company_code;

			return $this->db->query($sql)->row()->company_name;
		} else{
			return 'PandaBooks';
		}
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

	// Start - System Users

		public function system_user_table($daterange_from, $daterange_to, $position, $fullname){
			$position_id = $this->session->userdata('position_id');
			// storing  request (ie, get/post) global array to a variable  
			$requestData = $_REQUEST;

			$columns = array( 
				// datatable column index  => database column name for sorting
					0 => 'position',
					1 => 'user_fullname'
				);

			$position_id = $this->session->userdata('position_id');

			if ($position_id == 1) {
				$sql = "SELECT user_id, position_id, position, CONCAT(user_fname, ' ', user_lname) AS user_fullname, enabled FROM jcw_users LEFT JOIN (SELECT position_id AS pos_id, position FROM jcw_position) AS position_table ON position_table.pos_id = jcw_users.position_id WHERE enabled IN (1,2) ";
			}else{
				$sql = "SELECT user_id, position_id, position, CONCAT(user_fname, ' ', user_lname) AS user_fullname, enabled FROM jcw_users LEFT JOIN (SELECT position_id AS pos_id, position FROM jcw_position) AS position_table ON position_table.pos_id = jcw_users.position_id WHERE enabled IN (1,2) AND position_id != 1 ";
			}

			// getting records as per search parameters
			if ($daterange_from != "") {
				$sql .="AND jcw_users.date_created BETWEEN '".$this->app_db->escape_like_str($daterange_from)."' AND '".$this->app_db->escape_like_str($daterange_to)."' ";
			}

			if($position != "" ){  //position
				// $sql .=" AND position_id = " . $this->app_db->escape_str($position);
				$sql .=" AND position LIKE '%".$this->app_db->escape_like_str(trim($position))."%' ESCAPE '!' ";
			}
			if($fullname != ""){
				$sql .=" AND CONCAT(user_fname, ' ', user_lname) LIKE '%".$this->app_db->escape_like_str($fullname)."%' ESCAPE '!' ";
			}
			
			$query = $this->db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;
			
			 // when there is a search parameter then we have to modify total number filtered rows as per search result.
			
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
			$query = $this->db->query($sql);
	
			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array();
				$fullName;
				$nestedData[] = $row["position"];
				$nestedData[] = $row["user_fullname"];
				$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" data-value="'.$row['user_id'].'" id="'.$row['user_id'].'"><i class="fa fa-edit"></i>Edit</button>  <button class="btn btn-danger btnDelete btnTable btn-inline" name="delete" data-value="'.$row['user_id'].'" id="'.$row['user_id'].'"><i class="fa fa-trash-o"></i>Delete</button>';
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

		public function insert_system_user($row){
			$this->db->insert("jcw_users", $row);
			return $this->db->insert_id();
		}
	
		public function get_system_user($id){
			$sql = "SELECT *  FROM jcw_users WHERE user_id = ?
			AND enabled = ?";
			$data = array($id, 1);
			return $this->db->query($sql, $data);
		}
	
		public function get_system_user_unique($username){ //Simply returns the number of rows
			$this->db->where(array("username" => $username, "enabled" => 1));
			$this->db->from("jcw_users");
			return $this->db->count_all_results();
		}
	
		public function update_system_user($user_id, $row){
			$this->db->where("user_id", $user_id);
			$this->db->update("jcw_users", $row);
		}
	
		public function delete_system_user($del_user_id){
			$this->db->where("user_id", $del_user_id);
			$this->db->update("jcw_users", array("enabled" => 0, "date_updated" => date("Y-m-d H:i:s")));
		}
	
		public function get_positions(){
			$position_id = $this->session->userdata('position_id');

			if ($this->session->userdata('position_id') == 1){
				$sql = "SELECT * FROM jcw_position WHERE enabled = 1";	
			}else{
				$sql = "SELECT * FROM jcw_position WHERE enabled = 1 AND position_id !=	1";
			}

			return $this->db->query($sql);
		}
		// End - System Users

	//Start - User role main nav 090418
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
	//End - User role main nav 090418

	public function add_crud_navaccess($batchdata){

		// $sql = "INSERT INTO `jcw_crud_access`(navigation_id, position_id, edit,  status) VALUES (?, ?, ?, ?)";
		// $data = array($edit_access,$position_id, 1,  1);
		return $this->db->insert_batch('jcw_crud_access', $batchdata);
		//return $this->app_db->query($sql, $data);
	}

	public function edit_crud_navaccess($batchdata, $position_id){
		$sql = "UPDATE jcw_crud_access SET edit=? WHERE position_id=?";
	    $data_array = array(0,$position_id);
	    $query = $this->db->query($sql,$data_array);

	    return $this->db->insert_batch('jcw_crud_access', $batchdata);
	}

	// Start of Permit Type

		public function permit_type_table($description){
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			$columns = array(
				0 => 'id',
				1 => 'description'
			);

			// getting total number records without any search
			$sql = "SELECT id FROM pb_permit_type WHERE status = 1";
			$query = $this->app_db->query($sql);
			$totalData = $query->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

			$sql = "SELECT id, description FROM pb_permit_type WHERE status = 1";

			// getting records as per search parameters
			if( $description != "" ){  //position
				$sql.=" AND description LIKE '%" . $this->app_db->escape_like_str($description) . "%' ";
			}

			$query = $this->app_db->query($sql);
			$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query = $this->app_db->query($sql);

			$data = array();
			foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
				$nestedData=array(); 
				$nestedData[] = $row["id"];
				$nestedData[] = $row["description"];
				$nestedData[] = '
					<button class="btn btn-primary" id="btnUpdate" data-value="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>
					<button class="btn btn-danger" id="btnDelete" data-value="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>
				';
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

		public function save_permit_type($values) {
			$sql = "INSERT INTO `pb_permit_type`(`description`,`date_created`,`date_updated`) VALUES (?,?,?) ";

			return $this->app_db->query($sql, $values);
		}

		public function get_permit_type($id){
			$sql = "SELECT id, description FROM pb_permit_type WHERE id = ? AND status = ?";
			return $this->app_db->query($sql, array($id, 1));
		}

		public function update_permit_type($values){
			$sql = "UPDATE `pb_permit_type` SET `description` = ?, `date_updated` = ? WHERE id = ?";
			return $this->app_db->query($sql, $values);
		}

		public function delete_permit_type($values){
			$sql = "UPDATE `pb_permit_type` SET `date_updated` = ?, `status` = ? WHERE id = ?";
			return $this->app_db->query($sql, $values);
		}

		public function is_unique_permit_type($description) {
			$sql = "SELECT id FROM `pb_permit_type` WHERE description = ? AND status >= ?";
			return $this->app_db->query($sql, array($description, 1));
		}

	// End of Permit Type

	public function get_portal_announcement(){
		$sql = "SELECT * FROM  `cloudpanda-jc_portal`.`jc_home_announcement` WHERE status = 1";
		return $this->app_db->query($sql);
	}

	public function save_portal_announcement($title, $details, $document){

		if($document == ""){
			$sql = "UPDATE `cloudpanda-jc_portal`.`jc_home_announcement` SET title=?, subtitle=? WHERE status = 1";
			$data = array($title, $details);	
		}else{
			$sql = "UPDATE `cloudpanda-jc_portal`.`jc_home_announcement` SET title=?, subtitle=?, image=? WHERE status = 1";
			$data = array($title, $details, $document);	
		}
		
		return $this->app_db->query($sql, $data); 
	}

	//050319
	public function upgrade_package_table($search, $package_id, $package_description){
        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        $columns = array( 
            // datatable column index  => database column name for sorting
            0 => 'id',
            1 => 'description',
            2 => 'atype',
            3 => 'price'
        );

        $sql = "SELECT * FROM `8_packages` WHERE `status` = 1";

        if($search == "divitemcode"){
            $sql.=" AND `id` LIKE '%".preg_replace('/\s+/', '', sanitize($package_id))."%' ";
        }
        if($search == "divbarcode"){
            $sql.=" AND `description` LIKE '%".preg_replace('/\s+/', '', sanitize($package_description))."%' ";
        }

        $query = $this->app_db->query($sql);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $query = $this->app_db->query($sql);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();
            $nestedData[] = $row["id"];
            $nestedData[] = $row["description"];
            $nestedData[] = $row["atype"];
            $nestedData[] = number_format($row["price"], 2);
            $nestedData[] = '
                <a href="' . base_url() . 'Main_settings/upgrade_package_items/' . en_dec('en', $this->session->userdata('token_session')) . '/' . $row['id'] . '" class="btn btn-primary btnView" role="button"><i class="fa fa-eye"></i> Items</a> 
                <button class="btn btn-primary btnUpdate" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-pencil"></i> Edit</button> 
                <button class="btn btn-danger btnDelete" data-value="'.$row['id'].'" data-description="'.$row['description'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>
            ';

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

    public function insert_package_details($values) {
        $sql = "INSERT INTO `8_packages`(`description`,`shippingwt`,`atype`,`price`,`countryname`,`franchiseid`) VALUES (?,?,?,?,?,1)";

        return $this->app_db->query($sql, $values);
    }

    public function get_last_inserted_id() {
        $sql = "SELECT MAX(id) AS id FROM `8_packages`";

        return $this->app_db->query($sql);
    }

    public function insert_package_items($items) {
        return $this->app_db->insert_batch('8_packagesitem', $items);
    }

    public function delete_package(){
        $query = "UPDATE `8_packages` SET `status` = 0 WHERE `id` = ? ";
        $sql = "UPDATE `8_packagesitem` SET `status` = 0 WHERE `packageid` = ? ";

        if ($this->app_db->query($query, $this->input->post('del_item_id')) && $this->app_db->query($sql, $this->input->post('del_item_id'))) {
            $response['success'] = true;
            $response['message'] = "Record has been successfully deleted";
        }
        else {
            $response['success'] = false;
            $response['message'] = "Failed in deleting record.";
        }

        return $response;
    }
    
    public function get_package_details($values){
        $query = "SELECT `id`, `description`, `shippingwt`, `atype`, `price`, `countryname` 
            FROM `8_packages` 
            WHERE `id` = ? AND `status` >= 1";
                
        return $this->app_db->query($query, $values)->row_array();
    }

    public function update_package_details($values) {
        $sql = "UPDATE `8_packages` SET `description` = ?, `shippingwt` = ?, `atype` = ?, `price` = ?, `countryname` = ? WHERE `id` = ? ";

        return $this->app_db->query($sql, $values);
    }

    public function autocomplete_country($texttyped) {
        $sql = "SELECT id, countryname, currency 
            FROM 8_country 
            WHERE status = 1 AND countryname LIKE '%".$this->app_db->escape_like_str($texttyped)."%' ESCAPE '!' 
            ORDER by countryname LIMIT 5";

        return $this->app_db->query($sql)->result_array();
    }

    public function upgrade_package_details_table($values){
        // storing  request (ie, get/post) global array to a variable  
        $requestData = $_REQUEST;

        $columns = array( 
            // datatable column index  => database column name for sorting
            0 => 'a.itemid',
            1 => 'b.itemname',
            3 => 'a.qty'
        );

        $sql = "SELECT a.`packageid`, a.`id`, a.`itemid`, b.`itemname`, a.`qty` 
            FROM `8_packagesitem` AS a 
            LEFT JOIN `8_inventory` AS b ON a.`itemid` = b.`id` 
            WHERE a.`status` = 1 AND a.`packageid` = ? 
            GROUP BY a.`itemid` ";

        $query = $this->app_db->query($sql, $values);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $query = $this->app_db->query($sql, $values);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();
            $nestedData[] = $row["itemname"];
            $nestedData[] = number_format($row["qty"], 2);
            $nestedData[] = '
                <button class="btn btn-primary btnUpdate" 
                    data-value="'.$row['id'].'" 
                    data-itemid="'.$row['itemid'].'" 
                    data-itemname="'.$row['itemname'].'" 
                    data-upi_qty="'.$row['qty'].'" 
                    id="'.$row['id'].'"><i class="fa fa-pencil"></i> Edit</button> 
                <button class="btn btn-danger btnDelete" 
                    data-value="'.$row['id'].'" 
                    data-itemname="'.$row['itemname'].'" 
                    id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>
            ';

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

    public function autocomplete_item($texttyped) {
        $sql = "SELECT a.id, a.itemname 
            FROM 8_inventory AS a 
            LEFT JOIN 8_invstatus AS b ON a.id = b.itemid 
            WHERE a.status = 1 AND b.isforsale = 1 AND b.status = 1 AND a.itemname LIKE '%".$this->app_db->escape_like_str($texttyped)."%' ESCAPE '!' 
            ORDER by a.itemname LIMIT 5";

        return $this->app_db->query($sql)->result_array();
    }

    public function get_package_items($package_id) {
        $sql = "SELECT a.`itemid`, b.`itemname`, a.`qty` 
            FROM `8_packagesitem` AS a 
            LEFT JOIN `8_inventory` AS b ON a.`itemid` = a.`id` 
            WHERE a.`id` = ? AND a.`status` >= 1 ";

        return $this->app_db->query($sql, array($package_id));
    }

    public function insert_package_item($values) {
        $sql = "INSERT INTO `8_packagesitem`(`packageid`,`itemid`,`qty`,`status`) VALUES (?,?,?,1)";

        return $this->app_db->query($sql, $values);
    }

    public function update_package_item($values) {
        $sql = "UPDATE `8_packagesitem` SET `qty` = ? WHERE `id` = ? AND `itemid` = ? ";

        return $this->app_db->query($sql, $values);
    }

    public function delete_package_item($values) {
        $sql = "UPDATE `8_packagesitem` SET `status` = 0 WHERE `id` = ? ";

        return $this->app_db->query($sql, $values);
    }


	// End of Upgrade Package
	public function get_items(){
		$query = "SELECT id, itemname FROM 8_inventory";

		return $this->app_db->query($query);
	}

	public function package_release_cat_table($item){
			// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		$columns = array(
			0 => 'packagerelease_cat',
			1 => 'itemname'
		);

		// getting total number records without any search
		$sql = "SELECT a.id, a.itemid, a.packagerelease_cat, b.itemname, b.id as id2 FROM jcw_packagerelease_category as a LEFT JOIN 8_inventory as b ON a.itemid = b.id WHERE a.status=1 AND b.status=1";

		$query = $this->app_db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT a.id, a.itemid, a.packagerelease_cat, b.itemname, b.id as id2 FROM jcw_packagerelease_category as a LEFT JOIN 8_inventory as b ON a.itemid = b.id WHERE a.status=1 AND b.status=1";

		// getting records as per search parameters
		if( $item != "" ){  //position
			$sql.=" AND itemname LIKE '%" . $this->app_db->escape_like_str($item) . "%' ";
		}

		$query = $this->app_db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->app_db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["itemname"];

			$category = $row["packagerelease_cat"];

			if($category == 1){
				$nestedData[] = "Marketing Collaterals";
			}else if($category == 2){
				$nestedData[] = "Package Release";
			}else if($category == 3){
				$nestedData[] = "Replacements";
			}

			$nestedData[] = '
				<button class="btn btn-primary btnEdit" data-value="'.$row['id'].'" data-category="'.$row['packagerelease_cat'].'" data-item="'.$row['itemid'].'" data-id="'.$row['id'].'"><i class="fa fa-edit"></i> Edit</button>
				<button class="btn btn-danger btnDelete" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-trash-o"></i> Delete</button>
			';
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

	public function get_inventory_itemid_unique($itemid){
		$sql = "SELECT id FROM jcw_packagerelease_category WHERE itemid = ?
		AND status = ?";
		$data = array($itemid, 1);
		return $this->app_db->query($sql, $data);
	}

	public function add_releaseitem($category,$item){
		
		$query = "INSERT INTO jcw_packagerelease_category (itemid, packagerelease_cat, status) 
			VALUES (?,?,?)";

		$data = array($item, $category, 1);
		return $this->app_db->query($query,$data);
	}

	public function delete_releaseitem($id) {
		$query = "UPDATE jcw_packagerelease_category SET status = 0 WHERE id = ? ";

		$data = array($id);
		return $this->app_db->query($query, $data);
	}

	public function edit_releaseitem($category, $item, $id) {
		$query = "UPDATE jcw_packagerelease_category SET itemid = ?, packagerelease_cat = ?  WHERE id = ? ";

		$data = array($item, $category, $id);
		return $this->app_db->query($query, $data);
	}

	public function get_packagerelease_id($id){
		$sql = "SELECT * FROM jcw_packagerelease_category WHERE id = ?
		AND status = ?";
		$data = array($id, 1);
		return $this->app_db->query($sql, $data);	
	}

	public function get_packagerelease_info($item){
		$sql = "SELECT id, itemid FROM jcw_packagerelease_category WHERE status = ? AND itemid = ?";
		$data = array(1, $item);
		return $this->app_db->query($sql, $data);
	}

	// Start of Tax Settings

		public function taxes_table() {
			$sql = "SELECT `id`, `description`, `value` FROM `jcw_taxes` WHERE `status` = 1";

			return $this->app_db->query($sql);
		}

		public function save_tax($values) {
			$sql = "INSERT INTO `jcw_taxes` (`description`, `value`, `date_created`, `date_updated`) VALUES(?, ?, ?, ?)";
			
			return $this->app_db->query($sql, $values);
		}

		public function update_tax($values) {
			$sql = "UPDATE `jcw_taxes` SET `description` = ?, `value` = ?, `date_updated` = ? WHERE `status` = 1 AND `id` = ? ";

			return $this->app_db->query($sql, $values);
		}

		public function get_tax($tax_id) {
			$sql = "SELECT `id`, `description`, `value` FROM `jcw_taxes` WHERE `id` = ? ";

			return $this->app_db->query($sql, array($tax_id));
		}

		public function delete_tax($values) {
			$sql = "UPDATE `jcw_taxes` SET `status` = 0 WHERE `id` = ? ";

			return $this->app_db->query($sql, $values);
		}
		
	// End of Tax Settings

	# Start of Discount Settings
		
		# Get current discount value
		public function get_current_idisc() {
			$sql = "SELECT id, idsc FROM pb_discounts";

			return $this->app_db->query($sql);
		}

		# Save new discount value
		public function save_new_idisc($new_disc) {
			$sql = "UPDATE pb_discounts SET idsc = ? WHERE id = 1"; /* Id: 1 is hardcoded in the mean time since we only have 1 row to update from time to time */
			$data = ($new_disc);

			return $this->app_db->query($sql, $data);
		}

	# End of Discount Settings

	
	# Customization for first fil-bio

	public function get_inventory_expire($date){
		$query = "SELECT * FROM 8_inventory WHERE status = 1 AND expiration_date = ?";

		$data = array($date);
		return $this->app_db->query($query, $data);
	}

	public function comm_sup_table($supplier){
		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		$columns = array( 
			// datatable column index  => database column name for sorting
				0 => 'id',
				1 => 'suppliername'
			);

		// getting total number records without any search
		$sql = "SELECT * FROM 8_suppliers 
		WHERE status = 1";

		if( $supplier != "" ){  //position
			$sql.=" AND suppliername LIKE '%" . $this->app_db->escape_like_str($supplier) ."%' ";
		}

		$query = $this->app_db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->app_db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["id"];
			$nestedData[] = $row["suppliername"];
			$nestedData[] = '<button class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'">View</button>';
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

	public function comm_sup_product_table($supid){
		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		$columns = array( 
			// datatable column index  => database column name for sorting
				0 => 'id',
				1 => 'supplierid'
			);

		// getting total number records without any search
		$sql = "SELECT a.*, b.itemname, c.percentage FROM 8_supplieritem AS a
		LEFT JOIN 8_inventory AS b ON a.itemid = b.id
		LEFT JOIN pb_commission_supplier AS c ON a.supplierid = c.supid AND a.itemid = c.itemid
		WHERE a.status = 1 AND a.supplierid = ?";
	
		$data = array($supid);
		$query = $this->app_db->query($sql, $data);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->app_db->query($sql, $data);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["itemid"];
			$nestedData[] = $row["itemname"];
			$nestedData[] = $row["percentage"];
			$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-itemid="'.$row['itemid'].'" data-value="'.$row['supplierid'].'" id="'.$row['supplierid'].'"><i class="fa fa-edit"></i> Update</button>';
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

	public function targetsales_agent_table($agent){
		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		$columns = array( 
			// datatable column index  => database column name for sorting
				0 => 'id',
				1 => 'description'
			);

		// getting total number records without any search
		$sql = "SELECT a.*, b.target_sales, b.percentage FROM 8_agent AS a
		LEFT JOIN pb_targetsales_agent AS b ON a.id = b.agent_id
		WHERE a.status = 1";

		if( $agent != "" ){  //position
			$sql.=" AND a.description LIKE '%" . $this->app_db->escape_like_str($agent) ."%' ";
		}

		$query = $this->app_db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->app_db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$percentage   = ($row["percentage"] == "") ? 0 : $row["percentage"];
			$target_sales = ($row["target_sales"] == "") ? 0 : $row["target_sales"];

			$nestedData[] = $row["id"];
			$nestedData[] = $row["description"];
			$nestedData[] = number_format($target_sales, 2);
			$nestedData[] = $percentage.'%';
			$nestedData[] = '<button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="" class="btn btn-primary btnView btnTable" name="update" data-value="'.$row['id'].'" id="'.$row['id'].'"><i class="fa fa-edit"></i> Update</button>';
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

	public function get_supplier_info_comm($supid,$itemid){
		$sql = "SELECT a.*, b.percentage, c.itemname  FROM 8_supplieritem AS a
		LEFT JOIN pb_commission_supplier AS b ON a.supplierid = b.supid AND a.itemid = b.itemid
		LEFT JOIN 8_inventory AS c ON a.itemid = c.id
		WHERE a.supplierid = ? AND a.status = ? AND a.itemid = ?";
		$data = array($supid, 1, $itemid);
		return $this->app_db->query($sql, $data);
	}

	public function get_agent_targetsales($id){
		$sql = "SELECT a.*, b.target_sales, b.percentage  FROM 8_agent AS a
		LEFT JOIN pb_targetsales_agent AS b ON a.id = b.agent_id
		WHERE a.id = ? AND a.status = ?";
		$data = array($id, 1);
		return $this->app_db->query($sql, $data);
	}

	public function check_comm_supp($id, $itemid){
		$sql = "SELECT * FROM pb_commission_supplier
		WHERE supid = ? AND status = ? AND itemid = ?";
		$data = array($id, 1, $itemid);
		return $this->app_db->query($sql, $data);
	}

	public function check_target_sales($id){
		$sql = "SELECT * FROM pb_targetsales_agent
		WHERE agent_id = ? AND status = ?";
		$data = array($id, 1);
		return $this->app_db->query($sql, $data);
	}

	public function insert_comm_supp($supid, $itemid, $percentage, $trandate, $username) {
		$sql = "INSERT INTO pb_commission_supplier (`supid`, `itemid`, `percentage`, `username_inserted`,`date_inserted`,`status`)
			VALUES (?, ?, ?, ?, ?, ?)";
		$data = array($supid, $itemid, $percentage, $username, $trandate, 1);

		$query = $this->app_db->query($sql,$data);
	}

	public function insert_target_sales($agent_id, $target_sales, $percentage, $trandate, $username) {
		$sql = "INSERT INTO pb_targetsales_agent (`agent_id`, `target_sales`, `percentage`, `username_inserted`,`date_inserted`,`status`)
			VALUES (?, ?, ?, ?, ?, ?)";
		$data = array($agent_id, $target_sales, $percentage, $username, $trandate, 1);

		$query = $this->app_db->query($sql,$data);
	}

	public function update_comm_supp($supid, $itemid, $percentage, $trandate, $username) {
		$sql = "UPDATE pb_commission_supplier SET percentage = ?, username_updated = ?, date_updated = ? WHERE supid = ? AND itemid = ?";
		$data = array($percentage, $username, $trandate, $supid, $itemid);

		$query = $this->app_db->query($sql,$data);
	}

	public function update_target_sales($agent_id, $target_sales, $percentage, $trandate, $username) {
		$sql = "UPDATE pb_targetsales_agent SET target_sales = ?, percentage = ?, username_updated = ?, date_updated = ? WHERE agent_id = ?";
		$data = array($target_sales, $percentage, $username, $trandate, $agent_id);

		$query = $this->app_db->query($sql,$data);
	}

	public function member_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status  = $filters['_record_status'];
		$_shop 			 = $filters['_shop'];
		$_shopbranch 	 = $filters['_shopbranch'];
		$_name 			 = $filters['_name'];
		$_email 		 = $filters['_email'];
		$_mobile 		 = $filters['_mobile'];

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'id',
			1 => 'shop',
			2 => 'branchname',		
			3 => 'full_name',
			4 => 'mobile'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM app_members AS a
				LEFT JOIN sys_users AS b ON a.sys_user = b.id";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT a.id AS id, a.sys_user, b.username, (SELECT shopname FROM sys_shops WHERE id = a.sys_shop) AS shop, concat(a.fname,' ',a.mname,' ',a.lname) AS full_name, a.mobile_number AS mobile, a.status, b.avatar, x.branchname, a.branchid
				FROM app_members AS a
				LEFT JOIN sys_users AS b ON a.sys_user = b.id
				LEFT JOIN sys_branch_profile AS x ON a.branchid = x.id ";

		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE a.status = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE a.status = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE a.status > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if($_shop != ""){
			$sql.=" AND a.sys_shop = " . $this->db->escape_like_str($_shop) . " ";
		}
		if($_shopbranch != ""){
			$sql.=" AND a.branchid = " . $this->db->escape_like_str($_shopbranch) . " ";
		}
		if($_name != ""){
			$sql.=" AND concat(a.fname,' ',a.mname,' ',a.lname) LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_email != ""){
			$sql.=" AND b.username LIKE '%" . $this->db->escape_like_str($_email) . "%' ";
		}
		if($_mobile != ""){
			$sql.=" AND a.mobile_number LIKE '%" . $this->db->escape_like_str($_mobile) . "%' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		if (!$exportable) {
			$sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		}

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = '<img style="height:30px;" class="img img-circle memberavatar" src="'.get_s3_imgpath_upload().'assets/uploads/avatars/'.$row['avatar'].'" alt=""/>';

			if($row["shop"] != null && $row["shop"] != '')
                $nestedData[] = $row["shop"];
            else
                $nestedData[] = get_company_name(). '(Admin)';

            if ($row["branchname"] != '') {
            	$nestedData[] = $row["branchname"];
            }else{
            	$nestedData[] = "Main";
            }
            
			$nestedData[] = $row["full_name"];
			$nestedData[] = $row["username"];
			$nestedData[] = $row["mobile"];

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

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['members']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['members']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['members']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';
			
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

	public function member_disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `app_members` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function member_delete_modal_confirm($delete_id){
		$sql = "UPDATE `app_members` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_member_data($edit_id){
		$sql = "SELECT a.id AS id, a.sys_shop, a.fname, a.mname, a.lname, a.mobile_number AS mobile, b.avatar, branchid
				FROM app_members AS a
				LEFT JOIN sys_users AS b ON a.sys_user = b.id 
				WHERE a.id = ?";

		$data = array($edit_id);

		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function member_update_modal_confirm($id, $shop, $fname, $mname, $lname, $mobile, $shopbranch){
		$sql = "UPDATE `app_members` SET `sys_shop` = ?, `fname` = ?, `mname` = ?, `lname` = ?, `mobile_number` = ?, `branchid` = ?
				WHERE `id` = ?";

		$data = array($shop, $fname, $mname, $lname, $mobile, $shopbranch, $id);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function check_duplicate_member($user){
		$sql = "SELECT EXISTS(SELECT sys_user
				FROM app_members AS a
				WHERE sys_user = ?) AS checker";

		$data = array($user);

		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data)->row()->checker;
		}else{
			return 0;
		}
	}

	public function member_add_update_modal_confirm($shop, $user, $fname, $mname, $lname, $mobile, $shopbranch){
		$sql = "UPDATE `app_members` SET `sys_shop` = ?, `fname` = ?, `mname` = ?, `lname` = ?, `mobile_number` = ?, `branchid` = ?, `status` = 1 WHERE `sys_user` = ?";

		$data = array($shop, $fname, $mname, $lname, $mobile, $shopbranch, $user);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function member_add_modal_confirm($shop, $user, $fname, $mname, $lname, $mobile, $shopbranch){
		$sql = "INSERT INTO `app_members` (`sys_shop`, `sys_user`, `fname`, `mname`, `lname`, `mobile_number`, `branchid`, `created`, `updated`, `status`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$data = array($shop, $user, $fname, $mname, $lname, $mobile, $shopbranch, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 1);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}




	public function product_category_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status  = $filters['_record_status'];
		$_category  = $filters['_category'];
		$_name  = $filters['_name'];
		$_onmenu  = $filters['_onmenu'];
		$_priority  = $filters['_priority'];

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'category_code',
			1 => 'category_name',
			2 => 'on_menu',
			3 => 'priority',
			4 => 'updated'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM sys_product_category";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * 
			FROM sys_product_category";
		
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
		if($_category != ""){
			$sql.=" AND category_code LIKE '%" . $this->db->escape_like_str($_category) . "%' ";
		}
		if($_name != ""){
			$sql.=" AND category_name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_onmenu != ""){
			$sql.=" AND on_menu = '" . $this->db->escape_like_str($_onmenu) . "' ";
		}
		if($_priority != ""){
			$sql.=" AND priority = '" . $this->db->escape_like_str($_priority) . "' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["category_code"];
			$nestedData[] = $row["category_name"];

			if($row["on_menu"]==1){
				$nestedData[] = 'Displayed';
			} else{
				$nestedData[] = 'Not Displayed';
			}
				
			$nestedData[] = $row["priority"];
			$nestedData[] = $row["updated"];
				
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

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['category']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['category']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}
			
			if ($this->loginstate->get_access()['category']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';
			
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

	public function product_category_disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `sys_product_category` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function product_category_delete_modal_confirm($delete_id){
		$sql = "UPDATE `sys_product_category` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_product_category_data($edit_id){
		$sql = "SELECT *
				FROM sys_product_category 
				WHERE id = ?";

		$data = array($edit_id);

		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function product_category_update_modal_confirm($id, $category, $name, $onmenu, $priority){
		$sql = "UPDATE `sys_product_category` SET `category_code` = ?, `category_name` = ?, `on_menu` = ?, `priority` = ?, `updated` = ? WHERE `id` = ?";

		$data = array($category, $name, $onmenu, $priority, date('Y-m-d H:i:s'), $id);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function product_category_add_modal_confirm($category, $name, $onmenu, $priority){
		$sql = "INSERT INTO `sys_product_category` (`category_code`, `category_name`, `on_menu`, `priority`, `created`, `updated`, `status`)
				VALUES (?, ?, ?, ?, ?, ?, ?)";

		$data = array($category, $name, $onmenu, $priority, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 1);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_productcat_code($category){
		$sql = "SELECT * FROM sys_product_category
		WHERE category_code = ? AND status <> 0";

		$data = array($category);

		return $this->db->query($sql, $data);
	}

	public function get_productcat($id){
		$sql = "SELECT * FROM sys_product_category
		WHERE id = ? AND status <> 0";

		$data = array($id);

		return $this->db->query($sql, $data);
	}

	public function shipping_partner_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status  = $filters['_record_status'];
		$_code  = $filters['code'];
		$_name  = $filters['name'];

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'shipping_code',
			1 => 'name',
			2 => 'created'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM sys_shipping_partners";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * 
				FROM sys_shipping_partners";
		
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
		if($_code != ""){
			$sql.=" AND shipping_code LIKE '%" . $this->db->escape_like_str($_code) . "%' ";
		}
		if($_name != ""){
			$sql.=" AND name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		
		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		if (!$exportable) {
			$sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		}

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["shipping_code"];
			$nestedData[] = $row["name"];
			$nestedData[] = $row["created"];
				
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

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['payment_type']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['payment_type']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_name="'.$row['name'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}
			
			if ($this->loginstate->get_access()['payment_type']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-record_name="'.$row['name'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';
			
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

	public function shipping_partner_disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `sys_shipping_partners` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function shipping_partner_delete_modal_confirm($delete_id){
		$sql = "UPDATE `sys_shipping_partners` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_shipping_partner_data($edit_id){
		$sql = "SELECT *
				FROM sys_shipping_partners 
				WHERE id = ?";

		$data = array($edit_id);

		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function shipping_partner_update_modal_confirm($id, $code, $name, $api_isset, $dev_api_url, $test_api_url, $prod_api_url){
		$sql = "UPDATE `sys_shipping_partners` SET `shipping_code` = ?, `name` = ?, `api_isset` = ?, `dev_api_url` = ?, `test_api_url` = ?, `prod_api_url` = ?, `updated` = ? WHERE `id` = ?";

		$data = array(
			$code, 
			$name, 
			$api_isset,
			$dev_api_url,
			$test_api_url,
			$prod_api_url,
			date('Y-m-d H:i:s'), 
			$id
		);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function shipping_partner_add_modal_confirm($code, $name, $api_isset, $dev_api_url, $test_api_url, $prod_api_url){
		$sql = "INSERT INTO `sys_shipping_partners` (`shipping_code`, `name`, `api_isset`, `dev_api_url`, `test_api_url`, `prod_api_url`, `created`, `updated`, `status`)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$data = array(
			$code, 
			$name, 
			$api_isset, 
			$dev_api_url, 
			$test_api_url, 
			$prod_api_url, 
			date('Y-m-d H:i:s'), 
			date('Y-m-d H:i:s'), 
			1
		);

		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_shopbranch($shop_id){
		$sql = "SELECT * FROM sys_branch_profile 
				WHERE id IN (SELECT branchid FROM sys_branch_mainshop WHERE mainshopid = ? AND status = 1) 
				AND status = 1";
		$data = array($shop_id);
		$query = $this->db->query($sql, $data);

		return $query;
	}

	function get_all_sub_category_list(){
    	$sql="SELECT * FROM sys_product_category 
              WHERE status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

	public function product_main_category_list($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status  = $filters['_record_status'];
		$_category  = $filters['_category'];
		$_name  = $filters['_name'];
		$_onmenu  = $filters['_onmenu'];
		$_priority  = $filters['_priority'];

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'parent_category_code',
			1 => 'parent_category_name',
			2 => 'on_menu',
			3 => 'priority',
			4 => 'updated'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM sys_product_main_categories";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * 
			FROM sys_product_main_categories";
		
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
		if($_category != ""){
			$sql.=" AND parent_category_code LIKE '%" . $this->db->escape_like_str($_category) . "%' ";
		}
		if($_name != ""){
			$sql.=" AND parent_category_name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_onmenu != ""){
			$sql.=" AND on_menu = '" . $this->db->escape_like_str($_onmenu) . "' ";
		}
		if($_priority != ""){
			$sql.=" AND priority = '" . $this->db->escape_like_str($_priority) . "' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/img/main_category/'.$row['parent_img'].'">';
			$nestedData[] = $row["parent_category_code"];
			$nestedData[] = $row["parent_category_name"];

			if($row["on_menu"]==1){
				$nestedData[] = 'Displayed';
			} else{
				$nestedData[] = 'Not Displayed';
			}
				
			$nestedData[] = $row["priority"];
			$nestedData[] = $row["updated"];
				
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

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['products_main_category']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['products_main_category']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}
			
			if ($this->loginstate->get_access()['products_main_category']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';
			
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

	public function save_product_main_category($add_code, $add_name, $add_icon, $add_onmenu, $add_priority, $subcategory, $file_name) {
		$sql = "INSERT into sys_product_main_categories (`parent_category_code`, `parent_category_name`, `parent_icon`, `parent_img`, `sub_category_id`,  `on_menu`,  `priority`, `created`, `updated`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ";
		$data = array($add_code, $add_name, $add_icon, $file_name, $subcategory, $add_onmenu, $add_priority, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
		$this->db->query($sql, $data);
	}

	public function update_product_main_category($edit_code, $edit_name, $edit_icon, $edit_onmenu, $edit_priority, $subcategory, $edit_id, $file_name) {		
		$sql = "UPDATE sys_product_main_categories SET parent_category_code = ?, parent_category_name = ?, parent_icon = ?, parent_img = ?, sub_category_id = ?, on_menu = ?, priority = ?, updated = ? WHERE id = ? ";
		$data = array($edit_code, $edit_name, $edit_icon, $file_name, $subcategory, $edit_onmenu, $edit_priority, date('Y-m-d H:i:s'), $edit_id);
		$this->db->query($sql, $data);
	}


	function name_category_is_exist($name){
        $sql="SELECT COUNT(*) as count FROM sys_product_main_categories WHERE UPPER(parent_category_name) = ? AND status = ?";
    	$data = array(strtoupper($name), 1);
    	return $this->db->query($sql, $data)->row()->count;
    }

    function name_category_is_exist_update($name,$id){
        $sql="SELECT COUNT(*) as count FROM sys_product_main_categories WHERE UPPER(parent_category_name) = ? AND status = ? AND id != ?";
    	$data = array(strtoupper($name), 1, $id);
    	return $this->db->query($sql, $data)->row()->count;
    }

    public function get_product_main_category_data($edit_id){
		$sql = "SELECT *
				FROM sys_product_main_categories 
				WHERE id = ?";

		$data = array($edit_id);

		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function product_main_category_delete_modal_confirm($delete_id){
		$sql = "UPDATE `sys_product_main_categories` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function product_main_category_disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `sys_product_main_categories` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_cp_content_navigation($cn_name)
	{
		$sql = "SELECT * FROM cp_content_navigation where cn_name = ?";
		return $this->db->query($sql, [$cn_name])->row_array();
	}

		
}
