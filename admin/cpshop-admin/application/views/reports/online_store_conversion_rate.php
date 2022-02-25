<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  /* .datepicker{
    z-index: 999 !important;
  } */

  table{
      width: 100% !important;
  }

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
            <li class="breadcrumb-item active">Online Store Conversion Rate</li>
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
                                Online Conversion Rate
                            </h3>
                        </div>
                        <div class="col">
                            <p class="border-search_hideshow p-0 mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="search_hideshow_btn">Hide Filter <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i></a>                                
                            </p>
                        </div>                       
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search" class="col-md-9">
                      <div class="row">
                            <div class="col-lg-6 col-md-6 pb-3">
                                <div class="input-daterange input-group" id="datepicker">
                                    <span class="input-group-addon f_s1" style="background-color:#fff; display:none;">From</span>
                                    <input type="text" class="input-sm form-control search-input-select1 date_from datetimepicker" style="z-index: 2 !important;" id="date_from" value="<?=(isset($setDate['fromdate'])) ? $setDate['fromdate']:''?>" name="start" readonly/>
                                    <span class="input-group-addon f_s2" style="background-color: #fff;">&nbsp;To &nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker f_i" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                                </div>
                                <div class="my-2 my-md-0 text-xs font-semibold text-orange-400 pt-2"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;You cannot pick more than 3 months of data. Pick End Date First.</div>
                            </div>
                            <div class="col-lg-4 col-md-4 pb-3" id="datepicker2" style = "display:none;">
                                <div class="input-daterange input-group">
                                    <span class="input-group-addon" style="background-color: #fff;">To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker" style="z-index: 2 !important;" id="date_to_m" name="end" readonly/>
                                </div>
                            </div>
                          <?php if($shop == 0 && $branch == 0):?>
                          <div class="col-md-3">
                              <div class="form-group">
                                  <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                  <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                      <option value="all">All Shops</option>
                                      <?php foreach ($shops as $shop): ?>
                                          <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                      <?php endforeach ?>
                                  </select>
                              </div>
                          </div>
                          <?php endif;?>
                      </div>
                    </form>                   
                </div>
                <div class="card-body">
                    <div class="flex-right">
                        <form action="<?=base_url('reports/Online_conversion_rate/export_oscr_data')?>" method="post" target="_blank">
                            <input type="hidden" name="_search" id="_search">
                            <input type="hidden" name="_filter" id="_filter">
                            <!-- <input type="hidden" name="_data" id="_data"> -->
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
                    <div style="height: auto; min-width: initial; overflow: auto;">
                        <table class="table table-striped table-bordered table-responsive" id = "table-grid-top">
                            <thead class="thead">
                            <tr>
                                <th class="text-sm sm:text-base">Actions</th>
                                <th class="text-center"></i>Sessions</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Growth<br><span id="growth" class="text-xs"></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-sm sm:text-base">Visitors</td>
                                <td class = "align-right visits" id = "visits_count"></td>
                                <td class="align-right visits" id = "visits_p1"></td>
                                <td class="align-right visits" id = "visits_p2"></td>
                            </tr>
                            <tr>
                                <td class="text-sm sm:text-base">Added To Cart</td>
                                <td class = "align-right text-sm sm:text-base atc" id = "atc_count"></td>
                                <td class = "align-right text-sm sm:text-base atc" id = "atc_p1"></td>
                                <td class = "align-right text-sm sm:text-base atc" id = "atc_p2"></td>
                            </tr>
                            <tr>
                                <td class="text-sm sm:text-base">Reached Checkout</td>
                                <td class = "align-right text-sm sm:text-base rc" id = "rc_count"></td>
                                <td class = "align-right text-sm sm:text-base rc" id = "rc_p1"></td>
                                <td class = "align-right text-sm sm:text-base rc" id = "rc_p2"></td>
                            </tr>
                            <tr>
                                <td class="text-sm sm:text-base">Proceeded to Payment</td>
                                <td class = "align-right text-sm sm:text-base ptp" id = "ptp_count"></td>
                                <td class = "align-right text-sm sm:text-base ptp" id = "ptp_p1"></td>
                                <td class = "align-right text-sm sm:text-base ptp" id = "ptp_p2"></td>
                            </tr>
                            <tr>
                                <td class="text-sm sm:text-base">Total Sessions Converted</td>
                                <td class = "align-right text-sm sm:text-base sessions" id = "ss_count"></td>
                                <td class = "align-right text-sm sm:text-base sessions" id = "ss_p1"></td>
                                <td class = "align-right text-sm sm:text-base sessions" id = "ss_p2"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <table class="table table-striped table-hover table-bordered table-responsive" id="table-grid">
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
                              <th>Date Created</th>
                              <th>Added To Cart</th>
                              <th>Reached Checkout</th>
                              <th>Proceeded to Payment</th>
                              <th>Sessions Converted</th>
                          </tr>
                      </thead>
                      <tfoot>
                            <tr>
                                <td class="font-semibold">Total</td>
                                <td id="total_atc" class="font-semibold">?</td>
                                <td id="total_rc" class="font-semibold">?</td>
                                <td id="total_ptp" class="font-semibold">?</td>
                                <td id="total_sessions" class="font-semibold">?</td>
                            </tr>
                      </tfoot>
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
<script type="text/javascript" src="<?=base_url('assets\js\reports\oscr_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->
