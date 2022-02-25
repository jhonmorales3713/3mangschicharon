<style type="text/css">
    .disableddiv {
    pointer-events: none;
    opacity: 1;
}
</style>
    <section class="tables disableddiv">   
        <div class="container-fluid">
            <section class="tables">   
                <div class="container-fluid">
                    <div class="row justify-content-md-center">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Branch Details</h3>
                                </div>
                                <form class="form-horizontal personal-info-css" id="add-form">
                                    <div class="card-body">
                                        <input type="hidden" value="<?= $idno ?>" name="idno_hidden" id="idno_hidden">
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <?php if($this->session->userdata('branchid') == 0 AND $this->session->userdata('sys_shop') == 0){ ?>
                                                    <label class="form-control-label col-form-label-sm">Main Shop</label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-mainshop" data-reqselect2="yes">
                                                        <option value="" selected>Select Shop</option>
                                                        <?php select_option_obj($mainshop, 'mainshop') ?>
                                                    </select>
                                                <?php }else{ ?>
                                                    <label class="form-control-label col-form-label-sm">Main Shop</label>
                                                    <select class="select2 form-control form-control-sm required_fields" name="entry-mainshop" disabled>
                                                        <option value="" selected>Select Shop</option>
                                                        <?php select_option_obj($mainshop, 'mainshop') ?>
                                                    </select>
                                                    <div style="display: none !important;">
                                                    <select class="select2 form-control form-control-sm required_fields" name="entry-mainshop">
                                                        <option value="" selected>Select Shop</option>
                                                        <?php select_option_obj($mainshop, 'mainshop') ?>
                                                    </select>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-control-label col-form-label-sm">Branch Name</span></label>
                                                <input class="form-control form-control-sm required_fields" name="entry-branch" value="" type="text" placeholder="Ex. toktokmall Branch - BGC">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label class="form-control-label col-form-label-sm">Contact Person</label>
                                                <input class="form-control form-control-sm required_fields form-state" type="text" name="entry-contactperson" onkeydown="return alphaOnly(event);">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-control-label col-form-label-sm">Mobile Number</label>
                                                <?= infoicon_helper_msg('09XXXXXXXXX, Mobile No. must start with [09] follow by 9 digits number') ?>
                                                <input class="form-control form-control-sm allownumericwithoutdecimal required_fields form-state" type="text" name="entry-conno" pattern="[0]{1}[9]{1}[0-9]{9}"
placeholder="Ex. 09XXXXXXXXX" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-control-label col-form-label-sm">Email Address</label>
                                                <input type="email" class="form-control form-control-sm required_fields form-state" name="entry-email">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="form-control-label col-form-label-sm">Branch Address</label>
                                                <input class="form-control form-control-sm required_fields form-state" type="text" name="entry-address">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-control-label col-form-label-sm">Region</label>
                                                <select class="select2 form-control form-control-sm required_fields form-state" name="entry-branch_region" data-reqselect2="yes">
                                                    <option value="">Select Region</option>
                                                    <?php select_option_obj($region, 'region') ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-control-label col-form-label-sm">City</label>
                                                <select class="select2 form-control form-control-sm required_fields form-state" name="entry-branch_city" data-reqselect2="yes">
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <h3>Delivery Areas</h3>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="form-control-label col-form-label-sm">Cities</label>
                                                <select class="select2 form-control form-control-sm form-state taginput-field" name="entry-city[]" multiple="multiple" disabled>
                                                    <?php select_option_obj($city, 'city') ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-control-label col-form-label-sm">Province</label>
                                                <select class="select2 form-control form-control-sm form-state taginput-field" name="entry-province[]" multiple="multiple">
                                                    <?php select_option_obj($province, 'province') ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-control-label col-form-label-sm">Regions</label>
                                                <select class="select2 form-control form-control-sm form-state taginput-field" name="entry-region[]" multiple="multiple">
                                                    <?php select_option_obj($region, 'region') ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label class="form-control-label col-form-label-sm">Auto assign nearest orders?</label>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>