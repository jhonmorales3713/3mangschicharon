<div class="container" id="checkout_container">
    <div class="row">
        <div class="col-lg-8 col-md-6 col-sm-12">            
            <b>Shipping Address</b>
            <?php if(isset($shipping_address) && sizeof($shipping_address) > 0){ ?>
                
            <?php } else {?>
                <?php $this->load->view('user/cart/address_form'); ?>
            <?php }?>           

            <hr>
            <?php $this->load->view('user/cart/item_list'); ?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 p20">            
            <?php $this->load->view('user/cart/billing_summary'); ?>            
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/cart/cart.js') ?>"></script>
<script src="<?= base_url('assets/js/libs/user/cart/checkout.js') ?>"></script>