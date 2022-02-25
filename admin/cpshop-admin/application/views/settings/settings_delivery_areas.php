<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Delivery Areas Setting"> 
	<div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Delivery Areas</li>
        </ol>
    </div>

    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                Delivery Areas
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
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="px-4 pt-3 pb-0 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Code</label>
                                <input type="text" name="_code" class="form-control material_josh form-control-sm search-input-text url enter_search" placeholder="Code">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Delivery Area</label>
                                <input type="text" name="_delivery_area" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Delivery Area">
                            </div>
                        </div> -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Date Created</label> -->
                                <div class="input-daterange input-group " id="datepicker">
                                	<input type="text" name="_date_from" class="form-control search-input-text" id="datefrom" placeholder="mm/dd/yyyy" readonly />
                                	<span class="datetotext input-group-addon mt-0">to</span>
                                	<input type="text" name="_date_to" class="form-control search-input-text" id="dateto" placeholder="mm/dd/yyyy" readonly />
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                <option value="">All Records</option>
                                <option value="1" selected>Enabled</option>
                                <option value="2">Disabled</option>
                            </select> 
                        </div>
                    </div>
                    </form>
                    <!-- <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->
                    
                </div>
                <div class="card-body table-body">
                    <div class="col-md-8 col-lg-auto table-search-container">
                        <div class="row flex-md-row-reverse no-gutters">
                            <!-- <?php if ($this->loginstate->get_access()['customer']['create']==1){?>
                            <div class="col-12 col-md-auto mb-3 mb-md-0">
                                <a href="<?= base_url('Shops/new/'.$token) ?>" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary d-flex align-items-center justify-content-center btn-block" style="height: 40px">Add</a>
                            </div>
                            <?php } ?> -->

                            <?php if ($this->loginstate->get_access()['delivery_areas']['create'] == 1){ ?>
                                <div class="col-12 col-md-auto mb-3 mb-md-0">
                                    <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary btnClickAddArea d-flex align-items-center justify-content-center btn-block" style="height: 40px">Add</button>
                                </div>
                            <?php } ?>
                            <div class="col-12 col-md-auto">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <a class="btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                        <button class="btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                    <!-- <?php if($this->loginstate->get_access()['products']['create'] == 1){ ?>
                                    <button class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary align-items-center d-none d-lg-flex" id="addBtn" style="height: 40px;">Add</button>
                                    <?php } ?> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- start - record status is a default for every table -->
                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <div class="row">
                                
                            </div>
                        </div>
                    </div> -->
                    <!-- end - record status is a default for every table -->
                    
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Delivery Area</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </section>

</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title text-white" id="exampleModalLabel">Add Delivery Area</h3>
            </div>
            <div class="modal-body">
                <form id="add_area_form">
                    <div class="form-group">
                        <label class="col-form-label">Code:</label>
                        <input type="text" name="_add_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Delivery Area:</label>
                        <input type="text" name="_add_area" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="add_area_form">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title text-white" id="exampleModalLabel">Edit Delivery Area</h3>
            </div>
            <div class="modal-body">
                <form id="edit_area_form">
                    <div class="form-group">
                        <label class="col-form-label">Code:</label>
                        <input type="text" name="_edit_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Delivery Area:</label>
                        <input type="text" name="_edit_area" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="edit_area_form">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Disable Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="disable_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <small>This action cannot be undone.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_delivery_areas.js');?>"></script>
<!-- end - load the footer here and some specific js -->