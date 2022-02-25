$(function () {
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  let default_date_to = $('#date_to').val();
  let default_date_from = $('#date_from').val();
  var filter = {
    search: '',
    shop: '',
    from: '',
    to: ''
  };

  function gen_vouchers_claimed(search) {
    var vouchers_claimed = $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order": [[6, 'desc']],
      columnDefs: [{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }],
      "ajax": {
        url: base_url + 'vouchers/Claimed_vouchers/get_vouchers_claimed_json',
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
						$('.btnExport').hide(100);
					}
					// console.log(JSON.stringify(decodeURIComponent(this.data)));
					$("input#_search").val(JSON.stringify(this.data));
					$("input#_filters").val(JSON.stringify(filter));

        },
        error: function () {
          $.LoadingOverlay("hide");
        }
      }
    });
  };

  gen_vouchers_claimed(JSON.stringify(filter));

  $(document).on('click', '#btnSearch', function () {
    filter.search = $('#searchtext').val();
    filter.shop = $('#select_shop').val() || '';
    filter.from = $('#date_from').val();
    filter.to = $('#date_to').val();

    // console.log(filter);
    // return ;
    gen_vouchers_claimed(JSON.stringify(filter));
  });

  $(document).on('click', '#refresh_trigger_btn', function () {
    filter.search = '';
    filter.shop = '';
    filter.from = '';
    filter.to = '';

    gen_vouchers_claimed(JSON.stringify(filter));
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
    $('#date_to').val(default_date_to);
    $('#date_from').val(default_date_from);
    $(".search-input-text").val("");
    $('#select_shop option[value=""]').prop('selected', true);
    gen_vouchers_claimed(JSON.stringify(filter));
  });

});
