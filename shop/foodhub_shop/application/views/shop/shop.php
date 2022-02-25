<?php
$header_upper_bg = header_upper_bg(); //"none";
$header_upper_txtcolor = header_upper_txtcolor(); //"#222222";

$header_middle_bg = header_middle_bg(); //"#222";
$header_middle_txtcolor = header_middle_txtcolor(); //"#fff";
$header_middle_icons = header_middle_icons();

$header_bottom_bg = header_bottom_bg(); //"#ff4444";
$header_bottom_textcolor = header_bottom_textcolor(); //"#fff";

$footer_bg = footer_bg(); //"#222222";
$footer_textcolor = footer_textcolor(); //"#ffffff";
$footer_titlecolor = footer_titlecolor();

$primaryColor_accent = primaryColor_accent(); //"#ff4444";

$fontChoice = fontChoice(); //"Quicksand";
?>
<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    @media screen and (max-width: 767px){
      .compared-price{
        font-size:8px !important;
      }

    }

    @media screen and (max-width: 375px){
      .compared-price{
        font-size:8px !important;
      }

    }

    @media screen and (max-width: 310px){
      .compared-price{
        font-size:7px !important;
      }

    }
</style>
<div class="content-inner" id="pageActive" data-num="1" data-page="shop" data-keyword=""></div>
<div id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="true"></div>

<?php 
if($this->session->userdata('get_shipping_locs')!=''){?>
<div class="shop-container shop-container__web">
    <!-- <div class="container-fluid">
        <div class="alert alert-warning" role="alert">
            <?//=shop_main_announcement();?>
        </div>
    </div> -->
    <div class="w-100 ad-banner-section shop-section container-fluid">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php $counter=0; foreach ($get_banners as $gb_): ?>
                    <?php if ($counter == 0){ ?>
                        <li data-target="#carouselExampleIndicators" data-slide-to="<?=$counter++;?>" class="active"></li>
                    <?php }else{ ?>
                        <li data-target="#carouselExampleIndicators" data-slide-to="<?=$counter++;?>"></li>
                    <?php } ?>
                <?php endforeach ?>
            </ol>
            <div class="carousel-inner">
                <?php $count = 0; ?>
                <?php foreach ($get_banners as $gb): ?>
                    <?php $filename = pathinfo($gb['filename'], PATHINFO_FILENAME); ?>
                    <?php if ($count == 0){ ?>
                        <div class="carousel-item active">
                            <a>
                                <img class="d-block w-100" src="<?=get_s3_imgpath_upload()."assets/img/all_banner/".removeFileExtension($filename).'.jpg'?>"
                                onerror="this.onerror=null; this.src='<?=get_s3_imgpath_upload()."assets/img/ad-banner/".$gb['filename']?>'"
                                alt="First slide">
                            </a>
                        </div>
                    <?php $count++; ?>
                    <?php }else{ ?>
                        <div class="carousel-item">
                            <a>
                                <img class="d-block w-100" src="<?=get_s3_imgpath_upload()."assets/img/all_banner/".removeFileExtension($filename).'.jpg'?>"
                                onerror="this.onerror=null; this.src='<?=get_s3_imgpath_upload()."assets/img/ad-banner/".$gb['filename']?>'"
                                alt="First slide">
                            </a>
                        </div>
                    <?php } ?>
                <?php endforeach ?>
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
<?php }else{?>

<div class="modal modal-md fade" style="" id="popup_desktop" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog md2" role="document">
        <div class="modal-content-2 mc2">
            <center>
                <div class="modal-header" style="border-bottom:none !important;">
                    <div class="row" style="width:100%; margin-left: 0 !important;">
                        <div class="col-12 mt-4">
                            <img width=200 src="<?=main_logo()?>">
                        </div>
                        <div style="margin-top:5rem;">
                            <span class="pop_up_text p-4" style="float:left !important;">SATISFY YOUR <span class="pop_up_text" style="color:<?=$primaryColor_accent?>">CRAVINGS</span> WITH JUST FEW TAPS</span>
                        </div>
                        <div class="col-10 mt-2" style="
                              -webkit-box-shadow: 0 5px 5px #888888;
                              -moz-box-shadow: 0 5px 5px #888888;
                              box-shadow: 0 5px 5px #888888;
                              margin: auto;
                              width: 50%;
                              padding: 10px;
                              display: flex;
                            ">
                            <span  class="p-1">
                                <i class="fa fa-map-marker fa-lg"style="color:<?=$primaryColor_accent?>"></i>
                            </span>&nbsp;
                            <select class="select2 shipped-to-main-city_2 bg-white" id="shipped-to-main-city_desktop" placeholder="Select City" style = "width:85% !important; background-color:white !important;">
                                <option value="0" data-citymunDesc = ""
                                    data-provCode = "0"
                                    data-regCode = "0">Select your location</option>
                            </select><br>
                            <span id="error_location_2"></span>
                        </div>
                        <div class="col-12 mb-5 mt-5 justify-content-center align-items-center">
                            <center>
                                <button class="btn btn-lg m-2"style = "border-radius: .8rem; font-weight: 800; padding: 1rem 1rem; width: 40%;" id = "filter-shipped-to-main_3">PROCEED</button>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="col-12" style="border-radius:.8rem;height:14rem;background-size:contain;background-image:url(<?=base_url("assets/img/bg.jpg");?>);background-repeat: no-repeat;
                    background-position: bottom;">
                    &nbsp;
                </div>
            </center>
        </div>
    </div>
</div>
<div class="row bg-white pop_up" style="display:none;">
    <div class="col-12">
        <center>
            <div class="row mb-4">
                <div class="col-12 mt-5">
                <img width=50% src="<?=main_logo()?>">
                </div>
            </div>
            <br>
            <div class="row">
                <span class="pop_up_text p-4">SATISFY YOUR CRAVINGS WITH JUST FEW TAPS</span>
            </div>
            <div class="row ">
                
                <div class="col-12">
                    <span  class="p-1 border-right">
                        <i class="fa fa-map-marker fa-lg"style="color:<?=$primaryColor_accent?>"></i>
                    </span>&nbsp;
                    
                    <select class="select2 shipped-to-main-city_2 bg-white" id="shipped-to-main-city_popup"  style = "width:250px !important; background-color:white !important;">
                        <option value="0" data-citymunDesc = ""
                            data-provCode = "0"
                            data-regCode = "0">Ship To</option>
                    </select><br>
                    <span id="error_location"></span>
                </div>
                <div class="col-12 mb-5 mt-2 justify-content-center align-items-center">
                    <button class="btn btn-sm btn-primary closer-shipped-to"style = "width:300px !important;" id = "filter-shipped-to-main_2">Filter</button>
                </div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <img width=100% class="fixed-bottom" src="<?=base_url("assets/img/bg.jpg");?>">
            </div>
        </center>
    </div>
</div>
<?php }?>
<!-- <div class="single-product-container">
</div> -->

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
