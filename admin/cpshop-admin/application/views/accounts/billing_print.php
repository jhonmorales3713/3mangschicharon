<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  .date_input{
    z-index: 9999 !important;
  }

  td, th {
    vertical-align: middle !important;
  }

  .thead-background{
    background-color: #555;
    color:#fff;
  }

  tbody tr td{
    font-size: 9px !important;
  }

  .font-attr-12{
    /* font-size:9px; */
    width: 300px !important;
  }

  .font-attr-8{
    /* font-size:8px; */
  }

  #btn_view_attachment{
    text-decoration: underline;
    color: #117a8b;
    /* border-color: #10707f; */
  }

  .active_pic{
    border: 1px solid #ef4131 !important;
  }

  .modal-lg{
    max-width: 1300px !important;
  }
</style>
<!-- <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"> -->
<!-- theme stylesheet--><!-- we change the color theme by changing color.css -->
<link rel="stylesheet" href="<?=base_url('assets/css/style.blue.css');?>" id="theme-stylesheet">
<link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
<!-- Custom stylesheet - for your changes-->
<!-- <link rel="stylesheet" href="<?=base_url('assets/css/custom.css');?>"> -->
<!-- Favicon-->
<link rel="shortcut icon" href="<?=favicon();?>">
<!-- Font Awesome CDN-->
<!-- you can replace it by local Font Awesome-->
<link rel="stylesheet" href="<?=base_url('assets/css/font-awesome.min.css');?>">
<!-- Font Icons CSS-->
<!-- Jquery Datatable CSS-->
<!-- <link rel="stylesheet" href="<?//=base_url('assets/css/datatables.min.css');?>"> -->
<!-- Jquery Select2 CSS-->
<link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css');?>">
<!-- Jquery Toast CSS-->
<link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/cp-toast.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/easy-autocomplete.css');?>">
<!-- <link rel="stylesheet" href="<?=base_url('assets/css/mdb.min.css');?>"> -->
<link rel="stylesheet" href="<?=base_url('assets/css/Chart.min.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/cropper.min.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/theme.css');?>">
<link rel="stylesheet" href="<?= base_url('assets/css/alertify.css'); ?>">
<link rel="stylesheet" href="<?=base_url('assets/css/bootstrap-datepicker3.min.css');?>">
<link href="<?=base_url('assets/fonts/fontawesome-free-5.14.0-web/css/fontawesome.css');?>">
<link href="<?=base_url('assets/fonts/fontawesome-free-5.14.0-web/css/all.css');?>">

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">

