<style>
    .checked {
        color: orange;
    }
</style>
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
                    $discount_info = isset($order->discount_info) ? $order->discount_info : Array();
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
                        <a href="<?= base_url('orders/'.$order1['id']); ?>" class="badge badge-primary">View Order Details</a><br>
                        <?php if( $order1['status_name']  == 'Delivered' &&  $order1['customer_feedback'] =='' ){ ?>
                            <a class="badge badge-warning rate_btn" data-id="<?=$order1['id']?>">Rate Order</a>
                        <?php }else if( $order1['status_name']  == 'Delivered' &&  $order1['customer_feedback'] !='' ){
                            for($i=json_decode($order1['customer_feedback'])->rating;$i>0;$i--){
                            ?>
                            <span class="fa fa-star checked r1 r" data-rate=1></span>
                        <?php }
                            for($i=json_decode($order1['customer_feedback'])->rating;$i<5;$i++){?>
                                <span class="fa fa-star r1 r" data-rate=1></span>
                            <?php
                        }}?>
                    </div>              
                </div>  
            <?php } ?>         
        </div>
    </div>
    
</div>

<!-- Modal-->
<div class="modal fade" id="rate_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title " id="exampleModalLabel"><span class="mtext_record_status">Rate</span> Order</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center mb-3">
                        <div class="ratingControl">
                            <span class="fa fa-star r1 r" data-rate=1></span>
                            <span class="fa fa-star r2 r" data-rate=2></span>
                            <span class="fa fa-star r3 r" data-rate=3></span>
                            <span class="fa fa-star r4 r" data-rate=4></span>
                            <span class="fa fa-star r5 r" data-rate=5></span>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-start">
                        Message
                    </div>
                    <div class="col-12 d-flex justify-content-center ">
                        <textarea class="form-control" id="message" placeholder="Message (Optional)"></textarea>
                    </div>
                </div>
                <!-- <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="rate_modal_btn">Submit Rating</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    orderid_selected = '';
    rating = 0;
    $(".rate_btn").click(function(){
        $("#rate_modal").modal('show');
        orderid_selected = $(this).data('id');
        // alert(orderid_selected);
    });
    $(".ratebtn").click(function(){
        rating = $(this).data('rate');
        for(rating;rating<0;rating--){
            $("."+rating+"-rate").css('color','#FC0');
        }
    });
    $( ".ratingControl span" ).hover(
        function() {
            rate = $(this).data('rate');
            rating = rate;
            $('.r').removeClass('checked');
            for(i=rate;i>0;i--){
                $('.r'+i).addClass('checked');
            }
            // $( this ).append( $( "<span> ***</span>" ) );
        }, function() {
            // $( this ).find( "span" ).last().remove();
        }
    );
    $("#rate_modal_btn").click(function(){
		$.ajax({				
			url: base_url+'user/Orders/rate_order',
	       	type: 'POST',
			data: {
                id:orderid_selected,
                rating:rating,
                message:$("#message").val()
            },
			beforeSend:function() {
				$.LoadingOverlay("show"); 
			},
			success : function(data){
				json_data = JSON.parse(data);
				$.LoadingOverlay("hide"); 
                if(json_data.success){
                    $("#rate_modal").modal('hide');
                    sys_toast_success(json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    sys_toast_warning(json_data.message);
                }
            }
        });
    });
// alert("SD");
</script>