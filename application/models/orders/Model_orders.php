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
					
					$sql = 'UPDATE sys_inventory SET qty = '.($current_inventory_count - $orderproduct->quantity).' WHERE id = '.$inventory_id;
					if(($current_inventory_count - $orderproduct->quantity) <= 0){
						$response = Array(
							'success' => false,
							'message' => 'Insufficient Stocks, cannot process the order. You need to re-stock to proceed.'
						);
						echo json_encode($response);
					}
					$this->db->query($sql);
				}
				$inventory_data[]=Array('id' => $inventory_id,'qty' => $orderproduct->quantity);
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
				'payment_method_id' => isset($order_info -> payment_method_id)?$order_info -> payment_method_id:3,
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
                $qty = $value->quantity;
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
								$badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->disc_amount.' off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
								if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
									$badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->max_discount_price.' off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
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
                
		$order_amount = 0;
		$discount_total = 0;
		$subtotal = 0;
		foreach(json_decode($orderdata) as $key => $order ){
			// $order_info = json_decode($row["order_data"]);
			//print_r($value);
			$qty = $order->quantity;
			$amount = $order->amount;
			$product = (en_dec('dec',$key));
			$subtotal += $amount*$qty;
			$discount_info = isset($order->discount_info) ? $order->discount_info : Array();
			$discount_price = 0;
			// print_r($discount_info);
			$newprice = $amount;
			$badge = '';
			if($discount_info != '' && $discount_info != null){
				if(in_array($key,json_decode($discount_info->product_id))){
					$discount_id = $discount_info->id;
					if($discount_info->discount_type == 1){
						if($discount_info->disc_amount_type == 2){
							$oldvalue = $newprice;
							$newprice = $amount - ($amount * ($discount_info->disc_amount/100));
							$discount_price = $discount_info->disc_amount;
							if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
								$discount_price = $discount_info->max_discount_price;
								$newprice = $discount_info->max_discount_price;
							}
							$discount_total += $qty*($oldvalue - $newprice);
							$badge =  '<span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
						}else{
							$oldvalue = $newprice;
							$newprice = $amount - $discount_info->disc_amount;
							$discount_total += $qty*($oldvalue - $newprice);
							$discount_price = $discount_info->disc_amount;
							$badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->disc_amount.' off</span> <s><small>'.$amount.'</small></s>';
							if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
								$badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->max_discount_price.' off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
								$newprice = $discount_info->max_discount_price;
								// $newprice = $discount['max_discount_price'];
								$discount_price = $discount_info->max_discount_price;
							}
						}
						$amount = $newprice;
					}
				}
			}
			$order_amount += $newprice * $qty;
		}
		return Array('subtotal_converted'=>$subtotal,'subtotal_unconverted'=>$order_amount,'total_qty' => $qty);
	}
    public function order_table_data(){
		$sql = "SELECT * from sys_orders WHERE status_id = 5";
		return $this->db->query($sql)->result_array();
	}
    public function order_table($requestData = array(),$exportable = false){
		// storing  request (ie, get/post) global array to a variable
		$_record_status       = $exportable ? $requestData : $this->input->post('_record_status_export');
		$_name 			      = $exportable ? $requestData['_name'] : $this->input->post('_name');
		$status 		      = $exportable ? $requestData['status'] : $this->input->post('status');
		$citymunCode	      = $exportable ? $requestData['citymunCode'] : $this->input->post('citymunCode');
		$date 		          = $this->input->post('date');
		// $order_status_view    = $exportable ? $requestData['order_status_view'] : $this->input->post('order_status_view');
		// $forpickup            = $exportable ? $requestData['forpickup'] : $this->input->post('forpickup');
		// $isconfirmed          = $exportable ? $requestData['isconfirmed'] : $this->input->post('isconfirmed');
		// $_shops 		      = $exportable ? $requestData['_shops'] : $this->input->post('_shops');
		$date_from 		      = $exportable ? format_date_reverse_dash($requestData['date_from']) : format_date_reverse_dash($this->input->post('date_from'));
		$date_to 		      = $exportable ? format_date_reverse_dash($requestData['date_to']) : format_date_reverse_dash($this->input->post('date_to'));
		$token_session        = $this->session->userdata('token_session');
		$token                = en_dec('en', $token_session);
		$date_from_2          = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2         	  = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
		$date_from_2_shipping = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00 -2 days')));
		$date_to_2_shipping   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59 +2 days')));
		$order_status = Array(
			
			'pending'=>0,
			'fordelivery'=>0,
			'shipped'=>0,
			'failed'=>0,
			'processing'=>0,
			'delivered'=>0,
			'cancelled'=>0
		);
		$requestData = $exportable ? $requestData:$_REQUEST;
		// print_r($date);	
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
					4 => 'city',
					5 => 'discount',
					6 => 'shipping',
					7 => 'total_amount',
					8 => 'payment_status',
					9 => 'order_status'
				);
			break;
		}

		$date_to = date('Y-m-d',strtotime($date_to. ' + 1 days'));
		//print_r($date_to);
		$date_string  =  "date_created >= '".format_date_dash_reverse($date_from)."' AND date_created <= '".format_date_dash_reverse($date_to)."'";
		$sql = "SELECT * FROM sys_orders WHERE status_id > 0  AND ".$date_string;
		if($_name != ""){
			$sql.=" AND (order_id LIKE '%".$this->db->escape_like_str($_name)."%')";
		}
		// if($status == ""){

		// 	if($status != ""){
        //         $sql.=" AND payment_data  LIKE '%status_id:".'"'.$this->db->escape($status)." %'";
		// 	}
		// }
		$query = $this->db->query($sql);
		
		foreach( $query->result_array() as $row ) {  
			
			if($row['status_id'] == 1){
				$order_status['pending'] ++;
			}
			if($row['status_id'] == 2){
				$order_status['processing'] ++;
			}
			if($row['status_id'] == 3){
				$order_status['fordelivery'] ++;
			}
			if($row['status_id'] == 4){
				$order_status['shipped'] ++;
			}
			if($row['status_id'] == 5){
				$order_status['delivered'] ++;
			}
			if($row['status_id'] == 6|| $row['status_id'] == 7){
				$order_status['failed'] ++;
			}
			if($row['status_id'] == 8 ||$row['status_id'] == 9){
				$order_status['cancelled'] ++;
			}
		}
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		// if($)
		if($status != 10 ){
			$sql.=" AND status_id = ".$status; 
		}
		if($status == 89){
			$sql.=" AND (status_id =  8 OR status_id = 9)"; 
		}
		if($status == 67){
			$sql.=" AND (status_id =  7 OR status_id = 6)"; 
		}
		// print_r($status.'/');
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		
		$query = $this->db->query($sql);

		$data          = array();
		$city_list =  $this->get_cities()->result_array();
        //var_dump( $query->result_array());
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
            $name = json_decode($row['shipping_data'])->full_name;
            $contact_no = json_decode($row['shipping_data'])->contact_no;
			$city = '';
			foreach($city_list as $cit){
				// print_r($cit);
				if($cit['id'] == json_decode($row['shipping_data'])->city){
					$city = $cit['city_name'];
				}
			}
            // $city = $this->get_cities()[json_decode($row['shipping_data'])->city]; 
			$subtotal_converted = number_format($this->get_total_order_amount($row['order_data'])['subtotal_converted'], 2);
			$subtotal_unconverted = number_format($this->get_total_order_amount($row['order_data'])['subtotal_unconverted'], 2);
			$total_qty = number_format($this->get_total_order_amount($row['order_data'])['total_qty'], 2);
            $payment_method = json_decode($row['payment_data'])->payment_method_name;
			// print_r($payment_method);
			// if()
			// print_r($this->orders_details($row["order_id"]));
			$payment_details = $this->orders_details($row["order_id"])[0];
			$rating = 'None';
			if($row['customer_feedback'] != ''){
				$rating = json_decode($row['customer_feedback'])->rating.'/5';
			}
			$payment_status = 0;
			// print_r($payment_details);
			if(($payment_details['payment_details'] != '' && $payment_method !='COD') || $payment_details['status_id'] == 5){
				$payment_status = 1;
			}
            // $payment_status = $payment_details->payment_details != '' ? 1 : json_decode($row['payment_data'])->status_id;
			// if($international == 1){
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
			$exportable ? $nestedData[] = $row["date_created"]:'';
			$nestedData[] = $row["order_id"];
			$nestedData[] = str_replace($special_upper, $special_format, $name);
			// $nestedData[] = $contact_no;
			$nestedData[] = $city;
            $nestedData[] = number_format(floatval(str_replace(',','',$subtotal_converted)),2);
            $nestedData[] = number_format(floatval(str_replace(',','',$subtotal_unconverted))-floatval(str_replace(',','',$subtotal_converted)),2);
            $nestedData[] = 50;
            $nestedData[] = number_format(floatval(str_replace(',','',$subtotal_unconverted))+50,2);
			$nestedData[] = display_payment_status($payment_status, $payment_method,$exportable);
			$nestedData[] = $rating;
			// $nestedData[] = 
			$nestedData[] = display_order_status($row['status_id'],$exportable);
			// $nestedData[] = $row['status_id'];

			// print_r($row['status_id']);
            $nestedData[] =
            '<div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_menu_button">
                    <a class="dropdown-item"  href="'.base_url('admin/Main_orders/orders_view/'.$token.'/'.$row["order_id"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
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
			"data"            => $data,   // total data array
			"order_status"    => $order_status
		);

		return $json_data;
	}
}
?>