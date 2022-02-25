<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="5" data-namecollapse="" data-labelname="Customers List">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Customers</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center ">
                            <h3 class="title">
                                Customers
                            </h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn">
                                <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Customer</label> -->
                                <input id="search_name" type="text" class="form-control material_josh form-control-sm search-input-text" placeholder="Customer">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Customer Type</label> -->
                                <select id="search_type" class="form-control material_josh form-control-sm search-input-text">
                                    <option value="">All Customer Type</option>
                                    <option value="2">Verified</option>
                                    <option value="3">Guest</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">City</label> -->
                                <select id="search_city" class="form-control material_josh form-control-sm search-input-text">
                                    <option value="">All Cities</option>
                                    <?php foreach ($get_cities as $city): ?>
                                        <option><?=$city['name']?></option>
                                    <?php endforeach ?>
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
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <form action="<?=base_url('customers/Main_customers/export_customers')?>" method="post" target="_blank">
                                    <input type="hidden" name="_search" id="_search">
                                    <button class="btn btn-primary btn-mobile-w-100 btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                </form>
                            </div>
                            <div class="col-12 col-md-auto px-1">
                                <!-- <?php if ($this->loginstate->get_access()['customer']['create']==1){?> -->
                                    <!-- <a href="<?= base_url('Shops/new/'.$token) ?>" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger d-flex align-items-center justify-content-center btn-block">Add</a> -->
                                <!-- <?php } ?> -->
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

                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>City</th>
                                <th>Order History</th>
                                <th>Account</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Modal-->
<div id="addCustomerModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md modal-md-custom">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Add Customer</h6>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <!-- 1st Row -->
                    <div class="form-group row">

                        <div class="col-md-6">
                            <label class="form-control-label">First Name <span class="asterisk"></span></label>
                            <input id="add_fname" type="text" class="form-control form-control-success">
                        </div>
                        <div class="col-md-6">
                            <label class="form-control-label">Last Name <span class="asterisk"></span></label>
                            <input id="add_lname" type="text" class="form-control form-control-success">
                        </div>
                    </div>
                        <!-- 2nd Row -->
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="form-control-label">Birthdate <span class="asterisk"></span></label>
                            <input id="add_birthdate" type="text" placeholder="MM/DD/YYYY" class="input-sm form-control search-input-select2 datepicker" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-control-label">Gender <span class="asterisk"></span></label>
                            <select id="add_gender" class="form-control material_josh form-control-sm search-input-text">
                                <option value="">--Select Gender--</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                    </div>
                    <!-- 3rd Row -->
                    <div class="form-group row">

                        <div class="col-md-6">
                            <label class="form-control-label">Mobile No. <span class="asterisk"></span></label>
                            <input id="add_mobile" type="number" class="form-control form-control-success">
                        </div>
                        <div class="col-md-6">
                            <label class="form-control-label">Email <span class="asterisk"></span></label>
                            <input id="add_email" type="text" class="form-control form-control-success">
                        </div>
                    </div>
                        <!-- 4th Row -->
                        <div class="form-group row">
                        <div class="col-md-12">
                            <label class="form-control-label">Address Line 1 <span class="asterisk"></span></label>
                            <input id="add_address1" type="text" class="form-control form-control-success">
                        </div>
                    </div>
                        <!-- 5th Row -->
                        <div class="form-group row">
                        <div class="col-md-12">
                            <label class="form-control-label">Address Line 2 </label>
                            <input id="add_address2" placeholder="(Optional)"  type="text" class="form-control form-control-success">
                        </div>
                    </div>
                        <!-- 6th Row -->
                        <div class="form-group row">
                        <div class="col-md-12">
                            <label class="form-control-label">City <span class="asterisk"></span></label>
                            <select id="add_city" class="form-control material_josh form-control-sm search-input-text">
                                <option value="">--Select City--</option>
                                <?php foreach ($get_cities as $city): ?>
                                    <option value="<?=$city['id']?>"><?=$city['name']?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                <button id="addBtnCustomer" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<!-- View History -->
<div class="modal fade" id = "history_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header secondary-bg">
        <h4 class="modal-title">Order History</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <div class="col-12 col-sm-4">
              <label class="">Customer Name</label>
          </div>
          <div class="col-12 col-sm-8">
              <label id="c_name"></label>
          </div>
          <div class="col-12 col-sm-4">
              <label class="">Total Amount</label>
          </div>
          <div class="col-12 col-sm-8">
              <label id="c_total_amount"></label>
          </div>
        </div>
          <table class="table table-bordered table-striped" id = "history_tbl">
            <thead>
              <th>Order ref#</th>
              <th>Referral Code</th>
              <th>Address</th>
              <th>Amount</th>
              <th>Shipping Fee</th>
              <th>Date ordered</th>
              <th>Date shipped</th>
              <th>Status</th>
            </thead>
          </table>
      </div>
      <div class="modal-footer text-right">
        <button class="btn btn-outline-secondary" data-dismiss = "modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Login History -->
<div class="modal fade" id = "login_history_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Login History</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!-- <div class="col-12 col-sm-4">
            <label class="">Customer Name</label>
        </div>
        <div class="col-12 col-sm-8">
            <label id="c_name"></label>
        </div> -->
        <table class="table table-bordered table-striped" id = "login_history_tbl" width = "100%">
          <colgroup>
              <col style="width:50%;">
              <col style="width:20%">
              <col style="width:30%">
          </colgroup>
          <thead>
            <th>Name</th>
            <th>Login type</th>
            <th>Date</th>
          </thead>
        </table>
      </div>
      <div class="modal-footer text-right">
        <button class="btn btn-outline-secondary" data-dismiss = "modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('includes/footer');?>
<script type="text/javascript" src="<?=base_url('assets/js/customers/customer_list.js');?>"></script>
