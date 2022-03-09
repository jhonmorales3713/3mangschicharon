
<b>Select Payment Method</b><br>

<?php foreach($payment_methods as $method){ ?>
    <div class="payment-method-select" data-payment_method="<?= $method['id'] ?>">
    <b><?= $method['method']; ?></b><br>
    <small><?= $method['method_desc'] ?></small>
    </div>
<?php }?>

<hr>

<?php foreach($shipping_types as $type){ ?>
    <div class="payment-method-select" data-shipping_type="<?= $type['id'] ?>">
    <b><?= $type['type']; ?></b><br>
    <small>Estimated Time: <?= $type['est_delivery_start'].'-'.$type['est_delivery_end']; ?> hrs.</small>
    </div>
<?php }?>

<hr>

<b>Order Summary</b>   
<?php $total_amount = 0; ?>
<?php foreach($_SESSION['cart'] as $key => $value){ ?>
    <div class="row">
        <div class="col-7">
            <small><strong><?= $_SESSION['cart'][$key]['name'] ?> (<?= $_SESSION['cart'][$key]['size']; ?>)</strong> <b>x</b> <?= $_SESSION['cart'][$key]['quantity'] ?></small><br>
        </div>
        <div class="col-5 text-right">
            <?php $amount = floatval($_SESSION['cart'][$key]['amount']) * intval($_SESSION['cart'][$key]['quantity']); ?>
            <?php $total_amount += $amount; ?>
            <span><?= number_format($amount,2); ?></span>
        </div>           
    </div>         
<?php } ?>

<?php if($sub_active_page == 'checkout'){ ?>
<?php $total_amount += $shipping_types[0]['rate']; ?>
    <div class="row">
        <div class="col-7">
            <small><strong>Shipping Fee</strong></small>
        </div>
        <div class="col-5 text-right">
            <span><?= number_format($shipping_types[0]['rate'],2); ?></span>
        </div>
    </div>
<?php } ?>

<hr>        
<div class="row">
    <div class="col-7">
        <b>Sub Total</b>
    </div>
    <div class="col-5 text-right">
        <strong id="sub_total">&#8369; <?= number_format($total_amount,2); ?></strong>
    </div>
</div>

<div class="col-12">
    <br><br>
    <hr>
    <input type="hidden" id="sub_total" value='0'>
    <button type="button" class="btn btn-primary form-control" id="btn_place_order">PLACE ORDER</button>
</div>
<div class="col-12">
    <hr>
    <a href="<?= base_url('shop'); ?>">Back to shopping</a>
</div>

    