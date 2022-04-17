<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products_home/Products');?>"><?=$active_page?></a></span>
    </div>
</div>
<div class="col-12">
    <section class="tables  ml-4">   
        <div class="container-fluid">
            <div class="card ">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center text-white">
                            Customer List
                        </div>
                        <div class="col d-flex justify-content-end align-items-center ">
                            <p class="border-search_hideshow mb-0"><a href="#" class="text-white" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
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
                                        <option value="2">For Verification</option>
                                        <option value="1">Verified</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">City</label> -->
                                    <select id="search_status" class="form-control material_josh form-control-sm search-input-text">
                                        <option value="">All Status</option>
                                        <option value="1">Active</option>
                                        <option value="2">Declined</option>
                                        <!-- <?php foreach ($get_cities as $city): ?>
                                            <option><?=$city['name']?></option>
                                        <?php endforeach ?> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 table-search-container">
                                <div class="row">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('customers/Main_customers/export_customers')?>" method="post" target="_blank">
                                            <input type="hidden" name="_search" id="_search">
                                            <button class="btn btn-primary btn-mobile-w-100 btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                        </form>
                                    </div>
                                    <div class="col-6 col-md-auto px-1">
                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" data-show="1">
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Account Type</th>
                                <th>Status</th>
                                <th>Date Created</th>
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
                            <label class="form-control-label">Account Status <span class="asterisk"></span></label>
                            <select id="add_city" class="form-control material_josh form-control-sm search-input-text">
                                <option value="">--Select Status--</option>
                                
                                <option value="0">Active</option>
                                <option value="1">Declined</option>
                                <!-- <?php foreach ($get_cities as $city): ?>
                                    <option value="<?=$city['id']?>"><?=$city['name']?></option>
                                <?php endforeach ?> -->
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
<!-- Change Status Modal -->
<div class="modal fade" id = "changestatus_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header secondary-bg">
        <h4 class="modal-title">Change Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group disable_confirmation" style="display:none;">
            Are you sure you want to enable this user?
        </div>
        <div class="form-group enable_confirmation" style="display:none;">
            Are you sure you want to disable this user?
        </div>
      </div>
      <div class="modal-footer text-right">
        <button class="btn btn-outline-secondary" data-dismiss = "modal">No</button>
        <button class="btn btn-success" id="btnchangestatus">Yes</button>
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
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/customers/customers.js');?>"></script>
