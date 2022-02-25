<style>
@media screen and (max-width: 397px){
	.login-buttons .btn{
		white-space: normal;
		font-size:11px;
	}
}

@media screen and (max-width: 347px){
	.login-buttons .btn{
		padding: 10px 20px 10px 20px;
	}
}
</style>

<div class="content-inner" id="pageActive" data-num="1"></div>
<div id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="true"></div>

<div class="login-container detail-card <?=(allow_physical_login() == 1) ? 'col-md-8' : 'col-md-5'?> mx-auto mb-5">
<!-- <div class="login-container detail-card col-md-5 mx-auto mb-5"> -->
	<!-- LOGIN  -->
	<div class="page-container col-12 login">
		<div class="mb-3"><h4><b>Welcome back! Please sign in.</b></h4></div>
		<div class="row">

			<?php if (allow_physical_login() == 1){ ?> <!-- //validation for allow Guest -->
				<div class="col-lg-6 col-12 content-container left">
					<!-- <div class="col-lg-12 col-12 content-container left"> -->
					<div class="content">
						<?php
							$url = base_url('auth/authentication/login');
						    echo form_open($url,array('id'=>'login_form','method'=>'post'));
						 ?>
							<fieldset>
								<div class="form-group">
									<label for="username">Username</label>
									<input type="text" class="form-control" name="username" autocomplete="off">
								</div>
								<div class="form-group">
									<label for="password">Password</label>
									<div class="input-group">
	                                    <input type="password" name="password" id="password" class="form-control" autocomplete="off">
	                                    <div class="input-group-append toggle-password-icon">
	                                        <span class="input-group-text bg-white toggle-password px-3"><i class="fa fa-eye-slash no-margin" data-target="#password"></i></span>
	                                    </div>
	                                </div>
								</div>
								<div class="login-register-now mb-3">
									<label><a id = "btn_forgot_pass">Forgot password?</a> </label>
								</div>
								<div class="login-buttons">
									<button type="submit" class="btn btn-primary btn-block mb-3" <?= (isset($serviceurl) && isset($serviceaccount) ? 'data-serviceurl="'.$serviceurl.'" data-serviceaccount="'.$serviceaccount.'"' : '') ?> >Sign In</button>
								</div>
								<div class="login-register-now mb-3">
									<!-- <label>Don't have an account yet? <a href="<?=base_url('user/register')?>">Register Now</a> </label> -->
								</div>
							</fieldset>
						</form>
					</div>
				</div>

				<div class="col-lg-6 col-12 content-container right">
			<?php }else{ ?>
				<!-- <div class="col-lg-8 offset-2 col-12 content-container right"> -->
				<div class="col-12 content-container right">
			<?php } ?>
				<!-- <div class="col-12 content-container right"> -->
				<div class="content">
						<fieldset>
							<?php if (continue_as_guest_button() == 1): ?> <!-- //validation for allow Guest -->
								<div class="login-buttons">
									<button type="button" class="btn btn-secondary btn-block mb-3" id="guestBtn" >Continue as Guest</button>
								</div>
							<?php endif ?>
							<?php if (continue_as_guest_button() == 1 && allow_connect_as_online_reseller() == 1): ?> <!-- //validation for division of buttons -->
								<div class="login-div-span row">
									<div class="col-lg-5 col-5">
										<hr>
									</div>
									<div class="col-lg-2 col-2">
										<span class="login-span">or</span>
									</div>
									<div class="col-lg-5 col-5">
										<hr>
									</div>
								</div>
							<?php endif ?>

							<?php if (allow_connect_as_online_reseller() == 1): ?> <!-- //validation for allow Online Reseller -->
								<div class="login-buttons">
									<?php if(ini()=="jcww") { ?>
										<button type="button" class="btn btn-jc btn-block mb-2" id="jcBtn">Connect with JC</button>
									<?php } else { ?>

										<button type="button" class="btn btn-jc btn-block mb-2" id="jcBtn">Connect as Online Reseller</button>
									<?php } ?>
									<!-- <button type="submit" class="btn btn-facebook btn-block mb-2">Login with Facebook</button>
									<button type="button" class="btn btn-google btn-block">Sign In with Google</button> -->
								</div>
							<?php endif ?>

							<?php if (allow_facebook_login() == 1): ?> <!-- //validation for allow facebook login -->
								<div class="login-buttons">
									<button type="button" class="btn btn-secondary btn-block mb-3" id="fbBtn" style = "background-color:#3b5998;color:#fff;border: 2px solid #3b5998;">Connect using Facebook</button>
								</div>
							<?php endif ?>
							<?php if (allow_gmail_login() == 1): ?> <!-- //validation for allow gmail login -->
								<div class="login-buttons">
									<button type="button" class="btn btn-secondary btn-block mb-3" id="gmailBtn" style = "background-color:#d34836;color:#fff;border: 2px solid #d34836;">Connect using Gmail</button>
								</div>
							<?php endif ?>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- FORGOT PASS -->
	<div class="page-container col-12 forgot_pass" style = "display:none;">
		<div class="mb-3">
			<h4><b>Forgot Password ?</b></h4>
			<small class="form-text">Please enter the account that you want to reset the password.</small>
		</div>

		<div class="row">
			<div class="col-lg-12 col-12 content-container left">
				<div class="content">
					<?php
						$url = base_url('auth/authentication/forgot_password');
					    echo form_open($url,array('id'=>'forgot_form','method'=>'post'));
					 ?>
						 <fieldset>
							 <div class="form-group">
								 <label for="username">Email <span class="asterisk"></span></label>
								 <input type="text" class="form-control" id = "forgot_email" name="forgot_email" autocomplete="off" >
							 </div>
							 <div class="login-buttons">
								 <button type="submit" disabled id = "btn_send_link" class="btn btn-primary btn-block mb-3" <?= (isset($serviceurl) && isset($serviceaccount) ? 'data-serviceurl="'.$serviceurl.'" data-serviceaccount="'.$serviceaccount.'"' : '') ?> >Send Link</button>
								 <a id = "btn_goback_from_forgotpass" style = "cursor:pointer;">Go Back</a>
							 </div>
							 <div class="login-register-now mb-3">
								 <!-- <label>Don't have an account yet? <a href="<?=base_url('user/register')?>">Register Now</a> </label> -->
							 </div>
						 </fieldset>

					 </form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Connect with JC Modal -->
