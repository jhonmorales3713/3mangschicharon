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
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">System User</li>
        </ol>
    </div>
    
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <?php $this->load->view('components/com_table_search');?>
                <div class="card-body">
                    <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                        <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addSystemUserModal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary btnClickAddSystemUser">Add System User</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Full Name</th>
                                    <th width="190">Action</th>
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
<div id="addSystemUserModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Add New System User</h6>
            </div>
            <form class="form-horizontal personal-info-css" id="add_system_user-form">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">System User <span class="asterisk" style="color:red"></span></label>
                                <select name="info_position" class="form-control material_josh form-control-sm">
                                    <option disabled value="" selected>Select Position</option>
                                    <?php foreach($positions as $position):?>
                                        <option value="<?=$position['position_id']?>"><?=$position["position"]?></option>
                                    <?php endforeach?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">First Name <span class="asterisk" style="color:red"></span></label>
                                <input id="info_user_fname" type="text" class="form-control form-control-success" name="info_user_fname">
                            </div>
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Middle Name </label>
                                <input id="info_user_mname" type="text" class="form-control form-control-success" name="info_user_mname">
                            </div>
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Last Name <span class="asterisk" style="color:red"></span></label>
                                <input id="info_user_lname" type="text" class="form-control form-control-success" name="info_user_lname">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Username <span class="asterisk" style="color:red"></span></label>
                                <input id="info_username" type="text" class="form-control form-control-success" name="info_username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Password <span class="asterisk" style="color:red"></span></label>
                                <input id="info_password" type="password" class="form-control form-control-success" name="info_password">
                            </div>
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Re-type Password <span class="asterisk" style="color:red"></span></label>
                                <input id="info_re_password" type="password" class="form-control form-control-success" name="info_re_password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="btn btn-primary saveBtnArea">Add System User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="viewSystemUserModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Update System User</h6>
            </div>
            <form class="form-horizontal personal-info-css" id="update_system_user-form">
                <input type="hidden" name="info_user_id" class="update_user_id">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">System User <span class="asterisk" style="color:red"></span></label>
                                <select name="info_position" class="form-control material_josh form-control-sm update_position">
                                    <option disabled value="">Select Position</option>
                                    <?php foreach($positions as $position):?>
                                        <option value="<?=$position['position_id']?>"><?=$position["position"]?></option>
                                    <?php endforeach?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Full Name <span class="asterisk" style="color:red"></span></label>
                                <input id="update_user_fname" type="text" class="form-control form-control-success" name="info_user_fname">
                            </div>
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Middle Name </label>
                                <input id="update_user_mname" type="text" class="form-control form-control-success" name="info_user_mname">
                            </div>
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Last Name <span class="asterisk" style="color:red"></span></label>
                                <input id="update_user_lname" type="text" class="form-control form-control-success" name="info_user_lname">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Username <span class="asterisk" style="color:red"></span></label>
                                <input id="update_username" type="text" class="form-control form-control-success" name="info_username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Password</label>
                                <input id="update_password" type="password" class="form-control form-control-success" name="info_password">
                            </div>
                            <div class="form-group col-xl-4 col-md-6">
                                <label class="form-control-label col-form-label-sm">Re-type Password</label>
                                <input id="update_re_password" type="password" class="form-control form-control-success" name="info_re_password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="btn btn-primary saveBtnArea">Update System User</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="deleteSystemUserModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Delete System User</h6>
            </div>
            <form class="form-horizontal personal-info-css" id="delete_system_user-form">
                <div class="modal-body">
                    <p>Are you sure you want to delete this record <br>(<bold class="delete_username"></bold>) ?</p>
                    <input type="hidden" id="del_user_id" class="del_user_id" name="del_user_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" class="btn btn-primary deleteSystemUserBtn">Delete Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_system_user.js');?>"></script>

