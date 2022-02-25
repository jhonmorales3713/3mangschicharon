$(function(){
    //hides data container
    //data_toggle("hide");
    //false on first run
    var first_run = false;

    var branch_id = $('#branch_id').val();
    var shop_id = $('#shop_id').val();
  
      var base_url = $("body").data('base_url');
      var token = $('#token').val();
      var filter = {        
        shop_id: $('#shop_id').val(),
        branch_id: $('#branch_id').val()
      };
         
   
    
      function fillDataTable() {
        var to = $('#table-grid').DataTable({
          "processing": true,
          "serverSide": true,
          "responsive": true,
          "searching": false,
          "destroy": true,      
          "columnDefs": [
            { targets: 5, orderable: false, "sClass": "text-center" },
            { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
          ],
          "ajax": {
            url: base_url + 'reports/Order_status/pending_orders_branch_table',
            type: 'post',
            data: filter,
            beforeSend: function () {
              $.LoadingOverlay("show");          
            },
            complete: function (data) {
                $.LoadingOverlay("hide");          
                var response = $.parseJSON(data.responseText);
    
                if(response.data.length > 0){
                  $('.btnExport').show(100);                  
                  data_toggle("show");
                  $('#chart_toggle').show();
                  $('#t_pending').html(response.t_pending);
                  $('#t_onprocess').html(response.t_onprocess);
                  $('#t_pickup').html(response.t_pickup);
                  $('#t_confirmed').html(response.t_confirmed);                  
                }
                else{
                  data_toggle("hide");
                  $('#chart_toggle').hide();
                  $('#btnExport').hide(100);
                } 
                first_run = true;
                $("input#_search").val(JSON.stringify(this.data));
                $("input#_filters").val(JSON.stringify(filter));
    
            },
            error: function () {  // error handling
              $(".table-grid-error").html("");
              $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6"><center>No data found in the server</center></th></tr></tbody>');
              $("#table-grid_processing").css("display", "none");
            }
          }
        }).on('draw.dt', function () {
          var info = to.page.info();
          if(info.page + 1 === info.pages) {
            console.log('Last Page');
            //$('#last_page').show();
          } else {
            //$('#last_page').hide();
          }
        });    
      };
    
      fillDataTable();      
    
      $(document).on('click', '#btnSearch', function(){        
        filter.branch_id = $('#select_branch').children("option:selected").val();        
        fillDataTable();
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
  
        //chart toggle
        $("#chart_toggle").click(function(e){
          e.preventDefault();
  
          var visibility = $('#salesChart').is(':visible');
  
          if(!visibility){
              //visible
              if(window.matchMedia("(max-width: 767px)").matches){              
                  $("#chart_toggle").html('<i class="fa fa-area-chart"></i> <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
              } else{                
                  $("#chart_toggle").html('&ensp;Hide Chart <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
              }              
          }else{
              //not visible
              if(window.matchMedia("(max-width: 767px)").matches){              
                  $("#chart_toggle").html('<i class="fa fa-area-chart"></i> <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
              } else{                             
                  $("#chart_toggle").html('Show Chart <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
              }
              
          }
          $("#salesChart").slideToggle("slow");
      });
    
      $("#search_clear_btn").click(function(e){
        filter.fromdate = initial_datefrom;
        filter.todate = initial_dateto;                
        $('#date_from').val(initial_datefrom);
        $('#date_to').val(initial_dateto);
        chart();
        fillDataTable();
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
      
    });
    