$(function () {
    
    $('.super_parent_checkbox').change( (e) => {
        var val = $(e.target).is(':checked');
        if (val == false) {
			$('.parent_section').find('input[type="checkbox"]:not(.parent_checkbox)').prop('checked',false);
		}
		run_sub_parent_checkbox_checker();
	})
	
	$('.sub_parent_checkbox').change( (e) => {
		var val = $(e.target).is(':checked');
		var p = $(e.target).parent().parent().parent();
        if (val == true) {
			$(p).find('input[type="checkbox"]').prop('checked',true);
		}else{
			$(p).find('input[type="checkbox"]').prop('checked',false);
		}
		run_sub_parent_checkbox_checker();
	})
    
    $('#ac_online_ordering_view').change( (e) => {
		var val = $(e.target).is(':checked');
        if (val == true) {
			$('.online_ordering').find('input[type="checkbox"]:not(.parent_checkbox)').prop('checked',true);
		}else{
			$('.online_ordering').find('input[type="checkbox"]:not(.parent_checkbox)').prop('checked',false);
		}
		run_sub_parent_checkbox_checker();
	})

	$('input:checkbox').change( (e) => {
		run_sub_parent_checkbox_checker();
	})

    // start - checkboxes function
	$('#devs_access').change(function(e){
		if($(this).is(':checked') == true){
			$('#devs_access').prop('checked', false);

			$("#devs_purpose_div").find('input[type=checkbox]').prop('checked', true);
		}
    });
    
	$('#overall_access').change(function(e){
		if($(this).is(':checked') == true){
			$('.super_parent_checkbox:not(#overall_access)').prop('checked', false);
			$('input[type=checkbox]:not(input#seller_access)').prop('checked', true);
			$('#seller_branch_access').prop('checked', false);
			$('#food_hub_access').prop('checked', false);
			// $('input[type=checkbox]:not(input#seller_branch_access)').prop('checked', true);
			$('.ac_transactions_mark_as_paid').prop('checked', false);

			$('#shop-branch-account input[type=checkbox]').prop('checked', false);
			$('#shops-account input[type=checkbox]').prop('checked', false);

			$("#shop_mer_reg_view").prop('checked', false);
			$("#shop_mer_reg_approve").prop('checked', false);
			$("#shop_mer_reg_edit").prop('checked', false);
			$("#shop_mer_reg_decline").prop('checked', false);
			$("#shop_mer_reg_delete").prop('checked', false);

			$('input[type=checkbox]#ac_rbbr_view').prop('checked', false);

			$("#dev_settings_view").prop('checked', false);
			$("#dev_settings_content_navigation_view").prop('checked', false);
			$("#dev_settings_content_navigation_create").prop('checked', false);
			$("#dev_settings_content_navigation_update").prop('checked', false);
			$("#dev_settings_content_navigation_disable").prop('checked', false);
			$("#dev_settings_content_navigation_delete").prop('checked', false);

			$("#dev_settings_cron_logs_view").prop('checked', false);
			$("#dev_settings_cron_logs_disable").prop('checked', false);

			$("#dev_settings_clief_info_view").prop('checked', false);
			$("#dev_settings_clief_info_create").prop('checked', false);
			$("#dev_settings_clief_info_update").prop('checked', false);
			$("#dev_settings_clief_info_disable").prop('checked', false);
			$("#dev_settings_clief_info_delete").prop('checked', false);

			$("#dev_settings_mainte_page_view").prop('checked', false);
			$("#dev_settings_mainte_page_update").prop('checked', false);

            $("#dev_settings_api_request_postback_logs_view").prop('checked', false);



			$("#dev_settings_audit_trail_view").prop('checked', false);
			$("#dev_settings_pandabooks_api_logs_view").prop('checked', false);
			run_sub_parent_checkbox_checker();
		}
	});

	$('#seller_access').change(function(e){
		if($(this).is(':checked') == true){
			$('.super_parent_checkbox:not(#seller_access)').prop('checked', false);
			$('.tab-content :not(.has-seller-access) input[type=checkbox]').prop('checked', false);
			$('.tab-content .has-seller-access input[type=checkbox]').prop('checked', true);

			$('.ac_transactions_mark_as_paid').prop('checked', false);
			
			$('#ac_settings_shop_branch_view').prop('checked', true);
			$('#ac_settings_shop_branch_update').prop('checked', true);
			$('#shop_account_view').prop('checked', true);
			$('#shop_account_update').prop('checked', true);

			$('input[type=checkbox]#ac_rbsr_view').prop('checked', false);

			$('#ac_customer_view').prop('checked', true);
			
			$('#ac_billing_view').prop('checked', true);
			$('#billing_portal_fee_view').prop('checked', true);
			
			$('#ac_prepayment_view').prop('checked', true);
			
			$('#ac_settings_announcement_view').prop('checked', true);
			$('#void-record input[type=checkbox]').prop('checked', true);
			$('#void-record input[type=checkbox]').prop('checked', true);
			$('#void-record-list input[type=checkbox]').prop('checked', true);
			$('#ac_pending_orders').prop('checked', false);

			run_sub_parent_checkbox_checker();
		}
	});

	$('#seller_branch_access').change(function(e){
		if($(this).is(':checked') == true){
			$('.super_parent_checkbox:not(#seller_branch_access)').prop('checked', false);
			$('.tab-content :not(.has-seller-branch-access) input[type=checkbox]').prop('checked', false);
			$('.tab-content .has-seller-branch-access input[type=checkbox]').prop('checked', true);
			
			$('#ac_transactions_reassign').prop('checked', false);
			$('.ac_transactions_mark_as_paid').prop('checked', false);
			
			$('#ac_products_view').prop('checked', true);
			
			$('#branch_account_view').prop('checked', true);
			$('#branch_account_update').prop('checked', true);
			
			$('.reports-with-sessions').prop('checked', false);
			$('.reports-revenuesBy').prop('checked', false);
			$('#ac_bpr_view').prop('checked', false);
			$('#ac_osr_view').prop('checked', false);
			$('#ac_prr_view').prop('checked', false);
			$('#ac_sr_view').prop('checked', false);
			// $('#ac_tbr_view').prop('checked', false);

			$('input[type=checkbox]#ac_rbsr_view ').prop('checked', false);

			$('#ac_billing_view').prop('checked', true);
			$('#billing_portal_fee_view').prop('checked', true);
			
			$('#ac_settings_announcement_view').prop('checked', true);
			$('#ac_pending_orders').prop('checked', false);
			
			run_sub_parent_checkbox_checker();
		}
	});
	
    $('#food_hub_access').change(function(e){
		if($(this).is(':checked') == true){
			$('.super_parent_checkbox:not(#food_hub_access)').prop('checked', false);
			$('.tab-content :not(.has-food-hub-access) input[type=checkbox]').prop('checked', false);
			$('.tab-content .has-food-hub-access input[type=checkbox]').prop('checked', true);
			
			$('#ac_transactions_reassign').prop('checked', false);
			$('.ac_transactions_mark_as_paid').prop('checked', false);
			$('.ac_transactions_ready_pickup').prop('checked', false);
			$('.ac_transactions_booking_confirmed').prop('checked', false);
			$('.order-refund_order').prop('checked', false);
			
			$('#ac_pending_orders').prop('checked', false);
			$('#ac_readyforprocessing_orders').prop('checked', false);
			$('#ac_processing_orders').prop('checked', false);
			
			$('input[type=checkbox]#ac_rbbr_view').prop('checked', false);
			$('.reports-refund_order').prop('checked', false);
			$('.reports-with-sessions').prop('checked', false);
			$('.reports-revenuesBy').prop('checked', false);
			$('#ac_bpr_view').prop('checked', false);
			$('#ac_osr_view').prop('checked', false);
			$('#ac_prr_view').prop('checked', false);
			$('#ac_sr_view').prop('checked', false);
			// $('#ac_tbr_view').prop('checked', false);
			
			$('#ac_products_view').prop('checked', true);
			
			$('#ac_billing_view').prop('checked', true);
			$('#billing_portal_fee_view').prop('checked', true);

			$('#ac_settings_announcement_view').prop('checked', true);

			run_sub_parent_checkbox_checker();
        }
    })

	$('.order_list_ac_fxns').change( (e) => {
		var val = ($(e.target).is(':checked') == true) ? true:false;
		$(`.${e.target.name}`).prop('checked', val);
    })
	// end - checkboxes function

	$('button[data-target="#demo"]').click(function(){
		if($('#demo').hasClass('show')){
			$(this).text('Show Access Control');
		}else{
			$(this).text('Hide Access Control');
		}
	});

	$('button[data-target="#demo2"]').click(function(){
		if($('#demo2').hasClass('show')){
			$(this).text('Show Access Control');
		}else{
			$(this).text('Hide Access Control');
		}
	});
})
	
