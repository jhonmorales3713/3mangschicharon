<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_sale_settlement extends CI_Model {
  public function list_table($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'billcode',
      1 => 'shopname',
      2 => 'trandate',
      3 => 'totalamount',
      4 => 'processfee',
      5 => 'netamount',
      6 => 'paystatus'
    );


    $sql=" SELECT a.*, b.shopname as shopname FROM sys_billing as a
				LEFT JOIN sys_shops as b ON a.syshop = b.id
				WHERE a.status = 1 ";

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND a.syshop = $shopid";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND ((DATE(a.trandate) BETWEEN $from AND $to) OR (DATE(a.trandate) BETWEEN $from AND $to))";
    }

    if($this->loginstate->get_access()['seller_access'] == 1){
      $shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.syshop = $shop_id";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['billcode'];
      $nestedData[] = $row['shopname'];
      $nestedData[] = $row['trandate'];
      $nestedData[] = number_format($row['totalamount'],2);
      $nestedData[] = number_format($row['processfee'],2);
      $nestedData[] = number_format($row['netamount'],2);
      $nestedData[] = ($row['paystatus'] == 'On Process') ? '<center><label for="" class="badge badge-info">On Process</label></center>' : '<center><label for="Settled" class="badge badge-success">Settled</label></center>';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_shop_options($id = false) {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    if($id){
      $id = $this->db->escape($id);
      $query .= " AND id = $id";
    }
    return $this->db->query($query)->result_array();
  }

}
