<?php 
class Model_csr extends CI_Model {
    public function customer_list(){
        // storing  request (ie, get/post) global array to a variable
        $search_val = $this->input->post('search_val');

        $requestData = $_REQUEST;
        
        $columns = array( 
            // datatable column index  => database column name for sorting
            
            0 => 'fullname',
            1 => 'email',
            2 => 'conno',
            3 => 'address1'
        );

        $sql = "SELECT COUNT(*) as count 
                FROM app_customers 
                WHERE status IN (0, 1, 2)";

        $query = $this->db->query($sql);
        $totalData = $query->row()->count;
        $totalFiltered = $totalData;

        $sql = "SELECT *, CONCAT(first_name, ' ', last_name) as fullname
                FROM app_customers 
                WHERE status IN (0, 1, 2)";
        if($search_val == ""){
            $sql.=" AND email  = 'TheDarkHorse' ";
        }
        if($search_val != ""){
            $sql.=" AND lower(CONCAT(first_name, ' ', last_name))  = '".$this->db->escape_str(strtolower($search_val))."'";
            $sql.=" OR lower(email) = '".$this->db->escape_str(strtolower($search_val))."'";
        }

        $query = $this->db->query($sql);
        $totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $query = $this->db->query($sql);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();
            
            $nestedData[] = strtoupper($row['fullname']);   
            $nestedData[] = $row['email'];     
            $nestedData[] = $row['conno'];
            $nestedData[] = $row['address1'];

            $nestedData[] = '<button class="btn btn-success btn-selectcustomer" data-value="'.en_dec('en',$row['id']).'" data-fullname="'.$row['fullname'].'">Select</button>';

            $data[] = $nestedData;
        }
        
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
        );
        return $json_data;
    }

    public function is_ticket_exist($ticketno){
        $sql = "SELECT COUNT(*) as count FROM csr_ticket WHERE ticket_refno = ? AND status = ?";
        $data = array($ticketno, 1);
        $count = $this->db->query($sql, $data)->row()->count;

        if($count > 0){
            $status = true;
        }else{
            $status = false;
        }

        return $status;
    }

    public function save_ticket($ticketrefno, $tickettype, $branchid, $shopid, $member_type){
        $sql ="INSERT INTO csr_ticket (ticket_refno, subject, issuecatid, created_by, ticket_type, branch_id, shop_id, ticket_status,status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if($member_type == 4){
            $data = array($ticketrefno, "", "", $this->session->userdata('sys_users_id'), $tickettype, $branchid, $shopid, 1, 1);
        }else{
            $data = array($ticketrefno, "", "", $this->session->userdata('sys_users_id'), $tickettype, $branchid, $shopid, 3, 1);
        }
        $this->db->query($sql, $data);
    }

    public function save_ticket_details($ticketrefno, $commentbox){
        $sql ="INSERT INTO csr_ticketdetails (ticket_refno, description, created_by, status) VALUES (?, ?, ?, ?)";
        $data = array($ticketrefno, $commentbox, $this->session->userdata('sys_users_id'), 1);
        $this->db->query($sql, $data);
    }

    public function update_ticket($idno, $subject, $issuecat, $ticket_type, $agentid, $priolevel){
        $sql ="UPDATE csr_ticket SET subject = ?, issuecatid = ?, ticket_type = ?, assignee = ?, priority_level = ?, date_updated = ? WHERE status > ? AND id = ?";
        $data = array($subject, $issuecat, $ticket_type, $agentid, $priolevel, todaytime(), 0, $idno);
        $this->db->query($sql, $data);
    }

    public function update_ticket_details($ticketrefno, $commentbox){
        $sql ="UPDATE csr_ticketdetails SET description = ?, date_updated = ? WHERE status > ? AND ticket_refno = ?";
        $data = array($commentbox, todaytime(), 0, $ticketrefno);
        $this->db->query($sql, $data);
    }   

	public function ticket_list(){
        // storing  request (ie, get/post) global array to a variable
        $exportable = false;
        $_record_status = $this->input->post('_record_status');
        $ticket_refno = $this->input->post('ticket_refno');
        $date_from      = $this->input->post('date_from');
        $date_to      = $this->input->post('date_to');
        $date_from = date_format(date_create($date_from),"Y-m-d");
        $date_to = date_format(date_create($date_to),"Y-m-d");
        $branchid = $this->session->userdata('branchid');
        $shopid = $this->session->userdata('sys_shop');
        $sys_users_id = $this->session->userdata('sys_users_id');
        $member_type = $this->get_membertype_id($sys_users_id)->row()->member_type;
        $requestData = $_REQUEST;
        
        $columns = array( 
            //datatable column index  => database column name for sorting

            0 => 'ticket_refno',
            1 => 'subject',
            2 => 'ticket_type',
            3 => 'issuecatid',
            4 => 'ticket_status'
        );
        
        $sql = "SELECT ticket.* 
                FROM csr_ticket ticket 
                WHERE ticket.status IN (0, 1, 2)";

        if($ticket_refno != ""){
            $sql.=" AND ticket.ticket_refno LIKE '%".$this->db->escape_like_str($ticket_refno)."%'";
        }
        if($date_from != ""){
            $sql.=" AND DATE(ticket.date_created) BETWEEN '".$this->db->escape_str($date_from)."' AND '".$this->db->escape_str($date_to)."'";
        }
        if($branchid != "" || !empty($branchid) AND $member_type != 4){
            $sql.=" AND branch_id = '".$this->db->escape_str(strtolower($branchid))."'";
        }
        if($shopid != "" || !empty($shopid) AND $shopid != 1 AND $member_type != 4){
            $sql.=" AND shop_id = '".$this->db->escape_str(strtolower($shopid))."'";
        }
        // start - for default search
        if ($_record_status == 1) {
            $sql.=" AND ticket.status = " . $this->db->escape($_record_status) . "";
        }else if ($_record_status == 2){
            $sql.=" AND ticket.status = " . $this->db->escape($_record_status) . "";
        }else if ($_record_status == 3){
            $sql.=" AND ticket.status = 1 AND ticket.ticket_status = " . $this->db->escape($_record_status) . "";
        }else{
            $sql.=" AND ticket.status > 0 ";
        }
        // end - for default search
        // /print_r($this->db->query($sql));die();
        //$query = $this->db->query($sql);

        $query = $this->db->query($sql);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        }            

        $query = $this->db->query($sql);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();

            $nestedData[] = strtoupper($row['ticket_refno']);
            
            $nestedData[] = strtoupper($row['subject']);    
            $nestedData[] = $this->get_ticket_type_description($row['ticket_type']);
            $nestedData[] = $this->get_subcategory_description($row['issuecatid']);
            if ($row['ticket_status'] == 1) {
                $nestedData[] = 'OPEN';
            }else if ($row['ticket_status'] == 2) {
                $nestedData[] = 'RESOLVED';
            }else if ($row['ticket_status'] == 3) {
                $nestedData[] = 'PENDING';
            }else{
                $nestedData[] = 'REJECTED';
            }                 
            if ($row['status'] == 1) {
                $record_status = 'Archive';
                $rec_icon = 'fa-ban';
            }else if ($row['status'] == 2) {
                $record_status = 'Enable';
                $rec_icon = 'fa-check-circle';
            }else{
                $record_status = 'Disable';
                $rec_icon = 'fa-ban';
            }

            $actions = '
            <div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            ';
            if ($this->loginstate->get_access()['ticket_history']['update'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_edit" data-value="'.en_dec('en', $row['id']).'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Manage</a>
                    <div class="dropdown-divider"></div>';
                if($row['ticket_status'] == 3 AND $member_type == 4){
                    $actions .= '
                        <a class="dropdown-item action_approve" data-value="'.en_dec('en', $row['id']).'" data-toggle="modal" data-target="#approve_modal"><i class="fa fa-check" aria-hidden="true"></i> Approve</a>
                        <div class="dropdown-divider"></div>';
                    $actions .= '
                        <a class="dropdown-item action_reject" data-value="'.en_dec('en', $row['id']).'" data-toggle="modal" data-target="#reject_modal"><i class="fa fa-check" aria-hidden="true"></i> Reject</a>
                        <div class="dropdown-divider"></div>';
                }

            }

            if ($this->loginstate->get_access()['ticket_history']['disable'] == 1 AND $member_type == 4) {
                $actions .= '
                    <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
                    <div class="dropdown-divider"></div>';
            }
            
            if ($this->loginstate->get_access()['ticket_history']['delete'] == 1) {
                // $actions .= '
                //     <a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
            }
            $actions .= '
                </div>
            </div>';
            
            $nestedData[] = $actions;

            $data[] = $nestedData;
        }
        
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
        );
        return $json_data;
    }

    function get_all_issuecat(){
        $sql="SELECT * FROM  csr_issuecategory WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }

    function get_all_maincat(){
        $sql="SELECT * FROM  csr_main_category WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }

    function delete_modal_confirm($del_id){
        $ticket_refno = $this->get_ticketrefno($del_id)->row()->ticket_refno;
    	$sql="UPDATE csr_ticket SET `status` = ?, date_updated = ? WHERE `status` = ? AND ticket_refno = ?";
    	$data = array(0, todaytime(), 1, $ticket_refno);
        $this->db->query($sql, $data);
        
        $sql="UPDATE csr_ticketdetails SET `status` = ?, date_updated = ? WHERE `status` = ? AND ticket_refno = ?";
    	$data = array(0, todaytime(), 1, $ticket_refno);

        if ($this->db->query($sql, $data)) {
            return 1;
        }else{
            return 0;
        }
    }

    function disable_modal_confirm($disable_id, $record_status){
        $ticket_refno = $this->get_ticketrefno($disable_id)->row()->ticket_refno;
        $sql="UPDATE csr_ticket SET `status` = ?, date_updated = ? WHERE status > ? AND ticket_refno = ?";
        $data = array($record_status, todaytime(), 0, $ticket_refno);
        $this->db->query($sql, $data);
        
        $sql="UPDATE csr_ticketdetails SET `status` = ?, date_updated = ? WHERE status > ? AND ticket_refno = ?";
        $data = array($record_status, todaytime(), 0, $ticket_refno);
        if ($this->db->query($sql, $data)) {
            return 1;
        }else{
            return 0;
        }
    }

    function get_ticket_details($id){
        $sql ="SELECT ticket.*, cat.description as category, ticket_det.description as commentbox, branch.branchname as branchname, shops.shopname as shopname, ttype.description as ticket_type_desc 
                FROM csr_ticket ticket 
                LEFT JOIN csr_issuecategory cat ON ticket.issuecatid = cat.id 
                LEFT JOIN csr_ticketdetails ticket_det ON ticket.ticket_refno = ticket_det.ticket_refno 
                LEFT JOIN sys_branch_profile branch ON ticket.branch_id = branch.id 
                LEFT JOIN sys_shops shops ON ticket.shop_id = shops.id 
                LEFT JOIN csr_main_category ttype ON ticket.ticket_type = ttype.id 
                WHERE ticket.status > ? AND ticket.id = ?";
        $data = array(0, $id);
        return $this->db->query($sql, $data);
    }

    function get_ticketrefno($idno){
        $sql = "SELECT ticket_refno FROM csr_ticket WHERE status > ? AND id = ?";
        $data = array(0, $idno);
        return $this->db->query($sql, $data);
    }

    function get_log_details($id){
        $sql ="SELECT ticket.ticket_refno, ticket_det.*, user.username  as representative, user.avatar, CONCAT(member.lname, ', ', member.fname, ' ', member.mname) as csrname, memtype.type as membertype 
                FROM csr_ticket ticket LEFT JOIN csr_ticketdetails ticket_det ON ticket.ticket_refno = ticket_det.ticket_refno 
                LEFT JOIN sys_users user ON ticket_det.created_by = user.id  
                LEFT JOIN app_members member ON user.id = member.sys_user 
                LEFT JOIN app_member_type memtype ON member.member_type = memtype.id  
                WHERE ticket.status > ? AND ticket_det.status > ? AND ticket.id = ? 
                ORDER BY ticket_det.id DESC";
        $data = array(0 ,0, $id);
        return $this->db->query($sql, $data);
    }

    function close_ticket($id){
        $sql="UPDATE csr_ticket SET `ticket_status` = ?, date_updated = ? WHERE `status` = ? AND id = ?";
        $data = array(2, todaytime(), 1, $id);
        $this->db->query($sql, $data);
    }
    
    function reopen_ticket($id){
        $sql="UPDATE csr_ticket SET `ticket_status` = ?, date_updated = ? WHERE `status` = ? AND id = ?";
        $data = array(1, todaytime(), 1, $id);
        $this->db->query($sql, $data);
    }

    function get_id_byticketno($ticketrefno){
        $sql = "SELECT id FROM csr_ticket WHERE status > ? AND ticket_refno = ?";
        $data = array(0, $ticketrefno);
        return $this->db->query($sql, $data);
    }

    function get_orderrefbobyid($id){
        $sql = "SELECT order_refno FROM csr_ticket WHERE status > ? AND id = ?";
        $data = array(0, $id);
        return $this->db->query($sql, $data);
    }

    function get_membertype_id($sys_users_id){
        $sql ="SELECT * FROM app_members WHERE sys_user = ? AND status > ?";
        $data = array($sys_users_id, 0);
        return $this->db->query($sql, $data);
    }

    function approve_ticket($approve_id){
        $sql="UPDATE csr_ticket SET `ticket_status` = ?, date_updated = ? WHERE `status` = ? AND id = ?";
        $data = array(1, todaytime(), 1, $approve_id);
        $this->db->query($sql, $data);
    }

    function reject_ticket($reject_id){
        $sql="UPDATE csr_ticket SET `ticket_status` = ?, date_updated = ? WHERE `status` = ? AND id = ?";
        $data = array(4, todaytime(), 1, $reject_id);
        $this->db->query($sql, $data);
    }

    function get_shopname($id){
        $sql = "SELECT shopname FROM sys_shops WHERE status > ? AND id = ?";
        $data = array(0, $id);
        $query = $this->db->query($sql, $data);
        if($query->num_rows() > 0){
            $result = $query->row()->shopname;
        }else{
            $result = "";
        }
        return $result;
    }

    function get_branchname($id){
        $sql = "SELECT branchname FROM sys_branch_profile WHERE status > ? AND id = ?";
        $data = array(0, $id);
        $query = $this->db->query($sql, $data);
        if($query->num_rows() > 0){
            $result = $query->row()->branchname;
        }else{
            $result = "";
        }
        return $result;
    }

    function get_subcategory_description($id){
        $sql = "SELECT description FROM csr_issuecategory WHERE status > ? AND id = ?";
        $data = array(0, $id);
        $query = $this->db->query($sql, $data);
        if($query->num_rows() > 0){
            $result = $query->row()->description;
        }else{
            $result = "";
        }
        return $result;
    }

    function agent_list(){
        $sql ="SELECT user.id, user.username as username, CONCAT(appmem.lname, ' ', appmem.fname, ' ', appmem.mname, ' - ', user.username) as description 
               FROM sys_users user LEFT JOIN app_members appmem ON user.id = appmem.sys_user 
               WHERE user.active > ? AND appmem.status > ? AND appmem.sys_shop = ? AND appmem.branchid = ? AND appmem.member_type = ?";
        $data = array(0, 0, 0, 0, 4);
        return $this->db->query($sql, $data);
    }

    function get_memberdetails($id){
        $sql ="SELECT user.username, user.avatar, shop.shopname, branch.branchname, member.fname, member.mname, member.lname, member.mobile_number 
               FROM sys_users user 
               LEFT JOIN app_members member ON user.id = member.sys_user 
               LEFT JOIN sys_shops shop ON member.sys_shop = shop.id 
               LEFT JOIN sys_branch_profile branch ON member.branchid = branch.id 
               WHERE user.id = ? AND user.active > ? AND member.member_type <> ?";
        $data = array($id, 0, 4);
        return $this->db->query($sql, $data);
    }

    function get_customerdetails($id){
        $sql ="SELECT * FROM app_customers  
               WHERE id = ? AND status = ?";
        $data = array($id, 1);
        return $this->db->query($sql, $data);
    }

    function get_all_branch(){
        $sql ="SELECT * FROM sys_branch_profile  
               WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }

    function get_all_mainshop(){
        $sql ="SELECT * FROM sys_shops  
               WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }

    function validate_shop($email){
        $sql ="SELECT * FROM sys_shops WHERE status > ? AND email = ?";
        $data = array(0, $email);
        return $this->db->query($sql, $data);
    }

    function validate_branch($email){
        $sql ="SELECT * FROM sys_branch_profile WHERE status > ? AND email = ?";
        $data = array(0, $email);
        return $this->db->query($sql, $data);
    }

    function get_accountdetails($id){
        $sql ="SELECT mem.*, branch.branchname, shop.shopname 
               FROM app_members mem 
               LEFT JOIN sys_branch_profile branch ON mem.branchid = branch.id 
               LEFT JOIN sys_shops shop ON mem.sys_shop = shop.id 
               WHERE mem.id = ? AND mem.status = ?";
        $data = array($id, 1);
        return $this->db->query($sql, $data);
    }

    public function account_list(){
        // storing  request (ie, get/post) global array to a variable
        $search_val = $this->input->post('search_val');

        $requestData = $_REQUEST;
        
        $columns = array( 
            // datatable column index  => database column name for sorting
            0 => 'fullname',
            1 => 'email',
            2 => 'mobile_number'
        );

        $sql = "SELECT COUNT(*) as count 
                FROM app_members 
                WHERE status IN (0, 1, 2)";

        $query = $this->db->query($sql);
        $totalData = $query->row()->count;
        $totalFiltered = $totalData;

        $sql = "SELECT *, CONCAT(fname, ' ', mname, ' ', lname) as fullname
                FROM app_members 
                WHERE status IN (0, 1, 2)";
        if($search_val == ""){
            $sql.=" AND email  = 'TheDarkHorse' ";
        }
        if($search_val != ""){
            $sql.=" AND lower(CONCAT(fname, ' ', mname, ' ', lname))  LIKE '%".$this->db->escape_like_str(strtolower($search_val))."%'";
            $sql.=" OR lower(email) = '".$this->db->escape_str(strtolower($search_val))."'";
        }

        $query = $this->db->query($sql);
        $totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $query = $this->db->query($sql);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();
            
            $nestedData[] = strtoupper($row['fullname']);   
            $nestedData[] = $row['email'];     
            $nestedData[] = $row['mobile_number'];

            $nestedData[] = '<button class="btn btn-success btn-selectaccount" data-value="'.en_dec('en',$row['id']).'" data-fullname="'.$row['fullname'].'">Select</button>';

            $data[] = $nestedData;
        }
        
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
        );
        return $json_data;
    }

    function get_all_ticket_type(){
        $sql="SELECT * FROM  csr_main_category WHERE status > ?";
        $data = array(0);
        return $this->db->query($sql, $data);
    }

    function get_ticket_type_description($id){
        $sql = "SELECT description FROM csr_main_category WHERE status > ? AND id = ?";
        $data = array(0, $id);
        $query = $this->db->query($sql, $data);
        if($query->num_rows() > 0){
            $result = $query->row()->description;
        }else{
            $result = "";
        }
        return $result;
    }

    public function getByRefNum($refnum){
        $this->db->select("uo.*, uo.user_id as idno, uo.total_amount as order_total_amt, uo.name as fullname");
        $this->db->where("uo.reference_num", $refnum);
        return $this->db->get("app_order_details uo")->row_array();
    }

    public function getOrderDetails($start, $length, $orderId){
        $this->db->select("ord.order_id, ur.order_so_no, ord.product_id, ord.quantity, ord.amount, ord.total_amount, ur.order_status, ur.total_amount as order_total_amt, ur.date_ordered, ur.payment_date, ord.sys_shop");
        $this->db->select("ur.reference_num,ur.paypanda_ref,ur.date_received");
        $this->db->select("prod.itemname,prod.otherinfo");
        $this->db->from("app_order_logs ord");
        $this->db->join("app_order_details ur", "ur.order_id = ord.order_id", "left");
        $this->db->join("sys_products prod", "prod.Id = ord.product_id", "left");
        $this->db->where("ord.order_id", $orderId);
        if($start != null && $length != null){
            $this->db->limit($length,$start);
        }
        return $this->db->get();
    }

}
