<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Announcement Setting"> 
	<div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Announcement</li>
        </ol>
    </div>

    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p>
                <div class="card-header px-4" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label col-form-label-sm">Name</label>
                                <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text url enter_search" placeholder="Name">
                            </div>
                        </div>
                    </div>
                    </form>
                    <div class="form-group text-right">
                        <button class="btn-mobile-w-100 mx-0 btn btn-primary btnSearch" id="btnSearch">Search</button>
                    </div> 
                </div>
                <div class="card-body">      
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th width="30%">Name</th>
                                <th>Announcement</th>
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Edit Announcement</h3>
            </div>
            <div class="modal-body">
                <form id="edit_announcement_form">               
                    <div class="form-group">
                        <label class="col-form-label">Announcement:</label>
                        <textarea name="_edit_announcement" class="form-control" rows="6" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="edit_announcement_form">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_announcement.js');?>"></script>
<!-- end - load the footer here and some specific js -->