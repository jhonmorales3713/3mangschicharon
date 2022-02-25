<?php

class Voucher extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('VouchersModel');
    }

    public function validateVoucher() {
        $post_data = $this->input->post();

        $validation = array(
            array('shopid','Shop ID','required|trim|numeric'),
            array('shop_'.$post_data['shopid'].'_vcode','Voucher Code','required|trim|alpha_numeric', 
                array(
                    'required' => 'Please input a valid voucher code',
                )
            ),
        );

        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }

        $response = array(
            'success' => false,
            'message' => 'Please check for field error(s)',
        );

        if($this->form_validation->run() == FALSE) {
            $response['field_errors'] = $this->form_validation->error_array();
            generate_json($response);
            die();
        }      

        // Check if code is existing in v_wallet_claimed
        $voucher = $this->VouchersModel->getClaimedVoucher($post_data['shopid'], $post_data['shop_'.$post_data['shopid'].'_vcode']);
        // if exists means code is already used
        if(!empty($voucher)) {
            $response['field_errors'] = array(
                'shop_'.$post_data['shopid'].'_vcode' => 'Voucher code is already used'
            );
            generate_json($response);
            die();
        }

        $voucher = $this->VouchersModel->getAvailableVoucher($post_data['shopid'], $post_data['shop_'.$post_data['shopid'].'_vcode']);

        if (empty($voucher)) {
            $response['field_errors'] = array(
                'shop_'.$post_data['shopid'].'_vcode' => 'Voucher code is not valid'
            );
            generate_json($response);
            die();
        }

        if (strtotime(today()) >= strtotime($voucher['date_valid'])){
            $response['field_errors'] = array(
                'shop_'.$post_data['shopid'].'_vcode' => 'Voucher code is already expired'
            );
            generate_json($response);
            die();
        }

        if ($voucher['claim_status'] == '2'){
            $response['field_errors'] = array(
                'shop_'.$post_data['shopid'].'_vcode' => 'Voucher is already encoded/for payment'
            );
            generate_json($response);
            die();
        }

        if ($voucher['claim_status'] == '4'){
            $response['field_errors'] = array(
                'shop_'.$post_data['shopid'].'_vcode' => 'Cannot use voucher because it has been reissued'
            );
            generate_json($response);
            die();
        }

        // if passed validations
        
        $response = array(
            'success'      => true,
            'message' => 'Voucher code is valid',
            'voucher' => array(
                'shopid' => $voucher['shopid'],
                'shopcode' => $voucher['shopcode'],
                'vcode' => $voucher['vcode'],
                'key' => en_dec('en', $voucher['vcode']),
                'amount' => $voucher['vamount'],
                'valid' => true
            )
        );

        // process voucher for claim_status = 2 encoded/for payment
        // $this->VouchersModel->updateClaimStatus($post_data['shopid'], $post_data['shop_'.$post_data['shopid'].'_vcode'], 2);

        generate_json($response);
    }

    public function makeAvailableVoucher() {
        
        $post_data = $this->input->post();

        // check added voucher code is not tampered
        if (en_dec('en', $post_data['vcode']) !== $post_data['key']){
            $response = array(
                'success' => false,
                'message' => 'It seems that the voucher code is tampered'
            );
            generate_json($response);
            die();
        }

        // if passed validation
        
        // return back to claim_status = 1 if voucher code is removed from the ui
        // $this->VouchersModel->updateClaimStatus($post_data['shopid'], $post_data['vcode'], 1);

        $response = array(
            'success' => true,
            'message' => 'Voucher has been removed'
        );

        generate_json($response);
    }

    public function updateValidVouchers() {

        $post_data['validVouchers'] = json_decode($this->input->post('validVouchers', true),true);

        $this->session->validVouchers = $post_data['validVouchers'];

        $response = array(
            'success' => true,
            'validVouchers' => $this->session->userdata('validVouchers')
        );

        generate_json($response);
    }

    public function getValidVouchers() {

        $validVouchers = $this->session->userdata('validVouchers') ? $this->session->userdata('validVouchers') : [];

        $response = array(
            'success' => true,
            'validVouchers' => $validVouchers
        );

        generate_json($response);
    }
}