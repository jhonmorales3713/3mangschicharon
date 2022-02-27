<?php 

class Model_packages extends CI_Model { 
    
    public function get_package($id){
        $sql = "SELECT
                    p.*,
                    pt.id as packge_type_id,
                    pt.package_type,
                    pg.package_group_name
                FROM 
                    packages p
                LEFT JOIN
                    package_types pt
                ON
                    p.package_type_id = pt.id
                LEFT JOIN
                    package_groups pg
                ON
                    p.package_group_id = pg.id
                WHERE
                    p.id = ?";
        $data = [$id];
        return $this->db->query($sql,$data)->row_array();
    }
    
    public function get_packages(){
        $sql = "SELECT
                    p.*,
                    pt.id as packge_type_id,
                    pt.package_type
                FROM 
                    packages p
                LEFT JOIN
                    package_types pt
                ON
                    p.package_type_id = pt.id";
        return $this->db->query($sql)->result_array();
    }

    public function get_package_groups(){
        $sql = "SELECT
                    *
                FROM 
                    package_groups";
        return $this->db->query($sql)->result_array();
    }

    public function get_sub_packages($package_id = 0){
        $sql = "SELECT
                    *
                FROM 
                    sub_packages
                WHERE 1";

        if($package_id != 0){
            $sql .= " AND package_id = ?";
            $data = [$package_id];
            return $this->db->query($sql,$data)->result_array();
        }
        else{
            return $this->db->query($sql)->result_array();
        }

        
    }

    

}
