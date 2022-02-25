<?php


class Webtraff extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('cmj/Model_webtraff');
  }

  function webtraff()
  {
      //print_r("HERE");
      $get_online_visitor = $this->input->post('get_online_visitor');

      $this->Model_webtraff->total_online($_SESSION['sesswebtraf']);
      
  }
	





}//close class
?>