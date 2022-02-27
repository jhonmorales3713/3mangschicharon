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

    

}
