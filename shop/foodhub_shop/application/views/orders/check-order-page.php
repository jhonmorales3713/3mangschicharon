<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    @media only screen and (max-width: 991px) {
        footer {
            display: none;
        }
    }
</style>

<div class="content-inner" id="pageActive" data-num="1"></div>
<div class="content-inner" id="pageActiveMobile" data-num="1"></div>
<div id="headerTitle" data-title="Order Summary" data-search="false"></div>
<style>
  footer {
    padding-top: 2em !important;
    position: relative;
  }
</style>
<div class="checkout-page shop-container__web mb-5">
  <div class="portal-table col-12">
    <nav aria-label="breadcrumb ">
      <ol class="breadcrumb ">
          <li class="breadcrumb-item"><a href="<?=base_url('');?>">Shop</a></li>
          <li class="breadcrumb-item active" aria-current="page">Track Order</li>
      </ol>
    </nav>
    <div class="row">

        <div class="col-lg-7 col-12 checkout__order mb-4">
            <div class="portal-table h-100">
                <h6 class="mb-4 receiver__title"><i class="fa fa-shopping-cart mr-3"></i>Your Orders</h6>
                <div id="checkoutPage">
                    <!-- Order Summary Here -->

                    <?php foreach($order_items as $sKey => $shop){
                        // $shop['order_status'] = $shop['order_status'];
                     ?>
                    <div class="product-card" id="product-card-list">
                        <div class="product-card-header">
                            <div class="row">
                                <div class="col">
                                    <div class="row no-gutters">
                                        <div class="col-1 d-flex align-items-center justify-content-end">
                                            <div><img
                                                    class="img-thumbnail"
                                                    style="width: 50px;"
                                                    src="<?=get_s3_imgpath_upload().'assets/img/shops-60/webp/'.$shop['logo']?>"
                                                    onerror="this.onerror=null; this.src='<?=get_s3_imgpath_upload().'assets/img/shops-60/'.$shop['logo']?>'"></div>
                                        </div>
                                        <div class="col d-flex align-items-center">
                                            <div class="product-card-title"><?=$shop['shopname'];?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($order_details['payment_status'] == '1') {?>
                            <div class="row no-gutters mt-2">
                                <div class="col-12 col-md-8 offset-md-2">
                                    <div class="px-3 py-2 rounded" data-target="#order_status_child_collapse-<?=$shop['shopcode']?>" aria-expanded="false" aria-controls="order_status_child_collapse-<?=$shop['shopcode']?>" id="order_status_parent_collapse">
                                        <span class="product-card-name" style="font-size: 14px">Order Status</span>  <i class="fa fa-chevron-down float-right order-status-caret" hidden></i>
                                    </div>
                                    <div class="collapse col-12 col-md-12 py-3 px-4 show" id="order_status_child_collapse-<?=$shop['shopcode']?>">
                                        <div class="portal-timeline-vertical">
                                            <div class="portal-timeline-vertical__item portal-timeline__step-1 <?= in_array($shop['order_status'], ['p', 'po', 'rp', 'bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?> ">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['p', 'po', 'rp', 'bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Order Placed</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['p', 'po', 'rp', 'bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_ordered'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['po', 'rp', 'bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['po', 'rp', 'bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Processing</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['po', 'rp', 'bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_order_processed'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['rp', 'bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['rp', 'bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Ready for Pickup</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['rp', 'bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_ready_pickup'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['bc', 'f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['bc', 'f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Booking Confirmed</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['bc', 'f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_booking_confirmed'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-2 <?= in_array($shop['order_status'], ['f', 's']) ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if (in_array($shop['order_status'], ['f', 's'])) { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">On the Way</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= in_array($shop['order_status'], ['f', 's']) ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_fulfilled'])?></div>
                                            </div>
                                            <div class="portal-timeline-vertical__item portal-timeline__step-3 <?= $shop['order_status'] == 's'  ? 'portal-timeline-vertical__item--active' : '' ;?>">
                                                <div class="portal-timeline-vertical__node">
                                                    <?php if ($shop['order_status'] == 's') { ?>
                                                    <i class="fa fa-check" aria-hidden="true" rel="tooltip" title="Key active" id="" style="
                                                        color: var(--primary-color);position: relative;top: -5px;left: 2px;font-size: 12px;">
                                                    </i>
                                                    <?php } ?>
                                                </div>
                                                <div class="portal-timeline-vertical__title">Shipped</div>
                                                <div class="portal-timeline-vertical__timestamp text-right flex-grow-1" <?= $shop['order_status'] == 's' ? '' : 'hidden' ;?> ><?=format_shortdatetime($shop['date_shipped'])?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } $subtotal = 0;
                        foreach($shop['items'] as $order){ ?>
                            <div class="product-card-body">
                                <div class="product-card-item">
                                    <div class="row no-gutters">
                                        <div class="col">
                                            <div class="row no-gutters">
                                                <div class="col-2 col-md-1">
                                                    <div class="product-card-image" style="background-image: url(<?=get_s3_imgpath_upload()."assets/img/".$shop['shopcode']."/products-250/".$order['productid']."/".removeFileExtension($order['primary_pics']).".jpg"?>)"></div>
                                                </div>
                                                <div class="col product-card-content">
                                                    <div class="product-card-name">
                                                        <?=$order['itemname'];?>
                                                    </div>
                                                    <div class="product-card-quantity">
                                                        Quantity: <?=number_format($order['quantity'])?>&nbsp;
                                                        Unit Price: &#8369; <?=number_format($order['price'],2)?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 col-md-2 d-none d-md-block">
                                            <div class="product-card-price" style = "white-space:<?=(strlen($order['unit']) > 40) ? 'normal' : 'nowrap'?>">
                                                <?=$order['unit'];?>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="product-card-price">
                                                &#8369; <?=number_format($order['quantity'] * $order['price'],2)?>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php $subtotal += ($order['price'] * $order['quantity']); } ?>

                        <div id="shipping-card-${shop.shopid}" class="product-card-footer">
                            <div class="product-card-footer-content container-fluid">
                                <div class="row">
                                    <div class="col-12 col-md">
                                        <div class="row d-flex justify-content-end">
                                            <div class="col col-md-6">
                                                <div class="pb-2 col-12 text-left product-card-title">
                                                    Shipping
                                                </div>
                                                <div class="product-card-footer-option option--active">
                                                    <div id="shippingfee-card-${shop.shopid}" class="font-weight-bold">
                                                    <?= $shop['shippingfee'] != 0 ? "&#8369; ".$shop['shippingfee'] : "Free Shipping";?>
                                                    </div>
                                                <?php if($shop['order_status'] != 's') { ?>
                                                    <?php if($shop['order_status'] != 'p') { ?>
                                                        <div class="">Delivered On: </div>
                                                        <?php
                                                          $do = "";
                                                          if($shop['shopdts'] == '0' && $shop['shopdts_to'] == '0'){
                                                            $do = "Within 24 Hours";
                                                          }else if($shop['shopdts'] == $shop['shopdts_to']){
                                                            $do = date("F d, Y", strtotime($order_details['payment_date']. ' + '.$shop['shopdts_to']. ' days'));
                                                          }else{
                                                            $do = date("F d", strtotime($order_details['payment_date']. ' + '.$shop['shopdts'].' days')).' to '.date("F d, Y", strtotime($order_details['payment_date']. ' + '.$shop['shopdts_to']. ' days'));
                                                          }
                                                        ?>
                                                        <div id="shippingdts-card-${shop.shopid}" class=""><?=$do?></div>
                                                    <?php } else { ?>
                                                        <div class="">Estimated Delivery Date: </div>
                                                        <?php if($order_details['payment_status'] == '1') {?>
                                                          <?php
                                                            $edd = "";
                                                            if($shop['shopdts'] == '0' && $shop['shopdts_to'] == '0'){
                                                              $edd = "Within 24 Hours";
                                                            }else if($shop['shopdts'] == $shop['shopdts_to']){
                                                              if($order_details['payment_date'] != null || $order_details['payment_date'] == "" || $order_details['payment_date'] == '0000-00-00 00:00:00'){
                                                                $edd = date("F d, Y", strtotime($order_details['payment_date']. ' + '.$shop['shopdts_to']. ' days'));
                                                              }else{
                                                                $edd = $shop['shopdts'].' to '.$shop['shopdts_to']. ' days';
                                                              }
                                                            }else{
                                                              if($order_details['payment_date'] != null || $order_details['payment_date'] == "" || $order_details['payment_date'] == '0000-00-00 00:00:00'){
                                                                $edd = date("F d", strtotime($order_details['payment_date']. ' + '.$shop['shopdts'].' days')).' to '.date("F d, Y", strtotime($order_details['payment_date']. ' + '.$shop['shopdts_to']. ' days'));
                                                              }else{
                                                                $edd = $shop['shopdts'].' to '.$shop['shopdts_to']. ' days';
                                                              }
                                                            }
                                                          ?>
                                                          <div id="shippingdts-card-${shop.shopid}" class=""><?=$edd?></div>
                                                        <?php } else { ?>
                                                          <?php
                                                            $esd = "";
                                                            if($shop['shopdts'] == '0' && $shop['shopdts_to'] == '0'){
                                                              $esd = "Within 24 Hours";
                                                            }else if($shop['shopdts'] == $shop['shopdts_to']){
                                                              $esd = "Within ".$shop['shopdts_to']." day(s)";
                                                            }else{
                                                              $esd = $shop['shopdts'].' to '.$shop['shopdts_to']. ' days';
                                                            }
                                                          ?>
                                                          <div id="shippingdts-card-${shop.shopid}" class=""><?=$esd?></div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="">Date Shipped: </div>
                                                    <div id="shippingdts-card-${shop.shopid}" class=""><?= date_format(date_create($shop['date_shipped']), 'm/d/Y');?></div>
                                                <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="product-card-body py-3 product-card-total">
                            <div class="product-card-item">
                                <div class="row no-gutters">
                                    <div class="col product-card-name text-right">
                                        Sub-total:
                                    </div>
                                    <div class="col-5 col-md-4">
                                        <div class="product-card-price" id="subtotal-card-${shop.shopid}">
                                            &#8369; <?=number_format($subtotal,2)?>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="product-card-delete">
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($shop['vouchers']) && sizeof($shop['vouchers']) > 0) { ?>
                                <div id="applied-vouchers-section-<?=$sKey?>">
                                    <?php foreach($shop['vouchers'] as $key => $voucher) { ?>
                                    <div class="row no-gutters discount-item mb-1">
                                        <div class="col product-card-name applied-voucher-code text-md-right">
                                            <?= $key == 0 ? 'Voucher' : ''; ?>
                                        </div>
                                        <div class="col-11 col-md-4">
                                            <div class="inline-block pull-left">
                                                <span style="font-size: 10px; background-color: #eee;" class="badge badge-pill border p-2 ml-1">
                                                    <i style="font-size: inherit;" class="fa fa-tag mr-1" aria-hidden="true"></i>
                                                    <?= $voucher['vcode'] ?>
                                                </span>
                                            </div>
                                            <div class="inline-block pull-right total-discount-per-shop">
                                                <div class="product-card-price">
                                                    <?= '- &#8369; '.number_format($voucher['vamount'],2) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="row no-gutters" id="disc-holder-${shop.shopid}">
                                    <div hidden>
                                        <input id="disc-subtotal-value-${shop.shopid}" type="text" value="${parseFloat(subtotal)}">
                                    </div>
                                    <div class="col product-card-name text-md-right">
                                        New Sub-total:
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="product-card-price disc-sub-total-card" id="disc-subtotal-card-${shop.shopid}">
                                            <?php
                                              $new_sub_total = floatval($subtotal) - floatval($shop['voucherSubTotal']);
                                              $new_sub_total = ($new_sub_total < 0) ? 0 : $new_sub_total;
                                            ?>
                                            <?= '&#8369; '.number_format($new_sub_total,2) ?>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <?php if(isset($order_details['referral_code']) && $order_details['referral_code'] != ""):?>
                  <div class="product-card" id="referral-card">
                      <div class="product-card-body py-3 product-card-total">
                          <div class="row no-gutters mb-2">
                              <div class="col product-card-name text-right">
                                  Referral Code
                              </div>
                              <div class="col-1" >
                              </div>
                              <div class="col-5 col-md-3 text-right mr-1">
                                  <?=$order_details['referral_code']?>
                              </div>
                              <div class="col-1">
                                  <div class="" id="verifiedCode">
                                      <div class="">
                                          <a data-toggle="tooltip" data-placement="top" title="Verified Code"><i class="fa fa-check-circle" style="color:var(--green);" aria-hidden="true"></i></a>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                <?php endif;?>

                <div class="product-card">
                    <div class="product-card-body py-3 product-card-total">
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Sub-total
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price" id="sub_total_amount_checkout">
                                    &#8369; <?=number_format($order_details['order_total_amt'], 2, ".", ",");?>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                        <?php if ($order_details['voucherAmount'] > 0): ?>
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Voucher
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price" id="sub_total_amount_checkout">
                                   - &#8369; <?=number_format($order_details['voucherAmount'], 2, ".", ",");?>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Shipping Fee <i class="fa fa-exclamation-circle" aria-hidden="true" rel="tooltip" title="Key active" id=""></i>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price"  id="shipping_amount_checkout">
                                    &#8369; <?=number_format($order_details['delivery_amount'], 2, ".", ",");?>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <h6 class="mb-4 receiver__title"><i class="fa fa-user mr-3"></i>Contact Details</h6>
            <div class="detail-card receiver-detail-container mb-4">

                <small>Email Address</small>
                <div class="mb-2">
                    <input disabled id="checkout_email" name="checkout_email" class="detail-input" type="email" placeholder="Email Address" value="<?=$order_details['email']?>">
                </div>
                <div class="mb-2"> <small style="color:var(--gray);">Note: Please check your email for order updates</small>
                </div>
            </div>
            <h6 class="mb-4 receiver__title"><i class="fa fa-truck mr-3"></i>Shipping Details</h6>
            <div class="detail-card receiver-detail-container mb-4">
                <small>Receiver's Name</small>
                <div class="mb-2">
                    <input disabled id="checkout_name" name="checkout_name" class="detail-input" type="text" placeholder="Receiver's Name" value="<?=ucwords(strtolower($order_details['fullname']));?>">
                </div>
                <small>Receiver's Mobile Number</small>
                <div class="mb-2">
                    <input disabled id="checkout_conno" name="checkout_conno" class="detail-input numberInput" type="text" placeholder="Receiver's Mobile Number" value="<?=$order_details['conno']?>">
                </div>
                <small>Shipping Address</small>
                <textarea disabled id="checkout_address" name="checkout_address" class="detail-input" name="" id="" cols="30" rows="2" placeholder="Address (House #, Street,Village)" ><?=desanitize($order_details['address'])?></textarea>
                <?php if($order_details['notes'] != ''): ?>
                    <small>Landmark/Notes</small>
                    <textarea id="instructions" name="instructions" class="detail-input" name="" id="" cols="30" rows="3" placeholder="Landmarks/Notes"><?= $order_details['notes'] != "" ? desanitize($order_details['notes']) : "N/A";?></textarea>
                <?php endif;?>
            </div>
            <h6 class="mb-4 receiver__title"><i class="fa fa-tags mr-3"></i>Order Details</h6>
            <div class="detail-card receiver-detail-container">
                <small>Order Number</small>
                <div class="mb-2">
                    <input disabled id="checkout_refnum" name="checkout_refnum" class="detail-input" type="text" value="<?=$order_details['reference_num'];?>">
                </div>
                <?php if($order_details['payment_status'] == 1):?>
                    <small>Payment Reference No</small>
                    <div class="mb-2">
                        <input disabled id="checkout_payref" name="checkout_payref" class="detail-input" type="text" value="<?=$order_details['paypanda_ref'];?>">
                    </div>
                <?php endif;?>
                <small>Date of Purchase</small>
                <div class="mb-2">
                    <input disabled id="checkout_dop" name="checkout_dop" class="detail-input" type="text" placeholder="Receiver's Name" value="<?=date_format(date_create($order_details['date_ordered']), 'm/d/Y h:i:s A');?>">
                </div>
                <?php if($order_details['payment_status'] == 1): ?>
                    <small>Date of Payment</small>
                    <div class="mb-2">
                        <input disabled id="checkout_payment_date" name="checkout_payment_date" class="detail-input" type="text" placeholder="Receiver's Name" value="<?= $order_details['payment_date'] != "0000-00-00 00:00:00" ? date_format(date_create($order_details['payment_date']), 'm/d/Y h:i:s A') : "N/A";?>">
                    </div>
                <?php endif;?>
                <small>Payment Status</small>
                <div class="mb-2">
                    <?php if($status == 'Q') {?>
                        <input disabled id="checkout_paystatus_q" name="checkout_paystatus_q" class="detail-input font-weight-bold" type="text"  value="Processing Payment" style="color:var(--green)">
                    <?php } else if($order_details['payment_status'] == '0') {?>
                        <input disabled id="checkout_paystatus_0" name="checkout_paystatus_0" class="detail-input font-weight-bold" type="text" placeholder="Receiver's Name" value="Waiting for payment confirmation" style="color:var(--orange)">
                        <label>NOTE: All payment verification takes up to 24 hrs</label>
                    <?php } else if($order_details['payment_status'] == '1') {?>
                        <input disabled id="checkout_paystatus_1" name="checkout_paystatus_1" class="detail-input font-weight-bold" type="text"  value="Paid" style="color:var(--green)">
                    <?php } else if($order_details['payment_status'] == '2') {?>
                        <input disabled id="checkout_paystatus_2" name="checkout_paystatus_2" class="detail-input font-weight-bold" type="text"  value="Failed" style="color:var(--red)">
                    <?php } ?>
                </div>
            </div>

            <div class="detail-card checkout-footer">
                <div class="row no-gutters">
                    <div class="col checkout-total-container">
                        <div class="checkout-total">
                            <?php
                              $new_total = (floatval($order_details['order_total_amt']) - floatval($order_details['voucherAmount']));
                              $new_total = ($new_total < 0) ? 0 : $new_total;
                              $new_total = $new_total + floatval($order_details['delivery_amount']);
                            ?>
                            Total: <span class="highlight" id="total_amount_checkout"> <span class='ml-2'>&#8369; <?=number_format($new_total, 2, ".", ",");?></span></span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="<?=base_url('');?>"><button id="continueshop" class="btn checkout-button">Continue Shopping</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<?php  $this->load->view("includes/footer"); ?>
