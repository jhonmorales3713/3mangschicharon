<div id="login_form">    
    <div class="container">
        <div class="row">
            <h2>Login</h2>
            <div class="col-12">
                <div class="form-group">
                    <label for="">Email Address <span class="text-red">*</span></label>
                    <input type="text" id="login_email" class="form-control"/>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="">Password <span class="text-red">*</span></label>
                    <input type="password" id="login_password" class="form-control"/>
                </div>
            </div>   
            <div class="col-12">
                <div class="form-group">
                    <div class="btn btn-primary form-control" id="btn_login">LOGIN</div>
                </div>
            </div>
            <div class="col-12 clearfix">
                <span class="float-right">Don't have an account? click <a href="#" id="signup_link">here</a></span>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/signup/login.js') ?>"></script>