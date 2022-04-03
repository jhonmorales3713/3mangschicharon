<?php $this->load->view('templates/header');?>
<?php $this->load->view('admin/templates/admin_header');?>
<?php $this->load->view('admin/templates/admin_nav');?>
<div class="main-content container-fluid">
    <br><br><br><br>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-secondary ml-4 color-dark" role="alert">
                <span class="font-weight-bold"><?=$active_page?></span>
                &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
                <span class="font-weight-regular">Web Information</span>
            </div>
        </div>
        <div class="col-sm-5 mt-2 ml-4">
            <a href="<?=base_url('admin/settings/website_info/web_info_details');?>" >
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Website Information</div>
                    <small class="card-text text-black-50">Manage Website Details</small>
                </div>
            </a>
        </div>
        <div class="col-sm-5 mt-2 ml-4">
            <a href="<?=base_url('admin/settings/website_info/faqs_view');?>" >
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Frequently Asked Questions</div>
                    <small class="card-text text-black-50">Manage FAQS</small>
                </div>
            </a>
        </div>
        <div class="col-sm-5 mt-2 ml-4">
            <a href="<?=base_url('settings/website_info/web_info_details');?>" >
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">About Us</div>
                    <small class="card-text text-black-50">Manage About Us Page</small>
                </div>
            </a>
        </div>
    </div>
</div>


<?php $this->load->view('admin/templates/admin_footer');?>
<?php $this->load->view('templates/footer');?>

<script src="<?= base_url('assets/js/libs/admin/admin.js'); ?>"></script>