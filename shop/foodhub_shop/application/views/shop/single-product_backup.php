<style type="text/css">
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
                    <li class="breadcrumb-item"><a href="<?=base_url("#search=".$productInfo['category_name'])?>"><?= $productInfo["category_name"]?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $productInfo["itemname"]?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-12 single-product-summary">
            <div class="row">
                <!-- <div class="col-12 col-md-5 single-product-image">
                    <div class="slicks">
                        <?php $dir = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$productInfo["shopcode"].'/products-520/'.$productInfo["Id"].'/')."*";?>
                        <?php $count = 1; $count2 = 1; ?>
                        <?php  foreach (glob($dir) as $dir_): ?>
                            <div>
                                <img class="picture<?=$count++?>" 
                                src="<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/").pathinfo($dir_, PATHINFO_BASENAME)?>" 
                                onerror="this.onerror=null; this.src='<?=base_url("assets/img/products/".$productInfo["Id"].".png")?>'">
                            </div>
                        <?php endforeach ?>
                    </div>

                    <div class="slider-nav">
                        <?php $dir = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$productInfo["shopcode"].'/products-520/'.$productInfo["Id"].'/')."*";?>
                        <?php $count = 1; $count2 = 1; ?>
                        <?php  foreach (glob($dir) as $dir_): ?>
                            <div>
                                <img class="picture<?=$count++?>" 
                                src="<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/").pathinfo($dir_, PATHINFO_BASENAME)?>" 
                                onerror="this.onerror=null; this.src='<?=base_url("assets/img/products/".$productInfo["Id"].".png")?>'">
                            </div>
                        <?php endforeach ?>
                    </div>
                </div> -->
                    <!-- <div class="w-100 single-product-image">
                        <img class="w-100" src="<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>" alt="">
                    </div> -->
                <div class="col-12 col-md-5 single-product-image">
                    <!-- The grid: four columns -->
                    <div class="">
                        <div class="">
                            <div id="0" class="picture" style="display:none; background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                            </div>
                            <!-- <div id="1" class="picture" style="display:none; background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").'c6e7f40516594f64a6f48a64adb2a7e0'.".jpg"?>')">
                            </div>
                            <div id="2" class="picture" style="display:none; background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                            </div>
                            <div id="3" class="picture" style="display:none; background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                            </div>
                            <div id="4" class="picture" style="display:none; background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                            </div>
                            <div id="5" class="picture" style="display:none; background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                            </div> -->
                        </div>
                    </div>
                    <div class="row pt-0 pb-4 no-gutters myhover-container">
                        <a href="javascript:void(0)" class="myhover" onmouseover="openImg('0');" style="background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                        </a>
                        <!-- <a href="javascript:void(0)" class="myhover" onmouseover="openImg('1');" style="background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").'c6e7f40516594f64a6f48a64adb2a7e0'.".jpg"?>')">
                        </a>
                        <a href="javascript:void(0)" class="myhover" onmouseover="openImg('3');" style="background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                        </a>
                        <a href="javascript:void(0)" class="myhover" onmouseover="openImg('4');" style="background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                        </a>
                        <a href="javascript:void(0)" class="myhover" onmouseover="openImg('5');" style="background-image: url('<?=base_url("assets/img/".$productInfo["shopcode"]."/products-520/".$productInfo["Id"]."/0-").$productInfo["Id"].".jpg"?>')">
                        </a> -->
                    </div>
                </div>
                

                <div class="col-12 col-md-7 py-0 py-3">
                    <div hidden id="productName_id_<?= $productInfo["Id"]?>"><?= $productInfo["itemname"]?></div>
                    <h3 class="single-product-title single-product-text"><?= $productInfo["itemname"]?></h3>
                    <div hidden id="price_id_val_<?= $productInfo["Id"]?>" data-value="<?= number_format($productInfo["price"],2)?>">
                    </div>
                    <h2 class="single-product-price single-product-text">₱ <?= number_format($productInfo["price"],2)?></h2>
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
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-secondary quantity__minus" type="button"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input type="text" class="form-control text-center quantity__input shop-input_qty" placeholder="" aria-label="" aria-describedby="basic-addon1" value="1" id="quantity_id_<?= $productInfo["Id"]?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary quantity__plus" type="button"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="single-product-buttons">
                        <div class="row no-gutters">
                            <div class="px-1 col-6 col-md-auto">
                                <button class="btn btn-secondary buy_now btn-block" data-unit="<?= $productInfo["otherinfo"]?>" data-category="<?= $productInfo["category_id"]?>" data-id="<?= $productInfo["itemid"]?>" data-shop="<?= $productInfo["sys_shop"]?>" data-productid="<?= $productInfo["Id"]?>">Buy Now</button>
                            </div>
                            <div class="px-1 col-6 col-md-auto">
                                <button class="btn btn-primary add_to_cart btn-block" data-unit="<?= $productInfo["otherinfo"]?>" data-category="<?= $productInfo["category_id"]?>" data-id="<?= $productInfo["itemid"]?>" data-shop="<?= $productInfo["sys_shop"]?>" data-productid="<?= $productInfo["Id"]?>">Add to Cart</button>
                            </div>
                        </div>
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

    <div class="row mb-5">
        <div class="col-12">
            <h5 class="single-product-detail-title single-product-text">Related Products</h5>
        </div>

        <div class="row" id="productsTable">
            
        </div>
    </div>
</div>
<?php  $this->load->view("includes/footer"); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>

<script type="text/javascript" src="<?=base_url('assets/js/autosize.min.js');?>"></script>
<script type="text/javascript">autosize(document.getElementById("f_summary"));</script>

<script type="text/javascript">
    $('.slicks').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        // dots: true,
        // adaptiveHeight: true,
        asNavFor: '.slider-nav',
        nextArrow: '<i class="fa fa-2x fa-chevron-right"></i>',
        prevArrow: '<i class="fa fa-2x fa-chevron-left"></i>',
    // "setting-name: setting-value"
    });

    $('.slider-nav').slick({
        slidesToShow: 3 ,
        slidesToScroll: 1,
        asNavFor: '.slicks',
        dots: true,
        centerMode: true,
        focusOnSelect: true
    });

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
</script>