<div id="modal_jc" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">

		<?php if(ini()=="jcww") { ?>
        <div class="modal-content col-12 p-5 pt-5" style="background-image: url(<?= base_url('assets/img/jc-bg.png')?>); background-size: cover; top: 50px; border-radius: 10px">
			<?php
				$url = base_url('auth/authentication/login_jc');
			    echo form_open($url,array('id'=>'login_form_jc','method'=>'post'));
			 ?>
            <div class="modal-body shadow" style="background-color: rgba(255,255,255,0.7);">
				<div class="login">
					<div class="container">
						<div class="row">
							<div class="form col-12">
								<div class="text-center">
									<img src="<?= base_url('assets/img/jc-login-logo.png')?>" style="width:40%">
								</div>
								<form id="login-form" novalidate="novalidate">
									<div class="form-group">
										<input type="text" class="form-control" id="username" placeholder="Username" name="username">
									</div>
									<div class="form-group">
										<input type="password" class="form-control" id="password" placeholder="Password" name="password">
									</div>
									<button type="submit" class="btn-jc btn-jcp btn-block mb-3" id="btnLogin">Continue</button>
								</form>
								<button type="button" class="btn-jc btn-jcp btn-brown btn-block mb-3" data-dismiss="modal" aria-label="Back">Back</button>
								<div>
									<label>To continue, JC will share your name, address, contact number and email address with <?=get_company_name();?>. Before using this app, you can review <?=get_company_name();?>'s <a style="color:var(--blue)" href="<?=privacy_policy();?>" target="_blank" >terms and conditions</a> and <a style="color:var(--blue)" href="<?=terms_and_condition();?>" target="_blank" >terms of service</a>.</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
		<?php } else { ?>
        <div class="modal-content col-12 p-5 pt-5">
			<?php
				$url = base_url('auth/authentication/login_jc');
			    echo form_open($url,array('id'=>'login_form_jc','method'=>'post'));
			 ?>
            <div class="modal-body shadow" style="background-color: rgba(255,255,255,0.7);">
				<div class="login">
					<div class="container">
						<div class="row">
							<div class="form col-12 login-buttons">
								<div class="text-center">
									<img src="<?= base_url('assets/img/main-logo.png')?>" style="width:60%">
								</div>
								<form id="login-form" novalidate="novalidate">
									<div class="form-group">
										<input type="text" class="form-control" id="username" placeholder="Username" name="username">
									</div>
									<div class="form-group">
										<input type="password" class="form-control" id="password" placeholder="Password" name="password">
									</div>
									<button type="submit" class="btn btn-primary btn-block mb-3" id="btnLogin">Continue</button>
								</form>
								<button type="button" class="btn btn-secondary btn-block mb-3" data-dismiss="modal" aria-label="Back">Back</button>
								<div>
									<label>Before using this app, you can review <?=get_company_name();?>'s <a style="color:var(--blue)" href="<?=privacy_policy();?>" target="_blank" >terms and conditions</a> and <a style="color:var(--blue)" href="<?=terms_and_condition();?>" target="_blank" >terms of service</a>.</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
		<?php } ?>
    </div>
</div>

<?php  $this->load->view("includes/footer"); ?>

<?php if (allow_gmail_login() == 1): ?> <!-- //validation for allow gmail login -->
	<script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
	<script src="https://apis.google.com/js/api:client.js"></script>
	<script src="<?=base_url('assets\js\auth\gmail_login.js');?>"></script>
<?php endif ?>

<?php if (allow_facebook_login() == 1): ?>
	<script src="<?=base_url('assets\js\auth\fb_login.js');?>"></script>
<?php endif ?>

<script src="<?=base_url('assets/js/auth/login.js');?>"></script>
<script src="<?=base_url('assets\js\auth\forgot_pass.js');?>"></script>
