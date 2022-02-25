<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .date_input{
    z-index: 9999 !important;
  }

  td, th {
    vertical-align: middle !important;
  }

  #btn_view_attachment{
    text-decoration: underline;
    color: #117a8b;
    /* border-color: #10707f; */
  }

  .active_pic{
    border: 1px solid #ef4131 !important;
  }

  .modal-lg{
    max-width: 1300px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Accounts" data-access = "<?=en_dec('en',$this->loginstate->get_access()['billing']['admin_view'])?>" data-ini = "<?=en_dec('en',ini())?>">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/accounts_home/'.$token);?>">Accounts</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Billing Merchant</li>
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
                                Billing Merchant
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
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                    <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=en_dec('en',$shop['id']);?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                    <select name="select_branch" id = "select_branch" class="form-control material_josh form-control-sm search-input-text enter_search" disabled>
                                        <option value="null">All Branch</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif;?>
                            <div class="col-md-6 col-lg-2">
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
                                    <div class="col-4 col-md-auto px-1">
                                      <form action="<?=base_url('accounts/Billing/export_billing_tbl')?>" class="col-4" method="post" target="_blank">
                                          <input type="hidden" name="_search" id="_search">
                                          <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">
                                              <span class="text-sm md:text-base">Export</span>
                                          </button>&nbsp;
                                      </form>
                                    </div>
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                      <input type="text" class="form-control" placeholder="Search Billcode..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="search_billcode" id="search_billcode" value = "<?=$billcode?>">
                                    </div>
                                    <div class="col-4 col-md-auto px-1">
                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-4 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block" id="btnSearch">
                                            <span class="text-sm md:text-base">Search</span>
                                        </button>
                                    </div>
                                    <!-- <div class="col-6 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block w-100" id="btn_manual_trigger">Manual Cron</button>
                                    </div> -->
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
                      <colgroup>
                          <col style="width:15%;">
                          <col style="width:15%;">
                          <col style="width:15%;">
                          <col style="width:20%;">
                          <col style="width:10%;">
                          <col style="width:10%;">
                      </colgroup>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Billing Code</th>
                                <th>Total Process Fee</th>
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
                <button class="btn btn-info wave-effect wave-light btn_back" style="padding: .7rem 1.6rem !important"><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="card">
                <div class="card-header">
                  <h4>Billing Breakdown</h4>
                </div>
                <div class="card-body">
                  <div class="col-12">
                      <table class="table wrap-btn-last-td table-striped table-hover table-bordered" id="table-item"  style="width:100%">
                        <thead>
                            <tr>
                                <th>Fulfillment Date</th>
                                <th>Order Ref #</th>
                                <th>Payment Ref #</th>
                                <th>Order Type</th>
                                <th>Process Fee</th>
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
                      <div class="row mt-5">
                          <table class="table table-striped table-inverse table-responsive">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Transaction Date</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_trandate"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Billing Number</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_billno" class="green-text"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Billing Code</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_billcode" class="green-text"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Total Processing Fee</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_processfee" class="green-text"></label></td>
                                    </tr>
                                    <?php if(ini() == "toktokmall"):?>
                                      <!-- <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Total Withholding Tax</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_total_whtax" class="green-text"></label></td>
                                      </tr>
                                      <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Processing Fee less Withholding Tax</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_processfee_less_whtax" class="green-text"></label></td>
                                      </tr> -->
                                    <?php endif;?>
                                    <!-- <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Refcom Total Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_refcom_totalamount" class="green-text"></label></td>
                                    </tr> -->
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Net Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_netamount" class="green-text"></label></td>
                                    </tr>
                                    <?php if(ini() == "toktokmall"):?>
                                      <!-- <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Net Amount w/ Withholding Tax</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_netamount_w_whtax" class="green-text"></label></td>
                                      </tr> -->
                                    <?php endif;?>
                                </tbody>
                          </table>
                      </div>
                      <div class="row">
                        <div class="hr col-12 mb-5" style = "border-top:1px solid gainsboro;">
                        </div>
                      </div>
                      <div class="row mb-3" id = "unsettled_div" style = "display:none;">
                        <table class="table table-striped table-inverse table-responsive">
                          <tbody>
                            <tr>
                              <td class="w-50"><label class="text-xs md:text-base">Date Paid</label></td>
                              <td class="w-50"><label class="text-xs md:text-base" id="unsettled_date"></label></td>
                            </tr>
                            <tr>
                              <td class="w-50"><label class="text-xs md:text-base">Amount Settled</label></td>
                              <td class="w-50"><label class="text-xs md:text-base" id="settled_amount"></label></td>
                            </tr>
                            <tr>
                              <td class="w-50"><label class="text-xs md:text-base">Remaining to be Settled</label></td>
                              <td class="w-50"><label class="text-xs md:text-base" id="unsettled_amount"></label></td>
                            </tr>
                            <tr>
                              <td class="w-50"><label class="text-xs md:text-base">Billing Status</label></td>
                              <td class="w-50"><label class="text-xs md:text-base" id="unsettled_status"><label class='badge badge-info'>Partially Settled</label></label></td>
                            </tr>
                            <tr>
                                <td class="w-50"><label class="text-xs md:text-base">Payment Type</label></td>
                                <td class="w-50"><label class="text-xs md:text-base" id="unsettled_paytype">Pre Payment</label></td>
                            </tr>
                            <tr>
                                <td class="w-50"><label class="text-xs md:text-base">Payment Reference Number</label></td>
                                <td class="w-50"><label class="text-xs md:text-base" id="unsettled_payref"></label></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="row grp_payment" id="grp_payment">
                          <table class="table table-striped table-inverse table-responsive">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Date Settled</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_paiddate"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Amount Settled</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_paidamount" class="green-text"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Payment Type</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_paytype"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Payment Reference Number</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_payref"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Remarks</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_payremarks"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Attachment</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_payattach" class="green-text"></label></td>
                                    </tr>

                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Billing Status</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_paystatus" class="green-text"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Billing Note</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_remarks"></label></td>
                                    </tr>
                                </tbody>
                          </table>
                      </div>
                      <div class="row">
                        <div class="hr col-12 mb-5" style = "border-top:1px solid gainsboro;">
                        </div>
                      </div>
                      <div class="row">
                          <table class="table table-striped table-inverse table-responsive">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Shop Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_syshop"></label></td>
                                    </tr>

                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Branch Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_branchname"></label></td>
                                    </tr>

                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Account No</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_accountno"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Account Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_accountname"></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Bank Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_bankname"></label></td>
                                    </tr>
                                </tbody>
                          </table>
                      </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="col-md-12 text-right">
                        <!-- <button type="button" class="btn blue-grey" data-dismiss="modal" aria-label="Close">Close</button> -->
                        <!-- <form method = "post" action = "<?=base_url('accounts/Billing/print_breakdown')?>" id = "print_form"> -->
                          <input type="hidden" id = "billing_id" name="billing_id" value="">
                        <!-- </form> -->
                        <?php if(ini() == "jcww"):?>
                          <button type="button" class="btn btn-primary waves-effect waves-light" id="btn-print" >Print</button>
                        <?php endif;?>
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
                      <th>Srp Price</th>
                      <th>Ratetype</th>
                      <th>Process Rate</th>
                      <th>Quantity</th>
                      <th>Process Fee</th>
                      <th>Total Process Fee</th>
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
            <button type="button text-xl" class="close" data-dismiss="modal">&times;</button>
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
                <div class="modal-header hidden">
                    <div class="col-md-12">
                        <h4 id="tm_header_ref" class="modal-title">Billing Settlement</h4>
                    </div>
                </div>
                <div class="modal-body py-4">
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

                        <div class="col-12 mb-2">
                          <small>Attachment (optional) | Allowed format (jpg, jpeg, png) | Max size: 2mb</small>
                          <input type="hidden" id = "shopcode" name = "shopcode" class = "form-control">
                          <input type="file" id = "billing_attachment" name = "billing_attachment[]" class="form-control" placeholder="Attachment" multiple>
                        </div>
                        <div class="col-12">
                          <div class="form-group row" id = "img-upload-preview">

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
                    <div class="row m-0 p-0 justify-content-around">
                        <button type="button" class="btn btn-light col-4" data-dismiss="modal" aria-label="Close"><span class="text-sm md:text-base">Cancel</span></button>
                        <button type="submit" id="btn_tbl_confirm col-7" class="btn btn-primary waves-effect waves-light btn_tbl_confirm" aria-label="Close"><span class="text-sm md:text-base">Confirm Settlement</span></button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- VIEW IMAGE MODAL -->
    <div class="modal fade" id = "view_image_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Attachment</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="img-thumbnail view_image mb-2" style = "height:350px;width:100%;">

            </div>
            <div class="form-group small-thumbnail row">

            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn btn-outline-secondary" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "delete_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header" style = "background-color:#fdba1c;color:#fff;">
            <h4 class="modal-title">Delete Billing</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id = "delete_form">
            <div class="modal-body">
              <div class="col-lg-12">
                  <p>Are you sure you want to delete this record ( <span class="info_desc"></span> )?</p>
                  <!-- <p></p> -->
                  <input type="hidden" id = "delete_id" name = "delete_id">
                  <input type="hidden" id = "delete_trandate" name = "delete_trandate">
                  <input type="hidden" id = "delete_shopid" name="delete_shopid">
                  <input type="hidden" id = "delete_branchid" name="delete_branchid">
                  <input type="hidden" id = "delete_billcode" name="delete_billcode">
                  <input type="hidden" id = "delete_payref" name="delete_payref">
                  <input type="hidden" id = "delete_unsettled_payref" name="delete_unsettled_payref">
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
              <button type = "submit" class="btn btn-sm btn-primary">Yes</button>
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
<script type="text/javascript" src="<?=base_url('assets\js\accounts\image_upload.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\accounts\billing_merchant.js');?>"></script>

<!-- end - load the footer here and some specific js -->
