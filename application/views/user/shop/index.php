<div class="container">
    <div class="row">
        <div class="col-12 search-bar">
            
        </div>
    </div>
    <div class="row">
        <?php foreach($products as $product){ ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mt10">                
                <div class="product-img" style="background-image: url(<?= base_url('assets/img/shop_logo.png'); ?>);">                          
                </div>              
                <center>
                    <strong><?= $product['name']; ?></strong><br>
                    <?php if($product['price'] > 0){ ?>
                        <?= php_money($product['price']); ?>
                    <?php } else { ?>
                        <?php if($product['price_small'] > 0){ ?>
                            <span class="badge badge-info">Small</span> <?= php_money($product['price_small']); ?><br>
                        <?php }?>
                        <?php if($product['price_large'] > 0){ ?>
                            <span class="badge badge-primary">Large</span> <?= php_money($product['price_large']); ?>
                        <?php }?>
                    <?php }?>                    
                </center>
                <button class="btn btn-primary form-control mt5">ORDER NOW</button><br>
                <button class="btn btn-primary form-control mt5">ADD TO CART</button>                
            </div>
        <?php } ?>
    </div>
</div>