<?php 
class Model_user_list extends CI_Model {

	public function check_username($username, $id = 0)
	{
		$query="SELECT * FROM sys_users WHERE username = ? AND id != ? AND active = '1' ";
		$args = array(
			$username,
			$id
		);
		$result = $this->db->query($query,$args);
		if($result->num_rows()>0){
			return false;
		}else{
			return true;
		}
    }

	public function getImageByFileName($email) {
		$query="SELECT * FROM sys_users WHERE username= '".$email."'";
		
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
    
	public function create_user($status,$password,$username,$avatar,$functions, $args = '', $muserlist = '')
	{
		// for cp_main_navigation purposes
		$sql = "SELECT `main_nav_id`, `main_nav_href` FROM `cp_main_navigation` WHERE `enabled` > 0";
		$queries = $this->db->query($sql);

		$main_nav_ac_products_view = $this->input->post('ac_products_view');
		$main_nav_products = ($main_nav_ac_products_view) ? 1 : 0;

        
		$main_nav_ac_settings_view_aul = $this->input->post('settings_aul_view');
		$main_nav_settings = ($main_nav_ac_settings_view_aul) ? 1 : 0;
		

		$main_nav_ac_orders_view = $this->input->post('ac_transactions_view');
		$main_nav_orders = ($main_nav_ac_orders_view) ? 1 : 0;

		$main_nav_profile = 1;


		$array_main_nav_id = [];

		if ($queries->num_rows() > 0) {
			foreach ($queries->result() as $row) {


				if ($main_nav_orders == 1) {
					$main_nav_href_string = 'orders_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}

				if ($main_nav_profile == 1) {
					$main_nav_href_string = 'profile_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}


				if ($main_nav_products == 1) {
					$main_nav_href_string = 'products_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}


				if ($main_nav_settings == 1) {
					$main_nav_href_string = 'settings_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}


			}
		}

		$list_main_nav_id = implode(', ', $array_main_nav_id); 

		// end for cp_main_navigation purposes

		// for cp_content_navigation purposes
		$sql = "SELECT `id`, `cn_name` FROM `cp_content_navigation` WHERE `status` = 1";
		$queries = $this->db->query($sql);


		$ac_products_view = $this->input->post('ac_products_view');
		$ac_products_view = ($ac_products_view) ? 1 : 0;
        
		$ac_settings_aul_view = $this->input->post('settings_aul_view');
		$ac_settings_aul_view = ($ac_settings_aul_view) ? 1 : 0;
        
		$ac_orders_view = $this->input->post('ac_transactions_view');
		$ac_orders_view = ($ac_orders_view) ? 1 : 0;
		
		$ac_profile_view = 1;

		$array_content_nav_id = [];

		if ($queries->num_rows() > 0) {
			foreach ($queries->result() as $row) {

				if ($ac_products_view == 1) {
					$content_nav_href_string = 'Products'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
				}
                
                if($ac_settings_aul_view == 1){
					$content_nav_href_string = 'User List'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }

                if($ac_orders_view == 1){
					$content_nav_href_string = 'Order List'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }

                if($ac_profile_view == 1){
					$content_nav_href_string = 'Profile Update'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }

			}
		}
		$list_content_nav_id = implode(', ', $array_content_nav_id);
		
        // var_dump($list_main_nav_id);
        // var_dump($functions);
        // die();
		// end for cp_content_navigation purposes

		$argument = array(
			$status,
			password_hash($password,PASSWORD_BCRYPT,array('cost' => 12)),
			$username,
			$avatar,
			$functions,
			$list_main_nav_id,
			$list_content_nav_id,
			0,
			0,
			0,
			1
		);
		$query="INSERT INTO sys_users (active, password, username, avatar, functions, access_nav, access_content_nav, failed_login_attempts, first_login, code_isset, login_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		 $query = $this->db->query($query,$argument);

		 return $query;
	}

	public function update_user($username, $password, $avatar, $functions, $id, $args = '', $muserlist = '')
	{
		// for cp_main_navigation purposes
		$sql = "SELECT `main_nav_id`, `main_nav_href` FROM `cp_main_navigation` WHERE `enabled` > 0";
		$queries = $this->db->query($sql);

		$main_nav_ac_products_view = $this->input->post('main_nav_ac_products_view');
		$main_nav_products = ($main_nav_ac_products_view) ? 1 : 0;

        
		$main_nav_ac_settings_view_aul = $this->input->post('settings_aul_view');
		$main_nav_settings = ($main_nav_ac_settings_view_aul) ? 1 : 0;

		$main_nav_ac_orders_view = $this->input->post('ac_transactions_view');
		$main_nav_orders = ($main_nav_ac_orders_view) ? 1 : 0;

		$main_nav_profile = 1;

		$array_main_nav_id = [];

		if ($queries->num_rows() > 0) {
			foreach ($queries->result() as $row) {

				if ($main_nav_orders == 1) {
					$main_nav_href_string = 'orders_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}

				if ($main_nav_products == 1) {
					$main_nav_href_string = 'products_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}
				
				if ($main_nav_settings == 1) {
					$main_nav_href_string = 'settings_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}

				if ($main_nav_profile == 1) {
					$main_nav_href_string = 'profile_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}

			}
		}

		$list_main_nav_id = implode(', ', $array_main_nav_id); 

		// end for cp_main_navigation purposes

		// for cp_content_navigation purposes
		$sql = "SELECT `id`, `cn_name` FROM `cp_content_navigation` WHERE `status` = 1";
		$queries = $this->db->query($sql);

		$ac_products_view = $this->input->post('ac_products_view');
		$ac_products_view = ($ac_products_view) ? 1 : 0;
        
		$ac_settings_aul_view = $this->input->post('settings_aul_view');
		$ac_settings_aul_view = ($ac_settings_aul_view) ? 1 : 0;

		$ac_settings_web_view = $this->input->post('settings_web_view');
		$ac_settings_web_view = ($ac_settings_web_view) ? 1 : 0;
		
		$ac_orders_view = $this->input->post('ac_transactions_view');
		$ac_orders_view = ($ac_orders_view) ? 1 : 0;

		$ac_profile_view = 1;

		$array_content_nav_id = [];

		if ($queries->num_rows() > 0) {
			foreach ($queries->result() as $row) {

				if ($ac_products_view == 1) {
					$content_nav_href_string = 'Products'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
				}
                
                if($ac_settings_aul_view == 1){
					$content_nav_href_string = 'User List'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }
                
                if($ac_settings_web_view == 1){
					$content_nav_href_string = 'Website Information'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }
				
                if($ac_orders_view == 1){
					$content_nav_href_string = 'Order List'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }

                if($ac_profile_view == 1){
					$content_nav_href_string = 'Profile Update'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }
			}
		}

		$list_content_nav_id = implode(', ', $array_content_nav_id);
		
		// end for cp_content_navigation purposes

		$argument = array(
			$username,
			$avatar,
			$functions,
			$list_main_nav_id,
			$list_content_nav_id
		);
		$query="UPDATE sys_users SET username = ?, avatar = ?, functions = ?, access_nav = ?, access_content_nav = ? WHERE id = ?";

		if (!empty($password)) {
			$argument[] = password_hash($password,PASSWORD_BCRYPT,array('cost' => 12));
			$query="UPDATE sys_users SET username = ?, avatar = ?, functions = ?, access_nav = ?, access_content_nav = ?, password = ? WHERE id = ?";
		}
		
		$argument[] = $id;

		$query = $this->db->query($query,$argument);

		// if($muserlist == 1){
		//  	$sys_user_id = $this->db->insert_id();
		//  	$args['f_company'] = (empty($args['f_company'])) ? 0 : $args['f_company'];

		//  	$query="UPDATE app_members SET sys_shop = ?, fname = ?, mname = ?, lname = ?, mobile_number = ?, address = ?, position = ?, updated = ?, role_id = ?, company_id =? 
		//  	WHERE sys_user = ?";

		//  	$params = array(
		// 		$args['f_shops'],
		// 		$args['f_fname'],
		// 		$args['f_mname'],
		// 		$args['f_lname'],
		// 		$args['f_conno'],
		// 		$args['f_address'],
		// 		$args['f_position'],
		// 		date('Y-m-d H:i:s'),
		// 		$args['f_roles'],
		// 		$args['f_company'],
		// 		$id
		// 	);
		//  	$this->db->query($query,$params);
		//  }

		//  $query="UPDATE sys_linked_accounts SET from_shop_id = ?, date_updated = ? WHERE user_id = ?";

		//  	$params = array(
		// 		$args['f_shops'],
		// 		date('Y-m-d H:i:s'),
		// 		$id
		// 	);
		//  	$this->db->query($query,$params);
		return $query;
	}

	public function get_data($id)
	{
		$sql = "SELECT * FROM sys_users WHERE id = ?";
		$query = $this->db->query($sql, [$id]);
		return $query->result_array();
	}


	public function disable_data($disable_id, $record_status)
	{
		$sql = "UPDATE sys_users SET active = ? WHERE id = ?";
		return $this->db->query($sql, [$record_status, $disable_id]);
	}

	public function delete_data($delete_id,$user_data)
	{

		foreach($user_data as $value){       
		

			$sql = "UPDATE sys_users SET enabled = 0, username = ? WHERE id = ?";
			return $this->db->query($sql, ['Deleted('.$value['username'].')',$delete_id]);

		}
	}

	public function user_list_table($filters, $requestData, $exportable = false)
	{
		$_record_status = $filters['_record_status'];
		$_username 			= $filters['_username'];
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);

		$columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'username',
            3 => 'active'
		);

		$sql = "SELECT * FROM sys_users WHERE active > 0";
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 

		$sql = "SELECT * FROM sys_users WHERE 1";

		// start - for default search
		if ($_record_status == 1) {
			$sql.=" AND active = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" AND active = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" AND active > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if ($_username != "") {
			$sql.=" AND username LIKE '%" . $this->db->escape_like_str($_username) . "%' ";
		}
		// end - getting records as per search parameters

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		if (!$exportable) {
			$sql .= " LIMIT ".$requestData['start']." ,".$requestData['length'];
		}
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData = array();
			$nestedData[] = $row['id'];

			   $nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.base_url().'assets/uploads/avatars/'.$row['avatar'].'" onerror="this.onerror=null;this.src=`'.base_url().'assets/img/placeholder-500x500.jpg`;">';
		
		   
			$nestedData[] = $row['username'];
			if ($row['active'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
				$status = "Active";
			}else if ($row['active'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
				$status = "Inactive";
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
				$status = "Active";
			}

			$nestedData[] = $status;
			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['aul']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" href="'.base_url('admin/settings/user_list/edit_user/'.$token.'/'.$row['id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    		<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['aul']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['active'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    		<div class="dropdown-divider"></div>';
			}
			if ($this->loginstate->get_access()['aul']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';

			$nestedData[] = $actions;
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