    <tr>
      <td colspan="4">
        <br />
        <b>Hello <?= $transaction['shopItems'][$shop]["shopname"]; ?>,</b>
        <p>
          This is to notify you that <?= $transaction['name']; ?> has placed an
          order(#<?= $transaction['reference_num']; ?>) with your store on
          <?= format_fulldatetime($transaction['payment_date']); ?>.
        </p>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <br />
        <b>Shipping Details</b>
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
            <?php foreach($transaction['shopItems'][$shop]['items'] as $items){ ?>
            <tr>
              <td></td>
              <td width="10%">
                <div style="padding-right:10px">
                  <a href="">
                    <!-- src="<?//= base_url("assets/img/products-250/webp/".$items['productid'].".webp") ?>" -->
                    <img
                      src="<?= base_url("assets/img/".$shopcode."/products-250/".$items['productid']."/0-".$items['productid'].".jpg") ?>"
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
            <?php }  ?>
            </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="4">
         <table border='0'>
          <tbody>
            <tr>
              <td>Sub-total</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= '' . number_format($transaction['shopItems'][$shop]['subTotalPerShop'], 2); ?></td>
            </tr>
            <?php if (isset($transaction['shopItems'][$shop]['vouchers']) && sizeof($transaction['shopItems'][$shop]['vouchers']) > 0) {?>
            <tr>
              <td>Vouchers</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= implode(", ", $transaction['shopItems'][$shop]['vouchers']); ?></td>
            </tr>
            <tr>
              <td>Voucher Amount</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= ''. number_format($transaction['shopItems'][$shop]['voucherSubTotal'], 2); ?></td>
            </tr>
            <?php
              $total_amount_w_voucher = floatval($transaction['shopItems'][$shop]['subTotalPerShop']) - floatval($transaction['shopItems'][$shop]['voucherSubTotal']);
              $total_amount_w_voucher = ($total_amount_w_voucher < 0) ? 0 : $total_amount_w_voucher;
            ?>
            <tr>
              <td>New Sub-total</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= ''. number_format($total_amount_w_voucher, 2); ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td>Shipping Fee</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= ''. $transaction['shopItems'][$shop]['shippingfee']; ?></td>
            </tr>
            <tr>
              <td>Total</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= '' . number_format($transaction['shopItems'][$shop]['subTotalPerShop'] + $transaction['shopItems'][$shop]['shippingfee'], 2); ?></td>
            </tr>
            <tr>
              <td>Delivered On</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td>
                <?php
                    $esd = "";
                    if($transaction['shopItems'][$shop]['shopdts'] == '0' && $transaction['shopItems'][$shop]['shopdts_to'] == '0'){
                      $esd = "Within 24 Hours";
                    }else if($transaction['shopItems'][$shop]['shopdts'] == $transaction['shopItems'][$shop]['shopdts_to']){
                      $esd = date("F d, Y", strtotime($transaction['payment_date']. ' + '.$transaction['shopItems'][$shop]['shopdts_to']. ' days'));
                    }else{
                      $esd = date("F d", strtotime($transaction['payment_date']. ' + '.$transaction['shopItems'][$shop]['shopdts'].' days')).' to '.date("F d, Y", strtotime($transaction['payment_date']. ' + '.$transaction['shopItems'][$shop]['shopdts_to']. ' days'));
                    }
                ?>
                <?=$esd?>
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
        <br>
        <p>To login and access your seller portal, please <a href="<?= base_url(''); ?>" target="_blank">click here.</a></p>
       <!--  <table width="100%" height="44" cellpadding="0" cellspacing="0" border="0" bgcolor="<?=primaryColor_accent()?>"
          style="border-radius:4px;">
          <tr>
            <td align="center" valign="middle" height="44" style="vertical-align: middle">
              <a href="<?= base_url(''); ?>" target="_blank" style="color:white;">Go to seller portal.</a>
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
