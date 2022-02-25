<?php 
class Model_referral_comrate extends CI_Model {
	public function referral_comrate_list($filters, $requestData, $exportable = false){
        // storing  request (ie, get/post) global array to a variable
        
        //comment this because its not needed 081821 - josh
        // $_record_status = sanitize($filters['_record_status']);
        // $_itemid = sanitize($filters['_itemid']);
        // $_itemname      = sanitize($filters['_itemname']);
        
        // $DB_vouchers = $this->load->database('vouchers', TRUE);

        // $columns = array( 
        //     // datatable column index  => database column name for sorting
        //     0 => 'itemid',
        //     1 => 'itemname',
        //     2 => 'unit',
        //     3 => 'startup',
        //     4 => 'jc',
        //     5 => 'mcjr',
        //     6 => 'mc',
        //     7 => 'mcsuper',
        //     8 => 'mcmega',
        //     9 => 'others'
        // );

        // $sql = "SELECT COUNT(*) as count 
        //         FROM 8_referralcom_rate   
        //         WHERE status IN (0, 1, 2) AND instance_id = 'toktokmall'";

        // $query = $DB_vouchers->query($sql);
        // $totalData = $query->row()->count;
        // $totalFiltered = $totalData;

        // $sql = "SELECT * 
        //         FROM 8_referralcom_rate   
        //         WHERE status IN (0, 1, 2) AND instance_id = 'toktokmall'";

        // if($_itemid != ""){
        //     $sql.=" AND itemid LIKE '%".$_itemid."%'";
        // }
        // if($_itemname != ""){
        //     $sql.=" AND itemname LIKE '%".$this->db->escape_like_str($_itemname)."%'";
        // }
        // // start - for default search
        // if ($_record_status == 1) {
        //     $sql.=" AND status = " . $this->db->escape($_record_status) . "";
        // }else if ($_record_status == 2){
        //     $sql.=" AND status = " . $this->db->escape($_record_status) . "";
        // }else{
        //     $sql.=" AND status > 0 ";
        // }
        // // end - for default search

        // $query = $DB_vouchers->query($sql);

        // $totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        // $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
        // if (!$exportable) {
        //     $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        // }

        // $query = $DB_vouchers->query($sql);

        // $data = array();
        // foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
        //     $nestedData=array();
            
        //     $nestedData[] = strtoupper($row['itemid']);	
        //     $nestedData[] = strtoupper($row['itemname']);		
        //     $nestedData[] = $row['unit'];
        //     $nestedData[] = $row['startup'];
        //     $nestedData[] = $row['jc'];
        //     $nestedData[] = $row['mcjr'];
        //     $nestedData[] = $row['mc'];
        //     $nestedData[] = $row['mcsuper'];
        //     $nestedData[] = $row['mcmega'];
        //     $nestedData[] = $row['others'];
        //     if ($row['status'] == 1) {
        //         $record_status = 'Disable';
        //         $rec_icon = 'fa-ban';
        //     }else if ($row['status'] == 2) {
        //         $record_status = 'Enable';
        //         $rec_icon = 'fa-check-circle';
        //     }else{
        //         $record_status = 'Disable';
        //         $rec_icon = 'fa-ban';
        //     }

        //     $actions = '
        //     <div class="dropdown">
        //         <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
        //         <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
        //     ';

        //     if ($this->loginstate->get_access()['shop_branch']['update'] == 1 || $this->loginstate->get_access()['shop_branch']['view'] == 1) {
        //         $actions .= '
        //             <a class="dropdown-item action_edit" data-value="'.en_dec('en', $row['id']).'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
        //             <div class="dropdown-divider"></div>';
        //     }
        //     if ($this->loginstate->get_access()['shop_branch']['disable'] == 1) {
        //         $actions .= '
        //             <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
        //             <div class="dropdown-divider"></div>';
        //     }
        //     if ($this->loginstate->get_access()['shop_branch']['delete'] == 1) {
        //         $actions .= '
        //             <a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
        //     }
        //     $nestedData[] = $actions;
        //     $data[] = $nestedData;
        // }
        
        // $json_data = array(
        //     "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
        //     "recordsTotal"    => intval( $totalData ),  // total number of records
        //     "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
        //     "data"            => $data,   // total data array
        // );
        // return $json_data;
    }

    public function get_product_byshop($mainshop){
        $sql="SELECT * FROM sys_products WHERE sys_shop = ? AND enabled = ?";
        $data = array($mainshop, 1);
        return $this->db->query($sql, $data);
    }

    public function save_refcomrate($mainshop, $product, $startup, $jc, $mcjr, $mc, $mcsuper, $mcmega, $others){
        //comment this because its not needed 081821 - josh

        // $itemid = $this->get_shopcode($mainshop).'_'.$this->get_item_id($product);
        // $itemname = $this->get_item_name($product);
        // $uom = $this->get_item_uom($product);

        // $DB_vouchers = $this->load->database('vouchers', TRUE);
        
        // $sql="INSERT INTO 8_referralcom_rate (itemid, itemname, unit, product_id, startup, jc, mcjr, mc, mcsuper, mcmega, others, status, instance_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // $data = array($itemid, $itemname, $uom, $product, $startup, $jc, $mcjr, $mc, $mcsuper, $mcmega, $others, 1, 'toktokmall');
        // $DB_vouchers->query($sql, $data);
    }

