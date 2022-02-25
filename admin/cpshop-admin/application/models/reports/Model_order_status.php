<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_order_status extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_paid_orders_with_branch', 'model_powb');
        $this->db2 = $this->load->database('reports', TRUE);
    }

    public function main_nav_categories() {
		$sql = "SELECT `main_nav_id`, `main_nav_desc` FROM `cp_main_navigation` WHERE `enabled` >= 1";

		return $this->db2->query($sql);
	}

    public function get_shop_options($id = false) {
        $query="SELECT * FROM sys_shops WHERE status = 1";
        if($id){
            $id = $this->db2->escape($id);
            $query .= " AND id = $id";
        }
        return $this->db2->query($query)->result_array();
    }

    public function getBranches($shop_id){
        $shop_id = $this->db2->escape($shop_id);
        return $this->db2->query("SELECT a.branchid, b.branchname FROM sys_branch_mainshop a JOIN sys_branch_profile b on a.branchid = b.id WHERE mainshopid = $shop_id");
    }

    public function getShopName($shop_id){
        $row = $this->db2->query("SELECT * FROM sys_shops where id=$shop_id")->row();
        return $row->shopname;
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

    public function getBranchDetails($shop_id,$branch_id){
        if($branch_id == 0){
            return $this->db2->query("SELECT * FROM sys_shops WHERE id = $shop_id")->row();   
        }
        else{
            return $this->db2->query("SELECT * FROM sys_branch_profile WHERE id = $branch_id")->row();        
        }        
    }   

    public function getOrderDetails($reference_num){
        return $this->db2->query("SELECT * FROM app_sales_order_details WHERE reference_num ='".$reference_num."'")->row();
    }    

    //pending orders summary
    // all orders in shop
    public function get_pending_orders(){        

        $shop_filter = ""; $shop_id = 0; $usertype = 0;

        if($this->session->sys_shop_id == 0 && $this->input->post('shop_id') != 'all'){
            $shop_id = $this->input->post('shop_id');
            $shop_filter = 'sys_shop = '.$this->input->post('shop_id');
        }
        else{
            if($this->session->sys_shop_id != 0){
                $shop_id = $this->session->sys_shop_id;
                $shop_filter = 'sys_shop = '.$this->session->sys_shop_id;
            }
        }
        
        $key_ctrl = "shop_id, branch_id";
        $orders = $this->model_powb->paid_order_with_branch_query(
            ['shopname', 'shop_id', 'branchname', 'branch_id', 'single_cnt'],
            [
                'fromdate'    => null,
                'todate'      => null,
                'branch_id'   => 0,
                'shop_id'     => $shop_id,
                'pmethodtype' => "op",
                'filters'     => [
                    'shop_filter' => $shop_filter,
                    'date_filter' => "",
                    0             => "order_status NOT IN ('p','po','rp','bc')",
                ],
                'group_by'    => null,
            ], $usertype, $key_ctrl, 'single_cnt'
        );
        uasort($orders, build_sorter('single_cnt','desc'));

        return $orders;
    }

    // all orders in shop
    public function get_pending_orders_table($exportable = false){        
        
        $token_session 		= $this->session->userdata('token_session');
		$token = en_dec('en', $token_session); $usertype = 0;

        if(!$exportable){
            $requestData = $_REQUEST;    
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }        

		$columns = array( 		
            0 => 'shopname',
            1 => 'branchname', 
            2 => 'single_cnt'
        );
        
        $shop_filter = "";
        $shop_id = intval($this->session->sys_shop_id);

        if($shop_id == 0){
            if ($exportable) {
                $shop_id = $requestData['shop_id'];
                if ($shop_id !== 'all') {
                    $shop_filter = 'sys_shop = '.$requestData['shop_id'];
                }
            } else {
                if($this->input->post('shop_id') != 'all'){
                    $shop_id = $this->input->post('shop_id');
                    $shop_filter = 'sys_shop = '.$this->input->post('shop_id');                    
                }                
            }
        }
        else{
            $shop_filter = 'sys_shop = '.$this->session->sys_shop_id;                            
        }

        $key_ctrl = "shop_id, branch_id";

        $orders = $this->model_powb->paid_order_with_branch_query(
            ['shopname', 'shop_id', 'branchname', 'branch_id', 'single_cnt'],
            [
                'fromdate'    => null,
                'todate'      => null,
                'branch_id'   => 0,
                'shop_id'     => $shop_id,
                'pmethodtype' => "op",
                'filters'     => [
                    'shop_filter' => $shop_filter,
                    'date_filter' => "",
                    0             => "order_status IN ('p','po','rp','bc')",
                ],
                'group_by'    => null,
            ], $usertype, $key_ctrl, 'single_cnt'
        );

        $totalData = count($orders);
        $totalFiltered = $totalData;  
        $total_count = $totalData;		
        
        $total_pending_orders = getTotalInArray($orders,'single_cnt');        

        uasort($orders, build_sorter($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir']));
        if (!$exportable) {
            $orders = array_slice($orders, $requestData['start'], $requestData['length']);
        }
        
		$data = array();
		foreach( $orders as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
            $nestedData[] = $row["shopname"];
            $nestedData[] = $row["branchname"];
            $nestedData[] = $row["single_cnt"];	
            
            if(!$exportable){
                $buttons = "";                
                $buttons .= '<a class="dropdown-item" data-value="'.$row['shop_id'].'" href="'.base_url('Order_status/shop_branch_pending_orders_list/'.$row['shop_id'].'/'.$row['branch_id'].'/'.$token).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>';
    
                $nestedData[] = 
                '<div class="dropdown">
                    <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                    <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
                    '.$buttons.'
                      </div>
                </div>';
            }
			
			$data[] = $nestedData;
        }

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "total_pending"   => number_format($total_pending_orders,0),
            "data"            => $data   // total data array
            
		);

		return $json_data;
    }

    //pending orders branch
    public function get_pending_orders_branch_table($exportable=false){        
        
        $token_session = $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);

        if(!$exportable){            
            $requestData = $_REQUEST;
            $shop_id = $this->input->post('shop_id');
            $branch_id = $this->input->post('branch_id');
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));            
            $filters = json_decode($this->input->post('_filters'));
            $shop_id = $filters->shop_id;
            $branch_id = $filters->branch_id;
        }

		$columns = array( 		
            0 => 'payment_date',
            1 => 'total_pending_orders', 
            2 => 'total_orders_on_process',
            3 => 'total_orders_ready_for_pickup',
            4 => 'total_orders_booking_confirmed',
        );   
        $usertype = 0;

        if($shop_id != "all" && $shop_id != 0){
            $shop_filter = "sys_shop = $shop_id";            
            if($branch_id != "all"){
                if($branch_id == "main" || $branch_id == 0){
                    $branch_filter = ""; $usertype = 1;
                }
                else{
                    $usertype = 2;
                    $branch_filter = "branch_id = $branch_id";
                }
            }           
        }
 
        $key_ctrl = "payment_date";
        $res = $this->model_powb->paid_order_with_branch_query(['payment_date', 'reference_num', 'order_status'],
            [
                'fromdate'    => null,
                'todate'      => null,
                'branch_id'   => $branch_id,
                'shop_id'     => $shop_id,
                'pmethodtype' => "op",
                'filters'     => [
                    'shop_filter' => $shop_filter,
                    'branch_filter' => $branch_filter,
                    'date_filter' => "",
                    0             => "order_status IN ('p','po','rp','bc')",
                ],
                'group_by'    => "GROUP BY payment_date",
            ], $usertype, $key_ctrl, false
        );
        // print_r($orders); exit();
        $temp_res = []; $indexes = []; $set_key_ctlrs = explode(", ", "payment_date");
		foreach ($res as $key => $value) {
            $is_key = get_key_ctrl($value, $set_key_ctlrs);
			if (!array_key_exists($is_key, $indexes)) {
				$indexes[$is_key] = count($temp_res);
				$temp_res[] = [
					'payment_date' 	                    => $value['payment_date'],
					'reference_num' 	                => $value['reference_num'],
					'total_pending_orders'		        => ($value['order_status'] == 'p') ? 1:0,
					'total_orders_on_process'			=> ($value['order_status'] == 'po') ? 1:0,
					'total_orders_ready_for_pickup'		=> ($value['order_status'] == 'rp') ? 1:0,
					'total_orders_booking_confirmed'	=> ($value['order_status'] == 'bc') ? 1:0,
				];
			} else {
                $os_key = array(
                    'p' => 'total_pending_orders',
                    'po' => 'total_orders_on_process',
                    'rp' => 'total_orders_ready_for_pickup',
                    'bc' => 'total_orders_booking_confirmed'
                )[$value['order_status']];
				$temp_res[$indexes[$is_key]][$os_key] += 1;
			}
        }
		
        $totalData = count($temp_res);
        $totalFiltered = $totalData;  
        $total_count = $totalData;	
        
        $t_pending = getTotalInArray($temp_res,'total_pending_orders');
        $t_onprocess = getTotalInArray($temp_res,'total_orders_on_process');
        $t_pickup = getTotalInArray($temp_res,'total_orders_ready_for_pickup');
        $t_confirmed = getTotalInArray($temp_res,'total_orders_booking_confirmed');

		uasort($temp_res, build_sorter($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir']));
        if (!$exportable) {
            $temp_res = array_slice($temp_res, $requestData['start'], $requestData['length']);
        }
        
		$data = array();
		foreach( $temp_res as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
            $nestedData[] = $row["payment_date"];
            $nestedData[] = $row["total_pending_orders"];
            $nestedData[] = $row["total_orders_on_process"];			
            $nestedData[] = $row["total_orders_ready_for_pickup"];			
            $nestedData[] = $row["total_orders_booking_confirmed"];

			$buttons = "";                
            $buttons .= '<a class="dropdown-item" href="'.base_url('Order_status/shop_branch_pending_orders_in_date/'.$shop_id.'/'.$branch_id.'/'.$row['reference_num'].'/'.$token.'?payment_date='.$row['payment_date']).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>';

			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    '.$buttons.'
			  	</div>
			</div>';
			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
            "shop_id"         => $shop_id,
            "branch_id"       => $branch_id,
            "t_pending"       => number_format($t_pending,0),
            "t_onprocess"     => number_format($t_onprocess,0),
            "t_pickup"        => number_format($t_pickup,0),
            "t_confirmed"     => number_format($t_confirmed,0)
		);

		return $json_data;
    }

    //end of 2nd page

    //3rd page table
    public function get_branch_order_in_date_table($branch_id,$payment_date,$exportable=false){
        $token_session 		= $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);       
        $todate = date_add(date_create($payment_date),date_interval_create_from_date_string("1 days"));
		$todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

        $usertype = 0;
        if(!$exportable){
            $requestData = $_REQUEST;
            $shop_id = $this->input->post('shop_id');
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));            
            $filters = json_decode($this->input->post('_filters'));
            $shop_id = $filters->shop_id;
        }  

		$columns = array( 		
            0 => 'payment_date',
            1 => 'reference_num', 
            2 => 'name',
            3 => 'subtotal',
            4 => 'delivery_amount',
            5 => 'total_amount',            
            6 => 'address'
        );   
        
        $pdate = $payment_date;
        $payment_date = $this->db2->escape($payment_date);
        
        if($shop_id != "all" && $shop_id != 0){
            $shop_filter = "sys_shop = $shop_id";            
            if($branch_id != "all"){
                if($branch_id == "main" || $branch_id == 0){
                    $branch_filter = ""; $usertype = 1;
                }
                else{
                    $usertype = 2;
                    $branch_filter = "branch_id = $branch_id";
                }
            }           
        }

        $res = $this->model_powb->paid_order_with_branch_query(
            ['payment_date', 'reference_num', 'name', 'delivery_amount', 'total_amount', 'address', 'payment_method'],
            [
                'fromdate'      => $payment_date,
                'todate'        => $todate,
                'branch_id'     => 0,
                'shop_id'       => $shop_id,
                'pmethodtype'   => '',
                'filters'       => [
                    'shop_filter'   => $shop_filter,
                    'branch_filter' => $branch_filter,
                    'date_filter'   => "payment_date BETWEEN $payment_date AND $todate",
                    0               => "order_status IN ('p', 'po', 'rp', 'bc')"
                ],
                'group_by'      => 'GROUP BY payment_date',
            ], $usertype, "payment_date", false
        );
        $total_count = $totalFiltered = $totalData = count($res);
        $result = [];
        foreach ($res as $key => $value) {
            $result[] = [
                'payment_date'  => $value['payment_date'], 
                'reference_num' => $value['reference_num'], 
                'name'          => $value['name'],
                'subtotal'      => $value['total_amount'],
                'delivery_amount'   => is_null($value['delivery_amount']) ? 0:$value['delivery_amount'],
                'total_amount'  => is_null($value['delivery_amount']) ? $value['total_amount']:$value['total_amount'] + $value['delivery_amount'],
                'address'       => $value['address'],
                'is_manual_order' => strtolower($value['payment_method']) == 'manual_order' ? 1:0,
            ];
        }

        $t_stotal = getTotalInArray($result,'subtotal');
        $t_shipping = getTotalInArray($result, 'delivery_amount');
        $t_amount= getTotalInArray($result, 'total_amount');        

		uasort($result, build_sorter($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir']));
        if (!$exportable) {
            $result = array_slice($result, $requestData['start'], $requestData['length']);
        }
        
		$data = array();
        foreach( $result as $row ) {  // preparing an array for table tbody
            $shipping = 0;
			$nestedData=array(); 
            $nestedData[] = $row["payment_date"];
            $nestedData[] = $row["reference_num"];
            $nestedData[] = $row["name"];
            $nestedData[] = number_format($row["subtotal"], 2);
            $nestedData[] = number_format($row['delivery_amount'], 2);
            $nestedData[] = number_format($row['total_amount'], 2);
            $nestedData[] = $row["address"];
            
			$buttons = "";
            $buttons .= '<a class="dropdown-item" data-value="'.$branch_id.'" href="'.base_url('Order_status/shop_branch_order_details/'.$shop_id.'/'.$branch_id.'/'.$row['reference_num'].'/'.$row['is_manual_order'].'/'.$token.'?payment_date='.$pdate).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>';

			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    '.$buttons.'
			  	</div>
			</div>';
			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
            "t_count"         => $total_count,
            "t_stotal"        => number_format($t_stotal,2),
            "t_shipping"      => number_format($t_shipping,2),
            "t_amount"        => number_format($t_amount,2),
		);

		return $json_data;
    }    

    public function get_order_details_table($reference_num,$isManualOrder,$exportable=false){

        $token_session 		= $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);       

        if(!$exportable){
            $requestData = $_REQUEST;
            $shop_id = $this->input->post('shop_id');
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));            
            $filters = json_decode($this->input->post('_filters'));
            $shop_id = $filters->shop_id;
        }  

		$columns = array( 		
            0 => 'itemname',
            1 => 'quantity', 
            2 => 'amount',
            3 => 'total_amount'
        ); 

        if($isManualOrder == 1){
            $sql = "SELECT b.itemname, a.quantity, a.amount, a.total_amount, NULL 'refunded_quantity', NULL as 'refunded_amount'
            FROM app_manual_order_logs a 
            LEFT JOIN sys_products b ON a.product_id = b.Id
            WHERE a.order_id = '".$reference_num."'
            GROUP BY a.product_id";
        }
        else{
            $sql = "SELECT c.itemname, b.quantity, b.amount, b.total_amount, d.quantity as 'refunded_quantity', d.amount as 'refunded_amount'
            FROM app_sales_order_details a 
            LEFT JOIN app_sales_order_logs b ON a.id = b.order_id
            LEFT JOIN sys_products c on c.id = b.product_id
            LEFT JOIN app_refund_orders_details d ON a.reference_num = d.refnum AND b.product_id = d.product_id
            WHERE a.status = 1 AND a.payment_status = 1 AND a.order_status != 'f' AND a.order_status != 's' AND a.reference_num = '".$reference_num."' AND a.sys_shop = $shop_id 
            GROUP BY b.product_id";
        }
        
        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  
        $total_count = $totalData;

        $result = $query->result_array();
        $t_qty = getTotalInArray($result,'quantity');
        $t_stotal = getTotalInArray($result,'amount');
        $t_amount = getTotalInArray($result,'total_amount');

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];

        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }        

        $query = $this->db2->query($sql);
        
		$data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $shipping = 0;
			$nestedData=array(); 
            $nestedData[] = $row["itemname"];
            if($row["refunded_quantity"]){
                $nestedData[] = intval($row["quantity"]) - intval($row["refunded_quantity"]);
            }
            else{
                $nestedData[] = $row["quantity"];
            }
            if($row["refunded_amount"]){
                $nestedData[] = float_val($row["amount"]) - floatval($row["refunded_amount"]);
            }
            else{
                $nestedData[] = $row["amount"];
            }            
            $nestedData[] = $row["total_amount"];

			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
            "t_qty"           => number_format($t_qty,0),
            "t_stotal"        => number_format($t_stotal,2),
            "t_amount"        => number_format($t_amount,2)
		);

		return $json_data;        
    }

    //pending orders per branch summary
    public function get_total_pending_orders_per_branch($shop_id){
        $sql="SELECT b.shopname, 0 as branchid, 'Main' as branchname, count(a.date_ordered) as total_pending_orders
            FROM app_sales_order_details a
            LEFT JOIN sys_shops b on a.sys_shop = b.id
            WHERE a.status = 1 AND a.payment_status = 1 AND  a.order_status != 'f' AND a.order_status != 'd' AND a.sys_shop = $shop_id
            AND a.reference_num NOT IN (SELECT orderid FROM sys_branch_orders)
            GROUP BY branchname 
            UNION
            SELECT e.shopname, a.branchid, c.branchname, count(d.date_ordered) as total_pending_orders
            FROM sys_branch_orders a
            LEFT JOIN sys_branch_mainshop b on a.branchid = b.branchid
            LEFT JOIN sys_branch_profile c on a.branchid = c.id
            LEFT JOIN app_sales_order_details d on d.reference_num = a.orderid
            LEFT JOIN sys_shops e on b.mainshopid = e.id
            WHERE d.status = 1 AND d.payment_status = 1 AND  d.order_status != 'f' AND d.order_status != 'd' AND b.mainshopid = $shop_id
            GROUP BY a.branchid
            UNION 
            SELECT c.shopname, a.branchid, b.branchname, 0 as total_pending_orders
            FROM sys_branch_mainshop a            
            LEFT JOIN sys_branch_profile b on a.branchid = b.id
            LEFT JOIN sys_shops c on a.mainshopid = c.id
            WHERE a.mainshopid = $shop_id AND a.branchid NOT IN (SELECT branchid FROM sys_branch_orders)
            ORDER BY branchid";
        return $this->db2->query($sql)->result_array();
    }  
    
     //pending orders summary
    public function get_pending_orders_summary(){
        $sql="SELECT b.shopname, 0 as branchid, 'Main' as branchname, count(a.date_ordered) as total_pending_orders
            FROM app_sales_order_details a
            LEFT JOIN sys_shops b on a.sys_shop = b.id
            WHERE a.status = 1 AND a.payment_status = 1 AND  a.order_status != 'f' AND a.order_status != 'd'
            AND a.reference_num NOT IN (SELECT orderid FROM sys_branch_orders)
            GROUP BY branchname 
            UNION
            SELECT e.shopname, a.branchid, c.branchname, count(d.date_ordered) as total_pending_orders
            FROM sys_branch_orders a
            LEFT JOIN sys_branch_mainshop b on a.branchid = b.branchid
            LEFT JOIN sys_branch_profile c on a.branchid = c.id
            LEFT JOIN app_sales_order_details d on d.reference_num = a.orderid
            LEFT JOIN sys_shops e on b.mainshopid = e.id
            WHERE d.status = 1 AND d.payment_status = 1 AND  d.order_status != 'f' AND d.order_status != 'd'
            GROUP BY a.branchid            
            ORDER BY branchid";
        return $this->db2->query($sql)->result_array();
    }

    public function get_order_status_data($shop_id,$branch_id){           
        
        
    }

    public function get_order_status_table($shop_id, $branch_id, $exportable = false){      
        
       
        
    }
        
}