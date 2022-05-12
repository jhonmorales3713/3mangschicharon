<?php 

class Model_customers extends CI_Model {    
    
    public function insert_customer($customer_data){
        $this->db->insert('sys_customers',$customer_data);
        return $this->db->insert_id();
    }   
    
    public function get_customer_by_email($email){
        $this->db->where('email',$email);
        return $this->db->get('sys_customers')->row_array();
    }

    public function get_customer_addresses($id){
        $this->db->where('id',$id);
        return $this->db->get('sys_shipping_address')->result_array();
    }

    public function save_cart_session($cart_session){
        $this->db->insert('sys_cart_sessions',$cart_session);
    }

    public function get_cart_session($customer_id){
        $this->db->where('customer_id',$customer_id);
        return $this->db->get('sys_cart_sessions')->row_array();
    }

    public function update_cart_session($customer_id,$cart_data){
        $this->db->where('customer_id',$customer_id);    
        $this->db->update('sys_cart_sessions',array('cart_data' => $cart_data));
    }

    public function remove_cart_session($customer_id){
        $this->db->where('customer_id',$customer_id);
        $this->db->delete('sys_cart_sessions');
    }

}
