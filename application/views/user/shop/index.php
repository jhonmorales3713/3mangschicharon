<div class="container">
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-4 search_filters">
            <i class="fa fa-filter" aria-hidden="true"></i> <b>Search Filters</b>
            <br><br>
            <small><b>Category</b></small><br>            
            <?php foreach($categories as $category){ ?>
                <input type="checkbox"/ data-cat_id="<?= $category['id']; ?>"> <small><?= $category['category_name']; ?></small><br>
            <?php } ?>
            <br><br>
            <small><b>Price Range</b></small><br> 
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <small for="">From</small>
                        <input type="text" name="" id="" class="form-control form-control-sm">
                    </div>                    
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <small for="">To</small>
                        <input type="text" name="" id="" class="form-control form-control-sm">
                    </div>                    
                </div>
            </div>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-8">
            <?php foreach($categories as $category){ ?>        
                <div class="row">
                <div class="col-12">                
                    <?php if($category['id'] > 1){ ?>
                        <hr>
                    <?php } ?>
                    <strong><?= $category['category_name']; ?></strong>    
                </div>
                <?php foreach($products as $product){ ?>
                    <?php if($category['id'] == $product['category_id']){ ?>                   
                    <div class="col-lg-3 col-md-4 col-sm-6 mt10">   
                        <div class="product-img" style="background-image: url(<?= base_url('assets/img/shop_logo.png'); ?>);" data-product_id="<?= $product['id'] ?>"></div>                         
                        
                        <center>
                            <strong><?= $product['name']; ?></strong><br>
                            <?php if($product['price'] > 0){ ?>
                                <?= php_money($product['price']); ?>
                            <?php } else { ?>
                                <?php if($product['price_small'] > 0){ ?>
                                    <span class="badge badge-info size-select">Small</span> <?= php_money($product['price_small']); ?><br>
                                <?php }?>
                                <?php if($product['price_large'] > 0){ ?>
                                    <span class="badge badge-primary size-select">Large</span> <?= php_money($product['price_large']); ?>
                                <?php }?>
                            <?php }?>                    
                        </center>
                        <button class="btn btn-primary form-control mt5">ORDER NOW</button><br>
                        <button class="btn btn-primary add-to-cart form-control mt5">ADD TO CART</button>                
                    </div>
                    <?php } ?>
                <?php } ?>
                </div>
        <?php } ?>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/shop/shop.js'); ?>"></script>