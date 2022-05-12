<div class="container">
    <div class="row">
        <div class="col-12">
            <h5>Order Details</h5>
            <div class="row">

                <?php 
                    $order_data = json_decode($orders[0]['order_data'],true);
                    $payment_data = json_decode($orders[0]['payment_data'],true);
                    $shipping_data = json_decode($orders[0]['shipping_data'],true);
                    $order_amount = 0;
                    $discount_total = 0;
                    $subtotal = 0;
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
                        <?php foreach($order_data as $key => $order){ 
                            // print_r($order);
                            // $order_info = json_decode($row["order_data"]);
                            //print_r($value);
                            $qty = $order['quantity'];
                            $amount = $order['amount'];
                            $product = (en_dec('dec',$key));
                            $subtotal += $amount*$qty;
                            $discount_info = $order['discount_info'];
                            $discount_price = 0;
                            // print_r($discount_info);
                            $newprice = $amount;
                            $badge = '';
                            if($discount_info != '' && $discount_info != null){
                                if(in_array($key,json_decode($discount_info['product_id']))){
                                    $discount_id = $discount_info['id'];
                                    if($discount_info['discount_type'] == 1){
                                        if($discount_info['disc_amount_type'] == 2){
                                            $oldvalue = $newprice;
                                            $newprice = $amount - ($amount * ($discount_info['disc_amount']/100));
                                            $discount_price = $discount_info['disc_amount'];
                                            if($discount_info['max_discount_isset'] && $newprice < $discount_info['max_discount_price']){
                                                $discount_price = $discount_info['max_discount_price'];
                                                $newprice = $discount_info['max_discount_price'];
                                            }
                                            $discount_total += $qty*($oldvalue - $newprice);
                                            $badge =  '<span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
                                        }else{
                                            $oldvalue = $newprice;
                                            $newprice = $amount - $discount_info['disc_amount'];
                                            $discount_total += $qty*($oldvalue - $newprice);
                                            $discount_price = $discount_info['disc_amount'];
                                            $badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info['disc_amount'].' off</span> <s><small>'.$amount.'</small></s>';
                                            if($discount_info['max_discount_isset'] && $newprice < $discount_info['max_discount_price']){
                                                $badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info['max_discount_price'].' off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
                                                $newprice = $discount_info['max_discount_price'];
                                                // $newprice = $discount['max_discount_price'];
                                                $discount_price = $discount_info['max_discount_price'];
                                            }
                                        }
                                        $amount = $newprice;
                                    }
                                }
                            }
                            $order_amount += $newprice * $qty;
                            ?>     
                            <div class="col-2 mt5">
                                <img src="<?= base_url('assets/uploads/products/'.$order['img']); ?>" alt="" width="100%">
                            </div>                       
                            <div class="col-5 mt5">
                                <small><b><?= $order['name']; ?></b> (<?= $order['size']; ?>)</small>
                            </div>       
                            <div class="col-2 mt5">
                                <small>Qty: <?= $order['quantity']; ?></small>
                            </div>                     
                            <div class="col-3 mt5 clearfix">
                                <small class="float-right"><b><?= number_format($newprice,2); ?><?=$badge?></b></small>
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
                            <small><b class="float-right"><?= number_format($order_amount,2); ?></b></small>
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
                            <p class="float-right"><?= number_format($subtotal,2); ?></p>
                        </div>
                        <div class="col-8">
                            <p>Discount Total</p>
                        </div>
                        <div class="col-4 clearfix">
                            <p class="float-right"><?='-'. number_format($discount_total,2); ?></p>
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
                            <b class="float-right">&#8369; <?= number_format($order_amount+$delivery_amount,2); ?></b>
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