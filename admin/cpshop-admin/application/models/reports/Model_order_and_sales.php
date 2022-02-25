<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_order_and_sales extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_paid_orders_with_branch');
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
    
    public function get_order_and_sales($fromdate,$todate,$shop_id,$branch_id = 'all',$pmethodtype = ''){

        $is_single_date = false;
        if(date('Y-m-d',strtotime($fromdate)) == date('Y-m-d', strtotime($todate))){
            $is_single_date = true;
        }

        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";  
        
        $usertype = 0;
        if($shop_id != "all" && $shop_id != 0){
            $shop_filter = " sys_shop = $shop_id";            
            if($branch_id != "all"){
                if($branch_id == "main"){
                    $branch_id = 0; $usertype = 1;
                }
                else{
                    $branch_filter = "  branch_id = $branch_id";
                }
            }           
        }
        
        switch ($pmethodtype) {
            case 'op':
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('paypanda')";
                break;
            case 'op':
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('manual order')";
                break;
            default:
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('paypanda','manual order')";
                break;
        }

        if($is_single_date){	
            $date_format = "DATE_FORMAT(date_ordered, '%Y-%m-%d %H:00')";
            $group_format = " GROUP BY HOUR(date_ordered)";                
        }
        else{
            $date_format = "DATE(t.date_ordered)";
            $group_format = " GROUP BY DATE(date_ordered)";  
        }

        $fromdate = $this->db2->escape($fromdate.' 00:00:00');
        $todate = $this->db2->escape($todate.' 23:59:59');

        //data retrieved from model -> returns array merged 
        $result = $this->model_paid_orders_with_branch->paid_order_with_branch_query(['date_ordered','total_amount'], [
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
            
            $new_array = [];
        
            foreach($result as $res){
                $new_array[] = array(
                    'date_ordered' => $res['date_ordered'],
                    'total_amount' => $res['total_amount'],
                    'total_orders' => 1
                );
            }

            return $new_array;
    }
    
    public function get_order_and_sales_table($fromdate, $todate, $shop_id, $branch_id, $type, $datetype, $pmethodtype, $exportable = false){        

        $fromdate = date('Y-m-d',strtotime($fromdate));
        $todate = date('Y-m-d',strtotime($todate));     
        
        $is_single_date = false;
        if($fromdate == $todate){
            $is_single_date = true;
        }

        if(!$exportable){
            $requestData = $_REQUEST;	      
        } else {
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }

        $columns = array(
            0 => $datetype,
            1 => 'shopname',
            2 => 'branchname',
            3 => 'total_orders',
            4 => 'total_amount'
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
        
        if($fromdate == $todate){
            $dates = array($fromdate);
            $date_filter = " WHERE date(a.$datetype) = ?";
        }
        else{
            $dates = array($fromdate, $todate);
            $date_filter = " WHERE a.$datetype BETWEEN ? AND ?";
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

        if($type == "summary"){
            if($is_single_date){
                $date_format = "DATE_FORMAT(a.$datetype, '%Y-%m-%d %H:00')";
                $group_format = " GROUP BY HOUR(a.$datetype)";                
            }
            else{
                $date_format = "DATE(a.$datetype)";
                $group_format = " GROUP BY DATE(a.$datetype)";  
            }

            $fromdate = $this->db2->escape($fromdate);
            $todate = $this->db2->escape($todate.' 23:59:59');

            // $sql=" SELECT $date_format as `date_ordered`,shopname, branchname,
            //         SUM(1) as `total_orders`,
            //         SUM(total_amount) as `total_amount`
            //         FROM view_paid_orders_with_branch 
            //         WHERE date_ordered BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter $group_format";

            if($pmethodtype == 'op'){
                $sql=" SELECT $date_format as $datetype, c.shopname as shopname, a.reference_num, a.sys_shop,
                    SUM(1) as `total_orders`,
                    SUM(a.total_amount) as `total_amount`
                    FROM app_sales_order_details AS a
                    LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                    $leftjoin_branch
                    WHERE a.status = 1 AND a.$datetype BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter $group_format ";
            }
            else{
                $sql =" SELECT $date_format as $datetype, c.shopname as shopname, a.reference_num, a.sys_shop,
                    SUM(1) as `total_orders`,
                    SUM(a.total_amount) as `total_amount`,
                    case when a.branch_id = 0 then 'Main' else d.branchname end as `branchname`
                    FROM app_manual_order_details AS a
                    LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                    LEFT JOIN `sys_branch_profile` as d ON a.branch_id = d.id
                    WHERE a.status = 1 AND a.$datetype BETWEEN $fromdate AND $todate $shop_filter $branch_filter2 $pmethodtype_filter $group_format ";
            }
            
        }
        else if($type == "logs"){   

            if($is_single_date){
                $date_format = "DATE_FORMAT(a.$datetype, '%Y-%m-%d %H:00')";
                $group_format = " GROUP BY HOUR(a.$datetype)";                
            }
            else{
                $date_format = "DATE(a.$datetype)";
                $group_format = " GROUP BY DATE(a.$datetype)";  
            }

            $fromdate = $this->db2->escape($fromdate);
            $todate = $this->db2->escape($todate.' 23:59:59');  

            // $sql=" SELECT $date_format as `date_ordered`, shopname, branchname,
            //         1 as `total_orders`, 
            //         total_amount
            //         FROM view_paid_orders_with_branch WHERE date_ordered BETWEEN $fromdate AND $todate";

            if($pmethodtype == 'op'){
                $sql=" SELECT a.$datetype, c.shopname as shopname, a.reference_num, a.sys_shop,
                        1 as `total_orders`, 
                        a.total_amount
                        FROM app_sales_order_details AS a
                        LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                        $leftjoin_branch
                        WHERE a.status = 1 AND a.$datetype BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter ";
            }
            else{
                $sql =" SELECT a.$datetype, c.shopname as shopname, 
                        1 as `total_orders`, 
                        a.total_amount,
                        case when a.branch_id = 0 then 'Main' else d.branchname end as `branchname`
                        FROM app_manual_order_details AS a
                        LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                        LEFT JOIN `sys_branch_profile` as d ON a.branch_id = d.id
                        WHERE a.status = 1 AND a.$datetype BETWEEN $fromdate AND $todate $shop_filter $branch_filter2 $pmethodtype_filter ";
            }
        }      
    
        $query = $this->db2->query($sql,$dates);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;
        $total_count = $totalData;

        $totalOrders = getTotalInArray($query->result_array(),'total_orders');
        $totalAmount = getTotalInArray($query->result_array(),'total_amount');
    
        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }
    
        $query = $this->db2->query($sql,$dates);
    
        $data = array();
        $sub_total = 0;
        $sub_total_amount = 0.0;      
        foreach( $query->result_array() as $row )
        {
            $nestedData=array();      
            
            $nestedData[] = $row["$datetype"];
            $nestedData[] = $row['shopname'];
            $nestedData[] = ($pmethodtype != 'op') ? $row['branchname'] : $this->get_branchname($row['reference_num'], $row['sys_shop']);
            $nestedData[] = $row['total_orders'];
            $nestedData[] = number_format($row['total_amount'],2);	  
    
            $data[] = $nestedData;          
        }

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "total_orders"    => $totalOrders,
            "total_amount"    => number_format($totalAmount,2),
            "data"            => $data
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