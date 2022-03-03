<?php 

class Model_users extends CI_Model {    
    
    public function insert_user($user_data){
        $this->db->insert('users',$user_data);
        return $this->db->insert_id();
    }   
    
    public function get_user_by_email($email){
        $this->db->where('email',$email);
        return $this->db->get('users')->row_array();
    }

    public function get_user($user_id){
        
    }

}
