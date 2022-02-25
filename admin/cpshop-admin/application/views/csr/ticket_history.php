<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="CSR Section"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/csr_section_home/'.$token);?>">Support</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?= $breadcrumbs ?></li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0"><?= $breadcrumbs ?></h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" value="<?=today_text();?>" name="start" readonly/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" value="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" id="date_to" name="end" readonly/>    
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Ticket Reference No.</label> -->
                                <input type="text" name="ticket_refno" class="form-control material_josh form-control-sm search-input-text url enter_search" placeholder="Ticket Refno">
                            </div>
                        </div>
                        <!-- start - record status is a default for every table -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                    <option value="">All Records</option>
                                    <option value="1" selected>Enabled</option>
                                    <option value="2">Archived</option>
                                    <option value="3">Pending</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    </form>
                    <!-- <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->
                    
                </div>
                <div class="card-body table-body">
                    <!-- <?php if ($this->loginstate->get_access()['ticket_history']['create'] == 1){ ?>
                        <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                            <a href="<?= base_url('Csr/ticketing/'.$token) ?>" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary">Add</a>
                        </div>
                    <?php } ?> -->
                    <div class="col-lg-auto table-search-container">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                            <?php if ($this->loginstate->get_access()['ticket_history']['create'] == 1){ ?>
                                <a href="<?= base_url('Csr/ticketing/'.$token) ?>" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger">Add</a>
                            <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-6 col-md-auto px-1">
                                        <form action="<?=base_url('csr/Csr/export_ticket_list')?>" method="post" target="_blank">
                                            <input type="hidden" name="_search" id="_search">
                                            <input type="hidden" name="_filters" id="_filters">
                                            <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                        </form>
                                    </div>
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
                                <th>Ticket No.</th>
                                <th>Subject</th>
                                <th>Customer</th>
                                <th>Category</th>
                                <th>Status</th>
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
                    <h3 class="modal-title" id="exampleModalLabel">Archive Confirmation</h3>
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

    <div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Approve Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this ticket?</p>
                    <small>This action cannot be undone.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="approve_modal_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reject_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Reject Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this ticket?</p>
                    <small>This action cannot be undone.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="reject_modal_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/csr/ticket_history.js');?>"></script>
<!-- end - load the footer here and some specific js -->