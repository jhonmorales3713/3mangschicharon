<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_manual_order extends CI_Model {
  public function list_table($search, $requestData, $exportable = false){

    $columns = array(
      0 => 'shopname',
      1 => 'reference_num',
      2 => 'total_amount',
      3 => 'payment_method',
      4 => 'date_ordered',
      5 => 'date_shipped'
    );


    $sql = "SELECT a.*, b.shopname FROM app_sales_order_details a
      LEFT JOIN sys_shops b ON a.sys_shop = b.id
      WHERE a.order_status = 'f' AND b.status = 1
      AND a.payment_method = 'Prepayment' AND a.status = 1";

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND a.sys_shop = $shopid";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND ((DATE(a.date_ordered) BETWEEN $from AND $to) OR (DATE(a.date_shipped) BETWEEN $from AND $to))";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    if (!$exportable) {
      $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['shopname'];
      $nestedData[] = $row['reference_num'];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.number_format($row['total_amount'],2).'</span>':number_format($row['total_amount'],2);
      $nestedData[] = $row['payment_method'];
      $nestedData[] = $row['date_ordered'];
      $nestedData[] = $row['date_shipped'];

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

  public function get_shop($id = false) {
    $id = $this->db->escape($id);
    $query="SELECT * FROM sys_shops WHERE status = 1 AND id = $id";
    return $this->db->query($query)->row_array();
  }

  public function get_shop_w_wallet(){
    $sql = "SELECT a.id, a.shopname, a.shopcode, a.shippingfee, a.daystoship
      FROM sys_shops a INNER JOIN sys_shops_wallet b ON a.id = b.shopid
      WHERE b.balance > 0 AND b.enabled = 1 AND a.status = 1 ORDER BY a.shopname ASC";
    return $this->db->query($sql);
  }

  public function get_shop_products($shopid){
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT * FROM sys_products a
      WHERE sys_shop = $shopid AND enabled = 1 ORDER BY a.itemname ASC";
    return $this->db->query($sql);
  }

  public function get_shop_branches($shopid){
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT b.* FROM sys_branch_mainshop a
      INNER JOIN sys_branch_profile b ON a.branchid = b.id
      WHERE a.mainshopid = $shopid AND a.status = 1 AND b.status = 1
      ORDER BY branchname ASC";
    return $this->db->query($sql);
  }

  public function get_products($productid){
    $productid = $this->db->escape($productid);
    $sql = "SELECT a.no_of_stocks as quantity, CONCAT(a.itemname,' (',a.otherinfo,')') as product_name
      FROM sys_products a
      WHERE a.Id = $productid";
    return $this->db->query($sql);
  }

  public function set_app_order_details($data){
    $this->db->insert('app_order_details',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function get_cities(){
    $sql = "SELECT a.*, CONCAT(a.citymunDesc,', ',b.provDesc) as city, b.regCode
      FROM sys_citymun a
      INNER JOIN sys_prov b ON a.provCode = b.provCode
      ORDER BY a.regDesc";
    return $this->db->query($sql);
  }

  public function set_app_order_details_shipping($data){
    $this->db->insert('app_order_details_shipping',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_app_order_logs_batch($data){
    $this->db->insert_batch('app_order_logs',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_app_sales_order_details($data){
    $this->db->insert('app_sales_order_details',$data);
    return $this->db->insert_id();
  }

  public function set_app_sales_order_logs_batch($data){
    $this->db->insert_batch('app_sales_order_logs',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_branch_order($data){
    $this->db->insert('sys_branch_orders',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_sys_products_invtrans_batch($data){
    $this->db->insert_batch('sys_products_invtrans',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function so_no(){
		$this->db->select("order_id");
		$res = $this->db->get("app_order_details");
		return $res->num_rows();
	}

  public function update_prod_quantity($data){
    $this->db->update_batch('sys_products',$data,'Id');
  }

  public function update_sysproduct_invtrans_branch($data){
    $sql = '';
    // print_r($data);
    // die();
    foreach($data as $row){
      $quantity = $this->db->escape($row['no_of_stocks']);
      $product_id = $this->db->escape($row['Id']);
      $branchid = $this->db->escape($row['branchid']);
      $shopid = $this->db->escape($row['shopid']);
      $sql .= "UPDATE sys_products_invtrans_branch SET no_of_stocks = (no_of_stocks - $quantity)
        WHERE product_id = $product_id AND branchid = $branchid AND shopid = $shopid;";
    }
    $this->db->query($sql);
  }
}
