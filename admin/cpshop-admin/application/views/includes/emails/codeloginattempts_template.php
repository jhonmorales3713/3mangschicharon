<?php if(ini() == 'toktokmall'){ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    * {
        border: none;
        border-collapse: collapse;
    }
</style>
<body
		style="
			font-family: Arial, Helvetica, sans-serif;
			font-size:14px;
            background-image: url(<?= get_s3_imgpath_upload().'assets/img/toktokmall/email-background.png'?>);
			background-repeat: no-repeat;
			background-size: cover;
			height: 95vh;
		"
	>
	<table style="height: 100px">
		<tbody>
			<tr>
				<td>
					
				</td>
			</tr>
		</tbody>
	</table>
		<table
			style="
				width: 100%;
				border-collapse: collapse;
			"
		>
			<tbody>
				<tr>
					<td style="padding: 0 15px">
						<table
							style="
								width: 100%;
								max-width: 800px;
								margin: 0 auto;
								border-collapse: collapse;
								border-collapse: collapse;
								background-color: #fff;
								border-top-right-radius: 30px;
								border-top-left-radius: 30px;
							"
						>
							<tbody>
								<tr style="height: 100px; border: 0">
									<td style="padding: 40px 0px 0 40px">
										<h3 style="color:#F6841F; margin-top: 0;  width: 70%; font-size: 18px">Good day, ka-toktok!</h3>
									</td>
									<td style="padding: 40px 40px 0 0; width: 30%; text-align: right;">
										<img src="<?= get_s3_imgpath_upload().'assets/img/toktokmall/toktok-logo.png'?>" style="height: 30px" alt="toktok logo">
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<table style="width: 100%; border-collapse: collapse">
			<tbody>
				<tr>
					<td style="padding: 0 15px">
						<table
							style="
								width: 100%;
								max-width: 800px;
								margin: 0 auto;
								border-collapse: collapse;
								border-collapse: collapse;
								background-color: #fff;
							"
						>
							<tbody>
								<tr style="height: 100px; border: 0">
									<td style="padding: 20px 40px 0 40px">
										<table style="width: 100%">
											<tbody>
												<tr>
													<td>
														<!-- <p style="color: #F6841F; margin-bottom: 0; margin-top: 0;"><b>Email Address</b></p> -->
														<p style="color: #222; margin-top: 0;"> We detected an attempt to access your account, <?=$email;?>.</p>
													</td>
												</tr>
											</tbody>
										</table>
										<table style="width: 100%">
											<tbody>
												<tr>
													<td>
                                                        <p style="color: #F6841F; margin-bottom: 0; margin-top: 0;"><b>Time:</b><span style="color: #000;"> <?=$date_created;?></span></p>
														<!-- <p style="color: #222; margin-top: 0;">If that someone is you, <a class='btn btn-success' href='<?=$verify_href;?>'>CLICK HERE.</a></p> -->
													</td>
												</tr>
											</tbody>
										</table>
                                        <table style="width: 100%">
											<tbody>
												<tr>
													<td>
                                                       <p style="color: #F6841F; margin-bottom: 0; margin-top: 0;"><b>IP Address:</b><span style="color: #000;"> <?=$ip_address;?></span></p>
														<!-- <p style="color: #222; margin-top: 0;">This link will expire within 24 hours.If you are unaware, just ignore this message and nothing will be changed</p> -->
													</td>
												</tr>
											</tbody>
										</table>
                                        <table style="width: 100%">
											<tbody>
												<tr>
													<td>
                                                       <p style="color: #F6841F; margin-bottom: 0; margin-top: 0;"><b>Browser:</b><span style="color: #000;"> <?=$user_agent;?></span></p>
														<!-- <p style="color: #222; margin-top: 0;">This link will expire within 24 hours.If you are unaware, just ignore this message and nothing will be changed</p> -->
													</td>
												</tr>
											</tbody>
										</table>
                                        <table style="width: 100%">
											<tbody>
												<tr>
													<td>
                                                       <!-- <p style="color: #F6841F; margin-bottom: 0; margin-top: 0;"><b>Browser:</b><span style="color: #000;"> <?=$user_agent;?></span></p> -->
														<p style="color: #222; margin-top: 0;">To protect your account, we have blocked the login attempt. If this was an authorized login, please provide the below code on the login page.</p>
                                                        <h1 style="color: #FD7F20"><b><?=$login_code?></b></h1>
													</td>
												</tr>
											</tbody>
										</table>
										<table style="width: 100%">
											<tbody>
												<tr>
													<td style="padding: 50px 0 0 0">
														<i style="color:#525252;">Note: This is an auto-generated email. Please do not reply to this email thread.</i>
													</td>
												</tr>
											</tbody>
										</table>
										<table style="width: 100%">
											<tbody>
												<tr>
													<td>
														<p>
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
                <tr>
                <td style="padding: 0 15px">
                    <table
                        style="
                            width: 100%;
                            max-width: 800px;
                            margin: 0 auto;
                            border-collapse: collapse;
                            border-collapse: collapse;
                            background-color: #FCB71B;
                            border-bottom-right-radius: 30px;
                            border-bottom-left-radius: 30px;
                        "
                    >
                        <tbody>
                            <tr style="height: 100px; border: 0">
                                <td style="padding: 16px 40px 16px 40px">
                                    <table style="width: 100%;">
                                        <tbody>
											<tr>
                                                <td>
                                                    <p style="color: #fff; margin-bottom: 0; margin-top: 0; text-align: center; font-size: 14px"><b>CONNECT PEOPLE THROUGH DOOR-TO-DOOR DELIVERIES AND PROMOTE EASIER ONLINE SHOPPING.</b></p>
                                                    <a href="<?= faqs_link() ?>" style="color: #fff; margin-top: 0; text-decoration: underline; text-align:center; display: block; margin-top: 3px;">Help Centre</a>
                                                </td>
                                            </tr>
                                            <tr>
												<td style="text-align: center;">
												    <a href="<?= fb_link() ?>" target="_blank"><img style="margin-top: 12px;" src="<?= get_s3_imgpath_upload().'assets/img/toktokmall/fb.png'?>"></a>
                                                    <a href="<?= youtube_link() ?>" target="_blank"><img style="margin-top: 12px; margin-left: 4px" src="<?= get_s3_imgpath_upload().'assets/img/toktokmall/youtube.png'?>"></a>
													<a href="<?= ig_link() ?>" target="_blank"><img style="margin-top: 12px; margin-left: 4px" src="<?= get_s3_imgpath_upload().'assets/img/toktokmall/instagram.png'?>"></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
			</tbody>
		</table>
	</body>
</html>
<?php }else{ ?>

	<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title></title>
	<style>
		.order-status{
			font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color: lightgray;
		}
	</style>
</head>
<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css' rel='stylesheet' id='bootstrap-css'>
<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js'></script>
<script src='//code.jquery.com/jquery-1.11.1.min.js'></script>

<body data-base_url="<?=base_url();?>" style="font-family: Helvetica, sans-serif; ">
	<table border="0" style="margin: 100px auto;">
		<tbody>
			<tr>
				<td></td>
				<td style="text-align:center;" colspan="2"><label><img style="width: 300px;" src="<?=main_logo()?>"></label></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="4">
					<hr/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<br/>
					<!-- <b>Greetings from <?=get_company_name();?>,</b><p> -->
                    <p><br /> Hi, </p>
                    <p><br /> We detected an attempt to access your account, (<?=$email;?>)</p>
                    <label>-------------------------------</label><br>
                    <label>Time: <?=$date_created;?></label><br>
                    <label>IP Address: <?=$ip_address;?></label><br>
                    <label>Browser: <?=$user_agent;?></label><br>
                    <label>-------------------------------</label><br>
                    <p><br /> To protect your account, we have blocked the login attempt. If this was an authorized login, please provide the below code on the login page.</p>
                    <h1><b><?=$login_code?></b></h1>
				</td>
			</tr>
            <tr>
				<td colspan="4">
					<p>If you have any concerns, please do contact us at <?= get_company_email()?>. Thank you.</p>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br>
					<small><i>Note: This is an auto-generated email. Please do not reply to this email thread.</i></small>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<hr/>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
<?php } ?>