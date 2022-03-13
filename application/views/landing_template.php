<?php $this->load->view('templates/header');?>
<?php $this->load->view('templates/main_nav'); ?>

<div class="loading-screen">
    <div class="loading-icon">
    <center>
    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><br>
    <span>Loading...</span>
    </center>
    </div>
</div>

<div class="content">    
    <?= $page_content; ?>
</div>


<?php $this->load->view('templates/main_footer'); ?>
<?php $this->load->view('templates/footer');?>