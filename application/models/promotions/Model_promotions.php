<?php 
class Model_promotions extends CI_Model {

	# Start - Promotions

	public function save_discount($data,$products) {
        $sql = "INSERT INTO sys_products_discount ( `discount_type`, `product_id`, `start_date`, `end_date`, `disc_amount_type`, `disc_amount`, `max_discount_isset`, `max_discount_price` , `usage_quantity` , `date_created`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
		
		$date_created = date('Y-m-d H:i:s');
		$bind_data = array(
            1,
			json_encode($products),
			date('Y-m-d H:i:s',strtotime($data['date_from'])),
			date('Y-m-d H:i:s',strtotime($data['date_to'])),
			$data['disc_ammount_type'],
			$data['disc_ammount'],
			$data['set_max_amount'] != '' ? 1 : 0,
			$data['set_max_amount'] != '' ? $data['disc_ammount_limit'] : 0,
			isset($data['usage_quantity'])?$data['usage_quantity']:0,
            $date_created,
			1
		);
		$result = $this->db->query($sql, $bind_data);
    }
    public function get_ongoing(){
		$start_date = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM sys_products_discount WHERE start_date <= '".$start_date."' AND end_date >= '".$start_date."' AND status = 1";
		$result = $this->db->query($sql);
        return $result->result_array();
    }
	public function update_discount($data,$products,$id) {
        $sql = "UPDATE sys_products_discount SET `product_id` = ?, `start_date` = ?, `end_date` = ?, `disc_amount_type` = ?, `disc_amount` = ?, `max_discount_isset` = ?, `max_discount_price` = ? , `usage_quantity`  = ?, `date_updated` = ? WHERE id = ?";
		
		$date_created = date('Y-m-d H:i:s');
		$bind_data = array(
			json_encode($products),
			date('Y-m-d H:i:s',strtotime($data['date_from'])),
			date('Y-m-d H:i:s',strtotime($data['date_to'])),
			$data['disc_ammount_type'],
			$data['disc_ammount'],
			isset($data['set_max_amount']) ? 1 : 0,
			isset($data['set_max_amount']) ? $data['disc_ammount_limit'] : 0,
			isset($data['usage_quantity'])?$data['usage_quantity']:0,
            $date_created,
            en_dec('dec',$id)
		);
		$result = $this->db->query($sql, $bind_data);
    }

    public function checkProductActiveDiscount($products,$date_from,$date_to,$action = 'add'){
        $ongoing_promo = Array();
        $filter = " AND status = 1";
        $result = array();
        foreach($products as $product){
            //check products included if has ongoing 
            $sql1  = "SELECT * from sys_products_discount WHERE start_date >= '".date('Y-m-d H:i:s',strtotime($date_from))."' AND end_date >= '".date('Y-m-d H:i:s',strtotime($date_to))."'".$filter;
            $result1 = $this->db->query($sql1)->result_array();
            $idforupdate = 0;
            foreach($result1 as $res){
                if(in_array($product,json_decode($res['product_id']))){
                    $idforupdate = $res['id'];
                }
            }
            
            $sql  = "SELECT * from sys_products_discount WHERE start_date >= '".date('Y-m-d H:i:s',strtotime($date_from))."' AND end_date >= '".date('Y-m-d H:i:s',strtotime($date_to))."'".$filter;
            $result = $this->db->query($sql)->result_array();
            foreach($result as $res){
                foreach($products as $product){
                    if(in_array($product,json_decode($res['product_id'])) && !in_array(en_dec('dec',$product),$ongoing_promo) && ($action =='add'||($action == 'update' && $idforupdate != $res['id']))){
                        $ongoing_promo[] = en_dec('dec',$product);
                    }
                }
            }
        }
        if(count($ongoing_promo) > 0){
            $result = Array(
                'success' => false,
                'message' => 'You have a product with existing discount within the specified date range, remove it first.',
                'products' =>$ongoing_promo
            );
            echo json_encode($result);
            die();
        }
    }

