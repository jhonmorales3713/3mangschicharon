<style>
    .datepicker {
        z-index:1001 !important;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="col-12 alert_div">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_reports/reports_home/Reports');?>"><?=$active_page?></a></span>
    </div>
</div>
<div class="col-12">
    <section class="tables  ml-4">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Inventory Report</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg text-white" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row">
                        
                            <div class="search-filter col-lg-3 col-xs-12 col-md-6 col-sm-7 ">
                                Date From
                                <input type="text" placeholder="MM/DD/YYYY" autocomplete="false" class="form-control datetimepicker-input date_from" value="<?=today_date();?>"  id="date_from" data-toggle="datetimepicker" data-target="#datepicker"/>
                                
                                            <!--                                 
                                <div class="input-daterange input-group datetimepicker" data-date-end-date="0d">



                                    <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY" autocomplete="false">
                                    <span class="input-group-addon">&nbsp;to&nbsp;</span>
                                    <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" autocomplete="false">
                                    <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                                -->
                            </div>
                            <!-- <span class="input-group-addon">&nbsp;to&nbsp;</span> -->
                            
                            <div class="search-filter col-lg-3 col-xs-12 col-md-6 col-sm-7">
                                Date To
                                <input type="text" placeholder="MM/DD/YYYY" autocomplete="false" class="form-control datetimepicker-input date_to" value="<?=today_date();?>"  id="date_to" data-toggle="datetimepicker" data-target="#datepicker"/>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                Category
                                <select class="form-control select2" id="category" name="category">
                                    <option value="">All Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?=$category['id'];?>"><?=$category['category_name'];?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-12 col-lg-auto table-search-container text-right d-flex justify-content-end">
                        <div class="row no-gutters ">
                            <div class="col-12 col-md-3 px-1 mb-3">
                                <form action="<?php echo base_url('admin/Main_reports/export_inventory_report_table');?>" method="post" target="_blank">
                                    <input type="hidden" name="_record_status" id="_record_status">
                                    <input type="hidden" name="_date_from" id="_date_from">
                                    <input type="hidden" name="_date_to" id="_date_to">
                                    <input type="hidden" name="_payment_type" id="_payment_type">
                                    <input type="hidden" name="_search" id="_search">
                                    <input type="hidden" name="_city" id="_city">
                                    <button class="btn-mobile-w-100 btn btn-primary btnExport mr-3" type="submit" id="btnExport" style="display:none">Export to XLSX</button>
                                </form>
                            </div>
                            <div class="col-12 col-md-3 px-1 mb-3">
                                <form action="<?php echo base_url('admin/Main_reports/inventory_report_pdf');?>" method="post" target="_blank">
                                    <input type="hidden" name="_record_status" id="_record_status">
                                    <input type="hidden" name="_date_from" id="_date_from">
                                    <input type="hidden" name="_date_to" id="_date_to">
                                    <input type="hidden" name="_payment_type" id="_payment_type">
                                    <input type="hidden" name="_search" id="_search">
                                    <input type="hidden" name="_city" id="_city">
                                    <button class="btn-mobile-w-100 btn btn-primary btnExport mr-3" type="submit" id="btnExport" style="display:none">Export To PDF</button>
                                </form>
                            </div>
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="search" id="search">
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100 btn btn-primary btnSearch btn-block w-100" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tableDiv" >
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%;" id="table-grid-inventory"  cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Available Qty</th>
                                    <th>Date Manufactured</th>
                                    <th>Date Expiration</th>
                                    <th>Deducted Qty</th>
                                    <th>Total Qty</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="row mb-5">
                        <div class="col-2">
                            <span class="bg-danger p-1 mr-1" style="width:100px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</span>Expired Stocks
                        </div>
                        <div class="col-2">
                            <span class="bg-warning p-1 mr-1" style="width:100px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</span>Expiring soon
                        </div>
                        <div class="col-2">
                            <span class="bg-secondary p-1 mr-1" style="width:100px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</span>Out of Stocks
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<input type="hidden" id="order_status_view" value="all">
<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/reports/inventory_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->

