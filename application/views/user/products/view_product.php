<div class="container">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">           
            <?php
                if($product['img'] == '' || $product['img'] == NULL){
                    $image_path = base_url('assets/img/favicon.png');
                }
                else{                                
                    $image_path = base_url('assets/uploads/products/').str_replace('==','',$product['img']);
                }
            ?>
            <div class="product-img2" style="background-image: url(<?= $image_path; ?>); background-position: center; background-size: contain; background-repeat: no-repeat;" data-product_id="<?= $product['id'] ?>"></div>
            <br>
            <small><b>Category: </b><?= $product['category_name'] ?></small><br>
            <small><b>Product Rating: </b></small><br>
            <small><b><?=$product['sold_count']?></b> Sold</small><br>
            <br>            
        </div>  
        <div class="col-lg-8 col-md-8 col-sm-12">            
            <div class="col-12">
                <strong><?= $product['name']; ?></strong><br><br>
                <small><b>Product Description:</b></small><br>
                <small><?=$product['summary']?></small>
                
                <hr>
                <?php
                    $discount_id = '';
                    foreach($product['variants'] as $variant){ 
                    $newprice = $variant['price'];
                    $badge = $newprice;
                    foreach($discounts as $discount){
                        if(in_array(en_dec('en',$variant['id']),json_decode($discount['product_id']))){
                            $discount_id = $discount['id'];
                            // $discount = '<span class="badge badge-danger"></span>';
                            // $discount = $product['discount'];
                            if($discount['discount_type'] == 1){
                                if($discount['disc_amount_type'] == 2){
                                    $newprice = $variant['price'] - ($variant['price'] * ($discount['disc_amount']/100));
                                    $discount_price = $discount['disc_amount'];
                                    if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                        $discount_price = $discount['max_discount_price'];
                                    }
                                    $badge =  number_format($newprice,2).' <s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span>';
                                }else{
                                    $newprice = $variant['price'] -$discount['disc_amount'];
                                    $badge =  number_format($newprice,2).'<s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span> ';
                                    if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
                                        $badge = number_format($newprice,2).'<s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>';
                                        // $newprice = $discount['max_discount_price'];
                                    }
                                }
                            }


                        }
                    }
                    $inv = 0;
                    $inventories = $this->model_products->get_inventorydetails($variant['id']);
                    foreach($inventories as $inventory){
                        $now = time();
                        $expiration = strtotime($inventory['date_expiration']);
                        if(date('Y-m-d',$expiration) > date('Y-m-d') && $variant['id'] ==  $inventory['product_id']){
                            $inv += $inventory['qty'];
                            //(round(($expiration - $now)/(60*60*24))+1);
                        }
                    }
                    if($inv == 0){ ?>
                        <span class="badge" data-variant_id="<?= en_dec('en',$variant['id']); ?>" data-size="<?= $variant['name']; ?>"><?= $variant['name']; ?></span> <span>&#8369; <?= $badge;  ?></span><span class="ml-2 text-danger">SOLD OUT</span><br>
                    <?php }else{ ?>
                        <span class="badge  badge-info size-select" data-discountid="<?=$discount_id?>" data-variant_id="<?= en_dec('en',$variant['id']); ?>" data-size="<?= $variant['name']; ?>"><?= $variant['name']; ?></span> <span>&#8369; <?= $badge?></span> <br>
                    <?php } ?>
                    
                <?php } ?>

                <br><br>
                <small>Quantity</small><br>
                <input class="quantity-selector" id="qty" type="number" value="1" min="1" max="100">

                <hr>

            </div>
            <div class="row col-12">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <button class="btn btn-primary order-now form-control mt5" data-product_id="<?= $product['id'] ?>" data-max_checkout="<?=$product['max_qty'];?>">ORDER NOW</button>                   
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <button class="btn btn-primary add-to-cart form-control mt5" data-product_id="<?= $product['id'] ?>"  data-max_checkout="<?=$product['max_qty'];?>" data-category_id="<?= $product['category_id'] ?>">ADD TO CART</button>
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