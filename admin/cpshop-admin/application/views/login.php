<?php header("X-Frame-Options: DENY"); ?>

<?php
    $header_upper_bg = header_upper_bg(); //"none";
    $header_upper_txtcolor = header_upper_txtcolor(); //"#222222";

    $header_middle_bg = header_middle_bg(); //"#222";
    $header_middle_txtcolor = header_middle_txtcolor(); //"#fff";
    $header_middle_icons = header_middle_icons();

    $header_bottom_bg = header_bottom_bg(); //"#ff4444";
    $header_bottom_textcolor = header_bottom_textcolor(); //"#fff";

    $footer_bg = footer_bg(); //"#222222";
    $footer_textcolor = footer_textcolor(); //"#ffffff";
    $footer_titlecolor = footer_titlecolor();

    $primaryColor_accent = primaryColor_accent(); //"#ff4444";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?=get_company_name();?> | Login</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    
    <style>
        :root {
            --primary-color: <?=$primaryColor_accent?>;
            --primary-font: <?=$font?>;

            --header_upper_bg: <?=$header_upper_bg?>;
            --header_upper_txtcolor: <?=$header_upper_txtcolor?>;
            --header_middle_bg: <?=$header_middle_bg?>;
            --header_middle_txtcolor: <?=$header_middle_txtcolor?>;
            --header_middle_icons: <?=$header_middle_icons?>;
            --header_bottom_bg: <?=$header_bottom_bg?>;
            --header_bottom_textcolor: <?=$header_bottom_textcolor?>;
            --footer_bg: <?=$footer_bg?>;
            --footer_textcolor: <?=$footer_textcolor?>;
            --footer_titlecolor: <?=$footer_titlecolor?>;
        }
    </style>
	<!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.css');?>">
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>"> -->
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/style.blue.css');?>" id="theme-stylesheet"> -->
    <link rel="stylesheet" href="<?=base_url('assets/css/font-awesome.min.css');?>">
	<!-- Favicon-->
	<link rel="shortcut icon" href="<?=favicon()?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/login.css'); ?>">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<?php 
    switch (ini()) {
        case 'jcww':
            $bg_url = "blue.jpg";
            break;
        case 'jconlineshop':
            $bg_url = "green.jpg";
            break;
        case 'maisonlovemarie':
            $bg_url = "mlm.jpg";
            break;
        default:
            $bg_url = "yellow.jpg";
            break;
    }
?>
<body class="login-body" data-base_url="<?=base_url();?>" style="background-image: url(<?=base_url('assets/img/bg/'.$bg_url)?>) !important">
 
 <div class="login-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="login-page__container">
                    <div class="row">
                        <div class="col-12 pt-5 pl-5 pr-5 pb-4">
                            <div class="w-100 text-center">
                                <img class="logo_login" src="<?=main_logo()?>">
                            </div>
                            <div class="my-5">
                                <h1 class="login-page-title font-weight-light">
                                    Sign In
                                </h1>
                                <p class="login-page-subtitle">Proceed to your shop</p>
                            </div>
                            <form method="post" action="" id="login-form">
                                <div class="form-group">
                                    <label for="login-username">Username</label>
                                    <input type="text" class="form-control" name="loginUsername" id="login-username" aria-describedby="emailHelp" required placeholder="Type your username here...">
                                </div>
                                <div class="form-group">
                                    <label for="login-password" class="input-label">Password</label><i class="bar"></i>
                                    <div class="w-100 form-control-password">
                                        <input type="password" class="form-control" name="loginPassword" id="login-password" required="required" placeholder="Type your password here...">
                                        <i class="fa fa-eye-slash password-icon password-show" aria-hidden="true"></i>
                                        <i class="fa fa-eye password-icon password-hide" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <a href="<?= base_url('Account/forgotpassword') ?>">Forgot Password?</a>
                                </div>

                                <div class="form-group text-center justify-content-center" id="captchaDiv" style="display:none;">
                                    <div id="captcha">
                                    </div>
                                    <!-- <div class="form-group"> -->
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <!-- <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="refreshCaptcha"><i class="fa fa-refresh" aria-hidden="true"></i></a> -->
                                            <strong><small>Can't read the image? Click <span id="refreshCaptcha">here</span> to refresh.</small></strong>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <input type="text" class="form-control" placeholder="Enter the code" id="inputCaptcha"/>
                                        </div>
                                    </div>
                                    <!-- </div> -->
                                </div>

                                <br>
                                <div class="form-group text-center mb-5 justify-content-center">
                                    <button type="submit" class="btn btn-primary-cp py-1 px-4" id="btnLogin">Login</button>
                                </div>
                            </form>
                            <div class="login-footer footer-links text-right">
                                <a target="_blank" href="<?=contact_us_view();?>">Help</a>
                                <a target="_blank" href="<?=privacy_policy_view();?>">Privacy</a>
                                <a target="_blank" href="<?=terms_and_condition_view();?>">Terms</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>

<!-- Javascript files-->
<script src="<?=base_url('assets/js/jquery-3.5.1.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery-ui.js');?>"></script>
<script src="<?=base_url('assets/js/accounting.min.js');?>"></script>
<script src="<?=base_url('assets/js/globalfunctions.js');?>"></script>
<script src="<?= base_url('assets/js/tether.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.cookie.js'); ?>"> </script>
<script src="<?= base_url('assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/front.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>

<!---->
<script src="<?=base_url('assets/js/login.js');?>"></script>
<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>
</body>
</html>