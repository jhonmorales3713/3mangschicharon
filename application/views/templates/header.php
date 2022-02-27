<?php
    //if ($this->session->userdata('token_session') != en_dec("dec", $token)) {
        //header("Location:" . base_url('Main/logout')); /* Redirect to login */
        //exit();
    //}
    
    $access_nav = $this->session->userdata('access_nav');

    $company_logo = base_url('assets/img/pandabooks.png');
    $company_logo_small = base_url('assets/img/pandabookslogo.png');
    // avatar
    $avatar_img = base_url("assets/avatar/cp-panda-only.png");
    $avatar_file = $this->session->userdata('avatar_file');
    if (!empty($avatar_file)) {
        $avatar = base_url(avatar_folder_url() . '/' . $avatar_file);
        if (file_exists($avatar)) {
            $avatar_img = $avatar;
        }

    }
    $primary_color = cs_clients_info()->primary_color;
    $button_radius_size = cs_clients_info()->button_radius_size;
    $button_text_color = cs_clients_info()->button_text_color;
    $button_primary_color = cs_clients_info()->button_primary_color;
    $header_bg_color = cs_clients_info()->header_bg_color;
    $middle_bg_color = cs_clients_info()->middle_bg_color;
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
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/fontawesome/css/all.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery.toast.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery-ui.css')?>">        
        <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/bootstrap-4.1.3.min.css">
        <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/owl-carousel.min.css">        
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/header_styles.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/content_styles.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/error_styles.css')?>">
        <script src="<?= base_url();?>assets/js/libs/popper.min.js"></script>
        <script src="<?= base_url('assets/js/libs/jquery.min.js') ?>"></script>       
        <script src="<?= base_url('assets/js/libs/helper_functions.js') ?>"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <title>3Mangs Chicharon</title>
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
        </style>
</head>
<body data-base_url="<?= base_url(); ?>" style="background-image: url(<?=base_url('assets/img/bg.jpg')?>); background-size: cover;">