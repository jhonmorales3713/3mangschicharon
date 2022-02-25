<?php
class Model extends CI_Model {

	public function validate_username($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT *, m.status as user_status, u.id as sys_users_id, s.logo, s.shopcode, s.shopurl, s.shopname, m.id as app_members_id, s.id as sys_shop_id, s.status as shop_status, b.status as branch_status, u.first_login, u.code_isset
				FROM sys_users u
				LEFT JOIN app_members m
				ON u.id = m.sys_user
				LEFT JOIN sys_shops s
				ON m.sys_shop =  s.id
				LEFT JOIN sys_branch_profile b
				ON m.branchid =  b.id
				WHERE username = ?
				AND m.status IN (1,3)
				LIMIT 1";

		$data = array($username);
		return $this->db->query($sql, $data);
	}

	public function get_position($email){
		$sql = "SELECT * FROM jcw_position WHERE position_id = ? LIMIT 1";
		$data = array($email);
		return $this->db->query($sql, $data);
	}

	public function get_users($user_id){
		$sql = "SELECT * FROM  jcw_users WHERE enabled = 2 AND user_id = ?";
		$data = array($user_id);
		return $this->db->query($sql, $data);
	}

	public function get_main_page_navigation(){
		$sql = "SELECT * FROM cp_main_navigation WHERE enabled >= 1";
		return $this->db->query($sql);
	}

	public function get_position_details_access($position_id){
		$sql = "SELECT * FROM jcw_position WHERE position_id = ? AND enabled = 1";
		$data = array($position_id);
		return $this->db->query($sql, $data);
	}

	public function get_shop_wallet($shopid,$branchid = 0){
		$shopid = $this->db->escape($shopid);
		$branchid = $this->db->escape($branchid);
		$sql = "SELECT * FROM sys_shops_wallet WHERE enabled = 1 AND shopid = $shopid AND branchid = $branchid";
		return $this->db->query($sql);
	}

	public function get_total_deduction($shopid,$branchid){
    $shopid = $this->db->escape($shopid);
    $branchid = $this->db->escape($branchid);
    $total_deduction = 0;
    $sql = "SELECT SUM(amount) as total_deduction
      FROM sys_shops_wallet_logs WHERE type = 'minus'
      AND enabled = 1 AND shopid = $shopid AND branchid = $branchid";
    $query = $this->db->query($sql);
    if($query->num_rows() > 0){
      $total_deduction = $query->row()->total_deduction;
    }

    return $total_deduction;
  }

	// 07/13/18
	public function get_content_navigation($main_nav_id){
		$sql = "SELECT * FROM cp_content_navigation WHERE cn_fkey = ? AND status = 1  ORDER BY cn_name ASC";
		$data = array($main_nav_id);
		return $this->db->query($sql,$data);
	}

	public function get_main_nav_id($labelname){
		$sql = "SELECT * FROM cp_main_navigation WHERE main_nav_desc = ? LIMIT 1 ";
		$data = array($labelname);
		return $this->db->query($sql, $data);

	}

	public function get_url_content_db($arr_){
		$sql = "SELECT cn_url FROM cp_content_navigation WHERE id IN ? AND status = 1";
		$data = array($arr_);
		return $this->db->query($sql, $data);
	}

	public function get_main_nav_id_cn_url($content_url){
		$sql = "SELECT cn_fkey FROM `cp_content_navigation` WHERE cn_url = ? AND status = 1";
		$data = array($content_url);
		$query = $this->db->query($sql, $data);

		if ($query->num_rows() > 0) {
			return $query->row()->cn_fkey;
		}else{
			return "";
		}
	}


	public function get_url_content_hasline_db(){
		$sql = "SELECT id, ch_name FROM  jcw_content_hasline WHERE status = 1";
		return $this->db->query($sql);
	}

	public function get_datanum_mainnavigation_using_labelname($labelname){
		$sql = "SELECT main_nav_id FROM cp_main_navigation WHERE main_nav_desc = ? LIMIT 1";
		$data = array($labelname);
		return $this->db->query($sql, $data)->row()->main_nav_id;
	}

