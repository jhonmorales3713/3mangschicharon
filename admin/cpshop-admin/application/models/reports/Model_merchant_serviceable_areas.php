<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_merchant_serviceable_areas extends CI_Model {
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Model_paid_orders_with_branch', 'model_powb');
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
  
  public function get_branch_options($id = false) {
    $id = $this->db2->escape($id);
    $query="SELECT * FROM sys_branch_mainshop a LEFT JOIN sys_branch_profile b ON b.id = a.branchid WHERE a.mainshopid = $id";
    return $this->db2->query($query)->result_array();
  }

  public function get_merchant_serviceable_areass_data($fromdate,$todate,$shopid,$branchid,$filtertype, $pmethodtype, $requestData, $exportable = false){
    $paid = $this->sum_all_merchant_serviceable_areas($fromdate,$todate,$shopid,'paid');
    $unpaid = $this->sum_all_merchant_serviceable_areas($fromdate,$todate,$shopid,'unpaid');

    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);

    $columns = array(
      0 => 'count',
      1 => 'shopname',
      2 => 'name',
      3 => 'date_ordered',
      4 => 'reference_num',
      5 => 'total_amount',
      6 => 'status'
    );

    $sql = "SELECT `name`, payment_date, date_ordered, reference_num, total_amount, 1 as payment_status, shopname, branchname FROM `view_paid_orders_with_branch` WHERE DATE(payment_date) BETWEEN $fromdate AND $todate";

    if($shopid != 'all'){
      $sql .= " AND shop_id = '$shopid'";
      if ($branchid > 0) {
        $sql .= " AND branch_id = $branchid";
      } elseif ($branchid == 'main') {
        $sql .= " AND branch_id = 0";
      }
    }
    
    switch ($pmethodtype) {
      case 'op':
        $sql .= " AND LCASE(payment_method) IN ('paypanda')";
        break;
      case 'mp':
        $sql .= " AND LCASE(payment_method) IN ('manual order')";
        break;
      default:
        $sql .= " AND LCASE(payment_method) IN ('paypanda','manual order')";
        break;
    }

    if($filtertype != 'all'){
      switch ($filtertype) {
        case 'paid':
          $sql .= " AND a.payment_status = 1";
          break;
        case 'unpaid':
          $sql .= " AND a.payment_status IN(0,2)";
          break;
      }
    }

    $query = $this->db2->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $total_count = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
    if (!$exportable) {
      $sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }

    $query = $this->db2->query($sql);

    $data = array();
    $count = 0;
    $total_amount = 0;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $count++;
      $is_paid = ($row['payment_status'] == 1) ? "Paid - ".$row['payment_date']:"Unpaid";

      $nestedData[] = $count;
      $nestedData[] = $row['shopname'];
      $nestedData[] = $row['name'];
      $nestedData[] = $row['date_ordered'];
      $nestedData[] = $row['reference_num'];
      $nestedData[] = (!$exportable) ? '<span class="float-right">'.number_format($row['total_amount'],2).'</span>':number_format($row['total_amount'],2);
      $nestedData[] = (!$exportable) ? "<center>$is_paid</center>":$is_paid;

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "total_paid" => number_format($paid,2),
      "total_unpaid" => number_format($unpaid,2),
      "total_transaction_amount" => number_format(($paid + $unpaid),2),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_merchant_serviceable_areass_chart_data($fromdate,$todate,$shopid,$filtertype){
    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);
    $sql = "SELECT SUM(total_amount) as totalamount, DATE(date_ordered) as trandate
      FROM app_sales_order_details WHERE status = 1
      AND (DATE(payment_date) BETWEEN $fromdate AND $todate)";

    if($shopid != 'all'){
      $shopid = $this->db2->escape($shopid);
      $sql .= " AND sys_shop = $shopid";
    }

    if($filtertype != 'all'){
      switch ($filtertype) {
        case 'paid':
          $sql .= " AND payment_status = 1";
          break;
        case 'unpaid':
          $sql .= " AND payment_status IN(0,2)";
          break;
        default:
          $sql .= " AND payment_status IN(0,2)";
          break;
      }
    }

    $sql .= " GROUP BY DATE(date_ordered)";

    $sql2 = "SELECT SUM(total_amount) as totalamount, DATE(date_ordered) as trandate
      FROM app_manual_order_details WHERE status = 1
      AND (DATE(payment_date) BETWEEN $fromdate AND $todate)";

    if($shopid != 'all'){
      $shopid = $this->db2->escape($shopid);
      $sql2 .= " AND sys_shop = $shopid";
    }

    if($filtertype != 'all'){
      switch ($filtertype) {
        case 'paid':
          $sql2 .= " AND payment_status = 1";
          break;
        case 'unpaid':
          $sql2 .= " AND payment_status IN(0,2)";
          break;
        default:
          $sql2 .= " AND payment_status IN(0,2)";
          break;
      }
    }

    $sql2 .= " GROUP BY DATE(date_ordered)";

    $sql .= " UNION $sql2";

    return $this->db2->query($sql);
  }

  public function sum_all_merchant_serviceable_areas($fromdate,$todate,$shopid,$filtertype){
    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);
    $sql = "SELECT SUM(total_amount) as totalamount, DATE(date_ordered) as trandate
      FROM app_sales_order_details WHERE status = 1
      AND (DATE(payment_date) BETWEEN $fromdate AND $todate)";

    if($shopid != 'all'){
      $shopid = $this->db2->escape($shopid);
      $sql .= " AND sys_shop = $shopid";
    }

    if($filtertype != 'all'){
      switch ($filtertype) {
        case 'paid':
          $sql .= " AND payment_status = 1";
          break;
        case 'unpaid':
          $sql .= " AND payment_status IN(0,2)";
          break;
        default:
          $sql .= " AND payment_status IN(0,2)";
          break;
      }
    }

    $sql2 = "SELECT SUM(total_amount) as totalamount, DATE(date_ordered) as trandate
      FROM app_manual_order_details WHERE status = 1
      AND (DATE(payment_date) BETWEEN $fromdate AND $todate)";

    if($shopid != 'all'){
      $shopid = $this->db2->escape($shopid);
      $sql2 .= " AND sys_shop = $shopid";
    }

    if($filtertype != 'all'){
      switch ($filtertype) {
        case 'paid':
          $sql2 .= " AND payment_status = 1";
          break;
        case 'unpaid':
          $sql2 .= " AND payment_status IN(0,2)";
          break;
        default:
          $sql2 .= " AND payment_status IN(0,2)";
          break;
      }
    }

    $sql .= " UNION $sql2";

    return $this->db2->query($sql)->row()->totalamount;
  }

  public function get_totalsales_data_backup ($fromdate, $todate, $shopid = 'all', $branchid, $filtertype, $pmethodtype, $requestData, $exportable = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

    $columns = [
      'payment_date_time', 'date_ordered', 'shopname', 'branchname', 'reference_num', 'payment_method', 'cnt', 'total_amount'
    ];

    $usertype = 0; $shop_filter = ""; $branch_filter = "";

    if ($shopid > 0) {
      $shop_filter = "sys_shop = $shopid";
      if ($branchid == 'main') {
        $branchid = 0; $usertype = 1;
      } elseif ($branchid > 0) {
        $usertype = 2; 
        $branch_filter = "branch_id = $branchid";
      }
    }

    $key_ctrl = "shop_id"; $group_by = "GROUP BY shop_id";
    switch ($filtertype) {
      case 'summary':
        $columns[0] = 'payment_date';
        $group_by .= ($shopid > 0) ? ", branch_id, DATE(payment_date)":", DATE(payment_date)";
        $key_ctrl = ($shopid > 0) ? "shop_id, branch_id, payment_date":"shop_id, payment_date";
        break;
      default:
        $columns[0] = 'payment_date_time';
        $group_by .= ($shopid > 0) ? ", branch_id, payment_date":", payment_date";
        $key_ctrl = ($shopid > 0) ? "shop_id, branch_id, payment_date_time":"shop_id, payment_date_time";
        break;
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      $columns, [
        'fromdate'    => $fromdate,
        'todate'      => $todate,
        'shop_id'     => $shopid,
        'branch_id'   => $branchid,
        'pmethodtype' => $pmethodtype,
        'filters'     => [
          'shop_filter'   => $shop_filter,
          'branch_filter' => $branch_filter,
          'date_filter'   => "payment_date BETWEEN $fromdate AND $todate",
          0               => "total_amount > 0",
          1               => "order_status NOT IN ('rs')",
        ],
        "group_by"    => $group_by,
      ], $usertype, $key_ctrl, false
    );

    $total_count = $totalFiltered = $totalData = count($res);
    $total_amount = array_sum(array_pluck($res, 'total_amount'));
    $sales_count_total = array_sum(array_pluck($res, 'cnt'));
    uasort($res, build_sorter($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir']));
    if (!$exportable) {
      $res = array_slice($res, $requestData['start'], $requestData['length']);
    }

    // print_r($res); exit();
    $data = [];
    foreach($res as $row) {
      $row['total_amount'] = (!$exportable) ? "<div class='text-right'>".number_format($row['total_amount'], 2)."</div>":number_format($row['total_amount'], 2);
      $data[] = [
        $row[$columns[0]], $row['date_ordered'], $row['shopname'], $row['branchname'], $row['reference_num'], $row['payment_method'], $row['cnt'], $row['total_amount']
      ];
    }

    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count, 
      "total_amount" => number_format($total_amount, 2),
      "sales_count_total" => $sales_count_total,
      "data"            => $data
    );

    return $json_data;
  }

  public function get_totalsales_data($fromdate, $todate, $shop_id, $branch_id, $type, $pmethodtype, $exportable = false){        

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
        0 => 'payment_date',
        1 => 'shopname',
        2 => 'branchname',
        2 => 'reference_num',
        3 => 'count_sales',
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
        $date_filter = " WHERE date(a.payment_date) = ?";
    }
    else{
        $dates = array($fromdate, $todate);
        $date_filter = " WHERE a.payment_date BETWEEN ? AND ?";
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
            $date_format = "DATE_FORMAT(a.payment_date, '%Y-%m-%d %H:00')";
            $group_format = " GROUP BY HOUR(a.payment_date)";                
        }
        else{
            $date_format = "DATE(a.payment_date)";
            $group_format = " GROUP BY DATE(a.payment_date)";  
        }

        $fromdate = $this->db2->escape($fromdate);
        $todate = $this->db2->escape($todate.' 23:59:59');

        // $sql=" SELECT $date_format as `date_ordered`,shopname, branchname,
        //         SUM(1) as `total_orders`,
        //         SUM(total_amount) as `total_amount`
        //         FROM view_paid_orders_with_branch 
        //         WHERE date_ordered BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter $group_format";

        if($pmethodtype == 'op'){
            $sql=" SELECT $date_format as `payment_date`, c.shopname as shopname, a.reference_num, a.sys_shop,
                SUM(1) as `count_sales`,
                SUM(a.total_amount) as `total_amount`
                FROM app_sales_order_details AS a
                LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                $leftjoin_branch
                WHERE a.status = 1 AND a.payment_date BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter $group_format ";
        }
        else{
            $sql =" SELECT $date_format as `payment_date`, c.shopname as shopname, a.reference_num, a.sys_shop,
                SUM(1) as `count_sales`,
                SUM(a.total_amount) as `total_amount`,
                case when a.branch_id = 0 then 'Main' else d.branchname end as `branchname`
                FROM app_manual_order_details AS a
                LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                LEFT JOIN `sys_branch_profile` as d ON a.branch_id = d.id
                WHERE a.status = 1 AND a.payment_date BETWEEN $fromdate AND $todate $shop_filter $branch_filter2 $pmethodtype_filter $group_format ";
        }
        
    }
    else if($type == "logs"){   

        if($is_single_date){
            $date_format = "DATE_FORMAT(a.payment_date, '%Y-%m-%d %H:00')";
            $group_format = " GROUP BY HOUR(a.payment_date)";                
        }
        else{
            $date_format = "DATE(a.payment_date)";
            $group_format = " GROUP BY DATE(a.payment_date)";  
        }

        $fromdate = $this->db2->escape($fromdate);
        $todate = $this->db2->escape($todate.' 23:59:59');  

        // $sql=" SELECT $date_format as `date_ordered`, shopname, branchname,
        //         1 as `total_orders`, 
        //         total_amount
        //         FROM view_paid_orders_with_branch WHERE date_ordered BETWEEN $fromdate AND $todate";

        if($pmethodtype == 'op'){
            $sql=" SELECT a.payment_date, c.shopname as shopname, a.reference_num, a.sys_shop,
                    1 as `count_sales`, 
                    a.total_amount
                    FROM app_sales_order_details AS a
                    LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                    $leftjoin_branch
                    WHERE a.status = 1 AND a.payment_date BETWEEN $fromdate AND $todate $shop_filter $branch_filter $pmethodtype_filter ";
        }
        else{
            $sql =" SELECT a.payment_date, c.shopname as shopname, a.reference_num, a.sys_shop,
                    1 as `count_sales`, 
                    a.total_amount,
                    case when a.branch_id = 0 then 'Main' else d.branchname end as `branchname`
                    FROM app_manual_order_details AS a
                    LEFT JOIN `sys_shops` as c ON a.sys_shop = c.id
                    LEFT JOIN `sys_branch_profile` as d ON a.branch_id = d.id
                    WHERE a.status = 1 AND a.payment_date BETWEEN $fromdate AND $todate $shop_filter $branch_filter2 $pmethodtype_filter ";
        }
    }      

    $query = $this->db2->query($sql,$dates);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;
    $total_count = $totalData;

    $count_sales = getTotalInArray($query->result_array(),'count_sales');
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
        
        $nestedData[] = $row['payment_date'];
        $nestedData[] = $row['shopname'];
        $nestedData[] = ($pmethodtype != 'op') ? $row['branchname'] : $this->get_branchname($row['reference_num'], $row['sys_shop']);
        $nestedData[] = $row['reference_num'];
        $nestedData[] = $row['count_sales'];
        $nestedData[] = number_format($row['total_amount'],2);    

        $data[] = $nestedData;          
    }

    $json_data = array(
        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "total_orders"    => $count_sales,
        "total_amount"    => number_format($totalAmount,2),
        "data"            => $data
    );

    return $json_data;
  }

  public function get_rBS_reports_data($fromdate,$todate,$shopid,$filtertype,$pmethodtype, $requestData, $exportable = false){
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

    $columns = ['payment_date','date_ordered','shopname','branchname','reference_num','name', 'payment_method', 'cnt','total_amount'];
    $shop_filter = ""; $group_by = ""; $usertype = 0;
    if($shopid > 0){
      $shop_filter = "sys_shop = $shopid";
    }

    $key_ctrl = "";
    switch ($filtertype) {
      case 'summary':
        $key_ctrl = "shop_id, payment_date";
        $group_by = " GROUP BY shop_id, DATE(payment_date)";
        break;
      case 'logs':
        $columns[0] = 'payment_date_time';
        $key_ctrl = "shop_id, payment_date_time";
        $group_by = " GROUP BY shop_id, payment_date";
        break;
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      $columns,
      [
        'fromdate'    => $fromdate,
        'todate'      => $todate,
        'branch_id'   => '',
        'shop_id'     => $shopid,
        'pmethodtype' => $pmethodtype,
        'filters'     => [
          'shop_filter' => $shop_filter,
          'date_filter' => "payment_date BETWEEN $fromdate AND $todate",
          1               => "order_status NOT IN ('rs')",
        ],
        'group_by'    => $group_by,
      ], $usertype, $key_ctrl, 'total_amount'
    );

    // print_r($sql);
    $total_count = $totalFiltered = $totalData = count($res);
    uasort($res, build_sorter($columns[$requestData['order'][0]['column']],$requestData['order'][0]['dir']));
    if (!$exportable) {
      $res = array_slice($res, $requestData['start'], $requestData['length']);
    }

    $data = [];
    foreach ($res as $key => $row) {
      $row['total_amount'] = (!$exportable) ? "<div class='text-right'>".number_format($row['total_amount'], 2)."</div>":number_format($row['total_amount'], 2);
      $data[] = [
        $row[$columns[0]], $row['date_ordered'], $row['shopname'], $row['branchname'], $row['reference_num'], $row['name'], $row['payment_method'], $row['cnt'], $row['total_amount'],
      ];
    }

    // print_r($data);
    // exit();
    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "data"            => $data
    );

    return $json_data;
  }

  public function get_rBB_reports_data($fromdate,$todate,$shopid,$branchid,$filtertype,$pmethodtype, $requestData, $exportable = false){
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

    $columns = ['payment_date','date_ordered','shopname','branchname','reference_num','name', 'payment_method', 'cnt','total_amount'];
    $shop_filter = ""; $branch_filter = ""; $usertype = 0;
    if($shopid > 0 || $branchid > 0){
      $shop_filter = "sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      }elseif ($branchid == 'main') {
        $usertype = 1; $branchid = 0;
      }
    }

    $key_ctrl = ""; $group_by = "";
    switch ($filtertype) {
      case 'summary':
        $key_ctrl = "branch_id, payment_date";
        $group_by= " GROUP BY branch_id, DATE(payment_date)";
        break;
      case 'logs':
        $columns[0] = 'payment_date_time';
        $key_ctrl = "branch_id, payment_date_time";
        $group_by = " GROUP BY branch_id, payment_date";
        break;
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      $columns,
      [
        'fromdate'        => $fromdate,
        'todate'          => $todate,
        'shop_id'         => $shopid,
        'branch_id'       => $branchid,
        'pmethodtype'     => $pmethodtype,
        'filters'         => [
          'shop_filter'   => $shop_filter,
          'branch_filter' => $branch_filter,
          'date_filter'   => "payment_date BETWEEN $fromdate AND $todate",
          1               => "order_status NOT IN ('rs')",
        ],
        'group_by'        => $group_by,
      ], $usertype, $key_ctrl, 'total_amount'
    );

    // print_r($sql);
    $total_count = $totalFiltered = $totalData = count($res);
    uasort($res, build_sorter($columns[$requestData['order'][0]['column']], $requestData['order'][0]['dir']));
    if (!$exportable) {
      $res = array_slice($res, $requestData['start'], $requestData['length']);
    }
    $data = [];

    foreach ($res as $row) {
      $row['total_amount'] = (!$exportable) ? "<div class='text-right'>".number_format($row['total_amount'], 2)."</div>":number_format($row['total_amount'], 2);
      $data[] = [
        $row[$columns[0]], $row['date_ordered'], $row['shopname'], $row['branchname'], $row['reference_num'], $row['name'], $row['payment_method'], $row['cnt'], $row['total_amount']
      ];
    }

    // print_r($data);
    // exit();
    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "data"            => $data
    );

    return $json_data;
  }

  public function revenueByBranch($fromdate, $todate, $shopid, $branchid, $pmethodtype = '', $dashboard = false, $is_range_tiny = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $table = $dashboard ? ($is_range_tiny ? 'view_sales_14dys':'view_sales_6mons'):'view_paid_orders_with_branch';

    $shop_filter = ""; $branch_filter = ""; $usertype = 0;

    if($shopid > 0){
      $shop_filter = "sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      }elseif ($branchid == 'main') {
        $usertype = 1; $branchid = 0;
      }
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      ['branchname', 'total_amount'],
      [
        'fromdate' => $fromdate, 
        'todate' => $todate,
        'shop_id' => $shopid,
        'branch_id' => $branchid,
        'pmethodtype' => $pmethodtype,
        'filters' => [
          'shop_filter' => $shop_filter,
          'branch_filter' => $branch_filter,
          'date_filter' => "payment_date BETWEEN $fromdate AND $todate",
          1               => "order_status NOT IN ('rs')",
        ],
        'group_by' => "GROUP BY branch_id",
        'order_by' => [
          'column' => 'total_amount',
          'dir'    => 'desc'
        ]
      ], $usertype, 'branch_id', 'total_amount'
    );

    $data = [];
    foreach ($res as $key => $value) {
      $data[] = [
        'branchname' => (strlen($value['branchname']) > 15) ? substr($value['branchname'], 0, 12) . '...':$value['branchname'],
        'amount' => $value['total_amount']
      ];
    }
    return $data;
  }

  public function get_rBL_reports_data($fromdate,$todate,$shopid,$branchid=0, $filtertype,$location, $requestData, $exportable = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

    $columns = ['payment_date_time','shopname','branchname','city','prov','reg','total_amount'];

    $shop_filter = ""; $branch_filter = ""; $usertype = 0;
    if ($shopid > 0) {
      $shop_filter = "sys_shop = $shopid";
      if ($branchid == "main" || $branchid == 0) {
        $usertype = 1; $branchid = 0;
      } elseif ($branchid > 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      }
    }

    $group_by = "GROUP BY $location"; $key_ctrl = "$location";
    $group_by .= ($filtertype == 'summary') ? ", DATE(payment_date)":", payment_date";
    $columns[0] = ($filtertype == 'summary') ? 'payment_date':'payment_date_time';
    $key_ctrl .= ($filtertype == 'summary') ? ", payment_date":", payment_date_time";
    $group_by .= ($shopid > 0) ? ", sys_shop, branch_id":"";
    $key_ctrl .= ($shopid > 0) ? ", shop_id, branch_id":"";

    $res = $this->model_powb->paid_order_with_branch_query(
      $columns, 
      [
        'fromdate'        => $fromdate,
        'todate'          => $todate,
        'shop_id'         => $shopid,
        'branch_id'       => $branchid,
        'pmethodtype'     => '',
        'filters'         => [
          'shop_filter'   => $shop_filter,
          'branch_filter' => $branch_filter,
          'date_filter'   => "payment_date BETWEEN $fromdate AND $todate",
          1               => "order_status NOT IN ('rs')",
        ],
        'group_by'        => $group_by,
      ], $usertype, $key_ctrl, 'total_amount'
    );
    $city_codes = implode("','", array_column($res, 'city'));
    $cities = $this->get_cityNameByIds($city_codes);
    $city_ids = array_column($cities, 'id');
    $prov_codes = implode("','", array_column($res, 'prov'));
    $provs = $this->get_provsByIds($prov_codes);
    $prov_ids = array_column($provs, 'id');
    $reg_code = implode("','", array_column($res, 'reg'));
    $regs = $this->get_regionsByIds($reg_code);
    $reg_ids = array_column($regs, 'id');

    $temp_res = [];
    foreach ($res as $value) {
      $city = (!empty($cities[array_search($value['city'], $city_ids)]['name'])) ? title_case($cities[array_search($value['city'], $city_ids)]['name']) : 'None';
      $prov = (!empty($provs[array_search($value['prov'], $prov_ids)]['name'])) ? $provs[array_search($value['prov'], $prov_ids)]['name'] : 'None';
      $regi = (!empty($regs[array_search($value['reg'], $reg_ids)]['name'])) ? $regs[array_search($value['reg'], $reg_ids)]['name'] : 'None';
      
      $loc = "";
      switch ($location) {
        case 'city':
          $loc = "$city, $prov, $regi";
          break;
        case 'prov':
          $loc = "$prov, $regi";
          break;
        case 'reg':
          $loc = "$regi";
          break;
      }

      $temp_res[] = [
        'payment_date' => $value[$columns[0]], 
        'shopname'     => $value['shopname'], 
        'branchname'   => $value['branchname'], 
        'loc'          => $loc, 
        'total_amount' => $value['total_amount']
      ];
    }
    $res_col = ['payment_date', 'shopname', 'branchname', 'loc', 'total_amount'];
    $total_count = $totalFiltered = $totalData = count($temp_res);
    uasort($temp_res, build_sorter($res_col[$requestData['order'][0]['column']], $requestData['order'][0]['dir']));
    if (!$exportable) {
      $temp_res = array_slice($temp_res, $requestData['start'], $requestData['length']);
    }

    $data = [];
    foreach ($temp_res as $key => $row) {
      $row['total_amount'] = (!$exportable) ? "<div class='text-right'>".number_format($row['total_amount'], 2)."</div>":number_format($row['total_amount'], 2);
      $data[] = array_flatten($row);
    }

    // print_r($data);
    // exit();
    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "data"            => $data
    );

    return $json_data;
  }

  public function revenueByLocation($fromdate, $todate, $shopid, $branchid = '', $pmethodtype = '', $rbl_filter = 'city', $dashboard = false, $is_range_tiny = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $table = $dashboard ? ($is_range_tiny ? 'view_sales_14dys':'view_sales_6mons'):'view_paid_orders_with_branch';

    $shop_filter = ""; $branch_filter = ""; $usertype = 0;
    if ($shopid > 0) {
      $shop_filter = "sys_shop = $shopid";
      if ($branchid == "main" || $branchid == 0) {
        $usertype = 1; $branchid = 0;
      } elseif ($branchid > 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      } 
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      [$rbl_filter, 'total_amount'],
      [
        'fromdate' => $fromdate, 
        'todate' => $todate,
        'shop_id' => $shopid,
        'branch_id' => $branchid,
        'pmethodtype' => $pmethodtype,
        'filters' => [
          'shop_filter' => $shop_filter,
          'branch_filter' => $branch_filter,
          'date_filter' => "payment_date BETWEEN $fromdate AND $todate",
          1               => "order_status NOT IN ('rs')",
        ],
        'group_by' => "GROUP BY $rbl_filter",
        'order_by' => [
          'column' => 'total_amount',
          'dir'    => 'desc'
        ]
      ], $usertype, $rbl_filter, 'total_amount'
    );

    $loc_ids = array_column($res, $rbl_filter); $loc_names = [];
    switch ($rbl_filter) {
      case 'city':
        $loc_names = $this->get_cityNameByIds(implode("','", $loc_ids));
        break;
      case 'prov':
        $loc_names = $this->get_provsByIds(implode("','", $loc_ids));
        break;
      case 'reg':
        $loc_names = $this->get_regionsByIds(implode("','", $loc_ids));
        break;
    }
    $loc_ids = array_column($loc_names, 'id');
    $data = []; $labels = [];

    // print_r($res);
    // print_r($loc_names);
    // print_r($loc_ids);
    // exit();
    foreach ($res as $key => $row) {
      $loc_index = array_search($row[$rbl_filter], $loc_ids);
      if (isset($loc_names[$loc_index])) {
          $data[] = $row;
          $labels[] = explode(', ', $loc_names[$loc_index]['name']);
      }
    }

    return [
      'data' => $data,
      'labels' => $labels,
    ];
  }

  private function get_cityNameByIds($ids)
  {
    $sql = "SELECT citymunCode as id, citymunDesc as `name` FROM sys_citymun WHERE citymunCode IN ('$ids')";
    return $this->db2->query($sql)->result_array();
  }

  private function get_provsByIds($ids)
  {
    $sql = "SELECT provCode as id, provDesc as `name` FROM sys_prov WHERE provCode IN ('$ids')";
    return $this->db2->query($sql)->result_array();
  }

  private function get_regionsByIds($ids)
  {
    $sql = "SELECT regCode as id, regDesc as `name` FROM sys_region WHERE regCode IN ('$ids')";
    return $this->db2->query($sql)->result_array();
  }

  public function get_oblr_data($fromdate,$todate,$shopid,$branchid=0, $filtertype,$location, $requestData, $exportable = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));

    $columns = ['date_ordered','shopname','branchname','city','prov','reg','cnt','reference_num','order_status'];

    $shop_filter = ""; $branch_filter = ""; $usertype = 0;
    if ($shopid > 0) {
      $shop_filter = "sys_shop = $shopid";
      if ($branchid == "main" || $branchid == 0) {
        $usertype = 1; $branchid = 0;
      } elseif ($branchid > 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      }
    }

    $group_by = ""; $key_ctrl = "";
    $columns[0] = ($filtertype == 'summary') ? "date_ordered_date":"date_ordered";
    if ($filtertype == 'summary') {
      $group_by = "GROUP BY ".$columns[0].", $location";
      $key_ctrl = $columns[0].", $location";
      if ($shopid > 0) {
        $group_by .= ($shopid > 0) ? ", branch_id, sys_shop":"";
        $key_ctrl .= ($shopid > 0) ? ", branch_id, shop_id":"";
      }
    } else {
      $group_by = 'GROUP BY date_ordered, sys_shop, city';
      $key_ctrl = $columns[0].", shop_id, city";
      if ($shopid > 0) {
        $group_by = "GROUP BY date_ordered, branch_id, sys_shop, city";
        $key_ctrl = $columns[0].", branch_id, shop_id";
      }
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      $columns,
      [
        'fromdate'          => $fromdate,
        'todate'            => $todate,
        'shop_id'           => $shopid,
        'branch_id'         => $branchid,
        'pmethodtype'       => '',
        'filters'           => [
          'shop_filter'     => $shop_filter,
          'branch_filter'   => $branch_filter,
          'date_filter'     => "date_ordered BETWEEN $fromdate AND $todate",
          0                 => "order_status IN ('p', 'po', 'rp', 'bc', 'f', 's')"
        ],
        'group_by'          => $group_by
      ], $usertype, $key_ctrl, 'cnt'
    );
    $city_codes = implode("','", array_column($res, 'city'));
    $cities = $this->get_cityNameByIds($city_codes);
    $city_ids = array_column($cities, 'id');
    $prov_codes = implode("','", array_column($res, 'prov'));
    $provs = $this->get_provsByIds($prov_codes);
    $prov_ids = array_column($provs, 'id');
    $reg_code = implode("','", array_column($res, 'reg'));
    $regs = $this->get_regionsByIds($reg_code);
    $reg_ids = array_column($regs, 'id');

    // print_r($res); exit();

    $temp_res = [];
    foreach ($res as $value) {
      $city = (!empty($cities[array_search($value['city'], $city_ids)]['name'])) ? title_case($cities[array_search($value['city'], $city_ids)]['name']) : 'None';
      $prov = (!empty($provs[array_search($value['prov'], $prov_ids)]['name'])) ? $provs[array_search($value['prov'], $prov_ids)]['name'] : 'None';
      $regi = (!empty($regs[array_search($value['reg'], $reg_ids)]['name'])) ? $regs[array_search($value['reg'], $reg_ids)]['name'] : 'None';
      
      $loc = "";
      switch ($location) {
        case 'city':
          $loc = "$city, $prov, $regi";
          break;
        case 'prov':
          $loc = "$prov, $regi";
          break;
        case 'reg':
          $loc = "$regi";
          break;
      }
      // 'date_ordered','shopname','branchname','city','prov','reg','cnt','reference_num','order_status'
      $temp_res[] = [
        'date_ordered' => $value[$columns[0]], 
        'shopname'     => $value['shopname'], 
        'branchname'   => $value['branchname'], 
        'loc'          => $loc, 
        'cnt'          => $value['cnt'],
        'reference_num'=> $value['reference_num'],
        'order_status' => $value['order_status']
      ];
    }
    $res_col = ['date_ordered','shopname','branchname','loc','cnt','reference_num','order_status'];
    $total_count = $totalFiltered = $totalData = count($temp_res);
    uasort($temp_res, build_sorter($res_col[$requestData['order'][0]['column']], $requestData['order'][0]['dir']));
    if (!$exportable) {
      $temp_res = array_slice($temp_res, $requestData['start'], $requestData['length']);
    }

    $data = [];

    $order_status = ['p' => 'pending order',
    'po' => 'processing order',
    'rp' => 'ready for pickup',
    'bc' => 'booking confirmed',
    'f' => 'fulfilled',
    's' => 'shipped'  ];

    foreach ($temp_res as $key => $row) {
      $row['order_status'] = $order_status[$row['order_status']];
      $row['cnt'] = (!$exportable) ? "<div class='text-right'>".intval($row['cnt'])."</div>":(string) intval($row['cnt']);
      $data[] = array_flatten($row);
    }
    // print_r($query);
    // exit();

    // print_r($data);
    // exit();
    $json_data = array(
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "data"            => $data
    );

    return $json_data;
  }

  public function oblrChartData($fromdate, $todate, $shopid, $branchid, $location = 'city', $dashboard = false, $is_range_tiny = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $table = $dashboard ? ($is_range_tiny ? 'view_sales_14dys':'view_sales_6mons'):'view_paid_orders_with_branch';

    $shop_filter = ""; $branch_filter = ""; $usertype = 0;
    if ($shopid > 0) {
      $shop_filter = "sys_shop = $shopid";
      if ($branchid == "main" || $branchid == 0) {
        $branchid = 0; $usertype = 1;
      } elseif ($branchid > 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      }
    }

    $res = $this->model_powb->paid_order_with_branch_query(
      [$location, 'cnt'],
      [
        'fromdate' => $fromdate, 
        'todate' => $todate,
        'shop_id' => $shopid,
        'branch_id' => $branchid,
        'pmethodtype' => '',
        'filters' => [
          'shop_filter' => $shop_filter,
          'branch_filter' => $branch_filter,
          'date_filter' => "date_ordered BETWEEN $fromdate AND $todate",
          0                 => "order_status IN ('p', 'po', 'rp', 'bc', 'f', 's')"
        ],
        'group_by' => "GROUP BY $location",
        'order_by' => [
          'column' => 'cnt',
          'dir'    => 'desc'
        ]
      ], $usertype, $location, 'cnt'
    );

    $loc_ids = array_column($res, $location); $loc_names = [];
    switch ($location) {
      case 'city':
        $loc_names = $this->get_cityNameByIds(implode("','", $loc_ids));
        break;
      case 'prov':
        $loc_names = $this->get_provsByIds($loc_ids);
        break;
      case 'reg':
        $loc_names = $this->get_regionsByIds($loc_ids);
        break;
    }
    $loc_ids = array_column($loc_names, 'id');
    $data = []; $labels = [];

    foreach ($res as $key => $row) {
      $loc_index = array_search($row[$location], $loc_ids);
      if (isset($loc_names[$loc_index])) {
          $data[] = $row;
          $labels[] = explode(', ', $loc_names[$loc_index]['name']);
      }
    }

    return [
      'data' => $data,
      'labels' => $labels,
    ];
  }

  public function totalsales_view($fromdate,$todate,$shopid = "all", $branchid = 0, $pmethodtype = '', $dashboard = false, $is_range_tiny = false)
  {
    $group_by = ($fromdate == $todate) ? " GROUP BY HOUR(payment_date)":" GROUP BY DATE(payment_date)";
    $key_ctrl = ($fromdate == $todate) ? 'payment_date_time':'payment_date';
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $shopid = ($shopid == 0) ? 'all':$shopid;
    $table = $dashboard ? ($is_range_tiny ? 'view_sales_14dys':'view_sales_6mons'):'view_paid_orders_with_branch';

    $usertype = 0; $shop_filter = ""; $branch_filter = "";
    
    if($shopid > 0){
      $shop_filter = "sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $usertype = 2;
        $branch_filter = "branch_id = $branchid";
      }elseif ($branchid == 'main' || $branchid == 0) {
        $usertype = 1; $branchid = 0;
      }
    }

    $result = $this->model_powb->paid_order_with_branch_query(['total_amount', 'payment_date', 'payment_date_time'], [
      'fromdate' => $fromdate, 
      'todate' => $todate,
      'shop_id' => $shopid,
      'branch_id' => $branchid,
      'pmethodtype' => $pmethodtype,
      'filters' => [
        'shop_filter' => $shop_filter,
        'branch_filter' => $branch_filter,
        'date_filter' => "payment_date BETWEEN $fromdate AND $todate",
        1               => "order_status NOT IN ('rs')",
      ],
      'group_by' => $group_by,
      'order_by' => [
        'column' => $key_ctrl,
        'dir'    => 'asc'
      ]
    ], $usertype, $key_ctrl, 'total_amount');

    return $result;
  }

  public function onlinePurchases ($prev_start_date, $prev_end_date, $todate, $shopid = "all", $branchid = 0)
  {
    $prev_start_date = $this->db2->escape($prev_start_date);
    $prev_end_date = $this->db2->escape($prev_end_date);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $shopid = ($shopid == 0) ? 'all':$shopid;

    $shop_filter = ""; $branch_filter = "";
    
    if($shopid > 0){
      $shop_filter = " AND a.sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $branch_filter = " AND c.branchid = $branchid";
      }elseif ($branchid == 'main') {
        $branch_filter = " AND c.branchid = 0";
      }
    }

    $sql = "SELECT if(date(payment_date) BETWEEN $prev_start_date AND $prev_end_date, 'prev', 'curr') as a, SUM(a.total_amount) - IFNULL(SUM(b.amount), 0) AS total
    FROM `app_sales_order_details` a USE INDEX(payment_date, payment_method)
    LEFT JOIN `app_refund_orders_details` b ON 
    b.order_log_id = a.id
    LEFT JOIN `app_order_branch_details` c ON
    c.order_refnum = a.reference_num AND a.sys_shop = c.shopid
    WHERE a.status = 1 AND LCASE(payment_method) = 'paypanda' AND payment_date BETWEEN $prev_start_date AND $todate $shop_filter $branch_filter GROUP BY a";

    // print_r($sql);
    // exit();
    $res = $this->db2->query($sql);
    $data = [
      'curr' => ['total' => 0],
      'prev' => ['total' => 0],
    ];
    if ($res->num_rows() > 0) {
      foreach ($res->result_array() as $key => $value) {
        $data[$value['a']] = ['total' => $value['total']];
      }
    }

    return $data;
  }
  
  public function export_onlinePurchases ($fromdate, $todate, $shopid = "all", $branchid = 0)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $shopid = ($shopid == 0) ? 'all':$shopid;

    $shop_filter = ""; $branch_filter = "";
    
    if($shopid > 0){
      $shop_filter = " AND a.sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $branch_filter = " AND c.branchid = $branchid";
      }elseif ($branchid == 'main') {
        $branch_filter = " AND c.branchid = 0";
      }
    }

    $sql = "SELECT SUM(a.total_amount) - IFNULL(SUM(b.amount), 0) AS total
    FROM `app_sales_order_details` a USE INDEX(payment_date, payment_method)
    LEFT JOIN `app_refund_orders_details` b ON 
    b.order_log_id = a.id
    LEFT JOIN `app_order_branch_details` c ON
    c.order_refnum = a.reference_num AND a.sys_shop = c.shopid
    WHERE a.status = 1 AND LCASE(payment_method) = 'paypanda' AND payment_date BETWEEN $fromdate AND $todate $shop_filter $branch_filter";

    // print_r($sql);
    // exit();
    $res = $this->db2->query($sql);
    $data = [];
    if ($res->num_rows() > 0) {
      $data = $res->result_array()[0];
    }

    return $data;
  }

  public function manualPurchases ($prev_start_date, $prev_end_date, $todate,$shopid = "all", $branchid = 0)
  {
    $prev_start_date = $this->db2->escape($prev_start_date);
    $prev_end_date = $this->db2->escape($prev_end_date);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $shopid = ($shopid == 0) ? 'all':$shopid;

    $shop_filter = ""; $branch_filter = "";
    
    if($shopid > 0){
      $shop_filter = " AND sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $branch_filter = " AND branch_id = $branchid";
      }elseif ($branchid == 'main') {
        $branch_filter = " AND branch_id = 0";
      }
    }

    $sql = "SELECT if(date(payment_date) BETWEEN $prev_start_date AND $prev_end_date, 'prev', 'curr') as a, SUM(total_amount) AS total, payment_date
    FROM `app_manual_order_details` USE INDEX(payment_date, payment_method)
    WHERE LCASE(payment_method) = 'manual order' AND payment_date BETWEEN $prev_start_date AND $todate $shop_filter $branch_filter GROUP BY a";

    // print_r($sql);
    // exit();
    $res = $this->db2->query($sql);
    $data = [
      'curr' => ['total' => 0],
      'prev' => ['total' => 0],
    ];
    if ($res->num_rows() > 0) {
      foreach ($res->result_array() as $key => $value) {
        $data[$value['a']] = ['total' => $value['total']];
      }
    }

    return $data;
  }
  
  public function export_manualPurchases ($fromdate, $todate,$shopid = "all", $branchid = 0)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $shopid = ($shopid == 0) ? 'all':$shopid;

    $shop_filter = ""; $branch_filter = "";
    
    if($shopid > 0){
      $shop_filter = " AND sys_shop = $shopid";
      if ($branchid != 'all' && $branchid != 0) {
        $branch_filter = " AND branch_id = $branchid";
      }elseif ($branchid == 'main') {
        $branch_filter = " AND branch_id = 0";
      }
    }

    $sql = "SELECT SUM(total_amount) AS total, payment_date
    FROM `app_manual_order_details` USE INDEX(payment_date, payment_method)
    WHERE LCASE(payment_method) = 'manual order' AND payment_date BETWEEN $fromdate AND $todate $shop_filter $branch_filter";

    // print_r($sql);
    // exit();
    $res = $this->db2->query($sql);
    $data = [];
    if ($res->num_rows() > 0) {
      $data = $res->result_array()[0];
    }

    return $data;
  }

  public function revenueByStore($fromdate,$todate,$shopid = "all", $pmethodtype = '', $dashboard = false, $is_range_tiny = false)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
    $todate = $this->db2->escape(date_format($todate, 'Y-m-d'));
    $shopid = ($shopid == 0) ? 'all':$shopid;
    $table = $dashboard ? ($is_range_tiny ? 'view_sales_14dys':'view_sales_6mons'):'view_paid_orders_with_branch';
    
    $shop_filter = ""; $branch_filter = ""; $usertype = 0;
    if($shopid > 0){
      $shop_filter = "sys_shop = $shopid";
    }
    
    return $this->model_powb->paid_order_with_branch_query(['total_amount', 'shopname', 'payment_date'], [
      'fromdate' => $fromdate,
      'todate' => $todate,
      'branch_id' => 0,
      'shop_id' => $shopid,
      'pmethodtype' => $pmethodtype,
      'filters' => [
        'shop_filter' => $shop_filter,
        'branch_filter' => $branch_filter,
        'date_filter' => "payment_date BETWEEN $fromdate AND $todate",
        1               => "order_status NOT IN ('rs')",
      ],
      'group_by' => "GROUP BY shopname",
      'order_by' => [
        'column' => 'total_amount',
        'dir'    => 'DESC' 
      ],
      'limit' => ['start' => 0, 'length' => 10]
    ], $usertype, 'shop_id', 'total_amount');
  }

  public function get_payed_count_total($fromdate,$todate)
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);

    $sql="SELECT count(*) as total_sessions FROM app_sales_order_details a WHERE a.payment_status = 1 AND DATE(a.date_ordered) BETWEEN $fromdate AND $todate";

    if($this->loginstate->get_access()['seller_access'] == 1){
      $shop_id = $this->db2->escape($this->session->sys_shop_id);
      $sql .= " AND a.sys_shop = $shop_id";
    }

    $query = $this->db2->query($sql);

    return array_column($query->result_array(), 'total_sessions');
  }

  public function get_payed_count($fromdate,$todate,$shopid = 'all')
  {
    $fromdate = $this->db2->escape($fromdate);
    $todate = $this->db2->escape($todate);

    $sql="SELECT count(*) as visitors, date_format(payment_date, '%Y-%m-%d') as `date` FROM app_sales_order_details a WHERE a.payment_status = 1 AND DATE(a.payment_date) BETWEEN $fromdate AND $todate";

    if ($shopid !== 'all' && $shopid != 0) {
      $sql .= " and sys_shop = $shopid";
    }

    if($this->session->sys_shop_id > 0){
      $shop_id = $this->db2->escape($this->session->sys_shop_id);
      $sql .= " AND a.sys_shop = $shop_id";
    }

    $sql .= " group by DATE(payment_date)";
    $query = $this->db2->query($sql);

    return $query->result_array();
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

  public function getShopName($shop_id){
    $row = $this->db2->query("SELECT * FROM sys_shops where id=$shop_id")->row();
    return $row->shopname;
  }

  public function getBranchName_id($branchid){
    $sql="SELECT * FROM sys_branch_profile 
          WHERE status = ? AND id = ?";
    $data = array(1, $branchid);
    $result = $this->db2->query($sql, $data);

    if($result->num_rows() > 0){
        $branchname = $result->row()->branchname;
    }else{
        $branchname = 'Main';
    }
    return $branchname;
    }

    public function get_merchant_serviceable_areas_data($filters,$requestData){

    $_record_status = $filters['_record_status'];
    $_mainshop = $filters['_mainshop'];
    $_branchname      = $filters['_branchname'];
    $_city      = $filters['_city'];

    if($this->session->userdata('sys_shop_id')!=""){
      $_mainshop=$this->session->userdata('shopname');
    }
    $record_status_filter="";
    $branchname_filter="";
    $city_filter="";
    if($_record_status==1){
        $record_status_filter="zone.`enabled`=1"; 
    }else if($_record_status==2){
        $record_status_filter="zone.`enabled`=0";
    }else{
        $record_status_filter="(zone.`enabled`=1 OR zone.`enabled`=0)";
    }

    if(strtolower($_branchname)=="main"){
      $branchname_filter='AND branch.branchname IS NULL';
    }else if($_branchname==""){
      $branchname_filter="";
    }else{
      $branchname_filter='AND branch.branchname LIKE "%'.$_branchname.'%"';
    }

    if($_city==""){
      $city_filter="";
    }else{
      $city_filter='HAVING city_mun LIKE "%'.$_city.'%"';
    }

    $columns = array(
      0 => 'shopname',
      1 => 'branchname',
      2 => 'zone_name',
      3 => 'region',
      4 => 'province',
      5 => 'city_mun',
      6 => 'shipping_fee'
    );

      $sql = 'SELECT shop.shopname AS shopname,
      branch.branchname AS branchname, 
   
      GROUP_CONCAT(
      DISTINCT
      UPPER(region.`regDesc`)
      ORDER BY zone.`id`
      SEPARATOR ",sep")AS region,
      
      GROUP_CONCAT(
      DISTINCT
      UPPER(province.`provDesc`)
      ORDER BY zone.`id`
      SEPARATOR ",sep")AS province,
      
      GROUP_CONCAT(
      DISTINCT
      UPPER(city.`citymunDesc`)
      ORDER BY zone.`id`
      SEPARATOR ",sep")AS city_mun,
     
      UPPER(zone.`zone_name`) AS shipping_zone,
      CONCAT("pesosign ",rates.`rate_amount`) AS shipping_fee

        FROM `sys_shops` shop
          LEFT JOIN `sys_shipping` shipping ON 
          shipping.`sys_shop_id` = shop.id 
          
          LEFT JOIN `sys_shipping_zone` zone ON 
          zone.`sys_shipping_id` = shipping.`id`
          
          LEFT JOIN `sys_region` region ON
          region.`regCode` = zone.`regCode`
          
          LEFT JOIN `sys_prov` province ON
          province.`provCode` = zone.`provCode`
          
          LEFT JOIN `sys_citymun` city ON
          city.`citymunCode` = zone.`citymunCode`
          
          LEFT JOIN `sys_shipping_zone_rates` rates ON
          rates.`sys_shipping_zone_id` = zone.`id`

          LEFT JOIN `sys_branch_mainshop` branchms ON
          branchms.`mainshopid` = shop.`id`
          
          LEFT JOIN `sys_branch_profile` branch ON
          branch.`id` = branchms.`branchid`
          
          WHERE shop.shopname LIKE "%'.$_mainshop.'%" '.$branchname_filter.' AND '.$record_status_filter.'  
          
      GROUP BY shopname, zone_name
      '.$city_filter;



    $query = $this->db2->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $total_count = $totalData;

    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding 

    $query = $this->db2->query($sql);

    $data = array();
    $count = 0;
    $total_amount = 0;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $count++;

      $row['region'] = str_replace(',sep', '<br />', $row['region']);
      $row['province'] = str_replace(',sep', '<br />', $row['province']);
      $row['city_mun'] = str_replace(',sep', '<br />', $row['city_mun']);
      $row['shipping_fee'] = str_replace('pesosign', '₱', $row['shipping_fee']);
      
      if($row['branchname']==NULL||$row['branchname']==''||!($row['branchname'])){
        $row['branchname']='MAIN';
      }

      if($row['shipping_fee']==NULL||$row['shipping_fee']==''||!($row['shipping_fee'])){
        $row['shipping_fee']='--';
      }

      if($row['shipping_zone']==NULL||$row['shipping_zone']==''||!($row['shipping_zone'])){
        $row['shipping_zone']='--';
      }

      if($row['city_mun']==NULL||$row['city_mun']==''||!($row['city_mun'])){
        $row['city_mun']='--';
      }

      if($row['region']==NULL||$row['region']==''||!($row['region'])){
        $row['region']='--';
      }

      if($row['province']==NULL||$row['province']==''||!($row['province'])){
        $row['province']='--';
      }
      // $nestedData[] = $count;
      $nestedData[] = $row['shopname'];
      $nestedData[] = $row['branchname'];
      $nestedData[] = $row['shipping_zone']; 
      $nestedData[] = $row['region'];
      $nestedData[] = $row['province'];
      $nestedData[] = $row['city_mun'];
      $nestedData[] = $row['shipping_fee']; 
      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "data"            => $data
    );

    return $json_data;
  }

  function get_all_shop(){
        $sql="SELECT * FROM sys_shops WHERE status = ? ORDER BY shopname ASC";
      $data = array(1);
      return $this->db->query($sql, $data);
    }

    function get_all_city(){
        $sql="SELECT a.*, b.provDesc FROM sys_citymun a 
              LEFT JOIN sys_prov b ON a.provCode = b.provCode AND a.status = ? GROUP BY a.citymunDesc";
        $data = array(1);
        return $this->db->query($sql, $data);
    }



}
