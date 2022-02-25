<style>
	table {
		margin: 10px;
	}
	tbody tr td {
		padding: 4px;
	}
	.md {
		width: 350px;
		font-weight: bold;
	}

</style>
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
	<div class="bc-icons-2 card mb-4">
		<ol class="breadcrumb mb-0 primary-bg px-4 py-3">
			<li class="breadcrumb-item"><a class="white-text"
					href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
			<li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
			<li class="breadcrumb-item"><a class="white-text"
					href="<?=base_url('Settings_void_record/void_record_list/'.$token);?>">Void Record List</a></li>
			<li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
			<li class="breadcrumb-item active">Void - Order Details</li>
		</ol>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
						<table
							class="table table-striped table-hover table-bordered table-grid table-item display nowrap"
							style="width:100%" id="table-item">
							<thead>
								<tr>
									<th></th>
									<th>Item Name</th>
									<th>Qty</th>
									<th>Amount</th>
									<th>Total Amount</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>

			<div class="col-lg-12">&nbsp;</div>
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-info no-margin"></i> &nbsp;Void Details</h3>
					</div>
					<div class="card-body">
						<table>
							<tbody>
								<tr>
									<td class="md">Void Date</td>
									<td><span id="void-date"><?= $void_details->date_created; ?></span></td>
								</tr>
								<tr>
									<td class="md">Void ID</td>
									<td><span id="void-id"><?= $void_details->f_id; ?></span></td>
								</tr>
								<tr>
									<td class="md">Reference Number</td>
									<td><span id="ref-num"><?= $void_details->reference_num; ?></span></td>
								</tr>
								<tr>
									<td class="md">Type</td>
									<td><span id="void-type"><?= $void_details->type; ?></span></td>
								</tr>
								<tr>
									<td class="md">Void Reason</td>
									<td><span id="reason"><?= $void_details->reason; ?></span></td>
								</tr>
								<tr>
									<td class="md">Order Status</td>
									<td><span id="order-status"
											class="badge badge-danger"><?= ucfirst($order_status); ?></span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col-lg-12">&nbsp;</div>
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-info no-margin"></i> &nbsp;Order Details</h3>
					</div>

					<div class="card-body">
                        <?php if ($void_details->bid == '' || $void_details->bid == null): ?>
                            <h4>No Order Details History found.</h4>
                        <?php else: ?>
						<table>
							<tbody>
								<tr>
									<td class="md">PayPanda Reference No.</td>
									<td><span id="paypanda-ref"><?= $void_details->paypanda_ref ?></span></td>
								</tr>
								<tr>
									<td class="md">Total Amount</td>
									<td><span id="total-amnt"><?= $void_details->total_amount; ?></span></td>
								</tr>
								<tr>
									<td class="md">SRP Amount</td>
									<td><span id="srp-amnt"><?= $void_details->srp; ?></span></td>
								</tr>
								<tr>
									<td class="md">Payment Method</td>
									<td><span id="pay-method"><?= $void_details->payment_method; ?></span></td>
								</tr>
								<tr>
									<td class="md">Date Ordered</td>
									<td><span id="order-date"><?= $void_details->date_ordered; ?></span></td>
								</tr>
								<tr>
									<td class="md">Transaction Date</td>
									<td><span id="pay-date"><?= $void_details->payment_date; ?></span></td>
								</tr>
								<tr>
									<td class="md">Date Confirmed</td>
									<td><span id="date-confirmed"><?= $void_details->date_confirmed; ?></span></td>
								</tr>
								<tr>
									<td class="md">Date Shipped</td>
									<td><span id="date-shipped"><?php $split = explode(' ', $void_details->date_shipped); $date_shipped = $split[0]; echo $date_shipped == '0000-00-00' ? '-' : $void_details->date_shipped; ?></span></td>
								</tr>
							</tbody>
						</table>
                        <?php endif; ?>
					</div>
				</div>
			</div>
            
            <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-user no-margin"></i> &nbsp;Customer</h3>
                    </div>
                    <div class="card-body">
                        <table>
                            <tbody>
                                <tr>
									<td class="md">Name</td>
									<td><span id="name"><?= $void_details->name; ?></span></td>
								</tr>
								<tr>
									<td class="md">Contact No.</td>
									<td><span id="conno"><?= $void_details->conno; ?></span></td>
								</tr>
                                <tr>
									<td class="md">Email</td>
									<td><span id="username"><?= $void_details->username; ?></span></td>
								</tr>
								<tr>
									<td class="md">Address</td>
									<td><span id="address"><?= $void_details->address; ?></span></td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-12 text-right">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-info" id="closeBtn">Close</button>
                </div>
            </div>
        </div>
		</div>
	</div>
</div>

<!-- hidden fields -->
<input type="hidden" id="url_ref_num" value="<?= $url_ref_num; ?>">
<input type="hidden" id="ref_num" value="<?= $reference_num; ?>">
<!-- start ---- load footer here and some specific js -->
<?php $this->load->view('includes/footer'); ?> <!-- includes your footer -->
<script type="text/javascript" src="<?= base_url('assets/js/settings/settings_void_order_details_view.js'); ?>"></script>
<!-- end ---- load footer here and some specific js -->