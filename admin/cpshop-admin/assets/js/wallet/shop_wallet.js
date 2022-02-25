$(function(){
  var base_url = $("body").data('base_url');
  var s3bucket_url = $("body").data('s3bucket_url');
  var id = $('#sid').val();
  var branchid = $('#bid').val();
  var filter = {
    search: '',
    shop: '',
    from: '',
    to: ''
  };

  filter.shop = $('#shopid').val();
  $('#searchValue').val(JSON.stringify(filter));

  function gen_wallet_logs(search,id,branchid = 0){
    var wallet_logs = $('#logs_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order":[[4, 'desc']],
      "ajax":{
        url: base_url+'wallet/Prepayment/get_shop_wallet_logs_table',
        type: 'post',
        data: {
          searchValue: search,
          vid: id,
          branchid
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          $("#_search_logs").val(JSON.stringify(this.data));
        },
        error: function(){
          $.LoadingOverlay('hide');
        }
      }
    });
  };

  gen_wallet_logs(JSON.stringify(filter),id,branchid);

  $(document).on('click', '#btnSearch', function(){
    filter.from = $('#date_from').val();
    filter.to = $('#date_to').val();
    gen_wallet_logs(JSON.stringify(filter),id);
  });

  $(document).on('click', '.time_img', function () {
    var title = $(this).data('title');
    var url = $(this).data('url');
    url = url.replace(/ /g, '%20');
    console.log(url);

    $('.view_image').css('background-image', `url(${s3bucket_url}${url})`);
    $('.modal-title').text(title);
    $('.view_image').css({
      "background-image": `url(${s3bucket_url}${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat"
    });
    $('#view_image_modal').modal();
  });

  $(document).on('click', '#btnSearch_logs', function(){
    let logsdate_from = $('#logsdate_from').val();
    let plogs_search = $('#plogs_search').val();
    let logsdate_to = $('#logsdate_to').val();
    let shopid = $('#reset_logs').data('shopid');
    let ref_num = $('#reset_logs').data('ref_num');
    filter.search = plogs_search;
    filter.shop = id;
    filter.from = logsdate_from;
    filter.to = logsdate_to;
    gen_wallet_logs(JSON.stringify(filter), id);
  });

  $(document).on('click', '#reset_logs', function(){
    filter.search = '';
    filter.shop = '';
    filter.from = '';
    filter.to = '';
    gen_wallet_logs(JSON.stringify(filter), $(this).data('shopid'),$(this).data('branchid'));
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
    filter.from = "";
    filter.to = "";
    $('#date_from').val('');
    $('#date_to').val('');
		gen_wallet_logs(JSON.stringify(filter),id);
	});
});
