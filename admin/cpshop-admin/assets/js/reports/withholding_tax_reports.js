$(function(){
  var base_url = $("body").data('base_url');
  var s3bucket_url = $("body").data('s3bucket_url');
  var token = $('#token').val();
  var filter = {
    search: '',
    shop: '',
    branch: '',
    from: '',
    to: ''
  };

  function gen_tax_tbl(search) {
    var tax_tbl = $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order": [[0, 'desc']],
      "columnDefs":[
        {targets: [3,4], orderable: false},
        { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 },
        // { responsivePriority: 1, targets: 12 },
      ],
      "ajax": {
        url: base_url + 'reports/Withholding_tax_reports/list_table/'+token,
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

  gen_tax_tbl(JSON.stringify(filter));

  $(document).on('click', '#btnSearch', function () {
    filter.search = $('#search_billcode').val();
    filter.shop = $('#select_shop').val() || '';
    filter.branch = $('#select_branches').val() || '';
    filter.from = $('#date_from').val();
    filter.to = $('#date_to').val();

    gen_tax_tbl(JSON.stringify(filter));
  });

  $(document).on('change', '#select_shop', function(){
    let shopid = $(this).val();
    if(shopid != ""){
      $.ajax({
        url: base_url+'reports/Withholding_tax_reports/get_branches',
        type: 'post',
        data:{shopid},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#select_branches').html(`<option value = "0">Main</option>`);
            $.each(data.branches, function(i,val){
              $('#select_branches').append(
                `<option value = "${val.id}">${val.branchname}</option>`
              );
            });
            $('#select_branches').prop('disabled',false);
          }else{
            $('#select_branches').html(`<option value = "">Branches</option>`);
            $('#select_branches').prop('disabled',true);
          }
        },
        error: function(){
          messageBox('Something went wrong. Please try again', 'Error', 'error');
          $.LoadingOverlay('hide');
        }
      });
    }else{
      $('#select_branches').html(`<option value = "">Branches</option>`);
      $('#select_branches').prop('disabled',true);
    }
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
    // $('#select_location option[value="all"]').prop('selected', true).trigger('change');
    // gen_prepayment(JSON.stringify(filter));
    gen_tax_tbl(JSON.stringify(filter));
  });

  //check mobile view for filter icon and chart icon
  if(window.matchMedia("(max-width: 767px)").matches){
    // The viewport is less than 768 pixels wide                  
      $("#search_hideshow_btn").html('<i class="fa fa-search"></i>  <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
      $("#chart_toggle").html('<i class="fa fa-area-chart"></i>  <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
  }

  //filter toggle
  $("#search_hideshow_btn").click(function(e){
        e.preventDefault();

        var visibility = $('#card-header_search').is(':visible');

        if(!visibility){
            //visible
            if(window.matchMedia("(max-width: 767px)").matches){
              // The viewport is less than 768 pixels wide
                $("#search_hideshow_btn").html('<i class="fa fa-search"></i> <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
            } else{
                // The viewport is at least 768 pixels wide
                $("#search_hideshow_btn").html('&ensp;Hide Filter <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
            }              
        }else{
            //not visible
            if(window.matchMedia("(max-width: 767px)").matches){
              // The viewport is less than 768 pixels wide                  
                $("#search_hideshow_btn").html('<i class="fa fa-search"></i> <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
            } else{
                // The viewport is at least 768 pixels wide                  
                $("#search_hideshow_btn").html('Show Filter <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
            }
            
        }

        $("#card-header_search").slideToggle("slow");
    });
    
});
