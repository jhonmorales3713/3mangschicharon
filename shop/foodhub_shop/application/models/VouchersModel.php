<?php

class VouchersModel extends CI_Model {

    protected $db_voucher;

    public function __construct() {
        $this->db_voucher = $this->load->database('vouchers', TRUE);
    }

    public function getClaimedVoucher($shopid, $code) {
        $this->db_voucher->where('shopid', $shopid);
        $this->db_voucher->where('vcode', $code);
        $this->db_voucher->where('status', 1);
        $this->db_voucher->where('claim_status', 3);
        return $this->db_voucher->get('v_wallet_claimed')->row_array();
    }

    public function getAvailableVoucher($shopid, $code) {
        $this->db_voucher->where('shopid', $shopid);
        $this->db_voucher->where('vcode', $code);
        $this->db_voucher->where('status', 1);
        return $this->db_voucher->get('v_wallet_available')->row_array();
    }

    public function updateClaimedVoucher($shopid, $code, $data) {
        // Update 8_wallet_vouchers
        $this->db_voucher->where("shopid", $shopid);
        $this->db_voucher->where("vcode", $code);
        $this->db_voucher->update("8_wallet_vouchers", $data);

        // Update v_wallet_all
        $this->db_voucher->where("shopid", $shopid);
        $this->db_voucher->where("vcode", $code);
        $this->db_voucher->update("v_wallet_all", $data);
    }

    public function insertClaimedVoucher($data) {
        // Insert data to v_wallet_claimed
        $this->db_voucher->insert("v_wallet_claimed", $data);
        return $this->db_voucher->affected_rows();
    }

    public function deleteClaimedVoucher($shopid, $code) {
        // Delete data from v_wallet_avaiable
        $this->db_voucher->where("shopid", $shopid);
        $this->db_voucher->where("vcode", $code);
        $this->db_voucher->delete('v_wallet_available'); 
    }

    public function updateClaimStatus($shopid, $code, $status) {
        $this->db_voucher->where('shopid', $shopid);
        $this->db_voucher->where('vcode', $code);
        $this->db_voucher->update('v_wallet_available', array('claim_status'=>$status));

        $this->db_voucher->where('shopid', $shopid);
        $this->db_voucher->where('vcode', $code);
        $this->db_voucher->update('8_wallet_vouchers', array('claim_status'=>$status));

        $this->db_voucher->where('shopid', $shopid);
        $this->db_voucher->where('vcode', $code);
        $this->db_voucher->update('v_wallet_all', array('claim_status'=>$status));
    }
}

?>