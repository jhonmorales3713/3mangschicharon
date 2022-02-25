<?php 
class Model_faqs extends CI_Model {

	public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

    public function faqs_table($filters, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$_record_status  = $filters['_record_status'];
		$_name      	 = $filters['_name'];
		$_shops     	 = $filters['_shops'];
	
		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'id',
			1 => 'title',
			1 => 'faqs_for',
			2 => 'content',
			3 => 'arrangment',
			4 => 'status'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count 
				FROM sys_faqs";

		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * FROM sys_faqs ";
		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE status = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE status = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE status > 0 ";
		}
		// end - for default search

		// if($_name != ""){
		// 	$sql.=" AND a.role_name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		// }


		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["title_field"];
			$nestedData[] = $row["faqs_for"];
			$nestedData[] = $row["content_field"];
			$nestedData[] = $row["faqs_arrangement"];

			if ($row['status'] == 1) {
				$record_status = 'Disable';
				$status        = 'Active';
				$rec_icon      = 'fa-ban';
			}else if ($row['status'] == 2) {
				$record_status = 'Enable';
				$rec_icon      = 'fa-check-circle';
				$status        = 'Inactive';
			}else{
				$record_status = 'Disable';
				$status        = 'Active';
				$rec_icon      = 'fa-ban';
			}

			$nestedData[] = $status;

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['faqs']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['faqs']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-name="'.$row['title_field'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}
			
			if ($this->loginstate->get_access()['faqs']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-name="'.$row['title_field'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
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

	public function add_faqs($faqs_arrangement, $faqs_title, $faqs_content, $faqs_for){
		$sql = "INSERT INTO sys_faqs (`faqs_arrangement`, `title_field`, `content_field`, `date_created`, `status`, `faqs_for`) VALUES (?,?,?,?,?,?) ";

		$bind_data = array(
			$faqs_arrangement,
			$faqs_title,
			$faqs_content,
			date('Y-m-d H:i:s'),
			1,
			$faqs_for
		);
				
		if ($this->db->query($sql, $bind_data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_faqs_data($edit_id){
		$sql = "SELECT *
				FROM sys_faqs
				WHERE id = ?";

		$data = array($edit_id);

		if ($this->db->query($sql, $data)) {
			return $this->db->query($sql, $data);
		}else{
			return 0;
		}
	}

	public function get_faqs_merchant(){
		$sql = "SELECT *
				FROM sys_faqs
				WHERE status = 1 AND faqs_for='Merchant' ORDER by faqs_arrangement ASC";

		return $this->db->query($sql)->result_array();
	}

	public function update_faqs($id, $edit_faqs_arrangement, $edit_faqs_title, $edit_faqs_content,$edit_faqs_for){
		$sql = "UPDATE `sys_faqs` SET `faqs_arrangement` = ?, `title_field` = ?, `content_field` = ?, `date_updated` = ?, `faqs_for`=? WHERE `id` = ?";

		$bind_data = array(
			$edit_faqs_arrangement,
			$edit_faqs_title, 
			$edit_faqs_content,
			date('Y-m-d H:i:s'), 
			$edit_faqs_for,
			$id
		);

		if ($this->db->query($sql, $bind_data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function disable_faqs($disable_id, $record_status){
		$sql = "UPDATE `sys_faqs` SET `status` = ? WHERE `id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function delete_faqs($delete_id){
		$sql = "UPDATE `sys_faqs` SET `status` = '0' WHERE `id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}
}