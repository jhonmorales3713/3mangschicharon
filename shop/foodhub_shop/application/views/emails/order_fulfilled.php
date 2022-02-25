    <tr>
      <td colspan="4" width="100%">
        <br />
        <b>Dear <?= $transaction['name']; ?>,</b>
        <p>
          Good day! A package from your order #<?= $transaction['reference_num']; ?> is now being prepared for shipping by
          the seller. Please check your delivery and order details below. Thank you for shopping with us and hope to see you
          soon!</p>
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
    <tr>
      <td colspan="4">
        <br />
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
              <td colspan="4">Delivered On:
                <?= date("F d", strtotime($transaction['payment_date']. ' + '.$shops['shopdts'].' days')).' to '.date("F d, Y", strtotime($transaction['payment_date']. ' + '.$shops['shopdts_to']. ' days')) ?>
              </td>
            </tr>
            <?php foreach($shops['items'] as $items){ ?>
            <tr>
              <td></td>
              <td width="40%">
                <div style="padding-right:10px">
                  <a href=""><img
                      src="<?= base_url("assets/img/".$shops['shopcode']."/products-250/".$items['productid']."/0-".$items['productid'].".jpg") ?>"
                      onerror="this.onerror=null; this.src='<?=base_url("assets/img/products/".$items['productid'].".png")?>'"
                      style="width:100%;max-width:160px" class=""></a>
                </div>
              </td>
              <td colspan="2" width="60%">
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
            <?php if($transaction['delivery_ref_num'] != '') { ?>
            <tr>
              <td>Shipping</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['delivery_ref_num'] ?> (via <?= $transaction['delivery_info'] ?>)</td>
            </tr>
            <?php } else { ?>
            <tr>
              <td>Shipping</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td>Standard</td>
            </tr>
            <?php } ?>
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
        <p>If you have any concerns, please do contact us at <?= get_company_email()?>. Thank you.</p>
      </td>
    </tr>
