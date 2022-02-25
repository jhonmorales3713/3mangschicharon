<?php 
class Model_profile_settings extends CI_Model {

	// Start of Change Password

		public function check_pass_using_id_fk($id){
			$sql = "SELECT password FROM sys_users WHERE id = ? LIMIT 1";
			$data = array($id);
			return $this->db->query($sql, $data);
		}

		public function update_password($secNewpass, $id){
			$sql = "UPDATE sys_users SET password = ?WHERE id = ?";
			$data = array($secNewpass, $id);
			$this->db->query($sql,$data); 
		}

		public function update_first_password($secNewpass, $id){
			$sql = "UPDATE sys_users SET password = ?, login_code = null WHERE id = ?";
			$data = array($secNewpass, $id);
			$this->db->query($sql,$data); 
		}

	// End of Change Password

	// Start of Personal Information

		public function get_user_personal_information($id) {

			$sql = "SELECT a.fname, a.mname, a.lname, a.mobile_number, b.avatar
				FROM app_members a
				LEFT JOIN sys_users b ON a.sys_user = b.id 
				WHERE a.sys_user = ?";

			return $this->db->query($sql, array($id));
		}

		public function save_changeavatar($fname, $mname, $lname, $mobile_no, $img, $id){
			// Save Name
			$sql = "UPDATE app_members SET `fname` = ?, `mname` = ?, `lname` = ? , `mobile_number` = ? WHERE `sys_user` = ?";

			$data = array($fname, $mname, $lname, $mobile_no, $id);
			$this->db->query($sql, $data); 

			// Save Avatar
			$sql = "UPDATE sys_users SET `avatar` = ? WHERE `id` = ?";

			$data = array($img, $id);
			return $this->db->query($sql, $data);
		}

		public function save_profilename($fname, $mname, $lname, $mobile_no, $id){
			// Save Name
			$sql = "UPDATE app_members SET `fname` = ?, `mname` = ?, `lname` = ? , `mobile_number` = ? WHERE `sys_user` = ?";
			
			$data = array($fname, $mname, $lname, $mobile_no, $id);
		
			return $this->db->query($sql, $data);
		}

	// End of Personal Information
}