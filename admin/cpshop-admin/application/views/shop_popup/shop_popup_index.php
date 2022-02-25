<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/maploader.css');?>">
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/shops_home/'.$token);?>">Shops</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php if($this->session->userdata('sys_shop') == 0){ ?>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Shops/popup_image/'.$token);?>">Shops Pop Up Image</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php }?>
            <li class="breadcrumb-item active"><?= $breadcrumbs_active ?></li>
        </ol>
    </div>
        <form id="entry-form">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Shop Pop Up Image</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-7 col-lg-7">
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="popup_link" class="control-label">Pop up image link*</label>
                                                <input type="text" class="form-control" name="popup_link" id="popup_link" value="<?= c_popup_link() ?>">
                                            </div>
                                        </div>
                                         <div class="col-12 col-md-12 col-lg-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">Pop Up Image</label>
                                                        <div class="input-group" style="width:100%;">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" name="file_container" id="file_container">
                                                                <label class="custom-file-label" id="file_description">Choose file</label>
                                                            </div>
                                                        </div>
                                                        <div class="note mt-4">
                                                            Make sure to meet the minimum image size requirements for better image quality
                                                            <br>
                                                            <br>
                                                            1. Dimension:  380x450px<br>
                                                            2. File type: JPEG, PNG, JPG<br>
                                                            3. File Size: maximum 3mb
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <input type="checkbox" class="form-control-input" id="popup_enable" name="popup_enable" value="1"
                                                <?php echo c_popup_isset() == 1 ? 'checked' : '';  ?>>
                                                <label class="form-control-label" for="popup_enable">Enable Pop up image</label>
                                                <br>
                                            </div>
                                        </div>
                                        </div>
                                </div> 
                                <div class="col-12 col-md-5 col-lg-5">
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-lg-12">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-md-7 col-lg-7">
                                                    <label class="control-label">
                                                        Pop Up Image Preview
                                                    </label>
                                                    <?php if(!empty(c_popup_img())){ ?>
                                                        <div class="square" id="imgthumbnail-logo"><img src="<?= get_s3_imgpath_upload().'assets/img/promo_popup/'.c_popup_img(); ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                    <?php }else{ ?>
                                                        <div class="square" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/placeholder-380x450.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                    <?php } ?>
                                                    <div class="img_preview_container square" style="display:none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <section class="tables">   
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 col-12 text-right">
                                        <div class="card">
                                            <div class="card-body">
                                                <button type="button" class="btn btn-default waves-effect waves-light"
                                                id="back-button" onclick="history.go(-1)">Back</button>
                                                <?php if(
                                                    isset($this->loginstate->get_access()['shop_popup']['create']) ||
                                                    isset($this->loginstate->get_access()['shop_popup']['update'])
                                                    ) : ?>
                                                    <?php if(
                                                        $this->loginstate->get_access()['shop_account']['update'] == 1 ||
                                                        $this->loginstate->get_access()['shop_popup']['create'] == 1) : ?>
                                                        <button type="submit" class="btn btn-primary  waves-effect waves-light btn-save">Save</button>
                                                    <?php endif ?>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
    </form>                                       
</div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->

<script type="text/javascript" src="<?=base_url('assets/js/shops/shop_cityofregion.js');?>"></script>
<script type="text/javascript" src="<?=base_url($core_js);?>"></script>
<?php if(!isset($sys_shop_details)){ ?> <!-- ADDING NEW RECORD -->
    <script type="text/javascript" src="<?=base_url('assets/js/shops/googlemap.js');?>"></script>
<?php }else{ ?> <!-- UPDATING RECORD -->
    <script type="text/javascript" src="<?=base_url('assets/js/shops/googlemap_edit.js');?>"></script>
<?php } ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=get_google_api_key()?>&libraries=places&callback=initializeMaps"
async defer></script>

<!-- end - load the footer here and some specific js