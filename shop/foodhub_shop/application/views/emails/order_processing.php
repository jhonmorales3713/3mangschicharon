    <tr>
      <td colspan="4">
        <br />
        <b>Dear <?= $transaction['fullname']; ?>,</b>
        <p>
          Good day! Your payment for order #<?= $transaction['reference_num']; ?> has been confirmed. The seller(s) has also
          been notified to start processing your order and will update you once it's on its way. Thank you for shopping with
          us and hope to see you soon!</p>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <br />
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
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
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
            <td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/preparing-order.png'?>" alt="" style="height: 40px;"></td>
            <td style="width: 100%;"><strong>Preparing Order</strong></td>
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
                  <!-- Your order is now being prepared. -->
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
        <br />
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

            <tr>
              <td colspan="4"><b>Package # <?= $packageCnt ?></b> </td>
            </tr>
            <tr>
              <td colspan="4">Seller: <?= $shops['shopname']; ?></td>
            </tr>
            <tr>
              <td colspan="4">Email: <?= $shops['shopemail']; ?></td>
            </tr>
            <tr>
              <td colspan="4">Contact No: <?= $shops['shopmobile']; ?></td>
            </tr>
            <!-- <tr>
              <td colspan="4">Sub-total: <?= '' . number_format($shops['subTotalPerShop'], 2); ?></td>
            </tr> -->
            <?php if (isset($shops['vouchers']) && sizeof($shops['vouchers']) > 0) {?>
            <tr>
              <td colspan="4">Vouchers: <?= implode(", ", $shops['vouchers']); ?></td>
            </tr>
            <tr>
              <td colspan="4">Voucher Amount: <?= ''. number_format($shops['voucherSubTotal'], 2); ?></td>
            </tr>
            <tr>
              <td colspan="4">New Sub-total: <?php echo ''. number_format($shops["discounted"], 2); ?></td>
            </tr>
            <?php } ?>
            <?php
                if($shops['shippingfee'] != 0){
                  $shippingfee = ''.number_format($shops['shippingfee'], 2, ".", ",");
                }else{
                  $shippingfee = "Free Shipping";
                }
              ?>
            <!-- <tr>
              <td colspan="4">Shipping Fee: <?=$shippingfee;?></td>
            </tr> -->
            <tr>
              <td colspan="4">Estimated Shipping Date:
                <?php
                  $esd = "";
                  if($shops['shopdts'] == '0' && $shops['shopdts_to'] == '0'){
                    $esd = "Within 24 Hours";
                  }else if($shops['shopdts'] == $shops['shopdts_to']){
                    $esd = date("F d, Y", strtotime($transaction['payment_date']. ' + '.$shops['shopdts_to']. ' days'));
                  }else{
                    $esd = date("F d", strtotime($transaction['payment_date']. ' + '.$shops['shopdts'].' days')).' to '.date("F d, Y", strtotime($transaction['payment_date']. ' + '.$shops['shopdts_to']. ' days'));
                  }
                ?>
                <?=$esd?>
              </td>
            </tr>
            <?php foreach($shops['items'] as $items){ ?>
            <tr>
              <td></td>
              <td width="10%">
                <div style="padding-right:10px">
                  <a href="">
                    <!-- src="<?//= base_url("assets/img/products-250/webp/".$items['productid'].".webp") ?>" -->
                    <img
                      src="<?= base_url("assets/img/".$shops['shopcode']."/products-250/".$items['productid']."/0-".$items['productid'].".jpg") ?>"
                      onerror="this.onerror=null; this.src='<?=base_url("assets/img/products/".$items['productid'].".png")?>'"
                      style="width:100%;min-width:100px;max-width:160px" class="">
                  </a>
                </div>
              </td>
              <td colspan="2" width="100%">
                <div class="col-12 col-md-6 col-lg-12 mb-3">
                  <div class="portal-table__item">
                    <div class="portal-table__column col-12 col-lg portal-table__product"><?=$items['itemname'];?></div>
                    <div class="portal-table__column col-4 col-lg-2 portal-table__unit">
                      <span class="d-lg-none">Unit:</span> <?=$items['unit'];?>
                    </div>
                    <div class="portal-table__column col-12 col-lg-2 portal-table__price text-lg-right">Price:
                      <?=number_format($items['price'],2)?></div>
                    <div class="portal-table__column portal-table__quantity col-4 col-lg-2 text-right">
                      <span class="d-lg-none">Qty:</span> <?=number_format($items['quantity'])?>
                    </div>
                    <div
                      class="portal-table__column col-12 col-lg-2 portal-table__id portal-table__totalprice text-lg-right">
                      Total: <?=number_format(($items['price'] * $items['quantity']),2)?></div>

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
              <td>Sub-Total</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td align="left" colspan="2"><?=''.number_format($transaction['order_total_amt'], 2, ".", ","); ?></td>
            </tr>
            <?php if ($transaction['voucherAmount'] > 0): ?>
            <tr>
              <td>Voucher</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td align="left" colspan="2"> <?=''.number_format($transaction['voucherAmount'], 2, ".", ",").""; ?>
              </td>
            </tr>
            <?php endif; ?>
            <tr>
              <td>Shipping Fee</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <?php
                if($transaction['delivery_amount'] != 0){
                  $delivery_amount = ''.number_format($transaction['delivery_amount'], 2, ".", ",");
                }else{
                  $delivery_amount = "Free Shipping";
                }
              ?>
              <td align="left" colspan="2"> <?=$delivery_amount; ?></td>
            </tr>
            <tr>
              <td>Gateway Service Fee:</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td align="left" colspan="2"><?=number_format($transaction['payment_portal_fee'], 2);?>
              </td>
            </tr>
            <tr>
              <td>Total (VAT incl.)</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td align="left" colspan="2">
                <?php
                  $total_amount_w_voucher = (floatval($transaction['order_total_amt']) - floatval($transaction['voucherAmount']));
                  $total_amount_w_voucher = ($total_amount_w_voucher < 0) ? 0.00 : $total_amount_w_voucher;
                ?>
                <strong><?=''.number_format($total_amount_w_voucher+floatval($transaction['delivery_amount'])+floatval($transaction['payment_portal_fee']), 2, ".", ","); ?></strong>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <br />
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
              <td><?= format_fulldatetime($transaction['payment_date']); ?></td>
            </tr>
            <tr>
              <td>Payment Ref #</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['paypanda_ref'] ?></td>
            </tr>
            <tr>
              <td>Payment Method</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <?php if(!empty($transaction['payment_option'])){?>
                <td><?= $transaction['payment_method']; ?> - <?=payment_option($transaction['payment_option']);?></td>
              <?php }else{?>
                <td><?= $transaction['payment_method']; ?> - None</td>
              <?php }?>
            </tr>
           <!--  <tr>
              <td>Amount Paid</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td>
                <strong><?=''.number_format((floatval($transaction['order_total_amt']) - floatval($transaction['voucherAmount']))+floatval($transaction['delivery_amount']), 2, ".", ","); ?></strong>
              </td>
            </tr> -->
            <tr>
              <td>Payment Status</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <?php $transaction['payment_status'] = ($transaction['payment_status'] == 1) ? 'Paid' : $transaction['payment_status'];?>
              <td colspan="2"><?="<strong>".$transaction['payment_status']."</strong>"?></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <br>
        <p>To track your order, please <a href="<?= base_url().'check_order_details?refno=' . $transaction['reference_num']; ?>" target="_blank">click here.</a></p>
       <!--  <table width="100%" height="44" cellpadding="0" cellspacing="0" border="0" bgcolor="<?=primaryColor_accent()?>"
          style="border-radius:4px;">
          <tr>
            <td align="center" valign="middle" height="44" style="vertical-align: middle">
              <a href="<?= base_url().'check_order_details?refno=' . $transaction['reference_num']; ?>" target="_blank"
                style="color:white;">Track Order</a>
            </td>
          </tr>
        </table> -->
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <br>
        <p>If you have any concerns, please do contact us at <?= get_company_email()?>. Thank you.</p>
      </td>
    </tr>
