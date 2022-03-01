<?php 

class Model_users extends CI_Model {    
    
    public function insert_user($user_data){
        $this->db->insert('users',$user_data);
        return $this->db->insert_id();
    }    

}
