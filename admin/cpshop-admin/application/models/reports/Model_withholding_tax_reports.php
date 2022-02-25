<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_withholding_tax_reports extends CI_Model {
  public function list_table($search, $token, $requestData, $exportable = false){

    $columns = array(
      0 => 'billing_date',
      1 => 'billing_code',
      2 => 'billing_no',
      3 => 'billing_tax'
    );


    $sql = "SELECT a.billno as billing_no, a.billcode as billing_code,
      Date(a.trandate) as billing_date, a.total_whtax as billing_tax,
      @shopanme := (SELECT shopname FROM sys_shops WHERE id = a.syshop AND status = 1) as shopname,
      @branchname := (SELECT branchname FROM sys_branch_profile WHERE id = a.branch_id AND status = 1) as branchname,
      a.syshop, a.branch_id
      FROM sys_billing a
      WHERE a.status = 1";

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND ((DATE(a.trandate) BETWEEN $from AND $to) OR (DATE(a.trandate) BETWEEN $from AND $to))";
    }

    if($search->search != ''){
      $billcode = $this->db->escape($search->search);
      $sql .= " AND a.billcode = $billcode";
    }

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND a.syshop = $shopid";
    }

    if($search->branch != ''){
      $branchid = $this->db->escape($search->branch);
      $sql .= " AND a.branch_id = $branchid";
    }

    if($this->loginstate->get_access()['seller_access'] == 1 && $this->session->sys_shop_id != ''){
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.syshop = $sys_shop_id";
    }
    //
    if(($this->loginstate->get_access()['seller_branch_access'] == 1 || $this->loginstate->get_access()['food_hub_access'] == 1) && $this->session->sys_shop_id != ''){
      $branchid = $this->db->escape($this->session->branchid);
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.branch_id = $branchid AND a.syshop = $sys_shop_id";
    }

    $query = $this->db->query($sql);
    $total_whtax = 0;
    foreach($query->result_array() as $tax){
      $total_whtax += floatval($tax['billing_tax']);
    }

    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    if (!$exportable) {
      $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }

    $query = $this->db->query($sql);

    // die($this->db->last_query());
    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $nestedData[] = readable_date($row['billing_date']);
      $nestedData[] = $row['billing_code'];
      $nestedData[] = $row['billing_no'];
      $nestedData[] = $row['shopname'];
      $nestedData[] = ($row['branchname'] == null || $row['branchname'] == "") ? 'Main' : $row['branchname'];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.number_format($row['billing_tax'],2).'</span>': $row['billing_tax'];
      // $nestedData[] = '<span class="float-right">'.number_format($row['billing_tax'],2).'</span>';

      $data[] = $nestedData;
    }

    if($exportable === false){
      $nestedData = array();
      $nestedData[] = '<strong>Total</strong>';
      $nestedData[] = '';
      $nestedData[] = '';
      $nestedData[] = '';
      $nestedData[] = '';
      $nestedData[] = '<span class="float-right"><strong>'.number_format($total_whtax,2).'</strong></span>';

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

    $query .= " ORDER BY shopname ASC";
    return $this->db->query($query)->result_array();
  }

  public function get_branches($shopid){
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT a.id, a.branchname FROM sys_branch_profile a
      INNER JOIN sys_branch_mainshop b ON a.id = b.branchid AND b.mainshopid = $shopid
      WHERE a.status = 1 AND b.status = 1 AND b.mainshopid = $shopid
      ORDER BY a.branchname ASC";
    return $this->db->query($sql);
  }

}
