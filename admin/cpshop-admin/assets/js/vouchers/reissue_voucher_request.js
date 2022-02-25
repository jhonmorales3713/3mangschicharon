$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var filter = {
    search: '',
    shop: '',
    from: '',
    to: ''
  };

  function gen_reissue_voucher_request(search){
    var reissue_voucher_request = $('#table-grid').DataTable( {
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order":[[6, 'desc']],
      "ajax":{
        url: base_url+'vouchers/Reissue_voucher_request/get_reissue_voucher_request_json',
        type: 'post',
        data: {
          searchValue: search
        },
        beforeSend: function(){
          $.LoadingOverlay("show");
        },
        complete: function(data){
          $.LoadingOverlay("hide");
        },
        error: function(){
          $.LoadingOverlay("hide");
        }
      }
    });
  };

  gen_reissue_voucher_request();

  $(document).on('click', '.btn_reissue', function(){
    let uid = $(this).data('uid');
    let email = $(this).data('email');
    $.ajax({
      url: base_url+'vouchers/Reissue_voucher_request/request_reissue',
      type:'post',
      data:{uid,email},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          //messageBox(data.message,'Success','success');
          showCpToast("success", "Success!", data.message);
          gen_reissue_voucher_request($('#searchbox').val());
        }else{
          //messageBox(data.message,'Warning','warning');
          showCpToast("warning", "Warning!", data.message);
        }
      },
      error: function(){
        //messageBox('Oops! Something went wrong. Please try again.', 'Warning', 'warning');
        showCpToast("warning", "Warning!", 'Oops! Something went wrong. Please try again.');
        $.LoadingOverlay('hide');
      }
    });
  });

  $(document).on('click', '#btnSearch', function(){
    // filter.search = $('#searchtext').val();
    // filter.shop = $('#select_shop').val() || '';
    // filter.from = $('#date_from').val();
    // filter.to = $('#date_to').val();

    // console.log(filter);
    // return ;
    let search = $('#searchbox').val();
    console.log(search);
    gen_reissue_voucher_request(search);
  });

  $(document).on('click', '#refresh_trigger_btn', function(){
    filter.search = '';
    filter.shop = '';
    filter.from = '';
    filter.to = '';

    gen_vouchers_claimed(JSON.stringify(filter));
  });

  $("#search_hideshow_btn").click(function(e){
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if(!visibility){
			//visible
			$("#search_hideshow_btn").html('&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		}else{
			//not visible
			$("#search_hideshow_btn").html('Show Search <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
   		}

		$("#card-header_search").slideToggle("slow");
	});

  $("#search_clear_btn").click(function(e){
    // filter.search = "";
    // filter.shop = "";
    // filter.from = "";
    // filter.to = "";
    // $('#date_to').val('');
    // $('#date_from').val('');
		// $(".search-input-text").val("");
    // $('#select_shop option[value=""]').prop('selected', true);
    $('#searchbox').val('');
    gen_reissue_voucher_request();
		// gen_vouchers_claimed(JSON.stringify(filter));
	});

});
