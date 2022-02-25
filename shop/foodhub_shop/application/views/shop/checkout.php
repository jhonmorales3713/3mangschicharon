<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    @media only screen and (max-width: 991px) {
        footer {
            display: none;
        }
    }

    .has-error {
        border: 1px solid #ff0000 !important;
    }

    .btn-payment_type{
      background-color: #ef4131;
      border:1px solid #ef4131;
      color:#fff;
      border-radius: 3px;
      font-weight: bold;
      font-size:18px;
      height:50px;
    }

    .btn-payment_type_panda{
      background-color: #fff;
      border: 2px solid #9acd32;
      color:#222;
      border-radius: 3px;
      font-weight: bold;
      font-size:18px;
      height:50px;
    }

    .btn-img{
      /* float:left; */
      /* object-fit: contain; */
      height: 28px;
      /* width: 28px; */
    }

    .cod-logo{
      /* float:left; */
      font-size: 20px;
      color:#fff;
    }

    @media screen and (max-width: 368px){
      .btn-payment_type_panda{
        height:75px;
      }

      .btn-payment_type{
        height:75px;
      }

      .checkout-res{
        display: block;
      }
    }

    @media screen and (max-width: 390px){
      .p-name{
        font-size:11px;
      }

      .product-price{
        font-size:11px;
      }
    }

    .autocomplete-item {
      list-style-type: none;
      padding: 10px;
      background-color: white;
      border-bottom: 1px solid gainsboro;
    }

    .autocomplete-item:hover{
      cursor:pointer;
      background-color: gainsboro;
    }

    .select2-container--default .select2-selection--single{
        background-color: #eee !important;    
    }

</style>

<div class="content-inner" id="pageActive" data-num="1" data-page="checkout" data-allow_voucher = "<?=allow_voucher()?>"></div>
<div id="pageActiveMobile" data-num="6"></div>
<div id="headerTitle" data-title="Place Order" data-search="false"></div>
<style>
  footer {
    padding-top: 2em !important;
    position: relative;
  }