	public function get_userInformation($id){
		$sql = "SELECT * FROM sys_users
				WHERE id = ? LIMIT 1";
		$data = array($id);
		return $this->db->query($sql, $data);
	}

	public function get_sys_user_id($username){
		$sql = "SELECT * FROM sys_users
				WHERE username = ? AND active > 0 LIMIT 1";
		$data = array($username);
		$result = $this->db->query($sql, $data);
		return ($result->num_rows() > 0) ? $result->row()->id : 0;
	}

	public function get_sys_user($username){
		$sql = "SELECT * FROM sys_users
				WHERE username = ? AND active > 0 LIMIT 1";
		$data = array($username);

		$result = $this->db->query($sql, $data);

		return $result;
	}

	public function get_userInformation_md5($id){
		$sql = "SELECT * FROM sys_users
				WHERE md5(id) = ? LIMIT 1";
		$data = array($id);
		return $this->db->query($sql, $data);
	}

	// change_pass 092418

	public function check_pass_using_id_fk($id){
		$sql = "SELECT password FROM jcw_users WHERE user_id = ? LIMIT 1";
		$data = array($id);
		return $this->db->query($sql, $data);
	}

	public function update_password($secNewpass, $id){
		$sql = "UPDATE jcw_users SET password = ?, date_updated = ? WHERE user_id = ? ";
		$data = array($secNewpass, todaytime(), $id);
		$this->db->query($sql,$data);
	}

	// dynamic navigation functions
	public function get_modules($columns = '', $values = ''){
		$sql = "SELECT * FROM sys_modules WHERE  module_status = 1 ";
		if($columns && $values){
			if(is_array($columns)){
				foreach($columns as $key => $col){
					$sql.=" AND ";
					if(is_array($values[$key])){
						$sql.="`".$col."` IN ? ";
					}else{
						$sql.="`".$col."` = ? ";
					}
				}
				$args = $values;
			}else{
				$sql.=$columns." = ? ";
				$args = [$values];
			}
			$sql.=" ORDER BY arrangement ASC, module_name ASC ";
			return $this->db->query($sql, $args);
		}
		$sql.=" ORDER BY arrangement ASC, module_name ASC ";
		return $this->db->query($sql);
	}

	public function get_company_id($company_code){
		$sql = "SELECT company_id FROM pb_companies WHERE company_code = ?";

		$data = array($company_code);
		return $this->db->query($sql, $data)->row()->company_id;
	}

	public function get_issue_type(){
		$sql = "SELECT * FROM pb_desk_issue_type";

		return $this->db->query($sql);
	}

	public function get_company(){
		$sql = "SELECT company_code, company_name FROM pb_companies";

		return $this->db->query($sql);
	}

	public function insert_request($project_name ,$summary ,$description ,$filenames ,$email_address, $contact_person ,$contact_no ,$issue_type, $company_code, $user_id, $date_created) {
		// Gets the highest issue_type_no
			$sql = "SELECT (IFNULL(MAX(issue_type_no),0)+1) as issue_type_no FROM pb_request_form WHERE issue_type_id = ? AND company_code = ?";

			$data = array($issue_type, $company_code);
			$issue_type_no = $this->db->query($sql, $data)->row()->issue_type_no;
		// [END] Gets the highest issue_type_no

		// Inserting the form to db
			$sql = "INSERT INTO pb_request_form (issue_type_id, project_name, title, description, email_address, contact_person, contact_no, user_id, company_code, issue_type_no, status, date_created)
				VALUES (? ,? ,? ,? ,? ,? ,?, ?, ?, ?, ?, ?)";

			$data = array($issue_type, $project_name, $summary, $description, $email_address, $contact_person, $contact_no, $user_id, $company_code, $issue_type_no, 1, $date_created);
			$this->db->query($sql, $data);
		// [END] Inserting the form to db

		// Gets the id inserted into pb_request_form
			$id = $this->db->insert_id();
		// [END] Gets the id inserted into pb_request_form

		// Inserting the filenames to db
			$sql = "INSERT INTO pb_request_attachments (description, request_id, status)
				VALUES (? ,? ,?)";

			foreach($filenames as $row){
				$data = array($row, $id, 1);
				$this->db->query($sql, $data);
			}
		// [END] Inserting the filenames to db

			return $this->db->affected_rows();
	}

