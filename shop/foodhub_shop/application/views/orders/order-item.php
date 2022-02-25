<div class="content-inner" id="pageActive" data-num="1"></div>
<div class="content-inner" id="pageActiveMobile" data-num="1"></div>
<div id="headerTitle" data-title="Order Item" data-search="false"></div>

<div class="container-fluid checkout-page">
    <nav aria-label="breadcrumb ">
        <ol class="breadcrumb ">
            <li class="breadcrumb-item"><a href="<?=base_url('orders');?>">Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order Details</li>
        </ol>
    </nav>
    <div class="portal-tracker">
        <h4 class="text-center mb-3">Order Progress</h4>
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10">
                        <div class="portal-timeline">
                            <div class="portal-timelime__bg-line-container">
                                <div class="portal-timeline__line portal-timeline__line-1"></div>
                                <div class=" portal-timeline__line portal-timeline__line-2"></div>
                            </div>
                            <div class="portal-timeline__item portal-timeline__step-1 <?= $order_details[0]['order_status'] == 'p' ? 'portal-timeline__item--active' : '' ;?> ">
                                <div class="portal-timeline__node"></div>
                                <div class="portal-timeline__title">Order Placed</div>
                            </div>
                            <div class="portal-timeline__item portal-timeline__step-2 <?= $order_details[0]['order_status'] == 's' ? 'portal-timeline__item--active' : '' ;?>">
                                <div class="portal-timeline__node"></div>
                                <div class="portal-timeline__title">Preparing your Order</div>
                            </div>
                            <!-- <div class="portal-timeline__item portal-timeline__step-3 <?= $order_details[0]['order_status'] == 'd' ? 'portal-timeline__item--active' : '' ;?>">
                                <div class="portal-timeline__node"></div>
                                <div class="portal-timeline__title">Delivered</div>
                            </div> -->
                            <div class="portal-timeline__item portal-timeline__step-3 <?= $order_details[0]['order_status'] == 'd' ? 'portal-timeline__item--active' : '' ;?>">
                                <div class="portal-timeline__node"></div>
                                <div class="portal-timeline__title">Shipped</div>
                            </div>
                        </div>
                    </div>


                    <div class="report-order__container ">
                        <div class="report-order ">
                            <h5 class="mb-4">
                                Report Order
                            </h5>
                            <textarea resize = "none" rows = "3" placeholder = "Reason.." class = "form-control mb-3 reason" ></textarea>
                             <input class = "reference_num" hidden value = <?=$order_details[0]['reference_num']?>>
                             <button class="btn portal-primary-btn send-report_btn">Send Report</button>
                             <i class="fa fa-times report-order__close-icon"></i>
                             
                        </div>
                        <div class="report-order--overlay"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row flex-row-reverse">
        <div class="col-lg-4 col-12 ">
            <a class="collapse d-block" data-toggle="collapse" href="#collapse-collapsed">
                <i class="fa fa-chevron-down float-right"></i>
                <h6 class="mb-4 receiver__title"><i class="fa fa-truck mr-3"></i>Receiver's Details</h6>
            </a>
            <div class="collapse"  id="collapse-collapsed" >
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-5">Name:</div>
                        <div class="col-7 receiver__name">
                            <input disabled type="text" value="<?=ucwords(strtolower($order_details[0]['fullname']));?>">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-5">Contact Number:</div>
                        <div class="col-7 receiver__name">
                            <input disabled type="text" value="<?=$order_details[0]['conno']?>">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-5">Email:</div>
                        <div class="col-7 receiver__name">
                            <input  disabled type="email" value="<?=$order_details[0]['email']?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-5 mb-3">Shipping Address:</div>
                        <div class="col-12 receiver__name">
                            <div class="row">
                                <div class="col-md-8 col-12  col-lg-12 mb-2">
                                    <input disabled class="receiver__input" type="text" value="<?=$order_details[0]['address']?>">
                                </div>

                                <div class="col-md-8 col-12  col-lg-12 mb-2">
                                    <input disabled class="receiver__input" type="text" value="<?=$order_details[0]['area_desc']?>">
                                </div>
                                <!-- <div class="col-md-4 col-12 col-lg-12">
                                    <select class="w-100 h-100 receiver__input">
                                        <option selected="selected" hidden>(City)</option>
                                        <option>Taguig</option>
                                        <option>Makati</option>
                                    </select>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            <div class="row mb-4">
                <div class="col-5">PayPanda Reference No:</div>
                <div class="col-7 text-black font-weight-bold">
                    <?=$order_details[0]['paypanda_ref'];?>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-5">Date of Purchase:</div>
                <div class="col-7 text-black font-weight-bold">
                    <?=date_format(date_create($order_details[0]['date_ordered']), 'm/d/Y h:i:s A');?>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-5">Date of Payment:</div>
                <div class="col-7 text-black font-weight-bold">
                <?= $order_details[0]['payment_date'] != "0000-00-00 00:00:00" ? date_format(date_create($order_details[0]['payment_date']), 'm/d/Y h:i:s A') : "N/A";?>
                </div>
            </div>
            <div class="row mb-3">
                <?php if($order_details[0]['order_status'] == 'r') {?>
                    <div class="col-5">Date Recieved:</div>
                    <div class="col-7 text-black font-weight-bold">
                        <?= $order_details[0]['date_received'] != "0000-00-00 00:00:00" ? date_format(date_create($order_details[0]['date_received']), 'm/d/Y h:i:s A') : "N/A";?>
                    </div>
                <?php } else {?>
                    <div class="col-5">Estimated date of Delivery:</div>
                    <div class="col-7 text-black font-weight-bold">
                        <?=$this->session->userdata('est_delivery');?>
                    </div>
                <?php } ?>
            </div>
            <hr>
            <div class="row checkout__place-order">
                <div class="col-6 col-lg-12">
                    <h4 class="checkout__total mb-0">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center">Total: <span class="ml-2">PHP <?=number_format($order_details[0]['order_total_amt'],2);?></span></div>
                        </div>
                    </h4>
                </div>
                <?php if($order_details[0]['order_status'] == 'r') {?>
                    <div class="col-6 col-lg-12 mt-0 mt-lg-3 checkout__button">
                        <a href="<?=base_url("order_again/?sono=".$order_details[0]['order_so_no'])?>" class="btn portal-primary-btn btn-block">Order Again</a>
                    </div>
                    <div class="col-6 col-lg-12 mt-0 mt-lg-3 report__button">
                        <button type = "button" class="btn portal-warning-btn btn-block report-order_btn">Report Order</button>
                    </div>
                    <!-- <input type="text" value = "<?=today()?>"> -->
                <?php } ?>
            </div>
        </div>
        <div class="col-lg-8 col-12 checkout__order">
            <div class="portal-table h-100">
                <h5 class="page-title">Your Orders</h5>
                <div class="portal-table__container row">
                    <div class="portal-table__titles col-12 mb-4 ">
                        <div class="col">Product</div>
                        <div class="col-1">Unit</div>
                        <div class="col-3">Category</div>
                        <div class="col-1">Price</div>
                        <div class="col-1">Quantity</div>
                        <div class="col-2">Total Amount</div>

                    </div>
                    <?php echo $gg; ?>
                    <?php foreach($order_details as $order){ ?>
                        <div class="col-12 col-md-6 col-lg-12 mb-3">
                            <div class="portal-table__item">
                                <!-- <div class="portal-table__column col-12 col-lg-2 portal-table__id"><?= substr($order['order_id'], 0, -20);?></div> -->
                                <div class="portal-table__column col-12 col-lg portal-table__product"><?=$order['itemname'];?></div>
                                <div class="portal-table__column col-4 col-lg-1 portal-table__unit">
                                <span class="d-lg-none">Unit:</span> <?=$order['unit'];?>
                                </div>
                                <div class="portal-table__column col-4 col-lg-3 portal-table__category"><span class="d-lg-none">Category:</span> <?=$order['cat_desc']?></div>
                                <div class="portal-table__column col-12 col-lg-1 portal-table__price">  <?=number_format($order['amount'],2)?></div>
                                <div class="portal-table__column portal-table__quantity col-4 col-lg-1">
                                    <span class="d-lg-none">Qty:</span> <?=number_format($order['quantity'],2)?>
                                </div>
                                <div class="portal-table__column col-12 col-lg-2 portal-table__id">  <?=number_format(($order['amount'] * $order['quantity']),2)?></div>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/orders/order-item.js');?>"></script>
