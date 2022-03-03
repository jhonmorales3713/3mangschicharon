<div class="container">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">           
            <?php
                if($product['img'] == '' || $product['img'] == NULL){
                    $image_path = base_url('assets/img/favicon.png');
                }
                else{                                
                    $image_path = base_url('uploads/products/').$product['img'];
                }
            ?>
            <div class="product-img2" style="background-image: url(<?= $image_path; ?>); background-position: center; background-size: contain; background-repeat: no-repeat;" data-product_id="<?= $product['id'] ?>"></div>
            <br>
            <small><b>Category: </b><?= $product['category_name'] ?></small><br>
            <small><b>Product Rating: </b></small><br>
            <small><b>14</b> Sold</small><br>
            <br>            
        </div>  
        <div class="col-lg-8 col-md-4 col-sm-12">            
            <div class="col-12">
                <strong><?= $product['name']; ?></strong><br><br>
                <small><b>Product Description:</b></small><br>
                <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</small>
                
                <hr>

                <?php if($product['price'] > 0){ ?>
                    &#8369; <?= php_money($product['price']); ?>
                <?php } else { ?>
                    <small>Select Size:</small><br>
                    <?php if($product['price_small'] > 0){ ?>                        
                        <span class="badge badge-info size-select" data-size="S">Small</span> &#8369; <?= number_format($product['price_small'],2); ?><br>
                    <?php }?>
                    <?php if($product['price_large'] > 0){ ?>
                        <span class="badge badge-primary size-select" data-size="L">Large</span> &#8369; <?= number_format($product['price_large'],2); ?>
                    <?php }?>
                <?php }?>  

                <br><br>
                <small>Quantity</small><br>
                <input class="quantity-selector" id="qty" type="number" value="1" min="1" max="100">

                <hr>

            </div>
            <div class="row col-12">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <button class="btn btn-primary form-control mt5" data-product_id="<?= $product['id'] ?>">ORDER NOW</button>                   
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <button class="btn btn-primary add-to-cart form-control mt5" data-product_id="<?= $product['id'] ?>" data-category_id="<?= $product['category_id'] ?>">ADD TO CART</button>
                </div>                    
            </div>    
            <hr>
            <div class="row col-12">
                <div class="col-12">
                    <small><a href="<?= base_url('shop'); ?>" class=""><i class="fa fa-arrow-left" aria-hidden="true"></i> BACK TO SHOPPING</a></small>
                </div>
                
            </div>        
        </div>                  
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/products/view_product.js') ?>"></script>