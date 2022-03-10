<?php 
class Model_access_control extends CI_Model {

	public $access_control = array(
    );
    
	private $project_access_control = array(
		//transactions
		'transactions' => array(
			'view' => 0,
			'update' => 0,
		),
		//products
		'products' => array(
			'view' => 0,
			'create' => 0,
			'update' => 0,
			'delete' => 0,
			'disable' => 0
		)
	);


	public function generate_functions($data) {
		// overall access
		if(isset($data['overall_access'])) {
		  	$this->access_control['overall_access'] = 1;
		}
		if(isset($data['seller_access'])) {
		  	$this->access_control['seller_access'] = 1;
		}
		if(isset($data['seller_branch_access'])) {
		  	$this->access_control['seller_branch_access'] = 1;
		}
		if(isset($data['food_hub_access'])) {
		  	$this->access_control['food_hub_access'] = 1;
		}
		// balik
		if(isset($data['bussiness_dev_manager'])) {
			$this->access_control['business_dev_access'] = 1;
	  	}

		if(isset($data['accounting'])) {
			$this->access_control['accounting_access'] = 1;
		}

		// online ordering
		if(isset($data['ac_online_ordering_view'])) {
		  	$this->access_control['online_ordering'] = 1;
		}


		//dashboard
		if(isset($data['ac_dashboard_view'])) {
			$this->access_control['dashboard']['view'] = 1;
		}
		if(isset($data['ac_dashboard_sales_count'])) {
			$this->access_control['dashboard']['sales_count_view'] = 1;
		}
		if(isset($data['ac_dashboard_transactions_count'])) {
			$this->access_control['dashboard']['transactions_count_view'] = 1;
		}
		if(isset($data['ac_dashboard_views_count'])) {
			$this->access_control['dashboard']['views_count_view'] = 1;
		}
		if(isset($data['ac_dashboard_overall_sales_count'])) {
			$this->access_control['dashboard']['overall_sales_count_view'] = 1;
		}
		if(isset($data['ac_dashboard_visitors_chart'])) {
			$this->access_control['dashboard']['visitors_chart_view'] = 1;
		}
		if(isset($data['ac_dashboard_views_chart'])) {
			$this->access_control['dashboard']['views_chart_view'] = 1;
		}
		if(isset($data['ac_dashboard_sales_chart'])) {
			$this->access_control['dashboard']['sales_chart_view'] = 1;
		}
		if(isset($data['ac_dashboard_top10productsold_list'])) {
			$this->access_control['dashboard']['top10productsold_list_view'] = 1;
		}
		if(isset($data['ac_dashboard_transactions_chart'])) {
			$this->access_control['dashboard']['transactions_chart_view'] = 1;
		}
		
		//transactions
		if(isset($data['ac_transactions_view'])) {
			$this->access_control['transactions']['view'] = 1;
		}
		if(isset($data['ac_transactions_update'])) {
			$this->access_control['transactions']['update'] = 1;
		}
		if(isset($data['ac_transactions_reassign'])) {
			$this->access_control['transactions']['reassign'] = 1;
		}
		if(isset($data['ac_transactions_mark_as_paid'])) {
			$this->access_control['transactions']['mark_as_paid'] = 1;
		}
		if(isset($data['ac_transactions_process_order'])) {
			$this->access_control['transactions']['process_order'] = 1;
		}
		if(isset($data['ac_transactions_decline_order'])) {
			$this->access_control['transactions']['decline'] = 1;
		}
		if(isset($data['ac_transactions_ready_pickup'])) {
			$this->access_control['transactions']['ready_pickup'] = 1;
		}
		if(isset($data['ac_transactions_booking_confirmed'])) {
			$this->access_control['transactions']['booking_confirmed'] = 1;
		}
		if(isset($data['ac_transactions_mark_fulfilled'])) {
			$this->access_control['transactions']['mark_fulfilled'] = 1;
		}
		if(isset($data['ac_returntosender'])) {
			$this->access_control['transactions']['returntosender'] = 1;
		}
		if(isset($data['ac_redeliver'])) {
			$this->access_control['transactions']['redeliver'] = 1;
		}
		if(isset($data['ac_transactions_shipped'])) {
			$this->access_control['transactions']['shipped'] = 1;
		}
		if(isset($data['ac_transactions_incomplete'])) {
			$this->access_control['transactions']['incomplete'] = 1;
		}
		if(isset($data['ac_transactions_rebook'])) {
			$this->access_control['transactions']['rebook'] = 1;
		}
		if(isset($data['ac_confirmed'])) {
			$this->access_control['transactions']['confirmed'] = 1;
		}
		if(isset($data['ac_merchant_ol_view'])) {
			$this->access_control['transactions']['merchant_orderList'] = 1;
		}

		//new orders nav
		if(isset($data['ac_cancelled_orders'])) {
			$this->access_control['cancelled_orders']['view'] = 1;
		}
		if(isset($data['ac_incomplete_orders'])) {
			$this->access_control['incomplete_orders']['view'] = 1;
		}
		if(isset($data['ac_pending_orders'])) {
			$this->access_control['pending_orders']['view'] = 1;
		}
		if(isset($data['ac_paid_orders'])) {
			$this->access_control['paid_orders']['view'] = 1;
		}
		if(isset($data['ac_readyforprocessing_orders'])) {
			$this->access_control['readyforprocessing_orders']['view'] = 1;
		}
		if(isset($data['ac_processing_orders'])) {
			$this->access_control['processing_orders']['view'] = 1;
		}
		if(isset($data['ac_readyforpickup_orders'])) {
			$this->access_control['readyforpickup_orders']['view'] = 1;
		}
		if(isset($data['ac_bookingconfirmed_orders'])) {
			$this->access_control['bookingconfirmed_orders']['view'] = 1;
		}
		if(isset($data['ac_fulfilled_orders'])) {
			$this->access_control['fulfilled_orders']['view'] = 1;
		}
		if(isset($data['ac_shipped_orders'])) {
			$this->access_control['shipped_orders']['view'] = 1;
		}
		if(isset($data['ac_returntosender_orders'])) {
			$this->access_control['returntosender_orders']['view'] = 1;
		}
		if(isset($data['ac_voided_orders'])) {
			$this->access_control['voided_orders']['view'] = 1;
		}
		if(isset($data['ac_manualorder_list'])) {
			$this->access_control['manualorder_list']['view'] = 1;
		}
		if(isset($data['ac_manualorder_list_create'])) {
			$this->access_control['manualorder_list']['create'] = 1;
		}
		if(isset($data['ac_refund_order_approval_view'])) {
			$this->access_control['refund_order_approval']['view'] = 1;
		}
		if(isset($data['ac_refund_order_approval_update'])) {
			$this->access_control['refund_order_approval']['update'] = 1;
		}
		if(isset($data['ac_refund_order_approval_approve'])) {
			$this->access_control['refund_order_approval']['approve'] = 1;
		}
		if(isset($data['ac_refund_order_approval_reject'])) {
			$this->access_control['refund_order_approval']['reject'] = 1;
		}
		if(isset($data['ac_refund_order_create'])) {
			$this->access_control['refund_order']['create'] = 1;
		}
		if(isset($data['ac_refund_order_trans_view'])) {
			$this->access_control['refund_order_trans']['view'] = 1;
		}

		if(isset($data['ac_forpickup_orders'])) {
			$this->access_control['forpickup_orders']['view'] = 1;
		}
		if(isset($data['ac_confirmed_order_list'])) {
			$this->access_control['confirmed_orders']['view'] = 1;
		}

		//products
		if(isset($data['ac_products_view'])) {
			$this->access_control['products']['view'] = 1;
		}

		if(isset($data['ac_products_create'])) {
			$this->access_control['products']['create'] = 1;
		}

		if(isset($data['ac_products_update'])) {
			$this->access_control['products']['update'] = 1;
		}

		if(isset($data['ac_products_delete'])) {
			$this->access_control['products']['delete'] = 1;
		}

		if(isset($data['ac_products_disable'])) {
			$this->access_control['products']['disable'] = 1;
		}

		//menu plan
		if(isset($data['ac_menu_plan_view'])) {
			$this->access_control['menu_plan']['view'] = 1;
		}

		if(isset($data['ac_menu_plan_create'])) {
			$this->access_control['menu_plan']['create'] = 1;
		}

		if(isset($data['ac_menu_plan_update'])) {
			$this->access_control['menu_plan']['update'] = 1;
		}

		if(isset($data['ac_menu_plan_delete'])) {
			$this->access_control['menu_plan']['delete'] = 1;
		}

		if(isset($data['ac_menu_plan_disable'])) {
			$this->access_control['menu_plan']['disable'] = 1;
		}

		//products waiting for approval
		if(isset($data['ac_products_wfa_view'])) {
			$this->access_control['products_wfa']['view'] = 1;
		}

		if(isset($data['ac_products_wfa_edit'])) {
			$this->access_control['products_wfa']['edit'] = 1;
		}

		if(isset($data['ac_products_wfa_approve'])) {
			$this->access_control['products_wfa']['approved'] = 1;
		}

		if(isset($data['ac_products_wfa_decline'])) {
			$this->access_control['products_wfa']['declined'] = 1;
		}


		//products approve
		if(isset($data['ac_products_apr_view'])) {
			$this->access_control['products_apr']['view'] = 1;
		}

		if(isset($data['ac_products_apr_approve'])) {
			$this->access_control['products_apr']['approved'] = 1;
		}

		if(isset($data['ac_products_apr_decline'])) {
			$this->access_control['products_apr']['declined'] = 1;
		}


		//products approve
		if(isset($data['ac_products_dec_view'])) {
			$this->access_control['products_dec']['view'] = 1;
		}

		if(isset($data['ac_products_dec_approve'])) {
			$this->access_control['products_dec']['approved'] = 1;
		}


		//products Verify
		if(isset($data['ac_products_verified_view'])) {
			$this->access_control['products_verified']['view'] = 1;
		}


		//products changes approval
		if(isset($data['ac_products_changes_view'])) {
			$this->access_control['products_changes_approval']['view'] = 1;
		}

		if(isset($data['ac_products_changes_edit'])) {
			$this->access_control['products_changes_approval']['edit'] = 1;
		}

		if(isset($data['ac_products_changes_approve'])) {
			$this->access_control['products_changes_approval']['approved'] = 1;
		}

		if(isset($data['ac_products_changes_decline'])) {
			$this->access_control['products_changes_approval']['declined'] = 1;
		}


		//Linked Accounts
		if(isset($data['ac_linked_accounts_view'])) {
			$this->access_control['linked_accounts']['view'] = 1;
		}

		if(isset($data['ac_linked_accounts_create'])) {
			$this->access_control['linked_accounts']['create'] = 1;
		}

		if(isset($data['ac_linked_accounts_update'])) {
			$this->access_control['linked_accounts']['update'] = 1;
		}

		if(isset($data['ac_linked_accounts_unlink'])) {
			$this->access_control['linked_accounts']['unlink'] = 1;
		}


		//shops
		if(isset($data['ac_shops_view'])) {
			$this->access_control['shops']['view'] = 1;
		}

		if(isset($data['ac_shops_create'])) {
			$this->access_control['shops']['create'] = 1;
		}

		if(isset($data['ac_shops_update'])) {
			$this->access_control['shops']['update'] = 1;
		}

		if(isset($data['ac_shops_delete'])) {
			$this->access_control['shops']['delete'] = 1;
		}
		if(isset($data['ac_shops_disable'])) {
			$this->access_control['shops']['disable'] = 1;
		}

		//brand name list
		if(isset($data['ac_bnl_view'])) {
			$this->access_control['brand_name_list']['view'] = 1;
		}

		if(isset($data['ac_bnl_create'])) {
			$this->access_control['brand_name_list']['create'] = 1;
		}

		if(isset($data['ac_bnl_update'])) {
			$this->access_control['brand_name_list']['update'] = 1;
		}

		if(isset($data['ac_bnl_delete'])) {
			$this->access_control['brand_name_list']['delete'] = 1;
		}
		if(isset($data['ac_bnl_disable'])) {
			$this->access_control['brand_name_list']['disable'] = 1;
		}

		//company list
		if(isset($data['ac_cl_view'])) {
			$this->access_control['company_list']['view'] = 1;
		}

		if(isset($data['ac_cl_create'])) {
			$this->access_control['company_list']['create'] = 1;
		}

		if(isset($data['ac_cl_update'])) {
			$this->access_control['company_list']['update'] = 1;
		}

		if(isset($data['ac_cl_delete'])) {
			$this->access_control['company_list']['delete'] = 1;
		}
		if(isset($data['ac_cl_disable'])) {
			$this->access_control['company_list']['disable'] = 1;
		}

		//shop branch account
		if(isset($data['branch_account_view'])) {
			$this->access_control['branch_account']['view'] = 1;
		}

		if(isset($data['branch_account_create'])) {
			$this->access_control['branch_account']['create'] = 1;
		}

		if(isset($data['branch_account_update'])) {
			$this->access_control['branch_account']['update'] = 1;
		}

		if(isset($data['branch_account_disable'])) {
			$this->access_control['branch_account']['disable'] = 1;
		}
		if(isset($data['branch_account_delete'])) {
			$this->access_control['branch_account']['delete'] = 1;
		}

		//shop account
		if(isset($data['shop_account_view'])) {
			$this->access_control['shop_account']['view'] = 1;
		}

		if(isset($data['shop_account_create'])) {
			$this->access_control['shop_account']['create'] = 1;
		}

		if(isset($data['shop_account_update'])) {
			$this->access_control['shop_account']['update'] = 1;
		}

		if(isset($data['shop_account_disable'])) {
			$this->access_control['shop_account']['disable'] = 1;
		}
		if(isset($data['shop_account_delete'])) {
			$this->access_control['shop_account']['delete'] = 1;
		}

		//shop branch
		if(isset($data['ac_settings_shop_branch_view'])) {
			$this->access_control['shop_branch']['view'] = 1;
		}
		if(isset($data['ac_settings_shop_branch_create'])) {
			$this->access_control['shop_branch']['create'] = 1;
		}

		if(isset($data['ac_settings_shop_branch_update'])) {
			$this->access_control['shop_branch']['update'] = 1;
		}

		if(isset($data['ac_settings_shop_branch_delete'])) {
			$this->access_control['shop_branch']['delete'] = 1;
		}

		if(isset($data['ac_settings_shop_branch_disable'])) {
			$this->access_control['shop_branch']['disable'] = 1;
		}
		
		//merchant registration
		if(isset($data['shop_mer_reg_view'])) {
			$this->access_control['merchant_registration']['view'] = 1;
		}

		if(isset($data['shop_mer_reg_approve'])) {
			$this->access_control['merchant_registration']['approve'] = 1;
		}

		if(isset($data['shop_mer_reg_edit'])) {
			$this->access_control['merchant_registration']['edit'] = 1;
		}

		if(isset($data['shop_mer_reg_decline'])) {
			$this->access_control['merchant_registration']['decline'] = 1;
		}

		if(isset($data['shop_mer_reg_delete'])) {
			$this->access_control['merchant_registration']['delete'] = 1;
		}

		//shop changes approval
		if(isset($data['shop_changes_view'])) {
			$this->access_control['shop_changes_approval']['view'] = 1;
		}

		if(isset($data['shop_changes_edit'])) {
			$this->access_control['shop_changes_approval']['edit'] = 1;
		}

		if(isset($data['shop_changes_approve'])) {
			$this->access_control['shop_changes_approval']['approved'] = 1;
		}

		if(isset($data['shop_changes_decline'])) {
			$this->access_control['shop_changes_approval']['declined'] = 1;
		}


		//shops mcr
		if(isset($data['shop_mcr_view'])) {
			$this->access_control['shop_mcr']['view'] = 1;
		}

		///acces view

		if(isset($data['shop_mcr_wfa_view'])) {
			$this->access_control['shop_mcr']['wfa_view'] = 1;
		}
		if(isset($data['shop_mcr_approve_view'])) {
			$this->access_control['shop_mcr']['approve_view'] = 1;
		}
		if(isset($data['shop_mcr_decline_view'])) {
			$this->access_control['shop_mcr']['decline_view'] = 1;
		}
		if(isset($data['shop_mcr_verified_view'])) {
			$this->access_control['shop_mcr']['verified_view'] = 1;
		}


		/// access button

		if(isset($data['shop_mcr_approve'])) {
			$this->access_control['shop_mcr']['approve'] = 1;
		}
		if(isset($data['shop_mcr_verify'])) {
			$this->access_control['shop_mcr']['verify'] = 1;
		}
		if(isset($data['shop_mcr_decline'])) {
			$this->access_control['shop_mcr']['decline'] = 1;
		}
		if(isset($data['shop_mcr_edit'])) {
			$this->access_control['shop_mcr']['edit'] = 1;
		}

		//customer
		if(isset($data['ac_customer_view'])) {
			$this->access_control['customer']['view'] = 1;
		}

		if(isset($data['ac_customer_create'])) {
			$this->access_control['customer']['create'] = 1;
		}

		if(isset($data['ac_customer_update'])) {
			$this->access_control['customer']['update'] = 1;
		}

		if(isset($data['ac_customer_delete'])) {
			$this->access_control['customer']['delete'] = 1;
		}

		if(isset($data['ac_customer_disable'])) {
			$this->access_control['customer']['disable'] = 1;
		}

		//accounts
		if(isset($data['main_nav_ac_accounts_view'])) {
			$this->access_control['accounts'] = 1;
		}
		//remittance
		if(isset($data['main_nav_ac_remittance_view'])) {
			$this->access_control['remittance'] = 1;
		}
		//billing
		if(isset($data['ac_billing_view'])) {
			$this->access_control['billing']['view'] = 1;
		}

		if(isset($data['ac_billing_create'])) {
			$this->access_control['billing']['create'] = 1;
		}

		if(isset($data['ac_billing_update'])) {
			$this->access_control['billing']['update'] = 1;
		}

		if(isset($data['ac_billing_delete'])) {
			$this->access_control['billing']['delete'] = 1;
		}

		if(isset($data['ac_billing_disable'])) {
			$this->access_control['billing']['disable'] = 1;
		}

		if(isset($data['ac_billing_adminview'])) {
			$this->access_control['billing']['admin_view'] = 1;
		}


		//billing merchant
		if(isset($data['ac_billing_merchant_view'])) {
			$this->access_control['billing_merchant']['view'] = 1;
		}

		if(isset($data['ac_billing_merchant_create'])) {
			$this->access_control['billing_merchant']['create'] = 1;
		}

		if(isset($data['ac_billing_merchant_update'])) {
			$this->access_control['billing_merchant']['update'] = 1;
		}

		if(isset($data['ac_billing_merchant_delete'])) {
			$this->access_control['billing_merchant']['delete'] = 1;
		}

		if(isset($data['ac_billing_merchant_disable'])) {
			$this->access_control['billing_merchant']['disable'] = 1;
		}


		//billing portal payment fee
		if(isset($data['billing_portal_fee_view'])) {
			$this->access_control['billing_portal_fee']['view'] = 1;
		}

		if(isset($data['billing_portal_fee_create'])) {
			$this->access_control['billing_portal_fee']['create'] = 1;
		}

		if(isset($data['billing_portal_fee_update'])) {
			$this->access_control['billing_portal_fee']['update'] = 1;
		}

		if(isset($data['billing_portal_fee_disable'])) {
			$this->access_control['billing_portal_fee']['disable'] = 1;
		}

		if(isset($data['billing_portal_fee_delete'])) {
			$this->access_control['billing_portal_fee']['delete'] = 1;
		}

		// vouchers
		if(isset($data['main_nav_ac_vouchers_view'])) {
			$this->access_control['vouchers'] = 1;
		}
		//vouchers claimed
		if(isset($data['ac_vouchers_claimed_view'])) {
			$this->access_control['vc']['view'] = 1;
		}

		//vouchers list view
		if(isset($data['ac_vouchers_list_view'])) {
			$this->access_control['voucher_list']['view'] = 1;
		}

		//vouchers list create
		if(isset($data['ac_vouchers_list_create'])) {
			$this->access_control['voucher_list']['create'] = 1;
		}	

	   //vouchers list update
		if(isset($data['ac_vouchers_list_update'])) {
			$this->access_control['voucher_list']['update'] = 1;
		}	

		//vouchers list delete
		if(isset($data['ac_vouchers_list_delete'])) {
			$this->access_control['voucher_list']['delete'] = 1;
		}	

		//vouchers list disabled
		if(isset($data['ac_vouchers_list_disable'])) {
			$this->access_control['voucher_list']['disable'] = 1;
		}	

		//wallet
		if(isset($data['main_nav_ac_wallet_view'])) {
			$this->access_control['wallet'] = 1;
		}

		//pre payment
		if(isset($data['ac_prepayment_view'])) {
			$this->access_control['prepayment']['view'] = 1;
		}

		if(isset($data['ac_prepayment_create'])) {
			$this->access_control['prepayment']['create'] = 1;
		}

		//manual_order
		if(isset($data['ac_manual_order_view'])) {
			$this->access_control['manual_order']['view'] = 1;
		}

		if(isset($data['ac_manual_order_create'])) {
			$this->access_control['manual_order']['create'] = 1;
		}

		//manual_order
		if(isset($data['ac_wallet_page_view'])) {
			$this->access_control['wallet_page']['view'] = 1;
		}

		if(isset($data['ac_wallet_page_encash'])) {
			$this->access_control['wallet_page']['encash'] = 1;
		}

		if(isset($data['ac_wallet_page_adminview'])) {
			$this->access_control['wallet_page']['admin_view'] = 1;
		}


		//codes
		if(isset($data['ac_codes_view'])) {
			$this->access_control['codes']['view'] = 1;
		}

		if(isset($data['ac_codes_create'])) {
			$this->access_control['codes']['create'] = 1;
		}

		if(isset($data['ac_codes_update'])) {
			$this->access_control['codes']['update'] = 1;
		}

		if(isset($data['ac_codes_delete'])) {
			$this->access_control['codes']['delete'] = 1;
		}
		

		//reports
		if(isset($data['main_nav_ac_reports_view'])) {
			$this->access_control['reports'] = 1;
		}

		if(isset($data['ac_ps_view'])) {
			$this->access_control['ps']['view'] = 1;
		}
		if(isset($data['ac_por_view'])) {
			$this->access_control['por']['view'] = 1;
		}
		if(isset($data['ac_sr_view'])) {
			$this->access_control['sr']['view'] = 1;
		}
		if(isset($data['ac_tbr_view'])) {
			$this->access_control['tbr']['view'] = 1;
		}
		if(isset($data['ac_olps_view'])) {
			$this->access_control['olps']['view'] = 1;
		}
		if(isset($data['ac_ssr_view'])) {
			$this->access_control['ssr']['view'] = 1;
		}
		if(isset($data['ac_pr_view'])) {
			$this->access_control['pr']['view'] = 1;
		}
		if(isset($data['ac_psr_view'])) {
			$this->access_control['psr']['view'] = 1;
		}
		//new reports
		if(isset($data['ac_aov_view'])) {
			$this->access_control['aov']['view'] = 1;
		}
		if(isset($data['ac_to_view'])) {
			$this->access_control['to']['view'] = 1;
		}
		if(isset($data['ac_os_view'])) {
			$this->access_control['os']['view'] = 1;
		}
		if(isset($data['ac_tps_view'])) {
			$this->access_control['tps']['view'] = 1;
		}
		if(isset($data['ac_wtr_view'])) {
			$this->access_control['wtr']['view'] = 1;
		}
		if(isset($data['ac_msr_view'])) {
			$this->access_control['msr']['view'] = 1;
		}
		if(isset($data['ac_tsr_view'])) {
			$this->access_control['tsr']['view'] = 1;
		}
		if(isset($data['ac_rbsr_view'])) {
			$this->access_control['rbsr']['view'] = 1;
		}
		if(isset($data['ac_rbbr_view'])) {
			$this->access_control['rbbr']['view'] = 1;
		}
		if(isset($data['ac_oscrr_view'])) {
			$this->access_control['oscrr']['view'] = 1;
		}
		if(isset($data['ac_tacr_view'])) {
			$this->access_control['tacr']['view'] = 1;
		}
		if(isset($data['ac_po_view'])) {
			$this->access_control['po']['view'] = 1;
		}
		if(isset($data['ac_inv_view'])) {
			$this->access_control['inv']['view'] = 1;
		}
		if(isset($data['ac_invend_view'])) {
			$this->access_control['invend']['view'] = 1;
		}
		if(isset($data['ac_invlist_view'])) {
			$this->access_control['invlist']['view'] = 1;
		}
		if(isset($data['ac_osr_view'])) {
			$this->access_control['osr']['view'] = 1;
		}
		if(isset($data['ac_rbl_view'])) {
			$this->access_control['rbl']['view'] = 1;
		}
		if(isset($data['ac_oblr_view'])) {
			$this->access_control['oblr']['view'] = 1;
		}
		if(isset($data['ac_bpr_view'])) {
			$this->access_control['bpr']['view'] = 1;
		}
		if(isset($data['ac_prr_view'])) {
			$this->access_control['prr']['view'] = 1;
		}
		if(isset($data['ac_or_view'])) {
			$this->access_control['or']['view'] = 1;
		}
		if(isset($data['ac_rosum_view'])) {
			$this->access_control['rosum']['view'] = 1;
		}
		if(isset($data['ac_rostat_view'])) {
			$this->access_control['rostat']['view'] = 1;
		}

		//settings
		if(isset($data['main_nav_ac_settings_view'])) {
			$this->access_control['settings'] = 1;
		}

		if(isset($data['ac_csr_view'])) {
			$this->access_control['csr'] = 1;
		}

		//change password
		if(isset($data['ac_settings_change-password_update'])) {
			$this->access_control['change_password']['update'] = 1;
		}

		//users
		if(isset($data['ac_settings_users_view'])) {
			$this->access_control['users']['view'] = 1;
		}

		if(isset($data['ac_settings_users_create'])) {
			$this->access_control['users']['create'] = 1;
		}

		if(isset($data['ac_settings_users_update'])) {
			$this->access_control['users']['update'] = 1;
		}

		if(isset($data['ac_settings_users_delete'])) {
			$this->access_control['users']['delete'] = 1;
		}

		if(isset($data['ac_settings_users_disable'])) {
			$this->access_control['users']['disable'] = 1;
		}

		//members
		if(isset($data['ac_settings_members_view'])) {
			$this->access_control['members']['view'] = 1;
		}

		if(isset($data['ac_settings_members_create'])) {
			$this->access_control['members']['create'] = 1;
		}

		if(isset($data['ac_settings_members_update'])) {
			$this->access_control['members']['update'] = 1;
		}

		if(isset($data['ac_settings_members_delete'])) {
			$this->access_control['members']['delete'] = 1;
		}
		
		if(isset($data['ac_settings_members_disable'])) {
			$this->access_control['members']['disable'] = 1;
		}

		//merchant user list
		if(isset($data['ac_settings_muserlist_view'])) {
			$this->access_control['muserlist']['view'] = 1;
		}

		if(isset($data['ac_settings_muserlist_create'])) {
			$this->access_control['muserlist']['create'] = 1;
		}

		if(isset($data['ac_settings_muserlist_update'])) {
			$this->access_control['muserlist']['update'] = 1;
		}

		if(isset($data['ac_settings_muserlist_delete'])) {
			$this->access_control['muserlist']['delete'] = 1;
		}
		
		if(isset($data['ac_settings_muserlist_disable'])) {
			$this->access_control['muserlist']['disable'] = 1;
		}

		//Admin  user list
		if(isset($data['settings_aul_view'])) {
			$this->access_control['adminuserlist']['view'] = 1;
		}

		if(isset($data['settings_aul_create'])) {
			$this->access_control['adminuserlist']['create'] = 1;
		}

		if(isset($data['settings_aul_update'])) {
			$this->access_control['adminuserlist']['update'] = 1;
		}

		if(isset($data['settings_aul_delete'])) {
			$this->access_control['adminuserlist']['delete'] = 1;
		}
		
		if(isset($data['settings_aul_disable'])) {
			$this->access_control['adminuserlist']['disable'] = 1;
		}

		//delivery_areas
		if(isset($data['ac_settings_delivery_areas_view'])) {
			$this->access_control['delivery_areas']['view'] = 1;
		}

		if(isset($data['ac_settings_delivery_areas_create'])) {
			$this->access_control['delivery_areas']['create'] = 1;
		}

		if(isset($data['ac_settings_delivery_areas_update'])) {
			$this->access_control['delivery_areas']['update'] = 1;
		}

		if(isset($data['ac_settings_delivery_areas_delete'])) {
			$this->access_control['delivery_areas']['delete'] = 1;
		}
		
		if(isset($data['ac_settings_delivery_areas_disable'])) {
			$this->access_control['delivery_areas']['disable'] = 1;
		}

		//announcement
		if(isset($data['ac_settings_announcement_view'])) {
			$this->access_control['announcement']['view'] = 1;
		}

		if(isset($data['ac_settings_announcement_update'])) {
			$this->access_control['announcement']['update'] = 1;
		}
		
		//currency
		if(isset($data['ac_settings_currency_view'])) {
			$this->access_control['currency']['view'] = 1;
		}

		if(isset($data['ac_settings_currency_create'])) {
			$this->access_control['currency']['create'] = 1;
		}

		if(isset($data['ac_settings_currency_update'])) {
			$this->access_control['currency']['update'] = 1;
		}

		if(isset($data['ac_settings_currency_delete'])) {
			$this->access_control['currency']['delete'] = 1;
		}
		
		if(isset($data['ac_settings_currency_disable'])) {
			$this->access_control['currency']['disable'] = 1;
		}

		//payment type
		if(isset($data['ac_settings_payment_type_view'])) {
			$this->access_control['payment_type']['view'] = 1;
		}

		if(isset($data['ac_settings_payment_type_create'])) {
			$this->access_control['payment_type']['create'] = 1;
		}

		if(isset($data['ac_settings_payment_type_update'])) {
			$this->access_control['payment_type']['update'] = 1;
		}

		if(isset($data['ac_settings_payment_type_delete'])) {
			$this->access_control['payment_type']['delete'] = 1;
		}
		
		if(isset($data['ac_settings_payment_type_disable'])) {
			$this->access_control['payment_type']['disable'] = 1;
		}

		if(isset($data['ref_comrate_view'])) {
			$this->access_control['ref_comrate']['view'] = 1;
		}

		if(isset($data['ref_comrate_create'])) {
			$this->access_control['ref_comrate']['create'] = 1;
		}

		if(isset($data['ref_comrate_update'])) {
			$this->access_control['ref_comrate']['update'] = 1;
		}

		if(isset($data['ref_comrate_disable'])) {
			$this->access_control['ref_comrate']['disable'] = 1;
		}
		
		if(isset($data['ref_comrate_delete'])) {
			$this->access_control['ref_comrate']['delete'] = 1;
		}

		if(isset($data['settings_roles_view'])) {
			$this->access_control['settings_roles']['view'] = 1;
		}

		if(isset($data['settings_roles_create'])) {
			$this->access_control['settings_roles']['create'] = 1;
		}

		if(isset($data['settings_roles_update'])) {
			$this->access_control['settings_roles']['update'] = 1;
		}

		if(isset($data['settings_roles_disable'])) {
			$this->access_control['settings_roles']['disable'] = 1;
		}
		
		if(isset($data['settings_roles_delete'])) {
			$this->access_control['settings_roles']['delete'] = 1;
		}

		if(isset($data['settings_region_view'])) {
			$this->access_control['settings_region']['view'] = 1;
		}

		if(isset($data['settings_region_create'])) {
			$this->access_control['settings_region']['create'] = 1;
		}

		if(isset($data['settings_region_update'])) {
			$this->access_control['settings_region']['update'] = 1;
		}

		if(isset($data['settings_region_disable'])) {
			$this->access_control['settings_region']['disable'] = 1;
		}
		
		if(isset($data['settings_region_delete'])) {
			$this->access_control['settings_region']['delete'] = 1;
		}

		if(isset($data['settings_city_view'])) {
			$this->access_control['settings_city']['view'] = 1;
		}

		if(isset($data['settings_city_create'])) {
			$this->access_control['settings_city']['create'] = 1;
		}

		if(isset($data['settings_city_update'])) {
			$this->access_control['settings_city']['update'] = 1;
		}

		if(isset($data['settings_city_disable'])) {
			$this->access_control['settings_city']['disable'] = 1;
		}
		
		if(isset($data['settings_city_delete'])) {
			$this->access_control['settings_city']['delete'] = 1;
		}

		if(isset($data['settings_province_view'])) {
			$this->access_control['settings_province']['view'] = 1;
		}

		if(isset($data['settings_province_create'])) {
			$this->access_control['settings_province']['create'] = 1;
		}

		if(isset($data['settings_province_update'])) {
			$this->access_control['settings_province']['update'] = 1;
		}

		if(isset($data['settings_province_disable'])) {
			$this->access_control['settings_province']['disable'] = 1;
		}
		
		if(isset($data['settings_province_delete'])) {
			$this->access_control['settings_province']['delete'] = 1;
		}

		//shipping and delivery
		if(isset($data['ac_shipping_and_delivery_view'])) {
			$this->access_control['shipping_and_delivery'] = 1;
		}
			// general shipping
			if(isset($data['ac_general_shipping_view'])) {
				$this->access_control['general_shipping']['view'] = 1;
			}

			if(isset($data['ac_general_shipping_create'])) {
				$this->access_control['general_shipping']['create'] = 1;
			}

			if(isset($data['ac_general_shipping_update'])) {
				$this->access_control['general_shipping']['update'] = 1;
			}

			if(isset($data['ac_general_shipping_delete'])) {
				$this->access_control['general_shipping']['delete'] = 1;
			}
			
			if(isset($data['ac_general_shipping_disable'])) {
				$this->access_control['general_shipping']['disable'] = 1;
			}

			// custom shipping
			if(isset($data['ac_custom_shipping_view'])) {
				$this->access_control['custom_shipping']['view'] = 1;
			}

			if(isset($data['ac_custom_shipping_create'])) {
				$this->access_control['custom_shipping']['create'] = 1;
			}

			if(isset($data['ac_custom_shipping_update'])) {
				$this->access_control['custom_shipping']['update'] = 1;
			}

			if(isset($data['ac_custom_shipping_delete'])) {
				$this->access_control['custom_shipping']['delete'] = 1;
			}

			if(isset($data['ac_custom_shipping_disable'])) {
				$this->access_control['custom_shipping']['disable'] = 1;
			}

		//members
		if(isset($data['ac_settings_category_view'])) {
			$this->access_control['category']['view'] = 1;
		}

		if(isset($data['ac_settings_category_create'])) {
			$this->access_control['category']['create'] = 1;
		}

		if(isset($data['ac_settings_category_update'])) {
			$this->access_control['category']['update'] = 1;
		}

		if(isset($data['ac_settings_category_delete'])) {
			$this->access_control['category']['delete'] = 1;
		}

		if(isset($data['ac_settings_category_disable'])) {
			$this->access_control['category']['disable'] = 1;
		}

		//members
		if(isset($data['ac_settings_shipping_partners_view'])) {
			$this->access_control['shipping_partners']['view'] = 1;
		}

		if(isset($data['ac_settings_shipping_partners_create'])) {
			$this->access_control['shipping_partners']['create'] = 1;
		}

		if(isset($data['ac_settings_shipping_partners_update'])) {
			$this->access_control['shipping_partners']['update'] = 1;
		}

		if(isset($data['ac_settings_shipping_partners_delete'])) {
			$this->access_control['shipping_partners']['delete'] = 1;
		}

		if(isset($data['ac_settings_shipping_partners_disable'])) {
			$this->access_control['shipping_partners']['disable'] = 1;
		}

		//shop banners
		if(isset($data['ac_settings_shop_banners_view'])) {
			$this->access_control['shop_banners']['view'] = 1;
		}

		if(isset($data['ac_settings_shop_banners_create'])) {
			$this->access_control['shop_banners']['create'] = 1;
		}

		if(isset($data['ac_settings_shop_banners_update'])) {
			$this->access_control['shop_banners']['update'] = 1;
		}

		if(isset($data['ac_settings_shop_banners_delete'])) {
			$this->access_control['shop_banners']['delete'] = 1;
		}

		if(isset($data['ac_settings_shop_banners_disable'])) {
			$this->access_control['shop_banners']['disable'] = 1;
		}
		
		if(isset($data['ac_settings_void_record_process'])) {
			$this->access_control['void_record']['process'] = 1;
		}

		if(isset($data['ac_settings_void_record_list_view'])) {
			$this->access_control['void_record_list']['view'] = 1;
		}

		//bank_list
		if(isset($data['ac_settings_bank_view'])) {
			$this->access_control['bank_list']['view'] = 1;
		}

		if(isset($data['ac_settings_bank_create'])) {
			$this->access_control['bank_list']['create'] = 1;
		}

		if(isset($data['ac_settings_bank_update'])) {
			$this->access_control['bank_list']['update'] = 1;
		}

		if(isset($data['ac_settings_bank_delete'])) {
			$this->access_control['bank_list']['delete'] = 1;
		}

		//bank_list
		if(isset($data['ac_settings_co_view'])) {
			$this->access_control['cancellation_options']['view'] = 1;
		}

		if(isset($data['ac_settings_co_create'])) {
			$this->access_control['cancellation_options']['create'] = 1;
		}

		if(isset($data['ac_settings_co_update'])) {
			$this->access_control['cancellation_options']['update'] = 1;
		}

		if(isset($data['ac_settings_co_delete'])) {
			$this->access_control['cancellation_options']['delete'] = 1;
		}

		if(isset($data['ac_settings_co_disable'])) {
			$this->access_control['cancellation_options']['disable'] = 1;
		}

		//Faqs CMS
		if(isset($data['faqs_view'])) {
			$this->access_control['faqs']['view'] = 1;
		}

		if(isset($data['faqs_create'])) {
			$this->access_control['faqs']['create'] = 1;
		}

		if(isset($data['faqs_update'])) {
			$this->access_control['faqs']['update'] = 1;
		}

		if(isset($data['faqs_disable'])) {
			$this->access_control['faqs']['disable'] = 1;
		}

		if(isset($data['faqs_delete'])) {
			$this->access_control['faqs']['delete'] = 1;
		}

		//ticket history
		if(isset($data['ac_csr_ticket_history_view'])) {
			$this->access_control['ticket_history']['view'] = 1;
		}

		if(isset($data['ac_csr_ticket_history_create'])) {
			$this->access_control['ticket_history']['create'] = 1;
		}

		if(isset($data['ac_csr_ticket_history_update'])) {
			$this->access_control['ticket_history']['update'] = 1;
		}

		if(isset($data['ac_csr_ticket_history_delete'])) {
			$this->access_control['ticket_history']['delete'] = 1;
		}

		if(isset($data['ac_csr_ticket_history_disable'])) {
			$this->access_control['ticket_history']['disable'] = 1;
		}

		//csr ticket
		if(isset($data['ac_csr_ticket_view'])) {
			$this->access_control['csr_ticket']['view'] = 1;
		}

		if(isset($data['ac_csr_ticket_create'])) {
			$this->access_control['csr_ticket']['create'] = 1;
		}

		if(isset($data['ac_csr_ticket_update'])) {
			$this->access_control['csr_ticket']['update'] = 1;
		}

		if(isset($data['ac_csr_ticket_delete'])) {
			$this->access_control['csr_ticket']['delete'] = 1;
		}

		if(isset($data['ac_csr_ticket_disable'])) {
			$this->access_control['csr_ticket']['disable'] = 1;
		}

		//csr ticket log
		if(isset($data['ac_csr_ticket_log_view'])) {
			$this->access_control['csr_ticket_log']['view'] = 1;
		}

		if(isset($data['ac_csr_ticket_log_create'])) {
			$this->access_control['csr_ticket_log']['create'] = 1;
		}

		if(isset($data['ac_csr_ticket_log_update'])) {
			$this->access_control['csr_ticket_log']['update'] = 1;
		}

		if(isset($data['ac_csr_ticket_log_disable'])) {
			$this->access_control['csr_ticket_log']['delete'] = 1;
		}

		if(isset($data['ac_csr_ticket_log_delete'])) {
			$this->access_control['csr_ticket_log']['disable'] = 1;
		}

		//Promotion
		// Product Promotion
		if(isset($data['pr_product_promotion_view'])) {
			$this->access_control['product_promotion']['view'] = 1;
		}

		if(isset($data['pr_product_promotion_create'])) {
			$this->access_control['product_promotion']['create'] = 1;
		}

		if(isset($data['pr_product_promotion_update'])) {
			$this->access_control['product_promotion']['update'] = 1;
		}

		if(isset($data['pr_product_promotion_delete'])) {
			$this->access_control['product_promotion']['delete'] = 1;
		}
	
		if(isset($data['pr_product_promotion_approve'])) {
			$this->access_control['product_promotion']['approve'] = 1;
		}

		if(isset($data['pr_product_promotion_disable'])) {
			$this->access_control['product_promotion']['disable'] = 1;
		}
 
		//voucher discount
		if(isset($data['voucher_discount_view'])) {
			$this->access_control['voucher_discount']['view'] = 1;
		}

		if(isset($data['voucher_discount_create'])) {
			$this->access_control['voucher_discount']['create'] = 1;
		}

		if(isset($data['voucher_discount_update'])) {
			$this->access_control['voucher_discount']['update'] = 1;
		}

		if(isset($data['voucher_discount_delete'])) {
			$this->access_control['voucher_discount']['delete'] = 1;
		}
	

		if(isset($data['voucher_discount_disable'])) {
			$this->access_control['voucher_discount']['disable'] = 1;
		}

		//Shipping Fee Discount
		if(isset($data['sfd_view'])) {
			$this->access_control['sf_discount']['view'] = 1;
		}

		if(isset($data['sfd_create'])) {
			$this->access_control['sf_discount']['create'] = 1;
		}

		if(isset($data['sfd_update'])) {
			$this->access_control['sf_discount']['update'] = 1;
		}

		if(isset($data['sfd_delete'])) {
			$this->access_control['sf_discount']['delete'] = 1;
		}



		// developer settings
		if(isset($data['main_nav_ac_devsettings_view'])) {
			$this->access_control['developer_settings'] = 1;
		}

		//shop utilities
		if(isset($data['dev_settings_shop_utilities_view'])) {
			$this->access_control['shop_utilities']['view'] = 1;
		}

		if(isset($data['dev_settings_shop_utilities_update'])) {
			$this->access_control['shop_utilities']['update'] = 1;
		}

		//content navigation
		if(isset($data['dev_settings_content_navigation_view'])) {
			$this->access_control['content_navigation']['view'] = 1;
		}

		if(isset($data['dev_settings_content_navigation_create'])) {
			$this->access_control['content_navigation']['create'] = 1;
		}

		if(isset($data['dev_settings_content_navigation_update'])) {
			$this->access_control['content_navigation']['update'] = 1;
		}

		if(isset($data['dev_settings_content_navigation_disable'])) {
			$this->access_control['content_navigation']['disable'] = 1;
		}

		if(isset($data['dev_settings_content_navigation_delete'])) {
			$this->access_control['content_navigation']['delete'] = 1;
		}

		//cron logs
		if(isset($data['dev_settings_cron_logs_view'])) {
			$this->access_control['cron_logs']['view'] = 1;
		}

		if(isset($data['dev_settings_cron_logs_disable'])) {
			$this->access_control['cron_logs']['disable'] = 1;
		}

		//manual cron
		if(isset($data['dev_settings_mcron_view'])) {
			$this->access_control['manual_cron'] = 1;
		}

		//client info
		if(isset($data['dev_settings_clief_info_view'])) {
			$this->access_control['client_information']['view'] = 1;
		}

		if(isset($data['dev_settings_clief_info_create'])) {
			$this->access_control['client_information']['create'] = 1;
		}

		if(isset($data['dev_settings_clief_info_update'])) {
			$this->access_control['client_information']['update'] = 1;
		}

		if(isset($data['dev_settings_clief_info_disable'])) {
			$this->access_control['client_information']['disable'] = 1;
		}

		if(isset($data['dev_settings_clief_info_delete'])) {
			$this->access_control['client_information']['delete'] = 1;
		}

		//mainte page
		if(isset($data['dev_settings_mainte_page_view'])) {
			$this->access_control['maintenance_page']['view'] = 1;
		}

		if(isset($data['dev_settings_mainte_page_update'])) {
			$this->access_control['maintenance_page']['update'] = 1;
		}

		//api request postback logs
		if(isset($data['dev_settings_api_request_postback_logs_view'])) {
			$this->access_control['api_request_postback_logs']['view'] = 1;
		}

		//email settings
		if(isset($data['dev_settings_email_view'])) {
			$this->access_control['email_settings']['view'] = 1;
		}

		if(isset($data['dev_settings_email_update'])) {
			$this->access_control['email_settings']['update'] = 1;
		}

		if(isset($data['batch_upload_branch'])) {
			$this->access_control['batch_upload_shopbranch']['upload'] = 1;
		}
	
		//audit trail
		if(isset($data['dev_settings_audit_trail_view'])) {
			$this->access_control['audit_trail']['view'] = 1;
		}

		//Pandabooks Api Logs
		if(isset($data['dev_settings_pandabooks_api_logs_view'])) {
			$this->access_control['pandabooks_api_logs']['view'] = 1;
		}
		
		//postback logs
		if(isset($data['dev_settings_postback_logs_view'])) {
			$this->access_control['api_postback_logs']['view'] = 1;
		}

		return json_encode($this->access_control);
	}

	public function update_user_functions($args){
		$sql = 'INSERT into `sys_users` (id, functions)
		VALUES '.$args.'

		ON DUPLICATE KEY UPDATE
			functions = VALUES(functions)';
		return $this->db->query($sql);
		// return $this->db->query($sql, $args);
	}
}