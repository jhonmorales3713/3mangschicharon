<?php error_reporting(0);?>
<style>
    th{
        font-weight: bold;
        text-align: left;
        border-bottom: 0.5pt solid black;
        line-height: 12px;
    }

    td{
        line-height: 12px;
    }

    .line-height-one{
        line-height: 1px;
    }

    .line-height-two{
        line-height: 2px;
    }

    .line-height-five{
        line-height: 5px;
    }
</style>

<div class=" container-fluid main-content">
    <h1 class="line-height-two">Order Reference #<?=$reference_num;?></h1>
    <p class="line-height-one">Date Printed: <?=today()?></p>
    <br>
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
        $shipping_fee   = (floatval($order_details['sf'] != floatval(0))) ? number_format($order_details['sf'], 2):'Free Shipping';
        $payment_notes  = ($order_details['payment_notes'] != '') ? $order_details['payment_notes']:'None';
        $shipping_note  = ($order_details['shipping_note'] != '' || !empty($order_details['shipping_note'])) ? $order_details['shipping_note']:'None';

        $order_type  = "";
        if($order_details['order_type'] == 1){
            $order_type = "Guest";
        }
        else if($order_details['order_type'] == 2){
            $order_type = "Regular";
        }
        else if($order_details['order_type'] == 3){
            $order_type = "via Shoplink";
        }
        else if($order_details['order_type'] == 4){
            $order_type = "Reseller";
        }
        else{
            $order_type = "Login as OF ";
        }
        
        if(empty($branch_details)){
            $branchname = 'Main';
            $pickup_address = (empty($order_details['pickup_address']) ? 'None' : $order_details['pickup_address']);
        }else{
            $branchname = $branch_details->branchname;
            $pickup_address = $branch_details->address;
        }

        $payment_type = ($order_details['payment_method'] == '') ? 'Tagged as Paid(Admin)' : $order_details['payment_method'];

        $total_refund_amount = 0;
        if(!empty($refunded_order)){
            foreach($refunded_order as $refund){
                $total_refund_amount += $refund['amount'];
            }
        }

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

        if(ini() == 'toktokmart'){
            $refunded_amount_tm = 0;
            foreach($modified_order as $row){
                $refunded_amount_tm += $row['total_amount'];
    
            }
        }
    ?>
    <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
        <tbody>
            <tr>
                <td width="100"><b>Order Details </b></td>
                <td width="150"></td>
                <td width="100"></td>
                <td width="200"></td>
            </tr>
            <tr>
                <td width="100">Transaction Date: </td>
                <td width="150"><?=$order_details['date_ordered']?></td>
                <td width="120">Transaction Reference No.: </td>
                <td width="200"><?=$order_details['reference_num']?></td>
            </tr>
            <tr>
                <td width="100">Voucher: </td>
                <td width="150"><?=$vouchercodeArr?></td>
                <td width="120">Sub-Total:</td>
                <td width="200"><?=$sub_total_converted;?></td>
            </tr>
            <tr>
                <td width="100">Voucher Total:</td>
                <td width="150"><?=$vouchertotal_converted;?></td>
                <td width="120">Shipping:</td>
                <td width="200"><?=$shipping_fee_converted;?></td>
            </tr>
            <tr>
                <td width="100">Total Amount:</td>
                <td width="150"><?=$total_amount_converted;?></td>
                <?php if(ini() != 'toktokmart'){?>
                    <td width="120">Refunded Amount</td>
                    <td width="200"><?=$total_refund_amount_converted;?></td>
                <?php } else {?>
                    <td width="120">Total Refunded Amount</td>
                    <td width="200"><?=number_format($refunded_amount_tm, 2);?></td>
                <?php }?>
            </tr>
            <tr>
                <?php if($order_details['paystatus'] == 1){ ?> 
                    <?php if (strpos($notes, '|::PA::|') !== false) {?>
                        <td width="100">Delivery Status:</td>
                        <td width="150">For Pickup</td>
                    <?php } else{?>
                        <td width="100">Estimated Delivery Date:</td>
                        <?php if($order_details['paid_daystoship'] == $order_details['paid_daystoship_to']){?>
                            <td width="150"><?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></td>
                        <?php }else{?>
                            <td width="150"><?=date("F d", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship'].' days'))?> to <?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></td>
                        <?php }?>
                    <?php }?>

                    <?php if($order_details['sales_order_status'] == 's'){?>
                        <td width="120">Actual Shipping Date:</td>
                        <td width="200"><?=date("F d, Y", strtotime($order_details['shipped_date']))?></td>
                    <?php } else{?>
                        <td width="120">Order Status:</td>
                        <td width="200"><?=$order_status?></td>
                    <?php }?>

                <?php }else{ ?>

                    <?php if (strpos($notes, '|::PA::|') !== false) {?>
                        <td width="100">Delivery Status:</td>
                        <td width="150">For Pickup</td>
                    <?php } else{?>
                        <td width="100">Estimated Delivery Date:</td>
                        <?php if($order_details['paid_daystoship'] == $order_details['paid_daystoship_to']){?>
                            <td width="150"><?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></td>
                        <?php }else{?>
                            <td width="150"><?=date("F d", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship'].' days'))?> to <?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></td>
                        <?php }?>
                        <td width="120">Order Status:</td>
                        <td width="200"><?=$order_status?></td>
                    <?php }?>
                    

                        <td width="120"></td>
                        <td width="200"></td>
                <?php } ?>
            </tr>
            <tr>
                <td width="100">Shop Link:</td>
                <td width="150"><?=$referral_code?></td>
                <?php if(ini() == 'toktokmall'){?>
                    <td width="120">Order type:</td>
                    <td width="200"><?=$order_type?></td>
                <?php } else {?>
                    <td width="120"></td>
                    <td width="200"></td>
                <?php }?>
            </tr>
           
        </tbody>
    </table>
    <br><br>
    <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
        <tbody>
            <tr>
                <td width="100"><b>Customer Details</b></td>
                <td width="150"></td>
                <td width="100"></td>
                <td width="200"></td>
            </tr>
            <tr>
                <td width="100">Name: </td>
                <td width="150"><?=$order_details['name']?></td>
                <td width="120">Moble No.: </td>
                <td width="150"><?=$order_details['conno']?></td>
            </tr>
            <tr>
                <td width="100">Email: </td>
                <td width="150"><?=$order_details['email']?></td>
                <td width="120">Address: </td>
                <td width="150"><?=$order_details['address']?></td>
            </tr>
            <tr>
                <td width="100">Notes: </td>
                <td width="150"><?=htmlspecialchars_decode(strtolower(str_replace('|::PA::|', '', $notes)));?></td>
                <td width="120"></td>
                <td width="200"></td>
            </tr>
        </tbody>
    </table>
    <?php if($order_details['paystatus'] == 1){ ?>
        <br><br>
        <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
            <tbody>
                <tr>
                    <td width="100"><b>Payment Details</b></td>
                    <td width="150"></td>
                    <td width="100"></td>
                    <td width="200"></td>
                </tr>
                <tr>
                    <td width="100">Payment Date: </td>
                    <td width="150"><?=$order_details['payment_date']?></td>
                    <td width="120">Payment Reference No.:</td>
                    <td width="200"><?=$order_details['paypanda_ref']?></td>
                </tr>
                <tr>
                    <td width="100">Payment Type:</td>
                    <td width="150"><?=$payment_type?> - <?=payment_option($order_details['payment_option']);?></td>
                    <td width="120">Payment Notes:</td>
                    <td width="200"><?=$payment_notes?></td>
                </tr>
                <tr>
                    <td width="100">Payment Status:</td>
                    <td width="150"><?=draw_transaction_status($order_details['paystatus'])?></td>
                    <td width="120"></td>
                    <td width="200"></td>
                </tr>
            </tbody>
        </table>
    <?php }?>

    <?php if($order_details['paystatus'] == 1){ ?>
        <?php if($order_details['sales_order_status'] == 'bc' || $order_details['sales_order_status'] == 'f' || $order_details['sales_order_status'] == 's'){ ?>
            <br><br>
            <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                <tbody>
                    <tr>
                        <td width="100"><b>Shipping Details</b></td>
                        <td width="150"></td>
                        <td width="100"></td>
                        <td width="200"></td>
                    </tr>

                <?php if($order_details['sales_order_status'] == 'bc' || $order_details['sales_order_status'] == 'f' || $order_details['sales_order_status'] == 's'){?>
                    <?php if(!empty($order_details['rider_name'])){?>
                        <tr>
                            <td width="100">Rider Name: </td>
                            <td width="150"><?=$order_details['rider_name']?></td>
                            <td width="120">Rider Plate Number:</td>
                            <td width="200"><?=$order_details['rider_platenum']?></td>
                        </tr>
                        <tr>
                            <td width="100">Rider Contact No:</td>
                            <td width="150"><?=$order_details['rider_conno']?></td>
                            <td width="120"></td>
                            <td width="200"></td>
                        </tr>
                    <?php }?>

                    <?php if($order_details['sales_order_status'] == 'f' || $order_details['sales_order_status'] == 's'){?>
                        <?php if($order_details['t_deliveryId'] != ''){?>
                            <tr>
                                <td width="100">Delivery ID: </td>
                                <td width="150"><?=$order_details['t_deliveryId']?></td>
                                <td width="120"></td>
                                <td width="200"></td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td width="100">Shipping Partner: </td>
                            <td width="150"><?=$shippingpartner;?></td>
                            <td width="120">Shipping Reference No:</td>
                            <td width="200"><?=$shipping_ref;?></td>
                        </tr>
                        <tr>
                            <td width="100">Shipping Notes:</td>
                            <td width="150"><?=$shipping_note?></td>
                            <td width="120">Actual Shipping Fee:</td>
                            <td width="200"><?=$actual_shipping_fee;?></td>
                        </tr>
                    <?php }?>

                    <?php if($order_details['sales_order_status'] == 'bc'){?>
                        <tr>
                            <td width="100">Delivery ID:</td>
                            <td width="150"><?=$order_details['t_deliveryId']?></td>
                            <td width="120">Actual Shipping Fee:</td>
                            <td width="200"><?=$actual_shipping_fee?></td>
                        </tr>
                    <?php }?>
                <?php }?>

                </tbody>
            </table>
        <?php }?>
    <?php }?>

    <?php if($order_details['paystatus'] == 1){ ?>
        <br><br>
        <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
            <tbody>
                <tr>
                    <td width="100"><b>Seller Details</b></td>
                    <td width="150"></td>
                    <td width="100"></td>
                    <td width="200"></td>
                </tr>
                <tr>
                    <td width="100">Shop Name: </td>
                    <td width="150"><?=$order_details['shopname']?></td>
                    <td width="120">Assigned to Branch:</td>
                    <td width="200"><?=$branchname?></td>
                </tr>
                <tr>
                    <td width="100">Pickup Address:</td>
                    <td width="150"><?=$pickup_address?></td>
                    <td width="120"></td>
                    <td width="200"></td>
                </tr>
            </tbody>
        </table>
    <?php } ?>

    <?php if(!empty($refunded_order)){?>
        <br><br>
            <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                <tbody>
                    <tr>
                        <td width="300"><b>Refunded Order</b></td>
                        <td width="150"><b>Quantity</b></td>
                        <td width="100"><b>Amount</b></td>
                    </tr>
                    <?php foreach($refunded_order as $refund){?>
                        <tr>
                            <td width="300"><u><?=$refund['name_of_item']?></u></td>
                            <td width="150"><?=number_format($refund['quantity'], 2)?></td>
                            <td width="100"><?=number_format($refund['amount'], 2)?></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
    <?php }?>

    <?php if(ini() == 'toktokmart'){?>
        <?php if(!empty($modified_order)){?>
            <br><br>
                <table class="table table-striped table-hover table-bordered"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                    <tbody>
                        <tr>
                            <td width="250"><b>Product Name</b></td>
                            <td width="100"><b>Quantity</b></td>
                            <td width="100"><b>Amount</b></td>
                            <td width="100"><b>Total Amount</b></td>
                        </tr>
                        <?php foreach($modified_order as $refund){?>
                            <tr>
                                <td width="250"><u><?=$refund['product_name']?></u></td>
                                <td width="100"><?=number_format($refund['quantity'], 2)?></td>
                                <td width="100"><?=number_format($refund['amount'], 2)?></td>
                                <td width="100"><?=number_format($refund['total_amount'], 2)?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
        <?php }?>
    <?php }?>

    <br><br>
    <h3 class="line-height-two">Order Items:</h3>
    <table cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-left" width="200">Item Name</th>
                <th class="text-center" width="50">Qty</th>
                <th class="text-right" width="150">Amount</th>
                <th class="text-right" width="140">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($order_items as $row){ ?>
            <?php 
                if(c_international() == 1 && $shopid == 0){
                    $item_amount       = currencyConvertedRate($row['amount'], $row['currval']);
                    $item_total_amount = currencyConvertedRate_peritem($row['amount'], $row['currval'], $row['qty']);
                    
                    $item_amount       = displayCurrencyValue_withPHP($row['amount'], $item_amount, $row['currcode']);
                    $item_total_amount = displayCurrencyValue_withPHP($row['total_amount'], $item_total_amount, $row['currcode']);
                }
                else if(c_international() == 1 && $shopid != 0){
                    $item_amount       = currencyConvertedRate($row['amount'], $row['currval']);
                    $item_total_amount = currencyConvertedRate_peritem($row['amount'], $row['currval'], $row['qty']);
                
                    $item_amount       = displayCurrencyValue($row['amount'], $item_amount, $row['currcode']);
                    $item_total_amount = displayCurrencyValue($row['total_amount'], $item_total_amount, $row['currcode']);
                }
                else{
                    $item_amount       = number_format($row["amount"], 2);
                    $item_total_amount = number_format($row["total_amount"], 2);
                }      

                $parent_prod  = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : '';
            ?>
                <tr>
                    <td class="text-left" width="200"><?=ucwords($parent_prod.$row["itemname"])?></td>
                    <td class="text-center" width="50"><?=$row["qty"]?></td>
                    <td class="text-left" width="150" ><?=$item_amount;?></td>
                    <td class="text-left" width="140" ><?=$item_total_amount;?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>