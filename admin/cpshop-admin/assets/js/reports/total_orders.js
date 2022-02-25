$(function(){
  //hides data container
  data_toggle("hide");
  //false on first run
  var first_run = false;

  var shopid = $('#shopid').val();
  var branchid = $('#branchid').val();

    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    var filter = {
      fromdate: $('input#date_from').val(),
      todate: $('input#date_to').val(),
      shop_id: $('#select_shop').children("option:selected").val(),
      branch_id: $('#select_branch').children("option:selected").val(),
      type: $('#select_type').children("option:selected").val(),
      pmethodtype: $('#pmethodtype').val(),
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
  
    function chart(){
      // $.ajax({
      //         type:'post',
      //         url: base_url+'reports/Total_orders/get_total_orders_data',
      //         data: filter,
      //         success:function(data){
      //           $.LoadingOverlay("hide"); 
      //             if(data.success == true){                    
      //                 fill_data(data.chartdata);
      //             }else{
      //                 fill_data(data.chartdata);
      //             }
                  
      //         }
      //     });
    }
  
    function fillDataTable() {
      var to = $('#table-grid').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "searching": false,
        "destroy": true,      
        columnDefs: [
          { responsivePriority: 1},
          {"targets":[1],"visible":(filter.type == 'logs') && (shopid == 0) ? true:false},
          {"targets":[2],"visible":(filter.type == 'logs') ? true:false},
        ],
        "ajax": {
          url: base_url + 'reports/Total_orders/get_total_orders_table',
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
                $('#t_p_orders').html('<b>'+response.t_p_orders+'</b>');
                $('#t_f_orders').html('<b>'+response.t_f_orders+'</b>');
                $('#t_d_orders').html('<b>'+response.t_d_orders+'</b>');
                if(filter.type == "summary"){
                  $("#table-grid tfoot th:nth-child(1)").text('Total');
                  //$("#table-grid tfoot th:nth-child(3)").text('');
                }
                else{
                  $("#table-grid tfoot th:nth-child(1)").text('');
                  if(shopid == 0){
                    $("#table-grid tfoot th:nth-child(3)").text('Total');
                  }
                  else{
                    $("#table-grid tfoot th:nth-child(2)").text('Total');
                  }
                  
                }
                data_toggle("show");
                $('#chart_toggle').show();
              }
              else{
                data_toggle("hide");
                $('#chart_toggle').hide();
                $('#btnExport').hide(100);
                $('#t_p_orders').html('');
                $('#t_f_orders').html('');
                $('#t_d_orders').html('');
              } 
              first_run = true;
              $("input#_search").val(JSON.stringify(this.data));
              $("input#_filters").val(JSON.stringify(filter));
  
          },
          error: function () {  // error handling
            $(".table-grid-error").html("");
            $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="4">No data found in the server</th></tr></tbody>');
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
  
    chart(); // initialize chart
  
    $(document).on('click', '#btnSearch', function(){
      filter.fromdate = $('#date_from').val();
      filter.todate = $('#date_to').val();
      filter.pmethodtype = $('#pmethodtype').val();
      filter.shop_id = $('#select_shop').children("option:selected").val();
      filter.branch_id = $('#select_branch').children("option:selected").val();
      filter.type = $('#select_type').children("option:selected").val();
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
        type: 'line',
        data: {
            labels: [],
            datasets: [
              {
                label: 'Total Orders',
                borderColor: '#07DA63',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 3, 
                data: []
              },
              {
                label: 'Previous Total Order',
                borderColor: 'darkgray',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 3,
                data: []
              }
          ]
        },
        options: {
          elements: {
            line: {
                tension: 0.01                
            },
            point: {
                radius: 0
            }
          },
          tooltips: {
              mode: 'point'
          },    
          title:{
            display: true,
            position: 'top',
            text: "Total Orders Over Time"
          },     
          legend: {
              position: 'bottom'
          },
          scales: {
            yAxes: [{
                ticks: {
                    // beginAtZero: true,
                    //stepSize: 4000,
                    precision: 0,
                    // stacked: true,
                    backdropPaddingY: 30
                }
            }],
            xAxes: [{
                ticks: {
                    autoSkip: true,
                    autoSkipPadding: 30,
                    maxRotation: 0,
                    // maxTicksLimit: 20
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
  
          if(transactions_chart)
          {
            var cur_data = data.current_data;
            var pre_data = data.previous_data;
            var cur_period = data.cur_period;
            var pre_period = data.pre_period;                
  
            data0 = cur_data.map(function(obj) { return obj['total_paid_orders']; });
            data1 = pre_data.map(function(obj) { return obj['total_paid_orders']; });                
            
              data.dates[0] = "";
              graph__transactions.data.labels = data.dates;
  
              graph__transactions.data.datasets[0].label = cur_period;
              graph__transactions.data.datasets[1].label = pre_period;
              
              graph__transactions.data.datasets[0].data = data0;
              graph__transactions.data.datasets[1].data = data1;
              
              graph__transactions.update();

              $('#to_prev_head').html("Previous <br>" + `<span class="text-xs">${data.pre_period}</span>`);
              $('#to_cur_head').html("Current <br>" + `<span class="text-xs">${data.cur_period}</span>`);  

              $('#p_to_total_orders').text(data.pre_total);
              $('#to_total_orders').text(data.cur_total);
              
              if(data.pre_total != data.cur_total){
                $('#to_percent').html(`<i class="fa fa-arrow-${(data.percentage.increased) ? 'up text-blue-400':'down text-red-400'}"></i> ${data.percentage.percentage} %`);                
              }
              else{
                $('#to_percent').html(`${data.percentage.percentage} %`);                
              }              
              
              $('#p_to_f_total').text(data.pre_total_f);
              $('#to_f_total').text(data.cur_total_f);

              if(data.pre_total_f != data.cur_total_f){
                $('#to_f_percent').html(`<i class="fa fa-arrow-${(data.percentage_f.increased_f) ? 'up text-blue-400':'down text-red-400'}"></i> ${data.percentage_f.percentage_f} %`);              
              }
              else{
                $('#to_f_percent').text(`${data.percentage_f.percentage_f} %`);              
              }              
             
              $('#p_to_d_total').text(data.pre_total_d);
              $('#to_d_total').text(data.cur_total_d);

              if(data.pre_total_d != data.cur_total_d){
                $('#to_d_percent').html(`<i class="fa fa-arrow-${(data.percentage_d.increased_d) ? 'up text-blue-400':'down text-red-400'}"></i> ${data.percentage_d.percentage_d} %`);              
              }
              else{
                $('#to_d_percent').text(`${data.percentage_d.percentage_d} %`);              
              }
              
          }          
      }

      //changeable branches by shops
      if(shopid == 0){
        $('#select_branch').css('display','none');
        getShopBranches($('#select_shop').children("option:selected").val());
      }               
      else{
        getShopBranches(shopid);
      }         
      

      $('#select_shop').on('change',function(){
        if($('#select_shop').children("option:selected").val() == "all"){
          $('#select_branch').css('display','none');
        }
        else{
          $('#select_branch').css('display','inline-block');
          getShopBranches($('#select_shop').children("option:selected").val());
        }
        
      });
  
      //get branches of selected shop
      function getShopBranches($selected_shop){        
        $.ajax({
          url: base_url + 'reports/Report_tools/getBranchOptions/'+$selected_shop,
          type: 'GET',
          dataType: 'JSON',
          beforeSend: function(){
            $.LoadingOverlay("show");     
          },
          success: function(data){                
            $('#select_branch').html(data.options);   
            $.LoadingOverlay("hide");
          },
          error: function(){
            $.LoadingOverlay("hide");
          }
        });      
      }
      
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
  