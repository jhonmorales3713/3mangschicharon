<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_paid_orders_with_branch extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shops/Model_shops', 'shops');
        $this->load->model('orders/model_refund_orders', 'refunds');
        $this->load->model('shop_branch/Model_shopbranch', 'branches');
    }

	// returns array results
    public function paid_order_with_branch_query ($selects, $condition, $usertype = 0, $key_ctrl, $summation_ctrl) {
		$main = $this->main_branch_selCols($selects);
		$branch = $this->with_branch_selCols($selects);
		$manual = $this->manual_order_selCols($selects);
		extract($condition);
		
		// get reference numbers of branches
		// make it a string
		$branch_filter = (isset($filters['branch_filter'])) ? $filters['branch_filter']:"";
		$branches_with_orders = $this->get_BranchOrdersRefnumList($branch_filter, $shop_id, $fromdate, $todate);
		$branch_orders_refnum_ids_arr = implode("','", array_column($branches_with_orders, 'orderid'));
		
		$set_key_ctlrs = explode(", ", $key_ctrl);
		
		$temp_results = []; $result_array = [];
		$main_branch_results = []; $branches_result = [];
		if (in_array($pmethodtype, ['', 'op'])) {
			// if($usertype == 0){ 
			// 	$branches_result = array();
		
			// 	$main_branch_query_filters = array_merge($filters, [
			// 		'refnums_filter' => "",
			// 	]);
			// 	$main_branch_results = $this->all_branch_orders_query($main,$main_branch_query_filters,$group_by);
				
			// 	$temp_results = array_merge($main_branch_results, $branches_result);
			// }
			if($usertype == 0){ 
				$is_search_mainbranch = $usertype == 1 ? ($branch_id > 0 ? true:false):($branch_id >= 0 ? true:false);
				if (!isset($condition['branch_id']) || $is_search_mainbranch) {
					//branch filters
					$branches_result   = array();
					// $branches_result2  = array();
					// $branchesArr       = array();
					// $branchesArr2      = array();
					// $getBranches     = $this->getBranches($shop_id)->result_array();
					// foreach($getBranches as $row){

					// 	$branch_filter = "branch_id = ".$row['branchid'];
					// 	$branches_with_orders = $this->get_BranchOrdersRefnumList($branch_filter, $shop_id, $fromdate, $todate);
					// 	$branch_orders_refnum_ids_arr = implode("','", array_column($branches_with_orders, 'orderid'));
					// 	$branch_query_filters = array_merge($filters, [
					// 		'refnums_filter' => "reference_num IN ('$branch_orders_refnum_ids_arr')",
					// 	]);
					// 	$branchesArr      = $this->branch_orders_query($branch,$branch_query_filters,$group_by,$branches_with_orders);
					// 	foreach($branchesArr as $value){
					// 		$branches_result[] = $value;
					// 	}
					// }

					$branch_query_filters = array_merge($filters, [
								'refnums_filter' => "reference_num IN ('$branch_orders_refnum_ids_arr')",
							]);
					$branches_result = $this->branch_orders_query($branch,$branch_query_filters,$group_by,$branches_with_orders);
				}
				$main_branch_results = array();
				if ($branch_id == 0 || !isset($condition['branch_id'])) {
					// main filters
					$main_branch_query_filters = array_merge($filters, [
						'refnums_filter' => "reference_num NOT IN ('$branch_orders_refnum_ids_arr')",
					]);
					$main_branch_results = $this->main_branch_orders_query($main,$main_branch_query_filters,$group_by);
				}
				
				$temp_results = array_merge($main_branch_results, $branches_result);
			}
			else if($usertype == 1){ 
				$is_search_mainbranch = $usertype == 1 ? ($branch_id > 0 ? true:false):($branch_id >= 0 ? true:false);
				// if (!isset($condition['branch_id']) || $is_search_mainbranch) {
				// 	//branch filters
				// 	$branch_query_filters = array_merge($filters, [
				// 		'refnums_filter' => "reference_num IN ('$branch_orders_refnum_ids_arr')",
				// 	]);
					$branches_result = array();
				// }
	
				// if ($branch_id == 0 || !isset($condition['branch_id'])) {
					// main filters
					$main_branch_query_filters = array_merge($filters, [
						'refnums_filter' => "reference_num NOT IN ('$branch_orders_refnum_ids_arr')",
					]);
					$main_branch_results = $this->main_branch_orders_query($main,$main_branch_query_filters,$group_by);
				// }
				
				$temp_results = array_merge($main_branch_results, $branches_result);
			}
			else if($usertype == 2){
				//branch filters
				$branch_query_filters = array_merge($filters, [
					'refnums_filter' => "reference_num IN ('$branch_orders_refnum_ids_arr')",
				]);
				$branches_result = $this->branch_orders_query($branch,$branch_query_filters,$group_by,$branches_with_orders);
	
				$temp_results = array_merge($main_branch_results, $branches_result);
			}
		}
		if (in_array($pmethodtype, ['', 'mp']) ) {
			$filters_str = implode(" AND ", array_removeNullElements($filters));
			// manual order
			$manual_orders_query = "SELECT $manual, 0 AS `amount_refunded`
				FROM
					(
						(
							`app_manual_order_details` `a`
						USE INDEX
							(payment_date)
						LEFT JOIN `sys_shops` `b` ON
							(`a`.`sys_shop` = `b`.`id`)
						)
					LEFT JOIN `sys_branch_profile` `c` ON
						(`a`.`branch_id` = `c`.`id`)
					)
				WHERE
					$filters_str AND `a`.`payment_status` = 1 AND `a`.`status` = 1 AND LCASE(payment_method) = 'manual order'
				$group_by";

			$manual_orders_result = $this->db->query($manual_orders_query)->result_array();
			$temp_results = array_merge($temp_results, $manual_orders_result);
		}
		if (isset($condition['order_by'])) {
			uasort($temp_results, build_sorter($order_by['column'],$order_by['dir']));
		}
		$indexes = [];
		if ($key_ctrl && $summation_ctrl) {
			foreach ($temp_results as $value) {
				$is_key = get_key_ctrl($value, $set_key_ctlrs);
				if (!array_key_exists($is_key, $indexes)) {
					if ($value[$summation_ctrl] > 0) {
						$indexes[$is_key] = count($result_array);
						$result_array[] = array_intersect_key($value, array_flip($selects));
					}
				} else {
					$result_array[$indexes[$is_key]][$summation_ctrl] += $value[$summation_ctrl];
				}
			}
		} else {
			foreach ($temp_results as $value) {
				$result_array[] = array_intersect_key($value, array_flip($selects));
			}
		}
		if (isset($condition['limit'])) {
			$result_array = array_slice($result_array, $limit['start'], $limit['length']);
		}
		return $result_array;
    }
    
    public function main_branch_orders_query($mainCols,$filters,$group_by){
		$filters_str = implode(" AND ", array_removeNullElements($filters));

		$sql = "SELECT
					$mainCols
				FROM `app_sales_order_details` `a`
				WHERE
					$filters_str AND `a`.`payment_status` = 1 AND `a`.`status` = 1 
				$group_by";
		// echo $sql; exit();
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) {
			return [];
		}
		$result = $query->result_array();
		// get shop names
		$shop_ids = array_unique(array_column($result, 'shop_id'));
		$shop_ids_arr = implode("','", $shop_ids);
        $shops_arr = $this->shops->get_shopsListByIds($shop_ids_arr);
		$shop_ids = array_column($shops_arr, 'id');
        // get ref nums
		$refnums = implode("','", array_column($result, 'reference_num'));
		$refunds_arr = $this->refunds->get_RefundOrdersListByRefnumAndShops($refnums, $shop_ids_arr);
		$refund_ids = array_column($refunds_arr, 'id');

		// create an array result
		$main_result = [];
		foreach ($result as $key => $value) {
			$id = $value['reference_num'] . "." . $value['shop_id'] . ".0";
			$refund_index = array_search($id, $refund_ids);
			$value['amount_refunded'] = 0;

			$value['shopname'] = '';
			$shop_index = array_search($value['shop_id'], $shop_ids);
			if (isset($shops_arr[$shop_index])) {
				$value['shopname'] = $shops_arr[$shop_index]['shopname'];
			}
			if (isset($value['total_amount'])) {
				if (count($refunds_arr) > 0) {
					if (isset($refunds_arr[$refund_index])) {
						if ($refunds_arr[$refund_index]['id'] == $id) {
							$value['amount_refunded'] = $refunds_arr[$refund_index]['refund_amount'];
							$value['total_amount'] -= $value['amount_refunded'];
						}
					}
				}
			}
			$main_result[] = $value; 
		}

		return $main_result;
	}
	
	public function branch_orders_query($branchCols,$filters,$group_by,$branch_orders){
		unset($filters['branch_filter']);
		$filters_str = implode(" AND ", array_removeNullElements($filters));
		$sql = "SELECT
					$branchCols
				FROM `app_sales_order_details` `a`
				WHERE
					$filters_str AND `a`.`payment_status` = 1 AND `a`.`status` = 1 
				$group_by";
		// echo $sql; exit();
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) {
			return [];
		}
		$result = $query->result_array();
		// get shop names
		$shop_ids = array_unique(array_column($result, 'shop_id'));
		$shop_ids_arr = implode("','", $shop_ids);
        $shops_arr = $this->shops->get_shopsListByIds($shop_ids_arr);
		$shop_ids = array_column($shops_arr, 'id');
        // get ref nums
		$refnums = implode("','", array_column($result, 'reference_num'));
		$refunds_arr = $this->refunds->get_RefundOrdersListByRefnumAndShops($refnums, $shop_ids_arr);
		$refund_ids = array_column($refunds_arr, 'id');
		// get branch ids
		$branch_ids = array_column($branch_orders, 'id');

		// create an array result
		$branch_result = [];
		foreach ($result as $key => $value) {
			$id = $value['reference_num'] . "." . $value['shop_id'];
			$branch_index = array_search($id, $branch_ids);
			if (isset($branch_orders[$branch_index])) {
				if ($branch_orders[$branch_index]['id'] == $id) {
					$value['branch_id'] = $branch_orders[$branch_index]['branchid'];
					$value['branchname'] = $branch_orders[$branch_index]['branchname'];
				}
			}

			$id = $value['reference_num'] . "." . $value['shop_id'] . "." . $value['branch_id'];
			$refund_index = array_search($id, $refund_ids);
			$value['amount_refunded'] = 0;

			$value['shopname'] = '';
			$shop_index = array_search($value['shop_id'], $shop_ids);
			if (isset($shops_arr[$shop_index])) {
				$value['shopname'] = $shops_arr[$shop_index]['shopname'];
			}
			if (isset($value['total_amount'])) {
				if (count($refunds_arr) > 0) {
					if (isset($refunds_arr[$refund_index])) {
						if ($refunds_arr[$refund_index]['id'] == $id) {
							$value['amount_refunded'] = $refunds_arr[$refund_index]['refund_amount'];
							$value['total_amount'] -= $value['amount_refunded'];
						}
					}
				}
			}

			if ($value['branchname'] !== "0") {
				$branch_result[] = $value; 
			}
		}

		return $branch_result;
	}

	public function all_branch_orders_query($mainCols,$filters,$group_by){
		$filters_str = implode(" AND ", array_removeNullElements($filters));

		$sql = "SELECT
					$mainCols
				FROM `app_sales_order_details` `a`
				WHERE
					$filters_str AND `a`.`payment_status` = 1 AND `a`.`status` = 1 
				$group_by";
		// echo $sql; exit();
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) {
			return [];
		}
		$result = $query->result_array();
		// get shop names
		$shop_ids = array_unique(array_column($result, 'shop_id'));
		$shop_ids_arr = implode("','", $shop_ids);
        $shops_arr = $this->shops->get_shopsListByIds($shop_ids_arr);
		$shop_ids = array_column($shops_arr, 'id');
        // get ref nums
		$refnums = implode("','", array_column($result, 'reference_num'));
		$refunds_arr = $this->refunds->get_RefundOrdersListByRefnumAndShops($refnums, $shop_ids_arr);
		$refund_ids = array_column($refunds_arr, 'id');

		// create an array result
		$main_result = [];
		foreach ($result as $key => $value) {
			$id = $value['reference_num'] . "." . $value['shop_id'] . ".0";
			$refund_index = array_search($id, $refund_ids);
			$value['amount_refunded'] = 0;

			$value['shopname'] = '';
			$shop_index = array_search($value['shop_id'], $shop_ids);
			if (isset($shops_arr[$shop_index])) {
				$value['shopname'] = $shops_arr[$shop_index]['shopname'];
			}
			if (isset($value['total_amount'])) {
				if (count($refunds_arr) > 0) {
					if (isset($refunds_arr[$refund_index])) {
						if ($refunds_arr[$refund_index]['id'] == $id) {
							$value['amount_refunded'] = $refunds_arr[$refund_index]['refund_amount'];
							$value['total_amount'] -= $value['amount_refunded'];
						}
					}
				}
			}
			$main_result[] = $value; 
		}

		return $main_result;
	}

    public function get_BranchOrdersRefnumList($branch_filter, $shop_id, $fromdate, $todate)
    {
		$shop_filter = ""; $date_filter = "";
		if ($shop_id > 0) {
			$shop_filter = " AND `ms`.`mainshopid` = $shop_id";
		}
		if (!is_null($fromdate) && !is_null($todate)) {
			// $date_filter = "AND DATE(`bo`.`date_created`) BETWEEN $fromdate AND $todate";
			$date_filter = "AND DATE(`bo`.`date_created`) > $fromdate";
		}
		$branch_filter = str_replace('branch_id', "AND bo.branchid", $branch_filter);
        $sql = "SELECT CONCAT(`bo`.`orderid`,'.',`ms`.`mainshopid`) AS id, `bo`.`branchid`, `ms`.`mainshopid`, `bo`.`orderid`
                FROM
                    (
                        `sys_branch_orders` AS bo
                    LEFT JOIN `sys_branch_mainshop` `ms` ON
                        (
                            `ms`.`branchid` = `bo`.`branchid`
                        )
					)
				WHERE `bo`.`status` = 1 $date_filter $branch_filter $shop_filter";
		// echo $sql; exit();
		$result = $this->db->query($sql)->result_array();
		// print_r($sql);
		// die();
		$branchids = implode("','", array_unique(array_column($result, 'branchid')));
		$branchnames = $this->branches->get_allBranchesIn($branchids);
		$branchids = array_column($branchnames, 'id');
		
		$data = [];
		foreach ($result as $key => $value) {
			$branch_index = array_search($value['branchid'], $branchids);
			if (isset($branchnames[$branch_index])) {
				$value['branchname'] = $branchnames[$branch_index]['branchname'];
				$value['status'] = $branchnames[$branch_index]['status'];
				if ($branchnames[$branch_index]['status'] != 1 && $branch_filter !== "") {
					continue;
				}
			}
			$data[] = $value;
		}
        return $data;
	}
    
    // selects
    public function main_branch_selCols (array $keys = []) {
		// add key defaults
        $keys = array_merge($keys, ['reference_num', 'shop_id', 'branch_id', 'branchname', 'payment_date']);

		$arr = [
			'order_id' => "`a`.`id` AS `order_id`",
			'reference_num' => "`a`.`reference_num` AS `reference_num`",
			'date_ordered' => "`a`.`date_ordered` AS `date_ordered`",			
			'date_ordered_date' => "DATE(`a`.`date_ordered`) AS `date_ordered_date`",
			'payment_date' => "DATE(`a`.`payment_date`) AS `payment_date`",
			'payment_date_time' => "`a`.`payment_date` AS `payment_date_time`",
			'order_status' => "`a`.`order_status` AS `order_status`",
			'date_shipped' => "`a`.`date_shipped` AS `date_shipped`",
			'date_received' => "`a`.`date_received` AS `date_received`",
			'date_fulfilled' => "`a`.`date_fulfilled` AS `date_fulfilled`",
			'name' => "`a`.`name` AS `name`",
			'payment_method' => "`a`.`payment_method` AS `payment_method`",
            'shop_id' => "`a`.`sys_shop` AS `shop_id`",
            'shopname' => "`a`.`sys_shop` AS `shopname`",
			'branch_id' => "0 AS `branch_id`",
			'branchname' => "'Main' AS `branchname`",
			'total_amount' => "SUM(`a`.`total_amount`) AS `total_amount`",			
			'delivery_amount' => "`a`.`delivery_amount` AS `delivery_amount`",
			'reg' => "`a`.`regCode` AS `reg`",
			'prov' => "`a`.`provCode` AS `prov`",
			'city' => "`a`.`citymunCode` AS `city`",
			'address' => "`a`.`address` AS 'address'",
			'cnt' => "SUM(1) as `cnt`",
			'single_cnt' => "1 as `single_cnt`",
        ];
        if (count($keys) > 0) {
            $cols = array_flip($keys);
            return implode(',', array_intersect_key($arr, $cols));
        } else {
            return implode(',', $arr);
        }
	}

	public function with_branch_selCols (array $keys = []) {
		// add key defaults
		$keys = array_merge($keys, ['reference_num', 'shop_id', 'branch_id', 'branchname', 'payment_date']);

		$arr = [
			'order_id' => "`a`.`id` AS `order_id`",
			'reference_num' => "`a`.`reference_num` AS `reference_num`",
			'date_ordered' => "`a`.`date_ordered` AS `date_ordered`",	
			'date_ordered_date' => "DATE(`a`.`date_ordered`) AS `date_ordered_date`",	
			'payment_date' => "DATE(`a`.`payment_date`) AS `payment_date`",
			'payment_date_time' => "`a`.`payment_date` AS `payment_date_time`",
			'order_status' => "`a`.`order_status` AS `order_status`",
			'date_shipped' => "`a`.`date_shipped` AS `date_shipped`",
			'date_received' => "`a`.`date_received` AS `date_received`",
			'date_fulfilled' => "`a`.`date_fulfilled` AS `date_fulfilled`",
			'name' => "`a`.`name` AS `name`",
			'payment_method' => "`a`.`payment_method` AS `payment_method`",
			'shop_id' => "`a`.`sys_shop` AS `shop_id`",
			'shopname' => "`a`.`sys_shop` AS `shopname`",
			'branch_id' => "0 AS `branch_id`",
			'branchname' => "0 AS `branchname`",
			'total_amount' => "SUM(`a`.`total_amount`) AS `total_amount`",
			'delivery_amount' => "`a`.`delivery_amount` AS `delivery_amount`",
			'reg' => "`a`.`regCode` AS `reg`",
			'prov' => "`a`.`provCode` AS `prov`",
			'city' => "`a`.`citymunCode` AS `city`",
			'address' => "`a`.`address` AS 'address'",
			'cnt' => "SUM(1) as `cnt`",
			'single_cnt' => "1 as `single_cnt`",
		];
		$cols = array_flip($keys);
		return implode(',', array_intersect_key($arr, $cols));
	}

	public function manual_order_selCols (array $keys = []) {
		// add key defaults
		$keys = array_merge($keys, ['reference_num', 'shop_id', 'branch_id', 'branchname', 'payment_date']);
		$arr = [
			'order_id' => " `a`.`id` AS `order_id`",
			'reference_num' => "`a`.`reference_num` AS `reference_num`",
			'date_ordered' => "`a`.`date_ordered` AS `date_ordered`",	
			'date_ordered_date' => "DATE(`a`.`date_ordered`) AS `date_ordered_date`",			
			'payment_date' => "DATE(`a`.`payment_date`) AS `payment_date`",
			'payment_date_time' => "`a`.`payment_date` AS `payment_date_time`",
			'order_status' => "`a`.`order_status` AS `order_status`",
			'date_shipped' => "`a`.`date_shipped` AS `date_shipped`",
			'date_received' => "`a`.`date_received` AS `date_received`",
			'date_fulfilled' => "`a`.`date_fulfilled` AS `date_fulfilled`",
			'name' => "`a`.`name` AS `name`",
			'payment_method' => "`a`.`payment_method` AS `payment_method`",
			'shop_id' => "`a`.`sys_shop` AS `shop_id`",
			'shopname' => "`b`.`shopname` AS `shopname`",
			'branch_id' => "`a`.`branch_id` AS `branch_id`",
			'branchname' => "`c`.`branchname` AS `branchname`",
			'total_amount' => "SUM(`a`.`total_amount`) AS `total_amount`",
			'delivery_amount' => "`a`.`delivery_amount` AS `delivery_amount`",
			'reg' => "`a`.`regCode` AS `reg`",
			'prov' => "`a`.`provCode` AS `prov`",
			'city' => "`a`.`citymunCode` AS `city`",
			'address' => "`a`.`address` AS 'address'",
			'cnt' => "SUM(1) as `cnt`",
			'single_cnt' => "1 as `single_cnt`",
		];
		$cols = array_flip($keys);
		return implode(',', array_intersect_key($arr, $cols));
	}

	public function getBranches($shop_id){
		$sql = "SELECT * FROM sys_branch_mainshop WHERE mainshopid = ? AND status > 0";
		$params = array($shop_id);
		$query = $this->db->query($sql, $params);

		return $query;
	}

	public function view_paid_orders_with_branch()
    {
        // view_paid_orders_with_branch manual query
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
    
}

?>
