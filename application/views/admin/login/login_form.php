<div class="container">    
    <div class="row d-flex justify-content-center align-items-center m-0" style="height: 90vh;">
        <div class="col-lg-3 col-md-1 col-sm-1"></div>
        <div class="col-lg-6 col-md-10 col-sm-10">
                
            <div class="col-12">
                <center>
                    <img src="<?= base_url('assets/img/shop_logo.png') ?>" alt="" height="280"><br>
                    <strong>Admin Login</strong><br><br>
                </center>
            </div>                    
            <div class="col-12">
                <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                </div>
                <input name="username" type="text" value="" class="input form-control" id="username" placeholder="Username" aria-label="Username" />
                </div>
            </div>
            <div class="col-12">
                <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                </div>
                <input name="password" type="password" value="" class="input form-control" id="password" placeholder="password" required="true" aria-label="password" />
                <div class="input-group-append">
                    <span class="input-group-text" onclick="password_show_hide();">
                    <i class="fas fa-eye" id="show_eye"></i>
                    <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                    </span>
                </div>
                </div>
            </div>            
            <div class="col-12">
                <button class="btn btn-warning form-control" type="submit" name="signin">Login</button>
            </div>
            <div class="col-12 text-right">
                <br>
                <small>Forgot Password? <a href="#">click here</a></small>                
            </div>                
        
        </div>
        <div class="col-lg-3 col-md-1 col-sm-1"></div>
    </div>
</div>