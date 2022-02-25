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
      .summary tr, .summary td{
          padding: 3px !important;
          font-size: .8rem !important;
      }   
  }

</style>
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('reports/branch_performance/'.$token);?>">Branch Performance</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?= $shopname.' - '.$branchname; ?></li>
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
                                Branch Performance Report
                            </h3>
                        </div>                                          
                    </div>
                </div>                         
               <div class="card-body"> 
                    <div class="flex-right">
                        <form action="<?=base_url('reports/Branch_performance/export_branch_performance_breakdown_table')?>" method="post" target="_blank">
                            <input type="hidden" name="_search" id="_search">
                            <input type="hidden" name="_filters" id="_filters">                            
                            <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                        </form>  
                        <!--
                        <button class="btn btn-primary btnSearch" id="btnSearch">Search</button>      
                        -->
                    </div>                                                                                           
                    <div id="data-container">                       
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="">Shop Name:</label><br>
                                <label class="green-text font-weight-bold"><?= $shopname; ?></label>
                            </div>  
                            <div class="col-12 col-md-6">
                                <label class="">Branch Name:</label><br>
                                <label class="green-text font-weight-bold"><?= $branchname; ?></label>
                            </div>
                        </div>
                        <br><br>
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered" style="width:100%" id="table-grid">                      
                            <thead>
                                <tr>                
                                    <th>Payment Date</th>
                                    <th>Date Shipped</th>
                                    <th>Reference Number</th>
                                    <th>Order Aging</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr id="last_page" class="b">                                   
                                   <th colspan="3" class="align-right">Average</th>                                           
                                   <th id="t_average"></th>
                                   <th id="t_amount"></th>
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
<input type="hidden" id="shopid" value="<?=$this->session->sys_shop_id?>">
<input type="hidden" id="branchid" value="<?=$this->session->branchid?>">
<input type="hidden" id="shop_id" value="<?=$shop_id?>">
<input type="hidden" id="branch_id" value="<?=$branch_id?>">
<input type="hidden" id="time_in_seconds" value="<?=$time_in_seconds?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\public.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\branch_performance_details.js');?>"></script>
<!-- end - load the footer here and some specific js -->
