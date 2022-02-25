<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_payout_report extends CI_Model {
  public function list_table($fromdate,$todate,$member_id,$requestData){

    $fromdate = $this->db->escape($fromdate);
    $todate = $this->db->escape($todate);
    $member_id = $this->db->escape($member_id);

    $columns = array(
      0 => 'transaction_date',
      1 => 'netamount',
      2 => 'payoutdate'
    );


    // $sql = "SELECT fromdate,todate,processdate,netamount,payoutdate,commcode,commno,
    //       CONCAT(fromdate,' to ',todate) as transaction_date
		// 			FROM 8_referralcomsummary
		// 			WHERE status=1 AND (Date(processdate) BETWEEN $fromdate AND $todate)
    //       AND member_id= $member_id";

    $sql = "SELECT fromdate,todate,processdate,netamount,payoutdate,commcode,commno,
          CONCAT(fromdate,' to ',todate) as transaction_date
					FROM 8_referralcomsummary
					WHERE status=1 AND (Date(processdate) BETWEEN $fromdate AND $todate)
          ";

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

      $nestedData[] = $row['transaction_date'];
      $nestedData[] = '<span class="float-right">'.number_format($row["netamount"],2,".",",").'</span>';
      $nestedData[] = ($row['payoutdate'] != '0000-00-00')
      ? 'Paid - '.$row['payoutdate'] : 'On Process';

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

  public function get_payout_report_chart($fromdate,$todate,$member_id){
    $sql = "SELECT SUM(netamount) AS netamount, processdate AS trandate
					FROM 8_referralcomsummary
					WHERE status=1 AND
						(date(processdate) BETWEEN ? AND ?) AND
						member_id=?
					GROUP BY date(processdate)";
			$data = array($fromdate,$todate,$member_id);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $data['profit_share'] = $r;
  }
}
