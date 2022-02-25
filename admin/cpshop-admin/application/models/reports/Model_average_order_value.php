<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_average_order_value extends CI_Model {  	

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_paid_orders_with_branch');
    }

    public function getShopName($shop_id){
        $row = $this->db->query("SELECT * FROM sys_shops where id=$shop_id")->row();
        return $row->shopname;
    }

    public function getBranches($shop_id){
        $shop_id = $this->db->escape($shop_id);
        return $this->db->query("SELECT a.branchid, b.branchname FROM sys_branch_mainshop a JOIN sys_branch_profile b on a.branchid = b.id WHERE mainshopid = $shop_id");
    }
   
    public function get_order_values($fromdate,$todate,$shop_id,$branch_id,$pmethodtype=''){

        $fromdate = date('Y-m-d',strtotime($fromdate));
        $todate = date('Y-m-d',strtotime($todate));       

        $is_single_date = false;
        if($fromdate == $todate){
            $is_single_date = true;
        }
        
        //shop branch filter
        $shop_filter = "";
		$branch_filter = "";        
        $branch_filter_2 = "";
        
        $usertype = 0;

        if($shop_id != "all" && $shop_id != 0){

            $shop_filter = "  sys_shop = $shop_id";            

            if($branch_id != "all"){
                if($branch_id == "main"){
                    $branch_id = 0; $usertype = 1;
                }
                else{
					$branch_filter = "  branch_id = $branch_id";
					$branch_filter_2 = "  branchid = $branch_id";
                }
            }           
        }

        switch ($pmethodtype) {
            case 'op':
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('paypanda')";
                break;
            case 'mp':
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
            $date_format = "DATE(date_ordered)";
            $group_format = " GROUP BY DATE(date_ordered)";  
        }		
        
        $fromdate = $this->db->escape($fromdate.' 00:00:00');
        $todate = $this->db->escape($todate.' 23:59:59');
    
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
        'group_by' => ($is_single_date) ? " GROUP BY HOUR(date_ordered)" : " GROUP BY DATE(date_ordered)",
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
        return $result;

        //get sql tamplate from slqhelper in library
        /*
        $fromdate = $this->db->escape($fromdate);
        $todate = $this->db->escape($todate.' 23:59:59');
        return $this->model_paid_orders_with_branch->paid_order_with_branch_query(['reference_num','date_ordered','date_ordered_time','date_ordered_hr', 'total_amount'], [
            'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
            'fromdate' => $fromdate,
            'todate' => $todate,
            'shop_filter' => $shop_filter,
            'branch_filter' => $branch_filter,
            'branch_id' => $branch_id,
            'pmethodtype_filter' => $pmethodtype_filter,
            'pmethodtype' => $pmethodtype,
            'group_by' => $group_format,
            'order_by' => [
              'column' => 'date_ordered',
              'dir'    => 'asc'
            ]
          ], 0, 'date_ordered','total_amount');
        
        
        $sql = "SELECT $date_format as `date_ordered`,
                    SUM(t.total_amount) as `total_amount`
                    FROM ($sql_helper) as `t` $group_format";

        $sql.=" ORDER BY t.date_ordered asc";        

        return $this->db->query($sql)->result_array();
        */
    }

    public function get_order_values_table($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype, $exportable = false){        

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));        

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
            0 => 'date_ordered',
            1 => 'shopname',
            2 => 'branchname',
            3 => 'total_amount'
        );        
        
        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";        
        $date_filter = "";
        $pmethodtype_filter = "";

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

        switch ($pmethodtype) {
            case 'op':
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('paypanda')";
                break;
            case 'mp':
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('manual order')";
                break;
            default:
                $pmethodtype_filter = " AND LCASE(payment_method) IN ('paypanda','manual order')";
                break;
        }

        //summary and logs query
        if($type == "summary"){            
            if($is_single_date){
                $date_format = "DATE_FORMAT(date_ordered, '%Y-%m-%d %H:00')";
                $group_format = " GROUP BY HOUR(date_ordered)";                
            }
            else{
                $date_format = "DATE(date_ordered)";
                $group_format = " GROUP BY DATE(date_ordered)";  
            }
            
            $fromdate = $this->db->escape($fromdate);
            $todate = $this->db->escape($todate.' 23:59:59');

            $sql = "SELECT $date_format as `date_ordered`, 
                        shopname, branchname,
                        SUM(total_amount) as 'total_amount'
                        FROM (".$this->model_paid_orders_with_branch->view_paid_orders_with_branch().") AS view_paid_orders_with_branch 
                        WHERE date_ordered between $fromdate AND $todate
                        $shop_filter $branch_filter $group_format";
            
        }
        else if($type == "logs"){   
            
            $fromdate = $this->db->escape($fromdate);
            $todate = $this->db->escape($todate.' 23:59:59');

            $sql = "SELECT date_ordered, shopname, branchname, total_amount 
                    FROM (".$this->model_paid_orders_with_branch->view_paid_orders_with_branch().") AS view_paid_orders_with_branch 
                    WHERE date_ordered BETWEEN $fromdate AND $todate
                    $shop_filter $branch_filter";
        
        }

        $res = $this->db->query($sql);

        $totalData = (!is_bool($res)) ? $res->num_rows() : 0;
        $totalFiltered = $totalData;
        $total_count = $totalData;

        $totalAmount = getTotalInArray($res->result_array(), 'total_amount');

        if($requestData != null){                            
            $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];                 
        }        
        
        if(!$exportable){            
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";            
        }    

        $query = $this->db->query($sql);
        
        $data = array();
        foreach( $query->result_array() as $row )
        {
            $nestedData=array();    
            
            $nestedData[] = $row['date_ordered'];               
            $nestedData[] = $row['shopname'];
            $nestedData[] = $row['branchname'];
            $nestedData[] = number_format($row['total_amount'],2);	  
    
            $data[] = $nestedData;
        }

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),                 
            "total_amount"    => number_format($totalAmount,2),            
            "shop_id"         => $shop_id,
            "branch_id"       => $branch_id,
            "query"           => $sql,
            "data"            => $data,
            "columns"         => $columns,
            "type"            => $type,            
        );
  
        return $json_data;
    }        
}
