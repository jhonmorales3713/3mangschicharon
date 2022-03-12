<?php 
class Model_user_list extends CI_Model {

	public function check_username($username, $id = 0)
	{
		$query="SELECT * FROM sys_users WHERE username = ? AND id != ? AND active = '1' ";
		$args = array(
			$username,
			$id
		);
		$result = $this->db->query($query,$args);
		if($result->num_rows()>0){
			return false;
		}else{
			return true;
		}
    }

    
	public function create_user($status,$password,$username,$avatar,$functions, $args = '', $muserlist = '')
	{
		// for cp_main_navigation purposes
		$sql = "SELECT `main_nav_id`, `main_nav_href` FROM `cp_main_navigation` WHERE `enabled` > 0";
		$queries = $this->db->query($sql);

		// $main_nav_ac_dashboard_view = $this->input->post('ac_dashboard_view');
		// $main_nav_dashboard = ($main_nav_ac_dashboard_view) ? 1 : 0;

		// $main_nav_ac_orders_view = $this->input->post('main_nav_ac_orders_view');
		// $main_nav_orders = ($main_nav_ac_orders_view) ? 1 : 0;

		$main_nav_ac_products_view = $this->input->post('main_nav_ac_products_view');
		$main_nav_products = ($main_nav_ac_products_view) ? 1 : 0;

        
		$main_nav_ac_settings_view_aul = $this->input->post('settings_aul_view');
		$main_nav_settings = ($main_nav_ac_settings_view_aul) ? 1 : 0;

		// $main_nav_ac_shops_view = $this->input->post('main_nav_ac_shops_view');
		// $main_nav_shops = ($main_nav_ac_shops_view) ? 1 : 0;

		// $main_nav_ac_customers_view = $this->input->post('main_nav_ac_customers_view');
		// $main_nav_customers = ($main_nav_ac_customers_view) ? 1 : 0;

		// $main_nav_ac_accounts_view = $this->input->post('main_nav_ac_accounts_view');
		// $main_nav_accounts = ($main_nav_ac_accounts_view) ? 1 : 0;

		// $main_nav_ac_remittance_view = $this->input->post('main_nav_ac_remittance_view');
		// $main_nav_remittance = ($main_nav_ac_remittance_view) ? 1 : 0;

		// $main_nav_ac_vouchers_view = $this->input->post('main_nav_ac_vouchers_view');
		// $main_nav_vouchers = ($main_nav_ac_vouchers_view) ? 1 : 0;

		// $ac_vouchers_list_view = $this->input->post('ac_vouchers_list_view');
		// $ac_vouchers_list_view = ($ac_vouchers_list_view) ? 1 : 0;

		// $main_nav_ac_wallet_view = $this->input->post('main_nav_ac_wallet_view');
		// $main_nav_wallet = ($main_nav_ac_wallet_view) ? 1 : 0;

		// $main_nav_ac_reports_view = $this->input->post('main_nav_ac_reports_view');
		// $main_nav_reports = ($main_nav_ac_reports_view) ? 1 : 0;

		// $main_nav_ac_settings_view = $this->input->post('main_nav_ac_settings_view');
		// $main_nav_settings = ($main_nav_ac_settings_view) ? 1 : 0;

		// $main_nav_ac_csr_view = $this->input->post('main_nav_ac_csr_view');
		// $main_nav_ac_csr_view = ($main_nav_ac_csr_view) ? 1 : 0;

		// $main_nav_pr_product_promotion_view = $this->input->post('main_nav_pr_product_promotion_view');
		// $main_nav_pr_product_promotion_view = ($main_nav_pr_product_promotion_view) ? 1 : 0;

		// $main_nav_ac_devsettings_view = $this->input->post('main_nav_ac_devsettings_view');
		// $main_nav_devsettings = ($main_nav_ac_devsettings_view) ? 1 : 0;
		

		$array_main_nav_id = [];

		if ($queries->num_rows() > 0) {
			foreach ($queries->result() as $row) {

				
				// if ($main_nav_dashboard == 1) {
				// 	$main_nav_href_string = 'home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_orders == 1) {
				// 	$main_nav_href_string = 'orders_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				if ($main_nav_products == 1) {
					$main_nav_href_string = 'products_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}

				// if ($main_nav_shops == 1) {
				// 	$main_nav_href_string = 'shops_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_customers == 1) {
				// 	$main_nav_href_string = 'customers_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_accounts == 1) {
				// 	$main_nav_href_string = 'accounts_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }


				// if ($main_nav_remittance == 1) {
				// 	$main_nav_href_string = 'remittance_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_vouchers == 1) {
				// 	$main_nav_href_string = 'vouchers_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_wallet == 1) {
				// 	$main_nav_href_string = 'wallet_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_reports == 1) {
				// 	$main_nav_href_string = 'report_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				if ($main_nav_settings == 1) {
					$main_nav_href_string = 'settings_home'; //get reference in cp_main_navigation
					if ($main_nav_href_string == $row->main_nav_href) {
						$array_main_nav_id[] = $row->main_nav_id;
					} 
				}



				// if ($main_nav_ac_csr_view == 1) {
				// 	$main_nav_href_string = 'csr_section_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }

				// if ($main_nav_pr_product_promotion_view == 1) {
				// 	$main_nav_href_string = 'promotion_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }


				// if ($main_nav_devsettings == 1) {
				// 	$main_nav_href_string = 'dev_settings_home'; //get reference in cp_main_navigation
				// 	if ($main_nav_href_string == $row->main_nav_href) {
				// 		$array_main_nav_id[] = $row->main_nav_id;
				// 	} 
				// }
			}
		}

