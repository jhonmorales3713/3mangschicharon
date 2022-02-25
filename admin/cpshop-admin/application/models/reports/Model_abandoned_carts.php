<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_abandoned_carts extends CI_Model {

  public function __construct()
  {
      parent::__construct();
      $this->db2 = $this->load->database('reports', TRUE);
  }

  public function get_shop_options($id = false) {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    if($id){
      $id = $this->db2->escape($id);
      $query .= " AND id = $id";
    }
    return $this->db2->query($query)->result_array();
  }

  public function get_atc_reports_data($fromdate,$todate,$shopid = "all", $requestData, $exportable = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);
    // get added to cart
    $columns = [
      'date_created','atc','sessions','abandoned',
    ];
    $add = "";

    $oscrr_view = "SELECT session_id, date_created, 
                  CASE WHEN SUM(atc) > 0 THEN 1 ELSE 0 END AS 'atc', 
                  CASE WHEN SUM(rc) > 0 THEN 1 ELSE 0 END AS 'rc', 
                  CASE WHEN SUM(ptp) > 0 THEN 1 ELSE 0 END AS 'ptp' 
                  FROM sys_conversion_rate 
                  GROUP BY `session_id`";

    $oscrr_byshop_view = "SELECT session_id, date_created, sys_shop, 
                          CASE WHEN SUM(atc) > 0 THEN 1 ELSE 0 END AS 'atc', 
                          CASE WHEN SUM(rc) > 0 THEN 1 ELSE 0 END AS 'rc', 
                          CASE WHEN SUM(ptp) > 0 THEN 1 ELSE 0 END AS 'ptp' 
                          FROM sys_conversion_rate 
                          GROUP BY session_id, sys_shop";

    $sql = "SELECT 
                DATE(date_created) AS id, 
                DATE(date_created) AS date_created, 
                SUM(atc) AS atc
            FROM (".$oscrr_view.") a WHERE DATE(date_created) BETWEEN $fromdate AND $todate GROUP BY DATE(date_created)";
    if ($shopid > 0) {
      $sql = "SELECT 
                  CONCAT(sys_shop, '.', DATE(date_created)) as id,
                  DATE(date_created) AS date_created, 
                  SUM(atc) AS atc
              FROM (".$oscrr_byshop_view.") a WHERE DATE(date_created) BETWEEN $fromdate AND $todate AND sys_shop = $shopid GROUP BY DATE(date_created)";
    }

    // echo $sql;
    // exit();
    $res = $this->db2->query($sql);
    $totalData = $res->num_rows();
    $totalFiltered = $totalData;
    $total_count = $totalData;

    $temp_res = $res->result_array();
    $ids = implode("','", array_column($temp_res, 'id')); $sold_arr = [];
    if ($shopid > 0) {
      $sold_arr = $this->get_abandonedByDateAndShop($ids);
    } else {
      $sold_arr = $this->get_abandonedByDate($ids);
    }

    $data = [];
    foreach ($res->result_array() as $key => $value) {
      $sold_index = array_search($value['id'], $sold_arr);
      $value['sessions'] = 0; $value['abandoned'] = $value['atc'];
      if (isset($sold_arr[$sold_index])) {
        $value['sessions'] = $sold_arr[$sold_index]['total_sessions'];
        $abandoned = $value['atc'] - $sold_arr[$sold_index]['total_sessions'];
        $value['abandoned'] = ($abandoned < 0) ? 0:$abandoned;
      //  $value['abandoned'] = 0;
      }

      $data[] = [
        'date_created' => $value['date_created'],
        'atc' =>(empty($value['atc']) !== FALSE) ? '0' : $value['atc'], 
        'sessions' =>(empty($value['sessions']) !== FALSE) ? '0' : $value['sessions'], 
        'abandoned' => (empty($value['abandoned']) !== FALSE) ? '0' : $value['abandoned'],
      ];
    }

    $atc = array_sum(array_column($data, 'atc'));
    $ss = array_sum(array_column($data, 'sessions'));
    $abc = array_sum(array_column($data, 'abandoned'));

    $col = $columns[$requestData['order'][0]['column']];
    $dir = $requestData['order'][0]['dir'];
    uasort($data, build_sorter($col, $dir));
    if (!$exportable) {
      $data = array_slice($data, $requestData['start'], $requestData['length']);
    }

    return [
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      'data' => array_chunk(array_flatten($data), 4),
      'tfoot' => [
        $atc, $ss, $abc
      ]
    ];
  }

  public function get_atc_reports_chart($fromdate, $todate, $shopid)
  {
    $abandoned = createDateInterval($fromdate,$todate,'P1D','Y-m-d');
    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);

    $oscrr_view = "SELECT session_id, date_created, 
                  CASE WHEN SUM(atc) > 0 THEN 1 ELSE 0 END AS 'atc', 
                  CASE WHEN SUM(rc) > 0 THEN 1 ELSE 0 END AS 'rc', 
                  CASE WHEN SUM(ptp) > 0 THEN 1 ELSE 0 END AS 'ptp' 
                  FROM sys_conversion_rate 
                  GROUP BY `session_id`";

    $oscrr_byshop_view = "SELECT session_id, date_created, sys_shop, 
                          CASE WHEN SUM(atc) > 0 THEN 1 ELSE 0 END AS 'atc', 
                          CASE WHEN SUM(rc) > 0 THEN 1 ELSE 0 END AS 'rc', 
                          CASE WHEN SUM(ptp) > 0 THEN 1 ELSE 0 END AS 'ptp' 
                          FROM sys_conversion_rate";
    
    $sql = "SELECT 
              date_format(date_created, '%Y-%m-%d') as id, 
              date_format(date_created, '%Y-%m-%d') as date_created, 
              sum(atc) as abandoned
            from (".$oscrr_view.")  a
            WHERE DATE_FORMAT(date_created, '%Y-%m-%d') between $fromdate and $todate GROUP BY DATE(date_created)";
    if ($shopid > 0) {
    $sql = "SELECT 
              CONCAT(sys_shop, '.', DATE(date_created)) AS id,
              date_format(date_created, '%Y-%m-%d') as date_created, 
              sum(atc) as abandoned
            from (".$oscrr_view.") a
            WHERE DATE_FORMAT(date_created, '%Y-%m-%d') between $fromdate and $todate GROUP BY DATE(date_created) AND sys_shop = $shopid";
    }
    
    $res = $this->db2->query($sql)->result_array();
    $ids = implode("','", array_column($res, 'id')); $sold_arr = [];
    if ($shopid > 0) {
      $sold_arr = $this->get_abandonedByDateAndShop($ids);
    } else {
      $sold_arr = $this->get_abandonedByDate($ids);
    }

    data_set($abandoned, '*', ['date_created' => '', 'abandoned' => 0]);
    foreach ($res as $value) {
      $sold_index = array_search($value['id'], $sold_arr);
      if (isset($sold_arr[$sold_index])) {
        $value['abandoned'] -= $sold_arr[$sold_index]['total_sessions'];
      }

      if ($value['abandoned'] < 0) {
        $value['abandoned'] = 0;
      }
      $abandoned[$value['date_created']] = $value;
    }

    return $abandoned;
  }

  public function get_abandonedByDate($ids)
  {
    $query = "SELECT
                  date(date_ordered) as date_ordered,
                  COUNT(*) AS total_sessions
              FROM
                  app_sales_order_details
              WHERE
                  payment_status = 1 AND DATE(date_ordered) IN ('$ids')
              group by date(date_ordered)";
    
    return $this->db2->query($query)->result_array();
  }

  public function get_abandonedByDateAndShop($ids)
  {
    $query = "SELECT
                  CONCAT(sys_shop, '.', DATE(date_ordered)) as date_ordered,
                  COUNT(*) AS total_sessions
              FROM
                  app_sales_order_details
              WHERE
                  payment_status = 1 AND CONCAT(sys_shop, '.', DATE(date_ordered)) IN ('$ids')
              group by date(date_ordered)";
    
    return $this->db2->query($query)->result_array();
  }

}