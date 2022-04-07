<div class="container" id="checkout_container">
    <div class="row">
        <div class="col-lg-8 col-md-6 col-sm-12 clearfix">            
            <b>Shipping Address</b>

            <?php if(isset($_SESSION['has_logged_in'])){ ?>
                <?php if($_SESSION['has_logged_in'] == true){ ?>
                    <div type="button" class="badge badge-pill badge-primary btn float-right plr7 hidden" id="new_address">&plus;  New Address</div>
                <?php } ?>            
            <?php }?>

            <?php if(isset($shipping_address) && sizeof($shipping_address) > 0){ ?>
            <div class="col-12 p20">
                <h6>Deliver to:</h6>                
                    <?php foreach($shipping_address as $address){ ?>
                    <div class="payment-method-select" data-address_id="">
                        <span class="badge badge-pill badge-info"><?= $address['address_alias'] == '' ? $address['address_type'] : $address['address_alias']; ?></span><br>  
                        <b><?= $address['full_name']; ?></b><span>(<?= $address['contact_no']; ?>)</span><br>
                        <span><?= $address['address'] . ' ' . $address['barangay'] . ' ' . $address['city'] . ', ' .$address['province']; ?></span>         
                    </div>                               
                    <?php } ?>                
            </div>            
            <?php } ?>

            <br><br>

            <?php if(isset($shipping_address) && sizeof($shipping_address) > 0) ?>
            
            <?php $this->load->view('user/cart/address_form'); ?>            

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