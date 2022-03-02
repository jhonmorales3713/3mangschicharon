<div class="container signup-form">    
    <div class="row">        
        <h2>Sign Up</h2>        
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label for="">Full Name <span class="text-red">*</span></label>
                <input type="text" id="full_name" class="form-control"/>
            </div>
        </div>   
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label for="">Mobile No. <span class="text-red">*</span></label>
                <input type="text" id="mobile" class="form-control"/>
            </div>
        </div>    
    </div> 
    <div class="row">         
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label for="">Email Address <span class="text-red">*</span></label>
                <input type="text" id="email" class="form-control"/>
            </div>
        </div>
    </div>    
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label for="">Password <span class="text-red">*</span></label>
                <input type="password" id="password" class="form-control"/>
            </div>
        </div>   
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label for="">Confirm Password <span class="text-red">*</span></label>
                <input type="password" id="password2" class="form-control"/>
            </div>
        </div>   
    </div>
    <div class="row">        
        <div class="col-lg-6 col-md-6 col-sm-12">            
            <button class="btn btn-primary form-control" id="btn_signup">SIGNUP</button>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/signup/signup.js') ?>"></script>