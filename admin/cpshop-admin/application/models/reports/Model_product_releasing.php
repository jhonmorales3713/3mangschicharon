<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_product_releasing extends CI_Model {

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

    public function main_nav_categories() {
		$sql = "SELECT `main_nav_id`, `main_nav_desc` FROM `cp_main_navigation` WHERE `enabled` >= 1";
		return $this->db2->query($sql);
	}
    
    public function get_product_releasing_data($fromdate, $todate, $shop_id, $branch_id, $release_type){

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));

        $dates = array($fromdate,$todate);

        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";
        $release_filter = "";

        if($shop_id != "all"){

            $shop_filter = " AND b.shop_id = $shop_id";

            if($branch_id != "all"){
                if($branch_id == "main"){
                    $branch_filter = " AND b.branch_id = 0";
                }
                else{
                    $branch_filter = " AND b.branch_id = $branch_id";
                }
            }           
        }

        if($release_type == "released"){
            $release_filter = " AND b.order_status = 's'";
        }
        else{
            $release_filter = " AND b.order_status != 's'";
        }
     
        $sql = "SELECT c.itemname, b.shopname, b.branchname, SUM(a.quantity) - SUM(IFNULL(d.quantity, 0)) AS 'quantity'
                FROM app_sales_order_logs a
                LEFT JOIN view_paid_orders_with_branch b on a.order_id = b.order_id
                LEFT JOIN sys_products c on a.product_id = c.Id
                LEFT JOIN `app_refund_orders_details` d ON
                    a.product_id = d.product_id AND b.reference_num = d.refnum AND is_checked = 1
                LEFT JOIN `app_refund_orders_summary` s ON d.summary_id = s.id AND s.status = 1
                WHERE b.date_ordered BETWEEN ? AND ? $shop_filter $branch_filter $release_filter
                GROUP BY b.shop_id, b.branch_id, a.product_id                
                ORDER BY quantity desc
                LIMIT 10";

        $query = $this->db2->query($sql,$dates);
        return $query->result_array();
    }

    public function get_product_releasing_table($fromdate, $todate, $shop_id, $branch_id, $release_type, $exportable = false){   

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));

        $dates = array($fromdate,$todate);
        
        if(!$exportable){
            $requestData = $_REQUEST;
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }

        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";
        $release_filter = "";

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

        if($release_type == "released"){
            $release_filter = " AND b.order_status = 's'";
        }
        else{
            $release_filter = " AND b.order_status != 's'";
        }

        $columns = array(
            0 => 'shopname',
            1 => 'branchname',
            2 => 'itemname',
            3 => 'quantity',
        );
              
        $sql = "SELECT c.itemname, b.shopname, b.branchname, SUM(a.quantity) - SUM(IFNULL(d.quantity, 0)) AS 'quantity'
                FROM app_sales_order_logs a
                LEFT JOIN view_paid_orders_with_branch b on a.order_id = b.order_id
                LEFT JOIN sys_products c on a.product_id = c.Id
                LEFT JOIN `app_refund_orders_details` d ON
                    a.product_id = d.product_id AND b.reference_num = d.refnum AND is_checked = 1
                LEFT JOIN `app_refund_orders_summary` s ON d.summary_id = s.id AND s.status = 1
                WHERE b.date_ordered BETWEEN ? AND ? $shop_filter $branch_filter $release_filter
                GROUP BY b.shop_id, b.branch_id, a.product_id";
       
        $query = $this->db2->query($sql,$dates);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  
        $total_count = $totalData;   

        $total_quantity = getTotalInArray($query->result_array(), 'quantity');

        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];        
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }
  
        $query = $this->db2->query($sql,$dates);        
        
        $data = array();
        foreach( $query->result_array() as $row )
        {
            $nestedData=array();    
                
            $nestedData[] = $row['shopname'];               
            $nestedData[] = $row['branchname'];
            $nestedData[] = $row['itemname'];
            $nestedData[] = $row['quantity'];
        
            $data[] = $nestedData;
                                   
        }              

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),            
            "query"           => $sql,
            "data"            => $data,
            "columns"         => $columns,
            "_search"         => $requestData,
            "total_quantity"  => $total_quantity,            
        );
  
        return $json_data;
    }
  
    

}