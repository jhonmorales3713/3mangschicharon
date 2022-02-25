<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .unit-price{
      color:grey;
      display: block;
    }

    @media only screen and (max-width: 991px) {
        footer {
            display: none;
        }
    }

    
</style>

<div class="content-inner" id="pageActive" data-num="1"></div>
<div id="pageActiveMobile" data-num="6"></div>
<div id="headerTitle" data-title="" data-search="false"></div>

<div class="container-fluid shop-container">
    <div class="portal-table col-lg-10">
        <br>
        <nav aria-label="breadcrumb ">
            <ol class="breadcrumb ">
                <li class="breadcrumb-item"><a href="<?=base_url('');?>">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        <!-- <div class="portal-table__container portal-table__container--shadow row" style="margin-bottom: 100px;" id="cartPage"> -->
            <!-- CART ITEMS  -->
        <div class="cartpage-footer" style="z-index: 1;">
            <div class="container-fluid pb-0 pr-0">
                <div class="row no-gutters">
                    <div class="col cartpage-total-container pb-0">
                        <div class="cartpage-total total__item"  id="total_amount_cart">
                            Total: <span class="cartpage-highlight"></span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="<?= base_url("shop/checkout") ?>" class="disabled" id="proceedCheckout"><div class="cartpage-button">
                            Checkout
                        </div></a>
                    </div>
                </div>
            </div>
        </div>
        <div id="cartPage" class = "mb-5" style = "margin-bottom: 100px !important;">
            <!-- CART ITEMS  -->
        </div>

    </div>
</div>
<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/cart-052620.js');?>"></script>
