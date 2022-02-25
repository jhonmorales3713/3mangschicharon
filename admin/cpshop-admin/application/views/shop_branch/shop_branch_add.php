<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/maploader.css');?>">
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/shops_home/'.$token);?>">Shops</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php if($this->session->userdata('branchid') == 0){ ?>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Shopbranch/home/'.$token);?>">Shops Branch</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php } ?>
            <li class="breadcrumb-item active"><?= $breadcrumbs ?></li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <section class="tables" style="padding-bottom: 20px;">   
                <div class="container-fluid">
                    <div class="row justify-content-md-center">
                        <div class="col-lg-8" style="margin: 0px !important; padding: 0px !important;">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Branch Profile</h3>
                                    </div>
                                    <form class="form-horizontal personal-info-css" id="add-form">
                                        <div class="card-body">
                                            <input type="hidden" value="<?= $idno ?>" name="idno_hidden" id="idno_hidden">
                                            <div class="form-group row">
                                                    <?php if($this->session->userdata('branchid') == 0 AND $this->session->userdata('sys_shop') == 0){ ?>
                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm">Main Shop <span style="color:red">*</span></label>
                                                            <select class="select2 form-control form-control-sm required_fields form-state" name="entry-mainshop" data-reqselect2="yes">
                                                                <option value="" selected>Select Shop</option>
                                                                <?php select_option_obj($mainshop, 'mainshop') ?>
                                                            </select>
                                                    <?php }else{ ?>
                                                        <div class="col-md-6" style="display:none;">
                                                            <label class="form-control-label col-form-label-sm">Main Shop <span style="color:red">*</span></label>
                                                            <!-- <select class="select2 form-control form-control-sm required_fields" name="entry-mainshop" disabled>
                                                                <option value="" selected>Select Shop</option>
                                                                <?php select_option_obj($mainshop, 'mainshop') ?>
                                                            </select> -->
                                                            <div style="display: none !important;">
                                                            <select class="select2 form-control form-control-sm required_fields" name="entry-mainshop">
                                                                <option value="<?=$this->session->userdata('sys_shop');?>" selected>Select Shop</option>
                                                                <?php select_option_obj($mainshop, 'mainshop') ?>
                                                            </select>
                                                            </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-control-label col-form-label-sm">Branch Name <span style="color:red">*</span></span></label>
                                                    <input class="form-control form-control-sm required_fields" name="entry-branch" value="" type="text" placeholder="Ex. toktokmall Branch - BGC">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">Contact Person <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm required_fields form-state" type="text" name="entry-contactperson" onkeydown="return alphaOnly(event);">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">Mobile Number <span style="color:red">*</span></label>
                                                    <?= infoicon_helper_msg('09XXXXXXXXX, Mobile No. must start with [09] follow by 9 digits number') ?>
                                                    <!-- <input class="form-control form-control-sm allownumericwithoutdecimal required_fields form-state" type="text" name="entry-conno" pattern="[0]{1}[9]{1}[0-9]{9}" -->
    <!-- placeholder="Ex. 09XXXXXXXXX" required> -->
                                                   <input class="form-control form-control-sm allownumericwithoutdecimal required_fields form-state" type="text" name="entry-conno" required> 
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-control-label col-form-label-sm">Email Address <span style="color:red">*</span></label>
                                                    <input type="email" class="form-control form-control-sm required_fields form-state" name="entry-email">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-6">
                                                    <label class="form-control-label col-form-label-sm">Branch Address <span style="color:red">*</span></label>
                                                    <input class="form-control form-control-sm required_fields form-state" type="text" name="entry-address">
                                                </div>
                                                <div class="col-6" id="pac-container">
                                                    <label class="form-control-label col-form-label-sm" for="">Pin Address <span class="asterisk"></span></label>
                                                    <input id="pin_address" type="text" placeholder="Search" class = "form-control pr_field detail-input" name = "pin_address" style = "padding:10px;font-size:15px !important;" value="">
                                                    <input type="hidden" name = "loc_latitude" id = "loc_latitude" name = "loc_latitude" class = "pr_field" value = "">
                                                    <input type="hidden" name = "loc_longitude" id = "loc_longitude" name = "loc_longitude" class = "pr_field" value = "">
                                                </div>
                                                <div class="col-12">
                                                    <div id="map" style = "height:200px;margin-top:30px;"></div>
                                                    <div id="infowindow-content">
                                                      <!-- <img src="" width="16" height="16" id="place-icon"> -->
                                                      <span id="place-name"  class="title"></span><br>
                                                      <span id="place-address"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-control-label col-form-label-sm">Region <span style="color:red">*</span></label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-branch_region" data-reqselect2="yes">
                                                        <option value="">Select Region</option>
                                                        <?php select_option_obj($region, 'region') ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-control-label col-form-label-sm">City</label>
                                                    <select class="select2 form-control form-control-sm form-state" name="entry-branch_city" data-reqselect2="yes">
                                                        <option value="">Select City</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 mt-4 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Delivery Areas</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-control-label">Cities</label>
                                                <select class="select2 form-control form-control-sm form-state taginput-field" name="entry-city[]" multiple="multiple" disabled>
                                                    <?php select_option_obj($city, 'city') ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-control-label">Province</label>
                                                <select class="select2 form-control form-control-sm form-state taginput-field" name="entry-province[]" multiple="multiple">
                                                    <?php select_option_obj($province, 'province') ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-control-label">Regions</label>
                                                <select class="select2 form-control form-control-sm form-state taginput-field" name="entry-region[]" multiple="multiple">
                                                    <?php select_option_obj($region, 'region') ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row col-md-6" style="margin: 0px !important;">
                                                <label class="form-control-label mt-3">Auto assign nearest orders</label>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item" style="border-top: 0 none;">
                                                        <label class="switch">
                                                            <input type="hidden" id="entry-isautoassign" name="entry-isautoassign" value="">
                                                            <input type="checkbox" id="checkbox-isautoassign" class="success">
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4" style="margin: 0px !important; padding: 0px !important;">
                            <div class="col-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Bank Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-bankname" class="control-label">Bank Name <span style="color:red">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-bankname" id="entry-bankname" >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-acctname" class="control-label">Account Name <span style="color:red">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-acctname" id="entry-acctname" >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-acctno" class="control-label">Account Number <span style="color:red">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-acctno" id="entry-acctno" >
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="entry-desc" class="control-label">Account Type <span style="color:red">*</span></label>
                                                            <textarea class="form-control required_fields" name="entry-desc" id="entry-desc" row="2"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if($this->session->userdata('sys_shop') == 0){ ?> <!-- ADDING NEW RECORD -->
                            <div class="col-12 col-lg-12 mt-4">  
                            <?php }else{ ?>
                            <div class="col-12 col-md-12 d-none mt-4">   
                            <?php } ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Admin Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="entry-idnopb" class="control-label">Shop Branch ID Number</label>
                                                    <input type="text" class="form-control" name="entry-idnopb" id="entry-idnopb" >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="entry-idnopb" class="control-label">Treshold Inventory</label>
                                                    <input type="text" class="form-control allownumericwithoutdecimal" name="entry-treshold" id="entry-treshold" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </section>
        </div>
                            <div class="col-12 col-lg-12" style="padding: 0px;">  
                                <div class="card">
                                    <div class="card-body">       
                                        <div class="col-md-12">
                                            <?php if ($this->loginstate->get_access()['shop_branch']['create'] == 1 || $this->loginstate->get_access()['shop_branch']['update'] == 1 || $this->loginstate->get_access()['branch_account']['update'] == 1 || $this->loginstate->get_access()['branch_account']['update'] == 1){ ?>
                                                <button type="submit" style="float:right" class="btn btn-primary saveBtn ml-2">Save</button>
                                            <?php } ?>
                                            <?php if($this->loginstate->get_access()['branch_account']['update'] == 1 || $this->loginstate->get_access()['branch_account']['update'] == 1 AND $this->loginstate->get_access()['shop_branch']['create'] == 0 AND $this->loginstate->get_access()['shop_branch']['update'] == 0){ ?>
                                                <button type="button" style="float:right" class="btn btn-default" id="back-button" data-value="<?= base_url('Main_page/display_page/shops_home/'.$token) ?>">Back</button>
                                            <?php }else{ ?>
                                                <?= $back_button ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
    </section>
    </form>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/shop_branch/shop_branch_core_functions.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/shop_branch/shop_branch_cityofregion.js');?>"></script>
<script type="text/javascript" src="<?=base_url($main_js);?>"></script>
<script type="text/javascript" src="<?=base_url($googlemap_js);?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=get_google_api_key()?>&libraries=places&callback=initializeMaps"
async defer></script>
<!-- end - load the footer here and some specific js -->
