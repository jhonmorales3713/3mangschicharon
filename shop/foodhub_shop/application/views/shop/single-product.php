<style >
    textarea#f_summary {
    width:100%;
    display:block;
    max-width:100%;
}

.slick-slide {
    outline: none
}

i.fa.fa-chevron-left.slick-arrow {
    position:absolute;
    z-index:1;
    left:2%;
    top:50%;
    cursor: pointer;
}

i.fa.fa-chevron-right.slick-arrow {
    position:absolute;
    z-index:1;
    right:2%;
    top:50%;
    cursor: pointer;
}

/*.slider-nav .slick-slide.slick-current.slick-active.slick-center{
    transition: transform .2s;
    transform: scale(1.5);
}

.slider-nav .slick-slide {
  width: 50px;
  height: 50px;
}*/

</style>

<div class="content-inner" id="pageActive" data-num="1" data-page="product"></div>

<div class="container single-product-container">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?=base_url()?>">Shop</a></li>
                    <li class="breadcrumb-item"><a href="<?=base_url("search?keyword=".$productInfo['category_name'])?>"><?= $productInfo["category_name"]?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $productInfo["itemname"]?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-12 single-product-summary pl-lg-5">
            <div class="row">
                <div class="col-12 col-md-4 single-product-image">
                    <!-- The grid: four columns -->
                    <div class="">
                        <div class="">
                          <?php $images = get_product_images($productInfo['Id']);?>
                          <?php $dir = get_s3_imgpath_upload()."assets/img/".$productInfo['shopcode']."/products-520/".$productInfo['Id']."/";?>
                          <?php $count = 1; $count2 = 1; $cc = 0; $cc_p = 0; ?>

                          <?php if($images->num_rows() > 0):?>
                              <?php foreach($images->result_array() as $image):?>
                                <div id="<?=$cc++;?>" class="picture" style="display:none; background-image: url('<?=$dir.removeFileExtension($image['filename']).'.jpg'?>')">
                                </div>
                              <?php endforeach;?>
                          <?php endif;?>
                        </div>
                    </div>
                    <div class="row pt-0 pb-4 no-gutters myhover-container">
                      <?php if($images->num_rows() > 1):?>
                          <?php foreach($images->result_array() as $image):?>
                            <a href="javascript:void(0)" class="myhover" onmouseover="openImg('<?=$cc_p++;?>');" style="background-image: url(<?=$dir.removeFileExtension($image['filename']).'.jpg'?>)">
                            </a>
                          <?php endforeach;?>
                      <?php endif;?>
                    </div>
                </div>

                <div class="col-12 col-md-8 py-0 py-3">
                    <div class="row">
                        <div class="col-12 col-md">
                            <div hidden id="productName_id_<?= $productInfo["Id"]?>"><?= $productInfo["itemname"]?></div>
                            <h3 class="single-product-title single-product-text"><?= $productInfo["itemname"]?></h3>
                            <?php if((float)$productInfo['compare_at_price'] > 0 && (float)$productInfo['compare_at_price'] > (float)$productInfo['price']):?>
                              <h6>
                                <span style = "text-decoration:line-through;">&#8369; <?=number_format($productInfo['compare_at_price'],2)?></span> &nbsp;
                                <span class="badge badge-primary" style = "background-color:#<?=primaryColor_accent()?>">
                                  <?=(100 - ( round( ((float)$productInfo['price'] / (float)$productInfo['compare_at_price']) * 100 ) ) )?>% OFF
                                </span>
                              </h6>
                            <?php endif;?>
                            <!-- <h6 class="single-product-title single-product-text">Sold By: <a href="<?=base_url('store/'.$productInfo["shopcode"])?>"><?= $productInfo["shopname"]?></a></h6> -->
                            <div hidden id="price_id_val_<?= $productInfo["Id"]?>" data-value="<?= number_format($productInfo["price"],2)?>">
                            </div>
                            <h2 class="single-product-price single-product-text"><?php if($productInfo['variant_isset'] == 1 && $productInfo['parent_product_id'] == null):?>
                                <?php
                                  $min_arr = explode(',',$productInfo['min']);
                                  $max_arr = explode(',',$productInfo['max']);
                                  $min = $min_arr[0];
                                  $max = $max_arr[0];
                                ?>

                                <?php if($min == $max){?>
                                    <!-- //edited 05/29/21 -->
                                    <?php if ($min == "" || $max == ""){ ?>
                                        ₱ <?= number_format($productInfo["price"],2)?>
                                    <?php } else { ?>
                                        ₱ <?= number_format($min,2)?>
                                    <?php } ?>
                                <?php } else {?>
                                  ₱ <?= number_format($min,2)?> - ₱ <?= number_format($max,2)?>
                                <?php }?>
                              <?php else:?>
                                ₱ <?= number_format($productInfo["price"],2)?>
                              <?php endif;?></h2>
                        </div>
                        <?php if (allow_shop_page() == 1): ?> <!-- //validation for allow shop page -->
                            <div class="col-9 col-md-4 mb-3 mb-md-0">
                                <a href="<?=base_url('store/'.$productInfo["shopurl"])?>">
                                    <div class="row no-gutters">
                                        <div class="col-2 col-md-3">
                                            <img class="w-100" src="<?=base_url("assets/img/shops-60/".pathinfo($productInfo["logo"], PATHINFO_FILENAME).".jpg")?>" alt="">
                                        </div>
                                        <div class="col px-2">
                                            <div class="store" style="font-size: 13px;">
                                                <div style="font-size: 0.7em">Sold by</div>
                                                <div class="store-name font-weight-bold primary-color" style="font-size: 1em"><?= $productInfo["shopname"]?></div>
                                                <div style="font-size: 0.8em; color: #ff4444; font-weight: bold;">View Store</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="">Shipping calculated at checkout</div>
                    <div class="single-product-summary">
                        <div class="row mb-3">
                            <div class="col-3 col-lg-2 summary-title">
                                Pack
                            </div>
                            <div class="col summary-content">
                                <?= $productInfo["otherinfo"]?>
                            </div>
                        </div>
                        <?php if($productInfo["summary"] != "") {?>
                            <div class="row mb-3">
                                <div class="col-3 col-lg-2 summary-title">
                                    Summary
                                </div>
                                <div class="col summary-content">
                                    <textarea disabled style="border: 0px; background-color:white; resize: none;" class="summary-content" name="f_summary" id="f_summary" rows="3"><?=$productInfo["summary"]?></textarea>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="single-product-quantity">
                        <div class="row">
                            <div class="single-product-quantity-title col-3 col-lg-2 d-flex align-items-center summary-title">
                                Quantity
                            </div>
                            <div class="col d-flex align-items-center">
                                <?php if($productInfo["cartable"]) {?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-secondary quantity__minus" type="button"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input type="text" class="form-control text-center quantity__input shop-input_qty" placeholder="" aria-label="" aria-describedby="basic-addon1" value="1" id="shop_quantity_id_<?= $productInfo["Id"]?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary quantity__plus" type="button"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button disabled class="btn btn-outline-secondary quantity__minus" type="button"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input disabled type="text" style="background-color: white;" class="form-control text-center" placeholder="" aria-label="" aria-describedby="basic-addon1" min="0" max="0" value="0">
                                    <div class="input-group-append">
                                        <button disabled class="btn btn-outline-secondary quantity__plus" type="button"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="single-product-buttons">
                        <?php if($productInfo["cartable"]) {?>
                        <div class="row no-gutters">
                            <div class="px-1 col-6 col-md-auto">
                                <button class="btn btn-secondary buy_now btn-block" data-max_qty_isset = "<?=$productInfo["max_qty_isset"]?>" data-max_qty = "<?=$productInfo["max_qty"]?>" data-primary_pics = "<?=$productInfo["primary_pics"]?>" data-unit="<?= $productInfo["otherinfo"]?>" data-category="<?= $productInfo["category_id"]?>" data-id="<?= $productInfo["itemid"]?>" data-shop="<?= $productInfo["sys_shop"]?>" data-productid="<?= $productInfo["Id"]?>"  data-variant_isset="<?=$productInfo['variant_isset']?>" data-min= "<?=$productInfo['min']?>" data-max= "<?=$productInfo['max']?>">Buy Now</button>
                            </div>
                            <div class="px-1 col-6 col-md-auto">
                                <button class="btn btn-primary add_to_cart btn-block" data-max_qty_isset = "<?=$productInfo["max_qty_isset"]?>" data-max_qty = "<?=$productInfo["max_qty"]?>" data-primary_pics = "<?=$productInfo["primary_pics"]?>" data-unit="<?= $productInfo["otherinfo"]?>" data-category="<?= $productInfo["category_id"]?>" data-id="<?= $productInfo["itemid"]?>" data-shop="<?= $productInfo["sys_shop"]?>" data-productid="<?= $productInfo["Id"]?>" 
                                    data-variant_isset="<?=$productInfo['variant_isset']?>" data-min= "<?=$productInfo['min']?>" data-max= "<?=$productInfo['max']?>">Add to Cart</button>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="row" style="padding: 0 20px;">
                            <div class="col-3 col-lg-2 summary-title">
                            </div>
                            <div class="col summary-content" style="color: red;">
                                Sold Out
                            </div>
                        </div>
                        <?php } ?>
                        <br>
                        <div class="row no-gutters mb-2">
                            <div class="col px-2">
                                <div class="store" style="font-size: 13px;">
                                    <div style="font-size: 0.7em">Tags</div>
                                    <div class="store-name font-weight-bold primary-color" style="font-size: 0.9em">
                                        <?php if (strlen($productInfo['tags']) > 0){
                                            $tags = explode(",", $productInfo['tags']);
                                            $tags = array_map('strtoupper', $tags);
                                            $tags = implode(", ", $tags);
                                            echo $tags;
                                        ?>

                                        <?php }else{
                                            echo 'No available tags';
                                        }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row no-gutters">
                            <div class="col px-2">
                                <div class="store" style="font-size: 13px;">
                                    <div style="font-size: 0.7em">Ships To</div>
                                    <div class="store-name font-weight-bold primary-color" style="font-size: 0.9em"><?= strlen($shipsFrom) > 0 ? $shipsFrom : 'No available location'; ?></div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Details Section -->
    <!-- <div class="row mb-5">
        <div class="col-12 single-product-full-detail">
            <h5 class="single-product-detail-title single-product-text">Details</h5>
            <?= $productInfo["details"]?>
            <div class="">
                <div class="row">
                    <div class="content-container col-md-6 col-12">
                        <h6 class="font-weight-bold mb-3">Coffee Powder</h6>
                        <ul class="fa-ul">
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps relieve asthma and pharyngitis</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps relieve rheumatism, dyspepsia, boils and diarrhea</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps prevent cough, colds, fever, flu and other bronchopulmonary disorders</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>Its helps alleviate symptoms of chicken pox</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps the removal of worms and boils</li>
                        </ul>
                    </div>

                    <div class="content-container col-md-6 col-12">
                        <h6 class="font-weight-bold mb-3">Tongkat Ali</h6>
                        <ul class="fa-ul">
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps improve hormonal balance</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps improve focus and stamina</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps boost the immune system</li>
                        </ul>
                    </div>
                    <div class="content-container col-md-6 col-12">
                        <h6 class="font-weight-bold mb-3">Agaricus Mushroom</h6>
                        <ul class="fa-ul">
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps promote healthy function of major organs in the body</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps regulate blood pressure</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps support the body’s immune system</li>
                        </ul>
                    </div>
                    <div class="content-container col-md-6 col-12">
                        <h6 class="font-weight-bold mb-3">Ginseng</h6>
                        <ul class="fa-ul">
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps promote better sexual performance for men</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps promote healthier blood sugar level</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It is a very good weight management agent by suppressing appetite</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps ease distress from dysmenorrhea</li>
                    </ul>
                    </div>
                    <div class="content-container col-md-6 col-12">
                        <h6 class="font-weight-bold mb-3">Gingko Biloba</h6>
                        <ul class="fa-ul">
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps improve brain heath and function</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps increase memory and attention span</li>
                        </ul>
                    </div>
                    <div class="content-container col-md-6 col-12">
                        <h6 class="font-weight-bold mb-3">Omega 6</h6>
                        <ul class="fa-ul">
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps better absorption of calcium</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps fight osteoporosis</li>
                        <li><span class="fa-li"><i class="fab fa-envira"></i></span>It helps lower blood pressure</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Related Products Section -->
    <?php if(ini()!= 'coppermask') { ?>
        <div class="row mb-5 related-product-div">
            <div class="col-12">
                <h5 class="single-product-detail-title single-product-text">Related Products</h5>
            </div>
            <div class="col-12">
                <div class="row" id="productsTable">

                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php  $this->load->view("includes/footer"); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>

<script type="text/javascript" src="<?=base_url('assets/js/autosize.min.js');?>"></script>
<script type="text/javascript">autosize(document.getElementById("f_summary"));</script>

<script type="text/javascript">
    // $('.slicks').slick({
    //     infinite: true,
    //     slidesToShow: 1,
    //     slidesToScroll: 1,
    //     // dots: true,
    //     // adaptiveHeight: true,
    //     asNavFor: '.slider-nav',
    //     nextArrow: '<i class="fa fa-2x fa-chevron-right"></i>',
    //     prevArrow: '<i class="fa fa-2x fa-chevron-left"></i>',
    // // "setting-name: setting-value"
    // });

    // $('.slider-nav').slick({
    //     slidesToShow: 3 ,
    //     slidesToScroll: 1,
    //     asNavFor: '.slicks',
    //     dots: true,
    //     centerMode: true,
    //     focusOnSelect: true
    // });

    openImg("0");

    function openImg(imgName) {
        var i, x;
        x = document.getElementsByClassName("picture");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }

        // if (imgName === null) {
            document.getElementById(imgName).style.display = "block";
        // }

    }

    fbq('track', 'ViewContent');
</script>
