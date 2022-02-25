<?php 
class Model_currency extends CI_Model {

	public function create_data($file_name) 
	{
		$_add_country_name          = sanitize($this->input->post('_add_country_name'));
		$_add_currency              = sanitize($this->input->post('_add_currency'));
		$_add_currency_symbol       = sanitize($this->input->post('_add_currency_symbol'));
		$_add_country_code          = sanitize($this->input->post('_add_country_code'));
		$_add_exchangerate_php_to_n = sanitize($this->input->post('_add_exchangerate_php_to_n'));
		$_add_exchangerate_n_to_php = sanitize($this->input->post('_add_exchangerate_n_to_php'));
		$_add_from_dts 				= sanitize($this->input->post('_add_from_dts'));
		$_add_to_dts                = sanitize($this->input->post('_add_to_dts'));
		$_add_phone_prefix          = sanitize($this->input->post('_add_phone_prefix'));
		$_add_phone_limit           = sanitize($this->input->post('_add_phone_limit'));
		$_add_utc                   = sanitize($this->input->post('_add_utc'));
		$_add_arrangement           = sanitize($this->input->post('_add_arrangement'));

		$sql = "INSERT INTO app_currency (`country_name`, `currency`,`currency_symbol`, `country_code`, `exchangerate_php_to_n`, `exchangerate_n_to_php`, `from_dts`, `to_dts`, `phone_prefix`, `phone_limit`, `utc`, `filename`, `arrangement`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$bind_data = array($_add_country_name, $_add_currency, $_add_currency_symbol, $_add_country_code, $_add_exchangerate_php_to_n, $_add_exchangerate_n_to_php, $_add_from_dts, $_add_to_dts, $_add_phone_prefix, $_add_phone_limit, $_add_utc, $file_name, $_add_arrangement, 1);

		return $this->db->query($sql, $bind_data);
	}

	public function get_data($id)
	{
		$sql = "SELECT * FROM app_currency WHERE id = ?";
		$query = $this->db->query($sql, [$id]);
		return $query->result_array();
	}

	public function update_data($file_name)
	{
		$id 						 = sanitize($this->input->post('id'));
		$_edit_country_name 		 = sanitize($this->input->post('_edit_country_name'));
		$_edit_currency 			 = sanitize($this->input->post('_edit_currency'));
		$_edit_currency_symbol 	     = sanitize($this->input->post('_edit_currency_symbol'));
		$_edit_country_code 		 = sanitize($this->input->post('_edit_country_code'));
		$_edit_exchangerate_php_to_n = sanitize($this->input->post('_edit_exchangerate_php_to_n'));
		$_edit_exchangerate_n_to_php = sanitize($this->input->post('_edit_exchangerate_n_to_php'));
		$_edit_from_dts 			 = sanitize($this->input->post('_edit_from_dts'));
		$_edit_to_dts				 = sanitize($this->input->post('_edit_to_dts'));
		$_edit_phone_prefix			 = sanitize($this->input->post('_edit_phone_prefix'));
		$_edit_phone_limit			 = sanitize($this->input->post('_edit_phone_limit'));
		$_edit_utc   				 = sanitize($this->input->post('_edit_utc'));
		$_edit_arrangement			 = sanitize($this->input->post('_edit_arrangement'));

		$sql = 'UPDATE app_currency SET country_name = ?, currency = ?, currency_symbol = ?, country_code = ?, exchangerate_php_to_n = ?, exchangerate_n_to_php = ?, from_dts = ?, to_dts = ?, phone_prefix = ?, phone_limit = ?, utc = ?, filename = ?, arrangement = ? WHERE id = ?';
		$bind_data = array($_edit_country_name, $_edit_currency, $_edit_currency_symbol, $_edit_country_code, $_edit_exchangerate_php_to_n, $_edit_exchangerate_n_to_php, $_edit_from_dts, $_edit_to_dts, $_edit_phone_prefix, $_edit_phone_limit, $_edit_utc, $file_name, $_edit_arrangement, $id);
		return $this->db->query($sql, $bind_data);
	}

	public function disable_data($disable_id, $record_status)
	{
		$sql = "UPDATE app_currency SET status = ? WHERE id = ?";
		return $this->db->query($sql, [$record_status, $disable_id]);
	}

	public function delete_data($delete_id)
	{
		$sql = "UPDATE app_currency SET status = 0 WHERE id = ?";
		return $this->db->query($sql, [$delete_id]);
	}

	public function currency_table($filters, $requestData, $exportable = false)
	{
		$_record_status = $filters['_record_status'];
		$_code 			= $filters['_code'];
		$_country_name	= $filters['_country_name'];

		$columns = array(
            0 => 'country_name',
            1 => 'currency',
            2 => 'currency_symbol',
            3 => 'country_code'
		);

		$sql = "SELECT * FROM app_currency WHERE status > 0";
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 

		$sql = "SELECT * FROM app_currency WHERE 1";

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
			$sql.=" AND currency LIKE '%" . $this->db->escape_like_str($_code) . "%' ";
		}
		if ($_country_name != "") {
			$sql.=" AND country_name LIKE '%" . $this->db->escape_like_str($_country_name) . "%' ";
		}
		// end - getting records as per search parameters

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		if (!$exportable) {
			$sql .= " LIMIT ".$requestData['start']." ,".$requestData['length'];
		}
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData = array();
			$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/img/flags/'.$row['filename'].'">';
			$nestedData[] = $row['country_name'];
			$nestedData[] = $row['currency'];
			$nestedData[] = $row['exchangerate_php_to_n'];
			$nestedData[] = $row['exchangerate_n_to_php'];

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
			if ($this->loginstate->get_access()['currency']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" data-value="'.$row['id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    	<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['currency']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_name="'.$row['country_name'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    	<div class="dropdown-divider"></div>';
			}
			
			if ($this->loginstate->get_access()['currency']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-record_name="'.$row['country_name'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
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