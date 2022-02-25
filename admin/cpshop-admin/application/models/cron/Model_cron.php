<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_cron extends CI_Model {
  public function set_cron_logs($data){
    $this->db->insert('sys_cron_logs',$data);
    return $this->db->insert_id();
  }

  public function update_cron_logs($data,$id){
    $this->db->update('sys_cron_logs',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
