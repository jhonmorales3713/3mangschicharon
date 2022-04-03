
<div class="col-12">
    <div class="alert alert-secondary ml-3 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_settings/settings_home/Settings');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/settings/Website_info/view');?>">Website Information</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Update</span>
        
    </div>
</div>

<div class="col-12">
    <form name="web_info" type="POST">
        <div class="content-inner bg-white ml-3" id="pageActive" data-num="7" data-namecollapse="" data-labelname=""> 
            <div class="card-header py-2">
                <div class="row">
                    <div class="col d-flex align-items-center text-white">
                        Website Information
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Name*</label>
                            <input type="text" name="f_name" class="form-control" value = "<?=get_company_name();?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Shortened Name*</label>
                            <input type="text" name="f_shortname" class="form-control" value="<?=get_shortened_name();?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Tagline*</label>
                            <input type="text" name="f_tagline" class="form-control" value="<?=get_tag_line();?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="control-label">Main Icon</label>
                            <div class="main_logo" style="display:none;"></div><br>
                            <div class="input-group mb-3" style="width:100%;">
                                <div class="custom-file">
                                    <input type="file" accept="image/png, image/jpeg" class="custom-file-input" data-checker="main_icon_checker" data-target="main_icon_preview" name="main_icon" id="main_icon">
                                    <label class="custom-file-label" id="file_label_main_logo">Choose file</label>
                                    <input type="hidden" class="hidden" name="main_icon_checker" id="main_icon_checker" accept="image/png, image/jpeg" value="false">
                                </div>
                            </div>
                            <img src="<?=base_url('assets/img/'.get_icon())?>" id="main_icon_preview" width=100% class="img-responsive mb-1">
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="control-label">Main Logo</label>
                            <div class="main_logo" style="display:none;"></div><br>
                            <div class="input-group mb-3" style="width:100%;">
                                <div class="custom-file">
                                    <input type="file" accept="image/png, image/jpeg" class="custom-file-input" data-checker="main_logo_checker" data-target="main_logo_preview" name="main_logo" id="main_logo">
                                    <label class="custom-file-label" id="file_label_main_logo">Choose file</label>
                                    <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" accept="image/png, image/jpeg" value="false">
                                </div>
                            </div>
                            <img src="<?=base_url('assets/img/'.get_logo())?>" id="main_logo_preview" width=100% class="img-responsive mb-1">
                        </div>
                    </div>

                    <!-- <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="control-label">Secondary Logo</label>
                            <div class="secondary_logo" style="display:none;"></div><br>
                                <div class="input-group" style="width:100%;">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="secondary_logo" id="secondary_logo">
                                    <label class="custom-file-label" id="file_label_secondary_logo">Choose file</label>
                                    <input type="hidden" class="hidden" name="secondary_logo_checker" id="secondary_logo_checker" value="false">
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="control-label">Placeholder Image (500 x 500 px max dimensions)</label>
                            <div class="placeholder_image" style="display:none;"></div><br>
                            <div class="input-group mb-3" style="width:100%;">
                                <div class="custom-file">
                                    <input type="file" accept="image/png, image/jpeg" data-target="placeholder_image_preview"  data-checker="placeholder_image_checker"class="custom-file-input" name="placeholder_image" id="placeholder_image">
                                    <label class="custom-file-label" id="file_label_placeholder_image">Choose file</label>
                                    <input type="hidden" class="hidden" name="placeholder_image_checker" id="placeholder_image_checker" accept="image/png, image/jpeg" value="false">
                                </div>
                            </div>
                            <img src="<?=base_url('assets/img/'.get_placeholder())?>" id="placeholder_image_preview" width=100% class="img-responsive mb-1">
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="control-label">Background Image</label>
                            <div class="background_image" style="display:none;"></div><br>
                            <div class="input-group mb-3" style="width:100%;">
                                <div class="custom-file">
                                    <input type="file" accept="image/png, image/jpeg" class="custom-file-input" data-checker="background_image_checker" data-target="background_image_preview" name="background_image" id="background_image">
                                    <label class="custom-file-label" id="file_label_background_image">Choose file</label>
                                    <input type="hidden" class="hidden" name="background_image_checker" id="background_image_checker" accept="image/png, image/jpeg" value="false">
                                </div>
                            </div>
                            <img src="<?=base_url('assets/img/'.get_bg())?>" id="background_image_preview" width=100% class="img-responsive mb-1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="col-12 mt-3">
    <form name="web_info" type="POST">
        <div class="content-inner bg-white ml-4" id="pageActive" data-num="7" data-namecollapse="" data-labelname=""> 
            <div class="card-header py-2">
                <div class="row">
                    <div class="col d-flex align-items-center text-white">
                        Contact Information
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Telephone No.</label>
                            <input type="text" name="f_telephone" class="form-control" value="<?=get_telephone()?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Phone No.</label>
                            <input type="text" name="f_phone" class="form-control" value="<?=get_company_phone()?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Support E-mail*</label>
                            <input type="text" name="f_support" class="form-control" value="<?=get_company_email()?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Business Address</label>
                            <input type="text" name="f_address" class="form-control" value="<?=get_address()?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="col-12 mt-3">
    <form name="web_info" type="POST">
        <div class="content-inner bg-white ml-4" id="pageActive" data-num="7" data-namecollapse="" data-labelname=""> 
            <div class="card-header py-2">
                <div class="row">
                    <div class="col d-flex align-items-center text-white">
                        Social Media
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Facebook Link</label>
                            <input type="text" name="f_facebook" class="form-control" value="<?=fb_link()?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Instagram Link</label>
                            <input type="text" name="f_instagram" class="form-control" value="<?=ig_link()?>">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Youtube Link</label>
                            <input type="text" name="f_youtube" class="form-control" value="<?=youtube_link()?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="col-12 mt-5 mb-5">
    <div class="content-inner  bg-white ml-4" id="pageActive" data-num="7" data-namecollapse="" data-labelname=""> 
        <div class="card-body d-flex justify-content-end">
            <button class="btn btn-primary" type="button" id="btnSubmit">Save</button>
        </div>
    </div>
</div>  
<script>
var token = "<?=$token;?>";
</script>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/settings/web_info_details.js');?>"></script>