<style>
    a.custom-card:hover {
      color: inherit;
      background-color: black;
    }
</style>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Void Record</li>
        </ol>
    </div>
</div>

<section class="tables">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="">
                    <div class="col-lg-12 padding-0_mobile" style="padding-bottom: 30px;">
                        <div class="card-progress">
                            <br>
                            <div class="col-lg-12 padding-0_mobile">

                                <div class="step1" id="step1">
                                    <div class="form-group row">

                                        <!-- <div class="col-md-3"></div> -->
                                        <div class="col-md-6 offset-md-3 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h3 class="card-title">Void Record</h3>
                                                </div>
                                                <div class="p-4">
                                                    <div class="form-group">
                                                        <div class="alert alert-warning notif_changecustomer" id="notif_changecustomer" hidden>NOTE: Remove item added before select enable</div>
                                                        <small class="form-text">Select Void Record Type </small>
                                                        <select class="form-control select2 recordType" id="recordType" name="recordType">
                                                            <option value="">Select Void Record Type</option>
                                                            <option value="Order List">Order List</option>
                                                            <option value="Pre-Payment">Pre Payment</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group divorder" id="divorder" style="display:none;">
                                                        <small class="form-text">Reference Number </small>
                                                        <input type="" class="form-control form-control-success order_reference_num" name="order_reference_num" id="order_reference_num" title="Reference Number is the Identification Number of a Record to void.">
                                                    </div>
                                                    <div class="form-group div_prepayment" id="div_prepayment" style="display:none;">
                                                        <small class="form-text">Reference Number </small>
                                                        <input type="" class="form-control form-control-success order_reference_num" name="prepayment_tran_ref_num" id="prepayment_tran_ref_num" title="Reference Number is the Identification Number of a Record to void.">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 offset-md-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <button style="float: right;" id="btnNext" class="btn btn-primary BtnNext">Next </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="step2" style="display: none;">
                                    <div class="card">
                                        <div id="showInfo" style="font-size: 15px; margin-bottom: 0px;">
                                            <h6 class="secondary-bg px-4 py-3 white-text voidtype" id="voidtype"></h6>


                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <div class="form-group p-style">
                                                            <div class="row">
                                                                <div class="col-md-3">Reference No: </div>
                                                                <div class="col-md-9"><h4 class="text-uppercase" id="drno"></h4></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">Name: </div>
                                                                <div class="col-md-9"><h4 class="text-uppercase" id="name"></h4></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">ID No: </div>
                                                                <div class="col-md-9"><h4 class="text-uppercase" id="idno"></h4></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3">Address: </div>
                                                                <div class="col-md-9"><p id="address"></p></div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-3">Date: </div>
                                                                <div class="col-md-9"><p id="trandate"></p></div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-3">Classification: </div>
                                                                <div class="col-md-9"><p id="classification"></p></div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-3">Total Amount: </div>
                                                                <div class="col-md-9"><p id="totalamt"></p></div>
                                                            </div>

                                                            <!-- INSIDE SHOWINFO-->
                                                            <div class="row" id="divreason" style="display: none">
                                                                <div class="col-md-3">Ticket Details: </div>
                                                                <div class="col-md-9"><p id="t_details"></p></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button style="float: right;" id="btnVoid" class="btn btn-primary btnVoid"> Void Record</button>
                                    <button style="float: right; display: none" id="packageBtn" type="button" data-toggle="modal" data-target="#confirmApvModal" class="btn btn-success btnBack"> Void</button>
                                    <button style="float: right; margin-right:10px;" id="btnBack" class="btn blue-grey btnBack"> Back</button>
                                    <button style='float: right; display: none' id='btnConfirmVoid_additional' type="button" data-toggle="modal" data-target="#confirmApvModal_additional" class='btn btn-primary btnConfirmVoid_additional'> Void Record</button>
                                    <button style='float: right; display: none' id='btnConfirmVoid2' type="button" data-toggle="modal" data-target="#confirmApvModal2" class='btn btn-primary btnConfirmVoid2'> Void Record</button>
                                </div>

                                <div class="step3 card" style="display: none;">
                                    <div class="form-group" style="margin-top: 50px;">
                                        <!-- <center><h3>You have successfully created a new Sales Order!  <div id="showInfo" style="font-size: 15px;margin-bottom: 50px;"></div><span class="refNospan" style="color:red"></span></h3>
                                            <a href="<?=base_url('Main_sales/sales_order_form/'.$token);?>" class="btn blue-grey">  Add More Sales Order</a>
                                            <a href="<?=base_url('Main_sales/sales_summary/'.$token);?>" class="btn primary-bg"> Proceed to Transaction History</a>
                                        </center> -->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id = "prepayment_void_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Void Pre-Payment</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <div class="col-md-12 mb-3">
            <h4><i class="fa fa-info no-margin"></i>
            Prepayment Transaction Summary</h4>
            <input type="hidden" id = "prepayment_void_refnum">
          </div>
          <div class="col-md-4">
            <h5>Deposit Reference No.</h5>
          </div>
          <div class="col-md-8">
            <h5 id = "deposit_ref_num"></h5>
          </div>
          <div class="col-md-4">
            <h5>Transaction No.</h5>
          </div>
          <div class="col-md-8">
            <h5 id = "tran_ref_num"></h5>
          </div>
          <div class="col-md-4">
            <h5>Transaction Date</h5>
          </div>
          <div class="col-md-8">
            <h5 id = "tran_date"></h5>
          </div>
          <div class="col-md-4">
            <h5>Transaction Type</h5>
          </div>
          <div class="col-md-8">
            <h5 id = "tran_type"></h5>
            <input type="hidden" id = "log_typ">
          </div>
          <div class="col-md-4">
            <h5>Transaction Amount</h5>
          </div>
          <div class="col-md-8">
            <h5 id = "tran_amount"></h5>
          </div>
          <div class="col-12">
            <label for="Reason" class="form-control-label col-form-label-sm">Reason <span class="asterisk"></span></label>
            <textarea name="prepayment_reason" id="prepayment_reason" cols="30" rows="3" class="form-control rq" required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer text-right">
        <button class="btn blue-grey" data-dismiss = "modal">Close</button>
        <button class="btn btn-sm btn-primary" id = "btn_void_prepayment">Void</button>
      </div>
    </div>
  </div>
</div>


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_void_record.js');?>"></script>
<!-- end - load the footer here and some specific js -->
