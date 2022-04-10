<div class="container">
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-4 search_filters">
            <i class="fa fa-filter" aria-hidden="true"></i> <b>Search Filter</b>
            <br><br>
            <small><b>Category</b></small><br>            
            <?php foreach($categories as $category){ ?>
                <input type="checkbox"/ value="<?= $category['id']; ?>"> <small><?= $category['category_name']; ?></small><br>
            <?php } ?>

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
                </div>
                <div class="row">
                    <?php foreach($products as $product){ ?>
                        <?php if($category['id'] == $product['category_id']){ ?>                   
                            <div class="col-lg-3 col-md-4 col-sm-6 mt10">
                                <?php
                                    if($product['img'] == '' || $product['img'] == NULL){
                                        $image_path = base_url('assets/img/shop_logo.png');
                                    }
                                    else{                                
                                        $image_path = base_url('assets/uploads/products/').str_replace('==','',$product['img']);
                                    }
                                ?>
                                <div class="product-img" style="background-image: url(<?= $image_path; ?>); width: 100%;" data-product_id="<?= $product['id']; ?>">
                                    <div class="product-info">
                                    <strong class="product-name"><?= $product['name']; ?></strong><br>

                                    <?php foreach($product['variants'] as $variant){ ?>
                                        <span class="badge badge-info size-select"><?= $variant['name']; ?></span> <span>&#8369; <?= number_format($variant['price'],2); ?></span><br>
                                    <?php } ?>
                                    
                                    </div>   
                                </div>  
                                <div class="ml5">
                                
                                    <div class="row">
                                        <div class="col-8 clearfix">
                                            <?php if(isset($product['variants'][0])){ ?>
                                                <?php if(isset($product['promo'])){ ?>
                                                    <strong class="dashed">&#8369; <?= number_format($product['variants'][0]['price'],2); ?></strong><br>
                                                    <strong>&#8369; <?= number_format(50.000,2); ?></strong> <span class="badge badge-pill badge-danger">- 30%</span>
                                                <?php } else { ?>
                                                    <strong>&#8369; <?= number_format($product['variants'][0]['price'],2); ?></strong><br>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <strong class="dashed">&#8369; <?= number_format($product['price'],2); ?></strong>
                                            <?php } ?>
                                        </div>
                                        <div class="col-4 text-right">
                                            <small><b>1 sold</b></small>
                                        </div>
                                    </div>

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