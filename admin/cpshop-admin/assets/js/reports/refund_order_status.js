$(function(){
    //hides data container
    data_toggle("hide");
    //false on first run
    var first_run = false;
    
    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    var filter = {
      shopid: $('#select_shop').val(),
      fromdate: $('#date_from').val(),
      todate: $('#date_to').val()
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
  
    function table(){
        var table = $('#table-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "searching": false,
            "destroy": true,
            "order":[[0, 'asc']],
            "columnDefs":[
                // {"targets":[1,4,5,6],"visible":(filter.filtertype == 'summary') ? false:true},
                // {"targets":[3],"visible":(filter.shopid > 0) ? true:false},
                // {"targets":[6,7],"className": "text-right"},
            ],
            "ajax":{
            url: base_url+'reports/Refund_Order/get_RefundStatus_table',
            type: 'post',
            data: filter,
            beforeSend: function(){
                $.LoadingOverlay("show");
            },
            complete: function(data){
                $.LoadingOverlay("hide");
                var response = data.responseJSON.success;
                // var table_data = data.responseJSON.data;
                // console.log(table_data);
                // if (filter.filtertype == 'summary') {
                //     this.columnDefs = [{"targets":[3],"visible":false}];
                // }
                if(response == 1){
                    $('.btnExport').show(100);
                    $("#_search").val(JSON.stringify(this.data));
                    $("input#_filter").val(JSON.stringify(filter));
                    $('#table-grid-top').show();
                    data_toggle("show");
                    $('#chart_toggle').show();
                }else{
                    data_toggle("hide");
                    $('#chart_toggle').hide();
                    $('#btnExport').hide(100);
                    $('#table-grid-top').hide();
                }
                $("#table-grid tfoot #total_amount").text(`${data.responseJSON.total_amount}`);
                $("#table-grid tfoot #sales_count_total").text(`${data.responseJSON.sales_count_total}`);
                first_run = true;
            },
            error: function(){
                $.LoadingOverlay("hide");
            }
            }
        } );
    }
  
    // gen_sales_report_tbl(JSON.stringify(filter));
    table();
    
    $(document).on('click', '#btnSearch', function(){
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.shopid = $('#select_shop').val();
        table();
    });

    //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
        $('#date_to').val($(this).val());
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
  
    $("#search_clear_btn").click(function(e){
        filter.fromdate = initial_datefrom;
        filter.todate = initial_dateto;
        filter.shopid = "all";
            // $(".search-input-text").val("");
        $('#date_from').val(initial_datefrom);
        $('#date_to').val(initial_dateto);
        $('#select_shop').val();
        table();
      });
  });
  