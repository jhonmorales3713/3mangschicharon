<div class="container">  
  <div class="row">
      <div class="col-lg-3 col-md-3 col-sm-12"></div>
      <div class="col-lg-6 col-md-6 col-sm-12">
        <center>
          <img src="<?= base_url('assets/img/gcash_icon.png'); ?>" alt="" width="100%">
          <span>You are about to pay: </span><br><br>
          <strong><?= php_money($_SESSION['order_data']['total_amount']); ?></strong><br><br>
          <a class="btn btn-primary" href="<?= $checkout_url; ?>">PROCEED</a>
          
        </center>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-12"></div>
  </div>
</div>






