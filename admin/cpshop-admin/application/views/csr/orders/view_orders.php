    <div class="container-fluid">
        <div class="row flex-md-row-reverse">
            <!-- <div class="col-lg-12">
                <div class="col-md-12 text-right mb-3">
                </div>
                
            </div> -->
            <div class="col-12 d-md-none mb-3">
                <div class="text-right">
                    <?php if($prev_order != '-' && $prev_order != ''){?>
                        <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$prev_order)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a>
                    <?php } ?>
                    <?php if($next_order != '-' && $next_order != ''){?>
                        <a href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$next_order)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a>
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
                                    <?php
                                        if($order_details['notes'] != ''){
                                            $notes = $order_details['notes'];
                                        }else{
                                            $notes = '-';
                                        }
                                    ?>
                                    <div class="col-11">
                                        <label id="tm_landmark"><?=$notes?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="">
                                    <table class="table table-striped table-hover table-bordered table-grid table-item display nowrap" style="width:100%" id="table-item">
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
                                        
                                        <?php
                                            $vouchercodeArr = array();
                                            $vouchertotal = 0;
                                            foreach($voucher_details as $row){
                                                $vouchercodeArr[] = $row['vouchercode'];
                                                $vouchertotal += $row['voucheramount'];
                                            }
                                            
                                            if(empty($vouchercodeArr)){
                                                $vouchercodeArr = '-';
                                            }else{
                                                $vouchercodeArr = implode(", ", $vouchercodeArr);
                                            }

                                            $sub_total = max($order_details['totamt'] - $vouchertotal, 0);


                                        ?>

                                       
                                        <div class="col-12 col-md-6">
                                            <label class="">Voucher:</label>
                                            <label id="tm_voucher" class="font-weight-bold"><?=$vouchercodeArr?></label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Sub-Total:</label>
                                            <label id="tm_subtotal" class="font-weight-bold"><?=number_format($sub_total, 2)?></label>
                                        </div> 

                                        <div class="col-12 col-md-6">
                                            <label class="">Voucher Total:</label>
                                            <label id="tm_vouchertotal" class="font-weight-bold"><?=number_format($vouchertotal, 2)?></label>
                                        </div>
                                            
                                        <div class="col-12 col-md-6">
                                            <label class="">Shipping:</label>
                                            <label id="tm_shipping" class="font-weight-bold"><?=number_format($order_details['sf'], 2)?></label>
                                        </div>
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        <div class="col-12 col-md-6">
                                            <label class="">Total Amount:</label>
                                            <label id="tm_amount" class="green-text font-weight-bold"><?=number_format(max($sub_total + $order_details['sf'], 0), 2)?></label>
                                        </div>
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        <div class="col-12 col-md-6">
                                            <label class="">Order Status:</label>
                                            <?php
                                                if($order_details['paystatus'] == 1){
                                                    $order_status = draw_transaction_status($order_details['sales_order_status']);
                                                }else{
                                                    $order_status = draw_transaction_status($order_details['orderstatus']);
                                                }
                                            ?>
                                            <label id="tm_order_status" class="green-text font-weight"><?=$order_status?></label>
                                        </div>
                                       
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                        
                                    
        
                                    <?php if($order_details['paystatus'] == 1){ ?> 
                                        <div class="col-12 col-md-6">
                                            <label class="">Delivery Date:</label>
                                            <label id="tm_amount" class="green-text font-weight"><?=date("F d", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship'].' days'))?> to <?=date("F d, Y", strtotime($order_details['payment_date']. ' + '.$order_details['paid_daystoship_to'].' days'))?></label>
                                        </div>
                                        <!-- <div class="col-12 col-lg-8">
                                           
                                        </div> -->
                                    <?php }else{?>
                                        <div class="col-12 col-md-6">
                                            <label class="">Estimated Delivery Date:</label>
                                            <label id="tm_amount" class="green-text font-weight-bold"><?=date("F d", strtotime($order_details['date_ordered']. ' + '.$order_details['paid_daystoship'].' days'))?> to <?=date("F d, Y", strtotime($order_details['date_ordered']. ' + '.$order_details['paid_daystoship_to'].' days'))?></label>
                                        </div>
                                        <!-- <div class="col-12 col-md">
                                            
                                        </div> -->
                                    <?php }?>
                                        <div class="col-12">
                                            <div class="border-bottom w-100"></div>
                                        </div>
                                    </div>
                                            
                                    <?php if($shopid == 0){
                                        if($order_details['admin_drno'] == '0' || $order_details['admin_drno'] == 0 || $order_details['admin_drno'] == ''){
                                            $admin_drno = "-";
                                        }else{
                                            $admin_drno = $order_details['admin_drno'];
                                        }
        
                                        if($order_details['admin_sono'] == '0' || $order_details['admin_sono'] == 0 || $order_details['admin_sono'] == ''){
                                            $admin_sono = "-";
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
                                                <?php
                                                    if(!empty($referral['referral_code'])){
                                                        $referral_code = $referral['referral_code'];
                                                    }else{
                                                        $referral_code = '-';
                                                    }
                                                ?>
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
                                            <div class="col-12 col-md-6">
                                                <label class="">Payment Date:</label>
                                                <label id="tm_payment_date" class="green-text font-weight-bold"><?=$order_details['payment_date']?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-6">
                                                <label class="">Payment Reference No.:</label>
                                                <label id="tm_payment_ref_num" class="green-text font-weight-bold"><?=$order_details['paypanda_ref']?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <?php $payment_type = ($order_details['payment_method'] == '') ? 'Tagged as Paid(Admin)' : $order_details['payment_method'];?>
                                            <div class="col-12 col-md-6">
                                                <label class="">Payment Type:</label>
                                                <label id="tm_payment_type" class="green-text font-weight-bold"><?=$payment_type?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-6">
                                                <label class="">Payment Notes:</label>
                                                <label id="tm_payment_note" class="green-text font-weight-bold"><?=$order_details['payment_notes']?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                               
                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-6">
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
                                            <div class="col-12 col-md-6">
                                                <label class="">Shop Name:</label>
                                                <label id="tm_shopname"><?=$order_details['shopname']?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md-6">
                                                
                                            </div> -->
        
                                            <?php 
                                                if(empty($branch_details)){
                                                    $branchname = 'Main';
                                                    $pickup_address = $order_details['pickup_address'];
                                                }else{
                                                    $branchname = $branch_details->branchname;
                                                    $pickup_address = $branch_details->address;
                                                }
                                            ?>
                                            <div class="col-12 col-md-6">
                                                <label class="">Assigned to Branch:</label>
                                                <label id="branchname"><?=$branchname?></label>
                                            </div>
                                            <!-- <div class="col-12 col-md-6">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-6">
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
                                                    <div class="col-md-8" style="font-weight: normal">
                                                        <span><?=$logs['description']?></span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span><?=$logs['date_created']?></span>
                                                    </div>
                                                <?php } ?>

                                                <div class="col-md-8">
                                                    <span>Payment for order has been confirmed.</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span><?=$order_details['payment_date']?></span>
                                                </div>

                                                <div class="col-md-8">
                                                    <span>Order has been placed.</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span><?=$order_details['date_ordered']?></span>
                                                </div>
                                                
                                            <?php }else{?>

                                                <?php if($order_details['payment_date'] != '0000-00-00 00:00:00'){?>
                                                    <div class="col-md-8">
                                                        <span>Payment for order has been confirmed.</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span><?=$order_details['payment_date']?></span>
                                                    </div>
                                                <?php }?>

                                                <div class="col-md-8">
                                                    <span>Order has been placed.</span>
                                                </div>
                                                <div class="col-md-4">
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
    </div>