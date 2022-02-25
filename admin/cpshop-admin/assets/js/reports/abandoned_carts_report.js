$(function(){
      //hides data container
      data_toggle("hide");
      //false on first run
      var first_run = true;

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

    var element = document.getElementById("abandonedcart-chart").getContext('2d');
    var abandoned_cart_chart = null;
    setChart('line',{
      'labels':[],
      'legend':[],
      'data':[],
    });

    function fill_data(abandoned_cart)
    {
      abandoned_cart_chart.destroy();
      element = document.getElementById("abandonedcart-chart").getContext('2d');
      element.canvas.height = '300';
      if(abandoned_cart.totalData > 0){
        setChart(abandoned_cart.chartType, abandoned_cart);
        first_run = false;
      }
    }
  
    var abandoned_carts_tbl = () => {
      return $('#table-grid').DataTable( {
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "searching": false,
        "destroy": true,
        "order":[[0, 'asc']],
        "ajax":{
          url: base_url+'reports/abandoned_carts/abandoned_carts_data',
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
              $("td#total_atc").text(`${response.tfoot[0]}`);
              $("td#total_rc").text(`${response.tfoot[1]}`);
              $("td#total_ptp").text(`${response.tfoot[2]}`);
              data_toggle("show");
              $('#chart_toggle').show();
            }
            else{
              data_toggle("hide");
              $('#chart_toggle').hide();
              $('#btnExport').hide(100);
            }
            first_run = true;
            // console.log(JSON.stringify(decodeURIComponent(this.data)));
            $("input#_search").val(JSON.stringify(this.data));
            $("input#_filter").val(JSON.stringify(filter));
          },
          error: function(){
            $.LoadingOverlay("hide");
          }
        }
      });
    }

    function chart(){
      $.ajax({
          type:'post',
          url: base_url+'reports/Abandoned_carts/get_abandoned_carts_chart',
          data: filter,
          success:function(res){
            var response = $.parseJSON(res);
            // console.log(response);
            if (response.chartdata.totalData > 0) {
              $("#abandonedCartChart").show();
              fill_data(response.chartdata);
            } else {
              $("#abandonedCartChart").hide();
            }
          }
      });
  }

    function setChart (type, data) {
      abandoned_cart_chart = new Chart(element, {
        type: type,
        data: {
            labels: (type == 'line') ? data.labels:['Abandoned'],
            datasets: [{
                label: data.legend[0],
                data: data.data[0],
                backgroundColor: (type == 'line') ? 'rgba(0,0,0,0)':'#07DA63',
                borderColor: (type == 'line') ? '#07DA63':'rgba(0,0,0,0)',
                borderWidth: 3
            },
            {
                label: data.legend[1],
                data: data.data[1],
                backgroundColor: (type == 'line') ? 'rgba(0,0,0,0)':'darkgray',
                borderColor: (type == 'line') ? '#707070':'rgba(0,0,0,0)',
                borderWidth: 3
            }]
        },
        options: {
            title:{
                display: true,
                position: 'top',
                text: "Abandoned Carts Overtime"
            },
            legend: {
                display: true,
                position: 'bottom',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            elements: {
                line: {
                    tension: 0
                },
                point: {
                  radius: 0
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        stepSize: data.step,
                        beginAtZero: true,
                      }
                    }],
                    xAxes: [{
                      ticks: {
                        beginAtZero: true,
                        autoSkip: true,
                        autoSkipPadding: 30,
                        maxRotation: 0,
                    }
                }]
            }
        }
      });
    }
  
    $("#date_from").datepicker("setDate",initial_datefrom);
    $("#date_to").datepicker("setDate",initial_dateto);
    
    $(document).ready(function () {
      abandoned_carts_tbl();
      chart();
    })
  
    $(document).on('click', '#btnSearch', function(){
      filter.shopid = $('#select_shop').val() || '';
      filter.fromdate = $('#date_from').val();
      filter.todate = $('#date_to').val();
      filter.filtertype = $('#filtertype').val();
      abandoned_carts_tbl();
      chart();
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

        var visibility = $('#abandonedCartChart').is(':visible');

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
        $("#abandonedCartChart").slideToggle("slow");
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
      abandoned_carts_tbl();
      });
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
  
  