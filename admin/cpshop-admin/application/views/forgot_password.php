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
	<title><?=get_company_name();?> | Forgot Password</title>
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
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/forgot_password.css'); ?>">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<body data-base_url="<?=base_url();?>" style="background-image: url(<?=base_url('assets/img/bg/'.$bg_url)?>) !important">
	<!-- <div class="container padding-bottom-3x mb-2 mt-5">
	    <div class="row justify-content-center">
	        <div class="col-lg-8 col-md-10">
	            <div class="forgot">
	                <h2>Forgot your password?</h2>
	                <p>Change your password in three easy steps. This will help you to secure your password!</p>
	                <ol class="list-unstyled">
	                    <li>Enter your email address below.</li>
	                    <li><span class="text-medium">2. </span>Our system will send you a link to change your password within 24 hours.</li>
	                    <li><span class="text-medium">3. </span>Click the link to proceed to change password form then click save.</li>
	                </ol>
	            </div>
	            <form class="card mt-4" id="resetpass-form">
	                <div class="card-body">
	                    <div class="form-group"> <label for="email-for-pass">Enter your email address</label> 
	                    	<input class="form-control" type="text" id="email-for-pass" name="email-for-pass">
	                    	<small class="form-text text-muted">Please enter your valid email address. Then we’ll send you a link to change your password.</small> 
	                    </div>
	                </div>
	                <div class="card-footer"> <button class="btn btn-success" id="btnSubmit" type="submit">Submit</button> <a href="<?= base_url('Main') ?>" class="btn btn-danger">Back to Login</a> </div>
	            </form>
	        </div>
	    </div>
	</div> -->
	<div class="login-page">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-lg-4">
					<div class="login-page__container">
						<div class="row">
							<div class="col-12 pt-5 pl-5 pr-5 pb-4">
								<div class="w-100">
									<img class="logo_login img-fluid" src="<?=main_logo()?>">
								</div>
								<div class="my-5">
									<h1 class="login-page-title font-weight-light">
										Forgot Password?
									</h1>
									<p class="login-page-subtitle">Change your password in three easy steps. This will help you to secure your password!
										<a tabindex="0" class="" role="button" data-toggle="popover" data-trigger="focus" title="Change your password" data-content='<ol class="">
										<li class="mb-3">Enter your email address below.</li>
										<li class="mb-3">Our system will send you a link to change your password within 24 hours.</li>
										<li class="mb-3">Click the link to proceed to change password form then click save.</li>
									</ol>'
									data-html="true"
									>
											<i class="fa fa-info-circle" aria-hidden="true"></i>
										</a>
									</p>
									<!-- <ol class="">
										<li class="mb-3">Enter your email address below.</li>
										<li class="mb-3">Our system will send you a link to change your password within 24 hours.</li>
										<li class="mb-3">Click the link to proceed to change password form then click save.</li>
									</ol> -->
								</div>
								<form class="mt-4 mb-5" id="resetpass-form">
									<div class="form-group mb-4"> <label for="email-for-pass">Enter your email address</label> 
										<input class="form-control" type="text" id="email-for-pass" name="email-for-pass">
										<small class="form-text text-muted">Please enter your valid email address. Then we’ll send you a link to change your password.</small> 
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-12 mb-2">
												<button class="btn btn-primary-cp btn-block" id="btnSubmit" type="submit">Submit</button>
											</div>
											<div class="col-12">
												<a href="<?= base_url('Main') ?>" class="btn btn-primary-cp-outline btn-block">Back to Login</a> 
											</div>
										</div>
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
</body>
<!-- Javascript files-->
<script src="<?=base_url('assets/js/jquery-3.3.1.min.js');?>"></script>
<script src="<?= base_url('assets/js/tether.min.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.cookie.js'); ?>"> </script>
<script src="<?= base_url('assets/js/jquery.validate.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/front.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
<script src="<?= base_url('assets/js/custom.js'); ?>"></script>
<!---->
<script src="<?=base_url('assets/js/forgot_password.js');?>"></script>
<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>
<script>
	$(function () {
		$('[data-toggle="popover"]').popover();
	})
</script>
</body>
</html>