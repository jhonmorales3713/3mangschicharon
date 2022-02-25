<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//ini_set('memory_limit', '1024M');
class Settings_voidrecord extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('setting/model_voidrecord');
		$config_app = switch_db(company_database($this->session->userdata('company_id')));
		$this->model_voidrecord->app_db = $this->load->database($config_app,TRUE);
		$this->load->library('Pdf');
		$this->load->library('Numbertowords');
    }
    
	public function index(){
        if ($this->session->userdata('isLoggedIn') == true) {
            $token_session = $this->session->userdata('token_session');
            $token         = en_dec('en', $token_session);
            
            header("location:" . base_url('Main/home/' . $token));
        }
        
        $this->load->view('login');
    }
    
    public function logout(){
        $this->session->sess_destroy();
        $this->load->view('login');
    }
    
    public function isLoggedIn(){
        if ($this->session->userdata('isLoggedIn') == false) {
            if (empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
                header("location:" . base_url('Main/logout'));
            }
        }
        else {
            if (empty($this->session->userdata('position_id'))) { //kapag destroyed na ung session
                header("location:" . base_url('Main/logout'));
            }
        }
    }

	public function getRelated(){
		if ($this->session->userdata('position_id') != "") {
            $recordType = $this->input->post("recordType");
            $refno = $this->input->post("refno");

			generate_json($this->model_voidrecord->getRelated($recordType, $refno));
		}
	}

    public function getSalesOrderSummary() {
        $sono = $this->input->post('sono');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getSalesOrderSummary($sono);
		}
		$token = $this->session->userdata('token_session');
    }

    public function getDirectSalesSummary() {
        $drno = $this->input->post('drno');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getDirectSalesSummary($drno);
		}
		$token = $this->session->userdata('token_session');
    }

    public function getSalesInvoiceSummary() {
        $sino = $this->input->post('sino');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getSalesInvoiceSummary($sino);
		}
		$token = $this->session->userdata('token_session');
    }

    public function getCollectionSummary() {
        $colno = $this->input->post('colno');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getCollectionSummary($colno);
		}
		$token = $this->session->userdata('token_session');
    }

    public function getAllocationSummary() {
        $allocno = $this->input->post('allocno');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getAllocationSummary($allocno);
		}
		$token = $this->session->userdata('token_session');
    }

    public function getPurchaseOrderSummary() {
        $pono = $this->input->post('pono');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getPurchaseOrderSummary($pono);
		}
		$token = $this->session->userdata('token_session');
    }

    public function getReceiveSummary() {
        $rcvno = $this->input->post('rcvno');

		if ($this->session->userdata('position_id') != "") { 
			return $this->model_voidrecord->getReceiveSummary($rcvno);
		}
		$token = $this->session->userdata('token_session');
    }

    // Void functions

    public function voidCustomer() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $idno = $this->input->post("idno");

        if ($this->model_voidrecord->voidStatus('8_membermain', 'idno', $idno) && 
            $this->model_voidrecord->voidStatus('jcw_salesinvoicesummary', 'idno', $idno) && 
            $this->model_voidrecord->voidStatus('jcw_salesinvoice', 'idno', $idno) && 
            $this->model_voidrecord->voidStatus('8_drpayments', 'idno', $idno) && 
            $this->model_voidrecord->voidStatus('8_customeraccountallocation', 'idno', $idno)) {
            $this->model_voidrecord->saveVoidLog($recordType, $idno, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidSalesOrder() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $sono = $this->input->post("sono");

        if ($this->model_voidrecord->voidStatus('8_salesordersummary', 'sono', $sono) && $this->model_voidrecord->voidStatus('8_salesorder', 'sono', $sono)) {
            $this->model_voidrecord->saveVoidLog($recordType, $sono, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidDeliveryReceipt() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $drno = $this->input->post("drno");
        $sono = $this->input->post("sono");

        if ($this->model_voidrecord->voidStatus('8_directsalessummary', 'drno', $drno) && $this->model_voidrecord->voidStatus('8_directsales', 'drno', $drno) && $this->model_voidrecord->resetSoDrStatus($sono)) {
            $this->model_voidrecord->saveVoidLog($recordType, $drno, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidSalesInvoice() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $drno = $this->input->post("drno");
        $sino = $this->input->post("sino");

        if ($this->model_voidrecord->voidStatus('jcw_salesinvoicesummary', 'sino', $sino) && $this->model_voidrecord->voidStatus('jcw_salesinvoice', 'sino', $sino) && $this->model_voidrecord->resetDrSiStatus($drno)) {
            $this->model_voidrecord->saveVoidLog($recordType, $sino, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidCollection() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $colno = $this->input->post("colno");

        if ($this->model_voidrecord->voidStatus('8_drpayments', 'id', $colno)) {
            $this->model_voidrecord->saveVoidLog($recordType, $colno, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidAllocation() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $allocno = $this->input->post("allocno");
        $colno = $this->input->post("colno");
        $sino = $this->input->post("sino");

        if ($this->model_voidrecord->voidStatus('8_customeraccountallocation', 'id', $allocno) && $this->model_voidrecord->resetSIPaidStatus($sino) && $this->model_voidrecord->resetColAllocStatus($colno)) {
            $this->model_voidrecord->saveVoidLog($recordType, $allocno, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidPurchaseOrder() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $pono = $this->input->post("pono");

        if ($this->model_voidrecord->voidStatus('8_purchasessummary', 'pono', $pono) && $this->model_voidrecord->voidStatus('8_purchases', 'pono', $pono)) {
            $this->model_voidrecord->saveVoidLog($recordType, $pono, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidPOApproval() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $pono = $this->input->post("pono");

        if ($this->model_voidrecord->resetPoApproval($pono)) {
            $this->model_voidrecord->saveVoidLog($recordType, $pono, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

    public function voidReceivePO() {
        $recordType = $this->input->post("recordType");
        $reason = $this->input->post("reason");
        $pono = $this->input->post("pono");
        $rcvno = $this->input->post("rcvno");

        if ($this->model_voidrecord->voidStatus('8_invrcvsummary', 'rcvno', $rcvno) && $this->model_voidrecord->voidStatus('8_invrcv', 'rcvno', $rcvno) && $this->model_voidrecord->resetPoRCVStatus($pono) && $this->model_voidrecord->resetPoQtyRcv($pono)) {
            $this->model_voidrecord->saveVoidLog($recordType, $rcvno, $reason);
            $result['success'] = true;
        }
        else {
            $result['success'] = false;
        }

        generate_json($result);
    }

}