		$list_main_nav_id = implode(', ', $array_main_nav_id); 

		// end for cp_main_navigation purposes

		// for cp_content_navigation purposes
		$sql = "SELECT `id`, `cn_name` FROM `cp_content_navigation` WHERE `status` = 1";
		$queries = $this->db->query($sql);

		// ac_transactions_view
		// ac_products_view
		// ac_shops_view
		// ac_customer_view
		// ac_billing_view
		// billing_portal_fee_view
		// ac_vouchers_claimed_view
		// ac_prepayment_view
		// ac_manual_order_view
		// ac_ps_view
		// ac_por_view
		// ac_sr_view
		// ac_ssr_view
		// ac_pr_view
		// ac_psr_view
		// ac_settings_delivery_areas_view
		// ac_settings_announcement_view
		// ac_settings_members_view
		// ac_settings_payment_type_view
		// ref_comrate_view
		// settings_region_view
		// settings_city_view
		// settings_province_view
		// ac_shipping_and_delivery_view
		// ac_settings_category_view
		// ac_settings_shipping_partners_view
		// ac_settings_shop_banners_view
		// ac_settings_shop_branch_view
		// ac_csr_ticket_history_view
		// ac_csr_ticket_view
		// ac_csr_ticket_log_view
		// ac_settings_void_record_process
		// ac_settings_void_record_list_view
		// ac_settings_users_view
		
		// $ac_products_view = $this->input->post('ac_products_view');
		// $ac_products_view = ($ac_products_view) ? 1 : 0;

		// $ac_transactions_view = $this->input->post('ac_transactions_view');
		// $ac_transactions_view = ($ac_transactions_view) ? 1 : 0;

		// $ac_merchant_ol_view = $this->input->post('ac_merchant_ol_view');
		// $ac_merchant_ol_view = ($ac_merchant_ol_view) ? 1 : 0;

		// $ac_pending_orders = $this->input->post('ac_pending_orders');
		// $ac_pending_orders = ($ac_pending_orders) ? 1 : 0;

		// $ac_cancelled_orders = $this->input->post('ac_cancelled_orders');
		// $ac_cancelled_orders = ($ac_cancelled_orders) ? 1 : 0;

		// $ac_incomplete_orders = $this->input->post('ac_incomplete_orders');
		// $ac_incomplete_orders = ($ac_incomplete_orders) ? 1 : 0;


		// $ac_paid_orders = $this->input->post('ac_paid_orders');
		// $ac_paid_orders = ($ac_paid_orders) ? 1 : 0;

		// $ac_readyforprocessing_orders = $this->input->post('ac_readyforprocessing_orders');
		// $ac_readyforprocessing_orders = ($ac_readyforprocessing_orders) ? 1 : 0;

		// $ac_processing_orders = $this->input->post('ac_processing_orders');
		// $ac_processing_orders = ($ac_processing_orders) ? 1 : 0;
		
		// $ac_readyforpickup_orders = $this->input->post('ac_readyforpickup_orders');
		// $ac_readyforpickup_orders = ($ac_readyforpickup_orders) ? 1 : 0;
		
		// $ac_bookingconfirmed_orders = $this->input->post('ac_bookingconfirmed_orders');
		// $ac_bookingconfirmed_orders = ($ac_bookingconfirmed_orders) ? 1 : 0;
		
		// $ac_fulfilled_orders = $this->input->post('ac_fulfilled_orders');
		// $ac_fulfilled_orders = ($ac_fulfilled_orders) ? 1 : 0;
		
		// $ac_shipped_orders = $this->input->post('ac_shipped_orders');
		// $ac_shipped_orders = ($ac_shipped_orders) ? 1 : 0;
		
		// $ac_returntosender_orders = $this->input->post('ac_returntosender_orders');
		// $ac_returntosender_orders = ($ac_returntosender_orders) ? 1 : 0;

		// $ac_voided_orders = $this->input->post('ac_voided_orders');
		// $ac_voided_orders = ($ac_voided_orders) ? 1 : 0;

		// $ac_forpickup_orders = $this->input->post('ac_forpickup_orders');
		// $ac_forpickup_orders = ($ac_forpickup_orders) ? 1 : 0;

		// $ac_confirmed_order_list = $this->input->post('ac_confirmed_order_list');
		// $ac_confirmed_order_list = ($ac_confirmed_order_list) ? 1 : 0;

		// $ac_manualorder_list = $this->input->post('ac_manualorder_list');
		// $ac_manualorder_list = ($ac_manualorder_list) ? 1 : 0;
		
		// $ac_refund_order_trans = $this->input->post('ac_refund_order_trans_view');
		// $ac_refund_order_trans = ($ac_refund_order_trans) ? 1 : 0;
		
		// $ac_refund_order_approval = $this->input->post('ac_refund_order_approval_view');
		// $ac_refund_order_approval = ($ac_refund_order_approval) ? 1 : 0;

		// $ac_refund_order = $this->input->post('ac_refund_order_create');
		// $ac_refund_order = ($ac_refund_order) ? 1 : 0;

		$ac_products_view = $this->input->post('ac_products_view');
		$ac_products_view = ($ac_products_view) ? 1 : 0;
        
