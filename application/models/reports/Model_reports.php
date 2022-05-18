<?php
class Model_reports extends CI_Model {
    
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
        $query = 'SELECT a.*,b.date_created as payment_date,b.payment_data as payment_details FROM sys_orders a LEFT JOIN sys_payments b on a.order_id=b.order_id WHERE a.order_id = "'.$reference_num.'"';
		return $this->db->query($query)->result_array();
    }

    
	public function get_productinfo($id='') {
		$query="SELECT * FROM sys_products_images join sys_products on product_id = sys_products.id  WHERE status = 1 and product_id = ".$id;
		return $this->db->query($query)->result_array();
    }
	public function get_productinfo_parent($id='') {
		$query="SELECT * FROM sys_products where id = ".$id;
		return $this->db->query($query)->result_array();
    }

	public function get_inventorydetails($Id,$uniqueID = '') {
		$field = 'product_id';
		if($uniqueID != ''){
			$field = 'id';
			$Id = $uniqueID;
		}
		$query=" SELECT * from sys_inventory
		WHERE $field = ? AND status = 1 AND date_expiration >  CURRENT_DATE() ORDER BY date_expiration ASC";
		$params = array($Id);
		return $this->db->query($query, $params)->result_array();
	}
	public function get_inventorydetails2($Id,$uniqueID = '') {
		$field = 'product_id';
		if($uniqueID != ''){
			$field = 'id';
			$Id = $uniqueID;
		}
		$query=" SELECT * from sys_inventory
		WHERE $field = ? ORDER BY date_expiration ASC";
		$params = array($Id);
		return $this->db->query($query, $params)->result_array();
	}
	public function get_total_order_amount($orderdata){
		$total_amount = 0;
		$sub_total_converted = 0; 
		$discount_total = 0;
		$qty = 0;
		foreach(json_decode($orderdata) as $key => $row ){
			$total_amount = $row->qty * $row->amount;
			$discount_info = $row->discount_info;
			$qty += $row->qty;
			$amount = $row->amount;
			$badge = '';
			if($discount_info != '' && $discount_info != null){
				if(in_array($key,json_decode($discount_info->product_id))){
					$discount_id = $discount_info->id;
					$discount_price = 0;
					if($discount_info->discount_type == 1){
						if($discount_info->disc_amount_type == 2){
							$newprice = $amount - ($amount * ($discount_info->disc_amount/100));
							$discount_price = ($amount * ($discount_info->disc_amount/100));
							if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
								$discount_price = $discount_info->max_discount_price;
								$newprice = $discount_info->max_discount_price;
							}
							$badge =  '<span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
						}else{
							$newprice = $amount - $discount_info->disc_amount;
							$badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->disc_amount.' off</span>'.number_format($newprice,2);
							if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
								$discount_price = $discount_info->max_discount_price;
								$newprice = $discount_info->max_discount_price;
								$badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->max_discount_price.' off</span>'.number_format($newprice,2);
								// $newprice = $discount['max_discount_price'];
							}
						}
						$amount = $newprice;
						$discount_total += $discount_price* floatval($row->qty);
						$sub_total_converted += floatval($newprice) * floatval($row->qty); 
					}
				}
			}else{
				$sub_total_converted += floatval($row->amount) * floatval($row->qty); 
			}
		}
		return Array('subtotal_converted'=>$sub_total_converted,'subtotal_unconverted'=>$total_amount,'total_qty' => $qty);
	}
	public function get_productdetails($Id) {
		$query=" SELECT a.*, d.shopcode,aa.name as parent_name, d.shopname, c.category_name
		FROM sys_products AS a 
		LEFT JOIN sys_products AS aa ON a.parent_product_id = aa.id 
		LEFT JOIN sys_shops AS d ON 1 = d.id 
		LEFT JOIN sys_product_category c on c.id = a.category_id
		WHERE a.Id = ? AND a.enabled > 0;";
		
		$params = array($Id);
		return $this->db->query($query, $params)->row_array();
	}
	public function inventory_report_table($requestData = array(),$exportable = false){
		$_record_status       = $exportable ? $requestData : $this->input->post('_record_status');
		$_search 			  = $exportable ? $requestData['search'] : $this->input->post('search');
		$date_from 		      = $exportable ? format_date_reverse_dash($requestData['date_from']) : format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = $exportable ? format_date_reverse_dash($requestData['date_to']) : format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$_categories		  = $exportable ? $requestData['category'] : $this->input->post('category');
		$token                = en_dec('en', $token_session);
		
		$columns = array(
			// datatable column index  => database column name for sorting
				0 => 'img',
				1 => 'name',
				2 => 'category_name',
				3 => 'qty',
				4 => 'date_manufactured',
				5 => 'date_expiration',
				6 => 'deducted_qty',
				7 => 'total_qty',
				9 => 'status'
			);
		$date_to = date('Y-m-d',strtotime($date_to. ' + 1 days'));
		//print_r($date_to);
		$date_string  =  "date_manufactured >= '".format_date_dash_reverse($date_from)."' AND date_manufactured <= '".format_date_dash_reverse($date_to)."'";
		
		$sql = "SELECT a.*, c.category_name,img.filename,d.qty,d.date_manufactured,d.date_expiration,d.status as stock_status,d.id as inventory_id FROM sys_products a 
		LEFT JOIN sys_product_category c ON a.category_id = c.id
		LEFT JOIN sys_products_images img ON a.id = img.product_id
		LEFT JOIN sys_inventory d on d.product_id = a.id ";

		$sql.=" WHERE ".$date_string;
		$sql.=" AND a.parent_product_id IS NOT NULL";

		if($_categories != ""){
			$sql.=" AND (category_name LIKE '%".$this->db->escape_like_str($_categories)."%')";
		}
		$sql.=' GROUP by d.id';
		$count = 0;
		$query = $this->db->query($sql);
		// print_r($sql);
		$totalData = count($query->result_array());
		$totalFiltered = $totalData; 
		$data = [];
		// print_r($sql);
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$matched = false;
			$margin = '';
			$selected_products = $this->session->userdata('selected_products') == '' ? Array() : $this->session->userdata('selected_products');
			$checked = '';
			if(array_search(en_dec('en',$row["id"]),$selected_products) > -1){
				$checked = 'checked';
			}
			$details = $this->get_productdetails($row["parent_product_id"]);
			if(!$exportable){
				$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.base_url('assets/uploads/products/'.str_replace('==','',$details['img']).'?'.rand()).'">' ;
			}
			// print_r(strpos(strtoupper($details['name'].' - '.$row["name"]),strtoupper($_search)) === 0?1:2);
			// print_r($_search);
			// print_r($details['name'].' - '.$row["name"]);
			$nestedData[] = $details['name'].' - '.$row["name"];
			$stock_status = '';
			if($_search != "" && (strpos(strtoupper($details['name'].' - '.$row["name"]),strtoupper($_search)) === 0 || strpos(strtoupper($details['name'].' - '.$row["name"]),strtoupper($_search)) > 0)){
				$matched = true;
			}else if($_search == ""){
				$matched = true;
			}
			$nestedData[] = $details["category_name"];
			$variant_stocks = 0;
			$variant_price = [];
			$parent_stocks = 0;
			
			foreach($this->get_inventorydetails2('',$row["inventory_id"]) as $inventory){
				if($_search != "" && (strpos(strtoupper($details['name'].' - '.$row["name"]),strtoupper($_search)) === 0 || strpos(strtoupper($details['name'].' - '.$row["name"]),strtoupper($_search)) > 0)){
			
					// if($_search != '' && $row ['id'] == $inventory['product_id']){
					$now = time(); // or your date as well
					$your_date = strtotime($inventory['date_expiration']);
					$datediff = $now - $your_date;
					$days_differ =  -1*(round($datediff / (60 * 60 * 24))-1);
					if($inventory['status']==1){
						$parent_stocks += $inventory['qty'];
					}
						// print_r($inventory);
					if(in_array($inventory['status'],[1,2,3]) && date('Y-m-d',strtotime($inventory['date_expiration'])) <= date('Y-m-d')){
						$stock_status = 'Expired Stocks';
						$row['enabled'] = 2;
					}else if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) > date('Y-m-d') && $days_differ < 30 && $stock_status==''){
						$stock_status = 'Expiring Soon';
					}
					
					// print_r($stock_status);
				}else{
					
					$now = time(); // or your date as well
					$your_date = strtotime($inventory['date_expiration']);
					$datediff = $now - $your_date;
					$days_differ =  -1*(round($datediff / (60 * 60 * 24))-1);
					if($inventory['status']==1){
						$parent_stocks += $inventory['qty'];
					}
						// print_r($inventory);
					if(in_array($inventory['status'],[1,2,3]) && date('Y-m-d',strtotime($inventory['date_expiration'])) <= date('Y-m-d')){
						$stock_status = 'Expired Stocks';
						$row['enabled'] = 2;
					}else if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) > date('Y-m-d') && $days_differ < 30 && $stock_status==''){
						$stock_status = 'Expiring Soon';
					}
				}
				//print_r($days_differ.'//'.$row["id"].'?');
			}
			// foreach($this->getVariants($row["id"]) as $variant){
			// 	foreach($this->get_inventorydetails2($variant["id"]) as $inventory){
			// 		$now = time(); // or your date as well
			// 		$your_date = strtotime($inventory['date_expiration']);
			// 		$datediff = $now - $your_date;
					
			// 		$days_differ =  -1*(round($datediff / (60 * 60 * 24))-1);
			// 		if($inventory['status']==1){
			// 			$variant_stocks += $inventory['qty'];
			// 		}
			// 		if(in_array($inventory['status'],[1,2,3]) && date('Y-m-d',strtotime($inventory['date_expiration'])) <= date('Y-m-d')){
			// 			$stock_status = 'Expired Stocks';
			// 			// $this->disable_modal_confirm($inventory['product_id'],2);
			// 			$row['enabled'] = 2;
			// 		}else if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) > date('Y-m-d') && $days_differ < 30 && $stock_status==''){
			// 			$stock_status = 'Expiring Soon';
			// 		}
			// 	}
			// 	$variant_price [] = $variant['price'];
			// }
			// print_r($stock_status.'x');
			// print_r($parent_stocks.'y');
			// print_r('//');
			// print_r($variant_stocks.'z');
			if($stock_status == '' && $variant_stocks == 0&& $parent_stocks == 0){
				$stock_status = 'Out of Stocks';
			}else if($stock_status == ''){
				$stock_status = 'Active';
			}
			sort($variant_price);
			// $nestedData[] = !empty($variant_price) ? $variant_price[0] .'-'. $variant_price[count($variant_price)-1] : number_format($row["price"], 2);
			$nestedData[] = $variant_stocks > 0 && $row['variant_isset'] == 1 ? $variant_stocks : $parent_stocks;
			
			$nestedData[] = $row["date_manufactured"];
			$nestedData[] = $row["date_expiration"];
			$ordersInventory = $this->getUsedInventoryFromOrders();
			$deductedQty = 0;
			foreach ($ordersInventory as $order){

				foreach(json_decode($order['inventory_data']) as $inv){
					if($inv->id == $row['inventory_id']){
						$deductedQty += floatval($inv->qty);
					}
				}
			}
			$nestedData[] = $deductedQty;
			$nestedData[] = $deductedQty + ($variant_stocks > 0 && $row['variant_isset'] == 1 ? $variant_stocks : $parent_stocks);
			$nestedData[] = $stock_status;
			if($matched == true){
				$data[] = $nestedData;
				$count++;
			}
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $count ),  // total number of records
			"recordsFiltered" => intval( $count ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function getUsedInventoryFromOrders() {
		$query  = "SELECT * FROM sys_orders WHERE inventory_data IS NOT NULL";
		return $this->db->query($query)->result_array();
	}

	public function getVariants($product_id) {
		$query  = "SELECT * FROM sys_products WHERE enabled > 0 AND  parent_product_id = ?";
		$params = array($product_id);
		return $this->db->query($query, $params)->result_array();
	}

    public function sales_report_table($requestData = array(),$exportable = false){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $exportable ? $requestData : $this->input->post('_record_status');
		$_search 			  = $exportable ? $requestData['search'] : $this->input->post('search');
		$status 		      = 5;
		$citymunCode	      = $exportable ? $requestData['city'] : $this->input->post('city');
		$date 		          = $this->input->post('date');
		$date_from 		      = $exportable ? format_date_reverse_dash($requestData['date_from']) : format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = $exportable ? format_date_reverse_dash($requestData['date_to']) : format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));
		$requestData = $exportable ? $requestData:$_REQUEST;
		switch ($date) {
			case "date_fulfilled":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_fulfilled',
                        1 => 'reference_num',
                        2 => 'name',
                        3 => 'conno',
                        4 => 'city',
                        5 => 'discount',
                        6 => 'shipping',
                        7 => 'total_amount',
                        9 => 'order_status'
					);
			break;
			case "date_shipped":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_shipped',
                        1 => 'reference_num',
                        2 => 'name',
                        3 => 'conno',
                        4 => 'city',
                        5 => 'discount',
                        6 => 'shipping',
                        7 => 'total_amount',
                        9 => 'order_status'
					);
				
			break;
			case "date_processed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_order_processed',
                        1 => 'reference_num',
                        2 => 'name',
                        3 => 'conno',
                        4 => 'city',
                        5 => 'discount',
                        6 => 'shipping',
                        7 => 'total_amount',
                        9 => 'order_status'
					);
			break;
			case "date_booking_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_booking_confirmed',
                        1 => 'reference_num',
                        2 => 'name',
                        3 => 'conno',
                        4 => 'city',
                        5 => 'discount',
                        6 => 'shipping',
                        7 => 'total_amount',
                        9 => 'order_status'
					);
			break;
			case "date_confirmed":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'date_confirmed',
                        1 => 'reference_num',
                        2 => 'name',
                        3 => 'conno',
                        4 => 'city',
                        5 => 'discount',
                        6 => 'shipping',
                        7 => 'total_amount',
                        9 => 'order_status'
					);
			break;
			case "date_paid":
				$columns = array(
					// datatable column index  => database column name for sorting
						0 => 'payment_date',
                        1 => 'reference_num',
                        2 => 'name',
                        3 => 'conno',
                        4 => 'city',
                        5 => 'discount',
                        6 => 'shipping',
                        7 => 'total_amount',
                        9 => 'order_status'
					);
			break;
		    default:
			$columns = array(
				// datatable column index  => database column name for sorting
					0 => 'date_created',
					1 => 'reference_num',
					2 => 'name',
					3 => 'conno',
					4 => 'city',
					5 => 'discount',
					6 => 'shipping',
					7 => 'total_amount',
					9 => 'order_status'
				);
			break;
		}

		if($status != ""){
            $date_to = date('Y-m-d',strtotime($date_to. ' + 1 days'));
            //print_r($date_to);
			$date_string  =  "date_created >= '".format_date_dash_reverse($date_from)."' AND date_created <= '".format_date_dash_reverse($date_to)."'";
            $sql = "SELECT * FROM sys_orders WHERE status_id = 5 AND ".$date_string;

			if($_search != ""){
				$sql.=" AND (order_id LIKE '%".$this->db->escape_like_str($_search)."%')";
			}

			// if($status != ""){
            //     $sql.=" AND payment_data  LIKE '%status_id:".'"'.$this->db->escape($status)." %'";
			// }

		}
        // print_r($sql);
        $query = $this->db->query($sql);
		
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        if($exportable == false){
            $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        }
		$query = $this->db->query($sql);

		$data          = array();
        //var_dump( $query->result_array());
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
            $name = json_decode($row['shipping_data'])->full_name;
            $contact_no = json_decode($row['shipping_data'])->contact_no;
            $city = json_decode($row['shipping_data'])->city; 
			$subtotal_converted = number_format($this->get_total_order_amount($row['order_data'])['subtotal_converted'], 2);
			$subtotal_unconverted = number_format($this->get_total_order_amount($row['order_data'])['subtotal_unconverted'], 2);
			$total_qty = number_format($this->get_total_order_amount($row['order_data'])['total_qty'], 2);
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
			// $exportable ? $nestedData[] = $row["date_created"]:'';
			$nestedData[] = $row["order_id"];
			$nestedData[] = str_replace($special_upper, $special_format, $name);
			$nestedData[] = $contact_no;
			$nestedData[] = $city;
            $nestedData[] = $subtotal_unconverted;
            $nestedData[] = number_format(floatval($subtotal_unconverted)-floatval($subtotal_converted),2);
            $nestedData[] = 50;
            $nestedData[] = number_format(floatval($subtotal_unconverted)+50,2);

			$nestedData[] = display_payment_status($payment_status, $payment_method,$exportable);


            $nestedData[] =
            '<div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_menu_button">
                    <a class="dropdown-item"  href="'.base_url('admin/Main_orders/orders_view/'.$token.'/'.$row["order_id"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
					
					<a class="dropdown-item btn_changestatus" target="_blank"href="'.base_url('admin/Main_reports/export_receipt/'.$token.'/'.en_dec('en',$row["order_id"])).'">
					<i class="fa fa-print" aria-hidden="true"></i>Print Receipt
					</a>
                </div>
            </div>';

			if($citymunCode == '' || ($citymunCode != '' && $citymunCode == $city)){
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
}
?>