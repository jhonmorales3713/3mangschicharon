<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_promotions/');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Products Discount</span>
        
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title " id="exampleModalLabel"><span class="mtext_record_status">Disable</span> Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary modal-btnsave" id="disable_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <small>This action cannot be undone.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary modal-btnsave" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
	<section class="tables ml-4">   
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">
                                Products Discount List
                            </h3>
                        </div>
                        <div class="col">
                            <p class="border-search_hideshow  mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="search_hideshow_btn"><i class="fa  text-white fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
				<div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
					<form id="form_search">
						<div class="row">
								<!-- start - record status is a default for every table -->
							<div class="col-md-5 col-lg-3">
								<div class="form-group">
                                    <label for="_record_status">Status</label>
									<select name="_record_status" class="form-control  form-control-sm enter_search" id="_record_status">
										<option value="">All Records</option>
										<option value="1" selected>Enabled</option>
										<option value="2">Disabled</option>
									</select> 
								</div>
							</div>
							<div class="col-md-5 col-lg-3">
								<div class="form-group">
                                    <label for="date_from">Date From</label>
                                    <input type="date" placeholder="Date From" class="form-control form-control-sm" id="date_from">
								</div>
							</div>
							<div class="col-md-5 col-lg-3">
								<div class="form-group">
                                    <label for="date_to">Date To</label>
                                    <input type="date" placeholder="Date From" class="form-control form-control-sm" id="date_to">
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<!-- <div class="form-group"> -->
                                    <!-- <label for="_search">Search</label> -->
									<!-- <label class="form-control-label col-form-label-sm">Username</label> -->
									<!-- <input type="text" name="_search" id="_search"class="form-control  form-control-sm search-input-text url enter_search" placeholder="Search Here"> -->
								<!-- </div> -->
							</div>
                            <div class="col table-search-container pull-right">
                                <div class="d-flex justify-content-end">
                                    <span class="align-end">
                                        <div class="row no-gutters">
                                            <div class="col-12 col-md-auto px-1 mb-3 mr-1">
                                                <?php if ($this->loginstate->get_access()['aul']['create']== 1): ?>
                                                    <!-- <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#user_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger btnClickAddRecord d-flex align-items-center justify-content-center btn-block mb-3" id="addBtn">Add</button> -->
                                                    <a class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary d-flex align-items-center justify-content-center btn-block mb-3" href="<?=base_url()."admin/Main_promotions/add_product_discount/".$token?>" id="addBtn">Add</a>
                                                <?php endif ?>
                                            </div>
                                            <div class="col-12 col-md-auto">
                                                <div class="row no-gutters">
                                                    <div class="col-12 col-md-auto px-1">
                                                        <form action="<?=base_url('settings/user_list/export_user_list_table')?>" method="post" target="_blank">
                                                            <input type="hidden" name="_search" id="_search">
                                                            <input type="hidden" name="_filter" id="_filter">
                                                            <!-- <input type="hidden" name="_data" id="_data"> -->
                                                            <button class="btn btn-primary btnExport w-100" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                                        </form>  
                                                    </div>
                                                    <div class="col-6 col-md-auto px-1">
                                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                    </div>
                                                    <div class="col-6 col-md-auto px-1">
                                                        <button class="btn btn-primary btnSearch btn-block w-100" type="button" id="btnSearch">Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>
						</div>
					</form>
					<!-- <div class="form-group text-right">
						<button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
					</div> -->

				</div>
				<div class="card-body table-body ">
					

			
				<!-- end - record status is a default for every table -->
				<table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
					<thead>
						<tr>
							<th>Start Date</th>
							<th>Valid Until</th>
							<!-- <th>Discount Type</th> -->
							<th>Discount Amount</th>
							<!-- <th>Usage Quantity</th> -->
							<th>Status</th>
							<th width="30">Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
    </section>

</div>

<div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header secondary-bg white-text d-flex align-items-center">
				<h3 class="modal-title" id="exampleModalLabel">Confirmation</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
				<small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="disable_modal_confirm_btn">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header secondary-bg white-text d-flex align-items-center">
				<h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this record?</p>
				<small>This action cannot be undone.</small>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
			</div>
		</div>
	</div>
</div>

<!-- start - load the footer here and some specific js -->
<input type="hidden" id="token" value="<?=$token?>">

<script>
var token = "<?=$token;?>";
</script>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/promotions/products.js');?>"></script>
<!-- end - load the footer here and some specific js -->