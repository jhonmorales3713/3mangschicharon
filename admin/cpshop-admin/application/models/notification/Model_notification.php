<?php
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) { //to ignore maximum time limit
    @set_time_limit(0);
}
set_time_limit(0);
ini_set('memory_limit', '2048M');

class Model_notification extends CI_Model {

	# Start - Notification

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_paid_orders_with_branch', 'model_powb');	
	}

    public function get_sys_shop($user_id){
		$sql=" SELECT sys_shop FROM app_members WHERE sys_user = ? AND status = 1";
		$sql = $this->db->query($sql, $user_id);

        if($sql->num_rows() > 0){
            return $sql->row()->sys_shop;
        }else{
            return "";
        }
    }

    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1 ORDER BY shopname";
		return $this->db->query($query)->result_array();
    }

    public function notifications_table(){
		// storing  request (ie, get/post) global array to a variable
		$_shops 		   = $this->input->post('_shops');
		$token_session     = $this->session->userdata('token_session');
        $member_id         = $this->session->userdata('sys_users_id');
        $sys_shop          = $this->get_sys_shop($member_id);
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
			0 => 'b.date_created'
		);

        $sql = "SELECT a.*, shop.shopname, b.date_created, b.activity, b.activity_details, b.sub_module FROM sys_notification_logs AS a
        LEFT JOIN sys_notification AS b ON a.sys_notification_id = b.id AND b.status = 1
        LEFT JOIN sys_shops AS shop ON a.shop_id = shop.id
        WHERE a.status = 1
        ";

        if($sys_shop != 0 && $branchid == 0){
            $sql .= " AND a.shop_id =".$this->db->escape_str($sys_shop)." AND a.branch_id = 0";
        }
        else if($sys_shop != 0 && $branchid != 0){
            $sql .= " AND a.shop_id =".$this->db->escape_str($sys_shop)." AND a.branch_id =".$this->db->escape_str($branchid)."";
        }
        else{
            $sql .= " AND a.shop_id <> 0 AND a.branch_id = 0";
        }

        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array();
            $boldfont_start = ($row['has_read'] == 0) ? '<b>' : '';
            $boldfont_end   = ($row['has_read'] == 0) ? '</b>' : '';

			$nestedData[] = $boldfont_start.date("M d, Y h:i A", strtotime($row["date_created"])).$boldfont_end;
			$nestedData[] = $boldfont_start.$row["shopname"].$boldfont_end;
			$nestedData[] = $boldfont_start.$row["activity"].$boldfont_end;
			$nestedData[] = $boldfont_start.$row["activity_details"].$boldfont_end;
			$nestedData[] = $boldfont_start.$row["sub_module"].$boldfont_end;
            $buttons      = "<button type='button' class='btn btn-primary openNotifBtn' data-toggle='modal' data-target='#notifcationModal' id='openNotifBtn' data-notiflogs_id='".$row['id']."'>Open</button>";
			$nestedData[] = $buttons;

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

    public function get_notification_details($notiflogs_id){
        $sql = "SELECT a.*, shop.shopname, b.date_created, b.activity, b.activity_details, b.sub_module FROM sys_notification_logs AS a
        LEFT JOIN sys_notification AS b ON a.sys_notification_id = b.id AND b.status = 1
        LEFT JOIN sys_shops AS shop ON a.shop_id = shop.id
        WHERE a.status = 1 AND a.id = ?";
        
        $params = array($notiflogs_id);
        $query = $this->db->query($sql, $params);

        return $query;
    }

    public function get_notificationlogs_details($sys_notification_id, $shop_id, $branch_id){
        $sql = "SELECT * FROM sys_notification_logs
        WHERE sys_notification_id = ? AND shop_id = ? AND branch_id = ? AND status = 1";
        
        $params = array($sys_notification_id, $shop_id, $branch_id);
        $query = $this->db->query($sql, $params);

        return $query;
    }

    public function update_notifiation_has_read($notiflogs_id){
        $sql = "UPDATE sys_notification_logs SET has_read = 1, date_read = ? WHERE id = ?";
        
        $params = array(
            date('Y-m-d H:i:s'),
            $notiflogs_id
        );
        
        $query = $this->db->query($sql, $params);

        return $query;
    }

    public function get_notification(){
        $token_session     = $this->session->userdata('token_session');
        $member_id         = $this->session->userdata('sys_users_id');
        $sys_shop          = $this->get_sys_shop($member_id);
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');

        $sql = "SELECT a.*, shop.shopname, b.date_created, b.activity, b.activity_details, b.sub_module FROM sys_notification_logs AS a
        LEFT JOIN sys_notification AS b ON a.sys_notification_id = b.id AND b.status = 1
        LEFT JOIN sys_shops AS shop ON a.shop_id = shop.id
        WHERE a.status = 1
        ";

        if($sys_shop != 0 && $branchid == 0){
            $sql .= " AND a.shop_id =".$this->db->escape_str($sys_shop)." AND a.branch_id = 0";
        }
        else if($sys_shop != 0 && $branchid != 0){
            $sql .= " AND a.shop_id =".$this->db->escape_str($sys_shop)." AND a.branch_id =".$this->db->escape_str($branchid)."";
        }
        else{
            $sql .= " AND a.shop_id <> 0 AND a.branch_id = 0";
        }

        $sql .= " ORDER BY a.id DESC LIMIT 5";

        $query = $this->db->query($sql);

        return $query;

    }

    public function count_notification(){
        $token_session     = $this->session->userdata('token_session');
        $member_id         = $this->session->userdata('sys_users_id');
        $sys_shop          = $this->get_sys_shop($member_id);
		$token 			   = en_dec('en', $token_session);
		$branchid 		   = $this->session->userdata('branchid');

        $sql = "SELECT COUNT(a.id) as notif_count FROM sys_notification_logs AS a
        LEFT JOIN sys_notification AS b ON a.sys_notification_id = b.id AND b.status = 1
        LEFT JOIN sys_shops AS shop ON a.shop_id = shop.id
        WHERE a.status = 1
        ";

        if($sys_shop != 0 && $branchid == 0){
            $sql .= " AND a.shop_id =".$this->db->escape_str($sys_shop)." AND a.branch_id = 0";
        }
        else if($sys_shop != 0 && $branchid != 0){
            $sql .= " AND a.shop_id =".$this->db->escape_str($sys_shop)." AND a.branch_id =".$this->db->escape_str($branchid)."";
        }
        else{
            $sql .= " AND a.shop_id <> 0 AND a.branch_id = 0";
        }

        $sql .= " AND a.has_read = 0";

        $query = $this->db->query($sql);

        return $query;

    }
    # End - Notification
}
