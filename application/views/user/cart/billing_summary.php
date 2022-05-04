
<b>Select Payment Method</b><br>

<div id="payment_method_error">
    <?php foreach($payment_methods as $method){ ?>
        <div class="payment-method-select" data-payment_method="<?= $method['id']; ?>" data-keyword="<?= $method['keyword']; ?>">
        <center>
        
        <b><?= $method['method']; ?></b><br>        
        </center>
        </div>
    <?php }?>
</div>

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
<div class="p20" id="summary_div">
<?php foreach($_SESSION['cart'] as $key => $value){ ?>
    <?php
        $newprice = floatval($_SESSION['cart'][$key]['amount']);
        $badge = number_format($newprice,2);
        foreach($discounts as $discount){
            if(in_array($key,json_decode($discount['product_id']))){
                
                $discount_id = $discount['id'];
                // $discount = '<span class="badge badge-danger"></span>';
                // $discount = $product['discount'];
                if($discount['discount_type'] == 1){
                    if($discount['disc_amount_type'] == 2){
                        $newprice = $_SESSION['cart'][$key]['amount'] - ($_SESSION['cart'][$key]['amount'] * ($discount['disc_amount']/100));
                        $discount_price = $discount['disc_amount'];
                        if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                            $discount_price = $discount['max_discount_price'];
                            $newprice = $discount_price;
                        }
                        $badge =  number_format($newprice,2).' <s><small>'.$_SESSION['cart'][$key]['amount'].'</small></s><span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span>';
                    }else{
                        $newprice = $value['price'] -$discount['disc_amount'];
                        $badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>'.number_format($newprice,2);
                        if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                            $badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>'.number_format($newprice,2);
                            $newprice = $discount['max_discount_price'];
                            // $newprice = $discount['max_discount_price'];
                        }
                    }
                }
            }
        } ?>   
    <div class="row">
        <?php if($value['is_included'] == 1){ ?>
            <div class="col-7">
                <small><strong><?= $_SESSION['cart'][$key]['name'] ?> (<?= $_SESSION['cart'][$key]['size']; ?>)</strong> <b>x</b> <?= $_SESSION['cart'][$key]['quantity'] ?></small><br>
            </div>
            <div class="col-5 text-right">
                <?php $amount = floatval($newprice) * intval($_SESSION['cart'][$key]['quantity']); ?>
                <?php $total_amount += $amount; ?>
                <span><?= $badge; ?></span>
              
            </div>    
        <?php } ?>       
    </div>         
<?php } ?>
</div>

<div class="p20">
<?php if($sub_active_page == 'checkout'){ ?>    
<?php $total_amount += floatval($shipping_types[0]['rate']); ?>
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
</div>

<div class="col-12">
    <br><br>
    <hr>
    <input type="hidden" id="total_amount" value="<?= $total_amount; ?>">
    <button type="button" class="btn btn-primary form-control" id="btn_place_order">PLACE ORDER</button>
</div>
<div class="col-12">
    <hr>
    <a href="<?= base_url('shop'); ?>">Back to shopping</a>
</div>

    