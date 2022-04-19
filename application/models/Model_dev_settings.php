
<?php
    class Model_dev_settings extends CI_Model {

    # Start - Content Navigation

    public function main_nav_categories() {
        $sql = "SELECT `main_nav_id`, `main_nav_desc` FROM `cp_main_navigation` WHERE `enabled` >= 1";

        return $this->db->query($sql);
    }

    public function get_shipping_partners() {
        $sql = "SELECT * from sys_shipping_partners WHERE `enabled` >= 1";

        return $this->db->query($sql);
    }
    public function enable_disable_faq($data){
        $sql = "UPDATE sys_faqs SET status = ? WHERE id = ?";
        $bind_data = array(
            $data['status'],
            $data['selectedid']
        );
        return $this->db->query($sql,$bind_data);
    }
    public function delete_faq($data){
        $sql = "UPDATE sys_faqs SET status = 0 WHERE id = ?";
        $bind_data = array(
            $data['selectedid']
        );
        return $this->db->query($sql,$bind_data);
    }
    public function save_faq($data){
        $count = $this->db->query("SELECT * FROM sys_faqs where status > 0")->num_rows();
        if($data['selectedid']==''){
            $sql = "INSERT INTO sys_faqs(title,content,arrangement,status) VALUES(?,?,?,?)";
            $bind_data = array(
                $data['f_title'],
                $data['f_content'],
                $count+1,
                1
            );
        }else{
            $sql = "UPDATE sys_faqs SET title = ?,content = ? WHERE id = ?";
            $bind_data = array(
                $data['f_title'],
                $data['f_content'],
                $data['selectedid']
            );
        }
        return $this->db->query($sql,$bind_data);
    }
    public function get_faq($id){
        $sql = "SELECT * from sys_faqs WHERE `id` = $id";
        return $this->db->query($sql)->result_array();
    }
    public function get_active_faq(){
        $sql = "SELECT * from sys_faqs WHERE `status` = 1 ORDER BY arrangement";
        return $this->db->query($sql)->result_array();
    }
    public function load_faqs($requestData,$exportable = false) {
		// storing  request (ie, get/post) global array to a variable  
		$date_created       = $this->input->post('date_created');
		$_record_status     = $this->input->post('_record_status');
		$_search 			= $this->input->post('_search');
		$_status 			= $this->input->post('_status');
		$token_session      = $this->session->userdata('token_session');
		$token              = en_dec('en', $token_session);

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'id',
			1 => 'title',
            2 => 'content',
            3 => 'arrangement'
		);

		// getting total number records without any search
		$sql = "SELECT * from sys_faqs";
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 
		// start - for default search
		if ($_status == 1) {
			$sql.=" WHERE status = " . $this->db->escape($_status) . "";
		}else if ($_status == 2){
			$sql.=" WHERE status = " . $this->db->escape($_status) . "";
		}else{
			$sql.=" WHERE status > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		
		if($_search != ""){
			$sql.=" AND title LIKE '%" . $this->db->escape_like_str($_search) . "%' OR content LIKE '%" . $this->db->escape_like_str($_search) . "%' ";
		}
		// if($date_created != ""){
		// 	$sql.="  AND DATE_FORMAT(`date_created`, '%m/%d/%Y') ='".$date_created. "'";
		// }
		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY arrangement ";  // adding length
		if (!$exportable) {
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}

		// print_r($sql);
		// exit();
		
		$query = $this->db->query($sql);

		$data = array();

		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
            $nestedData[]='<span class="fa fa-reorder w-100"></span>';
			$nestedData[] = $row["id"];
            // $nestedData[] = (!$exportable) ?'<u><a href="'.base_url('Main_products/view_products/'.$token.'/'.$row['id']).'" style="color:blue;">'.$row["name"].'</a></u>':$row["name"];
			$nestedData[] = $row["title"];
			$nestedData[] = $row["content"];


			
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

            $nestedData[] = $row["arrangement"];
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
			if($this->loginstate->get_access()['web']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<button class="dropdown-item update_faq" data-value="'.$row['id'].'" data-toggle="modal" data-target="#add_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>';
			}

            /// checker of product in product status table
            if($this->loginstate->get_access()['web']['update'] == 1){
                $buttons .= '<div class="dropdown-divider"></div>
                <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#enable_disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
            }
		

			if($this->loginstate->get_access()['web']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}


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
    public function save_arrangement($id,$order){
        $sql = "UPDATE sys_faqs set arrangement = $order WHERE `id` = $id";

        return $this->db->query($sql);
    }
}
?>