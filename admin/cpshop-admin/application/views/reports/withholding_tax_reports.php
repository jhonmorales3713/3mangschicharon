<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .datepicker{
    z-index: 9999 !important;
  }

  td, th {
    vertical-align: middle !important;
  }

  .req_msg{
    display:none !important;
  }

  .red-border{
    border: 1px solid red !important;
  }

  .dt-right{
    text-align: right;
  }

  .btn-guest{
    border:2px solid #ef4131;
    color: #ef4131;
    background-color: #fff;
    font-weight: bold;
    width:100% !important;
  }

  .btn-reseller{
    border: 2px solid #b2c73e;
    background-color: #b2c73e;
    color: #fff;
    width:100% !important;
  }

  #no_of_stocks{
    background-color: #fff !important;
  }

</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Wallet">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Withholding Tax Reports</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <!-- MAIN -->
            <div class="card" id = "main">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Withholding Tax Reports</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn">Hide Filter <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">

                        <div class="col-lg-6">
                            <!-- <label class="form-control-label col-form-label-sm">Date</label> -->
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" placeholder="<?=today_text();?>" name="start" readonly/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" placeholder="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                            </div>
                        </div>
                        <?php if($this->loginstate->get_access()['overall_access'] == 1):?>
                          <div class="col-md-6 col-lg-3">
                              <div class="form-group">
                                  <!-- <label class="form-control-label col-form-label-sm">Shops</label> -->
                                  <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search" style = "display:<?=($this->loginstate->get_access()['overall_access'] == 0) ? 'none': '' ?>">
                                      <option value="">All Shops</option>
                                      <?php foreach ($shops as $shop): ?>
                                          <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                      <?php endforeach ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group">
                                  <select name="select_branches" id = "select_branches" class="form-control material_josh form-control-sm search-input-text enter_search" disabled>
                                    <option value="" selected>Branches</option>
                                  </select>
                              </div>
                          </div>
                        <?php endif;?>

                    </div>
                    </form>
                    <!-- <div class="form-group text-right">
                      <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->

                </div>
                <div class="card-body table-body" >
                    <div class="card-body table-body">
                      <div class="col-md-auto table-search-container">
                          <div class="row no-gutters">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                      <form action="<?=base_url('reports/Withholding_tax_reports/export_list_table')?>" method="post" target="_blank" class="col-12 col-sm-6 col-md-auto px-1 mb-3">
                                          <input type="hidden" name="_search" id="_search">
                                          <button class="btn-mobile-w-100 btn btn-outline-danger btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                                      </form>
                                    </div>
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                      <input type="text" class="form-control" placeholder="Search Billcode..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="search_billcode" id="search_billcode">
                                    </div>

                                    <div class="col-6 col-md-auto px-1 mb-3">
                                      <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                      <button class="btn-mobile-w-100 btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                              </div>
                          </div>
                      </div>
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap text-center" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                          <colgroup>
                              <col style="width:20%;">
                              <col style="width:20%;">
                              <col style="width:20%;">
                              <col style="width:15%;">
                              <col style="width:15%;">
                              <col style="width:15%;">
                          </colgroup>
                          <thead>
                              <tr>
                                  <th>Billing Date</th>
                                  <th>Billing Code</th>
                                  <th>Billing No</th>
                                  <th>Shopname</th>
                                  <th>Branch</th>
                                  <th>Withholding Tax</th>
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
<script type="text/javascript" src="<?=base_url('assets\js\wallet\cleave.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom-cleave.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\jquery.alphanum.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\wallet\custom.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\withholding_tax_reports.js');?>"></script>
<!-- end - load the footer here and some specific js -->
