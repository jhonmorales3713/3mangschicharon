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

	public function processOrder($reference_num) {

		$sql = "SELECT * from sys_orders WHERE order_id = ?";
		
		$bind_data = array(
			$reference_num
		);
		$inventory_data = Array();
		foreach($this->db->query($sql,$bind_data)->result_array() as $products){
			foreach(json_decode($products['order_data']) as $key => $orderproduct){
				$inventory_id = 0;
				foreach($this->get_inventorydetails(en_dec('dec',$key)) as $inventory_details){
					// print_r($inventory_details);
					$inventory_id = $inventory_details['id'];
					$current_inventory_count = $inventory_details['qty'];
					
					$sql = 'UPDATE sys_inventory SET qty = '.($current_inventory_count - $orderproduct->qty).' WHERE id = '.$inventory_id;
					$this->db->query($sql);
				}
				$inventory_data[]=Array('id' => $inventory_id,'qty' => $orderproduct->qty);
			}
		}
		// die();
		$sql = "UPDATE `sys_orders` SET status_id = 2,inventory_data = ?, `date_processed` = ? WHERE order_id = ?";
		
		$bind_data = array(
			json_encode($inventory_data),
			date('Y-m-d H:i:s'),
			$reference_num
		);
		return $this->db->query($sql, $bind_data);
	}

	public function fulFillOrder($reference_num) {

		$sql = "UPDATE `sys_orders` SET status_id = 4, `date_fulfilled` = ? WHERE order_id = ? ";
		$bind_data = array(
			date('Y-m-d H:i:s'),
			$reference_num
		);
		return $this->db->query($sql, $bind_data);
	}

	public function cancelOrder($reference_num,$reason = '') {

		$sql = "UPDATE `sys_orders` SET status_id = 7, `date_declined` = ?,`reasons` = ? WHERE order_id = ? ";
		$reasons = array(
			'redeliver1' => '',
			'redeliver2' => '',
			'cancel' => '',
			'decline' => $reason
		);
		$bind_data = array(
			date('Y-m-d H:i:s'),
			json_encode($reasons),
			$reference_num
		);
		return $this->db->query($sql, $bind_data);
	}

	public function confirmOrder($reference_num,$order_status,$reason = '') {

		if($order_status == 0 || $order_status == 9){
			$sql = "SELECT * from sys_orders WHERE order_id = ?";
			
			$bind_data = array(
				$reference_num
			);

			$inventory_data = Array();
			foreach($this->db->query($sql,$bind_data)->result_array() as $products){
				foreach(json_decode($products['inventory_data']) as $key => $orderproduct){
					// print_r($orderproduct);
					if(count($this->get_inventorydetails('',$orderproduct->id)) > 0){
						$current_inventory_count = $this->get_inventorydetails('',$orderproduct->id)[0]['qty'];
						$sql = 'UPDATE sys_inventory SET qty = '.($current_inventory_count + $orderproduct->qty).' WHERE id = '.$orderproduct->id;
						$this->db->query($sql);
					}
					// $inventory_data[]=Array('id' => $inventory_id,'qty' => $orderproduct->qty);
				}
			}
		}
		$sql = "UPDATE `sys_orders` SET status_id = ?";
		$bind_data = array(
			$order_status
		);
		if($order_status == 8){
			$sql .=", date_deliveryfailed1 = ? ";
			array_push($bind_data, date('Y-m-d H:i:s'));
			$order_info = $this->orders_details($reference_num)[0]['reasons'];
			$reasons = array(
				'redeliver1' => $reason
			);
			$sql .=", reasons = ? ";
			array_push($bind_data, json_encode($reasons));
		}else
		if($order_status == 0){
			$sql .=", date_declined = ? ";
			array_push($bind_data, date('Y-m-d H:i:s'));
			$order_info = $this->orders_details($reference_num)[0]['reasons'];
			$order_info = json_decode($order_info);
			$reasons = array(
				'redeliver1' => $order_info->redeliver1,
				'redeliver2' => $order_info->redeliver2,
				'cancel' => $reason
			);
			$sql .=", reasons = ? ";
			array_push($bind_data, json_encode($reasons));
		}else
		if($order_status == 9){
			$order_info_ = $this->orders_details($reference_num);
			$order_info = json_decode($order_info_[0]['reasons']);
			//setting to failed if exceeded delivery count
			if($order_info_[0]['date_deliveryfailed2']!=''){
				$sql = "UPDATE `sys_orders` SET status_id = ?,date_declined = ?";
				$bind_data2 = array(
					0,date('Y-m-d H:i:s')
				);
				$bind_data = $bind_data2;
			}else{
				$sql .=", date_deliveryfailed2 = ? ";
				array_push($bind_data, date('Y-m-d H:i:s'));
			}
			
			if($order_info_[0]['date_deliveryfailed2']!=''){
				$reasons = array(
					'redeliver1' => $order_info -> redeliver1,
					'redeliver2' => $order_info -> redeliver2,
					'cancel' => $reason,
				);
			}else{
				$reasons = array(
					'redeliver1' => $order_info -> redeliver1,
					'redeliver2' => $reason
				);
			}
			$sql .=", reasons = ? ";
			array_push($bind_data, json_encode($reasons));
		}else{
			$sql .=", date_delivered = ? ";
			array_push($bind_data, date('Y-m-d H:i:s'));
			$order_info = $this->orders_details($reference_num)[0]['payment_data'];
			$order_info = json_decode($order_info);
			$new_shipping_data = array(
				'payment_method_id' => $order_info -> payment_method_id,
				'payment_method_name' => $order_info -> payment_method_name,
				'amount' => $order_info -> amount,
				'status_id' => $order_info -> status_id,
				'paid_date' => date('Y-m-d H:i:s')
			);
			//print_r(json_encode($new_shipping_data));
			
			$sql .=", payment_data = ? ";
			array_push($bind_data, json_encode($new_shipping_data));
		}
        
        
        $sql .=" WHERE order_id = ? ";
        array_push($bind_data, $reference_num);
		return $this->db->query($sql, $bind_data);
	}
	public function getImageByFileName($id='') {
		$query="SELECT * FROM sys_orders_images join sys_orders on sys_orders.order_id = sys_orders_images.reference_num ";
		if($id != ''){
			$query.=' WHERE sys_orders_images.reference_num = "'.$id.'"';
		}
		//print_r($filename);
		// }else if($update_isset){
		//  	$query.='WHERE filename = ? and status = 1';
		// }else{
		// 	$query.='WHERE filename = ? ';
		// }
		//params = array($filename);
		// print_r($query);
		// print_r($filename);
		// print_r($id);
		// die();
		return $this->db->query($query);
	}
	public function getshippingpartners($id = '') {
        $sql = "SELECT * from sys_shipping_partners";
        if($id!=''){
            $sql.=" WHERE id =".$id;
        }
		return $this->db->query($sql)->result_array();
    }
	public function readyfordeliveryOrder($reference_num,$imgArr,$courier_info) {

		$sql = "UPDATE `sys_orders` SET status_id = 3, `date_readyforpickup` = ?";
		$bind_data = array(
			date('Y-m-d H:i:s')
		);

        if($courier_info['reference_num'] != ''){
            $order_info = $this->orders_details($reference_num)[0]['shipping_data'];
            $order_info = json_decode($order_info);
            $new_shipping_data = array(
                'address_category_id' => $order_info -> address_category_id,
                'full_name' => $order_info -> full_name,
                'contact_no' => $order_info -> contact_no,
                'province' => $order_info -> province,
                'city' => $order_info -> city,
                'barangay' => $order_info -> barangay,
                'zip_code' => $order_info -> zip_code,
                'address' => $order_info -> address,
                'notes' => $order_info -> notes,
                'rider' => $courier_info
            );
            //print_r(json_encode($new_shipping_data));
            
            $sql .=", shipping_data = ? ";
            array_push($bind_data, json_encode($new_shipping_data));
            
            //array_push($array, "item", "another item");
        }
        
        $sql .=" WHERE order_id = ? ";
        array_push($bind_data, $reference_num);

        foreach($imgArr as $key => $value){
            if($value != ""){
                $sql2 = "INSERT INTO sys_orders_images (`reference_num`,`filename`,`date_created`, `status`) VALUES (?,?,?,?) ";
                $bind_data2 = array(
                    $reference_num,
                    $value,
                    date('Y-m-d H:i:s'),
                    1
                );

                $this->db->query($sql2, $bind_data2);
            }
        }
		return $this->db->query($sql, $bind_data);
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
        
        $sql = 'SELECT * FROM sys_orders WHERE order_id = "'.$reference_num.'"';


        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $product_id = json_decode($row["product_id"]);
            $order_info = json_decode($row["order_data"]);
            foreach($order_info  as $key => $value){
               //print_r($value);
                $qty = $value->qty;
                $amount = $value->amount;
				$product = (en_dec('dec',$key));
				$discount_info = $value->discount_info;
				// print_r($discount_info);
				$badge = '';
				if($discount_info != '' && $discount_info != null){
					if(in_array($key,json_decode($discount_info->product_id))){
						$discount_id = $discount_info->id;
						if($discount_info->discount_type == 1){
							if($discount_info->disc_amount_type == 2){
								$newprice = $amount - ($amount * ($discount_info->disc_amount/100));
								$discount_price = $discount_info->disc_amount;
								if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
									$discount_price = $discount_info->max_discount_price;
									$newprice = $discount_info->max_discount_price;
								}
								$badge =  '<span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
							}else{
								$newprice = $amount - $discount_info->disc_amount;
								$badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->disc_amount.' off</span>'.number_format($newprice,2);
								if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
									$badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->max_discount_price.' off</span>'.number_format($newprice,2);
									$newprice = $discount_info->max_discount_price;
									// $newprice = $discount['max_discount_price'];
								}
							}
							$amount = $newprice;
						}
					}
				}
				
				// print_r($value);

				// print_r(en_dec('dec',$key));
				// print_r($value);
				// print_r($product);
				$parent_product_info = $this->get_productinfo_parent($product);
				$parent_product_info_ = $this->get_productinfo_parent($parent_product_info[0]['parent_product_id']);
				$size_info = $parent_product_info[0];
				
				// print_r($product_info);
				// $product_info_parent = $parent_product_info['parent_product_id']!=''?$this->get_productinfo($this->get_productinfo($product)['parent_product_id'])['name'].' - ':'';
				// print_r($size_info);
				// print_r($parent_product_info);
				$nestedData=array();
				$nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.base_url().'assets/uploads/products/'.str_replace('==.','.',$size_info['img'] == '' ?$parent_product_info_[0]['img']:$size_info['img'] ).'?'.rand().'">';
				$nestedData[] = ucwords($parent_product_info[0]['parent_product_id'] != '' ? 
					$this->get_productinfo($parent_product_info[0]['parent_product_id'])[0]['name'].' - '.$size_info["name"]:
					$size_info["name"]);
				$nestedData[] = $qty;
	
				$nestedData[] = $badge!=''?$badge:number_format($amount, 2);
				$nestedData[] = number_format($amount * $qty, 2);
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
	public function get_total_order_amount($orderdata){
		$total_amount = 0;
		$sub_total_converted = 0; 
		$discount_total = 0;
		foreach(json_decode($orderdata) as $key => $row ){
			
			$discount_info = $row->discount_info;
			$amount = $row->amount;
			$badge = '';
			if($discount_info != '' && $discount_info != null){
				if(in_array($key,json_decode($discount_info->product_id))){
					$discount_id = $discount_info->id;
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
		return($sub_total_converted);
	}
    public function order_table_data(){
		$sql = "SELECT * from sys_orders WHERE status_id = 5";
		return $this->db->query($sql)->result_array();
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
            $date_to = date('Y-m-d',strtotime($date_to. ' + 1 days'));
            //print_r($date_to);
			$date_string  = ($_name != "") ? "" : "date_created >= '".format_date_dash_reverse($date_from)."' AND date_created <= '".format_date_dash_reverse($date_to)."'";
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
			$subtotal_converted = number_format($this->get_total_order_amount($row['order_data']), 2);
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
			$nestedData[] = $row["order_id"];
			$nestedData[] = str_replace($special_upper, $special_format, $name);
			$nestedData[] = $contact_no;
            $nestedData[] = $subtotal_converted;

			$nestedData[] = display_payment_status($payment_status, $payment_method);
			$nestedData[] = display_order_status($row['status_id']);


            $nestedData[] =
            '<div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_menu_button">
                    <a class="dropdown-item"  href="'.base_url('admin/Main_orders/orders_view/'.$token.'/'.$row["order_id"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
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