		$ac_settings_aul_view = $this->input->post('settings_aul_view');
		$ac_settings_aul_view = ($ac_settings_aul_view) ? 1 : 0;

		// $ac_menu_plan_view = $this->input->post('ac_menu_plan_view');
		// $ac_menu_plan_view = ($ac_menu_plan_view) ? 1 : 0;

		// $ac_products_wfa_view = $this->input->post('ac_products_wfa_view');
		// $ac_products_wfa_view = ($ac_products_wfa_view) ? 1 : 0;

		// $ac_products_apr_view = $this->input->post('ac_products_apr_view');
		// $ac_products_apr_view = ($ac_products_apr_view) ? 1 : 0;

		// $ac_products_verified_view = $this->input->post('ac_products_verified_view');
		// $ac_products_verified_view = ($ac_products_verified_view) ? 1 : 0;

		// $ac_products_dec_view = $this->input->post('ac_products_dec_view');
		// $ac_products_dec_view = ($ac_products_dec_view) ? 1 : 0;

		// $ac_products_changes_view = $this->input->post('ac_products_changes_view');
		// $ac_products_changes_view = ($ac_products_changes_view) ? 1 : 0;


		// $ac_shops_view = $this->input->post('ac_shops_view');
		// $ac_shops_view = ($ac_shops_view) ? 1 : 0;

		// $ac_bnl_view = $this->input->post('ac_bnl_view');
		// $ac_bnl_view = ($ac_bnl_view) ? 1 : 0;

		// $ac_cl_view = $this->input->post('ac_cl_view');
		// $ac_cl_view = ($ac_cl_view) ? 1 : 0;

		// $ac_linked_accounts_view = $this->input->post('ac_linked_accounts_view');
		// $ac_linked_accounts_view = ($ac_linked_accounts_view) ? 1 : 0;

		// $branch_account_view = $this->input->post('branch_account_view');
		// $branch_account_view = ($branch_account_view) ? 1 : 0;

		// $shop_account_view = $this->input->post('shop_account_view');
		// $shop_account_view = ($shop_account_view) ? 1 : 0;

		// $ac_customer_view = $this->input->post('ac_customer_view');
		// $ac_customer_view = ($ac_customer_view) ? 1 : 0;

		// $ac_billing_view = $this->input->post('ac_billing_view');
		// $ac_billing_view = ($ac_billing_view) ? 1 : 0;

		
		// $ac_billing_merchant_view = $this->input->post('ac_billing_merchant_view');
		// $ac_billing_merchant_view = ($ac_billing_merchant_view) ? 1 : 0;

		// $billing_portal_fee_view = $this->input->post('billing_portal_fee_view');
		// $billing_portal_fee_view = ($billing_portal_fee_view) ? 1 : 0;

		// $ac_vouchers_claimed_view = $this->input->post('ac_vouchers_claimed_view');
		// $ac_vouchers_claimed_view = ($ac_vouchers_claimed_view) ? 1 : 0;

		// $ac_vouchers_list_view = $this->input->post('ac_vouchers_list_view');
		// $ac_vouchers_list_view = ($ac_vouchers_list_view) ? 1 : 0;

		// $ac_prepayment_view = $this->input->post('ac_prepayment_view');
		// $ac_prepayment_view = ($ac_prepayment_view) ? 1 : 0;

		// $ac_manual_order_view = $this->input->post('ac_manual_order_view');
		// $ac_manual_order_view = ($ac_manual_order_view) ? 1 : 0;

		// $ac_wallet_page_view = $this->input->post('ac_wallet_page_view');
		// $ac_wallet_page_view = ($ac_wallet_page_view) ? 1 : 0;

		// $ac_ps_view = $this->input->post('ac_ps_view');
		// $ac_ps_view = ($ac_ps_view) ? 1 : 0;

		// $ac_pr_view = $this->input->post('ac_pr_view');
		// $ac_pr_view = ($ac_pr_view) ? 1 : 0;

		// $ac_psr_view = $this->input->post('ac_psr_view');
		// $ac_psr_view = ($ac_psr_view) ? 1 : 0;

		// $ac_por_view = $this->input->post('ac_por_view');
		// $ac_por_view = ($ac_por_view) ? 1 : 0;

		// $ac_sr_view = $this->input->post('ac_sr_view');
		// $ac_sr_view = ($ac_sr_view) ? 1 : 0;

		// $ac_tbr_view = $this->input->post('ac_tbr_view');
		// $ac_tbr_view = ($ac_tbr_view) ? 1 : 0;

		// $ac_olps_view = $this->input->post('ac_olps_view');
		// $ac_olps_view = ($ac_olps_view) ? 1 : 0;

		// $ac_ssr_view = $this->input->post('ac_ssr_view');
		// $ac_ssr_view = ($ac_ssr_view) ? 1 : 0;

		// //new reports
		// $ac_aov_view = $this->input->post('ac_aov_view');
		// $ac_aov_view = ($ac_aov_view) ? 1 : 0;
		
		// $ac_to_view = $this->input->post('ac_to_view');
		// $ac_to_view = ($ac_to_view) ? 1 : 0;

		// $ac_os_view = $this->input->post('ac_os_view');
		// $ac_os_view = ($ac_os_view) ? 1 : 0;

		// $ac_tps_view = $this->input->post('ac_tps_view');
		// $ac_tps_view = ($ac_tps_view) ? 1 : 0;

