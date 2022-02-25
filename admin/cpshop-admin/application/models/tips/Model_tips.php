<?php 
class Model_tips extends CI_Model {
    public function is_tipsOn($user_id){
        $sql ="SELECT tips FROM sys_users WHERE id = ? AND active > ?";
        $data = array($user_id, 0);
        $result = $this->db->query($sql, $data);

        $status = true;
        if($result->row()->tips == 1){
            $status = true;
        }else{
            $status = false;
        }
        return $status;
    }

    public function tip_off($user_id){
        $sql ="UPDATE sys_users SET tips = ? WHERE active > ? AND id = ?";
        $data = array(0, 0, $user_id);
        $this->db->query($sql, $data);
    }

    public function check_product($sys_shop_id){
        $sql = "SELECT * FROM sys_products WHERE sys_shop = ? AND enabled > ?";
        $data = array($sys_shop_id, 0);
        return $this->db->query($sql, $data);
    }

    public function check_shipping_delivery($sys_shop_id){
        $sql = "SELECT * FROM sys_shipping WHERE sys_shop_id = ? AND enabled > ?";
        $data = array($sys_shop_id, 0);
        return $this->db->query($sql, $data);
    }

    public function check_banner(){
        $sql = "SELECT * FROM sys_banners WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }

    public function check_product_category(){
        $sql = "SELECT * FROM sys_product_category WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }
    public function check_tips_status($user_id){
        $sql = "SELECT tips FROM sys_users WHERE active > ? AND id = ?";
        $data = array(0, $user_id);
        return $this->db->query($sql, $data);
    }
}
