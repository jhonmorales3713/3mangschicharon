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
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/bootstrap.min.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/fontawesome/css/all.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery.toast.css')?>">
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/jquery-ui.css')?>">        
        <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/bootstrap-4.1.3.min.css">
        <link rel="stylesheet" href="<?=base_url();?>assets/css/libs/owl-carousel.min.css">        
        <link rel="stylesheet" href="<?=base_url('assets/css/libs/header_styles.css')?>">    
        <script src="<?php echo base_url();?>assets/js/libs/popper.min.js"></script>
        <title><?=$name.' - '.$tagline;?></title>
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
<nav class="navbar navbar-expand-md navbar-dark fixed-top" style="background-color: <?=$header_bg_color;?> !important;"> <!-- header navigation - put styles here -->
    <?php if(isset($this->session->chapter_id)){ ?>
        <a class="navbar-brand" href="#"><img width="50" height="50" src="<?=base_url('uploads/chapters/chapter_logo/'.$this->session->chapter_logo);?>"><span class="h4" style="margin-left: 10px;">JCI<?php if(isset($_SESSION['chapter'])){echo'  '. $_SESSION['chapter'];} ?></span></a>
    <?php } else { ?>
        <a class="navbar-brand" href="#"><img width="50" height="50" src="<?=base_url('assets/img/logo.png')?>"><span class="h4" style="margin-left: 10px;"><?=$name?><?php if(isset($_SESSION['chapter'])){echo' - '. $_SESSION['chapter'];} ?></span></a>
    <?php } ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <?php if(isset($_SESSION['chapter_id'])){?>
                    <a class="nav-link p-2" href="<?= 'Chapter?chapter='.$_SESSION["chapter"]; ?>" id="landing">Home</a>
                <?php }else{?>
                    <!-- <a class="nav-link p-2" href="<?= $home_link ?>" id="landing">Home</a> -->
                <?php } ?>
            </li>
            <li class="nav-item">
                <a class="nav-link p-2" href="<?= base_url('about'); ?>" id="about">About Us</a>
            </li>
            <?php if(!isset($_SESSION['is_logged_in'])){ ?>
                <li class="nav-item">
                    <a class="nav-link p-2" href="<?= base_url('chapter/propose'); ?>" id="login">Chapter Application</a>
                </li>           
                <li class="nav-item bg-primary rounded">
                    <a class="nav-link p-2 text-white" href="<?= base_url('login'); ?>">Login</a>
                </li>
            <?php }else{ ?>
                <li class="nav-item">
                    <a class="nav-link p-2" href="<?= base_url('events'); ?>" id="events">Events</a>
                </li>           
                <li class="nav-item">
                    <a class="nav-link p-2" href="<?= base_url('members'); ?>" id="member">Members</a>
                </li>           
                <li class="nav-item">
                    <a class="nav-link p-2" href="<?= base_url('EventPropose'); ?>" id="eventpropose">Propose Event</a>
                </li>           
                <li class="nav-item dropleft bg-primary rounded">
                    <a class="nav-link p-2 dropdown-toggle text-white form-inline" data-toggle="dropdown" href="#"><?=$_SESSION['username'];?></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="<?php echo base_url('members?view='.$_SESSION['id']);?>">Account Profile</a>
                        <a class="dropdown-item" href="<?php echo base_url('Login')?>">Logout</a>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>
<header>
<!-- Fixed navbar -->
</header>