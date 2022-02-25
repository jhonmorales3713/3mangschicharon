$( function () {
    data_toggle("hide");
    var first_run = false;

    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    
    var filter = {
        shopid: $('#select_shop').val(),
        branchid: $('#select_branch').val(),
        fromdate: $('#date_from').val(),
        todate: $('#date_to').val()
    };
    var initial_shop_id = ($('#select_shop').val() == 'all') ? 0:$('#select_shop').val();
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();

    var ctx = document.getElementById("poChart").getContext("2d");
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, '#07da63');
    gradient.addColorStop(0.4, '#08f36e');
    gradient.addColorStop(0.8, '#1cf87c');
    gradient.addColorStop(1, '#35f98a');
    var poChart = new Chart(ctx, {
      type: 'horizontalBar',
      data: {
          labels: [],
          datasets: [
            {
              'backgroundColor': gradient
            }
          ]
      },        
      options: {
        title:{
          display: false,
          position: 'top',
          text: "Pending Orders"
        },     
        legend: {
            position: 'bottom',
            fullWidth: true,
            labels: {
              boxWidth: 10
            }
        },
        scales: {
          yAxes: [{
              ticks: {
                  beginAtZero: true,
                  autoSkip: true,
                  backdropPaddingY: 30,
              }
          }],
          xAxes: [{
              ticks: {
                beginAtZero: true,
                maxRotation: 0,
                precision: 0
              }
          }]
        }               
      }
    });

    function gen_pending_orders_tbl () {
      $.ajax({
        url: base_url+'reports/Pending_orders/get_pending_orders_table',
        type: 'post',
        data: filter,
        beforeSend: function(){
          $.LoadingOverlay("show");
        },
        complete: function(data){
          $.LoadingOverlay("hide");
          var response = $.parseJSON(data.responseText);
          
          if(response.data.length > 0){
            $('#table-grid').DataTable( {
              "processing": true,
              "responsive": true,
              "searching": false,
              "destroy": true,
              "order":[[0, 'asc']],
              "data": response.data,
              "columnDefs":[
                  {"targets":[1],"visible":(initial_shop_id > 0) ? false:true}
              ]
            });
            $('.btnExport').show(100);
            $("input#_search").val(JSON.stringify(this.data));
            $("input#_filter").val(JSON.stringify(filter));
            $("td#p").text(`${response.tfoot['p']}`);
            $("td#po").text(`${response.tfoot['po']}`);
            $("td#rp").text(`${response.tfoot['rp']}`);
            $("td#bc").text(`${response.tfoot['bc']}`);
            data_toggle("show");
            get_po_chart(filter);
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
      })
    }

    function get_po_chart(json_data) {
        $.ajax({
            type:'post',
            url: base_url+'reports/Pending_orders/get_po_chart',
            data: {filter:filter,request:json_data},
            success:function(data){
              $.LoadingOverlay("hide");
              data = JSON.parse(data);
                if(data.success == true){                    
                    fill_data(data.chartdata);
                }
            }
        });
    }

    function fill_data (chartdata) {
        poChart.data.labels = chartdata.labels;
        poChart.data.datasets[0] = {
          'label' : chartdata.data.label,
          'backgroundColor' : gradient,
          'data' : chartdata.data.data
        };
        poChart.update();
    }
    
    var currentDate = new Date();  
    $("#date_from").datepicker("setDate",initial_datefrom);
    $("#date_to").datepicker("setDate",initial_dateto);

    gen_pending_orders_tbl();

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

    $(document).on('click', '#btnSearch', function(){
        filter.shopid = $('#select_shop').val() || '';
        filter.branchid = $('#select_branch').val() || '';
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.filtertype = $('#filtertype').val();
        gen_pending_orders_tbl();
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

  $('select#select_shop').change( (el) => {
    var s_shop = $(el.target).val();
    if (s_shop == "all") {
        $('#select_branch_container').hide();
    } else {
        getShopBranches(s_shop);
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

  function getShopBranches(s_shop){
    $.ajax({
      url: base_url + 'reports/Report_tools/getBranchOptions/'+s_shop,
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
        showCpToast("info", "Note!", "Error occured while trying get branches of selected shop.");
        // $.toast({
        //   heading: 'Note:',
        //   text: "Error occured while trying get branches of selected shop.",
        //   icon: 'info',
        //   loader: false,   
        //   stack: false,
        //   position: 'top-center',  
        //   bgColor: '#FFA500',
        //   textColor: 'white',
        //   allowToastClose: false,
        //   hideAfter: 3000          
        // });
      }
    });      
  }



})