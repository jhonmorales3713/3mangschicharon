<?php 
class Model_generatedfilename extends CI_Model {
	public function is_exist($filename){
		$sql ="SELECT COUNT(*) as count FROM generated_filename WHERE filename = ? AND status = ?";
		$data = array($filename, 1);
		$count = $this->db->query($sql, $data)->row()->count;

		if($count > 0){
			$status = true;
		}else{
			$status = false;
		}

		return $status;
	}

	public function save_generatedfilename($filename){
		$sql="INSERT INTO generated_filename (filename, status) VALUES (?, ?)";
    	$data = array(strtoupper($filename), 1);
    	$this->db->query($sql, $data);
	}
}
?>