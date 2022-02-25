<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .datepicker{
    z-index: 9999 !important;
  }

  td, th {
    vertical-align: middle !important;
  }

  .req_msg{
    display:none !important;
  }

  .red-border{
    border: 1px solid red !important;
  }

  .dt-right{
    text-align: right;
  }
</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Wallet">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>">Orders</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Manual Order</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card" id = "main">
              <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Manual Order</h3>
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
                                <input type="text" placeholder="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                            </div>
                        </div>

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
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Status</label>
                                <select name="select_status" id = "select_status" class="form-control material_josh form-control-sm search-input-text enter_search">
                                  <option value="1" selected>All</option>
                                  <option value="2">On Process</option>
                                  <option value="3">Settled</option>
                                </select>
                            </div>
                        </div> -->
                    </div>
                    </form>
                    <!-- <div class="form-group text-right">
                      <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->

                </div>
                <div class="card-body table-body">
                  <div class="col-lg-auto col-md-8 table-search-container">
                        <div class="row no-gutters">
                            <form action="<?=base_url('wallet/Manual_order/export_list_table')?>" method="post" target="_blank" class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                                <input type="hidden" name="_search" id="_search">
                                <button class="w-100 h-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>
                            </form>
                            <div class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                              <?php if($this->loginstate->get_access()['manual_order']['create'] == 1):?>
                              <button class="w-100 btn btn-outline-danger btn_add" id="btn_addorder">Add</button>
                              <?php endif;?>
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
                          <?php if($this->loginstate->get_access()['manual_order']['create'] == 1):?>
                            <button class="btn-mobile-w-100 mx-0 btn btn-primary btn_add" id="btn_addorder">Add</button>
                          <?php endif;?>
                        </div>
                      </div> -->
                    </div>
                    <!-- end - record status is a default for every table -->

                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                          <!-- <colgroup>
                              <col style="width:10%;">
                              <col style="width:15%;">
                              <col style="width:8%;">
                              <col style="width:10%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:5%;">
                          </colgroup> -->
                          <thead>
                              <tr>
                                <th>Shopname</th>
                                <th>Ref #</th>
                                <th>Amount</th>
                                <th>Payment Type</th>
                                <th>Date Ordered</th>
                                <th>Date Shipped</th>
                              </tr>
                          </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id = "add_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Order Details</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-md-6 mb-2">
                <label for="Customer Name" class="form-control-label col-form-label-sm">Customer Name <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <input type="text" name = "customer" id = "customer" class="form-control rq" placeholder="Ex. John Doe">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Email Address" class="form-control-label col-form-label-sm">Email Address <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <input type="text" name = "email" id = "email" class="form-control rq">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Contact Number" class="form-control-label col-form-label-sm">Contact Number <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <input type="text" name = "contact_no" id = "contact_no" class="form-control rq contactNumber">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Select City" class="form-control-label col-form-label-sm">Select City <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <select name="city" id="city" class="form-control rq select2" data-citymuncode = "" data-provcode = "" data-regcode = "">
                  <option value="" data-citymuncode = "" data-provcode = "" data-regcode = "">Select City</option>
                  <?php if($cities->num_rows() > 0):?>
                    <?php foreach($cities->result_array() as $city):?>
                      <option value="<?=$city['city']?>" data-citymuncode = "<?=$city['citymunCode']?>" data-provcode = "<?=$city['provCode']?>" data-regcode = "<?=$city['regCode']?>"><?=$city['city']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label for="Date Ordered" class="form-control-label col-form-label-sm">Date Ordered <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <input type="text" name = "date_ordered" id = "date_ordered" class="form-control rq search-input-select1 date_from date_input_from" placeholder="<?=today_text();?>">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Date Shipped" class="form-control-label col-form-label-sm">Date Shipped <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <input type="text" name = "date_shipped" id = "date_shipped" class="form-control rq search-input-select1 date_to date_input_from" placeholder="<?=today_text();?>">
              </div>
              <div class="col-md-6 mb-2">
                <label for="Shop Name" class="form-control-label col-form-label-sm">Shop Name <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <select name="shop" id="shop" class="form-control rq select2">
                  <option value="">------</option>
                  <?php if($shops_w_wallets->num_rows() > 0):?>
                    <?php foreach($shops_w_wallets->result_array() as $shop):?>
                      <option value="<?=en_dec('en',$shop['id'])?>" data-shipping_fee = "<?=(float)$shop['shippingfee']?>"><?=$shop['shopname']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
              <di class="col-md-6 mb-2">
                <label for="Branch" class="form-control-label col-form-label-sm">Branch <small>(optional)</small></label>
                <select name="branches" id="branches" class="form-control select2" disabled>
                  <option value="">------</option>
                </select>
              </di>

              <div class="col-md-6 mb-2">
                <label for="Item" class="form-control-label col-form-label-sm">Item <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                <select name="products" id="products" data-price = "0" data-pname = "" class="form-control rq select2" disabled>
                  <option value="">------</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <div class="row">
                  <div class="col-md-6 mb-2">
                    <label for="Quantity" class="form-control-label col-form-label-sm">Quantity <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                    <input type="number" id = "quantity" name = "quantity" class="form-control rq text-right" min = 0 value = 0>
                  </div>
                  <div class="col-md-6 mb-2">
                    <label for="Shipping Fee" class="form-control-label col-form-label-sm">Shipping Fee <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                    <input type="number" name = "shipping" id = "shipping" class="form-control text-right" value = "0" min = 0>
                  </div>
                </div>
              </div>

              <div class="col-md-12 text-right mb-4">
                <?php if($this->loginstate->get_access()['manual_order']['create'] == 1):?>
                  <button class="btn btn-primary" id = "btn_add_order">Add to order</button>
                <?php endif;?>
              </div>
              <div class="col-12 mb-2">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "order_tbl">
                    <colgroup>
                        <!-- <col style="width:5%;"> -->
                        <col style="width:50%;">
                        <col style="width:10%;">
                        <col style="width:17%">
                        <col style="width:17%">
                        <col style="width:6%">
                    </colgroup>
                    <thead>
                      <!-- <th>No</th> -->
                      <th>Product Name</th>
                      <th>Quantity</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th></th>
                    </thead>
                    <tbody id = "order_cart">

                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="form-group row">
                  <div class="col-md-9 text-right mb-2">
                    <span class = "font-weight-bold">Shipping Fee</span class = "font-weight-bold">
                  </div>
                  <div class="col-md-3 text-right mb-2">
                    <span class = "font-weight-bold" id = "shipping_fee">0.00</span class = "font-weight-bold">
                  </div>
                  <div class="col-md-9 text-right">
                    <span class = "font-weight-bold">Total Amount</span class = "font-weight-bold">
                  </div>
                  <div class="col-md-3 text-right">
                    <span class = "font-weight-bold" id = "total_amount">0.00</span class = "font-weight-bold">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
            <?php if($this->loginstate->get_access()['manual_order']['create'] == 1):?>
            <button class="btn btn-sm btn-primary" id = "btn_save">Save</button>
            <?php endif;?>
          </div>
        </div>
      </div>
    </div>

</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\wallet\cleave.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom-cleave.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\jquery.alphanum.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\manual_order.js');?>"></script>
<!-- end - load the footer here and some specific js -->
