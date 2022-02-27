<?php 

class Model_bookings extends CI_Model {

    public function insert_booking($booking_info){
        $this->db->insert('bookings',$booking_info);
        return $this->db->insert_id();
    }   

    public function check_unique($token){
        $this->db->where('ticket_num',$token);
        return $this->db->get('bookings')->num_rows();
    }

}
