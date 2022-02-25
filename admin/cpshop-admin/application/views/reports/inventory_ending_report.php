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
            <li class="breadcrumb-item active">Inventory Ending Report</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Inventory Ending Report</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                    
                        <div class="search-filter col-lg-3 col-xs-12 col-md-7 col-sm-7 row m-0">
                            <div class="input-daterange input-group datetimepicker" data-date-end-date="0d">
                                <!-- <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY" autocomplete="false">
                                <span class="input-group-addon">&nbsp;to&nbsp;</span> -->
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" autocomplete="false">
                                <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                            </div>
                            <!-- <div class="my-2 my-md-0 text-xs font-semibold text-orange-400 pt-2"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;You cannot pick more than 3 months of data. Pick End Date First.</div> -->
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <select name="_searchproduct" id="_searchproduct" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option selected value="">All products</option>
                                        <option value="withStock">With Stock products</option>
                                        <option value="withoutStock">Out of Stock products</option>
                                    </select>
                                </div>
                            </div>
                        <?php if($shopid == 0){?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="_shops" id="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php } else{ ?>
                            <div class="col-md-3" style="display:none;">
                                <div class="form-group">
                                    <select name="_shops" id="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                            <option value="<?=$shopid;?>" selected></option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if($branchid == 0){?>
                            <?php if($shopid == 0){?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="_branches" id="_branches" class="form-control material_josh form-control-sm search-input-text enter_search">
                                            <option value="" selected>All Branches</option>
                                        </select>
                                    </div>
                                </div>
                            <?php } else{?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="_branches" id="_branches" class="form-control material_josh form-control-sm search-input-text enter_search">
                                            <option value="" selected>All Branch</option>
                                            <option value="0">Main</option>
                                            <?php foreach ($branches as $branch): ?>
                                                <option value="<?=$branch['branchid'];?>"><?=$branch['branchname'];?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            <?php }?>
                        <?php } else{ ?>
                            <div class="col-md-3" style="display:none;">
                                <div class="form-group">
                                    <select name="_branches" id="_branches" class="form-control material_josh form-control-sm search-input-text enter_search">
                                            <option value="<?=$branchid;?>" selected></option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                            <form action="<?php echo base_url('reports/Inventory_report/inventory_ending_export');?>" method="post" target="_blank">
                                <input type="hidden" name="_name_export" id="_name_export">
                                <input type="hidden" name="_shops_export" id="_shops_export">
                                <input type="hidden" name="_searchproduct_export" id="_searchproduct_export">
                                <input type="hidden" name="_branches_export" id="_branches_export">
                                <input type="hidden" name="date_from_export" id="date_from_export">
                                <input type="hidden" name="date_to_export" id="date_to_export">
                                <input type="hidden" name="request_filter" id="request_filter">
                                <button class="btn-mobile-w-100 btn btn-outline-primary btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-auto px-1 mb-3">
                            <input type="text" class="form-control" placeholder="Search Item Name..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="_name" id="_name">
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3">
                            <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3">
                            <button class="btn-mobile-w-100 btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Branch Name</th>
                            <th>Item Name</th>
                            <th width="50">Total Qty</th>
                            <th>Category</th>
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
<script type="text/javascript" src="<?=base_url('assets/js/reports/inventory_ending_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->

