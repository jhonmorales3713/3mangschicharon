<?php
class Model_authentication extends CI_Model {

	public function get_user($username)
	{
		$query = "SELECT a.*,b.first_name, b.last_name, b.email, b.conno,
		 b.address1, b.address2, b.gender, b.birthdate, c.receiver_name, c.address as receiver_address,
		 c.receiver_contact, c.landmark as receiver_landmark, c.postal_code as receiver_postal_code,
		 c.region_id as receiver_region_id, c.municipality_id as receiver_municipality_id
		 FROM sys_customer_auth as a
		 left join app_customers as b on a.id = b.user_id
		 LEFT JOIN app_customer_addresses c ON a.id = c.customer_id AND c.default_add = 1 AND c.enabled = 1
		 WHERE username = ? and a.active = '1' and b.status = '1'";

    return $this->db->query($query,$username);
	}

	public function validate_username($username)
	{
		$username = $this->db->escape($username);
		$sql = "SELECT username FROM sys_customer_auth WHERE username = $username AND active = 1";
		return $this->db->query($sql);
	}

	public function validate_customer_email($email,$self = false)
	{
		$email = $this->db->escape($email);
		$sql = "SELECT email FROM app_customers
		 WHERE email = $email AND status = 1";
		if($self){
			$self = $this->db->escape($self);
			$sql .= " AND user_id != $self";
		}
		return $this->db->query($sql);
	}

	public function set_sys_customer_auth($data)
	{
		$this->db->insert('sys_customer_auth', $data);
		$res = array(
			"user_id" => ($this->db->affected_rows() > 0) ? $this->db->insert_id() : 0,
			"status" => ($this->db->affected_rows() > 0) ? true: false
		);
		return $res;
	}

	public function set_app_customers($data)
	{
		$this->db->insert('app_customers', $data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function set_customer_auth_audittrail($data){
		$this->db->insert('sys_customer_auth_audittrail',$data);
	}

	public function update_password($new_pass,$username)
	{
		$sql = "UPDATE sys_customer_auth SET password = ? WHERE username = ?";
		$data = array($new_pass,$username);
		$this->db->query($sql,$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function update_failed_attempt($username,$reset = false)
	{
		if($reset){
			$sql = "UPDATE sys_customer_auth
				SET failed_login_attempts = 0, last_failed_attempt = ?
				WHERE username = ?";
		}else{
			$sql = "UPDATE sys_customer_auth
				SET failed_login_attempts = (failed_login_attempts + 1), last_failed_attempt = ?
				WHERE username = ?";
		}

		$data = array(todaytime(),$username);
		$this->db->query($sql,$data);
	}

	public function update_last_seen($username)
	{
		$sql = "UPDATE sys_customer_auth SET lastseen = ? WHERE username = ?";
		$data = array(todaytime(),$username);
		$this->db->query($sql,$data);
	}

	public function set_address($data){
    $this->db->insert('app_customer_addresses',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }
}