		// $ac_tsr_view = $this->input->post('ac_tsr_view');
		// $ac_tsr_view = ($ac_tsr_view) ? 1 : 0;

		// $ac_wtr_view = $this->input->post('ac_wtr_view');
		// $ac_wtr_view = ($ac_wtr_view) ? 1 : 0;

		// $ac_msr_view = $this->input->post('ac_msr_view');
		// $ac_msr_view = ($ac_msr_view) ? 1 : 0;

		// $ac_rbsr_view = $this->input->post('ac_rbsr_view');
		// $ac_rbsr_view = ($ac_rbsr_view) ? 1 : 0;

		// $ac_rbbr_view = $this->input->post('ac_rbbr_view');
		// $ac_rbbr_view = ($ac_rbbr_view) ? 1 : 0;

		// $ac_oscrr_view = $this->input->post('ac_oscrr_view');
		// $ac_oscrr_view = ($ac_oscrr_view) ? 1 : 0;

		// $ac_tacr_view = $this->input->post('ac_tacr_view');
		// $ac_tacr_view = ($ac_tacr_view) ? 1 : 0;

		// $ac_po_view = $this->input->post('ac_po_view');
		// $ac_po_view = ($ac_po_view) ? 1 : 0;

		// $ac_inv_view = $this->input->post('ac_inv_view');
		// $ac_inv_view = ($ac_inv_view) ? 1 : 0;

		// $ac_invend_view = $this->input->post('ac_invend_view');
		// $ac_invend_view = ($ac_invend_view) ? 1 : 0;

		// $ac_invlist_view = $this->input->post('ac_invlist_view');
		// $ac_invlist_view = ($ac_invlist_view) ? 1 : 0;
		
		// $ac_osr_view = $this->input->post('ac_osr_view');
		// $ac_osr_view = ($ac_osr_view) ? 1 : 0;
		
		// $ac_rbl_view = $this->input->post('ac_rbl_view');
		// $ac_rbl_view = ($ac_rbl_view) ? 1 : 0;
		
		// $ac_oblr_view = $this->input->post('ac_oblr_view');
		// $ac_oblr_view = ($ac_oblr_view) ? 1 : 0;
		
		// $ac_bpr_view = $this->input->post('ac_bpr_view');
		// $ac_bpr_view = ($ac_bpr_view) ? 1 : 0;
		
		// $ac_prr_view = $this->input->post('ac_prr_view');
		// $ac_prr_view = ($ac_prr_view) ? 1 : 0;
		
		// $ac_or_view = $this->input->post('ac_or_view');
		// $ac_or_view = ($ac_or_view) ? 1 : 0;

		// $ac_rosum_view = $this->input->post('ac_rosum_view');
		// $ac_rosum_view = ($ac_rosum_view) ? 1 : 0;
		
		// $ac_rostat_view = $this->input->post('ac_rostat_view');
		// $ac_rostat_view = ($ac_rostat_view) ? 1 : 0;

		// $ac_settings_delivery_areas_view = $this->input->post('ac_settings_delivery_areas_view');
		// $ac_settings_delivery_areas_view = ($ac_settings_delivery_areas_view) ? 1 : 0;

		// $ac_settings_announcement_view = $this->input->post('ac_settings_announcement_view');
		// $ac_settings_announcement_view = ($ac_settings_announcement_view) ? 1 : 0;

		// $ac_settings_members_view = $this->input->post('ac_settings_members_view');
		// $ac_settings_members_view = ($ac_settings_members_view) ? 1 : 0;

		// $ac_settings_muserlist_view = $this->input->post('ac_settings_muserlist_view');
		// $ac_settings_muserlist_view = ($ac_settings_muserlist_view) ? 1 : 0;

		
		// $settings_aul_view = $this->input->post('settings_aul_view');
		// $settings_aul_view = ($settings_aul_view) ? 1 : 0;

		// $ac_settings_payment_type_view = $this->input->post('ac_settings_payment_type_view');
		// $ac_settings_payment_type_view = ($ac_settings_payment_type_view) ? 1 : 0;

		// $ac_settings_currency_view = $this->input->post('ac_settings_currency_view');
		// $ac_settings_currency_view = ($ac_settings_currency_view) ? 1 : 0;

		// $ref_comrate_view = $this->input->post('ref_comrate_view');
		// $ref_comrate_view = ($ref_comrate_view) ? 1 : 0;

		// $settings_roles_view = $this->input->post('settings_roles_view');
		// $settings_roles_view = ($settings_roles_view) ? 1 : 0;

		// $settings_region_view = $this->input->post('settings_region_view');
		// $settings_region_view = ($settings_region_view) ? 1 : 0;

		// $settings_city_view = $this->input->post('settings_city_view');
		// $settings_city_view = ($settings_city_view) ? 1 : 0;

		// $settings_province_view = $this->input->post('settings_province_view');
		// $settings_province_view = ($settings_province_view) ? 1 : 0;

		// $ac_shipping_and_delivery_view = $this->input->post('ac_shipping_and_delivery_view');
		// $ac_shipping_and_delivery_view = ($ac_shipping_and_delivery_view) ? 1 : 0;

		// $ac_settings_category_view = $this->input->post('ac_settings_category_view');
		// $ac_settings_category_view = ($ac_settings_category_view) ? 1 : 0;
		
