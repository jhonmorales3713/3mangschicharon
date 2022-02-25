
function filter_error(){
  var error = 0;
  var errorMsg = "";
  let attachment = document.getElementById('attachment');

  $('.rq').each(function(){
    let id = $(this).get(0).id;
    if($(this).val() == ""){
      $(this).css("border", "1px solid #ef4131");
      $(this).siblings('.select2-container').css('border', '1px solid #ef4131');
    }else{
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

  if(attachment.value != ""){
    let upload_size = attachment.files[0].size / 1024;
    // if(upload_size == 0){
    //   error = 1;
    //   errorMsg = 'Payment attachment is to large. Try to upload with file size lower than 2mb.';
    //   attachment.style.borderColor = "#ef4131";
    // }

    if(upload_size > 2048){
      error = 1;
      errorMsg = 'Payment attachment is to large. Try to upload with file size lower than 2mb.';
      attachment.style.border = "1px solid #ef4131";
    }
  }

  return result = {error: error, errorMsg: errorMsg};
}

function reset_form(){
  $('#shop option[value=""]').prop('selected',true);
  $('#type option[value=""]').prop('selected',true);
  $('#attachment').val("");
  $('#deposit_ref_no').val("");
  $('#amount').val("");
  $('#amount').data("raw","");
  $('#remarks').val("");
  $('#c_password').val('');
  $('.step_1').tab('show');
  $('.fa-check').hide();
  $('#btn-back-step').hide();
  $('#btn-finish-step').hide();
  $('#btn-next-step').show();
}

$(function(){
  var base_url = $("body").data('base_url');
  var token = $("body").data('token');
  var s3bucket_url = $("body").data('s3bucket_url');
  var filter = {
    search: '',
    shop: '',
    branch: '',
    from: '',
    to: ''
  };
  var filter2 = {
    search: '',
    shop: '',
    from: '',
    to: ''
  };

  $('#searchValue').val(JSON.stringify(filter2));

  function gen_prepayment(search) {
    var prepayment = $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order": [[3, 'desc']],
      "columnDefs": [
        { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
      ],
      "ajax": {
        url: base_url + 'wallet/Prepayment/get_prepayment_table',
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

  function gen_wallet_logs(search, id, refnum, branchid) {
    var wallet_logs = $('#logs_tbl').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order": [[4, 'desc']],
      "ajax": {
        url: base_url + 'wallet/Prepayment/get_shop_wallet_logs_table',
        type: 'post',
        data: {
          searchValue: search,
          vid: id,
          'refnum': refnum,
          branchid
        },
        beforeSend: function () {
          $.LoadingOverlay("show");
        },
        complete: function (data) {
          $.LoadingOverlay("hide");
          $("#_search_logs").val(JSON.stringify(this.data));
        },
        error: function () {
          $.LoadingOverlay("hide");
        }
      }
    });
  };

  gen_prepayment(JSON.stringify(filter));

  $(document).on('click', '#btnSearch', function () {
    filter.search = '';
    filter.shop = $('#select_shop').val() || '';
    filter.branch = $('#select_branch').val() || '';
    filter.from = $('#date_from').val();
    filter.to = $('#date_to').val();

    // console.log(filter);
    // return ;
    gen_prepayment(JSON.stringify(filter));
  });

  $(document).on('click', '#refresh_trigger_btn', function () {
    filter.search = '';
    filter.shop = '';
    filter.from = '';
    filter.to = '';

    gen_prepayment(JSON.stringify(filter));
  });

  $(document).on('click', '#btn_deposit', function () {
    $('#add_modal_new').modal();
    $('.rq').css('border', '1px solid gainsboro');
    $('#attachment').css('border', '1px solid gainsboro');
  });

  $(document).on('submit', '#deposit_form', function (e) {
    e.preventDefault();
    let form_data = new FormData(this);
    form_data.append('deposit_amount', $('#amount').attr('data-raw'));
    form_data.append('shopname', $('#shop option:selected').text());
    form_data.append('branchname', $('#branch option:selected').text());
    let attachment = document.getElementById('attachment');
    if(attachment.value != ""){
      let upload_size = attachment.files[0].size / 1024;
      if(upload_size > 2048){
        //messageBox('Payment attachment is to large. Try to upload with file size lower than 2mb.','Warning','warning');
        showCpToast("warning", "Warning!", 'Payment attachment is to large. Try to upload with file size lower than 2mb.');
        return ;
      }
    }

    var error_filter = filter_error();
    if(error_filter.error == 0){
      $.ajax({
        url: base_url+'wallet/Prepayment/deposit',
        type: 'post',
        data: form_data,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#add_modal').modal('hide');
          if(data.success == true){
            //messageBox(data.message,'Success','success');
            showCpToast("success", "Success!", data.message);
            gen_prepayment(JSON.stringify(filter));
            reset_form();
          }else{
            //messageBox(data.message,'Warning','warning');
            showCpToast("warning", "Warning!", data.message);
          }
        },
        error: function(){
          $.LoadingOverlay('hide');
          //messageBox(data.message,'','');
          showCpToast("warning", "Warning!", data.message);
        }
      });
    }else{
      //messageBox(error_filter.errorMsg, "Warning", 'warning');
      showCpToast("warning", "Warning!", error_filter.errorMsg);
    }


  });

  $(document).on('click', '.btn_view_logs', function () {
    let shopid = $(this).data('shopid');
    let branchid = $(this).data('branchid');
    let refnum = $(this).data('ref_num');

    gen_wallet_logs(JSON.stringify(filter2), shopid, refnum, branchid);
    $('#logs-title').text($(this).data('shopname'));
    $('#wallet_ballance').text('₱ ' + $(this).data('balance'));
    $('#wallet_sales').text('₱ ' + $(this).data('total_sales'));
    $('#export_logs_shopid').val(shopid);
    $('#export_logs_branchid').val(branchid);
    // $('#wallet_view_modal').modal();
    $('#reset_logs').data('shopid', shopid);
    $('#reset_logs').data('branchid', branchid);
    $('#reset_logs').data('ref_num', refnum);
    $('#main_body').hide();
    $('#sub_body').show();
  });

  $(document).on('click', '.time_img', function () {
    var title = $(this).data('title');
    var url = $(this).data('url');
    url = url.replace(/ /g, '%20');
    console.log(base_url+url);
    $('#btn-download-image').attr('href',s3bucket_url+url);
    $('.view_image').css('background-image', `url(${s3bucket_url}${url})`);
    $('.modal-title').text(title);
    $('.view_image').css({
      "background-image": `url(${s3bucket_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat",
      "background-position": "center"
    });
    $('#view_image_modal').modal();
  });

  $(document).on('click', '#btn-next-step', function(){
    let step = parseInt($('.nav-link.active').attr('data-step'));
    let previous_check = $(`.step_${step}`).children('.fa-check');
    let shopname = $('#shop option:selected').text();
    let branchname = $('#branch option:selected').text();
    let deposit_type = $('#type option:selected').text();
    let deposit_refno = $('#deposit_ref_no').val();
    let amount = $('#amount').val();
    let remarks = $('#remarks').val();

    // check filter
    if(step == 1){
      error_filter = filter_error();
      if(error_filter.error == 1){
        //messageBox(error_filter.errorMsg,'Warning', 'warning');
        showCpToast("warning", "Warning!", error_filter.errorMsg);
        return;
      }
    }

    step += 1;

    if(step > 1 && step < 3){
      $('#btn-back-step').show();
    }

    if(step == 3){
      $('#btn-next-step').hide();
      $('#btn-finish-step').show();
    }

    if(step == 2){
      $('#c_shopname').val(shopname);
      $('#c_branchname').val(branchname);
      $('#c_deposit_type').val(deposit_type);
      $('#c_deposit_refno').val(deposit_refno);
      $('#c_amount').val(amount);
      $('#c_remarks').val(remarks);
    }
    $(`.step_${step}`).tab('show');
    previous_check.show();
  });

  $(document).on('click', '#btn-back-step', function(){
    let step = parseInt($('.nav-link.active').attr('data-step'));
    current_check = $(`.step_${step}`).children('.fa-check');
    step -= 1;

    if(step == 1){
      $('#btn-back-step').hide();
    }

    if(step > 1 && step < 3){
      $('#btn-finish-step').hide();
      $('#btn-next-step').show();
    }

    console.log(step);
    $(`.step_${step}`).tab('show');
    current_check.hide();
  });

  $(document).on('click', '#btn-finish-step', function(){
    $.ajax({
      url: base_url+'wallet/Prepayment/authentication_password',
      type: 'post',
      data:{c_password: $('#c_password').val()},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          //messageBox(data.message,'Success','success');
          showCpToast("success", "Success!", data.message);
          $('#add_modal_new').modal('hide');
          setTimeout(() => {$('#deposit_form').submit();},1000);

        }else{
          //messageBox(data.message,'Warning','warning');
          showCpToast("warning", "Warning!", data.message);
        }
      },
      error: function(){
        // notificationError('Error', 'Oops! Something went wrong. Please try again.');
        //messageBox('Oops! Something went wrong. Please try again.','Error','error');
        showCpToast("error", "Error!", 'Something went wrong. Please try again.');
        $.LoadingOverlay('hide');
      }
    });
  });

  $(document).on('keydown', '#c_password', function(e){
    let keyCode = (event.keyCode ? event.keyCode : event.which);
    if(keyCode == 13){
      $('#btn-finish-step').click();
    }
  });

  $(document).on('click', '#reset_logs', function(){
    filter2.search = '';
    filter2.shop = '';
    filter2.from = '';
    filter2.to = '';
    gen_wallet_logs(JSON.stringify(filter2), $(this).data('shopid'), $(this).data('ref_num'), $(this).data('branchid'));
    $.ajax({
      url: base_url+'prepayment/get_shop_wallet_and_sales',
      type: 'post',
      data:{
        shopid: $(this).data('shopid'),
        branchid: $(this).data('branchid')
      },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          let balance = data.balance;
          let total_sales = data.total_sales;

          $('#wallet_ballance').html(`₱ ${balance}`);
          $('#wallet_sales').html(`₱ ${total_sales}`);
        }else{
          //messageBox(data.message,'Warning','warning');
          showCpToast("warning", "Warning!", data.message);
        }
      },
      error: function(){
        //messageBox('Something went wrong. Please try again','Error','error');
        showCpToast("error", "Error!", 'Something went wrong. Please try again.');
        $.LoadingOverlay('hide');
      }
    });
    // location.reload()
  });

  $(document).on('click', '.btn_back_main', function(){
    $.LoadingOverlay('show');
    $('#sub_body').hide();
    $.LoadingOverlay('hide');
    $('#main_body').show();
  });

  $(document).on('click', '#btnSearch_logs', function(){
    let logsdate_from = $('#logsdate_from').val();
    let logsdate_to = $('#logsdate_to').val();
    let plogs_search = $('#plogs_search').val();
    let shopid = $('#reset_logs').data('shopid');
    let ref_num = $('#reset_logs').data('ref_num');
    filter2.shop = shopid,
    filter2.from = logsdate_from;
    filter2.to = logsdate_to;
    filter2.search = plogs_search;
    $('#searchValue').val(JSON.stringify(filter2));
    gen_wallet_logs(JSON.stringify(filter2), shopid, ref_num);
  });

  $(document).on('change', '#shop', function(){
    let shopid = $(this).val();
    if(shopid != ""){
      $.ajax({
        url: base_url+'prepayment/get_branches',
        type: 'post',
        data:{shopid},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            console.log(data.branches);
            $('#branch').html(`<option value = "0">Main</option>`);
            $.each(data.branches, function(i,val){
              $('#branch').append(
                `<option value = "${val.branchid}">${val.branchname}</option>`
              );
            });
            $('#branch').prop('disabled',false);
          }else{
            $('#branch').html(`<option value = "0">Main</option>`);
            $('#branch').prop('disabled',true);
          }
        },
        error: function(){
          $('#branch').prop('disabled',true);
          //messageBox("Oops something went wrong please try again", "Error", 'error');
          showCpToast("error", "Error!", 'Something went wrong. Please try again.');
          $.LoadingOverlay('hide');
        }
      });
    }
  });

  $(document).on('change', '#select_shop', function(){
    let shopid = $(this).val();
    if(shopid != ''){
      $.ajax({
        url: base_url+'prepayment/get_branches',
        type: 'post',
        data:{shopid},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#select_branch').html('<option value = "">All Branch</option>');
            $('#select_branch').append('<option value = "0">Main</option>');
            $.each(data.branches, function(i,val){
              $('#select_branch').append(
                `<option value = "${val.branchid}">${val.branchname}</option>`
              );
            });
            $('#select_branch').prop('disabled',false);
          }else{
            $('#select_branch').html('<option value = "0">Main</option>');
            $('#select_branch').prop('disabled',true);
          }
        },
        error: function(){
          $('#select_branch').prop('disabled',true);
          //messageBox("Oops something went wrong please try again", "Error", 'error');
          showCpToast("error", "Error!", 'Something went wrong. Please try again.');
          $.LoadingOverlay('hide');
        }
      });
    }else{
      $('#select_branch').html(`<option value = "">All Branch</option>`);
      $('#select_branch').prop('disabled',true);
    }
  });

  // $(document).on('submit', '#export_wallet_logs', function(e){
  //   e.preventDefault();
  //   let form = new FormData(this);
  //   form.append('searchValue',JSON.stringify(filter2));
  //   $.ajax({
  //     url: base_url+'prepayment/export_logs',
  //     type: 'post',
  //     processData: false,
  //     contentType: false,
  //     data: form,
  //     beforeSend: function(){
  //       $.LoadingOverlay('show');
  //     },
  //     success: function(data){
  //       $.LoadingOverlay('hide');
  //       window.open('data:application/vnd.ms-excel,' + data);
  //     },
  //     error: function(){
  //       messageBox('Oops! Something went wrong. Please try again','Error','error');
  //       $.LoadingOverlay('hide');
  //     }
  //   });
  // });

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
    filter.branch = "";
    filter.from = "";
    filter.to = "";
    // $(".search-input-text").val("");
    $('#date_from').val('');
    $('#date_to').val('');
    $('#select_shop option[value=""]').prop('selected', true);
    $('#select_branch').html('<option value = "">All Branch</option>');
    $('#select_branch').prop('disabled',true);
    gen_prepayment(JSON.stringify(filter));
  });
});
