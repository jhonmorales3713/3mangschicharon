<?php
class model_refund_orders extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('reports', TRUE);
    }

    private $table_summary = 'app_refund_orders_summary';
    private $table_details = 'app_refund_orders_details';

    public function createRefundSummary($data)
    {
        if ($this->db->insert($this->table_summary, $data)) {
            $result = $this->db->select('id')->from($this->table_summary)->order_by('date_created', 'desc')->limit(1)->get();
            return $result->result_array()[0]['id'];
        }
        return false;
    }

    public function createRefundDetails($data)
    {
        $resp = $this->db->insert_batch($this->table_details, $data);
        return $resp;
    }

    public function is_in_db($refnum)
    {
        $sql = $this->db->get_where($this->table_summary, ['refnum' => $refnum, 'status' => 1]);
        return ($sql->num_rows() > 0);
    }

    public function getRefundOrderById($reforder_id)
    {
        $sql = $this->db->select()->where('id',$reforder_id)->get($this->table_summary);
        // print_r($sql);
        $result = $sql->result_array();
        // exit();
        if (isset($result[0])) {
            return $result[0];
        }else {
            die();
        }
    }
    
    public function getRefundOrderByIdForUpdateCompare($reforder_id)
    {
        $sql = $this->db->select(['total_amount','mode','acc_num','remarks'])->where('id',$reforder_id)->get($this->table_summary);
        // print_r($sql);
        $result = $sql->result_array();
        // exit();
        if (isset($result[0])) {
            return $result[0];
        }else {
            die();
        }
    }

    public function getRefundOrder_refnum_ById($reforder_id)
    {
        $sql = $this->db->select('refnum')->where('id',$reforder_id)->get($this->table_summary);
        // print_r($sql);
        $result = $sql->result_array();
        // exit();
        if (isset($result[0])) {
            return $result[0];
        }else {
            die();
        }
    }

    public function get_refund_orders($fromdate, $todate, $refnum, $status, $requestData, $exportable = false)
    {
        $token_session     = $this->session->userdata('token_session');
        $token             = en_dec('en', $token_session);
        $get_all           = ($fromdate !== '1970-01-01' && '1970-01-01' !== $todate) ? true:false;
        $fromdate = $this->db->escape($fromdate);
        $todate = $this->db->escape($todate);
        $shopid            = $this->session->sys_shop;
        $has_access = ($shopid > 0) ? '2':'1';
        
        $columns = ['date_created', 'date_updated', 'refnum', 'total_amount', 'mode', 'acc_num', 'remarks', 'review_remarks', 'reviewer', 'name', 'status'];
        // for datatable misc data
        $this->db->select()->like('refnum',$refnum);
        if ($status !== 'all') {
            $this->db->where('status', $status);
        }else{
            $this->db->where('status >', 0);
        }
        if ($get_all) {
            $this->db->where("date(date_created) BETWEEN $fromdate AND $todate");
        }
        $condition = ($shopid == 0) ? ">=":"=";
        $this->db->where("created_by $condition $shopid");
        $this->db->where("has_access >= $has_access");
        $totalData = $this->db->get($this->table_summary)->num_rows();
        $totalFiltered = $totalData;
        $total_count = $totalData;
        
        // for table data
        $this->db->select('id, date(date_created) as date_created, date(date_updated) as date_updated, refnum, total_amount, mode, acc_num, remarks, name, review_remarks, status')->like('refnum',$refnum);
        if ($status !== 'all') {
            $this->db->where('status', $status);
        }else{
            $this->db->where('status >', 0);
        }
        if ($get_all) {
            $this->db->where("date(date_created) BETWEEN $fromdate AND $todate");
        }
        $condition = ($shopid == 0) ? ">=":"=";
        $this->db->where("created_by $condition $shopid");
        $this->db->where("has_access >= $has_access");
        $this->db->order_by($columns[$requestData['order'][0]['column']], $requestData['order'][0]['dir']);
        if (!$exportable) {
            $this->db->limit($requestData['length'], $requestData['start']);
        }

        $result = $this->db->get($this->table_summary);
        // echo $result->result_id->queryString;
        
        $data = [];
        $roa_access = $this->loginstate->get_access()['refund_order_approval'];
        foreach ($result->result_array() as $key => $value) {
            // setting buton styles
            $approve = (isset($roa_access['approve']) && $roa_access['approve'] == 1) ? '<button type="button" data-id="'.$value['id'].'" class="btnApprove is-action my-2 col-sm-12 col-6 btn btn-primary cursor-pointer"><i class="fa fa-thumbs-up" aria-hidden="true"></i></button>':'';
            $reject = (isset($roa_access['reject']) && $roa_access['reject'] == 1) ? '<button type="button" data-id="'.$value['id'].'" class="btnReject is-action my-2 col-sm-12 col-6 btn btn-light cursor-pointer"><i class="fa fa-thumbs-down" aria-hidden="true"></i></button>':'';
            $status = '
            <div class="row m-0">
                '.$approve.$reject.'
            </div>';

            // set parent nav for display
            $parent_nav = ($value['status'] == 0) ? "refund_approval":"refund_order_transactions";
            // edit action
            $edit = (isset($roa_access['update']) && $roa_access['update'] == 1) ? '<a class="dropdown-item" href="'.base_url('Main_orders/'.$parent_nav.'/refund_order/edit/'.$token.'/'.$value["id"]).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>':'';
            // view action
            $actions = $edit.'<a class="dropdown-item" href="'.base_url('Main_orders/'.$parent_nav.'/refund_order/view/'.$token.'/'.$value["id"]).'"><i class="fa fa-eye" aria-hidden="true"></i> View</a>';
            
            if ($value['status'] == 1) {
                $status = '
                <div class="row m-0">
                    <button type="button" class="btn btn-primary cursor-default"><i class="fa fa-thumbs-up" aria-hidden="true"></i></button>
                </div>';
                $actions = '<a class="dropdown-item" href="'.base_url('Main_orders/'.$parent_nav.'/refund_order/view/'.$token.'/'.$value["id"]).'"><i class="fa fa-eye" aria-hidden="true"></i> View</a>';
            } elseif ($value['status'] == 2) {
                $status = '
                <div class="row m-0">
                    <button type="button" class="btn btn-light cursor-default"><i class="fa fa-thumbs-down" aria-hidden="true"></i></button>
                </div>';
                $actions = '<a class="dropdown-item" href="'.base_url('Main_orders/'.$parent_nav.'/refund_order/view/'.$token.'/'.$value["id"]).'"><i class="fa fa-eye" aria-hidden="true"></i> View</a>';
            }

            $is_disabled = ($value['review_remarks'] == '' || is_null($value['review_remarks'])) ? '':'readonly';
            
            $data[] = [
                $value['date_created'],
                $value['date_updated'],
                $value['refnum'],
                (!$exportable) ? number_format($value['total_amount'], 2):$value['total_amount'],
                $value['mode'],
                (string) $value['acc_num'],
                (!$exportable) ? '<textarea class="w-100 px-3 py-2 rounded-md bg-gray-200 text-sm" style="height: 100px;" readonly>'.$value['remarks'].'</textarea>':$value['remarks'],
                $value['name'],
                (!$exportable) ? '<textarea data-id="'.$value['id'].'" class="review_remarks w-100 px-3 py-2 rounded-md bg-gray-200 text-sm" style="height: 100px;" placeholder="Please enter your remarks here before approving or rejecting." '.$is_disabled.'>'.$value['review_remarks'].'</textarea>':$value['review_remarks'],
                (!$exportable) ? $status:$value['status'],
                '<div class="dropdown">
                    <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                    <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
                        '.$actions.'
                    </div>
                </div>'
            ];
        }

        $json_data = array(
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"total_transaction" => $total_count,
			"data"				=> $data
        );
        
        return $json_data;
    }

    public function getRefundOrders($id)
    {
        $sql = $this->db->select()->where('summary_id', $id)->get($this->table_details);
        return $sql->result_array();
    }

    public function getRefundOrdersByRefNum($refnum)
    {
        $sql = $this->db->select()->where('refnum', $refnum)->get($this->table_details);
        return $sql->result_array();
    }

    public function getRefundOrdersByRefNumAndSummaryId($refnum, $summary_id)
    {
        $sql = $this->db->select()->where(['refnum' => $refnum, 'summary_id' => $summary_id, 'is_checked' => 1])->get($this->table_details);
        return $sql->result_array();
    }

    public function reviewRefundSummary($data, $id)
    {
        return $this->db->where('id', $id)->update($this->table_summary, $data);
    }

    public function updateRefundSummary($data, $id)
    {
        return $this->db->where('id', $id)->update($this->table_summary, $data);
    }

    public function updateRefundDetails($data)
    {
        return $this->db->update_batch($this->table_details, $data, 'id');
    }

    public function get_refundorders_data($fromdate, $todate, $shopid, $branchid)
    {
        $fromdate = $this->db->escape($fromdate);
        $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
        $todate = $this->db->escape(date_format($todate, 'Y-m-d'));

        $columns = [
            'date', 'refnum', 'shopname', 'branchname', 'itemname', 'quantity', 'itemprice', 'amount'
        ];
        $sel = explode(", ", "date(b.date_updated) as `date`, SUM(amount) as amount");

        $this->db->from("$this->table_details as a");
        $this->db->join("$this->table_summary as b", 'a.summary_id = b.id', 'left');
        $this->db->where(['is_checked' => 1, 'b.status' => 1]);
        $this->db->where("b.date_updated BETWEEN $fromdate AND $todate");

        if ($shopid > 0) {
            $this->db->where('sys_shop', $shopid);
            if ($branchid == 'main') {
                $this->db->where('branchid', 0);
            } elseif ($branchid > 0) {
                $this->db->where('branchid', $branchid);
            }
        }

        $this->db->group_by(['DATE(b.date_updated)']);
        $this->db->select($sel);

        $data = $this->db->get()->result_array();
  
        return $data;
    }

    public function get_refundorders_table ($fromdate, $todate, $shopid, $branchid, $filtertype, $requestData, $exportable = false)
    {
        $fromdate = $this->db->escape($fromdate);
        $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
        $todate = $this->db->escape(date_format($todate, 'Y-m-d'));

        $columns = [
            'date', 'refnum', 'shopname', 'branchname', 'itemname', 'quantity', 'itemprice', 'amount'
        ];
        $sel = explode(", ", "date(b.date_updated) as `date`, a.refnum, shopname, branchname, itemname, quantity, itemprice");

        $this->db->from("$this->table_details as a");
        $this->db->join("$this->table_summary as b", 'a.summary_id = b.id', 'left');
        $this->db->where(['is_checked' => 1, 'b.status' => 1]);
        $this->db->where("b.date_updated BETWEEN $fromdate AND $todate");

        if ($shopid > 0) {
            $this->db->where('sys_shop', $shopid);
            if ($branchid == 'main') {
                $this->db->where('branchid', 0);
            } elseif ($branchid > 0) {
                $this->db->where('branchid', $branchid);
            }
        }

        switch ($filtertype) {
            case 'summary':
                $sel[] = "SUM(amount) as amount";
                if ($branchid > 0) {
                    $this->db->group_by(['DATE(b.date_updated)', 'sys_shop', 'branchid']);
                } else {
                    $this->db->group_by(['DATE(b.date_updated)', 'sys_shop']);
                }
                break;
            default:
                $sel[] = "amount";
                break;
        }
        $this->db->select($sel);

        $result = $this->db->get();
        $raw_query = $result->result_id->queryString;
        $total_amount = array_sum(array_column($result->result_array(), 'amount'));
        $totalData = $result->num_rows();
        $totalFiltered = $totalData;
        $total_count = $totalData;

        $raw_query.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
        if (!$exportable) {
            $raw_query .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }

        $data = $this->db->query($raw_query)->result_array();
        
        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "total_transaction" => $total_count,
            "total_amount"            => number_format($total_amount, 2),
            "data"            => array_chunk(array_flatten($data), 8)
        );
  
        return $json_data;
    }

    public function get_refundorders_status_table ($fromdate, $todate, $shopid, $requestData, $exportable = false)
    {
        $fromdate = $this->db->escape($fromdate);
        $todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
        $todate = $this->db->escape(date_format($todate, 'Y-m-d'));
        $has_access = ($shopid > 0) ? '2':'1';

        $columns = [
            'date', 'requests', 'approved', 'rejected'
        ];

        $and_where = "AND DATE(date_created) = DATE(a.date_created) AND has_access >= $has_access";
        $and_shop = ($shopid > 0) ? "AND created_by = $shopid":"";
        $sql = "SELECT 
                    DATE(a.date_created) AS `date`, 
                    (SELECT COUNT(id) FROM `app_refund_orders_summary` WHERE `status` = 0 $and_where) AS requests,
                    (SELECT COUNT(id) FROM `app_refund_orders_summary` WHERE `status` = 1 $and_where) AS approved,
                    (SELECT COUNT(id) FROM `app_refund_orders_summary` WHERE `status` = 2 $and_where) AS rejected
                FROM `app_refund_orders_summary` a
                WHERE date_created BETWEEN $fromdate AND $todate $and_shop";

        $sql .= " GROUP BY DATE(a.date_created)";

        $result = $this->db->query($sql);
        $totalData = $result->num_rows();
        $totalFiltered = $totalData;
        $total_count = $totalData;

        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
        if (!$exportable) {
            $sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }

        $data = $this->db->query($sql)->result_array();

        $json_data = array(
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "total_transaction" => $total_count,
            "data"            => array_chunk(array_flatten($data), 4)
        );

        return $json_data;
    }

    public function get_RefundOrdersListByRefnumAndShops($refnums, $shop_ids)
    {
        $sql = "SELECT
                    CONCAT(`det`.`refnum`,'.',`det`.`sys_shop`,'.',`det`.`branchid`) AS id,
                    `det`.`refnum` AS `refnum`,
                    `det`.`sys_shop` AS `sys_shop`,
                    `det`.`branchid` AS `branchid`,
                    SUM(`det`.`amount`) AS `refund_amount`
                FROM
                    (
                        `$this->table_details` `det`
                    LEFT JOIN `$this->table_summary` `summary` ON
                        (`det`.`summary_id` = `summary`.`id`)
                    )
                WHERE
                    `det`.`is_checked` = 1 AND `summary`.`status` = 1 AND `det`.`refnum` IN ('$refnums') AND `det`.`sys_shop` IN ('$shop_ids')
                GROUP BY
                    `summary`.`refnum`,
                    `det`.`sys_shop`,
                    `det`.`branchid`";

        return $this->db->query($sql)->result_array();
    }

    public function get_RefundQtyByRefnum($refnums) 
    {
        $sql = "SELECT CONCAT(a.refnum, '.', product_id) AS id, SUM(quantity) AS qty
                FROM $this->table_details a
                LEFT JOIN $this->table_summary b
                    ON b.id = a.summary_id
                WHERE b.status = 1 AND a.is_checked = 1 AND a.refnum IN ('$refnums')
                GROUP BY product_id";

        return $this->db->query($sql)->result_array();
    }
}

?>