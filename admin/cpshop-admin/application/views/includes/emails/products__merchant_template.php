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
										<h3 style="color:#F6841F; margin-top: 0;  width: 70%; font-size: 18px">Good day, ka-toktok <?=$data['merchantname']?>!</h3>
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
														<p style="color: #222; margin-top: 0;">
																		This message is to notify you that the  <span  style="font-weight: 700;"><?=$data['itemname']?></span>  has been activated and ready for selling.  
														</p>
													</td>
												</tr>
											</tbody>
										</table>
										<table style="width: 100%">
											<tbody>
												<tr>
													<td>
														<!-- <p style="color: #F6841F; margin-bottom: 0; margin-top: 0;"><b>Email Address</b></p> -->
														<p style="color: #222; margin-top: 0;">Click <a style="color: blue;" href="<?=get_shop_url('store/'.$data['shopurl']);?>">here </a> to view your item.</p>
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
