<div class="container">
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-4 search_filters">
            <i class="fa fa-filter" aria-hidden="true"></i> <b>Search Filter</b>
            <br><br>
            <small><b>Category</b></small><br>            
            <?php foreach($categories as $category){ 
                $checked = '';
                if($search_categories == ''){
                    $search_categories = Array();
                }
                if(count($search_categories) == 0){
                    $checked ='checked';
                }
                if($search_categories != ''){
                    if(in_array($category['id'],$search_categories)){
                        $checked ='checked';
                    }
                }
            ?>
                <input type="checkbox" class="category_checkbox" <?=$checked?> value="<?= $category['id']; ?>"> <small><?= $category['category_name']; ?></small><br>
            <?php } ?>

            <br><br>
            <button class="btn btn-sm btn-primary form-control form-control-sm apply-filter">Apply</button>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-8">
            <?php        
                $discount_ids = array();
                if(isset($discounts)){
                    foreach($discounts as $discount){ 
                        foreach($discount['products'] as $product){ 
                            array_push($discount_ids,$product['id']);
                        }
                    }
                }
                foreach($categories as $category){ 
                    if($search_categories != ''){
                        if(in_array($category['id'],$search_categories) || count($search_categories) == 0){
                    ?>        
                <div class="row">
                    <div class="col-12">                
                        <?php if($category['id'] > 1){?>
                            <hr>
                        <?php }
                        $included=0;
                        foreach($products as $product){
                            if($category['id'] == $product['category_id']){ 
                                $included++;
                            }
                        }
                        if($included>0){
                        ?>
                        <strong><?= $category['category_name']; ?></strong> 
                        <?php } ?>   
                    </div>
                </div>
                <div class="row">
                    <?php foreach($products as $product){
                        $discount_badge = '';?>
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
                                    <div class="product-info <?= sizeof($product['variants']) == 1 ? 'single-variant' : '';?>">
                                    <strong class="product-name"><?= $product['name']; ?></strong><br>
                                    <?php foreach($product['variants'] as $variant){ 
                                        $newprice = $variant['price'];
                                        $badge = $newprice;
                                        if(in_array(en_dec('en',$variant['id']),$discount_ids)){
                                            // $discount = $product['discount'];
                                            foreach($discounts as $discount_){
                                                $discount = ($discount_['discount_info']);
                                                if($discount['discount_type'] == 1){
                                                    if($discount['disc_amount_type'] == 2){
                                                        $newprice = $variant['price'] - ($variant['price'] * ($discount['disc_amount']/100));
                                                        $discount_price = $discount['disc_amount'];
                                                        if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                            $discount_price = $discount['max_discount_price'];
                                                        }
                                                        $discount_badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_price.' % off</span>';
                                                        $badge =  number_format($newprice,2).' <s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span>';
                                                    }else{
                                                        $newprice = $product['price'] -$discount['disc_amount'];
                                                        $badge =  number_format($newprice,2).'<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>';
                                                        $discount_badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>';
                                                        if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                            $discount_badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>';
                                                            $badge = number_format($newprice,2).'<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>';
                                                            // $newprice = $discount['max_discount_price'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                    <span class="badge badge-info size-select"><?= $variant['name']; ?></span> <span>&#8369; <?= $badge; ?></span><br>
                                    <?php } ?>
                                    
                                    </div>   
                                </div>  
                                <div class="ml5">
                                
                                    <div class="row">
                                        <div class="col-8">
                                            <?php 
                                                $inv = 0;
                                                $price = 0;
                                                if(isset($product['variants'][0])){
                                                    foreach($product['variants'] as $variant){
                                                        $inventories = $this->model_products->get_inventorydetails($variant['id']);
                                                        foreach($inventories as $inventory){
                                                            $now = time();
                                                            $expiration = strtotime($inventory['date_expiration']);
                                                            if(date('Y-m-d',$expiration) > date('Y-m-d') && $variant['id'] ==  $inventory['product_id']){
                                                                $inv += $inventory['qty'];
                                                                //(round(($expiration - $now)/(60*60*24))+1);
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <strong>&#8369; <?= number_format($product['variants'][0]['price'],2).$discount_badge;?></strong>
                                            <?php } else { 
                                                $novariant_product = $product;
                                                $newprice = $novariant_product['price'];
                                                $badge = $newprice;
                                                if(in_array($novariant_product['id'],$discount_ids)){
                                                    // $discount = $product['discount'];
                                                    foreach($discounts as $discount_){
                                                        $discount = ($discount_['discount_info']);
                                                        if($discount['discount_type'] == 1){
                                                            if($discount['disc_amount_type'] == 2){
                                                                $newprice = $novariant_product['price'] - ($novariant_product['price'] * ($discount['disc_amount']/100));
                                                                $discount_price = $discount['disc_amount'];
                                                                if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                    $discount_price = $discount['max_discount_price'];
                                                                }
                                                                $discount_badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_price.' % off</span>';
                                                                $badge =  number_format($newprice,2).' <s><small>'.$novariant_product['price'].'</small></s><span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span>';
                                                            }else{
                                                                $newprice = $product['price'] -$discount['disc_amount'];
                                                                $badge =  number_format($newprice,2).'<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>';
                                                                $discount_badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>';
                                                                if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                    $discount_badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>';
                                                                    $badge = number_format($newprice,2).'<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>';
                                                                    // $newprice = $discount['max_discount_price'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                foreach($product['inventory'] as $inventory){
                                                    $now = time();
                                                    $expiration = strtotime($inventory['date_expiration']);
                                                    if(date('Y-m-d',$expiration) > date('Y-m-d') && $product['id'] ==  en_dec('en',$inventory['product_id'])){
                                                        $inv += $inventory['qty'];
                                                        //(round(($expiration - $now)/(60*60*24))+1);
                                                    }
                                                }
                                            ?>
                                                <strong>&#8369; <?= !isset($product['variants'][0]) ? $badge:number_format($product['price'],2).$discount_badge;?></strong>
                                                <!-- <span class="badge badge-success">New</span> -->
                                            <?php } ?>
                                        </div>
                                        <div class="col-4 text-right">
                                            <small><b><?=$product['sold_count'];?> sold</b></small>
                                        </div>
                                        <?php if($inv==0){ ?>
                                        <div class="col-12 text-right text-danger">
                                            <small><b>SOLD OUT</b></small>
                                        </div>
                                        <?php } ?>
                                    </div>

                                </div>                 
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php }
                        }
                    }  ?>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/shop/shop.js'); ?>"></script>