<div class="content-inner" id="pageActive" data-num="2"></div>
<div class="content-inner" id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="false"></div>
<div class="container-fluid payment-done check_order">
  <br>
  <br><br>
    <div class="row">
      <div class="col-lg-3 col-sm-1"></div>
      <div class="col-lg-9 col-sm-10">
        <h6 class="portal-table__product">Please enter your order reference number</h6>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3 col-sm-1"></div>
      <div class="col-sm-10 col-lg-6  text-center">
        <input type="text" class="form-control" id="refno" name="refno" placeholder="<?= 'e.g. '.order_so_ref_prefix().'2431586022615';?>">
        <br>
        <button type="button" id="checkOrder" class="btn portal-primary-btn">Track my Order</button>
      </div>
    </div>
</div>

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/orders/check-order.js');?>"></script>