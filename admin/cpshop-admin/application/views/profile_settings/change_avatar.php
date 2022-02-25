<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
    img, canvas {
        max-width: 100%; /* This rule is very important, please do not ignore this! */
    }
</style>
<div class="content-inner" id="pageActive" data-num="99" data-namecollapse="#profile-collapse-a" data-labelname="Change Avatar">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/profile_settings_home/'.$token);?>">Profile Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Change Personal Information</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="h4">Personal Information</h3>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal security-css" id="saveChangeAvatarForm">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="label-material">First Name <span class="asterisk"></span></label>
                                            <input id="first_name" type="text" name="first_name" value="<?=$personal_information->fname?>" class="form-control required">
                                        </div>

                                        <div class="form-group">
                                            <label class="label-material">Middle Name</label>
                                            <input id="middle_name" type="text" name="middle_name" value="<?=$personal_information->mname?>" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label class="label-material">Last Name <span class="asterisk"></span></label>
                                            <input id="last_name" type="text" name="last_name" value="<?=$personal_information->lname?>" class="form-control required">
                                        </div>

                                        <div class="form-group">
                                            <label class="label-material">Mobile No. <span class="asterisk"></span></label>
                                            <input id="mobile_no" type="text" name="mobile_no" value="<?=$personal_information->mobile_number?>" class="form-control required allownumericwithoutdecimal">
                                        </div>

                                        <div class="form-group">
                                            <label class="label-material">Avatar <span class="asterisk"></span></label>
                                            <input id="avatar_file" type="file" name="avatar_file" class="form-control">
                                            <small>Allowed image format: .jpg, .png, .jpeg</small><br>
                                            <div id="crop_container">
                                                <img id="avatar_file_view" class="avatar_file_view" src="" alt="" />
                                            </div>
                                        </div>

                                        <div class="form-group row mt-4">       
                                            <div class="col-md-12">
                                                <button style="float:right" class="btn btn-primary saveChangeAvatarBtn">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="cropModal" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="deleteModalLabel" class="modal-title">Crop Image</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <img id="avatar_file_crop" src="" alt="" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" style="float:right;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button type="submit" style="float:right; margin-right:10px;" class="btn btn-primary btnCrop" id="btnCrop">Crop</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->load->view('includes/footer'); ?>

<script src="<?=base_url('assets/js/profile_settings/change_avatar.js');?>"></script>