		// $ac_settings_shipping_partners_view = $this->input->post('ac_settings_shipping_partners_view');
		// $ac_settings_shipping_partners_view = ($ac_settings_shipping_partners_view) ? 1 : 0;

		// $ac_settings_shop_banners_view = $this->input->post('ac_settings_shop_banners_view');
		// $ac_settings_shop_banners_view = ($ac_settings_shop_banners_view) ? 1 : 0;

		// $ac_settings_shop_branch_view = $this->input->post('ac_settings_shop_branch_view');
		// $ac_settings_shop_branch_view = ($ac_settings_shop_branch_view) ? 1 : 0;

		// $ac_csr_ticket_history_view = $this->input->post('ac_csr_ticket_history_view');
		// $ac_csr_ticket_history_view = ($ac_csr_ticket_history_view) ? 1 : 0;

		// $ac_csr_ticket_view = $this->input->post('ac_csr_ticket_view');
		// $ac_csr_ticket_view = ($ac_csr_ticket_view) ? 1 : 0;

		// $ac_csr_ticket_log_view = $this->input->post('ac_csr_ticket_log_view');
		// $ac_csr_ticket_log_view = ($ac_csr_ticket_log_view) ? 1 : 0;

		// $ac_settings_void_record_process = $this->input->post('ac_settings_void_record_process');
		// $ac_settings_void_record_process = ($ac_settings_void_record_process) ? 1 : 0;
		
		// $ac_settings_void_record_list_view = $this->input->post('ac_settings_void_record_list_view');
		// $ac_settings_void_record_list_view = ($ac_settings_void_record_list_view) ? 1 : 0;

		// $faqs_view = $this->input->post('faqs_view');
		// $faqs_view = ($faqs_view) ? 1 : 0;

		// $ac_settings_bank_view = $this->input->post('ac_settings_bank_view');
		// $ac_settings_bank_view = ($ac_settings_bank_view) ? 1 : 0;

		// $ac_settings_co_view = $this->input->post('ac_settings_co_view');
		// $ac_settings_co_view = ($ac_settings_co_view) ? 1 : 0;
		
		// $ac_settings_users_view = $this->input->post('ac_settings_users_view');
		// $ac_settings_users_view = ($ac_settings_users_view) ? 1 : 0;

		// //promotion
		// //piso deals
		// $pr_product_promotion_view = $this->input->post('pr_product_promotion_view');
		// $pr_product_promotion_view = ($pr_product_promotion_view) ? 1 : 0;

		
		// $voucher_discount_view = $this->input->post('voucher_discount_view');
		// $voucher_discount_view = ($voucher_discount_view) ? 1 : 0;

		// $sfd_view = $this->input->post('sfd_view');
		// $sfd_view = ($sfd_view) ? 1 : 0;

		// // dev settings
		// $dev_settings_shop_utilities_view = $this->input->post('dev_settings_shop_utilities_view');
		// $dev_settings_shop_utilities_view = ($dev_settings_shop_utilities_view) ? 1 : 0;

		// $dev_settings_content_navigation_view = $this->input->post('dev_settings_content_navigation_view');
		// $dev_settings_content_navigation_view = ($dev_settings_content_navigation_view) ? 1 : 0;

		// $dev_settings_cron_logs_view = $this->input->post('dev_settings_cron_logs_view');
		// $dev_settings_cron_logs_view = ($dev_settings_cron_logs_view) ? 1 : 0;

		// $dev_settings_mcron_view = $this->input->post('dev_settings_mcron_view');
		// $dev_settings_mcron_view = ($dev_settings_mcron_view) ? 1 : 0;

		// $dev_settings_clief_info_view = $this->input->post('dev_settings_clief_info_view');
		// $dev_settings_clief_info_view = ($dev_settings_clief_info_view) ? 1 : 0;

		// $dev_settings_mainte_page_view = $this->input->post('dev_settings_mainte_page_view');
		// $dev_settings_mainte_page_view = ($dev_settings_mainte_page_view) ? 1 : 0;

		// $dev_settings_api_request_postback_logs_view = $this->input->post('dev_settings_api_request_postback_logs_view');
		// $dev_settings_api_request_postback_logs_view = ($dev_settings_api_request_postback_logs_view) ? 1 : 0;

		// $dev_settings_email_view = $this->input->post('dev_settings_email_view');
		// $dev_settings_email_view = ($dev_settings_email_view) ? 1 : 0;

		// $shop_mer_reg_view = $this->input->post('shop_mer_reg_view');
		// $shop_mer_reg_view = ($shop_mer_reg_view) ? 1 : 0;

		// $shop_changes_view = $this->input->post('shop_changes_view');
		// $shop_changes_view = ($shop_changes_view) ? 1 : 0;

		// $shop_mcr_view = $this->input->post('shop_mcr_view');
		// $shop_mcr_view = ($shop_mcr_view) ? 1 : 0;


		// $dev_settings_audit_trail_view = $this->input->post('dev_settings_audit_trail_view');
		// $dev_settings_audit_trail_view = ($dev_settings_audit_trail_view) ? 1 : 0;

		// $dev_settings_postback_logs_view = $this->input->post('dev_settings_postback_logs_view');
		// $dev_settings_postback_logs_view = ($dev_settings_postback_logs_view) ? 1 : 0;

