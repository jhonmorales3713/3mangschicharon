<?php 
//071318
//this code is for destroying session and page if they access restricted page

$position_access = $this->session->userdata('get_position_access');
$access_content_nav = $position_access->access_content_nav;
$arr_ = explode(', ', $access_content_nav); //string comma separated to array 
$get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();

$url_content_arr = array();
foreach ($get_url_content_db as $cun) {
    $url_content_arr[] = $cun['cn_url'];
}
$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';

if (in_array($content_url, $url_content_arr) == false){
    header("location:".base_url('Main/logout'));
}    
//071318
?>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings"> 

    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Sales Area</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header px-4">
                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Sales Area</label>
                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchArea" placeholder="Description">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch">Search</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                        <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addSalesAreaModal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary btnClickAddSalesArea">Add Sales Area</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Description</th>
                                    <th width="190">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal-->
<div id="addSalesAreaModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md modal-md-custom">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Add New Sales Area</h6>
            </div>
            <form class="form-horizontal personal-info-css" id="add_sales_area-form">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-4 form-control-label">Sales Area<span class="asterisk"></span></label>
                            <div class="col-md-8">
                                <input id="info_desc" type="text" class="form-control form-control-success" name="info_desc"><small class="form-text">Description</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="button" class="btn btn-primary saveBtnSalesArea">Add Sales Area</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="viewSalesAreaModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md modal-md-custom">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Update Sales Area</h6>
            </div>
            <form class="form-horizontal personal-info-css" id="update_sales_area-form">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-4 form-control-label">Sales Area <span class="asterisk"></span></label>
                            <div class="col-md-8">
                                <input type="hidden" name="info_id" class="info_id">
                                <input id="info_desc" type="text" class="form-control form-control-success info_desc" name="info_desc"><small class="form-text">Description</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="btn btn-primary updateBtnSalesArea">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="deleteSalesAreaModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h6 id="exampleModalLabel" class="modal-title">Delete Sales Area</h6>
            </div>
            <form class="form-horizontal personal-info-css" id="delete_sales_area-form">
                <div class="modal-body">
                    <p>Are you sure you want to delete this record <br>(<bold class="info_desc"></bold>) ?</p>
                    <input type="hidden" class="del_id" name="del_id" value="">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" class="btn btn-primary deleteSalesAreaBtn">Delete Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_sales_area.js');?>"></script>

