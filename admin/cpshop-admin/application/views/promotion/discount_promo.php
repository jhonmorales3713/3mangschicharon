<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<style>
    .required {
    color: red;
    }

</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/promotion_home/'.$token);?>">Promotion</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Discount Promo</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Discount Promo</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row">
                            <div class="col-md-10 col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label col-form-label-sm">Promotion Period</label>   
                                    <div class="input-daterange input-group datetimepicker">
                                        <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 start_date col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="" id="start_date" name="start_date" placeholder="MM/DD/YYYY" autocomplete="false">
                                        <span class="input-group-addon" style="height:95%">&nbsp;to&nbsp;</span>
                                        <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 end_date col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="" id="end_date" name="end_date" placeholder="MM/DD/YYYY" autocomplete="false">        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label col-form-label-sm">Promotion Name</label>
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Promotion Name">
                                </div>
                            </div>
                         </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    
                    <div class="col-md-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <?php

                                    $token_session = $this->session->userdata('token_session');
                                    $token = en_dec('en', $token_session);

                                 if ($this->loginstate->get_access()['mystery_coupon']['create'] == 1)
                                    { ?>
                                    <a href="<?=base_url("promotion/Main_promotion/mystery_coupon/".$token."")?>"><button data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger mb-3" id="action_add">Add</button></a>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
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
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Promotion Name</th>
                                <th>Status</th>
                                <th>Period</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/promotion/discount_promo.js');?>"></script>
<!-- end - load the footer here and some specific js -->

