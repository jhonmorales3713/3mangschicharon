<?php 
class Model_shopbranch extends CI_Model {
	public function shopbranch_list($filters, $requestData, $exportable = false){
        // storing  request (ie, get/post) global array to a variable
        $_record_status = $filters['_record_status'];
        $_mainshop = $filters['_mainshop'];
        $_branchname      = $filters['_branchname'];
        $_city      = $filters['_city'];
        $shopid         = $this->session->userdata('sys_shop_id');
        
        $columns = array( 
            // datatable column index  => database column name for sorting
            0 => 'shopname',
            1 => 'branchname',
            2 => 'email',
            3 => 'mobileno',
            4 => 'tagcity'
        );

        $sql = "SELECT COUNT(*) as count 
                FROM sys_branch_profile  
                WHERE status IN (0, 1, 2)";

        $query = $this->db->query($sql);
        $totalData = $query->row()->count;
        $totalFiltered = $totalData;

        $sql = "SELECT branch.*, branchms.mainshopid, shop.shopname, loc.citymunDesc as tagcity 
                FROM sys_branch_profile branch 
                LEFT JOIN sys_branch_mainshop branchms ON branch.id = branchms.branchid 
                LEFT JOIN sys_shops shop ON branchms.mainshopid = shop.id 
                LEFT JOIN sys_citymun loc ON branch.branch_city = loc.citymunCode 
                WHERE branch.status IN (0, 1, 2)";

        if($shopid > 0){
            $sql .=" AND branchms.mainshopid = " . $this->db->escape($shopid) . "";
        }

        if($_branchname != ""){
            $sql.=" AND branch.branchname LIKE '%".$this->db->escape_like_str($_branchname)."%'";
        }
        if($_mainshop != ""){
            $sql.=" AND branchms.mainshopid = '".$this->db->escape_str($_mainshop)."'";
        }
        if($_city != ""){
            $sql.=" AND branch.branch_city = '".$this->db->escape_str($_city)."'";
        }
        // start - for default search
        if ($_record_status == 1) {
            $sql.=" AND branch.status = " . $this->db->escape($_record_status) . "";
        }else if ($_record_status == 2){
            $sql.=" AND branch.status = " . $this->db->escape($_record_status) . "";
        }else{
            $sql.=" AND branch.status > 0 ";
        }
        // end - for default search
        $sql.=" GROUP BY branch.id";
        $query = $this->db->query($sql);
        $totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
        if (!$exportable) {
            $sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        }

        $query = $this->db->query($sql);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();
            
            $nestedData[] = strtoupper($row['shopname']);	
            $nestedData[] = strtoupper($row['branchname']);		
            $nestedData[] = $row['email'];
            $nestedData[] = $row['mobileno'];
            $nestedData[] = $row['tagcity'];
            if ($row['status'] == 1) {
                $record_status = 'Disable';
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

            if ($this->loginstate->get_access()['shop_branch']['update'] == 1 || $this->loginstate->get_access()['shop_branch']['view'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_edit" data-value="'.en_dec('en', $row['id']).'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                    <div class="dropdown-divider"></div>';
            }

            if ($this->loginstate->get_access()['shop_branch']['disable'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
                    <div class="dropdown-divider"></div>';
            }
            
            if ($this->loginstate->get_access()['shop_branch']['delete'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
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

    function get_all_city(){
    	$sql="SELECT a.*, b.provDesc FROM sys_citymun a 
              LEFT JOIN sys_prov b ON a.provCode = b.provCode AND a.status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

     function get_all_region(){
        $sql="SELECT * FROM sys_region WHERE status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }   

    public function get_allBranchesIn($branch_ids)
    {
        $sql="SELECT id, branchname, `status` FROM sys_branch_profile WHERE id IN ('$branch_ids')";
        return $this->db->query($sql)->result_array();
    }

    function get_all_shop(){
        $sql="SELECT * FROM sys_shops WHERE status = ? ORDER BY shopname ASC";
    	$data = array(1);
    	return $this->db->query($sql, $data);
    }

    function name_is_exist($name){
        $sql="SELECT COUNT(*) as count FROM sys_branch_profile WHERE UPPER(branchname) = ? AND status = ?";
    	$data = array(strtoupper($name), 1);
    	return $this->db->query($sql, $data)->row()->count;
    }

    function name_is_exist_edit($name, $idno){
        $sql="SELECT COUNT(*) as count FROM sys_branch_profile WHERE UPPER(branchname) = ? AND status = ?  AND id <> ?";
    	$data = array(strtoupper($name), 1, $idno);
    	return $this->db->query($sql, $data)->row()->count;
    }

    function save_shop_branch($mainshop, $branchname, $contactperson, $conno, $email, $address, $branch_city, $branch_region, $city, $province, $region, $isautoassign, $loc_latitude, $loc_longitude, $idnopb, $treshold){
        $sql="INSERT INTO sys_branch_profile (branchname, branchcode, contactperson, mobileno, email, address, branch_city, branch_region, city, province, region, isautoassign, `status`, latitude, `longitude`, `idnopb`,`inv_threshold`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data = array($branchname, $idnopb, $contactperson, $conno, $email, $address, $branch_city, $branch_region, $city, $province, $region, $isautoassign, 1,$loc_latitude, $loc_longitude, $idnopb, $treshold);
        $this->db->query($sql, $data);

        $branchid = $this->db->insert_id();
        
        // $DB_vouchers = $this->load->database('vouchers', TRUE);
        // $sql="INSERT INTO v_shops_branch (shopid, shopcode, branchid, branchname, branchaddr, branchcity, branchcountry, branchconno, branchemail, branchothers, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // $data = array($mainshop, $this->get_shopcode($mainshop), $branchid, $branchname, $address, $branch_city, 'Philippines', $conno, $email, '', 1);
        // $DB_vouchers->query($sql, $data);
        
        $sql="INSERT INTO sys_branch_mainshop (branchid, mainshopid, `status`) VALUES (?, ?, ?)";
        $data = array($branchid, $mainshop, 1);
        $this->db->query($sql, $data);

        return $branchid;
    }

    function get_record_details($idno){
        $sql="SELECT branch.*, branchms.mainshopid, bankdet.* 
              FROM sys_branch_profile branch LEFT JOIN sys_branch_mainshop branchms ON branch.id = branchms.branchid 
              LEFT JOIN sys_shop_account bankdet ON bankdet.branch_id = branch.id AND bankdet.sys_shop = branchms.mainshopid   
              WHERE branch.status > ? AND branch.id = ?";
    	$data = array(0, $idno);
    	return $this->db->query($sql, $data);
    }

    function update_shop_branch($idno, $mainshop, $branchname, $contactperson, $conno, $email, $address, $branch_city, $branch_region, $city, $province, $region, $isautoassign, $loc_latitude, $loc_longitude, $idnopb, $treshold){
    	$sql="UPDATE sys_branch_profile SET branchname = ?, branchcode = ?, contactperson = ?, mobileno = ?, email = ?, address = ?, branch_city = ?, branch_region = ?, city = ?, province = ?, region = ?, isautoassign = ?, date_updated = ?, latitude = ?, longitude = ?, idnopb = ?, inv_threshold = ?  WHERE `status` > ? AND id = ?";
    	$data = array($branchname, $idnopb, $contactperson, $conno, $email, $address, $branch_city, $branch_region, $city, $province, $region, $isautoassign, todaytime(), $loc_latitude, $loc_longitude, $idnopb, $treshold, 0, $idno);
        $this->db->query($sql, $data);

        // $DB_vouchers = $this->load->database('vouchers', TRUE);
        // $sql="UPDATE v_shops_branch SET shopid = ?, shopcode = ?, branchid = ?, branchname = ?, branchaddr = ?, branchcity = ?, branchcountry = ?, branchconno = ?, branchemail = ?, branchothers = ? WHERE `status` > ? AND branchid = ?";
        // $data = array($mainshop, $this->get_shopcode($mainshop), $idno, $branchname, $address, $branch_city, 'Philippines', $conno, $email, '', 0, $idno);
        // $DB_vouchers->query($sql, $data);
        
        $sql ="UPDATE sys_branch_mainshop SET mainshopid = ?, date_updated = ? WHERE `status` > ? AND branchid = ?";
        $data = array($mainshop, todaytime(), 0, $idno);
        $this->db->query($sql, $data);
    }

    function delete_modal_confirm($del_id){
    	$sql="UPDATE sys_branch_profile SET `status` = ?, date_updated = ? WHERE `status` > ? AND id = ?";
    	$data = array(0, todaytime(), 0, $del_id);
        $this->db->query($sql, $data);
        
        $sql="UPDATE sys_branch_mainshop SET `status` = ?, date_updated = ? WHERE `status` > ? AND branchid = ?";
    	$data = array(0, todaytime(), 0, $del_id);

        if ($this->db->query($sql, $data)) {
            return 1;
        }else{
            return 0;
        }
    }

    function get_all_branch($mainshopid){
        $sql="SELECT branch.branchname, branch.id, branchms.mainshopid, branchms.branchid 
        FROM sys_branch_profile branch LEFT JOIN sys_branch_mainshop branchms ON branch.id = branchms.branchid 
        WHERE branchms.mainshopid = ? AND branchms.status = ?";
        $data = array($mainshopid, 1);
        return $this->db->query($sql, $data);
    }

    function get_branch_details($orderid){
        $sql="SELECT a.branchid, a.orderid, b.branchname, d.shopname FROM sys_branch_orders a 
              LEFT JOIN sys_branch_profile b ON a.branchid =  b.id 
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid 
              LEFT JOIN sys_shops d ON c.mainshopid = d.id 
              WHERE a.status = ? AND a.orderid = ?";
        $data = array(1, $orderid);
        return $this->db->query($sql, $data);
    }

    function get_mainshopname($sys_shop){
        $sql="SELECT shopname FROM sys_shops WHERE status = ? AND id = ?";
        $data = array(1, $sys_shop);
        return $this->db->query($sql, $data);
    }

    function reassign_branch($branchid, $orderid, $remarks){
        if($this->check_if_exist_to_branch($orderid) > 0){
            $sql="UPDATE sys_branch_orders SET status = ?, remarks = ? WHERE status = ? AND orderid = ?";
            $data = array(0, $remarks, 1, $orderid);
            $this->db->query($sql, $data);  
        }
        if(!empty($branchid) || $branchid != ""){
            $sql="INSERT INTO sys_branch_orders (branchid, orderid, status) VALUES (?, ?, ?)";
            $data = array($branchid, $orderid, 1);
            $this->db->query($sql, $data);        
        }
    }

    function check_if_exist_to_branch($orderid){
        $sql="SELECT * FROM sys_branch_orders WHERE status = ? AND orderid = ?";
        $data = array(1, $orderid);
        return $this->db->query($sql, $data)->num_rows();  
    }

    function get_branchname($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname FROM sys_branch_orders a 
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id 
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db->query($sql, $data);

        if($result->num_rows() > 0){
            $branchname = $result->row()->branchname;
        }else{
            $branchname = 'Main';
        }
        return $branchname;
    }

    public function get_branchnameById($branchid)
    {
        $sql = "SELECT a.branchid, a.mainshopid, b.branchname, c.shopname
        FROM `sys_branch_mainshop` a
        LEFT JOIN `sys_branch_profile` b
           ON a.branchid = b.id
        LEFT JOIN `sys_shops` c
           ON a.mainshopid = c.id
        WHERE a.branchid = $branchid";
        return $this->db->query($sql);
    }

    function get_city_of_region($region){
        $sql="SELECT a.id, a.citymunDesc, a.citymunCode, a.regDesc FROM sys_citymun a 
              LEFT JOIN sys_region b ON a.regDesc = b.regCode WHERE a.regDesc = ? AND a.status = ?";
        $data = array($region, 1);
        return $this->db->query($sql, $data);
    }

    function get_all_province(){
        $sql="SELECT a.*, b.regDesc FROM sys_prov a
              LEFT JOIN sys_region b ON a.regCode = b.regCode WHERE a.status = ?";
        $data = array(1);
        return $this->db->query($sql, $data);
    }

    function disable_modal_confirm($disable_id, $record_status){
        $sql="UPDATE sys_branch_profile SET `status` = ?, date_updated = ? WHERE status > ? AND id = ?";
        $data = array($record_status, todaytime(), 0, $disable_id);
        $this->db->query($sql, $data);
        
        $sql="UPDATE sys_branch_mainshop SET `status` = ?, date_updated = ? WHERE status > ? AND branchid = ?";
        $data = array($record_status, todaytime(), 0, $disable_id);
        if ($this->db->query($sql, $data)) {
            return 1;
        }else{
            return 0;
        }
    }

    function get_shopcode($id){
        $sql="SELECT shopcode FROM sys_shops WHERE status = ? AND id = ?";
        $data = array(1, $id);
        return $this->db->query($sql, $data)->row()->shopcode;
    }

    function check_pendingorder($branchid){

        $shopid = $this->get_shopid($branchid);

        $sql ="SELECT b.id, b.date_ordered FROM sys_branch_orders AS a
        LEFT JOIN app_sales_order_details AS b ON a.orderid = b.reference_num AND b.sys_shop = ?
        WHERE a.branchid = ? AND b.order_status <> ? AND date(b.date_ordered) >= ? AND a.status > ? LIMIT 1";

        $data = array(
            $shopid,
            $branchid,
            's', 
            date('Y-m-d', strtotime("-30 days")),
            0
        );
        return $this->db->query($sql, $data);
    }

    function get_shopid($branchid){
        $sql ="SELECT mainshopid as sys_shop FROM sys_branch_mainshop WHERE branchid = ? AND status > ?";
        $data = array($branchid, 0);
        $result = $this->db->query($sql, $data);

        if($result->num_rows() > 0){
            $shopid = $result->row()->sys_shop;
        }else{
            $shopid = 0;
        }

        return $shopid;
    }

    function updateInventoryQty($branchid){
        $sql ="SELECT * FROM sys_products_invtrans_branch
        WHERE branchid = ? AND status = 1
        GROUP BY product_id";
        $data = array($branchid);
        $result = $this->db->query($sql, $data)->result_array();

        if(!empty($result)){
            foreach($result as $row){
                $sql = "SELECT SUM(no_of_stocks) as grand_total_no_of_stocks FROM sys_products_invtrans_branch WHERE product_id = ? AND status = 1";
            
                $bind_data = array(
                    $row['product_id']
                );
                $grand_total_no_of_stocks = $this->db->query($sql, $bind_data);

                $sql = "SELECT SUM(a.no_of_stocks) as deleted_stocks FROM sys_products_invtrans_branch AS a
                LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
                WHERE a.product_id = ? AND a.status = 1 AND b.status IN (0, 2)";

                $bind_data = array(
                    $row['product_id']
                );
                $deleted_stocks = $this->db->query($sql, $bind_data)->row()->deleted_stocks;

                $grand_total_no_of_stocks = $grand_total_no_of_stocks->row()->grand_total_no_of_stocks;
                $grand_total              = abs($grand_total_no_of_stocks - $deleted_stocks);

                $sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
                $bind_data = array(
                    $grand_total,
                    $row['product_id']
                );	
                $this->db->query($sql, $bind_data);
            }
        }
    }

    function get_refnumbybranch($branchid){
        $sql ="SELECT orderid FROM sys_branch_orders WHERE branchid = ? AND status = ?";
        $data = array($branchid, 1);
        $result = $this->db->query($sql, $data);

        if($result->num_rows() > 0){
            $orderid = $result->row()->orderid;
        }else{
            $orderid = "NORECORDFOUND";
        }

        return $orderid;
    }

    function idnopb_isvalid($idnopb){
        $sql ="SELECT idnopb FROM sys_branch_profile WHERE status > ? AND UCASE(idnopb) = ? AND idnopb <> '' ";
        $data = array(0, strtoupper($idnopb));
        $result =$this->db->query($sql, $data);

        $status = false;
        if($result->num_rows() > 0){
            $status = false;
        }else{
            $status = true;
        }
        return $status;
    }

    function idnopb_isvalid_edit($idnopb, $id){
        $sql ="SELECT idnopb FROM sys_branch_profile WHERE status > ? AND UCASE(idnopb) = ? AND id <> ? AND idnopb <> ?";
        $data = array(0, strtoupper($idnopb), $id, 0);
        $result =$this->db->query($sql, $data);

        $status = false;
        if($result->num_rows() > 0){
            $status = false;
        }else{
            $status = true;
        }
        return $status;
    }

    public function get_BranchesCount()
    {
        $sql = "SELECT COUNT(*) as `count` FROM sys_branch_profile";
        $result =$this->db->query($sql);
        return $result->result_array()[0]['count'];
    }

    public function get_branch_options($id = false) {
        $id = $this->db->escape($id);
        $query="SELECT * FROM sys_branch_mainshop a LEFT JOIN sys_branch_profile b ON b.id = a.branchid WHERE a.mainshopid = $id AND b.status = 1";
        return $this->db->query($query)->result_array();
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
}
