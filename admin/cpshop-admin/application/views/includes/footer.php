                            <!-- Page Footer-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id='easy-top'><i class="fa fa-arrow-up no-margin fa-lg"></i></div>
    </main>
    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <p><?php echo company_initial(); ?> <?=(company_initial() == '') ? "" : "|";?> <?php echo get_company_name(); ?> &copy; <?php echo year_only(); ?></p>
                </div>
                <div class="col-sm-6">
                    <p><?=powered_by();?></p>
                </div>
            </div>
        </div>
    </footer>



<?php
$class = $this->router->class; // your controller name
$method = $this->router->method; // your function name
$arr_dts = ["document_tracker_list", "document_monitoring"];
?>
<!-- Javascript files-->
<script src="<?=base_url('assets/js/popper.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery-3.5.1.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/jquery-ui.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/tether.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/moment.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
<script type="text/javascript" src="<?=base_url('assets/js/mdb.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/jquery.cookie.js');?>"> </script>
<script type="text/javascript" src="<?=base_url('assets/js/jquery.validate.min.js');?>"></script>
<!-- <script src="<?//=base_url('assets/js/datatables.min.js');?>"></script> -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
<script src="<?=base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>

<script src="<?=base_url('assets/js/select2.min.js');?>"></script>
<script src="<?=base_url('assets/js/accounting.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.easy-autocomplete.min.js');?>"></script>
<!-- custom script for your overall script -->
<script src="<?=base_url('assets/js/cp-toast.js');?>"></script>
<script src="<?=base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?=base_url('assets/js/custom.js');?>"></script>
<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>
<script src="<?=base_url('assets/js/globalfunctions.js');?>"></script>
<script src="<?= base_url(); ?>assets/js/alertify.js"></script>
<!-- uncomment this if you need charts -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="js/charts-home.js"></script> -->
<script src="<?=base_url('assets/js/front.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.toast.js');?>"></script>
<script src="<?=base_url('assets/js/jquery-code-scanner.js');?>"></script>
<script src="<?=base_url('assets/js/cropper.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.mask.min.js');?>"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="<?=base_url('assets/js/notification/auto_notification.js');?>"></script>
<!-- Datatable buttons -->


<!-- <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script> -->
</body>
</html>
