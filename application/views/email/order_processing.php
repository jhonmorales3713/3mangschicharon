
<table style="width: 100%; ">
    <tbody>
        <tr>
            <td style="padding: 16px 0px 0 0">
                <h3 style="color:#F6841F; margin-top: 0; font-size: 18px; font-family: 'Fira Sans', sans-serif;">Good day <?=$recipient_details->full_name;?>!</h3>
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%">
    <tbody>
        <tr>
            <td>
                <p style="color: #222; margin-top: 0;">Your order #<?= $reference_num; ?> has been confirmed. Our Shop has also
          been notified to start processing your order and will update you once it's on its way. Thank you for shopping with
          us and hope to see you soon</p>
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%">
        <tbody>
            <tr>
                <td>
                    <p style="color: #222; margin-top: 0;">
                        <b>Delivery Details</b>
                    </p>
                </td>
            </tr>
            <tr>
                <td>Order Reference Number</td>
                <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
                <td colspan="2"><?= $reference_num; ?></td>
            </tr>
            <tr>
              <td>Recipient</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"><?= $recipient_details->full_name; ?></td>
            </tr>
            <tr>
              <td>Contact No.</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"><?= $recipient_details->contact_no; ?></td>
            </tr>
            <tr>
              <td>Email</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"><?= isset($recipient_details->email)?$recipient_details->email:'NONE'; ?></td>
            </tr>
            <tr>
              <td>Address</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"><?= $recipient_details->address.', Brgy. '.$recipient_details->barangay.' '.$recipient_details->city.', '.$recipient_details->zip_code.', '.$recipient_details->province; ?> </td>
            </tr>
            <?php if($recipient_details->notes != ''): ?>
            <tr>
              <td>Notes/Landmark</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
              <td colspan="2"><?= $recipient_details->notes; ?></td>
            </tr>
            <?php endif;?>
        </tbody>
</table>

<table class="order-status">
        <tbody>
          <tr>
            <td style="width: 100px; text-align: center;"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/confirmed-order-done.png';?>" alt="" style="height: 40px;"></td>
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
                  <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_created'])); ?>
                  </li>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>

      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/preparing-order.png';?>" alt="" style="height: 40px;"></td>
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
            <td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/ready-for-pickup.png';?>" alt="" style="height: 40px;"></td>
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
            <td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/fulfilled.png';?>" alt="" style="height: 40px;"></td>
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
            <td style="width: 100px; text-align: center"><img src="<?=get_s3_imgpath_upload().'assets/img/order-status/delivered.png';?>" alt="" style="height: 40px;"></td>
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



<!-- 

<table style="width: 100%">
        <tbody>
            <tr>
                <td>
                    <p style="color: #222; margin-top: 0;">Please change your password upon log-in at <?=$resetpasslink;?></p>
                </td>
            </tr>
        </tbody>
</table>

<table style="width: 100%">
    <tbody>
        <tr>
            <td style="padding: 20px 0 0 0">
                <i style="font-size: 13px; color:#525252; font-family: 'Fira Sans', sans-serif;">Note: This is an auto-generated email. Please do not reply to this email thread.</i>
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
</table> -->