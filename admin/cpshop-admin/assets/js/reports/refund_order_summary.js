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
      fromdate: $('#date_from').val(),
      todate: $('#date_to').val()
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
  
    var element = document.getElementById("totalsales");
    var reforder_sum_chart = new Chart(element, {
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
                text: "Loss Revenue Over Time"
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

    function fill_data(reforders)
    {
        if(reforder_sum_chart){
            reforder_sum_chart.options.scales.yAxes[0] = {
                ticks: {
                    stepSize: reforders.step,
                    backdropPaddingY: 30,
                    callback: function (value) {
                        if (value >= 1000) {
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
            reforder_sum_chart.data.datasets[0].label = [reforders.legend[0]];
            reforder_sum_chart.data.datasets[1].label = [reforders.legend[1]];
            // console.log(reforders.dates);
            reforder_sum_chart.data.labels = reforders.dates;
            
            reforder_sum_chart.data.datasets[0].data = reforders.cur_val;
            reforder_sum_chart.data.datasets[1].data = reforders.pre_val;

            reforder_sum_chart.update();
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
                {"targets":[1,4,5,6],"visible":(filter.filtertype == 'summary') ? false:true},
                {"targets":[3],"visible":(filter.shopid > 0) ? true:false},
                {"targets":[6,7],"className": "text-right"},
            ],
            "ajax":{
            url: base_url+'reports/Refund_Order/get_RefundSummary_table',
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
        $.ajax({
            type:'post',
            url: base_url+'reports/Refund_Order/get_RefundSummary_data',
            data: filter,
            success:function(data){
                if(data.success == 1)
                {
                    // console.log(data);
                    fill_data(data.chartdata);
                    $(".btnSearch").prop('disabled', false);
                    $(".btnSearch").text("Search");

                    // var html = json_data.message;
                    // $(".comdiv").html(html);
                    // $('.overlay').css("display","none");
                }
                else
                {
                    fill_data(data.chartdata);
                    $(".btnSearch").prop('disabled', false);
                    $(".btnSearch").text("Search");
                    // var html = json_data.message;
                    // $(".comdiv").html(html);
                    // $('.overlay').css("display","none");
                }
            }
        });
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
        filter.fromdate = initial_datefrom;
        filter.todate = initial_dateto;
            // $(".search-input-text").val("");
        $('#date_from').val(initial_datefrom);
        $('#date_to').val(initial_dateto);
        $('#select_shop option[value="all"]').prop('selected', true);
        $('#filtertype option[value="all"]').prop('selected', true);
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
            showCpToast("info", "Note!", "Error occured while trying get branches of selected shop.");
   //          $.toast({
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
  