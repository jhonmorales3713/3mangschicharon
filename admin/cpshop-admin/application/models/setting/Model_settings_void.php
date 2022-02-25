<?php

class Model_settings_void extends CI_Model {

    public $app_db;

    public function getRelated($recordType, $refno) {
        
        if ($recordType == "Package Sales") {

            $membermain = $this->check_membermain_if_exist($refno);
            if($membermain->num_rows() == 0){
                $sql = "SELECT DISTINCT soldto as name, idno as idno, packageamount as packageamount, date as date FROM 8_memberpayments WHERE status >= 1 AND idno = ? ";
                $data = array($refno);
                $query = $this->app_db->query($sql, $data)->row();
                $checker = 1;
            }else{
                $checker = 0;
            }

        }elseif ($recordType == "Check") {

            $sql = "SELECT idno FROM 8_checklist WHERE status >= 1 AND chkno = ? ";
            $data = array($refno);
            $get_id = $this->app_db->query($sql, $data);

            if($get_id->num_rows() != ""){
                $get_name = check_recipient($refno, $get_id->row()->idno);

                $sql = "SELECT '".$get_name."' as name, idno as idno, amount as amount, chkdate as date, chkno as chkno FROM 8_checklist WHERE status >= 1 AND chkno = ? ";
                $data = array($refno);
                $query = $this->app_db->query($sql, $data)->row();
                $checker = 1;
            }else{
                $checker=0;
            }

        }elseif ($recordType == "Cash Voucher") {

            $sql = "SELECT payto as name, tranamt as amount, trandate as date, cvno as cvno FROM 8_cashvoucher WHERE status >= 1 AND cvno = ? ";
            $data = array($refno);
            $query = $this->app_db->query($sql, $data)->row();
            $checker = 1;

        }elseif ($recordType == "Customer Ticket") {

            $sql = "SELECT idno FROM 8_ticket WHERE status >= 1 AND id = ? ";
            $data = array($refno);
            $get_id = $this->app_db->query($sql, $data);

            if($get_id->num_rows() != ""){

            $fullname = $this->Model_distributor_cmj->getDistributor_fullname($get_id->row()->idno);

                $sql = "SELECT '".$fullname."' as name, idno as idno, id as ticketno, trandate as date, ticketdetails as details FROM 8_ticket WHERE status >= 1 AND id = ? ";
                $data = array($refno);
                $query = $this->app_db->query($sql, $data)->row();
                $checker = 1;
            }else{
                $checker=0;
            }
        }
        elseif ($recordType == "Accounts Payable Voucher") { 

            $sql = "SELECT apvno, supplierid FROM 8_apvsummary WHERE status >= 1 AND apvno = ? AND apvstatus IN ('Waiting for Approval', 'Approved')";
                $data = array($refno);
                $get_id = $this->app_db->query($sql, $data);

                if($get_id->num_rows() != ""){

                    $fullname = $this->Model_settings_void->get_supplier_name($get_id->row()->supplierid);

                    $sql = "SELECT '".$fullname."' as name, apvno as apvno, trandate as date, amount as amount FROM 8_apvsummary WHERE status >= 1 AND apvno = ? AND apvstatus IN ('Waiting for Approval', 'Approved')";
                    $data = array($refno);
                    $query = $this->app_db->query($sql, $data)->row();
                    $checker = 1;
                }else{
                    $checker=0;
                }
        }elseif ($recordType == "Receive Purchase Order") { 

            $sql = "SELECT rcvno, pono, supid FROM 8_invrcvsummary WHERE status >= 1 AND rcvno = ? AND rcvstatus IN ('Waiting for Approval', 'Approved')";
                $data = array($refno);
                $get_id = $this->app_db->query($sql, $data);

                if($get_id->num_rows() != ""){

                    $fullname = $this->Model_settings_void->get_supplier_name($get_id->row()->supid);
                    //$amount = $this->Model_settings_void->get_purchase_amount($get_id->row()->pono);

                    $sql = "SELECT '".$fullname."' as name, rcvno as rcvno, trandate as date FROM 8_invrcvsummary WHERE status >= 1 AND rcvno = ? AND rcvstatus IN ('Waiting for Approval', 'Approved')";
                    $data = array($refno);
                    $query = $this->app_db->query($sql, $data)->row();
                    $checker = 1;
                }else{
                    $checker=2;
                }

        }elseif ($recordType == "Purchase Order") { 

            $sql = "SELECT pono, supid FROM 8_purchasessummary WHERE status >= 1 AND pono = ? AND rcvall = 'No Delivery'";
                $data = array($refno);
                $get_id = $this->app_db->query($sql, $data);

                if($get_id->num_rows() != ""){

                    $fullname = $this->Model_settings_void->get_supplier_name($get_id->row()->supid);

                    $sql = "SELECT '".$fullname."' as name, pono as pono, trandate as date, totalamt as amount FROM 8_purchasessummary WHERE status >= 1 AND pono = ? AND rcvall = 'No Delivery'";
                    $data = array($refno);
                    $query = $this->app_db->query($sql, $data)->row();
                    $checker = 1;
                }else{
                    $checker=2;
                }
        }
        elseif ($recordType == "Bank Deposit") {

            $sql = "SELECT accountin FROM 8_deposits WHERE status >= 1 AND depno = ?";
                $data = array($refno);
                $get_id = $this->app_db->query($sql, $data);
        
                if($get_id->num_rows() != ""){
        
                    $account = $this->get_account($get_id->row()->accountin)->row()->description;
                    $sql = "SELECT '".$account."' as name, deptype as deptype, depamount as amount, depdate as date, depno as depno FROM 8_deposits WHERE status >= 1 AND depno = ? ";
                    $data = array($refno);
                    $query = $this->app_db->query($sql, $data)->row();
                    $checker = 1;
        
                }else{
                    $checker=2;
                }
        
        }elseif ($recordType == "GL Transaction") {
        
            $sql = "SELECT idno FROM 8_begbalances WHERE status >= 1 AND id = ?";
            $data = array($refno);
            $get_id = $this->app_db->query($sql, $data);
        
            if($get_id->num_rows() != ""){
        
            $fullname = $this->Model_distributor_cmj->getDistributor_fullname($get_id->row()->idno);
        
                $sql = "SELECT '".$fullname."' as name, trandate as date, id as glno, tamount as amount FROM 8_begbalances WHERE status >= 1 AND id = ? ";
                $data = array($refno);
                $query = $this->app_db->query($sql, $data)->row();
                $checker = 1;
        
            }
        
            $checker = 1;
        
        }elseif ($recordType == "PO Return") { 
        
            $sql = "SELECT supid FROM 8_poreturnssummary WHERE status >= 1 AND poretno = ? AND allocateall = 'No Allocation'";
                $data = array($refno);
                $get_id = $this->app_db->query($sql, $data);
        
                if($get_id->num_rows() != ""){
        
                    $fullname = $this->Model_settings_void->get_supplier_name($get_id->row()->supid);
        
                    $sql = "SELECT '".$fullname."' as name, poretno as poretno, trandate as date, totalamt as amount FROM 8_poreturnssummary WHERE status >= 1 AND poretno = ? AND allocateall ='No Allocation'";
                    $data = array($refno);
                    $query = $this->app_db->query($sql, $data)->row();
                    $checker = 1;
                }else{
                    $checker=2;
                }
        
        }elseif ($recordType == "Inventory Adjustment") { 
        
            $sql = "SELECT trandate as date,  adjno as adjno FROM 8_invadj WHERE status >= 1 AND adjno = ?";
            $data = array($refno);
            $query = $this->app_db->query($sql, $data)->row();
        
            $checker = 1;
        
        }elseif ($recordType == "Inventory Location Transfer") { 
        
            $sql = "SELECT trandate as date,  refid as iltno FROM 8_inventorytrans WHERE status >= 1 AND reftype='ILT' AND refid = ?";
            $data = array($refno);
            $query = $this->app_db->query($sql, $data)->row();
        
            $checker = 1;
        
        }elseif ($recordType == "Supplier") { 
        
            $sql = "SELECT pono FROM 8_purchasessummary WHERE status >= 1 AND supid = ?";
            $data = array($refno);
            $query = $this->app_db->query($sql, $data);
        
            if(!empty($query)){
                    $checker = 2;
            }else{
                $sql = "SELECT id as supid, suppliername as name FROM 8_suppliers WHERE status >= 1 AND id = ?";
                    $data = array($refno);
                    $query = $this->app_db->query($sql, $data)->row();
                    $checker = 1;
        
            }                    
        
        }elseif ($recordType == "Build Inventory") { 
        
                $sql = "SELECT buildno as buildno, trandate as date FROM 8_builditems WHERE status >= 1 AND buildno = ?";
                $data = array($refno);
                $query = $this->app_db->query($sql, $data)->row();
                $checker = 1;
        }
        else{
            $checker = 0;
        }

        if($checker == 0){
            return 'idno_exist_in_membermain';
        }else if($checker == 2){
            return 'cannot_be_voided';
        }else{
            return $query;
        }
    }

