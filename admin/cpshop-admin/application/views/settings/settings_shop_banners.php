<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- jquery UI -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?= $main_nav_id; ?>" data-namecollapse="" data-labelname="User List Setting">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?= base_url('Main_page/display_page/settings_home/' . $token); ?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Shop Banners</li>
        </ol>
    </div>
    <style>
        .image-preview {
            /* background: rgba(255,255,255,0.6); */
            cursor: move;
            text-align: center;
        }

        .preview-container {
            width: 100%;
            position: relative;
            margin: 0 15px;
        }

        #sortable,
        #unsortable {
            width: 100%;
        }

        #sortable li {
            border: none !important;
            display: block;
            width: 100% !important;
            padding: 10px;
            border-radius: 15px;
            text-align: center;
            background: lightgray;
            margin-top: 15px;
            min-height: 300px;
            max-height: max-content;
            position: relative;
            cursor: move;
            background-repeat: no-repeat !important;
            background-position: top center !important;
            background-size: cover !important;
            -webkit-box-shadow: 2px 2px 17px -5px rgba(138, 138, 138, 1);
            -moz-box-shadow: 2px 2px 17px -5px rgba(138, 138, 138, 1);
            box-shadow: 2px 2px 17px -5px rgba(138, 138, 138, 1);
            overflow: hidden;
        }

        #unsortable li {
            border: none !important;
            display: block;
            width: 100% !important;
            padding: 20px;
            border-radius: 15px;
            opacity: 0.65;
            text-align: center;
            background: lightgray;
            margin-top: 15px;
            min-height: 300px;
            max-height: max-content;
            position: relative;
            background-repeat: no-repeat !important;
            background-position: top center !important;
            background-size: cover !important;
            -webkit-box-shadow: 2px 2px 17px -5px rgba(138, 138, 138, 1);
            -moz-box-shadow: 2px 2px 17px -5px rgba(138, 138, 138, 1);
            box-shadow: 2px 2px 17px -5px rgba(138, 138, 138, 1);
            overflow: hidden;
        }

        #unsortable li:hover {
            opacity: 1;
        }

        #sortable li *,
        #unsortable li * {
            color: white;
            font-weight: bold;
        }

        #sortable li p,
        #unsortable li p {
            position: absolute;
            bottom: 0;
            width: 50%;
            left: 0;
            color: white;
            text-align: center;
            padding: 3px 5px;
            border-radius: 0 20px 0 0;
            background: rgba(0, 0, 0, 0.5) !important;
            background-size: 75% !important;
            transition: all 0.3s ease-in-out !important;
        }

        #sortable li .remove-btn
        {
            position: absolute;
            opacity: 0;
            top: 0;
            right: 0;
            font-size: 1rem;
            font-weight: bold !important;
            padding: 3px 15px;
            border-radius: 0 0 0 20px;
            transition: all 0.2s ease-in-out !important;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2;
        }
        
        #unsortable li .remove-btn
        {
            position: absolute;
            opacity: 0;
            top: 0;
            right: 0;
            font-size: 1rem;
            font-weight: bold !important;
            padding: 3px 15px;
            border-radius: 0 0 0 20px;
            transition: all 0.2s ease-in-out !important;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2;
        }

        #sortable li .deact {
            position: absolute;
            opacity: 0;
            top: 0;
            left: 0;
            font-size: 1rem;
            font-weight: bold !important;
            padding: 3px 15px; 
            border-radius: 20px 0 20px 0;
            transition: all 0.2s ease-in-out !important;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2;
        }

        #unsortable li .activate {
            opacity: 0;
            font-size: 1rem;
            font-weight: bold !important;
            border-radius: 12px;
            position: absolute;
            top: 0;
            left: 0;
            border: 1px solid #28a745;
            padding: 0 15px;
            border-radius: 20px 0 20px 0;
            color: #28a745;
        }

        #unsortable li:hover .activate {
            opacity: 1;
        }

        #unsortable li .activate:hover {
            color: #fff;
            background-color: #28a745;
            background-image: none;
            border-color: #28a745;
        }

        #sortable li:hover .remove-btn,
        #unsortable li:hover .remove-btn
        {
            opacity: 1;
        }

        #sortable li:hover .deact {
            opacity: 1;
        }

        #sortable li .deact:hover {
            background: rgba(253, 186, 28, 0.8);
        }

        #dropzone {
            border: 2px dashed #808080;
            height: 150px;
            width: 100%;
            display: block;
            position: relative;
        }

        #dropzone p {
            margin: auto;
            margin-top: 55px;
            font-size: 1.2rem;
            font-weight: 700;
            width: 80%;
            color: #333;
            text-align: center;
        }

        #dropzone input {
            display: block;
            position: absolute;
            opacity: 0;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }
    </style>
    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">

                        <div class="card-body">
                            <div id="dropzone">
                                <p>Drag your banners here to upload.</p>
                                <input type="file" name="banner_images" id="banner_images" multiple>
                            </div>

                            <div class="row">
                                <div class="preview-container">
                                <form action="" method="post">
                                    <input type="button" class="btn btn-success pull-right my-3 mr-2" value="SAVE BANNER" id="save">
                                </form>
                                    <ul id="sortable">
                                        <?php foreach ($banners as $banner) { ?>
                                            <?php $filepath = get_s3_imgpath_upload() . 'assets/img/ad-banner/' . $banner->filename; ?>
                                            <li id="<?= substr($banner->filename, 0, strrpos($banner->filename, ".")); ?>" class="exists" style="background: url('<?= $filepath ?>');">
                                                <!-- <button class="deact" data-id="<?= $banner->id; ?>" data-toggle="modal" data-target="#deactModal">DEACTIVATE</button> -->
                                                <button class="remove-btn">REMOVE BANNER</button>
                                                <p><?= $banner->filename; ?></p>
                                                <br>
                                                <?php if ($banner->banner_link == '') { ?>
                                                    <button class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#myFuture" data-id="<?= $banner->id ?>" style="border-radius: 12px;">Add Link</button>
                                                <?php } else { ?>
                                                    <button class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#UpdateLink" data-banner="<?= $banner->banner_link ?>" data-id="<?= $banner->id ?>" style="border-radius: 12px;">Update Link</button>
                                                <?php } ?>


                                                <?php if ($banner->scheduledFrom_post == '' && $banner->scheduledTo_post == '') : ?>
                                                    <button class="btn btn-sm btn-primary pull-right mr-2" data-toggle="modal" data-target="#setPostSchedule" data-banner="<?= $banner->banner_link ?>" data-id="<?= $banner->id ?>" data-is-active="<?= $banner->is_active ?>" data-status="<?= $banner->status ?>" style="border-radius: 12px;">SET SCHEDULE</button>
                                                <?php else : ?>
                                                    <button class="btn btn-sm btn-primary pull-right mr-2" data-toggle="modal" data-target="#setPostSchedule" data-banner="<?= $banner->banner_link ?>" data-id="<?= $banner->id ?>" data-is-active="<?= $banner->is_active ?>" data-status="<?= $banner->status ?>" data-start-date="<?= date('Y-m-d', strtotime($banner->scheduledFrom_post)); ?>" data-start-time="<?= date('H:i', strtotime($banner->scheduledFrom_post)); ?>" data-end-date="<?= date('Y-m-d', strtotime($banner->scheduledTo_post)); ?>" data-end-time="<?= date('H:i', strtotime($banner->scheduledTo_post)); ?>" style="border-radius: 12px;">UPDATE SCHEDULE</button>
                                                <?php endif; ?>

                                                <button class="btn btn-sm btn-danger pull-right mr-2" data-id="<?= $banner->id; ?>" data-toggle="modal" data-target="#deactModal" style="border-radius: 12px;">DEACTIVATE</button>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <?php if(count($inactive_banners) != 0): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="preview-container">
                                    <ul id="unsortable">
                                        <?php foreach ($inactive_banners as $i_banner) { ?> 
                                            <?php $file_path = get_s3_imgpath_upload() . 'assets/img/ad-banner/' . $i_banner->filename; ?>
                                            <li id="<?= substr($i_banner->filename, 0, strpos($i_banner->filename, ".")); ?>" class="exists" style="background: url('<?= $file_path ?>');">
                                                <button class="activate" data-id="<?= $i_banner->id ?>" data-toggle="modal" data-target="#activationModal">ACTIVATE BANNER</button>
                                                <button class="remove-btn">REMOVE BANNER</button>
                                                <p><?= $i_banner->filename; ?></p>
                                                <br>

                                                <div id="buttons">
                                                    <?php if ($i_banner->banner_link == '') { ?>
                                                        <button class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#myFuture" data-id="<?= $i_banner->id ?>" style="border-radius: 12px;">Add Link</button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#UpdateLink" data-banner="<?= $i_banner->banner_link ?>" data-id="<?= $i_banner->id ?>" style="border-radius: 12px;">Update Link</button>
                                                    <?php } ?>
                                                    <?php if ($i_banner->scheduledFrom_post == '' && $i_banner->scheduledTo_post == '') : ?>
                                                        <button class="btn btn-sm btn-primary pull-right mr-2" data-toggle="modal" data-target="#setPostSchedule" data-banner="<?= $i_banner->banner_link ?>" data-id="<?= $i_banner->id ?>" data-is-active="<?= $i_banner->is_active ?>" data-status="<?= $i_banner->status ?>" style="border-radius: 12px;">SET SCHEDULE</button>
                                                    <?php else : ?>
                                                        <button class="btn btn-sm btn-primary pull-right mr-2" data-toggle="modal" data-target="#setPostSchedule" data-banner="<?= $i_banner->banner_link ?>" data-id="<?= $i_banner->id ?>" data-is-active="<?= $i_banner->is_active ?>" data-status="<?= $i_banner->status ?>" data-start-date="<?= date('Y-m-d', strtotime($i_banner->scheduledFrom_post)); ?>" data-start-time="<?= date('H:i', strtotime($i_banner->scheduledFrom_post)); ?>" data-end-date="<?= date('Y-m-d', strtotime($i_banner->scheduledTo_post)); ?>" data-end-time="<?= date('H:i', strtotime($i_banner->scheduledTo_post)); ?>" style="border-radius: 12px;">UPDATE SCHEDULE</button>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Upload Confirmation</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to Save the Banners?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="upload_confirm_btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="myFuture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Link</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="BannerId">
                <textarea id="BannerLink" name="BannerLink" rows="4" placeholder="link....." style="width: 100%;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="CloseBtnLink">Close</button>
                <button type="button" class="btn btn-primary" id="AddlinkBtn">Add link</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="setPostSchedule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Set Banner Schedules</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="SchedBannerId">
                <div class="div">
                    <div class="input-group datetimepicker" data-date-end-date="0d">
                        <input type="date" class="form-control" style="z-index: 1050 !important;" id="date_from" min="<?= today(); ?>" placeholder="MM/DD/YYYY">
                        <span class="input-group-addon">From</span>
                        <input type="time" class="form-control" style="z-index: 2 !important;" id="time_from">
                    </div>
                    <br>
                    <div class="input-group" data-date-end-date="0d">
                        <input type="date" class="form-control" style="z-index: 1050 !important;" id="date_to" min="<?= today(); ?>" placeholder="MM/DD/YYYY">
                        <span class="input-group-addon">&nbsp;&nbsp; To &nbsp;&nbsp;</span>
                        <input type="time" class="form-control" style="z-index: 2 !important;" id="time_to">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="CloseBtnLink">Close</button>
                <button type="button" class="btn btn-primary" id="setScheduleBtn">Set Schedule</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="UpdateLink" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Update Link</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="UpdateBannerId">
                <textarea id="UpdateBannerLink" name="UpdateBannerLink" rows="4" placeholder="link....." style="width: 100%;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="CloseBtnLink">Close</button>
                <button type="button" class="btn btn-primary" id="UpdatelinkBtn">Update link</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deactModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Update Link</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deactId">
                <p>Are you sure you want to set this as inactive?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="CloseBtnLink">Close</button>
                <button type="button" class="btn btn-primary" id="deactivate">Deactivate</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="activationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Update Link</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="activationId">
                <p>Are you sure you want to set this as active?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="CloseBtnLink">Close</button>
                <button type="button" class="btn btn-primary" id="activate">Activate</button>
            </div>
        </div>
    </div>
</div>


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer'); ?>
<!-- includes your footer -->
<script type="text/javascript" src="<?= base_url('assets/js/settings/settings_shop_banners.js'); ?>"></script>
<!-- end - load the footer here and some specific js -->