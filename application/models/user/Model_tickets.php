<?php 

class Model_tickets extends CI_Model { 
    
    public function check_status($ticket_num){
        $this->db->where('ticket_num',$ticket_num);
        return $this->db->get('bookings')->row_array();
    }

    

}
