
<?php
    //if ($this->session->userdata('token_session') != en_dec("dec", $token)) {
        //header("Location:" . base_url('Main/logout')); /* Redirect to login */
        //exit();
    //}
    
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
                            
            <form id="form-login">
                <div class="card " style="width: 20rem;">
                    <div class="card-body">
                        <div class="w-100 text-center">
                            <img class="logo_login" src="<?=base_url('assets/img/'.$c_main_logo);?>" width=100%>
                        </div>
                        <br>
                        <h5 class="card-title">Invalid URL</h5>
                        <div class="form-group">
                            <label for="login-form">You are accessing an unknown url. Please go back.</label>
                        </div>
                </div>
            </form>
        </span>
    </div>
</div>