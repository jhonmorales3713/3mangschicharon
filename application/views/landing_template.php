<?php $this->load->view('templates/header');?>
<?php $this->load->view('templates/main_nav'); ?>

<div class="main-content">
    <?= $page_content; ?>
</div>


<?php $this->load->view('templates/main_footer'); ?>
<?php $this->load->view('templates/footer');?>