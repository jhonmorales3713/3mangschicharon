<style>
    .datepicker {
        z-index:1001 !important;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Products"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">toktok Booking Report</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">toktok Booking Report</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                    
                        <div class="search-filter col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0">
                            <div class="input-daterange input-group datetimepicker" data-date-end-date="0d">
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY" autocomplete="false">
                                <span class="input-group-addon">&nbsp;to&nbsp;</span>
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" autocomplete="false">
                                <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                            </div>
                            <div class="my-2 my-md-0 text-xs font-semibold text-orange-400 pt-2"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;You cannot pick more than 3 months of data. Pick End Date First.</div>
                        </div>

                        <div class="col-md-3 mb-3" style="display:none;">
                            <select class="form-control" id="select_status">
                                <option value="1" selected>Paid</option>
                            </select>
                        </div>
                        
                        <div class="col-lg-3 addressdiv" style="display:none;">
                            <input type="text" class="form-control" id="address" placeholder="Address">
                        </div>

                        <div class="col-lg-3 regiondiv" style="display:none;">
                            <select class="form-control select2" id="regCode">
                                <?php foreach($regions as $region){?>
                                    <option value="<?=$region['regCode']?>"><?=$region['regDesc']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-3 provincediv" style="display:none;">
                            <select class="form-control select2" id="provCode">
                                <?php foreach($provinces as $province){?>
                                    <option value="<?=$province['provCode']?>"><?=$province['provDesc']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-3 citymundiv" style="display:none;">
                            <select class="form-control select2" id="citymunCode">
                                <?php foreach($citymuns as $citymun){?>
                                    <option value="<?=$citymun['citymunCode']?>"><?=$citymun['citymunName']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <?php if($shopid == 0){?>
                            <div class="col-md-3 shopdiv">
                                <div class="form-group">
                                    <select name="_shops" id="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="0">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php } else{?>
                            <div class="col-md-3 shopdiv">
                                <div class="form-group">
                                    <select name="_shops" id="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="<?=$shopid?>" selected></option>
                                        
                                    </select>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-md-3" style="display:none" id="showBranches">
                            <div class="form-group">
                                <select name="select_branch" id = "select_branch" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="">None</option>
                                    <?php if($branches){?>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?=$branch['branchid'];?>"><?=$branch['branchname'];?></option>
                                        <?php endforeach ?>
                                    <?php }?>
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
                            <form action="<?php echo base_url('order_report/export_toktok_booking_report');?>" method="post" target="_blank">
                                <input type="hidden" name="_record_status_export" id="_record_status_export">
                                <input type="hidden" name="_name_export" id="_name_export">
                                <input type="hidden" name="status_export" id="status_export">
                                <input type="hidden" name="_shops_export" id="_shops_export">
                                <input type="hidden" name="_branch_export" id="_branch_export">
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
                        <div class="col-12 col-md-auto px-1 mb-3">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="_name" id="_name">
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3">
                            <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3">
                            <button class="btn-mobile-w-100 btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                    <!-- start - record status is a default for every table -->
                    <div class="col-md-3" hidden>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6" style="padding:0px;">
                                    <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                        <option value="">All Records</option>
                                        <option value="1" selected>Enabled</option>
                                        <option value="2">Disabled</option>
                                    </select> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-order"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Ordered Date</th>
                                <th width="150">Reference Number</th>
                                <th>Rider Name</th>
                                <th>Contact No.</th>
                                <th>Plate Number</th>
                                <th>Delivery Amount</th>
                                <th>Shop</th>
                                <th>Branch</th>
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
<input type="hidden" id="order_status_view" value="all">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/reports/toktok_booking_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->

