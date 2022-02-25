<link rel="stylesheet" href="<?=base_url('assets/css/jquery.timepicker.min.css')?>">
<style>
    .required {
    color: red;
    }
    .timepicker {
        z-index: 1001 !important;
    }
    .modal-content {
        z-index: 1000 !important;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Menu Management"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings _home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Faqs</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Faqs</h3>
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
                                    <!-- <label class="form-control-label col-form-label-sm">Category Name</label> -->
                                    <!-- <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Role Name"> -->
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->
                </div>
                <div class="card-body table-body">
                    <div class="col-md-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php if ($this->loginstate->get_access()['faqs']['create'] == 1){ ?>
                                    <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary mb-3" id="action_add">Add</button>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('settings/Roles/export_roles_table')?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <input type="hidden" name="_filter" id="_filter">
                                        <!-- <input type="hidden" name="_data" id="_data"> -->
                                        <!-- <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp; -->
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
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th width="120">Title</th>
                                <th width="30">FAQs For</th>
                                <th width="120">Content</th>
                                <th width="30">Arrangement</th>
                                <th width="30">Status</th>
                                <th width="30">Actions</th>
                            </tr>
                        </thead>
                    </table>
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
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Role</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id" class="form-control material_josh form-control-sm search-input-text">

                <label class="form-control-label col-form-label-sm">Faqs For<span class="required">*</span></label>
                <select name="" id="edit_faqs_for" class="form-control material_josh form-control-sm search-input-text">
                  <option value="blank" hidden disabled selected>Select FAQs location</option>
                  <option value="Merchant">Merchant</option>
                  <option value="Customer">Customer</option>
                </select>

                <label class="form-control-label col-form-label-sm">Faqs Arrangement<span class="required">*</span></label>
                <input type="number" id="edit_faqs_arrangement" name="faqs_arrangement" class="form-control material_josh form-control-sm search-input-text" placeholder="Arrangement....">

                <label class="form-control-label col-form-label-sm">Faqs Title<span class="required">*</span></label>
                <input type="text" id="edit_faqs_title" name="faqs_title" class="form-control material_josh form-control-sm search-input-text" placeholder="Title.....">

                <label class="form-control-label col-form-label-sm">Faqs Content<span class="required">*</span></label>
                <textarea type="text" id="edit_faqs_content"  name="faqs_content" class="form-control material_josh form-control-sm search-input-text" placeholder="Arrangement"> </textarea>
               
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update_modal_confirm_btn">Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Content</h3>
            </div>
            <div class="modal-body">
              
                <div class="form-group">

                    <label class="form-control-label col-form-label-sm">Faqs For<span class="required">*</span></label>
                    <select name="" id="faqs_for" class="form-control material_josh form-control-sm search-input-text">
                      <option value="blank" hidden disabled selected>Select FAQs location</option>
                      <option value="Merchant">Merchant</option>
                      <option value="Customer">Customer</option>
                    </select>

                    <label class="form-control-label col-form-label-sm">Faqs Arrangement<span class="required">*</span></label>
                    <input type="number" id="faqs_arrangement" name="faqs_arrangement" class="form-control material_josh form-control-sm search-input-text" placeholder="Arrangement....">

                    <label class="form-control-label col-form-label-sm">Faqs Title<span class="required">*</span></label>
                    <input type="text" id="faqs_title" name="faqs_title" class="form-control material_josh form-control-sm search-input-text" placeholder="Title.....">

                    <label class="form-control-label col-form-label-sm">Faqs Content<span class="required">*</span></label>
                    <textarea type="text" id="faqs_content"  name="faqs_content" class="form-control material_josh form-control-sm search-input-text" placeholder="Arrangement"> </textarea>

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
<script src="<?= base_url('assets/js/ckeditor/ckeditor.js') ?>"></script>
<script src="<?=base_url('assets/js/jquery.timepicker.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_faqs.js');?>"></script>
<!-- end - load the footer here and some specific js -->