	public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query);
	}

	public function get_non_members(){
		$query	= "SELECT * FROM sys_users WHERE active > 0 AND id NOT IN (SELECT DISTINCT(sys_user) FROM app_members WHERE status = '1')";
		return $this->db->query($query);
	}

	public function get_cities($sys_shop = null) {
		if ($sys_shop == null) {
			$query="SELECT * FROM sys_delivery_areas WHERE status = 1";
			return $this->db->query($query);
		}else{
			$query = "SELECT * FROM `sys_delivery_areas` WHERE `status` = 1 AND id NOT IN (SELECT areaid FROM `sys_shop_shipping` WHERE `sys_shop` = ?)";
			$data = array($sys_shop);
			return $this->db->query($query, $data);
		}
	}

	public function getDashboardTable($fromdate,$todate,$shopid)
	{
		$data = [
			'total_sales' => $this->get_totalsales($fromdate,$todate,$shopid),
			'total_orderscount' => $this->total_orderscount($fromdate,$todate,$shopid),
			'visitors'         => $this->get_visitors($fromdate,$todate),
			'pageviews' => $this->get_pageviews($fromdate,$todate),
			'visitors_online' => $this->get_visitors_online($fromdate,$todate),
		];

        return $data;
	}

	public function get_totalsales($fromdate,$todate,$shopid)
	{
		if($shopid == 0)
		{
			$sql = "SELECT DATE_FORMAT(date_ordered, '%Y-%m-%d') AS trandate,
					SUM((CASE WHEN payment_status = 1 THEN total_amount else 0 END)) AS paid_amount,
					SUM((CASE WHEN payment_status IN (0,2) THEN total_amount else 0 END)) AS unpaid_amount,
	               	SUM(total_amount) AS total_amount
					FROM app_sales_order_details
					WHERE status = 1
						AND (date(date_ordered) BETWEEN ? AND ?)
	                GROUP By trandate
	            ";
			$data = array($fromdate,$todate);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
		else
		{
			$sql = "SELECT DATE_FORMAT(date_ordered, '%Y-%m-%d') AS trandate,
					SUM((CASE WHEN payment_status = 1 THEN total_amount else 0 END)) AS paid_amount,
					SUM((CASE WHEN payment_status IN (0,2) THEN total_amount else 0 END)) AS unpaid_amount,
	               	SUM(total_amount) AS total_amount
					FROM app_sales_order_details
					WHERE status = 1
						AND (date(date_ordered) BETWEEN ? AND ?)
						AND sys_shop=?
	                GROUP By trandate
	            ";
			$data = array($fromdate,$todate,$shopid);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
	}

	public function total_orderscount($fromdate,$todate,$shopid)
	{
		if($shopid == 0)
		{
			$sql = "SELECT DATE_FORMAT(date_ordered, '%Y-%m-%d') AS trandate,
					COUNT((CASE WHEN payment_status = 1 THEN id END)) AS paid_count,
					COUNT((CASE WHEN payment_status IN (0,2) THEN id END)) AS unpaid_count,
	               	COUNT(*) AS total_count
					FROM app_sales_order_details
					WHERE status = 1
						AND (date(date_ordered) BETWEEN ? AND ?)
	                GROUP By trandate
	            ";
			$data = array($fromdate,$todate);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
		else
		{
			$sql = "SELECT DATE_FORMAT(date_ordered, '%Y-%m-%d') AS trandate,
					COUNT((CASE WHEN payment_status = 1 THEN id END)) AS paid_count,
					COUNT((CASE WHEN payment_status IN (0,2) THEN id END)) AS unpaid_count,
	               	COUNT(*) AS total_count
					FROM app_sales_order_details
					WHERE status = 1
						AND (date(date_ordered) BETWEEN ? AND ?)
						AND sys_shop=?
	                GROUP By trandate
	            ";
			$data = array($fromdate,$todate,$shopid);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
	}

	public function get_visitors($fromdate,$todate)
	{

		$sql = "SELECT count(*) AS bilang, date(trandate) AS trandate
				FROM web_total_visitors
				WHERE date(trandate) BETWEEN ? AND ?
				GROUP BY date(trandate)";
		$data = array($fromdate,$todate);
		$res = $this->db->query($sql,$data);
		$r = $res->result_array();

		return $r;
	}

	public function get_pageviews($fromdate,$todate)
	{

		$sql = "SELECT count(*) AS bilang, date(trandate) AS trandate
				FROM web_pageviews
				WHERE date(trandate) BETWEEN ? AND ?
				GROUP BY date(trandate)";

		$data = array($fromdate,$todate);
		$res = $this->db->query($sql,$data);
		$r = $res->result_array();

		return $r;
	}

	public function get_visitors_online($fromdate,$todate)
	{
		$todaytime = todaytime();
		$today = today();

		$current_time=time();
		$timeout = $current_time - (60);

		$sql = "SELECT count(*) AS bilang
				FROM web_total_visitors
				WHERE (date(trandate) BETWEEN ? AND ?)
					AND timesess>=?";
		$data = array($today,$today,$timeout);
		$res = $this->db->query($sql,$data);
		$r = $res->result_array();

		return $r;
	}

	public function total_topitems($fromdate,$todate,$shopid)
	{
		if($shopid == 0)
		{
			$sql = "SELECT sum(ol.quantity) AS qty, ol.product_id AS pid, p.itemname AS itemname, p.otherinfo AS uom
					FROM app_sales_order_details od, app_sales_order_logs ol, sys_products p
					WHERE od.status = 1
						AND ol.status=1
						AND od.payment_status=1
						AND od.id=ol.order_id
						AND ol.product_id=p.id
						AND (date(od.date_ordered) BETWEEN ? AND ?)
	                GROUP By pid
	                ORDER BY qty DESC, itemname
	                LIMIT 10
	            ";
			$data = array($fromdate,$todate);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
		else
		{
			$sql = "SELECT sum(ol.quantity) AS qty, ol.product_id AS pid, p.itemname AS itemname,  p.otherinfo AS uom
					FROM app_sales_order_details od, app_sales_order_logs ol, sys_products p
					WHERE od.status = 1
						AND ol.status=1
						AND od.payment_status=1
						AND od.id=ol.order_id
						AND ol.product_id=p.id
						AND (date(od.date_ordered) BETWEEN ? AND ?)
						AND od.sys_shop=?
	                GROUP By pid
	                ORDER BY qty DESC, itemname
	                LIMIT 10
	            ";
			$data = array($fromdate,$todate,$shopid);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
	}

	public function total_overallsales($shopid)
	{
		if($shopid == 0)
		{
			$sql = "SELECT SUM(total_amount) AS total_amount
					FROM app_sales_order_details
					WHERE status = 1
	            ";

			$res = $this->db->query($sql);
			$r = $res->result_array();

			return $r;
		}
		else
		{
			$sql = "SELECT SUM(total_amount) AS total_amount
					FROM app_sales_order_details
					WHERE status = 1
					AND sys_shop=?
				";

			$data = array($shopid);
			$res = $this->db->query($sql,$data);
			$r = $res->result_array();

			return $r;
		}
	}

	public function averageOrderValue($fromdate,$todate){
		$sql=" SELECT date(date_ordered) as 'date_ordered', count(total_amount) as total_orders, SUM(total_amount) as 'total_amount' FROM app_sales_order_details WHERE status = 1 AND payment_status = '1' AND DATE(date_ordered) BETWEEN ? AND ? GROUP BY date(date_ordered) ORDER BY date(date_ordered) asc";
		$data = array($fromdate,$todate);
		$res = $this->db->query($sql,$data);
		$r = $res->result_array();

		return $r;
	}

	public function is_token_exist($generated_id){
		$sql = "SELECT COUNT(*) as count FROM sys_password_reset WHERE status = ? AND token = ?";
		$data = array(1, $generated_id);
		$count = $this->db->query($sql, $data)->row()->count;

        if($count > 0){
            $status = true;
        }else{
            $status = false;
        }

        return $status;
	}

	public function save_passres_token($token){
		$sql = "INSERT INTO sys_password_reset (token, expiration, status) VALUES (?, ?, ?)";
		$data = array($token, todaytime(), 1);
		$this->db->query($sql, $data);
	}

	public function validate_username_md5($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT *, u.id as sys_users_id, m.id as app_members_id, s.id as sys_shop_id
				FROM sys_users u
				LEFT JOIN app_members m
				ON u.id = m.sys_user
				LEFT JOIN sys_shops s
				ON m.sys_shop =  s.id
				WHERE md5(username) = ?
				AND m.status = 1
				LIMIT 1";

		$data = array($username);
		return $this->db->query($sql, $data);
	}

	public function validate_username_md5_2($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT *, u.id as sys_users_id, s.logo, s.shopcode, s.shopurl, s.shopname, m.id as app_members_id, s.id as sys_shop_id, s.status as shop_status, b.status as branch_status, u.first_login, u.username as acc_username
				FROM sys_users u
				LEFT JOIN app_members m
				ON u.id = m.sys_user
				LEFT JOIN sys_shops s
				ON m.sys_shop =  s.id
				LEFT JOIN sys_branch_profile b
				ON m.branchid =  b.id
				WHERE md5(username) = ?
				AND m.status = 1
				LIMIT 1";

		$data = array($username);
		return $this->db->query($sql, $data);
	}

	public function first_validate_username_md5($username){ // validate email if exist and get the info
		// $sql = "SELECT * FROM jcw_users WHERE company_code = ? AND username = ? LIMIT 1";
		$sql = "SELECT * FROM sys_users
				WHERE md5(username) = ?
				AND active = 1
				LIMIT 1";

		$data = array($username);
		return $this->db->query($sql, $data);
	}

	public function is_24hrsold($reset_token){
		$sql ="SELECT expiration FROM sys_password_reset WHERE token = ? AND status = ?";
		$data = array($reset_token, 1);
		$result = $this->db->query($sql, $data);
		$status = true;
		if($result->num_rows() > 0){
			$row = $result->row();
			if (strtotime(todaytime()) - strtotime($row->expiration) > 60*60*24) {
			   $status = true;//Older than 24hrs
			} else {
			   $status = false;//Newer than 24hrs
			}
		}else{
			$status = true;
		}
		return $status;
	}

	public function close_reset_token($reset_token){
		$sql = "UPDATE sys_password_reset SET status = ? WHERE token = ?";
		$data = array(0, $reset_token);
		$this->db->query($sql, $data);
	}

	public function get_NullDatesRecords()
	{
		$sales_sql = "SELECT DATE(payment_date) AS dates FROM `app_sales_order_details` use index(payment_date) WHERE payment_date >= NOW() - INTERVAL 3 MONTH GROUP BY DATE(payment_date)";
		$pview_sql = "SELECT DATE(trandate) AS dates FROM `web_pageviews` use index(trandate) WHERE trandate >= NOW() - INTERVAL 3 MONTH GROUP BY DATE(trandate)";
		$order_sql = "SELECT DATE(date_ordered) AS dates FROM `app_sales_order_details` use index(date_ordered) WHERE date_ordered >= NOW() - INTERVAL 3 MONTH GROUP BY DATE(date_ordered)";

		$sales_query = array_flatten($this->db->query($sales_sql)->result_array());
		$pview_query = array_flatten($this->db->query($pview_sql)->result_array());
		$order_query = array_flatten($this->db->query($order_sql)->result_array());

		$fromdate = date_sub(date_create(date('Y-m-d')), date_interval_create_from_date_string('3 Month'))->format("Y-m-d");
		$todate = date_add(date_create(date('Y-m-d')), date_interval_create_from_date_string('1 Day'))->format("Y-m-d");
		$period = new DatePeriod(
			new DateTime($fromdate),
			new DateInterval('P1D'),
			new DateTime($todate)
		);

		$arr_default = [
			'cur' => 0,
			'prev' => 0,
			'cnt' => 0
		];
		$sales = []; $views = []; $order = [];
		$s_m = $arr_default; $s_d = $arr_default; $s_fromdate = null; $s_todate = null;
		$v_m = $arr_default; $v_d = $arr_default; $v_fromdate = null; $v_todate = null;
		$o_m = $arr_default; $o_d = $arr_default; $o_fromdate = null; $o_todate = null;

		foreach ($period as $key => $value) {

			if (!in_array($value->format('Y-m-d'), $sales_query)) {
				$sales[] = $value->format('m/d/Y');
				if ($s_m['cnt']+1 == $value->format('m') && intval($value->format('d')) == 1) {
					$s_d['cnt'] = $value->format('d');
					$s_todate = $value->format('m/d/Y');
				} elseif ($s_d['cnt']+1 == intval($value->format('d'))) {
					$s_d['cnt']++;
					$s_todate = $value->format('m/d/Y');
				} else {
					if ($s_fromdate !== null && $s_todate !== null) {
						$sales[] = "$s_fromdate - $s_todate";
					}
					$s_fromdate = $value->format('m/d/Y');
					$s_m['prev'] = $value->format('m');
					$s_m['cnt'] = $value->format('m');

					$s_d['prev'] = $value->format('d');
					$s_d['cnt'] = $value->format('d');
					$s_todate = $value->format('m/d/Y');
				}
			}
			if (!in_array($value->format('Y-m-d'), $pview_query)) {
				$views[] = $value->format('m/d/Y');
				if ($v_m['cnt']+1 == $value->format('m') && intval($value->format('d')) == 1) {
					$v_d['cnt'] = $value->format('d');
					$v_todate = $value->format('m/d/Y');
				} elseif ($v_d['cnt']+1 == intval($value->format('d'))) {
					$v_d['cnt']++;
					$v_todate = $value->format('m/d/Y');
				} else {
					if ($v_fromdate !== null && $v_todate !== null) {
						$views[] = "$v_fromdate - $v_todate";
					}
					$v_fromdate = $value->format('m/d/Y');
					$v_m['prev'] = $value->format('m');
					$v_m['cnt'] = $value->format('m');

					$v_d['prev'] = $value->format('d');
					$v_d['cnt'] = $value->format('d');
					$v_todate = $value->format('m/d/Y');
				}
			}
			if (!in_array($value->format('Y-m-d'), $order_query)) {
				$order[] = $value->format('m/d/Y');
				if ($o_m['cnt']+1 == $value->format('m') && intval($value->format('d')) == 1) {
					$o_d['cnt'] = $value->format('d');
					$o_todate = $value->format('m/d/Y');
				} elseif ($o_d['cnt']+1 == intval($value->format('d'))) {
					$o_d['cnt']++;
					$o_todate = $value->format('m/d/Y');
				} else {
					if ($o_fromdate !== null && $o_todate !== null) {
						$order[] = "$o_fromdate - $o_todate";
					}
					$o_fromdate = $value->format('m/d/Y');
					$o_m['prev'] = $value->format('m');
					$o_m['cnt'] = $value->format('m');

					$o_d['prev'] = $value->format('d');
					$o_d['cnt'] = $value->format('d');
					$o_todate = $value->format('m/d/Y');
				}
			}
		}
		// get last instance
		$sales[] = "$s_fromdate - $s_todate";
		$views[] = "$v_fromdate - $v_todate";
		$order[] = "$o_fromdate - $o_todate";

		$arr = [
			'sales' => $sales,
			'views' => $views,
			'order' => $order,
		];

		return json_encode($arr);
	}

	public function get_NullDatesRecord()
	{
		$sales = []; $views = []; $order = [];
		$first_sale_date_sql = $this->db->query("SELECT DATE_FORMAT(payment_date - INTERVAL 1 DAY, '%m/%d/%Y') AS date FROM app_sales_order_details WHERE DATE(payment_date) IS NOT NULL ORDER BY payment_date LIMIT 1")->result_array();
		if (count($first_sale_date_sql) > 0) {
			$first_sale_date_sql = $first_sale_date_sql[0]['date'];
			$sales[] = "01/01/1900 - $first_sale_date_sql";
		}
		$first_views_date_sql = $this->db->query("SELECT DATE_FORMAT(trandate - INTERVAL 1 DAY, '%m/%d/%Y') AS date FROM web_pageviews WHERE DATE(trandate) IS NOT NULL ORDER BY trandate LIMIT 1")->result_array();
		if (count($first_views_date_sql) > 0) {
			$first_views_date_sql = $first_views_date_sql[0]['date'];
			$views[] = "01/01/1900 - $first_views_date_sql";
		}
		$first_order_date_sql = $this->db->query("SELECT DATE_FORMAT(date_ordered - INTERVAL 1 DAY, '%m/%d/%Y') AS date FROM app_sales_order_details WHERE DATE(date_ordered) IS NOT NULL ORDER BY date_ordered LIMIT 1")->result_array();
		if (count($first_order_date_sql) > 0) {
			$first_order_date_sql = $first_order_date_sql[0]['date'];
			$order[] = "01/01/1900 - $first_order_date_sql";
		}

		$arr = [
			'sales' => $sales,
			'views' => $views,
			'order' => $order,
		];

		return json_encode($arr);
	}

	public function addLoginAttempts($get_sys_user_id, $ip_address, $date_created){
		$sql = "SELECT * FROM sys_login_attempt WHERE user_id = ? AND ip_address = ? AND status = 1";
		$data = array($get_sys_user_id, $ip_address);
		$check_exist = $this->db->query($sql, $data)->row_array();

		if($get_sys_user_id != 0){
			if(!empty($check_exist)){
				$attempt = $check_exist['attempt'] + 1;

				$sql = "UPDATE sys_login_attempt SET attempt = ?, date_updated = ? WHERE user_id = ? AND ip_address = ?";
				$data = array($attempt, $date_created, $get_sys_user_id, $ip_address);
				$this->db->query($sql, $data);
			}
			else{
				$attempt = 1;
				$sql = "INSERT INTO sys_login_attempt (user_id, attempt, ip_address, date_created, status) VALUES (?, ?, ?, ?, ?)";
				$data = array($get_sys_user_id, $attempt, $ip_address, $date_created, 1);
				$this->db->query($sql, $data);
			}
		}

		return $attempt;
	}

	public function getLoginAttempts($ip_address){
		$sql = "SELECT * FROM sys_login_attempt WHERE ip_address = ? AND status = 1";
		$data = array($ip_address);
		$check_exist = $this->db->query($sql, $data)->row_array();

		if(!empty($check_exist['attempt'])){
			$attempt = $check_exist['attempt'];
		}
		else{
			$attempt = 0;
		}

		return $attempt;
	}

	public function resetLoginAttempts($user_id, $ip_address, $date_created){
		$sql = "SELECT * FROM sys_login_attempt WHERE user_id = ? AND ip_address = ? AND status = 1";
		$data = array($user_id, $ip_address);
		$check_exist = $this->db->query($sql, $data)->row_array();

		if(!empty($check_exist)){
			$attempt = 0;

			$sql = "UPDATE sys_login_attempt SET attempt = ?, isLoggedIn = 1, date_updated = ? WHERE user_id = ? AND ip_address = ?";
			$data = array($attempt, $date_created, $user_id, $ip_address);
			$this->db->query($sql, $data);
		}
		else{
			$attempt = 0;
			$sql = "INSERT INTO sys_login_attempt (user_id, attempt, ip_address, isLoggedIn, date_created, status) VALUES (?, ?, ?, ?, ?, ?)";
			$data = array($user_id, $attempt, $ip_address, 1, $date_created, 1);
			$this->db->query($sql, $data);
		}

		return $attempt;
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

	public function get_shop_logo(){
		$member_id = $this->session->userdata('sys_users_id');
		$sys_shop  = $this->get_sys_shop($member_id);

		if($sys_shop != ""){
			$sql=" SELECT logo FROM sys_shops WHERE id = ? AND status > 0";
			$sql = $this->db->query($sql, $sys_shop);

			if($sql->num_rows() > 0){
				return $sql->row()->logo;
			}else{
				return "";
			}
		}
		else{
			return "";
		}
	}

	public function setpassword($username, $password){

		$query="UPDATE sys_users SET password = ?, first_login = 0 WHERE username = ? AND active > 0 AND first_login = 1";
		$argument = array(
			password_hash($password,PASSWORD_BCRYPT,array('cost' => 12)),
			$username
		);

		return $this->db->query($query,$argument);
	}

	public function setcode($username){

		$query="UPDATE sys_users SET code_isset = 0, login_code = '' WHERE username = ? AND active > 0 AND code_isset = 1";
		$argument = array(
			$username
		);

		return $this->db->query($query,$argument);
	}

	public function getClientIP(){
        $return = '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
               $return = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
               $return = $_SERVER["REMOTE_ADDR"];
        }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
               $return = $_SERVER["HTTP_CLIENT_IP"];
        }

        $return = ($return != '') ? $return : '';
        $split  = explode(",",$return);
        $return = (!empty($split[1])) ? $split[0] : $return;

        return $return;
   }

	public function checkIPNotExist($user_id){
		$ip_address   = $this->getClientIP();
		$query="SELECT * FROM sys_login_attempt WHERE user_id = ? AND ip_address = ? AND isLoggedIn = 1";
		$argument = array(
			$user_id,
			$ip_address
		);

		return $this->db->query($query,$argument)->num_rows();
	}

	public function checkIfFirstLogin($user_id){
		$query="SELECT * FROM sys_login_attempt WHERE user_id = ? and isLoggedIn = 1";
		$argument = array(
			$user_id
		);

		return $this->db->query($query,$argument)->num_rows();
	}

	public function setLoginCode($id, $login_code){

		$query="UPDATE sys_users SET code_isset = 1, login_code = ? WHERE id = ?";
		$argument = array(
			$login_code,
			$id
		);

		return $this->db->query($query,$argument);
	}

	public function get_comrate_by_account_type($account_type){
		// $account_type = $this->db->escape($account_type);
		$sql = "SELECT a.itemid, a.itemname, a.unit, a.product_id, a.".$account_type." as discrate
			FROM 8_referralcom_rate a WHERE status = 1";
		return $this->db->query($sql);
	}

	public function get_shopname($shopid){
		$shopid = $this->db->escape($shopid);
		$sql = "SELECT shopname FROM sys_shops WHERE id = $shopid AND status = 1";
		return $this->db->query($sql)->row()->shopname;
	}

	public function get_branchname($branchid){
		$branchid = $this->db->escape($branchid);
		$sql = "SELECT branchname FROM sys_branch_profile WHERE id = $branchid AND status = 1";
		$branchname = 'Main';
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$branchname = $query->row()->branchname;
		}

		return $branchname;
	}

	public function checkIfFirstReset($email){
		$query="SELECT * FROM sys_users WHERE md5(username) = ? AND login_code = 1";
		$argument = array(
			$email
		);

		return $this->db->query($query,$argument);
	}

	public function get_all_shops(){
		$sql = "SELECT id, shopcode, shopname
			FROM sys_shops WHERE status = 1
			ORDER BY shopname ASC";
		return $this->db->query($sql)->result_array();
	}

	public function log_seller_time_activity($seller, $shop, $activity = 'in')
	{
		$date = date('Y-m-d H:i:s');
		if ($activity == 'out') {
			$sql = 'Update sys_users_activity SET out_time = ? WHERE sys_user_id = ? and sysshop = ?';
			$this->db->query($sql, [$date, $seller, $shop]);
			log_message('error', 'out');
			return ;
		}

		$sql = 'SELECT * FROM sys_users_activity where sys_user_id = ? and sysshop = ? LIMIT 1';
		$result = $this->db->query($sql, [$seller, $shop])->row_array();

		$response = '';
		if (! isset($result['id'])) {
			$sql = 'INSERT into sys_users_activity (in_time, sys_user_id, sysshop) VALUES (?, ?, ?)';
			$response = $this->db->query($sql, [$date, $seller, $shop]);
		} else {
			if (isset($result['id'])) {
				if ($activity == 'in') {
					$sql = 'Update sys_users_activity SET in_time = ? , out_time = ? WHERE sys_user_id = ? and sysshop = ?';
					$response = $this->db->query($sql, [$date, '', $seller, $shop]);
				}
			}
		}
		return $response;
	}
}
