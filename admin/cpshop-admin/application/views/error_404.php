<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
if( ! isset($CI))
{
    $CI = new CI_Controller();
}
$CI->load->helper('url');

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<link rel="stylesheet" href="<?=base_url("assets/css/bootstrap.min.css")?>">
<link rel="shortcut icon" type="image/png" href="<?=base_url('assets/img/logos/PandaBooks-ico.png'); ?>"/>
<style type="text/css">

* {
	font-family: 'Fira Sans', san serif;
}

.logo {
	width: 250px;
}

.btn {
	width: 20%;
	font-size: 15px;
}

.btn-home-error {
	border: 1px solid var(--primary-color);
	border-radius: 30px;
	color: #222;
	padding: 10px 20px;
}

.btn-home-error:hover {
	color: #fff;
	background-color: var(--primary-color);
}

html, body, .container, .row {
	height: 100% !important;
}

</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="offset-lg-2 col-lg-8 col-12 text-center my-auto">
				<img src="<?php echo base_url("assets/img/graphics/panda-error.png") ?>" alt="" class="mb-4 w-75">
				<h3>Oops, you've encountered an error!</h3>
				<h6>It appears the page you were looking for doesn't exists. Sorry about that.</h6>
				<div class="text-center pt-3">
					<a href="<?php echo base_url();?>" class="btn btn-home-error">Home</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>