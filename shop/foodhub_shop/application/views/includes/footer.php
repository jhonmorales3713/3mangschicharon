    </main>
    <footer>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-3 d-none d-lg-block">
                    <img src="<?=secondary_logo()?>" onerror="this.onerror=null; this.src='<?=secondary_logo()?>'" alt="Logo" class="w-75">
                    <?php if(get_tag_line() != ""){ ?><p><?=get_company_name();?> <?=get_tag_line();?></p><?php } ?>
                </div>
                <div class="col-6 col-md-4 col-lg-3 text-center text-md-left">
                    <h5 class="footer-title">Useful links</h5>
                    <p><a href="<?=privacy_policy();?>">Privacy Policy</a></p>
                    <p><a href="<?=terms_and_condition();?>">Terms and Condition</a></p>
                </div>
                <div class="col-6 col-md-4 col-lg-3 text-center text-md-left">
                    <h5 class="footer-title">Follow Us</h5>
                    <p>
                        <a href="<?=fb_link()?>" class="footer-socmed"><i class="fa fa-facebook-official mr-3"></i></a>
                        <a href="<?=ig_link()?>" class="footer-socmed"><i class="fa fa-instagram mr-3"></i></a>
                    </p>
                </div>
                <div class="col-12 col-md-4 col-lg-3 text-center text-md-left">
                    <h5 class="footer-title">Contact Us</h5>
                    <p><?=get_company_phone();?></p>
                    <p><?=get_company_email();?></p>
                    <p><a href="<?=contact_us();?>" class="btn portal-primary-btn">Customer Care</a></p>
                </div>
                <div class="col-12">
                    <hr>
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 justify-content-center align-items-bottom system-name__name">
                            <p class="mb-0"><a href="<?=base_url("")?>"><img style="height:3vh;margin-left:10px;" src="<?=secondary_logo()?>" onerror="this.onerror=null; this.src='<?=secondary_logo()?>'" alt="Logo" /></a> &copy; <?=date("Y")?> All Rights Reserved</p>
                        </div>
                        <div class="col-lg-6 col-sm-6 justify-content-center system-name__name">
                            <p class="mb-0">Powered by<a href="<?=powered_by()?>"><img style="height:2vh;margin-left:10px;" src="<?=cp_logo()?>" onerror="this.onerror=null; this.src='<?=cp_logo()?>'" alt="Logo" /></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- for lazyload -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script> -->
    <script src="<?=base_url('assets\js\lozad.min.js');?>"></script>
    <!-- end for lazyload -->
    <!-- for jquery -->
	<script src="<?=base_url('assets\js\jquery-3.5.1.min.js');?>"></script>
	<script src="<?=base_url('assets\js\jquery-ui.js');?>"></script>
    <script type="text/javascript" src="<?=base_url("assets/js/slick.min.js")?>"></script>
    <script src="<?=base_url('assets/js/select2.js');?>"></script>
    <!-- for jquery -->

    <!-- for tooltip -->
	<!-- <script src="https://unpkg.com/@popperjs/core@2"></script> -->
    <script src="<?=base_url('assets/js/popper.min.js'); ?>"></script>
    <!-- end for tooltip -->

    <!-- for conversion of dates -->
    <script src="<?=base_url('assets\js\moment.js'); ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script> -->
    <!-- for conversion of dates -->

    <!-- for responsive layout -->
    <script src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
    <script src="<?=base_url('assets\js\modernizr.min.js');?>"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script> -->
    <!-- end for responsive layout -->

    <!-- for toast -->
    <script src="<?=base_url('assets\js\toastr.min.js');?>"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script> -->
    <!-- end for toast -->

    <!-- our own script -->
    <script type="text/javascript" src="<?=base_url('assets/js/script-052620.js');?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/js/app/helper.js'); ?>"></script>
    <!-- <script src="<?=base_url('assets/js/cmj_js/webtraf.js'); ?>"></script> -->
    <!-- for our own script -->


    <!-- <script src="<?//=base_url('assets/js/lazysizes.min.js');?>"></script> -->


	<!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> -->
	<!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script> -->
	<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 	 -->
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.27/moment-timezone-with-data.min.js"></script> -->
	<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script> -->
	<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
	<!-- <script type="text/javascript" src="<?//=base_url('assets/js/jquery.steps.js'); ?>"></script> -->
	<!-- <script type="text/javascript" src="<?//=base_url('assets/js/datatables.js'); ?>"></script> -->
	<!-- <script  type="text/javascript" src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js"></script> -->  <!-- gijgo datepicker -->
	<!-- <script src="<?//=base_url('assets/js/moment.min.js');?>"></script> -->
    <!-- <script src="<?//=base_url('assets/js/daterangepicker.js');?>"></script> -->
  <style>
      .slick-track {
          display: flex;
          align-items: center;
      }
  </style>
</body>
</html>
