    <tr>
      <td colspan="4">
        <br />
        <b>Dear <?= $transaction['fullname']; ?>,</b><br /><br />
        <p>Good day! Your transaction is ready for delivery. Please refer to the following details: </p>
        <br />
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <table border='0'>
          <tbody>
            <tr>
              <td>Date Shipped</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= format_fulldatetime($transaction['date_shipped']); ?></td>
            </tr>
            <tr>
              <td>Delivery Partner</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['delivery_info']; ?></td>
            </tr>
            <tr>
              <td>Delivery Reference Number</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['delivery_ref_num']; ?></td>
            </tr>
            <tr>
              <td>Delivery Fee</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?='PHP '.number_format($transaction['delivery_amount'], 2, ".", ",") . '(COD)'; ?></td>
            </tr>
            <tr>
              <td>Order Reference Number</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?= $transaction['reference_num']; ?></td>
            </tr>
            <tr>
              <td>Status</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td><?="<strong>".$order_status."</strong>"?></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
