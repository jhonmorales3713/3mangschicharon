$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var filter = {
    search: '',
    shop: '',
    from: '',
    to: ''
  };

  function gen_sale_settlement_tbl(search){
    var manual_order_tbl = $('#table-grid').DataTable( {
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order":[[2, 'desc']],
      "ajax":{
        url: base_url+'reports/sale_settlement/list_table',
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

  gen_sale_settlement_tbl(JSON.stringify(filter));

  $(document).on('click', '#btnSearch', function(){
    filter.search = '';
    filter.shop = $('#select_shop').val() || '';
    filter.from = $('#date_from').val();
    filter.to = $('#date_to').val();

    // console.log(filter);
    // return ;
    gen_sale_settlement_tbl(JSON.stringify(filter));
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
    filter.search = "";
    filter.shop = "";
    filter.from = "";
    filter.to = "";
		// $(".search-input-text").val("");
    $('#date_from').val('');
    $('#date_to').val('');
    $('#select_shop option[value=""]').prop('selected', true);
		gen_sale_settlement_tbl(JSON.stringify(filter));
	});
});
