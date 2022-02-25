<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<style>
    .required {
    color: red;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shipping Partners</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Shipping Partners</h3>
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
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shipping Code</label> -->
                                    <input type="text" name="_code" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Shipping Code" maxlength="5">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shipping Name</label> -->
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Shipping Name">
                                </div>
                            </div>
                            <!-- start - record status is a default for every table -->
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                        <option value="">All Records</option>
                                        <option value="1" selected>Enabled</option>
                                        <option value="2">Disabled</option>
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
                    <!-- <?php if ($this->loginstate->get_access()['shipping_partners']['create'] == 1){ ?>
                        <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary" id="action_add">Add</button>
                        </div>
                    <?php } ?> -->
                    <div class="col-lg-auto table-search-container">
                        <div class="row no-gutters">
                            <?php if ($this->loginstate->get_access()['shipping_partners']['create'] == 1){ ?>
                                <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger mb-3" id="action_add">Add</button>
                            <?php } ?>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('Main_settings/export_shipping_partner_list')?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <input type="hidden" name="_filter" id="_filter">
                                        <!-- <input type="hidden" name="_data" id="_data"> -->
                                        <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                    </form>  
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
                    <!-- end - record status is a default for every table -->
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th>Shipping Code</th>
                                    <th>Shipping Name</th>
                                    <th>Date Created</th>
                                    <th width="30">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal-->
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
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
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
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Shipping Partner</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id" class="form-control material_josh form-control-sm search-input-text">
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shipping Code<span class="required">*</span></label>
                    <input type="text" id="edit_code" class="form-control material_josh form-control-sm search-input-text" placeholder="Shipping Code" maxlength="5">
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shipping Name<span class="required">*</span></label>
                    <input type="text" id="edit_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Shipping Name">
                </div>

                <div class="form-group">
                    <input type="checkbox" class="form-control-input" id="edit_api_isset" name="edit_api_isset" value="1">
                    <label class="form-control-label" for="edit_api_isset">Set API Link</label>
                </div>

                <div class="form-group edit_apilink_div" style="display:none">
                    <label class="form-control-label col-form-label-sm">Development Environment Link</label>
                    <input type="text" id="edit_dev_api_url" class="form-control material_josh form-control-sm search-input-text" placeholder="URL... Ex: test.com/api/123">
                </div>

                <div class="form-group edit_apilink_div" style="display:none">
                    <label class="form-control-label col-form-label-sm">Testing Environment Link</label>
                    <input type="text" id="edit_test_api_url" class="form-control material_josh form-control-sm search-input-text" placeholder="URL... Ex: test.com/api/123">
                </div>

                <div class="form-group edit_apilink_div" style="display:none">
                    <label class="form-control-label col-form-label-sm">Production Environment Link</label>
                    <input type="text" id="edit_prod_api_url" class="form-control material_josh form-control-sm search-input-text" placeholder="URL... Ex: test.com/api/123">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update_modal_confirm_btn">Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Shipping Partner</h3>
            </div>
            <div class="modal-body">
            <input type="hidden" id="edit_id" class="form-control material_josh form-control-sm search-input-text">
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shipping Code<span class="required">*</span></label>
                    <input type="text" id="add_code" class="form-control material_josh form-control-sm search-input-text" placeholder="Shipping Code" maxlength="5">
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shipping Name<span class="required">*</span></label>
                    <input type="text" id="add_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Shipping Name">
                </div>

                <div class="form-group">
                    <input type="checkbox" class="form-control-input" id="add_api_isset" name="add_api_isset" value="1">
                    <label class="form-control-label" for="add_api_isset">Set API Link</label>
                </div>

                <div class="form-group add_apilink_div" style="display:none">
                    <label class="form-control-label col-form-label-sm">Development Environment Link</label>
                    <input type="text" id="add_dev_api_url" class="form-control material_josh form-control-sm search-input-text" placeholder="URL... Ex: test.com/api/123">
                </div>

                <div class="form-group add_apilink_div" style="display:none">
                    <label class="form-control-label col-form-label-sm">Testing Environment Link</label>
                    <input type="text" id="add_test_api_url" class="form-control material_josh form-control-sm search-input-text" placeholder="URL... Ex: test.com/api/123">
                </div>

                <div class="form-group add_apilink_div" style="display:none">
                    <label class="form-control-label col-form-label-sm">Production Environment Link</label>
                    <input type="text" id="add_prod_api_url" class="form-control material_josh form-control-sm search-input-text" placeholder="URL... Ex: test.com/api/123">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add_modal_confirm_btn">Add</button>
            </div>
        </div>
    </div>
</div>


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_shipping_partner.js');?>"></script>
<!-- end - load the footer here and some specific js -->

