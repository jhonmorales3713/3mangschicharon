<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.colorpickersliders.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dropzone.min.css'); ?>">

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- jquery UI -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="User List Setting"> 
	<div class="bc-icons-2 card mb-4">
		<ol class="breadcrumb mb-0 primary-bg px-4 py-3">
			<li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
			<li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
			<li class="breadcrumb-item active">Shop Banners</li>
		</ol>
	</div>
    <style type="text/css">
        .pointed_arrow{
            cursor: pointer;
        }
        .sub_content {
            position: absolute;
            top: 0;
            left:0;
            width: 100%;
            height: 100%;
            background-attachment: fixed;
        }
        .sub_content > .details {
            transition: .2s ease;
            opacity: 0;
            position:absolute;
            right:15px;
            bottom:0;
        }
        .details button{
            width:125px;
        }

        .sub_content:hover .details{
            opacity: 1;
        }

        .dropzone .dz-preview .dz-image {
            width: 100% !important; 
            height: auto !important; 
        }

        .dz-image img{
            max-width:100%;
            height:auto;
            margin-left: auto !important;
            margin-right: auto !important;
        }
        .dz-default.dz-message span {
            font-size:16px;
            font-weight:bold !important;
        }
        .dropzone.dz-started .dz-message {
            display: block; 
        }
        .dropzone {
            border: 2px dashed rgba(0,0,0,0.3);
        }

        .dropzone_div > .waves-input-wrapper.waves-effect.waves-light {
            float: right;
        }
        .dropzone_div > .waves-input-wrapper.waves-effect.waves-light input{
        }

        .dropzone .dz-preview:hover .dz-image img {
            -webkit-transform: scale(1.05, 1.05);
            -moz-transform: scale(1.05, 1.05);
            -ms-transform: scale(1.05, 1.05);
            -o-transform: scale(1.05, 1.05);
            transform: scale(1.05, 1.05);
            -webkit-filter: blur(0px);
            filter: blur(0px); 
        }

        a.dz-remove {
            font-weight:bold;
            color: #dc3545 !important;
        }

        .dz-preview.dz-error img {
            border: 5px solid red;
            opacity: .5;
        }
        .dz-default.dz-message  button{
            font-weight: bold !important;
            font-size:16px !important;
        }  

    </style>
    <section class="tables">   
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">

                        <div class="card-body">

                            <?php if ($this->loginstate->get_access()['shop_banners']['view']==1){ ?>                           
                            <div class="row">
                                <?php if ($this->loginstate->get_access()['shop_banners']['create']==1){ ?>
                                    <span style="display: none;" id="for_create_access" data-has_create_access="1"></span>
                                <?php }else{ ?>
                                    <span style="display: none;" id="for_create_access" data-has_create_access="0"></span>
                                <?php } ?>


                                <?php if ($this->loginstate->get_access()['shop_banners']['delete']==1){ ?>
                                <div class="col-lg-12 dropzone_div" id="dropzone_div" data-has_remove_access="1">
                                <?php }else{ ?>
                                <div class="col-lg-12 dropzone_div" id="dropzone_div" data-has_remove_access="0">
                                <?php } ?>        

                                <form action="" class="dropzone" id="myawesomedropzone"></form> 
                                <?php if ($this->loginstate->get_access()['shop_banners']['update']==1){ ?>
                                <input type="button" class="btn btn-success pull-right mt-3" value="SAVE BANNER" id="upload_btn_banner">
                                <?php } ?>                          
                                   
                            </div>

                            <?php } ?>

                        </div>                        
                    </div>
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


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/dropzone.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_shop_banners.js');?>"></script>
<!-- end - load the footer here and some specific js -->


<style type="text/css">
	.dz-remove{
		display: none;
	}
</style>