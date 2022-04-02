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
    $tagline = cs_clients_info()->tagline;
    $c_phone = cs_clients_info()->c_phone;
    $font_choice = cs_clients_info()->font_choice;
    $fonts_link=base_url('assets/fonts');
    $name = cs_clients_info()->name;
    $contact_us = cs_clients_info()->c_contact_us;
    $terms_and_conditions = cs_clients_info()->c_terms_and_condition;
?>
<!DOCTYPE HTML>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        
        <meta name="description" content="<?=$name;?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="icon.png">
        <!-- Place favicon.ico in the root directory -->
        
        <link rel = "icon" href = "<?=base_url('assets/img/favicon.png')?>" type = "image/x-icon">
        <link rel="shortcut icon" href="<?=base_url('assets/img/favicon.png')?>">        
        <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery.toast.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery-ui.css')?>">        
        <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/bootstrap-4.1.3.min.css">
        <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/owl-carousel.min.css">    

        <?php if(strpos($_SERVER['REQUEST_URI'],'admin')){?>
            <link rel="stylesheet" href="<?=base_url('assets/css/admin/admin_styles.css')?>">
        <?php }else{?>
            <link rel="stylesheet" href="<?=base_url('assets/css/libs/header_styles.css')?>">
            <link rel="stylesheet" href="<?=base_url('assets/css/libs/content_styles.css')?>">
        <?php } ?>

        <link rel="stylesheet" href="<?=base_url('assets/css/libs/error_styles.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jconfirm.css')?>">

        
        <script src="<?= base_url();?>assets/js/libs/popper.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/bootstrap-datepicker.css')?>">

        <script src="<?=base_url('assets/js/libs/jquery.min.js')?>"></script>
	    <title><?=$name;?></title>
        <style>
            @font-face {
                font-family:  <?=$font_choice;?>;
                src: url(<?=base_url('assets/fonts/'.$font_choice.'-Regular.ttf');?>);
            }
            *
            {
                font-family : <?=$font_choice;?>
            }
            .btn-primary{
                background-color: <?=$button_primary_color;?>; /* Green */
                color: <?=$button_text_color?>;
            }
            header{
                margin-top:100px;
            }
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
        </style>
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
<body data-base_url="<?= base_url(); ?>" style="background-image: url(<?=base_url('assets/img/bg.jpg')?>); background-size: cover;">