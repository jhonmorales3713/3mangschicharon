<div class="container" id="cart_container">        
    <?php if(!isset($_SESSION['cart'])){ ?>
        <div class="row">
            <div class="col-12">
                <center>
                    No Items in cart
                    <br><br>
                    <a href="<?= base_url('shop'); ?>">Back to shopping</a>
                </center>                
            </div>
        </div>
    <?php } else { ?>
    <div class="row">
        <div class="col-12">
            <span id="delete_cart_all" class="a">
                <i class="fa fa-trash" aria-hidden="true"></i>
                <small>DELETE ALL</small>
            </span>            
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 col-md-7 col-sm-12 cart-item-list">
            <center>
                <h5>Review Items</h5>
            </center>
            <div class="container p20">               
                <div class="row">                    
                    <?php foreach($_SESSION['cart'] as $key => $value){ ?>
                        <div class="col-6">
                            <img src="<?= base_url('assets/img/shop_logo.png'); ?>" alt="" width="50px">
                            <strong><?= $_SESSION['cart'][$key]['name'] ?> (<?= $_SESSION['cart'][$key]['size']; ?>)</strong> <br>
                        </div>
                        <div class="col-3">                            
                            <input type="number" value="<?= $_SESSION['cart'][$key]['quantity']; ?>" min="1" max="100" class="qty" data-target="<?= $key; ?>"/>
                        </div>    
                        <div class="col-3">
                            <span class="remove-item text-danger"><i class="fa fa-trash" aria-hidden="true"></i></span>
                        </div>            
                    <?php } ?>                    
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-12">
            <center>
            <h5>Order Summary</h5>
            </center>
            <div class="row p20">
                <div class="col-12">
                    <div class="row">
                        <?php $total_amount = 0; ?>
                        <?php foreach($_SESSION['cart'] as $key => $value){ ?>
                            <div class="col-7">
                                <strong><?= $_SESSION['cart'][$key]['name'] ?> (<?= $_SESSION['cart'][$key]['size']; ?>)</strong> <small><b>x</b> <?= $_SESSION['cart'][$key]['quantity'] ?></small><br>
                            </div>
                            <div class="col-5">
                                <?php $amount = floatval($_SESSION['cart'][$key]['amount']) * intval($_SESSION['cart'][$key]['quantity']); ?>
                                <?php $total_amount += $amount; ?>
                                <?= php_money($amount); ?>
                            </div>                
                        <?php } ?>                        
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-7">
                            <b>Sub Total</b>
                        </div>
                        <div class="col-5">
                            <strong><?= php_money($total_amount); ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <br><br>
                    <hr>
                    <button class="btn btn-primary form-control">PROCEED TO CHECKOUT</button>
                </div>
                <div class="col-12">
                    <hr>
                    <a href="<?= base_url('shop'); ?>">Back to shopping</a>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script src="<?= base_url('assets/js/libs/user/cart/cart.js') ?>"></script>

