<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  /* .datepicker{
    z-index: 999 !important;
  } */

  td, th {
    vertical-align: middle !important;
  }

  table{
      width: 100% !important;
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
            <li class="breadcrumb-item active">Sale Settlement Report</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card" id = "main">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            Sale Settlement Report
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="card-header px-4" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">

                        <div class="col-md-3">
                            <label class="form-control-label col-form-label-sm">Date</label>
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" placeholder="<?=today_text();?>" name="start" readonly/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" placeholder="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" style="z-index: 2 !important;" id="date_to" name="end" readonly/>
                            </div>
                        </div>
                        <?php if($this->loginstate->get_access()['overall_access'] == 1):?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Shops</label>
                                <select name="select_shop" id = "select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="">All</option>
                                    <?php foreach ($shops as $shop): ?>
                                        <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <?php endif;?>
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Status</label>
                                <select name="select_status" id = "select_status" class="form-control material_josh form-control-sm search-input-text enter_search">
                                  <option value="1" selected>All</option>
                                  <option value="2">On Process</option>
                                  <option value="3">Settled</option>
                                </select>
                            </div>
                        </div> -->
                    </div>
                    </form>
                    <div class="form-group text-right">
                      <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div>

                </div>
                <div class="card-body">
                    <!-- start - record status is a default for every table -->
                    <div class="row">
                      <!-- <div class="col-md-3">
                          <div class="form-group">
                              <div class="row">
                                  <div class="col-6" style="padding:0px;">
                                      <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                          <option value="">All Records</option>
                                          <option value="1" selected>Enabled</option>
                                          <option value="2">Disabled</option>
                                      </select>
                                  </div>
                              </div>
                          </div>
                      </div> -->
                    </div>
                    <!-- end - record status is a default for every table -->

                    <!-- <div class="table-responsive"> -->
                        <table class="table table-striped table-hover table-bordered" id="table-grid">
                          <colgroup>
                              <col style="width:10%;">
                              <col style="width:15%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:8%;">
                              <col style="width:5%;">
                          </colgroup>
                          <thead>
                              <tr>
                                <th>Billcode</th>
                                <th>Shop</th>
                                <th>Date</th>
                                <th>Gross Amount</th>
                                <th>Fee</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                              </tr>
                          </thead>
                        </table>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </section>
</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\reports\sale_settlement.js');?>"></script>
<!-- end - load the footer here and some specific js -->
