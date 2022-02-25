<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Settings_void_record/void_order/'.$token.'/'.$reference_num);?>">Void Record - Order List</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Order View</li>
        </ol>
    </div>

<div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
            <div class="card">
                    <div class="card-body">
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered table-grid table-item display nowrap" style="width:100%" id="table-item">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Item Name</th>
                                        <th>Qty</th>
                                        <th>Amount</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-info no-margin"></i> Order Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <label class="">Transaction Date:</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_order_date" class="green-text"><?=$order_details['date_ordered']?></label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label class="">Transaction Reference No.</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_order_reference_num" class="green-text"><?=$order_details['reference_num']?></label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label class="">Sub-Total</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_subtotal"><?=number_format($order_details['totamt'], 2)?></label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label class="">Shipping</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_shipping"><?=number_format($order_details['sf'], 2)?></label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label class="">Total Amount</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_amount" class="green-text"><?=number_format($order_details['totamt'] + $order_details['sf'], 2)?></label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label class="">Order Status</label>
                                </div>
                                <?php
                                    if($order_details['paystatus'] == 1){
                                        $order_status = draw_transaction_status($order_details['sales_order_status']);
                                    }else{
                                        $order_status = draw_transaction_status($order_details['orderstatus']);
                                    }
                                ?>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_order_status" class="green-text"><?=$order_status?></label>
                                </div>
                            </div>
                            <?php if($order_details['paystatus'] != 1){

                                  }else{
                            ?>
                            <div class="row grp_payment" id="grp_payment">
                                <div class="col-12 col-sm-3">
                                    <label class="">Payment Date</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_payment_date" class="green-text"><?=$order_details['payment_date']?></label>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <label class="">Payment Reference No.</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_payment_ref_num" class="green-text"><?=$order_details['paypanda_ref']?></label>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <label class="">Payment Status</label>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label id="tm_payment_status" class="green-text"><?=draw_transaction_status($order_details['paystatus'])?></label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

             <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-user no-margin"></i> Customer</h3>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-3">
                                <label class="">Name</label>
                            </div>
                            <div class="col-12 col-sm-8">
                                <label id="tm_name"><?=$order_details['name']?></label>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="">Contact Number</label>
                            </div>
                            <div class="col-12 col-sm-8">
                                <label id="tm_mobile"><?=$order_details['conno']?></label>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="" >Email</label>
                            </div>
                            <div class="col-12 col-sm-8">
                                <label id="tm_email" ><?=$order_details['email']?></label>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="" >Shipping Address</label>
                            </div>
                            <div class="col-12 col-sm-8">
                                <label id="tm_address"><?=$order_details['address']?></label>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="" >Notes/Landmark</label>
                            </div>
                            <?php
                                if($order_details['notes'] != ''){
                                    $notes = $order_details['notes'];
                                }else{
                                    $notes = '-';
                                }
                            ?>
                            <div class="col-12 col-sm-8">
                                <label id="tm_landmark"><?=$notes?></label>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="">Referral Code</label>
                            </div>
                            <?php
                                if(!empty($referral['referral_code'])){
                                    $referral_code = $referral['referral_code'];
                                }else{
                                    $referral_code = '-';
                                }
                            ?>
                            <div class="col-12 col-sm-8">
                                <label id="tm_referral_code"><?=$referral_code?></label>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="">Branch Name</label>
                            </div>
                            <?php 
                                if(empty($branch_details)){
                                    $branchname = 'Main';
                                }else{
                                    $branchname = $branch_details->branchname;
                                }
                            ?>
                            <div class="col-12 col-sm-8">
                                <label id="branchname"><?=$branchname?></label>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
        
        <?php 
            if($branch_details == null || $branch_details == "" || empty($branch_details)){
                $branch_id = 0;
            }else{
                $branch_id = $branch_details->branchid;
            }
        ?>
    <br>
        <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-info cancelBtn" id="backBtn">Close</button>

                    <?php if($this->loginstate->get_access()['void_record']['process'] == 1){ ?>
                        <button type="button" class="btn btn-primary waves-effect waves-light voidBtn" id="voidBtn" data-value="<?=$url_ref_num?>">Void</button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </form>
</div>


<!-- Void Modal-->
<div id="void_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_void_order" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title">Void Order</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <i class="fa fa-info no-margin">
                        </i> Order Summary
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Date:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_order_date"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Transaction Reference #:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_order_reference_num" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Total Amount:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_amount" class="green-text"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Payment Status</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_payment_status"></label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="">Order Status:</label>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label id="po_order_status"></label>
                    </div>
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="po_id" id="po_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-12 col-sm-12">
                        <small class="form-text">Reason </small>
                        <textarea class="form-control form-control-success reason" name="reason" id="reason"></textarea>
                </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-default cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" id="btn_tbl_confirm" class="btn btn-primary waves-effect waves-light" aria-label="Close">Void</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<input type="hidden" id="url_ref_num" value="<?=$url_ref_num?>">
<input type="hidden" id="reference_num" value="<?=$reference_num?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/settings/settings_void_order_view.js');?>"></script>