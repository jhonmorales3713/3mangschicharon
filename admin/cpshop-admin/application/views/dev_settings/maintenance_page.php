<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Developer Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Maintenance Page</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Maintenance Page</h3>
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
                                <!-- <label class="form-control-label col-form-label-sm">Name</label> -->
                                <input type="text" name="c_name" class="form-control material_josh form-control-sm search-input-text url enter_search" placeholder="Client Name">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Description</label> -->
                                <input type="text" name="c_initial" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Client Initials">
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
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Description</label>
                                <input type="text" name="_description" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Description">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Main Navigation</label>
                                <select name="_main_nav" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="">All</option>
                                    <?php foreach ($main_nav_categories as $c_row): ?>
                                        <option value="<?=$c_row['main_nav_id'];?>"><?=$c_row['main_nav_desc'];?></option>
                                    <?php endforeach ?>
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
                    <!-- <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                        <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary btnClickAddArea">Add</button>
                    </div> -->

                    
                    <!-- end - record status is a default for every table -->
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
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
                                <th>Company Name</th>
                                <th>Company Initial</th>
                                <th>ID Key</th>
                                <th width="30">Action</th>
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
                <h3 class="modal-title" id="exampleModalLabel">Update Content Navigation</h3>
            </div>
            <form class="form-horizontal personal-info-css" id="update_content_nav-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <input id="update_id" type="text" class="form-control form-control-success" name="update_id" hidden>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">URL <span class="asterisk" style="color:red"></span></label>
                                        <input id="update_url" type="text" class="form-control form-control-success" name="update_url">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">Name <span class="asterisk" style="color:red"></span></label>
                                        <input id="update_name" type="text" class="form-control form-control-success" name="update_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">Description <span class="asterisk" style="color:red"></span></label>
                                    <textarea id="update_description" type="text" class="form-control form-control-success" name="update_description"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">Main Navigation <span class="asterisk" style="color:red"></span></label>
                                        <select name="update_main_nav_category" id="update_main_nav_category" class="form-control material_josh form-control-sm">
                                            <option disabled value="" selected>Select Category</option>
                                            <?php foreach($main_nav_categories as $category):?>
                                                <option value="<?=$category['main_nav_id']?>"><?=$category["main_nav_desc"]?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="update_modal_confirm_btn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Client</h3>
            </div>
            <form class="form-horizontal personal-info-css" id="add_content_nav-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">URL <span class="asterisk" style="color:red"></span></label>
                                        <input id="add_url" type="text" class="form-control form-control-success" name="add_url">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">Name <span class="asterisk" style="color:red"></span></label>
                                        <input id="add_name" type="text" class="form-control form-control-success" name="add_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">Description <span class="asterisk" style="color:red"></span></label>
                                    <textarea id="add_description" type="text" class="form-control form-control-success" name="add_description"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-control-label col-form-label-sm">Main Navigation <span class="asterisk" style="color:red"></span></label>
                                        <select name="add_main_nav_category" id="add_main_nav_category" class="form-control material_josh form-control-sm">
                                            <option disabled value="" selected>Select Category</option>
                                            <?php foreach($main_nav_categories as $category):?>
                                                <option value="<?=$category['main_nav_id']?>"><?=$category["main_nav_desc"]?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="add_modal_confirm_btn">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<input type="text" style="display:none;" id="token" value="<?=$token?>">

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

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Message:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div


<!-- end - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/maintenance_page.js');?>"></script> 
<!-- end - load the footer here and some specific js -->

