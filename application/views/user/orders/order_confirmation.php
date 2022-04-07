<div class="container">
    <div class="row">
        <h4 class="text-success">Order Sucessful!</h4>
        <div class="col-12">
            <div class="p20">                
                <span>Your order id is <a href="<?= base_url('orders/'.$id); ?>"><?= $order_id; ?></a></span><br>
                <small>Your order will be processed within <b>24hrs</b></small><br><br>
                <hr>
                <?php if(isset($_SESSION['has_logged_in'])){ ?>
                    <?php if($_SESSION['has_logged_in']){ ?>
                        <small>You may also check the status of your order in your <a href="<?= base_url('orders') ?>">Orders</a> in your account options. </small>
                    <?php }?>
                <?php }?>
            </div>
        </div>
    </div>
</div>