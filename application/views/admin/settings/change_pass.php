
<style>
    
a,.btn-link{
	color: var(--primary-color) !important;
}
</style>
<div class="col-12">
    <div class="alert alert-secondary ml-3 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_settings/');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Change Password</span>
        
    </div>
</div>
<div class="col-12 " id="pageActive" data-num="3"  data-labelname="Products"> 
    <div class="container-fluid col-lg-auto-ml-3">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4 text-white">Change Password</h3>
                    </div>
                    <div class="card-body">
                        <!-- <p>Make sure you choose a strong password.</p> -->
                        <form class="form-horizontal security-css" id="saveChangePassForm">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group" id="show_hide_password"data-email="<?=$this->session->username?>">
                                        <label for="login-form">Password</label>
                                        <div class="input-group">
                                            <input type="password"required id="password" name="oldpassword" placeholder="Enter Password" class="form-control">
                                            <div class="d-flex justify-content-center">
                                                <span class="align-middle m-2">
                                                    <a href="#" class="showpassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="show_hide_password2">
                                        <label for="login-form">New Password</label>
                                        <div class="input-group">
                                            <input type="password"required id="passwordnew" name="password" placeholder="Enter New Password" class="form-control">
                                            <div class="d-flex justify-content-center">
                                                <span class="align-middle m-2">
                                                    <a href="#" class="showpassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="show_hide_password3">
                                        <label for="login-form">Re-type Password</label>
                                        <div class="input-group">
                                            <input type="password"required id="passwordretype" name="passwordretype" placeholder="Re-type Password" class="form-control">
                                            <div class="d-flex justify-content-center">
                                                <span class="align-middle m-2">
                                                    <a href="#" class="showpassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <small class="text-xs font-semibold text-orange-400">Note: Password must contain at least 8 characters long, alphanumeric, lowercase and uppercase.</small>          
                                    </div>
                                    <div class="form-group row">       
                                        <div class="col-md-12">
                                            <button style="float:right" class="btn btn-primary saveChangePassBtn">Save</button>
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
</div>
<script src="<?=base_url('assets/js/libs/admin/settings/change_pass.js');?>"></script>