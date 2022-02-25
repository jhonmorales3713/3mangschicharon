<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_reissue_voucher_request extends CI_Model {

  public function get_reissue_voucher_request_json($search){
    $requestData = $_REQUEST;
    $search = $this->db->escape($search);

    $columns = array(
      0 => 'shopname',
      1 => 'customer_name',
      2 => 'order_ref_num',
      3 => 'vrefno',
      4 => 'vcode',
      5 => 'vamount',
      6 => 'date_processed',
      7 => 'claim_status',

    );

    $sql = "SELECT a.*, b.date_processed, c.name as customer_name, b.order_ref_num,
      c.email,
      @shopname := (SELECT shopname FROM sys_shops WHERE id = a.shopid AND status = 1) as shopname
      FROM toktokmall_vouchers.v_wallet_all a
      LEFT JOIN app_order_payment b ON a.vcode = b.payment_refno AND b.payment_type = 'toktokmall'
      LEFT JOIN app_order_details c ON b.order_ref_num = c.reference_num
      WHERE a.claim_status = 2 AND
      (b.order_ref_num = (SELECT order_ref_num FROM app_order_payment WHERE payment_refno = $search AND payment_type = 'toktokmall')
      OR b.order_ref_num = $search)";

    // if($search->search != ''){
    //   $keyword = $this->db->escape($search->search);
    //   $name =$this->db->escape("%".$search->search."%");
    //
    //   $sql .= " AND (
    //     vc.use_orderref = $keyword
    //     OR vc.vrefno = $keyword
    //     OR vc.vcode = $keyword
    //     OR (SELECT CONCAT(b.first_name,' ',b.last_name)
    //       FROM sys_customer_auth a
    //       LEFT JOIN app_customers b ON a.id = b.user_id
    //       WHERE a.username = vc.username) LIKE $name
    //   )";
    // }
    //
    // if($search->shop != ''){
    //   $shopid = $this->db->escape($search->shop);
    //   $sql .= " AND shopid = $shopid";
    // }
    //
    // if($search->from != '' && $search->to != ''){
    //   $from = new Datetime($search->from);
    //   $from = $this->db->escape($from->format('Y-m-d'));
    //   $to = new Datetime($search->to);
    //   $to = $this->db->escape($to->format('Y-m-d'));
    //   $sql .= " AND DATE(vc.date_used) BETWEEN $from AND $to";
    //   // return $sql;
    // }

    $functions = json_decode($this->session->functions);
    if($functions->overall_access != 1){
      $sql .= " AND a.shopid = ".$this->db->escape($this->session->sys_shop);
    }

    $query = $this->db->query($sql);
    // return $this->db->last_query();
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['shopname'];
      $nestedData[] = ($row['customer_name'] == "") ? 'GUEST' : $row['customer_name'];
      $nestedData[] = $row['order_ref_num'];
      $nestedData[] = $row['vrefno'];
      $nestedData[] = $row['vcode'];
      $nestedData[] = '<span class="float-right">'.number_format($row['vamount'],2).'</span>';
      $nestedData[] = $row['date_processed'];
      $nestedData[] = '<label for="" class="badge badge-warning">Encoded/For Payment</label>';
      $nestedData[] =
        '
        <button class="btn btn-primary btn-sm btn_reissue p1"
          data-uid = "'.en_dec('en',$row['vrefno']).'"
          data-email = "'.$row['email'].'"
        >
          Reissue
        </button>
        ';

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

  public function check_voucher($vrefno){
    $vrefno = $this->db->escape($vrefno);
    $sql = "SELECT b.claim_status
      FROM toktokmall_vouchers.8_wallet_vouchers a
      INNER JOIN toktokmall_vouchers.v_wallet_all b ON a.vrefno = b.vrefno
      INNER JOIN toktokmall_vouchers.v_wallet_available c ON a.vrefno = c.vrefno
      WHERE b.claim_status = 2 AND b.vrefno = $vrefno";
    return $this->db->query($sql);
  }

  public function set_reissue_voucher_request($vrefno,$email){
    $vrefno = $this->db->escape($vrefno);
    $sql = "SELECT * FROM toktokmall_vouchers.v_wallet_all WHERE vrefno = $vrefno AND claim_status = 2";
    $sql1 = "UPDATE toktokmall_vouchers.8_wallet_vouchers SET claim_status = 4 WHERE vrefno = $vrefno";
    $sql2 = "UPDATE toktokmall_vouchers.v_wallet_all SET claim_status = 4 WHERE vrefno = $vrefno";
    $sql3 = "DELETE FROM toktokmall_vouchers.v_wallet_available WHERE vrefno = $vrefno";
    $row = $this->db->query($sql);
    $this->db->query($sql1);
    $this->db->query($sql2);
    $this->db->query($sql3);
    if($row->num_rows() > 0){
      $row = $row->row_array();
      $insert_data = array(
        "shopid" => $row['shopid'],
        "shopcode" => $row['shopcode'],
        "vrefno" => $row['vrefno'],
        "vcode" => $row['vcode'],
        "vamount" => $row['vamount'],
        "date_issued" => $row['date_issue'],
        "date_valid" => $row['date_valid'],
        "date_used" => $row['date_used'],
        "use_branchid" => $row['use_branchid'],
        "use_branchcode" => $row['use_branchcode'],
        "use_orderref" => $row['use_orderref'],
        "use_in" => $row['use_in'],
        "username" => $email,
        "others" => $row['others']
      );
      $this->db->insert('toktokmall_vouchers.v_wallet_reissue',$insert_data);
      return ($this->db->affected_rows() > 0) ? true : false;
    }
  }
}
