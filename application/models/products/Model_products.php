<?php 
class Model_products extends CI_Model {

	# Start - Products

    public function get_sys_shop($user_id){
		$sql=" SELECT sys_shop FROM app_members WHERE sys_user = ? AND status = 1";
		$sql = $this->db->query($sql, $user_id); 

        if($sql->num_rows() > 0){
            return $sql->row()->sys_shop;
        }else{
            return "";
        }
	}
    public function product_table($sys_shop, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$date_from      = $this->input->post('date_from');
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_categories 	= $this->input->post('_categories');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'itemname',
            1 => 'itemname',
            2 => 'category_name',
            3 => 'price',
            4 => 'no_of_stocks',
            5 => 'shopname',
            6 => 'enabled',
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
                LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				WHERE a.enabled > 0 AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		if (!$exportable) {
			$sql = "SELECT a.*, code.shopcode, c.category_name, b.shopname, no_of_stocks FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id";
				// LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1";
		}
		else{
			$sql = "SELECT a.*, code.shopcode, c.category_name, b.shopname, no_of_stocks FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id";
		}
		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		
		if($_name != ""){
			$sql.=" AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_categories != ""){
			$sql.=" AND c.id = " . $this->db->escape($_categories) . "";
		}

		if($date_from != ""){
			$sql.="  AND DATE_FORMAT(a.`date_created`, '%m/%d/%Y') ='".$date_from. "'";
		}


		if($sys_shop != 0){
			$sql.=" AND b.id = " . $this->db->escape($sys_shop) . "";
		}

		$sql.=" AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";  // adding length
		if (!$exportable) {
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}

		// print_r($sql);
		// exit();
		$query = $this->db->query($sql);

		$data = array();
		$get_s3_imgpath_upload = get_s3_imgpath_upload();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = (!$exportable) ? '<img class="img-thumbnail" style="width: 50px;" src="'.$get_s3_imgpath_upload.'assets/img/'.$row['shopcode'].'/products-250/'.$row['Id'].'/'.removeFileExtension($row['img_1']).'.jpg?'.rand().'">' : '';;
			// $nestedData[] = (!$exportable) ?'<u><a href="'.base_url('Main_products/view_products/'.$token.'/'.$row['Id']).'" style="color:blue;">'.$row["itemname"].'</a></u>':$row["itemname"];
			$nestedData[] = $row["itemname"];
			$nestedData[] = $row["category_name"];
			$nestedData[] = number_format($row["price"], 2);


			
			// $inv_qty_branch = $this->get_uptodate_nostocks($row['sys_shop'], $row['Id']);
			// $total_inv_qty  = 0;

			// if($inv_qty_branch != false){
			// 	foreach($inv_qty_branch as $val){
			// 		$total_inv_qty += $val['total_inv_qty'];
			// 	}
			// }
			// $total_inv_qty_main = ($this->get_uptodate_nostocks_main($row['Id']) != false) ? $this->get_uptodate_nostocks_main($row['Id'])->total_inv_qty:0;
			// $grand_total_qty = ($total_inv_qty+$total_inv_qty_main == 0) ? $row['no_of_stocks'] : $total_inv_qty+$total_inv_qty_main;
			// $no_of_stocks = ($branchid != 0) ? (!empty($this->get_invqty_branch($branchid, $row['Id'])['no_of_stocks']) ? $this->get_invqty_branch($branchid, $row['Id'])['no_of_stocks'] : 0) : $grand_total_qty;
			// $no_of_stocks = ($branchid != 0 && $no_of_stocks == 0) ? $this->getParentProductInvBranch($row['Id'], $branchid) : $no_of_stocks;
			// $nestedData[] = number_format($no_of_stocks, 1);

			$nestedData[] = $row["no_of_stocks"];
            $nestedData[] = $row["shopname"];



			if($row["enabled"]==1){
                $nestedData[] = 'Enabled';
            }else{
                $nestedData[] = 'Disabled';
            }

			if ($row['enabled'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['enabled'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			$buttons = "";
			$buttons .= '
				<a class="dropdown-item" data-value="'.$row['Id'].'" href="'.base_url('Main_products/view_products/'.$token.'/'.$row['Id']).'"><i class="fa fa-search" aria-hidden="true"></i> View</a>';

			if($this->loginstate->get_access()['products']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item" data-value="'.$row['Id'].'" href="'.base_url('Main_products/update_products/'.$token.'/'.$row['Id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
			}

            /// checker of product in product status table
			if(ini() == 'toktokmall'){


				$sql = "SELECT * FROM sys_product_status WHERE product_id = '".$row['Id']."' AND status != '1'";
				$check_status_product = $this->db->query($sql);		
	
				if($check_status_product->num_rows() == 0){
	
						if($this->loginstate->get_access()['products']['disable'] == 1){
							$buttons .= '<div class="dropdown-divider"></div>
							<a class="dropdown-item action_disable" data-value="'.$row['Id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
						}
		
				}else{
	
				}

			}else{

				if($this->loginstate->get_access()['products']['disable'] == 1){
					$buttons .= '<div class="dropdown-divider"></div>
					<a class="dropdown-item action_disable" data-value="'.$row['Id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
				}

			}
		

			if($this->loginstate->get_access()['products']['delete'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_delete " data-value="'.$row['Id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}


			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
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
    public function get_shopcode($user_id){
		$sql=" SELECT b.shopcode as shopcode FROM app_members a 
			    LEFT JOIN sys_shops b ON a.sys_shop = b.id
				WHERE a.sys_user = ? AND a.status = 1";
		$sql = $this->db->query($sql, $user_id); 

        if($sql->num_rows() > 0){
            return $sql->row()->shopcode;
        }else{
            return "";
        }
    }
    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

    public function get_category_options() {
		$query="SELECT * FROM sys_product_category WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

}