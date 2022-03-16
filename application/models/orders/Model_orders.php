<?php
class Model_orders extends CI_Model {

	public function get_cities() {
		$query = "SELECT * FROM sys_cities
				WHERE enabled = 1
				ORDER BY city_name ASC";

		return $this->db->query($query);

	}

	public function get_citymuns() {
		$query = "SELECT a.*, CONCAT(a.citymunDesc, ' - ', b.provDesc) as citymunName FROM sys_citymun AS a
				LEFT JOIN sys_prov AS b ON a.provCode = b.provCode AND b.status = 1
				WHERE a.status = 1
				ORDER BY a.citymunDesc ASC";

		return $this->db->query($query);

	}
    public function orders_details($reference_num){
        $query = 'SELECT * FROM sys_orders WHERE reference_num = "'.$reference_num.'"';
		return $this->db->query($query)->result_array();
    }

    function order_item_table($reference_num){
        // storing  request (ie, get/post) global array to a variable
		$token_session = $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);
		// $member_id = $this->session->userdata('sys_users_id');
        // $sys_shop = $this->model_orders->get_sys_shop($member_id);

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
			0 => 'id',
			1 => 'itemname',
			2 => 'qty',
			3 => 'amount',
			4 => 'total_amount',
		);
        
        $sql = 'SELECT * FROM sys_orders WHERE reference_num = "'.$reference_num.'"';


        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db2->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();

			$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/img/'.$row['shopcode'].'/products-250/'.$row['productid'].'/'.removeFileExtension($row['primary_pic']).'.jpg?'.rand().'">';
            $parent_prod  = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : '';
			$nestedData[] = ucwords($parent_prod.$row["itemname"]);
			$nestedData[] = $row["qty"];

            $nestedData[] = number_format($row["amount"], 2);
            $nestedData[] = number_format($row["total_amount"], 2);

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

    public function order_table(){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $this->input->post('_record_status');
		$_name 			      = $this->input->post('_name');
		$status 		      = $this->input->post('status');
		$date 		          = $this->input->post('date');
		$location 		      = $this->input->post('location');
		$address 		      = $this->input->post('address');
		$regCode 		      = $this->input->post('regCode');
		$provCode 		      = $this->input->post('provCode');
		$citymunCode	      = $this->input->post('citymunCode');
		$drno	 		      = $this->input->post('drno');
		$order_status_view    = $this->input->post('order_status_view');
		$forpickup            = $this->input->post('forpickup');
		$isconfirmed          = $this->input->post('isconfirmed');
		$_shops 		      = $this->input->post('_shops');
		$date_from 		      = format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));

		$requestData = $_REQUEST;

		switch ($date) {
			case "date_fulfilled":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_fulfilled',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_shipped":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_shipped',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
				
			break;
			case "date_processed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_order_processed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_booking_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_booking_confirmed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_confirmed',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
			case "date_paid":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'payment_date',
						1 => 'reference_num',
						2 => 'name',
						3 => 'conno',
						4 => 'total_amount',
						5 => 'payment_status',
						6 => 'order_status',
						7 => 'shopname',
					);
			break;
		    default:
			$columns = array(
				// datatable column index  => database column name for sorting
					0 => 'date_created',
					1 => 'reference_num',
					2 => 'name',
					3 => 'conno',
					4 => 'total_amount',
					5 => 'payment_status',
					6 => 'order_status'
				);
			break;
		}

		if($status == ""){
			$date_string  = ($_name != "") ? "" : "date_created BETWEEN '".$date_from."' AND '".$date_to."'";
            $sql = "SELECT * FROM sys_orders WHERE ".$date_string;

			// $sql = "SELECT * FROM (
			// 	SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, a.total_amount  as total_amount, a.order_status as order_status, a.payment_status as payment_status, 0 as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
			// 	a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.notes, a.conno, a.payment_date
			// 	FROM `app_order_details` as a USE INDEX(date_ordered)
			// 	WHERE a.payment_status = 0 AND a.status = 1 ".$date_string."
			// 	UNION ALL
			// 	SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount  as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, 0 as shopname, 'none' as delivery_amount,
			// 	b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.notes, b.conno, b.payment_date
			// 	FROM `app_sales_order_details` as b USE INDEX(date_ordered)
			// 	WHERE b.status = 1 AND b.status = 1 ".$date_string2.") as u
			// 	WHERE u.reference_num <> ''";

			// getting records as per search parameters
			if($_name != ""){
				$sql.=" AND (reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
			}

			if($status != ""){
				// if($status == '1'){
				// 	$sql.=" AND payment_data  LIKE '%status_id:".$this->db->escape($status)." %'";
				// }
				// else if($status == '0'){
				// 	$sql.=" AND payment_data = ".$this->db->escape($status)." ";
				// }
                $sql.=" AND payment_data  LIKE '%status_id:".'"'.$this->db->escape($status)." %'";
				// else if($status == '6'){
				// 	$sql.=" AND payment_data = 0 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				// }
				// else if($status == '7'){
				// 	$sql.=" AND payment_data = 1 AND u.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
				// }
				// else{
				// 	$sql.=" AND payment_data = ".$this->db->escape($status)." AND u.payment_status = 1 ";
				// }
			}

			if($location != ''){
				$sql.=" AND shipping_data LIKE '%address".'"'.$address."%' ";
			}
		}
		// else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
			
		// 	if($date === 'date_fulfilled'){
		// 	   	$date_string = ($_name != "") ? "" : "AND b.date_fulfilled BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	}else if($date === 'date_shipped'){
		// 		$date_string = ($_name != "") ? "" : "AND b.date_shipped BETWEEN ".$date_from_2." AND ".$date_to_2."";
		//     }else if($date === 'date_processed'){
		// 	    $date_string = ($_name != "") ? "" : "AND b.date_order_processed BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	}else if($date === 'date_booking_confirmed'){
		// 		$date_string = ($_name != "") ? "" : "AND b.date_booking_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	}else if($date === 'date_confirmed'){
		// 		$date_string = ($_name != "") ? "" : "AND b.date_confirmed BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	}else if($date === 'date_paid'){
		// 		$date_string = ($_name != "") ? "" : "AND b.payment_date BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	}
		// 	else{
		// 	    $date_string = ($_name != "") ? "" : "AND b.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	}
			
		// 	$sql = " SELECT b.id as orderid, b.date_ordered as date_ordered, b.reference_num as reference_num,b.name as name, b.total_amount  as total_amount, b.order_status as order_status, b.payment_status as payment_status, b.sys_shop as sys_shop, c.shopname as shopname,
		// 		b.address, b.regCode, b.provCode, b.citymunCode, b.payment_method, b.exrate_n_to_php as currval, b.currency as currcode, b.conno, b.payment_date, b.date_fulfilled, b.date_shipped, b.date_order_processed, b.date_booking_confirmed, b.date_confirmed, b.payment_date
		// 		FROM `app_sales_order_details` as b
		// 		LEFT JOIN `sys_shops` as c ON b.sys_shop = c.id
		// 		-- LEFT JOIN `app_order_details_shipping` as d ON b.reference_num = d.reference_num AND b.sys_shop = d.sys_shop
		// 		-- LEFT JOIN `app_order_details` as e ON b.reference_num = e.reference_num
		// 		WHERE b.status = 1 AND b.status = 1 ".$date_string." ";

		// 	// getting records as per search parameters
		// 	if($_name != ""){
		// 		$sql.=" AND (b.reference_num LIKE '%".$this->db->escape_like_str($_name)."%')";
		// 	}

		// 	if($status != ""){
		// 		if($status == '1'){
		// 			$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
		// 		}
		// 		else if($status == '0'){
		// 			$sql.=" AND b.payment_status = ".$this->db->escape($status)." ";
		// 		}
		// 		else if($status == '6'){
		// 			$sql.=" AND b.payment_status = 0 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
		// 		}
		// 		else if($status == '7'){
		// 			$sql.=" AND b.payment_status = 1 AND b.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
		// 		}
		// 		else{
		// 			$sql.=" AND b.order_status = ".$this->db->escape($status)." AND b.payment_status = 1 ";
		// 		}
		// 	}

		// 	if($location == 'address'){
		// 		$sql.=" AND b.address LIKE '%".$address."%' ";
		// 	}
		// 	else if($location == 'region'){
		// 		$sql.=" AND b.regCode = '".$regCode."' ";
		// 	}
		// 	else if($location == 'province'){
		// 		$sql.=" AND b.provCode = '".$provCode."' ";
		// 	}
		// 	else if($location == 'citymun'){
		// 		$sql.=" AND b.citymunCode = '".$citymunCode."' ";
		// 	}
			
		// 	if($forpickup == 'fp'){
		// 		$sql .= " AND b.notes LIKE '%|::PA::|%' ";
		// 	}

		// 	if($isconfirmed != ''){
		// 		$sql .= " AND b.isconfirmed = ".$this->db->escape($isconfirmed)." AND b.order_status = 's' ";
		// 	}

		// }
		// else if($status == 0 || $status == '6'){
		// 	$date_string = ($_name != "") ? "" : "AND a.date_ordered BETWEEN ".$date_from_2." AND ".$date_to_2."";
		// 	$_shops      = ($_shops == '') ? 0 : $_shops;

		// 	$sql = " SELECT a.order_id as orderid, a.date_ordered as date_ordered, a.reference_num as reference_num, a.name as name, SUM(s.total_amount) as total_amount, a.order_status as order_status, a.payment_status as payment_status, '".$_shops."' as sys_shop, '".get_company_name()."' as shopname, a.delivery_amount as delivery_amount,
		// 		a.admin_drno, a.address, a.regCode, a.provCode, a.citymunCode, a.payment_method, a.exrate_n_to_php as currval, a.currency as currcode, a.conno, a.payment_date
		// 		FROM `app_order_details` as a USE INDEX(date_ordered)
		// 		LEFT JOIN `app_order_logs` as s ON a.order_id = s.order_id
		// 		WHERE a.payment_status = 0 AND a.status = 1 ".$date_string." ";

		// 	// getting records as per search parameters
		// 	if($_name != ""){
		// 		$sql.=" AND ( a.reference_num LIKE '%".$this->db->escape_like_str($_name)."%') ";
		// 	}

		// 	if($_shops != 0) {
		// 		$sql.=" AND s.sys_shop = ".$this->db->escape($_shops)." ";
		// 	}

		// 	if($status != ""){
		// 		if($status == '1'){
		// 			$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
		// 		}
		// 		else if($status == '0'){
		// 			$sql.=" AND a.payment_status = ".$this->db->escape($status)." ";
		// 		}
		// 		else if($status == '6'){
		// 			$sql.=" AND a.payment_status = 0 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
		// 		}
		// 		else if($status == '7'){
		// 			$sql.=" AND a.payment_status = 1 AND a.payment_method NOT IN ('PayPanda', 'Free Payment', 'Prepayment') ";
		// 		}
		// 		else{
		// 			$sql.=" AND a.order_status = ".$this->db->escape($status)." AND a.payment_status = 1 ";
		// 		}
		// 	}

		// 	if($location == 'address'){
		// 		$sql.=" AND a.address LIKE '%".$address."%' ";
		// 	}
		// 	else if($location == 'region'){
		// 		$sql.=" AND a.regCode = '".$regCode."' ";
		// 	}
		// 	else if($location == 'province'){
		// 		$sql.=" AND a.provCode = '".$provCode."' ";
		// 	}
		// 	else if($location == 'citymun'){
		// 		$sql.=" AND a.citymunCode = '".$citymunCode."' ";
		// 	}

		// 	$sql .= "GROUP BY a.reference_num";
		// }

		// if($status == 0 || $status == '6'){
            // print_r($sql);
            // die();
			$query = $this->db->query($sql);
		// }else{
		// 	$query = $this->db->query($sql);
		// }
		
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	
		$query = $this->db->query($sql);

		$data          = array();
        //var_dump( $query->result_array());
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
            $name = json_decode($row['shipping_data'])->full_name;
            $contact_no = json_decode($row['shipping_data'])->contact_no;
			$subtotal_converted = number_format($row['total_amount'], 2);
            $payment_status = json_decode($row['payment_data'])->status_id;
            $payment_method = json_decode($row['payment_data'])->payment_method_name;
			// if($international == 1){

			// 	$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
			// 	$total_amount_item              = 0;
			// 	foreach($getOrderLogs as $val){
			// 		$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
			// 	}
			// 	$subtotal_amount_conv           = $total_amount_item;

			// 	$subtotal_converted             = displayCurrencyValue_withPHP($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
				
			// }
			// else if($international == 1){
			// 	$getOrderLogs                   = $this->getOrderLogs_orderid($row['orderid'])->result_array();
			// 	$total_amount_item              = 0;
			// 	foreach($getOrderLogs as $val){
			// 		$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
			// 	}
			// 	// $voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
			// 	$subtotal_amount_conv           = $total_amount_item;
			// 	// $delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

			// 	$subtotal_converted             = displayCurrencyValue($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
			// 	// $voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
			// 	// $delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
			// 	// $total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
			// }
			// else{
			// 	$subtotal_converted             = number_format($row['total_amount'], 2);
			// }


			if($date === 'date_fulfilled'){	
		       $nestedData[] = $row["date_fulfilled"];
			}else if($date === 'date_shipped'){
		     	$nestedData[] = $row["date_shipped"];
			}else if($date === 'date_processed'){
			    $nestedData[] = $row["date_order_processed"];   
			}else if($date === 'date_booking_confirmed'){
				$nestedData[] = $row["date_booking_confirmed"];
			}else if($date === 'date_confirmed'){
			    $nestedData[] = $row["date_confirmed"];
			}else if($date === 'date_paid'){
			    $nestedData[] = $row["payment_date"];
			}
			else{
				$nestedData[] = $row["date_created"]; 
			}
		    $special_upper = ["&NTILDE", "&NDASH"];
    		$special_format = ["&Ntilde", "&ndash"];
			$nestedData[] = $row["reference_num"];
			$nestedData[] = str_replace($special_upper, $special_format, $name);
			$nestedData[] = $contact_no;
            $nestedData[] = $subtotal_converted;

			$nestedData[] = display_payment_status($payment_status, $payment_method);
			$nestedData[] = display_order_status($row['status_id']);


            $nestedData[] =
            '<div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
                    <a class="dropdown-item"  href="'.base_url('admin/Main_orders/orders_view/'.$token.'/'.$row["reference_num"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
                </div>
            </div>';

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

}
?>