<div class="container">
    <div class="row">
        <div class="col-12" style="height: 600px; overflow-y: scroll;">            
            <h4>Order History</h4>               
            <?php foreach($orders as $order){ ?>                          
                <div class="row p20 border-bottom">
                    <div class="col-4">
                        <b><?= format_shortfulldate($order['date_created']) ?></b><br>
                        <small><b>Order ID: </b><a href="<?= base_url('orders/'.$order['id']); ?>"><?= $order['order_id']; ?></a></small>
                    </div>
                    <div class="col-3">
                        <b>Amount: </b><br>
                        <small><?= php_money(floatval($order['total_amount']) + floatval($order['delivery_amount'])); ?></small>
                    </div>
                    <div class="col-3">
                        <b>Status:</b><br>
                        <small><?= $order['status_name'] ?></small>
                    </div>  
                    <div class="col-2">                    
                        <a href="<?= base_url('orders/'.$order['id']); ?>" class="badge badge-primary">View Order Details</a>
                    </div>              
                </div>  
            <?php } ?>         
        </div>
    </div>
    
</div>