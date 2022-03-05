<?php 

class Model_address extends CI_Model {        
    
    public  function insert_address($shipping_address){
        $this->db->insert('sys_shipping_address',$shipping_address);
        return $this->db->insert_id();
    }
    
    public function get_shipping_address($customer_id){
        $this->db->where('customer_id',$customer_id);
        $this->db->where('enabled',1);
        return $this->db->get('sys_shipping_address')->result_array();
    }

    public function update_shipping_address($customer_id,$shipping_address){
        $this->db->where('customer_id',$customer_id);
        return $this->db->update('sys_shipping_address',$shipping_address);
    }

}
