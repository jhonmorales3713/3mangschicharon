<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="12" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_dev_settings/company_manager/'.$token);?>">Company Manager</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?=$company_details->company_name;?> Users</li>
        </ol>
    </div>
    
    <section class="tables">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header d-flex align-items-center">

                                <div class="col-md-12">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Name</label>
                                                <input type="text" class="form-control form-control-sm search-input-text txt_search_name" placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Position</label>
                                                <select name="search_position" id="search_position" class="form-control form-control-sm">
                                                    <option value="" hidden selected>Select Position</option>
                                                    <?php
                                                        foreach($all_position as $position)
                                                        {
                                                            echo "<option value=".$position['position_id'].">".$position['position_name']."</option>";
                                                        } 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" id="company_code" value="<?=$company_details->company_code;?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#userModal" class="btn btn-primary btn-modal" id="btnClickAddSystemUser">Add User</button>
                            <button class="btn btn-primary btnSearch" style="right:120px; position: absolute; top:20px;">Search</button>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th width="220">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add and Update user modal -->
    <div id="userModal" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="userModalLabel" class="modal-title"></h4>
                </div>
                <form id="user_form" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">First Name <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="user_idno" type="hidden" name="user_idno">
                                            <input type="hidden" name="user_company_code" id="user_company_code" value="<?=$company_details->company_code;?>">
                                            <input id="user_fname" type="text" class="form-control form-control-success" name="user_fname">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Middle Name </label>
                                        <div class="col-md-9">
                                            <input id="user_mname" type="text" class="form-control form-control-success" name="user_mname">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Last Name <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="user_lname" type="text" class="form-control form-control-success" name="user_lname">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Username <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="user_username" type="text" class="form-control form-control-success" name="user_username">
                                        </div>
                                    </div>
                                    <div class="form-group row" id="password_div">
                                        <label class="col-md-3 form-control-label">Password <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="user_password" type="password" class="form-control form-control-success" name="user_password">
                                        </div>
                                    </div>
                                    <div class="form-group row" id="new_password_div">
                                        <label class="col-md-3 form-control-label">New Password <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="new_user_password" type="password" class="form-control form-control-success" name="new_user_password">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Position <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <select name="user_position" id="user_position" class="form-control material_josh form-control-sm">
                                                <option value="" hidden selected>Select Position</option>
                                                <?php
                                                    foreach($all_position as $position)
                                                    {
                                                        echo "<option value=".$position['position_id'].">".$position['position_name']."</option>";
                                                    } 
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="button" style="float:right;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                <button type="submit" style="float:right margin-right:10px;" class="btn btn-primary btnSave" id="btnSave">Add user</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deactivate user modal -->
    <div id="deactivateUserModal" role="dialog" aria-labelledby="deactivateModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="deactivateModalLabel" class="modal-title">Confirm Deactivation</h4>
                </div>
                <form class="form-horizontal personal-info-css" id="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Are you sure you want to deactivate <strong class="del_name"></strong>?</p>
                                <input type="hidden" id="del_id" class="del_id" name="del_id" value="">
                                <input type="hidden" id="del_status" class="del_status" name="del_status" value="">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="button" style="float:right" class="btn btn-danger deactUserBtn">Deactivate User</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reactivate user modal -->
    <div id="reactivateUserModal" role="dialog" aria-labelledby="reactivateModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="reactivateModalLabel" class="modal-title">Confirm Reactivation</h4>
                </div>
                <form class="form-horizontal personal-info-css">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Are you sure you want to reactivate <strong class="react_name"></strong>?</p>
                                <input type="hidden" id="react_id" class="react_id" name="react_id" value="">
                                <input type="hidden" id="react_status" class="react_status" name="react_status" value="">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="button" style="float:right" class="btn btn-primary reactUserBtn">Reactivate User</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script type="text/javascript" src="<?=base_url('assets/js/dev_settings/settings_company_users.js');?>"></script>

