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

}