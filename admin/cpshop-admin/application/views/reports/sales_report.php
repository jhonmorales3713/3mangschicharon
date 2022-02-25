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

  .flex-right {
    display:flex;
    justify-content: flex-end;   
    padding-bottom: 20px; 
  }

  .btn{
      padding-top: 10px !important;
      padding-bottom: 10px !important;
  }  

  thead tr td{      
      text-align: center !important;
  }

  tr.b *{
    font-weight: bold !important;
  }

  /* responsiveness of datepicker */
  @media screen and (max-width: 450px) {
      #datepicker{
          height: max-content !important;
      }
      .f_s1{
          display: inline !important;
      }
      .f_s2{
          display: none !important;
      }
      .f_i{
          display: none !important;
      }
      #datepicker2{
          display: block !important;
      }      
  }

</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Sales Report</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card" id = "main_body">
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                Sales Report
                            </h3>
                        </div>
                        <div class="col">
                            <p class="border-search_hideshow p-0 mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="search_hideshow_btn">Hide Filter <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i></a>                                
                            </p>
                        </div>
                        <div style="margin-right: 15px;">
                            <p class="border-search_hideshow p-0 mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="chart_toggle">Hide Chart <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i></a>                                
                            </p>                           
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                      <div class="row">
                        <div class="col-lg-6 col-md-6 pb-3">
                              <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                              <div class="input-daterange input-group" id="datepicker">
                                  <span class="input-group-addon f_s1" style="background-color:#fff; display:none;">From</span>
                                  <input type="text" class="input-sm form-control search-input-select1 date_from datetimepicker" style="z-index: 2 !important;" id="date_from" name="start" readonly/>
                                  <span class="input-group-addon f_s2" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                  <input type="text" class="input-sm form-control search-input-select2 date_to f_i datetimepicker" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                              </div>
                          </div>
                          <div class="col-lg-4 col-md-4 pb-3" id="datepicker2" style = "display:none;">
                                <div class="input-daterange input-group">
                                    <span class="input-group-addon" style="background-color: #fff;">To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker" style="z-index: 2 !important;" id="date_to_m" name="end" readonly/>
                                </div>
                          </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Status</label> -->
                                    <select name="pmethodtype" id = "pmethodtype" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Payment Methods</option>
                                        <option value="op">Online Purchases</option>
                                        <option value="mp">Manual Purchases</option>
                                    </select>
                                </div>
                            </div>
                          <div class="col-lg-3 col-md-6" hidden>
                              <div class="form-group">
                                  <label class="form-control-label col-form-label-sm">Status</label>
                                  <select name="filtertype" id = "filtertype" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="all">All Transaction</option>
                                    <option value="paid">Paid Transactions</option>
                                    <option value="unpaid">Unpaid Transactions</option>
                                  </select>
                              </div>
                          </div>
                          <?php if($shopid == 0 && $branch == 0):?>
                          <div class="col-lg-3 col-md-6">
                              <div class="form-group">
                                  <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                  <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                      <option value="all">All</option>
                                      <?php foreach ($shops as $shop): ?>
                                          <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                      <?php endforeach ?>
                                  </select>
                              </div>
                          </div>
                          <div id="select_branch_container" class="col-md-2" style="display: none;">
                              <div class="form-group">
                                  <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                  <select name="select_branch" id = "select_branch" class="form-control material_josh form-control-sm search-input-text enter_search"></select>
                              </div>
                          </div>
                          <?php elseif($shopid > 0 && $branch == 0): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                    <input type="hidden" name="select_shop" id="select_shop" value="<?=$shopid;?>">
                                    <select name="select_branch" id = "select_branch" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="all">All Branches</option>
                                        <option value="main">Main</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['branchid'];?>"><?=$shop['branchname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <?php else : ?>
                            <div>
                                <input type="hidden" name="select_shop" id="select_shop" value="<?=$shopid;?>">
                                <input type="hidden" name="select_branch" id="select_branch" value="<?=$branch;?>">
                            </div>
                          <?php endif;?>
                      </div>
                    </form>
                    <div class="col-md-3 text-right">
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="flex-right">
                        <form action="<?=base_url('reports/sales_report/export_salesreport_data')?>" method="post" target="_blank">
                            <input type="hidden" name="_search" id="_search">
                            <input type="hidden" name="_filters" id="_filters">                            
                            <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                        </form>  
                        <button class="btn btn-primary btnSearch" id="btnSearch">Search</button>      
                    </div>
                    <div id="message-container">
                        <center> 
                        <div class="col-8">                                                       
                            <i class="fa fa-search"></i><span> To show records, kindly select your preferred date range. You may use other filter(s) if there's any.</span>
                        </div>
                        </center>
                    </div>
                    <div id="data-container">
                        <div id="salesChart" class="chartWrapper mb-2 collapse show">
                            <div class="card card-body chartAreaWrapper" style="box-shadow: 0 0 0 0 !important;">
                                <canvas id="transactions" height="300" width="0" style = "border:none;"></canvas>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id = "table-grid-top" style = "display:none;">
                        <tbody>
                            <tr>
                            <td>Total No. of Transactions</td>
                            <td class = "align-right" id = "total_count"></td>
                            </tr>
                            <tr>
                            <td>Total Paid Amount</td>
                            <td class = "align-right" id = "total_paid"></td>
                            </tr>
                            <tr>
                            <td>Total Unpaid Amount</td>
                            <td class = "align-right" id = "total_unpaid"></td>
                            </tr>
                            <tr>
                            <td>Total Transaction Amount</td>
                            <td class = "align-right" id = "total_transaction_amount"></td>
                            </tr>
                        </tbody>
                        </table>                        
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered" style="width:100%" id="table-grid">                                      
                        
                        <!-- <colgroup>
                            <col style="width:10%;">
                            <col style="width:15%;">
                            <col style="width:8%;">
                            <col style="width:8%;">
                            <col style="width:8%;">
                            <col style="width:8%;">
                            <col style="width:5%;">
                        </colgroup> -->
                        <thead>
                            <tr>
                                <th>Count</th>
                                <th>Shop</th>
                                <th>Customer</th>
                                <th>Order Date</th>
                                <th>Ref#</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        </table>                        
                    </div>
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
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\sales_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->
