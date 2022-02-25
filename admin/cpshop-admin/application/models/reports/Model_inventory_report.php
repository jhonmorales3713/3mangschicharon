<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_inventory_report extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('reports', TRUE);
    }

    public function get_shop_options($id = false) {
        $query="SELECT * FROM sys_shops WHERE status = 1";
        if($id){
            $id = $this->db2->escape($id);
            $query .= " AND id = $id";
        }
        return $this->db2->query($query)->result_array();
    }

    public function getShopName($shop_id){
        $row = $this->db2->query("SELECT * FROM sys_shops where id=$shop_id")->row();
        return $row->shopname;
    }

    public function getBranches($shop_id){
        $shop_id = $this->db2->escape($shop_id);
        return $this->db2->query("SELECT a.branchid, b.branchname FROM sys_branch_mainshop a JOIN sys_branch_profile b on a.branchid = b.id WHERE mainshopid = $shop_id");
    }

    public function getBranchName($branch_id){
        $row = $this->db2->query("SELECT * FROM sys_branch_profile WHERE id = $branch_id")->row();
        if($branch_id){
            return $row->branchname;
        }
        else{
            return 'Main';
        }        
    }

    public function getTransactionTypes(){
        return $this->db2->query("SELECT type from sys_products_invtrans GROUP BY type")->result_array();        
    }
   
    public function get_inventory_trans_table($fromdate, $todate, $exportable = false){     
        
        $fromdate = date("Y-m-d", strtotime($fromdate));
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
        
        $fromdate = $this->db2->escape($fromdate);
		$todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

        if(!$exportable){
            $requestData = $_REQUEST;    
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));
        } 

        $columns = array( 		
            0 => 'date_created',   
            1 => 'shopname',
            2 => 'branchname',
            3 => 'itemname',            
            4 => 'price',
            5 => 'quantity',
            6 => 'quantity', 
            7 => 'ending_quantity'                 
        );
		
        //fiters if any
        $shop_filter = "";      
        $branch_filter = "";  
        $item_filter = ""; $prods_arr = []; $ids = [];
        $prod_ids = []; $prod_ids_str = ""; $prods = [];

        $itemname = $this->input->post('itemname');
        $prods_arr = $this->getProductList($itemname);
        $prod_ids = array_column($prods_arr, 'product_id');
        $prod_ids_str = implode("','", $prod_ids);

        if($exportable){
            if(intval($this->session->sys_shop_id) == 0){
                $filters = json_decode($this->input->post('_filters'));
                $shop_id = $filters->shop_id;
                $branch_id = $filters->branch_id;
            }
            else{
                $filters = json_decode($this->input->post('_filters'));
                $shop_id = $this->session->sys_shop_id;
                $shop_filter = ' AND b.sys_shop = '.$shop_id;

                if($this->session->branchid != 0){
                    $branch_id = $this->session->branchid;
                }
                else{
                    $branch_id = $filters->branch_id;
                }
            }
        }
        else{
            if(intval($this->session->sys_shop_id) == 0){
                $shop_id = $this->input->post('shop_id');
                $branch_id = $this->input->post('branch_id');
            }
            else{         
                $shop_id = $this->session->sys_shop_id;       
                $shop_filter = ' AND b.sys_shop = '.$this->session->sys_shop_id;                

                if($this->session->branchid != 0){
                    $branch_id = $this->session->branchid;
                }
                else{
                    $branch_id = $this->input->post('branch_id');
                }
            }
        }    
        $prods = $this->get_ProdsByShopidAndBranchid($shop_id, $branch_id, $prod_ids_str);
        $ids = array_column($prods, 'product_id');
        $ids_str = implode("','", $ids);
        $branch_filter = " AND a.product_id in ('$ids_str')";

        $shop_ids = implode("','", array_unique(array_column($prods, 'shopid')));
        $shops = $this->get_Shops($shop_ids);
        $shop_ids = array_column($shops, "id");

        $new_prods = [];
        foreach ($prods as $key => $value) {
            $prod_index = array_search($value['product_id'], $prod_ids);
            $new_prods[] = [
                'product_id' => $value['product_id'],
                'itemname' => $prods_arr[$prod_index]['itemname'],
                'price'    => $prods_arr[$prod_index]['price'],
                'shopid'   => $value['shopid'],
                'shopname' => $shops[array_search($value['shopid'], $shop_ids)]['shopname'],
            ];
        }
        
        // print_r($new_prods); exit();
        /*
        $trans_type_filter = "";
        if($transaction_type != "all"){
            $trans_type_filter = " AND a.type = '".$transaction_type."'";
        }
        */
        $branchid = $branch_id;
        $branchid = ($branchid != 'all') ? $branchid : '';
        // $branchid = ($branchid == 'main') ? 0 : $branchid;

      
        if($branchid == 'main'){
            $branch_filter2 = "AND branchid = 0";
        }
        else if($branchid != ""){
            $branch_filter2 = "AND branchid = ".$branchid."";
        }
        else{
            $branch_filter2 = '';
        }

        $sql = "SELECT product_id FROM sys_products_invtrans WHERE date_created BETWEEN $fromdate AND $todate $branch_filter2";
        if ($this->db2->query($sql)->num_rows() > 0) {  
            $sql="SELECT a.product_id, a.branchid, a.quantity, a.type, a.date_created, c.itemname as parent_product_name      
                FROM sys_products_invtrans AS a
                LEFT JOIN sys_products AS b ON a.product_id = b.Id
                LEFT JOIN sys_products AS c ON b.parent_product_id = c.Id
                WHERE a.enabled = 1 AND a.date_created BETWEEN $fromdate AND $todate $item_filter $branch_filter
                $branch_filter2
                ";
        }

        $result = $this->db2->query($sql);
        $totalData = $result->num_rows();
		$totalFiltered = $result->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        if(!$exportable){
		    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        }

		$result = $this->db2->query($sql)->result_array();

        $prod_ids = array_column($new_prods, 'product_id');
        $branch_ids = implode("','", array_unique(array_column($result, 'branchid')));
        $branches = $this->get_Branches($branch_ids);
        $branch_ids = array_column($branches, "id");
        
		$data = array();
		foreach( $result as $row ) {  // preparing an array for table tbody
            $nestedData=array();       

            $row["itemname"] = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ': '';
            $row["itemname"] = $row["itemname"].$new_prods[array_search($row['product_id'], $prod_ids)]['itemname'];
            $row['shopname'] = $new_prods[array_search($row['product_id'], $prod_ids)]['shopname'];
            $row['price']    = $new_prods[array_search($row['product_id'], $prod_ids)]['price'];
            $row['branchname'] = ($row['branchid'] == 0) ? "Main":$branches[array_search($row['branchid'], $branch_ids)]['branchname'];
            
            $nestedData[] = $row["date_created"];
            // if($this->session->sys_shop_id == 0)      {
                $nestedData[] = $row['shopname'];
                $nestedData[] = $row['branchname'];
            // }
            // else{
            //     if($this->session->branchid == 0){
            //         $nestedData[] = $row['branchname'];
            //     }                
            // }
            // $row["beginning_quantity"] = $this->get_ProdQty($row['product_id'], $row['date_created'])[0]['qty'];
            
            $nestedData[] = $row["itemname"];
            $nestedData[] = number_format($row["price"],2);
            // $nestedData[] = number_format($row["beginning_quantity"],0);
            $nestedData[] = number_format($row["quantity"],0);    
            // $ending = intval($row['beginning_quantity']) + intval($row['quantity']);
            // $nestedData[] = number_format($ending,0);
            
            //$nestedData[] = $row["type"];	            
            
			
			$data[] = $nestedData;
        }

		$json_data = array(
			"recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData            
            "data"            => $data,   // total data array
            "shop_id"         => $shop_id,
            "branch_id"       => $branch_id            
		);

		return $json_data;
    }   

    private function getProductList($itemname)
    {
        $sql = "SELECT id AS product_id, itemname, price, sys_shop AS shopid FROM sys_products WHERE itemname LIKE '%$itemname%'";
        return $this->db2->query($sql)->result_array();
    }

    private function get_ProdsByShopidAndBranchid($shop_id, $branch_id, $prod_ids_str)
    {
        $shop_filter = ""; $branch_filter = "";
        if ($shop_id > 0) {
            $shop_filter = " AND shopid = $shop_id";
            if ($branch_id > 0) {
                $branch_filter = " AND branchid = $branch_id";
            } elseif ($branch_id == "main") {
                $branch_filter = " AND branchid = 0";
            }
        }
        $sql = "SELECT product_id, shopid FROM sys_products_invtrans_branch WHERE product_id IN ('$prod_ids_str') $shop_filter $branch_filter";
        return $this->db2->query($sql)->result_array();
    }

    private function get_ProdQty($prod_id, $current_item_datecreated)
    {
        $sql = "SELECT SUM(quantity) as qty FROM sys_products_invtrans WHERE date_created < '$current_item_datecreated' AND product_id = '$prod_id'";
        return $this->db2->query($sql)->result_array();
    }
    
    private function get_Shops($shop_ids)
    {
        $sql = "SELECT id, shopname FROM sys_shops WHERE id IN ('$shop_ids')";
        return $this->db2->query($sql)->result_array();
    }
    
    private function get_Branches($branch_ids)
    {
        $sql = "SELECT id, branchname FROM sys_branch_profile WHERE id IN ('$branch_ids')";
        return $this->db2->query($sql)->result_array();
    }

    public function inventory_ending_table(){
		
        $_name 			      = $this->input->post('_name');
        $_searchproduct 	  = $this->input->post('_searchproduct');
        $_shops 		      = $this->input->post('_shops');
        $_branches 		      = $this->input->post('_branches');
        $date_from 		      = format_date_reverse_dash($this->input->post('date_from'));
        $date_to 		      = format_date_reverse_dash($this->input->post('date_to'));
        $token_session        = $this->session->userdata('token_session');
        $token                = en_dec('en', $token_session);
        $date_from_2          = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
        $date_to_2         	  = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

		$requestData = $_REQUEST;

		$columns = array(
		// datatable column index  => database column name for sorting
            0 => 'b.shopname',
            1 => 'a.branchid',
            2 => 'c.itemname',
            3 => 'product_id',
            4 => 'd.category_name'
		);

        $getTotalEndingQty    = $this->getTotalEndingQty($date_to_2)->result_array();
		$getTotalEndingQtyArr = [];

        $getBranch    = $this->getBranch()->result_array();
		$getBranchArr = [];

		foreach($getTotalEndingQty as $row){
			$getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'] = $row['ending_quantity'];
		}

        foreach($getBranch as $row){
			$getBranchArr[strval($row['id'])]['branchname'] = $row['branchname'];
		}
         
        $sql = "SELECT a.*, b.shopname, c.itemname, d.category_name, e.itemname as parent_product_name, c.variant_isset, c.parent_product_id FROM sys_products_invtrans_branch AS a
        LEFT JOIN sys_shops AS b ON a.shopid = b.id
        LEFT JOIN sys_products AS c ON a.product_id = c.Id
        LEFT JOIN sys_product_category AS d ON c.cat_id = d.id
        LEFT JOIN sys_products AS e ON c.parent_product_id = e.Id
        WHERE a.status = '1' AND c.enabled > '0' ";
        

        if($_name != ""){
            $sql.=" AND (c.itemname LIKE '%".$this->db2->escape_like_str($_name)."%' OR e.itemname LIKE '%".$this->db2->escape_like_str($_name)."%')";
        }
        
        if($_shops != ""){
            $sql.=" AND a.shopid = ".$_shops."";
        }

        if($_searchproduct == "withStock"){
            $sql.=" AND a.no_of_stocks  >  0 ";
            $sql.=" AND a.no_of_stocks  !=  0 ";
        }else if($_searchproduct == "withoutStock"){
            $sql.=" AND a.no_of_stocks  <= 0 ";
        }

        if($_branches != ""){
            $sql.=" AND a.branchid = ".$_branches."";
        }

        $sql.=" GROUP BY a.branchid, a.product_id";

        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		// $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	
		$query = $this->db2->query($sql);

		$data = array();
        $count = 0;
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			

          if($row["variant_isset"] == 0 && $row['parent_product_id'] == ''){
                $nestedData=array();
                $ending_qty = (!empty($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'])) ? number_format($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'], 2) : '-';
                $branch     = ($row["branchid"] == 0) ? 'Main' :  $getBranchArr[strval($row['branchid'])]['branchname'];
                $nestedData[] = $row["shopname"];
                $nestedData[] = $branch;
                $parent_itemname = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : ''; 
                $nestedData[] = $parent_itemname.$row["itemname"];
                $nestedData[] = $ending_qty;
                $nestedData[] = $row["category_name"];
                $data[] = $nestedData;

            }else if($row['parent_product_id'] != ''){
                $nestedData=array();
                $ending_qty = (!empty($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'])) ? number_format($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'], 2) : '-';
                $branch     = ($row["branchid"] == 0) ? 'Main' :  $getBranchArr[strval($row['branchid'])]['branchname'];
                $nestedData[] = $row["shopname"];
                $nestedData[] = $branch;
                $parent_itemname = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : ''; 
                $nestedData[] = $parent_itemname.$row["itemname"];
                $nestedData[] = $ending_qty;
                $nestedData[] = $row["category_name"];
                $data[] = $nestedData;
            }else{   
                $count++;
            }

		}
        
		$key = $requestData['order'][0]['column'];
		$dir = $requestData['order'][0]['dir'];
		uasort($data, build_sorter($key, $dir));
		$data = (isset($requestData['start'])) ? array_slice($data, $requestData['start'], $requestData['length']):$data;

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData -  $count),  // total number of records
			"recordsFiltered" => intval( $totalFiltered -  $count), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}


    public function getTotalEndingQty($date_to_2){

		$sql = "SELECT branchid, product_id, SUM(quantity) as ending_quantity FROM sys_products_invtrans 
        WHERE date_created <= ".$date_to_2." AND enabled = 1
        GROUP BY branchid, product_id";

		
		$result = $this->db2->query($sql);

		return $result;
    }

    public function getBranch(){

		$sql = "SELECT * FROM sys_branch_profile";

		$result = $this->db2->query($sql);

		return $result;
    }

    public function inventory_ending_export(){
        $_name 			      = $this->input->post('_name_export');
        $_shops 		      = $this->input->post('_shops_export');
        $_searchproduct 	  = $this->input->post('_searchproduct_export');
        $_branches 		      = $this->input->post('_branches_export');
        $date_from 		      = format_date_reverse_dash($this->input->post('date_from_export'));
        $date_to 		      = format_date_reverse_dash($this->input->post('date_to_export'));
        $date_from_2          = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
        $date_to_2         	  = $this->db2->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));

        $sql = "SELECT a.*, b.shopname, c.itemname, d.category_name, e.itemname as parent_product_name, c.variant_isset, c.parent_product_id FROM sys_products_invtrans_branch AS a
        LEFT JOIN sys_shops AS b ON a.shopid = b.id
        LEFT JOIN sys_products AS c ON a.product_id = c.Id
        LEFT JOIN sys_product_category AS d ON c.cat_id = d.id
        LEFT JOIN sys_products AS e ON c.parent_product_id = e.Id
        WHERE a.status = 1 AND c.enabled > 0 ";
        

        if($_name != ""){
            $sql.=" AND (c.itemname LIKE '%".$this->db2->escape_like_str($_name)."%' OR e.itemname LIKE '%".$this->db2->escape_like_str($_name)."%')";
        }
        
        if($_shops != ""){
            $sql.=" AND a.shopid = ".$_shops."";
        }

        if($_searchproduct == "withStock"){
            $sql.=" AND a.no_of_stocks  >  '0' ";
        }else if($_searchproduct == "withoutStock"){
            $sql.=" AND a.no_of_stocks  <= '0' ";
        }

        if($_branches != ""){
            $sql.=" AND a.branchid = ".$_branches."";
        }

        return $this->db2->query($sql)->result_array();

    }


        
}