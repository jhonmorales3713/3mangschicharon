<?php
$_SESSION['sesswebtraf'] = session_id();
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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/daterangepicker.css');?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url("assets/css/slick.css")?>"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/style-proto.css');?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/media-queries.css');?>">
    <!-- <link rel="stylesheet" type="text/css" href="<?//=base_url('assets/css/charts.css');?>"> -->
    <link rel="shortcut icon" type="image/png" href="<?=favicon()?>"/>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/slick.css');?>"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/slick-theme.css');?>"/>

    <!-- for jquery -->
    <script src="<?=base_url('assets/js/jquery.min.js');?>"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="<?=base_url("assets/js/slick.min.js")?>"></script>
    <!-- for jquery -->
    <script src="<?=base_url('assets/js/cmj_js/webtraf.js'); ?>"></script>
    <!-- for our own script -->

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
<body data-total_amount="<?=$total_amount;?>">


<script type="text/javascript">
    var total_amount = $("body").data('total_amount');
    $('#continueshop').click( function () {
        fbq("track", "Purchase", {currency: "PHP", value: total_amount, contents: order_content});
    })
</script>


</body>
</html>

