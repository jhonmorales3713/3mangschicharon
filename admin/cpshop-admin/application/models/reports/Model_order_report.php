<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_order_report extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('reports', TRUE);
    }

    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1 ORDER BY shopname";
		return $this->db2->query($query)->result_array();
    }

    public function get_regions() {
		$query = "SELECT * FROM sys_region
				WHERE status = 1
				ORDER BY regDesc ASC";

		return $this->db2->query($query);

	}

    public function get_provinces() {
		$query = "SELECT * FROM sys_prov
				WHERE status = 1
				ORDER BY provDesc ASC";

		return $this->db2->query($query);

	}

    public function get_citymuns() {
		$query = "SELECT a.*, CONCAT(a.citymunDesc, ' - ', b.provDesc) as citymunName FROM sys_citymun AS a
				LEFT JOIN sys_prov AS b ON a.provCode = b.provCode AND b.status = 1
				WHERE a.status = 1
				ORDER BY a.citymunDesc ASC";

		return $this->db2->query($query);

	}

	public function get_sys_shop($user_id){
		$sql=" SELECT sys_shop FROM app_members WHERE sys_user = ? AND status = 1";
		$sql = $this->db2->query($sql, $user_id);

        if($sql->num_rows() > 0){
            return $sql->row()->sys_shop;
        }else{
            return "";
        }
    }

	

	public function get_branchname($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db2->query($sql, $data);

        if($result->num_rows() > 0){
            $branchname = $result->row()->branchname;
        }else{
            $branchname = 'Main';
        }
        return $branchname;
	}

	public function getOrderLogs_orderid($orderid){
		$sql = "SELECT quantity as qty, amount, exrate_n_to_php as currval FROM app_order_logs WHERE order_id = ? AND status = 1";
		$params = array($orderid);
		
		$result = $this->db2->query($sql, $params);

		if(empty($result->result_array())){
			$sql = "SELECT quantity as qty, amount, exrate_n_to_php as currval FROM app_sales_order_logs WHERE order_id = ? AND status = 1";
			$params = array($orderid);
			
			$result = $this->db2->query($sql, $params);
		}

		return $result;
	}

	public function order_report_table($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $this->input->post('_record_status');
		$_name 			      = $this->input->post('_name');
		$status 		      = $this->input->post('status');
		$location 		      = $this->input->post('location');
		$address 		      = $this->input->post('address');
		$regCode 		      = $this->input->post('regCode');
		$provCode 		      = $this->input->post('provCode');
		$citymunCode	      = $this->input->post('citymunCode');
		$drno	 		      = $this->input->post('drno');
		$order_status_view    = $this->input->post('order_status_view');
		$forpickup            = $this->input->post('forpickup');
		$_shops 		      = $this->input->post('_shops');
		$_branch 		      = $this->input->post('_branch');
		$date_from 		      = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));
		$requestData 		  = $_REQUEST;

		$getShippingData    = $this->getShippingData($_name, $date_from_2_shipping, $date_to_2_shipping)->result_array();
		$getShippingDataArr = [];

		foreach($getShippingData as $row){
			$getShippingDataArr[strval($row['reference_num'])][strval($row['sys_shop'])]['delivery_amount'] = $row['delivery_amount'];
		}

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_ordered',
            1 => 'payment_date',
            2 => 'reference_num',
            3 => 'name',
            4 => 'conno',
            5 => 'total_amount',
            8 => 'delivery_amount',
            9 => 'payment_status',
            10 => 'order_status',
            11 => 'shopname',
		);
		if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
			$branchid = $_branch;
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7' || $status == 'dc'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db2->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == 'dc'){
						$sql.=" AND b.isconfirmed = 1 ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}
			}
		}
		else{
			if($_branch == "main"){
				$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
				$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
				$string_ref        = "";

				foreach($refnumbranchorder as $row){
					$string_ref .= "'".$row['reference_num']."', ";
				}
				$string_ref = rtrim($string_ref, ', ');
				$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
			}
			else{
				$branch_filter = "";
			}

			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7' || $status == 'dc'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
				$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
					FROM `app_sales_order_details` as b
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND b.status = 1 ".$date_string." ";
	
				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
				}

				if($branch_filter != ""){
					$sql.= $branch_filter;
				}
	
			
				if($_shops != '0') {
					$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
				}
	
				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == 'dc'){
						$sql.=" AND b.isconfirmed = 1 ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
	
				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}
				
				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}
	
			}
		}

        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	
		$query = $this->db2->query($sql);

		$data          = array();
		$international = c_international();
		$allow_cod     = cs_clients_info()->c_allow_cod;
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($row['sys_shop'] != 0) {
				// $voucher_total_amount = $this->get_vouchers_total_shop($row["reference_num"], $row["sys_shop"])->total_voucher_amount;
				$voucher_total_amount = 0;
				$subtotal = max($row["total_amount"] - $voucher_total_amount, 0);
			}else{
				$voucher_total_amount = 0;
				$subtotal = $row["total_amount"];
			}

			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
			}

			if($international == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);
				$actual_shipping_fee_conv       = currencyConvertedRate($row['delivery_amount'], $row['currval']);

				$subtotal_converted             = displayCurrencyValue_withPHP($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue_withPHP($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue_withPHP($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue_withPHP(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				$actual_shipping_fee_converted  = displayCurrencyValue_withPHP($row['delivery_amount'], $actual_shipping_fee_conv, $row['currcode']);
			}
			else if($international == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);
				$actual_shipping_fee_conv       = currencyConvertedRate($row['delivery_amount'], $row['currval']);

				$subtotal_converted             = displayCurrencyValue($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				$actual_shipping_fee_converted  = displayCurrencyValue($row['delivery_amount'], $actual_shipping_fee_conv, $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($subtotal, 2);
				$voucher_total_amount_converted = number_format($voucher_total_amount, 2);
				$delivery_amount_converted      = number_format($delivery_amount, 2);
				$total_amount_converted         = number_format(max($subtotal+$delivery_amount, 0), 2);
				$actual_shipping_fee_converted  = number_format($row['delivery_amount'], 2);
			}
			
			$nestedData[] = $row["date_ordered"];
			$nestedData[] = $row["payment_date"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
			$nestedData[] = $row["conno"];
			$nestedData[] = $subtotal_converted;
            // $nestedData[] = $voucher_total_amount_converted;
            $nestedData[] = $delivery_amount_converted;
            $nestedData[] = $total_amount_converted;
            $nestedData[] = $actual_shipping_fee_converted;

			$nestedData[] = display_payment_status($row['payment_status'], $row['payment_method'], $allow_cod);
			$nestedData[] = display_order_status($row['order_status']);

            $nestedData[] = $row['shopname'];

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
            $nestedData[] = $branchname;

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function toktok_booking_report_table($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $this->input->post('_record_status');
		$_name 			      = $this->input->post('_name');
		$status 		      = $this->input->post('status');
		$location 		      = $this->input->post('location');
		$address 		      = $this->input->post('address');
		$regCode 		      = $this->input->post('regCode');
		$provCode 		      = $this->input->post('provCode');
		$citymunCode	      = $this->input->post('citymunCode');
		$drno	 		      = $this->input->post('drno');
		$order_status_view    = $this->input->post('order_status_view');
		$forpickup            = $this->input->post('forpickup');
		$_shops 		      = $this->input->post('_shops');
		$_branch 		      = $this->input->post('_branch');
		$date_from 		      = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));
		$requestData 		  = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_ordered',
            1 => 'reference_num',
            2 => 'rider_name',
            3 => 'rider_conno',
            4 => 'rider_platenum',
            5 => 'delivery_amount',
            6 => 'shopname',
		);

		if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
			$branchid = $_branch;
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE b.order_status = 's' AND b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."
					AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
			}
		}
		else{
			if($_branch == "main"){
				$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
				$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
				$string_ref        = "";

				foreach($refnumbranchorder as $row){
					$string_ref .= "'".$row['reference_num']."', ";
				}
				$string_ref = rtrim($string_ref, ', ');
				$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
			}
			else{
				$branch_filter = "";
			}

			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
				$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
					FROM `app_sales_order_details` as b
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE b.order_status = 's' AND b.status = 1 AND b.status = 1 ".$date_string." 
					AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
	
				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
				}

				if($branch_filter != ""){
					$sql.= $branch_filter;
				}
	
			
				if($_shops != '0') {
					$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
				}
	
				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
			}
		}
	
        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	
		$query = $this->db2->query($sql);

		$data          = array();

		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($row["rider_name"] != ''){
				$nestedData[] = $row["date_ordered"];
				$nestedData[] = $row["reference_num"];
				$nestedData[] = ucwords($row["rider_name"]);
				$nestedData[] = $row["rider_conno"];
				$nestedData[] = $row["rider_platenum"];
				$nestedData[] = $row["delivery_amount"];
				$nestedData[] = $row['shopname'];
				$nestedData[] = $this->get_branchname($row["reference_num"], $row['sys_shop']);

				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function order_report_table_shop($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status    = $this->input->post('_record_status');
		$_name 			   = $this->input->post('_name');
		$status 		   = $this->input->post('status');
		$location 		   = $this->input->post('location');
		$address 		   = $this->input->post('address');
		$regCode 		   = $this->input->post('regCode');
		$provCode 		   = $this->input->post('provCode');
		$citymunCode	   = $this->input->post('citymunCode');
		$forpickup         = $this->input->post('forpickup');
		$_shops 		   = $this->input->post('_shops');
		$_branch 		   = $this->input->post('_branch');
		$date_from 		   = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		   = format_date_reverse_dash($this->input->post('date_to'));
		$token_session     = $this->session->userdata('token_session');
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');
		$order_status_view = $this->input->post('order_status_view');
		$date_from_2       = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$requestData       = $_REQUEST;

		$getShippingData    = $this->getShippingData($_name, $date_from_2, $date_to_2)->result_array();
		$getShippingDataArr = [];

		foreach($getShippingData as $row){
			$getShippingDataArr[strval($row['reference_num'])][strval($row['sys_shop'])]['delivery_amount'] = $row['delivery_amount'];
		}

		$columns = array(
		// datatable column index  => database column name for sorting
			0 => 'date_ordered',
			1 => 'payment_date',
			2 => 'reference_num',
			3 => 'name',
			4 => 'conno',
			5 => 'total_amount',
            8 => 'delivery_amount',
			9 => 'payment_status',
			10 => 'order_status',
			11 => 'shopname',
		);

		if($branchid == 0){
			if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
				$branchid = $_branch;
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
					$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
						b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
						FROM `app_sales_order_details` as b USE INDEX(date_ordered)
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
						LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
						LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
						LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
						-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
						WHERE b.status = 1 AND bor.status = 1
						AND bor.branchid = ".$this->db2->escape_str($branchid)."
						AND bms.branchid = ".$this->db2->escape_str($branchid)."
						AND b.status = 1
						".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."";
	
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND ( b.name LIKE '%".$this->db2->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
					}
	
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
	
					if($location == 'address'){
						$sql.=" AND b.address LIKE '%".$address."%' ";
					}
					else if($location == 'region'){
						$sql.=" AND b.regCode = '".$regCode."' ";
					}
					else if($location == 'province'){
						$sql.=" AND b.provCode = '".$provCode."' ";
					}
					else if($location == 'citymun'){
						$sql.=" AND b.citymunCode = '".$citymunCode."' ";
					}
	
					if($forpickup == 'fp'){
						$sql .= " AND b.notes LIKE '%|::PA::|%' ";
					}
				}
			}
			else{
				if($_branch == "main"){
					$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
					$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
					$string_ref        = "";
	
					foreach($refnumbranchorder as $row){
						$string_ref .= "'".$row['reference_num']."', ";
					}
					$string_ref = rtrim($string_ref, ', ');
					$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
				}
				else{
					$branch_filter = "";
				}
	
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
		
					$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
						b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
						FROM `app_sales_order_details` as b
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
						-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
						WHERE b.status = 1 AND b.status = 1 ".$date_string." ";
		
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
					}
	
					if($branch_filter != ""){
						$sql.= $branch_filter;
					}
				
					if($_shops != '0') {
						$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
					}
		
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
		
					if($location == 'address'){
						$sql.=" AND b.address LIKE '%".$address."%' ";
					}
					else if($location == 'region'){
						$sql.=" AND b.regCode = '".$regCode."' ";
					}
					else if($location == 'province'){
						$sql.=" AND b.provCode = '".$provCode."' ";
					}
					else if($location == 'citymun'){
						$sql.=" AND b.citymunCode = '".$citymunCode."' ";
					}
					
					if($forpickup == 'fp'){
						$sql .= " AND b.notes LIKE '%|::PA::|%' ";
					}
		
				}
			}
		}else{
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db2->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}
			}
		}

        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db2->query($sql);
		$international = c_international();
		$allow_cod     = cs_clients_info()->c_allow_cod;
		$data          = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($row['sys_shop'] != 0) {
				// $voucher_total_amount = $this->get_vouchers_total_shop($row["reference_num"], $row["sys_shop"])->total_voucher_amount;
				$voucher_total_amount = 0;
				$subtotal = max($row["total_amount"] - $voucher_total_amount, 0);
			}else{
				$voucher_total_amount = 0;
				$subtotal = $row["total_amount"];
			}

			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
			}

			if($international == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);
				$actual_shipping_fee_conv       = currencyConvertedRate($row['delivery_amount'], $row['currval']);

				$subtotal_converted             = displayCurrencyValue_withPHP($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue_withPHP($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue_withPHP($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue_withPHP(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				$actual_shipping_fee_converted  = displayCurrencyValue_withPHP($row['delivery_amount'], $actual_shipping_fee_conv, $row['currcode']);
			}
			else if($international == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);
				$actual_shipping_fee_conv       = currencyConvertedRate($row['delivery_amount'], $row['currval']);

				$subtotal_converted             = displayCurrencyValue($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				$actual_shipping_fee_converted  = displayCurrencyValue($row['delivery_amount'], $actual_shipping_fee_conv, $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($subtotal, 2);
				$voucher_total_amount_converted = number_format($voucher_total_amount, 2);
				$delivery_amount_converted      = number_format($delivery_amount, 2);
				$total_amount_converted         = number_format(max($subtotal+$delivery_amount, 0), 2);
				$actual_shipping_fee_converted  = number_format($row['delivery_amount'], 2);
			}

			$nestedData[] = $row["date_ordered"];
			$nestedData[] = $row["payment_date"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = ucwords($row["name"]);
			$nestedData[] = $row["conno"];
            $nestedData[] = $subtotal_converted;
            // $nestedData[] = $voucher_total_amount_converted;
            $nestedData[] = $delivery_amount_converted;
            $nestedData[] = $total_amount_converted;
            $nestedData[] = $actual_shipping_fee_converted;

           	$nestedData[] = display_payment_status($row['payment_status'], $row['payment_method'], $allow_cod);
           	$nestedData[] = display_order_status($row['order_status']);

            $nestedData[] = $row['shopname'];

            $branchname = $this->get_branchname($row["reference_num"], $row['sys_shop']);
			$nestedData[] = $branchname;

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function toktok_booking_report_table_shop($sys_shop){
		// storing  request (ie, get/post) global array to a variable
		$_record_status    = $this->input->post('_record_status');
		$_name 			   = $this->input->post('_name');
		$status 		   = $this->input->post('status');
		$location 		   = $this->input->post('location');
		$address 		   = $this->input->post('address');
		$regCode 		   = $this->input->post('regCode');
		$provCode 		   = $this->input->post('provCode');
		$citymunCode	   = $this->input->post('citymunCode');
		$forpickup         = $this->input->post('forpickup');
		$_shops 		   = $this->input->post('_shops');
		$_branch 		   = $this->input->post('_branch');
		$date_from 		   = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		   = format_date_reverse_dash($this->input->post('date_to'));
		$token_session     = $this->session->userdata('token_session');
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');
		$order_status_view = $this->input->post('order_status_view');
		$date_from_2       = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$requestData       = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'date_ordered',
            1 => 'reference_num',
            2 => 'rider_name',
            3 => 'rider_conno',
            4 => 'rider_platenum',
            5 => 'delivery_amount',
            6 => 'shopname',
		);

		if($branchid == 0){
			if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
				$branchid = $_branch;
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
					$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
						FROM `app_sales_order_details` as b USE INDEX(date_ordered)
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
						LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
						LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
						LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
						WHERE b.order_status = 's' AND b.status = 1 AND bor.status = 1
						AND bor.branchid = ".$this->db2->escape_str($branchid)."
						AND bms.branchid = ".$this->db2->escape_str($branchid)."
						AND b.status = 1
						".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."
						AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
	
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND ( b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
					}
	
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
				}
			}
			else{
				if($_branch == "main"){
					$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
					$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
					$string_ref        = "";
	
					foreach($refnumbranchorder as $row){
						$string_ref .= "'".$row['reference_num']."', ";
					}
					$string_ref = rtrim($string_ref, ', ');
					$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
				}
				else{
					$branch_filter = "";
				}
	
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
		
					$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
						FROM `app_sales_order_details` as b
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
						WHERE b.order_status = 's' AND b.status = 1 AND b.status = 1 ".$date_string." 
						AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
		
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
					}
	
					if($branch_filter != ""){
						$sql.= $branch_filter;
					}
				
					if($_shops != '0') {
						$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
					}
		
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
				}
			}
		}else{
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($sys_shop)."
					AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
			}
		}

        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db2->query($sql);
		$data          = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			if($row["rider_name"] != ''){
				$nestedData[] = $row["date_ordered"];
				$nestedData[] = $row["reference_num"];
				$nestedData[] = ucwords($row["rider_name"]);
				$nestedData[] = $row["rider_conno"];
				$nestedData[] = $row["rider_platenum"];
				$nestedData[] = $row["delivery_amount"];
				$nestedData[] = $row['shopname'];
				$nestedData[] = $this->get_branchname($row["reference_num"], $row['sys_shop']);

				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function order_report_table_export($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$_shops 		= $this->input->post('_shops_export');
		$_branch 		= $this->input->post('_branch_export');
		$forpickup 		= $this->input->post('forpickup_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$date_from_2    = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
			$branchid = $_branch;
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db2->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}
			}
		}
		else{
			if($_branch == "main"){
				$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
				$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
				$string_ref        = "";

				foreach($refnumbranchorder as $row){
					$string_ref .= "'".$row['reference_num']."', ";
				}
				$string_ref = rtrim($string_ref, ', ');
				$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
			}
			else{
				$branch_filter = "";
			}

			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
				$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
					FROM `app_sales_order_details` as b
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND b.status = 1 ".$date_string." ";
	
				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
				}

				if($branch_filter != ""){
					$sql.= $branch_filter;
				}
	
			
				if($_shops != '0') {
					$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
				}
	
				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
	
				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}
				
				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}
	
			}
		}

		$sql .= " ORDER BY date_ordered ASC";

		return $this->db2->query($sql)->result_array();
	}

	public function toktok_booking_report_table_export($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$_shops 		= $this->input->post('_shops_export');
		$_branch 		= $this->input->post('_branch_export');
		$forpickup 		= $this->input->post('forpickup_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$date_from_2    = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
			$branchid = $_branch;
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."
					AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
			}
		}
		else{
			if($_branch == "main"){
				$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
				$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
				$string_ref        = "";

				foreach($refnumbranchorder as $row){
					$string_ref .= "'".$row['reference_num']."', ";
				}
				$string_ref = rtrim($string_ref, ', ');
				$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
			}
			else{
				$branch_filter = "";
			}

			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
				$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
					FROM `app_sales_order_details` as b
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE b.status = 1 AND b.status = 1 ".$date_string." 
					AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
	
				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
				}

				if($branch_filter != ""){
					$sql.= $branch_filter;
				}
	
			
				if($_shops != '0') {
					$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
				}
	
				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
			}
		}

		$sql .= " ORDER BY date_ordered ASC";

		return $this->db2->query($sql)->result_array();
	}

	public function order_report_table_shop_export($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$forpickup		= $this->input->post('forpickup_export');
		$_shops 		= $this->input->post('_shops_export');
		$_branch 		= $this->input->post('_branch_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$branchid 		= $this->session->userdata('branchid');
		$date_from_2    = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($branchid == 0){
			if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
				$branchid = $_branch;
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
					$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
						b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
						FROM `app_sales_order_details` as b USE INDEX(date_ordered)
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
						LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
						LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
						LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
						-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
						WHERE b.status = 1 AND bor.status = 1
						AND bor.branchid = ".$this->db2->escape_str($branchid)."
						AND bms.branchid = ".$this->db2->escape_str($branchid)."
						AND b.status = 1
						".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."";
	
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND ( b.name LIKE '%".$this->db2->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
					}
	
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
	
					if($location == 'address'){
						$sql.=" AND b.address LIKE '%".$address."%' ";
					}
					else if($location == 'region'){
						$sql.=" AND b.regCode = '".$regCode."' ";
					}
					else if($location == 'province'){
						$sql.=" AND b.provCode = '".$provCode."' ";
					}
					else if($location == 'citymun'){
						$sql.=" AND b.citymunCode = '".$citymunCode."' ";
					}
	
					if($forpickup == 'fp'){
						$sql .= " AND b.notes LIKE '%|::PA::|%' ";
					}
				}
			}
			else{
				if($_branch == "main"){
					$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
					$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
					$string_ref        = "";
	
					foreach($refnumbranchorder as $row){
						$string_ref .= "'".$row['reference_num']."', ";
					}
					$string_ref = rtrim($string_ref, ', ');
					$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
				}
				else{
					$branch_filter = "";
				}
	
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
		
					$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
						b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
						FROM `app_sales_order_details` as b
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
						-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
						WHERE b.status = 1 AND b.status = 1 ".$date_string." ";
		
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
					}
	
					if($branch_filter != ""){
						$sql.= $branch_filter;
					}
				
					if($_shops != '0') {
						$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
					}
		
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
		
					if($location == 'address'){
						$sql.=" AND b.address LIKE '%".$address."%' ";
					}
					else if($location == 'region'){
						$sql.=" AND b.regCode = '".$regCode."' ";
					}
					else if($location == 'province'){
						$sql.=" AND b.provCode = '".$provCode."' ";
					}
					else if($location == 'citymun'){
						$sql.=" AND b.citymunCode = '".$citymunCode."' ";
					}
					
					if($forpickup == 'fp'){
						$sql .= " AND b.notes LIKE '%|::PA::|%' ";
					}
		
				}
			}
		}else{
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
					b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.delivery_amount
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($sys_shop)."";

				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.name LIKE '%".$this->db2->escape_like_str($_name)."%' OR b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}

				if($location == 'address'){
					$sql.=" AND b.address LIKE '%".$address."%' ";
				}
				else if($location == 'region'){
					$sql.=" AND b.regCode = '".$regCode."' ";
				}
				else if($location == 'province'){
					$sql.=" AND b.provCode = '".$provCode."' ";
				}
				else if($location == 'citymun'){
					$sql.=" AND b.citymunCode = '".$citymunCode."' ";
				}

				if($forpickup == 'fp'){
					$sql .= " AND b.notes LIKE '%|::PA::|%' ";
				}
			}
		}

		$sql .= " ORDER BY date_ordered ASC";

		return $this->db2->query($sql)->result_array();
	}

	public function toktok_booking_report_table_shop_export($sys_shop){
		$_record_status = $this->input->post('_record_status_export');
		$_name 			= $this->input->post('_name_export');
		$status 		= $this->input->post('status_export');
		$location 		= $this->input->post('location_export');
		$address 		= $this->input->post('address_export');
		$regCode 		= $this->input->post('regCode_export');
		$provCode 		= $this->input->post('provCode_export');
		$citymunCode	= $this->input->post('citymunCode_export');
		$drno	 		= $this->input->post('drno_export');
		$forpickup		= $this->input->post('forpickup_export');
		$_shops 		= $this->input->post('_shops_export');
		$_branch 		= $this->input->post('_branch_export');
		$date_from 		= format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 		= format_date_reverse_dash($this->input->post('date_to_export'));
		$branchid 		= $this->session->userdata('branchid');
		$date_from_2    = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2      = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		if($branchid == 0){
			if($_branch != '' && $_branch != 'main' && $_branch != 'all'){
				$branchid = $_branch;
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
	
					$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
						FROM `app_sales_order_details` as b USE INDEX(date_ordered)
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
						LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
						LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
						LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
						WHERE b.status = 1 AND bor.status = 1
						AND bor.branchid = ".$this->db2->escape_str($branchid)."
						AND bms.branchid = ".$this->db2->escape_str($branchid)."
						AND b.status = 1
						".$date_string." AND b.sys_shop = ".$this->db2->escape_str($_shops)."
						AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
	
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND ( b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
					}
	
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
				}
			}
			else{
				if($_branch == "main"){
					$fromdate2 = date('Y-m-d',strtotime($date_from." -2 days"));
					$refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
					$string_ref        = "";
	
					foreach($refnumbranchorder as $row){
						$string_ref .= "'".$row['reference_num']."', ";
					}
					$string_ref = rtrim($string_ref, ', ');
					$branch_filter = " AND b.reference_num NOT IN ($string_ref)";
				}
				else{
					$branch_filter = "";
				}
	
				if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
					$date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
		
					$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
						FROM `app_sales_order_details` as b
						LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
						LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
						WHERE b.status = 1 AND b.status = 1 ".$date_string." 
						AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
		
					// getting records as per search parameters
					if($_name != ""){
						$sql.=" AND (b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%')";
					}
	
					if($branch_filter != ""){
						$sql.= $branch_filter;
					}
				
					if($_shops != '0') {
						$sql.=" AND b.sys_shop = ".$this->db2->escape($_shops)." ";
					}
		
					if($status != ""){
						if($status == '1'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '0'){
							$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
						}
						else if($status == '6'){
							$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else if($status == '7'){
							$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
						}
						else{
							$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
						}
					}
				}
			}
		}else{
			if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$date_string  = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";

				$sql=" SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num, b.sys_shop as sys_shop, c.shopname as shopname, b.delivery_amount, rider.rider_name, rider.rider_conno, rider.rider_platenum
					FROM `app_sales_order_details` as b USE INDEX(date_ordered)
					LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
					LEFT JOIN `sys_branch_orders` bor ON b.reference_num = bor.orderid
					LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
					LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = b.sys_shop
					LEFT JOIN `app_sales_order_details_rider` as rider ON b.id = rider.app_sales_order_id AND rider.status = 1
					WHERE b.status = 1 AND bor.status = 1
					AND bor.branchid = ".$this->db2->escape_str($branchid)."
					AND bms.branchid = ".$this->db2->escape_str($branchid)."
					AND b.status = 1
					".$date_string." AND b.sys_shop = ".$this->db2->escape_str($sys_shop)."
					AND b.t_deliveryId <> '' AND b.delivery_amount <> '' AND b.t_deliveryId IS NOT NULL AND b.delivery_amount IS NOT NULL ";
				// getting records as per search parameters
				if($_name != ""){
					$sql.=" AND ( b.reference_num LIKE '%".$this->db2->escape_like_str($_name)."%' ) ";
				}

				if($status != ""){
					if($status == '1'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '0'){
						$sql.=" AND b.payment_status = ".$this->db2->escape($status)." ";
					}
					else if($status == '6'){
						$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else if($status == '7'){
						$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
					}
					else{
						$sql.=" AND b.order_status = ".$this->db2->escape($status)." AND b.payment_status = 1 ";
					}
				}
			}
		}

		$sql .= " ORDER BY date_ordered ASC";

		return $this->db2->query($sql)->result_array();
	}

	public function getShippingData($_name, $date_from, $date_to){
		$where_string = ($_name != "") ? "reference_num = '".$_name."'" : "created BETWEEN ".$date_from." AND ".$date_to."";
		$sql = "SELECT * FROM app_order_details_shipping WHERE ".$where_string." AND status = 1";

		
		$result = $this->db->query($sql);

		return $result;
	}

	public function getShopData(){
		$sql = "SELECT * FROM sys_shops";

		
		$result = $this->db->query($sql);

		return $result;
	}

	public function get_vouchers_total_shop($reference_num, $sys_shop) {
		$query=" SELECT SUM(amount) as total_voucher_amount FROM app_order_payment
		WHERE status = 1 AND payment_type = 'toktokmall' AND order_ref_num = ? AND shopid = ?";
		$params = array($reference_num, $sys_shop);
		return $this->db->query($query, $params)->row();
	}

	public function getBranchOrders_RefNum($fromdate){
        $sql="SELECT orderid as reference_num FROM sys_branch_orders WHERE DATE(date_created) > ? AND status = 1";
        $data = array($fromdate );
        $result = $this->db2->query($sql, $data)->result_array();

        return $result;
	}



	// Order List Payout Status Report

	public function get_delivery_amount($export_data){


		$custom_query = "";

		if($export_data['_shops'] != ""){
			$custom_query.="WHERE  sys_shop = '".$export_data['_shops']."' ";
		}


		$sql = "SELECT delivery_amount, reference_num, sys_shop  FROM  app_order_details_shipping  ".$custom_query.""; 
        $query = $this->db2->query($sql); 

		return $query->result_array();
	}		


	public function get_delivery_amount_export($export_data){


		$custom_query = "";

		if($export_data['_shops_export'] != ""){
			$custom_query.="WHERE  sys_shop = '".$export_data['_shops_export']."' ";
		}


		$sql = "SELECT delivery_amount, reference_num, sys_shop  FROM  app_order_details_shipping  ".$custom_query.""; 
        $query = $this->db2->query($sql); 

		return $query->result_array();
	}		


	
	public function filter_delivery_Amount($data,$cond){
		$return_data = array();


		foreach($data as $key => $row){
			if($row['sys_shop'] == $cond['sys_shop']  && $row['reference_num'] == $cond['reference_num']){
				$return_data[] = $data[$key];
			}
		}


		return $return_data;
	}

	public function get_refcom_totalamount(){

		$sql = "
		SELECT 
		`order_id`,
		`total_amount`,
		`refcom_rate`,
		(`total_amount` * `refcom_rate`) as `total`
		FROM 
		app_sales_order_logs"; 
        $query = $this->db2->query($sql); 

		return $query->result_array();

	}	


	public function get_referral_code(){

		$sql="SELECT referral_code, order_reference_num FROM app_referral_codes WHERE status = 1";
		$query = $this->db2->query($sql); 

		return $query->result_array();

	}

	public function get_sys_shopname(){

		$sql="SELECT id, shopname FROM sys_shops WHERE status = 1";
		$query = $this->db2->query($sql); 

		return $query->result_array();

	}


	public function get_sys_billing($export_data){

		
		$custom_query = "";

		if($export_data['_bill_code'] != ""){
			$custom_query.=" AND billcode = '".$export_data['_bill_code']."' ";
		}

		// if($export_data['_bill_code_export'] != ""){
		// $custom_query.=" AND   billcode LIKE '%" . $this->db2->escape_like_str($export_data['_bill_code_export']) . "%' ";
		// }

		$sql="SELECT billcode, Date(trandate) as sys_trandate, syshop, branch_id FROM sys_billing WHERE status = 1  ". $custom_query ." ";
		$query = $this->db2->query($sql); 

		return $query->result_array();

	

	}



	public function get_sys_billing_export($export_data){

		
		$custom_query = "";

	

		if($export_data['_bill_code_export'] != ""){
		$custom_query.=" AND   billcode  = '".$export_data['_bill_code_export']."' ";
		}

		$sql="SELECT billcode, Date(trandate) as sys_trandate, syshop, branch_id FROM sys_billing WHERE status = 1  ". $custom_query ." ";
		$query = $this->db2->query($sql); 

		return $query->result_array();

		// print_r($query);
		// die();


	}

	public function filter_sys_billing($data,$cond){
		$return_data = array();



		switch (ini()) {
			case "jcww":

					
					foreach($data as $key => $row){
						if($row['sys_trandate'] == $cond['date_confirmed']  && $row['syshop'] == $cond['sys_shop']){
							$return_data[] = $data[$key];
						}
					}
			
			break;
			default:

					foreach($data as $key => $row){
						if($row['sys_trandate'] == $cond['trandate']  && $row['syshop'] == $cond['sys_shop']){
							$return_data[] = $data[$key];
						}
					}

			break;
		}
	

		return $return_data;
	}
	
	
	private function __debugger($rs){
		echo "<pre>";
		echo(count($rs));
		echo "<hr>";
		print_r($rs);
		echo "</pre>";
		die("");
	}

	public function order_list_payout_status_report_model($export_data){

		$requestData 		  = $_REQUEST;
		
			$columns = array(
				0 => 'trandate',
				1 => 'billcode',
				2 => 'sys_shop',
				3 => 'reference_num',
				4 => 'name',
				5 => 'paypanda_ref',
				6 => 'delivery_amount',
				7 => 'total_amount',
				8 => 'refcom_totalamount',
				9 => 'Net_Amount'
			);

 
			// sub queries
			$refcom_totalamount     = $this->get_refcom_totalamount();
			$referral_code          = $this->get_referral_code();
			$shop_name               = $this->get_sys_shopname();
			$delivery_amount        = $this->get_delivery_amount($export_data);
			$billing_code           = $this->get_sys_billing($export_data);
			

			$custom_query = "";

			$custom_query .="WHERE a.status = 1  AND a.date_shipped != '0000-00-00 00:00:00'";

			switch (ini()) {
				case "jcww":

					
					if($export_data['date_from'] != ""){
						$custom_query.=" AND DATE_FORMAT(a.`date_confirmed`, '%m/%d/%Y') ='".$export_data['date_from']. "'";
					}
				
				break;
				default:

					if($export_data['date_from'] != ""){
						$custom_query.=" AND DATE_FORMAT(a.`date_shipped`, '%m/%d/%Y') ='".$export_data['date_from']. "'";
					}

				break;
			}


			if($export_data['_order_ref'] != ""){
				$custom_query.=" AND a.reference_num LIKE '%" . $this->db2->escape_like_str($export_data['_order_ref']) . "%' ";
			}


			if($export_data['_payment_ref'] != ""){
				$custom_query.=" AND a.paypanda_ref LIKE '%" . $this->db2->escape_like_str($export_data['_payment_ref']) . "%' ";
			}
			
			if($export_data['_shops'] != ""){
				$custom_query.=" AND a.sys_shop = '" . $this->db2->escape_like_str($export_data['_shops']) . "' ";
			}
		
			$sql = "
			SELECT 
			DATE(`a`.`date_shipped`)  as trandate,
			`a`.`id`,
			`a`.`sys_shop`,
			`a`.`reference_num`,
			`a`.`paypanda_ref`,
			`a`.`user_id`,
			`a`.`name`,
			`a`.`total_amount`,
			`a`.`srp_totalamount`,
			DATE(`a`.`date_confirmed`) as date_confirmed
			FROM app_sales_order_details a
			LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum 
			". $custom_query ."
			GROUP BY `a`.`id`
			";

	        $query = $this->db2->query($sql);

			$main_rs = $query->result_array();

		

			// preparation
			$rs = [];
			$i = 0;

			// simulation process
			if(!empty($main_rs[0]['id']) !== false){

				foreach($main_rs as $k => $v){

					$billing_code   = $this->get_sys_billing($export_data);

					$billing_array =  $this->filter_sys_billing($billing_code,$v);

					// print_r($v);
					// die();

					$bc = '';
					foreach($billing_array as $k1 => $v2){
					        
						$bc = $v2['billcode'];

					}
				    $main_rs[$i]['billcode'] =  $bc;
					// // $main_rs[$k]['sys_trandate'] = $billing_array['sys_trandate'];

				
					// reset 
					$ref_com = [];

					// ref com
					$ref_com = array_keys(array_column($refcom_totalamount, 'order_id'), $v['id']);

					$main_rs[$i]['ref_com'] = $ref_com;

					$rc = 0;
					foreach($main_rs[$i]['ref_com'] as $k1 => $v1){
						$rc += (double)$refcom_totalamount[$v1]['total_amount'] * (double)$refcom_totalamount[$v1]['refcom_rate'];
					}

					$main_rs[$i]['refcom_total_amount'] = $rc;



					$delivery_amount        = $this->get_delivery_amount($export_data);

					$delivery_amount_array =  $this->filter_delivery_Amount($delivery_amount,$v);


					$da = '';
					foreach($delivery_amount_array as $k1 => $v3){
					        
						$da = $v3['delivery_amount'];

					}
				    $main_rs[$i]['delivery_amount'] =  $da;

					
					// referral_code
					$referralcode = array_keys(array_column($referral_code, 'order_reference_num'), $v['reference_num']);

					$main_rs[$i]['referral_code'] = $referralcode;

					$rcod = '';
					foreach($main_rs[$i]['referral_code'] as $k1 => $v1){
						$rcod = $referral_code[$v1]['referral_code'];
					}
				
					$main_rs[$i]['referral_code'] = $rcod;


					// shopname
					$shopname = array_keys(array_column($shop_name, 'id'), $v['sys_shop']);

					$main_rs[$i]['shopname'] = $shopname;

					$s = '';
					foreach($main_rs[$i]['shopname'] as $k1 => $v1){
						$s = $shop_name[$v1]['shopname'];
					}

					$main_rs[$i]['shopname'] = $s;

					$i++;

					
				}

			}

			// return $this->__debugger($main_rs);

	
		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
	
		$sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query = $this->db2->query($sql);
		   
		$data = array();
		  
		  $count = 0;
		   foreach( $main_rs as $row )
		   {
			 




			switch (ini()) {
				case "toktokmall":
								if($row['billcode'] != ''){
									$nestedData=array();
									$nestedData[] = $row['trandate']; 
									$nestedData[] = $row['billcode'];
									$nestedData[] = $row['shopname'];
									$nestedData[] = $row['reference_num'];
									$nestedData[] = $row['name'];
									$nestedData[] = $row['paypanda_ref'];
					
									$order_type = 'Regular Order';
									if($row['user_id'] != 0){
									$order_type = "Thru OF Login";
									}
					
									if($rcod != "" || $rcod != null){
										$order_type = "Via OF Shoplink";
									}
					
					
									$NetAmount =
									$row['delivery_amount'] + 
									$row['srp_totalamount'] - 
									$row['refcom_total_amount'];
								
									$nestedData[] = $order_type;
									$nestedData[] = $row['srp_totalamount'];
									$nestedData[] = $row['delivery_amount'];
									$nestedData[] = number_format($row['refcom_total_amount'], 2);
									$nestedData[] = number_format($NetAmount, 2);
									
									$data[] = $nestedData;
					
								}else{
									$count++;
								}	
							
				break;
				default:

						if($row['billcode'] != ''){
							$nestedData=array();
							$nestedData[] = $row['trandate']; 
							$nestedData[] = $row['billcode'];
							$nestedData[] = $row['shopname'];
							$nestedData[] = $row['reference_num'];
							$nestedData[] = $row['name'];
							$nestedData[] = $row['paypanda_ref'];
			
							$order_type = 'Regular Order';
							if($row['user_id'] != 0){
							$order_type = "Thru OF Login";
							}
			
							if($rcod != "" || $rcod != null){
								$order_type = "Via OF Shoplink";
							}
			
			
							$NetAmount =
							$row['delivery_amount'] + 
							$row['total_amount'] - 
							$row['refcom_total_amount'];
						
							$nestedData[] = $order_type;
							$nestedData[] = number_format($row['total_amount'],2);
							$nestedData[] = number_format($row['delivery_amount'],2);
							$nestedData[] = number_format($row['refcom_total_amount'], 2);
							$nestedData[] = number_format($NetAmount, 2);
							
							$data[] = $nestedData;
			
						}else{
							$count++;
						}	

				break;
			}





		

		   }
	   
		   $json_data = array(
	   
			 "recordsTotal"    => intval( $totalData -$count),
			 "recordsFiltered" => intval( $totalFiltered -$count),
			 "data"            => $data
		   );
	   
		   return $json_data;
		  
	
	  }






	  public function order_list_payout_status_report_export($export_data){



		$columns = array(
			0 => 'trandate',
			1 => 'billcode',
			2 => 'sys_shop',
			3 => 'reference_num',
			4 => 'name',
			5 => 'paypanda_ref',
			6 => 'delivery_amount',
			7 => 'total_amount',
			8 => 'refcom_totalamount',
			9 => 'Net_Amount'
		);


		// sub queries
		$refcom_totalamount     = $this->get_refcom_totalamount();
		$referral_code          = $this->get_referral_code();
		$shop_name               = $this->get_sys_shopname();
		$delivery_amount        = $this->get_delivery_amount_export($export_data);
		$billing_code           = $this->get_sys_billing_export($export_data);

		$custom_query = "";

		$custom_query .="WHERE a.status = 1 AND a.date_shipped != '0000-00-00 00:00:00' ";


		switch (ini()) {
			case "jcww":
				if($export_data['date_from_export'] != ""){
					$custom_query.=" AND DATE_FORMAT(a.`date_confirmed`, '%m/%d/%Y') ='".$export_data['date_from_export']. "'";
				 }
			
			break;
			default:

				if($export_data['date_from_export'] != ""){
					$custom_query.=" AND DATE_FORMAT(a.`date_shipped`, '%m/%d/%Y') ='".$export_data['date_from_export']. "'";
				}

			break;
		}

	

		if($export_data['_order_ref_export'] != ""){
			$custom_query.=" AND a.reference_num LIKE '%" . $this->db2->escape_like_str($export_data['_order_ref_export']) . "%' ";
		}


		if($export_data['_payment_ref_export'] != ""){
			$custom_query.=" AND a.paypanda_ref LIKE '%" . $this->db2->escape_like_str($export_data['_payment_ref_export']) . "%' ";
		}
		
		if($export_data['_shops_export'] != ""){
			$custom_query.=" AND a.sys_shop LIKE '%" . $this->db2->escape_like_str($export_data['_shops_export']) . "%' ";
		}
	
	
		
		$sql = "
		SELECT 
		DATE(`a`.`date_shipped`)  as trandate,
		`a`.`id`,
		`a`.`sys_shop`,
		`a`.`reference_num`,
		`a`.`paypanda_ref`,
		`a`.`user_id`,
		`a`.`name`,
		`a`.`total_amount`,
		`a`.`srp_totalamount`,
		DATE(`a`.`date_confirmed`) as date_confirmed
		FROM app_sales_order_details a
		LEFT JOIN app_order_branch_details b ON a.reference_num = b.order_refnum  
		". $custom_query ."
		GROUP BY `a`.`id`
		";

		$query = $this->db2->query($sql);

		$main_rs = $query->result_array();

	

		// preparation
		$rs = [];
		$i = 0;

		// simulation process
		if(!empty($main_rs[0]['id']) !== false){

			foreach($main_rs as $k => $v){

				$billing_code   = $this->get_sys_billing_export($export_data);

				$billing_array =  $this->filter_sys_billing($billing_code,$v);

				$bc = '';
				foreach($billing_array as $k1 => $v2){
						
					$bc = $v2['billcode'];

				}
				$main_rs[$i]['billcode'] =  $bc;
				// // $main_rs[$k]['sys_trandate'] = $billing_array['sys_trandate'];

				// reset 
				$ref_com = [];

				// ref com
				$ref_com = array_keys(array_column($refcom_totalamount, 'order_id'), $v['id']);

				$main_rs[$i]['ref_com'] = $ref_com;
				// reset 
				$ref_com = [];

				// ref com
				$ref_com = array_keys(array_column($refcom_totalamount, 'order_id'), $v['id']);

				$main_rs[$i]['ref_com'] = $ref_com;

				$rc = 0;
				foreach($main_rs[$i]['ref_com'] as $k1 => $v1){
					$rc += (double)$refcom_totalamount[$v1]['total_amount'] * (double)$refcom_totalamount[$v1]['refcom_rate'];
				}

				$main_rs[$i]['refcom_total_amount'] = $rc;

            	// // Delivery Amount
				$delivery_amount        = $this->get_delivery_amount_export($export_data);

				$delivery_amount_array =  $this->filter_delivery_Amount($delivery_amount,$v);


				$da = '';
				foreach($delivery_amount_array as $k1 => $v3){
						
					$da = $v3['delivery_amount'];

				}
				$main_rs[$i]['delivery_amount'] =  $da;


				// // Delivery Amount
				// $deliveryamount = array_keys(array_column($delivery_amount, 'reference_num'), $v['reference_num']);

				// $main_rs[$i]['delivery_amount'] = $deliveryamount;

				// $da = 0;
				// foreach($main_rs[$i]['delivery_amount'] as $k1 => $v1){
				// 	$da = $delivery_amount[$v1]['delivery_amount'];
				// }
			
				// $main_rs[$i]['delivery_amount'] = $da;


				
				// referral_code
				$referralcode = array_keys(array_column($referral_code, 'order_reference_num'), $v['reference_num']);

				$main_rs[$i]['referral_code'] = $referralcode;

				$rcod = '';
				foreach($main_rs[$i]['referral_code'] as $k1 => $v1){
					$rcod = $referral_code[$v1]['referral_code'];
				}
			
				$main_rs[$i]['referral_code'] = $rcod;


				// shopname
				$shopname = array_keys(array_column($shop_name, 'id'), $v['sys_shop']);

				$main_rs[$i]['shopname'] = $shopname;

				$s = '';
				foreach($main_rs[$i]['shopname'] as $k1 => $v1){
					$s = $shop_name[$v1]['shopname'];
				}

				$main_rs[$i]['shopname'] = $s;

				$i++;
			}

		}

	

        return $main_rs;
			  
	
	  }
	
        
}