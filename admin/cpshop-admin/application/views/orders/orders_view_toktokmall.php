<style>
    .pac-container {
    z-index: 1051 !important;
    }
    .modal{
        z-index: 1050;   
    }
</style>
<?php
    $vouchercodeArr = array();
    $vouchertotal = 0;
    if(!empty($voucher_details)){
        foreach($voucher_details as $row){
            $vouchercodeArr[] = $row['vouchercode'];
            $vouchertotal += $row['voucheramount'];
        }

        $vouchercodeArr = (empty($vouchercodeArr)) ? 'None' : implode(", ", $vouchercodeArr);
    }else{
        $vouchercodeArr = 'None';
        $vouchertotal   = 0;
    }

    $sub_total      = max($order_details['totamt'] - $vouchertotal, 0);
    $order_status   = ($order_details['paystatus'] == 1) ? draw_transaction_status($order_details['sales_order_status']) : $order_status = draw_transaction_status($order_details['orderstatus']);
    $order_status   = ($order_details['paystatus'] == 0) ? draw_transaction_status('Unpaid') : $order_status;
    $notes          = ($order_details['notes'] != '') ? $order_details['notes'] : 'None';;
    $referral_code  = (!empty($referral['referral_code'])) ? $referral['referral_code'] : 'None';
    $payment_notes  = ($order_details['payment_notes'] != '') ? $order_details['payment_notes']:'None';
    $shipping_note  = ($order_details['shipping_note'] != '' || !empty($order_details['shipping_note'])) ? $order_details['shipping_note']:'None';
    
    $order_details['merch_referral_code']  = (!empty($order_details['merch_referral_code'])) ? $order_details['merch_referral_code']:' ';
    if(empty($branch_details)){
        $branchname          = 'Main';
        $pickup_address      = (empty($order_details['pickup_address']) ? 'None' : $order_details['pickup_address']);
        $toktokreferral_code = ($order_details['merch_referral_code'] == '') ? '' : $order_details['merch_referral_code'];
    }else{
        $branchname          = $branch_details->branchname;
        $pickup_address      = $branch_details->address;
        $toktokreferral_code = ($order_details['merch_referral_code'] == '') ? '' : $order_details['merch_referral_code'];
    }
    $toktokreferral_code = str_replace(' ', '', $toktokreferral_code);
    $latitude  = ($order_details['lati'] == '') ? '0' : $order_details['lati'];
    $longitude = ($order_details['longi'] == '') ?  '0' : $order_details['longi'];

    $payment_type = ($order_details['payment_method'] == '') ? 'Tagged as Paid(Admin)' : $order_details['payment_method'];

    $total_refund_amount = 0;
    if(!empty($refunded_order)){
        foreach($refunded_order as $refund){
            $total_refund_amount += $refund['amount'];
        }
    }

    $total_item_amount = 0;
    foreach($order_items as $row){
        $total_item_amount += $row['total_amount'];
    }

    $refunded_all = ($total_refund_amount == $total_item_amount) ? 1:0;
    $actual_shipping_fee = ($order_details['shipping_cost'] != '' || $order_details['shipping_cost'] != 0) ? number_format($order_details['shipping_cost'], 2) : 'Not Provided';
    $shippingpartner     = ($order_details['shippingpartner'] != '') ? $order_details['shippingpartner'] : 'Not Provided';
    $shipping_ref        = ($order_details['shipping_ref'] != '') ? $order_details['shipping_ref'] : 'Not Provided';

    if(c_international() == 1 && $shopid == 0){

        $total_amount_item              = 0;
        foreach($getOrderLogs as $val){
            $total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
        }
        $voucher_total_amount_conv     = currencyConvertedRate($vouchertotal, $order_details['currval']);
        $subtotal_amount_conv          = max($total_amount_item - $voucher_total_amount_conv, 0);
        $delivery_amount_conv          = currencyConvertedRate($order_details['sf'], $order_details['currval']);
        $total_refunded_amount_conv    = currencyConvertedRate($total_refund_amount, $order_details['currval']);
                
        $sub_total_converted           = displayCurrencyValue_withPHP($sub_total, $subtotal_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = displayCurrencyValue_withPHP($vouchertotal, $voucher_total_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = (floatval($vouchertotal) == floatval(0)) ? 'None' : $vouchertotal_converted;
        $shipping_fee_converted        = (floatval($order_details['sf'] != floatval(0))) ? displayCurrencyValue_withPHP($order_details['sf'], $delivery_amount_conv, $order_details['currcode']):'Free Shipping';
        $shipping_fee_converted        = (strpos($notes, '|::PA::|') !== false) ? 'For Pickup' : $shipping_fee_converted;
        $total_amount_converted        = displayCurrencyValue_withPHP(max(($sub_total + $order_details['sf']) - $total_refund_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $order_details['currcode']);
        $total_refund_amount_converted = displayCurrencyValue_withPHP($total_refund_amount, $total_refunded_amount_conv, $order_details['currcode']);
    }
    else if(c_international() == 1 && $shopid != 0){
        $total_amount_item              = 0;
        foreach($getOrderLogs as $val){
            $total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
        }
        $voucher_total_amount_conv     = currencyConvertedRate($vouchertotal, $order_details['currval']);
        $subtotal_amount_conv          = max($total_amount_item - $voucher_total_amount_conv, 0);
        $delivery_amount_conv          = currencyConvertedRate($order_details['sf'], $order_details['currval']);
        $total_refunded_amount_conv    = currencyConvertedRate($total_refund_amount, $order_details['currval']);
                
        $sub_total_converted           = displayCurrencyValue($sub_total, $subtotal_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = displayCurrencyValue($vouchertotal, $voucher_total_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = (floatval($vouchertotal) == floatval(0)) ? 'None' : $vouchertotal_converted;
        $shipping_fee_converted        = (floatval($order_details['sf'] != floatval(0))) ? displayCurrencyValue_withPHP($order_details['sf'], $delivery_amount_conv, $order_details['currcode']):'Free Shipping';
        $total_amount_converted        = displayCurrencyValue(max(($sub_total + $order_details['sf']) - $total_refund_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $order_details['currcode']);
        $total_refund_amount_converted = displayCurrencyValue($total_refund_amount, $total_refunded_amount_conv, $order_details['currcode']);
    }
    else{
        $sub_total_converted           = number_format($sub_total, 2);
        $vouchertotal_converted        = $vouchertotal;
        $vouchertotal_converted        = (floatval($vouchertotal) == floatval(0)) ? 'None' : number_format($vouchertotal_converted, 2);
        $shipping_fee_converted        = (floatval($order_details['sf'] != floatval(0))) ? number_format($order_details['sf'], 2):'Free Shipping';
        $total_amount_converted        = number_format(max(($sub_total + $order_details['sf']) - $total_refund_amount, 0), 2);
        $total_refund_amount_converted = number_format($total_refund_amount, 2);
    }
    
?>

<div class="content-inner" id="pageActive" data-num="2" data-namecollapse="" data-labelname="Order View"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>">Orders</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>

            <?php if($order_status_view == 'all'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/orders/'.$token);?>">Merchant Order List</a></li>
            <?php }else if($order_status_view == 'pending'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/pending_orders/'.$token);?>">Pending Order List</a></li>
            <?php }else if($order_status_view == 'paid'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/paid_orders/'.$token);?>">Paid Order List</a></li>
            <?php }else if($order_status_view == 'readyforprocessing'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/readyforprocessing_orders/'.$token);?>">Ready for Processing Order List</a></li>
            <?php }else if($order_status_view == 'processing'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/processing_orders/'.$token);?>">Processing Order List</a></li>
            <?php }else if($order_status_view == 'readyforpickup'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/readyforpickup_orders/'.$token);?>">Ready for Pickup Order List</a></li>
            <?php }else if($order_status_view == 'bookingconfirmed'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/bookingconfirmed_orders/'.$token);?>">Booking Confirmed Order List</a></li>
            <?php }else if($order_status_view == 'fulfilled'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/fulfilled_orders/'.$token);?>">Fulfilled Order List</a></li>
            <?php }else if($order_status_view == 'shipped'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/shipped_orders/'.$token);?>">Shipped Order List</a></li>
            <?php }else if($order_status_view == 'returntosender'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/returntosender_orders/'.$token);?>">Return to Sender Order List</a></li>
            <?php }else if($order_status_view == 'forpickup'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/forpickup_orders/'.$token);?>">For Pick up Order List</a></li>
            <?php }else if($order_status_view == 'confirmed'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/confirmed_orders/'.$token);?>">Delivery Confirmed Order List</a></li>
            <?php } ?>

            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">View Orders</li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row flex-md-row-reverse">
            <!-- <div class="col-lg-12">
                <div class="col-md-12 text-right mb-3">
                </div>
                
            </div> -->
            <div class="col-12 d-md-none mb-3">
                <div class="text-right">
                    <?php if($next_order != '-' && $next_order != ''){?>
                        <!-- <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$next_order.'/'.$order_status_view)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a> -->
                    <?php } ?>
                    <?php if($prev_order != '-' && $prev_order != ''){?>
                        <!-- <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$prev_order.'/'.$order_status_view)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a> -->
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card customer-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <!-- <i class="fa fa-user no-margin"></i> -->
                                    Customer
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row no-gutters">
                                    <div class="col-1 pr-2 d-flex justify-content-center">
                                        <label class=""><i class="fa fa-user" aria-hidden="true"></i></label>
                                    </div>
                                    <div class="col-11">
                                        <label id="tm_name"><?=$order_details['name']?></label>
                                    </div>
                                    <div class="col-1 pr-2 d-flex justify-content-center">
                                        <label class=""><i class="fa fa-mobile" aria-hidden="true"></i></label>
                                    </div>
                                    <div class="col-11">
                                        <label id="tm_mobile"><?=$order_details['conno']?></label>
                                    </div>
                                    <div class="col-1 pr-2 d-flex justify-content-center">
                                        <label class="" ><i class="fa fa-envelope" aria-hidden="true"></i></label>
                                    </div>
                                    <div class="col-11">
                                        <label id="tm_email" ><?=$order_details['email']?></label>
                                    </div>
                                    <div class="col-1 pr-2 d-flex justify-content-center">
                                        <label class="" ><i class="fa fa-truck" aria-hidden="true"></i></label>
                                    </div>
                                    <div class="col-11">
                                        <label id="tm_address"><?=$order_details['address']?></label>
                                    </div>
                                    <div class="col-1 pr-2 d-flex justify-content-center">
                                        <label class="" ><i class="fa fa-sticky-note" aria-hidden="true"></i></label>
                                    </div>
                                    <div class="col-11">
                                        <label id="tm_landmark"><?=strtoupper(htmlspecialchars_decode(strtolower(str_replace('|::PA::|', '', $notes))));?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($order_details['paystatus'] == 1){ ?>
                        <div class="col-12 mb-4">
                            <div class="card detail-container">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <!-- <i class="fa fa-credit-card no-margin"></i> -->
                                        Payment Details
                                    </h3>
                                </div>
                                <div class="card-body px-lg-5">
                                    <div class="">

                                        <div class="row grp_payment" id="grp_payment">
                                            <div class="col-12 col-md-12">
                                                <label class="">Payment Date:</label>
                                                <label id="tm_payment_date" class="green-text font-weight-bold"><?=$order_details['payment_date']?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-12">
                                                <label class="">Payment Reference No.:</label>
                                                <label id="tm_payment_ref_num" class="green-text font-weight-bold"><?=$order_details['paypanda_ref']?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-12">
                                                <label class="">Payment Type:</label>
                                                <label id="tm_payment_type" class="green-text font-weight-bold"><?=$payment_type?> - <?=payment_option($order_details['payment_option']);?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-12">
                                                <label class="">Payment Notes:</label>
                                                <label id="tm_payment_note" class="green-text font-weight-bold"><?=$payment_notes;?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                               
                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-12">
                                                <label class="">Payment Status:</label>
                                                <label id="tm_payment_status" class="green-text font-weight-bold"><?=draw_transaction_status_method($order_details['paystatus'], $order_details['payment_method'])?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                        </div>
                                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                    <?php if($order_details['paystatus'] == 1){ ?>
                        <div class="col-12 mb-4">
                            <div class="card detail-container">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <!-- <i class="fa fa-shopping-cart no-margin"></i> -->
                                        Seller Details
                                    </h3>
                                </div>
                                <div class="card-body px-lg-5">
                                    <div class="">
        
                                        <div class="row">
                                            <div class="col-12 col-md-12">
                                                <label class="">Shop Name:</label>
                                                <label id="tm_shopname"><?=$order_details['shopname'];?><?=$status_shop;?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md-6">
                                                
                                            </div> -->
        
                                            <div class="col-12 col-md-12">
                                                <label class="">Assigned to Branch:</label>
                                                <label id="branchname"><?=$branchname?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md-6">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-12">
                                                <label class="">Pickup Address:</label>
                                                <label id="tm_pickup_address"><?=$pickup_address?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md-6">
                                                
                                            </div> -->
                                        </div>
        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col d-flex align-items-center">
                                        <h3 class="card-title">
                                            Transaction Reference # <?=$reference_num?>
                                        </h3>
                                        
                                    </div>
                                    <div class="col-auto d-none d-md-flex justify-content-end">
                                        <div>
                                            <?php if($next_order != '-' && $next_order != ''){?>
                                                <!-- <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$next_order.'/'.$order_status_view)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a> -->
                                            <?php } ?>
                                            <?php if($prev_order != '-' && $prev_order != ''){?>
                                                <!-- <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$prev_order.'/'.$order_status_view)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a> -->
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="">
                                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-item"  cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Item Name</th>
                                                <th>Qty</th>
                                                <th>Amount</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card detail-container">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <!-- <i class="fa fa-info mr-3"></i> -->
                                    Order Details</h3>
                            </div>
                            <div class="card-body px-lg-5">
                                <div class="">
        
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="">Transaction Date:</label>
                                            <label id="tm_order_date" class="green-text font-weight-bold"><?=$order_details['date_ordered']?></label>
                                        </div>
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        <div class="col-12 col-md-6">
                                            <label class="">Transaction Reference No.:</label>
                                            <label id="tm_order_reference_num" class="green-text font-weight-bold"><?=$order_details['reference_num']?></label>
                                        </div>
                                       
                                        <div class="col-12 col-md-6">
                                            <label class="">Voucher:</label>
                                            <label id="tm_voucher" class="font-weight-bold"><?=$vouchercodeArr?></label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Sub-Total:</label>
                                            <label id="tm_subtotal" class="font-weight-bold"><?=$sub_total_converted;?></label>
                                        </div> 

                                        <div class="col-12 col-md-6">
                                            <label class="">Voucher Total:</label>
                                            <label id="tm_vouchertotal" class="font-weight-bold"><?=$vouchertotal_converted?></label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Shipping:</label>
                                            <label id="tm_shipping" class="font-weight-bold"><?=$shipping_fee_converted;?></label>
                                        </div>
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        <div class="col-12 col-md-6">
                                            <label class="">Total Amount:</label>
                                            <label id="tm_amount" class="green-text font-weight-bold"><?=$total_amount_converted;?></label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Refunded Amount:</label>
                                            <label id="tm_amount" class="green-text font-weight-bold"><?=$total_refund_amount_converted;?></label>
                                        </div>
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        <div class="col-12 col-md-6">
                                            <label class="">Order Status:</label>
                                            <label id="tm_order_status" class="green-text font-weight"><?=$order_status?></label>
                                        </div>
                                       
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        
                                    
        
                                    <?php if($order_details['paystatus'] == 1){ ?> 

                                        <?php if (strpos($notes, '|::PA::|') !== false) {?>
                                            <div class="col-12 col-md-6">
                                                <label class="">Delivery Status:</label>
                                                <label class='badge badge-success'>For Pickup</label>
                                            </div>
                                        <?php }else{?>
                                            <div class="col-12 col-md-6">
                                                <label class="">Estimated Shipping Date:</label>
                                                <?php if($order_details['paid_daystoship'] == $order_details['paid_daystoship_to']){?>
                                                    <label id="tm_amount" class="green-text font-weight"><?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></label>
                                                <?php }else{?>
                                                    <label id="tm_amount" class="green-text font-weight"><?=date("F d", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship'].' days'))?> to <?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></label>
                                                <?php }?>
                                            </div>
                                        <?php } ?>

                                        <?php if($order_details['sales_order_status'] == 's'){?>
                                            <div class="col-12 col-md-6">
                                                <label class="">Actual Shipping Date:</label>
                                                <label id="tm_amount" class="green-text font-weight"><?=date("F d, Y", strtotime($order_details['shipped_date']))?></label>
                                            </div>
                                        <?php }?>
                                    <?php }else{?>

                                        <?php if (strpos($notes, '|::PA::|') !== false) {?>
                                            <div class="col-12 col-md-6">
                                                <label class="">Delivery Status:</label>
                                                <label class='badge badge-success'>For Pickup</label>
                                            </div>
                                        <?php }else{?>

                                            <div class="col-12 col-md-6">
                                                <label class="">Estimated Shipping Date:</label>
                                                <?php
                                                    $shipping_date = ($order_details['payment_date'] == '0000-00-00 00:00:00') ? $order_details['date_ordered'] : $order_details['payment_date'];
                                                ?>
                                                <?php if($order_details['paid_daystoship'] == $order_details['paid_daystoship_to']){?>
                                                    <label id="tm_amount" class="green-text font-weight"><?=date("F d, Y", strtotime($shipping_date. ' + '.$order_details['paid_daystoship_to'].' days'))?></label>
                                                <?php }else{?>
                                                    <label id="tm_amount" class="green-text font-weight"><?=date("F d", strtotime($shipping_date. ' + '.$order_details['paid_daystoship'].' days'))?> to <?=date("F d, Y", strtotime($shipping_date. ' + '.$order_details['paid_daystoship_to'].' days'))?></label>
                                                <?php }?>
                                            </div>
                                        <?php } ?>
                        
                                    <?php }?>

                                        <div class="col-12">
                                            <div class="border-bottom w-100"></div>
                                        </div>
                                    </div>

                                    <?php if($shopid == 0){
                                        if($order_details['admin_drno'] == '0' || $order_details['admin_drno'] == 0 || $order_details['admin_drno'] == ''){
                                            $admin_drno = "None";
                                        }else{
                                            $admin_drno = $order_details['admin_drno'];
                                        }
        
                                        if($order_details['admin_sono'] == '0' || $order_details['admin_sono'] == 0 || $order_details['admin_sono'] == ''){
                                            $admin_sono = "None";
                                        }else{
                                            $admin_sono = $order_details['admin_sono'];
                                        }
                                        
                                    ?>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <label class="">DR No:</label>
                                                <label id="tm_drno" class="green-text font-weight-bold"><?=$admin_drno?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                               
                                            </div> -->
        
                                            <div class="col-12 col-md-6">
                                                <label class="">SO No:</label>
                                                <label id="tm_drno" class="green-text font-weight-bold"><?=$admin_sono?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
        
                                            <div class="col-12 col-md-6">
                                                <label class="">Shop Link:</label>
                                                <label id="tm_referral_code" class="font-weight-bold"><?=$referral_code?></label>
                                            </div>
                                            
                                            <!-- <div class="col-12 col-md">
                                               
                                            </div> -->
        
                                        </div>
                                    <?php } ?>
        
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($order_details['paystatus'] == 1){ ?>
                        <?php if($order_details['sales_order_status'] == 'bc' || $order_details['sales_order_status'] == 'f' || $order_details['sales_order_status'] == 's'){?>
                            <div class="col-12 mb-4">
                                <div class="card detail-container">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Shipping Details
                                        </h3>
                                    </div>
                                    <div class="card-body px-lg-5">
                                        <div class="">
                                            <?php if($order_details['sales_order_status'] == 'bc' || $order_details['sales_order_status'] == 'f' || $order_details['sales_order_status'] == 's'){?>
                                                <?php if(!empty($order_details['rider_name'])){?>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Rider Name:</label>
                                                            <label id="tm_rider_name" class="green-text font-weight-bold"><?=$order_details['rider_name']?></label>
                                                        </div>
                                                            
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Rider Plate Number:</label>
                                                            <label id="tm_plate_num" class="green-text font-weight-bold"><?=$order_details['rider_platenum']?></label>
                                                        </div>
                                                            
                                                        <div class="col-12 col-md-12">
                                                            <label class="">Rider Contact No:</label>
                                                            <label id="tm_payment_type" class="green-text font-weight-bold"><?=$order_details['rider_conno']?></label>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <?php if($order_details['sales_order_status'] == 'f' || $order_details['sales_order_status'] == 's'){?>
                                                    <div class="row">

                                                        <?php if($order_details['t_deliveryId'] != ''){?>
                                                            <div class="col-12 col-md-6">
                                                                <label class="">Delivery ID:</label>
                                                                <label id="tm_deliveryid" class="green-text font-weight-bold"><?=$order_details['t_deliveryId']?></label>
                                                            </div>
                                                        <?php } ?>

                                                        <div class="col-12 col-md-6">
                                                            <label class="">Shipping Partner:</label>
                                                            <label id="tm_shipping_partner" class="green-text font-weight-bold"><?=$shippingpartner?></label>
                                                        </div>
                                                            
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Shipping Reference No:</label>
                                                            <label id="tm_shipping_ref" class="green-text font-weight-bold"><?=$shipping_ref?></label>
                                                        </div>
                                                            
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Shipping Notes:</label>
                                                            <label id="tm_shipping_notes" class="green-text font-weight-bold"><?=$shipping_note?></label>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Actual Shipping Fee:</label>
                                                            <label id="tm_shipping_notes" class="green-text font-weight-bold"><?=$actual_shipping_fee;?></label>
                                                        </div>
                                                    </div>
                                                <?php }else if($order_details['sales_order_status'] == 'bc'){?>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Delivery ID:</label>
                                                            <label id="tm_deliveryid" class="green-text font-weight-bold"><?=$order_details['t_deliveryId']?></label>
                                                        </div>
                                                            
                                                        <div class="col-12 col-md-6">
                                                            <label class="">Actual Shipping Fee:</label>
                                                            <label id="tm_shipping_notes" class="green-text font-weight-bold"><?=$actual_shipping_fee;?></label>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                    <?php }?>
                    
                    <?php if(!empty($refunded_order)){?>
                        <div class="col-12 mb-4">
                                <div class="card detail-container">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                                    Refunded Order
                                        </h3>
                                    </div>
                                    <div class="card-body px-lg-5">
                                        <div class="">
                                            <div class="row">

                                                <?php if(!empty($refunded_order)){?>
                                                    <?php foreach($refunded_order as $refund){?>
                                                        <div class="col-md-6" style="font-weight: normal">
                                                            <span><u><?=$refund['name_of_item']?></u></span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <span><?=number_format($refund['quantity'], 2)?></span>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span><?=number_format($refund['amount'], 2)?></span>
                                                        </div>
                                                        <!-- <div class="w-100" style="border-bottom: 1px dotted black;"></div> -->
                                                    <?php } ?>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php }?>

                    <div class="col-12 mb-4">
                        <div class="card detail-container">
                            <div class="card-header">
                                <h3 class="card-title">
                                            Order Timeline/History
                                </h3>
                            </div>
                            <div class="card-body px-lg-5">
                                <div class="">
                                    <div class="row">

                                        <?php if(!empty($orders_history)){?>
                                            <?php foreach($orders_history as $logs){?>
                                                <?php if($logs['action'] == 'Item picked up' && $order_details['pickedup_photo'] != ''){?>
                                                    <div class="col-md-6" style="font-weight: normal;padding-top:13px;">
                                                        <span><?=$logs['description']?></span>
                                                        <span data-toggle="modal" data-target="#itemPickedupModal"><u>View Image</u></span>
                                                    </div>
                                                <?php }else if($logs['action'] == 'Mark as Shipped' && $order_details['shipped_photo'] != ''){?>
                                                    <div class="col-md-6" style="font-weight: normal;padding-top:13px;">
                                                        <span><?=$logs['description']?></span>
                                                        <span data-toggle="modal" data-target="#itemShippedModal"><u>View Image</u></span>
                                                    </div>
                                                <?php }else{?>
                                                    <div class="col-md-6" style="font-weight: normal;padding-top:13px;">
                                                        <span><?=$logs['description']?></span>
                                                    </div>
                                                <?php }?>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span>[ <?=$logs['username']?> ]</span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$logs['date_created']?></span>
                                                </div>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                            <?php } ?>

                                            <div class="col-md-6" style="padding-top:13px;">
                                                <span>Payment for order has been confirmed.</span>
                                            </div>
                                            <div class="col-md-4" style="padding-top:13px;">
                                                <span></span>
                                            </div>
                                            <div class="col-md-2" style="padding-top:5px;">
                                                <span><?=$order_details['payment_date']?></span>
                                            </div>
                                            <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                            <div class="col-md-6" style="padding-top:13px;">
                                                <span>Order has been placed.</span>
                                            </div>
                                            <div class="col-md-4" style="padding-top:13px;">
                                                <span></span>
                                            </div>
                                            <div class="col-md-2" style="padding-top:5px;">
                                                <span><?=$order_details['date_ordered']?></span>
                                            </div>
                                        <?php }else{?>

                                            <?php if($order_details['payment_date'] != '0000-00-00 00:00:00'){?>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Payment for order has been confirmed.</span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['payment_date']?></span>
                                                </div>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                            <?php }?>

                                            <div class="col-md-6" style="padding-top:13px;">
                                                <span>Order has been placed.</span>
                                            </div>
                                            <div class="col-md-4" style="padding-top:13px;">
                                                <span></span>
                                            </div>
                                            <div class="col-md-2" style="padding-top:5px;">
                                                <span><?=$order_details['date_ordered']?></span>
                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <?php 
            if($branch_details == null || $branch_details == "" || empty($branch_details)){
                $branch_id  = 0;
                $rp_shop_id = $mainshopid;
            }else{
                $branch_id  = $branch_details->branchid;
                $rp_shop_id = 0;
            }
        ?>
                                                
            <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn-mobile-w-100 btn btn-success printBtn mb-2 mb-md-0" id="printBtn" data-reference_num="<?=$url_ref_num?>">Print</button>
                        <button type="button" class="btn-mobile-w-100 btn btn-outline-secondary backBtn mb-2 mb-md-0" id="backBtn">Close</button>
                        <?php if($order_details['paystatus'] == 1 && $branch_count > 0 && $order_details['sales_order_status'] == 'p' && $this->loginstate->get_access()['transactions']['reassign'] == 1 && $branchid == 0 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-success reassignBtn mb-2 mb-md-0" id="reassignBtn" data-value="<?=$mainshopid?>" data-branchid="<?=$branch_id?>" data-reference_num="<?=$reference_num?>">Re-Assign</button>
                        <?php } ?> 

                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'p' && $this->loginstate->get_access()['transactions']['process_order'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light processBtn mb-2 mb-md-0" id="processBtn" data-value="<?=$url_ref_num?>">Process Order</button>
                        <?php } ?>
                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'po' && $this->loginstate->get_access()['transactions']['ready_pickup'] == 1 && $this->loginstate->get_access()['transactions']['mark_fulfilled'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light readyPickupBtn mb-2 mb-md-0" id="readyPickupBtn" data-value="<?=$url_ref_num?>">Book toktok</button>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light fulfillmentBtn mb-2 mb-md-0" id="fulfillmentBtn" data-value="<?=$url_ref_num?>">Mark as Fulfilled</button>
                        <?php }else if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'po' && $this->loginstate->get_access()['transactions']['ready_pickup'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light readyPickupBtn mb-2 mb-md-0" id="readyPickupBtn" data-value="<?=$url_ref_num?>">Book toktok</button>
                        <?php }else if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'po' && $this->loginstate->get_access()['transactions']['mark_fulfilled'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light fulfillmentBtn mb-2 mb-md-0" id="fulfillmentBtn" data-value="<?=$url_ref_num?>">Mark as Fulfilled</button>
                        <?php } ?>
                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'rp' && $this->loginstate->get_access()['transactions']['booking_confirmed'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-danger waves-effect waves-light cancelOrderBtn" id="cancelOrderBtn" data-value="<?=$url_ref_num?>">Cancel Booking</button>
                            <!-- <button type="button" class="btn btn-info waves-effect waves-light bookingConfirmBtn" id="bookingConfirmBtn" data-value="<?=$url_ref_num?>">Booking Confirmed</button> -->
                        <?php } ?>
                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'bc' && $this->loginstate->get_access()['transactions']['mark_fulfilled'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light fulfillmentBtn mb-2 mb-md-0" id="fulfillmentBtn" data-value="<?=$url_ref_num?>">Mark as Fulfilled</button>
                        <?php } ?>
                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'f' && $this->loginstate->get_access()['transactions']['returntosender'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-warning waves-effect waves-light returntosenderBtn mb-2 mb-md-0" id="returntosenderBtn" data-value="<?=$url_ref_num?>">Return to Sender</button>
                        <?php } ?>
                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'f' && $this->loginstate->get_access()['transactions']['shipped'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light shippedBtn mb-2 mb-md-0" id="shippedBtn" data-value="<?=$url_ref_num?>">Mark as Shipped</button>
                        <?php } ?>

                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'rs' && $this->loginstate->get_access()['transactions']['redeliver'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light redeliverBtn mb-2 mb-md-0" id="redeliverBtn" data-value="<?=$url_ref_num?>">Re-Deliver Order</button>
                        <?php } ?>

                        <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 's' && $this->loginstate->get_access()['transactions']['confirmed'] == 1 && $order_details['isconfirmed'] == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light confirmedBtn mb-2 mb-md-0" id="confirmedBtn" data-value="<?=$url_ref_num?>">Delivery Confirmed</button>
                        <?php } ?>
                        
                        <?php if($order_details['paystatus'] != 1 && $this->loginstate->get_access()['transactions']['mark_as_paid'] == 1 && $refunded_all == 0 && $shopid == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-success payBtn mb-2 mb-md-0" id="payBtn" data-value="<?=$url_ref_num?>">Mark as Paid</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
        

        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
        </div>
    </form>
</div>


<!-- Paid Modal-->
<div id="payment_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_payment" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Order Payment</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="tm_order_date-p"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="tm_order_reference_num-p" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Sub-Total:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="tm-subtotal-p"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Shipping:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="tm_shipping-p"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Amount to be paid:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="tm_amount-p" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="tm_order_status-p"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="f_id-p" id="f_id-p" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>CheckBoxID</label>
                            <input type="text" name="f_payment_ischecked" id="f_payment_ischecked" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12 col-sm-12">
                        <label>
                            <input type="checkbox" id="tag_payment" name="tag_payment" class="checkbox-template m-r-xs">
                            &nbsp;Tag Payment Details
                        </label>
                    </div>
                </div>
                <div class="row grp_payment-p" id="grp_payment-p">
                    <div class="col-6">
                        <div class="form-group" id="payment_field">
                            <select style="height:42px;" type="text" name="f_payment" id="f_payment" class="form-control">
                                <option value="">-- Select Payment Type --</option>
                                <option
                                <?php
                                    foreach ($payments as $payment) {
                                        ?>
                                            <option value="<?= $payment['id']; ?>"><?= $payment['description']; ?></option>
                                        <?php
                                    }
                                ?>

                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group grp_payment_others" id="grp_payment_others">
                            <input type="text" class="form-control" name="f_payment_others" id="f_payment_others" placeholder="Enter payment type">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="f_payment_ref_num" id="f_payment_ref_num" placeholder="Enter reference number">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control allownumericwithdecimal" name="f_payment_fee" id="f_payment_fee" placeholder="Enter payment amount">
                            <input type="text" class="hidden" name="f_total_amount" id="f_total_amount" value="<?=$order_details['totamt'] + $order_details['sf']?>" hidden>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <textarea type="text" class="form-control" name="f_payment_notes" id="f_payment_notes" placeholder="Enter notes (optional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12 col-sm-12">
                        <label>
                            Note: If you received payment manually, mark this order as paid to continue with fulfillment process.
                        </label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" id="btn_tbl_confirm" class="btn btn-success waves-effect waves-light btn_tbl_confirm" aria-label="Close">Confirm Payment</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Process Order Modal-->
<div id="processOrder_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_process" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Process Order</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="po_id" id="po_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" id="btn_tbl_confirm" class="btn btn-success waves-effect waves-light" aria-label="Close">Process Order</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Ready for Pickup Modal -->
<div id="readyPickup_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_save_ready_pickup" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Order Ready for Pickup</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rp_order_status"></label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label class="">Shipping Partner:</label>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <select class="form-control" id="rp_shipping_partner" name="rp_shipping_partner">
                                <!-- <option value="">Manual</option> -->
                                <?php foreach($partners_api_isset as $row){?>
                                    <option value="<?=$row['id']?>"><?=$row['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label class="">Referral Code:</label>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="rp_referralCode" name="rp_referralCode" value="<?=$toktokreferral_code;?>">
                              
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label class="">Notes:</label>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <textarea type="text" class="form-control" name="rp_notes" id="rp_notes" placeholder="Enter notes"><?=strtoupper(htmlspecialchars_decode(strtolower(str_replace('|::PA::|', '', $notes))));?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            Weight Limit: Up to 20 kg. Size Limit (L x W x H): 20 × 20 × 20 inches. 
                         </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label class="">Sender Address:</label>
                    </div>
                    <div class="col-12">
                        <input type="hidden" class="hidden" name="rp_branch_id" id="rp_branch_id" value="<?=$branch_id?>">
                        <input type="hidden" class="hidden" name="rp_shop_id" id="rp_shop_id" value="<?=$rp_shop_id?>">
                        <div class="form-group">
                            <input type="text" class="form-control" name="rp_pickup_address" id="rp_pickup_address" placeholder="Pickup Address" value="<?=$pickup_address?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div id="pac-container">
                            <label for="">Recipient Address <span class="asterisk"></span></label>
                            <input id="pin_address" type="text" placeholder="Search" class = "form-control pr_field detail-input" name = "pin_address" style = "padding:10px;font-size:15px !important;" value="">
                            <input type="hidden" name = "loc_latitude" id = "loc_latitude" name = "loc_latitude" class = "pr_field" value = "<?=$latitude?>">
                            <input type="hidden" name = "loc_longitude" id = "loc_longitude" name = "loc_longitude" class = "pr_field" value = "<?=$longitude?>">
                        </div>
                        <div id="map" style = "height:300px;margin-top:30px;"></div>
                        <div id="infowindow-content">
                        <!-- <img src="" width="16" height="16" id="place-icon"> -->
                        <span id="place-name"  class="title"></span><br>
                        <span id="place-address"></span>
                        </div>
                    </div>
                </div>

             
                
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="rp_id" id="rp_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-1">
                        <button type="button" class="btn-mobile-w-100 btn btn-info cancelBtn waves-effect waves-light mb-2 mb-m-0" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" id="btn_tbl_confirm" class="btn-mobile-w-100 btn btn-success waves-effect waves-light mb-2 mb-m-0" aria-label="Close">Book toktok</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="CancelOrder_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_cancel_order" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Are you sure you want to cancel booking?</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="cn_order_status"></label>
                    </div>
                </div>

                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="cn_id" id="cn_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label class="">Why do you want to cancel the booking?</label>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <select class="form-control" id="cn_cancellation_cat" name="cn_cancellation_cat">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <textarea type="text" class="form-control" name="cn_cancellation_notes" id="cn_cancellation_notes" placeholder="Enter notes (optional)"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" id="btn_tbl_confirm" class="btn btn-success waves-effect waves-light" aria-label="Close">Yes</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Booking Confirmed Modal -->
<div id="bookingConfirm_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_booking_confirm" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Order Booking Confirmed</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="bc_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="bc_id" id="bc_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>CheckBoxID</label>
                            <input type="text" name="bc_rider_ischecked" id="bc_rider_ischecked" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12 col-sm-12">
                        <label>
                            <input type="checkbox" id="tag_rider" name="tag_rider" class="checkbox-template m-r-xs">
                            &nbsp;Tag Rider Details
                        </label>
                    </div>
                </div>
                <div class="row grp_rider" id="grp_rider">
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" class="form-control" name="bc_rider_name" id="bc_rider_name" placeholder="Enter Rider's Name">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="bc_platenum" id="bc_platenum" placeholder="Enter Rider's Plate Number">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="bc_conno" id="bc_conno" placeholder="Enter Rider's Contact Number">
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" id="btn_tbl_confirm" class="btn btn-success waves-effect waves-light btn_tbl_confirm" aria-label="Close">Booking Confirmed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Mark as Fulfilled-->
<div id="fulfillment_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_fulfillment" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Order Fulfillment</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_payment_ref_num-f" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="f_order_status"></label>
                    </div>
                   
                    <?php if($order_details['rider_name'] != ''){?>
                        <div class="col-12 col-sm-6">
                            <label class="">Rider Name:</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="f_rider_name"><?=$order_details['rider_name']?></label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="">Plate Number:</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="f_platenum"><?=$order_details['rider_platenum']?></label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="">Contact No.:</label>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label id="f_rider_conno"><?=$order_details['rider_conno']?></label>
                        </div>
                    <?php } ?>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="f_id" id="f_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>CheckBoxID</label>
                            <input type="text" name="f_shipping_ischecked" id="f_shipping_ischecked" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row mb-2 hidden">
                    <div class="col-12 col-sm-12">
                        <label>
                            <input type="checkbox" id="tag_shipping" name="tag_shipping" class="checkbox-template m-r-xs">
                            &nbsp;Tag Shipping Details
                        </label>
                    </div>
                </div>
                <div class="row grp_shipping" id="grp_shipping">
                    <div class="col-6">
                        <div class="form-group" id="shipping_field">
                            <select style="height:42px;" type="text" name="f_shipping" id="f_shipping" class="form-control">
                                <option value="">-- Select Shipping Partner --</option>
                                <option
                                <?php
                                    foreach ($partners as $partner) {
                                        ?>
                                            <option value="<?= $partner['id']; ?>"><?= $partner['name']; ?></option>
                                        <?php
                                    }
                                ?>

                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group grp_shipping_others" id="grp_shipping_others">
                            <input type="text" class="form-control" name="f_shipping_others" id="f_shipping_others" placeholder="Enter shipping partner">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="f_shipping_ref_num" id="f_shipping_ref_num" placeholder="Enter reference number">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control allownumericwithdecimal" name="f_shipping_fee" id="f_shipping_fee" placeholder="Enter actual shipping fee" value="<?=$order_details['shipping_cost']?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <!-- <input type="text" class="form-control" name="f_shipping_notes" id="f_shipping_notes" placeholder="Enter notes (optional)"> -->
                            <textarea type="text" class="form-control" name="f_shipping_notes" id="f_shipping_notes" placeholder="Enter notes (optional)"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>CheckBoxID</label>
                            <input type="text" name="mf_rider_ischecked" id="mf_rider_ischecked" class="form-control" value="0" >
                        </div>
                    </div>
                </div>

                 <div class="row mb-2">
                    <div class="col-12 col-sm-12">
                        <label>
                            <input type="checkbox" id="mf_rider" name="mf_rider" class="checkbox-template m-r-xs">
                            &nbsp;Tag Rider Details
                        </label>
                    </div>
                </div>
                <?php 
                    $order_details['rider_name']     = ($order_details['rider_name'] != '') ? $order_details['rider_name'] : '';
                    $order_details['rider_platenum'] = ($order_details['rider_platenum'] != '') ? $order_details['rider_platenum'] : '';
                    $order_details['rider_conno']    = ($order_details['rider_conno'] != '') ? $order_details['rider_conno'] : '';
                ?>
                <div class="row mf_rider" id="mf_rider" style="display:none">
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" class="form-control" name="mf_rider_name" id="mf_rider_name" placeholder="Enter Rider's Name" value="<?=$order_details['rider_name']?>">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="mf_platenum" id="mf_platenum" placeholder="Enter Rider's Plate Number" value="<?=$order_details['rider_platenum']?>">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="mf_conno" id="mf_conno" placeholder="Enter Rider's Contact Number" value="<?=$order_details['rider_conno']?>">
                        </div>
                    </div>
                </div>

            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" id="btn_tbl_confirm" class="btn btn-success waves-effect waves-light btn_tbl_confirm" aria-label="Close">Fulfill Order</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Return to Sender Modal -->
<div id="returntosender_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_returntosender" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Return to Sender Order</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rs_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="rs_id" id="rs_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
                        <button type="button" class="btn-mobile-w-100 btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" id="btn_tbl_confirm" class="btn-mobile-w-100 btn btn-warning waves-effect waves-light" aria-label="Close">Return to Sender</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Re-Deliver Modal -->
<div id="redeliver_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_redeliver" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Re-Deliver Order</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="rd_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="rd_id" id="rd_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
                        <button type="button" class="btn-mobile-w-100 btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" id="btn_tbl_confirm" class="btn-mobile-w-100 btn btn-success waves-effect waves-light" aria-label="Close">Re-Deliver Order</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Shipped Modal -->
<div id="shipped_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_shipped" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Order Shipped</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="s_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="s_id" id="s_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
                        <button type="button" class="btn-mobile-w-100 btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" id="btn_tbl_confirm" class="btn-mobile-w-100 btn btn-success waves-effect waves-light" aria-label="Close">Mark as Shipped</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Delivery Confirmed Modal -->
<div id="confirmed_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_save_confirmed" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="oc_header_ref" class="modal-title" style="color:black;">Delivery Confirmed</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Date</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_payment_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Reference No.</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_payment_ref_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="oc_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="oc_id" id="oc_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
                        <button type="button" class="btn-mobile-w-100 btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" id="btn_tbl_confirm" class="btn-mobile-w-100 btn btn-success waves-effect waves-light" aria-label="Close">Delivery Confirmed</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- check item reassign modal -->
<div id="check_branches-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Branch re-assignment confirmation</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                       <div style="color:#de3a3a;font-style:italic;" id="reassign_note"></div>
                       <label><br>Are you sure you want to re-assign this order?</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                <button type="button" id="check_update-branches" class="btn btn-success cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- reassign modal -->
<div id="branches-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Branch re-assignment</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <label>Assign to Branch: <span style="color:red;"> *</span></label>
                    </div>
                    <div class="row">
                        <select class="select2 form-control" id="shop-branches"></select>
                    </div>
                    <div class="row">
                        <label>Remarks: <span style="color:red;"> *</span></label>
                    </div>
                    <div class="row">
                        <textarea id="branch-remarks" style="width:100%"></textarea>
                    </div>
                    <input type="hidden" name="prev_branchid" id="prev_branchid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                <button type="button" id="update-branches" class="btn btn-success cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Re-assign</button>
            </div>
        </div>
    </div>
</div>

<!-- item picked up photo modal -->
<div class="modal fade" id="itemPickedupModal" tabindex="-1" role="dialog" aria-labelledby="itemPickedupModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header"> -->
                <!-- <h5 class="modal-title" id="exampleModalLabel" style="color:black;">Item picked up photo</h5> -->
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                <!-- <span aria-hidden="true">&times;</span> -->
                <!-- </button> -->
            <!-- </div> -->
            <div class="modal-body">
                <img src="<?=$order_details['pickedup_photo']?>" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--item shipped photo modal -->
<div class="modal fade" id="itemShippedModal" tabindex="-1" role="dialog" aria-labelledby="itemShippedModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header"> -->
                <!-- <h5 class="modal-title" id="exampleModalLabel" style="color:black;">Item shipped photo</h5> -->
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                <!-- <span aria-hidden="true">&times;</span> -->
                <!-- </button> -->
            <!-- </div> -->
            <div class="modal-body">
                <img src="<?=$order_details['shipped_photo']?>" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="url_ref_num" value="<?=$url_ref_num?>">
<input type="hidden" id="shop_status" value="<?=$status_shop?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->


<script type="text/javascript" src="<?=base_url('assets/js/shops/googlemap_edit.js');?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=get_google_api_key()?>&libraries=places&callback=initializeMaps"
async defer></script>
<script type="text/javascript" src="<?=base_url('assets/js/orders/orders_view_toktokmall.js');?>"></script>
