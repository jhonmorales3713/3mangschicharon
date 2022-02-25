<style>
    a.custom-card:hover {
      color: inherit;
      background-color: black;
    }    
</style>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shipping and Delivery</li>
        </ol>
    </div>
    <div class="container-fluid">
        <div class="row m-b-sm">
            <div class="col-lg-12">
                <div class="row">

                    <div class="col-md-6">
                        <?php if($sys_shop == 0){ ?>
                                <a href="<?= base_url('Settings_shipping_delivery/general_list/'.$token); ?>" class="w-100">
                        <?php }else{?>
                                <a href="<?= base_url('Settings_shipping_delivery/general_rates/'.$token.'/'.md5($sys_shop).''); ?>" class="w-100">
                        <?php } ?>
                            <div class="card card-option card-hover white p-3 mb-3 w-100">
                                <div class="row no-gutters">
                                    <div class="col-auto d-flex align-items-center">
                                        <i class="fa fa-truck fa-2x"></i>
                                    </div>
                                    <div class="col pl-3">
                                        <div class="card-header-title font-weight-bold">General Shipping</div>
                                        <small class="card-text text-black-50">Manage zone and rates that will apply to all your products.</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <?php if($sys_shop == 0){ ?>
                                <a href="<?= base_url('Settings_shipping_delivery/custom_list/'.$token); ?>" class="w-100">
                        <?php }else{?>
                                <a href="<?= base_url('Settings_shipping_delivery/custom_profile_list/'.$token.'/'.md5($sys_shop).''); ?>" class="w-100">
                        <?php } ?>
                            <div class="card card-option card-hover white p-3 mb-3 w-100">
                                <div class="row no-gutters">
                                    <div class="col-auto d-flex align-items-center">
                                        <i class="fa fa-cubes fa-2x"></i>
                                    </div>
                                    <div class="col pl-3">
                                        <div class="card-header-title font-weight-bold">Custom Shipping</div>
                                        <small class="card-text text-black-50">Create profiles for groups of products that need special rates.</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<!-- end - load the footer here and some specific js -->

