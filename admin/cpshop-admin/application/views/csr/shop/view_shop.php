<style type="text/css">
    .disableddiv {
    	pointer-events: none;
    	opacity: 1;
	}
</style>
    <section class="tables disableddiv">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Shop Details</h3>
                        </div>
						<div class="card-body">
						    <div class="row">
						        <div class="col-12">
						            <div class="row hidden">
						                <div class="col-12">
						                    <div class="form-group">
						                        <label>ID</label>
						                        <input type="text" name="entry-id" id="entry-id" class="form-control" value="<?= $sys_shop_details->idno ?>" >
						                        <input type="hidden" name="entry-old_logo" class="form-control" value="<?= $sys_shop_details->logo ?>" >
						                        <input type="hidden" name="entry-old_banner" class="form-control" value="<?= $sys_shop_details->banner ?>" >
						                        <input type="text" name="logo_select" id="logo_select" class="form-control">
						                        <input type="text" name="banner_select" id="banner_select" class="form-control">
						                    </div>
						                </div>
						            </div>
						            <div class="row">
						                <div class="col-md-12">
						                    <div class="form-group">
						                        <label for="entry-shopname" class="col-form-label-sm">Shop Name</label>
						                        <input class="form-control form-control-sm disabled" type="text" value="<?= $sys_shop_details->shopname ?>" disabled>
						                        <!-- <label class="control-label form-control-lg" id="entry-shopname" style="padding: 0px !important;"><?= $sys_shop_details->shopname ?></label> -->
						                    </div>
						                </div>
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <label for="entry-mobile" class="col-form-label-sm">Contact Number <span class="red-asterisk">*</span></label>
						                        <input type="text" class="form-control allownumericwithoutdecimal required_fields" name="entry-mobile" id="entry-mobile" value="<?= $sys_shop_details->mobile ?>">
						                    </div>
						                </div>
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <label for="entry-email" class="col-form-label-sm">Email Address <span class="red-asterisk">*</span></label>
						                        <input type="email" class="form-control required_fields" name="entry-email" id="entry-email" value="<?= $sys_shop_details->email ?>">
						                    </div>
						                </div>
						                <div class="col-12">
						                    <div class="form-group">
						                        <label class="form-control-label col-form-label-sm">Address <span class="red-asterisk">*</span></label>
						                        <input class="form-control form-control-sm required_fields form-state" type="text" name="entry-address" value="<?= $sys_shop_details->address ?>">
						                    </div>
						                </div>
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <label class="form-control-label col-form-label-sm">Region <span class="red-asterisk">*</span></label>
						                        <select class="select2 form-control form-control-sm required_fields form-state" name="entry-shop_region" data-reqselect2="yes">
						                            <option value="">Select Region</option>
						                            <?php foreach($region as $row){ ?>
						                                    <?php if($sys_shop_details->shop_region == $row->regCode){ ?>
						                                            <option value="<?= $row->regCode ?>" data-regcode="<?= $row->regCode ?>" selected><?= $row->regDesc ?></option>
						                                    <?php }else{ ?>
						                                            <option value="<?= $row->regCode ?>"  data-regcode="<?= $row->regCode ?>"><?= $row->regDesc ?></option>
						                                    <?php } ?>
						                            <?php } ?>
						                        </select>
						                    </div>
						                </div>
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <input type="hidden" id="city_hidden" value="<?= $sys_shop_details->shop_city ?>">
						                        <label class="form-control-label col-form-label-sm">City <span class="red-asterisk">*</span></label>
						                        <select class="select2 form-control form-control-sm required_fields form-state" name="entry-shop_city" data-reqselect2="yes">
						                            <option value="">Select City</option>
						                        </select>
						                    </div>
						                </div>
						            </div>
						        </div>
						        <div class="col-12">
						            <hr>
						        </div>
						        <div class="col-12">
						            <h3>Bank Details</h3>
						            <div class="row">
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <label for="entry-bankname" class="control-label">Bank Name <span class="red-asterisk">*</span></label>
						                        <input type="text" class="form-control required_fields" name="entry-bankname" id="entry-bankname" value="<?= $sys_shop_details->bankname ?>">
						                    </div>
						                </div>
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <label for="entry-acctname" class="control-label">Account Name <span class="red-asterisk">*</span></label>
						                        <input type="text" class="form-control required_fields" name="entry-acctname" id="entry-acctname" value="<?= $sys_shop_details->accountname ?>">
						                    </div>
						                </div>
						                <div class="col-md-6">
						                    <div class="form-group">
						                        <label for="entry-acctno" class="control-label">Account Number <span class="red-asterisk">*</span></label>
						                        <input type="text" class="form-control required_fields" name="entry-acctno" id="entry-acctno" value="<?= $sys_shop_details->accountno ?>">
						                    </div>
						                </div>
						                <div class="col-12">
						                    <div class="form-group">
						                        <label for="entry-desc" class="control-label">Description <span class="red-asterisk">*</span></label>
						                        <textarea class="form-control required_fields" name="entry-desc" id="entry-desc" row="2"><?= $sys_shop_details->description ?></textarea>
						                    </div>
						                </div>
						            </div>
						        </div>
						    </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </section>