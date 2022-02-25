<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
</style>

<div class="content-inner" id="pageActive" data-num="1" data-page="store" data-shopid="<?= $shopInfo["id"]?>"></div>
<div id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="true"></div>

<div class="container single-product-container">


<!-- Details Section -->
    <div class="container-fluid">
        <div class="shop-page-banner p-lg-3" style="background-image: url('<?=get_s3_imgpath_upload()."assets/img/shops-banner1500/".$shopInfo["banner"]?>')">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <div class="shop-page-container">
                        <div class="row no-gutters">
                            <div class="col-auto">
                                <img class="shop-page-image" src="<?=get_s3_imgpath_upload()."assets/img/shops-60/".$shopInfo["logo"]?>" onerror="this.onerror=null; this.src='<?=get_s3_imgpath_upload()."assets/img/shops/".$shopInfo["logo"]?>'" alt="">
                            </div>
                            <div class="col p-2">
                                <h5 class="shop-page-name"><?= $shopInfo["shopname"]?></h5>
                                <!-- <p class="shop-page-subname"><i class="fa fa-shopping-bag mr-2" aria-hidden="true"></i>1.1k Products</p> -->
                                <div class="row no-gutters">
                                    <div class="col-auto px-1">
                                        <a href="" class="socmed-item">
                                            <i class="fa fa-facebook-official mr-2"></i>
                                            Facebook
                                        </a>
                                    </div>
                                    <div class="col-auto px-1">
                                        <a href="" class="socmed-item">
                                            <i class="fa fa-instagram mr-2"></i>
                                            Instagram
                                        </a>
                                    </div>
                                    <div class="col-12 px-1">
                                        <div class="socmed-item">
                                            <i class="fa fa-envelope mr-2"></i>
                                            <?= $shopInfo["email"]?>
                                        </div>
                                    </div>
                                    <div class="col-12 px-1">
                                        <div class="socmed-item">
                                            <i class="fa fa-phone mr-2"></i>
                                            <?= $shopInfo["mobile"]?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="product-section shop-section container-fluid">
        <div class="row" id="searchDiv" hidden>
            <div class="col-12">
                <p class="search-result-title"><i class="far fa-lightbulb mr-2"></i>Search result for "<span class="highlight" id="searchkey_span"></span>"</p>
            </div>
        </div>
        <div class="row" id="productsTable">
            <!-- Product list here -->
        </div>
    </div>
    <div class="d-flex justify-content-center mb-4" >
        <button hidden class="btn load-more" id="load-more">Load More</button>
    </div>
</div>

<!-- <div class="single-product-container">
</div> -->

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
