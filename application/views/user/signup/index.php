<div class="container">
    <div class="row" id="signup_page" style="display: none;">
        <div class="col-lg-3 col-md-3 col-sm-2"></div>
        <div class="col-lg-6 col-md-6 col-sm-8">
            <?php $this->load->view('user/signup/signup_form'); ?>
        </div>        
        <div class="col-lg-3 col-md-3 col-sm-2"></div>
    </div>
    <div class="row" id="login_page">
        <div class="col-lg-3 col-md-3 col-sm-2"></div>
        <div class="col-lg-6 col-md-6 col-sm-8">
            <?php $this->load->view('user/signup/login_form'); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-2"></div>
    </div>
</div>