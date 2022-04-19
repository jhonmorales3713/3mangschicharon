<style>
#avatar-placeholder {
    width:100px;
    height:100px;
    background-color:#cccccc;
    cursor:pointer
}
#avatar_preview {
    width:100px;
    margin: auto;
}
</style>
<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_settings/settings_home/Settings');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/settings/user_list/view');?>">User List</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular"><?=$type?></span>
        
    </div>
</div>

<div class="col-12">
<div class="content-inner  ml-4" id="pageActive" data-num="7" data-namecollapse="" data-labelname="New User"> 

    <div class="card mb-4">
        <form method="POST" action="<?php echo base_url() . 'admin/settings/user_list/create_data'; ?>" id="record_form" enctype='multipart/form-data' class="card-body">
            <input type="password" id="f_id" name="f_id" class="form-control" hidden autocomplete='new-password' value="<?=$id?>" >
            <div class="row">
                <div class="col-md-6 col-lg-3 div_avatar">
                    <div class="form-group">
                        <center>
                        <label>Avatar</label><br/>
                        <input type="file" class="hidden" name="avatar_image" id="avatar_image">
                        <div id="avatar-placeholder" class="text-center">
                            <br>
                            <p class="small" style="margin-top: 8px;">click here <br> to <br> upload</p>
                        </div>
                        <img src="" id="avatar_preview" class="img-responsive mb-1" >
                        <input type="text" class="hidden" name="current_avatar_url" id="current_avatar_url"><br>
                        <button type="button" class="btn btn-primary btn-sm" id="change-image">Change photo</button>
                        </center>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <label class="col-form-label">User Email</label>
                        <input type="text" name="f_email" class="form-control" required>
                    </div>
                    <div class="form-group" hidden>
                        <label class="col-form-label">Password</label>
                        <input type="password" name="f_password" class="form-control">
                    </div>
                </div>
            </div>
            <hr>
            <!-- <div class="col-12 text-center mt-3">
                <button type="button" data-toggle="collapse" data-target="#demo" class="btn btn-primary">Show Access Control</button>
            </div> -->
            <div id="demo">
                <?php $this->load->view('admin/settings/include_user_access_control'); ?>
            </div>
        </form>
    </div>

    <div class="card p-2">
        <div class="row justify-content-end">
            <a href="<?=base_url('admin/settings/user_list/view');?>" class="btn btn-outline-secondary col-md-1 mr-3" id="backBtn">Back</a>
            <button type="submit" class="btn btn-primary col-md-1 mr-3" form="record_form">Save</button>
        </div>
    </div>
</div>
<script>
var token = "<?=$token;?>";
</script>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/settings/add_user.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/settings/user_access.js');?>"></script>
<!-- end - load the footer here and some specific