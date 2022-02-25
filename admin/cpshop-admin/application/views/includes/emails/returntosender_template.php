<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title></title>
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
          				Our rider attempted to deliver your order #<?= $transaction['reference_num']; ?>. Order has been returned to seller and will be reschedule for next delivery.</p>
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
