<?php 

class Model_orders extends CI_Model {        
    
    public function insert_order($order){
        $this->db->insert('sys_orders',$order);
        return $this->db->insert_id();
    }

}
