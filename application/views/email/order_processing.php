
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
            <td style="width: 100px; text-align: center;"><img src="<?=base_url().'assets/img/icons/confirmed-order-done.png';?>" alt="" style="height: 40px;"></td>
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

      <?php if($order_data_main[0]['status_id'] == 7 && !empty($order_data_main[0]['date_declined'])){?>
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/preparing-order-done.png';?>" alt="" style="height: 40px;"></td>
            
            <td style="width: 100%;"><strong>Order Cancelled By System</strong></td>
          </tr>
        </tbody>
      </table>

      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
            <td style="width: 47px;"></td>
            <td style="width: 100%;">
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <?php if($order_data_main[0]['date_declined']!=''){?>
                  <li style="list-style: none; color: #222;">
                    We have tagged your order as Declined by System.<br>
                    <b>Reason:<?=json_decode($order_data_main[0]['reasons'])->decline;?></b>
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                    <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_declined'])); ?>
                    </li>
                <?php } ?>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>
      <?php } ?>
      <table class="order-status">
        <tbody>
          <tr>
            <?php if($order_data_main[0]['status_id'] >= 3 && empty($order_data_main[0]['date_declined'])){?>
            <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/preparing-order-done.png';?>" alt="" style="height: 40px;"></td>
            
            <?php }else{?>
            <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/preparing-order.png';?>" alt="" style="height: 40px;"></td>
            
            <?php } ?>
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
                <?php if($order_data_main[0]['date_readyforpickup']!=''){?>
                  <li style="list-style: none; color: #222;">
                    We have processed your order.
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                  <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_processed'])); ?>
                  </li>
                <?php } ?>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>

      <table class="order-status">
        <tbody>
          <tr>
            <?php if($order_data_main[0]['status_id'] >= 3 && empty($order_data_main[0]['date_declined'])){?>
                <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/ready-for-pickup-done.png';?>" alt="" style="height: 40px;"></td>
            
            <?php }else{?>
                <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/ready-for-pickup.png';?>" alt="" style="height: 40px;"></td>
            
            <?php } ?>
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
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <li style="list-style: none; color: #222;">
                  <?php if($order_data_main[0]['date_readyforpickup']!=''){?>
                    <li style="list-style: none; color: #222;">
                    We have tagged your order as ready for pickup.
                    </li>
                    <li style="list-style: none; font-size: 12px; color: gray;">
                      <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_readyforpickup'])); ?>
                    </li>
                  <?php } ?>
                </li>
              </ul>
            </td>
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
            <?php if($order_data_main[0]['status_id'] >= 4 && !empty($order_data_main[0]['date_fulfilled'])){?>
                <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/fulfilled-done.png';?>" alt="" style="height: 40px;"></td>
            
            <?php }else{?>
                <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/fulfilled.png';?>" alt="" style="height: 40px;"></td>
            
            <?php } ?>
            <td style="width: 100%;"><strong>Fulfilled</strong></td>
          </tr>
        </tbody>
      </table>
      
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
            <td style="width: 47px;"></td>
            <td style="width: 100%;">
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <?php if($order_data_main[0]['date_fulfilled']!=''){?>
                  <li style="list-style: none; color: #222;">
                    We have tagged your order as for fulfilment.
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                    <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_fulfilled'])); ?>
                    </li>
                <?php } ?>
              </ul>
            </td>
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
      <?php if($order_data_main[0]['date_deliveryfailed1']!=''){?>

      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/delivered-error.png';?>" alt="" style="height: 40px;"></td>
            
            <td style="width: 100%;"><strong>Re-Deliver</strong></td>
          </tr>
        </tbody>
      </table>

      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
            <td style="width: 47px;"></td>
            <td style="width: 100%;">
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <?php if($order_data_main[0]['date_deliveryfailed1']!=''){?>
                  <li style="list-style: none; color: #222;">
                    We have tagged your order as for re-deliver. Please respond or attend the calls our rider make to deliver your goods successfully.<br>
                    <b>Reason:<?=json_decode($order_data_main[0]['reasons'])->redeliver1;?></b>
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                    <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_deliveryfailed1'])); ?>
                    </li>
                <?php } ?>
              </ul>
            </td>
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
      <?php } ?>
      <?php if($order_data_main[0]['date_deliveryfailed2']!=''){?>
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/delivered-error.png';?>" alt="" style="height: 40px;"></td>
            
            <td style="width: 100%;"><strong>Re-Deliver</strong></td>
          </tr>
        </tbody>
      </table>
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
            <td style="width: 47px;"></td>
            <td style="width: 100%;">
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <?php if($order_data_main[0]['date_deliveryfailed2']!=''){?>
                  <li style="list-style: none; color: #222;">
                    We have tagged your order as for re-deliver. Failed delivery will be tagged as failed delivery.<br>
                    <b>Reason:<?=json_decode($order_data_main[0]['reasons'])->redeliver2;?></B>
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                    <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_deliveryfailed2'])); ?>
                    </li>
                <?php } ?>
              </ul>
            </td>
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
      <?php } ?>

      <?php if(($order_data_main[0]['date_deliveryfailed2']!='' && $order_data_main[0]['date_deliveryfailed1']!='') && $order_data_main[0]['status_id'] == 0){?>
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/delivered-error.png';?>" alt="" style="height: 40px;"></td>
            
            <td style="width: 100%;"><strong>Delivery Failed</strong></td>
          </tr>
        </tbody>
      </table>
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
            <td style="width: 47px;"></td>
            <td style="width: 100%;">
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <?php if($order_data_main[0]['date_declined']!=''){?>
                  <li style="list-style: none; color: #222;">
                    We have tagged your order as Failed Delivery due to <br>
                    <b>Reason:<?=json_decode($order_data_main[0]['reasons'])->cancel;?></B>
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                    <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_declined'])); ?>
                    </li>
                <?php } ?>
              </ul>
            </td>
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
      <?php } ?>

      
      <table class="order-status">
        <tbody>
          <tr>
            <?php if($order_data_main[0]['status_id'] >= 4 && !empty($order_data_main[0]['date_delivered'])){?>
                <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/delivered-done.png';?>" alt="" style="height: 40px;"></td>
            
            <?php }else{?>
                <td style="width: 100px; text-align: center"><img src="<?=base_url().'assets/img/icons/delivered.png';?>" alt="" style="height: 40px;"></td>
            
            <?php } ?>
            <td style="width: 100%;"><strong>Delivered</strong></td>
          </tr>
        </tbody>
      </table>
      
      <table class="order-status">
        <tbody>
          <tr>
            <td style="width: 47px; border-right: 2px solid lightgray"></td>
            <td style="width: 47px;"></td>
            <td style="width: 100%;">
              <ul style="list-style-type: circle; padding-left: 0; margin-top: 0; vertical-align:top; margin-bottom: 0px">
                <?php if($order_data_main[0]['date_delivered']!=''){?>
                  <li style="list-style: none; color: #222;">
                  We have tagged your order as delivered.
                  </li>
                  <li style="list-style: none; font-size: 12px; color: gray;">
                    <?= date("M d, Y h:i a", strtotime($order_data_main[0]['date_delivered'])); ?>
                    </li>
                <?php } ?>
              </ul>
            </td>
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
      <table style="width: 100%">
          <tbody>
              <tr>
                  <td>
                      <b>Order Details</b>
                  </td>
              </tr>
          </tbody>
      </table>
      <table style="width: 100%">
        <tbody>
            <?php $packageCnt = 1;?>
            <?php if($order_data_main[0]['date_delivered']!=''){?>
            <tr>
              <td colspan="4">Delivered On: <?= $order_data_main[0]['date_delivered'];?></td> 
            </tr>
            <?php }?>
            <?php $total_amount = 0;$total_amount_conv = 0;?>
            <?php print_r('');foreach($order_data as $key => $value){ ?>
            <?php $primary_pic = str_replace('=','',$value->img);?>
              <tr>
                  <td></td>
                  <td width="10%">
                      <div style="padding-right:10px">
                        <a href=""><img src="<?= base_url('assets/uploads/products/'.$primary_pic)?>" style="width:100%;min-width:100px;max-width:160px" class=""></a>
                      </div>
                  </td>
                  <td colspan="2" width="100%;">
                      <div class="col-12 col-md-6 col-lg-12 mb-3">
                          <div class="portal-table__item">
                              <div class="portal-table__column col-12 col-lg portal-table__product"><?=$value->name;?></div>
                              <div class="portal-table__column col-4 col-lg-2 portal-table__unit">
                              <span class="d-lg-none">Unit:</span> <?=$value->qty;?>
                              </div>
                              <div class="portal-table__column col-12 col-lg-2 portal-table__price text-lg-right">Price:  <?=number_format($value->amount,2)?></div>
                              <div class="portal-table__column col-12 col-lg-2 portal-table__id portal-table__totalprice text-lg-right">Total: <?=number_format($value->amount,2) * number_format($value->qty,2)?></div>
                              
                              <?php $total_amount += number_format($value->amount,2) * number_format($value->qty,2);?>
                          </div>
                      </div>
                  </td>
              </tr>
            <?php  $packageCnt++; }?>
        </tbody>
      </table>
      <table style="width: 100%">
          <tbody>
              <tr>
                  <td>
                      <b>Order Summary</b>
                  </td>
              </tr>
          </tbody>
      </table>
      <table style="width: 100%">
          <tbody>
              <tr>
                  <td>Total Amount</td>
                  <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
                  <td colspan="2"><?="<strong>".number_format($total_amount, 2)."</strong>"?></td>
              </tr>
              <tr>
                  <td>Payment Status</td>
                  <td>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
                  <td colspan="2"><?="<strong>".(json_decode($order_data_main[0]['payment_data'])->payment_method_name=='COD' && $order_data_main[0]['status_id']==5)?'Paid':'Pending'."</strong>"?></td>
              </tr>
          </tbody>
      </table>
      <br><br><br>



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