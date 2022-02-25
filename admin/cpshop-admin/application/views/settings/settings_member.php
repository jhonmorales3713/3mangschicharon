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
            <li class="breadcrumb-item active">Members</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">
                                Members
                            </h3>
                        </div>
                        <div class="col">
                            <p class="border-search_hideshow mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
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
                                    <!-- <label class="form-control-label col-form-label-sm">Shop</label> -->
                                    <select name="_shop" id="_shop" class="form-control material_josh form-control-sm search-input-text">
                                        <option value="" selected>-- Select Shop --</option>
                                        <option value="0"><?=get_company_name();?>(Admin)</option>
                                        <?php foreach($shop_options as $shop_option){ ?>
                                            <option value="<?= $shop_option['id'] ?>"><?= $shop_option['shopname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shop Branch</label> -->
                                    <select name="_shopbranch" id="search_shopbranch" class="form-control material_josh form-control-sm search-input-text">
                                        <option value="" selected hidden>-- Select Shop Branch --</option>
                                        <option value="0" selected>-- Main --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Name</label> -->
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Name">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Email</label> -->
                                    <input type="text" name="_email" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Mobile Number</label> -->
                                    <input type="text" name="_mobile" class="form-control material_josh form-control-sm search-input-text enter_search allownumber" placeholder="Mobile Number">
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
                    <!-- <?php if ($this->loginstate->get_access()['members']['create'] == 1){ ?>
                        <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary" id="action_add">Add</button>
                        </div>
                    <?php } ?> -->

                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php if ($this->loginstate->get_access()['members']['create'] == 1){ ?>
                                    <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary d-flex align-items-center justify-content-center btn-block" id="action_add" style="height: 40px">Add</button>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('Main_settings/export_member_list')?>" method="post" target="_blank">
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
                                    <th>Avatar</th>
                                    <th>Shop Name</th>
                                    <th>Shop Branch</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
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
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Member</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id" class="form-control material_josh form-control-sm search-input-text">
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shop<span class="required">*</span></label>
                    <select id="edit_shop" class="form-control material_josh form-control-sm search-input-text">
                        <option value="" selected>-- Select Shop --</option>
                        <option value="0"><?=get_company_name();?>(Admin)</option>
                        <?php foreach($shop_options as $shop_option){ ?>
                            <option value="<?= $shop_option['id'] ?>"><?= $shop_option['shopname'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shop Branch<span class="required">*</span></label>
                    <select id="edit_shopbranch" class="form-control material_josh form-control-sm search-input-text">
                        <option value="0" selected hidden>-- Select Shop Branch --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">First Name<span class="required">*</span></label>
                    <input type="text" id="edit_fname" class="form-control material_josh form-control-sm search-input-text" placeholder="First Name">
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Middle Name / Initial</label>
                    <input type="text" id="edit_mname" class="form-control material_josh form-control-sm search-input-text" placeholder="Middle Name / Initial">
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Last Name<span class="required">*</span></label>
                    <input type="text" id="edit_lname" class="form-control material_josh form-control-sm search-input-text" placeholder="Last Name">
                </div>
                <!-- <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Email<span class="required">*</span></label>
                    <input type="text" id="edit_email" class="form-control material_josh form-control-sm search-input-text" placeholder="Email">
                </div> -->
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Mobile Number<span class="required">*</span></label>
                    <input type="text" id="edit_mobile" class="form-control material_josh form-control-sm search-input-text allownumber" placeholder="Mobile Number">
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
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title text-white   " id="exampleModalLabel">Add Member</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shop<span class="required">*</span></label>
                    <select id="add_shop" class="form-control material_josh form-control-sm search-input-text">
                        <option value="" selected>-- Select Shop --</option>
                        <option value="0"><?=get_company_name();?>(Admin)</option>
                        <?php foreach($shop_options as $shop_option){ ?>
                            <option value="<?= $shop_option['id'] ?>"><?= $shop_option['shopname'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Shop Branch<span class="required">*</span></label>
                    <select id="add_shopbranch" class="form-control material_josh form-control-sm search-input-text">
                        <option value="0" selected hidden>-- Select Shop Branch --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">User<span class="required">*</span></label>
                    <select id="add_user" class="form-control material_josh form-control-sm search-input-text"></select>
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">First Name<span class="required">*</span></label>
                    <input type="text" id="add_fname" class="form-control material_josh form-control-sm search-input-text" placeholder="First Name">
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Middle Name / Initial</label>
                    <input type="text" id="add_mname" class="form-control material_josh form-control-sm search-input-text" placeholder="Middle Name / Initial">
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Last Name<span class="required">*</span></label>
                    <input type="text" id="add_lname" class="form-control material_josh form-control-sm search-input-text" placeholder="Last Name">
                </div>
                <!-- <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Email<span class="required">*</span></label>
                    <input type="text" id="add_email" class="form-control material_josh form-control-sm search-input-text" placeholder="Email">
                </div> -->
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Mobile Number<span class="required">*</span></label>
                    <input type="text" id="add_mobile" class="form-control material_josh form-control-sm search-input-text allownumber" placeholder="Mobile Number">
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
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_member.js');?>"></script>
<!-- end - load the footer here and some specific js -->

