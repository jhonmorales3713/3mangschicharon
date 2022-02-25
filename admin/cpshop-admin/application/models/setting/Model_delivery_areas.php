<?php 
class Model_delivery_areas extends CI_Model {

	public function create_data() 
	{
		$_add_code = sanitize($this->input->post('_add_code'));
		$_add_area = sanitize($this->input->post('_add_area'));

		$sql = "INSERT INTO sys_delivery_areas (`code`, `name`,`created`, `updated`, `status`) VALUES (?, ?, ?, ?, ?)";
		$bind_data = array($_add_code, $_add_area, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 1);
		return $this->db->query($sql, $bind_data);
	}

	public function get_data($id)
	{
		$sql = "SELECT * FROM sys_delivery_areas WHERE id = ?";
		$query = $this->db->query($sql, [$id]);
		return $query->result_array();
	}

	public function update_data()
	{
		$id = sanitize($this->input->post('id'));
		$_edit_code = sanitize($this->input->post('_edit_code'));
		$_edit_area = sanitize($this->input->post('_edit_area'));

		$sql = 'UPDATE sys_delivery_areas SET code = ?, name = ?, updated = ? WHERE id = ?';
		$bind_data = array($_edit_code, $_edit_area, date('Y-m-d H:i:s'), $id);
		return $this->db->query($sql, $bind_data);
	}

	public function disable_data($disable_id, $record_status)
	{
		$sql = "UPDATE sys_delivery_areas SET status = ? WHERE id = ?";
		return $this->db->query($sql, [$record_status, $disable_id]);
	}

	public function delete_data($delete_id)
	{
		$sql = "UPDATE sys_delivery_areas SET status = 0 WHERE id = ?";
		return $this->db->query($sql, [$delete_id]);
	}

	public function delivery_areas_table()
	{
		$_record_status = $this->input->post('_record_status');
		$_code 			= $this->input->post('_code');
		$_delivery_area	= $this->input->post('_delivery_area');
		$_date_from 	= ($this->input->post('_date_from') != "") ? date("Y-m-d 00:00:00", strtotime($this->input->post('_date_from'))) : "";
		$_date_to 		= ($this->input->post('_date_to') != "") ? date("Y-m-d 23:59:59", strtotime($this->input->post('_date_to'))) : "";

		$requestData = $_REQUEST;
		$columns = array(
            0 => 'code',
            1 => 'name',
            2 => 'created',
            3 => 'status'
		);

		$sql = "SELECT * FROM sys_delivery_areas WHERE status > 0";
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 

		$sql = "SELECT * FROM sys_delivery_areas WHERE 1";

		// start - for default search
		if ($_record_status == 1) {
			$sql.=" AND status = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" AND status = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" AND status > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if ($_code != "") {
			$sql.=" AND code LIKE '%" . $this->db->escape_like_str($_code) . "%' ";
		}
		if ($_delivery_area != "") {
			$sql.=" AND name LIKE '%" . $this->db->escape_like_str($_delivery_area) . "%' ";
		}
		if ($_date_from != "" && $_date_to != "") {
			$sql.=" AND created BETWEEN '" . $_date_from . "' AND '" . $_date_to . "'";
		}
		// end - getting records as per search parameters

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length'];
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData = array();
			$nestedData[] = $row['code'];
			$nestedData[] = $row['name'];
			$nestedData[] = date('m-d-Y H:i:s', strtotime($row['created']));

			if ($row['status'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
				$status = "Active";
			}else if ($row['status'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
				$status = "Inactive";
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
				$status = "Active";
			}

			$nestedData[] = $status;

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['delivery_areas']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['delivery_areas']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}
			if ($this->loginstate->get_access()['delivery_areas']['delete'] == 1) {
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
}