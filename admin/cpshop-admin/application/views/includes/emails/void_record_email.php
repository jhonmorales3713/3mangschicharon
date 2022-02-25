<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Your order has been cancelled</title>
	</head>
	<body
		style="
			font-family: Arial, Helvetica, sans-serif;
			background-color: #edf0f8;
			font-size: 13px;
			padding: 0;
			margin: 0;
		"
	>
		<table
			style="
				width: 100%;
				background-color: #FDBA1C;
				border-collapse: collapse;
				background-image: url('<?=get_s3_imgpath_upload().'assets/img/toktokmall/bg_email.png'?>');
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
			"
		>
			<tbody>
				<tr>
					<td style="padding: 0 15px; height: 100px; text-align: center; padding-top: 150px; padding-bottom: 20px;">
						<img style="width: 100%; max-width: 400px;" src="<?=main_logo()?>" alt="">
					</td>
				</tr>
				<tr>
					<td style="padding: 0 15px">
						<table
							style="
								width: 100%;
								max-width: 600px;
								margin: 0 auto;
								border-collapse: collapse;
								border-collapse: collapse;
								background-color: #fff;
								border-top-left-radius: 7px;
								border-top-right-radius: 7px;
							"
						>
							<tbody>
								<tr style="border: 0">
									<td style="padding: 15px 30px 0; text-align: center;">
										<h2>Order has been Cancelled<span style="color: #F38220;"></span></h2>
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
								max-width: 600px;
								margin: 0 auto 50px;
								border-collapse: collapse;
								border-collapse: collapse;
								background-color: #fff;
								border-bottom-right-radius: 7px;
								border-bottom-left-radius: 7px;
							"
						>
							<tbody>
								<tr style="border: 0">
									<td style="padding: 0 30px 30px">
										<table style="width: 100%;">
											<tbody>
												<tr>
													<td>
														<p
														
														>
															<?php if ($name !="") {?>
															<strong>Hi, <span style="color: #F9B71A">ka-toktok <?= $name; ?></span>!</strong>
															<?php }?>
														</p>
													</td>
												</tr>
												<tr>
													<td>
														<p
															
														>
															We regret to notify you that your order at toktokmall with order reference number <?= $reference_num; ?> has been cancelled. Refund of your payment shall be processed accordingly. 
															<br><br>
															We sincerely apologize for any inconvenience this may have caused you. 
															<br><br>
															Thank you for your continued support.

														</p>
													
													</td>
												</tr>
											
											</tbody>
										</table>
										
										<table>
											<tbody>
												<tr>
													<td style="padding: 20px 0;">If you have any questions, please email us at <?=get_company_email();?> - we're glad to be of help.</td>
												</tr>
												<tr>
													<td>Cheers,</td>
												</tr>
												<tr>
													<td><strong style="color:#F38220">toktokmall Team</strong></td>
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
