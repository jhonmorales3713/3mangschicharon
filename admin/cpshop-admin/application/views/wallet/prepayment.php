<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .datepicker{
    z-index: 999 !important;
  }

  img{
    object-fit: 'contain'
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

  td, th {
    vertical-align: middle !important;
  }

  .nav-link.active{
    background-color: #28a745 !important;
  }

  .nav-link{
    font-size: 13px !important;
    font-weight: bold;
    pointer-events: none !important;
  }

  .btn-sm{
    font-size: 11px !important;
    padding: 10px 15px !important;
  }

</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Wallet">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/wallet_home/'.$token);?>">Wallet</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Pre Payment</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <!-- MAIN BODY -->
            <div class="card" id = "main_body">
              <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Pre Payment</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">

                        <div class="col-lg-6">
                            <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" placeholder="<?=today_text();?>" name="start" readonly/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" placeholder="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" id="date_to" name="end" readonly/>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="">All Shop</option>
                                    <?php foreach ($shops as $shop): ?>
                                        <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                <select name="select_branch" id = "select_branch" class="form-control material_josh form-control-sm search-input-text enter_search" disabled>
                                    <option value="">All Branch</option>
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
                  <div class="col-lg-auto col-md-8 table-search-container">
                        <div class="row no-gutters">
                            <form action="<?=base_url('wallet/Prepayment/export_prepayment_table')?>" method="post" target="_blank" class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                                <input type="hidden" name="_search" id="_search">
                                <button class="w-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>
                            </form>
                            <div class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                              <?php if ($this->loginstate->get_access()['prepayment']['create'] == 1){ ?>
                                  <button class="w-100 btn btn-outline-danger btn_add" id="btn_deposit">Add</button>
                              <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
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
                    <!-- start - record status is a default for every table -->
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
                            <?php if ($this->loginstate->get_access()['prepayment']['create'] == 1){ ?>
                                <button class="btn-mobile-w-100 mx-0 btn btn-primary btn_add" id="btn_deposit">Add</button>
                            <?php } ?>
                        </div>
                      </div> -->
                    </div>
                    <!-- end - record status is a default for every table -->

                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                      <thead>
                          <tr>
                              <th>Ref #</th>
                              <th>Shop</th>
                              <th>Branch</th>
                              <th>Date updated</th>
                              <th>Balance</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                    </table>
                </div>
            </div>
            <!-- SUB BODY -->
            <div id="sub_body" style = "display:none;">
              <div class="col-12 text-right mb-3">
                <button class="btn btn-info wave-effect wave-light btn_back_main"><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="card">
                <div class="card">
                  <div class="card-header">
                        <div class="row">
                            <div class="col d-flex align-items-center">
                                <h3 class="card-title mb-0" id = "logs-title"></h3>
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
                        <!-- <div class="form-group text-right">
                          <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                        </div> -->

                    </div>
                    <div class="card-body">
                      <div class="form-group row">
                        <div class="col-md-4">
                          <h4>Current Wallet Balance</h4>
                          <h1 id = "wallet_ballance"> >₱ 0.00</h1>
                          <!-- <hr> -->
                        </div>
                        <div class="col-md-4">
                          <h4>Total Sales</h4>
                          <h1 id = "wallet_sales"> >₱ 0.00</h1>
                          <!-- <hr> -->
                        </div>
                      </div>
                    </div>
                    <div class="card-body table-body">


                      <div class="col-lg-auto col-md-8 table-search-container">
                            <div class="row no-gutters">
                                <div class="col-12 col-md-auto">
                                    <div class="row no-gutters">
                                        <div class="col-4 col-md-auto px-1">
                                          <form id = "export_wallet_logs" method = "post" action = "<?=base_url('prepayment/export_logs')?>">
                                            <input type="hidden" id = "export_logs_shopid" name="export_logs_shopid" value="">
                                            <input type="hidden" id = "export_logs_branchid" name="export_logs_branchid" value="">
                                            <input type="hidden" id= "_search_logs" name="_search_logs" value="">
                                            <input type="hidden" id = "searchValue" name="searchValue" value="">
                                            <button type = "submit" class="btn btn-primary py-1 btn-block" data-shopid = "" data-branchid = "" type="button" id="btn_export_logs">Export</button>
                                          </form>
                                        </div>
                                        <div class="col-4 col-md-auto px-1">
                                            <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="reset_logs"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-4 col-md-auto px-1">
                                            <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch_logs">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped text-center" id = "logs_tbl">
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
        </div>
    </section>
    <!-- 3 STEP VERIFICATION MODAL -->
    <div class="modal fade" id = "add_modal_new">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header secondary-bg">
            <h4 class="modal-title">Deposit Prepayment</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-pills nav-justified mb-4" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link p-3 step_1 active" data-step = "1" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                  <i class="fa fa-check mr-1" style = "font-size:20px;color:#28a745 !important;display:none;"></i>Deposit Informations
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link p-3 step_2" data-step = "2" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">
                  <i class="fa fa-check mr-1" style = "font-size:20px;color:#28a745 !important;display:none;"></i>Confirmations
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link p-3 step_3" data-step = "3" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Authentication</a>
              </li>
            </ul>
            <div class="tab-content p-4" id="myTabContent">
              <!-- DEPOSIT INFORMATIONS -->
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <form id="deposit_form">
                  <div class="form-group row">
                    <div class="col-md-6 mb-2">
                      <label for="Shop">Shop <span class="asterisk"></span></label>
                      <select name="shop" id="shop" class="form-control rq" >
                        <option value="">--Select Shop--</option>
                        <?php if(count((array)$shops) > 0):?>
                          <?php foreach($shops as $shop):?>
                            <option value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label for="Branch">Branch</label>
                      <select name="branch" id="branch" class="form-control rq" disabled>
                        <option value="0">Main</option>
                      </select>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="Attachment">Payment Attachment <small>(jpg,jpeg,png) (optional)</small></label>
                      <input type="file" name = "attachment" id = "attachment" class="form-control">
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="Deposit type">Deposit type <span class="asterisk"></span></label>
                      <select name="type" id="type" class="form-control rq" >
                        <?php if($payments->num_rows() > 0):?>
                          <?php foreach($payments->result_array() as $pay):?>
                            <option value="<?=$pay['paycode']?>"><?=$pay['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                        <!-- <option value="cash">Cash</option> -->
                        <!-- <option value="check">Check</option> -->
                        <!-- <option value="online_banking">Online Banking</option> -->
                      </select>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="Deposit Ref No.">Deposit Ref No. <span class="asterisk"></span></label>
                      <input type="text" name = "deposit_ref_no" id = "deposit_ref_no" class="form-control rq" >
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="Amount">Amount <span class="asterisk"></span></label>
                      <input type="text" class="form-control text-right money-input rq" name = "amount" id = "amount" >
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="Remarks">Remarks <small>(Optional)</small></label>
                      <textarea name="remarks" id = "remarks" rows="3" cols="30" class = "form-control"></textarea>
                    </div>
                  </div>
                </form>
              </div>
              <!-- CONFIRMATIONS -->
              <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="Shopname" class="form-control-label col-form-label-sm">Shopname</label>
                    <input type="text" class="form-control" id="c_shopname" readonly>
                  </div>

                  <div class="col-md-6">
                    <label for="Shopname" class="form-control-label col-form-label-sm">Branchname</label>
                    <input type="text" class="form-control" id="c_branchname" readonly>
                  </div>

                  <div class="col-md-4">
                    <label for="Deposit Type" class="form-control-label col-form-label-sm">Deposit Type</label>
                    <input type="text" class="form-control" id="c_deposit_type" readonly>
                  </div>

                  <div class="col-md-4">
                    <label for="Deposit Refno" class="form-control-label col-form-label-sm">Deposit Refno</label>
                    <input type="text" class="form-control" id="c_deposit_refno" readonly>
                  </div>

                  <div class="col-md-4">
                    <label for="Amount" class="form-control-label col-form-label-sm">Amount</label>
                    <input type="text" class="form-control" id="c_amount" readonly>
                  </div>

                  <div class="col-md-12">
                    <label for="Remarks" class="form-control-label col-form-label-sm">Remarks</label>
                    <textarea name="" id="c_remarks" cols="30" rows="5" class="form-control" readonly></textarea>
                  </div>

                </div>
              </div>
              <!-- CREDENTIALS -->
              <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class="form-group row">
                  <div class="col-md-8 offset-md-2">
                    <h1>Authentication Required!</h1>
                    <small>Please input your password to deposit prepayment</small>
                  </div>
                  <div class="col-md-8 offset-md-2">
                    <label for="Password" class="form-control-label col-form-label-sm">Password</label>
                    <input type="password" class="form-control" id = "c_password">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
            <button class="btn btn-sm btn-primary" id = "btn-back-step" style = "display:none;">Back</button>
            <button class="btn btn-sm btn-primary" id = "btn-next-step">Next</button>
            <button class="btn btn-sm btn-primary" id = "btn-finish-step" style = "display:none;">Finish</button>
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
          </div>
        </div>
      </div>
    </div>
    <!-- VIEW MODAL -->
    <!-- <div class="modal fade" id = "wallet_view_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header secondary-bg">
            <h4 class="modal-title" id = "logs-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row mt-4 mb-3">
              <div class="col-12">
                <h4>Current Wallet Balance</h4>
                <h1 id = "wallet_ballance"> >₱ 0.00</h1>
                <hr>
              </div>
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "logs_tbl">
                    <thead>
                      <tr>
                        <th>Attachment</th>
                        <th>Transaction No</th>
                        <th>Transaction Date</th>
                        <th>Transaction Type</th>
                        <th>Transaction Amount</th>
                        <th>Wallet Amount</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-outline-secondary" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div> -->
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
            <a href = "#" class = "btn btn-sm btn-primary" id = "btn-download-image" download>Download</a>
            <button class="btn btn-sm btn-outline-secondary" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\wallet\jquery.steps.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\cleave.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom-cleave.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\prepayment.js');?>"></script>
<!-- end - load the footer here and some specific js -->
