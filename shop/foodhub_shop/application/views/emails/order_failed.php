    <tr>
      <td colspan="4">
        <br />
        <b>Dear <?= $transaction['fullname']; ?>,</b>
        <p>
          Your payment for order #<?= $transaction['reference_num']; ?> has failed.</p>
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
        <b>Payment Details</b>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <table border='0'>
          <tbody>
            <tr>
              <td>Payment Ref #</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['paypanda_ref'] ?></td>
            </tr>
            <tr>
              <td>Payment Method</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['payment_method']; ?></td>
            </tr>
            <tr>
              <td>Sub-Total</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"><?='PHP '.number_format($transaction['total_amount'], 2, ".", ","); ?></td>
            </tr>
            <tr>
              <td>Shipping Fee</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"> <?='PHP '.number_format($transaction['delivery_amount'], 2, ".", ","); ?></td>
            </tr>
            <tr>
              <td>Total (VAT incl.)</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2">
                <strong><?='PHP '.number_format(floatval($transaction['total_amount'])+floatval($transaction['delivery_amount']), 2, ".", ","); ?></strong>
              </td>
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