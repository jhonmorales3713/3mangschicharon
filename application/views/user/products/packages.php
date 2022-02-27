<link rel="stylesheet" href="<?=base_url('assets/css/libs/gallery_animation.css')?>">
<div class="row">
    <div class="container">
        <center>
        <h1>Packages</h1>
        </center>
            
            <div class="col-12">
                <?php foreach($package_groups as $group){ ?>
                    <div class="col-12 <?=strtolower($group['package_group_name']);?> gallery-img-animation">
                        <hr>
                        <center>
                        <h3 class="package-group"><?= $group['package_group_name']; ?></h3>                                                                        
                        </center>
                        <hr>
                        <div class="row p20">
                            <?php foreach($packages as $package){ ?>  
                                <?php if($package['package_group_id'] == $group['id']){ ?>
                                    <div class="col-lg-4 col-md-6 col-sm-6 mt5">
                                        <div class="card">
                                            <div class="card-header">                                                
                                                <h4><?= $package['package_name']; ?></h4>                                                
                                                <strong><?= php_money($package['price']); ?></strong><br>
                                            </div>
                                            <div class="card-body">
                                                <img src="<?= base_url('uploads/package_img/'.$package['package_img']); ?>" width="100%" class="package-img"><br><br>                                    
                                                <?php $package_inclusions = explode(',',$package['package_inclusions']); ?>                                                 
                                                <?php if($package['package_group_id'] != 4){ ?> 
                                                    <?php if($package['package_inclusions'] != ''){?>   
                                                        <strong>Package Includes</strong>                      
                                                        <ul>
                                                            <?php foreach($package_inclusions as $inclusion){ ?>
                                                                <li><?= $inclusion; ?></li>
                                                            <?php }?>
                                                        </ul>
                                                    <?php }?>  
                                                <?php } else {?>                                                    
                                                    <?php foreach($sub_packages as $sub_package){ ?>
                                                        <?php if($package['id'] == $sub_package['package_id']){ ?>
                                                            <strong>**<?= $sub_package['sub_package_name']; ?>**</strong><br>
                                                            <?php if($sub_package['inclusions'] != '' || $sub_package['inclusions'] != NULL){ ?>
                                                                <?php $sub_inclusions = explode(',',$sub_package['inclusions']); ?>
                                                                <ul>
                                                                <?php foreach($sub_inclusions as $in){ ?>
                                                                    <li><?= $in; ?></li>
                                                                <?php } ?>
                                                                </ul>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php }?>        
                                                <?php }?>                                                
                                                <?php if($package['notes'] != '' || $package['notes'] != NULL){ ?>
                                                    <div class="card-footer">                                                    
                                                    <?php $notes = explode(',',$package['notes']); ?>
                                                    <?php foreach($notes as $note){ ?>
                                                        <small><?= $note; ?></small><br>
                                                    <?php }?>                
                                                    </div>                                          
                                                <?php }?>
                                            </div>
                                            <div class="card-footer">
                                                <center>
                                                    <a href="<?= base_url('packages/view_package').'/'.en_dec('en',$package['id']); ?>" class="btn btn-sm btn-info mt5 view-package-details">VIEW PACKAGE</a>   
                                                    <a href="<?= base_url('booking/'.en_dec('en',$package['id'])); ?>" class="btn btn-sm btn-warning mt5 ml5 view-package-details">BOOK NOW</a>
                                                </center>
                                            </div>
                                        </div>                                    
                                    </div>
                                <?php }?>
                            <?php }?>                                                     
                        </div>
                    </div>                    
                <?php }?>
            </div>

    </div>
</div>

<!-- MODAL -->
<div id="modal_package_details" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">New message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>   
                <button type="button" class="btn btn-sm btn-warning book-now">BOOK NOW</button>             
            </div>
            
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/libs/user/packages/packages.js'); ?>"></script>