<?php 

class Model extends CI_Model { 
	public function validate_username($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT *, active as user_status, u.id as sys_users_id, s.logo, s.shopcode, s.shopurl, s.shopname, s.id as sys_shop_id, s.status as shop_status, u.first_login, u.code_isset
				FROM sys_users u
				LEFT JOIN sys_shops s
				ON 1 =  s.id
				WHERE username = ?
				LIMIT 1";
		$data = array($username);
		return $this->db->query($sql, $data);
	}
    
	public function get_content_navigation($main_nav_id){
		$sql = "SELECT * FROM cp_content_navigation WHERE cn_fkey = ? AND status = 1  ORDER BY cn_name ASC";
		$data = array($main_nav_id);
		return $this->db->query($sql,$data);
	}
	
	public function first_validate_username_md5($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT * FROM sys_users
				WHERE md5(username) = ?
				AND active = 1
				LIMIT 1";

		$data = array($username);
		return $this->db->query($sql, $data);
	}
	
	public function checkIfFirstReset($email){
		$query="SELECT * FROM sys_users WHERE md5(username) = ? AND login_code = 1";
		$argument = array(
			$email
		);

		return $this->db->query($query,$argument);
	}
	public function get_cities($sys_shop = null) {
		if ($sys_shop == null) {
			$query="SELECT * FROM sys_delivery_areas WHERE status = 1";
			return $this->db->query($query);
		}else{
			$query = "SELECT * FROM `sys_delivery_areas` WHERE `status` = 1 AND id NOT IN (SELECT areaid FROM `sys_shop_shipping` WHERE `sys_shop` = ?)";
			$data = array($sys_shop);
			return $this->db->query($query, $data);
		}
	}


	public function get_url_content_db($arr_){
		$sql = "SELECT cn_url FROM cp_content_navigation WHERE id IN ? AND status = 1";
		$data = array($arr_);
		return $this->db->query($sql, $data);
	}

	public function resetLoginAttempts($user_id){
		$sql = "SELECT * FROM sys_users WHERE id = ? AND active = 1";
		$data = array($user_id);
		$check_exist = $this->db->query($sql, $data)->row_array();

		if(!empty($check_exist)){
			$attempt = 0;
			$sql = "UPDATE sys_users SET attempt = ? WHERE id = ?";
			$data = array($attempt,$user_id);
			$this->db->query($sql, $data);
		}

		return $attempt;
	}

	public function getMainNav()
	{
		
		$sql = "SELECT * from cp_main_navigation";
		return $this->db->query($sql);
	}

	public function get_main_nav_id($labelname)
	{
		
		$sql = "SELECT * from cp_main_navigation where main_nav_desc = '$labelname'";
		return $this->db->query($sql);
	}
	
	public function get_main_nav_id_cn_url($content_url){
		$sql = "SELECT cn_fkey FROM `cp_content_navigation` WHERE cn_url = ? AND status = 1";
		$data = array($content_url);
		$query = $this->db->query($sql, $data);

		if ($query->num_rows() > 0) {
			return $query->row()->cn_fkey;
		}else{
			return "";
		}
	}

	public function log_seller_time_activity($seller, $shop, $activity = 'in')
	{
		$date = date('Y-m-d H:i:s');
		if ($activity == 'out') {
			$sql = 'Update sys_users_activity SET out_time = ? WHERE sys_user_id = ? and sysshop = ?';
			$this->db->query($sql, [$date, $seller, $shop]);
			log_message('error', 'out');
			return ;
		}

		$sql = 'SELECT * FROM sys_users_activity where sys_user_id = ? and sysshop = ? LIMIT 1';
		$result = $this->db->query($sql, [$seller, $shop])->row_array();

		$response = '';
		if (! isset($result['id'])) {
			$sql = 'INSERT into sys_users_activity (in_time, sys_user_id, sysshop) VALUES (?, ?, ?)';
			$response = $this->db->query($sql, [$date, $seller, $shop]);
		} else {
			if (isset($result['id'])) {
				if ($activity == 'in') {
					$sql = 'Update sys_users_activity SET in_time = ? , out_time = ? WHERE sys_user_id = ? and sysshop = ?';
					$response = $this->db->query($sql, [$date, '', $seller, $shop]);
				}
			}
		}
		return $response;
	}
}
?>