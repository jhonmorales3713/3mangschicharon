<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_customer_profile extends CI_Model {

  public function get_user_address($id){
    $id = $this->db->escape($id);
    $sql = "SELECT a.*, d.provDesc, c.citymunDesc, b.regDesc
      FROM app_customer_addresses a
      LEFT JOIN sys_region b ON a.region_id = b.regCode
      LEFT JOIN sys_citymun c ON a.municipality_id = c.citymunCode
      LEFT JOIN sys_prov d ON c.provCode = d.provCode
      WHERE a.enabled = 1 AND customer_id = $id ORDER BY default_add DESC";
    return $this->db->query($sql);
  }

  public function get_app_order_details($id,$status = null,$offset = 0, $limit = 5){
    $id = $this->db->escape($id);
    $offset = $this->db->escape($offset);
    $sql = "SELECT a.*, b.referral_code FROM app_order_details a
      LEFT JOIN app_referral_codes b ON a.reference_num = b.order_reference_num AND b.status = 1
      WHERE a.user_id = $id";

    if($status !== null){
      $status = $this->db->escape($status);
      $sql .= " AND a.payment_status = $status";
    }

    $sql .= " ORDER BY a.payment_status DESC, date_ordered DESC LIMIT $limit OFFSET $offset";

    return $this->db->query($sql);
  }

  public function get_app_order_shipping_shop($id,$status = null,$offset = 0){
    $id = $this->db->escape($id);
    $offset = $this->db->escape($offset);
    if($status !== null){
      $status = $this->db->escape($status);
      $sql = "SELECT a.reference_num, a.delivery_amount, b.*,
        @payment_status := (SELECT payment_status FROM app_order_details WHERE reference_num = a.reference_num AND payment_status = 1 AND status = 1) as payment_status,
        @orderstatus := (SELECT order_status FROM app_sales_order_details WHERE reference_num = a.reference_num AND sys_shop = a.sys_shop) as orderstatus,
        @date_shipped := (SELECT date_shipped FROM app_sales_order_details WHERE reference_num = a.reference_num AND sys_shop = a.sys_shop) as date_shipped
        FROM app_order_details_shipping a
        LEFT JOIN sys_shops b ON a.sys_shop = b.id
        WHERE reference_num IN (SELECT * FROM (SELECT reference_num FROM app_order_details WHERE user_id = $id
          AND payment_status = $status
          ORDER BY payment_status DESC, date_ordered DESC LIMIT 5 OFFSET $offset) as order_ids)";
    }else{
      $sql = "SELECT a.reference_num, a.delivery_amount, b.*,
        @payment_status := (SELECT payment_status FROM app_order_details WHERE reference_num = a.reference_num AND payment_status = 1 AND status = 1) as payment_status,
        @orderstatus := (SELECT order_status FROM app_sales_order_details WHERE reference_num = a.reference_num AND sys_shop = a.sys_shop) as orderstatus,
        @date_shipped := (SELECT date_shipped FROM app_sales_order_details WHERE reference_num = a.reference_num AND sys_shop = a.sys_shop) as date_shipped
        FROM app_order_details_shipping a
        LEFT JOIN sys_shops b ON a.sys_shop = b.id
        WHERE reference_num IN (SELECT * FROM (SELECT reference_num FROM app_order_details WHERE user_id = $id
          ORDER BY payment_status DESC, date_ordered DESC LIMIT 5 OFFSET $offset) as order_ids)";
    }
    return $this->db->query($sql);
  }

  public function get_app_order_logs($id,$status = null,$offset = 0){
    $id = $this->db->escape($id);
    $offset = $this->db->escape($offset);
    if($status !== null){
      $status = $this->db->escape($status);
      $sql = "SELECT a.order_id, (IF(b.parent_product_id IS NULL, b.itemname,(SELECT b FROM sys_products WHERE id = b.parent_product_id))) as itemname
        ,b.itemname as parent_item_name, b.otherinfo, b.price, a.quantity,
        a.total_amount, a.sys_shop, a.product_id
        FROM app_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND b.enabled = 1
        AND a.order_id IN (SELECT * FROM (SELECT order_id FROM app_order_details WHERE user_id = $id
          AND payment_status = $status
          ORDER BY payment_status DESC, date_ordered DESC LIMIT 5 OFFSET $offset) as order_ids)";
    }else{
      $sql = "SELECT a.order_id, (IF(b.parent_product_id IS NULL, b.itemname,(SELECT b FROM sys_products WHERE id = b.parent_product_id))) as itemname
        ,b.itemname as parent_item_name, b.otherinfo, b.price, a.quantity,
        a.total_amount, a.sys_shop, a.product_id
        FROM app_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND b.enabled = 1
        AND a.order_id IN (SELECT * FROM (SELECT order_id FROM app_order_details WHERE user_id = $id
          ORDER BY payment_status DESC, date_ordered DESC LIMIT 5 OFFSET $offset) as order_ids)";
    }

    return $this->db->query($sql);
  }

  public function get_app_order_vouchers($refno,$shopid){
    $refno = $this->db->escape($refno);
    $shopid = $this->db->escape($shopid);
    $sql = "SELECT amount,payment_refno as voucher_code,
      @total_amount := (SELECT SUM(amount) FROM app_order_payment WHERE payment_type = 'Shoplink' AND shopid = $shopid AND order_ref_num = $refno) as total_amount
      FROM app_order_payment
      WHERE payment_type = 'Shoplink'
      AND shopid = $shopid AND order_ref_num = $refno
      ORDER BY amount DESC";
    return $this->db->query($sql);
  }

  public function get_order_history($refnum,$shopid){
    $refnum = $this->db->escape($refnum);
    $shopid = $this->db->escape($shopid);

    $sql = "SELECT c.* FROM app_order_details a
      INNER JOIN app_sales_order_details b ON a.reference_num = b.reference_num
      INNER JOIN app_order_history c ON b.id = c.order_id
      WHERE a.status = 1 AND b.status = 1 AND c.status = 1
      AND a.reference_num = $refnum AND b.sys_shop = $shopid";
    return $this->db->query($sql);
  }

  // public function get_app_order_logs($user_id,$status = false){
  //   $user_id = $this->db->escape($user_id);
  //   $sql = "SELECT a.*, c.itemname, c.otherinfo, c.price, b.sys_shop, b.quantity, b.total_amount
  //     FROM app_order_details a
  //     LEFT JOIN app_order_logs b ON a.order_id = b.order_id
  //     LEFT JOIN sys_products c ON b.product_id = c.Id
  //     LEFT JOIN sys_shops d ON c.sys_shop = d.id
  //     WHERE b.status = 1 AND c.enabled = 1 AND d.status = 1 AND a.user_id = $user_id";
  //
  //   if($status){
  //     $status = $this->db->escape($status);
  //     $sql .= " AND a.payment_status = $status";
  //   }
  //
  //   $sql .= " ORDER BY a.payment_status DESC, a.date_ordered DESC";
  //
  //   return $this->db->query($sql);
  // }
  //
  // public function get_app_order_details($email, $status = false){
  //   $email = $this->db->escape($email);
  //   $sql = "SELECT a.*, c.shopname, c.shopcode,c.logo, c.id as shopid, b.delivery_amount,
  //
  //     FROM app_order_details a
  //     LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num
  //     LEFT JOIN sys_shops c ON b.sys_shop = c.id
  //     WHERE a.email = $email";
  //   if($status){
  //     $status = $this->db->escape($status);
  //     $sql .= " AND a.payment_status = $status";
  //   }
  //
  //   $sql .= " ORDER BY a.payment_status DESC, a.date_ordered DESC LIMIT 5";
  //
  //   return $this->db->query($sql);
  // }

  public function set_address($data){
    $this->db->insert('app_customer_addresses',$data);
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function update_address($data,$id){
    $this->db->update('app_customer_addresses',$data,array('id' => $id));
    // return ($this->db->affected_rows() > 0) ? true : false;
    return true;
  }

  public function update_address_status($id,$enabled = 0){
    $sql = "UPDATE app_customer_addresses SET enabled = ? WHERE id = ?";
    $data = array($enabled,$id);
    $this->db->query($sql,$data);
    // return ($this->db->affected_rows() > 0) ? true : false;
    return true;
  }

  public function update_default_address($id,$user_id){
    $id = $this->db->escape($id);
    $user_id = $this->db->escape($user_id);
    $sql = "UPDATE app_customer_addresses SET default_add = 0 WHERE customer_id = $user_id";
    $this->db->query($sql);

    $sql2 = "UPDATE app_customer_addresses SET default_add = 1 WHERE id = $id";
    $this->db->query($sql2);

    $sql3 = "SELECT * FROM app_customer_addresses WHERE id = $id AND enabled = 1 AND default_add = 1";
    return array("success" => true, "data" => $this->db->query($sql3));
    // return ($this->db->affected_rows() > 0) ? true: false;
    return true;
  }

  public function update_profile($data,$id){
    $this->db->update('app_customers',$data,array('user_id' => $id));
    // return ($this->db->affected_rows() > 0) ? true: false;
    return true;
  }

  public function update_password($pass,$user_id){
    $sql = "UPDATE sys_customer_auth SET password = ? WHERE id = ?";
    $data = array($pass,$user_id);
    $this->db->query($sql,$data);
    // return ($this->db->affected_rows() > 0) ? true : false;
    return true;
  }


}
