<?php ini_set('memory_limit', '1024M');?>
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="General List"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>

            <?php if($sys_shop == 0){?>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Settings_shipping_delivery/shipping_delivery/'.$token);?>">Shipping and Delivery</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Settings_shipping_delivery/custom_list/'.$token);?>">Custom Shop List</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Settings_shipping_delivery/custom_profile_list/'.$token.'/'.$shop_id_md5);?>">Profile List</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php }else{?>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Settings_shipping_delivery/shipping_delivery/'.$token);?>">Shipping and Delivery</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Settings_shipping_delivery/custom_profile_list/'.$token.'/'.$shop_id_md5);?>">Profile List</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php }?>
            
            <li class="breadcrumb-item active">Custom Shipping Rates</li>
        </ol>
    </div>

    <?php 
        $shipping_access_create = $this->loginstate->get_access()['custom_shipping']['create'];
        $shipping_access_update = $this->loginstate->get_access()['custom_shipping']['update'];
        $shipping_access_delete = $this->loginstate->get_access()['custom_shipping']['delete'];
    ?>

    <input type="hidden" id="shipping_access_create" value="<?=$shipping_access_create;?>">
    <input type="hidden" id="shipping_access_update" value="<?=$shipping_access_update;?>">
    <input type="hidden" id="shipping_access_delete" value="<?=$shipping_access_delete;?>">

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Products - <?=$shop_name?></h3>
                    </div>
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="profile_name" class="control-label">Profile Name</label>
                                <input type="text" class="form-control" name="profile_name" id="profile_name" >
                            </div>
                        </div>
                        <hr>
                        <?php if($shipping_access_create == 1){?>
                            <a href="" class="btn btn-outline-danger" data-toggle="modal" data-target="#addProductModal" id="addProductsBtn" style="float:right;">Add Products</a>
                        <?php }?>
                        <br><br>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" style="width:70%;"><b>Item Name</b></th>
                                    <th scope="col" style="width:20%;"><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody id="tbody_products" class="tbody_products">
                            <tr>
                                <th></th>
                                <th style="text-align: center;">No products</th>
                                <th></th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Shipping to</h3>
                    </div>
                    <div class="card-body">
                            <?php if($shipping_access_create == 1){?>
                                <a href="" class="btn btn-outline-danger" data-toggle="modal" data-target="#shippingZoneModal" style="float:right;">Create Shipping Zone</a>
                            <?php }?>
                            <br><br><br><br><br>
                            <div id="zoneDiv">
                                <center><label>No zones or rates</label></center> 
                            </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php if($this->loginstate->get_access()['custom_shipping']['update'] == 1 || $this->loginstate->get_access()['custom_shipping']['create'] == 1){ ?>
        <div class="col-lg-12">&nbsp;</div>
        <div class="footer">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary  waves-effect waves-light" data-toggle="modal" data-target="#ConfirmModal" id="confirmBtn" disabled>Save</button>
            </div>
        </div>
    <?php } ?>

    <!-- add product modal -->
    <div id="addProductModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header secondary-bg">
                    <div class="col-md-12">
                        <h4 id="exampleModalLabel" class="modal-title">Add product</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="provCode" class="control-label">Product</label>
                                    <select class="form-control select2" name="products" id="products">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-outline-secondary cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Close</button>
                        <button type="button" class="btn btn-primary  waves-effect waves-light" id="addProductBtn" >Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- shipping zone modal -->
    <div id="shippingZoneModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header secondary-bg">
                    <div class="col-md-12">
                        <h4 id="exampleModalLabel" class="modal-title">Create Shipping Zone</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="regCode" class="control-label">Zone Name<span style="color:red">*</span></label>
                                    <input type="text" class="form-control" name="zone_name" id="zone_name">
                                    <input type="hidden" class="hidden" name="zone_checker" id="zone_checker" value="add">
                                    <input type="hidden" class="hidden" name="zone_f_key" id="zone_f_key">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="regCode" class="control-label">Region</label>
                                    <select class="select2 form-control form-control-sm form-state taginput-field" name="regCode" id="regCode" multiple="multiple">
                                        <?php foreach($get_region as $region){ ?>
                                            <option value="<?=$region['regCode']?>"  data-provcode="0" data-provdesc="" data-citymuncode="0" data-citymundesc=""><?=$region['regDesc']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="provCode" class="control-label">Province</label>
                                    <select class="select2 form-control form-control-sm form-state taginput-field" name="provCode" id="provCode" multiple="multiple">
                                        <?php foreach($get_province as $province){ ?>
                                            <option value="<?=$province['provCode']?>" data-regcode="<?=$province['regCode']?>" data-regdesc="<?=$province['regDesc']?>" data-citymuncode="0" data-citymundesc=""><?=$province['provDesc']?> - <?=$province['regDesc']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="citymunCode" class="control-label">City/Municipality</label>
                                    <select class="select2 form-control form-control-sm form-state taginput-field" name="citymunCode" id="citymunCode" multiple="multiple">
                                        <?php foreach($get_citymun as $citymun){ ?>
                                            <option value="<?=$citymun['citymunCode']?>" data-regcode="<?=$citymun['regCode']?>" data-regdesc="<?=$citymun['regDesc']?>" data-provcode="<?=$citymun['provCode']?>" data-provDesc="<?=$citymun['provDesc']?>"><?=$citymun['citymunDesc']?> - <?=$citymun['provDesc']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <?php if(!empty($get_branches)){ ?>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="branch_id" class="control-label">Branch</label>
                                        <select class="select2 form-control form-control-sm form-state taginput-field taginput-field" name="branch_id" id="branch_id" multiple="multiple">
                                            <?php foreach($get_branches as $branch){ ?>
                                                <option value="<?=$branch['branch_id']?>"><?=$branch['branchname']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                    <div class="hidden">
                                        <select class="select2 custom-select" name="branch_id" id="branch_id" multiple></select>
                                    </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-outline-secondary cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Close</button>
                        <button type="button" class="btn btn-primary  waves-effect waves-light" id="addZoneBtn" >Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- add rate modal -->
    <div id="AddRateModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header secondary-bg">
                    <div class="col-md-12">
                        <h4 id="exampleModalLabel" class="modal-title">Add Rate</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="row">
                            <input type="hidden" class="form-control" name="index_id" id="index_id">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="rate_name" class="control-label">Rate Name<span style="color:red">*</span></label>
                                    <input type="text" class="form-control" name="rate_name" id="rate_name">
                                    <input type="hidden" class="hidden" name="rate_checker" id="rate_checker" value="add">
                                    <input type="hidden" class="hidden" name="rate_p_key" id="rate_p_key">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="regCode" class="control-label">Price <small>(0 if free)<span style="color:red">*</span></small></label>
                                    <input type="number" class="form-control allownumericwithdecimal" name="rate_amount" id="rate_amount">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group" style="padding-left: 10px;">
                                    <label for="is_condition" class="control-label">&nbsp; </label>
                                    <input type="checkbox" class="form-check-input" name="sameday_delivery" id="sameday_delivery">Same-day delivery
                                </div>
                            </div>
                            <div class="col-6 daysshipdiv">
                                <div class="form-group">
                                    <label for="from_day" class="control-label">From (Days to Ship)</label>
                                    <input type="number" class="form-control allownumericwithdecimal" name="from_day" id="from_day" value="0">
                                </div>
                            </div>
                            <div class="col-6 daysshipdiv">
                                <div class="form-group">
                                    <label for="to_day" class="control-label">To (Days to Ship)</label>
                                    <input type="number" class="form-control allownumericwithdecimal" name="to_day" id="to_day" value="0">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <a><small><u id="condition_rate">Add Condition</u></small></a>
                                </div>
                            </div>
                            <div class="col-12 conditiondiv" style="display:none">
                                <div class="form-group" style="padding-left: 10px;">
                                    <form id="conditionForm">
                                        <label for="is_condition" class="control-label">&nbsp; </label>
                                        <input type="radio" class="form-check-input" name="is_condition" id="is_condition" value="1">Based on item weight (grams)<br>
                                        <label for="is_condition" class="control-label">&nbsp; </label>
                                        <input type="radio" class="form-check-input" name="is_condition" id="is_condition" value="2">Based on order price
                                        <input type="radio" class="form-check-input" name="is_condition" id="is_condition" value="0" checked hidden>&nbsp;
                                    </form>
                                </div>
                            </div>
                            <div class="col-6 conditiondiv" style="display:none">
                                <div class="form-group">
                                    <label for="minimum_value" class="control-label minimum_value">Minimum Weight</label><span style="color:red">*</span>
                                    <input type="number" class="form-control allownumericwithdecimal" name="minimum_value" id="minimum_value">
                                </div>
                            </div>
                            <div class="col-6 conditiondiv" style="display:none">
                                <div class="form-group">
                                    <label for="maximum_value" class="control-label maximum_value">Maximum Weight</label>
                                    <input type="number" class="form-control allownumericwithdecimal" name="maximum_value" id="maximum_value">
                                </div>
                            </div>

                            <div class="col-12 additionaldiv" style="display:none">
                                <div class="form-group" style="padding-left: 10px;">
                                    <label for="is_condition" class="control-label">&nbsp; </label>
                                    <input type="checkbox" class="form-check-input" name="additional_isset" id="additional_isset" value="1">Set additional condition
                                    <input type="checkbox" class="form-check-input" name="additional_isset" id="additional_isset" value="0" checked hidden>
                                </div>
                            </div>

                             <div class="additionaldiv2" style="display:none">
                            <div class="row">
                                <div class="col-6 col-sm-5" style="margin-left:10px;">
                                    <label class="">For every succeeding</label>
                                </div>
                                <div class="col-6 col-sm-5" style="margin-left:-30px;">
                                    <input type="text" class="form-control allownumericwithdecimal" name="set_value" id="set_value" placeholder="Ex. 1.00">
                                </div>
                                <div class="col-8 col-sm-3" style="margin-left:-20px;">
                                    <label class="set_value_label">grams,</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">&nbsp;</div>

                        <div class="additionaldiv2" style="display:none">
                            <div class="row">
                                <div class="col-6 col-sm-4" style="margin-left:10px;">
                                    <label class="">add additional</label>
                                </div>
                                <div class="col-6 col-sm-5" style="margin-left:-20px;">
                                    <input type="text" class="form-control allownumericwithdecimal" name="set_amount" id="set_amount" placeholder="Ex. 50.00">
                                </div>
                                <div class="col-8 col-sm-3" style="margin-left:-20px;">
                                    <label>PHP.</label>
                                </div>
                            </div>
                        </div>


                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-outline-secondary cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close" id="closeRateBtn">Close</button>
                        <button type="button" class="btn btn-primary  waves-effect waves-light" id="addRateBtn" >Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- add rate modal -->
    <div id="ConfirmModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header secondary-bg">
                    <div class="col-md-12">
                        <h4 id="exampleModalLabel" class="modal-title">Confirmation</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="regCode" class="control-label">Are you sure you want to save shipping rates?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-outline-secondary cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Close</button>
                        <button type="button" class="btn btn-primary  waves-effect waves-light" id="SaveRateBtn" >Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="DeleteProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Delete Product Confirmation</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Are you sure you want to delete this Product?</label>
                        <input type="hidden" class="hidden" id="delete_id_product" name="delete_id_product">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="DeleteProductConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div id="DeleteZoneModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Delete Zone Confirmation</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Are you sure you want to delete this Zone?</label>
                        <input type="hidden" class="hidden" id="delete_id_zone" name="delete_id_zone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="DeleteZoneConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div id="DeleteRateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Delete Rate Confirmation</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Are you sure you want to delete this Rate?</label>
                        <input type="hidden" class="hidden" id="delete_id_rate" name="delete_id_rate">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="DeleteRateConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>



    <input type="hidden" class="hidden" id="shop_id" value="<?=$shop_id?>">
    <input type="hidden" class="hidden" id="shop_id_md5" value="<?=$shop_id_md5?>">
    <input type="hidden" id="shipping_id" value="<?=$shipping_id?>">
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_custom_rates.js');?>"></script>
<!-- end - load the footer here and some specific js -->
