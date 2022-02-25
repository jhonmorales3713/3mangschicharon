<style>
.borderless td, .borderless th {
    border: none;
}
</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Refund Order"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>">Orders</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php if ($task == 'Create'): ?>
                <li class="breadcrumb-item active">Refund Order</li>
            <?php else: ?>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url("Main_orders/$parent_url/$token");?>">Refund Order <?php echo title_case(array_last(explode('_', $parent_url))) ?></a></li>
                <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                <li class="breadcrumb-item active"><?=$task?></li>
            <?php endif; ?>
        </ol>
    </div>

    <button id="approve_mdl_btn" class="hidden" data-toggle="modal" data-target="#approve_modal"></button>
    <button id="reject_mdl_btn" class="hidden" data-toggle="modal" data-target="#reject_modal"></button>

    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title"><?=$task?> Refund Order</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <?php if ($task == 'Create') : ?>
                        <div class="row m-0 mb-3">
                            <select id="refnum_search" title="Reference Number" class="selectpicker show-menu-arrow mr-md-2 col-sm-12 col-md-2 p-0" data-live-search="true"></select>
                            <button class="btn btn-primary btnSearch col-md-1 mt-2 mt-md-0 col-12" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                        <?php else : ?>
                            <div class="form-group col-md-3">
                                <input type="hidden" name="summary_id" id="summary_id" value="<?=$reforder_id?>">
                                <input type="text" class="form-control" name="refnum_search" id="refnum_search" value="<?=$refnum?>" placeholder="Reference Num" disabled>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
                <div id="ref-info-container" class="card-body" style="display: none;">
                    <div class="record_status_1 card bg-green-400 mb-2" style="display: none;">
                        <div class="card-body text-md text-center font-bold"><i class="fa fa-thumbs-up" aria-hidden="true"></i>&nbsp;Approved</div>
                    </div>
                    <div class="record_status_2 card bg-red-400 mb-2" style="display: none;">
                        <div class="card-body text-md text-center font-bold"><i class="fa fa-thumbs-down" aria-hidden="true"></i>&nbsp;Rejected</div>
                    </div>
                    <div class="card bg-blue-300 px-4 py-2 mb-3">
                        <table id="card-table" class="table borderless table-responsive">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="font-bold text-lg"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;Order Summary</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-semibold" data-name>Name</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="font-semibold">Payment Breakdown</td>
                                </tr>
                                <tr>
                                    <td>Orders Amount</td>
                                    <td data-total_amount></td>
                                </tr>
                                <tr>
                                    <td>Shipping Fee</td>
                                    <td data-delivery_amount></td>
                                </tr>
                                <tr>
                                    <td>Payment Portal Fee</td>
                                    <td data-payment_portal_fee></td>
                                </tr>
                                <tr>
                                    <td>Total Amount</td>
                                    <td data-subtotal></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <table id="table-grid" class="table table-striped table-hover table-bordered table-responsive">
                        <thead class="thead-inverse">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input type="checkbox" id="chkAll" checked>
                                    </div>
                                </th>
                                <th>Shop</th>
                                <th>Branch</th>
                                <th>Item Name</th>
                                <th>Item Price</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="">
                        <form id="refund_form" action="<?=base_url() . 'orders/Refund_order/'.$formAction;?>" method="POST">
                            <div class="row">
                                <input type="hidden" name="refnum" id="refnum">
                                <input type="hidden" name="summary_tbl" id="summary_tbl">
                                <input type="hidden" name="refund_tbl" id="refund_tbl">
                                <div class="form-group col-12">
                                    <label for="ref_amt" class="col-sm-1-12 col-form-label">Refund Amount</label>
                                    <div class="col-sm-1-12">
                                        <input type="text" class="form-control" name="ref_amt" id="ref_amt" placeholder="" readonly data-createdetails data-total_amount>
                                        <div class="ref_amt invalid-feedback text-sm"></div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ref_mode" class="col-sm-1-12 col-form-label">Mode of Refund</label>
                                    <select class="form-control custom-select" name="ref_mode" id="ref_mode" data-createdetails data-mode>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">G-Cash</option>
                                        <option value="onlinebank">Online Bank</option>
                                    </select>
                                    <div class="ref_mode invalid-feedback text-sm"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="acc_num" class="col-sm-1-12 col-form-label" data-acc_num_str>Address</label>
                                    <div class="col-sm-1-12">
                                        <input type="text" class="form-control" name="acc_num" id="acc_num" placeholder="" data-createdetails data-acc_num>
                                        <div class="acc_num invalid-feedback text-sm"></div>
                                    </div>
                                </div>
                                <div class="container p-0">
                                    <div class="row m-0">
                                        <div class="form-group col">
                                            <label for="remarks">Refund Reasons</label>
                                            <textarea class="form-control col-12" name="remarks" id="remarks" rows="3" data-createdetails data-remarks></textarea>
                                            <div class="remarks invalid-feedback text-sm"></div>
                                        </div>
                                        <?php if ($task == 'View') : ?>
                                        <div class="form-group col">
                                            <label for="review_remarks">Review Remarks</label>
                                            <textarea class="form-control" name="review_remarks" id="review_remarks" rows="3" data-viewdetails data-review_remarks></textarea>
                                            <div class="review_remarks invalid-feedback text-sm"></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($task == 'View') : ?>
                            <div id="viewdetails-actions" class="row justify-content-end">
                                <?php if (isset($can['reject']) && $can['reject'] == 1) : ?>
                                <div class="col-12 col-md-2 my-2">
                                    <button id="btnReject" type="button" class="w-100 text-center btn btn-outline-secondary">Reject</button>
                                </div>
                                <?php endif; if (isset($can['approve']) && $can['approve'] == 1) : ?>
                                <div class="col-12 col-md-2 my-2">
                                    <button type="submit" class="w-100 text-center btn btn-primary">Approve</button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php else : ?>
                            <div class="row justify-content-end">
                                <div class="col-12 col-md-8 my-2">
                                    <div class="form-group">
                                        <div class="alert alert-warning" role="alert">
                                            Refund can only be used for processing partial refund transactions only. For full refund, please use the Void Submodule.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-2 my-2">
                                    <?php if ($task == 'Create') : ?>
                                        <a href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>" type="reset" class="w-100 text-center btn btn-outline-secondary">Back</a>
                                    <?php elseif ($task == 'Edit') : ?>
                                        <a href="<?=base_url('Main_orders/refund_approval/'.$token);?>" type="reset" class="w-100 text-center btn btn-outline-secondary">Back</a>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($can['update']) && $can['update'] == 1 || isset($can['create']) && $can['create'] == 1) : ?>
                                <div class="col-12 col-md-2 my-2">
                                    <button type="submit" class="w-100 text-center btn btn-primary">Save</button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal-->
<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Approve Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <b class="mtext_record_status">Approved</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Approved.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="approve_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reject_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Reject Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <b class="mtext_record_status">Reject</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Rejected.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="reject_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>
    var token = "<?=$token;?>";
    var task = "<?=$task;?>";
</script>
<script type="text/javascript" src="<?=base_url('assets/js/orders/refund_order.js');?>"></script>