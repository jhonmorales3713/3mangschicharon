<div class="row">
    <div class="container">
        <div class="col-12">        
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('packages'); ?>">Packages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Package</li>
                </ol>            
            </nav>            
        </div>
        
        <div class="col-12">
            <h4><?= $package['package_name']; ?></h4>
            <strong><?= php_money($package['price']); ?></strong>
        </div>
        
        <div class="col-12">
            <img src="<?= base_url('uploads/package_img/'.$package['package_img']) ?>" alt="" width="100%">
        </div>

        <?php if($package['package_group_id'] == 4){ ?>
            <div class="row col-12">
                <?php if(isset($sub_package)){ 
                    foreach($sub_package as $sub){ ?>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <strong><?= $sub['sub_package_name']; ?></strong>
                            <?php if($sub['inclusions'] != '' || $sub['inclusions'] != NULL){ 
                                $inc = explode(',',$sub['inclusions']); ?>
                                <ul>
                                <?php foreach($inc as $in){ ?>
                                    <li><?= $in; ?></li>
                                <?php }?>
                                </ul>
                            <?php }?>
                        </div>
                    <?php }
                }?>
            </div>
        <?php } else { ?>
            <div class="col-12">
                <strong>Package Inclusions</strong><br>
                <?php if($package['package_inclusions'] != '' || $package['package_inclusions'] != NULL){ ?>
                    <?php $package_inclusions = explode(',',$package['package_inclusions']); ?>  
                    <ul>
                        <?php foreach($package_inclusions as $inclusion){ ?>
                            <li><?= $inclusion; ?></li>
                        <?php }?>
                    </ul>
                <?php }?>      
            </div>
        <?php } ?>
        <?php if($package['notes'] != '' || $package['notes'] != NULL){ ?>
            <div class="col-12">
                <?php $notes = explode(',',$package['notes']); ?>
                <?php foreach($notes as $note){ ?>
                    <small><?= $note; ?></small><br>
                <?php }?>                
            </div>
        <?php } ?>
      
        <hr>
        <div class="col-12">            
            <a href="<?= base_url('booking/'.en_dec('en',$package['id'])); ?>" class="btn btn-sm btn-warning no-decor">BOOK NOW</a>
        </div>
        
        
    </div>
</div>