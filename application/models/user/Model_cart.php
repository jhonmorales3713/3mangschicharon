<?php 

class Model_cart extends CI_Model {        
    
    public function get_payment_methods(){
        $this->db->where('enabled',1);
        return $this->db->get('sys_payment_methods')->result_array();
    }

    public function get_shipping_types(){
        $this->db->where('enabled',1);
        return $this->db->get('sys_shipping_types')->result_array();
    }

}
