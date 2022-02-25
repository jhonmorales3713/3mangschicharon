<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .btn_reissue{
    padding: 5px !important;
    font-size: 11px !important;
  }
</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Products">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/vouchers_home/'.$token);?>">Vouchers</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Reissue Voucher Request</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card">
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="card-header mb-3">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Reissue Voucher Request</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>

                <div class="card-body table-body">
                  <div class="col-md-6 col-lg-auto table-search-container">
                      <div class="input-group mb-3">
                          <input type="text" class="form-control" id = "searchbox" placeholder="Order Ref # | Voucher Code" aria-label="Recipient's username" aria-describedby="basic-addon2">
                          <div class="input-group-append">
                              <a class="btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                              <button class="btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                          </div>
                      </div>
                  </div>
                    <!-- start - record status is a default for every table -->
                    <div class="col-md-3" hidden>
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
                    </div>
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                      <thead>
                        <tr>
                            <th>Shop</th>
                            <th>Customer</th>
                            <th>Order ref #</th>
                            <th>Voucher ref #</th>
                            <th>Voucher Code</th>
                            <th>Voucher amount</th>
                            <th>Date Processed</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>
                    </table>
                </div>
                <div class="card-footer text-right">
                  <button class="btn btn-primary btn-sm" id = "btn_request_reissue">Request reissue</button>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<!-- <script type="text/javascript" src="<?=base_url('assets/js/orders/orders.js');?>"></script> -->
<script type="text/javascript" src="<?=base_url('assets\js\vouchers\reissue_voucher_request.js');?>"></script>
<!-- end - load the footer here and some specific js -->
