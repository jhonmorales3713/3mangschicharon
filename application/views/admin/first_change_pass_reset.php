<?php $this->load->view('templates/header') ?>
 <?php
 
 $contact_us = cs_clients_info()->c_contact_us;
 $terms_and_conditions = cs_clients_info()->c_terms_and_condition;
 $company_logo = base_url('assets/img/logo.png');
 ?>
 <head>
     <link rel="stylesheet" href="<?=base_url('assets/css/admin/login.css')?>">   
 </head>     
 <style>
     @font-face {
         font-family: <?=$font_choice?>;
         src: url(<?=base_url('assets/fonts/'.$font_choice.'-Regular.ttf')?>) format("opentype");
     }
     span,h1,h2,h3,h4,h5,span,input{
         font-family: <?=$font_choice?> !important;
     }
 </style>
 <div class="container mt-5">    
     <div class="d-flex justify-content-center">
         <span class="align-middle">
                             
             <form id="saveChangePassForm">
                 <div class="card " style="width: 20rem;">
                     <div class="card-body">
                         
                        <input type="hidden" name="user-id" value="<?= $sys_users_id ?>">
                         <div class="w-100 text-center">
                             <img class="logo_login" src="<?=$company_logo;?>" width=100%>
                         </div>
                         <br>
                         <br>    
                         <h5 class=""> Set Password</h5>Please enter and confirm your new password below to access your account.
                         <br>
                         <br>
                         <div class="form-group" id="show_hide_password">
                             <label for="login-form">Password</label>
                             <div class="input-group">
                                 <input type="password"required id="password" name="password" placeholder="Enter Password" class="form-control password">
                                 <div class="d-flex justify-content-center">
                                     <span class="align-middle m-2">
                                         <a href="#" class="showpassword"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                     </span>
                                 </div>
                             </div>
                         </div>
                        <div class="form-group" id="show_hide_password2">
                            <label for="login-form">Re-type Password</label>
                            <div class="input-group">
                                <input type="password"required id="passwordretype" name="passwordretype" placeholder="Enter Password" class="form-control passwordretype">
                                <div class="d-flex justify-content-center">
                                    <span class="align-middle m-2">
                                        <a href="#" class="showpassword2"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                    </span>
                                </div>
                                
                            </div> 
                            <small class="form-text text-muted">Note: Password must contain at least 8 characters long, alphanumeric, lowercase and uppercase.</small> 
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 mb-2">
                                <button disabled style="float:right" class="btn btn-primary saveChangePassBtn">Save</button>
                                </div>
                            </div>
                        </div>
                     </div>
                 </div>
             </form>
         </span>
     </div>
 </div>

</body>
     
 </body>
 <footer>
<script src="<?=base_url('assets/js/libs/admin/first_change_pass_reset.js')?>"></script>
<?php $this->load->view('templates/footer') ?>

<script>
	$(function () {
		$('[data-toggle="popover"]').popover();
	})

    $("#show_hide_password a").on('click', function() {
    if($('#show_hide_password input').attr("type") == "text"){
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password i').addClass( "fa-eye-slash" );
        $('#show_hide_password i').removeClass( "fa-eye" );
    }else if($('#show_hide_password input').attr("type") == "password"){
        $('#show_hide_password input').attr('type', 'text');
        $('#show_hide_password i').removeClass( "fa-eye-slash" );
        $('#show_hide_password i').addClass( "fa-eye" );
    }
  });
    $("#show_hide_password2 a").on('click', function() {
    if($('#show_hide_password2 input').attr("type") == "text"){
        $('#show_hide_password2 input').attr('type', 'password');
        $('#show_hide_password2 i').addClass( "fa-eye-slash" );
        $('#show_hide_password2 i').removeClass( "fa-eye" );
    }else if($('#show_hide_password2 input').attr("type") == "password"){
        $('#show_hide_password2 input').attr('type', 'text');
        $('#show_hide_password2 i').removeClass( "fa-eye-slash" );
        $('#show_hide_password2 i').addClass( "fa-eye" );
    }
  });

</script>
</body>
</html>