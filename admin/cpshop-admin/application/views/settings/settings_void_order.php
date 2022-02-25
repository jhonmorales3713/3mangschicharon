<style>
    a.custom-card:hover {
      color: inherit;
      background-color: black;
    }    
</style>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Void Record - Order List</li>
        </ol>
    </div>
</div>

<section class="tables">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="">
                    <div class="col-lg-12 padding-0_mobile" style="padding-left: 60px;padding-right: 60px;">
                        <div class="card">
                            <br>
                            <div class="col-lg-12 padding-0_mobile">

                                <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th width="150">Order</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Shipping</th>
                                            <th>Total</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                            <th>Shop</th>
                                            <th>Branch</th>
                                            <th width="30">Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<input type="hidden" value="<?=$reference_num?>" id="reference_num">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_void_order.js');?>"></script>
<!-- end - load the footer here and some specific js -->