    public function check_membermain_if_exist($refno){
        $sql = "SELECT idno FROM 8_membermain WHERE idno = ? AND status >= 1";
        $data = array($refno);
        return $this->app_db->query($sql, $data);
    }

    public function getPackageSummary($idno) {

        $requestData= $_REQUEST;

        $sql = "SELECT DISTINCT idno, date,  packageamount FROM 8_memberpayments WHERE status=1 AND idno = ?" ;
        $data = array($idno);
               
        $query         = $this->app_db->query($sql, $data);
        $totalData     = $query->num_rows();
        $totalFiltered = $totalData; // when there is no search parameter then total number rows = total number filtered rows.

        $data  = array();
        $token = en_dec("en", $this->session->userdata('token_session'));
        foreach( $query->result_array() as $row ) {
            $nestedData     = array(); 
            $nestedData[]   = $row["date"];
            $nestedData[]   = $row["packageamount"];
            //$nestedData[]   = $row["actno"];//general_discounted_total($row["amount"]);
            //$nestedData[]   = '<button class="btn btn-secondary btn-sm btnAPVVoid" data-rcvno = "' . $row['id'] . '" id = "btnAPVVoid">Void Record</button>';
            $data[]         = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );

        echo json_encode($json_data);
    }

    public function get_purchase_amount($pono){
        $sql = "SELECT totalamt FROM 8_purchasessummary WHERE pono = ? AND status >= 1";
        $data = array($pono);
        return $this->app_db->query($sql, $data)->row()->totalamt;
    }

