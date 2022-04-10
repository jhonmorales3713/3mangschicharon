<div class="signup-form">    
    <div class="row">        
        <h2>Sign Up</h2>        
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="">Full Name <span class="text-red">*</span></label>
                <input type="text" id="full_name" class="form-control"/>
            </div>
        </div>   
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="">Mobile No. <span class="text-red">*</span></label>
                <input type="text" id="mobile" class="form-control"/>
            </div>
        </div>    
    </div> 
    <div class="row">         
        <div class="col-12">
            <div class="form-group">
                <label for="">Email Address <span class="text-red">*</span></label>
                <input type="text" id="email" class="form-control"/>
            </div>
        </div>
    </div>    
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="">Password <span class="text-red">*</span></label>
                <input type="password" id="password" class="form-control"/>
            </div>
        </div>   
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="">Confirm Password <span class="text-red">*</span></label>
                <input type="password" id="password2" class="form-control"/>
            </div>
        </div>   
    </div>
    <div class="row">        
        <div class="col-12 form-group">            
            <button class="btn btn-primary form-control" id="btn_signup">SIGNUP</button>
        </div>        
    </div>
    <div class="row">
        <div class="col-12 clearfix">
            <span class="float-right">Already have an account? click <a href="#" id="login_link">here</a> to login</span>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/signup/signup.js') ?>"></script>