    public function change_status($id,$status,$date_from,$date_to){
        if($status == 1){
            $sql ="SELECT * from sys_products_discount WHERE id = ".$id;
            
            $result = $this->db->query($sql)->result_array()[0]['product_id'];
            $this->checkProductActiveDiscount(json_decode($result),$date_from,$date_to,false);
        }
        $sql = "UPDATE sys_products_discount SET `date_updated` = ?, `status` = ? WHERE id = ?";
		
		$date_created = date('Y-m-d H:i:s');
		$bind_data = array(
            $date_created,
			$status,
            $id
		);
		$result = $this->db->query($sql, $bind_data);
    }
    public function get_promotion($id){
        $sql ="SELECT * from sys_products_discount WHERE id = ".$id;
        
		$result = $this->db->query($sql);
        return $result->row_array();
    }
    public function discount_table($sys_shop, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$date_from      = $this->input->post('date_from');
		$date_to      = $this->input->post('date_to');
		$_record_status = $this->input->post('_record_status');
		$_categories 	= $this->input->post('_categories');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		// $branchid       = $this->session->userdata('branchid');

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'start_date',
            1 => 'end_date',
            2 => 'disc_amount',
            // 3 => 'usage_quantity',
            4 => 'status',
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_products_discount where status > 0";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		if (!$exportable) {
			$sql = "SELECT * FROM sys_products_discount WHERE status > 0 ";
				// LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1";
		}
		else{
			$sql = "SELECT * FROM sys_products_discount WHERE status > 0 ";
		}
		// start - for default search
		if ($_record_status > 0) {
			$sql.="AND  status = " . $this->db->escape($_record_status) . "";
		}
		if ($date_from != '') {
			$sql.="AND start_date >= " . $this->db->escape($date_from) . "";
		}
		if ($date_to != '') {
			$sql.="AND end_date <= " . $this->db->escape(date('Y-m-d', strtotime($date_to. ' + 1 days'))) . "";
		}
		// end - for default search

		// getting records as per search parameters
		
		// if($_name != ""){
		// 	$sql.=" AND a.name LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		// }
		// if($_categories != ""){
		// 	$sql.=" AND c.id = " . $this->db->escape($_categories) . "";
		// }

		// if($date_from != ""){
		// 	$sql.="  AND DATE_FORMAT(a.`date_created`, '%m/%d/%Y') ='".$date_from. "'";
		// }
		// $sql.=" AND a.parent_product_id IS NULL GROUP BY a.id";
		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";  // adding length
		if (!$exportable) {
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}

		// print_r($sql);
		// exit();
		
		$query = $this->db->query($sql);

        // print_r($query);
        // print_r($sql);
		$data = array();
		$get_s3_imgpath_upload = get_s3_imgpath_upload();
        setlocale(LC_MONETARY,"en_US");
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			// $nestedData[] = (!$exportable) ? '<img class="img-thumbnail" style="width: 50px;" src="'.base_url('assets/uploads/products/'.str_replace('==','',$row['img']).'?'.rand()).'">' : '';;
			// $nestedData[] = (!$exportable) ?'<u><a href="'.base_url('Main_products/view_products/'.$token.'/'.$row['id']).'" style="color:blue;">'.$row["name"].'</a></u>':$row["name"];
			$nestedData[] = $row["start_date"];
			$nestedData[] = $row["end_date"];
			$nestedData[] = $row["disc_amount_type"] == 1 ? 'P'.number_format($row["disc_amount"]) : $row["disc_amount"].'%';
			// $nestedData[] = $row["usage_quantity"];


			
			// $inv_qty_branch = $this->get_uptodate_nostocks($row['sys_shop'], $row['id']);
			// $total_inv_qty  = 0;

