<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class model_order_logs extends CI_Model {
    private $table = 'app_sales_order_logs';

    public function get_referenceNumsLike($refnum)
	{
		$sql = "SELECT reference_num FROM `app_order_details` WHERE payment_status = 1 AND reference_num != '' and reference_num LIKE '%$refnum%' ORDER BY reference_num LIMIT 0, 6";
		$result = $this->db->query($sql);
		return array_flatten($result->result_array());
	}

	public function getOrderDetailByRefNum($refnum)
	{
        $shop_filter = "";
        if ($this->session->sys_shop > 0) {
            $shopid = $this->session->sys_shop;
            $shop_filter = "AND sys_shop = $shopid";
        }
        $sql = "SELECT * FROM `app_sales_order_details` WHERE LCASE(order_status) != 's' AND reference_num != '' and reference_num = '$refnum' $shop_filter";
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $sql = "SELECT id as order_id, reference_num, `user_id`, `name`, SUM(total_amount) AS total_amount, SUM(payment_portal_fee) AS payment_portal_fee, SUM(delivery_amount) as delivery_amount, (ifnull(SUM(total_amount),0) + ifnull(SUM(payment_portal_fee),0) + ifnull(SUM(delivery_amount),0)) as subtotal FROM `app_sales_order_details` WHERE reference_num != '' and reference_num = '$refnum'";
            $result = $this->db->query($sql);
            $data = $result->result_array();
            if (isset($data[0])) {
                return $data[0];
            } else {
                die();
            }
        } else {
            return false;
        }
	}

    public function getOrderDetailsPerShopByOrderIdAndRefNum($refnum)
    {
        $sql = "SELECT
                    1 AS is_checked,
                    a.id,
                    a.id AS order_log_id,
                    c.sys_shop,
                    s.shopname,
                    branch.branchid,
                    IFNULL(d.branchname, 'Main') AS branchname,
                    a.product_id,
                    b.itemname,
                    a.quantity,
                    a.quantity AS maxqty,
                    a.amount AS itemprice,
                    (a.amount * a.quantity) AS amount
                FROM
                    app_sales_order_logs a
                LEFT JOIN sys_products b ON
                    b.id = a.product_id
                LEFT JOIN app_sales_order_details c ON
                    c.id = a.order_id AND c.reference_num = '$refnum'
                LEFT JOIN `app_order_branch_details` branch ON
                    branch.shopid = c.sys_shop AND branch.order_refnum = c.reference_num
                LEFT JOIN `sys_shops` s ON
                    s.id = c.sys_shop
                LEFT JOIN `sys_branch_profile` d ON
                    d.id = branch.branchid
                WHERE
                    c.order_status != 's'";

        if ($this->session->sys_shop > 0) {
            $shop_id = $this->session->sys_shop;
            $sql .= " AND c.sys_shop = $shop_id";
        }
        
        $result = $this->db->query($sql);
		return $result->result_array();
    }
}
?>