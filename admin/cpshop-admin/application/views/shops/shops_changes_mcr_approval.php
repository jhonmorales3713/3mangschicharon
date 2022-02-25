<style>

.modal-title{
    color: black !important;
}

</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Products"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/shops_home/'.$token);?>">Shops</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shop MCR Approval</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            Shop MCR Approval List
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">

                    <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                   <?php if($validation['wfa_view']){?>
                                         <option value="3" >Waiting for a approval</option>
                                   <?php }?>
                                   <?php if($validation['decline_view']){?>
                                          <option value="0">Declined</option>
                                    <?php }?>
                                    <?php if($validation['approve_view']){?>
                                    <option value="2">Approved</option>
                                    <?php }?>
                                    <?php if($validation['verified_view']){?>
                                    <option value="1">Verified</option>
                                    <?php }?>
                                </select> 
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <?php if($shopid == 0){?> 
                                <div class="form-group">
                                    <select name="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            <?php } ?>
                        </div>
                 
                        <!-- start - record status is a default for every table -->
                        <!-- <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                    <option value="">All Records</option>
                                    <option value="1" selected>Active</option>
                                    <option value="2">Inactive</option>
                                </select> 
                            </div>
                        </div>
                        -->
                    </div>
                    </form>
                    
                </div>
                <div class="card-body table-body">

                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('products/Main_products/export_product_table')?>" method="post" target="_blank">
                                            <input type="hidden" name="_shops" id="_shops">
                                            <input type="hidden" name="_name" id="_name">
                                            <input type="hidden" name="_record_status" id="_record_status">
                                            <input type="hidden" name="_search" id="_search">
                                            <!-- <button class="btn-mobile-w-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp; -->
                                        </form>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100 btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br><br> <br>

                    <!-- end - record status is a default for every table -->
                <div class="table-responsive">
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-productss" cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th><input type='checkbox' name="check_all_items" id='check_all_items' /></th>                      
                                <th>Shop Name</th>
                                <th>Merchant Comrate</th>
                                <th>Startup</th>
                                <th>Jc</th>
                                <th>Mcjr</th>
                                <th>Mc</th>
                                <th>Mcsuper</th>
                                <th>Mcmega </th>
                                <th>Others</th>
                                <th>Status</th>
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                        <tbody class="waiting_for_approval_table">


                        </tbody>
                    </table>
                </div>
                <br>
                    <div class="row no-gutters  pull-right">
                        <div class="col-6 col-md-auto px-1 mb-3">
                             <?php if($validation['verify']){?>
                                     <button class="btn btn-success ApproveAll_Verify BatchVerify"  style="display: none;" type="button" id="ApproveAll_Verify"> Batch Verify</button>
                              <?php } ?>
                              <?php if($validation['approve']){?>
                                   <button class="btn btn-success ApproveAll BatchApprove" style="display: none;"  type="button" id="ApproveAll"> Batch Approve</button>
                              <?php } ?>
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3">
                             <?php if($validation['decline']){?>
                                   <button class="btn btn-danger DeclineALL_Verify BatchVerify"  style="display: none;"  type="button" id="DeclineALL_Verify">Batch Decline</button>
                            <?php } ?>
                            <?php if($validation['decline']){?>
                                  <button class="btn btn-danger DeclineALL BatchApprove" style="display: none;"  type="button" id="DeclineALL">Batch Decline</button>
                            <?php } ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Approve Modal-->
<div id="approveModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Approve</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Shop changes will be successfully Approved.</label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-prod_id="">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Decline Modal-->
<div id="declineModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Decline</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Shop  changes will be declined.</label>
                    </div>
                </div>
               
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" id="declineButton" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedDecBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-id="">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>



<!-- Verify Modal-->
<div id="VerifyModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Verifiy</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Shop changes will be successfully Verified.</label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedBtnVerify" class="btn btn-success waves-effect waves-light" aria-label="Close" data-prod_id="">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>



<input type="hidden" id="md5" value="<?= en_dec('en', $shopid) ?>">  
<input type="hidden" id="token" value="<?=$token?>">

<input type="hidden" id="verify" value="<?=$validation['verify']?>">
<input type="hidden" id="approve" value="<?= $validation['approve'] ?>">
<input type="hidden" id="decline" value="<?= $validation['decline'] ?>">
<input type="hidden" id="edit" value="<?= $validation['edit'] ?>">



<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/shops/shops_changes_mcr_approval.js');?>"></script>
<!-- end - load the footer here and some specific js -->