</style>
<div class="checkout-page shop-container__web mb-5">
  <div class="portal-table col-12">
    <nav aria-label="breadcrumb ">
      <ol class="breadcrumb ">
          <li class="breadcrumb-item"><a href="<?=base_url('');?>">Shop</a></li>
          <li class="breadcrumb-item active" aria-current="page">Checkout</li>
      </ol>
    </nav>
    <div class="row">
        <?php
            $reseller = '';
            $referral = '';
            if (!empty($this->session->userdata("user_id")) && $this->session->userdata("user_type") == "JC") {
                $reseller = en_dec('en', $this->session->userdata("user_id"));
            }

            if (!empty($this->session->userdata('referral')) && $this->session->userdata('referral') != '') {
                $referral = en_dec('en', $this->session->userdata("user_id"));
            }
        ?>
        <div class="col-lg-7 col-12 checkout__order mb-4">
            <div class="portal-table h-100">
                <h6 class="mb-4 receiver__title" id="ordersLabel"><i class="fa fa-shopping-cart mr-3"></i>Your Orders</h6>
                <div id="checkoutPage" data-reseller='<?php echo $reseller; ?>' data-referral='<?php echo $referral; ?>'>
                    <!-- Order Summary Here -->
                </div>
                <?php if($this->session->userdata('referral') != '') {?>
                    <div class="product-card" id="referral-card">
                        <div class="product-card-body py-3 product-card-total">
                            <div class="row no-gutters mb-2">
                                <div class="col product-card-name text-right">
                                    Referral Code
                                </div>
                                <div class="col-3" >
                                </div>
                                <div class="col-4 col-md-2">
                                    <?= $this->session->userdata('referral');?> <a data-toggle="tooltip" data-placement="top" title="Verified Code"><i class="fa fa-check-circle" style="color:var(--green);" aria-hidden="true"></i></a>
                                </div>
                                <div hidden class="col-5 col-md-3">
                                    <input id="checkout_code" name="checkout_code" class="detail-input" type="text" value="<?= $this->session->userdata('referral');?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else {?>
                    <div hidden class="product-card" id="referral-card">
                        <div class="product-card-body py-3 product-card-total">
                            <div class="row no-gutters mb-2">
                                <div class="col product-card-name text-right">
                                    Referral Code <a data-toggle="tooltip" data-placement="top" title="Got a referral code from a friend or colleague? Please enter it here."><i class="fa fa-info-circle"></i></a>
                                </div>
                                <div class="col-1" >
                                </div>
                                <div class="col-5 col-md-3">
                                    <input id="checkout_code" name="checkout_code" class="detail-input" type="text" placeholder="Enter code here...">
                                </div>
                                <div class="col-1">
                                    <div class="" id="verifiedCode" hidden>
                                        <div class="">
                                            <a data-toggle="tooltip" data-placement="top" title="Verified Code"><i class="fa fa-check-circle" style="color:var(--green);" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="product-card" id="item-total-card">
                    <div class="product-card-body py-3 product-card-total">
                        <div class="row no-gutters mb-2">
                            <div class="col product-card-name text-right">
                                Sub-total
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price" id="sub_total_amount_checkout">
                                    <span class="ml-2 spinner-border spinner-border-sm"></span>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="col product-card-name text-right">
                                Shipping Fee <a data-toggle="tooltip" data-placement="top" title="Shipping fee will be calculated based on delivery area."><i class="fa fa-exclamation-circle" aria-hidden="true" id=""></i></a>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="product-card-price" id="shipping_amount_checkout">
                                    <small style="font-size:12px; font-weight: bold; color:var(--gray);">Calculated at next step</small>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="product-card-delete">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h6 hidden class="mb-2 receiver__title" id="unavailableOrdersLabel"><i class="fa fa-exclamation-triangle mr-3"></i>Order cannot proceed</h6>
                <div hidden class="confirmationBox" id="unavailableOrdersNote" style="background-color: #ff4d4d;">
                    <p style="color: #ffffff;">
                    NOTE: You ordered products that are currently unavailable on your selected location. Kindly select your location on "Ship to" dropdown list found in Home Page to filter available products on your area.
                    </p>
                </div>
                <div hidden class="confirmationBox" id="unavailableOrdersNote2" style="background-color: #ff4d4d;">
                    <p style="color: #ffffff;">
                    NOTE: You ordered products that are currently unavailable on your selected location. Kindly select your location on "Ship to" dropdown list found in Home Page to filter available products on your area.
                    </p>
                </div>
                <div hidden disabled id="checkoutPageUnavailable">
                    <!-- Unavailable Order Summary Here -->
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <form id="contact_details_form">
                <h6 class="mb-4 receiver__title"><i class="fa fa-user mr-1 mr-3"></i>Contact Details</h6>
                <!-- <h6 class="mb-2 receiver__title"><i class="fa fa-user mr-1"></i>Contact Details</h6>
                <div class="mb-2 text-right col-11">Already have an account? <a href="" style="color:var(--blue)">Sign In</a></div> -->
                <div class="detail-card receiver-detail-container mb-4">
                    <div class="mb-2">
                        <input required id="checkout_email" name="checkout_email" class="email_input detail-input" type="email" placeholder="Email Address" value="<?= $this->session->userdata('email'); ?>" style="text-transform: uppercase;">
                    </div>
                    <div class="mb-2">
                        <input required id="confirm_checkout_email" name="confirm_checkout_email" class="email_input detail-input" type="text" placeholder="Retype Email Address" value="<?= $this->session->userdata('email'); ?>" style="text-transform: uppercase;">
                    </div>
                    <div class="mb-2">
                        <input required id="checkout_conno" name="checkout_conno" class="detail-input numberInput" type="text" placeholder="11 Digit Mobile Number (Format: 09122333444)" value="<?= $this->session->userdata('receiver_conno') != "" ? $this->session->userdata('receiver_conno') : $this->session->userdata('conno'); ?>"  style="text-transform: uppercase;">
                    </div>
                    <div class="mb-2">
                        <input required id="confirm_checkout_conno" name="checkout_conno" class="detail-input numberInput" type="text" placeholder="Retype Mobile Number (Format: 09122333444)" value="<?= $this->session->userdata('receiver_conno') != "" ? $this->session->userdata('receiver_conno') : $this->session->userdata('conno'); ?>"  style="text-transform: uppercase;">
                    </div>
                    <!-- <div class="mb-2">
                        <input type="checkbox" class="form-control-check" name="newsletterBox" id="newsletterBox"> Keep me up to date on news and exclusive offers via email
                    </div> -->
                </div>
                <h6 class="mb-4 receiver__title"><i class="fa fa-truck mr-1 mr-3"></i>Shipping Details</h6>
                <div class="detail-card receiver-detail-container">
                    <div class="mb-2">
                        <input required id="checkout_name" name="checkout_name" class="detail-input" type="text" placeholder="Receiver's Name" value="<?= $this->session->userdata('receiver_name') != "" ? $this->session->userdata('receiver_name') : ""; ?>" style="text-transform: uppercase;">
                    </div>
                    <?php if(allow_google_addr() == 1):?>
                      <div class="form-group mb-2">
                          <!-- <div id="pac-container"> -->
                              <!-- <label for="">Location <span class="asterisk"></span></label> -->
                              <!-- <input required id="checkout_address" type="text" placeholder="Address (House #, Street,Village)" class = "form-control pr_field detail-input" name = "checkout_address" style = "height:37px;font-size:12px !important;text-transform: uppercase;"> -->
                              <!-- <input type="hidden" name = "loc_latitude" id = "loc_latitude" class = "pr_field" value = ""> -->
                              <!-- <input type="hidden" name = "loc_longitude" id = "loc_longitude" class = "pr_field" value = ""> -->
                          <!-- </div> -->
                          <!-- <div id="map" style = "height:0px;margin-top:0px;"></div> -->
                          <!-- <div id="infowindow-content" class = "d-none"> -->
                              <!-- <img src="" width="16" height="16" id="place-icon"> -->
                              <!-- <span id="place-name"  class="title"></span><br> -->
                              <!-- <span id="place-address"></span> -->
                          <!-- </div> -->
                          <div class="autocomplete-input-container">
                            <div class="autocomplete-input">
                              <input required id="checkout_address" placeholder="Address (House #, Street,Village)" autocomplete="off" class = "form-control pr_field detail-input" name = "checkout_address" style = "height:37px;font-size:12px !important;text-transform: uppercase;">
                              <input type="hidden" name = "loc_latitude" id = "loc_latitude" class = "pr_field" value = "">
                              <input type="hidden" name = "loc_longitude" id = "loc_longitude" class = "pr_field" value = "">
                            </div>
                            <ul class="autocomplete-results d-none" style = "box-shadow: 1px 5px 8px #9999;padding:0;">
                            </ul>
                          </div>
                          <div id="map" style = "height:0px;margin-top:0px;"></div>
                      </div>
                    <?php else:?>
                      <textarea required id="checkout_address" name="checkout_address" class="detail-input" name="" id="" cols="30" rows="2" placeholder="Address (House #, Street,Village)" style="text-transform: uppercase;"><?= ($this->session->userdata('receiver_address') != "") ? $this->session->userdata('receiver_address') : $this->session->userdata('address'); ?></textarea>
                    <?php endif;?>
                    <div class="row">
                        <div class="mb-2 col-12 col-lg-12">
                            <?php if($this->session->userdata('get_shipping_locs')==''){?>
                                <select required class="detail-input select2" name="" id="citymunCode">
                                    <option value="">SELECT CITY</option>
                                    <?php foreach($get_city as $city){ ?>
                                        <option value="<?=$city['citymunCode']?>" data-citymunDesc = "<?=$city['citymunDesc']?>" data-provCode = "<?=$city['provCode']?>"  <?php if ($this->session->receiver_municipality_id == $city['citymunCode']) echo ' selected="selected"'; ?>><?=$city['citymunDesc'].", ".$city['provDesc']?></option>
                                    <?php } ?>
                                </select>
                            <?php }else{ $arr = explode(",",$this->session->userdata('get_shipping_locs')) ?>
                                <select required class="detail-input select2" name="" id="citymunCode" disabled>
                                <option value="<?=$arr[1]?>" data-citymunDesc = "<?=$arr[1]?>" data-provCode = "<?=$arr[2]?>"  selected><?=$arr[4].", ".$arr[5]?></option>
                                <option value="<?=$arr[1]?>" data-citymunDesc = "<?=$arr[1]?>" data-provCode = "<?=$arr[2]?>"  selected><?=$arr[4].", ".$arr[5]?></option>
                                   
                                </select>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-2 col-6 col-lg-6">
                            <select required class="detail-input select2" name="checkout_countryid" id="checkout_countryid" disabled>
                                <option value="">PHILIPPINES</option>
                            </select>
                        </div>
                        <div class="mb-2 col-6 col-lg-6">
                            <input id="checkout_postal" name="checkout_postal" class="detail-input" type="text" placeholder="Postal Code (Optional)" value="<?= $this->session->userdata('receiver_postal_code') != "" ? $this->session->userdata('receiver_postal_code') : $this->session->userdata('postal'); ?>"  style="text-transform: uppercase;">
                        </div>
                    </div>
                    <textarea id="instructions" name="instructions" class="detail-input" name="" id="" cols="30" rows="3" placeholder="Landmarks/Exact Address/ Notes to Rider (Optional)" style="text-transform: uppercase;"><?= $this->session->userdata('receiver_landmark') != "" ? $this->session->userdata('receiver_landmark') : $this->session->userdata('landmark'); ?></textarea>
                    <!-- <div class="mb-2">
                        <input type="checkbox" class="form-control-check" name="createAccountBox" id="createAccountBox"> Save this information and create a <?=get_company_name();?> account
                    </div> -->
                </div>
            </form>
            <div hidden id="shipping_details" class="mb-4">
                <h6 class="mb-4 receiver__title"><i class="fa fa-truck mr-1 mr-3"></i>Ship To</h6>
                <div class="detail-card receiver-detail-container">
                    <small>Email</small>
                    <div class="mb-2">
                        <input disabled id="checkout_email-s" name="checkout_email-s" class="detail-input" type="email" value="">
                    </div>
                    <small>Name</small>
                    <div class="mb-2">
                        <input disabled id="checkout_name-s" name="checkout_name-s" class="detail-input" type="text" value="" style="text-transform: uppercase;">
                    </div>
                    <small>Address</small>
                    <textarea disabled id="checkout_address-s" name="checkout_address-s" class="detail-input mb-2" name="" id="" cols="30" rows="2" style="text-transform: uppercase;"></textarea>
                    <?php if(allow_registration() == 1 && !isset($this->session->user_id)):?>
                      <div class="mb-2">
                        <div class="row">
                          <div class="col-1">
                            <input type="checkbox" id = "register_upon_checkout" name="register_upon_checkout" value="" style = "margin-top:3px;">
                          </div>
                          <div class="col-11 pl-0">
                            <span class = "text-bold" style = "font-weight:bold !important;">Register me upon checkout </span>
                          </div>
                        </div>
                      </div>
                    <?php endif;?>
                </div>
            </div>
            <div hidden class="product-card" id="referral-card-s">
                <div class="product-card-body py-3 product-card-total">
                    <div class="row no-gutters mb-2">
                        <div class="col product-card-name text-right">
                            Referral Code
                        </div>
                        <div class="col-2" >
                        </div>
                        <div class="col-5 col-md-6">
                            <?php if($this->session->userdata('referral') != '') {?>
                                <?= $this->session->userdata('referral');?> <a data-toggle="tooltip" data-placement="top" title="Verified Code"><i class="fa fa-check-circle" style="color:var(--green);" aria-hidden="true"></i></a>
                            <?php } else { ?>
                                <input disabled class="detail-input" id="checkout_code-s" type="text">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div hidden class="detail-card" id="confirmationBox">
                <div class="confirmationBox">
                    <p>
                    <input type="checkbox" class="form-control-check" name="signatureBox" id="signatureBox">
                    I hereby confirm that all order details and delivery information are correctly entered. Furthermore, I have read the and agreed with the <a style="color:var(--blue)" href="<?=terms_and_condition();?>" target="_blank" >terms and conditions</a> of this service.
                    </p>
                </div>
            </div> -->

            <div hidden id="remove_shop_details">
                <h6 class="mb-4 receiver__title"><i class="fa fa-exclamation-triangle mr-3"></i>Confirmation</h6>
                <div class="detail-card receiver-detail-container">
                    <div class="confirmationBox">
                        <p>
                        NOTE: The following shop(s) cannot deliver to your selected location. All item(s) listed will be removed from your cart upon checkout.
                        <!-- <input type="checkbox" class="form-control-check" name="signatureBox" id="signatureBox">
                        I hereby confirm that all order details and delivery information are correctly entered. Furthermore, I have read the and agreed with the <a style="color:var(--blue)" href="<?=base_url('terms-and-conditions');?>" target="_blank" >terms and conditions</a> of this service. -->
                        </p>
                    </div>
                </div>
            </div>

            <div hidden id="confirmation_details" class="detail-card receiver-detail-container">
                <div class="form-group row">
                    <div class="col-12 pl-0-xl">
                        <label style="font-size: 12px !important;"><input type="checkbox" name="" value="" id="confirmation">&nbsp; I hereby confirm that all order details and delivery information are correctly entered. Furthermore, I have read and agreed with the <a href="<?=terms_and_condition();?>" target="_blank" style="color: var(--footer_titlecolor) !important;">terms and conditions</a> of this service.</label>
                    </div>
                </div>
            </div>

            <div class="detail-card checkout-footer">
                <!-- Voucher note -->
                <div hidden id="voucher-note" class="row mb-1">
                    <div class="col-12">
                        <div class="conditions alert alert-warning border">
                            <p class="m-0 font-weight-bold"><i class="fa fa-info-circle mr-3"></i>Please note that all vouchers applied on this order will not be usable in future transactions.
                            </p>
                        </div>
                    </div>
                </div>
                <div hidden class="row mb-4">
                    <div class="col-5">Shipping Fee:</div>
                    <div class="col-7">
                        <input id="shipping_fee" name="shipping_fee" required type="text" value="">
                    </div>
                </div>
                <div hidden class="row mb-4">
                    <div class="col-5">Total:</div>
                    <div class="col-7">
                        <input id="total_amount" name="total_amount" required type="text" value="">
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="col checkout-total-container">
                        <div class="checkout-total" id="checkout-total">
                            Total: <span class="highlight" id="total_amount_checkout"><span class="ml-2 spinner-border spinner-border-sm"></span></span>
                        </div>
                    </div>
                    <div hidden class="col-auto mx-lg-1" id="remove-shop-card-backbtn">
                        <button id="backbtn" class="btn checkout-button" style="background-color: #D3D3D3;">Back</button>
                    </div>
                    <div hidden class="col-auto" id="remove-shop-card-continuebtn">
                        <button id="continuebtn" class="btn checkout-button">Continue Shopping</button>
                    </div>
                    <div class="col-auto" id="checkout-card">
                        <form method="post" id="paypanda_trans" action="<?=c_paypanda_link()?>">
                        <input type="hidden" readonly="true" name="merchant_id" id="merchant_id">
                        <input type="hidden" name="reference_number" id="reference_number">
                        <input type="hidden" name="email_address" id="email_address">
                        <input type="hidden" name="payer_name" id="payer_name">
                        <input type="hidden" name="mobile_number" id="mobile_number">
                        <input type="hidden" name="amount_to_pay" id="amount_to_pay">
                        <input type="hidden" readonly="true" name="currency" value="PHP" id="currency">
                        <input type="hidden" name="remarks" id="remarks">
                        <input type="hidden" name="signature" id="signature">
                        <input type="hidden" name = "latitude" id = "latitude">
                        <input type="hidden" name = "longitude" id = "longitude">
                        <button type="submit" hidden name="submit_checkout" id="submit_checkout"></button>
                        </form>

                        <form method = "post" id = "zero_payment_trans">
                          <input type="hidden" readonly="true" name="z_merchant_id" id="z_merchant_id">
                          <input type="hidden" name="z_reference_number" id="z_reference_number">
                          <input type="hidden" name="z_email_address" id="z_email_address">
                          <input type="hidden" name="z_payer_name" id="z_payer_name">
                          <input type="hidden" name="z_mobile_number" id="z_mobile_number">
                          <input type="hidden" name="z_amount_to_pay" id="z_amount_to_pay">
                          <input type="hidden" readonly="true" name="z_currency" value="PHP" id="z_currency">
                          <input type="hidden" name="z_remarks" id="z_remarks">
                          <input type="hidden" name="z_signature" id="z_signature">
                          <input type="hidden" name = "latitude" id = "z_latitude">
                          <input type="hidden" name = "longitude" id = "z_longitude">
                        </form>
                        <button id="shipping" disabled class="btn checkout-button">Proceed to Shipping</button>
                        <button hidden id="checkOut" data-allow_cod = "<?=allow_cod()?>" class="btn checkout-button">Checkout</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
  </div>
</div>
<div class="container">
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-info-circle mr-3"></i>Confirmation</h4>
        </div>
        <div class="modal-body">
          <div class="confirmationBox">
            <p>By clicking on proceed, I hereby confirm that all order details and delivery information are correctly entered. Furthermore, I have read the and agreed with the terms and conditions of this service. </p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn portal-primary-btn" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn portal-primary-btn" id="confirmation_modal_proceed">Proceed</button>
        </div>
      </div>

    </div>
  </div>
  <!-- COD MODAL -->
  <div class="modal fade" id = "cod_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title">Select Payment Method</h6>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12 mb-3">
              <button class="btn btn-payment_type_panda form-control" id = "payment_paypanda">
                Online Payment via
                <img src="<?=base_url('assets/img/logo-2.png')?>" class = "btn-img" alt="">
              </button>
            </div>
            <div class="col-12 mb-3">
              <button class="btn btn-payment_type form-control" id = "payment_cod">
                <i class="fa fa-truck cod-logo"></i>
                Cash on Delivery
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php  $this->load->view("includes/footer"); ?>

<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/select2-materialize.css');?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/select2.css');?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/select2.min.css');?>">
<script src="<?=base_url('assets/js/select2.js');?>"></script>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
<script src="<?=base_url('assets/js/app/shop/checkout-052620.js');?>"></script>
<script src="<?=base_url('assets\js\app\shop\google_map.js');?>"></script>
<script src="<?=base_url('assets\js\app\shop\google_map_debounce.js');?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=c_google_api_key()?>&libraries=places"></script>
