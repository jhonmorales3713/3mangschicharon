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
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Inventory Report</li>
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
                                Inventory Report
                            </h3>
                        </div>
                        <div class="col">
                            <p class="border-search_hideshow p-0 mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="search_hideshow_btn">Hide Filter <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i></a>                                
                            </p>
                        </div>
                        <!--
                        <div style="margin-right: 15px;">
                            <p class="border-search_hideshow p-0 mb-0 d-flex align-items-center justify-content-end">
                                <a href="#" id="chart_toggle">Hide Chart <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i></a>                                
                            </p>                           
                        </div>
                        -->
                    </div>
                </div>                
                <div class="px-6 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search" class="col-lg-12 col-md-12">
                      <div class="row">                            
                            <div class="col-lg-6 col-md-6 pb-3">
                                <div class="input-daterange input-group" id="datepicker">
                                    <span class="input-group-addon f_s1" style="background-color:#fff; display:none;">From</span>
                                    <input type="text" class="input-sm form-control search-input-select1 date_from datetimepicker" style="z-index: 2 !important;" id="date_from" value="<?=(isset($setDate['fromdate'])) ? $setDate['fromdate']:''?>" name="start" readonly/>
                                    <span class="input-group-addon f_s2" style="background-color: #fff;">&nbsp;To &nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker f_i" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                                </div>
                                <div class="my-2 my-md-0 text-xs font-semibold text-orange-400"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;You cannot pick more than 1 month of data. Pick End Date First.</div>
                            </div>
                            <div class="col-lg-4 col-md-4 pb-3" id="datepicker2" style = "display:none;">
                                <div class="input-daterange input-group">
                                    <span class="input-group-addon" style="background-color: #fff;">To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <input type="text" value="<?=(isset($setDate['todate'])) ? $setDate['todate']:'';?>" class="input-sm form-control search-input-select2 date_to datetimepicker" style="z-index: 2 !important;" id="date_to_m" name="end" readonly/>
                                </div>
                            </div>                                                                    
                            <?php if($this->session->sys_shop_id == 0){?>
                            <div class="col-lg-3 col-md-4">
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
                            <?php } else{?>   
                                <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search" style="display:none;">
                                    <option value="<?=$this->session->sys_shop_id;?>" selected></option>
                                </select>                 
                            <?php }?>         
                                               
                            <?php if($this->session->branchid == 0 || $this->session->sys_shop_id == 0) {?>                                
                                <div class="col-lg-3 col-md-4">
                                    <div class="form-group">
                                        <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                        <select name="select_branch" id = "select_branch" class="form-control material_josh form-control-sm search-input-text enter_search">
                                            <option value="main">Main</option>
                                            <?php if($branches){?>
                                                <?php foreach ($branches as $branch): ?>
                                                    <option value="<?=$branch['branchid'];?>"><?=$branch['branchname'];?></option>
                                                <?php endforeach ?>
                                            <?php }?>
                                        </select>
                                    </div>                                
                                </div>                                
                            <?php }?>    
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <input type="text" id="itemname" class="input-sm form-control" placeholder="Search items here"/>
                                </div>
                            </div>  
                            <!--
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">                                    
                                    <select name="select_type" id = "select_type" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="all">All</option>
                                        <?php foreach ($types as $type): ?>
                                            <option value="<?=$type['type'];?>"><?=$type['type'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>      
                            -->                       
                      </div>
                    </form>                                           
                </div>                     
                <div class="card-body">                     
                    <div class="flex-right">
                        <form action="<?=base_url('reports/Inventory_report/export_inventory_transactions_table')?>" method="post" target="_blank">
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
                        <!--
                        <div id="salesChart" class="chartWrapper mb-2 collapse show">
                            <div class="card card-body chartAreaWrapper" style="box-shadow: 0 0 0 0 !important;">
                                <canvas id="transactions" height="400" width="0" style = "border:none;"></canvas>
                            </div>
                        </div>                        
                        <table class="table table-striped table-bordered summary" id = "table-grid-top">
                        <thead>
                            <tr class="b">
                                <td rowspan="2">Label</td>
                                <td colspan="2" style="text-align: center;">Total</td>
                                <td rowspan ="2" class = "align-right">Percentage</td>
                            </tr>
                            <tr>
                                <td class = "align-right bg-green-100" id="to_cur_head">Current</td>
                                <td class = "align-right bg-gray-100" id="to_prev_head">Previous</td>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Orders</td>                                
                                <td class = "align-right" id = "to_total_orders"></td>
                                <td class = "align-right" id = "p_to_total_orders"></td>
                                <td class = "align-right" id = "to_percent"></td>
                            </tr>
                            <tr>
                                <td>Total Orders Fulfilled</td>
                                <td class = "align-right" id = "to_f_total"></td>
                                <td class = "align-right" id = "p_to_f_total"></td>                                
                                <td class = "align-right" id = "to_f_percent"></td>
                            </tr>
                            <tr>
                                <td>Total Orders Delivered</td>                                
                                <td class = "align-right" id = "to_d_total"></td>
                                <td class = "align-right" id = "p_to_d_total"></td>
                                <td class = "align-right" id = "to_d_percent"></td>
                            </tr>
                        </tbody>
                        </table>        
                        -->               
                        <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered table-responsive" style="width:100%" id="table-grid">
                            <thead>
                                <tr>
                                    <th>Transaction Date</th>  
                                    <th>Shop Name</th>
                                    <th>Branch Name</th>                       
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <!-- <th>Beginning Quantity</th> -->
                                    <th>Quantity</th>
                                    <!-- <th>Ending Quantity</th>                                  -->
                                     
                                </tr>
                            </thead>        
                            <!--       
                            <tfoot>
                                <tr class="b">
                                    <td colspan="2" class="align-right">Total</td>
                                    <td id="t_pending"></td>
                                    <td></td>
                                </tr>
                            </tfoot>                                  
                            -->
                        </table>                        
                    </div>
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
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\public.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\inventory_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->
