<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_prepayment extends CI_Model {
  public function get_prepayment_table($search, $requestData, $exportable = false){
    $columns = array(
      0 => 'refnum',
      1 => 'shopname',
      2 => 'branchname',
      3 => 'updated_at',
      4 => 'balance'
    );

    $sql = "SELECT a.*, b.shopname, a.balance, b.prepayment, b.threshold_amt,
      c.branchname, c.branchcode, c.contactperson, c.mobileno, c.email
      FROM sys_shops_wallet a
      INNER JOIN sys_shops b ON a.shopid = b.id
      LEFT JOIN sys_branch_profile c ON a.branchid = c.id AND c.status = 1
      WHERE a.enabled = 1 AND b.status = 1";

    if($search->search != ""){
      // $sql .= $search;
    }

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND a.shopid = $shopid";
    }

    if($search->branch != ''){
      $branchid = $this->db->escape($search->branch);
      $sql .= " AND a.branchid = $branchid";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND DATE(a.updated_at) BETWEEN $from AND $to";
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
      $total_deduction = $this->model_prepayment->get_total_deduction($row['shopid'],$row['branchid']);
      $branchname = ($row['branchname'] != "" || $row['branchname'] != null) ? $row['branchname'] : 'Main';
      $red = ($row['prepayment'] == 1 && (float)$row['threshold_amt'] > (float)$row['balance']) ? 'text-danger' : '';
      $nestedData[] = (!$exportable) ? '<span class="'.$red.'">'.$row['refnum'].'</span>':$row['refnum'];
      $nestedData[] = (!$exportable) ? '<span class="'.$red.'">'.$row['shopname'].'</span>' : $row['shopname'];
      $nestedData[] = (!$exportable) ? '<span class="'.$red.'">'.$branchname.'</span>' : $branchname;
      $nestedData[] = (!$exportable) ? '<span class="'.$red.'">'.$row['updated_at'].'</span>' : $row['updated_at'];
      $nestedData[] = (!$exportable) ? '<span class="float-right '.$red.'">'.number_format($row['balance'],2).'</span>':number_format($row['balance'],2);
      // $nestedData[] = (!$exportable) ? '<textarea disabled readonly class = "form-control '.$red.'" rows = "2" cols = "30">'.$row['remarks'].'</textarea>':$row['remarks'];
      $nestedData[] = (!$exportable) ?
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view_logs"
              data-vid = "'.en_dec('en',$row['id']).'"
              data-ref_num = "'.$row['refnum'].'"
              data-shopid = "'.en_dec('en',$row['shopid']).'"
              data-branchid = "'.en_dec('en',$row['branchid']).'"
              data-shopname = "'.$row['shopname'].'"
              data-branchname = "'.$row['branchname'].'"
              data-balance = "'.number_format($row['balance'],2).'"
              data-total_sales = "'.number_format($total_deduction,2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View logs
            </a>
			  	</div>
  			</div>
      ':'';
      $nestedData[] =
        '


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

  public function get_shop_wallet_logs_table($search,$id,$branchid,$exportable = false){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);
    $branchid = $this->db->escape($branchid);
    $columns = array(
      0 => 'attachment',
      1 => 'deposit_ref_num',
      2 => 'deposit_type',
      3 => 'tran_ref_num',
      4 => 'created_at',
      5 => 'type',
      6 => 'remarks',
      7 => 'amount',
      8 => 'balance'
    );

    $sql = "SELECT a.*,
      @deposit_type := (SELECT description FROM sys_payment_type WHERE status = 1 AND paycode = a.logs_type) as deposit_type
      FROM sys_shops_wallet_logs a
      WHERE a.shopid = $id AND a.branchid = $branchid AND a.enabled = 1";

    if($search->search != ''){
      $keyword = $this->db->escape($search->search);
      $sql.= " AND (a.tran_ref_num = $keyword OR a.refnum = $keyword)";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND DATE(a.created_at) BETWEEN $from AND $to";
      // return $sql;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    // $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    if (!$exportable) {
      $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      if($row['attachment'] != ""){
        $img_row = '<div class="img-thumbnail d-inline-block time_img mr-1" style = "min-height:50px;" data-url = "'.$row['attachment'].'"><img src="'.get_s3_imgpath_upload().$row['attachment'].'" alt="" height = "50" width = "40"/></div>';
      }else{
        $img_row = '---';
      }

      $deposit_type = ($row['deposit_type'] != null || $row['deposit_type'] != "") ? $row['deposit_type'] : "---";
      $deposit_ref_no = ($row['deposit_ref_num'] != null || $row['deposit_ref_num'] != "") ? $row['deposit_ref_num'] : "---";
      $type = ($row['type'] == 'plus') ? '+' : '-';
      $transaction_type = ($row['type'] == 'plus')
      ? 'Pre-Payment Reload ('.ucfirst(str_replace('_',' ',$row['logs_type'])).')'
      : 'Sales Billing <span class = "d-block">(<a href = "'.base_url('billing/index/'.en_dec('en',$this->session->token_session)).'/'.$row['refnum'].'"><u>'.$row['refnum'].'</u></a>)</span>';
      if(!$exportable){
        $nestedData[] = '<center>'.$img_row.'</center>';
      }
      $nestedData[] = (!$exportable) ? '<center>'.$deposit_ref_no.'</center>' : $deposit_ref_no;
      $nestedData[] = (!$exportable) ? '<center>'.$deposit_type.'</center>' : $deposit_type;
      $nestedData[] = (!$exportable) ? ($row['tran_ref_num'] == "") ? '<center>---</center>' : '<center>'.$row['tran_ref_num'].'</center>' : $row['tran_ref_num'];
      $nestedData[] = (!$exportable) ? $row['created_at'] : $row['created_at'];
      $nestedData[] = (!$exportable) ? $transaction_type : $transaction_type;
      $nestedData[] = (!$exportable) ? '<textarea cols="30" rows="2" class = "form-control">'.$row['remarks'].'</textarea>' : $row['remarks'];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.$type.' '.number_format($row['amount'],2).'</span>' : $type." ".$row['amount'];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.number_format($row['balance'],2).'</span>' : $row['balance'];
      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_shop_wallet_logs_table_export($search,$id,$branchid){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);
    $branchid = $this->db->escape($branchid);
    $columns = array(
      0 => 'attachment',
      1 => 'deposit_ref_num',
      2 => 'deposit_type',
      3 => 'tran_ref_num',
      4 => 'created_at',
      5 => 'type',
      6 => 'remarks',
      7 => 'amount',
      8 => 'balance'
    );

    $sql = "SELECT a.*,
      @deposit_type := (SELECT description FROM sys_payment_type WHERE status = 1 AND paycode = a.logs_type) as deposit_type
      FROM sys_shops_wallet_logs a
      WHERE a.shopid = $id AND a.branchid = $branchid AND a.enabled = 1";

    if($search->search != ''){
      $keyword = $this->db->escape($search->search);
      $sql.= " AND (a.tran_ref_num = $keyword OR a.refnum = $keyword)";
    }

    if($search->from != '' && $search->to != ''){
      $from = new Datetime($search->from);
      $from = $this->db->escape($from->format('Y-m-d'));
      $to = new Datetime($search->to);
      $to = $this->db->escape($to->format('Y-m-d'));
      $sql .= " AND DATE(a.created_at) BETWEEN $from AND $to";
      // return $sql;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql .= " ORDER BY created_at DESC";
    // $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    // $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    // if (!$exportable) {
    //   $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    // }

    $query = $this->db->query($sql);
    // return $this->db->last_query();

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $type = ($row['type'] == 'plus') ? '+' : '-';
      $transaction_type = ($row['type'] == 'plus')
      ? 'Pre-Payment Reload ('.ucfirst(str_replace('_',' ',$row['logs_type'])).')'
      : 'Sales Billing ('.$row['refnum'].')';

      $nestedData['tran_depnum']  = $row['deposit_ref_num'];
      $nestedData['tran_deptype'] = $row['deposit_type'];
      $nestedData['tran_num']     = $row['tran_ref_num'];
      $nestedData['tran_date']    = $row['created_at'];
      $nestedData['tran_type']    = $transaction_type;
      $nestedData['tran_remarks'] = $row['remarks'];
      $nestedData['tran_amount']  = number_format($row['amount'],2);
      $nestedData['tran_balance'] = number_format($row['balance'],2);

      $data[] = $nestedData;
    }

    return $data;
  }

  public function get_shop_options() {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    return $this->db->query($query)->result_array();
  }

  public function get_shop_wallet($shopid,$branchid = false){
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT * FROM sys_shops_wallet WHERE enabled = 1 AND shopid = $shopid";
    if($branchid){
      $sql .= " AND branchid = $branchid";
    }else{
      $sql .= " AND branchid = 0";
    }
    return $this->db->query($sql);
  }

  public function get_allshop_wallet(){
    $sql = "SELECT a.*, b.threshold_amt, b.shopname, b.email as merchant_email,
      c.branchname, c.email as branch_merchant_email
      FROM sys_shops_wallet a
      INNER JOIN sys_shops b ON a.shopid = b.id AND b.prepayment = 1
      LEFT JOIN sys_branch_profile c ON a.branchid = c.id
      WHERE a.enabled = 1 AND b.status = 1";
    return $this->db->query($sql);
  }

  public function get_billing_for_shop($shopid,$trandate,$branchid = false){
    $shopid = $this->db->escape($shopid);
    $trandate = $this->db->escape($trandate);
    $sql = "SELECT *, ROUND((delivery_amount + netamount),2) as total_deduct FROM sys_billing WHERE syshop = $shopid
      AND status = 1 AND paystatus = 'On Process' AND DATE(trandate) = $trandate";
    if($branchid){
      $branchid = $this->db->escape($branchid);
      $sql .= " AND branch_id = $branchid";
    }else{
      $sql .= " AND branch_id = 0";
    }
    return $this->db->query($sql);
    // return $this->db->last_query();
  }

  public function get_payment_type(){
    $sql = "SELECT * FROM sys_payment_type WHERE status = 1 ORDER BY description ASC";
    return $this->db->query($sql);
  }

  public function get_wallet_log_tran_refno($refno){
    $refno = $this->db->escape($refno);
    $sql = "SELECT tran_ref_num FROM sys_shops_wallet_logs WHERE tran_ref_num = $refno AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_wallet_logs_thru_refnum($billcode){
    $billcode = $this->db->escape($billcode);
    $sql = "SELECT refnum FROM sys_shops_wallet_logs WHERE refnum = $billcode AND enabled = 1";
    return $this->db->query($sql);
  }

  public function get_shop_branches($shopid = false){
    $sql = "SELECT b.branchname, b.branchcode, b.contactperson, b.mobileno,
      b.email, b.id as branchid
      FROM sys_branch_mainshop a
      INNER JOIN sys_branch_profile b ON a.branchid = b.id
      WHERE a.status = 1 AND b.status = 1";

    if($shopid){
      $shopid = $this->db->escape($shopid);
      $sql .= " AND a.mainshopid = $shopid";
    }

    $sql .= " ORDER BY b.branchname ASC";
    return $this->db->query($sql);
  }

  public function get_branch($branchid = false){
    $sql = "SELECT branchname FROM sys_branch_profile WHERE status = 1";
    if($branchid){
      $branchid = $this->db->escape($branchid);
      $sql .= " AND id = $branchid";
    }

    return $this->db->query($sql);
  }

  public function get_total_balance($shopid,$branchid){
    $shopid = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $total_deduct = 0;
    $sql = "SELECT
      ROUND(SUM((CASE WHEN type = 'minus' THEN CONCAT('-',amount) ELSE amount END)),2) as total_balance
      FROM sys_shops_wallet_logs WHERE shopid = $shopid AND branchid = $branchid AND enabled = 1";
    $query = $this->db->query($sql);
    if($query->num_rows() > 0){
      $total_deduct = $query->row()->total_balance;
    }

    return $total_deduct;
  }

  public function get_total_deduction($shopid,$branchid){
    $shopid = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $total_deduction = 0;
    $sql = "SELECT SUM(amount) as total_deduction
      FROM sys_shops_wallet_logs WHERE type = 'minus'
      AND enabled = 1 AND shopid = $shopid AND branchid = $branchid";
    $query = $this->db->query($sql);
    if($query->num_rows() > 0){
      $total_deduction = $query->row()->total_deduction;
    }

    return $total_deduction;
  }

  public function get_total_deposit($shopid,$branchid){
    $shopid = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $total_deposit = 0;
    $sql = "SELECT SUM(amount) as total_deposit
      FROM sys_shops_wallet_logs WHERE type = 'plus'
      AND enabled = 1 AND shopid = $shopid AND branchid = $branchid";
    $query = $this->db->query($sql);
    if($query->num_rows() > 0){
      $total_deposit = $query->row()->total_deposit;
    }

    return $total_deposit;
  }

  public function set_wallet($data){
    $this->db->insert('sys_shops_wallet',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_wallet_logs($data){
    $this->db->insert('sys_shops_wallet_logs',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function set_wallet_logs_batch($data){
    $this->db->insert_batch('sys_shops_wallet_logs',$data);
  }

  public function update_wallet_balance($amount,$id,$branchid = false){
    $sql = "UPDATE sys_shops_wallet SET balance =  ROUND(((SELECT SUM((CASE WHEN type = 'minus' THEN CONCAT('-',amount) ELSE amount END)) FROM sys_shops_wallet_logs WHERE shopid = ? AND branchid = ? AND enabled = 1) + ?),2)
    WHERE shopid = ? AND enabled = 1";
    if($branchid){
      $data = array($id,$branchid,$amount,$id,$branchid);
      $sql .= " AND branchid = ?";
    }else{
      $sql .= " AND branchid = 0";
      $data = array($id,0,$amount,$id);
    }

    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_wallet_balance_deduct($amount,$shopid,$branchid = false){
    $sql = "UPDATE sys_shops_wallet SET balance = ROUND((balance - ?),2) WHERE shopid = ? AND enabled = 1";
    if($branchid){
      $sql .= " AND branchid = ?";
      $data = array($amount,$shopid,$branchid);
    }else{
      $sql .= " AND branchid = 0";
      $data = array($amount,$shopid);
    }
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_billings_batch($data){
    $this->db->update_batch('sys_billing',$data,'id');
  }

  public function update_wallet_remarks($shop,$branch,$remarks){
    $shop = $this->db->escape($shop);
    $branch = $this->db->escape($branch);
    $remarks = $this->db->escape($remarks);
    $sql = "UPDATE sys_shops_wallet SET remarks = CONCAT(remarks,' ',$remarks) WHERE shopid = $shop AND branchid = $branch AND enabled = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
