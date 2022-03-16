<!-- change the data-num and data-subnum for numbering of navigation -->

<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products_home/Products');?>"><?=$active_page?></a></span>
        
    </div>
</div>
<div class="col-12">
    <section class="tables  ml-4">   
        <div class="container-fluid">
            <div class="card ">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center text-white">
                            Product List
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg  text-white" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row ">
                            <div class="col-md-6 col-lg-3">
                                Date Created
                                <input type="text" placeholder="Select Date Product Created" autocomplete="false" class="form-control form-control-sm datetimepicker-input" id="date_from" data-toggle="datetimepicker" data-target="#datepicker"/>
        
                            </div>
                            <div class="col-md-6 col-lg-3">
                                Product Name
                                <div class="form-group">
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Name">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                Filter By
                                <div class="form-group">
                                    <select name="_categories" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?=$category['id'];?>"><?=$category['category_name'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                Status
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
                    
                </div>
                <div class="card-body table-body">
                    <div class="col-md-12 col-lg-auto table-search-container text-right d-flex justify-content-end">
                        <div class="row no-gutters ">
                            <div class="col-12 col-md-auto">
                                <?php if($this->loginstate->get_access()['products']['create'] == 1){ ?>
                                        <button class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary mb-3 mb-md-0" id="addBtn">Add</button>
                                <?php } ?>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('admin/Main_products/export_product_table')?>" method="post" target="_blank">
                                            <input type="hidden" name="date_from" id="date_from">
                                            <input type="hidden" name="_shops" id="_shops">
                                            <input type="hidden" name="_name" id="_name">
                                            <input type="hidden" name="_record_status" id="_record_status">
                                            <input type="hidden" name="_search" id="_search">
                                            <button class="btn-mobile-w-100 btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                        </form>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100 btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-product"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="150">Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>No of Stock</th>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Message:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>



<!-- MODAL UPLOAD START -->
<div class="modal fade" id="batchUploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Batch Upload</h3>
            </div>
            <div class="container">
                <div class="row">
                    <div class=" col-md-1"></div>
                    <a class="btn btn-primary primary-bg btn-lg  col-md-5 m-2 btn-cus" href="<?php  echo base_url('products/Main_products/export_template_product/'.$token);?>">
                    <i class="fa fa-download fa-3x"></i><h3>Download <?=get_company_name()?> Product Template</h3> 
                        <small>Download product template excel file</small>
                    </a>
                    <a class="btn btn-primary primary-bg btn-lg  col-md-5 m-2 btn-cus" data-dismiss="modal" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#tokmartUploadModal" class="btn-mobile" id="tokmartUpload">
                    <i class="fa fa-upload fa-3x"></i><h3>Upload <?=get_company_name()?> Product Data</h3> 
                        <small>Upload product data excel file</small>
                    </a>
                </div>
                <div class="row">
                    <div class=" col-md-1"></div>
                    <a class="btn btn-primary primary-bg btn-lg  col-md-5 m-2 btn-cus" href="<?php  echo base_url('products/Main_products/export_template_product_inventory/'.$token);?>">
                    <i class="fa fa-download fa-3x"></i><h3>Download <?=get_company_name()?> Inventory Template</h3> 
                        <small>Download product inventory template excel file</small>
                    </a>
                    <a class="btn btn-primary primary-bg btn-lg  col-md-5 m-2 btn-cus" data-dismiss="modal" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#tokmartinvUploadModal" class="btn-mobile" id="tokmartinvUpload">
                    <i class="fa fa-upload fa-3x"></i><h3>Upload <?=get_company_name()?> Inventory Data</h3> 
                        <small>Upload inventory data excel file</small>
                    </a>
                </div>
                <?php if(ini() == 'toktokmart'){?>
                    <div class="row">
                        <div class=" col-md-1"></div>
                        <a class="btn btn-primary primary-bg btn-lg  col-md-5 m-2 btn-cus" href="<?php  echo base_url('products/Main_products/puregold_export_template_product/'.$token);?>">
                        <i class="fa fa-download fa-3x"></i><h3>Download Puregold Product Template</h3> 
                            <small>Download puregold product template file</small>
                        </a>
                        <a class="btn btn-primary primary-bg btn-lg  col-md-5 m-2 btn-cus" data-dismiss="modal" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#puregoldUploadModal" class="btn-mobile" id="puregoldUpload">
                        <i class="fa fa-upload fa-3x"></i><h3>Upload Puregold Product Data</h3> 
                            <small>Upload product data file</small>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                <!-- <button type="submit" class="btn btn-primary saveBtn" id="addFile" form="add_record_form">Add</button> -->
            </div>
        </div>
    </div>
</div>


<!-- puregold upload -->
<div class="modal fade" id="puregoldUploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Puregold Product Upload file</h3>
            </div>
            <form id="upload_pg_form" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Excel/CSV File<span class="required">*</span></label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" name="file" accept=".csv,.xlsx,.xls">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn" id="PGupload" form="upload_pg_form">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tokmartUploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel"><?=get_company_name();?> Product Upload file</h3>
            </div>
            <form id="upload_tokmart_form" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Excel/CSV File<span class="required">*</span></label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" name="file" accept=".csv,.xlsx,.xls">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn" id="TMupload" form="upload_tokmart_form">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tokmartinvUploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel"><?=get_company_name();?> Inventory Upload file</h3>
            </div>
            <form id="upload_inv_tokmart_form" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Excel/CSV File<span class="required">*</span></label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" name="file" accept=".csv,.xlsx,.xls">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary " data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveBtn" id="TMinvupload" form="upload_inv_tokmart_form">Upload</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL UPLOAD END -->

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/products/products.js');?>"></script>
<!-- end - load the footer here and some specific js -->

