<?php 

class Model_home extends CI_Model {        
    
    public function get_banners(){
        return $this->db->where('enabled',1)->get('sys_banners')->result_array();
    }
    
    
}
