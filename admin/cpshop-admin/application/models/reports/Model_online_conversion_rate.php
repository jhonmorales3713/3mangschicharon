<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_online_conversion_rate extends CI_Model {

  public function get_shop_options($id = false) {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    if($id){
      $id = $this->db->escape($id);
      $query .= " AND id = $id";
    }
    $query .= " ORDER BY shopname";
    return $this->db->query($query)->result_array();
  }

  public function get_oscr_reports_data($fromdate,$todate,$shopid = "all",$requestData = false,$exportable = false)
  {
    $columns = [
      'date_created','atc','rc','ptp', 'sessions'
    ];
    $fromdate = $this->db->escape($fromdate);
    $todate = $this->db->escape($todate);
    // get all data from conversion rate to cart
    $oscrr_view = "SELECT session_id, date_created, CASE WHEN SUM(atc) > 0 THEN 1 ELSE 0 END AS 'atc', CASE WHEN SUM(rc) > 0 THEN 1 ELSE 0 END AS 'rc', CASE WHEN SUM(ptp) > 0 THEN 1 ELSE 0 END AS 'ptp' FROM sys_conversion_rate GROUP BY `session_id`";
    $oscrr_byshop_view = "SELECT session_id, date_created, sys_shop, CASE WHEN SUM(atc) > 0 THEN 1 ELSE 0 END AS 'atc', CASE WHEN SUM(rc) > 0 THEN 1 ELSE 0 END AS 'rc', CASE WHEN SUM(ptp) > 0 THEN 1 ELSE 0 END AS 'ptp' FROM sys_conversion_rate GROUP BY session_id, sys_shop";
    $sql = "SELECT DATE(date_created) AS date_created, SUM(atc) AS atc, SUM(rc) AS rc, SUM(ptp) AS ptp, (SELECT COUNT(*) AS total_sessions FROM app_order_details WHERE payment_status = 1 AND DATE(date_ordered) = DATE(a.date_created) AND DATE(payment_date) = DATE(a.date_created)) AS `sessions` FROM (".$oscrr_view.") a WHERE DATE(date_created) BETWEEN $fromdate AND $todate GROUP BY DATE(date_created)";
    
    if ($shopid > 0) {
      $sql = "SELECT DATE(date_created) AS date_created, SUM(atc) AS atc, SUM(rc) AS rc, SUM(ptp) AS ptp, (SELECT COUNT(*) AS total_sessions FROM app_order_details WHERE payment_status = 1 AND DATE(date_ordered) = DATE(a.date_created) AND DATE(payment_date) = DATE(a.date_created) AND sys_shop = $shopid) AS `sessions` FROM (".$oscrr_byshop_view.") a WHERE DATE(date_created) BETWEEN $fromdate AND $todate GROUP BY DATE(date_created)";
    }
    // echo $sql;
    $result = $this->db->query($sql);
    $data = $result->result_array();
    // print_r($data);
    // exit();
    $totalData = count($data);
    $total_atc = array_sum(array_column($data, 'atc'));
    $total_rc = array_sum(array_column($data, 'rc'));
    $total_ptp = array_sum(array_column($data, 'ptp'));
    $total_sessions = array_sum(array_column($data, 'sessions'));
    
    $totalFiltered = $totalData;

    // process sorting and pagination
    if ($requestData) {
      $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
      if (!$exportable) {
        $sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
      }
      $result = $this->db->query($sql);
      $data = array_where($result->result_array(), function ($val) {
        $t_sessions = $val['atc'] + $val['rc'] + $val['ptp'] + $val['sessions'];
        return ($t_sessions > 0);
      });
    }
    $table_data = array_chunk(array_flatten($data), 5);
    return [
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      'data' => $table_data,
      "tfoot" => [
        $total_atc, $total_rc, $total_ptp, $total_sessions
      ]
    ];
  }

  public function get_visitors($prev_start_date, $prev_end_date, $todate)
  {
    $prev_start_date = $this->db->escape($prev_start_date);
    $prev_end_date = $this->db->escape($prev_end_date);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db->escape(date_format($todate, 'Y-m-d'));

    $sql = "SELECT if(date(trandate) BETWEEN $prev_start_date AND $prev_end_date, 'prev', 'curr') as a, date(trandate) as 'date', COUNT(*) as 'visitors' FROM web_pageviews USE INDEX(trandate) WHERE trandate BETWEEN $prev_start_date AND $todate GROUP BY a";
        // echo $sql;
        // exit();

    $query = $this->db->query($sql);

    return $query->result_array();
  }

}