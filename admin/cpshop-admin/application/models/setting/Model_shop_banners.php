<?php 
class Model_shop_banners extends CI_Model {

	public function get_banners(){
		$sql = "SELECT * FROM sys_banners where status = 1 ORDER BY sorting, scheduledFrom_post ASC";
		return $this->db->query($sql)->result();
	}

	public function get_inactive_banners()
	{
		$sql = "SELECT * FROM `sys_banners` WHERE `status` = 0 ORDER BY id, `scheduledFrom_post` ASC";
		return $this->db->query($sql)->result();
	}

	public function get_banner_info($filename){
		$sql = "SELECT * FROM sys_banners WHERE `filename` = ? AND `status` = 1";
		$data = array($filename);
		return $this->db->query($sql,$data)->row();
	}

	public function add_banner($filename,$sorting){
		$sql = "INSERT INTO sys_banners (`filename`,`sorting`)
				VALUES (?,?)";
		$data = array($filename,$sorting);
		$query = $this->db->query($sql, $data);
		return $this->db->insert_id();
	}

	public function delete_banner($target_directory_image){
		$sql = "UPDATE `sys_banners` SET `status` = '0', `is_active` = 0 WHERE `filename` = ? AND `status` = 1";
		$data = array($target_directory_image);
		$query = $this->db->query($sql, $data);
		return $this->db->affected_rows();
	}

	public function update_sorting($filename,$sorting){
		$sql = "UPDATE sys_banners SET sorting = ? WHERE filename = ? AND status = 1";
		$data = array($sorting,$filename);
		$query = $this->db->query($sql,$data);
		return $this->db->affected_rows();
	}


	public function add_banner_link($banner_array){
		$sql = "UPDATE `sys_banners` SET `banner_link` = ? WHERE `id` = ? AND `status` = 1";
		$data = array($banner_array['BannerLink'],$banner_array['BannerID']);
		$query = $this->db->query($sql, $data);
	}

	public function setSchedPost_Banner($data = array())
	{
		$sql = "UPDATE `sys_banners` SET `scheduledFrom_post` = ?, `scheduledTo_post` = ? WHERE id = ?";
		return $this->db->query($sql, $data);
	}

	public function getBanner_info($id)
	{
		$sql = "SELECT * FROM `sys_banners` WHERE id = ?";
		return $this->db->query($sql, $id);
	}

	public function deact_banner($id)
	{
		$sql = "UPDATE `sys_banners` SET `status` = 1, `is_active` = 0 WHERE id = ?";
		return $this->db->query($sql, $id);
	}

	public function activate_banner($id)
	{
		$sql = "UPDATE `sys_banners` SET `status` = 1, `is_active` = 1 WHERE id = ?";
		return $this->db->query($sql, $id);
	}

	public function check_activate($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_banners` WHERE DATE(`scheduledFrom_post`) = DATE(?) AND TIME(`scheduledFrom_post`) = TIME(?) AND `status` = ? AND `is_active` = ?";
		return $this->db->query($sql, $data)->row()->count;
	}

	public function check_deactivate($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_banners` WHERE DATE(`scheduledTo_post`) = DATE(?) AND TIME(`scheduledTo_post`) = TIME(?) AND `status` = ? AND `is_active` = ?";
		return $this->db->query($sql, $data)->row()->count;
	}

	public function deact_banner_sorting($data = array())
	{
		$sql = "UPDATE `sys_banners` SET `is_active` = 0 WHERE `status` = ? AND `is_active` = ? AND `sorting` = ?";
		return $this->db->query($sql, $data);
	}

	public function activate_banner_sched($data = array())
	{
		$sql = "UPDATE `sys_banners` SET `is_active` = 1 WHERE DATE(`scheduledFrom_post`) = DATE(?) AND TIME(`scheduledFrom_post`) = TIME(?) AND `status` = ? AND `is_active` = ?";
		return $this->db->query($sql, $data);
	}

	public function deactivate_banner_sched($data = array())
	{
		$sql = "UPDATE `sys_banners` SET `is_active` = 0 WHERE DATE(`scheduledTo_post`) = DATE(?) AND TIME(`scheduledTo_post`) = TIME(?) AND `status` = ? AND `is_active` = ?";
		return $this->db->query($sql, $data);
	}

	public function check_banner_startdate($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_banners` WHERE DATE(`scheduledFrom_post`) >= DATE(?) AND DATE(`scheduledTo_post`) <= DATE(?) /** startdate rin yung end date comparison */ AND `id` != ?";
		return $this->db->query($sql, $data)->row()->count;
	}

	public function check_banner_startdate_endDate($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_banners` WHERE DATE(`scheduledFrom_post`) > DATE(?) AND id != ?";
		return $this->db->query($sql, $data)->row()->count;
	}
	
	public function check_banner_startdate_endDate_final($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_banners` WHERE DATE(`scheduledFrom_post`) > DATE(?) AND DATE(`scheduledTo_post`) < DATE(?)/* end_date */ AND id != ?";
		return $this->db->query($sql, $data)->row()->count;
	}

	public function check_banner_endDate($data = array())
	{
		$sql = "SELECT COUNT(*) AS `count` FROM `sys_banners` WHERE DATE(`scheduledFrom_post`) >= DATE(?) AND DATE(`scheduledTo_post`) = DATE(?) /* both end_date */ AND id != ?";
		return $this->db->query($sql, $data)->row()->count;
	}

}