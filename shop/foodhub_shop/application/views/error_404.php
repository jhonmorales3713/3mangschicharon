    <div class="row">
      <div class="offset-lg-2 col-lg-8 col-12 text-center my-5">
        <?php
          $img = c_404page();
          if ($img == ''):
        ?>
        <img src="<?php echo base_url('assets/img/404.png') ?>" alt="" class="mb-4 w-50">
        <?php else: ?>
        <img src="<?php echo base_url('assets/img/'.$img.'') ?>" alt="" class="mb-4 w-50">
        <?php endif; ?>
        <h3>Page not found.</h3>
        <h6>It appears the page you were looking for doesn't exists. Sorry about that.</h6>
        <div class="text-center pt-3 pb-3">
          <a href="<?php echo base_url();?>" class="btn btn-secondary">Continue Shopping</a>
        </div>
      </div>
    </div>

<?php  $this->load->view("includes/footer"); ?>