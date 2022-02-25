<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_sale_settlement2 extends CI_Model {
  public function get_billing_table($search, $requestData, $exportable = false){
    // print_r($requestData);
    // exit();
		$totalData = 0;
		$totalFiltered = 0;

		$columns = array(
            // 0 => 'Id',
            0 => 'trandate',
            1 => 'billcode',
            2 => 'totalamount',
            3 => 'processfee',
            4 => 'netamount',
            5 => 'shopname',
            6 => 'paystatus'
		);

		$sql=" SELECT a.*, b.shopname as shopname,
        @branch_name := (SELECT branchname FROM sys_branch_profile WHERE id = a.branch_id AND status = 1) as branch_name
        FROM sys_billing as a
				LEFT JOIN sys_shops as b ON a.syshop = b.id
        WHERE a.status = 1 ";

    if($search->status != 1){
      switch ($search->status) {
        case 2:
          $sql .= " AND a.paystatus = 'On Process'";
          break;
        case 3:
          $sql .= " AND a.paystatus = 'Settled'";
          break;
        default:
          // code...
          break;
      }
    }

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND syshop = $shopid";
    }

    if($search->from !== '' && $search->to !== ''){
      $from =  $this->db->escape(format_date_reverse_dash($search->from));
      $to = $this->db->escape(format_date_reverse_dash($search->to));
    }else{
      $from =  $this->db->escape(format_date_reverse_dash(date('m/d/Y')));
      $to = $this->db->escape(format_date_reverse_dash(date('m/d/Y')));
    }
    $sql .= " AND DATE(trandate) BETWEEN $from AND $to";

    if($this->loginstate->get_access()['seller_access'] == 1 && $this->session->sys_shop_id != ''){
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND syshop = $sys_shop_id";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    // print_r($requestData);
    // exit();
    $sql.=" ORDER BY " . $columns[$requestData->order[0]->column] . " " . $requestData->order[0]->dir;
    if (!$exportable) {
      $sql .= " LIMIT ".$requestData->start." ,".$requestData->length."   ";
    }

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $branch_name = ($row['branch_name'] != NULL) ? $row['branch_name'] : 'Main';
      $shopname = ($row['per_branch_billing'] == 1) ? $row['shopname']."(".$branch_name.")" : $row['shopname'];
      $total_amount = number_format(($row["totalamount"] + $row['delivery_amount']),2);
      $processfee = number_format($row["processfee"],2);
      $netamount = number_format(($row["netamount"] + $row['delivery_amount']),2);

      $nestedData[] = readable_date($row["trandate"]);
      $nestedData[] = $row["billcode"];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.$total_amount.'</span>':$total_amount;
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.$processfee.'</span>':$processfee;
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.$netamount.'</span>':$netamount;
      $nestedData[] = $shopname;

      switch ($row["paystatus"]) {
          case 'On Process':
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
          case 'Settled':
              $nestedData[] = '<center><label class="badge badge-success"> Settled</label></center>';
              break;

          default:
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
      }

      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view"
              id="'.$row['id'].'"
              data-ref_num="'.$row['billcode'].'"
              data-total_amount = "'.number_format($row["totalamount"],2).'"
              data-delivery_amount = "'.$row['delivery_amount'].'"
              data-total_amount_w_shipping = "'.number_format(($row["totalamount"] + $row['delivery_amount']),2).'"
              data-processfee = "'.$row["processfee"].'"
              data-netamount = "'.number_format($row["netamount"],2).'"
              data-netamount_w_shipping = "'.number_format(($row["netamount"] + $row['delivery_amount']),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View
            </a>
			  	</div>
  			</div>
      ';
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

  public function get_billing_government_table($search){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;

		$columns = array(
            // 0 => 'Id',
            0 => 'trandate',
            1 => 'billcode',
            2 => 'totalamount',
            3 => 'portal_fee',
            4 => 'netamount',
            5 => 'shopname',
            6 => 'paystatus'
		);

		$sql=" SELECT a.*, b.shopname as shopname FROM sys_billing_government as a
				LEFT JOIN sys_shops as b ON a.syshop = b.id
				WHERE a.status = 1 ";

    if($search->status != 1){
      switch ($search->status) {
        case 2:
          $sql .= " AND a.paystatus = 'On Process'";
          break;
        case 3:
          $sql .= " AND a.paystatus = 'Settled'";
          break;
        default:
          // code...
          break;
      }
    }

    if($search->shop != ''){
      $shopid = $this->db->escape($search->shop);
      $sql .= " AND syshop = $shopid";
    }

    if($search->from != '' && $search->to != ''){
      $from =  $this->db->escape(format_date_reverse_dash($search->from));
      $to = $this->db->escape(format_date_reverse_dash($search->to));
      $sql .= " AND DATE(trandate) BETWEEN $from AND $to";
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

      $nestedData[] = readable_date($row["trandate"]);
      $nestedData[] = $row["billcode"];
      $nestedData[] = '<span class="float-right">'.number_format($row["totalamount"] + $row['delivery_amount'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["portal_fee"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["netamount"] + $row['delivery_amount'],2).'</span>';
      $nestedData[] = $row["shopname"];

      switch ($row["paystatus"]) {
          case 'On Process':
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
          case 'Settled':
              $nestedData[] = '<center><label class="badge badge-success"> Settled</label></center>';
              break;

          default:
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
      }

      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view"
              id="'.$row['id'].'"
              data-total_amount = "'.number_format($row["totalamount"],2).'"
              data-processfee = "'.number_format($row["portal_fee"],2).'"
              data-netamount = "'.number_format($row["netamount"],2).'"
              data-delivery_amount = "'.$row['delivery_amount'].'"
              data-total_amount_w_shipping = "'.number_format(($row["totalamount"] + $row['delivery_amount']),2).'"
              data-netamount_w_shipping = "'.number_format(($row["netamount"] + $row['delivery_amount']),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View
            </a>
			  	</div>
  			</div>
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

  public function get_billing_breakdown_table($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount,$delivery_amount){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;
    $sys_shop = $shop;
    // $trandate = $this->db->escape($trandate);
    $total_processfee = $processfee;
		$columns = array(
            0 => 'trandate',
            1 => 'refnum',
            2 => 'payrefnum',
            3 => 'amount',
            4 => 'shippingfee'
		);

    // Per shop billing
    $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum,
      a.paypanda_ref as payrefnum, a.total_amount as amount, a.id as order_id,
      @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
      @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee
      FROM `app_sales_order_details` a
      WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' ";

    // Per branch billing
    if($per_branch_billing == 1){
      // Main Branch
      if($branch_id == 0){
        $sql .= " AND a.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1)";
      }

      if($branch_id != 0){
        $sql .= " AND a.reference_num IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND orderid = a.reference_num AND branchid = '".$branch_id."')";
      }

    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();
    $total_shippingfee = 0;
    $total_amount = 0;
    $total_netpay = 0;

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $shippingfee = ($row['shippingfee'] == '') ? 0 : $row['shippingfee'];
      $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
      $total_shippingfee += $shippingfee;
      $total_amount += $row["amount"];
      $total_netpay += ($row["amount"] - $row['processfee']);

      $t_date = new Datetime($row['trandate']);
      $nestedData[] = $t_date->format('M d, Y h:i:m');
      $nestedData[] = $row["refnum"];
      $nestedData[] = $row["payrefnum"];
      $nestedData[] = '<span class="float-right">'.number_format($row["amount"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($shippingfee,2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['processfee'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format(($row["amount"] - $row['processfee']) + ($shippingfee),2).'</span>';
      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view_logs"
              data-orderid = "'.en_dec('en',$row['order_id']).'"
              data-refnum = "'.$row['refnum'].'"
              data-totalamount = "'.number_format($row['amount'],2).'"
              data-processfee = "'.number_format($row['processfee'],2).'"
              data-netamount = "'.number_format(($row['amount'] - $row['processfee']),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View logs
            </a>
			  	</div>
  			</div>
      ';
      $data[] = $nestedData;
    }

    $nestedData = array();
		$nestedData[] = '<strong>Total</strong>';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '<span class="float-right"><strong>'.$totalamount.'</strong></span>';
    $nestedData[] = '<span class="float-right"><strong>'.number_format($delivery_amount,2).'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.number_format($total_processfee,2).'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
    $nestedData[] = '';
		$data[] = $nestedData;

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_logs($order_id,$totalamount,$processfee,$netamount){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;
    $order_id = $this->db->escape($order_id);
		$columns = array(
            0 => 'product_name',
            1 => 'price',
            2 => 'quantity',
            3 => 'totalamount',
            4 => 'ratetype',
            5 => 'processrate',
            6 => 'processfee',
            7 => 'netamount'
		);

		$sql=" SELECT a.*, CONCAT(b.itemname,'(',b.otherinfo,')') as product_name,
      c.shopname
      FROM sys_billing_logs a
      LEFT JOIN sys_products b ON a.product_id = b.Id
      LEFT JOIN sys_shops c ON a.sys_shop = c.id
      WHERE order_id = $order_id";


    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row["product_name"];
      $nestedData[] = '<span class="float-right">'.number_format($row["price"],2).'</span>';
      $nestedData[] = ($row['ratetype'] == 'p') ? 'Percentage' : 'Fix';
      $nestedData[] = ($row['ratetype'] == 'p') ? ($row['processrate'] * 100).' %' : '<span class="float-right">'.number_format($row["processrate"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.$row["quantity"].'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["totalamount"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["processfee"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["netamount"],2).'</span>';
      $data[] = $nestedData;
    }

    $nestedData = array();
		$nestedData[] = '<strong>Total</strong>';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';
    $nestedData[] = '<span class="float-right"><strong>'.$totalamount.'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$processfee.'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
		$data[] = $nestedData;

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_government_breakdown_table($search,$shop,$trandate,$portal_fee,$totalamount,$processfee,$netamount,$delivery_amount){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;
    $sys_shop = $shop;
    // $trandate = $this->db->escape($trandate);
    $total_processfee = $processfee;
		$columns = array(
            0 => 'trandate',
            1 => 'refnum',
            2 => 'payrefnum',
            3 => 'shippingfee',
            4 => 'amount'
		);

		$sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, a.payment_portal_fee,
      a.paypanda_ref as payrefnum, a.total_amount as amount,
      @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
      @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee
			FROM `app_sales_order_details` a
			WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' ";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();
    $total_shippingfee = 0;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $t_date = new Datetime($row['trandate']);
      $shippingfee = ($row['shippingfee'] == '') ? 0 : $row['shippingfee'];
      $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
      $total_shippingfee += $shippingfee;

      $nestedData[] = $t_date->format('M d, Y h:i:m');
      $nestedData[] = $row["refnum"];
      $nestedData[] = $row["payrefnum"];
      $nestedData[] = '<span class="float-right">'.number_format($row["amount"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($shippingfee,2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['payment_portal_fee'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format(($row["amount"] - $row['payment_portal_fee']) + ($shippingfee),2).'</span>';
      $data[] = $nestedData;
    }

    $nestedData = array();
		$nestedData[] = '<strong>Total</strong>';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '<span class="float-right"><strong>'.$totalamount.'</strong></span>';
    $nestedData[] = '<span class="float-right"><strong>'.number_format($delivery_amount,2).'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$total_processfee.'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
		$data[] = $nestedData;

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_branch_tbl($search,$id,$trandate){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);
		$trandate = new Datetime($trandate);
		$date = $this->db->escape($trandate->format('Y-m-d'));
    $columns = array(
      0 => 'transdate',
      1 => 'branchname',
      2 => 'totalamount',
      3 => 'processfee',
			4 => 'netamount'
    );

    $sql = "SELECT a.*, b.branchname, c.orderid FROM sys_billing_branch a
			LEFT JOIN sys_branch_profile b ON a.branchid = b.id
			INNER JOIN sys_branch_orders c ON a.branchid = c.branchid
			WHERE a.syshop = $id AND a.status = 1 AND b.status = 1 AND
			DATE(a.transdate) = DATE(c.date_created) AND DATE(c.date_created) = $date
      GROUP BY a.id";

    $sql2 = "SELECT SUM(total_amount) as total_amount, SUM(total_process_fee) as total_process_fee, SUM(total_net_amount) as total_net_amount
      FROM (SELECT a.totalamount as total_amount,
      a.processfee as total_process_fee,
      a.netamount as total_net_amount
      FROM sys_billing_branch a
			LEFT JOIN sys_branch_profile b ON a.branchid = b.id
			INNER JOIN sys_branch_orders c ON a.branchid = c.branchid
			WHERE a.syshop = $id AND a.status = 1 AND b.status = 1 AND
			DATE(a.transdate) = DATE(c.date_created) AND DATE(c.date_created) = $date
            GROUP BY a.id) as sum";
    $query2 = $this->db->query($sql2);

    if($search != ""){
      // $sql .= $search;
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
      $trandate = new Datetime($row['transdate']);

      $nestedData[] = $trandate->format('M d, Y');
      $nestedData[] = $row['branchname'];
      $nestedData[] = '<span class="float-right">'.number_format($row['totalamount'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['processfee'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['netamount'],2).'</span>';
      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view_branch_logs"
              data-branchid = "'.en_dec('en',$row['branchid']).'"
              data-trandate = "'.$row['transdate'].'"
              data-totalamount = "'.number_format($row['totalamount'],2).'"
              data-processfee = "'.number_format($row['processfee'],2).'"
              data-netamount = "'.number_format(($row['netamount']),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View logs
            </a>
			  	</div>
  			</div>
      ';
      $data[] = $nestedData;
    }

    if($query2->num_rows() > 0){
      $row2 = $query2->row();
      $nestedData = array();
      $nestedData[] = '<strong>Total</strong>';
      $nestedData[] = '';
      $nestedData[] = '<span class="float-right"><strong>'.$row2->total_amount.'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.$row2->total_process_fee.'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.$row2->total_net_amount.'</strong></span>';
      $nestedData[] = '';
      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_branch_logs($branch_id,$trandate,$totalamount,$processfee,$netamount){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;
    $branch_id = $this->db->escape($branch_id);
    $trandate = new Datetime($trandate);
		$date = $this->db->escape($trandate->format('Y-m-d'));
    // return $date;
		$columns = array(
            0 => 'product_name',
            1 => 'price',
            2 => 'quantity',
            3 => 'totalamount',
            4 => 'ratetype',
            5 => 'processrate',
            6 => 'processfee',
            7 => 'netamount'
		);

		$sql=" SELECT a.*, CONCAT(b.itemname,'(',b.otherinfo,')') as product_name,
      c.shopname
      FROM sys_billing_branch_logs a
      LEFT JOIN sys_products b ON a.product_id = b.Id
      LEFT JOIN sys_shops c ON a.sys_shop = c.id
      WHERE DATE(a.trandate) = $date AND a.branch_id = $branch_id
      GROUP BY a.id";


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

      $nestedData[] = $row["product_name"];
      $nestedData[] = '<span class="float-right">'.number_format($row["price"],2).'</span>';
      $nestedData[] = ($row['ratetype'] == 'p') ? 'Percentage' : 'Fix';
      $nestedData[] = ($row['ratetype'] == 'p') ? ($row['processrate'] * 100).' %' : '<span class="float-right">'.number_format($row["processrate"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.$row["quantity"].'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["totalamount"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["processfee"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["netamount"],2).'</span>';
      $data[] = $nestedData;
    }

    $nestedData = array();
		$nestedData[] = '<strong>Total</strong>';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';
    $nestedData[] = '<span class="float-right"><strong>'.$totalamount.'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$processfee.'</strong></span>';
		$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
		$data[] = $nestedData;

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_branch_government_tbl($search,$id,$trandate){
    $requestData = $_REQUEST;
    $id = $this->db->escape($id);
		$trandate = new Datetime($trandate);
		$date = $this->db->escape($trandate->format('Y-m-d'));
    $columns = array(
      0 => 'transdate',
      1 => 'branchname',
      2 => 'totalamount',
      3 => 'processfee',
			4 => 'netamount'
    );

    $sql = "SELECT a.*, b.branchname FROM sys_billing_branch_government a
			LEFT JOIN sys_branch_profile b ON a.branchid = b.id
			INNER JOIN sys_branch_orders c ON a.branchid = c.branchid
			WHERE a.syshop = $id AND a.status = 1 AND b.status = 1 AND
			DATE(a.transdate) = DATE(c.date_created) AND DATE(c.date_created) = $date
      GROUP BY a.id";

    $sql2 = "SELECT SUM(total_amount) as total_amount, SUM(total_portal_fee) as total_portal_fee, SUM(total_net_amount) as total_net_amount
      FROM (SELECT a.totalamount as total_amount,
      portal_fee as total_portal_fee, a.netamount as total_net_amount
      FROM sys_billing_branch_government a
			LEFT JOIN sys_branch_profile b ON a.branchid = b.id
			INNER JOIN sys_branch_orders c ON a.branchid = c.branchid
			WHERE a.syshop = $id AND a.status = 1 AND b.status = 1 AND
			DATE(a.transdate) = DATE(c.date_created) AND DATE(c.date_created) = $date
      GROUP BY a.id) as sum";
    $query2 = $this->db->query($sql2);

    if($search != ""){
      // $sql .= $search;
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
      $trandate = new Datetime($row['transdate']);

      $nestedData[] = $trandate->format('M d, Y');
      $nestedData[] = $row['branchname'];
      $nestedData[] = '<span class="float-right">'.number_format($row['totalamount'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['portal_fee'],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row['netamount'],2).'</span>';
      $data[] = $nestedData;
    }

    if($query2->num_rows() > 0){
      $row2 = $query2->row();
      $nestedData = array();
      $nestedData[] = '<strong>Total</strong>';
      $nestedData[] = '';
      $nestedData[] = '<span class="float-right"><strong>'.$row2->total_amount.'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.$row2->total_portal_fee.'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.$row2->total_net_amount.'</strong></span>';
      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing($id) {
		$query=" SELECT a.*, DATE(a.trandate) as trandate, b.shopname, c.description as pay_type
        FROM sys_billing as a
				LEFT JOIN sys_shops as b ON a.syshop = b.id
				LEFT JOIN sys_payment_type as c ON a.paytype = c.id
				WHERE a.id = ? ";
		return $this->db->query($query, $id);
	}

  public function get_billing_government($id) {
		$query=" SELECT a.*, b.shopname, c.description as pay_type FROM sys_billing_government as a
				LEFT JOIN sys_shops as b ON a.syshop = b.id
				LEFT JOIN sys_payment_type as c ON a.paytype = c.id
				WHERE a.id = ? ";
		return $this->db->query($query, $id);
	}

  public function get_shop_options() {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    return $this->db->query($query)->result_array();
  }

  public function get_shops($shopid = false){
    $sql = "SELECT * FROM sys_shops WHERE status = 1";
    if($shopid){
      $shopid = $this->db->escape($shopid);
      $sql .= " AND id = $shopid";
    }
    return $this->db->query($sql);
  }

  public function get_options() {
		$query="SELECT * FROM sys_payment_type WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

  public function get_amount_to_pay($billcode) {
		$query="SELECT (netamount + delivery_amount) as netamount FROM sys_billing WHERE billcode = ? AND status = 1";

		$result = $this->db->query($query, $billcode);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

  public function get_amount_to_pay_portal_fee($billcode) {
		$query="SELECT (netamount + delivery_amount) as netamount FROM sys_billing WHERE billcode = ? AND status = 1";

		$result = $this->db->query($query, $billcode);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

  public function tagPayment($args) {

		$sql = "UPDATE `sys_billing` SET `paytype` = ?, `payref` = ?,`paidamount` = ?, `payremarks` = ?, `paiddate` = ?, `paystatus` = ? WHERE billcode = ? ";
		$bind_data = array(
			$args['f_payment'],
			$args['f_payment_ref_num'],
			$args['f_payment_fee'],
			$args['f_payment_notes'],
			date('Y-m-d H:i:s'),
			"Settled",
			$args['f_id-p']
		);
		return $this->db->query($sql, $bind_data);
	}

  public function tagPayment_portal_fee($args) {

		$sql = "UPDATE `sys_billing_government` SET `paytype` = ?, `payref` = ?,`paidamount` = ?, `payremarks` = ?, `paiddate` = ?, `paystatus` = ? WHERE billcode = ? ";
		$bind_data = array(
			$args['f_payment'],
			$args['f_payment_ref_num'],
			$args['f_payment_fee'],
			$args['f_payment_notes'],
			date('Y-m-d H:i:s'),
			"Settled",
			$args['f_id-p']
		);
		return $this->db->query($sql, $bind_data);
	}

  public function processDailyMerchantPay($trandate){
		$count = 0;
		$todaydate = todaytime();

		$sql = "SELECT id FROM sys_billing WHERE status=1 AND trandate=?";
		$data = array($trandate);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();


		if($r["id"] == "")
		{

			$sql = "SELECT sum(od.total_amount) AS totalamount, od.sys_shop AS shopid,
          @shipping_fee := (SELECT SUM(delivery_amount) FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND DATE(created) BETWEEN ? AND ? AND status = 1) as shipping_fee,
          @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
					FROM app_sales_order_details od
					WHERE od.payment_status=1
						AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
						AND (date(od.date_shipped) BETWEEN ? AND ?)
					GROUP BY od.sys_shop";

			$data = array($trandate,$trandate,$trandate,$trandate);
			$numrows = $this->db->query($sql,$data)->num_rows();


			if($numrows > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per shop rate', 'For Accounts Billing'));

				$billno = $this->getNewBillNo();

				$res = $this->db->query($sql,$data);
				$r = $res->result_array();

				foreach ($r AS $row)
				{
					if($row["totalamount"] > 0)
					{
						$billcode = $this->generateBillCode($billno,$row["shopid"]);
						$remarks = 'Settlement for transactions dated '.$trandate;

						$shoprate = $this->getShopRate($row["shopid"]);
						$ratetype = $shoprate["ratetype"];
						$processrate = $shoprate["rateamount"];
            $wallet = $this->get_shop_wallet($row['shopid'])->num_rows();
            $delivery_amount = ($row['billing_type'] == 1 && $wallet == 0) ? $row['shipping_fee'] : 0.00;

						$netamount = 0;
            $totalamount = $row['totalamount'];
						if($ratetype=='f')
						{
							$processfee = $this->getOrderCountPerDay($trandate,$row["shopid"]) * $processrate;
							if($processfee<0)
							{
								$processfee = $processrate;
							}

							// $netamount = $row["totalamount"] - $processfee;
							$netamount = $totalamount - $processfee;

						}
						else
						{
							// $processfee = $row["totalamount"] * $processrate;
							// $netamount = $row["totalamount"] - $processfee;

              $processfee = $totalamount * $processrate;
              $netamount = $totalamount - $processfee;
						}

						$count++;
						$sql = "INSERT INTO sys_billing (billno, billcode, syshop, trandate, delivery_amount, totalamount, remarks, processdate, dateupdated, ratetype, processrate, processfee, netamount, status)
								VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)
							";
						$data = array($billno,$billcode,$row["shopid"],$trandate,$delivery_amount,$row["totalamount"],$remarks, $todaydate, $todaydate, $ratetype, $processrate,$processfee,$netamount,1);
						$this->db->query($sql,$data);
					}
				}


        ### === FOR BILLING PER BRANCH === ###
  				$sql2 = "SELECT SUM(a.total_amount) as totalamount, a.sys_shop as shopid, b.branchid,
            @order_count := (SELECT COUNT(id) FROM sys_branch_orders WHERE orderid = a.reference_num) as order_count
  					FROM app_sales_order_details a
  					INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid
  					INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid
  					WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
  					AND DATE(a.date_shipped) BETWEEN ? AND ? GROUP BY b.branchid";

  				$branch_bills = $this->db->query($sql2,array($trandate,$trandate));
  				if($branch_bills->num_rows() > 0){

  					foreach ($branch_bills->result_array() as $row)
  					{
  						if($row["totalamount"] > 0)
  						{
  							$billcode = $this->generateBillCode($billno,$row["shopid"]);
  							$remarks = 'Settlement for transactions dated '.$trandate;

  							$shoprate = $this->getShopRate($row["shopid"]);
  							$ratetype = $shoprate["ratetype"];
  							$processrate = $shoprate["rateamount"];

  							$netamount = 0;
                $totalamount =  $row['totalamount'];
  							if($ratetype=='f')
  							{
  								$processfee = $row['order_count'] * $processrate;
  								if($processfee<0)
  								{
  									$processfee = $processrate;
  								}

  								// $netamount = $row["totalamount"] - $processfee;
  								$netamount = $totalamount - $processfee;

  							}
  							else
  							{
  								// $processfee = $row["totalamount"] * $processrate;
  								// $netamount = $row["totalamount"] - $processfee;

                  $processfee = $totalamount * $processrate;
                  $netamount = $totalamount - $processfee;
  							}

  							$count++;
  							$insert_data = array(
  								"billno" => $billno,
  								"billcode" => $billcode,
  								"syshop" => $row['shopid'],
  								"branchid" => $row['branchid'],
  								"transdate" => $trandate,
  								"totalamount" => $row['totalamount'],
  								"remarks" => $remarks,
  								"processdate" => $todaydate,
  								"dateupdated" => $todaydate,
  								"ratetype" => $ratetype,
  								"processrate" => $processrate,
  								"processfee" => $processfee,
  								"netamount" => $netamount,
  								"status" => 1
  							);
  							$this->db->insert('sys_billing_branch', $insert_data);
  						}
  					}
  				}

  			### ============================== ###


        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);
			}


		}

		return $count;

  }//close processDailyMerchantPay

  public function processDailyMerchantPay_per_product_rate($trandate){
		$count = 0;
		$todaydate = todaytime();

		$sql = "SELECT id FROM sys_billing WHERE status=1 AND trandate=?";
		$data = array($trandate);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();
    $shops_billing_per_branch = array();


		if($r["id"] == "")
		{
      // Query per shop billing
			$sql = "SELECT sum(od.total_amount) AS totalamount, od.sys_shop AS shopid,
          @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
          @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type,
          @billing_perbranch := (SELECT generatebilling FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_perbranch,
          @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count
					FROM app_sales_order_details od
					WHERE od.payment_status=1
						AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
						AND (date(od.date_shipped) BETWEEN ? AND ?)
					GROUP BY od.sys_shop ORDER BY sys_shop ASC";

      $data = array($trandate,$trandate);
      $numrows = $this->db->query($sql,$data)->num_rows();
      // Query for shop logs
      $sql_logs = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
        b.Id as product_id
        FROM app_sales_order_logs a
        INNER JOIN sys_products b ON a.product_id = b.Id
        WHERE order_id IN ((SELECT * FROM (SELECT id FROM app_sales_order_details WHERE payment_status = 1 AND order_status IN ('f','s') AND (DATE(date_shipped) BETWEEN ? AND ?)) as sys_shop))
        ORDER BY b.sys_shop ASC";
      // Query per branch billing logs
      $sql_logs_perbranch =
      "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
        b.Id as product_id
        FROM app_sales_order_logs a
        INNER JOIN sys_products b ON a.product_id = b.Id
        WHERE order_id IN ((SELECT * FROM (SELECT id FROM app_sales_order_details WHERE payment_status = 1 AND order_status IN ('f','s') AND (DATE(date_shipped) BETWEEN ? AND ?) AND reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1)) as sys_shop))
        ORDER BY b.sys_shop ASC";
      // Query for branch breakdown
      $sql2 = "SELECT SUM(a.total_amount) as totalamount, a.sys_shop as shopid, b.branchid
        FROM app_sales_order_details a
        INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
        INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
        WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
        AND DATE(a.date_shipped) BETWEEN ? AND ? GROUP BY b.branchid ORDER BY b.branchid";
      // Query for branch breakdown logs
      $sql_logs2 = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
        d.branchid, b.Id as product_id
        FROM app_sales_order_logs a
        INNER JOIN sys_products b ON a.product_id = b.Id
        INNER JOIN app_sales_order_details c ON a.order_id = c.id
        INNER JOIN sys_branch_orders d ON c.reference_num = d.orderid
        WHERE order_id IN ((SELECT * FROM (SELECT a.id FROM app_sales_order_details a INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid INNER JOIN sys_branch_mainshop c ON  b.branchid = c.branchid WHERE c.mainshopid = a.sys_shop AND a.payment_status = 1 AND a.order_status IN ('f','s') AND (DATE(a.date_shipped) BETWEEN ? AND ?) GROUP BY b.orderid) as logs))
        ORDER BY d.branchid ASC";
      // Query for per branch logs
      $sql_logs3 = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
        d.branchid, b.Id as product_id
        FROM app_sales_order_logs a
        INNER JOIN sys_products b ON a.product_id = b.Id
        INNER JOIN app_sales_order_details c ON a.order_id = c.id
        INNER JOIN sys_branch_orders d ON c.reference_num = d.orderid
        WHERE order_id IN ((SELECT * FROM (SELECT a.id FROM app_sales_order_details a INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid INNER JOIN sys_branch_mainshop c ON  b.branchid = c.branchid WHERE c.mainshopid = a.sys_shop AND a.payment_status = 1 AND a.order_status IN ('f','s') AND (DATE(a.date_shipped) BETWEEN ? AND ?) AND a.reference_num IN (SELECT orderid FROM sys_branch_orders WHERE status = 1) GROUP BY b.orderid) as logs))
        ORDER BY d.branchid ASC";

      $query2 = $this->db->query($sql_logs,array($trandate,$trandate));
      $query3 = $this->db->query($sql_logs_perbranch,array($trandate,$trandate));

			if($numrows > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));

				$billno = $this->getNewBillNo();

				$res = $this->db->query($sql,$data);
				$r = $res->result_array();
        $billing_logs_batchdata = array();

				foreach ($r AS $row){
					if($row["totalamount"] > 0){
            // Billing per branch
            if($row['billing_perbranch'] == 1 && $row['branch_count'] > 0){
              // for checking of shops with billing per branch ON
              array_push($shops_billing_per_branch,$row['shopid']);
              // Query for billing per branch
              $per_branch_sql = "SELECT sum(od.total_amount) AS totalamount, od.sys_shop AS shopid, od.id as orderid,
                  @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
                  @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
        					FROM app_sales_order_details od
        					WHERE od.payment_status=1
        						AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
        						AND (date(od.date_shipped) BETWEEN ? AND ?) AND od.sys_shop = ?
                    AND od.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1)";

              // Main shop
              $main_shop = $this->db->query($per_branch_sql, array($trandate,$trandate,$row['shopid']));
              if($main_shop->num_rows() > 0){
                $main_shop = $main_shop->row_array();
                if($main_shop['totalamount'] != NULL){
                  $total_amount = $main_shop['totalamount'];
                  $wallet = $this->get_shop_wallet($main_shop['shopid'])->num_rows();
                  $delivery_amount = ($main_shop['billing_type'] == 1 && $wallet == 0) ? $main_shop['shipping_fee']: 0.00;
                  $total_fee = 0;
                  $netamount = 0;

                  $billcode = $this->generateBillCode($billno,$main_shop["shopid"]);
      						$remarks = 'Settlement for transactions dated '.$trandate;

                  // processing fee
                  if($query3->num_rows() > 0){
                    foreach($query3->result_array() as $logs){
                      if($main_shop['shopid'] == $logs['sys_shop']){
                        $fee = ($logs['ratetype'] == 'p')
                        ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                        : $logs['rate'] * $logs['quantity'];  // for fix rate type

                        $total_fee += $fee;
                        $billing_logs_data = array(
                          "sys_shop" => $main_shop['shopid'],
                          "branch_id" => 0, // main shop
                          "product_id" => $logs['product_id'],
                          "order_id" => $logs['order_id'],
                          "trandate" => $trandate,
                          "totalamount" => $logs['total_amount'],
                          "price" => $logs['price'],
                          "quantity" => $logs['quantity'],
                          "ratetype" => $logs['ratetype'],
                          "processrate" => $logs['rate'],
                          "processfee" => $fee,
                          "netamount" => $logs['total_amount'] - $fee
                        );
                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }
                  }

                  $netamount = $total_amount - $total_fee;
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $row['shopid'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $delivery_amount,
                    "totalamount" => $total_amount,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $total_fee,
                    "netamount" => $netamount,
                    "status" => 1
                  );
                  $this->db->insert('sys_billing',$billing_data);
                }
              }

              // Branches
              $branch_sql = "SELECT SUM(a.total_amount) as totalamount, a.sys_shop as shopid, b.branchid,
                @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND status = 1 AND reference_num = a.reference_num)) as shipping_fee,
                @billing_type := (SELECT billing_type FROM sys_shops WHERE id = a.sys_shop AND status = 1) as billing_type
      					FROM app_sales_order_details a
      					INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
      					INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
      					WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
      					AND DATE(a.date_shipped) BETWEEN ? AND ? AND a.sys_shop = ? GROUP BY b.branchid";

              $branches = $this->db->query($branch_sql,array($trandate,$trandate,$row['shopid']));
              if($branches->num_rows() > 0){
                $branches = $branches->result_array();
                foreach($branches as $branch){
                  if($branch['totalamount'] > 0){
                    $billcode = $this->generateBillCode($billno,$branch["shopid"]);
                    $billcode = $billcode.$branch['branchid'];
      							$remarks = 'Settlement for transactions dated '.$trandate;
                    $total_amount = $branch['totalamount'];
                    $wallet = $this->get_shop_wallet($branch['shopid'])->num_rows();
                    $delivery_amount = ($branch['billing_type'] == 1 && $wallet == 0) ? $branch['shipping_fee']: 0.00;
                    $netamount = 0;
                    $total_fee = 0;
                    $per_branch_logs = $this->db->query($sql_logs3,array($trandate,$trandate));
                    if($per_branch_logs->num_rows() > 0){
                      foreach($per_branch_logs->result_array() as $logs){
                        if($branch['shopid'] == $logs['sys_shop'] && $branch['branchid'] == $logs['branchid']){
                          $bfee = ($logs['ratetype'] == 'p')
                          ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                          : $logs['rate'] * $logs['quantity'];  // for fix rate type

                          $total_fee += $bfee;
                          $billing_logs_data = array(
                            "sys_shop" => $branch['shopid'],
                            "branch_id" => $branch['branchid'],
                            "product_id" => $logs['product_id'],
                            "order_id" => $logs['order_id'],
                            "trandate" => $trandate,
                            "totalamount" => $logs['total_amount'],
                            "price" => $logs['price'],
                            "quantity" => $logs['quantity'],
                            "ratetype" => $logs['ratetype'],
                            "processrate" => $logs['rate'],
                            "processfee" => $bfee,
                            "netamount" => $logs['total_amount'] - $bfee
                          );
                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      $netamount = $total_amount - $total_fee;
                    }

                    $count++;
                    $billing_data = array(
                      "billno" => $billno,
                      "billcode" => $billcode,
                      "syshop" => $branch['shopid'],
                      "branch_id" => $branch['branchid'],
                      "per_branch_billing" => 1,
                      "trandate" => $trandate,
                      "delivery_amount" => $delivery_amount,
                      "totalamount" => $total_amount,
                      "remarks" => $remarks,
                      "processdate" => $todaydate,
                      "dateupdated" => $todaydate,
                      "processfee" => $total_fee,
                      "netamount" => $netamount,
                      "status" => 1
                    );
                    $this->db->insert('sys_billing',$billing_data);

                  }
                }
              }

            // Billing per shop
            }else{
              $total_amount = $row['totalamount'];
              $wallet = $this->get_shop_wallet($row['shopid'])->num_rows();
              $delivery_amount = ($row['billing_type'] == 1 && $wallet == 0) ? $row['shipping_fee']: 0.00;
              $total_fee = 0;
              $netamount = 0;

  						$billcode = $this->generateBillCode($billno,$row["shopid"]);
  						$remarks = 'Settlement for transactions dated '.$trandate;

              // processing fee
              if($query2->num_rows() > 0){
                foreach($query2->result_array() as $logs){
                  if($row['shopid'] == $logs['sys_shop']){
                    $fee = ($logs['ratetype'] == 'p')
                    ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                    : $logs['rate'] * $logs['quantity'];  // for fix rate type

                    $total_fee += $fee;
                    $billing_logs_data = array(
                      "sys_shop" => $row['shopid'],
                      "branch_id" => 0,
                      "product_id" => $logs['product_id'],
                      "order_id" => $logs['order_id'],
                      "trandate" => $trandate,
                      "totalamount" => $logs['total_amount'],
                      "price" => $logs['price'],
                      "quantity" => $logs['quantity'],
                      "ratetype" => $logs['ratetype'],
                      "processrate" => $logs['rate'],
                      "processfee" => $fee,
                      "netamount" => $logs['total_amount'] - $fee
                    );
                    $billing_logs_batchdata[] = $billing_logs_data;
                  }
                }
              }

              $netamount = $total_amount - $total_fee;

  						$count++;
              $billing_data = array(
                "billno" => $billno,
                "billcode" => $billcode,
                "syshop" => $row['shopid'],
                "trandate" => $trandate,
                "delivery_amount" => $delivery_amount,
                "totalamount" => $total_amount,
                "remarks" => $remarks,
                "processdate" => $todaydate,
                "dateupdated" => $todaydate,
                "processfee" => $total_fee,
                "netamount" => $netamount,
                "status" => 1
              );
              $this->db->insert('sys_billing',$billing_data);
            }

					}
				}
        // print("<pre>".print_r($billing_logs_batchdata,true)."</pre>");
        // die();
        $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);


        ### === FOR BILLING PER BRANCH === ###
          $b_logs = $this->db->query($sql_logs2,array($trandate,$trandate));

  				$branch_bills = $this->db->query($sql2,array($trandate,$trandate));
  				if($branch_bills->num_rows() > 0){
            $billing_branch_batchdata = array();

  					foreach ($branch_bills->result_array() as $row){
              // check if total amout greater than zero and shop is not per branch billing
  						if($row["totalamount"] > 0 && !in_array($row['shopid'],$shops_billing_per_branch))
  						{
  							$billcode = $this->generateBillCode($billno,$row["shopid"]);
  							$remarks = 'Settlement for transactions dated '.$trandate;
                $netamount = 0;
                $total_fee = 0;
                $total_amount = $row['totalamount'];

                if($b_logs->num_rows() > 0){
                  foreach($b_logs->result_array() as $logs){
                    if($row['shopid'] == $logs['sys_shop'] && $row['branchid'] == $logs['branchid']){
                      $bfee = ($logs['ratetype'] == 'p')
                      ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                      : $logs['rate'] * $logs['quantity'];  // for fix rate type

                      $total_fee += $bfee;
                      $billing_logs_data = array(
                        "sys_shop" => $row['shopid'],
                        "product_id" => $logs['product_id'],
                        "order_id" => $logs['order_id'],
                        "branch_id" => $row['branchid'],
                        "trandate" => $trandate,
                        "totalamount" => $logs['total_amount'],
                        "price" => $logs['price'],
                        "quantity" => $logs['quantity'],
                        "ratetype" => $logs['ratetype'],
                        "processrate" => $logs['rate'],
                        "processfee" => $bfee,
                        "netamount" => $logs['total_amount'] - $bfee
                      );
                      $billing_branch_batchdata[] = $billing_logs_data;
                    }
                  }

                  $netamount = $total_amount - $total_fee;
                }

  							$count++;
  							$insert_data = array(
  								"billno" => $billno,
  								"billcode" => $billcode,
  								"syshop" => $row['shopid'],
  								"branchid" => $row['branchid'],
  								"transdate" => $trandate,
  								"totalamount" => $total_amount,
  								"remarks" => $remarks,
  								"processdate" => $todaydate,
  								"dateupdated" => $todaydate,
  								"processfee" => $total_fee,
  								"netamount" => $netamount,
  								"status" => 1
  							);
  							$this->db->insert('sys_billing_branch', $insert_data);
  						}

  					}
            if(count((array)$billing_branch_batchdata) > 0){
              $this->db->insert_batch('sys_billing_branch_logs',$billing_branch_batchdata);
            }
  				}

  			### ============================== ###


        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);
			}



		}

		return $count;

  }//close processDailyMerchantPay

  public function processdaily_merchant_pay_government($trandate){
		$count = 0;
		$todaydate = todaytime();

		$sql = "SELECT id FROM sys_billing_government WHERE status=1 AND trandate=?";
		$data = array($trandate);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();


		if($r["id"] == "")
		{

			$sql = "SELECT sum(od.total_amount) AS totalamount,
          SUM(od.payment_portal_fee) as total_portal_fee,
          od.sys_shop AS shopid,
          @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
          @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
					FROM app_sales_order_details od
					WHERE od.payment_status=1
						AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
						AND (date(od.date_shipped) BETWEEN ? AND ?)
					GROUP BY od.sys_shop";

			$data = array($trandate,$trandate);
			$numrows = $this->db->query($sql,$data)->num_rows();


			if($numrows > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per payment potal fee', 'For Accounts Billing'));

				$billno = $this->getNewBillNo();

				$res = $this->db->query($sql,$data);
				$r = $res->result_array();

				foreach ($r AS $row)
				{
					if($row["totalamount"] > 0)
					{
						$billcode = $this->generateBillCode($billno,$row["shopid"]);
						$remarks = 'Settlement for transactions dated '.$trandate;
            $portal_fee = $row['total_portal_fee'];
            $netamount = $row['totalamount'] - $row['total_portal_fee'];
            $wallet = $this->get_shop_wallet($row['shopid'])->num_rows();
            $delivery_amount = ($row['billing_type'] == 1) ? $row['shipping_fee'] : 0.00;
            // $delivery_amount = ($row['billing_type'] == 1 && $wallet == 0) ? $row['shipping_fee'] : 0.00;


						$count++;
						$sql = "INSERT INTO sys_billing_government (billno, billcode, syshop, trandate, delivery_amount, totalamount, remarks, processdate, dateupdated, portal_fee, netamount, status)
								VALUES(?,?,?,?,?,?,?,?,?,?,?,?)
							";
						$data = array($billno,$billcode,$row["shopid"],$trandate,$delivery_amount,$row["totalamount"],$remarks, $todaydate, $todaydate, $portal_fee, $netamount,1);
						$this->db->query($sql,$data);
					}
				}

        ### === FOR BILLING PER BRANCH === ###
  				$sql2 = "SELECT SUM(a.total_amount) as totalamount, a.sys_shop as shopid, b.branchid,
            SUM(a.payment_portal_fee) as total_portal_fee
  					FROM app_sales_order_details a
  					INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid
  					INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid
  					WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
  					AND DATE(a.date_shipped) BETWEEN ? AND ? GROUP BY b.branchid";

  				$branch_bills = $this->db->query($sql2,array($trandate,$trandate));
  				if($branch_bills->num_rows() > 0){

  					foreach ($branch_bills->result_array() as $row)
  					{
  						if($row["totalamount"] > 0)
  						{
  							$billcode = $this->generateBillCode($billno,$row["shopid"]);
  							$remarks = 'Settlement for transactions dated '.$trandate;

                $portal_fee = $row['total_portal_fee'];
                $netamount = $row['totalamount'] - $row['total_portal_fee'];

  							$count++;
  							$insert_data = array(
  								"billno" => $billno,
  								"billcode" => $billcode,
  								"syshop" => $row['shopid'],
  								"branchid" => $row['branchid'],
  								"transdate" => $trandate,
  								"totalamount" => $row['totalamount'],
  								"remarks" => $remarks,
  								"processdate" => $todaydate,
  								"dateupdated" => $todaydate,
  								"portal_fee" => $portal_fee,
  								"netamount" => $netamount,
  								"status" => 1
  							);
  							$this->db->insert('sys_billing_branch_government', $insert_data);
  						}
  					}
  				}

  			### ============================== ###


        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

			}

		}

		return $count;

  }//close processDailyMerchantPay

  public function process_billing_per_shop($trandate,$data){
    $row = $data;
  }

  public function getNewBillNo(){
		$sql = "SELECT billno FROM sys_idkey WHERE status=1";
		$res = $this->db->query($sql);
		$r = $res->row_array();

		$billno = $r["billno"];
		$billno ++;

		$checker=1;

		if($checker==1)
		{
			$sql = "SELECT billno FROM sys_billing WHERE status=1 AND billno=?";
			$data = array($billno);
			$res = $this->db->query($sql,$data);
			$r = $res->row_array();

			if($r["billno"] != "")
			{
				$billno++;
			}
			else
			{
				$sql = "UPDATE sys_idkey SET billno=? WHERE status=1";
				$data = array($billno);
				$this->db->query($sql,$data);

				$checker = 0;
			}

		}

		return $billno;

	}//close getNewBillNo

  public function generateBillCode($billno,$shopid){
		$todaydate = today();
		$todayref = str_replace('-','', $todaydate);
		if($billno < 100000)
		{
			$billno = $billno+100000;
		}

		$shopcode = $this->getShopCode($shopid);
		$shopcode = str_replace(' ','', $shopcode);
		$billcode = strtoupper($shopcode).$todayref.$billno;

		return $billcode;
	}

  public function getShopRate($shopid){
		$sql = "SELECT ratetype,rateamount FROM sys_shop_rate WHERE status=1 AND syshop=?";
		$data = array($shopid);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();

		if($r["ratetype"]=="")
		{
			$sql = "SELECT ratetype,rateamount FROM sys_shop_rate WHERE status=1 AND syshop=?";
			$data = array(0);
			$res = $this->db->query($sql,$data);
			$r = $res->row_array();
		}

		return $r;

	}//close getNewBillNo

  public function getOrderCountPerDay($trandate,$shopid){
		$sql = "SELECT DISTINCT COUNT(reference_num) AS bilang
				FROM app_sales_order_details
				WHERE payment_status=1
					AND order_status IN ('f')
					AND (date(payment_date) BETWEEN ? AND ?)
					AND sys_shop=? ";

		$data = array($trandate,$trandate,$shopid);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();

		if($r["bilang"]=="")
		{
			return 1;
		}
		else
		{
			return $r["bilang"];
		}

	}

  public function getShopCode($shopid){
		$sql = "SELECT shopcode FROM sys_shops WHERE status=1 and id=?";
		$data = array($shopid);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();
		return $r["shopcode"];
	}

  public function get_shop_wallet($shopid){
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT * FROM sys_shops_wallet WHERE enabled = 1 AND shopid = $shopid AND balance > 0";
    return $this->db->query($sql);
  }


}
