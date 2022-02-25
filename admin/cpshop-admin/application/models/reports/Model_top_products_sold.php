<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_top_products_sold extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_paid_orders_with_branch', 'model_powb');   
        $this->load->model('model_refund_orders', 'model_refunds');
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

    public function get_top_products_sold($fromdate,$todate,$shop_id,$branch_id,$pmethodtype = '',$limit = 10){

        $fromdate = $this->db2->escape($fromdate);
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

        $res = $this->get_Items($fromdate, $todate, $shop_id, $branch_id, $pmethodtype);

        // print_r($res); exit();

        uasort($res, build_sorter('qty', 'desc'));
        return array_slice($res, 0, 10);
    }

    public function get_top_products_sold_in($fromdate,$todate,$shop_id,$branch_id,$array = null,$pmethodtype = ''){

        $fromdate = $this->db2->escape($fromdate);
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
        $res = [];

        if($array != null){
            $prod_ids = array_column($array, 'id');
            $temp_res = $this->get_Items($fromdate, $todate, $shop_id, $branch_id, $pmethodtype);

            foreach ($temp_res as $key => $value) {
                if (in_array($value['id'], $prod_ids)) {
                    $res[] = $value;
                }
            }

            return $res;

        } else {
            return [];
        }
    }

    public function get_top_products_sold_table($fromdate, $todate, $shop_id, $branch_id, $pmethodtype = '', $exportable = false){      

        $fromdate = date("Y-m-d", strtotime($fromdate));
        $todate = date("Y-m-d", strtotime($todate));  

        $fromdate = $this->db2->escape($fromdate);
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

        $requestData;
    
        if(!$exportable){
            $requestData = $_REQUEST;	
            $date_from = $fromdate;
            $date_to = $todate;
        }
        else{
            $requestData = url_decode(json_decode($this->input->post("_search")));
        }
        
        $columns = array(
            0 => 'itemname',
            1 => 'qty',
            2 => 'total_sales',
            3 => 'date_ordered'
        );
      
        $temp_res = $this->get_Items($fromdate, $todate, $shop_id, $branch_id, $pmethodtype);
        // print_r($temp_res); exit();
        $total_count = $totalFiltered = $totalData = count($temp_res);

        $t_qty = 0;
        $t_sales_amount=0;

        foreach($temp_res as $row){        
            $t_qty += intval($row['qty']);
            $t_sales_amount += floatval($row['totamt']);
        }
    
        $data = array();
        $count = 0;
        $st_qty = 0;        

        foreach( $temp_res as $row )
        {

            $itemname = $row['itemname'];

            $itemInfo = $this->getItemNameInfo($row['itemname']);
            foreach($itemInfo as $row_b){        
                 if($row_b['parent_product_id'] !== NULL){
                    $parent = $this->getParentProductName($row_b['parent_product_id']);
                    foreach($parent as $row_a){        
                        $itemname = $row_a['itemname']."(".$row['itemname'].")";
                    }
                }
            }

            
            $nestedData=array();      
            
            $nestedData[] = $itemname;   
            $nestedData[] = number_format($row['qty'],0);
            $nestedData[] = number_format($row['totamt'],2);
            $nestedData[] = $row['date_ordered'];
    
            $data[] = $nestedData;

            $st_qty += intval($row['qty']);
        }

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "total_orders" => number_format($total_count,0),
            "t_sales_amount" => number_format($t_sales_amount,2),
            "st_qty"      => $st_qty,
            "t_qty"       => $t_qty,
            "data"       => $data
        );
    
        return $json_data;
    }
        
    private function get_SalesLogs($op_ids, $res_op)
    {
        $res_op_ids = array_column($res_op, 'order_id');

        $sql = "SELECT CONCAT(order_id, '.', product_id) AS id, order_id, product_id, itemname, SUM(quantity) AS qty, otherinfo, SUM(a.total_amount) as totamt
                FROM `app_sales_order_logs` a
                LEFT JOIN `sys_products` b ON b.id = a.product_id
                WHERE order_id IN ('$op_ids')
                GROUP BY product_id";
        $result = [];
        foreach ($this->db2->query($sql)->result_array() as $key => $value) {
            if (in_array($value['order_id'], $res_op_ids)) {
                $value = array_merge($value, $res_op[array_search($value['order_id'], $res_op_ids)]);
                // $value['total_amount'] = $res_op[array_search($value['order_id'], $res_op_ids)]['total_amount'];
            }
            $result[] = $value;
        }

        return $result;
    }

     private function get_SalesLogs_filtered($op_ids, $res_op)
    {
        $res_op_ids = array_column($res_op, 'order_id');

        $sql = "SELECT CONCAT(order_id, '.', a.product_id) AS id, order_id, a.product_id, itemname, SUM(quantity) AS qty, otherinfo, SUM(a.total_amount) as totamt
                FROM `app_sales_order_logs` a
                LEFT JOIN `sys_products` b ON b.id = a.product_id
                LEFT JOIN sys_shops e 
                    ON b.sys_shop = e.id 
                    AND e.status > 0 
                LEFT JOIN sys_product_category c 
                    ON b.cat_id = c.id 
                    AND c.status > 0 
                LEFT JOIN sys_shops code 
                    ON b.sys_shop = code.id 
                LEFT JOIN sys_products_images d 
                    ON b.Id = d.product_id 
                    AND d.arrangement = 1 
                    AND d.status = 1 
                WHERE order_id IN ('$op_ids') AND b.enabled = '1' 
                AND b.parent_product_id IS NULL 
                GROUP BY a.product_id";
        $result = [];
        foreach ($this->db2->query($sql)->result_array() as $key => $value) {
            if (in_array($value['order_id'], $res_op_ids)) {
                $value = array_merge($value, $res_op[array_search($value['order_id'], $res_op_ids)]);
                // $value['total_amount'] = $res_op[array_search($value['order_id'], $res_op_ids)]['total_amount'];
            }
            $result[] = $value;
        }

        return $result;
    }

    private function get_ManualLogs($mp_ids, $res_mp)
    {
        $res_mp_ids = array_column($res_mp, 'reference_num');

        $sql = "SELECT order_id, product_id, itemname, SUM(quantity) AS qty, otherinfo 
                FROM `app_manual_order_logs` a
                LEFT JOIN `sys_products` b ON b.id = a.product_id
                WHERE order_id IN ('$mp_ids')
                GROUP BY product_id";

        $result = [];
        foreach ($this->db2->query($sql)->result_array() as $key => $value) {
            if (in_array($value['order_id'], $res_mp_ids)) {
                $value = array_merge($value, $res_mp[array_search($value['order_id'], $res_mp_ids)]);
                // $value['total_amount'] = $res_mp[array_search($value['order_id'], $res_op_ids)]['total_amount'];
            }
            $result[] = $value;
        }

        return $result;
    }

    private function get_ManualLogs_filtered($mp_ids, $res_mp)
    {
        $res_mp_ids = array_column($res_mp, 'reference_num');

        $sql = "SELECT order_id, a.product_id, itemname, SUM(quantity) AS qty, otherinfo 
                FROM `app_manual_order_logs` a
                LEFT JOIN `sys_products` b ON b.id = a.product_id
                LEFT JOIN sys_shops e 
                    ON b.sys_shop = e.id 
                    AND e.status > 0 
                LEFT JOIN sys_product_category c 
                    ON b.cat_id = c.id 
                    AND c.status > 0 
                LEFT JOIN sys_shops code 
                    ON b.sys_shop = code.id 
                LEFT JOIN sys_products_images d 
                    ON b.Id = d.product_id 
                    AND d.arrangement = 1 
                    AND d.status = 1 
                WHERE order_id IN ('$mp_ids') AND b.enabled = '1' 
                AND b.parent_product_id IS NULL 
                GROUP BY a.product_id";

        $result = [];
        foreach ($this->db2->query($sql)->result_array() as $key => $value) {
            if (in_array($value['order_id'], $res_mp_ids)) {
                $value = array_merge($value, $res_mp[array_search($value['order_id'], $res_mp_ids)]);
                // $value['total_amount'] = $res_mp[array_search($value['order_id'], $res_op_ids)]['total_amount'];
            }
            $result[] = $value;
        }

        return $result;
    }

    private function get_Items($fromdate, $todate, $shop_id, $branch_id, $pmethodtype)
    {
        $res = []; $shop_filter = ""; $branch_filter = ""; $usertype = 0; $prod_ids_ix = [];
        //admin, seller, branch filter
        if($this->session->sys_shop_id != 0){
            $shop_filter ="sys_shop =".$shop_id;
            if($branch_id != "all" && $branch_id != null){                
                if ($branch_id == 'main') {
                    $branch_id = 0; $usertype = 1;
                    // $shop_filter = "branch_id = 0";
                } else {
                    $branch_filter = "branch_id =".$branch_id;
                    $usertype = 2;
                    // $shop_filter = "branch_id = $branch_id";
                }
            }

        }
        else{
            //applies on admin user
            if($shop_id != "all" && $shop_id != 0 && $shop_id != "0"){
                $shop_filter ="sys_shop =".$shop_id;
            }

            if($branch_id != "all" && $branch_id != null){                
                if ($branch_id == 'main') {
                    $branch_id = 0; $usertype = 1;
                    // $shop_filter = "branch_id = 0";
                } else {
                    $branch_filter = "branch_id =".$branch_id;
                    $usertype = 2;
                    // $shop_filter = "branch_id = $branch_id";
                }
            }
        }

        if (in_array($pmethodtype, ['', 'op'])) {
            // get online orders
            $res_op = $this->model_powb->paid_order_with_branch_query(
                ['order_id', 'total_amount', 'date_ordered'],
                [
                    'fromdate' => $fromdate,
                    'todate'   => $todate,
                    'shop_id'   => $shop_id,
                    'branch_id'   => $branch_id,
                    'pmethodtype' => 'op',
                    'filters'   => [
                        'shop_filter' => $shop_filter,
                        'branch_filter' => $branch_filter,
                        'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
                    ],
                    'group_by' => "GROUP BY order_id"
                ], $usertype, false, false
            );
            // print_r($res_op); exit();
            $op_ids = implode("','", array_column($res_op, 'order_id'));
            $refunds = $this->model_refunds->get_RefundQtyByRefnum($op_ids);
            $ref_ids = array_column($refunds, 'id');
            // $sales_logs = $this->get_SalesLogs($op_ids, $res_op);
            $sales_logs = $this->get_SalesLogs_filtered($op_ids, $res_op);
            foreach ($sales_logs as $key => $value) {
                if (isset($refunds[array_search($value['id'], $ref_ids)])) {
                    if ($value['id'] == $refunds[array_search($value['id'], $ref_ids)]['id']) {
                        $value['qty'] -= $refunds[array_search($value['id'], $ref_ids)]['qty'];
                    }
                }

                if (!array_key_exists($value['product_id'], $prod_ids_ix)) {
                    $prod_ids_ix[$value['product_id']] = count($prod_ids_ix);
                    $value['id'] = $value['product_id'];
                    $res[] = $value;
                } else {
                    $res[$prod_ids_ix[$value['product_id']]]['qty'] += $value['qty'];
                }
            }
        }

        if (in_array($pmethodtype, ['', 'mp'])) {
            // get manual orders
            $res_mp = $this->model_powb->paid_order_with_branch_query(
                ['reference_num', 'total_amount', 'date_ordered'],
                [
                    'fromdate' => $fromdate,
                    'todate'   => $todate,
                    'shop_id'   => $shop_id,
                    'branch_id'   => $branch_id,
                    'pmethodtype' => 'mp',
                    'filters'   => [
                        'shop_filter' => $shop_filter,
                        'branch_filter' => $branch_filter,
                        'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
                    ],
                    'group_by' => "GROUP BY reference_num"
                ], $usertype, false, false
            );
            // print_r($res_mp); exit();
            $mp_ids = implode("','", array_column($res_mp, 'reference_num'));
            // $manual_logs = $this->get_ManualLogs($mp_ids, $res_mp);
            $manual_logs = $this->get_ManualLogs_filtered($mp_ids, $res_mp);
            // print_r($manual_logs);
            foreach ($manual_logs as $key => $value) {
                if (!array_key_exists($value['product_id'], $prod_ids_ix)) {
                    $prod_ids_ix[$value['product_id']] = count($prod_ids_ix);
                    $value['id'] = $value['product_id'];
                    $res[] = $value;
                } else {
                    $res[$prod_ids_ix[$value['product_id']]]['qty'] += $value['qty'];
                }
            }
        }

        return $res;
    }


    private function getItemNameInfo($itemname){
        $query="SELECT * FROM sys_products where itemname=?";
        $params = array($itemname);

        return $this->db->query($query,$params)->result_array();;
    }

    private function getParentProductName($parent_id){
        $query="SELECT * FROM sys_products where id=?";
        $params = array($parent_id);

        return $this->db->query($query,$params)->result_array();;
    }
}
