<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_product_orders_report extends CI_Model {
  public function get_shop_options($id = false) {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    if($id){
      $id = $this->db->escape($id);
      $query .= " AND id = $id";
    }
    return $this->db->query($query)->result_array();
  }

  public function list_table($fromdate,$todate,$shopid,$filtertype, $requestData, $exportable = false){

    $columns = array(
      0 => 'shopname',
      1 => 'product',
      2 => 'quantity'
    );

    $sql=" SELECT a.*, c.shopname, CONCAT(d.itemname,' (',d.otherinfo,') ') as product,
      SUM(b.quantity) as quantity
      FROM app_sales_order_details a
      LEFT JOIN app_sales_order_logs b ON a.id = b.order_id
      LEFT JOIN sys_shops c ON a.sys_shop = c.id
      LEFT JOIN sys_products d ON b.product_id = d.id
      WHERE a.status = 1 AND b.status = 1";

    if($fromdate != '' || $todate != ''){
      $fromdate = $this->db->escape($fromdate);
      $todate = $this->db->escape($todate);
      $sql .= " AND DATE(a.payment_date) BETWEEN $fromdate AND $todate";
    }

    if($shopid != 'all'){
      $shopid = $this->db->escape($shopid);
      $sql .= " AND a.sys_shop = $shopid";
    }

    if($filtertype != 'all'){
      switch ($filtertype) {
        case 'forprocess':
          $sql .= " AND a.order_status = 'p'";
          break;
        case 'fullfilled':
          $sql .= " AND a.order_status IN ('s','d','r','f')";
          break;
        default:
          $sql .= " AND a.order_status IN ('p','po','rp','bc','s','d','r','f')";
          break;
      }
    }

    if($this->loginstate->get_access()['seller_access'] == 1){
      $shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.sys_shop = $shop_id";
    }

    $sql .= " GROUP BY b.product_id";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    if (!$exportable) {
      $sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['shopname'];
      $nestedData[] = $row['product'];
      $nestedData[] = '<span class="float-right">'.number_format($row['quantity'],2).'</span>';

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  
    

     
              
  

    
  
}
