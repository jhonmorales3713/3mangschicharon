<div class="container">
    <div class="row">
        <?php foreach($products as $product){ ?>
            <div class="col-lg-3 col-md-4 col-sm-6">                
                <div class="product-img" style="background-image: url(<?= base_url('assets/img/shop_logo.png'); ?>);">                          
                </div>              
                <center>
                    <strong><?= $product['name']; ?></strong><br>
                    <?= php_money($product['price']); ?>
                </center>
                <button class="btn btn-primary form-control mt5">ORDER NOW</button><br>
                <button class="btn btn-primary form-control mt5">ADD TO CART</button>
                
                
            </div>
        <?php } ?>
    </div>
</div>