			// if($inv_qty_branch != false){
			// 	foreach($inv_qty_branch as $val){
			// 		$total_inv_qty += $val['total_inv_qty'];
			// 	}
			// }
			// $total_inv_qty_main = ($this->get_uptodate_nostocks_main($row['id']) != false) ? $this->get_uptodate_nostocks_main($row['id'])->total_inv_qty:0;
			// $grand_total_qty = ($total_inv_qty+$total_inv_qty_main == 0) ? $row['no_of_stocks'] : $total_inv_qty+$total_inv_qty_main;
			// $no_of_stocks = ($branchid != 0) ? (!empty($this->get_invqty_branch($branchid, $row['id'])['no_of_stocks']) ? $this->get_invqty_branch($branchid, $row['id'])['no_of_stocks'] : 0) : $grand_total_qty;
			// $no_of_stocks = ($branchid != 0 && $no_of_stocks == 0) ? $this->getParentProductInvBranch($row['id'], $branchid) : $no_of_stocks;
			// $nestedData[] = number_format($no_of_stocks, 1);
			// $variant_stocks = 0;
			// $variant_price = [];
			// $parent_stocks = 0;
			// $stock_status = '';
			//print_r($row['id'].'..');
			// foreach($this->get_inventorydetails($row["id"]) as $inventory){
			// 	$now = time(); // or your date as well
			// 	$your_date = strtotime($inventory['date_expiration']);
			// 	$datediff = $now - $your_date;
			// 	$days_differ =  -1*(round($datediff / (60 * 60 * 24))-1);
			// 	if($inventory['status']==1){
			// 		$parent_stocks += $inventory['qty'];
			// 	}
			// 	if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) <= date('Y-m-d')){
			// 		$stock_status = 'Expired Stocks';
			// 		$this->disable_modal_confirm($inventory['product_id'],2);
			// 		$row['enabled'] = 2;
			// 	}else if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) > date('Y-m-d') && $days_differ < 30 && $stock_status==''){
			// 		$stock_status = 'Expiring Soon';
			// 	}
			// 	//print_r($days_differ.'//'.$row["id"].'?');
			// }
			// foreach($this->getVariants($row["id"]) as $variant){
			// 	foreach($this->get_inventorydetails($variant["id"]) as $inventory){
			// 		$now = time(); // or your date as well
			// 		$your_date = strtotime($inventory['date_expiration']);
			// 		$datediff = $now - $your_date;
					
			// 		$days_differ =  -1*(round($datediff / (60 * 60 * 24))-1);
			// 		if($inventory['status']==1){
			// 			$variant_stocks += $inventory['qty'];
			// 		}
			// 		if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) <= date('Y-m-d')){
			// 			$stock_status = 'Expired Stocks';
			// 			$this->disable_modal_confirm($inventory['product_id'],2);
			// 			$row['enabled'] = 2;
			// 		}else if(in_array($inventory['status'],[1,2]) && date('Y-m-d',strtotime($inventory['date_expiration'])) > date('Y-m-d') && $days_differ < 30 && $stock_status==''){
			// 			$stock_status = 'Expiring Soon';
			// 		}
			// 	}
			// 	$variant_price [] = $variant['price'];
			// }
			// if($stock_status == '' && $variant_stocks == 0){
			// 	$stock_status = 'Out of Stocks';
			// }
			// sort($variant_price);
			// $nestedData[] = !empty($variant_price) ? $variant_price[0] .'-'. $variant_price[count($variant_price)-1] : number_format($row["price"], 2);
			// $nestedData[] = $variant_stocks > 0 && $row['variant_isset'] == 1 ? $variant_stocks : $parent_stocks;



			if($row["status"]==1){
                $nestedData[] = 'Enabled';
            }else{
                $nestedData[] = 'Disabled';
            }

			if ($row['status'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['status'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			$buttons = "";
			$buttons .= '
				<a class="dropdown-item" data-value="'.$row['id'].'" href="'.base_url('admin/Main_products/view_products/'.$token.'/'.en_dec('en',$row['id'])).'"><i class="fa fa-search" aria-hidden="true"></i> View</a>';

			if($this->loginstate->get_access()['product_discount']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item " data-value="'.$row['id'].'" href="'.base_url('admin/Main_promotions/update_discount/'.$token.'/'.en_dec('en',$row['id'])).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
			}


			if($this->loginstate->get_access()['product_discount']['disable'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item '.($row['status'] == 1 ?'action_disable' :'action_enable').'" data-value="'.$row['id'].'"  data-date_from="'.$row['start_date'].'"  data-date_to="'.$row['end_date'].'" data-record_status="'.$row['status'].'"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
			}
		

			if($this->loginstate->get_access()['product_discount']['delete'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_delete " data-value="'.$row['id'].'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}


			// $nestedData[] = $stock_status;
			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_menu_button">
			    '.$buttons.'
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