<div class="content-inner" id="pageActive" data-namecollapse="" data-labelname="Accounts" data-access = "<?=en_dec('en',$this->loginstate->get_access()['billing']['admin_view'])?>" data-ini = "<?=en_dec('en',ini())?>">
    <section class="tables">
        <div class="container-fluid">
            <!-- SUB BODY  -->
            <div id="sub_body">
              <div class="card">
                <div class="card-body">
                  <div class="col-12">
                      <table class="table wrap-btn-last-td table-striped table-hover table-bordered mb-2" id="table-item"  style="width:100%">
                        <thead class = "thead-background">
                            <tr>
                              <?php if($this->loginstate->get_access()['billing']['admin_view'] == 1):?>
                                <th class = "font-attr-12 thead-background" style = "width:100px;">Fulfillment Date</th>
                                <th class = "font-attr-12 thead-background" style = "width:100px;">Order Ref #</th>
                                <th class = "font-attr-12 thead-background" style = "width:100px;">Payment Ref #</th>
                                <th class = "font-attr-12 thead-background" style = "width:100px;">Order Type</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Amount</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Voucher Amount</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Amount w/ Voucher</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Delivery Amount</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Refcom Amount</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Process Fee</th>
                                <th class = "font-attr-12 thead-background" style = "width:55px;text-align:right;">Net Amount</th>
                              <?php else:?>
                                <th class = "font-attr-12 thead-background" style = "width:125px;">Fulfillment Date</th>
                                <th class = "font-attr-12 thead-background" style = "width:125px;">Order Ref #</th>
                                <th class = "font-attr-12 thead-background" style = "width:125px;">Payment Ref #</th>
                                <!-- <th class = "font-attr-12 thead-background">Order Type</th> -->
                                <th class = "font-attr-12 thead-background" style = "width:100px;text-align:right;">Amount</th>
                                <!-- <th class = "font-attr-12 thead-background">Voucher Amount</th> -->
                                <!-- <th class = "font-attr-12 thead-background">Amount w/ Voucher</th> -->
                                <th class = "font-attr-12 thead-background" style = "width:100px;text-align:right;">Delivery Amount</th>
                                <!-- <th class = "font-attr-12 thead-background">Refcom Amount</th> -->
                                <th class = "font-attr-12 thead-background" style = "width:100px;text-align:right;">Process Fee</th>
                                <th class = "font-attr-12 thead-background" style = "width:100px;text-align:right;">Net Amount</th>
                              <?php endif;?>
                            </tr>
                        </thead>

                        <tbody>
                          <?php foreach($billing_breakdown as $bill):?>
                            <tr>
                              <?php if($this->loginstate->get_access()['billing']['admin_view'] == 1):?>
                                <td class = "font-attr-8" style = "width:100px;"><?=$bill['trandate']?></td>
                                <td class = "font-attr-8" style = "width:100px;"><?=$bill['reference_num']?></td>
                                <td class = "font-attr-8" style = "width:100px;"><?=$bill['payrefnum']?></td>
                                <td class = "font-attr-8" style = "width:100px;"><?=$bill['order_type']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['amount']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['voucher_amount']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['total_amount_w_voucher']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['shippingfee']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['refcom_totalamount']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['processfee_only']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:55px;"><?=$bill['netamount']?></td>
                              <?php else:?>
                                <td class = "font-attr-8" style = "width:125px;"><?=$bill['trandate']?></td>
                                <td class = "font-attr-8" style = "width:125px;"><?=$bill['reference_num']?></td>
                                <td class = "font-attr-8" style = "width:125px;"><?=$bill['payrefnum']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:100px;"><?=$bill['total_amount_w_voucher']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:100px;"><?=$bill['shippingfee']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:100px;"><?=$bill['processfee_plus_refcom']?></td>
                                <td class = "font-attr-8" align = "right" style = "width:100px;"><?=$bill['netamount']?></td>
                              <?php endif;?>
                            </tr>
                          <?php endforeach;?>
                        </tbody>
                      </table>
                      <br>
                      <hr>
                      <div class="row mt-2">
                          <table class="table table-striped table-inverse table-responsive">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Transaction Date</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_trandate"><?=$billing_trandate?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Billing Number</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_billno" class="green-text"><?=$billing_billno?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Billing Code</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_billcode" class="green-text"><?=$billing_billcode?></label></td>
                                    </tr>

                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Total Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_totalamount" class="green-text"><?=number_format($billing_totalamount,2)?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Delivery Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_delivery_amount" class="green-text"><?=number_format($billing_deliveryamount,2)?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Total Amount w/ Delivery Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_totalamount_w_shipping" class="green-text"><?=number_format(floatval($billing_totalamount) + floatval($billing_deliveryamount),2)?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Processing Fee</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_processfee" class="green-text"><?=number_format(floatval($billing_processfee) + floatval($billing_totalcomrate),2)?></label></td>
                                    </tr>
                                    <!-- <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Refcom Total Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_refcom_totalamount" class="green-text"></label></td>
                                    </tr> -->
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Net Amount</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_netamount" class="green-text"><?=number_format(floatval($billing_netamount) + floatval($billing_deliveryamount),2)?></label></td>
                                    </tr>
                                </tbody>
                          </table>
                      </div>
                      <?php if($billing_paystatus == 'Settled'):?>
                        <div class="row grp_payment" id="grp_payment">
                            <table class="table table-striped table-inverse table-responsive">
                                  <tbody>
                                      <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Date Settled</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_paiddate"><?=$billing_paiddate?></label></td>
                                      </tr>
                                      <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Amount Settled</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_paidamount" class="green-text"><?=number_format($billing_paidamount)?></label></td>
                                      </tr>
                                      <!-- <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Payment Type</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_paytype"></label></td>
                                      </tr> -->
                                      <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Payment Reference Number</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_payref"><?=$billing_payref?></label></td>
                                      </tr>
                                      <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Remarks</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_payremarks"><?=$billing_payremarks?></label></td>
                                      </tr>
                                      <!-- <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Attachment</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_payattach" class="green-text"></label></td>
                                      </tr> -->

                                      <!-- <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Billing Status</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_paystatus" class="green-text"></label></td>
                                      </tr>
                                      <tr>
                                          <td class="w-50"><label class="text-xs md:text-base">Billing Note</label></td>
                                          <td class="w-50"><label class="text-xs md:text-base" id="tm_remarks"></label></td>
                                      </tr> -->
                                  </tbody>
                            </table>
                        </div>
                      <?php endif;?>
                      <hr>
                      <br>
                      <div class="row">
                          <table class="table table-striped table-inverse table-responsive">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Shop Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_syshop"><?=$shopname?></label></td>
                                    </tr>

                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Branch Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_branchname"><?=$branchname?></label></td>
                                    </tr>

                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Account No</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_accountno"><?=$accountno?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Account Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_accountname"><?=$accountname?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="w-50"><label class="text-xs md:text-base">Bank Name</label></td>
                                        <td class="w-50"><label class="text-xs md:text-base" id="tm_bankname"><?=$bankname?></label></td>
                                    </tr>
                                </tbody>
                          </table>
                      </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </section>

</div>
