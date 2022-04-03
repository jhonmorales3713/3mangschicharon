<link rel="stylesheet" href="<?=base_url('assets/css/libs/footer_styles.css')?>">        

<div class="container-fluid footer-nav">
    <div class="container">
        <div class="row">        

            <div class="col-lg-4 col-md-4 col-sm-12 p20">
                <center>
               
                <img src="<?= base_url('assets/img/shop_logo.png'); ?>" alt="" width="160"><br>
                <span class="small n-top20">&copy; All Rights Reserved 2022</span>
                </center>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 p20 social-links">
                <br>       
                <small><strong class="mb-3 p10">SOCIAL MEDIA LINKS:</strong></small>                
                <ul class="list-unstyled form-inline p10">
                    <?php if(fb_link()!=''){?>
                        <li><a href="<?=fb_link();?>" class="primary-color p-3 n-top10"><span class="fa fa-facebook-square"></span></a></li>
                    <?php }?>
                    <?php if(ig_link()!=''){?>
                        <li><a href="<?=ig_link();?>" class="primary-color p-3 n-top10"><span class="fa fa-instagram"></span></a></li>
                    <?php }?>
                    <?php if(youtube_link()!=''){?>
                        <li><a href="<?=youtube_link();?>" class="primary-color p-3 n-top10"><span class="fa fa-youtube-square"></span></a></li>
                    <?php }?>
                </ul>                
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 p20 about-links">
                <br>
                <small><strong class="mb-3">ABOUT US</strong></small>
                <ul class="list-unstyled">
                    <li><a href="<?=base_url('about');?>" class="primary-color n-top10"><small class="b">Our Team</small></a></li>
                    <li><a href="<?=base_url('about');?>" class="primary-color"><small class="b">Contact Us</small></a></li>
                    <li><a href="<?=base_url('user/about/'.faqs_link());?>" class="primary-color"><small class="b">FAQs</small></a></li>
                </ul>
            </div>
        </div>
    </div>   
</div>