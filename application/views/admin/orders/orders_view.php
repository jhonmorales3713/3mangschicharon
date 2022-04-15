<style>
    .pac-container {
    z-index: 1051 !important;
    }
    .modal{
        z-index: 1050;   
    }
</style> 
<?php

    //$sub_total      = max($order_details['totamt'] - $vouchertotal, 0);
    // $order_status   = ($order_details['paystatus'] == 1) ? draw_transaction_status($order_details['sales_order_status']) : $order_status = draw_transaction_status($order_details['orderstatus']);
    // $order_status   = ($order_details['paystatus'] == 0) ? draw_transaction_status('Unpaid') : $order_status;
    // $notes          = ($order_details['notes'] != '') ? $order_details['notes'] : 'None';;
    // $referral_code  = (!empty($referral['referral_code'])) ? $referral['referral_code'] : 'None';
    // $payment_notes  = ($order_details['payment_notes'] != '') ? $order_details['payment_notes']:'None';
    // $shipping_note  = ($order_details['shipping_note'] != '' || !empty($order_details['shipping_note'])) ? $order_details['shipping_note']:'None';

    $sub_total_converted = 0; ;
    foreach(json_decode($order_details['order_data']) as $row ){
        $sub_total_converted += floatval($row->price); 
    }
    $sub_total_converted=number_format($sub_total_converted, 2); 
    $shipping_fee_converted = number_format($order_details['delivery_amount'],2);
    $total_amount_converted = $sub_total_converted + $shipping_fee_converted;
    $payment_method = json_decode($order_details['payment_data'])->payment_method_name;
    // $special_upper = ["&NTILDE", "&NDASH",'|::PA::|'];
    // $special_format   = ["&Ntilde", "&ndash",''];
    // $order_details['name']= str_replace($special_upper, $special_format, $order_details['name']);
    // $order_details['address']= str_replace($special_upper, $special_format, $order_details['address']);
    
?>

<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_orders/orders_home/Orders');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold">View Orders</span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular"><?=$reference_num?></span>
        
    </div>
</div>

