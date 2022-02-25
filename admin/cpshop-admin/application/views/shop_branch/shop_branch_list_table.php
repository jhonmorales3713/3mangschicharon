<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/shops_home/'.$token);?>">Shops</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shops Branch</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <p class="border-search_hideshow">
                    <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> 
                    <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                    </p>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <?php if ($this->session->userdata('sys_shop') == 0){ ?>
                        <div class="col-md-6 col-lg-3">
                        <?php }else{ ?>
                        <div class="col-md-6 col-lg-3" style="display: none !important;">
                        <?php } ?>
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Main Shop</label> -->
                                <select class="select2 form-control form-control-sm required_fields form-state" name="_mainshop">
                                    <option value="" selected>Select Shop</option>
                                    <?php select_option_obj($mainshop, 'mainshop') ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Branch Name</label> -->
                                <input type="text" name="_branchname" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Branch Name">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">City</label> -->
                                <select class="select2 form-control form-control-sm form-state taginput-field" name="_city">
                                    <option value="" selected>Select City</option>
                                    <?php select_option_obj($city, 'city') ?>
                                </select>
                            </div>
                        </div>
                        <!-- start - record status is a default for every table -->
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                    <option value="">All Records</option>
                                    <option value="1" selected>Enabled</option>
                                    <option value="2">Disabled</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    </form>
                </div>

                <div class="card-body table-body">
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php if($this->loginstate->get_access()['shop_branch']['create'] == 1){ ?>
                                    <a href="<?= base_url('Shopbranch/create/'.$token) ?>" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-primary d-flex align-items-center justify-content-center btn-block">Add</a>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('shop_branch/Shop_branch/export_shopbranch_list')?>" method="post" target="_blank">
                                            <input type="hidden" name="_search" id="_search">
                                            <input type="hidden" name="_filter" id="_filter">
                                            <!-- <input type="hidden" name="_data" id="_data"> -->
                                            <button class="btn-mobile-w-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                        </form>  
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100  btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Main Shop</th>
                                <th>Branch</th>
                                <th>Email</th>
                                <th>Mobile No</th>
                                <th>City</th>
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal-->
    <div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Disable Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                    <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="disable_modal_confirm_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/shop_branch/shop_branch.js');?>"></script>
<!-- end - load the footer here and some specific js -->