<?php 

class Model_landing extends CI_Model { 
    
    public function get_faqs($category_id = 0){
        $sql = "SELECT 
                    *
                FROM
                    faqs
                WHERE
                    1";
        
        if($category_id != 0){
            $sql .= " AND category_id = ?";
            return $this->db->query($sql,[$category_id])->result_array();
        }
        else{
            return $this->db->query($sql)->result_array();
        }
    }

    public function insert_message($info_message){
        $this->db->insert('sys_messages',$info_message);        
        return $this->db->insert_id();
    }

    public function get_message($message_id){
        $this->db->where('id',$message_id);
        return $this->db->get('sys_messages')->row_array();
    }

    public function get_messages(){
        return $this->db->get('messages')->result_array();
    }

    

}
