<link rel="stylesheet" href="<?= base_url('assets/css/select2required.css'); ?>">
<style>
    .required {
        color: red;
    }

    .datepicker {
        z-index: 999999 !important;
    }

    /*toggle styles*/
    .switch {
      position: relative;
      display: inline-block;
      width: 90px;
      height: 34px;
    }

    .switch input {display:none;}

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: var(--primary-color) !important
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(55px);
      -ms-transform: translateX(55px);
      transform: translateX(55px);
    }

    /*------ ADDED CSS ---------*/
    .on
    {
      display: none;
    }

    .on
    {
      color: white;
      position: absolute;
      transform: translate(-50%,-50%);
      top: 50%;
      left: 35%;
      font-size: 10px;
      font-family: Verdana, sans-serif;
    }
    .off
    {
      color: white;
      position: absolute;
      transform: translate(-50%,-50%);
      top: 50%;
      left: 65%;
      font-size: 10px;
      font-family: Verdana, sans-serif;
    }

    input:checked+ .slider .on
    {display: block;}

    input:checked + .slider .off
    {display: none;}

    /*--------- END --------*/

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;}
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?= $main_nav_id; ?>" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?= base_url('Main_page/display_page/promotion_home/' . $token); ?>">Promotion</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shipping Fee Discount</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Shipping Fee Discount</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Category Code</label> -->
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Shipping Fee Promotion Name">

                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <select name="select_shop" id="select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <?php

                                        $shopid = $this->session->userdata('sys_shop_id');

                                        if ($shopid != 0) {

                                            foreach ($shops as $shop) {

                                                if ($shop['id'] == $shopid) {
                                        ?>
                                                    <option selected value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                                <?php
                                                }
                                            }
                                        } else {
                                    ?>

                                        <option value="0">All Shops</option>
                                    <?php

                                            foreach ($shops as $shop) {
                                                ?>
                                                <option value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                                ?>
                                        <?php

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="card-body table-body">

                    <div class="col-md-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php if ($this->loginstate->get_access()['sf_discount']['create'] == 1) { ?>
                                    <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger mb-3" id="action_add">Add</button>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <!-- <form action="<?= base_url('promotion/Main_promotion/export_campaign_type_list') ?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <input type="hidden" name="_filter" id="_filter">
                                        <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                    </form>   -->
                                    <div class="col-6 col-md-auto px-1">
                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>

                                    <div class="col-6 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-list" cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Shipping Fee Promotion Name</th>
                                <th>Shipping Fee Condition</th>
                                <th>Status</th>
                                <th>Promotion Period</th>
                                <th>Shouldered By</th>
                                <th width="30">Actions</th>
                                <th>On Top</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal-->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <small>This action cannot be undone.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Shipping Fee Discount</h3>
            </div>
            <form id="form_promoprod_edit" enctype="multipart/form-data" method="post">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Shipping Fee Information</label>
                    </div>
                    <div class="row pl-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shipping Fee Discount Name<span class="required">*</span></label>
                                <input type="text" id="edit_sfd_name" name="edit_sfd_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Shipping Fee Discount Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shipping Fee Code</label>
                                <input type="text" id="edit_sfd_code" name="edit_sfd_code" class="form-control material_josh form-control-sm search-input-text" maxlength="20" placeholder="Shipping Fee Code" onkeyup="this.value = this.value.toUpperCase();">
                                <div class="form-check">
                                    <input class="form-check-input edit_require_code" type="checkbox" value="1" name="edit_require_code" id="edit_require_code">
                                    <label class="form-check-label" for="edit_require_code">
                                        Require Shipping Fee Code
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: -.5em;">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shop Name<span class="required">*</span></label>
                                <select name="edit_select_shop" id="edit_select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <?php

                                    $shopid = $this->session->userdata('sys_shop_id');

                                    if ($shopid != 0) {

                                        foreach ($shops as $shop) {

                                            if ($shop['id'] == $shopid) {
                                    ?>
                                                <option selected value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                            <?php
                                            }
                                        }
                                    } else {
                                    ?>

                                        <option value="0">All Shops</option>
                                    <?php
                                        foreach ($shops as $shop) {
                                            ?>
                                            <option value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                            ?>
                                    <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                            <label class="form-control-label col-form-label-sm">Usage Limit<span class="required">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input edit_limit" type="checkbox" value="1" name="edit_limit" id="edit_limit">
                                    <label class="form-check-label" for="limit2">
                                        Limit number of times this discount can be used in total
                                    </label>
                                </div>
                                <div id="edit_showLimitnum" style="display: none;">
                                    <input type="number" id="edit_usage_qty" name="edit_usage_qty" class="form-control material_josh form-control-sm search-input-text" min="1" placeholder="Usage Limit">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input edit_limit" type="checkbox" value="1" name="edit_limit_times" id="edit_limit_times">
                                    <label class="form-check-label" for="edit_limit_times">
                                        Limit to one use per customer
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Start Date<span class="required">*</span></label>
                                <input type="date" class="form-control" style="z-index: 2 !important; text-align:center;" min="<?php echo today(); ?>" id="edit_date_from" name="edit_date_from" placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Start Time<span class="required">*</span></label>
                                <input type="time" class="form-control" style="z-index: 2 !important; text-align:center;" id="edit_time_from" name="edit_time_from">
                            </div>
                        </div>
                        <div class="col-md-6 edit_showEndDate" style="display: none;">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">End Date</label>
                                <input type="date" class="form-control" style="z-index: 2 !important; text-align:center;" id="edit_date_to" min="<?php echo today(); ?>" name="edit_date_to" placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6 edit_showEndDate" style="display: none;">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">End Time</label>
                                <input type="time" class="form-control" style="z-index: 2 !important; text-align:center;" id="edit_time_to" name="edit_time_to">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_setEndDate" value="1" name="edit_setEndDate">
                                <label class="form-check-label" for="edit_setEndDate">
                                    Set End Date
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Region<span class="required">*</span></label>
                                <select class="select2 form-control form-control-sm form-state taginput-field" id="edit_select_region" name="edit_select_region[]" multiple="multiple">
                                    <?php select_region_list($region) ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Shipping Fee Condition</label>
                    </div>
                    <div class="row pl-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="radio" class="edit-requirement-button" name="edit_requirement" value="0" /> No Requirement &nbsp;&nbsp;
                                <input type="radio" class="edit-requirement-button" name="edit_requirement" value="1" id="edit_requirement" /> With Requirement
                            </div>
                        </div>
                    </div>
                    <div class="row pl-3 edit_showSFReq" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-edit" cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 300px;">Minimum Cart Amount</th>
                                            <th style="width: 300px;">Shipping Fee</th>
                                            <th style="width: 200px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <div style="float: left;">
                                    <button type="button" class="btn btn-primary pull-right edit-add-record" id="btnTierEdit" data-added="0"><i class="fa fa-plus"></i> Add Tier</button>
                                </div>
                                <div style="float: left;">
                                    </br>
                                    <p class="error_tier_edit" style="color:red"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary saveBtn" id="editSfd">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Shipping Fee Discount</h3>
            </div>
            <form id="form_promoprod" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Shipping Fee Information</label>
                    </div>
                    <div class="row pl-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shipping Fee Discount Name&nbsp;<span class="required">*</span></label>
                                <input type="text" id="sfd_name" name="sfd_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Shipping Fee Discount Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shipping Fee Code</label>
                                <input type="text" id="sfd_code" name="sfd_code" style="display: none;" class="form-control material_josh form-control-sm search-input-text" maxlength="20" placeholder="Shipping Fee Code" onkeyup="this.value = this.value.toUpperCase();">
                                <div class="form-check">
                                    <input class="form-check-input require-code" type="checkbox" value="1" name="require_code" id="require_code">
                                    <label class="form-check-label" for="require_code">
                                        Require Shipping Fee Code
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: -.5em;">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Applies To<span class="required">*</span></label>
                                <select name="select_shop" id="select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    
                                    <?php

                                    $shopid = $this->session->userdata('sys_shop_id');

                                    if ($shopid != 0) {

                                        foreach ($shops as $shop) {

                                            if ($shop['id'] == $shopid) {
                                    ?> 
                                                <option selected value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                            <?php
                                            }
                                        }
                                    } else {
                                    ?>

                                        <option value="0">All Shops</option>
                                    <?php

                                        foreach ($shops as $shop) {
                                            ?>
                                            <option value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                            ?>
                                    <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Usage Limit<span class="required">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input limit" type="checkbox" value="1" name="limit" id="limit" checked>
                                    <label class="form-check-label" for="limit_times">
                                        Limit number of times this discount can be used in total
                                    </label>
                                </div>
                                <div id="showLimitnum">
                                    <input type="number" id="usage_qty" name="usage_qty" class="form-control material_josh form-control-sm search-input-text" min="1" placeholder="Usage Limit">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input limit" type="checkbox" value="1" name="limit_times" id="limit_times">
                                    <label class="form-check-label" for="limit_times2">
                                        Limit to one use per customer
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Start Date<span class="required">*</span></label>
                                <input type="date" class="form-control" style="z-index: 2 !important; text-align:center;" min="<?php echo today(); ?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Start Time<span class="required">*</span></label>
                                <input type="time" class="form-control" style="z-index: 2 !important; text-align:center;" id="time_from" name="time_from">
                            </div>
                        </div>
                        <div class="col-md-6 showEndDate" style="display: none;">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">End Date</label>
                                <input type="date" class="form-control" style="z-index: 2 !important; text-align:center;" min="<?php echo today(); ?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6 showEndDate" style="display: none;">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">End Time</label>
                                <input type="time" class="form-control" style="z-index: 2 !important; text-align:center;" id="time_to" name="time_to">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="setEndDate" value="1" name="setEndDate">
                                <label class="form-check-label" for="setEndDate">
                                    Set End Date
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Region<span class="required">*</span></label>
                                <select class="select2 form-control form-control-sm form-state taginput-field" id="select_region" name="select_region[]" multiple="multiple">
                                    <?php select_region_list($region) ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shouldered By: <span class="required">*</span></label>
                                <select class="form-control form-control-sm form-state taginput-field" id="shouldered_by" name="shouldered_by">
                                    <option value="0">Toktokmall</option>
                                    <option value="1">Merchant</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ml-3">
                        <label class="control-label font-weight-bold">Shipping Fee Condition</label>
                    </div>
                    <div class="row pl-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="radio" class="requirement-button" checked="checked" name="requirement" value="0" /> No Requirement &nbsp;&nbsp;
                                <input type="radio" class="requirement-button" name="requirement" value="1" id="requirement" /> With Requirement
                            </div>
                        </div>
                    </div>
                    <div class="row pl-3 showSFReq" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid" cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 300px;">Minimum Cart Amount</th>
                                            <th style="width: 300px;">Shipping Fee Discount</th>
                                            <th style="width: 200px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <div style="float: left;">
                                    <button type="button" class="btn btn-primary pull-right add-record" id="btnTier" data-added="0"><i class="fa fa-plus"></i> Add Tier</button>
                                </div>
                                <div style="float: left;">
                                    </br>
                                    <p class="error_tier" style="color:red"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary saveBtn" id="submitSfd">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="setFeadutedModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel"> Set to All</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id" placeholder="">
                <p>Are you sure you want to set to all?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveFeatureConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unsetFeadutedModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel"> Unset to all</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id" placeholder="">
                <p>Are you sure you want to unset to all?</p>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="unsaveFeatureConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="setFeadutedModalPromo" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel">Shouldered By</h3>
            </div>
            <div class="modal-body">
            <input  type="hidden" id="id" placeholder=""> 
            <p>Are you sure you want to change shouldered by ?</p>
             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveFeatureConfirmPromo" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unsetFeadutedModalPromo" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel">Shouldered By</h3>
            </div>
            <div class="modal-body">
               <input  type="hidden" id="id" placeholder=""> 
                <p>Are you sure you want to change shouldered by ?</p>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="unsaveFeatureConfirmPromo" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer'); ?>
<!-- includes your footer -->
<script type="text/javascript" src="<?= base_url('assets/js/promotion/sf_discount.js'); ?>"></script>
<!-- end - load the footer here and some specific js -->