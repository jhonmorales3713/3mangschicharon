<link rel="stylesheet" href="<?=base_url("assets/css/profile/profile.css")?>">
<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }


    .sidebar-item.sidebar-item-3 {
        color: #222;
        border-right: 4px solid var(--primary-color);
        font-weight: 700;
    }

    .sidebar-item.sidebar-item-3 i {
        color: var(--primary-color);
    }

    @media only screen and (max-width: 767px) {
            .sidebar-item.sidebar-item-3 {
            color: #fff;
            border-right: none;
        }
    }

    .btn-shoplink{
      border: 1px solid gainsboro;
      padding: 3px 8px;
      color: #222;
      background-color: #fff;
      font-size: 10px;
      border-radius: 0px;
    }

    .btn-vieworder{
      background-color: #fff;
      /* color:#222; */
      font-size:14px;
      padding: 5px 10px;
      border: 1px solid gainsboro;
    }

    .btn-tab{
      background-color: #fff;
      /* color:#222; */
      font-size:14px;
      padding: 5px 10px;
      border: 1px solid gainsboro;
      /* min-width:150px; */
    }

    .btn-vieworder:hover{
      border: 1px solid #ff4444;
    }

    .btn-total{
      background-color: #ef4131 !important;
      color:#fff;
    }

    .btn-purchase{
      border-radius: 0px;
    }

    .voucher-pill{
      font-size: 10px;
      background-color: #eee;
    }

    .product-card-price{
      font-size:10px;
    }

    .product-card{
      /* min-height: 358px; */
      /* max-height: 358px; */
    }

    input[type=text], textarea{
      border-radius: 1px !important;
      font-size: 13px !important;
      text-align: left;
      border: none;
      border-bottom: 1px solid #222;
      pointer-events: none;
    }

    .input-numbers{
      font-weight: bold;
      font-size:11px;
    }

    @media only screen and (max-width: 767px) {
      .portal-timeline-vertical__title{
        font-size:9px;
      }

    }

    @media only screen and (max-width: 407px) {
      .portal-timeline-vertical__title{
        font-size:7px;
      }

    }

    @media only screen and (max-width: 350px) {
      .portal-timeline-vertical__title{
        font-size:6.2px;
        padding: 0 0 0 4px;
      }

    }

    @media only screen and (max-width: 390px) {
      .product-card-name{
        font-size:12px;
      }

      .product-card-quantity{
        font-size:11px;
      }

    }
    @media only screen and (max-width: 320px) {
      .product-card-name{
        font-size:
      }

      .product-card-quantity{
        font-size:9px;
      }
    }
</style>

