<div class="container">
    <div class="row">
        <div class="col-12" style="height: 600px; overflow-y: scroll;">            
            <h4>Order History</h4>               
            <?php foreach($orders as $order1){  
                
                $order_amount = 0;
                $discount_total = 0;
                $subtotal = 0;
                foreach(json_decode($order1['order_data']) as $key => $order){ 
                    // $order_info = json_decode($row["order_data"]);
                    //print_r($value);
                    $qty = $order->quantity;
                    $amount = $order->amount;
                    $product = (en_dec('dec',$key));
                    $subtotal += $amount*$qty;
                    $discount_info = $order->discount_info;
                    $discount_price = 0;
                    // print_r($discount_info);
                    $newprice = $amount;
                    $badge = '';
                    if($discount_info != '' && $discount_info != null){
                        if(in_array($key,json_decode($discount_info->product_id))){
                            $discount_id = $discount_info->id;
                            if($discount_info->discount_type == 1){
                                if($discount_info->disc_amount_type == 2){
                                    $oldvalue = $newprice;
                                    $newprice = $amount - ($amount * ($discount_info->disc_amount/100));
                                    $discount_price = $discount_info->disc_amount;
                                    if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
                                        $discount_price = $discount_info->max_discount_price;
                                        $newprice = $discount_info->max_discount_price;
                                    }
                                    $discount_total += $qty*($oldvalue - $newprice);
                                    $badge =  '<span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
                                }else{
                                    $oldvalue = $newprice;
                                    $newprice = $amount - $discount_info->disc_amount;
                                    $discount_total += $qty*($oldvalue - $newprice);
                                    $discount_price = $discount_info->disc_amount;
                                    $badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->disc_amount.' off</span> <s><small>'.$amount.'</small></s>';
                                    if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
                                        $badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->max_discount_price.' off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
                                        $newprice = $discount_info->max_discount_price;
                                        // $newprice = $discount['max_discount_price'];
                                        $discount_price = $discount_info->max_discount_price;
                                    }
                                }
                                $amount = $newprice;
                            }
                        }
                    }
                    $order_amount += $newprice * $qty;
                }
                ?>                          
                <div class="row p20 border-bottom">
                    <div class="col-4">
                        <b><?= format_shortfulldate($order1['date_created']) ?></b><br>
                        <small><b>Order ID: </b><a href="<?= base_url('orders/'.$order1['id']); ?>"><?= $order1['order_id']; ?></a></small>
                    </div>
                    <div class="col-3">
                        <b>Amount: </b><br>
                        <small><?= php_money(floatval($subtotal) + floatval($order1['delivery_amount'] -$discount_total)); ?></small>
                    </div>
                    <div class="col-3">
                        <b>Status:</b><br>
                        <small><?= $order1['status_name'] ?></small>
                    </div>  
                    <div class="col-2">                    
                        <a href="<?= base_url('orders/'.$order1['id']); ?>" class="badge badge-primary">View Order Details</a>
                    </div>              
                </div>  
            <?php } ?>         
        </div>
    </div>
    
</div>