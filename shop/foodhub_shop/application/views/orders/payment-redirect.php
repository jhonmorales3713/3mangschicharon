<?php $this->load->view('includes/header'); ?>
<div class="row mt-5 pb-5">
  <?php if ((int) $transaction['payment_status'] !== 1) { ?>
    <div class="col-12 col-md-6 offset-md-3">
      <div class="product-card" id="item-total-card">
        <div class="product-card-body py-3 product-card-total text-center">
            <div class="row no-gutters mb-3">
              <div class="col-12">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              </div>
            </div>
            <div class="row no-gutters">
              <div class="col-12">
                <p class="font-weight-bold">Please wait as the system redirect you to Paypanda...</p>
              </div>
            </div>
        </div> 
      </div>
    </div>  
    <form id="paypanda_trans" method="post" action="<?=c_paypanda_link()?>">
        <?php 
            if(isset($params))
            {
                foreach ($params as $key => $value)
                {
                    echo '<input class="form-control" type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'">';
                }
            }
        ?>
    </form>
  <?php } else { ?>
    <div class="col-12 col-md-6 offset-md-3">
      <div class="product-card" id="item-total-card">
        <div class="product-card-body py-3 product-card-total text-center">
            <div class="row no-gutters">
              <div class="col-12">
                <p class="font-weight-bold">This order has been paid already.</p>
              </div>
              <div class="col-auto mx-auto">
                  <a href="<?=base_url('');?>"><button id="continueshop" class="btn checkout-button">Continue Shopping</button></a>
              </div>
            </div>
        </div> 
      </div>
    </div>
  <?php } ?>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
  $(document).ready(function(){
    if ($('#paypanda_trans').length){
      $('#paypanda_trans').submit();
    }
  });
</script>