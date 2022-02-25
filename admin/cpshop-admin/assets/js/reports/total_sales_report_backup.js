$(function(){
    //hides data container
    data_toggle("hide");
    //false on first run
    var first_run = false;
    
    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    var filter = {
      shopid: $('#select_shop').val(),
      branchid: $('#select_branch').val(),
      filtertype: $('#filtertype').val(),
      pmethodtype: $('#pmethodtype').val(),
      fromdate: $('#date_from').val(),
      todate: $('#date_to').val()
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
  
    var element = document.getElementById("totalsales");
    var totalsales_chart = new Chart(element, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: '',
                data: [],
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: '#07DA63',
                borderWidth: 3
            },
            {
                label: '',
                data: [],
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'darkgray',
                borderWidth: 3
            }]
        },
        options: {
            title:{
                display: true,
                position: 'top',
                text: "Sales Over Time"
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
                        // beginAtZero: true,
                        stepSize: 4000,
                        // precision: 200
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

    function fill_data(totalsales)
    {
        if(totalsales_chart){
            totalsales_chart.options.scales.yAxes[0] = {
                ticks: {
                    stepSize: totalsales.step,
                    backdropPaddingY: 30,
                    callback: function (value) {
                        if (value >= 1000000) {
                            return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                        } else if (value >= 1000) {
                            return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                        }else{
                            return value;
                        }
                    }
                },
                scaleLabel: {
                    display: true,
                    labelString: '1k = 1000'
                }
            };
            totalsales_chart.data.datasets[0].label = [totalsales.legend[0]];
            totalsales_chart.data.datasets[1].label = [totalsales.legend[1]];
            // console.log(totalsales.dates);
            totalsales_chart.data.labels = totalsales.dates;
            
            totalsales_chart.data.datasets[0].data = totalsales.ts[0];
            totalsales_chart.data.datasets[1].data = totalsales.ts[1];

            totalsales_chart.update();
        }
    }
  
    function table(){
        var table = $('#table-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "searching": false,
            "destroy": true,
            "order":[[0, 'asc']],
            "columnDefs":[
                {"targets":[1,4,5],"visible":(filter.filtertype == 'summary') ? false:true},
                {"targets":[6],"visible":(filter.filtertype == 'summary') ? true:false},
                {"targets":[3],"visible":(filter.shopid > 0) ? true:false},
            ],
            "ajax":{
            url: base_url+'reports/total_sales/total_sales_data',
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
      
  
    function chart(){
        // $.ajax({
        //     type:'post',
        //     url: base_url+'reports/total_sales/total_sales_report_chart_data',
        //     data: filter,
        //     success:function(data){
        //         if(data.success == 1)
        //         {
        //             // console.log(data);
        //             fill_data(data.chartdata);
        //             $(".btnSearch").prop('disabled', false);
        //             $(".btnSearch").text("Search");
        //             $('#table-grid-top').show();
        //             $("#total_amount").text(`P ${data.chartdata.head.total}`);
        //             $("#cur_date_range").text(`${data.chartdata.legend[0]}`);
        //             $("#pre_date_range").text(`${data.chartdata.legend[1]}`);
        //             $("#pre_total_amount").text(`P ${data.chartdata.head.pre_total}`);
        //             $("#total_amount_percent").html(data.chartdata.head.percent);
        //             $("#op_amount").text(`P ${data.chartdata.op.total}`);
        //             $("#pre_op_amount").text(`P ${data.chartdata.op.pre_total}`);
        //             $("#op_percent").html(data.chartdata.op.percent);
        //             $("#pre_mp_amount").text(`P ${data.chartdata.mp.pre_total}`);
        //             $("#mp_amount").text(`P ${data.chartdata.mp.total}`);
        //             $("#mp_percent").html(data.chartdata.mp.percent);

        //             // var html = json_data.message;
        //             // $(".comdiv").html(html);
        //             // $('.overlay').css("display","none");
        //         }
        //         else
        //         {
        //             fill_data(data.chartdata);
        //             $(".btnSearch").prop('disabled', false);
        //             $(".btnSearch").text("Search");
        //             // var html = json_data.message;
        //             // $(".comdiv").html(html);
        //             // $('.overlay').css("display","none");
        //         }
        //     }
        // });
    }
  
    // gen_sales_report_tbl(JSON.stringify(filter));
    chart();
    
    table();
    
    $(document).on('click', '#btnSearch', function(){
        filter.shopid = $('#select_shop').val() || '';
        filter.branchid = $('#select_branch').val() || '';
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.filtertype = $('#filtertype').val();
        filter.pmethodtype = $('#pmethodtype').val();
        chart();
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
        filter.shopid = "all";
        filter.branchid = "all";
        filter.filtertype = "all";
        filter.pmethodtype = "";
        filter.fromdate = initial_datefrom;
        filter.todate = initial_dateto;
            // $(".search-input-text").val("");
        $('#date_from').val(initial_datefrom);
        $('#date_to').val(initial_dateto);
        $('#select_shop option[value="all"]').prop('selected', true);
        $('#filtertype option[value="all"]').prop('selected', true);
        $('#pmethodtype option[value=""]').prop('selected', true);
        chart();
        table();
      });

      $('select#select_shop').change( (el) => {
          var s_shop = $(el.target).val();
          if (s_shop == "all") {
              $('#select_branch_container').hide();
          } else {
              getShopBranches(s_shop);
          }
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
            showCpToast("info", "Note!", 'Error occured while trying get branches of selected shop.');
            //$.toast({
			//     heading: 'Note:',
			//     text: "Error occured while trying get branches of selected shop.",
			//     icon: 'info',
			//     loader: false,   
			//     stack: false,
			//     position: 'top-center',  
			//     bgColor: '#FFA500',
			// 	textColor: 'white',
			// 	allowToastClose: false,
			// 	hideAfter: 3000          
			// });
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
  