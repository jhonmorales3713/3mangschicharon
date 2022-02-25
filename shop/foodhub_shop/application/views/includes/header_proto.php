<?php
$_SESSION['sesswebtraf'] = session_id();

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

$fontChoice = fontChoice(); //"Quicksand";
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
<head lang="en" data-fb_pixel_id="<?=fb_pixel_id();?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="google-site-verification" content="<?=google_site_verification();?>">
    <meta property="og:title" content=<?=get_company_name();?> />
    <meta property="og:site_name" content="CP" />
    <meta property="og:image" content="<?=fb_image()?>"/>
    <title><?=get_company_name();?></title>
    <?=$fontLink;?>

    <link href="<?=base_url('assets/css/font-awesome.min.css');?>" rel="stylesheet">
    <link href="<?=base_url('assets/css/bootstrap.min.css');?>" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/daterangepicker.css');?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url("assets/css/slick.css")?>"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/style-proto.css');?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/media-queries.css');?>">
    <!-- <link rel="stylesheet" type="text/css" href="<?//=base_url('assets/css/charts.css');?>"> -->
    <link rel="shortcut icon" type="image/png" href="<?=favicon()?>"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/slick.css');?>"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/slick-theme.css');?>"/>
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
    <script>
        const fb_pixel_id = document.getElementsByTagName("head")[0].getAttribute("data-fb_pixel_id");

        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', fb_pixel_id);
    </script>
    <?php if (fb_pixel_id() != "") { ?>
        <noscript>
            <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?=fb_pixel_id()?>&ev=PageView&noscript=1"/>
        </noscript>
    <?php } ?>
</head>
<body data-base_url="<?=base_url();?>" data-placeholder_img="<?=placeholder_img();?>">

