<div class="content-inner" id="pageActive" data-num="1"></div>
<div id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="true"></div>

<!-- <div class="login-container detail-card col-md-8 mx-auto mb-5"> -->
<div class="login-container detail-card col-md-5 mx-auto mb-5">
	<!-- FORGOT PASS -->
	<div class="page-container col-12 forgot_pass">
		<div class="mb-3">
			<h4><b>Change Password</b></h4>
			<small class="form-text">Please enter your new password below</small>
		</div>

		<div class="row">
			<div class="col-lg-12 col-12 content-container left">
				<div class="content">
				  <?php
						$url = base_url('auth/authentication/reset_password');
					    echo form_open($url,array('id'=>'reset_form','method'=>'post'));
				   ?>
            <fieldset>
              <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-group">
                    <input type="password" name="new_pass" id="new_pass" class="form-control password" placeholder="Minimum of 8 characters" autocomplete="off">
                    <input type="hidden" name = "email" value = "<?=$email_hash?>">
                    <div class="input-group-append toggle-password-icon">
                      <span class="input-group-text bg-white toggle-password px-3"><i class="fa fa-eye-slash no-margin" data-target="#password"></i></span>
                    </div>
                </div>
              </div>
              <div class="form-group">
                <label for="password">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" name="con_new_pass" id="con_new_pass" class="form-control password"  autocomplete="off">
                    <div class="input-group-append toggle-password-icon">
                      <span class="input-group-text bg-white toggle-password px-3"><i class="fa fa-eye-slash no-margin" data-target="#password"></i></span>
                    </div>
                </div>
              </div>
              <div class="login-buttons">
                <button type="submit" class="btn btn-primary btn-block mb-3" <?= (isset($serviceurl) && isset($serviceaccount) ? 'data-serviceurl="'.$serviceurl.'" data-serviceaccount="'.$serviceaccount.'"' : '') ?> >Reset Password</button>
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

<?php  $this->load->view("includes/footer"); ?>
<!-- <script src="<?=base_url('assets/js/auth/login.js');?>"></script> -->
<script src="<?=base_url('assets\js\auth\forgot_pass.js');?>"></script>
