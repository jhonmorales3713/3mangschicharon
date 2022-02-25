<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_claimed_vouchers extends CI_Model {

  public function __construct()
  {
      parent::__construct();
      $this->db_vouchers = $this->load->database('vouchers', TRUE);
  }

  public function get_vouchers_claimed_json($search, $exportable = false){
    $requestData = $_REQUEST;

    //when not export
		if(!$exportable){
			// storing  request (ie, get/post) global array to a variable
			$requestData = $_REQUEST;
		}
		else{
      //on form export from controller
			$requestData 	= url_decode(json_decode($this->input->post("_search")));
		}

    $columns = array(
      0 => 'shopname',
      1 => 'customer_name',
      2 => 'use_orderref',
      3 => 'vrefno',
      4 => 'vcode',
      5 => 'vamount',
      6 => 'date_used'

    );

    $sql = "SELECT vc.*,
      @shopname := (SELECT shopname FROM sys_shops WHERE id = vc.shopid AND status = 1) as shopname,
      @customer_name := (SELECT name FROM app_order_details WHERE reference_num = vc.use_orderref) as customer_name
      FROM ".$this->db_vouchers->database.".v_wallet_claimed vc
      WHERE vc.status = 1";

    if($search->search != ''){
      $keyword = $this->db->escape($search->search);
      $name =$this->db->escape("%".$search->search."%");

      $sql .= " AND (
        vc.use_orderref = $keyword
        OR vc.vrefno = $keyword
        OR vc.vcode = $keyword
        OR (SELECT CONCAT(b.first_name,' ',b.last_name)
          FROM sys_customer_auth a
          LEFT JOIN app_customers b ON a.id = b.user_id
          WHERE a.username = vc.username) LIKE $name
      )";
    }

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND shopid = $shopid";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND DATE(vc.date_used) BETWEEN $from AND $to";
      // return $sql;
    }

    $functions = json_decode($this->session->functions);
    if($functions->overall_access != 1){
      $sql .= " AND vc.shopid = ".$this->db->escape($this->session->sys_shop);
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];

    //export
    if(!$exportable){
      $sql .=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }


    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['shopname'];
      $nestedData[] = ($row['customer_name'] == "") ? 'GUEST' : $row['customer_name'];
      $nestedData[] = $row['use_orderref'];
      $nestedData[] = $row['vrefno'];
      $nestedData[] = $row['vcode'];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.number_format($row['vamount'],2).'</span>':number_format($row['vamount'],2);
      $nestedData[] = $row['date_used'];

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_shop_options() {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    return $this->db->query($query)->result_array();
  }
}