function run_sub_parent_checkbox_checker () {
	$('.sub_parent_checkbox').each( (k, el) => {
		var p = $(el).parent().parent().parent();
		var checked = $(p).find(':not(:first-child) input:not(.hidden):checked').length;
		id = el.className.split(' ')[0];
		$(`input#${id}`).prop('checked', (checked > 0) ? true:false);
		var tab_id = $(p).parent().attr('id');
		$(`#${tab_id}-tab .check-count`).text(checked);
		var unchecked = $(p).find(':not(:first-child) input:checkbox:not(:checked)').length;
		if (unchecked > 0) {
			$(el).prop('checked', false);
		} else {
			$(el).prop('checked', true);
		}
	})
}

function populate_edit_user_form(data) {
	$("input[name='f_id']").val(data.id);
	$("input[name='f_email']").val(data.username);
	$("input[name='f_email']").attr('readonly', true);


	// User avatar
	if (data.avatar != "") {
		$('#avatar-placeholder').addClass('hidden');
		$('#avatar_preview').attr('src', s3bucket_url + 'assets/uploads/avatars/' + data.avatar);
		$('#current_avatar_url').val(data.avatar);
	} else {
		$('#avatar-placeholder').removeClass('hidden');
		$('#change-image').hide();
	}

	if (data.access_nav != "") {
		// online ordering view
		var access_nav = data.access_nav;
		var access_nav_arr = access_nav.split(", ");

		$('#main_nav_ac_orders_view').prop('checked', false);
		$('#main_nav_ac_products_view').prop('checked', false);
		$('#main_nav_ac_shops_view').prop('checked', false);
		$('#main_nav_ac_customers_view').prop('checked', false);
		$('#ac_accounts_view').prop('checked', false);
		$('#ac_vouchers_view').prop('checked', false);
		$('#ac_vouchers_list_view').prop('checked', false);
		$('#ac_wallet_view').prop('checked', false);
		$('#ac_reports_view').prop('checked', false);
		$('#ac_settings_view').prop('checked', false);
		$('#ac_csr_view').prop('checked', false);
		$('#dev_settings_view').prop('checked', false);
		if(access_nav_arr != "") {
			
			var nav_length = access_nav_arr.length;
			for(var i = 0; i < nav_length; i++) {
				if(access_nav_arr[i] == 2){ // reference cp_main_navigation->main_nav_id
					//have orders module
					$('#main_nav_ac_orders_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 3){ // reference cp_main_navigation->main_nav_id
					//have products module
					$('#main_nav_ac_products_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 4){ // reference cp_main_navigation->main_nav_id
					//have shops module
					$('#main_nav_ac_shops_view').prop('checked', true);
				}

				if(access_nav_arr[i] == 5){ // reference cp_main_navigation->main_nav_id
					//have customers module
					$('#main_nav_ac_customers_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 6){ // reference cp_main_navigation->main_nav_id
					//have accounts module
					$('#ac_accounts_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 10){ // reference cp_main_navigation->main_nav_id
					//have accounts module
					$('#ac_vouchers_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 10){ // reference cp_main_navigation->main_nav_id
					//have accounts module
					$('#ac_vouchers_list_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 11){ // reference cp_main_navigation->main_nav_id
					//have accounts module
					$('#ac_wallet_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 7){ // reference cp_main_navigation->main_nav_id
					//have reports module
					$('#ac_reports_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 8){ // reference cp_main_navigation->main_nav_id
					//have settings module
					$('#ac_settings_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 12){ // reference cp_main_navigation->main_nav_id
					//have settings module
					$('#ac_csr_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 9){ // reference cp_main_navigation->main_nav_id
					//have dev settings module
					$('#dev_settings_view').prop('checked', true);
				}

			}
		}
	}

	// Active access control
	if(data.functions != "") {
		var fn = JSON.parse(data.functions);
		if(('overall_access' in fn) && fn.overall_access == 1) {
			$('#overall_access').prop('checked', true);
		}
		if(('seller_access' in fn) && fn.seller_access == 1) {
			$('#seller_access').prop('checked', true);
		}

		if(('seller_branch_access' in fn) && fn.seller_branch_access == 1) {
			$('#seller_branch_access').prop('checked', true);
		}
		if(('food_hub_access' in fn) && fn.food_hub_access == 1) {
			$('#food_hub_access').prop('checked', true);
		}

		//dashboard
		if(('dashboard' in fn) && fn.dashboard.view == 1) {
			$('#ac_dashboard_view').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.sales_count_view == 1) {
			$('#ac_dashboard_sales_count').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.transactions_count_view == 1) {
			$('#ac_dashboard_transactions_count').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.views_count_view == 1) {
			$('#ac_dashboard_views_count').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.overall_sales_count_view == 1) {
			$('#ac_dashboard_overall_sales_count').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.visitors_chart_view == 1) {
			$('#ac_dashboard_visitors_chart').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.views_chart_view == 1) {
			$('#ac_dashboard_views_chart').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.sales_chart_view == 1) {
			$('#ac_dashboard_sales_chart').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.top10productsold_list_view == 1) {
			$('#ac_dashboard_top10productsold_list').prop('checked', true);
		}
		if(('dashboard' in fn) && fn.dashboard.transactions_chart_view == 1) {
			$('#ac_dashboard_transactions_chart').prop('checked', true);
		}

		//transactions
		if(('transactions' in fn) && fn.transactions.view == 1) {
			$('#ac_transactions_view').prop('checked', true);
		}

		if(('transactions' in fn) && fn.transactions.merchant_orderList == 1) {
			$('.ac_merchant_ol_view').prop('checked', true);
		}

		if(('transactions' in fn) && fn.transactions.update == 1) {
			$('#ac_transactions_update').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.reassign == 1) {
			$('#ac_transactions_reassign').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.mark_as_paid == 1) {
			$('.ac_transactions_mark_as_paid').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.process_order == 1) {
			$('.ac_transactions_process_order').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.decline == 1) {
			$('.ac_transactions_decline_order').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.ready_pickup == 1) {
			$('.ac_transactions_ready_pickup').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.booking_confirmed == 1) {
			$('.ac_transactions_booking_confirmed').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.mark_fulfilled == 1) {
			$('.ac_transactions_mark_fulfilled').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.returntosender == 1) {
			$('.ac_returntosender').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.redeliver == 1) {
			$('.ac_redeliver').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.shipped == 1) {
			$('.ac_transactions_shipped').prop('checked', true);
		}
		if(('transactions' in fn) && fn.transactions.confirmed == 1) {
			$('.ac_confirmed').prop('checked', true);
		}

	

		//new orders nav
		if(('cancelled_orders' in fn) && fn.cancelled_orders.view == 1) {
			$('#ac_cancelled_orders').prop('checked', true);
		}
		if(('pending_orders' in fn) && fn.pending_orders.view == 1) {
			$('#ac_pending_orders').prop('checked', true);
		}
		if(('paid_orders' in fn) && fn.paid_orders.view == 1) {
			$('#ac_paid_orders').prop('checked', true);
		}
		if(('readyforprocessing_orders' in fn) && fn.readyforprocessing_orders.view == 1) {
			$('#ac_readyforprocessing_orders').prop('checked', true);
		}
		if(('processing_orders' in fn) && fn.processing_orders.view == 1) {
			$('#ac_processing_orders').prop('checked', true);
		}
		if(('readyforpickup_orders' in fn) && fn.readyforpickup_orders.view == 1) {
			$('#ac_readyforpickup_orders').prop('checked', true);
		}
		if(('bookingconfirmed_orders' in fn) && fn.bookingconfirmed_orders.view == 1) {
			$('#ac_bookingconfirmed_orders').prop('checked', true);
		}
		if(('fulfilled_orders' in fn) && fn.fulfilled_orders.view == 1) {
			$('#ac_fulfilled_orders').prop('checked', true);
		}
		if(('shipped_orders' in fn) && fn.shipped_orders.view == 1) {
			$('#ac_shipped_orders').prop('checked', true);
		}
		if(('returntosender_orders' in fn) && fn.returntosender_orders.view == 1) {
			$('#ac_returntosender_orders').prop('checked', true);
		}
		if(('voided_orders' in fn) && fn.voided_orders.view == 1) {
			$('#ac_voided_orders').prop('checked', true);
		}
		if(('manualorder_list' in fn) && fn.manualorder_list.view == 1) {
			$('#ac_manualorder_list').prop('checked', true);
		}
		if(('manualorder_list' in fn) && fn.manualorder_list.create == 1) {
			$('#ac_manualorder_list_create').prop('checked', true);
		}
		if(('refund_order' in fn) && fn.refund_order.view == 1) {
			$('#ac_refund_order_view').prop('checked', true);
		}
		if(('refund_order' in fn) && fn.refund_order.create == 1) {
			$('#ac_refund_order_create').prop('checked', true);
		}
		if(('refund_order_approval' in fn) && fn.refund_order_approval.view == 1) {
			$('#ac_refund_order_approval_view').prop('checked', true);
		}
		if(('refund_order_approval' in fn) && fn.refund_order_approval.update == 1) {
			$('#ac_refund_order_approval_update').prop('checked', true);
		}
		if(('refund_order_approval' in fn) && fn.refund_order_approval.approve == 1) {
			$('#ac_refund_order_approval_approve').prop('checked', true);
		}
		if(('refund_order_approval' in fn) && fn.refund_order_approval.reject == 1) {
			$('#ac_refund_order_approval_reject').prop('checked', true);
		}
		if(('refund_order_trans' in fn) && fn.refund_order_trans.view == 1) {
			$('#ac_refund_order_trans_view').prop('checked', true);
		}
		if(('forpickup_orders' in fn) && fn.forpickup_orders.view == 1) {
			$('#ac_forpickup_orders').prop('checked', true);
		}
		if(('confirmed_orders' in fn) && fn.confirmed_orders.view == 1) {
			$('#ac_confirmed_order_list').prop('checked', true);
		}

		//products
		if(('products' in fn) && fn.products.view == 1) {
			$('#ac_products_view').prop('checked', true);
		}
		if(('products' in fn) && fn.products.create == 1) {
			$('#ac_products_create').prop('checked', true);
		}
		if(('products' in fn) && fn.products.update == 1) {
			$('#ac_products_update').prop('checked', true);
		}
		if(('products' in fn) && fn.products.delete == 1) {
			$('#ac_products_delete').prop('checked', true);
		}

		//menu plan
		if(('menu_plan' in fn) && fn.menu_plan.view == 1) {
			$('#ac_menu_plan_view').prop('checked', true);
		}
		if(('menu_plan' in fn) && fn.menu_plan.create == 1) {
			$('#ac_menu_plan_create').prop('checked', true);
		}
		if(('menu_plan' in fn) && fn.menu_plan.update == 1) {
			$('#ac_menu_plan_update').prop('checked', true);
		}
		if(('menu_plan' in fn) && fn.menu_plan.delete == 1) {
			$('#ac_menu_plan_delete').prop('checked', true);
		}
		if(('menu_plan' in fn) && fn.menu_plan.disable == 1) {
			$('#ac_menu_plan_disable').prop('checked', true);
		}

		//products waiting for approval
		if(('products_wfa' in fn) && fn.products_wfa.view == 1) {
			$('#ac_products_wfa_view').prop('checked', true);
		}
		if(('products_wfa' in fn) && fn.products_wfa.edit == 1) {
			$('#ac_products_wfa_edit').prop('checked', true);
		}
		if(('products_wfa' in fn) && fn.products_wfa.approved == 1) {
			$('#ac_products_wfa_approve').prop('checked', true);
		}
		if(('products_wfa' in fn) && fn.products_wfa.declined == 1) {
			$('#ac_products_wfa_decline').prop('checked', true);
		}

		//products approve
		if(('products_apr' in fn) && fn.products_apr.view == 1) {
			$('#ac_products_apr_view').prop('checked', true);
		}
		if(('products_apr' in fn) && fn.products_apr.approved == 1) {
			$('#ac_products_apr_approve').prop('checked', true);
		}
		if(('products_apr' in fn) && fn.products_apr.declined == 1) {
			$('#ac_products_apr_decline').prop('checked', true);
		}

		//products decline
		if(('products_dec' in fn) && fn.products_dec.view == 1) {
			$('#ac_products_dec_view').prop('checked', true);
		}
		if(('products_dec' in fn) && fn.products_dec.approved == 1) {
			$('#ac_products_dec_approve').prop('checked', true);
		}

		//products verify
		if(('products_verified' in fn) && fn.products_verified.view == 1) {
			$('#ac_products_verified_view').prop('checked', true);
		}


		//products changes approval
		if(('products_changes_approval' in fn) && fn.products_changes_approval.view == 1) {
			$('#ac_products_changes_view').prop('checked', true);
		}
		if(('products_changes_approval' in fn) && fn.products_changes_approval.edit == 1) {
			$('#ac_products_changes_edit').prop('checked', true);
		}
		if(('products_changes_approval' in fn) && fn.products_changes_approval.approved == 1) {
			$('#ac_products_changes_approve').prop('checked', true);
		}
		if(('products_changes_approval' in fn) && fn.products_changes_approval.declined == 1) {
			$('#ac_products_changes_decline').prop('checked', true);
		}

		//shops
		if(('shops' in fn) && fn.shops.view == 1) {
			$('#ac_shops_view').prop('checked', true);
		}
		if(('shops' in fn) && fn.shops.create == 1) {
			$('#ac_shops_create').prop('checked', true);
		}
		if(('shops' in fn) && fn.shops.update == 1) {
			$('#ac_shops_update').prop('checked', true);
		}
		if(('shops' in fn) && fn.shops.delete == 1) {
			$('#ac_shops_delete').prop('checked', true);
		}
		if(('shops' in fn) && fn.shops.disable == 1) {
			$('#ac_shops_disable').prop('checked', true);
		}

		//shop branch account 
		if(('branch_account' in fn) && fn.branch_account.view == 1) {
			$('#branch_account_view').prop('checked', true);
		}
		if(('branch_account' in fn) && fn.branch_account.create == 1) {
			$('#branch_account_create').prop('checked', true);
		}
		if(('branch_account' in fn) && fn.branch_account.update == 1) {
			$('#branch_account_update').prop('checked', true);
		}
		if(('branch_account' in fn) && fn.branch_account.disable == 1) {
			$('#branch_account_disable').prop('checked', true);
		}
		if(('branch_account' in fn) && fn.branch_account.delete == 1) {
			$('#branch_account_delete').prop('checked', true);
		}

		//shop account 
		if(('shop_account' in fn) && fn.shop_account.view == 1) {
			$('#shop_account_view').prop('checked', true);
		}
		if(('shop_account' in fn) && fn.shop_account.create == 1) {
			$('#shop_account_create').prop('checked', true);
		}
		if(('shop_account' in fn) && fn.shop_account.update == 1) {
			$('#shop_account_update').prop('checked', true);
		}
		if(('shop_account' in fn) && fn.shop_account.disable == 1) {
			$('#shop_account_disable').prop('checked', true);
		}
		if(('shop_account' in fn) && fn.shop_account.delete == 1) {
			$('#shop_account_delete').prop('checked', true);
		}

		//merchant registration
		if(('merchant_registration' in fn) && fn.merchant_registration.view == 1) {
			$('#shop_mer_reg_view').prop('checked', true);
		}

		if(('merchant_registration' in fn) && fn.merchant_registration.approve == 1) {
			$('#shop_mer_reg_approve').prop('checked', true);
		}

		if(('merchant_registration' in fn) && fn.merchant_registration.edit == 1) {
			$('#shop_mer_reg_edit').prop('checked', true);
		}

		if(('merchant_registration' in fn) && fn.merchant_registration.decline == 1) {
			$('#shop_mer_reg_decline').prop('checked', true);
		}

		if(('merchant_registration' in fn) && fn.merchant_registration.delete == 1) {
			$('#shop_mer_reg_delete').prop('checked', true);
		}


		//shop changes approval
		if(('shop_changes_approval' in fn) && fn.shop_changes_approval.view == 1) {
			$('#shop_changes_view').prop('checked', true);
		}
		if(('shop_changes_approval' in fn) && fn.shop_changes_approval.edit == 1) {
			$('#shop_changes_edit').prop('checked', true);
		}
		if(('shop_changes_approval' in fn) && fn.shop_changes_approval.approved == 1) {
			$('#shop_changes_approve').prop('checked', true);
		}
		if(('shop_changes_approval' in fn) && fn.shop_changes_approval.declined == 1) {
			$('#shop_changes_decline').prop('checked', true);
		}

		//customer
		if(('customer' in fn) && fn.customer.view == 1) {
			$('#ac_customer_view').prop('checked', true);
		}
		if(('customer' in fn) && fn.customer.create == 1) {
			$('#ac_customer_create').prop('checked', true);
		}
		if(('customer' in fn) && fn.customer.update == 1) {
			$('#ac_customer_update').prop('checked', true);
		}
		if(('customer' in fn) && fn.customer.delete == 1) {
			$('#ac_customer_delete').prop('checked', true);
		}
		if(('customer' in fn) && fn.customer.disable == 1) {
			$('#ac_customer_disable').prop('checked', true);
		}

		// accounts
		if(('accounts' in fn) && fn.accounts == 1) {
			$('#ac_accounts_view').prop('checked', true);
		}

		// vouchers
		if(('vouchers' in fn) && fn.vouchers == 1) {
			$('#ac_vouchers_view').prop('checked', true);
		}

		//vouchers claimed
		if(('vc' in fn) && fn.vc.view == 1) {
			$('#ac_vouchers_claimed_view').prop('checked', true);
		}

		//vouchers list
		if(('voucher_list' in fn) && fn.voucher_list.view == 1) {
			$('#ac_vouchers_list_view').prop('checked', true);
		}

		//vouchers create
		if(('voucher_list' in fn) && fn.voucher_list.create == 1) {
			$('#ac_vouchers_list_create').prop('checked', true);
		}

		//vouchers update
		if(('voucher_list' in fn) && fn.voucher_list.update == 1) {
			$('#ac_vouchers_list_update').prop('checked', true);
		}

		//vouchers delete
		if(('voucher_list' in fn) && fn.voucher_list.delete == 1) {
			$('#ac_vouchers_list_delete').prop('checked', true);
		}

		//vouchers disabled
		if(('voucher_list' in fn) && fn.voucher_list.disable == 1) {
			$('#ac_vouchers_list_disable').prop('checked', true);
		}


		// wallet
		if(('wallet' in fn) && fn.wallet == 1) {
			$('#ac_wallet_view').prop('checked', true);
		}

		// prepayment view
		if(('prepayment' in fn) && fn.prepayment.view == 1) {
			$('#ac_prepayment_view').prop('checked', true);
		}
		// prepayment create
		if(('prepayment' in fn) && fn.prepayment.create == 1) {
			$('#ac_prepayment_create').prop('checked', true);
		}

		// manual_order view
		if(('manual_order' in fn) && fn.manual_order.view == 1) {
			$('#ac_manual_order_view').prop('checked', true);
		}
		// manual_order create
		if(('manual_order' in fn) && fn.manual_order.create == 1) {
			$('#ac_manual_order_create').prop('checked', true);
		}

		// manual_order view
		if(('wallet_page' in fn) && fn.wallet_page.view == 1) {
			$('#ac_wallet_page_view').prop('checked', true);
		}
		// manual_order create
		if(('wallet_page' in fn) && fn.wallet_page.encash == 1) {
			$('#ac_wallet_page_encash').prop('checked', true);
		}
		

		//billing
		if(('billing' in fn) && fn.billing.view == 1) {
			$('#ac_billing_view').prop('checked', true);
		}
		if(('billing' in fn) && fn.billing.create == 1) {
			$('#ac_billing_create').prop('checked', true);
		}
		if(('billing' in fn) && fn.billing.update == 1) {
			$('#ac_billing_update').prop('checked', true);
		}
		if(('billing' in fn) && fn.billing.delete == 1) {
			$('#ac_billing_delete').prop('checked', true);
		}
		if(('billing' in fn) && fn.billing.disable == 1) {
			$('#ac_billing_disable').prop('checked', true);
		}
		if(('billing' in fn) && fn.billing.admin_view == 1) {
			$('#ac_billing_adminview').prop('checked', true);
		}

		//billing portal payment fee
		if(('billing_portal_fee' in fn) && fn.billing_portal_fee.view == 1) {
			$('#billing_portal_fee_view').prop('checked', true);
		}
		if(('billing_portal_fee' in fn) && fn.billing_portal_fee.create == 1) {
			$('#billing_portal_fee_create').prop('checked', true);
		}
		if(('billing_portal_fee' in fn) && fn.billing_portal_fee.update == 1) {
			$('#billing_portal_fee_update').prop('checked', true);
		}
		if(('billing_portal_fee' in fn) && fn.billing_portal_fee.disable == 1) {
			$('#billing_portal_fee_disable').prop('checked', true);
		}
		if(('billing_portal_fee' in fn) && fn.billing_portal_fee.delete == 1) {
			$('#billing_portal_fee_delete').prop('checked', true);
		}

		//codes
		if(('codes' in fn) && fn.codes.view == 1) {
			$('#ac_codes_view').prop('checked', true);
		}
		if(('codes' in fn) && fn.codes.create == 1) {
			$('#ac_codes_create').prop('checked', true);
		}
		if(('codes' in fn) && fn.codes.update == 1) {
			$('#ac_codes_update').prop('checked', true);
		}
		if(('codes' in fn) && fn.codes.delete == 1) {
			$('#ac_codes_delete').prop('checked', true);
		}
		

		//reports view
		if(('reports' in fn) && fn.reports == 1) {
			$('#ac_reports_view').prop('checked', true);
		}
		if(('ps' in fn) && fn.ps.view == 1) {
			$('#ac_ps_view').prop('checked', true);
		}
		if(('por' in fn) && fn.por.view == 1) {
			$('#ac_por_view').prop('checked', true);
		}
		if(('sr' in fn) && fn.sr.view == 1) {
			$('#ac_sr_view').prop('checked', true);
		}
		if(('tbr' in fn) && fn.tbr.view == 1) {
			$('#ac_tbr_view').prop('checked', true);
		}
		if(('olps' in fn) && fn.olps.view == 1) {
			$('#ac_olps_view').prop('checked', true);
		}
		if(('ssr' in fn) && fn.ssr.view == 1) {
			$('#ac_ssr_view').prop('checked', true);
		}
		if(('pr' in fn) && fn.pr.view == 1) {
			$('#ac_pr_view').prop('checked', true);
		}
		if(('psr' in fn) && fn.psr.view == 1) {
			$('#ac_psr_view').prop('checked', true);
		}
		//new reports
		if(('aov' in fn) && fn.aov.view == 1) {
			$('#ac_aov_view').prop('checked', true);
		}
		if(('to' in fn) && fn.to.view == 1) {
			$('#ac_to_view').prop('checked', true);
		}
		if(('os' in fn) && fn.os.view == 1) {
			$('#ac_os_view').prop('checked', true);
		}
		if(('tps' in fn) && fn.tps.view == 1) {
			$('#ac_tps_view').prop('checked', true);
		}
		if(('tsr' in fn) && fn.tsr.view == 1) {
			$('#ac_tsr_view').prop('checked', true);
		}
		if(('wtr' in fn) && fn.wtr.view == 1) {
			$('#ac_wtr_view').prop('checked', true);
		}
		if(('msr' in fn) && fn.msr.view == 1) {
			$('#ac_msr_view').prop('checked', true);
		}
		if(('rbsr' in fn) && fn.rbsr.view == 1) {
			$('#ac_rbsr_view').prop('checked', true);
		}
		if(('rbbr' in fn) && fn.rbbr.view == 1) {
			$('#ac_rbbr_view').prop('checked', true);
		}
		if(('oscrr' in fn) && fn.oscrr.view == 1) {
			$('#ac_oscrr_view').prop('checked', true);
		}
		if(('tacr' in fn) && fn.tacr.view == 1) {
			$('#ac_tacr_view').prop('checked', true);
		}
		if(('po' in fn) && fn.po.view == 1) {
			$('#ac_po_view').prop('checked', true);
		}
		if(('inv' in fn) && fn.inv.view == 1) {
			$('#ac_inv_view').prop('checked', true);
		}
		if(('invend' in fn) && fn.invend.view == 1) {
			$('#ac_invend_view').prop('checked', true);
		}
		if(('invlist' in fn) && fn.invlist.view == 1) {
			$('#ac_invlist_view').prop('checked', true);
		}
		if(('osr' in fn) && fn.osr.view == 1) {
			$('#ac_osr_view').prop('checked', true);
		}
		if(('rbl' in fn) && fn.rbl.view == 1) {
			$('#ac_rbl_view').prop('checked', true);
		}
		if(('oblr' in fn) && fn.oblr.view == 1) {
			$('#ac_oblr_view').prop('checked', true);
		}
		if(('bpr' in fn) && fn.bpr.view == 1) {
			$('#ac_bpr_view').prop('checked', true);
		}
		if(('prr' in fn) && fn.prr.view == 1) {
			$('#ac_prr_view').prop('checked', true);
		}
		if(('or' in fn) && fn.or.view == 1) {
			$('#ac_or_view').prop('checked', true);
		}
		if(('rosum' in fn) && fn.rosum.view == 1) {
			$('#ac_rosum_view').prop('checked', true);
		}
		if(('rostat' in fn) && fn.rostat.view == 1) {
			$('#ac_rostat_view').prop('checked', true);
		}


		// settings
		if(('settings' in fn) && fn.settings == 1) {
			$('#ac_settings_view').prop('checked', true);
		}

		if(('csr' in fn) && fn.csr == 1) {
			$('#ac_csr_view').prop('checked', true);
		}

		// change password
		if(('change_password' in fn) && fn.change_password.update == 1) {
			$('#ac_settings_change-password_update').prop('checked', true);
		}

		//void_record
		if(('void_record' in fn) && fn.void_record.process == 1) {
			$('#ac_settings_void_record_process').prop('checked', true);
		}

		//void_record_list
		if(('void_record_list' in fn) && fn.void_record_list.view == 1) {
			$('#ac_settings_void_record_list_view').prop('checked', true);
		}

		//users
		if(('users' in fn) && fn.users.view == 1) {
			$('#ac_settings_users_view').prop('checked', true);
		}
		if(('users' in fn) && fn.users.create == 1) {
			$('#ac_settings_users_create').prop('checked', true);
		}
		if(('users' in fn) && fn.users.update == 1) {
			$('#ac_settings_users_update').prop('checked', true);
		}
		if(('users' in fn) && fn.users.delete == 1) {
			$('#ac_settings_users_delete').prop('checked', true);
		}
		if(('users' in fn) && fn.users.disable == 1) {
			$('#ac_settings_users_disable').prop('checked', true);
		}

		//members
		if(('members' in fn) && fn.members.view == 1) {
			$('#ac_settings_members_view').prop('checked', true);
		}
		if(('members' in fn) && fn.members.create == 1) {
			$('#ac_settings_members_create').prop('checked', true);
		}
		if(('members' in fn) && fn.members.update == 1) {
			$('#ac_settings_members_update').prop('checked', true);
		}
		if(('members' in fn) && fn.members.delete == 1) {
			$('#ac_settings_members_delete').prop('checked', true);
		}
		if(('members' in fn) && fn.members.disable == 1) {
			$('#ac_settings_members_disable').prop('checked', true);
		}

		//merchant user list
		if(('muserlist' in fn) && fn.muserlist.view == 1) {
			$('#ac_settings_muserlist_view').prop('checked', true);
		}
		if(('muserlist' in fn) && fn.muserlist.create == 1) {
			$('#ac_settings_muserlist_create').prop('checked', true);
		}
		if(('muserlist' in fn) && fn.muserlist.update == 1) {
			$('#ac_settings_muserlist_update').prop('checked', true);
		}
		if(('muserlist' in fn) && fn.muserlist.delete == 1) {
			$('#ac_settings_muserlist_delete').prop('checked', true);
		}
		if(('muserlist' in fn) && fn.muserlist.disable == 1) {
			$('#ac_settings_muserlist_disable').prop('checked', true);
		}

		if(('announcement' in fn) && fn.announcement.view == 1) {
			$('#ac_settings_announcement_view').prop('checked', true);
		}
		if(('announcement' in fn) && fn.announcement.update == 1) {
			$('#ac_settings_announcement_update').prop('checked', true);
		}

		//delivery_areas
		if(('delivery_areas' in fn) && fn.delivery_areas.view == 1) {
			$('#ac_settings_delivery_areas_view').prop('checked', true);
		}
		if(('delivery_areas' in fn) && fn.delivery_areas.create == 1) {
			$('#ac_settings_delivery_areas_create').prop('checked', true);
		}
		if(('delivery_areas' in fn) && fn.delivery_areas.update == 1) {
			$('#ac_settings_delivery_areas_update').prop('checked', true);
		}
		if(('delivery_areas' in fn) && fn.delivery_areas.delete == 1) {
			$('#ac_settings_delivery_areas_delete').prop('checked', true);
		}
		if(('delivery_areas' in fn) && fn.delivery_areas.disable == 1) {
			$('#ac_settings_delivery_areas_disable').prop('checked', true);
		}

		//currency
		if(('currency' in fn) && fn.currency.view == 1) {
			$('#ac_settings_currency_view').prop('checked', true);
		}
		if(('currency' in fn) && fn.currency.create == 1) {
			$('#ac_settings_currency_create').prop('checked', true);
		}
		if(('currency' in fn) && fn.currency.update == 1) {
			$('#ac_settings_currency_update').prop('checked', true);
		}
		if(('currency' in fn) && fn.currency.delete == 1) {
			$('#ac_settings_currency_delete').prop('checked', true);
		}
		if(('currency' in fn) && fn.currency.disable == 1) {
			$('#ac_settings_currency_disable').prop('checked', true);
		}

		//payment_type
		if(('payment_type' in fn) && fn.payment_type.view == 1) {
			$('#ac_settings_payment_type_view').prop('checked', true);
		}
		if(('payment_type' in fn) && fn.payment_type.create == 1) {
			$('#ac_settings_payment_type_create').prop('checked', true);
		}
		if(('payment_type' in fn) && fn.payment_type.update == 1) {
			$('#ac_settings_payment_type_update').prop('checked', true);
		}
		if(('payment_type' in fn) && fn.payment_type.delete == 1) {
			$('#ac_settings_payment_type_delete').prop('checked', true);
		}
		if(('payment_type' in fn) && fn.payment_type.disable == 1) {
			$('#ac_settings_payment_type_disable').prop('checked', true);
		}

		//Admin user list
		if(('adminuserlist' in fn) && fn.adminuserlist.view == 1) {
			$('#settings_aul_view').prop('checked', true);
		}
		if(('adminuserlist' in fn) && fn.adminuserlist.create == 1) {
			$('#settings_aul_create').prop('checked', true);
		}
		if(('adminuserlist' in fn) && fn.adminuserlist.update == 1) {
			$('#settings_aul_update').prop('checked', true);
		}
		if(('adminuserlist' in fn) && fn.adminuserlist.delete == 1) {
			$('#settings_aul_disable').prop('checked', true);
		}
		if(('adminuserlist' in fn) && fn.adminuserlist.disable == 1) {
			$('#settings_aul_delete').prop('checked', true);
		}

		//ref_comrate
		if(('ref_comrate' in fn) && fn.ref_comrate.view == 1) {
			$('#ref_comrate_view').prop('checked', true);
		}
		if(('ref_comrate' in fn) && fn.ref_comrate.create == 1) {
			$('#ref_comrate_create').prop('checked', true);
		}
		if(('ref_comrate' in fn) && fn.ref_comrate.update == 1) {
			$('#ref_comrate_update').prop('checked', true);
		}
		if(('ref_comrate' in fn) && fn.ref_comrate.disable == 1) {
			$('#ref_comrate_disable').prop('checked', true);
		}
		if(('ref_comrate' in fn) && fn.ref_comrate.delete == 1) {
			$('#ref_comrate_delete').prop('checked', true);
		}

		//settings roles
		if(('settings_roles' in fn) && fn.settings_roles.view == 1) {
			$('#settings_roles_view').prop('checked', true);
		}
		if(('settings_roles' in fn) && fn.settings_roles.create == 1) {
			$('#settings_roles_create').prop('checked', true);
		}
		if(('settings_roles' in fn) && fn.settings_roles.update == 1) {
			$('#settings_roles_update').prop('checked', true);
		}
		if(('settings_roles' in fn) && fn.settings_roles.disable == 1) {
			$('#settings_roles_disable').prop('checked', true);
		}
		if(('settings_roles' in fn) && fn.settings_roles.delete == 1) {
			$('#settings_roles_delete').prop('checked', true);
		}

		//settings_region
		if(('settings_region' in fn) && fn.settings_region.view == 1) {
			$('#settings_region_view').prop('checked', true);
		}
		if(('settings_region' in fn) && fn.settings_region.create == 1) {
			$('#settings_region_create').prop('checked', true);
		}
		if(('settings_region' in fn) && fn.settings_region.update == 1) {
			$('#settings_region_update').prop('checked', true);
		}
		if(('settings_region' in fn) && fn.settings_region.disable == 1) {
			$('#settings_region_disable').prop('checked', true);
		}
		if(('settings_region' in fn) && fn.settings_region.delete == 1) {
			$('#settings_region_delete').prop('checked', true);
		}

		//settings_city
		if(('settings_city' in fn) && fn.settings_city.view == 1) {
			$('#settings_city_view').prop('checked', true);
		}
		if(('settings_city' in fn) && fn.settings_city.create == 1) {
			$('#settings_city_create').prop('checked', true);
		}
		if(('settings_city' in fn) && fn.settings_city.update == 1) {
			$('#settings_city_update').prop('checked', true);
		}
		if(('settings_city' in fn) && fn.settings_city.disable == 1) {
			$('#settings_city_disable').prop('checked', true);
		}
		if(('settings_city' in fn) && fn.settings_city.delete == 1) {
			$('#settings_city_delete').prop('checked', true);
		}

		//settings_province
		if(('settings_province' in fn) && fn.settings_province.view == 1) {
			$('#settings_province_view').prop('checked', true);
		}
		if(('settings_province' in fn) && fn.settings_province.create == 1) {
			$('#settings_province_create').prop('checked', true);
		}
		if(('settings_province' in fn) && fn.settings_province.update == 1) {
			$('#settings_province_update').prop('checked', true);
		}
		if(('settings_province' in fn) && fn.settings_province.disable == 1) {
			$('#settings_province_disable').prop('checked', true);
		}
		if(('settings_province' in fn) && fn.settings_province.delete == 1) {
			$('#settings_province_delete').prop('checked', true);
		}

		//shipping and delivery
		if(('shipping_and_delivery' in fn) && fn.shipping_and_delivery == 1) {
			$('#ac_shipping_and_delivery_view').prop('checked', true);
		}	
			//general shipping (this is the child, don't add this to the cp_content_navigation. what you need is to add this in sys_users->functions)
			if(('general_shipping' in fn) && fn.general_shipping.view == 1) {
				$('#ac_general_shipping_view').prop('checked', true);
			}
			if(('general_shipping' in fn) && fn.general_shipping.create == 1) {
				$('#ac_general_shipping_create').prop('checked', true);
			}
			if(('general_shipping' in fn) && fn.general_shipping.update == 1) {
				$('#ac_general_shipping_update').prop('checked', true);
			}
			if(('general_shipping' in fn) && fn.general_shipping.delete == 1) {
				$('#ac_general_shipping_delete').prop('checked', true);
			}
			if(('general_shipping' in fn) && fn.general_shipping.disable == 1) {
				$('#ac_general_shipping_disable').prop('checked', true);
			}

			//custom shipping (this is the child, don't add this to the cp_content_navigation. what you need is to add this in sys_users->functions)
			if(('custom_shipping' in fn) && fn.custom_shipping.view == 1) {
				$('#ac_custom_shipping_view').prop('checked', true);
			}
			if(('custom_shipping' in fn) && fn.custom_shipping.create == 1) {
				$('#ac_custom_shipping_create').prop('checked', true);
			}
			if(('custom_shipping' in fn) && fn.custom_shipping.update == 1) {
				$('#ac_custom_shipping_update').prop('checked', true);
			}
			if(('custom_shipping' in fn) && fn.custom_shipping.delete == 1) {
				$('#ac_custom_shipping_delete').prop('checked', true);
			}
			if(('custom_shipping' in fn) && fn.custom_shipping.disable == 1) {
				$('#ac_custom_shipping_disable').prop('checked', true);
			}

		//category
		if(('category' in fn) && fn.category.view == 1) {
			$('#ac_settings_category_view').prop('checked', true);
		}
		if(('category' in fn) && fn.category.create == 1) {
			$('#ac_settings_category_create').prop('checked', true);
		}
		if(('category' in fn) && fn.category.update == 1) {
			$('#ac_settings_category_update').prop('checked', true);
		}
		if(('category' in fn) && fn.category.delete == 1) {
			$('#ac_settings_category_delete').prop('checked', true);
		}
		if(('category' in fn) && fn.category.disable == 1) {
			$('#ac_settings_category_disable').prop('checked', true);
		}

		//shipping_partners
		if(('shipping_partners' in fn) && fn.shipping_partners.view == 1) {
			$('#ac_settings_shipping_partners_view').prop('checked', true);
		}
		if(('shipping_partners' in fn) && fn.shipping_partners.create == 1) {
			$('#ac_settings_shipping_partners_create').prop('checked', true);
		}
		if(('shipping_partners' in fn) && fn.shipping_partners.update == 1) {
			$('#ac_settings_shipping_partners_update').prop('checked', true);
		}
		if(('shipping_partners' in fn) && fn.shipping_partners.delete == 1) {
			$('#ac_settings_shipping_partners_delete').prop('checked', true);
		}
		if(('shipping_partners' in fn) && fn.shipping_partners.disable == 1) {
			$('#ac_settings_shipping_partners_disable').prop('checked', true);
		}

		//shop banners "shop_banners" is the same you can get in the sys_users->functions column, 
		// use json formatter https://jsonformatter.curiousconcept.com/
		if(('shop_banners' in fn) && fn.shop_banners.view == 1) {
			$('#ac_settings_shop_banners_view').prop('checked', true);
		}
		if(('shop_banners' in fn) && fn.shop_banners.create == 1) {
			$('#ac_settings_shop_banners_create').prop('checked', true);
		}
		if(('shop_banners' in fn) && fn.shop_banners.update == 1) {
			$('#ac_settings_shop_banners_update').prop('checked', true);
		}
		if(('shop_banners' in fn) && fn.shop_banners.delete == 1) {
			$('#ac_settings_shop_banners_delete').prop('checked', true);
		}
		if(('shop_banners' in fn) && fn.shop_banners.disable == 1) {
			$('#ac_settings_shop_banners_disable').prop('checked', true);
		}


		if(('shop_branch' in fn) && fn.shop_branch.view == 1) {
			$('#ac_settings_shop_branch_view').prop('checked', true);
		}
		if(('shop_branch' in fn) && fn.shop_branch.create == 1) {
			$('#ac_settings_shop_branch_create').prop('checked', true);
		}
		if(('shop_branch' in fn) && fn.shop_branch.update == 1) {
			$('#ac_settings_shop_branch_update').prop('checked', true);
		}
		if(('shop_branch' in fn) && fn.shop_branch.delete == 1) {
			$('#ac_settings_shop_branch_delete').prop('checked', true);
		}
		if(('shop_branch' in fn) && fn.shop_branch.disable == 1) {
			$('#ac_settings_shop_branch_disable').prop('checked', true);
		}

		//bank list
		if(('bank_list' in fn) && fn.bank_list.view == 1) {
			$('#ac_settings_bank_view').prop('checked', true);
		}
		if(('bank_list' in fn) && fn.bank_list.create == 1) {
			$('#ac_settings_bank_create').prop('checked', true);
		}
		if(('bank_list' in fn) && fn.bank_list.update == 1) {
			$('#ac_settings_bank_update').prop('checked', true);
		}
		if(('bank_list' in fn) && fn.bank_list.delete == 1) {
			$('#ac_settings_bank_delete').prop('checked', true);
		}


		//CSR NAV

		//ticket history
		if(('ticket_history' in fn) && fn.ticket_history.view == 1) {
			$('#ac_csr_ticket_history_view').prop('checked', true);
		}
		if(('ticket_history' in fn) && fn.ticket_history.create == 1) {
			$('#ac_csr_ticket_history_create').prop('checked', true);
		}
		if(('ticket_history' in fn) && fn.ticket_history.update == 1) {
			$('#ac_csr_ticket_history_update').prop('checked', true);
		}
		if(('ticket_history' in fn) && fn.ticket_history.delete == 1) {
			$('#ac_csr_ticket_history_disable').prop('checked', true);
		}
		if(('ticket_history' in fn) && fn.ticket_history.disable == 1) {
			$('#ac_csr_ticket_history_delete').prop('checked', true);
		}

		if(('csr_ticket' in fn) && fn.csr_ticket.view == 1) {
			$('#ac_csr_ticket_view').prop('checked', true);
		}
		if(('csr_ticket' in fn) && fn.csr_ticket.create == 1) {
			$('#ac_csr_ticket_create').prop('checked', true);
		}
		if(('csr_ticket' in fn) && fn.csr_ticket.update == 1) {
			$('#ac_csr_ticket_update').prop('checked', true);
		}
		if(('csr_ticket' in fn) && fn.csr_ticket.delete == 1) {
			$('#ac_csr_ticket_disable').prop('checked', true);
		}
		if(('csr_ticket' in fn) && fn.csr_ticket.disable == 1) {
			$('#ac_csr_ticket_delete').prop('checked', true);
		}

		if(('csr_ticket_log' in fn) && fn.csr_ticket_log.view == 1) {
			$('#ac_csr_ticket_log_view').prop('checked', true);
		}
		if(('csr_ticket_log' in fn) && fn.csr_ticket_log.create == 1) {
			$('#ac_csr_ticket_log_create').prop('checked', true);
		}
		if(('csr_ticket_log' in fn) && fn.csr_ticket_log.update == 1) {
			$('#ac_csr_ticket_log_update').prop('checked', true);
		}
		if(('csr_ticket_log' in fn) && fn.csr_ticket_log.delete == 1) {
			$('#ac_csr_ticket_log_disable').prop('checked', true);
		}
		if(('csr_ticket_log' in fn) && fn.csr_ticket_log.disable == 1) {
			$('#ac_csr_ticket_log_delete').prop('checked', true);
		}

		//Promotion
		// Product Promotion

		if(('product_promotion' in fn) && fn.product_promotion.view == 1) {
			$('#pr_product_promotion_view').prop('checked', true);
		}
		if(('product_promotion' in fn) && fn.product_promotion.create == 1) {
			$('#pr_product_promotion_create').prop('checked', true);
		}
		if(('product_promotion' in fn) && fn.product_promotion.update == 1) {
			$('#pr_product_promotion_update').prop('checked', true);
		}
		if(('product_promotion' in fn) && fn.product_promotion.delete == 1) {
			$('#pr_product_promotion_delete').prop('checked', true);
		}
		if(('product_promotion' in fn) && fn.product_promotion.approve == 1) {
			$('#pr_product_promotion_approve').prop('checked', true);
		}
		if(('product_promotion' in fn) && fn.product_promotion.disable == 1) {
			$('#pr_product_promotion_disable').prop('checked', true);
		}

		//BALIK101
		// developer settings
		if(('developer_settings' in fn) && fn.developer_settings == 1) {
			$('#dev_settings_view').prop('checked', true);
		}

		//shop utilities
		if(('shop_utilities' in fn) && fn.shop_utilities.view == 1) {
			$('#dev_settings_shop_utilities_view').prop('checked', true);
		}
		if(('shop_utilities' in fn) && fn.shop_utilities.update == 1) {
			$('#dev_settings_shop_utilities_update').prop('checked', true);
		}

		//content navigation
		if(('content_navigation' in fn) && fn.content_navigation.view == 1) {
			$('#dev_settings_content_navigation_view').prop('checked', true);
		}
		if(('content_navigation' in fn) && fn.content_navigation.create == 1) {
			$('#dev_settings_content_navigation_create').prop('checked', true);
		}
		if(('content_navigation' in fn) && fn.content_navigation.update == 1) {
			$('#dev_settings_content_navigation_update').prop('checked', true);
		}
		if(('content_navigation' in fn) && fn.content_navigation.disable == 1) {
			$('#dev_settings_content_navigation_disable').prop('checked', true);
		}
		if(('content_navigation' in fn) && fn.content_navigation.delete == 1) {
			$('#dev_settings_content_navigation_delete').prop('checked', true);
		}

		//cron logs
		if(('cron_logs' in fn) && fn.cron_logs.view == 1) {
			$('#dev_settings_cron_logs_view').prop('checked', true);
		}
		if(('cron_logs' in fn) && fn.cron_logs.disable == 1) {
			$('#dev_settings_cron_logs_disable').prop('checked', true);
		}

		//manual cron
		if(('manual_cron' in fn) && fn.manual_cron == 1) {
			$('#dev_settings_mcron_view').prop('checked', true);
		}

		//client information
		if(('client_information' in fn) && fn.client_information.view == 1) {
			$('#dev_settings_clief_info_view').prop('checked', true);
		}
		if(('client_information' in fn) && fn.client_information.create == 1) {
			$('#dev_settings_clief_info_create').prop('checked', true);
		}
		if(('client_information' in fn) && fn.client_information.update == 1) {
			$('#dev_settings_clief_info_update').prop('checked', true);
		}
		if(('client_information' in fn) && fn.client_information.disable == 1) {
			$('#dev_settings_clief_info_disable').prop('checked', true);
		}
		if(('client_information' in fn) && fn.client_information.delete == 1) {
			$('#dev_settings_clief_info_delete').prop('checked', true);
		}

		//maintenance page
		if(('maintenance_page' in fn) && fn.maintenance_page.view == 1) {
			$('#dev_settings_mainte_page_view').prop('checked', true);
		}
	
		if(('maintenance_page' in fn) && fn.maintenance_page.update == 1) {
			$('#dev_settings_mainte_page_update').prop('checked', true);
		}

        //api request postback logs
		if(('api_request_postback_logs' in fn) && fn.api_request_postback_logs.view == 1) {
			$('#dev_settings_api_request_postback_logs_view').prop('checked', true);
		}

		//email_settings
		if(('email_settings' in fn) && fn.email_settings.view == 1) {
			$('#dev_settings_email_view').prop('checked', true);
		}
	
		if(('email_settings' in fn) && fn.email_settings.update == 1) {
			$('#dev_settings_email_update').prop('checked', true);
		}
	
	
		//audit trail
		if(('audit_trail' in fn) && fn.audit_trail.view == 1) {
			$('#dev_settings_audit_trail_view').prop('checked', true);
		}
		
		//postback logs
		if(('api_postback_logs' in fn) && fn.api_postback_logs.view == 1) {
			$('#dev_settings_postback_logs_view').prop('checked', true);
		}

		//pandabooks api logs
		if(('pandabooks_api_logs' in fn) && fn.pandabooks_api_logs.view == 1) {
			$('#dev_settings_pandabooks_api_logs_view').prop('checked', true);
		}
	}
	
	run_sub_parent_checkbox_checker();
}