    public function get_item_id($productid){
        $sql="SELECT itemid FROM sys_products WHERE Id = ? AND enabled = ?";
        $data = array($productid, 1);
        $result = $this->db->query($sql, $data);
        
        if(!empty($result->row()->itemid)){
            $itemid = $result->row()->itemid;
        }else{
            $itemid = 0;
        }

        return $itemid;
    }

    public function get_item_name($productid){
        $sql="SELECT itemname FROM sys_products WHERE Id = ? AND enabled = ?";
        $data = array($productid, 1);
        $result = $this->db->query($sql, $data);
        
        if(!empty($result->row()->itemname)){
            $itemname = $result->row()->itemname;
        }else{
            $itemname = 'N/A';
        }

        return $itemname;
    }

    public function get_item_uom($productid){
        $sql="SELECT b.description 
              FROM sys_products a LEFT JOIN sys_uom b ON a.uom = b.id 
              WHERE a.Id = ? AND a.enabled = ?";
        $data = array($productid, 1);
        $result = $this->db->query($sql, $data);
        
        if(!empty($result->row()->description)){
            $uom = $result->row()->description;
        }else{
            $uom = 'N/A';
        }

        return $uom;
    }

    public function get_shopcode($mainshopid){
        $sql="SELECT shopcode FROM sys_shops WHERE id = ? AND status = ?";
        $data = array($mainshopid, 1);
        $result = $this->db->query($sql, $data);
        
        if(!empty($result->row()->shopcode)){
            $shopcode = $result->row()->shopcode;
        }else{
            $shopcode = 'N/A';
        }

        return $shopcode;
    }

    public function productid_isexist($productid){
        //comment this because its not needed 081821 - josh

        // $DB_vouchers = $this->load->database('vouchers', TRUE);
        // $sql ="SELECT COUNT(*) as count FROM 8_referralcom_rate WHERE product_id = ? AND status IN (0, 1, 2) AND instance_id = 'toktokmall'";
        // $data = array($productid);
        // return $DB_vouchers->query($sql, $data);
    }

    public function productid_isexist_edit($productid, $idno){
        //comment this because its not needed 081821 - josh

        // $DB_vouchers = $this->load->database('vouchers', TRUE);
        // $sql ="SELECT COUNT(*) as count FROM 8_referralcom_rate WHERE product_id = ? AND status IN (0, 1, 2) AND id <> ? AND instance_id = 'toktokmall'";
        // $data = array($productid, $idno);
        // return $DB_vouchers->query($sql, $data);
    }

    function delete_modal_confirm($del_id){
        //comment this because its not needed 081821 - josh

        // $DB_vouchers = $this->load->database('vouchers', TRUE);

        // $sql="UPDATE 8_referralcom_rate SET `status` = ? WHERE `status` = ? AND id = ?";
        // $data = array(0, 1, $del_id);
        // $DB_vouchers->query($sql, $data);

        // if ($DB_vouchers->query($sql, $data)) {
        //     return 1;
        // }else{
        //     return 0;
        // }
    }

    function disable_modal_confirm($disable_id, $record_status){
        //comment this because its not needed 081821 - josh

        // $DB_vouchers = $this->load->database('vouchers', TRUE);

        // $sql="UPDATE 8_referralcom_rate SET `status` = ? WHERE status > ? AND id = ?";
        // $data = array($record_status, 0, $disable_id);
        // $DB_vouchers->query($sql, $data);

        // if ($DB_vouchers->query($sql, $data)) {
        //     return 1;
        // }else{
        //     return 0;
        // }
    }

    function get_record_details($id){
        //comment this because its not needed 081821 - josh

        // $DB_vouchers = $this->load->database('vouchers', TRUE);

        // $sql ="SELECT * FROM 8_referralcom_rate WHERE status = ? AND id = ?";
        // $data = array(1, $id);
        // return $DB_vouchers->query($sql, $data);
    }

    function update_refcomrate($idno, $mainshop, $product, $startup, $jc, $mcjr, $mc, $mcsuper, $mcmega, $others){
        //comment this because its not needed 081821 - josh
        
        // $itemid = $this->get_shopcode($mainshop).'_'.$this->get_item_id($product);
        // $itemname = $this->get_item_name($product);
        // $uom = $this->get_item_uom($product);
        
        // $DB_vouchers = $this->load->database('vouchers', TRUE);

        // $sql="UPDATE 8_referralcom_rate SET itemid = ?, itemname = ?, unit = ?, product_id = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE status > ? AND id = ?";
        // $data = array($itemid, $itemname, $uom, $product, $startup, $jc, $mcjr, $mc, $mcsuper, $mcmega, $others, 0, $idno);
        // $DB_vouchers->query($sql, $data);
    }
}
