$(function(){
  var base_url = $("body").data('base_url');
  var s3_url = $("body").data("s3_url");

  function get_order_history(refno,shopid){
    let return_data = 0;
    $.ajax({
      url: base_url+'profile/Customer_profile/get_order_history',
      type: 'post',
      async: false,
      data:{refno, shopid},
      beforeSend: function(){
        showCover('Loading data ...');
      },
      success: function(data){
        hideCover();
        if(data.success == 1){
          return_data = data.message;
        }
      },
      error: function(){
        hideCover();
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again"
        });
      }
    });
    // console.log(return_data);
    return return_data;
  }

  function my_orders(status,wrapper){
    $.ajax({
      url: base_url+'profile/Customer_profile/purchases_status',
      type: 'post',
      dataType: 'json',
      data: {status: status},
      cache: false,
      beforeSend: function(){
        showCover('Loading data ...');
      },
      success: function(data){
        hideCover();
        if(data.success == true){
          if(data.orders.length < 5){
            $('.btn_load_more').hide();
          }
          $(`.${wrapper}`).html('');
          data.orders.forEach((order) => {
            let html2 = "";
            let vouchers_html = "";
            let voucher_amount_html = "";
            let total_amount = 0;

            html2 += `<div class="product-card">`;
            $.each(data.order_shops, (i, shops) => {
              if(order.reference_num == shops.reference_num){
                let order_status = "";

                if(shops.orderstatus == 'po'){
                  order_status = "Processing Order";
                }else if(shops.orderstatus == 'rp'){
                  order_status = "Ready for Pickup";
                }else if(shops.orderstatus == 'bc'){
                  order_status = "Booking Confirmed";
                }else if(shops.orderstatus == 'f'){
                  order_status = "Fulfilled";
                }else if(shops.orderstatus == 's'){
                  order_status = "Shipped";
                }else if(shops.orderstatus == 'p' && shops.payment_status == 1){
                  order_status = 'Ready for processing';
                }else{
                  order_status = "Waiting for payment";
                }

                html2 +=
                `

                  <div class="product-card-header mb-3">
                    <div class="row">
                      <div class="col">
                        <div class="row no-gutters">
                          <div class="col-1 d-flex align-items-center justify-content-end">
                              <div><img class="img-thumbnail" style="width: 50px;" src="${base_url}assets/img/shops-60/webp/${shops.logo}.webp"
                              onerror="this.onerror=null; this.src='${base_url}assets/img/shops-60/${shops.logo.split('.').slice(0, -1).join('.')}.jpg'"></div>
                          </div>
                          <div class="col d-flex align-items-center">
                            <div class="product-card-title" style = "width:100%;">
                              ${shops.shopname}
                              <a data-url="${base_url}store/${shops.shopurl}" class = "btn btn-sm btn-shoplink ml-1"><i class="fa fa-home mr-1"></i>View Shop</a>
                              <span class = "float-right">[ ${(order.payment_status == 1) ? order_status : "Waiting for Payment"} ]</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="product-card-body mb-3">

                `;

                $.each(data.order_logs, (i, logs) => {
                  if(order.order_id == logs.order_id && shops.id == logs.sys_shop){
                    html2 +=
                    `
                    <div class="product-card-item">
                      <div class="row no-gutters">
                        <div class="col">
                          <div class="row no-gutters">
                            <div class="col-2 col-md-1">
                              <div class="product-card-image" style="background-image: url(${base_url}assets/img/${shops.shopcode}/products-50/${logs.product_id}/0-${logs.product_id}.jpg)"></div>
                            </div>
                            <div class="co product-card-content">
                              <div class="product-card-name">
                                ${logs.itemname}
                              </div>
                              <div class="product-card-quantity">
                                Quantity: ${logs.quantity}
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-3 col-md-2 d-none d-md-block">
                          <div class="product-card-price">
                            ${logs.otherinfo}
                          </div>
                        </div>
                        <div class="col-2">
                          <div class="product-card-price">
                            ₱ ${logs.total_amount}
                          </div>
                        </div>
                        <div class="col-1">
                          <div class="product-card-delete removeCart" data-id = "" data-shopid = "" data-display = "">
                          </div>
                        </div>
                      </div>
                    </div>
                    `;
                  }
                });
                html2 += `</div>`; // end of product-card-body
                var referral = (order.referral_code != null)
                ? `<br />Referral Code: <p>${order.referral_code}</p>`
                : `<br />Referral Code: <p>---</p>`;
                var vouchers = get_vouchers(order.reference_num,shops.id);
                var total = parseFloat(order.total_amount) + parseFloat(order.delivery_amount);
                total = (vouchers != 0) ? total - vouchers[0]['total_amount']: total;
                total = (total < 0) ? 0 : total;
                total_amount = total;
                // console.log(vouchers[0]['total_amount']);

                // VOUCHERS
                if(vouchers != 0){
                  vouchers_html +=
                  `
                  <div class="form-group row mt-4 voucher_wrapper">
                    <div class="col-12 mb-2">
                      <h2 class = "font-weight-bold">Voucher(s):</h2>
                    </div>
                  `;
                  $.each(vouchers, function(i, val){
                    vouchers_html +=
                    `
                    <div class="col-12 mb-1">
                      <span class="badge badge-pill voucher-pill p-2 ml-1">
                        <i class="fa fa-tag" style = "font-size:8px;"></i>
                        ${val['voucher_code']}
                      </span>
                    </div>
                    `;
                  });
                  vouchers_html += `</div>`;
                }
                // VOUCHERS AMOUNT
                if(vouchers != 0){
                  voucher_amount_html +=
                  `
                  <div class="form-group row mt-3 voucher_wrapper">
                    <div class="col-12 mb-2"><h2></h2></div>
                  `;
                  $.each(vouchers, function(i, val){
                    voucher_amount_html +=
                    `<div class="col-12 mb-1 text-right font-weight-bold">- ₱ ${numberFormatter(val['amount'])}</div>`;
                  });
                  voucher_amount_html += `</div>`;
                }
                html2 +=
                `
                  <div class="product-card-body py-3 product-card-total">
                    <div class="product-card-item">
                      <div class="row no-gutters">
                        <div class="col product-card-name pl-2">
                          Date Ordered: <p style = "font-size:11px !important;">${order.date_ordered}</p><br />
                          Shipping Address: <p style = "font-size:11px !important;">${order.address}</p>
                          ${referral}
                        </div>
                        <div class="col product-card-name text-right">
                          Shipping Fee:
                          ${vouchers_html}
                        </div>
                        <div class="col-4 col-md-4">
                          <div class="product-card-price">
                            ₱ ${shops.delivery_amount}
                            <br />
                            ${voucher_amount_html}
                          </div>
                        </div>
                        <div class="col-1"></div>
                      </div>
                    </div>
                  </div>
                `;
              }


            });

            html2 +=
            `
              <div class="product-card-body py-3 product-card-total">
                <div class="product-card-item">
                  <div class="row no-gutters">
                    <div class="col product-card-name text-right">
                      Total:
                    </div>
                    <div class="col-5 col-md-4">
                      <div class="product-card-price">
                        ₱ ${numberFormatter(total_amount)}
                      </div>
                    </div>
                    <div class="col-1"></div>
                  </div>
                </div>
              </div>
              <div class="product-card-footer text-right">
                <a data-url="${base_url}check_order_details?refno=${order.reference_num}" class="btn btn-vieworder">View Order Details</a>
              </div>
            `;

            html2 += "</div>";

            $(`.${wrapper}`).append(html2);

          });
        }else{
          $('.btn_load_more').hide();
          $(`.${wrapper}`).html(
            `
              <div class="form-group row">
                <div class="col-12 text-center">
                  <h6>You don't have any orders to pay.</h6>
                  <a href = "${base_url}" class="btn portal-primary-btn">Start Shopping</a>
                </div>
              </div>
            `
          );
        }
      },
      error: function(){
        hideCover();
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again"
        });
      }
    });
  }

  function my_orders2(status,wrapper){
    $.ajax({
      url: base_url+'profile/Customer_profile/purchases_status',
      type: 'post',
      dataType: 'json',
      data: {status: status},
      cache: false,
      beforeSend: function(){
        showCover('Loading data ...');
      },
      success: function(data){
        hideCover();
        if(data.success == true){
          if(data.orders.length < 5){
            $('.btn_load_more').hide();
          }
          $(`.${wrapper}`).html('');
          $.each(data.orders, function(i,order){


            $.each(data.order_shops, (i, shops) => {
              if(order.reference_num == shops.reference_num){
                let html2 = "";
                let vouchers_html = "";
                let voucher_amount_html = "";
                let total_amount = 0;
                let order_status = "";
                let total = 0;

                if(shops.orderstatus == 'po'){
                  order_status = "Processing Order";
                }else if(shops.orderstatus == 'rp'){
                  order_status = "Ready for Pickup";
                }else if(shops.orderstatus == 'bc'){
                  order_status = "Booking Confirmed";
                }else if(shops.orderstatus == 'f'){
                  order_status = "Fulfilled";
                }else if(shops.orderstatus == 's'){
                  order_status = "Shipped";
                }else if(shops.orderstatus == 'p' && shops.payment_status == 1){
                  order_status = 'Ready for processing';
                }else{
                  order_status = "Waiting for payment";
                }

                html2 += `<div class="product-card px-0 pb-0">`;

                  html2 +=
                  `
                    <div class="product-card-header p-2">
                      <div class="row">
                        <div class="col">
                          <div class="row no-gutters">
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <div><img class="img-thumbnail shop-logo" style="width: 50px;" src="${s3_url}assets/img/shops-60/${shops.logo}"
                                onerror="this.onerror=null; this.src=${s3_url}assets/img/shops-60/${shops.logo}"></div>
                            </div>
                            <div class="col d-flex align-items-center">
                              <div class="product-card-title" style = "width:100%;">
                                ${shops.shopname}
                                <a data-url="${base_url}store/${shops.shopurl}" class = "btn btn-sm btn-shoplink ml-1"><i class="fa fa-home mr-1"></i>View Shop</a>
                              </div>
                            </div>
                            <div class="col text-right order-status" >
                              <p style = "font-weight:0;font-size:10px;margin:0;white-space:nowrap;">[${order.reference_num}]</p>
                              <p style = "font-size:10px;font-weight:bold;margin:0;white-space:nowrap;">[ ${(order.payment_status == 1) ? order_status : "Waiting for Payment"} ]</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  `;

                  html2 += `<div class="product-card-body px-3 order-details">`;
                    $.each(data.order_logs, (i, logs) => {
                      if(order.order_id == logs.order_id && shops.id == logs.sys_shop){
                      total += parseFloat(logs.total_amount);
                      html2 +=
                      `
                        <div class="product-card-item">
                          <div class="row no-gutters">
                            <div class="col">
                              <div class="row no-gutters">
                                <div class="col-2 col-md-1">
                                  <div class="product-card-image" style="background-image: url(${s3_url}assets/img/${shops.shopcode}/products-50/${logs.product_id}/1-${logs.product_id}.jpg)"></div>
                                </div>
                                <div class="co product-card-content">
                                  <div class="product-card-name">
                                    ${logs.itemname}
                                  </div>
                                  <div class="product-card-quantity">
                                    Quantity: ${logs.quantity} Unit Price: &#8369; ${logs.price}
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-3 col-md-2 d-none d-md-block">
                              <div class="product-card-price">
                                ${logs.otherinfo}
                              </div>
                            </div>
                            <div class="col-2">
                              <div class="product-card-price" style = "white-space: nowrap;">
                                &#8369; ${logs.total_amount}
                              </div>
                            </div>
                          </div>
                        </div>
                        `
                      }
                    });
                  html2 +=
                  `
                  <div class="product-card-item">
                    <div class="row no-gutters">
                      <div class="col d-none d-md-block">
                        <div class="row no-gutters">
                          <div class="col-2 col-md-1 ">
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
                          &#8369; ${shops.delivery_amount}
                        </div>
                      </div>
                    </div>
                  </div>
                  `;
                  html2 += `</div>`;
                  let vouchers = get_vouchers(order.reference_num,shops.id);
                  html2 += `<div class="product-card-body px-3 shipping-details" style = "display:none;">`;
                    // console.log('referral', order.referral_code);
                    html2 +=
                    `
                    <div class="product-card-item">
                      <div class="row no-gutters">
                        <!-- CONTACT PERSON AND ADDRESS -->
                        <div class="col-12 col-lg-${vouchers != 0 ? `6` : `12`}">
                          <div class="form-group row">
                            <div class="col-md-8 mb-2">
                              <input type="text" class="form-control" value = "${order.name}">
                              <small class = "font-weight-bold">Contact Person</small>
                            </div>
                            <div class="col-md-4 mb-2">
                              <input type="text" class="form-control" value = "${order.conno}">
                              <small class = "font-weight-bold">Contact no.</small>
                            </div>
                            <div class="col-md-12 mb-2">
                              <input type="text" class="form-control" value = "${order.address}">
                              <small class = "font-weight-bold">Shipping Address</small>
                            </div>
                            <div class="col-md-6 mb-2">
                              <input type="text" class="form-control" value = "${order.date_ordered}">
                              <small class = "font-weight-bold">Date Ordered</small>
                            </div>
                            <div class="col-md-6 mb-2">
                              <input type="text" class="form-control" value = "${(shops.date_shipped != '0000-00-00 00:00:00') ? shops.date_shipped : '---'}">
                              <small class = "font-weight-bold">Date Shipped</small>
                            </div>
                            <div class="col-12 ">
                              <input type="text" class="form-control" value = "${(order.referral_code !== null) ? order.referral_code : '---'}">
                              <small class="font-weight-bold">Referral Code</small>
                            </div>
                          </div>
                        </div>

                          `;


                        if(vouchers != 0){
                          html2 +=
                            `
                          <!-- SHIPPING FEE AND VOUCHERS -->
                          <div class="col-12 col-lg-6">
                            <div class="form-group row">
                              <div class="col-md-6">
                                <p class="font-weight-bold d-flex align-items-end justify-content-center m-0" style="font-size: 11px !important">Voucher(s):</p>
                              </div>
                            </div>
                            `;

                            $.each(vouchers, function(i, voucher){
                              html2 +=
                              `
                                <div class="form-group row">
                                  <div class="col-6 col-md-6 d-flex align-items-end justify-content-center mb-2">
                                    <span class="badge badge-pill voucher-pill p-2 ml-1">
                                      <i class="fa fa-tag" style = "font-size:8px;"></i>
                                      ${voucher['voucher_code']}
                                    </span>
                                  </div>
                                  <div class="col-6 col-md-6 mb-2">
                                    <input type="text" class="form-control text-right input-numbers" value = "- &#8369; ${numberFormatter(voucher['amount'])}">
                                  </div>
                                </div>
                              `;
                            });
                            html2 += `</div>`;
                          }


                html2 += `
                        </div>
                      </div>
                        `;

                  html2 += `</div>`;

                  html2 +=
                  `
                  <!-- ORDER TIMELINE -->
                  <div class="product-card-body px-3 order-timeline" style = "display:none">
                    <div class="row no-gutters mt-2">
                    <div class="col-12 col-md-9 offset-md-3">
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
                                <small class = "portal-timeline-vertical__title">${order.date_ordered}</small><br>
                            </div>
                  `;

                  if(shops.payment_status == 1){
                    html2 +=
                    `
                    <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                        <div class="portal-timeline-vertical__node">
                          <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                          </i>
                        </div>
                        <small class = "portal-timeline-vertical__title">Payment for order has been confirmed.</small><br>
                        <small class = "portal-timeline-vertical__title">${order.payment_date}</small><br>
                    </div>
                    `;
                  }

                    var histories = get_order_history(order.reference_num,shops.id);
                    if(histories != 0){

                      $.each(histories, function(i, history){
                        html2 +=
                        `
                          <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                              <div class="portal-timeline-vertical__node">
                                <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                </i>
                              </div>
                              <small class = "portal-timeline-vertical__title">${history.action}</small><br>
                              <small class = "portal-timeline-vertical__title">${history.description}</small><br>
                              <small class = "portal-timeline-vertical__title">${history.date_created}</small><br>
                          </div>
                        `;
                      });
                    }

                    html2 +=
                    `
                              </div>
                          </div>
                      </div>
                    `;
                  let total_oder_amount = total + parseFloat(shops.delivery_amount);
                  // console.log('total', total);
                  // console.log('shipping', shops.delivery_amount);
                  html2 +=
                  `</div>
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
                      Total: &#8369; ${numberFormatter(total_oder_amount)}
                    </button>
                  </div>
                  `;

                html2 += `</div>`;

              $(`.${wrapper}`).append(html2);

            }
          });
          });
        }else{
          $('.btn_load_more').hide();
          $(`.${wrapper}`).html(
            `
              <div class="form-group row">
                <div class="col-12 text-center">
                  <h6>You don't have any orders to pay.</h6>
                  <a href = "${base_url}" class="btn portal-primary-btn">Start Shopping</a>
                </div>
              </div>
            `
          );
        }
      },
      error: function(jqXHR, textStatus, errorThrown){
        hideCover();
        showToast({
            type: "warning",
            css: "toast-top-full-width mt-5",
            msg: "Slow connection. Please try again."
        });
      }
    });
  }

  function get_vouchers(refno,shopid){
    let return_data = 0;
    // console.log(refno,shopid);
    $.ajax({
      url: base_url+'profile/Customer_profile/get_vouchers',
      type: 'post',
      async: false,
      data:{refno, shopid},
      beforeSend: function(){
        showCover('Loading data ...');
      },
      success: function(data){
        hideCover();
        // console.log("tangina");
        return_data = data.vouchers;
        // console.log(data);
      },
      error: function(){
        hideCover();
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again"
        });
      }
    });
    // console.log(return_data);
    return return_data;
    // $.ajax({
    //   url: base_url+'profile/Customer_profile/get_vouchers',
    //   type: 'post',
    //   data:{refno,shopid},
    //   beforeSend: function(){
    //     showCover('Loading data ...');
    //   },
    //   success: function(data){
    //     hideCover();
    //     return_data = data;
    //   },
    //   error: function(){
    //     hideCover();
    //     showToast({
    //         type: "error",
    //         css: "toast-top-full-width mt-5",
    //         msg: "Something went wrong. Please try again"
    //     });
    //   }
    // });
  }

  $(document).on('click', '.btn_load_more', function(){
    const that = $(this);
    const status = $(this).data('status');
    const wrapper = $(this).data('wrapper');
    $.ajax({
      url: base_url+'profile/Customer_profile/purchase_load_more',
      type: 'post',
      data: {status: status},
      cache: false,
      dataType: 'json',
      beforeSend: function(){
        showCover('Loading data ...');
      },
      success: function(data){
        hideCover();
        if(data.success == true){
          console.log(data.orders.length);
          if(data.orders_count == 0){
            that.hide();
          }

          data.orders.forEach((order) => {

            $.each(data.order_shops, (i, shops) => {
              if(order.reference_num == shops.reference_num){
                let html2 = "";
                let vouchers_html = "";
                let voucher_amount_html = "";
                let total_amount = 0;
                let order_status = "";
                let total = 0;

                if(shops.orderstatus == 'po'){
                  order_status = "Processing Order";
                }else if(shops.orderstatus == 'rp'){
                  order_status = "Ready for Pickup";
                }else if(shops.orderstatus == 'bc'){
                  order_status = "Booking Confirmed";
                }else if(shops.orderstatus == 'f'){
                  order_status = "Fulfilled";
                }else if(shops.orderstatus == 's'){
                  order_status = "Shipped";
                }else if(shops.orderstatus == 'p' && shops.payment_status == 1){
                  order_status = 'Ready for processing';
                }else{
                  order_status = "Waiting for payment";
                }

                html2 += `<div class="product-card px-0 pb-0">`;

                  html2 +=
                  `
                    <div class="product-card-header p-2">
                      <div class="row">
                        <div class="col">
                          <div class="row no-gutters">
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <div><img class="img-thumbnail shop-logo" style="width: 50px;" src="${s3_url}assets/img/shops-60/${shops.logo}"
                                onerror="this.onerror=null; this.src=${s3_url}assets/img/shops-60/${shops.logo}"></div>
                            </div>
                            <div class="col d-flex align-items-center">
                              <div class="product-card-title" style = "width:100%;">
                                ${shops.shopname}
                                <a data-url="${base_url}store/${shops.shopurl}" class = "btn btn-sm btn-shoplink ml-1"><i class="fa fa-home mr-1"></i>View Shop</a>
                              </div>
                            </div>
                            <div class="col text-right order-status" >
                              <p style = "font-weight:0;font-size:10px;margin:0;white-space:nowrap;">[${order.reference_num}]</p>
                              <p style = "font-size:10px;font-weight:bold;margin:0;white-space:nowrap;">[ ${(order.payment_status == 1) ? order_status : "Waiting for Payment"} ]</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  `;

                  html2 += `<div class="product-card-body px-3 order-details">`;
                    $.each(data.order_logs, (i, logs) => {
                      if(order.order_id == logs.order_id && shops.id == logs.sys_shop){
                      total += parseFloat(logs.total_amount);
                      html2 +=
                      `
                        <div class="product-card-item">
                          <div class="row no-gutters">
                            <div class="col">
                              <div class="row no-gutters">
                                <div class="col-2 col-md-1">
                                  <div class="product-card-image" style="background-image: url(${s3_url}assets/img/${shops.shopcode}/products-50/${logs.product_id}/1-${logs.product_id}.jpg)"></div>
                                </div>
                                <div class="co product-card-content">
                                  <div class="product-card-name">
                                    ${logs.itemname}
                                  </div>
                                  <div class="product-card-quantity">
                                    Quantity: ${logs.quantity} Unit Price: &#8369; ${logs.price}
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-3 col-md-2 d-none d-md-block">
                              <div class="product-card-price">
                                ${logs.otherinfo}
                              </div>
                            </div>
                            <div class="col-2">
                              <div class="product-card-price" style = "white-space: nowrap;">
                                &#8369; ${logs.total_amount}
                              </div>
                            </div>
                          </div>
                        </div>
                        `
                      }
                    });
                  html2 +=
                  `
                  <div class="product-card-item">
                    <div class="row no-gutters">
                      <div class="col d-none d-md-block">
                        <div class="row no-gutters">
                          <div class="col-2 col-md-1 ">
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
                          &#8369; ${shops.delivery_amount}
                        </div>
                      </div>
                    </div>
                  </div>
                  `;
                  html2 += `</div>`;
                  let vouchers = get_vouchers(order.reference_num,shops.id);
                  html2 += `<div class="product-card-body px-3 shipping-details" style = "display:none;">`;
                    // console.log('referral', order.referral_code);
                    html2 +=
                    `
                    <div class="product-card-item">
                      <div class="row no-gutters">
                        <!-- CONTACT PERSON AND ADDRESS -->
                        <div class="col-12 col-lg-${vouchers != 0 ? `6` : `12`}">
                          <div class="form-group row">
                            <div class="col-md-8 mb-2">
                              <input type="text" class="form-control" value = "${order.name}">
                              <small class = "font-weight-bold">Contact Person</small>
                            </div>
                            <div class="col-md-4 mb-2">
                              <input type="text" class="form-control" value = "${order.conno}">
                              <small class = "font-weight-bold">Contact no.</small>
                            </div>
                            <div class="col-md-12 mb-2">
                              <input type="text" class="form-control" value = "${order.address}">
                              <small class = "font-weight-bold">Shipping Address</small>
                            </div>
                            <div class="col-md-6 mb-2">
                              <input type="text" class="form-control" value = "${order.date_ordered}">
                              <small class = "font-weight-bold">Date Ordered</small>
                            </div>
                            <div class="col-md-6 mb-2">
                              <input type="text" class="form-control" value = "${(shops.date_shipped != '0000-00-00 00:00:00') ? shops.date_shipped : '---'}">
                              <small class = "font-weight-bold">Date Shipped</small>
                            </div>
                            <div class="col-12 ">
                              <input type="text" class="form-control" value = "${(order.referral_code !== null) ? order.referral_code : '---'}">
                              <small class="font-weight-bold">Referral Code</small>
                            </div>
                          </div>
                        </div>

                          `;


                        if(vouchers != 0){
                          html2 +=
                            `
                          <!-- SHIPPING FEE AND VOUCHERS -->
                          <div class="col-12 col-lg-6">
                            <div class="form-group row">
                              <div class="col-md-6">
                                <p class="font-weight-bold d-flex align-items-end justify-content-center m-0" style="font-size: 11px !important">Voucher(s):</p>
                              </div>
                            </div>
                            `;

                            $.each(vouchers, function(i, voucher){
                              html2 +=
                              `
                                <div class="form-group row">
                                  <div class="col-6 col-md-6 d-flex align-items-end justify-content-center mb-2">
                                    <span class="badge badge-pill voucher-pill p-2 ml-1">
                                      <i class="fa fa-tag" style = "font-size:8px;"></i>
                                      ${voucher['voucher_code']}
                                    </span>
                                  </div>
                                  <div class="col-6 col-md-6 mb-2">
                                    <input type="text" class="form-control text-right input-numbers" value = "- &#8369; ${numberFormatter(voucher['amount'])}">
                                  </div>
                                </div>
                              `;
                            });
                            html2 += `</div>`;
                          }


                html2 += `
                        </div>
                      </div>
                        `;

                  html2 += `</div>`;

                  html2 +=
                  `
                  <!-- ORDER TIMELINE -->
                  <div class="product-card-body px-3 order-timeline" style = "display:none">
                    <div class="row no-gutters mt-2">
                    <div class="col-12 col-md-9 offset-md-3">
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
                                <small class = "portal-timeline-vertical__title">${order.date_ordered}</small><br>
                            </div>
                  `;

                  if(shops.payment_status == 1){
                    html2 +=
                    `
                    <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                        <div class="portal-timeline-vertical__node">
                          <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                          </i>
                        </div>
                        <small class = "portal-timeline-vertical__title">Payment for order has been confirmed.</small><br>
                        <small class = "portal-timeline-vertical__title">${order.payment_date}</small><br>
                    </div>
                    `;
                  }

                    var histories = get_order_history(order.reference_num,shops.id);
                    if(histories != 0){

                      $.each(histories, function(i, history){
                        html2 +=
                        `
                          <div class="portal-timeline-vertical__item portal-timeline__step-1 portal-timeline-vertical__item--active ">
                              <div class="portal-timeline-vertical__node">
                                <i class="fa fa-check" aria-hidden="true" rel="tooltip">
                                </i>
                              </div>
                              <small class = "portal-timeline-vertical__title">${history.action}</small><br>
                              <small class = "portal-timeline-vertical__title">${history.description}</small><br>
                              <small class = "portal-timeline-vertical__title">${history.date_created}</small><br>
                          </div>
                        `;
                      });
                    }

                    html2 +=
                    `
                              </div>
                          </div>
                      </div>
                    `;
                  let total_oder_amount = total + parseFloat(shops.delivery_amount);
                  // console.log('total', total);
                  // console.log('shipping', shops.delivery_amount);
                  html2 +=
                  `</div>
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
                      Total: &#8369; ${numberFormatter(total_oder_amount)}
                    </button>
                  </div>
                  `;

                html2 += `</div>`;

              $(`.${wrapper}`).append(html2);

            }
          });
          });
        }else{
          // showToast({
          //     type: "warning",
          //     css: "toast-top-full-width mt-5",
          //     msg: data.message
          // });
          that.hide();
        }
      },
      error: function(jqXHR, textStatus, errorThrown){
        showToast({
            type: "warning",
            css: "toast-top-full-width mt-5",
            msg: "Slow connection. Please try again."
        });
        hideCover();
      }
    });
  });

  $(document).on('click', '#all-tab', function(){
    my_orders2('all','purchase_wrapper');
    // $.ajax({
    //   url: base_url+'profile/Customer_profile/purchases_status',
    //   type: 'post',
    //   dataType: 'json',
    //   data: {status: 'all'},
    //   cache: false,
    //   beforeSend: function(){
    //     showCover('Loading data ...');
    //   },
    //   success: function(data){
    //     hideCover();
    //     if(data.success == true){
    //       if(data.orders.length < 5){
    //         $('.btn_load_more').hide();
    //       }
    //       $('.purchase_wrapper').html('');
    //       data.orders.forEach((order) => {
    //         let html2 = "";
    //         html2 += `<div class="product-card">`;
    //         $.each(data.order_shops, (i, shops) => {
    //           if(order.reference_num == shops.reference_num){
    //             html2 +=
    //             `
    //
    //               <div class="product-card-header mb-3">
    //                 <div class="row">
    //                   <div class="col">
    //                     <div class="row no-gutters">
    //                       <div class="col-1 d-flex align-items-center justify-content-end">
    //                           <div><img class="img-thumbnail" style="width: 50px;" src="${base_url}assets/img/shops-60/webp/${shops.logo}.webp"
    //                           onerror="this.onerror=null; this.src='${base_url}assets/img/shops-60/${shops.logo.split('.').slice(0, -1).join('.')}.jpg'"></div>
    //                       </div>
    //                       <div class="col d-flex align-items-center">
    //                         <div class="product-card-title" style = "width:100%;">
    //                           ${shops.shopname} <span class = "float-right">[ ${(order.payment_status == 1) ? "To Ship" : "To Pay"} ]</span>
    //                         </div>
    //                       </div>
    //                     </div>
    //                   </div>
    //                 </div>
    //               </div>
    //               <div class="product-card-body mb-3">
    //
    //             `;
    //
    //             $.each(data.order_logs, (i, logs) => {
    //               if(order.order_id == logs.order_id && shops.id == logs.sys_shop){
    //                 html2 +=
    //                 `
    //                 <div class="product-card-item">
    //                   <div class="row no-gutters">
    //                     <div class="col">
    //                       <div class="row no-gutters">
    //                         <div class="col-2 col-md-1">
    //                           <div class="product-card-image" style="background-image: url(${base_url}assets/img/${shops.shopcode}/products-40/${logs.product_id}/0-${logs.product_id}.jpg)"></div>
    //                         </div>
    //                         <div class="co product-card-content">
    //                           <div class="product-card-name">
    //                             ${logs.itemname}
    //                           </div>
    //                           <div class="product-card-quantity">
    //                             Quantity: ${logs.quantity}
    //                           </div>
    //                         </div>
    //                       </div>
    //                     </div>
    //                     <div class="col-3 col-md-2 d-none d-md-block">
    //                       <div class="product-card-price">
    //                         ${logs.otherinfo}
    //                       </div>
    //                     </div>
    //                     <div class="col-2">
    //                       <div class="product-card-price">
    //                         ₱ ${logs.total_amount}
    //                       </div>
    //                     </div>
    //                     <div class="col-1">
    //                       <div class="product-card-delete removeCart" data-id = "" data-shopid = "" data-display = "">
    //                       </div>
    //                     </div>
    //                   </div>
    //                 </div>
    //                 `;
    //               }
    //             });
    //             html2 += `</div>`; // end of product-card-body
    //             var referral = (order.referral_code != null)
    //             ? `<br />Referral Code: <p>${order.referral_code}</p>`
    //             : `<br />Referral Code: <p>---</p>`;
    //             html2 +=
    //             `
    //               <div class="product-card-body py-3 product-card-total">
    //                 <div class="product-card-item">
    //                   <div class="row no-gutters">
    //                     <div class="col product-card-name pl-2">
    //                       Date Ordered: <p style = "font-size:11px !important;">${order.date_ordered}</p><br />
    //                       Shipping Address: <p style = "font-size:11px !important;">${order.address}</p>
    //                       ${referral}
    //                     </div>
    //                     <div class="col product-card-name text-right">
    //                       Shipping Fee:
    //                     </div>
    //                     <div class="col-4 col-md-4">
    //                       <div class="product-card-price">
    //                         ₱ ${shops.delivery_amount}
    //                       </div>
    //                     </div>
    //                     <div class="col-1"></div>
    //                   </div>
    //                 </div>
    //               </div>
    //             `;
    //           }
    //
    //
    //         });
    //
    //         html2 +=
    //         `
    //           <div class="product-card-body py-3 product-card-total">
    //             <div class="product-card-item">
    //               <div class="row no-gutters">
    //                 <div class="col product-card-name text-right">
    //                   Total:
    //                 </div>
    //                 <div class="col-5 col-md-4">
    //                   <div class="product-card-price">
    //                     ₱ ${numberFormatter(parseFloat(order.total_amount) + parseFloat(order.delivery_amount),false)}
    //                   </div>
    //                 </div>
    //                 <div class="col-1"></div>
    //               </div>
    //             </div>
    //           </div>
    //         `;
    //
    //         html2 += "</div>";
    //
    //         $('.purchase_wrapper').append(html2);
    //
    //       });
    //     }else{
    //       $('.btn_load_more').hide();
    //       $('.purchase_wrapper_toship').html(
    //         `
    //           <div class="form-group row">
    //             <div class="col-12 text-center">
    //               <h6>You did not purchase anything yet.</h6>
    //               <a href = "${base_url}" class="btn portal-primary-btn">Start Shopping</a>
    //             </div>
    //           </div>
    //         `
    //       );
    //     }
    //   },
    //   error: function(){
    //     hideCover();
    //     showToast({
    //         type: "error",
    //         css: "toast-top-full-width mt-5",
    //         msg: "Something went wrong. Please try again"
    //     });
    //   }
    // });
  });

  $(document).on('click', '#topay-tab', function(){
    my_orders('to_pay','purchase_wrapper_topay');
    // $.ajax({
    //   url: base_url+'profile/Customer_profile/purchases_status',
    //   type: 'post',
    //   dataType: 'json',
    //   data: {status: 'to_pay'},
    //   cache: false,
    //   beforeSend: function(){
    //     showCover('Loading data ...');
    //   },
    //   success: function(data){
    //     hideCover();
    //     if(data.success == true){
    //       if(data.orders.length < 5){
    //         $('.btn_load_more').hide();
    //       }
    //       $('.purchase_wrapper_topay').html('');
    //       data.orders.forEach((order) => {
    //         let html2 = "";
    //         html2 += `<div class="product-card">`;
    //         $.each(data.order_shops, (i, shops) => {
    //           if(order.reference_num == shops.reference_num){
    //             html2 +=
    //             `
    //
    //               <div class="product-card-header mb-3">
    //                 <div class="row">
    //                   <div class="col">
    //                     <div class="row no-gutters">
    //                       <div class="col-1 d-flex align-items-center justify-content-end">
    //                           <div><img class="img-thumbnail" style="width: 50px;" src="${base_url}assets/img/shops-60/webp/${shops.logo}.webp"
    //                           onerror="this.onerror=null; this.src='${base_url}assets/img/shops-60/${shops.logo.split('.').slice(0, -1).join('.')}.jpg'"></div>
    //                       </div>
    //                       <div class="col d-flex align-items-center">
    //                         <div class="product-card-title" style = "width:100%;">
    //                           ${shops.shopname} <span class = "float-right">[ ${(order.payment_status == 1) ? "To Ship" : "To Pay"} ]</span>
    //                         </div>
    //                       </div>
    //                     </div>
    //                   </div>
    //                 </div>
    //               </div>
    //               <div class="product-card-body mb-3">
    //
    //             `;
    //
    //             $.each(data.order_logs, (i, logs) => {
    //               if(order.order_id == logs.order_id && shops.id == logs.sys_shop){
    //                 html2 +=
    //                 `
    //                 <div class="product-card-item">
    //                   <div class="row no-gutters">
    //                     <div class="col">
    //                       <div class="row no-gutters">
    //                         <div class="col-2 col-md-1">
    //                           <div class="product-card-image" style="background-image: url(${base_url}assets/img/${shops.shopcode}/products-40/${logs.product_id}/0-${logs.product_id}.jpg)"></div>
    //                         </div>
    //                         <div class="co product-card-content">
    //                           <div class="product-card-name">
    //                             ${logs.itemname}
    //                           </div>
    //                           <div class="product-card-quantity">
    //                             Quantity: ${logs.quantity}
    //                           </div>
    //                         </div>
    //                       </div>
    //                     </div>
    //                     <div class="col-3 col-md-2 d-none d-md-block">
    //                       <div class="product-card-price">
    //                         ${logs.otherinfo}
    //                       </div>
    //                     </div>
    //                     <div class="col-2">
    //                       <div class="product-card-price">
    //                         ₱ ${logs.total_amount}
    //                       </div>
    //                     </div>
    //                     <div class="col-1">
    //                       <div class="product-card-delete removeCart" data-id = "" data-shopid = "" data-display = "">
    //                       </div>
    //                     </div>
    //                   </div>
    //                 </div>
    //                 `;
    //               }
    //             });
    //             html2 += `</div>`; // end of product-card-body
    //             var referral = (order.referral_code != null)
    //             ? `<br />Referral Code: <p>${order.referral_code}</p>`
    //             : `<br />Referral Code: <p>---</p>`;
    //             html2 +=
    //             `
    //               <div class="product-card-body py-3 product-card-total">
    //                 <div class="product-card-item">
    //                   <div class="row no-gutters">
    //                     <div class="col product-card-name pl-2">
    //                       Date Ordered: <p style = "font-size:11px !important;">${order.date_ordered}</p><br />
    //                       Shipping Address: <p style = "font-size:11px !important;">${order.address}</p>
    //                       ${referral}
    //                     </div>
    //                     <div class="col product-card-name text-right">
    //                       Shipping Fee:
    //                     </div>
    //                     <div class="col-4 col-md-4">
    //                       <div class="product-card-price">
    //                         ₱ ${shops.delivery_amount}
    //                       </div>
    //                     </div>
    //                     <div class="col-1"></div>
    //                   </div>
    //                 </div>
    //               </div>
    //             `;
    //           }
    //
    //
    //         });
    //
    //         html2 +=
    //         `
    //           <div class="product-card-body py-3 product-card-total">
    //             <div class="product-card-item">
    //               <div class="row no-gutters">
    //                 <div class="col product-card-name text-right">
    //                   Total:
    //                 </div>
    //                 <div class="col-5 col-md-4">
    //                   <div class="product-card-price">
    //                     ₱ ${numberFormatter(parseFloat(order.total_amount) + parseFloat(order.delivery_amount),false)}
    //                   </div>
    //                 </div>
    //                 <div class="col-1"></div>
    //               </div>
    //             </div>
    //           </div>
    //         `;
    //
    //         html2 += "</div>";
    //
    //         $('.purchase_wrapper_topay').append(html2);
    //
    //       });
    //     }else{
    //       $('.btn_load_more').hide();
    //       $('.purchase_wrapper_topay').html(
    //         `
    //           <div class="form-group row">
    //             <div class="col-12 text-center">
    //               <h6>You don't have any orders to pay.</h6>
    //               <a href = "${base_url}" class="btn portal-primary-btn">Start Shopping</a>
    //             </div>
    //           </div>
    //         `
    //       );
    //     }
    //   },
    //   error: function(){
    //     hideCover();
    //     showToast({
    //         type: "error",
    //         css: "toast-top-full-width mt-5",
    //         msg: "Something went wrong. Please try again"
    //     });
    //   }
    // });
  });

  $(document).on('click', '#toship-tab', function(){
    my_orders2('to_ship','purchase_wrapper_toship');
    // $.ajax({
    //   url: base_url+'profile/Customer_profile/purchases_status',
    //   type: 'post',
    //   dataType: 'json',
    //   data: {status: 'to_ship'},
    //   cache: false,
    //   beforeSend: function(){
    //     showCover('Loading data ...');
    //   },
    //   success: function(data){
    //     hideCover();
    //     if(data.success == true){
    //       if(data.orders.length < 5){
    //         $('.btn_load_more').hide();
    //       }
    //       $('.purchase_wrapper_toship').html('');
    //       data.orders.forEach((order) => {
    //         let html2 = "";
    //         html2 += `<div class="product-card">`;
    //         $.each(data.order_shops, (i, shops) => {
    //           if(order.reference_num == shops.reference_num){
    //             html2 +=
    //             `
    //
    //               <div class="product-card-header mb-3">
    //                 <div class="row">
    //                   <div class="col">
    //                     <div class="row no-gutters">
    //                       <div class="col-1 d-flex align-items-center justify-content-end">
    //                           <div><img class="img-thumbnail" style="width: 50px;" src="${base_url}assets/img/shops-60/webp/${shops.logo}.webp"
    //                           onerror="this.onerror=null; this.src='${base_url}assets/img/shops-60/${shops.logo.split('.').slice(0, -1).join('.')}.jpg'"></div>
    //                       </div>
    //                       <div class="col d-flex align-items-center">
    //                         <div class="product-card-title" style = "width:100%;">
    //                           ${shops.shopname} <span class = "float-right">[ ${(order.payment_status == 1) ? "To Ship" : "To Pay"} ]</span>
    //                         </div>
    //                       </div>
    //                     </div>
    //                   </div>
    //                 </div>
    //               </div>
    //               <div class="product-card-body mb-3">
    //
    //             `;
    //
    //             $.each(data.order_logs, (i, logs) => {
    //               if(order.order_id == logs.order_id && shops.id == logs.sys_shop){
    //                 html2 +=
    //                 `
    //                 <div class="product-card-item">
    //                   <div class="row no-gutters">
    //                     <div class="col">
    //                       <div class="row no-gutters">
    //                         <div class="col-2 col-md-1">
    //                           <div class="product-card-image" style="background-image: url(${base_url}assets/img/${shops.shopcode}/products-40/${logs.product_id}/0-${logs.product_id}.jpg)"></div>
    //                         </div>
    //                         <div class="co product-card-content">
    //                           <div class="product-card-name">
    //                             ${logs.itemname}
    //                           </div>
    //                           <div class="product-card-quantity">
    //                             Quantity: ${logs.quantity}
    //                           </div>
    //                         </div>
    //                       </div>
    //                     </div>
    //                     <div class="col-3 col-md-2 d-none d-md-block">
    //                       <div class="product-card-price">
    //                         ${logs.otherinfo}
    //                       </div>
    //                     </div>
    //                     <div class="col-2">
    //                       <div class="product-card-price">
    //                         ₱ ${logs.total_amount}
    //                       </div>
    //                     </div>
    //                     <div class="col-1">
    //                       <div class="product-card-delete removeCart" data-id = "" data-shopid = "" data-display = "">
    //                       </div>
    //                     </div>
    //                   </div>
    //                 </div>
    //                 `;
    //               }
    //             });
    //             html2 += `</div>`; // end of product-card-body
    //             var referral = (order.referral_code != null)
    //             ? `<br />Referral Code: <p>${order.referral_code}</p>`
    //             : `<br />Referral Code: <p>---</p>`;
    //             html2 +=
    //             `
    //               <div class="product-card-body py-3 product-card-total">
    //                 <div class="product-card-item">
    //                   <div class="row no-gutters">
    //                     <div class="col product-card-name pl-2">
    //                       Date Ordered: <p style = "font-size:11px !important;">${order.date_ordered}</p><br />
    //                       Shipping Address: <p style = "font-size:11px !important;">${order.address}</p>
    //                       ${referral}
    //                     </div>
    //                     <div class="col product-card-name text-right">
    //                       Shipping Fee:
    //                     </div>
    //                     <div class="col-4 col-md-4">
    //                       <div class="product-card-price">
    //                         ₱ ${shops.delivery_amount}
    //                       </div>
    //                     </div>
    //                     <div class="col-1"></div>
    //                   </div>
    //                 </div>
    //               </div>
    //             `;
    //           }
    //
    //
    //         });
    //
    //         html2 +=
    //         `
    //           <div class="product-card-body py-3 product-card-total">
    //             <div class="product-card-item">
    //               <div class="row no-gutters">
    //                 <div class="col product-card-name text-right">
    //                   Total:
    //                 </div>
    //                 <div class="col-5 col-md-4">
    //                   <div class="product-card-price">
    //                     ₱ ${numberFormatter(parseFloat(order.total_amount) + parseFloat(order.delivery_amount),false)}
    //                   </div>
    //                 </div>
    //                 <div class="col-1"></div>
    //               </div>
    //             </div>
    //           </div>
    //         `;
    //
    //         html2 += "</div>";
    //
    //         $('.purchase_wrapper_toship').append(html2);
    //
    //         // console.log("psok");
    //       });
    //     }else{
    //       $('.btn_load_more').hide();
    //       $('.purchase_wrapper_toship').html(
    //         `
    //           <div class="form-group row">
    //             <div class="col-12 text-center">
    //               <h6>You don't have any orders to ship.</h6>
    //               <a href = "${base_url}" class="btn portal-primary-btn">Start Shopping</a>
    //             </div>
    //           </div>
    //         `
    //       );
    //     }
    //   },
    //   error: function(){
    //     hideCover();
    //     showToast({
    //         type: "error",
    //         css: "toast-top-full-width mt-5",
    //         msg: "Something went wrong. Please try again"
    //     });
    //   }
    // });
  });

  $(document).on('click', '.nav-link', function(){
    $('.btn_load_more').show();
  });

  $(document).on('click', '.btn-vieworder', function(){
    let url = $(this).data('url');
    window.open(url);
  });

  $(document).on('click', '.btn-shoplink', function(){
    let url = $(this).data('url');
    window.open(url);
  });

  $(document).on('click', '.btn-tab', function(){
    let tab = $(this).data('tab');

    $(this).parent().siblings('.product-card-body').hide();
    $(this).parent().siblings('.'+tab).show();
  });

  $(document).on('error', '.shop-logo', function(){
    this.src = `${s3_url}assets/img/shops-60/${shops.logo.split('.').slice(0, -1).join('.')}.jpg'`;
  });
});
