<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Products"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
           <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/vouchers_home/'.$token);?>">Vouchers</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Reclaimed Vouchers List</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                           Reclaimed Vouchers List
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                <div class="row">                
                    <div class="col-lg-3">
                            <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" value="<?=today_text();?>" name="start" readonly/>
                                <!-- <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" value="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" id="date_to" name="end" readonly/> -->
                            </div>
                        </div>
                          <br>


                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Shipping Code</label> -->
                                <input type="text" name="_vcode"   id="_vcode" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Voucher Code">
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Shipping Code</label> -->
                                <input type="text" name="_order_ref"   id="_order_ref" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Order Ref #">
                            </div>
                        </div>
                       
            
    
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                    <option value=""selected>All Status</option>
                                    <option value="1" >Approved</option>
                                    <option value="0">Declined</option>
                                </select> 
                            </div>
                        </div>

                      
                    </div>
                    </form>
                    
                </div>
                <div class="card-body table-body">

                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('vouchers/Reclaimed_vouchers/export_reclaimed_voucher_table')?>" method="post" target="_blank">
                                            <input type="hidden" name="date_from_export" id="date_from_export">
                                            <input type="hidden" name="_vcode_export" id="_vcode_export">
                                            <input type="hidden" name="_order_ref_export" id="_order_ref_export">
                                            <input type="hidden" name="_record_status_export" id="_record_status_export">
                                            <button class="btn-mobile-w-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
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

                    
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="reclaimed_order_table"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                               <th>Reclaimed Date</th>
                                <th>Voucher Code</th>
                                <th>Name</th>
                                <th>Order Ref #</th>
                                <th>Order Date</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th width="150">Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/vouchers/reclaimed_vouchers.js');?>"></script>
<!-- end - load the footer here and some specific js -->

