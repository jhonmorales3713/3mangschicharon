<?php 

class Model_orders extends CI_Model {        
    
    public function insert_order($order){
        $this->db->insert('sys_orders',$order);
        return $this->db->insert_id();
    }
    
    public function check_unique($order_id){
        $this->db->where('order_id',$order_id);
        return $this->db->get('sys_orders')->num_rows();
    }

}
