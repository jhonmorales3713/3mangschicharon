<div class="container">
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-4 search_filters">
            <i class="fa fa-filter" aria-hidden="true"></i> <b>Search Filter</b>
            <br><br>
            <small><b>Category</b></small><br>            
            <?php foreach($categories as $category){ ?>
                <input type="checkbox"/ data-cat_id="<?= $category['id']; ?>"> <small><?= $category['category_name']; ?></small><br>
            <?php } ?>
            
            <br>
            <small><b>Size</b></small><br>
            <input type="checkbox"> <small>Small</small><br>
            <input type="checkbox"> <small>Large</small><br>           

            <br><br>
            <button class="btn btn-sm btn-primary form-control form-control-sm add-to-cart">Apply</button>
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
                        <?php
                            if($product['img'] == '' || $product['img'] == NULL){
                                $image_path = base_url('assets/img/shop_logo.png');
                            }
                            else{                                
                                $image_path = base_url('uploads/products/').$product['img'];
                            }
                        ?>
                        <div class="product-img" style="background-image: url(<?= $image_path; ?>);" data-product_id="<?= $product['id'] ?>">                       
                            <div class="product-info">
                            <strong class="product-name"><?= $product['name']; ?></strong><br>
                            <?php if($product['price'] > 0){ ?>
                                <span class="badge badge-info size-select">Regular</span> <span>&#8369; <?= number_format($product['price'],2); ?></span><br>
                                <span class="badge badge-info size-select">Large</span> <span>Not Available</span><br>
                            <?php } else { ?>
                                <?php if($product['price_small'] > 0){ ?>
                                    <span class="badge badge-info size-select">Small</span> <span>&#8369; <?= number_format($product['price_small'],2); ?></span><br>
                                <?php }?>
                                <?php if($product['price_large'] > 0){ ?>
                                    <span class="badge badge-primary size-select">Large</span> <span>&#8369; <?= number_format($product['price_large'],2); ?></span>
                                <?php }?>
                            <?php }?>
                            </div>   
                        </div>  
                        <div class="ml5">
                        <?php if($product['price'] > 0){ ?>
                            <div class="row">
                                <div class="col-6">
                                    <strong>&#8369; <?= number_format($product['price'],2); ?></strong>
                                </div>
                                <div class="col-6 text-right">
                                    <small><b>1 sold</b></small>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-6">
                                    <strong>&#8369; <?= number_format($product['price_small'],2); ?></strong>
                                </div>
                                <div class="col-6 text-right">
                                    <small><b>1 sold</b></small>
                                </div>
                            </div>                            
                        <?php }?>        
                        </div>                
                    </div>
                    <?php } ?>
                <?php } ?>
                </div>
        <?php } ?>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/shop/shop.js'); ?>"></script>