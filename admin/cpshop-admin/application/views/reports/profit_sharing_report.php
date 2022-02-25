<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  /* .datepicker{
    z-index: 999 !important;
  } */

  td, th {
    vertical-align: middle !important;
  }

  .align-right{
    text-align: right;
  }

</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Profit Sharing Report</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card" id = "main">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            Profit Sharing Report
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" value="<?=today_text();?>" name="start" disabled/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" value="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" style="z-index: 2 !important;" id="date_to" name="end" disabled/>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Status</label> -->
                                <select style="height:42px;" type="text" name="reprange" id="reprange" class="form-control" >
                                    <option value="today">Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="last_7">Last 7 Days</option>
                                    <option value="last_30">Last 30 Days</option>
                                    <option value="last_90">Last 90 Days</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <input type="hidden" id="todaydate" name="todaydate" value="<?=today_text();?>"/>
                <input type="hidden" id="member_id" name="member_id" value="<?=$member_id;?>"/>
                <div class="card-body m-t-md">
                    <div class="row">
                        <div class="col-12">
                            <form class="text-right" action="<?=base_url('reports/Profit_sharing_report/profit_sharing_export')?>" method="post" target="_blank">
                                <input type="hidden" name="_search" id="_search">
                                <input type="hidden" name="member_id_export" id="member_id_export">
                                <input type="hidden" name="date_from_export" id="date_from_export">
                                <input type="hidden" name="date_to_export" id="date_to_export">
                                <button class="btn btn-primary btnExport mr-md-3" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                <button type="button" class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-4 space-10">
                            <a class="no-decor">
                                <h1 id="total_profit" class="bolder">0</h1>
                                <p class="no-margin-bottom">Total Order Amount</p>
                            </a>
                        </div>
                        <div class="col-12 col-lg-4 space-10">
                            <a class="no-decor">
                                <h1 id="total_profit_net" class="bolder">0</h1>
                                <p class="no-margin-bottom">Total Profit Share Amount</p>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h5><b>Profit Share Amount</b></h5>
                    <div class="chartWrapper mb-2" >
                        <div class="chartAreaWrapper">
                            <canvas id="transactions" height="300" width="0" style = "border:none;"></canvas>
                        </div>
                    </div>

                    <h5><b>Order Transaction List</b></h5>
                    <table class="table table-striped table-hover table-bordered" id="table-grid">
                      <thead>
                          <tr>
                            <th>Sold To</th>
                            <th>Reference Number</th>
                            <th>Date</th>
                            <th>Order Amount</th>
                            <th>Profit Sharing Rate</th>
                            <th>Profit Share Amount</th>
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
<script type="text/javascript" src="<?=base_url('assets\js\public.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\moment.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\profit_sharing_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->
