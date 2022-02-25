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
            <li class="breadcrumb-item active">Payout Report</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card" id = "main">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            Payout Report
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
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search" class="col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 pb-3">
                            <div class="input-daterange input-group" id="datepicker">
                                <span class="input-group-addon f_s1" style="background-color:#fff; display:none;">From</span>
                                <input type="text" class="input-sm form-control search-input-select1 date_from datetimepicker" style="z-index: 2 !important;" id="date_from" name="start" readonly/>
                                <span class="input-group-addon f_s2" style="background-color: #fff;">&nbsp;To &nbsp;</span>
                                <input type="text" class="input-sm form-control search-input-select2 date_to datetimepicker f_i" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 pb-3" id="datepicker2" style = "display:none;">
                            <div class="input-daterange input-group">
                                <span class="input-group-addon" style="background-color: #fff;">To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <input type="text" class="input-sm form-control search-input-select2 date_to datetimepicker" style="z-index: 2 !important;" id="date_to_m" name="end" readonly/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
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
                <div class="card-body">
                    <div class="flex-right">
                        <form class="text-right" action="<?=base_url('reports/Payout_report/payout_export')?>" method="post" target="_blank">
                                <input type="hidden" name="_search" id="_search">
                                <input type="hidden" name="member_id_export" id="member_id_export">
                                <input type="hidden" name="date_from_export" id="date_from_export">
                                <input type="hidden" name="date_to_export" id="date_to_export">
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
                        <div class="row">
                            <div class="col-12 col-lg-4 space-10">
                                <a class="no-decor">
                                    <h1 id="total_profit" class="bolder">0</h1>
                                    <p class="no-margin-bottom">Total Payout</p>
                                </a>
                            </div>
                        </div>                        
                        <div class="chartWrapper mb-2" id="payoutChart">
                            <h5><b>Payout</b></h5>
                            <div class="chartAreaWrapper">
                                <canvas id="transactions" height="300" width="0" style = "border:none;"></canvas>
                            </div>
                        </div>
                        <h5><b>Payout Transaction List</b></h5>
                        <table class="table table-striped table-hover table-bordered" id="table-grid">
                        <thead>
                            <tr>
                                <th>Transaction Date</th>
                                <th>Amount</th>
                                <th>Payout</th>
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
<script type="text/javascript" src="<?=base_url('assets\js\reports\moment.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\payout_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->
