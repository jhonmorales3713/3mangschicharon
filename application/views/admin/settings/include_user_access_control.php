<style>
	.nav-link{
		z-index: 100;
	}
    
	
    .nav-link.active{
        background: var(--primary-color)  !important;
        color:white !important;
        transition: all 0.1s ease-in-out;
        
    }
	.tab-pane{
		border-color: #007bff;
	}
	.check-count{
		position: relative;
		top: -10px;
		left: 10px;
	}
</style>
<div class="py-4">
	<div class="form-group col-12 super_parent_section">
		<!-- <h2 class="font-weight-bold">Overall Access</h2>
		<div class="row">
			<div class="col-md-3">
				<label>
					<input type="checkbox" id="overall_access" name="overall_access" class="super_parent_checkbox checkbox-template m-r-xs mr-2" >
					System Administrator
				</label>
			</div>
			<div class="col-md-3">
				<label>
					<input type="checkbox" id="seller_access" name="seller_access" class="super_parent_checkbox checkbox-template m-r-xs mr-2" >
					Seller Account
				</label>
			</div>
			<div class="col-md-3">
				<label>
					<input type="checkbox" id="seller_branch_access" name="seller_branch_access" class="super_parent_checkbox checkbox-template m-r-xs mr-2" >
					Seller Branch
				</label>
			</div>
			<div class="col-md-3">
				<label>
					<input type="checkbox" id="food_hub_access" name="food_hub_access" class="super_parent_checkbox checkbox-template m-r-xs mr-2" >
					Food Hub
				</label>
			</div>
		</div>
		<hr> -->
		<div class="form-group parent_section mt-3">
			<h3 class="font-weight-bold">Access Control</h3>
			<div class="row mb-1">
				<div class="col-6 col-sm-3">
					<label>
						<input type="checkbox" id="ac_online_ordering_view" name="ac_online_ordering_view" class="checkbox-template m-r-xs mr-2">
						All
					</label>
				</div>
			</div>
		</div>
		<div class="">
			<div class="nav nav-tabs p-0 row" id="v-pills-tab">
				<a class="nav-link col active" id="v-pills-dashboard-tab" data-toggle="pill" href="#v-pills-dashboard" role="tab" aria-controls="v-pills-dashboard" aria-selected="true">Dashboard <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a>
				<!-- <a class="nav-link col" id="v-pills-order-headers-tab" data-toggle="pill" href="#v-pills-order-headers" role="tab" aria-controls="v-pills-order-headers" aria-selected="false" class="row m-0">Orders <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a> -->
				<a class="nav-link col" id="v-pills-products-tab" data-toggle="pill" href="#v-pills-products" role="tab" aria-controls="v-pills-products" aria-selected="false" class="row m-0">Products <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a>
				<!-- <a class="nav-link col" id="v-pills-shops-tab" data-toggle="pill" href="#v-pills-shops" role="tab" aria-controls="v-pills-shops" aria-selected="false">Shops <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a>
				<a class="nav-link col" id="v-pills-customers-tab" data-toggle="pill" href="#v-pills-customers" role="tab" aria-controls="v-pills-customers" aria-selected="false">Customers <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a>
				<a class="nav-link col" id="v-pills-accounts-tab" data-toggle="pill" href="#v-pills-accounts" role="tab" aria-controls="v-pills-accounts" aria-selected="false">Accounts <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a>
				<a class="nav-link col" id="v-pills-reports-tab" data-toggle="pill" href="#v-pills-reports" role="tab" aria-controls="v-pills-reports" aria-selected="false">Reports <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a> -->
				<a class="nav-link col" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a>
				<!-- <a class="nav-link col" id="v-pills-dev_settings-tab" data-toggle="pill" href="#v-pills-dev_settings" role="tab" aria-controls="v-pills-dev_settings" aria-selected="false">Developer Settings <span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light">0</span></a> -->
			</div>
			<div class="tab-content" id="v-pills-tabContent">
				<div class="tab-pane fade show active w-100 online_ordering parent_section has-seller-access has-seller-branch-access has-food-hub-access" id="v-pills-dashboard" role="tabpanel" aria-labelledby="v-pills-dashboard-tab">
					<div class="py-3 px-4 row m-0">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" id="main_nav_dashboard_view" name="main_nav_dashboard_view" class="main_nav_dashboard_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_view" name="ac_dashboard_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_sales_count" name="ac_dashboard_sales_count" class="checkbox-template m-r-xs mr-2">
										Sales (Count)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_transactions_count" name="ac_dashboard_transactions_count" class="checkbox-template m-r-xs mr-2">
										Transactions (Count)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_views_count" name="ac_dashboard_views_count" class="checkbox-template m-r-xs mr-2">
										Views (Count)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_overall_sales_count" name="ac_dashboard_overall_sales_count" class="checkbox-template m-r-xs mr-2">
										Overall Sales (Count)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_visitors_chart" name="ac_dashboard_visitors_chart" class="checkbox-template m-r-xs mr-2">
										Visitors (Chart)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_views_chart" name="ac_dashboard_views_chart" class="checkbox-template m-r-xs mr-2">
										Views (Chart)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_sales_chart" name="ac_dashboard_sales_chart" class="checkbox-template m-r-xs mr-2">
										Sales (Chart)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_top10productsold_list" name="ac_dashboard_top10productsold_list" class="checkbox-template m-r-xs mr-2">
										Top 10 Products Sold (List)
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_dashboard_transactions_chart" name="ac_dashboard_transactions_chart" class="checkbox-template m-r-xs mr-2">
										Transactions (Chart)
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 online_ordering parent_section has-seller-access has-seller-branch-access has-food-hub-access" id="v-pills-order-headers" role="tabpanel" aria-labelledby="v-pills-order-headers-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden"  id="main_nav_ac_orders_view" name="main_nav_ac_orders_view">
								<input type="checkbox" class="main_nav_ac_orders_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Orders</h4>
							<div class="row">
								<!-- <div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_transactions_view" name="ac_transactions_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div> -->
								<!-- <div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_transactions_create" name="ac_transactions_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div> -->
								<!-- <div class="col-6 col-sm-3 hidden">
									<label>
										<input type="checkbox" id="ac_transactions_update" name="ac_transactions_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div> -->
								<!-- <div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_transactions_delete" name="ac_transactions_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div> -->
								<!-- <div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" id="ac_transactions_reassign" name="ac_transactions_reassign" class="checkbox-template m-r-xs mr-2">
										Re-assign
									</label>
								</div>
								<div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" name="ac_transactions_mark_as_paid" class="ac_transactions_mark_as_paid checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Mark as Paid
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_process_order" class="ac_transactions_process_order checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Accept
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_decline_order" class="ac_transactions_decline_order checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Decline
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_ready_pickup" class="ac_transactions_ready_pickup checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Ready for Pick Up
									</label>
								</div>
								<div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" name="ac_transactions_booking_confirmed" class="ac_transactions_booking_confirmed checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Confirm Booking
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_mark_fulfilled" class="ac_transactions_mark_fulfilled checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										To Deliver
									</label>
								</div>
								<div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" name="ac_returntosender" class="ac_returntosender checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Return To Sender
									</label>
								</div>
								<div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" name="ac_redeliver" class="ac_redeliver checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Re-Deliver Order
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_shipped" class="ac_transactions_shipped checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Item Delivered
									</label>
								</div>
								<div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" name="ac_confirmed" class="ac_confirmed checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Delivery Confirmed
									</label>
								</div>
								<div class="col-6 col-sm-3" style="display:none;">
									<label>
										<input type="checkbox" name="ac_merchant_ol_view" class="ac_merchant_ol_view checkbox-template m-r-xs mr-2 order_list_ac_fxns">
										Merchant Order List
									</label>
								</div> -->
							</div>
						</div>
						<!-- <div class="col-12 mt-2">
							<h4 class="font-semibold">Cancelled Orders</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_cancelled_orders" name="ac_cancelled_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div> -->
						<!-- <div class="col-12 mt-2">
							<h4 class="font-semibold">Pending Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_pending_orders" name="ac_pending_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_mark_as_paid" class="ac_transactions_mark_as_paid checkbox-template m-r-xs mr-2">
										Process
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Paid Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_paid_orders" name="ac_paid_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Ready For Processing Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_readyforprocessing_orders" name="ac_readyforprocessing_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_process_order" class="ac_transactions_process_order checkbox-template m-r-xs mr-2">
										Process
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Processing Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_processing_orders" name="ac_processing_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_ready_pickup" class="ac_transactions_ready_pickup checkbox-template m-r-xs mr-2">
										Process Pick-up
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_mark_fulfilled" class="ac_transactions_mark_fulfilled checkbox-template m-r-xs mr-2">
										Process Fulfillment
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Ready For Pickup Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_readyforpickup_orders" name="ac_readyforpickup_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_booking_confirmed" class="ac_transactions_booking_confirmed checkbox-template m-r-xs mr-2">
										Process
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Booking Confirmed Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_bookingconfirmed_orders" name="ac_bookingconfirmed_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_mark_fulfilled" class="ac_transactions_mark_fulfilled checkbox-template m-r-xs mr-2">
										Process
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Fulfilled Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_fulfilled_orders" name="ac_fulfilled_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_transactions_shipped" class="ac_transactions_shipped checkbox-template m-r-xs mr-2">
										Process Shipped
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_returntosender" class="ac_returntosender checkbox-template m-r-xs mr-2">
										Process Return
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Shipped Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_shipped_orders" name="ac_shipped_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Return To Sender List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_returntosender_orders" name="ac_returntosender_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" name="ac_redeliver" class="ac_redeliver checkbox-template m-r-xs mr-2">
										Process
									</label>
								</div>
							</div>
						</div> -->
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Voided Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_voided_orders" name="ac_voided_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Manual Orders</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_manualorder_list" name="ac_manualorder_list" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_manualorder_list_create" name="ac_manualorder_list_create" class=" checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Refund Order</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_refund_order_create" name="ac_refund_order_create" class="order-refund_order checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Refund Order Approval</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_refund_order_approval_view" name="ac_refund_order_approval_view" class="order-refund_order checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_refund_order_approval_update" name="ac_refund_order_approval_update" class="order-refund_order checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_refund_order_approval_approve" name="ac_refund_order_approval_approve" class="order-refund_order checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_refund_order_approval_reject" name="ac_refund_order_approval_reject" class="order-refund_order checkbox-template m-r-xs mr-2">
										Reject
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Refund Order Transactions</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_refund_order_trans_view" name="ac_refund_order_trans_view" class="order-refund_order checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">For Pick up Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_forpickup_orders" name="ac_forpickup_orders" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Delivery Confirmed Order List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_confirmed_order_list" name="ac_confirmed_order_list" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 online_ordering parent_section has-seller-access" id="v-pills-products" role="tabpanel" aria-labelledby="v-pills-messages-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden"  id="main_nav_ac_products_view" name="main_nav_ac_products_view">
								<input type="checkbox" class="main_nav_ac_products_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Products List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_view" name="ac_products_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_create" name="ac_products_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_update" name="ac_products_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_disable" name="ac_products_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_delete" name="ac_products_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Product Variant</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_variants_view" name="ac_variants_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_variants_create" name="ac_variants_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_variants_update" name="ac_variants_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_variants_disable" name="ac_variants_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_variants_delete" name="ac_variants_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Product Category</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_category_view" name="ac_products_category_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_category_create" name="ac_products_category_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_category_update" name="ac_products_category_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_category_disable" name="ac_products_category_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_category_delete" name="ac_products_category_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
                        
						<!-- <div class="col-12 mt-2">
							<h4 class="font-semibold">Menu waiting for approval</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_wfa_view" name="ac_products_wfa_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_wfa_edit" name="ac_products_wfa_edit" class="checkbox-template m-r-xs mr-2">
										Edit
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_wfa_approve" name="ac_products_wfa_approve" class="checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_wfa_decline" name="ac_products_wfa_decline" class="checkbox-template m-r-xs mr-2">
										Decline
									</label>
								</div>
							</div>
						</div>

						<div class="col-12 mt-2">
							<h4 class="font-semibold">Menu for Approve</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_apr_view" name="ac_products_apr_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_apr_approve" name="ac_products_apr_approve" class="checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_apr_decline" name="ac_products_apr_decline" class="checkbox-template m-r-xs mr-2">
										Decline
									</label>
								</div>
							</div>
						</div>


						<div class="col-12 mt-2">
							<h4 class="font-semibold">Menu for Decline</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_dec_view" name="ac_products_dec_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_dec_approve" name="ac_products_dec_approve" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
						     </div>
						</div>

						<div class="col-12 mt-2">
							<h4 class="font-semibold">Menu Verify </h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_verified_view" name="ac_products_verified_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>


						<div class="col-12 mt-2">
							<h4 class="font-semibold">Menu changes approval</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_changes_view" name="ac_products_changes_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_changes_edit" name="ac_products_changes_edit" class="checkbox-template m-r-xs mr-2">
										Edit
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_changes_approve" name="ac_products_changes_approve" class="checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_products_changes_decline" name="ac_products_changes_decline" class="checkbox-template m-r-xs mr-2">
										Decline
									</label>
								</div>
							</div>
						</div> -->


					</div>
				</div>
				<!-- <div class="tab-pane fade w-100 online_ordering parent_section" id="v-pills-shops" role="tabpanel" aria-labelledby="v-pills-shops-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden"  id="main_nav_ac_shops_view" name="main_nav_ac_shops_view">
								<input type="checkbox" class="main_nav_ac_shops_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Shops Profile</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_shops_view" name="ac_shops_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_shops_create" name="ac_shops_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_shops_update" name="ac_shops_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_shops_disable" name="ac_shops_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_shops_delete" name="ac_shops_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div id="shop-branch" class="col-12 mt-2">
							<h4 class="font-semibold">Shop Branch</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_branch_view" name="ac_settings_shop_branch_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_branch_create" name="ac_settings_shop_branch_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_branch_update" name="ac_settings_shop_branch_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_branch_disable" name="ac_settings_shop_branch_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_branch_delete" name="ac_settings_shop_branch_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div id="shop-branch-account" class="col-12 mt-2">
							<h4 class="font-semibold">Shops Branch Account</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="branch_account_view" name="branch_account_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="branch_account_create" name="branch_account_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="branch_account_update" name="branch_account_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="branch_account_disable" name="branch_account_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="branch_account_delete" name="branch_account_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div id="shops-account" class="col-12 mt-2">
							<h4 class="font-semibold">Shops Account</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_account_view" name="shop_account_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_account_create" name="shop_account_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_account_update" name="shop_account_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_account_disable" name="shop_account_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_account_delete" name="shop_account_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Merchant Registration</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_mer_reg_view" name="shop_mer_reg_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_mer_reg_approve" name="shop_mer_reg_approve" class="checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_mer_reg_edit" name="shop_mer_reg_edit" class="checkbox-template m-r-xs mr-2">
										Edit
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_mer_reg_decline" name="shop_mer_reg_decline" class="checkbox-template m-r-xs mr-2">
										Decline
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_mer_reg_delete" name="shop_mer_reg_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>

						<div class="col-12 mt-2">
							<h4 class="font-semibold">Shop changes approval</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_changes_view" name="shop_changes_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_changes_edit" name="shop_changes_edit" class="checkbox-template m-r-xs mr-2">
										Edit
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_changes_approve" name="shop_changes_approve" class="checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="shop_changes_decline" name="shop_changes_decline" class="checkbox-template m-r-xs mr-2">
										Decline
									</label>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="tab-pane fade w-100 online_ordering parent_section" id="v-pills-customers" role="tabpanel" aria-labelledby="v-pills-customers-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden" id="main_nav_ac_customers_view" name="main_nav_ac_customers_view">
								<input type="checkbox" class="main_nav_ac_customers_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Customer List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_customer_view" name="ac_customer_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3" hidden>
									<label>
										<input type="checkbox" id="ac_customer_create" name="ac_customer_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3" hidden>
									<label>
										<input type="checkbox" class="hidden"  id="ac_customer_update" name="ac_customer_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3" hidden>
									<label>
										<input type="checkbox" class="hidden"  id="ac_customer_disable" name="ac_customer_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3" hidden>
									<label>
										<input type="checkbox" class="hidden"  id="ac_customer_delete" name="ac_customer_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 online_ordering parent_section" id="v-pills-accounts" role="tabpanel" aria-labelledby="v-pills-accounts-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden" id="main_nav_ac_accounts_view" name="main_nav_ac_accounts_view">
								<input type="checkbox" class="main_nav_ac_accounts_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Billing Settlement</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_billing_view" name="ac_billing_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_billing_create" name="ac_billing_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_billing_update" name="ac_billing_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_billing_disable" name="ac_billing_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_billing_delete" name="ac_billing_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_billing_adminview" name="ac_billing_adminview" class="checkbox-template m-r-xs mr-2">
										Admin View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Billing (By Payment Portal Fee)</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="billing_portal_fee_view" name="billing_portal_fee_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="billing_portal_fee_create" name="billing_portal_fee_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="billing_portal_fee_update" name="billing_portal_fee_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="billing_portal_fee_disable" name="billing_portal_fee_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="billing_portal_fee_delete" name="billing_portal_fee_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 parent_section has-seller-access has-seller-branch-access has-food-hub-access" id="v-pills-reports" role="tabpanel" aria-labelledby="v-pills-reports-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-12">
							<label> 
								<input type="checkbox" id="main_nav_ac_reports_view" name="main_nav_ac_reports_view" class="hidden">
								<input type="checkbox" class="main_nav_ac_reports_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Average Order Value Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_aov_view" name="ac_aov_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Branch Performance Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_bpr_view" name="ac_bpr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Inventory List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_invlist_view" name="ac_invlist_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Inventory Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_inv_view" name="ac_inv_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Online Store Conversion Rate</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_oscrr_view" name="ac_oscrr_view" class="reports-with-sessions checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Online Store Sessions</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_ps_view" name="ac_ps_view" class="reports-with-sessions checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Inventory Ending Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_invend_view" name="ac_invend_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Order and Sales Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_os_view" name="ac_os_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2" style="display: none;">
							<h4 class="font-semibold">Order Status Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_osr_view" name="ac_osr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Orders By Location</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_oblr_view" name="ac_oblr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Pending Orders</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_po_view" name="ac_po_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Product Releasing Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_prr_view" name="ac_prr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Order Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_or_view" name="ac_or_view" class="reports-refund_order checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Refund Order Summary</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_rosum_view" name="ac_rosum_view" class="reports-refund_order checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Refund Order Status</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_rostat_view" name="ac_rostat_view" class="reports-refund_order checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2" hidden>
							<h4 class="font-semibold">Revenue by Branch</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_rbbr_view" name="ac_rbbr_view" class="reports-revenuesBy checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Revenue By Location</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_rbl_view" name="ac_rbl_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Revenue by Store</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_rbsr_view" name="ac_rbsr_view" class="reports-revenuesBy checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2" style="display: none;">
							<h4 class="font-semibold">Sales Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_sr_view" name="ac_sr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2" hidden>
							<h4 class="font-semibold">toktok Booking Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_tbr_view" name="ac_tbr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Order List Payout Status Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_olps_view" name="ac_olps_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Top Products Sold Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_tps_view" name="ac_tps_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Total Abandoned Carts Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_tacr_view" name="ac_tacr_view" class="reports-with-sessions checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Total Orders Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_to_view" name="ac_to_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Total Sales Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_tsr_view" name="ac_tsr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Withholding Tax Reports</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_wtr_view" name="ac_wtr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-4 mt-2">
							<h4 class="font-semibold">Merchant Serviceable Areas Report</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_msr_view" name="ac_msr_view" class=" checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>-->
				<div class="tab-pane fade w-100 parent_section" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label>
								<input type="checkbox" class="hidden" id="main_nav_ac_settings_view" name="main_nav_ac_settings_view">
								<input type="checkbox" class="main_nav_ac_settings_view sub_parent_checkbox checkbox-template m-r-xs mr-2">
								All
							</label>
						</div>
						<!-- <div class="col-12 mt-2">
							<h4 class="font-semibold">Announcement</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_announcement_view" name="ac_settings_announcement_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_announcement_update" name="ac_settings_announcement_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Currency</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_currency_view" name="ac_settings_currency_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_currency_create" name="ac_settings_currency_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_currency_update" name="ac_settings_currency_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_currency_disable" name="ac_settings_currency_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_currency_delete" name="ac_settings_currency_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Members</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_members_view" name="ac_settings_members_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_members_create" name="ac_settings_members_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_members_update" name="ac_settings_members_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_members_disable" name="ac_settings_members_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_members_delete" name="ac_settings_members_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Merchant User List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_muserlist_view" name="ac_settings_muserlist_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_muserlist_create" name="ac_settings_muserlist_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_muserlist_update" name="ac_settings_muserlist_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_muserlist_disable" name="ac_settings_muserlist_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_muserlist_delete" name="ac_settings_muserlist_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div> -->
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Admin User List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_aul_view" name="settings_aul_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_aul_create" name="settings_aul_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_aul_update" name="settings_aul_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_aul_disable" name="settings_aul_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_aul_delete" name="settings_aul_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div><!--
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Payment Types</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_payment_type_view" name="ac_settings_payment_type_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_payment_type_create" name="ac_settings_payment_type_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_payment_type_update" name="ac_settings_payment_type_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_payment_type_disable" name="ac_settings_payment_type_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_payment_type_delete" name="ac_settings_payment_type_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Referral Comrate</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ref_comrate_view" name="ref_comrate_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ref_comrate_create" name="ref_comrate_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ref_comrate_update" name="ref_comrate_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ref_comrate_disable" name="ref_comrate_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ref_comrate_delete" name="ref_comrate_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Region</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_region_view" name="settings_region_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_region_create" name="settings_region_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_region_update" name="settings_region_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_region_disable" name="settings_region_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_region_delete" name="settings_region_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Roles</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_roles_view" name="settings_roles_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_roles_create" name="settings_roles_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_roles_update" name="settings_roles_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_roles_disable" name="settings_roles_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_roles_delete" name="settings_roles_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">City</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_city_view" name="settings_city_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_city_create" name="settings_city_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_city_update" name="settings_city_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_city_disable" name="settings_city_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_city_delete" name="settings_city_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Province</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_province_view" name="settings_province_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_province_create" name="settings_province_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_province_update" name="settings_province_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_province_disable" name="settings_province_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="settings_province_delete" name="settings_province_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2 with_sub_parent_section" hidden>
							<h4 class="font-semibold">Shipping and Delivery</h4>
							<div class="py-3 px-4 row m-0 mb-1">
								<div class="col-6 col-sm-3">
									<label> 
										<input type="checkbox" class="hidden"  id="ac_shipping_and_delivery_view" name="ac_shipping_and_delivery_view">
										<input type="checkbox" class="ac_shipping_and_delivery_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
										All
									</label>
								</div>
								<div class="col-12 mt-2">
									<h4 class="font-semibold">General Shipping</h4>
									<div class="row">
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_general_shipping_view" name="ac_general_shipping_view" class="checkbox-template m-r-xs mr-2">
												View
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_general_shipping_create" name="ac_general_shipping_create" class="checkbox-template m-r-xs mr-2">
												Create
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_general_shipping_update" name="ac_general_shipping_update" class="checkbox-template m-r-xs mr-2">
												Update
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_general_shipping_disable" name="ac_general_shipping_disable" class="checkbox-template m-r-xs mr-2">
												Disable
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_general_shipping_delete" name="ac_general_shipping_delete" class="checkbox-template m-r-xs mr-2">
												Delete
											</label>
										</div>
									</div>
								</div>
								<div class="col-12 mt-2" hidden>
									<h4 class="font-semibold">Custom Shipping</h4>
									<div class="row">
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_custom_shipping_view" name="ac_custom_shipping_view" class="checkbox-template m-r-xs mr-2">
												View
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_custom_shipping_create" name="ac_custom_shipping_create" class="checkbox-template m-r-xs mr-2">
												Create
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_custom_shipping_update" name="ac_custom_shipping_update" class="checkbox-template m-r-xs mr-2">
												Update
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_custom_shipping_disable" name="ac_custom_shipping_disable" class="checkbox-template m-r-xs mr-2">
												Disable
											</label>
										</div>
										<div class="col-6 col-sm-3">
											<label>
												<input type="checkbox" id="ac_custom_shipping_delete" name="ac_custom_shipping_delete" class="checkbox-template m-r-xs mr-2">
												Delete
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Shipping Partners</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shipping_partners_view" name="ac_settings_shipping_partners_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shipping_partners_create" name="ac_settings_shipping_partners_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shipping_partners_update" name="ac_settings_shipping_partners_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shipping_partners_disable" name="ac_settings_shipping_partners_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shipping_partners_delete" name="ac_settings_shipping_partners_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Shop Banners</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_banners_view" name="ac_settings_shop_banners_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_banners_create" name="ac_settings_shop_banners_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_banners_update" name="ac_settings_shop_banners_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_banners_disable" name="ac_settings_shop_banners_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_shop_banners_delete" name="ac_settings_shop_banners_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Users</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_users_view" name="ac_settings_users_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_users_create" name="ac_settings_users_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_users_update" name="ac_settings_users_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_users_disable" name="ac_settings_users_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_users_delete" name="ac_settings_users_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div id="void-record" hidden class="col-12 mt-2">=
							<h4 class="font-semibold">Void Record</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_void_record_process" name="ac_settings_void_record_process" class="checkbox-template m-r-xs mr-2">
										Process
									</label>
								</div>
							</div>
						</div>
						<div id="void-record-list" hidden class="col-12 mt-2">
							<h4 class="font-semibold">Void Record List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_void_record_list_view" name="ac_settings_void_record_list_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div id="void-record-list" hidden class="col-12 mt-2">
							<h4 class="font-semibold">Bank List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_bank_view" name="ac_settings_bank_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_bank_create" name="ac_settings_bank_create" class="checkbox-template m-r-xs mr-2">
										Add
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_bank_update" name="ac_settings_bank_update" class="checkbox-template m-r-xs mr-2">
										Edit
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_settings_bank_delete" name="ac_settings_bank_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 online_ordering parent_section" id="v-pills-vouchers" role="tabpanel" aria-labelledby="v-pills-vouchers-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden" id="main_nav_ac_vouchers_view" name="main_nav_ac_vouchers_view">
								<input type="checkbox" class="main_nav_ac_vouchers_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Vouchers Claimed</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_vouchers_claimed_view" name="ac_vouchers_claimed_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Vouchers List</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_vouchers_list_view" name="ac_vouchers_list_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_vouchers_list_create" name="ac_vouchers_list_create" class="checkbox-template m-r-xs mr-2">
										create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_vouchers_list_update" name="ac_vouchers_list_update" class="checkbox-template m-r-xs mr-2">
										update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_vouchers_list_delete" name="ac_vouchers_list_delete" class="checkbox-template m-r-xs mr-2">
										delete
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_vouchers_list_disable" name="ac_vouchers_list_disable" class="checkbox-template m-r-xs mr-2">
										disable
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="tab-pane fade w-100 online_ordering parent_section" id="v-pills-wallet" role="tabpanel" aria-labelledby="v-pills-wallet-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label> 
								<input type="checkbox" class="hidden" id="main_nav_ac_wallet_view" name="main_nav_ac_wallet_view">
								<input type="checkbox" class="main_nav_ac_wallet_view sub_parent_checkbox checkbox-template m-r-xs mr-2" >
								All
							</label>
						</div>
						<div class="col-12">
							<h4 class="font-semibold">Pre Payment</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_prepayment_view" name="ac_prepayment_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_prepayment_create" name="ac_prepayment_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
							</div>
						</div>
						<div class="col-12">
							<h4 class="font-semibold">Manual Order</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_manual_order_view" name="ac_manual_order_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_manual_order_create" name="ac_manual_order_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
							</div>
						</div>
						<div class="col-12">
							<h4 class="font-semibold">Wallet Page</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_wallet_page_view" name="ac_wallet_page_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_wallet_page_encash" name="ac_wallet_page_encash" class="checkbox-template m-r-xs mr-2">
										Encash
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 parent_section" id="v-pills-support" role="tabpanel" aria-labelledby="v-pills-support-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label>
								<input type="checkbox" class="hidden"  id="main_nav_ac_csr_view" name="main_nav_ac_csr_view">
								<input type="checkbox" class="main_nav_ac_csr_view sub_parent_checkbox checkbox-template m-r-xs mr-2">
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Ticket History</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_history_view" name="ac_csr_ticket_history_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_history_create" name="ac_csr_ticket_history_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_history_update" name="ac_csr_ticket_history_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_history_disable" name="ac_csr_ticket_history_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_history_delete" name="ac_csr_ticket_history_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">CSR Ticket</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_view" name="ac_csr_ticket_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_create" name="ac_csr_ticket_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_update" name="ac_csr_ticket_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_disable" name="ac_csr_ticket_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_delete" name="ac_csr_ticket_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">CSR Ticket Log</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_log_view" name="ac_csr_ticket_log_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_log_create" name="ac_csr_ticket_log_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_log_update" name="ac_csr_ticket_log_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_log_disable" name="ac_csr_ticket_log_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="ac_csr_ticket_log_delete" name="ac_csr_ticket_log_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 parent_section" id="v-pills-promotion" role="tabpanel" aria-labelledby="v-pills-promotion-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label>
								<input type="checkbox" class="hidden"  id="main_nav_pr_product_promotion_view" name="main_nav_pr_product_promotion_view">
								<input type="checkbox" class="main_nav_pr_product_promotion_view sub_parent_checkbox checkbox-template m-r-xs mr-2">
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Piso Deals</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="pr_product_promotion_view" name="pr_product_promotion_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="pr_product_promotion_create" name="pr_product_promotion_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="pr_product_promotion_update" name="pr_product_promotion_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="pr_product_promotion_approve" name="pr_product_promotion_approve" class="checkbox-template m-r-xs mr-2">
										Approve
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="pr_product_promotion_disable" name="pr_product_promotion_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="pr_product_promotion_delete" name="pr_product_promotion_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade w-100 parent_section" id="v-pills-dev_settings" role="tabpanel" aria-labelledby="v-pills-dev_settings-tab">
					<div class="py-3 px-4 row m-0 mb-1">
						<div class="col-6 col-sm-3">
							<label>
								<input type="checkbox" class="hidden" id="main_nav_ac_devsettings_view" name="main_nav_ac_devsettings_view">
								<input type="checkbox" class="main_nav_ac_devsettings_view sub_parent_checkbox checkbox-template m-r-xs mr-2">
								All
							</label>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Audit Trail</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_audit_trail_view" name="dev_settings_audit_trail_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Tok-Tok API Postback Logs</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_postback_logs_view" name="dev_settings_postback_logs_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Pandabooks Api Logs</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_pandabooks_api_logs_view" name="dev_settings_pandabooks_api_logs_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Shop Utilities</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_shop_utilities_view" name="dev_settings_shop_utilities_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_shop_utilities_update" name="dev_settings_shop_utilities_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" >
							<h4 class="font-semibold">Content Navigation</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_content_navigation_view" name="dev_settings_content_navigation_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_content_navigation_create" name="dev_settings_content_navigation_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_content_navigation_update" name="dev_settings_content_navigation_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_content_navigation_disable" name="dev_settings_content_navigation_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_content_navigation_delete" name="dev_settings_content_navigation_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Cron Logs</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_cron_logs_view" name="dev_settings_cron_logs_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_cron_logs_disable" name="dev_settings_cron_logs_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Manual Cron</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_mcron_view" name="dev_settings_mcron_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<h4 class="font-semibold">Client Information</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_clief_info_view" name="dev_settings_clief_info_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_clief_info_create" name="dev_settings_clief_info_create" class="checkbox-template m-r-xs mr-2">
										Create
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_clief_info_update" name="dev_settings_clief_info_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_clief_info_disable" name="dev_settings_clief_info_disable" class="checkbox-template m-r-xs mr-2">
										Disable
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_clief_info_delete" name="dev_settings_clief_info_delete" class="checkbox-template m-r-xs mr-2">
										Delete
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Maintenance Page</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_mainte_page_view" name="dev_settings_mainte_page_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_mainte_page_update" name="dev_settings_mainte_page_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2" hidden>
							<h4 class="font-semibold">Api Request Postback Logs</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_api_request_postback_logs_view" name="dev_settings_api_request_postback_logs_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
							</div>
						</div>

						<div class="col-12 mt-2">
							<h4 class="font-semibold">Email Settings</h4>
							<div class="row">
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_email_view" name="dev_settings_email_view" class="checkbox-template m-r-xs mr-2">
										View
									</label>
								</div>
								<div class="col-6 col-sm-3">
									<label>
										<input type="checkbox" id="dev_settings_email_update" name="dev_settings_email_update" class="checkbox-template m-r-xs mr-2">
										Update
									</label>
								</div>
							</div>
						</div>
					
					</div>
				</div> -->
			</div>
		</div>
	</div>
</div>