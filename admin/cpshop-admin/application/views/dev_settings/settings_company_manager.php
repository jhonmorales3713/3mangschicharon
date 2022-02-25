<?php 
//071318
//this code is for destroying session and page if they access restricted page

$position_access = $this->session->userdata('get_position_access');
$access_content_nav = $position_access->access_content_nav;
$arr_ = explode(', ', $access_content_nav); //string comma separated to array 
$get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();

$url_content_arr = array();
foreach ($get_url_content_db as $cun) {
    $url_content_arr[] = $cun['cn_url'];
}
$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';

if (in_array($content_url, $url_content_arr) == false){
    header("location:".base_url('Main/logout'));
}    
//071318
?>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="12" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Company Manager</li>
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
                                                <label class="form-control-label col-form-label-sm">Company Code</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text txt_search_code" placeholder="Company Code">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Company Name</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text txt_search_name" placeholder="Company Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <table class="table table-striped table-hover"> -->
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addContentNavigationModal" class="btn btn-primary btnClickAddSystemUser" name="update" style="right:20px; position: absolute; top:20px;">Add Company</button>
                            <button class="btn btn-primary btnSearch" style="right:145px; position: absolute; top:20px;">Search</button>
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="110">Company Code</th>
                                            <th>Company Name</th>
                                            <th>Plan</th>
                                            <th>Date Registered</th>
                                            <th>End of Free Trial</th>
                                            <th width="250">Action</th>
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

    <!-- Add company modal -->
    <div id="addContentNavigationModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add New Company</h4>
                </div>
                <form enctype="multipart/form-data" id="add_company_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Code <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_code" type="text" class="form-control form-control-success" name="add_code">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Name <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_name" type="text" class="form-control form-control-success" name="add_name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Initial <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_initial" type="text" class="form-control form-control-success" name="add_initial">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Logo <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_logo" type="file" name="add_logo"><br>
                                            <h6 class="red-text mt-1">Upload 320px x 320px (or lesser dimension) .png or .jpg image.</h6>
                                            <img id="add_logo_view" style="width: 50%;" src="" alt="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Logo (Small) <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_logo_small" type="file" name="add_logo_small"><br>
                                            <h6 class="red-text mt-1">Upload 320px x 320px .png or .jpg image.</h6>
                                            <img id="add_logo_small_view" style="width: 50%;" src="" alt="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Address <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <textarea id="add_address" type="text" class="form-control form-control-success" name="add_address"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Website <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_website" type="text" class="form-control form-control-success" name="add_website">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Phone <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_phone" type="text" class="form-control form-control-success" name="add_phone">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Email <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_email" type="text" class="form-control form-control-success" name="add_email">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Database <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="add_database" type="text" class="form-control form-control-success" name="add_database">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Plan <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <select name="add_plan" id="add_plan" class="form-control material_josh form-control-sm">
                                                <option value="">Select Plan</option>
                                                <option value="1">Silver</option>
                                                <option value="2">Gold</option>
                                                <option value="3">Demo</option>
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
                                <button type="submit" style="float:right" class="btn btn-primary saveBtnContentNavigation">Add Company</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update company modal -->
    <div id="updateCompanyModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Update Company Details</h4>
                </div>
                <form class="form-horizontal personal-info-css" id="update_company_form">
                    <input type="hidden" name="update_user_id" id="update_user_id" class="update_user_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Code <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_id" type="hidden" name="update_id">
                                            <input id="update_code" type="text" class="form-control form-control-success" name="update_code">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Name <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_name" type="text" class="form-control form-control-success" name="update_name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Initial <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_initial" type="text" class="form-control form-control-success" name="update_initial">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Logo <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_logo" type="file" name="update_logo"><br>
                                            <h6 class="red-text mt-1">Upload 320px x 320px .png or .jpg image.</h6>
                                            <img id="update_logo_view" style="width: 50%;" src="" alt="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Logo (Small) <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_logo_small" type="file" name="update_logo_small"><br>
                                            <h6 class="red-text mt-1">Upload 320px x 320px .png or .jpg image.</h6>
                                            <img id="update_logo_small_view" style="width: 50%;" src="" alt="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Address <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <textarea id="update_address" type="text" class="form-control form-control-success" name="update_address"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Website <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_website" type="text" class="form-control form-control-success" name="update_website">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Phone <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_phone" type="text" class="form-control form-control-success" name="update_phone">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Email <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_email" type="text" class="form-control form-control-success" name="update_email">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Database <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <input id="update_database" type="text" class="form-control form-control-success" name="update_database">
                                            <h6 class="red-text mt-1">Make sure that the database schema name has been changed manually before changing this data.</h6>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Company Plan <span class="" style="color:red">*</span></label>
                                        <div class="col-md-9">
                                            <select name="update_plan" id="update_plan" class="form-control material_josh form-control-sm">
                                                <option value="">Select Plan</option>
                                                <option value="1">Silver</option>
                                                <option value="2">Gold</option>
                                                <option value="3">Demo</option>
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
                                <button type="submit" style="float:right" class="btn btn-success updateBtnContentNavigation">Update Content Navigation</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deactivate company modal -->
    <div id="deleteCompanyModal" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="deleteModalLabel" class="modal-title">Confirm Deactivate</h4>
                </div>
                <?php echo form_open('class="horizontal personal-info-css" id="delete_form"'); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Are you sure you want to deactivate the company <strong class="del_comp_name"></strong>?</p>
                                <input type="hidden" id="del_comp_id" class="del_comp_id" name="comp_id" value="">
                                <input type="hidden" id="del_comp_status" class="del_comp_status" name="comp_status" value="">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" style="float:right; margin-right:10px;" class="btn btn-danger btnDeleteComp" id="btnDeleteComp">Deactivate</button>
                                <button type="button" style="float:right;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reactivate company modal -->
    <div id="reactCompanyModal" role="dialog" aria-labelledby="reactModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="reactModalLabel" class="modal-title">Confirm Reactivate</h4>
                </div>
                <?php echo form_open('class="horizontal personal-info-css" id="reactivate_form"'); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Are you sure you want to reactivate the company <strong class="react_comp_name"></strong>?</p>
                                <input type="hidden" id="react_comp_id" class="react_comp_id" name="comp_id" value="">
                                <input type="hidden" id="react_comp_status" class="react_comp_status" name="comp_status" value="">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" style="float:right; margin-right:10px;" class="btn btn-primary btnReactComp" id="btnReactComp">Reactivate</button>
                                <button type="button" style="float:right;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/globalfunctions.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/dev_settings/settings_company_manager.js');?>"></script>

