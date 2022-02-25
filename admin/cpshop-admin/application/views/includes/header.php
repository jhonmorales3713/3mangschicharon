<?php // matching the token url and the token session
if ($this->session->userdata('token_session') != en_dec("dec", $token)) {
    header("Location:" . base_url('Main/logout')); /* Redirect to login */
    exit();
}

header("X-Frame-Options: DENY");

//022818
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
?>

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

$fontChoice = "Montserrat"; //"Quicksand";
// Font Options:
// Roboto
// Roboto Condensed
// Open sans
// Lato
// Montserrat
// Oswald
// Raleway
// Poppins
// Ubuntu
// Nunito
// Fira Sans
// Fira Sans Condensed
// Quicksand
// PT Sans Narrow
// Barlow
// Yanone Kaffeesatz
// Cuprum
// Montserrat Alternates
// Arsenal

$fontLink;
$font;
switch ($fontChoice) {
    case "Roboto":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Roboto", sans-serif;';
        break;
    case "Roboto Condensed":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"Roboto Condensed", sans-serif;';
        break;
    case "Open sans":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700;800&display=swap" rel="stylesheet">';
        $font = '"Open Sans", sans-serif;';
        break;

    case "Lato":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Lato", sans-serif;';
        break;
    case "Montserrat":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Montserrat", sans-serif;';
        break;

    case "Montserrat Alternates":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Montserrat+Alternates:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Montserrat Alternates", sans-serif;';
        break;

    case "Oswald":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"Oswald", sans-serif;';
        break;

    case "Raleway":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Raleway", sans-serif;';
        break;

    case "Poppins":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Poppins", sans-serif;';
        break;

    case "Ubuntu":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"Ubuntu", sans-serif;';
        break;

    case "Nunito":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"Nunito", sans-serif;';
        break;

    case "Fira Sans":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Fira Sans", sans-serif;';
        break;

    case "Fira Sans Condensed":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Fira Sans Condensed", sans-serif;';
        break;

    case "Quicksand":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet" defer>';
        $font = '"Quicksand", sans-serif;';
        break;

    case "PT Sans Narrow":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"PT Sans Narrow", sans-serif;';
        break;

    case "Barlow":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;700;900&display=swap" rel="stylesheet">';
        $font = '"Barlow", sans-serif;';
        break;

    case "Yanone Kaffeesatz":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Yanone+Kaffeesatz:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"Yanone Kaffeesatz", sans-serif;';
        break;

    case "Cuprum":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Cuprum:ital,wght@0,400;1,700&display=swap" rel="stylesheet">';
        $font = '"Cuprum", sans-serif;';
        break;

    case "Arsenal":
        $fontLink = '<link href="https://fonts.googleapis.com/css2?family=Arsenal:wght@400;700&display=swap" rel="stylesheet">';
        $font = '"Arsenal", sans-serif;';
        break;
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=get_company_name();?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <!-- <link rel="stylesheet" href="<?//=base_url('assets/css/fonts.css');?>"> -->
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.min.css');?>">
    <!-- <link rel="stylesheet" href="<?//=base_url('assets/css/jquery-ui.css');?>"> -->
    <?=$fontLink;?>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet"> -->

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

    <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"> -->
    <!-- theme stylesheet--><!-- we change the color theme by changing color.css -->
    <link rel="stylesheet" href="<?=base_url('assets/css/style.blue.css');?>" id="theme-stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
    <!-- Custom stylesheet - for your changes-->
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/custom.css');?>"> -->
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?=favicon();?>">
    <!-- Font Awesome CDN-->
    <!-- you can replace it by local Font Awesome-->
    <link rel="stylesheet" href="<?=base_url('assets/css/font-awesome.min.css');?>">
    <!-- Font Icons CSS-->
    <!-- Jquery Datatable CSS-->
    <!-- <link rel="stylesheet" href="<?//=base_url('assets/css/datatables.min.css');?>"> -->
    <!-- Jquery Select2 CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css');?>">
    <!-- Jquery Toast CSS-->
    <link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/cp-toast.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/easy-autocomplete.css');?>">
    <!-- <link rel="stylesheet" href="<?=base_url('assets/css/mdb.min.css');?>"> -->
    <link rel="stylesheet" href="<?=base_url('assets/css/Chart.min.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/cropper.min.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/theme.css');?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/alertify.css'); ?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap-datepicker3.min.css');?>">
    <link href="<?=base_url('assets/fonts/fontawesome-free-5.14.0-web/css/fontawesome.css');?>">
    <link href="<?=base_url('assets/fonts/fontawesome-free-5.14.0-web/css/all.css');?>">

    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body data-base_url="<?=base_url();?>" data-token="<?=$token?>" data-shop_url="<?= shop_url(); ?>" data-s3bucket_url="<?= get_s3_imgpath_upload(); ?>" data-shop_id="<?=$this->session->userdata('sys_shop');?>" data-branch_id="<?=$this->session->userdata('branchid');?>" data-pusher_app_key="<?=get_pusher_app_key();?>" data-ini="<?=ini();?>" onload=display_ct();>
    <input type="hidden" id="csrf_hash" value="<?=$this->security->get_csrf_hash();?>">
    <input type="hidden" id="c_startup" value="<?=cs_clients_info()->c_startup;?>">
    <input type="hidden" id="c_jc" value="<?=cs_clients_info()->c_jc;?>">
    <input type="hidden" id="c_mcjr" value="<?=cs_clients_info()->c_mcjr;?>">
    <input type="hidden" id="c_mc" value="<?=cs_clients_info()->c_mc;?>">
    <input type="hidden" id="c_mcsuper" value="<?=cs_clients_info()->c_mcsuper;?>">
    <input type="hidden" id="c_mcmega" value="<?=cs_clients_info()->c_mcmega;?>">
    <input type="hidden" id="c_others" value="<?=cs_clients_info()->c_others;?>">
    <input type="hidden" id="c_ofps" value="<?=cs_clients_info()->c_ofps;?>">
    <div class="page charts-page">
        <!-- Main Navbar-->
        <header class="header w-100">
            <nav class="navbar w-100">
                <div class="header-time d-md-none">
                    <?php if(c_international() == 1){?>
                        <div class="col-xs-6">
                            <small  class="currentDateTime"></small>
                        </div>
                    <?php } else{?>
                        <div class="col-xs-6" hidden>
                            <small  class="currentDateTime"></small>
                        </div>
                    <?php }?>
                </div>
                <div class="container-fluid">
                    <div class="navbar-holder d-flex align-items-center justify-content-between w-100">
                        <!-- Navbar Header-->
                        <div class="navbar-header d-flex align-items-center">
                            <!-- Navbar Brand -->
                            <a href="#" id="menu-toggle" class="d-inline d-xl-none"><i class="fa fa-bars text-white fa-lg mr-2"></i></a>
                            <a href="#" class="navbar-brand"><!-- pwedeng link for shop page nila -->
                                <div class="brand-text brand-big hidden-lg-down d-none d-xl-block">
                                    <img src="<?=main_logo()?>" class="nav-logo">
                                </div>
                                <div class="brand-text brand-small d-block d-xl-none">
                                    <strong><img src="<?=main_logo()?>" class="nav-logo"></strong>
                                </div>
                            </a>
                        </div>
                        <!-- Navbar Menu -->
                        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                            <li class="dropdown open nav-item d-flex align-items-center"> <!-- unhide this if system notification is already setup -->
                                <a class="nav-link text-grey" data-toggle="dropdown" href="#" aria-expanded="true"><i class="fa fa-bell"></i> <span class="badge badge-primary" id="notif_count" style="display:none;">0</span></a>
                                <ul class="dropdown-menu dropdown-messages notifdropdown" id="notification_menu" style="background-color:#e2e8f0;">

                                </ul>
                            </li>
                            <li class="nav-item admin-user">
                                <?php if($this->loginstate->get_access()['overall_access'] == 1){ ?>
                                    <img src="<?=get_shop_url("assets/img/android-chrome-512x512.png")?>" alt="" class="navbar-company">
                                <?php } else {?>
                                    <img src="<?=get_s3_imgpath_upload().'assets/img/'.$this->session->userdata('shop_logo'); ?>" alt="" class="navbar-company">
                                <?php }?>
                                <!-- $this->session->userdata('shop_logo');  -->
                                <!-- <img src="<?//=favicon()?>" style="height: 30px; width: 30px"> -->
                                <?php if(c_international() == 1){?>
                                    <div class="col-xs-6 pt-3 d-none d-md-block">
                                        <small id='ct' class="currentDateTime" style="color:black;padding-right:15px;"></small>
                                    </div>
                                <?php } ?>
                                <div class="dropdown">
                                    <a class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php
                                            $avatar_default = $this->model->get_shop_logo();
                                            if($avatar_default != ""){
                                                $avatar_default = $avatar_default;
                                            }
                                            else{

                                            }
                                            $avatar_default = ($avatar_default != "") ? get_s3_imgpath_upload().'assets/img/shops/'.$avatar_default : base_url("assets/img/blank_avatar.png");
                                        ?>
                                        <img class="navbar-profile" src="<?=get_s3_imgpath_upload().'assets/uploads/avatars/'.$this->session->userdata('avatar');?>" onerror="this.onerror=null; this.src='<?=$avatar_default;?>'" class="img-fluid rounded-circle">
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <?php if($this->loginstate->get_access()['overall_access'] == 1){ ?>
                                            <?php if(ini() != 'jconlineshop'){ ?>
                                                <a class="dropdown-item" href="<?= shop_url(); ?>" target="_blank">
                                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                                    View Store
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if($this->loginstate->get_access()['seller_access'] == 1 || $this->loginstate->get_access()['seller_branch_access'] == 1){ ?>
                                            <?php if(ini() != 'jconlineshop'){ ?>
                                                <a class="dropdown-item" href="<?= get_shop_url('store/'.$this->session->userdata('shop_url')); ?>" target="_blank">
                                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                                    View Store
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                        <a class="dropdown-item" href="<?=base_url('Main_profile_settings/change_personal_information/'.$token);?>">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            Update Profile
                                        </a>
                                        <a class="dropdown-item" href="<?=base_url('Main_profile_settings/change_pass/'.$token);?>">
                                            <i class="fa fa-lock" aria-hidden="true"></i>
                                            Change Password
                                        </a>
                                        <!-- <a class="dropdown-item" href="<?//=base_url('Tips/tips_section/'.$token);?>">
                                            <i class="fa fa-info" aria-hidden="true"></i>
                                            Tips
                                        </a> -->
                                        <a class="dropdown-item" href="<?=base_url('Main/logout');?>">
                                            <i class="fa fa-sign-out fa-lg"></i>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- <li class="nav-item"><a href="" class="nav-link logout"><i class="fa fa-sign-out fa-lg"></i></a></li> -->
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="page-content d-flex align-items-stretch">
            <div id="overlay"></div>
            <!-- Side Navbar -->
            <nav id="sideNav" class="side-navbar sidebar-fixed position-fixed">
                <!-- Sidebar Header-->
                <div class="sidebar-top">
                    <?=get_company_name();?> Dashboard
                </div>
                <!-- <div class="sidebar-header d-flex align-items-center">
                    <div class="avatar">
                        <img src="<?//=base_url('assets/uploads/avatars/'.$this->session->userdata('avatar'));?>" onerror="this.onerror=null; this.src='<?//=base_url("assets/img/blank_avatar.png")?>'" alt="Display Profile" class="img-fluid rounded-circle">
                    </div>
                    <div class="title">
                        <h1 class="h4"><?//=$this->session->userdata('fname');?> <?//=$this->session->userdata('lname');?></h1>
                        <p></p>
                    </div>
                </div> -->
                <!-- Sidebar Navidation Menus-->
                <div class="side_nav_scroll">
                    <span class="heading">Main</span>
                    <?php $main_page_nav = $this->model->get_main_page_navigation()->result();?>

                    <ul class="list-unstyled pageNavigation list-group list-group-flush" id="pageNavigation">
                        <?php $arr_ = explode(', ', $access_nav); //array to string concut using comma ?>
                        <?php foreach ($main_page_nav as $mpn) {?>
                            <?php if (in_array($mpn->main_nav_id, $arr_)) {?>
                                <?php $functions = json_decode($this->session->functions);?>
                                <?php if($mpn->main_nav_desc == 'Wallet' && $functions->wallet == 1 && $functions->overall_access == 0):?>
                                  <?php
                                    $wallet = $this->model->get_shop_wallet($this->session->sys_shop_id,$this->session->branchid);
                                  ?>
                                  <?php if($wallet->num_rows() > 0 && $mpn->enabled == "1"):?>
                                    <li data-num="<?=$mpn->main_nav_id;?>" id="moduleLink" data-href="<?=base_url('Main_page/display_page/' . $mpn->main_nav_href . '/' . $token);?>">
                                      <a><i class="fa <?=$mpn->main_nav_icon;?>" aria-hidden="true"></i> <?=$mpn->main_nav_desc;?></a>
                                    </li>
                                  <?php endif;?>
                                <?php else:?>
                                  <?php if ($mpn->enabled == "1") {?>
                                    <li data-num="<?=$mpn->main_nav_id;?>" id="moduleLink" data-href="<?=base_url('Main_page/display_page/' . $mpn->main_nav_href . '/' . $token);?>">
                                      <a><i class="fa <?=$mpn->main_nav_icon;?>" aria-hidden="true"></i> <?=$mpn->main_nav_desc;?></a>
                                    </li>
                                  <?php } else if ($mpn->enabled == "1") {?>
                                    <li data-num="<?=$mpn->main_nav_id;?>" id="moduleLink" data-href="<?=base_url('Main_page/page_under_construction/' . $token);?>">
                                      <a><i class="fa <?=$mpn->main_nav_icon;?>" aria-hidden="true"></i> <?=$mpn->main_nav_desc;?></a>
                                    </li>
                                  <?php }?>
                                <?php endif;?>
                            <?php }?>
                        <?php }?>
                    </ul>
                    <span class="heading">Others</span>
                    <ul class="list-unstyled pageNavigation list-group list-group-flush">
                        <li data-num="99"><a href="<?=base_url('Main_page/display_page/profile_settings_home/' . $token);?>"> <i class="fa fa-user"></i>Profile Settings </a></li>
                        <li data-num="100"><a href="<?=base_url('notification/Notification/notifications/' . $token);?>"> <i class="fa fa-bell"></i>Notifications</a></li>
                    </ul>

                    <?php if($this->loginstate->get_access()['developer_settings'] == 1){?>
                        <span class="heading">CONTROL PANEL</span>
                        <ul class="list-unstyled pageNavigation list-group list-group-flush">
                            <?php foreach ($main_page_nav as $mpn) {?>
                                <?php if ($mpn->enabled == "2") {?>
                                    <li data-num="<?=$mpn->main_nav_id;?>">
                                        <a href="<?=base_url('Main_page/display_page/' . $mpn->main_nav_href . '/' . $token);?>"><i class="fa <?=$mpn->main_nav_icon;?>" aria-hidden="true"></i> <?=$mpn->main_nav_desc;?></a>
                                    </li>
                                <?php }?>
                            <?php }?>
                        </ul>
                    <?php } ?>

                </div>
            </nav>
            <main class="w-100">
                <div class="container-fluid"></div>
                 <div class="push-footer">
