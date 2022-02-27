<?php header("X-Frame-Options: DENY"); ?>

<?php
    $access_nav = $this->session->userdata('access_nav');

    $company_logo = base_url('assets/img/logo.png');
    $company_logo_small = base_url('assets/img/logo.png');
    // avatar
    $primary_color = cs_clients_info()->primary_color;
    $button_radius_size = cs_clients_info()->button_radius_size;
    $button_text_color = cs_clients_info()->button_text_color;
    $button_primary_color = cs_clients_info()->button_primary_color;
    $header_bg_color = cs_clients_info()->header_bg_color;
    $middle_bg_color = cs_clients_info()->header_bg_color;
    $footer_bg_color = cs_clients_info()->footer_bg_color;
    $facebook_link = cs_clients_info()->facebook_link;
    $instagram_link = cs_clients_info()->instagram_link;
    $youtube_link = cs_clients_info()->youtube_link;
    $twitter_link = cs_clients_info()->twitter_link;
    $c_favicon = cs_clients_info()->c_favicon;
    $c_main_logo = cs_clients_info()->c_main_logo;
    $c_secondary_logo = cs_clients_info()->c_secondary_logo;
    $tagline = cs_clients_info()->tagline;
    $c_phone = cs_clients_info()->c_phone;
    $font_choice = cs_clients_info()->font_choice;
    $fonts_link=base_url('assets/fonts');
    $name = cs_clients_info()->name;
    $contact_us = cs_clients_info()->c_contact_us;
    $terms_and_conditions = cs_clients_info()->c_terms_and_condition;
    
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?=$name;?> | Login</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    
    <style>
        :root {
            --primary-color: <?=$primary_color?>;
            --primary-font: <?=$font_choice?>;

            --header_upper_bg: <?=$header_bg_color?>;
            --header_upper_txtcolor: <?=$primary_color?>;
            --header_middle_bg: <?=$header_bg_color?>;
            --header_middle_txtcolor: <?=$primary_color?>;
            --header_middle_icons: <?=$primary_color?>;
            --header_bottom_bg: <?=$footer_bg_color?>;
            --header_bottom_textcolor: <?=$primary_color?>;
            --footer_bg: <?=$footer_bg_color?>;
            --footer_textcolor: <?=$primary_color?>;
            --footer_titlecolor: <?=$primary_color?>;
        }
        @font-face {
            font-family: <?=$font_choice?>;
            src: url(<?=base_url('assets/fonts/'.$font_choice.'-Regular.otf')?>) format("opentype");
        }
        *{
            
            font-family: <?=$font_choice?>;
        }
    </style>
    <link rel="stylesheet" href="<?=base_url('assets/css/libs/font-awesome.min.css');?>">
	<!-- Favicon-->
	<link rel="shortcut icon"  href="<?=base_url('assets/img/'.$c_favicon)?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/libs/bootstrap.min.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/libs/fontawesome/css/all.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery.toast.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery-ui.css')?>">        
    <link rel="stylesheet" href="<?=base_url('assets/css/admin/login.css')?>">        
    <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/bootstrap-4.1.3.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/owl-carousel.min.css">        
    <link rel="stylesheet" href="<?=base_url('assets/css/libs/header_styles.css')?>">  
</head>
<?php 
    switch (ini()) {
        case '3mangs':
            $bg_url = "bg.jpg";
            break;
        default:
            $bg_url = "bg_default.jpg";
            break;
    }
?>
<body class="login-body" data-base_url="<?=base_url();?>" style="background-image: url(<?=base_url('assets/img/bg/'.$bg_url)?>) !important;background-size:cover;">
 
    <div class="d-flex justify-content-center">
        <span class="align-middle">
                            
            <form name="login">
                <div class="card " style="width: 20rem;">
                    <div class="card-body">
                        <div class="w-100 text-center">
                            <img class="logo_login" src="<?=base_url('assets/img/'.$c_main_logo);?>" width=100%>
                        </div>
                        <br>
                        <br>    
                        <h5 class="card-title">Sign In</h5>To proceed to your shop
                        <br>
                        <br>
                        <div class="form-group">
                            <label for="login-form">Username</label>
                            <input type="text" required placeholder="Enter Username" name="username"id="username" class="form-control">
                        </div>
                        <div class="form-group" id="show_hide_password">
                            <label for="login-form">Password</label>
                            <div class="input-group">
                                <input type="password"required id="password" name="password" placeholder="Enter Password" class="form-control">
                                <div class="d-flex justify-content-center">
                                    <span class="align-middle m-2">
                                        <a href="#" class="showpassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-link" id="forgotpass">Forgot Password?</button>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary  w-100" id="signin">LogIn</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </span>
    </div>
<!-- Javascript files-->
    <script src="<?= base_url('assets/js/libs/jquery.min.js') ?>"></script>        
    <!-- libs -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>         
    <script src="<?=base_url('assets/js/libs/jquery.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/tether.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/bootstrap.bundle.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/bootstrap.bundle.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/mdb.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/jquery-ui.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/jquery.toast.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/moment.js')?>"></script>

<!---->
<script src="<?=base_url('assets/js/admin/login.js');?>"></script>
<script src="<?=base_url('assets/js/libs/loadingoverlay.js');?>"></script>
</body>

</html>