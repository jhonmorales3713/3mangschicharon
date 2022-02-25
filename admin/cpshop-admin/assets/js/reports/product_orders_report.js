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
    fromdate: '',
    todate: ''
  };

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

  var gen_product_orders_tbl = () => {
    return $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false,
      "destroy": true,
      "order":[[2, 'desc']],
      "ajax":{
        url: base_url+'reports/product_orders_report/list_table',
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
            data_toggle("show");
            $('#chart_toggle').show();
          }
          else{
            $('#btnExport').hide(100);
            data_toggle("hide");
            $('#chart_toggle').hide();
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

  var currentDate = new Date();  
  $("#date_from").datepicker("setDate",currentDate);
  $("#date_to").datepicker("setDate",currentDate);
  
  gen_product_orders_tbl();

  $(document).on('click', '#btnSearch', function(){
    filter.shopid = $('#select_shop').val() || '';
    filter.fromdate = $('#date_from').val();
    filter.todate = $('#date_to').val();
    filter.filtertype = $('#filtertype').val();
    gen_product_orders_tbl();
  });

  //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
      $('#date_to').val($(this).val());
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

  $("#search_clear_btn").click(function(e){
    filter.shopid = "all";
    filter.filtertype = "all";
    filter.fromdate = '';
    filter.todate = '';
		// $(".search-input-text").val("");
    $('#date_from').val('');
    $('#date_to').val('');
    $('#select_shop option[value="all"]').prop('selected', true);
    $('#filtertype option[value="all"]').prop('selected', true);
    gen_product_orders_tbl();
	});
});
