<style>
    .bg-custom-primary{
        background-color:var(--primary-color);
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div id="carouselExampleIndicators" class="carousel slide full-page" data-ride="carousel">        
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                <img class="d-block w-100" src="<?= base_url('uploads/banners/b1.jpg') ?>" alt="">            
                </div>
                <div class="carousel-item">
                <img class="d-block w-100" src="<?= base_url('uploads/banners/b2.jpg') ?>" alt="Second slide">
                    <div class="carousel-caption d-none d-md-block">
                        <a href="<?= base_url('shop'); ?>"><div class="badge badge-pill badge-primary p20">SHOP NOW</div></a>                
                    </div>
                </div>
                <div class="carousel-item">
                <img class="d-block w-100" src="<?= base_url('uploads/banners/b3.jpg') ?>" alt="Third slide">                 
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            </div>    
        </div>    
    </div>
    <div class="container-fluid p20 mt50">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 mt10">
                <center>
                <img src="<?= base_url('assets/img/payment.png') ?>" alt="" height="70px"><br>                    
                <small><b>ONLINE PAYMENT</b></small><br>
                <small>FAST & EASY TRANSACTION VIA GCASH</small><br>            
                </center>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 mt10">
                <center>
                <img src="<?= base_url('assets/img/cod.png') ?>" alt="" height="70px"><br>                    
                <small><b>CASH ON DELIVERY</b></small><br>
                <small>ORDER NOW PAY LATER w/ COD</small><br>            
                </center>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 mt10">
                <center>
                <img src="<?= base_url('assets/img/customer-service.png') ?>" alt="" height="70px"><br>                    
                <small><b>CUSTOMER SERVICE</b></small><br>
                <small>GET DEDICATED SUPPORT ANYTIME YOU WANT</small><br>            
                </center>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 mt10">
                <center>
                <img src="<?= base_url('assets/img/authentic.png') ?>" alt="" height="70px"><br>                    
                <small><b>100% AUTHENTIC</b></small><br>
                <small>100% SECURE PAYMENT AND LEGITIMATE SELLER</small><br>            
                </center>
            </div>
        </div>    
        <div class="row bg-custom-primary mt-3 flashsales " style="display:none;">
            <div class="col-12 text-white h3 d-flex justify-content-center ">
                Flash Sale
            </div>
            <?php 
            $variants2 = array();
            $discount_ids = array();
            foreach($discounts as $discount_){
                ?>
                <div class="col-12 text-white d-flex justify-content-center  timer-<?=$discount_['discount_info']['id']?>" style="display:none;" >
                    Promo Until<br>
                </div>
                <div class="col-12 h2 text-white d-flex justify-content-center timer timer-<?=$discount_['discount_info']['id']?>"  style="display:none;" data-id="timer-<?=$discount_['discount_info']['id']?>" data-startdate='<?=$discount_['discount_info']['start_date']?>' data-enddate='<?=$discount_['discount_info']['end_date']?>' id="promodatetime" data-datetime="">
                    
                </div>
                <div class="col-12">
                    <div class="row d-flex justify-content-center  timer-<?=$discount_['discount_info']['id']?>">
                        <?php $count =0; 
                            foreach($discount_['products'] as $product){ 
                                array_push($discount_ids,$product['id']);
                            }
                            foreach($categories as $category){ ?>    
                                <?php foreach($discount_['products'] as $product){ ?>
                                    <!-- if variant is in discount -->
                                    <?php if(0 == $product['category_id'] && !in_array($product['id'],$variants2)&& !in_array($product['parent_product_id'],$variants2) && $count < 5){ 
                                        array_push($variants2,$product['id']);
                                        array_push($variants2,$product['parent_product_id']);
                                        $product2 = $this->model_products->get_product_info($product['parent_product_id']);
                                        $variants = $this->model_products->get_variants($product['parent_product_id']);;
                                        $count ++;?>                   
                                        <div class="col-lg-2  col-md-4 col-sm-6 mt10  bg-white m-1 p-1">
                                            <?php
                                                if($product['img'] == '' || $product['img'] == NULL){
                                                    $image_path = base_url('assets/img/shop_logo.png');
                                                }
                                                else{                                
                                                    $image_path = base_url('assets/uploads/products/').str_replace('==','',$product2['img']);
                                                }
                                            ?>
                                            <div class="product-img " style="background-image: url(<?= $image_path; ?>); width: 100%;" data-product_id="<?=en_dec('en',$product['parent_product_id']); ?>">
                                                <div class="product-info <?= sizeof($variants) == 1 ? 'single-variant' : '';?>">
                                                <strong class="product-name"><?= $product2['name']; ?></strong><br>
                                                <?php foreach($variants as $variant){ 
                                                    $newprice = $variant['price'];
                                                    $badge = $newprice;
                                                    if(in_array(en_dec('en',$variant['id']),$discount_ids)){
                                                        $discount = $product['discount'];
                                                        if($discount['discount_type'] == 1){
                                                            if($discount['disc_amount_type'] == 2){
                                                                $newprice = $product['price'] - ($product['price'] * ($discount['disc_amount']/100));
                                                                $discount_price = $discount['disc_amount'];
                                                                if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                    $newprice = $discount['max_discount_price'];
                                                                    $discount_price = $discount['max_discount_price'];
                                                                }
                                                                $badge =  number_format($newprice,2).' <s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span>';
                                                            }else{
                                                                $newprice = $product['price'] -$discount['disc_amount'];
                                                                $badge =  number_format($newprice,2).'<s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>';
                                                                if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                    $newprice = $discount['max_discount_price'];
                                                                    $badge = number_format($newprice,2).'<s><small>'.($variant['price']).'</small></s><span class=" mr-1 badge badge-danger">- &#8369; '.($variant['price']-$discount['max_discount_price']).' off</span>';
                                                                    // $newprice = $discount['max_discount_price'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                                    <span class="badge badge-info size-select"><?= $variant['name']; ?></span> <span>&#8369; <?=$badge ?></span><br>
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
                                                            <strong>&#8369; <?= number_format($product['variants'][0]['price'],2); ?></strong>
                                                        <?php } else {
                                                            $discount = $product['discount'];
                                                            $newprice = 0;
                                                            $badge = '';
                                                            if($discount['discount_type'] == 1){
                                                                if($discount['disc_amount_type'] == 2){
                                                                    $newprice = $product['price'] - ($product['price'] * ($discount['disc_amount']/100));
                                                                    $badge = 'SALE';
                                                                    if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                        $newprice = $discount['max_discount_price'];
                                                                    }
                                                                }else{
                                                                    $newprice = $product['price'] -$discount['disc_amount'];
                                                                    $badge = 'SALE';
                                                                    if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                        $newprice = $discount['max_discount_price'];
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
                                                            <strong>&#8369; <?= number_format($product['price'],2); ?></strong>&nbsp;<span class=" mr-1 badge badge-danger"><?=$badge?></span> <br>
                                                            <!-- <span><small><s>&#8369; <?= number_format($product['price'],2); ?></s></small></span> -->
                                                            <!-- <span class="badge badge-success">New</span> -->
                                                        <?php } ?>
                                                    </div>
                                                    <!-- <div class="col-4 text-right">
                                                        <small><b>1 sold</b></small>
                                                    </div>
                                                    <?php if($inv==0){ ?>
                                                    <div class="col-12 text-right text-danger">
                                                        <small><b>SOLD OUT</b></small>
                                                    </div>
                                                    <?php } ?> -->
                                                </div>

                                            </div>                
                                        </div>
                                    <?php } ?>
                                    <?php if($category['id'] == $product['category_id'] && $count < 5){ $count ++;?>                   
                                        <div class="col-lg-2  col-md-4 col-sm-6 mt10  bg-white m-1 p-1">
                                            <?php
                                                if($product['img'] == '' || $product['img'] == NULL){
                                                    $image_path = base_url('assets/img/shop_logo.png');
                                                }
                                                else{                                
                                                    $image_path = base_url('assets/uploads/products/').str_replace('==','',$product['img']);
                                                }
                                            ?>
                                            <div class="product-img " style="background-image: url(<?= $image_path; ?>); width: 100%;" data-product_id="<?= $product['id']; ?>">
                                                <div class="product-info <?= sizeof($product['variants']) == 1 ? 'single-variant' : '';?>">
                                                <strong class="product-name"><?= $product['name']; ?></strong><br>
                                                <?php foreach($product['variants'] as $variant){ ?>
                                                    <span class="badge badge-info size-select"><?= $variant['name']; ?></span> <span>&#8369; <?= number_format($variant['price'],2); ?></span><br>
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
                                                            <strong>&#8369; <?= number_format($product['variants'][0]['price'],2); ?></strong>
                                                        <?php } else {
                                                            $discount = $product['discount'];
                                                            $newprice = 0;
                                                            $badge = '';
                                                            if($discount['discount_type'] == 1){
                                                                if($discount['disc_amount_type'] == 2){
                                                                    $newprice = $product['price'] - ($product['price'] * ($discount['disc_amount']/100));
                                                                    $badge = '- '.$discount['disc_amount'].'% off';
                                                                    if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                        $newprice = $discount['max_discount_price'];
                                                                    }
                                                                }else{
                                                                    $newprice = $product['price'] -$discount['disc_amount'];
                                                                    $badge = '- &#8369; '.$discount['disc_amount'].' off';
                                                                    if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                                                        $newprice = $discount['max_discount_price'];
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
                                                            <strong>&#8369; <?= number_format($newprice,2); ?></strong>&nbsp;<span class=" mr-1 badge badge-danger"><?=$badge?></span> <br>
                                                            <span><small><s>&#8369; <?= number_format($product['price'],2); ?></s></small></span>
                                                            <!-- <span class="badge badge-success">New</span> -->
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <small><b>1 sold</b></small>
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
                        <?php } ?>             
                        <?php if(count($products) > 5){ ?>
                        <div class="col-12  d-flex justify-content-center mt-2 mb-2">
                            <button class="btn btn-primary">Show More</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php $target = 0;
         if(count($top_products) > 0){?>
        <div class="row bg-custom-primary mt-3 ">
            <div class="col-12 text-white h3 d-flex justify-content-start ">
                Top Products Sold
            </div>
        </div>
        <div class="row">
            <?php foreach($top_products as $product){
                if($target<10){
                    $target++;
                    $discount_badge = '';?>
                    <div class="col-lg-2 col-md-4 col-sm-6 mt10">
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
            <?php }} ?>
        </div>
        <?php } ?>
    </div>
    <div class="container-fluid">
    </div>

    <script src="<?= base_url('assets/js/libs/user/home/home.js'); ?>"></script>