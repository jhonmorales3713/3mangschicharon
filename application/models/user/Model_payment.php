<?php 

class Model_payment extends CI_Model {

    public function save_source_data($source_data,$order_data){

        $info = array(
            'order_id' => $order_data['order_id'],
            'ref_no' => $source_data['data']['id'],
            'source_data' => json_encode($source_data),
            'total_amount' => $order_data['total_amount'],
        );

        $this->db->insert('sys_payments',$info);
        
		$sql = "UPDATE `sys_orders` SET status_id = 1 WHERE order_id = ?";
		
		$bind_data = array(
			$order_data['order_id']
		);
        
		return $this->db->query($sql, $bind_data);
        return $this->db->insert_id();

    }

    public function check_order_id_exists($order_id){
        $this->db->where('order_id',$order_id);
        return $this->db->get('sys_payments')->num_rows();
    }

    public function get_payment_data($order_id){
        $this->db->where('order_id',$order_id);
        return $this->db->get('sys_payments')->row_array();
    }

    public function update_source_data($source_data){
        $this->db->where('ref_no',$source_data['data']['id']);
        $update = array(
            'source_data' => json_encode($source_data),
        );
        $this->db->update('sys_payments',$update);
    }

    public function update_payment($payment_id, $order_id = null){

    }
    

}
