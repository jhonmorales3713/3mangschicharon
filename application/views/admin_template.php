<?php $this->load->view('templates/header');?>
<?php $this->load->view('admin/templates/admin_header');?>
<?php $this->load->view('admin/templates/admin_nav');?>

<div class="main-content">
    <?= $page_content; ?>
</div>


<?php $this->load->view('admin/templates/admin_footer');?>
<?php $this->load->view('templates/footer');?>