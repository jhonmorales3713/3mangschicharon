<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Developer Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shop Utilities</li>
        </ol>
    </div>

    <form id="form_shop_util" enctype="multipart/form-data" method="post" class="card">
        <div class="card-header">
            <h3 class="card-title">Details</h3>
        </div>

        <div class="card-body">
            <input type="hidden" id="cp_id" name="cp_id" value="<?=$shop_util['id'];?>">
            <input type="hidden" id="prev_val" name="prev_val">
            <div class="form-group">
                <label for="powered_by">Powered By:</label>
                <input type="url" class="form-control" name="powered_by" id="powered_by" aria-describedby="helpId" placeholder="www.cloudpanda.com" value="<?=$shop_util['powered_by']; ?>">
                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
            </div>

            <div class="form-group">
                <label>LOGO <small>(520x520 minimum width x height | JPG, PNG & JPEG)</small></label>
                <div class="file-input input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="logo_image" id="logo_image_multip" accept="image/*">
                        <label class="custom-file-label" id="file_label">Choose file</label>
                    </div>
                </div>
                <div id="product-placeholder">
                    
                </div>

                <div class="imagepreview mb-3">
                    <img src="<?=$shop_util['cp_logo']?>" alt="" id="product_preview">
                </div>

                <img src="" id="product_preview_multiple" class="img-responsive">
            </div>

            <div class="form-group">
                <label for="shop_main_announcement">Shop Main Announcement</label>
                <textarea class="form-control" name="shop_main_announcement" id="shop_main_announcement" rows="3" placeholder="Text here..."><?=$shop_util['shop_main_announcement']?></textarea>
            </div>

            <div class="form-group">
                <label for="c_paypanda_link_live">Paypanda Link Live:</label>
                <input type="link" class="form-control" name="c_paypanda_link_live" id="c_paypanda_link_live" aria-describedby="helpId" placeholder="www.paypanda.com/---/---" value="<?=$shop_util['c_paypanda_link']; ?>">
                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
            </div>
            
            <div class="form-group">
                <label for="c_paypanda_link_test">Paypanda Link Test:</label>
                <input type="url" class="form-control" name="c_paypanda_link_test" id="c_paypanda_link_test" aria-describedby="helpId" placeholder="www.paypanda.com/---/---" value="<?=$shop_util['c_paypanda_test'];?>">
                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
            </div>
            
            <div class="form-group">
                <label for="c_allowed_jcfulfillment_prefix">Allowed JC Fulfillment Prefix:</label>
                <input type="text" class="form-control" name="c_allowed_jcfulfillment_prefix" id="c_allowed_jcfulfillment_prefix" aria-describedby="helpId" placeholder="www.paypanda.com/---/---" value="<?=$shop_util['c_allowed_jcfulfillment_prefix'];?>">
                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
            </div>
            <?php if(isset($this->loginstate->get_access()['shop_utilities']['update']) && $this->loginstate->get_access()['shop_utilities']['update'] == 1) : ?>
                <div class="buttons text-right">
                    <a type="button" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</a>
                    <button type="submit" class="btn btn-success saveBtn">Save</button>
                </div>
            <?php endif ?>
        </div>

    </form>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script>var is_editable = <?=(isset($this->loginstate->get_access()['shop_utilities']['update']) && $this->loginstate->get_access()['shop_utilities']['update'] == 1) ? "true":"false";?>;</script>
<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/shop_utilities.js');?>"></script>