		// $dev_settings_pandabooks_api_logs_view = $this->input->post('dev_settings_pandabooks_api_logs_view');
		// $dev_settings_pandabooks_api_logs_view = ($dev_settings_pandabooks_api_logs_view) ? 1 : 0; 


		$array_content_nav_id = [];

		if ($queries->num_rows() > 0) {
			foreach ($queries->result() as $row) {

				if ($ac_products_view == 1) {
					$content_nav_href_string = 'Products'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
				}
                
                if($ac_settings_aul_view == 1){
					$content_nav_href_string = 'User List'; //get reference in cp_content_navigation->cn_name
					if ($content_nav_href_string == $row->cn_name) {
						$array_content_nav_id[] = $row->id;
					} 
                }

				// if ($ac_cancelled_orders == 1) {
				// 	$content_nav_href_string = 'Cancelled Orders'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_incomplete_orders == 1) {
				// 	$content_nav_href_string = 'Incomplete Orders'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_merchant_ol_view == 1) {
				// 	$content_nav_href_string = 'Merchant Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_pending_orders == 1) {
				// 	$content_nav_href_string = 'Pending Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_paid_orders == 1) {
				// 	$content_nav_href_string = 'Paid Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_readyforprocessing_orders == 1) {
				// 	$content_nav_href_string = 'Ready for Processing Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_processing_orders == 1) {
				// 	$content_nav_href_string = 'Processing Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_readyforpickup_orders == 1) {
				// 	$content_nav_href_string = 'Ready for Pickup Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_bookingconfirmed_orders == 1) {
				// 	$content_nav_href_string = 'Booking Confirmed Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_fulfilled_orders == 1) {
				// 	$content_nav_href_string = 'Fulfilled Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_shipped_orders == 1) {
				// 	$content_nav_href_string = 'Shipped Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_returntosender_orders == 1) {
				// 	$content_nav_href_string = 'Return to Sender Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_voided_orders == 1) {
				// 	$content_nav_href_string = 'Voided Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_forpickup_orders == 1) {
				// 	$content_nav_href_string = 'For Pick up Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_confirmed_order_list == 1) {
				// 	$content_nav_href_string = 'Delivery Confirmed Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_manualorder_list == 1) {
				// 	$content_nav_href_string = 'Manual Order List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_refund_order_trans == 1) {
				// 	$content_nav_href_string = 'Refund Order Transactions'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_refund_order_approval == 1) {
				// 	$content_nav_href_string = 'Refund Order Approval'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_refund_order == 1) {
				// 	$content_nav_href_string = 'Refund Order'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_products_view == 1) {
				// 	$content_nav_href_string = 'Menu List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_menu_plan_view == 1) {
				// 	$content_nav_href_string = 'Menu Plan'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_products_wfa_view == 1) {
				// 	$content_nav_href_string = 'Menu Waiting for Approval'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_products_apr_view == 1) {
				// 	$content_nav_href_string = 'Menu Approved'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_products_dec_view == 1) {
				// 	$content_nav_href_string = 'Menu Declined'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				
				// if ($ac_products_verified_view == 1) {
				// 	$content_nav_href_string = 'Menu Verified'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_products_changes_view == 1) {
				// 	$content_nav_href_string = 'Menu Changes Approval'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_shops_view == 1) {
				// 	$content_nav_href_string = 'Shop Profile'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_bnl_view == 1) {
				// 	$content_nav_href_string = 'Brand Name List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_cl_view == 1) {
				// 	$content_nav_href_string = 'Company List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }


				// if ($ac_linked_accounts_view == 1) {
				// 	$content_nav_href_string = 'Linked Accounts'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($branch_account_view == 1) {
				// 	$content_nav_href_string = 'Shop Branch Account'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($shop_account_view == 1) {
				// 	$content_nav_href_string = 'Shop Account'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($shop_mer_reg_view == 1) {
				// 	$content_nav_href_string = 'Merchant Registration';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($shop_changes_view == 1) {
				// 	$content_nav_href_string = 'Shop Changes Approval';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($shop_mcr_view == 1) {
				// 	$content_nav_href_string = 'Shop MCR Approval';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }


				// if ($ac_customer_view == 1) {
				// 	$content_nav_href_string = 'Customer List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_billing_view == 1) {
				// 	$content_nav_href_string = 'Billing'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_billing_merchant_view == 1) {
				// 	$content_nav_href_string = 'Invoice Billing'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($billing_portal_fee_view == 1) {
				// 	$content_nav_href_string = 'Billing (By Payment Portal Fee)'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_vouchers_claimed_view == 1) {
				// 	$content_nav_href_string = 'Claimed Vouchers'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_vouchers_list_view == 1) {
				// 	$content_nav_href_string = 'Voucher List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_prepayment_view == 1) {
				// 	$content_nav_href_string = 'Pre Payment'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_manual_order_view == 1) {
				// 	$content_nav_href_string = 'Manual Order'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_wallet_page_view == 1) {
				// 	$content_nav_href_string = 'Wallet Page'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_ps_view == 1) {
				// 	$content_nav_href_string = 'Page Statistics'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_pr_view == 1) {
				// 	$content_nav_href_string = 'Payout Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_psr_view == 1) {
				// 	$content_nav_href_string = 'Profit Sharing Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_por_view == 1) {
				// 	$content_nav_href_string = 'Product Orders Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_sr_view == 1) {
				// 	$content_nav_href_string = 'Sales Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_tbr_view == 1) {
				// 	$content_nav_href_string = 'toktok Booking Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				
				// if ($ac_olps_view == 1) {
				// 	$content_nav_href_string = 'Order List Payout Status Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }


				// if ($ac_ssr_view == 1) {
				// 	$content_nav_href_string = 'Sales Settlement Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// //new reports - content nav

				// if ($ac_aov_view == 1) {
				// 	$content_nav_href_string = 'Average Order Value Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_to_view == 1) {
				// 	$content_nav_href_string = 'Total Orders'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_os_view == 1) {
				// 	$content_nav_href_string = 'Order and Sales Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_tps_view == 1) {
				// 	$content_nav_href_string = 'Top Products Sold'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_tsr_view == 1) {
				// 	$content_nav_href_string = 'Total Sales'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_wtr_view == 1) {
				// 	$content_nav_href_string = 'Withholding Tax Reports'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_msr_view == 1) {
				// 	$content_nav_href_string = 'Merchant Serviceable Areas'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_rbsr_view == 1) {
				// 	$content_nav_href_string = 'Revenue By Store'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_rbbr_view == 1) {
				// 	$content_nav_href_string = 'Revenue By Branch'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_oscrr_view == 1) {
				// 	$content_nav_href_string = 'Online Store Conversion Rate'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// if ($ac_tacr_view == 1) {
				// 	$content_nav_href_string = 'Total Abandoned Carts'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_po_view == 1) {
				// 	$content_nav_href_string = 'Pending Orders'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_inv_view == 1) {
				// 	$content_nav_href_string = 'Inventory Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_invend_view == 1) {
				// 	$content_nav_href_string = 'Inventory Ending Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_invlist_view == 1) {
				// 	$content_nav_href_string = 'Inventory List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_osr_view == 1) {
				// 	$content_nav_href_string = 'Order Status Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_rbl_view == 1) {
				// 	$content_nav_href_string = 'Revenue By Location'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_oblr_view == 1) {
				// 	$content_nav_href_string = 'Orders By Location'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_bpr_view == 1) {
				// 	$content_nav_href_string = 'Branch Performance Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_prr_view == 1) {
				// 	$content_nav_href_string = 'Product Releasing Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_or_view == 1) {
				// 	$content_nav_href_string = 'Order Report'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_rosum_view == 1) {
				// 	$content_nav_href_string = 'Refund Order Summary'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
				
				// if ($ac_rostat_view == 1) {
				// 	$content_nav_href_string = 'Refund Order Status'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			
				// //end of new reports

				// if ($ac_settings_delivery_areas_view == 1) {
				// 	$content_nav_href_string = 'Delivery Areas'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_announcement_view == 1) {
				// 	$content_nav_href_string = 'Announcement'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_members_view == 1) {
				// 	$content_nav_href_string = 'Members'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_muserlist_view == 1) {
				// 	$content_nav_href_string = 'Merchant User List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($settings_aul_view == 1) {
				// 	$content_nav_href_string = 'Admin User List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_payment_type_view == 1) {
				// 	$content_nav_href_string = 'Payment Types'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_currency_view == 1) {
				// 	$content_nav_href_string = 'Currency'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ref_comrate_view == 1) {
				// 	$content_nav_href_string = 'Referral Comrate'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($settings_roles_view == 1) {
				// 	$content_nav_href_string = 'Roles'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($settings_region_view == 1) {
				// 	$content_nav_href_string = 'Region'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($settings_city_view == 1) {
				// 	$content_nav_href_string = 'City'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($settings_province_view == 1) {
				// 	$content_nav_href_string = 'Province'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_shipping_and_delivery_view == 1) {
				// 	$content_nav_href_string = 'Shipping and Delivery'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_category_view == 1) {
				// 	$content_nav_href_string = 'Menu Category'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_shipping_partners_view == 1) {
				// 	$content_nav_href_string = 'Shipping Partners'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_shop_banners_view == 1) {
				// 	$content_nav_href_string = 'Shop Banners'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_shop_branch_view == 1) {
				// 	$content_nav_href_string = 'Shop Branch'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_csr_ticket_history_view == 1) {
				// 	$content_nav_href_string = 'Ticket Transaction History'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_csr_ticket_view == 1) {
				// 	$content_nav_href_string = 'Create Ticket'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_csr_ticket_log_view == 1) {
				// 	$content_nav_href_string = 'Ticket Log'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($pr_product_promotion_view == 1) {
				// 	$content_nav_href_string = 'Piso Deals'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($voucher_discount_view == 1) {
				// 	$content_nav_href_string = 'Vouchers Discount'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($sfd_view == 1) {
				// 	$content_nav_href_string = 'Delivery Fee Discount'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_void_record_process == 1) {
				// 	$content_nav_href_string = 'Void Record'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_void_record_list_view == 1) {
				// 	$content_nav_href_string = 'Void Record List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_bank_view == 1) {
				// 	$content_nav_href_string = 'Bank List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($ac_settings_co_view == 1) {
				// 	$content_nav_href_string = 'Cancellation Options'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($faqs_view == 1) {
				// 	$content_nav_href_string = 'Faqs'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }


				// if ($ac_settings_users_view == 1) {
				// 	$content_nav_href_string = 'User List'; //get reference in cp_content_navigation->cn_name
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($dev_settings_shop_utilities_view == 1) {
				// 	$content_nav_href_string = 'Shop Utilities';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($dev_settings_content_navigation_view == 1) {
				// 	$content_nav_href_string = 'Content Navigation';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
		
				// if ($dev_settings_cron_logs_view == 1) {
				// 	$content_nav_href_string = 'Cron Logs';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
		
				// if ($dev_settings_mcron_view == 1) {
				// 	$content_nav_href_string = 'Manual Cron';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
		
				// if ($dev_settings_clief_info_view == 1) {
				// 	$content_nav_href_string = 'Client Information';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($dev_settings_mainte_page_view == 1) {
				// 	$content_nav_href_string = 'Maintenance Page';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($dev_settings_api_request_postback_logs_view == 1) {
				// 	$content_nav_href_string = 'Api Request Postback Logs';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

			

				// if ($dev_settings_audit_trail_view == 1) {
				// 	$content_nav_href_string = 'Audit Trail';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
	
				// if ($dev_settings_pandabooks_api_logs_view == 1) {
				// 	$content_nav_href_string = 'Pandabooks Api Logs';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			

				// if ($dev_settings_postback_logs_view == 1) {
				// 	$content_nav_href_string = 'API Postback Logs';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }

				// if ($dev_settings_email_view == 1) {
				// 	$content_nav_href_string = 'Email Settings';
				// 	if ($content_nav_href_string == $row->cn_name) {
				// 		$array_content_nav_id[] = $row->id;
				// 	} 
				// }
			}
		}
		$list_content_nav_id = implode(', ', $array_content_nav_id);
		
        // var_dump($list_main_nav_id);
        // var_dump($functions);
        // die();
		// end for cp_content_navigation purposes

		$argument = array(
			$status,
			password_hash($password,PASSWORD_BCRYPT,array('cost' => 12)),
			$username,
			$avatar,
			$functions,
			$list_main_nav_id,
			$list_content_nav_id,
			0,
			0,
			0,
			1
		);
		$query="INSERT INTO sys_users (active, password, username, avatar, functions, access_nav, access_content_nav, failed_login_attempts, first_login, code_isset, login_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		 $query = $this->db->query($query,$argument);

		//  if($muserlist == 1){
		//  	$sys_user_id = $this->db->insert_id();
		//  	$query="INSERT INTO app_members (member_type, sys_user, sys_shop, fname, mname, lname, email, mobile_number, address, position, comm_type, created, status, role_id, company_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		//  	$params = array(
		// 		1,
		// 		$sys_user_id,
		// 		$args['f_shops'],
		// 		$args['f_fname'],
		// 		$args['f_mname'],
		// 		$args['f_lname'],
		// 		$args['f_email'],
		// 		$args['f_conno'],
		// 		$args['f_address'],
		// 		$args['f_position'],
		// 		0,
		// 		date('Y-m-d H:i:s'),
		// 		1,
		// 		$args['f_roles'],
		// 		$args['f_company'] ?? 0,
		// 	);
		//  	$this->db->query($query,$params);
		//  }

		 return $query;
	}