    function void_accounts_payable($refid,$reason,$trandate,$datatype,$jcusername){


        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);

        $sql = "SELECT rcvno FROM 8_apvlog WHERE status >= 1 AND apvno = ?" ;
        $data = array($refid);
        $query = $this->app_db->query($sql,$data);
        $res = $query->result_array();

        foreach ($res AS $row) {
            $sql = "UPDATE 8_invrcvsummary SET rcvstatus='Approved' WHERE  rcvno = ?" ;
            $data = array($row['rcvno']);
            $this->app_db->query($sql,$data);
        }
        
        $sql = "UPDATE 8_apvsummary SET status=0 WHERE apvno=?" ;
        $data = array($refid);
        $this->app_db->query($sql,$data);

        $sql = "UPDATE 8_apvlog SET status=0 WHERE apvno=?" ;
        $data = array($refid);
        $this->app_db->query($sql,$data);

        return 1;
        
    }

    function void_receive_po($refid,$reason,$trandate,$datatype,$jcusername){

        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);

        $sql = "SELECT pono,itemid,qty FROM 8_invrcv WHERE status=1 AND rcvno = ?";
        $data = array($refid);
        $query = $this->app_db->query($sql,$data);
        $res = $query->result_array();

        $iszero=1;
        $pono=0;
        foreach($res AS $row)
        {
            $pono=$row["pono"];

            $sql = "SELECT qtyrcv FROM 8_purchases WHERE status=1 AND pono = ? AND itemid = ?";
            $data = array($row["pono"],$row["itemid"]);
            $query1 = $this->app_db->query($sql,$data);
            $res1 = $query1->row_array();

            $qtyrcv = $res1["qtyrcv"];
            $qtyrcv = $qtyrcv - $row["qty"];

            $sql = "UPDATE  8_purchases SET qtyrcv=? WHERE status=1 AND pono = ? AND itemid = ?";
            $data = array($qtyrcv,$row["pono"],$row["itemid"]);
            $this->app_db->query($sql,$data);

            if($qtyrcv > 0)
            {
                $iszero=0;
            }
        }


        $sql = "UPDATE 8_inventorytrans SET status = 0 WHERE refid = ? AND reftype = ? AND status >= 1";
        $data = array($refid, 'Inventory Receiving');
        $this->app_db->query($sql, $data);

        $sql = "UPDATE 8_invrcv SET status = 0 WHERE rcvno = ? AND status >= 1";
        $data = array($refid);
        $this->app_db->query($sql, $data);

        $sql = "UPDATE 8_invrcvsummary SET status = 0 WHERE rcvno = ? AND status >= 1";
        $data = array($refid);
        $this->app_db->query($sql, $data);

        if($iszero==1){

            $sql = "UPDATE 8_purchasessummary SET rcvall='No Delivery' WHERE status>=1 AND  pono = ?";
            $data = array($pono);
            $this->app_db->query($sql, $data);
        }else{ 

            $sql = "UPDATE 8_purchasessummary SET rcvall='Partial Delivery' WHERE status>=1 AND  pono = ? ";
            $data = array($pono);
            $this->app_db->query($sql, $data);

        }

        return 1;

    }

    function void_purchase_order($refid,$reason,$trandate,$datatype,$jcusername){

        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);


        $sql = "UPDATE 8_purchasessummary SET status = 0 WHERE pono = ? AND status >= 1";
        $data = array($refid);
        $this->app_db->query($sql, $data);

        $sql = "UPDATE 8_purchases SET status = 0 WHERE pono = ? AND status >= 1";
        $data = array($refid);
        $this->app_db->query($sql, $data);

        return 1;

    }

    function get_supplier_name($supid){
        $sql = "SELECT suppliername FROM 8_suppliers WHERE status=1 AND id = ?";
        $data = array($supid);
        return $this->app_db->query($sql,$data)->row()->suppliername;
        
    }

    public function get_account($id){
        $sql = "SELECT description FROM 8_accountlist WHERE status=1 AND id = ?";
        $data = array($id);
        return $this->app_db->query($sql, $data);
    }
    
    function void_bank_deposit($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql = "UPDATE 8_deposits SET status=0 WHERE status=1 AND depno = ?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        return 1;
    
    }
    
    function void_gl_transaction($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql = "UPDATE 8_begbalances SET status=0 WHERE status=1 AND id = ?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        return 1;
    }
    
    function void_po_return($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql  = "UPDATE 8_poreturnssummary SET status=0 WHERE status=1 AND poretno=?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
            
        $sql  = "UPDATE 8_poreturns SET status=0 WHERE status=1 AND poretno=?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
            
        $sql  = "UPDATE 8_inventorytrans SET status=0 WHERE status=1 AND reftype='PO Return' AND refid=?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
             
        $sql  = "SELECT id,allocrefid,allocreftype FROM 8_customeraccountallocation WHERE status=1 AND reftype='PO Return' AND refid=?";
        $data = array($refid);
        $query = $this->app_db->query($sql, $data);
        
        if(empty($query)){
                // no execution
        }else{
            foreach ($query->result_array() as $row) {
              
                $sql  = "SELECT id FROM 8_customeraccountallocation WHERE status=1 AND allocreftype=? AND allocrefid=? AND id<>?";
                $data = array($row["allocreftype"], $row["allocrefid"], $row["id"]);
                $row1 = $this->app_db->query($sql, $data)->row_array();
                
                if($row1["id"]==""){             
                    $sql  = "UPDATE 8_purchasessummary SET ispaid='No Payment' WHERE status=2 AND pono=?";
                    $data = array($row["allocrefid"]);
                    $query = $this->app_db->query($sql, $data);
                }
                else{      
                    $sql  = "UPDATE 8_purchasessummary SET ispaid='Partial Payment' WHERE status=2 AND pono=?";  
                    $data = array($row["allocrefid"]);
                    $query = $this->app_db->query($sql, $data);
                }
            }
        }
            
        $sql  = "UPDATE 8_customeraccountallocation SET status=0 WHERE status=1 AND reftype='PO Return' AND refid=?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
        
        return 1;
    }
    
    function void_inventory_adjustment($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql = "UPDATE 8_invadj SET status=0 WHERE status=1 AND adjno = ?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        $sql = "UPDATE 8_inventorytrans SET status=0 WHERE status=1 AND reftype='Inventory Adjustment' AND refid = ?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        return 1;
    }   
    
    function void_inventory_location_transfer($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql = "UPDATE 8_invtrans SET status=0 WHERE status=1 AND iltno = ?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        $sql = "UPDATE 8_inventorytrans SET status=0 WHERE status=1 AND reftype='ILT' AND refid=?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        return 1;
    }
    
    function void_supplier($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql = "UPDATE 8_suppliers SET status=0 WHERE status=1 AND id= ?";
        $data = array($refid);
        $this->app_db->query($sql, $data);
    
        return 1;
    }
    
    function void_build_inventory($refid,$reason,$trandate,$datatype,$jcusername){
    
        $sql = "INSERT INTO 8_voidlog(trandate,ftype,refid,username,reason,status) 
        VALUES(?,?,?,?,?,?)" ;
        $data = array($trandate,$datatype,$refid,$jcusername,$reason,1);
        $this->app_db->query($sql,$data);
    
        $sql  = "UPDATE 8_builditems SET status=0 WHERE status=1 AND buildno=?";
        $data = array($refid);
        $this->app_db->query($sql,$data);
            
        $sql  = "UPDATE 8_builditemslog SET status=0 WHERE status=1 AND buildno=?";
        $data = array($refid);
        $this->app_db->query($sql,$data);
            
        $strSQL  = "UPDATE 8_inventorytrans SET status=0 WHERE status=1 AND reftype='BOM' AND refid=?";
        $data = array($refid);
        $this->app_db->query($sql,$data);
    
        return 1;
    }
}