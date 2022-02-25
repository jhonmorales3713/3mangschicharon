<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .datepicker{
    z-index: 999 !important;
  }

  td, th {
    vertical-align: middle !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Accounts">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Sale Settlement Report</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <!-- MAIN BODY -->
            <div class="card" id = "main_body">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                Sale Settlement Report
                            </h3>
                        </div>
                        <div class="col">
                            <p class="border-search_hideshow p-0 mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row">

                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" placeholder="<?=today_text();?>" name="start" readonly/>
                                    <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                    <input type="text" placeholder="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" id="date_to" name="end" readonly/>
                                </div>
                            </div>
                            <?php if($this->loginstate->get_access()['overall_access'] == 1):?>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                    <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif;?>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Status</label> -->
                                    <select name="select_status" id = "select_status" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="1" selected>All Status</option>
                                    <option value="2">On Process</option>
                                    <option value="3">Settled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- <div class="form-group text-right">
                      <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->

                </div>
                <div class="card-body table-body">
                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('reports/Sale_settlement2/export_billing_tbl')?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                    </form>
                                    <div class="col-6 col-md-auto px-1">
                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                      <!-- <div class="col-md-3">
                          <div class="form-group">
                              <div class="row">
                                  <div class="col-6" style="padding:0px;">
                                      <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                          <option value="">All Records</option>
                                          <option value="1" selected>Enabled</option>
                                          <option value="2">Disabled</option>
                                      </select>
                                  </div>
                              </div>
                          </div>
                      </div> -->
                      <!-- <div class="col-md-3 offset-md-9">
                        <div class="form-group text-right">
                          <button class="btn-mobile-w-100 mx-0 btn btn-primary btn_add" id="btn_deposit">Add</button>
                        </div>
                      </div> -->
                    </div>
                    <!-- end - record status is a default for every table -->

                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid" cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Billing Code</th>
                                <th>Total Amount</th>
                                <th>Processing Fee</th>
                                <th>Net Amount</th>
                                <th>Shop</th>
                                <th>Status</th>
                                <th width="30">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- SUB BODY  -->
            <div id="sub_body" style = "display:none">
              <div class="col-12 text-right mb-3">
                <button class="btn btn-info wave-effect wave-light btn_back"><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="card">
                <div class="card-header">
                  <h4>Billing Breakdown</h4>
                </div>
                <div class="card-body">
                  <div class="col-12 branch_billing_breakdown" >
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered" id = "table-item-branch" style = "width:100%;">
                      <colgroup>
                          <col style="width:10%;">
                          <col style="width:10%;">
                          <col style="width:10%;">
                          <col style="width:10%;">
                          <col style="width:10%;">
                          <col style="width:6%;">
                      </colgroup>
                      <thead>
                          <tr>
                              <th>Date</th>
                              <th>Branch</th>
                              <th>Total Amount</th>
                              <th>Processing Fee</th>
                              <th>Net Amount</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                    </table>
                    <hr>
                  </div>
                  <div class="col-12">
                      <table class="table wrap-btn-last-td table-striped table-hover table-bordered" id="table-item"  style="width:100%">
                          <colgroup>
                              <col style="width:10%;">
                              <col style="width:10%;">
                              <col style="width:10%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:6%;">
                          </colgroup>
                          <thead>
                              <tr>
                                  <th>Fulfillment Date</th>
                                  <th>Order Ref #</th>
                                  <th>Payment Ref #</th>
                                  <th>Amount</th>
                                  <th>Delivery Amount</th>
                                  <th>Process Fee</th>
                                  <th>Net Amount</th>
                                  <th>Action</th>
                              </tr>
                          </thead>
                      </table>
                      <hr>
                      <div class="row hidden">
                          <div class="col-12">
                              <div class="form-group">
                                  <label>Bill ID</label>
                                  <input type="text" name="f_billid" id="f_billid" class="form-control" value="0" >
                              </div>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-12 col-sm-4">
                              <label class="">Transaction Date</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_trandate"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Billing Number</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_billno" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Billing Code</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_billcode" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Shop Name</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_syshop"></label>
                          </div>
                          <!-- <div class="col-12 col-sm-4">
                              <label class="">Rate Type</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_ratetype"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Processing Rate</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_processrate"></label>
                          </div> -->
                          <div class="col-12 col-sm-4">
                              <label class="">Total Amount</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_totalamount" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Delivery Amount</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_delivery_amount" class="green-text"></label>
                          </div>

                          <div class="col-12 col-sm-4">
                              <label class="">Total Amount w/ Delivery Amount</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_totalamount_w_shipping" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Processing Fee</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_processfee" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Net Amount</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_netamount" class="green-text"></label>
                          </div>
                      </div>
                      <div class="row grp_payment" id="grp_payment">
                          <div class="col-12 col-sm-4">
                              <label class="">Date Settled</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_paiddate"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Amount Settled</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_paidamount" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Payment Type</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_paytype"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Payment Reference Number</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_payref"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Remarks</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_payremarks"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Attachment</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_payattach" class="green-text"></label>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-12 col-sm-4">
                              <label class="">Billing Status</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_paystatus" class="green-text"></label>
                          </div>
                          <div class="col-12 col-sm-4">
                              <label class="">Billing Note</label>
                          </div>
                          <div class="col-12 col-sm-8">
                              <label id="tm_remarks"></label>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="col-md-12 text-right">
                        <!-- <button type="button" class="btn blue-grey" data-dismiss="modal" aria-label="Close">Close</button> -->
                        <?php $functions = json_decode($this->session->functions);?>
                        <?php if($functions->billing->update == 1 ) : ?>
                        <button type="button" class="btn btn-primary waves-effect waves-light btn_tbl_pay" id="" >Mark as Settled</button>
                    <?php endif; ?>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id = "view_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Billing Logs</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id = "tbl_logs">
                <!-- <colgroup>
                    <col style="width:10%;">
                    <col style="width:10%;">
                    <col style="width:8%;">
                    <col style="width:8%;">
                    <col style="width:8%;">
                    <col style="width:8%;">
                    <col style="width:6%;">
                </colgroup> -->
                <thead>
                  <tr>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Ratetype</th>
                      <th>Process Rate</th>
                      <th>Quantity</th>
                      <th>Amount</th>
                      <th>Process Fee</th>
                      <th>Net Amount</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "view_modal2">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Billing Branch Logs</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id = "tbl_branch_logs">
                <!-- <colgroup>
                    <col style="width:10%;">
                    <col style="width:10%;">
                    <col style="width:8%;">
                    <col style="width:8%;">
                    <col style="width:8%;">
                    <col style="width:8%;">
                    <col style="width:6%;">
                </colgroup> -->
                <thead>
                  <tr>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Ratetype</th>
                      <th>Process Rate</th>
                      <th>Quantity</th>
                      <th>Amount</th>
                      <th>Process Fee</th>
                      <th>Net Amount</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="payment_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md">
            <div class="modal-content">
                <form id="form_save_payment" enctype="multipart/form-data" method="post" action="" >
                <div class="modal-header">
                    <div class="col-md-12">
                        <h4 id="tm_header_ref" class="modal-title">Billing Settlement</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <i class="fa fa-info no-margin">
                            </i> Billing Summary
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="">Billing Date:</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="tm_order_date-p"></label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="">Billing Code:</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="tm_order_reference_num-p" class="green-text"></label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="">Amount to be paid:</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="tm_amount-p" class="green-text"></label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="">Billing Status</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="tm_payment_status-p"></label>
                        </div>
                    </div>
                    <div class="row hidden">
                        <div class="col-12">
                            <div class="form-group">
                                <label>ID</label>
                                <input type="text" name="f_id-p" id="f_id-p" class="form-control" value="0" >
                            </div>
                        </div>
                    </div>
                    <div class="row hidden">
                        <div class="col-12">
                            <div class="form-group">
                                <label>CheckBoxID</label>
                                <input type="text" name="f_payment_ischecked" id="f_payment_ischecked" class="form-control" value="0" >
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row mb-2">
                        <div class="col-12 col-sm-12">
                            <label>
                                <input type="checkbox" id="tag_payment" name="tag_payment" class="checkbox-template m-r-xs">
                                Tag Payment Details
                            </label>
                        </div>
                    </div> -->
                    <div class="row grp_payment-p" id="grp_payment-p">
                        <div class="col-6">
                            <div class="form-group" id="payment_field">
                                <select style="height:42px;" type="text" name="f_payment" id="f_payment" class="form-control">
                                    <option value="">-- Select Payment Type --</option>
                                    <option
                                    <?php
                                        foreach ($payments as $payment) {
                                            ?>
                                                <option value="<?= $payment['id']; ?>"><?= $payment['description']; ?></option>
                                            <?php
                                        }
                                    ?>

                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group grp_payment_others" id="grp_payment_others">
                                <input type="text" class="form-control" name="f_payment_others" id="f_payment_others" placeholder="Enter payment type">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="f_payment_ref_num" id="f_payment_ref_num" placeholder="Enter reference number">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="f_payment_fee" id="f_payment_fee" placeholder="Enter payment amount">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <!-- <input type="text" class="form-control" name="f_shipping_notes" id="f_shipping_notes" placeholder="Enter notes (optional)"> -->
                                <textarea type="text" class="form-control" name="f_payment_notes" id="f_payment_notes" placeholder="Enter notes (optional)"></textarea>
                            </div>
                        </div>
                        <!-- <div class="col-12">
                            <div class="form-group">
                                <label>Proof of Payment</label><br/>
                                <input type="file" class="hidden" name="product_image" id="product_image">
                                <div id="product-placeholder">
                                    <p class="small">Click here to upload</p>
                                </div>
                                <img src="" id="product_preview" class="img-responsive">
                                <input type="text" class="hidden" name="current_product_url" id="current_product_url">
                                <button type="button" class="btn btn-primary btn-sm" id="change-product-image">Attach</button>
                            </div>
                        </div> -->
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn blue-grey" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" id="btn_tbl_confirm" class="btn btn-primary waves-effect waves-light btn_tbl_confirm" aria-label="Close">Confirm Settlement</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\reports\sale_settlement2.js');?>"></script>
<!-- end - load the footer here and some specific js -->
