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
        return $this->db->insert_id();

    }

    public function update_payment($payment_id, $order_id = null){
        
    }
    

}
