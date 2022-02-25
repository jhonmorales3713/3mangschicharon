<?php 
class Model_maitenance_page extends CI_Model {

    public function __construct(){
        parent::__construct();  
    }    

 
    public function client_information_table(){
        //switch db to core
        $this->db = $this->load->database('core', TRUE);

		// storing  request (ie, get/post) global array to a variable  
		$_record_status = $this->input->post('_record_status');
		$c_name 			= $this->input->post('c_name');
		$c_initial 			= $this->input->post('c_initial');
		$token_session 		= $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'c_name',
			1 => 'c_initial',
			2 => 'id_key'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM cs_clients_info ";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * FROM cs_clients_info ";

		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE enabled = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE enabled = 2 ";
		}else{
			$sql.=" WHERE enabled > 0 ";
		}
	
		if($c_name != ""){
			$sql.=" AND c_name LIKE '%" . $this->db->escape_like_str($c_name) . "%' ";
		}
		if($c_initial != ""){
			$sql.=" AND c_initial LIKE '%" . $this->db->escape_like_str($c_initial) . "%' ";
		}

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["c_name"];
			$nestedData[] = $row["c_initial"];
			$nestedData[] = $row["id_key"];

		
			$buttons = "";
		
				$buttons .= '
				<a class="dropdown-item" data-value="'.$row['c_id'].'" href="'.base_url('Dev_settings_maintenance_page/update_maintenance_page/'.$token.'/'.$row['c_id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
	

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


 public function get_client_info_data($c_id)
 {

	$this->db = $this->load->database('core', TRUE);
	$sql = "SELECT * FROM cs_clients_info WHERE c_id = '$c_id' ";
	return $this->db->query($sql);

 }

 public function client_info_update_data($csc_local, $csc_test, $csc_live, $csc_local_pass, $csc_test_pass, $csc_live_pass, $c_id){
	$this->db = $this->load->database('core', TRUE);
    
                // die($c_id);

				$sql = "UPDATE cs_clients_info  SET  `c_with_comingsoon_cover_local` = ?, `c_with_comingsoon_cover_test` = ?, `c_with_comingsoon_cover_live` = ? , `c_comingsoon_password_local` = ? , `c_comingsoon_password_test` = ?, `c_comingsoon_password_live` = ? WHERE `c_id` = ?";

				$data = array( $csc_local, $csc_test, $csc_live, $csc_local_pass, $csc_test_pass, $csc_live_pass, $c_id);

		
		
			if ($this->db->query($sql, $data) ) {
			return 1;
			}else{
			return 0;
			}

		}


}
