<style>
    .pac-container {
    z-index: 1051 !important;
    }
    .modal{
        z-index: 1050;   
    }
</style>
<?php
    $vouchercodeArr = array();
    $vouchertotal = 0;
    if(!empty($voucher_details)){
        foreach($voucher_details as $row){
            $vouchercodeArr[] = $row['vouchercode'];
            $vouchertotal += $row['voucheramount'];
        }

        $vouchercodeArr = (empty($vouchercodeArr)) ? 'None' : implode(", ", $vouchercodeArr);
    }else{
        $vouchercodeArr = 'None';
        $vouchertotal   = 0;
    }

    $sub_total      = max($order_details['totamt'] - $vouchertotal, 0);
    $order_status   = ($order_details['paystatus'] == 1) ? draw_transaction_status($order_details['sales_order_status']) : $order_status = draw_transaction_status($order_details['orderstatus']);
    $order_status   = ($order_details['paystatus'] == 0) ? draw_transaction_status('Unpaid') : $order_status;
    $notes          = ($order_details['notes'] != '') ? $order_details['notes'] : 'None';;
    $referral_code  = (!empty($referral['referral_code'])) ? $referral['referral_code'] : 'None';
    $payment_notes  = ($order_details['payment_notes'] != '') ? $order_details['payment_notes']:'None';
    $shipping_note  = ($order_details['shipping_note'] != '' || !empty($order_details['shipping_note'])) ? $order_details['shipping_note']:'None';
    
    $order_details['merch_referral_code']  = (!empty($order_details['merch_referral_code'])) ? $order_details['merch_referral_code']:' ';
    if(empty($branch_details)){
        $branchname          = 'Main';
        $pickup_address      = (empty($order_details['pickup_address']) ? 'None' : $order_details['pickup_address']);
        $toktokreferral_code = ($order_details['merch_referral_code'] == '') ? '' : $order_details['merch_referral_code'];
    }else{
        $branchname          = $branch_details->branchname;
        $pickup_address      = $branch_details->address;
        $toktokreferral_code = ($order_details['merch_referral_code'] == '') ? '' : $order_details['merch_referral_code'];
    }
    $toktokreferral_code = str_replace(' ', '', $toktokreferral_code);
    $latitude  = ($order_details['lati'] == '') ? '0' : $order_details['lati'];
    $longitude = ($order_details['longi'] == '') ?  '0' : $order_details['longi'];

    $payment_type = ($order_details['payment_method'] == '') ? 'Tagged as Paid(Admin)' : $order_details['payment_method'];

    $total_refund_amount = 0;
    if(!empty($refunded_order)){
        foreach($refunded_order as $refund){
            $total_refund_amount += $refund['amount'];
        }
    }

    $total_item_amount = 0;
    foreach($order_items as $row){
        $total_item_amount += $row['total_amount'];
    }

    $refunded_all = ($total_refund_amount == $total_item_amount) ? 1:0;
    $actual_shipping_fee = ($order_details['shipping_cost'] != '' || $order_details['shipping_cost'] != 0) ? number_format($order_details['shipping_cost'], 2) : 'Not Provided';
    $shippingpartner     = ($order_details['shippingpartner'] != '') ? $order_details['shippingpartner'] : 'Not Provided';
    $shipping_ref        = ($order_details['shipping_ref'] != '') ? $order_details['shipping_ref'] : 'Not Provided';

    if(c_international() == 1 && $shopid == 0){

        $total_amount_item              = 0;
        foreach($getOrderLogs as $val){
            $total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
        }
        $voucher_total_amount_conv     = currencyConvertedRate($vouchertotal, $order_details['currval']);
        $subtotal_amount_conv          = max($total_amount_item - $voucher_total_amount_conv, 0);
        $delivery_amount_conv          = currencyConvertedRate($order_details['sf'], $order_details['currval']);
        $total_refunded_amount_conv    = currencyConvertedRate($total_refund_amount, $order_details['currval']);
                
        $sub_total_converted           = displayCurrencyValue_withPHP($sub_total, $subtotal_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = displayCurrencyValue_withPHP($vouchertotal, $voucher_total_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = (floatval($vouchertotal) == floatval(0)) ? 'None' : $vouchertotal_converted;
        $shipping_fee_converted        = (floatval($order_details['sf'] != floatval(0))) ? displayCurrencyValue_withPHP($order_details['sf'], $delivery_amount_conv, $order_details['currcode']):'Free Shipping';
        $shipping_fee_converted        = (strpos($notes, '|::PA::|') !== false) ? 'For Pickup' : $shipping_fee_converted;
        $total_amount_converted        = displayCurrencyValue_withPHP(max(($sub_total + $order_details['sf']) - $total_refund_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $order_details['currcode']);
        $total_refund_amount_converted = displayCurrencyValue_withPHP($total_refund_amount, $total_refunded_amount_conv, $order_details['currcode']);
    }
    else if(c_international() == 1 && $shopid != 0){
        $total_amount_item              = 0;
        foreach($getOrderLogs as $val){
            $total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
        }
        $voucher_total_amount_conv     = currencyConvertedRate($vouchertotal, $order_details['currval']);
        $subtotal_amount_conv          = max($total_amount_item - $voucher_total_amount_conv, 0);
        $delivery_amount_conv          = currencyConvertedRate($order_details['sf'], $order_details['currval']);
        $total_refunded_amount_conv    = currencyConvertedRate($total_refund_amount, $order_details['currval']);
                
        $sub_total_converted           = displayCurrencyValue($sub_total, $subtotal_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = displayCurrencyValue($vouchertotal, $voucher_total_amount_conv, $order_details['currcode']);
        $vouchertotal_converted        = (floatval($vouchertotal) == floatval(0)) ? 'None' : $vouchertotal_converted;
        $shipping_fee_converted        = (floatval($order_details['sf'] != floatval(0))) ? displayCurrencyValue_withPHP($order_details['sf'], $delivery_amount_conv, $order_details['currcode']):'Free Shipping';
        $total_amount_converted        = displayCurrencyValue(max(($sub_total + $order_details['sf']) - $total_refund_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $order_details['currcode']);
        $total_refund_amount_converted = displayCurrencyValue($total_refund_amount, $total_refunded_amount_conv, $order_details['currcode']);
    }
    else{
        $sub_total_converted           = number_format($sub_total, 2);
        $vouchertotal_converted        = $vouchertotal;
        $vouchertotal_converted        = (floatval($vouchertotal) == floatval(0)) ? 'None' : number_format($vouchertotal_converted, 2);
        $shipping_fee_converted        = (floatval($order_details['sf'] != floatval(0))) ? number_format($order_details['sf'], 2):'Free Shipping';
        $total_amount_converted        = number_format(max(($sub_total + $order_details['sf']) - $total_refund_amount, 0), 2);
        $total_refund_amount_converted = number_format($total_refund_amount, 2);
    }
    $special_upper = ["&NTILDE", "&NDASH",'|::PA::|'];
    $special_format   = ["&Ntilde", "&ndash",''];
    $order_details['name']= str_replace($special_upper, $special_format, $order_details['name']);
    $order_details['address']= str_replace($special_upper, $special_format, $order_details['address']);
    
?>

<div class="content-inner" id="pageActive" data-num="2" data-namecollapse="" data-labelname="Order View"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>">Orders</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>

            <?php if($order_status_view == 'all'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/orders/'.$token);?>">Order List</a></li>
            <?php }else if($order_status_view == 'pending'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/pending_orders/'.$token);?>">Pending Order List</a></li>
            <?php }else if($order_status_view == 'paid'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/paid_orders/'.$token);?>">Paid Order List</a></li>
            <?php }else if($order_status_view == 'readyforprocessing'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/readyforprocessing_orders/'.$token);?>">Ready for Processing Order List</a></li>
            <?php }else if($order_status_view == 'processing'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/processing_orders/'.$token);?>">Processing Order List</a></li>
            <?php }else if($order_status_view == 'readyforpickup'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/readyforpickup_orders/'.$token);?>">Ready for Pickup Order List</a></li>
            <?php }else if($order_status_view == 'bookingconfirmed'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/bookingconfirmed_orders/'.$token);?>">Booking Confirmed Order List</a></li>
            <?php }else if($order_status_view == 'fulfilled'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/fulfilled_orders/'.$token);?>">Fulfilled Order List</a></li>
            <?php }else if($order_status_view == 'shipped'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/shipped_orders/'.$token);?>">Shipped Order List</a></li>
            <?php }else if($order_status_view == 'returntosender'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/returntosender_orders/'.$token);?>">Return to Sender Order List</a></li>
            <?php }else if($order_status_view == 'forpickup'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/forpickup_orders/'.$token);?>">For Pick up Order List</a></li>
            <?php }else if($order_status_view == 'confirmed'){ ?>
                <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/confirmed_orders/'.$token);?>">Delivery Confirmed Order List</a></li>
            <?php } ?>

            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Main_orders/orders_view/'.$token.'/'.$url_ref_num.'/'.$order_status_view);?>">View Orders</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Modify Orders</li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row flex-md-row-reverse">
     
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col d-flex align-items-center">
                                        <h3 class="card-title">
                                            Transaction Reference # <?=$reference_num?>
                                        </h3>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                            <div class="table-responsive">
                                <table class='table table-striped table-hover table-bordered table-grid display nowrap' id="producttable">
                                    <thead class="mainthead">
                                        <tr>
                                            <th scope='col' width="40%"><b>Item Name</b></th>
                                            <th scope='col' width="15%"><b>Edit Quantity</b></th>
                                            <th scope='col' width="15%"><b>Quantity</b></th>
                                            <th scope='col' width="15%"><b>Amount</b></th>
                                            <th scope='col' width="15%"><b>Total Amount</b></th>
                                            <th scope='col' width="15%"><b>Action</b></th>
                                        </tr>
                                    </thead>
                                    <tbody id='tbody_product' class='tbody_product maintbody'>
                            
                                    </tbody>
                                    <!-- <tfoot>
                                        <tr>
                                        <td colspan = "11"><span id="number_of_check">0</span> selected check box out of <span id="total_row">0</span> </td>
                                        </tr>
                                    </tfoot> -->
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card detail-container">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Order Details</h3>
                            </div>
                            <div class="card-body px-lg-5">
                                <div class="">
        
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="">Transaction Date:</label>
                                            <label id="tm_order_date" class="green-text font-weight-bold"><?=$order_details['date_ordered']?></label>
                                        </div>
                                    
                                        <div class="col-12 col-md-6">
                                            <label class="">Transaction Reference No.:</label>
                                            <label id="tm_order_reference_num" class="green-text font-weight-bold"><?=$order_details['reference_num']?></label>
                                        </div>
                                       
                                        <div class="col-12 col-md-6">
                                            <label class="">Voucher:</label>
                                            <label id="tm_voucher" class="font-weight-bold"><?=$vouchercodeArr?></label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Sub-Total:</label>
                                            <label id="tm_subtotal" class="font-weight-bold">0.00</label>
                                        </div> 

                                        <div class="col-12 col-md-6">
                                            <label class="">Voucher Total:</label>
                                            <label id="tm_vouchertotal" class="font-weight-bold"><?=$vouchertotal_converted?></label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Shipping:</label>
                                            <label id="tm_shipping" class="font-weight-bold"><?=$shipping_fee_converted;?></label>
                                        </div>
                                  
                                        <div class="col-12 col-md-6">
                                            <label class="">Total Amount:</label>
                                            <label id="tm_total_amount" class="green-text font-weight-bold">0.00</label>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="">Refunded Amount:</label>
                                            <label id="tm_refund_amount" class="green-text font-weight-bold">0.00</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php if(!empty($refunded_order)){?>
                        <div class="col-12 mb-4">
                                <div class="card detail-container">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                                    Refunded Order
                                        </h3>
                                    </div>
                                    <div class="card-body px-lg-5">
                                        <div class="">
                                            <div class="row">

                                                <?php if(!empty($refunded_order)){?>
                                                    <?php foreach($refunded_order as $refund){?>
                                                        <div class="col-md-6" style="font-weight: normal">
                                                            <span><u><?=$refund['name_of_item']?></u></span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <span><?=number_format($refund['quantity'], 2)?></span>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span><?=number_format($refund['amount'], 2)?></span>
                                                        </div>
                                                        <!-- <div class="w-100" style="border-bottom: 1px dotted black;"></div> -->
                                                    <?php } ?>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php }?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-12 text-right col-md-auto px-1 mb-3">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn-mobile-w-100 btn btn-outline-secondary backBtn mb-2 mb-md-0" id="backBtn">Close</button>
                        <?php if($order_details['sales_order_status'] == 'p' && $this->loginstate->get_access()['transactions']['modify'] == 1 && $refunded_all == 0){ ?>
                            <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light modifyBtn mb-2 mb-md-0" id="modifyBtn" data-value="<?=$url_ref_num?>">Save Changes</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
        

        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
        </div>
    </form>
</div>

<!--  Modify Order Modal-->
<div id="modifyOrder_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Modify Order</h4>
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
                </div>
                <div class="row hidden">
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input type="text" name="po_id" id="po_id" class="form-control" value="0" >
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="savesChangesConfirm" class="btn btn-success waves-effect waves-light" aria-label="Close">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- deleteProduct -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Remove Product</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this product? This action cannot be reversed.</p>
                <input type="hidden" id="deleteProductArrayIndex">
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="removeProductConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="url_ref_num" value="<?=$url_ref_num?>">
<input type="hidden" id="shop_status" value="<?=$status_shop?>">
<input type="hidden" id="sys_shop" value="<?=$mainshopid?>">
<input type="hidden" id="reference_num" value="<?=$reference_num?>">
<input type="hidden" id="total_refund_amount" value="<?=$total_refund_amount?>">
<input type="hidden" id="order_shipping_amount" value="<?=$order_details['sf']?>">
<input type="hidden" id="order_sub_total" value="<?=$sub_total?>">
<input type="hidden" id="order_vouchertotal" value="<?=$vouchertotal?>">
<input type="hidden" id="order_status_view" value="<?=$order_status_view?>">



<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->

<script type="text/javascript" src="<?=base_url('assets/js/orders/orders_modify.js');?>"></script>
