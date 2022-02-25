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

  .btn-guest{
    border:2px solid #ef4131;
    color: #ef4131;
    background-color: #fff;
    font-weight: bold;
    width:100% !important;
  }

  .btn-reseller{
    border: 2px solid #b2c73e;
    background-color: #b2c73e;
    color: #fff;
    width:100% !important;
  }

  #no_of_stocks{
    background-color: #fff !important;
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
            <!-- MAIN -->
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

                        <div class="col-md-3 mb-3">
                           <!-- <label class="form-control-label col-form-label-sm">Location</label> -->
                           <select class="form-control" id="select_location">
                               <option value="all">All Locations</option>
                               <option value="address">Address</option>
                               <option value="region">Region</option>
                               <option value="province">Province</option>
                               <option value="citymun">City/Municipality</option>
                           </select>
                        </div>

                        <div class="col-lg-3 addressdiv" style="display:none;">
                            <!-- <label class="form-control-label col-form-label-sm">Address</label> -->
                            <input type="text" class="form-control" id="address" placeholder="Address">
                        </div>


                        <div class="col-lg-3 regiondiv" style="display:none;">
                            <!-- <label class="form-control-label col-form-label-sm">Region</label> -->
                            <select class="form-control select2" id="regCode">
                                <?php foreach($regions as $region){?>
                                    <option value="<?=$region['regCode']?>"><?=$region['regDesc']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-3 provincediv" style="display:none;">
                            <!-- <label class="form-control-label col-form-label-sm">Province</label> -->
                            <select class="form-control select2" id="provCode">
                                <?php foreach($provinces as $province){?>
                                    <option value="<?=$province['provCode']?>"><?=$province['provDesc']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-3 citymundiv" style="display:none;">
                            <!-- <label class="form-control-label col-form-label-sm">City/Municipality</label> -->
                            <select class="form-control select2" id="citymunCode">
                                <?php foreach($citymuns as $citymun){?>
                                    <option value="<?=$citymun['citymunCode']?>"><?=$citymun['citymunName']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search" style = "display:<?=($this->loginstate->get_access()['overall_access'] == 0) ? 'none': '' ?>">
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
                <div class="card-body table-body" >
                  <div class="col-lg-auto col-md-8 table-search-container">
                        <!-- <div class="row no-gutters">
                            <form action="<?=base_url('wallet/Manual_order/export_list_table')?>" method="post" target="_blank" class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                                <input type="hidden" name="_search" id="_search">
                                <button class="btn btn-outline-danger btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                            </form>
                            <div class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                              <?php if($this->loginstate->get_access()['manual_order']['create'] == 1):?>
                              <button class="w-100 btn btn-outline-danger btn_add" id="btn_addorder">Add</button>
                              <?php endif;?>
                            </div>
                        </div> -->
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

                    <div class="card-body table-body">
                      <div class="col-md-auto table-search-container">
                          <div class="row no-gutters">
                              <div class="col-12 col-md-auto px-1 mb-3">
                                  <?php if($this->loginstate->get_access()['manualorder_list']['create'] == 1):?>
                                    <button class="w-100 btn btn-outline-danger btn_add" id="btn_addorder">Add</button>
                                  <?php endif;?>
                                </div>
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                      <form action="<?=base_url('orders/Manual_order/export_list_table')?>" method="post" target="_blank" class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                                          <input type="hidden" name="_search" id="_search">
                                          <button class="btn-mobile-w-100 btn btn-outline-danger btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                                      </form>
                                    </div>
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                      <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="_name" id="_name">
                                    </div>

                                    <div class="col-6 col-md-auto px-1 mb-3">
                                      <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                      <button class="btn-mobile-w-100 btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                              </div>
                          </div>
                      </div>
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
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
                                  <th>Date</th>
                                  <th width="150">Order</th>
                                  <th>Customer</th>
                                  <th>Amount</th>
                                  <th>Voucher</th>
                                  <th>Shipping</th>
                                  <th>Total</th>
                                  <th>Payment</th>
                                  <th>Status</th>
                                  <th>Shop</th>
                                  <th>Branch</th>
                                  <th>City</th>
                                  <th width="30">Action</th>
                              </tr>
                          </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- SUB -->
            <div id="sub" style = "display:none;">
              <div class="col-12 text-right mb-3">
                <button class="btn btn-info wave-effect wave-light btn_back"><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title mb-0">Add Manual Order</h3>
                </div>
                <div class="card-body p-5">
                  <!-- AUTHENTICATE WRAPPER -->
                  <div class="form-group row mb-3" id = "authenticate-wrapper">
                    <div class="col-md-6 offset-md-3 row">
                      <div class="col-md-12 mb-3">
                        <label for="Reseller Id" class="form-control-label col-form-label-sm">Online Franchise ID Number <span class="asterisk"></span></label>
                        <input type="text" id = "reseller_id" name = "reseller_id" class="form-control">
                      </div>
                      <div class="col-lg-6 mb-3">
                        <button class="btn btn-primary form-control" id = "btn-validate-reseller" style = "width:100% !important;">Validate</button>
                      </div>
                      <div class="col-lg-6 mb-3">
                        <button class="btn btn-guest form-control" id = "btn-guest">Continue as Guest</button>
                      </div>
                    </div>
                  </div>
                  <!-- ORDER WRAPPER -->
                  <div class="form-group row" id = "order-wrapper" style = "display:none;">
                    <div class="col-6 mb-2">
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
                      <!-- <label for="Date Ordered" class="form-control-label col-form-label-sm">Date Ordered <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label> -->
                      <input type="hidden" name = "date_ordered" id = "date_ordered" class="form-control rq" value="<?=today();?>" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                      <!-- <label for="Date Shipped" class="form-control-label col-form-label-sm">Date Shipped <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label> -->
                      <input type="hidden" name = "date_shipped" id = "date_shipped" class="form-control rq" value="<?=today();?>" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                      <label for="Shop Name" class="form-control-label col-form-label-sm">Shop Name <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                      <select name="shop" id="shop" class="form-control rq select2">
                        <option value="">------</option>

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
                      <select name="products" id="products" data-price = "0" data-pname = "" data-nos = "" data-csi = "" class="form-control rq select2" disabled>
                        <option value="">------</option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-2">
                      <label for="No. of Stocks" class="form-control-label col-form-label-sm">No. of Stocks</label>
                      <input type="text" id = "no_of_stocks" name = "no_of_stocks" class="form-control text-right" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                      <label for="Quantity" class="form-control-label col-form-label-sm">Quantity <small class="text-danger req_msg">Required</small> <span class="asterisk"></span></label>
                      <input type="text" id = "quantity" name = "quantity" class="form-control rq text-right number-input-4" min = 0 value = 0>
                    </div>
                    <div class="col-md-6 mb-2">
                      <label for="Shipping Fee" class="form-control-label col-form-label-sm">Shipping Fee </label>
                      <input type="text" name = "shipping" id = "shipping" class="form-control text-right money-input" value = "0" min = 0>
                    </div>

                    <div class="col-md-12 text-right mb-4">
                      <?php if($this->loginstate->get_access()['manualorder_list']['create'] == 1):?>
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
                <div class="card-footer text-right order-footer">
                  <button class="btn blue-grey btn-back-authenticate">Back</button>
                  <?php if($this->loginstate->get_access()['manualorder_list']['create'] == 1):?>
                  <button class="btn btn-sm btn-primary" id = "btn_save">Save</button>
                  <?php endif;?>
                </div>
              </div>
            </div>
        </div>
    </section>

</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\wallet\cleave.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom-cleave.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\jquery.alphanum.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\orders\manual_order.js');?>"></script>
<!-- end - load the footer here and some specific js -->
