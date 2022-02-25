<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_profit_sharing_report extends CI_Model {
  public function list_table($fromdate,$todate,$member_id,$requestData){
    $fromdate = $this->db->escape($fromdate);
    $todate = $this->db->escape($todate);
    $member_id = $this->db->escape($member_id);

    $columns = array(
      0 => 'soldto',
      1 => 'order_reference_num',
      2 => 'totalamount',
      3 => 'compercentage',
      4 => 'netamount',
      5 => 'trandate'
    );

    $sql = "SELECT soldto, order_reference_num,totalamount, compercentage, netamount,trandate
			FROM 8_referralcomlog
      WHERE status=1 AND (DATE(trandate) BETWEEN $fromdate AND $todate)";
      
    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $total_count = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData->order[0]->column] . " " . $requestData->order[0]->dir ." LIMIT ".$requestData->start." ,".$requestData->length."   ";

    $query = $this->db->query($sql);

    $data = array();
    $count = 0;
    $total_amount = 0;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $count++;

      $nestedData[] = strtoupper($row["soldto"]);
      $nestedData[] = $row['order_reference_num'];
      $nestedData[] = $row['trandate'];
      $nestedData[] = '<span class="float-right">'.number_format($row["totalamount"],2,".",",").'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["compercentage"],2,".",",").'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["netamount"],2,".",",").'</span>';

      $data[] = $nestedData;
    }
    $json_data = array(
      "filters"         => $requestData,
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_profit_sharing_report_chart($fromdate,$todate,$member_id){
    $sql = "SELECT SUM(totalamount) AS totalamount, SUM(netamount) AS netamount, trandate
					FROM 8_referralcomlog
					WHERE status=1 AND
						(date(trandate) BETWEEN ? AND ?) AND
						member_id=?
					GROUP BY date(trandate)";
			$data = array($fromdate,$todate,$member_id);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $data['profit_share'] = $r;
  }

}
