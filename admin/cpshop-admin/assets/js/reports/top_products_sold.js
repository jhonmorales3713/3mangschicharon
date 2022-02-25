$(function(){
      //hides data container
      data_toggle("hide");
      //false on first run
      var first_run = false;

    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    var filter = {
      fromdate: $('input#date_from').val(),
      todate: $('input#date_to').val(),
      pmethodtype: $('#pmethodtype').val(),
      shop_id: $('#select_shop').children("option:selected").val(),
      branch_id: $('#select_branch').children("option:selected").val()
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
  
    function chart(){
      $.ajax({
              type:'post',
              url: base_url+'reports/Top_products_sold/get_top_products_sold_data',
              data: filter,
              success:function(data){
                $.LoadingOverlay("hide"); 
                  if(data.success == true){                    
                      fill_data(data.chartdata);
                  }else{
                      fill_data(data.chartdata);
                  }
                  
              }
          });
    }

    function fillDataTable() {
      $.ajax({
        url: base_url + 'reports/Top_products_sold/get_top_products_sold_table',
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
              $('#table-grid').DataTable({
                "processing": true,
                "responsive": true,
                "searching": false,
                "destroy": true,
                "data": response.data,
                "order":[[0, 'asc']],
                columnDefs: [{ responsivePriority: 1, targets: 0 }]
              });
              //$('#st_qty').html(response.st_qty);
              $('#t_qty').html(response.t_qty);
              $('#t_sales_amount').html(response.t_sales_amount);                
              data_toggle("show");
              $('#chart_toggle').show();
            }
            else{
              data_toggle("hide");
              $('#chart_toggle').hide();
              $('#btnExport').hide(100);
              //$('#st_qty').html('');
              $('#t_qty').html('');
            } 
            first_run = true;
            $("input#_search").val(JSON.stringify(this.data));
            $("input#_filters").val(JSON.stringify(filter));

        },
        error: function () {  // error handling
          $.LoadingOverlay("hide");
        }
      })
    }
  
    fillDataTable();
  
    chart(); // initialize chart
  
    $(document).on('click', '#btnSearch', function(){
      filter.fromdate = $('#date_from').val();
      filter.todate = $('#date_to').val();
      filter.pmethodtype = $('#pmethodtype').val();
      filter.shop_id = $('#select_shop').children("option:selected").val();
      filter.branch_id = $('#select_branch').children("option:selected").val();
      chart();
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
  
    // Charts --------------------
  
    var transactions_chart = document.getElementById("transactions");
  
    if(transactions_chart){      
      var graph__transactions = new Chart(transactions_chart, {
        type: 'horizontalBar',
        data: {
            labels: [],
            datasets: [
              {
                label: 'Current',                            
                backgroundColor: '#07DA63',                                
                data: []
              },
              {
                label: 'Previous',                
                backgroundColor: 'darkgray',                                
                data: []
              }
          ]
        },        
        options: {
          title:{
            display: true,
            position: 'top',
            text: "Top Products Sold Over Time"
          },     
          legend: {
              position: 'bottom'
          },
          scales: {
            yAxes: [{
                ticks: {
                    //beginAtZero: true,
                    //stepSize: 4000,
                    // precision: 200
                    // stacked: true,
                    backdropPaddingY: 30
                }
            }],
            xAxes: [{
                ticks: {
                    beginAtZero: true,
                    autoSkip: true,
                    autoSkipPadding: 30,
                    maxRotation: 0,
                    // maxTicksLimit: 20
                    precision: 0
                }
            }]
          }               
        }
    });
  
    }    

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
  
    function fill_data(data){

      graph__transactions.data.labels = [];
      graph__transactions.data.datasets[0].data = [];
      graph__transactions.data.datasets[1].data = [];
      graph__transactions.update();
  
          if(transactions_chart)
          {
            var cur_data = data.cur_data;
            var pre_data = data.pre_data;
            var cur_period = data.cur_period;
            var pre_period = data.pre_period;                
  
            top10 = cur_data.map(function(obj) {
              if(obj['itemname'].length > 15) {
                return obj['itemname'].substr(0, 12) + '...';
              } else {
                return obj['itemname'];
              }
            });
            
            current = cur_data.map(function(obj) { return obj['qty']; });
            previous = pre_data.map(function(obj) { return obj['qty']; });                

              graph__transactions.data.labels = top10;
  
              graph__transactions.data.datasets[0].label = cur_period;
              graph__transactions.data.datasets[1].label = pre_period;
              
              graph__transactions.data.datasets[0].data = current;
              graph__transactions.data.datasets[1].data = previous;
              
              graph__transactions.update();
              
          }          
      }

    //changeable branches by shops
    var shopid = $('#shopid').val();
    var branchid = $('#branchid').val();

    if(branchid != 0 || shopid == 0){
      $('#select_branch').css('display','none');
    }      
    
    getShopBranches();

    $('#select_shop').on('change',function(){
      if($('#select_shop').children("option:selected").val() == "all"){
        $('#select_branch').css('display','none');
      }
      else{
        $('#select_branch').css('display','inline-block');
        getShopBranches();
      }
      
    });

    //get branches of selected shop
    function getShopBranches(){
      var $selected_shop = $('#select_shop').children("option:selected").val();  
      $.ajax({
        url: base_url + 'reports/Report_tools/getBranchOptions/'+$selected_shop,
        type: 'GET',
        dataType: 'JSON',
        success: function(data){
          if (data.total_opts > 0) {
            $('#select_branch_container').show();
            $('select#select_branch').html(data.options);   
          } else {
              $('#select_branch_container').hide();
          }
        },
        error: function(){
          $.LoadingOverlay("hide");
        }
      });      
    }
    
  });
  