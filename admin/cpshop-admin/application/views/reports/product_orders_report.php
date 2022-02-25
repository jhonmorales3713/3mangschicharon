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
            <li class="breadcrumb-item active">Product Orders Report</li>
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
                                Product Order Report
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
                                    <select name="filtertype" id = "filtertype" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Status">
                                    <option value="all">All Transaction</option>
                                    <option value="forprocess">For Process/Delivery</option>
                                    <option value="fullfilled">Fulfilled/Delivered</option>
                                    </select>
                                </div>
                            </div>
                            <?php if($this->loginstate->get_access()['overall_access'] == 1):?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                    <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Shops">
                                        <option value="all">All</option>
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
                <div class="card-body table-body">
                    <div class="flex-right">
                        <form action="<?=base_url('reports/product_orders_report/export_list_table')?>" method="post" target="_blank">
                            <input type="hidden" name="_search" id="_search">
                            <input type="hidden" name="_filters" id="_filters">                            
                            <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                        </form>  
                        <button class="btn btn-primary btnSearch" id="btnSearch">Search</button>      
                    </div>
                    <!--
                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('reports/product_orders_report/export_list_table')?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <input type="hidden" name="_filters" id="_filters">
                                        <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                    </form>
                                    <div class="col-6 col-md-auto px-1">
                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid" cellpadding="0" cellspacing="0" border="0">
                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:50%;">
                            <col style="width:20%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Shop</th>
                                <th>Product</th>
                                <th>Quantity</th>
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
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\product_orders_report.js');?>"></script>
<!-- end - load the footer here and some specific js -->
