$(function () {
  var base_url = $("body").data('base_url');
  var s3bucket_url = $("body").data('s3bucket_url');
  var access = $(".content-inner").data('access');
  var ini = $(".content-inner").data('ini');
  var billcode = $('#search_billcode').val();

  var token = $('#token').val();
  var filter = {
    search: '',
    status: 1,
    shop: '',
    branch: 'null',
    from: '',
    to: ''
  };

  var main_visible = {};
  var target_visible = {};
  var target_visible_logs ={};
  var orderable = {};

  function get_billing_table(search) {
    var billing = $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "language": {
        "emptyTable": "<i class='fa fa-search mr-2'></i>To show records, kindly select your preferred date range. You may use other filter(s) if there's any"
      },
      "autoWidth": true,
      "order": [[0, 'desc']],
      "columnDefs": [
        // { targets: [6, 7], orderable: false },
        { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 },
        main_visible
      ],
      "ajax": {
        url: base_url + 'accounts/Billing_merchant/get_billing_table',
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
          if (response.recordsTotal > 0) {
          	$('.btnExport').show(100);
          }

          $('input#_search').val(JSON.stringify(response.filters));
        },
        error: function () {
          $.LoadingOverlay("hide");
        }
      }
    });
  };

  get_billing_table(JSON.stringify(filter));

  var resetPaymentForm = function () {
    $('#f_payment').val('');
    $('#f_payment_others').val('');
    $('#f_payment_ref_num').val('');
    $('#f_payment_fee').val('');
    $('#f_payment_notes').val('');
  }

  $(document).on('click', '#btnSearch', function () {
    filter.status = $('#select_status').val() || 1;
    filter.search = $('#search_billcode').val();
    filter.shop = $('#select_shop').val() || '';
    filter.from = $('#date_from').val();
    filter.branch = $('#select_branch').val();
    filter.to = $('#date_to').val();

    get_billing_table(JSON.stringify(filter));
  });

  $(document).on('click', '.btn_view', function () {
    let id = $(this).get(0).id;
    let encrypted_id = $(this).data('encrypted_id');
    let bilcod = $(this).data('ref_num');
    let total_amount = $(this).data('total_amount');
    let total_comrate = $(this).data('total_comrate');
    let processfee = $(this).data('processfee');
    let netamount = $(this).data('netamount');
    let total_amount_w_shipping = $(this).data('total_amount_w_shipping');
    let delivery_amount = $(this).data('delivery_amount');
    let netamount_w_shipping = $(this).data('netamount_w_shipping');
    let currency = $(this).data('currency');
    let c_international = $(this).data('c_international');
    let accountname = $(this).data('accountname');
    let accountno = $(this).data('accountno');
    let bankname = $(this).data('bankname');
    let branch_name = $(this).data('branch_name');
    branch_name = (branch_name == "" || branch_name == null) ? 'Main' : branch_name;

    // if(c_international != undefined && currency != undefined && c_international == 1 && currency != 'PHP'){
      let total_amount_string = $(this).data('total_amount_string');
      let netamount_string = $(this).data('netamount_string');
      let total_amount_w_shipping_string = $(this).data('total_amount_w_shipping_string');
      let delivery_amount_string = $(this).data('delivery_amount_string');
      let netamount_w_shipping_string = $(this).data('netamount_w_shipping_string');
      let processfee_string = $(this).data('processfee_string');
      // console.log(delivery_amount_string);
    // }

    $('.small-thumbnail').html('');

    $.ajax({
      url: base_url + 'accounts/billing/get_billing',
      type: 'post',
      data: { id,bilcod },
      dataType: 'json',
      beforeSend: function () {
        $.LoadingOverlay('show');
      },
      success: function (json_data) {
        $.LoadingOverlay('hide');
        if (json_data.success == true) {
          $('#tm_branchname').html(branch_name);
          $('#tm_accountno').html(accountno);
          $('#tm_accountname').html(accountname);
          $('#tm_bankname').html(bankname);
          $('#exampleModalLabel').html('Billing Code: ' + json_data.message.billcode);
          $('#f_billid').html(json_data.message.id);
          $('#tm_billno').html(json_data.message.billno);
          $('#tm_billcode').html(json_data.message.billcode);
          $('#tm_syshop').html(json_data.message.shopname);
          $('#shopcode').val(json_data.message.shopcode);
          $('#tm_billcode').html(json_data.message.billcode);
          $('#tm_trandate').html(json_data.message.trandate);
          $('#tm_remarks').html(json_data.message.remarks);

          if (json_data.message.ratetype == 'f')
            $('#tm_ratetype').html('Fixed');
          else
            $('#tm_ratetype').html('Percentage');

          $('#tm_processrate').html(json_data.message.processrate);

          if(c_international != undefined && currency != undefined && c_international == 1 && currency != 'PHP'){
            $('#tm_totalamount').html(total_amount_string);
            $('#tm_delivery_amount').html(delivery_amount_string);
            $('#tm_totalamount_w_shipping').html(total_amount_w_shipping_string);
            $('#tm_processfee').html(processfee_string);
            $('#tm_netamount').html(netamount_w_shipping_string);
          }else{
            $('#tm_delivery_amount').html(delivery_amount);
            $('#tm_totalamount').html(accounting.formatMoney(json_data.message.totalamount));
            $('#tm_totalamount_w_shipping').html(total_amount_w_shipping);
            let fee = parseFloat(json_data.message.processfee) + parseFloat(json_data.message.totalcomrate);
            $('#tm_processfee').html(accounting.formatMoney(fee));
            $('#tm_netamount').html(netamount_w_shipping);
            // $('#tm_refcom_totalamount').html(total_comrate);
            if(ini == 'c0Nta1MvSVRTZ2s3anFaVW9ZQ2tiUT09'){ // toktokmall
              // console.log('net', json_data.message.netamount);
              // console.log('sf', json_data.message.delivery_amount);
              let total_process_fee_less_tax = parseFloat(fee) - parseFloat(json_data.message.total_whtax);
              let total_net_amount_w_tax = parseFloat(json_data.message.netamount) + parseFloat(json_data.message.delivery_amount) + parseFloat(json_data.message.total_whtax);
              $('#tm_total_whtax').html(accounting.formatMoney(json_data.message.total_whtax));
              $('#tm_processfee_less_whtax').html(accounting.formatMoney(total_process_fee_less_tax));
              $('#tm_netamount_w_whtax').html(accounting.formatMoney(total_net_amount_w_tax));
            }
          }


          // $('#tm_netamount').html(accounting.formatMoney(json_data.message.netamount));
          $('#tm_paystatus').html(draw_transaction_status(json_data.message.paystatus));

          if(parseFloat(json_data.message.paid_prepayment) != 0){
            if(json_data.message.paystatus == 'Unsettled'){
              $('#unsettled_div').show();
              $('#unsettled_date').html(json_data.message.prepaymentpaid_date);
              $('#settled_amount').html(accounting.formatMoney(json_data.message.paid_prepayment));
              $('#unsettled_amount').html(accounting.formatMoney(json_data.message.remaining_to_pay));
              $('#unsettled_payref').html(json_data.message.unsettled_payref);
            }else{
              $('#unsettled_div').show();
              $('#unsettled_date').html(json_data.message.prepaymentpaid_date);
              $('#settled_amount').html(accounting.formatMoney(json_data.message.paid_prepayment));
              $('#unsettled_amount').html(accounting.formatMoney(json_data.message.remaining_to_pay));
              $('#unsettled_payref').html(json_data.message.unsettled_payref);
              $('#unsettled_status').html("<label class='badge badge-success'>Settled</label>");
            }

          }

          if (json_data.message.paystatus != 'Settled') {
            $('.grp_payment').hide();
            $('.btn_tbl_pay').show();
          }
          else {
            $('#tm_paiddate').html(json_data.message.paiddate);
            $('#tm_paidamount').html(accounting.formatMoney(json_data.message.paidamount));
            $('#tm_paytype').html(json_data.message.pay_type);
            $('#tm_payref').html(json_data.message.payref);
            if(json_data.message.payattach != null){
              let attachments = json_data.message.payattach.split(',');
              $('#tm_payattach').html('<a href="#" id = "btn_view_attachment" data-url = "'+attachments[0]+'">View Image</a>');
              for (var i = 0; i < attachments.length; i++) {
                let active = (i == 0) ? 'active_pic' : '';
                $('.small-thumbnail').append(
                  `
                  <div class="col-md-2 pr-1">
                    <div class="attach-pics img-thumbnail p-0 ${active}" data-url = "${attachments[i]}" style = "min-height:64px;">
                      <img src="${s3bucket_url}${attachments[i]}" alt="" style = "object-fit:contain;"/>
                    </div>
                  </div>

                  `
                );
              }
            }else{
              $('#tm_payattach').html('No Attachment Available')
            }
            // $('#tm_payattach').html(json_data.message.payattach);
            $('#tm_payremarks').html(json_data.message.payremarks);
            $('.grp_payment').show();
            $('.btn_tbl_pay').hide();
          }
          const billing_data = {
            search: '',
            shop: json_data.message.syshop,
            trandate: json_data.message.trandate,
            ratetype: json_data.message.ratetype,
            processrate: json_data.message.processrate,
            branch_id: json_data.message.branch_id,
            per_branch_billing: json_data.message.per_branch_billing,
            totalamount: total_amount,
            processfee: processfee,
            netamount: netamount,
            netamount_w_shipping: netamount_w_shipping,
            delivery_amount: delivery_amount,
            total_comrate: total_comrate
          }
          $('#billing_id').val(encrypted_id);
          get_billing_breakdown_table(billing_data);
          // gen_branch_billing_tbl('', json_data.message.syshop, json_data.message.trandate);
          $('#main_body').hide();
          $('#sub_body').show();
        } else {
          //messageBox(data.message, 'Warning', 'warning');
          showCpToast("warning", "Warning!", data.message);
          setTimeout(function(){location.reload()}, 2000);
        }
      },
      error: function () {
        //notificationError('Error', 'Oops! Something went wrong. Please try again.');
        showCpToast("error", "Error!", 'Oops! Something went wrong. Please try again.');
        setTimeout(function(){location.reload()}, 2000);
        $.LoadingOverlay('hide');
      }
    });
  });

  $(document).delegate('.btn_tbl_pay', 'click', function (e) {
    $("#tag_payment").prop("checked", false);
    $('#modal_save').modal('hide');
    let status = $('#tm_paystatus').html();
    $('.grp_payment_others').hide();
    // $('.grp_payment-p').hide();
    $.LoadingOverlay('show');
    $('#f_id-p').val($('#tm_billcode').html().trim());
    $('#tm_header_ref').html('Settlement for Billing Code ' + $('#tm_billcode').html());
    $('#tm_order_date-p').html(' ' + $('#tm_trandate').html());
    $('#tm_order_reference_num-p').html(' ' + $('#tm_billcode').html());
    // $('#tm_amount-p').html(' ' + $('#tm_netamount_w_whtax').html());
    // console.log(status);
    if(status == '<label class="badge badge-danger">Unsettled</label>'){
      $('#tm_amount-p').html(' ' + $('#unsettled_amount').html());
      $('#tm_payment_status-p').html('<label class = "badge badge-info">Partially Settled</labled>');
    }else{
      $('#tm_amount-p').html(' ' + $('#tm_netamount').html());
      $('#tm_payment_status-p').html(' ' + $('#tm_paystatus').html());
    }
    $.LoadingOverlay('hide');
    $('#payment_modal').modal();

  });

  $(document).on('submit', '#form_save_payment', function (e) {
    e.preventDefault();
    $.LoadingOverlay('show');
    // checkPayment();
    let status = $('#tm_paystatus').html();
    var form = $(this);
    var form_data = new FormData(form[0]);
    if(status == '<label class="badge badge-danger">Unsettled</label>'){
      form_data.append('status','Unsettled');
    }
    // form_data.append([ajax_token_name],ajax_token);

    $.ajax({
      type: form[0].method,
      url: base_url + "accounts/Billing/settleBilling",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        $.LoadingOverlay('hide')
        var json_data = JSON.parse(data);
        update_token(json_data.csrf_hash);
        if (json_data.success) {
          resetPaymentForm();
          $('#payment_modal').modal('hide');
          //messageBox(json_data.message, 'Success', 'success');
          showCpToast("success", "Success!", json_data.message);
          setTimeout(function(){location.reload()}, 2000);

          get_billing_table(JSON.stringify(filter));
          $('.btn_back').trigger('click');
        } else {
          //messageBox(json_data.message, 'Warning', 'warning');
          showCpToast("warning", "Warning!", json_data.message);
          setTimeout(function(){location.reload()}, 2000);
        }

      },
      error: function (error) {
        //messageBox('Something went wrong. Please try again.', 'Error', 'error');
        showCpToast("error", "Error!", 'Something went wrong. Please try again.');
        setTimeout(function(){location.reload()}, 2000);
      }
    });

  });

  $(document).on('click', '.btn_view_logs', function () {
    let orderid = $(this).data('orderid');
    let refnum = $(this).data('refnum');
    let total_comrate = $(this).data('total_comrate');
    let totalamount = $(this).data('totalamount');
    let total_srp = $(this).data('total_srp');
    let processfee = $(this).data('processfee');
    let netamount = $(this).data('netamount');

    get_billing_logs(orderid, total_srp, processfee, netamount, total_comrate);
    $('#view_modal').modal();
  });

  $(document).on('click', '.btn_view_branch_logs', function () {
    let branchid = $(this).data('branchid');
    let trandate = $(this).data('trandate');
    let totalamount = $(this).data('totalamount');
    let processfee = $(this).data('processfee');
    let netamount = $(this).data('netamount');
    get_billing_branch_logs(branchid, trandate, totalamount, processfee, netamount);
    $('#view_modal2').modal();
  });

  $(document).on('click', '.btn_back', function () {
    $.LoadingOverlay('show');
    $('#sub_body').hide();
    $('#main_body').show();
    $.LoadingOverlay('hide');
  });

  $("#search_hideshow_btn").click(function (e) {
    e.preventDefault();

    var visibility = $('#card-header_search').is(':visible');

    if (!visibility) {
      //visible
      $("#search_hideshow_btn").html('&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
    } else {
      //not visible
      $("#search_hideshow_btn").html('Show Search <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
    }

    $("#card-header_search").slideToggle("slow");
  });

  $("#search_clear_btn").click(function (e) {
    filter.status = 1;
    filter.search = "";
    filter.shop = "";
    filter.from = "";
    filter.to = "";
    // $(".search-input-text").val("");
    $('#date_from').val('');
    $('#date_to').val('');
    $('#search_billcode').val('');
    $('#select_status option[value="1"]').prop('selected', true);
    $('#select_shop option[value=""]').prop('selected', true);
    get_billing_table(JSON.stringify(filter));
  });

  $(document).on('click', '#btn_view_attachment', function () {
    var title = $(this).data('title');
    var url = $(this).data('url');
    $('.attach-pics').removeClass('active_pic');
    $('.attach-pics:first').addClass('active_pic');
    url = url.replace(/ /g, '%20');
    // console.log(url);

    $('.view_image').css('background-image', `url(${s3bucket_url}${url})`);
    $('.modal-title').text(title);
    $('.view_image').css({
      "background-image": `url(${s3bucket_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat"
    });
    $('#view_image_modal').modal();
  });

  $(document).on('click', '.attach-pics', function(){
    let url = $(this).attr('data-url');
    console.log(url);
    url = url.replace(/ /g, '%20');
    $('.attach-pics').removeClass('active_pic');
    $('.view_image').css('background-image', `url(${s3bucket_url}${url})`);
    // $('.modal-title').text(title);
    $('.view_image').css({
      "background-image": `url(${s3bucket_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat"
    });
    $(this).addClass('active_pic');

  });

  $(document).on('change', '#select_shop', function(){
    let shop = $(this).val();
    $('#select_branch').html(`<option value="null">All Branch</option>`);
    $('#select_branch').prop('disabled',true);
    if(shop != ""){
      $.ajax({
        url: base_url+'accounts/Billing/get_shop_branches',
        type: 'post',
        data:{shop},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            if(data.branches.length > 0){
              $('#select_branch').prop('disabled',false);
              $('#select_branch').append(`<option value = "0">Main</option>`);
              $.each(data.branches, function(i,val){
                $('#select_branch').append(`<option value = "${val['branchid']}">${val['branchname']}</option>`);
              });
            }
          }else{
            $('#select_branch').prop('disabled',false);
            $('#select_branch').append(`<option value = "0">Main</option>`);
            // messageBox(data.message, 'Warning', 'warning');
          }
        },
        error: function(){
          //messageBox("Unable to connect to internet. Please try again", 'Error', 'error');
          showCpToast("error", "Error!", "Unable to connect to internet. Please try again");
          setTimeout(function(){location.reload()}, 2000);
          $.LoadingOverlay('hide');
        }
      });
    }
  });

  $(document).on('click', '#btn-print', function(){
      // let print = document.getElementById('print_form');
      let billing_id = $('#billing_id').val();
      window.open(base_url + 'accounts/Billing/print_breakdown/'+billing_id);
      // window.location.open
      // print.submit();
  });

  $(document).on('click', '.btn_delete', function(){
    let encrypted_id = $(this).data('encrypted_id');
    let shopid = $(this).data('shopid');
    let branchid = $(this).data('branchid');
    let billcode = $(this).data('billcode');
    let trandate = $(this).data('trandate');
    let payref = $(this).data('payref');
    let unsettled_payref = $(this).data('unsettled_payref');
    let name = $(this).data('name');

    $('.info_desc').html(name);
    $('#delete_id').val(encrypted_id);
    $('#delete_trandate').val(trandate);
    $('#delete_shopid').val(shopid);
    $('#delete_branchid').val(branchid);
    $('#delete_billcode').val(billcode);
    $('#delete_payref').val(payref);
    $('#delete_unsettled_payref').val(unsettled_payref);
    $('#delete_modal').modal();
  });

  $(document).on('submit', '#delete_form', function(e){
    e.preventDefault();
    let delete_form = new FormData(this);
    $.ajax({
      url: base_url+'billing/delete_billing',
      type: 'post',
      data:delete_form,
      processData: false,
      contentType: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          //messageBox(data.message, 'Success', 'success');
          showCpToast("success", "Success!", data.message);
          setTimeout(function(){location.reload()}, 2000);
          get_billing_table(JSON.stringify(filter));
          $('.info_desc').html('');
          $('#delete_id').val('');
          $('#delete_trandate').val('');
          $('#delete_shopid').val('');
          $('#delete_branchid').val('');
          $('#delete_billcode').val('');
          $('#delete_modal').modal('hide');
        }else{
          //messageBox(data.message, 'Warning', 'warning');
          showCpToast("warning", "Warning!", data.message);
          setTimeout(function(){location.reload()}, 2000);
        }
      },
      error: function(){
        //messageBox('Something went wrong. Please try again.', 'Error', 'error');
        showCpToast("error", "Error!", 'Something went wrong. Please try again.');
        setTimeout(function(){location.reload()}, 2000);
        $.LoadingOverlay('hide');
      }
    });
  });

});
