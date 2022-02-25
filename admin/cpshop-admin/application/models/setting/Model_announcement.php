<?php 
class Model_announcement extends CI_Model {

    public function announcement_table()
	{
        $_name = $this->input->post('_name');

		$requestData = $_REQUEST;
		$columns = array(
            0 => 'c_name',
            1 => 'c_shop_main_announcement',
            2 => 'c_name',
		);

		$sql = "SELECT c_id, c_name, c_shop_main_announcement FROM cs_clients_info";
		$query = db_core()->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 
       
		$sql = "SELECT c_id, c_name, c_shop_main_announcement FROM cs_clients_info WHERE 1";

        if ($_name != '') {
			$sql.=" AND c_name = " . $this->db->escape($_name) . "";
        }
    
		$query = db_core()->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length'];
        
        $query = db_core()->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData = array();
			$nestedData[] = $row['c_name'];
			$nestedData[] = $row['c_shop_main_announcement'];

			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['announcement']['update'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_edit" data-value="'.$row['c_id'].'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                ';
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
    
    public function get_data($id)
	{
		$sql = "SELECT c_shop_main_announcement FROM cs_clients_info WHERE c_id = ?";
        $query = db_core()->query($sql, $id);
		return $query->result_array();
    }
    
    public function update_data()
	{
		$id = sanitize($this->input->post('id'));
		$_edit_announcement = sanitize($this->input->post('_edit_announcement'));

		$sql = 'UPDATE cs_clients_info SET c_shop_main_announcement = ? WHERE c_id = ?';
		$bind_data = array($_edit_announcement, $id);

        return db_core()->query($sql, $bind_data);
    }
}