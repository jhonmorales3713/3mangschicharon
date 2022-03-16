<?php $this->load->view('templates/header');?>
<?php $this->load->view('admin/templates/admin_header');?>
<?php $this->load->view('admin/templates/admin_nav');?>
<div class="main-content container-fluid">
    <?php if($subnav==true){
        $this->load->view('admin/templates/admin_body',$active_page);
    }else{?><br><br><br><br>
    <div class="row body" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="<?=$active_page?>" data-base_url="<?=base_url()?>">
        <?=($page_content);
    }?>
        
    </div>
</div>


<?php $this->load->view('admin/templates/admin_footer');?>
<?php $this->load->view('templates/footer');?>

<script src="<?= base_url('assets/js/libs/admin/admin.js'); ?>"></script>