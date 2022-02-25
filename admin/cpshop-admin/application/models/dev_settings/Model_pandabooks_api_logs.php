<?php
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) //to ignore maximum time limit
{
    @set_time_limit(0);
}
class Model_pandabooks_api_logs extends CI_Model {
	public $app_db;

	public function pandabooks_api_logs_table($exportable = false){

		$token_session  = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$_reference_number;
		$date_from;
		$date_to;
		$requestData;

		//when not export
		if(!$exportable){
			// storing  request (ie, get/post) global array to a variable
		
			$_reference_number	    = $this->input->post('_reference_number');
			$date_from 		        = format_date_reverse_dash($this->input->post('date_from'));
			$date_to 	        	= format_date_reverse_dash($this->input->post('date_to'));	
			$requestData = $_REQUEST;
		}
		else{
			$filters = json_decode($this->input->post('_filters'));
	
			$_reference_number  = $filters->_reference_number;
			$date_from 		     = format_date_reverse_dash($filters->date_from);
			$date_to 		     = format_date_reverse_dash($filters->date_to);
			$requestData 	     = url_decode(json_decode($this->input->post("_search")));
		}		

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_created',
            1 => 'refnum',
            2 => 'response',
		);

		$sql = "SELECT * FROM api_jcww_logs
	    WHERE DATE(date_created) BETWEEN ".$this->db->escape($date_from)." AND ".$this->db->escape($date_to)."";
     

		// getting records as per search parameters
		if($_reference_number  != ""){	
			$sql.=" AND refnum = ".$this->db->escape($_reference_number)." ";
		} 

        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];

		//when exportable limit is removed
		if(!$exportable){
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		}
		

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
			$nestedData[] = $row["date_created"];
			$nestedData[] = $row["refnum"];
			$nestedData[] = (!$exportable) ? '<textarea disabled readonly class = "form-control" rows = "4" cols = "30">'.$row["response"].'</textarea>':$row["response"];
		



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
