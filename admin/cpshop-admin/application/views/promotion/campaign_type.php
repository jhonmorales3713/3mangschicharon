<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<style>
    .required {
    color: red;
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
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/promotion_home/'.$token);?>">Promotion</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Campaign Type</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Campaign Type</h3>
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
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Category Code</label> -->
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Campaign Name">
                                </div>
                            </div>
                            <div class="col-md-9 col-lg-9">
                                <div class="form-group">
                                    <button class="btn btn-primary btnSearch pull-right" style="float: right;" id="feat_campaign" data-toggle="modal" data-target="#featured_modal">Set Featured Campaign</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    
                    <div class="col-md-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php if ($this->loginstate->get_access()['campaign_type']['create'] == 1){ ?>
                                    <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger mb-3" id="action_add">Add</button>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('promotion/Main_promotion/export_campaign_type_list')?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <input type="hidden" name="_filter" id="_filter">
                                        <!-- <input type="hidden" name="_data" id="_data"> -->
                                        <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                    </form>  
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
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Campaign Name</th>
                                <th>Shouldered by</th>
                                <th>Actions</th>
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
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Campaign Type</h3>
            </div>
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-body">
                <div id="img-upload-update">
                    <!-- <div class="form-group">
                        <div class="category_img square" id="imgthumbnail-logo"></div>
                        <div class="img_preview_container_update square" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Image</label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file_container_update" id="file_container_update">
                                <label class="custom-file-label" id="file_description_update">Choose file</label>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Campaign Name<span class="required">*</span></label>
                    <input type="text" id="edit_name" name="edit_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Campaign Name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary updateBtn" id="update_modal_confirm_btn">Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Campaign Type</h3>
            </div>
            <div class="modal-body">
                <div id="img-upload-add">
                    <!-- <div class="form-group">
                        <div class="square imgthumbnail-logo" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/placeholder-any.jpg') ?>" style="max-width: 100%;max-height: 100%; "></div>
                        <div class="img_preview_container square" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Image</label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file_container" id="file_container">
                                <label class="custom-file-label" id="file_description">Choose file</label>
                            </div>
                        </div>
                    </div> -->
                </div>
                
                <div class="form-group">
                    <label class="form-control-label col-form-label-sm">Campaign Name<span class="required">*</span></label>
                    <input type="text" id="add_name" name="add_name" class="form-control material_josh form-control-sm search-input-text" placeholder="Campaign Name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn" id="add_modal_confirm_btn">Add</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="setFeadutedModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Set to All</h3>
            </div>
            <div class="modal-body">
            <input  type="hidden" id="id" placeholder=""> 
            <p>Are you sure you want to set to all?</p>
             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveFeatureConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unsetFeadutedModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Unset to all</h3>
            </div>
            <div class="modal-body">
               <input  type="hidden" id="id" placeholder=""> 
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
            <h3 class="modal-title" id="exampleModalLabel">Loss of promotion</h3>
            </div>
            <div class="modal-body">
            <input  type="hidden" id="id" placeholder=""> 
            <p>Are you sure you want to cater the loss of promotion to Merchant?</p>
             
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
            <h3 class="modal-title" id="exampleModalLabel">Loss of promotion</h3>
            </div>
            <div class="modal-body">
               <input  type="hidden" id="id" placeholder=""> 
                <p>Are you sure you want to cater the loss of promotion to Toktokmall?</p>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="unsaveFeatureConfirmPromo" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-sm" id="featured_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="modal-title">Set Featured Campaign</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="campaign_type">Choose Campaign Type:</label>
                    <select name="campaign_type_div" id="campaign_type_div" class="form-control">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirm_feature">Confirm</button>
            </div>
        </div>
    </div>
</div>


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/promotion/campaign_type.js');?>"></script>
<!-- end - load the footer here and some specific js -->

 