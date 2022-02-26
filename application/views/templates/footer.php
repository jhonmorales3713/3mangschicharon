
    <div class="w-100 py-4 flex-shrink-0 bg-dark">
        <div class="container py-4">
            <div class="row gy-4 gx-5">
                <div class="col-lg-4 col-md-6">
                    <h5 class="h1 text-white"><?=cs_clients_info()->name;?></h5>
                    <p class="small text-white"><?= cs_clients_info()->tagline;?></p>
                    <p class="small text-white mb-0">&copy; Copyrights. All rights reserved. </p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-3">Quick links</h5>
                    <ul class="list-unstyled text-muted">
                        <li>
                            <a href="<?=cs_clients_info()->facebook_link;?>">
                                <span class="fab fa-facebook p-3"></span>
                            </a>
                            <a href="<?=cs_clients_info()->twitter_link;?>">
                                <span class="fab fa-twitter p-3"></span>
                            </a>
                            <a href="<?=cs_clients_info()->instagram_link;?>">
                                <span class="fab fa-instagram p-3"></span>
                            </a>
                            <a href="<?=cs_clients_info()->youtube_link;?>">
                                <span class="fab fa-youtube p-3"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-3">About Us</h5>
                    <ul class="list-unstyled text-muted">
                        <li><a href="<?=base_url('about');?>" class="text-white">Our Team</a></li>
                        <li><a href="<?=base_url('about');?>" class="text-white">Contact Us</a></li>
                        <li><a href="<?=base_url('about/faqs');?>" class="text-white">FAQS</a></li>
                    </ul>
                </div>
            </div>
        </div>
    
    </div>
    <script src="<?= base_url('assets/js/libs/jquery.min.js') ?>"></script>        
    <!-- libs -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>         
    <script src="<?=base_url('assets/js/libs/jquery.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/tether.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/bootstrap.bundle.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/bootstrap.bundle.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/mdb.min.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/jquery-ui.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/jquery.toast.js'); ?>" ></script>
    <script src="<?=base_url('assets/js/libs/moment.js')?>"></script>
</html>