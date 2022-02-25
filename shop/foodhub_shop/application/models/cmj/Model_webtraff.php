<?php

class Model_webtraff extends CI_Model {

		
	
	function total_online($sess)
	{
		$todaytime = todaytime();
		$today = today();


		$current_time=time();
		$timeout = $current_time - (60);

		$sql = "SELECT session FROM web_total_visitors WHERE session=? AND date(trandate)=?";
		$data = array($sess,$today);
		$numrows = $this->db->query($sql,$data)->num_rows();
		
		if($numrows==0 AND $sess!="")
		{
			$sql = "INSERT INTO web_total_visitors(session,timesess,trandate) VALUES(?,?,?)";
			$data = array($sess,$current_time,$todaytime);
			$this->db->query($sql,$data);
		}
		else
		{
			$sql = "UPDATE web_total_visitors SET timesess=? WHERE session=? AND date(trandate)=?";
			$data = array($current_time,$sess,$today);
			$this->db->query($sql,$data);
		}

		// $sql = "SELECT * FROM total_visitors WHERE timesess>=?";
		// $data = array($timeout);
		// $numrows = $this->db->query($sql,$data)->num_rows();

		// return $numrows;
		return 1;

	}


	function total_pageviews()
	{
		$user_ip=$_SERVER['REMOTE_ADDR'];
		$page=$_SERVER['PHP_SELF'];
		$todaytime = todaytime();

		$sql = "INSERT INTO web_pageviews(page,ip,trandate) VALUES(?,?,?)";
		$data = array($page,$user_ip,$todaytime);
		$this->db->query($sql,$data);

		// $sql = "SELECT * FROM pageviews";
		// $data = array($timeout);
		// $numrows = $this->db->query($sql,$data)->num_rows();

		// return $numrows;
		return 1;
	}






}//close class

?>