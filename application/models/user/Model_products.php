<?php 

class Model_products extends CI_Model {    

    public function get_categories(){
        $sql = "SELECT
                    c.*                    
                FROM                    
                    sys_product_category c";
                    
        return $this->db->query($sql)->result_array();
    }
    
    public function get_products(){
        $sql = "SELECT
                    p.*,                    
                    c.category_name
                FROM 
                    sys_products p
                LEFT JOIN
                    sys_product_category c
                ON
                    p.category_id = c.id
                WHERE
                    p.parent_product_id IS NULL";
        return $this->db->query($sql)->result_array();
    }  

    public function get_variants($product_id){
        $sql = "SELECT
                    id,
                    name,
                    price
                FROM
                    sys_products
                WHERE
                    parent_product_id = ?";

        return $this->db->query($sql,[$product_id])->result_array();
    }
    
    public function get_product_info($product_id){
        $sql = "SELECT
                    p.*,                                        
                    c.category_name
                FROM 
                    sys_products p
                LEFT JOIN
                    sys_product_category c
                ON
                    p.category_id = c.id
                WHERE
                    p.id = ?";
                
        return $this->db->query($sql,[$product_id])->row_array();
    }

}
