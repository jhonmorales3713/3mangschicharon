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
            <li class="breadcrumb-item active">Product Main Categories</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Product Main Categories</h3>
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
                                    <!-- <label class="form-control-label col-form-label-sm">Category Code</label> -->
                                    <input type="text" name="_category" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Category Code">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Category Name</label> -->
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Category Name">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">On Menu</label> -->
                                    <select name="_onmenu" class="form-control material_josh form-control-sm search-input-text">
                                        <option value="" selected>-- Select On Menu --</option>
                                        <option value="1">Displayed</option>
                                        <option value="0">Not Displayed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Priority</label> -->
                                    <input type="text" name="_priority" class="form-control material_josh form-control-sm search-input-text enter_search allowdecimal" placeholder="Priority">
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
                    
                    <!-- <?php if ($this->loginstate->get_access()['category']['create'] == 1){ ?>
                        <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary" id="action_add">Add</button>
                        </div>
                    <?php } ?> -->
                    <div class="col-md-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php if ($this->loginstate->get_access()['products_main_category']['create'] == 1){ ?>
                                    <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger mb-3" id="action_add">Add</button>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('Main_settings/export_product_main_category_list')?>" method="post" target="_blank">
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
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Category Image</th>
                                <th>Category Code</th>
                                <th>Category Name</th>
                                <th>On Menu</th>
                                <th>Priority</th>
                                <th>Last Updated</th>
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
                <h3 class="modal-title" id="exampleModalLabel">Edit Product Category</h3>
            </div>

            <form id="update_record_form">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                      <div class="note">
                            Make sure to meet the minimum image size requirements for better image quality
                            <br>
                            <br>
                            1. Dimension: 250px x 250px<br>
                            2. File type: JPEG, PNG, JPG<br>
                            3. File Size: maximum 3mb 
                        </div>
                        <div class="category_img square" id="imgthumbnail-logo"></div>
                        <div class="img_preview_container_update square" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Image<span class="required">*</span></label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file_container_update" id="file_container_update">
                                <label class="custom-file-label" id="file_description_update">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Category Code<span class="required">*</span></label>
                        <input type="text" id="edit_code" name="edit_code" class="form-control material_josh form-control-sm search-input-text" placeholder="Category Code">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Category Name<span class="required">*</span></label>
                        <input type="text" id="edit_name" name="edit_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Category Name">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Category Icon<span class="required">*</span></label>
                        <input type="text" id="edit_icon" name="edit_icon" class="form-control material_josh form-control-sm search-input-text" placeholder="Category Icon">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">On Menu<span class="required">*</span></label>
                        <select id="edit_onmenu" name="edit_onmenu" class="form-control material_josh form-control-sm search-input-text">
                            <option value="">-- Select On Menu --</option>
                            <option value="1">Displayed</option>
                            <option value="2">Not Displayed</option>
                        </select>           
                    </div>  
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Priority<span class="required">*</span></label>
                        <input type="text" id="edit_priority" name="edit_priority" class="form-control material_josh form-control-sm search-input-text allowdecimal" placeholder="Priority">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Sub Categories<span class="required">*</span></label>
                        <select class="select2 form-control form-control-sm form-state taginput-field" id="edit_edit_subcategory" name="edit-entry-subcategory[]" multiple="multiple">
                            <?php select_sub_category_list($product_category) ?>
                        </select>
                    </div>
                    <input type="hidden" name="_edit_filename" class="form-control">
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary updateBtn" id="updateCategory" form="update_record_form">Update</button>
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Product Main Category</h3>
            </div>
            <form id="add_record_form">
                <div class="modal-body">
                    <div class="form-group">
                      <div class="note">
                            Make sure to meet the minimum image size requirements for better image quality
                            <br>
                            <br>
                            1. Dimension: 250px x 250px<br>
                            2. File type: JPEG, PNG, JPG<br>
                            3. File Size: maximum 3mb 
                        </div>
                        <div class="square" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/product-main-category.jpg') ?>" style="max-width: 100%;max-height: 100%; "></div>
                        <div class="img_preview_container square" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Image<span class="required">*</span></label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file_container" id="file_container">
                                <label class="custom-file-label" id="file_description">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Category Code<span class="required">*</span></label>
                        <input type="text" id="add_code" name="add_code" class="form-control material_josh form-control-sm search-input-text" placeholder="Category Code">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Category Name<span class="required">*</span></label>
                        <input type="text" id="add_name" name="add_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Category Name">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Category Icon<span class="required">*</span></label>
                        <input type="text" id="add_icon" name="add_icon" class="form-control material_josh form-control-sm search-input-text" placeholder="Category Icon">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">On Menu<span class="required">*</span></label>
                        <select id="add_onmenu" name="add_onmenu" class="form-control material_josh form-control-sm search-input-text">
                            <option value="">-- Select On Menu --</option>
                            <option value="1">Displayed</option>
                            <option value="2">Not Displayed</option>
                        </select>           
                    </div>  
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Priority<span class="required">*</span></label>
                        <input type="text" id="add_priority" name="add_priority" class="form-control material_josh form-control-sm search-input-text allowdecimal" placeholder="Priority">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Sub Categories<span class="required">*</span></label>
                        <select class="select2 form-control form-control-sm form-state taginput-field" id="entry_subcategory" name="entry-subcategory[]" multiple="multiple">
                            <?php select_sub_category_list($product_category) ?>
                        </select>
                    </div>
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary saveBtn" id="addCategory" form="add_record_form">Add</button>
                </div>
        </div>
    </div>
</div>


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_product_main_category.js');?>"></script>
<!-- end - load the footer here and some specific js -->

