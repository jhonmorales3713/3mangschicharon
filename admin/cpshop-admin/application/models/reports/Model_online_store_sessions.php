<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_online_store_sessions extends CI_Model {

  public function get_page_statistics_table($fromdate,$todate){
    $data = [
			'visitors'        => $this->get_visitors($fromdate,$todate),
			'p_visitors'	  => $this->get_visitors($p_fromdate,$p_todate),			
			'visitors_online' => $this->get_visitors_online($fromdate,$todate),
			'current' => array('fromdate' => $fromdate, 'todate' => $todate),
			'prev' => array('fromdate' => $p_fromdate, 'todate' => $p_todate),
			'dates' => $this->generateDates($fromdate,$todate),
			'prev_dates' => $this->generateDates($p_fromdate,$p_todate)
    ];
    return $data;
  }

  public function get_visitors($fromdate,$todate){
	$fromdate = date("Y-m-d", strtotime($fromdate));	
	$todate   = date("Y-m-d", strtotime($todate));	
	$from = $this->db->escape($fromdate.' 00:00:00');
	$to = $this->db->escape($todate.' 23:59:59');
	
	if($fromdate == $todate){	
		$sql = "SELECT count(trandate) AS visitors, DATE_FORMAT(trandate, '%Y-%m-%d %H:00') as trandate
				FROM web_pageviews use index(trandate)
				WHERE trandate BETWEEN ".$from." AND ".$to."
				GROUP BY HOUR(trandate)";
	}
	else{
		$sql = "SELECT count(trandate) AS visitors, date(trandate) AS trandate
				FROM web_pageviews use index(trandate)
				WHERE trandate BETWEEN ".$from." AND ".$to."
				GROUP BY date(trandate)";
	}
		
	$res = $this->db->query($sql);

	$r = [];
	if(is_array($res)){
		$r = $res->result_array();
	}
	return $r;
	
  }

  

  public function get_pageviews($fromdate,$todate){
    $sql = "SELECT count(*) AS bilang, date(trandate) AS trandate
				FROM web_pageviews use index(trandate)
				WHERE trandate BETWEEN ? AND ?
				GROUP BY date(trandate)";

		$data = array($fromdate,$todate);
		$res = $this->db->query($sql,$data);
		$r = $res->result_array();

		return $r;
  }

  public function get_visitors_online($fromdate,$todate){
    $todaytime = todaytime();
		$today = today();

		$current_time=time();
		$timeout = $current_time - (60);

		$sql = "SELECT count(*) AS bilang
				FROM web_pageviews use index(trandate)
				WHERE trandate BETWEEN ? AND ?
					AND timesess>=?";
		$data = array($today,$today,$timeout);
		$res = $this->db->query($sql,$data);
		$r = $res->result_array();

		return $r;
  }

  public function get_visitors_online_table($fromdate, $todate, $exportable = false){
	
	$requestData;	

	$fromdate = date("Y-m-d", strtotime($fromdate));
	$todate = date("Y-m-d", strtotime($todate));

	$fromString = $fromdate;
	$toString = $todate;
	
	$fromdate = $this->db->escape($fromdate);
	$todate = $this->db->escape($todate);

    if(!$exportable){
		$requestData = $_REQUEST;
	}
	else{
		$requestData = url_decode(json_decode($this->input->post("_search")));
	}

    $columns = array(
      0 => 'trandate',
      1 => 'visitors'
	);	
	
	if($fromdate == $todate){		
		$from = $this->db->escape($fromString.' 00:00:00');
		$to = $this->db->escape($toString.' 23:59:59');
		$sql = "SELECT DATE_FORMAT(trandate, '%Y-%m-%d %H:00') as trandate, count(trandate) as 'visitors'
				FROM web_pageviews USE INDEX(trandate)
				WHERE trandate BETWEEN ".$from." AND ".$to." GROUP BY HOUR(trandate)";		
	}
	else{
		$from = $this->db->escape($fromString.' 00:00:00');
		$to = $this->db->escape($toString.' 23:59:59');
		$sql = "SELECT date(trandate) as 'trandate', count(trandate) as 'visitors'
				FROM web_pageviews USE INDEX(trandate)
				WHERE trandate BETWEEN ".$from." AND ".$to." GROUP BY date(trandate)";	
	}
	
    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;
	$total_count = $totalData;
	
	$grandTotal = getTotalInArray($query->result_array(),'visitors');

	$sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
	
	if(!$exportable){
		$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	}	

    $query = $this->db->query($sql);

    $data = array();
    $count = 0;
    $total_visitors = 0;
    foreach( $query->result_array() as $row )
    {
			
      $nestedData=array();      
      
      $nestedData[] = $row['trandate'];
      $nestedData[] = $row['visitors'];

	  $data[] = $nestedData;
	  
	  $total_visitors += intval($row['visitors']);
    }
    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
	  "sub_total" => $total_visitors,
	  "grand_total" => number_format($grandTotal,0),
      "data"            => $data
    );

    return $json_data;
  }

  
}
