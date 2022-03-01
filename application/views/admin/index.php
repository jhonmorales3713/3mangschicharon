<?php $this->load->view('templates/header');?>

<?php $this->load->view('admin/templates/admin_header');?>
<?php $this->load->view('admin/templates/loading_screen');?>
<?php $this->load->view('admin/templates/admin_nav');?>

<div class="main-content">
    <div class="pl30">    
        <?php if(isset($sub_page)){ ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="<?= $active_page; ?>"><a href="<?= base_url('admin/'.$active_page); ?>"><?= ucfirst($active_page); ?></a></li>
                    <li class="breadcrumb-item" aria-current="<?= $sub_page; ?>"><?= ucfirst($sub_page); ?></li>
                </ol>
            </nav>
            <hr>
        <?php } ?>
        
        <?= $page_content; ?>
    </div>    
</div>


<?php $this->load->view('admin/templates/admin_footer');?>
<?php $this->load->view('templates/footer');?>

<script src="<?= base_url('assets/js/libs/admin/admin.js'); ?>"></script>