	public function user_list_table($filters, $requestData, $exportable = false)
	{
		$_record_status = $filters['_record_status'];
		$_username 			= $filters['_username'];
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);

		$columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'username',
            3 => 'active'
		);

		$sql = "SELECT * FROM sys_users WHERE active > 0";
		$query = $this->db->query($sql);
		$totalData = $query->num_rows();
		$totalFiltered = $totalData; 

		$sql = "SELECT * FROM sys_users WHERE 1";

		// start - for default search
		if ($_record_status == 1) {
			$sql.=" AND active = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" AND active = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" AND active > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if ($_username != "") {
			$sql.=" AND username LIKE '%" . $this->db->escape_like_str($_username) . "%' ";
		}
		// end - getting records as per search parameters

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		if (!$exportable) {
			$sql .= " LIMIT ".$requestData['start']." ,".$requestData['length'];
		}
		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData = array();
			$nestedData[] = $row['id'];

		if(ini() == 'toktokmall'){
			if ($row['avatar'] == Null ) {
				$nestedData[] = '<img   class="img-thumbnail"  style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/img/toktokmall/toktokmall-userlist.png">';
			}else{
				$nestedData[] = '<img  class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/uploads/avatars/'.$row['avatar'].'"  onerror="this.onerror=null;this.src=`'.get_s3_imgpath_upload().'assets/img/toktokmall/toktokmall-userlist.png`;"/>';
			}
		}else{
			   $nestedData[] = '<img class="img-thumbnail" style="width: 50px;" src="'.get_s3_imgpath_upload().'assets/uploads/avatars/'.$row['avatar'].'">';
		}
		   
			$nestedData[] = $row['username'];
			if ($row['active'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
				$status = "Active";
			}else if ($row['active'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
				$status = "Inactive";
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
				$status = "Active";
			}

			$nestedData[] = $status;
			$actions = '
			<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			';
			if ($this->loginstate->get_access()['aul']['update'] == 1) {
				$actions .= '
					<a class="dropdown-item action_edit" href="'.base_url('/settings/user_list/edit_user/'.$token.'/'.$row['id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
			    		<div class="dropdown-divider"></div>';
			}

			if ($this->loginstate->get_access()['aul']['disable'] == 1) {
				$actions .= '
			    	<a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['active'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
			    		<div class="dropdown-divider"></div>';
			}
			if ($this->loginstate->get_access()['aul']['delete'] == 1) {
				$actions .= '
					<a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}
			$actions .= '
				</div>
			</div>';

			$nestedData[] = $actions;
			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}
}