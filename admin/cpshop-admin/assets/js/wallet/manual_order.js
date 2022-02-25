function validateEmail(email) {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

function error_filter() {
  var error = 0;
  var errorMsg = "";



  $('.rq').each(function () {
    let id = $(this).get(0).id;
    if ($(this).val() == "") {
      $(this).css("border", "1px solid #ef4131");
      $(this).siblings('.select2-container').css('border', '1px solid #ef4131');
    } else {
      $(this).css("border", "1px solid gainsboro");
      $(this).siblings('.select2-container').css("border", "none");
      $(this).siblings('.select2-container').css("border-bottom", "1px solid gainsboro");
    }
  });

  $('.rq').each(function(){
    if($(this).val() == ""){
      $(this).focus();
      error = 1;
      errorMsg = "Please fill up all required fields.";
      return false;
    }
  });

  if ($('#customer').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if ($('#email').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if ($('#contact_no').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if ($('#city').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
    // console.log(error);
    // console.log(errorMsg);
  }

  if ($('#date_ordered').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if ($('#date_shipped').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if ($('#shop').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if ($('#item').val() == "") {
    error = 1;
    errorMsg = "Please fill up all required fields";
  }

  if($('#quantity').val() == 0){
    error = 1;
    errorMsg = "Please fill up all required fields";
    $('#quantity').css("border", "1px solid #ef4131");
  }

  if (validateEmail($('#email').val()) == false) {
    error = 1;
    errorMsg = "Invalid email format";
    $('#email').css("border", "1px solid #ef4131");
  } else {
    $('#email').css("border", "1px solid gainsboro");
  }

  if ($('#contact_no').val().length < 7) {
    error = 1;
    errorMsg = "Invalid contact number";
    $('#contact_no').css("border", "1px solid #ef4131");
  } else {
    $('#contact_no').css("border", "1px solid gainsboro");
  }

  if ($('#customer').val().length < 4) {
    error = 1;
    errorMsg = "Customer name is to short";
    $('#customer').css("border", "1px solid #ef4131");
  } else {
    $('#customer').css("border", "1px solid gainsboro");
  }

  return result = { error: error, errorMsg: errorMsg };
}

$(function () {
  var base_url = $("body").data('base_url');
  var filter = {
    search: '',
    shop: '',
    from: '',
    to: ''
  };
  let orders = [];
  let productids = [];
  let item = {
    productid: '',
    quantity: 0,
    amount: 0,
    price: 0
  };
  let total = {
    quantity: 0,
    amount: 0.00
  }

  let order_tbl = $('#order_tbl').DataTable({
    destroy: true,
    columnDefs: [{
      targets: [1, 2, 3],
      className: 'text-right'
    }],
    order:[[4,'asc']],
    columnDefs: [{
      targets: [0,1,2,3], orderable: false
    }]
  });

  function gen_manual_order_tbl(search) {
    var manual_order_tbl = $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order": [[4, 'desc']],
      "ajax": {
        url: base_url + 'wallet/Manual_order/list_table',
        type: 'post',
        data: {
          searchValue: search
        },
        beforeSend: function () {
          $.LoadingOverlay("show");
        },
        complete: function (data) {
          $.LoadingOverlay("hide");
          var response = $.parseJSON(data.responseText);

					if(response.data.length > 0){
						$('.btnExport').show(100);
					}
					else{
						$('#btnExport').hide(100);
          }

          $("#_search").val(JSON.stringify(this.data));
        },
        error: function () {
          $.LoadingOverlay("hide");
        }
      }
    });
  };

  gen_manual_order_tbl(JSON.stringify(filter));

  function reset_form(all = true) {
    let cust = $('#customer').val();
    let date_ordered = $('#date_ordered').val();
    let date_shipped = $('#date_shipped').val();
    let contact_no = $('#contact_no').val()
    let email = $('#email').val();
    let city = $('#city').val();
    $('#customer').val('');
    $('#date_ordered').val('');
    $('#date_shipped').val('');
    $('#contact_no').val('');
    $('#email').val('');
    $('#city option[value=""]').prop('selected', true).trigger('change');
    $('#products').html('<option value="">------</option>');
    $('#branches').html('<option value="">------</option>');
    $('#quantity').val('0');
    $('#shipping').val('0');
    $('#shipping_fee').text('0.00');
    $('#total_amount').text('0.00');
    order_tbl.clear().draw();
    orders = [];
    productids = [];
    item.productid = '';
    item.quantity = 0;
    item.amount = 0;
    item.price = 0;
    total.quantity = 0;
    total.amount = 0.00;
    if (all === true) {
      $('#shop option[value=""]').prop('selected', true).trigger('change');
      $('#add_modal').modal('hide');
    } else {
      $('#customer').val(cust);
      $('#date_ordered').val(date_ordered);
      $('#date_shipped').val(date_shipped);
      $('#contact_no').val(contact_no)
      $('#email').val(email);
      $('#city option[value="' + city + '"]').prop('selected', true).trigger('change');
    }

  }

  $(document).on('click', '#btn_addorder', function () {
    // $.LoadingOverlay('show');
    // $('#main').hide();
    // $('#sub').show();
    // $.LoadingOverlay('hide');
    reset_form();
    $('#add_modal').modal();
  });

  $(document).on('click', '.btn_back', function () {
    $.LoadingOverlay('show');
    $('#main').show();
    $('#sub').hide();
    $.LoadingOverlay('hide');
  });

  $(document).on('change', '#shop', function () {
    let that = $(this);
    let shopid = $(this).val();
    if (shopid != "") {
      $.ajax({
        url: base_url + 'wallet/Manual_order/get_shop_order_details',
        type: 'post',
        data: { shopid },
        cache: false,
        beforeSend: function () {
          $.LoadingOverlay('show');
          $('#branches').prop('disabled', true);
          $('#products').prop('disabled', true);
          $('#products').html('<option value="">------</option>');
          $('#branches').html('<option value="">------</option>');
        },
        success: function (data) {
          $.LoadingOverlay('hide');
          reset_form(false);
          if (data.success == 1) {
            if (data.products.length > 0) {
              $('#products').prop('disabled', false);
              $.each(data.products, function (i, val) {
                $('#products').append(
                  `<option value="${val['Id']}" data-price = "${val['price']}" data-pname = '${val['itemname']} (${val['otherinfo']})'>
                  ${val['itemname']} (${val['otherinfo']})
                </option>`);
              });
            }

            if (data.branches.length > 0) {
              $('#branches').prop('disabled', false);
              $.each(data.branches, function (i, val) {
                $('#branches').append(`<option value="${val['id']}">${val['branchname']}</option>`);
              })
            }

            let shop_shippingfee = $(that).children('option:selected').data('shipping_fee');
            // console.log(shop_shippingfee);
            // $('#shipping').val(shop_shippingfee).trigger('keyup');

          } else {
            //messageBox(data.message, 'Warning', 'warning');
            showCpToast("warning", "Warning!", data.message);
          }
        },
        error: function () {
          $.LoadingOverlay('hide');
          //messageBox(data.message, '', '');
          showCpToast("error", "Error!", data.message);
        }
      });
    }
  });

  $(document).on('change', '#products', function () {
    if ($(this).val() != '') {
      let price = $('#products option:selected').data('price');
      let product_name = $('#products option:selected').data('pname');
      $(this).data('price', price);
      $(this).data('pname', product_name);
      $('#quantity').val(1);
    } else {
      $('#quantity').val(0);
    }
  });

  $(document).on('change', '#city', function () {
    let citymuncode = $(this).children('option:selected').data('citymuncode');
    let provcode = $(this).children('option:selected').data('provcode');
    let regcode = $(this).children('option:selected').data('regcode');

    $(this).data('citymuncode', citymuncode);
    $(this).data('provcode', provcode);
    $(this).data('regcode', regcode);
  });

  $(document).on('keyup', '#shipping', function () {
    let shipping = parseFloat($(this).val());
    $('#shipping_fee').text(accounting.formatMoney(shipping));
    $('#total_amount').text(accounting.formatMoney(total.amount + shipping));
  });

  $(document).on('click', '#btn_add_order', function () {
    let item = {
      productid: '',
      quantity: 0,
      amount: 0,
      price: 0
    };
    let shipping = parseFloat($('#shipping').val());
    let quantity = $('#quantity').val();
    let product_name = $('#products').data('pname');
    let row_count = order_tbl.rows().count();
    // console.log(row_count);
    var filter_result = error_filter();
    var error = 0;
    var errorMsg = "";
    error = filter_result.error;
    errorMsg = filter_result.errorMsg;

    if(error == 1){
      // console.log("1st error");
      //messageBox(errorMsg, 'Warning', 'warning');
      showCpToast("warning", "Warning!", errorMsg);
      return;
    }

    // console.log("entry point");

    if(error == 0){

      let price = $('#products').data('price');
      let amount = parseFloat(price) * parseInt(quantity);
      item.productid = $('#products').val();
      item.quantity = quantity;
      item.amount = amount;
      item.price = price;
      total.quantity += parseInt(quantity);
      total.amount += amount;

      if ($.inArray($('#products').val(), productids) !== -1) {
        var data = order_tbl.rows().data();
        data.rows().data().each(function (val, i) {
          // console.log(i);
          if (val[0] == product_name) {
            // console.log(i);
            let num = val[1];
            let ex_amount = parseFloat(val[3].replace(/,/g, ''));
            let total_quan = parseInt(num) + parseInt(quantity);
            let total = ex_amount + amount;
            let new_data = [
              product_name,
              total_quan,
              accounting.formatMoney(price),
              accounting.formatMoney(total),
              '<center><i class="fa fa-trash text-danger btn_delete" data-id = "' + $('#products').val() + '" data-amount = "' + total + '"></i></center>'
            ]
            order_tbl.row(i).data(new_data).draw();
            orders.forEach((data) => {
              if (data.productid == $('#products').val()) {
                data.quantity = total_quan;
                data.amount = total;
              }
            })
          }
        });

      } else {
        orders.push(item);
        productids.push($('#products').val());
        order_tbl.row.add(
          [
            product_name,
            quantity,
            accounting.formatMoney(price),
            accounting.formatMoney(amount),
            '<center><i class="fa fa-trash text-danger btn_delete" data-id = "' + $('#products').val() + '" data-amount = "' + amount + '"></i></center>'
            // '<center><i class="fa fa-trash text-danger btn_delete"></i></center>'
          ]
        ).draw(false);
      }
      // console.log(shipping);
      // $('#shipping_fee').text(accounting.formatMoney(shipping));
      $('#total_quantity').text(total.quantity);
      $('#total_amount').text(accounting.formatMoney(total.amount + shipping));
    }else{
      // console.log('error');
      //messageBox(errorMsg, 'Warning', 'warning');
      showCpToast("warning", "Warning!", errorMsg);
    }

    // if(parseInt(quantity) == 0 || product_name == "" || $('#date_ordered').val() == "" || $('#date_shipped').val() == "" || $('#customer').val() == "" || $('#email').val() == "" || $('#contact_no').val() == "" || $('#city').val() == ""){
    //   messageBox('Please fill up all required fields.', 'Warning', 'warning');
    //   return ;
    // }
    // console.log(orders);
    // console.log(productids);
  });

  $(document).on('click', '.btn_delete', function () {
    let id = $(this).data('id');
    let amount = $(this).data('amount');
    order_tbl.row($(this).parents('tr')).remove().draw();
    let filtered = orders.filter(function (value) { return value.productid != id });
    let filtered2 = productids.filter(function (value) { return value != id });
    productids = filtered2;
    orders = filtered;
    total.amount -= amount;
    total.amount = (total.amount < 0) ? 0.00 : total.amount;
    let shipping = parseFloat($('#shipping').val());
    $('#total_amount').text(accounting.formatMoney(total.amount + shipping));
    // console.log(productids);
    // console.log(orders);
    // orders.push(filtered);
    // console.log( order_tbl.row( this ).data()[0] );
  });

  $(document).on('click', '#btn_save', function () {

    var error = 0;
    var errorMsg = "";

    var result = error_filter();
    error = result.error;
    errorMsg = result.errorMsg;

    if (error == 0) {
      if (orders.length > 0) {
        $.ajax({
          url: base_url + 'wallet/Manual_order/create',
          type: 'post',
          data: {
            shop: $('#shop').val(),
            branches: $('#branches').val(),
            products: orders,
            total_amount: total.amount,
            date_ordered: $('#date_ordered').val(),
            date_shipped: $('#date_shipped').val(),
            customer: $('#customer').val(),
            city: $('#city').val(),
            citymuncode: $('#city').data('citymuncode'),
            provcode: $('#city').data('provcode'),
            regcode: $('#city').data('regcode'),
            contact_no: $('#contact_no').val(),
            email: $('#email').val(),
            shipping: parseFloat($('#shipping').val())
          },
          beforeSend: function () {
            $.LoadingOverlay('show');
            $('#btn_save').attr('disabled', true);
          },
          success: function (data) {
            $('#btn_save').prop('disabled', false);
            $.LoadingOverlay('hide');
            if (data.success == 1) {
              //messageBox(data.message, 'Success', 'success');
              showCpToast("success", "Success!", data.message);
              gen_manual_order_tbl(JSON.stringify(filter));
              reset_form();
            } else {
              //messageBox(data.message, 'Warning', 'warning');
              showCpToast("warning", "Warning!", data.message);
            }
          },
          error: function () {
            $('#btn_save').prop('disabled', false);
            //messageBox('Something went wrong. Please try again', 'Error', 'error');
            showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            $.LoadingOverlay('hide');
          }
        });
      } else {
        //messageBox('No available product to save. Please try again', 'Warning', 'warning');
        showCpToast("warning", "Warning!", 'No available product to save. Please try again');
      }
    } else {
      //messageBox(errorMsg, 'Warning', 'warning');
      showCpToast("warning", "Warning!", errorMsg);
    }

  });

  $(document).on('click', '#btnSearch', function () {
    filter.search = '';
    filter.shop = $('#select_shop').val() || '';
    filter.from = $('#date_from').val();
    filter.to = $('#date_to').val();

    // console.log(filter);
    // return ;
    gen_manual_order_tbl(JSON.stringify(filter));
  });

  $("#search_hideshow_btn").click(function (e) {
    e.preventDefault();

    var visibility = $('#card-header_search').is(':visible');

    if (!visibility) {
      //visible
      $("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
    } else {
      //not visible
      $("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
    }

    $("#card-header_search").slideToggle("slow");
  });

  $("#search_clear_btn").click(function (e) {
    filter.search = "";
    filter.shop = "";
    filter.from = "";
    filter.to = "";
    // $(".search-input-text").val("");
    $('#date_from').val('');
    $('#date_to').val('');
    $('#select_shop option[value=""]').prop('selected', true);
    // gen_prepayment(JSON.stringify(filter));
    gen_manual_order_tbl(JSON.stringify(filter));
  });
});
