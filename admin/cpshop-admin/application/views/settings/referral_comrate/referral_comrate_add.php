<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Referralcomrate/home/'.$token);?>">Referral Comrate</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add New Referral Comrate</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <section class="tables">   
                <div class="container-fluid">
                    <div class="row justify-content-md-center">
                        <div class="col-lg-10">
                            <div class="card">
                                <form class="form-horizontal personal-info-css" id="add-form">
                                    <input type="hidden" value="<?= $idno ?>" name="idno_hidden" id="idno_hidden">
                                    <div class="card-header">
                                        <h3 class="card-title">Referral Comrate</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if($idno == "" || empty($idno)){ ?> <!-- ADDING RECORD -->
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="form-control-label col-form-label-sm">Main Shop <span style="color:red">*</span></label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-mainshop">
                                                        <option value="" selected>Select Shop</option>
                                                        <?php select_option_obj($mainshop, 'mainshop') ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-control-label col-form-label-sm">Product <span style="color:red">*</span></label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-product">
                                                        <option value="" selected>Select Product</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">Startup <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm required_fields allownumericwithdecimal form-state" type="text" name="entry-startup">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">JC <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm allownumericwithdecimal required_fields form-state" type="text" name="entry-jc">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MCJR <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control form-control-sm allownumericwithdecimal required_fields form-state" name="entry-mcjr">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MC <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm required_fields allownumericwithdecimal form-state" type="text" name="entry-mc">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MC SUPER <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm allownumericwithdecimal required_fields form-state" type="text" name="entry-mcsuper">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MC MEGA <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control form-control-sm allownumericwithdecimal required_fields form-state" name="entry-mcmega">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">OTHERS <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control form-control-sm allownumericwithdecimal required_fields form-state" name="entry-others">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="form-group row">       
                                                    <div class="col-md-12">
                                                        <button type="submit" style="float:right" class="btn btn-primary saveBtn">Save</button>
                                                        <?= $back_button ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }else{ ?> <!-- UPDATE RECORD -->
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="form-control-label col-form-label-sm">Main Shop <span style="color:red">*</span></label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-mainshop">
                                                        <option value="">Select Shop</option>
                                                        <?php foreach($mainshop as $row){ ?>
                                                                <?php $shopcode = substr($record_details->itemid, 0, strpos($record_details->itemid, "_")); ?>
                                                                <?php if($shopcode == $row->shopcode){ ?>
                                                                        <option value="<?= $row->id ?>" selected><?= $row->shopname ?></option>
                                                                <?php }else{ ?>
                                                                        <option value="<?= $row->id ?>"><?= $row->shopname ?></option>
                                                                <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="hidden" id="product_hidden" value="<?= $record_details->product_id ?>">
                                                    <label class="form-control-label col-form-label-sm">Product <span style="color:red">*</span></label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-product">
                                                        <option value="" selected>Select Product</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">Startup <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm required_fields allownumericwithdecimal form-state" type="text" name="entry-startup" value="<?= $record_details->startup ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">JC <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm allownumericwithdecimal required_fields form-state" type="text" name="entry-jc" value="<?= $record_details->jc ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MCJR <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control form-control-sm allownumericwithdecimal required_fields form-state" name="entry-mcjr" value="<?= $record_details->mcjr ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MC <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm required_fields allownumericwithdecimal form-state" type="text" name="entry-mc" value="<?= $record_details->mc ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MC SUPER <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm allownumericwithdecimal required_fields form-state" type="text" name="entry-mcsuper" value="<?= $record_details->mcsuper ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">MC MEGA <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control form-control-sm allownumericwithdecimal required_fields form-state" name="entry-mcmega" value="<?= $record_details->mcmega ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">OTHERS <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control form-control-sm allownumericwithdecimal required_fields form-state" name="entry-others" value="<?= $record_details->others ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="form-group row">       
                                                    <div class="col-md-12">
                                                        <button type="submit" style="float:right" class="btn btn-primary saveBtn">Update</button>
                                                        <?= $back_button ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/referral_comrate/get_product_of_shop.js');?>"></script>
<script type="text/javascript" src="<?=base_url($main_js);?>"></script>
<!-- end - load the footer here and some specific js -->
