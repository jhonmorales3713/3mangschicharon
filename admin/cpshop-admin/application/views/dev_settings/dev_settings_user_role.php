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
<!-- 
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
 -->
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="User Role">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">User Role</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="">
                            <div class="card-header d-flex align-items-center">
                                 <div class="col-lg-12">
                                    <br>
                                    <div class="row">

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label col-form-label-sm">Position</label>
                                                <input type="text" class="form-control material_josh form-control-sm search-input-text searchPosition" placeholder="Position">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <button type="button" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_userrole_modal" class="btn btn-primary pull-right" id="add_userrole_btn" style="right:20px; position: absolute; top:20px;">Add Role</button>
                            <button class="btn btn-primary btnSearch" style="right:125px; position: absolute; top:20px;">Search</button>

                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="table-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th width="190">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <br><br><br><br><br><br>

    <div id="add_userrole_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg modal-lg-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Add User Role</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="add_userrole_form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="form-control-label" style="font-size:1em; font-weight: bold;">Main Navigation Role</label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2 form-control-label">Position <span class="asterisk" style="color:red"></span></label>
                                                <div class="col-md-4">
                                                    <input required id="a_position" type="text" class="form-control form-control-warning a_position" name="a_position">
                                                </div>
                                            </div>
                                            <br>
                                            <?php
                                                $main_nav_role = $this->model_settings->get_pb_userrole_main_nav();
                                            ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="i-checks">
                                                            <input id="acb_all" type="checkbox" value="Sales" class="checkbox-template acb_all" name="acb_all">
                                                            <label for="acb_all">All</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php foreach ($main_nav_role->result() as $mnr){ ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="i-checks">
                                                                <input id="<?=$mnr->attr_val;?>" type="checkbox" value="<?=$mnr->main_nav_id;?>" class="checkbox-template checkbox-template2 <?=$mnr->attr_val;?>" name="<?=$mnr->attr_val;?>">
                                                                <label for="<?=$mnr->attr_val;?>"><?=$mnr->main_nav_desc;?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-md-6 form-control-label" style="font-size:1em; font-weight: bold;">Content Navigation Role</label>
                                                    </div>
                                                </div>
                                                <br>
                                                <?php 
                                                $get_content_nav = $this->model_settings->get_content_nav_userrole();
                                                $get_cn_fkey_eq_name = $this->model_settings->get_cn_fkey_eq_name();
                                                ?>  
                                                    
                                                <?php foreach($get_cn_fkey_eq_name->result() as $gcfk){  ?>
                                                    <?php if ($gcfk->cn_url != null) { ?>
                                                        <div class="col-md-12">
                                                            <div class="form-group row">
                                                                <label class="col-md-6 form-control-label" style="font-weight: bold;"><?=$gcfk->main_nav_desc;?></label>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php foreach($get_content_nav->result() as $gcf){  ?>
                                                        <?php if ($gcfk->main_nav_id == $gcf->cn_fkey){ ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <div class="i-checks">
                                                                        <input id="acb_<?=$gcf->id;?>" type="checkbox" value="<?=$gcf->id;?>" class="checkbox-template checkbox-template2 acb_<?=$gcf->id;?>" name="acb_content[]">
                                                                        <label for="acb_<?=$gcf->id;?>"><?=$gcf->cn_name?></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }else{ ?>
                                                            <!-- <div class="col-md-12"></div> -->
                                                        <?php }?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="submit" style="float:right" class="btn btn-primary edit_userrole_save_btn">Save</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancel_btn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit_userrole_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-lg modal-lg-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Edit User Role</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="edit_userrole_form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="form-control-label" style="font-size:1em; font-weight: bold;">Main Navigation Role</label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2 form-control-label">Position <span class="asterisk" style="color:red"></span></label>
                                                <div class="col-md-4">
                                                    <input type="hidden" id="r_positionorig" class="r_positionorig" name="r_positionorig">

                                                    <input type="hidden" id="r_position_id" class="r_position_id" name="r_position_id">
                                                    <input required id="r_position" type="text" class="form-control form-control-warning r_position" name="r_position">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="i-checks">
                                                            <input id="cb_all" type="checkbox" value="Sales" class="checkbox-template cb_all" name="cb_all">
                                                            <label for="cb_all">All</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php foreach ($main_nav_role->result() as $mnr){ ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="i-checks">
                                                                <input id="<?=$mnr->attr_val_edit;?>" type="checkbox" value="<?=$mnr->main_nav_id;?>" class="checkbox-template checkbox-template2 <?=$mnr->attr_val_edit;?>" name="<?=$mnr->attr_val_edit;?>">
                                                                <label for="<?=$mnr->attr_val_edit;?>"><?=$mnr->main_nav_desc;?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label class="col-md-6 form-control-label" style="font-size: 1em; font-weight: bold;">Content Navigation Role </label>
                                                    </div>
                                                </div>
                                                <br>
                                                <?php 
                                                $get_content_nav = $this->model_settings->get_content_nav_userrole();
                                                $get_cn_fkey_eq_name = $this->model_settings->get_cn_fkey_eq_name();
                                                ?>  
                                                    
                                                <?php foreach($get_cn_fkey_eq_name->result() as $gcfk){  ?>
                                                    <?php if ($gcfk->cn_url != null) { ?>
                                                        <div class="col-md-12">
                                                            <div class="form-group row">
                                                                <label class="col-md-6 form-control-label" style=" font-weight: bold;"><?=$gcfk->main_nav_desc;?></label>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php foreach($get_content_nav->result() as $gcf){  ?>
                                                        <?php if ($gcfk->main_nav_id == $gcf->cn_fkey){ ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <div class="i-checks">
                                                                        <input id="cb_<?=$gcf->id;?>" type="checkbox" value="<?=$gcf->id;?>" class="checkbox-template checkbox-template2 cb_<?=$gcf->id;?> cb_content_class" name="cb_content[]">
                                                                        <label for="cb_<?=$gcf->id;?>"><?=$gcf->cn_name?></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }else{ ?>
                                                            <!-- <div class="col-md-12"></div> -->
                                                        <?php }?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="submit" style="float:right" class="btn btn-primary edit_userrole_save_btn">Save</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancel_btn" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="delete_userrole_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete Accounts</h4>
                    <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                </div>
                <form class="form-horizontal personal-info-css" id="delete_userrole_form">
                    <div class="modal-body">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Are you sure you want to delete this record <br>(<bold class="r_position_delete" id="r_position_delete"></bold>) ?</p>
                                    <input type="hidden" class="r_position_id_delete" name="r_position_id_delete" id="r_position_id_delete">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="form-group row">       
                            <div class="col-md-12">
                                <button type="submit" style="float:right" class="btn btn-primary deleteAccountBtn">Delete</button>
                                <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer'); ?>
<script type="text/javascript" src="<?=base_url('assets/js/globalfunctions.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/dev_settings/dev_settings_user_role.js');?>"></script>
