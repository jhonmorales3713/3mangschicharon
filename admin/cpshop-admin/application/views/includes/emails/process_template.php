<?php 
$date = new DateTime("now", new DateTimeZone('Asia/Manila') );
$timezone = $date->format('Y-m-d H:i:s A');
?>
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
					<b>Dear <?= $transaction['name']; ?>,</b><p>
          				Good day! A package from your order #<?= $transaction['reference_num']; ?> is now being prepared for shipping by the seller. Please check your order details below.</p>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br/>
					<b>Delivery Details</b>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table border='0'>
						<tbody>
							<tr>
								<td>Recipient</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td colspan="2"><?= $transaction['name']; ?></td>
							</tr>
							<tr>
								<td>Contact No.</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td colspan="2"><?= $transaction['conno']; ?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td colspan="2"><?= $transaction['email']; ?></td>
							</tr>
							<tr>
								<td>Address</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td colspan="2"><?= $transaction['address']; ?> </td>
							</tr>
							<?php if($transaction['notes'] != ''): ?>
								<tr>
									<td>Notes/Landmark</td>
									<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
									<td colspan="2"><?= $transaction['notes']; ?></td>
								</tr>
                    		<?php endif;?>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- ORDER STATUS TIMELINE -->
			<tr>
				<td></td>
				<td colspan="4">
					<br/>
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 100px; text-align: center;"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/confirmed-order-done.png'?>" alt="" style="height: 40px;"></td>
							<td style="width: 100%; color: #222;"><strong>Confirmed Order</strong></td>
						</tr>
					</tbody>
				</table>

				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 47px; border-right: 2px solid #222;"></td>
							<td style="width: 47px;"></td>
							<td style="width: 100%;">
								<ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 40px">
									<li style="list-style: none; color: #222;">
										We have verified your order
									</li>
									<li style="list-style: none; font-size: 12px; color: gray;">
									<?= date("M d, Y h:i a", strtotime($transaction['payment_date'])); ?>
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/preparing-order-done.png'?>" alt="" style="height: 40px;"></td>
							<td style="width: 100%; color: #222;"><strong>Preparing Order</strong></td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 47px; border-right: 2px solid lightgray"></td>
							<td style="width: 47px;"></td>
							<td style="width: 100%;">
								<ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 40px">
									<li style="list-style: none; color: #222">
										Your order is now being prepared.
									</li>
									<li style="list-style: none; font-size: 12px; color: gray">
									<?= date("M d, Y h:i a", strtotime($transaction['date_order_processed'])); ?>
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/ready-for-pickup.png'?>" alt="" style="height: 40px;"></td>
							<td style="width: 100%;"><strong>Ready for Pickup</strong></td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 47px; border-right: 2px solid lightgray"></td>
							<td style="width: 47px;"></td>
							<td style="width: 100%;">
								<ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 40px">
									<li style="list-style: none;">
										<!-- Your order is now being coordinated with our delivery provider -->
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/fulfilled.png'?>" alt="" style="height: 40px;"></td>
							<td style="width: 100%;"><strong>Fulfilled</strong></td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 47px; border-right: 2px solid lightgray;"></td>
							<td style="width: 47px;"></td>
							<td style="width: 100%;">
								<ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 40px">
									<li style="list-style: none;">
										<!-- Item has been picked up and is now being delivered. -->
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="order-status">
					<tbody>
						<tr>
							<td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/delivered.png'?>" alt="" style="height: 40px;"></td>
							<td style="width: 100%;"><strong>Delivered</strong></td>
						</tr>
						<tr>
							<td colspan="2" style="width: 100%;">
								<ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 40px">
									<li style="list-style: none;">
										<!-- &nbsp;&nbsp;&nbsp;&nbsp;Order has been successfully shipped. -->
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
				<td></td>
				<td></td>
			</tr>
			<!-- END ORDER STATUS TIMELINE -->
			<tr>
				<td colspan="4">
					<br/>
					<b>Order Details</b>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table border='0' style="width:100%;">
						<tbody>
                    <?php $packageCnt = 1;
                    	foreach($transaction['shopItems'] as $shops){ 
                    	
                    	?>

              				<!-- <tr>
								<td colspan="4"><b>Package # <?= $packageCnt ?></b> </td>
              				</tr> -->
							<?php 
								$shopname = ($shops['branchname'] == "Main") ? $shops['shopname'] : $shops['branchname'];
							?>
              				<tr>
								<td colspan="4">Seller: <?= $shopname;?></td>
              				</tr>
              				<tr>
								<td colspan="4">Email: <?= $shops['shopemail']; ?></td>
              				</tr>
              				<tr>
								<td colspan="4">Contact No: <?= $shops['shopmobile']; ?></td>
              				</tr>
              				<tr>
							  	<?php if($shops['shopdts'] == $shops['shopdts_to']){?>
									<td colspan="4">Delivered On: <?= date("F d, Y", strtotime($transaction['payment_date']. ' + '.$shops['shopdts'].' days'))?></td>
								<?php }else{?>
								  	<td colspan="4">Delivered On: <?= date("F d", strtotime($transaction['payment_date']. ' + '.$shops['shopdts'].' days')).' to '.date("F d, Y", strtotime($transaction['payment_date']. ' + '.$shops['shopdts_to']. ' days')) ?></td>
								<?php }?>
              				</tr>
						<?php 
							$total_amount = 0;
							$total_amount_conv = 0;
						?>
                    	<?php foreach($shops['items'] as $items){ ?>
						<?php $primary_pic = $this->model_orders->get_primarypics3($items['productid']);?>
                    		<tr>
                    			<td></td>
                    			<td width="10%">
									<div style="padding-right:10px">
		                              <a href=""><img src="<?= get_s3_imgpath_upload()."assets/img/".$shops['shopcode']."/products-250/".$items['productid']."/".removeFileExtension($primary_pic).".jpg" ?>" style="width:100%;min-width:100px;max-width:160px" class=""></a>
									</div>
		                        </td>
                    			<td colspan="2" width="100%;">
			                        <div class="col-12 col-md-6 col-lg-12 mb-3">
			                            <div class="portal-table__item">
			                                <div class="portal-table__column col-12 col-lg portal-table__product"><?=$items['itemname'];?></div>
			                                <div class="portal-table__column col-4 col-lg-2 portal-table__unit">
			                                <span class="d-lg-none">Unit:</span> <?=$items['unit'];?>
			                                </div>
											<?php if(c_international() == 1){
												$item_amount = currencyConvertedRate($items['price'], $transaction['currval']);
											?>
			                                	<div class="portal-table__column col-12 col-lg-2 portal-table__price text-lg-right">Price:  <?=displayCurrencyValue($items['price'], $item_amount, $transaction['currcode'])?></div>
											<?php } else{?>
			                                	<div class="portal-table__column col-12 col-lg-2 portal-table__price text-lg-right">Price:  <?=number_format($items['price'],2)?></div>
											<?php }?>
			                                <div class="portal-table__column portal-table__quantity col-4 col-lg-2 text-right">
			                                    <span class="d-lg-none">Qty:</span> <?=number_format($items['quantity'])?>
			                                </div>
											<?php if(c_international() == 1){
												$item_total_amount   = currencyConvertedRate_peritem($items['price'], $transaction['currval'], $items['quantity']);
												$total_amount_conv  += $item_total_amount;
											?>
			                                	<div class="portal-table__column col-12 col-lg-2 portal-table__id portal-table__totalprice text-lg-right">Total: <?=displayCurrencyValue($items['price'] * $items['quantity'], $item_total_amount, $transaction['currcode'])?></div>
											<?php } else{?>
												<div class="portal-table__column col-12 col-lg-2 portal-table__id portal-table__totalprice text-lg-right">Total: <?=number_format(($items['price'] * $items['quantity']),2)?></div>
											<?php } ?>
											<?php $total_amount += $items['price'] * $items['quantity'];?>
			                            </div>
			                        </div>
	                    		</td>
	                    	</tr>
                    	<?php } $packageCnt++; ?>
                    <?php } ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table border='0'>
						<tbody>
							<tr>
								<td>Total Amount</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<?php if(c_international() == 1){?>
									<td colspan="2"><?="<strong>".displayCurrencyValue($total_amount, $total_amount_conv, $transaction['currcode'])."</strong>"?></td>
								<?php }else{?>
									<td colspan="2"><?="<strong>".number_format($total_amount, 2)."</strong>"?></td>
								<?php }?>
							</tr>
							<tr>
								<td>Payment Status</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td colspan="2"><?="<strong>".$transaction['payment_status']."</strong>"?></td>
							</tr>
							<?php if(c_international() == 1){?>
								<tr>
									<td>Time Zone</td>
									<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
									<td colspan="2"><?="<strong>".date('D, d M Y H:i:s \G\M\TO')."</strong>"?></td>
								</tr>
							<?php }?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br>
					<br>
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
