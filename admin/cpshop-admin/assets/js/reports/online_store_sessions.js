$(function(){
  //hides data container
  data_toggle("hide");
  //false on first run
  var first_run = false;

  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var filter = {
    fromdate: $('#date_from').val(),
    todate: $('#date_to').val()
  };
  var initial_datefrom = $('#date_from').val();
  var initial_dateto = $('#date_to').val();

  function chart(){
    $.ajax({
			type:'post',
			url: base_url+'reports/Online_store_sessions/get_page_statistics_data',
			data: filter,
			success:function(data){
				if(data.success == true){
					fill_data(data.chartdata);					
				}else{
					fill_data(data.chartdata);
				}
			}
		});
  }

  function fillDataTable() {
    var visitors = $('#table-grid').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "searching": false, 
      "destroy": true,      
      columnDefs: [{ responsivePriority: 1, targets: 0 }],     
      "ajax": {
        url: base_url + 'reports/Online_store_sessions/get_visitors_online_table',
        type: 'post',
        data: {
          fromdate: $('#date_from').val(),
          todate: $('#date_to').val()
        },
        beforeSend: function () {
          $.LoadingOverlay("show");
        },
        complete: function (data) {
            $.LoadingOverlay("hide");          
            var response = $.parseJSON(data.responseText);

            if(response.data.length > 0){
              $('.btnExport').show(100);              
              $('#t_visitors').html('<b>'+response.grand_total+'</b>');
              data_toggle("show");
              $('#chart_toggle').show();
            }
            else{
              $('#btnExport').hide(100);              
              $('#t_visitors').html('');
              data_toggle("hide");
              $('#chart_toggle').hide();
            }
            first_run = true; 
            $("input#_search").val(JSON.stringify(this.data));
            $("input#_filters").val(JSON.stringify(filter));
        },        
        error: function () {  // error handling
            $(".table-grid-error").html("");
            $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#table-grid_processing").css("display", "none");
        }
      }
    }).on('draw.dt', function () {
      var info = visitors.page.info();
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
    chart();
    fillDataTable();
  });

    //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
      $('#date_to').val($(this).val());
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
              label: 'Visitors',
              borderColor: '#07DA63',                  
              backgroundColor: 'rgba(0, 0, 0, 0)',
              borderWidth: 3, 
              data: []
            },
            {
              label: 'Previous Visitors',
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
          text: "Visitors Over Time"
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
                  //stacked: true,
                  backdropPaddingY: 30
              }
          }],
          xAxes: [{
              ticks: {
                  autoSkip: true,
                  //stacked: true,
                  autoSkipPadding: 30,
                  maxRotation: 0,
                  // maxTicksLimit: 20
              }
          }]
        }
      }
    });
  }

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

      data0 = cur_data.map(function(obj) { return obj['visitors']; });
      data1 = pre_data.map(function(obj) { return obj['visitors']; });                

        data.dates[0] = "";
        graph__transactions.data.labels = data.dates;

        graph__transactions.data.datasets[0].label = cur_period;
        graph__transactions.data.datasets[1].label = pre_period;
        
        graph__transactions.data.datasets[0].data = data0;
        graph__transactions.data.datasets[1].data = data1;
        
        graph__transactions.update();

        $('#tv_total_header').html("Current <br>" + data.cur_tb_head);
        $('#p_tv_total_header').html("Previous <br>" + data.pre_tb_head);

        $('#tv_total').text(data.cur_total);
        $('#p_tv_total').text(data.pre_total);

        if(data.pre_total != data.cur_total){
          $('#tv_percent').html(`<i class="fa fa-arrow-${(data.percentage.increased) ? 'up text-blue-400':'down text-red-400'}"></i> ${data.percentage.percentage} %`);              
        }
        else{
          $('#tv_percent').text(`${data.percentage.percentage} %`);
        }
        
    }            
  }

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
  
});


