$(function(){
      //hides data container
      data_toggle("hide");
      //false on first run
      var first_run = false;

    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    var filter = {
      shopid: $('#select_shop').val(),
      filtertype: $('#filtertype').val(),
      fromdate: $('#date_from').val(),
      todate: $('#date_to').val()
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
  
    var gen_product_orders_tbl = () => {
      return $('#table-grid').DataTable( {
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "searching": false,
        "destroy": true,
        "order":[[0, 'asc']],
        "ajax":{
          url: base_url+'reports/online_conversion_rate/oscr_data',
          type: 'post',
          data: filter,
          beforeSend: function(){
            $.LoadingOverlay("show");
          },
          complete: function(data){
            $.LoadingOverlay("hide");
            var response = $.parseJSON(data.responseText);
    
            if(response.data.length > 0){
              $('.btnExport').show(100);
              $("input#_search").val(JSON.stringify(this.data));
              $("input#_filter").val(JSON.stringify(filter));
              $("td#total_atc").text(`${response.tfoot[0]}`);
              $("td#total_rc").text(`${response.tfoot[1]}`);
              $("td#total_ptp").text(`${response.tfoot[2]}`);
              $("td#total_sessions").text(`${response.tfoot[3]}`);
              data_toggle("show");
            }
            else{
              data_toggle("hide");
              $('#btnExport').hide(100);
            }
            first_run = true;
            // console.log(JSON.stringify(decodeURIComponent(this.data)));
            $("input#_search").val(JSON.stringify(this.data));
            $("input#_filters").val(JSON.stringify(filter));
          },
          error: function(){
            $.LoadingOverlay("hide");
          }
        }
      });
    }

    var oscr_chart = () => {
      $.LoadingOverlay("show");
      $.ajax({
        type: "POST",
        url: base_url+'reports/online_conversion_rate/oscr_chart_data',
        data: filter,
        success: function (data) {
          $.LoadingOverlay("hide");
          // console.log(data);
          var response = $.parseJSON(data);
          if (response.success) {
            $('#growth').text(response.legend);
            $.each(response.data, function ($k, $v) {
              $.each($(`td.${$k}`), function ($el_k, $el_v) {
                $($el_v).html($v[$el_k]);
              })
            });
          }
        },
      });
    }
  
    var currentDate = new Date();  
    $("#date_from").datepicker("setDate",initial_datefrom);
    $("#date_to").datepicker("setDate",initial_dateto);
    
    gen_product_orders_tbl();

    oscr_chart();
  
    $(document).on('click', '#btnSearch', function(){
      filter.shopid = $('#select_shop').val() || '';
      filter.fromdate = $('#date_from').val();
      filter.todate = $('#date_to').val();
      filter.filtertype = $('#filtertype').val();
      gen_product_orders_tbl();
      oscr_chart();
    });

    function data_toggle(action){
      if(first_run){
        $("#message-container div i").next().text(" No search results found");
      }
      else{
        $("#message-container div i").next().text(" To show records, kindly select your preferred date range. You may use other filter(s) if there's any.");
      }
      if(action == "hide"){
        $('#data-container').fadeOut();
        $('#message-container').show();
      }
      else if(action =="show"){
        $('#data-container').fadeIn(500);
        $('#message-container').hide();
      }
    }

    //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
      $('#date_to').val($(this).val());
    });
  
    //check mobile view for filter icon and chart icon
    if(window.matchMedia("(max-width: 767px)").matches){
      // The viewport is less than 768 pixels wide                  
        $("#search_hideshow_btn").html('<i class="fa fa-search"></i>  <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');        
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


    $('#date_to').datepicker().on('changeDate', (e) => {
      var todaydate = $('#todaydate').val();
      var new_start_date = moment(e.date).subtract(93, 'day').format('MM/DD/YYYY');
    
      $('#date_from').datepicker('setStartDate', new_start_date);
      $('#date_to').datepicker('setEndDate', todaydate);
    });
    
    $("#date_from").click(function (e) {
      var date_to = $('#date_to').val();
      var new_start_date = moment(date_to).subtract(93, 'day').format('MM/DD/YYYY');
      $('#date_from').datepicker('setStartDate', new_start_date);
    });
      

    $("#search_clear_btn").click(function(e){
      filter.shopid = "all";
      filter.filtertype = "all";
      filter.fromdate = initial_datefrom;
      filter.todate = initial_dateto;
          // $(".search-input-text").val("");
      $('#date_from').val(initial_datefrom);
      $('#date_to').val(initial_dateto);
      $('#select_shop option[value="all"]').prop('selected', true);
      $('#filtertype option[value="all"]').prop('selected', true);
      gen_product_orders_tbl();
      });
  });
  
  