<div class="col ml-3 container-fluid">
    <div class="row ">
        <div class="col-md-4  order-md-2">
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
                                    <label id="tm_name"><?=json_decode($order_details['shipping_data'])->full_name?></label>
                                </div>
                                <div class="col-1 pr-2 d-flex justify-content-center">
                                    <label class=""><i class="fa fa-mobile" aria-hidden="true"></i></label>
                                </div>
                                <div class="col-11">
                                    <label id="tm_mobile"><?=json_decode($order_details['shipping_data'])->contact_no?></label>
                                </div>
                                <div class="col-1 pr-2 d-flex justify-content-center">
                                    <label class="" ><i class="fa fa-envelope" aria-hidden="true"></i></label>
                                </div>
                                <div class="col-11">
                                    <label id="tm_email" ><?=isset(json_decode($order_details['shipping_data'])->email)?json_decode($order_details['shipping_data'])->email:'none';?></label>
                                </div>
                                <div class="col-1 pr-2 d-flex justify-content-center">
                                    <label class="" ><i class="fa fa-truck" aria-hidden="true"></i></label>
                                </div>
                                <div class="col-11">
                                    <label id="tm_address"><?=json_decode($order_details['shipping_data'])->address.', Brgy. '.json_decode($order_details['shipping_data'])->barangay.' '.json_decode($order_details['shipping_data'])->city.' '.json_decode($order_details['shipping_data'])->zip_code.', '.json_decode($order_details['shipping_data'])->province?></label>
                                </div>
                                <div class="col-1 pr-2 d-flex justify-content-center">
                                    <label class="" ><i class="fa fa-sticky-note" aria-hidden="true"></i></label>
                                </div>
                                <div class="col-11">
                                    <label id="tm_notes"style="display: inline-block; width: 300px; overflow: hidden"><?=json_decode($order_details['shipping_data'])->notes//str_replace($special_upper, $special_format, $notes);?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                        <label id="tm_payment_date" class="green-text font-weight-bold"><?=($payment_method=='COD' && $order_details['status_id']==5)?json_decode($order_details['payment_data'])->paid_date:'None'?></label>
                                    </div>
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                    <div class="col-12 col-md-12">
                                        <label class="">Payment Reference No.:</label>
                                        <label id="tm_payment_ref_num" class="green-text font-weight-bold"><?=$payment_method=='COD'?'None':json_decode($order_details['payment_data'])->ref_num?></label>
                                    </div>
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                    <div class="col-12 col-md-12">
                                        <label class="">Payment Type:</label>
                                        <label id="tm_payment_type" class="green-text font-weight-bold"><?=$payment_method?></label>
                                    </div>
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                    <!-- <div class="col-12 col-md-12">
                                        <label class="">Payment Notes:</label>
                                        <label id="tm_payment_note" class="green-text font-weight-bold"><?=$payment_notes;?></label>
                                    </div> -->
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-12">
                                        <label class="">Payment Status:</label>
                                        <label id="tm_payment_status" class="green-text font-weight-bold"><?=($payment_method=='COD' && $order_details['status_id']==5)?'Paid':($order_details['status_id']==6||$order_details['status_id']==7||$order_details['status_id']==0)?'Cancelled':'Pending'?></label>
                                    </div>
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                </div>
                                        
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-8  order-md-1">
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
                                <!-- <div class="col-auto d-none d-md-flex justify-content-end">
                                    <div>
                                        <?php if($next_order != '-' && $next_order != ''){?>
                                                <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$next_order.'/'.$order_status_view)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a> 
                                        <?php } ?>
                                        <?php if($prev_order != '-' && $prev_order != ''){?>
                                            <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$prev_order.'/'.$order_status_view)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a> 
                                        <?php } ?>
                                    </div>
                                </div> -->
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
                                        <label id="tm_order_date" class="green-text font-weight-bold"><?=$order_details['date_created']?></label>
                                    </div>
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                    <div class="col-12 col-md-6">
                                        <label class="">Transaction Reference No.:</label>
                                        <label id="tm_order_reference_num" class="green-text font-weight-bold"><?=$order_details['order_id']?></label>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="">Sub-Total:</label>
                                        <label id="tm_subtotal" class="font-weight-bold"><?=$sub_total_converted;?></label>
                                    </div> 

                                    <!-- <div class="col-12 col-md-6">
                                        <label class="">Voucher Total:</label>
                                        <label id="tm_vouchertotal" class="font-weight-bold"><?=$vouchertotal_converted?></label>
                                    </div> -->

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

                                    <!-- <div class="col-12 col-md-6">
                                        <label class="">Refunded Amount:</label>
                                        <label id="tm_amount" class="green-text font-weight-bold"><?=$total_refund_amount_converted;?></label>
                                    </div> -->
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                    <div class="col-12 col-md-6">
                                        <label class="">Order Status:</label>
                                        <label id="tm_order_status" class="green-text font-weight"><?=get_status_ui($order_details['status_id'])?></label>
                                    </div>
                                    
                                    <!-- <div class="col-12 col-md">
                                        
                                    </div> -->
                                    
                                
    
                                <?php if($order_details['status_id'] == 1){ ?> 

                                    <?php if($payment_method!='COD'){?>
                                        <div class="col-12 col-md-6">
                                            <label class="">Delivery Status:</label>
                                            <label class='badge badge-success'>For Processing</label>
                                        </div>
                                    <?php }else{?>
                                        <div class="col-12 col-md-6">
                                            <label class="">Estimated Shipping Date:</label>
                                            <label id="tm_amount" class="green-text font-weight"><?=date("F d, Y", strtotime($order_details['date_created'].' + 1 day'))?></label>
                                        </div>
                                    <?php } ?>

                                    <?php if($order_details['date_fulfilled'] != ''){?>
                                        <div class="col-12 col-md-6">
                                            <label class="">Actual Shipping Date:</label>
                                            <label id="tm_amount" class="green-text font-weight"><?=date("F d, Y", strtotime($order_details['shipped_date']))?></label>
                                        </div>
                                    <?php }?>
                                <?php }else{?>

                                    <?php if($payment_method!='COD'){?>
                                        <div class="col-12 col-md-6">
                                            <label class="">Delivery Status:</label>
                                            <label class='badge badge-success'>For Processing</label>
                                        </div>
                                    <?php }else{?>
                                        <div class="col-12 col-md-6">
                                            <label class="">Estimated Shipping Date:</label>
                                            <label id="tm_amount" class="green-text font-weight"><?=date("F d, Y", strtotime($order_details['date_created'].' + 1 day'))?></label>
                                        </div>
                                    <?php } ?>
                    
                                <?php }?>

                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($order_details['date_readyforpickup'] != ''){?>
                    <div class="col-12 mb-4">
                        <div class="card detail-container">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Shipping Details
                                </h3>
                            </div>
                            <div class="card-body px-lg-5">
                                <div class="">
                                    <?php if(isset((json_decode($order_details['shipping_data'])->rider->partner))){?>
                                        <div class="row">
                                            <div class="col-12 font-weight-bold">
                                                <label class="">Rider Information:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Shipping Partner:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_partner_name"><?=($this->model_orders->getshippingpartners(json_decode($order_details['shipping_data'])->rider->partner)[0]['name']);?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Rider Name:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_name"><?=json_decode($order_details['shipping_data'])->rider->name?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Reference Number:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_platenum"><?=json_decode($order_details['shipping_data'])->rider->reference_num?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Contact No.:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_conno"><?=json_decode($order_details['shipping_data'])->rider->contact_no?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Vehicle Type:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_vehicle"><?=json_decode($order_details['shipping_data'])->rider->vehicle_type?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Delivery Fee:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_conno"><?=json_decode($order_details['shipping_data'])->rider->delivery_fee?></label>
                                            </div>
                                        </div>
                                    <?php }else{?>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <label class="">Rider Name:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_name"><?=json_decode($order_details['shipping_data'])->rider->name?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Contact No.:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_conno"><?=json_decode($order_details['shipping_data'])->rider->contact_no?></label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="">Vehicle Type:</label>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label id="f_rider_vehicle"><?=json_decode($order_details['shipping_data'])->rider->vehicle_type?></label>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <?php if($payment_method=='COD'){?>
                                            <div class="col-md-4" style="padding-top:13px;">
                                                <span><?=json_decode($order_details['shipping_data'])->full_name?> </span>
                                            </div>
                                            <?php if($order_details['date_delivered'] != '' ){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is Delivered.</span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_delivered']?></span>
                                                </div>
                                            <?php } if($order_details['status_id'] == 0 && $order_details['date_deliveryfailed2'] != ''){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is Cancelled.</span><br>
                                                    <span class="font-weight-bold">Reason:<?=json_decode($order_details['reasons'])->cancel;?></span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_deliveryfailed2']?></span>
                                                </div>
                                            <?php } if($order_details['date_deliveryfailed2'] != '' ||$order_details['status_id'] == 0 ){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is Re-delivered.</span><br>
                                                    <span class="font-weight-bold">Reason:<?=json_decode($order_details['reasons'])->redeliver2;?></span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_deliveryfailed2']?></span>
                                                </div>
                                            <?php }if($order_details['status_id'] == 0 && $order_details['date_deliveryfailed2'] == ''){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is Cancelled.</span><br>
                                                    <span class="font-weight-bold">Reason:<?=json_decode($order_details['reasons'])->cancel;?></span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_deliveryfailed2']?></span>
                                                </div>
                                            <?php }  if($order_details['date_deliveryfailed1'] != ''  ||$order_details['status_id'] == 0){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is Re-delivered.</span><br>
                                                    <span class="font-weight-bold">Reason:<?=json_decode($order_details['reasons'])->redeliver1;?></span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_deliveryfailed1']?></span>
                                                </div>
                                            <?php }if(($order_details['status_id'] >= 4  ||$order_details['status_id'] == 0)&& $order_details['status_id'] != 6&& $order_details['status_id'] != 7){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is fulfilled.</span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_fulfilled']?></span>
                                                </div>
                                            <?php }if(($order_details['status_id'] >= 3  ||$order_details['status_id'] == 0)  && $order_details['status_id'] != 6&& $order_details['status_id'] != 7){?>
                                                
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is ready for pickup.</span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_readyforpickup']?></span>
                                                    <span data-toggle="modal" data-target="#itemPickedupModal"><u>View Image</u></span>
                                                </div>
                                            <?php }  if($order_details['status_id'] == 6 || $order_details['status_id'] == 7){?>
                                                <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                                <div class="col-md-6" style="padding-top:13px;">
                                                    <span>Order is Cancelled By <?=($order_details['status_id'] == 6)?'Customer': 'Admin';?>.</span><br>
                                                    <span class="font-weight-bold">Reason:<?=json_decode($order_details['reasons'])->decline;?></span>
                                                </div>
                                                <div class="col-md-4" style="padding-top:13px;">
                                                    <span></span>
                                                </div>
                                                <div class="col-md-2" style="padding-top:5px;">
                                                    <span><?=$order_details['date_declined']?></span>
                                                </div>
                                            <?php }?>
                                            
                                            <div class="w-100" style="border-bottom: 1px dotted black;"></div>
                                            <div class="col-md-6" style="padding-top:13px;">
                                                <span>Order has been placed.</span>
                                            </div>
                                            <div class="col-md-4" style="padding-top:13px;">
                                                <span></span>
                                            </div>
                                            <div class="col-md-2" style="padding-top:5px;">
                                                <span><?=$order_details['date_created']?></span>
                                            </div>
                                        <?php }else{ ?>

                                        <!-- <div class="col-md-6" style="padding-top:13px;">
                                            <span>Payment for order has been confirmed.</span>
                                        </div>
                                        <div class="col-md-4" style="padding-top:13px;">
                                            <span></span>
                                        </div>
                                        <div class="col-md-2" style="padding-top:5px;">
                                            <span><?=$order_details['payment_date']?></span>
                                        </div> -->

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
        <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn-mobile-w-100 btn btn-success printBtn mb-2 mb-md-0" id="printBtn" data-reference_num="<?=$reference_num?>">Print</button>
                    <button type="button" class="btn-mobile-w-100 btn btn-outline-secondary backBtn mb-2 mb-md-0" id="backBtn">Close</button>
                    <!-- <?php if($order_details['paystatus'] == 1 && $branch_count > 0 && $order_details['sales_order_status'] == 'p' && $this->loginstate->get_access()['transactions']['reassign'] == 1 && $branchid == 0 && $refunded_all == 0){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-success reassignBtn mb-2 mb-md-0" id="reassignBtn" data-value="<?=$mainshopid?>" data-branchid="<?=$branch_id?>" data-reference_num="<?=$reference_num?>">Re-Assign</button>
                    <?php } ?>  -->

                    <?php if($order_details['status_id'] == 1){ ?>
                        <button type="button" class=" btn btn-warning processBtn mb-2 mb-md-0" id="processBtn" data-value="<?=$reference_num?>">Process Order</button>
                        <button type="button" class="btn-mobile-w-100 btn btn-danger waves-effect waves-light DeclineOrderBtn" id="DeclineOrderBtn" data-value="<?=$reference_num?>">Decline Order</button> 
                    <?php } ?>
                    <?php if($order_details['status_id'] == 2){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-outline-info waves-effect waves-light readyforpickupBtn mb-2 mb-md-0" id="readyforpickupBtn" data-value="<?=$reference_num?>">Mark as Ready for Pickup</button>
                    <?php } ?>
                    <?php if($order_details['status_id'] == 3){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-outline-warning waves-effect waves-light fulfillmentBtn mb-2 mb-md-0" id="fulfillmentBtn" data-value="<?=$reference_num?>">Mark as Fulfilled</button>
                    <?php } ?>
                    <?php if($order_details['status_id'] == 4 || $order_details['status_id'] == 8 || $order_details['status_id'] == 9){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-outline-success waves-effect waves-light confirmedBtn mb-2 mb-md-0" id="confirmedBtn" data-value="<?=$reference_num?>">Change Delivery Status</button>
                    <?php } ?>
                        <!-- <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'po' && $this->loginstate->get_access()['transactions']['ready_pickup'] == 1 && $this->loginstate->get_access()['transactions']['mark_fulfilled'] == 1 && $refunded_all == 0){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light readyPickupBtn mb-2 mb-md-0" id="readyPickupBtn" data-value="<?=$url_ref_num?>">Book toktok</button>
                        <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light fulfillmentBtn mb-2 mb-md-0" id="fulfillmentBtn" data-value="<?=$url_ref_num?>">Mark as Fulfilled</button>
                    <?php }else if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'po' && $this->loginstate->get_access()['transactions']['ready_pickup'] == 1 && $refunded_all == 0){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light readyPickupBtn mb-2 mb-md-0" id="readyPickupBtn" data-value="<?=$url_ref_num?>">Book toktok</button>
                    <?php }else if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'po' && $this->loginstate->get_access()['transactions']['mark_fulfilled'] == 1 && $refunded_all == 0){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light fulfillmentBtn mb-2 mb-md-0" id="fulfillmentBtn" data-value="<?=$url_ref_num?>">Mark as Fulfilled</button>
                    <?php } ?>
                    <?php if(!empty($order_details['sales_order_status']) && $order_details['sales_order_status'] == 'rp' && $this->loginstate->get_access()['transactions']['booking_confirmed'] == 1 && $refunded_all == 0){ ?>
                        <button type="button" class="btn-mobile-w-100 btn btn-danger waves-effect waves-light cancelOrderBtn" id="cancelOrderBtn" data-value="<?=$url_ref_num?>">Cancel Booking</button> -->
                        <!-- <button type="button" class="btn btn-info waves-effect waves-light bookingConfirmBtn" id="bookingConfirmBtn" data-value="<?=$url_ref_num?>">Booking Confirmed</button> -->
                    <!-- <?php } ?>
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
                    <?php } ?> -->
                </div>
            </div>
        </div>
    </div>
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
                            <input type="text" class="hidden" name="f_total_amount" id="f_total_amount" value="<?=$total_amount_converted?>" hidden>
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
<div id="order_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <?php $form_id = 'form_save_process';
                $header = 'Process Order';
                if($order_details['status_id'] == 1){
                    $form_id='form_save_process';
                }
                if($order_details['status_id'] == 2 || $order_details['status_id'] >7){
                    $form_id='form_save_ready_pickup';
                    $header = 'Order Ready for Pickup';
                }
                if($order_details['status_id'] == 3){
                    $form_id='form_save_fulfillment_modal';
                    $header = 'Order Fulfill';
                }
                if($order_details['status_id'] == 4 || $order_details['status_id'] == 8 || $order_details['status_id'] == 9){
                    $form_id='form_save_delivery_confirmed';
                    $header = 'Delivery Status';
                }
            ?>
            <form id="<?=$form_id?>" enctype="multipart/form-data" method="post" action="" >
                <div class="modal-header">
                    <div class="col-md-12">
                        <h4 id="tm_header_ref" class="modal-title" id="order_modal_title" style="color:black;"><?=$header?></h4>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" name="reference_num" id="reference_num" value="<?=$reference_num?>" hidden>
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
                                <label class="order_date"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Transaction Reference #:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_reference_num" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Date</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label id="payment_date"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Reference No.</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="payment_ref_num" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Total Amount:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="amount" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Status</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="payment_status"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Order Status:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_status"></label>
                            </div>
                        </div>
                        <div class="row hidden">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" name="order_id" id="order_id" class="form-control" value="<?$reference_num?>" >
                                </div>
                            </div>
                        </div>
                        
                        <?php if($order_details['status_id'] == 2){?>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <label class="">Shipping Partner:</label>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <select class="form-control" id="shipping_partner" name="shipping_partner">
                                        <option value="">Internal</option>
                                        <?php foreach($shipping_partners as $row){?>
                                            <option value="<?=$row['id']?>"><?=$row['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <label class="">Notes:</label>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea type="text" class="form-control" name="rp_notes" id="rp_notes" placeholder="Enter notes"><?=strtoupper(htmlspecialchars_decode(strtolower((json_decode($order_details['shipping_data'])->notes))));?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div id="pac-container">
                                    <label for="">Order Photo <span class="asterisk"></span></label>
                                    <br>
                                    <small>Attachment (optional) | Allowed format (jpg, jpeg, png) | Max size: 2mb</small>
                                    <input type="file" id = "order_attachment" name = "order_attachment[]" class="form-control" placeholder="Attachment" multiple>
                                    
                                    <!-- <label id="tm_address font-weight-bold h3"><?=json_decode($order_details['shipping_data'])->address.', Brgy. '.json_decode($order_details['shipping_data'])->barangay.' '.json_decode($order_details['shipping_data'])->city.' '.json_decode($order_details['shipping_data'])->zip_code.', '.json_decode($order_details['shipping_data'])->province?></label> -->
                                </div>
                            </div>
                            <div class="col-12">
                            <div class="form-group row" id = "img-upload-preview">

                            </div>
                        </div>

                        <div class="row col-12">
                            <div class="col-12 col-lg-6 note_notinternal">
                                Reference #
                                <input type="text" class="form-control" name="c_reference_num" id="c_reference_num">
                            </div>
                            <div class="col-12 col-lg-6 note_notinternal">
                                Driver Name
                                <input type="text" class="form-control" name="c_driver_name" id="c_driver_name">
                            </div>
                            <div class="col-12 col-lg-6 note_notinternal">
                                Vehicle Type
                                <input type="text" class="form-control" name="c_vehicle_type" id="c_vehicle_type">
                            </div>
                            <div class="col-12 col-lg-6 note_notinternal">
                                Contact No.
                                <input type="text" class="form-control" name="c_contact_number" id="c_contact_number">
                            </div>
                            <div class="col-12 col-lg-6 note_notinternal">
                                Amount
                                <input type="number" class="form-control allownumericwithdecimal" name="c_delivery_fee" id="c_delivery_fee">
                            </div>

                            <div class="col-12 note_notinternal mt-2">
                                <div class="alert alert-warning" role="alert">
                                    Weight Limit: Up to 20 kg. Size Limit (L x W x H): 20 × 20 × 20 inches. Delivery fee from other courier that will exceed the base Amount
                                    of delivery fee settled to customer will be shouldered by 3Mang's Chicharon.
                                </div>
                            </div>
                        </div>

                        <div class="row col">
                            <div class="col-12 col-sm-12 font-weight-bold">
                                    <label for="">Recipient Address <span class="asterisk"></span></label>
                                    <br>
                                    <label id="tm_address font-weight-bold h3"><?=json_decode($order_details['shipping_data'])->address.', Brgy. '.json_decode($order_details['shipping_data'])->barangay.' '.json_decode($order_details['shipping_data'])->city.' '.json_decode($order_details['shipping_data'])->zip_code.', '.json_decode($order_details['shipping_data'])->province?></label>
                                
                            </div>
                        </div>
                        <?php }else if($order_details['status_id'] == 3 || $order_details['status_id'] == 4){?>
                            <?php if(isset((json_decode($order_details['shipping_data'])->rider->partner))){?>
                                <div class="row">
                                    <div class="col-12 font-weight-bold">
                                        <label class="">Rider Information:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Shipping Partner:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_partner_name"><?=($this->model_orders->getshippingpartners(json_decode($order_details['shipping_data'])->rider->partner)[0]['name']);?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Rider Name:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_name"><?=json_decode($order_details['shipping_data'])->rider->name?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Reference Number:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_platenum"><?=json_decode($order_details['shipping_data'])->rider->reference_num?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Contact No.:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_conno"><?=json_decode($order_details['shipping_data'])->rider->contact_no?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Vehicle Type:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_vehicle"><?=json_decode($order_details['shipping_data'])->rider->vehicle_type?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Delivery Fee:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_conno"><?=json_decode($order_details['shipping_data'])->rider->delivery_fee?></label>
                                    </div>
                                </div>
                                <?php }else{?>
                                <div class="row">
                                    <div class="col-12 font-weight-bold">
                                        <label class="">Rider Information:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Rider Name:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_name"><?=json_decode($order_details['shipping_data'])->rider->name?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Contact No.:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_conno"><?=json_decode($order_details['shipping_data'])->rider->contact_no?></label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="">Vehicle Type:</label>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label id="f_rider_vehicle"><?=json_decode($order_details['shipping_data'])->rider->vehicle_type?></label>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php }if($order_details['status_id'] == 4 || $order_details['status_id'] == 8 || $order_details['status_id'] == 9) {
                                $redeliver = $order_details['date_deliveryfailed1'] == ''?8:9;
                                ?>
                             <div class="row">
                                <div class="col-12 font-weight-bold">
                                    <label class="">Delivery Status</label>
                                </div>
                                <div class="col">
                                    <input type="radio" name="delivery_option" value="5" text="Delivered">Delivered Success</input>
                                </div>
                                <?php if($order_details['date_delivered'] == '' && $order_details['date_deliveryfailed2'] == '' && $order_details['status_id'] <= 9){?>
                                <div class="col">
                                    <input type="radio"name="delivery_option" value="<?=$redeliver?>" text="Delivered">Re-Deliver Next Business Day</input>
                                </div>
                                <?php } if($order_details['date_delivered'] == '' && $order_details['date_deliveryfailed2']!='' && $order_details['date_deliveryfailed2'] != '' && $order_details['status_id'] <= 9){?>
                                <div class="col">
                                    <input type="radio"name="delivery_option" value="0" text="Delivered">Delivery Failed</input>
                                </div>
                                <?php }?>
                                <div class="col-12 font-weight-bold delivery_reason">
                                    <label class="">Reason</label>
                                </div>
                                <div class="col delivery_reason">
                                    <input type="radio"name="reason_option" value="Unreachable Number" >Unreachable Number</input>
                                </div>
                                <div class="col delivery_reason">
                                    <input type="radio"name="reason_option" value="Unreachable Area" >Unreachable Area</input>
                                </div>
                                
                                <?php if($order_details['date_delivered'] == ''  && $order_details['date_deliveryfailed2'] == '' && $order_details['status_id'] <= 9){?>
                                <div class="col delivery_reason">
                                    <input type="radio"name="reason_option" value="Request to re-deliver by customer" >Request to re-deliver by customer</input>
                                </div>
                                <?php }else{?>
                                <div class="col delivery_reason">
                                    <input type="radio"name="reason_option" value="Reached maximum re-delivery count" >Reached maximum re-delivery count</input>
                                </div>
                                <?php } ?>
                                <div class="col delivery_reason">
                                    <input type="radio"name="reason_option" value="Others" >Others</input>
                                </div>
                                <div class="col-12 reason_option_others" >
                                    <textarea class="form-control" name="f_reason" id="f_reason" placeholder="Type reason here"></textarea>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="alert alert-warning" role="alert">
                                        Note: You can only re-deliver twice to customer and the system will automatically tagged as delivery failed.
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                        
                        <?php if($order_details['status_id'] == 1){?>
                            <button type="submit" id="btn_tbl_confirm" class="btn btn-success waves-effect waves-light" aria-label="Close">Process Order</button>
                        <?php }?>
                        <?php if($order_details['status_id'] == 2){?>
                            <button type="submit" id="btn_readyforpickup" class=" btn btn-outline-info waves-effect waves-light" aria-label="Close">Mark as Ready for Pickup</button>
                        <?php }?>
                        <?php if($order_details['status_id'] == 3){?>
                            <button type="submit" id="btn_fulfilled" class=" btn btn-outline-warning waves-effect waves-light" aria-label="Close">Mark as Fulfilled</button>
                        <?php }?>
                        <?php if($order_details['status_id'] == 4 || $order_details['status_id'] == 8 || $order_details['status_id'] == 9){?>
                            <button type="submit" id="btn_delivered" class=" btn btn-outline-success waves-effect waves-light" aria-label="Close">Proceed</button>
                        <?php }?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ready for Pickup Modal -->
<!-- <div id="readyPickup_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
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
                                <label class="order_date"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Transaction Reference #:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_reference_num" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Date</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label id="payment_date"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Reference No.</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="payment_ref_num" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Total Amount:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="amount" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Status</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="payment_status"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Order Status:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_status"></label>
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
</div> -->

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
                    
                        <input type="text" class="hidden" name="reference_number" id="reference_number" value="<?=$reference_num?>" hidden>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <i class="fa fa-info no-margin">
                                </i> Order Summary
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Transaction Date:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_date"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Transaction Reference #:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_reference_num" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Date</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label id="payment_date"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Reference No.</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="payment_ref_num" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Total Amount:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="amount" class="green-text"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Payment Status</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="payment_status"></label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="">Order Status:</label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="order_status"></label>
                            </div>
                        </div>

                    <div class="row hidden">
                        <div class="col-12">
                            <div class="form-group">
                                <label>ID</label>
                                <input type="text" name="order_id" id="order_id" class="form-control" value="<?$reference_num?>" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="">Why do you want to cancel the booking?</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col ">
                            <input type="radio"name="reason_option" value="Repetitive Order" >Repetitive Order</input>
                        </div>
                        <div class="col">
                            <input type="radio"name="reason_option" value="Unreachable Area" >Unreachable Area</input>
                        </div>
                        <div class="col">
                            <input type="radio"name="reason_option" value="Spam Order" >Spam Order</input>
                        </div>
                        <div class="col">
                            <input type="radio"name="reason_option" value="Others" >Others</input>
                        </div>
                        <div class="col-12 reason_option_others" >
                            <textarea class="form-control" name="f_reason" id="f_reason" placeholder="Type reason here"></textarea>
                        </div>
                    </div>
                                    
                    <!-- <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <textarea type="text" class="form-control" name="cn_cancellation_notes" id="cn_cancellation_notes" placeholder="Enter notes (optional)"></textarea>
                            </div>
                        </div>
                    </div> -->

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
                <?php foreach($images->result_array() as $image){?>
                <img src="<?=base_url('assets/uploads/orders/'.str_replace('=','',$image['filename']));?>" class="img-fluid">
                <?php }?>
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

<input type="hidden" id="url_ref_num" value="<?=$reference_num?>">


<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/orders/orders_view.js');?>"></script>
