<style>
    .datepicker {
        z-index:1001 !important;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="100" data-namecollapse="" data-labelname="Notification"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item active">Notifications</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Notifications</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1" hidden>
                    <form id="form_search">
                    <div class="row">
                        <div class="search-filter col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0">
                            <div class="input-daterange input-group datetimepicker" data-date-end-date="0d">
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY" autocomplete="false">
                                <span class="input-group-addon">&nbsp;to&nbsp;</span>
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" autocomplete="false">
                                <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                            </div>
                        </div>

                        <?php if($shopid == 0){?>
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <select name="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div> -->
                        <?php } ?>
                    </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3" hidden>
                                <form action="<?php echo base_url('orders/main_orders/export_orders');?>" method="post" target="_blank">
                                    <input type="hidden" name="_record_status_export" id="_record_status_export">
                                    <input type="hidden" name="_name_export" id="_name_export">
                                    <input type="hidden" name="status_export" id="status_export">
                                    <input type="hidden" name="_shops_export" id="_shops_export">
                                    <input type="hidden" name="date_from_export" id="date_from_export">
                                    <input type="hidden" name="date_to_export" id="date_to_export">
                                    <input type="hidden" name="location_export" id="location_export">
                                    <input type="hidden" name="address_export" id="address_export">
                                    <input type="hidden" name="regCode_export" id="regCode_export">
                                    <input type="hidden" name="provCode_export" id="provCode_export">
                                    <input type="hidden" name="citymunCode_export" id="citymunCode_export">
                                    <input type="hidden" name="drno_export" id="drno_export">
                                    <input type="hidden" name="forpickup_export" id="forpickup_export">
                                    <input type="hidden" name="request_filter" id="request_filter">
                                    <button class="btn-mobile-w-100 btn btn-primary btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                                </form>
                            </div>
                        <div class="col-12 col-md-auto px-1 mb-3" hidden>
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="_name" id="_name">
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3"hidden>
                            <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3" hidden>
                            <button class="btn-mobile-w-100 btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                        <?php if($shopid == 0){?>
                            <div class="col-6 col-md-auto px-1 mb-3">
                                <select name="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="">All Shops</option>
                                    <?php foreach ($shops as $shop): ?>
                                        <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-notif"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th width="150">Shop</th>
                                <th width="150">Activity</th>
                                <th>Activity Details</th>
                                <th>Module</th>
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal-->
<div class="modal fade" id="notifcationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md   ">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">View Notification</h3>
            </div>
            <div class="modal-body">
                <strong id="notif_activity_details"></strong>
                <p>&nbsp;</p>
                <p id="notif_message"></p>
                <u><a href="#" id="notif_link" target="_blank" style="color:blue;">View</a></u>
                <hr>
                <small id="notif_date_created"></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnCloseModal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- hidden fields -->
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/notification/notifications.js');?>"></script>
<!-- end - load the footer here and some specific js -->