<div class="shop-container profile-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <?php include 'sidebar.php'?>
            </div>
            <div class="col-12 col-md-9">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="all-tab" data-toggle="tab" href="#purchaseAll" role="tab" aria-controls="home" aria-selected="true">All</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="topay-tab" data-toggle="tab" href="#purchaseToPaY" role="tab" aria-controls="profile" aria-selected="false">To Pay</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" id="toship-tab" data-toggle="tab" href="#purchaseToShip" role="tab" aria-controls="contact" aria-selected="false">To Ship</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="toreceive-tab" data-toggle="tab" href="#purchaseToReceive" role="tab" aria-controls="contact" aria-selected="false">To Receive</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="completed-tab" data-toggle="tab" href="#purchaseCompleted" role="tab" aria-controls="contact" aria-selected="false">Completed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cancelled-tab" data-toggle="tab" href="#purchaseCancelled" role="tab" aria-controls="contact" aria-selected="false">Cancelled</a>
                    </li> -->
                </ul>
                <div class="tab-content" id="myTabContent">
                    <!-- ALL -->
                    <div class="tab-pane fade show active" id="purchaseAll" role="tabpanel" aria-labelledby="all-tab">
                      <div class="purchase_wrapper">
                        <?php if($orders->num_rows() > 0):?>
                          <?php foreach($orders->result_array() as $order):?>
                            <?php if($order_shops->num_rows() > 0):?>
                              <?php foreach($order_shops->result_array() as $shop):?>
                                <?php if($order['reference_num'] == $shop['reference_num']):?>
                                  <?php
                                      $order_status = "";
                                      $total = 0;

                                      if($shop['orderstatus'] == 'po'){
                                        $order_status = 'Processing Order';
                                      }else if($shop['orderstatus'] == 'rp'){
                                        $order_status = 'Ready for Pickup';
                                      }else if($shop['orderstatus'] == 'bc'){
                                        $order_status = 'Booking Confirmed';
                                      }else if($shop['orderstatus'] == 'f'){
                                        $order_status = 'Fulfilled';
                                      }else if($shop['orderstatus'] == 's'){
                                        $order_status = 'Shipped';
                                      }else if($shop['orderstatus'] == 'p' && $shop['payment_status'] == 1){
                                        $order_status = 'Ready for processing';
                                      }else{
                                        $order_status = 'Waiting for payment';
                                      }
                                  ?>
                                  <div class="product-card px-0 pb-0">
                                    <div class="product-card-header p-2">
                                      <div class="row">
                                        <div class="col">
                                          <div class="row no-gutters">
                                            <div class="col-1 d-flex align-items-center justify-content-end">
                                                <div><img class="img-thumbnail" style="width: 50px;" src="<?=get_s3_imgpath_upload().'assets/img/shops-60/webp/'.pathinfo($shop['logo'], PATHINFO_FILENAME).'.webp'?>"
                                                onerror="this.onerror=null; this.src='<?=get_s3_imgpath_upload().'assets/img/shops-60/'.$shop['logo']?>'"></div>
                                            </div>
                                            <div class="col d-flex align-items-center">
                                              <div class="product-card-title" style = "width:100%;">

                                                <?=$shop['shopname']?>
                                                <!-- <a href="<?=base_url('store/'.$shop['shopurl'])?>" class = "btn btn-sm btn-shoplink ml-1"><i class="fa fa-home mr-1"></i>View Shop</a> -->
                                                <a data-url="<?=base_url('store/'.$shop['shopurl'])?>" class = "btn btn-sm btn-shoplink ml-1"><i class="fa fa-home mr-1"></i>View Shop</a>
                                              </div>
                                            </div>
                                            <div class="col text-right order-status" >
                                              <p style = "font-weight:0;font-size:10px;margin:0;white-space:nowrap;">[ <?=$order['reference_num']?> ]</p>
                                              <p style = "font-size:10px;font-weight:bold;margin:0;white-space:nowrap;">[ <?=($order['payment_status'] == 1) ? $order_status : "Waiting for Payment"?> ]</p>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- ORDER DETAILS -->
                                    <div class="product-card-body px-3 order-details">
                                      <?php if($order_logs->num_rows() > 0):?>
                                        <?php foreach($order_logs->result_array() as $logs):?>
                                          <?php if($order['order_id'] == $logs['order_id'] && $shop['id'] == $logs['sys_shop']):?>
                                            <?php $total += $logs['total_amount'];?>
                                            <div class="product-card-item">
                                              <div class="row no-gutters">
                                                <div class="col">
                                                  <div class="row no-gutters">
                                                    <div class="col-2 col-md-1">
                                                      <div class="product-card-image" style="background-image: url(<?=get_s3_imgpath_upload().'assets/img/'.$shop['shopcode'].'/products-50'.'/'.$logs['product_id'].'/1-'.$logs['product_id'].'.jpg'?>)"></div>
                                                    </div>
                                                    <div class="co product-card-content">
                                                      <div class="product-card-name">
                                                        <?=$logs['itemname']?>
                                                      </div>
                                                      <div class="product-card-quantity">
                                                        Quantity: <?=$logs['quantity']?> Unit Price: &#8369; <?=$logs['price']?>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-3 col-md-2 d-none d-md-block">
                                                  <div class="product-card-price">
                                                    <?=$logs['otherinfo']?>
                                                  </div>
                                                </div>
                                                <div class="col-2">
                                                  <div class="product-card-price" style = "white-space: nowrap;">
                                                    &#8369; <?=$logs['total_amount']?>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          <?php endif;?>
                                        <?php endforeach;?>
                                        <div class="product-card-item">
                                          <div class="row no-gutters">
                                            <div class="col d-none d-md-block">
                                              <div class="row no-gutters">
                                                <div class="col-2 col-md-1 ">
                                                  <!-- <div class="product-card-image" style="background-image: url(<?=get_s3_imgpath_upload().'assets/img/'.$shop['shopcode'].'/products-50'.'/'.$logs['product_id'].'/1-'.$logs['product_id'].'.jpg'?>)"></div> -->
                                                </div>
                                                <div class="co product-card-content">
                                                  <div class="product-card-name">
                                                  </div>
                                                  <div class="product-card-quantity">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-10 col-md-2">
                                              <div class="product-card-price">
                                                Shipping Fee:
                                              </div>
                                            </div>
                                            <div class="col-2">
                                              <div class="product-card-price" style = "white-space: nowrap;">
                                                &#8369; <?=$shop['delivery_amount']?>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      <?php endif;?>
                                    </div>
                                    <!-- SHIPPING DETAILS -->
                                    <div class="product-card-body px-3 shipping-details" style = "display:none;">
                                      <div class="product-card-item">
                                        <div class="row no-gutters">
                                          <?php
                                            $vouchers = get_vouchers($order['reference_num'],$shop['id']);
                                          ?>
                                          <!-- CONTACT PERSON AND ADDRESS -->
                                          <div class="col-12 col-lg-<?=$vouchers != 0 ? '6' : '12'?>">
                                            <div class="form-group row">
                                              <div class="col-md-8 mb-2">
                                                <input type="text" class="form-control" value = "<?=$order['name']?>">
                                                <small class = "font-weight-bold">Contact Person</small>
                                              </div>
                                              <div class="col-md-4 mb-2">
                                                <input type="text" class="form-control" value = "<?=$order['conno']?>">
                                                <small class = "font-weight-bold">Contact no.</small>
                                              </div>
                                              <div class="col-md-12 mb-2">
                                                <input type="text" class="form-control" value = "<?=desanitize($order['address'])?>">
                                                <small class = "font-weight-bold">Shipping Address</small>
                                              </div>
                                              <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control" value = "<?=$order['date_ordered']?>">
                                                <small class = "font-weight-bold">Date Ordered</small>
                                              </div>
                                              <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control" value = "<?=($shop['date_shipped'] != '0000-00-00 00:00:00') ? $shop['date_shipped'] : '---'?>">
                                                <small class = "font-weight-bold">Date Shipped</small>
                                              </div>
                                              <?php if($order['referral_code'] != null):?>
                                                <div class="col-md-6">
                                                  <input type="text" class="form-control" value = "<?=($order['referral_code'] == null) ? '---' : $order['referral_code']?>">
                                                  <small class="font-weight-bold">Referral Code</small>
                                                </div>
                                              <?php endif;?>
                                            </div>
                                          </div>
                                          <!-- SHIPPING FEE AND VOUCHERS -->
                                          <?php if($vouchers != 0):?>
                                            <div class="col-12 col-lg-6">
                                              <!-- VOUCHERS  -->
                                                <div class="form-group row">
                                                  <div class="col-md-6">
                                                    <p class="font-weight-bold d-flex align-items-end justify-content-center m-0" style="font-size: 11px !important">Voucher(s):</p>
                                                  </div>
                                                </div>
                                                <?php foreach($vouchers as $voucher):?>
                                                  <div class="form-group row">
                                                    <div class="col-6 col-md-6 d-flex align-items-end justify-content-center mb-2">
                                                      <span class="badge badge-pill voucher-pill p-2 ml-1">
                                                        <i class="fa fa-tag" style = "font-size:8px;"></i>
                                                        <?= $voucher['voucher_code']?>
                                                      </span>
                                                    </div>
                                                    <div class="col-6 col-md-6 mb-2">
                                                      <input type="text" class="form-control text-right input-numbers" value = "- &#8369; <?=number_format($voucher['amount'],2)?>">
                                                    </div>
                                                  </div>
                                                <?php endforeach;?>
                                                <!-- VOUCHER CODES -->
                                            </div>
                                          <?php endif;?>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- ORDER TIMELINE -->
                                    <div class="product-card-body px-3 order-timeline" style = "display:none">
                                      <div class="row no-gutters mt-2">
                                        <div class="col-12 col-lg-9 offset-lg-3">
                                            <div class="px-3 py-2 rounded" data-target="#order_status_child_collapse-JCWW" aria-expanded="false" aria-controls="order_status_child_collapse-JCWW" id="order_status_parent_collapse">
                                                <span class="product-card-name" style="font-size: 14px">Order Status</span>  <i class="fa fa-chevron-down float-right order-status-caret" hidden></i>
                                            </div>
                                            <div class="collapse col-12 col-md-12 py-3 px-4 show" id="order_status_child_collapse-JCWW">
                                                <div class="portal-timeline-vertical">

                                                  <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                                                      <div class="portal-timeline-vertical__node">

                                                        <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                        </i>
                                                      </div>
                                                      <small class = "portal-timeline-vertical__title">Order has been placed</small><br>
                                                      <small class = "portal-timeline-vertical__title"><?=$order['date_ordered']?></small><br>
                                                  </div>

                                                  <?php if($shop['payment_status'] == 1 ):?>
                                                    <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                                                        <div class="portal-timeline-vertical__node">

                                                          <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                          </i>
                                                        </div>
                                                        <small class = "portal-timeline-vertical__title">Payment for order has been confirmed.</small><br>
                                                        <small class = "portal-timeline-vertical__title"><?=$order['payment_date']?></small><br>
                                                    </div>
                                                  <?php endif;?>

                                                  <?php
                                                    $order_histories = get_order_history($order['reference_num'],$shop['id']);
                                                  ?>
                                                  <?php if($order_histories->num_rows() > 0):?>
                                                    <?php foreach($order_histories->result_array() as $row):?>
                                                      <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                                                          <div class="portal-timeline-vertical__node">

                                                            <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                                            </i>
                                                          </div>
                                                          <small class = "portal-timeline-vertical__title"><?=$row['action']?></small><br>
                                                          <small class = "portal-timeline-vertical__title d-none d-lg-block"><?=$row['description']?></small>
                                                          <small class = "portal-timeline-vertical__title"><?=$row['date_created']?></small>
                                                      </div>
                                                    <?php endforeach;?>
                                                  <?php endif;?>

                                                </div>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- FOOTER -->
                                    <div class="product-card-footer d-flex justify-content-end p-0">
                                      <button class="btn btn-tab btn-purchase py-2" data-tab = "order-details">
                                        <i class="fa fa-shopping-cart d-block d-sm-none fa-lg"></i>
                                        <span class = "	d-none d-sm-block">Order Details</span>
                                      </button>
                                      <button class="btn btn-tab btn-purchase py-2" data-tab = "shipping-details">
                                        <i class="fa fa-truck d-block d-sm-none fa-lg"></i>
                                        <span class = "	d-none d-sm-block">Shipping Details</span>
                                      </button>
                                      <button class="btn btn-tab btn-purchase py-2" data-tab = "order-timeline">
                                        <i class="fa fa-clock-o d-block d-sm-none fa-lg"></i>
                                        <span class="	d-none d-sm-block">Order Timeline</span>
                                      </button>
                                      <button class="btn btn-total font-weight-bold btn-purchase py-2">
                                        <?php
                                          $total_amount = ($order['total_amount'] + $order['delivery_amount']);
                                          $total_amount = ($vouchers != 0) ? $total_amount - $vouchers[0]['total_amount']: $total_amount;
                                          $total_amount = ($total_amount < 0) ? 0 : $total_amount;
                                        ?>
                                        Total: &#8369; <?=number_format($total + $shop['delivery_amount'], 2)?>
                                      </button>
                                    </div>
                                  </div>
                                <?php endif;?>
                              <?php endforeach;?>
                            <?php endif;?>
                          <?php endforeach;?>
                        <?php else:?>
                          <div class="form-group row">
                            <div class="col-12 text-center">
                              <h6>You did not purchase anything yet.</h6>
                              <a href = "<?=base_url()?>" class="btn portal-primary-btn">Start Shopping</a>
                            </div>
                          </div>
                        <?php endif;?>
                      </div>

                      <div class="form-group row">
                        <div class="col-12 text-center">
                          <button class="btn portal-primary-btn btn_load_more" data-status = "all" data-wrapper = "purchase_wrapper" style = "display: <?=($orders_count > 5) ? '' : 'none'?>;">Load More</button>
                        </div>
                      </div>

                    </div>
                    <!-- To Pay -->
                    <div class="tab-pane fade" id="purchaseToPaY" role="tabpanel" aria-labelledby="topay-tab">
                      <div class="purchase_wrapper_topay">

                      </div>

                      <div class="form-group row">
                        <div class="col-12 text-center">
                          <button class="btn portal-primary-btn btn_load_more" data-status = "to_pay" data-wrapper = "purchase_wrapper_topay">Load More</button>
                        </div>
                      </div>
                    </div>
                    <!-- To Ship -->
                    <div class="tab-pane fade" id="purchaseToShip" role="tabpanel" aria-labelledby="toship-tab">
                      <div class="purchase_wrapper_toship">

                      </div>

                      <div class="form-group row">
                        <div class="col-12 text-center">
                          <button class="btn portal-primary-btn btn_load_more" data-status = "to_ship" data-wrapper = "purchase_wrapper_toship">Load More</button>
                        </div>
                      </div>
                    </div>
                    <!-- <div class="tab-pane fade" id="purchaseToReceive" role="tabpanel" aria-labelledby="toreceive-tab">

                    </div>
                    <div class="tab-pane fade" id="purchaseCompleted" role="tabpanel" aria-labelledby="completed-tab">

                    </div>
                    <div class="tab-pane fade" id="purchaseCancelled" role="tabpanel" aria-labelledby="cancelled-tab">

                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets\js\profile\purchases.js');?>"></script>
