<?php 

class Model_orders extends CI_Model {        
    
    public function insert_order($order){
        $this->db->insert('sys_orders',$order);
        return $this->db->insert_id();
    }

    public function get_order_info($order_id){
        $this->db->where('id',$order_id);
        return $this->db->get('sys_orders')->row_array();
    }
    
    public function check_unique($order_id){
        $this->db->where('order_id',$order_id);
        return $this->db->get('sys_orders')->num_rows();
    }

    public function orders_details($reference_num){
        $query = 'SELECT * FROM sys_orders WHERE order_id = "'.$reference_num.'"';
		return $this->db->query($query)->result_array();
    }
    
	public function get_cities() {
		$query = "SELECT * FROM sys_cities
				WHERE enabled = 1
				ORDER BY city_name ASC";

		return $this->db->query($query);

	}
    public function rate_order($id,$rating_data){
        $sql = 'UPDATE sys_orders SET customer_feedback = ? where id = ?';
        $bind_data = Array($rating_data,$id);
        $this->db->query($sql,$bind_data);
    }
    public function get_completed_orders(){
        $query = 'SELECT * FROM sys_orders WHERE status_id = 5';
		return $this->db->query($query)->result_array();
    }
    
    public function get_orders($customer_id,$order_id){
        $bind_data = [$customer_id];
        $sql = "SELECT 
                    o.*,
                    s.status_name
                FROM
                    sys_orders o
                LEFT JOIN
                    sys_order_status s
                ON
                    o.status_id = s.id
                WHERE
                    o.customer_id = ?";

        if($order_id != ""){
            $sql .= " AND o.id = ? ";
            array_push($bind_data,$order_id);
        }

        $sql .= " ORDER BY
                    o.date_created DESC";

        return $this->db->query($sql,$bind_data)->result_array();
    }
    public function update_payment($order_id = ''){
        
        if($this->get_payment_data($order_id) != '')
        {
            $sql = "UPDATE `sys_payments` SET status_id = 3 WHERE order_id = ?";
            
            $bind_data = array(
                $order_id
            );
            
            $this->db->query($sql, $bind_data);
            $sql = "UPDATE `sys_orders` SET status_id = 1 WHERE order_id = ?";
            
            $bind_data = array(
                $order_id
            );
            $this->db->query($sql, $bind_data);
        }

    }

}
