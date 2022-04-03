<style type="text/css">
    table th, table td
    {
        width: 100px;
        padding: 5px;
        border: 1px solid #ccc;
    }
    .selected
    {
        background-color:  var(--primary-color);
        color: #fff;
    }
    .main-footer{z-index: 1;}
</style>
<div class="col-12 pl-5">
    <div class="alert alert-secondary color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_settings/settings_home/Settings');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/settings/Website_info/view');?>">Website Information</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">FAQS</span>
        
    </div>
</div>

<div class="col-12 pl-4 mb-3 bg-white">
    <!-- //d-flex align-items-end flex-column  -->
    <div class="ml-4">
        <div class="row">
            <div class="col-12 col-lg-4 mb-3">
                <label for="f_search">Search</label>
                <input type="text" placeholder="Input Here" autocomplete="false" class="form-control form-sm" name="f_search" id="f_search"/>
            </div>
            <div class="col-12 col-lg-2 mb-3">
                <label for="f_status">Status</label>
                <select id="f_status" class="form-control form-sm" id="f_status" name="f_status">
                    <option value="1">Enabled</option>
                    <option value="2">Disabled</option>
                </select>
            </div>
            <div class="col-6 col-lg-2 mb-3 offset-lg-2">
                <button type="button" style="display:none" class="btn btn-primary" id="btnsaveArrangement">Save Arrangement</button>
            </div>
            <div class="col-6 col-lg-2 form-inline">
                <button type="button" data-toggle="modal" data-target="#add_modal"class="btn btn-primary mx-1" id="btnAdd">Add</button>
                <button type="button"class="btn btn-primary mx-1" id="btnRefresh"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                <button type="button"class="btn btn-primary" id="btnSearch">Search</button>
            </div>
        </div>
    </div>
</div>
<div class="col-12 bg-white pl-5">
    <table class="table wrap-btn-last-td  table-striped table-hover table-bordered display nowrap" id="tblfaqs"  style="width:100%" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
                <th style="width:50px" width="20"></th>
                <th hidden>id</th>
                <th width="150">Title</th>
                <th>Content</th>
                <th style="display:none">Arrangement</th>
                <th width="30">Status</th>
                <th width="30">Action</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="add_update_title">Add FAQ</h3>
            </div>
            <div class="modal-body">
                <form name="form_faq" id="form_faq" method="POST">
                    <div class="form-group">
                        <label for="f_title" class="col-form-label">Title</label>
                        <input type="text" class="form-control" name="f_title" id="f_title">
                    </div>
                    <div class="form-group">
                        <label for="f_content" class="col-form-label">Content</label>
                        <textarea id="f_content" name="f_content" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveFAQ">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="enable_disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <button type="button" class="btn btn-primary" id="disable_enable_modal_confirm_btn">Confirm</button>
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

<br>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/settings/faqs.js');?>"></script>