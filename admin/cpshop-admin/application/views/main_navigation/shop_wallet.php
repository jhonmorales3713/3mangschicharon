<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .datepicker{
    z-index: 999 !important;
  }

  td, th {
    vertical-align: middle !important;
  }

  .time_img:hover{
    cursor: pointer;
    border: 1px solid #72716f ;
  }

  #wallet_ballance{
    font-size: 30px !important;
  }

  #wallet_sales{
    font-size: 30px !important;
  }

  .btn{
    height: 35px;
    padding: 10px 20px !important;
    font-size: 12px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="11" data-namecollapse="" data-labelname="Products">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token);?>">Home</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Wallet</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card">
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0" id = "logs-title"><?=$this->session->shopname?></h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <!-- <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> -->
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-3" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">

                        <div class="col-lg-6">
                            <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="logsdate_from" placeholder="<?=today_text();?>" name="start" readonly/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" placeholder="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" id="logsdate_to" name="end" readonly/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <input type="text" placeholder="Search" id = "plogs_search" name = "plogs_search" class="form-control">
                        </div>
                    </div>
                    </form>
                </div>
                <div class="card-body">
                    <!-- start - record status is a default for every table -->
                    <div class="form-group row">
                      <div class="col-md-4">
                        <h4>Wallet Balances</h4>
                        <h1 id = "wallet_ballance">₱ <?=number_format($balance,2)?></h1>
                        <input type="hidden" id = "shopid" value="<?=$this->session->sys_shop?>">
                        <input type="hidden" id = "sid" value = "<?=en_dec('en',$this->session->sys_shop)?>">
                        <input type="hidden" id = "bid" value = "<?=(isset($this->session->branchid) && !empty($this->session->branchid)) ? en_dec('en',$this->session->branchid) : 0 ?>">
                      </div>
                      <div class="col-md-4">
                        <h4>Total Sales</h4>
                        <h1 id = "wallet_sales">₱ <?=number_format($total_sales,2)?></h1>
                      </div>

                      <!-- <hr> -->
                    </div>
                    <!-- end - record status is a default for every table -->
                    <div class="col-12 col-md-auto float-right px-0">
                        <div class="row no-gutters">
                            <div class="col-4 col-md-auto px-1">
                              <form id = "export_wallet_logs" method = "post" action = "<?=base_url('prepayment/export_logs')?>">
                                <input type="hidden" id = "export_logs_shopid" name="export_logs_shopid" value="<?=en_dec('en',$this->session->sys_shop)?>">
                                <input type="hidden" id = "export_logs_branchid" name="export_logs_branchid" value="<?=en_dec('en',$this->session->branchid)?>">
                                <input type="hidden" id= "_search_logs" name="_search_logs" value="">
                                <input type="hidden" id = "searchValue" name="searchValue" value="">
                                <button type = "submit" class="btn btn-primary py-1 btn-block" data-shopid = "" data-branchid = "" type="button" id="btn_export_logs">Export</button>
                              </form>
                            </div>
                            <div class="col-4 col-md-auto px-1">
                                <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="reset_logs"
                                  data-shopid = "<?=en_dec('en',$this->session->sys_shop)?>"
                                  data-branchid = "<?=en_dec('en',$this->session->branchid)?>"
                                  data-refnum = "<?=$refnum?>"
                                >
                                  <i class="fa fa-refresh" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="col-4 col-md-auto px-1">
                                <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch_logs">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered text-center" id="logs_tbl"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                          <!-- <colgroup>
                              <col style="width:25%;">
                              <col style="width:25%;">
                              <col style="width:25%;">
                              <col style="width:25%;">
                          </colgroup> -->
                          <thead>
                              <tr>
                                <th>Attachment</th>
                                <th>Deposit Refno</th>
                                <th>Deposit Type</th>
                                <th>Transaction No</th>
                                <th>Transaction Date</th>
                                <th width = "180">Transaction Type</th>
                                <th width = "250">Remarks</th>
                                <th>Transaction Amount</th>
                                <th>Wallet Amount</th>
                              </tr>
                          </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
        <div class="img-thumbnail view_image" style = "height:350px;width:100%;">

        </div>
      </div>
      <div class="modal-footer text-right">
        <!-- <button class="btn btn-sm btn-primary">Save</button> -->
        <button class="btn btn-outline-secondary" data-dismiss = "modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\wallet\shop_wallet.js');?>"></script>
<!-- end - load the footer here and some specific js -->
