<?php 

class Model_address extends CI_Model {        
    
    public  function insert_address($shipping_address){
        $count_all = $this->get_shipping_address($shipping_address['customer_id']);
        if(count($count_all) == 0){
            $shipping_address['enabled'] = 1;
        }else{
            $shipping_address['enabled'] = 2;
        }
        $this->db->insert('sys_shipping_address',$shipping_address);
        return $this->db->insert_id();
    }

    public  function set_default_address($shipping_address){
        // print_r(en_dec('dec',$shipping_address['customer_id']));
        // print_r(en_dec('dec',$shipping_address['id']));
        // die();
        $sql = "UPDATE sys_shipping_address SET enabled = 2 WHERE customer_id = ?";
        $this->db->query($sql,en_dec('dec',$shipping_address['customer_id']));

        $sql = "UPDATE sys_shipping_address SET enabled = 1 WHERE id = ?";
        $this->db->query($sql,en_dec('dec',$shipping_address['id']));
        
        // $count_all = $this->get_shipping_address($shipping_address['customer_id']);
        // if(count($count_all) == 0){
        //     $shipping_address['enabled'] = 1;
        // }else{
        //     $shipping_address['enabled'] = 2;
        // }
        // $this->db->insert('sys_shipping_address',$shipping_address);
        // return $this->db->insert_id();
    }
    public  function remove_address($shipping_address){
        // print_r(en_dec('dec',$shipping_address['customer_id']));
        // print_r(en_dec('dec',$shipping_address['id']));
        // die();

        $sql = "UPDATE sys_shipping_address SET enabled = -1 WHERE id = ?";
        $this->db->query($sql,en_dec('dec',$shipping_address['id']));
        
        $count_all = $this->get_shipping_address(en_dec('dec',$shipping_address['customer_id']));
        $present_default = 0;
        foreach($count_all as $data){
            if($data['enabled'] == 1){
                $present_default = 1;
            }
        }
        if($present_default == 0){
            $id = 0;
            foreach($count_all as $data){
                $id = $count_all[0]['id'];
            }
            
            $sql = "UPDATE sys_shipping_address SET enabled = 1 WHERE id = ?";
            $this->db->query($sql,$id);
        }
        // $count_all = $this->get_shipping_address($shipping_address['customer_id']);
        // if(count($count_all) == 0){
        //     $shipping_address['enabled'] = 1;
        // }else{
        //     $shipping_address['enabled'] = 2;
        // }
        // $this->db->insert('sys_shipping_address',$shipping_address);
        // return $this->db->insert_id();
    }
    
    public function get_shipping_address($customer_id){
        $sql = "SELECT
                    sa.*,
                    sat.address_type
                FROM
                    sys_shipping_address sa
                LEFT JOIN
                    sys_shipping_address_types sat
                ON 
                    sa.address_category_id = sat.id
                WHERE
                    sa.customer_id = ? AND sa.enabled > -1";
        
        return $this->db->query($sql,[$customer_id])->result_array();
    }

    public function update_shipping_address($customer_id,$shipping_address){
        $this->db->where('customer_id',$customer_id);
        return $this->db->update('sys_shipping_address',$shipping_address);
    }

    public function update_shipping_address_by_id($address_id,$shipping_address){
        $this->db->where('id',$address_id);
        return $this->db->update('sys_shipping_address',$shipping_address);
    }

    public function get_shippind_address_by_id($address_id){
        $sql = "SELECT
                    sa.*,
                    sat.address_type
                FROM
                    sys_shipping_address sa
                LEFT JOIN
                    sys_shipping_address_types sat
                ON 
                    sa.address_category_id = sat.id
                WHERE
                    sa.id = ?";
        
        return $this->db->query($sql,[$address_id])->row_array();
    }

}
