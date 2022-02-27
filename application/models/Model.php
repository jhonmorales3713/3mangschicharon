<?php 

class Model extends CI_Model { 
	public function validate_username($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT *, m.status as user_status, u.id as sys_users_id, s.logo, s.shopcode, s.shopurl, s.shopname, m.id as app_members_id, s.id as sys_shop_id, s.status as shop_status, b.status as branch_status, u.first_login, u.code_isset
				FROM sys_users u
				LEFT JOIN app_members m
				ON u.id = m.sys_user
				LEFT JOIN sys_shops s
				ON m.sys_shop =  s.id
				LEFT JOIN sys_branch_profile b
				ON m.branchid =  b.id
				WHERE username = ?
				AND m.status IN (1,3)
				LIMIT 1";
		$data = array($username);
		return $this->db->query($sql, $data);
	}
    
	public function resetLoginAttempts($user_id, $ip_address, $date_created){
		$sql = "SELECT * FROM sys_login_attempt WHERE user_id = ? AND ip_address = ? AND status = 1";
		$data = array($user_id, $ip_address);
		$check_exist = $this->db->query($sql, $data)->row_array();

		if(!empty($check_exist)){
			$attempt = 0;

			$sql = "UPDATE sys_login_attempt SET attempt = ?, isLoggedIn = 1, date_updated = ? WHERE user_id = ? AND ip_address = ?";
			$data = array($attempt, $date_created, $user_id, $ip_address);
			$this->db->query($sql, $data);
		}
		else{
			$attempt = 0;
			$sql = "INSERT INTO sys_login_attempt (user_id, attempt, ip_address, isLoggedIn, date_created, status) VALUES (?, ?, ?, ?, ?, ?)";
			$data = array($user_id, $attempt, $ip_address, 1, $date_created, 1);
			$this->db->query($sql, $data);
		}

		return $attempt;
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