<div class="content-inner" id="pageActive" data-num="1"></div>
<div id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="true"></div>

<div class="login-container detail-card col-md-8 mx-auto mb-5">
	<div class="page-container col-12 login">
		<div class="mb-3"><h4><b>Register your account</b></h4></div>
		<div class="row">
			<div class="col-lg-6 col-12 content-container left">
				<div class="content">
					<?php
						$url = base_url('auth/authentication/register_user');
					    echo form_open($url,array('id'=>'reg_form','method'=>'post'));
					 ?>
						<fieldset>
							<div class="login-register-now mb-3">
								<label>Already a member? <a href="<?=base_url('user/login')?>">Sign In Here</a> </label>
							</div>
							<div class="form-group">
								<label for="email">Email Address*</label>
								<input type="text" class="form-control" id = "email" name="email" autocomplete="off">
								<div class="email-error-msg pl-3"></div>
							</div>
							<div class="login-buttons get-email-code-btn-wrapper">
								<button type="button" id = "btn-getemail-code" disabled class="btn btn-primary btn-block mb-3" <?= (isset($serviceurl) && isset($serviceaccount) ? 'data-serviceurl="'.$serviceurl.'" data-serviceaccount="'.$serviceaccount.'"' : '') ?> >Get Email Code</button>
							</div>
							<div class="verification-box mb-3" style = "display:none;">
								<input type="text" id = "email_code" name = "email_code" class="form-control " placeholder="Email Code">
								<span class = "pl-3"><a id = "resend" style = "cursor:pointer;" data-disabled = "1">Resend</a> <span class="count_resend"></span></span>
							</div>
							<div id="proceed_register">
								<div class="form-group">
									<label for="password">Password*</label>
									<div class="input-group">
	                    <input type="password" name="password" id="password" class="form-control" autocomplete="off" placeholder="">
	                    <div class="input-group-append toggle-password-icon">
	                        <span class="input-group-text bg-white toggle-password px-3 "><i class="fa fa-eye-slash no-margin show-password" data-target="#password"></i></span>
	                    </div>
	                </div>
								</div>
								<div class="form-group row">
									<div class="col-lg-8 col-8">
										<label for="birthdate">Birthdate</label>
										<input type="date" id = "birthday" name = "birthday" class="form-control" name="bday" autocomplete="off">
									</div>
									<div class="col-lg-4 col-4">
										<label for="gender">Gender</label>
                    <select class="form-control" id="select_status" name = "gender">
                        <option value="" selected>Select</option>
                        <option value="1">Male</option>
                        <option value="2">Female</option>
                    </select>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-5 col-5">
										<label for="fname">First Name*</label>
										<input type="text" class="form-control" id = "fname" name="fname" autocomplete="off">
									</div>
									<div class="col-lg-7 col-7">
										<label for="lname">Last Name*</label>
										<input type="text" class="form-control" id = "lname" name="lname" autocomplete="off">
									</div>
								</div>
								<div class="login-buttons">
									<button type="submit" class="btn btn-primary btn-block mb-3" <?= (isset($serviceurl) && isset($serviceaccount) ? 'data-serviceurl="'.$serviceurl.'" data-serviceaccount="'.$serviceaccount.'"' : '') ?> >Register</button>
								</div>
								<div class="register-now mb-3">
									<label>By clicking on "Register", I agree to <a href="<?=base_url('main/terms')?>" target="_blank">Terms and Conditions</a> and <a href="<?=base_url('main/privacy')?>" target="_blank">Privacy Policy</a>.</label>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			<div class="col-lg-6 col-12 content-container right">
				<div class="content">
						<fieldset>
							<div class="login-buttons">
								<button type="button" class="btn btn-secondary btn-block mb-3" id="guestBtn" >Continue as Guest</button>
							</div>
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
							<div class="login-buttons">
								<button type="submit" class="btn btn-jc btn-block mb-2">Connect as Online Reseller</button>
								<!-- <button type="submit" class="btn btn-facebook btn-block mb-2">Login with Facebook</button>
								<button type="button" class="btn btn-google btn-block">Sign In with Google</button> -->
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/auth/register.js');?>"></script>
