<?php 

class Model_accounts extends CI_Model {    
    
    public function insert_document($doc_data){
        // print_r($doc_data);
        $this->db->where('id',$doc_data['customer_id']);
        $this->db->update('sys_customers',array('user_type_id'=>3));

        $this->db->insert('sys_uploaded_documents',$doc_data);
        return $this->db->insert_id();
    }

    public function check_pending_verification($customer_id){
        $this->db->where('customer_id',$customer_id);
        $this->db->where('is_verified',0);
        return $this->db->get('sys_uploaded_documents')->num_rows();
    }

    public function get_verified_documents($customer_id){
        $this->db->where('customer_id',$customer_id);
        return $this->db->get('sys_uploaded_documents')->result_array();
    }    

    public function update_profile($customer_id, $customer_info){
        $this->db->where('id',$customer_id);
        $this->db->update('sys_customers',$customer_info);
    }

}
