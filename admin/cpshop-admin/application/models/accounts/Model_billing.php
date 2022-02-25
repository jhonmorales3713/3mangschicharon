<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_billing extends CI_Model {
  public function get_billing_table($search, $requestData, $exportable = false){
    // print_r($search);
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

    if(c_international() == 1){
      $sql=" SELECT a.*, b.shopname as shopname, c.currency_symbol,
          @branch_name := (SELECT branchname FROM sys_branch_profile WHERE id = a.branch_id AND status = 1) as branch_name
          FROM sys_billing as a
  				LEFT JOIN sys_shops as b ON a.syshop = b.id
          LEFT JOIN app_currency c ON b.app_currency_id = c.id
          WHERE a.status = 1 AND b.status = 1 AND c.status = 1";
    }else{
      $sql=" SELECT a.*, b.shopname as shopname, c.accountname, c.accountno, c.bankname,
          @branch_name := (SELECT branchname FROM sys_branch_profile WHERE id = a.branch_id AND status = 1) as branch_name
          FROM sys_billing as a
  				LEFT JOIN sys_shops as b ON a.syshop = b.id
          LEFT JOIN sys_shop_account c ON a.syshop = c.sys_shop AND a.branch_id = c.branch_id AND c.status = 1
          WHERE a.status = 1";
    }


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

    if($search->search != ''){
      $billcode = $this->db->escape($search->search);
      $sql .= " AND a.billcode = $billcode";
    }

    if($search->shop != ''){
      $shopid = $this->db->escape(en_dec('dec',$search->shop));
      $sql .= " AND syshop = $shopid";
    }

    if($search->branch != 'null'){
      $branchid = $this->db->escape($search->branch);
      $sql .= " AND a.branch_id = $branchid";
    }

    if($search->from != '' && $search->to != ''){
      $from =  $this->db->escape(format_date_reverse_dash($search->from));
      $to = $this->db->escape(format_date_reverse_dash($search->to));
      $sql .= " AND DATE(trandate) BETWEEN $from AND $to";
    }

    if($this->loginstate->get_access()['seller_access'] == 1 && $this->session->sys_shop_id != ''){
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND syshop = $sys_shop_id";
    }

    if(($this->loginstate->get_access()['seller_branch_access'] == 1 || $this->loginstate->get_access()['food_hub_access'] == 1) && $this->session->sys_shop_id != ''){
      $branchid = $this->db->escape($this->session->branchid);
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.branch_id = $branchid AND syshop = $sys_shop_id";
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
      if(c_international() == 1 && $row['currency_symbol'] != "PHP"){
        $total_amount = 'PHP '.number_format(($row["totalamount"] + $row['delivery_amount']),2).'/ '.$row['currency_symbol'].' '.number_format($row['totalamount_oc'] + $row['delivery_amount_oc'],2);
        $processfee = 'PHP '.number_format($row["processfee"],2).'/ '.$row['currency_symbol'].' '.number_format($row['processfee_oc'],2);
        $netamount = 'PHP '.number_format(($row["netamount"] + $row['delivery_amount']),2).'/ '.$row['currency_symbol'].' '.number_format($row['netamount_oc'] + $row['delivery_amount_oc'],2);
      }else{
        $total_amount = ($row["totalamount"] + $row['delivery_amount']);
        $total_amount = 'PHP '.number_format($total_amount,2);
        $total_comrate = 'PHP '.number_format($row['totalcomrate'],2);
        if($this->loginstate->get_access()['billing']['admin_view'] == 1){
          $processfee = 'PHP '.number_format($row["processfee"],2);
        }else{
          $processfee = 'PHP '.number_format($row["processfee"] + $row['totalcomrate'],2);
        }
        $netamount = 'PHP '.number_format(($row["netamount"] + $row['delivery_amount']),2);
      }


      $nestedData[] = readable_date($row["trandate"]);
      $nestedData[] = $row["billcode"];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.$total_amount.'</span>':$total_amount;
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.$total_comrate.'</span>':$total_comrate;
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
          case 'Unsettled':
              $nestedData[] = '<center><label class="badge badge-info"> Partially Settled</label></center>';
              break;

          default:
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
      }

      $delete_btn = "";
      if($this->loginstate->get_access()['billing']['admin_view'] == 1){
        $delete_btn =
        '
          <a class = "dropdown-item btn_delete"
            id = "'.en_dec('en',$row['id']).'"
            data-shopid = "'.en_dec('en',$row['syshop']).'"
            data-branchid = "'.en_dec('en',$row['branch_id']).'"
            data-encrypted_id = "'.en_dec('en',$row['id']).'"
            data-billcode = "'.$row['billcode'].'"
            data-payref = "'.$row['payref'].'"
            data-unsettled_payref = "'.$row['unsettled_payref'].'"
            data-name = "Billing '.$shopname.' - '.readable_date($row["trandate"]).'"
            data-trandate = "'.$row["trandate"].'"
          >
            Delete
          </a>
        ';
      }


      if(c_international() == 1 && $row['currency_symbol'] != 'PHP'){
        $nestedData[] =
        '
          <div class="dropdown text-center">
    				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
    				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
              <a class="dropdown-item btn_view"
                id="'.$row['id'].'"
                data-encrypted_id = "'.en_dec('en',$row['id']).'"
                data-c_international = "'.c_international().'"
                data-currency = "'.$row['currency_symbol'].'"
                data-ref_num="'.$row['billcode'].'"
                data-total_amount = "'.number_format($row["totalamount"],2).'"
                data-total_amount_string = "'.'PHP '.number_format($row["totalamount"],2).'/ '.$row['currency_symbol'].' '.number_format($row['totalamount_oc'],2).'"
                data-delivery_amount = "'.$row['delivery_amount'].'"
                data-delivery_amount_string = "'.'PHP '.$row['delivery_amount'].'/ '.$row['currency_symbol'].' '.number_format($row['delivery_amount_oc'],2).'"
                data-total_amount_w_shipping = "'.number_format(($row["totalamount"] + $row['delivery_amount']),2).'"
                data-total_amount_w_shipping_string = "'.'PHP '.number_format(($row["totalamount"] + $row['delivery_amount']),2).'/ '.$row['currency_symbol'].' '.number_format($row['totalamount_oc'] + $row['delivery_amount_oc'],2).'"
                data-processfee = "'.$row["processfee"].'"
                data-processfee_string = "'.'PHP '.$row["processfee"].'/ '.$row['currency_symbol'].' '.number_format($row['processfee_oc'],2).'"
                data-netamount = "'.number_format($row["netamount"],2).'"
                data-netamount_string = "'.'PHP '.number_format($row["netamount"],2).'"
                data-netamount_w_shipping = "'.number_format(($row["netamount"] + $row['delivery_amount']),2).'"
                data-netamount_w_shipping_string = "'.'PHP '.number_format(($row["netamount"] + $row['delivery_amount']),2).'/ '.$row['currency_symbol'].' '.number_format($row['netamount_oc'] + $row['delivery_amount_oc'],2).'"
              >
                <i class="fa fa-ye" aria-hidden="true"></i> View
              </a>
  			  	</div>
    			</div>
        ';
      }else{
        $nestedData[] =
        '
          <div class="dropdown text-center">
    				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
    				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
              <a class="dropdown-item btn_view"
                id="'.$row['id'].'"
                data-encrypted_id = "'.en_dec('en',$row['id']).'"
                data-ref_num="'.$row['billcode'].'"
                data-total_amount = "'.number_format($row["totalamount"],2).'"
                data-total_comrate = "'.number_format($row["totalcomrate"],2).'"
                data-delivery_amount = "'.$row['delivery_amount'].'"
                data-total_amount_w_shipping = "'.number_format(($row["totalamount"] + $row['delivery_amount']),2).'"
                data-processfee = "'.$row["processfee"].'"
                data-netamount = "'.number_format($row["netamount"],2).'"
                data-netamount_w_shipping = "'.number_format(($row["netamount"] + $row['delivery_amount']),2).'"
                data-accountname = "'.$row['accountname'].'"
                data-accountno = "'.$row['accountno'].'"
                data-bankname = "'.$row['bankname'].'"
                data-branch_name = "'.$row['branch_name'].'"
              >
                <i class="fa fa-ye" aria-hidden="true"></i> View
              </a>
              '.$delete_btn.'
  			  	</div>
    			</div>
        ';
      }

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

  public function get_billing_breakdown_table($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount,$delivery_amount,$total_comrate){
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
            4 => 'shippingfee',
            5 => 'voucher_amount',
            6 => 'total_amount_w_voucher',
            7 => 'shippingfee',
            8 => 'refcom_totalamount',
            9 => 'processfee'
		);

    // Per shop billing
    if(c_international() == 1){
      $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, a.currency, a.exrate_n_to_php, @sys_shop := a.sys_shop,
        a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher, a.id as order_id, a.total_amount_oc, a.user_id,
        @process_fee := (SELECT CONCAT(SUM(processfee),',',SUM(processfee_oc)) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
        @shippingfee := (SELECT (CASE WHEN converted_delivery_amount IS NULL THEN CONCAT(delivery_amount,',','0.00') ELSE CONCAT(delivery_amount,',',converted_delivery_amount) END) as shipping_fee FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
        @voucher_amount := (SELECT SUM(amount) FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
        @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
        FROM `app_sales_order_details` a
        LEFT JOIN `app_order_branch_details` b ON a.reference_num = b.order_refnum
        WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' AND a.status = 1";
    }else{
      // $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, @sys_shop := a.sys_shop,
      //   a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher, a.id as order_id,
      //   @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
      //   @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
      //   @voucher_amount := (SELECT amount FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount
      //   FROM `app_sales_order_details` a
      //   LEFT JOIN `app_order_branch_details` b ON a.reference_num = b.order_refnum
      //   WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' AND a.status = 1";

      $sql = "SELECT a.date_shipped as trandate, a.reference_num as refnum, a.sys_shop,
        a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher,
        a.id order_id, a.user_id,
        @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
        @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
        @voucher_amount := (SELECT SUM(amount) FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
        @refcom_totalamount := (SELECT SUM(total_amount * refcom_rate) FROM app_sales_order_logs WHERE order_id = a.id AND status = 1) as refcom_totalamount,
        @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
        FROM app_sales_order_details a
        LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum";

      if(ini() == "jcww"){

        if(strtotime($trandate) <= strtotime('2021-05-20')){
          $sql .= " WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_shipped) = '".$trandate."'
            AND a.status = 1";
        }else{
          $sql .= " WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_confirmed) = '".$trandate."'
            AND a.status = 1 AND a.isconfirmed = 1";
        }

      }else{
        $sql .= " WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_shipped) = '".$trandate."'
          AND a.status = 1";
      }


    }


    // Per branch billing
    if($per_branch_billing == 1){
      // Main Branch
      if($branch_id == 0){
        // $sql .= " AND a.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND a.sys_shop = '".$sys_shop. "')";
        $sql .= " AND b.branchid = 0";
      }

      if($branch_id != 0){
        $sql .= " AND b.branchid != 0 AND b.branchid = ".$branch_id."";
      }

    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    $query = $this->db->query($sql);
    // print_r($query);
    // print_r($query->result_array());
    // die();

    $data = array();
    $total_shippingfee = 0;
    $total_amount = 0;
    $total_amount_w_voucher = 0;
    $total_netpay = 0;
    if(c_international() == 1){
      $total_shippingfee_oc = 0;
      $total_amount_oc = 0;
      $total_fee_oc = 0;
      $total_netpay_oc = 0;
      $total_amount_w_voucher_oc = 0;
      $curr = "PHP";
    }

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != 'PHP'){
        // echo $row['shippingfee'];
        $shippingfee_arr = explode(',',$row['shippingfee']);
        $curr = $row['currency'];
        $row['total_amount_w_voucher'] = (floatval($row['total_amount_w_voucher']) == 0 && floatval($row['voucher_amount'] == 0)) ? $row['total_amount'] : $row['total_amount_w_voucher'];
        // print_r($shippingfee_arr);
        // die();
        $shippingfee = ($shippingfee_arr[0] == '') ? 0 : $shippingfee_arr[0];
        $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
        $total_shippingfee += $shippingfee;
        $shippingfee_oc = $shippingfee_arr[1];
        $fee = explode(',',$row['processfee']);
        $fee_oc = (isset($fee[1])) ? $fee[1] : 0;
        $total_fee_oc += $fee_oc;
        $total_amount_oc += floatval($row['total_amount_oc']);
        $total_shippingfee_oc += $shippingfee_oc;
        $total_netpay_oc += ($row['total_amount_oc'] - $fee_oc) + $shippingfee_oc;
        $row['processfee'] = $fee[0];
      }else if(c_international() == 1 && $row['currency'] == null){
        $shippingfee_arr = explode(',',$row['shippingfee']);
        $shippingfee = ($shippingfee_arr[0] == '') ? 0 : $shippingfee_arr[0];
        $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
        $fee = explode(',',$row['processfee']);
        $row['processfee'] = floatval($fee[0]);

      }else{
        $shippingfee = ($row['shippingfee'] == '') ? 0 : $row['shippingfee'];
        $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
        $total_shippingfee += $shippingfee;
      }

      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != 'PHP'){
        $total_amount += $row["amount"];
        $total_amount_w_voucher += floatval($row['total_amount_w_voucher']);
        $total_amount_w_voucher_oc += php_to_n(floatval($row['total_amount_w_voucher']),$row['exrate_n_to_php']);
        $total_netpay += ($row["total_amount_w_voucher"] - $fee[0]);
      }else{
        // if(floatval($row['refcom_totalamount']) > 0){
        //   $row['amount']= floatval($row['refcom_totalamount']);
        //   $row['total_amount_w_voucher'] = floatval($row['refcom_totalamount']) - floatval($row['voucher_amount']);
        // }

        $total_amount += $row["amount"];
        $total_amount_w_voucher += floatval($row['total_amount_w_voucher']);
        $total_netpay += ($row["total_amount_w_voucher"] - $row['processfee']);
      }

      $order_type = 'Regular Order';
      if($row['user_id'] != 0){
        $order_type = "Thru OF Login";
      }

      if($row['referral_code'] != "" || $row['referral_code'] != null){
        $order_type = "Via OF Shoplink";
      }

      $t_date = new Datetime($row['trandate']);
      $nestedData[] = $t_date->format('M d, Y h:i:m');
      $nestedData[] = $row["refnum"];
      $nestedData[] = $row["payrefnum"];
      $nestedData[] = $order_type;
      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != "PHP"){
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["amount"],2).'/ '.$row['currency'].' '.number_format($row['total_amount_oc'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["voucher_amount"],2).'/ '.$row['currency'].' '.number_format(php_to_n($row['voucher_amount'],$row['exrate_n_to_php']),2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["total_amount_w_voucher"],2).'/ '.$row['currency'].' '.number_format(php_to_n($row['voucher_amount'],$row['exrate_n_to_php']),2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($shippingfee,2).'/ '.$row['currency'].' '.number_format($shippingfee_oc,2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($fee[0],2).'/ '.$row['currency'].' '.number_format($fee_oc,2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format(($row["amount"] - $fee[0]) + ($shippingfee),2).'/ '.$row['currency'].' '.number_format(($row['total_amount_oc'] - $fee_oc) + $shippingfee_oc,2).'</span>';
      }else{
        $nestedData[] = '<span class="float-right">'.number_format($row["amount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["voucher_amount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["total_amount_w_voucher"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($shippingfee,2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row['refcom_totalamount'],2).'</span>';
        if($this->loginstate->get_access()['billing']['admin_view'] == 1){
          $nestedData[] = '<span class="float-right">'.number_format($row['processfee'],2).'</span>';
        }else{
          $nestedData[] = '<span class="float-right">'.number_format($row['processfee'] + $row['refcom_totalamount'],2).'</span>';
        }
        $nestedData[] = '<span class="float-right">'.number_format((($row["total_amount_w_voucher"] - $row['refcom_totalamount']) - $row['processfee']) + ($shippingfee),2).'</span>';
      }

      if($this->loginstate->get_access()['billing']['admin_view'] == 1){
        $total_processfee_logs = number_format($row['processfee'],2);

      }else{
        $total_processfee_logs = number_format($row['processfee'] + $row['refcom_totalamount'],2);
      }

      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view_logs"
              data-orderid = "'.en_dec('en',$row['order_id']).'"
              data-refnum = "'.$row['refnum'].'"
              data-totalamount = "'.number_format(($row['amount'] - $row['refcom_totalamount']),2).'"
              data-processfee = "'.$total_processfee_logs.'"
              data-netamount = "'.number_format((($row['amount'] - $row['refcom_totalamount']) - $row['processfee']),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View logs
            </a>
			  	</div>
  			</div>
      ';

      $data[] = $nestedData;
    }
    // print_r($data);
    // $nestedData = array();
		// $nestedData[] = '<strong>Total</strong>';
		// $nestedData[] = '';
		// $nestedData[] = '';
		// $nestedData[] = '';
    // if(c_international() == 1 && $curr != 'PHP'){
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$totalamount.'/ '.$row['currency'].' '.number_format($total_amount_oc,2).'</strong></span>';
    //   $nestedData[] = '';
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$total_amount_w_voucher.'/ '.$row['currency'].' '.number_format($total_amount_w_voucher_oc,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.number_format($delivery_amount,2).'/ '.$row['currency'].' '.number_format($total_shippingfee_oc,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.'PHP '.number_format($total_processfee,2).'/ '.$row['currency'].' '.number_format($total_fee_oc,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.'PHP '.$netamount.'/ '.$row['currency'].' '.number_format($total_netpay_oc,2).'</strong></span>';
    // }else{
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($total_amount,2).'</strong></span>';
    //   $nestedData[] = '';
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($total_amount_w_voucher,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($delivery_amount,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.$total_comrate.'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.number_format($total_processfee,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
    // }
    // $nestedData[] = '';
		// $data[] = $nestedData;
    // print_r($data);
    // die();

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_breakdown_foodhub($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount,$delivery_amount,$total_comrate){
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
            4 => 'shippingfee',
            5 => 'voucher_amount',
            6 => 'total_amount_w_voucher',
            7 => 'shippingfee',
            8 => 'refcom_totalamount',
            9 => 'processfee'
		);

    // Per shop billing
    if(c_international() == 1){
      $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, a.currency, a.exrate_n_to_php, @sys_shop := a.sys_shop,
        a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher, a.id as order_id, a.total_amount_oc, a.user_id,
        @process_fee := (SELECT CONCAT(SUM(processfee),',',SUM(processfee_oc)) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
        @shippingfee := (SELECT (CASE WHEN converted_delivery_amount IS NULL THEN CONCAT(delivery_amount,',','0.00') ELSE CONCAT(delivery_amount,',',converted_delivery_amount) END) as shipping_fee FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
        @voucher_amount := (SELECT SUM(amount) FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
        @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
        FROM `app_sales_order_details` a
        LEFT JOIN `app_order_branch_details` b ON a.reference_num = b.order_refnum
        WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' AND a.status = 1";
    }else{
      // $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, @sys_shop := a.sys_shop,
      //   a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher, a.id as order_id,
      //   @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
      //   @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
      //   @voucher_amount := (SELECT amount FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount
      //   FROM `app_sales_order_details` a
      //   LEFT JOIN `app_order_branch_details` b ON a.reference_num = b.order_refnum
      //   WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' AND a.status = 1";

      // BILLING LOGS
      $sql_billing_logs = "SELECT processfee, order_id FROM sys_billing_logs WHERE DATE(trandate) = '".$trandate."'";

      // SHIPPING FEE
      $sql_shipping = "SELECT b.delivery_amount, a.reference_num
        FROM app_sales_order_details a
        INNER JOIN app_order_details_shipping b ON a.reference_num = b.reference_num
        AND a.sys_shop = b.sys_shop WHERE a.status = 1 AND b.status = 1";
      if(ini() == "jcww"){
        if(strtotime($trandate) <= strtotime('2021-05-20')){
          $sql_shipping .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }else{
          // $sql_shipping .= " AND DATE(a.date_shipped) = '".$trandate."'";
          $sql_shipping .= " AND DATE(a.date_confirmed) = '".$trandate."' AND a.isconfirmed = 1";
        }
      }

      // VOUCHER AMOUNT
      $sql_voucher = "SELECT b.amount, a.reference_num FROM app_sales_order_details a
        INNER JOIN app_order_payment b ON a.reference_num = b.order_ref_num
        WHERE payment_type = 'toktokmall' AND a.status = 1 AND b.status = 1";
      if(ini() == "jcww"){
        if(strtotime($trandate) <= strtotime('2021-05-20')){
          $sql_voucher .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }else{
          $sql_voucher .= " AND DATE(a.date_confirmed) = '".$trandate."' AND a.isconfirmed = 1";
          // $sql_shipping .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }
      }

      // REFCOM TOTALAMOUNT
      $sql_refcom = "SELECT (b.total_amount * b.refcom_rate) as refcom, a.reference_num FROM app_sales_order_details a
        INNER JOIN app_sales_order_logs b ON a.id = b.order_id
        WHERE a.status = 1 AND b.status = 1";
      if(ini() == "jcww"){
        if(strtotime($trandate) <= strtotime('2021-05-20')){
          $sql_refcom .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }else{
          $sql_refcom .= " AND DATE(a.date_confirmed) = '".$trandate."' AND a.isconfirmed = 1";
          // $sql_shipping .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }
      }

      // REFERRAL CODE
      $sql_referral = "SELECT b.referral_code, a.reference_num FROM app_sales_order_details a
        INNER JOIN app_referral_codes b ON a.reference_num = b.order_reference_num
        AND a.status = 1 AND b.status = 1";
      if(ini() == "jcww"){
        if(strtotime($trandate) <= strtotime('2021-05-20')){
          $sql_referral .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }else{
          $sql_referral .= " AND DATE(a.date_confirmed) = '".$trandate."' AND a.isconfirmed = 1";
          // $sql_shipping .= " AND DATE(a.date_shipped) = '".$trandate."'";
        }
      }

      $sql = "SELECT a.date_shipped as trandate, a.reference_num as refnum, a.sys_shop,
        a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher,
        a.id order_id, a.user_id
        FROM app_sales_order_details a
        LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum";

      if(strtotime($trandate) <= strtotime('2021-05-20')){
        $sql .= " WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_shipped) = '".$trandate."'
          AND a.status = 1";
      }else{
        $sql .= " WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_confirmed) = '".$trandate."'
          AND a.status = 1 AND a.isconfirmed = 1";
        // $sql .= " WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_shipped) = '".$trandate."'
        //   AND a.status = 1";
      }



    }


    // Per branch billing
    if($per_branch_billing == 1){
      // Main Branch
      if($branch_id == 0){
        // $sql .= " AND a.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND a.sys_shop = '".$sys_shop. "')";
        $sql .= " AND b.branchid = 0";
      }

      if($branch_id != 0){
        $sql .= " AND b.branchid != 0 AND b.branchid = ".$branch_id."";
      }

    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    $query = $this->db->query($sql);
    // $main_query     = $query->result_array();
    $billing_query  = $this->db->query($sql_billing_logs)->result_array();
    $shipping_query = $this->db->query($sql_shipping)->result_array();
    $voucher_query  = $this->db->query($sql_voucher)->result_array();
    $refcom_query   = $this->db->query($sql_refcom)->result_array();
    $referral_query = $this->db->query($sql_referral)->result_array();

    $data = array();
    $total_shippingfee = 0;
    $total_amount = 0;
    $total_amount_w_voucher = 0;
    $total_netpay = 0;

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $processfee     = filter_process_fee($billing_query,array('order_id' => $row['order_id']));
      $shippingfee    = filter_shippingfee($shipping_query,array('reference_num' => $row['refnum']));
      $voucher_amount = filter_voucher($voucher_query,array('reference_num' => $row['refnum']));
      $refcom         = filter_refcom($refcom_query,array('reference_num' => $row['refnum']));
      $referral       = filter_referral($referral_query,array('reference_num' => $row['refnum']));
      $shippingfee = ($shippingfee == '') ? 0 : $shippingfee;
      $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
      $total_shippingfee += $shippingfee;

        // if(floatval($row['refcom_totalamount']) > 0){
        //   $row['amount']= floatval($row['refcom_totalamount']);
        //   $row['total_amount_w_voucher'] = floatval($row['refcom_totalamount']) - floatval($row['voucher_amount']);
        // }

      $total_amount += $row["amount"];
      $total_amount_w_voucher += floatval($row['total_amount_w_voucher']);
      $total_netpay += ($row["total_amount_w_voucher"] - $processfee);

      $order_type = 'Regular Order';
      if($row['user_id'] != 0){
        $order_type = "Thru OF Login";
      }

      if($referral != 0){
        $order_type = "Via OF Shoplink";
      }

      $t_date = new Datetime($row['trandate']);
      $nestedData[] = $t_date->format('M d, Y h:i:m');
      $nestedData[] = $row["refnum"];
      $nestedData[] = $row["payrefnum"];
      $nestedData[] = $order_type;
      $nestedData[] = '<span class="float-right">'.number_format($row["amount"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($voucher_amount,2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($row["total_amount_w_voucher"],2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($shippingfee,2).'</span>';
      $nestedData[] = '<span class="float-right">'.number_format($refcom,2).'</span>';
      if($this->loginstate->get_access()['billing']['admin_view'] == 1){
        $nestedData[] = '<span class="float-right">'.number_format($processfee,2).'</span>';
      }else{
        $nestedData[] = '<span class="float-right">'.number_format($processfee + $row['refcom_totalamount'],2).'</span>';
      }
      $nestedData[] = '<span class="float-right">'.number_format((($row["total_amount_w_voucher"] - $refcom) - $processfee) + ($shippingfee),2).'</span>';

      if($this->loginstate->get_access()['billing']['admin_view'] == 1){
        $total_processfee_logs = number_format($processfee,2);

      }else{
        $total_processfee_logs = number_format($processfee + $refcom,2);
      }

      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view_logs"
              data-orderid = "'.en_dec('en',$row['order_id']).'"
              data-refnum = "'.$row['refnum'].'"
              data-totalamount = "'.number_format(($row['amount'] - $refcom),2).'"
              data-processfee = "'.$total_processfee_logs.'"
              data-netamount = "'.number_format((($row['amount'] - $refcom) - $processfee),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View logs
            </a>
			  	</div>
  			</div>
      ';

      $data[] = $nestedData;
    }
    // print_r($data);
    // $nestedData = array();
		// $nestedData[] = '<strong>Total</strong>';
		// $nestedData[] = '';
		// $nestedData[] = '';
		// $nestedData[] = '';
    // if(c_international() == 1 && $curr != 'PHP'){
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$totalamount.'/ '.$row['currency'].' '.number_format($total_amount_oc,2).'</strong></span>';
    //   $nestedData[] = '';
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$total_amount_w_voucher.'/ '.$row['currency'].' '.number_format($total_amount_w_voucher_oc,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.number_format($delivery_amount,2).'/ '.$row['currency'].' '.number_format($total_shippingfee_oc,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.'PHP '.number_format($total_processfee,2).'/ '.$row['currency'].' '.number_format($total_fee_oc,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.'PHP '.$netamount.'/ '.$row['currency'].' '.number_format($total_netpay_oc,2).'</strong></span>';
    // }else{
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($total_amount,2).'</strong></span>';
    //   $nestedData[] = '';
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($total_amount_w_voucher,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($delivery_amount,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.$total_comrate.'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.number_format($total_processfee,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
    // }
    // $nestedData[] = '';
		// $data[] = $nestedData;
    // print_r($data);
    // die();

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_breakdown_toktokmall($search,$shop,$trandate,$ratetype,$processrate,$branch_id,$per_branch_billing,$totalamount,$processfee,$netamount,$delivery_amount,$total_comrate){
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
            4 => 'shippingfee',
            5 => 'voucher_amount',
            6 => 'total_amount_w_voucher',
            7 => 'shippingfee',
            8 => 'refcom_totalamount',
            9 => 'processfee'
		);

    // Per shop billing
    if(c_international() == 1){
      $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, a.currency, a.exrate_n_to_php, @sys_shop := a.sys_shop,
        a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher, a.id as order_id, a.total_amount_oc, a.user_id,
        @process_fee := (SELECT CONCAT(SUM(processfee),',',SUM(processfee_oc)) FROM sys_billing_logs WHERE order_id = a.id AND status = 1) as processfee,
        @shippingfee := (SELECT (CASE WHEN converted_delivery_amount IS NULL THEN CONCAT(delivery_amount,',','0.00') ELSE CONCAT(delivery_amount,',',converted_delivery_amount) END) as shipping_fee FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
        @voucher_amount := (SELECT amount FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
        @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
        FROM `app_sales_order_details` a
        LEFT JOIN `app_order_branch_details` b ON a.reference_num = b.order_refnum
        WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' AND a.status = 1";
    }else{
      // $sql =" SELECT a.date_shipped as trandate, a.reference_num as refnum, @sys_shop := a.sys_shop,
      //   a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher, a.id as order_id,
      //   @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
      //   @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
      //   @voucher_amount := (SELECT amount FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount
      //   FROM `app_sales_order_details` a
      //   LEFT JOIN `app_order_branch_details` b ON a.reference_num = b.order_refnum
      //   WHERE a.sys_shop = '".$sys_shop. "' AND DATE(a.date_shipped) = '".$trandate."' AND a.status = 1";

      if(ini() == "jcww"){
        $sql = "SELECT a.date_shipped as trandate, a.reference_num as refnum, a.sys_shop,
          a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher,
          a.id order_id, a.user_id, a.srp_totalamount,
          @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id AND status = 1) as processfee,
          @comrate := (SELECT SUM(comrate) FROM sys_billing_logs WHERE order_id = a.id AND status = 1) as comrate,
          @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
          @voucher_amount := (SELECT SUM(amount) FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
          @refcom_totalamount := (SELECT ROUND(SUM(total_amount * refcom_rate),2) FROM app_sales_order_logs WHERE order_id = a.id AND status = 1) as refcom_totalamount,
          @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
          FROM app_sales_order_details a
          LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum AND b.shopid = '".$sys_shop."'
          WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_confirmed) = '".$trandate."' AND a.isconfirmed = 1
            AND a.status = 1";
      }else{
        $sql = "SELECT a.date_shipped as trandate, a.reference_num as refnum, a.sys_shop,
          a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher,
          a.id order_id, a.user_id, a.srp_totalamount, a.account_type, a.actual_totalamount,
          @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
          @comrate := (SELECT SUM(comrate) FROM sys_billing_logs WHERE order_id = a.id) as comrate,
          @shippingfee := (SELECT IF(handle_shipping_promo='1',original_shipping_fee,delivery_amount) FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
          @voucher_amount := (SELECT SUM(amount) FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
          @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
          FROM app_sales_order_details a
          LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum AND b.shopid = '".$sys_shop."'
          WHERE a.sys_shop = '".$sys_shop."' AND DATE(a.date_shipped) = '".$trandate."'
            AND a.status = 1";
      }

    }


    // Per branch billing
    if($per_branch_billing == 1){
      // Main Branch
      if($branch_id == 0){
        // $sql .= " AND a.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND a.sys_shop = '".$sys_shop. "')";
        $sql .= " AND b.branchid = 0";
      }

      if($branch_id != 0){
        $sql .= " AND b.branchid != 0 AND b.branchid = ".$branch_id."";
      }

    }


    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    $query = $this->db->query($sql);
    // print_r($query);
    // print_r($query->result_array());
    // die();

    $data = array();
    $total_shippingfee = 0;
    $total_amount = 0;
    $total_amount_w_voucher = 0;
    $total_netpay = 0;
    if(c_international() == 1){
      $total_shippingfee_oc = 0;
      $total_amount_oc = 0;
      $total_fee_oc = 0;
      $total_netpay_oc = 0;
      $total_amount_w_voucher_oc = 0;
      $curr = "PHP";
    }

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      // $mystery_amount = $this->check_mystery_coupon($row['order_id']);
      // $srp_totalamount = ($mystery_amount != 0) ? $mystery_amount : floatval($row['srp_totalamount']);

      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != 'PHP'){
        // echo $row['shippingfee'];
        $shippingfee_arr = explode(',',$row['shippingfee']);
        $curr = $row['currency'];
        $row['total_amount_w_voucher'] = (floatval($row['total_amount_w_voucher']) == 0 && floatval($row['voucher_amount'] == 0)) ? $row['total_amount'] : $row['total_amount_w_voucher'];
        // print_r($shippingfee_arr);
        // die();
        $shippingfee = ($shippingfee_arr[0] == '') ? 0 : $shippingfee_arr[0];
        $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
        $total_shippingfee += $shippingfee;
        $shippingfee_oc = $shippingfee_arr[1];
        $fee = explode(',',$row['processfee']);
        $fee_oc = (isset($fee[1])) ? $fee[1] : 0;
        $total_fee_oc += $fee_oc;
        $total_amount_oc += floatval($row['total_amount_oc']);
        $total_shippingfee_oc += $shippingfee_oc;
        $total_netpay_oc += ($row['total_amount_oc'] - $fee_oc) + $shippingfee_oc;
        $row['processfee'] = $fee[0];
      }else if(c_international() == 1 && $row['currency'] == null){
        $shippingfee_arr = explode(',',$row['shippingfee']);
        $shippingfee = ($shippingfee_arr[0] == '') ? 0 : $shippingfee_arr[0];
        $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
        $fee = explode(',',$row['processfee']);
        $row['processfee'] = floatval($fee[0]);

      }else{
        $shippingfee = ($row['shippingfee'] == '') ? 0 : $row['shippingfee'];
        $shippingfee = ($delivery_amount == '0.00') ? 0.00 : $shippingfee;
        $total_shippingfee += $shippingfee;
      }

      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != 'PHP'){
        $total_amount += $row["amount"];
        $total_amount_w_voucher += floatval($row['total_amount_w_voucher']);
        $total_amount_w_voucher_oc += php_to_n(floatval($row['total_amount_w_voucher']),$row['exrate_n_to_php']);
        $total_netpay += ($row["total_amount_w_voucher"] - $fee[0]);
      }else{
        // if(floatval($row['refcom_totalamount']) > 0){
        //   $row['amount']= floatval($row['refcom_totalamount']);
        //   $row['total_amount_w_voucher'] = floatval($row['refcom_totalamount']) - floatval($row['voucher_amount']);
        // }

        $total_amount += $row["amount"];
        $total_amount_w_voucher += floatval($row['total_amount_w_voucher']);
        $total_netpay += ($row["total_amount_w_voucher"] - $row['processfee']);
      }

      $order_type = 'Regular Order';
      if($row['user_id'] != 0 && $row['account_type'] == 'jc'){
        $order_type = "Thru OF Login";
      }

      if($row['referral_code'] != "" || $row['referral_code'] != null){
        $order_type = "Via OF Shoplink";
      }

      $t_date = new Datetime($row['trandate']);
      $nestedData[] = $t_date->format('M d, Y h:i:m');
      $nestedData[] = $row["refnum"];
      $nestedData[] = $row["payrefnum"];
      $nestedData[] = $order_type;
      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != "PHP"){
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["amount"],2).'/ '.$row['currency'].' '.number_format($row['total_amount_oc'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["voucher_amount"],2).'/ '.$row['currency'].' '.number_format(php_to_n($row['voucher_amount'],$row['exrate_n_to_php']),2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["total_amount_w_voucher"],2).'/ '.$row['currency'].' '.number_format(php_to_n($row['voucher_amount'],$row['exrate_n_to_php']),2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($shippingfee,2).'/ '.$row['currency'].' '.number_format($shippingfee_oc,2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($fee[0],2).'/ '.$row['currency'].' '.number_format($fee_oc,2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format(($row["amount"] - $fee[0]) + ($shippingfee),2).'/ '.$row['currency'].' '.number_format(($row['total_amount_oc'] - $fee_oc) + $shippingfee_oc,2).'</span>';
      }else{
        $nestedData[] = '<span class="float-right">'.number_format($row['srp_totalamount'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row['actual_totalamount'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["amount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["voucher_amount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row['actual_totalamount'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($shippingfee,2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row['comrate'],2).'</span>';
        if($this->loginstate->get_access()['billing']['admin_view'] == 1){
          $nestedData[] = '<span class="float-right">'.number_format(($row['processfee']),2).'</span>';
        }else{
          $nestedData[] = '<span class="float-right">'.number_format(($row['processfee'] + $row['comrate']),2).'</span>';
        }
        $nestedData[] = '<span class="float-right">'.number_format(($row['actual_totalamount'] - ($row['processfee'] + $row['comrate'])) + ($shippingfee),2).'</span>';
      }

      if($this->loginstate->get_access()['billing']['admin_view'] == 1){
        $logs_total_process_fee = number_format($row['processfee'],2);
      }else{
        $logs_total_process_fee = number_format($row['processfee'] + $row['comrate'],2);
      }

      $nestedData[] =
      '
        <div class="dropdown text-center">
  				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
  				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view_logs"
              data-orderid = "'.en_dec('en',$row['order_id']).'"
              data-refnum = "'.$row['refnum'].'"
              data-total_srp = "'.number_format(($row['actual_totalamount']),2).'"
              data-total_actual = "'.number_format($row['actual_totalamount'],2).'"
              data-total_comrate = "'.number_format(($row['comrate']),2).'"
              data-totalamount = "'.number_format(($row['amount']),2).'"
              data-processfee = "'.$logs_total_process_fee.'"
              data-netamount = "'.number_format(($row['actual_totalamount'] - ($row['processfee'] + $row['comrate'])),2).'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View logs
            </a>
			  	</div>
  			</div>
      ';

      $data[] = $nestedData;
    }
    // print_r($data);
    // $nestedData = array();
		// $nestedData[] = '<strong>Total</strong>';
		// $nestedData[] = '';
		// $nestedData[] = '';
		// $nestedData[] = '';
    // if(c_international() == 1 && $curr != 'PHP'){
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$totalamount.'/ '.$row['currency'].' '.number_format($total_amount_oc,2).'</strong></span>';
    //   $nestedData[] = '';
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$total_amount_w_voucher.'/ '.$row['currency'].' '.number_format($total_amount_w_voucher_oc,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.'PHP '.number_format($delivery_amount,2).'/ '.$row['currency'].' '.number_format($total_shippingfee_oc,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.'PHP '.number_format($total_processfee,2).'/ '.$row['currency'].' '.number_format($total_fee_oc,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.'PHP '.$netamount.'/ '.$row['currency'].' '.number_format($total_netpay_oc,2).'</strong></span>';
    // }else{
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($total_amount,2).'</strong></span>';
    //   $nestedData[] = '';
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($total_amount_w_voucher,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.number_format($delivery_amount,2).'</strong></span>';
    //   $nestedData[] = '<span class="float-right"><strong>'.$total_comrate.'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.number_format($total_processfee,2).'</strong></span>';
  	// 	$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
    // }
    // $nestedData[] = '';
		// $data[] = $nestedData;
    // print_r($data);
    // die();

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
      WHERE order_id = $order_id AND a.status = 1";


    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();

    if(c_international() == 1){
      $total_amount_oc = 0;
      $total_processfee_oc = 0;
      $total_netpay_oc = 0;
      $curr = "PHP";
    }

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != 'PHP'){
        $total_amount_oc += $row['totalamount_oc'];
        $total_processfee_oc += $row['processfee_oc'];
        $total_netpay_oc += $row['netamount_oc'];
        $curr = $row['currency'];

        $nestedData[] = $row["product_name"];
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["price"],2).'/ '.$row['currency'].' '.number_format($row['price_oc'],2).'</span>';
        $nestedData[] = ($row['ratetype'] == 'p') ? 'Percentage' : 'Fix';
        $nestedData[] = ($row['ratetype'] == 'p') ? ($row['processrate'] * 100).' %' : '<span class="float-right">'.number_format($row["processrate"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.$row["quantity"].'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["totalamount"],2).'/ '.$row['currency'].' '.number_format($row['totalamount_oc'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["processfee"],2).'/ '.$row['currency'].' '.number_format($row['processfee_oc'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["netamount"],2).'/ '.$row['currency'].' '.number_format($row['netamount_oc'],2).'</span>';
      }else{
        $nestedData[] = $row["product_name"];
        $nestedData[] = '<span class="float-right">'.number_format($row["price"],2).'</span>';
        $nestedData[] = ($row['ratetype'] == 'p') ? 'Percentage' : 'Fix';
        $nestedData[] = ($row['ratetype'] == 'p') ? ($row['processrate'] * 100).' %' : '<span class="float-right">'.number_format($row["processrate"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.$row["quantity"].'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["totalamount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["processfee"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["netamount"],2).'</span>';
      }

      $data[] = $nestedData;
    }

    $nestedData = array();
		$nestedData[] = '<strong>Total</strong>';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';

    if(c_international() == 1 && $curr != 'PHP'){
      $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$totalamount.'/ '.$row['currency'].' '.number_format($total_amount_oc,2).'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$processfee.'/ '.$row['currency'].' '.number_format($total_processfee_oc,2).'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$netamount.'/ '.$row['currency'].' '.number_format($total_netpay_oc,2).'</strong></span>';

    }else{
      $nestedData[] = '<span class="float-right"><strong>'.$totalamount.'</strong></span>';
  		$nestedData[] = '<span class="float-right"><strong>'.$processfee.'</strong></span>';
  		$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
    }

		$data[] = $nestedData;

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_billing_logs_toktokmall($order_id,$totalamount,$processfee,$netamount,$comrate){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;
    $order_id = $this->db->escape($order_id);
		$columns = array(
            0 => 'product_name',
            1 => 'srp_amount',
            2=>  'price',
            3 => 'quantity',
            4 => 'srp_totalamount',
            5 => 'ratetype',
            6 => 'processrate',
            7 => 'comrate',
            8 => 'processfee',
            9 => 'netamount'
		);

		$sql=" SELECT a.*, b.itemname, CONCAT(b.itemname,'(',b.otherinfo,')') as product_name,
      b.parent_product_id, c.shopname,
      @parent_name := (SELECT itemname FROM sys_products WHERE Id = b.parent_product_id) as parent_name,
      @refcom_amount := (SELECT refcom_amount FROM app_sales_order_logs WHERE order_id = a.order_id AND product_id = a.product_id) as refcom_amount
      FROM sys_billing_logs a
      LEFT JOIN sys_products b ON a.product_id = b.Id
      LEFT JOIN sys_shops c ON a.sys_shop = c.id
      WHERE order_id = $order_id AND a.status = 1";


    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();

    if(c_international() == 1){
      $total_amount_oc = 0;
      $total_processfee_oc = 0;
      $total_netpay_oc = 0;
      $curr = "PHP";
    }

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $itemname = ($row['parent_product_id'] == null || $row['parent_product_id'] == "")
      ? $row['product_name']
      : $row['parent_name']." [".$row['itemname']."]";

      if(c_international() == 1 && $row['currency'] != null && $row['currency'] != 'PHP'){
        $total_amount_oc += $row['totalamount_oc'];
        $total_processfee_oc += $row['processfee_oc'];
        $total_netpay_oc += $row['netamount_oc'];
        $curr = $row['currency'];

        $nestedData[] = $row["product_name"];
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["price"],2).'/ '.$row['currency'].' '.number_format($row['price_oc'],2).'</span>';
        $nestedData[] = ($row['ratetype'] == 'p') ? 'Percentage' : 'Fix';
        $nestedData[] = ($row['ratetype'] == 'p') ? ($row['processrate'] * 100).' %' : '<span class="float-right">'.number_format($row["processrate"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.$row["quantity"].'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["totalamount"],2).'/ '.$row['currency'].' '.number_format($row['totalamount_oc'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["processfee"],2).'/ '.$row['currency'].' '.number_format($row['processfee_oc'],2).'</span>';
        $nestedData[] = '<span class="float-right">'.'PHP '.number_format($row["netamount"],2).'/ '.$row['currency'].' '.number_format($row['netamount_oc'],2).'</span>';
      }else{
        $nestedData[] = $itemname;
        $nestedData[] = '<span class="float-right">'.number_format($row["srp_amount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["actual_amount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format(($row['refcom_amount'] > 0) ? $row['actual_amount'] : $row["price"],2).'</span>';
        $nestedData[] = ($row['ratetype'] == 'p') ? 'Percentage' : 'Fix';
        $nestedData[] = ($row['ratetype'] == 'p') ? ($row['processrate'] * 100).' %' : '<span class="float-right">'.number_format($row["processrate"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.$row["quantity"].'</span>';
        // $nestedData[] = '<span class="float-right">'.number_format($row["srp_totalamount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["actual_totalamount"],2).'</span>';
        $nestedData[] = '<span class="float-right">'.number_format($row["comrate"],2).'</span>';
        if($this->loginstate->get_access()['billing']['admin_view'] == 1){
          $nestedData[] = '<span class="float-right">'.number_format($row["processfee"],2).'</span>';
        }else{
          $nestedData[] = '<span class="float-right">'.number_format($row["processfee"] + $row['comrate'],2).'</span>';
        }
        $nestedData[] = '<span class="float-right">'.number_format($row["netamount"],2).'</span>';
      }

      $data[] = $nestedData;
    }

    $nestedData = array();
		$nestedData[] = '<strong>Total</strong>';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';
		$nestedData[] = '';

    if(c_international() == 1 && $curr != 'PHP'){
      $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$totalamount.'/ '.$row['currency'].' '.number_format($total_amount_oc,2).'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$processfee.'/ '.$row['currency'].' '.number_format($total_processfee_oc,2).'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.'PHP '.$netamount.'/ '.$row['currency'].' '.number_format($total_netpay_oc,2).'</strong></span>';

    }else{
      $nestedData[] = '';
      $nestedData[] = '';
      $nestedData[] = '<span class="float-right"><strong>'.$totalamount.'</strong></span>';
      $nestedData[] = '<span class="float-right"><strong>'.$comrate.'</strong></span>';
  		$nestedData[] = '<span class="float-right"><strong>'.$processfee.'</strong></span>';
  		$nestedData[] = '<span class="float-right"><strong>'.$netamount.'</strong></span>';
    }

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
		$query=" SELECT a.*, DATE(a.trandate) as trandate, b.shopname, c.description as pay_type,
        b.shopcode
        FROM sys_billing as a
				LEFT JOIN sys_shops as b ON a.syshop = b.id
				LEFT JOIN sys_payment_type as c ON a.paytype = c.id
				WHERE a.id = ? ";
		return $this->db->query($query, $id);
	}

  public function get_billing_thru_trandate($trandate){
    $trandate = $this->db->escape($trandate);
    $sql = "SELECT trandate FROM sys_billing WHERE DATE(trandate) = $trandate AND status = 1";
    return $this->db->query($sql);
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

  public function get_shop_branch($shop){
    $shop = $this->db->escape($shop);
    $sql = "SELECT b.branchname, b.branchcode, a.branchid
      FROM sys_branch_mainshop a
      INNER JOIN sys_branch_profile b ON a.branchid = b.id
      WHERE a.status = 1 AND b.status = 1 AND a.mainshopid = $shop
      ORDER BY b.branchname ASC";
    return $this->db->query($sql);
  }

  public function get_options() {
		$query="SELECT * FROM sys_payment_type WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

  public function get_amount_to_pay($billcode) {
    if(ini() == "toktokmall"){
      // $query="SELECT (netamount + delivery_amount + total_whtax) as netamount FROM sys_billing WHERE billcode = ? AND status = 1";
      $query="SELECT (netamount + delivery_amount) as netamount FROM sys_billing WHERE billcode = ? AND status = 1";
    }else{
      $query="SELECT (netamount + delivery_amount) as netamount FROM sys_billing WHERE billcode = ? AND status = 1";
    }

		$result = $this->db->query($query, $billcode);
		if($result->num_rows() > 0)
			return $result->row_array();
		else
			return "";
	}

  public function get_unsettled_amount($billcode){
    $query="SELECT remaining_to_pay as unsettled_amount, syshop, branch_id FROM sys_billing WHERE billcode = ? AND status = 1";
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

  public function tag_payment($data,$id){
    $this->db->update('sys_billing',$data,array('billcode' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
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
    try{
      // set process billing
      $this->set_process_billing($trandate);
      // get process billing
      $process = $this->get_process_billing($trandate);
      if($process->num_rows() == 0){
        $check_sql = "SELECT id FROM sys_billing WHERE status=1 AND trandate=?";
        $check_query = $this->db->query($check_sql,array($trandate));
        $c = $check_query->row_array();
        $main_sql = "SELECT GROUP_CONCAT(syshop SEPARATOR ',') as sid FROM sys_billing WHERE status = 1 AND trandate = ?";
        $main_data = array($trandate);
    		$res = $this->db->query($main_sql,$main_data);
    		$r = $res->row_array();
        $shops_billing_per_branch = array();
        $shopid_arr = explode(",",$r['sid']);

        if($c["id"] == ""){

          // Query per shop billing
          // $sql = "SELECT sum(od.total_amount) AS totalamount, od.sys_shop AS shopid,
          //     @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
          //     @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type,
          //     @billing_perbranch := (SELECT generatebilling FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_perbranch,
          //     @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count,
          //     @shop_rate := (SELECT rateamount FROM sys_shop_rate WHERE syshop = od.sys_shop AND status = 1) as shop_rate,
          //     @shop_ratetype := (SELECT ratetype FROM sys_shop_rate WHERE syshop = od.sys_shop AND status = 1) as shop_ratetype,
          //     @prepayment := (SELECT prepayment FROM sys_shops WHERE id = od.sys_shop AND status = 1) as prepayment,
          //     @sid := (SELECT id FROM sys_shops WHERE id = od.sys_shop AND status = 1) as sid
          // 		FROM app_sales_order_details od
          // 		WHERE od.payment_status=1
          // 			AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
          // 			AND (date(od.date_shipped) BETWEEN ? AND ?)
          // 		GROUP BY od.sys_shop ORDER BY sys_shop ASC";
          if(c_international() == 1){
            $sql = "SELECT SUM(od.total_amount_w_voucher) AS totalamount, od.sys_shop AS shopid, SUM(c.delivery_amount) as shipping_fee,
              SUM(c.converted_delivery_amount) as converted_shipping_fee, b.billing_type as billing_type,
              b.generatebilling as billing_perbranch, e.rateamount as shop_rate, e.ratetype as shop_ratetype, b.prepayment as prepayment, od.sys_shop as sid,
              @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count
              FROM app_sales_order_details od
              LEFT JOIN sys_shops b ON od.sys_shop = b.id
              LEFT JOIN app_order_details_shipping c ON od.sys_shop = c.sys_shop AND od.reference_num = c.reference_num
              LEFT JOIN sys_shop_rate e ON od.sys_shop = e.syshop
              WHERE b.status = 1 AND c.status = 1 AND e.status = 1 AND od.payment_status = 1 AND od.order_status IN ('f','s')
              AND od.payment_method != 'COD' AND (date(od.date_shipped) BETWEEN ? AND ?)
              GROUP BY od.sys_shop ORDER BY od.sys_shop ASC";
          }else{
            $sql = "SELECT SUM(od.total_amount_w_voucher) AS totalamount, od.sys_shop AS shopid, SUM(c.delivery_amount) as shipping_fee, b.billing_type as billing_type,
              b.generatebilling as billing_perbranch, e.rateamount as shop_rate, e.ratetype as shop_ratetype, b.prepayment as prepayment, od.sys_shop as sid,
              @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count
              FROM app_sales_order_details od
              LEFT JOIN sys_shops b ON od.sys_shop = b.id
              LEFT JOIN app_order_details_shipping c ON od.sys_shop = c.sys_shop AND od.reference_num = c.reference_num
              LEFT JOIN sys_shop_rate e ON od.sys_shop = e.syshop
              WHERE b.status = 1 AND c.status = 1 AND e.status = 1 AND od.payment_status = 1 AND od.order_status IN ('f','s')
              AND od.payment_method != 'COD' AND (date(od.date_shipped) BETWEEN ? AND ?)
              GROUP BY od.sys_shop ORDER BY od.sys_shop ASC";
          }


          $data = array($trandate,$trandate);
          $numrows = $this->db->query($sql,$data);
          // print_r($numrows);
          // die();
          // Query for shop logs
          $sql_logs = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
            b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            LEFT JOIN sys_products b ON a.product_id = b.Id
            WHERE order_id IN ((SELECT * FROM (SELECT id FROM app_sales_order_details WHERE payment_status = 1 AND order_status IN ('f','s') AND (DATE(date_shipped) BETWEEN ? AND ?)) as sys_shop))
            ORDER BY b.sys_shop ASC";
          // Query per branch billing logs
          // $sql_logs_perbranch = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop, @sys_shop := b.sys_shop,
          //   b.Id as product_id, b.admin_isset
          //   FROM app_sales_order_logs a
          //   INNER JOIN sys_products b ON a.product_id = b.Id
          //   WHERE order_id IN ((SELECT * FROM (SELECT c.id FROM app_sales_order_details c WHERE c.payment_status = 1 AND c.sys_shop = b.sys_shop AND c.order_status IN ('f','s') AND (DATE(c.date_shipped) BETWEEN ? AND ?) AND c.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND @sys_shop = c.sys_shop)) as sys_shop))
          //   ORDER BY b.sys_shop ASC";
          $sql_logs_perbranch = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop, @sys_shop := b.sys_shop,
            b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            INNER JOIN sys_products b ON a.product_id = b.Id
            WHERE order_id IN (SELECT c.id FROM app_sales_order_details c LEFT JOIN app_order_branch_details d ON c.reference_num = d.order_refnum WHERE d.shopid = c.sys_shop AND d.branchid = 0 AND DATE(c.date_shipped) BETWEEN ? AND ?)
            ORDER BY b.sys_shop ASC";
          // Query for branch breakdown
          $sql2 = "SELECT SUM(a.total_amount_w_voucher) as totalamount, a.sys_shop as shopid, b.branchid
            FROM app_sales_order_details a
            LEFT JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
            LEFT JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
            WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
            AND DATE(a.date_shipped) BETWEEN ? AND ? GROUP BY b.branchid ORDER BY b.branchid";
          // Query for branch breakdown logs
          $sql_logs2 = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
            d.branchid, b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            LEFT JOIN sys_products b ON a.product_id = b.Id
            LEFT JOIN app_sales_order_details c ON a.order_id = c.id
            LEFT JOIN sys_branch_orders d ON c.reference_num = d.orderid
            LEFT JOIN sys_branch_mainshop e ON d.branchid = e.branchid
            WHERE e.mainshopid = b.sys_shop AND
            DATE(c.date_shipped) BETWEEN ? AND ? AND c.order_status IN ('f','s')
            GROUP BY a.id
            ORDER BY d.branchid ASC";
          // Query for per branch logs
          $sql_logs3 = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
            d.branchid, b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            LEFT JOIN sys_products b ON a.product_id = b.Id
            LEFT JOIN app_sales_order_details c ON a.order_id = c.id
            LEFT JOIN sys_branch_orders d ON c.reference_num = d.orderid
            LEFT JOIN sys_branch_mainshop e ON d.branchid = e.branchid
            WHERE e.mainshopid = b.sys_shop AND
            DATE(c.date_shipped) BETWEEN ? AND ? AND c.order_status IN ('f','s')
            GROUP BY a.id
            ORDER BY d.branchid ASC";

          $query2 = $this->db->query($sql_logs,array($trandate,$trandate));
          $query3 = $this->db->query($sql_logs_perbranch,array($trandate,$trandate));

          if($numrows->num_rows() > 0){
            // start cron logs
            $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));

            $billno = $this->getNewBillNo();

            // $res = $this->db->query($sql,$data);
            $r = $numrows->result_array();
            $billing_logs_batchdata = array();

            foreach ($r AS $row){
              if($row["totalamount"] > 0){
                $shop_rate = $row['shop_rate'];
                $shop_ratetype = $row['shop_ratetype'];

                if(!in_array($row['sid'],$shopid_arr)){
                  // Billing per branch
                  if($row['billing_perbranch'] == 1 && $row['branch_count'] > 0){
                    // for checking of shops with billing per branch ON
                    array_push($shops_billing_per_branch,$row['shopid']);
                    // Query for billing per branch
                    if(c_international() == 1){
                      $per_branch_sql = "SELECT sum(od.total_amount_w_voucher) AS totalamount, SUM(b.delivery_amount) as shipping_fee,
                        SUM(b.converted_delivery_amount) as converted_shipping_fee, od.sys_shop AS shopid, od.id as orderid, @sys_shop := od.sys_shop,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details od
                        LEFT JOIN app_order_details_shipping b ON od.sys_shop = b.sys_shop AND od.reference_num = b.reference_num
                        WHERE od.payment_status=1
                        AND b.status = 1
                        AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
                        AND (date(od.date_shipped) BETWEEN ? AND ?) AND od.sys_shop = ?
                        AND od.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND @sys_shop = od.sys_shop)";
                    }else{
                      $per_branch_sql = "SELECT sum(od.total_amount_w_voucher) AS totalamount, SUM(b.delivery_amount) as shipping_fee,
                        od.sys_shop AS shopid, od.id as orderid, @sys_shop := od.sys_shop,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details od
                        LEFT JOIN app_order_details_shipping b ON od.sys_shop = b.sys_shop AND od.reference_num = b.reference_num
                        WHERE od.payment_status=1
                        AND b.status = 1
                        AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
                        AND (date(od.date_shipped) BETWEEN ? AND ?) AND od.sys_shop = ?
                        AND od.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND @sys_shop = od.sys_shop)";
                    }

                    // Main shop
                    $main_shop = $this->db->query($per_branch_sql, array($trandate,$trandate,$row['shopid']));
                    if($main_shop->num_rows() > 0){
                      $main_shop = $main_shop->row_array();
                      if(floatval($main_shop['totalamount']) > 0){
                        $total_amount = $main_shop['totalamount'];

                        $wallet = $this->get_shop_wallet($main_shop['shopid'])->num_rows();
                        $delivery_amount = ($main_shop['billing_type'] == 1 && $row['prepayment'] == 0) ? $main_shop['shipping_fee']: 0.00;
                        $total_fee = 0;
                        $netamount = 0;

                        if(c_international() == 1){
                          $total_amount_oc = 0;
                          $total_fee_oc = 0;
                          $netamount_oc = 0;
                          $delivery_amount_oc = ($main_shop['billing_type'] == 1 && $row['prepayment'] == 0) ? $main_shop['converted_shipping_fee']: 0.00;;
                        }

                        $billcode = $this->generateBillCode($billno,$main_shop["shopid"]);
                        $remarks = 'Settlement for transactions dated '.$trandate;

                        // processing fee
                        if($query3->num_rows() > 0){
                          foreach($query3->result_array() as $logs){
                            if($main_shop['shopid'] == $logs['sys_shop']){
                              $fee = 0;

                              // with product rate
                              if($logs['admin_isset'] == 1){
                                $fee = ($logs['ratetype'] == 'p')
                                ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                                : $logs['rate'] * $logs['quantity'];  // for fix rate type
                                $rate = $logs['rate'];
                                $ratetype = $logs['ratetype'];
                                // without product rate then fetch shop
                              }else{
                                $fee = ($shop_ratetype == 'p')
                                ? $shop_rate * $logs['total_amount']
                                : $shop_rate * $logs['quantity'];
                                $rate = $shop_rate;
                                $ratetype = $shop_ratetype;
                              }

                              if(c_international() == 1){
                                $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                                $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                                $total_amount_oc += php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                $total_fee_oc += php_to_n($fee,$logs['exrate_n_to_php']);
                              }

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
                                "ratetype" => $ratetype,
                                "processrate" => $rate,
                                "processfee" => $fee,
                                "netamount" => $logs['total_amount'] - $fee
                              );
                              if(c_international() == 1){
                                $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                                $billing_logs_data['processfee_oc'] = php_to_n($fee,$logs['exrate_n_to_php']);
                                $billing_logs_data['netamount_oc'] = floatval($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                                $billing_logs_data['exrate_n_to_php'] = $logs['exrate_n_to_php'];
                                $billing_logs_data['currency'] = $logs['currency'];
                              }

                              $billing_logs_batchdata[] = $billing_logs_data;
                            }
                          }
                        }

                        $netamount = $total_amount - $total_fee;
                        if(c_international() == 1){
                          $netamount_oc = $total_amount_oc - $total_fee_oc;
                        }
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

                        if(c_international() == 1){
                          $billing_data['delivery_amount_oc'] = $delivery_amount_oc;
                          $billing_data['totalamount_oc'] = $total_amount_oc;
                          $billing_data['processfee_oc'] = $total_fee_oc;
                          $billing_data['netamount_oc'] = $netamount_oc;
                        }
                        $this->db->insert('sys_billing',$billing_data);
                      }
                    }

                    // Branches
                    if(c_international() == 1){
                      $branch_sql = "SELECT SUM(a.total_amount_w_voucher) as totalamount, a.sys_shop as shopid, b.branchid,
                        SUM(d.delivery_amount) as shipping_fee, SUM(d.converted_delivery_amount) as converted_shipping_fee,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND status = 1 AND reference_num = a.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = a.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details a
                        INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
                        INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
                        LEFT JOIN app_order_details_shipping d ON a.sys_shop = d.sys_shop AND a.reference_num = d.reference_num
                        WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
                        AND d.status = 1
                        AND DATE(a.date_shipped) BETWEEN ? AND ? AND a.sys_shop = ? GROUP BY b.branchid";
                    }else{
                      $branch_sql = "SELECT SUM(a.total_amount_w_voucher) as totalamount, a.sys_shop as shopid, b.branchid,
                        SUM(d.delivery_amount) as shipping_fee,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND status = 1 AND reference_num = a.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = a.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details a
                        INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
                        INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
                        LEFT JOIN app_order_details_shipping d ON a.sys_shop = d.sys_shop AND a.reference_num = d.reference_num
                        WHERE c.mainshopid = a.sys_shop AND a.order_status IN ('f','s') AND a.payment_method != 'COD'
                        AND d.status = 1
                        AND DATE(a.date_shipped) BETWEEN ? AND ? AND a.sys_shop = ? GROUP BY b.branchid";
                    }


                    $branches = $this->db->query($branch_sql,array($trandate,$trandate,$row['shopid']));
                    if($branches->num_rows() > 0){
                      $branches = $branches->result_array();
                      foreach($branches as $branch){
                        if($branch['totalamount'] > 0 && $branch['branchid'] != 0){
                          $billcode = $this->generateBillCode($billno,$branch["shopid"]);
                          $billcode = $billcode.$branch['branchid'];
                          $remarks = 'Settlement for transactions dated '.$trandate;
                          $total_amount = $branch['totalamount'];

                          $wallet = $this->get_shop_wallet($branch['shopid'])->num_rows();
                          $delivery_amount = ($branch['billing_type'] == 1 && $row['prepayment'] == 0) ? $branch['shipping_fee']: 0.00;
                          $netamount = 0;
                          $total_fee = 0;

                          if(c_international() == 1){
                            $total_amount_oc = 0;
                            $total_fee_oc = 0;
                            $delivery_amount_oc = ($branch['billing_type'] == 1 && $row['prepayment'] == 0) ? $branch['converted_shipping_fee']: 0.00;;
                            $netamount_oc = 0;
                          }

                          $per_branch_logs = $this->db->query($sql_logs3,array($trandate,$trandate));
                          if($per_branch_logs->num_rows() > 0){
                            foreach($per_branch_logs->result_array() as $logs){
                              if($branch['shopid'] == $logs['sys_shop'] && $branch['branchid'] == $logs['branchid']){
                                $bfee = 0;
                                // with product rate
                                if($logs['admin_isset'] == 1){
                                  $bfee = ($logs['ratetype'] == 'p')
                                  ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                                  : $logs['rate'] * $logs['quantity'];  // for fix rate type
                                  $rate = $logs['rate'];
                                  $ratetype = $logs['ratetype'];
                                  // without product rate then fetch shop
                                }else{
                                  $bfee = ($shop_ratetype == 'p')
                                  ? $shop_rate * $logs['total_amount']
                                  : $shop_rate * $logs['quantity'];
                                  $rate = $shop_rate;
                                  $ratetype = $shop_ratetype;
                                }

                                if(c_international() == 1){
                                  $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                                  $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                                  $total_amount_oc += php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                  $total_fee_oc += php_to_n($bfee,$logs['exrate_n_to_php']);
                                }

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
                                  "ratetype" => $ratetype,
                                  "processrate" => $rate,
                                  "processfee" => $bfee,
                                  "netamount" => $logs['total_amount'] - $bfee
                                );

                                if(c_international() == 1){
                                  $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                  $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                                  $billing_logs_data['processfee_oc'] = php_to_n($bfee,$logs['exrate_n_to_php']);
                                  $billing_logs_data['netamount_oc'] = floatval($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                                  $billing_logs_data['exrate_n_to_php'] = $logs['exrate_n_to_php'];
                                  $billing_logs_data['currency'] = $logs['currency'];
                                }

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

                          if(c_international() == 1){
                            $billing_data['delivery_amount_oc'] = $delivery_amount_oc;
                            $billing_data['totalamount_oc'] = $total_amount_oc;
                            $billing_data['processfee_oc'] = $total_fee_oc;
                            $billing_data['netamount_oc'] = $netamount_oc;
                          }
                          $this->db->insert('sys_billing',$billing_data);

                        }
                      }
                    }

                  // Billing per shop
                  }else{
                    $total_amount = $row['totalamount'];

                    $wallet = $this->get_shop_wallet($row['shopid'])->num_rows();
                    $delivery_amount = ($row['billing_type'] == 1 && $row['prepayment'] == 0) ? $row['shipping_fee']: 0.00;
                    $total_fee = 0;
                    $netamount = 0;

                    if(c_international() == 1){
                      $total_amount_oc = 0;
                      $delivery_amount_oc = ($row['billing_type'] == 1 && $row['prepayment'] == 0) ? $row['converted_shipping_fee'] : 0.00;
                      $total_fee_oc = 0;
                      $netamount_oc = 0;
                    }

                    $billcode = $this->generateBillCode($billno,$row["shopid"]);
                    $remarks = 'Settlement for transactions dated '.$trandate;

                    // processing fee
                    if($query2->num_rows() > 0){
                      foreach($query2->result_array() as $logs){
                        if($row['shopid'] == $logs['sys_shop']){
                          $fee = 0;

                          // with product rate
                          if($logs['rate'] > 0 && $logs['admin_isset'] == 1){
                            $fee = ($logs['ratetype'] == 'p')
                            ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                            : $logs['rate'] * $logs['quantity'];  // for fix rate type
                            $rate = $logs['rate'];
                            $ratetype = $logs['ratetype'];
                            // without product rate then fetch shop
                          }else{
                            $fee = ($shop_ratetype == 'p')
                            ? $shop_rate * $logs['total_amount']
                            : $shop_rate * $logs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          if(c_international() == 1){
                            $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                            $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                            $total_amount_oc += php_to_n($logs['amount'],$logs['exrate_n_to_php']) * $logs['quantity'];
                            $total_fee_oc += php_to_n($fee,$logs['exrate_n_to_php']);
                          }


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
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $fee,
                            "netamount" => $logs['total_amount'] - $fee
                          );

                          if(c_international() == 1){
                            $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                            $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                            $billing_logs_data['processfee_oc'] = php_to_n($fee,$logs['exrate_n_to_php']);
                            $billing_logs_data['netamount_oc'] = floatval($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                            $billing_logs_data['exrate_n_to_php'] = $logs['exrate_n_to_php'];
                            $billing_logs_data['currency'] = $logs['currency'];
                          }
                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }
                    }

                    $netamount = $total_amount - $total_fee;
                    if(c_international() == 1){
                      $netamount_oc = floatval($total_amount_oc - $total_fee_oc);
                    }

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

                    if(c_international() == 1){
                      $billing_data['delivery_amount_oc'] = $delivery_amount_oc;
                      $billing_data['totalamount_oc'] = $total_amount_oc;
                      $billing_data['netamount_oc'] = $netamount_oc;
                      $billing_data['processfee_oc'] = $total_fee_oc;
                    }
                    $this->db->insert('sys_billing',$billing_data);
                  }
                }

              }
            }

            // var_dump($billing_logs_batchdata);
            if(count($billing_logs_batchdata) > 0){
              $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
            }

            ### === FOR BILLING PER BRANCH === ###
            $b_logs = $this->db->query($sql_logs2,array($trandate,$trandate));

            $branch_bills = $this->db->query($sql2,array($trandate,$trandate));
            if($branch_bills->num_rows() > 0){
              $billing_branch_batchdata = array();

              foreach ($branch_bills->result_array() as $row){
                if(!in_array($row['shopid'],$shopid_arr)){
                  // check if total amout greater than zero and shop is not per branch billing
                  if($row["totalamount"] > 0 && !in_array($row['shopid'],$shops_billing_per_branch)){
                    $billcode = $this->generateBillCode($billno,$row["shopid"]);
                    $remarks = 'Settlement for transactions dated '.$trandate;
                    $netamount = 0;
                    $total_fee = 0;
                    $total_amount = $row['totalamount'];

                    if(c_international() == 1){
                      $total_amount_oc = 0;
                      $total_fee_oc = 0;
                      $netamount_oc = 0;
                    }

                    if($b_logs->num_rows() > 0){
                      foreach($b_logs->result_array() as $logs){
                        if($row['shopid'] == $logs['sys_shop'] && $row['branchid'] == $logs['branchid']){
                          $bfee = 0;
                          // with product rate
                          if($logs['rate'] > 0 && $logs['admin_isset'] == 1){
                            $bfee = ($logs['ratetype'] == 'p')
                            ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                            : $logs['rate'] * $logs['quantity'];  // for fix rate type
                            $rate = $logs['rate'];
                            $ratetype = $logs['ratetype'];
                            // without product rate then fetch shop
                          }else{
                            $bfee = ($shop_ratetype == 'p')
                            ? $shop_rate * $logs['total_amount']
                            : $shop_rate * $logs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          if(c_international() == 1){
                            $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                            $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                            $total_amount_oc += php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                            $total_fee_oc += php_to_n($bfee,$logs['exrate_n_to_php']);
                          }

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
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $bfee,
                            "netamount" => $logs['total_amount'] - $bfee
                          );

                          if(c_international() == 1){
                            $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['price'];
                            $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                            $billing_logs_data['processfee_oc'] = php_to_n($bfee,$logs['exrate_n_to_php']);
                            $billing_logs_data['netamount_oc'] = php_to_n($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                            $billing_logs_data['exrate_n_to_php'] = $logs('exrate_n_to_php');
                            $billing_logs_data['currency'] = $logs('currency');
                          }
                          $billing_branch_batchdata[] = $billing_logs_data;
                        }
                      }

                      $netamount = $total_amount - $total_fee;
                      if(c_international() == 1){
                        $netamount_oc = $total_amount_oc - $total_fee_oc;
                      }
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

                    if(c_international() == 1){
                      $insert_data['totalamount_oc'] = $total_amount_oc;
                      $insert_data['processfee_oc'] = $total_fee_oc;
                      $insert_data['netamount_oc'] = $netamount_oc;
                    }
                    $this->db->insert('sys_billing_branch', $insert_data);
                  }
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
        // update process billing
        $this->update_process_billing($trandate);
      }

    }catch(Exception $e){

    }

    return $count;

  }//close processDailyMerchantPay

  public function processDailyMerchantPay_per_product_rate_confirmed($trandate){
		$count = 0;
		$todaydate = todaytime();
    try{
      // set process billing
      $this->set_process_billing($trandate);
      // get process billing
      $process = $this->get_process_billing($trandate);
      if($process->num_rows() == 0){
        $check_sql = "SELECT id FROM sys_billing WHERE status=1 AND trandate=?";
        $check_query = $this->db->query($check_sql,array($trandate));
        $c = $check_query->row_array();
        $main_sql = "SELECT GROUP_CONCAT(syshop SEPARATOR ',') as sid FROM sys_billing WHERE status = 1 AND trandate = ?";
        $main_data = array($trandate);
    		$res = $this->db->query($main_sql,$main_data);
    		$r = $res->row_array();
        $shops_billing_per_branch = array();
        $shopid_arr = explode(",",$r['sid']);

        if($c["id"] == ""){

          // Query per shop billing
          // $sql = "SELECT sum(od.total_amount) AS totalamount, od.sys_shop AS shopid,
          //     @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
          //     @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type,
          //     @billing_perbranch := (SELECT generatebilling FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_perbranch,
          //     @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count,
          //     @shop_rate := (SELECT rateamount FROM sys_shop_rate WHERE syshop = od.sys_shop AND status = 1) as shop_rate,
          //     @shop_ratetype := (SELECT ratetype FROM sys_shop_rate WHERE syshop = od.sys_shop AND status = 1) as shop_ratetype,
          //     @prepayment := (SELECT prepayment FROM sys_shops WHERE id = od.sys_shop AND status = 1) as prepayment,
          //     @sid := (SELECT id FROM sys_shops WHERE id = od.sys_shop AND status = 1) as sid
          // 		FROM app_sales_order_details od
          // 		WHERE od.payment_status=1
          // 			AND od.order_status IN ('f','s') AND od.payment_method != 'COD'
          // 			AND (date(od.date_shipped) BETWEEN ? AND ?)
          // 		GROUP BY od.sys_shop ORDER BY sys_shop ASC";
          if(c_international() == 1){
            $sql = "SELECT SUM(od.total_amount_w_voucher) AS totalamount, od.sys_shop AS shopid, SUM(c.delivery_amount) as shipping_fee,
              SUM(c.converted_delivery_amount) as converted_shipping_fee, b.billing_type as billing_type,
              b.generatebilling as billing_perbranch, e.rateamount as shop_rate, e.ratetype as shop_ratetype, b.prepayment as prepayment, od.sys_shop as sid,
              @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count
              FROM app_sales_order_details od
              LEFT JOIN sys_shops b ON od.sys_shop = b.id
              LEFT JOIN app_order_details_shipping c ON od.sys_shop = c.sys_shop AND od.reference_num = c.reference_num
              LEFT JOIN sys_shop_rate e ON od.sys_shop = e.syshop
              WHERE b.status = 1 AND c.status = 1 AND e.status = 1 AND od.payment_status = 1 AND od.isconfirmed = 1
              AND od.payment_method != 'COD' AND (date(od.date_confirmed) BETWEEN ? AND ?)
              GROUP BY od.sys_shop ORDER BY od.sys_shop ASC";
          }else{
            $sql = "SELECT SUM(od.total_amount_w_voucher) AS totalamount, od.sys_shop AS shopid, SUM(c.delivery_amount) as shipping_fee, b.billing_type as billing_type,
              b.generatebilling as billing_perbranch, e.rateamount as shop_rate, e.ratetype as shop_ratetype, b.prepayment as prepayment, od.sys_shop as sid,
              @branch_count := (SELECT COUNT(id) FROM sys_branch_mainshop WHERE mainshopid = od.sys_shop AND status = 1) as branch_count
              FROM app_sales_order_details od
              LEFT JOIN sys_shops b ON od.sys_shop = b.id
              LEFT JOIN app_order_details_shipping c ON od.sys_shop = c.sys_shop AND od.reference_num = c.reference_num
              LEFT JOIN sys_shop_rate e ON od.sys_shop = e.syshop
              WHERE b.status = 1 AND c.status = 1 AND e.status = 1 AND od.payment_status = 1 AND od.isconfirmed = 1
              AND od.payment_method != 'COD' AND (date(od.date_confirmed) BETWEEN ? AND ?)
              GROUP BY od.sys_shop ORDER BY od.sys_shop ASC";
          }


          $data = array($trandate,$trandate);
          $numrows = $this->db->query($sql,$data);
          // print_r($numrows);
          // die();
          // Query for shop logs
          $sql_logs = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
            b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            LEFT JOIN sys_products b ON a.product_id = b.Id
            WHERE order_id IN ((SELECT * FROM (SELECT id FROM app_sales_order_details WHERE payment_status = 1 AND isconfirmed = 1 AND (DATE(date_confirmed) BETWEEN ? AND ?)) as sys_shop))
            ORDER BY b.sys_shop ASC";
          // Query per branch billing logs
          // $sql_logs_perbranch = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop, @sys_shop := b.sys_shop,
          //   b.Id as product_id, b.admin_isset
          //   FROM app_sales_order_logs a
          //   INNER JOIN sys_products b ON a.product_id = b.Id
          //   WHERE order_id IN ((SELECT * FROM (SELECT c.id FROM app_sales_order_details c WHERE c.payment_status = 1 AND c.sys_shop = b.sys_shop AND c.order_status IN ('f','s') AND (DATE(c.date_shipped) BETWEEN ? AND ?) AND c.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND @sys_shop = c.sys_shop)) as sys_shop))
          //   ORDER BY b.sys_shop ASC";
          $sql_logs_perbranch = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop, @sys_shop := b.sys_shop,
            b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            INNER JOIN sys_products b ON a.product_id = b.Id
            WHERE order_id IN (SELECT c.id FROM app_sales_order_details c LEFT JOIN app_order_branch_details d ON c.reference_num = d.order_refnum WHERE d.shopid = c.sys_shop AND d.branchid = 0 AND DATE(c.date_confirmed) BETWEEN ? AND ?)
            ORDER BY b.sys_shop ASC";
          // Query for branch breakdown
          $sql2 = "SELECT SUM(a.total_amount_w_voucher) as totalamount, a.sys_shop as shopid, b.branchid
            FROM app_sales_order_details a
            LEFT JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
            LEFT JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
            WHERE c.mainshopid = a.sys_shop AND a.isconfirmed = 1 AND a.payment_method != 'COD'
            AND DATE(a.date_confirmed) BETWEEN ? AND ? GROUP BY b.branchid ORDER BY b.branchid";
          // Query for branch breakdown logs
          $sql_logs2 = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
            d.branchid, b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            LEFT JOIN sys_products b ON a.product_id = b.Id
            LEFT JOIN app_sales_order_details c ON a.order_id = c.id
            LEFT JOIN sys_branch_orders d ON c.reference_num = d.orderid
            LEFT JOIN sys_branch_mainshop e ON d.branchid = e.branchid
            WHERE e.mainshopid = b.sys_shop AND
            DATE(c.date_confirmed) BETWEEN ? AND ? AND c.isconfirmed = 1
            GROUP BY a.id
            ORDER BY d.branchid ASC";
          // Query for per branch logs
          $sql_logs3 = "SELECT a.*, b.disc_ratetype as ratetype, b.disc_rate as rate, b.price, b.sys_shop,
            d.branchid, b.Id as product_id, b.admin_isset
            FROM app_sales_order_logs a
            LEFT JOIN sys_products b ON a.product_id = b.Id
            LEFT JOIN app_sales_order_details c ON a.order_id = c.id
            LEFT JOIN sys_branch_orders d ON c.reference_num = d.orderid
            LEFT JOIN sys_branch_mainshop e ON d.branchid = e.branchid
            WHERE e.mainshopid = b.sys_shop AND
            DATE(c.date_confirmed) BETWEEN ? AND ? AND c.isconfirmed = 1
            GROUP BY a.id
            ORDER BY d.branchid ASC";

          $query2 = $this->db->query($sql_logs,array($trandate,$trandate));
          $query3 = $this->db->query($sql_logs_perbranch,array($trandate,$trandate));

          if($numrows->num_rows() > 0){
            // start cron logs
            $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate confirmed', 'For Accounts Billing'));

            $billno = $this->getNewBillNo();

            // $res = $this->db->query($sql,$data);
            $r = $numrows->result_array();
            $billing_logs_batchdata = array();

            foreach ($r AS $row){
              if($row["totalamount"] > 0){
                $shop_rate = $row['shop_rate'];
                $shop_ratetype = $row['shop_ratetype'];

                if(!in_array($row['sid'],$shopid_arr)){
                  // Billing per branch
                  if($row['billing_perbranch'] == 1 && $row['branch_count'] > 0){
                    // for checking of shops with billing per branch ON
                    array_push($shops_billing_per_branch,$row['shopid']);
                    // Query for billing per branch
                    if(c_international() == 1){
                      $per_branch_sql = "SELECT sum(od.total_amount_w_voucher) AS totalamount, SUM(b.delivery_amount) as shipping_fee,
                        SUM(b.converted_delivery_amount) as converted_shipping_fee, od.sys_shop AS shopid, od.id as orderid, @sys_shop := od.sys_shop,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details od
                        LEFT JOIN app_order_details_shipping b ON od.sys_shop = b.sys_shop AND od.reference_num = b.reference_num
                        WHERE od.payment_status=1
                        AND b.status = 1
                        AND od.isconfirmed = 1 AND od.payment_method != 'COD'
                        AND (date(od.date_confirmed) BETWEEN ? AND ?) AND od.sys_shop = ?
                        AND od.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND @sys_shop = od.sys_shop)";
                    }else{
                      $per_branch_sql = "SELECT sum(od.total_amount_w_voucher) AS totalamount, SUM(b.delivery_amount) as shipping_fee,
                        od.sys_shop AS shopid, od.id as orderid, @sys_shop := od.sys_shop,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = od.sys_shop AND status = 1 AND reference_num = od.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = od.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details od
                        LEFT JOIN app_order_details_shipping b ON od.sys_shop = b.sys_shop AND od.reference_num = b.reference_num
                        WHERE od.payment_status=1
                        AND b.status = 1
                        AND od.isconfirmed = 1 AND od.payment_method != 'COD'
                        AND (date(od.date_confirmed) BETWEEN ? AND ?) AND od.sys_shop = ?
                        AND od.reference_num NOT IN (SELECT orderid FROM sys_branch_orders WHERE status = 1 AND @sys_shop = od.sys_shop)";
                    }


                    // Main shop
                    $main_shop = $this->db->query($per_branch_sql, array($trandate,$trandate,$row['shopid']));
                    if($main_shop->num_rows() > 0){
                      $main_shop = $main_shop->row_array();
                      if(floatval($main_shop['totalamount']) > 0){
                        $total_amount = $main_shop['totalamount'];

                        $wallet = $this->get_shop_wallet($main_shop['shopid'])->num_rows();
                        $delivery_amount = ($main_shop['billing_type'] == 1 && $row['prepayment'] == 0) ? $main_shop['shipping_fee']: 0.00;
                        $total_fee = 0;
                        $netamount = 0;

                        if(c_international() == 1){
                          $total_amount_oc = 0;
                          $total_fee_oc = 0;
                          $netamount_oc = 0;
                          $delivery_amount_oc = ($main_shop['billing_type'] == 1 && $row['prepayment'] == 0) ? $main_shop['converted_shipping_fee']: 0.00;;
                        }

                        $billcode = $this->generateBillCode($billno,$main_shop["shopid"]);
                        $remarks = 'Settlement for transactions dated '.$trandate;

                        // processing fee
                        if($query3->num_rows() > 0){
                          foreach($query3->result_array() as $logs){
                            if($main_shop['shopid'] == $logs['sys_shop']){
                              $fee = 0;

                              // with product rate
                              if($logs['admin_isset'] == 1){
                                $fee = ($logs['ratetype'] == 'p')
                                ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                                : $logs['rate'] * $logs['quantity'];  // for fix rate type
                                $rate = $logs['rate'];
                                $ratetype = $logs['ratetype'];
                                // without product rate then fetch shop
                              }else{
                                $fee = ($shop_ratetype == 'p')
                                ? $shop_rate * $logs['total_amount']
                                : $shop_rate * $logs['quantity'];
                                $rate = $shop_rate;
                                $ratetype = $shop_ratetype;
                              }

                              if(c_international() == 1){
                                $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                                $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                                $total_amount_oc += php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                $total_fee_oc += php_to_n($fee,$logs['exrate_n_to_php']);
                              }

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
                                "ratetype" => $ratetype,
                                "processrate" => $rate,
                                "processfee" => $fee,
                                "netamount" => $logs['total_amount'] - $fee
                              );
                              if(c_international() == 1){
                                $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                                $billing_logs_data['processfee_oc'] = php_to_n($fee,$logs['exrate_n_to_php']);
                                $billing_logs_data['netamount_oc'] = floatval($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                                $billing_logs_data['exrate_n_to_php'] = $logs['exrate_n_to_php'];
                                $billing_logs_data['currency'] = $logs['currency'];
                              }

                              $billing_logs_batchdata[] = $billing_logs_data;
                            }
                          }
                        }

                        $netamount = $total_amount - $total_fee;
                        if(c_international() == 1){
                          $netamount_oc = $total_amount_oc - $total_fee_oc;
                        }
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

                        if(c_international() == 1){
                          $billing_data['delivery_amount_oc'] = $delivery_amount_oc;
                          $billing_data['totalamount_oc'] = $total_amount_oc;
                          $billing_data['processfee_oc'] = $total_fee_oc;
                          $billing_data['netamount_oc'] = $netamount_oc;
                        }
                        $this->db->insert('sys_billing',$billing_data);
                      }
                    }

                    // Branches
                    if(c_international() == 1){
                      $branch_sql = "SELECT SUM(a.total_amount_w_voucher) as totalamount, a.sys_shop as shopid, b.branchid,
                        SUM(d.delivery_amount) as shipping_fee, SUM(d.converted_delivery_amount) as converted_shipping_fee,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND status = 1 AND reference_num = a.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = a.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details a
                        INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
                        INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
                        LEFT JOIN app_order_details_shipping d ON a.sys_shop = d.sys_shop AND a.reference_num = d.reference_num
                        WHERE c.mainshopid = a.sys_shop AND a.isconfirmed IN ('f','s') AND a.payment_method != 'COD'
                        AND d.status = 1
                        AND DATE(a.date_confirmed) BETWEEN ? AND ? AND a.sys_shop = ? GROUP BY b.branchid";
                    }else{
                      $branch_sql = "SELECT SUM(a.total_amount_w_voucher) as totalamount, a.sys_shop as shopid, b.branchid,
                        SUM(d.delivery_amount) as shipping_fee,
                        -- @shipping_fee := SUM((SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND status = 1 AND reference_num = a.reference_num)) as shipping_fee,
                        @billing_type := (SELECT billing_type FROM sys_shops WHERE id = a.sys_shop AND status = 1) as billing_type
                        FROM app_sales_order_details a
                        INNER JOIN sys_branch_orders b ON a.reference_num = b.orderid AND b.status = 1
                        INNER JOIN sys_branch_mainshop c ON b.branchid = c.branchid AND c.status = 1
                        LEFT JOIN app_order_details_shipping d ON a.sys_shop = d.sys_shop AND a.reference_num = d.reference_num
                        WHERE c.mainshopid = a.sys_shop AND a.isconfirmed IN ('f','s') AND a.payment_method != 'COD'
                        AND d.status = 1
                        AND DATE(a.date_confirmed) BETWEEN ? AND ? AND a.sys_shop = ? GROUP BY b.branchid";
                    }


                    $branches = $this->db->query($branch_sql,array($trandate,$trandate,$row['shopid']));
                    if($branches->num_rows() > 0){
                      $branches = $branches->result_array();
                      foreach($branches as $branch){
                        if($branch['totalamount'] > 0 && $branch['branchid'] != 0){
                          $billcode = $this->generateBillCode($billno,$branch["shopid"]);
                          $billcode = $billcode.$branch['branchid'];
                          $remarks = 'Settlement for transactions dated '.$trandate;
                          $total_amount = $branch['totalamount'];

                          $wallet = $this->get_shop_wallet($branch['shopid'])->num_rows();
                          $delivery_amount = ($branch['billing_type'] == 1 && $row['prepayment'] == 0) ? $branch['shipping_fee']: 0.00;
                          $netamount = 0;
                          $total_fee = 0;

                          if(c_international() == 1){
                            $total_amount_oc = 0;
                            $total_fee_oc = 0;
                            $delivery_amount_oc = ($branch['billing_type'] == 1 && $row['prepayment'] == 0) ? $branch['converted_shipping_fee']: 0.00;;
                            $netamount_oc = 0;
                          }

                          $per_branch_logs = $this->db->query($sql_logs3,array($trandate,$trandate));
                          if($per_branch_logs->num_rows() > 0){
                            foreach($per_branch_logs->result_array() as $logs){
                              if($branch['shopid'] == $logs['sys_shop'] && $branch['branchid'] == $logs['branchid']){
                                $bfee = 0;
                                // with product rate
                                if($logs['admin_isset'] == 1){
                                  $bfee = ($logs['ratetype'] == 'p')
                                  ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                                  : $logs['rate'] * $logs['quantity'];  // for fix rate type
                                  $rate = $logs['rate'];
                                  $ratetype = $logs['ratetype'];
                                  // without product rate then fetch shop
                                }else{
                                  $bfee = ($shop_ratetype == 'p')
                                  ? $shop_rate * $logs['total_amount']
                                  : $shop_rate * $logs['quantity'];
                                  $rate = $shop_rate;
                                  $ratetype = $shop_ratetype;
                                }

                                if(c_international() == 1){
                                  $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                                  $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                                  $total_amount_oc += php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                  $total_fee_oc += php_to_n($bfee,$logs['exrate_n_to_php']);
                                }

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
                                  "ratetype" => $ratetype,
                                  "processrate" => $rate,
                                  "processfee" => $bfee,
                                  "netamount" => $logs['total_amount'] - $bfee
                                );

                                if(c_international() == 1){
                                  $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                                  $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                                  $billing_logs_data['processfee_oc'] = php_to_n($bfee,$logs['exrate_n_to_php']);
                                  $billing_logs_data['netamount_oc'] = floatval($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                                  $billing_logs_data['exrate_n_to_php'] = $logs['exrate_n_to_php'];
                                  $billing_logs_data['currency'] = $logs['currency'];
                                }

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

                          if(c_international() == 1){
                            $billing_data['delivery_amount_oc'] = $delivery_amount_oc;
                            $billing_data['totalamount_oc'] = $total_amount_oc;
                            $billing_data['processfee_oc'] = $total_fee_oc;
                            $billing_data['netamount_oc'] = $netamount_oc;
                          }
                          $this->db->insert('sys_billing',$billing_data);

                        }
                      }
                    }

                  // Billing per shop
                  }else{
                    $total_amount = $row['totalamount'];

                    $wallet = $this->get_shop_wallet($row['shopid'])->num_rows();
                    $delivery_amount = ($row['billing_type'] == 1 && $row['prepayment'] == 0) ? $row['shipping_fee']: 0.00;
                    $total_fee = 0;
                    $netamount = 0;

                    if(c_international() == 1){
                      $total_amount_oc = 0;
                      $delivery_amount_oc = ($row['billing_type'] == 1 && $row['prepayment'] == 0) ? $row['converted_shipping_fee'] : 0.00;
                      $total_fee_oc = 0;
                      $netamount_oc = 0;
                    }

                    $billcode = $this->generateBillCode($billno,$row["shopid"]);
                    $remarks = 'Settlement for transactions dated '.$trandate;

                    // processing fee
                    if($query2->num_rows() > 0){
                      foreach($query2->result_array() as $logs){
                        if($row['shopid'] == $logs['sys_shop']){
                          $fee = 0;

                          // with product rate
                          if($logs['rate'] > 0 && $logs['admin_isset'] == 1){
                            $fee = ($logs['ratetype'] == 'p')
                            ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                            : $logs['rate'] * $logs['quantity'];  // for fix rate type
                            $rate = $logs['rate'];
                            $ratetype = $logs['ratetype'];
                            // without product rate then fetch shop
                          }else{
                            $fee = ($shop_ratetype == 'p')
                            ? $shop_rate * $logs['total_amount']
                            : $shop_rate * $logs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          if(c_international() == 1){
                            $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                            $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                            $total_amount_oc += php_to_n($logs['amount'],$logs['exrate_n_to_php']) * $logs['quantity'];
                            $total_fee_oc += php_to_n($fee,$logs['exrate_n_to_php']);
                          }


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
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $fee,
                            "netamount" => $logs['total_amount'] - $fee
                          );

                          if(c_international() == 1){
                            $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                            $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                            $billing_logs_data['processfee_oc'] = php_to_n($fee,$logs['exrate_n_to_php']);
                            $billing_logs_data['netamount_oc'] = floatval($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                            $billing_logs_data['exrate_n_to_php'] = $logs['exrate_n_to_php'];
                            $billing_logs_data['currency'] = $logs['currency'];
                          }
                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }
                    }

                    $netamount = $total_amount - $total_fee;
                    if(c_international() == 1){
                      $netamount_oc = floatval($total_amount_oc - $total_fee_oc);
                    }

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

                    if(c_international() == 1){
                      $billing_data['delivery_amount_oc'] = $delivery_amount_oc;
                      $billing_data['totalamount_oc'] = $total_amount_oc;
                      $billing_data['netamount_oc'] = $netamount_oc;
                      $billing_data['processfee_oc'] = $total_fee_oc;
                    }
                    $this->db->insert('sys_billing',$billing_data);
                  }
                }

              }
            }

            // var_dump($billing_logs_batchdata);
            if(count($billing_logs_batchdata) > 0){
              $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
            }

            ### === FOR BILLING PER BRANCH === ###
            $b_logs = $this->db->query($sql_logs2,array($trandate,$trandate));

            $branch_bills = $this->db->query($sql2,array($trandate,$trandate));
            if($branch_bills->num_rows() > 0){
              $billing_branch_batchdata = array();

              foreach ($branch_bills->result_array() as $row){
                if(!in_array($row['shopid'],$shopid_arr)){
                  // check if total amout greater than zero and shop is not per branch billing
                  if($row["totalamount"] > 0 && !in_array($row['shopid'],$shops_billing_per_branch)){
                    $billcode = $this->generateBillCode($billno,$row["shopid"]);
                    $remarks = 'Settlement for transactions dated '.$trandate;
                    $netamount = 0;
                    $total_fee = 0;
                    $total_amount = $row['totalamount'];

                    if(c_international() == 1){
                      $total_amount_oc = 0;
                      $total_fee_oc = 0;
                      $netamount_oc = 0;
                    }

                    if($b_logs->num_rows() > 0){
                      foreach($b_logs->result_array() as $logs){
                        if($row['shopid'] == $logs['sys_shop'] && $row['branchid'] == $logs['branchid']){
                          $bfee = 0;
                          // with product rate
                          if($logs['rate'] > 0 && $logs['admin_isset'] == 1){
                            $bfee = ($logs['ratetype'] == 'p')
                            ? ($logs['total_amount'] * $logs['rate']) // for percentate rate type
                            : $logs['rate'] * $logs['quantity'];  // for fix rate type
                            $rate = $logs['rate'];
                            $ratetype = $logs['ratetype'];
                            // without product rate then fetch shop
                          }else{
                            $bfee = ($shop_ratetype == 'p')
                            ? $shop_rate * $logs['total_amount']
                            : $shop_rate * $logs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          if(c_international() == 1){
                            $logs['exrate_n_to_php'] = ($logs['exrate_n_to_php'] == null || $logs['exrate_n_to_php'] == "") ? 1 : $logs['exrate_n_to_php'];
                            $logs['currency'] = ($logs['currency'] == null || $logs['currency'] == "") ? 'PHP' : $logs['currency'];
                            $total_amount_oc += php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['quantity'];
                            $total_fee_oc += php_to_n($bfee,$logs['exrate_n_to_php']);
                          }

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
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $bfee,
                            "netamount" => $logs['total_amount'] - $bfee
                          );

                          if(c_international() == 1){
                            $billing_logs_data['totalamount_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']) * $logs['price'];
                            $billing_logs_data['price_oc'] = php_to_n($logs['price'],$logs['exrate_n_to_php']);
                            $billing_logs_data['processfee_oc'] = php_to_n($bfee,$logs['exrate_n_to_php']);
                            $billing_logs_data['netamount_oc'] = php_to_n($billing_logs_data['totalamount_oc'] - $billing_logs_data['processfee_oc']);
                            $billing_logs_data['exrate_n_to_php'] = $logs('exrate_n_to_php');
                            $billing_logs_data['currency'] = $logs('currency');
                          }
                          $billing_branch_batchdata[] = $billing_logs_data;
                        }
                      }

                      $netamount = $total_amount - $total_fee;
                      if(c_international() == 1){
                        $netamount_oc = $total_amount_oc - $total_fee_oc;
                      }
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

                    if(c_international() == 1){
                      $insert_data['totalamount_oc'] = $total_amount_oc;
                      $insert_data['processfee_oc'] = $total_fee_oc;
                      $insert_data['netamount_oc'] = $netamount_oc;
                    }
                    $this->db->insert('sys_billing_branch', $insert_data);
                  }
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
        // update process billing
        $this->update_process_billing($trandate);
      }

    }catch(Exception $e){

    }

    return $count;

  }//close processDailyMerchantPay

  public function process_billing($trandate){
    // set process billing
    $count = 0;
    $todaydate = todaytime();
    $escape_trandate = $this->db->escape($trandate);
    $this->set_process_billing($trandate);
    // get process billing
    $process = $this->get_process_billing($trandate);
    if($process->num_rows() == 0){
      // FETCH ALL SHOPS
      $shop_sql = "SELECT a.id as shopid, a.shopcode, a.shopname, a.shippingfee, a.billing_type,
        a.is_preorder, a.generatebilling as billing_perbranch,
        a.prepayment, b.ratetype as shoprate_type, b.rateamount as shoprate
        FROM sys_shops a
        LEFT JOIN sys_shop_rate b ON a.id = b.syshop
        WHERE a.status = 1 AND b.status = 1";

      // FETCH ALL SALES ORDERS
      $sales_order_sql = "SELECT a.id as so_id, a.sys_shop, a.reference_num, a.total_amount,
        a.total_amount_w_voucher, a.payment_portal_fee, a.delivery_amount, a.payment_id, a.payment_amount,
        a.date_ordered, a.date_shipped, a.date_confirmed, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        b.delivery_amount as shippingfee, b.daystoship, b.daystoship_to, c.branchid
        FROM app_sales_order_details a
        LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num AND a.sys_shop = b.sys_shop
        LEFT JOIN app_order_branch_details c ON a.reference_num = c.order_refnum AND a.sys_shop = c.shopid
        WHERE a.status = 1 AND b.status = 1 AND c.enabled = 1 AND
          a.order_status = 's' AND a.payment_status = 1 AND a.payment_method != 'COD' AND DATE(a.date_shipped) = $escape_trandate";

      // FETCH ALL SALES ORDER LOGS
      $sales_order_logs_sql = "SELECT a.order_id, a.product_id, a.quantity, a.amount,
        a.total_amount, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        a.refcom_amount, a.refcom_totalamount, a.refcom_rate,
        b.sys_shop, b.cat_id, b.itemid, b.itemname, b.otherinfo, b.price, b.admin_isset,
        b.disc_ratetype, b.disc_rate
        FROM app_sales_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND a.order_id
        IN (SELECT * FROM (SELECT id FROM app_sales_order_details WHERE order_status = 's' AND payment_method != 'COD' AND DATE(date_shipped) = $escape_trandate AND payment_status = 1 AND status = 1) as so)";

      $shop_query = $this->db->query($shop_sql);
      $sales_order_query = $this->db->query($sales_order_sql);
      $sales_order_logs_query = $this->db->query($sales_order_logs_sql);

      if($sales_order_query->num_rows() > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));
        $billno = $this->getNewBillNo();
        $sales_order_arr = $sales_order_query->result_array();
        $shops_arr = $shop_query->result_array();
        $sales_order_logs_arr = $sales_order_logs_query->result_array();
        $shops = filter_unique($sales_order_arr,'sys_shop'); // filter unique shop
        // print_r($shops);
        // die();
        // $main_so = filter_so_main($sales_order_arr);
        // $branch_so = filter_so_branch($sales_order_arr);

        $billing_batch = array();
        $billing_logs_batchdata = array();

        if(count((array)$shops) > 0){
          // LOOP THRU EACH SHOPS
          foreach($shops as $shop){
            $skey = array_search($shop['sys_shop'],array_column($shops_arr,'shopid')); // shop index key
            $shopdetails = $shops_arr[$skey];
            $shop_ratetype = $shopdetails['shoprate_type'];
            $shop_rate = $shopdetails['shoprate'];
            $main_so = filter_so_main($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $branch_so = filter_so_branch($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            // BILLING PER BRANCH
            if($shopdetails['billing_perbranch'] == 1){
              // MAIN
              if(count((array)$main_so) > 0){
                $total_amount = 0;
                $total_comrate = 0;
                $delivery_amount = 0;
                $total_fee = 0;
                $netamount = 0;
                foreach($main_so as $mkey => $main){
                  if($main['sys_shop'] == $shop['sys_shop']){
                    $total_amount += floatval($main['total_amount_w_voucher']);
                    $delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                    ? floatval($main['shippingfee']) : 0;

                    $main_logs = filter_logs_main($sales_order_logs_arr,array('order_id' => $main['so_id']));
                    if(count((array)$main_logs) > 0){
                      $new_total_amount = 0;
                      foreach($main_logs as $lkey => $mlogs){
                        $fee = 0;
                        if(floatval($mlogs['refcom_totalamount']) > 0){
                          $total_comrate += floatval($mlogs['total_amount'] * floatval($mlogs['refcom_rate']));
                        }

                        // $new_total_amount += floatval($mlogs['total_amount']);

                        // with product rate
                        if($mlogs['admin_isset'] == 1){
                          if($mlogs['disc_ratetype'] == 'p'){
                            $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                            ? $mlogs['refcom_totalamount'] * $mlogs['disc_rate']
                            : $mlogs['total_amount'] * $mlogs['disc_rate'];
                          }else{
                            $fee = $mlogs['disc_rate'] * $mlogs['quantity'];
                          }
                          // $fee = ($mlogs['disc_ratetype'] == 'p')
                          // ? ($mlogs['total_amount'] * $mlogs['disc_rate']) // for percentate rate type
                          // : $mlogs['disc_rate'] * $mlogs['quantity'];  // for fix rate type
                          $rate = $mlogs['disc_rate'];
                          $ratetype = $mlogs['disc_ratetype'];

                        // without product rate then fetch shop
                        }else{
                          if($shop_ratetype == 'p'){
                            $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                            ? $shop_rate * $mlogs['refcom_totalamount']
                            : $shop_rate * $mlogs['total_amount'];
                          }else{
                            $fee = $shop_rate * $mlogs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $mlogs['total_amount']
                          // : $shop_rate * $mlogs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                        $total_fee += $fee;

                        $billing_logs_data = array(
                          "sys_shop" => $shop['sys_shop'],
                          "branch_id" => 0, // main shop
                          "product_id" => $mlogs['product_id'],
                          "order_id" => $mlogs['order_id'],
                          "trandate" => $trandate,
                          "totalamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? $mlogs['refcom_totalamount'] : $mlogs['total_amount'],
                          "price" => (floatval($mlogs['refcom_amount']) > 0 ) ? $mlogs['refcom_amount'] : $mlogs['amount'],
                          "quantity" => $mlogs['quantity'],
                          "ratetype" => $ratetype,
                          "processrate" => $rate,
                          "processfee" => $fee,
                          "netamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? ($mlogs['refcom_totalamount'] - $fee) : ($mlogs['total_amount'] - $fee)
                        );

                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }

                    // if($new_total_amount > 0){
                    //   $temp_total_amount = $new_total_amount;
                    // }
                    //
                    // $total_amount += $temp_total_amount;

                  }
                }

                $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                $remarks = 'Settlement for transactions dated '.$trandate;
                $netamount = $total_amount -  ($total_fee + $total_comrate);
                $count++;
                $billing_data = array(
                  "billno" => $billno,
                  "billcode" => $billcode,
                  "syshop" => $shop['sys_shop'],
                  "branch_id" => 0, // main shop
                  "per_branch_billing" => 1,
                  "trandate" => $trandate,
                  "delivery_amount" => $delivery_amount,
                  "totalamount" => $total_amount,
                  "totalcomrate" => $total_comrate,
                  "remarks" => $remarks,
                  "processdate" => $todaydate,
                  "dateupdated" => $todaydate,
                  "processfee" => $total_fee,
                  "netamount" => $netamount,
                  "status" => 1
                );

                $billing_batch[] = $billing_data;
              }

              // BRANCH
              if(count((array)$branch_so) > 0){
                $branches = filter_unique($branch_so,'branchid');
                foreach($branches as $branch){
                  $branch_total_amount = 0;
                  $branch_total_comrate = 0;
                  $branch_delivery_amount = 0;
                  $branch_total_fee = 0;
                  $branch_netamount = 0;

                  foreach($branch_so as $bkey => $b_so){
                    if($branch['sys_shop'] == $b_so['sys_shop'] && $branch['branchid'] == $b_so['branchid']){
                      // $total_amount += floatval($b_so['total_amount_w_voucher']);
                      $branch_total_amount += floatval($b_so['total_amount_w_voucher']);
                      $branch_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                      ? floatval($b_so['shippingfee']) : 0;

                      $branch_logs = filter_logs_branch($sales_order_logs_arr,array('order_id' => $b_so['so_id']));
                      if(count((array)$branch_logs) > 0){
                        $new_branch_total_amount = 0;
                        foreach($branch_logs as $blkey => $blogs){
                          $bfee = 0;
                          if(floatval($blogs['refcom_totalamount']) > 0){
                            $branch_total_comrate += floatval($blogs['total_amount'] * floatval($blogs['refcom_rate']));
                          }

                          // with product rate
                          if($blogs['admin_isset'] == 1){
                            if($blogs['disc_ratetype'] == 'p'){
                              $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                              ? $blogs['refcom_totalamount'] * $blogs['disc_rate']
                              : $blogs['total_amount'] * $blogs['disc_rate'];
                            }else{
                              $bfee = $blogs['disc_rate'] * $blogs['quantity'];
                            }
                            // $bfee = ($blogs['disc_ratetype'] == 'p')
                            // ? ($blogs['total_amount'] * $blogs['disc_rate']) // for percentate rate type
                            // : $blogs['disc_rate'] * $blogs['quantity'];  // for fix rate type
                            $rate = $blogs['disc_rate'];
                            $ratetype = $blogs['disc_ratetype'];
                            // without product rate then fetch shop
                          }else{
                            if($shop_ratetype == 'p'){
                              $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                              ? $shop_rate * $blogs['refcom_amount']
                              : $shop_rate * $blogs['total_amount'];
                            }else{
                              $bfee = $shop_rate * $blogs['quantity'];
                            }
                            // $bfee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $blogs['total_amount']
                            // : $shop_rate * $blogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          $branch_total_fee += $bfee;
                          $billing_logs_data = array(
                            "sys_shop" => $shop['sys_shop'],
                            "branch_id" => $b_so['branchid'], // branchid
                            "product_id" => $blogs['product_id'],
                            "order_id" => $blogs['order_id'],
                            "trandate" => $trandate,
                            "totalamount" => (floatval($blogs['refcom_totalamount']) > 0) ? $blogs['refcom_totalamount'] : $blogs['total_amount'],
                            "price" => (floatval($blogs['refcom_amount']) > 0 ) ? $blogs['refcom_amount'] : $blogs['amount'],
                            "quantity" => $blogs['quantity'],
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $bfee,
                            "netamount" => (floatval($blogs['refcom_totalamount']) > 0) ? ($blogs['refcom_totalamount'] - $bfee) : ($blogs['total_amount'] - $bfee)
                          );
                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      // if($new_branch_total_amount > 0){
                      //   $temp_branch_total_amount = $new_branch_total_amount;
                      // }
                      //
                      // $branch_total_amount += $temp_branch_total_amount;
                    }
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $billcode = $billcode.$branch['branchid'];
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $branch_netamount = ($branch_total_amount) - ($branch_total_fee + $branch_total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => $branch['branchid'], // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $branch_delivery_amount,
                    "totalamount" => $branch_total_amount,
                    "totalcomrate" => $branch_total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $branch_total_fee,
                    "netamount" => $branch_netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }
              }

            // BILLING PER SHOP
            }else{
              $pershop_so = filter_so($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              if(count((array)$pershop_so) > 0){
                $pershop_total_amount = 0;
                $pershop_total_comrate = 0;
                $pershop_delivery_amount = 0;
                $pershop_total_fee = 0;
                $pershop_netamount = 0;

                foreach($pershop_so as $pskey => $pershop){
                  // $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                  $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                  $pershop_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                  ? floatval($pershop['shippingfee']) : 0;

                  $pershop_logs = filter_so_logs($sales_order_logs_arr,array('order_id' => $pershop['so_id']));
                  if(count((array)$pershop_logs) > 0){
                    $new_pershop_total_amount = 0;
                    foreach($pershop_logs as $psl_key => $ps_logs){
                      $pershop_fee = 0;
                      if(floatval($ps_logs['refcom_totalamount']) > 0){
                        $pershop_total_comrate += floatval($ps_logs['total_amount'] * floatval($ps_logs['refcom_rate']));
                      }

                      // with product rate
                      if($ps_logs['admin_isset'] == 1){
                        if($ps_logs['disc_ratetype'] == 'p'){
                          $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                          ? $ps_logs['disc_rate'] * $ps_logs['refcom_totalamount']
                          : $ps_logs['total_amount'] * $ps_logs['disc_rate'];
                        }else{
                          $pershop_fee = $ps_logs['disc_rate'] * $ps_logs['quantity'];
                        }
                        // $fee = ($ps_logs['disc_ratetype'] == 'p')
                        // ? ($ps_logs['total_amount'] * $ps_logs['disc_rate']) // for percentate rate type
                        // : $ps_logs['disc_rate'] * $ps_logs['quantity'];  // for fix rate type
                        $rate = $ps_logs['disc_rate'];
                        $ratetype = $ps_logs['disc_ratetype'];
                        // without product rate then fetch shop
                      }else{
                        if($shop_ratetype == 'p'){
                          $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                          ? $shop_rate * $ps_logs['refcom_totalamount']
                          : $shop_rate * $ps_logs['total_amount'];
                        }else{
                          $pershop_fee = $shop_rate * $ps_logs['quantity'];
                        }
                        // $fee = ($shop_ratetype == 'p')
                        // ? $shop_rate * $ps_logs['total_amount']
                        // : $shop_rate * $ps_logs['quantity'];
                        $rate = $shop_rate;
                        $ratetype = $shop_ratetype;
                      }

                      $pershop_total_fee += $pershop_fee;

                      $billing_logs_data = array(
                        "sys_shop" => $shop['sys_shop'],
                        "branch_id" => 0, // main shop
                        "product_id" => $ps_logs['product_id'],
                        "order_id" => $ps_logs['order_id'],
                        "trandate" => $trandate,
                        "totalamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? $ps_logs['refcom_totalamount'] : $ps_logs['total_amount'],
                        "price" => (floatval($ps_logs['refcom_amount']) > 0 ) ? $ps_logs['refcom_amount'] : $ps_logs['amount'],
                        "quantity" => $ps_logs['quantity'],
                        "ratetype" => $ratetype,
                        "processrate" => $rate,
                        "processfee" => $pershop_fee,
                        "netamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? ($ps_logs['refcom_totalamount'] - $pershop_fee) : ($ps_logs['total_amount'] - $pershop_fee)
                      );

                      $billing_logs_batchdata[] = $billing_logs_data;
                    }
                  }

                  // if($new_pershop_total_amount > 0){
                  //   $temp_pershop_total_amount = $new_pershop_total_amount;
                  // }
                  //
                  // $pershop_total_amount += $temp_pershop_total_amount;
                }

                $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                $remarks = 'Settlement for transactions dated '.$trandate;
                $pershop_netamount = ($pershop_total_amount) - ($pershop_total_fee + $pershop_total_comrate);
                $count++;
                $billing_data = array(
                  "billno" => $billno,
                  "billcode" => $billcode,
                  "syshop" => $shop['sys_shop'],
                  "branch_id" => 0, // main shop
                  "per_branch_billing" => 1,
                  "trandate" => $trandate,
                  "delivery_amount" => $pershop_delivery_amount,
                  "totalamount" => $pershop_total_amount,
                  "totalcomrate" => $pershop_total_comrate,
                  "remarks" => $remarks,
                  "processdate" => $todaydate,
                  "dateupdated" => $todaydate,
                  "processfee" => $pershop_total_fee,
                  "netamount" => $pershop_netamount,
                  "status" => 1
                );

                $billing_batch[] = $billing_data;
              }
            }


          } // END LOOP THRU SHOP

          // SET BILLING BATCH
          if(count($billing_batch) > 0){
            $this->db->insert_batch('sys_billing',$billing_batch);
          }

          // SET BILLING LOGS BATCH
          if(count($billing_logs_batchdata) > 0){
            $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
          }
        }

        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

      }

      // update process billing
      $this->update_process_billing($trandate);
    }

    return $count;
  }

  public function process_billing_process($trandate){
    // set process billing
    $count = 0;
    $todaydate = todaytime();
    $escape_trandate = $this->db->escape($trandate);
    $this->set_process_billing($trandate);
    // get process billing
    $process = $this->get_process_billing($trandate);
    if($process->num_rows() == 0){
      // FETCH ALL SHOPS
      $shop_sql = "SELECT a.id as shopid, a.shopcode, a.shopname, a.shippingfee, a.billing_type,
        a.is_preorder, a.generatebilling as billing_perbranch,
        a.prepayment, b.ratetype as shoprate_type, b.rateamount as shoprate
        FROM sys_shops a
        LEFT JOIN sys_shop_rate b ON a.id = b.syshop
        WHERE a.status = 1 AND b.status = 1";

      // FETCH ALL SALES ORDERS
      $sales_order_sql = "SELECT a.id as so_id, a.sys_shop, a.reference_num, a.total_amount,
        a.total_amount_w_voucher, a.payment_portal_fee, a.delivery_amount, a.payment_id, a.payment_amount,
        a.date_ordered, a.date_shipped, a.date_confirmed, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        b.delivery_amount as shippingfee, b.daystoship, b.daystoship_to, c.branchid
        FROM app_sales_order_details a
        LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num AND a.sys_shop = b.sys_shop
        LEFT JOIN app_order_branch_details c ON a.reference_num = c.order_refnum AND a.sys_shop = c.shopid
        WHERE a.status = 1 AND b.status = 1 AND c.enabled = 1 AND
          a.order_status = 's' AND a.payment_status = 1 AND a.payment_method != 'COD' AND DATE(a.date_shipped) = $escape_trandate";

      // FETCH ALL SALES ORDER LOGS
      $sales_order_logs_sql = "SELECT a.order_id, a.product_id, a.quantity, a.amount,
        a.total_amount, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        a.refcom_amount, a.refcom_totalamount, a.refcom_rate,
        b.sys_shop, b.cat_id, b.itemid, b.itemname, b.otherinfo, b.price, b.admin_isset,
        b.disc_ratetype, b.disc_rate
        FROM app_sales_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND a.order_id
        IN (SELECT * FROM (SELECT id FROM app_sales_order_details WHERE order_status = 's' AND payment_method != 'COD' AND DATE(date_shipped) = $escape_trandate AND payment_status = 1 AND status = 1) as so)";

      // FETCH ALL SETTLED BILLING
      $settled_sql = "SELECT syshop, branch_id, DATE(trandate) as trandate
        FROM sys_billing
        WHERE DATE(trandate) = $escape_trandate AND status = 1 AND paystatus = 'Settled'";

      $shop_query = $this->db->query($shop_sql);
      $sales_order_query = $this->db->query($sales_order_sql);
      $sales_order_logs_query = $this->db->query($sales_order_logs_sql);
      $settled_query = $this->db->query($settled_sql);

      if($sales_order_query->num_rows() > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));
        $billno = $this->getNewBillNo();
        $sales_order_arr = $sales_order_query->result_array();
        $shops_arr = $shop_query->result_array();
        $sales_order_logs_arr = $sales_order_logs_query->result_array();
        $settled_arr = $settled_query->result_array();
        $shops = filter_unique($sales_order_arr,'sys_shop'); // filter unique shop
        // print_r($shops);
        // die();
        // $main_so = filter_so_main($sales_order_arr);
        // $branch_so = filter_so_branch($sales_order_arr);

        $billing_batch = array();
        $billing_logs_batchdata = array();

        if(count((array)$shops) > 0){
          // LOOP THRU EACH SHOPS
          foreach($shops as $shop){
            $skey = array_search($shop['sys_shop'],array_column($shops_arr,'shopid')); // shop index key
            $shopdetails = $shops_arr[$skey];
            $shop_ratetype = $shopdetails['shoprate_type'];
            $shop_rate = $shopdetails['shoprate'];
            $main_so = filter_so_main($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $branch_so = filter_so_branch($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $settled = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => 0));
            // BILLING PER BRANCH
            if($shopdetails['billing_perbranch'] == 1){
              // MAIN
              if(count((array)$main_so) > 0){
                $settled_main = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => 0));
                if(count((array)$settled_main) == 0){
                  $total_amount = 0;
                  $total_comrate = 0;
                  $delivery_amount = 0;
                  $total_fee = 0;
                  $netamount = 0;
                  foreach($main_so as $mkey => $main){
                    if($main['sys_shop'] == $shop['sys_shop']){
                      $total_amount += floatval($main['total_amount_w_voucher']);
                      $delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                      ? floatval($main['shippingfee']) : 0;

                      $main_logs = filter_logs_main($sales_order_logs_arr,array('order_id' => $main['so_id']));
                      if(count((array)$main_logs) > 0){
                        $new_total_amount = 0;
                        foreach($main_logs as $lkey => $mlogs){
                          $fee = 0;
                          if(floatval($mlogs['refcom_totalamount']) > 0){
                            $total_comrate += floatval($mlogs['total_amount'] * floatval($mlogs['refcom_rate']));
                          }

                          // $new_total_amount += floatval($mlogs['total_amount']);

                          // with product rate
                          if($mlogs['admin_isset'] == 1){
                            if($mlogs['disc_ratetype'] == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? $mlogs['refcom_totalamount'] * $mlogs['disc_rate']
                              : $mlogs['total_amount'] * $mlogs['disc_rate'];
                            }else{
                              $fee = $mlogs['disc_rate'] * $mlogs['quantity'];
                            }
                            // $fee = ($mlogs['disc_ratetype'] == 'p')
                            // ? ($mlogs['total_amount'] * $mlogs['disc_rate']) // for percentate rate type
                            // : $mlogs['disc_rate'] * $mlogs['quantity'];  // for fix rate type
                            $rate = $mlogs['disc_rate'];
                            $ratetype = $mlogs['disc_ratetype'];

                          // without product rate then fetch shop
                          }else{
                            if($shop_ratetype == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? $shop_rate * $mlogs['refcom_totalamount']
                              : $shop_rate * $mlogs['total_amount'];
                            }else{
                              $fee = $shop_rate * $mlogs['quantity'];
                            }
                            // $fee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $mlogs['total_amount']
                            // : $shop_rate * $mlogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          $total_fee += $fee;

                          $billing_logs_data = array(
                            "sys_shop" => $shop['sys_shop'],
                            "branch_id" => 0, // main shop
                            "product_id" => $mlogs['product_id'],
                            "order_id" => $mlogs['order_id'],
                            "trandate" => $trandate,
                            "totalamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? $mlogs['refcom_totalamount'] : $mlogs['total_amount'],
                            "price" => (floatval($mlogs['refcom_amount']) > 0 ) ? $mlogs['refcom_amount'] : $mlogs['amount'],
                            "quantity" => $mlogs['quantity'],
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $fee,
                            "netamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? ($mlogs['refcom_totalamount'] - $fee) : ($mlogs['total_amount'] - $fee)
                          );

                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      // if($new_total_amount > 0){
                      //   $temp_total_amount = $new_total_amount;
                      // }
                      //
                      // $total_amount += $temp_total_amount;

                    }
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $netamount = $total_amount -  ($total_fee + $total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $delivery_amount,
                    "totalamount" => $total_amount,
                    "totalcomrate" => $total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $total_fee,
                    "netamount" => $netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }
              }

              // BRANCH
              if(count((array)$branch_so) > 0){
                $branches = filter_unique($branch_so,'branchid');
                foreach($branches as $branch){
                  $branch_settled = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => $branch['branchid']));
                  if(count((array)$branch_settled) == 0){
                    $branch_total_amount = 0;
                    $branch_total_comrate = 0;
                    $branch_delivery_amount = 0;
                    $branch_total_fee = 0;
                    $branch_netamount = 0;

                    foreach($branch_so as $bkey => $b_so){
                      if($branch['sys_shop'] == $b_so['sys_shop'] && $branch['branchid'] == $b_so['branchid']){
                        // $total_amount += floatval($b_so['total_amount_w_voucher']);
                        $branch_total_amount += floatval($b_so['total_amount_w_voucher']);
                        $branch_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                        ? floatval($b_so['shippingfee']) : 0;

                        $branch_logs = filter_logs_branch($sales_order_logs_arr,array('order_id' => $b_so['so_id']));
                        if(count((array)$branch_logs) > 0){
                          $new_branch_total_amount = 0;
                          foreach($branch_logs as $blkey => $blogs){
                            $bfee = 0;
                            if(floatval($blogs['refcom_totalamount']) > 0){
                              $branch_total_comrate += floatval($blogs['total_amount'] * floatval($blogs['refcom_rate']));
                            }

                            // with product rate
                            if($blogs['admin_isset'] == 1){
                              if($blogs['disc_ratetype'] == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? $blogs['refcom_totalamount'] * $blogs['disc_rate']
                                : $blogs['total_amount'] * $blogs['disc_rate'];
                              }else{
                                $bfee = $blogs['disc_rate'] * $blogs['quantity'];
                              }
                              // $bfee = ($blogs['disc_ratetype'] == 'p')
                              // ? ($blogs['total_amount'] * $blogs['disc_rate']) // for percentate rate type
                              // : $blogs['disc_rate'] * $blogs['quantity'];  // for fix rate type
                              $rate = $blogs['disc_rate'];
                              $ratetype = $blogs['disc_ratetype'];
                              // without product rate then fetch shop
                            }else{
                              if($shop_ratetype == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? $shop_rate * $blogs['refcom_amount']
                                : $shop_rate * $blogs['total_amount'];
                              }else{
                                $bfee = $shop_rate * $blogs['quantity'];
                              }
                              // $bfee = ($shop_ratetype == 'p')
                              // ? $shop_rate * $blogs['total_amount']
                              // : $shop_rate * $blogs['quantity'];
                              $rate = $shop_rate;
                              $ratetype = $shop_ratetype;
                            }

                            $branch_total_fee += $bfee;
                            $billing_logs_data = array(
                              "sys_shop" => $shop['sys_shop'],
                              "branch_id" => $b_so['branchid'], // branchid
                              "product_id" => $blogs['product_id'],
                              "order_id" => $blogs['order_id'],
                              "trandate" => $trandate,
                              "totalamount" => (floatval($blogs['refcom_totalamount']) > 0) ? $blogs['refcom_totalamount'] : $blogs['total_amount'],
                              "price" => (floatval($blogs['refcom_amount']) > 0 ) ? $blogs['refcom_amount'] : $blogs['amount'],
                              "quantity" => $blogs['quantity'],
                              "ratetype" => $ratetype,
                              "processrate" => $rate,
                              "processfee" => $bfee,
                              "netamount" => (floatval($blogs['refcom_totalamount']) > 0) ? ($blogs['refcom_totalamount'] - $bfee) : ($blogs['total_amount'] - $bfee)
                            );
                            $billing_logs_batchdata[] = $billing_logs_data;
                          }
                        }

                        // if($new_branch_total_amount > 0){
                        //   $temp_branch_total_amount = $new_branch_total_amount;
                        // }
                        //
                        // $branch_total_amount += $temp_branch_total_amount;
                      }
                    }

                    $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                    $billcode = $billcode.$branch['branchid'];
                    $remarks = 'Settlement for transactions dated '.$trandate;
                    $branch_netamount = ($branch_total_amount) - ($branch_total_fee + $branch_total_comrate);
                    $count++;
                    $billing_data = array(
                      "billno" => $billno,
                      "billcode" => $billcode,
                      "syshop" => $shop['sys_shop'],
                      "branch_id" => $branch['branchid'], // main shop
                      "per_branch_billing" => 1,
                      "trandate" => $trandate,
                      "delivery_amount" => $branch_delivery_amount,
                      "totalamount" => $branch_total_amount,
                      "totalcomrate" => $branch_total_comrate,
                      "remarks" => $remarks,
                      "processdate" => $todaydate,
                      "dateupdated" => $todaydate,
                      "processfee" => $branch_total_fee,
                      "netamount" => $branch_netamount,
                      "status" => 1
                    );

                    $billing_batch[] = $billing_data;
                  }
                }
              }

            // BILLING PER SHOP
            }else{
              $pershop_so = filter_so($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              if(count((array)$pershop_so) > 0){
                $settled_main = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => 0));
                if(count((array)$settled_main) > 0){
                  $pershop_total_amount = 0;
                  $pershop_total_comrate = 0;
                  $pershop_delivery_amount = 0;
                  $pershop_total_fee = 0;
                  $pershop_netamount = 0;

                  foreach($pershop_so as $pskey => $pershop){
                    // $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                    $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                    $pershop_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                    ? floatval($pershop['shippingfee']) : 0;

                    $pershop_logs = filter_so_logs($sales_order_logs_arr,array('order_id' => $pershop['so_id']));
                    if(count((array)$pershop_logs) > 0){
                      $new_pershop_total_amount = 0;
                      foreach($pershop_logs as $psl_key => $ps_logs){
                        $pershop_fee = 0;
                        if(floatval($ps_logs['refcom_totalamount']) > 0){
                          $pershop_total_comrate += floatval($ps_logs['total_amount'] * floatval($ps_logs['refcom_rate']));
                        }

                        // with product rate
                        if($ps_logs['admin_isset'] == 1){
                          if($ps_logs['disc_ratetype'] == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? $ps_logs['disc_rate'] * $ps_logs['refcom_totalamount']
                            : $ps_logs['total_amount'] * $ps_logs['disc_rate'];
                          }else{
                            $pershop_fee = $ps_logs['disc_rate'] * $ps_logs['quantity'];
                          }
                          // $fee = ($ps_logs['disc_ratetype'] == 'p')
                          // ? ($ps_logs['total_amount'] * $ps_logs['disc_rate']) // for percentate rate type
                          // : $ps_logs['disc_rate'] * $ps_logs['quantity'];  // for fix rate type
                          $rate = $ps_logs['disc_rate'];
                          $ratetype = $ps_logs['disc_ratetype'];
                          // without product rate then fetch shop
                        }else{
                          if($shop_ratetype == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? $shop_rate * $ps_logs['refcom_totalamount']
                            : $shop_rate * $ps_logs['total_amount'];
                          }else{
                            $pershop_fee = $shop_rate * $ps_logs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $ps_logs['total_amount']
                          // : $shop_rate * $ps_logs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                        $pershop_total_fee += $pershop_fee;

                        $billing_logs_data = array(
                          "sys_shop" => $shop['sys_shop'],
                          "branch_id" => 0, // main shop
                          "product_id" => $ps_logs['product_id'],
                          "order_id" => $ps_logs['order_id'],
                          "trandate" => $trandate,
                          "totalamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? $ps_logs['refcom_totalamount'] : $ps_logs['total_amount'],
                          "price" => (floatval($ps_logs['refcom_amount']) > 0 ) ? $ps_logs['refcom_amount'] : $ps_logs['amount'],
                          "quantity" => $ps_logs['quantity'],
                          "ratetype" => $ratetype,
                          "processrate" => $rate,
                          "processfee" => $pershop_fee,
                          "netamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? ($ps_logs['refcom_totalamount'] - $pershop_fee) : ($ps_logs['total_amount'] - $pershop_fee)
                        );

                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }

                    // if($new_pershop_total_amount > 0){
                    //   $temp_pershop_total_amount = $new_pershop_total_amount;
                    // }
                    //
                    // $pershop_total_amount += $temp_pershop_total_amount;
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $pershop_netamount = ($pershop_total_amount) - ($pershop_total_fee + $pershop_total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $pershop_delivery_amount,
                    "totalamount" => $pershop_total_amount,
                    "totalcomrate" => $pershop_total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $pershop_total_fee,
                    "netamount" => $pershop_netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }

              }
            }


          } // END LOOP THRU SHOP

          // SET BILLING BATCH
          if(count($billing_batch) > 0){
            $this->db->insert_batch('sys_billing',$billing_batch);
          }

          // SET BILLING LOGS BATCH
          if(count($billing_logs_batchdata) > 0){
            $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
          }
        }

        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

      }

      // update process billing
      $this->update_process_billing($trandate);
    }

    return $count;
  }

  public function process_billing_process_confirmed($trandate){
    // set process billing
    $count = 0;
    $todaydate = todaytime();
    $escape_trandate = $this->db->escape($trandate);
    $this->set_process_billing($trandate);
    // get process billing
    $process = $this->get_process_billing($trandate);
    if($process->num_rows() == 0){
      // FETCH ALL SHOPS
      $shop_sql = "SELECT a.id as shopid, a.shopcode, a.shopname, a.shippingfee, a.billing_type,
        a.is_preorder, a.generatebilling as billing_perbranch,
        a.prepayment, b.ratetype as shoprate_type, b.rateamount as shoprate
        FROM sys_shops a
        LEFT JOIN sys_shop_rate b ON a.id = b.syshop
        WHERE a.status = 1 AND b.status = 1";

      // FETCH ALL SALES ORDERS
      $sales_order_sql = "SELECT a.id as so_id, a.sys_shop, a.reference_num, a.total_amount,
        a.total_amount_w_voucher, a.payment_portal_fee, a.delivery_amount, a.payment_id, a.payment_amount,
        a.date_ordered, a.date_shipped, a.date_confirmed, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        b.delivery_amount as shippingfee, b.daystoship, b.daystoship_to, c.branchid
        FROM app_sales_order_details a
        LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num AND a.sys_shop = b.sys_shop
        LEFT JOIN app_order_branch_details c ON a.reference_num = c.order_refnum AND a.sys_shop = c.shopid
        WHERE a.status = 1 AND b.status = 1 AND c.enabled = 1 AND
          a.order_status = 's' AND a.payment_status = 1 AND a.payment_method != 'COD' AND a.isconfirmed = 1 AND DATE(a.date_confirmed) = $escape_trandate";

      // FETCH ALL SALES ORDER LOGS
      $sales_order_logs_sql = "SELECT a.order_id, a.product_id, a.quantity, a.amount,
        a.total_amount, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        a.refcom_amount, a.refcom_totalamount, a.refcom_rate,
        b.sys_shop, b.cat_id, b.itemid, b.itemname, b.otherinfo, b.price, b.admin_isset,
        b.disc_ratetype, b.disc_rate
        FROM app_sales_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND a.order_id
        IN (SELECT * FROM (SELECT id FROM app_sales_order_details WHERE order_status = 's' AND payment_method != 'COD' AND isconfirmed = 1 AND DATE(date_confirmed) = $escape_trandate AND payment_status = 1 AND status = 1) as so)";

      // FETCH ALL SETTLED BILLING
      $settled_sql = "SELECT syshop, branch_id, DATE(trandate) as trandate
        FROM sys_billing
        WHERE DATE(trandate) = $escape_trandate AND status = 1 AND paystatus = 'Settled'";

      $shop_query = $this->db->query($shop_sql);
      $sales_order_query = $this->db->query($sales_order_sql);
      $sales_order_logs_query = $this->db->query($sales_order_logs_sql);
      $settled_query = $this->db->query($settled_sql);

      if($sales_order_query->num_rows() > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));
        $billno = $this->getNewBillNo();
        $sales_order_arr = $sales_order_query->result_array();
        $shops_arr = $shop_query->result_array();
        $sales_order_logs_arr = $sales_order_logs_query->result_array();
        $settled_arr = $settled_query->result_array();
        $shops = filter_unique($sales_order_arr,'sys_shop'); // filter unique shop
        // print_r($shops);
        // die();
        // $main_so = filter_so_main($sales_order_arr);
        // $branch_so = filter_so_branch($sales_order_arr);

        $billing_batch = array();
        $billing_logs_batchdata = array();

        if(count((array)$shops) > 0){
          // LOOP THRU EACH SHOPS
          foreach($shops as $shop){
            $skey = array_search($shop['sys_shop'],array_column($shops_arr,'shopid')); // shop index key
            $shopdetails = $shops_arr[$skey];
            $shop_ratetype = $shopdetails['shoprate_type'];
            $shop_rate = $shopdetails['shoprate'];
            $main_so = filter_so_main($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $branch_so = filter_so_branch($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $settled = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => 0));
            // BILLING PER BRANCH
            if($shopdetails['billing_perbranch'] == 1){
              // MAIN
              if(count((array)$main_so) > 0){
                $settled_main = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => 0));
                if(count((array)$settled_main) == 0){
                  $total_amount = 0;
                  $total_comrate = 0;
                  $delivery_amount = 0;
                  $total_fee = 0;
                  $netamount = 0;
                  foreach($main_so as $mkey => $main){
                    if($main['sys_shop'] == $shop['sys_shop']){
                      $total_amount += floatval($main['total_amount_w_voucher']);
                      $delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                      ? floatval($main['shippingfee']) : 0;

                      $main_logs = filter_logs_main($sales_order_logs_arr,array('order_id' => $main['so_id']));
                      if(count((array)$main_logs) > 0){
                        $new_total_amount = 0;
                        foreach($main_logs as $lkey => $mlogs){
                          $fee = 0;
                          if(floatval($mlogs['refcom_totalamount']) > 0){
                            $total_comrate += floatval($mlogs['total_amount'] * floatval($mlogs['refcom_rate']));
                          }

                          // $new_total_amount += floatval($mlogs['total_amount']);

                          // with product rate
                          if($mlogs['admin_isset'] == 1){
                            if($mlogs['disc_ratetype'] == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? $mlogs['refcom_totalamount'] * $mlogs['disc_rate']
                              : $mlogs['total_amount'] * $mlogs['disc_rate'];
                            }else{
                              $fee = $mlogs['disc_rate'] * $mlogs['quantity'];
                            }
                            // $fee = ($mlogs['disc_ratetype'] == 'p')
                            // ? ($mlogs['total_amount'] * $mlogs['disc_rate']) // for percentate rate type
                            // : $mlogs['disc_rate'] * $mlogs['quantity'];  // for fix rate type
                            $rate = $mlogs['disc_rate'];
                            $ratetype = $mlogs['disc_ratetype'];

                          // without product rate then fetch shop
                          }else{
                            if($shop_ratetype == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? $shop_rate * $mlogs['refcom_totalamount']
                              : $shop_rate * $mlogs['total_amount'];
                            }else{
                              $fee = $shop_rate * $mlogs['quantity'];
                            }
                            // $fee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $mlogs['total_amount']
                            // : $shop_rate * $mlogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          $total_fee += $fee;

                          $billing_logs_data = array(
                            "sys_shop" => $shop['sys_shop'],
                            "branch_id" => 0, // main shop
                            "product_id" => $mlogs['product_id'],
                            "order_id" => $mlogs['order_id'],
                            "trandate" => $trandate,
                            "totalamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? $mlogs['refcom_totalamount'] : $mlogs['total_amount'],
                            "price" => (floatval($mlogs['refcom_amount']) > 0 ) ? $mlogs['refcom_amount'] : $mlogs['amount'],
                            "quantity" => $mlogs['quantity'],
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $fee,
                            "netamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? ($mlogs['refcom_totalamount'] - $fee) : ($mlogs['total_amount'] - $fee)
                          );

                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      // if($new_total_amount > 0){
                      //   $temp_total_amount = $new_total_amount;
                      // }
                      //
                      // $total_amount += $temp_total_amount;

                    }
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $netamount = $total_amount -  ($total_fee + $total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $delivery_amount,
                    "totalamount" => $total_amount,
                    "totalcomrate" => $total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $total_fee,
                    "netamount" => $netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }
              }

              // BRANCH
              if(count((array)$branch_so) > 0){
                $branches = filter_unique($branch_so,'branchid');
                foreach($branches as $branch){
                  $branch_settled = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => $branch['branchid']));
                  if(count((array)$branch_settled) == 0){
                    $branch_total_amount = 0;
                    $branch_total_comrate = 0;
                    $branch_delivery_amount = 0;
                    $branch_total_fee = 0;
                    $branch_netamount = 0;

                    foreach($branch_so as $bkey => $b_so){
                      if($branch['sys_shop'] == $b_so['sys_shop'] && $branch['branchid'] == $b_so['branchid']){
                        // $total_amount += floatval($b_so['total_amount_w_voucher']);
                        $branch_total_amount += floatval($b_so['total_amount_w_voucher']);
                        $branch_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                        ? floatval($b_so['shippingfee']) : 0;

                        $branch_logs = filter_logs_branch($sales_order_logs_arr,array('order_id' => $b_so['so_id']));
                        if(count((array)$branch_logs) > 0){
                          $new_branch_total_amount = 0;
                          foreach($branch_logs as $blkey => $blogs){
                            $bfee = 0;
                            if(floatval($blogs['refcom_totalamount']) > 0){
                              $branch_total_comrate += floatval($blogs['total_amount'] * floatval($blogs['refcom_rate']));
                            }

                            // with product rate
                            if($blogs['admin_isset'] == 1){
                              if($blogs['disc_ratetype'] == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? $blogs['refcom_totalamount'] * $blogs['disc_rate']
                                : $blogs['total_amount'] * $blogs['disc_rate'];
                              }else{
                                $bfee = $blogs['disc_rate'] * $blogs['quantity'];
                              }
                              // $bfee = ($blogs['disc_ratetype'] == 'p')
                              // ? ($blogs['total_amount'] * $blogs['disc_rate']) // for percentate rate type
                              // : $blogs['disc_rate'] * $blogs['quantity'];  // for fix rate type
                              $rate = $blogs['disc_rate'];
                              $ratetype = $blogs['disc_ratetype'];
                              // without product rate then fetch shop
                            }else{
                              if($shop_ratetype == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? $shop_rate * $blogs['refcom_amount']
                                : $shop_rate * $blogs['total_amount'];
                              }else{
                                $bfee = $shop_rate * $blogs['quantity'];
                              }
                              // $bfee = ($shop_ratetype == 'p')
                              // ? $shop_rate * $blogs['total_amount']
                              // : $shop_rate * $blogs['quantity'];
                              $rate = $shop_rate;
                              $ratetype = $shop_ratetype;
                            }

                            $branch_total_fee += $bfee;
                            $billing_logs_data = array(
                              "sys_shop" => $shop['sys_shop'],
                              "branch_id" => $b_so['branchid'], // branchid
                              "product_id" => $blogs['product_id'],
                              "order_id" => $blogs['order_id'],
                              "trandate" => $trandate,
                              "totalamount" => (floatval($blogs['refcom_totalamount']) > 0) ? $blogs['refcom_totalamount'] : $blogs['total_amount'],
                              "price" => (floatval($blogs['refcom_amount']) > 0 ) ? $blogs['refcom_amount'] : $blogs['amount'],
                              "quantity" => $blogs['quantity'],
                              "ratetype" => $ratetype,
                              "processrate" => $rate,
                              "processfee" => $bfee,
                              "netamount" => (floatval($blogs['refcom_totalamount']) > 0) ? ($blogs['refcom_totalamount'] - $bfee) : ($blogs['total_amount'] - $bfee)
                            );
                            $billing_logs_batchdata[] = $billing_logs_data;
                          }
                        }

                        // if($new_branch_total_amount > 0){
                        //   $temp_branch_total_amount = $new_branch_total_amount;
                        // }
                        //
                        // $branch_total_amount += $temp_branch_total_amount;
                      }
                    }

                    $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                    $billcode = $billcode.$branch['branchid'];
                    $remarks = 'Settlement for transactions dated '.$trandate;
                    $branch_netamount = ($branch_total_amount) - ($branch_total_fee + $branch_total_comrate);
                    $count++;
                    $billing_data = array(
                      "billno" => $billno,
                      "billcode" => $billcode,
                      "syshop" => $shop['sys_shop'],
                      "branch_id" => $branch['branchid'], // main shop
                      "per_branch_billing" => 1,
                      "trandate" => $trandate,
                      "delivery_amount" => $branch_delivery_amount,
                      "totalamount" => $branch_total_amount,
                      "totalcomrate" => $branch_total_comrate,
                      "remarks" => $remarks,
                      "processdate" => $todaydate,
                      "dateupdated" => $todaydate,
                      "processfee" => $branch_total_fee,
                      "netamount" => $branch_netamount,
                      "status" => 1
                    );

                    $billing_batch[] = $billing_data;
                  }
                }
              }

            // BILLING PER SHOP
            }else{
              $pershop_so = filter_so($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              if(count((array)$pershop_so) > 0){
                $settled_main = filter_settled($settled_arr,array('shopid' => $shop['sys_shop'], 'branchid' => 0));
                if(count((array)$settled_main) > 0){
                  $pershop_total_amount = 0;
                  $pershop_total_comrate = 0;
                  $pershop_delivery_amount = 0;
                  $pershop_total_fee = 0;
                  $pershop_netamount = 0;

                  foreach($pershop_so as $pskey => $pershop){
                    // $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                    $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                    $pershop_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                    ? floatval($pershop['shippingfee']) : 0;

                    $pershop_logs = filter_so_logs($sales_order_logs_arr,array('order_id' => $pershop['so_id']));
                    if(count((array)$pershop_logs) > 0){
                      $new_pershop_total_amount = 0;
                      foreach($pershop_logs as $psl_key => $ps_logs){
                        $pershop_fee = 0;
                        if(floatval($ps_logs['refcom_totalamount']) > 0){
                          $pershop_total_comrate += floatval($ps_logs['total_amount'] * floatval($ps_logs['refcom_rate']));
                        }

                        // with product rate
                        if($ps_logs['admin_isset'] == 1){
                          if($ps_logs['disc_ratetype'] == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? $ps_logs['disc_rate'] * $ps_logs['refcom_totalamount']
                            : $ps_logs['total_amount'] * $ps_logs['disc_rate'];
                          }else{
                            $pershop_fee = $ps_logs['disc_rate'] * $ps_logs['quantity'];
                          }
                          // $fee = ($ps_logs['disc_ratetype'] == 'p')
                          // ? ($ps_logs['total_amount'] * $ps_logs['disc_rate']) // for percentate rate type
                          // : $ps_logs['disc_rate'] * $ps_logs['quantity'];  // for fix rate type
                          $rate = $ps_logs['disc_rate'];
                          $ratetype = $ps_logs['disc_ratetype'];
                          // without product rate then fetch shop
                        }else{
                          if($shop_ratetype == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? $shop_rate * $ps_logs['refcom_totalamount']
                            : $shop_rate * $ps_logs['total_amount'];
                          }else{
                            $pershop_fee = $shop_rate * $ps_logs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $ps_logs['total_amount']
                          // : $shop_rate * $ps_logs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                        $pershop_total_fee += $pershop_fee;

                        $billing_logs_data = array(
                          "sys_shop" => $shop['sys_shop'],
                          "branch_id" => 0, // main shop
                          "product_id" => $ps_logs['product_id'],
                          "order_id" => $ps_logs['order_id'],
                          "trandate" => $trandate,
                          "totalamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? $ps_logs['refcom_totalamount'] : $ps_logs['total_amount'],
                          "price" => (floatval($ps_logs['refcom_amount']) > 0 ) ? $ps_logs['refcom_amount'] : $ps_logs['amount'],
                          "quantity" => $ps_logs['quantity'],
                          "ratetype" => $ratetype,
                          "processrate" => $rate,
                          "processfee" => $pershop_fee,
                          "netamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? ($ps_logs['refcom_totalamount'] - $pershop_fee) : ($ps_logs['total_amount'] - $pershop_fee)
                        );

                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }

                    // if($new_pershop_total_amount > 0){
                    //   $temp_pershop_total_amount = $new_pershop_total_amount;
                    // }
                    //
                    // $pershop_total_amount += $temp_pershop_total_amount;
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $pershop_netamount = ($pershop_total_amount) - ($pershop_total_fee + $pershop_total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $pershop_delivery_amount,
                    "totalamount" => $pershop_total_amount,
                    "totalcomrate" => $pershop_total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $pershop_total_fee,
                    "netamount" => $pershop_netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }

              }
            }


          } // END LOOP THRU SHOP

          // SET BILLING BATCH
          if(count($billing_batch) > 0){
            $this->db->insert_batch('sys_billing',$billing_batch);
          }

          // SET BILLING LOGS BATCH
          if(count($billing_logs_batchdata) > 0){
            $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
          }
        }

        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

      }

      // update process billing
      $this->update_process_billing($trandate);
    }

    return $count;
  }

  public function process_billing_toktokmall($trandate,$shopid = false){
    // set process billing
    $count = 0;
    $todaydate = todaytime();
    $escape_trandate = $this->db->escape($trandate);
    if($shopid === false){
      $this->set_process_billing($trandate);
    }
    // get process billing
    $process = $this->get_process_billing($trandate);
    if($process->num_rows() == 0 || $shopid !== false){
      // FETCH ALL SHOPS
      $shop_sql = "SELECT a.id as shopid, a.shopcode, a.shopname, a.shippingfee, a.billing_type,
        a.is_preorder, a.generatebilling as billing_perbranch,
        a.prepayment, b.ratetype as shoprate_type, b.rateamount as shoprate
        FROM sys_shops a
        LEFT JOIN sys_shop_rate b ON a.id = b.syshop
        WHERE a.status = 1 AND b.status = 1";

      // FETCH ALL SALES ORDERS
      $sales_order_sql = "SELECT a.id as so_id, a.sys_shop, a.reference_num, a.total_amount, a.srp_totalamount,
        a.total_amount_w_voucher, a.payment_portal_fee, a.delivery_amount, a.payment_id, a.payment_amount,
        a.date_ordered, a.date_shipped, a.date_confirmed, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        b.daystoship, b.daystoship_to, c.branchid, a.actual_totalamount, b.handle_shipping_promo,
        (IF(b.handle_shipping_promo = '1',b.original_shipping_fee,b.delivery_amount)) as shippingfee
        FROM app_sales_order_details a
        LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num AND a.sys_shop = b.sys_shop
        LEFT JOIN app_order_branch_details c ON a.reference_num = c.order_refnum AND a.sys_shop = c.shopid
        WHERE a.status = 1 AND b.status = 1 AND c.enabled = 1 AND
          a.order_status = 's' AND a.payment_status = 1 AND a.payment_method != 'COD' AND a.srp_totalamount > 0
          AND DATE(a.date_shipped) = $escape_trandate";

      // FETCH ALL SALES ORDER LOGS
      $sales_order_logs_sql = "SELECT a.order_id, a.product_id, a.quantity, a.amount, a.srp_amount,
        a.srp_totalamount, a.total_amount, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        a.refcom_amount, a.refcom_totalamount, a.refcom_rate, a.actual_amount, a.actual_totalamount,
        b.sys_shop, b.cat_id, b.itemid, b.itemname, b.otherinfo, b.price, b.admin_isset,
        b.disc_ratetype, b.disc_rate, a.order_type
        FROM app_sales_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND a.srp_amount > 0 AND a.order_id
        IN (SELECT * FROM (SELECT id FROM app_sales_order_details WHERE order_status = 's' AND payment_method != 'COD' AND DATE(date_shipped) = $escape_trandate AND payment_status = 1 AND status = 1) as so)";

      // IF SHOPID IS NOT FALSE
      if($shopid){
        $shopid = $this->db->escape($shopid);
        $shop_sql .= " AND a.id = $shopid";
        $sales_order_sql .= " AND a.sys_shop = $shopid";
        $sales_order_logs_sql .= " AND b.sys_shop = $shopid";
      }

      $shop_query = $this->db->query($shop_sql);
      $sales_order_query = $this->db->query($sales_order_sql);
      $sales_order_logs_query = $this->db->query($sales_order_logs_sql);

      if($sales_order_query->num_rows() > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));
        $billno = $this->getNewBillNo();
        $sales_order_arr = $sales_order_query->result_array();
        $shops_arr = $shop_query->result_array();
        $sales_order_logs_arr = $sales_order_logs_query->result_array();
        $shops = filter_unique($sales_order_arr,'sys_shop'); // filter unique shop
        // print_r($shops);
        // die();
        // $main_so = filter_so_main($sales_order_arr);
        // $branch_so = filter_so_branch($sales_order_arr);

        $billing_batch = array();
        $billing_logs_batchdata = array();

        if(count((array)$shops) > 0){
          // LOOP THRU EACH SHOPS
          foreach($shops as $shop){
            $skey = array_search($shop['sys_shop'],array_column($shops_arr,'shopid')); // shop index key
            $shopdetails = $shops_arr[$skey];
            $shop_ratetype = $shopdetails['shoprate_type'];
            $shop_rate = $shopdetails['shoprate'];
            $main_so = filter_so_main($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $branch_so = filter_so_branch($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            // BILLING PER BRANCH
            if($shopdetails['billing_perbranch'] == 1){
              // MAIN
              if(count((array)$main_so) > 0){
                $total_amount = 0;
                $total_comrate = 0;
                $delivery_amount = 0;
                $total_fee = 0;
                $netamount = 0;
                foreach($main_so as $mkey => $main){
                  if($main['sys_shop'] == $shop['sys_shop']){
                    // $total_amount += floatval($main['srp_totalamount']);
                    $delivery_amount += floatval($main['shippingfee']);
                    // $delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                    // ? floatval($main['shippingfee']) : 0;

                    $main_logs = filter_logs_main($sales_order_logs_arr,array('order_id' => $main['so_id']));
                    if(count((array)$main_logs) > 0){
                      $new_total_amount = 0;
                      foreach($main_logs as $lkey => $mlogs){
                        $total_amount += ($mlogs['order_type'] == 5) ? floatval($mlogs['actual_totalamount']) : floatval($mlogs['srp_totalamount']);
                        $fee = 0;
                        $comrate = 0;
                        if(floatval($mlogs['refcom_totalamount']) > 0){
                          $comrate = round(floatval($mlogs['total_amount'] * floatval($mlogs['refcom_rate'])),2);
                          $total_comrate += round($comrate,2);
                        }

                        // $new_total_amount += floatval($mlogs['total_amount']);

                        // with product rate
                        if($mlogs['admin_isset'] == 1){
                          if(floatval($mlogs['disc_rate']) > 0){
                            if(strtolower($mlogs['disc_ratetype']) == 'p'){
                              // $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              // ? round($mlogs['total_amount'] * $mlogs['disc_rate'],2)
                              // : round(($mlogs['srp_amount'] * $mlogs['disc_rate']) * $mlogs['quantity'],2);

                              // REFCOM
                              if(floatval($mlogs['order_type']) == 2){
                                $fee = round($mlogs['srp_totalamount'] * $mlogs['disc_rate'],2);
                              // MYSTERY COUPON
                              }else if($mlogs['order_type'] == 5){
                                $fee = round(($mlogs['actual_amount'] * $mlogs['disc_rate']) * $mlogs['quantity'],2);
                              // DEFAULT
                              }else{
                               $fee = round(($mlogs['srp_amount'] * $mlogs['disc_rate']) * $mlogs['quantity'],2);
                              }

                            }else{
                              $fee = $mlogs['disc_rate'] * $mlogs['quantity'];
                            }
                            // $fee = ($mlogs['disc_ratetype'] == 'p')
                            // ? ($mlogs['total_amount'] * $mlogs['disc_rate']) // for percentate rate type
                            // : $mlogs['disc_rate'] * $mlogs['quantity'];  // for fix rate type
                            $rate = $mlogs['disc_rate'];
                            $ratetype = $mlogs['disc_ratetype'];

                          }else{
                            if(strtolower($shop_ratetype) == 'p'){
                              // $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              // ? round($shop_rate * $mlogs['total_amount'],2)
                              // : round(($shop_rate * $mlogs['srp_amount']) * $mlogs['quantity'],2);

                              // REFCOM
                              if(floatval($mlogs['order_type']) == 2){
                                $fee = round($mlogs['srp_totalamount'] * $shop_rate,2);
                              // MYSTERY COUPON
                              }else if($mlogs['order_type'] == 5){
                                $fee = round(($mlogs['actual_amount'] * $shop_rate) * $mlogs['quantity'],2);
                              // DEFAULT
                              }else{
                               $fee = round(($mlogs['srp_amount'] * $shop_rate) * $mlogs['quantity'],2);
                              }

                            }else{
                              $fee = $shop_rate * $mlogs['quantity'];
                            }
                            // $fee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $mlogs['total_amount']
                            // : $shop_rate * $mlogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                        // without product rate then fetch shop
                        }else{
                          if(strtolower($shop_ratetype) == 'p'){
                            // $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                            // ? round($shop_rate * $mlogs['total_amount'],2)
                            // : round(($shop_rate * $mlogs['srp_amount']) * $mlogs['quantity'],2);

                            // REFCOM
                            if(floatval($mlogs['order_type']) == 2){
                              $fee = round($mlogs['srp_totalamount'] * $shop_rate,2);
                            // MYSTERY COUPON
                            }else if($mlogs['order_type'] == 5){
                              $fee = round(($mlogs['actual_amount'] * $shop_rate) * $mlogs['quantity'],2);
                            // DEFAULT
                            }else{
                             $fee = round(($mlogs['srp_amount'] * $shop_rate) * $mlogs['quantity'],2);
                            }

                          }else{
                            $fee = $shop_rate * $mlogs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $mlogs['total_amount']
                          // : $shop_rate * $mlogs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                        // $total_fee += $fee;
                        $total_fee += (floatval($mlogs['refcom_totalamount']) > 0) ? round(($fee - $comrate),2) : $fee;

                        $billing_logs_data = array(
                          "sys_shop" => $shop['sys_shop'],
                          "branch_id" => 0, // main shop
                          "product_id" => $mlogs['product_id'],
                          "order_id" => $mlogs['order_id'],
                          "trandate" => $trandate,
                          "srp_totalamount" => $mlogs['srp_amount'] * $mlogs['quantity'],
                          "totalamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? $mlogs['refcom_totalamount'] : $mlogs['total_amount'],
                          "srp_amount" => $mlogs['srp_amount'],
                          "price" => (floatval($mlogs['refcom_amount']) > 0 ) ? $mlogs['refcom_amount'] : $mlogs['amount'],
                          "actual_amount" => $mlogs['actual_amount'],
                          "actual_totalamount" => $mlogs['actual_totalamount'],
                          "quantity" => $mlogs['quantity'],
                          "ratetype" => $ratetype,
                          "processrate" => $rate,
                          "comrate" => $comrate,
                          "processfee" => (floatval($mlogs['refcom_totalamount']) > 0) ? ($fee - $comrate) : $fee
                        );

                        if(floatval($mlogs['refcom_totalamount']) > 0){
                          $billing_logs_data['netamount'] = floatval($mlogs['total_amount']) - $fee;
                        }else if($mlogs['order_type'] == 5){
                          $billing_logs_data['netamount'] = floatval($mlogs['actual_totalamount']) - $fee;
                        }else{
                          $billing_logs_data['netamount'] = ($mlogs['srp_amount'] * $mlogs['quantity']) - $fee;
                        }

                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }

                    // if($new_total_amount > 0){
                    //   $temp_total_amount = $new_total_amount;
                    // }
                    //
                    // $total_amount += $temp_total_amount;

                  }
                }

                // var_dump($total_amount);

                $total_commission = $total_fee + $total_comrate;
                $total_whtax = ($total_commission * c_whtax_percentage());
                $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                $remarks = 'Settlement for transactions dated '.$trandate;
                $netamount = $total_amount - ($total_fee + $total_comrate);
                $count++;
                $billing_data = array(
                  "billno" => $billno,
                  "billcode" => $billcode,
                  "syshop" => $shop['sys_shop'],
                  "branch_id" => 0, // main shop
                  "per_branch_billing" => 1,
                  "trandate" => $trandate,
                  "delivery_amount" => $delivery_amount,
                  "totalamount" => $total_amount,
                  "totalcomrate" => $total_comrate,
                  // "total_whtax" => $total_whtax,
                  "remarks" => $remarks,
                  "processdate" => $todaydate,
                  "dateupdated" => $todaydate,
                  "processfee" => $total_fee,
                  "netamount" => $netamount,
                  "status" => 1
                );

                $billing_batch[] = $billing_data;
              }

              // BRANCH
              if(count((array)$branch_so) > 0){
                $branches = filter_unique($branch_so,'branchid');
                foreach($branches as $branch){
                  $branch_total_amount = 0;
                  $branch_total_comrate = 0;
                  $branch_delivery_amount = 0;
                  $branch_total_fee = 0;
                  $branch_netamount = 0;

                  foreach($branch_so as $bkey => $b_so){
                    if($branch['sys_shop'] == $b_so['sys_shop'] && $branch['branchid'] == $b_so['branchid']){
                      // $total_amount += floatval($b_so['total_amount_w_voucher']);
                      // $branch_total_amount += floatval($b_so['srp_totalamount']);

                      $branch_delivery_amount += floatval($b_so['shippingfee']);
                      // $branch_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                      // ? floatval($b_so['shippingfee']) : 0;

                      $branch_logs = filter_logs_branch($sales_order_logs_arr,array('order_id' => $b_so['so_id']));
                      if(count((array)$branch_logs) > 0){
                        $new_branch_total_amount = 0;
                        foreach($branch_logs as $blkey => $blogs){
                          $branch_total_amount += ($blogs['order_type'] == 5) ? floatval($blogs['actual_totalamount']) : floatval($blogs['srp_totalamount']);
                          $bfee = 0;
                          $bcomrate = 0;
                          if(floatval($blogs['refcom_totalamount']) > 0){
                            $bcomrate = round(floatval($blogs['total_amount'] * floatval($blogs['refcom_rate'])),2);
                            $branch_total_comrate += round($bcomrate,2);
                          }

                          // with product rate
                          if($blogs['admin_isset'] == 1){
                            if(floatval($blogs['disc_rate']) > 0){
                              if(strtolower($blogs['disc_ratetype']) == 'p'){
                                // $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                // ? round($blogs['total_amount'] * $blogs['disc_rate'],2)
                                // : round(($blogs['srp_amount'] * $blogs['disc_rate']) * $blogs['quantity'],2);

                                // REFCOM
                                if(floatval($blogs['order_type']) == 2){
                                  $bfee = round($blogs['srp_totalamount'] * $blogs['disc_rate'],2);
                                // MYSTERY COUPON
                                }else if($blogs['order_type'] == 5){
                                  $bfee = round(($blogs['actual_amount'] * $blogs['disc_rate']) * $blogs['quantity'],2);
                                // DEFAULT
                                }else{
                                 $bfee = round(($blogs['srp_amount'] * $blogs['disc_rate']) * $blogs['quantity'],2);
                                }

                              }else{
                                $bfee = $blogs['disc_rate'] * $blogs['quantity'];
                              }
                              // $bfee = ($blogs['disc_ratetype'] == 'p')
                              // ? ($blogs['total_amount'] * $blogs['disc_rate']) // for percentate rate type
                              // : $blogs['disc_rate'] * $blogs['quantity'];  // for fix rate type
                              $rate = $blogs['disc_rate'];
                              $ratetype = $blogs['disc_ratetype'];
                            }else{
                              if(strtolower($shop_ratetype) == 'p'){
                                // $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                // ? round($shop_rate * $blogs['total_amount'],2)
                                // : round(($shop_rate * $blogs['srp_amount']) * $blogs['quantity'],2);

                                // REFCOM
                                if(floatval($blogs['order_type']) == 2){
                                  $bfee = round($blogs['srp_totalamount'] * $shop_rate,2);
                                // MYSTERY COUPON
                                }else if($blogs['order_type'] == 5){
                                  $bfee = round(($blogs['actual_amount'] * $shop_rate) * $blogs['quantity'],2);
                                // DEFAULT
                                }else{
                                 $bfee = round(($blogs['srp_amount'] * $shop_rate) * $blogs['quantity'],2);
                                }

                              }else{
                                $bfee = $shop_rate * $blogs['quantity'];
                              }
                              // $bfee = ($shop_ratetype == 'p')
                              // ? $shop_rate * $blogs['total_amount']
                              // : $shop_rate * $blogs['quantity'];
                              $rate = $shop_rate;
                              $ratetype = $shop_ratetype;
                            }

                          // without product rate then fetch shop
                          }else{
                            if(strtolower($shop_ratetype) == 'p'){
                              // $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                              // ? round($shop_rate * $blogs['total_amount'],2)
                              // : round(($shop_rate * $blogs['srp_amount']) * $blogs['quantity'],2);

                              // REFCOM
                              if(floatval($blogs['order_type']) == 2){
                                $bfee = round($blogs['srp_totalamount'] * $shop_rate,2);
                              // MYSTERY COUPON
                              }else if($blogs['order_type'] == 5){
                                $bfee = round(($blogs['actual_amount'] * $shop_rate) * $blogs['quantity'],2);
                              // DEFAULT
                              }else{
                               $bfee = round(($blogs['srp_amount'] * $shop_rate) * $blogs['quantity'],2);
                              }

                            }else{
                              $bfee = $shop_rate * $blogs['quantity'];
                            }
                            // $bfee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $blogs['total_amount']
                            // : $shop_rate * $blogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          $branch_total_fee += (floatval($blogs['refcom_totalamount']) > 0) ? round(($bfee - $bcomrate),2) : $bfee;
                          $billing_logs_data = array(
                            "sys_shop" => $shop['sys_shop'],
                            "branch_id" => $b_so['branchid'], // branchid
                            "product_id" => $blogs['product_id'],
                            "order_id" => $blogs['order_id'],
                            "trandate" => $trandate,
                            "srp_totalamount" => $blogs['srp_amount'] * $blogs['quantity'],
                            "totalamount" => (floatval($blogs['refcom_totalamount']) > 0) ? $blogs['refcom_totalamount'] : $blogs['total_amount'],
                            "srp_amount" => $blogs['srp_amount'],
                            "price" => (floatval($blogs['refcom_amount']) > 0 ) ? $blogs['refcom_amount'] : $blogs['amount'],
                            "actual_amount" => $blogs['actual_amount'],
                            "actual_totalamount" => $blogs['actual_totalamount'],
                            "quantity" => $blogs['quantity'],
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "comrate" => $bcomrate,
                            "processfee" => (floatval($blogs['refcom_totalamount']) > 0) ? ($bfee - $bcomrate) : $bfee
                          );

                          if(floatval($blogs['refcom_totalamount']) > 0){
                            $billing_logs_data['netamount'] = floatval($blogs['total_amount']) - $bfee;
                          }else if($blogs['order_type'] == 5){
                            $billing_logs_data['netamount'] = floatval($blogs['actual_totalamount']) - $bfee;
                          }else{
                            $billing_logs_data['netamount'] = ($blogs['srp_amount'] * $blogs['quantity']) - $bfee;
                          }

                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      // if($new_branch_total_amount > 0){
                      //   $temp_branch_total_amount = $new_branch_total_amount;
                      // }
                      //
                      // $branch_total_amount += $temp_branch_total_amount;
                    }
                  }

                  $total_branch_commission = $branch_total_fee + $branch_total_comrate;
                  $total_branch_whtax = $total_branch_commission * c_whtax_percentage();
                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $billcode = $billcode.$branch['branchid'];
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $branch_netamount = $branch_total_amount - ($branch_total_fee + $branch_total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => $branch['branchid'], // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $branch_delivery_amount,
                    "totalamount" => $branch_total_amount,
                    "totalcomrate" => $branch_total_comrate,
                    // "total_whtax" => $total_branch_whtax,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $branch_total_fee,
                    "netamount" => $branch_netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }
              }

            // BILLING PER SHOP
            }else{
              $pershop_so = filter_so($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              if(count((array)$pershop_so) > 0){
                $pershop_total_amount = 0;
                $pershop_total_comrate = 0;
                $pershop_delivery_amount = 0;
                $pershop_total_fee = 0;
                $pershop_netamount = 0;

                foreach($pershop_so as $pskey => $pershop){
                  // $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                  // $pershop_total_amount += floatval($pershop['srp_totalamount']);
                  $pershop_delivery_amount += floatval($pershop['shippingfee']);
                  // $pershop_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                  // ? floatval($pershop['shippingfee']) : 0;

                  $pershop_logs = filter_so_logs($sales_order_logs_arr,array('order_id' => $pershop['so_id']));
                  if(count((array)$pershop_logs) > 0){
                    $new_pershop_total_amount = 0;
                    foreach($pershop_logs as $psl_key => $ps_logs){
                      $pershop_total_amount += ($ps_logs['order_type'] == 5) ? $ps_logs['actual_totalamount'] : $ps_logs['srp_totalamount'];
                      $pershop_fee = 0;
                      $pershop_comrate = 0;
                      if(floatval($ps_logs['refcom_totalamount']) > 0){
                        $pershop_comrate = round(floatval($ps_logs['total_amount'] * floatval($ps_logs['refcom_rate'])),2);
                        $pershop_total_comrate += round($pershop_comrate,2);
                      }

                      // with product rate
                      if($ps_logs['admin_isset'] == 1){
                        if(floatval($ps_logs['disc_rate']) > 0){
                          if(strtolower($ps_logs['disc_ratetype']) == 'p'){
                            // $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            // ? round($ps_logs['disc_rate'] * $ps_logs['total_amount'],2)
                            // : round(($ps_logs['srp_amount'] * $ps_logs['disc_rate']) * $ps_logs['quantity'],2);

                            // REFCOM
                            if(floatval($ps_logs['order_type']) == 2){
                              $pershop_fee = round($ps_logs['srp_totalamount'] * $ps_logs['disc_rate'],2);
                            // MYSTERY COUPON
                            }else if($ps_logs['order_type'] == 5){
                              $pershop_fee = round(($ps_logs['actual_amount'] * $ps_logs['disc_rate']) * $ps_logs['quantity'],2);
                            // DEFAULT
                            }else{
                             $pershop_fee = round(($ps_logs['srp_amount'] * $ps_logs['disc_rate']) * $ps_logs['quantity'],2);
                            }

                          }else{
                            $pershop_fee = $ps_logs['disc_rate'] * $ps_logs['quantity'];
                          }
                          // $fee = ($ps_logs['disc_ratetype'] == 'p')
                          // ? ($ps_logs['total_amount'] * $ps_logs['disc_rate']) // for percentate rate type
                          // : $ps_logs['disc_rate'] * $ps_logs['quantity'];  // for fix rate type
                          $rate = $ps_logs['disc_rate'];
                          $ratetype = $ps_logs['disc_ratetype'];
                        }else{
                          if(strtolower($shop_ratetype) == 'p'){
                            // $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            // ? round($shop_rate * $ps_logs['total_amount'],2)
                            // : round(($shop_rate * $ps_logs['srp_amount']) * $ps_logs['quantity'],2);

                            // REFCOM
                            if(floatval($ps_logs['order_type']) == 2){
                              $pershop_fee = round($ps_logs['srp_totalamount'] * $shop_rate,2);
                            // MYSTERY COUPON
                            }else if($ps_logs['order_type'] == 5){
                              $pershop_fee = round(($ps_logs['actual_amount'] * $shop_rate) * $ps_logs['quantity'],2);
                            // DEFAULT
                            }else{
                             $pershop_fee = round(($ps_logs['srp_amount'] * $shop_rate) * $ps_logs['quantity'],2);
                            }

                          }else{
                            $pershop_fee = $shop_rate * $ps_logs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $ps_logs['total_amount']
                          // : $shop_rate * $ps_logs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                      // without product rate then fetch shop
                      }else{
                        if(strtolower($shop_ratetype) == 'p'){
                          // $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                          // ? round($shop_rate * $ps_logs['total_amount'],2)
                          // : round(($shop_rate * $ps_logs['srp_amount']) * $ps_logs['quantity'],2);

                          // REFCOM
                          if(floatval($ps_logs['order_type']) == 2){
                            $pershop_fee = round($ps_logs['srp_totalamount'] * $shop_rate,2);
                          // MYSTERY COUPON
                          }else if($ps_logs['order_type'] == 5){
                            $pershop_fee = round(($ps_logs['actual_amount'] * $shop_rate) * $ps_logs['quantity'],2);
                          // DEFAULT
                          }else{
                           $pershop_fee = round(($ps_logs['srp_amount'] * $shop_rate) * $ps_logs['quantity'],2);
                          }
                        }else{
                          $pershop_fee = $shop_rate * $ps_logs['quantity'];
                        }
                        // $fee = ($shop_ratetype == 'p')
                        // ? $shop_rate * $ps_logs['total_amount']
                        // : $shop_rate * $ps_logs['quantity'];
                        $rate = $shop_rate;
                        $ratetype = $shop_ratetype;
                      }

                      $pershop_total_fee += (floatval($ps_logs['refcom_totalamount']) > 0) ? round(($pershop_fee - $pershop_comrate),2) : $pershop_fee;

                      $billing_logs_data = array(
                        "sys_shop" => $shop['sys_shop'],
                        "branch_id" => 0, // main shop
                        "product_id" => $ps_logs['product_id'],
                        "order_id" => $ps_logs['order_id'],
                        "trandate" => $trandate,
                        "srp_totalamount" => $ps_logs['srp_amount'] * $ps_logs['quantity'],
                        "totalamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? $ps_logs['refcom_totalamount'] : $ps_logs['total_amount'],
                        "srp_amount" => $ps_logs['srp_amount'],
                        "price" => (floatval($ps_logs['refcom_amount']) > 0 ) ? $ps_logs['refcom_amount'] : $ps_logs['amount'],
                        "actual_amount" => $ps_logs['actual_amount'],
                        "actual_totalamount" => $ps_logs['actual_totalamount'],
                        "quantity" => $ps_logs['quantity'],
                        "ratetype" => $ratetype,
                        "processrate" => $rate,
                        "comrate" => $pershop_comrate,
                        "processfee" => (floatval($ps_logs['refcom_totalamount']) > 0) ? ($pershop_fee - $pershop_comrate) : $pershop_fee
                      );

                      if(floatval($ps_logs['refcom_totalamount']) > 0){
                        $billing_logs_data['netamount'] = floatval($ps_logs['total_amount']) - $pershop_fee;
                      }else if($ps_logs['order_type'] == 5){
                        $billing_logs_data['netamount'] = floatval($ps_logs['actual_totalamount']) - $pershop_fee;
                      }else{
                        $billing_logs_data['netamount'] = ($ps_logs['srp_amount'] * $ps_logs['quantity']) - $pershop_fee;
                      }

                      $billing_logs_batchdata[] = $billing_logs_data;
                    }
                  }

                  // if($new_pershop_total_amount > 0){
                  //   $temp_pershop_total_amount = $new_pershop_total_amount;
                  // }
                  //
                  // $pershop_total_amount += $temp_pershop_total_amount;
                }

                $pershop_total_commission = $pershop_total_fee + $pershop_total_comrate;
                $pershop_total_whtax = $pershop_total_commission * floatval(c_whtax_percentage());
                $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                $remarks = 'Settlement for transactions dated '.$trandate;
                $pershop_netamount = $pershop_total_amount - ($pershop_total_fee + $pershop_total_comrate);
                $count++;
                $billing_data = array(
                  "billno" => $billno,
                  "billcode" => $billcode,
                  "syshop" => $shop['sys_shop'],
                  "branch_id" => 0, // main shop
                  "per_branch_billing" => 1,
                  "trandate" => $trandate,
                  "delivery_amount" => $pershop_delivery_amount,
                  "totalamount" => $pershop_total_amount,
                  "totalcomrate" => $pershop_total_comrate,
                  // "total_whtax" => $pershop_total_whtax,
                  "remarks" => $remarks,
                  "processdate" => $todaydate,
                  "dateupdated" => $todaydate,
                  "processfee" => $pershop_total_fee,
                  "netamount" => $pershop_netamount,
                  "status" => 1
                );

                $billing_batch[] = $billing_data;
              }
            }


          } // END LOOP THRU SHOP

          // print_r($billing_logs_batchdata);

          // SET BILLING BATCH
          if(count($billing_batch) > 0){
            $this->db->insert_batch('sys_billing',$billing_batch);
          }

          // SET BILLING LOGS BATCH
          if(count($billing_logs_batchdata) > 0){
            $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
          }
        }

        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

      }

      // update process billing
      if($shopid === false){
        $this->update_process_billing($trandate);
      }
    }

    return $count;
  }

  public function process_billing_dateconfirmed($trandate){
    // set process billing
    $count = 0;
    $todaydate = todaytime();
    $escape_trandate = $this->db->escape($trandate);
    $this->set_process_billing($trandate);
    // get process billing
    $process = $this->get_process_billing($trandate);
    if($process->num_rows() == 0){
      // FETCH ALL SHOPS
      $shop_sql = "SELECT a.id as shopid, a.shopcode, a.shopname, a.shippingfee, a.billing_type,
        a.is_preorder, a.generatebilling as billing_perbranch,
        a.prepayment, b.ratetype as shoprate_type, b.rateamount as shoprate
        FROM sys_shops a
        LEFT JOIN sys_shop_rate b ON a.id = b.syshop
        WHERE a.status = 1 AND b.status = 1";

      // FETCH ALL SALES ORDERS
      $sales_order_sql = "SELECT a.id as so_id, a.sys_shop, a.reference_num, a.total_amount,
        a.total_amount_w_voucher, a.payment_portal_fee, a.delivery_amount, a.payment_id, a.payment_amount,
        a.date_ordered, a.date_shipped, a.date_confirmed, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        b.delivery_amount as shippingfee, b.daystoship, b.daystoship_to, c.branchid
        FROM app_sales_order_details a
        LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num AND a.sys_shop = b.sys_shop
        LEFT JOIN app_order_branch_details c ON a.reference_num = c.order_refnum AND a.sys_shop = c.shopid
        WHERE a.status = 1 AND b.status = 1 AND c.enabled = 1 AND
          a.isconfirmed = 1 AND a.payment_status = 1 AND a.payment_method != 'COD' AND DATE(a.date_confirmed) = $escape_trandate";

      // FETCH ALL SALES ORDER LOGS
      $sales_order_logs_sql = "SELECT a.order_id, a.product_id, a.quantity, a.amount,
        a.total_amount, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        a.refcom_amount, a.refcom_totalamount, a.refcom_rate,
        b.sys_shop, b.cat_id, b.itemid, b.itemname, b.otherinfo, b.price, b.admin_isset,
        b.disc_ratetype, b.disc_rate
        FROM app_sales_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND a.order_id
        IN (SELECT * FROM (SELECT id FROM app_sales_order_details WHERE isconfirmed = 1 AND payment_method != 'COD' AND DATE(date_confirmed) = $escape_trandate AND payment_status = 1 AND status = 1) as so)";

        $shop_query = $this->db->query($shop_sql);
        $sales_order_query = $this->db->query($sales_order_sql);
        $sales_order_logs_query = $this->db->query($sales_order_logs_sql);

        if($sales_order_query->num_rows() > 0){
          // start cron logs
          $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron billing per product rate', 'For Accounts Billing'));
          $billno = $this->getNewBillNo();
          $sales_order_arr = $sales_order_query->result_array();
          $shops_arr = $shop_query->result_array();
          $sales_order_logs_arr = $sales_order_logs_query->result_array();
          $shops = filter_unique($sales_order_arr,'sys_shop'); // filter unique shop

          // $main_so = filter_so_main($sales_order_arr);
          // $branch_so = filter_so_branch($sales_order_arr);

          $billing_batch = array();
          $billing_logs_batchdata = array();

          if(count((array)$shops) > 0){
            // LOOP THRU EACH SHOPS
            foreach($shops as $shop){
              $skey = array_search($shop['sys_shop'],array_column($shops_arr,'shopid')); // shop index key
              $shopdetails = $shops_arr[$skey];
              $shop_ratetype = $shopdetails['shoprate_type'];
              $shop_rate = $shopdetails['shoprate'];
              $main_so = filter_so_main($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              $branch_so = filter_so_branch($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              // BILLING PER BRANCH
              if($shopdetails['billing_perbranch'] == 1){
                // MAIN
                if(count((array)$main_so) > 0){
                  $total_amount = 0;
                  $total_comrate = 0;
                  $delivery_amount = 0;
                  $total_fee = 0;
                  $netamount = 0;

                  foreach($main_so as $mkey => $main){
                    if($main['sys_shop'] == $shop['sys_shop']){
                      $total_amount += floatval($main['total_amount_w_voucher']);
                      $delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                      ? floatval($main['shippingfee']) : 0;

                      $main_logs = filter_logs_main($sales_order_logs_arr,array('order_id' => $main['so_id']));
                      if(count((array)$main_logs) > 0){
                        $new_total_amount = 0;
                        foreach($main_logs as $lkey => $mlogs){
                          $fee = 0;
                          if(floatval($mlogs['refcom_totalamount']) > 0){
                            $total_comrate += floatval($mlogs['total_amount'] * floatval($mlogs['refcom_rate']));
                          }

                          // $new_total_amount += floatval($mlogs['total_amount']);

                          // with product rate
                          if($mlogs['admin_isset'] == 1){
                            if($mlogs['disc_ratetype'] == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? $mlogs['refcom_totalamount'] * $mlogs['disc_rate']
                              : $mlogs['total_amount'] * $mlogs['disc_rate'];
                            }else{
                              $fee = $mlogs['disc_rate'] * $mlogs['quantity'];
                            }
                            // $fee = ($mlogs['disc_ratetype'] == 'p')
                            // ? ($mlogs['total_amount'] * $mlogs['disc_rate']) // for percentate rate type
                            // : $mlogs['disc_rate'] * $mlogs['quantity'];  // for fix rate type
                            $rate = $mlogs['disc_rate'];
                            $ratetype = $mlogs['disc_ratetype'];

                          // without product rate then fetch shop
                          }else{
                            if($shop_ratetype == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? $shop_rate * $mlogs['refcom_totalamount']
                              : $shop_rate * $mlogs['total_amount'];
                            }else{
                              $fee = $shop_rate * $mlogs['quantity'];
                            }
                            // $fee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $mlogs['total_amount']
                            // : $shop_rate * $mlogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          $total_fee += $fee;

                          $billing_logs_data = array(
                            "sys_shop" => $shop['sys_shop'],
                            "branch_id" => 0, // main shop
                            "product_id" => $mlogs['product_id'],
                            "order_id" => $mlogs['order_id'],
                            "trandate" => $trandate,
                            "totalamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? $mlogs['refcom_totalamount'] : $mlogs['total_amount'],
                            "price" => (floatval($mlogs['refcom_amount']) > 0 ) ? $mlogs['refcom_amount'] : $mlogs['price'],
                            "quantity" => $mlogs['quantity'],
                            "ratetype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $fee,
                            "netamount" => (floatval($mlogs['refcom_totalamount']) > 0) ? ($mlogs['refcom_totalamount'] - $fee) : ($mlogs['total_amount'] - $fee)
                          );

                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      // if($new_total_amount > 0){
                      //   $temp_total_amount = $new_total_amount;
                      // }
                      //
                      // $total_amount += $temp_total_amount;

                    }
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $netamount = ($total_amount - $total_comrate) - $total_fee;
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $delivery_amount,
                    "totalamount" => $total_amount,
                    "totalcomrate" => $total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $total_fee,
                    "netamount" => $netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }

                // BRANCH
                if(count((array)$branch_so) > 0){
                  $branches = filter_unique($branch_so,'branchid');
                  foreach($branches as $branch){
                    $branch_total_amount = 0;
                    $branch_total_comrate = 0;
                    $branch_delivery_amount = 0;
                    $branch_total_fee = 0;
                    $branch_netamount = 0;

                    foreach($branch_so as $bkey => $b_so){
                      if($branch['sys_shop'] == $b_so['sys_shop'] && $branch['branchid'] == $b_so['branchid']){
                        // $total_amount += floatval($b_so['total_amount_w_voucher']);
                        $branch_total_amount += floatval($b_so['total_amount_w_voucher']);
                        $branch_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                        ? floatval($b_so['shippingfee']) : 0;

                        $branch_logs = filter_logs_branch($sales_order_logs_arr,array('order_id' => $b_so['so_id']));
                        if(count((array)$branch_logs) > 0){
                          $new_branch_total_amount = 0;
                          foreach($branch_logs as $blkey => $blogs){
                            $bfee = 0;
                            if(floatval($blogs['refcom_totalamount']) > 0){
                              $branch_total_comrate += floatval($blogs['total_amount'] * floatval($blogs['refcom_rate']));
                            }

                            // with product rate
                            if($blogs['admin_isset'] == 1){
                              if($blogs['disc_ratetype'] == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? $blogs['refcom_totalamount'] * $blogs['disc_rate']
                                : $blogs['total_amount'] * $blogs['disc_rate'];
                              }else{
                                $bfee = $blogs['disc_rate'] * $blogs['quantity'];
                              }
                              // $bfee = ($blogs['disc_ratetype'] == 'p')
                              // ? ($blogs['total_amount'] * $blogs['disc_rate']) // for percentate rate type
                              // : $blogs['disc_rate'] * $blogs['quantity'];  // for fix rate type
                              $rate = $blogs['disc_rate'];
                              $ratetype = $blogs['disc_ratetype'];
                              // without product rate then fetch shop
                            }else{
                              if($shop_ratetype == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? $shop_rate * $blogs['refcom_amount']
                                : $shop_rate * $blogs['total_amount'];
                              }else{
                                $bfee = $shop_rate * $blogs['quantity'];
                              }
                              // $bfee = ($shop_ratetype == 'p')
                              // ? $shop_rate * $blogs['total_amount']
                              // : $shop_rate * $blogs['quantity'];
                              $rate = $shop_rate;
                              $ratetype = $shop_ratetype;
                            }

                            $branch_total_fee += $bfee;
                            $billing_logs_data = array(
                              "sys_shop" => $shop['sys_shop'],
                              "branch_id" => $b_so['branchid'], // branchid
                              "product_id" => $blogs['product_id'],
                              "order_id" => $blogs['order_id'],
                              "trandate" => $trandate,
                              "totalamount" => (floatval($blogs['refcom_totalamount']) > 0) ? $blogs['refcom_totalamount'] : $blogs['total_amount'],
                              "price" => (floatval($blogs['refcom_amount']) > 0 ) ? $blogs['refcom_amount'] : $blogs['price'],
                              "quantity" => $blogs['quantity'],
                              "ratetype" => $ratetype,
                              "processrate" => $rate,
                              "processfee" => $bfee,
                              "netamount" => (floatval($blogs['refcom_totalamount']) > 0) ? ($blogs['refcom_totalamount'] - $bfee) : ($blogs['total_amount'] - $bfee)
                            );
                            $billing_logs_batchdata[] = $billing_logs_data;
                          }
                        }

                        // if($new_branch_total_amount > 0){
                        //   $temp_branch_total_amount = $new_branch_total_amount;
                        // }
                        //
                        // $branch_total_amount += $temp_branch_total_amount;
                      }
                    }

                    $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                    $billcode = $billcode.$branch['branchid'];
                    $remarks = 'Settlement for transactions dated '.$trandate;
                    $branch_netamount = ($branch_total_amount - $branch_total_comrate) - $branch_total_fee;
                    $count++;
                    $billing_data = array(
                      "billno" => $billno,
                      "billcode" => $billcode,
                      "syshop" => $shop['sys_shop'],
                      "branch_id" => $branch['branchid'], // main shop
                      "per_branch_billing" => 1,
                      "trandate" => $trandate,
                      "delivery_amount" => $branch_delivery_amount,
                      "totalamount" => $branch_total_amount,
                      "totalcomrate" => $branch_total_comrate,
                      "remarks" => $remarks,
                      "processdate" => $todaydate,
                      "dateupdated" => $todaydate,
                      "processfee" => $branch_total_fee,
                      "netamount" => $branch_netamount,
                      "status" => 1
                    );

                    $billing_batch[] = $billing_data;
                  }
                }

              // BILLING PER SHOP
              }else{
                $pershop_so = filter_so($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
                if(count((array)$pershop_so) > 0){
                  $pershop_total_amount = 0;
                  $pershop_total_comrate = 0;
                  $pershop_delivery_amount = 0;
                  $pershop_total_fee = 0;
                  $pershop_netamount = 0;

                  foreach($pershop_so as $pskey => $pershop){
                    // $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                    $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                    $pershop_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                    ? floatval($pershop['shippingfee']) : 0;

                    $pershop_logs = filter_so_logs($sales_order_logs_arr,array('order_id' => $pershop['so_id']));
                    if(count((array)$pershop_logs) > 0){
                      $new_pershop_total_amount = 0;
                      foreach($pershop_logs as $psl_key => $ps_logs){
                        $pershop_fee = 0;
                        if(floatval($ps_logs['refcom_totalamount']) > 0){
                          $pershop_total_comrate += floatval($ps_logs['total_amount'] * floatval($ps_logs['refcom_rate']));
                        }

                        // with product rate
                        if($ps_logs['admin_isset'] == 1){
                          if($ps_logs['disc_ratetype'] == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? $ps_logs['disc_rate'] * $ps_logs['refcom_totalamount']
                            : $ps_logs['total_amount'] * $ps_logs['disc_rate'];
                          }else{
                            $pershop_fee = $ps_logs['disc_rate'] * $ps_logs['quantity'];
                          }
                          // $fee = ($ps_logs['disc_ratetype'] == 'p')
                          // ? ($ps_logs['total_amount'] * $ps_logs['disc_rate']) // for percentate rate type
                          // : $ps_logs['disc_rate'] * $ps_logs['quantity'];  // for fix rate type
                          $rate = $ps_logs['disc_rate'];
                          $ratetype = $ps_logs['disc_ratetype'];
                          // without product rate then fetch shop
                        }else{
                          if($shop_ratetype == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? $shop_rate * $ps_logs['refcom_totalamount']
                            : $shop_rate * $ps_logs['total_amount'];
                          }else{
                            $pershop_fee = $shop_rate * $ps_logs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $ps_logs['total_amount']
                          // : $shop_rate * $ps_logs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                        $pershop_total_fee += $pershop_fee;

                        $billing_logs_data = array(
                          "sys_shop" => $shop['sys_shop'],
                          "branch_id" => 0, // main shop
                          "product_id" => $ps_logs['product_id'],
                          "order_id" => $ps_logs['order_id'],
                          "trandate" => $trandate,
                          "totalamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? $ps_logs['refcom_totalamount'] : $ps_logs['total_amount'],
                          "price" => (floatval($ps_logs['refcom_amount']) > 0 ) ? $ps_logs['refcom_amount'] : $ps_logs['price'],
                          "quantity" => $ps_logs['quantity'],
                          "ratetype" => $ratetype,
                          "processrate" => $rate,
                          "processfee" => $pershop_fee,
                          "netamount" => (floatval($ps_logs['refcom_totalamount']) > 0) ? ($ps_logs['refcom_totalamount'] - $pershop_fee) : ($ps_logs['total_amount'] - $pershop_fee)
                        );

                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }

                    // if($new_pershop_total_amount > 0){
                    //   $temp_pershop_total_amount = $new_pershop_total_amount;
                    // }
                    //
                    // $pershop_total_amount += $temp_pershop_total_amount;
                  }

                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $remarks = 'Settlement for transactions dated '.$trandate;
                  $pershop_netamount = ($pershop_total_amount - $pershop_total_comrate) - $pershop_total_fee;
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "syshop" => $shop['sys_shop'],
                    "branch_id" => 0, // main shop
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "delivery_amount" => $pershop_delivery_amount,
                    "totalamount" => $pershop_total_amount,
                    "totalcomrate" => $pershop_total_comrate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "dateupdated" => $todaydate,
                    "processfee" => $pershop_total_fee,
                    "netamount" => $pershop_netamount,
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }
              }

            } // END LOOP THRU SHOP

            // SET BILLING BATCH
            if(count($billing_batch) > 0){
              $this->db->insert_batch('sys_billing',$billing_batch);
            }

            // SET BILLING LOGS BATCH
            if(count($billing_logs_batchdata) > 0){
              $this->db->insert_batch('sys_billing_logs',$billing_logs_batchdata);
            }
          }

          // End of cron logs
          $cron_status = ($cron_id != '') ? 'successful' : 'failed';
          $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

        }

      // update process billing
      $this->update_process_billing($trandate);
    }

    return $count;
  }

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
		$billno++;

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

  public function set_process_billing($date){
    $data = array('billing_date' => $date, 'is_processed' => 0);
    $this->db->insert('sys_billing_processed',$data);
  }

  public function update_process_billing($date,$status = 1){
    $data = array('is_processed' => $status);
    $this->db->update('sys_billing_processed',$data,array('billing_date' => $date));
  }

  public function get_process_billing($date){
    $date = $this->db->escape($date);
    $sql = "SELECT * FROM sys_billing_processed WHERE billing_date = $date AND status = 1 AND is_processed = 1";
    return $this->db->query($sql);
  }

  public function get_shop_bankaccount($shopid,$branchid){
    $shopid   = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $sql = "SELECT accountname, accountno, bankname, description
      FROM sys_shop_account
      WHERE status = 1 AND sys_shop = $shopid AND branch_id = $branchid";
    return $this->db->query($sql);
  }

  public function get_shop_n_branch($shopid,$branchid){
    $shopid   = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $sql = "SELECT a.shopname,
    @branchname = (SELECT c.branchname FROM sys_branch_mainshop b INNER JOIN sys_branch_profile c ON b.branchid = c.id WHERE b.status = 1 AND c.status = 1 AND b.mainshopid = $shopid AND b.branchid = $branchid) as branchname
    FROM sys_shops a
    WHERE a.status = 1 AND a.id = $shopid";

    return $this->db->query($sql);
  }

  public function get_billing_breakdown_pdf($shopid,$branchid,$trandate){
    $sys_shop = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $trandate = $this->db->escape($trandate);

    $sql = "SELECT a.date_shipped as trandate, a.reference_num as refnum, a.sys_shop,
      a.paypanda_ref as payrefnum, a.total_amount as amount, a.total_amount_w_voucher,
      a.id order_id, a.user_id,
      @process_fee := (SELECT SUM(processfee) FROM sys_billing_logs WHERE order_id = a.id) as processfee,
      @shippingfee := (SELECT delivery_amount FROM app_order_details_shipping WHERE sys_shop = a.sys_shop AND reference_num = a.reference_num) as shippingfee,
      @voucher_amount := (SELECT amount FROM app_order_payment WHERE shopid = a.sys_shop AND order_ref_num = a.reference_num AND payment_type = 'toktokmall' AND status = 1) as voucher_amount,
      @refcom_totalamount := (SELECT SUM(total_amount * refcom_rate) FROM app_sales_order_logs WHERE order_id = a.id AND status = 1) as refcom_totalamount,
      @referral := (SELECT referral_code FROM app_referral_codes WHERE order_reference_num = a.reference_num AND status = 1) as referral_code
      FROM app_sales_order_details a
      LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum";

    if(ini() == "jcww"){

      if(strtotime($trandate) <= strtotime('2021-05-20')){
        $sql .= " WHERE a.sys_shop = $sys_shop AND DATE(a.date_shipped) = $trandate
          AND a.status = 1";
      }else{
        $sql .= " WHERE a.sys_shop = $sys_shop AND DATE(a.date_confirmed) = $trandate
          AND a.status = 1 AND a.isconfirmed = 1";
      }
    }

    // $sql .= " ORDER BY date_shipped DESC";
    $sql .= " AND b.branchid = $branchid ORDER BY date_shipped DESC";

    $query = $this->db->query($sql);
    // return $query;
    $data = array();

    foreach($query->result_array() as $row ){
      $nestedData = array();

      $order_type = 'Regular Order';
      if($row['user_id'] != 0){
        $order_type = "Thru OF Login";
      }

      if($row['referral_code'] != "" || $row['referral_code'] != null){
        $order_type = "Via OF Shoplink";
      }

      $t_date = new Datetime($row['trandate']);
      $nestedData['trandate'] = $t_date->format('M d, Y h:i:m');
      $nestedData['reference_num'] = $row["refnum"];
      $nestedData['payrefnum'] = $row["payrefnum"];
      $nestedData['order_type'] = $order_type;

      $nestedData['amount']                 = number_format($row["amount"],2);
      $nestedData['voucher_amount']         = number_format($row["voucher_amount"],2);
      $nestedData['total_amount_w_voucher'] = number_format($row["total_amount_w_voucher"],2);
      $nestedData['shippingfee']            = number_format(floatval($row['shippingfee']),2);
      $nestedData['refcom_totalamount']     = number_format($row['refcom_totalamount'],2);
      $nestedData['processfee_only']        = number_format($row['processfee'],2);
      $nestedData['processfee_plus_refcom'] = number_format($row['processfee'] + $row['refcom_totalamount'],2);
      $nestedData['netamount']              = number_format((($row["total_amount_w_voucher"] - $row['refcom_totalamount']) - $row['processfee']) + (floatval($row['shippingfee'])),2);

      $data[] = $nestedData;
    }

    return $data;

  }

  ///////////////////////////////////////////////////////////////////////////
  //////////  TOTAL SALES CRON
  ///////////////////////////////////////////////////////////////////////////

  public function get_totalsales_logs($refnum){
    $refnum = $this->db->escape($refnum);
    $sql = "SELECT reference_num FROM api_totalsales_logs WHERE reference_num = $refnum";
    return $this->db->query($sql);
  }

  public function get_totalsales_billing($trandate){
    $trandate = $this->db->escape($trandate);
    $sql = "SELECT SUM(totalcomrate) as totalcomrate, SUM(processfee) as totalsales FROM `sys_billing` WHERE DATE(trandate) = $trandate";
    return $this->db->query($sql)->row_array();
  }

  public function set_totalsales_api_logs($data){
    $this->db->insert('api_totalsales_logs',$data);
    return $this->db->insert_id();
  }

  public function update_totalsales_api($data,$id){
    $this->db->update('api_totalsales_logs',$data,array('id' => $id));
  }

  ///////////////////////////////////////////////////////////////////////////
  //////////  DELETE SECTION
  ///////////////////////////////////////////////////////////////////////////

  public function delete_billing($billing_id,$status = 0){
    $billing_id = $this->db->escape($billing_id);
    $status = $this->db->escape($status);
    $sql = "UPDATE sys_billing SET status = $status WHERE id = $billing_id AND status = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function delelete_billing_logs($shopid,$branchid,$trandate,$status = 0){
    $shopid = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $trandate = $this->db->escape($trandate);
    $status = $this->db->escape($status);

    $sql = "UPDATE sys_billing_logs SET status = $status
      WHERE sys_shop = $shopid AND branch_id = $branchid
      AND DATE(trandate) = $trandate AND status = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function delete_wallet_logs($billcode,$shopid,$branchid,$payref,$unsettled_payref){
    $billcode = $this->db->escape($billcode);
    $shopid = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $payref = ($payref == null || $payref == "") ? $this->db->escape('---') : $this->db->escape($payref);
    $unsettled_payref = ($unsettled_payref == null || $unsettled_payref == "") ? $this->db->escape('---') : $this->db->escape($unsettled_payref);

    $sql = "UPDATE sys_shops_wallet_logs SET enabled = 0
      WHERE shopid = $shopid AND branchid = $branchid AND (refnum = $payref OR refnum = $unsettled_payref) AND enabled = 1";
    $this->db->query($sql);
    if($this->db->affected_rows() > 0){
      $sql_logs = "SELECT id, amount, balance, type FROM sys_shops_wallet_logs
        WHERE shopid = $shopid AND branchid = $branchid AND enabled = 1";
      $query = $this->db->query($sql_logs);
      if($query->num_rows() > 0){
        $sql_wallet = "UPDATE sys_shops_wallet SET balance =  ROUND((SELECT SUM((CASE WHEN type = 'minus' THEN CONCAT('-',amount) ELSE amount END)) FROM sys_shops_wallet_logs WHERE shopid = $shopid AND branchid = $branchid AND enabled = 1),2)
          WHERE shopid = $shopid AND branchid = $branchid AND enabled = 1";
        $this->db->query($sql_wallet);
        if($this->db->affected_rows() > 0){
          $balance = 0;
          $update_data = array();
          foreach($query->result_array() as $key => $row){
            if($row['type'] == 'plus'){
              $balance += floatval($row['amount']);
            }else{
              $balance -= floatval($row['amount']);
            }
            $log_data = array();
            $log_data['id'] = $row['id'];
            $log_data['amount'] = floatval($row['amount']);
            $log_data['balance'] = floatval($balance);
            $update_data[] = $log_data;
          }

          $this->db->update_batch('sys_shops_wallet_logs',$update_data,'id');
          return true;
        }

      }
    }
  }

  public function delete_process_billing($trandate){
    $trandate = $this->db->escape($trandate);
    $sql = "DELETE FROM sys_billing_processed WHERE billing_date = $trandate";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  ///////////////////////////////////////////////////////////////////////////
  //////////  RANDOM METHOD
  ///////////////////////////////////////////////////////////////////////////

  public function check_mystery_coupon($orderid){
    $orderid = $this->db->escape($orderid);
    $total_amount = 0;
    $sql = "SELECT total_amount, srp_totalamount, order_type
      FROM app_sales_order_logs
      WHERE order_id = $orderid";
    $logs = $this->db->query($sql)->result_array();

    $key = array_search(5,array_column($logs,'order_type'));
    if($key === false){
      return $total_amount;
    }

    foreach($logs as $lkey => $log){
      if($log['order_type'] == 5){
        $total_amount += floatval($log['total_amount']);
      }else{
        $total_amount += floatval($log['srp_totalamount']);
      }
    }

    return $total_amount;
  }

  public function check_item_if_mystery_coupon($orderid,$productid){
    $orderid = $this->db->escape($orderid);
    $productid = $this->db->escape($productid);

    $sql = "SELECT total_amount, srp_totalamount, order_type
      FROM app_sales_order_logs
      WHERE order_id = $orderid AND product_id = $productid AND order_type = 5";
    $query = $this->db->query($sql);
    if($query->num_rows() > 0){
      return $query->row()->total_amount;
    }else{
      return false;
    }
  }
}
