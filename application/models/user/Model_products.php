<?php 

class Model_products extends CI_Model {    

    public function get_categories(){
        $sql = "SELECT
                    c.*                    
                FROM                    
                    categories c";
                    
        return $this->db->query($sql)->result_array();
    }
    
    public function get_products(){
        $sql = "SELECT
                    p.*,                    
                    c.category_name
                FROM 
                    products p
                LEFT JOIN
                    categories c
                ON
                    p.category_id = c.id";
        return $this->db->query($sql)->result_array();
    }    

}
