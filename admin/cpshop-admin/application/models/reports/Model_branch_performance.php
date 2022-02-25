<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_branch_performance extends CI_Model {

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

    function views(){
        $pre_sql = "SELECT
                    `a`.`id` AS `order_id`,
                    `a`.`reference_num` AS `reference_num`,
                    `a`.`date_ordered` AS `date_ordered`,
                    `a`.`payment_date` AS `payment_date`,
                    `a`.`order_status` AS `order_status`,
                    `a`.`date_shipped` AS `date_shipped`,
                    `a`.`date_received` AS `date_received`,
                    `a`.`date_fulfilled` AS `date_fulfilled`,
                    `a`.`name` AS `name`,
                    `a`.`payment_method` AS `payment_method`,
                    `a`.`sys_shop` AS `shop_id`,
                    `b`.`shopname` AS `shopname`,
                    0 AS `branch_id`,
                    'Main' AS `branchname`,
                    `a`.`total_amount` AS `total_amount`,
                    IF(
                        `c`.`status` = 1,
                        c.refund_amount,
                        ''
                    ) AS `amount_refunded`
                    FROM
                    (
                        (
                        `app_sales_order_details` `a`
                        LEFT JOIN `sys_shops` `b`
                            ON (`a`.`sys_shop` = `b`.`id`)
                        )
                        LEFT JOIN
                        (SELECT
                            `det`.`refnum` AS `refnum`,
                            `det`.`sys_shop` AS `sys_shop`,
                            `det`.`branchid` AS `branchid`,
                            SUM(det.amount) AS refund_amount,
                            `summary`.`status` AS `status`
                        FROM
                            (
                            `app_refund_orders_details` `det`
                            LEFT JOIN `app_refund_orders_summary` `summary`
                                ON (
                                `det`.`summary_id` = `summary`.`id`
                                )
                            )
                        WHERE det.is_checked = 1
                            AND summary.status = 1
                        GROUP BY summary.refnum,
                            det.sys_shop,
                            det.branchid) `c`
                        ON (
                            `a`.`reference_num` = `c`.`refnum`
                            AND `c`.`sys_shop` = `a`.`sys_shop`
                            AND `c`.`branchid` = 0
                        )
                    )
                    WHERE `a`.`status` = 1
                    AND `a`.`payment_status` = 1
                    AND ! (
                        `a`.`reference_num` IN
                        (SELECT
                        `sys_branch_orders`.`orderid`
                        FROM
                        (
                            `sys_branch_orders`
                            LEFT JOIN `sys_branch_mainshop` `ms`
                            ON (
                                `ms`.`branchid` = `sys_branch_orders`.`branchid`
                            )
                        )
                        WHERE `ms`.`mainshopid` = `a`.`sys_shop`)
                    )
                    HAVING total_amount != amount_refunded
                    UNION
                    SELECT
                    `c`.`id` AS `order_id`,
                    `a`.`orderid` AS `orderid`,
                    `c`.`date_ordered` AS `date_ordered`,
                    `c`.`payment_date` AS `payment_date`,
                    `c`.`order_status` AS `order_status`,
                    `c`.`date_shipped` AS `date_shipped`,
                    `c`.`date_received` AS `date_received`,
                    `c`.`date_fulfilled` AS `date_fulfilled`,
                    `c`.`name` AS `name`,
                    `c`.`payment_method` AS `payment_method`,
                    `b`.`mainshopid` AS `shop_id`,
                    `d`.`shopname` AS `shopname`,
                    `a`.`branchid` AS `branchid`,
                    `e`.`branchname` AS `branchname`,
                    `c`.`total_amount` AS `total_amount`,
                    IF(
                        `f`.`status` = 1,
                        f.refund_amount,
                        ''
                    ) AS `amount_refunded`
                    FROM
                    (
                        (
                        (
                            (
                            (
                                `sys_branch_orders` `a`
                                LEFT JOIN `sys_branch_mainshop` `b`
                                ON (`a`.`branchid` = `b`.`branchid`)
                            )
                            LEFT JOIN `app_sales_order_details` `c`
                                ON (
                                `b`.`mainshopid` = `c`.`sys_shop`
                                AND `a`.`orderid` = `c`.`reference_num`
                                )
                            )
                            LEFT JOIN `sys_shops` `d`
                            ON (`b`.`mainshopid` = `d`.`id`)
                        )
                        LEFT JOIN `sys_branch_profile` `e`
                            ON (`a`.`branchid` = `e`.`id`)
                        )
                        LEFT JOIN
                        (SELECT
                            `det`.`refnum` AS `refnum`,
                            `det`.`sys_shop` AS `sys_shop`,
                            `det`.`branchid` AS `branchid`,
                            SUM(det.amount) AS refund_amount,
                            `summary`.`status` AS `status`
                        FROM
                            (
                            `app_refund_orders_details` `det`
                            LEFT JOIN `app_refund_orders_summary` `summary`
                                ON (
                                `det`.`summary_id` = `summary`.`id`
                                )
                            )
                        WHERE det.is_checked = 1
                            AND summary.status = 1
                        GROUP BY summary.refnum,
                            det.sys_shop,
                            det.branchid) `f`
                        ON (
                            `f`.`refnum` = `c`.`reference_num`
                            AND `f`.`sys_shop` = `c`.`sys_shop`
                            AND `f`.`branchid` = `a`.`branchid`
                        )
                    )
                    WHERE `a`.`status` = 1
                    AND `c`.`status` = 1
                    AND `c`.`payment_status` = 1
                    HAVING total_amount != amount_refunded
                    UNION
                    SELECT
                    `a`.`id` AS `order_id`,
                    `a`.`reference_num` AS `reference_num`,
                    `a`.`date_ordered` AS `date_ordered`,
                    `a`.`payment_date` AS `payment_date`,
                    `a`.`order_status` AS `order_status`,
                    `a`.`date_shipped` AS `date_shipped`,
                    `a`.`date_received` AS `date_received`,
                    `a`.`date_fulfilled` AS `date_fulfilled`,
                    `a`.`name` AS `name`,
                    `a`.`payment_method` AS `payment_method`,
                    `a`.`sys_shop` AS `sys_shop`,
                    `b`.`shopname` AS `shopname`,
                    `a`.`branch_id` AS `branch_id`,
                    `c`.`branchname` AS `branchname`,
                    `a`.`total_amount` AS `total_amount`,
                    '' AS `amount_refunded`
                    FROM
                    (
                        (
                        `app_manual_order_details` `a`
                        LEFT JOIN `sys_shops` `b`
                            ON (`a`.`sys_shop` = `b`.`id`)
                        )
                        LEFT JOIN `sys_branch_profile` `c`
                        ON (`a`.`branch_id` = `c`.`id`)
                    )
                    WHERE `a`.`status` = 1
                    AND `a`.`payment_status` = 1
                    ORDER BY order_id";   

            return $pre_sql;
    }
    
    public function get_branch_performance_data($shop_id,$branch_id,$time_in_seconds){

        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";        
        $time_filter = ""; 
        
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

        if($time_in_seconds != 0){
            if($time_in_seconds < 604800){
                $time_filter = " HAVING average <= $time_in_seconds";         
            }
            else{
                $time_filter = " HAVING average >= $time_in_seconds";         
            }            
        }

              

        $sql = "SELECT shopname, branchname, 
                SUM(time_to_sec(timediff(date_shipped,payment_date))) as total_delivery_time,
                SUM(1) as total_deliveries,
                ABS((SUM(time_to_sec(timediff(date_shipped,payment_date)))) / SUM(1)) as average
                FROM (".$this->views().") AS view_paid_orders_with_branch
                WHERE order_status = 's' $shop_filter $branch_filter
                GROUP BY shop_id, branch_id
                $time_filter
                ORDER BY average desc";  

        $query = $this->db2->query($sql);
        
        $result = array();
        foreach($query->result_array() as $row){
            $result_row = array();

            $result_row['shopname'] = $row['shopname'];
            $result_row['branchname'] = $row['branchname'];
            
            //$hours = floatval($row['total_delivery_time'])/3600;
            $average = intval($row['average']);
            
            $result_row['average'] = $average;
                        
            $result[] = $result_row;            
        }        
        return $result;
    }

    public function get_branch_performance_table($shop_id, $branch_id, $time_in_seconds, $exportable = false){   
        
        $first_run = $this->input->post('first_run');

        $token_session      = $this->session->userdata('token_session');
        $token = en_dec('en', $token_session);
    
        if(!$exportable){
            $requestData = $_REQUEST;
        } else {
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }

        $columns = array(
            0 => 'shopname',
            1 => 'branchname',
            2 => 'total_deliveries',
            3 => 'average_delivery_time',
        );  

        //shop branch filter
        $shop_filter = "";
        $branch_filter = ""; 
        $time_filter = "";
        
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
        
        if($time_in_seconds != 0){
            if($time_in_seconds < 604800){
                $time_filter = " HAVING average <= $time_in_seconds";         
            }
            else{
                $time_filter = " HAVING average >= $time_in_seconds";         
            }            
        }       
              
        $sql = "SELECT shopname, branchname, shop_id, branch_id, date_shipped, date_ordered, payment_date,
                ABS(SUM(time_to_sec(timediff(date_shipped,payment_date)))) as total_delivery_time,
                ABS(SUM(1)) as total_deliveries,                
                ABS((SUM(time_to_sec(timediff(date_shipped,payment_date)))) / sum(1)) as average                
                FROM (".$this->views().") AS view_paid_orders_with_branch
                WHERE order_status = 's' $shop_filter $branch_filter
                GROUP BY shop_id, branch_id
                $time_filter";
                
       
        $query = $this->db2->query($sql);   
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  
        $total_count = $totalData;   

        $total_shipped = getTotalInArray($query->result_array(), 'total_deliveries');
        
        $selected = $columns[$requestData['order'][0]['column']];

        if($selected == "average_delivery_time" || $selected == "shopname"){
            $sql.=" ORDER BY average desc";
        }
        else{
            $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
        }               
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }
  
        $query = $this->db2->query($sql);        
        
        $data = array();
        foreach( $query->result_array() as $row )
        {
            $nestedData=array();    
                
            $nestedData[] = $row['shopname'];               
            $nestedData[] = $row['branchname'];    
            
            $in_seconds = intval($row['total_delivery_time']);
            $total_deliveries = intval($row['total_deliveries']);
            $nestedData[] = $row['total_deliveries'];                
            $nestedData[] = format_seconds_to_time(intval($row['average']));

            $buttons = "";                
            $buttons .= '<a class="dropdown-item" href="'.base_url('reports/Branch_performance/branch_performance_details/'.$row['shop_id'].'/'.$row['branch_id'].'/'.$time_in_seconds.'/'.$token).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>';

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
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),            
            "query"           => $sql,
            "data"            => $data,
            "columns"         => $columns,
            "_search"         => $requestData,
            "total_shipped"   => $total_shipped,  
            "hours"           => $time_in_seconds,
            "selected"         => $selected
        );
  
        return $json_data;
    }        
  
    public function branch_performance_breakdown_table($shop_id, $branch_id, $time_in_seconds, $exportable = false){       
    
        if(!$exportable){
            $requestData = $_REQUEST;
        } else {
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }

        $columns = array(
            0 => 'shopname',
            1 => 'branchname',           
            2 => 'delivery_time',
        );  

        $shop_filter = " AND shop_id = $shop_id";
        $branch_filter = " AND branch_id = $branch_id";
        $time_filter = "";

        if($time_in_seconds != 0){
            if($time_in_seconds < 604800){
                $time_filter = " HAVING time_to_sec(timediff(date_shipped,payment_date)) <= $time_in_seconds";         
            }
            else{
                $time_filter = " HAVING time_to_sec(timediff(date_shipped,payment_date)) >= $time_in_seconds";         
            }            
        }
              
        $sql = "SELECT shopname, branchname, reference_num, payment_date, date_ordered, date_shipped,
                time_to_sec(timediff(date_shipped,payment_date)) as 'delivered_in'
                FROM (".$this->views().") AS view_paid_orders_with_branch
                WHERE order_status = 's' $shop_filter $branch_filter $time_filter";
       
        $query = $this->db2->query($sql);
        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  
        $total_count = $totalData;    

        //$totalAmount = getTotalInArray($query->result_array(), 'total_amount');
        
        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }
  
        $query = $this->db2->query($sql);                

        $total_shipped = getTotalInArray($query->result_array(),'total_deliveries');
        
        $data = array();
        foreach( $query->result_array() as $row )
        {
            $nestedData=array();    
            
            $nestedData[] = $row['shopname'];               
            $nestedData[] = $row['branchname'];    
            
            $time_in_seconds = intval($row['total_delivery_time']);
            $total_deliveries = intval($row['total_deliveries']);
            
            //$nestedData[] = format_seconds_to_time($time_in_seconds);

            $nestedData[] = $row['total_deliveries'];

            $average = abs($time_in_seconds/$total_deliveries);
            
            $nestedData[] = format_seconds_to_time($average);

            $buttons = "";                
            $buttons .= '<a class="dropdown-item" href="'.base_url('reports/Branch_performance/branch_performance_details/'.$row['shop_id'].'/'.$row['branch_id'].'/'.$token).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>';

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
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),            
            "query"           => $sql,
            "data"            => $data,
            "columns"         => $columns,
            "_search"         => $requestData,
            "total_shipped"   => $total_shipped,                 
        );
  
        return $json_data;
    } 

    public function get_branch_performance_breakdown_table($shop_id, $branch_id, $time_in_seconds, $exportable = false){

        if(!$exportable){
            $requestData = $_REQUEST;
        } else {
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }

        $columns = array(
            0 => 'payment_date',
            1 => 'date_shipped',
            2 => 'reference_num',
            3 => 'order_aging',
            4 => 'total_amount',
        );  

        //shop branch filter
        $shop_filter = "";
        $branch_filter = "";         
        $time_filter = "";

        $shop_filter = " AND shop_id = $shop_id";
        $branch_filter = " AND branch_id = $branch_id";                     
        
        if($time_in_seconds != 0){
            if($time_in_seconds < 604800){
                $time_filter = " HAVING order_aging <= $time_in_seconds";         
            }
            else{
                $time_filter = " HAVING order_aging >= $time_in_seconds";         
            }            
        }
              
        $sql = "SELECT shopname, branchname, shop_id, branch_id, reference_num, total_amount, payment_date, date_shipped,
                time_to_sec(timediff(date_shipped,payment_date)) as order_aging
                FROM (".$this->views().") AS view_paid_orders_with_branch
                WHERE order_status = 's' $shop_filter $branch_filter $time_filter";
       
        $query = $this->db2->query($sql);

        $totalData = $query->num_rows();
        $totalFiltered = $totalData;  
        $total_count = $totalData;

        $total_amount = getTotalInArray($query->result_array(), 'total_amount');
        $total_seconds = abs(getTotalInArray($query->result_array(), 'order_aging'));
        
        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'];
        
        if(!$exportable){
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }
  
        $query = $this->db2->query($sql);        
        
        $data = array();
        foreach( $query->result_array() as $row )
        {            
            $nestedData=array();
            
            $order_age = round(intval($row['order_aging']),0);
            
            $nestedData[] = $row['payment_date'];               
            $nestedData[] = $row['date_shipped'];
            $nestedData[] = $row['reference_num'];                    
            $nestedData[] = format_seconds_to_time($order_age);
            $nestedData[] = number_format($row['total_amount'],2);                   
        
            $data[] = $nestedData;                
                                  
        }              
        
        $average = floatval($total_seconds/$totalData);

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),            
            "query"           => $sql,
            "data"            => $data,
            "columns"         => $columns,
            "_search"         => $requestData,
            "total_amount"    => number_format($total_amount,2),
            "average"         => format_seconds_to_time($average),
            "hours"           => $time_in_seconds,
            "total_seconds"   => $total_seconds
        );
  
        return $json_data;
    }
}