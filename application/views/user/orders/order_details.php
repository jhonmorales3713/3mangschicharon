<div class="container">
    <div class="row">
        <div class="col-12">
            <h5>Order Details</h5>
            <div class="row">

                <?php 
                    $order_data = json_decode($orders[0]['order_data'],true);
                    $payment_data = json_decode($orders[0]['payment_data'],true);
                    $shipping_data = json_decode($orders[0]['shipping_data'],true);
                    $order_amount = number_format($orders[0]['total_amount'],2);
                    $delivery_amount = number_format($orders[0]['delivery_amount'],2);
                    $total_amount = number_format(floatval($orders[0]['total_amount']) + floatval($orders[0]['delivery_amount']),2);
                    $total_quantity = 0;
                ?>

                <div class="col-lg-12 col-md-12 col-sm-12 p20">
                    <b>Order ID:</b> <?= $orders[0]['order_id']; ?> <?= get_status_ui($orders[0]['status_id']); ?><br>
                    <b>Order Date: </b><?= format_date_full($orders[0]['date_created']); ?>                    
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 p20">                   
                    <div class="row">
                        <div class="col-12 text-center">
                            <b>Item Summary</b>
                        </div>                                                                  
                    </div>
                    <hr>
                    <div class="row">
                        <?php foreach($order_data as $order){ ?>     
                            <div class="col-2 mt5">
                                <img src="<?= base_url('assets/uploads/products/'.$order['img']); ?>" alt="" width="100%">
                            </div>                       
                            <div class="col-5 mt5">
                                <small><b><?= $order['name']; ?></b></small>
                            </div>       
                            <div class="col-2 mt5">
                                <small>Qty: <?= $order['quantity']; ?></small>
                            </div>                     
                            <div class="col-3 mt5 clearfix">
                                <small class="float-right"><b><?= number_format($order['amount'],2); ?></b></small>
                            </div>
                            <?php $total_quantity += intval($order['quantity']); ?>
                        <?php }?>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-9 text-right">
                            <small><b>Sub Total</b></small>
                        </div>
                        <div class="col-3 clearfix">                            
                            <small><b class="float-right"><?= $order_amount; ?></b></small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1"></div>
                <div class="col-lg-5 col-md-5 col-sm-11 p20">
                    <div class="row">
                        <div class="col-12 text-center">
                            <b>Total Summary</b>
                        </div>     
                        <hr>                                          
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <p>Sub Total (<?= $total_quantity; ?>) item(s)</p>
                        </div>
                        <div class="col-4 clearfix">
                            <p class="float-right"><?= $order_amount; ?></p>
                        </div>
                        <div class="col-8">
                            <p>Shipping Fee</p>
                        </div>
                        <div class="col-4 clearfix">
                            <p class="float-right"><?= $delivery_amount; ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <b>Total</b>
                        </div>
                        <div class="col-4 clearfix">                                
                            <b class="float-right">&#8369; <?= $total_amount; ?></b>
                        </div>
                    </div>
                </div>                
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <b>Shipping Details</b>
                    <hr>
                    <small>Deliver to: </small><br>
                    <strong><?= $shipping_data['full_name']; ?></strong> - <?= $shipping_data['contact_no']; ?><br>
                    <?= $shipping_data['address'].' '.$shipping_data['barangay'].', '.$shipping_data['city'].' '.$shipping_data['province']; ?><br><br>
                    <?php if(in_array($orders[0]['status_id'],[4,'4',5,'5'])){ ?>
                        <small>Delivered by: </small><br>
                        <b><?= $shipping_data['rider']['name']; ?></b> - <?= $shipping_data['rider']['contact_no']; ?><br>
                        Via <?= $shipping_data['rider']['vehicle_type']; ?>
                    <?php } else { ?>
                    
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>