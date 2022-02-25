<?php 
    function select_option_msa($obj_res, $type=""){
        $list = "";
        foreach ($obj_res as $row) {
            if($type == "city"){
                $list .= "<option value='".$row->citymunDesc."'>".$row->citymunDesc."</option>";
            }
        }
        echo $list;
    }

?>
<style>
.dropdown-item:hover{
    cursor: pointer;
    background-color: lightblue;
}
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Shops</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shop MCR Scheduling</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <p class="border-search_hideshow">
                    <a href="#" id="search_hideshow_btn">Hide Filter <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> 
                    <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                    </p>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <?php if ($this->session->userdata('sys_shop') == 0){ ?>
                        <div class="col-md-6 col-lg-3">
                        <?php }else{ ?>
                        <div class="col-md-6 col-lg-3" style="display: none !important;">
                        <?php } ?>
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Main Shop</label> -->
                                <select class="select2 form-control form-control-sm required_fields form-state" name="_mainshop">
                                    <option value="" selected>Select Shop</option>
                                    <?php select_option_obj($mainshop, 'mainshop') ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Branch Name</label> -->
                                <input type="text" id="effectivity_date" name="_effectivity_date" class="form-control material_josh form-control-sm search-input-text datepicker" placeholder="Effectivity Date">
                            </div>
                        </div>
                        <!-- start - record status is a default for every table -->
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                    <option value="" selected>All Records</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Applied</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    </form>
                </div>

                <div class="card-body table-body">
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
                            
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('Shop_mcr_scheduling/export_shop_mcr_scheduling')?>" method="post" target="_blank">
                                            <input type="hidden" name="_search" id="_search">
                                            <input type="hidden" name="_filter" id="_filter">
                                            <!-- <input type="hidden" name="_data" id="_data"> -->
                                            <button class="btn-mobile-w-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                        </form>  
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100  btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Shop Name</th>
                                <th>MCR</th>
                                <th>Startup</th>
                                <th>JC</th>
                                <th>MCJR</th>
                                <th>MC</th>
                                <th>MCSUPER</th>
                                <th>MCMEGA</th>
                                <th>Others</th>
                                <th>Effectivity Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal-->
    <div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Disable Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                    <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="disable_modal_confirm_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this record?</p>
                    <small>This action cannot be undone.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal" style="background-color: #222 !important;
                    color: var(--primary-color) !important;
                    border: 2px solid #222 !important;
                    font-weight: 500 !important;
                    padding: 10px 30px;">Close</button>
                    <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/shops/shop_mcr_scheduling.js');?>"></script>
<!-- end - load the footer here and some specific js