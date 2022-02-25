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
          				Good day! Your payment for order #<?= $transaction['reference_num']; ?> has been confirmed. The seller will start processing your order and will update you once it's on its way. Thank you for shopping with us and hope to see you soon!</p>
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
					<table border='0' width="100%">
						<tbody width="100%" style="margin-left: auto; margin-right: auto;">
                    <?php $packageCnt = 1;
                    	foreach($transaction['shopItems'] as $shops){ 
                    	
                    	?>

              				<!-- <tr>
								<td colspan="4"><b>Package # <?= $packageCnt ?></b> </td>
              				</tr> -->
              				<tr>
								<td colspan="4">Seller: <?= $shops['shopname']; ?> (<?= $shops['shopcode']; ?>)</td>
              				</tr>
              				<tr>
								<td colspan="4">Email: <?= $shops['shopemail']; ?></td>
              				</tr>
              				<tr>
								<td colspan="4">Contact No: <?= $shops['shopmobile']; ?></td>
              				</tr>

              				<tr>
							  <td colspan="4">Delivered On: <?= date("F d", strtotime($transaction['payment_date']. ' + '.$shops['shopdts'].' days')).' to '.date("F d, Y", strtotime($transaction['payment_date']. ' + '.$shops['shopdts_to']. ' days')) ?></td>
              				</tr>
                    	<?php $subtotal = 0; foreach($shops['items'] as $items){  ?>
                    		<tr>
                    			<td></td>
                    			<td width="40%">
									<div style="padding-right:10px">
		                              <a href=""><img src="<?= get_shop_url("assets/img/".$shops['shopcode']."/products-250/".$items['productid']."/0-".$items['productid'].".jpg") ?>" style="width:100%;max-width:160px" class=""></a>
									</div>
		                        </td>
                    			<td colspan="2" width="60%">
			                        <div class="col-12 col-md-6 col-lg-12 mb-3">
			                            <div class="portal-table__item">
			                                <div class="portal-table__column col-12 col-lg portal-table__product"><?=$items['itemname'];?></div>
			                                <div class="portal-table__column col-4 col-lg-2 portal-table__unit">
			                                <span class="d-lg-none">Unit:</span> <?=$items['unit'];?>
			                                </div>
			                                <div class="portal-table__column col-12 col-lg-2 portal-table__price text-lg-right">Price:  <?=number_format($items['price'],2)?></div>
			                                <div class="portal-table__column portal-table__quantity col-4 col-lg-2 text-right">
			                                    <span class="d-lg-none">Qty:</span> <?=number_format($items['quantity'])?>
			                                </div>
			                                <div class="portal-table__column col-12 col-lg-2 portal-table__id portal-table__totalprice text-lg-right">Total: <?=number_format(($items['price'] * $items['quantity']),2)?></div>

			                            </div>
			                        </div>
	                    		</td>
	                    	</tr>
                    	<?php $subtotal += ($items['price'] * $items['quantity']); } $packageCnt++; ?>
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
								<td>Sub-Total</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td align="right" colspan="2"><?='PHP '.number_format($subtotal, 2, ".", ","); ?></td>
							</tr>
							<tr>
								<td>Shipping Fee</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td align="right" colspan="2"> <?='PHP '.number_format($transaction['delivery_amount'], 2, ".", ","); ?></td>
							</tr>
							<tr>
								<td>Total (VAT incl.)</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td align="right" colspan="2"><strong><?='PHP '.number_format(floatval($subtotal)+floatval($transaction['delivery_amount']), 2, ".", ","); ?></strong></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br/>
					<b>Payment Details</b>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table border='0'>
						<tbody>
              				<tr>
								<td>Payment Date</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td ><?= todaytime(); ?></td>
              				</tr>
							<tr>
								<td>Payment Method</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td ><?= $transaction['payment_method']; ?></td>
							</tr>
							<tr>
								<td>Amount Paid</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td ><strong><?='PHP '.number_format(floatval($subtotal)+floatval($transaction['delivery_amount']), 2, ".", ","); ?></strong></td>
				              </tr>
							<tr>
								<td>Payment Status</td>
								<td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
								<td colspan="2"><?="<strong>".$transaction['payment_status']."</strong>"?></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
          <br/>
					<br><br>
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
