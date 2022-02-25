$(function(){
    //hides data container
    
    //false on first run
    var first_run = false;
  
    var shopid = $('#shopid').val();
    var branchid = $('#branchid').val();    
    
      var base_url = $("body").data('base_url');
      var token = $('#token').val();
      var filter = {        
        shop_id: $('#shop_id').val(),
        branch_id: $('#branch_id').val(),
        time_in_seconds: $('#time_in_seconds').val()
      };
    
      function fillDataTable() {      
        var aov = $('#table-grid').DataTable({
          "processing": true,
          "serverSide": true,
          "responsive": true,
          "searching": false,
          "destroy": true,      
          columnDefs: [                      
            { responsivePriority: 1, targets: 0},
          ],
          "ajax": {
            url: base_url + 'reports/Branch_performance/branch_performance_breakdown_table',
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
                  $('#t_amount').html('<b>Total: '+response.total_amount+'</b>');
                  $('#t_average').html('<b>'+response.average+'</b>');
                }
                else{                  
                  $('#btnExport').hide(100);                                       
                  $('#t_amount').html('');
                }
                first_run = true; 
                $("input#_search").val(JSON.stringify(this.data));
                $("input#_filters").val(JSON.stringify(filter));  
            },
            error: function () {  // error handling
                $(".table-grid-error").html("");            
                $("#table-grid_processing").css("display", "none");
            }
          }
        }).on('draw.dt', function () {
          var info = aov.page.info();        
          if(info.page + 1 === info.pages) {
            console.log('Last Page');
            //$('#last_page').show();
          } else {
            //$('#last_page').hide();
          }
        });
      };
  
      //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
      //it will be removed soon once layout problem is fixed
      $('#date_to_m').on('change',function(){
        $('#date_to').val($(this).val());
      });
    
      fillDataTable();     
  
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
  