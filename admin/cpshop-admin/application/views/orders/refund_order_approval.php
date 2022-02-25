<style>
  table{
      width: 100% !important;
  }

  td, th {
    vertical-align: middle !important;
  }
  
  .align-right{
    text-align: right;
  }

  .flex-right {
    display:flex;
    justify-content: flex-end;   
    padding-bottom: 20px; 
  }

.borderless td, .borderless th {
    border: none;
}

/* responsiveness of datepicker */
@media screen and (max-width: 450px) {
      #datepicker{
          height: max-content !important;
      }
      .f_s1{
          display: inline !important;
      }
      .f_s2{
          display: none !important;
      }
      .f_i{
          display: none !important;
      }
      #datepicker2{
          display: block !important;
      }
  }
</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Refund Order"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>">Orders</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Refund Order Approval</li>
        </ol>
    </div>

    <input type="hidden" name="token" id="token" value="<?=$token?>">
    <button id="approve_mdl_btn" data-toggle="modal" data-target="#approve_modal"></button>
    <button id="reject_mdl_btn" data-toggle="modal" data-target="#reject_modal"></button>

    <section class="tables p-0">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Refund Order Approval</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="refnum_search" id="refnum_search" aria-describedby="helpId" placeholder="Reference Number">
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6 pb-3">
                                <div class="input-daterange input-group" id="datepicker">
                                    <span class="input-group-addon f_s1" style="background-color:#fff; display:none;">From</span>
                                    <input type="text" class="input-sm form-control search-input-select1 date_from datetimepicker" style="z-index: 2 !important;" id="date_from" value="<?=(isset($setDate['fromdate'])) ? $setDate['fromdate']:''?>" name="start" readonly/>
                                    <span class="input-group-addon f_s2" style="background-color: #fff;">&nbsp;To &nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker f_i" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 pb-3" id="datepicker2" style = "display:none;">
                                <div class="input-daterange input-group">
                                    <span class="input-group-addon" style="background-color: #fff;">To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker" style="z-index: 2 !important;" id="date_to_m" name="end" readonly/>
                                </div>
                            </div>
                            <div class="form-group hidden">
                                <select class="form-control" name="refstatus" id="refstatus">
                                    <option value="0" selected>For Review</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-6 col-lg-auto table-search-container">
                        <div class="flex-right">
                            <form action="<?=base_url('orders/Refund_order/export_refund_orders_approval')?>" method="post" target="_blank">
                                <input type="hidden" name="_search" id="_search">
                                <input type="hidden" name="_filter" id="_filter">
                                <!-- <input type="hidden" name="_data" id="_data"> -->
                                <button class="btn btn-primary btnExport h-100" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                            </form>  
                            <button class="btn btn-primary btnSearch" id="btnSearch">Search</button>
                        </div>
                    </div>
                    <table id="table-grid" class="table table-striped table-hover table-bordered table-responsive">
                        <thead class="thead-inverse">
                            <tr>
                                <th>Date Created</th>
                                <th>Date Reviewed</th>
                                <th>Reference Num.</th>
                                <th>Amount</th>
                                <th>Refund Mode</th>
                                <th>Account Number</th>
                                <th>Reasons</th>
                                <th>Reviewer</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                    </table>
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
<script type="text/javascript" src="<?=base_url('assets/js/orders/refund_order_approval.js');?>"></script>