<?php $this->load->view('includes/cover');?>
<header>
    <?php if($this->session->userdata("referral_disname") != "") { ?>
    <div class="header__top">
        <div class="container">
            <span class="highlight">Hello!</span> Welcome to <?=$this->session->userdata("referral_disname");?>'s Shoplink.
        </div>
    </div>
    <?php } ?>
    <?php if(shop_main_announcement() != "") { ?>
    <div class="announcement-container">
        <div class="container">
            <div class="announcement-banner">
                <span class="announcement-text"><?=shop_main_announcement();?></span>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="header__upper">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3">
                    <a href="<?=base_url()?>">
                        <img
                            class="header-logo"
                            src="<?=main_logo()?>"
                            onerror="this.onerror=null; this.src='<?=main_logo()?>'"
                            alt="Main Logo"
                        />
                    </a>
                </div>
                <div class="col d-flex align-items-center justify-content-end">
                    <div class="upper__content">
                        <div class="d-none d-md-block">
                            <a href="<?=base_url()?>" class="ml-3 upper__item">Shop</a>
                            <a href="<?=contact_us();?>" class="ml-3 upper__item">Customer Care</a>
                            <?php if (!empty(get_seller_reg_form())): ?>
                                 <a href="<?=get_seller_reg_form()?>" target="_blank" class="ml-3 upper__item">Be a Seller</a>
                            <?php endif ?>
                            <a href="<?=base_url('check_order')?>" class="ml-3 upper__item">Track Order</a>
                            <?php if (allow_login() == 1): ?> <!-- //validation for allow login -->
                                <?php if(empty($this->session->userdata("user_id"))) { ?>
                                    <a href="<?=base_url('user/login')?>" class="ml-3 upper__item">Sign In</a>
                                    <?php if (allow_registration() == 1): ?>
                                        <a href="<?=base_url('user/register')?>" class="ml-3 upper__item">Register</a>
                                    <?php endif ?>
                                <?php } else { ?>
                                    <a href="<?=base_url('user/logout')?>" class="ml-3 upper__item">Sign Out</a>
                                <?php } ?>
                            <?php endif ?>
                        </div>
                        <div class="d-md-none text-right">
                            <span class="sidemenu">
                                <i class="fa fa-bars fa-2x"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header__middle">
        <div class="container">
            <div class="row">
                <div class="col col-lg-6 d-flex align-items-center">
                    <div class="search-container w-100">
                        <div class="search">
                            <!-- <form action=""> -->
                                <div class="search__content">
                                    <div class="mb-0">
                                        <input type="text" id="search__input" class="search__input autocomplete search__input_proto" autocomplete="off" placeholder="Search...">
                                    </div>
                                    <button class="btn portal-primary-btn search__button" id="search__button_proto"><i class="fa fa-search no-margin"></i></button>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
                <div class="col-auto ml-auto d-flex align-items-center cartHolder">
                    <?php if(empty($this->session->userdata("user_id")) || allow_login() == 0) { ?>
                        <a href="" class="mr-3 middle__user"><i class="fa fa-user-circle mr-2"></i>Guest</a>
                    <?php } else { ?>
                        <a href="<?=base_url('user/profile')?>" class="mr-3 middle__user"><i class="fa fa-user-circle mr-2"></i><?= $this->session->userdata("fname");?></a>
                    <?php } ?>
                    <?php
                        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                        $uri_segments = explode('/', $uri_path);
                    ?>
                    <?php $segment = $uri_segments[count($uri_segments) - 1] ;?>
                    <?php if ($segment != "checkout" && $segment != "returnurl") { ?>
                        <div class="header__item cart__menu cart__notif">
                            <i class="fa fa-shopping-cart"><div class="cart__notif__number"><span class="cart_count"></span></div></i>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="header__bottom">
        <div class="container">
            <div class="row">
                <?php if ($categories != null) {?>
                <div class="col-5 col-md-12">
                    <?php
                        foreach ($categories as $category) {?>
                            <a href="#search=<?=$category['category_name'];?>" class="header__category__item category_nav"><span><?=$category['category_name']?></span></a>
                        <?php } ?>
                </div>
                <?php }?>
                <div class="col d-md-none">
                    <a href="<?=base_url()?>" class="header__category__item ml-3">
                        <div class="row">
                            <div class="col-1">
                                <i class="fa fa-shopping-bag"></i>
                            </div>
                            <div class="col font-weight-bold">
                                Shop
                            </div>
                        </div>
                    </a>
                    <a href="<?=contact_us();?>" class="header__category__item ml-3">
                        <div class="row">
                            <div class="col-1">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="col font-weight-bold">
                                Customer Care
                            </div>
                        </div>
                    </a>
                    <?php if (!empty(get_seller_reg_form())): ?>
                        <a href="<?=get_seller_reg_form()?>" target="_blank" class="header__category__item ml-3">
                            <div  div class="row">
                                <div class="col-1">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="col font-weight-bold">
                                    Be a Seller
                                </div>
                            </div>
                        </a>
                    <?php endif ?>
                        <a href="<?=base_url('check_order')?>" class="header__category__item ml-3">
                            <div  div class="row">
                                <div class="col-1">
                                    <i class="fa fa-map-pin"></i>
                                </div>
                                <div class="col font-weight-bold">
                                    Track Order
                                </div>
                            </div>
                        </a>
                    <?php if (allow_login() == 1): ?> <!-- //validation for allow login -->
                        <?php if(empty($this->session->userdata("user_id"))) { ?>
                            <a href="<?=base_url('user/login')?>" class="header__category__item ml-3">
                                <div  div class="row">
                                    <div class="col-1">
                                        <i class="fa fa-sign-in"></i>
                                    </div>
                                    <div class="col font-weight-bold">
                                        Sign In
                                    </div>
                                </div>
                            </a>
                            <?php if (allow_registration() == 1): ?>
                                <a href="<?=base_url('user/register')?>" target="_blank" class="header__category__item ml-3">
                                    <div  div class="row">
                                        <div class="col-1">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </div>
                                        <div class="col font-weight-bold">
                                            Register
                                        </div>
                                    </div>
                                </a>
                            <?php endif ?>
                        <?php } else { ?>
                            <a href="<?=base_url('user/logout')?>" class="header__category__item ml-3">
                                <div  div class="row">
                                    <div class="col-1">
                                        <i class="fa fa-sign-out"></i>
                                    </div>
                                    <div class="col font-weight-bold">
                                        Sign Out
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="cart">
    <h2 class="cart__title page-title mb-5">
        <i class="fa fa-shopping-cart mr-3"></i>
        Cart
        <i class="fa fa-times cart__close-icon"></i>
    </h2>
    <div class="portable-table__container cart__table" id="headerCart">
    </div>
    <div class="cart__footer">
        <h4 class="cart-table__total col" id="total_amount">
        </h4>
        <div class="cart__button">
            <button id="shop_proceed" class="btn portal-primary-btn col">Proceed to Checkout</button>
        </div>
    </div>
</div>

<div class="modal fade" style="margin-top: 100px;" id="changeBranchModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">There are items in your cart, do you wish to proceed changing branch and remove all cart items?</h6>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg" id="changeBranchModalYes">Yes</button>
                <button type="submit" class="btn btn-danger btn-lg" id="changeBranchModalNo">No</button>
            </div>
        </div>
    </div>
</div>
<main class="container">
