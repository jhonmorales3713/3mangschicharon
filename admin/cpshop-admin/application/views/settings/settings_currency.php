<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Payment Types Setting"> 
	<div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Currency</li>
        </ol>
    </div>

    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Currency</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Code</label> -->
                                <input type="text" name="_code" class="form-control material_josh form-control-sm search-input-text url enter_search" placeholder="Currency Code">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <!-- <label class="form-control-label col-form-label-sm">Payment Type</label> -->
                                <input type="text" name="_country_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Country Name">
                            </div>
                        </div>
                         <!-- start - record status is a default for every table -->
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group">
                                <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                    <option value="">All Records</option>
                                    <option value="1" selected>Enabled</option>
                                    <option value="2">Disabled</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    </form>
                    <!-- <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> -->
                    
                </div>
                <div class="card-body table-body">
                    <!-- <?php if ($this->loginstate->get_access()['currency']['create'] == 1){ ?>
                        <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary btnClickAddArea">Add</button>
                        </div>
                    <?php } ?> -->
                    <div class="col-md-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <?php if ($this->loginstate->get_access()['currency']['create'] == 1){ ?>
                                <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger btnClickAddArea mb-3">Add</button>
                            <?php } ?>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <form action="<?=base_url('settings/currency/export_currency_table')?>" method="post" target="_blank">
                                        <input type="hidden" name="_search" id="_search">
                                        <input type="hidden" name="_filter" id="_filter">
                                        <!-- <input type="hidden" name="_data" id="_data"> -->
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

                   
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Flag</th>
                                <th>Country Name</th>
                                <th>Currency Code</th>
                                <th>Exchange Rate to PHP</th>
                                <th>Exchange Rate from PHP</th>
                                <th>Status</th>
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>

</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Currency</h3>
            </div>
            <div class="modal-body">
                <form id="add_record_form">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="square" id="imgthumbnail-logo"><img id="flagImg" src="" style="max-width: 100%;max-height: 100%; display:none;"></div>
                                    <div class="img_preview_container square" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Upload Flag</label>
                                    <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_container" id="file_container">
                                            <label class="custom-file-label" id="file_description">Choose file</label>
                                            <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Country Name:</label>
                                    <input type="text" name="_add_country_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Currency Code:</label>
                                    <input type="text" name="_add_currency" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Currency Symbol:</label>
                                    <input type="text" name="_add_currency_symbol" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Country Code:</label>
                                    <input type="text" name="_add_country_code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Exchange Rate to PHP:</label>
                                    <input type="text" name="_add_exchangerate_php_to_n" class="form-control allownumericwithdecimal" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Exchange Rate from PHP:</label>
                                    <input type="text" name="_add_exchangerate_n_to_php" class="form-control allownumericwithdecimal" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Phone Prefix:</label>
                                    <input type="text" name="_add_phone_prefix" class="form-control add_phone_prefix">
                                </div>
                            </div>
                            <div class="col-6" style="display: none;">
                                <div class="form-group">
                                    <label class="col-form-label">From(<small>Days to ship</small>):</label>
                                    <input type="text" name="_add_from_dts" class="form-control allownumericwithdecimal">
                                </div>
                            </div>
                            <div class="col-6" style="display: none;">
                                <div class="form-group">
                                    <label class="col-form-label">To(<small>Days to ship</small>):</label>
                                    <input type="text" name="_add_to_dts" class="form-control allownumericwithdecimal">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Phone Limit:</label>
                                    <input type="text" name="_add_phone_limit" class="form-control add_phone_limit">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">UTC:</label>
                                    <input type="text" name="_add_utc" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Arrangement:</label>
                                    <input type="text" name="_add_arrangement" class="form-control allownumericwithdecimal">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="addCurrency" form="add_record_form">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Currency</h3>
            </div>
            <div class="modal-body">
                <form id="edit_record_form">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="flag_img square"></div>
                                    <div class="img_preview_container_update square" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Upload Flag</label>
                                    <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_container_update" id="file_container_update">
                                            <label class="custom-file-label" id="file_description_update">Choose file</label>
                                            <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Country Name:</label>
                                    <input type="text" name="_edit_country_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Currency Code:</label>
                                    <input type="text" name="_edit_currency" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Currency Symbol:</label>
                                    <input type="text" name="_edit_currency_symbol" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Country Code:</label>
                                    <input type="text" name="_edit_country_code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Exchange Rate to PHP:</label>
                                    <input type="text" name="_edit_exchangerate_php_to_n" class="form-control allownumericwithdecimal" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Exchange Rate from PHP:</label>
                                    <input type="text" name="_edit_exchangerate_n_to_php" class="form-control allownumericwithdecimal" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Phone Prefix:</label>
                                    <input type="text" name="_edit_phone_prefix" class="form-control edit_phone_prefix" required>
                                </div>
                            </div>
                            
                            <div class="col-6" style="display: none;">
                                <div class="form-group">
                                    <label class="col-form-label">From(<small>Days to ship</small>):</label>
                                    <input type="text" name="_edit_from_dts" class="form-control allownumericwithdecimal">
                                </div>
                            </div>
                            <div class="col-6" style="display: none;">
                                <div class="form-group">
                                    <label class="col-form-label">To(<small>Days to ship</small>):</label>
                                    <input type="text" name="_edit_to_dts" class="form-control allownumericwithdecimal">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Phone Limit:</label>
                                    <input type="text" name="_edit_phone_limit" class="form-control edit_phone_limit">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">UTC:</label>
                                    <input type="text" name="_edit_utc" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">Arrangement:</label>
                                    <input type="text" name="_edit_arrangement" class="form-control allownumericwithdecimal">
                                </div>
                            </div>
                            <input type="hidden" name="_edit_filename" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="editCurrency" form="edit_record_form">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
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
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_currency.js');?>"></script>
<!-- end - load the footer here and some specific js -->