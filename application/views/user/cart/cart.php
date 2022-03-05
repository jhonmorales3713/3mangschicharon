<div class="container">    
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
        <div class="row" id="cart_container">
            <div class="col-lg-7 col-md-7 col-sm-12 cart-item-list">   
                <div class="col-12 clearfix">
                    <h5 class="float-left">Review Items</h5>            
                    <span class="a float-right" data-toggle="modal" data-target="#remove_item_modal">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        <small>DELETE ALL</small>
                    </span>   
                </div>            
                <div class="container p20">                 
                    <div class="col-12" id="cart_div">                  
                        <?php foreach($_SESSION['cart'] as $key => $value){ ?>
                            <div class="row p5" id="<?= $key; ?>"> 
                                <div class="col-6">                                
                                    <strong><?= $_SESSION['cart'][$key]['name'] ?> (<?= $_SESSION['cart'][$key]['size']; ?>)</strong> <br>
                                </div>
                                <div class="col-3">                            
                                    <input type="number" value="<?= $_SESSION['cart'][$key]['quantity']; ?>" min="1" max="100" class="qty" data-target="<?= $key; ?>"/>
                                </div>    
                                <div class="col-3">
                                    <span class="remove-item a" data-target="<?= $key; ?>"><i class="fa fa-times" aria-hidden="true"></i></span>
                                </div>            
                            </div>
                        <?php } ?>                    
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12">            
                <h5>Order Summary</h5>            
                <div class="row p20">
                    <div class="col-12">
                        <div id="summary_div">
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
                        </div>
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
                        <button class="btn btn-primary form-control" id="btn_checkout">PROCEED TO CHECKOUT</button>
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

<div id="remove_item_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">               
            <div class="modal-body">            
                <div class="row">
                    <div class="col-12">
                        <h5>Remove from cart</h5><hr>
                        <span id="remove_label">Are you sure you want to remove all items?</span>    
                        <input type="hidden" id="item_key" value="all">                              
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="button" class="btn btn-sm btn-danger" id="remove_from_cart">REMOVE</button>      
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/cart/cart.js') ?>"></script>

