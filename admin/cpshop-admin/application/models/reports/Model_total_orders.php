<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_total_orders extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_paid_orders_with_branch');
        $this->db2 = $this->load->database('reports', TRUE);
    }
    
    public function get_total_orders($fromdate,$todate,$shop_id,$branch_id){        
    
        $dates = array($fromdate,$todate);
        
        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";
 
        if($shop_id != "all"){
 
            $shop_filter = " AND shop_id = $shop_id";            
 
            if($branch_id != "all"){
                if($branch_id == "main"){
                    $branch_filter = " AND branch_id = 0";
                }
                else{
                    $branch_filter = " AND branch_id = $branch_id";
                }
            }
        }     

        $date_filter = " WHERE date_ordered BETWEEN ? AND ?";

        if(date('Y-m-d',strtotime($fromdate)) == date('Y-m-d',strtotime($todate))){
            $date_format = "DATE_FORMAT(date_ordered, '%Y-%m-%d %H:00')";
            $group_format = " GROUP BY HOUR(date_ordered)";                
        }
        else{
            $date_format = "DATE(date_ordered)";
            $group_format = " GROUP BY DATE(date_ordered)";  
        }
        $sql=" SELECT $date_format as 'date_ordered',shopname, branchname,
                sum(1) as total_paid_orders, 
                sum(case when order_status = 'f' then 1 else 0 end) as total_fulfilled_orders,
                sum(case when order_status = 's' then 1 else 0 end) as total_delivered_orders
                FROM view_paid_orders_with_branch
                $date_filter $shop_filter $branch_filter
                $group_format";        

        $result = $this->db2->query($sql,$dates)->result_array();
        
        return $result;
    }

    public function get_total_orders_data($fromdate,$todate,$shop_id,$branch_id = 'all',$pmethodtype = ''){  
        
        $is_single_date = false;
        if(date('Y-m-d',strtotime($fromdate)) == date('Y-m-d', strtotime($todate))){
            $is_single_date = true;
        }
        
        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";
        $pmethodtype_filter = "";
 
        $usertype = 0;
        if($shop_id > 0){
 
            $shop_filter = "sys_shop = $shop_id";            
 
            if($branch_id != "all" && $branch_id != 0){
                if($branch_id == "main"){
                    $usertype = 1; $branch_id = 0;
                }
                else{
                    $branch_filter = "branch_id = $branch_id";
                }
            }
        }

        if($is_single_date){
            $date_format = "DATE_FORMAT(date_ordered, '%Y-%m-%d %H:00')";
            $group_format = " GROUP BY HOUR(date_ordered)";
        }
        else{
            $date_format = "DATE(date_ordered)";
            $group_format = " GROUP BY DATE(date_ordered)";  
        }

        $fromdate = $this->db2->escape($fromdate);
        $todate = $this->db2->escape($todate.' 23:59:59');

        //data retrieved from model -> returns array merged 
        $result = $this->model_paid_orders_with_branch->paid_order_with_branch_query(['date_ordered','total_amount','order_status'], [
            'fromdate' => $fromdate,
            'todate' => $todate,
            'branch_id' => $branch_id,
            'shop_id' => $shop_id,
            'pmethodtype' => $pmethodtype,
            'filters' => [
                'shop_filter' => $shop_filter,
                'branch_filter' => $branch_filter,
                'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
            ],
            'group_by' => " GROUP BY date_ordered",
            'order_by' => [
                'column' => 'date_ordered',
                'dir'    => 'asc' 
            ],      
            ], $usertype, 'date_ordered', 'total_amount');

            if($result){
                for($x=0; $x < sizeof($result); $x++){                
                    if($is_single_date){
                        $result[$x]['date_ordered'] = date('Y-m-d H:00',strtotime($result[$x]['date_ordered']));
                    }
                    else{
                        $result[$x]['date_ordered'] = date('Y-m-d',strtotime($result[$x]['date_ordered']));
                    }            
                }          
            }        

            uasort($result, build_sorter('date_ordered','asc'));

        /*
        $sql=" SELECT $date_format as `date_ordered`, shopname, branchname,
                    sum(1) as `total_paid_orders`,
                    sum(case when order_status = 'f' then 1 else 0 end) as `total_fulfilled_orders`,
                    sum(case when order_status = 's' then 1 else 0 end) as `total_delivered_orders`
                    FROM view_paid_orders_with_branch WHERE date_ordered BETWEEN $fromdate and $todate $shop_filter $branch_filter $pmethodtype_filter $group_format "; 

        $result = $this->db2->query($sql)->result_array();
        */
        
        return $result;        
    }

    public function get_total_orders_table($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype, $exportable = false){      
        
        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));
        
        $is_single_date = false;
        if($fromdate == $todate){
            $is_single_date = true;
        }

        if(!$exportable){
            $requestData = $_REQUEST;            
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }
        
        $columns = array(
            0 => 'date_ordered',
            1 => 'shopname',
            2 => 'branchname',
            3 => 'total_paid_orders',
            4 => 'total_fulfilled_orders',
            5 => 'total_delivered_orders'
        );

        //shop branch filter
        $shop_filter        = "";
        $branch_filter      = "";
        $branch_filter2     = "";
        $pmethodtype_filter = "";
        $leftjoin_branch    = "";
        if($shop_id != "all"){

            $shop_filter = " AND a.sys_shop = $shop_id";            

            if($branch_id != "all"){
                if($branch_id == "main"){
                    $fromdate2 = date('Y-m-d',strtotime($fromdate." -2 days"));
                    $refnumbranchorder = $this->getBranchOrders_RefNum($fromdate2);
                    $string_ref        = "";

                    foreach($refnumbranchorder as $row){
                        $string_ref .= "'".$row['reference_num']."', ";
                    }
                    $string_ref = rtrim($string_ref, ', ');
                    $branch_filter = ($string_ref != '') ? " AND a.reference_num NOT IN ($string_ref)" : "";
                    $branch_filter2 = " AND branch_id = 0";
                }
                else{
                    $leftjoin_branch = "
                                        LEFT JOIN `sys_branch_orders` bor ON a.reference_num = bor.orderid
                                        LEFT JOIN `sys_branch_profile` bprof ON bor.branchid = bprof.id
                                        LEFT JOIN `sys_branch_mainshop` bms ON bms.mainshopid = a.sys_shop";
                    $branch_filter  = " AND bor.branchid = $branch_id AND bms.branchid = $branch_id AND bor.status = 1";
                    $branch_filter2 = " AND branch_id = $branch_id";
                }
            }
        }

        switch ($pmethodtype) {
            case 'op':
                $pmethodtype_filter = " AND LCASE(a.payment_method) IN ('PayPanda')";
            break;
            case 'mp':
                $pmethodtype_filter = " AND LCASE(a.payment_method) IN ('Manual Order')";
                break;
            default:
                $pmethodtype_filter = " AND LCASE(a.payment_method) IN ('PayPanda','Manual Order')";
                break;
        }
      
        /*
        //get sql tamplate from slqhelper in library
        $fromdate = $this->db2->escape($fromdate);
        $todate = $this->db2->escape($todate.' 23:59:59');
        $sql_helper = paid_order_with_branch_query(
            [ 'date_ordered', 'shopname', 'branchname', 'order_status','total_amount'],
            [
                'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
                'shop_filter' => $shop_filter,
                'branch_filter' => $branch_filter,
                'pmethodtype_filter' => $pmethodtype_filter,
                'group_by' => "",
                'order_by' => ""
            ]
        );
        */

        //summary and logs query
        if($type == "summary"){
            if($is_single_date){
                $date_format = "DATE_FORMAT(a.date_ordered, '%Y-%m-%d %H:00')";
                $group_format = " GROUP BY HOUR(a.date_ordered)";
            }
            else{
                $date_format = "DATE(a.date_ordered)";
                $group_format = " GROUP BY DATE(a.date_ordered)";
            }

            $fromdate = $this->db2->escape($fromdate.' 00:00:00');
            $todate = $this->db2->escape($todate.' 23:59:59');

            if($pmethodtype == 'op'){
                $sql=" SELECT $date_format as `date_ordered`, c.shopname as shopname, a.reference_num, a.sys_shop,
                    sum(1) as `total_paid_orders`,
                    sum(case when a.order_status = 'f' then 1 else 0 end) as `total_fulfilled_orders`,
                    sum(case when a.order_status = 's' then 1 else 0 end) as `total_delivered_orders`
                    FROM app_sales_order_details AS a
                    LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                    $leftjoin_branch
                    WHERE a.status = 1 AND a.date_ordered BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter $group_format ";
            }
            else{
                $sql =" SELECT $date_format as `date_ordered`, c.shopname as shopname,
                    sum(1) as `total_paid_orders`,
                    sum(case when a.order_status = 'f' then 1 else 0 end) as `total_fulfilled_orders`,
                    sum(case when a.order_status = 's' then 1 else 0 end) as `total_delivered_orders`,
                    case when a.branch_id = 0 then 'Main' else d.branchname end as `branchname`
                    FROM app_manual_order_details AS a
                    LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                    LEFT JOIN `sys_branch_profile` as d ON a.branch_id = d.id
                    WHERE a.status = 1 AND a.date_ordered BETWEEN $fromdate AND $todate $shop_filter $branch_filter2 $pmethodtype_filter $group_format ";
            }
            

        }
        else if($type == "logs"){     

            $fromdate = $this->db2->escape($fromdate);
            $todate = $this->db2->escape($todate.' 23:59:59');

            if($pmethodtype == 'op'){
                $sql=" SELECT a.date_ordered, c.shopname as shopname, a.reference_num, a.sys_shop,
                        1 as `total_paid_orders`,
                        case when a.order_status = 'f' then 1 else 0 end as `total_fulfilled_orders`,
                        case when a.order_status = 's' then 1 else 0 end as `total_delivered_orders`
                        FROM app_sales_order_details AS a
                        LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                        $leftjoin_branch
                        WHERE a.status = 1 AND a.date_ordered BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter ";
            }
            else{
                $sql =" SELECT a.date_ordered, c.shopname as shopname,
                        1 as `total_paid_orders`,
                        case when a.order_status = 'f' then 1 else 0 end as `total_fulfilled_orders`,
                        case when a.order_status = 's' then 1 else 0 end as `total_delivered_orders`,
                        case when a.branch_id = 0 then 'Main' else d.branchname end as `branchname`
                        FROM app_manual_order_details AS a
                        LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                        LEFT JOIN `sys_branch_profile` as d ON a.branch_id = d.id
                        WHERE a.status = 1 AND a.date_ordered BETWEEN $fromdate AND $todate $shop_filter $branch_filter2 $pmethodtype_filter ";
            }
        }
       
        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;
        $temp = $query->result();
        $t_p_orders = 0;
        $t_f_orders = 0;
        $t_d_orders = 0; 
        
        
        foreach($temp as $row){
            $t_p_orders += intval($row->total_paid_orders);
            $t_f_orders += intval($row->total_fulfilled_orders);
            $t_d_orders += intval($row->total_delivered_orders);
        }
        

        $total_count = $totalData;
        
        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }
        $query = $this->db2->query($sql);

        $data = array();
        $count = 0;
        $st_p_orders = 0;
        $st_f_orders = 0;
        $st_d_orders = 0;

        $dataloopArr = array();
        $resultDataArr = array();
        
        foreach($query->result_array() as $row ){
            
            $nestedData=array();            

            $nestedData[] = $row['date_ordered'];
            $nestedData[] = $row['shopname'];
            $nestedData[] = ($pmethodtype != 'op') ? $row['branchname'] : $this->get_branchname($row['reference_num'], $row['sys_shop']);
            $nestedData[] = $row['total_paid_orders'];	  
            $nestedData[] = $row['total_fulfilled_orders'];	  
            $nestedData[] = $row['total_delivered_orders'];	               
            
            $data[] = $nestedData;

            $st_p_orders += $row['total_paid_orders'];
            $st_f_orders += $row['total_fulfilled_orders'];
            $st_d_orders += $row['total_delivered_orders'];
        }

        //$this->db2->query("drop view view_total_orders");

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "total_orders" => $total_count,
            "st_p_orders" => $st_p_orders,
            "st_f_orders" => $st_f_orders,
            "st_d_orders" => $st_d_orders,
            "t_p_orders" => $t_p_orders,
            "t_f_orders" => $t_f_orders,
            "t_d_orders" => $t_d_orders,            
            "data"       => $data,
            "query"      => $sql
        );
        
        return $json_data;
        
    }        

    public function get_branchname($orderid, $shopid){
        $sql="SELECT a.orderid, a.branchid, b.branchname FROM sys_branch_orders a
              LEFT JOIN sys_branch_profile b ON a.branchid = b.id
              LEFT JOIN sys_branch_mainshop c ON a.branchid = c.branchid
              WHERE a.status = ? AND a.orderid = ? AND c.mainshopid = ?";
        $data = array(1, $orderid, $shopid);
        $result = $this->db2->query($sql, $data);

        if($result->num_rows() > 0){
            $branchname = $result->row()->branchname;
        }else{
            $branchname = 'Main';
        }
        return $branchname;
	}

    public function getBranchOrders_RefNum($fromdate){
        $sql="SELECT orderid as reference_num FROM sys_branch_orders WHERE DATE(date_created) > ? AND status = 1";
        $data = array($fromdate );
        $result = $this->db2->query($sql, $data)->result_array();

        return $result;
	}
}