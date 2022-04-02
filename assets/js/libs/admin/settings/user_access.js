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
		$('#avatar_preview').attr('src', base_url + 'assets/uploads/avatars/' + data.avatar);
		$('#current_avatar_url').val(data.avatar);
	} else {
		$('#avatar-placeholder').removeClass('hidden');
		$('#change-image').hide();
	}
	console.log(data);

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
					//$('#ac_products_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 3){ // reference cp_main_navigation->main_nav_id
					//have products module
					$('#ac_products_view').prop('checked', true);
				}
				if(access_nav_arr[i] == 8){ // reference cp_main_navigation->main_nav_id
					//have shops module
					$('#settings_aul_view').prop('checked', true);
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
		if(('products' in fn) && fn.products.disable == 1) {
			$('#ac_products_disable').prop('checked', true);
		}
		
		//variants
		if(('variants' in fn) && fn.products.view == 1) {
			$('#ac_variants_view').prop('checked', true);
		}
		if(('variants' in fn) && fn.products.create == 1) {
			$('#ac_variants_create').prop('checked', true);
		}
		if(('variants' in fn) && fn.products.update == 1) {
			$('#ac_variants_update').prop('checked', true);
		}
		if(('variants' in fn) && fn.products.delete == 1) {
			$('#ac_variants_delete').prop('checked', true);
		}
		if(('variants' in fn) && fn.products.disable == 1) {
			$('#ac_variants_disable').prop('checked', true);
		}

		//Product Category
		if(('product_category' in fn) && fn.product_category.view == 1) {
			$('#ac_products_category_view').prop('checked', true);
		}
		if(('product_category' in fn) && fn.product_category.create == 1) {
			$('#ac_products_category_create').prop('checked', true);
		}
		if(('product_category' in fn) && fn.product_category.update == 1) {
			$('#ac_products_category_update').prop('checked', true);
		}
		if(('product_category' in fn) && fn.product_category.delete == 1) {
			$('#ac_products_category_delete').prop('checked', true);
		}
		if(('product_category' in fn) && fn.product_category.disable == 1) {
			$('#ac_products_category_disable').prop('checked', true);
		}

		
		//Admin User List
		if(('aul' in fn) && fn.aul.view == 1) {
			$('#settings_aul_view').prop('checked', true);
		}
		if(('aul' in fn) && fn.aul.create == 1) {
			$('#settings_aul_create').prop('checked', true);
		}
		if(('aul' in fn) && fn.aul.update == 1) {
			$('#settings_aul_update').prop('checked', true);
		}
		if(('aul' in fn) && fn.aul.delete == 1) {
			$('#settings_aul_delete').prop('checked', true);
		}
		if(('aul' in fn) && fn.aul.disable == 1) {
			$('#settings_aul_disable').prop('checked', true);
		}
		
		//Website Information
		if(('web' in fn) && fn.web.view == 1) {
			$('#settings_web_view').prop('checked', true);
		}
		if(('web' in fn) && fn.web.update == 1) {
			$('#settings_web_update').prop('checked', true);
		}

		//console.log(fn);
		//orders
		if(('orders' in fn) && fn.orders.view == 1) {
			$('#ac_transactions_view').prop('checked', true);
		}
		if(('orders' in fn) && fn.orders.process == 1) {
			$('#ac_transactions_process').prop('checked', true);
		}
		if(('orders' in fn) && fn.orders.decline == 1) {
			$('#ac_transactions_decline').prop('checked', true);
		}

	}
	run_sub_parent_checkbox_checker();
}