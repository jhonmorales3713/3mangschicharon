<?php 

class Model_address extends CI_Model {        
    
    public  function insert_address($shipping_address){
        $this->db->insert('sys_shipping_address',$shipping_address);
        return $this->db->insert_id();
    }
    
    public function get_shipping_address($customer_id){
        $sql = "SELECT
                    sa.*,
                    sat.address_type
                FROM
                    sys_shipping_address sa
                LEFT JOIN
                    sys_shipping_address_types sat
                ON 
                    sa.address_category_id = sat.id
                WHERE
                    sa.customer_id = ? AND sa.enabled = 1";
        
        return $this->db->query($sql,[$customer_id])->result_array();
    }

    public function update_shipping_address($customer_id,$shipping_address){
        $this->db->where('customer_id',$customer_id);
        return $this->db->update('sys_shipping_address',$shipping_address);
    }

    public function update_shipping_address_by_id($address_id,$shipping_address){
        $this->db->where('id',$address_id);
        return $this->db->update('sys_shipping_address',$shipping_address);
    }

    public function get_shippind_address_by_id($address_id){
        $sql = "SELECT
                    sa.*,
                    sat.address_type
                FROM
                    sys_shipping_address sa
                LEFT JOIN
                    sys_shipping_address_types sat
                ON 
                    sa.address_category_id = sat.id
                WHERE
                    sa.id = ?";
        
        return $this->db->query($sql,[$address_id])->row_array();
    }

}
